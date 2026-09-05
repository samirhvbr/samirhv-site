<?php

namespace App\Services\AiMemory;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * LOW-LEVEL, READ-ONLY access to the ai-memory SQLite index.
 *
 * ▸ WHY THIS EXISTS, AND WHY IT CAN "STOP WORKING"
 *   ai-memory (github.com/akitaonrails/ai-memory) runs on the SAME production
 *   host as Samirhv and keeps its index in a WAL-mode SQLite file (2.x installs
 *   it under /opt/ai-memory/data/db/memory.sqlite; 1.x used the Docker volume
 *   `ai-memory-data`). This class opens that file as a SECOND READER, so the
 *   module is HOST-COUPLED by design: move the app to another machine, change
 *   the install layout, or take away the PHP-FPM user's access and the queries
 *   stop. When that happens `isAvailable()` returns false, `unavailableReason()`
 *   explains what exactly failed, and the UI shows the notice — never a 500.
 *   See docs/AI-MEMORY.md and config/aimemory.php.
 *
 * ▸ WAL READERS NEED A WRITABLE DIRECTORY (the trap that caused the 2.0 outage)
 *   Read permission on memory.sqlite is NOT enough. A WAL database is read
 *   through the shared-memory index `-shm`; whenever that file is absent (the
 *   writer checkpointed and closed its last connection) ANY reader has to
 *   create `-shm`/`-wal` itself, which requires WRITE permission on the
 *   directory holding the database. Without it SQLite fails the very first read
 *   with `SQLITE_READONLY_DIRECTORY` — reported by PDO as the confusing
 *   "General error: 8 attempt to write a readonly database" on a plain SELECT.
 *   Opening the file with `?mode=ro` does NOT help: a read-only handle cannot
 *   create `-shm` either. The supported fix is filesystem permission on the
 *   directory (docs/AI-MEMORY.md §4).
 *
 * ▸ READ-ONLY (security invariant)
 *   ai-memory is the ONLY legitimate writer: it serialises writes through a
 *   writer actor and maintains FTS5 (pages_fts) and workspace×project invariant
 *   triggers. Writing from outside would corrupt all of that. The connection is
 *   pinned with `PRAGMA query_only = 1` (declared in config/database.php) and
 *   only SELECT is exposed here.
 *
 * ▸ TIMESTAMPS
 *   ai-memory stores time as INTEGER MICROSECONDS since the epoch (UTC).
 *   Use AiMemoryTime::ts() to render them in the display timezone.
 */
class AiMemoryDatabase
{
    /** Per-request memo of the availability probe. */
    private ?bool $available = null;

    /** Operator-facing explanation of the last failure (UI text, pt-BR). */
    private ?string $reason = null;

    /** Absolute path of memory.sqlite on the host (used by the notice). */
    public function path(): string
    {
        return (string) config('aimemory.path');
    }

    /** Display timezone for timestamps. */
    public function timezone(): string
    {
        return (string) config('aimemory.timezone', 'America/Sao_Paulo');
    }

    /**
     * Is the ai-memory database readable RIGHT NOW?
     *
     * Never throws: a missing file, a missing pdo_sqlite extension, denied
     * permission or a lock all become `false` plus a reason. The result is
     * memoised on the instance (registered as a singleton) so a page with
     * several repositories probes only once.
     *
     * The probe deliberately reads `sqlite_master` instead of `SELECT 1`:
     * `SELECT 1` is a constant SQLite answers without ever opening the database
     * file, so it stays green in exactly the broken scenario described in the
     * class docblock. Reading the schema touches page 1 and therefore forces the
     * WAL index setup that actually fails.
     */
    public function isAvailable(): bool
    {
        if ($this->available !== null) {
            return $this->available;
        }

        $path = $this->path();

        if ($path === '') {
            return $this->fail('O caminho do banco (AI_MEMORY_SQLITE_PATH) está vazio nesta instalação.');
        }

        if (! is_file($path)) {
            return $this->fail("O arquivo [{$path}] não existe neste servidor.");
        }

        if (! is_readable($path)) {
            return $this->fail("O usuário do PHP-FPM não tem permissão de leitura em [{$path}].");
        }

        try {
            $this->connection()->select('SELECT count(*) FROM sqlite_master');

            $this->reason = null;

            return $this->available = true;
        } catch (Throwable $e) {
            return $this->fail($this->explain($e));
        }
    }

    /** Why the module is degraded, in operator language — or null when it is fine. */
    public function unavailableReason(): ?string
    {
        return $this->available === false ? $this->reason : null;
    }

    /**
     * Degrade the module after a query that failed even though the probe passed
     * (permission changed mid-request, ai-memory upgrade renamed a table, lock
     * timeout...). Called by the controller's guard so every later screen and
     * the live endpoint in this request answer "unavailable" instead of retrying.
     */
    public function markUnavailable(Throwable $e): void
    {
        $this->fail($this->explain($e));
    }

    /**
     * Read-only SELECT. Returns an array of stdClass (query builder default).
     * Callers do not need to check isAvailable() first: the controller guard
     * turns any failure here into the explanatory notice.
     */
    public function select(string $sql, array $bindings = []): array
    {
        return $this->connection()->select($sql, $bindings);
    }

    /** First row of a SELECT, or null. */
    public function selectOne(string $sql, array $bindings = []): ?object
    {
        return $this->connection()->select($sql, $bindings)[0] ?? null;
    }

    /** Scalar value of the first column of the first row (e.g. COUNT(*)). */
    public function scalar(string $sql, array $bindings = []): mixed
    {
        $row = $this->selectOne($sql, $bindings);
        if ($row === null) {
            return null;
        }

        return array_values((array) $row)[0] ?? null;
    }

    /**
     * Paginate a raw SELECT reusing LengthAwarePaginator, so the views keep
     * using `{{ $x->links() }}` and `->withQueryString()` like the rest of the
     * admin. `$sql` must NOT contain LIMIT/OFFSET (added here).
     */
    public function paginate(string $sql, array $bindings, string $countSql, array $countBindings, int $perPage): LengthAwarePaginator
    {
        $total = (int) $this->scalar($countSql, $countBindings);
        $page = max(1, (int) LengthAwarePaginator::resolveCurrentPage());
        $offset = ($page - 1) * $perPage;

        $items = $total > 0
            ? $this->select($sql.' LIMIT ? OFFSET ?', [...$bindings, $perPage, $offset])
            : [];

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /** Record the degraded state and its reason. Always returns false. */
    private function fail(string $reason): bool
    {
        $this->reason = $reason;

        return $this->available = false;
    }

    /**
     * Turn a driver exception into something an operator can act on. The
     * readonly case gets the full explanation because its PDO message ("attempt
     * to write a readonly database" on a SELECT) points at the wrong problem.
     */
    private function explain(Throwable $e): string
    {
        $message = $e->getMessage();
        $path = $this->path();
        $dir = $path !== '' ? dirname($path) : '';

        if (str_contains($message, 'attempt to write a readonly database')) {
            return 'O banco está em modo WAL e o usuário do PHP-FPM (www-data) não tem permissão de '
                ."ESCRITA no diretório [{$dir}]. Um leitor de WAL precisa poder criar os arquivos "
                .'`-shm`/`-wal` quando eles não existem — só leitura no `memory.sqlite` não basta. '
                .'Ver docs/AI-MEMORY.md §4.';
        }

        if (str_contains($message, 'unable to open database file')) {
            return "O SQLite não conseguiu abrir [{$path}] nem seus arquivos `-wal`/`-shm` — normalmente "
                ."falta permissão de leitura em algum deles, ou de travessia (x) em [{$dir}].";
        }

        if (str_contains($message, 'could not find driver')) {
            return 'A extensão `pdo_sqlite` do PHP não está instalada neste servidor.';
        }

        if (str_contains($message, 'database is locked')) {
            return 'O banco está travado por outro processo há mais tempo que o `busy_timeout` — '
                .'o ai-memory pode estar num ciclo longo de escrita. Tente novamente em instantes.';
        }

        if (preg_match('/no such (table|column)/i', $message)) {
            return 'O esquema do ai-memory nesta versão não tem uma tabela/coluna que esta tela consulta '
                ."(detalhe do driver: {$message}). Provável mudança de versão do ai-memory.";
        }

        return "Falha ao consultar [{$path}]: {$message}";
    }

    /**
     * The read-only connection. Private on purpose: nobody should grab the raw
     * connection and write. `PRAGMA query_only = 1` is applied by the connector
     * itself (see the `pragmas` key of the `aimemory` connection in
     * config/database.php), so it also covers any other consumer of the
     * connection and survives reconnects.
     */
    private function connection(): ConnectionInterface
    {
        return DB::connection((string) config('aimemory.connection', 'aimemory'));
    }
}
