<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\SiteController;
use App\Support\Locales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

/*
| ── Public pages, in both languages ─────────────────────────────────────────
|
| The public pages are defined ONCE and registered TWICE: bare (Portuguese, the
| addresses that already exist and are linked from outside) and under the `en`
| prefix. One definition means the two languages cannot drift into different
| paths or different parameters — the shape is shared by construction.
|
| The English routes are named `en.<name>`, so `route('downloads')` is the
| Portuguese url and `route('en.downloads')` the English one. App\Support\Locales
| turns the current route into its twin, which is what feeds both the language
| switcher and the `hreflang` pair.
*/
$publico = function (): void {
    Route::get('/', [SiteController::class, 'home'])->name('home');
    Route::get('/downloads', [SiteController::class, 'downloads'])->name('downloads');
    Route::get('/p/{project}', [SiteController::class, 'show'])->name('project.show');

    // Static project pages (a description, with no database row).
    Route::view('/projetos/github-desktop', 'projects.github-desktop')->name('project.github-desktop');
};

Route::group([], $publico);
Route::prefix(Locales::PREFIXED)->name(Locales::PREFIXED.'.')->group($publico);

/*
| The language switcher. A GET that writes the cookie and sends you to the twin
| of the page you were on — not to the home page, which is what a naive switcher
| does and what makes people stop using it.
|
| It is a link, not a form: it has to work with JavaScript off, and it carries no
| state beyond the locale, so a GET is honest here.
*/
Route::get('/lang/{locale}', function (Request $request, string $locale) {
    $canonical = Locales::canonical($locale);

    if ($canonical === null) {
        return redirect()->to('/');
    }

    $back = $request->query('to');
    $target = ($back && str_starts_with($back, '/'))
        ? url($back)
        : Locales::sameRouteIn($canonical);

    return redirect()->to($target)->withCookie(
        Cookie::forever(Locales::COOKIE, $canonical)
    );
})->whereIn('locale', ['en', 'pt-br', 'pt_BR', 'pt-BR'])->name('lang.set');

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
