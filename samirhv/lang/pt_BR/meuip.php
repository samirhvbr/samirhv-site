<?php

/*
| A seção do meuip.rs em /p/meuip — tradução de lang/en/meuip.php.
|
| Conferido contra o código do próprio aplicativo (index.php, views/home.php,
| README.md) em 14/09/2026. A tabela de endpoints é a tabela de rotas daquele
| front controller; se os dois divergirem, quem está certo é o front controller
| e é este arquivo que está velho.
*/

return [
    'title' => 'Um endereço, duas respostas',
    'lead' => 'Chame com :curl e você recebe o endereço puro e uma quebra de linha — pronto para ler, sem nada para limpar. Abra a mesma url no navegador e você recebe a página: o endereço em corpo grande, quem é o seu provedor, onde a rota te coloca e os comandos para fazer tudo isso pelo terminal.',
    'lead_2' => 'A ideia é essa. Um "qual é o meu IP" que um script consegue usar é um serviço diferente de um que uma pessoa consegue ler, e a maioria dos sites obriga a escolher. Este lê o cabeçalho :accept e responde a pergunta no formato que quem perguntou consegue aproveitar.',

    'terminal_title' => 'Pelo terminal',
    'terminal_note' => 'Sem chave, sem conta, sem dança de rate limit. A saída é uma linha só, então vai direto para uma variável.',

    'endpoints_title' => 'Endpoints',
    'endpoints_route' => 'Rota',
    'endpoints_answer' => 'O que responde',

    'ep_root' => 'Seu endereço público — texto puro para um cliente, a página inteira para um navegador.',
    'ep_ip' => 'Só o endereço, sempre como texto.',
    'ep_asn' => 'O sistema autônomo por onde a sua rota sai.',
    'ep_isp' => 'O provedor que anuncia aquele AS.',
    'ep_country' => 'Código do país.',
    'ep_region' => 'Estado ou região.',
    'ep_coord' => 'Latitude e longitude — na página, um link para o mapa.',
    'ep_all' => 'Tudo o que está acima num único objeto JSON.',
    'ep_trace' => 'Traceroute: a rota até o seu próprio endereço, ou até um host que você indicar.',
    'ep_doc' => 'A documentação, o mesmo conteúdo nos dois formatos.',

    'trace_label' => 'Looking glass',
    'trace_title' => 'Um traceroute que roda no servidor, não na sua máquina',
    'trace_desc' => 'Rede corporativa bloqueia ICMP, e um traceroute rodado de dentro de uma conta sobre aquela rede, não sobre a rota. O :trace executa os saltos a partir do servidor e manda cada um para a página assim que ele volta — sem esperar a execução inteira terminar — e mantém a rota aberta numa leitura contínua no estilo do :mtr quando você pede.',
    'trace_desc_2' => 'Responde nos dois formatos, como todo o resto: navegador recebe a página ao vivo, :curl recebe a rota em texto.',

    'facts_title' => 'O que ele não faz',
    'fact_account' => 'Sem conta',
    'fact_account_desc' => 'Não há cadastro e não há login. Também não há plano pago que libere os outros endpoints.',
    'fact_key' => 'Sem chave de API',
    'fact_key_desc' => 'Os endpoints são abertos. A consulta de geolocalização por trás deles fica em cache no servidor, então um laço de :curl não vira fatura.',
    'fact_cookie' => 'Sem cookie',
    'fact_cookie_desc' => 'A página não grava nenhum. O tema que você escolhe fica no :storage, no seu próprio navegador, e nunca sai de lá.',
    'fact_js' => 'Sem exigir JavaScript',
    'fact_js_desc' => 'A página é montada no servidor e se lê igual com script desligado — quem precisa dele são os botões de copiar e o trace ao vivo.',

    'note' => 'Feito e mantido pela Blue3, em PHP sem framework: um front controller, prepared statements e a consulta de geolocalização por cURL nativo, com timeout e cache. A reescrita da v2.0 foi o que substituiu o :shell do original.',
];
