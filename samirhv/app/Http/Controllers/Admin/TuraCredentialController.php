<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Services\TuraCredentials;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Credenciais de sincronização do Tura Notes: criar, baixar uma vez, revogar.
 *
 * Controller fino — a conversa com o servidor vive em TuraCredentials. O que
 * fica aqui é a decisão de produto: o segredo é entregue como DOWNLOAD e não
 * como texto na página. Um segredo renderizado num HTML fica no histórico do
 * navegador, no cache e em qualquer captura de tela; o arquivo vai direto para
 * o gerenciador de senhas e para o campo "Arquivo de credencial" do aplicativo,
 * que é o formato que o app espera de qualquer jeito.
 */
class TuraCredentialController extends Controller
{
    /** O que uma credencial de sincronização precisa, e nada além disso. */
    public const PERMISSIONS = ['read', 'create', 'update', 'move', 'delete'];

    public function __construct(
        private readonly TuraCredentials $tura,
        private readonly AuditLogger $audit,
    ) {}

    public function index(): View
    {
        $credentials = $this->tura->list();

        return view('admin.tura.index', [
            'credentials' => $credentials ?? [],
            'available' => $credentials !== null,
            'unavailableReason' => $this->tura->unavailableReason(),
            'wrapper' => (string) config('tura.wrapper'),
            'workspace' => $this->tura->workspace(),
        ]);
    }

    public function store(Request $request): Response|RedirectResponse
    {
        /* O rótulo vira argumento de um shell remoto e nome de arquivo baixado.
           O wrapper valida de novo — é a fronteira que não pode confiar em
           ninguém — e esta validação existe para dar erro de formulário em vez
           de erro de script. */
        $data = $request->validate([
            'label' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{1,64}$/'],
        ], [], ['label' => 'rótulo']);

        $secret = $this->tura->create($data['label'], self::PERMISSIONS);

        if ($secret === null) {
            return back()->with('error', $this->tura->unavailableReason() ?? 'Não foi possível criar a credencial.');
        }

        // Rótulo e workspace, nunca o segredo — AuditLogger é explícito sobre isso.
        $this->audit->record('tura.credential.create', $data['label'],
            "Credencial de sincronização criada para o workspace {$this->tura->workspace()}.", 'tura');

        return response($secret, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$data['label'].'.secret"',
            // Um segredo não é cacheável em lugar nenhum do caminho.
            'Cache-Control' => 'no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'uuid'],
        ]);

        if (! $this->tura->revoke($data['id'])) {
            return back()->with('error', $this->tura->unavailableReason() ?? 'Não foi possível revogar.');
        }

        $this->audit->record('tura.credential.revoke', $data['id'],
            'Credencial de sincronização revogada.', 'tura');

        return back()->with('status', 'Credencial revogada. O aparelho perde o acesso na próxima requisição.');
    }
}
