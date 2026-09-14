<?php

/*
| The project page (/p/{slug}): access panel, recommended download, file tabs.
| Values are Portuguese: end-user copy is the carve-out in this repository's
| English-only rule.
*/

return [
    'meta_download' => 'Download de :project',

    'access_aria' => 'Opções de acesso',
    'access' => 'Acesso',

    'online_version' => 'Versão online',
    'online_version_desc' => 'Abra no navegador, sem instalar e sempre na versão mais recente.',
    'open_app' => 'Abrir aplicação',

    'desktop_app' => 'Aplicativo desktop',
    'desktop_app_desc' => 'Builds nativos com versão, arquitetura e hash para conferência.',
    'choose_download' => 'Escolher download',
    'in_preparation' => 'Em preparação',

    /*
    | Texto por projeto do bloco "site" do painel de acesso. Tradução do bloco
    | de mesmo nome em lang/en/project.php — as chaves têm de existir nos dois
    | arquivos, senão a página em português renderiza o inglês (o `Lang::has()`
    | da view cai no idioma de fallback; ver App\Support\Content).
    */
    'sites' => [
        'meuip' => [
            'title' => 'O serviço',
            'desc' => 'Roda no navegador e no terminal. Nada para instalar, sem conta e sem chave.',
            'cta' => 'Abrir o meuip.rs',
        ],

        'sshvterm' => [
            'title' => 'Site oficial',
            'desc' => 'É de lá que saem os instaladores de Windows, macOS e Linux, com as notas de cada versão.',
            'cta' => 'Ir para o sshvterm.com',
        ],

        'ai-memory' => [
            'title' => 'Código e instalação',
            'desc' => 'Um binário em Rust, compilado do fonte ou instalado por pacote. O README tem o passo a passo de cada sistema.',
            'cta' => 'Abrir o ai-memory',
        ],
    ],

    'unavailable' => 'O arquivo :file está indisponível no momento.',

    'recommended' => 'Download recomendado',
    'download_for' => 'Baixar para :os',
    'change_system' => 'trocar de sistema',
    'no_build' => 'Nenhum build disponível ainda.',
    'copy_command' => 'Copiar comando',
    'copy' => 'copiar',

    'files' => 'Arquivos',

    'about_this_build' => 'O que é este build, e o que ele empacota',
    'os_tabs_aria' => 'Sistemas operacionais',
    'soon' => 'em breve',
    'build_soon' => 'Build de :os em breve.',
    'older_versions' => 'Versões anteriores (:count)',

    'verify' => 'Confira a integridade após baixar — Linux/macOS: :unix · Windows: :windows',
];
