@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Busca')

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/admin/ai-memory-search.css') }}">
@endpush

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php $tierChip = ['working' => 'aim-chip--warn', 'episodic' => 'aim-chip--accent', 'semantic' => 'aim-chip--ok']; @endphp

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Busca no conhecimento</h2>
                    <p class="aim-sub">índice FTS5 · título + corpo das páginas consolidadas</p>
                </div>
            </header>

            <form method="GET" action="{{ route('admin.ai-memory.search') }}" class="aim-filters aim-filters--search">
                <div class="form-row">
                    <label for="q">Pesquisar</label>
                    <input type="search" name="q" id="q" value="{{ $q }}" placeholder="ex: autenticação oauth" autofocus autocomplete="off" spellcheck="false">
                </div>
                <div class="aim-filters__go">
                    <button type="submit" class="admin-btn admin-btn-primary">Buscar</button>
                    @if($q !== '')<a href="{{ route('admin.ai-memory.search') }}" class="admin-btn">Limpar</a>@endif
                </div>
            </form>

            @if($q === '')
                <div class="aim-blank">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <p>Procure por um termo que apareça no texto das páginas.</p>
                    <p>A busca é literal (FTS5): ela casa palavras do título e do corpo — não entende sinônimos. Use <code>deploy</code>, <code>sqlite wal</code>, <code>"erro 500"</code>.</p>
                </div>
            @elseif(empty($results))
                <div class="aim-blank">
                    <i class="fa-solid fa-circle-question"></i>
                    <p>Nenhum resultado para <b>{{ $q }}</b>.</p>
                    <p>Como o índice é literal, termos que não aparecem no texto não retornam. Tente uma palavra mais curta, ou procure pelo nome do arquivo/caminho.</p>
                </div>
            @else
                <p class="aim-sub" style="margin:0 0 4px">
                    {{ count($results) }} {{ count($results) === 1 ? 'resultado' : 'resultados' }} para “{{ $q }}”
                </p>

                @foreach($results as $r)
                    <article class="aim-hit">
                        <div class="aim-hit__top">
                            <a href="{{ route('admin.ai-memory.pages.show', $r->id_hex) }}" class="aim-hit__title">{{ $r->title }}</a>
                            <span class="aim-chip {{ $tierChip[$r->tier] ?? '' }}">{{ $r->tier }}</span>
                            <span class="aim-mono">{{ $r->project }}</span>
                        </div>
                        <span class="aim-path" title="{{ $r->path }}">{{ $r->path }}</span>
                        {{-- O ai-memory devolve o trecho com <<< >>> marcando o termo; escapamos
                             o texto e só então trocamos os marcadores por <mark>. --}}
                        <p class="aim-hit__snip">{!! str_replace(['&lt;&lt;&lt;', '&gt;&gt;&gt;'], ['<mark>', '</mark>'], e($r->snippet)) !!}</p>
                    </article>
                @endforeach
            @endif
        </section>
    @endunless
</div>
@endsection
