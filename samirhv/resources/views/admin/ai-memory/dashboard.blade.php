@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Dashboard')


@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $n = fn ($v) => number_format((int) $v, 0, ',', '.');
            $T = \App\Services\AiMemory\AiMemoryTime::class;

            $obs = $summary->series($observationsByDay);
            $ses = $summary->series($sessionsByDay);
            $days = array_keys($observationsByDay);
            $labelEvery = 5;
            $fmtDay = fn ($ymd) => \Illuminate\Support\Carbon::parse($ymd)->format('d/m');

            $deltas = [
                'observations' => $summary->delta($history, 'observations', (int) $counts['observations']),
                'pages' => $summary->delta($history, 'pages', (int) $counts['pages']),
                'sessions' => $summary->delta($history, 'sessions', (int) $counts['sessions']),
            ];

            $histMetrics = ['observations' => 'Observações', 'pages' => 'Páginas', 'sessions' => 'Sessões'];
            $histData = $summary->historySeries($history, array_keys($histMetrics));
            $histDates = $history->map(fn ($s) => $s->captured_on->format('d/m/Y'))->values()->all();
            $histTop = $summary->niceMax(max($histData['observations'] ?: [0]));
            $histPath = $summary->areaPath($histData['observations'], $histTop);

            $sparks = [
                'observations' => $summary->sparkline($histData['observations']),
                'pages' => $summary->sparkline($histData['pages']),
                'sessions' => $summary->sparkline($histData['sessions']),
            ];
            $hasHistory = $history->count() > 1;
        @endphp

        {{-- ── Totais ao vivo: um painel de instrumentos, não oito cartões iguais ── --}}
        <section class="aim-panel" aria-label="Totais na memória">
            <div class="aim-panel__row aim-panel__row--lede">
                @php
                    $lede = [
                        ['key' => 'observations', 'label' => 'Observações', 'count' => $counts['observations'], 'route' => route('admin.ai-memory.observations'), 'hero' => true],
                        ['key' => 'pages', 'label' => 'Páginas', 'count' => $counts['pages'], 'route' => route('admin.ai-memory.pages'), 'hero' => false],
                        ['key' => 'sessions', 'label' => 'Sessões', 'count' => $counts['sessions'], 'route' => route('admin.ai-memory.sessions'), 'hero' => false],
                    ];
                @endphp
                @foreach($lede as $cell)
                    @php $delta = $deltas[$cell['key']]; @endphp
                    <div class="aim-cell {{ $cell['hero'] ? 'aim-cell--lede' : '' }}">
                        <div class="aim-cell__top">
                            <a class="aim-cell__link" href="{{ $cell['route'] }}">
                                <p class="aim-label">{{ $cell['label'] }}</p>
                                <p class="aim-value {{ $cell['hero'] ? 'aim-value--hero' : 'aim-value--md' }}" data-live="counts.{{ $cell['key'] }}">{{ $n($cell['count']) }}</p>
                            </a>
                            @if($hasHistory)
                                <span class="aim-spark {{ $cell['hero'] ? 'aim-spark--lede' : '' }}" aria-hidden="true">
                                    <svg viewBox="0 0 100 30" preserveAspectRatio="none"><path class="l" d="{{ $sparks[$cell['key']]['line'] }}"></path></svg>
                                    <span class="aim-spark__dot" style="top:{{ round($sparks[$cell['key']]['last_y'] / 30 * 100, 2) }}%"></span>
                                    @if($cell['hero'])
                                        <span class="aim-spark__cap">{{ $history->count() }} retratos</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                        @if($delta !== null)
                            <p class="aim-delta {{ $delta['value'] > 0 ? 'aim-delta--up' : ($delta['value'] < 0 ? 'aim-delta--down' : 'aim-delta--flat') }}"
                               data-live-delta="{{ $cell['key'] }}" data-ref="{{ (int) $cell['count'] - $delta['value'] }}">
                                <i>{{ $delta['value'] > 0 ? '▲' : ($delta['value'] < 0 ? '▼' : '•') }}</i>
                                <b>{{ $delta['value'] > 0 ? '+' : '' }}{{ $n($delta['value']) }}</b>
                                em {{ $delta['days'] }} {{ $delta['days'] === 1 ? 'dia' : 'dias' }}@if($cell['hero']) · <b data-live="obs.today">{{ $n($obs['today']) }}</b> hoje @endif
                            </p>
                        @elseif($cell['hero'])
                            <p class="aim-delta aim-delta--flat"><b data-live="obs.today">{{ $n($obs['today']) }}</b> hoje · sem retrato anterior para comparar</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="aim-panel__row aim-panel__row--facts">
                <div class="aim-cell">
                    <p class="aim-label">Workspaces</p>
                    <a class="aim-fact" href="{{ route('admin.ai-memory.workspaces') }}">
                        <span class="aim-value aim-value--sm" data-live="counts.workspaces">{{ $n($counts['workspaces']) }}</span>
                    </a>
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Projetos</p>
                    <a class="aim-fact" href="{{ route('admin.ai-memory.projects') }}">
                        <span class="aim-value aim-value--sm" data-live="counts.projects">{{ $n($counts['projects']) }}</span>
                    </a>
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Embeddings</p>
                    <p class="aim-value aim-value--sm" data-live="counts.embeddings">{{ $n($counts['embeddings']) }}</p>
                    @if((int) $counts['embeddings'] === 0)
                        <p class="aim-hint">índice semântico ainda não gerado</p>
                    @endif
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Handoffs abertos</p>
                    <a class="aim-fact" href="{{ route('admin.ai-memory.handoffs', ['state' => 'open']) }}">
                        <span class="aim-dot {{ (int) $counts['handoffs_open'] > 0 ? 'aim-dot--warn' : 'aim-dot--idle' }}"></span>
                        <span class="aim-value aim-value--sm" data-live="counts.handoffs_open">{{ $n($counts['handoffs_open']) }}</span>
                    </a>
                    <p class="aim-hint">{{ (int) $counts['handoffs_open'] > 0 ? 'esperando serem aceitos' : 'nenhum pendente' }}</p>
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Propostas pendentes</p>
                    <span class="aim-fact">
                        <span class="aim-dot {{ (int) $counts['proposals_pending'] > 0 ? 'aim-dot--warn' : 'aim-dot--idle' }}"></span>
                        <span class="aim-value aim-value--sm" data-live="counts.proposals_pending">{{ $n($counts['proposals_pending']) }}</span>
                    </span>
                    <p class="aim-hint">{{ (int) $counts['proposals_pending'] > 0 ? 'na fila do auto-improve' : 'nada na fila do auto-improve' }}</p>
                </div>
            </div>
        </section>

        {{-- Procedência + "ao vivo": de onde saiu cada número, e quando chegou. --}}
        <div class="aim-source">
            <span>fonte: <b>{{ basename($aimemoryPath ?: 'memory.sqlite') }}</b> (somente leitura)</span>
            <span>em <b>{{ $aimemoryPath ? dirname($aimemoryPath) : '(caminho não configurado)' }}</b></span>
            <span>dias agrupados em <b>UTC</b></span>

            {{-- O ai-memory é escrito pelos agentes, não por este app: não há evento
                 nosso para transmitir. "Ao vivo" aqui é polling curto do navegador
                 (sem daemon, sem WebSocket) — ver dashboard.js. --}}
            <span class="aim-live" data-aim-live data-url="{{ route('admin.ai-memory.live') }}" data-every="15">
                <button type="button" class="aim-live__btn" data-aim-livetoggle aria-pressed="true"
                        title="Pausar/retomar a atualização automática">
                    <span class="aim-live__dot" aria-hidden="true"></span>
                    <span data-aim-livelabel>ao vivo</span>
                </button>
                <span class="aim-live__when" data-aim-livewhen role="status" aria-live="polite"></span>
            </span>
        </div>

        {{-- ── Atividade: observações e sessões compartilhando o MESMO eixo de dias ── --}}
        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Atividade</h2>
                    <p class="aim-sub">últimos {{ count($days) }} dias · {{ $fmtDay($days[0]) }} → {{ $fmtDay(end($days)) }}</p>
                </div>
                <button type="button" class="admin-btn admin-btn-sm" data-aim-table aria-pressed="false" aria-expanded="false" aria-controls="aim-activity-table">tabela</button>
            </header>

            <p class="aim-serie">
                <span class="aim-serie__name"><span class="aim-key aim-key--obs"></span>Observações</span>
                <span>
                    <b data-live="obs.total">{{ $n($obs['total']) }}</b> no período · média <b data-live="obs.avg">{{ $n($obs['avg']) }}</b>/dia
                    @if($obs['peak_day']) · pico <b data-live="obs.max">{{ $n($obs['max']) }}</b> em <span data-live="obs.peak">{{ $fmtDay($obs['peak_day']) }}</span> @endif
                </span>
            </p>

            <div class="aim-plot" data-aim-plot tabindex="0" role="img"
                 data-days='@json(array_map($fmtDay, $days), JSON_HEX_APOS | JSON_HEX_QUOT)'
                 data-obs='@json(array_values($observationsByDay), JSON_HEX_APOS | JSON_HEX_QUOT)'
                 data-ses='@json(array_values($sessionsByDay), JSON_HEX_APOS | JSON_HEX_QUOT)'
                 aria-label="Observações e sessões por dia nos últimos {{ count($days) }} dias. Observações: {{ $n($obs['total']) }} no período, média de {{ $n($obs['avg']) }} por dia. Sessões: {{ $n($ses['total']) }} no período, média de {{ $n($ses['avg']) }} por dia. Use as setas do teclado para percorrer os dias, ou o botão tabela para ver os números.">
                <div class="aim-row aim-row--obs">
                    <span class="aim-gridline" style="bottom:100%"></span>
                    <span class="aim-gridline" style="bottom:50%"></span>
                    <span class="aim-ymax" data-live="obs.top">{{ $n($obs['top']) }}</span>
                    @foreach($observationsByDay as $day => $value)
                        <div class="aim-col">
                            <span class="aim-colbar{{ $value === 0 ? ' aim-colbar--zero' : ($value === $obs['max'] ? ' aim-colbar--peak' : '') }}"
                                  @if($value > 0) style="height:{{ round($value / $obs['top'] * 100, 2) }}%" @endif></span>
                        </div>
                    @endforeach
                </div>

                <p class="aim-serie aim-serie--stack">
                    <span class="aim-serie__name"><span class="aim-key aim-key--ses"></span>Sessões</span>
                    <span>
                        <b data-live="ses.total">{{ $n($ses['total']) }}</b> no período · média <b data-live="ses.avg">{{ $n($ses['avg']) }}</b>/dia
                        @if($ses['peak_day']) · pico <b data-live="ses.max">{{ $n($ses['max']) }}</b> em <span data-live="ses.peak">{{ $fmtDay($ses['peak_day']) }}</span> @endif
                    </span>
                </p>

                <div class="aim-row aim-row--ses">
                    <span class="aim-gridline" style="bottom:100%"></span>
                    <span class="aim-ymax" data-live="ses.top">{{ $n($ses['top']) }}</span>
                    @foreach($sessionsByDay as $day => $value)
                        <div class="aim-col">
                            <span class="aim-colbar{{ $value === 0 ? ' aim-colbar--zero' : ($value === $ses['max'] ? ' aim-colbar--peak' : '') }}"
                                  @if($value > 0) style="height:{{ round($value / $ses['top'] * 100, 2) }}%" @endif></span>
                        </div>
                    @endforeach
                </div>

                <div class="aim-axis">
                    @foreach($days as $idx => $day)
                        @php
                            $isLast = $idx === count($days) - 1;
                            $show = $idx % $labelEvery === 0 || $isLast;
                            // em tela estreita só sobram os rótulos "esparsos" (um a cada dois)
                            $dense = $show && ! $isLast && $idx % ($labelEvery * 2) !== 0;
                        @endphp
                        <span class="{{ $isLast ? 'is-today' : '' }}{{ $dense ? ' is-dense' : '' }}">{{ $show ? $fmtDay($day) : '' }}</span>
                    @endforeach
                </div>

                <div class="aim-cross" data-aim-cross></div>
                <div class="aim-tip" data-aim-tip role="status" aria-live="polite"></div>
            </div>

            @if($obs['total'] === 0 && $ses['total'] === 0)
                <p class="aim-empty" style="margin-top:16px">
                    Nenhuma atividade nestes {{ count($days) }} dias — os agentes não abriram sessão
                    nem gravaram observação. Os totais acima continuam valendo: são o acumulado da memória.
                </p>
            @endif

            {{-- Toda leitura do gráfico existe também em texto: o tooltip nunca é o único caminho. --}}
            <div class="aim-tablewrap" id="aim-activity-table" data-aim-tablewrap hidden>
                <table class="admin-table">
                    <caption class="sr-only">Observações e sessões por dia, do mais recente para o mais antigo</caption>
                    <thead><tr><th scope="col">Dia</th><th scope="col">Observações</th><th scope="col">Sessões</th></tr></thead>
                    <tbody>
                        @foreach(array_reverse($days) as $day)
                            <tr>
                                <td>{{ $fmtDay($day) }}</td>
                                <td>{{ $n($observationsByDay[$day]) }}</td>
                                <td>{{ $n($sessionsByDay[$day] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="aim-grid2">
            {{-- ── Evolução histórica DURÁVEL (MySQL — sobrevive a reset do ai-memory) ── --}}
            <section class="admin-card aim-card aim-card--tall">
                <header class="aim-card__head">
                    <div>
                        <h2>Evolução histórica</h2>
                        <p class="aim-sub">{{ $history->count() }} {{ $history->count() === 1 ? 'retrato diário' : 'retratos diários' }} · MySQL</p>
                    </div>
                    @if($hasHistory)
                        <div class="aim-seg" role="group" aria-label="Métrica do histórico">
                            @foreach($histMetrics as $key => $label)
                                <button type="button" data-aim-metric="{{ $key }}" aria-pressed="{{ $key === 'observations' ? 'true' : 'false' }}">{{ $label }}</button>
                            @endforeach
                        </div>
                    @endif
                </header>

                @if(! $hasHistory)
                    <p class="aim-empty">
                        @if($history->isEmpty())
                            Ainda sem retratos. O job diário <code>aimemory:snapshot</code> não rodou —
                            execute <code>php artisan aimemory:snapshot</code> para gravar o primeiro
                            (e confira o cron do Laravel no servidor).
                        @else
                            Só um retrato até agora ({{ $histDates[0] }}). A curva aparece a partir do segundo —
                            o job <code>aimemory:snapshot</code> grava um por dia.
                        @endif
                    </p>
                @else
                    <div class="aim-area" data-aim-area
                         data-dates='@json($histDates, JSON_HEX_APOS | JSON_HEX_QUOT)'
                         data-series='@json($histData, JSON_HEX_APOS | JSON_HEX_QUOT)'
                         data-labels='@json($histMetrics, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)'>
                        <div class="aim-area__grid" data-aim-areagrid>
                            <span style="top:0"></span><i style="top:0" data-aim-ytop>{{ $n($histTop) }}</i>
                            <span style="top:50%"></span><i style="top:50%" data-aim-ymid>{{ $n((int) ($histTop / 2)) }}</i>
                            <span style="bottom:0"></span><i style="bottom:0" class="is-zero">0</i>
                        </div>
                        <svg viewBox="0 0 1000 220" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <linearGradient id="aimFade" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#6366f1" stop-opacity=".26"></stop>
                                    <stop offset="100%" stop-color="#6366f1" stop-opacity="0"></stop>
                                </linearGradient>
                            </defs>
                            <path class="a" data-aim-areapath d="{{ $histPath['area'] }}"></path>
                            <path class="l" data-aim-linepath d="{{ $histPath['line'] }}"></path>
                        </svg>
                        <div class="aim-area__dot" data-aim-areadot style="left:100%;top:{{ round($histPath['last_y'] / 220 * 100, 2) }}%"></div>
                        <div class="aim-area__end" data-aim-areaend style="left:100%;top:{{ round($histPath['last_y'] / 220 * 100, 2) }}%">{{ $n(end($histData['observations'])) }}</div>
                        <div class="aim-area__hit" data-aim-areahit tabindex="0" role="img"
                             aria-label="Evolução histórica das observações, de {{ $histDates[0] }} a {{ end($histDates) }}: de {{ $n($histData['observations'][0]) }} a {{ $n(end($histData['observations'])) }}."></div>
                        <div class="aim-tip" data-aim-areatip role="status" aria-live="polite"></div>
                    </div>
                    <p class="aim-sub aim-area__foot">{{ $histDates[0] }} → {{ end($histDates) }} · os retratos sobrevivem a um reset do ai-memory</p>
                @endif
            </section>

            {{-- ── Ranking: por onde a memória cresceu ── --}}
            <section class="admin-card aim-card">
                <header class="aim-card__head">
                    <div>
                        <h2>Projetos mais ativos</h2>
                        <p class="aim-sub">por observações acumuladas</p>
                    </div>
                </header>

                @if($topProjects->isEmpty())
                    <p class="aim-empty">Nenhum projeto na memória ainda.</p>
                @else
                    @php $rankMax = max(1, (int) $topProjects->max('observations')); @endphp
                    <ul class="aim-rank">
                        @foreach($topProjects as $project)
                            <li>
                                <a href="{{ route('admin.ai-memory.projects.show', $project->id_hex) }}">
                                    <span class="aim-rank__name">{{ $project->name }}</span>
                                    <span class="aim-rank__n">{{ $n($project->observations) }}</span>
                                    <span class="aim-rank__track"><span class="aim-rank__fill" style="width:{{ round($project->observations / $rankMax * 100, 1) }}%"></span></span>
                                    <span class="aim-rank__meta">{{ $T::human($project->last_session_at) }} · {{ $n($project->sessions) }} sessões · {{ $n($project->pages) }} páginas</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a class="aim-more" href="{{ route('admin.ai-memory.projects') }}">ver todos os {{ $n($counts['projects']) }} projetos →</a>
                @endif
            </section>
        </div>
    @endunless
</div>
@endsection

@push('scripts')
<script defer src="{{ vasset('js/admin/ai-memory/dashboard.js') }}"></script>
@endpush
