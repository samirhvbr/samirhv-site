{{-- Card de um repo no dashboard. Porte de dashboard/_card.html.erb.
     Vars: repo, stats, chipDays. --}}
@php
    $ci = $stats['ci_conclusion'];
    $dot = $ci === 'success'
        ? 'ok'
        : (in_array($ci, ['failure', 'timed_out', 'startup_failure'], true) ? 'bad' : ($ci ? 'warn' : 'none'));
@endphp
<div class="gh-card">
    <div class="gh-card__head">
        <div style="min-width:0">
            <a href="{{ route('admin.github-view.repos.show', ['owner' => $repo->owner, 'name' => $repo->name]) }}" class="gh-card__name">{{ $repo->fullName() }}</a>
            @if($repo->description)<p class="gh-card__desc">{{ $repo->description }}</p>@endif
        </div>
        <span class="gh-dot gh-dot--{{ $dot }}" title="CI: {{ $ci ?: 'sem runs' }}"></span>
    </div>

    <div class="gh-card__chips" title="commits por dia, últimos {{ $chipDays }} dias">
        @foreach($stats['daily_counts'] as $count)
            <span class="gh-chip" style="background-color: {{ \App\Services\GitHub\Visualizations\RepositoryOverview::heatColor((int) $count, (int) $stats['max_daily']) }}"></span>
        @endforeach
    </div>

    <p class="gh-card__ago">
        último commit: {{ $stats['last_committed_at'] ? $stats['last_committed_at']->diffForHumans() : 'nunca sincronizado' }}
    </p>

    <div class="gh-card__foot">
        <span class="gh-card__stats">
            {{ number_format((int) $stats['total_commits'], 0, ',', '.') }} commits
            <span class="gh-add-n">+{{ number_format((int) $stats['total_additions'], 0, ',', '.') }}</span>
            <span class="gh-del-n">−{{ number_format((int) $stats['total_deletions'], 0, ',', '.') }}</span>
        </span>
        {{-- Um repo em 'syncing' termina no worker, não nesta requisição: sem
             polling o cartão diz "syncing" até alguém recarregar a página. O
             endpoint de status existia desde o port e não tinha cliente — este
             é o cliente. Só cartões em 'syncing' declaram a url, então uma tela
             de repos sincronizados não faz uma requisição sequer. --}}
        <span class="gh-badge gh-badge--{{ $repo->sync_status }}"
              role="status"
              @if($repo->isSyncing())
                  data-gh-sync
                  data-url="{{ route('admin.github-view.repos.status', ['owner' => $repo->owner, 'name' => $repo->name]) }}"
                  data-every="5"
              @endif
        >
            @switch($repo->sync_status)
                @case('synced')<i class="fa-solid fa-circle-check"></i> synced @break
                @case('syncing')<i class="fa-solid fa-rotate"></i> syncing @break
                @case('failed')<i class="fa-solid fa-circle-exclamation"></i> failed @break
                @default<i class="fa-solid fa-clock"></i> pending
            @endswitch
        </span>
    </div>
</div>
