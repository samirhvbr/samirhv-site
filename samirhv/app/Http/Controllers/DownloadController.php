<?php

namespace App\Http\Controllers;

use App\Models\DownloadLog;
use App\Models\ProjectFile;
use App\Services\UserAgentParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Servidor de download com contagem e auditoria. Único caminho para baixar um
 * arquivo de projeto (o disco `downloads` é privado). Conta só GET completo de
 * humano; bots e requisições parciais (Range) são auditados mas não contam.
 */
class DownloadController extends Controller
{
    public function track(Request $request, ProjectFile $file): RedirectResponse|StreamedResponse
    {
        if (! $file->is_available) {
            return redirect()->route('downloads')->with('download_unavailable', $file->label);
        }
        if (! $file->is_mirrored) {
            return redirect()->route('downloads')->with('download_unavailable', $file->label);
        }

        $ua = (string) $request->userAgent();

        $range = strtolower((string) $request->headers->get('Range'));
        $isPartialRange = $range !== '' && ! str_starts_with($range, 'bytes=0-');

        if ($request->isMethod('GET') && ! $isPartialRange) {
            $isBot = app(UserAgentParser::class)->isBotRequest($ua, (string) $request->ip());

            if (! $isBot) {
                $file->increment('downloads_count');
            }

            try {
                DownloadLog::create([
                    'project_file_id' => $file->id,
                    'user_id' => $request->user()?->id,
                    'ip' => (string) $request->ip(),
                    'user_agent' => $ua !== '' ? mb_substr($ua, 0, 1024) : null,
                    'referer' => mb_substr((string) $request->headers->get('referer'), 0, 1024) ?: null,
                    'method' => $request->method(),
                    'is_bot' => $isBot,
                    /* Aqui, ao contrário do PageView, fica a preferência do
                       NAVEGADOR de propósito: `/d/{file}` não é gêmea por
                       idioma — é um endpoint de entrega, com um endereço só —
                       então `app()->getLocale()` seria sempre o idioma nu e não
                       diria nada. O que o navegador pede é o único sinal de
                       idioma que existe num download. */
                    'locale' => mb_substr((string) $request->getPreferredLanguage(), 0, 35) ?: null,
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return Storage::disk('downloads')->download($file->filename, $file->original_name);
    }
}
