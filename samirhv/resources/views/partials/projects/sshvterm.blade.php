{{-- The SShvTerm section — /p/sshvterm.
     Rendered by the convention in projects/show.blade.php: a project whose slug
     has a partial in this folder gets it between the description and the
     changelog.

     WHY THIS SECTION EXISTS. SShvTerm used to be a pure link: clicking the card
     left this site without ever saying what the product does. A client whose
     two differentiators are zero-knowledge sync and an agent under an
     allow·ask·deny policy does not fit in a showcase card, and neither of them
     is self-evident from the name. So the page describes it and the ACCESS
     PANEL, not this section, is what sends you to sshvterm.com — the installers
     are served there, never from here.

     Every string is a lang key (lang/{en,pt_BR}/sshvterm.php). The values are
     repository content and no user input reaches the unescaped echoes. --}}
@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/project-sections.css') }}">
@endpush

@php
    $sstCode = fn (string $text) => '<code>'.e($text).'</code>';
    $sstBold = fn (string $text) => '<b>'.e($text).'</b>';
@endphp

<section class="ps" aria-labelledby="sshvterm-title">
    <h2 id="sshvterm-title" class="ps__title">{{ __('sshvterm.title') }}</h2>
    <p class="ps__lead">{!! __('sshvterm.lead', ['xterm' => $sstCode('xterm.js')]) !!}</p>
    <p class="ps__lead">{{ __('sshvterm.lead_2') }}</p>

    <div class="ps-callout">
        <div class="ps-callout__top">
            <span class="ps-glyph" aria-hidden="true"><i class="fa-solid fa-lock"></i></span>
            <span class="ps-callout__label">{{ __('sshvterm.sync_label') }}</span>
        </div>
        <h3>{{ __('sshvterm.sync_title') }}</h3>
        <p>{!! __('sshvterm.sync_desc', ['crypto' => $sstCode('AES-256-CBC + HMAC-SHA256')]) !!}</p>
        <p>{{ __('sshvterm.sync_desc_2') }}</p>
    </div>

    <div class="ps-callout">
        <div class="ps-callout__top">
            <span class="ps-glyph" aria-hidden="true"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
            <span class="ps-callout__label">{{ __('sshvterm.agent_label') }}</span>
        </div>
        <h3>{{ __('sshvterm.agent_title') }}</h3>
        <p>{!! __('sshvterm.agent_desc', [
            'allow' => $sstBold(__('sshvterm.agent_allow')),
            'ask' => $sstBold(__('sshvterm.agent_ask')),
            'deny' => $sstBold(__('sshvterm.agent_deny')),
        ]) !!}</p>
        <p>{!! __('sshvterm.agent_desc_2', ['approx' => $sstCode('~')]) !!}</p>
        <p>{!! __('sshvterm.agent_providers', ['run' => $sstCode('```run')]) !!}</p>
    </div>

    <h3 class="ps__sub">{{ __('sshvterm.features_title') }}</h3>
    <div class="ps-grid">
        <div class="ps-card">
            <i class="fa-solid fa-folder-tree" aria-hidden="true"></i>
            <h3>{{ __('sshvterm.f_sftp') }}</h3>
            <p>{{ __('sshvterm.f_sftp_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
            <h3>{{ __('sshvterm.f_vpn') }}</h3>
            <p>{{ __('sshvterm.f_vpn_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-key" aria-hidden="true"></i>
            <h3>{{ __('sshvterm.f_keys') }}</h3>
            <p>{{ __('sshvterm.f_keys_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-arrows-left-right-to-line" aria-hidden="true"></i>
            <h3>{{ __('sshvterm.f_forward') }}</h3>
            <p>{!! __('sshvterm.f_forward_desc', ['local' => $sstCode('-L'), 'remote' => $sstCode('-R')]) !!}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-file-import" aria-hidden="true"></i>
            <h3>{{ __('sshvterm.f_import') }}</h3>
            <p>{!! __('sshvterm.f_import_desc', ['config' => $sstCode('~/.ssh/config')]) !!}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-bolt" aria-hidden="true"></i>
            <h3>{{ __('sshvterm.f_snippets') }}</h3>
            <p>{{ __('sshvterm.f_snippets_desc') }}</p>
        </div>
    </div>

    <p class="ps-note">
        <i class="fa-solid fa-download" aria-hidden="true"></i>
        <span>{!! __('sshvterm.note', [
            'site' => '<a href="https://sshvterm.com" target="_blank" rel="noopener">sshvterm.com</a>',
        ]) !!}</span>
    </p>
</section>
