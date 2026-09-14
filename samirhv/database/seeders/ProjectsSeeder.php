<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Projetos curados da vitrine, na ordem oficial:
 *   1. ShvIA (híbrido: site + app desktop)
 *   2. Tura Notes (download: o .dmg assinado que o CI do repo não publica)
 *   3. GitHub Desktop (download)
 *   4. ai-usagebar (documentação: página curada de instalação)
 *   5. SShvTerm (projeto de site: os instaladores moram no sshvterm.com)
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

        // 2) Download: app local-first de notas em Markdown. Repositório público
        //    (samirhvbr/tura-notes), mas o .dmg assinado + notarizado NÃO sai no
        //    GitHub Release — o certificado Developer ID mora num keychain, não
        //    num secret de CI, então quem empacota é a máquina que o tem. É por
        //    isso que este projeto está aqui: o site é o canal de macOS.
        Project::updateOrCreate(
            ['slug' => 'tura-notes'],
            [
                'title' => 'Tura Notes',
                'description' => "Aplicativo de notas em Markdown local-first para Linux, macOS e Windows. Você escolhe uma pasta; essa pasta é o seu workspace; os arquivos .md dentro dela são as suas notas.\n\nOs arquivos são seus, não do aplicativo: não há formato proprietário, não há conta e não há nuvem nossa. Cada nota continua legível por um terminal, pelo VS Code, por git, rsync ou qualquer outro editor — e trabalhar offline não é um modo, é o caso normal. Busca incremental, propriedades YAML, tags, wiki links, backlinks e um servidor MCP para agentes de IA, com permissões por escopo.\n\nO .dmg do macOS é assinado e notarizado pela Apple e sai daqui. Os pacotes de Linux (.deb, AppImage, AUR) ficam nos Releases do GitHub.",
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
                'sort_order' => 2,
            ]
        );

        // 3) Download: build da comunidade do GitHub Desktop (a GitHub não publica p/ Linux).
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
                'sort_order' => 3,
            ]
        );

        // 4) Documentação: página curada 'projects.ai-usagebar' (instalação por SO). Sem binários
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
                'sort_order' => 4,
            ]
        );

        // 5) Projeto de site: os binários moram no sshvterm.com, a explicação mora aqui.
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
                'sort_order' => 5,
            ]
        );

        $this->command?->info('Projetos sincronizados: ShvIA (1) · Tura Notes (2) · GitHub Desktop (3) · ai-usagebar (4) · SShvTerm (5).');
    }
}
