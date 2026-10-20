@extends('layouts.app')

@php
    use App\Support\AiBenchmark;
    use Illuminate\Support\Facades\Lang;

    $repo = 'https://github.com/samirhvbr/ai-benchmark';

    // The copy of this track (`leb_default` or `leb_security`: what the track measures). What the two pages say alike is
    // `leb_series`. `$instance` and `$other` come from LebTrackController::TRACKS.
    $copy = "leb_{$track}";

    // The aggregate of this track's instance, once the synced results carry one (the series publishes its numbers as an aggregate only
    // while an instance is active). Without it the page is the status page it was.
    $aggregate = AiBenchmark::aggregate($instance);
    $agents = ($aggregate['track'] ?? null) === $track ? ($aggregate['agents'] ?? []) : [];
    $hasResult = ! empty($agents);
    $pt = app()->getLocale() === 'pt_BR';
    $num = fn (int|float $v, int $d = 0): string => number_format($v, $d, '.', '');   // the site's own convention: a dot in both languages, like every other page
    $cats = ['ARCH', 'BUG', 'PERF', 'CLN', 'COMP', 'EXPL'];
    $suffix = $hasResult ? '_results' : '';
    $link = fn (string $label, string $href, bool $external = false): string => '<a href="'.e($href).'"'.($external ? ' target="_blank" rel="noopener"' : '').'>'.e($label).'</a>';
@endphp

@section('title', __("{$copy}.title"))
@section('description', __("{$copy}.meta_description".($hasResult && Lang::has("{$copy}.meta_description_result") ? '_result' : '')))

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/ai-benchmark.css') }}">
@endpush

{{-- The vendor filter and the category order of the cases (only drawn once the page has results). --}}
@if($hasResult)
    @push('scripts')
        <script defer src="{{ vasset('js/site/ai-benchmark.js') }}"></script>
    @endpush
@endif

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
                <p class="s-body"><strong>{{ __('leb_series.note_title'.$suffix) }}</strong> {{ __('leb_series.note_body'.$suffix) }}</p>
            </div>
        </div>
    </section>

    {{-- ═══ THE CASES SO FAR (only once the synced results carry this track's aggregate) ═══
         One card per configuration: who it is, the score, the categories and the runs, the facts, the reading and the notes.
         Everything a reader needs to tell the numbers belong to that case is inside the card. --}}
    @if($hasResult)
        <section class="s-section s-section--tight">
            <div class="container ab-wrap">
                <h2 class="s-h2 ab-h2">{{ __('leb_series.results_title') }}</h2>
                <p class="s-body ab-intro">{{ __('leb_series.results_intro', ['minutes' => $aggregate['budget_minutes'] ?? '']) }}</p>

                {{-- Vendor filter and category order, as on the leaderboards (js/site/ai-benchmark.js). The filter only hides cards and the order only
                     moves them; ordering by a category adds the card's position in that category, counted over every configuration. Every card
                     stays on the page (data-ab-page="0": no pages). Rendered hidden, so without JavaScript the page is the plain list of cards. --}}
                @php $vendors = collect($agents)->map(fn ($a) => $a['model']['provider'] ?? null)->filter()->unique()->values(); @endphp
                <div class="ab-filter" data-ab-filter data-ab-page="0" hidden>
                    <div class="ab-filter__chips" role="group" aria-label="{{ __('ai_benchmark.filter_label') }}">
                        <span class="ab-filter__label">{{ __('ai_benchmark.filter_label') }}</span>
                        <button type="button" class="ab-chip" data-vendor="" aria-pressed="true">{{ __('ai_benchmark.filter_all') }}</button>
                        @foreach($vendors as $vendor)
                            <button type="button" class="ab-chip" data-vendor="{{ $vendor }}" aria-pressed="false">{{ $vendor }} <span class="ab-chip__n">{{ collect($agents)->where('model.provider', $vendor)->count() }}</span></button>
                        @endforeach
                    </div>
                    <div class="ab-filter__chips ab-sort" role="group" aria-label="{{ __('ai_benchmark.sort_label') }}">
                        <span class="ab-filter__label">{{ __('ai_benchmark.sort_label') }}</span>
                        <button type="button" class="ab-chip" data-sort="" aria-pressed="true">{{ __('ai_benchmark.sort_overall') }}</button>
                        @foreach($cats as $cat)
                            <button type="button" class="ab-chip" data-sort="{{ $cat }}" aria-pressed="false">{{ __("ai_benchmark.categories.$cat") }}</button>
                        @endforeach
                    </div>
                    <span class="ab-sort__tpl" hidden>{{ __('ai_benchmark.sort_position', ['pos' => ':pos', 'cat' => ':cat']) }}</span>
                    <span class="ab-filter__tpl" hidden>{{ __('leb_series.filter_shown', ['shown' => ':shown', 'total' => ':total']) }}</span>
                    <p class="ab-filter__status s-meta" aria-live="polite">{{ __('leb_series.filter_shown', ['shown' => count($agents), 'total' => count($agents)]) }}</p>
                </div>

                <div class="ab-board ab-board--cases">
                @foreach($agents as $agent)
                    @php
                        $model = $agent['model'] ?? [];
                        $effort = AiBenchmark::effortLevel($model);
                        $name = ($model['name'] ?? $agent['agent']).($effort !== '' ? " · {$effort}" : '');
                        $st = $agent['statistics'];
                        $quality = $st['quality'];
                        $expected = $st['expected_per_scheduled_attempt'];
                        $comment = $agent['comment'][$pt ? 'pt_BR' : 'en'] ?? null;
                    @endphp
                    <article class="ab-case" aria-labelledby="ab-case-{{ $loop->index }}" data-vendor="{{ $model['provider'] ?? '' }}" data-scores="{{ collect($cats)->map(fn ($c) => $c.':'.($agent['categories'][$c] ?? 0))->implode(' ') }}">
                        <header class="ab-case__head">
                            <h3 class="ab-case__name" id="ab-case-{{ $loop->index }}">{{ $name }}</h3>
                            <span class="ab-row__catpos" hidden></span>
                            <p class="ab-case__ids">{{ trans_choice('leb_series.results_case_ids', $st['scheduled'], ['instance' => $aggregate['instance'], 'version' => $aggregate['version'], 'track' => ucfirst($track), 'runs' => $st['scheduled']]) }}</p>
                        </header>

                        <dl class="ab-case__stats">
                            <div class="ab-case__stat">
                                <dt>{{ __('leb_series.results_col_estimate') }}</dt>
                                <dd class="ab-case__score">{{ $num($agent['score']) }}</dd>
                            </div>
                            <div class="ab-case__stat">
                                <dt>{{ __('leb_series.results_col_interval') }}</dt>
                                <dd>{{ $num($quality['low']) }} – {{ $num($quality['high']) }}</dd>
                            </div>
                            <div class="ab-case__stat">
                                <dt>{{ __('leb_series.results_col_grade') }}</dt>
                                <dd>{{ $agent['grade'] }}</dd>
                            </div>
                        </dl>

                        <div class="ab-table-wrap" tabindex="0" role="region" aria-label="{{ $name }}">
                            <table class="ab-levels ab-levels--results">
                                <thead>
                                    <tr>
                                        @foreach($cats as $cat)
                                            <th scope="col" data-cat="{{ $cat }}">{{ $cat }}</th>
                                        @endforeach
                                        <th scope="col">{{ __('leb_series.results_col_runs') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($cats as $cat)
                                            <td data-cat="{{ $cat }}">{{ $num($agent['categories'][$cat]) }}/{{ $num($agent['category_max'][$cat]) }}</td>
                                        @endforeach
                                        <td>{{ collect($agent['runs'])->pluck('total')->implode(' · ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="ab-case__body">
                            <ul class="ab-list">
                                <li>{{ trans_choice('leb_series.results_completion', $st['scheduled'], ['completed' => $st['completion']['completed'], 'scheduled' => $st['scheduled'], 'all' => $st['completion']['systems_all_completed'], 'systems' => $st['completion']['systems']]) }}</li>
                                <li>{{ __('leb_series.results_expected', ['mean' => $num($expected['mean'], 1), 'low' => $num($expected['low'], 1), 'high' => $num($expected['high'], 1)]) }}</li>
                                <li>{{ __('leb_series.results_outcomes', ['blocked' => $st['outcomes']['blocked'], 'start' => $st['outcomes']['blocked_at_the_start'], 'work' => $st['outcomes']['blocked_during_the_work'], 'budget' => $st['outcomes']['budget_exhausted'], 'switched' => $st['outcomes']['switched'], 'lab' => $st['lab_failures_rerun']]) }}</li>
                                @if(isset($agent['cost_usd'], $agent['wall_minutes']))
                                    <li>{{ __('leb_series.results_cost', ['cost' => $num($agent['cost_usd'], 2), 'minutes' => $num($agent['wall_minutes'])]) }}</li>
                                @endif
                            </ul>
                            @if($comment)
                                <p class="s-body">{{ $comment }}</p>
                            @endif
                            <p class="s-body ab-case__fine">{{ __('leb_series.results_method') }}</p>
                            @if(isset($aggregate['notes']))
                                <p class="s-body ab-case__fine">{{ $aggregate['notes'][$pt ? 'pt_BR' : 'en'] ?? $aggregate['notes']['en'] }}</p>
                            @endif
                            <p class="s-meta ab-case__hash">{{ __('leb_series.results_key', ['matrix' => $aggregate['matrix_sha256'], 'package' => $agent['package_sha256'] ?? $aggregate['package_sha256'] ?? '']) }}</p>
                        </div>
                    </article>
                @endforeach
                </div>
            </div>
        </section>
    @endif

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
            <p class="s-body">{{ __('leb_series.state_body'.$suffix) }}</p>
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
                            <td>{{ __('leb_series.system_a_status'.$suffix) }}</td>
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
