<?php

/*
| The GitHub Desktop project page (a static view, no database row).
|
| Facts checked against the fork's own tree (samirhvbr/github-desktop, branch
| `development`) on 2026-09-05: script/package.ts, app/src/lib/fork-version.ts
| and VERSION.md.
*/

return [
    'title' => 'GitHub Desktop multi-repositório',
    'meta_description' => 'GitHub Desktop — o cliente Git visual e open-source da GitHub (Electron, TypeScript, React), com um painel multi-repositório e instaladores para Linux, Windows e macOS.',

    'kicker' => 'Aplicativo desktop',
    'heading' => 'GitHub Desktop',
    'heading_accent' => 'multi-repositório',

    'intro_what' => 'O :name é o cliente Git visual e open-source da GitHub — construído em :electron e escrito em :typescript com :react. Ele deixa o dia a dia com Git mais simples: commits, branches, histórico, pull requests e resolução de conflitos numa interface limpa, sem precisar decorar comandos.',
    'intro_why' => 'Oficialmente, a GitHub :does_not_ship. Este fork compila o GitHub Desktop a partir do código-fonte e empacota para os três sistemas — e acrescenta o que motivou o fork: um :multirepo, com todos os seus repositórios numa tela só.',
    'does_not_ship' => 'não distribui o app para Linux',
    'intro_multirepo' => 'painel multi-repositório',

    'feature_multirepo' => 'Painel multi-repositório',
    'feature_multirepo_desc' => 'Todos os seus repositórios numa tela, marcando os que têm commit pendente ou estão atrás do remoto.',
    'feature_batch' => 'Pull e push em lote',
    'feature_batch_desc' => 'Marque vários repositórios e sincronize os três de cada vez, com resultado por repositório.',
    'feature_commits' => 'Commits visuais',
    'feature_commits_desc' => 'Stage, commit, branches e merges sem linha de comando.',
    'feature_diff' => 'Diff lado a lado',
    'feature_diff_desc' => 'Veja exatamente o que mudou antes de confirmar.',
    'feature_pr' => 'Pull requests',
    'feature_pr_desc' => 'Crie e acompanhe PRs e o status dos checks.',
    'feature_packages' => 'Instalador nativo',
    'feature_packages_desc' => '.deb, .rpm, AppImage e .pkg.tar.zst no Linux; .exe e .msi no Windows; .dmg no macOS.',

    'versions' => 'Duas versões, e elas não são a mesma coisa: :fork é a versão deste fork e :upstream é a release do GitHub Desktop em que ele se baseia. As duas aparecem na tela Sobre.',
    'autoupdate' => 'A atualização automática está desligada de propósito: o feed oficial da GitHub sobrescrevia o build do fork. Atualizar é baixar a versão nova.',

    'download' => 'Baixar',
    'source' => 'Código no GitHub',

    'disclaimer' => 'Projeto da comunidade, sem vínculo oficial com a GitHub, Inc. Licença MIT, herdada do desktop/desktop. As marcas citadas pertencem aos respectivos donos.',
];
