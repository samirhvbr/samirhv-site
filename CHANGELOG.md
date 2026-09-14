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

## 0.9.0 - A project page can carry a section of its own

`/p/{slug}` renders a header, a description, an access panel, a changelog and a
list of files. That is enough for a download and not enough for a product: the
ShvIA page needed a section about the models behind it, and `show.blade.php`
grew an `@includeWhen($project->slug === 'shvia', ...)` to get one. A second
project would have made it two lines in a file that has nothing to do with
either project, and a fourth would have made it a list nobody remembers to
update — the same failure mode `Project::getMarkUrlAttribute()` already avoids
by treating the project mark as a file on disk rather than a column.

So the section is a convention now: `partials/projects/<slug>.blade.php`, picked
up by a single `@includeIf` between the description and the changelog. Adding a
section is adding a file; `show.blade.php` is not touched again.
`partials/shvia-models.blade.php` moved to `partials/projects/shvia.blade.php`
and is the first thing the convention renders, so the mechanism is exercised by
the section it replaced.

The slug comes from route-model binding and is `Str::slug`-shaped, so it cannot
carry a path separator into a view name.

`public/css/site/project-sections.css` is the other half. The two curated pages
that came before it — ai-usagebar and github-desktop — each carry their own
inline styles, which is why the site has three slightly different cards, three
code blocks and three caption styles. The new sections share one vocabulary of
token-based classes, so writing one is writing markup and no CSS.

## 0.9.0 - The English project page printed its category in Portuguese

`App\Support\Content` exists because a project's title, description and
category are written in the admin, in Portuguese, and are content rather than
interface. Every surface that renders them goes through it — the downloads list,
the structured data in the page head, the meta description. Two did not: the
`<h1>` and the category tag of `/p/{slug}`, which echoed the database column
straight out.

The category is the one that showed. The English page rendered an English
description under a tag reading "Assistente IA", and `/projects/tura-notes` put
"Notas em Markdown" above prose about Markdown notes. The title survived only
because every project is currently named the same in both languages; the day one
is not, it would have leaked the same way, which is why both go through Content
now rather than only the one that was visibly wrong.

BilingualRenderTest never saw it. It is written against
`/projects/github-desktop`, which is a `Route::view` with no database row — so
the page with no database prose is the page the leak test reads, and the pages
that have some were never checked.

## 0.8.2 - The deploy runs from a copy, because it rewrites itself

The entry below added a step and the deploy did not run it. It reported
`✅ Deploy concluido` anyway, which is the part that matters: the run looked
clean, `migrate` was followed straight by the cache rebuild, and `/p/tura-notes`
was still a 404 afterwards.

`deploy.sh` runs `git merge` on the repository that contains it, so it rewrites
itself halfway through its own execution. Bash does not load a script into
memory before running it — it reads as it executes, straight from the file. Swap
the file underneath it and what runs after the merge is no longer reliably the
file that started running. A skipped step is the good outcome; the bad one is
bash resuming mid-command and executing half a line. Reproduced at the real
file's geometry — merge near byte 6800, insertion near byte 9800 — where the line
immediately after the rewrite was swallowed and the script still exited 0.

So the script now copies itself into `/run` and re-execs from there, before the
lock and after the root check, so a run without `sudo` still gets the root
message rather than a permission error. `bash "$copy"` rather than executing it,
because `/run` is usually mounted `noexec`. An `EXIT` trap removes the copy on
every path out, including `fail`.

THE CONSEQUENCE, WRITTEN DOWN: a change to `deploy.sh` now takes effect on the
FOLLOWING deploy, never the one that delivered it — the running process executes
the version it started with, whole. That is the trade being made: a predictable
effect one deploy later instead of an unpredictable one now.

## 0.8.1 - The deploy syncs the project catalogue, not just the schema

`deploy.sh` ran `migrate` and never `db:seed`, so the catalogue reached the
server as code and never as rows — `migrate` creates tables, not content. The
entry below is what made it visible: Tura Notes shipped with its seeder entry,
its English translation and its mark, all of it live on the server, and
`/p/tura-notes` answered 404. The row did not exist, and nothing in the deploy
was ever going to create it. The same hole was waiting for every project added
after it.

Step 7 now runs `db:seed --class=ProjectsSeeder --force`, after the migrations
and before the caches are rebuilt. Naming the class is not a detail: the bare
`db:seed` runs `DatabaseSeeder`, which also calls `AdminUserSeeder` — and that
one re-hashes the admin password whenever `ADMIN_PASSWORD` is present in `.env`,
and sets `must_change_password`. On every deploy.

A failing seeder warns, notifies and lets the deploy continue. Stopping between
the migration and the cache rebuild would leave the new code running against the
routes and views compiled from the previous commit, which is worse than a
catalogue one deploy behind.

THE COST, WRITTEN DOWN: `ProjectsSeeder` is authoritative — `updateOrCreate` by
slug — so a title, description, category, icon, order or flag edited in the
admin returns to what the code says at the next deploy. That is the intent its
own docblock already declares, and the download FILES stay the admin's: nothing
in this step touches them.

## 0.8.0 - Tura Notes joins the catalogue

Tura Notes is a local-first Markdown note-taking app — you pick a folder, the
`.md` files inside it are your notes, and there is no proprietary format and no
account. It is published openly at `samirhvbr/tura-notes`, but the signed and
notarised macOS `.dmg` is **not** attached to the GitHub Release: the Developer
ID certificate lives in a keychain rather than in a CI secret, so the machine
that holds it is the machine that packages. This site is that build's
distribution channel, which is why the project belongs here and not only on
GitHub. Its `build-local.sh --publish` uploads through `php artisan files:add`,
so the file arrives on the private downloads disk, hashed and counted, like any
other.

The seeder carries it in second place, after ShvIA, with the Portuguese
description; `lang/en/content.php` carries the English, and the new category
"Notas em Markdown" is translated alongside it. Its mark is in
`public/img/projects/tura-notes/`, which is the convention the previous entry
introduced.

## 0.8.0 - A project can have a logo instead of a glyph

The catalogue drew a Font Awesome icon inside a rounded square, in four
templates — the home page's featured card and its list, the downloads card and
the project header — and each held its own copy of the markup. So a project with
an actual logo had to be taught to four places, or would show a generic glyph in
three of them.

`partials/project-mark` is now the single place that decides what goes inside
that square. It takes the square's own classes from the caller, because those
four are different sizes and radii and none of that changes; what it owns is the
content and the fall-through — logo, then icon, then nothing at all, since an
empty bordered box is worse than no box.

`Project::$mark_url` answers from a convention on disk,
`public/img/projects/<slug>/mark.svg`, rather than from a new column. Same
reason the English prose lives in `lang/` and not in the database: a column
would need a field in the admin CRUD, and the admin is out of scope. A project
with no file keeps its icon, which is what every project did before this
existed. The directory is scanned once per request rather than once per card,
because the same project is drawn on the home page, in the list and on its own
page; `forgetMarks()` is what makes that memoization testable.

## 0.7.4 - Nothing goes into the cache that has to come back as an object

The public home page was answering 500 on every request:

> The script tried to call a method on an incomplete object […] the class
> definition "Illuminate\Database\Eloquent\Collection" […] was loaded
> _before_ unserialize() gets called

`config/cache.php` carries Laravel 13's `serializable_classes => false`: the
framework refuses to unserialize any PHP class out of cache storage, so that a
leaked `APP_KEY` cannot be turned into a gadget chain. Writing an object still
works — it is the read that hands back a `__PHP_Incomplete_Class`. Two places in
this app were storing objects, and neither of them knew it.

**The projects menu.** The `layouts.app` view composer cached the published
projects as an Eloquent Collection under `nav.projects`, for six hours, on every
public page. The read after the first write turned it into a broken object and
`$navProjects->isNotEmpty()` in the layout took the site down with it — home,
project pages, downloads, every URL that renders the shell. It now caches the
raw attributes, an array of arrays, and rehydrates the models with
`Project::hydrate()` on the way out: same query, same menu, same invalidation.

**The monitor's refresh floor.** `monitor:last-refresh` stored `now()`, a Carbon
instance, and the check reading it back was `instanceof CarbonInterface` —
always false against an incomplete object. The 5-minute floor between two real
GitHub checks never fired, so every click on "Verificar agora" went straight to
an API that allows 60 unauthenticated requests per hour, which is the one thing
that block exists to prevent. The key now holds a Unix timestamp.

Nothing else in the app cached an object: the sitemap caches a string, and the
release checker and the repository suggestions cache arrays of scalars.

## 0.7.3 - the git hooks are regenerated from repodocs

Both hooks of the standard are rewritten from repodocs, and `tools/release.sh`
with them when it came from there. `commit-msg` checks the shape of the subject
(`X.Y.Z - description`), refuses a Conventional Commits prefix and a vague
message, **and checks that the subject's `X.Y.Z` is the version this commit
carries in `version.md`**. `pre-push` compares the local `version.md` against
the remote default branch for a repeated or a backwards version — **only when
the push actually updates that branch**, so a branch deletion, a tag and a topic
branch pass through.

The hook does **not** check the language and could not: what it measures is the
shape and the number.

Escape hatch, declared in both: `REPODOCS_NO_HOOK=1`. In a fresh clone, enable them with
`git config core.hooksPath tools/git-hooks`.

## 0.7.2 - the git hooks arrive from repodocs and are enabled here

Both hooks of the standard now run here: `commit-msg`, which checks the shape of
the subject (`X.Y.Z - description`), refuses a Conventional Commits prefix and a
vague message, **and checks that the subject's `X.Y.Z` is the version this commit
carries in `version.md`**; and `pre-push`, which compares the local `version.md`
against the remote default branch for a repeated version and for one that moves
backwards.

The hook does **not** check the language and could not: what it measures is the
shape and the number.

Until now the commit rule lived here only as prose in `CLAUDE.md`, and prose is
what gets forgotten at the end of a long session. On 07/09/2026 the hooks were
enabled in 3 clones out of 58, and two repositories of the fleet were measurably
off the norm with nothing to say so.

Escape hatch, declared in both: `REPODOCS_NO_HOOK=1`. It exists so the hooks stay installed —
a guard with no declared bypass gets bypassed with `--no-verify`, which switches
off every guard at once. In a fresh clone, enable them with
`git config core.hooksPath tools/git-hooks`.

## 0.7.1 - The findings the 0.7.0 review left on the table

A sweep through the medium and low priority findings of the review that
produced 0.7.0.

### Things that were declared and never happened

- `TrackPageView` skipped thirteen path prefixes for routes that do not exist
  in this app — Breeze scaffold. `bootstrap/app.php` declared a JSON exception
  renderer for `api/*` routes that were never registered. `EnsurePasswordChanged`
  exempted a route its middleware group never sees.
- `admin.github-view.repos.status` got the polling client its own docblock
  described, so a syncing repository card stops freezing until a page reload.
- `laravel/pint` had been a dev dependency since the beginning and had never
  run; it now runs over the codebase and on every push.

### Screens that paid per row, per file and per fork

- `is_mirrored` made one filesystem call per rendered file — invisible in a
  query log. One listing per request now, invalidated by the two places that
  write to the disk.
- The public nav menu queried on every page view; it is cached, and the
  `Project` model forgets the key on save, delete and restore.
- The download audit loaded every file in the table into a `<select>`.
- The monitor's "Verificar agora" fired one synchronous GitHub call per tracked
  project against a 60-per-hour unauthenticated limit. A five-minute floor now
  stands between real checks, and the screen says so.

### Correctness

- `page_views.locale` recorded the language the browser asked for instead of
  the one the page rendered in — on a bilingual site, the wrong measurement.
- "Today" was cut in UTC in two more screens, the same defect fixed for
  downloads. `AnalyticsService::todayStart()` is the panel's one definition.
- `EnsureIsAdmin` signed a valid user out for reaching a page they may not see;
  it answers 403.
- The admin file list compared versions with raw `version_compare()` while
  `App\Support\SemVer` existed and was used everywhere else.

### The head, and getting in with a keyboard

- The social card promised `summary_large_image` and shipped no image. There is
  one now, generated by `tools/make-og-card.php`, alongside `og:url`,
  `og:site_name`, the alternate locale, and JSON-LD describing each download
  page as a `SoftwareApplication`.
- The projects menu was an anchor that navigated nowhere and opened only on
  hover — absent for anyone using a keyboard. It is a button that states its
  expanded state, a skip link precedes the page, and the content sits in a
  `<main>` landmark.

### Structure

- 912 lines of CSS left thirteen inline `<style>` blocks and became cacheable
  files; the new `vasset()` helper cache-busts them by mtime. Inline `style=`
  attributes remain, so a `style-src 'self'` policy is still not possible.
- Import loops and validation left the controllers for `RepositoryImporter`,
  `ProjectRequest` and `Project::uniqueSlug()`.
- The four pure classes that decide the most — `SemVer`, `OsDetector`,
  `FilenameInspector`, `UserAgentParser` — got their first tests. The suite goes
  from 137 to 158 passing.

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
