<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * `/en/...` → `/...`, permanently.
 *
 * Between 0.6.0 and 0.7.0 English lived under `/en`, and those are the urls
 * every `hreflang`, every canonical and every shared English link advertised.
 * English is now the bare url, so without this rule all of them 404.
 *
 * An invokable controller rather than `Route::permanentRedirect('/en/{path}', '/{path}')`
 * for two reasons: `RedirectController` runs the destination through
 * `UrlGenerator::toRoute()`, which `rawurlencode`s each parameter — a
 * multi-segment `{path}` comes back with `%2F` where the slashes were — and
 * `Route::redirect` drops the query string, which is where inbound campaign
 * tags live.
 *
 * Deliberately unnamed: NegotiateLocale bails on a route with no name, so a
 * legacy url can never be negotiated on top of being redirected, and it can
 * never surface in `Locales::alternates()`.
 */
class LegacyEnglishPrefix extends Controller
{
    public function __invoke(Request $request, string $path = ''): RedirectResponse
    {
        $query = $request->getQueryString();

        return redirect()->to('/'.ltrim($path, '/').($query ? '?'.$query : ''), 301);
    }
}
