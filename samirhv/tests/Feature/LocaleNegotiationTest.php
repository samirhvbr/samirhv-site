<?php

namespace Tests\Feature;

use App\Support\Locales;
use Tests\TestCase;

/**
 * The language a URL renders in, and where an unprefixed URL sends you.
 *
 * Every case here uses `/projetos/github-desktop`, a `Route::view` that touches
 * no database — so what fails is the negotiation, never the data.
 */
class LocaleNegotiationTest extends TestCase
{
    private const BARE = '/projetos/github-desktop';
    private const EN = '/en/projetos/github-desktop';

    public function test_a_browser_asking_for_english_is_sent_to_the_en_twin(): void
    {
        $this->get(self::BARE, ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertRedirect(url(self::EN));
    }

    public function test_a_browser_asking_for_brazilian_portuguese_stays_on_the_bare_url(): void
    {
        $this->get(self::BARE, ['Accept-Language' => 'pt-BR,pt;q=0.9,en;q=0.8'])
            ->assertOk();
    }

    /** Silence has to resolve to something, and English is the one that travels. */
    public function test_a_browser_stating_no_preference_gets_english(): void
    {
        $this->get(self::BARE)->assertRedirect(url(self::EN));
    }

    /** An explicit address outranks every preference, including the cookie. */
    public function test_the_en_url_is_never_redirected_away(): void
    {
        $this->withCookie(Locales::COOKIE, 'pt_BR')
            ->get(self::EN, ['Accept-Language' => 'pt-BR,pt;q=0.9'])
            ->assertOk();
    }

    public function test_the_cookie_outranks_the_header(): void
    {
        $this->withCookie(Locales::COOKIE, 'pt_BR')
            ->get(self::BARE, ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertOk();

        $this->withCookie(Locales::COOKIE, 'en')
            ->get(self::BARE, ['Accept-Language' => 'pt-BR,pt;q=0.9'])
            ->assertRedirect(url(self::EN));
    }

    /** A cookie holding anything else must not steer, and must not crash. */
    public function test_a_junk_cookie_falls_back_to_the_header(): void
    {
        $this->withCookie(Locales::COOKIE, 'klingon')
            ->get(self::BARE, ['Accept-Language' => 'pt-BR,pt;q=0.9'])
            ->assertOk();
    }

    public function test_the_negotiated_response_declares_vary(): void
    {
        $this->get(self::BARE, ['Accept-Language' => 'pt-BR'])
            ->assertOk()
            ->assertHeader('Vary', 'Accept-Language, Cookie');

        $this->get(self::BARE, ['Accept-Language' => 'en-US'])
            ->assertRedirect(url(self::EN))
            ->assertHeader('Vary', 'Accept-Language, Cookie');
    }

    public function test_the_switcher_remembers_the_choice_and_keeps_the_page(): void
    {
        $this->get('/lang/en?to=/downloads')
            ->assertRedirect(url('/downloads'))
            ->assertCookie(Locales::COOKIE, 'en');

        $this->get('/lang/pt-br?to=/downloads')
            ->assertRedirect(url('/downloads'))
            ->assertCookie(Locales::COOKIE, 'pt_BR');
    }

    /** `to` is attacker-controlled: an absolute url must not become an open redirect. */
    public function test_the_switcher_refuses_an_off_site_destination(): void
    {
        $response = $this->get('/lang/en?to=https://evil.example/phish');

        $response->assertRedirect(url('/en'));
    }

    public function test_an_unknown_locale_does_not_set_a_cookie(): void
    {
        $this->get('/lang/klingon')->assertNotFound();
    }
}
