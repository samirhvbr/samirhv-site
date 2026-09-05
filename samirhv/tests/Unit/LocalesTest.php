<?php

namespace Tests\Unit;

use App\Support\Locales;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * The derivation rules behind the URL scheme.
 *
 * Locale id, url slug, url segment and route-name prefix are four different
 * things that looked like one while the prefixed language was `en` — which is
 * its own slug, its own segment and its own route prefix at once. `pt_BR` is
 * none of those, so each conversion is asserted separately.
 */
class LocalesTest extends TestCase
{
    public function test_the_bare_language_is_english(): void
    {
        $this->assertSame('en', Locales::BARE);
    }

    public function test_the_slug_is_lowercase_and_hyphenated(): void
    {
        $this->assertSame('en', Locales::slug('en'));
        $this->assertSame('pt-br', Locales::slug('pt_BR'));
    }

    /** The bare language has no segment at all — that is what "bare" means. */
    public function test_only_the_prefixed_language_has_a_url_segment(): void
    {
        $this->assertSame('', Locales::segment('en'));
        $this->assertSame('pt-br', Locales::segment('pt_BR'));
    }

    public function test_the_route_prefix_matches_the_segment(): void
    {
        $this->assertSame('', Locales::routePrefix('en'));
        $this->assertSame('pt-br.', Locales::routePrefix('pt_BR'));
    }

    public function test_the_home_url_of_each_language(): void
    {
        $this->assertSame(url('/'), Locales::homeUrl('en'));
        $this->assertSame(url('/pt-br'), Locales::homeUrl('pt_BR'));
    }

    public function test_a_route_name_names_its_language(): void
    {
        $this->assertSame('pt_BR', Locales::fromRouteName('pt-br.home'));
        $this->assertSame('pt_BR', Locales::fromRouteName('pt-br.project.show'));

        // Anything without a known prefix renders in the bare language.
        foreach ([null, '', 'home', 'downloads', 'admin.dashboard', 'lang.set', 'download.track'] as $name) {
            $this->assertSame('en', Locales::fromRouteName($name), (string) $name);
        }
    }

    public function test_stripping_the_prefix_round_trips(): void
    {
        foreach (Locales::SUPPORTED as $locale) {
            $this->assertSame(
                'downloads',
                Locales::stripRoutePrefix(Locales::routePrefix($locale).'downloads'),
            );
        }
    }

    public function test_canonical_accepts_every_spelling_and_refuses_the_rest(): void
    {
        foreach (['pt-br', 'pt_BR', 'PT-BR', 'pt-BR', ' pt-br '] as $spelling) {
            $this->assertSame('pt_BR', Locales::canonical($spelling), $spelling);
        }

        $this->assertSame('en', Locales::canonical('EN'));

        foreach ([null, '', 'klingon', 'pt', 'en-GB'] as $rubbish) {
            $this->assertNull(Locales::canonical($rubbish), var_export($rubbish, true));
        }
    }

    public function test_the_tag_is_bcp_47(): void
    {
        $this->assertSame('pt-BR', Locales::tag('pt_BR'));
        $this->assertSame('en', Locales::tag('en'));
    }

    /**
     * The invariant that keeps SetLocale honest: a route named `pt-br.*`
     * renders in Portuguese, whatever its path. If someone ever names a route
     * that way outside the localized group, it silently flips language — and
     * this is the only thing that would notice.
     */
    public function test_no_route_claims_a_language_prefix_its_path_does_not_have(): void
    {
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if ($name === null || ! str_starts_with($name, 'pt-br.')) {
                continue;
            }

            $uri = $route->uri();
            $this->assertTrue(
                $uri === 'pt-br' || str_starts_with($uri, 'pt-br/'),
                "Route [{$name}] is named for Portuguese but lives at [/{$uri}].",
            );
        }
    }
}
