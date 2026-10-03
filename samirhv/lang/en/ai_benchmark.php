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
            'name' => 'Support-ticket panel of an internet provider',
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
        // Read from the LEB-100-A scorecards of 2026-09-30 and 2026-10-01, thirty-two agents — revisit when results.json changes.
        'LEB-100-A' => [
            '**Six agents fixed the formula injection in the CSV (SEC-008)** — Sonnet 5.5 in all three of its settings, GPT-6.1-sol, GPT-5.6-sol and Grok 4.7 — with the fix the answer key expects; Sonnet 5.5 is the only model with security at 250 of 250, in all three of its settings. GPT-5.6-sol also turned the `-` of a ticket with no technician into `\'-`, a new bug (−15).',
            '**Architecture was the weakest category for everyone**: 50 of 200 for Opus 5.5, 25 for Sonnet 5.5 at xhigh and for GPT-6.1-sol, 12 for Sonnet 5.5 at max, and 0 for the other twenty-eight. Nobody split the dispatcher that does everything; those three named it and declined to restructure it.',
            '**Only one published run broke the contract mechanically.** All thirty-two stayed on mysqli and reported no decoy, and thirty-one kept the 22 characterization checks green; DeepSeek V4 Flash publishes a run whose search throws. Judgement is what separated the rest: twenty-three kept compatibility at 100, while the other nine each changed at least one business value (−30 each), most often the SLA average, scoped to each client.',
            '**Sixteen scores are official: Claude Sonnet 5.5 at xhigh, 809 (runs of 825, 809 and 724), Claude Sonnet 5.5 at max, 807 (807, 773 and 820), Claude Opus 5.5, 717 (711, 717 and 805), GPT-6.1-sol, 661 (666, 653 and 661), GPT-6.1-sol at ultra, 616 (597, 656 and 616), Grok 4.7, 638 (638, 663 and 607), GPT-6-astra, 628 (661, 596 and 628), GPT-5.6-terra, 625 (625, 611 and 645), GLM-5.3 Prime, 628 (635, 628 and 541), DeepSeek V4.1 Flash, 612 (625, 612 and 597), GPT-5.6-sol, 612 (612, 612 and 608), GPT-5.6-luna, 601 (599, 601 and 624), GPT-5.5, 568 (601, 558 and 568), DeepSeek V4 Pro, 496 (604, 496 and 432), MiniMax-M3 with the thinking variant, 462 (462, 616 and 434), and Claude Haiku 4.5, 317 (317, 369 and 232)**, each the median of three runs. A single run can sit more than 100 points from the median: Opus\'s third scored 805, Sonnet\'s third 724, DeepSeek V4 Pro\'s first 604, and astra\'s first, 661, had placed it 5th, from judgement calls such as which flaws to leave unfixed. Claude Fable 5.1 has two runs, 781 and 764, and publishes the lower, 4th',
            '**Gemini 3.8 Flash swung 99 points between two runs at high** (687 and 588) and publishes the lower, 21st, Bronze. Its first run, 6th, fixed 8 of the 13 planted flaws and is the only one outside Claude to flatten the nested ifs (CLN-007); its second fixed 7, and searched the VM for the answer key, by name and by the hash quoted in the task. The key is not on the VM, and the search turned up nothing the run did not already have. Both runs kept MD5 and both secrets and left the CSV export serving every client. At medium effort the same model scored 550 (23rd) in 4.5 minutes: it fixed 6 flaws and claimed SQL injection in two functions that only take integers.',
            '**GPT-6.1-sol is the strongest GPT model here**, official at 661 (runs of 666, 653 and 661), 6th; GPT-6.1-sol pro follows at 654, 7th, from one run. GPT-6-astra, official at 628, is 10th. Its first run fixed the CSV injection and kept MD5; its official run did the opposite, migrating MD5 to `password_hash` and leaving the CSV injection alone; GPT-6.1-sol\'s official run fixed both',
            '**Eight hours of multi-agent work scored less than half an hour of one agent at the same effort.** Claude Sonnet 5.5 in Claude Code\'s multi-agent mode, at max effort, ran 7 workflows with 68 subagents for 8.1 hours and US$ 233, about 65 times the cost of its xhigh run, and scored 774 (3rd, Gold). The same model at the same max effort as a single agent took about half an hour a run and is official at 807 (runs of 807, 773 and 820), 2nd, 33 points higher; at xhigh it is official at 809. It fixed the same flaws, with better calibration and a report scored 45 of 50, but left the architecture untouched. Nine of its subagents fell back to an older Sonnet after a safety classifier stopped them; none of the delivery came from them.',
            '**GPT-6.1-sol at ultra is official 45 points below itself at xhigh** (616 against 661), the median of three runs spread over 59 points (597, 656 and 616). With three subagents, its first found 9 of the 13 planted flaws, kept MD5 and did not name the dispatcher; with two, its second fixed 10, the CSV injection and MD5 among them. Each took under half an hour, not the 8 hours of the multi-agent Sonnet.',
            '**Grok 4.7 is the strongest model here from outside Anthropic and OpenAI** (638, 8th, Silver, official over three runs of 638, 663 and 607), with an explanation at the level of the best GPT reports (42 of 50). In its first run it is also the only agent that used the web, to read the PHP manual page for fputcsv; the VM blocks GitHub, not the web. Grok 4.6 follows at 633 (9th), the lower of two runs (633 and 640), for about an eighth of the cost (US$ 0.31 against 2.43).',
            '**GLM-5.3 Prime is the strongest GLM model** (628, 11th, Silver), official over three runs through two hosts (635, 628 and 541): the first fixed all four probe-covered flaws, the CSV injection among them, for US$ 1.69; the second left the injection alone. GLM-5.3 publishes 621 (14th), the lower of its two runs (629 and 621), the second served by another host; GLM-5.3-Flash scores 624 for about a tenth of the cost, and GLM-5.3-FlashX 597',
            '**DeepSeek V4.1 Flash is the cheapest agent here** (US$ 0.01 to 0.04 a run; its last two took under two minutes each) and is official at 612 (runs of 625, 612 and 597), 18th. DeepSeek V4 Flash, run through two other hosts, publishes 282, the lower of its two runs (612 and 282), last: the first fixed eight flaws fully, while the second\'s SQL-injection fix throws on every search and its client CSV comes out on one line. DeepSeek now routes the V4 Flash name to V4.1 on its own API, so which weights those hosts served is not certain. DeepSeek V4 Pro, the larger model, is official at 496, the median of three runs through two hosts (604, 496 and 432), all below DeepSeek V4.1 Flash\'s official 612: the first rates SQL injection at confidence 100 in two functions that only take integers, and the other two left the N+1 query in place.',
            '**Twenty-one agents did not run at xhigh.** MiniMax-M3, Kimi K2.7 Code, Qwen3 Coder Next and Claude Haiku 4.5 have no effort setting and ran at their model\'s default; Kimi K3 is filed at its default; the two Grok models, the five GLM models, the three DeepSeek models and Gemini 3.8 Flash ran at high, in opencode, and Gemini 3.8 Flash also at medium; MiniMax-M3 also ran in opencode with its thinking variant; Sonnet 5.5 ran twice at max, once in multi-agent mode, and GPT-6.1-sol once at ultra. The rest ran at xhigh.',
            '**Kimi K3, Qwen3 Coder Next, DeepSeek V4 Pro, MiniMax-M3 (with the thinking variant and at its default), Kimi K2.7 Code, GPT-5.3-Codex, GLM-5.2, Claude Haiku 4.5 and DeepSeek V4 Flash close the table** (528, 507, 496, 462, 460, 415, 403, 388, 317 and 282; the last three are below the pass line). Claude Haiku 4.5 is official at 317 (runs of 317, 369 and 232); in the first, 2.5 minutes and US$ 0.21, its visibility fix hides a client\'s own tickets on the main page, comparing an integer with the string mysqli returns. All but Qwen3 Coder Next left the N+1 query in place. Qwen3 Coder Next has the weakest explanation (18 of 50) and the worst calibration (Brier 0.301): it reported three flaws that cannot exist at confidence 100, SQL injection in two functions that only take integers among them, as Kimi K2.7 Code did. It also ran no tests.',
            '**Places 8 to 20 sit within 41 points** (638 to 597), inside the noise of a single run. GPT-5.6-terra reported the fewest planted flaws and still ranks 12th, on compatibility and on what it did fix.',
            '**The GPT models are among the best calibrated** (Brier 0.015 or less): fewer findings, each stated with high confidence and nearly all real.',
        ],
    ],

    'readings' => [
        // Read from the LEB-100-A verdicts and scorecards of 2026-10-01 — revisit when results.json changes.
        'LEB-100-A' => [
            'title' => 'Finding is not fixing',
            'lead' => 'Side by side, the three Claude models find nearly the same flaws and fix different numbers of them. One total hides which of the two it is measuring.',
            'points' => [
                '**They find nearly the same.** In the run that counts for each, Sonnet 5.5 at xhigh reported 12 of the 13 planted flaws and Fable 5.1 and Opus 5.5 11; across their eight runs, each found 11 or 12',
                '**Sonnet 5.5 fixes more, in the run that counts.** Its official run fixed 11 of the 13, against 9 for Opus\'s official run and 10 for Fable\'s. Run by run it varies: Sonnet fixed 11, 11 and 8, Opus 9 in each of its three, Fable 9 and 10. Fable and Opus found and explained the formula injection in the CSV and left it in place, to protect the file\'s consumers; Sonnet fixed it in two of its three runs, prefixing only the cells that start a formula. Its 92-point lead over Opus\'s official 717 comes from clean code (+100), not from security (+17); its 45-point lead over Fable\'s 764 is security (+17) and architecture (+25)',
                '**More compute did not change the pattern.** The single-agent Claude runs took 16 to 27 minutes each, and the three at the same max effort scored 807, 773 and 820. Sonnet 5.5 in multi-agent mode took almost 8 hours and US$ 233, about 25 times as long and 65 times the cost of its single-agent run, and scored less: the same fixes and a deeper check of its own code, but the dispatcher that the single-agent run named never reached its report.',
                '**How to read it.** On this task the three Claude models see the same problems; what separates them is how far they go in fixing them within the contract. In the median, Sonnet went further; a single run can reverse it, as Sonnet\'s third did. One task, two or three runs per model: a pattern worth testing, not a verdict',
            ],
        ],
    ],

    'caveats_title' => 'Read this before quoting a number',
    'caveats' => [
        'single_run' => '**One run per agent.** An official LEB score is the median of three independent runs. These are single runs, and a second run can move a total by tens of points.',
        'multi_run' => '**Up to three runs per agent.** An official LEB score is the median of three independent runs. Until an agent has three, its score is the lower of its totals so far, the totals are listed next to it, and everything shown with the score comes from that same run.',
        'judge' => '**The judge is an AI.** Claude Opus 5.5 applied the published rubric to every delivery without knowing which model wrote it — each was anonymised — and the explanation was scored by a separate judge that saw neither the answer key nor the other scores.',
        'conflict' => '**The judge is also a contestant.** Claude Opus 5.5 is one of the agents evaluated, and six of the thirty-two are Claude models, Sonnet 5.5 three times: they hold the top five places and the second to last. Anonymity limits that bias; it does not remove it, since a model can recognise its own style. Every verdict is published with its rationale, flaw by flaw, and the four verdicts changed in review say why; one of them adds 41 points to GPT-5.6-terra\'s total.',
        'void' => '**Sixteen attempts were left unscored, each before any judge saw it.** Twice, Claude Fable 5.1\'s safeguards stopped one of its responses while it worked on the security flaws, and its client handed the rest of the run to Claude Opus 4.8, which wrote the whole report; a run must come from one model, so both are void, and Fable 5.1 shows its two complete runs. Four ended on the provider\'s side before the delivery was complete: GPT-5.6-sol pro when its gateway ran out of credit, and Gemini 3.8 Flash three times, on a gateway timeout and twice on a rate limit. Four were set up wrong: the first multi-agent Sonnet 5.5 run was stopped to give the machine more processors, the second found its client logged out and never reached the model, Claude Haiku 4.5\'s first attempt had its client mode changed mid-run, and a Gemini attempt was started outside the task\'s folder. One finished, but GPT-5.6-terra ran in a different client from its first run, and was repeated in the same one. Five finished after their agent already had its three runs, two of GPT-6.1-sol at xhigh and three at ultra; a fourth run would let the published score be chosen. All sixteen are kept in the repository.',
        'key_public' => '**The answer key is public.** The failure matrix of LEB-100-A has been in the public repository since 13 July 2026. The runs of 29 September had the VM\'s name block only, so no agent could fetch it by accident; the runs of 30 September also had GitHub\'s addresses blocked, and the session logs of every run that left one show no request to GitHub at all. What a model saw in training depends on how far its training data reaches: each run records the cutoff its provider publishes, and a model whose cutoff is later, or not published, is marked in the leaderboard.',
        'harness' => '**The benchmark\'s own tests were fixed.** Scoring these runs exposed two defects in the evaluation tooling: an SQL loader that split a statement on a semicolon inside a comment, and CSV checks that read a temporary file the contract never promised. Both were fixed before scoring, the same way for every agent, and are recorded in the repository.',
        'machine' => '**The machine changed on 30 September.** Until then the agents ran as the VM\'s administrator, with sudo, on a machine that kept earlier runs\' leftovers. Their session logs show no agent reading another\'s work; the opencode agents shared a leftover test database that held only the seed rows. From Qwen3 Coder Next on, runs are made as an unprivileged user on a machine cleaned before each run.',
        'params' => '**Not every run parameter was recorded.** The exact model version and the temperature were not, and cost and model time only where the client kept them; GPT-5.5\'s first run and MiniMax-M3\'s and Kimi K3\'s runs left no session log at all. Each run marks what is missing instead of guessing.',
    ],

    'audit_title' => 'Audit it',
    'audit_body' => 'Every delivery, mechanical report, verdict and scorecard is in the repository, next to the specification that produced them. The same data is here as two spreadsheets: one row per run, and one per run and planted flaw.',
    'audit_results' => 'Results on GitHub',
    'audit_method' => 'Specification',
    'download_runs' => 'Runs (CSV)',
    'download_flaws' => 'Flaw by flaw (CSV)',
    'download_dictionary' => 'What each column means',
];
