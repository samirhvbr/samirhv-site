@extends('admin.layouts.app')

@section('title', $allowed ? 'Tura — aparelho permitido' : 'Tura — aparelho negado')

@section('content')

    <div class="admin-card" style="max-width:760px">
        @if($allowed)
            <h2><i class="fa-solid fa-circle-check"></i> Aparelho permitido</h2>
            <p style="color:#cbd5e1;line-height:1.6">
                A credencial de <b>{{ $device }}</b> foi criada. O navegador vai devolver o controle ao
                aplicativo. Se nada acontecer, copie o endereço abaixo e cole no campo do aplicativo,
                ou use o botão.
            </p>
        @else
            <h2><i class="fa-solid fa-circle-xmark"></i> Aparelho negado</h2>
            <p style="color:#cbd5e1;line-height:1.6">
                Nada foi criado. O aplicativo será avisado de que você negou.
            </p>
        @endif

        <p style="margin-top:14px">
            <a href="{{ $uri }}" class="btn btn-primary">Voltar ao aplicativo</a>
        </p>
        <p style="margin-top:14px">
            <label for="tura-uri" style="display:block;color:#94a3b8;margin-bottom:6px">Endereço para colar no aplicativo</label>
            <input id="tura-uri" type="text" readonly value="{{ $uri }}" style="width:100%;font-family:monospace" onfocus="this.select()">
        </p>
        <p style="margin-top:10px">
            <button type="button" class="btn" id="tura-copy">Copiar</button>
        </p>
    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            var uri = @json($uri);
            var copy = document.getElementById('tura-copy');
            copy.addEventListener('click', function () {
                var field = document.getElementById('tura-uri');
                field.select();
                (navigator.clipboard ? navigator.clipboard.writeText(uri) : Promise.reject())
                    .then(function () { copy.textContent = 'Copiado'; })
                    .catch(function () { document.execCommand('copy'); copy.textContent = 'Copiado'; });
            });
            // Se o navegador não tem quem abra tura://, nada acontece e a página fica,
            // com o endereço à mão. Um instante antes, para a página existir.
            setTimeout(function () { window.location.href = uri; }, 300);
        })();
    </script>
@endpush
