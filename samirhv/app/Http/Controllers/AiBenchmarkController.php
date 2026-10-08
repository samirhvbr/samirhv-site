<?php

namespace App\Http\Controllers;

use App\Support\AiBenchmark;
use Illuminate\View\View;

/**
 * `/ai-benchmark` — what LEB is: why it exists, how a run works, how it is scored, how the two levels differ.
 *
 * The first entry of the menu, and the way into the pages of the two instances. No database: the
 * explanation is copy in lang/, and the few numbers it shows (how many agents, the top score) come from the
 * results file synced from samirhvbr/ai-benchmark (App\Support\AiBenchmark). The results themselves are on
 * the pages of the instances (Leb100Controller, Leb300Controller).
 */
class AiBenchmarkController extends Controller
{
    public const LEB100 = 'LEB-100-A';

    public const LEB300 = 'LEB-300-A';

    public function __invoke(): View
    {
        $results = AiBenchmark::results();
        $leb100 = collect($results['instances'])->firstWhere('id', self::LEB100);

        return view('ai-benchmark.index', [
            'leb100' => $leb100,
            'aggregate' => AiBenchmark::aggregate(self::LEB300),
        ]);
    }
}
