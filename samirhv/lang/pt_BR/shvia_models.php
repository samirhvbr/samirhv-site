<?php

/*
| The "models behind it" section, shown only on /p/shvia.
| Values are Portuguese: end-user copy is the carve-out in this repository's
| English-only rule.
*/

return [
    'title' => 'Os modelos por trás',
    'lead' => 'A ShvIA é :hybrid. Por padrão, responde com modelos :local, rodando dentro da Blue3 — o prompt não sai da rede. Quando o trabalho pede um modelo de fronteira, você conecta um provedor de :cloud com a sua própria chave.',
    'hybrid' => 'híbrida',
    'local' => 'locais',
    'cloud' => 'nuvem',

    'onprem_label' => 'Local · on-prem',
    'onprem_pill' => 'o prompt não sai da Blue3',
    'onprem_sub' => 'Modelos da Blue3 servidos por :ollama na infraestrutura interna — sem custo por token, sem dado saindo da rede.',

    'cloud_label' => 'Nuvem · opcional',
    'cloud_note' => 'com a sua própria chave (BYOK)',
    'cloud_more' => '+ outros',
    'cloud_caption' => 'Ao usar nuvem, o :data_leaves — sempre com mascaramento de PII para a LGPD, e só aparece pra quem configura a própria chave.',
    'data_leaves' => 'dado sai da Blue3',
];
