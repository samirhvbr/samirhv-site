<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\GithubReleaseChecker;
use App\Support\SemVer;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Monitor de projetos: para cada projeto que é fork de um OSS (tem
 * `upstream_repo`), compara a NOSSA versão (maior semver entre os arquivos
 * disponíveis) com a última do upstream no GitHub, sinalizando divergência.
 *
 * Controller fino: a consulta ao GitHub vive em GithubReleaseChecker (com
 * cache), a comparação em SemVer. Aqui só orquestramos e montamos as linhas.
 */
class MonitorController extends Controller
{
    /** Piso entre duas checagens reais no GitHub, em segundos. */
    private const REFRESH_EVERY = 300;

    private const REFRESH_KEY = 'monitor:last-refresh';

    public function __construct(private readonly GithubReleaseChecker $github) {}

    public function index(Request $request): View
    {
        $projects = Project::with('availableFiles')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        /* "Verificar agora": fura o cache dos repos antes de recomputar.
           Cada projeto rastreado vira UMA chamada HTTP síncrona dentro deste
           request, e a API do GitHub sem autenticação dá 60 requisições por
           hora por IP (ver GithubReleaseChecker). Com alguns forks, dois
           cliques seguidos no botão já comem uma fatia do teto e o resultado é
           uma tela inteira de `rate_limit` — que parece defeito nosso.
           Daí o piso: refurar o cache no máximo uma vez a cada 5 minutos, e
           dizer que foi isso que aconteceu em vez de silenciosamente não
           atualizar. O cache normal continua servindo a tela. */
        $refreshFloor = null;

        if ($request->boolean('refresh')) {
            $lastRefresh = Cache::get(self::REFRESH_KEY);

            if ($lastRefresh instanceof CarbonInterface && $lastRefresh->diffInSeconds(now()) < self::REFRESH_EVERY) {
                $refreshFloor = (int) ceil(self::REFRESH_EVERY - $lastRefresh->diffInSeconds(now()));
            } else {
                $projects->filter->hasUpstream()
                    ->each(fn (Project $p) => $this->github->refresh($p->upstream_repo));

                Cache::put(self::REFRESH_KEY, now(), now()->addSeconds(self::REFRESH_EVERY));
            }
        }

        $rows = $projects->map(fn (Project $p) => $this->buildRow($p));

        $tracked = $rows->where('tracked', true);
        $summary = [
            'tracked' => $tracked->count(),
            'outdated' => $tracked->where('status', 'outdated')->count(),
            'errors' => $tracked->where('status', 'error')->count(),
        ];

        return view('admin.monitor.index', compact('rows', 'summary', 'refreshFloor'));
    }

    /** @return array<string,mixed> */
    private function buildRow(Project $project): array
    {
        $local = $project->localVersion();

        $row = [
            'project' => $project,
            'tracked' => $project->hasUpstream(),
            'local' => $local,
            'upstream' => null,
            'upstream_raw' => null,
            'upstream_url' => $project->upstream_url,
            'source' => null,
            'published_at' => null,
            'status' => 'untracked',
            'error' => null,
        ];

        if (! $project->hasUpstream()) {
            return $row;
        }

        $res = $this->github->latest($project->upstream_repo);

        if (! ($res['ok'] ?? false)) {
            return ['status' => 'error', 'error' => $res['error'] ?? 'desconhecido'] + $row;
        }

        $upstream = $res['version'];

        return [
            'upstream' => $upstream,
            'upstream_raw' => $res['raw'] ?? $upstream,
            'upstream_url' => $res['url'] ?? $project->upstream_url,
            'source' => $res['source'] ?? null,
            'published_at' => $res['published_at'] ?? null,
            'status' => $this->status($local, $upstream),
        ] + $row;
    }

    /**
     * Compara nossa versão com a do upstream.
     *  - no_local  : não temos versão local para comparar (mostra só a upstream)
     *  - unknown   : alguma das versões não é semver comparável
     *  - outdated  : upstream à frente (precisa atualizar) ← o alerta
     *  - up_to_date: iguais
     *  - ahead     : estamos à frente do upstream (incomum)
     */
    private function status(?string $local, ?string $upstream): string
    {
        if ($local === null || $local === '') {
            return 'no_local';
        }
        if (! SemVer::isParsable($local) || ! SemVer::isParsable($upstream)) {
            return 'unknown';
        }

        return match (SemVer::compare($local, $upstream) <=> 0) {
            -1 => 'outdated',
            0 => 'up_to_date',
            default => 'ahead',
        };
    }
}
