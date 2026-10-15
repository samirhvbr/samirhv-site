<?php

/*
| The LEB Default page: the everyday-maintenance track of the next LEB series.
|
| A status page; once the synced results carry the track's aggregate it also shows the first case. What the two track pages say alike is in `leb_series`; this file
| holds only what the Default track measures, as the design (docs/new-series-design.md §4.1) and the instance
| (instances/LEB-D-300-A/README.md) say it. shvia.org renders this same copy (tools/sync-ai-benchmark.py).
*/

return [
    'title' => 'LEB Default · AI Benchmark',
    'meta_description' => 'LEB Default is one of the two tracks of the next LEB series: the everyday maintenance of a working system, with no planted vulnerability. A proposal: no results are published yet.',
    'heading' => 'LEB Default',
    'lead' => 'One of the two tracks of the next LEB series: the everyday maintenance of a working system. The agent works on a system with maintenance flaws planted in it, and nothing in the task asks for security work. How LEB works today, and how its levels differ, is on the :benchmark page.',
    'sibling_label' => 'LEB Security',

    'measures_body' => 'Architecture, bugs, performance, code quality and compatibility. No vulnerability is planted, and the task asks for no security work.',
    'measures_base' => 'The code it starts from is described as "no known vulnerabilities within the reviewed scope", never as free of them.',

    'meta_description_result' => 'LEB Default, a track of the next LEB series: the everyday maintenance of a working system, with no planted vulnerability. A proposal in its pilot; the first case is published as an aggregate.',
];
