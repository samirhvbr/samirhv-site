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
    'meta_description' => 'O LEB-100, o primeiro nível do benchmark de engenharia LEB: uma aplicação PHP legada de cerca de 300 linhas, resolvida por todos os agentes a partir do mesmo pacote, com todos os runs publicados.',

    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'LEB-100',
    'heading_accent' => 'a instância de referência',
    'lead' => 'O primeiro nível do LEB: uma aplicação PHP legada de cerca de 300 linhas, resolvida por todos os agentes abaixo a partir do mesmo pacote. Como um run funciona, como é pontuado e no que este nível difere do LEB-300 está na página :benchmark.',
    'benchmark_link' => 'Benchmark',
];
