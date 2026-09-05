<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Entrar — Samirhv</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- The font was pulled with `@import` from inside the <style> below. An
         @import is discovered only after the stylesheet that contains it has
         been parsed, and it blocks rendering while it resolves — the two
         requests run one after the other instead of together. A <link> with its
         preconnects starts the connection immediately and in parallel.

         This is the ONLY page on the site that loads a Google font, which is
         also why the public layout no longer preconnects to one. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">

    <link rel="stylesheet" href="{{ vasset('css/auth/login.css') }}">
</head>
<body>
    <div class="login-card">
        <div class="brand-row">
            <div class="brand-icon">S81</div>
            <div class="brand">samirhv<span>.</span></div>
        </div>
        <div class="subtitle">Painel administrativo</div>

        @if(session('error'))
            <div class="error-box"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif
        @if($errors->has('email'))
            <div class="error-box"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <label class="remember">
                <input type="checkbox" name="remember" value="1"> Manter conectado
            </label>

            <button type="submit">Entrar</button>
        </form>

        <a href="{{ route('home') }}" class="back"><i class="fa-solid fa-arrow-left" style="font-size: 0.7rem;"></i> Voltar ao site</a>
    </div>
</body>
</html>
