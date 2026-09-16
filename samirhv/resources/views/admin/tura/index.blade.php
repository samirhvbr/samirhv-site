@extends('admin.layouts.app')

@section('title', 'Tura — credenciais')

@section('content')

    <div class="admin-card" style="margin-bottom:18px">
        <h2 style="display:flex;align-items:center;gap:10px"><i class="fa-solid fa-feather-pointed"></i> Como um aparelho entra</h2>
        <p style="color:#cbd5e1;line-height:1.6;max-width:820px">
            Uma credencial por aparelho. Crie aqui, o arquivo <code>.secret</code> baixa
            <b>uma vez</b> — guarde no gerenciador de senhas — e no Tura Notes aponte
            <b>Arquivo de credencial</b> para ele, com <b>Endereço do servidor</b>
            <code>https://tura.samirhv.com.br</code> e <b>Workspace no servidor</b>
            <code>{{ $workspace }}</code>.
        </p>
        <p style="color:#cbd5e1;line-height:1.6;max-width:820px">
            <b>Não há usuário e senha</b>, e não é omissão: o servidor guarda só o digest BLAKE3 do
            segredo, então ele precisa ser cunhado lá — uma senha escolhida por alguém seria uma
            string que o servidor nunca viu. Dois aparelhos com a mesma credencial não se
            distinguem, e aí revogar o que se perdeu corta o que se manteve.
        </p>
    </div>

    @if(! $available)
        @include('admin.tura._unavailable')
    @else

        <div class="admin-card" style="margin-bottom:18px">
            <h2 style="display:flex;align-items:center;gap:10px"><i class="fa-solid fa-key"></i> Nova credencial</h2>
            <form method="POST" action="{{ route('admin.tura.store') }}">
                @csrf
                <label for="tura-label" class="card-sub">Rótulo do aparelho — letras, números, <code>-</code> e <code>_</code></label>
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:8px">
                    <input id="tura-label" name="label" required maxlength="64" pattern="[A-Za-z0-9_-]{1,64}"
                           placeholder="macbook" value="{{ old('label') }}"
                           style="flex:1;min-width:240px">
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <i class="fa-solid fa-download"></i> Criar e baixar
                    </button>
                </div>
                @error('label')<p class="admin-alert admin-alert-error" style="margin-top:10px">{{ $message }}</p>@enderror
                <p class="card-sub" style="margin-top:12px">
                    Permissões concedidas: <code>{{ implode(', ', \App\Http\Controllers\Admin\TuraCredentialController::PERMISSIONS) }}</code>
                    — o que a sincronização usa, e nada além. O escopo é o workspace inteiro.
                    <b>O download acontece sem sair desta página</b>; recarregue para ver a
                    credencial na lista.
                </p>
            </form>
        </div>

        <div class="admin-card" style="padding:0;overflow:hidden">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Rótulo</th>
                        <th>Workspace</th>
                        <th>Permissões</th>
                        <th>Status</th>
                        <th style="text-align:right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($credentials as $credential)
                        <tr>
                            <td>
                                <strong style="color:#f1f5f9">{{ $credential['label'] ?? '—' }}</strong>
                                <div class="muted" style="font-family:'JetBrains Mono',monospace;font-size:.72rem">{{ $credential['id'] ?? '' }}</div>
                            </td>
                            <td>{{ $credential['workspace'] ?? '—' }}</td>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:.75rem">
                                {{ is_array($credential['permissions'] ?? null) ? implode(', ', $credential['permissions']) : '—' }}
                            </td>
                            <td>
                                @if($credential['revoked'] ?? false)
                                    <span class="badge badge-muted">revogada</span>
                                @else
                                    <span class="badge">ativa</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                @if(! ($credential['revoked'] ?? false))
                                    <form method="POST" action="{{ route('admin.tura.destroy') }}" style="display:inline"
                                          onsubmit="return confirm('Revogar esta credencial? O aparelho perde o acesso na próxima requisição.')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $credential['id'] ?? '' }}">
                                        <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger">Revogar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted" style="padding:22px">Nenhuma credencial ainda. Crie a primeira acima.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="card-sub" style="margin-top:14px;max-width:820px">
            A revogação vale na hora, sem reiniciar o servidor, e as outras credenciais seguem
            funcionando. Credenciais revogadas continuam na lista porque continuam ocupando lugar:
            o servidor guarda no máximo 1024 entradas, revogadas incluídas.
        </p>
    @endif

@endsection
