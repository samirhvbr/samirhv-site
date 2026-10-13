<?php

/*
| The LEB-300 page: the second level of LEB.
|
| Built like the LEB-100 page (a hero with the leaders, the results with the facts, the filters and the
| board, then the caveats), and deliberately thin in what it says of the instance. LEB-300 is an ACTIVE
| instance: the page publishes only the aggregate (the total, the grade and the category scores of each
| agent, with cost and time), never a run's delivery, verdict or flaw-by-flaw results. The instance and its
| answer key are public on GitHub, and the page says so, and says the bench machines cannot reach GitHub.
| The numbers are never written here: they come from the synced results file
| (App\Support\AiBenchmark::aggregate). Until the file carries an aggregate the page is a status page,
| and the `*_result` keys are the same sections once it does. shvia.org renders this same copy
| (tools/sync-ai-benchmark.py reads this file), so a change here reaches both sites.
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
    // The note above the title, the twin of the one the LEB-100 page carries about this level.
    'leb100_note' => 'The first level, :link, is the reference instance: every agent evaluated so far has run on it, and every run is published.',
    'leb100_note_link' => 'LEB-100',
    'lead' => 'The second level of LEB: an application of about 3,000 lines, in multiple files, whose flaws cross files. The instance is active, so only the aggregate of each agent\'s runs is published. Its answer key is public on GitHub with the instance, and the machines that run the agents cannot reach GitHub. How a run works, how it is scored and how this level differs from LEB-100 is on the :benchmark page.',

    'note_title' => 'There are no LEB-300 results yet.',
    'note_body' => 'This page says where it stands and what will appear here. Nothing on it is a score.',

    'result_title' => 'The results so far',
    'results_intro' => 'One line per agent: the total, the grade, the score in each category, how many runs it rests on, and the cost and the time. Every agent solved the same package, byte for byte, so the numbers compare like for like.',
    // The instance has no public description: it is active, so the page says of it what the level says.
    'instance_name' => 'An application of about 3,000 lines',
    'result_note_title' => 'An exploratory pilot.',
    'result_note_body' => 'An official score is the median of three runs of an agent. A line that rests on fewer runs is not official: it says how many it has, and is listed all the same, as on the LEB-100 page. The instance itself is still a pilot: its difficulty has not been homologated. Only the aggregate is published; each run\'s delivery, verdict and flaw-by-flaw results stay in a private archive. The answer key is public on GitHub with the instance, since 12 October 2026, after the first runs; the machines that ran the agents cannot reach GitHub, and every run records its model\'s training cutoff.',
    'result_note_body_three' => 'Each score is the median of three runs of an agent, as the protocol asks. The instance itself is still a pilot: its difficulty has not been homologated. Only the aggregate is published; each run\'s delivery, verdict and flaw-by-flaw results stay in a private archive. The answer key is public on GitHub with the instance, since 12 October 2026, after the first runs; the machines that ran the agents cannot reach GitHub, and every run records its model\'s training cutoff.',
    'facts_key' => 'key public',
    'session_time' => 'Session :min min',
    'more_note' => 'A written reading of the aggregate; it is not part of the score, and it names no flaw.',
    'session_time_help' => 'From the first message to the end of the session, without the minutes the operator took between the two stages.',

    'stands_body' => 'The instance is being prepared. Its first runs are internal, and they are there to check the procedure, so they are not published.',
    'stands_body_result' => 'The instance is in an exploratory pilot. The results above are the ones published so far, and other agents follow. Its first runs also served to check the procedure.',

    'caveats_title' => 'What the record does not have',
    'caveat_checkpoint' => 'No checkpoint was taken between the two stages of the task in any of the runs, so it is not on record that the model stayed the same through the first stage. The transcripts show a single model.',
    'caveat_matrix' => 'The task text the agent read names the previous scoring-matrix hash. The matrix this run is scored against differs from it only by a header field that marks the instance as active. The scoring is the same.',
    'caveat_client' => 'The agents did not all run in the same client, Claude Code or OpenCode, each with its own tools. The client of every run is in its record.',

    'publish_title' => 'What will be published',
    'publish_body' => 'When there are results, this page will show one line per agent: the total, the grade, the score in each category, how many runs it rests on, and the cost and the time. It will not show a run\'s delivery, its verdict or its flaw-by-flaw results, which stay in a private archive. The instance and its answer key are public on GitHub; the machines that run the agents cannot reach GitHub.',
    'publish_title_result' => 'What is published',
    'publish_body_result' => 'This page shows one line per agent: the total, the grade, the score in each category, how many runs it rests on, and the cost and the time. It never shows a run\'s delivery, its verdict or its flaw-by-flaw results, which stay in a private archive. The instance and its answer key are public on GitHub; the machines that run the agents cannot reach GitHub.',

    'more_body' => 'The protocol, the scoring and the tools are public in the :repo. How a run works and how the two levels differ is on the :benchmark page, and the results of the first level are on the :leb100.',
    'repo_link' => 'ai-benchmark repository',
    'benchmark_link' => 'Benchmark',
    'leb100_link' => 'LEB-100 page',
    'leb100_results' => 'LEB-100 results',
];
