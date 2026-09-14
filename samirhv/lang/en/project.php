<?php

/*
| The project page (/p/{slug}): access panel, recommended download, file tabs.
*/

return [
    'meta_download' => 'Download :project',

    'access_aria' => 'Access options',
    'access' => 'Access',

    'online_version' => 'Online version',
    'online_version_desc' => 'Open it in the browser — nothing to install, always the latest version.',
    'open_app' => 'Open the app',

    'desktop_app' => 'Desktop application',
    'desktop_app_desc' => 'Native builds with version, architecture and hash to check against.',
    'choose_download' => 'Choose a download',
    'in_preparation' => 'In preparation',

    /*
    | Per-project wording for the "site" block of the access panel.
    |
    | The generic strings above describe ShvIA: a product whose site is the
    | online half of the same thing you can also download. They are wrong for a
    | project whose site is where the INSTALLERS live (SShvTerm), for one whose
    | site IS the product (meuip.rs), and for one whose home is a repository
    | (ai-memory). Any of the three strings can be overridden per slug; a
    | project with no entry here keeps the generic ones.
    |
    | Keys must exist in BOTH languages. `Lang::has()` in the view falls back to
    | English, so a slug translated only here would put English on the
    | Portuguese page — the same leak App\Support\Content documents.
    */
    'sites' => [
        'meuip' => [
            'title' => 'The service',
            'desc' => 'It runs in the browser and in the terminal. Nothing to install, no account, no key.',
            'cta' => 'Open meuip.rs',
        ],

        'sshvterm' => [
            'title' => 'Official site',
            'desc' => 'The installers for Windows, macOS and Linux are served from there, with the release notes.',
            'cta' => 'Go to sshvterm.com',
        ],

        'ai-memory' => [
            'title' => 'Source and installation',
            'desc' => 'One Rust binary, built from source or installed from a package. The README has the steps for each system.',
            'cta' => 'Open on GitHub',
        ],
    ],

    'unavailable' => 'The file :file is unavailable at the moment.',

    'recommended' => 'Recommended download',
    'download_for' => 'Download for :os',
    'change_system' => 'change system',
    'no_build' => 'No build available yet.',
    'copy_command' => 'Copy the command',
    'copy' => 'copy',

    'files' => 'Files',

    'about_this_build' => 'What this build is, and what it packages',
    'os_tabs_aria' => 'Operating systems',
    'soon' => 'soon',
    'build_soon' => 'A :os build is coming.',
    'older_versions' => 'Earlier versions (:count)',

    'verify' => 'Check the integrity after downloading — Linux/macOS: :unix · Windows: :windows',
];
