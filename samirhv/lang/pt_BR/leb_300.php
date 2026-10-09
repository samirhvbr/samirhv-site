<?php

/*
| A página do LEB-300: o segundo nível do LEB.
|
| Montada como a página do LEB-100 (um hero com os líderes, os resultados com os fatos, os filtros e o
| placar, depois as ressalvas), e de propósito enxuta no que diz da instância. O LEB-300 é uma instância
| ATIVA: a página publica só o agregado (o total, o selo e a pontuação por categoria de cada agente, com
| custo e tempo), nunca um defeito plantado, um veredito, o código avaliado ou o gabarito. Os números nunca
| são escritos aqui: vêm do arquivo de resultados sincronizado (App\Support\AiBenchmark::aggregate).
| Enquanto o arquivo não traz um agregado a página é de status, e as chaves `*_result` são as mesmas
| seções depois que ele traz. O shvia.org renderiza este mesmo texto (tools/sync-ai-benchmark.py lê
| este arquivo), então uma mudança aqui chega aos dois sites.
*/

return [
    'title' => 'LEB-300 · AI Benchmark',
    'meta_description' => 'O LEB-300 é o próximo nível do benchmark de engenharia LEB, uma aplicação de cerca de 3.000 linhas. Está em preparação: ainda não há resultados publicados.',
    'meta_description_result' => 'O LEB-300 é o próximo nível do benchmark de engenharia LEB, uma aplicação de cerca de 3.000 linhas. Um piloto exploratório: os resultados agregados até agora estão publicados.',

    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'LEB-300',
    'heading_accent' => 'em preparação',
    'heading_accent_result' => 'piloto exploratório',
    // A nota acima do título, gêmea da que a página do LEB-100 traz sobre este nível.
    'leb100_note' => 'O primeiro nível, :link, é a instância de referência: todo agente avaliado até aqui rodou nele, e toda execução é publicada.',
    'leb100_note_link' => 'LEB-100',
    'lead' => 'O segundo nível do LEB: uma aplicação de cerca de 3.000 linhas, em vários arquivos, cujas falhas atravessam arquivos. A instância é ativa, então o gabarito fica privado e só o agregado de cada agente é publicado. Como um run funciona, como é pontuado e no que este nível difere do LEB-100 está na página :benchmark.',

    'note_title' => 'Ainda não há resultados do LEB-300.',
    'note_body' => 'Esta página diz em que pé ele está e o que vai aparecer aqui. Nada nela é uma nota.',

    'result_title' => 'Os resultados até agora',
    'results_intro' => 'Uma linha por agente: o total, a nota, a pontuação em cada categoria, em quantas execuções ela se baseia, e o custo e o tempo. Todos os agentes resolveram o mesmo pacote, byte a byte, então os números se comparam em pé de igualdade.',
    // A instância não tem descrição pública: é ativa, então a página diz dela o que o nível diz.
    'instance_name' => 'Uma aplicação de cerca de 3.000 linhas',
    'result_note_title' => 'Um piloto exploratório.',
    'result_note_body' => 'A nota oficial é a mediana de três execuções de um agente. Uma linha que se apoia em menos execuções não é oficial: ela diz quantas tem, e é listada do mesmo jeito, como na página do LEB-100. A instância ainda é um piloto: a dificuldade dela não foi homologada. Só o agregado é publicado: os defeitos plantados, o veredito, o código avaliado e o gabarito ficam privados.',
    'result_note_body_three' => 'Cada nota é a mediana de três execuções de um agente, como o protocolo pede. A instância ainda é um piloto: a dificuldade dela não foi homologada. Só o agregado é publicado: os defeitos plantados, o veredito, o código avaliado e o gabarito ficam privados.',
    'facts_key' => 'gabarito privado',
    'session_time' => 'Sessão de :min min',
    'more_note' => 'Uma leitura escrita do agregado; não faz parte da nota e não cita nenhuma falha.',
    'session_time_help' => 'Da primeira mensagem ao fim da sessão, sem os minutos que o operador levou entre as duas etapas.',

    'stands_body' => 'A instância está em preparação. As primeiras execuções são internas e servem para conferir o procedimento, por isso não são publicadas.',
    'stands_body_result' => 'A instância está em piloto exploratório. Os resultados acima são os publicados até agora, e outros agentes virão. As primeiras execuções serviram também para conferir o procedimento.',

    'caveats_title' => 'O que o registro não tem',
    'caveat_checkpoint' => 'Não foi feito o checkpoint entre as duas etapas da tarefa em nenhuma das execuções, então não consta no registro que o modelo foi o mesmo durante a primeira etapa. As transcrições mostram um único modelo.',
    'caveat_matrix' => 'O texto da tarefa que o agente leu cita o hash da matriz de pontuação anterior. A matriz com que esta execução é pontuada difere dela só por um campo do cabeçalho que marca a instância como ativa. A pontuação é a mesma.',
    'caveat_client' => 'Os agentes não rodaram todos no mesmo cliente, o Claude Code ou o OpenCode, cada um com as suas ferramentas. O cliente de cada execução consta no registro dela.',

    'publish_title' => 'O que será publicado',
    'publish_body' => 'Quando houver resultados, esta página mostrará uma linha por agente: o total, a nota, a pontuação em cada categoria, em quantas execuções ela se baseia, e o custo e o tempo. Não mostrará a lista de defeitos plantados, o código avaliado nem o gabarito. A mesma instância precisa continuar medindo agentes novos, então isso fica privado.',
    'publish_title_result' => 'O que é publicado',
    'publish_body_result' => 'Esta página mostra uma linha por agente: o total, a nota, a pontuação em cada categoria, em quantas execuções ela se baseia, e o custo e o tempo. Nunca mostra a lista de defeitos plantados, o código avaliado nem o gabarito. A mesma instância precisa continuar medindo agentes novos, então isso fica privado.',

    'more_body' => 'O protocolo, a pontuação e as ferramentas são públicos no :repo. Como um run funciona e no que os dois níveis diferem está na página :benchmark, e os resultados do primeiro nível estão na :leb100.',
    'repo_link' => 'repositório ai-benchmark',
    'benchmark_link' => 'Benchmark',
    'leb100_link' => 'página do LEB-100',
    'leb100_results' => 'Resultados do LEB-100',
];
