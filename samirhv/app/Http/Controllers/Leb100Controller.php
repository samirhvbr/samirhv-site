<?php

namespace App\Http\Controllers;

use App\Support\AiBenchmark;
use Illuminate\View\View;

/**
 * `/ai-benchmark/leb-100` — the results of the first level of LEB.
 *
 * No database: the explanation of the benchmark is on `/ai-benchmark`, the numbers come from the
 * results file synced from samirhvbr/ai-benchmark (App\Support\AiBenchmark). LEB-100-A's answer key is
 * public, so every delivery, verdict and scorecard is published and the page shows them flaw by flaw.
 */
class Leb100Controller extends Controller
{
    public function __invoke(): View
    {
        return view('ai-benchmark.leb-100', [
            'instances' => AiBenchmark::results()['instances'],
        ]);
    }
}
