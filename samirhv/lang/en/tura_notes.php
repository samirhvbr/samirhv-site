<?php

/*
| The Tura Notes section on /p/tura-notes — the Portuguese pair is
| lang/pt_BR/tura_notes.php.
|
| THIS IS PRODUCT COPY, NOT THE PROCEDURE. The full walkthrough is the
| repository's docs/SELF-HOSTING.md, and this text is written from it. Repeating
| the procedure here would create a second copy to go stale in silence; what
| belongs here is what a person needs in order to DECIDE.
|
| The mode labels are the strings the application shows
| (apps/notes-app/src/i18n/en.json, keys device.mode.*). If the app renames a
| field, the app is right and this file is old.
| Checked against the application's panel on 16/09/2026.
*/

return [
    'title' => 'Sync across machines, on your own server',
    'lead' => 'The application works on one machine with no server at all — that is the normal case. When you want the same notes on the laptop and the desktop, Tura syncs through a server that is yours: no account here, no cloud of ours, nobody in the middle.',
    'lead_2' => 'The server keeps the notes the way the application does: .md files in ordinary directories, on a machine you control. Nothing becomes a proprietary format by being synchronized.',

    'honest_label' => 'Before you decide',
    'honest_title' => 'There is no end-to-end encryption',
    'honest_desc' => 'The server reads its own notes. Anyone with access to its filesystem has your notes, and so does anyone who takes the machine. That is a stated boundary of the project rather than a gap to route around — do not put notes on a server you would not trust with the notes.',
    'honest_desc_2' => 'And sync is not a backup. It copies your mistakes to the other machine promptly and correctly. Back the server data tree up separately, with the server stopped.',

    'setup_title' => 'What goes on the server',
    'setup_desc' => 'You need a Linux machine that is on when you want to sync, a DNS name pointing at it, and TCP 80 and 443 reachable. With :compose it is three commands, and the certificate is obtained for you.',
    'setup_commands' => "git clone https://github.com/samirhvbr/tura-notes.git\ncd tura-notes\nexport NOTES_DOMAIN=notes.example.com\ndocker compose -f server/compose.yml up -d --build",
    'setup_after' => 'What remains is creating the workspace, creating one credential per device, and carrying the credential file to that device by a means you trust. It is a bearer secret: whoever reads it is the device.',

    'pair_title' => 'Pair the application',
    'pair_panel' => 'Device sync',
    'pair_desc' => 'Close your workspace, open the :panel strip at the top of the window and fill in the server address, the workspace, the credential file and the mode. The mode is the part to get right, because it states what the two sides already hold.',
    'pair_col_mode' => 'Mode',
    'pair_col_when' => 'When to pick it',
    'mode_upload' => 'Send local folder to empty inbox',
    'mode_upload_when' => 'The first device. Your notes go up, and that workspace inbox on the server has to be empty.',
    'mode_download' => 'Receive into empty local folder',
    'mode_download_when' => 'A second device with nothing to keep. Point it at an empty folder.',
    'mode_reconcile' => 'Reconcile existing folders',
    'mode_reconcile_when' => 'Both sides already have notes. You get a preview of what would be sent, received, linked as already identical or flagged as a conflict — and nothing happens until you confirm.',
    'pair_apply' => 'Transfer moves revisions; writing the received files into your folder is a separate, explicit step, with the workspace closed. Nothing arrives underneath you while you type, which is why the two are separate.',

    'c_credential' => 'One credential per device',
    'c_credential_desc' => 'Two devices sharing one credential cannot be told apart, so revoking the one you lost cuts off the one you kept. Lost a device? Revoke its credential — it takes effect at once, with no restart, and the others carry on.',
    'c_backup' => 'Back up the whole tree',
    'c_backup_desc' => 'With the server stopped, and the entire data tree — not just the notes directory and not just a database file. Always restore into a new directory and check it before switching over.',
    'c_existing' => 'A server that already has a site',
    'c_existing_desc' => 'If 80 and 443 already belong to another site, Tura runs on loopback behind the nginx, Apache or Caddy that is already there. The guide carries the systemd unit and all three templates.',

    'note' => 'The full walkthrough — including what to do when it does not work — is in :guide, in the repository.',
];
