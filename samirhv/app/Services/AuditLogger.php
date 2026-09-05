<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Grava a trilha de auditoria de ações do admin: quem fez, o quê, sobre qual
 * alvo, de qual IP/user-agent. NUNCA registra senha/token — quem chama deve
 * passar apenas descrição segura.
 */
class AuditLogger
{
    /**
     * @param  string  $event  Identificador do evento (ex.: project.create).
     * @param  int|string|null  $subjectId  Id do alvo.
     * @param  string  $description  Texto livre, sem segredos.
     * @param  string|null  $subjectType  Tipo do alvo (default: project).
     */
    public function record(string $event, int|string|null $subjectId, string $description, ?string $subjectType = 'project'): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'event' => $event,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId !== null ? (string) $subjectId : null,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 1000),
                'description' => $description,
            ]);
        } catch (\Throwable $e) {
            Log::warning('audit_log_failed', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
