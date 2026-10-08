<?php

namespace Tests\Feature;

use App\Support\AiBenchmark;
use Tests\TestCase;

/**
 * The AI Benchmark menu is the way into the results, and says what they hold before the click: under each
 * instance, a second line with how many agents it ranks and the top score, read from the same synced results
 * file the pages read (AppServiceProvider::shareNavBenchmark). An instance with nothing published gets no line.
 */
class BenchmarkMenuTest extends TestCase
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

    /** @param  list<array{string, int}>  $agents300  agent id and score, in rank order */
    private function fake(array $agents300, bool $withLeb100 = false): void
    {
        $agent = fn (string $id, int $score) => ['agent' => $id, 'score' => $score, 'grade' => 'Reprovada', 'runs_count' => 1,
            'categories' => self::CATEGORIES, 'cost_usd' => 0.5, 'wall_minutes' => 10.0];
        $instance = ['entries' => [['agent' => 'agent-a', 'model' => ['name' => 'Model A', 'provider' => 'Maker', 'reasoning_effort' => 'high']]]];
        if ($withLeb100) {
            $instance = ['id' => 'LEB-100-A', 'entries' => [
                ['agent' => 'agent-a', 'score' => 809, 'model' => ['name' => 'Model A', 'provider' => 'Maker', 'reasoning_effort' => 'high']],
                ['agent' => 'agent-b', 'score' => 700, 'model' => ['name' => 'Model B', 'provider' => 'Maker', 'reasoning_effort' => 'high']],
            ]];
        }
        AiBenchmark::fake([
            'instances' => [$instance],
            'aggregate_instances' => $agents300 === [] ? [] : [['instance' => 'LEB-300-A', 'publication' => 'aggregate', 'edition' => '2026',
                'agents' => array_map(fn ($a) => $agent(...$a), $agents300)]],
        ]);
    }

    public function test_each_instance_says_how_many_agents_it_ranks_and_its_top_score_in_both_languages(): void
    {
        $this->fake([['agent-a', 649], ['agent-b', 242]], withLeb100: true);

        $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('<strong>LEB-100-A</strong><small>About 300 lines</small><small>2 agents · top 809</small>', false)
            ->assertSee('<strong>LEB-300-A</strong><small>About 3,000 lines</small><small>2 agents · top 649</small>', false);

        $this->get(self::PT)->assertOk()
            ->assertSee('<strong>LEB-100-A</strong><small>Cerca de 300 linhas</small><small>2 agentes · melhor 809</small>', false)
            ->assertSee('<strong>LEB-300-A</strong><small>Cerca de 3.000 linhas</small><small>2 agentes · melhor 649</small>', false);
    }

    public function test_one_agent_is_singular_and_an_instance_with_nothing_published_gets_no_line(): void
    {
        $this->fake([['agent-a', 227]]);

        $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('<strong>LEB-300-A</strong><small>About 3,000 lines</small><small>1 agent · top 227</small>', false)
            // The faked instance carries no id, so LEB-100 has nothing to say.
            ->assertSee('<strong>LEB-100-A</strong><small>About 300 lines</small></span>', false);

        $this->get(self::PT)->assertOk()
            ->assertSee('<strong>LEB-300-A</strong><small>Cerca de 3.000 linhas</small><small>1 agente · melhor 227</small>', false);

        $this->fake([]);
        $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('<strong>LEB-300-A</strong><small>About 3,000 lines</small></span>', false)
            ->assertDontSee('agent · top')
            ->assertDontSee('agents · top');
    }

    /** The real synced file: the line under each instance matches the count and the leader the pages show. */
    public function test_the_real_synced_file_feeds_the_menu_on_every_public_page(): void
    {
        $leb100 = collect(AiBenchmark::results()['instances'])->firstWhere('id', 'LEB-100-A');
        $aggregate = AiBenchmark::aggregate('LEB-300-A');
        if ($leb100 === null || $aggregate === null) {
            $this->markTestSkipped('the synced results file carries no LEB-100-A instance or no LEB-300-A aggregate');
        }

        $line100 = count($leb100['entries']).' agents · top '.$leb100['entries'][0]['score'];
        $line300 = count($aggregate['agents']).' agents · top '.$aggregate['agents'][0]['score'];

        // Pages that need no database row: the suite runs without one (the home and the downloads page do not).
        foreach (['/projects/github-desktop', '/ai-benchmark', '/ai-benchmark/leb-100', self::EN] as $page) {
            $this->get($page, self::EN_HEADER)->assertOk()
                ->assertSee('<small>'.$line100.'</small>', false)
                ->assertSee('<small>'.$line300.'</small>', false);
        }
    }
}
