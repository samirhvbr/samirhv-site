<?php

namespace App\Services\GitHub;

/**
 * Filtra e ordena os repositórios candidatos do autocomplete do add-form.
 * Porte da lógica de app/controllers/suggestions_controller.rb (github-visualize):
 *   - casa a query como SUBSTRING do nome OU PREFIXO do owner ("blue3" acha os
 *     repos da org BLUE3-ISP); "owner/name" estreita os dois segmentos;
 *   - rankeia quem casa no nome acima de quem casa só no owner (estável, mantém
 *     a ordem por push-recency); owner é prefixo (não substring) pra um owner
 *     comum ("akitaonrails") não casar toda query solta ("ai");
 *   - derruba o prefixo do owner default no display.
 * Pura (sem DB/HTTP) — testável em unit test. Ver §16 do doc de paridade.
 */
class RepositorySuggestions
{
    public const LIMIT = 8;

    /**
     * @param  array<int,array{full_name: ?string, description: ?string, private: bool}>  $repos
     * @param  iterable<string>  $monitored  full_names já monitorados (case-insensitive)
     * @return array<int,array<string,mixed>>
     */
    public function build(array $repos, string $query, iterable $monitored = [], ?string $defaultOwner = null): array
    {
        $query = trim(mb_strtolower($query));

        $blocked = [];
        foreach ($monitored as $full) {
            $blocked[mb_strtolower($full)] = true;
        }

        $candidates = array_filter(
            $repos,
            fn (array $repo): bool => ! isset($blocked[mb_strtolower((string) ($repo['full_name'] ?? ''))]),
        );

        $matched = array_values(array_filter($candidates, fn (array $repo): bool => $this->matches($repo, $query)));
        $ranked = $this->rank($matched, $query);

        return array_map(
            fn (array $repo): array => $repo + ['display_name' => $this->displayName($repo, $defaultOwner)],
            array_slice($ranked, 0, self::LIMIT),
        );
    }

    /** @param  array<string,mixed>  $repo */
    private function matches(array $repo, string $query): bool
    {
        if ($query === '') {
            return true;
        }

        [$owner, $name] = $this->split($repo);

        if (str_contains($query, '/')) {
            [$queryOwner, $queryName] = array_pad(explode('/', $query, 2), 2, '');

            return str_starts_with($owner, $queryOwner) && str_contains($name, $queryName);
        }

        return str_contains($name, $query) || str_starts_with($owner, $query);
    }

    /**
     * @param  array<int,array<string,mixed>>  $repos
     * @return array<int,array<string,mixed>>
     */
    private function rank(array $repos, string $query): array
    {
        if ($query === '') {
            return $repos;
        }

        $nameQuery = str_contains($query, '/') ? explode('/', $query, 2)[1] : $query;
        $nameMatches = [];
        $ownerOnly = [];

        foreach ($repos as $repo) {
            [, $name] = $this->split($repo);
            if (str_contains($name, $nameQuery)) {
                $nameMatches[] = $repo;
            } else {
                $ownerOnly[] = $repo;
            }
        }

        return array_merge($nameMatches, $ownerOnly);
    }

    /** @param  array<string,mixed>  $repo */
    private function displayName(array $repo, ?string $defaultOwner): string
    {
        $full = (string) ($repo['full_name'] ?? '');
        [$owner] = array_pad(explode('/', $full, 2), 2, '');

        return $owner === $defaultOwner ? substr($full, strlen($owner) + 1) : $full;
    }

    /**
     * @param  array<string,mixed>  $repo
     * @return array{0: string, 1: string} owner e name em minúsculas
     */
    private function split(array $repo): array
    {
        $parts = array_pad(explode('/', mb_strtolower((string) ($repo['full_name'] ?? '')), 2), 2, '');

        return [$parts[0], $parts[1]];
    }
}
