<?php

namespace App\Services\AiMemory;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/** Observations (each fact learned during a session), with filters. */
class ObservationRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /**
     * Filterable listing. $filters: kind, importance (minimum), project (hex),
     * days (window). Most recent first.
     */
    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        $where = '1 = 1';
        $bind = [];

        if (! empty($filters['kind'])) {
            $where .= ' AND o.kind = ?';
            $bind[] = $filters['kind'];
        }
        if (! empty($filters['importance'])) {
            $where .= ' AND o.importance >= ?';
            $bind[] = (int) $filters['importance'];
        }
        if (! empty($filters['project'])) {
            $where .= ' AND lower(hex(o.project_id)) = ?';
            $bind[] = strtolower($filters['project']);
        }
        if (! empty($filters['days'])) {
            $where .= ' AND o.created_at >= ?';
            $bind[] = Carbon::now('UTC')->subDays((int) $filters['days'])->timestamp * 1_000_000;
        }

        $sql = "SELECT lower(hex(o.id)) AS id_hex, o.kind, o.title, o.importance, o.created_at,
                       pr.name AS project, lower(hex(o.session_id)) AS session_hex
                  FROM observations o
                  JOIN projects pr ON pr.id = o.project_id
                 WHERE {$where}
                 ORDER BY o.created_at DESC";

        return $this->db->paginate($sql, $bind, "SELECT COUNT(*) FROM observations o WHERE {$where}", $bind, $perPage);
    }

    /** One observation by hex id, with its session/project. */
    public function find(string $hexId): ?object
    {
        return $this->db->selectOne(
            'SELECT lower(hex(o.id)) AS id_hex, o.kind, o.title, o.body, o.importance, o.created_at,
                    pr.name AS project, lower(hex(o.session_id)) AS session_hex, s.agent_kind
               FROM observations o
               JOIN projects pr ON pr.id = o.project_id
               LEFT JOIN sessions s ON s.id = o.session_id
              WHERE lower(hex(o.id)) = ?',
            [strtolower($hexId)]
        );
    }

    /** Distinct observation kinds, for the filter select. */
    public function kinds(): array
    {
        return array_map(
            static fn (object $r) => $r->kind,
            $this->db->select('SELECT DISTINCT kind FROM observations ORDER BY kind')
        );
    }
}
