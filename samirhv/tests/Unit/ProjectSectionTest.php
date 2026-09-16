<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

/**
 * The per-project sections of /p/{slug} — partials/projects/<slug>.blade.php.
 *
 * These render with NO DATABASE, which is not an accident of how the test is
 * written: the partials take no `$project` and read everything from `lang/`.
 * That is what lets them be asserted here at all, in a suite that deliberately
 * has no database (see Tests\TestCase).
 *
 * WHAT WOULD GO WRONG WITHOUT THIS FILE, and what each group catches:
 *
 *   - A key written in `lang/en` and forgotten in `lang/pt_BR`. Nothing errors:
 *     the translator falls back to English, so the Portuguese page renders an
 *     English sentence in the middle of a Portuguese paragraph. That is the
 *     exact leak App\Support\Content documents, and the only thing that
 *     notices is a parity assertion.
 *
 *   - A key renamed in the view and not in the file. `__()` returns the KEY, so
 *     the page prints "ai_memory.f_team_desc" as body text — visible to a
 *     reader, invisible to a 200 OK.
 *
 *   - A `:placeholder` in a sentence with no argument passed for it, which
 *     renders the literal colon-word in the middle of the prose.
 */
class ProjectSectionTest extends TestCase
{
    /** slug => the lang file its section reads. */
    private const SECTIONS = [
        'meuip' => 'meuip',
        'sshvterm' => 'sshvterm',
        'ai-memory' => 'ai_memory',
        'shvia' => 'shvia_models',
        'tura-notes' => 'tura_notes',
    ];

    /**
     * The optional extra rows of the access panel, under the same convention:
     * partials/projects/<slug>-access.blade.php. Same key shape as above.
     *
     * They are listed apart from SECTIONS because they are a row inside an
     * `<aside>` rather than a `<section>` — the copy assertions apply to both,
     * the structural one does not.
     */
    private const ACCESS_ROWS = [
        'ai-memory-access' => 'ai_memory',
    ];

    /** Every partial this file asserts, section or access row. */
    private function allPartials(): array
    {
        return self::SECTIONS + self::ACCESS_ROWS;
    }

    private function render(string $slug, string $locale): string
    {
        App::setLocale($locale);

        return view('partials.projects.'.$slug)->render();
    }

    public function test_every_section_renders_in_both_languages(): void
    {
        foreach (array_keys(self::SECTIONS) as $slug) {
            foreach (['en', 'pt_BR'] as $locale) {
                $html = $this->render($slug, $locale);

                $this->assertNotSame('', trim($html), "[$locale/$slug] rendered nothing.");
                $this->assertStringContainsString('<section', $html, "[$locale/$slug] has no section element.");
            }
        }
    }

    /**
     * An access row has to be a row of the panel it is dropped into.
     *
     * `show.blade.php` includes it inside the `<aside>` with no wrapper of its
     * own, so a partial that forgot the panel's option class would render as
     * unstyled text hanging off the bottom of the box — visible only by looking.
     */
    public function test_every_access_row_renders_as_a_panel_option(): void
    {
        foreach (array_keys(self::ACCESS_ROWS) as $slug) {
            foreach (['en', 'pt_BR'] as $locale) {
                $html = $this->render($slug, $locale);

                $this->assertStringContainsString('s-project-action-panel__option', $html, "[$locale/$slug] is not a panel option.");
                $this->assertStringContainsString('rel="noopener"', $html, "[$locale/$slug] opens an external link without rel=noopener.");
            }
        }
    }

    /**
     * A key that reached the page as text.
     *
     * `__('ai_memory.nope')` returns the string "ai_memory.nope", which Blade
     * then escapes and prints. It looks like a typo in the copy rather than a
     * missing translation, which is why it survives a read-through.
     */
    public function test_no_translation_key_reaches_the_page_as_text(): void
    {
        foreach ($this->allPartials() as $slug => $file) {
            /* The real key list, not a regular expression for one: the meuip
               section prints "meuip.rs/asn" as content, and a pattern loose
               enough to catch "meuip.lead" catches that too. */
            $keys = $this->flatten(require lang_path('en/'.$file.'.php'));

            foreach (['en', 'pt_BR'] as $locale) {
                $html = $this->render($slug, $locale);

                foreach ($keys as $key) {
                    $this->assertStringNotContainsString(
                        $file.'.'.$key,
                        $html,
                        "[$locale/$slug] printed the key '$file.$key' instead of its translation."
                    );
                }
            }
        }
    }

    /** An unreplaced `:placeholder` left in the middle of a sentence. */
    public function test_no_placeholder_is_left_unreplaced(): void
    {
        foreach (array_keys($this->allPartials()) as $slug) {
            foreach (['en', 'pt_BR'] as $locale) {
                $html = $this->render($slug, $locale);
                $text = strip_tags($html);

                $this->assertDoesNotMatchRegularExpression(
                    '/(?<![\w:])\:[a-z][a-z0-9_]{2,}/',
                    $text,
                    "[$locale/$slug] left a :placeholder in the rendered text."
                );
            }
        }
    }

    /**
     * Both files cover the same keys, recursively.
     *
     * `Lang::has()` falls back to English on a miss, so a slug or a field
     * present only in `lang/en` renders ENGLISH on the Portuguese page with no
     * error anywhere. Asserting the key SETS is the only way to see it.
     */
    public function test_both_languages_define_the_same_keys(): void
    {
        $files = array_values(self::SECTIONS);
        $files[] = 'project';

        foreach ($files as $file) {
            $en = require lang_path('en/'.$file.'.php');
            $pt = require lang_path('pt_BR/'.$file.'.php');

            $this->assertSame(
                $this->flatten($en),
                $this->flatten($pt),
                "lang/en/$file.php and lang/pt_BR/$file.php cover different keys."
            );
        }
    }

    /** Every project whose page names a site has its three strings in both files. */
    public function test_every_per_project_site_entry_is_complete(): void
    {
        foreach (['en', 'pt_BR'] as $locale) {
            $sites = (require lang_path($locale.'/project.php'))['sites'];

            foreach ($sites as $slug => $entry) {
                foreach (['title', 'desc', 'cta'] as $field) {
                    $this->assertArrayHasKey($field, $entry, "[$locale] project.sites.$slug is missing '$field'.");
                    $this->assertNotSame('', trim($entry[$field]), "[$locale] project.sites.$slug.$field is empty.");
                }

                $this->assertTrue(
                    Lang::has("project.sites.$slug.title", $locale, false),
                    "[$locale] project.sites.$slug does not resolve in its own locale."
                );
            }
        }
    }

    /**
     * The panel must not promise a download this site will never serve.
     *
     * "Desktop application — in preparation" is true of a download project with
     * no binary uploaded yet, and false of a project whose installers live on
     * its own site. Project::distributesFilesHere() is what tells them apart.
     */
    public function test_a_link_project_with_no_files_here_does_not_claim_one_is_coming(): void
    {
        $linkOnly = new Project(['slug' => 'meuip', 'external_url' => 'https://meuip.rs']);
        $linkOnly->setRelation('availableFiles', collect());

        $this->assertFalse($linkOnly->distributesFilesHere());
    }

    public function test_a_download_project_still_says_a_build_is_coming(): void
    {
        $download = new Project(['slug' => 'tura-notes', 'external_url' => null]);

        $this->assertTrue($download->distributesFilesHere());
    }

    /** A hybrid (a site AND files here) keeps the promise: ShvIA is one. */
    public function test_a_hybrid_with_files_here_still_distributes_here(): void
    {
        $hybrid = new Project(['slug' => 'shvia', 'external_url' => 'https://ia.blue3.com.br']);
        $hybrid->setRelation('availableFiles', collect([new ProjectFile]));

        $this->assertTrue($hybrid->distributesFilesHere());
    }

    /** @return list<string> every leaf key path, sorted. */
    private function flatten(array $array, string $prefix = ''): array
    {
        $keys = [];

        foreach ($array as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            $keys = array_merge($keys, is_array($value) ? $this->flatten($value, $path) : [$path]);
        }

        sort($keys);

        return $keys;
    }
}
