@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Lang;

    $repo = 'https://github.com/samirhvbr/ai-benchmark';

    // The copy of this track (`leb_default` or `leb_security`: what the track measures). What the two pages say alike is
    // `leb_series`. `$instance` and `$other` come from LebTrackController::TRACKS.
    $copy = "leb_{$track}";
    $link = fn (string $label, string $href, bool $external = false): string => '<a href="'.e($href).'"'.($external ? ' target="_blank" rel="noopener"' : '').'>'.e($label).'</a>';
@endphp

@section('title', __("{$copy}.title"))
@section('description', __("{$copy}.meta_description"))

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/ai-benchmark.css') }}">
@endpush

@section('content')

    {{-- ═══ HERO ═══ --}}
    <section class="s-section ab-hero">
        <div class="s-aura"></div>
        <div class="container ab-wrap">
            <nav class="ab-back">
                <a href="{{ lroute('ai-benchmark') }}" class="s-meta"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>{{ __('leb_series.back') }}</a>
            </nav>
            {{-- The other track has a page of its own. `{!! !!}` is safe: markup of this file plus lang/ text, which is
                 repository content. --}}
            <p class="s-meta">{!! __('leb_series.sibling_note', ['link' => $link(__("{$copy}.sibling_label"), lroute("ai-benchmark.leb-$other"))]) !!}</p>

            <div class="ab-hero__grid">
                <div>
                    <span class="s-kicker">{{ __('leb_series.kicker') }}</span>
                    <h1 class="s-display ab-title">{{ __("{$copy}.heading") }} <span class="ab-accent nocolor">{{ __('leb_series.heading_accent') }}</span></h1>
                    <p class="s-lead ab-lead">{!! __("{$copy}.lead", ['benchmark' => $link(__('leb_series.benchmark_link'), lroute('ai-benchmark'))]) !!}</p>

                    <div class="ab-actions">
                        <a href="{{ $repo }}/blob/master/docs/new-series-design.md" target="_blank" rel="noopener" class="s-btn s-btn--lg"><i class="fa-brands fa-github" aria-hidden="true"></i> {{ __('leb_series.cta_design') }}</a>
                    </div>
                </div>
            </div>

            <div class="s-card ab-status">
                <p class="s-body"><strong>{{ __('leb_series.note_title') }}</strong> {{ __('leb_series.note_body') }}</p>
            </div>
        </div>
    </section>

    {{-- ═══ WHAT THE TRACK MEASURES ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('leb_series.measures_title') }}</h2>
            <p class="s-body">{{ __("{$copy}.measures_body") }}</p>
            @if(Lang::has("{$copy}.measures_items"))
                <ul class="ab-list">
                    @foreach(trans("{$copy}.measures_items") as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @endif
            @foreach(['measures_after', 'measures_base'] as $key)
                @if(Lang::has("{$copy}.$key"))
                    <p class="s-body">{{ __("{$copy}.$key") }}</p>
                @endif
            @endforeach
            <p class="s-body">{{ __('leb_series.sides_note') }}</p>
        </div>
    </section>

    {{-- ═══ WHERE THE SERIES STANDS ═══ --}}
    <section class="s-section s-section--tight s-bg-2">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('leb_series.state_title') }}</h2>
            <p class="s-body">{{ __('leb_series.state_body') }}</p>
        </div>
    </section>

    {{-- ═══ THE SYSTEMS ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('leb_series.systems_title') }}</h2>
            <p class="s-body ab-intro">{{ __('leb_series.systems_intro') }}</p>

            <div class="ab-table-wrap" tabindex="0" role="region" aria-label="{{ __('leb_series.systems_title') }}">
                <table class="ab-levels ab-levels--systems">
                    <thead>
                        <tr>
                            @foreach(['system', 'domain', 'stack', 'instance', 'status'] as $column)
                                <th scope="col">{{ __("leb_series.systems_col_$column") }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- One row per system. Today there is one. --}}
                        <tr>
                            <th scope="row">{{ __('leb_series.system_a_name') }}</th>
                            <td>{{ __('leb_series.system_a_domain') }}</td>
                            <td>{{ __('leb_series.system_a_stack') }}</td>
                            <td>{{ $instance }}</td>
                            <td>{{ __('leb_series.system_a_status') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ═══ HOW A CASE RUNS ═══ --}}
    <section class="s-section s-section--tight s-bg-2">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('leb_series.run_title') }}</h2>
            <p class="s-body">{{ __('leb_series.run_intro') }}</p>
            <ul class="ab-list">
                @foreach(trans('leb_series.run_items') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══ WHAT WILL BE PUBLISHED ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('leb_series.publish_title') }}</h2>
            <p class="s-body">{{ __('leb_series.publish_intro') }}</p>
            <ul class="ab-list">
                @foreach(trans('leb_series.publish_items') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══ THE ANSWER KEY, AND THE COMPARISON ═══ --}}
    <section class="s-section s-section--tight s-bg-2">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('leb_series.key_title') }}</h2>
            <p class="s-body">{{ __('leb_series.key_body') }}</p>

            <h2 class="s-h2 ab-h2">{{ __('leb_series.compare_title') }}</h2>
            <p class="s-body">{{ __('leb_series.compare_body') }}</p>
        </div>
    </section>

    {{-- ═══ READ MORE ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('leb_series.links_title') }}</h2>
            <ul class="ab-list">
                <li>{!! $link(__('leb_series.link_design'), "$repo/blob/master/docs/new-series-design.md", true) !!} <code>docs/new-series-design.md</code></li>
                <li>{!! $link(__('leb_series.link_system'), "$repo/tree/master/systems/300-A", true) !!} <code>systems/300-A/</code></li>
                <li>{!! $link(__('leb_series.link_instance'), "$repo/tree/master/instances/$instance", true) !!} <code>instances/{{ $instance }}/</code></li>
            </ul>
        </div>
    </section>

@endsection
