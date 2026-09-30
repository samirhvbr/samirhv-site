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
    'top_title' => 'Top :n so far',
    'top_note' => 'Score out of 1000, of :total agents. Not official until each has three runs.',
    'top_link' => 'Full leaderboard',

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
    'isolation_body' => 'Agents run on a dedicated Linux VM isolated from GitHub. Its names resolve to loopback, its address ranges (the prefixes it announces and the edge addresses it publishes) are blackhole routes, and the VM has no IPv6 connectivity. The address blocks came on 30 September 2026. The runs of 29 September had the name block only: no agent could reach GitHub by name, but a deliberate connection straight to one of its addresses was not blocked, and the session logs show none was attempted. Of the runs of 30 September, all but Kimi K3\'s had every layer; Kimi K3 ran on a clone of the VM whose address blocks are not recorded. What a model saw in training is a separate question, answered by the training cutoff each run records.',
    'term_title' => 'benchmark VM',
    'hosts_comment' => '/etc/hosts · IPv4 and IPv6 alike',
    'routes_comment' => 'ip route · GitHub\'s prefixes',
    'routes_more' => '+ 71 edge addresses (api.github.com/meta)',
    'hosts_same' => 'the same names',

    'results_title' => 'Results',
    'results_intro' => 'Every delivery in a table solved the same package, byte for byte — the same SHA-256 — so the numbers compare like for like.',
    'results_empty' => 'No results have been published yet.',
    'instance_title' => ':id · :name',
    'instances' => [
        'LEB-100-A' => [
            'name' => 'Support-ticket panel (NetX ISP)',
            'desc' => 'The support-ticket panel of an internet provider, written in 2013-style PHP: data-access functions and an index.php that routes, authorizes and builds the HTML. About 300 lines on PHP 8, mysqli and MySQL 8, with 13 planted flaws and 2 decoys.',
        ],
    ],
    'facts_mode' => 'mode :mode · :turns turns',
    'facts_edition' => 'edition :edition',
    'facts_evaluated' => 'evaluated :date',
    'facts_matrix' => 'matrix :sha',
    'effort' => 'effort :level',
    'effort_default' => 'default effort (not configurable)',

    'col_rank' => '#',
    'col_model' => 'Agent',
    'col_total' => 'Total',
    'out_of' => 'of 1000',
    'runs_label' => ':count of 3 runs',
    'unofficial' => 'not official',
    'exposure_after' => 'training cutoff after the answer key went public',
    'exposure_unknown' => 'training cutoff not published',
    'exposure_help' => 'The answer key has been public since :date. By the cutoff its provider publishes, or the lack of one, this model may have trained on it.',
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
    'flaws_top' => 'The table shows the top :shown of :total agents. Every agent\'s result, flaw by flaw, is in its scorecard.',
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
        // Read from the LEB-100-A scorecards of 2026-09-30, twenty agents — revisit when results.json changes.
        'LEB-100-A' => [
            '**Five agents fixed the formula injection in the CSV (SEC-008)** — Sonnet 5.5, GPT-5.6-sol, GPT-6-astra, Grok 4.7 and DeepSeek V4.1 Flash — with the fix the answer key expects; Sonnet 5.5 is the only agent with security at 250 of 250. GPT-5.6-sol and DeepSeek V4.1 Flash also turned the `-` of a ticket with no technician into `\'-`, a new bug (−15).',
            '**Architecture was the weakest category for everyone**: 50, 25 and 50 of 200 for the Claude models, 25 for GPT-6.1-sol and 0 for the other sixteen. Nobody split the dispatcher that does everything; four agents named it and declined to restructure it.',
            '**Nobody broke the contract mechanically.** All twenty stayed on mysqli, kept the 22 characterization checks green and reported no decoy. Judgement is what separated them: Sonnet 5.5, Fable 5.1, the two Grok models, GPT-5.6-terra, the two Kimi models, the four GLM models and DeepSeek V4.1 Flash kept compatibility at 100, while the other eight each changed a business value (−30).',
            '**GPT-6.1-sol and GPT-6-astra are the strongest GPT models here** (666 and 661, 4th and 5th), with the best explanations among the GPT models. They split on the hard calls: 6.1-sol migrated MD5 to `password_hash` and left the CSV injection alone; astra did the opposite.',
            '**Grok 4.7 is the strongest model here from outside Anthropic and OpenAI** (638, 6th, Silver): the fourth agent to fix the CSV injection, with an explanation at the level of the best GPT reports (42 of 50). It is also the only agent that used the web, to read the PHP manual page for fputcsv; the VM blocks GitHub, not the web. Grok 4.6 follows at 633 (7th), five points behind for about an eighth of the cost (US$ 0.31 against 2.43).',
            '**GLM-5.3 scores 629 (8th, Silver)**: 241 points above GLM-5.2, in the same client at the same effort. Its smaller variant GLM-5.3-Flash is five points behind (624) for a tenth of the cost (US$ 0.08 against 0.73); GLM-5.3-FlashX scores 597.',
            '**DeepSeek V4.1 Flash is the cheapest run here** (US$ 0.04) and ranks 9th (625). DeepSeek V4 Flash, run through another host, scores 612: it fixed eight flaws fully but lost 30 points for relabelling the fallback of formatarStatus. DeepSeek now routes the V4 Flash name to V4.1 on its own API, so which weights that host served is not certain.',
            '**Opus 5.5\'s second run scored 717, six points above its first** — the first measure here of how much a total moves between runs. The published score is the lower of the two, 711.',
            '**Eleven agents did not run at xhigh.** MiniMax-M3 and Kimi K2.7 Code have no effort setting and ran at their model\'s default; Kimi K3 is filed at its default; the two Grok models, the four GLM models and the two DeepSeek models ran at high, in opencode. The other nine ran at xhigh.',
            '**Kimi K3, MiniMax-M3, Kimi K2.7 Code and GLM-5.2 close the table** (528, 460, 415, and 388, the first result below 400): the only four agents that left the N+1 query in place. MiniMax-M3\'s report has the most wrong mechanisms (explanation 20 of 50); Kimi K2.7 Code is the worst calibrated (Brier 0.225), with two SQL injections reported at confidence 100 in functions that only take integers.',
            '**Places 6 to 16 sit within 41 points** (638 to 597), inside the noise of a single run. GPT-5.6-terra reported the fewest planted flaws and still ranks tenth, on compatibility and on what it did fix.',
            '**The GPT models are the best calibrated** (Brier 0.000–0.006): fewer findings, each stated with high confidence and each real.',
        ],
    ],

    'caveats_title' => 'Read this before quoting a number',
    'caveats' => [
        'single_run' => '**One run per agent.** An official LEB score is the median of three independent runs. These are single runs, and a second run can move a total by tens of points.',
        'multi_run' => '**Up to three runs per agent.** An official LEB score is the median of three independent runs. Until an agent has three, its score is the lower of its totals so far, the totals are listed next to it, and everything shown with the score comes from that same run.',
        'judge' => '**The judge is an AI.** Claude Opus 5.5 applied the published rubric to every delivery without knowing which model wrote it — each was anonymised — and the explanation was scored by a separate judge that saw neither the answer key nor the other scores.',
        'conflict' => '**The judge is also a contestant.** Claude Opus 5.5 is one of the agents evaluated, and three of the twenty are Claude models — the top three places. Anonymity limits that bias; it does not remove it, since a model can recognise its own style. Every verdict is published with its rationale, flaw by flaw, and the four verdicts changed in review say why; one of them adds 41 points to GPT-5.6-terra\'s total.',
        'void' => '**One run was voided.** Claude Fable 5.1\'s second run was finished by another model: after Fable\'s safeguards stopped one of its responses, its client handed the rest of the run to Claude Opus 4.8, which wrote the whole report. It is kept, unscored, and Fable 5.1 still shows its first run only.',
        'key_public' => '**The answer key is public.** The failure matrix of LEB-100-A has been in the public repository since 13 July 2026. The runs of 29 September had the VM\'s name block only, so no agent could fetch it by accident; the runs of 30 September also had GitHub\'s addresses blocked, and the session logs of every run that left one show no request to GitHub at all. What a model saw in training depends on how far its training data reaches: each run records the cutoff its provider publishes, and a model whose cutoff is later, or not published, is marked in the leaderboard.',
        'harness' => '**The benchmark\'s own tests were fixed.** Scoring these runs exposed two defects in the evaluation tooling: an SQL loader that split a statement on a semicolon inside a comment, and CSV checks that read a temporary file the contract never promised. Both were fixed before scoring, the same way for every agent, and are recorded in the repository.',
        'params' => '**Not every run parameter was recorded.** The exact model version and the temperature were not, and cost and model time only where the client kept them; GPT-5.5\'s, MiniMax-M3\'s and Kimi K3\'s runs left no session log at all. Each run marks what is missing instead of guessing.',
    ],

    'audit_title' => 'Audit it',
    'audit_body' => 'Every delivery, mechanical report, verdict and scorecard is in the repository, next to the specification that produced them.',
    'audit_results' => 'Results on GitHub',
    'audit_method' => 'Specification',
];
