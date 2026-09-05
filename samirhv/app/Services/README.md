# `app/Services/`

Three kinds of class live here, and the subdirectory says which:

| Path | Kind | What it does |
|---|---|---|
| `Services/*.php` | Service | A use case or a capability: `FileIngestService`, `GithubReleaseChecker`, `AnalyticsService`, `AuditLogger`, `UserAgentParser`, `DownloadPresenter`. |
| `Services/AiMemory/*Repository.php` | Repository | Read-only queries against the **external** `ai-memory` SQLite index. Nothing here writes. |
| `Services/GitHub/Visualizations/*.php` | Presenter | Turns rows into what a chart needs — `RepositoryOverview`, `CommitHeatmap`. No queries of their own. |

## Why the repositories and presenters are not in `app/Repositories/` and `app/Presenters/`

A review in 0.7.1 noted, correctly, that eleven of the classes under this
directory are repositories and two are presenters, so `Services/` had become
"everything that is not a model or a controller".

The decision was to **name the pattern rather than move the files**. Moving
them changes a namespace in every one of them plus every importing file, for no
behaviour change — and it detaches `git blame` on exactly the files whose
history is worth reading, like the AI-MEMORY reader with its production
WAL-permissions regression.

What actually caused the confusion was that the convention was never written
down. It is written down now: **the subdirectory is the pattern.** A new
repository goes in a `*Repository.php` under its own subdirectory; a new
presenter goes under `Visualizations/`; anything else is a service at the root
of this directory.

If the count of repositories ever outgrows the services here, revisit it — but
move them in one commit that does nothing else.
