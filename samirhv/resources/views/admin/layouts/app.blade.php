<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Painel') — Samirhv Admin</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ vasset('vendor/canvas/css/font-icons.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/admin/layout.css') }}">
    @stack('styles')
</head>
<body>
@php $r = request()->route()?->getName(); @endphp
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">S81</div>
            <div class="brand-text">samirhv<span>.</span></div>
        </div>

        <nav>
            <div class="nav-section">Geral</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $r === 'admin.dashboard' ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
            </a>

            <div class="nav-section">Conteúdo</div>
            <a href="{{ route('admin.projects.index') }}" class="nav-link {{ str_starts_with((string) $r, 'admin.projects') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-open"></i><span>Projetos</span>
            </a>

            <div class="nav-section">Monitoramento</div>
            <a href="{{ route('admin.monitor.index') }}" class="nav-link {{ $r === 'admin.monitor.index' ? 'active' : '' }}">
                <i class="fa-solid fa-code-compare"></i><span>Monitor</span>
            </a>
            <a href="{{ route('admin.audit.index') }}" class="nav-link {{ $r === 'admin.audit.index' ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i><span>Auditoria</span>
            </a>
            <a href="{{ route('admin.access-audit.index') }}" class="nav-link {{ $r === 'admin.access-audit.index' ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield"></i><span>Aud. de Acesso</span>
            </a>
            <a href="{{ route('admin.github-view.index') }}" class="nav-link {{ str_starts_with((string) $r, 'admin.github-view') ? 'active' : '' }}">
                <i class="fa-solid fa-code-branch"></i><span>GitHub View</span>
            </a>

            <div class="nav-section">AI</div>
            <a href="{{ route('admin.ai-memory.dashboard') }}" class="nav-link {{ str_starts_with((string) $r, 'admin.ai-memory') ? 'active' : '' }}">
                <i class="fa-solid fa-brain"></i><span>AI-MEMORY</span>
            </a>

            <div class="nav-section">Conta</div>
            <a href="{{ route('admin.profile') }}" class="nav-link {{ $r === 'admin.profile' ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i><span>Perfil</span>
            </a>
        </nav>

        <div class="sidebar-spacer"></div>

        <div class="sidebar-user">
            <div class="sidebar-user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="sidebar-user-email">{{ auth()->user()->email ?? '' }}</div>
                @if(!empty($appVersion))
                    <div class="sidebar-version" title="Versão do painel (version.md)">
                        <i class="fa-solid fa-code-branch"></i><span>v{{ $appVersion }}</span>
                    </div>
                @endif
            </div>
        </div>

        <a href="{{ route('home') }}" target="_blank" class="nav-link" style="margin-top: 6px;">
            <i class="fa-solid fa-up-right-from-square"></i><span>Ver site</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="width:100%;background:none;border:none;text-align:left;cursor:pointer;font:inherit;color:inherit;padding:9px 11px;">
                <i class="fa-solid fa-right-from-bracket"></i><span>Sair</span>
            </button>
        </form>
    </aside>

    <main class="main">
        <div class="topbar">
            <h1>@yield('title', 'Painel')</h1>
            <div>@yield('topbar-actions')</div>
        </div>
        <div class="content">
            @if(session('status'))
                <div class="admin-alert admin-alert-ok"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="admin-alert admin-alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>
