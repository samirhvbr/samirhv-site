<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The LEB-300 page: the second level of LEB exists and has no results yet.
 *
 * It is a `Route::view` and touches no database and no results file, so these
 * assertions are about the language machinery, the links around the page, and
 * the one promise the page makes: nothing on it is a score.
 */
class Leb300PageTest extends TestCase
{
    private const EN = '/ai-benchmark/leb-300';

    private const PT = '/pt-br/ai-benchmark/leb-300';

    private const EN_HEADER = ['Accept-Language' => 'en-US,en;q=0.9'];

    public function test_the_english_page_says_there_are_no_results(): void
    {
        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('There are no LEB-300 results yet.')
            ->assertSee('Nothing on it is a score.');
    }

    public function test_the_portuguese_page_says_there_are_no_results(): void
    {
        $this->get(self::PT)
            ->assertOk()
            ->assertSee('lang="pt-BR"', false)
            ->assertSee('Ainda não há resultados do LEB-300.')
            ->assertSee('Nada nela é uma nota.');
    }

    public function test_no_language_leaks_into_the_other(): void
    {
        $en = $this->get(self::EN, self::EN_HEADER)->assertOk();
        foreach (['Ainda não há', 'Em que pé está', 'O que será publicado', 'Onde ler mais'] as $portuguese) {
            $en->assertDontSee($portuguese);
        }

        $pt = $this->get(self::PT)->assertOk();
        foreach (['There are no LEB-300', 'Where it stands', 'What will be published', 'Where to read more'] as $english) {
            $pt->assertDontSee($english);
        }
    }

    /** Google discards a non-reciprocal hreflang set without saying so. */
    public function test_both_pages_declare_the_same_reciprocal_pair_and_their_own_canonical(): void
    {
        foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
            $this->get($url, $headers)
                ->assertOk()
                ->assertSee('hreflang="en" href="'.url(self::EN).'"', false)
                ->assertSee('hreflang="pt-BR" href="'.url(self::PT).'"', false)
                ->assertSee('hreflang="x-default" href="'.url(self::EN).'"', false)
                ->assertSee('rel="canonical" href="'.url($url).'"', false);
        }
    }

    /** It carries no leaderboard, no run and no flaw table: a page of status, not of numbers. */
    public function test_it_carries_no_results_block(): void
    {
        foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
            $this->get($url, $headers)
                ->assertOk()
                ->assertDontSee('ab-board', false)
                ->assertDontSee('data-scores', false)
                ->assertDontSee('runs.csv', false)
                ->assertDontSee('flaws.csv', false);
        }
    }

    public function test_it_links_to_the_leb_100_page_in_its_own_language(): void
    {
        $this->get(self::EN, self::EN_HEADER)
            ->assertSee('href="'.url('/ai-benchmark').'"', false)
            ->assertDontSee('href="'.url('/pt-br/ai-benchmark').'"', false);

        $this->get(self::PT)
            ->assertSee('href="'.url('/pt-br/ai-benchmark').'"', false)
            ->assertDontSee('href="'.url('/ai-benchmark').'"', false);
    }

    public function test_the_leb_100_page_points_to_it_in_each_language(): void
    {
        $this->get('/ai-benchmark', self::EN_HEADER)
            ->assertOk()
            ->assertSee('href="'.url(self::EN).'"', false)
            ->assertSee('is in preparation');

        $this->get('/pt-br/ai-benchmark')
            ->assertOk()
            ->assertSee('href="'.url(self::PT).'"', false)
            ->assertSee('está em preparação');
    }

    public function test_the_switcher_points_at_the_twin_of_the_page(): void
    {
        $this->get(self::EN, self::EN_HEADER)->assertSee('href="'.url(self::PT).'"', false);
        $this->get(self::PT)->assertSee('href="'.url(self::EN).'"', false);
    }
}
