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

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: #0a0a11; color: #c3c8d8; padding: 24px;
            font-family: 'Archivo', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            position: relative; overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute; top: -35%; right: -12%;
            width: 520px; height: 520px;
            background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 62%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute; bottom: -25%; left: -10%;
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(99,102,241,0.04) 0%, transparent 60%);
            pointer-events: none;
        }
        .login-card {
            width: 100%; max-width: 410px;
            background: rgba(20,20,30,0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(150,155,185,0.12); border-radius: 18px;
            padding: 40px 36px;
            box-shadow: 0 30px 80px -30px rgba(0,0,0,0.7),
                        inset 0 1px 0 rgba(255,255,255,0.03);
            position: relative; z-index: 1;
        }
        .brand-row { display: flex; align-items: center; gap: 11px; margin-bottom: 6px; }
        .brand-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.82rem; font-weight: 700; color: #fff;
            font-family: 'JetBrains Mono', monospace; letter-spacing: -0.02em;
        }
        .brand { font-family: 'JetBrains Mono', monospace; font-size: 1.55rem; font-weight: 700; letter-spacing: -0.02em; color: #f4f5fb; margin: 0; line-height: 1; }
        .brand span { color: #6366f1; }
        .subtitle { color: #8b93a7; font-size: 0.88rem; margin-bottom: 28px; margin-top: 4px; }

        label { display: block; font-size: 0.8rem; color: #94a3b8; margin-bottom: 7px; font-weight: 600; letter-spacing: 0.01em; }
        input[type=email], input[type=password] {
            width: 100%; background: rgba(11,11,17,0.8); border: 1px solid rgba(150,155,185,0.14); color: #f4f5fb;
            border-radius: 9px; padding: 12px 15px; font-size: 0.92rem; outline: none;
            font-family: inherit; transition: all 0.18s ease;
        }
        input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); background: rgba(11,11,17,1); }

        .remember { display: flex; align-items: center; gap: 9px; margin-bottom: 24px; font-size: 0.84rem; color: #8b93a7; cursor: pointer; }
        .remember input { margin: 0; accent-color: #6366f1; width: 16px; height: 16px; }

        button {
            width: 100%; background: linear-gradient(135deg, #6366f1, #818cf8); border: none; color: #fff;
            font-weight: 600; font-size: 0.95rem; border-radius: 9px; padding: 13px;
            cursor: pointer; transition: all 0.2s ease; font-family: inherit;
        }
        button:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(99,102,241,0.32); }
        button:active { transform: translateY(0); }

        .error-box {
            background: rgba(248,113,113,0.08); border: 1px solid rgba(248,113,113,0.25);
            color: #fca5a5; border-radius: 9px; padding: 11px 15px; font-size: 0.84rem;
            margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
        }
        .field-error { color: #fca5a5; font-size: 0.76rem; margin-top: -10px; margin-bottom: 16px; }

        .back {
            display: inline-flex; align-items: center; gap: 5px;
            text-align: center; margin-top: 24px; color: #8b93a7;
            font-size: 0.82rem; text-decoration: none;
            transition: color 0.18s ease;
        }
        .back:hover { color: #a5b4fc; }
    </style>
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
