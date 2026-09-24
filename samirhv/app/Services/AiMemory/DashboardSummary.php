<?php

namespace App\Services\AiMemory;

use App\Models\AiMemoryStatSnapshot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Derived numbers for the dashboard.
 *
 * Nothing here touches a database: it receives what the repositories (and the
 * durable snapshot table) already brought and returns what the view needs to
 * draw — series summaries, the change over N days, the axis scale and the SVG
 * paths of the charts. It lives in a service because it is the only real logic
 * on that screen, which makes it testable without rendering the view.
 */
class DashboardSummary
{
    /**
     * Summary of a daily series [Y-m-d => total]: period total, average, peak
     * (value + day) and today's value. `top` is the ceiling of the Y axis.
     *
     * @param  array<string,int>  $byDay
     * @return array{total:int,avg:int,max:int,top:int,peak_day:?string,today:int,days:int}
     */
    public function series(array $byDay): array
    {
        $values = array_values($byDay);
        $count = count($values);
        $total = array_sum($values);
        $max = $values ? max($values) : 0;

        $peakDay = null;
        foreach ($byDay as $day => $value) {
            if ($value === $max) {
                $peakDay = $day;   // on a tie, the most recent peak
            }
        }

        return [
            'total' => $total,
            'avg' => $count > 0 ? (int) round($total / $count) : 0,
            'max' => $max,
            'top' => $this->niceMax($max),
            'peak_day' => $max > 0 ? $peakDay : null,
            'today' => $count > 0 ? (int) end($values) : 0,
            'days' => $count,
        ];
    }

    /**
     * Change in a total between the oldest snapshot inside the window and the
     * live number right now. It also returns how many days that interval really
     * spans (snapshots are daily, but one can be missing) — the UI shows the
     * real figure, never a "7 days" that did not happen. Null when there is no
     * usable snapshot.
     *
     * @param  Collection<int,AiMemoryStatSnapshot>  $history
     * @return array{value:int,days:int}|null
     */
    public function delta(Collection $history, string $metric, int $live, int $days = 7): ?array
    {
        if ($history->isEmpty()) {
            return null;
        }

        $limit = Carbon::today()->subDays($days);
        $reference = $history->last(fn ($snap) => $snap->captured_on->lessThanOrEqualTo($limit))
            ?? $history->first();

        $elapsed = (int) round(abs($reference->captured_on->diffInDays(Carbon::today())));
        if ($elapsed < 1) {
            return null;   // only today's snapshot exists: nothing to compare against yet
        }

        return ['value' => $live - (int) $reference->{$metric}, 'days' => $elapsed];
    }

    /**
     * A "round" ceiling for the Y axis (half-order-of-magnitude steps), so the
     * top of the chart is not an odd number: 78 → 80, 959 → 1000, 65,453 → 70,000.
     */
    public function niceMax(int $max): int
    {
        if ($max <= 5) {
            return max($max, 1);
        }

        $step = max(1, (10 ** (int) floor(log10($max))) / 2);

        return (int) (ceil($max / $step) * $step);
    }

    /**
     * Sparkline: scaled min→max (the SHAPE of the curve is what matters, not the
     * zero) with a little vertical padding so the line never touches the edges.
     *
     * @param  array<int,int>  $values
     * @return array{line:string,last_y:float,height:float}
     */
    public function sparkline(array $values, float $width = 100, float $height = 30, float $pad = 3): array
    {
        return $this->path($values, $width, $height, null, $pad, false);
    }

    /**
     * History area: baseline at zero (it is a running total — the scale has to
     * be honest) and an explicit ceiling, the same one the axis shows.
     *
     * @param  array<int,int>  $values
     * @return array{line:string,area:string,last_y:float,height:float}
     */
    public function areaPath(array $values, int $top, float $width = 1000, float $height = 220): array
    {
        return $this->path($values, $width, $height, $top, 0, true);
    }

    /** Snapshot series per metric: ['observations' => [1, 2, …], …]. */
    public function historySeries(Collection $history, array $metrics): array
    {
        $out = [];
        foreach ($metrics as $metric) {
            $out[$metric] = $history->map(fn ($snap) => (int) $snap->{$metric})->values()->all();
        }

        return $out;
    }

    /**
     * Build the SVG path of a series. Coordinates live in the viewBox space —
     * the SVG is drawn with `preserveAspectRatio="none"` and a
     * `non-scaling-stroke`, so it follows the width of the card without
     * distorting the line.
     *
     * @param  array<int,int>  $values
     * @return array{line:string,area:string,last_y:float,height:float}
     */
    private function path(array $values, float $width, float $height, ?int $forceMax, float $pad, bool $zeroBase): array
    {
        $values = array_values($values);
        $count = count($values);

        if ($count === 0) {
            return ['line' => '', 'area' => '', 'last_y' => $height, 'height' => $height];
        }
        if ($count === 1) {
            $values = [$values[0], $values[0]];
            $count = 2;
        }

        $max = $forceMax ?? max($values);
        $min = $zeroBase ? 0 : min($values);
        $span = max($max - $min, 1);
        $inner = $height - 2 * $pad;
        $step = $width / ($count - 1);

        $points = [];
        $lastY = $height;
        foreach ($values as $i => $value) {
            $x = round($i * $step, 2);
            $y = round($pad + $inner - (($value - $min) / $span) * $inner, 2);
            $points[] = "{$x},{$y}";
            $lastY = $y;
        }

        $line = 'M'.implode(' L', $points);

        return [
            'line' => $line,
            'area' => $line." L{$width},{$height} L0,{$height} Z",
            'last_y' => $lastY,
            'height' => $height,
        ];
    }
}
