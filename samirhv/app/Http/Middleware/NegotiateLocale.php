<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends a visitor who arrived at a BARE url to the language they asked for.
 *
 * This site privileges neither language. The bare paths are Portuguese because
 * they were Portuguese before English existed and every published link points
 * at them; that is a URL fact, not a statement that Portuguese is the default.
 * So: a browser asking for English is forwarded to the `/en` twin, a browser
 * asking for Portuguese is served where it stands, and a browser that asks for
 * nothing gets English — because silence has to resolve to something, and
 * English is the one that travels.
 *
 * Never applied to a `/en` url: an explicit address wins over any preference,
 * including the cookie. Someone who was handed an English link gets English,
 * whatever their browser or their last visit says.
 *
 * 302, never 301: language is a negotiation, not a change of address. And
 * `Vary` is mandatory — without it a shared cache can hand the first visitor's
 * language to everyone behind it.
 */
class NegotiateLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $redirect = $this->target($request);

        if ($redirect !== null) {
            return redirect($redirect, 302)->header('Vary', 'Accept-Language, Cookie');
        }

        $response = $next($request);
        $response->headers->set('Vary', 'Accept-Language, Cookie', false);

        return $response;
    }

    private function target(Request $request): ?string
    {
        // Only a plain page view is negotiable. A form post, a download hit or
        // a non-HTML client must land where it was aimed.
        if (! $request->isMethod('GET') || $request->ajax() || ! $request->acceptsHtml()) {
            return null;
        }

        $name = $request->route()?->getName();
        if ($name === null || str_starts_with($name, Locales::PREFIXED.'.')) {
            return null;
        }

        $wanted = $this->wanted($request);
        if ($wanted === Locales::BARE) {
            return null;
        }

        /* Only a route that HAS a twin is negotiable. Asking Locales for a
           "best effort" url here was a defect: `/lang/{locale}` has no twin, so
           the switcher itself got redirected to `/en` and never wrote its
           cookie — the control existed and did nothing. Caught by
           LocaleNegotiationTest. */
        $target = Locales::alternates($request)[$wanted] ?? null;

        // A redirect to the address you are already on is an infinite loop,
        // not a no-op.
        return ($target === null || $target === $request->url()) ? null : $target;
    }

    private function wanted(Request $request): string
    {
        $chosen = Locales::canonical($request->cookie(Locales::COOKIE));
        if ($chosen !== null) {
            return $chosen;
        }

        $preferred = $request->getPreferredLanguage(Locales::SUPPORTED);

        return Locales::canonical($preferred) ?? Locales::SUPPORTED[0];
    }
}
