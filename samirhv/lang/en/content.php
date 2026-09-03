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
*/

return [
    'projects' => [
        'shvia' => [
            'title' => 'ShvIA',
            'description' => "Blue3's internal AI assistant for operational support and corporate knowledge lookup. Chat across multiple models, voice dictation and read-aloud, and a Code mode for development work.\n\nUse it online in the browser (always the latest version) or download the desktop app for Windows, macOS and Linux.",
        ],

        'github-desktop' => [
            'title' => 'GitHub Desktop',
            'description' => "GitHub Desktop is GitHub's open-source visual Git client — Electron, TypeScript and React. Commits, branches, history, pull requests and conflict resolution in a clean interface, with no commands to memorise.\n\nGitHub does not distribute the app for Linux. This is a community build that compiles from source and packages it as a .deb for Debian, Ubuntu and derivatives — and also produces .exe/.msi installers for Windows.",
        ],

        'ai-usagebar' => [
            'title' => 'ai-usagebar',
            /* Authored rather than translated line for line: this project's page
               is a custom view, so the database description is never rendered in
               full anywhere public and only its first 160 characters could be
               read back. The English below says the same thing, from the page
               content itself. Replace it if the database text changes. */
            'description' => 'Monitor how much of your AI plans you have used — Anthropic Claude, OpenAI Codex, Z.AI, OpenRouter and DeepSeek — straight in your system bar (Waybar/GNOME on Linux, the menu bar on macOS, the tray on Windows), plus a cross-platform terminal TUI.',
        ],

        /* TODO — sshvterm has no `title`/`description` entry yet.
           It is a link-only project, so it has no /p/ page and its description is
           only ever rendered truncated; the full database text is not readable
           from the public HTML, and guessing the cut-off tail would be inventing
           copy about a product. Until it is filled in, the fallback renders the
           Portuguese description on the English page.

           What to translate (the Portuguese, truncated at 160 chars as the list
           shows it): "Cliente SSH/SFTP desktop e multiplataforma, com sync
           zero-knowledge. Tem um agente de IA que opera o terminal — propõe e
           executa comandos no PTY visível, sob um…" */
        'sshvterm' => [
            'title' => 'SShvTerm',
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
