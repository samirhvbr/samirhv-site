{{-- The ai-memory section — /p/ai-memory.
     Rendered by the convention in projects/show.blade.php: a project whose slug
     has a partial in this folder gets it between the description and the
     changelog.

     TWO PROJECTS, AND THE PAGE NEVER LETS THEM BLUR. The first half explains
     ai-memory, which is Fabio Akita's (akitaonrails/ai-memory). The second half
     shows ai-memory-web, which is ours (samirhvbr/ai-memory-web) and is the
     only reason this page can show anything at all — ai-memory is a daemon and
     a CLI, and a daemon has no screenshots. Each half links its own repository,
     and the closing block names both again side by side.

     Every string is a lang key (lang/{en,pt_BR}/ai_memory.php). The values are
     repository content and no user input reaches the unescaped echoes. --}}
@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/project-sections.css') }}">
@endpush

@php
    $amCode = fn (string $text) => '<code>'.e($text).'</code>';
    $amBold = fn (string $text) => '<b>'.e($text).'</b>';

    $amCoreRepo = 'https://github.com/akitaonrails/ai-memory';
    $amWebRepo = 'https://github.com/samirhvbr/ai-memory-web';

    /* file, caption key. `dashboard` is the overview shot and takes the full
       width; the rest read fine at half. */
    $amShots = [
        ['dashboard.png', 'dashboard', true],
        ['dashboard2.png', 'dashboard2', false],
        ['projects.png', 'projects', false],
        ['workspaces.png', 'workspaces', false],
        ['pages.png', 'pages', false],
        ['sessions.png', 'sessions', false],
        ['views.png', 'views', false],
        ['handoffs.png', 'handoffs', false],
        ['search.png', 'search', false],
    ];
@endphp

<section class="ps" aria-labelledby="ai-memory-title">
    <h2 id="ai-memory-title" class="ps__title">{{ __('ai_memory.title') }}</h2>
    <p class="ps__lead">{!! __('ai_memory.lead', [
        'name' => $amBold('ai-memory'),
        'claude' => $amBold('Claude Code'),
        'codex' => $amBold('Codex'),
    ]) !!}</p>
    <p class="ps__lead">{{ __('ai_memory.lead_2') }}</p>

    <h3 class="ps__sub">{{ __('ai_memory.features_title') }}</h3>
    <div class="ps-grid">
        <div class="ps-card">
            <i class="fa-solid fa-people-arrows" aria-hidden="true"></i>
            <h3>{{ __('ai_memory.f_agents') }}</h3>
            <p>{{ __('ai_memory.f_agents_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-server" aria-hidden="true"></i>
            <h3>{{ __('ai_memory.f_machines') }}</h3>
            <p>{{ __('ai_memory.f_machines_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-brands fa-markdown" aria-hidden="true"></i>
            <h3>{{ __('ai_memory.f_markdown') }}</h3>
            <p>{!! __('ai_memory.f_markdown_desc', ['md' => $amCode('.md')]) !!}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-wave-square" aria-hidden="true"></i>
            <h3>{{ __('ai_memory.f_silent') }}</h3>
            <p>{{ __('ai_memory.f_silent_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-coins" aria-hidden="true"></i>
            <h3>{{ __('ai_memory.f_nollm') }}</h3>
            <p>{{ __('ai_memory.f_nollm_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-users" aria-hidden="true"></i>
            <h3>{{ __('ai_memory.f_team') }}</h3>
            <p>{{ __('ai_memory.f_team_desc') }}</p>
        </div>
    </div>
</section>

<section class="ps" aria-labelledby="ai-memory-web-title">
    <div class="ps-callout">
        <div class="ps-callout__top">
            <span class="ps-glyph" aria-hidden="true"><i class="fa-solid fa-table-columns"></i></span>
            <span class="ps-callout__label">{{ __('ai_memory.web_label') }}</span>
        </div>
        <h3 id="ai-memory-web-title">{{ __('ai_memory.web_title') }}</h3>
        <p>{!! __('ai_memory.web_desc', [
            'web' => $amBold('ai-memory-web'),
            'q1' => $amBold(__('ai_memory.web_q1')),
            'q2' => $amBold(__('ai_memory.web_q2')),
        ]) !!}</p>
        <p>{!! __('ai_memory.web_desc_2', ['pragma' => $amCode('PRAGMA query_only = 1')]) !!}</p>
    </div>

    <h3 class="ps__sub">{{ __('ai_memory.screens_title') }}</h3>
    <p class="ps__lead">{{ __('ai_memory.screens_lead') }}</p>

    <div class="ps-shots">
        @foreach($amShots as [$file, $key, $wide])
            <figure class="ps-shot{{ $wide ? ' ps-shot--wide' : '' }}">
                <img src="{{ asset('img/projects/ai-memory/'.$file) }}"
                     alt="{{ __('ai_memory.shot_'.$key) }} — {{ __('ai_memory.shot_'.$key.'_desc') }}"
                     loading="lazy" decoding="async">
                <figcaption>
                    <b>{{ __('ai_memory.shot_'.$key) }}</b>
                    {{ __('ai_memory.shot_'.$key.'_desc') }}
                </figcaption>
            </figure>
        @endforeach
    </div>

    <p class="ps-note">
        <i class="fa-solid fa-language" aria-hidden="true"></i>
        <span>{!! __('ai_memory.shots_note', ['web' => $amBold('ai-memory-web')]) !!}</span>
    </p>

    <h3 class="ps__sub">{{ __('ai_memory.repos_title') }}</h3>
    <div class="ps-repos">
        <a class="ps-repo" href="{{ $amCoreRepo }}" target="_blank" rel="noopener">
            <i class="fa-brands fa-github" aria-hidden="true"></i>
            <span>
                <strong>akitaonrails/ai-memory</strong>
                <small>{{ __('ai_memory.repo_core_desc') }}</small>
            </span>
        </a>
        <a class="ps-repo" href="{{ $amWebRepo }}" target="_blank" rel="noopener">
            <i class="fa-brands fa-github" aria-hidden="true"></i>
            <span>
                <strong>samirhvbr/ai-memory-web</strong>
                <small>{{ __('ai_memory.repo_web_desc') }}</small>
            </span>
        </a>
    </div>
</section>
