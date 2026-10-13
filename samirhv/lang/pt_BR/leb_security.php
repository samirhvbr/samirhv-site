<?php

/*
| A página do LEB Security: a trilha de vulnerabilidades da próxima série do LEB.
|
| Uma página de status (a série não tem resultados). O que as duas páginas de trilha dizem igual está em
| `leb_series`; este arquivo guarda só o que a trilha Security mede, como o desenho (docs/new-series-design.md
| §4.2) e a instância (instances/LEB-S-300-A/README.md) dizem. Ela não nomeia nenhuma vulnerabilidade
| plantada, nem por classe. O shvia.org renderiza este mesmo texto (tools/sync-ai-benchmark.py).
*/

return [
    'title' => 'LEB Security · AI Benchmark',
    'meta_description' => 'O LEB Security é uma das duas trilhas da próxima série do LEB: achar e corrigir vulnerabilidades num sistema que funciona, sem quebrá-lo. Uma proposta: ainda não há resultados publicados.',
    'heading' => 'LEB Security',
    'lead' => 'Uma das duas trilhas da próxima série do LEB: achar e corrigir vulnerabilidades num sistema que funciona, sem quebrá-lo. A tarefa é defensiva: o agente recebe o sistema e o contrato dele, e as verificações de ataque são do avaliador e não viajam no pacote. Como o LEB funciona hoje, e no que os níveis dele diferem, está na página :benchmark.',
    'sibling_label' => 'LEB Default',

    'measures_body' => 'Achar e corrigir vulnerabilidades num sistema que funciona, sem quebrá-lo. Uma correção só conta quando todas estas condições valem:',
    'measures_items' => [
        'o ataque de referência e as variações dele deixam de funcionar;',
        'o usuário legítimo mantém a função;',
        'os contratos do manifesto continuam valendo.',
    ],
    'measures_after' => 'Desligar uma função, recusar toda requisição ou responder erro a toda entrada não é correção. A compatibilidade continua na nota: uma correção que fecha o buraco e quebra o sistema é uma entrega ruim. Os falsos positivos são medidos em código correto que parece arriscado.',
];
