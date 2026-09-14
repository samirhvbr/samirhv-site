<?php

/*
| The ai-memory section of /p/ai-memory — partials/projects/ai-memory.blade.php.
|
| TWO PROJECTS, ONE PAGE, AND THE PAGE SAYS WHICH IS WHICH. ai-memory is Fabio
| Akita's (akitaonrails/ai-memory): the product being explained. ai-memory-web
| is ours (samirhvbr/ai-memory-web): the read-only panel over its index, and the
| reason this page has screenshots at all. Every claim below is attributed to
| one of the two — a page that blurred them would be taking credit for the
| first one.
|
| Checked against both repositories on 2026-09-14.
*/

return [
    /* ── ai-memory itself ─────────────────────────────────────────── */
    'title' => 'The memory your agent does not have',
    'lead' => 'Your coding agent already takes notes. They live on one machine, belong to that one agent, and are gone the moment you switch tools. :name is what sits on the other side of that wall: one shared memory that more than twenty harnesses — :claude, :codex, Cursor, Gemini CLI, OpenCode and others — read from and write to.',
    'lead_2' => 'Quit Claude Code mid-task, open Codex in the same directory, and the next agent picks up a real handoff: where you stopped, what you already tried and why it failed, what is still open. It is a typed protocol, claimed exactly once — not a note you hope the next model reads.',

    'features_title' => 'What makes it different',

    'f_agents' => 'It follows you across agents',
    'f_agents_desc' => 'Twenty-plus harnesses feed one memory. The handoff is owned, typed and claimed once, so two agents cannot both pick up the same baton.',
    'f_machines' => 'It follows you across machines',
    'f_machines_desc' => 'The memory lives in a server you run — the same laptop, a homelab box, wherever. The project you left on the desktop is the project you resume on the laptop.',
    'f_markdown' => 'Your memory is plain markdown',
    'f_markdown_desc' => 'The source of truth is a git-backed wiki of ordinary :md files. Grep it, open it in Obsidian, edit it by hand. The database is a derived index that can be rebuilt from the files.',
    'f_silent' => 'It records the work, not a ceremony',
    'f_silent_desc' => 'Lifecycle hooks capture what actually happened — prompts, tool calls, session boundaries — sanitised at a typed privacy boundary before anything is stored. There is no "remember this" to say.',
    'f_nollm' => 'The default path costs no tokens',
    'f_nollm_desc' => 'Capture, search and handoffs all work with no API key at all. The LLM passes — consolidation, auto-improve — are the opt-in part, not the price of entry.',
    'f_team' => 'It works for a team',
    'f_team_desc' => 'Point everyone at one server and what one person\'s sessions learn, everyone\'s agents can retrieve. Multi-user auth, per-person attribution and an audit log are built in, not a paid tier.',

    /* ── ai-memory-web ─────────────────────────────────────────────── */
    'web_label' => 'The web panel',
    'web_title' => 'ai-memory-web — see what your agents remember',
    'web_desc' => 'ai-memory stores; :web shows. It is a read-only Laravel panel over the same SQLite index, on the same host, answering two questions: :q1, and :q2.',
    'web_desc_2' => 'It never writes, and that is enforced at the engine rather than promised in a comment: the connection is pinned with :pragma, the raw handle is private, and a test attempts a real write and asserts that it throws. ai-memory stays the only writer of its own index.',
    'web_q1' => 'what did the agents remember',
    'web_q2' => 'how was it collected',

    'screens_title' => 'Nine screens over one database',
    'screens_lead' => 'A dashboard with live totals and a history that outlives an ai-memory reset, the projects and workspaces the memory is spread across, the consolidated wiki pages and their versions, the sessions and the facts each one learned, the handoffs between agents, and full-text search through ai-memory\'s own FTS5 index.',

    'shot_dashboard' => 'Dashboard',
    'shot_dashboard_desc' => 'Totals and daily activity at a glance — observations, pages, sessions, open handoffs.',
    'shot_dashboard2' => 'History and active projects',
    'shot_dashboard2_desc' => 'Memory growth over time, and which projects are actually producing it.',
    'shot_projects' => 'Projects',
    'shot_projects_desc' => 'Every project side by side: pages, sessions and observations each one holds.',
    'shot_workspaces' => 'Workspaces',
    'shot_workspaces_desc' => 'How the collected memory is distributed across workspaces.',
    'shot_pages' => 'Knowledge pages',
    'shot_pages_desc' => 'The consolidated wiki, browsable by project and memory tier — with the version history of each page.',
    'shot_sessions' => 'Agent sessions',
    'shot_sessions_desc' => 'Which agent ran, for how long, and the observations that session collected.',
    'shot_views' => 'Observations',
    'shot_views_desc' => 'The raw facts, filtered by type, importance, project and period.',
    'shot_handoffs' => 'Handoffs',
    'shot_handoffs_desc' => 'The context transfers between agents, and the status of each one.',
    'shot_search' => 'Search',
    'shot_search_desc' => 'Full-text search across the remembered knowledge, with the matching excerpt on every hit.',

    'shots_note' => 'These captures are of the Portuguese AI-MEMORY integration in this site\'s own admin, which is where the standalone app was extracted from. :web itself is in English and carries its own navigation.',

    /* ── Repositories ──────────────────────────────────────────────── */
    'repos_title' => 'The two repositories',
    'repo_core_desc' => 'ai-memory itself — Rust, MIT, by Fabio Akita. Installation and configuration are in its README.',
    'repo_web_desc' => 'The read-only web panel. PHP 8.3+ with pdo_sqlite, no Node and no build step.',
];
