@extends('admin.layouts.app')

@section('title', 'Tura — pedido inválido')

@section('content')

    <div class="admin-card" style="max-width:760px">
        <h2><i class="fa-solid fa-triangle-exclamation"></i> Pedido inválido</h2>
        <p style="color:#cbd5e1;line-height:1.6">
            Este endereço não é um pedido do aplicativo Tura Notes. Volte ao aplicativo e comece de
            novo em <b>Entrar com o seu site</b>; nada foi criado.
        </p>
    </div>

@endsection
