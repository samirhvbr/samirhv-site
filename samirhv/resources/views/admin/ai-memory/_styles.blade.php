{{-- Sistema visual do módulo AI-MEMORY.

     Empurrado uma vez por requisição (via _tabs, que toda tela inclui) e escopado
     em .aim para não vazar para o resto do painel. As telas usam o vocabulário do
     admin (.admin-card, .admin-table, .admin-btn, .badge) e este arquivo só
     acrescenta o que o módulo precisa: painel de instrumentos, gráficos, chips,
     medidor de importância, timeline e prosa. --}}
@once
@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/admin/ai-memory.css') }}">
@endpush
@endonce
