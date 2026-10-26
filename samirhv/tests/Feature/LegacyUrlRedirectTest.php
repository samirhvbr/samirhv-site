<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The addresses that existed before 0.7.0 must keep working.
 *
 * Two families:
 *  - `/en/...`  — English lived there from 0.6.0 to 0.7.0, and those urls are
 *    what every hreflang, every canonical and every shared English link
 *    advertised. Without a rule they 404.
 *  - `/projetos/github-desktop` — the one Portuguese-shaped path that was
 *    renamed rather than re-prefixed.
 *
 * Three urls cannot be redirected and are not tested here, because there is
 * nothing to assert: `/`, `/downloads` and `/p/{slug}` were Portuguese and are
 * now English at the same address. A 301 would break the English page.
 */
class LegacyUrlRedirectTest extends TestCase
{
    /** 301, not 302: a 302 here would pass assertRedirect and be an SEO no-op. */
    public function test_the_renamed_portuguese_page_moves_permanently(): void
    {
        $this->get('/projetos/github-desktop')
            ->assertStatus(301)
            ->assertRedirect(url('/pt-br/projects/github-desktop'));
    }

    /**
     * The ordering trap, and the reason this test exists.
     *
     * If the wildcard rule were registered before this one,
     * `/en/projetos/github-desktop` would 301 to `/projetos/github-desktop`,
     * which the first rule then 301s to the PORTUGUESE page. An English link
     * would land in Portuguese, with a 200 and no error anywhere.
     */
    public function test_the_english_legacy_page_lands_in_english_not_portuguese(): void
    {
        $this->get('/en/projetos/github-desktop')
            ->assertStatus(301)
            ->assertRedirect(url('/projects/github-desktop'));
    }

    public function test_every_en_url_moves_to_its_bare_twin(): void
    {
        foreach ([
            '/en' => '/',
            '/en/downloads' => '/downloads',
            '/en/p/some-slug' => '/p/some-slug',
            '/en/projects/github-desktop' => '/projects/github-desktop',
        ] as $legacy => $target) {
            $this->get($legacy)
                ->assertStatus(301)
                ->assertRedirect(url($target));
        }
    }

    /**
     * Inbound campaign tags live in the query string, and `Route::redirect`
     * drops it — which is one of the two reasons the wildcard is a controller.
     *
     * Symfony's `getQueryString()` normalises by sorting the parameters, so the
     * assertion is alphabetical rather than as-sent. Order carries no meaning
     * in a query string; presence does.
     */
    public function test_the_query_string_survives_the_redirect(): void
    {
        $this->get('/en/downloads?utm_source=newsletter&utm_medium=email')
            ->assertStatus(301)
            ->assertRedirect(url('/downloads').'?utm_medium=email&utm_source=newsletter');
    }

    /** One hop, not a chain: a redirect that redirects again loses PageRank. */
    public function test_a_legacy_url_resolves_in_a_single_hop(): void
    {
        $response = $this->get('/en/projects/github-desktop')->assertStatus(301);

        $this->get($response->headers->get('Location'), ['Accept-Language' => 'en-US'])
            ->assertOk();
    }
}
