<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The JSON-LD blocks in the head.
 *
 * Structured data fails silently: a stray quote makes the block invalid JSON,
 * every crawler drops it without a word, and the page keeps rendering exactly
 * as before. Nothing but a parser notices — so a parser runs here.
 */
class StructuredDataTest extends TestCase
{
    /** @return list<array<string, mixed>> */
    private function blocks(string $url, array $headers = []): array
    {
        $html = $this->get($url, $headers)->assertOk()->getContent();

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

        $out = [];
        foreach ($matches[1] as $raw) {
            $decoded = json_decode(trim($raw), true);
            $this->assertIsArray($decoded, 'A JSON-LD block did not parse: '.trim($raw));
            $out[] = $decoded;
        }

        return $out;
    }

    public function test_every_public_page_carries_valid_structured_data(): void
    {
        foreach ([
            '/projects/github-desktop' => ['Accept-Language' => 'en-US'],
            '/pt-br/projects/github-desktop' => [],
        ] as $url => $headers) {
            $blocks = $this->blocks($url, $headers);

            $this->assertNotEmpty($blocks, "[$url] declares no structured data.");
            $this->assertSame('WebSite', $blocks[0]['@type']);
            $this->assertSame('https://schema.org', $blocks[0]['@context']);
        }
    }

    /** The declared language has to match the page, like every other tag in the head. */
    public function test_the_structured_data_declares_the_page_language(): void
    {
        $this->assertSame('en', $this->blocks('/projects/github-desktop', ['Accept-Language' => 'en-US'])[0]['inLanguage']);
        $this->assertSame('pt-BR', $this->blocks('/pt-br/projects/github-desktop')[0]['inLanguage']);
    }

    /** JSON_HEX_TAG is what stops a title containing `</script>` from ending the block. */
    public function test_the_blocks_cannot_be_closed_from_inside(): void
    {
        $html = $this->get('/projects/github-desktop', ['Accept-Language' => 'en-US'])->getContent();

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        $this->assertStringNotContainsString('<', $m[1]);
        $this->assertStringNotContainsString('>', $m[1]);
    }

    public function test_the_social_card_is_declared_with_its_dimensions(): void
    {
        $this->get('/projects/github-desktop', ['Accept-Language' => 'en-US'])
            ->assertOk()
            ->assertSee('property="og:image" content="'.url('/img/og-card.png').'"', false)
            ->assertSee('property="og:image:width" content="1200"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee('property="og:url" content="'.url('/projects/github-desktop').'"', false)
            ->assertSee('property="og:site_name" content="Samirhv"', false);
    }

    /** The card promises a large image; the file has to be there and be that size. */
    public function test_the_social_card_file_exists_at_the_declared_size(): void
    {
        $path = public_path('img/og-card.png');

        $this->assertFileExists($path);
        $this->assertSame([1200, 630], array_slice(getimagesize($path) ?: [], 0, 2));
    }

    public function test_the_page_declares_the_other_language_as_an_alternate(): void
    {
        $this->get('/projects/github-desktop', ['Accept-Language' => 'en-US'])
            ->assertOk()
            ->assertSee('property="og:locale" content="en"', false)
            ->assertSee('property="og:locale:alternate" content="pt_BR"', false);
    }
}
