<?php

/*
| Credenciais de sincronização do Tura Notes, geridas pela tela do admin.
|
| O acoplamento a ESTE host é esperado, como no módulo AI-MEMORY: o
| `notes-server` roda nesta máquina, em loopback, atrás do vhost
| tura.samirhv.com.br. Quando o script não está instalado, a tela explica em vez
| de dar 500 — ver App\Services\TuraCredentials.
|
| O script e a linha de sudoers que ele exige vivem no repositório do Tura,
| em server/cotenant/ — a instalação está documentada no cabeçalho do script.
*/

return [

    // O wrapper revisado, chamado via `sudo -n -u <run_as>`. Não é o
    // `notes-server`: ver o comentário de TuraCredentials sobre por que a CLI
    // não pode ser alcançada direto.
    'wrapper' => env('TURA_CREDENTIAL_WRAPPER', '/usr/local/bin/tura-credential'),

    // O usuário de sistema dono de /var/lib/notes-server.
    'run_as' => env('TURA_CREDENTIAL_USER', 'notes'),

    // O workspace do servidor em que as credenciais desta tela são criadas.
    // O escopo é sempre o workspace inteiro: escopo por subpasta é recurso do
    // servidor e não desta tela, e uma opção que nenhuma interface escolhe é
    // uma opção que fica errada.
    'workspace' => env('TURA_WORKSPACE', 'personal'),

];
