# Samirhv — Central de Projetos/Downloads — Guia para Agentes de IA

Este documento orienta agentes de IA (Claude Code, etc.) que trabalham no projeto **Samirhv** — uma central pessoal para organizar e disponibilizar projetos para download (não é mais um blog; pivotado na v0.2.0).

---

## 🔄 Antes de começar: `git pull`

**SEMPRE** verifique atualizações remotas antes de escrever ou alterar qualquer coisa neste repositório:

```bash
git pull          # já está pré-autorizado (allow)
```

Trabalhar sobre uma base desatualizada gera conflitos. Puxe primeiro, sempre. Para só inspecionar antes: `git fetch && git status`.

---

## Comunicação

- **Idioma:** Português (pt-BR) para mensagens ao operador, comentários e textos de UI.
- **Commits:** Formato `versão - comentário` (ex: `0.1.0 - adiciona página de contato`). Versão extraída de `version.md`. Mensagem em português.
- **Identificadores de código:** Inglês (classes, métodos, variáveis, rotas).
- **Strings de UI:** Português.

---

## Stack

- **Framework:** Laravel (PHP 8.4+), pasta `samirhv/`
- **Template engine:** Blade
- **Frontend:** Canvas 7 (tema HTML5) — assets em `public/vendor/canvas/`
- **Banco de Dados:** MySQL / MariaDB — nunca usar SQLite como **armazenamento do app**. **Exceção deliberada:** o módulo **AI-MEMORY** do admin lê (somente leitura, `PRAGMA query_only`) o SQLite **externo** do produto `ai-memory` — é fonte de dados de terceiro, não storage nosso. Não "conserte" a conexão `aimemory` em `config/database.php`. Ver `samirhv/docs/AI-MEMORY.md`.
- **CSS theme:** `public/vendor/canvas/style.css` + `css/blog-theme.css`

---

## Pastas Temporárias

`tmp/` na raiz é para referência visual apenas — não referenciar no código de produção. Se precisar de um asset de lá, copiar para `public/vendor/canvas/`.

---

## Convenções (Laravel)

- **Controllers finos:** request handling + response. Lógica vai em Services.
- **Nomes de views:** `snake_case` em sub-pastas (ex: `projects/show.blade.php`).
- **Rotas nomeadas:** sempre com `->name()`, ex: `route('project.show', $project)`.
- **Assets:** sempre via `asset('vendor/canvas/...')`, nunca caminho relativo.
- **Str::limit:** usar para truncar textos no blade.

## Estrutura (v0.2.0+)

**Público** (`routes/web.php`): `/` (home, vitrine), `/downloads` (lista), `/p/{slug}` (projeto), `/d/{file}` (download com contagem + auditoria), `/login`, `/logout`.

**Admin** (`routes/admin.php`, prefixo `/admin`, middleware `auth,admin,password.changed`): dashboard, `projetos` (CRUD), `projetos/{p}/arquivos` (upload), `monitor` (versão nossa × upstream OSS no GitHub, via `GithubReleaseChecker`), `auditoria` (downloads + analytics), `auditoria-acesso` (ações/logins), `perfil`.

**Modelo de dados:** `Project` → `hasMany ProjectFile`. Cada arquivo tem `downloads_count`; cada download gera uma linha em `download_logs`. Auditoria de visitas em `page_views` (via middleware `TrackPageView`), ações do admin em `activity_logs` (`AuditLogger`), autenticação em `auth_events` (listeners no `AppServiceProvider`).

**Arquivos para download:** disco `downloads` **privado** (`storage/app/private/downloads`). O único acesso é via `/d/{file}` (`DownloadController`), que conta e audita. Upload no admin tem limite de 500 MB; arquivos maiores: `php artisan files:add <path> --project=<slug>`.

**Admin único:** flag `users.is_admin` (sem Spatie). Seeder `AdminUserSeeder` cria o admin (`ADMIN_EMAIL`/`ADMIN_PASSWORD` no `.env`) com troca de senha obrigatória no 1º acesso. Sem cadastro público.

## Comandos Rápidos

| Comando                          | Uso                                   |
|----------------------------------|---------------------------------------|
| `php artisan serve`              | Servidor local (http://localhost:8000)|
| `php artisan route:list`         | Lista rotas registradas               |
| `php artisan view:clear`         | Limpa cache de views                  |
| `php artisan optimize:clear`     | Limpa todo cache                      |
| `php -l arquivo.php`             | Valida sintaxe PHP                    |

## Checklist de PR

- [ ] `php -l` em arquivos PHP alterados
- [ ] `php artisan route:list` sem erros
- [ ] `php artisan view:cache` valida Blade (depois `view:clear`)
- [ ] `README.md` atualizado se mudou estrutura
- [ ] `version.md` incrementado (Z+1 para mudança de layout/feature)

---

## PS — Commits: a skill COMMITTER cuida disso

**Existe `.committer.yml` na raiz deste repositório** — é o opt-in da skill
**COMMITTER**, que roda em ciclo (cron, via `~/x/GIT/run.sh`). Enquanto esse arquivo
existir com `enabled: true`, **commitar e pushar não é trabalho seu**.

**O que muda para você:**

- **Não commite nem pushe por padrão.** Conclua a entrega bumpando o `version.md`
  **com a entrada de changelog** e deixe a árvore pronta. É dali que a mensagem do
  commit sai — o changelog virou o artefato de handoff entre você e a skill.
- A skill monta `X.Y.Z - descrição`, commita e pusha a branch atual sozinha. Ela
  **nunca bumpa versão** (isso continua sendo julgamento seu) e nunca inventa
  mensagem: sem entrada de changelog ela cai num fallback Sonnet, e sem conseguir
  descrever com honestidade ela aborta e espera.

**Você ainda commita quando:**

- o Samir pedir explicitamente;
- a tarefa exigir o SHA na hora (deploy, abrir PR, referência cruzada);
- o `.committer.yml` sumir ou estiver `enabled: false` — aí vale o fluxo antigo,
  você bumpa, commita e pusha.

**Por que isso existe:** tirar de um modelo caro (Opus/Fable) o trabalho mecânico de
empacotar commit, que um Sonnet — ou, na maioria das vezes, nenhum modelo — resolve.
Economiza token e devolve tempo de desenvolvimento.

---

<!-- RELEASES-RULE:repodocs -->

## Releases — the `version.md` on GitHub is what the Releases show

> Marked echo. The single source is **[samirhvbr/repodocs](https://github.com/samirhvbr/repodocs/blob/master/docs/versioning.md)**
> — change it there, not here. This block is regenerated.

**The `version.md` of the default branch, on GitHub, is what the GitHub Releases
must show.** The local checkout does not enter the calculation: it can be behind,
ahead or mid-work, and none of that is published — GitHub cannot tag a commit it
does not have.

**The bump and the Release are one act.** A commit that bumps `version.md` is not
finished until that version has a tag, a published Release, and the **`Latest`
badge on it** — the same push, not "later". A badge sitting on an older release
tells whoever looks that the project is at a version it is not.

- `.github/workflows/release.yml` does it on any push that touches `version.md`.
- `./tools/release.sh` does it by hand. It is **idempotent and self-healing**:
  it publishes whatever is missing and moves a drifted badge back. Running it is
  always safe, so it is both the check and the fix.

A PR publishes nothing while it is a PR. The moment it merges, the push moves
`version.md` on the default branch and the Release becomes that version.

Tag and Release title are the **bare version — no `v` prefix**.

<!-- /RELEASES-RULE -->
