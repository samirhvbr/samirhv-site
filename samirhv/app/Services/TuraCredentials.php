<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;

/**
 * Credenciais de sincronização do Tura Notes, criadas e revogadas a partir do
 * admin — para que registrar um aparelho seja baixar um arquivo, em vez de
 * abrir uma sessão ssh no servidor.
 *
 * COMO ISSO ALCANÇA O SERVIDOR, e por que não é o `notes-server` direto:
 * `/var/lib/notes-server` é 0700 e pertence ao usuário `notes`, então o
 * `www-data` não roda a CLI de jeito nenhum; e o `token create` grava o segredo
 * num arquivo NOVO em modo 0600 pertencendo a quem rodou — que o `www-data`
 * então não consegue ler. Quem resolve as duas metades é o
 * `tura-credential`, um script revisado do repositório do Tura
 * (`server/cotenant/tura-credential`), chamado por uma única linha de sudoers:
 *
 *     www-data ALL=(notes) NOPASSWD: /usr/local/bin/tura-credential
 *
 * O acoplamento a ESTE host é esperado — a mesma postura do módulo AI-MEMORY:
 * quando o binário não está lá, `isAvailable()` é falso, `unavailableReason()`
 * nomeia a falha real e a tela explica em vez de dar 500.
 *
 * O SEGREDO NUNCA É PERSISTIDO AQUI. Ele volta pelo pipe, vira o corpo de um
 * download e acaba. Não vai para sessão, cache, log nem banco: um segredo que
 * pode ser pedido de novo é um segredo com duas cópias.
 */
class TuraCredentials
{
    private ?string $reason = null;

    /** Segundos antes de desistir do servidor local. Ele é local; isto é rede parada. */
    private const TIMEOUT = 15;

    public function __construct(
        private readonly string $wrapper,
        private readonly string $runAs,
        private readonly string $workspace,
    ) {}

    public function workspace(): string
    {
        return $this->workspace;
    }

    public function unavailableReason(): ?string
    {
        return $this->reason;
    }

    /**
     * As credenciais existentes, já redigidas pelo próprio servidor: o
     * `token list` devolve rótulo, workspace, escopo, permissões e revogação —
     * nunca o segredo, que só existe no digest BLAKE3.
     *
     * @return array<int,array<string,mixed>>|null null quando indisponível.
     */
    public function list(): ?array
    {
        $output = $this->run(['list']);

        if ($output === null) {
            return null;
        }

        $rows = json_decode($output, true);

        if (! is_array($rows)) {
            $this->reason = 'O servidor respondeu algo que não é a lista JSON esperada.';

            return null;
        }

        return $rows;
    }

    /**
     * Cria uma credencial e devolve o segredo — uma vez, e só aqui.
     *
     * @param  list<string>  $permissions
     */
    public function create(string $label, array $permissions): ?string
    {
        $secret = $this->run(['create', $label, $this->workspace, implode(',', $permissions)]);

        return $secret === null || $secret === '' ? null : $secret;
    }

    public function revoke(string $id): bool
    {
        return $this->run(['revoke', $id]) !== null;
    }

    /** @param list<string> $arguments */
    private function run(array $arguments): ?string
    {
        $this->reason = null;

        if (! is_file($this->wrapper)) {
            $this->reason = "O script {$this->wrapper} não existe neste servidor.";

            return null;
        }

        /* sudo -n: nunca perguntar. Uma senha pedida a um processo do PHP-FPM
           não tem terminal para ser digitada — o request ficaria pendurado até
           o timeout em vez de falhar dizendo o que falta. */
        $command = ['sudo', '-n', '-u', $this->runAs, $this->wrapper, ...$arguments];

        try {
            $result = Process::timeout(self::TIMEOUT)->run($command);
        } catch (\Throwable $e) {
            $this->reason = 'Não foi possível executar o script: '.$e->getMessage();

            return null;
        }

        if ($result->failed()) {
            /* O erro do wrapper é operador-para-operador e não contém segredo:
               ele valida argumentos e nunca ecoa o que o `token create` grava. */
            $this->reason = trim($result->errorOutput()) ?: 'O script falhou sem dizer por quê (saída '.$result->exitCode().').';

            return null;
        }

        return trim($result->output());
    }
}
