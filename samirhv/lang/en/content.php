<?php

/*
| English for the prose that lives in the DATABASE.
|
| `projects.title`, `.description` and `.category`, plus `project_files.label`,
| are written in the admin in Portuguese. They are content, not interface, so
| they are not in the per-screen files — they are here, keyed by project slug,
| and App\Support\Content looks them up.
|
| A MISSING KEY IS SAFE. Content::project() falls back to the database value, so
| a project with no entry renders Portuguese on the English page rather than
| rendering blank. That is deliberate: a new project created in the admin
| appears immediately, in one language, instead of appearing broken.
|
| THE COST, WRITTEN DOWN: rename a project in the admin and its English title
| keeps the old translation until someone edits this file. A `title_en` column
| would follow automatically, but it would need a field in the admin CRUD, and
| the admin is out of scope.
|
| The Portuguese source for all of this is database/seeders/ProjectsSeeder.php,
| which is authoritative: it was checked against each application's own
| repository on 2026-09-05. Change a description there and change it here.
*/

return [
    'projects' => [
        'shvia' => [
            'title' => 'ShvIA',
            'description' => "Blue3's internal AI assistant for operational support and corporate knowledge lookup. Chat across multiple models, voice dictation and read-aloud, and a Code mode for development work.\n\nUse it online in the browser (always the latest version) or download the desktop app for Windows, macOS and Linux.",
        ],

        'github-desktop' => [
            'title' => 'GitHub Desktop',
            'description' => "GitHub Desktop is GitHub's open-source visual Git client — Electron, TypeScript and React. Commits, branches, history, pull requests and conflict resolution in a clean interface, with no commands to memorise.\n\nGitHub does not distribute the app for Linux. This is a fork that compiles from source and packages for all three platforms: .deb, .rpm, AppImage and .pkg.tar.zst on Linux, .exe and .msi on Windows, .dmg on macOS. The fork adds a multi-repository panel — every repository of yours on one screen, with batch pull and push.",
        ],

        'ai-usagebar' => [
            'title' => 'ai-usagebar',
            /* Authored rather than translated line for line: this project's page
               is a custom view, so the database description is never rendered in
               full anywhere public and only its first 160 characters could be
               read back. The English below says the same thing, from the page
               content itself. Replace it if the database text changes. */
            'description' => 'Monitor how much of your AI plans you have used — fourteen providers, among them Anthropic Claude, OpenAI Codex, Z.AI, OpenRouter, DeepSeek, Kimi, xAI/Grok, MiniMax and ShvIA itself — straight in your system bar (Waybar/GNOME on Linux, the menu bar on macOS), plus a terminal TUI that runs on all three systems.',
        ],

        /* Filled in from the seeder, which is now the readable source for this
           text — it was the missing tail of a truncated list entry that made
           this a TODO for two releases. */
        'sshvterm' => [
            'title' => 'SShvTerm',
            'description' => "A cross-platform desktop SSH/SFTP client with zero-knowledge sync: hosts, keys and passwords are encrypted on your own machine and the server never sees the contents — and that sync server can be one you host yourself.\n\nIt carries an AI agent that operates the terminal — proposing and running commands in the visible PTY, under an allow · ask · deny policy you control (Anthropic, OpenAI, xAI/Grok and more), with your own key. Windows, macOS and Linux. Download from the official site.",
        ],
    ],

    'categories' => [
        'assistente_ia' => 'AI assistant',
        'aplicativo_desktop' => 'Desktop application',
        'monitor_de_uso_de_ia' => 'AI usage monitor',
        'cliente_ssh' => 'SSH client',
    ],

    /* File labels, keyed by the label itself (lowercased, unaccented,
       underscored). Empty on purpose: the current labels are filenames and
       format names, which are the same in both languages. Add an entry only
       when a label is actually prose. */
    'file_labels' => [],
];
