<?php

/*
| The meuip.rs section of /p/meuip — partials/projects/meuip.blade.php.
|
| Checked against the application's own source (index.php, views/home.php,
| README.md) on 2026-09-14. The endpoint table below is the routing table of
| that front controller; if the two ever disagree, the front controller is
| right and this file is stale.
*/

return [
    'title' => 'One address, two answers',
    'lead' => 'Ask :curl and you get the bare address and a newline — parseable, with nothing to strip. Open the same url in a browser and you get the page: the address in large type, who your provider is, where the route puts you, and the commands to do it all from the terminal.',
    'lead_2' => 'That is the whole idea. A "what is my IP" that a script can use is a different service from one a person can read, and most sites make you pick. This one reads the :accept header and answers the question in the shape the caller can actually use.',

    'terminal_title' => 'From the terminal',
    'terminal_note' => 'No key, no account, no rate-limit dance. The output is one line, so it goes straight into a variable.',

    'endpoints_title' => 'Endpoints',
    'endpoints_route' => 'Route',
    'endpoints_answer' => 'What it answers',

    'ep_root' => 'Your public address — plain text for a client, the full page for a browser.',
    'ep_ip' => 'The address on its own, always as text.',
    'ep_asn' => 'The autonomous system your route comes out of.',
    'ep_isp' => 'The provider that announces that AS.',
    'ep_country' => 'Country code.',
    'ep_region' => 'State or region.',
    'ep_coord' => 'Latitude and longitude — on the page, a link to the map.',
    'ep_all' => 'Everything above in one JSON object.',
    'ep_trace' => 'Traceroute: the route to your own address, or to a host you name.',
    'ep_doc' => 'The documentation, same content in both shapes.',

    'trace_label' => 'Looking glass',
    'trace_title' => 'A traceroute that runs on the server, not on your machine',
    'trace_desc' => 'Corporate networks block ICMP, and a traceroute run from inside one tells you about that network, not about the route. :trace runs the hops from the server and streams them to the page as they come back — no waiting for the whole run to finish — and holds the route open in a continuous :mtr-style reading when you ask it to.',
    'trace_desc_2' => 'It answers in both shapes like everything else: a browser gets the live page, :curl gets the plain route.',

    'facts_title' => 'What it does not do',
    'fact_account' => 'No account',
    'fact_account_desc' => 'Nothing to sign up for and nothing to log in to. There is no paid tier that unlocks the other endpoints.',
    'fact_key' => 'No API key',
    'fact_key_desc' => 'The endpoints are open. The geo lookup behind them is cached on the server, so a loop over :curl does not become a bill.',
    'fact_cookie' => 'No cookie',
    'fact_cookie_desc' => 'The page sets none. The theme you choose is kept in :storage, in your own browser, and never travels.',
    'fact_js' => 'No JavaScript required',
    'fact_js_desc' => 'The page is rendered on the server and reads the same with scripting off — the copy buttons and the live trace are what need it.',

    'note' => 'Built and run by Blue3, in PHP with no framework: a front controller, prepared statements, and a geo lookup over native cURL with a timeout and a cache. The v2.0 rewrite is what replaced the :shell of the original.',
];
