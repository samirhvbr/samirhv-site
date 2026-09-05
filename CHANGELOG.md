# Changelog

Every release of this repository, newest first.

The heading of an entry **is** the subject of the commit that carried it —
`## X.Y.Z - short description in English (US)` — and `tools/release.sh` reads
this file to build the notes of the matching GitHub Release. The version comes
from `version.md` and is bumped in the same commit as the entry.

Entries before 0.6.0 were reconstructed from `git log` on 2026-09-05, when this
file was first written; their commit subjects were in Portuguese and stay that
way in the history, so the descriptions here are translations, not the original
subjects.

## 0.7.0 - English becomes the site's own language, and the catalogue tells the truth about the apps

### The URL decides the language, and the bare URL is now English

- The unprefixed paths (`/`, `/downloads`, `/p/{slug}`) are English. Portuguese
  moved to the `/pt-br` prefix. A visitor whose browser asks for Portuguese is
  still sent there; anyone else — including a browser that states no preference
  — now stays where it is instead of being redirected.
- Every `/en/…` address published since 0.6.0 answers with a 301 to its bare
  twin, and `/projetos/github-desktop` 301s to `/pt-br/projects/github-desktop`.
  Three URLs cannot be redirected and are not: `/`, `/downloads` and `/p/{slug}`
  were Portuguese and are now English at the same address.
- `/sitemap.xml` lists both languages of every public page with reciprocal
  `xhtml:link` alternates and `x-default`, and `robots.txt` points at it.
- Project cards link to the project page in the language being read.
- **Security:** the language switcher's `to` parameter accepted a
  protocol-relative URL — `?to=//evil.example` passed the `starts_with('/')`
  guard and emitted a cross-origin `Location`. It is now rebuilt from its path
  component instead of validated.
- The admin panel stays Portuguese, by decision.

### The catalogue is checked against the applications themselves

- Descriptions, feature lists and version numbers were verified against each
  application's own repository. ai-usagebar named five providers and has
  fourteen; the GitHub Desktop fork claimed two artifact types and builds six,
  and its multi-repository panel went unmentioned; SShvTerm said nothing about
  its zero-knowledge sync or its self-hostable server.
- The ai-usagebar guide stopped documenting a Windows tray app that is no
  longer in the tree — its install steps told the reader to enter a directory
  that does not exist.
- Every application has a changelog on its page, curated from its own
  changelog, in both languages.
- The seeder fills `upstream_repo`, which it never did — a fresh seed left the
  Monitor tracking nothing.

### Repository hygiene

- This `CHANGELOG.md`, which the project's own rules required and which
  `tools/release.sh` already tried to read.
- Vite and Tailwind, installed and never used, were removed — along with the
  `npm install && npm run build` that ran on every production deploy to produce
  a bundle no page loaded.
- "Downloads today" is counted in one timezone in all three places that show
  it; two of them counted in UTC on a screen that also showed the correct
  number.
- The access audit validates its filters, like the audit screen next to it.
- The admin panel's three security controls have tests, and the suite goes from
  36 passing to 75. `phpunit.xml` now names a database that does not exist, so a
  test that touches one fails instead of writing to the development database.
- The READMEs describe the download hub instead of the blog removed in 0.2.0.

## 0.6.0 - The site learns to speak English

- Bilingual routing, locale negotiation and a language switcher that keeps you
  on the page you were reading.
- The home page, the download list, the project page, the ShvIA models section,
  the GitHub Desktop page and the ai-usagebar guide all speak both languages —
  including dates, thousands separators and the comments inside command blocks.
- Database prose gets its English through `App\Support\Content`, and the
  translator's own fallback stops leaking English back onto Portuguese pages.
- The deploy runs composer when `composer.json` changes, not only when its lock
  does.

## 0.5.12 - The human gate becomes the refused push, not the commit

## 0.5.11 - The agent doc stops contradicting the COMMIT-RULE block

## 0.5.10 - COMMIT-RULE replaces the COMMITTER delegation: the agent commits again

- The COMMITTER kill-switch: the marker stays, the automation stops.

## 0.5.9 - Agent doc: the Releases rule and the English-only language rule

## 0.5.8 - Releases rule in the agent doc: the bump and the Release are one act

## 0.5.7 - Automatic releases: the `version.md` of master becomes a tag and a Release

## 0.5.6 - Fixes the AI-MEMORY module's degradation

- A real probe, a guard in two layers, the failure reason surfaced in the UI,
  and the path of ai-memory 2.x.

## 0.5.5 - A Workspaces screen in AI-MEMORY, with aggregate counts and a link from the dashboard

## 0.5.4 - Fixes the github-visualize fork paths after the reorganisation of `~/x`

- Joins the COMMITTER rollout: marker and PS block in the agent doc.

## 0.5.3 - Adopts the COMMITTER: an opt-in marker for the automatic commit cycle

## 0.5.2 - Reviews the AI-MEMORY screens, fixes the panel's pagination and turns on "live"

## 0.5.1 - Redesigns the AI-MEMORY dashboard

## 0.5.0 - Unifies GitHub View (production) with Monitor/version (local)

- A discreet version in the footer and under the user.
- GitHub View arrived across many commits: the three `github_*` tables, the
  Eloquent models, `GitHubClient` (GraphQL/REST) with an incremental upsert
  job, the listing screen, the day-by-hour commit heatmap, bulk import of a
  user's repositories, an hourly sync command, the `RepositoryOverview`
  dashboard, and organisation-repository discovery with autocomplete search.

## 0.4.4 - SShvTerm and ai-usagebar join the showcase, with Fabio Akita credited

## 0.4.3 - Upload suggests the name and version from the filename, on client and server

## 0.4.2 - The file admin sorts by most recent build, semver-aware

## 0.4.1 - Restores the animated dot-grid in the hero

## 0.4.0 - Redesigns the public showcase

- The Archivo design system, the terminal costume removed, and the SShvTerm
  agent given prominence.

## 0.3.1 - AI-MEMORY: agent and period filters, and sorting by duration in Sessions

## 0.3.0 - The AI-MEMORY module in the admin panel

- A read-only observatory over the external `ai-memory` SQLite index.

## 0.2.8 - Matomo tracking (site 2, config-driven)

- The README is translated to English; the original is preserved in
  `README_br.md`.

## 0.2.7 - The ai-usagebar page joins the menu

- Documentation and installation guides for Linux, macOS and Windows.

## 0.2.6 - A project can be a pure link or a hybrid

- `redirect_to_site` separates a project that lives on its own site from one
  that has both a site and downloads — in the menu, the admin and the showcase.

## 0.2.5 - Hybrid projects, and the ShvIA desktop app becomes downloadable

## 0.2.4 - Files: an Edit button, to correct a file's metadata

## 0.2.3 - The download list adopts the project page's layout, with per-OS badges

## 0.2.2 - Downloads are grouped by operating system

- The `os`, `arch`, `file_type` and `released_at` columns, `FilenameInspector`
  and a backfill.
- The upload form fills OS, architecture and type from the filename, with an
  override.
- OS detection from the User-Agent, version grouping and an install command.
- `/p/{slug}` grouped by OS, with a recommended card.
- Accessibility and responsive polish, plus documentation.

## 0.2.0 - The pivot: from blog to project and download hub

- Public browsing, a single-admin panel, and per-file downloads that are
  counted and audited. The blog is removed.

## 0.1.0 - MySQL replaces SQLite as the default database

- `CLAUDE.md` drops its SQLite reference and fixes the commit format.
