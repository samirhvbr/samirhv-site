<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

/**
 * A tela de credenciais do Tura — /admin/tura.
 *
 * O que ela manipula é um segredo ao portador: quem o lê é o aparelho. Então o
 * que estes casos protegem não é o formulário, é o segredo e a fronteira.
 *
 *   - O segredo volta como DOWNLOAD e nunca como HTML. Renderizado numa página
 *     ele fica no histórico, no cache e em qualquer captura de tela, e nada
 *     nisso dá erro — some sem avisar.
 *   - Um rótulo inválido é recusado ANTES de virar argumento do wrapper. O
 *     wrapper valida de novo, porque é a fronteira que não confia em ninguém,
 *     mas um formulário que deixa passar transforma erro de campo em erro de
 *     script.
 *   - Sem o wrapper instalado a tela EXPLICA. O modo de falha alternativo é um
 *     500 numa tela de admin, que não diz o que instalar.
 *
 * Sem banco, como o resto da suíte: AuditLogger já engole a própria falha de
 * gravação, e nenhum caso aqui depende de uma linha persistida.
 */
class TuraCredentialTest extends TestCase
{
    private const SECRET = 'not-a-real-secret-bytes';

    private function admin(): User
    {
        $user = new User(['name' => 'Samir', 'email' => 'samir@example.test']);
        $user->is_admin = true;
        $user->must_change_password = false;

        return $user;
    }

    /** O comando pedido, como texto — `command` é o array que o serviço passou. */
    private function commandLine(object $process): string
    {
        return is_array($process->command) ? implode(' ', $process->command) : (string) $process->command;
    }

    /** Um wrapper que existe em disco; o que ele faria é assunto do Process::fake. */
    private function installWrapper(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'tura-wrapper-');
        $this->beforeApplicationDestroyed(fn () => @unlink($path));
        config(['tura.wrapper' => $path, 'tura.run_as' => 'notes', 'tura.workspace' => 'personal']);

        return $path;
    }

    public function test_it_explains_itself_when_the_wrapper_is_not_installed(): void
    {
        config(['tura.wrapper' => '/nao/existe/tura-credential']);
        Process::fake();

        $response = $this->actingAs($this->admin())->get('/admin/tura');

        $response->assertOk();
        $response->assertSee('Servidor do Tura indisponível neste host');
        $response->assertSee('/nao/existe/tura-credential');
        Process::assertNothingRan();
    }

    public function test_it_lists_the_credentials_the_server_reports(): void
    {
        $wrapper = $this->installWrapper();
        Process::fake(['*' => Process::result(output: json_encode([
            ['id' => '0f1e2d3c-4b5a-6978-8796-a5b4c3d2e1f0', 'label' => 'macbook',
                'workspace' => 'personal', 'permissions' => ['read', 'create'], 'revoked' => false],
            ['id' => '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d', 'label' => 'antigo',
                'workspace' => 'personal', 'permissions' => ['read'], 'revoked' => true],
        ]))]);

        $response = $this->actingAs($this->admin())->get('/admin/tura');

        $response->assertOk();
        $response->assertSee('macbook');
        $response->assertSee('ativa');
        $response->assertSee('revogada');
        Process::assertRan(fn ($process) => str_contains($this->commandLine($process), 'sudo')
            && str_contains($this->commandLine($process), $wrapper));
    }

    public function test_the_secret_comes_back_as_a_download_and_never_as_a_page(): void
    {
        $this->installWrapper();
        Process::fake(['*' => Process::result(output: self::SECRET."\n")]);

        $response = $this->actingAs($this->admin())->post('/admin/tura', ['label' => 'macbook']);

        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename="macbook.secret"');
        // O Symfony normaliza e reordena Cache-Control (e acrescenta `private`),
        // então o que se assere é a diretiva, não a string que saiu daqui.
        $this->assertStringContainsString('no-store', $response->headers->get('cache-control'));
        $this->assertSame(self::SECRET, $response->getContent());
        // O corpo é o segredo e nada mais: sem HTML em volta não há página para
        // ele vazar, que é exatamente a propriedade que se quer.
        $this->assertStringNotContainsString('<', $response->getContent());
    }

    public function test_the_permissions_are_the_ones_synchronization_uses(): void
    {
        $this->installWrapper();
        Process::fake(['*' => Process::result(output: self::SECRET)]);

        $this->actingAs($this->admin())->post('/admin/tura', ['label' => 'macbook']);

        Process::assertRan(fn ($process) => str_ends_with(
            $this->commandLine($process), 'create macbook personal read,create,update,move,delete'
        ));
    }

    public function test_a_label_that_could_reach_a_shell_never_reaches_the_wrapper(): void
    {
        $this->installWrapper();
        Process::fake();

        foreach (['macbook; rm -rf /', 'mac book', 'mac/book', '', str_repeat('a', 65), '../etc'] as $label) {
            $response = $this->actingAs($this->admin())->post('/admin/tura', ['label' => $label]);
            $response->assertSessionHasErrors('label');
        }

        Process::assertNothingRan();
    }

    public function test_a_revocation_target_must_be_a_credential_id(): void
    {
        $this->installWrapper();
        Process::fake();

        $response = $this->actingAs($this->admin())
            ->delete('/admin/tura', ['id' => 'todas; drop database']);

        $response->assertSessionHasErrors('id');
        Process::assertNothingRan();
    }

    /**
     * Uma classe de CSS que não existe não dá erro em lugar nenhum.
     *
     * A primeira versão desta tela usava `btn btn-primary` e `btn-danger`, que
     * são as classes de outro projeto: o botão renderiza, a página responde
     * 200, a suíte passa, e o que aparece é texto sem forma nenhuma. O único
     * jeito de ver é olhando — ou assim.
     */
    public function test_every_style_class_these_views_use_exists_in_the_admin_css(): void
    {
        $css = file_get_contents(public_path('css/admin/layout.css'));
        $markup = file_get_contents(resource_path('views/admin/tura/index.blade.php'))
            .file_get_contents(resource_path('views/admin/tura/_unavailable.blade.php'));

        preg_match_all('/class="([^"{}]+)"/', $markup, $matches);
        $used = collect($matches[1])
            ->flatMap(fn (string $attribute) => preg_split('/\s+/', trim($attribute)))
            ->filter()
            ->unique();

        $this->assertNotEmpty($used, 'no class attributes found — the pattern stopped matching');

        foreach ($used as $class) {
            // Os ícones vêm do Font Awesome do tema (vendor/canvas/css/font-icons.css),
            // que é outra folha e outro assunto.
            if (str_starts_with($class, 'fa-')) {
                continue;
            }

            $this->assertStringContainsString(".{$class}", $css, "a classe .{$class} não existe no CSS do admin");
        }
    }

    public function test_a_failing_wrapper_becomes_a_message_and_not_a_five_hundred(): void
    {
        $this->installWrapper();
        Process::fake(['*' => Process::result(output: '', errorOutput: 'tura-credential: invalid workspace', exitCode: 2)]);

        $response = $this->actingAs($this->admin())->post('/admin/tura', ['label' => 'macbook']);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'tura-credential: invalid workspace');
    }
}
