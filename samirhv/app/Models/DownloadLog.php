<?php

namespace App\Models;

use App\Services\UserAgentParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trilha de auditoria de downloads. Uma linha por GET concluído (não-parcial)
 * em /d/{file}. Sem pruning (retenção definitiva por decisão de produto).
 */
class DownloadLog extends Model
{
    protected $fillable = [
        'project_file_id', 'user_id', 'ip', 'user_agent', 'referer',
        'method', 'is_bot', 'locale',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(ProjectFile::class, 'project_file_id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** "Navegador · SO" (ou "Bot" / "—") a partir do user-agent. */
    public function getClientLabelAttribute(): string
    {
        $ua = (string) ($this->user_agent ?? '');
        if (trim($ua) === '') {
            return '—';
        }
        $info = app(UserAgentParser::class)->parse($ua, $this->ip);

        return $info['is_bot'] ? 'Bot' : $info['browser'].' · '.$info['os'];
    }
}
