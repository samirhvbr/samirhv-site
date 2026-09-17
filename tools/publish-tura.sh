#!/usr/bin/env bash
# publish-tura.sh — publica os pacotes do Tura Notes como downloads do site.
#
# POR QUE EXISTE: o .dmg do Tura é assinado e notarizado pela máquina que tem o
# certificado Developer ID no keychain, não por um runner de CI — por isso o
# GitHub Release do tura-notes nunca carrega o .dmg, e este site é o canal de
# macOS. A cada versão, publicar era repetir na mão, pacote por pacote: scp,
# ssh, `sudo -u www-data php artisan files:add`, apagar o staging. Quatro
# pacotes, oito comandos, e um `--project` digitado errado publica no projeto
# vizinho sem avisar.
#
# O QUE ELE NÃO FAZ, DE PROPÓSITO: construir os pacotes. Quem constrói é o
# `tools/build-linux.sh` do repositório do Tura e o build assinado no Mac. Aqui
# só entra arquivo pronto.
#
# COMO UM ARQUIVO VIRA DOWNLOAD: não é o arquivo no disco que publica, é a linha
# em `project_files`. Copiar para storage/app/private/downloads na mão deixa o
# arquivo invisível; criar a linha sem o arquivo deixa um botão quebrado. Só o
# `files:add` (FileIngestService) cria os dois juntos, com tamanho e sha256 — e
# é por isso que este script atravessa o artisan em vez de copiar direto.
#
# O NOME DO ARQUIVO É METADE DO TRABALHO: o FilenameInspector deduz SO,
# arquitetura, tipo e versão do NOME, e é isso que agrupa os botões por sistema
# na página. `Tura-Notes-1.0.3-arm64.dmg` preenche tudo sozinho. Reenviar o
# mesmo nome ATUALIZA a linha em vez de duplicar, preservando o contador de
# downloads — trocar um build é seguro.
#
#   ./tools/publish-tura.sh --host samirhv dist/Tura-Notes-1.0.3-arm64.dmg
#   ./tools/publish-tura.sh --host samirhv --dir ~/x/tura-notes/dist
#   ./tools/publish-tura.sh --host samirhv --dir dist --dry-run
#
# O host NÃO tem default: um hostname chutado publica num servidor errado, e
# isso é pior do que não rodar. Defina SAMIRHV_SSH_HOST no ambiente ou passe
# --host; o caminho limpo é um alias em ~/.ssh/config.
set -uo pipefail

HOST="${SAMIRHV_SSH_HOST:-}"
APP="${SAMIRHV_APP_PATH:-/srv/www/samirhv.com.br/samirhv}"
PROJECT=tura-notes
VERSION=""
DIR=""
DRY=0
FILES=()

die() { printf 'publish-tura.sh: %s\n' "$1" >&2; exit 1; }
log() { printf '==> %s\n' "$*"; }
warn() { printf '    ! %s\n' "$*" >&2; }

while [ $# -gt 0 ]; do
  case "$1" in
    --host)    HOST="${2:-}"; shift ;;
    --app)     APP="${2:-}"; shift ;;
    --project) PROJECT="${2:-}"; shift ;;
    --version) VERSION="${2:-}"; shift ;;
    --dir)     DIR="${2:-}"; shift ;;
    --dry-run|-n) DRY=1 ;;
    -h|--help) sed -n '2,36p' "$0" | sed 's/^# \{0,1\}//'; exit 0 ;;
    -*) die "argumento desconhecido '$1' (tente --help)" ;;
    *)  FILES+=("$1") ;;
  esac
  shift
done

[ -n "$HOST" ] || die "informe o servidor com --host ou SAMIRHV_SSH_HOST (ex.: --host samirhv)"

# --dir: pega os pacotes da pasta, sem descer em subpastas. As extensões são as
# que o FilenameInspector reconhece; qualquer outra coisa na pasta (checksums,
# .zip intermediário, logs de build) fica de fora em vez de virar download.
if [ -n "$DIR" ]; then
  [ -d "$DIR" ] || die "pasta não encontrada: $DIR"
  while IFS= read -r f; do
    [ -n "$f" ] && FILES+=("$f")
  done <<EOF
$(find "$DIR" -maxdepth 1 -type f \
    \( -iname '*.dmg' -o -iname '*.pkg' -o -iname '*.deb' -o -iname '*.rpm' \
       -o -iname '*.AppImage' -o -iname '*.exe' -o -iname '*.msi' \))
EOF
fi

[ ${#FILES[@]} -gt 0 ] || die "nenhum arquivo para publicar (passe caminhos ou --dir)"

# sha256 local: macOS traz shasum, Linux traz sha256sum. O script roda no Mac
# (é lá que o .dmg é assinado), mas serve nos dois.
sha256_of() {
  if command -v shasum >/dev/null 2>&1; then
    shasum -a 256 "$1" | awk '{print $1}'
  elif command -v sha256sum >/dev/null 2>&1; then
    sha256sum "$1" | awk '{print $1}'
  else
    die "nem shasum nem sha256sum disponíveis para conferir a transferência"
  fi
}

STAGE="/tmp/publish-tura.$$"

# Só remove o que este script criou: os arquivos que subiu e o diretório dele.
# `rmdir` e não `rm -rf` de propósito — se sobrou algo inesperado lá dentro, a
# falha do rmdir é o aviso, e nada é apagado às cegas.
cleanup() { [ "$DRY" -eq 1 ] || ssh "$HOST" "rmdir '$STAGE' 2>/dev/null" >/dev/null 2>&1 || true; }
trap cleanup EXIT

log "servidor: $HOST · app: $APP · projeto: $PROJECT"
log "${#FILES[@]} arquivo(s) para publicar"
[ "$DRY" -eq 1 ] && log "DRY RUN: nada é enviado nem publicado"

if [ "$DRY" -eq 0 ]; then
  ssh "$HOST" "mkdir -p '$STAGE' && chmod 0755 '$STAGE'" || die "não consegui criar o staging em $HOST:$STAGE"
fi

FAILED=0
PUBLISHED=0

for f in "${FILES[@]}"; do
  [ -f "$f" ] || { warn "não é um arquivo, pulando: $f"; FAILED=$((FAILED + 1)); continue; }

  base=$(basename "$f")
  log "$base"

  case "$base" in
    *.dmg|*.pkg|*.deb|*.rpm|*.AppImage|*.appimage|*.exe|*.msi) ;;
    *) warn "extensão que o FilenameInspector não reconhece: $base (SO e tipo ficarão vazios; dá para corrigir no admin)" ;;
  esac

  if [ "$DRY" -eq 1 ]; then
    printf '    scp %s %s:%s/\n' "$f" "$HOST" "$STAGE"
    printf '    ssh %s -- cd %s && sudo -u www-data php artisan files:add %s/%s --project=%s%s\n' \
      "$HOST" "$APP" "$STAGE" "$base" "$PROJECT" "${VERSION:+ --version=$VERSION}"
    PUBLISHED=$((PUBLISHED + 1))
    continue
  fi

  local_sha=$(sha256_of "$f") || { FAILED=$((FAILED + 1)); continue; }

  if ! scp -q "$f" "$HOST:$STAGE/"; then
    warn "scp falhou: $base"
    FAILED=$((FAILED + 1))
    continue
  fi

  # Um ssh só por arquivo: confere o sha da transferência, publica pelo artisan
  # e apaga o staging. O sha é conferido ANTES do files:add — um .dmg truncado
  # publicado é pior do que um não publicado, porque parece pronto.
  if ssh "$HOST" bash -s -- "$STAGE" "$base" "$APP" "$PROJECT" "$VERSION" "$local_sha" <<'REMOTE'
set -uo pipefail
stage=$1; name=$2; app=$3; project=$4; version=$5; expected=$6
f="$stage/$name"

[ -f "$f" ] || { echo "o arquivo não chegou ao servidor: $f" >&2; exit 1; }

got=$(sha256sum "$f" | awk '{print $1}')
if [ "$got" != "$expected" ]; then
  echo "sha256 não confere (esperado $expected, recebido $got) — nada foi publicado" >&2
  rm -f "$f"
  exit 1
fi

# www-data precisa ler o arquivo no ingest, e a linha do banco tem que nascer
# com o storage/ pertencendo ao web server — mesma regra do deploy.sh.
chmod 0644 "$f"
cd "$app" || { echo "app não encontrado em $app" >&2; rm -f "$f"; exit 1; }

args=(files:add "$f" "--project=$project")
[ -n "$version" ] && args+=("--version=$version")

if [ "$(id -un)" = "www-data" ]; then
  php artisan "${args[@]}"
else
  sudo -u www-data php artisan "${args[@]}"
fi
rc=$?

rm -f "$f"
exit $rc
REMOTE
  then
    PUBLISHED=$((PUBLISHED + 1))
  else
    warn "falhou ao publicar: $base"
    FAILED=$((FAILED + 1))
  fi
done

echo
log "publicados: $PUBLISHED · falhas: $FAILED"

if [ "$FAILED" -gt 0 ]; then
  exit 1
fi

[ "$DRY" -eq 1 ] || log "confira em https://samirhv.com.br/p/$PROJECT"
