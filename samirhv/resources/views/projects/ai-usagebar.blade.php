@extends('layouts.app')

@section('title', 'ai-usagebar')
@section('description', __('ai_usagebar.meta_description'))

@push('styles')
<style>
    /* ── Tipografia de seção ─────────────────────────────────────── */
    .aub-h2{ font-family:var(--s-sans); font-size:1.35rem; font-weight:700; color:#f1f5f9; letter-spacing:-.01em; margin:0 0 6px; }
    .aub-lead{ font-family:var(--s-sans); font-size:.95rem; color:#94a3b8; line-height:1.7; margin:0 0 22px; }
    .aub-h3{ font-family:var(--s-sans); font-size:1.02rem; font-weight:600; color:#e2e8f0; margin:26px 0 10px; display:flex; align-items:center; gap:9px; }
    .aub-h3 i{ color:#6366f1; font-size:.95rem; }

    /* ── Blocos de comando com copiar ────────────────────────────── */
    .aub-cmd{ position:relative; margin:0 0 14px; }
    .aub-code{ margin:0; background:#0a0a12; border:1px solid rgba(99,102,241,0.16); border-radius:11px; padding:15px 54px 15px 16px; overflow-x:auto; }
    .aub-code code{ font-family:'JetBrains Mono',monospace; font-size:.8rem; line-height:1.75; color:#c7d2fe; white-space:pre; background:none; padding:0; }
    .aub-code .cmt{ color:#5b6478; }
    .aub-code .prompt{ color:#6366f1; user-select:none; }
    .aub-copy{ position:absolute; top:9px; right:9px; font-family:'JetBrains Mono',monospace; font-size:.66rem; color:#818cf8; background:#12121c; border:1px solid rgba(99,102,241,0.22); border-radius:7px; padding:4px 9px; cursor:pointer; transition:.15s; }
    .aub-copy:hover{ color:#c7d2fe; border-color:rgba(99,102,241,0.5); }
    .aub-copy.copied{ color:#34d399; border-color:rgba(52,211,153,0.5); }

    /* ── Callout ─────────────────────────────────────────────────── */
    .aub-note{ display:flex; gap:11px; background:rgba(99,102,241,0.07); border:1px solid rgba(99,102,241,0.2); border-radius:11px; padding:13px 16px; margin:0 0 16px; font-family:var(--s-sans); font-size:.86rem; color:#cbd5e1; line-height:1.6; }
    .aub-note i{ color:#6366f1; margin-top:2px; flex-shrink:0; }
    .aub-note.amber{ background:rgba(234,179,8,0.07); border-color:rgba(234,179,8,0.25); color:#fde68a; }
    .aub-note.amber i{ color:#eab308; }

    /* ── Screenshots ─────────────────────────────────────────────── */
    .aub-shots{ display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:16px; margin:8px 0 6px; }
    .aub-shot{ margin:0; }
    .aub-shot img{ width:100%; height:auto; border-radius:12px; border:1px solid rgba(99,102,241,0.18); background:#0a0a12; display:block; box-shadow:0 8px 30px rgba(0,0,0,0.4); }
    .aub-shot figcaption{ font-family:'JetBrains Mono',monospace; font-size:.68rem; color:#64748b; margin-top:8px; line-height:1.5; }

    /* ── Abas por SO ─────────────────────────────────────────────── */
    .os-tabs{ display:flex; gap:8px; margin:0 0 22px; flex-wrap:wrap; }
    .os-tab{ font-family:var(--s-sans); font-size:.85rem; font-weight:600; color:#94a3b8; background:#11111c; border:1px solid rgba(99,102,241,0.15); border-radius:9px; padding:9px 18px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:.15s; }
    .os-tab:hover{ color:#e2e8f0; border-color:rgba(99,102,241,0.4); }
    .os-tab.is-active{ color:#fff; background:rgba(99,102,241,0.15); border-color:#6366f1; }
    .os-panel{ animation:aubFade .2s ease; }
    @keyframes aubFade{ from{opacity:0; transform:translateY(4px);} to{opacity:1; transform:none;} }
    @media (max-width:560px){ .os-tabs{ overflow-x:auto; } }
</style>
{{-- Sem JS: mostra todas as seções de SO e esconde as abas (nada fica inacessível). --}}
<noscript><style>.os-panel{ display:block !important; } .os-tabs{ display:none !important; } .os-panel + .os-panel{ margin-top:40px; padding-top:34px; border-top:1px solid rgba(99,102,241,0.12); }</style></noscript>
@endpush

@section('content')

    <section class="s-section" style="padding-top:clamp(7rem,11vw,10rem); position:relative; overflow:hidden;">
        <div class="s-aura"></div>

        <div class="container s-prose" style="position:relative; z-index:1; max-width:880px;">

            <nav style="margin-bottom:32px; font-family:'JetBrains Mono',monospace; font-size:.78rem;">
                <a href="{{ lroute('home') }}" style="color:#6366f1; text-decoration:none;"><i class="fa-solid fa-arrow-left me-2"></i>{{ __('shell.home') }}</a>
            </nav>

            {{-- ── Header ───────────────────────────────────────────────── --}}
            <header style="display:flex; align-items:flex-start; gap:18px; margin-bottom:24px;">
                <span style="width:58px; height:58px; border-radius:14px; background:rgba(99,102,241,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid fa-gauge-high" style="color:#6366f1; font-size:1.6rem;"></i>
                </span>
                <div>
                    <span class="s-kicker">{{ __('ai_usagebar.kicker') }}</span>
                    <h1 style="font-family:var(--s-sans); font-size:2.2rem; font-weight:700; color:#f1f5f9; letter-spacing:-.02em; margin:4px 0 0;">ai<span style="color:#6366f1;">-</span>usagebar</h1>
                    <p style="margin:9px 0 0; font-family:var(--s-sans); font-size:.9rem; color:#94a3b8; line-height:1.5;">
                        @php
                            // Whole sentences as single keys, with markers for the links.
                            // The values come from lang/, which is repository content — no
                            // user input reaches these unescaped echoes.
                            $aubLink = fn (string $href, string $text, string $extra = '') => '<a href="'.$href.'" target="_blank" rel="noopener" style="color:#818cf8; text-decoration:none;'.$extra.'">'.$text.'</a>';
                        @endphp
                        {!! __('ai_usagebar.byline', [
                            'akita' => $aubLink('https://github.com/akitaonrails/ai-usagebar', 'Fabio Akita', ' font-weight:600;'),
                            'fork' => $aubLink('https://github.com/samirhvbr/ai-usagebar', __('ai_usagebar.byline_fork')),
                        ]) !!}
                    </p>
                </div>
            </header>

            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:30px; font-family:'JetBrains Mono',monospace; font-size:.7rem;">
                @foreach(['Rust', 'Waybar', 'GNOME Shell', 'macOS menu bar', 'Windows tray', 'TUI', 'MIT'] as $tech)
                    <span style="color:#a5b4fc; background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); border-radius:6px; padding:4px 11px;">{{ $tech }}</span>
                @endforeach
            </div>

            {{-- ── Intro ────────────────────────────────────────────────── --}}
            <div style="font-family:var(--s-sans); font-size:1.02rem; color:#cbd5e1; line-height:1.8; margin-bottom:30px;">
                @php
                    $aubB = fn (string $text) => '<strong style="color:#f1f5f9;">'.$text.'</strong>';
                    $aubC = fn (string $text) => '<code style="color:#a5b4fc;">'.$text.'</code>';
                @endphp
                <p>{!! __('ai_usagebar.intro_what', [
                    'name' => $aubB('ai-usagebar'),
                    'anthropic' => $aubB('Anthropic Claude'),
                    'codex' => $aubB('OpenAI Codex'),
                    'zai' => $aubB('Z.AI (GLM)'),
                    'openrouter' => $aubB('OpenRouter'),
                    'deepseek' => $aubB('DeepSeek'),
                    'window' => $aubB(__('ai_usagebar.intro_window')),
                    'weekly' => $aubB(__('ai_usagebar.intro_weekly')),
                ]) !!}</p>
                <p style="margin-top:1rem;">{!! __('ai_usagebar.intro_how', [
                    'backend' => $aubB(__('ai_usagebar.intro_backend')),
                    'waybar' => $aubB('Waybar'),
                    'tui' => $aubB(__('ai_usagebar.intro_tui')),
                    'gnome' => $aubB('GNOME Shell'),
                    'menubar' => $aubB(__('ai_usagebar.intro_menubar')),
                    'tray' => $aubB(__('ai_usagebar.intro_tray')),
                    'json' => $aubC('--json'),
                ]) !!}</p>
            </div>

            <figure class="aub-shot" style="margin:0 0 40px;">
                <img src="{{ asset('img/projects/ai-usagebar/linux-1.png') }}" alt="{{ __('ai_usagebar.shot_main_alt') }}" loading="lazy">
                <figcaption>{{ __('ai_usagebar.shot_main') }}</figcaption>
            </figure>

            {{-- ── Features ─────────────────────────────────────────────── --}}
            <h2 class="aub-h2">{{ __('ai_usagebar.features') }}</h2>
            <div class="row g-3" style="margin:0 0 44px;">
                @php
                    $features = [
                        ['fa-solid fa-layer-group', __('ai_usagebar.feature_multi'), __('ai_usagebar.feature_multi_desc')],
                        ['fa-solid fa-window-maximize', __('ai_usagebar.feature_native'), __('ai_usagebar.feature_native_desc')],
                        ['fa-solid fa-terminal', __('ai_usagebar.feature_tui'), __('ai_usagebar.feature_tui_desc')],
                        ['fa-solid fa-arrows-rotate', __('ai_usagebar.feature_refresh'), __('ai_usagebar.feature_refresh_desc')],
                        ['fa-solid fa-shield-halved', __('ai_usagebar.feature_auth'), __('ai_usagebar.feature_auth_desc')],
                        ['fa-solid fa-feather', __('ai_usagebar.feature_dropin'), __('ai_usagebar.feature_dropin_desc')],
                    ];
                @endphp
                @foreach($features as [$icon, $title, $desc])
                    <div class="col-md-6">
                        <div class="s-card h-100" style="padding:20px;">
                            <i class="{{ $icon }}" style="color:#6366f1; font-size:1.1rem;"></i>
                            <h3 style="font-family:var(--s-sans); font-size:.98rem; font-weight:600; color:#f1f5f9; margin:11px 0 5px;">{{ $title }}</h3>
                            <p style="font-family:var(--s-sans); font-size:.84rem; color:#94a3b8; line-height:1.6; margin:0;">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Step 0: authentication ───────────────────────────────── --}}
            <h2 class="aub-h2">{{ __('ai_usagebar.auth_heading') }}</h2>
            @php($aubS = fn (string $text) => '<strong style="color:#e2e8f0;">'.$text.'</strong>')
            <p class="aub-lead">{!! __('ai_usagebar.auth_lead', ['claude_code' => $aubS('Claude Code')]) !!}</p>
            <div class="aub-cmd">
                <pre class="aub-code"><code><span class="prompt">$</span> claude        <span class="cmt"># {{ __('ai_usagebar.cmt_claude_paths') }}</span>
<span class="prompt">$</span> codex login   <span class="cmt"># OpenAI Codex → ~/.codex/auth.json</span></code></pre>
                <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
            </div>
            <div class="aub-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>{!! __('ai_usagebar.auth_note', [
                    'api_key' => '<strong>'.__('ai_usagebar.auth_note_key').'</strong>',
                    'zai' => '<code>ZAI_API_KEY</code>',
                    'openrouter' => '<code>OPENROUTER_API_KEY</code>',
                    'deepseek' => '<code>DEEPSEEK_API_KEY</code>',
                    'config' => '<code>~/.config/ai-usagebar/config.toml</code>',
                    'vendors' => '<strong>Vendors</strong>',
                ]) !!}</span>
            </div>

            {{-- ── Installation per OS ──────────────────────────────────── --}}
            <h2 class="aub-h2" style="margin-top:44px;">{{ __('ai_usagebar.install_heading') }}</h2>
            <p class="aub-lead">{{ __('ai_usagebar.install_lead') }}</p>

            <div class="os-tabs" role="tablist" aria-label="{{ __('ai_usagebar.os_tabs_aria') }}">
                <button type="button" class="os-tab is-active" data-os-tab="linux"><i class="fa-brands fa-linux"></i> Linux</button>
                <button type="button" class="os-tab" data-os-tab="macos"><i class="fa-brands fa-apple"></i> macOS</button>
                <button type="button" class="os-tab" data-os-tab="windows"><i class="fa-brands fa-windows"></i> Windows</button>
            </div>

            {{-- ─────────── LINUX ─────────── --}}
            <div class="os-panel" data-os-panel="linux">

                <h3 class="aub-h3"><i class="fa-solid fa-download"></i> {{ __('ai_usagebar.linux_binary') }}</h3>
                <p class="aub-lead">{!! __('ai_usagebar.linux_binary_lead', [
                    'arch' => $aubS(__('ai_usagebar.linux_binary_arch')),
                    'others' => $aubS(__('ai_usagebar.linux_binary_others')),
                    'rustup' => '<code>rustup</code>',
                    'binstall' => '<code>cargo-binstall</code>',
                ]) !!}</p>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="cmt"># {{ __('ai_usagebar.cmt_arch_pick') }}</span>
<span class="prompt">$</span> yay -S ai-usagebar-bin     <span class="cmt"># {{ __('ai_usagebar.cmt_prebuilt') }}</span>
<span class="prompt">$</span> yay -S ai-usagebar         <span class="cmt"># {{ __('ai_usagebar.cmt_from_source_fast') }}</span>

<span class="cmt"># {{ __('ai_usagebar.cmt_other_distros') }}</span>
<span class="prompt">$</span> cargo install ai-usagebar  <span class="cmt"># {{ __('ai_usagebar.cmt_from_source_rustup') }}</span>
<span class="prompt">$</span> cargo binstall ai-usagebar <span class="cmt"># {{ __('ai_usagebar.cmt_binstall') }}</span></code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>
                <p class="aub-lead">{!! __('ai_usagebar.linux_test_lead', ['bin' => '<code>ai-usagebar</code>', 'tui' => '<code>ai-usagebar-tui</code>']) !!}</p>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> ai-usagebar --vendor anthropic --pretty   <span class="cmt"># {{ __('ai_usagebar.cmt_should_print') }}</span>
<span class="prompt">$</span> ai-usagebar-tui                            <span class="cmt"># {{ __('ai_usagebar.cmt_tui_standalone') }}</span></code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>

                <h3 class="aub-h3"><i class="fa-brands fa-gnome"></i> {{ __('ai_usagebar.linux_gnome') }}</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> cd gnome-extension
<span class="prompt">$</span> ./install.sh          <span class="cmt"># {{ __('ai_usagebar.cmt_symlink_schema') }}</span>
<span class="cmt"># {{ __('ai_usagebar.cmt_reload_gnome') }}</span>
<span class="prompt">$</span> gnome-extensions enable ai-usagebar@akitaonrails.github.io
<span class="prompt">$</span> gnome-extensions prefs  ai-usagebar@akitaonrails.github.io   <span class="cmt"># {{ __('ai_usagebar.cmt_prefs_vendors') }}</span></code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>
                <div class="aub-shots">
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/linux-3.png') }}" alt="{{ __('ai_usagebar.shot_gnome_prefs_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_gnome_prefs') }}</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/linux-4.png') }}" alt="{{ __('ai_usagebar.shot_gnome_panel_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_gnome_panel') }}</figcaption></figure>
                </div>

                <h3 class="aub-h3"><i class="fa-solid fa-bars-staggered"></i> {{ __('ai_usagebar.linux_waybar') }}</h3>
                <p class="aub-lead">{!! __('ai_usagebar.linux_waybar_lead', ['config' => '<code>~/.config/waybar/config</code>']) !!}</p>
                <div class="aub-cmd">
                    <pre class="aub-code"><code>"custom/aibar": {
    "exec": "ai-usagebar --format '{vendor_short} {session_pct}% · {session_reset}'",
    "return-type": "json",
    "interval": 300,
    "signal": 13,
    "tooltip": true,
    "on-click": "ai-usagebar-tui",
    "on-scroll-up":   "ai-usagebar --cycle-next",
    "on-scroll-down": "ai-usagebar --cycle-prev"
}</code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>
                <div class="aub-note amber">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{!! __('ai_usagebar.linux_waybar_warn', ['interval' => '<code>interval: 300</code>']) !!}</span>
                </div>
                <div class="aub-shots">
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/waybar.png') }}" alt="{{ __('ai_usagebar.shot_waybar_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_waybar') }}</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/tui-openai.png') }}" alt="{{ __('ai_usagebar.shot_tui_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_tui') }}</figcaption></figure>
                </div>

            </div>

            {{-- ─────────── macOS ─────────── --}}
            <div class="os-panel" data-os-panel="macos" style="display:none;">

                <h3 class="aub-h3"><i class="fa-solid fa-list-check"></i> {{ __('ai_usagebar.prereqs') }}</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> xcode-select --install     <span class="cmt"># {{ __('ai_usagebar.cmt_clt_swiftc') }}</span>
<span class="prompt">$</span> cargo install ai-usagebar   <span class="cmt"># {{ __('ai_usagebar.cmt_backend_cargo') }}</span>
<span class="prompt">$</span> claude                      <span class="cmt"># {{ __('ai_usagebar.cmt_login_keychain') }}</span></code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>

                <h3 class="aub-h3"><i class="fa-solid fa-hammer"></i> {{ __('ai_usagebar.macos_build') }}</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> git clone https://github.com/samirhvbr/ai-usagebar.git
<span class="prompt">$</span> cd ai-usagebar/macos
<span class="prompt">$</span> ./build.sh                  <span class="cmt"># {{ __('ai_usagebar.cmt_swiftc_noxcode') }}</span>
<span class="prompt">$</span> ./ai-usagebar-menubar &      <span class="cmt"># {{ __('ai_usagebar.cmt_menubar_nodock') }}</span></code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>

                <h3 class="aub-h3"><i class="fa-solid fa-power-off"></i> {{ __('ai_usagebar.macos_login') }}</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> ./install-agent.sh          <span class="cmt"># {{ __('ai_usagebar.cmt_launchagent') }}</span></code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>
                <div class="aub-note">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{!! __('ai_usagebar.macos_note', [
                        'preferences' => '<strong>'.__('ai_usagebar.macos_note_prefs').'</strong>',
                        'shortcut' => '<strong>⌘,</strong>',
                    ]) !!}</span>
                </div>
                <div class="aub-shots">
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/macosx-1.jpeg') }}" alt="{{ __('ai_usagebar.shot_macos_menu_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_macos_menu') }}</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/macosx-2.jpeg') }}" alt="{{ __('ai_usagebar.shot_macos_prefs_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_macos_prefs') }}</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/macosx-3.jpeg') }}" alt="{{ __('ai_usagebar.shot_macos_detail_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_macos_detail') }}</figcaption></figure>
                </div>

            </div>

            {{-- ─────────── WINDOWS ─────────── --}}
            <div class="os-panel" data-os-panel="windows" style="display:none;">

                <h3 class="aub-h3"><i class="fa-solid fa-list-check"></i> {{ __('ai_usagebar.prereqs') }}</h3>
                <p class="aub-lead">{!! __('ai_usagebar.windows_prereqs_lead', [
                    'rust' => $aubS(__('ai_usagebar.windows_prereqs_rust')),
                    'dotnet' => $aubS(__('ai_usagebar.windows_prereqs_dotnet')),
                ]) !!}</p>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">></span> winget install Rustlang.Rustup
<span class="prompt">></span> winget install Microsoft.DotNet.SDK.8</code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>

                <h3 class="aub-h3"><i class="fa-solid fa-hammer"></i> {{ __('ai_usagebar.windows_build') }}</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">></span> git clone https://github.com/samirhvbr/ai-usagebar.git
<span class="prompt">></span> cd ai-usagebar
<span class="prompt">></span> cargo build --release                    <span class="cmt"># → target\release\ai-usagebar.exe</span>
<span class="prompt">></span> cd windows-tray
<span class="prompt">></span> dotnet build -c Debug                     <span class="cmt"># {{ __('ai_usagebar.cmt_dotnet_debug') }}</span>
<span class="prompt">></span> start "" "bin\Debug\net8.0-windows\ai-usagebar-tray.exe"</code></pre>
                    <button class="aub-copy" type="button">{{ __('ai_usagebar.copy') }} ⧉</button>
                </div>
                <div class="aub-note">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{!! __('ai_usagebar.windows_note', [
                        'tray' => '<strong>'.__('ai_usagebar.windows_note_tray').'</strong>',
                        'caret' => '<code>^</code>',
                        'vendor_selector' => '<strong>'.__('ai_usagebar.windows_note_vendor').'</strong>',
                        'claude_path' => '<code>%USERPROFILE%\.claude\.credentials.json</code>',
                        'codex_path' => '<code>%USERPROFILE%\.codex\auth.json</code>',
                        'claude' => '<code>claude</code>',
                        'codex' => '<code>codex</code>',
                    ]) !!}</span>
                </div>
                <div class="aub-note amber">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{!! __('ai_usagebar.windows_warn', [
                        'waybar' => '<strong>'.__('ai_usagebar.windows_warn_waybar').'</strong>',
                        'tui' => '<code>ai-usagebar-tui</code>',
                        'json' => '<code>ai-usagebar --json/--pretty</code>',
                        'publish' => '<code>dotnet publish -c Release</code>',
                    ]) !!}</span>
                </div>
                <div class="aub-shots">
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/panel-anthropic.png') }}" alt="{{ __('ai_usagebar.shot_win_panel_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_win_panel') }}</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/vendor-menu.png') }}" alt="{{ __('ai_usagebar.shot_win_menu_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_win_menu') }}</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/tray-tooltip.png') }}" alt="{{ __('ai_usagebar.shot_win_tooltip_alt') }}" loading="lazy"><figcaption>{{ __('ai_usagebar.shot_win_tooltip') }}</figcaption></figure>
                </div>
                <p class="aub-lead" style="margin-top:14px; font-size:.82rem;"><i class="fa-solid fa-hands-clapping" style="color:#6366f1; margin-right:6px;"></i>{!! __('ai_usagebar.windows_credit', ['author' => $aubLink('https://github.com/EaeDave/ai-usagebar', 'EaeDave')]) !!}</p>

            </div>

            {{-- ── CTA ──────────────────────────────────────────────────── --}}
            <div class="d-flex gap-3 flex-wrap" style="margin-top:44px;">
                <a href="https://github.com/akitaonrails/ai-usagebar" target="_blank" rel="noopener" class="button button-rounded button-large m-0" style="background:#6366f1; border-color:#6366f1; color:#fff; font-family:var(--s-sans); font-weight:600; padding:14px 30px; box-shadow:0 4px 24px rgba(99,102,241,0.35);">
                    <i class="fa-brands fa-github me-2"></i>{{ __('ai_usagebar.cta_repo') }}
                </a>
                <a href="https://github.com/samirhvbr/ai-usagebar/blob/master/DESKTOP.md" target="_blank" rel="noopener" class="button button-rounded button-large button-border m-0" style="border-color:rgba(99,102,241,0.45); color:#a5b4fc; font-family:var(--s-sans); font-weight:600; padding:14px 30px;">
                    <i class="fa-solid fa-book me-2"></i>{{ __('ai_usagebar.cta_guide') }}
                </a>
            </div>

            <p style="margin-top:30px; font-family:'JetBrains Mono',monospace; font-size:.72rem; color:#64748b; line-height:1.7;">
                <i class="fa-solid fa-circle-info me-1" style="color:#6366f1;"></i>
                {!! __('ai_usagebar.footnote', [
                    'akita' => $aubLink('https://github.com/akitaonrails/ai-usagebar', 'Fabio Akita'),
                    'claudebar' => $aubLink('https://github.com/mryll/claudebar', 'claudebar'),
                    'fork' => $aubLink('https://github.com/samirhvbr/ai-usagebar', __('ai_usagebar.byline_fork')),
                ]) !!}
            </p>

        </div>
    </section>

@endsection

@push('scripts')
<script>
(function () {
    // Trocar de aba de SO.
    var tabs = document.querySelectorAll('.os-tab[data-os-tab]');
    var panels = document.querySelectorAll('.os-panel[data-os-panel]');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var os = tab.getAttribute('data-os-tab');
            tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
            panels.forEach(function (p) {
                p.style.display = (p.getAttribute('data-os-panel') === os) ? '' : 'none';
            });
        });
    });

    // Copiar o comando (lê o texto do bloco irmão, preservando quebras de linha).
    document.querySelectorAll('.aub-copy').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var block = btn.parentElement.querySelector('.aub-code');
            if (!block || !navigator.clipboard) return;
            navigator.clipboard.writeText(block.innerText.trim()).then(function () {
                var original = btn.textContent;
                btn.classList.add('copied');
                btn.textContent = 'copiado ✓';
                setTimeout(function () { btn.classList.remove('copied'); btn.textContent = original; }, 1400);
            });
        });
    });
})();
</script>
@endpush
