<?php

namespace App\Services\AiMemory;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/** Sessões (um agente trabalhando) e a timeline de observações de cada uma. */
class SessionRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /**
     * Lista filtrável/ordenável. $filters: project (hex), agent, days (janela
     * sobre started_at), sort ∈ recent|oldest|longest|shortest. A duração
     * ordena por (ended_at - started_at); sessões em aberto usam "agora" como
     * fim, para ordenar pela duração corrente.
     */
    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        $where = '1 = 1';
        $bind = [];
        if (! empty($filters['project'])) {
            $where .= ' AND lower(hex(s.project_id)) = ?';
            $bind[] = strtolower($filters['project']);
        }
        if (! empty($filters['agent'])) {
            $where .= ' AND s.agent_kind = ?';
            $bind[] = $filters['agent'];
        }
        if (! empty($filters['days'])) {
            $where .= ' AND s.started_at >= ?';
            $bind[] = Carbon::now('UTC')->subDays((int) $filters['days'])->timestamp * 1_000_000;
        }

        // A ordenação por duração injeta "agora" (?) no ORDER BY; por isso as
        // bindings do SELECT podem ter um item a mais que as do COUNT.
        $selectBind = $bind;
        $sort = $filters['sort'] ?? 'recent';
        if (in_array($sort, ['longest', 'shortest'], true)) {
            $order = 'COALESCE(s.ended_at, ?) - s.started_at '.($sort === 'longest' ? 'DESC' : 'ASC');
            $selectBind[] = Carbon::now('UTC')->timestamp * 1_000_000;
        } else {
            $order = 's.started_at '.($sort === 'oldest' ? 'ASC' : 'DESC');
        }

        $sql = "SELECT lower(hex(s.id)) AS id_hex, s.agent_kind, s.cwd, s.started_at, s.ended_at,
                       pr.name AS project,
                       (SELECT COUNT(*) FROM observations o WHERE o.session_id = s.id) AS obs_count
                  FROM sessions s
                  JOIN projects pr ON pr.id = s.project_id
                 WHERE {$where}
                 ORDER BY {$order}";

        return $this->db->paginate($sql, $selectBind, "SELECT COUNT(*) FROM sessions s WHERE {$where}", $bind, $perPage);
    }

    /** agent_kinds distintos, para o select de filtro. */
    public function agentKinds(): array
    {
        return array_map(
            static fn (object $r) => $r->agent_kind,
            $this->db->select('SELECT DISTINCT agent_kind FROM sessions ORDER BY agent_kind')
        );
    }

    /** Uma sessão por id hex, com projeto e página-resumo (se houver). */
    public function find(string $hexId): ?object
    {
        return $this->db->selectOne(
            'SELECT lower(hex(s.id)) AS id_hex, s.agent_kind, s.cwd, s.started_at, s.ended_at,
                    pr.name AS project, w.name AS workspace,
                    lower(hex(s.project_id)) AS project_hex,
                    lower(hex(s.summary_page_id)) AS summary_page_hex,
                    sp.title AS summary_title,
                    (SELECT COUNT(*) FROM observations o WHERE o.session_id = s.id) AS obs_count
               FROM sessions s
               JOIN projects pr ON pr.id = s.project_id
               JOIN workspaces w ON w.id = s.workspace_id
               LEFT JOIN pages sp ON sp.id = s.summary_page_id
              WHERE lower(hex(s.id)) = ?',
            [strtolower($hexId)]
        );
    }

    /** Observações da sessão em ordem cronológica (timeline). */
    public function observations(string $sessionHex): array
    {
        return $this->db->select(
            'SELECT lower(hex(id)) AS id_hex, kind, title, importance, created_at
               FROM observations
              WHERE lower(hex(session_id)) = ?
              ORDER BY created_at ASC',
            [strtolower($sessionHex)]
        );
    }
}
