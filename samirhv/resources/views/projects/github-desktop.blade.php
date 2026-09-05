@extends('layouts.app')

@section('title', __('github_desktop.title'))
@section('description', __('github_desktop.meta_description'))

@section('content')

    <section class="s-section" style="padding-top:clamp(7rem,11vw,10rem); position:relative;">
        <div class="s-aura"></div>
        <div class="container s-prose" style="position:relative; z-index:1; max-width:820px;">

            <nav style="margin-bottom:30px;">
                <a href="{{ lroute('home') }}" class="s-meta" style="color:var(--s-accent-ink-2);"><i class="fa-solid fa-arrow-left" style="margin-right:7px;"></i>{{ __('shell.home') }}</a>
            </nav>

            <header class="d-flex align-items-start gap-3" style="margin-bottom:24px;">
                <span class="s-icon s-icon--lg"><i class="fa-brands fa-github"></i></span>
                <div>
                    <span class="s-kicker" style="margin-bottom:8px;">{{ __('github_desktop.kicker') }}</span>
                    <h1 class="s-display" style="font-size:clamp(1.9rem,4vw,2.7rem);">{{ __('github_desktop.heading') }} <span style="color:var(--s-accent-ink-2);">{{ __('github_desktop.heading_accent') }}</span></h1>
                </div>
            </header>

            @php
                /* Conferidos no fork em 05/09/2026: app/src/lib/fork-version.ts
                   (ForkVersion) e app/package.json (a release do upstream em que
                   o fork se baseia). São números independentes e o usuário
                   confunde os dois. */
                $forkVersion = '0.4.1';
                $upstreamVersion = 'GitHub Desktop 3.6.3';
            @endphp

            <div class="d-flex flex-wrap" style="gap:8px; margin-bottom:32px;">
                @foreach(['Electron', 'TypeScript', 'React', 'Linux · Windows · macOS', 'MIT'] as $tech)
                    <span class="s-tag">{{ $tech }}</span>
                @endforeach
            </div>

            <div class="s-body" style="color:var(--s-ink-2); line-height:1.8; margin-bottom:36px;">
                @php
                    // A sentença inteira é UMA chave, com marcas para as partes em
                    // negrito. Montar a frase por concatenação é o que produz
                    // metade em cada idioma quando só um lado é traduzido.
                    // `{!! !!}` é seguro aqui: as substituições são markup deste
                    // arquivo e o texto vem de lang/, que é conteúdo do repo —
                    // nada de entrada de usuário passa por aqui.
                    $forte = fn (string $texto) => '<strong style="color:var(--s-ink);">'.$texto.'</strong>';
                @endphp
                <p>{!! __('github_desktop.intro_what', [
                    'name' => $forte('GitHub Desktop'),
                    'electron' => $forte('Electron'),
                    'typescript' => $forte('TypeScript'),
                    'react' => $forte('React'),
                ]) !!}</p>
                <p style="margin-top:1rem;">{!! __('github_desktop.intro_why', [
                    'does_not_ship' => $forte(__('github_desktop.does_not_ship')),
                    'multirepo' => $forte(__('github_desktop.intro_multirepo')),
                ]) !!}</p>
            </div>

            <div class="s-grid" style="margin-bottom:42px; grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
                @php
                    // O painel multi-repositório vem primeiro: é o que este fork
                    // acrescenta, e o resto da lista o upstream já entrega.
                    $features = [
                        ['fa-solid fa-layer-group', __('github_desktop.feature_multirepo'), __('github_desktop.feature_multirepo_desc')],
                        ['fa-solid fa-arrows-rotate', __('github_desktop.feature_batch'), __('github_desktop.feature_batch_desc')],
                        ['fa-solid fa-code-commit', __('github_desktop.feature_commits'), __('github_desktop.feature_commits_desc')],
                        ['fa-solid fa-code-compare', __('github_desktop.feature_diff'), __('github_desktop.feature_diff_desc')],
                        ['fa-solid fa-code-pull-request', __('github_desktop.feature_pr'), __('github_desktop.feature_pr_desc')],
                        ['fa-solid fa-box-open', __('github_desktop.feature_packages'), __('github_desktop.feature_packages_desc')],
                    ];
                @endphp
                @foreach($features as [$icon, $title, $desc])
                    <div class="s-card s-card--pad" style="padding:22px;">
                        <i class="{{ $icon }}" style="color:var(--s-accent-ink-2); font-size:1.15rem;"></i>
                        <h3 class="s-h3" style="font-size:1rem; margin:12px 0 6px;">{{ $title }}</h3>
                        <p class="s-body s-muted" style="font-size:0.85rem; margin:0;">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            @include('partials.app-changelog', ['slug' => 'github-desktop'])

            <div class="d-flex gap-3 flex-wrap" style="margin-top:44px;">
                <a href="{{ lroute('downloads') }}" class="s-btn s-btn--lg"><i class="fa-solid fa-download"></i> {{ __('github_desktop.download') }}</a>
                <a href="https://github.com/samirhvbr/github-desktop" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--lg"><i class="fa-brands fa-github"></i> {{ __('github_desktop.source') }}</a>
            </div>

            {{-- Duas consequências operacionais que o usuário descobre depois de
                 instalar se ninguém contar antes: qual dos dois números é a
                 versão, e que o app não se atualiza sozinho. --}}
            <p class="s-meta" style="margin-top:30px; line-height:1.7;">
                <i class="fa-solid fa-code-branch" style="color:var(--s-accent-ink-2); margin-right:5px;"></i>
                {!! __('github_desktop.versions', [
                    'fork' => $forte('fork '.$forkVersion),
                    'upstream' => $forte($upstreamVersion),
                ]) !!}
            </p>

            <p class="s-meta" style="margin-top:12px; line-height:1.7;">
                <i class="fa-solid fa-circle-exclamation" style="color:var(--s-accent-ink-2); margin-right:5px;"></i>
                {{ __('github_desktop.autoupdate') }}
            </p>

            <p class="s-meta" style="margin-top:12px; line-height:1.7;">
                <i class="fa-solid fa-circle-info" style="color:var(--s-accent-ink-2); margin-right:5px;"></i>
                {{ __('github_desktop.disclaimer') }}
            </p>

        </div>
    </section>

@endsection
