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
| repository on 2026-09-05, and Tura Notes against samirhvbr/tura-notes on
| 2026-09-11. Change a description there and change it here.
*/

return [
    'projects' => [
        'shvia' => [
            'title' => 'ShvIA',
            'description' => "Blue3's internal AI assistant for operational support and corporate knowledge lookup. Chat across multiple models, voice dictation and read-aloud, and a Code mode for development work.\n\nUse it online in the browser (always the latest version) or download the desktop app for Windows, macOS and Linux.",
        ],

        'tura-notes' => [
            'title' => 'Tura Notes',
            'description' => "A local-first Markdown note-taking app for Linux, macOS and Windows. You pick a folder; that folder is your workspace; the .md files inside it are your notes.\n\nThe files belong to you, not to the application: no proprietary format, no account, and no cloud of ours. Every note stays readable from a terminal, from VS Code, from git, rsync or any other editor — and working offline is not a mode, it is the normal case. Incremental search, YAML properties, tags, wiki links, backlinks, and an MCP server for AI agents with scoped permissions.\n\nThe macOS .dmg is signed and notarised by Apple and is served from here. The Linux packages (.deb, AppImage, .rpm) come out of the same local build pipeline and are published both here and on the GitHub Releases, which is also where the AUR package lives.",
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

        'ai-memory' => [
            'title' => 'ai-memory',
            'description' => "Long-term memory for coding agents. What a session learned — decisions, approaches that failed, questions still open — is written to markdown and comes back to the next agent, even a different one: quit Claude Code mid-task, open Codex in the same directory, and carry on from where you stopped.\n\nA project by Fabio Akita (akitaonrails/ai-memory), written in Rust, MIT licence. This page also shows ai-memory-web, the web panel that opens that archive for reading — nine screens over the same index, never writing to it.",
        ],

        /* Filled in from the seeder, which is now the readable source for this
           text — it was the missing tail of a truncated list entry that made
           this a TODO for two releases. */
        'sshvterm' => [
            'title' => 'SShvTerm',
            'description' => "A cross-platform desktop SSH/SFTP client with zero-knowledge sync: hosts, keys and passwords are encrypted on your own machine and the server never sees the contents — and that sync server can be one you host yourself.\n\nIt carries an AI agent that operates the terminal — proposing and running commands in the visible PTY, under an allow · ask · deny policy you control (Anthropic, OpenAI, xAI/Grok and more), with your own key. Windows, macOS and Linux. Download from the official site.",
        ],

        'meuip' => [
            'title' => 'meuip.rs',
            'description' => "Blue3's \"what is my IP\" service: it shows the caller's public address, with ASN, provider, country and region — and answers in the shape the caller asked in. `curl` gets the bare address and a newline; a browser gets the whole page.\n\nIt also carries a looking glass: a traceroute run on the server and streamed live to the page, for when it is your own network that blocks ICMP. No account, no API key and no cookie.",
        ],
    ],

    'categories' => [
        'assistente_ia' => 'AI assistant',
        'aplicativo_desktop' => 'Desktop application',
        'monitor_de_uso_de_ia' => 'AI usage monitor',
        'cliente_ssh' => 'SSH client',
        'notas_em_markdown' => 'Markdown notes',
        'memoria_de_agentes' => 'Agent memory',
        'ferramenta_de_rede' => 'Network tool',
    ],

    /* File labels, keyed by the label itself (lowercased, unaccented,
       underscored). Empty on purpose: the current labels are filenames and
       format names, which are the same in both languages. Add an entry only
       when a label is actually prose. */
    'file_labels' => [],
];
