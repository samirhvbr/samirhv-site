<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * The two languages this site speaks, and everything derived from that.
 *
 * The URL is the authority on which language a page renders in: the bare paths
 * are Portuguese and the `/en` prefix is English. That is what makes `hreflang`
 * possible at all — one URL cannot declare two languages, so a site that
 * switched by cookie alone would be indexed in one language and no visitor
 * could share "the English page" by link.
 *
 * Negotiation therefore decides only WHERE TO SEND someone who arrived without
 * a prefix, never what a given URL means. See NegotiateLocale.
 */
final class Locales
{
    /** Cookie shared with nothing else; the site is the only writer. */
    public const COOKIE = 'samirhv_locale';

    /** Route-name prefix and URL segment of the non-default language. */
    public const PREFIXED = 'en';

    /** The language served by the bare, unprefixed URLs. */
    public const BARE = 'pt_BR';

    /**
     * Order matters. `Request::getPreferredLanguage()` returns the FIRST entry
     * when the browser sends no `Accept-Language` at all, so English sits first:
     * a client that states no preference gets the language that travels
     * furthest. A browser that does state one is obeyed either way — this site
     * privileges neither language, it only needs a defined answer for silence.
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

    /** The route-name prefix for a locale: '' for the bare one, 'en.' for English. */
    public static function routePrefix(string $locale): string
    {
        return $locale === self::BARE ? '' : self::PREFIXED.'.';
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

        // Strip any locale prefix to get the bare name: 'en.downloads' → 'downloads'.
        $bareName = preg_replace('/^'.preg_quote(self::PREFIXED, '/').'\./', '', $route->getName());

        /* Only the parameters the URI actually declares. `$route->parameters()`
           also returns route DEFAULTS, and `Route::view` stores `view` and
           `status` as defaults — so passing that straight to `route()` produced
           `/en/projetos/github-desktop?view=projects.github-desktop&status=200`,
           leaking an internal view name into a public hreflang. Caught by
           LocaleNegotiationTest. */
        $parameters = [];
        foreach ($route->parameterNames() as $name) {
            $value = $route->parameter($name);
            if ($value !== null) {
                $parameters[$name] = $value;
            }
        }

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
     * The url the language switcher points at, for one language.
     *
     * It goes through `lang.set` so the choice is REMEMBERED, and carries `to`
     * so it lands on the twin of the page you are on — a switcher that drops
     * you at the home page is a switcher nobody uses twice.
     *
     * `to` is a path, never an absolute url: the route validates that it starts
     * with `/` precisely so this parameter cannot become an open redirect.
     */
    public static function switchUrl(string $locale, ?Request $request = null): string
    {
        $twin = self::sameRouteIn($locale, $request);
        $path = parse_url($twin, PHP_URL_PATH) ?: '/';

        return route('lang.set', ['locale' => str_replace('_', '-', strtolower($locale))])
            .'?to='.urlencode($path);
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
        return self::alternates($request)[$locale] ?? url(
            $locale === self::BARE ? '/' : '/'.self::PREFIXED
        );
    }
}
