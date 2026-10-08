<?php

/*
| A página do LEB-300: o segundo nível do LEB.
|
| Escrita à mão e de propósito enxuta. O LEB-300 é uma instância ATIVA: a página diz o que existe e
| publica só o agregado (o total, o selo e a pontuação por categoria de cada agente, com custo e
| tempo), nunca um defeito plantado, um veredito, o código avaliado ou o gabarito. Os números nunca
| são escritos aqui: vêm do arquivo de resultados sincronizado (App\Support\AiBenchmark::aggregate).
| Enquanto o arquivo não traz um agregado a página é de status, e as chaves `*_result` são as mesmas
| seções depois que ele traz. O shvia.org renderiza este mesmo texto (tools/sync-ai-benchmark.py lê
| este arquivo), então uma mudança aqui chega aos dois sites.
*/

return [
    'title' => 'LEB-300 · AI Benchmark',
    'meta_description' => 'O LEB-300 é o próximo nível do benchmark de engenharia LEB, uma aplicação de cerca de 3.000 linhas. Está em preparação: ainda não há resultados publicados.',
    'meta_description_result' => 'O LEB-300 é o próximo nível do benchmark de engenharia LEB, uma aplicação de cerca de 3.000 linhas. Um piloto exploratório: o primeiro resultado agregado está publicado.',

    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'LEB-300',
    'heading_accent' => 'em preparação',
    'heading_accent_result' => 'piloto exploratório',

    'note_title' => 'Ainda não há resultados do LEB-300.',
    'note_body' => 'Esta página diz em que pé ele está e o que vai aparecer aqui. Nada nela é uma nota.',

    'result_title' => 'O primeiro resultado',
    'result_note_title' => 'Um piloto exploratório.',
    'result_note_body' => 'É uma execução de um agente. A nota oficial é a mediana de três execuções de um agente, então esta não é oficial. Só o agregado é publicado: os defeitos plantados, o veredito, o código avaliado e o gabarito ficam privados.',
    'result_note_body_three' => 'É a mediana de três execuções de um agente, como o protocolo pede. A instância ainda é um piloto: a dificuldade dela não foi homologada. Só o agregado é publicado: os defeitos plantados, o veredito, o código avaliado e o gabarito ficam privados.',
    'session_time' => 'Sessão de :min min',
    'session_time_help' => 'Da primeira mensagem ao fim da sessão, como consta no registro da execução.',

    'stands_title' => 'Em que pé está',
    'stands_body' => 'A instância está em preparação. As primeiras execuções são internas e servem para conferir o procedimento, por isso não são publicadas.',
    'stands_body_result' => 'A instância está em piloto exploratório. O resultado acima é o primeiro publicado, e outros agentes virão. As primeiras execuções serviram também para conferir o procedimento.',

    'caveats_title' => 'O que o registro não tem',
    'caveat_checkpoint' => 'Não foi feito o checkpoint entre as duas etapas da tarefa em nenhuma das execuções, então não consta no registro que o modelo foi o mesmo durante a primeira etapa. As transcrições mostram um único modelo.',
    'caveat_matrix' => 'O texto da tarefa que o agente leu cita o hash da matriz de pontuação anterior. A matriz com que esta execução é pontuada difere dela só por um campo do cabeçalho que marca a instância como ativa. A pontuação é a mesma.',

    'publish_title' => 'O que será publicado',
    'publish_body' => 'Quando houver resultados, esta página mostrará uma linha por agente: o total, a nota, a pontuação em cada categoria, em quantas execuções ela se baseia, e o custo e o tempo. Não mostrará a lista de defeitos plantados, o código avaliado nem o gabarito. A mesma instância precisa continuar medindo agentes novos, então isso fica privado.',
    'publish_title_result' => 'O que é publicado',
    'publish_body_result' => 'Esta página mostra uma linha por agente: o total, a nota, a pontuação em cada categoria, em quantas execuções ela se baseia, e o custo e o tempo. Nunca mostra a lista de defeitos plantados, o código avaliado nem o gabarito. A mesma instância precisa continuar medindo agentes novos, então isso fica privado.',

    'more_title' => 'Onde ler mais',
    'more_body' => 'O protocolo, a pontuação e as ferramentas são públicos no :repo. Como um run funciona e no que os dois níveis diferem está na página :benchmark, e os resultados do primeiro nível estão na :leb100.',
    'repo_link' => 'repositório ai-benchmark',
    'benchmark_link' => 'Benchmark',
    'leb100_link' => 'página do LEB-100',
];
