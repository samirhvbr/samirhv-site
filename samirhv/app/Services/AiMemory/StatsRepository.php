<?php

namespace App\Services\AiMemory;

use Illuminate\Support\Carbon;
use Throwable;

/** Contagens e séries temporais para o Dashboard do módulo AI-MEMORY. */
class StatsRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /**
     * Current totals, live from ai-memory itself. Each COUNT tolerates a table
     * that a given ai-memory version does not have (it becomes 0 instead of
     * taking the Dashboard down) — but ONLY that: see count().
     */
    public function counts(): array
    {
        return [
            'workspaces' => $this->count('SELECT COUNT(*) FROM workspaces'),
            'projects' => $this->count('SELECT COUNT(*) FROM projects'),
            'pages' => $this->count('SELECT COUNT(*) FROM pages WHERE is_latest = 1'),
            'sessions' => $this->count('SELECT COUNT(*) FROM sessions'),
            'observations' => $this->count('SELECT COUNT(*) FROM observations'),
            'embeddings' => $this->count('SELECT COUNT(*) FROM page_embeddings'),
            'handoffs_open' => $this->count("SELECT COUNT(*) FROM handoffs WHERE state = 'open'"),
            'proposals_pending' => $this->count("SELECT COUNT(*) FROM auto_improve_proposals WHERE status = 'pending'"),
        ];
    }

    /** [Y-m-d => total] contínuo dos últimos $days dias (observações criadas). */
    public function observationsByDay(int $days): array
    {
        return $this->byDay('observations', 'created_at', $days);
    }

    /** [Y-m-d => total] contínuo dos últimos $days dias (sessões iniciadas). */
    public function sessionsByDay(int $days): array
    {
        return $this->byDay('sessions', 'started_at', $days);
    }

    private function count(string $sql): int
    {
        try {
            return (int) $this->db->scalar($sql);
        } catch (Throwable $e) {
            // Swallow ONLY "this ai-memory version has no such table/column".
            // Anything else (permission, lock, IO) has to reach the controller
            // guard, which degrades the whole screen with an explanation —
            // showing a fabricated zero would also poison the durable history
            // written by `aimemory:snapshot`.
            if (! $this->isMissingSchema($e)) {
                throw $e;
            }

            return 0;
        }
    }

    /** Is this failure just a table/column absent in this ai-memory version? */
    private function isMissingSchema(Throwable $e): bool
    {
        return (bool) preg_match('/no such (table|column)/i', $e->getMessage());
    }

    /**
     * Group by day (UTC buckets, which is how ai-memory stores time) and fill
     * the empty days with 0 so the chart has no holes.
     */
    private function byDay(string $table, string $column, int $days): array
    {
        $start = Carbon::now('UTC')->subDays($days - 1)->startOfDay();
        $sinceMicros = $start->timestamp * 1_000_000;

        $found = [];
        try {
            foreach ($this->db->select(
                "SELECT date({$column} / 1000000, 'unixepoch') AS d, COUNT(*) AS total
                   FROM {$table}
                  WHERE {$column} >= ?
                  GROUP BY d",
                [$sinceMicros]
            ) as $row) {
                $found[$row->d] = (int) $row->total;
            }
        } catch (Throwable $e) {
            // Same rule as count(): only a missing table/column is tolerable.
            if (! $this->isMissingSchema($e)) {
                throw $e;
            }

            $found = [];
        }

        $series = [];
        $cursor = $start->copy();
        for ($i = 0; $i < $days; $i++) {
            $key = $cursor->format('Y-m-d');
            $series[$key] = $found[$key] ?? 0;
            $cursor->addDay();
        }

        return $series;
    }
}
