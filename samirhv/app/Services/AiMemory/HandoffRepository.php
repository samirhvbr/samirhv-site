<?php

namespace App\Services\AiMemory;

use Illuminate\Pagination\LengthAwarePaginator;

/** Handoffs — transferências de contexto "onde paramos" entre agentes. */
class HandoffRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /** Lista, opcionalmente filtrada por estado (open|accepted|expired). */
    public function paginate(?string $state, int $perPage): LengthAwarePaginator
    {
        $where = '1 = 1';
        $bind = [];
        if ($state) {
            $where .= ' AND h.state = ?';
            $bind[] = $state;
        }

        $sql = "SELECT lower(hex(h.id)) AS id_hex, h.from_agent, h.to_agent, h.state, h.cwd, h.created_at,
                       pr.name AS project,
                       json_array_length(h.open_questions) AS open_questions,
                       json_array_length(h.next_steps) AS next_steps
                  FROM handoffs h
                  JOIN projects pr ON pr.id = h.project_id
                 WHERE {$where}
                 ORDER BY h.created_at DESC";

        return $this->db->paginate($sql, $bind, "SELECT COUNT(*) FROM handoffs h WHERE {$where}", $bind, $perPage);
    }

    /** Um handoff por id hex, com os campos JSON crus (decodificados na view). */
    public function find(string $hexId): ?object
    {
        return $this->db->selectOne(
            'SELECT lower(hex(h.id)) AS id_hex, h.from_agent, h.to_agent, h.state, h.cwd, h.summary,
                    h.open_questions, h.next_steps, h.files_touched,
                    h.created_at, h.accepted_by, h.accepted_at,
                    pr.name AS project
               FROM handoffs h
               JOIN projects pr ON pr.id = h.project_id
              WHERE lower(hex(h.id)) = ?',
            [strtolower($hexId)]
        );
    }
}
