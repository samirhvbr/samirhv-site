{{-- Degradação, mesma postura do módulo AI-MEMORY: esta tela depende de algo
     que vive FORA do Laravel, neste mesmo host, e quando não está lá ela
     explica em vez de dar 500. `$unavailableReason` vem de
     TuraCredentials::unavailableReason() e nomeia a falha real. --}}
<div class="admin-card" style="border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.05)">
    <h2 style="color:#fcd34d;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-triangle-exclamation"></i> Servidor do Tura indisponível neste host
    </h2>

    <p style="color:#e2e8f0;line-height:1.6">
        Esta tela cria e revoga credenciais de sincronização chamando o
        <b>notes-server</b> que roda <b>nesta máquina</b>, em loopback, atrás do vhost
        <code>tura.samirhv.com.br</code>. Ela não fala com ele direto: o diretório de dados é
        <code>0700</code> do usuário <code>notes</code>, e o segredo é gravado em modo <code>0600</code>
        por quem o cria — o <code>www-data</code> não conseguiria nem rodar a CLI, nem ler o arquivo.
        Quem resolve as duas metades é o script revisado <code>tura-credential</code>.
    </p>

    @if(! empty($unavailableReason))
        <p class="card-sub" style="margin:14px 0 6px">O que falhou agora:</p>
        <div style="overflow-x:auto;line-height:1.6;color:#fde68a">{{ $unavailableReason }}</div>
    @endif

    <p class="card-sub" style="margin:14px 0 6px">Script configurado (<code>TURA_CREDENTIAL_WRAPPER</code>):</p>
    <div style="overflow-x:auto"><code style="color:#a5b4fc">{{ $wrapper ?: '(vazio)' }}</code></div>

    <p class="card-sub" style="margin:18px 0 6px">O que costuma faltar, nesta ordem:</p>
    <ul class="an-list" style="max-width:820px">
        <li><span>O <b>notes-server não foi implantado</b> neste host. É o caso normal até a
            primeira vez: a sequência ordenada está em <code>.continue/0.6-sync.md</code>, no
            repositório do Tura.</span></li>
        <li><span>O <b>script não está instalado</b>:
            <code>install -m 0755 server/cotenant/tura-credential /usr/local/bin/</code>.</span></li>
        <li><span>A <b>linha de sudoers não existe</b>. Sem ela o <code>sudo -n</code> falha na hora,
            de propósito — uma senha pedida ao PHP-FPM não tem terminal para ser digitada, e o
            request ficaria pendurado em vez de falhar dizendo o que falta:<br>
            <code>www-data ALL=(notes) NOPASSWD: /usr/local/bin/tura-credential</code></span></li>
        <li><span>O <b>workspace não existe</b> no servidor:
            <code>sudo -u notes NOTES_SERVER_DATA=/var/lib/notes-server notes-server workspace create {{ $workspace }}</code>.</span></li>
    </ul>

    <p class="card-sub" style="margin-top:16px">
        O acoplamento a este host é <b>esperado</b>. O contrato do lado do servidor está em
        <code>docs/SERVER-0.5.md</code>, seção <i>Minting credentials from a web administration screen</i>.
    </p>
</div>
