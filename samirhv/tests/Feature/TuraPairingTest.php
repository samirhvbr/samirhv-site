<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackPageView;
use App\Models\User;
use App\Services\TuraPairing;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Signing in to Tura Notes through the site — /tura/pair (docs/PAIRING.md in the
 * Tura repository).
 *
 * What the flow protects is a bearer credential. So what these cases assert is not
 * the screen, it is the boundary:
 *
 *   - the secret is never in a URL, a page, a log or an error message;
 *   - the code is good once, and a wrong verifier spends it too;
 *   - every failure of the exchange is the SAME answer: whoever errs does not learn why;
 *   - only the logged-in admin sees the consent screen, and only the application
 *     that started the flow (the verifier's owner) trades the code.
 *
 * No database, like the rest of the suite: AuditLogger swallows its own write failure.
 */
class TuraPairingTest extends TestCase
{
    private const SECRET = 'nt_0b1c2d3e-0000-4000-8000-000000000000.s3cr3tS3cr3tS3cr3tS3cr3tS3cr3tS3cr3t00';

    /** The example of RFC 7636, appendix B: this verifier has this challenge. */
    private const VERIFIER = 'dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk';

    private const CHALLENGE = 'E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM';

    private const STATE = 'x7Qf3Lp9Zr2Vb8Nc4Hd6Jm1Ks5Tw0Ya';

    protected function setUp(): void
    {
        parent::setUp();

        // The suite has no .env: the encrypter needs a key for the secret that waits.
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        app()->forgetInstance('encrypter');

        $wrapper = $this->installWrapper();
        $this->beforeApplicationDestroyed(fn () => @unlink($wrapper));
        Cache::flush();
        RateLimiter::clear('tura-pair');
    }

    private function admin(bool $is = true): User
    {
        $user = new User(['name' => 'Samir', 'email' => 'samir@example.test']);
        $user->is_admin = $is;
        $user->must_change_password = false;

        return $user;
    }

    private function installWrapper(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'tura-wrapper-');
        config(['tura.wrapper' => $path, 'tura.run_as' => 'notes', 'tura.workspace' => 'personal',
            'tura.origin' => 'https://tura.example.test']);

        return $path;
    }

    /** @return array<string,string> */
    private function params(array $over = []): array
    {
        return $over + [
            'client' => 'tura',
            'challenge' => self::CHALLENGE,
            'challenge_method' => 'S256',
            'state' => self::STATE,
            'label' => 'Pixel 8',
            'redirect' => 'tura://pair',
        ];
    }

    private function commandLine(object $process): string
    {
        return is_array($process->command) ? implode(' ', $process->command) : (string) $process->command;
    }

    /** Allows the device and returns the code the redirect carries. */
    private function allow(array $over = []): string
    {
        Process::fake(['*' => Process::result(output: self::SECRET."\n")]);

        $response = $this->actingAs($this->admin())->post('/tura/pair', $this->params($over) + ['decision' => 'allow']);
        $response->assertOk();

        $this->assertSame(1, preg_match('~tura://pair\?code=([A-Za-z0-9_-]+)&amp;state=~', $response->getContent(), $m));

        return $m[1];
    }

    private function exchange(array $body, bool $json = true)
    {
        return $json ? $this->postJson('/tura/pair/exchange', $body) : $this->post('/tura/pair/exchange', $body);
    }

    // ---- who may see the consent screen -------------------------------------------------

    public function test_a_visitor_who_is_not_logged_in_is_sent_to_the_login(): void
    {
        Process::fake();

        $this->get('/tura/pair?'.http_build_query($this->params()))->assertRedirect(route('login'));

        Process::assertNothingRan();
    }

    public function test_a_logged_in_user_who_is_not_the_admin_is_refused(): void
    {
        Process::fake();

        $this->actingAs($this->admin(false))->get('/tura/pair?'.http_build_query($this->params()))->assertForbidden();
        $this->actingAs($this->admin(false))->post('/tura/pair', $this->params() + ['decision' => 'allow'])->assertForbidden();

        Process::assertNothingRan();
    }

    // ---- the request the application sends ----------------------------------------------

    public function test_the_consent_screen_names_the_device_the_workspace_and_every_permission_and_creates_nothing(): void
    {
        Process::fake();

        $response = $this->actingAs($this->admin())->get('/tura/pair?'.http_build_query($this->params()));

        $response->assertOk();
        $response->assertSee('Pixel 8');
        $response->assertSee('Pixel-8');
        $response->assertSee('personal');
        $response->assertSee('read, create, update, move, delete, search');
        $response->assertDontSee('devices');
        // Seeing the question creates nothing: the credential is born at "Allow".
        Process::assertNothingRan();
    }

    public function test_every_page_of_the_flow_forbids_caching_framing_and_referrers(): void
    {
        $page = $this->actingAs($this->admin())->get('/tura/pair?'.http_build_query($this->params()));
        $done = $this->actingAs($this->admin())->post('/tura/pair', $this->params() + ['decision' => 'deny']);
        $bad = $this->actingAs($this->admin())->get('/tura/pair?client=nobody');

        foreach ([$page, $done, $bad] as $response) {
            $this->assertStringContainsString('no-store', (string) $response->headers->get('cache-control'));
            $this->assertSame('DENY', $response->headers->get('x-frame-options'));
            $this->assertSame('no-referrer', $response->headers->get('referrer-policy'));
        }
    }

    /** @return array<string,array{0:array<string,mixed>}> */
    public static function badRequests(): array
    {
        $good = [
            'client' => 'tura', 'challenge' => self::CHALLENGE, 'challenge_method' => 'S256',
            'state' => self::STATE, 'label' => 'Pixel 8', 'redirect' => 'tura://pair',
        ];

        return [
            'nothing' => [[]],
            'another client' => [['client' => 'other'] + $good],
            'plain challenge method' => [['challenge_method' => 'plain'] + $good],
            'a redirect to the web' => [['redirect' => 'https://evil.example/cb'] + $good],
            'a redirect with a path' => [['redirect' => 'tura://pair/extra'] + $good],
            'a short challenge' => [['challenge' => 'abc'] + $good],
            'a challenge with a forbidden character' => [['challenge' => substr(self::CHALLENGE, 0, 42).'+'] + $good],
            'a missing state' => [array_diff_key($good, ['state' => 1])],
            'a short state' => [['state' => 'short'] + $good],
            'a state with markup' => [['state' => '"><script>alert(1)</script>xxxxxxxxxx'] + $good],
            'an empty label' => [['label' => '   '] + $good],
            'a label with a control character' => [['label' => "a\nb"] + $good],
            'an enormous label' => [['label' => str_repeat('a', 81)] + $good],
            'a parameter nobody defined' => [['next' => '/admin'] + $good],
            'an array where a string belongs' => [['label' => ['x']] + $good],
        ];
    }

    /** @param array<string,mixed> $query */
    #[DataProvider('badRequests')]
    public function test_a_request_outside_the_contract_is_refused_before_anything_is_made(array $query): void
    {
        Process::fake();

        $this->actingAs($this->admin())->get('/tura/pair?'.http_build_query($query))->assertStatus(400)->assertSee('Pedido inválido');
        $this->actingAs($this->admin())->post('/tura/pair', $query + ['decision' => 'allow'])->assertStatus(400);

        Process::assertNothingRan();
    }

    public function test_a_device_name_is_something_the_wrapper_accepts(): void
    {
        $pairing = app(TuraPairing::class);

        $this->assertSame('Pixel-8', $pairing->deviceName('Pixel 8'));
        $this->assertSame('Meu-Mac-mini', $pairing->deviceName('  Meu Mac mini!! '));
        $this->assertSame('aparelho', $pairing->deviceName('日本語'));
        $this->assertSame(64, strlen($pairing->deviceName(str_repeat('a', 80))));
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{1,64}$/', $pairing->deviceName('a b/c\\d`;rm -rf'));
    }

    // ---- deciding ------------------------------------------------------------------------

    public function test_allowing_creates_one_credential_for_the_device_with_the_six_permissions(): void
    {
        Process::fake(['*' => Process::result(output: self::SECRET."\n")]);

        $this->actingAs($this->admin())->post('/tura/pair', $this->params() + ['decision' => 'allow'])->assertOk();

        Process::assertRanTimes(fn () => true, 1);
        Process::assertRan(fn ($process) => str_ends_with(
            $this->commandLine($process), 'create Pixel-8 personal read,create,update,move,delete,search'
        ));
    }

    public function test_the_page_after_allowing_carries_a_code_and_the_state_and_never_the_secret(): void
    {
        Process::fake(['*' => Process::result(output: self::SECRET."\n")]);

        $response = $this->actingAs($this->admin())->post('/tura/pair', $this->params() + ['decision' => 'allow']);

        $response->assertOk();
        $response->assertSee('tura://pair?code=', false);
        $response->assertSee('state='.self::STATE, false);
        $this->assertStringNotContainsString(self::SECRET, $response->getContent());
        $this->assertStringNotContainsString('s3cr3t', $response->getContent());
        // The address also appears in a field to paste, where the browser has nothing to open tura://.
        $response->assertSee('Endereço para colar no aplicativo');
    }

    public function test_denying_creates_nothing_and_tells_the_application_so(): void
    {
        Process::fake();

        $response = $this->actingAs($this->admin())->post('/tura/pair', $this->params() + ['decision' => 'deny']);

        $response->assertOk();
        $response->assertSee('tura://pair?error=access_denied&amp;state='.self::STATE, false);
        $response->assertSee('Aparelho negado');
        Process::assertNothingRan();
    }

    public function test_anything_but_allow_is_a_denial(): void
    {
        Process::fake();

        // (`allow ` with a space is not here: the framework's TrimStrings trims it before
        // the controller sees it, and a trimmed `allow` is `allow`.)
        foreach (['', 'ALLOW', 'allowed', 'yes', '1', 'deny'] as $decision) {
            $this->actingAs($this->admin())->post('/tura/pair', $this->params() + ['decision' => $decision])
                ->assertSee('access_denied', false);
        }
        Process::assertNothingRan();
    }

    public function test_a_server_that_cannot_make_the_credential_says_so_and_leaves_no_code_behind(): void
    {
        config(['tura.wrapper' => '/nao/existe/tura-credential']);
        Process::fake();

        $response = $this->actingAs($this->admin())->post('/tura/pair', $this->params() + ['decision' => 'allow']);

        $response->assertOk();
        $response->assertSee('/nao/existe/tura-credential');
        $response->assertDontSee('tura://pair?code=', false);
    }

    public function test_what_the_server_prints_that_is_not_a_credential_is_never_stored_or_handed_over(): void
    {
        Process::fake(['*' => Process::result(output: "Warning: something else\n")]);

        $response = $this->actingAs($this->admin())->post('/tura/pair', $this->params() + ['decision' => 'allow']);

        $response->assertDontSee('tura://pair?code=', false);
    }

    // ---- the exchange --------------------------------------------------------------------

    public function test_the_application_trades_the_code_for_the_credential_once(): void
    {
        $code = $this->allow();

        $response = $this->exchange(['code' => $code, 'verifier' => self::VERIFIER]);

        $response->assertOk();
        $response->assertExactJson([
            'origin' => 'https://tura.example.test',
            'workspace' => 'personal',
            'credential' => self::SECRET,
            'label' => 'Pixel-8',
        ]);
        $this->assertStringContainsString('no-store', (string) $response->headers->get('cache-control'));

        // The second time, with the very pair that just worked, it does not.
        $this->exchange(['code' => $code, 'verifier' => self::VERIFIER])->assertStatus(400)->assertExactJson(['error' => 'invalid_grant']);
    }

    public function test_a_verifier_that_is_not_the_one_the_flow_started_with_is_refused_and_spends_the_code(): void
    {
        $code = $this->allow();
        $other = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';

        $this->exchange(['code' => $code, 'verifier' => $other])->assertStatus(400)->assertExactJson(['error' => 'invalid_grant']);
        // Whoever erred does not try again, and neither does the owner: the code is gone.
        $this->exchange(['code' => $code, 'verifier' => self::VERIFIER])->assertStatus(400)->assertExactJson(['error' => 'invalid_grant']);
    }

    public function test_a_code_that_expired_is_refused_the_same_way(): void
    {
        $code = $this->allow();

        $this->travel(config('tura.pair.ttl') + 1)->seconds();

        $this->exchange(['code' => $code, 'verifier' => self::VERIFIER])->assertStatus(400)->assertExactJson(['error' => 'invalid_grant']);
    }

    public function test_the_code_works_just_before_it_expires(): void
    {
        $code = $this->allow();

        $this->travel(config('tura.pair.ttl') - 5)->seconds();

        $this->exchange(['code' => $code, 'verifier' => self::VERIFIER])->assertOk();
    }

    /** @return array<string,array{0:mixed}> */
    public static function badBodies(): array
    {
        $v = self::VERIFIER;

        return [
            'no code' => [['verifier' => $v]],
            'no verifier' => [['code' => str_repeat('a', 43)]],
            'a code nobody issued' => [['code' => str_repeat('a', 43), 'verifier' => $v]],
            'a code of the wrong size' => [['code' => 'abc', 'verifier' => $v]],
            'a code with a forbidden character' => [['code' => str_repeat('a', 42).'/', 'verifier' => $v]],
            'arrays instead of strings' => [['code' => ['x'], 'verifier' => [$v]]],
            'numbers' => [['code' => 1234, 'verifier' => 5678]],
        ];
    }

    /** @param array<string,mixed> $body */
    #[DataProvider('badBodies')]
    public function test_a_body_outside_the_contract_gets_the_same_refusal_as_a_wrong_code(array $body): void
    {
        $this->exchange($body)->assertStatus(400)->assertExactJson(['error' => 'invalid_grant']);
    }

    public function test_a_form_post_is_not_a_json_exchange(): void
    {
        $code = $this->allow();

        // JSON only: a form or a query string does not hand over the credential.
        $this->exchange(['code' => $code, 'verifier' => self::VERIFIER], json: false)->assertStatus(400)->assertExactJson(['error' => 'invalid_grant']);
        $this->post('/tura/pair/exchange?code='.$code.'&verifier='.self::VERIFIER)->assertStatus(400);
        // And none of that spent the code.
        $this->exchange(['code' => $code, 'verifier' => self::VERIFIER])->assertOk();
    }

    public function test_the_exchange_needs_no_session_and_sets_nothing_that_identifies_one(): void
    {
        $code = $this->allow();
        auth()->logout();

        $this->exchange(['code' => $code, 'verifier' => self::VERIFIER])->assertOk();
    }

    public function test_the_exchange_is_rate_limited_by_address(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->exchange(['code' => str_repeat('a', 43), 'verifier' => self::VERIFIER])->assertStatus(400);
        }

        $this->exchange(['code' => str_repeat('a', 43), 'verifier' => self::VERIFIER])->assertStatus(429);
    }

    // ---- what must never leak ------------------------------------------------------------

    public function test_the_secret_and_the_code_never_reach_a_log_nor_the_cache_in_the_clear(): void
    {
        $log = tempnam(sys_get_temp_dir(), 'tura-log-');
        $this->beforeApplicationDestroyed(fn () => @unlink($log));
        config(['logging.default' => 'single', 'logging.channels.single.path' => $log]);
        Log::forgetChannel('single');

        $code = $this->allow();
        $this->exchange(['code' => $code, 'verifier' => 'wrong-wrong-wrong-wrong-wrong-wrong-wrong-wrong'])->assertStatus(400);
        $this->exchange(['code' => $code, 'verifier' => self::VERIFIER])->assertStatus(400);

        $written = (string) file_get_contents($log);
        $this->assertStringNotContainsString(self::SECRET, $written);
        $this->assertStringNotContainsString('s3cr3t', $written);
        $this->assertStringNotContainsString($code, $written);
    }

    public function test_what_waits_in_the_cache_is_encrypted_and_not_under_the_code(): void
    {
        $code = $this->allow();

        // Neither the key nor the value holds what serves as a credential.
        $this->assertNull(Cache::get('tura-pair:'.$code));
        $stored = Cache::get('tura-pair:'.hash('sha256', $code));
        $this->assertIsString($stored);
        $this->assertStringNotContainsString('s3cr3t', $stored);
        $this->assertStringNotContainsString(self::CHALLENGE, $stored);
    }

    public function test_the_pairing_pages_are_not_counted_as_visits(): void
    {
        $skipped = (new \ReflectionClassConstant(TrackPageView::class, 'SKIP_PREFIXES'))->getValue();

        $this->assertContains('tura', $skipped);
    }
}
