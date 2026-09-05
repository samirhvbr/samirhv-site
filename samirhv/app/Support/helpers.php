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

if (! function_exists('lnumber')) {
    /**
     * A whole number with the separators of the page's language.
     *
     * `number_format($n, 0, ',', '.')` was hardcoded across the download views:
     * correct for pt-BR, and wrong in English, where 1.234 reads as one point
     * two three four. Small, and exactly the kind of thing that makes a
     * translated page feel translated rather than written.
     */
    function lnumber(int|float $value): string
    {
        return app()->getLocale() === 'pt_BR'
            ? number_format($value, 0, ',', '.')
            : number_format($value, 0, '.', ',');
    }
}

if (! function_exists('vasset')) {
    /**
     * `asset()` com cache-busting pelo mtime do arquivo.
     *
     * O tema é estático e servido direto pelo nginx, sem bundler e sem
     * manifest, então um CSS editado continuaria sendo servido do cache do
     * navegador até alguém dar Ctrl+F5 — o que é exatamente o que acontece
     * depois de um deploy que só mexeu em estilo. O mtime resolve isso sem
     * introduzir um passo de build.
     *
     * Memoiza por processo: são poucos arquivos e um `stat` por link em cada
     * request seria trocar um problema por outro.
     */
    function vasset(string $path): string
    {
        static $stamps = [];

        if (! array_key_exists($path, $stamps)) {
            $full = public_path($path);
            $stamps[$path] = is_file($full) ? (string) filemtime($full) : null;
        }

        $url = asset($path);

        return $stamps[$path] === null ? $url : $url.'?v='.$stamps[$path];
    }
}
