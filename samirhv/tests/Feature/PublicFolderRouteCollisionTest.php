<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * A folder in public/ must never share its name with a page's first segment.
 *
 * Apache serves public/ directly and rewrites to index.php only what is neither
 * a file nor a directory (`RewriteCond %{REQUEST_FILENAME} !-d` in .htaccess).
 * A real public/downloads/ therefore turned /downloads into a directory: Apache
 * added the trailing slash and answered 403, because listings are off. The
 * English page broke and the Portuguese one (/pt-br/downloads) did not, which is
 * how it went unnoticed from 1.0.47 to 1.0.68. Laravel's own test client never
 * sees Apache, so only a check on the folder names can catch it.
 */
class PublicFolderRouteCollisionTest extends TestCase
{
    public function test_no_public_folder_shadows_a_route(): void
    {
        $segments = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => explode('/', trim($route->uri(), '/'))[0])
            ->filter(fn ($segment) => $segment !== '' && ! str_starts_with($segment, '{'))
            ->unique();

        $folders = collect(scandir(public_path()))
            ->reject(fn ($name) => str_starts_with($name, '.'))
            ->filter(fn ($name) => is_dir(public_path($name)));

        $this->assertSame([], $folders->intersect($segments)->values()->all(),
            'A folder in public/ has the name of a route; Apache will serve the folder instead of the page.');
    }
}
