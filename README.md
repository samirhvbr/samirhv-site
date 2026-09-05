# SAMIRHV — REPOSITORY

A personal hub for publishing and distributing Samir Hanna Verza's projects,
built with Laravel and the Canvas theme. It was a blog until 0.2.0; it is now a
project and download catalogue, and it is bilingual.

## Technologies

- **Backend:** Laravel (PHP 8.4+)
- **Frontend:** Blade + Canvas 7 (HTML5 theme — assets served statically from `public/vendor/canvas/`; there is no build step and no bundler)
- **Database:** MySQL / MariaDB for app storage (nothing else, and never use sqlite). *Exception:* the admin **AI-MEMORY** module reads the external `ai-memory` SQLite **read-only** — see `samirhv/docs/AI-MEMORY.md`.
- **Server:** Debian (Linux)
- **GitHub:** Always commit in blocks and with a good description; the standard is the version from `version.md` - (hyphen) comment, in English (US)

## Goal

One address where a project can be found, understood and downloaded — with the
download counted and audited, and with the provenance of every build visible.
`PRODUCT.md` states the thesis: provenance is the feature.

## Languages

The site speaks English and Brazilian Portuguese, and **the URL decides which**.

| Page | English (canonical) | Portuguese |
|---|---|---|
| home | `/` | `/pt-br` |
| downloads | `/downloads` | `/pt-br/downloads` |
| project | `/p/{slug}` | `/pt-br/p/{slug}` |

A visitor arriving at a bare URL is negotiated: a browser asking for Portuguese
is sent to `/pt-br` (302, `Vary: Accept-Language, Cookie`), anyone else stays on
English. An explicit `/pt-br` address is never negotiated away — being handed a
Portuguese link beats any preference. The choice is remembered in the
`samirhv_locale` cookie, written by `/lang/{locale}`.

`App\Support\Locales` is the single source: the route groups, the `hreflang`
pair, the switcher and `/sitemap.xml` are all derived from it. Interface strings
live in `lang/en` and `lang/pt_BR`; prose stored in the database is translated
by `App\Support\Content`, keyed by project slug.

**The admin panel is Portuguese only, on purpose.** It has one user.

## Structure

```
samirhv/                     ← repository root
├── samirhv/                 ← Laravel application
│   ├── app/
│   │   ├── Http/Controllers/       ← SiteController, DownloadController, Admin/*
│   │   ├── Http/Middleware/        ← SetLocale, NegotiateLocale, TrackPageView, EnsureIsAdmin…
│   │   ├── Models/                 ← Project, ProjectFile, DownloadLog, PageView…
│   │   ├── Services/               ← DownloadPresenter, FileIngestService, GithubReleaseChecker…
│   │   └── Support/                ← Locales, Content, SemVer, OsDetector…
│   ├── lang/{en,pt_BR}/     ← interface strings + the per-app changelog
│   ├── public/vendor/canvas/← Canvas theme assets (CSS, JS)
│   ├── resources/views/
│   ├── routes/{web,admin}.php
│   └── tests/
├── img/                     ← project favicons and images
├── tmp/                     ← reference files (git-ignored, will be deleted)
├── CHANGELOG.md             ← one entry per release; its heading is the commit subject
├── CLAUDE.md                ← guide for AI agents
├── SECURITY_GUIDELINES.md
└── version.md
```

## Public surface

`/` and `/downloads` browse the catalogue; `/p/{slug}` is a project's page, with
its files grouped by operating system and version, a recommended build picked
from the User-Agent, and its changelog. `/d/{file}` is the only way to fetch a
file: the disk is private, and every hit is counted and audited.

## Admin

`/admin`, behind `auth`, `admin` and `password.changed`. Projects and their
files (upload up to 500 MB; larger files via
`php artisan files:add <path> --project=<slug>`), a monitor comparing our
version against the upstream OSS release on GitHub, download and access audits,
and read-only observatories over `ai-memory` and GitHub.

There is no public sign-up. The single admin comes from `AdminUserSeeder`
(`ADMIN_EMAIL` / `ADMIN_PASSWORD`) and must change the password on first login.

## Tests

```bash
cd samirhv && php artisan test
```

The suite needs **no database** by design, and `phpunit.xml` points at one that
does not exist so a test that touches a database fails loudly instead of writing
to the development one. It covers locale negotiation, bilingual rendering,
legacy redirects, the sitemap, the app changelog and the admin panel's access
controls.

## Version

`version.md` at the root records the public version in the `X.Y.Z` format:

- **X** — stable version (manual change)
- **Y** — significant structural change
- **Z** — increment for each new screen, table, or layout change

The `version.md` on the default branch is what the GitHub Releases must show;
`.github/workflows/release.yml` and `tools/release.sh` keep them in step, and
`CHANGELOG.md` supplies the notes.
