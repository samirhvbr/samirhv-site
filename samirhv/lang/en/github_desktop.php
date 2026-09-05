<?php

/*
| The GitHub Desktop project page (a static view, no database row).
|
| Facts checked against the fork's own tree (samirhvbr/github-desktop, branch
| `development`) on 2026-09-05: script/package.ts, app/src/lib/fork-version.ts
| and VERSION.md.
*/

return [
    'title' => 'Multi-repository GitHub Desktop',
    'meta_description' => 'GitHub Desktop — GitHub\'s open-source visual Git client (Electron, TypeScript, React), with a multi-repository panel and installers for Linux, Windows and macOS.',

    'kicker' => 'Desktop application',
    'heading' => 'GitHub Desktop',
    'heading_accent' => 'multi-repository',

    'intro_what' => ':name is GitHub\'s open-source visual Git client — built on :electron and written in :typescript with :react. It makes day-to-day Git simpler: commits, branches, history, pull requests and conflict resolution in a clean interface, with no commands to memorise.',
    'intro_why' => 'Officially, GitHub :does_not_ship. This fork compiles GitHub Desktop from source and packages it for all three systems — and adds the reason the fork exists: a :multirepo, with every repository of yours on one screen.',
    'does_not_ship' => 'does not distribute the app for Linux',
    'intro_multirepo' => 'multi-repository panel',

    'feature_multirepo' => 'Multi-repository panel',
    'feature_multirepo_desc' => 'Every repository on one screen, flagging the ones with a pending commit or behind their remote.',
    'feature_batch' => 'Batch pull and push',
    'feature_batch_desc' => 'Tick several repositories and sync three at a time, with a result for each one.',
    'feature_commits' => 'Visual commits',
    'feature_commits_desc' => 'Stage, commit, branch and merge without the command line.',
    'feature_diff' => 'Side-by-side diff',
    'feature_diff_desc' => 'See exactly what changed before you confirm.',
    'feature_pr' => 'Pull requests',
    'feature_pr_desc' => 'Open and follow PRs and the status of their checks.',
    'feature_packages' => 'Native installer',
    'feature_packages_desc' => '.deb, .rpm, AppImage and .pkg.tar.zst on Linux; .exe and .msi on Windows; .dmg on macOS.',

    'versions' => 'Two versions, and they are not the same thing: :fork is this fork\'s own version and :upstream is the GitHub Desktop release it is based on. Both appear in the About screen.',
    'autoupdate' => 'Auto-update is off on purpose: GitHub\'s official feed was overwriting the fork\'s build. Updating means downloading the new version.',

    'download' => 'Download',
    'source' => 'Source on GitHub',

    'disclaimer' => 'A community project, with no official affiliation to GitHub, Inc. MIT licence, inherited from desktop/desktop. Trademarks mentioned belong to their respective owners.',
];
