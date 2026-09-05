<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AuthEvent;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Auditoria de Acesso — duas frentes, em abas (?tab=):
 *  - actions : ações do admin sobre projetos/arquivos (tabela activity_logs).
 *  - logins  : acessos ao painel — login/falha/logout (tabela auth_events).
 */
class AccessAuditController extends Controller
{
    private const TABS = ['actions', 'logins'];

    public function __construct(private readonly AnalyticsService $analytics) {}

    public function index(Request $request): View
    {
        /* Os filtros são validados aqui, uma vez, para as duas abas — do mesmo
           jeito que o AuditController ao lado valida os seus. Sem isso, `?user=abc`
           virava `where('user_id', 'abc')` em silêncio e `?days=99999` era aceito:
           não é injeção (o Eloquent faz bind), mas é um filtro que responde
           qualquer coisa em vez de dizer que o pedido está errado. `days` aceita
           só os quatro valores que o próprio select oferece. */
        $filters = $request->validate([
            'event' => ['nullable', 'string', 'max:60'],
            'user' => ['nullable', 'integer'],
            'ip' => ['nullable', 'string', 'max:45'],
            'days' => ['nullable', 'integer', 'in:1,7,30,90'],
        ]);

        $tab = in_array($request->input('tab'), self::TABS, true) ? $request->input('tab') : 'actions';

        $data = match ($tab) {
            'logins' => $this->loginsTab($filters),
            default => $this->actionsTab($filters),
        };

        return view('admin.access-audit.index', $data + ['tab' => $tab]);
    }

    /**
     * Ações do admin sobre projetos/arquivos (activity_logs).
     *
     * @param  array<string, mixed>  $filters  já validado em index()
     */
    private function actionsTab(array $filters): array
    {
        $filters += ['event' => null, 'user' => null, 'ip' => null, 'days' => null];

        $query = $this->applyFilters(ActivityLog::with('user')->latest(), $filters);

        if (filled($filters['user'])) {
            $query->where('user_id', $filters['user']);
        }

        $logs = $query->paginate(50)->withQueryString();

        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::where('created_at', '>=', $this->analytics->todayStart())->count(),
            'admins' => ActivityLog::whereNotNull('user_id')->distinct()->count('user_id'),
            'ips' => ActivityLog::whereNotNull('ip_address')->distinct()->count('ip_address'),
        ];

        $events = ActivityLog::query()->select('event')->distinct()->orderBy('event')->pluck('event');
        $adminIds = ActivityLog::query()->whereNotNull('user_id')->distinct()->pluck('user_id');
        $admins = User::whereIn('id', $adminIds)->orderBy('email')->get(['id', 'name', 'email']);

        return compact('logs', 'stats', 'filters', 'events', 'admins');
    }

    /**
     * Acessos ao painel (auth_events).
     *
     * @param  array<string, mixed>  $filters  já validado em index()
     */
    private function loginsTab(array $filters): array
    {
        $filters += ['event' => null, 'ip' => null, 'days' => null];

        $query = $this->applyFilters(AuthEvent::with('user')->latest('created_at'), $filters);

        $logs = $query->paginate(50)->withQueryString();

        $today = $this->analytics->todayStart();
        $stats = [
            'logins' => AuthEvent::where('event', 'login')->count(),
            'logins_today' => AuthEvent::where('event', 'login')->where('created_at', '>=', $today)->count(),
            'failed' => AuthEvent::where('event', 'failed')->count(),
            'failed_today' => AuthEvent::where('event', 'failed')->where('created_at', '>=', $today)->count(),
        ];

        return compact('logs', 'stats', 'filters');
    }

    /**
     * Os filtros que as duas abas têm em comum: `event`, `ip` e `days`.
     *
     * Eram trinta linhas espelhadas em `actionsTab()` e `loginsTab()`, sobre
     * tabelas diferentes mas com colunas de mesmo nome. Duas cópias do mesmo
     * filtro divergem no dia em que alguém corrige uma delas.
     *
     * `user` fica de fora de propósito: só `activity_logs` tem essa coluna.
     *
     * @param  array<string, mixed>  $filters  já validado em index()
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (filled($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (filled($filters['ip'])) {
            $query->where('ip_address', 'like', $filters['ip'].'%');
        }

        if (filled($filters['days'])) {
            $query->where('created_at', '>=', now()->subDays((int) $filters['days']));
        }

        return $query;
    }
}
