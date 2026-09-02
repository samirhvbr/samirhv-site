{{-- Aviso de degradação: renderizado por TODA aba quando o SQLite do ai-memory
     não está acessível. É a explicação, na própria UI, de "por que parou".
     `$unavailableReason` vem de AiMemoryDatabase::unavailableReason() e nomeia
     a falha real (permissão, caminho, driver, esquema) — ver docs/AI-MEMORY.md. --}}
<div class="admin-card" style="border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.05)">
    <h2 style="color:#fcd34d;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-triangle-exclamation"></i> AI-MEMORY indisponível neste servidor
    </h2>

    <p style="color:#e2e8f0;line-height:1.6">
        Esta tela consulta, <b>somente leitura</b>, o banco <b>SQLite do ai-memory</b> — a memória
        de longo prazo dos agentes de código. Esse arquivo pertence ao processo do
        <code>ai-memory</code> e é lido <b>direto do sistema de arquivos deste servidor</b>.
        No momento ele <b>não está acessível</b>, então não há o que mostrar.
    </p>

    @if(! empty($unavailableReason))
        <p class="card-sub" style="margin:14px 0 6px">O que falhou agora:</p>
        <div style="overflow-x:auto;line-height:1.6;color:#fde68a">{{ $unavailableReason }}</div>
    @endif

    <p class="card-sub" style="margin:14px 0 6px">Caminho configurado (<code>AI_MEMORY_SQLITE_PATH</code>):</p>
    <div style="overflow-x:auto"><code style="color:#a5b4fc">{{ $aimemoryPath ?: '(vazio)' }}</code></div>

    <p class="card-sub" style="margin:18px 0 6px">Causas prováveis:</p>
    <ul class="an-list" style="max-width:760px">
        <li><span>O <code>www-data</code> <b>não tem permissão de escrita no diretório</b> do banco. Não é engano:
            o banco está em modo <b>WAL</b> e qualquer leitor precisa poder criar os arquivos
            <code>-shm</code>/<code>-wal</code> quando o ai-memory não está com eles abertos.</span></li>
        <li><span>O ai-memory <b>mudou de layout na atualização</b> (a 2.x instala em
            <code>/opt/ai-memory/data/db</code>; a 1.x usava o volume Docker <code>ai-memory-data</code>)
            e o <code>AI_MEMORY_SQLITE_PATH</code> ficou apontando para o caminho antigo.</span></li>
        <li><span>O app <b>saiu do servidor</b> onde o ai-memory roda — o arquivo só existe naquele host.</span></li>
        <li><span>A extensão <code>pdo_sqlite</code> do PHP não está instalada neste ambiente.</span></li>
    </ul>

    <p class="card-sub" style="margin-top:16px">
        O acoplamento ao host é <b>esperado</b>; o 500 não era. Roteiro de diagnóstico e os comandos de
        permissão estão em <code>docs/AI-MEMORY.md</code>.
    </p>
</div>
