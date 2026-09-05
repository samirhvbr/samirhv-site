<?php

namespace Tests\Unit;

use App\Support\Content;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

/**
 * Database prose, translated by file.
 *
 * The behaviour that matters is the FALLBACK: a project with no entry has to
 * render what the database says, in Portuguese, rather than render blank or
 * render a raw translation key. A blank title on the English page would be a
 * broken site; a Portuguese one is merely an untranslated one.
 */
class ContentTranslationTest extends TestCase
{
    private function project(string $slug, string $title = 'x', string $description = 'y'): object
    {
        return (object) ['slug' => $slug, 'title' => $title, 'description' => $description];
    }

    public function test_a_translated_project_renders_the_translation_in_english(): void
    {
        App::setLocale('en');

        $shvia = $this->project('shvia', 'ShvIA', 'Assistente de IA interno da Blue3');

        $this->assertSame('ShvIA', Content::project($shvia, 'title'));
        $this->assertStringContainsString(
            'internal AI assistant',
            Content::project($shvia, 'description'),
        );
    }

    public function test_portuguese_renders_the_database_value(): void
    {
        App::setLocale('pt_BR');

        $shvia = $this->project('shvia', 'ShvIA', 'Assistente de IA interno da Blue3');

        $this->assertSame('Assistente de IA interno da Blue3', Content::project($shvia, 'description'));
    }

    /** A project created in the admin after this file was written. */
    public function test_an_unknown_slug_falls_back_to_the_database_value(): void
    {
        App::setLocale('en');

        $novo = $this->project('projeto-novo', 'Projeto Novo', 'Descrição em português');

        $this->assertSame('Projeto Novo', Content::project($novo, 'title'));
        $this->assertSame('Descrição em português', Content::project($novo, 'description'));
    }

    /**
     * A slug WITH an entry but WITHOUT that field.
     *
     * The fixture is declared here rather than pointed at a real project: this
     * used to name `sshvterm`, which had a title and no description, and the
     * test broke the day that description was written — asserting a fact about
     * the content instead of a fact about the fallback.
     */
    public function test_a_partial_entry_falls_back_field_by_field(): void
    {
        App::setLocale('en');
        app('translator')->addLines(['content.projects.so-o-titulo.title' => 'Title Only'], 'en');

        $parcial = $this->project('so-o-titulo', 'Só o título', 'Descrição sem tradução');

        $this->assertSame('Title Only', Content::project($parcial, 'title'));
        $this->assertSame('Descrição sem tradução', Content::project($parcial, 'description'));
    }

    /** Every project in the catalogue is fully translated — no field left behind. */
    public function test_every_catalogued_project_has_a_title_and_a_description(): void
    {
        $entries = (require lang_path('en/content.php'))['projects'];

        $this->assertNotEmpty($entries);

        foreach ($entries as $slug => $fields) {
            $this->assertArrayHasKey('title', $fields, $slug);
            $this->assertArrayHasKey('description', $fields, $slug);
        }
    }

    /** Never the key itself: `__()` returns the key on a miss, `Lang::has` does not. */
    public function test_a_miss_never_renders_a_translation_key(): void
    {
        App::setLocale('en');

        $valor = Content::project($this->project('nao-existe', 'Título'), 'title');

        $this->assertSame('Título', $valor);
        $this->assertStringNotContainsString('content.projects', $valor);
    }

    public function test_categories_are_matched_ignoring_case_and_accents(): void
    {
        App::setLocale('en');

        $this->assertSame('AI assistant', Content::category('Assistente IA'));
        $this->assertSame('Desktop application', Content::category('Aplicativo Desktop'));
        $this->assertSame('SSH client', Content::category('Cliente SSH'));
    }

    public function test_an_untranslated_category_comes_back_unchanged(): void
    {
        App::setLocale('en');

        $this->assertSame('Categoria Nova', Content::category('Categoria Nova'));
        $this->assertSame('', Content::category(null));
    }

    public function test_a_file_label_with_no_entry_comes_back_unchanged(): void
    {
        App::setLocale('en');

        $file = (object) ['label' => 'ShvIA_1.4.8_amd64.deb'];

        $this->assertSame('ShvIA_1.4.8_amd64.deb', Content::fileLabel($file));
        $this->assertSame('', Content::fileLabel((object) ['label' => null]));
    }
}
