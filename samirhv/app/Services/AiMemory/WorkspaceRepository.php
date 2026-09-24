<?php

namespace App\Services\AiMemory;

/** ai-memory workspaces, with the counts of the projects they hold. */
class WorkspaceRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /** Workspaces with counts and last activity. */
    public function all(): array
    {
        return $this->db->select(
            'SELECT lower(hex(w.id)) AS id_hex,
                    w.name,
                    (SELECT COUNT(*) FROM projects p WHERE p.workspace_id = w.id) AS projects,
                    (SELECT COUNT(*) FROM pages pg WHERE pg.workspace_id = w.id AND pg.is_latest = 1) AS pages,
                    (SELECT COUNT(*) FROM sessions s WHERE s.workspace_id = w.id) AS sessions,
                    (SELECT COUNT(*) FROM observations o
                       JOIN projects p ON p.id = o.project_id
                      WHERE p.workspace_id = w.id) AS observations,
                    (SELECT MAX(started_at) FROM sessions s WHERE s.workspace_id = w.id) AS last_session_at
               FROM workspaces w
              ORDER BY (last_session_at IS NULL), last_session_at DESC, w.name'
        );
    }
}
