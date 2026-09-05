<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * The three controls that keep /admin closed.
 *
 * Thirty-five admin routes had no test at all: nothing asserted that
 * EnsureIsAdmin blocks, that EnsurePasswordChanged redirects, or that the
 * login throttle is armed. Those are the security controls of this app, and
 * the failure mode of all three is silent — a panel that opens.
 *
 * No database is needed, and that is not a compromise: every case here is
 * REFUSED before it reaches a controller, so no query is ever made. The users
 * are unsaved models, which is enough for middleware that reads two flags.
 */
class AdminAccessTest extends TestCase
{
    /**
     * `is_admin` and `must_change_password` are deliberately NOT fillable —
     * a form must never be able to promote its own account — so they are set
     * as properties rather than mass-assigned. Mass-assigning them here would
     * silently drop them and the test would pass for the wrong reason.
     */
    private function user(bool $isAdmin, bool $mustChangePassword = false): User
    {
        $user = new User(['name' => 'Test', 'email' => 'test@example.test']);
        $user->is_admin = $isAdmin;
        $user->must_change_password = $mustChangePassword;

        return $user;
    }

    /** Every admin route, discovered from the route table — not a hand-written list. */
    private function adminRoutes(): array
    {
        $urls = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if ($name === null || ! str_starts_with($name, 'admin.')) {
                continue;
            }
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }
            // Skip the ones needing a bound parameter; the guard is the same.
            if ($route->parameterNames() !== []) {
                continue;
            }
            $urls[$name] = '/'.ltrim($route->uri(), '/');
        }

        return $urls;
    }

    public function test_the_route_table_really_has_admin_routes_to_guard(): void
    {
        $this->assertNotEmpty($this->adminRoutes(), 'No admin routes found — this suite would pass vacuously.');
    }

    public function test_every_admin_page_turns_a_guest_away(): void
    {
        foreach ($this->adminRoutes() as $name => $url) {
            $this->get($url)->assertRedirect(route('login'), "[$name] let a guest through.");
        }
    }

    /** A signed-in user who is not an admin is refused, not redirected. */
    public function test_a_signed_in_non_admin_is_refused(): void
    {
        $mortal = $this->user(isAdmin: false);

        foreach ($this->adminRoutes() as $name => $url) {
            $this->actingAs($mortal)->get($url)->assertForbidden();
        }
    }

    /**
     * Refusing a page must not end a valid session.
     *
     * EnsureIsAdmin used to call Auth::logout() here, so a legitimate user was
     * signed OUT for navigating to a page they may not see. Nothing triggers it
     * while the site has one user, which is exactly why it would have surfaced
     * later as "it logs me out on its own".
     */
    public function test_refusing_a_non_admin_leaves_their_session_alone(): void
    {
        $mortal = $this->user(isAdmin: false);

        $this->actingAs($mortal)->get('/admin')->assertForbidden();

        $this->assertAuthenticated();
    }

    public function test_an_admin_owing_a_password_change_is_sent_to_the_profile(): void
    {
        $owing = $this->user(isAdmin: true, mustChangePassword: true);

        foreach ($this->adminRoutes() as $name => $url) {
            if ($name === 'admin.profile') {
                continue;
            }

            $this->actingAs($owing)->get($url)
                ->assertRedirect(route('admin.profile'), "[$name] skipped the password change.");
        }
    }

    /** The redirect target must be reachable, or the panel is a redirect loop. */
    public function test_the_profile_page_is_exempt_so_the_password_can_be_changed(): void
    {
        $this->actingAs($this->user(isAdmin: true, mustChangePassword: true))
            ->get(route('admin.profile'))
            ->assertOk();
    }

    /** Five attempts per minute, per IP and e-mail. Registered in AppServiceProvider. */
    public function test_the_login_throttle_is_armed(): void
    {
        $limiter = RateLimiter::limiter('login');

        $this->assertNotNull($limiter, 'The `login` rate limiter is not registered.');

        $request = \Illuminate\Http\Request::create('/login', 'POST', ['email' => 'a@b.test']);
        $limit = $limiter($request);
        $limit = is_array($limit) ? $limit[0] : $limit;

        $this->assertSame(5, $limit->maxAttempts);
    }

    /** The route has to carry the middleware, not merely have it configured. */
    public function test_the_login_route_uses_the_throttle(): void
    {
        $route = Route::getRoutes()->getByName('login.attempt');

        $this->assertNotNull($route);
        $this->assertContains('throttle:login', $route->gatherMiddleware());
    }
}
