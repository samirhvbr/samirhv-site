<?php

namespace Tests\Unit;

use App\Models\Project;
use Tests\TestCase;

/**
 * A marca do projeto é uma CONVENÇÃO de disco, não uma coluna.
 *
 * O que tem de valer, e o motivo de cada asserção:
 *
 *   - um projeto COM `public/img/projects/<slug>/mark.svg` devolve a url dele;
 *   - um projeto SEM devolve null, porque o partial cai no ícone do Font
 *     Awesome — todos os projetos faziam isso antes de a marca existir, e
 *     nenhum pode parar de fazer;
 *   - a varredura é uma só por request. Isso é o que a memoização compra e é
 *     também o que a torna capaz de mentir, então `forgetMarks()` existe e é
 *     exercitado aqui: sem ele, este arquivo dependeria da ordem dos testes.
 */
class ProjectMarkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Project::forgetMarks();
    }

    protected function tearDown(): void
    {
        Project::forgetMarks();
        parent::tearDown();
    }

    public function test_a_project_with_a_mark_on_disk_returns_its_url(): void
    {
        $projeto = new Project(['slug' => 'tura-notes']);

        $this->assertFileExists(public_path('img/projects/tura-notes/mark.svg'));
        $this->assertSame(asset('img/projects/tura-notes/mark.svg'), $projeto->mark_url);
    }

    public function test_a_project_with_no_mark_returns_null(): void
    {
        $projeto = new Project;
        $projeto->slug = 'nao-tem-marca-nenhuma';

        $this->assertNull($projeto->mark_url);
    }

    /** Um slug vazio não pode virar `img/projects//mark.svg`. */
    public function test_an_empty_slug_returns_null(): void
    {
        $projeto = new Project;

        $this->assertNull($projeto->mark_url);
    }
}
