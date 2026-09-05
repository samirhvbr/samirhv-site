{{-- Sub-navegação do módulo AI-MEMORY. Usada por TODAS as abas — o estilo vem
     junto (uma vez só) para o módulo ter o mesmo vocabulário em toda tela. --}}
@include('admin.ai-memory._styles')

@once
@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/admin/ai-memory-tabs.css') }}">
@endpush
@endonce

@php
    $r = (string) request()->route()?->getName();
    $tabs = [
        'admin.ai-memory.dashboard' => 'Dashboard',
        'admin.ai-memory.projects' => 'Projetos',
        'admin.ai-memory.workspaces' => 'Workspaces',
        'admin.ai-memory.pages' => 'Páginas',
        'admin.ai-memory.sessions' => 'Sessões',
        'admin.ai-memory.observations' => 'Observações',
        'admin.ai-memory.handoffs' => 'Handoffs',
        'admin.ai-memory.search' => 'Busca',
    ];
@endphp

<nav class="aimnav" aria-label="Seções do AI-MEMORY">
    @foreach($tabs as $name => $label)
        {{-- a tela de detalhe (…/{hexId}) mantém acesa a aba da sua listagem --}}
        <a href="{{ route($name) }}" @if($r === $name || str_starts_with($r, $name.'.')) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
