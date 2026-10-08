<?php

/*
| The LEB-100 page: the results of the first level.
|
| The explanation of LEB (why, how a run works, how it is scored, where the agents run) is on the
| Benchmark page, lang/{en,pt_BR}/ai_benchmark.php; this page keeps what belongs to one instance: its results,
| the caveats on quoting them and the way to audit them. The numbers are never written here: they come
| from the synced results file (App\Support\AiBenchmark). shvia.org renders this same copy
| (tools/sync-ai-benchmark.py reads this file), so a change here reaches both sites.
*/

return [
    'title' => 'LEB-100 · AI Benchmark',
    'meta_description' => 'LEB-100, the first level of the LEB engineering benchmark: one legacy PHP application of about 300 lines, solved by every agent from the same package, with every run published.',

    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'LEB-100',
    'heading_accent' => 'the reference instance',
    'lead' => 'The first level of LEB: a legacy PHP application of about 300 lines, solved by every agent below from the same package. How a run works, how it is scored and how this level differs from LEB-300 is on the :benchmark page.',
    'benchmark_link' => 'Benchmark',
];
