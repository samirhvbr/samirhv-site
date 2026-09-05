#!/bin/bash
# versão 2.0 - 2026-06-25
#
# Deploy de produção do samirhv.com.br em /srv/www/samirhv.com.br.
# Baseado no deploy da INTRANET (v3.4), adaptado para a estrutura DESTE repo:
#
#   PARTICULARIDADE: o app Laravel NÃO está na raiz do repositório — ele vive
#   na subpasta samirhv/. Então:
#     - git roda na RAIZ do repo  ->  $DIR  = /srv/www/samirhv.com.br
#     - artisan/composer/npm/.env ->  $APP  = $DIR/samirhv
#
# Características herdadas da INTRANET:
#   - Idempotente: sai cedo se não há nada novo no branch.
#   - Lock impede dois deploys simultâneos.
#   - Backup do banco (mysqldump) ANTES de mexer em schema.
#   - Pula o composer quando os arquivos relevantes não mudaram.
#   - Em erro: sai do modo manutenção, tenta rollback da última migration e
#     (se configurado) avisa no Telegram.
#
# Diferenças em relação à INTRANET (refletem a realidade deste projeto):
#   - Sem passo de frontend: o tema é o Canvas, servido estático de
#     public/vendor/canvas/. Não há Vite, não há bundle, não há npm.
#   - Backup via mysqldump genérico (não há o comando custom mariadb:backup).
#
# MODELO DE OWNERSHIP (igual INTRANET):
#   - git/composer rodam como o DONO do tree (b3sys) — ambos tocam
#     .git/vendor/public. Evita "dubious ownership" e
#     "Permission denied".
#   - artisan de RUNTIME (down/backup/migrate/caches/up) roda como www-data —
#     esses escrevem em storage/ e bootstrap/cache/, território do web server.
#   - composer usa --no-scripts pra NÃO rodar 'package:discover' como o dono
#     (sujaria bootstrap/cache); o cache de pacotes é apagado como www-data e o
#     framework redescobre no próximo boot, já como www-data.
#
# Requisito: rode como ROOT (`sudo bash deploy.sh`). Root desce p/ b3sys e
# www-data via sudo sem pedir senha.
#
# Variáveis OPCIONAIS no $APP/.env:
#   DEPLOY_TELEGRAM_BOT_TOKEN / DEPLOY_TELEGRAM_CHAT_ID - avisos de deploy.
#   DB_* (DB_DATABASE/DB_USERNAME/DB_PASSWORD/...)       - usadas no backup.

set -euo pipefail

# ── Config ───────────────────────────────────────────────────────────────────
DIR="/srv/www/samirhv.com.br"       # raiz do repositório git (git pull aqui)
APP="$DIR/samirhv"                  # app Laravel (artisan/composer/npm/.env)
BRANCH="${DEPLOY_BRANCH:-master}"
LOCK="/run/samirhv-deploy.lock"     # /run (tmpfs, root) — não /tmp

# ── Helpers ──────────────────────────────────────────────────────────────────
log() { printf '[%(%H:%M:%S)T] %s\n' -1 "$*"; }

# Lê uma chave do $APP/.env (sem 'source', pra evitar quebra com quoting).
get_env() {
    local key=$1 envf="$APP/.env"
    [ -f "$envf" ] || return 0
    { grep -E "^${key}=" "$envf" 2>/dev/null || true; } | head -1 | cut -d= -f2- | sed 's/^"//;s/"$//'
}

# Roda comando como www-data (artisan de runtime: storage/, bootstrap/cache/).
www() {
    if [ "$(id -un)" = "www-data" ]; then "$@"; else sudo -u www-data "$@"; fi
}

# Roda comando como o DONO do tree (git/composer/npm). OWNER definido após cd.
asowner() {
    if [ "$(id -un)" = "$OWNER" ]; then "$@"; else sudo -u "$OWNER" "$@"; fi
}

# Normaliza o dono de um caminho de build pro OWNER, só se houver divergência.
heal_owner() {
    local p=$1
    [ -e "$p" ] || return 0
    [ "$(stat -c '%U' "$p")" = "$OWNER" ] && return 0
    log "  ⚠️  $p pertence a $(stat -c '%U' "$p") — normalizando para $OWNER..."
    chown -R "$OWNER": "$p"
}

# Telegram (silencioso se as credenciais não estão no .env).
notify() {
    [ -n "${TG_BOT:-}" ] && [ -n "${TG_CHAT:-}" ] || return 0
    curl -fsS -m 5 -X POST "https://api.telegram.org/bot${TG_BOT}/sendMessage" \
        --data-urlencode "chat_id=${TG_CHAT}" \
        --data-urlencode "text=$1" \
        --data-urlencode "parse_mode=HTML" \
        >/dev/null 2>&1 || log "(notify Telegram falhou; ignorando)"
}

cleanup_on_failure() {
    log "❌ Deploy falhou — reativando aplicação..."
    ( cd "$APP" && www php artisan up ) >/dev/null 2>&1 || true
    notify "❌ <b>Deploy samirhv falhou</b> em <code>$(cd "$DIR" && asowner git rev-parse --short HEAD 2>/dev/null || echo '?')</code>"
}

fail() { log "❌ $*"; cleanup_on_failure; exit 1; }

# Backup do MySQL antes de tocar em schema. Best-effort:
#   - pula (com aviso) se não for mysql ou se faltam credenciais;
#   - aborta o deploy se o mysqldump for tentado e falhar.
backup_db() {
    local conn db user pass host port dumpdir stamp file cnf
    conn=$(get_env DB_CONNECTION)
    if [ "${conn:-mysql}" != "mysql" ]; then
        log "⚠️  DB_CONNECTION=$conn — backup automático cobre só mysql; pulando."
        return 0
    fi
    db=$(get_env DB_DATABASE); user=$(get_env DB_USERNAME); pass=$(get_env DB_PASSWORD)
    host=$(get_env DB_HOST); host=${host:-127.0.0.1}
    port=$(get_env DB_PORT); port=${port:-3306}
    if [ -z "$db" ] || [ -z "$user" ]; then
        log "⚠️  credenciais de DB ausentes no .env — backup pulado (risco!)."
        return 0
    fi
    dumpdir="$APP/storage/app/backups"
    mkdir -p "$dumpdir"
    stamp=$(date +%Y%m%d-%H%M%S)
    file="$dumpdir/${db}-${stamp}.sql.gz"
    log "    Destino: $file"
    # defaults-file temporário (evita a senha aparecer em ps/cmdline).
    cnf=$(mktemp); chmod 600 "$cnf"
    printf '[client]\nhost=%s\nport=%s\nuser=%s\npassword=%s\n' "$host" "$port" "$user" "$pass" > "$cnf"
    if mysqldump --defaults-extra-file="$cnf" \
            --single-transaction --quick --routines --no-tablespaces "$db" \
            | gzip > "$file"; then
        rm -f "$cnf"
        chown -R www-data: "$dumpdir" 2>/dev/null || true
        # Mantém os 7 backups mais recentes (ordena por mtime via find, sem ls).
        find "$dumpdir" -maxdepth 1 -type f -name '*.sql.gz' -printf '%T@\t%p\n' \
            | sort -rn | tail -n +8 | cut -f2- | xargs -r rm -f
        log "  ✓ backup OK"
    else
        rm -f "$cnf"
        fail "mysqldump falhou — aborta antes de migrate"
    fi
}

# ── Pré-requisito: roda como ROOT ────────────────────────────────────────────
if [ "$(id -u)" -ne 0 ]; then
    printf '❌ rode como root: sudo bash %s\n' "$0" >&2
    exit 1
fi

# ── Lock ─────────────────────────────────────────────────────────────────────
exec 9>"$LOCK"
flock -n 9 || { printf '❌ outro deploy já está rodando (lock: %s)\n' "$LOCK" >&2; exit 1; }

cd "$DIR" || { printf '❌ %s não existe\n' "$DIR" >&2; exit 1; }

# ── Dono do tree + .env ──────────────────────────────────────────────────────
OWNER=$(stat -c '%U' "$DIR")
log "==> Repo: $DIR  |  App: $APP  |  Dono: $OWNER  |  Branch: $BRANCH"

TG_BOT=$(get_env DEPLOY_TELEGRAM_BOT_TOKEN)
TG_CHAT=$(get_env DEPLOY_TELEGRAM_CHAT_ID)

# ── 1. Fetch e checagem de mudanças ──────────────────────────────────────────
log "==> Buscando alterações em origin/$BRANCH..."
asowner git fetch --quiet origin "$BRANCH" || { log "git fetch falhou"; exit 1; }

LOCAL=$(asowner git rev-parse HEAD)
REMOTE=$(asowner git rev-parse "origin/$BRANCH")

if [ "$LOCAL" = "$REMOTE" ]; then
    log "✓ Nada novo em origin/$BRANCH. Saindo."
    exit 0
fi

if ! asowner git diff --quiet || ! asowner git diff --cached --quiet; then
    log "⚠️  Working tree tem mudanças locais não commitadas."
fi

log "==> Trazendo $(asowner git rev-parse --short "$LOCAL") → $(asowner git rev-parse --short "$REMOTE")..."
asowner git merge --ff-only "origin/$BRANCH" \
    || { log "❌ fast-forward falhou (working tree divergiu? resolva manual e rode de novo)"; exit 1; }

CHANGED=$(asowner git diff --name-only "$LOCAL" "$REMOTE")

# Daqui pra frente, qualquer falha dispara o cleanup automático.
trap cleanup_on_failure ERR

# A partir daqui tudo acontece dentro do app Laravel.
cd "$APP" || fail "$APP não existe"

# ── 2. Modo manutenção ───────────────────────────────────────────────────────
log "==> Ativando modo de manutenção..."
www php artisan down --refresh=15

# ── 3. Backup do banco (antes de mexer em schema) ────────────────────────────
log "==> Backup do banco via mysqldump..."
backup_db

# ── 4. PHP dependencies (composer.json OR .lock changed, or vendor missing) ───
# composer.json joined this condition in 0.6.0, over a real defect: the commit
# that added `autoload.files` (the lroute() helper) does NOT change
# composer.lock, because it touches no dependency. The deploy skipped
# `composer install`, the autoloader stayed stale, and every public page would
# have 500'd with `Call to undefined function lroute()`. It holds for any future
# composer.json-only change too: a new autoload path, a script, a config key.
if [ ! -d vendor ] \
   || grep -q '^samirhv/composer\.lock$' <<<"$CHANGED" \
   || grep -q '^samirhv/composer\.json$' <<<"$CHANGED"; then
    log "==> Instalando dependências PHP (como $OWNER)..."
    heal_owner vendor
    asowner env COMPOSER_ALLOW_SUPERUSER=1 composer install \
        --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts \
        || fail "composer install falhou"
    www find bootstrap/cache -maxdepth 1 -type f \
        \( -name 'packages.php' -o -name 'services.php' \) -delete 2>/dev/null || true
else
    log "✓ composer.json/.lock inalterados — pulando composer install."
fi

# ── 6. Migrations: lista pendentes, tenta migrate, rollback se falhar ─────────
log "==> Verificando migrações pendentes..."
PENDING=$(www php artisan migrate:status 2>/dev/null | grep -i "pending" || true)
if [ -n "$PENDING" ]; then echo "$PENDING"; else log "  (nenhuma pendente)"; fi

log "==> Rodando migrate --force..."
if ! www php artisan migrate --force; then
    log "❌ migrate falhou — tentando rollback do último batch..."
    if www php artisan migrate:rollback --force --step=1; then
        log "✓ rollback OK — schema voltou ao estado anterior."
    else
        log "⚠️  rollback também falhou. Restaure manualmente do backup em storage/app/backups."
    fi
    fail "migrate falhou"
fi

# ── 7. Caches de produção ────────────────────────────────────────────────────
log "==> Reconstruindo caches..."
www php artisan optimize:clear
www php artisan config:cache
www php artisan route:cache
www php artisan view:cache
www php artisan event:cache 2>/dev/null || true

# ── 8. Sai do modo manutenção ────────────────────────────────────────────────
log "==> Desativando modo de manutenção..."
www php artisan up

# Sucesso — desarma o trap e notifica.
trap - ERR

SHORT_SHA=$(asowner git -C "$DIR" rev-parse --short HEAD)
SUBJECT=$(asowner git -C "$DIR" log -1 --pretty=%s | head -c 80)

log "✅ Deploy concluído: $SHORT_SHA ($SUBJECT)"
notify "✅ <b>Deploy samirhv concluído</b>: <code>$SHORT_SHA</code> — $SUBJECT"
