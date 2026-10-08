<?php

/*
| The LEB-300 page: the second level of LEB.
|
| Written by hand and deliberately thin. LEB-300 is an ACTIVE instance: the page says what exists and
| publishes only the aggregate (the total, the grade and the category scores of each agent, with cost and
| time), never a planted defect, a verdict, the code under test or the answer key. The numbers are never
| written here: they come from the synced results file (App\Support\AiBenchmark::aggregate). Until the
| file carries an aggregate the page is a status page, and the `*_result` keys are the same sections once
| it does. shvia.org renders this same copy (tools/sync-ai-benchmark.py reads this file), so a change here
| reaches both sites.
*/

return [
    'title' => 'LEB-300 · AI Benchmark',
    'meta_description' => 'LEB-300 is the next level of the LEB engineering benchmark, an application of about 3,000 lines. It is in preparation: no results are published yet.',
    'meta_description_result' => 'LEB-300 is the next level of the LEB engineering benchmark, an application of about 3,000 lines. An exploratory pilot: the aggregate results so far are published.',

    'back' => 'AI Benchmark',
    'kicker' => 'AI Benchmark · LEB',
    'heading' => 'LEB-300',
    'heading_accent' => 'in preparation',
    'heading_accent_result' => 'exploratory pilot',

    'note_title' => 'There are no LEB-300 results yet.',
    'note_body' => 'This page says where it stands and what will appear here. Nothing on it is a score.',

    'result_title' => 'The results so far',
    'result_note_title' => 'An exploratory pilot.',
    'result_note_body' => 'An official score is the median of three runs of an agent. A line that rests on fewer runs is not official: it says how many it has, and is listed all the same, as on the LEB-100 page. The instance itself is still a pilot: its difficulty has not been homologated. Only the aggregate is published: the planted defects, the verdict, the code under test and the answer key stay private.',
    'result_note_body_three' => 'Each score is the median of three runs of an agent, as the protocol asks. The instance itself is still a pilot: its difficulty has not been homologated. Only the aggregate is published: the planted defects, the verdict, the code under test and the answer key stay private.',
    'session_time' => 'Session :min min',
    'more_note' => 'A written reading of the aggregate; it is not part of the score, and it names no flaw.',
    'session_time_help' => 'From the first message to the end of the session, as the run record has it.',

    'stands_title' => 'Where it stands',
    'stands_body' => 'The instance is being prepared. Its first runs are internal, and they are there to check the procedure, so they are not published.',
    'stands_body_result' => 'The instance is in an exploratory pilot. The results above are the ones published so far, and other agents follow. Its first runs also served to check the procedure.',

    'caveats_title' => 'What the record does not have',
    'caveat_checkpoint' => 'No checkpoint was taken between the two stages of the task in any of the runs, so it is not on record that the model stayed the same through the first stage. The transcripts show a single model.',
    'caveat_matrix' => 'The task text the agent read names the previous scoring-matrix hash. The matrix this run is scored against differs from it only by a header field that marks the instance as active. The scoring is the same.',

    'caveat_client' => 'The agents did not all run in the same client, Claude Code or OpenCode, each with its own tools. The client of every run is in its record.',

    'publish_title' => 'What will be published',
    'publish_body' => 'When there are results, this page will show one line per agent: the total, the grade, the score in each category, how many runs it rests on, and the cost and the time. It will not show the list of planted defects, the code under test or the answer key. The same instance has to keep measuring new agents, so those stay private.',
    'publish_title_result' => 'What is published',
    'publish_body_result' => 'This page shows one line per agent: the total, the grade, the score in each category, how many runs it rests on, and the cost and the time. It never shows the list of planted defects, the code under test or the answer key. The same instance has to keep measuring new agents, so those stay private.',

    'more_title' => 'Where to read more',
    'more_body' => 'The protocol, the scoring and the tools are public in the :repo. How a run works and how the two levels differ is on the :benchmark page, and the results of the first level are on the :leb100.',
    'repo_link' => 'ai-benchmark repository',
    'benchmark_link' => 'Benchmark',
    'leb100_link' => 'LEB-100 page',
];
