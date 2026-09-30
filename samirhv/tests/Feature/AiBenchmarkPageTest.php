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
            'effort xhigh', 'matrix 68088', 'the same names', 'not official'] as $english) {
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
