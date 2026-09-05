{{-- Estilo da paginação do painel (empurrado uma vez só, venha de qual view vier). --}}
@once
@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/admin/pagination.css') }}">
@endpush
@endonce
