<?php

namespace App\Services;

use App\Support\SemVer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Descobre a "última versão" de um repositório GitHub para o monitor de
 * projetos. Estratégia (decidida com o operador): `releases/latest` primeiro
 * (semver limpo); se o repo não publica releases, cai para a maior git tag.
 *
 * Sem autenticação (repos públicos): a API do GitHub dá 60 req/h por IP, então
 * cada resultado é cacheado por 1h. `refresh()` fura o cache sob demanda pelo
 * botão "Verificar agora". Erros são cacheados por pouco tempo para não marretar
 * a API a cada carregamento da tela quando algo está fora do ar.
 *
 * NÃO lança exceção: sempre retorna um array com `ok` — a tela de monitor nunca
 * deve quebrar por causa de um repo mal configurado ou da rede.
 */
class GithubReleaseChecker
{
    private const CACHE_PREFIX = 'monitor:gh:';

    private const TTL_OK = 3600;      // 1h — resultado bom

    private const TTL_ERROR = 300;    // 5min — evita insistir num erro

    /**
     * Última versão do repo (cacheada).
     *
     * @return array{ok:bool, version?:string, raw?:string, url?:string, published_at?:?string, source?:string, error?:string}
     */
    public function latest(string $repo): array
    {
        $normalized = self::normalizeRepo($repo);
        if ($normalized === null) {
            return ['ok' => false, 'error' => 'repo_invalido'];
        }

        // get/put manual (não remember) porque o TTL é condicional: 1h no
        // sucesso, 5min no erro. fetch() grava o resultado via storeResult().
        $cached = Cache::get(self::CACHE_PREFIX.$normalized);
        if (is_array($cached)) {
            return $cached;
        }

        return $this->fetch($normalized);
    }

    /** Descarta o cache de um repo (botão "Verificar agora"). */
    public function refresh(string $repo): void
    {
        $normalized = self::normalizeRepo($repo);
        if ($normalized !== null) {
            Cache::forget(self::CACHE_PREFIX.$normalized);
        }
    }

    /**
     * Normaliza a entrada para "owner/repo". Aceita URL completa, "github.com/…",
     * sufixo ".git" e barras sobrando. Retorna null se não der para extrair.
     */
    public static function normalizeRepo(?string $input): ?string
    {
        $s = trim((string) $input);
        if ($s === '') {
            return null;
        }
        // Tira esquema e host se vier URL.
        $s = preg_replace('#^https?://#i', '', $s);
        $s = preg_replace('#^(www\.)?github\.com/#i', '', $s);
        $s = trim($s, '/');
        $s = preg_replace('#\.git$#i', '', $s);

        // Sobra "owner/repo" (ignora caminho extra tipo /tree/main).
        if (preg_match('#^([\w.-]+)/([\w.-]+)#', $s, $m)) {
            return $m[1].'/'.$m[2];
        }

        return null;
    }

    /** Vai à API (sem cache). Tenta releases/latest, depois tags. */
    private function fetch(string $repo): array
    {
        try {
            $result = $this->fromLatestRelease($repo) ?? $this->fromTags($repo);
        } catch (\Throwable $e) {
            Log::warning('monitor_github_fetch_failed', ['repo' => $repo, 'error' => $e->getMessage()]);
            $result = ['ok' => false, 'error' => 'rede'];
        }

        return $this->storeResult($repo, $result);
    }

    /** GET /releases/latest — a release não-draft/não-prerelease mais recente. */
    private function fromLatestRelease(string $repo): ?array
    {
        $res = $this->request("repos/{$repo}/releases/latest");

        if ($res['rate_limited']) {
            return ['ok' => false, 'error' => 'rate_limit'];
        }
        // 404 aqui = repo sem NENHUMA release publicada → tenta tags.
        if ($res['status'] === 404) {
            return null;
        }
        if (! $res['ok'] || ! is_array($res['json'])) {
            return $res['status'] === 403
                ? ['ok' => false, 'error' => 'rate_limit']
                : ['ok' => false, 'error' => 'http_'.$res['status']];
        }

        $tag = $res['json']['tag_name'] ?? null;
        if (! $tag) {
            return null; // resposta estranha; deixa o fallback de tags decidir
        }

        return [
            'ok' => true,
            'version' => ltrim($tag, 'vV'),
            'raw' => $tag,
            'url' => $res['json']['html_url'] ?? "https://github.com/{$repo}/releases",
            'published_at' => $res['json']['published_at'] ?? null,
            'source' => 'release',
        ];
    }

    /** GET /tags — fallback: maior tag semver de repos que só tagueiam. */
    private function fromTags(string $repo): array
    {
        $res = $this->request("repos/{$repo}/tags?per_page=100");

        if ($res['rate_limited'] || $res['status'] === 403) {
            return ['ok' => false, 'error' => 'rate_limit'];
        }
        if ($res['status'] === 404) {
            return ['ok' => false, 'error' => 'repo_nao_encontrado'];
        }
        if (! $res['ok'] || ! is_array($res['json'])) {
            return ['ok' => false, 'error' => 'http_'.$res['status']];
        }

        $names = array_values(array_filter(array_map(
            fn ($t) => is_array($t) ? ($t['name'] ?? null) : null,
            $res['json']
        )));

        $best = SemVer::max($names);
        if ($best === null) {
            return ['ok' => false, 'error' => 'sem_release_ou_tag'];
        }

        return [
            'ok' => true,
            'version' => ltrim($best, 'vV'),
            'raw' => $best,
            'url' => "https://github.com/{$repo}/releases/tag/{$best}",
            'published_at' => null, // /tags não traz data
            'source' => 'tag',
        ];
    }

    /**
     * Um GET à API do GitHub. Devolve uma forma uniforme e NUNCA lança por
     * status HTTP (só por falha de conexão, capturada em fetch()).
     *
     * @return array{ok:bool, status:int, json:mixed, rate_limited:bool}
     */
    private function request(string $path): array
    {
        $res = Http::withHeaders([
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            // A API exige User-Agent; sem ele responde 403.
            'User-Agent' => 'samirhv-monitor',
        ])->timeout(8)->retry(2, 200, throw: false)->get('https://api.github.com/'.$path);

        $remaining = $res->header('X-RateLimit-Remaining');

        return [
            'ok' => $res->successful(),
            'status' => $res->status(),
            'json' => $res->json(),
            'rate_limited' => $res->status() === 403 && $remaining !== null && (int) $remaining === 0,
        ];
    }

    /** Cacheia: 1h se ok, 5min se erro (para recuperar rápido sem marretar). */
    private function storeResult(string $repo, array $result): array
    {
        Cache::put(
            self::CACHE_PREFIX.self::normalizeRepo($repo),
            $result,
            ($result['ok'] ?? false) ? self::TTL_OK : self::TTL_ERROR,
        );

        return $result;
    }
}
