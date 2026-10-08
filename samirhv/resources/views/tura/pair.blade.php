@extends('admin.layouts.app')

@section('title', 'Tura — entrar no aplicativo')

@section('content')

    <div class="admin-card" style="max-width:760px">
        <h2 style="display:flex;align-items:center;gap:10px"><i class="fa-solid fa-feather-pointed"></i> Permitir este aparelho?</h2>

        @isset($error)
            <p role="alert" style="color:#fca5a5;line-height:1.6">{{ $error }}</p>
        @endisset

        <p style="color:#cbd5e1;line-height:1.6">
            O aplicativo <b>Tura Notes</b> pede para entrar como <b>{{ $pair['label'] }}</b>.
            Se foi você que apertou <b>Entrar com o seu site</b> agora, permita; se não foi,
            negue.
        </p>
        <p style="color:#cbd5e1;line-height:1.6">
            Permitir cria uma credencial <b>só para este aparelho</b>, chamada
            <code>{{ $pair['device'] }}</code>, no workspace <code>{{ $workspace }}</code>, com estas
            permissões: <code>{{ implode(', ', $permissions) }}</code>. Ela aparece em
            <a href="{{ route('admin.tura.index') }}">Credenciais do Tura</a>, onde pode ser revogada
            sozinha. O segredo vai direto para o chaveiro do aparelho e não passa por esta página.
        </p>

        <form method="POST" action="{{ route('tura.pair.decide') }}" style="display:flex;gap:10px;margin-top:18px">
            @csrf
            <input type="hidden" name="client" value="tura">
            <input type="hidden" name="challenge" value="{{ $pair['challenge'] }}">
            <input type="hidden" name="challenge_method" value="S256">
            <input type="hidden" name="state" value="{{ $pair['state'] }}">
            <input type="hidden" name="label" value="{{ $pair['label'] }}">
            <input type="hidden" name="redirect" value="tura://pair">
            <button type="submit" name="decision" value="allow" class="btn btn-primary">Permitir</button>
            <button type="submit" name="decision" value="deny" class="btn">Negar</button>
        </form>
    </div>

@endsection
