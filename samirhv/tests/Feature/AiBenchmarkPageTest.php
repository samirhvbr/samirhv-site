<?php

namespace Tests\Feature;

use App\Support\AiBenchmark;
use Tests\TestCase;

/**
 * `/ai-benchmark`: LEB explained, then the results synced from ai-benchmark.
 *
 * The numbers are not asserted here — they belong to results.json, which is a
 * copy (tools/sync-ai-benchmark-results.sh). What is asserted is that the page
 * can read that copy, renders every entry in it, speaks one language at a
 * time, and that every id the file carries has a name in both languages.
 */
class AiBenchmarkPageTest extends TestCase
{
    private const EN = '/ai-benchmark';

    private const PT = '/pt-br/ai-benchmark';

    private const EN_HEADER = ['Accept-Language' => 'en-US,en;q=0.9'];

    protected function setUp(): void
    {
        parent::setUp();
        AiBenchmark::flush();
    }

    public function test_the_results_file_is_readable_and_has_entries(): void
    {
        $instances = AiBenchmark::results()['instances'];

        $this->assertNotEmpty($instances, 'resources/'.AiBenchmark::PATH.' is missing, invalid, or has no instance.');

        foreach ($instances as $inst) {
            $this->assertNotEmpty($inst['entries'], "{$inst['id']} has no entry.");
            foreach ($inst['entries'] as $e) {
                $this->assertIsInt($e['score']);
                $this->assertIsInt($e['rank']);
                $this->assertNotEmpty($e['runs']);
                $this->assertSame(
                    ['SEC', 'ARCH', 'BUG', 'PERF', 'CLN', 'COMP', 'EXPL'],
                    array_keys($e['runs'][0]['categories']),
                );
            }
        }
    }

    public function test_the_english_page_explains_and_lists_every_entry(): void
    {
        $response = $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Can an AI maintain legacy code')
            ->assertSee('How a run works')
            ->assertSee('Read this before quoting a number');

        foreach (AiBenchmark::results()['instances'] as $inst) {
            foreach ($inst['entries'] as $e) {
                $response->assertSee($e['model']['name'])->assertSee((string) $e['score']);
            }
        }
    }

    public function test_the_portuguese_page_speaks_portuguese_only(): void
    {
        $response = $this->get(self::PT)
            ->assertOk()
            ->assertSee('lang="pt-BR"', false)
            ->assertSee('Uma IA consegue manter código legado')
            ->assertSee('Leia isto antes de citar um número');

        foreach (['Can an AI maintain', 'How a run works', 'Read this before quoting', 'See the results', 'Flaw by flaw',
            'effort xhigh', 'effort default', 'default effort', 'not configurable', 'matrix 68088', 'the same names', 'not official',
            'training cutoff', 'runs per agent', 'prefixes are blackhole', 'benchmark VM', 'so far', 'Full leaderboard'] as $english) {
            $response->assertDontSee($english);
        }
    }

    public function test_no_portuguese_leaks_into_the_english_page(): void
    {
        $response = $this->get(self::EN, self::EN_HEADER)->assertOk();

        foreach (['Uma IA consegue', 'Como funciona um run', 'Falha por falha', 'Ver os resultados', 'não oficial'] as $portuguese) {
            $response->assertDontSee($portuguese);
        }
    }

    public function test_neither_page_names_the_instance_s_fictional_company(): void
    {
        // LEB-100-A's legacy system is set at a made-up ISP whose name belongs to a real company;
        // the repository keeps it, the page describes the instance without it.
        foreach ([[self::EN, self::EN_HEADER], [self::PT, []]] as [$url, $headers]) {
            $this->get($url, $headers)->assertOk()->assertDontSee('NetX')->assertDontSee('N3tX');
        }
    }

    public function test_the_flaw_table_shows_the_top_ten_and_the_leaderboard_shows_everyone(): void
    {
        $data = AiBenchmark::results();
        $inst = &$data['instances'][0];
        $extra = end($inst['entries']);
        $extra['model']['name'] = 'Eleventh Agent';
        $extra['rank'] = count($inst['entries']) + 1;
        $inst['entries'] = array_slice($inst['entries'], 0, AiBenchmark::FLAW_TABLE_AGENTS);
        $inst['entries'][] = $extra;
        unset($inst);
        AiBenchmark::fake($data);

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('Eleventh Agent</strong>', false)
            ->assertDontSee('<th scope="col">Eleventh Agent</th>', false)
            ->assertSee('The table shows the top 10 of 11 agents.');

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('A tabela mostra os 10 primeiros de 11 agentes.');
    }

    public function test_the_top_ten_note_stays_away_while_every_agent_fits(): void
    {
        $data = AiBenchmark::results();
        $data['instances'][0]['entries'] = array_slice($data['instances'][0]['entries'], 0, AiBenchmark::FLAW_TABLE_AGENTS);
        AiBenchmark::fake($data);

        $this->get(self::EN, self::EN_HEADER)->assertOk()->assertDontSee('The table shows the top');
    }

    public function test_a_second_run_shows_its_totals_and_the_score_run_supplies_the_details(): void
    {
        $data = AiBenchmark::results();
        $e = &$data['instances'][0]['entries'][0];
        $second = $e['runs'][0];
        $second['run'] = 2;
        $second['total'] = $e['score'] + 59;
        $second['grade'] = 'Platinum';
        $e['runs'][] = $second;
        $e['runs_count'] = 2;
        $e['totals'] = [$e['score'], $second['total']];
        $e['representative_run'] = 1;
        unset($e);
        AiBenchmark::fake($data);

        $first = $data['instances'][0]['entries'][0];
        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('2 of 3 runs ('.$first['totals'][0].' · '.$first['totals'][1].')')
            ->assertDontSee('ab-grade--platinum ab-grade--pill', false)
            ->assertSee('Up to three runs per agent.')
            ->assertDontSee('One run per agent.');
    }

    public function test_one_run_each_keeps_the_single_run_caveat(): void
    {
        $data = AiBenchmark::results();
        foreach ($data['instances'] as &$inst) {
            foreach ($inst['entries'] as &$e) {
                $e['runs'] = [$e['runs'][0]];
                $e['runs_count'] = 1;
                $e['totals'] = [$e['runs'][0]['total']];
                $e['representative_run'] = $e['runs'][0]['run'];
            }
        }
        unset($inst, $e);
        AiBenchmark::fake($data);

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('One run per agent.')
            ->assertDontSee('Up to three runs per agent.');
    }

    public function test_the_representative_run_falls_back_to_the_best_one_in_an_older_file(): void
    {
        $entry = ['runs' => [['run' => 1, 'total' => 700], ['run' => 2, 'total' => 760]]];
        $this->assertSame(2, AiBenchmark::representativeRun($entry)['run']);

        $entry['representative_run'] = 1;
        $this->assertSame(1, AiBenchmark::representativeRun($entry)['run']);
    }

    public function test_a_model_that_may_have_trained_on_the_key_is_marked(): void
    {
        $data = AiBenchmark::results();
        $inst = &$data['instances'][0];
        $inst['key_published_on'] = '2026-07-13';
        foreach ($inst['entries'] as $i => &$e) {
            $e['key_exposure'] = ['before', 'after', 'unknown'][min($i, 2)];
        }
        unset($inst, $e);
        AiBenchmark::fake($data);

        $this->get(self::EN, self::EN_HEADER)
            ->assertOk()
            ->assertSee('training cutoff after the answer key went public')
            ->assertSee('no published cutoff or earlier release')
            ->assertSee('The answer key has been public since 2026-07-13.', false);

        $this->get(self::PT)
            ->assertOk()
            ->assertSee('corte de treino posterior à publicação do gabarito')
            ->assertSee('sem corte ou lançamento anterior publicado');
    }

    public function test_the_hero_summarises_the_top_six_in_rank_order(): void
    {
        $entries = AiBenchmark::results()['instances'][0]['entries'];
        $response = $this->get(self::EN, self::EN_HEADER)->assertOk()->assertSee('Top 6 so far');

        $names = array_map(fn ($e) => AiBenchmark::displayName($e, $entries), array_slice($entries, 0, AiBenchmark::HERO_TOP));
        $response->assertSeeInOrder(['Top 6 so far', ...$names, 'Full leaderboard'], false);

        $this->get(self::PT)->assertOk()->assertSee('Os 6 melhores até aqui')->assertSee('Placar completo');
    }

    /** A key with no string behind it renders as itself: `ai_benchmark.instances.LEB-100-A.name`. */
    public function test_no_translation_key_renders_raw_on_either_page(): void
    {
        foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
            $this->get($url, $headers)->assertOk()->assertDontSee('ai_benchmark.', false);
        }
    }

    public function test_the_main_menu_links_to_the_page_in_the_current_language(): void
    {
        $this->get('/projects/github-desktop', self::EN_HEADER)
            ->assertOk()
            ->assertSee('href="'.url(self::EN).'"', false);

        $this->get('/pt-br/projects/github-desktop')
            ->assertOk()
            ->assertSee('href="'.url(self::PT).'"', false);
    }

    public function test_both_pages_declare_the_same_reciprocal_hreflang_pair(): void
    {
        foreach ([self::EN => self::EN_HEADER, self::PT => []] as $url => $headers) {
            $this->get($url, $headers)
                ->assertOk()
                ->assertSee('hreflang="en" href="'.url(self::EN).'"', false)
                ->assertSee('hreflang="pt-BR" href="'.url(self::PT).'"', false);
        }
    }

    /** An id the file carries and lang/ does not would render as a raw key. */
    public function test_every_instance_and_flaw_in_the_file_has_a_name_in_both_languages(): void
    {
        foreach (['en', 'pt_BR'] as $locale) {
            $lang = require lang_path("$locale/ai_benchmark.php");
            foreach (AiBenchmark::results()['instances'] as $inst) {
                $this->assertArrayHasKey($inst['id'], $lang['instances'], "$locale: no name for {$inst['id']}");
                $this->assertIsString($lang['instances'][$inst['id']]['name'] ?? null, "$locale: no name for {$inst['id']}");
                $this->assertIsString($lang['instances'][$inst['id']]['desc'] ?? null, "$locale: no description for {$inst['id']}");
                $this->assertNotEmpty($lang['highlights'][$inst['id']] ?? [], "$locale: no highlights for {$inst['id']}");
                foreach ($inst['flaws'] as $flaw) {
                    $this->assertArrayHasKey($flaw['id'], $lang['flaws'], "$locale: no name for {$flaw['id']}");
                    $this->assertArrayHasKey($flaw['severity'], $lang['severity'], "$locale: no label for {$flaw['severity']}");
                    $this->assertArrayHasKey($flaw['difficulty'], $lang['difficulty'], "$locale: no label for {$flaw['difficulty']}");
                }
                foreach ($inst['entries'] as $e) {
                    $this->assertArrayHasKey($e['runs'][0]['grade'], $lang['grades'], "$locale: no label for grade {$e['runs'][0]['grade']}");
                }
            }
        }
    }

    public function test_the_portuguese_strings_cover_every_english_key(): void
    {
        $en = require lang_path('en/ai_benchmark.php');
        $pt = require lang_path('pt_BR/ai_benchmark.php');

        $this->assertSame($this->keys($en), $this->keys($pt));
    }

    /** The hero and the flaw table show the name alone, so a model run twice needs its effort. */
    public function test_the_effort_is_appended_only_to_a_model_that_appears_twice(): void
    {
        $entry = fn (string $name, string $effort, ?string $mode = null) => ['model' => ['name' => $name, 'reasoning_effort' => $effort, 'client_mode' => $mode]];
        $entries = [$entry('Claude Sonnet 5.5', 'xhigh'), $entry('Claude Fable 5.1', 'xhigh'), $entry('Claude Sonnet 5.5', 'max', 'ultracode')];

        $this->assertSame('Claude Sonnet 5.5 · xhigh', AiBenchmark::displayName($entries[0], $entries));
        $this->assertSame('Claude Sonnet 5.5 · max (ultracode)', AiBenchmark::displayName($entries[2], $entries));
        $this->assertSame('Claude Fable 5.1', AiBenchmark::displayName($entries[1], $entries));
    }

    /** A client mode that changes how the model works is named next to its effort, in both languages. */
    /**
     * The vendor filter only hides rows: one chip per vendor in the file, with its count, each row
     * tagged with its vendor, and the ranks left as the full leaderboard's. The bar starts hidden,
     * so without the script the page is the plain leaderboard.
     */
    public function test_the_vendor_filter_offers_every_vendor_and_tags_every_row(): void
    {
        $entries = AiBenchmark::results()['instances'][0]['entries'];
        $vendors = array_count_values(array_map(fn ($e) => $e['model']['provider'], $entries));

        $response = $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('<div class="ab-filter" data-ab-filter hidden>', false)
            ->assertSee('Filter by vendor')
            ->assertSee('Showing '.count($entries).' of '.count($entries).' agents.')
            ->assertSee('<span class="ab-filter__tpl" hidden>Showing :shown of :total agents.', false)
            ->assertSee('js/site/ai-benchmark.js', false);
        foreach ($vendors as $vendor => $n) {
            $attr = e($vendor);
            $response->assertSee('data-vendor="'.$attr.'" aria-pressed="false">'.$attr.' <span class="ab-chip__n">'.$n.'</span>', false);
        }
        $this->assertSame(count($entries), preg_match_all('/<li class="s-card ab-row( ab-row--leader)?" data-vendor="/', $response->getContent()));
        foreach ($entries as $e) {
            $response->assertSee('<span class="ab-row__rank" aria-label="#'.$e['rank'].'">', false);
        }

        // The category order: one chip per category, every row carrying its scores,
        // and only the rank-1 card marked as the leader (not whichever card is first).
        $response->assertSee('<span class="ab-sort__tpl" hidden>#:pos in :cat</span>', false)
            ->assertSee('data-sort="" aria-pressed="true">Overall</button>', false);
        foreach (['SEC' => 'Security', 'ARCH' => 'Architecture', 'BUG' => 'Bugs', 'PERF' => 'Performance',
            'CLN' => 'Clean code', 'COMP' => 'Compatibility', 'EXPL' => 'Explanation'] as $cat => $name) {
            $response->assertSee('data-sort="'.$cat.'" aria-pressed="false">'.$name.'</button>', false);
        }
        $html = $response->getContent();
        $this->assertSame(count($entries), preg_match_all('/data-scores="SEC:\d+ ARCH:\d+ BUG:\d+ PERF:\d+ CLN:\d+ COMP:\d+ EXPL:\d+"/', $html));
        $this->assertSame(count(array_filter($entries, fn ($e) => $e['rank'] === 1)), substr_count($html, 'ab-row--leader'));

        $this->get(self::PT)->assertOk()
            ->assertSee('Ordenar por')
            ->assertSee('#:pos em :cat')
            ->assertSee('Filtrar por fornecedor')
            ->assertSee('Todos')
            ->assertSee('Mostrando '.count($entries).' de '.count($entries).' agentes.');
    }

    public function test_every_card_opens_on_its_comment_and_the_details_read_from_its_runs(): void
    {
        $entries = AiBenchmark::results()['instances'][0]['entries'];
        $en = $this->get(self::EN, self::EN_HEADER)->assertOk();
        $html = $en->getContent();
        $this->assertSame(count($entries), substr_count($html, '<details class="ab-row__more">'));
        foreach ($entries as $e) {
            if ($e['comment'] ?? null) {
                $en->assertSee($e['comment']['en']);
            }
        }
        $this->assertSame(array_sum(array_map(fn ($e) => count($e['runs']), $entries)), substr_count($html, '<li>
                                                <strong>Run '));

        // Wall-clock time, run by run: minutes under an hour, hours and minutes above.
        foreach ($entries as $e) {
            foreach ($e['runs'] as $r) {
                if (($r['wall_minutes'] ?? null) !== null) {
                    $m = (int) round($r['wall_minutes']);
                    $en->assertSee($m < 60 ? "$m min" : intdiv($m, 60).' h '.($m % 60).' min');
                }
            }
        }

        $pt = $this->get(self::PT)->assertOk()->assertSee('Comentário e ficha')->assertSee('Run a run');
        foreach ($entries as $e) {
            if ($e['comment'] ?? null) {
                $pt->assertSee($e['comment']['pt_BR']);
            }
        }
    }

    public function test_a_card_without_a_comment_still_opens_on_its_details(): void
    {
        $data = AiBenchmark::results();
        $data['instances'][0]['entries'][0]['comment'] = null;
        AiBenchmark::fake($data);

        $html = $this->get(self::EN, self::EN_HEADER)->assertOk()->getContent();
        $this->assertSame(count($data['instances'][0]['entries']), substr_count($html, '<details class="ab-row__more">'));
        $this->assertSame(count(array_filter($data['instances'][0]['entries'], fn ($e) => $e['comment'] ?? null)), substr_count($html, 'class="ab-more__comment"'));
    }

    public function test_the_leaderboard_names_the_client_mode_next_to_the_effort(): void
    {
        $data = AiBenchmark::results();
        $entries = &$data['instances'][0]['entries'];
        $entries[0]['model'] = ['client_mode' => 'ultracode', 'reasoning_effort' => 'max'] + $entries[0]['model'];
        $entries[1]['model'] = ['client_mode' => 'ultracode', 'reasoning_effort' => 'default'] + $entries[1]['model'];
        unset($entries);
        AiBenchmark::fake($data);

        $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('effort max (ultracode)')
            ->assertSee('default effort (not configurable) (ultracode)');
        $this->get(self::PT)->assertOk()
            ->assertSee('esforço max (ultracode)')
            ->assertSee('esforço padrão (não configurável) (ultracode)');
    }

    /** The cross-agent reading renders under its instance, in both languages, with its markup. */
    public function test_the_reading_across_agents_renders_in_both_languages(): void
    {
        $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('Finding is not fixing')
            ->assertSee('<strong>Sonnet 5.5 fixes more, in the run that counts.</strong>', false);
        $this->get(self::PT)->assertOk()
            ->assertSee('Achar não é corrigir')
            ->assertSee('<strong>Sonnet 5.5 corrige mais, no run que conta.</strong>', false);
    }

    /** The downloadable CSVs are linked from both pages and hold the same runs as the page's data. */
    public function test_the_csv_downloads_are_linked_and_match_the_results(): void
    {
        $this->get(self::EN, self::EN_HEADER)->assertOk()
            ->assertSee('data/ai-benchmark/runs.csv', false)
            ->assertSee('data/ai-benchmark/flaws.csv', false);
        $this->get(self::PT)->assertOk()->assertSee('Falha por falha (CSV)');

        $runs = array_map('str_getcsv', file(public_path('data/ai-benchmark/runs.csv'), FILE_IGNORE_NEW_LINES));
        $header = array_shift($runs);
        $this->assertSame(['edition', 'instance', 'agent', 'run'], array_slice($header, 0, 4));
        $expected = collect(AiBenchmark::results()['instances'])->flatMap(fn ($i) => $i['entries'])->sum('runs_count');
        $this->assertCount($expected, $runs, 'runs.csv and results.json come from different exports: run tools/sync-ai-benchmark-results.sh');

        $flaws = file(public_path('data/ai-benchmark/flaws.csv'), FILE_IGNORE_NEW_LINES);
        $planted = count(AiBenchmark::results()['instances'][0]['flaws']);
        $this->assertCount($expected * $planted + 1, $flaws);
    }

    public function test_a_score_bar_is_clamped_to_its_track(): void
    {
        $this->assertSame(50, AiBenchmark::percent(125, 250));
        $this->assertSame(100, AiBenchmark::percent(300, 250));
        $this->assertSame(0, AiBenchmark::percent(-5, 250));
        $this->assertSame(0, AiBenchmark::percent(10, 0));
    }

    /** @return list<string> dotted key paths, sorted */
    private function keys(array $tree, string $prefix = ''): array
    {
        $out = [];
        foreach ($tree as $k => $v) {
            // Lists (the highlights) may differ in length per language; their
            // parent key is what has to exist on both sides.
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
