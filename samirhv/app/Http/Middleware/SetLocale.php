<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the application locale from the URL, and only from the URL.
 *
 * Every public route is registered once per language: bare (English) and under
 * the language's segment (`/pt-br`). The route NAME carries which one was
 * matched, so the URL that the visitor is looking at is the single authority on
 * the language the page renders in.
 *
 * Deliberately NOT consulted here: the cookie and `Accept-Language`. If either
 * could change what a URL renders, the same address would serve two languages
 * and `hreflang` would be a lie — a crawler and a visitor would disagree about
 * what lives at that link. Those two inputs steer redirection instead, in
 * NegotiateLocale, which is a different question.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->fromRoute($request);

        App::setLocale($locale);

        /* Carbon is set EXPLICITLY rather than left to a side effect of
           App::setLocale: the download rows print dates with
           `translatedFormat('d M Y')`, so "3 set 2026" against "3 Sep 2026" is
           decided here. Relying on the framework to propagate it would make the
           month name depend on boot order. */
        Carbon::setLocale(str_replace('_', '-', $locale));

        return $next($request);
    }

    /**
     * A route with no known language prefix renders in the bare language. That
     * covers `/login`, `/d/{file}`, `/lang/*` and all of `admin.*` — none of
     * which call `__()` today; the admin hardcodes its Portuguese and is out of
     * scope on purpose. Worth knowing before translating it.
     */
    private function fromRoute(Request $request): string
    {
        return Locales::fromRouteName($request->route()?->getName());
    }
}
