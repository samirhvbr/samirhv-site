<?php

namespace App\Console\Commands;

use App\Models\AiMemoryStatSnapshot;
use App\Services\AiMemory\AiMemoryDatabase;
use App\Services\AiMemory\StatsRepository;
use Illuminate\Console\Command;
use Throwable;

/**
 * Writes the daily snapshot of ai-memory statistics into the durable
 * `ai_memory_stat_snapshots` table (MySQL). Idempotent per day (updateOrCreate
 * on captured_on). Scheduled in routes/console.php (daily).
 *
 * If ai-memory is unreachable (app moved off the server, layout or permission
 * changed), it writes NOTHING and exits successfully — the existing history is
 * preserved. Writing a row of zeros would be worse than writing nothing: the
 * charts would show a cliff that never happened.
 */
class SnapshotAiMemoryStats extends Command
{
    protected $signature = 'aimemory:snapshot';

    protected $description = 'Grava um retrato diário das estatísticas do ai-memory (histórico durável em MySQL)';

    public function handle(AiMemoryDatabase $db, StatsRepository $stats): int
    {
        if (! $db->isAvailable()) {
            $this->warn("ai-memory indisponível em [{$db->path()}]: {$db->unavailableReason()} — retrato pulado, histórico preservado.");

            return self::SUCCESS;
        }

        try {
            $counts = $stats->counts();
        } catch (Throwable $e) {
            // The probe passed and the queries still failed (permission changed
            // between the two, lock, ai-memory upgrade mid-run). Same rule as
            // above: preserve the history and report the reason.
            report($e);

            $this->warn("ai-memory indisponível em [{$db->path()}]: {$db->unavailableReason()} — retrato pulado, histórico preservado.");

            return self::SUCCESS;
        }

        $snapshot = AiMemoryStatSnapshot::updateOrCreate(
            ['captured_on' => today()],
            [...$counts, 'raw_json' => $counts],
        );

        $this->info('Retrato de '.$snapshot->captured_on->format('d/m/Y').': '
            .$counts['pages'].' páginas, '
            .$counts['sessions'].' sessões, '
            .$counts['observations'].' observações.');

        return self::SUCCESS;
    }
}
