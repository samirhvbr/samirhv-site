<?php

namespace App\Http\Controllers;

use App\Support\BenchmarkSite;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * `/ai-benchmark`, `/ai-benchmark/leb-100`, … → the Benchmark page of shvia.org, permanently.
 *
 * Every page of the benchmark lived here until 1.1.0 (bare, under `/en` and under
 * `/pt-br`). All of them land on the one page that explains it and links the rest,
 * in the language of the address that was asked for.
 *
 * Deliberately unnamed, like LegacyEnglishPrefix: NegotiateLocale bails on a route
 * with no name, so an old address is never negotiated on top of being redirected,
 * and it can never surface in `Locales::alternates()` or the sitemap.
 */
class BenchmarkMoved extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $locale = str_starts_with($request->path(), Locales::segment('pt_BR').'/') ? 'pt_BR' : 'en';

        return redirect()->away(BenchmarkSite::page($locale), 301);
    }
}
