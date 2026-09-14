<?php

/*
| What changed in each application, per version.
|
| CURATED, not mirrored. Each entry is written from the application's own
| changelog — CHANGELOG.md for most of them, VERSION.md for the GitHub Desktop
| fork — and rewritten for someone deciding whether to use it,
| not for someone maintaining it. An entry that only means something inside the
| repository does not belong on a product page.
|
| Checked against every repository on 2026-09-05, and the two whose pages arrived in
| 0.9.0 — SShvTerm and ai-memory — on 2026-09-14. The date on an entry
| is the date of the release, not of the check.
|
| ai-memory's entries are the UPSTREAM project's (akitaonrails/ai-memory), the
| one the page explains. ai-memory-web has its own version line; it is named in
| the side note rather than interleaved, because two version lines in one list
| read as one line that skips numbers.
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

        'ai-memory' => [
            [
                'version' => '2.2.0',
                'date' => '2026-09-12',
                'notes' => [
                    'Retrieval explains itself: a page that surfaced through the link graph now says which typed edge it arrived by — causes, fixes or contradicts — so "why did this come back" has an answer.',
                    'Questions about a moment in the past get a second path. Asking as of a date now searches the page versions that were alive then, not only the entity timeline, so "which database were we on during the outage" finds pages that name no entity at all.',
                    'Two opt-in ranking signals, both off by default so an unconfigured store ranks exactly as before. Measured on a 138-query set over a two-year production wiki, the pair moves hit@1 from 0.61 to 0.75.',
                ],
            ],
            [
                'version' => '2.1.0',
                'date' => '2026-09-06',
                'notes' => [
                    'Provider fallback chains: a transient failure — 429, 5xx, a timeout — moves to the next provider you listed, carrying the same request and schema. A deterministic error still stops immediately, as it did with one provider.',
                    'An interrupted bootstrap can be resumed from durable per-chunk progress instead of paying for every LLM call a second time.',
                    'OpenCode 2.0 beta becomes a first-party client, and a finished Codex session now gets the same automatic summary and cross-agent handoff Claude Code already had.',
                ],
            ],
            [
                'version' => '2.0.3',
                'date' => '2026-09-04',
                'notes' => [
                    'Source installs build with the committed lockfile, so two people installing the same tag get the same dependencies instead of whatever resolved that day.',
                    'Status reports the free space on the data directory. An operator whose safety archive left 77 MB free had the store fail to extend its write-ahead log eight minutes later, silently, for hours.',
                ],
            ],
            [
                'version' => '2.0.2',
                'date' => '2026-09-03',
                'notes' => [
                    'Relative links inside the wiki stop returning 404 in the web view, and a directory path lists the pages under it instead of failing.',
                    'A page whose stored title was blank reads back with the title from its own heading, instead of disappearing behind a bogus "multiple pages share title" warning.',
                ],
            ],
            [
                'version' => '2.0.0',
                'date' => '2026-09-02',
                'notes' => [
                    'A launchd agent for macOS, so the server keeps running after the terminal that started it closes. Until now closing it stopped hook delivery with nothing to say so.',
                    'Local embeddings with no API key and no external server: an in-process model, fetched once, whose vectors coexist with any provider\'s.',
                    'Status tells more of the truth — migration state, stored embeddings by provider and model, typed-edge counts, and a queue gauge that surfaces a writer that has wedged.',
                ],
            ],
        ],

        'sshvterm' => [
            [
                'version' => '2.0.78',
                'date' => '2026-09-11',
                'notes' => [
                    'Nothing visible changed, and that is the entry: the five largest files in the app now carry a size ceiling that fails the build when they grow. They had been growing for thirty versions, and the cost was the kind of bug you do see — a fix applied to one path and not to its twin.',
                ],
            ],
            [
                'version' => '2.0.77',
                'date' => '2026-09-10',
                'notes' => [
                    'The VPN screens stop stating things they cannot know. A refused WireGuard change now says why instead of the switch snapping back on its own, and a vault it could not read says exactly that rather than printing "no host with VPN configured" — a claim expensive enough to make someone re-import a .conf holding a private key.',
                    'The markdown that renders the agent\'s replies no longer rewrites the commands it is showing you.',
                ],
            ],
            [
                'version' => '2.0.76',
                'date' => '2026-09-10',
                'notes' => [
                    'Secret detection runs on its own; rewriting what it found waits for your click. Finding and changing are two decisions, and only one of them is the app\'s to make.',
                ],
            ],
            [
                'version' => '2.0.75',
                'date' => '2026-09-10',
                'notes' => [
                    'The redaction can be re-run over what is already stored. The earlier fix stopped new leaks and removed none of the old ones, which left the sessions recorded before it exactly as they were.',
                ],
            ],
            [
                'version' => '2.0.74',
                'date' => '2026-09-10',
                'notes' => [
                    'The leak at the boundary between two chunks of terminal output was closed in one of the three places that write the session log. The other two write the same log, and now they close it too.',
                ],
            ],
            [
                'version' => '2.0.73',
                'date' => '2026-09-10',
                'notes' => [
                    'Six failures that reached you as "unrecognised error" now say what went wrong.',
                ],
            ],
        ],

    ],

    /* Facts that belong beside the list rather than inside an entry. */
    'notes' => [
        'ai-usagebar' => 'Later work in this fork — the ShvIA and MiniMax providers, and the API status panel — has shipped but has not been given a version of its own yet.',
        'shvia' => 'ShvIA is three products with three version lines: the web platform above, the desktop app, and the public site. The patch number climbs fast by design — it increments on every new screen, migration or visible change.',
        'github-desktop' => 'The fork version and the upstream GitHub Desktop release it is based on are different numbers. The entries above are the fork\'s.',
        'ai-memory' => 'These are ai-memory\'s own releases. ai-memory-web — the web panel shown above — is a separate project on its own version line, currently 0.1.x.',
        'sshvterm' => 'SShvTerm releases often, and not every version changes something you can see; the list above is the ones that do. The installers and the full notes are on sshvterm.com.',
    ],
];
