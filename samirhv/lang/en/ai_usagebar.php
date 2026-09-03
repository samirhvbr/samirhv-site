<?php

/*
| The ai-usagebar project page (/p/ai-usagebar) — a per-OS install guide.
|
| The shell COMMANDS stay in the view: they are commands, identical in every
| language, and they belong where they can be reviewed next to their block. Only
| the `# comments` inside those blocks are keys here — they are prose that a
| reader depends on, and leaving them Portuguese is exactly the leak the
| bilingual render test exists to catch.
*/

return [
    'meta_description' => 'Monitor the usage of your AI plans (Claude, Codex, Z.AI, OpenRouter, DeepSeek) in your system bar — Linux, macOS and Windows. How to install on each OS.',

    'kicker' => 'AI usage monitor',
    'byline' => 'A project by :akita. The desktop integrations shown here (GNOME · macOS · Windows) are contributions from :fork.',
    'byline_fork' => "Samir's fork",

    'intro_what' => ':name shows how much of your AI plans you have already used — :anthropic, :codex, :zai, :openrouter and :deepseek — right in your system bar, with the usage bars for the :window and :weekly windows beside the clock and a menu holding the full breakdown.',
    'intro_window' => '5-hour window',
    'intro_weekly' => 'weekly',
    'intro_how' => 'It is a :backend with four interfaces: the :waybar widget and a :tui (cross-platform), the :gnome extension (Linux), the :menubar app (macOS) and the :tray app (Windows). All of them read the same :json from the binary — the authentication logic and each provider live only in the auditable Rust.',
    'intro_backend' => 'fast Rust backend',
    'intro_tui' => 'terminal TUI',
    'intro_menubar' => 'menu bar',
    'intro_tray' => 'tray',

    'shot_main' => 'Usage bars beside the clock + a menu with Session / Weekly / Sonnet / Extra usage.',
    'shot_main_alt' => 'GNOME panel showing the Claude usage bars with the dropdown menu open',

    'features' => 'Features',
    'feature_multi' => 'Multi-provider',
    'feature_multi_desc' => 'Claude, Codex, Z.AI, OpenRouter and DeepSeek in one place.',
    'feature_native' => 'Native UI per OS',
    'feature_native_desc' => 'Waybar/GNOME on Linux, the menu bar on macOS, the tray on Windows.',
    'feature_tui' => 'Cross-platform TUI',
    'feature_tui_desc' => '`ai-usagebar-tui` runs in any terminal, even over SSH.',
    'feature_refresh' => 'Auto-refresh',
    'feature_refresh_desc' => 'Refreshes every 60s in the app; a flock-guarded cache avoids rate limits.',
    'feature_auth' => 'No re-implemented auth',
    'feature_auth_desc' => 'Reads the OAuth that `claude`/`codex` already wrote; keys via env/config.',
    'feature_dropin' => 'Drop-in claudebar',
    'feature_dropin_desc' => 'The same flags and placeholders as the original claudebar, in Rust.',

    'auth_heading' => 'First of all: sign in to the provider once',
    'auth_lead' => 'ai-usagebar has no login of its own — it reads the credentials the official CLIs already write. For Claude, run :claude_code once; the token renews itself afterwards.',
    'auth_note' => 'Z.AI, OpenRouter and DeepSeek use an :api_key (the :zai / :openrouter / :deepseek environment variable, or inline in :config). You can also set them straight from the :vendors tab in the UIs.',
    'auth_note_key' => 'API key',

    'install_heading' => 'How to install',
    'install_lead' => 'Pick your system. The commands are the same as in the official repository.',
    'os_tabs_aria' => 'Operating system',

    'linux_binary' => '1. Install the binary',
    'linux_binary_lead' => 'On :arch, use the AUR. On :others, use crates.io (needs :rustup, or :binstall to fetch a prebuilt one).',
    'linux_binary_arch' => 'Arch',
    'linux_binary_others' => 'other distros',
    'linux_test_lead' => 'Installs :bin plus :tui. Test it right away:',
    'linux_gnome' => '2a. GNOME Shell extension',
    'shot_gnome_prefs' => 'Preferences → Vendors tab (login/config per provider).',
    'shot_gnome_prefs_alt' => 'GNOME preferences — Vendors tab',
    'shot_gnome_panel' => 'The bars appear in the top panel, beside the clock.',
    'shot_gnome_panel_alt' => 'Bars in the GNOME panel',
    'linux_waybar' => '2b. Waybar widget (Wayland)',
    'linux_waybar_lead' => 'A single module: scroll to cycle providers, click to open the TUI. Add it to your :config:',
    'linux_waybar_warn' => 'Keep :interval. The Anthropic and OpenAI endpoints are undocumented and rate-limit below roughly 300s. The internal 60s cache lets several surfaces coexist without exhausting the API.',
    'shot_waybar' => 'The Waybar widget with its breakdown tooltip (Pango).',
    'shot_waybar_alt' => 'Waybar widget showing Claude usage with the tooltip',
    'shot_tui' => '`ai-usagebar-tui` — one tab per provider (here, OpenAI Codex).',
    'shot_tui_alt' => 'TUI showing the OpenAI tab',

    'prereqs' => '1. Prerequisites',
    'macos_build' => '2. Build and run the menu bar app',
    'macos_login' => '3. (Optional) start at login',
    'macos_note' => 'Open :preferences from the menu bar (or :shortcut): bars, colours per severity, provider and interval — all applied live. The menu bar runs on macOS 10.15+; the Preferences window needs macOS 12+.',
    'macos_note_prefs' => 'Preferences',
    'shot_macos_menu' => 'The menu bar dropdown — Session / Weekly / Sonnet / Extra.',
    'shot_macos_menu_alt' => 'The app dropdown in the macOS menu bar',
    'shot_macos_prefs' => 'Preferences — colours per severity and the Vendors section.',
    'shot_macos_prefs_alt' => 'The app preferences on macOS, with colours and Vendors',
    'shot_macos_detail' => 'Bar, provider and interval settings.',
    'shot_macos_detail_alt' => 'A detail of the app preferences on macOS',

    'windows_prereqs_lead' => 'You need the :rust and the :dotnet:',
    'windows_prereqs_rust' => 'Rust toolchain',
    'windows_prereqs_dotnet' => '.NET 8 SDK',
    'windows_build' => '2. Build and run the tray app',
    'windows_note' => 'Look for the coloured dot in the :tray (click :caret to show hidden icons): left click → panel, right click → menu (Refresh, :vendor_selector, start with Windows…). Credentials live in :claude_path / :codex_path — run :claude/:codex once to create them.',
    'windows_note_tray' => 'tray',
    'windows_note_vendor' => 'Vendor selector',
    'windows_warn' => 'The :waybar and does not apply to Windows. :tui and :json do run natively. For a portable package (with no .NET installed): :publish.',
    'windows_warn_waybar' => 'Waybar widget is Wayland-only',
    'shot_win_panel' => 'The tray panel (left click) with Claude usage.',
    'shot_win_panel_alt' => 'The Windows tray app panel showing Claude usage',
    'shot_win_menu' => 'The menu (right click) — Vendor selector.',
    'shot_win_menu_alt' => 'Provider selection menu of the Windows tray app',
    'shot_win_tooltip' => 'The tray icon tooltip.',
    'shot_win_tooltip_alt' => 'Tooltip of the tray icon on Windows',
    'windows_credit' => 'The Windows tray app is by :author (MIT), included here with credit.',

    'cta_repo' => "Fabio Akita's repository",
    'cta_guide' => 'Desktop guide (fork)',

    'footnote' => 'ai-usagebar is a project by :akita — open source (MIT), a Rust port of :claudebar with support for more providers. The native desktop integrations (GNOME/macOS/Windows) shown here come from :fork. Some endpoints are undocumented; trademarks mentioned belong to their respective owners.',

    'copy' => 'copy',

    // The `# comments` inside the command blocks.
    'cmt_claude_paths' => 'Linux/Windows → ~/.claude/.credentials.json  ·  macOS → Keychain (read automatically)',
    'cmt_arch_pick' => 'Arch (AUR) — pick one:',
    'cmt_prebuilt' => 'prebuilt binary from Releases (~5s)',
    'cmt_from_source_fast' => 'builds from source (~30-60s)',
    'cmt_other_distros' => 'Other distros (crates.io):',
    'cmt_from_source_rustup' => 'builds from source (needs rustup)',
    'cmt_binstall' => 'downloads the prebuilt binary (needs cargo-binstall)',
    'cmt_should_print' => 'should print the bars',
    'cmt_tui_standalone' => 'tabbed TUI — works on its own, no Waybar',
    'cmt_symlink_schema' => 'symlink into ~/.local/share + compiles the GSettings schema',
    'cmt_reload_gnome' => 'Reload GNOME Shell: LOG OUT / LOG IN (do not use gnome-shell --replace)',
    'cmt_prefs_vendors' => 'bars, colours, Vendor login',
    'cmt_clt_swiftc' => 'Command Line Tools (for swiftc)',
    'cmt_backend_cargo' => 'backend in ~/.cargo/bin (needs rustup)',
    'cmt_login_keychain' => 'log in once — credentials go to the Keychain (read automatically)',
    'cmt_swiftc_noxcode' => 'swiftc -O → ./ai-usagebar-menubar (no Xcode project)',
    'cmt_menubar_nodock' => 'appears in the menu bar (no Dock icon)',
    'cmt_launchagent' => 'LaunchAgent with RunAtLoad — comes back at every login',
    'cmt_dotnet_debug' => 'fast (uses the installed runtime)',
];
