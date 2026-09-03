@extends('layouts.app')

@section('title', __('downloads.title'))
@section('description', __('downloads.meta_description'))

@section('content')

    <section class="s-page-hero">
        <div class="s-aura"></div>
        <div class="container">

            <div class="s-page-hero__content">
                <span class="s-kicker">{{ __('downloads.kicker') }}</span>
                <h1 class="s-display" style="font-size:clamp(2.2rem,5vw,3.4rem);">{{ __('downloads.heading') }}</h1>
                <p class="s-lead" style="margin-top:1rem;">
                    {{ __('downloads.lead') }}
                </p>
                <div class="s-page-hero__meta">
                    <span>{{ trans_choice('downloads.projects_count', $projects->count()) }}</span>
                    <span>{{ trans_choice('downloads.files_count', $totalFiles) }}</span>
                    <span>{{ __('downloads.versioned') }}</span>
                </div>
            </div>

            @if(session('download_unavailable'))
                <div class="s-card" style="max-width:820px; margin:0 0 24px; padding:14px 18px; border-color:rgba(248,113,113,0.3); background:rgba(248,113,113,0.08);">
                    <span class="s-body" style="color:#fca5a5; font-size:0.92rem;">{!! __('downloads.unavailable', ['file' => '<strong>'.e(session('download_unavailable')).'</strong>']) !!}</span>
                </div>
            @endif

            <div class="s-project-list">
                @forelse($projects as $project)
                    @php
                        $files = $project->availableFiles;
                        $oses = collect(\App\Support\OsDetector::OSES)
                            ->filter(fn ($os) => $files->contains(fn ($f) => ($f->os ?: 'linux') === $os))
                            ->values();
                        $latest = $files->sortByDesc(fn ($f) => $f->effective_date)->first();
                        $arches = $files->map(fn ($f) => $f->arch)->filter()->unique()->values();
                        $dlTotal = $files->sum('downloads_count');
                    @endphp
                    <article class="s-card s-download-card">
                        <div class="s-download-card__top">
                            @if($project->icon)
                                <span class="s-icon"><i class="{{ $project->icon }}"></i></span>
                            @endif
                            <div class="s-download-card__body">
                                <div class="s-download-card__title-row">
                                    <h2 class="s-h3" style="font-size:1.25rem;">
                                        <a href="{{ $project->public_url }}" @if($project->redirectsToSite()) target="_blank" rel="noopener" @endif style="color:inherit;">{{ \App\Support\Content::project($project, 'title') }}</a>
                                    </h2>
                                    @if($project->category)<span class="s-tag">{{ \App\Support\Content::category($project->category) }}</span>@endif
                                    {{-- "use online" only for the hybrid case (a site AND downloads here); a pure link gets the "Visit site" button below. --}}
                                    @if($project->isHybrid())
                                        <a href="{{ $project->external_url }}" target="_blank" rel="noopener" class="s-tag s-tag--accent" style="text-decoration:none;"><i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('downloads.use_online') }}</a>
                                    @endif
                                </div>

                                @if($project->description)
                                    <p class="s-download-card__description">{{ Str::limit(\App\Support\Content::project($project, 'description'), 160) }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="s-download-card__footer">
                            @if($project->redirectsToSite())
                                {{-- Link-only project (e.g. SShvTerm): it lives on its own site. A ghost button means an action that leaves here. --}}
                                <div class="s-release-meta">
                                    <span class="s-release-meta__os">{{ __('downloads.official_site') }}</span>
                                    <span>{{ preg_replace('#^www\.#', '', parse_url($project->external_url, PHP_URL_HOST) ?? '') }}</span>
                                </div>
                                <a href="{{ $project->external_url }}" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--sm m-0">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ __('downloads.visit_site') }}
                                </a>
                            @elseif($project->hasCustomPage())
                                {{-- Documentation (e.g. ai-usagebar): no binaries here — installed via a package manager, with a guide per OS. --}}
                                <div class="s-release-meta">
                                    <span class="s-release-meta__os">{{ __('downloads.cross_platform') }}</span>
                                    <span>Linux · macOS · Windows</span>
                                    <span>{{ __('downloads.via_package_manager') }}</span>
                                </div>
                                <a href="{{ lroute('project.show', $project) }}" class="s-btn s-btn--sm m-0">
                                    <i class="fa-solid fa-terminal"></i> {{ __('downloads.install') }}
                                </a>
                            @else
                                {{-- Download / hybrid: provenance on show (OS, version, date, architecture, downloads). --}}
                                @if($files->isNotEmpty())
                                    <div class="s-release-meta">
                                        @foreach($oses as $os)
                                            <span class="s-release-meta__os">{{ \App\Support\OsDetector::label($os) }}</span>
                                        @endforeach
                                        @if($latest && $latest->version)<span class="s-release-meta__primary">v{{ $latest->version }}</span>@endif
                                        @if($latest && $latest->effective_date)<span>{{ $latest->effective_date->translatedFormat('d M Y') }}</span>@endif
                                        @if($arches->isNotEmpty())<span>{{ $arches->implode(' · ') }}</span>@endif
                                        @if($dlTotal > 0)<span>{{ lnumber($dlTotal) }} {{ __('downloads.downloads') }}</span>@endif
                                    </div>
                                @else
                                    <span class="s-meta">{{ __('downloads.files_soon') }}</span>
                                @endif
                                <a href="{{ lroute('project.show', $project) }}" class="s-btn s-btn--sm m-0">
                                    <i class="fa-solid fa-download"></i> {{ __('downloads.view_files') }}
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="s-card s-card--pad" style="text-align:center; padding:70px 0;">
                        <span class="s-meta">{{ __('downloads.empty') }}</span>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

@endsection
