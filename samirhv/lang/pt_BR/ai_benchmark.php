<?php

/*
| The AI Benchmark page, Portuguese. Keys mirror lang/en/ai_benchmark.php,
| which is the source of the key set.
*/

return [
    'title' => 'AI Benchmark',
    'meta_description' => 'LEB, o LLM Engineering Benchmark: um agente de IA consegue evoluir um sistema legado sem quebrá-lo? O método e os resultados até agora.',

    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'Uma IA consegue manter código legado',
    'heading_accent' => 'sem quebrar nada?',
    'lead' => 'O LEB — LLM Engineering Benchmark — entrega a um agente de IA um sistema legado em produção, com falhas plantadas e consumidores que dependem de como ele se comporta hoje. Ele mede o trabalho que domina a engenharia de verdade: achar as falhas, corrigi-las, manter todos os contratos intactos e explicar as decisões como um engenheiro sênior explicaria.',
    'cta_results' => 'Ver os resultados',
    'cta_source' => 'Método no GitHub',

    'why_title' => 'Por que mais um benchmark',
    'why_body' => 'A maioria dos benchmarks mede código escrito do zero, ou uma issue isolada resolvida. Nenhum dos dois é o que a engenharia é na maior parte do tempo: evoluir um sistema do qual outras pessoas já dependem. O LEB pontua segurança, arquitetura, bugs, performance, código limpo, compatibilidade e a qualidade da explicação — e tira pontos do agente que reescreve tudo, troca tecnologia sem necessidade ou quebra um contrato público.',
    'why_quote' => 'Reescrever do zero não é engenharia. É fuga.',

    'how_title' => 'Como funciona um run',
    'step1_title' => 'Um sistema legado, com falhas plantadas',
    'step1_desc' => 'O agente recebe o código, um manifesto da sua superfície pública — o contrato — e uma tarefa neutra: reportar os problemas, corrigir o que deve ser corrigido, manter a compatibilidade e justificar cada decisão. Ele nunca sabe quais falhas existem, quantas são nem onde estão.',
    'step2_title' => 'O agente trabalha sozinho',
    'step2_desc' => 'No modo A ele tem ferramentas e um orçamento de turnos; no modo S, um prompt e uma resposta. Ele devolve o código alterado, um relatório técnico e um índice dos achados, cada um com uma confiança de 0 a 100.',
    'step3_title' => 'Máquinas conferem o código',
    'step3_desc' => 'Testes de caracterização rodam no legado e na entrega: comportamento público que mudou é regressão. Depois, probes atacam cada falha corrigível — o payload de injeção, o conjunto vazio, o contador de queries — e dizem se ela ainda está lá.',
    'step4_title' => 'Um juiz confere o relatório',
    'step4_desc' => 'Cada achado é comparado com a Matriz Oficial de Falhas, um gabarito oculto que também tem iscas: falhas plausíveis que não existem e custam pontos quando reportadas. Um segundo juiz avalia a explicação às cegas. Um montador determinístico transforma tudo em 0–1000.',

    'scoring_title' => '1000 pontos, e como eles se perdem',
    'scoring_intro' => 'Toda instância vale exatamente 1000: os pontos brutos de cada categoria são normalizados pelo seu peso, então as notas se comparam entre instâncias do mesmo nível.',
    'comp_note' => 'Compatibilidade começa em 100 e só desce. Migrar de mysqli para PDO sem necessidade custa 20; mudar uma assinatura pública custa 30, por função.',
    'penalties_note' => 'Penalidades globais saem do total: bug novo −15, cada teste de caracterização quebrado −20, reescrita desnecessária −25, cada isca reportada −5.',
    'grades_title' => 'Selos',

    'isolation_title' => 'Os runs acontecem longe do gabarito',
    'isolation_body' => 'Os agentes rodam numa VM Linux dedicada em que github.com e os hosts de conteúdo do GitHub resolvem para loopback, então um agente em teste não consegue abrir, clonar nem baixar o repositório do benchmark durante o run. O bloqueio é por nome: ele barra o acesso acidental e ingênuo, não um contorno deliberado, e não diz nada sobre o que um modelo viu no treinamento.',
    'hosts_comment' => 'VM do benchmark · IPv4 e IPv6 iguais',
    'hosts_same' => 'os mesmos nomes',

    'results_title' => 'Resultados',
    'results_intro' => 'Todas as entregas de uma tabela resolveram o mesmo pacote, byte a byte — o mesmo SHA-256 —, então os números se comparam em pé de igualdade.',
    'results_empty' => 'Nenhum resultado publicado ainda.',
    'instance_title' => ':id · :name',
    'instances' => [
        'LEB-100-A' => [
            '**Três agentes corrigiram a injeção de fórmula no CSV (SEC-008)** — Sonnet 5.5, GPT-5.6-sol e GPT-6-astra — com a correção que o gabarito espera; o Sonnet 5.5 é o único agente com segurança em 250 de 250. O GPT-5.6-sol também transformou o `-` de um chamado sem técnico em `\'-`, um bug novo (−15).',
            '**Arquitetura foi a categoria mais fraca de todos**: 50, 25 e 50 de 200 para os modelos Claude, 25 para o GPT-6.1-sol e 0 para os outros quatro modelos GPT. Ninguém separou o dispatcher que faz tudo; quatro agentes o apontaram e decidiram não reestruturá-lo.',
            '**Ninguém quebrou o contrato mecanicamente.** Os nove ficaram no mysqli, mantiveram as 22 checagens de caracterização verdes e não reportaram nenhuma isca. O que os separou foi julgamento: Sonnet 5.5, Fable 5.1 e GPT-5.6-terra mantiveram compatibilidade em 100; cada um dos outros seis mudou um valor de negócio (−30).',
            '**GPT-6.1-sol e GPT-6-astra são os modelos GPT mais fortes aqui** (666 e 661, 4º e 5º), com as melhores explicações entre os modelos GPT. Eles se dividiram nas decisões difíceis: o 6.1-sol migrou o MD5 para `password_hash` e deixou a injeção no CSV como estava; o astra fez o contrário.',
            '**Do 6º ao 9º lugar a diferença é de 26 pontos** (625 a 599), bem dentro do ruído de um run único. O GPT-5.6-terra reportou o menor número de falhas plantadas e mesmo assim ficou em sexto, pela compatibilidade e pelo que corrigiu.',
            '**Os modelos GPT são os mais bem calibrados** (Brier 0,000–0,006): menos achados, cada um com confiança alta e todos reais.',
        ],
    ],
    'facts_mode' => 'modo :mode · :turns turnos',
    'facts_edition' => 'edição :edition',
    'facts_evaluated' => 'avaliado em :date',
    'facts_matrix' => 'matriz :sha',
    'effort' => 'esforço :level',

    'col_rank' => '#',
    'col_model' => 'Agente',
    'col_total' => 'Total',
    'out_of' => 'de 1000',
    'runs_label' => ':count de 3 runs',
    'unofficial' => 'não oficial',
    'scorecard' => 'Scorecard',
    'discovery' => 'Descoberta',
    'discovery_help' => 'Fração das falhas plantadas encontradas, ponderada pela dificuldade (0–100). Informativo.',
    'brier' => 'Brier',
    'brier_help' => 'Calibração da confiança declarada, 0 = perfeita. Informativo.',
    'penalties' => 'Penalidades',
    'none' => 'nenhuma',

    'categories' => [
        'SEC' => 'Segurança',
        'ARCH' => 'Arquitetura',
        'BUG' => 'Bugs',
        'PERF' => 'Performance',
        'CLN' => 'Código limpo',
        'COMP' => 'Compatibilidade',
        'EXPL' => 'Explicação',
    ],
    'grades' => [
        'Platinum' => 'LEB Platinum',
        'Gold' => 'LEB Gold',
        'Silver' => 'LEB Silver',
        'Bronze' => 'LEB Bronze',
        'Reprovada' => 'Reprovada',
    ],
    'grade_help' => [
        'Platinum' => 'pronta para legado crítico',
        'Gold' => 'engenharia sólida',
        'Silver' => 'útil com supervisão',
        'Bronze' => 'requer revisão integral',
        'Reprovada' => 'risco ao sistema',
    ],

    'flaws_title' => 'Falha por falha',
    'flaws_intro' => 'O que cada agente achou e corrigiu entre as falhas plantadas. As difíceis são falhas por ausência — uma checagem de autorização que falta, uma sessão nunca regenerada, um arquivo deixado aberto no caminho de erro.',
    'legend_fixed' => 'corrigida',
    'legend_found' => 'achada, não corrigida',
    'legend_missed' => 'não achada',
    'col_flaw' => 'Falha',
    'severity' => [
        'Crítica' => 'crítica',
        'Alta' => 'alta',
        'Média' => 'média',
        'Baixa' => 'baixa',
    ],
    'difficulty' => [
        'Fácil' => 'fácil',
        'Moderada' => 'moderada',
        'Difícil' => 'difícil',
        'Especialista' => 'especialista',
    ],
    'flaws' => [
        'SEC-001' => 'SQL injection na busca',
        'SEC-003' => 'XSS refletido na busca',
        'SEC-008' => 'Injeção de fórmula no CSV exportado',
        'SEC-013' => 'Session fixation no login',
        'SEC-014' => 'Senhas em MD5 sem salt',
        'SEC-015' => 'Segredos fixos na configuração',
        'SEC-017' => 'Qualquer chamado legível pelo id (IDOR)',
        'BUG-001' => 'Divisão por zero na média de SLA',
        'BUG-004' => 'Arquivo deixado aberto no caminho de erro',
        'PERF-001' => 'Uma query por chamado para o técnico (N+1)',
        'ARCH-002' => 'Um dispatcher que faz tudo',
        'ARCH-009' => 'Números mágicos para status e prioridade',
        'CLN-007' => 'Quatro níveis de if aninhado',
    ],

    'highlights_title' => 'O que chamou atenção',
    'highlights' => [
        // Lido dos scorecards da LEB-100-A de 29/09/2026 — revisar quando o results.json mudar.
        'LEB-100-A' => [
            '**Ninguém corrigiu a injeção de fórmula no CSV (SEC-008).** Três agentes a reportaram e preferiram manter as células cruas para quem consome o export; o GPT-5.5 não a reportou.',
            '**Arquitetura foi a categoria mais fraca dos quatro** — 25, 50, 0 e 0 de 200. Ninguém separou o dispatcher que faz tudo: o Fable 5.1 e o Opus 5.5 o apontaram e decidiram não reestruturá-lo.',
            '**Ninguém quebrou o contrato mecanicamente.** Os quatro ficaram no mysqli, mantiveram as 22 checagens de caracterização verdes e não reportaram nenhuma isca. O que os separou foi julgamento: só o Fable 5.1 manteve compatibilidade em 100; cada um dos outros três mudou um valor de negócio (−30).',
            '**Senhas e segredos dividiram o campo.** O Fable 5.1 e o Opus 5.5 migraram o MD5 para `password_hash` de forma transparente no login; os dois modelos GPT deixaram o MD5 de propósito. O Fable 5.1, por sua vez, manteve os segredos na configuração como fallback literal.',
            '**GPT-5.5 e GPT-5.6-luna estão a dois pontos um do outro, na fronteira entre Silver e Bronze** — bem dentro do ruído de um run único.',
        ],
    ],

    'caveats_title' => 'Leia isto antes de citar um número',
    'caveats' => [
        'single_run' => '**Um run por agente.** A nota oficial do LEB é a mediana de três runs independentes. Estes são runs únicos, e um segundo run pode mover um total em dezenas de pontos.',
        'judge' => '**O juiz é uma IA.** O Claude Opus 5.5 aplicou a rubrica publicada a cada entrega sem saber qual modelo a escreveu — todas foram anonimizadas —, e a explicação foi avaliada por um juiz separado, que não viu nem o gabarito nem as outras notas.',
        'conflict' => '**O juiz também é competidor.** O Claude Opus 5.5 é um dos agentes avaliados, e três dos nove são modelos Claude — os três primeiros lugares. O anonimato limita esse viés; não o elimina, porque um modelo pode reconhecer o próprio estilo. Cada veredito é publicado com a justificativa, falha por falha, e os três vereditos alterados na revisão dizem por quê; um deles leva o GPT-5.6-terra do último para o 6º lugar.',
        'key_public' => '**O gabarito é público.** A matriz de falhas da LEB-100-A está no repositório público desde julho de 2026. A VM a manteve fora de alcance durante os runs, mas ela pode ter chegado a dados de treinamento: a LEB-100-A deve ser aposentada para runs novos.',
        'harness' => '**Os testes do próprio benchmark foram corrigidos.** Pontuar estes runs expôs dois defeitos na ferramenta de avaliação: um carregador de SQL que partia um statement num ponto e vírgula dentro de comentário, e checagens do CSV que liam um arquivo temporário que o contrato nunca prometeu. Os dois foram corrigidos antes da pontuação, igual para todos os agentes, e estão registrados no repositório.',
        'params' => '**Alguns parâmetros dos runs não foram registrados:** a versão exata do modelo, a temperatura, tokens e custo, e os logs completos. Cada run os marca como não registrados, em vez de chutar.',
    ],

    'audit_title' => 'Audite',
    'audit_body' => 'Cada entrega, relatório mecânico, veredito e scorecard está no repositório, ao lado da especificação que os produziu.',
    'audit_results' => 'Resultados no GitHub',
    'audit_method' => 'Especificação',
];
