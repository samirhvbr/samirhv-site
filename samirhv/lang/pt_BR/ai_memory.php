<?php

/*
| A seção do ai-memory em /p/ai-memory — tradução de lang/en/ai_memory.php.
|
| DOIS PROJETOS, UMA PÁGINA, E A PÁGINA DIZ QUAL É QUAL. O ai-memory é do Fabio
| Akita (akitaonrails/ai-memory): é o produto que está sendo explicado. O
| ai-memory-web é nosso (samirhvbr/ai-memory-web): é o painel somente-leitura
| sobre o índice dele, e é por causa dele que esta página tem telas. Cada
| afirmação abaixo é atribuída a um dos dois — uma página que misturasse os dois
| estaria tomando crédito pelo primeiro.
|
| Conferido contra os dois repositórios em 14/09/2026.
*/

return [
    /* ── O ai-memory ──────────────────────────────────────────────── */
    'title' => 'A memória que o seu agente não tem',
    'lead' => 'O seu agente de programação já toma notas. Elas vivem numa máquina só, pertencem àquele agente só e somem no instante em que você troca de ferramenta. O :name é o que fica do outro lado desse muro: uma memória compartilhada que mais de vinte harnesses — :claude, :codex, Cursor, Gemini CLI, OpenCode e outros — leem e escrevem.',
    'lead_2' => 'Saia do Claude Code no meio da tarefa, abra o Codex na mesma pasta e o próximo agente recebe um handoff de verdade: onde você parou, o que já foi tentado e por que falhou, o que continua em aberto. É um protocolo tipado, reivindicado uma única vez — não um bilhete que você torce para o próximo modelo ler.',

    'features_title' => 'O que o torna diferente',

    'f_agents' => 'Acompanha você entre agentes',
    'f_agents_desc' => 'Mais de vinte harnesses alimentam uma memória só. O handoff tem dono, tem tipo e é reivindicado uma vez — dois agentes não pegam o mesmo bastão.',
    'f_machines' => 'Acompanha você entre máquinas',
    'f_machines_desc' => 'A memória mora num servidor que é seu — o mesmo notebook, uma máquina no homelab, onde você quiser. O projeto que você deixou no desktop é o projeto que você retoma no notebook.',
    'f_markdown' => 'A sua memória é markdown puro',
    'f_markdown_desc' => 'A fonte da verdade é um wiki versionado em git, de arquivos :md comuns. Dá para usar grep, abrir no Obsidian, editar na mão. O banco é um índice derivado, que se reconstrói a partir dos arquivos.',
    'f_silent' => 'Registra o trabalho, não uma cerimônia',
    'f_silent_desc' => 'Hooks de ciclo de vida capturam o que de fato aconteceu — prompts, chamadas de ferramenta, início e fim de sessão — sanitizados numa fronteira de privacidade tipada antes de qualquer gravação. Não existe um "lembre disso" para dizer.',
    'f_nollm' => 'O caminho padrão não gasta token',
    'f_nollm_desc' => 'Captura, busca e handoff funcionam sem nenhuma chave de API. As passadas com LLM — consolidação, auto-improve — são a parte opcional, não o preço de entrada.',
    'f_team' => 'Funciona para um time',
    'f_team_desc' => 'Aponte todo mundo para um servidor e o que as sessões de uma pessoa aprenderam, os agentes de todo mundo conseguem recuperar. Autenticação multiusuário, atribuição por pessoa e log de auditoria já vêm juntos, não num plano pago.',

    /* ── O ai-memory-web ──────────────────────────────────────────── */
    'web_label' => 'O painel web',
    'web_title' => 'ai-memory-web — veja o que os seus agentes lembram',
    'web_desc' => 'O ai-memory guarda; o :web mostra. É um painel Laravel somente-leitura sobre o mesmo índice SQLite, no mesmo host, respondendo a duas perguntas: :q1 e :q2.',
    'web_desc_2' => 'Ele nunca escreve, e isso é garantido no motor em vez de prometido num comentário: a conexão é fixada com :pragma, o handle cru é privado e um teste tenta uma escrita de verdade e exige que ela falhe. O ai-memory segue sendo o único escritor do próprio índice.',
    'web_q1' => 'o que os agentes lembraram',
    'web_q2' => 'como aquilo foi coletado',

    'screens_title' => 'Nove telas sobre um banco só',
    'screens_lead' => 'Um dashboard com totais ao vivo e um histórico que sobrevive a um reset do ai-memory, os projetos e workspaces por onde a memória está espalhada, as páginas consolidadas do wiki e as versões delas, as sessões e os fatos que cada uma aprendeu, os handoffs entre agentes e busca full-text pelo índice FTS5 do próprio ai-memory.',

    'shot_dashboard' => 'Dashboard',
    'shot_dashboard_desc' => 'Totais e atividade diária num relance — observações, páginas, sessões, handoffs abertos.',
    'shot_dashboard2' => 'Histórico e projetos ativos',
    'shot_dashboard2_desc' => 'O crescimento da memória ao longo do tempo, e quais projetos estão de fato produzindo isso.',
    'shot_projects' => 'Projetos',
    'shot_projects_desc' => 'Todos os projetos lado a lado: quantas páginas, sessões e observações cada um guarda.',
    'shot_workspaces' => 'Workspaces',
    'shot_workspaces_desc' => 'Como a memória coletada se distribui entre os workspaces.',
    'shot_pages' => 'Páginas de conhecimento',
    'shot_pages_desc' => 'O wiki consolidado, navegável por projeto e por camada de memória — com o histórico de versões de cada página.',
    'shot_sessions' => 'Sessões dos agentes',
    'shot_sessions_desc' => 'Qual agente rodou, por quanto tempo, e as observações que aquela sessão coletou.',
    'shot_views' => 'Observações',
    'shot_views_desc' => 'Os fatos crus, filtrados por tipo, importância, projeto e período.',
    'shot_handoffs' => 'Handoffs',
    'shot_handoffs_desc' => 'As transferências de contexto entre agentes, e o estado de cada uma.',
    'shot_search' => 'Busca',
    'shot_search_desc' => 'Busca full-text por todo o conhecimento guardado, com o trecho que casou em cada resultado.',

    'shots_note' => 'Estas capturas são da integração AI-MEMORY em português no admin deste próprio site, de onde o aplicativo separado foi extraído. O :web em si é em inglês e tem a navegação dele.',

    /* ── Repositórios ─────────────────────────────────────────────── */
    'repos_title' => 'Os dois repositórios',
    'repo_core_desc' => 'O ai-memory em si — Rust, licença MIT, de Fabio Akita. Instalação e configuração estão no README dele.',
    'repo_web_desc' => 'O painel web somente-leitura. PHP 8.3+ com pdo_sqlite, sem Node e sem etapa de build.',
];
