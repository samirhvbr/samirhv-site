@extends('layouts.app')

@php
    use App\Support\AiBenchmark;
    use Illuminate\Support\Str;

    // `$aggregate` is null until the synced results file carries one; the page is then a status page.
    $hasResult = ! empty($aggregate['agents'] ?? []);
    $allThree = $hasResult && collect($aggregate['agents'])->every(fn ($a) => ($a['runs_count'] ?? 0) >= 3);
    $weights = ['SEC' => 250, 'ARCH' => 200, 'BUG' => 150, 'PERF' => 150, 'CLN' => 100, 'COMP' => 100, 'EXPL' => 50];
@endphp

@section('title', __('leb_300.title'))
@section('description', __($hasResult ? 'leb_300.meta_description_result' : 'leb_300.meta_description'))

@if($hasResult)
    @push('styles')
        <link rel="stylesheet" href="{{ vasset('css/site/ai-benchmark.css') }}">
    @endpush
@endif

@section('content')

    <section class="s-section" style="padding-top:clamp(7rem,11vw,10rem); position:relative;">
        <div class="s-aura"></div>
        <div class="container s-prose" style="position:relative; z-index:1; max-width:{{ $hasResult ? '980px' : '820px' }};">

            <nav style="margin-bottom:30px;">
                <a href="{{ lroute('ai-benchmark') }}" class="s-meta" style="color:var(--s-accent-ink-2);"><i class="fa-solid fa-arrow-left" style="margin-right:7px;" aria-hidden="true"></i>{{ __('leb_300.back') }}</a>
            </nav>

            <header style="margin-bottom:24px;">
                <span class="s-kicker" style="margin-bottom:8px;">{{ __('leb_300.kicker') }}</span>
                <h1 class="s-display" style="font-size:clamp(1.9rem,4vw,2.7rem);">{{ __('leb_300.heading') }} <span style="color:var(--s-accent-ink-2);">{{ __($hasResult ? 'leb_300.heading_accent_result' : 'leb_300.heading_accent') }}</span></h1>
            </header>

            <div class="s-card s-card--pad" style="padding:22px; margin-bottom:32px;">
                @if($hasResult)
                    <p class="s-body" style="margin:0;"><strong style="color:var(--s-ink);">{{ __('leb_300.result_note_title') }}</strong> {{ __($allThree ? 'leb_300.result_note_body_three' : 'leb_300.result_note_body') }}</p>
                @else
                    <p class="s-body" style="margin:0;"><strong style="color:var(--s-ink);">{{ __('leb_300.note_title') }}</strong> {{ __('leb_300.note_body') }}</p>
                @endif
            </div>

            @if($hasResult)
                <h2 class="s-h3" style="margin:0 0 14px;">{{ __('leb_300.result_title') }}</h2>
                <ol class="ab-board" style="list-style:none; padding:0; margin:0 0 36px;">
                    @foreach($aggregate['agents'] as $i => $a)
                        @php
                            $model = AiBenchmark::modelOf($a['agent']);
                            $effort = $model
                                ? (($model['reasoning_effort'] ?? null) === 'default'
                                    ? __('ai_benchmark.effort_default')
                                    : __('ai_benchmark.effort', ['level' => AiBenchmark::effortLevel($model)]))
                                : null;
                        @endphp
                        <li class="s-card ab-row{{ $i === 0 ? ' ab-row--leader' : '' }}">
                            <span class="ab-row__rank" aria-label="#{{ $i + 1 }}">{{ $i + 1 }}</span>

                            <div class="ab-row__id">
                                <strong>{{ $model['name'] ?? $a['agent'] }}</strong>
                                <small>{{ $model ? $model['provider'].' · '.$effort : $a['agent'] }}</small>
                                <span class="ab-row__runs">{{ __('ai_benchmark.runs_label', ['count' => $a['runs_count']]) }}@if($a['runs_count'] < 3) · {{ __('ai_benchmark.unofficial') }}@endif</span>
                            </div>

                            <div class="ab-row__total">
                                <span class="ab-score">{{ $a['score'] }}</span>
                                <small>{{ __('ai_benchmark.out_of') }}</small>
                                <span class="ab-grade ab-grade--{{ Str::lower($a['grade']) }} ab-grade--pill">{{ __("ai_benchmark.grades.{$a['grade']}") }}</span>
                            </div>

                            <ul class="ab-row__cats">
                                @foreach($a['categories'] as $cat => $score)
                                    <li data-cat="{{ $cat }}">
                                        <span class="ab-cat__name">{{ __("ai_benchmark.categories.$cat") }}</span>
                                        <span class="ab-bar" aria-hidden="true"><i style="width: {{ AiBenchmark::percent($score, $weights[$cat]) }}%"></i></span>
                                        <span class="ab-cat__val">{{ $score }}<small>/{{ $weights[$cat] }}</small></span>
                                    </li>
                                @endforeach
                            </ul>

                            <p class="ab-row__meta s-meta">
                                @if(($a['cost_usd'] ?? null) !== null)
                                    <span class="ab-row__cost">{{ __('ai_benchmark.cost_run', ['cost' => number_format($a['cost_usd'], 2)]) }}</span>
                                @endif
                                @if(($a['wall_minutes'] ?? null) !== null)
                                    <span title="{{ __('leb_300.session_time_help') }}">{{ __('leb_300.session_time', ['min' => number_format($a['wall_minutes'], 1)]) }}</span>
                                @endif
                            </p>
                        </li>
                    @endforeach
                </ol>
            @endif

            <div class="s-body" style="color:var(--s-ink-2); line-height:1.8;">
                <h2 class="s-h3" style="margin:0 0 8px;">{{ __('leb_300.stands_title') }}</h2>
                <p>{{ __($hasResult ? 'leb_300.stands_body_result' : 'leb_300.stands_body') }}</p>

                @if($hasResult)
                    <h2 class="s-h3" style="margin:28px 0 8px;">{{ __('leb_300.caveats_title') }}</h2>
                    <ul>
                        <li>{{ __('leb_300.caveat_checkpoint') }}</li>
                        <li>{{ __('leb_300.caveat_matrix') }}</li>
                    </ul>
                @endif

                <h2 class="s-h3" style="margin:28px 0 8px;">{{ __($hasResult ? 'leb_300.publish_title_result' : 'leb_300.publish_title') }}</h2>
                <p>{{ __($hasResult ? 'leb_300.publish_body_result' : 'leb_300.publish_body') }}</p>

                <h2 class="s-h3" style="margin:28px 0 8px;">{{ __('leb_300.more_title') }}</h2>
                {{-- `{!! !!}` is safe here: the substitutions are markup of this file and the
                     text comes from lang/, which is repository content — no user input. --}}
                <p>{!! __('leb_300.more_body', [
                    'repo' => '<a href="https://github.com/samirhvbr/ai-benchmark" target="_blank" rel="noopener">'.e(__('leb_300.repo_link')).'</a>',
                    'benchmark' => '<a href="'.e(lroute('ai-benchmark')).'">'.e(__('leb_300.benchmark_link')).'</a>',
                    'leb100' => '<a href="'.e(lroute('ai-benchmark.leb-100')).'">'.e(__('leb_300.leb100_link')).'</a>',
                ]) !!}</p>
            </div>

        </div>
    </section>

@endsection
