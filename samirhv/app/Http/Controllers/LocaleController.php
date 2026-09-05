<?php

namespace App\Http\Controllers;

use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * The language switcher: remember a choice, and stay on the page.
 *
 * A GET, not a form: it has to work with JavaScript off, and it carries no
 * state beyond the locale, so a GET is honest here.
 */
class LocaleController extends Controller
{
    public function set(Request $request, string $locale): RedirectResponse
    {
        $canonical = Locales::canonical($locale);

        /* `canonical()` is the ONLY authority on what a locale segment means.
           The route used to carry `whereIn('locale', ['en','pt-br','pt_BR','pt-BR'])`
           — three spellings of one thing, plus a duplicate — and Symfony compiles
           that constraint case-SENSITIVELY, so `/lang/PT-BR` 404'd on a value
           this method accepts. One rule, in one place. */
        if ($canonical === null) {
            abort(404);
        }

        return redirect()
            ->to($this->destination($request, $canonical))
            ->withCookie(Cookie::forever(Locales::COOKIE, $canonical));
    }

    /**
     * Where to send the visitor after writing the cookie.
     *
     * `to` is rebuilt from its path component rather than validated, because
     * validating it is what failed: the guard was `str_starts_with($to, '/')`,
     * and a PROTOCOL-RELATIVE url passes that. `UrlGenerator::isValidUrl()`
     * matches `~^(#|//|https?://|…)~` and `to()` then returns the string
     * verbatim, so `?to=//evil.example/phish` emitted
     * `Location: //evil.example/phish` — a cross-origin redirect off our own
     * domain. `parse_url(..., PHP_URL_PATH)` also drops any `@`, `?` or `#`
     * payload, so what reaches `url()` is a path and nothing else.
     *
     * The fallback is the home page of the chosen language, never
     * `Locales::sameRouteIn()`: the "current route" during this request is
     * `lang.set` itself, whose twin resolves back to `lang.set` — the switcher
     * would redirect to itself forever.
     */
    private function destination(Request $request, string $locale): string
    {
        $raw = (string) $request->query('to', '');

        $isLocalPath = str_starts_with($raw, '/')
            && ! str_starts_with($raw, '//')
            && ! str_starts_with($raw, '/\\');

        if ($isLocalPath) {
            $path = (string) parse_url($raw, PHP_URL_PATH);
            if ($path !== '') {
                return url('/'.ltrim($path, '/'));
            }
        }

        return Locales::homeUrl($locale);
    }
}
