<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the application locale from the URL, and only from the URL.
 *
 * Every public route is registered twice: bare (Portuguese) and under the `en`
 * prefix (English). The route NAME carries which one was matched, so the URL
 * that the visitor is looking at is the single authority on the language the
 * page renders in.
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
        App::setLocale($this->fromRoute($request));

        return $next($request);
    }

    private function fromRoute(Request $request): string
    {
        $name = $request->route()?->getName() ?? '';

        return str_starts_with($name, Locales::PREFIXED.'.')
            ? Locales::PREFIXED
            : Locales::BARE;
    }
}
