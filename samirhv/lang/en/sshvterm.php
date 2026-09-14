<?php

/*
| The SShvTerm section of /p/sshvterm — partials/projects/sshvterm.blade.php.
|
| Written from what the product itself publishes at sshvterm.com, checked on
| 2026-09-14. Nothing here comes from the private repository: this is a catalogue
| page, and a catalogue page that described unreleased internals would be
| publishing them.
|
| The installers are NOT served from this site. Every path out of this section
| ends at sshvterm.com, which is the download channel.
*/

return [
    'title' => 'An SSH client that carries its own agent',
    'lead' => 'Hosts, identities, keys, groups, tags, snippets and port-forwarding rules in one desktop client, over a real terminal — :xterm with GPU-accelerated rendering, tabs and splits of up to four panes, password or key, and automatic reconnection when the network drops.',
    'lead_2' => 'What it adds to that is an AI agent that operates the terminal: you ask in plain language, it proposes the command, and — once you approve — it types it into the visible session while you watch.',

    'sync_label' => 'Zero-knowledge sync',
    'sync_title' => 'The server holds your hosts and cannot read them',
    'sync_desc' => 'Hosts, identities, keys and snippets sync end to end. The sensitive fields are encrypted on your own machine — :crypto — and the key never leaves it: the sidecar holds it in memory and nothing else does. Install SShvTerm on another machine, sign in, and everything reappears; the server that carried it never saw any of it.',
    'sync_desc_2' => 'And that server can be yours. The sync backend is a separate, self-hostable piece, so "the cloud" here is a deployment choice rather than a condition of using the product.',

    'agent_label' => 'The AI agent',
    'agent_title' => 'It runs commands under a policy you wrote',
    'agent_desc' => 'Every command is :allow, :ask or :deny by your rule. A destructive action asks for confirmation, and nothing runs at all with execution switched off. The agent types into the visible terminal of the tab — you watch every keystroke — and both the conversation and the commands land in the audit log.',
    'agent_desc_2' => 'Stop interrupts during the call to the provider, not only between steps. Each turn reports its time, its tokens and its cost — the real figure when the provider reports one, marked with :approx when it is an estimate.',
    'agent_allow' => 'allow',
    'agent_ask' => 'ask',
    'agent_deny' => 'deny',
    'agent_providers' => 'Bring your own key: Claude (Anthropic), ShvIA (Blue3), xAI/Grok, local Ollama, LM Studio, vLLM and any OpenAI-compatible endpoint. Where the provider supports tools the model returns a structured call; where it does not, a text bridge extracts the command from a :run block and runs it down the same guarded path.',

    'features_title' => 'What else is in it',

    'f_sftp' => 'SFTP on the open connection',
    'f_sftp_desc' => 'Browse, upload and download reusing the SSH session you already have. A transfer queue with progress, several at once, cancellable while running.',
    'f_vpn' => 'WireGuard VPN per host',
    'f_vpn_desc' => 'Reach servers on private networks through a tunnel scoped to that connection — no root, and no touching the system VPN. If the tunnel does not come up, nothing leaks: it fails closed.',
    'f_keys' => 'Keys under control',
    'f_keys_desc' => 'Generate, import and organise Ed25519, ECDSA and RSA keys, and reuse an identity across hosts instead of copying it around.',
    'f_forward' => 'Visual port forwarding',
    'f_forward_desc' => 'Local (:local) and remote (:remote) tunnels tied to the session, without memorising the flags.',
    'f_import' => 'Bring your existing configuration',
    'f_import_desc' => 'Import hosts from OpenSSH (:config), PuTTY, MobaXterm and Termius, with a preview before anything is applied.',
    'f_snippets' => 'Snippets and autocomplete',
    'f_snippets_desc' => 'Save the commands you repeat and fire them in any session, with per-host startup snippets. Suggestions as you type, switchable off from the bar when they get in the shell\'s way.',

    'note' => 'Windows, macOS and Linux. The installers and the release notes are served from :site — this page is the description, not the download.',
];
