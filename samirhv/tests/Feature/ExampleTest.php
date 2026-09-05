<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The root responds, and it responds by NEGOTIATING.
     *
     * Only the redirect case is asserted here, and deliberately: rendering `/`
     * for real needs the database, which a test environment without MySQL does
     * not have. Negotiation runs before the controller, so a Portuguese browser
     * is answered with a 302 without a single query.
     *
     * The 200 case — an English browser staying on a bare url — is asserted
     * below on the static page, which is database-free. The full render of both
     * languages is covered by BilingualRenderTest.
     */
    public function test_the_root_negotiates_the_language(): void
    {
        $this->get('/', ['Accept-Language' => 'pt-BR,pt;q=0.9'])
            ->assertRedirect(url('/pt-br'));
    }

    public function test_an_english_browser_is_not_redirected_away_from_a_bare_url(): void
    {
        $this->get('/projects/github-desktop', ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertOk();
    }
}
