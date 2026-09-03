<?php

use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\NegotiateLocale;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\TrackPageView;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Analytics de páginas públicas (roda em terminate, sem latência).
        // Order matters: SetLocale reads the matched route and fixes the
        // rendering language; NegotiateLocale may REDIRECT someone who arrived
        // at an unprefixed url. The redirect comes second because it only makes
        // sense once a route has resolved.
        $middleware->web(append: [
            SetLocale::class,
            NegotiateLocale::class,
            TrackPageView::class,
        ]);

        $middleware->alias([
            'admin' => EnsureIsAdmin::class,
            'password.changed' => EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
