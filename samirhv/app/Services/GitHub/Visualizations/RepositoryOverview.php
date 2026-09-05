<?php

namespace App\Services\GitHub\Visualizations;

use App\Models\GitHubView\Commit;
use App\Models\GitHubView\Repository;
use App\Models\GitHubView\WorkflowRun;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Stats por repositório p/ o dashboard (chips diários de atividade, totais de
 * commits/linhas, último CI) — calculados em LOTE (evita N+1). Porte de
 * app/presenters/visualizations/repository_overview.rb (github-visualize).
 */
class RepositoryOverview
{
    public const CHIP_DAYS = 21;

    /** @var Collection<int, Repository> */
    private Collection $repositories;

    /** @var array<int, array<string, mixed>>|null */
    private ?array $stats = null;

    /** @param iterable<Repository> $repositories */
    public function __construct(iterable $repositories)
    {
        $this->repositories = collect($repositories);
    }

    /** @return array<string, mixed> */
    public function for(Repository $repository): array
    {
        return $this->statsByRepositoryId()[$repository->id] ?? $this->emptyStats();
    }

    public function maxCommits(): int
    {
        return (int) ($this->repositories->max(fn (Repository $r) => $this->for($r)['total_commits']) ?? 0);
    }

    /** Últimos CHIP_DAYS dias (datas 'Y-m-d' no fuso configurado). @return array<int, string> */
    public function chipDates(): array
    {
        $start = Carbon::now($this->timezone())->subDays(self::CHIP_DAYS - 1)->startOfDay();

        return array_map(fn (int $i) => $start->copy()->addDays($i)->format('Y-m-d'), range(0, self::CHIP_DAYS - 1));
    }

    /**
     * Ordena por "key_direction" (name/created/updated + asc/desc). "updated" =
     * último commit sincronizado, caindo p/ quando o repo foi adicionado.
     *
     * @return Collection<int, Repository>
     */
    public function sorted(string $sort): Collection
    {
        [$key, $direction] = array_pad(explode('_', $sort, 2), 2, 'desc');

        return $this->repositories->sortBy(fn (Repository $repository) => match ($key) {
            'name' => strtolower($repository->fullName()),
            'created' => $repository->created_at?->timestamp ?? 0,
            default => $this->for($repository)['last_committed_at']?->timestamp
                ?? $repository->created_at?->timestamp ?? 0,
        }, SORT_REGULAR, $direction === 'desc')->values();
    }

    /** Rampa de calor roxo→amarelo (mesma do heatmap). '#17131f' quando 0. */
    public static function heatColor(int $value, int $max): string
    {
        if ($value === 0 || $max === 0) {
            return '#17131f';
        }

        $stops = [[45, 27, 78], [126, 34, 206], [192, 38, 211], [236, 72, 153], [249, 115, 22], [250, 204, 21]];
        $t = sqrt($value / $max) * (count($stops) - 1);
        $i = min((int) floor($t), count($stops) - 2);
        $f = $t - $i;
        $ch = fn (int $k): int => (int) round($stops[$i][$k] + ($stops[$i + 1][$k] - $stops[$i][$k]) * $f);

        return sprintf('rgb(%d, %d, %d)', $ch(0), $ch(1), $ch(2));
    }

    // ── stats em lote ─────────────────────────────────────────────────────────

    /** @return array<int, array<string, mixed>> */
    private function statsByRepositoryId(): array
    {
        if ($this->stats !== null) {
            return $this->stats;
        }

        $ids = $this->repositories->pluck('id')->all();
        if ($ids === []) {
            return $this->stats = [];
        }

        // Totais por repo (count/sum/max) numa query só.
        $totals = Commit::query()
            ->selectRaw('repository_id, count(*) as commits, coalesce(sum(additions),0) as additions, coalesce(sum(deletions),0) as deletions, max(committed_at) as last_committed_at')
            ->whereIn('repository_id', $ids)
            ->groupBy('repository_id')
            ->get()
            ->keyBy('repository_id');

        $daily = $this->dailyCounts($ids);
        $ci = $this->latestCiConclusions($ids);
        $chipDates = $this->chipDates();

        $out = [];
        foreach ($this->repositories as $repository) {
            $row = $totals->get($repository->id);
            $repoDaily = $daily[$repository->id] ?? [];
            $counts = array_map(fn (string $date): int => $repoDaily[$date] ?? 0, $chipDates);

            $out[$repository->id] = [
                'total_commits' => (int) ($row->commits ?? 0),
                'total_additions' => (int) ($row->additions ?? 0),
                'total_deletions' => (int) ($row->deletions ?? 0),
                'daily_counts' => $counts,
                'max_daily' => $counts === [] ? 0 : max($counts),
                'ci_conclusion' => $ci[$repository->id] ?? null,
                'last_committed_at' => ($row && $row->last_committed_at) ? Carbon::parse($row->last_committed_at) : null,
            ];
        }

        return $this->stats = $out;
    }

    /**
     * @param  array<int, int>  $ids
     * @return array<int, array<string, int>> [repo_id][data'Y-m-d'] = contagem
     */
    private function dailyCounts(array $ids): array
    {
        $tz = $this->timezone();
        $since = Carbon::now($tz)->subDays(self::CHIP_DAYS - 1)->startOfDay()->utc();

        $rows = Commit::query()
            ->whereIn('repository_id', $ids)
            ->where('committed_at', '>=', $since)
            ->get(['repository_id', 'committed_at']);

        $out = [];
        foreach ($rows as $row) {
            $date = $row->committed_at->copy()->setTimezone($tz)->format('Y-m-d');
            $out[$row->repository_id][$date] = ($out[$row->repository_id][$date] ?? 0) + 1;
        }

        return $out;
    }

    /**
     * @param  array<int, int>  $ids
     * @return array<int, ?string> [repo_id] = conclusion do run mais recente
     */
    private function latestCiConclusions(array $ids): array
    {
        $rows = WorkflowRun::query()
            ->whereIn('repository_id', $ids)
            ->whereNotNull('run_started_at')
            ->orderBy('run_started_at')
            ->get(['repository_id', 'conclusion']);

        $out = [];
        foreach ($rows as $row) {
            $out[$row->repository_id] = $row->conclusion; // asc → o último sobrescreve = mais recente
        }

        return $out;
    }

    /** @return array<string, mixed> */
    private function emptyStats(): array
    {
        return [
            'total_commits' => 0, 'total_additions' => 0, 'total_deletions' => 0,
            'daily_counts' => array_fill(0, self::CHIP_DAYS, 0), 'max_daily' => 0,
            'ci_conclusion' => null, 'last_committed_at' => null,
        ];
    }

    private function timezone(): string
    {
        return config('services.github.timezone') ?: config('app.timezone', 'UTC');
    }
}
