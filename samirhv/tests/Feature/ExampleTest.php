<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The root responds. Since 0.6.0 it responds by NEGOTIATING: a client that
     * states no `Accept-Language` — which is what a bare test request is — is
     * sent to the English twin, so the honest assertion here is the redirect,
     * not a 200.
     *
     * This scaffold test asserted 200 and was already failing before the
     * bilingual work, for a different reason: rendering `/` needs the database,
     * which a test environment without MySQL does not have. Asserting the
     * redirect makes it meaningful AND independent of the database, because the
     * negotiation happens before the controller.
     *
     * The full render of both languages is covered by BilingualRenderTest.
     */
    public function test_the_root_negotiates_the_language(): void
    {
        $this->get('/')->assertRedirect(url('/en'));
    }

    public function test_a_brazilian_browser_is_not_redirected_away_from_the_bare_url(): void
    {
        $this->get('/projetos/github-desktop', ['Accept-Language' => 'pt-BR,pt;q=0.9'])
            ->assertOk();
    }
}
