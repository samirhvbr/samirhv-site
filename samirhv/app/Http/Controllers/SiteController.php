<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DownloadPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Site público: vitrine de projetos disponibilizados para download.
 */
class SiteController extends Controller
{
    public function home(): View
    {
        $projects = Project::published()
            ->withCount('files')
            ->withSum('files as downloads_total', 'downloads_count')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('home', [
            // Destaque (card "releases.txt") só faz sentido com projeto que tem arquivos
            // (download ou híbrido); projetos-link puros não entram.
            'featured' => $projects->first(fn ($p) => $p->files_count > 0),
            'projects' => $projects->take(6),
            'categories' => $this->categories($projects),
        ]);
    }

    public function downloads(): View
    {
        // Vitrine completa: projetos de download, híbridos, de documentação (página
        // curada, ex. ai-usagebar) E projeto-link puro (ex. SShvTerm → site oficial).
        // A view escolhe o botão certo por tipo: "Ver arquivos" · "Instalar" · "Acessar site".
        $projects = Project::published()
            ->with(['availableFiles' => fn ($q) => $q->orderBy('label')])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('downloads.index', [
            'projects' => $projects,
            'totalFiles' => $projects->sum(fn ($p) => $p->availableFiles->count()),
        ]);
    }

    public function show(Project $project, Request $request, DownloadPresenter $presenter): View|RedirectResponse
    {
        abort_unless($project->is_published, 404);

        // Link puro (redirect ligado): manda direto pro site externo. Híbrido/download
        // renderiza a página /p/{slug}, com o botão "usar online" quando há site.
        if ($project->redirectsToSite()) {
            return redirect()->away($project->external_url);
        }

        // Página curada (projeto de documentação, sem binários hospedados aqui): renderiza
        // o Blade dedicado — screenshots, instalação por SO, etc. — em vez do download genérico.
        if ($project->hasCustomPage()) {
            return view($project->page_view, ['project' => $project]);
        }

        $project->load(['availableFiles' => fn ($q) => $q->orderBy('label')]);

        return view('projects.show', [
            'project' => $project,
            'download' => $presenter->for($project, $request),
        ]);
    }

    /** Categorias distintas presentes nos projetos publicados (para os chips/filtros). */
    private function categories($projects): array
    {
        return $projects
            ->pluck('category')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
