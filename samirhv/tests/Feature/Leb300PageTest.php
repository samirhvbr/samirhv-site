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
            ->assertSee('ab-caveats', false)
            // The page is built like the LEB-100 page: the hero with the leaders, the facts, the filter and the order.
            ->assertSee('The second level of LEB')
            ->assertSee('See the results')
            ->assertSee('Top 1 so far')
            ->assertSee('Full leaderboard')
            ->assertSee('key private')
            ->assertSee('data-ab-filter', false)
            ->assertSee('Filter by vendor')
            ->assertSee('Order by')
            ->assertSee('data-rank="#1 · "', false)
            ->assertDontSee('There are no LEB-300 results yet.');
    }

    public function test_without_an_aggregate_the_page_keeps_the_hero_and_the_closing_and_no_board(): void
    {
        $this->withoutAggregate();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('The second level of LEB')
            ->assertSee('Method on GitHub')
            ->assertSee('What will be published')
            ->assertSee('LEB-100 results')
            ->assertDontSee('See the results')
            ->assertDontSee('ab-top__list', false)
            ->assertDontSee('Full leaderboard')
            ->assertDontSee('data-ab-filter', false)
            ->assertDontSee('What the record does not have');
    }

    /** One order chip per category plus "Overall", and one filter chip per vendor plus "All", as on the LEB-100 page. */
    public function test_the_order_chips_cover_the_seven_categories_and_the_filter_each_vendor(): void
    {
        $this->withReading();

        $html = $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('Showing 1 of 1 agents')
            ->assertSee('<button type="button" class="ab-chip" data-vendor="Maker" aria-pressed="false">Maker <span class="ab-chip__n">1</span></button>', false)
            ->assertSee('data-vendor="Maker" data-scores="SEC:56 ARCH:25 BUG:29 PERF:0 CLN:0 COMP:100 EXPL:32"', false)
            ->getContent();
        $this->assertSame(8, substr_count($html, 'class="ab-chip" data-sort="'));
        $this->assertSame(2, substr_count($html, 'class="ab-chip" data-vendor="'));
    }

    /** @return array<string, mixed> */
    private function withReading(): array
    {
        $agent = ['agent' => 'agent-x', 'score' => 388, 'grade' => 'Reprovada', 'runs_count' => 2, 'categories' => self::CATEGORIES, 'cost_usd' => 1.21, 'wall_minutes' => 43.7,
            'runs' => [['run' => 1, 'total' => 388, 'cost_usd' => 1.21, 'wall_minutes' => 43.7], ['run' => 2, 'total' => 398, 'cost_usd' => 0.81, 'wall_minutes' => 66.1]],
            'comment' => ['en' => 'Two steady runs, strong on compatibility.', 'pt_BR' => 'Duas execuções estáveis, fortes em compatibilidade.']];
        AiBenchmark::fake([
            'instances' => [['entries' => [['agent' => 'agent-x', 'model' => ['name' => 'Model X', 'provider' => 'Maker', 'reasoning_effort' => 'default']]]]],
            'aggregate_instances' => [['instance' => 'LEB-300-A', 'publication' => 'aggregate', 'edition' => '2026', 'agents' => [$agent]]],
        ]);

        return $agent;
    }

    /** A click on the card opens its comment and details, as on the LEB-100 page: the reading, the categories, and the runs. */
    public function test_a_card_opens_on_its_reading_and_the_details_read_from_the_aggregate(): void
    {
        $this->withReading();

        $html = $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('<details class="ab-row__more">', false)
            ->assertSee('Comment and details')
            ->assertSee('Two steady runs, strong on compatibility.')
            ->assertSee('A written reading of the aggregate; it is not part of the score, and it names no flaw.')
            ->assertSee('2 of 3 runs (388 · 398)')
            ->assertSee('Run 1 · 388')->assertSee('Run 2 · 398')
            ->assertSee('44min')->assertSee('1h 6min')->assertSee('US$ 1.21')->assertSee('US$ 0.81')
            ->assertSee('Compatibility')
            ->assertSee('js/site/ai-benchmark.js', false)
            ->getContent();
        $this->assertSame(1, substr_count($html, '<details class="ab-row__more">'));

        $this->get(self::PT)->assertOk()
            ->assertSee('Duas execuções estáveis, fortes em compatibilidade.')
            ->assertDontSee('Two steady runs')
            ->assertSee('Run 1 · 388')->assertSee('Comentário e ficha');
    }

    public function test_a_card_without_a_reading_still_opens_on_its_runs_and_a_file_without_runs_still_renders(): void
    {
        $agent = $this->withReading();
        unset($agent['comment']);
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [['instance' => 'LEB-300-A', 'publication' => 'aggregate', 'agents' => [$agent]]]]);
        $this->get(self::EN, self::EN_HEADER)->assertOk()->assertSee('Run 2 · 398')->assertDontSee('A written reading of the aggregate');

        unset($agent['runs']);
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [['instance' => 'LEB-300-A', 'publication' => 'aggregate', 'agents' => [$agent]]]]);
        $this->get(self::EN, self::EN_HEADER)->assertOk()->assertSee('Comment and details')->assertDontSee('Run by run');
    }

    /** An agent with no run on LEB-100 is named by the model block its aggregate line carries, not by its raw id. */
    public function test_an_agent_with_no_leb_100_run_is_named_from_its_aggregate_line(): void
    {
        $agent = ['agent' => 'claude-haiku-9.9-xhigh', 'score' => 700, 'grade' => 'Silver', 'runs_count' => 2, 'categories' => self::CATEGORIES,
            'model' => ['name' => 'Claude Haiku 9.9', 'id' => 'claude-haiku-9-9', 'provider' => 'Anthropic', 'reasoning_effort' => 'xhigh']];
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [['instance' => 'LEB-300-A', 'publication' => 'aggregate', 'edition' => '2026', 'agents' => [$agent]]]]);

        $this->get(self::EN, self::EN_HEADER)->assertOk()->assertSee('Claude Haiku 9.9')->assertSee('Anthropic')->assertSee('effort xhigh')->assertDontSee('claude-haiku-9.9-xhigh');

        unset($agent['model']);
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [['instance' => 'LEB-300-A', 'publication' => 'aggregate', 'edition' => '2026', 'agents' => [$agent]]]]);
        $this->get(self::EN, self::EN_HEADER)->assertOk()->assertSee('claude-haiku-9.9-xhigh');   // a file without the block still renders, by the id
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

        // The facts and the leaders come from the same aggregate.
        $agents = $aggregate['agents'];
        $this->assertStringContainsString('mode '.$aggregate['mode'].' · '.$aggregate['turn_budget'].' turns', $html);
        $this->assertStringContainsString('edition '.$aggregate['edition'], $html);
        $this->assertStringContainsString('matrix '.substr($aggregate['matrix_sha256'], 0, 12), $html);
        $this->assertStringContainsString('Top '.min(AiBenchmark::HERO_TOP, count($agents)).' so far', $html);
    }
}
