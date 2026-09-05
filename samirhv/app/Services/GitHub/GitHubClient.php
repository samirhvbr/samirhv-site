<?php

namespace App\Services\GitHub;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * Cliente da API do GitHub para o GitHub View. Porte de
 * app/services/github/client.rb (github-visualize):
 *   - histórico de commits via GraphQL (additions/deletions em lote, paginado);
 *   - workflow runs (CI) e repos do usuário via REST.
 * Token: config('services.github.token') — fine-grained, Contents:read +
 * Actions:read. Ver .continue/migracao-github-visualize.md (§5).
 */
class GitHubClient
{
    private const GRAPHQL_URL = 'https://api.github.com/graphql';

    private const REST_URL = 'https://api.github.com';

    private const PAGE_SIZE = 100;

    /**
     * Quais repos o /user/repos traz p/ o autocomplete: tudo que o token alcança
     * — próprios, colaborações e organizações — pra descobrir repos de org (ex.:
     * BLUE3-ISP), não só os pessoais. Espelha REPO_AFFILIATION do client.rb.
     */
    private const REPO_AFFILIATION = 'owner,collaborator,organization_member';

    /** Mesma query do Rails (HISTORY_QUERY). Nowdoc: NÃO interpolar ($ são vars GraphQL). */
    private const HISTORY_QUERY = <<<'GRAPHQL'
    query($owner: String!, $name: String!, $since: GitTimestamp, $cursor: String, $pageSize: Int!) {
      repository(owner: $owner, name: $name) {
        description
        defaultBranchRef {
          name
          target {
            ... on Commit {
              history(first: $pageSize, since: $since, after: $cursor) {
                pageInfo { hasNextPage endCursor }
                nodes {
                  oid
                  messageHeadline
                  committedDate
                  additions
                  deletions
                  author { user { login } name }
                }
              }
            }
          }
        }
      }
    }
    GRAPHQL;

    private string $token;

    public function __construct(?string $token = null)
    {
        $token ??= (string) config('services.github.token');

        if ($token === '') {
            throw new MissingTokenException('GITHUB_TOKEN não configurado (config/services.php › github.token).');
        }

        $this->token = $token;
    }

    /**
     * Overview do repo + histórico de commits (paginado). Se $onBatch for passado,
     * é chamado a cada página com o lote já no formato de linha do DB — permite o
     * upsert incremental (como o `yield` do Rails).
     *
     * @param  callable(array<int,array<string,mixed>>): void|null  $onBatch
     * @return array{description: ?string, default_branch: ?string, commits: array<int,array<string,mixed>>}
     */
    public function repositoryOverview(
        string $owner,
        string $name,
        ?Carbon $since = null,
        int $maxCommits = 1000,
        ?callable $onBatch = null,
    ): array {
        $commits = [];
        $cursor = null;
        $description = null;
        $defaultBranch = null;

        do {
            $data = $this->graphql([
                'owner' => $owner,
                'name' => $name,
                'since' => $since?->toIso8601String(),
                'cursor' => $cursor,
                'pageSize' => self::PAGE_SIZE,
            ]);

            $repo = $data['repository'] ?? null;
            if ($repo === null) {
                throw new NotFoundException("{$owner}/{$name} não encontrado.");
            }

            $description = $repo['description'] ?? null;
            $branchRef = $repo['defaultBranchRef'] ?? null;
            if ($branchRef === null) {
                break; // repo sem branch default / sem commits
            }
            $defaultBranch = $branchRef['name'] ?? null;

            $history = $branchRef['target']['history'] ?? null;
            if ($history === null) {
                break;
            }

            $pageCommits = array_map(
                fn (array $node): array => $this->commitAttributes($node),
                $history['nodes'] ?? [],
            );

            if ($onBatch !== null && $pageCommits !== []) {
                $onBatch($pageCommits);
            }
            $commits = array_merge($commits, $pageCommits);

            $page = $history['pageInfo'] ?? ['hasNextPage' => false, 'endCursor' => null];
            $cursor = $page['endCursor'] ?? null;
            $hasNext = ($page['hasNextPage'] ?? false) && count($commits) < $maxCommits;
        } while ($hasNext);

        return [
            'description' => $description,
            'default_branch' => $defaultBranch,
            'commits' => array_slice($commits, 0, $maxCommits),
        ];
    }

    /** @return array<int,array<string,mixed>> */
    public function workflowRuns(string $owner, string $name, int $maxRuns = 300): array
    {
        $runs = [];
        $page = 1;

        while (count($runs) < $maxRuns) {
            $body = $this->restGet("/repos/{$owner}/{$name}/actions/runs", [
                'per_page' => self::PAGE_SIZE,
                'page' => $page,
                'exclude_pull_requests' => 'true', // encolhe MUITO o payload (evita timeout)
            ]);
            $batch = $body['workflow_runs'] ?? [];
            if ($batch === []) {
                break;
            }
            foreach ($batch as $run) {
                $runs[] = $this->workflowRunAttributes($run);
            }
            if (count($batch) < self::PAGE_SIZE) {
                break;
            }
            $page++;
        }

        return array_slice($runs, 0, $maxRuns);
    }

    /**
     * Repos que o usuário autenticado alcança (próprios, colaborações e orgs —
     * ver REPO_AFFILIATION), p/ o autocomplete do add-form. Mais recentes primeiro.
     *
     * @return array<int,array{full_name: ?string, description: ?string, private: bool}>
     */
    public function userRepositories(int $maxRepos = 300): array
    {
        $repos = [];
        $page = 1;

        while (count($repos) < $maxRepos) {
            $body = $this->restGet('/user/repos', [
                'per_page' => self::PAGE_SIZE,
                'page' => $page,
                'affiliation' => self::REPO_AFFILIATION,
                'sort' => 'pushed',
            ]);
            if ($body === []) {
                break;
            }
            foreach ($body as $repo) {
                $repos[] = [
                    'full_name' => $repo['full_name'] ?? null,
                    'description' => $repo['description'] ?? null,
                    'private' => (bool) ($repo['private'] ?? false),
                ];
            }
            if (count($body) < self::PAGE_SIZE) {
                break;
            }
            $page++;
        }

        return array_slice($repos, 0, $maxRepos);
    }

    public function authenticatedLogin(): string
    {
        return (string) ($this->restGet('/user')['login'] ?? '');
    }

    // ── internals ────────────────────────────────────────────────────────────

    /**
     * @param  array<string,mixed>  $variables
     * @return array<string,mixed>
     */
    private function graphql(array $variables): array
    {
        $response = $this->client()->post(self::GRAPHQL_URL, [
            'query' => self::HISTORY_QUERY,
            'variables' => $variables,
        ]);

        if ($response->failed()) {
            throw new GitHubException("GraphQL falhou (HTTP {$response->status()}).");
        }

        $json = $response->json();
        if (! empty($json['errors'])) {
            $message = $json['errors'][0]['message'] ?? 'erro desconhecido';
            throw new GitHubException("GraphQL: {$message}");
        }

        return $json['data'] ?? [];
    }

    /**
     * @param  array<string,mixed>  $query
     * @return array<mixed>
     */
    private function restGet(string $path, array $query = []): array
    {
        $response = $this->client()->get(self::REST_URL.$path, $query);

        if ($response->status() === 404) {
            throw new NotFoundException("{$path} não encontrado (404).");
        }
        if ($response->failed()) {
            throw new GitHubException("REST {$path} falhou (HTTP {$response->status()}).");
        }

        return $response->json() ?? [];
    }

    private function client(): PendingRequest
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->withHeaders(['User-Agent' => 'samirhv-github-view'])
            ->connectTimeout(10)
            ->timeout(45)
            ->retry(2, 1000); // 1 retry em falha transiente (timeout/5xx)
    }

    /**
     * @param  array<string,mixed>  $node
     * @return array<string,mixed>
     */
    private function commitAttributes(array $node): array
    {
        return [
            'sha' => $node['oid'] ?? null,
            'message' => $node['messageHeadline'] ?? null,
            'author_login' => $node['author']['user']['login'] ?? ($node['author']['name'] ?? null),
            'committed_at' => $this->toDbDate($node['committedDate'] ?? null),
            'additions' => (int) ($node['additions'] ?? 0),
            'deletions' => (int) ($node['deletions'] ?? 0),
        ];
    }

    /**
     * @param  array<string,mixed>  $run
     * @return array<string,mixed>
     */
    private function workflowRunAttributes(array $run): array
    {
        return [
            'github_id' => $run['id'] ?? null,
            'workflow_name' => $run['name'] ?? null,
            'run_number' => $run['run_number'] ?? null,
            'status' => $run['status'] ?? null,
            'conclusion' => $run['conclusion'] ?? null,
            'branch' => $run['head_branch'] ?? null,
            'run_started_at' => $this->toDbDate($run['run_started_at'] ?? ($run['created_at'] ?? null)),
        ];
    }

    /**
     * ISO 8601 (ex.: "2026-07-10T21:38:16Z") → "Y-m-d H:i:s" em UTC. Necessário
     * porque `upsert()` do Eloquent BYPASSA o cast do model — a string crua iria
     * direto pro MySQL, que recusa o formato ISO (SQLite do original aceitava).
     */
    private function toDbDate(?string $iso): ?string
    {
        return $iso ? Carbon::parse($iso)->utc()->format('Y-m-d H:i:s') : null;
    }
}
