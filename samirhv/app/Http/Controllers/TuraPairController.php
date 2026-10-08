<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use App\Services\TuraCredentials;
use App\Services\TuraPairing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Signing in to Tura Notes through the site: the three routes the application
 * uses (docs/PAIRING.md in the Tura repository).
 *
 *   GET  /tura/pair           the consent screen (needs the admin logged in)
 *   POST /tura/pair           Allow or Deny; returns the page that hands control
 *                             back to the application
 *   POST /tura/pair/exchange  the application trades the code for the credential
 *
 * A thin controller: the conversation with the server and the rules of the flow
 * live in {@see TuraPairing}. What stays here is the shape of the responses, and
 * one rule that holds for all three: NONE of it is cacheable or may be framed.
 * The second page carries a single-use code in its HTML, and the exchange returns
 * a bearer secret.
 */
class TuraPairController extends Controller
{
    public function __construct(
        private readonly TuraPairing $pairing,
        private readonly TuraCredentials $tura,
        private readonly AuditLogger $audit,
    ) {}

    public function show(Request $request): Response
    {
        $pair = $this->pairing->parse($request->query());

        if ($pair === null) {
            return $this->guarded(response()->view('tura.pair-invalid', [], 400));
        }

        return $this->guarded(response()->view('tura.pair', $this->consent($pair, $request)));
    }

    public function decide(Request $request): Response
    {
        $pair = $this->pairing->parse($request->except(['_token', 'decision']));

        if ($pair === null) {
            return $this->guarded(response()->view('tura.pair-invalid', [], 400));
        }

        if ($request->input('decision') !== 'allow') {
            $this->audit->record('tura.pair.deny', $pair['device'],
                "Aparelho «{$pair['device']}» negado ao tentar entrar no Tura Notes.", 'tura');

            return $this->guarded(response()->view('tura.pair-done', [
                'allowed' => false,
                'uri' => 'tura://pair?'.http_build_query(['error' => 'access_denied', 'state' => $pair['state']], '', '&', PHP_QUERY_RFC3986),
                'device' => $pair['device'],
            ]));
        }

        $code = $this->pairing->issue($pair);

        if ($code === null) {
            // Back to the same screen, saying what is missing: without the Tura
            // server on this host there is no credential to mint, and a 500
            // would not say so.
            return $this->guarded(response()->view('tura.pair', $this->consent($pair, $request) + [
                'error' => $this->tura->unavailableReason() ?? 'Não foi possível criar a credencial.',
            ]));
        }

        // The device's name, never the code nor the secret.
        $this->audit->record('tura.pair.allow', $pair['device'],
            "Aparelho «{$pair['device']}» autorizado a entrar no Tura Notes (workspace {$this->tura->workspace()}).", 'tura');

        return $this->guarded(response()->view('tura.pair-done', [
            'allowed' => true,
            'uri' => 'tura://pair?'.http_build_query(['code' => $code, 'state' => $pair['state']], '', '&', PHP_QUERY_RFC3986),
            'device' => $pair['device'],
        ]));
    }

    /**
     * The exchange. Every failure is the same answer (400 invalid_grant): a spent,
     * expired or unknown code and a verifier that does not match cannot be told
     * apart, and any attempt that finds the code consumes it.
     */
    public function exchange(Request $request): JsonResponse
    {
        $headers = ['Cache-Control' => 'no-store, max-age=0', 'Pragma' => 'no-cache', 'X-Content-Type-Options' => 'nosniff'];

        $body = $request->isJson() ? $request->json()->all() : [];
        $granted = $this->pairing->redeem($body['code'] ?? null, $body['verifier'] ?? null);

        if ($granted === null) {
            return response()->json(['error' => 'invalid_grant'], 400, $headers);
        }

        $this->audit->record('tura.pair.exchange', $granted['label'],
            "Credencial entregue ao aparelho «{$granted['label']}» do Tura Notes.", 'tura');

        return response()->json([
            'origin' => (string) config('tura.origin'),
            'workspace' => $this->tura->workspace(),
            'credential' => $granted['credential'],
            'label' => $granted['label'],
        ], 200, $headers);
    }

    /**
     * @param  array{challenge:string,state:string,label:string,device:string}  $pair
     * @return array<string,mixed>
     */
    private function consent(array $pair, Request $request): array
    {
        return [
            'pair' => $pair,
            'permissions' => (array) config('tura.pair.permissions'),
            'workspace' => $this->tura->workspace(),
            'user' => $request->user(),
        ];
    }

    private function guarded(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-store, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
