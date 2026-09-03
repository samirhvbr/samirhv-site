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

    /** A slug WITH an entry but WITHOUT that field — sshvterm has no description. */
    public function test_a_partial_entry_falls_back_field_by_field(): void
    {
        App::setLocale('en');

        $sshvterm = $this->project('sshvterm', 'SShvTerm', 'Cliente SSH/SFTP desktop');

        $this->assertSame('SShvTerm', Content::project($sshvterm, 'title'));
        $this->assertSame('Cliente SSH/SFTP desktop', Content::project($sshvterm, 'description'));
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
