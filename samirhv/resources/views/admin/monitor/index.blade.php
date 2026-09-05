@extends('admin.layouts.app')

@section('title', 'Monitor')

@section('topbar-actions')
    <a href="{{ route('admin.monitor.index', ['refresh' => 1]) }}" class="admin-btn admin-btn-primary">
        <i class="fa-solid fa-arrows-rotate"></i> Verificar agora
    </a>
@endsection


@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/admin/monitor.css') }}">
@endpush

@section('content')

    @isset($refreshFloor)
        @if($refreshFloor)
            <div class="mon-throttle" role="status">
                <i class="fa-solid fa-hourglass-half"></i>
                Checagem no GitHub feita há pouco — a tela abaixo é a última leitura.
                Dá para verificar de novo em {{ $refreshFloor }}s.
                <span class="mon-throttle__why">A API sem autenticação dá 60 requisições por hora, e cada projeto rastreado gasta uma.</span>
            </div>
        @endif
    @endisset

    @php
        // Rótulo/estilo de cada status (montados no controller).
        $badges = [
            'outdated'   => ['badge-warn',   'fa-arrow-up',        'Desatualizado'],
            'up_to_date' => ['badge-ok',     'fa-check',           'Em dia'],
            'ahead'      => ['badge-accent', 'fa-arrow-down',      'À frente'],
            'no_local'   => ['badge-muted',  'fa-circle-question', 'Sem versão local'],
            'unknown'    => ['badge-muted',  'fa-circle-question', 'Não comparável'],
            'error'      => ['badge-danger', 'fa-triangle-exclamation', 'Erro'],
            'untracked'  => ['badge-muted',  'fa-minus',           'Sem upstream'],
        ];
        // Motivos de erro do GithubReleaseChecker → texto curto.
        $errText = [
            'rate_limit'          => 'Limite da API do GitHub — tente mais tarde',
            'repo_nao_encontrado' => 'Repositório não encontrado',
            'sem_release_ou_tag'  => 'Sem release nem tag',
            'rede'                => 'Falha de rede',
            'repo_invalido'       => 'Repositório inválido',
        ];
    @endphp

    <div class="admin-stats-grid">
        <div class="admin-stat">
            <div class="label">Projetos monitorados</div>
            <div class="value">{{ $summary['tracked'] }}</div>
        </div>
        <div class="admin-stat">
            <div class="label">Desatualizados</div>
            <div class="value" style="color:{{ $summary['outdated'] > 0 ? 'var(--warn)' : '#f1f5f9' }}">{{ $summary['outdated'] }}</div>
        </div>
        <div class="admin-stat">
            <div class="label">Com erro de checagem</div>
            <div class="value" style="color:{{ $summary['errors'] > 0 ? 'var(--danger)' : '#f1f5f9' }}">{{ $summary['errors'] }}</div>
        </div>
    </div>

    @if($summary['outdated'] > 0)
        <div class="admin-alert admin-alert-warn">
            <i class="fa-solid fa-arrow-up-right-dots"></i>
            {{ $summary['outdated'] }}
            {{ \Illuminate\Support\Str::plural('projeto', $summary['outdated']) }}
            com versão nova disponível no upstream.
        </div>
    @endif

    <div class="admin-card">
        <h2>Versões dos projetos</h2>
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Projeto</th>
                        <th>Nossa versão</th>
                        <th>Upstream</th>
                        <th>Status</th>
                        <th>Publicado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            [$cls, $icon, $label] = $badges[$row['status']] ?? $badges['untracked'];
                            $project = $row['project'];
                        @endphp
                        <tr class="{{ $row['tracked'] ? '' : 'mon-row-untracked' }}">
                            <td>
                                <div class="mon-proj">
                                    <i class="{{ $project->icon ?: 'fa-solid fa-cube' }}"></i>
                                    <div>
                                        <div>{{ $project->title }}</div>
                                        @if($row['tracked'])
                                            <div class="repo">
                                                <a href="{{ $project->upstream_url }}" target="_blank" rel="noopener">
                                                    <i class="fa-solid fa-code-branch"></i> {{ $project->upstream_repo }}
                                                </a>
                                            </div>
                                        @else
                                            <div class="repo">
                                                <a href="{{ route('admin.projects.edit', $project) }}">definir upstream…</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($row['local'])
                                    <span class="mon-ver">v{{ ltrim($row['local'], 'vV') }}</span>
                                @else
                                    <span class="mon-ver dim">—</span>
                                @endif
                            </td>
                            <td>
                                @if($row['upstream'])
                                    <span class="mon-up">
                                        <a href="{{ $row['upstream_url'] }}" target="_blank" rel="noopener" class="mon-ver">v{{ $row['upstream'] }}</a>
                                        @if($row['source'])<span class="mon-src">{{ $row['source'] }}</span>@endif
                                    </span>
                                @else
                                    <span class="mon-ver dim">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $cls }}"><i class="fa-solid {{ $icon }}"></i> {{ $label }}</span>
                                @if($row['status'] === 'error')
                                    <div class="mon-note">{{ $errText[$row['error']] ?? $row['error'] }}</div>
                                @endif
                            </td>
                            <td>
                                @if($row['published_at'])
                                    <span class="mon-when">{{ \Illuminate\Support\Carbon::parse($row['published_at'])->translatedFormat('d/M/Y') }}</span>
                                @else
                                    <span class="mon-when">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:var(--dim);padding:28px">Nenhum projeto cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mon-legend">
            <span><b>Nossa versão:</b> maior versão entre os arquivos publicados do projeto.</span>
            <span><b>Upstream:</b> última release (ou tag) no GitHub, consulta pública cacheada por 1h.</span>
            <span><b>Verificar agora</b> refaz a consulta ignorando o cache.</span>
        </div>
    </div>

@endsection
