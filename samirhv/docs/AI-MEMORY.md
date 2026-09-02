# AI-MEMORY module (admin) — reading the ai-memory index

> **TL;DR** — The admin's **AI-MEMORY** tab reads, **read-only**, the
> **SQLite index of [ai-memory](https://github.com/akitaonrails/ai-memory)** —
> the long-term memory of the coding agents. That database lives on the **same
> production server**, so the module is **host-coupled**: move the app off that
> server, change the ai-memory layout, or take the PHP-FPM user's access away
> and the screen stops returning data and shows a notice instead. **That is the
> coupling, not a bug** — but it must never be an HTTP 500 (see §4.1). Usage
> statistics are copied daily into an app-owned MySQL table
> (`ai_memory_stat_snapshots`) so they **survive an ai-memory reset**.

---

## 1. What it is and why it exists

`ai-memory` is a server (Rust binary, also shipped as the Docker image
`akitaonrails/ai-memory`) that gives long-term memory to coding agents (Claude
Code, Codex, etc.). It:

- keeps the **Markdown wiki** as the source of truth (`<data>/wiki/`), and
- maintains a **derived SQLite index** (`<data>/db/memory.sqlite`, **WAL** mode)
  holding sessions, observations, pages, handoffs, embeddings, audit rows and an
  **FTS5** index for search.

The Samirhv admin opens that `memory.sqlite` directly and renders Dashboard,
Projects, Pages (wiki), Sessions, Observations, Handoffs and Search.

## 2. The production coupling (READ THIS)

```
┌───────────────────────── production server ──────────────────────────┐
│                                                                       │
│   PHP-FPM (Samirhv/Laravel)  ──reads (RO)──►  /opt/ai-memory/data/db/ │
│        [www-data]                             memory.sqlite           │
│                                               (+ -wal, -shm)          │
│                                                    ▲                  │
│   ai-memory service  ──writes (sole writer)────────┘                  │
│        [systemd or Docker]                                            │
└───────────────────────────────────────────────────────────────────────┘
```

The path depends on how ai-memory was installed, and **it changed on this server
when 2.0 went in**:

| Install | Data directory | memory.sqlite |
| --- | --- | --- |
| 2.x here (operator's choice, `--data-dir`) | `/opt/ai-memory/data` | `/opt/ai-memory/data/db/memory.sqlite` |
| upstream systemd default | `/var/lib/ai-memory` | `/var/lib/ai-memory/db/memory.sqlite` |
| 1.x Docker volume | `ai-memory-data` | `/var/lib/docker/volumes/ai-memory-data/_data/db/memory.sqlite` |

Find the real path on the server:

```bash
ls -l /opt/ai-memory/data/db/memory.sqlite                     # 2.x native install
systemctl cat ai-memory | grep -- --data-dir                   # whatever the unit says
docker volume inspect ai-memory-data -f '{{ .Mountpoint }}'    # 1.x, + /db/memory.sqlite
```

Set it in `.env` → `AI_MEMORY_SQLITE_PATH` (the config default is the 2.x path).
Remember that `deploy.sh` runs `config:cache`, so **a change in `.env` only takes
effect after `php artisan config:cache`**.

## 3. Read-only — and why we never write here

`ai-memory` is the **only legitimate writer**: it serialises writes through a
*writer actor* and maintains **FTS5 triggers** (`pages_fts`) and
**workspace×project invariant triggers**. Writing from outside would corrupt the
index. Upstream is explicit about it — even its own CLI never opens the SQLite
file, it talks to the server.

Guarantees in the code:

- the `aimemory` connection (`config/database.php`) is only ever given `SELECT`;
- that connection declares **`'pragmas' => ['query_only' => 1]`**, so the
  connector pins the engine-level guard at connect time, for every consumer and
  across reconnects: any `INSERT/UPDATE/DELETE/DDL` **fails**;
- the availability guard is **two layers** (§4.1), so a failure degrades into the
  notice instead of a 500.

> ai-memory **write** actions (approving/rejecting Auto Improve, generating
> embeddings) are **Phase 2** and must go through ai-memory's read-only HTTP API
> / MCP, never through this SQLite file. See §7.

## 4. Permissions (the #1 cause of "it stopped working")

**Read permission on `memory.sqlite` is not enough.** This is the single most
important fact in this document, and the one that took the module down in
September 2026.

A WAL database is read through two sidecar files next to it: `-wal` (the log) and
`-shm` (the shared-memory index). When ai-memory has checkpointed and closed its
last connection, **those files do not exist**, and the *reader* is the one that
has to create them — which requires **WRITE permission on the directory** that
holds the database. Without it, the very first SELECT fails with
`SQLITE_READONLY_DIRECTORY`, which PDO surfaces as the thoroughly misleading:

```
SQLSTATE[HY000]: General error: 8 attempt to write a readonly database
```

...on a plain `SELECT`. Two corollaries worth knowing before you try to be clever:

- **`?mode=ro` does not fix it.** A read-only handle cannot create the sidecars
  either. That is why the connection is a plain path plus `query_only`.
- **`chmod` on `-wal`/`-shm` does not stick.** SQLite recreates them with the
  mode of the *main* database file (measured: exactly its mode, regardless of the
  creating process's umask), and `wal_checkpoint(TRUNCATE)`/`VACUUM`/last-close
  delete them. So the mode you have to fix is the one on `memory.sqlite` and on
  its directory.

Why 2.0 triggered it: since 1.27.0 ai-memory creates data directories `0700` and
`memory.sqlite` `0600`, owned by the service user, *independently of umask*, and
leaves existing installations alone (`SECURITY.md`). The old Docker-volume file
predated that hardening; the fresh `/opt/ai-memory/data/db` install did not.

### 4.1 The two-layer guard (why this is no longer a 500)

1. `AiMemoryDatabase::isAvailable()` probes with **`SELECT count(*) FROM
   sqlite_master`**. It must be a query that *touches the file*: `SELECT 1` is a
   constant SQLite answers without ever opening the database, so it stayed green
   in exactly the broken scenario — that false positive is what produced the 500s.
2. `AiMemoryController::screen()` also wraps the screen's queries in
   `try/catch`. If the probe passes and a query still fails (permission changed
   mid-request, a lock outlived `busy_timeout`, an upgrade renamed a table), the
   screen degrades to the notice, `report()` logs the exception, and
   `AiMemoryDatabase::markUnavailable()` short-circuits the rest of the request.
   HTTP exceptions are rethrown, so `abort_if(..., 404)` still 404s.

`unavailableReason()` names the actual failure and the notice shows it, so the UI
tells you *which* of these it is. Regression tests:
`tests/Unit/AiMemory/AiMemoryDatabaseTest.php` (skipped where `pdo_sqlite` is
absent or the process is root).

### 4.2 Granting access — the shared-group recipe

Run on the server, as root. A **dedicated group** is used instead of the
service's own group, so this works whether ai-memory runs as `root` or as its own
system user: the database files stay owned by the service (owner keeps `rw`
regardless of group), and the group only carries the reader in.

```bash
DB=/opt/ai-memory/data/db/memory.sqlite     # adjust if the data dir differs
DBDIR=$(dirname "$DB")

# 1. a group whose only purpose is "may read the ai-memory index"
groupadd -f aimemory-read
usermod -aG aimemory-read www-data

# 2. traverse-only on the way in: group gets x, not r (no listing)
chgrp aimemory-read /opt/ai-memory /opt/ai-memory/data
chmod 0710          /opt/ai-memory /opt/ai-memory/data

# 3. the db directory: group rwx + SETGID, so every file created in it — by
#    either process — inherits the group
chgrp aimemory-read "$DBDIR"
chmod 2770          "$DBDIR"

# 4. the database itself: group rw. SQLite creates -wal/-shm with this exact
#    mode, which is what lets both processes maintain them.
chgrp aimemory-read "$DB"
chmod 0660          "$DB"
for f in "$DB"-wal "$DB"-shm; do [ -e "$f" ] && chgrp aimemory-read "$f" && chmod 0660 "$f"; done

# 5. php-fpm only picks up the new group membership on restart
systemctl restart php8.4-fpm
```

If ai-memory runs under systemd with `ProtectSystem=strict`, make sure its unit
has `ReadWritePaths=` covering the data dir (`systemctl cat ai-memory`); the
upstream template only lists `/var/lib/ai-memory`.

This does hand `www-data` real write capability over that directory, a deliberate
departure from ai-memory's declared threat model ("single-tenant service, we rely
on filesystem permissions"). The app's side of the bargain is `query_only` plus
SELECT-only repositories. If that trade ever stops being acceptable, §7 has the
two decoupled alternatives.

### 4.3 Diagnosis, from the PHP user's point of view

```bash
P=/opt/ai-memory/data/db/memory.sqlite
sudo -u www-data test -r "$P"        && echo "read: OK"  || echo "read: DENIED"
sudo -u www-data test -w "$(dirname "$P")" && echo "dir write: OK" || echo "dir write: DENIED  <-- the 500"
ls -la "$(dirname "$P")"; id www-data
sudo -u www-data php /srv/www/samirhv.com.br/samirhv/artisan aimemory:snapshot   # must write a snapshot
```

A one-liner that reproduces exactly what the module does (fails the same way the
page did, succeeds when the permissions are right):

```bash
sudo -u www-data php -r '$d=new PDO("sqlite:/opt/ai-memory/data/db/memory.sqlite");
  $d->exec("pragma query_only=1");
  var_dump($d->query("select count(*) from sqlite_master")->fetchColumn());'
```

## 5. Durable history (`ai_memory_stat_snapshots`)

`memory.sqlite` is a **derived** index — it can be rebuilt or reset. So that the
**usage history is not lost**, a scheduled command writes a daily snapshot into
**the app's own MySQL database**:

- Command: `php artisan aimemory:snapshot` (idempotent per day — `updateOrCreate`
  on `captured_on`). If ai-memory is unreachable it **writes nothing and keeps**
  the existing history: a row of zeros would draw a cliff that never happened.
- Schedule: `routes/console.php` → `Schedule::command('aimemory:snapshot')->dailyAt('03:10')`.
  Requires Laravel's cron on the server: `* * * * * php artisan schedule:run`.
- The Dashboard shows **live** totals (from ai-memory) and the **historical
  evolution** (from this table, which survives a reset).
- Snapshots also feed the Dashboard *sparklines* and the delta ("+N in X days"):
  the delta compares the **live** number now against the oldest snapshot inside
  the window, and the UI states **how many days** that interval really spans — if
  snapshots are missing, the label says so.

### "Live" (Dashboard)

`memory.sqlite` is written by the **agents**, not by this app — there is no event
of ours to broadcast, so WebSocket/broadcast would buy nothing (just one more
daemon on the server, which would end up polling the file anyway).

The Dashboard uses **short browser polling** instead: `dashboard.js` calls
`GET /admin/ai-memory/live` (same session/middleware as the panel) every 15s and
swaps **only the values** — numbers, bar heights, axis ceiling and the equivalent
table. Nothing is recreated in the DOM, so it neither flickers nor jumps.
Details:

- **a hidden tab does not poll** (`visibilitychange`); it refreshes on return;
- **pausable** through the "ao vivo" button (kept in `localStorage`);
- **repeated errors back off** (up to 2 min) and the dot turns red;
- while fetching, the previous drawing stays up at lower opacity — no skeleton.

To change the interval: `data-every` (seconds) on the `[data-aim-live]` element.

## 6. Code map

| Layer | File |
| --- | --- |
| RO connection + `isAvailable`/`unavailableReason` + pagination | `app/Services/AiMemory/AiMemoryDatabase.php` |
| Time formatting (µs → local) | `app/Services/AiMemory/AiMemoryTime.php` |
| Queries (one per screen) | `app/Services/AiMemory/{Stats,Project,Workspace,Page,Session,Observation,Handoff,Search}Repository.php` |
| Derived Dashboard numbers (no database) | `app/Services/AiMemory/DashboardSummary.php` |
| Dashboard "live" JSON | `AiMemoryController::live()` → route `admin.ai-memory.live` |
| Module visual system (CSS, `@once`) | `resources/views/admin/ai-memory/_styles.blade.php` |
| Thin controller + availability guard | `app/Http/Controllers/Admin/AiMemoryController.php` |
| Routes | `routes/admin.php` (group `admin.ai-memory.*`) |
| Views | `resources/views/admin/ai-memory/*.blade.php` |
| Module sub-navigation (+ CSS, `@once`) | `resources/views/admin/ai-memory/_tabs.blade.php` |
| Chart reading aids (crosshair/tooltip/keyboard) | `public/js/admin/ai-memory/dashboard.js` |
| Degradation notice | `resources/views/admin/ai-memory/_unavailable.blade.php` |
| Config | `config/aimemory.php`, `aimemory` connection in `config/database.php` |
| Guard regression tests | `tests/Unit/AiMemory/AiMemoryDatabaseTest.php` |
| History | migration `..._create_ai_memory_stat_snapshots_table`, `App\Models\AiMemoryStatSnapshot`, `app/Console/Commands/SnapshotAiMemoryStats.php` |

### ai-memory schema (reference)

The queries follow ai-memory's migrations (`crates/ai-memory-store/migrations/`,
which reach V58 in 2.0.0). Points worth remembering:

- **timestamps = microseconds** since the epoch (UTC) → divide by 1,000,000;
- **ids = BLOB** (UUIDv7) → URLs use `lower(hex(id))` (32 chars);
- `pages`: current version is `is_latest=1`; history through `supersedes`;
- search through `pages_fts` (FTS5, columns `title`+`body`), ordered by `bm25`;
- `workspaces`: only `id` and `name` are referenced in the code; `projects`,
  `pages` and `sessions` carry `workspace_id` directly (not only through
  `project_id`), which is what `WorkspaceRepository` sums for its counts;
- 2.0.0 added no renames — V56/V57/V58 are additive (entity-link validity,
  experience-pass state). But `pages`/`sessions`/`links`/`users` have been
  rebuilt by migrations before (`DROP` + `RENAME`), so **`rowid` is not stable
  across versions** — never persist one;
- `page_embeddings` and `auto_improve_proposals` may be absent in older
  versions: `StatsRepository` tolerates *"no such table/column"* for those
  counts and nothing else (a permission error must not be shown as a zero).

## 7. Phase 2 (out of current scope)

The decoupled options, if reading the file directly ever stops being acceptable
(app moves off the server, or the write permission of §4.2 becomes unwanted):

- **ai-memory's read-only HTTP API** — `/api/v1/*` is read-only by construction
  (workspaces, projects, pages, search, recent, briefing, overview, handoffs,
  graph, sessions, observations), authenticated with a User-level `aim_` API key
  (`ai-memory api-key add --username <u> --label painel-laravel`). This is the
  path upstream supports for exactly our use case, and it is what the write
  actions below would need anyway.
- **Periodic consistent snapshot** — `ai-memory backup --to <file>` (SQLite
  online backup API) into a directory owned by `www-data`, with
  `AI_MEMORY_SQLITE_PATH` pointing at the copy. Costs freshness, needs zero
  permission on the ai-memory data dir, and the copy has no WAL sidecars at all.
- **Auto Improve** (approve/reject proposals) and **generating embeddings** are
  **writes**, therefore API/MCP only — never this SQLite file.
- Visual **Knowledge Graph** from the `links` table; dedicated Embeddings /
  ai-memory audit screens.
