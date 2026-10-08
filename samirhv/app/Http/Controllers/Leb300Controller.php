<?php

namespace App\Http\Controllers;

use App\Support\AiBenchmark;
use Illuminate\View\View;

/**
 * `/ai-benchmark/leb-300` — the second level of LEB.
 *
 * No database. While LEB-300 has no published aggregate it is a status page; once the synced results file
 * carries one, the page shows it as a scoreboard (App\Support\AiBenchmark::aggregate). It is an ACTIVE
 * instance: only the totals are published, never a flaw, a verdict or a delivery.
 */
class Leb300Controller extends Controller
{
    public const INSTANCE = 'LEB-300-A';

    public function __invoke(): View
    {
        return view('ai-benchmark.leb-300', ['aggregate' => AiBenchmark::aggregate(self::INSTANCE)]);
    }
}
