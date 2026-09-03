<?php

use App\Support\Locales;

if (! function_exists('lroute')) {
    /**
     * `route()`, but in the language of the page being rendered.
     *
     * Every public route exists twice — bare for Portuguese, `en.`-named for
     * English — so a plain `route('downloads')` always produces the Portuguese
     * url. On an English page that means every internal link walks the reader
     * back into Portuguese, silently. This is the only link helper the public
     * views use.
     *
     * A name with no twin in the current language falls back to the bare route
     * rather than throwing: `/d/{file}` and the admin have one url on purpose,
     * and a download button must not 500 because the page is in English.
     */
    function lroute(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $prefixed = Locales::routePrefix(app()->getLocale()).$name;

        return app('router')->has($prefixed)
            ? route($prefixed, $parameters, $absolute)
            : route($name, $parameters, $absolute);
    }
}
