<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Projetos curados da vitrine, na ordem oficial:
 *   1. ShvIA (híbrido: site + app desktop)
 *   2. ai-memory (projeto de site: explicação + galeria do painel web)
 *   3. ai-usagebar (documentação: página curada de instalação)
 *   4. GitHub Desktop (download)
 *   5. Tura Notes (download: o .dmg assinado que o CI do repo não publica)
 *   6. SShvTerm (projeto de site: os instaladores moram no sshvterm.com)
 *   7. meuip.rs (projeto de site: o produto É o endereço)
 *
 * A ORDEM DESTE ARQUIVO É A ORDEM DA TELA, e é por isso que os blocos são
 * movidos junto com o `sort_order` em vez de só renumerados no lugar. O menu, a
 * home e a lista de downloads leem todos `orderBy('sort_order')`, então quem
 * quiser conferir a vitrine lê este arquivo de cima para baixo. Renumerar sem
 * mover deixaria as duas ordens divergentes, e a errada seria a que se lê.
 *
 * updateOrCreate por slug: idempotente E autoritativo — rodar de novo
 * sincroniza título/descrição/ordem/flags com o que está aqui no código.
 * (Os arquivos de download continuam sendo gerenciados pelo admin, aba Arquivos.)
 *
 * As descrições foram conferidas contra o repositório de cada app em 05/09/2026.
 * Onde este arquivo diverge do que o app faz, é este arquivo que está errado.
 */
class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Híbrido: link pra plataforma web + downloads do app desktop (Tauri).
        Project::updateOrCreate(
            ['slug' => 'shvia'],
            [
                'title' => 'ShvIA',
                'description' => "Assistente de IA interno da Blue3 para apoio operacional e consulta de conhecimento corporativo. Chat com múltiplos modelos, ditado por voz e leitura em voz alta, e um modo Code para tarefas de desenvolvimento.\n\nUse online direto no navegador (sempre na última versão) ou baixe o app desktop para Windows, macOS e Linux.",
                'category' => 'Assistente IA',
                'icon' => 'fa-solid fa-robot',
                'page_view' => null,
                'external_url' => 'https://ia.blue3.com.br',
                'upstream_repo' => null, // repositórios privados: nada a rastrear no monitor
                'redirect_to_site' => false, // híbrido: abre /p/shvia com botão "usar online" + downloads
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        // 2) Projeto de site: o upstream é de terceiro (Fabio Akita) e mora no
        //    GitHub, então é para lá que o painel de acesso aponta. O que esta
        //    página acrescenta ao README é o ai-memory-web — o painel de leitura,
        //    nosso (samirhvbr/ai-memory-web) — com as telas dele.
        Project::updateOrCreate(
            ['slug' => 'ai-memory'],
            [
                'title' => 'ai-memory',
                'description' => "Memória de longo prazo para agentes de programação. O que uma sessão aprendeu — decisões, tentativas que falharam, perguntas em aberto — fica gravado em markdown e volta para o próximo agente, mesmo que seja outro: saia do Claude Code no meio da tarefa, abra o Codex na mesma pasta e continue de onde parou.\n\nProjeto de Fabio Akita (akitaonrails/ai-memory), escrito em Rust, licença MIT. Esta página mostra também o ai-memory-web, o painel web que abre esse acervo para leitura — nove telas sobre o mesmo índice, sem nunca escrever nele.",
                'category' => 'Memória de agentes',
                'icon' => 'fa-solid fa-brain',
                'page_view' => null,
                'external_url' => 'https://github.com/akitaonrails/ai-memory',
                'upstream_repo' => 'akitaonrails/ai-memory',
                'redirect_to_site' => false,
                'is_published' => true,
                'sort_order' => 2,
            ]
        );

        // 3) Documentação: página curada 'projects.ai-usagebar' (instalação por SO). Sem binários
        //    hospedados aqui — instala via AUR/crates.io/build. Autoria de Fabio Akita.
        Project::updateOrCreate(
            ['slug' => 'ai-usagebar'],
            [
                'title' => 'ai-usagebar',
                'description' => "Monitor de uso dos seus planos de IA — quatorze provedores, entre eles Anthropic Claude, OpenAI Codex, Z.AI, OpenRouter, DeepSeek, Kimi, xAI/Grok, MiniMax e a própria ShvIA — direto na barra do sistema (Waybar/GNOME no Linux, menu bar no macOS) e num TUI de terminal que roda nos três sistemas.\n\nProjeto de Fabio Akita (akitaonrails/ai-usagebar), escrito em Rust, licença MIT. As integrações nativas de GNOME e macOS mostradas aqui nasceram neste fork e foram adotadas pelo upstream. Veja como instalar em cada sistema.",
                'category' => 'Monitor de uso de IA',
                'icon' => 'fa-solid fa-gauge-high',
                'page_view' => 'projects.ai-usagebar',
                'external_url' => null,
                'upstream_repo' => 'akitaonrails/ai-usagebar',
                'redirect_to_site' => false,
                'is_published' => true,
                'sort_order' => 3,
            ]
        );

        // 4) Download: build da comunidade do GitHub Desktop (a GitHub não publica p/ Linux).
        Project::updateOrCreate(
            ['slug' => 'github-desktop'],
            [
                'title' => 'GitHub Desktop',
                'description' => "GitHub Desktop é o cliente Git visual e open-source da GitHub — Electron, TypeScript e React. Commits, branches, histórico, pull requests e resolução de conflitos numa interface limpa, sem decorar comandos.\n\nA GitHub não distribui o app para Linux. Este é um fork que compila do código-fonte e empacota para as três plataformas: .deb, .rpm, AppImage e .pkg.tar.zst no Linux, .exe e .msi no Windows, .dmg no macOS. O fork acrescenta um painel multi-repositório — todos os seus repositórios numa tela, com pull e push em lote.",
                'category' => 'Aplicativo Desktop',
                'icon' => 'fa-brands fa-github',
                'page_view' => null,
                'external_url' => null,
                'upstream_repo' => 'desktop/desktop',
                'redirect_to_site' => false,
                'is_published' => true,
                'sort_order' => 4,
            ]
        );

        // 5) Download: app local-first de notas em Markdown. Repositório público
        //    (samirhvbr/tura-notes), mas o .dmg assinado + notarizado NÃO sai no
        //    GitHub Release — o certificado Developer ID mora num keychain, não
        //    num secret de CI, então quem empacota é a máquina que o tem. É por
        //    isso que este projeto está aqui: o site é o canal de macOS — e, desde
        //    o `tools/build-linux.sh` da 1.0.3, também de .deb, AppImage e .rpm,
        //    que sobem pelo mesmo `files:add` que o .dmg.
        Project::updateOrCreate(
            ['slug' => 'tura-notes'],
            [
                'title' => 'Tura Notes',
                'description' => "Aplicativo de notas em Markdown local-first para Linux, macOS e Windows. Você escolhe uma pasta; essa pasta é o seu workspace; os arquivos .md dentro dela são as suas notas.\n\nOs arquivos são seus, não do aplicativo: não há formato proprietário, não há conta e não há nuvem nossa. Cada nota continua legível por um terminal, pelo VS Code, por git, rsync ou qualquer outro editor — e trabalhar offline não é um modo, é o caso normal. Busca incremental, propriedades YAML, tags, wiki links, backlinks e um servidor MCP para agentes de IA, com permissões por escopo.\n\nO .dmg do macOS é assinado e notarizado pela Apple e sai daqui. Os pacotes de Linux (.deb, AppImage, .rpm) saem da mesma linha de build local e são publicados aqui e nos Releases do GitHub, onde também fica o pacote do AUR.",
                'category' => 'Notas em Markdown',
                'icon' => 'fa-solid fa-feather-pointed',
                'page_view' => null,
                'external_url' => null,
                // Não é fork: o upstream é este repositório. O monitor rastreia
                // fork vs. upstream, e apontá-lo para o próprio repo faria a tela
                // comparar o projeto com ele mesmo.
                'upstream_repo' => null,
                'redirect_to_site' => false,
                'is_published' => true,
                'sort_order' => 5,
            ]
        );

        // 6) Projeto de site: os binários moram no sshvterm.com, a explicação mora aqui.
        //
        //    ERA um link puro (`redirect_to_site => true`): clicar no card saía do
        //    site sem nunca mostrar o que o produto faz. Um cliente SSH com sync
        //    zero-knowledge e um agente sob política allow·ask·deny não se explica
        //    num card de vitrine, e quem chegava pelo /downloads não tinha onde ler
        //    isso. Agora abre /p/sshvterm — descrição, a seção de
        //    partials/projects/sshvterm.blade.php, changelog — e o botão do painel
        //    de acesso é que leva ao site oficial, que continua sendo o canal de
        //    download.
        Project::updateOrCreate(
            ['slug' => 'sshvterm'],
            [
                'title' => 'SShvTerm',
                'description' => "Cliente SSH/SFTP desktop e multiplataforma, com sync zero-knowledge: hosts, chaves e senhas são cifrados no seu computador e o servidor nunca vê o conteúdo — e esse servidor de sync pode ser hospedado por você.\n\nTem um agente de IA que opera o terminal — propõe e executa comandos no PTY visível, sob uma política allow · ask · deny que você controla (Anthropic, OpenAI, xAI/Grok e mais), com a sua própria chave. Windows, macOS e Linux. Baixe pelo site oficial.",
                'category' => 'Cliente SSH',
                'icon' => 'fa-solid fa-terminal',
                'page_view' => null,
                'external_url' => 'https://sshvterm.com',
                'upstream_repo' => null, // repositórios privados: nada a rastrear no monitor
                'redirect_to_site' => false, // projeto de site: abre /p/sshvterm, com o link no painel
                'is_published' => true,
                'sort_order' => 6,
            ]
        );

        // 7) Projeto de site: o produto É o endereço. Não há binário para hospedar
        //    aqui e não haverá — o `distributesFilesHere()` do model é o que impede
        //    a página de prometer um "app desktop em preparação" que não existe.
        Project::updateOrCreate(
            ['slug' => 'meuip'],
            [
                'title' => 'meuip.rs',
                'description' => "Serviço \"qual é o meu IP\" da Blue3: mostra o endereço público de quem chega, com ASN, provedor, país e região — e responde no formato de quem perguntou. O `curl` recebe o endereço puro e uma quebra de linha; o navegador recebe a página inteira.\n\nTem também um looking glass: um traceroute executado no servidor e transmitido ao vivo para a página, para quando é a sua própria rede que bloqueia ICMP. Sem conta, sem chave de API e sem cookie.",
                'category' => 'Ferramenta de rede',
                'icon' => 'fa-solid fa-network-wired',
                'page_view' => null,
                'external_url' => 'https://meuip.rs',
                'upstream_repo' => null, // repositório privado: nada a rastrear no monitor
                'redirect_to_site' => false,
                'is_published' => true,
                'sort_order' => 7,
            ]
        );

        $this->command?->info('Projetos sincronizados: ShvIA (1) · ai-memory (2) · ai-usagebar (3) · GitHub Desktop (4) · Tura Notes (5) · SShvTerm (6) · meuip.rs (7).');
    }
}
