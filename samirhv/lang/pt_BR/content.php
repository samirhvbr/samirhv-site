<?php

/*
| Portuguese for the prose that lives in the DATABASE.
|
| Intentionally almost empty: the database already holds Portuguese, and
| App\Support\Content falls back to it. An entry here would only ever be an
| OVERRIDE — a case where the Portuguese shown on the site should differ from
| what the admin stored. There is none today, and adding one without a reason
| would create a second source of truth for the same sentence.
|
| The keys are the same shape as lang/en/content.php.
*/

return [
    'projects' => [],
    'categories' => [],
    'file_labels' => [],
];
