<?php

namespace App\Models;

use App\Support\InstallCommand;
use App\Support\OsDetector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ProjectFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'label', 'filename', 'original_name', 'version',
        'size', 'sha256', 'is_available', 'downloads_count',
        'os', 'arch', 'file_type', 'released_at',
    ];

    protected $casts = [
        'size' => 'integer',
        'downloads_count' => 'integer',
        'is_available' => 'boolean',
        'released_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Índice dos arquivos presentes no disco, montado UMA vez por request.
     *
     * @var array<string, true>|null
     */
    private static ?array $mirrored = null;

    /**
     * O arquivo está realmente no disco de downloads?
     *
     * Era um `exists()` por arquivo — ou seja, uma syscall por linha renderizada.
     * Uma página /p/{slug} com 20 builds fazia 20 idas ao disco, e como não é
     * query nenhuma delas aparece no log de queries: o custo era invisível.
     * Agora é uma listagem recursiva só, memoizada pelo request.
     *
     * A memoização é por processo (PHP-FPM: um request). Quem ESCREVE no disco
     * chama `forgetMirrored()` para não ler um índice anterior à própria escrita.
     */
    public function getIsMirroredAttribute(): bool
    {
        if (! $this->filename) {
            return false;
        }

        self::$mirrored ??= array_fill_keys(Storage::disk('downloads')->allFiles(), true);

        return isset(self::$mirrored[$this->filename]);
    }

    /** Invalida o índice acima. Chamado por quem grava ou apaga no disco. */
    public static function forgetMirrored(): void
    {
        self::$mirrored = null;
    }

    /** Tamanho legível (B/KB/MB/GB). */
    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes <= 0) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), $i === 0 ? 0 : 1).' '.$units[$i];
    }

    /** Primeiros 16 hex do sha256, para exibição. */
    public function getShortHashAttribute(): ?string
    {
        return $this->sha256 ? substr($this->sha256, 0, 16) : null;
    }

    /** Data efetiva de lançamento (released_at, com fallback para created_at). */
    public function getEffectiveDateAttribute(): ?Carbon
    {
        return $this->released_at ?? $this->created_at;
    }

    /** Rótulo do SO para exibição (Linux/Windows/macOS). */
    public function getOsLabelAttribute(): ?string
    {
        return $this->os ? OsDetector::label($this->os) : null;
    }

    /** Comando de instalação + verificação por (os, file_type). */
    public function getInstallCommandAttribute(): array
    {
        return InstallCommand::for($this);
    }
}
