<?php

/*
| The "models behind it" section, shown only on /p/shvia.
|
| It tells the hybrid story: local models on-prem (the prompt never leaves the
| network) and an OPTIONAL cloud provider with your own key (BYOK, with PII
| masking for Brazil's LGPD). The whole sentence is one key with markers for its
| bold parts — a claim about where data goes must not end up half translated.
*/

return [
    'title' => 'The models behind it',
    'lead' => 'ShvIA is :hybrid. By default it answers with :local models running inside Blue3 — the prompt never leaves the network. When the work calls for a frontier model, you connect a :cloud provider with your own key.',
    'hybrid' => 'hybrid',
    'local' => 'local',
    'cloud' => 'cloud',

    'onprem_label' => 'Local · on-prem',
    'onprem_pill' => 'the prompt never leaves Blue3',
    'onprem_sub' => 'Blue3 models served by :ollama on internal infrastructure — no cost per token, no data leaving the network.',

    'cloud_label' => 'Cloud · optional',
    'cloud_note' => 'with your own key (BYOK)',
    'cloud_more' => '+ others',
    'cloud_caption' => 'When you use the cloud, :data_leaves — always with PII masking for Brazil\'s LGPD, and only for whoever configures their own key.',
    'data_leaves' => 'data leaves Blue3',
];
