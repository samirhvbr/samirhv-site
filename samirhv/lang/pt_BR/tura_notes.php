<?php

/*
| A seção do Tura Notes em /p/tura-notes — o par em inglês é lang/en/tura_notes.php.
|
| ESTA É CÓPIA DE PRODUTO, NÃO O PROCEDIMENTO. O passo a passo completo é o
| docs/SELF-HOSTING.md do repositório, em inglês, e é dele que este texto foi
| escrito. Repetir o procedimento aqui criaria uma segunda cópia para envelhecer
| em silêncio; o que fica aqui é o que a pessoa precisa saber para DECIDIR.
|
| Os rótulos de modo são as strings que o aplicativo mostra em português
| (apps/notes-app/src/i18n/pt-BR.json, chaves device.mode.*). Se o app renomear
| um campo, quem está certo é o app e é este arquivo que está velho.
| Conferido contra o painel do aplicativo em 16/09/2026.
*/

return [
    'title' => 'Sincronizar entre máquinas, no seu servidor',
    'lead' => 'O aplicativo funciona numa máquina só, sem servidor nenhum — esse é o caso normal. Quando você quer as mesmas notas no notebook e no desktop, o Tura sincroniza através de um servidor que é seu: não há conta aqui, não há nuvem nossa e não há ninguém no meio.',
    'lead_2' => 'O servidor guarda as notas do mesmo jeito que o aplicativo guarda: arquivos .md em pastas comuns, numa máquina que você controla. Nada vira formato proprietário por estar sincronizando.',

    'honest_label' => 'Antes de decidir',
    'honest_title' => 'Não há criptografia ponta a ponta',
    'honest_desc' => 'O servidor lê as próprias notas. Quem tiver acesso ao sistema de arquivos dele tem as suas notas, e quem levar a máquina também. Isso é um limite declarado do projeto, não uma falha a contornar — não coloque notas num servidor em que você não confiaria com as notas.',
    'honest_desc_2' => 'E sincronizar não é backup. Ele copia os seus erros para a outra máquina com pontualidade e exatidão. Faça backup da árvore de dados do servidor à parte, com ele parado.',

    'setup_title' => 'O que subir no servidor',
    'setup_desc' => 'Você precisa de uma máquina Linux ligada na hora de sincronizar, um nome de DNS apontando para ela e as portas 80 e 443 alcançáveis. Com :compose são três comandos, e o certificado é obtido sozinho.',
    'setup_commands' => "git clone https://github.com/samirhvbr/tura-notes.git\ncd tura-notes\nexport NOTES_DOMAIN=notes.exemplo.com\ndocker compose -f server/compose.yml up -d --build",
    'setup_after' => 'Depois disso falta criar o workspace, criar uma credencial para cada aparelho e levar o arquivo da credencial até ele por um meio em que você confie. A credencial é um segredo ao portador: quem lê é o aparelho.',

    'pair_title' => 'Parear o aplicativo',
    'pair_panel' => 'Sincronização entre dispositivos',
    'pair_desc' => 'Feche a pasta de trabalho, abra a faixa :panel no topo da janela e preencha o endereço do servidor, o workspace, o arquivo de credencial e o modo. O modo é a parte que importa acertar, porque ele diz o que os dois lados já têm.',
    'pair_col_mode' => 'Modo',
    'pair_col_when' => 'Quando escolher',
    'mode_upload' => 'Enviar pasta local para inbox vazio',
    'mode_upload_when' => 'O primeiro aparelho. Suas notas sobem, e o inbox daquele workspace no servidor precisa estar vazio.',
    'mode_download' => 'Receber em pasta local vazia',
    'mode_download_when' => 'Um segundo aparelho sem nada a preservar. Aponte para uma pasta vazia.',
    'mode_reconcile' => 'Reconciliar pastas existentes',
    'mode_reconcile_when' => 'Os dois lados já têm notas. Você recebe uma prévia do que seria enviado, recebido, vinculado por já ser igual ou marcado como conflito — e nada acontece até você confirmar.',
    'pair_apply' => 'O transporte move revisões; escrever os arquivos recebidos dentro da sua pasta é um passo separado e explícito, com a pasta de trabalho fechada. Nada aparece embaixo de você enquanto digita, e é por isso que os dois são separados.',

    'c_credential' => 'Uma credencial por aparelho',
    'c_credential_desc' => 'Dois aparelhos com a mesma credencial não se distinguem, e aí revogar o que você perdeu corta o que você manteve. Perdeu um aparelho? Revogue a credencial dele — vale na hora, sem reiniciar nada, e os outros seguem funcionando.',
    'c_backup' => 'Backup da árvore inteira',
    'c_backup_desc' => 'Com o servidor parado, e a árvore de dados toda — não só a pasta de notas e não só um arquivo de banco. Restaure sempre para um diretório novo e confira antes de trocar.',
    'c_existing' => 'Servidor que já tem site',
    'c_existing_desc' => 'Se as portas 80 e 443 já são de outro site, o Tura roda em loopback atrás do nginx, Apache ou Caddy que já está ali. O guia traz a unidade do systemd e os três modelos prontos.',

    'note' => 'O passo a passo completo — incluindo o que fazer quando não funciona — está em :guide, no repositório.',
];
