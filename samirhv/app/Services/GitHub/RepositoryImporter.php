<?php

namespace App\Services\GitHub;

use App\Models\GitHubView\Repository;

/**
 * Traz para o monitor todos os repositórios que o token alcança, como
 * PENDENTES — sem sincronizar. Sincronizar dezenas de repositórios de uma vez
 * seria pesado e comeria o rate limit; depois cada um é sincronizado sob
 * demanda.
 *
 * Idempotente: rodar de novo não duplica nada, só reporta quantos já existiam.
 *
 * Isto morava dentro do GitHubViewController como trinta e cinco linhas de
 * laço. É um caso de uso — decidir o que importar, contar o resultado — e não
 * tratamento de request, então mora aqui, onde pode ser lido e testado sem um
 * HTTP kernel em volta.
 */
class RepositoryImporter
{
    /**
     * @param  iterable<int, array<string, mixed>>  $repos  como vêm de GitHubClient::userRepositories()
     * @return array{created: int, skipped: int}
     */
    public function import(iterable $repos): array
    {
        $created = 0;
        $skipped = 0;

        foreach ($repos as $repo) {
            $fullName = (string) ($repo['full_name'] ?? '');

            // Sem "owner/name" não há o que identificar; a linha é descartada
            // em silêncio porque é dado de terceiro, não erro nosso.
            if (! str_contains($fullName, '/')) {
                continue;
            }

            [$owner, $name] = explode('/', $fullName, 2);

            $model = Repository::firstOrNew(['owner' => $owner, 'name' => $name]);

            if ($model->exists) {
                $skipped++;

                continue;
            }

            $model->fill([
                'description' => $repo['description'] ?? null,
                'sync_status' => 'pending',
            ])->save();

            $created++;
        }

        return ['created' => $created, 'skipped' => $skipped];
    }
}
