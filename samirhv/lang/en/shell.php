<?php

/*
| The chrome: header, menu, footer. Shared by every public page.
|
| `lang/en` is the source of truth for the key set — a key that exists here and
| not in pt_BR renders in English, which is legible; the reverse renders the raw
| key, which is not. Keys are semantic, never the phrase itself: the phrase
| changes with the copy, the key names the slot.
*/

return [
    'home_aria' => 'Samirhv — home',
    'menu_open' => 'Open menu',
    'nav_main' => 'Main navigation',
    'skip_to_content' => 'Skip to content',

    'home' => 'Home',
    'projects' => 'Projects',
    'downloads' => 'Downloads',
    'explore_releases' => 'Browse releases',

    'title_suffix' => 'Projects',
    'meta_description' => 'Projects and tools by Samir Hanna Verza, available to download.',
    'og_image_alt' => 'The samirhv wordmark over a dark grid, with the names of the four applications: ShvIA, GitHub Desktop, ai-usagebar and SShvTerm.',

    'tagline' => 'Projects and tools by Samir Hanna Verza, available to download. Technology, development and Linux.',
    'nav' => 'Navigation',
    'contact' => 'Contact',
    'admin_panel' => 'Admin panel',

    'copyright' => '© :year Samirhv. All rights reserved.',
    'made_with' => 'built with Laravel + Debian',

    // The label names the language in the OTHER tongue, so whoever landed on
    // the wrong side still recognises their own word for it.
    'language' => 'Language · Idioma',
];
