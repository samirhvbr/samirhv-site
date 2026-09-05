<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Services\GithubReleaseChecker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Validação e normalização do formulário de projeto (criar e editar).
 *
 * Estava no controller como `validateData()`, misturando três coisas: as regras,
 * a normalização dos campos e um `throw ValidationException` na mão. FormRequest
 * é onde o Laravel põe exatamente isso, e separa as três — as regras em
 * `rules()`, a normalização em `normalized()`, e a mensagem de erro do upstream
 * volta a ser uma regra de validação como qualquer outra.
 *
 * A autorização não é feita aqui: quem chega a estas rotas já passou por
 * `auth`, `admin` e `password.changed` em routes/admin.php.
 */
class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $project = $this->route('project');
        $ignore = $project instanceof Project ? ','.$project->id : '';

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:projects,slug'.$ignore],
            'description' => ['nullable', 'string', 'max:5000'],
            'external_url' => ['nullable', 'url:http,https', 'max:2048'],
            'redirect_to_site' => ['nullable', 'boolean'],
            'upstream_repo' => ['nullable', 'string', 'max:140'],
            'category' => ['nullable', 'string', 'max:60'],
            'icon' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Os dados prontos para gravar.
     *
     * @return array<string, mixed>
     */
    public function normalized(): array
    {
        $project = $this->route('project');
        $project = $project instanceof Project ? $project : null;

        $data = $this->validated();

        $data['slug'] = Project::uniqueSlug($data['slug'] ?? $data['title'], $project);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_published'] = $this->boolean('is_published');
        $data['external_url'] = ($data['external_url'] ?? null) ?: null;

        // Só faz sentido redirecionar quando há site externo.
        $data['redirect_to_site'] = $data['external_url'] ? $this->boolean('redirect_to_site') : false;

        // Normaliza o upstream para "owner/repo" (aceita URL do GitHub). Se veio
        // preenchido mas não dá para extrair, é erro de formato — não engole.
        $raw = $data['upstream_repo'] ?? null;
        $data['upstream_repo'] = GithubReleaseChecker::normalizeRepo($raw);

        if (filled($raw) && $data['upstream_repo'] === null) {
            throw ValidationException::withMessages([
                'upstream_repo' => 'Formato inválido. Use "owner/repo" ou a URL do repositório no GitHub.',
            ]);
        }

        return $data;
    }
}
