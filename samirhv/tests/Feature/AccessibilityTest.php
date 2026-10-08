<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The parts of the public shell a keyboard or a screen reader depends on.
 *
 * These regressed once already and nobody noticed, because an inaccessible
 * page looks exactly like an accessible one.
 */
class AccessibilityTest extends TestCase
{
    private const EN = ['Accept-Language' => 'en-US'];

    public function test_the_page_declares_its_language(): void
    {
        $this->get('/projects/github-desktop', self::EN)->assertSee('lang="en"', false);
        $this->get('/pt-br/projects/github-desktop')->assertSee('lang="pt-BR"', false);
    }

    /** First focusable element on the page, in the reader's language. */
    public function test_a_skip_link_leads_to_the_main_content(): void
    {
        $this->get('/projects/github-desktop', self::EN)
            ->assertOk()
            ->assertSee('class="s-skip" href="#content"', false)
            ->assertSee('Skip to content')
            ->assertSee('<main id="content"', false);

        $this->get('/pt-br/projects/github-desktop')
            ->assertOk()
            ->assertSee('Pular para o conteúdo', false);
    }

    /**
     * The projects menu used to be `<a href="#" onclick="return false;">`: a
     * link that does not link, opened only by :hover, so it did not exist for
     * anyone navigating by keyboard.
     */
    public function test_the_projects_menu_opens_from_a_button_that_states_its_state(): void
    {
        $html = $this->get('/projects/github-desktop', self::EN)->assertOk()->getContent();

        // The nav only renders with projects in the database; skip when empty.
        if (! str_contains($html, 'id="nav-projects"')) {
            $this->markTestSkipped('No projects in the nav on this environment.');
        }

        $this->assertStringContainsString('<button type="button" class="menu-link s-dd-trigger"', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('aria-controls="nav-projects"', $html);
        $this->assertStringNotContainsString('onclick="return false;"', $html);
    }

    /**
     * AI Benchmark is a menu of its own, and it needs no project in the database. Same contract as the projects menu: a
     * button that says what it does and carries its state, with the three pages as links in the current language.
     */
    public function test_the_ai_benchmark_menu_is_a_button_with_the_three_pages_in_the_current_language(): void
    {
        foreach (['/projects/github-desktop' => ['', self::EN], '/pt-br/projects/github-desktop' => ['/pt-br', []]] as $page => [$prefix, $headers]) {
            $html = $this->get($page, $headers)->assertOk()->getContent();

            $this->assertMatchesRegularExpression('#<button type="button" class="menu-link s-dd-trigger" aria-expanded="false" aria-controls="nav-ai-benchmark" data-dd-trigger>\s*<div>AI Benchmark#', $html);

            preg_match('#<ul class="s-dd" id="nav-ai-benchmark">(.*?)</ul>#s', $html, $menu);
            $this->assertNotEmpty($menu, 'The AI Benchmark menu list is missing.');
            foreach (['/ai-benchmark', '/ai-benchmark/leb-100', '/ai-benchmark/leb-300'] as $path) {
                $this->assertStringContainsString('href="'.url($prefix.$path).'"', $menu[1]);
            }
            $this->assertStringContainsString('LEB-100-A', $menu[1]);
            $this->assertStringContainsString('LEB-300-A', $menu[1]);
        }
    }

    /** An image with no alt is invisible to a reader; one with a bad alt is worse. */
    public function test_every_image_carries_an_alt_attribute(): void
    {
        $html = $this->get('/projects/github-desktop', self::EN)->assertOk()->getContent();

        preg_match_all('/<img\b[^>]*>/i', $html, $images);

        foreach ($images[0] as $img) {
            $this->assertMatchesRegularExpression('/\balt=("|\')/', $img, "An <img> has no alt: $img");
        }
    }

    /** Landmarks a reader jumps between. */
    public function test_the_shell_declares_its_landmarks(): void
    {
        $this->get('/projects/github-desktop', self::EN)
            ->assertOk()
            ->assertSee('<main id="content"', false)
            ->assertSee('<footer', false)
            ->assertSee('aria-current="page"', false);
    }
}
