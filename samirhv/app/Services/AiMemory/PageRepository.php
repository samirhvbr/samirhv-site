<?php

namespace App\Services\AiMemory;

use Illuminate\Pagination\LengthAwarePaginator;

/** Páginas (wiki consolidada) do ai-memory: lista, leitura e histórico. */
class PageRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /** Páginas na versão atual (is_latest), opcionalmente de um projeto. */
    public function paginate(?string $projectHex, int $perPage): LengthAwarePaginator
    {
        $where = 'p.is_latest = 1';
        $bind = [];
        if ($projectHex) {
            $where .= ' AND lower(hex(p.project_id)) = ?';
            $bind[] = strtolower($projectHex);
        }

        $sql = "SELECT lower(hex(p.id)) AS id_hex, p.title, p.path, p.tier, p.pinned, p.updated_at,
                       pr.name AS project
                  FROM pages p
                  JOIN projects pr ON pr.id = p.project_id
                 WHERE {$where}
                 ORDER BY p.pinned DESC, p.updated_at DESC";

        return $this->db->paginate($sql, $bind, "SELECT COUNT(*) FROM pages p WHERE {$where}", $bind, $perPage);
    }

    /** Uma versão específica de página (qualquer, não só a atual) por id hex. */
    public function find(string $hexId): ?object
    {
        return $this->db->selectOne(
            'SELECT lower(hex(p.id)) AS id_hex, p.title, p.path, p.tier, p.body, p.frontmatter_json,
                    p.is_latest, p.pinned, p.created_at, p.updated_at,
                    lower(hex(p.workspace_id)) AS workspace_hex,
                    lower(hex(p.project_id)) AS project_hex,
                    lower(hex(p.supersedes)) AS supersedes_hex,
                    pr.name AS project, w.name AS workspace, u.username AS author
               FROM pages p
               JOIN projects pr ON pr.id = p.project_id
               JOIN workspaces w ON w.id = p.workspace_id
               LEFT JOIN users u ON u.id = p.author_id
              WHERE lower(hex(p.id)) = ?',
            [strtolower($hexId)]
        );
    }

    /**
     * Todas as versões da mesma (workspace, project, path) — atual → antigas —
     * a "linha do tempo" da página via supersedes.
     */
    public function history(object $page): array
    {
        return $this->db->select(
            'SELECT lower(hex(id)) AS id_hex, title, is_latest, created_at, updated_at
               FROM pages
              WHERE lower(hex(workspace_id)) = ?
                AND lower(hex(project_id)) = ?
                AND path = ?
              ORDER BY created_at DESC',
            [$page->workspace_hex, $page->project_hex, $page->path]
        );
    }
}
