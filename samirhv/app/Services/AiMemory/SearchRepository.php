<?php

namespace App\Services\AiMemory;

/**
 * Full-text search over the pages using ai-memory's OWN FTS5 index (the
 * `pages_fts` table, columns title/body). Ordered by bm25 (lower is better).
 */
class SearchRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /** Hits with a highlighted snippet (the <<< >>> sentinels become <mark> in the view). */
    public function search(string $query, int $limit = 30): array
    {
        $match = $this->toMatch($query);
        if ($match === '') {
            return [];
        }

        return $this->db->select(
            "SELECT lower(hex(p.id)) AS id_hex, p.title, p.path, p.tier, pr.name AS project,
                    snippet(pages_fts, 1, '<<<', '>>>', '…', 14) AS snippet
               FROM pages_fts
               JOIN pages p ON p.rowid = pages_fts.rowid
               JOIN projects pr ON pr.id = p.project_id
              WHERE pages_fts MATCH ? AND p.is_latest = 1
              ORDER BY bm25(pages_fts)
              LIMIT ?",
            [$match, $limit]
        );
    }

    /**
     * Translate the user's query into a safe MATCH expression: each token
     * becomes a quoted phrase followed by `*` (prefix), so "oauth" finds
     * "oauth2" and "auth" finds "authentication". The quotes neutralise the
     * FTS5 syntax (AND/OR/NOT/NEAR operators, quotes) that would otherwise
     * raise a query error; several tokens combine with an implicit AND.
     */
    private function toMatch(string $query): string
    {
        preg_match_all('/[\p{L}\p{N}_\/.-]+/u', $query, $matches);
        $tokens = array_slice($matches[0] ?? [], 0, 10);
        $quoted = array_map(static fn (string $t) => '"'.str_replace('"', '""', $t).'"*', $tokens);

        return implode(' ', $quoted);
    }
}
