<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The Google tag (gtag.js) in the public head.
 *
 * A tag that is missing, mistyped or loaded twice reports nothing and breaks
 * nothing: the page renders the same and the property just stays empty. So the
 * id the property expects, the single load and the off switch are asserted here.
 */
class GoogleTagTest extends TestCase
{
    private const ID = 'G-WTE9C2ZB0V';

    public function test_the_public_head_loads_the_tag_once_and_configures_the_property(): void
    {
        config(['services.google.tag_id' => self::ID]);

        foreach (['/projects/github-desktop', '/pt-br/projects/github-desktop'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertSame(
                1,
                substr_count($html, 'https://www.googletagmanager.com/gtag/js?id='.self::ID),
                "[$url] must load gtag.js exactly once.",
            );
            $this->assertStringContainsString("gtag('config', '".self::ID."');", $html, "[$url] must configure the property.");
            $this->assertStringContainsString('<script async src=', $html);

            $head = substr($html, 0, strpos($html, '</head>'));
            $this->assertStringContainsString('googletagmanager.com', $head, "[$url] the tag belongs in the head.");
        }
    }

    public function test_an_empty_id_turns_the_tag_off(): void
    {
        config(['services.google.tag_id' => '']);

        $html = $this->get('/projects/github-desktop')->assertOk()->getContent();

        $this->assertStringNotContainsString('googletagmanager.com', $html);
        $this->assertStringNotContainsString('gtag(', $html);
    }

    public function test_the_login_view_does_not_carry_the_tag(): void
    {
        config(['services.google.tag_id' => self::ID]);

        $this->get('/login')->assertOk()->assertDontSee('googletagmanager.com', false);
    }
}
