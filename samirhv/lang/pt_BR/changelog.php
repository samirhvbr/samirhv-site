<?php

/*
| O que mudou em cada aplicativo, por versão. Tradução de lang/en/changelog.php.
|
| CURADO, não espelhado. Cada entrada é escrita a partir do changelog do próprio
| aplicativo e reescrita para quem está decidindo se vai usar, não para quem o
| mantém. Entrada que só quer dizer alguma coisa dentro do repositório não entra
| numa página de produto.
|
| Conferido contra cada repositório em 05/09/2026, e os quatro que entraram na
| 0.9.0 — tura-notes, ai-memory, sshvterm, meuip — em 14/09/2026. A data da
| entrada é a do lançamento, não a da conferência.
|
| As entradas do ai-memory são as do projeto UPSTREAM (akitaonrails/ai-memory),
| que é o que a página explica. O ai-memory-web tem linha de versão própria; ele
| aparece na nota ao lado, e não intercalado, porque duas linhas de versão numa
| lista só se leem como uma linha que pula números.
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

        'tura-notes' => [
            [
                'version' => '1.1.0',
                'date' => '2026-09-12',
                'notes' => [
                    'O aplicativo procura as próprias atualizações — logo depois de abrir, a cada seis horas ou quando você pedir. Uma versão que você não quer agora pode ser dispensada, e instalar é um ato explícito, que espera o workspace fechar antes de reiniciar qualquer coisa.',
                    'Os feeds de atualização são assinados, um por formato (macOS, AppImage, .deb e .rpm), e um pacote cuja assinatura não confere é recusado. Os pacotes de Arch continuam com o pacman, que é o atualizador certo naquele sistema.',
                ],
            ],
            [
                'version' => '1.0.3',
                'date' => '2026-09-12',
                'notes' => [
                    'Os instaladores de Linux saem da mesma linha de build local que o do macOS: .deb e AppImage por padrão, .rpm quando pedido, com as dependências conferidas antes de qualquer compilação.',
                    'A publicação lê o hash de volta do servidor e compara. Um upload truncado deixa um arquivo que existe e que a página linka sem reclamar — ele tem de falhar aqui, não no seu navegador.',
                ],
            ],
            [
                'version' => '1.0.1',
                'date' => '2026-09-11',
                'notes' => [
                    'O build de macOS é assinado com um Developer ID e notarizado pela Apple, com o ticket grampeado na imagem, então o .dmg baixado abre offline e sem aviso nenhum.',
                    'A publicação recusa de saída uma imagem sem assinatura ou sem o ticket. A regra passou a ser imposta pela ferramenta, em vez de lembrada por quem a executa.',
                ],
            ],
            [
                'version' => '1.0.0',
                'date' => '2026-09-11',
                'notes' => [
                    'A identidade Tura Notes: o logo do T em fita, ícones para cada plataforma e uma tela de boas-vindas que os carrega.',
                    'O identificador do aplicativo, o executável e os caminhos de dados continuam os mesmos, então quem já tinha instalado mantém as notas e as configurações através da troca de nome.',
                ],
            ],
            [
                'version' => '0.20.27',
                'date' => '2026-09-11',
                'notes' => [
                    'Importar o texto de um PDF como Markdown: a extração abre numa prévia editável e só um salvar explícito grava a nota. O PDF em si e as imagens dele ficam fora do workspace.',
                ],
            ],
        ],

        'ai-memory' => [
            [
                'version' => '2.2.0',
                'date' => '2026-09-12',
                'notes' => [
                    'A recuperação passa a se explicar: uma página que apareceu pelo grafo de links agora diz por qual aresta tipada ela chegou — causes, fixes ou contradicts — então "por que isto voltou" tem resposta.',
                    'Pergunta sobre um momento no passado ganha um segundo caminho. Consultar com uma data agora busca nas versões de página que estavam vivas naquele momento, e não só na linha do tempo de entidades, então "em qual banco a gente estava durante a queda" encontra páginas que não citam entidade nenhuma.',
                    'Dois sinais de ranqueamento opcionais, os dois desligados por padrão para que um índice sem configuração ranqueie exatamente como antes. Medidos num conjunto de 138 consultas sobre um wiki de produção de dois anos, o par leva o hit@1 de 0,61 para 0,75.',
                ],
            ],
            [
                'version' => '2.1.0',
                'date' => '2026-09-06',
                'notes' => [
                    'Cadeias de fallback de provedor: uma falha transitória — 429, 5xx, timeout — passa para o próximo provedor da sua lista, levando a mesma requisição e o mesmo schema. Um erro determinístico continua parando na hora, como já parava com um provedor só.',
                    'Um bootstrap interrompido pode ser retomado a partir do progresso gravado por bloco, em vez de pagar de novo por cada chamada ao LLM.',
                    'O OpenCode 2.0 beta vira cliente de primeira classe, e uma sessão do Codex que termina passa a receber o mesmo resumo automático e o mesmo handoff entre agentes que o Claude Code já tinha.',
                ],
            ],
            [
                'version' => '2.0.3',
                'date' => '2026-09-04',
                'notes' => [
                    'A instalação a partir do fonte usa o lockfile versionado, então duas pessoas instalando a mesma tag recebem as mesmas dependências, e não o que resolveu naquele dia.',
                    'O status informa o espaço livre do diretório de dados. Um operador cujo arquivo de segurança deixou 77 MB livres viu o índice falhar ao estender o write-ahead log oito minutos depois, em silêncio, por horas.',
                ],
            ],
            [
                'version' => '2.0.2',
                'date' => '2026-09-03',
                'notes' => [
                    'Links relativos dentro do wiki deixam de dar 404 na visão web, e um caminho de diretório lista as páginas abaixo dele em vez de falhar.',
                    'Uma página cujo título gravado estava vazio volta a ser lida com o título do próprio cabeçalho, em vez de sumir atrás de um alerta falso de "várias páginas com o mesmo título".',
                ],
            ],
            [
                'version' => '2.0.0',
                'date' => '2026-09-02',
                'notes' => [
                    'Um agente launchd para macOS, para o servidor continuar rodando depois que o terminal que o iniciou fecha. Até então fechar o terminal parava a entrega dos hooks sem nada avisar.',
                    'Embeddings locais, sem chave de API e sem servidor externo: um modelo que roda no próprio processo, baixado uma vez, cujos vetores convivem com os de qualquer provedor.',
                    'O status conta mais da verdade — estado da migração, embeddings gravados por provedor e modelo, contagem de arestas tipadas e um medidor de fila que revela um escritor travado.',
                ],
            ],
        ],

        'sshvterm' => [
            [
                'version' => '2.0.78',
                'date' => '2026-09-11',
                'notes' => [
                    'Nada visível mudou, e é essa a entrada: os cinco maiores arquivos do aplicativo passam a ter um teto de tamanho que quebra o build quando eles crescem. Eles vinham crescendo havia trinta versões, e o custo era do tipo que você vê — correção aplicada num caminho e não no irmão dele.',
                ],
            ],
            [
                'version' => '2.0.77',
                'date' => '2026-09-10',
                'notes' => [
                    'As telas de VPN param de afirmar o que não têm como saber. Uma mudança de WireGuard recusada agora diz por quê, em vez de a chave voltar sozinha, e um cofre que não pôde ser lido diz exatamente isso, em vez de escrever "nenhum host com VPN configurada" — uma afirmação cara o bastante para fazer alguém reimportar um .conf que carrega uma chave privada.',
                    'O markdown que renderiza as respostas do agente não reescreve mais os comandos que está mostrando para você.',
                ],
            ],
            [
                'version' => '2.0.76',
                'date' => '2026-09-10',
                'notes' => [
                    'A detecção de segredo roda sozinha; reescrever o que ela achou espera o seu clique. Achar e alterar são duas decisões, e só uma delas é do aplicativo.',
                ],
            ],
            [
                'version' => '2.0.75',
                'date' => '2026-09-10',
                'notes' => [
                    'A redação pode ser reexecutada sobre o que já está gravado. A correção anterior estancou os vazamentos novos e não removeu nenhum dos velhos, o que deixou as sessões registradas antes dela exatamente como estavam.',
                ],
            ],
            [
                'version' => '2.0.74',
                'date' => '2026-09-10',
                'notes' => [
                    'O vazamento na emenda entre dois pedaços da saída do terminal foi fechado num dos três lugares que gravam o log da sessão. Os outros dois gravam o mesmo log, e agora também fecham.',
                ],
            ],
            [
                'version' => '2.0.73',
                'date' => '2026-09-10',
                'notes' => [
                    'Seis falhas que chegavam até você como "erro não reconhecido" passam a dizer o que deu errado.',
                ],
            ],
        ],

        'meuip' => [
            [
                'version' => '0.2.10',
                'date' => '2026-09-08',
                'notes' => [
                    'O diretório .git exposto foi fechado, e o código da v1 que ainda executava em produção ao lado da reescrita saiu de lá.',
                ],
            ],
            [
                'version' => '0.2.0',
                'date' => '2026-08-24',
                'notes' => [
                    'A página ganha tema escuro que não pisca ao carregar, endereço copiável com selo de IPv4/IPv6, chips de contexto, botão de copiar em cada comando curl e coordenadas que abrem um mapa.',
                    'O foco do teclado volta a ser visível, e a animação respeita um sistema que pede menos dela.',
                    'Aferido contra a produção, endpoint por endpoint: a API de texto puro responde exatamente como antes. Uma passada visual que mudasse o que o curl devolve teria quebrado o propósito do serviço.',
                ],
            ],
        ],
    ],

    /* Fatos que ficam ao lado da lista, não dentro de uma entrada. */
    'notes' => [
        'ai-usagebar' => 'O trabalho posterior deste fork — os provedores ShvIA e MiniMax, e o painel de status das APIs — já está entregue, mas ainda não ganhou uma versão própria.',
        'shvia' => 'O ShvIA são três produtos com três linhas de versão: a plataforma web acima, o app desktop e o site público. O número de correção sobe rápido de propósito — ele avança a cada tela nova, migração ou mudança visível.',
        'github-desktop' => 'A versão do fork e a release do GitHub Desktop em que ele se baseia são números diferentes. As entradas acima são as do fork.',
        'ai-memory' => 'Estas são as versões do próprio ai-memory. O ai-memory-web — o painel web mostrado acima — é um projeto separado, com linha de versão própria, hoje na 0.1.x.',
        'sshvterm' => 'O SShvTerm lança com frequência, e nem toda versão muda algo que você vê; a lista acima é a das que mudam. Os instaladores e as notas completas estão no sshvterm.com.',
        'meuip' => 'A reescrita da v2.0 e o traceroute ao vivo vieram antes desta linha de versão e estão no histórico do git, não na numeração. As entradas acima começam onde começa o versionamento da casa.',
    ],
];
