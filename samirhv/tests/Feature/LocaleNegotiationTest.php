<?php

namespace Tests\Feature;

use App\Support\Locales;
use Tests\TestCase;

/**
 * The language a URL renders in, and where an unprefixed URL sends you.
 *
 * Every case here uses `/projects/github-desktop`, a `Route::view` that touches
 * no database — so what fails is the negotiation, never the data.
 */
class LocaleNegotiationTest extends TestCase
{
    private const EN = '/projects/github-desktop';

    private const PT = '/pt-br/projects/github-desktop';

    public function test_a_browser_asking_for_english_stays_on_the_bare_url(): void
    {
        $this->get(self::EN, ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertOk();
    }

    public function test_a_browser_asking_for_brazilian_portuguese_is_sent_to_the_pt_br_twin(): void
    {
        $this->get(self::EN, ['Accept-Language' => 'pt-BR,pt;q=0.9,en;q=0.8'])
            ->assertRedirect(url(self::PT));
    }

    /**
     * Silence resolves to English, and English is what the bare url already
     * serves — so it costs no redirect at all. Under the old scheme this same
     * request ate a 302 on every anonymous hit.
     */
    public function test_a_browser_stating_no_preference_is_not_redirected_at_all(): void
    {
        $this->get(self::EN)->assertOk();
    }

    /** An explicit address outranks every preference, including the cookie. */
    public function test_the_pt_br_url_is_never_redirected_away(): void
    {
        $this->withCookie(Locales::COOKIE, 'en')
            ->get(self::PT, ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertOk();
    }

    public function test_the_cookie_outranks_the_header(): void
    {
        $this->withCookie(Locales::COOKIE, 'en')
            ->get(self::EN, ['Accept-Language' => 'pt-BR,pt;q=0.9'])
            ->assertOk();

        $this->withCookie(Locales::COOKIE, 'pt_BR')
            ->get(self::EN, ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertRedirect(url(self::PT));
    }

    /**
     * The cookie survives the inversion untouched: it holds locale IDS
     * (`pt_BR`, `en`), never url segments, so a visitor who chose Portuguese
     * before 0.7.0 is still sent to Portuguese after it. No migration, no
     * clearing, no version bump on the cookie.
     */
    public function test_a_cookie_written_before_the_inversion_still_steers(): void
    {
        $this->withCookie(Locales::COOKIE, 'pt_BR')
            ->get(self::EN)
            ->assertRedirect(url(self::PT));
    }

    /** A cookie holding anything else must not steer, and must not crash. */
    public function test_a_junk_cookie_falls_back_to_the_header(): void
    {
        $this->withCookie(Locales::COOKIE, 'klingon')
            ->get(self::EN, ['Accept-Language' => 'pt-BR,pt;q=0.9'])
            ->assertRedirect(url(self::PT));
    }

    public function test_the_negotiated_response_declares_vary(): void
    {
        $this->get(self::EN, ['Accept-Language' => 'en-US'])
            ->assertOk()
            ->assertHeader('Vary', 'Accept-Language, Cookie');

        $this->get(self::EN, ['Accept-Language' => 'pt-BR'])
            ->assertRedirect(url(self::PT))
            ->assertHeader('Vary', 'Accept-Language, Cookie');
    }

    public function test_the_switcher_remembers_the_choice_and_keeps_the_page(): void
    {
        $this->get('/lang/en?to=/downloads')
            ->assertRedirect(url('/downloads'))
            ->assertCookie(Locales::COOKIE, 'en');

        $this->get('/lang/pt-br?to=/pt-br/downloads')
            ->assertRedirect(url('/pt-br/downloads'))
            ->assertCookie(Locales::COOKIE, 'pt_BR');
    }

    /**
     * `to` is attacker-controlled. An absolute url was already refused; a
     * PROTOCOL-RELATIVE one was not — `str_starts_with($to, '/')` accepts
     * `//evil.example`, `UrlGenerator::isValidUrl()` matches `~^(#|//|…)~` and
     * returns the string verbatim, so the site emitted
     * `Location: //evil.example/phish`. A live open redirect until 0.7.0.
     */
    public function test_the_switcher_refuses_an_off_site_destination(): void
    {
        foreach ([
            'https://evil.example/phish',
            '//evil.example/phish',
            '/\\evil.example/phish',
            'evil.example',
        ] as $hostile) {
            $this->get('/lang/en?to='.urlencode($hostile))
                ->assertRedirect(url('/'));
        }
    }

    /**
     * With no `to` at all the switcher lands on the home page of the chosen
     * language. It must NOT consult `Locales::sameRouteIn()`: the current route
     * during this request is `lang.set`, whose twin under the new prefixes
     * resolves back to `lang.set` — the switcher would redirect to itself.
     */
    public function test_the_switcher_with_no_destination_lands_on_that_language_home(): void
    {
        $this->get('/lang/en')->assertRedirect(url('/'));
        $this->get('/lang/pt-br')->assertRedirect(url('/pt-br'));
    }

    /** `canonical()` is case-insensitive; the route constraint used not to be. */
    public function test_the_switcher_accepts_every_spelling_of_the_locale(): void
    {
        foreach (['pt-br', 'pt-BR', 'PT-BR', 'pt_BR'] as $spelling) {
            $this->get('/lang/'.$spelling)
                ->assertRedirect(url('/pt-br'))
                ->assertCookie(Locales::COOKIE, 'pt_BR');
        }
    }

    public function test_an_unknown_locale_does_not_set_a_cookie(): void
    {
        $this->get('/lang/klingon')->assertNotFound();
    }
}
