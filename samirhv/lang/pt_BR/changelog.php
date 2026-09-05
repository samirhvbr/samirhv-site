<?php

/*
| O que mudou em cada aplicativo, por versão. Tradução de lang/en/changelog.php.
|
| CURADO, não espelhado. Cada entrada é escrita a partir do changelog do próprio
| aplicativo e reescrita para quem está decidindo se baixa, não para quem o
| mantém. Entrada que só quer dizer alguma coisa dentro do repositório não entra
| numa página de produto.
|
| Conferido contra cada repositório em 05/09/2026. A data da entrada é a do
| lançamento, não a da conferência.
|
| Para atualizar: leia o changelog do app, acrescente a versão nova no TOPO da
| lista dele e traduza a entrada em lang/en/changelog.php. Os conjuntos de
| chaves são conferidos pelo AppChangelogTest.
*/

return [
    'heading' => 'O que mudou',
    'lead' => 'Os lançamentos mais recentes, tirados do changelog do próprio projeto.',
    'current' => 'atual',
    'source' => 'Changelog completo',
    'empty' => 'Ainda sem notas de lançamento publicadas.',

    'apps' => [
        'ai-usagebar' => [
            [
                'version' => '0.16.0',
                'date' => '2026-07-22',
                'notes' => [
                    'O Google Antigravity entra na lista de provedores, informando as quatro janelas reais de cota — um limite de 5 horas e um semanal para cada um dos dois grupos de modelos — sem nenhuma credencial para configurar.',
                    'A extensão do GNOME passa a suportar o Shell 45 até o 50, antes 45–48.',
                    'As bordas do tooltip não saem mais quebradas quando um rótulo contém &, < ou >.',
                ],
            ],
            [
                'version' => '0.15.0',
                'date' => '2026-07-22',
                'notes' => [
                    'O monitor de contexto se encaixa no painel, com uma tecla para alternar entre inteiro, dividido e rodapé.',
                    'O gasto em créditos deixa de ficar escondido em planos sem teto, e o uso extra aparece na moeda dele em vez de um cifrão fixo.',
                ],
            ],
            [
                'version' => '0.14.0',
                'date' => '2026-07-20',
                'notes' => [
                    'Um monitor local do contexto do Claude Code, opcional, desligado até você ligar e lendo só trechos limitados — sem varrer o disco.',
                    'Quatro provedores de saldo em conta: Kilo, Novita, Moonshot e xAI/Grok. Esses mostram dinheiro, não porcentagem de uso.',
                ],
            ],
            [
                'version' => '0.13.0',
                'date' => '2026-07-17',
                'notes' => [
                    'O Kimi entra na lista de provedores.',
                    'As marcas de ritmo — se você está adiantado ou atrasado em relação ao consumo — chegam ao menu bar do macOS e ao GNOME.',
                    'Corrige uma barra travada em "Sonnet only 0%", linhas cortadas nas preferências do macOS e nomes de chave de API vazando no texto de erro.',
                ],
            ],
            [
                'version' => '0.12.0',
                'date' => '2026-07-08',
                'notes' => [
                    'Limites semanais por modelo passam a aparecer nas interfaces de desktop.',
                ],
            ],
        ],

        'github-desktop' => [
            [
                'version' => '0.4.1',
                'date' => '2026-08-03',
                'notes' => [
                    'O empacotamento passa a gerar também o pacote do Arch (.pkg.tar.zst), convertendo o .deb na própria máquina de build, com as dependências mapeadas para os nomes do Arch.',
                    'Quais formatos de Linux compilar virou opção, então um contêiner pode gerar só o .rpm.',
                ],
            ],
            [
                'version' => '0.4.0',
                'date' => '2026-07-06',
                'notes' => [
                    'Pull e push em lote nos repositórios que você marcar, três de cada vez, com o resultado em cada linha em vez de um veredito no fim.',
                    'Uma tela de relatório — quantos repositórios estão para commitar, atrás, à frente ou em dia — com tudo copiável como texto.',
                    'Todo instalador passa a levar as duas versões no nome do arquivo, então um .deb e um .dmg do mesmo build se reconhecem como tal.',
                ],
            ],
            [
                'version' => '0.3.0',
                'date' => '2026-06-30',
                'notes' => [
                    'Selecionar todos no clone em lote, com um terceiro estado para seleção parcial, respeitando o filtro de busca.',
                    'Um repositório que já existe na pasta de destino é pulado com um aviso, em vez do popup de erro que interrompia o lote inteiro.',
                ],
            ],
            [
                'version' => '0.2.0',
                'date' => '2026-06-27',
                'notes' => [
                    'Clone seletivo: lista os repositórios públicos de qualquer usuário e clona vários deles numa pasta só, de uma vez.',
                ],
            ],
            [
                'version' => '0.1.0',
                'date' => '2026-06-24',
                'notes' => [
                    'O painel multi-repositório — todos os repositórios numa tela, marcando os que têm algo pendente.',
                ],
            ],
        ],

        'shvia' => [
            [
                'version' => '2.110.183',
                'date' => '2026-09-05',
                'notes' => [
                    'A fila de trabalho vira uma lista enumerada; remedi-la contra o código fechou seis itens que já estavam prontos.',
                ],
            ],
            [
                'version' => '2.110.181',
                'date' => '2026-09-05',
                'notes' => [
                    'A central de comando fecha a distância para o desenho aprovado — menos os mostradores que não tinham fonte de dados atrás, que ficaram de fora em vez de serem simulados.',
                ],
            ],
            [
                'version' => '2.110.178',
                'date' => '2026-09-05',
                'notes' => [
                    'A troca de infraestrutura continua funcionando quando o modelo escolhido é gratuito.',
                ],
            ],
            [
                'version' => '2.110.177',
                'date' => '2026-09-05',
                'notes' => [
                    'A tela de painel passa a ser escolha sua: o layout clássico por padrão, a central de comando opcional.',
                ],
            ],
            [
                'version' => '2.110.176',
                'date' => '2026-09-04',
                'notes' => [
                    'O preset radar: o visual de console como um décimo primeiro tema.',
                ],
            ],
        ],
    ],

    /* Fatos que ficam ao lado da lista, não dentro de uma entrada. */
    'notes' => [
        'ai-usagebar' => 'O trabalho posterior deste fork — os provedores ShvIA e MiniMax, e o painel de status das APIs — já está entregue, mas ainda não ganhou uma versão própria.',
        'shvia' => 'O ShvIA são três produtos com três linhas de versão: a plataforma web acima, o app desktop e o site público. O número de correção sobe rápido de propósito — ele avança a cada tela nova, migração ou mudança visível.',
        'github-desktop' => 'A versão do fork e a release do GitHub Desktop em que ele se baseia são números diferentes. As entradas acima são as do fork.',
    ],
];
