<?php

/*
| The pages of the next LEB series: LEB Default and LEB Security (one page per track).
|
| The series is a proposal (docs/new-series-design.md of samirhvbr/ai-benchmark, status PROPOSED), so both pages
| are status pages; the Default one also shows the first case once the synced results carry its aggregate. This file holds what the two say alike: the status, the systems,
| how a case runs, what will be published, the answer key, the comparison and the links. `leb_default` and
| `leb_security` hold what each track measures. Everything written here is what the public repository says
| (docs/new-series-design.md, systems/, instances/LEB-D-300-A/ and instances/LEB-S-300-A/), and nothing more:
| no number the series has not published, no date it has not set.
|
| The answer key of every instance, this one and every future one, is published on GitHub with the instance,
| before its first run, and the bench machines cannot reach GitHub. No page of the series calls a key private,
| secret or hidden, or says it will be revealed later. The Security page names no planted vulnerability, not
| even by class.
|
| shvia.org renders this same copy (tools/sync-ai-benchmark.py reads this file), so a change here reaches
| both sites.
*/

return [
    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB · next series',
    'heading_accent' => 'proposed',
    // The link to the other track, above the title.
    'sibling_note' => 'This is one of the two tracks of the next series. The other is :link.',
    'benchmark_link' => 'Benchmark',
    'cta_design' => 'Read the design',

    'note_title' => 'There are no results yet.',
    'note_body' => 'This page says where the series stands and what will appear here. Nothing on it is a score.',

    'measures_title' => 'What this track measures',
    'sides_note' => 'The Default and Security scores of a configuration are shown side by side and never added.',

    'state_title' => 'Where the series stands',
    'state_body' => 'The next series is a proposal: its design was decided on 12 October 2026, and its normative texts (the spec, the protocol, the scoring and the levels) are written after a pilot has validated the process. It changes nothing in LEB-100 or LEB-300, whose results stay as published. There is no scoreboard yet.',

    'systems_title' => 'The reference systems',
    'systems_intro' => 'Each track is measured on several reference systems, and each system yields both tracks: two planting profiles of the same clean code. The same letter is the same system. The pilot will have 3 systems; the evaluation set, about 6.',
    'systems_col_system' => 'System',
    'systems_col_domain' => 'Domain',
    'systems_col_stack' => 'Stack',
    'systems_col_instance' => 'Instance',
    'systems_col_status' => 'Status',
    'system_a_name' => 'A',
    'system_a_domain' => 'Condominium management (CondoDesk)',
    'system_a_stack' => 'Java 21 · Maven · PostgreSQL',
    'system_a_status' => 'In authoring',

    'run_title' => 'How a case runs',
    'run_intro' => 'A case is one system, one track and one agent configuration.',
    'run_items' => [
        '5 runs per case, and all of them count.',
        'The requested model is pinned in the client, in the main conversation and in every subagent, so that no substitute can answer.',
        'An automated lab runs each case, from the restoration of the machine to the filing of the evidence.',
    ],

    'publish_title' => 'What will be published',
    'publish_intro' => 'For each configuration and track:',
    'publish_items' => [
        'the quality of the evaluable deliveries, with its uncertainty interval;',
        'the completion rate with the requested model;',
        'the expected score per scheduled attempt;',
        'blocks (at the start and during the work), model switches, exhausted budgets and lab failures, each counted separately.',
    ],

    'key_title' => 'The answer key',
    'key_body' => 'The answer key of an instance is published on GitHub with the instance, before its first run, and the machines that run the agents cannot reach GitHub. Each run also records the model\'s training cutoff, for what a model may have seen in training.',

    'compare_title' => 'Comparison',
    'compare_body' => 'A score of the next series is never set beside one of LEB-100 or LEB-300.',

    'links_title' => 'Read more',
    'link_design' => 'The design of the series',
    'link_system' => 'System A',
    'link_instance' => 'The instance folder',

    // Once the synced results carry the aggregate of a track's instance (the Default track since 15 October 2026), its page says so and shows
    // the first case. These replace the keys of the same name without `_results`; the Security page keeps the status copy above.
    'note_title_results' => 'The first results are in.',
    'note_body_results' => 'One configuration has run on one system. This is the first measurement of the pilot, not a scoreboard.',
    'state_body_results' => 'The next series is a proposal: its design was decided on 12 October 2026, and its normative texts (the spec, the protocol, the scoring and the levels) are written after a pilot has validated the process. The pilot has begun on system A, and its first case is above. It changes nothing in LEB-100 or LEB-300, whose results stay as published. There is no scoreboard yet: it needs more configurations and more systems.',
    'system_a_status_results' => 'First case run',

    'results_title' => 'The first case',
    'results_intro' => 'Each configuration ran :runs scheduled runs on system A, :minutes minutes at most each. While an instance is active only its aggregate is published: no per-flaw result, no verdict, no delivery.',
    'results_case_ids' => ':instance v:version · :track track · :runs runs',
    'results_col_estimate' => 'Estimate',
    'results_col_interval' => '95% interval',
    'results_col_grade' => 'Grade',
    'results_col_runs' => 'Score of each run',
    'results_completion' => ':completed of :scheduled scheduled attempts completed with the requested model. Systems on which every attempt completed: :all of :systems.',
    'results_expected' => 'Expected score per scheduled attempt: :mean (:low to :high).',
    'results_outcomes' => 'Blocked: :blocked (at the start :start, during the work :work). Budget exhausted: :budget. Switched: :switched. Lab failures rerun: :lab.',
    'results_cost' => 'Per run, on average: US$ :cost and :minutes minutes.',
    'results_method' => 'The estimate is the mean of the completed runs, out of 1000. The interval is a 95% percentile bootstrap over systems and runs; with one system it reflects only the variation among that system\'s runs. The category columns show the runs\' mean against the most the category can give.',
    'results_key' => 'Answer key (matrix) SHA-256 :matrix · package SHA-256 :package.',
];
