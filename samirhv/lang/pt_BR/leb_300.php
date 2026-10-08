<?php

/*
| A página do LEB-300: o segundo nível do LEB existe e ainda não tem resultados.
|
| Escrita à mão e de propósito enxuta. Enquanto o LEB-300 não tiver resultados
| publicados, ela diz o que existe e nada sobre a instância em si: nem pacote,
| nem lista de defeitos, nem gabarito. Quando um agregado for publicado, os
| números virão do arquivo de resultados sincronizado, como na página do
| LEB-100, e este texto muda junto.
*/

return [
    'title' => 'LEB-300 · AI Benchmark',
    'meta_description' => 'O LEB-300 é o próximo nível do benchmark de engenharia LEB, uma aplicação de cerca de 3.000 linhas. Está em preparação: ainda não há resultados publicados.',

    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'LEB-300',
    'heading_accent' => 'em preparação',

    'note_title' => 'Ainda não há resultados do LEB-300.',
    'note_body' => 'Esta página diz em que pé ele está e o que vai aparecer aqui. Nada nela é uma nota.',

    'what_title' => 'O que é',
    'what_body' => 'O LEB é o LLM Engineering Benchmark: um agente recebe um sistema funcionando com defeitos plantados e é avaliado pelo que encontra, explica e corrige sem quebrar o que já funcionava. O LEB-100 é o primeiro nível, uma aplicação de cerca de 300 linhas. O LEB-300 é o nível acima, uma aplicação de cerca de 3.000 linhas.',

    'stands_title' => 'Em que pé está',
    'stands_body' => 'A instância está em preparação. As primeiras execuções são internas e servem para conferir o procedimento, por isso não são publicadas.',

    'publish_title' => 'O que será publicado',
    'publish_body' => 'Quando houver resultados, esta página mostrará uma linha por agente: o total, a nota, a pontuação em cada categoria, em quantas execuções ela se baseia, e o custo e o tempo. Não mostrará a lista de defeitos plantados, o código avaliado nem o gabarito. A mesma instância precisa continuar medindo agentes novos, então isso fica privado.',

    'more_title' => 'Onde ler mais',
    'more_body' => 'O protocolo, a pontuação e as ferramentas são públicos no :repo. Os resultados do primeiro nível estão na :leb100.',
    'repo_link' => 'repositório ai-benchmark',
    'leb100_link' => 'página do LEB-100',
];
