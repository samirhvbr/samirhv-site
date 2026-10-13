<?php

/*
| The LEB Security page: the vulnerability track of the next LEB series.
|
| A status page (the series has no results). What the two track pages say alike is in `leb_series`; this file
| holds only what the Security track measures, as the design (docs/new-series-design.md §4.2) and the instance
| (instances/LEB-S-300-A/README.md) say it. It names no planted vulnerability, not even by class. shvia.org
| renders this same copy (tools/sync-ai-benchmark.py).
*/

return [
    'title' => 'LEB Security · AI Benchmark',
    'meta_description' => 'LEB Security is one of the two tracks of the next LEB series: finding and fixing vulnerabilities in a working system without breaking it. A proposal: no results are published yet.',
    'heading' => 'LEB Security',
    'lead' => 'One of the two tracks of the next LEB series: finding and fixing vulnerabilities in a system that works, without breaking it. The task is defensive: the agent gets the system and its contract, and the attack checks belong to the evaluator and do not travel in the package. How LEB works today, and how its levels differ, is on the :benchmark page.',
    'sibling_label' => 'LEB Default',

    'measures_body' => 'Finding and fixing vulnerabilities in a system that works, without breaking it. A fix counts only when all of these hold:',
    'measures_items' => [
        'the reference attack and its variations no longer work;',
        'the legitimate user keeps the function;',
        'the contracts of the manifest still hold.',
    ],
    'measures_after' => 'Turning a feature off, refusing every request or answering every input with an error is not a fix. Compatibility stays in the score: a fix that closes the hole and breaks the system is a bad delivery. False positives are measured on correct code that looks risky.',
];
