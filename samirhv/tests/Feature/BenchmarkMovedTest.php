<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The AI Benchmark lives on shvia.org (1.1.0). This site keeps no page of it:
 * every address it ever had answers one permanent redirect to the Benchmark page
 * there, in the language of the address, and the header and footer link to it.
 */
class BenchmarkMovedTest extends TestCase
{
    private const EN = 'https://shvia.org/en/ai-benchmark.html';

    private const PT = 'https://shvia.org/pt-br/ai-benchmark.html';

    public function test_every_old_benchmark_address_redirects_to_the_benchmark_page_of_shvia(): void
    {
        $pages = ['', '/leb-100', '/leb-300', '/leb-default', '/leb-security', '/leb-unknown'];

        foreach (['' => self::EN, '/en' => self::EN, '/pt-br' => self::PT] as $prefix => $target) {
            foreach ($pages as $page) {
                $this->get($prefix.'/ai-benchmark'.$page)->assertStatus(301)->assertRedirect($target);
            }
        }
    }

    public function test_the_old_csv_addresses_land_on_the_same_files_at_shvia(): void
    {
        foreach (['runs.csv', 'flaws.csv'] as $file) {
            foreach (['/downloads/ai-benchmark/', '/data/ai-benchmark/'] as $folder) {
                $this->get($folder.$file)->assertStatus(301)->assertRedirect('https://shvia.org/data/ai-benchmark/'.$file);
            }
        }
    }

    public function test_the_header_and_the_footer_link_to_shvia_in_the_language_of_the_page(): void
    {
        foreach (['/projects/github-desktop' => [self::EN, ['Accept-Language' => 'en-US']], '/pt-br/projects/github-desktop' => [self::PT, []]] as $path => [$target, $headers]) {
            $html = $this->get($path, $headers)->assertOk()->getContent();

            $this->assertSame(2, substr_count($html, 'href="'.$target.'"'), 'The header and the footer each carry the link.');
            $this->assertStringNotContainsString('nav-ai-benchmark', $html);
            $this->assertStringNotContainsString('/ai-benchmark/leb-', $html);
        }
    }

    public function test_the_sitemap_lists_no_benchmark_page(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('ai-benchmark', false);
    }
}
