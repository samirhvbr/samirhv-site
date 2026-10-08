@extends('layouts.app')

@section('title', __('ai_benchmark.title'))
@section('description', __('ai_benchmark.meta_description'))

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/ai-benchmark.css') }}">
@endpush

@push('scripts')
    <script defer src="{{ vasset('js/site/ai-benchmark.js') }}"></script>
@endpush

@php
    use App\Support\AiBenchmark;
    use Illuminate\Support\Str;

    $repo = 'https://github.com/samirhvbr/ai-benchmark';

    // The caveats carry **bold** lead-ins. Escaped first, so the only markup
    // that reaches the page is the <strong> this produces; the text itself is
    // repository content from lang/, never user input.
    $md = fn (string $text) => Str::inlineMarkdown($text, ['html_input' => 'escape', 'allow_unsafe_links' => false]);

    $categoryWeights = ['SEC' => 250, 'ARCH' => 200, 'BUG' => 150, 'PERF' => 150, 'CLN' => 100, 'COMP' => 100, 'EXPL' => 50];
    $grades = ['Platinum' => '900–1000', 'Gold' => '750–899', 'Silver' => '600–749', 'Bronze' => '400–599', 'Reprovada' => '< 400'];
@endphp

@section('content')

    {{-- ═══ HERO ═══ --}}
    <section class="s-section ab-hero">
        <div class="s-aura"></div>
        <div class="container ab-wrap">
            <nav class="ab-back">
                <a href="{{ lroute('home') }}" class="s-meta"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>{{ __('shell.home') }}</a>
            </nav>
            {{-- The next level, in preparation, has a page of its own. `{!! !!}` is safe: markup of this
                 file plus lang/ text, which is repository content. --}}
            <p class="s-meta">{!! __('ai_benchmark.leb300_note', ['link' => '<a href="'.e(lroute('ai-benchmark.leb-300')).'">'.e(__('ai_benchmark.leb300_link')).'</a>']) !!}</p>

            @php
                // The first instance's leaders, for a reader who stops at the fold. The full
                // leaderboard, with runs and caveats, is further down.
                $topInst = $instances[0] ?? null;
                $top = $topInst ? array_slice($topInst['entries'], 0, AiBenchmark::HERO_TOP) : [];
            @endphp
            <div class="ab-hero__grid">
                <div>
                    <span class="s-kicker">{{ __('ai_benchmark.kicker') }}</span>
                    <h1 class="s-display ab-title">{{ __('ai_benchmark.heading') }} <span class="ab-accent nocolor">{{ __('ai_benchmark.heading_accent') }}</span></h1>
                    <p class="s-lead ab-lead">{{ __('ai_benchmark.lead') }}</p>

                    <div class="ab-actions">
                        <a href="#results" class="s-btn s-btn--lg">{{ __('ai_benchmark.cta_results') }} <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                        <a href="{{ $repo }}" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--lg"><i class="fa-brands fa-github" aria-hidden="true"></i> {{ __('ai_benchmark.cta_source') }}</a>
                    </div>
                </div>

                @if(count($top))
                    <aside class="s-card ab-top" aria-labelledby="ab-top-title">
                        <div class="ab-top__head">
                            <h2 class="ab-top__title" id="ab-top-title">{{ __('ai_benchmark.top_title', ['n' => count($top)]) }}</h2>
                            <span class="ab-top__cap">{{ $topInst['id'] }}</span>
                        </div>
                        <ol class="ab-top__list">
                            @foreach($top as $e)
                                @php $grade = AiBenchmark::representativeRun($e)['grade']; @endphp
                                <li>
                                    <span class="ab-top__rank">{{ $e['rank'] }}</span>
                                    <span class="ab-top__name">{{ AiBenchmark::displayName($e, $topInst['entries']) }}</span>
                                    <span class="ab-top__score">{{ $e['score'] }}</span>
                                    <span class="ab-grade ab-grade--{{ Str::lower($grade) }} ab-grade--pill">{{ __("ai_benchmark.grades.$grade") }}</span>
                                </li>
                            @endforeach
                        </ol>
                        <p class="ab-top__note">{{ __('ai_benchmark.top_note', ['total' => count($topInst['entries'])]) }}</p>
                        <a href="#results" class="ab-top__link">{{ __('ai_benchmark.top_link') }} <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                    </aside>
                @endif
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

    {{-- ═══ RESULTS ═══ --}}
    <section class="s-section" id="results">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.results_title') }}</h2>
            <p class="s-body ab-intro">{{ __('ai_benchmark.results_intro') }}</p>

            @forelse($instances as $inst)
                @php
                    $instKey = "ai_benchmark.instances.{$inst['id']}";
                    $entries = $inst['entries'];
                @endphp
                <article class="ab-instance" aria-labelledby="inst-{{ $inst['id'] }}">
                    <header class="ab-instance__head">
                        <h3 class="ab-instance__title" id="inst-{{ $inst['id'] }}">{{ __('ai_benchmark.instance_title', ['id' => $inst['id'].' v'.$inst['version'], 'name' => __("$instKey.name")]) }}</h3>
                        <p class="s-body s-muted">{{ __("$instKey.desc") }}</p>
                        <div class="ab-facts">
                            <span class="s-tag">{{ __('ai_benchmark.facts_mode', ['mode' => $inst['mode'], 'turns' => $inst['turn_budget']]) }}</span>
                            <span class="s-tag">{{ __('ai_benchmark.facts_edition', ['edition' => $inst['edition']]) }}</span>
                            <span class="s-tag">{{ __('ai_benchmark.facts_evaluated', ['date' => $inst['evaluated_on']]) }}</span>
                            <span class="s-tag" title="{{ $inst['matrix_sha256'] }}">{{ __('ai_benchmark.facts_matrix', ['sha' => substr($inst['matrix_sha256'], 0, 12).'…']) }}</span>
                        </div>
                    </header>

                    {{-- Vendor filter and category order. The filter only hides rows and the order
                         only moves them: the number on each card stays its rank in the full
                         leaderboard, and a category order adds the card's position in that category.
                         Rendered hidden and revealed by js/site/ai-benchmark.js, so without
                         JavaScript the page is the plain leaderboard. The templates travel as hidden
                         text, as on shvia.org, whose language-parity check compares attributes. --}}
                    @php $vendors = collect($entries)->pluck('model.provider')->unique()->values(); @endphp
                    <div class="ab-filter" data-ab-filter hidden>
                        <div class="ab-filter__chips" role="group" aria-label="{{ __('ai_benchmark.filter_label') }}">
                            <span class="ab-filter__label">{{ __('ai_benchmark.filter_label') }}</span>
                            <button type="button" class="ab-chip" data-vendor="" aria-pressed="true">{{ __('ai_benchmark.filter_all') }}</button>
                            @foreach($vendors as $vendor)
                                <button type="button" class="ab-chip" data-vendor="{{ $vendor }}" aria-pressed="false">{{ $vendor }} <span class="ab-chip__n">{{ collect($entries)->where('model.provider', $vendor)->count() }}</span></button>
                            @endforeach
                        </div>
                        <div class="ab-filter__chips ab-sort" role="group" aria-label="{{ __('ai_benchmark.sort_label') }}">
                            <span class="ab-filter__label">{{ __('ai_benchmark.sort_label') }}</span>
                            <button type="button" class="ab-chip" data-sort="" aria-pressed="true">{{ __('ai_benchmark.sort_overall') }}</button>
                            @foreach(array_keys(AiBenchmark::representativeRun($entries[0])['categories']) as $cat)
                                <button type="button" class="ab-chip" data-sort="{{ $cat }}" aria-pressed="false">{{ __("ai_benchmark.categories.$cat") }}</button>
                            @endforeach
                        </div>
                        <span class="ab-filter__tpl" hidden>{{ __('ai_benchmark.filter_shown', ['shown' => ':shown', 'total' => ':total']) }}</span>
                        <span class="ab-pager__tpl" hidden>{{ __('ai_benchmark.page_of', ['n' => ':n', 'total' => ':total']) }}</span>
                        <span class="ab-pager__prev" hidden>{{ __('ai_benchmark.page_prev') }}</span>
                        <span class="ab-pager__next" hidden>{{ __('ai_benchmark.page_next') }}</span>
                        <span class="ab-sort__tpl" hidden>{{ __('ai_benchmark.sort_position', ['pos' => ':pos', 'cat' => ':cat']) }}</span>
                        <p class="ab-filter__status s-meta" aria-live="polite">{{ __('ai_benchmark.filter_shown', ['shown' => count($entries), 'total' => count($entries)]) }}</p>
                    </div>

                    {{-- The leaderboard. An ordered list, because the order IS the content. --}}
                    <ol class="ab-board">
                        @foreach($entries as $e)
                            @php
                                $run = AiBenchmark::representativeRun($e);
                                $penalty = collect($run['penalties'])->sum('deduction');
                            @endphp
                            <li class="s-card ab-row{{ $e['rank'] === 1 ? ' ab-row--leader' : '' }}{{ $loop->even ? ' ab-row--alt' : '' }}" data-vendor="{{ $e['model']['provider'] }}" data-scores="{{ collect($run['categories'])->map(fn ($c, $k) => $k.':'.$c['score'])->implode(' ') }}">
                                <span class="ab-row__rank" aria-label="#{{ $e['rank'] }}">{{ $e['rank'] }}</span>

                                <div class="ab-row__id">
                                    <strong data-rank="#{{ $e['rank'] }} · ">{{ $e['model']['name'] }}</strong>
                                    {{-- "default" is a model with no effort setting at all, not a level
                                         someone chose — it gets words, not the raw value. --}}
                                    <small>{{ $e['model']['provider'] }} · {{ ($e['model']['reasoning_effort'] ?? null) === 'default'
                                        ? __('ai_benchmark.effort_default').(($e['model']['client_mode'] ?? null) ? " ({$e['model']['client_mode']})" : '')
                                        : __('ai_benchmark.effort', ['level' => AiBenchmark::effortLevel($e['model'])]) }}</small>
                                    <span class="ab-row__runs">
                                        {{ __('ai_benchmark.runs_label', ['count' => $e['runs_count']]) }}@if($e['runs_count'] > 1) ({{ implode(' · ', $e['totals']) }})@endif @unless($e['official']) · {{ __('ai_benchmark.unofficial') }}@endunless
                                    </span>
                                    {{-- Could this model have trained on the public answer key? From the
                                         cutoff its provider publishes; only a "maybe" is worth a tag. --}}
                                    <span class="ab-row__catpos" hidden></span>
                                    @if(in_array($e['key_exposure'] ?? null, ['after', 'unknown'], true))
                                        <span class="ab-row__exposure" title="{{ __('ai_benchmark.exposure_help', ['date' => $inst['key_published_on']]) }}">{{ __('ai_benchmark.exposure_'.$e['key_exposure']) }}</span>
                                    @endif
                                </div>

                                <div class="ab-row__total">
                                    <span class="ab-score">{{ $e['score'] }}</span>
                                    <small>{{ __('ai_benchmark.out_of') }}</small>
                                    <span class="ab-grade ab-grade--{{ Str::lower($run['grade']) }} ab-grade--pill">{{ __("ai_benchmark.grades.{$run['grade']}") }}</span>
                                </div>

                                <ul class="ab-row__cats">
                                    @foreach($run['categories'] as $cat => $c)
                                        <li data-cat="{{ $cat }}">
                                            <span class="ab-cat__name">{{ __("ai_benchmark.categories.$cat") }}</span>
                                            <span class="ab-bar" aria-hidden="true"><i style="width: {{ AiBenchmark::percent($c['score'], $c['max']) }}%"></i></span>
                                            <span class="ab-cat__val">{{ $c['score'] }}<small>/{{ $c['max'] }}</small></span>
                                        </li>
                                    @endforeach
                                </ul>

                                <p class="ab-row__meta s-meta">
                                    <span>{{ __('ai_benchmark.penalties') }}: {{ $penalty === 0 ? __('ai_benchmark.none') : $penalty }}</span>
                                    <span title="{{ __('ai_benchmark.discovery_help') }}">{{ __('ai_benchmark.discovery') }} {{ number_format($e['discovery_index'], 1) }}</span>
                                    @if($e['brier'] !== null)
                                        <span title="{{ __('ai_benchmark.brier_help') }}">{{ __('ai_benchmark.brier') }} {{ number_format($e['brier'], 3) }}</span>
                                    @endif
                                    @php $costs = collect($e['runs'])->pluck('cost_time.usd_estimate')->filter(fn ($v) => $v !== null); @endphp
                                    @if($costs->isNotEmpty())
                                        <span class="ab-row__cost" title="{{ __('ai_benchmark.cost_help', ['n' => $costs->count()]) }}">{{ __('ai_benchmark.cost_run', ['cost' => number_format($costs->avg(), 2)]) }}</span>
                                    @endif
                                    <a href="{{ $run['scorecard_url'] }}" target="_blank" rel="noopener">{{ __('ai_benchmark.scorecard') }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                                </p>

                                {{-- Opened by its summary or by a click anywhere on the card. The comment is
                                     written (comments.json in the benchmark repo); everything below it is
                                     read from the runs, so it never disagrees with the scorecards. --}}
                                @php
                                    $comment = $e['comment'][app()->getLocale() === 'pt_BR' ? 'pt_BR' : 'en'] ?? null;
                                    $neverFixed = collect($inst['flaws'])->pluck('id')->filter(fn ($id) => collect($e['runs'])->every(fn ($r) => ! ($r['flaws'][$id]['fixed'] ?? false)));
                                    $catNames = fn ($cats) => $cats->keys()->map(fn ($c) => __("ai_benchmark.categories.$c"))->implode(', ');
                                    $full = collect($run['categories'])->filter(fn ($c) => $c['score'] === $c['max']);
                                    $zero = collect($run['categories'])->filter(fn ($c) => $c['score'] === 0);
                                @endphp
                                <details class="ab-row__more">
                                    <summary>{{ __('ai_benchmark.more_open') }}</summary>
                                    @if($comment)
                                        <p class="ab-more__comment">{{ $comment }}</p>
                                        <p class="ab-more__note s-meta">{{ __('ai_benchmark.more_note') }}</p>
                                    @endif
                                    <dl class="ab-more__facts">
                                        @if($full->isNotEmpty())
                                            <dt>{{ __('ai_benchmark.more_full') }}</dt><dd>{{ $catNames($full) }}</dd>
                                        @endif
                                        @if($zero->isNotEmpty())
                                            <dt>{{ __('ai_benchmark.more_zero') }}</dt><dd>{{ $catNames($zero) }}</dd>
                                        @endif
                                        <dt>{{ __('ai_benchmark.more_never_fixed') }}</dt>
                                        <dd>{{ $neverFixed->isEmpty() ? __('ai_benchmark.more_all_fixed') : $neverFixed->map(fn ($id) => __("ai_benchmark.flaws.$id"))->implode('; ') }}</dd>
                                    </dl>
                                    <h5 class="ab-more__title">{{ __('ai_benchmark.more_runs') }}</h5>
                                    <ul class="ab-more__runs">
                                        @foreach($e['runs'] as $r)
                                            <li>
                                                <strong>{{ __('ai_benchmark.more_run', ['n' => $r['run']]) }} · {{ $r['total'] }}</strong>
                                                <span>{{ __('ai_benchmark.more_fixed', ['fixed' => collect($r['flaws'])->where('fixed', true)->count(), 'total' => count($r['flaws'])]) }}</span>
                                                <span>{{ trans_choice('ai_benchmark.more_fp', $r['false_positives'], ['count' => $r['false_positives']]) }}</span>
                                                <span>{{ trans_choice('ai_benchmark.more_comp', count($r['comp_violations']), ['count' => count($r['comp_violations'])]) }}</span>
                                                <span>{{ __('ai_benchmark.more_checks', ['passed' => $r['characterization']['passed'], 'total' => $r['characterization']['passed'] + $r['characterization']['failed']]) }}</span>
                                                @if(($r['wall_minutes'] ?? null) !== null)
                                                    @php $m = (int) round($r['wall_minutes']); @endphp
                                                    <span title="{{ __('ai_benchmark.more_time_help') }}">{{ $m < 60 ? __('ai_benchmark.more_time_min', ['m' => $m]) : ($m % 60 === 0 ? __('ai_benchmark.more_time_h_only', ['h' => intdiv($m, 60)]) : __('ai_benchmark.more_time_h', ['h' => intdiv($m, 60), 'm' => $m % 60])) }}</span>
                                                @endif
                                                @if(($r['cost_time']['usd_estimate'] ?? null) !== null)
                                                    <span>{{ __('ai_benchmark.more_cost', ['cost' => number_format($r['cost_time']['usd_estimate'], 2)]) }}</span>
                                                @endif
                                                <a href="{{ $r['scorecard_url'] }}" target="_blank" rel="noopener">{{ __('ai_benchmark.scorecard') }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            </li>
                        @endforeach
                    </ol>

                    {{-- ── Flaw by flaw ── --}}
                    {{-- Only the top ten: one column per agent stops being readable past that,
                         and every agent's flaw-by-flaw result is in its scorecard anyway. --}}
                    @php $flawEntries = array_slice($entries, 0, AiBenchmark::FLAW_TABLE_AGENTS); @endphp
                    <h4 class="s-h3 ab-subtitle">{{ __('ai_benchmark.flaws_title') }}</h4>
                    <p class="s-body s-muted">{{ __('ai_benchmark.flaws_intro') }}</p>
                    @if(count($entries) > count($flawEntries))
                        <p class="s-body s-muted">{{ __('ai_benchmark.flaws_top', ['shown' => count($flawEntries), 'total' => count($entries)]) }}</p>
                    @endif
                    <ul class="ab-legend">
                        <li><span class="ab-dot ab-dot--fixed" aria-hidden="true"></span>{{ __('ai_benchmark.legend_fixed') }}</li>
                        <li><span class="ab-dot ab-dot--found" aria-hidden="true"></span>{{ __('ai_benchmark.legend_found') }}</li>
                        <li><span class="ab-dot ab-dot--missed" aria-hidden="true"></span>{{ __('ai_benchmark.legend_missed') }}</li>
                    </ul>

                    <div class="ab-table-wrap" tabindex="0" role="region" aria-label="{{ __('ai_benchmark.flaws_title') }}">
                        <table class="ab-flaws">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('ai_benchmark.col_flaw') }}</th>
                                    @foreach($flawEntries as $e)
                                        <th scope="col">{{ AiBenchmark::displayName($e, $entries) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inst['flaws'] as $flaw)
                                    <tr>
                                        <th scope="row">
                                            <span class="ab-flaw__name">{{ __("ai_benchmark.flaws.{$flaw['id']}") }}</span>
                                            <span class="ab-flaw__meta">{{ $flaw['id'] }} · {{ __("ai_benchmark.severity.{$flaw['severity']}") }} · {{ __("ai_benchmark.difficulty.{$flaw['difficulty']}") }}</span>
                                        </th>
                                        @foreach($flawEntries as $e)
                                            @php
                                                $f = AiBenchmark::representativeRun($e)['flaws'][$flaw['id']] ?? null;
                                                $state = $f === null ? 'missed' : ($f['fixed'] ? 'fixed' : ($f['found'] ? 'found' : 'missed'));
                                            @endphp
                                            <td>
                                                <span class="ab-dot ab-dot--{{ $state }}" aria-hidden="true"></span>
                                                <span class="ab-flaw__pts">{{ $f['earned'] ?? 0 }}/{{ $flaw['points_possible'] }}</span>
                                                <span class="visually-hidden">{{ __("ai_benchmark.legend_$state") }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @php $highlights = trans('ai_benchmark.highlights'); @endphp
                    @if(is_array($highlights) && !empty($highlights[$inst['id']] ?? []))
                        <h4 class="s-h3 ab-subtitle">{{ __('ai_benchmark.highlights_title') }}</h4>
                        <ul class="ab-highlights">
                            @foreach($highlights[$inst['id']] as $line)
                                <li class="s-body">{!! $md($line) !!}</li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- A hand-written reading across agents, for an instance that has one:
                         what a single total hides. Same source and escaping as the highlights. --}}
                    @php $reading = trans('ai_benchmark.readings'); $reading = is_array($reading) ? ($reading[$inst['id']] ?? null) : null; @endphp
                    @if(is_array($reading) && !empty($reading['points'] ?? []))
                        <div class="ab-reading">
                            <h4 class="s-h3 ab-subtitle">{{ $reading['title'] }}</h4>
                            <p class="s-body ab-reading__lead">{!! $md($reading['lead']) !!}</p>
                            <ol class="ab-reading__list">
                                @foreach($reading['points'] as $line)
                                    <li class="s-body">{!! $md($line) !!}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </article>
            @empty
                <p class="s-body s-muted">{{ __('ai_benchmark.results_empty') }}</p>
            @endforelse
        </div>
    </section>

    {{-- ═══ CAVEATS ═══ --}}
    <section class="s-section s-section--tight s-bg-2">
        <div class="container ab-wrap">
            <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.caveats_title') }}</h2>
            <ul class="ab-caveats">
                @php
                    // One of the two run caveats applies: all single runs, or some agent with more.
                    $multiRun = collect($instances)->flatMap(fn ($i) => $i['entries'])->contains(fn ($e) => $e['runs_count'] > 1);
                @endphp
                @foreach(trans('ai_benchmark.caveats') as $key => $text)
                    @continue($key === ($multiRun ? 'single_run' : 'multi_run'))
                    <li class="s-body">
                        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                        <span>{!! $md($text) !!}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══ AUDIT ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap ab-audit">
            <div>
                <h2 class="s-h2 ab-h2">{{ __('ai_benchmark.audit_title') }}</h2>
                <p class="s-body">{{ __('ai_benchmark.audit_body') }}</p>
            </div>
            <div class="ab-actions">
                <a href="{{ $repo }}/tree/master/results" target="_blank" rel="noopener" class="s-btn s-btn--lg"><i class="fa-brands fa-github" aria-hidden="true"></i> {{ __('ai_benchmark.audit_results') }}</a>
                <a href="{{ $repo }}/blob/master/SPEC.md" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--lg">{{ __('ai_benchmark.audit_method') }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
            </div>
            {{-- Byte-identical copies of ai-benchmark's results/*.csv, brought by tools/sync-ai-benchmark-results.sh. --}}
            <div class="ab-actions ab-downloads">
                <a href="{{ asset('data/ai-benchmark/runs.csv') }}" download class="s-btn s-btn--ghost"><i class="fa-solid fa-file-csv" aria-hidden="true"></i> {{ __('ai_benchmark.download_runs') }}</a>
                <a href="{{ asset('data/ai-benchmark/flaws.csv') }}" download class="s-btn s-btn--ghost"><i class="fa-solid fa-file-csv" aria-hidden="true"></i> {{ __('ai_benchmark.download_flaws') }}</a>
                <a href="{{ $repo }}/blob/master/results/CSV.md" target="_blank" rel="noopener" class="ab-downloads__dict">{{ __('ai_benchmark.download_dictionary') }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>

@endsection
