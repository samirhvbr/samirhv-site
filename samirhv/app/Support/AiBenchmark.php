<?php

namespace App\Support;

use JsonException;

/**
 * The published results of LEB, the LLM Engineering Benchmark.
 *
 * `resources/data/ai-benchmark/results.json` is a byte-identical copy of
 * `results/results.json` in samirhvbr/ai-benchmark, where
 * `tools/export-results.py` builds it from the scorecards. It arrives here
 * through `tools/sync-ai-benchmark-results.sh`. Never edit it by hand: a number
 * changed here would disagree with the scorecard that audits it.
 *
 * Read once per request. A missing or unreadable file degrades to "no results
 * yet" rather than a 500: the page still explains the benchmark, and
 * AiBenchmarkPageTest is what fails loudly when the file is broken.
 */
final class AiBenchmark
{
    public const PATH = 'data/ai-benchmark/results.json';

    /** Columns in the flaw-by-flaw table: the top N entries, in rank order. */
    public const FLAW_TABLE_AGENTS = 10;

    /** Entries in the summary next to the page title. */
    public const HERO_TOP = 6;

    private static ?array $cache = null;

    /**
     * @return array{instances: list<array<string, mixed>>}
     */
    public static function results(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $path = resource_path(self::PATH);
        $data = ['instances' => []];

        if (is_file($path)) {
            try {
                $decoded = json_decode((string) file_get_contents($path), true, 64, JSON_THROW_ON_ERROR);
                if (is_array($decoded) && isset($decoded['instances']) && is_array($decoded['instances'])) {
                    $data = $decoded;
                }
            } catch (JsonException $e) {
                report($e);
            }
        }

        return self::$cache = $data;
    }

    /** Forget the per-request copy (tests swap the file between cases). */
    public static function flush(): void
    {
        self::$cache = null;
    }

    /**
     * Serve these results instead of the file until the next flush(). Tests
     * only: a shape the real file does not have yet (an eleventh agent, a
     * second run) is exercised without touching the synced copy.
     *
     * @param  array{instances: list<array<string, mixed>>}  $data
     */
    public static function fake(array $data): void
    {
        self::$cache = $data;
    }

    /**
     * The run an entry's score belongs to. Its grade, categories and flaws are
     * what the page shows next to that score (ai-benchmark PROTOCOL §4). A file
     * older than `representative_run` falls back to the best run, as before.
     *
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    public static function representativeRun(array $entry): array
    {
        $runs = collect($entry['runs']);

        return $runs->firstWhere('run', $entry['representative_run'] ?? null)
            ?? $runs->sortByDesc('total')->first();
    }

    /**
     * Width of a score bar, 0–100, never NaN.
     */
    public static function percent(int|float $score, int|float $max): int
    {
        if ($max <= 0) {
            return 0;
        }

        return (int) round(max(0, min(1, $score / $max)) * 100);
    }
}
