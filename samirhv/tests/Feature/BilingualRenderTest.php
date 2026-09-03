<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * A public page rendered in both languages, end to end.
 *
 * `/projetos/github-desktop` is a `Route::view` and touches no database, so
 * these assertions are about the language machinery and nothing else: the
 * `lang` attribute, the reciprocal `hreflang` pair, the canonical, the switcher
 * and — the one that matters most — that neither language leaks into the other.
 */
class BilingualRenderTest extends TestCase
{
    private const BARE = '/projetos/github-desktop';
    private const EN = '/en/projetos/github-desktop';

    private const PT_HEADER = ['Accept-Language' => 'pt-BR,pt;q=0.9'];

    public function test_the_portuguese_page_declares_and_renders_portuguese(): void
    {
        $this->get(self::BARE, self::PT_HEADER)
            ->assertOk()
            ->assertSee('lang="pt-BR"', false)
            ->assertSee('Aplicativo desktop')
            ->assertSee('para Linux')
            ->assertSee('Explorar releases');
    }

    public function test_the_english_page_declares_and_renders_english(): void
    {
        $this->get(self::EN)
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Desktop application')
            ->assertSee('for Linux')
            ->assertSee('Browse releases');
    }

    /** The defect that makes a bilingual site feel broken: half a page each. */
    public function test_no_portuguese_leaks_into_the_english_page(): void
    {
        $response = $this->get(self::EN)->assertOk();

        foreach (['Aplicativo desktop', 'Explorar releases', 'Navegação', 'Todos os direitos', 'Projeto da comunidade'] as $portuguese) {
            $response->assertDontSee($portuguese);
        }
    }

    public function test_no_english_leaks_into_the_portuguese_page(): void
    {
        $response = $this->get(self::BARE, self::PT_HEADER)->assertOk();

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
        foreach ([self::BARE => self::PT_HEADER, self::EN => []] as $url => $headers) {
            $this->get($url, $headers)
                ->assertOk()
                ->assertSee('hreflang="pt-BR" href="'.url(self::BARE).'"', false)
                ->assertSee('hreflang="en" href="'.url(self::EN).'"', false);
        }
    }

    public function test_each_page_is_its_own_canonical(): void
    {
        $this->get(self::BARE, self::PT_HEADER)
            ->assertSee('rel="canonical" href="'.url(self::BARE).'"', false);

        $this->get(self::EN)
            ->assertSee('rel="canonical" href="'.url(self::EN).'"', false);
    }

    /** An internal link on the English page must not walk the reader back to Portuguese. */
    public function test_internal_links_stay_in_the_page_language(): void
    {
        $this->get(self::EN)
            ->assertOk()
            ->assertSee('href="'.url('/en/downloads').'"', false)
            ->assertDontSee('href="'.url('/downloads').'"', false);
    }

    /** The switcher keeps you on the same page, and works without JavaScript. */
    public function test_the_switcher_points_at_the_twin_of_the_current_page(): void
    {
        $this->get(self::BARE, self::PT_HEADER)
            ->assertOk()
            ->assertSee('/lang/en?to='.urlencode(self::EN), false);

        $this->get(self::EN)
            ->assertOk()
            ->assertSee('/lang/pt-br?to='.urlencode(self::BARE), false);
    }

    /** `Route::view` stores `view` and `status` as route defaults; neither is a URL parameter. */
    public function test_the_alternate_urls_carry_no_internal_query_string(): void
    {
        $this->get(self::EN)
            ->assertOk()
            ->assertDontSee('view=projects.github-desktop')
            ->assertDontSee('status=200');
    }
}
