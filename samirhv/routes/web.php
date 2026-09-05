<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LegacyEnglishPrefix;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use App\Support\Locales;
use Illuminate\Support\Facades\Route;

/*
| ── Legacy addresses, 301 ───────────────────────────────────────────────────
|
| Registered first so they are read first, and unnamed so NegotiateLocale never
| touches them.
|
| The `/en/projetos/...` rule MUST precede the wildcard below it. Without it,
| `/en/projetos/github-desktop` would 301 to `/projetos/github-desktop`, which
| the first rule then 301s to the PORTUGUESE page — an English link landing
| silently in Portuguese, with a 200 and no error anywhere.
*/
Route::permanentRedirect('/projetos/github-desktop', '/pt-br/projects/github-desktop');
Route::permanentRedirect('/en/projetos/github-desktop', '/projects/github-desktop');
Route::get('/en/{path?}', LegacyEnglishPrefix::class)->where('path', '.*');

/*
| ── Public pages, in both languages ─────────────────────────────────────────
|
| The public pages are defined ONCE and registered once per language: bare
| (English, the canonical addresses) and under each other language's segment
| (`/pt-br`). One definition means the two languages cannot drift into
| different paths or different parameters — the shape is shared by construction.
|
| The Portuguese routes are named `pt-br.<name>`, so `route('downloads')` is the
| English url and `route('pt-br.downloads')` the Portuguese one. App\Support\Locales
| turns the current route into its twin, which is what feeds both the language
| switcher and the `hreflang` pair. Views never write either name by hand: they
| call `lroute()`, which picks the one that matches the page being rendered.
*/
$publico = function (): void {
    Route::get('/', [SiteController::class, 'home'])->name('home');
    Route::get('/downloads', [SiteController::class, 'downloads'])->name('downloads');
    Route::get('/p/{project}', [SiteController::class, 'show'])->name('project.show');

    // Static project pages (a description, with no database row).
    Route::view('/projects/github-desktop', 'projects.github-desktop')->name('project.github-desktop');
};

/* Prefixed languages first: a prefixed path must never be shadowed by a bare
   route someone adds later with a loose constraint. */
foreach (Locales::SUPPORTED as $locale) {
    if ($locale === Locales::BARE) {
        continue;
    }

    Route::prefix(Locales::segment($locale))
        ->name(Locales::routePrefix($locale))
        ->group($publico);
}

Route::group([], $publico);

/*
| The language switcher. A GET that writes the cookie and sends you to the twin
| of the page you were on — not to the home page, which is what a naive switcher
| does and what makes people stop using it.
|
| The segment is only shape-checked here; App\Support\Locales::canonical() is
| the single authority on what it means. See LocaleController.
*/
Route::get('/lang/{locale}', [LocaleController::class, 'set'])
    ->where('locale', '[A-Za-z_-]{2,7}')
    ->name('lang.set');

// Both languages of every public page, with reciprocal alternates. Unnamed, so
// NegotiateLocale leaves it alone; TrackPageView already skips this exact path.
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// Download with counting + audit (the only way to fetch a file; private disk).
// Deliberately NOT twinned per language: it is a delivery endpoint, not a page,
// and duplicating it would double-count downloads of the same file.
Route::get('/d/{file}', [DownloadController::class, 'track'])->name('download.track');

// ── Panel authentication (no public sign-up) ─────────────────
// Outside the bilingual surface: this is the door to /admin, which is out of
// scope by the owner's instruction.
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
