<?php

namespace App\Services\AiMemory;

/** Projetos do ai-memory (com contagens) e opções para selects de filtro. */
class ProjectRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /** Lista de projetos com workspace + contagens + última atividade. */
    public function all(): array
    {
        return $this->db->select(
            'SELECT lower(hex(p.id)) AS id_hex,
                    p.name,
                    p.repo_path,
                    w.name AS workspace,
                    p.created_at,
                    (SELECT COUNT(*) FROM pages pg WHERE pg.project_id = p.id AND pg.is_latest = 1) AS pages,
                    (SELECT COUNT(*) FROM sessions s WHERE s.project_id = p.id) AS sessions,
                    (SELECT COUNT(*) FROM observations o WHERE o.project_id = p.id) AS observations,
                    (SELECT MAX(started_at) FROM sessions s WHERE s.project_id = p.id) AS last_session_at
               FROM projects p
               JOIN workspaces w ON w.id = p.workspace_id
              ORDER BY (last_session_at IS NULL), last_session_at DESC, p.name'
        );
    }

    /** Um projeto por id (hex) com contagens. */
    public function find(string $hexId): ?object
    {
        return $this->db->selectOne(
            'SELECT lower(hex(p.id)) AS id_hex,
                    p.name,
                    p.repo_path,
                    w.name AS workspace,
                    p.created_at,
                    (SELECT COUNT(*) FROM pages pg WHERE pg.project_id = p.id AND pg.is_latest = 1) AS pages,
                    (SELECT COUNT(*) FROM sessions s WHERE s.project_id = p.id) AS sessions,
                    (SELECT COUNT(*) FROM observations o WHERE o.project_id = p.id) AS observations
               FROM projects p
               JOIN workspaces w ON w.id = p.workspace_id
              WHERE lower(hex(p.id)) = ?',
            [strtolower($hexId)]
        );
    }

    /** [{id_hex, name}] para popular selects de filtro. */
    public function options(): array
    {
        return $this->db->select('SELECT lower(hex(id)) AS id_hex, name FROM projects ORDER BY name');
    }
}
