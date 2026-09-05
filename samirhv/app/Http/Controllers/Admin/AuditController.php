<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadLog;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Auditoria de Downloads + Analytics. KPIs do dia, gráficos por dia (visitas/
 * downloads), top páginas/IPs, dispositivos/navegadores, downloads por projeto,
 * bots à parte, top arquivos e a tabela bruta de registros (com filtros).
 */
class AuditController extends Controller
{
    /** Teto de opções no select de arquivos. Ver o comentário em index(). */
    private const FILTER_OPTIONS_LIMIT = 500;

    public function index(Request $request, AnalyticsService $analytics): View
    {
        $filters = $request->validate([
            'ip' => ['nullable', 'string', 'max:45'],
            'file' => ['nullable', 'integer'],
            'project' => ['nullable', 'integer'],
            'days' => ['nullable', 'integer', 'in:1,7,30,90'],
        ]);

        $query = DownloadLog::with('file.project')->latest();

        if (! empty($filters['ip'])) {
            $query->where('ip', 'like', $filters['ip'].'%');
        }
        if (! empty($filters['file'])) {
            $query->where('project_file_id', $filters['file']);
        }
        if (! empty($filters['project'])) {
            $query->whereHas('file', fn ($q) => $q->where('project_id', $filters['project']));
        }
        if (! empty($filters['days'])) {
            $query->where('created_at', '>=', now()->subDays((int) $filters['days']));
        }

        $logs = $query->paginate(50)->withQueryString();

        // Cards de resumo (visão geral, independem dos filtros).
        $stats = [
            'today' => $analytics->downloadsToday(),
            'week' => DownloadLog::where('is_bot', false)->where('created_at', '>=', now()->subDays(7))->count(),
            'month' => DownloadLog::where('is_bot', false)->where('created_at', '>=', now()->subDays(30))->count(),
            'unique_ips' => DownloadLog::where('is_bot', false)->where('created_at', '>=', now()->subDays(30))->distinct('ip')->count('ip'),
        ];

        /* Opções dos selects de filtro.
           Os arquivos são carregados por projeto e limitados: era
           `ProjectFile::with('project')->get()`, ou seja, TODA linha da tabela
           trazida para dentro de um <select>, crescendo sem teto conforme se
           publica build. O limite é generoso o bastante para o select continuar
           completo na prática e existe para que a página não pare de abrir no
           dia em que não estiver. */
        $projects = Project::orderBy('title')->get(['id', 'title']);
        $files = ProjectFile::with('project:id,title')
            ->orderBy('project_id')
            ->orderBy('label')
            ->limit(self::FILTER_OPTIONS_LIMIT)
            ->get(['id', 'label', 'project_id']);

        // Métricas do AnalyticsService.
        $cards = $analytics->cards();
        $visitsByDay = $analytics->visitsByDay(14);
        $downloadsByDay = $analytics->downloadsByDay(14);
        $topPages = $analytics->topPages(10);
        $topIps = $analytics->topIps(10);
        $byDevice = $analytics->byDevice();
        $byBrowser = $analytics->byBrowser();
        $downloadsByProject = $analytics->downloadsByProject();
        $topFiles = $analytics->topFiles(8);
        $bots = $analytics->bots(30);

        return view('admin.audit.index', compact(
            'logs', 'stats', 'filters', 'projects', 'files',
            'cards', 'visitsByDay', 'downloadsByDay', 'topPages', 'topIps',
            'byDevice', 'byBrowser', 'downloadsByProject', 'topFiles', 'bots',
        ));
    }
}
