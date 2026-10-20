<?php

namespace Tests\Feature;

use App\Support\AiBenchmark;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * LEB Default and LEB Security: the two tracks of the next LEB series.
 *
 * The series is a proposal, so each track has a status page (App\Http\Controllers\LebTrackController, no
 * database), written from lang/*\/leb_series.php, leb_default.php and leb_security.php. What these tests pin is what was decided:
 * one page per track; nothing beyond what the public repository says (no number the series has not published, no date it has
 * not set); the answer key described as public on GitHub with the instance and never as private, secret or hidden; and no
 * planted vulnerability named on either page, not even by class.
 */
class LebTrackPagesTest extends TestCase
{
    private const EN_HEADER = ['Accept-Language' => 'en-US,en;q=0.9'];

    private const REPO = 'https://github.com/samirhvbr/ai-benchmark';

    /** The status pages are tested with no results at all: the synced file carries the Default track's aggregate now. */
    protected function setUp(): void
    {
        parent::setUp();
        AiBenchmark::fake(['instances' => []]);
    }

    protected function tearDown(): void
    {
        AiBenchmark::flush();
        parent::tearDown();
    }

    /** A second configuration whose case is still being run: three runs, its own package. */
    private function interimCase(): array
    {
        return [
            'agent' => 'claude-sonnet-5.5-xhigh', 'score' => 785, 'grade' => 'Gold', 'runs_count' => 3, 'package_sha256' => str_repeat('d', 64),
            'categories' => ['ARCH' => 182, 'BUG' => 197, 'PERF' => 175, 'CLN' => 89, 'COMP' => 100, 'EXPL' => 44],
            'category_max' => ['ARCH' => 283, 'BUG' => 213, 'PERF' => 213, 'CLN' => 141, 'COMP' => 100, 'EXPL' => 50],
            'cost_usd' => 6.05, 'wall_minutes' => 35.5,
            'model' => ['name' => 'Claude Sonnet 5.5', 'id' => 'claude-sonnet-5-5', 'provider' => 'Anthropic', 'reasoning_effort' => 'xhigh'],
            'comment' => ['en' => 'Three runs of a case of five.', 'pt_BR' => 'Três execuções de um caso de cinco.'],
            'statistics' => [
                'scheduled' => 3, 'quality' => ['n' => 3, 'systems' => 1, 'mean' => 785.3, 'low' => 750.0, 'high' => 835.0],
                'expected_per_scheduled_attempt' => ['n' => 3, 'systems' => 1, 'mean' => 785.3, 'low' => 750.0, 'high' => 835.0],
                'completion' => ['rate' => 1.0, 'completed' => 3, 'scheduled' => 3, 'systems_all_completed' => 1, 'systems' => 1],
                'outcomes' => ['completed' => 3, 'blocked' => 0, 'blocked_at_the_start' => 0, 'blocked_during_the_work' => 0, 'budget_exhausted' => 0, 'switched' => 0],
                'lab_failures_rerun' => 1, 'median' => 771, 'min' => 750, 'max' => 835,
            ],
            'runs' => array_map(fn ($t, $i) => ['run' => $i + 2, 'total' => $t], [750, 771, 835], range(0, 2)),
        ];
    }

    /** A configuration with a single run and a client mode: the cards must read in the singular, and the mode is the amber tag next to the effort. */
    private function singleRunCase(): array
    {
        return [
            'agent' => 'kimi-k3-max', 'score' => 512, 'grade' => 'Bronze', 'runs_count' => 1, 'package_sha256' => str_repeat('e', 64),
            'categories' => ['ARCH' => 101, 'BUG' => 166, 'PERF' => 104, 'CLN' => 0, 'COMP' => 100, 'EXPL' => 42],
            'category_max' => ['ARCH' => 283, 'BUG' => 213, 'PERF' => 213, 'CLN' => 141, 'COMP' => 100, 'EXPL' => 50],
            'cost_usd' => 4.09, 'wall_minutes' => 52.0,
            'model' => ['name' => 'Kimi K3', 'id' => 'kimi-k3', 'provider' => 'Moonshot AI', 'reasoning_effort' => 'max', 'client_mode' => 'permissions auto-approved'],
            'comment' => ['en' => 'One run of a case of five scored 512.', 'pt_BR' => 'Uma execução de um caso de cinco fez 512.'],
            'statistics' => [
                'scheduled' => 1, 'quality' => ['n' => 1, 'systems' => 1, 'mean' => 512.0, 'low' => 512.0, 'high' => 512.0],
                'expected_per_scheduled_attempt' => ['n' => 1, 'systems' => 1, 'mean' => 512.0, 'low' => 512.0, 'high' => 512.0],
                'completion' => ['rate' => 1.0, 'completed' => 1, 'scheduled' => 1, 'systems_all_completed' => 1, 'systems' => 1],
                'outcomes' => ['completed' => 1, 'blocked' => 0, 'blocked_at_the_start' => 0, 'blocked_during_the_work' => 0, 'budget_exhausted' => 0, 'switched' => 0],
                'lab_failures_rerun' => 0, 'median' => 512, 'min' => 512, 'max' => 512,
            ],
            'runs' => [['run' => 1, 'total' => 512]],
        ];
    }

    /** The aggregate of the Haiku case, in the shape tools/export-results.py publishes it. */
    private function firstCase(): array
    {
        return [
            'instance' => 'LEB-D-300-A', 'publication' => 'aggregate', 'edition' => '2026', 'version' => '1.1', 'track' => 'default', 'mode' => 'A',
            'turn_budget' => null, 'budget_minutes' => 90, 'matrix_sha256' => str_repeat('a', 64),
            'notes' => ['en' => 'The runs received the earlier package.', 'pt_BR' => 'As execuções receberam o pacote anterior.'],
            'agents' => [$this->interimCase(), [
                'agent' => 'claude-haiku-5.5-high', 'package_sha256' => str_repeat('c', 64), 'score' => 547, 'grade' => 'Bronze', 'runs_count' => 5,
                'categories' => ['ARCH' => 96, 'BUG' => 173, 'PERF' => 125, 'CLN' => 10, 'COMP' => 100, 'EXPL' => 42],
                'category_max' => ['ARCH' => 283, 'BUG' => 213, 'PERF' => 213, 'CLN' => 141, 'COMP' => 100, 'EXPL' => 50],
                'cost_usd' => 1.13, 'wall_minutes' => 26.6,
                'model' => ['name' => 'Claude Haiku 5.5', 'id' => 'claude-haiku-5-5', 'provider' => 'Anthropic', 'reasoning_effort' => 'high'],
                'comment' => ['en' => 'Five runs between 524 and 580.', 'pt_BR' => 'Cinco execuções entre 524 e 580.'],
                'statistics' => [
                    'scheduled' => 5, 'quality' => ['n' => 5, 'systems' => 1, 'mean' => 546.8, 'low' => 530.0, 'high' => 565.0],
                    'expected_per_scheduled_attempt' => ['n' => 5, 'systems' => 1, 'mean' => 546.8, 'low' => 530.0, 'high' => 565.0],
                    'completion' => ['rate' => 1.0, 'completed' => 5, 'scheduled' => 5, 'systems_all_completed' => 1, 'systems' => 1],
                    'outcomes' => ['completed' => 5, 'blocked' => 0, 'blocked_at_the_start' => 0, 'blocked_during_the_work' => 0, 'budget_exhausted' => 0, 'switched' => 0],
                    'lab_failures_rerun' => 1, 'median' => 536, 'min' => 524, 'max' => 580,
                ],
                'runs' => array_map(fn ($t, $i) => ['run' => $i + 1, 'total' => $t], [524, 533, 580, 561, 536], range(0, 4)),
            ]],
        ];
    }

    private function visit(string $track, string $locale): TestResponse
    {
        $path = '/ai-benchmark/leb-'.$track;

        return $locale === 'pt_BR'
            ? $this->get('/pt-br'.$path)->assertOk()
            : $this->get($path, self::EN_HEADER)->assertOk();
    }

    /** What a reader reads in the body of the page: no markup, scripts or header. */
    private function visible(TestResponse $response): string
    {
        preg_match('#<main id="content".*?</main>#s', $response->getContent(), $main);

        return html_entity_decode(trim(preg_replace('/\s+/', ' ', strip_tags($main[0] ?? ''))), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** @return list<array{string, string}> */
    public static function everyPage(): array
    {
        return [['default', 'en'], ['default', 'pt_BR'], ['security', 'en'], ['security', 'pt_BR']];
    }

    // ── what each page says ──────────────────────────────────────────────────────────────────────

    public function test_the_default_page_says_what_the_track_measures_in_both_languages(): void
    {
        $this->visit('default', 'en')
            ->assertSee('lang="en"', false)
            ->assertSee('LEB Default')
            ->assertSee('There are no results yet.')
            ->assertSee('Nothing on it is a score.')
            ->assertSee('the everyday maintenance of a working system')
            ->assertSee('Architecture, bugs, performance, code quality and compatibility.')
            ->assertSee('No vulnerability is planted, and the task asks for no security work.')
            ->assertSee('"no known vulnerabilities within the reviewed scope"')
            ->assertSee('LEB-D-300-A')
            ->assertDontSee('LEB-S-300-A');

        $this->visit('default', 'pt_BR')
            ->assertSee('lang="pt-BR"', false)
            ->assertSee('Ainda não há resultados.')
            ->assertSee('Nada nela é uma nota.')
            ->assertSee('a manutenção do dia a dia de um sistema que funciona')
            ->assertSee('Nenhuma vulnerabilidade é plantada, e a tarefa não pede trabalho de segurança.')
            ->assertSee('LEB-D-300-A')
            ->assertDontSee('LEB-S-300-A')
            ->assertDontSee('There are no results yet.')
            ->assertDontSee('What this track measures');
    }

    public function test_the_security_page_says_what_the_track_measures_in_both_languages(): void
    {
        $this->visit('security', 'en')
            ->assertSee('LEB Security')
            ->assertSee('There are no results yet.')
            ->assertSee('finding and fixing vulnerabilities in a system that works, without breaking it')
            ->assertSee('A fix counts only when all of these hold:')
            ->assertSee('the reference attack and its variations no longer work;')
            ->assertSee('the legitimate user keeps the function;')
            ->assertSee('the contracts of the manifest still hold.')
            ->assertSee('is not a fix.')
            ->assertSee('a fix that closes the hole and breaks the system is a bad delivery')
            ->assertSee('LEB-S-300-A')
            ->assertDontSee('LEB-D-300-A');

        $this->visit('security', 'pt_BR')
            ->assertSee('Ainda não há resultados.')
            ->assertSee('achar e corrigir vulnerabilidades num sistema que funciona, sem quebrá-lo')
            ->assertSee('Uma correção só conta quando todas estas condições valem:')
            ->assertSee('os contratos do manifesto continuam valendo.')
            ->assertSee('uma correção que fecha o buraco e quebra o sistema é uma entrega ruim')
            ->assertSee('LEB-S-300-A')
            ->assertDontSee('LEB-D-300-A')
            ->assertDontSee('A fix counts only');
    }

    /** The two scores are side by side and never added; a score of this series is never set beside the other levels'. */
    public function test_both_pages_keep_the_two_scores_apart_and_do_not_compare_with_the_other_levels(): void
    {
        foreach (['default', 'security'] as $track) {
            $this->visit($track, 'en')
                ->assertSee('shown side by side and never added')
                ->assertSee('A score of the next series is never set beside one of LEB-100 or LEB-300.');

            $this->visit($track, 'pt_BR')
                ->assertSee('aparecem lado a lado e nunca são somadas')
                ->assertSee('Uma nota da próxima série nunca é posta ao lado de uma do LEB-100 ou do LEB-300.');
        }
    }

    public function test_both_pages_describe_the_series_the_systems_the_run_and_what_will_be_published(): void
    {
        foreach (['default', 'security'] as $track) {
            $this->visit($track, 'en')
                ->assertSeeInOrder(['What this track measures', 'Where the series stands', 'The reference systems', 'How a case runs',
                    'What will be published', 'The answer key', 'Comparison', 'Read more'])
                ->assertSee('The next series is a proposal')
                ->assertSee('There is no scoreboard yet.')
                ->assertSee('The pilot will have 3 systems; the evaluation set, about 6.')
                ->assertSee('<th scope="col">System</th>', false)
                ->assertSee('Condominium management (CondoDesk)')
                ->assertSee('Java 21 · Maven · PostgreSQL')
                ->assertSee('In authoring')
                ->assertSee('5 runs per case, and all of them count.')
                ->assertSee('pinned in the client, in the main conversation and in every subagent')
                ->assertSee('An automated lab runs each case, from the restoration of the machine to the filing of the evidence.')
                ->assertSee('the quality of the evaluable deliveries, with its uncertainty interval;')
                ->assertSee('the completion rate with the requested model;')
                ->assertSee('the expected score per scheduled attempt;')
                ->assertSee('exhausted budgets and lab failures, each counted separately.');

            $this->visit($track, 'pt_BR')
                ->assertSeeInOrder(['O que esta trilha mede', 'Em que pé está a série', 'Os sistemas de referência', 'Como um caso roda',
                    'O que será publicado', 'O gabarito', 'Comparação', 'Para ler mais'])
                ->assertSee('A próxima série é uma proposta')
                ->assertSee('Ainda não há placar.')
                ->assertSee('O piloto terá 3 sistemas; o conjunto de avaliação, cerca de 6.')
                ->assertSee('<th scope="col">Sistema</th>', false)
                ->assertSee('Gestão de condomínio (CondoDesk)')
                ->assertSee('Em autoria')
                ->assertSee('5 execuções por caso, e todas contam.')
                ->assertSee('travado no cliente, na conversa principal e em cada subagente')
                ->assertSee('da restauração da máquina ao arquivamento da evidência')
                ->assertSee('a qualidade das entregas avaliáveis, com o intervalo de incerteza;')
                ->assertSee('a taxa de conclusão com o modelo pedido;')
                ->assertSee('a nota esperada por tentativa agendada;');
        }
    }

    // ── the answer key ───────────────────────────────────────────────────────────────────────────

    /**
     * The rule of every instance, this one and the future ones: the key is published on GitHub with the instance, before its
     * first run, and the bench machines cannot reach GitHub. No page calls a key private, secret or hidden, or says it will be
     * revealed later.
     */
    #[DataProvider('everyPage')]
    public function test_the_answer_key_is_public_on_github_and_never_private_secret_or_hidden(string $track, string $locale): void
    {
        $text = $this->visible($this->visit($track, $locale));

        if ($locale === 'pt_BR') {
            $this->assertStringContainsString('O gabarito de uma instância é publicado no GitHub junto com a instância, antes da primeira execução, e as máquinas que rodam os agentes não alcançam o GitHub.', $text);
            $this->assertStringContainsString('Cada execução registra também o corte de treino do modelo', $text);
            $this->assertDoesNotMatchRegularExpression('/privad|secret|ocult|revel|aposent|escond|sigilo/iu', $text);
        } else {
            $this->assertStringContainsString('The answer key of an instance is published on GitHub with the instance, before its first run, and the machines that run the agents cannot reach GitHub.', $text);
            $this->assertStringContainsString('Each run also records the model\'s training cutoff', $text);
            $this->assertDoesNotMatchRegularExpression('/private|secret|hidden|reveal|retire|conceal|undisclosed/iu', $text);
        }
    }

    /**
     * Neither page describes a planted vulnerability, not even by class: the Security page is about what it measures and how a fix
     * is judged, and the Default page has none to describe.
     */
    #[DataProvider('everyPage')]
    public function test_no_page_names_a_planted_vulnerability_or_a_class_of_them(string $track, string $locale): void
    {
        $text = $this->visible($this->visit($track, $locale));

        $this->assertDoesNotMatchRegularExpression(
            '/\b(?:SEC|ARCH|BUG|PERF|CLN)-\d{3}\b|\bSQL\b|injection|inje[cç][aã]o|\bXSS\b|cross-site|\bCSRF\b|traversal|\bSSRF\b|\bXXE\b|open redirect|session fixation|fixa[cç][aã]o de sess|\bIDOR\b|path\b.*\btravers|deserializ|hardcoded|sensitive information|weak password|senha fraca|rate limit|missing authorization/iu',
            $text,
        );
    }

    /** Nothing the series has not published: the only numbers on the pages are the ones the repository gives. */
    public function test_the_only_numbers_on_the_pages_are_the_ones_the_repository_gives(): void
    {
        // 12 October 2026 (the design), LEB-100 and LEB-300, Java 21, 3 pilot systems, about 6, 5 runs, system A's level 300.
        $allowed = ['5', '6', '12', '21', '3', '100', '300', '2026'];

        foreach (self::everyPage() as [$track, $locale]) {
            preg_match_all('/\d+/', $this->visible($this->visit($track, $locale)), $numbers);

            $this->assertSame([], array_values(array_diff(array_unique($numbers[0]), $allowed)), "$track/$locale carries a number the series has not published");
        }
    }

    public function test_the_pages_have_no_scoreboard_and_no_translation_key_renders_raw(): void
    {
        foreach (self::everyPage() as [$track, $locale]) {
            $this->visit($track, $locale)
                ->assertDontSee('ab-board', false)
                ->assertDontSee('ab-top__list', false)
                ->assertDontSee('data-ab-filter', false)
                ->assertDontSee('leb_series.', false)
                ->assertDontSee('leb_default.', false)
                ->assertDontSee('leb_security.', false)
                ->assertDontSee('shell.', false);
        }
    }

    // ── the first case, once the synced results carry the Default track's aggregate ──────────────

    public function test_the_default_page_shows_the_cases_so_far_in_both_languages(): void
    {
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [$this->firstCase()]]);

        $this->visit('default', 'en')
            ->assertSee('The first results are in.')
            ->assertDontSee('There are no results yet.')
            ->assertSeeInOrder(['The cases so far', 'Claude Sonnet 5.5 · xhigh', '785', '750 – 835', 'Gold', '182/283', '750 · 771 · 835',
                'Claude Haiku 5.5 · high', '547', '530 – 565', 'Bronze', '96/283', '173/213', '524 · 533 · 580 · 561 · 536'])
            ->assertSee('Each configuration is run on system A, 90 minutes at most per run.')
            ->assertSee('5 of 5 scheduled attempts completed with the requested model.')
            ->assertSee('3 of 3 scheduled attempts completed with the requested model.')
            ->assertSee('Systems on which every attempt completed: 1 of 1.')
            ->assertSee('Expected score per scheduled attempt: 546.8 (530.0 to 565.0).')
            ->assertSee('Lost to the lab or the operator, rerun: 1.')
            ->assertSee('Per run, on average: US$ 1.13 and 27 minutes.')
            ->assertSee('Per run, on average: US$ 6.05 and 36 minutes.')
            ->assertSee('with one system it reflects only the variation among that system')
            ->assertSee('Five runs between 524 and 580.')
            ->assertSee('Three runs of a case of five.')
            ->assertSee('The runs received the earlier package.')
            ->assertSee(str_repeat('c', 64))
            ->assertSee(str_repeat('d', 64))
            ->assertSee('These configurations have run on one system.')
            ->assertSee('The pilot has begun on system A')
            ->assertSee('There is no scoreboard yet')
            ->assertSee('Pilot cases under way')
            ->assertDontSee('In authoring')
            ->assertDontSee('<ol class="ab-board">', false);   // the cards are filterable and orderable (div.ab-board--cases), never a ranked leaderboard

        $this->visit('default', 'pt_BR')
            ->assertSee('Os primeiros resultados saíram.')
            ->assertDontSee('Ainda não há resultados.')
            ->assertSeeInOrder(['Os casos até agora', 'Claude Sonnet 5.5 · xhigh', '785', '750 – 835', 'Gold', 'Claude Haiku 5.5 · high', '547', '530 – 565', 'Bronze', '96/283'])
            ->assertSee('5 de 5 tentativas agendadas terminaram com o modelo pedido.')
            ->assertSee('Nota esperada por tentativa agendada: 546.8 (530.0 a 565.0).')
            ->assertSee('Perdidas para o laboratório ou o operador, refeitas: 1.')
            ->assertSee('Por execução, em média: US$ 1.13 e 27 minutos.')
            ->assertSee('Cinco execuções entre 524 e 580.')
            ->assertSee('As execuções receberam o pacote anterior.')
            ->assertSee('Casos do piloto em andamento');
    }

    public function test_a_configuration_of_one_run_reads_in_the_singular_and_shows_its_client_mode(): void
    {
        $aggregate = $this->firstCase();
        $aggregate['agents'][] = $this->singleRunCase();
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [$aggregate]]);

        $en = $this->visible($this->visit('default', 'en'));
        $this->assertStringContainsString('Default track · 1 run', $en);
        $this->assertStringNotContainsString('1 runs', $en);
        $this->assertStringContainsString('1 of 1 scheduled attempt completed with the requested model.', $en);
        $this->assertStringNotContainsString('1 of 1 scheduled attempts', $en);
        $this->assertStringContainsString('Default track · 5 runs', $en);
        $this->assertStringContainsString('3 of 3 scheduled attempts completed with the requested model.', $en);
        $this->assertStringContainsString('permissions auto-approved', $en);

        $pt = $this->visible($this->visit('default', 'pt_BR'));
        $this->assertStringContainsString('trilha Default · 1 execução', $pt);
        $this->assertStringNotContainsString('1 execuções', $pt);
        $this->assertStringContainsString('1 de 1 tentativa agendada terminou com o modelo pedido.', $pt);
        $this->assertStringNotContainsString('1 de 1 tentativas', $pt);
        $this->assertStringContainsString('trilha Default · 5 execuções', $pt);
        $this->assertStringContainsString('3 de 3 tentativas agendadas terminaram com o modelo pedido.', $pt);
        $this->assertStringContainsString('permissions auto-approved', $pt);
    }

    public function test_the_default_page_filters_its_cases_by_vendor_and_orders_them_by_category_in_both_languages(): void
    {
        $aggregate = $this->firstCase();
        $aggregate['agents'][] = $this->singleRunCase();
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [$aggregate]]);
        $agents = $aggregate['agents'];
        $vendors = collect($agents)->pluck('model.provider')->unique()->values();

        foreach ([['en', 'Filter by vendor', 'Order by', 'Showing :shown of :total configurations.', 'Showing 3 of 3 configurations.'],
                  ['pt_BR', 'Filtrar por fornecedor', 'Ordenar por', 'Mostrando :shown de :total configurações.', 'Mostrando 3 de 3 configurações.']] as [$locale, $filter, $sort, $tpl, $status]) {
            $html = $this->visit('default', $locale)->getContent();
            $this->assertStringContainsString('<div class="ab-filter" data-ab-filter data-ab-page="0" hidden>', $html);
            $this->assertStringContainsString($filter, $html);
            $this->assertStringContainsString('<span class="ab-filter__tpl" hidden>'.$tpl, $html);
            $this->assertStringContainsString($status, $html);
            foreach ($vendors as $vendor) {
                $n = collect($agents)->where('model.provider', $vendor)->count();
                $this->assertStringContainsString('data-vendor="'.$vendor.'" aria-pressed="false">'.$vendor.' <span class="ab-chip__n">'.$n.'</span>', $html);
            }
            foreach (['ARCH', 'BUG', 'PERF', 'CLN', 'COMP', 'EXPL'] as $cat) {
                $this->assertStringContainsString('data-sort="'.$cat.'" aria-pressed="false">', $html);
                $this->assertStringContainsString('<td data-cat="'.$cat.'">', $html);
            }
            $this->assertStringNotContainsString('data-sort="SEC"', $html);
            // one filterable card per configuration, inside the board the script reads, each with the scores it orders by
            $this->assertStringContainsString('<div class="ab-board ab-board--cases">', $html);
            $this->assertSame(count($agents), preg_match_all('/<article class="ab-case"[^>]* data-vendor="[^"]*" data-scores="ARCH:[\d.]+ BUG:[\d.]+ PERF:[\d.]+ CLN:[\d.]+ COMP:[\d.]+ EXPL:[\d.]+"/', $html));
            $this->assertStringContainsString('data-scores="ARCH:96 BUG:173 PERF:125 CLN:10 COMP:100 EXPL:42"', $html);
        }
    }

    public function test_the_security_page_stays_a_status_page_when_only_the_default_track_has_results(): void
    {
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [$this->firstCase()]]);

        $this->visit('security', 'en')
            ->assertSee('There are no results yet.')
            ->assertSee('In authoring')
            ->assertDontSee('The cases so far')
            ->assertDontSee('Claude Haiku 5.5');
        $this->visit('security', 'pt_BR')
            ->assertSee('Ainda não há resultados.')
            ->assertDontSee('Os casos até agora');
    }

    public function test_an_aggregate_of_another_track_does_not_make_the_default_page_show_results(): void
    {
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [['track' => 'security'] + $this->firstCase()]]);

        $this->visit('default', 'en')->assertSee('There are no results yet.')->assertDontSee('The cases so far');
    }

    public function test_the_first_case_names_no_flaw_and_sets_no_score_beside_the_other_levels(): void
    {
        AiBenchmark::fake(['instances' => [], 'aggregate_instances' => [$this->firstCase()]]);

        foreach (['en', 'pt_BR'] as $locale) {
            $text = $this->visible($this->visit('default', $locale));
            $this->assertDoesNotMatchRegularExpression('/\b(?:SEC|ARCH|BUG|PERF|CLN)-\d{3}\b/', $text);
            $this->assertMatchesRegularExpression('/A score of the next series is never set beside|Uma nota da próxima série nunca é posta ao lado/', $text);
        }
    }

    public function test_the_synced_results_file_still_renders_the_default_page(): void
    {
        AiBenchmark::flush();   // the file the site ships, not the fake: whatever it carries must render without a raw key

        foreach (['en', 'pt_BR'] as $locale) {
            $this->visit('default', $locale)->assertDontSee('leb_series.', false)->assertDontSee('leb_default.', false);
        }
    }

    // ── links, language pairs and the copy itself ────────────────────────────────────────────────

    public function test_each_page_links_the_other_track_the_benchmark_page_and_its_sources_in_its_language(): void
    {
        $this->visit('default', 'en')
            ->assertSee('href="'.url('/ai-benchmark/leb-security').'"', false)
            ->assertSee('href="'.url('/ai-benchmark').'"', false)
            ->assertSee('href="'.self::REPO.'/blob/master/docs/new-series-design.md"', false)
            ->assertSee('href="'.self::REPO.'/tree/master/systems/300-A"', false)
            ->assertSee('href="'.self::REPO.'/tree/master/instances/LEB-D-300-A"', false);

        $this->visit('security', 'pt_BR')
            ->assertSee('href="'.url('/pt-br/ai-benchmark/leb-default').'"', false)
            ->assertSee('href="'.url('/pt-br/ai-benchmark').'"', false)
            ->assertSee('href="'.self::REPO.'/blob/master/docs/new-series-design.md"', false)
            ->assertSee('href="'.self::REPO.'/tree/master/systems/300-A"', false)
            ->assertSee('href="'.self::REPO.'/tree/master/instances/LEB-S-300-A"', false);
    }

    public function test_the_two_languages_of_each_page_declare_the_same_reciprocal_hreflang_pair(): void
    {
        foreach (['default', 'security'] as $track) {
            $en = url('/ai-benchmark/leb-'.$track);
            $pt = url('/pt-br/ai-benchmark/leb-'.$track);

            foreach (['en', 'pt_BR'] as $locale) {
                $this->visit($track, $locale)
                    ->assertSee('hreflang="en" href="'.$en.'"', false)
                    ->assertSee('hreflang="pt-BR" href="'.$pt.'"', false);
            }
        }
    }

    public function test_both_languages_define_the_same_keys_for_the_three_files(): void
    {
        foreach (['leb_series', 'leb_default', 'leb_security'] as $file) {
            $en = require lang_path("en/$file.php");
            $pt = require lang_path("pt_BR/$file.php");

            $this->assertSame($this->keys($en), $this->keys($pt), "$file differs between languages");
            // The two lists of a language have the same length too: a bullet missing in one language is a missing fact.
            foreach ($en as $key => $value) {
                if (is_array($value)) {
                    $this->assertCount(count($value), $pt[$key], "$file.$key has a different number of items");
                }
            }
        }
    }

    /** @return list<string> dotted key paths, sorted */
    private function keys(array $tree, string $prefix = ''): array
    {
        $out = [];
        foreach ($tree as $k => $v) {
            if (is_array($v) && ! array_is_list($v)) {
                $out = [...$out, ...$this->keys($v, "$prefix$k.")];
            } else {
                $out[] = "$prefix$k";
            }
        }
        sort($out);

        return $out;
    }
}
