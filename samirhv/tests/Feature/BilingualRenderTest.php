<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * A public page rendered in both languages, end to end.
 *
 * `/projects/github-desktop` is a `Route::view` and touches no database, so
 * these assertions are about the language machinery and nothing else: the
 * `lang` attribute, the reciprocal `hreflang` pair, the canonical, the switcher
 * and — the one that matters most — that neither language leaks into the other.
 */
class BilingualRenderTest extends TestCase
{
    private const EN = '/projects/github-desktop';

    private const PT = '/pt-br/projects/github-desktop';

    /** Sent on bare requests to prove the page is not negotiated away. */
    private const EN_HEADER = ['Accept-Language' => 'en-US,en;q=0.9'];

    public function test_the_portuguese_page_declares_and_renders_portuguese(): void
    {
        $this->get(self::PT)
            ->assertOk()
            ->assertSee('lang="pt-BR"', false)
            ->assertSee('Aplicativo desktop')
            ->assertSee('para Linux')
            ->assertSee('Explorar releases');
    }

    public function test_the_english_page_declares_and_renders_english(): void
    {
        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Desktop application')
            ->assertSee('for Linux')
            ->assertSee('Browse releases');
    }

    /** The defect that makes a bilingual site feel broken: half a page each. */
    public function test_no_portuguese_leaks_into_the_english_page(): void
    {
        $response = $this->get(self::EN, self::EN_HEADER)->assertOk();

        foreach (['Aplicativo desktop', 'Explorar releases', 'Navegação', 'Todos os direitos', 'Projeto da comunidade'] as $portuguese) {
            $response->assertDontSee($portuguese);
        }
    }

    public function test_no_english_leaks_into_the_portuguese_page(): void
    {
        $response = $this->get(self::PT)->assertOk();

        foreach (['Desktop application', 'Browse releases', 'All rights reserved', 'A community project'] as $english) {
            $response->assertDontSee($english);
        }
    }

    /**
     * Google discards a non-reciprocal hreflang set without reporting anything,
     * so both pages have to declare the same pair, each including itself.
     */
    public function test_both_pages_declare_the_same_reciprocal_hreflang_pair(): void
    {
        foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
            $this->get($url, $headers)
                ->assertOk()
                ->assertSee('hreflang="en" href="'.url(self::EN).'"', false)
                ->assertSee('hreflang="pt-BR" href="'.url(self::PT).'"', false);
        }
    }

    /** x-default is the negotiating address, which is the bare English url. */
    public function test_both_pages_point_x_default_at_the_bare_url(): void
    {
        foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
            $this->get($url, $headers)
                ->assertOk()
                ->assertSee('hreflang="x-default" href="'.url(self::EN).'"', false);
        }
    }

    public function test_each_page_is_its_own_canonical(): void
    {
        $this->get(self::EN, self::EN_HEADER)
            ->assertSee('rel="canonical" href="'.url(self::EN).'"', false);

        $this->get(self::PT)
            ->assertSee('rel="canonical" href="'.url(self::PT).'"', false);
    }

    /**
     * An internal link on the Portuguese page must not walk the reader back to
     * English.
     *
     * The `href="` prefix is what makes the negative assertion sound:
     * `href="http://host/downloads"` is not a substring of
     * `href="http://host/pt-br/downloads"`, so a Portuguese page full of
     * correct links cannot accidentally satisfy it.
     */
    public function test_internal_links_stay_in_the_page_language(): void
    {
        $this->get(self::PT)
            ->assertOk()
            ->assertSee('href="'.url('/pt-br/downloads').'"', false)
            ->assertDontSee('href="'.url('/downloads').'"', false);
    }

    /** The switcher keeps you on the same page, and works without JavaScript. */
    public function test_the_switcher_points_at_the_twin_of_the_current_page(): void
    {
        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('/lang/pt-br?to='.urlencode(self::PT), false);

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('/lang/en?to='.urlencode(self::EN), false);
    }

    /**
     * The changelog is curated in lang/, so it renders through the same
     * machinery as the rest of the page — including its dates, which is where
     * a half-translated section shows itself first.
     */
    public function test_the_changelog_renders_in_the_page_language(): void
    {
        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('What changed')
            ->assertSee('v0.4.1')
            ->assertSee('03 Aug 2026')
            ->assertDontSee('O que mudou');

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('O que mudou')
            ->assertSee('v0.4.1')
            ->assertSee('03 ago 2026')
            ->assertDontSee('What changed');
    }

    /** `Route::view` stores `view` and `status` as route defaults; neither is a URL parameter. */
    public function test_the_alternate_urls_carry_no_internal_query_string(): void
    {
        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertDontSee('view=projects.github-desktop')
            ->assertDontSee('status=200');
    }
}
