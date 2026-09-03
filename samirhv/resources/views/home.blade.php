@extends('layouts.app')

@section('title', 'Samirhv')
@section('description', __('home.meta_description'))

@section('content')

    @php
        $featuredProject = $featured ?? $projects->first();
        $otherProjects = $featuredProject
            ? $projects->reject(fn ($project) => $project->id === $featuredProject->id)->values()
            : collect();
    @endphp

    {{-- ═══ HERO ═══ --}}
    <section class="s-home-hero">
        <div class="s-aura"></div>
        <div class="container">
            <div class="s-home-hero__grid">
                <div class="s-home-hero__copy s-reveal" data-d="1">
                    <span class="s-kicker">{{ __('home.kicker') }}</span>
                    <h1 class="s-home-title">{{ __('home.title') }}</h1>
                    <p class="s-lead s-home-intro">
                        {{ __('home.intro') }}
                    </p>

                    <div class="s-home-actions">
                        <a href="#projetos" class="s-btn s-btn--lg">
                            {{ __('home.explore') }} <i class="fa-solid fa-arrow-down"></i>
                        </a>
                        <a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="s-text-link">
                            <i class="fa-brands fa-github"></i> {{ __('home.see_code') }}
                        </a>
                    </div>

                    <div class="s-home-trust">
                        <span><i class="fa-solid fa-circle-check"></i> {{ __('home.trust_versioned') }}</span>
                        <span><i class="fa-solid fa-shield-halved"></i> {{ __('home.trust_hashes') }}</span>
                        <span><i class="fa-brands fa-linux"></i> {{ __('home.trust_linux') }}</span>
                    </div>
                </div>

                <div class="s-release-board s-reveal" data-d="2" aria-label="{{ __('home.board_aria') }}">
                    <div class="s-release-board__bar">
                        <div>
                            <span class="s-release-board__eyebrow">{{ __('home.board_eyebrow') }}</span>
                            <strong>{{ __('home.board_title') }}</strong>
                        </div>
                        <span class="s-live-status"><i></i> {{ __('home.board_online') }}</span>
                    </div>

                    @if($featuredProject)
                        <a href="{{ $featuredProject->public_url }}" @if($featuredProject->redirectsToSite()) target="_blank" rel="noopener" @endif class="s-release-board__featured">
                            <span class="s-release-board__icon"><i class="{{ $featuredProject->icon ?: 'fa-solid fa-cube' }}"></i></span>
                            <span class="s-release-board__featured-copy">
                                <small>{{ __('home.board_featured') }}</small>
                                <strong>{{ \App\Support\Content::project($featuredProject, 'title') }}</strong>
                                <span>{{ Str::limit(\App\Support\Content::project($featuredProject, 'description'), 105) }}</span>
                            </span>
                            <i class="fa-solid fa-arrow-up-right-from-square s-release-board__arrow"></i>
                        </a>
                    @endif

                    <div class="s-release-board__list">
                        @foreach($projects->take(4) as $project)
                            <a href="{{ $project->public_url }}" @if($project->redirectsToSite()) target="_blank" rel="noopener" @endif class="s-release-row">
                                <span class="s-release-row__index">0{{ $loop->iteration }}</span>
                                <span class="s-release-row__name">{{ \App\Support\Content::project($project, 'title') }}</span>
                                <span class="s-release-row__type">{{ \App\Support\Content::category($project->category) ?: ($project->redirectsToSite() ? 'site' : 'software') }}</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        @endforeach
                    </div>

                    <div class="s-release-board__foot">
                        <span>{{ trans_choice('home.board_count', $projects->count()) }}</span>
                        <a href="{{ lroute('downloads') }}">{{ __('home.board_open_downloads') }} <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ PROJECTS ═══ --}}
    <section class="s-projects-section" id="projetos">
        <div class="container">
            <div class="s-heading-row">
                <div>
                    <span class="s-section-number">{{ __('home.section_projects') }}</span>
                    <h2 class="s-h2">{{ __('home.projects_heading') }}</h2>
                    <p class="s-lead s-muted">{{ __('home.projects_lead') }}</p>
                </div>
                <a href="{{ lroute('downloads') }}" class="s-text-link d-none d-md-inline-flex">
                    {{ __('home.see_all_downloads') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            @if($featuredProject)
                <div class="s-portfolio-layout">
                    <a href="{{ $featuredProject->public_url }}" @if($featuredProject->redirectsToSite()) target="_blank" rel="noopener" @endif class="s-portfolio-featured">
                        <div class="s-portfolio-featured__top">
                            <span class="s-project-mark"><i class="{{ $featuredProject->icon ?: 'fa-solid fa-cube' }}"></i></span>
                            <span class="s-project-state"><i></i> {{ __('home.available') }}</span>
                        </div>
                        <div class="s-portfolio-featured__content">
                            <span class="s-project-overline">{{ __('home.main_project') }}</span>
                            <h3>{{ \App\Support\Content::project($featuredProject, 'title') }}</h3>
                            <p>{{ Str::limit(\App\Support\Content::project($featuredProject, 'description'), 220) }}</p>
                        </div>
                        <div class="s-portfolio-featured__foot">
                            <div>
                                @if($featuredProject->category)<span>{{ \App\Support\Content::category($featuredProject->category) }}</span>@endif
                                @if(($featuredProject->files_count ?? 0) > 0)<span>{{ $featuredProject->files_count }} {{ __('home.builds') }}</span>@endif
                                @if(($featuredProject->downloads_total ?? 0) > 0)<span>{{ lnumber($featuredProject->downloads_total) }} {{ __('home.downloads') }}</span>@endif
                            </div>
                            <strong>{{ $featuredProject->redirectsToSite() ? __('home.visit_project') : __('home.view_project') }} <i class="fa-solid fa-arrow-right"></i></strong>
                        </div>
                    </a>

                    <div class="s-portfolio-list">
                        @foreach($otherProjects as $project)
                            @php
                                $isLink = $project->redirectsToSite();
                                $meta = $isLink
                                    ? __('home.external_site')
                                    : (($project->downloads_total ?? 0) > 0
                                        ? lnumber($project->downloads_total).' '.__('home.downloads')
                                        : ($project->hasCustomPage() ? __('home.documentation') : __('home.project')));
                            @endphp
                            <a href="{{ $project->public_url }}" @if($isLink) target="_blank" rel="noopener" @endif class="s-portfolio-item">
                                <span class="s-portfolio-item__icon"><i class="{{ $project->icon ?: 'fa-solid fa-cube' }}"></i></span>
                                <span class="s-portfolio-item__copy">
                                    <small>{{ \App\Support\Content::category($project->category) ?: __('home.software') }}</small>
                                    <strong>{{ \App\Support\Content::project($project, 'title') }}</strong>
                                    <span>{{ Str::limit(\App\Support\Content::project($project, 'description'), 90) }}</span>
                                </span>
                                <span class="s-portfolio-item__meta">{{ $meta }}</span>
                                <i class="fa-solid {{ $isLink ? 'fa-arrow-up-right-from-square' : 'fa-arrow-right' }}"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="s-empty-state">{{ __('home.empty') }}</div>
            @endif

            <a href="{{ lroute('downloads') }}" class="s-btn s-btn--ghost d-md-none" style="width:100%; margin-top:24px;">
                {{ __('home.see_all_downloads') }} <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

    {{-- ═══ METHOD ═══ --}}
    <section class="s-method-section">
        <div class="container">
            <div class="s-method-intro">
                <span class="s-section-number">{{ __('home.section_principles') }}</span>
                <h2 class="s-h2">{!! __('home.principles_heading') !!}</h2>
            </div>
            <div class="s-method-grid">
                <article>
                    <span>01</span>
                    <h3>{{ __('home.principle_1') }}</h3>
                    <p>{{ __('home.principle_1_desc') }}</p>
                </article>
                <article>
                    <span>02</span>
                    <h3>{{ __('home.principle_2') }}</h3>
                    <p>{{ __('home.principle_2_desc') }}</p>
                </article>
                <article>
                    <span>03</span>
                    <h3>{{ __('home.principle_3') }}</h3>
                    <p>{{ __('home.principle_3_desc') }}</p>
                </article>
            </div>
        </div>
    </section>

@endsection
