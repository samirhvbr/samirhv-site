<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use App\Services\UserAgentParser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registra uma visita por requisição de PÁGINA pública. É terminable: o registro roda
 * em terminate(), DEPOIS da resposta enviada → zero latência para o visitante. Bots são
 * registrados (is_bot=true) para contagem separada. Não rastreia admin/auth/download/
 * assets, nem métodos != GET, redirects (3xx), erros (>=400) ou respostas não-HTML.
 */
class TrackPageView
{
    /**
     * Prefixos que NÃO são "visita de conteúdo" (1º segmento do path).
     *
     * Só o que este app realmente serve. A lista carregava treze prefixos do
     * scaffold do Breeze — `register`, `forgot-password`, `two-factor`,
     * `dashboard`, `locale`… — para rotas que nunca existiram aqui: não há
     * cadastro público (ver LoginController) e o seletor de idioma mora em
     * `/lang`, não em `/locale`. Prefixo que não casa com nada não filtra
     * nada; só faz a próxima pessoa acreditar que a rota existe.
     */
    private const SKIP_PREFIXES = [
        'admin', 'login', 'logout', 'lang', 'd', 'en', 'up',
        'js', 'img', 'vendor', 'storage',
    ];

    /** Paths exatos a ignorar. */
    private const SKIP_EXACT = ['favicon.ico', 'robots.txt', 'sitemap.xml'];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            if (! $this->shouldTrack($request, $response)) {
                return;
            }

            $ua = (string) $request->userAgent();
            $info = app(UserAgentParser::class)->parse($ua, (string) $request->ip());

            PageView::create([
                'path' => mb_substr('/'.ltrim($request->path(), '/'), 0, 255),
                'method' => $request->method(),
                'user_id' => $request->user()?->id,
                'ip' => (string) $request->ip(),
                'user_agent' => $ua !== '' ? mb_substr($ua, 0, 1024) : null,
                'is_bot' => $info['is_bot'],
                'device' => $info['device'],
                'browser' => $info['browser'],
                'os' => $info['os'],
                'referer' => mb_substr((string) $request->headers->get('referer'), 0, 1024) ?: null,
                /* O idioma em que a página foi RENDERIZADA, não o que o
                   navegador pediu. Era `getPreferredLanguage()`, e num site
                   bilíngue isso mede a coisa errada: um brasileiro lendo a
                   versão em inglês era gravado como pt-BR, então o relatório
                   dizia que ninguém lia em inglês. Como a rota decide o idioma
                   (ver SetLocale), este valor é exatamente a página que a
                   pessoa viu.

                   Nota sobre os relatórios: `/` e `/pt-br` são linhas distintas
                   em page_views, e continuam sendo — são duas páginas, com dois
                   textos e dois endereços. Esta coluna é o que separa uma da
                   outra sem depender do path. */
                'locale' => mb_substr(app()->getLocale(), 0, 35) ?: null,
            ]);
        } catch (\Throwable $e) {
            // Analytics nunca pode quebrar a entrega da página — só registra no log.
            Log::warning('page_view_track_failed', ['error' => $e->getMessage()]);
        }
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->ajax() || $request->wantsJson()) {
            return false;
        }
        // Só páginas realmente servidas (2xx) — fora redirects (login/locale) e erros.
        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            return false;
        }
        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && ! str_contains($contentType, 'text/html')) {
            return false;
        }

        $path = ltrim($request->path(), '/');
        if (in_array($path, self::SKIP_EXACT, true)) {
            return false;
        }
        foreach (self::SKIP_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return false;
            }
        }

        return true;
    }
}
