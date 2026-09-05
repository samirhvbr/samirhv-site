<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GitHubView\SyncRepositoryJob;
use App\Models\GitHubView\Repository;
use App\Services\GitHub\GitHubClient;
use App\Services\GitHub\GitHubException;
use App\Services\GitHub\RepositoryImporter;
use App\Services\GitHub\RepositorySuggestions;
use App\Services\GitHub\Visualizations\CommitHeatmap;
use App\Services\GitHub\Visualizations\RepositoryOverview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * GitHub View (admin) — dashboard de visualização animada de repositórios do
 * GitHub. Porte dos controllers do github-visualize (dashboard/repositories/
 * syncs/sync_statuses) num único controller fino, no estilo do AiMemoryController.
 * Sync roda SÍNCRONO (o samirhv não roda queue:work 24/7). Ver §6/§7 do plano.
 */
class GitHubViewController extends Controller
{
    /** Janelas de dias oferecidas no seletor (igual ao Rails WINDOWS). */
    private const WINDOWS = [15, 42, 60, 90];

    private const DEFAULT_WINDOW = 42;

    /** Ordenações do dashboard (igual ao Rails SORTS). "updated" = último commit. */
    private const SORTS = ['updated_desc', 'updated_asc', 'name_asc', 'name_desc', 'created_desc', 'created_asc'];

    private const DEFAULT_SORT = 'updated_desc';

    /** Dashboard: barras de commits/repo + cards com atividade diária, ordenáveis. */
    public function index(Request $request): View
    {
        $sort = in_array($request->query('sort'), self::SORTS, true) ? (string) $request->query('sort') : self::DEFAULT_SORT;
        $overview = new RepositoryOverview(Repository::all());

        return view('admin.github-view.index', [
            'repositories' => $overview->sorted($sort),
            'overview' => $overview,
            'sort' => $sort,
            'defaultOwner' => Repository::defaultOwner(),
        ]);
    }

    /** Adiciona um repositório (owner/name) e sincroniza na hora. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['repository' => ['required', 'string', 'max:255']]);

        [$owner, $name] = $this->parseRepository($request->string('repository')->toString());

        $repository = Repository::firstOrCreate(['owner' => $owner, 'name' => $name]);

        return $this->runSync($repository, "Repositório {$repository->fullName()} adicionado e sincronizado.");
    }

    /**
     * Importa TODOS os repositórios do usuário autenticado (via /user/repos) como
     * PENDENTES — 1 chamada de API, SEM sincronizar (sincronizar todos de uma vez
     * seria pesado/rate-limit). Depois é só sincronizar cada um. Idempotente.
     */
    public function importAll(RepositoryImporter $importer): RedirectResponse
    {
        try {
            $repos = app(GitHubClient::class)->userRepositories();
        } catch (GitHubException $e) {
            return redirect()->route('admin.github-view.index')->with('error', 'GitHub: '.$e->getMessage());
        }

        ['created' => $created, 'skipped' => $skipped] = $importer->import($repos);

        if ($created === 0 && $skipped === 0) {
            return redirect()->route('admin.github-view.index')
                ->with('error', 'Nenhum repositório retornado pela API (confira o GITHUB_TOKEN).');
        }

        return redirect()->route('admin.github-view.index')->with('status',
            "Importados {$created} novo(s); {$skipped} já existiam. Sincronize cada um pra ver os gráficos.");
    }

    /**
     * Autocomplete do add-form (JSON): repos que o token alcança (próprios +
     * orgs + colaborações), menos os já monitorados, filtrados/ordenados por
     * RepositorySuggestions. Cache de 10 min p/ não bater no GitHub a cada tecla.
     * Porte de suggestions_controller.rb.
     */
    public function suggestions(Request $request): JsonResponse
    {
        // Termo limitado: chega cru de um campo de busca, e um `q` de megabytes
        // não é ataque, é só trabalho desperdiçado a cada tecla.
        $request->validate(['q' => ['nullable', 'string', 'max:100']]);

        try {
            $repos = Cache::remember(
                'github/user_repositories',
                now()->addMinutes(10),
                fn (): array => app(GitHubClient::class)->userRepositories(),
            );
        } catch (GitHubException $e) {
            return response()->json([]);
        }

        $monitored = Repository::all(['owner', 'name'])->map->fullName();

        $suggestions = (new RepositorySuggestions)->build(
            $repos,
            $request->string('q')->toString(),
            $monitored->all(),
            Repository::defaultOwner(),
        );

        return response()->json($suggestions);
    }

    /** Página do repositório com as visualizações (Fatia 1: heatmap). */
    public function show(Request $request, string $owner, string $name): View
    {
        $repository = $this->findRepository($owner, $name);
        $window = $this->resolveWindow($request);

        return view('admin.github-view.show', [
            'repository' => $repository,
            'window' => $window,
            'windows' => self::WINDOWS,
            'heatmap' => (new CommitHeatmap($repository, $window))->toArray(),
        ]);
    }

    /** Re-sincroniza um repositório já cadastrado. */
    public function sync(string $owner, string $name): RedirectResponse
    {
        $repository = $this->findRepository($owner, $name);

        return $this->runSync($repository, "Repositório {$repository->fullName()} sincronizado.");
    }

    /** Remove o repositório e todos os seus dados (cascade nas FKs). */
    public function destroy(string $owner, string $name): RedirectResponse
    {
        $repository = $this->findRepository($owner, $name);
        $full = $repository->fullName();
        $repository->delete();

        return redirect()
            ->route('admin.github-view.index')
            ->with('status', "Repositório {$full} removido.");
    }

    /** Status do sync p/ polling (o JS pergunta enquanto está 'syncing'). */
    public function status(string $owner, string $name): JsonResponse
    {
        $repository = $this->findRepository($owner, $name);

        return response()->json([
            'sync_status' => $repository->sync_status,
            'sync_progress' => $repository->sync_progress,
            'sync_error' => $repository->sync_error,
            'last_synced_at' => $repository->last_synced_at?->toIso8601String(),
            'syncing' => $repository->isSyncing(),
        ]);
    }

    // ── internals ────────────────────────────────────────────────────────────

    private function findRepository(string $owner, string $name): Repository
    {
        return Repository::query()
            ->where('owner', $owner)
            ->where('name', $name)
            ->firstOrFail();
    }

    /**
     * Aceita "owner/name" OU só "name" (usa o GITHUB_OWNER default). Valida os
     * dois contra o mesmo formato do Rails (NAME_FORMAT).
     *
     * @return array{0: string, 1: string}
     */
    private function parseRepository(string $input): array
    {
        $input = trim($input);

        if (Str::contains($input, '/')) {
            [$owner, $name] = explode('/', $input, 2);
        } else {
            $owner = (string) Repository::defaultOwner();
            $name = $input;
        }

        $owner = trim($owner);
        $name = trim($name);

        foreach (['owner' => $owner, 'name' => $name] as $field => $value) {
            if ($value === '' || ! preg_match(Repository::NAME_FORMAT, $value)) {
                throw ValidationException::withMessages([
                    'repository' => $owner === ''
                        ? 'Informe "owner/name" (ou configure GITHUB_OWNER para usar só o nome).'
                        : "Formato inválido em {$field}: use owner/name com letras, números, '.', '-' ou '_'.",
                ]);
            }
        }

        return [$owner, $name];
    }

    private function resolveWindow(Request $request): int
    {
        $days = (int) $request->integer('days', self::DEFAULT_WINDOW);

        return in_array($days, self::WINDOWS, true) ? $days : self::DEFAULT_WINDOW;
    }

    /** Dispara o sync síncrono e trata a falta de token com mensagem amigável. */
    private function runSync(Repository $repository, string $successMessage): RedirectResponse
    {
        try {
            SyncRepositoryJob::dispatchSync($repository);
        } catch (GitHubException $e) {
            // MissingToken (config) estoura aqui, antes do try/catch do job.
            return redirect()
                ->route('admin.github-view.index')
                ->with('error', 'GitHub: '.$e->getMessage());
        }

        return redirect()
            ->route('admin.github-view.repos.show', ['owner' => $repository->owner, 'name' => $repository->name])
            ->with($repository->sync_status === 'failed' ? 'error' : 'status',
                $repository->sync_status === 'failed'
                    ? "Falha ao sincronizar {$repository->fullName()}: {$repository->sync_error}"
                    : $successMessage);
    }
}
