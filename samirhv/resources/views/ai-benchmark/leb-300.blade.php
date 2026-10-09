@extends('layouts.app')

@php
    use App\Http\Controllers\Leb300Controller;
    use App\Support\AiBenchmark;
    use Illuminate\Support\Str;

    $repo = 'https://github.com/samirhvbr/ai-benchmark';

    // `$aggregate` is null until the synced results file carries one; the page is then a status page: the same
    // hero and the same closing section, with a note in place of the leaders, the board and the caveats.
    $agents = $aggregate['agents'] ?? [];
    $hasResult = ! empty($agents);
    $allThree = $hasResult && collect($agents)->every(fn ($a) => ($a['runs_count'] ?? 0) >= 3);
    $instance = $aggregate['instance'] ?? Leb300Controller::INSTANCE;
    $weights = ['SEC' => 250, 'ARCH' => 200, 'BUG' => 150, 'PERF' => 150, 'CLN' => 100, 'COMP' => 100, 'EXPL' => 50];

    // An aggregate names its agents by id; the model (name, provider, effort) comes from the instances that
    // publish per-flaw results, where the same agent has run before. An agent unknown there keeps its id.
    $models = collect($agents)->map(fn ($a) => AiBenchmark::modelOf($a['agent']) ?? ['name' => $a['agent'], 'provider' => $a['agent']])->all();
    $entries = array_map(fn ($m) => ['model' => $m], $models);
    $vendors = collect($models)->pluck('provider')->unique()->values();
    $top = array_slice($agents, 0, AiBenchmark::HERO_TOP);

    // "default" is a model with no effort setting at all, not a level someone chose: it gets words, not the raw
    // value. A model with no recorded effort gets nothing after its provider.
    $effortOf = function (array $m): ?string {
        if (($m['reasoning_effort'] ?? null) === 'default') {
            return __('ai_benchmark.effort_default').(($m['client_mode'] ?? null) ? " ({$m['client_mode']})" : '');
        }
        $level = AiBenchmark::effortLevel($m);

        return $level === '' ? null : __('ai_benchmark.effort', ['level' => $level]);
    };
@endphp

@section('title', __('leb_300.title'))
@section('description', __($hasResult ? 'leb_300.meta_description_result' : 'leb_300.meta_description'))

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/ai-benchmark.css') }}">
@endpush

@if($hasResult)
    {{-- The vendor filter, the category order and the card that opens on a click, as on the LEB-100 page. --}}
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
                <a href="{{ lroute('ai-benchmark') }}" class="s-meta"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>{{ __('leb_300.back') }}</a>
            </nav>
            {{-- The first level has a page of its own, as the LEB-100 page points here. `{!! !!}` is safe: markup of
                 this file plus lang/ text, which is repository content. --}}
            <p class="s-meta">{!! __('leb_300.leb100_note', ['link' => '<a href="'.e(lroute('ai-benchmark.leb-100')).'">'.e(__('leb_300.leb100_note_link')).'</a>']) !!}</p>

            <div class="ab-hero__grid">
                <div>
                    <span class="s-kicker">{{ __('leb_300.kicker') }}</span>
                    <h1 class="s-display ab-title">{{ __('leb_300.heading') }} <span class="ab-accent nocolor">{{ __($hasResult ? 'leb_300.heading_accent_result' : 'leb_300.heading_accent') }}</span></h1>
                    <p class="s-lead ab-lead">{!! __('leb_300.lead', ['benchmark' => '<a href="'.e(lroute('ai-benchmark')).'">'.e(__('leb_300.benchmark_link')).'</a>']) !!}</p>

                    <div class="ab-actions">
                        @if($hasResult)
                            <a href="#results" class="s-btn s-btn--lg">{{ __('ai_benchmark.cta_results') }} <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                        @endif
                        <a href="{{ $repo }}" target="_blank" rel="noopener" class="s-btn{{ $hasResult ? ' s-btn--ghost' : '' }} s-btn--lg"><i class="fa-brands fa-github" aria-hidden="true"></i> {{ __('ai_benchmark.cta_source') }}</a>
                    </div>
                </div>

                @if(count($top))
                    {{-- The leaders, for a reader who stops at the fold. The full board, with runs and caveats, is further down. --}}
                    <aside class="s-card ab-top" aria-labelledby="ab-top-title">
                        <div class="ab-top__head">
                            <h2 class="ab-top__title" id="ab-top-title">{{ __('ai_benchmark.top_title', ['n' => count($top)]) }}</h2>
                            <span class="ab-top__cap">{{ $instance }}</span>
                        </div>
                        <ol class="ab-top__list">
                            @foreach($top as $i => $a)
                                <li>
                                    <span class="ab-top__rank">{{ $i + 1 }}</span>
                                    <span class="ab-top__name">{{ AiBenchmark::displayName($entries[$i], $entries) }}</span>
                                    <span class="ab-top__score">{{ $a['score'] }}</span>
                                    <span class="ab-grade ab-grade--{{ Str::lower($a['grade']) }} ab-grade--pill">{{ __("ai_benchmark.grades.{$a['grade']}") }}</span>
                                </li>
                            @endforeach
                        </ol>
                        <p class="ab-top__note">{{ __('ai_benchmark.top_note', ['total' => count($agents)]) }}</p>
                        <a href="#results" class="ab-top__link">{{ __('ai_benchmark.top_link') }} <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                    </aside>
                @endif
            </div>

            @unless($hasResult)
                <div class="s-card ab-status">
                    <p class="s-body"><strong>{{ __('leb_300.note_title') }}</strong> {{ __('leb_300.note_body') }}</p>
                </div>
            @endunless
        </div>
    </section>

    @if($hasResult)
        {{-- ═══ RESULTS ═══ --}}
        <section class="s-section" id="results">
            <div class="container ab-wrap">
                <h2 class="s-h2 ab-h2">{{ __('leb_300.result_title') }}</h2>
                <p class="s-body ab-intro">{{ __('leb_300.results_intro') }}</p>

                <article class="ab-instance" aria-labelledby="inst-{{ $instance }}">
                    <header class="ab-instance__head">
                        <h3 class="ab-instance__title" id="inst-{{ $instance }}">{{ __('ai_benchmark.instance_title', ['id' => $instance.(isset($aggregate['version']) ? ' v'.$aggregate['version'] : ''), 'name' => __('leb_300.instance_name')]) }}</h3>
                        <p class="s-body s-muted"><strong>{{ __('leb_300.result_note_title') }}</strong> {{ __($allThree ? 'leb_300.result_note_body_three' : 'leb_300.result_note_body') }}</p>
                        {{-- The facts the aggregate carries. An ACTIVE instance has no evaluation date and no public
                             answer key: the key stays private, and the matrix hash is what proves which key scored it. --}}
                        <div class="ab-facts">
                            @if(isset($aggregate['mode'], $aggregate['turn_budget']))
                                <span class="s-tag">{{ __('ai_benchmark.facts_mode', ['mode' => $aggregate['mode'], 'turns' => $aggregate['turn_budget']]) }}</span>
                            @endif
                            @if(isset($aggregate['edition']))
                                <span class="s-tag">{{ __('ai_benchmark.facts_edition', ['edition' => $aggregate['edition']]) }}</span>
                            @endif
                            <span class="s-tag">{{ __('leb_300.facts_key') }}</span>
                            @if(isset($aggregate['matrix_sha256']))
                                <span class="s-tag" title="{{ $aggregate['matrix_sha256'] }}">{{ __('ai_benchmark.facts_matrix', ['sha' => substr($aggregate['matrix_sha256'], 0, 12).'…']) }}</span>
                            @endif
                        </div>
                    </header>

                    {{-- Vendor filter and category order, as on the LEB-100 page. The filter only hides rows and
                         the order only moves them: the number on each card stays its rank in the full board, and
                         a category order adds the card's position in that category. Rendered hidden and revealed
                         by js/site/ai-benchmark.js, so without JavaScript the page is the plain board. The
                         templates travel as hidden text, as on shvia.org, whose language-parity check compares
                         attributes. --}}
                    <div class="ab-filter" data-ab-filter hidden>
                        <div class="ab-filter__chips" role="group" aria-label="{{ __('ai_benchmark.filter_label') }}">
                            <span class="ab-filter__label">{{ __('ai_benchmark.filter_label') }}</span>
                            <button type="button" class="ab-chip" data-vendor="" aria-pressed="true">{{ __('ai_benchmark.filter_all') }}</button>
                            @foreach($vendors as $vendor)
                                <button type="button" class="ab-chip" data-vendor="{{ $vendor }}" aria-pressed="false">{{ $vendor }} <span class="ab-chip__n">{{ collect($models)->where('provider', $vendor)->count() }}</span></button>
                            @endforeach
                        </div>
                        <div class="ab-filter__chips ab-sort" role="group" aria-label="{{ __('ai_benchmark.sort_label') }}">
                            <span class="ab-filter__label">{{ __('ai_benchmark.sort_label') }}</span>
                            <button type="button" class="ab-chip" data-sort="" aria-pressed="true">{{ __('ai_benchmark.sort_overall') }}</button>
                            @foreach(array_keys($weights) as $cat)
                                <button type="button" class="ab-chip" data-sort="{{ $cat }}" aria-pressed="false">{{ __("ai_benchmark.categories.$cat") }}</button>
                            @endforeach
                        </div>
                        <span class="ab-filter__tpl" hidden>{{ __('ai_benchmark.filter_shown', ['shown' => ':shown', 'total' => ':total']) }}</span>
                        <span class="ab-pager__tpl" hidden>{{ __('ai_benchmark.page_of', ['n' => ':n', 'total' => ':total']) }}</span>
                        <span class="ab-pager__prev" hidden>{{ __('ai_benchmark.page_prev') }}</span>
                        <span class="ab-pager__next" hidden>{{ __('ai_benchmark.page_next') }}</span>
                        <span class="ab-sort__tpl" hidden>{{ __('ai_benchmark.sort_position', ['pos' => ':pos', 'cat' => ':cat']) }}</span>
                        <p class="ab-filter__status s-meta" aria-live="polite">{{ __('ai_benchmark.filter_shown', ['shown' => count($agents), 'total' => count($agents)]) }}</p>
                    </div>

                    {{-- The board. An ordered list, because the order IS the content. --}}
                    <ol class="ab-board">
                        @foreach($agents as $i => $a)
                            @php
                                $model = $models[$i];
                                $effort = $effortOf($model);
                                $rank = $i + 1;
                            @endphp
                            <li class="s-card ab-row{{ $i === 0 ? ' ab-row--leader' : '' }}{{ $loop->even ? ' ab-row--alt' : '' }}" data-vendor="{{ $model['provider'] }}" data-scores="{{ collect($a['categories'])->map(fn ($score, $cat) => "$cat:$score")->implode(' ') }}">
                                <span class="ab-row__rank" aria-label="#{{ $rank }}">{{ $rank }}</span>

                                <div class="ab-row__id">
                                    <strong data-rank="#{{ $rank }} · ">{{ $model['name'] }}</strong>
                                    <small>{{ $model['provider'] }}@if($effort) · {{ $effort }}@endif</small>
                                    <span class="ab-row__runs">{{ __('ai_benchmark.runs_label', ['count' => $a['runs_count']]) }}@if(count($a['runs'] ?? []) > 1) ({{ collect($a['runs'])->pluck('total')->implode(' · ') }})@endif @if($a['runs_count'] < 3) · {{ __('ai_benchmark.unofficial') }}@endif</span>
                                    <span class="ab-row__catpos" hidden></span>
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

                                {{-- Opened by its summary or by a click anywhere on the card. The reading is written (it travels in the
                                     aggregate, see comments.json of the private archive); the rest is read from the aggregate, so it never
                                     disagrees with the table. An ACTIVE instance has no flaw to list: only categories, runs, cost and time. --}}
                                @php
                                    $comment = $a['comment'][app()->getLocale() === 'pt_BR' ? 'pt_BR' : 'en'] ?? null;
                                    $full = collect($a['categories'])->filter(fn ($score, $cat) => $score === $weights[$cat]);
                                    $zero = collect($a['categories'])->filter(fn ($score) => $score === 0);
                                    $catNames = fn ($cats) => $cats->keys()->map(fn ($c) => __("ai_benchmark.categories.$c"))->implode(', ');
                                @endphp
                                <details class="ab-row__more">
                                    <summary>{{ __('ai_benchmark.more_open') }}</summary>
                                    @if($comment)
                                        <p class="ab-more__comment">{{ $comment }}</p>
                                        <p class="ab-more__note s-meta">{{ __('leb_300.more_note') }}</p>
                                    @endif
                                    @if($full->isNotEmpty() || $zero->isNotEmpty())
                                        <dl class="ab-more__facts">
                                            @if($full->isNotEmpty())
                                                <dt>{{ __('ai_benchmark.more_full') }}</dt><dd>{{ $catNames($full) }}</dd>
                                            @endif
                                            @if($zero->isNotEmpty())
                                                <dt>{{ __('ai_benchmark.more_zero') }}</dt><dd>{{ $catNames($zero) }}</dd>
                                            @endif
                                        </dl>
                                    @endif
                                    @if(! empty($a['runs']))
                                        <h5 class="ab-more__title">{{ __('ai_benchmark.more_runs') }}</h5>
                                        <ul class="ab-more__runs">
                                            @foreach($a['runs'] as $r)
                                                <li>
                                                    <strong>{{ __('ai_benchmark.more_run', ['n' => $r['run']]) }} · {{ $r['total'] }}</strong>
                                                    @if(($r['wall_minutes'] ?? null) !== null)
                                                        @php $m = (int) round($r['wall_minutes']); @endphp
                                                        <span title="{{ __('leb_300.session_time_help') }}">{{ $m < 60 ? __('ai_benchmark.more_time_min', ['m' => $m]) : ($m % 60 === 0 ? __('ai_benchmark.more_time_h_only', ['h' => intdiv($m, 60)]) : __('ai_benchmark.more_time_h', ['h' => intdiv($m, 60), 'm' => $m % 60])) }}</span>
                                                    @endif
                                                    @if(($r['cost_usd'] ?? null) !== null)
                                                        <span>{{ __('ai_benchmark.more_cost', ['cost' => number_format($r['cost_usd'], 2)]) }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </details>
                            </li>
                        @endforeach
                    </ol>
                </article>
            </div>
        </section>

        {{-- ═══ CAVEATS ═══ --}}
        <section class="s-section s-section--tight s-bg-2">
            <div class="container ab-wrap">
                <h2 class="s-h2 ab-h2">{{ __('leb_300.caveats_title') }}</h2>
                <ul class="ab-caveats">
                    @foreach(['caveat_checkpoint', 'caveat_matrix', 'caveat_client'] as $caveat)
                        <li class="s-body">
                            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                            <span>{{ __("leb_300.$caveat") }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- ═══ WHAT IS PUBLISHED ═══ --}}
    <section class="s-section s-section--tight">
        <div class="container ab-wrap ab-audit">
            <div>
                <h2 class="s-h2 ab-h2">{{ __($hasResult ? 'leb_300.publish_title_result' : 'leb_300.publish_title') }}</h2>
                <p class="s-body">{{ __($hasResult ? 'leb_300.publish_body_result' : 'leb_300.publish_body') }}</p>
                <p class="s-body">{{ __($hasResult ? 'leb_300.stands_body_result' : 'leb_300.stands_body') }}</p>
                {{-- `{!! !!}` is safe here: the substitutions are markup of this file and the
                     text comes from lang/, which is repository content — no user input. --}}
                <p class="s-body">{!! __('leb_300.more_body', [
                    'repo' => '<a href="'.$repo.'" target="_blank" rel="noopener">'.e(__('leb_300.repo_link')).'</a>',
                    'benchmark' => '<a href="'.e(lroute('ai-benchmark')).'">'.e(__('leb_300.benchmark_link')).'</a>',
                    'leb100' => '<a href="'.e(lroute('ai-benchmark.leb-100')).'">'.e(__('leb_300.leb100_link')).'</a>',
                ]) !!}</p>
            </div>
            <div class="ab-actions">
                <a href="{{ $repo }}" target="_blank" rel="noopener" class="s-btn s-btn--lg"><i class="fa-brands fa-github" aria-hidden="true"></i> {{ __('ai_benchmark.cta_source') }}</a>
                <a href="{{ $repo }}/blob/master/SPEC.md" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--lg">{{ __('ai_benchmark.audit_method') }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                <a href="{{ lroute('ai-benchmark.leb-100') }}" class="s-btn s-btn--ghost s-btn--lg">{{ __('leb_300.leb100_results') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>

@endsection
