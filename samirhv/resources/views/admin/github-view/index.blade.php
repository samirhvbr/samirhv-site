@extends('admin.layouts.app')

@section('title', 'GitHub View')

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/admin/github-view.css') }}">
@endpush

@section('content')
    @php
        $max = $overview->maxCommits();
        $ranked = $repositories->sortByDesc(fn ($r) => $overview->for($r)['total_commits'])->values();
        $barColors = ['#d946ef', '#fb923c', '#22d3ee', '#34d399', '#ec4899', '#8b5cf6', '#fcd34d', '#38bdf8'];
        $sortKey = explode('_', $sort)[0];
        $sortDir = explode('_', $sort)[1] ?? 'desc';
        $chipDays = \App\Services\GitHub\Visualizations\RepositoryOverview::CHIP_DAYS;
    @endphp

    <div class="gh-top">
        <div>
            <p class="gh-kicker">Repositórios monitorados</p>
            <p class="gh-count">{{ $repositories->count() }} <span>repos</span></p>
        </div>
        <div class="gh-top__actions">
            <form method="POST" action="{{ route('admin.github-view.repos.store') }}" class="gh-add">
                @csrf
                <div class="gh-ac" data-gh-autocomplete data-url="{{ route('admin.github-view.suggestions') }}">
                    <input type="text" name="repository" class="gh-input" autocomplete="off" spellcheck="false" required
                        value="{{ old('repository') }}" data-gh-autocomplete-input
                        placeholder="{{ $defaultOwner ? $defaultOwner.'/repo · ou só repo' : 'owner/repo' }}">
                    <div class="gh-ac__list is-hidden" data-gh-autocomplete-list></div>
                </div>
                <button type="submit" class="gh-btn"><i class="fa-solid fa-plus"></i> add + sync</button>
            </form>
            <form method="POST" action="{{ route('admin.github-view.import') }}"
                  onsubmit="return confirm('Importar TODOS os seus repositórios do GitHub como pendentes? (você sincroniza depois, ou deixa o cron)')">
                @csrf
                <button type="submit" class="gh-btn gh-btn--ghost"><i class="fa-solid fa-cloud-arrow-down"></i> Importar todos os meus repos</button>
            </form>
        </div>
    </div>
    @error('repository')<p class="gh-err">{{ $message }}</p>@enderror

    @if($repositories->isEmpty())
        <div class="gh-empty">
            <p><i class="fa-solid fa-code-branch" style="font-size:1.7rem;opacity:.4"></i></p>
            <p>Nenhum repositório ainda. Adicione um <code>owner/name</code> acima (ou importe todos) — o 1º sync começa na hora.</p>
        </div>
    @else
        {{-- Barras: commits por repo (log scale por padrão; toggle p/ linear). --}}
        <section class="gh-bars" data-gh-bars>
            <p class="gh-bars__label">+ commits por repo
                <button type="button" class="gh-scale" data-gh-barscale-label>(log scale)</button>
            </p>
            <div class="gh-bars__list">
                @foreach($ranked->take(3) as $i => $repo)
                    @include('admin.github-view._bar', ['repo' => $repo, 'stats' => $overview->for($repo), 'max' => $max, 'color' => $barColors[$i % count($barColors)]])
                @endforeach
            </div>
            @if($ranked->count() > 3)
                <div class="gh-bars__list gh-bars__more is-hidden" data-gh-reveal-content>
                    {{-- slice(3) preserva as chaves originais (3,4,5…), então $i já é o índice global. --}}
                    @foreach($ranked->slice(3) as $i => $repo)
                        @include('admin.github-view._bar', ['repo' => $repo, 'stats' => $overview->for($repo), 'max' => $max, 'color' => $barColors[$i % count($barColors)]])
                    @endforeach
                </div>
                <button type="button" class="gh-showall" data-gh-reveal-toggle
                        data-more="… mostrar todos os {{ $ranked->count() }} repos" data-less="mostrar menos">… mostrar todos os {{ $ranked->count() }} repos</button>
            @endif
        </section>

        <div class="gh-sort">
            <span class="gh-sort__label">ordenar</span>
            @foreach(['updated' => 'modificado', 'name' => 'nome', 'created' => 'criado'] as $key => $label)
                @php
                    $isActive = $sortKey === $key;
                    $nextDir = $isActive ? ($sortDir === 'desc' ? 'asc' : 'desc') : ($key === 'name' ? 'asc' : 'desc');
                    $arrow = $isActive ? ($sortDir === 'desc' ? ' ↓' : ' ↑') : '';
                @endphp
                <a href="{{ route('admin.github-view.index', ['sort' => $key.'_'.$nextDir]) }}"
                   class="gh-sort__opt {{ $isActive ? 'is-active' : '' }}">{{ $label }}{{ $arrow }}</a>
            @endforeach
        </div>

        <div data-gh-filter>
            <div class="gh-filterbar">
                <input type="search" class="gh-filter" placeholder="filtrar repos…" autocomplete="off"
                       aria-label="filtrar repositórios" data-gh-filter-input>
            </div>

            <section class="gh-grid">
                @foreach($repositories as $repo)
                    <div data-gh-filter-item data-repo-name="{{ strtolower($repo->fullName()) }}">
                        @include('admin.github-view._card', ['repo' => $repo, 'stats' => $overview->for($repo), 'chipDays' => $chipDays])
                    </div>
                @endforeach
            </section>

            <p class="gh-empty is-hidden" data-gh-filter-empty>Nenhum repositório corresponde ao filtro.</p>
        </div>
    @endif
@endsection

@push('scripts')
<script defer src="{{ vasset('js/admin/github-view/dashboard.js') }}"></script>
@endpush
