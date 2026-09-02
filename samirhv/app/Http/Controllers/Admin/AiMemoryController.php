<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiMemoryStatSnapshot;
use App\Services\AiMemory\AiMemoryDatabase;
use App\Services\AiMemory\DashboardSummary;
use App\Services\AiMemory\HandoffRepository;
use App\Services\AiMemory\ObservationRepository;
use App\Services\AiMemory\PageRepository;
use App\Services\AiMemory\ProjectRepository;
use App\Services\AiMemory\SearchRepository;
use App\Services\AiMemory\SessionRepository;
use App\Services\AiMemory\StatsRepository;
use App\Services\AiMemory\WorkspaceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Admin AI-MEMORY module — READ-ONLY over the ai-memory SQLite index
 * (see App\Services\AiMemory\AiMemoryDatabase and docs/AI-MEMORY.md).
 *
 * Thin controller: each method injects the repositories of its screen and
 * returns the view. `screen()` centralises the availability guard — it probes
 * first (so no query runs when the database is unreachable) AND catches any
 * failure of the queries themselves, because the module's contract is an
 * explanatory notice, never an HTTP 500.
 */
class AiMemoryController extends Controller
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    public function dashboard(StatsRepository $stats, ProjectRepository $projects, DashboardSummary $summary): View
    {
        $days = (int) config('aimemory.chart_days', 30);

        return $this->screen('admin.ai-memory.dashboard', fn () => [
            'counts' => $stats->counts(),
            'observationsByDay' => $stats->observationsByDay($days),
            'sessionsByDay' => $stats->sessionsByDay($days),
            // Evolução de longo prazo vem da tabela DURÁVEL (sobrevive a reset).
            'history' => AiMemoryStatSnapshot::orderBy('captured_on')->get(),
            // Ranking: os mesmos projetos da aba "Projetos", ordenados por volume.
            'topProjects' => collect($projects->all())
                ->sortByDesc(fn ($project) => (int) $project->observations)
                ->take(6)
                ->values(),
            // Contas/escalas dos gráficos (matemática de apresentação, sem banco).
            'summary' => $summary,
        ]);
    }

    /**
     * Endpoint do "ao vivo" do Dashboard: os mesmos números da tela, em JSON.
     *
     * O ai-memory é escrito por OUTROS processos (os agentes), não por este app —
     * não existe evento nosso para transmitir. Então "tempo real" aqui é polling
     * curto do navegador, que não exige daemon nenhum no servidor (nada de
     * WebSocket/Reverb para manter). O JS só troca os números e as barras.
     */
    public function live(StatsRepository $stats): JsonResponse
    {
        if (! $this->db->isAvailable()) {
            return response()->json(['available' => false]);
        }

        $days = (int) config('aimemory.chart_days', 30);

        try {
            return response()->json([
                'available' => true,
                'counts' => $stats->counts(),
                'observationsByDay' => $stats->observationsByDay($days),
                'sessionsByDay' => $stats->sessionsByDay($days),
            ]);
        } catch (Throwable $e) {
            // Polled every 15s: a failure here must not fill the log with 500s
            // nor break the page that is already rendered.
            report($e);
            $this->db->markUnavailable($e);

            return response()->json(['available' => false]);
        }
    }

    public function projects(ProjectRepository $projects): View
    {
        return $this->screen('admin.ai-memory.projects', fn () => [
            'projects' => $projects->all(),
        ]);
    }

    public function projectShow(string $hexId, ProjectRepository $projects, PageRepository $pages, SessionRepository $sessions): View
    {
        return $this->screen('admin.ai-memory.project', function () use ($hexId, $projects, $pages, $sessions) {
            $project = $projects->find($hexId);
            abort_if($project === null, 404);

            return [
                'project' => $project,
                'recentPages' => $pages->paginate($hexId, 10)->items(),
                'recentSessions' => $sessions->paginate(['project' => $hexId], 10)->items(),
            ];
        });
    }

    public function workspaces(WorkspaceRepository $workspaces): View
    {
        return $this->screen('admin.ai-memory.workspaces', fn () => [
            'workspaces' => $workspaces->all(),
        ]);
    }

    public function pages(Request $request, PageRepository $pages, ProjectRepository $projects): View
    {
        $project = $request->string('project')->toString() ?: null;

        return $this->screen('admin.ai-memory.pages', fn () => [
            'pages' => $pages->paginate($project, $this->perPage())->withQueryString(),
            'projectOptions' => $projects->options(),
            'project' => $project,
        ]);
    }

    public function pageShow(string $hexId, PageRepository $pages): View
    {
        return $this->screen('admin.ai-memory.page', function () use ($hexId, $pages) {
            $page = $pages->find($hexId);
            abort_if($page === null, 404);

            return ['page' => $page, 'history' => $pages->history($page)];
        });
    }

    public function sessions(Request $request, SessionRepository $sessions, ProjectRepository $projects): View
    {
        $filters = $request->validate([
            'project' => ['nullable', 'string', 'max:64'],
            'agent' => ['nullable', 'string', 'max:40'],
            'days' => ['nullable', 'integer', 'in:1,7,30,90'],
            'sort' => ['nullable', 'string', 'in:recent,oldest,longest,shortest'],
        ]);

        return $this->screen('admin.ai-memory.sessions', fn () => [
            'sessions' => $sessions->paginate($filters, $this->perPage())->withQueryString(),
            'projectOptions' => $projects->options(),
            'agentKinds' => $sessions->agentKinds(),
            'filters' => $filters,
        ]);
    }

    public function sessionShow(string $hexId, SessionRepository $sessions): View
    {
        return $this->screen('admin.ai-memory.session', function () use ($hexId, $sessions) {
            $session = $sessions->find($hexId);
            abort_if($session === null, 404);

            return ['session' => $session, 'observations' => $sessions->observations($hexId)];
        });
    }

    public function observations(Request $request, ObservationRepository $observations, ProjectRepository $projects): View
    {
        $filters = $request->validate([
            'kind' => ['nullable', 'string', 'max:80'],
            'importance' => ['nullable', 'integer', 'between:1,10'],
            'project' => ['nullable', 'string', 'max:64'],
            'days' => ['nullable', 'integer', 'in:1,7,30,90'],
        ]);

        return $this->screen('admin.ai-memory.observations', fn () => [
            'observations' => $observations->paginate($filters, $this->perPage())->withQueryString(),
            'kinds' => $observations->kinds(),
            'projectOptions' => $projects->options(),
            'filters' => $filters,
        ]);
    }

    public function observationShow(string $hexId, ObservationRepository $observations): View
    {
        return $this->screen('admin.ai-memory.observation', function () use ($hexId, $observations) {
            $observation = $observations->find($hexId);
            abort_if($observation === null, 404);

            return ['observation' => $observation];
        });
    }

    public function handoffs(Request $request, HandoffRepository $handoffs): View
    {
        $state = $request->validate([
            'state' => ['nullable', 'string', 'in:open,accepted,expired'],
        ])['state'] ?? null;

        return $this->screen('admin.ai-memory.handoffs', fn () => [
            'handoffs' => $handoffs->paginate($state, $this->perPage())->withQueryString(),
            'state' => $state,
        ]);
    }

    public function handoffShow(string $hexId, HandoffRepository $handoffs): View
    {
        return $this->screen('admin.ai-memory.handoff', function () use ($hexId, $handoffs) {
            $handoff = $handoffs->find($hexId);
            abort_if($handoff === null, 404);

            return ['handoff' => $handoff];
        });
    }

    public function search(Request $request, SearchRepository $search): View
    {
        $q = trim($request->string('q')->toString());

        return $this->screen('admin.ai-memory.search', fn () => [
            'q' => $q,
            'results' => $q !== '' ? $search->search($q) : [],
        ]);
    }

    /** Teto de linhas por página (config). */
    private function perPage(): int
    {
        return (int) config('aimemory.per_page', 50);
    }

    /**
     * Render a screen of the module behind the availability guard.
     *
     * Two layers, because one is not enough:
     *  1. the probe (`isAvailable()`), which skips the queries altogether when
     *     the database is unreachable;
     *  2. this try/catch, for when the probe passes and a query still fails —
     *     permission changed mid-request, an ai-memory upgrade renamed a table,
     *     a lock outlived `busy_timeout`. The screen degrades to the notice and
     *     the exception goes to the log via report().
     *
     * HTTP exceptions are rethrown: a 404 from `abort_if()` inside a screen's
     * closure must stay a 404, not turn into "ai-memory unavailable".
     */
    private function screen(string $view, callable $data): View
    {
        if (! $this->db->isAvailable()) {
            return view($view, $this->baseData());
        }

        try {
            return view($view, array_merge($this->baseData(), $data()));
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);
            $this->db->markUnavailable($e);

            return view($view, $this->baseData());
        }
    }

    /**
     * Variables every screen of the module receives: the availability flag and
     * the material the notice needs to explain a degradation.
     */
    private function baseData(): array
    {
        return [
            'available' => $this->db->isAvailable(),
            'aimemoryPath' => $this->db->path(),
            'unavailableReason' => $this->db->unavailableReason(),
        ];
    }
}
