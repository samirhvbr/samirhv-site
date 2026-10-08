<?php

namespace Tests\Feature;

use App\Support\AiBenchmark;
use Tests\TestCase;

/**
 * `/ai-benchmark`, the first entry of the AI Benchmark menu: what LEB is, how the two levels differ, and the way
 * into the page of each instance.
 *
 * It holds no leaderboard: the results are on the pages of the instances (Leb100PageTest, Leb300PageTest). The
 * few numbers it quotes (how many agents, the top score, the turns of a run) are read from the synced results
 * file, so they are exercised with `AiBenchmark::fake`, plus one test on the real copy. LEB-300-A is ACTIVE: the
 * page may say what the public repository already says of its level and must name nothing of the instance itself.
 */
class BenchmarkPageTest extends TestCase
{
    private const EN = '/ai-benchmark';

    private const PT = '/pt-br/ai-benchmark';

    private const EN_HEADER = ['Accept-Language' => 'en-US,en;q=0.9'];

    protected function tearDown(): void
    {
        AiBenchmark::flush();
        parent::tearDown();
    }

    /** @return array<string, mixed> */
    private function entry(int $rank, int $score, string $name, string $effort = 'high'): array
    {
        return ['rank' => $rank, 'score' => $score, 'agent' => "agent-$rank",
            'model' => ['name' => $name, 'provider' => 'Maker', 'reasoning_effort' => $effort]];
    }

    private function withBothInstances(): void
    {
        AiBenchmark::fake([
            'instances' => [[
                'id' => 'LEB-100-A', 'mode' => 'A', 'turn_budget' => 30,
                'entries' => [$this->entry(1, 809, 'Model One'), $this->entry(2, 700, 'Model Two'), $this->entry(3, 650, 'Model Three')],
            ]],
            'aggregate_instances' => [[
                'instance' => 'LEB-300-A', 'publication' => 'aggregate', 'mode' => 'A', 'turn_budget' => 60,
                'agents' => [['agent' => 'agent-2', 'score' => 242, 'grade' => 'Reprovada', 'runs_count' => 3,
                    'categories' => ['SEC' => 56, 'ARCH' => 25, 'BUG' => 29, 'PERF' => 0, 'CLN' => 0, 'COMP' => 100, 'EXPL' => 32]]],
            ]],
        ]);
    }

    private function withoutPilot(): void
    {
        AiBenchmark::fake(['instances' => [[
            'id' => 'LEB-100-A', 'mode' => 'A', 'turn_budget' => 30,
            'entries' => [$this->entry(1, 809, 'Model One')],
        ]]]);
    }

    public function test_the_english_page_explains_the_project_and_the_two_levels(): void
    {
        $this->withBothInstances();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSeeInOrder([
                'Can an AI maintain legacy code',
                'Why another benchmark',
                'How a run works',
                'LEB-100 and LEB-300',
                'The instances',
                '1000 points, and how they are lost',
                'Runs happen where the answer key is out of reach',
                'Where the method lives',
            ]);
    }

    public function test_the_portuguese_page_speaks_portuguese_only(): void
    {
        $this->withBothInstances();

        $response = $this->get(self::PT)
            ->assertOk()
            ->assertSee('lang="pt-BR"', false)
            ->assertSee('Uma IA consegue manter código legado')
            ->assertSee('LEB-100 e LEB-300')
            ->assertSee('As instâncias');

        foreach (['Can an AI maintain', 'How a run works', 'Compare the levels', 'The instances', 'Open LEB-100-A', 'Where the method lives',
            'agents ranked', 'Reference instance', 'Exploratory pilot', 'The answer key', 'What is published'] as $english) {
            $response->assertDontSee($english);
        }
    }

    public function test_no_portuguese_leaks_into_the_english_page(): void
    {
        $this->withBothInstances();

        $response = $this->get(self::EN, self::EN_HEADER)->assertOk();

        foreach (['Uma IA consegue', 'Comparar os níveis', 'As instâncias', 'Abrir o LEB', 'Instância de referência', 'Piloto exploratório', 'O gabarito'] as $portuguese) {
            $response->assertDontSee($portuguese);
        }
    }

    /** A key with no string behind it renders as itself: `ai_benchmark.levels_row_size`. */
    public function test_no_translation_key_renders_raw_in_either_state_or_language(): void
    {
        foreach (['withBothInstances', 'withoutPilot'] as $state) {
            $this->$state();
            foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
                $this->get($url, $headers)->assertOk()->assertDontSee('ai_benchmark.', false)->assertDontSee('shell.', false);
            }
        }
    }

    public function test_the_comparison_sets_the_two_instances_side_by_side_with_the_turns_of_each_run(): void
    {
        $this->withBothInstances();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('<th scope="col">LEB-100-A</th>', false)
            ->assertSee('<th scope="col">LEB-300-A</th>', false)
            ->assertSeeInOrder(['Size', 'About 300 lines', 'About 3,000 lines', 'What it tests', 'Difficulty', 'The run',
                'Mode A, 30 turns.', 'Mode A, 60 turns, in two stages.', 'The answer key', 'What is published', 'Where it stands'])
            ->assertSee('never set beside a LEB-100 one');

        $this->get(self::PT)
            ->assertOk()
            ->assertSeeInOrder(['Tamanho', 'Cerca de 300 linhas', 'Cerca de 3.000 linhas', 'A execução', 'Modo A, 30 turnos.', 'Modo A, 60 turnos, em duas etapas.']);
    }

    public function test_the_cards_say_how_many_agents_are_ranked_and_who_leads_and_open_each_page(): void
    {
        $this->withBothInstances();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('3 agents ranked')
            ->assertSee('Top score 809 of 1000, Model One')
            ->assertSee('1 agent ranked')
            ->assertSee('Top score 242 of 1000, Model Two')
            ->assertSee('Exploratory pilot')
            ->assertSee('href="'.url('/ai-benchmark/leb-100').'"', false)
            ->assertSee('href="'.url('/ai-benchmark/leb-300').'"', false);

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('3 agentes no placar')
            ->assertSee('Maior nota 809 de 1000, Model One')
            ->assertSee('1 agente no placar')
            ->assertSee('href="'.url('/pt-br/ai-benchmark/leb-100').'"', false)
            ->assertSee('href="'.url('/pt-br/ai-benchmark/leb-300').'"', false);
    }

    public function test_without_a_pilot_result_the_second_card_says_it_is_in_preparation(): void
    {
        $this->withoutPilot();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('In preparation')
            ->assertSee('No results published yet.')
            ->assertSee('In preparation: no results yet.')
            ->assertDontSee('Exploratory pilot')
            ->assertDontSee('An exploratory pilot');

        $this->get(self::PT)->assertOk()->assertSee('Em preparação')->assertSee('Em preparação: ainda sem resultados.');
    }

    public function test_without_any_result_the_page_still_renders(): void
    {
        AiBenchmark::fake(['instances' => []]);

        $this->get(self::EN, self::EN_HEADER)->assertOk()->assertSee('LEB-100 and LEB-300')->assertSee('No results published yet.');
        $this->get(self::PT)->assertOk()->assertSee('Nenhum resultado publicado ainda.');
    }

    /** The page lists no leaderboard and no per-flaw result: those are on the pages of the instances. */
    public function test_it_carries_no_leaderboard_and_no_flaw_table(): void
    {
        $this->withBothInstances();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertDontSee('class="ab-board"', false)
            ->assertDontSee('ab-flaws', false)
            ->assertDontSee('Read this before quoting a number')
            ->assertDontSee('flaws.csv', false);
    }

    /**
     * LEB-300-A is active. What the page says of it is what the public repository says of its level (size, files, the
     * mode and turns of a run, that its key is private); no stack, no count of flaws or decoys, no name of a flaw.
     */
    public function test_it_names_nothing_of_the_active_instance_beyond_what_the_repository_says(): void
    {
        $this->withBothInstances();

        foreach ([[self::EN, self::EN_HEADER], [self::PT, []]] as [$url, $headers]) {
            $response = $this->get($url, $headers)->assertOk();
            foreach (['Java', 'Maven', 'Spring', ' 36 ', 'decoys of', 'iscas de', 'matrix.json'] as $secret) {
                $response->assertDontSee($secret);
            }
        }
    }

    public function test_both_pages_declare_the_same_reciprocal_hreflang_pair(): void
    {
        $this->withBothInstances();

        foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
            $this->get($url, $headers)
                ->assertOk()
                ->assertSee('hreflang="en" href="'.url(self::EN).'"', false)
                ->assertSee('hreflang="pt-BR" href="'.url(self::PT).'"', false);
        }
    }

    public function test_the_menu_entry_and_the_footer_open_this_page_in_the_current_language(): void
    {
        $this->get('/projects/github-desktop', self::EN_HEADER)->assertOk()->assertSee('href="'.url(self::EN).'"', false);
        $this->get('/pt-br/projects/github-desktop')->assertOk()->assertSee('href="'.url(self::PT).'"', false);
    }

    public function test_the_real_synced_copy_renders_with_its_own_numbers(): void
    {
        $leb100 = collect(AiBenchmark::results()['instances'])->firstWhere('id', 'LEB-100-A');
        $this->assertNotNull($leb100, 'resources/'.AiBenchmark::PATH.' has no LEB-100-A.');
        $top = $leb100['entries'][0];

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee(count($leb100['entries']).' agents ranked')
            ->assertSee("Top score {$top['score']} of 1000")
            ->assertSee("Mode {$leb100['mode']}, {$leb100['turn_budget']} turns.");
    }
}
