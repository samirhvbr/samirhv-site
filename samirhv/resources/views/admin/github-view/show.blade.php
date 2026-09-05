@extends('admin.layouts.app')

@section('title', $repository->fullName().' · GitHub View')

@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/admin/github-view-show.css') }}">
@endpush

@section('content')
    <div class="gh-head">
        <div>
            <a href="{{ route('admin.github-view.index') }}" class="gh-back">← GitHub View</a>
            <h1 class="gh-title">{{ $repository->fullName() }}</h1>
            @if($repository->description)<p class="gh-desc">{{ $repository->description }}</p>@endif
        </div>
        <div class="gh-actions">
            <form method="POST" action="{{ route('admin.github-view.repos.sync', ['owner' => $repository->owner, 'name' => $repository->name]) }}">
                @csrf
                <button type="submit" class="gh-btn"><i class="fa-solid fa-rotate"></i> sincronizar</button>
            </form>
            <a href="{{ $repository->githubUrl() }}" target="_blank" rel="noopener" class="gh-btn"><i class="fa-brands fa-github"></i> github ↗</a>
            <form method="POST" action="{{ route('admin.github-view.repos.destroy', ['owner' => $repository->owner, 'name' => $repository->name]) }}"
                  onsubmit="return confirm('Remover {{ $repository->fullName() }} e todos os seus dados?')">
                @csrf @method('DELETE')
                <button type="submit" class="gh-btn gh-btn--danger"><i class="fa-solid fa-trash"></i> remover</button>
            </form>
        </div>
    </div>

    @if($repository->sync_error)
        <div class="admin-alert admin-alert-error" style="margin-top:16px">
            <i class="fa-solid fa-circle-exclamation"></i> Última sincronização falhou: {{ $repository->sync_error }}
        </div>
    @endif

    <div class="gh-window">
        <span style="color:var(--dim);text-transform:uppercase;letter-spacing:.12em;margin-right:4px">janela</span>
        @foreach($windows as $days)
            <a href="{{ route('admin.github-view.repos.show', ['owner' => $repository->owner, 'name' => $repository->name, 'days' => $days]) }}"
               class="{{ $days === $window ? 'is-active' : '' }}">{{ $days }}d</a>
        @endforeach
    </div>

    @if($heatmap['total'] === 0)
        <div class="admin-card gh-empty" style="margin-top:16px">
            Sem commits nos últimos {{ $window }} dias{{ $repository->isSyncing() ? ' (ainda) — sync em andamento.' : ' — tente uma janela maior.' }}
        </div>
    @else
        {{-- Heatmap dia × hora (canvas). O ES module lê o JSON abaixo e anima. --}}
        <div class="admin-card" style="margin-top:16px" data-gh-heatmap>
            <div class="gh-hm__head">
                <div>
                    <p class="card-sub" style="text-transform:uppercase;letter-spacing:.12em">últimos {{ $window }} dias · 24 horas</p>
                    <p class="gh-hm__count"><span data-gh-heatmap-counter>{{ number_format($heatmap['total'], 0, ',', '.') }}</span> commits</p>
                </div>
                <div class="gh-hm__legend">
                    <span>1</span>
                    <span class="gh-hm__ramp"></span>
                    <span>{{ $heatmap['max'] }} commits/hora</span>
                    <button type="button" class="gh-btn" data-gh-heatmap-replay><i class="fa-solid fa-rotate-right"></i> replay</button>
                </div>
            </div>
            <canvas class="gh-hm__canvas" data-gh-heatmap-canvas></canvas>
            <script type="application/json" data-gh-heatmap-data>@json($heatmap)</script>
        </div>
    @endif
@endsection

@push('scripts')
<script type="module" src="{{ vasset('js/admin/github-view/heatmap.js') }}"></script>
@endpush
