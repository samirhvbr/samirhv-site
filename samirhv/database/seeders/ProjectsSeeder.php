<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Projetos curados da vitrine, na ordem oficial:
 *   1. ShvIA (híbrido: site + app desktop)
 *   2. GitHub Desktop (download)
 *   3. ai-usagebar (documentação: página curada de instalação)
 *   4. SShvTerm (projeto-link: mora no site oficial)
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

        // 2) Download: build da comunidade do GitHub Desktop (a GitHub não publica p/ Linux).
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

        // 4) Projeto-link puro: mora no site oficial (redirect ligado). Fica por último.
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
                'redirect_to_site' => true, // link puro: abre o site direto
                'is_published' => true,
                'sort_order' => 4,
            ]
        );

        $this->command?->info('Projetos sincronizados: ShvIA (1) · GitHub Desktop (2) · ai-usagebar (3) · SShvTerm (4).');
    }
}
