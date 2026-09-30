<?php

/*
| The AI Benchmark page: LEB explained, then its results.
|
| The method is summarised from samirhvbr/ai-benchmark (README, SPEC, SCORING,
| PROTOCOL). The numbers are never written here — they come from the synced
| results file (App\Support\AiBenchmark). What IS written here, by hand, is the
| reading of those numbers (`highlights`) and the caveats; both belong to one
| evaluation and must be revisited when the results file changes.
*/

return [
    'title' => 'AI Benchmark',
    'meta_description' => 'LEB, the LLM Engineering Benchmark: can an AI agent evolve a legacy system without breaking it? The method, and the results so far.',

    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'Can an AI maintain legacy code',
    'heading_accent' => 'without breaking it?',
    'lead' => 'LEB — the LLM Engineering Benchmark — hands an AI agent a legacy system in production, with flaws planted in it and consumers that depend on how it behaves today. It measures the work that dominates real engineering: finding the flaws, fixing them, keeping every contract intact, and explaining the decisions like a senior engineer would.',
    'cta_results' => 'See the results',
    'cta_source' => 'Method on GitHub',

    'why_title' => 'Why another benchmark',
    'why_body' => 'Most benchmarks measure code written from scratch, or one isolated issue solved. Neither is what most engineering is: evolving a system that other people already depend on. LEB scores security, architecture, bugs, performance, clean code, compatibility and the quality of the explanation — and it takes points away from the agent that rewrites everything, swaps technologies without need, or breaks a public contract.',
    'why_quote' => 'Rewriting from scratch is not engineering. It is running away.',

    'how_title' => 'How a run works',
    'step1_title' => 'A legacy system, with planted flaws',
    'step1_desc' => 'The agent receives the code, a manifest of its public surface — the contract — and a neutral task: report the problems, fix what should be fixed, keep compatibility, justify every decision. It is never told which flaws exist, how many, or where.',
    'step2_title' => 'The agent works alone',
    'step2_desc' => 'In mode A it gets tools and a budget of turns; in mode S, one prompt and one answer. It hands back the changed code, a technical report, and an index of its findings, each with a 0–100 confidence.',
    'step3_title' => 'Machines check the code',
    'step3_desc' => 'Characterization tests run on the legacy code and on the delivery: public behaviour that changed is a regression. Probes then attack each fixable flaw — the injection payload, the empty dataset, the query counter — and report whether it is still there.',
    'step4_title' => 'A judge checks the report',
    'step4_desc' => 'Each finding is matched against the Official Failure Matrix, a hidden answer key that also holds decoys: plausible flaws that do not exist, and cost points when reported. A second judge scores the explanation blind. A deterministic scorer turns it all into 0–1000.',

    'scoring_title' => '1000 points, and how they are lost',
    'scoring_intro' => 'Every instance is worth exactly 1000: the raw points of each category are normalised to its weight, so scores compare across instances of a level.',
    'comp_note' => 'Compatibility starts at 100 and only goes down. Migrating mysqli to PDO without need costs 20; changing a public signature costs 30, per function.',
    'penalties_note' => 'Global penalties come off the total: a new bug −15, each broken characterization test −20, a needless rewrite −25, each decoy reported −5.',
    'grades_title' => 'Grades',

    'isolation_title' => 'Runs happen where the answer key is out of reach',
    'isolation_body' => 'Agents run on a dedicated Linux VM where github.com and GitHub\'s content hosts resolve to loopback, so an agent under test cannot open, clone or download the benchmark repository during a run. The block is by name: it stops accidental and naive access, not a deliberate bypass, and it says nothing about what a model saw in training.',
    'hosts_comment' => 'benchmark VM · IPv4 and IPv6 alike',
    'hosts_same' => 'the same names',

    'results_title' => 'Results',
    'results_intro' => 'Every delivery in a table solved the same package, byte for byte — the same SHA-256 — so the numbers compare like for like.',
    'results_empty' => 'No results have been published yet.',
    'instance_title' => ':id · :name',
    'instances' => [
        'LEB-100-A' => [
            '**Only Sonnet 5.5 and GPT-5.6-sol fixed the formula injection in the CSV (SEC-008)**, with the fix the answer key expects; Sonnet 5.5 is the only agent with security at 250 of 250. GPT-5.6-sol also turned the `-` of a ticket with no technician into `\'-`, a new bug (−15). Three agents reported the flaw and kept the cells raw; two did not report it.',
            '**Architecture was the weakest category for everyone**: 50, 25 and 50 of 200 for the Claude models, 0 for all four GPT models. Nobody split the dispatcher that does everything; the three Claude models named it and declined to restructure it.',
            '**Nobody broke the contract mechanically.** All seven stayed on mysqli, kept the 22 characterization checks green and reported no decoy. Judgement is what separated them: Sonnet 5.5, Fable 5.1 and GPT-5.6-terra kept compatibility at 100, while the other four each changed a business value (−30).',
            '**Five agents migrated MD5 to `password_hash`** transparently at login; GPT-5.5 and GPT-5.6-luna left MD5 in place on purpose. Fable 5.1 kept the secrets in the config as literal fallbacks.',
            '**Places 4 to 7 sit within 26 points** (625 to 599), well inside the noise of a single run. GPT-5.6-terra reported the fewest planted flaws and still ranks fourth, on compatibility and on what it did fix.',
            '**The GPT models are the best calibrated** (Brier 0.000–0.006): fewer findings, each stated with high confidence and each real.',
        ],
    ],
    'facts_mode' => 'mode :mode · :turns turns',
    'facts_edition' => 'edition :edition',
    'facts_evaluated' => 'evaluated :date',
    'facts_matrix' => 'matrix :sha',
    'effort' => 'effort :level',

    'col_rank' => '#',
    'col_model' => 'Agent',
    'col_total' => 'Total',
    'out_of' => 'of 1000',
    'runs_label' => ':count of 3 runs',
    'unofficial' => 'not official',
    'scorecard' => 'Scorecard',
    'discovery' => 'Discovery',
    'discovery_help' => 'Difficulty-weighted share of the planted flaws found (0–100). Informative.',
    'brier' => 'Brier',
    'brier_help' => 'Calibration of the declared confidence, 0 = perfect. Informative.',
    'penalties' => 'Penalties',
    'none' => 'none',

    'categories' => [
        'SEC' => 'Security',
        'ARCH' => 'Architecture',
        'BUG' => 'Bugs',
        'PERF' => 'Performance',
        'CLN' => 'Clean code',
        'COMP' => 'Compatibility',
        'EXPL' => 'Explanation',
    ],
    'grades' => [
        'Platinum' => 'LEB Platinum',
        'Gold' => 'LEB Gold',
        'Silver' => 'LEB Silver',
        'Bronze' => 'LEB Bronze',
        'Reprovada' => 'Failed',
    ],
    'grade_help' => [
        'Platinum' => 'ready for critical legacy',
        'Gold' => 'solid engineering',
        'Silver' => 'useful with supervision',
        'Bronze' => 'needs a full review',
        'Reprovada' => 'a risk to the system',
    ],

    'flaws_title' => 'Flaw by flaw',
    'flaws_intro' => 'What each agent found and fixed among the planted flaws. The hard ones are flaws of absence — a missing authorization check, a session never regenerated, a file left open on the error path.',
    'legend_fixed' => 'fixed',
    'legend_found' => 'found, not fixed',
    'legend_missed' => 'missed',
    'col_flaw' => 'Flaw',
    'severity' => [
        'Crítica' => 'critical',
        'Alta' => 'high',
        'Média' => 'medium',
        'Baixa' => 'low',
    ],
    'difficulty' => [
        'Fácil' => 'easy',
        'Moderada' => 'moderate',
        'Difícil' => 'hard',
        'Especialista' => 'expert',
    ],
    'flaws' => [
        'SEC-001' => 'SQL injection in the search',
        'SEC-003' => 'Reflected XSS in the search',
        'SEC-008' => 'Formula injection in the CSV export',
        'SEC-013' => 'Session fixation at login',
        'SEC-014' => 'Unsalted MD5 passwords',
        'SEC-015' => 'Secrets hardcoded in the config',
        'SEC-017' => 'Any ticket readable by id (IDOR)',
        'BUG-001' => 'Division by zero in the SLA average',
        'BUG-004' => 'File handle leaked on the error path',
        'PERF-001' => 'One query per ticket for the technician (N+1)',
        'ARCH-002' => 'A dispatcher that does everything',
        'ARCH-009' => 'Magic numbers for status and priority',
        'CLN-007' => 'Four levels of nested ifs',
    ],

    'highlights_title' => 'What stood out',
    'highlights' => [
        // Read from the LEB-100-A scorecards of 2026-09-29 — revisit when results.json changes.
        'LEB-100-A' => [
            '**Nobody fixed the formula injection in the CSV (SEC-008).** Three agents reported it and chose to keep the cells raw for the export\'s consumers; GPT-5.5 did not report it.',
            '**Architecture was the weakest category for all four** — 25, 50, 0 and 0 of 200. Nobody split the dispatcher that does everything: Fable 5.1 and Opus 5.5 named it and declined to restructure it.',
            '**Nobody broke the contract mechanically.** All four stayed on mysqli, kept the 22 characterization checks green and reported no decoy. Judgement is what separated them: only Fable 5.1 kept compatibility at 100, while each of the other three changed a business value (−30).',
            '**Passwords and secrets split the field.** Fable 5.1 and Opus 5.5 migrated MD5 to `password_hash` transparently at login; both GPT models left MD5 in place on purpose. Fable 5.1, in turn, kept the secrets in the config as literal fallbacks.',
            '**GPT-5.5 and GPT-5.6-luna are two points apart, across the Silver/Bronze line** — well inside the noise of a single run.',
        ],
    ],

    'caveats_title' => 'Read this before quoting a number',
    'caveats' => [
        'single_run' => '**One run per agent.** An official LEB score is the median of three independent runs. These are single runs, and a second run can move a total by tens of points.',
        'judge' => '**The judge is an AI.** Claude Opus 5.5 applied the published rubric to every delivery without knowing which model wrote it — each was anonymised — and the explanation was scored by a separate judge that saw neither the answer key nor the other scores.',
        'conflict' => '**The judge is also a contestant.** Claude Opus 5.5 is one of the agents evaluated, and three of the seven are Claude models — the top three places. Anonymity limits that bias; it does not remove it, since a model can recognise its own style. Every verdict is published with its rationale, flaw by flaw, and the two verdicts changed in review say why; one of them lifts GPT-5.6-terra from 7th to 4th.',
        'key_public' => '**The answer key is public.** The failure matrix of LEB-100-A has been in the public repository since July 2026. The VM kept it out of reach during the runs, but it may have reached training data: LEB-100-A should be retired for new runs.',
        'harness' => '**The benchmark\'s own tests were fixed.** Scoring these runs exposed two defects in the evaluation tooling: an SQL loader that split a statement on a semicolon inside a comment, and CSV checks that read a temporary file the contract never promised. Both were fixed before scoring, the same way for every agent, and are recorded in the repository.',
        'params' => '**Some run parameters were not recorded:** the exact model version, the temperature, token counts and cost, and the full logs. Each run marks them as not recorded rather than guessing.',
    ],

    'audit_title' => 'Audit it',
    'audit_body' => 'Every delivery, mechanical report, verdict and scorecard is in the repository, next to the specification that produced them.',
    'audit_results' => 'Results on GitHub',
    'audit_method' => 'Specification',
];
