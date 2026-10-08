@extends('layouts.app')

@section('title', __('leb_300.title'))
@section('description', __('leb_300.meta_description'))

@section('content')

    <section class="s-section" style="padding-top:clamp(7rem,11vw,10rem); position:relative;">
        <div class="s-aura"></div>
        <div class="container s-prose" style="position:relative; z-index:1; max-width:820px;">

            <nav style="margin-bottom:30px;">
                <a href="{{ lroute('ai-benchmark') }}" class="s-meta" style="color:var(--s-accent-ink-2);"><i class="fa-solid fa-arrow-left" style="margin-right:7px;" aria-hidden="true"></i>{{ __('leb_300.back') }}</a>
            </nav>

            <header style="margin-bottom:24px;">
                <span class="s-kicker" style="margin-bottom:8px;">{{ __('leb_300.kicker') }}</span>
                <h1 class="s-display" style="font-size:clamp(1.9rem,4vw,2.7rem);">{{ __('leb_300.heading') }} <span style="color:var(--s-accent-ink-2);">{{ __('leb_300.heading_accent') }}</span></h1>
            </header>

            <div class="s-card s-card--pad" style="padding:22px; margin-bottom:32px;">
                <p class="s-body" style="margin:0;"><strong style="color:var(--s-ink);">{{ __('leb_300.note_title') }}</strong> {{ __('leb_300.note_body') }}</p>
            </div>

            <div class="s-body" style="color:var(--s-ink-2); line-height:1.8;">
                <h2 class="s-h3" style="margin:0 0 8px;">{{ __('leb_300.what_title') }}</h2>
                <p>{{ __('leb_300.what_body') }}</p>

                <h2 class="s-h3" style="margin:28px 0 8px;">{{ __('leb_300.stands_title') }}</h2>
                <p>{{ __('leb_300.stands_body') }}</p>

                <h2 class="s-h3" style="margin:28px 0 8px;">{{ __('leb_300.publish_title') }}</h2>
                <p>{{ __('leb_300.publish_body') }}</p>

                <h2 class="s-h3" style="margin:28px 0 8px;">{{ __('leb_300.more_title') }}</h2>
                {{-- `{!! !!}` is safe here: the substitutions are markup of this file and the
                     text comes from lang/, which is repository content — no user input. --}}
                <p>{!! __('leb_300.more_body', [
                    'repo' => '<a href="https://github.com/samirhvbr/ai-benchmark" target="_blank" rel="noopener">'.e(__('leb_300.repo_link')).'</a>',
                    'leb100' => '<a href="'.e(lroute('ai-benchmark')).'">'.e(__('leb_300.leb100_link')).'</a>',
                ]) !!}</p>
            </div>

        </div>
    </section>

@endsection
