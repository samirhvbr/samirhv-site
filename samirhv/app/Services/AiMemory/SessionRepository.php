<?php

namespace App\Services\AiMemory;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/** Sessions (one agent at work) and the observation timeline of each one. */
class SessionRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /**
     * Filterable, sortable listing. $filters: project (hex), agent, days
     * (window over started_at), sort ∈ recent|oldest|longest|shortest. Duration
     * sorts by (ended_at - started_at); open sessions use "now" as their end, so
     * they sort by their current duration.
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

        // Sorting by duration injects "now" (?) into the ORDER BY, which is why
        // the SELECT bindings can carry one more item than the COUNT ones.
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

    /** Distinct agent_kinds, for the filter select. */
    public function agentKinds(): array
    {
        return array_map(
            static fn (object $r) => $r->agent_kind,
            $this->db->select('SELECT DISTINCT agent_kind FROM sessions ORDER BY agent_kind')
        );
    }

    /** One session by hex id, with its project and summary page (if any). */
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

    /** The session's observations in chronological order (timeline). */
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
