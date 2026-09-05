<?php

namespace Tests\Unit\AiMemory;

use App\Services\AiMemory\AiMemoryDatabase;
use App\Services\AiMemory\StatsRepository;
use Illuminate\Support\Facades\DB;
use PDO;
use Tests\TestCase;
use Throwable;

/**
 * Regression tests for the availability guard of the AI-MEMORY module.
 *
 * The bug these lock down (production, Sep 2026): ai-memory 2.x installed to a
 * fresh /opt/ai-memory/data/db, whose directory it creates 0700 and whose
 * memory.sqlite it creates 0600. www-data could read the file but could not
 * write the DIRECTORY, so the first SELECT against a real table failed with
 * SQLITE_READONLY_DIRECTORY ("attempt to write a readonly database") — a WAL
 * reader has to create the -shm/-wal sidecars when the writer is not holding
 * them. `isAvailable()` probed with `SELECT 1`, which SQLite answers without
 * ever opening the file, so the guard stayed green and the admin screens
 * returned HTTP 500 instead of the notice.
 *
 * These tests run only where pdo_sqlite exists and the process is not root
 * (root ignores permission bits, so the failure cannot be simulated).
 */
class AiMemoryDatabaseTest extends TestCase
{
    private string $dir;

    private string $dbPath;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite is required to exercise the ai-memory reader.');
        }

        if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
            $this->markTestSkipped('running as root: permission bits would not be enforced.');
        }

        $this->dir = sys_get_temp_dir().'/aimemory-test-'.bin2hex(random_bytes(6));
        mkdir($this->dir.'/db', 0o755, true);
        $this->dbPath = $this->dir.'/db/memory.sqlite';

        $this->seedWalDatabase();
        $this->pointAppAt($this->dbPath);
    }

    protected function tearDown(): void
    {
        /* setUp() skips before it ever assigns $dir when pdo_sqlite is absent
           or the process is root — and PHPUnit still runs tearDown after a
           skip. Reading a typed property that was never initialised turns a
           clean skip into an Error, which is how eight tests reported as
           skipped AND errored on any machine without pdo_sqlite. */
        if (! isset($this->dir)) {
            parent::tearDown();

            return;
        }

        // Make the tree writable again before deleting it.
        @chmod($this->dir.'/db', 0o755);
        @chmod($this->dbPath, 0o644);
        foreach (glob($this->dir.'/db/*') ?: [] as $file) {
            @chmod($file, 0o644);
            @unlink($file);
        }
        @rmdir($this->dir.'/db');
        @rmdir($this->dir);

        DB::purge('aimemory');

        parent::tearDown();
    }

    public function test_it_reads_the_database_when_the_directory_is_writable(): void
    {
        $db = new AiMemoryDatabase;

        $this->assertTrue($db->isAvailable());
        $this->assertNull($db->unavailableReason());
        $this->assertSame(2, (int) $db->scalar('SELECT COUNT(*) FROM projects'));
    }

    public function test_the_connection_stays_read_only(): void
    {
        $db = new AiMemoryDatabase;
        $this->assertTrue($db->isAvailable());

        $this->expectException(Throwable::class);

        // `pragmas` in config/database.php pins PRAGMA query_only = 1, so even a
        // deliberate write attempt on this connection must fail.
        DB::connection('aimemory')->statement("INSERT INTO projects (id, name) VALUES (x'03', 'hack')");
    }

    public function test_it_degrades_when_a_wal_reader_cannot_create_the_sidecar_files(): void
    {
        // The exact production shape: writer idle (no -wal/-shm around), file
        // readable, directory not writable by the reader.
        $this->dropWalSidecars();
        chmod($this->dbPath, 0o444);
        chmod($this->dir.'/db', 0o555);
        DB::purge('aimemory');

        $db = new AiMemoryDatabase;

        $this->assertFalse($db->isAvailable(), 'the guard must see the readonly-directory failure');
        $this->assertStringContainsString('ESCRITA no diretório', (string) $db->unavailableReason());
        $this->assertStringContainsString($this->dir.'/db', (string) $db->unavailableReason());
    }

    public function test_select_1_would_not_have_caught_it(): void
    {
        // Why the probe reads sqlite_master: this is the assertion that fails
        // if anyone "simplifies" the probe back to a constant expression.
        $this->dropWalSidecars();
        chmod($this->dbPath, 0o444);
        chmod($this->dir.'/db', 0o555);
        DB::purge('aimemory');

        $this->assertSame(1, (int) DB::connection('aimemory')->select('SELECT 1')[0]->{'1'});

        $this->expectException(Throwable::class);
        DB::connection('aimemory')->select('SELECT count(*) FROM sqlite_master');
    }

    public function test_it_degrades_when_the_file_is_missing(): void
    {
        $this->pointAppAt($this->dir.'/db/does-not-exist.sqlite');

        $db = new AiMemoryDatabase;

        $this->assertFalse($db->isAvailable());
        $this->assertStringContainsString('não existe', (string) $db->unavailableReason());
    }

    public function test_it_degrades_when_the_path_is_not_configured(): void
    {
        $this->pointAppAt('');

        $db = new AiMemoryDatabase;

        $this->assertFalse($db->isAvailable());
        $this->assertStringContainsString('AI_MEMORY_SQLITE_PATH', (string) $db->unavailableReason());
    }

    public function test_stats_tolerate_a_table_this_ai_memory_version_lacks(): void
    {
        // page_embeddings / auto_improve_proposals do not exist in older
        // ai-memory versions: those counts must degrade to 0, alone.
        $counts = (new StatsRepository(new AiMemoryDatabase))->counts();

        $this->assertSame(2, $counts['projects']);
        $this->assertSame(0, $counts['embeddings']);
        $this->assertSame(0, $counts['proposals_pending']);
    }

    public function test_stats_do_not_hide_a_permission_failure_as_zero(): void
    {
        $this->dropWalSidecars();
        chmod($this->dbPath, 0o444);
        chmod($this->dir.'/db', 0o555);
        DB::purge('aimemory');

        // A fabricated zero would reach the durable history written by
        // `aimemory:snapshot`; the failure has to surface instead.
        $this->expectException(Throwable::class);
        (new StatsRepository(new AiMemoryDatabase))->counts();
    }

    /** Point config + connection at a given file, as production .env does. */
    private function pointAppAt(string $path): void
    {
        config([
            'aimemory.path' => $path,
            'database.connections.aimemory.database' => $path !== '' ? $path : ':memory:',
        ]);

        DB::purge('aimemory');
    }

    /** A minimal WAL database with the tables the module actually reads. */
    private function seedWalDatabase(): void
    {
        $pdo = new PDO('sqlite:'.$this->dbPath, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec('PRAGMA journal_mode = WAL');
        $pdo->exec('CREATE TABLE workspaces (id BLOB PRIMARY KEY, name TEXT)');
        $pdo->exec('CREATE TABLE projects (id BLOB PRIMARY KEY, workspace_id BLOB, name TEXT, repo_path TEXT, created_at INTEGER)');
        $pdo->exec('CREATE TABLE pages (id BLOB PRIMARY KEY, project_id BLOB, is_latest INTEGER DEFAULT 1)');
        $pdo->exec('CREATE TABLE sessions (id BLOB PRIMARY KEY, project_id BLOB, started_at INTEGER)');
        $pdo->exec('CREATE TABLE observations (id BLOB PRIMARY KEY, project_id BLOB, created_at INTEGER)');
        $pdo->exec('CREATE TABLE handoffs (id BLOB PRIMARY KEY, state TEXT)');
        $pdo->exec("INSERT INTO workspaces (id, name) VALUES (x'01', 'default')");
        $pdo->exec("INSERT INTO projects (id, workspace_id, name) VALUES (x'01', x'01', 'alpha'), (x'02', x'01', 'beta')");
        $pdo = null;
    }

    /**
     * Simulate "the writer checkpointed and closed": the sidecar files are gone
     * and the next reader has to create them.
     */
    private function dropWalSidecars(): void
    {
        foreach (['-wal', '-shm'] as $suffix) {
            if (file_exists($this->dbPath.$suffix)) {
                @unlink($this->dbPath.$suffix);
            }
        }
    }
}
