<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Matomo Analytics (self-hosted em a.blue3.cloud). O snippet
    // (resources/views/partials/matomo.blade.php) só é injetado no <head> quando
    // 'enabled' é true E 'site_id' está definido. site_id 2 = "samirhv.com.br".
    'matomo' => [
        'enabled' => env('MATOMO_ENABLED', false),
        'url' => env('MATOMO_URL', 'https://a.blue3.cloud/'),
        'site_id' => env('MATOMO_SITE_ID'),
        'cookie_domain' => env('MATOMO_COOKIE_DOMAIN'),
    ],

    // Google tag (gtag.js), Google Analytics 4. The snippet
    // (resources/views/partials/google-tag.blade.php) is injected into the public
    // <head> only when 'tag_id' is non-empty; GOOGLE_TAG_ID= (empty) turns it off.
    // The default is the production measurement id, so a deploy ships the tag
    // without touching the server's .env. It is public in the page source, not a secret.
    'google' => [
        'tag_id' => env('GOOGLE_TAG_ID', 'G-WTE9C2ZB0V'),
    ],

    // GitHub View (admin): token + owner default do dashboard de visualização de
    // repositórios (porte do github-visualize). Token fine-grained com escopo
    // MÍNIMO: Contents:read + Actions:read. Ver .continue/migracao-github-visualize.md.
    'github' => [
        'token' => env('GITHUB_TOKEN'),
        'owner' => env('GITHUB_OWNER'),
        'timezone' => env('APP_TIME_ZONE'), // fuso p/ bucket dos charts (fallback: app.timezone)
    ],

];
