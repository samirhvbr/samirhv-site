<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): View
    {
        $projects = Project::withCount('files')
            ->withSum('files as downloads_total', 'downloads_count')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.projects.create', ['project' => new Project]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $data = $request->normalized();

        $project = Project::create($data);

        $this->audit->record('project.create', $project->id, "Projeto criado: {$project->title}");

        // Sempre vai pro upload: projeto de download precisa de arquivos, e um
        // projeto-link pode virar híbrido enviando arquivos (ex: app desktop).
        // Para projeto-link puro, é só não enviar nada e sair.
        $message = $project->isLink()
            ? "Projeto-link \"{$project->title}\" criado. Para oferecer também downloads (ex: app desktop), envie os arquivos abaixo — senão é só sair."
            : 'Projeto criado. Agora envie os arquivos para download.';

        return redirect()
            ->route('admin.projects.files.index', $project)
            ->with('status', $message);
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $request->normalized();

        $wasPublished = $project->is_published;
        $project->update($data);

        if ($wasPublished !== $project->is_published) {
            $event = $project->is_published ? 'project.publish' : 'project.unpublish';
            $this->audit->record($event, $project->id, "Projeto: {$project->title}");
        } else {
            $this->audit->record('project.update', $project->id, "Projeto atualizado: {$project->title}");
        }

        return redirect()->route('admin.projects.index')->with('status', 'Projeto atualizado.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $title = $project->title;
        $project->delete();

        $this->audit->record('project.delete', $project->id, "Projeto removido: {$title}");

        return redirect()->route('admin.projects.index')->with('status', 'Projeto removido.');
    }
}
