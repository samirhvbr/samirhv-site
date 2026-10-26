<?php

namespace Tests\Feature;

use App\Support\Locales;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * The file that tells a crawler this site has two languages.
 *
 * No database here, and none needed: the static pages come from the route
 * table, and the project rows degrade to none when the database is absent —
 * which is also what should happen in production during a migration, since a
 * crawler losing the project urls beats a crawler getting a 500.
 */
class SitemapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('sitemap.xml');
    }

    public function test_it_is_served_as_xml(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee('xmlns:xhtml="http://www.w3.org/1999/xhtml"', false);
    }

    public function test_it_parses_as_well_formed_xml(): void
    {
        $xml = simplexml_load_string($this->get('/sitemap.xml')->getContent());

        $this->assertNotFalse($xml, 'The sitemap is not well-formed XML.');
        $this->assertGreaterThan(0, $xml->count());
    }

    public function test_every_static_page_appears_in_both_languages(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        foreach (['/', '/downloads', '/projects/github-desktop',
            '/pt-br', '/pt-br/downloads', '/pt-br/projects/github-desktop'] as $path) {
            $response->assertSee('<loc>'.url($path).'</loc>', false);
        }
    }

    /** Each url declares the whole set, itself included, or Google drops it. */
    public function test_each_entry_carries_the_full_alternate_set(): void
    {
        $xml = simplexml_load_string($this->get('/sitemap.xml')->getContent());

        foreach ($xml->url as $url) {
            $hreflangs = [];
            foreach ($url->children('http://www.w3.org/1999/xhtml')->link as $link) {
                $hreflangs[] = (string) $link->attributes()->hreflang;
            }

            $this->assertContains('en', $hreflangs, (string) $url->loc);
            $this->assertContains('pt-BR', $hreflangs, (string) $url->loc);
            $this->assertContains('x-default', $hreflangs, (string) $url->loc);
        }
    }

    /** x-default is the address that negotiates, which is the bare url. */
    public function test_x_default_points_at_the_bare_language(): void
    {
        $xml = simplexml_load_string($this->get('/sitemap.xml')->getContent());

        $home = $xml->url[0];
        foreach ($home->children('http://www.w3.org/1999/xhtml')->link as $link) {
            if ((string) $link->attributes()->hreflang === 'x-default') {
                $this->assertSame(Locales::homeUrl(Locales::BARE), (string) $link->attributes()->href);

                return;
            }
        }

        $this->fail('The home entry declares no x-default.');
    }

    /** A sitemap must not list an address that immediately redirects. */
    public function test_it_lists_no_legacy_or_redirecting_url(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $response->assertDontSee('<loc>'.url('/en').'</loc>', false);
        $response->assertDontSee('/projetos/', false);
    }
}
