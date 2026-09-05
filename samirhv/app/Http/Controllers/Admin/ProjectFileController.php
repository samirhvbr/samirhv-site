<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\AuditLogger;
use App\Services\FileIngestService;
use App\Support\SemVer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectFileController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly FileIngestService $ingest,
    ) {}

    public function index(Project $project): View
    {
        /* Build mais recente no topo: versão desc (semver-aware, então 0.10 > 0.9);
           empate cai para data efetiva (released_at ?? created_at) desc e depois
           rótulo.

           Via App\Support\SemVer, não `version_compare()` cru: o mesmo conceito
           já estava implementado ali, com o tratamento do prefixo `v` e de
           versão não-parseável, e é o que o Monitor e o DownloadPresenter usam.
           Duas comparações de versão que discordam em algum caso de borda é uma
           tela de admin ordenada diferente da página pública. */
        $files = $project->files()->get()
            ->sort(fn (ProjectFile $a, ProjectFile $b) => SemVer::compare($b->version, $a->version)
               ?: ($b->effective_date <=> $a->effective_date)
               ?: strcmp((string) $a->label, (string) $b->label))
            ->values();

        return view('admin.projects.files', compact('project', 'files'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            /* Sem `mimes:`/`extensions:` DE PROPÓSITO, e isto está escrito aqui
               para não parecer esquecimento: este é um repositório de binários
               arbitrários — .deb, .rpm, .AppImage, .pkg.tar.zst, .exe, .msi,
               .dmg — e a lista de formatos cresce a cada plataforma que um
               projeto passa a suportar. O que torna isso seguro não é a
               extensão: o disco é PRIVADO (storage/app/private/downloads), nada
               ali é servido pelo web server, e a única saída é o
               DownloadController, que entrega como anexo. Só o admin envia. */
            'file' => ['required', 'file', 'max:512000'],   // 500 MB
            'label' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:30'],
            'os' => ['required', 'in:linux,windows,macos'],
            'arch' => ['nullable', 'in:x64,arm64,universal'],
            'file_type' => ['nullable', 'string', 'max:16'],
        ], [
            'file.max' => 'O arquivo excede o limite de 500 MB do upload via navegador. '
                .'Para arquivos maiores, use o comando files:add no servidor.',
            'os.required' => 'Selecione o sistema operacional do arquivo.',
            'os.in' => 'Sistema operacional inválido.',
        ]);

        $file = $this->ingest->ingest($request->file('file'), $project, [
            'label' => $request->input('label') ?: null,
            'version' => $request->input('version') ?: null,
            'os' => $request->input('os'),
            'arch' => $request->input('arch') ?: null,
            'file_type' => $request->input('file_type') ?: null,
        ]);

        $this->audit->record('file.upload', $file->id,
            "Arquivo enviado: {$file->label} ({$file->human_size}) — projeto {$project->title}");

        return redirect()->route('admin.projects.files.index', $project)
            ->with('status', "Arquivo \"{$file->label}\" enviado.");
    }

    public function toggleAvailable(Project $project, ProjectFile $file): RedirectResponse
    {
        abort_unless($file->project_id === $project->id, 404);

        $file->update(['is_available' => ! $file->is_available]);

        $event = $file->is_available ? 'file.available' : 'file.unavailable';
        $this->audit->record($event, $file->id, "Arquivo: {$file->label} — projeto {$project->title}");

        return back()->with('status', $file->is_available ? 'Arquivo disponibilizado.' : 'Arquivo ocultado.');
    }

    public function update(Request $request, Project $project, ProjectFile $file): RedirectResponse
    {
        abort_unless($file->project_id === $project->id, 404);

        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:30'],
            'os' => ['required', 'in:linux,windows,macos'],
            'arch' => ['nullable', 'in:x64,arm64,universal'],
            'file_type' => ['nullable', 'string', 'max:16'],
        ], [
            'os.required' => 'Selecione o sistema operacional do arquivo.',
            'os.in' => 'Sistema operacional inválido.',
        ]);

        // Só metadados — o binário (filename/size/sha256) nunca muda aqui.
        $file->update([
            'label' => $data['label'] ?: $file->original_name,
            'version' => $data['version'] ?: null,
            'os' => $data['os'],
            'arch' => $data['arch'] ?: null,
            'file_type' => $data['file_type'] ?: null,
        ]);

        $this->audit->record('file.update', $file->id,
            "Arquivo editado: {$file->label} — projeto {$project->title}");

        return redirect()->route('admin.projects.files.index', $project)
            ->with('status', "Arquivo \"{$file->label}\" atualizado.");
    }

    public function destroy(Project $project, ProjectFile $file): RedirectResponse
    {
        abort_unless($file->project_id === $project->id, 404);

        $label = $file->label;
        // Remove o binário do disco e a linha (soft delete preserva o histórico de logs).
        Storage::disk(FileIngestService::DISK)->delete($file->filename);
        ProjectFile::forgetMirrored();   // o índice do request acabou de ficar velho
        $file->delete();

        $this->audit->record('file.delete', $file->id, "Arquivo removido: {$label} — projeto {$project->title}");

        return back()->with('status', "Arquivo \"{$label}\" removido.");
    }
}
