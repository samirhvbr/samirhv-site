<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * The two languages this site speaks, and everything derived from that.
 *
 * The URL is the authority on which language a page renders in: the bare paths
 * are English and the `/pt-br` prefix is Portuguese. That is what makes
 * `hreflang` possible at all — one URL cannot declare two languages, so a site
 * that switched by cookie alone would be indexed in one language and no visitor
 * could share "the English page" by link.
 *
 * Negotiation therefore decides only WHERE TO SEND someone who arrived without
 * a prefix, never what a given URL means. See NegotiateLocale.
 *
 * Four things used to be conflated here, and only looked like one because the
 * prefixed language was `en`, which happens to be its own URL segment and its
 * own route-name prefix at once:
 *
 *   locale id      pt_BR    what App::setLocale() and lang/ want
 *   slug           pt-br    what /lang/{locale} accepts
 *   URL segment    pt-br    and '' for the language served bare
 *   route prefix   pt-br.   and '' for the language served bare
 *
 * `pt_BR` breaks that coincidence, so all four are derived from ONE constant
 * below. Adding a third language means adding it to SUPPORTED and nothing else.
 */
final class Locales
{
    /** Cookie shared with nothing else; the site is the only writer. */
    public const COOKIE = 'samirhv_locale';

    /** The language served by the bare, unprefixed URLs — the canonical one. */
    public const BARE = 'en';

    /**
     * Order matters. `Request::getPreferredLanguage()` returns the FIRST entry
     * when the browser sends no `Accept-Language` at all, so English sits first:
     * a client that states no preference gets the language that travels
     * furthest — and, since English is also what the bare URL serves, it gets
     * it without a redirect. A browser that does state a preference is obeyed
     * either way.
     */
    public const SUPPORTED = ['en', 'pt_BR'];

    public static function valid(?string $locale): bool
    {
        return is_string($locale) && in_array($locale, self::SUPPORTED, true);
    }

    /** `pt-br`, `pt-BR` and `pt_BR` all mean the same thing to a browser. */
    public static function canonical(?string $locale): ?string
    {
        if (! is_string($locale) || $locale === '') {
            return null;
        }

        $normalised = str_replace('-', '_', trim($locale));

        foreach (self::SUPPORTED as $supported) {
            if (strcasecmp($normalised, $supported) === 0) {
                return $supported;
            }
        }

        return null;
    }

    /** BCP 47, for `<html lang>` and `hreflang`: pt_BR becomes pt-BR. */
    public static function tag(?string $locale = null): string
    {
        return str_replace('_', '-', $locale ?? App::getLocale());
    }

    /** The lowercase-hyphen spelling: 'pt_BR' → 'pt-br'. What a URL carries. */
    public static function slug(string $locale): string
    {
        return str_replace('_', '-', strtolower($locale));
    }

    /** The URL segment for a locale: '' for the bare one, 'pt-br' otherwise. */
    public static function segment(string $locale): string
    {
        return $locale === self::BARE ? '' : self::slug($locale);
    }

    /** The route-name prefix: '' for the bare one, 'pt-br.' otherwise. */
    public static function routePrefix(string $locale): string
    {
        $segment = self::segment($locale);

        return $segment === '' ? '' : $segment.'.';
    }

    /** The home page of a language: `/` or `/pt-br`. */
    public static function homeUrl(string $locale): string
    {
        return url('/'.self::segment($locale));
    }

    /**
     * Which language a route name renders in.
     *
     * Replaces the `str_starts_with($name, 'en.')` that both middlewares
     * carried: a literal there meant the middleware had to be edited to add a
     * language, and edited correctly, or a page would silently render in the
     * wrong one. A name with no known prefix is the bare language — which
     * covers `/login`, `/d/{file}` and all of `admin.*`.
     */
    public static function fromRouteName(?string $name): string
    {
        if (! is_string($name) || $name === '') {
            return self::BARE;
        }

        foreach (self::SUPPORTED as $locale) {
            $prefix = self::routePrefix($locale);
            if ($prefix !== '' && str_starts_with($name, $prefix)) {
                return $locale;
            }
        }

        return self::BARE;
    }

    /** 'pt-br.downloads' → 'downloads'. The name shared by every language. */
    public static function stripRoutePrefix(string $name): string
    {
        foreach (self::SUPPORTED as $locale) {
            $prefix = self::routePrefix($locale);
            if ($prefix !== '' && str_starts_with($name, $prefix)) {
                return substr($name, strlen($prefix));
            }
        }

        return $name;
    }

    /**
     * Every language's URL for one route, as [locale => absolute URL].
     *
     * The request-free primitive. `alternates()` below is a wrapper that reads
     * the current route; the sitemap calls THIS directly, with a route name it
     * chooses, because a sitemap has no current route and faking a Request to
     * get one would be a lie with consequences. Sharing this function is what
     * makes it impossible for the sitemap to advertise alternates that
     * contradict the `hreflang` tags in the page's own head.
     */
    public static function alternatesFor(string $bareName, array $parameters = []): array
    {
        $out = [];

        foreach (self::SUPPORTED as $locale) {
            $name = self::routePrefix($locale).$bareName;
            if (! app('router')->has($name)) {
                continue;
            }
            $out[$locale] = route($name, $parameters);
        }

        return $out;
    }

    /**
     * The same route in every language, as [locale => absolute URL].
     *
     * Built from the CURRENT route rather than a table of paths, so a page
     * cannot be added without its alternate appearing — and cannot claim an
     * alternate that does not resolve.
     */
    public static function alternates(?Request $request = null): array
    {
        $request ??= request();
        $route = $request->route();

        if (! $route || ! $route->getName()) {
            return [];
        }

        /* Only the parameters the URI actually declares. `$route->parameters()`
           also returns route DEFAULTS, and `Route::view` stores `view` and
           `status` as defaults — so passing that straight to `route()` produced
           `/pt-br/projects/github-desktop?view=projects.github-desktop&status=200`,
           leaking an internal view name into a public hreflang. Caught by
           LocaleNegotiationTest. */
        $parameters = [];
        foreach ($route->parameterNames() as $name) {
            $value = $route->parameter($name);
            if ($value !== null) {
                $parameters[$name] = $value;
            }
        }

        return self::alternatesFor(self::stripRoutePrefix($route->getName()), $parameters);
    }

    /**
     * The url the language switcher points at, for one language.
     *
     * It goes through `lang.set` so the choice is REMEMBERED, and carries `to`
     * so it lands on the twin of the page you are on — a switcher that drops
     * you at the home page is a switcher nobody uses twice.
     *
     * `to` is a path, never an absolute url: the controller rebuilds it from
     * `parse_url` precisely so this parameter cannot become an open redirect.
     */
    public static function switchUrl(string $locale, ?Request $request = null): string
    {
        $twin = self::sameRouteIn($locale, $request);
        $path = parse_url($twin, PHP_URL_PATH) ?: '/';

        return route('lang.set', ['locale' => self::slug($locale)]).'?to='.urlencode($path);
    }

    /**
     * The language's name IN ITSELF — 'English', 'Português'. Never translated:
     * someone looking for their own language scans for the word they know, and
     * "Portuguese" is not that word for the person who needs it.
     */
    public static function nativeName(string $locale): string
    {
        return match ($locale) {
            'pt_BR' => 'Português',
            'en' => 'English',
            default => $locale,
        };
    }

    /** 'en' → 'EN', 'pt_BR' → 'PT'. The label a two-letter switcher shows. */
    public static function shortLabel(string $locale): string
    {
        return strtoupper(explode('_', $locale)[0]);
    }

    /**
     * The current route's name in another language, keeping its parameters.
     * Used by the language switcher, so switching keeps you on the same page
     * instead of dropping you at the home page.
     */
    public static function sameRouteIn(string $locale, ?Request $request = null): string
    {
        return self::alternates($request)[$locale] ?? self::homeUrl($locale);
    }
}
