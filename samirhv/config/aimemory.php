<?php

/*
|--------------------------------------------------------------------------
| AI-MEMORY module (admin) — configuration and production coupling
|--------------------------------------------------------------------------
|
| The Samirhv admin has an "AI-MEMORY" screen that READS (read-only) the
| database of the `ai-memory` product (github.com/akitaonrails/ai-memory) —
| the long-term memory of the coding agents (Claude Code, Codex, etc.).
|
| >>> READ THIS BEFORE MOVING THE APP TO ANOTHER SERVER <<<
|
| ai-memory runs on the SAME production server as Samirhv and keeps its index
| in a WAL-mode SQLite file. The layout depends on the version:
|
|     2.x  /opt/ai-memory/data/db/memory.sqlite
|     1.x  /var/lib/docker/volumes/ai-memory-data/_data/db/memory.sqlite
|
| The admin screen opens that file straight off the filesystem, as a second
| read-only reader. That makes the module HOST-COUPLED: move the Samirhv app to
| another machine, change the ai-memory layout, or take away the PHP-FPM user's
| access and the screen stops returning data and shows the notice instead.
|
| >>> WHAT "ACCESS" MEANS FOR A WAL DATABASE <<<
|
| Read permission on memory.sqlite is NOT enough. A WAL reader also needs WRITE
| permission on the DIRECTORY that holds the file, because whenever ai-memory
| has checkpointed and closed its last connection the sidecar files `-shm`/
| `-wal` are gone and the reader has to create them. Without that, the first
| SELECT fails with SQLITE_READONLY_DIRECTORY, which PDO reports as the
| misleading "General error: 8 attempt to write a readonly database".
| The full recipe (groups, permissions, diagnosis) is in docs/AI-MEMORY.md §4.
|
*/

return [

    // Absolute path of memory.sqlite ON THE HOST. Set it in production via
    // AI_MEMORY_SQLITE_PATH in .env. Empty/missing => the module degrades.
    'path' => env('AI_MEMORY_SQLITE_PATH', '/opt/ai-memory/data/db/memory.sqlite'),

    // Name of the connection declared in config/database.php (read-only).
    'connection' => 'aimemory',

    // Timezone used to display timestamps (ai-memory stores UTC microseconds).
    'timezone' => env('AI_MEMORY_TIMEZONE', 'America/Sao_Paulo'),

    // How many days of history the Dashboard charts show.
    'chart_days' => 30,

    // Row cap per page in the listings (sessions/observations/pages).
    'per_page' => 50,
];
