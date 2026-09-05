<?php

namespace App\Models;

use App\Support\SemVer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'category', 'icon', 'page_view', 'external_url',
        'redirect_to_site', 'upstream_repo', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'redirect_to_site' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Tem um site externo associado (projeto-link ou híbrido). */
    public function isLink(): bool
    {
        return filled($this->external_url);
    }

    /** Página curada (Blade) em vez da página genérica de download. Ex: projeto de documentação. */
    public function hasCustomPage(): bool
    {
        return filled($this->page_view);
    }

    /** Tem ao menos um arquivo disponível para download. Prefere dados já carregados (evita N+1). */
    public function hasFiles(): bool
    {
        if ($this->relationLoaded('availableFiles')) {
            return $this->availableFiles->isNotEmpty();
        }

        if (array_key_exists('files_count', $this->attributes)) {
            return (int) $this->files_count > 0;
        }

        return $this->availableFiles()->exists();
    }

    /**
     * Clicar no projeto deve ir direto pro site externo? Só quando tem external_url
     * E a flag redirect_to_site está ligada (link puro, ex: SShvTerm). Um híbrido
     * (ShvIA) tem external_url mas a flag desligada → abre a página /p/{slug}.
     */
    public function redirectsToSite(): bool
    {
        return $this->isLink() && (bool) $this->redirect_to_site;
    }

    /** Híbrido: tem site externo mas mostra a página /p/{slug} (botão "usar online" + downloads). */
    public function isHybrid(): bool
    {
        return $this->isLink() && ! $this->redirect_to_site;
    }

    /**
     * URL pública do projeto: o site externo (se redireciona) ou a página /p/{slug}.
     *
     * `lroute()`, não `route()`: cada idioma tem a sua rota, e `route()` devolve
     * sempre a inglesa. Todo card de projeto numa página /pt-br apontava para a
     * página em inglês — e o NegotiateLocale trazia o visitante de volta com um
     * 302, então o defeito se escondia atrás de um redirect a mais por clique.
     *
     * Cuidado: `lroute()` lê `app()->getLocale()`, que fora de um request HTTP é
     * o locale de boot. Num command, num job ou no sitemap isto devolve a url do
     * locale de boot, não a do idioma pretendido — por isso o SitemapController
     * monta as duas urls a partir do nome da rota em vez de usar este accessor.
     */
    public function getPublicUrlAttribute(): string
    {
        return $this->redirectsToSite() ? $this->external_url : lroute('project.show', $this);
    }

    /** É fork de um OSS com upstream rastreável no monitor? */
    public function hasUpstream(): bool
    {
        return filled($this->upstream_repo);
    }

    /** URL do repositório upstream no GitHub (para linkar no monitor). */
    public function getUpstreamUrlAttribute(): ?string
    {
        return $this->upstream_repo ? 'https://github.com/'.$this->upstream_repo : null;
    }

    /**
     * "Nossa versão": a maior versão semver entre os arquivos disponíveis
     * (o que servimos hoje). Sem versão semver, cai para a versão do arquivo
     * mais recente por data. Null = nenhum arquivo versionado. Prefere a relação
     * já carregada (evita N+1 no monitor, que faz eager-load de availableFiles).
     */
    public function localVersion(): ?string
    {
        $files = $this->relationLoaded('availableFiles')
            ? $this->availableFiles
            : $this->availableFiles()->get();

        $versions = $files->pluck('version')->filter()->all();

        return SemVer::max($versions)
            ?? $files->sortByDesc(fn (ProjectFile $f) => $f->effective_date)->first()?->version;
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    /** Arquivos visíveis ao público (disponíveis e com espelho no disco). */
    public function availableFiles(): HasMany
    {
        return $this->files()->where('is_available', true);
    }

    /** Soma de downloads de todos os arquivos do projeto. */
    public function getDownloadsCountAttribute(): int
    {
        return (int) $this->files()->sum('downloads_count');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
