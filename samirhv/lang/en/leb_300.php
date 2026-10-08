<?php

/*
| The LEB-300 page: the second level of LEB exists and has no results yet.
|
| Written by hand and deliberately thin. While LEB-300 has no published
| results it says what exists and nothing about the instance itself: no
| package, no defect list, no answer key. When an aggregate is published, the
| numbers will come from the synced results file like the LEB-100 page, and this
| copy changes with it.
*/

return [
    'title' => 'LEB-300 · AI Benchmark',
    'meta_description' => 'LEB-300 is the next level of the LEB engineering benchmark, an application of about 3,000 lines. It is in preparation: no results are published yet.',

    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'LEB-300',
    'heading_accent' => 'in preparation',

    'note_title' => 'There are no LEB-300 results yet.',
    'note_body' => 'This page says where it stands and what will appear here. Nothing on it is a score.',

    'what_title' => 'What it is',
    'what_body' => 'LEB is the LLM Engineering Benchmark: an agent is handed a working system with defects planted in it, and is scored on what it finds, explains and fixes without breaking what already worked. LEB-100 is the first level, an application of about 300 lines. LEB-300 is the level above, an application of about 3,000 lines.',

    'stands_title' => 'Where it stands',
    'stands_body' => 'The instance is being prepared. Its first runs are internal, and they are there to check the procedure, so they are not published.',

    'publish_title' => 'What will be published',
    'publish_body' => 'When there are results, this page will show one line per agent: the total, the grade, the score in each category, how many runs it rests on, and the cost and the time. It will not show the list of planted defects, the code under test or the answer key. The same instance has to keep measuring new agents, so those stay private.',

    'more_title' => 'Where to read more',
    'more_body' => 'The protocol, the scoring and the tools are public in the :repo. The results of the first level are on the :leb100.',
    'repo_link' => 'ai-benchmark repository',
    'leb100_link' => 'LEB-100 page',
];
