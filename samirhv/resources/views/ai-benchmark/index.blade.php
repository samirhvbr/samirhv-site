@extends('layouts.app')

@section('title', __('ai_benchmark.title'))
@section('description', __('ai_benchmark.meta_description'))

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/ai-benchmark.css') }}">
@endpush

@php
    use App\Support\AiBenchmark;
    use Illuminate\Support\Str;

    $repo = 'https://github.com/samirhvbr/ai-benchmark';

    $categoryWeights = ['SEC' => 250, 'ARCH' => 200, 'BUG' => 150, 'PERF' => 150, 'CLN' => 100, 'COMP' => 100, 'EXPL' => 50];
    $grades = ['Platinum' => '900–1000', 'Gold' => '750–899', 'Silver' => '600–749', 'Bronze' => '400–599', 'Reprovada' => '< 400'];

    // The two instances. LEB-100-A publishes everything, so its card can say how many agents are ranked and who
    // leads; LEB-300-A is active and publishes an aggregate only, or nothing yet.
    $entries100 = $leb100['entries'] ?? [];
    $top100 = $entries100[0] ?? null;
    $agents300 = $aggregate['agents'] ?? [];
    $top300 = $agents300[0] ?? null;
    $model300 = $top300 ? AiBenchmark::modelOf($top300['agent']) : null;
@endphp

@section('content')

    {{-- ═══ HERO ═══ --}}
    <section class="s-section ab-hero">
        <div class="s-aura"></div>
        <div class="container ab-wrap">
            <nav class="ab-back">
                <a href="{{ lroute('home') }}" class="s-meta"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>{{ __('shell.home') }}</a>
            </nav>

            <span class="s-kicker">{{ __('ai_benchmark.kicker') }}</span>
            <h1 class="s-display ab-title">{{ __('ai_benchmark.heading') }} <span class="ab-accent nocolor">{{ __('ai_benchmark.heading_accent') }}</span></h1>
            <p class="s-lead ab-lead">{{ __('ai_benchmark.lead') }}</p>

            <div class="ab-actions">
                <a href="#levels" class="s-btn s-btn--lg">{{ __('ai_benchmark.cta_levels') }} <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                <a href="{{ $repo }}" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--lg"><i class="fa-brands fa-github" aria-hidden="true"></i> {{ __('ai_benchmark.cta_source') }}</a>
            </div>
        </div>
    </section>

    {{-- ═══ WHY ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap ab-why">
            <div>
                <h2 class="s-h2">{{ __('ai_benchmark.why_title') }}</h2>
                <p class="s-body">{{ __('ai_benchmark.why_body') }}</p>
            </div>
            <blockquote class="ab-quote">{{ __('ai_benchmark.why_quote') }}</blockquote>
        </div>
    </section>

    {{-- ═══ HOW A RUN WORKS ═══ --}}
    <section class="s-section s-section--tight s-bg-2">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.how_title') }}</h2>
            <ol class="ab-steps">
                @foreach([1 => 'fa-solid fa-box-archive', 2 => 'fa-solid fa-robot', 3 => 'fa-solid fa-vial-circle-check', 4 => 'fa-solid fa-scale-balanced'] as $n => $icon)
                    <li class="s-card ab-step">
                        <span class="ab-step__n">0{{ $n }}</span>
                        <i class="{{ $icon }} ab-step__icon" aria-hidden="true"></i>
                        <h3 class="s-h3">{{ __("ai_benchmark.step{$n}_title") }}</h3>
                        <p class="s-body s-muted">{{ __("ai_benchmark.step{$n}_desc") }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ═══ THE TWO LEVELS ═══ --}}
    <section class="s-section s-section--tight" id="levels">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.levels_title') }}</h2>
            <p class="s-body ab-intro">{{ __('ai_benchmark.levels_intro') }}</p>

            <div class="ab-table-wrap" tabindex="0" role="region" aria-label="{{ __('ai_benchmark.levels_title') }}">
                <table class="ab-levels">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('ai_benchmark.levels_col_dimension') }}</th>
                            <th scope="col">LEB-100-A</th>
                            <th scope="col">LEB-300-A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['size', 'tests', 'difficulty', 'run', 'key', 'published', 'status'] as $row)
                            <tr>
                                <th scope="row">{{ __("ai_benchmark.levels_row_$row") }}</th>
                                @if($row === 'run')
                                    <td>{{ $leb100 ? __('ai_benchmark.levels_run_100', ['mode' => $leb100['mode'], 'turns' => $leb100['turn_budget']]) : '—' }}</td>
                                    <td>{{ $aggregate ? __('ai_benchmark.levels_run_300', ['mode' => $aggregate['mode'], 'turns' => $aggregate['turn_budget']]) : __('ai_benchmark.card_pending') }}</td>
                                @elseif($row === 'status')
                                    <td>{{ __('ai_benchmark.levels_status_100') }}</td>
                                    <td>{{ __($top300 ? 'ai_benchmark.levels_status_300' : 'ai_benchmark.levels_status_300_pending') }}</td>
                                @else
                                    <td>{{ __("ai_benchmark.levels_{$row}_100") }}</td>
                                    <td>{{ __("ai_benchmark.levels_{$row}_300") }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="s-body ab-levels__note">{{ __('ai_benchmark.levels_same_scale') }}</p>
            <p class="s-body ab-levels__note">{{ __('ai_benchmark.levels_why') }}</p>
        </div>
    </section>

    {{-- ═══ THE INSTANCES ═══ --}}
    <section class="s-section s-section--tight s-bg-2" id="instances">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.instances_title') }}</h2>
            <p class="s-body ab-intro">{{ __('ai_benchmark.instances_intro') }}</p>

            <ul class="ab-cards">
                <li class="s-card ab-card">
                    <div class="ab-card__tags">
                        <span class="s-tag">{{ __('ai_benchmark.card_level', ['level' => 100]) }}</span>
                        <span class="s-tag">{{ __('ai_benchmark.card_reference') }}</span>
                    </div>
                    <h3 class="ab-card__title">LEB-100-A</h3>
                    <p class="s-body s-muted">{{ __('ai_benchmark.card_100_desc') }}</p>
                    <p class="ab-card__stat s-meta">
                        @if($top100)
                            {{ trans_choice('ai_benchmark.card_agents', count($entries100), ['count' => count($entries100)]) }}
                            · {{ __('ai_benchmark.card_top', ['score' => $top100['score'], 'name' => AiBenchmark::displayName($top100, $entries100)]) }}
                        @else
                            {{ __('ai_benchmark.card_no_results') }}
                        @endif
                    </p>
                    <a href="{{ lroute('ai-benchmark.leb-100') }}" class="s-btn">{{ __('ai_benchmark.card_open_100') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </li>

                <li class="s-card ab-card">
                    <div class="ab-card__tags">
                        <span class="s-tag">{{ __('ai_benchmark.card_level', ['level' => 300]) }}</span>
                        <span class="s-tag">{{ __($top300 ? 'ai_benchmark.card_pilot' : 'ai_benchmark.card_pending') }}</span>
                    </div>
                    <h3 class="ab-card__title">LEB-300-A</h3>
                    <p class="s-body s-muted">{{ __('ai_benchmark.card_300_desc') }}</p>
                    <p class="ab-card__stat s-meta">
                        @if($top300)
                            {{ trans_choice('ai_benchmark.card_agents', count($agents300), ['count' => count($agents300)]) }}
                            · {{ __('ai_benchmark.card_top', ['score' => $top300['score'], 'name' => $model300['name'] ?? $top300['agent']]) }}
                        @else
                            {{ __('ai_benchmark.card_no_results') }}
                        @endif
                    </p>
                    <a href="{{ lroute('ai-benchmark.leb-300') }}" class="s-btn">{{ __('ai_benchmark.card_open_300') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </li>
            </ul>
        </div>
    </section>

    {{-- ═══ SCORING ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap ab-scoring">
            <div>
                <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.scoring_title') }}</h2>
                <p class="s-body">{{ __('ai_benchmark.scoring_intro') }}</p>
                <p class="s-body">{{ __('ai_benchmark.comp_note') }}</p>
                <p class="s-body">{{ __('ai_benchmark.penalties_note') }}</p>
            </div>
            <div>
                <ul class="ab-weights" aria-label="{{ __('ai_benchmark.scoring_title') }}">
                    @foreach($categoryWeights as $cat => $weight)
                        <li>
                            <span class="ab-weights__name">{{ __("ai_benchmark.categories.$cat") }} <small>{{ $cat }}</small></span>
                            <span class="ab-bar" aria-hidden="true"><i style="width: {{ AiBenchmark::percent($weight, 250) }}%"></i></span>
                            <span class="ab-weights__val">{{ $weight }}</span>
                        </li>
                    @endforeach
                </ul>
                <h3 class="s-h3 ab-grades-title">{{ __('ai_benchmark.grades_title') }}</h3>
                <ul class="ab-grades">
                    @foreach($grades as $grade => $range)
                        <li class="ab-grade ab-grade--{{ Str::lower($grade) }}">
                            <strong>{{ __("ai_benchmark.grades.$grade") }}</strong>
                            <span>{{ $range }} · {{ __("ai_benchmark.grade_help.$grade") }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ═══ ISOLATION ═══ --}}
    <section class="s-section s-section--tight s-bg-2">
        <div class="container ab-wrap ab-isolation">
            <div>
                <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.isolation_title') }}</h2>
                <p class="s-body">{{ __('ai_benchmark.isolation_body') }}</p>
            </div>
            <div class="s-term" aria-hidden="true">
                <div class="s-term__bar">
                    <span class="s-term__dot s-term__dot--r"></span><span class="s-term__dot s-term__dot--y"></span><span class="s-term__dot s-term__dot--g"></span>
                    <span class="s-term__title">{{ __('ai_benchmark.term_title') }}</span>
                </div>
                <div class="s-term__body">
                    <span class="c-cmt"># {{ __('ai_benchmark.hosts_comment') }}</span><br>
                    <span class="c-key">127.0.0.1</span> github.com api.github.com<br>
                    <span class="c-key">127.0.0.1</span> codeload.github.com<br>
                    <span class="c-key">127.0.0.1</span> raw.githubusercontent.com<br>
                    <span class="c-key">::1</span> <span class="c-dim">({{ __('ai_benchmark.hosts_same') }})</span><br>
                    <br>
                    <span class="c-cmt"># {{ __('ai_benchmark.routes_comment') }}</span><br>
                    @foreach(['140.82.112.0/20', '143.55.64.0/20', '185.199.108.0/22', '192.30.252.0/22',
                        '2a0a:a440::/29', '2620:112:3000::/44', '2606:50c0::/32'] as $prefix)
                        <span class="c-key">blackhole</span> {{ $prefix }}<br>
                    @endforeach
                    <span class="c-dim">{{ __('ai_benchmark.routes_more') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ WHERE THE METHOD LIVES ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap ab-audit">
            <div>
                <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.method_title') }}</h2>
                <p class="s-body">{{ __('ai_benchmark.method_body') }}</p>
            </div>
            <div class="ab-actions">
                <a href="{{ $repo }}" target="_blank" rel="noopener" class="s-btn s-btn--lg"><i class="fa-brands fa-github" aria-hidden="true"></i> {{ __('ai_benchmark.cta_source') }}</a>
                <a href="{{ $repo }}/blob/master/SPEC.md" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--lg">{{ __('ai_benchmark.audit_method') }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>

@endsection
