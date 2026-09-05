<?php

namespace App\Jobs\GitHubView;

use App\Models\GitHubView\Commit;
use App\Models\GitHubView\Repository;
use App\Models\GitHubView\WorkflowRun;
use App\Services\GitHub\GitHubClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Sincroniza um repositório com o GitHub: commits (GraphQL, upsert incremental)
 * + workflow runs (REST). Porte de app/jobs/sync_repository_job.rb.
 *
 * Roda na fila OU síncrono (`SyncRepositoryJob::dispatchSync($repo)`), já que o
 * samirhv não roda `queue:work` 24/7 hoje. Ver §6 do plano de migração.
 */
class SyncRepositoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const INITIAL_COMMIT_LIMIT = 2000;

    private const WORKFLOW_RUN_LIMIT = 300;

    public function __construct(public readonly Repository $repository) {}

    public function handle(GitHubClient $client): void
    {
        $this->repository->startSync();

        // Commits são o dado essencial (o heatmap). Falha aqui → repo 'failed'.
        try {
            $this->syncCommits($client);
        } catch (\Throwable $e) {
            $this->repository->failSync($e->getMessage());

            return;
        }

        // CI é BEST-EFFORT: timeout / histórico grande de runs NÃO deve derrubar um
        // repo cujos commits já sincronizaram. Loga o aviso e segue como 'synced'.
        try {
            $this->syncWorkflowRuns($client);
        } catch (\Throwable $e) {
            Log::warning("GitHub View: CI sync falhou p/ {$this->repository->fullName()}: {$e->getMessage()}");
        }

        $this->repository->finishSync();
    }

    private function syncCommits(GitHubClient $client): void
    {
        // Incremental: só o que veio depois do último commit já salvo (+1s).
        $latest = $this->repository->commits()->max('committed_at');
        $since = $latest ? Carbon::parse($latest)->addSecond() : null;

        $fetched = 0;
        $overview = $client->repositoryOverview(
            $this->repository->owner,
            $this->repository->name,
            since: $since,
            maxCommits: self::INITIAL_COMMIT_LIMIT,
            onBatch: function (array $batch) use (&$fetched): void {
                $rows = array_map(
                    fn (array $row): array => $row + ['repository_id' => $this->repository->id],
                    $batch,
                );
                Commit::upsert($rows, ['repository_id', 'sha']);
                $fetched += count($batch);
                $this->repository->update(['sync_progress' => "{$fetched} commits fetched"]);
            },
        );

        $this->repository->update([
            'description' => $overview['description'],
            'default_branch' => $overview['default_branch'],
        ]);
    }

    private function syncWorkflowRuns(GitHubClient $client): void
    {
        $this->repository->update(['sync_progress' => 'fetching CI runs']);

        $runs = $client->workflowRuns(
            $this->repository->owner,
            $this->repository->name,
            self::WORKFLOW_RUN_LIMIT,
        );

        $rows = array_map(
            fn (array $run): array => $run + ['repository_id' => $this->repository->id],
            $runs,
        );

        if ($rows !== []) {
            WorkflowRun::upsert($rows, ['repository_id', 'github_id']);
        }
    }
}
