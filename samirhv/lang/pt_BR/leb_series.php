<?php

/*
| As páginas da próxima série do LEB: LEB Default e LEB Security (uma página por trilha).
|
| A série é uma proposta (docs/new-series-design.md do samirhvbr/ai-benchmark, status PROPOSED), então as duas
| páginas são de status; a Default mostra também os casos até agora quando os resultados sincronizados trazem o agregado dela. Este arquivo guarda o que as duas dizem igual: o estado, os
| sistemas, como um caso roda, o que será publicado, o gabarito, a comparação e os links. `leb_default` e
| `leb_security` guardam o que cada trilha mede. Tudo o que está escrito aqui é o que o repositório público
| diz (docs/new-series-design.md, systems/, instances/LEB-D-300-A/ e instances/LEB-S-300-A/), e nada além:
| nenhum número que a série não publicou, nenhuma data que ela não marcou.
|
| O gabarito de toda instância, esta e as futuras, é publicado no GitHub junto com a instância, antes da
| primeira execução, e as máquinas de bancada não alcançam o GitHub. Nenhuma página da série chama um gabarito
| de privado, secreto ou oculto, nem diz que ele será revelado depois. A página Security não nomeia nenhuma
| vulnerabilidade plantada, nem por classe.
|
| O shvia.org renderiza este mesmo texto (tools/sync-ai-benchmark.py lê este arquivo), então uma mudança
| aqui chega aos dois sites.
*/

return [
    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB · próxima série',
    'heading_accent' => 'proposta',
    // O link para a outra trilha, acima do título.
    'sibling_note' => 'Esta é uma das duas trilhas da próxima série. A outra é :link.',
    'benchmark_link' => 'Benchmark',
    'cta_design' => 'Ler o desenho',

    'note_title' => 'Ainda não há resultados.',
    'note_body' => 'Esta página diz em que pé a série está e o que vai aparecer aqui. Nada nela é uma nota.',

    'measures_title' => 'O que esta trilha mede',
    'sides_note' => 'As notas Default e Security de uma configuração aparecem lado a lado e nunca são somadas.',

    'state_title' => 'Em que pé está a série',
    'state_body' => 'A próxima série é uma proposta: o desenho dela foi decidido em 12 de outubro de 2026, e os textos normativos (a especificação, o protocolo, a pontuação e os níveis) são escritos depois que um piloto validar o processo. Ela não muda nada no LEB-100 nem no LEB-300, cujos resultados continuam como foram publicados. Ainda não há placar.',

    'systems_title' => 'Os sistemas de referência',
    'systems_intro' => 'Cada trilha é medida em vários sistemas de referência, e cada sistema gera as duas trilhas: dois perfis de plantio do mesmo código limpo. A mesma letra é o mesmo sistema. O piloto terá 3 sistemas; o conjunto de avaliação, cerca de 6.',
    'systems_col_system' => 'Sistema',
    'systems_col_domain' => 'Domínio',
    'systems_col_stack' => 'Stack',
    'systems_col_instance' => 'Instância',
    'systems_col_status' => 'Estado',
    'system_a_name' => 'A',
    'system_a_domain' => 'Gestão de condomínio (CondoDesk)',
    'system_a_stack' => 'Java 21 · Maven · PostgreSQL',
    'system_a_status' => 'Em autoria',

    'run_title' => 'Como um caso roda',
    'run_intro' => 'Um caso é um sistema, uma trilha e uma configuração de agente.',
    'run_items' => [
        '5 execuções por caso, e todas contam.',
        'O modelo pedido fica travado no cliente, na conversa principal e em cada subagente, para que nenhum substituto possa responder.',
        'Um laboratório automático roda cada caso, da restauração da máquina ao arquivamento da evidência.',
    ],

    'publish_title' => 'O que será publicado',
    'publish_intro' => 'Para cada configuração e trilha:',
    'publish_items' => [
        'a qualidade das entregas avaliáveis, com o intervalo de incerteza;',
        'a taxa de conclusão com o modelo pedido;',
        'a nota esperada por tentativa agendada;',
        'bloqueios (no início e durante o trabalho), trocas de modelo, orçamento esgotado e falhas de laboratório, cada um contado à parte.',
    ],

    'key_title' => 'O gabarito',
    'key_body' => 'O gabarito de uma instância é publicado no GitHub junto com a instância, antes da primeira execução, e as máquinas que rodam os agentes não alcançam o GitHub. Cada execução registra também o corte de treino do modelo, para o que um modelo pode ter visto no treino.',

    'compare_title' => 'Comparação',
    'compare_body' => 'Uma nota da próxima série nunca é posta ao lado de uma do LEB-100 ou do LEB-300.',

    'links_title' => 'Para ler mais',
    'link_design' => 'O desenho da série',
    'link_system' => 'O sistema A',
    'link_instance' => 'A pasta da instância',

    // Quando os resultados sincronizados trazem o agregado da instância de uma trilha (a trilha Default desde 15 de outubro de 2026), a página diz isso e
    // mostra os casos até agora. Estas chaves substituem as de mesmo nome sem `_results`; a página Security mantém o texto de status acima.
    'note_title_results' => 'Os primeiros resultados saíram.',
    'note_body_results' => 'Duas configurações rodaram em um sistema. É a medição do piloto até agora, não um placar.',
    'state_body_results' => 'A próxima série é uma proposta: o desenho dela foi decidido em 12 de outubro de 2026, e os textos normativos (a especificação, o protocolo, a pontuação e os níveis) são escritos depois que um piloto validar o processo. O piloto começou no sistema A, e os casos dele até agora estão acima. Ela não muda nada no LEB-100 nem no LEB-300, cujos resultados continuam como foram publicados. Ainda não há placar: ele pede mais configurações e mais sistemas.',
    'system_a_status_results' => 'Casos do piloto em andamento',

    'results_title' => 'Os casos até agora',
    'results_intro' => 'Cada configuração roda no sistema A, com :minutes minutos no máximo por execução. Enquanto uma instância está ativa só o agregado dela é publicado: nenhum resultado por falha, nenhum veredito, nenhuma entrega.',
    'results_case_ids' => ':instance v:version · trilha :track · :runs execuções',
    'results_col_estimate' => 'Estimativa',
    'results_col_interval' => 'Intervalo de 95%',
    'results_col_grade' => 'Grau',
    'results_col_runs' => 'Nota de cada execução',
    'results_completion' => ':completed de :scheduled tentativas agendadas terminaram com o modelo pedido. Sistemas em que todas as tentativas terminaram: :all de :systems.',
    'results_expected' => 'Nota esperada por tentativa agendada: :mean (:low a :high).',
    'results_outcomes' => 'Bloqueadas: :blocked (no início :start, durante o trabalho :work). Orçamento esgotado: :budget. Trocas de modelo: :switched. Perdidas para o laboratório ou o operador, refeitas: :lab.',
    'results_cost' => 'Por execução, em média: US$ :cost e :minutes minutos.',
    'results_method' => 'A estimativa é a média das execuções terminadas, em 1000. O intervalo é um bootstrap por percentis de 95% sobre sistemas e execuções; com um sistema só, ele reflete apenas a variação entre as execuções desse sistema. As colunas de categoria mostram a média das execuções contra o máximo que a categoria pode dar.',
    'results_key' => 'SHA-256 do gabarito (matriz) :matrix · SHA-256 do pacote :package.',
];
