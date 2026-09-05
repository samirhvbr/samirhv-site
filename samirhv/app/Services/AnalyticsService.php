<?php

namespace App\Services;

use App\Models\DownloadLog;
use App\Models\PageView;
use App\Models\User;
use Carbon\Carbon;

/**
 * Métricas da auditoria/analytics. Storage é UTC; tudo aqui é exibido/agrupado no fuso
 * LOCAL do servidor (America/Sao_Paulo, -3 fixo — Brasil sem horário de verão). Bots
 * SEMPRE contam à parte: as séries/painéis "legítimos" filtram is_bot = false.
 */
class AnalyticsService
{
    public const TZ = 'America/Sao_Paulo';

    private const OFFSET_HOURS = 3;   // -3 fixo

    /** Expressão SQL para a DATA local (created_at é UTC → subtrai o offset). */
    private function localDateExpr(string $column = 'created_at'): string
    {
        return "DATE(`{$column}` - INTERVAL ".self::OFFSET_HOURS.' HOUR)';
    }

    /** Início do dia local de hoje, como instante UTC (para WHERE created_at >= ...). */
    private function todayStart(): Carbon
    {
        return Carbon::today(self::TZ)->utc();
    }

    private function daysAgoStart(int $days): Carbon
    {
        return Carbon::today(self::TZ)->subDays($days)->utc();
    }

    /**
     * Downloads legítimos de HOJE, no fuso local.
     *
     * Público e usado por todas as telas que mostram esse número, porque ele
     * aparece em três lugares e chegou a ser calculado de duas formas: aqui,
     * com a virada do dia em America/Sao_Paulo, e nos controllers, com
     * `whereDate(created_at, today())` — que é a data em UTC, já que
     * config/app.php fixa o fuso da aplicação em UTC. Entre 21h e meia-noite
     * as duas contas discordam, e o painel de auditoria mostra as duas na
     * mesma tela. Uma definição só, num lugar só.
     */
    public function downloadsToday(): int
    {
        return (int) DownloadLog::where('is_bot', false)
            ->where('created_at', '>=', $this->todayStart())
            ->count();
    }

    /** Cartões do topo (de HOJE, fuso local). Acessos = visitantes únicos sem bot. */
    public function cards(): array
    {
        $t = $this->todayStart();

        return [
            'visits_today' => (int) PageView::where('is_bot', false)->where('created_at', '>=', $t)->distinct('ip')->count('ip'),
            'logins_today' => (int) User::whereNotNull('last_login_at')->where('last_login_at', '>=', $t)->count(),
            'downloads_today' => $this->downloadsToday(),
            'bots_today' => (int) PageView::where('is_bot', true)->where('created_at', '>=', $t)->count(),
        ];
    }

    /** Visitantes ÚNICOS (distinct IP) por dia, sem bot. Chave = 'Y-m-d' local. */
    public function visitsByDay(int $days = 14): array
    {
        $rows = PageView::where('is_bot', false)
            ->where('created_at', '>=', $this->daysAgoStart($days - 1))
            ->selectRaw($this->localDateExpr().' as d, COUNT(DISTINCT ip) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        return $this->fillDays($rows, $days);
    }

    /** Downloads legítimos (sem bot) por dia. */
    public function downloadsByDay(int $days = 14): array
    {
        $rows = DownloadLog::where('is_bot', false)
            ->where('created_at', '>=', $this->daysAgoStart($days - 1))
            ->selectRaw($this->localDateExpr().' as d, COUNT(*) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        return $this->fillDays($rows, $days);
    }

    /** Preenche os N dias (com zeros) na ordem cronológica. */
    private function fillDays($rows, int $days): array
    {
        $out = [];
        $cursor = Carbon::today(self::TZ)->subDays($days - 1);
        for ($i = 0; $i < $days; $i++) {
            $key = $cursor->toDateString();
            $out[$key] = (int) ($rows[$key] ?? 0);
            $cursor->addDay();
        }

        return $out;
    }

    /** Páginas mais acessadas (sem bot, janela em dias). */
    public function topPages(int $limit = 10, int $days = 30): array
    {
        return PageView::where('is_bot', false)
            ->where('created_at', '>=', $this->daysAgoStart($days))
            ->selectRaw('path, COUNT(*) as total')
            ->groupBy('path')->orderByDesc('total')->limit($limit)
            ->get()
            ->map(fn ($r) => ['path' => $r->path, 'total' => (int) $r->total])
            ->all();
    }

    /** IPs que mais acessaram (sem bot): total + último acesso. */
    public function topIps(int $limit = 10, int $days = 30): array
    {
        return PageView::where('is_bot', false)
            ->where('created_at', '>=', $this->daysAgoStart($days))
            ->selectRaw('ip, COUNT(*) as total, MAX(created_at) as last_at')
            ->groupBy('ip')->orderByDesc('total')->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'ip' => $r->ip,
                'total' => (int) $r->total,
                'last_at' => Carbon::parse($r->last_at),
            ])
            ->all();
    }

    /** Dispositivos (sem bot): desktop|mobile|tablet → total. */
    public function byDevice(int $days = 30): array
    {
        return PageView::where('is_bot', false)
            ->where('created_at', '>=', $this->daysAgoStart($days))
            ->selectRaw('device, COUNT(*) as total')
            ->groupBy('device')->orderByDesc('total')
            ->pluck('total', 'device')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /** Navegadores (sem bot). */
    public function byBrowser(int $days = 30): array
    {
        return PageView::where('is_bot', false)
            ->where('created_at', '>=', $this->daysAgoStart($days))
            ->selectRaw('browser, COUNT(*) as total')
            ->groupBy('browser')->orderByDesc('total')
            ->pluck('total', 'browser')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /** Downloads legítimos (sem bot) agrupados por projeto. [titulo => total]. */
    public function downloadsByProject(int $days = 30): array
    {
        return DownloadLog::where('download_logs.is_bot', false)
            ->where('download_logs.created_at', '>=', $this->daysAgoStart($days))
            ->join('project_files', 'project_files.id', '=', 'download_logs.project_file_id')
            ->join('projects', 'projects.id', '=', 'project_files.project_id')
            ->selectRaw('projects.title as title, COUNT(*) as total')
            ->groupBy('projects.title')->orderByDesc('total')
            ->pluck('total', 'title')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /** Arquivos mais baixados (sem bot) na janela: [{label, project, total}]. */
    public function topFiles(int $limit = 8, int $days = 30): array
    {
        return DownloadLog::where('download_logs.is_bot', false)
            ->where('download_logs.created_at', '>=', $this->daysAgoStart($days))
            ->join('project_files', 'project_files.id', '=', 'download_logs.project_file_id')
            ->leftJoin('projects', 'projects.id', '=', 'project_files.project_id')
            ->selectRaw('project_files.label as label, projects.title as project, COUNT(*) as total')
            ->groupBy('project_files.label', 'projects.title')
            ->orderByDesc('total')->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'label' => $r->label,
                'project' => $r->project,
                'total' => (int) $r->total,
            ])
            ->all();
    }

    /** Bots — SEMPRE separados das métricas legítimas (visitas + downloads + top UAs). */
    public function bots(int $days = 30): array
    {
        $start = $this->daysAgoStart($days);

        $rows = PageView::where('is_bot', true)
            ->where('created_at', '>=', $this->daysAgoStart(13))
            ->selectRaw($this->localDateExpr().' as d, COUNT(*) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        return [
            'visits' => (int) PageView::where('is_bot', true)->where('created_at', '>=', $start)->count(),
            'downloads' => (int) DownloadLog::where('is_bot', true)->where('created_at', '>=', $start)->count(),
            'by_day' => $this->fillDays($rows, 14),
            'top' => PageView::where('is_bot', true)->where('created_at', '>=', $start)
                ->selectRaw('user_agent, COUNT(*) as total')
                ->groupBy('user_agent')->orderByDesc('total')->limit(8)
                ->get()
                ->map(fn ($r) => ['ua' => $r->user_agent ?: '(sem user-agent)', 'total' => (int) $r->total])
                ->all(),
        ];
    }
}
