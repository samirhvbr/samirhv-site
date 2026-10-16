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

    /** Both instances, and the Default track's aggregate: two configurations, five and three runs completed. */
    private function withDefaultTrack(): void
    {
        $this->withBothInstances();
        $data = AiBenchmark::results();
        $agent = fn (string $id, int $score, int $completed) => ['agent' => $id, 'score' => $score, 'grade' => 'Silver', 'runs_count' => $completed,
            'statistics' => ['scheduled' => $completed, 'completion' => ['completed' => $completed, 'scheduled' => $completed]]];
        $data['aggregate_instances'][] = ['instance' => 'LEB-D-300-A', 'publication' => 'aggregate', 'track' => 'default',
            'agents' => [$agent('agent-a', 712, 5), $agent('agent-b', 655, 3)]];
        AiBenchmark::fake($data);
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

    /** The answer key of LEB-300-A has been on GitHub since 2026-10-12: the page says so, and never that it is private or hidden. */
    public function test_the_comparison_says_the_key_of_the_second_instance_is_public(): void
    {
        $this->withBothInstances();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('Public on GitHub since 12 October 2026')
            ->assertSee('its answer key is public too')
            ->assertDontSee('Private. The instance is active')
            ->assertDontSee('keeps its key private')
            ->assertDontSee('hidden answer key');

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('Público no GitHub desde 12 de outubro de 2026')
            ->assertSee('o gabarito dele também é público')
            ->assertDontSee('Privado. A instância está ativa')
            ->assertDontSee('mantém o gabarito privado')
            ->assertDontSee('gabarito oculto');
    }

    /** The section after the instances: a card per track of the next series, opening its status page, and no result on them. */
    public function test_the_next_series_has_a_card_per_track_that_opens_its_page_and_no_result(): void
    {
        $this->withBothInstances();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSeeInOrder(['The instances', 'The next series', 'LEB Default', 'Open LEB Default', 'LEB Security', 'Open LEB Security', '1000 points, and how they are lost'])
            ->assertSee('It is a proposal, and no results are published yet.')
            ->assertSee('A score of the next series is never set beside one of LEB-100 or LEB-300.')
            ->assertSee('href="'.url('/ai-benchmark/leb-default').'"', false)
            ->assertSee('href="'.url('/ai-benchmark/leb-security').'"', false);

        $this->get(self::PT)
            ->assertOk()
            ->assertSeeInOrder(['As instâncias', 'A próxima série', 'LEB Default', 'Abrir o LEB Default', 'LEB Security', 'Abrir o LEB Security'])
            ->assertSee('É uma proposta, e ainda não há resultados publicados.')
            ->assertSee('Uma nota da próxima série nunca é posta ao lado de uma do LEB-100 ou do LEB-300.')
            ->assertSee('href="'.url('/pt-br/ai-benchmark/leb-default').'"', false)
            ->assertSee('href="'.url('/pt-br/ai-benchmark/leb-security').'"', false);
    }

    /** Once the Default track's instance has an aggregate, its card counts the configurations and the runs, the intro says the
     *  pilot has begun, and no score of the series appears on this page: Security keeps the status copy. */
    public function test_with_the_default_track_aggregate_the_series_counts_its_cases_and_shows_no_score(): void
    {
        $this->withDefaultTrack();

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('It is a proposal in its pilot: the Default track has its first cases, published as aggregates, and Security has no results yet.')
            ->assertDontSee('It is a proposal, and no results are published yet.')
            // The card's tags come before its title: "Next series" (the h2 says "The next series") then the state, then the name.
            ->assertSeeInOrder(['Next series', 'Pilot under way', 'LEB Default', 'LEB-D-300-A · 2 configurations measured · 8 runs completed', 'Open LEB Default',
                'Next series', 'Proposal', 'LEB Security', 'No results published yet.', 'Open LEB Security'])
            ->assertSee('A score of the next series is never set beside one of LEB-100 or LEB-300.')
            ->assertDontSee('712 of 1000')
            ->assertDontSee('655 of 1000');

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('É uma proposta em piloto: a trilha Default já tem os primeiros casos, publicados como agregados, e a Security ainda não tem resultados.')
            ->assertSeeInOrder(['Próxima série', 'Piloto em andamento', 'LEB Default', 'LEB-D-300-A · 2 configurações medidas · 8 execuções terminadas', 'Abrir o LEB Default',
                'Próxima série', 'Proposta', 'LEB Security', 'Nenhum resultado publicado ainda.', 'Abrir o LEB Security'])
            ->assertDontSee('712 de 1000')
            ->assertDontSee('655 de 1000');
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
     * mode and turns of a run, that its key is public); no stack, no count of flaws or decoys, no name of a flaw.
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
