<?php

/*
| A página do LEB Default: a trilha de manutenção do dia a dia da próxima série do LEB.
|
| Uma página de status; quando os resultados sincronizados trazem o agregado da trilha, ela mostra também o primeiro caso. O que as duas páginas de trilha dizem igual está em
| `leb_series`; este arquivo guarda só o que a trilha Default mede, como o desenho (docs/new-series-design.md
| §4.1) e a instância (instances/LEB-D-300-A/README.md) dizem. O shvia.org renderiza este mesmo texto
| (tools/sync-ai-benchmark.py).
*/

return [
    'title' => 'LEB Default · AI Benchmark',
    'meta_description' => 'O LEB Default é uma das duas trilhas da próxima série do LEB: a manutenção do dia a dia de um sistema que funciona, sem vulnerabilidade plantada. Uma proposta: ainda não há resultados publicados.',
    'heading' => 'LEB Default',
    'lead' => 'Uma das duas trilhas da próxima série do LEB: a manutenção do dia a dia de um sistema que funciona. O agente trabalha num sistema com falhas de manutenção plantadas, e nada na tarefa pede trabalho de segurança. Como o LEB funciona hoje, e no que os níveis dele diferem, está na página :benchmark.',
    'sibling_label' => 'LEB Security',

    'measures_body' => 'Arquitetura, bugs, desempenho, qualidade de código e compatibilidade. Nenhuma vulnerabilidade é plantada, e a tarefa não pede trabalho de segurança.',
    'measures_base' => 'O código de que ela parte é descrito como "nenhuma vulnerabilidade conhecida no escopo revisado", nunca como livre delas.',

    'meta_description_result' => 'LEB Default, uma trilha da próxima série do LEB: a manutenção do dia a dia de um sistema que funciona, sem vulnerabilidade plantada. Proposta em piloto; o primeiro caso está publicado como agregado.',
];
