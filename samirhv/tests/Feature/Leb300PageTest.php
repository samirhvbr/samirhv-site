<?php

namespace Tests\Feature;

use App\Support\AiBenchmark;
use Tests\TestCase;

/**
 * The LEB-300 page: a status page until the synced results file carries an aggregate for it, then a scoreboard.
 *
 * It touches no database. Both states are exercised with `AiBenchmark::fake`, so the tests do not depend on
 * what the synced copy holds today; one test then checks the real file. What the page must never do is name a
 * flaw: LEB-300 is an ACTIVE instance and only its aggregate is published.
 */
class Leb300PageTest extends TestCase
{
    private const EN = '/ai-benchmark/leb-300';

    private const PT = '/pt-br/ai-benchmark/leb-300';

    private const EN_HEADER = ['Accept-Language' => 'en-US,en;q=0.9'];

    private const CATEGORIES = ['SEC' => 56, 'ARCH' => 25, 'BUG' => 29, 'PERF' => 0, 'CLN' => 0, 'COMP' => 100, 'EXPL' => 32];

    protected function tearDown(): void
    {
        AiBenchmark::flush();
        parent::tearDown();
    }

    private function withAggregate(int $runs = 1): void
    {
        AiBenchmark::fake([
            'instances' => [['entries' => [['agent' => 'agent-x', 'model' => ['name' => 'Model X', 'provider' => 'Maker', 'reasoning_effort' => 'default']]]]],
            'aggregate_instances' => [[
                'instance' => 'LEB-300-A', 'publication' => 'aggregate', 'edition' => '2026',
                'agents' => [['agent' => 'agent-x', 'score' => 227, 'grade' => 'Reprovada', 'runs_count' => $runs,
                    'categories' => self::CATEGORIES, 'cost_usd' => 0.96, 'wall_minutes' => 13.4]],
            ]],
        ]);
    }

    private function withoutAggregate(): void
    {
        AiBenchmark::fake(['instances' => []]);
    }

    // ── the status state ─────────────────────────────────────────────────────────────────────────

    public function test_without_an_aggregate_the_english_page_says_there_are_no_results(): void
    {
        $this->withoutAggregate();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('There are no LEB-300 results yet.')
            ->assertSee('Nothing on it is a score.')
            ->assertDontSee('ab-board', false);
    }

    public function test_without_an_aggregate_the_portuguese_page_says_there_are_no_results(): void
    {
        $this->withoutAggregate();

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('lang="pt-BR"', false)
            ->assertSee('Ainda não há resultados do LEB-300.')
            ->assertSee('Nada nela é uma nota.')
            ->assertDontSee('ab-board', false);
    }

    // ── the result state ─────────────────────────────────────────────────────────────────────────

    public function test_with_an_aggregate_the_english_page_shows_the_scoreboard_and_says_it_is_not_official(): void
    {
        $this->withAggregate();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('ab-board', false)
            ->assertSee('Model X')
            ->assertSee('Maker')
            ->assertSee('227')
            ->assertSee('of 1000')
            ->assertSee('ab-grade--reprovada', false)
            ->assertSee('1 of 3 runs')
            ->assertSee('not official')
            ->assertSee('Cost US$ 0.96 a run')
            ->assertSee('Session 13.4 min')
            ->assertSee('An exploratory pilot.')
            ->assertSee('A line that rests on fewer runs is not official')
            ->assertSee('What the record does not have')
            ->assertDontSee('There are no LEB-300 results yet.');
    }

    public function test_with_an_aggregate_the_portuguese_page_shows_the_scoreboard(): void
    {
        $this->withAggregate();

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('ab-board', false)
            ->assertSee('Model X')
            ->assertSee('227')
            ->assertSee(trans('ai_benchmark.out_of', [], 'pt_BR'))
            ->assertSee(trans('ai_benchmark.runs_label', ['count' => 1], 'pt_BR'))
            ->assertSee('Um piloto exploratório.')
            ->assertSee('Uma linha que se apoia em menos execuções não é oficial')
            ->assertSee('O que o registro não tem')
            ->assertDontSee('Ainda não há resultados do LEB-300.');
    }

    public function test_each_category_shows_its_score_over_its_weight(): void
    {
        $this->withAggregate();
        $weights = ['SEC' => 250, 'ARCH' => 200, 'BUG' => 150, 'PERF' => 150, 'CLN' => 100, 'COMP' => 100, 'EXPL' => 50];

        $html = $this->get(self::EN, self::EN_HEADER)->assertOk()->getContent();
        foreach (self::CATEGORIES as $cat => $score) {
            $this->assertMatchesRegularExpression('#data-cat="'.$cat.'".*?'.$score.'<small>/'.$weights[$cat].'</small>#s', $html, $cat);
        }
    }

    public function test_three_runs_are_no_longer_marked_not_official(): void
    {
        $this->withAggregate(3);

        $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('3 of 3 runs')
            ->assertDontSee('not official</span>', false)
            ->assertSee('Each score is the median of three runs of an agent, as the protocol asks.')
            ->assertSee('its difficulty has not been homologated')
            ->assertDontSee('A line that rests on fewer runs is not official');
    }

    // ── what the page must never do ──────────────────────────────────────────────────────────────

    /** Agents on different numbers of runs share one board: each line says its own count, and only the short ones are unofficial. */
    public function test_agents_on_different_numbers_of_runs_each_say_their_own_count(): void
    {
        $agent = fn (string $id, int $score, int $runs) => ['agent' => $id, 'score' => $score, 'grade' => 'Reprovada', 'runs_count' => $runs,
            'categories' => self::CATEGORIES, 'cost_usd' => 0.5, 'wall_minutes' => 10.0];
        AiBenchmark::fake([
            'instances' => [['entries' => [
                ['agent' => 'agent-a', 'model' => ['name' => 'Model A', 'provider' => 'Maker', 'reasoning_effort' => 'high']],
                ['agent' => 'agent-b', 'model' => ['name' => 'Model B', 'provider' => 'Maker', 'reasoning_effort' => 'default']],
            ]]],
            'aggregate_instances' => [['instance' => 'LEB-300-A', 'publication' => 'aggregate', 'edition' => '2026',
                'agents' => [$agent('agent-a', 649, 1), $agent('agent-b', 242, 3)]]],
        ]);

        $html = $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('Model A')->assertSee('Model B')
            ->assertSee('1 of 3 runs')->assertSee('3 of 3 runs')
            ->assertSee('The results so far')
            ->assertSee('A line that rests on fewer runs is not official')
            ->assertDontSee('Each score is the median of three runs of an agent')
            ->assertSee('did not all run in the same client')
            ->getContent();
        $this->assertSame(1, substr_count($html, '<span class="ab-row__runs">') - substr_count($html, 'ab-row__runs">3 of 3 runs</span>'));

        $this->get(self::PT)->assertOk()->assertSee('Os resultados até agora')->assertSee('Uma linha que se apoia em menos execuções não é oficial')
            ->assertSee('não rodaram todos no mesmo cliente');
    }

    public function test_neither_state_names_a_flaw_or_carries_a_flaw_table(): void
    {
        foreach (['withoutAggregate', 'withAggregate'] as $state) {
            $this->{$state}();
            foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
                $html = $this->get($url, $headers)->assertOk()->getContent();
                $this->assertDoesNotMatchRegularExpression('/\b(?:SEC|ARCH|PERF|BUG|CLN)-\d{3}/', $html, "$state $url");
                foreach (['ab-flaws', 'runs.csv', 'flaws.csv', 'scorecard.md'] as $marker) {
                    $this->assertStringNotContainsString($marker, $html, "$state $url $marker");
                }
            }
        }
    }

    public function test_no_language_leaks_into_the_other_in_either_state(): void
    {
        foreach (['withoutAggregate', 'withAggregate'] as $state) {
            $this->{$state}();

            $en = $this->get(self::EN, self::EN_HEADER)->assertOk();
            foreach (['Ainda não há', 'Em que pé está', 'O que é publicado', 'Onde ler mais', 'O que o registro não tem'] as $portuguese) {
                $en->assertDontSee($portuguese);
            }

            $pt = $this->get(self::PT)->assertOk();
            foreach (['There are no LEB-300', 'Where it stands', 'What is published', 'Where to read more', 'What the record does not have'] as $english) {
                $pt->assertDontSee($english);
            }
        }
    }

    // ── language machinery and links, whatever the state ────────────────────────────────────────

    /** Google discards a non-reciprocal hreflang set without saying so. */
    public function test_both_pages_declare_the_same_reciprocal_pair_and_their_own_canonical(): void
    {
        $this->withAggregate();

        foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
            $this->get($url, $headers)
                ->assertOk()
                ->assertSee('hreflang="en" href="'.url(self::EN).'"', false)
                ->assertSee('hreflang="pt-BR" href="'.url(self::PT).'"', false)
                ->assertSee('hreflang="x-default" href="'.url(self::EN).'"', false)
                ->assertSee('rel="canonical" href="'.url($url).'"', false);
        }
    }

    public function test_it_links_to_the_leb_100_and_benchmark_pages_in_its_own_language(): void
    {
        $this->withAggregate();

        $this->get(self::EN, self::EN_HEADER)
            ->assertSee('href="'.url('/ai-benchmark/leb-100').'"', false)
            ->assertSee('href="'.url('/ai-benchmark').'"', false)
            ->assertDontSee('href="'.url('/pt-br/ai-benchmark/leb-100').'"', false)
            ->assertDontSee('href="'.url('/pt-br/ai-benchmark').'"', false);

        $this->get(self::PT)
            ->assertSee('href="'.url('/pt-br/ai-benchmark/leb-100').'"', false)
            ->assertSee('href="'.url('/pt-br/ai-benchmark').'"', false)
            ->assertDontSee('href="'.url('/ai-benchmark/leb-100').'"', false)
            ->assertDontSee('href="'.url('/ai-benchmark').'"', false);
    }

    /** The general explanation of LEB (and of the two levels) is on the Benchmark page; this one keeps the instance. */
    public function test_it_leaves_the_general_explanation_to_the_benchmark_page(): void
    {
        $this->withAggregate();

        $this->get(self::EN, self::EN_HEADER)->assertOk()->assertDontSee('What it is')->assertDontSee('is handed a working system');
        $this->get(self::PT)->assertOk()->assertDontSee('recebe um sistema funcionando');
    }

    public function test_the_leb_100_page_points_to_it_and_says_which_state_it_is_in(): void
    {
        $this->withoutAggregate();
        $this->get('/ai-benchmark/leb-100', self::EN_HEADER)->assertOk()->assertSee('href="'.url(self::EN).'"', false)->assertSee('is in preparation');
        $this->get('/pt-br/ai-benchmark/leb-100')->assertOk()->assertSee('href="'.url(self::PT).'"', false)->assertSee('está em preparação');

        // The LEB-100 view needs whole instances; only the aggregate matters to the note, so no instance is faked.
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [['instance' => 'LEB-300-A', 'publication' => 'aggregate', 'agents' => []]]]);
        $this->get('/ai-benchmark/leb-100', self::EN_HEADER)->assertOk()->assertSee('is an exploratory pilot');
        $this->get('/pt-br/ai-benchmark/leb-100')->assertOk()->assertSee('está em piloto exploratório');
    }

    public function test_the_switcher_points_at_the_twin_of_the_page(): void
    {
        $this->withAggregate();

        $this->get(self::EN, self::EN_HEADER)->assertSee('href="'.url(self::PT).'"', false);
        $this->get(self::PT)->assertSee('href="'.url(self::EN).'"', false);
    }

    // ── the real synced file ─────────────────────────────────────────────────────────────────────

    /** The page shows what the synced copy says, number for number. */
    public function test_the_real_synced_aggregate_is_shown_as_it_is(): void
    {
        $aggregate = AiBenchmark::aggregate('LEB-300-A');
        if ($aggregate === null) {
            $this->markTestSkipped('the synced results file carries no LEB-300-A aggregate yet');
        }

        $html = $this->get(self::EN, self::EN_HEADER)->assertOk()->getContent();
        foreach ($aggregate['agents'] as $agent) {
            $this->assertStringContainsString('<span class="ab-score">'.$agent['score'].'</span>', $html, $agent['agent']);
            $this->assertStringContainsString('ab-grade--'.strtolower($agent['grade']), $html);
        }
        $this->assertDoesNotMatchRegularExpression('/\b(?:SEC|ARCH|PERF|BUG|CLN)-\d{3}/', $html);
    }
}
