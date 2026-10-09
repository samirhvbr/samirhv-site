<?php

namespace App\Services;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Signing in to the Tura Notes application through the site (docs/PAIRING.md in
 * the Tura repository).
 *
 * The application opens /tura/pair in the browser; the admin signs in and allows
 * the device; this service mints a credential for it and keeps it, for two
 * minutes, under a single-use CODE; the application trades the code for it,
 * proving it is the one that started the flow by presenting the `verifier`
 * (PKCE, RFC 7636).
 *
 * WHAT THIS SERVICE GUARANTEES, and why:
 *
 *   - The secret is NEVER in a URL. The redirect carries only the code, and
 *     another application on the device can claim `tura://`: what it carries
 *     must be useless without the `verifier`, which never left the application.
 *   - The secret waits ENCRYPTED (the application key) and the cache key is the
 *     HASH of the code, so a dump of the cache table yields neither the
 *     credential nor the code.
 *   - The code is good ONCE. The exchange removes it before checking the
 *     verifier, so a wrong verifier spends it too: whoever gets it wrong does
 *     not try again.
 *   - Every failure of the exchange is the SAME failure. A spent, expired or
 *     unknown code and a verifier that does not match cannot be told apart.
 *   - The secret goes to no log, queue, session or error message.
 */
class TuraPairing
{
    /** Length of an S256 challenge: 32 bytes as unpadded base64url. */
    private const CHALLENGE_LENGTH = 43;

    public function __construct(
        private readonly TuraCredentials $tura,
    ) {}

    /**
     * Validates what the application sent to /tura/pair and returns only what
     * the flow uses; null for anything outside the contract. It refuses rather
     * than repairs.
     *
     * @param  array<string,mixed>  $query
     * @return array{challenge:string,state:string,label:string,device:string}|null
     */
    public function parse(array $query): ?array
    {
        $allowed = ['client', 'challenge', 'challenge_method', 'state', 'label', 'redirect'];

        if (array_diff(array_keys($query), $allowed) !== []) {
            return null;
        }

        foreach ($allowed as $key) {
            if (! isset($query[$key]) || ! is_string($query[$key])) {
                return null;
            }
        }

        if ($query['client'] !== 'tura' || $query['challenge_method'] !== 'S256' || $query['redirect'] !== 'tura://pair') {
            return null;
        }

        if (strlen($query['challenge']) !== self::CHALLENGE_LENGTH || ! $this->isBase64Url($query['challenge'])) {
            return null;
        }

        // The `state` is opaque to the site: the application checks it on the way
        // back. Only its size and alphabet are limited, so the page never echoes
        // whatever arrives.
        if (strlen($query['state']) < 16 || strlen($query['state']) > 128 || ! $this->isBase64Url($query['state'])) {
            return null;
        }

        $label = trim($query['label']);
        if ($label === '' || mb_strlen($label) > 80 || preg_match('/[\x00-\x1f\x7f]/u', $label)) {
            return null;
        }

        return [
            'challenge' => $query['challenge'],
            'state' => $query['state'],
            'label' => $label,
            'device' => $this->deviceName($label),
        ];
    }

    /**
     * The name the credential carries on the server: what the device called
     * itself, in the alphabet the wrapper accepts (`[A-Za-z0-9_-]{1,64}`). The
     * application is given this name back, and it is what the credential list shows.
     */
    public function deviceName(string $label): string
    {
        $name = trim((string) preg_replace('/[^A-Za-z0-9_-]+/', '-', $label), '-');

        return $name === '' ? 'aparelho' : substr($name, 0, 64);
    }

    /**
     * Mints the credential and keeps it under a fresh code. Returns the code, or
     * null if the Tura server could not mint it (the reason is in
     * {@see TuraCredentials::unavailableReason()}).
     *
     * @param  array{challenge:string,state:string,label:string,device:string}  $request
     */
    public function issue(array $request): ?string
    {
        $secret = $this->tura->create($request['device'], (array) config('tura.pair.permissions'));

        // A credential has a shape: what lacks it is neither stored nor handed over.
        if ($secret === null || ! preg_match('/^nt_[A-Za-z0-9._-]{1,195}$/', $secret)) {
            return null;
        }

        $code = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        Cache::put($this->key($code), Crypt::encryptString(json_encode([
            'secret' => $secret,
            'challenge' => $request['challenge'],
            'label' => $request['device'],
        ], JSON_THROW_ON_ERROR)), (int) config('tura.pair.ttl'));

        return $code;
    }

    /**
     * Trades the code for the credential. Returns null for ANY reason, so the
     * caller cannot tell them apart. The code is consumed before it is checked.
     *
     * @return array{credential:string,label:string}|null
     */
    public function redeem(mixed $code, mixed $verifier): ?array
    {
        if (! $this->isToken($code) || ! $this->isToken($verifier)) {
            return null;
        }

        $key = $this->key($code);
        $payload = null;

        // `Cache::pull` is not atomic on the database driver: two simultaneous
        // exchanges of one code could both read before the first one deletes.
        // The lock closes that. Without it "single use" would mean "single use
        // when nobody races".
        $lock = Cache::lock('lock:'.$key, 5);

        try {
            $lock->block(2, function () use ($key, &$payload): void {
                $payload = Cache::get($key);
                Cache::forget($key);
            });
        } catch (LockTimeoutException) {
            // It did not get its turn: treated as any other failure of the
            // exchange. The code was not read and lives until it expires; whoever
            // tries again sees the same "invalid" a spent code would give.
            return null;
        }

        if (! is_string($payload)) {
            return null;
        }

        try {
            $data = json_decode(Crypt::decryptString($payload), true, 8, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }

        $expected = (string) ($data['challenge'] ?? '');
        $actual = rtrim(strtr(base64_encode(hash('sha256', (string) $verifier, true)), '+/', '-_'), '=');

        if ($expected === '' || ! hash_equals($expected, $actual)) {
            return null;
        }

        return ['credential' => (string) $data['secret'], 'label' => (string) $data['label']];
    }

    /** The cache key is the HASH of the code: the code itself never rests on disk. */
    private function key(string $code): string
    {
        return 'tura-pair:'.hash('sha256', $code);
    }

    private function isToken(mixed $value): bool
    {
        return is_string($value) && strlen($value) >= 43 && strlen($value) <= 128 && $this->isBase64Url($value);
    }

    private function isBase64Url(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9_-]+$/', $value) === 1;
    }
}
