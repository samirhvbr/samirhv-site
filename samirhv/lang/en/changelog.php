<?php

/*
| What changed in each application, per version.
|
| CURATED, not mirrored. Each entry is written from the application's own
| changelog — CHANGELOG.md for ai-usagebar, ShvIA and SShvTerm, VERSION.md for
| the GitHub Desktop fork — and rewritten for someone deciding whether to
| download it, not for someone maintaining it. An entry that only means
| something inside the repository does not belong on a product page.
|
| Checked against every repository on 2026-09-05. The date on an entry is the
| date of the release, not of the check.
|
| SShvTerm has no entry: it is a link-only project with no page here, so there
| is nowhere to render one. Its changelog lives on its own site.
|
| To update: read the app's changelog, add the new version at the TOP of its
| list, and translate the entry in lang/pt_BR/changelog.php. Key sets are
| asserted identical by AppChangelogTest.
*/

return [
    'heading' => 'What changed',
    'lead' => 'The most recent releases, taken from the project\'s own changelog.',
    'current' => 'current',
    'source' => 'Full changelog',
    'empty' => 'No release notes published yet.',

    'apps' => [
        'ai-usagebar' => [
            [
                'version' => '0.16.0',
                'date' => '2026-07-22',
                'notes' => [
                    'Google Antigravity joins the providers, reporting all four real quota windows — a 5-hour and a weekly limit for each of its two model pools — with no credentials to configure.',
                    'The GNOME extension supports Shell 45 through 50, up from 45–48.',
                    'Tooltip borders no longer come out ragged when a label contains &, < or >.',
                ],
            ],
            [
                'version' => '0.15.0',
                'date' => '2026-07-22',
                'notes' => [
                    'The context monitor docks into the dashboard, with a key to cycle full, split and bottom.',
                    'Credit spend is no longer hidden on uncapped plans, and extra usage prints in its own currency instead of a hard-coded dollar sign.',
                ],
            ],
            [
                'version' => '0.14.0',
                'date' => '2026-07-20',
                'notes' => [
                    'An opt-in local monitor for Claude Code context, off until you enable it and reading only bounded tails — no filesystem scan.',
                    'Four account-balance providers: Kilo, Novita, Moonshot and xAI/Grok. These show money rather than a usage percentage.',
                ],
            ],
            [
                'version' => '0.13.0',
                'date' => '2026-07-17',
                'notes' => [
                    'Kimi joins the providers.',
                    'Pace markers — whether you are ahead of or behind burn pace — reach the macOS menu bar and GNOME.',
                    'Fixes a bar stuck at "Sonnet only 0%", clipped rows in the macOS preferences, and API key names leaking into error text.',
                ],
            ],
            [
                'version' => '0.12.0',
                'date' => '2026-07-08',
                'notes' => [
                    'Model-scoped weekly limits are rendered on the desktop surfaces.',
                ],
            ],
        ],

        'github-desktop' => [
            [
                'version' => '0.4.1',
                'date' => '2026-08-03',
                'notes' => [
                    'Packaging also produces the Arch package (.pkg.tar.zst), converting the .deb on the build host, with dependencies mapped to Arch names.',
                    'Which Linux formats to build is now selectable, so a container can build only the .rpm.',
                ],
            ],
            [
                'version' => '0.4.0',
                'date' => '2026-07-06',
                'notes' => [
                    'Batch pull and push over the repositories you tick, three at a time, with a result on each row instead of one verdict at the end.',
                    'A status report screen — how many repositories are waiting to commit, behind, ahead or up to date — with the whole thing copyable as text.',
                    'Every installer now carries both version numbers in its filename, so a .deb and a .dmg from the same build are recognisable as such.',
                ],
            ],
            [
                'version' => '0.3.0',
                'date' => '2026-06-30',
                'notes' => [
                    'Select-all in the batch clone, with a third state for a partial selection, and honouring the search filter.',
                    'A repository that already exists in the target folder is skipped with a banner, instead of an error popup that stopped the whole batch.',
                ],
            ],
            [
                'version' => '0.2.0',
                'date' => '2026-06-27',
                'notes' => [
                    'Selective clone: list any user\'s public repositories and clone several of them into one folder in a single action.',
                ],
            ],
            [
                'version' => '0.1.0',
                'date' => '2026-06-24',
                'notes' => [
                    'The multi-repository panel — every repository on one screen, flagging the ones with something pending.',
                ],
            ],
        ],

        'shvia' => [
            [
                'version' => '2.110.183',
                'date' => '2026-09-05',
                'notes' => [
                    'The work queue becomes an enumerated list; re-measuring it against the code closed six items that were already done.',
                ],
            ],
            [
                'version' => '2.110.181',
                'date' => '2026-09-05',
                'notes' => [
                    'The command centre closes the gap to the approved design — minus the dials that had no data source behind them, which were left out rather than faked.',
                ],
            ],
            [
                'version' => '2.110.178',
                'date' => '2026-09-05',
                'notes' => [
                    'Switching infrastructure keeps working when the model chosen is a free one.',
                ],
            ],
            [
                'version' => '2.110.177',
                'date' => '2026-09-05',
                'notes' => [
                    'The panel screen becomes your choice: the classic layout by default, the command centre opt-in.',
                ],
            ],
            [
                'version' => '2.110.176',
                'date' => '2026-09-04',
                'notes' => [
                    'The radar preset: the console look as an eleventh theme.',
                ],
            ],
        ],
    ],

    /* Facts that belong beside the list rather than inside an entry. */
    'notes' => [
        'ai-usagebar' => 'Later work in this fork — the ShvIA and MiniMax providers, and the API status panel — has shipped but has not been given a version of its own yet.',
        'shvia' => 'ShvIA is three products with three version lines: the web platform above, the desktop app, and the public site. The patch number climbs fast by design — it increments on every new screen, migration or visible change.',
        'github-desktop' => 'The fork version and the upstream GitHub Desktop release it is based on are different numbers. The entries above are the fork\'s.',
    ],
];
