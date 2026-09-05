# SAMIRHV — REPOSITÓRIO

> **Este é o único documento do repositório escrito em português, por decisão
> explícita do dono (05/09/2026).** A regra do repositório é inglês (US) para
> tudo — documento, commit, PR, issue, comentário de código. Este arquivo é a
> exceção deliberada: ele existe para ser a versão em português do README.
> Não "conserte" o idioma dele. Ao mudar o `README.md`, mude este junto.

Central pessoal para publicar e distribuir os projetos de Samir Hanna Verza,
construída com Laravel e o tema Canvas. Foi um blog até a 0.2.0; hoje é uma
vitrine de projetos e downloads, e é bilíngue.

## Tecnologias

- **Backend:** Laravel (PHP 8.4+)
- **Frontend:** Blade + Canvas 7 (tema HTML5 — assets servidos estáticos de `public/vendor/canvas/`; não há build nem bundler)
- **Banco de Dados:** MySQL / MariaDB como armazenamento do app (nenhum outro, e nunca usar sqlite). *Exceção:* o módulo **AI-MEMORY** do admin lê o SQLite externo do `ai-memory` em **somente leitura** — veja `samirhv/docs/AI-MEMORY.md`.
- **Servidor:** Debian (Linux)
- **GitHub:** Sempre faça commits em blocos e com uma boa descrição; o padrão é a versão do `version.md` - (hífen) comentário, em inglês (US)

## Objetivo

Um endereço onde um projeto possa ser encontrado, entendido e baixado — com o
download contado e auditado, e com a procedência de cada build visível. O
`PRODUCT.md` diz a tese: a procedência é a feature.

## Idiomas

O site fala inglês e português do Brasil, e **quem decide qual é a URL**.

| Página | Inglês (canônico) | Português |
|---|---|---|
| home | `/` | `/pt-br` |
| downloads | `/downloads` | `/pt-br/downloads` |
| projeto | `/p/{slug}` | `/pt-br/p/{slug}` |

Quem chega numa URL sem prefixo passa por negociação: navegador que pede
português vai para `/pt-br` (302, com `Vary: Accept-Language, Cookie`), o resto
fica no inglês. Um endereço `/pt-br` explícito nunca é desviado — receber um
link em português vale mais que qualquer preferência. A escolha fica no cookie
`samirhv_locale`, escrito por `/lang/{locale}`.

`App\Support\Locales` é a fonte única: os grupos de rota, o par `hreflang`, o
seletor de idioma e o `/sitemap.xml` são todos derivados dele. As strings de
interface ficam em `lang/en` e `lang/pt_BR`; o texto guardado no banco é
traduzido por `App\Support\Content`, com chave no slug do projeto.

**O painel admin é só em português, de propósito.** Ele tem um usuário.

## Estrutura

```
samirhv/                     ← raiz do repositório
├── samirhv/                 ← aplicação Laravel
│   ├── app/
│   │   ├── Http/Controllers/       ← SiteController, DownloadController, Admin/*
│   │   ├── Http/Middleware/        ← SetLocale, NegotiateLocale, TrackPageView, EnsureIsAdmin…
│   │   ├── Models/                 ← Project, ProjectFile, DownloadLog, PageView…
│   │   ├── Services/               ← DownloadPresenter, FileIngestService, GithubReleaseChecker…
│   │   └── Support/                ← Locales, Content, SemVer, OsDetector…
│   ├── lang/{en,pt_BR}/     ← strings de interface + o changelog de cada app
│   ├── public/vendor/canvas/← assets do tema Canvas (CSS, JS)
│   ├── resources/views/
│   ├── routes/{web,admin}.php
│   └── tests/
├── img/                     ← favicons e imagens do projeto
├── tmp/                     ← arquivos de referência (fora do git, serão apagados)
├── CHANGELOG.md             ← uma entrada por release; o título dela é o assunto do commit
├── CLAUDE.md                ← guia para agentes de IA
├── SECURITY_GUIDELINES.md
└── version.md
```

## Superfície pública

`/` e `/downloads` navegam a vitrine; `/p/{slug}` é a página de um projeto, com
os arquivos agrupados por sistema operacional e versão, um build recomendado
escolhido pelo User-Agent, e o changelog dele. `/d/{file}` é o único jeito de
baixar um arquivo: o disco é privado, e cada acesso é contado e auditado.

## Admin

`/admin`, atrás de `auth`, `admin` e `password.changed`. Projetos e seus
arquivos (upload até 500 MB; arquivos maiores via
`php artisan files:add <path> --project=<slug>`), um monitor comparando a nossa
versão com a release do OSS upstream no GitHub, auditoria de downloads e de
acesso, e observatórios somente-leitura sobre o `ai-memory` e o GitHub.

Não há cadastro público. O admin único vem do `AdminUserSeeder`
(`ADMIN_EMAIL` / `ADMIN_PASSWORD`) e é obrigado a trocar a senha no 1º acesso.

## Testes

```bash
cd samirhv && php artisan test
```

A suíte **não precisa de banco**, por desenho, e o `phpunit.xml` aponta para um
que não existe — assim um teste que encostar num banco falha alto em vez de
escrever no de desenvolvimento. Ela cobre a negociação de idioma, a renderização
bilíngue, os redirects legados, o sitemap, o changelog dos apps e os controles
de acesso do painel.

## Versão

O `version.md` na raiz guarda a versão pública no formato `X.Y.Z`:

- **X** — versão estável (mudança manual)
- **Y** — mudança estrutural significativa
- **Z** — incrementa a cada nova tela, tabela ou mudança de layout

O `version.md` do branch padrão é o que as Releases do GitHub têm de mostrar; o
`.github/workflows/release.yml` e o `tools/release.sh` mantêm os dois em passo,
e o `CHANGELOG.md` fornece as notas.
