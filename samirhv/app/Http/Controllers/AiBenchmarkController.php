<?php

namespace App\Http\Controllers;

use App\Support\AiBenchmark;
use Illuminate\View\View;

/**
 * `/ai-benchmark` — what LEB measures, how, and the results so far.
 *
 * No database: the explanation is copy in lang/, the numbers come from the
 * results file synced from samirhvbr/ai-benchmark (App\Support\AiBenchmark).
 */
class AiBenchmarkController extends Controller
{
    public function __invoke(): View
    {
        return view('ai-benchmark.index', [
            'instances' => AiBenchmark::results()['instances'],
        ]);
    }
}
