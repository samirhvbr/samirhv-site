<?php

/*
| A seção do SShvTerm em /p/sshvterm — tradução de lang/en/sshvterm.php.
|
| Escrita a partir do que o próprio produto publica em sshvterm.com, conferido
| em 14/09/2026. Nada aqui vem do repositório privado: esta é uma página de
| catálogo, e página de catálogo que descrevesse interno não lançado estaria
| publicando esse interno.
|
| Os instaladores NÃO saem deste site. Todo caminho de saída desta seção termina
| no sshvterm.com, que é o canal de download.
*/

return [
    'title' => 'Um cliente SSH que traz o próprio agente',
    'lead' => 'Hosts, identidades, chaves, grupos, tags, snippets e regras de port forwarding num cliente desktop só, sobre um terminal de verdade — :xterm com renderização acelerada por GPU, abas e splits de até quatro painéis, conexão por senha ou por chave e reconexão automática quando a rede cai.',
    'lead_2' => 'O que ele acrescenta a isso é um agente de IA que opera o terminal: você pede em linguagem natural, ele propõe o comando e — depois que você aprova — digita na sessão visível enquanto você acompanha.',

    'sync_label' => 'Sync zero-knowledge',
    'sync_title' => 'O servidor guarda os seus hosts e não consegue lê-los',
    'sync_desc' => 'Hosts, identidades, chaves e snippets sincronizam de ponta a ponta. Os campos sensíveis são cifrados na sua própria máquina — :crypto — e a chave nunca sai dela: quem a mantém é o sidecar, em memória, e mais ninguém. Instale o SShvTerm em outra máquina, faça login e tudo reaparece; o servidor que carregou aquilo nunca viu nada.',
    'sync_desc_2' => 'E esse servidor pode ser o seu. O backend de sync é uma peça separada e hospedável por você, então "a nuvem" aqui é uma escolha de implantação, não uma condição para usar o produto.',

    'agent_label' => 'O agente de IA',
    'agent_title' => 'Ele roda comando sob uma política que você escreveu',
    'agent_desc' => 'Cada comando é :allow, :ask ou :deny pela sua regra. Ação destrutiva pede confirmação, e nada roda com a execução desligada. O agente digita no terminal visível da aba — você acompanha cada tecla — e tanto a conversa quanto os comandos ficam registrados na auditoria.',
    'agent_desc_2' => 'O Parar interrompe durante a chamada ao provedor, não só entre passos. Cada turno mostra o tempo, os tokens e o custo — o número real quando o provedor informa, marcado com :approx quando é estimativa.',
    'agent_allow' => 'permitir',
    'agent_ask' => 'perguntar',
    'agent_deny' => 'negar',
    'agent_providers' => 'Com a sua própria chave: Claude (Anthropic), ShvIA (Blue3), xAI/Grok, Ollama local, LM Studio, vLLM e qualquer endpoint compatível com OpenAI. Onde o provedor suporta tools, o modelo devolve uma chamada estruturada; onde não suporta, uma ponte de texto extrai o comando de um bloco :run e executa pelo mesmo caminho protegido.',

    'features_title' => 'O que mais tem nele',

    'f_sftp' => 'SFTP na conexão já aberta',
    'f_sftp_desc' => 'Navegue, envie e baixe reaproveitando a sessão SSH que já existe. Fila de transferências com progresso, várias em paralelo, canceláveis em tempo real.',
    'f_vpn' => 'VPN WireGuard por host',
    'f_vpn_desc' => 'Alcance servidores em redes privadas por um túnel escopado só àquela conexão — sem root e sem mexer na VPN do sistema. Se o túnel não sobe, nada vaza: ele falha fechado.',
    'f_keys' => 'Chaves sob controle',
    'f_keys_desc' => 'Gere, importe e organize chaves Ed25519, ECDSA e RSA, e reutilize uma identidade entre hosts em vez de ficar copiando.',
    'f_forward' => 'Port forwarding visual',
    'f_forward_desc' => 'Túneis Local (:local) e Remote (:remote) atrelados à sessão, sem decorar as flags.',
    'f_import' => 'Traga a configuração que você já tem',
    'f_import_desc' => 'Importe hosts do OpenSSH (:config), do PuTTY, do MobaXterm e do Termius, com pré-visualização antes de aplicar qualquer coisa.',
    'f_snippets' => 'Snippets e autocomplete',
    'f_snippets_desc' => 'Salve os comandos que você repete e dispare em qualquer sessão, com snippets de inicialização por host. Sugestões enquanto digita, desligáveis pela barra quando atrapalham o shell.',

    'note' => 'Windows, macOS e Linux. Os instaladores e as notas de versão saem do :site — esta página é a descrição, não o download.',
];
