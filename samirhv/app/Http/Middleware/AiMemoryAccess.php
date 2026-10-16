<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate of the AI-MEMORY admin screens.
 *
 * A request from an address listed in `aimemory.monitor_ips` (the monitoring
 * host) is let through without a login. Everyone else goes through the same
 * three controls as the rest of /admin: auth, admin, password.changed.
 *
 * Only GET/HEAD get the pass: the module is read-only, and a listed address
 * must not become a way to submit anything else.
 *
 * The address is `$request->ip()`, i.e. REMOTE_ADDR. Apache serves the app directly
 * (no CDN or proxy in front, no trusted proxy configured), so X-Forwarded-For sent by a client is never read — it
 * cannot be forged into the list. If a trusted proxy is ever put in front of
 * the app, this check starts seeing the proxy's address and must be revisited.
 */
class AiMemoryAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethodSafe() && $this->isMonitor($request)) {
            return $next($request);
        }

        return (new Pipeline(app()))
            ->send($request)
            ->through([
                Authenticate::class,
                EnsureIsAdmin::class,
                EnsurePasswordChanged::class,
            ])
            ->then($next);
    }

    private function isMonitor(Request $request): bool
    {
        $allowed = array_filter((array) config('aimemory.monitor_ips', []));

        return $allowed !== [] && in_array($request->ip(), $allowed, true);
    }
}
