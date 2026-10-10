# Changelog

Every release of this repository, newest first.

The heading of an entry **is** the subject of the commit that carried it —
`## X.Y.Z - short description in English (US)` — and `tools/release.sh` reads
this file to build the notes of the matching GitHub Release. The version comes
from `version.md` and is bumped in the same commit as the entry.

Entries before 0.6.0 were reconstructed from `git log` on 2026-09-05, when this
file was first written; their commit subjects were in Portuguese and stay that
way in the history, so the descriptions here are translations, not the original
subjects.

## 1.0.128 - The LEB-300 page lists Claude Opus 5.5 at high effort and a second run of Claude Sonnet 5.5 at high effort and of GPT-5.6-terra

ai-benchmark 0.2.137 publishes the LEB-300-A aggregate with twelve agents, and `samirhv/resources/data/ai-benchmark/results.json` is synced from it. Claude Opus 5.5 at high effort is new (848 of 1000, Gold, one run of three). Claude Sonnet 5.5 at high effort now has two runs (897 and 763) and is published at the lower, 763; GPT-5.6-terra at xhigh has two (625 and 574) and is published at 574 (Bronze). Data only: no view, copy, style or code changes. 298 tests run, 289 pass and 9 skip for environment reasons (no pdo_sqlite, running as root, no projects in the nav), none of them the LEB-300 tests.

## 1.0.127 - The LEB-300 page lists eleven agents: Claude Sonnet 5.5 at high effort leads, Claude Haiku 5.5 is added

ai-benchmark 0.2.136 publishes the LEB-300-A aggregate with eleven agents, and `samirhv/resources/data/ai-benchmark/results.json` is synced from it: Claude Sonnet 5.5 at high effort (897 of 1000, Gold, one run of three), Claude Opus 5.5 (886), Claude Sonnet 5.5 at xhigh (860), Kimi K3 (737), Claude Haiku 5.5 at xhigh (708, two runs, published at the lower), GLM-5.3 (688), DeepSeek V4.1 Flash (649), GPT-5.6-terra (625), GLM-5.2 (390), MiniMax-M3 (388, two runs) and Claude Haiku 4.5 (242, three). A line on fewer than three runs says so and is not official. One change in the code, because the two new agents have no run on LEB-100: `AiBenchmark::modelOf()`, which found the name of an agent's model in the LEB-100 runs, falls back to the `model` block the aggregate line now carries (name, provider, effort), so the line reads "Claude Haiku 5.5 · Anthropic · effort xhigh" and not the raw id; a file without the block still renders by the id. `Leb300PageTest` gains a test for both cases (298 tests). The views, the other copy and the styles do not change: the page keeps the visual of the last versions, with eleven lines, and the menu's second line and the Benchmark page's card read the count and the top score from the same file.

## 1.0.126 - The LEB-300 page lists nine agents: Claude Opus 5.5 leads, Claude Sonnet 5.5 is second

ai-benchmark 0.2.135 publishes the LEB-300-A aggregate with nine agents, and `samirhv/resources/data/ai-benchmark/results.json` is synced from it: Claude Opus 5.5 at xhigh (886 of 1000, Gold, one run of three), Claude Sonnet 5.5 (860), Kimi K3 (737), GLM-5.3 at high effort (688, served by OpenRouter), DeepSeek V4.1 Flash (649), GPT-5.6-terra (625), GLM-5.2 at high effort (390, served by Novita AI), MiniMax-M3 (388, two runs) and Claude Haiku 4.5 (242, three). A line on fewer than three runs says so and is not official. Nothing in the views, the copy or the styles changes: the page keeps the visual of the last versions, with nine lines, and the menu's second line and the Benchmark page's card read the count and the top score from the same file.

## 1.0.125 - The Tura Notes application can sign in through the site: a consent screen and a code exchange

The Tura Notes application gets its connection to the cloud today by someone minting a credential on `/admin/tura`, downloading a file and pointing the application at its path. On a phone there is no file to point at, and typing a sixty-character secret on a touch keyboard is how one ends up in a note or a screenshot. The owner asked that signing in bring the connection with it. This is the site's half of that, as specified in `docs/PAIRING.md` of the Tura repository (ADR-105 there): an OAuth 2.0 authorization code flow with PKCE, with this site as the authorization server.

Three routes. `GET /tura/pair` is the consent screen, behind the same `auth`, `admin` and `password.changed` middleware as the rest of the panel; it validates what the application sent (a client name, an S256 challenge of the right length and alphabet, an opaque state, a device label, and a redirect that is exactly `tura://pair`) and refuses anything else with a 400 before creating anything. `POST /tura/pair` is Allow or Deny: Allow mints a credential through the same `tura-credential` wrapper `/admin/tura` uses, with the six permissions the remote folder needs (including `search`, which the existing screen does not offer; `devices` stays out), keeps the secret for two minutes under a random code and hands control back to the application through `tura://pair?code=...&state=...`, on a page that also shows the address in a field to paste, for a browser with nothing to open the scheme. `POST /tura/pair/exchange` trades `{code, verifier}` for the credential, with no session, and is the only route exempt from CSRF.

What matters in it is the boundary. The secret is in no URL: the redirect carries only the code, which is useless without the verifier that never left the application, and that is exactly the attack a claimed `tura://` link would otherwise allow. The code is single use, removed before the verifier is checked and under a lock, because the database cache's `pull` is not atomic and "single use" would otherwise mean "single use when nobody races"; a wrong verifier spends it too. Every failure of the exchange is the same 400, so a spent code, an expired one, an unknown one and a wrong verifier cannot be told apart. The secret waits encrypted under a cache key that is the hash of the code, and reaches no log, queue, session or error message; the audit records the device's name only. Every page forbids caching, framing and referrers, and `/tura/*` is not counted as a visit.

To switch it on, set `TURA_ORIGIN` in `.env` (it defaults to `https://tura.samirhv.com.br`) and deploy; the wrapper and the sudoers line already in place are enough. `docs/TURA-PAIRING.md` says the rest. Forty-three tests cover who may see the screen, each kind of malformed request, what Allow mints and with which permissions, that the secret is never in a page or a log, the single use, the wrong verifier, expiry and the per-IP limit; the whole suite passes (288 of 297, the rest skipped as before).

## 1.0.124 - The session time on the LEB-300 page leaves out the operator's wait between the stages

The operator's wait between the end of stage 1 and the stage 2 message is not the agent's time, and the LEB-300 session times had it inside them (20 minutes in GPT-5.6-terra's, 10 in Kimi K3's). ai-benchmark 0.2.134 subtracts it, measured for all nine runs from their own logs, and `samirhv/resources/data/ai-benchmark/results.json` is synced from it: the lines now read Claude Sonnet 5.5 42.1 minutes (was 54.8), Kimi K3 69.4 (79.8), DeepSeek V4.1 Flash 35.6 (38.6), GPT-5.6-terra 32.1 (52.2), MiniMax-M3 42.3 (43.7) and Claude Haiku 4.5 12.4 (13.4); the runs listed under each card follow. Scores, grades and costs do not move. The tooltip of the session time, on the card and in the run list, now says what it leaves out ("without the minutes the operator took between the two stages"); the run list used the LEB-100 wording and uses the LEB-300 one. Nothing else in the views, the copy or the styles changes.

## 1.0.123 - The LEB-300 page lists six agents: Kimi K3 is second

ai-benchmark 0.2.133 publishes the LEB-300-A aggregate with six agents, and `samirhv/resources/data/ai-benchmark/results.json` is synced from it: Claude Sonnet 5.5 (860 of 1000, Gold, one run of three), Kimi K3 at high effort (737, Silver, one run, served by Novita AI, not by Moonshot's own API as in LEB-100), DeepSeek V4.1 Flash (649, one run), GPT-5.6-terra (625, one run), MiniMax-M3 (388, two runs) and Claude Haiku 4.5 (242, three). A line on fewer than three runs says so and is not official. Nothing in the views, the copy or the styles changes: the page keeps the visual of the last versions, with six lines, and the menu's second line and the Benchmark page's card read the count and the top score from the same file.

## 1.0.122 - The LEB-300 page lists five agents: Claude Sonnet 5.5 leads, GPT-5.6-terra is third

ai-benchmark 0.2.132 publishes the LEB-300-A aggregate with five agents, and `samirhv/resources/data/ai-benchmark/results.json` is synced from it: Claude Sonnet 5.5 at xhigh (860 of 1000, Gold, one run of three), DeepSeek V4.1 Flash (649, one run), GPT-5.6-terra at xhigh (625, one run, from Codex CLI, which records no cost, so its line has none), MiniMax-M3 (388, two runs) and Claude Haiku 4.5 (242, three). A line on fewer than three runs says so and is not official. Nothing in the views, the copy or the styles changes: the page is the one the last two versions built, with five lines instead of three, and the menu's second line ("5 agents · top 860") and the Benchmark page's card read the count and the top score from the same file. The written readings travel in the aggregate, as before.

## 1.0.121 - The AI Benchmark menu says how many agents each instance ranks and its top score

Under each instance of the AI Benchmark menu, a second line says how many agents it ranks and the top score: "37 agents · top 809" under LEB-100-A and "3 agents · top 649" under LEB-300-A today, from the same synced results file the pages read. A view composer (`shareNavBenchmark` in `AppServiceProvider`) hands the layout the count and the leader of each instance, and `shell.ai_benchmark_state` writes the line in both languages, singular and plural. An instance with nothing published gets no line, so the menu never shows a zero that means "no data". The menu is the way into the results, and now says what they hold before the click. `BenchmarkMenuTest` pins the line in both languages, with a faked file and with the real one, on every public page that needs no database row.

## 1.0.120 - The LEB-300 page is built like the LEB-100 page: the leaders, the facts, the filters and the caveats

The LEB-300 page had the structure of a status page dressed as a results page: a smaller title with inline styles and the accent on the same line, no lead, no call to action, no leaders beside the title, no facts, no vendor filter or category order, the caveats as a plain list, section titles a size smaller, and a container narrower than the LEB-100 page's, so its cards came out narrower too. On a phone the position of each card was missing, because the LEB-100 cards carry it in `data-rank` and the LEB-300 cards did not.

It is now built on the structure of the LEB-100 page, with the same classes and the same stylesheet. The hero has the note on the first level (the twin of the one the LEB-100 page carries about this level), the lead, "See the results" and "Method on GitHub", and the leaders' card ("Top 3 so far", up to six, as on LEB-100). The results have the instance title, the pilot note as the instance's description, the facts the aggregate carries (mode and turns, edition, "key private" and the matrix hash), the vendor filter and the category order, and the board, where every second card is a shade off the surface, each name carries `data-rank`, and each card carries `data-scores` so the order chips work. "What the record does not have" is the caveat cards. The page closes with what is published, where the instance stands and where to read more, next to the buttons to the method, the specification and the LEB-100 results. Without an aggregate the page keeps the hero and the closing section and shows the status note in place of the leaders, the board and the caveats.

The copy in `lang/{en,pt_BR}/leb_300.php` gains `lead`, `leb100_note`, `leb100_note_link`, `results_intro`, `instance_name`, `facts_key` and `leb100_results`, and loses `stands_title`, which is no longer a heading. shvia.org renders this same copy through `tools/sync-ai-benchmark.py`. `Leb300PageTest` follows the page and gains two tests, on the status state and on the filter and order chips.

## 1.0.119 - A LEB-300 card opens on a written reading, as a LEB-100 card does

ai-benchmark 0.2.131 puts two things in each agent of the LEB-300-A aggregate: the total, cost and time of each run, and a short written reading in English and Brazilian Portuguese. The LEB-300 page shows them the way the LEB-100 page does: a click anywhere on a card, or on "Comment and details", opens below it the reading, the categories at full marks and at zero, and "Run by run" with each run's total, session time and cost. The line "2 of 3 runs" now lists the totals of the runs, as on LEB-100, and the page loads the script that opens a card on a click. The readings talk of categories, scores, runs, cost and time and name no flaw: LEB-300 is an active instance, so the data has no field for one and the exporter refuses a flaw id or a text of the matrix in a reading. A file synced before 0.2.131, without `runs` or `comment`, still renders (the card opens on what it has). `Leb300PageTest` gains two tests (253 in all).

## 1.0.118 - The LEB-300 page lists three agents, each with the runs it rests on

ai-benchmark 0.2.130 publishes the LEB-300-A aggregate with three agents: DeepSeek V4.1 Flash (649 of 1000, Silver, one run of three), MiniMax-M3 (388, Reprovada, two runs of three) and Claude Haiku 4.5 (242, Reprovada, three). `samirhv/resources/data/ai-benchmark/results.json` is synced from it, and the Benchmark page's card now says three agents are ranked and who leads.

The copy that assumed one agent is rewritten in `lang/{en,pt_BR}/leb_300.php`: the note says that a line resting on fewer than three runs is not official, says it is listed all the same as on the LEB-100 page, and says each score is the median of three runs only when every line has three; the title is "The results so far"; and the instance is described as being in an exploratory pilot whose results are "the ones published so far". A third caveat says the agents did not all run in the same client (Claude Code or OpenCode, each with its own tools). The note on the LEB-100 page says the aggregate results are published, not the first one. `Leb300PageTest` gains a test with two agents on different numbers of runs (251 tests).

## 1.0.117 - AI Benchmark is a menu: a Benchmark page, then LEB-100-A and LEB-300-A

The `AI Benchmark` entry of the header opens a dropdown, built like Projects (a button that opens on hover, focus and click, `aria-expanded`, the same script), with three pages:

- **Benchmark** (`/ai-benchmark`) is new. It holds the explanation that used to sit at the top of the LEB-100 page (why another benchmark, how a run works, how it is scored, where the agents run), a table that sets LEB-100-A and LEB-300-A side by side (size, what each tests, difficulty, the run, the answer key, what is published, where it stands) and a card for each instance with the number of agents ranked, the top score and a link. What the table says of LEB-300-A is what the public repository already says of its level; nothing of the active instance is added.
- **LEB-100-A** (`/ai-benchmark/leb-100`) is the page that was at `/ai-benchmark`, without the explanation: hero, leaderboard, flaw table, reading, caveats and the audit downloads.
- **LEB-300-A** (`/ai-benchmark/leb-300`) loses its "What it is" paragraph, which the Benchmark page now says, and links to both other pages.

`/ai-benchmark` no longer shows the leaderboard: a link to the old address lands on the Benchmark page, one click from the results. The sitemap lists the new page in both languages, `lang/{en,pt_BR}/leb_100.php` is new, and the explanation keeps its keys in `ai_benchmark.php`. 250 tests pass (`BenchmarkPageTest` is new; `AiBenchmarkPageTest` became `Leb100PageTest`).

## 1.0.116 - The route imports follow the code-style order

1.0.115 put `use App\Http\Controllers\Leb300Controller;` out of the alphabetical order Pint enforces, so the "Check code style" step of CI failed on that commit while the tests passed. `routes/web.php` is reordered by Pint and nothing else changes.

## 1.0.115 - The LEB-300 page shows the first aggregate result, as LEB-100 shows its own

`/ai-benchmark/leb-300` and `/pt-br/ai-benchmark/leb-300` were a static status page. They are now served by `Leb300Controller`, which reads the aggregate that ai-benchmark 0.2.129 published for LEB-300-A
(three runs of Claude Haiku 4.5, total 242 of 1000, grade Reprovada) from the synced `results.json`, and the view shows it with the LEB-100 card: rank, name and provider, runs, the score and grade, the seven
categories over their weights, cost and session time. LEB-300 is an active instance, so the page never shows a flaw, a flaw table or the per-flaw files. It still falls back to the status text when the file has
no aggregate. The copy says that the instance is an exploratory pilot whose difficulty has not been homologated, whether the score rests on three runs (official) or fewer (not official), and what the record
does not have: no checkpoint between the two stages in any run, and the matrix hash named by the task text (`3331a107`) against the one scored (`c42c8287`), which differ only by a header field. The LEB-100 page's
note says which state LEB-300 is in. `AiBenchmark` gains `aggregate()` and `modelOf()`; `Leb300PageTest` is rewritten to cover both states with fakes, plus the real file (235 tests).

## 1.0.114 - A LEB-300 page says the second level exists and has no results yet

`/ai-benchmark/leb-300` and `/pt-br/ai-benchmark/leb-300` are new: a `Route::view` with its copy in `lang/en/leb_300.php`
and `lang/pt_BR/leb_300.php`. The page names the level (an application of about 3,000 lines, above LEB-100's 300), says
it is in preparation with no results published, says what will appear when there are some (one line per agent: the
total, the grade, the score per category, the number of runs, the cost and the time) and what never will (the planted
defects, the code under test, the answer key), and links to the ai-benchmark repository and to the LEB-100 page. It has
no database, no results file and no number that is a score. The LEB-100 page gains a one-line note that points to it,
the sitemap lists it with the usual alternates, and `Leb300PageTest` covers it in both languages (language, reciprocal
hreflang and canonical, no leak between languages, no results block, the links in each direction, the switcher).
`SitemapTest` now includes the two addresses. Nothing else moves.

## 1.0.113 - The Google tag defaults to G-WTE9C2ZB0V, the property created for samirhv.com.br alone

1.0.112 shipped `G-BC74RPH8P3`, a property that was not made for this site. `services.google.tag_id` now defaults to
`G-WTE9C2ZB0V` ("samirhv.com.br – GA4", web stream https://samirhv.com.br, UTC−3), `.env.example` and `GoogleTagTest`
follow. A server `.env` that sets `GOOGLE_TAG_ID` explicitly keeps its value, so it must carry the new id or be
cleared. 1.0.112 should not be deployed on its own: it would send hits to the wrong property.

## 1.0.112 - The public pages load the Google tag, so the Google Analytics property G-BC74RPH8P3 receives hits

New `partials/google-tag.blade.php`, included at the end of the public layout's `<head>` beside the Matomo
snippet: the stock gtag.js loader and `gtag('config', …)` call, async. The measurement id comes from
`services.google.tag_id` (`GOOGLE_TAG_ID`), defaulting to `G-BC74RPH8P3` so a deploy ships the tag without
touching the server's `.env`; an empty value turns it off, which `.env.example` and `phpunit.xml` do so local
copies and the suite send no hits. The admin layout and the login view do not carry it. `GoogleTagTest` pins
the single load, the configured id, the head placement, the off switch and the login exclusion.

## 1.0.111 - The AI Benchmark English copy parses again: 1.0.110 shipped two unescaped apostrophes

1.0.110 added "operator's" and "Nex N2.5 Pro's" to the void caveat in `lang/en/ai_benchmark.php` without escaping
the apostrophes inside the single-quoted PHP string, so the file did not parse and the English page answered 500.
Both are escaped; `php -l` and the feature tests pass. 1.0.110 should not be deployed.

## 1.0.110 - The AI Benchmark page counts twenty-one void attempts and says Nex N2.5 Pro is too slow for a third run

Synced from ai-benchmark 0.2.109. Nex N2.5 Pro's third run was cut off by the operator's connection after more
than 21 hours and voided, and the operator will not repeat it, the model being too slow: the void caveat counts
twenty-one attempts and names it, and the highlight that names Nex says it stays at two runs, in both languages.

## 1.0.109 - The AI Benchmark page adds Nex N2.5 Pro's second run, 428; it still publishes 317

Synced from ai-benchmark 0.2.107. Nex N2.5 Pro's run 2 scored 428 in 19h 35min; with two runs it publishes the
lower, 317, still 35th. The highlight that names it gives both runs and their times, in both languages.

## 1.0.108 - The AI Benchmark page adds Nex N2.5 Pro at high, 317, below the pass line, 35th of thirty-seven

`results.json` and the CSVs are synced from ai-benchmark 0.2.106: Nex N2.5 Pro at high, a new agent, scores 317
in its first run (10h 03min, US$ 2.80); its functions return nothing without a logged-in user and break 8 of
the 22 characterization checks. The highlights count thirty-seven agents, two runs that broke the contract
mechanically, twenty-four agents not at xhigh, and Nex among the agents that close the table, in both languages.

## 1.0.107 - The AI Benchmark leaderboard shades every second card, so each card's edges are clear

Every second card on screen gets a background halfway between `--s-surface` and `--s-surface-2`, a light
shade off the plain card; the leader keeps its own look. The view marks every second card in rank order, and
the script recounts the shade on each filter, order and page, so it follows the cards actually shown. The
vendor-filter test accepts the class and counts it.

## 1.0.106 - The AI Benchmark page writes run times as 7h 55min, from the session record

The run time in each card's sheet reads `19min`, `2h` or `7h 55min` instead of `7 h 55 min`. The highlights
quote the multi-agent Sonnet's runs from their session records, 7h 55min, 2h and 3h 34min (they said 8.1, 2
and 3.6 hours), and Gemini 3.8 Flash at medium as 4min 34s (it said 4.5 minutes). Synced from ai-benchmark
0.2.104.

## 1.0.105 - The AI Benchmark card sheet shows how long each run took

Each run line in a card's sheet now shows its wall-clock time, from the session's first message to its end
(`wall_minutes` in `results.json`, synced from ai-benchmark 0.2.103): minutes under an hour, hours and minutes
above. The feature test checks the time of every run that has one.

## 1.0.104 - The AI Benchmark cards open, on a click, a written comment and a run-by-run sheet for each agent

Each leaderboard card carries a `<details>` panel that opens on its summary or on a click anywhere on the
card (links, controls and a text selection keep their own behaviour). It shows the agent's written comment,
from the benchmark's `comments.json` via `results.json` (`comment.en` / `comment.pt_BR`), then a sheet read
from the runs: the categories at full marks and at zero, the planted flaws no run fixed, and run by run the
total, flaws fixed, false positives, business values changed, characterization checks, cost and scorecard.
`results.json` is synced from ai-benchmark 0.2.102. Two feature tests cover the panel in both languages and
a card with no comment.

## 1.0.103 - The AI Benchmark page shows twenty-six official scores, with GPT-5.5 official again at 558

`results.json` and the CSVs are synced from ai-benchmark 0.2.101 (ai-benchmark@f3e72f5). GPT-5.5 has its third
counted run, 536, and is official again at 558 (558, 568 and 536), 26th. The official-scores highlight names
twenty-six, in both languages.

## 1.0.102 - The AI Benchmark page adds DeepSeek V4 Flash at xhigh (617, 18th) and shifts the ranks below it

`results.json` and the CSVs are synced from ai-benchmark 0.2.100 (ai-benchmark@24255c4). DeepSeek V4 Flash at
xhigh joins as a new agent with one run of 617, 18th. The same model at high still publishes 282.

The copy is updated in both languages:
- **The DeepSeek highlight** adds the xhigh run.
- **Ranks.** Every rank cited from 18th down moves down one place, and "places 9 to 22" becomes "9 to 23".
- **Counts.** Thirty-six agents, thirty-five with the characterization green, twenty-four at
  compatibility 100.

## 1.0.101 - The AI Benchmark page shows twenty-five official scores, with GPT-6-astra at ultra the strongest GPT agent (666)

`results.json` and the CSVs are synced from ai-benchmark 0.2.99 (ai-benchmark@ab39440). GPT-6-astra at ultra
has its third run (651) and is official at 666, 6th, above GPT-6.1-sol (661).

The copy is updated in both languages:
- **The GPT highlight** now leads with GPT-6-astra at ultra.
- **The official-scores highlight** names twenty-five.

## 1.0.100 - The AI Benchmark page adds GPT-6-astra at ultra's second run (666); it publishes 666, 6th

`results.json` and the CSVs are synced from ai-benchmark 0.2.98 (ai-benchmark@30e5519). GPT-6-astra at ultra
now has two runs, 668 and 666, and publishes 666, still 6th. The GPT highlight gives both runs, in both
languages.

## 1.0.99 - The AI Benchmark page shows twenty-four official scores, with Grok 4.7 at xhigh official at 631

`results.json` and the CSVs are synced from ai-benchmark 0.2.97 (ai-benchmark@1e32533). Grok 4.7 at xhigh
has its second and third runs (617 and 631) and is official at 631, 11th. That puts it below Grok 4.7 at
high, so Grok 4.7 at high and Grok 4.6 move back to 9th and 10th.

The copy is updated in both languages:
- **The official-scores highlight** names twenty-four.
- **The Grok highlight** gives the xhigh agent's official score and its explanation range.
- **Ranks.** Grok 4.7 at high is 9th and Grok 4.6 10th again, and "places 10 to 22" becomes "9 to 22".

## 1.0.98 - The AI Benchmark page adds Grok 4.7 at xhigh (640, 9th) and shifts the ranks below it

`results.json` and the CSVs are synced from ai-benchmark 0.2.96 (ai-benchmark@d86883e). Grok 4.7 at xhigh
joins as a new agent with one run of 640, 9th. Its report scored 44/50, the best explanation from outside
Anthropic and OpenAI.

The copy is updated in both languages:
- **The Grok highlight** adds the xhigh run.
- **Ranks.** Every rank cited from 9th down moves down one place, and "places 9 to 21" becomes "10 to 22".
- **Counts.** Thirty-five agents, thirty-four with the characterization green, twenty-three at
  compatibility 100.

## 1.0.97 - The AI Benchmark page shows twenty-three official scores, with Gemini 3.7 Flash official at 587

`results.json` and the CSVs are synced from ai-benchmark 0.2.95 (ai-benchmark@8b59002). Gemini 3.7 Flash
at high is official at 587 (587, 587 and 556), 23rd. A fourth run of it is kept unscored.

The copy is updated in both languages:
- The official-scores highlight names twenty-three.
- The Gemini highlight calls Gemini 3.7 Flash official.
- The unscored-attempts caveat counts twenty, with the fourth Gemini 3.7 Flash run.

## 1.0.96 - The AI Benchmark page adds Gemini 3.7 Flash at high (587 in both runs, 23rd)

`results.json` and the CSVs are synced from ai-benchmark 0.2.93 (ai-benchmark@6e83f85). Gemini 3.7 Flash
at high joins as a new agent with two runs, both 587, and publishes 587, 23rd. The ranks below it move down
one place.

The copy is updated in both languages:
- **The Gemini highlight** mentions the generation before.
- **Ranks.** The medium-effort Gemini 3.8 Flash is 25th.
- **Counts.** Thirty-four agents, thirty-three with the characterization green, twenty-two at compatibility
  100, twenty-three not at xhigh.

## 1.0.95 - The AI Benchmark leaderboard pages at 25 agents, and each card shows its cost per run

- **Pages of 25.** The leaderboard now shows 25 agents a page, with Previous/Next buttons and a "Page n of
  m" line under it. Pages run over the agents the vendor filter keeps, in the current order (overall or by
  category). Changing the filter or the order returns to page 1. Each card keeps its rank in the full
  leaderboard. Without JavaScript the page still lists every agent.
- **Cost per run.** Each card's meta line shows the average cost of the runs that recorded one, e.g.
  "Cost US$ 0.57 a run". A tooltip says how many runs that average covers, and that Codex CLI keeps no
  cost. Cards with no recorded cost show none.
- `public/js/site/ai-benchmark.js`: the pager, applied after the filter and the order. Hiding rows is now
  done in one place.
- `lang/{en,pt_BR}/ai_benchmark.php`: `cost_run`, `cost_help`, `page_prev`, `page_next`, `page_of`. The pager
  labels travel as hidden text, as the other templates do, for shvia.org's language-parity check.

## 1.0.94 - The AI Benchmark page adds Kimi K2.7 Code highspeed (402, official) and applies the release-date rule to the dagger

`results.json` and the CSVs are synced from ai-benchmark 0.2.91 (ai-benchmark@f43621f). Two upstream
changes reach the page:
- **A release date bounds the training cutoff** (ai-benchmark 0.2.90). Qwen3 Coder Next, MiniMax-M3,
  Kimi K2.7 Code and GLM-5.2 were released before the answer key went public, so they lose the dagger.
- **Kimi K2.7 Code highspeed** joins as a new agent, official at 402 (402, 363 and 402), 30th.

The copy is updated in both languages:
- **The dagger label** now reads "no published cutoff or earlier release", and its tooltip and the
  answer-key caveat explain the release-date rule.
- **Counts.** Twenty-two official scores, thirty-three agents, thirty-two with the characterization
  green, twelve below compatibility 100, twenty-two not at xhigh.
- **The closing list** adds Kimi K2.7 Code highspeed.
- `AiBenchmarkPageTest` checks the new dagger label.

## 1.0.93 - The AI Benchmark page shows twenty-one official scores, with Kimi K3 at high and Kimi K2.7 Code official

`results.json` and the CSVs are synced from ai-benchmark 0.2.89 (ai-benchmark@359b1cf). Both third runs
are in: Kimi K3 at high is official at 629 (629, 634 and 574), 11th, and Kimi K2.7 Code at 415 (415, 415
and 512), 28th. No rank moves. The official-scores highlight names twenty-one in both languages.

## 1.0.92 - The AI Benchmark data adds Kimi K2.7 Code's second run (415)

`results.json` and the CSVs are synced from ai-benchmark 0.2.88 (ai-benchmark@b0b8dfb). Kimi K2.7 Code's
second run scored 415, the same as its first, so it still publishes 415, 28th. No rank moves, and the
copy is unchanged.

## 1.0.91 - The AI Benchmark data adds Kimi K3's second run at high (634); it still publishes 629

`results.json` and the CSVs are synced from ai-benchmark 0.2.87 (ai-benchmark@0d037cd). Kimi K3 at high now
has two runs (629 and 634) and still publishes the lower, 11th. No rank moves, so the copy is unchanged.

## 1.0.90 - The AI Benchmark page adds Kimi K3 at high (629, 11th) and counts nineteen unscored attempts

`results.json` and the CSVs are synced from ai-benchmark 0.2.86 (ai-benchmark@716ef5d). Kimi K3 returns
as a new agent at its high variant, with one recorded run of 629, 11th. Its default-effort run had been
withdrawn in 1.0.87.

The copy is updated in both languages:
- **Ranks.** Every rank cited from 11th down moves down one place.
- **Counts.** Thirty-two agents, thirty-one with the characterization green, eleven below compatibility
  100, twenty-one not at xhigh.
- **The parameters caveat** says Kimi K3 is back with a recorded run.
- **Unscored attempts.** The caveat counts nineteen. One more Kimi K3 run was lost to a VM restore
  before the copy, and Fable 5.1's third attempt hit an expired login.

## 1.0.89 - The AI Benchmark page adds GPT-6-astra at ultra, 668, the best GPT run, and shifts the ranks below it

`results.json` and the CSVs are synced from ai-benchmark 0.2.83 (ai-benchmark@b1387cc). GPT-6-astra at
ultra joins as a new agent with one run of 668, 6th. A finished Grok 4.7 run at xhigh was lost when its VM
was restored before the copy, and is kept as void.

The copy is updated in both languages:
- **GPT.** GPT-6.1-sol is the strongest *official* GPT model (7th). The best GPT run is GPT-6-astra at
  ultra (668, one run).
- **Ranks.** Every rank cited below 6th moves down one place. The historical ones ("its first run, 6th")
  stay.
- **Counts.** Thirty-one agents, thirty with the characterization green, ten below compatibility 100,
  twenty not at xhigh, six Claude models out of thirty-one.
- **Unscored attempts.** The caveat counts seventeen, with the lost Grok 4.7 run.

## 1.0.88 - The AI Benchmark page shows nineteen official scores, with Grok 4.6 official at 633

`results.json` and the CSVs are synced from ai-benchmark 0.2.80 (ai-benchmark@05c00df): Grok 4.6's third
run scored 620, and with runs of 633, 640 and 620 it is official at 633, 9th. The copy names nineteen
official scores in both languages, and the Grok highlight gives Grok 4.6's three runs.

## 1.0.87 - The AI Benchmark page drops the three runs withdrawn for an incomplete record: thirty agents, eighteen official

`results.json` and the CSVs are synced from ai-benchmark 0.2.78 (ai-benchmark@db95496). Under the new
`PROTOCOL §4` item 5, a run counts only if its client, first message, session log and cost were recorded.
Three runs from the first batch had none of them, and are withdrawn:
- GPT-5.5's run 1. GPT-5.5 now publishes 558 from two runs and is no longer official.
- MiniMax-M3 at its default, and Kimi K3. These were their only runs, so both agents leave the table.

The copy is updated in both languages:
- **Official scores.** The highlight names eighteen; GPT-5.5 is out of the list.
- **Counts.** Thirty agents, twenty-nine with the characterization green, twenty-one at compatibility
  100, nineteen not at xhigh.
- **The closing list** loses Kimi K3 and MiniMax-M3 at its default.
- **Isolation.** It no longer needs the Kimi K3 exception.
- **The parameters caveat** says the three runs without a session log were withdrawn.

## 1.0.86 - The AI Benchmark page shows nineteen official scores, with Sonnet 5.5's multi-agent mode official at 774

`results.json` and the CSVs are synced from ai-benchmark 0.2.76 (ai-benchmark@cabc5c1). Claude Sonnet
5.5's third multi-agent run at max scored 759 in 3.6 hours for US$ 119, with an explanation of 47/50, the
best here. With runs of 774, 820 and 759 the agent is official at 774, 3rd.

The copy is updated in both languages:
- **Official scores.** The highlight names nineteen, adding the multi-agent mode.
- **Multi-agent.** The highlight and the Claude comparison now give all three runs (8.1, 2 and 3.6 hours;
  US$ 233, 171 and 119; 774, 820 and 759), the official 774 against the single agent's 807, and seven
  fallback subagents in the third run.

## 1.0.85 - The AI Benchmark page shows eighteen official scores, with GLM-5.3 and Gemini 3.8 Flash official, and corrects three ranks

`results.json` and the CSVs are synced from ai-benchmark 0.2.75 (ai-benchmark@fe67d8e). It adds the third
runs of GLM-5.3 (604) and Gemini 3.8 Flash at high (100). Both agents are now official: GLM-5.3 at 621
(629, 621 and 604) and Gemini 3.8 Flash at 588 (687, 588 and 100).

The copy is updated in both languages:
- **Official scores.** The highlight names eighteen, with GLM-5.3 and Gemini 3.8 Flash at high. It
  adds Gemini's third run to the examples of a run far from its median.
- **Gemini 3.8 Flash.** Its highlight now states the official score. It also says how the third run
  ended: a database server the agent started kept its own command from returning, and the run was
  scored as delivered.
- **GLM-5.3.** Official at 621 over three runs, instead of "the lower of two".
- **Three stale ranks, wrong since 1.0.81.** When DeepSeek V4 Flash fell to last, every agent below its
  old place moved up one, and the copy was not checked. Gemini 3.8 Flash at high goes from 21st to
  20th, at medium from 23rd to 22nd, and DeepSeek V4.1 Flash from 18th to 17th. "Places 8 to 20"
  becomes "8 to 19": 638 to 597 is now places 8 to 19.

## 1.0.84 - The AI Benchmark leaderboard can be ordered by category, with each card's position in it

A second row of chips, "Order by", sits under the vendor filter: Overall (the default) and the seven
categories. Picking a category re-sorts the leaderboard by that category's score, with ties kept in
overall-rank order. Each card then shows its position in the category, e.g. "#4 in Security", and that
category's bar is highlighted.

- **The rank does not move.** The number on each card stays its rank in the full leaderboard.
  Positions in a category use competition ranking (1, 1, 1, 4) and are counted over every agent, so
  they do not change with the vendor filter. The two controls combine.
- **The leader card is marked by rank, not by position.** Its highlight was `:first-child`, which would
  have followed whichever card a category order put first. It is now `.ab-row--leader`, on the rank-1
  entry.
- **Templates are hidden text.** Both templates (the "showing" line and the position) travel as hidden
  text instead of a `data-template` attribute, as on shvia.org, so one script serves both sites.
- `lang/{en,pt_BR}/ai_benchmark.php`: `sort_label`, `sort_overall`, `sort_position`.
- `AiBenchmarkPageTest`: a chip per category, `data-scores` on every row, one leader card.

## 1.0.83 - The AI Benchmark leaderboard can be filtered by vendor, keeping every agent's rank

A row of vendor chips sits above each leaderboard, one per vendor in the results file with its agent
count, plus "All". Selecting chips shows only those vendors' agents. Several chips can be selected at
once; turning the last one off, or choosing "All", shows everyone again. A live line says how many
agents are shown.

The filter only hides rows. Every agent keeps the rank it has in the full leaderboard, so a filtered
view reads 8, 9, 20, 22, not 1 to 4.

- `resources/views/ai-benchmark/index.blade.php`: the chip bar, and `data-vendor` on every row. The
  bar is rendered `hidden` and revealed by the script, so without JavaScript the page is the plain
  leaderboard, with no dead controls.
- `public/js/site/ai-benchmark.js` (new): the filter, using `aria-pressed` on the chips and an
  `aria-live` status line.
- `public/css/site/ai-benchmark.css`: chip styles, matching the site's chips.
- `lang/{en,pt_BR}/ai_benchmark.php`: `filter_label`, `filter_all`, `filter_shown`.
- `AiBenchmarkPageTest`: one chip per vendor with its count, one tagged row per entry, the ranks
  unchanged, and both languages.

The flaw-by-flaw table is not filtered: it shows the top ten by rank.

## 1.0.82 - The AI Benchmark page adds the second multi-agent Sonnet 5.5 run, 820 in 2 hours

`results.json` and the CSVs are synced from ai-benchmark 0.2.73 (ai-benchmark@e9865d4): Claude Sonnet
5.5's second run in multi-agent mode at max scored 820 in 2 hours for US$ 171, against 774 in 8.1 hours
for US$ 233. It still publishes the lower, 774, 3rd.

The multi-agent highlight and the Claude comparison are rewritten in both languages. Before, they said
eight hours of multi-agent work scored less than a single agent. Now they say that at best it tied the
single-agent 820, at many times the cost, and that the dispatcher reached neither report.

## 1.0.81 - The AI Benchmark page shows sixteen official scores, with MiniMax-M3 thinking official and DeepSeek V4 Flash last at 282

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.72
(ai-benchmark@4c14c20). It adds runs 2 and 3 of MiniMax-M3 with the thinking variant (616 and 434;
official at 462), Grok 4.6's run 2 (640; it still publishes 633) and DeepSeek V4 Flash's run 2 (282).
That last run is now its published score, last of thirty-two.

The copy is updated in both languages:
- **Official scores.** The highlight names sixteen, with MiniMax-M3 with the thinking variant.
- **Contract.** The heading said nobody broke the contract mechanically. DeepSeek V4 Flash's published
  run now breaks one characterization check, so the highlight says one run did. The counts are now
  twenty-three at compatibility 100 and nine below it.
- **DeepSeek.** V4 Flash publishes 282 from two hosts, and V4 Pro is compared with V4.1 Flash's 612,
  not with "the Flash models".
- **The closing list** ends with DeepSeek V4 Flash, three below the pass line. The judge caveat says
  Claude models hold second-to-last place, not last.
- **Unscored attempts.** The caveat counts sixteen: a third GPT-6.1-sol run at ultra after its
  three, and a multi-agent Sonnet 5.5 attempt whose client was logged out.
- **Grok 4.6** shows its two runs (633 and 640).

## 1.0.80 - The AI Benchmark data catches up with the copy that 1.0.79 published ahead of it

1.0.79 rewrote the copy for ai-benchmark 0.2.69 (fifteen official scores, MiniMax-M3 with the
thinking variant, Claude Haiku 4.5 and GLM-5.3 Prime official) before that version was committed
upstream. `tools/sync-ai-benchmark-results.sh` reads the data from the upstream commit, so 1.0.79
shipped the new copy over the previous data (ai-benchmark@b1937cf). This release syncs
`results.json` and the CSVs from ai-benchmark@9e4449e, the 0.2.69 commit the copy describes. No copy
changes.

## 1.0.79 - The AI Benchmark page shows fifteen official scores, adds MiniMax-M3 with the thinking variant, and counts fourteen unscored attempts

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.69: the
third runs of Claude Haiku 4.5 (official at 317) and GLM-5.3 Prime (official at 628), and MiniMax-M3
with opencode's thinking variant as a new agent (462). The table has thirty-two agents.

The copy is updated in both languages:
- **Official scores.** The highlight names fifteen, with GLM-5.3 Prime and Claude Haiku 4.5.
- **Counts.** Thirty-two agents, ten below compatibility 100, twenty-one not at xhigh.
- **The closing list** adds MiniMax-M3 with the thinking variant.
- **Unscored attempts.** The caveat counts fourteen, adding the four runs made after their agent
  already had three.

## 1.0.78 - The AI Benchmark page shows GPT-6.1-sol at ultra official at 616, 15th

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.67, which
adds GPT-6.1-sol's third run at ultra (616). Its score becomes official at 616, the median of 597,
656 and 616, and it moves from 19th to 15th.

The copy is updated in both languages: the highlight names thirteen official scores, the ultra
highlight gives its three runs and the official 616, and DeepSeek V4.1 Flash is 18th.

## 1.0.77 - The AI Benchmark page gives both of GPT-6.1-sol's ultra runs, 597 and 656

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.66, which
adds GPT-6.1-sol's second run at ultra (656). Its published score stays 597, the lower, 19th.

The ultra highlight is rewritten in both languages: it no longer calls the agent a case of more
effort scoring less without qualification, and gives both runs — the first with three subagents and
9 flaws found, the second with two and 10 fixed.

## 1.0.76 - The AI Benchmark page shows GPT-5.6-luna and GPT-5.5 official, at 601 and 568

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.65, which
adds the third runs of GPT-5.6-luna (624) and GPT-5.5 (568). Both scores become official, at 601 and
568, with no change of rank.

The copy is updated in both languages: the highlight names twelve official scores; twenty-two agents
now keep compatibility at 100, since GPT-5.5's official run does; and the caveat on missing session
logs now says only GPT-5.5's first run left none.

## 1.0.75 - The AI Benchmark page shows Claude Sonnet 5.5 at max official at 807, 2nd, above the multi-agent run

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.64. That
version adds Claude Sonnet 5.5's third single-agent max run (820), and second runs of GPT-5.5 (558)
and GPT-5.6-luna (601). Sonnet 5.5 at max becomes official at 807, 2nd; the multi-agent run (774) is
3rd. GPT-5.5 publishes 558 and drops to 22nd.

The copy is updated in both languages:
- **Multi-agent highlight.** Eight hours of multi-agent work again scores less than half an hour of
  one agent at the same effort: 774 against an official 807.
- **Official scores.** The highlight names ten.
- **Security and architecture.** Sonnet 5.5 is at 250 in all three settings again, and scores 12 in
  architecture at max, since the run that now counts is its first.
- **Ranks.** Gemini 3.8 Flash at high is 21st; the band of close scores is places 8 to 20.

## 1.0.74 - The Portuguese AI Benchmark summary says "Nota sobre 1000", as the English says "out of 1000"

The summary next to the title read "Nota de 0 a 1000" in Portuguese and "Score out of 1000" in
English. The same AI Benchmark copy now also renders on shvia.org, whose harness compares the
numbers in the two languages and counted the extra 0 as a fact one side lacks. The Portuguese now
matches the English: same fact, one number.

## 1.0.73 - The AI Benchmark page shows GLM-5.3 Prime at 628, the lower of its two runs

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.63, which
adds GLM-5.3 Prime's second run (628). Its published score becomes 628, the lower of 635 and 628,
and it moves from 9th to 11th.

The copy is updated in both languages: the GLM highlight gives both runs; six agents fixed the CSV
injection, since the Prime run that now counts left it alone; Grok 4.6 is 9th and GPT-6-astra 10th.

## 1.0.72 - The AI Benchmark page shows GPT-5.6-sol's official score, 612, the ninth official score

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.62, which
adds GPT-5.6-sol's third run at xhigh (608). Its score becomes official at 612, the median,
unchanged and 15th. The highlight on official scores now names nine, in both languages.

## 1.0.71 - The AI Benchmark page shows GPT-6.1-sol's official score, 661, the eighth official score

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark 0.2.61. That
version adds GPT-6.1-sol's third run at xhigh (661) and GPT-5.6-sol's second (612). GPT-6.1-sol's
score becomes official at 661 and it moves to 6th, above GPT-6.1-sol pro.

The copy is updated in both languages:
- **Official scores.** The highlight names eight.
- **GPT models.** GPT-6.1-sol is the strongest, official at 661; GPT-6.1-sol pro follows at 654.
  GPT-6.1-sol at ultra is now 64 points below its official score at xhigh.
- **CSV injection.** Seven agents fixed it: GPT-6.1-sol's official run joins them.
- **Architecture.** GPT-6.1-sol scores 25, for naming the dispatcher, with Sonnet 5.5 at xhigh.

## 1.0.70 - The AI Benchmark data carries the recorded cost of Claude Sonnet 5.5's second max run

`resources/data/ai-benchmark/results.json` and `public/data/ai-benchmark/runs.csv` are synced from
ai-benchmark@a69a23c (0.2.60), which replaces that run's pending cost with the client's record,
US$ 4.75. No score or copy changes.

## 1.0.69 - The English Downloads page answers again: the benchmark CSVs move out of a folder named like the page

Since 1.0.47, `https://samirhv.com.br/downloads` answered **403 Forbidden** while
`/pt-br/downloads` worked. 1.0.47 published the benchmark CSVs in
`samirhv/public/downloads/ai-benchmark/`, a real folder with the same name as the bare (English)
`/downloads` route. Apache serves `public/` directly and rewrites to `index.php` only what is not a
directory, so it treated `/downloads` as the folder: it added the trailing slash and, with listings
off, refused it. There is no `public/pt-br/`, so the Portuguese page never hit the folder.

- **The CSVs move to `samirhv/public/data/ai-benchmark/`.** The download buttons and
  `tools/sync-ai-benchmark-results.sh` follow; `public/downloads/` no longer exists.
- **Old CSV links keep working:** `/downloads/ai-benchmark/{runs,flaws}.csv` answer 301 to the new
  addresses (`LegacyUrlRedirectTest`).
- **It cannot come back unnoticed:** `PublicFolderRouteCollisionTest` fails when any folder in
  `public/` has the name of a route's first segment. Laravel's test client never goes through
  Apache, so the page tests alone could not have caught it.

## 1.0.68 - The AI Benchmark page shows Claude Sonnet 5.5 at max at 773, one point below the multi-agent run

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@0.2.59. That
version adds the second single-agent run of Claude Sonnet 5.5 at max (773). Its published score
becomes 773, the lower of 807 and 773, and it moves to 3rd; the multi-agent run (774) is 2nd again.

The copy is updated in both languages:
- **Multi-agent highlight.** Eight hours of multi-agent work now scores the same as half an hour of
  one agent at the same effort (774 against 807 and 773), not more.
- **Security at 250** is Sonnet's at xhigh and in multi-agent mode; the max run that now counts
  left MD5 in place.
- **Architecture** is 0 for every agent but Opus 5.5 and Sonnet 5.5 at xhigh.

## 1.0.67 - The AI Benchmark page shows Gemini 3.8 Flash at 588, the lower of its two high runs, and its search for the answer key

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@37311a9. That
version adds Gemini 3.8 Flash's second run at high (588). Its published score becomes 588, the
lower of 687 and 588, and it moves from 6th to 22nd.

The copy is updated in both languages:
- **Gemini highlight.** It no longer calls Gemini the strongest model from outside Anthropic. It
  gives both runs, and says the second searched the VM for the answer key, which is not there.
- **Grok 4.7** is again the strongest model from outside Anthropic and OpenAI, 8th.
- **Ranks.** Every rank from 6th to 21st moves one place up, each checked against the table; the
  band of close scores is places 8 to 21.

## 1.0.66 - AiBenchmarkPageTest checks that the page does not name LEB-100-A's company

Adds `test_neither_page_names_the_instance_s_fictional_company`, which fetches the English and the
Portuguese page and asserts that neither shows the company name. The 1.0.65 entry described this
test, but its commit did not carry it: the edit to the test file failed and the commit went out
with the copy change only.

## 1.0.65 - The AI Benchmark page no longer names the company in LEB-100-A's legacy system

LEB-100-A's legacy system is set at a made-up internet provider whose name, it turns out, belongs to
a real company. The page now calls the instance "Support-ticket panel of an internet provider" /
"Painel de chamados de um provedor de internet". The benchmark repository keeps the instance as it
is: renaming it would change the package and the answer key the runs were made against.

`AiBenchmarkPageTest` checks that neither language's page shows the name, so a later sync cannot
bring it back.

## 1.0.64 - The AI Benchmark page shows DeepSeek V4.1 Flash's official score, 612, the seventh official score

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@d445d16. That
version adds DeepSeek V4.1 Flash's runs 2 (612) and 3 (597). Its score becomes official at 612, the
median, and it moves from 13th to 18th. Its published run is now run 2, which did not fix the CSV
injection.

The copy is updated in both languages:
- **Official scores.** The highlight names seven.
- **CSV injection.** Six agents fixed it, not seven, and only GPT-5.6-sol broke the technician
  column's `-`; DeepSeek V4.1 Flash's published run does neither.
- **DeepSeek V4.1 Flash.** It is the cheapest agent, official at 612, 18th.
- **Ranks.** GLM-5.3 is 15th and GPT-5.6-terra 13th, one place up each.

## 1.0.63 - The AI Benchmark page shows GPT-5.6-terra's official score, 625, the sixth official score

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@537fcb5. That
version adds GPT-5.6-terra's runs 2 (611) and 3 (645) in Codex CLI. Its score becomes official at
625, the median, unchanged and 14th.

The copy is updated in both languages: the highlight names six official scores, and the caveat on
unscored attempts says the GPT-5.6-terra run made in another client has now been repeated in Codex.

## 1.0.62 - The AI Benchmark page lists all ten unscored attempts, grouped by cause

`resources/data/ai-benchmark/UPSTREAM.json` now points at ai-benchmark@2297a38, which records two
more Gemini 3.8 Flash attempts cut off by OpenRouter's rate limit and a GPT-5.6-terra run made in a
different client from its first. No score changes.

The caveat about voided runs is rewritten in both languages. It now counts ten unscored attempts
and groups them by cause: a switch to another model (Fable 5.1, twice), a provider failure (four),
a setup error (three) and a client different from the agent's first run (one).

## 1.0.61 - The AI Benchmark page adds Gemini 3.8 Flash at medium (550, 23rd)

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@4de89a6. That
version adds Gemini 3.8 Flash at medium effort (550, 23rd), a separate agent from the same model at
high, and records an attempt started outside the task's folder as void. The table now has
thirty-one agents.

The copy is updated in both languages: the Gemini highlight compares the two efforts (687 against
550), and the counts read thirty-one agents, twenty-one at compatibility 100, twenty not at xhigh
and seven voided runs.

## 1.0.60 - The AI Benchmark page shows Grok 4.7's official score, 638, the fifth official score

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@e84305f. That
version adds Grok 4.7's runs 2 (663) and 3 (607). Its score becomes official at 638, the median,
unchanged and 9th.

The copy is updated in both languages: the highlight names five official scores, and the Grok 4.7
highlight gives its three runs and says the web lookup happened in its first run.

## 1.0.59 - The AI Benchmark page adds Gemini 3.8 Flash (687, 6th), the strongest model from outside Anthropic

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@a02a2a8. That
version adds Gemini 3.8 Flash at high (687, 6th), the first Google model, and records a second
Gemini attempt as void after an OpenRouter timeout. The table now has thirty agents.

The copy is updated in both languages:
- **New highlight.** Gemini 3.8 Flash is the strongest model from outside Anthropic, 33 points
  above the best GPT model; the Grok 4.7 highlight no longer claims that place.
- **Ranks.** Every rank from 6th down moves one place, and each quoted rank is checked against the
  new table. The band of close scores is now places 9 to 22.
- **Counts.** Thirty agents, twenty at compatibility 100, nineteen not at xhigh, six voided runs.

## 1.0.58 - The AI Benchmark page lists all five voided runs, including Fable 5.1's second switch to Opus 4.8

`resources/data/ai-benchmark/UPSTREAM.json` now points at ai-benchmark@0.2.51, which records Claude
Fable 5.1's third attempt as void. No score changes.

The "voided runs" caveat is rewritten in both languages. It said two runs were voided and that
Fable 5.1 showed only its first run; both were out of date. It now names all five voids: Fable
5.1 twice (its client handed the run to Claude Opus 4.8 after Fable's safeguards stopped it during
the security work), the first multi-agent Sonnet 5.5 run, Claude Haiku 4.5's first attempt and
GPT-5.6-sol pro's first attempt.

## 1.0.57 - The AI Benchmark page shows DeepSeek V4 Pro's official score, 496, the fourth official score

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@4aa1e52. That
version adds DeepSeek V4 Pro's third run (432). Its score becomes official at 496, the median of
604, 496 and 432, still 24th.

The copy is updated in both languages: the highlight now names four official scores and notes that
a single run can sit more than 100 points from the median, and the DeepSeek highlight gives all
three runs.

## 1.0.56 - The AI Benchmark page shows DeepSeek V4 Pro at 496, the lower of its two runs

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@c9a3d45. That
version adds DeepSeek V4 Pro's second run (496), made at the same time as the first (604) through
another host. Its published score becomes 496, 24th.

The copy is updated in both languages: the DeepSeek highlight gives both runs, DeepSeek V4 Pro
joins the agents that close the table, and the band of close scores is now places 8 to 21.

## 1.0.55 - The AI Benchmark page adds Claude Sonnet 5.5 at max without ultracode (807, 2nd) and DeepSeek V4 Pro (604)

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@9e33149. That
version adds two agents, so the table now has twenty-nine:
- **Claude Sonnet 5.5 at max, as a single agent (807, 2nd).** It took 27 minutes and US$ 3.86.
- **DeepSeek V4 Pro at high (604, 18th).** It ranks below both DeepSeek Flash models.

The copy is updated in both languages:
- **Multi-agent highlight.** It now compares the 8-hour multi-agent Sonnet run (774, now 3rd) with
  the same model at the same max effort as a single agent (807).
- **Counts.** Seven agents fixed the CSV injection, nineteen kept compatibility at 100, eighteen
  did not run at xhigh, and six of the twenty-nine agents are Claude models.
- **Ranks.** Every rank quoted in the highlights is checked against the new table. In 1.0.54,
  Grok 4.7, GLM-5.3 Prime and Grok 4.6 were quoted one place too low after GPT-6-astra moved
  down; the new agent at 2nd makes those numbers right again.

## 1.0.54 - The AI Benchmark page shows GPT-6-astra's official score, 628, and recounts the CSV injection fixes

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@60c0305. That
version adds GPT-6-astra's runs 2 (596) and 3 (628) at xhigh. Its score becomes official at 628,
the median of 661, 596 and 628, and it moves from 5th to 10th.

The copy is rewritten in both languages:
- **Official scores.** The highlight now names three official scores, adding GPT-6-astra's.
- **CSV injection (SEC-008).** It now says six agents fixed it, not seven: GPT-6-astra's official
  run left it in place, where its first run had fixed it.
- **The GPT models.** The strongest are now GPT-6.1-sol pro and GPT-6.1-sol (654 and 653). The
  sentence saying 6.1-sol and astra split on MD5 and the CSV injection is gone: astra's official
  run made the same two calls as 6.1-sol.

## 1.0.53 - The AI Benchmark page shows two official scores, Sonnet 5.5 at 809 and Opus 5.5 at 717, and qualifies the fixing pattern

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@63ca2d9. That
version adds three runs:
- **Claude Sonnet 5.5 at xhigh, run 3 (724).** Its score becomes official at 809, still first.
- **GPT-6.1-sol, run 2 (653).** Its published score is now 653, 7th.
- **GLM-5.3, run 2 (621).**

The copy is rewritten in both languages:
- **"Finding is not fixing".** It no longer says Sonnet fixes more "every time". Sonnet's third run
  fixed 8 of the 13, fewer than any of Opus's three, so the reading now says Sonnet fixes more "in
  the run that counts" (11 against 9 and 10) and lists every run.
- **Highlights.**
  - A line on the two official scores.
  - The GPT and GLM lines with their new scores.
  - Architecture: two agents above 0.
  - Ranks from DeepSeek V4.1 Flash down.
- **A correction.** 1.0.51 counted eleven agents with lost compatibility points. Since Opus's counted
  run is its second, which kept compatibility, the count is ten, and seventeen at 100.

The reading test follows the new heading.

## 1.0.52 - The AI Benchmark page shows Claude Haiku 4.5's second run, with its score still 317

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@4188a74, which
adds Claude Haiku 4.5's second run, at 369. Its published score stays the lower, 317, last. The
bottom-of-table line, in both languages, now says it publishes the lower of its two runs, and that
the visibility defect was in the first.

## 1.0.51 - The AI Benchmark page shows Claude Haiku 4.5, last of twenty-seven at 317

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@34278fd, which
adds Claude Haiku 4.5 at the model's default effort, since Haiku 4.5 does not support the setting.
It scores 317, below the pass line, 27th of 27.

The copy follows, in both languages:
- **Bottom of the table.** Haiku 4.5 joins it, with its time and cost, and the reproduced defect
  that hides a client's own tickets on the main page.
- **Counts.** Sixteen agents did not run at xhigh, eleven lost compatibility points, and
  twenty-four score 0 in architecture.
- **The judge-conflict caveat.** It says five of the twenty-seven are Claude models, holding the top
  four places and the last.

## 1.0.50 - The AI Benchmark page shows Claude Fable 5.1's second xhigh run: its published score becomes 764, third

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@f196839, which
adds Fable 5.1's second run at xhigh (764, against 781). Its published score becomes the lower, 764,
and it moves to 3rd. Claude Sonnet 5.5's multi-agent run moves to 2nd (774).

The copy follows, in both languages:
- **Architecture.** Only three agents score above 0 now.
- **Multi-run line.** It names Fable's two runs.
- **The multi-agent highlight.** It says 2nd.
- **"Finding is not fixing".** Fable found 12 and 11 and fixed 9 and 10. Sonnet still fixed 11 in
  both runs. Its 45-point lead over Fable's 764 is security (+17) and architecture (+25).

## 1.0.49 - The AI Benchmark page shows the first official score: Claude Opus 5.5 at 717, the median of three runs

`resources/data/ai-benchmark/results.json` and the CSVs are synced from ai-benchmark@e7fa5c7, which
adds Opus 5.5's third run at xhigh (805). With three runs the agent is official. Its published score
is the median, 717, 4th, and the page drops its "not official" mark on its own.

The copy is rewritten in both languages:
- **Highlight.** A new line names the first official score and explains its spread. The three runs
  fixed the same 9 planted flaws; the 94 points came from judgement calls. It replaces the line on
  Sonnet's two runs and keeps their numbers.
- **"Finding is not fixing".**
  - The three Claude models now find "nearly the same": 12, 12 and 11 in the runs that count.
  - Sonnet "fixes more, every time": 11 in both of its runs, against 9 in each of Opus's three.
  - Its lead over the official 717 is restated as clean code (+100), not security (+17).
  - The caution now says the pattern held across every run so far.

The reading test follows the new heading.

## 1.0.48 - The AI Benchmark page shows Sonnet 5.5's second xhigh run and GLM-5.3 Prime, and rereads the Claude comparison

`resources/data/ai-benchmark/results.json` and the two CSVs are synced from ai-benchmark@77df832.
That version adds:
- **Claude Sonnet 5.5 at xhigh, run 2: 809.** The published score becomes the lower of 825 and
  809, still first.
- **GLM-5.3 Prime: 635**, 9th, the strongest GLM model.

The hand-written copy is rewritten from the new data, in both languages:
- **"Finding is not fixing".** The reading now says that the three Claude models find the same:
  12 of the 13 planted flaws each in the run that counts, with the same discovery index. They
  differ only in how many they fix: Sonnet 11, Fable and Opus 9. Sonnet's leads are restated
  against its published 809, over Opus (+98) and over Fable (+28).
- **Highlights.**
  - New: Sonnet 5.5 has two runs, 16 points apart.
  - Updated:
    - seven agents with the CSV fix;
    - the per-model architecture scores;
    - sixteen agents at compatibility 100;
    - GLM-5.3 Prime as the strongest GLM;
    - every rank from Grok 4.6 down;
    - fifteen agents not at xhigh.
  - The GPT models' calibration claim now reads "among the best (Brier 0.015 or less)".

## 1.0.47 - The AI Benchmark page offers the results for download as CSV

`tools/sync-ai-benchmark-results.sh` now brings, besides `results.json`, the two CSVs that
ai-benchmark@b9a121d exports. They are byte-identical copies in
`public/downloads/ai-benchmark/`, never edited here. `--check` covers them too.
- **`runs.csv`** has one row per scored run: score, rank, model and host, effort, client mode,
  categories, flaws found and fixed, time, tokens and cost.
- **`flaws.csv`** has one row per run and planted flaw.

The "Audit it" section gains two download buttons and a link to the data dictionary
(`results/CSV.md`). Its text mentions the spreadsheets in both languages. A new test checks that
both pages link the files, and that the downloadable `runs.csv` has exactly the runs of the
page's `results.json`, so a half-done sync fails the suite.

## 1.0.46 - The AI Benchmark page shows the three GPT runs of 1 October and twenty-five agents

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@eb92014. That version adds:
- **GPT-6.1-sol pro** at 654 (Silver, 7th).
- **GPT-6.1-sol at effort ultra** at 597 (Bronze, 18th).
- **GPT-5.3-Codex** at 403 (Bronze, 24th).

GPT-6.1-sol now has two entries, so the hero and the flaw table name them "· xhigh" and "· ultra".

The hand-written copy is rewritten for twenty-five agents in both languages:
- **A new highlight** says GPT-6.1-sol at ultra scored 69 points below itself at xhigh. It is the
  second case here of more effort scoring less, and it took 24 minutes, not 8 hours.
- **Ranks.** Places from Grok 4.7 down move by one. The near-tie band is places 8 to 19.
- **The other lines.** GPT-5.3-Codex joins the bottom of the table and the agents with
  compatibility at 100; fourteen agents did not run at xhigh.

## 1.0.45 - The AI Benchmark page adds a reading across the Claude models: finding is not fixing

A new block, under each instance's highlights, holds a hand-written reading across agents
(`readings.<instance>`). It is rendered and escaped like the highlights. For LEB-100-A it
compares the three Claude models on two axes that one total hides, in English and Portuguese:
- **Finding.** Fable 5.1 and Opus 5.5 each reported 12 of the 13 planted flaws, and Sonnet 5.5
  reported 11.
- **Fixing.** Sonnet 5.5 fixed 11, and Fable and Opus fixed 9 each. Sonnet's lead over Opus
  comes mostly from clean code and compatibility, not from security.
- **Compute.** The three single-agent runs took 16 to 20 minutes each. Sonnet 5.5 in multi-agent
  mode took almost 8 hours and US$ 233 and scored 51 points less.

Every number comes from the verdicts, scorecards and `run.json` files of ai-benchmark 0.2.35. A
test checks that the block renders in both languages.

## 1.0.44 - The AI Benchmark page names the client mode next to the effort: max (ultracode)

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@67caaef, which records a
client mode that changes how the model works as `model.client_mode`. Claude Sonnet 5.5's
multi-agent run carries `ultracode`.
- **Leaderboard.** That row reads "Anthropic · effort max (ultracode)", in Portuguese "esforço max
  (ultracode)". A default-effort row keeps its wording and adds the mode the same way.
- **Hero and flaw table.** The run reads "Claude Sonnet 5.5 · max (ultracode)", next to "Claude
  Sonnet 5.5 · xhigh". The hero's name column now wraps instead of cutting the name with an
  ellipsis, which at desktop widths had hidden "(ultracode)".
- **Tests.** `AiBenchmark::effortLevel()` builds the label. The new page test fakes a mode on two
  entries and asserts the literal text in both languages, so it no longer depends on the synced
  data or on the function it tests.

## 1.0.43 - The AI Benchmark page shows Claude Sonnet 5.5 in multi-agent mode, third at 774

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@3374f01. That version adds
**Claude Sonnet 5.5 in Claude Code's multi-agent mode** at 774 (Gold, 3rd of 22), at effort max:
7 workflows with 68 subagents, 8.1 hours and US$ 233, against 825 for the same model at xhigh in 19
minutes and US$ 3.60. Every agent from Opus 5.5 down moves one place.

The hand-written copy is rewritten for twenty-two agents in both languages:
- **A new highlight** says eight hours of multi-agent work scored 51 points below nineteen minutes
  of one agent: the same scores elsewhere and better calibration, but 0 in architecture against 50.
  It also says nine subagents fell back to an older Sonnet, and that none of the delivery came from
  them.
- **SEC-008 and security.** Six agents fixed the CSV injection. Sonnet 5.5 is the only model at 250
  of 250, in both modes.
- **Ranks.** GPT-6.1-sol and GPT-6-astra are 5th and 6th, the two Grok models 7th and 8th, GLM-5.3
  9th and DeepSeek V4.1 Flash 10th. The near-tie band is places 7 to 17.
- **Caveats.** Four of the twenty-two are Claude models, Sonnet 5.5 twice, and they hold the top four
  places. Two runs were voided; the second is the first multi-agent attempt, stopped to give the
  machine more processors.

## 1.0.42 - The AI Benchmark page tells two entries of the same model apart by their effort

The hero's top six and the flaw-by-flaw table show only the model's name, so a model run in two
configurations would appear twice under the same name. `AiBenchmark::displayName()` appends the
effort level only when another entry of the same instance runs the same model ("Claude Sonnet 5.5
· xhigh" and "Claude Sonnet 5.5 · max"); every other name is unchanged. The leaderboard already
showed the effort under each name. A new test covers the helper, and the hero test now expects the
disambiguated names.

## 1.0.41 - The AI Benchmark page shows Qwen3 Coder Next and says the execution machine changed on 30 September

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@8d7b628. That version adds
**Qwen3 Coder Next** at 507 (Bronze, 18th of 21), run through Novita in opencode at the model's
default: it is non-thinking and has no effort setting. It carries the tag, since the Qwen team
publishes no cutoff. It is the first run made as an unprivileged user on a machine cleaned of
earlier runs' leftovers.

The hand-written copy is rewritten for twenty-one agents in both languages:
- **Bottom of the table.** Qwen3 Coder Next joins it. It is the only one of the five that fixed
  the N+1 query, and it has the weakest explanation (18 of 50) and the worst calibration (Brier
  0.301).
- **Effort.** Twelve agents did not run at xhigh.
- **Compatibility and architecture.** Qwen3 Coder Next kept compatibility at 100; architecture reads
  "0 for the other seventeen".
- **A new caveat, "The machine changed on 30 September."** It says that until then the agents ran
  as the VM's administrator on a machine that kept earlier runs' leftovers, and what their session
  logs show: no agent read another's work, and the opencode agents shared a leftover test database
  holding only seed rows.

## 1.0.40 - The AI Benchmark page shows twenty agents on LEB-100-A with both DeepSeek runs

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@4f3e4b1. That version adds:
- **DeepSeek V4 Flash** at 612 (Silver, 13th), run through Novita. Its note says DeepSeek now
  routes that name to V4.1 on its own API.
- **DeepSeek V4.1 Flash** at 625 (Silver, 9th), run through DeepSeek's own API and the cheapest run
  so far.

Both carry the tag, since DeepSeek publishes no cutoff. The flaw table shows the top ten of twenty.

The hand-written copy is rewritten for twenty agents in both languages:
- **SEC-008.** Five agents fixed the CSV injection. Two of them, GPT-5.6-sol and DeepSeek V4.1
  Flash, also rewrote the `-` marker.
- **DeepSeek.** A new line gives the cost of V4.1 Flash and V4 Flash's lost 30 points, with the
  routing caveat.
- **Architecture.** It reads "0 for the other sixteen".
- **Compatibility.** Scores of 100 include DeepSeek V4.1 Flash; eight agents changed a business
  value.
- **Effort.** Eleven agents did not run at xhigh.
- **Places 6 to 16.** They sit within 41 points, with GPT-5.6-terra tenth.
- **Judge caveat.** It counts three Claude models of twenty.
- **Isolation section.** It now says that every run of 30 September but Kimi K3's had every layer.

Validation: the full suite, Pint, `view:cache` and the sync `--check` pass.

## 1.0.39 - The AI Benchmark page shows the top six next to its title

A reader who stops at the fold now sees who leads. The hero becomes two columns: the title, lead
and buttons on the left, and a card on the right. The card lists the first six agents of the first
instance with rank, model, score and grade, then a line giving the score scale and the total number
of agents ("not official until each has three runs"), and a link to the full leaderboard.

The card reads the same `results.json` as the leaderboard, so it follows every sync with no hand
edits. The score and grade come from each agent's representative run, and the size is
`AiBenchmark::HERO_TOP`. Below 860 px the card stacks under the buttons. The strings exist in both
languages.

Validation:
- a new test checks the heading, the six names in rank order and the link on the English page, and
  the Portuguese heading and link;
- the Portuguese page is checked for the English strings;
- the full suite, Pint and `view:cache` pass;
- checked in headless Chromium at 1366 px and 390 px.

## 1.0.38 - The AI Benchmark page shows Kimi K2.7 Code among eighteen agents on LEB-100-A

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@142f665. That version adds
Kimi K2.7 Code at 415 (Bronze, 17th), run in opencode at the model's only effort (the default) with
every isolation layer. Its training cutoff is unpublished, so it carries the tag. The flaw table
shows the top ten of eighteen.

The hand-written copy is rewritten for eighteen agents in both languages:
- **Architecture.** It reads "0 for the other fourteen".
- **Compatibility.** Scores of 100 now include the two Kimi models.
- **Effort.** Nine agents did not run at xhigh.
- **Bottom of the table.** It names the four agents that left the N+1 query in place. Kimi K2.7
  Code is the worst calibrated so far (Brier 0.225): it reported two SQL injections at confidence
  100 in integer-only functions.
- **Judge caveat.** It counts three Claude models of eighteen.
- **Isolation section.** It adds Kimi K2.7 Code to the runs of 30 September with every layer.

Validation: the full suite, Pint, `view:cache` and the sync `--check` pass.

## 1.0.37 - The AI Benchmark page shows Grok 4.6 seventh among seventeen agents on LEB-100-A

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@cd025bf:
- **Grok 4.6 enters at 633** (Silver, 7th). It ran in opencode at effort high with every isolation
  layer.
- **It carries the tag.** xAI publishes no training cutoff for it.
- **Flaw table.** It shows the top ten of seventeen.

The hand-written copy is rewritten for seventeen agents in both languages:
- **Grok line.** It adds Grok 4.6, five points behind Grok 4.7 for about an eighth of the cost.
- **GLM-5.3 line.** It gives its new place, 8th.
- **Other highlights.**
  - Architecture reads "0 for the other thirteen".
  - Compatibility at 100 names the two Grok models.
  - Eight agents did not run at xhigh.
  - Places 6 to 14 sit within 41 points, with GPT-5.6-terra ninth.
- **Judge caveat.** It counts three Claude models of seventeen.
- **Isolation section.** The runs of 30 September with every layer are Opus 5.5's second, the two
  Grok runs and the four GLM runs.

Validation: the full suite, Pint, `view:cache` and the sync `--check` pass.

## 1.0.36 - The AI Benchmark page shows Grok 4.7 sixth among sixteen agents on LEB-100-A

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@460c11b. That version adds
Grok 4.7 at 638 (Silver, 6th), run in opencode at effort high with every isolation layer. Its
training cutoff (May 2026) predates the public answer key, so it carries no tag. The flaw table
shows the top ten of sixteen.

The hand-written copy is rewritten for sixteen agents in both languages:
- **SEC-008.** Four agents fixed the CSV formula injection, Grok 4.7 among them.
- **Grok 4.7.** A new line calls it the strongest model here from outside Anthropic and OpenAI,
  and the only agent that used the web: one read of the PHP manual. The GLM-5.3 line now follows
  it, at 7th.
- **Other highlights.** Architecture reads "0 for the other twelve". Compatibility at 100 includes
  Grok 4.7. Seven agents did not run at xhigh. Places 6 to 13 sit within 41 points (638 to 597),
  with GPT-5.6-terra eighth.
- **Judge caveat.** It counts three Claude models of sixteen.
- **Isolation section.** It adds Grok 4.7 to the runs of 30 September with every layer.

Validation: the full suite, Pint, `view:cache` and the sync `--check` pass.

## 1.0.35 - The AI Benchmark page shows fifteen agents on LEB-100-A with the two GLM-5.3 variants

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@2bfd623:
- **GLM-5.3-Flash enters at 624** (Silver, 8th).
- **GLM-5.3-FlashX enters at 597** (Bronze, 12th).
- **Both carry the tag.** Both ran in opencode at effort high with every isolation layer, and
  neither has a published training cutoff.
- **Flaw table.** It shows the top ten of fifteen.

The hand-written copy is rewritten for fifteen agents in both languages:
- **Highlights.** Architecture reads "0 for the other eleven". Compatibility at 100 now covers
  Sonnet 5.5, Fable 5.1, GPT-5.6-terra, Kimi K3 and the four GLM models. Six agents did not run at
  xhigh. Places 6 to 12 sit within 32 points (629 to 597). The GLM-5.3 line adds Flash, five points
  behind at a tenth of the cost, and FlashX.
- **Judge caveat.** It counts three Claude models of fifteen.
- **Isolation section.** The runs of 30 September with every layer are Opus 5.5's second and the
  four GLM runs.

Validation: the full suite, Pint, `view:cache` and the sync `--check` pass.

## 1.0.34 - The AI Benchmark page shows GLM-5.3 sixth among thirteen agents on LEB-100-A

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@d81b9bd. That version adds
GLM-5.3 at 629 (Silver, 6th), run in opencode at effort high with every isolation layer, 241 points
above GLM-5.2. Its training cutoff is unpublished, so it carries the tag. The flaw table shows the
top ten of thirteen.

The hand-written copy is rewritten for thirteen agents in both languages:
- **Highlights.** Architecture reads "0 for the other nine". Compatibility at 100 includes GLM-5.3.
  Four agents did not run at xhigh. Places 6 to 10 sit within 30 points (629 to 599), with
  GPT-5.6-terra seventh. A new line names GLM-5.3 the strongest model here from outside Anthropic
  and OpenAI.
- **Judge caveat.** It counts three Claude models of thirteen. The verdict changed in review for
  GPT-5.6-terra is given as the 41 points it adds, since the ranks it quoted (9th to 6th) no longer
  hold.
- **Isolation section.** It adds GLM-5.3 to the runs of 30 September that had every layer.

Validation: the full suite, Pint, `view:cache` and the sync `--check` pass.

## 1.0.33 - The AI Benchmark page shows twelve agents on LEB-100-A with Kimi K3 tenth

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@c886584:
- **Kimi K3 enters at 528 (Bronze, 10th).** It ran on a clone of the execution VM, since destroyed
  with its session log. Its training cutoff is unpublished, so it carries the tag.
- **Two agents move down.** MiniMax-M3 is 11th and GLM-5.2 12th.
- **The first message is fixed.** The upstream protocol now fixes it word for word.

The hand-written copy is rewritten for twelve agents in both languages:
- **Highlights.** Architecture reads "0 for the other eight". Compatibility at 100 includes Kimi
  K3. Three agents did not run at xhigh. The last three, Kimi K3, MiniMax-M3 and GLM-5.2, are the
  only ones that left the N+1 query in place; the line said "only two" and was wrong once Kimi K3
  arrived.
- **Judge caveat.** It counts three Claude models of twelve.
- **Parameters caveat.** It adds Kimi K3 to the runs with no session log.
- **Isolation section.** Only Opus 5.5's second run and GLM-5.2's had every layer on 30 September.
  Kimi K3's clone did not record its address blocks.

Validation: the full suite, Pint, `view:cache` and the sync `--check` pass.

## 1.0.32 - The AI Benchmark page shows eleven agents on LEB-100-A and Opus 5.5's second run

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@174843a. That version adds
two things: Claude Opus 5.5's second run (717, published as 711, the lower of two) and GLM-5.2
(388, 11th and Failed, run at effort high in opencode). It also voids Claude Fable 5.1's second
run, which the client finished with a different model.

The page follows the data on its own, from 1.0.27 and 1.0.28:
- the flaw-by-flaw table shows the top ten of eleven, with its note;
- Opus 5.5's card reads "2 of 3 runs (711 · 717)";
- the run caveat switches to "Up to three runs per agent";
- GLM-5.2 carries the "training cutoff not published" tag.

The hand-written copy is rewritten for eleven agents in both languages:
- **Highlights.** Architecture now reads "0 for the other seven". Compatibility at 100 now
  includes GLM-5.2. A new line gives Opus's six-point spread between runs, and another names the
  two agents that did not run at xhigh. MiniMax-M3 and GLM-5.2 are the two that left the N+1 in
  place.
- **Caveats.** A new one explains the voided run. The judge caveat counts three Claude models of
  eleven. The parameters caveat says what was recorded from the session logs. The public-key
  caveat and the isolation section separate the runs of 29 September (name block only) from those
  of 30 September (every layer), and add that no session log shows a request to GitHub.

Validation:
- the single-run caveat test now builds its own one-run data, since the real file has a second run;
- the full suite, Pint, `view:cache` and the sync `--check` pass;
- no raw translation key renders on either page.

## 1.0.31 - The AI Benchmark page says the published runs had GitHub's names blocked, not its addresses

1.0.29 and 1.0.30 described the VM's blackhole routes as if every published run had them. The VM's
logs show otherwise, as ai-benchmark 0.2.15 records. The routes went in on 30 September 2026, after
the ten published runs of the 29th, and those runs had only the `/etc/hosts` name block. The block
stopped accidental access but not a deliberate connection straight to a GitHub address.

In both languages, the isolation section now separates what the VM does today from what the
published runs had. Today it blocks the names, the announced prefixes and the edge addresses
GitHub publishes, and it has no IPv6. The runs of the 29th had the name block only. The public-key
caveat says the same, and the terminal adds a line for the 71 edge addresses under the seven
prefixes.

Validation: the full suite, Pint and `view:cache` pass. The section was checked in headless Chromium
at 1366 px.

## 1.0.30 - The AI Benchmark page shows GitHub's IPv6 prefixes among the VM's blackhole routes

1.0.29 listed only GitHub's four IPv4 prefixes as blackhole routes. The script the execution VM
runs (published in ai-benchmark 0.2.12) also blackholes three IPv6 prefixes: 2a0a:a440::/29,
2620:112:3000::/44 and 2606:50c0::/32. The terminal now lists all seven. In both languages the
text says the IPv4 and IPv6 prefixes are blackhole routes and that the VM has no IPv6 to begin
with.

Validation: the full suite, Pint and `view:cache` pass. The section was checked in headless Chromium
at 1366 px.

## 1.0.29 - The AI Benchmark page describes the execution VM's full isolation from GitHub

The section "Runs happen where the answer key is out of reach" said the block was by name only, and
so that a deliberate bypass was open. As ai-benchmark 0.2.11 now documents, the VM that runs the
benchmark has three layers, all in place since the first scored run:

- GitHub's names resolve to loopback.
- GitHub's IPv4 prefixes are blackhole routes.
- The VM has no IPv6.

The text says so in both languages. It keeps the one limit that remains: GitHub's regional edge
addresses outside those prefixes are not blocked. It also points training exposure at the cutoff
each run records. The terminal next to it is titled "benchmark VM" and adds the four blackhole
routes under the `/etc/hosts` lines.

Validation: the full suite, Pint and `view:cache` pass. The Portuguese page is checked for the new
English labels. The section was checked in headless Chromium at 1366 px.

## 1.0.28 - The AI Benchmark page takes each agent's details from the run its score belongs to, and marks a model that may have trained on the answer key

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@a6f02a3, where an agent can
now have up to three runs. Its score is the lower median of their totals, which is always the
total of one run, `representative_run`, and every entry carries `totals` and `key_exposure`. No
number on the page moves.

- **Details come from the score's run.** The leaderboard's grade, category bars, penalties and
  scorecard link, and each column of the flaw-by-flaw table, read `AiBenchmark::representativeRun()`
  instead of the best run. With two runs the page would otherwise have shown one run's score next
  to the other run's grade. A file without `representative_run` falls back to the best run.
- **More than one run shows the totals**, as in "2 of 3 runs (711 · 770)". The caveats show "One
  run per agent" while that is true, and switch to "Up to three runs per agent" once any agent has
  a second run.
- **Answer-key exposure.** A model whose published training cutoff is after the day LEB-100-A's
  answer key went public, or whose provider publishes none, carries a tag under its run count. The
  tag reads "training cutoff after the answer key went public" or "training cutoff not published",
  and its tooltip gives the date. Today only MiniMax-M3 carries it (not published).
- **The public-key caveat is rewritten** in both languages. The VM that runs the benchmark cannot
  reach GitHub, what a model saw in training depends on its cutoff, and a later or missing cutoff
  is marked in the leaderboard. "LEB-100-A should be retired for new runs" goes; the instance stays
  current under those two conditions.

Validation: new tests cover a second run (totals shown, the higher run's grade not shown, the
caveat switch), the single-run caveat, the representative-run fallback, and the exposure tags in
both languages. The Portuguese page is checked for the English labels. The full suite, Pint,
`view:cache` and the sync `--check` pass. The MiniMax-M3 card was checked in headless Chromium at
1366 px.

## 1.0.27 - The flaw-by-flaw table shows the top ten agents

The flaw-by-flaw table has one column per agent, and at ten it already fills the 1040 px container
(1.0.24). From the eleventh agent on, the table shows the top ten in rank order, with a line
saying how many agents there are and that every agent's result, flaw by flaw, is in its
scorecard. The leaderboard above it still lists every agent. The limit is
`AiBenchmark::FLAW_TABLE_AGENTS`; with ten agents today nothing on the page changes.

`AiBenchmark::fake()` serves a results array to the page in tests, so a shape the synced file
does not have yet (here, an eleventh agent) can be exercised without touching the copy.

Validation: a test with eleven agents checks that the eleventh appears in the leaderboard and not
in the table header, and that the note shows in both languages; another checks the note stays
away at ten. The full suite, Pint and `view:cache` pass.

## 1.0.26 - The AI Benchmark page shows the instance's name and the current highlights

Since the page was created (1.0.18), the instance heading rendered the raw keys
`ai_benchmark.instances.LEB-100-A.name` and `.desc`, and "What stood out" showed the first
four-agent reading. Every results update wrote the new reading under `instances.LEB-100-A`,
where the view looks for the instance's name, instead of under `highlights.LEB-100-A`, where it
looks for the reading.

In both languages `instances.LEB-100-A` now holds the name and description of the instance (the
support-ticket panel of an internet provider, in legacy PHP), and the ten-agent reading replaces
the four-agent one under `highlights.LEB-100-A`. A new test fails when either page renders any
`ai_benchmark.` key, and the lang test now requires a name, a description and highlights for
every instance in the results file.

Validation: both new assertions fail on 1.0.25's lang files and pass here; the full suite, Pint
and `view:cache` pass.

## 1.0.25 - The AI Benchmark page shows ten agents on LEB-100-A

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@251e77c, which adds
MiniMax-M3 (460, 10th), the first model from neither Anthropic nor OpenAI, run at its model's
default effort because it has no effort setting.

The hand-written reading of the results is rewritten for ten agents in both languages, with a
line saying MiniMax-M3 ran at the model's default while the other nine ran at xhigh. The
conflict-of-interest caveat now counts four verdicts changed in review and places GPT-5.6-terra
9th to 6th after its review.

Validation: the full suite, Pint, `view:cache` and the sync `--check` pass; the leaderboard shows
"default effort (not configurable)" / "esforço padrão (não configurável)" for MiniMax-M3.

## 1.0.24 - The flaw-by-flaw table fits ten agents on a desktop

With a tenth agent the last column fell outside the 1040 px container and only showed up when the
table was scrolled sideways, which hid the newest entry. The flaw column's minimum width goes
from 14rem to 12.5rem, each agent column's from 5.6rem to 4.9rem, and cell padding from 10 px to
8 px, so ten agents fit at desktop width; phones keep the pinned flaw column and scroll the rest.

Validation: checked in headless Chromium at 1366 px (all ten columns visible) and 390 px.

## 1.0.23 - An agent that runs at its model's default effort says so, in both languages

Some models expose no reasoning-effort setting and always run at their own default; the results
file marks them `reasoning_effort: "default"`. The leaderboard showed the raw value ("effort
default", in English on both pages). It now reads "default effort (not configurable)" and
"esforço padrão (não configurável)"; a chosen level still reads "effort xhigh" / "esforço xhigh".

Validation: AiBenchmarkPageTest (the Portuguese page is checked for the English label), Pint and
`view:cache` pass.

## 1.0.22 - The AI Benchmark page shows nine agents on LEB-100-A

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@b0e2fa6, which adds
GPT-6.1-sol (666, 4th) to the LEB-100-A leaderboard, five points above GPT-6-astra.

The hand-written reading of the results is rewritten for nine agents in both languages: four
agents identified the dispatcher and declined to split it, GPT-6.1-sol and GPT-6-astra are the
strongest GPT models and split on MD5 versus the CSV injection, places 6 to 9 sit within 26
points. The conflict-of-interest caveat now places GPT-5.6-terra 6th after its review.

Validation: the full suite, Pint and `view:cache` pass; the nine-column flaw table checked in
headless Chromium at 1366 px.

## 1.0.21 - The AI Benchmark page shows eight agents on LEB-100-A

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@9b05e25, which adds
GPT-6-astra (661, 4th) to the LEB-100-A leaderboard.

The hand-written reading of the results is rewritten for eight agents in both languages: three
agents fixed SEC-008, GPT-6-astra is the strongest GPT model here, three agents keep
compatibility at 100, places 5 to 8 sit within 26 points. The conflict-of-interest caveat now
counts three verdicts changed in review.

Validation: the full suite, Pint and `view:cache` pass.

## 1.0.20 - The flaw-by-flaw table stays readable as agents are added

With eight agents the table squeezed the flaw column to a sliver — names wrapped to four lines —
and cut the last agent off. The model names in the header may now wrap, each agent column keeps a
minimum width, and the flaw column keeps its own (narrower on phones) and stays pinned to the left
while the agent columns scroll sideways.

Validation: checked in headless Chromium at 1366 px (eight columns, no scroll) and 390 px (pinned
flaw column, agents scroll).

## 1.0.19 - The AI Benchmark page shows seven agents on LEB-100-A

`resources/data/ai-benchmark/results.json` is synced from ai-benchmark@fbcc857, which adds
GPT-5.6-terra (625, 4th) and GPT-5.6-sol (612, 5th) to the LEB-100-A leaderboard; the flaw
table now has seven columns and still fits a desktop width without scrolling.

The hand-written reading of the results is rewritten for seven agents in both languages: two
agents fixed SEC-008, architecture is the weakest category for everyone, three agents keep
compatibility at 100, places 4 to 7 sit within 26 points. The conflict-of-interest caveat now says
the three Claude models hold the top three places and that one of the two verdicts changed in
review moves GPT-5.6-terra from 7th to 4th.

Validation: the full suite, Pint and `view:cache` pass; checked in headless Chromium at 1366 px.

## 1.0.18 - An AI Benchmark page in the main menu explains LEB and publishes its results

`/ai-benchmark` (and `/pt-br/ai-benchmark`) explains what LEB measures — evolving a legacy
system without breaking it — how a run works, how the 1000 points are scored and lost, and how
runs are isolated from the answer key. Then the results from the synced file: per instance a
leaderboard (total, grade, the seven categories, penalties, discovery index, calibration, a link
to each audited scorecard) and a flaw-by-flaw table of what each agent found and fixed, followed
by a hand-written reading of the numbers and the caveats — one run per agent, an AI judge that
is also a contestant, an answer key already public, harness fixes made before scoring.

The main menu and the footer link to it, and it joins `/sitemap.xml`. Interface strings live in
`lang/{en,pt_BR}/ai_benchmark.php`; the page's CSS is `public/css/site/ai-benchmark.css`, theme
tokens only.

Validation: AiBenchmarkPageTest (both languages render with no leak, every entry appears, the
menu links in the page's language, hreflang is reciprocal, every id in the file has a name in
both languages, the key sets match); the full suite, Pint and `view:cache` pass; checked in
headless Chromium at 1366 and 390 px.

## 1.0.17 - The LEB results arrive as a synced copy of ai-benchmark's results.json

The benchmark's numbers are produced and audited in samirhvbr/ai-benchmark, where
`tools/export-results.py` builds `results/results.json` from the scorecards. A number edited
here would disagree with the scorecard that justifies it, so the site keeps a byte-identical
copy in `samirhv/resources/data/ai-benchmark/`, with `UPSTREAM.json` naming the source commit.

`tools/sync-ai-benchmark-results.sh` refreshes it from GitHub (or `--from` a local clone) and
`--check` exits 1 when the copy has drifted; it never reads through command substitution, so
the copy stays byte-identical. `App\Support\AiBenchmark` reads the file once per request and
degrades to "no results yet" when it is missing or invalid.

Validation: `--check` fails before a sync and passes after; the copy matches ai-benchmark@e219e58.

## 1.0.16 - Document the yellow accent specification in the brand manual

Add a dedicated yellow specification page with sRGB HEX, RGB, HSL, opacity,
terminal dimensions and favicon usage rules. Link it from the palette page
and refresh the ten-page PDF and handoff archive.

Validation: check extracted color values and page references, render the full
manual and visually inspect the new specification page.

## 1.0.15 - Add a subtle pure-yellow terminal to the samirhv brand

Confine the #FFFF00 accent to a thin inset at the upper tip of the S. Refresh
full-color vector and raster assets, optical favicons, previews and the visual
manual. Monochrome signatures and production website assets remain unchanged.

Validation: inspect the updated overview, rendered manual and 16/32/48 px
favicons; verify SVG structure, export dimensions and ICO frames.

## 1.0.14 - Refine the samirhv visual identity and package brand assets

Add the second S essencial identity edition with outlined vector signatures,
optical favicons, app icons, licensed Manrope fonts, color tokens, social covers
and a nine-page visual manual. Preserve the original study and supplied reference.
The identity remains a proposal under `brand/`; production assets are unchanged.

Validation: parse all SVGs; verify PNG dimensions, ICO frames and PDF page count;
render and visually review the manual and overview.

## 1.0.13 - a test and a CI job fail when the AI-MEMORY copy stops matching ai-memory-web

1.0.12 made `samirhv/app/Services/AiMemory/` a copy. This entry adds what the owner asked
for alongside it: a check that fails when the two differ. There are two ways to differ,
and each is caught where it can be measured.

**Offline, in the suite: `ReaderCopyTest`.** It fails when:

- a copied file was edited here: each sha256 is compared with `UPSTREAM.json`, and so is
  the set of classes;
- a sync brought a string with no `lang/pt_BR.json` entry. The admin would show it in
  English. The strings are read from the tokens of each `__()`/`say()` call, and a known
  one (`still open`) is asserted first, so an extractor that finds nothing cannot pass;
- a sync brought a `config('aimemory.*')` key `config/aimemory.php` does not declare, or
  an import this app does not have;
- under this app's own config, with the request locale set to `en` as on admin routes,
  the copy no longer renders `05/09/2026 09:34` and `em aberto`.

Five reversions, each measured red on its own test: a comment edited in a copied file, a
translation removed, `date_format` removed from the config, `locale` set back to follow
the request, and an import of a class this app lacks.

**Online, in CI: `.github/workflows/ai-memory-reader.yml`.** It runs
`tools/sync-ai-memory-reader.sh --check` on every push to `master`, every PR and daily at
09:17 UTC, since a push to ai-memory-web triggers nothing here. It fails with exit 1 when
upstream `master` differs from the copy, and with exit 2 when it could not measure (clone
failed, ref missing). Before the sync, `--check` against ai-memory-web 0.1.19 listed all
eleven files as different and exited 1. After it, `--check` reported "in sync" and exited
0, both from GitHub and from a local clone.

Suite: **191 passed, 1 skipped** (186 + these 5; the skip is the unrelated accessibility
one), in `php:8.4-cli` with `pdo_sqlite`. `pint --test` passes.

## 1.0.12 - the AI-MEMORY reader classes become a synced copy of ai-memory-web's

The eleven classes under `samirhv/app/Services/AiMemory/` are now **byte-identical** to the
same directory in [samirhvbr/ai-memory-web](https://github.com/samirhvbr/ai-memory-web)
0.1.19 (`9088c01`). That repository is the source. The owner decided on 23–24/09/2026 to
keep both screens, and to keep this copy in sync by a script with a check that fails on
divergence. The decision is ADR-006 there.

Measured before the change, the two copies were 217 lines apart. 179 of those lines were
comments. The other 38 were three choices that belong to each app: the fallback timezone,
the default date format and the UI language. ai-memory-web 0.1.18 moved all three out of
the classes, so here they live in:

- `config/aimemory.php`, where `date_format` = `d/m/Y H:i` and `locale` = `pt_BR` are new.
  `timezone` was already there.
- `lang/pt_BR.json`: the ten strings, keyed by ai-memory-web's English. The locale is fixed
  in config, not taken from the request, because admin routes render in the bare
  (English) locale.

**What an operator sees does not change.** The ten Portuguese strings were checked by
script to be byte-identical to the ones the old classes returned, with each `:path`,
`:dir` and `:message` in place of the interpolated variable. Right after the raw sync,
two existing tests failed: `AiMemoryDatabaseTest` asserts `ESCRITA no diretório` and
`não existe`. With the translation in place, the suite is back to its baseline: **186
passed, 1 skipped** (the unrelated accessibility skip), in `php:8.4-cli` with
`pdo_sqlite`, and `pint --test` passes on the copied files.

`tools/sync-ai-memory-reader.sh` makes the copy. It reads ai-memory-web from GitHub (a
bare, blob-less clone) or from a local clone with `--from`, writes the files and
`UPSTREAM.json` (commit, version, one sha256 per file), deletes a class upstream no longer
has, and commits nothing. `--check` exits 1 when the copy differs from upstream and 2 when
it could not measure, never 0 on a failed clone or an unknown ref. The comments in the
copied files are now English, because they are that repository's bytes.
`docs/AI-MEMORY.md` §6.1, `CLAUDE.md`, `app/Services/README.md` and the `SetLocale`
docblock say so where an editor will look. The docblock had said that nothing under
`admin.*` calls `__()`, which stopped being true with this change.

## 1.0.11 - the permission lists follow repodocs: five commands move to ask, seven rules leave deny

`rm -rf` and `curl`/`wget` piped into a shell leave `deny` and now ask for confirmation.
Reading `.env`/`.env.*`, `git push --force`/`-f`, `git reset --hard` and `git clean -fd`
leave `deny`. Key reads (`*.pem`, `*.key`, `*.p8`, `*.p12`, `*.pfx`) stay blocked. The
owner's decision on 24/09/2026, replicated from repodocs 1.17.0 (ADR-028).

## 1.0.10 - the repository stops choosing the model

`CLAUDE_CODE_SUBAGENT_MODEL` leaves `.claude/settings.json`. The model is now the user's
choice, made with `/model`, and a subagent inherits it: by default Claude Code gives a
subagent the session's model, and this variable was the only thing making it different —
with the session on Opus 5.5, subagents were measured on Opus 5, because the variable names
the `opus` alias and the alias still resolves through the organization's managed pin.

`.claude/README.md` stop(s) describing a model profile.

Rule and measurement: repodocs ADR-027.

No test: configuration and documents. Checked that the file parses and that repodocs
runbook §7's check is silent here.

## 1.0.9 - the model pin leaves .claude/settings.json

`"model": "opus[1m]"` and the `ANTHROPIC_DEFAULT_OPUS_MODEL` env pin are gone.
The window suffix was a version pin in disguise — the 1M variant existed only for the
previous Opus, so every session was born on it while the catalog already offered the
newer one. The env var is worse than a pin: it redefines what `opus` means for
everything that reads it, the model picker included.

It unblocks nothing on its own: the deciding layer is the account's server-managed
settings, which outrank every local file. Rule, measurement and what to write instead
(`"model": "opus55"`, the version named): repodocs ADR-026.

No test: two JSON keys and a comment. Checked that the file still parses.

## 1.0.8 - `files:add` gets a version flag that can actually be reached

`AddProjectFile` declared `--version`, and `--version` is a **global** Symfony
Console option. `Application::doRun()` intercepts it before it resolves any
command: it prints `Laravel Framework 13.12.0`, returns 0, and the command never
runs. A caller passing `files:add … --version=1.6.0` got clean output and exit 0
with nothing published — the worst shape a failure can take, because every
automated check of it passes.

That is not hypothetical. `build-local.sh --publish` in the `tura-notes`
repository ingests with exactly that flag. It reported four green steps, printed
`Laravel Framework 13.12.0` where the ingest line belonged, announced
"Published", deleted its staging copy, and left `/p/tura-notes` reading "Em
preparação" with zero `project_files` rows. The version string in the transcript
is what identified it.

The option is now `--file-version`. Reproduced in a standalone Symfony Console
application: with `--version` the command body never executes and the framework
version is printed instead, byte for byte what the server produced; with
`--file-version` it runs and receives the value.

**The old name broke the no-flag form too.** In that same reproduction,
`files:add <path> --project=<slug>` — the form `README.md` and `CLAUDE.md`
document — fails with `An option named "version" already exists.`, because
merging the application definition finds a conflicting option of the same name.
After the rename both forms run.

The flag stays optional either way: `FileIngestService` asks `FilenameInspector`
for the version when none is given, and it reads the first `X.Y(.Z)` in the
filename, so `TuraNotes_1.6.0_aarch64.dmg` describes itself.

**Callers must change.** `build-local.sh` and `tools/build-linux.sh` in the
`tura-notes` repository both pass `--version=`; after this they will be told the
option does not exist and will fail loudly instead of succeeding silently, which
is the point. Dropping the flag there loses nothing.

## 1.0.7 - The Tura publisher is removed: the Tura repository already had one

`tools/publish-tura.sh`, added one version ago in 1.0.5, is deleted. It should
never have been written here.

`build-local.sh --publish`, in the `tura-notes` repository, already published the
signed `.dmg` to this site, and by the same four steps for the same stated
reasons — preflight the download service, `scp` to a staging directory, compare
the sha256 **before** the ingest because a truncated `scp` leaves a file that
`files:add` swallows happily, then one `files:add --project=tura-notes`. It
already carried the destination as documented constants (`TURA_PUBLISH_HOST`,
`TURA_PUBLISH_APP`, `TURA_PUBLISH_SLUG`).

And it carried a gate that 1.0.5 did not: it refuses to publish a `.dmg` that is
unsigned or has no notarisation ticket (ADR-024 of that repository). The script
removed here would have published an unsigned build without a word — dropping
the exact protection that makes this site the macOS channel in the first place.

Two tools for one job is worse than one; two where the redundant one is missing
the safety check is a trap with a timer on it. Whoever found `publish-tura.sh`
first would have used it.

**This leaves no gap.** Every platform the catalogue entry promises is already
covered there: `build-local.sh --publish` sends the signed `.dmg`, and on a
Linux host that same entry point `exec`s `tools/build-linux.sh`, whose
`--publish` sends the `.deb`, AppImage and `.rpm` through the same `files:add`,
with the same hash check, and publishes the updater feed besides. ADR-072 of
that repository decided exactly this shape.

What is missing is not code but the act: nobody has run `--publish` yet, which
is why `/p/tura-notes` still shows "Em preparação". It is the first item of that
repository's queue, and it needs the machine holding the Developer ID.

## 1.0.6 - The style check goes green on a double space in a docblock

`vendor/bin/pint --test` had been failing on the default branch since 1.0.4, on
`app/Services/TuraCredentials.php`. Under the five fixer names Pint reported —
`unary_operator_spaces`, `braces_position`, `not_operator_with_successor_space`,
`single_line_empty_body`, `phpdoc_align` — the actual change is one space: a
`@return` line aligned with two spaces instead of one.

Small, and worth its own commit rather than a ride along the next feature: while
CI is red for a reason nobody remembers, a red badge stops being information,
and the next real failure arrives looking exactly like this one.

The fix is Pint's own output, not a hand edit — `pint --test samirhv/` now
passes over all 164 files.

## 1.0.5 - Publishing a Tura build stops being eight commands typed by hand

`tools/publish-tura.sh` takes the built packages and publishes them as downloads
of the site, in one run.

The Tura `.dmg` is signed and notarized by the machine that holds the Developer
ID certificate in its keychain, not by a CI runner — which is why the
`tura-notes` GitHub Release never carries it, and why this site is the macOS
channel. Since the project's `tools/build-linux.sh`, the `.deb`, AppImage and
`.rpm` come out of the same local build and travel the same way. Publishing a
release meant repeating, package by package: `scp`, `ssh`, `sudo -u www-data php
artisan files:add`, delete the staging file. Four packages, eight commands, and
a mistyped `--project` publishes into the neighbouring project without saying so.

    ./tools/publish-tura.sh --host samirhv --dir ~/x/tura-notes/dist

What it does per package: checks the sha256 **after** the transfer and before
the ingest — a truncated `.dmg` that got published is worse than one that did
not, because it looks ready — then runs `files:add` as `www-data` and removes
the staging file. It creates its own staging directory and removes only what it
put there, with `rmdir` rather than `rm -rf`, so anything unexpected left inside
fails loudly instead of being deleted blind.

It does **not** build anything. Only finished files go in.

`--dry-run` prints the exact `scp` and `ssh` lines without touching the server.
`--dir` picks up only the extensions `FilenameInspector` knows, so checksums and
build logs sitting next to the packages do not become downloads.

The host has no default on purpose: a guessed hostname publishes to the wrong
server, which is worse than not running. `--host`, or `SAMIRHV_SSH_HOST`.

## 1.0.4 - The admin mints Tura sync credentials

Enrolling a device on the Tura sync server meant an ssh session, a CLI and
carrying a file off the server by hand. `/admin/tura` does it instead: a label,
one button, and the `.secret` downloads once — into the password manager, and
into the application's **Arquivo de credencial** field, which is the format it
expects anyway.

**There is no user and password, and that is not an omission.** The server keeps
only a BLAKE3 digest of the secret, so it has to be minted there; a password
someone chose would be a string the server has never seen. The screen says so,
along with the reason for one credential per device: two devices sharing one
cannot be told apart, so revoking the one you lost cuts off the one you kept.

Reaching the server needed a wrapper, not a sudoers line on `notes-server`.
`/var/lib/notes-server` is 0700 owned by `notes`, so `www-data` cannot run the
CLI at all — and `token create` writes the secret to a new 0600 file owned by
whoever ran it, which `www-data` then cannot read. Granting it the CLI buys a
file nobody can open. `tura-credential`, in the Tura repository, returns the
secret over the pipe and removes the file; here it is one `sudo -n`, and `-n`
so a password request fails immediately instead of hanging a PHP-FPM worker
that has no terminal to type into.

The secret comes back as a **download**, never as HTML. Rendered into a page it
would sit in history, in the cache and in any screenshot, and none of that
raises an error — it leaks quietly. `TuraCredentialTest` asserts the body is the
secret with no markup around it.

When the wrapper is not installed the screen explains, the same posture as the
AI-MEMORY module: it is a 500 on an admin page that tells you nothing about what
to install. The four things that are usually missing are listed in the order
they are usually missing, starting with "the server was never deployed".

Eight cases. Among them, one that exists because the first version of this
screen failed it: every style class the two views use must exist in the admin
CSS. `btn btn-primary` and `btn-danger` are another project's classes — the
button renders, the page returns 200, the suite passes, and what appears is
unstyled text. The only way to see it is to look, or to assert it.

## 1.0.3 - preserve the refined S essencial brand concept

Store the selected geometric S exploration under `brand/s-essencial`, with editable SVG sources, PNG/ICO exports, a presentation board and the unchanged reference. The concept remains parked; no production templates, styles, favicon links or public assets change.

Validation: SVG parsing, PNG decoding and dimensions, ICO resolutions, and visual inspection of the presentation board.

## 1.0.2 - The Tura Notes page says its sync runs on a server you own

The description on /p/tura-notes ends at "no account and no cloud of ours",
which is true and reads as an absence. It is not one: the app syncs between
machines through a server the **reader** runs, and until now the page said
nothing about it. Someone who wanted their notes on two machines had nowhere to
go from here, and someone who assumed there was a sign-in went looking for one
that does not exist.

The section under the convention — `partials/projects/tura-notes.blade.php`,
its strings in `lang/{en,pt_BR}/tura_notes.php` — is product copy and
deliberately not a second copy of the procedure: what it is for, the three
commands that stand the server up, the pairing modes as a table, and what to
read before deciding. The walkthrough it is written from stays in the Tura
repository (`docs/SELF-HOSTING.md`), and a second copy of a procedure is a copy
that goes stale in silence.

It leads with the part a download page is tempted to leave out. There is **no
end-to-end encryption**: the server reads its own notes, so the machine has to
be one the reader would trust with them. **One credential per device**, because
two devices sharing one cannot be told apart and revoking the lost one cuts off
the kept one. And **sync is not a backup** — it copies your mistakes to the
other machine promptly and correctly.

`ProjectSectionTest` gains the slug: nine cases, 491 assertions, both languages
rendering with no database, no key printed as text, no unreplaced placeholder,
and the two lang files covering the same keys.

## 1.0.1 - The ai-memory access panel offers both repositories

The panel had one row, `external_url`, pointing at akitaonrails/ai-memory — the
product the page explains, and the right link for someone who wants to install
it. What it left out is the half that is ours: samirhvbr/ai-memory-web, the
read-only panel whose nine captures fill the rest of the page. A reader who
scrolled through those screens and then went looking for the repository that
produced them was being offered the wrong two links.

It could not go in a column. `external_url` holds the first one and
`upstream_repo` holds akitaonrails/ai-memory too — that is what the version
monitor compares our fork against, and it means "the OSS this is a fork of",
not "a second link".

So the panel gained the same convention the page section already uses:
`partials/projects/<slug>-access.blade.php`, picked up by one `@includeIf`
inside the aside. The partial owns its url and its copy together, which is the
point — putting the address in the two lang files would have given it two homes
that can drift apart, and a lang file is for translations rather than for
addresses.

Both buttons said "Open on GitHub", for two different repositories. They name
their destination now — "Open ai-memory", "Open ai-memory-web" — which is what
the meuip.rs and SShvTerm rows already do ("Open meuip.rs", "Go to
sshvterm.com").

`ProjectSectionTest` covers access rows alongside sections: the same key-leak and
placeholder assertions, plus one that the partial actually carries the panel's
option class. Included with no wrapper of its own, a partial that forgot it would
render as unstyled text hanging off the bottom of the box, and only looking would
find it.

## 1.0.0 - The catalogue takes its official order

ShvIA, ai-memory, ai-usagebar, GitHub Desktop, Tura Notes, SShvTerm, meuip.rs.
One `sort_order` drives every surface — the nav menu, the home showcase, the
downloads list, the admin list, the monitor and the sitemap all read
`orderBy('sort_order')` — so this is a change to the seeder and to nothing else.

The blocks were MOVED, not renumbered in place. The seeder is the readable
source for what the catalogue is, and someone checking the order reads it top to
bottom; renumbering without moving would leave the file's order and the screen's
order disagreeing, and the file is the one a person reads. The reason is written
into the file itself so the next edit keeps the pair together.

The order is not alphabetical and not chronological. It is the two AI tools
first, then the two Git/desktop applications, then the two things that live on
their own sites — a reader scanning the menu meets things that belong together.

WHY 1.0.0. The owner's call, and 0.9.0 is what earned it: the catalogue is
complete, every project in it has a page rather than a redirect, every page has a
changelog, and both languages are asserted rather than hoped for. The number says
the site is finished being assembled, not that it stops changing.

## 0.9.0 - The project sections are asserted in both languages

Four sections, two languages, and every string in a `lang/` file: the failure
mode is not an exception, it is a page that renders fine and says the wrong
thing. A key written in `lang/en` and forgotten in `lang/pt_BR` falls back to
English, so the Portuguese page carries an English sentence mid-paragraph — the
leak `App\Support\Content` documents, in reverse. A key renamed in the view and
not in the file makes `__()` return the key, so "ai_memory.f_team_desc" is
printed as body text. A `:placeholder` with no argument renders the literal
colon-word. None of the three is an error anywhere.

`ProjectSectionTest` renders all four partials in both languages and asserts
against each. It needs no database, and that is not luck: the sections take no
`$project` and read everything from `lang/`, which is what lets them be tested in
a suite that deliberately has none. The key-leak assertion compares against the
real key list rather than a pattern, because the meuip section prints
"meuip.rs/asn" as content and a pattern loose enough to catch "meuip.lead"
catches that too — the first version of this test failed on exactly that.

It also pins `Project::distributesFilesHere()` in its three shapes: a link
project with no files here, a download project, and a hybrid that has both.

## 0.9.0 - Tura Notes gets a changelog, and a description that names its Linux packages

Tura Notes joined the catalogue in 0.8.0 and has had an empty "What changed"
section ever since — the partial renders nothing at all when an app has no
entries, so the gap was invisible rather than broken, which is how it survived
two releases. Five curated entries now: in-app updates with signed feeds per
format, the Linux installers, the signed and notarised macOS build, the rename
to Tura Notes with the data paths deliberately unchanged, and PDF text imported
as Markdown.

The description was one release out of date in the part that matters to whoever
is deciding where to download from. It said the Linux packages live on the GitHub
Releases, which was true until `tools/build-linux.sh` arrived in 1.0.3 and gave
.deb, AppImage and .rpm the same `files:add` path the .dmg already used. Both
channels are wired now, and the description says both.

Not in this release, and worth writing down: no file has actually been ingested
yet, so `/p/tura-notes` still shows "in preparation". The pipeline is complete on
both sides — `build-local.sh --publish` on macOS, `tools/build-linux.sh
--publish` on Linux — and publishing is a run on the machine that holds the
signing key, not a change to this repository.

## 0.9.0 - meuip.rs joins the catalogue

Blue3's "what is my IP" service: it shows the caller's public address with ASN,
provider, country and region, and answers in the shape the caller asked in. `curl
meuip.rs` returns the bare address and a newline, parseable with nothing to
strip; the same url in a browser returns the whole page. That is the product, and
it is why the section shows the terminal block and the routing table rather than
describing them — a service you curl is explained by the shape of its answer.

It also runs a looking glass: a traceroute executed on the server and streamed to
the page hop by hop, for the case where it is your own network that blocks ICMP,
with a continuous mtr-style reading when you ask for it.

There is no binary to host here and there never will be, which is what
`distributesFilesHere()` is for — without it the page would have advertised a
desktop application in preparation for a web service.

The changelog has two entries, and that is the honest number. The v2.0 rewrite
and the traceroute came before the repository adopted the house versioning, so
they live in the git history rather than in the numbering; the side note says so
instead of back-dating versions that were never released.

## 0.9.0 - ai-memory joins the catalogue, with the screens of its web panel

Two projects, one page, and the page never lets them blur. ai-memory is Fabio
Akita's (akitaonrails/ai-memory): long-term memory for coding agents, where what
a session learned — decisions, approaches that failed, questions still open — is
written to markdown and comes back to the next agent, even a different one.
ai-memory-web is ours (samirhvbr/ai-memory-web): a read-only Laravel panel over
the same SQLite index, and the only reason this page can show anything at all.
ai-memory is a daemon and a CLI, and a daemon has no screenshots.

So the section explains the first and shows the second, each half linking its own
repository and the closing block naming both again side by side. Getting that
attribution wrong in either direction would be taking credit for someone else's
project, which is the one mistake a page like this cannot make.

The nine captures come from the panel's own `docs/screenshots/`. They are
resized to 1600px and palette-reduced on the way in — 5.2 MB of originals
becomes 764 KB, on a page that renders them at a third of that width. The
captures show the Portuguese AI-MEMORY integration in this site's admin, which is
where the standalone app was extracted from; the note under the gallery says so,
because a reader who opens the repository and finds an English interface should
not have to wonder which one is real.

The changelog lists ai-memory's own releases, not the panel's. Interleaving two
version lines reads as one line that skips numbers, so ai-memory-web is named in
the side note instead.

## 0.9.0 - SShvTerm gets the page it was only a link to

SShvTerm was a pure link: `redirect_to_site` on, so clicking its card 302'd
straight to sshvterm.com and this site never said what the product is. That is a
reasonable arrangement for something self-evident. It is a bad one for a desktop
SSH client whose two differentiators — sync the server cannot read, and an agent
that runs commands under an allow · ask · deny policy you wrote — are invisible
from the name and do not fit in a showcase card.

So the redirect is off and `/p/sshvterm` renders: the description, a section of
its own, and its changelog. The official site is still the download channel, and
the access panel is what sends you there — worded for what it actually is
("Official site · the installers for Windows, macOS and Linux are served from
there") rather than the generic "online version, always the latest", which would
have been a lie about a desktop app.

`partials/projects/sshvterm.blade.php` is written from what the product itself
publishes at sshvterm.com, checked on 2026-09-14. Nothing in it comes from the
private repository: a catalogue page that described unreleased internals would be
publishing them.

The changelog entries are curated the way every app's are — six releases that
change something a user can see, out of a version line that moves several times a
day. 2.0.78 is in the list precisely because it changes nothing visible: it is
the ceiling that stops the five largest files from growing, and leaving it out
would have labelled 2.0.77 "current" when it is not.

## 0.9.0 - A project whose files live elsewhere stops being promised as a download

The access panel on /p/{slug} has two blocks and both of them described ShvIA.
"Online version · nothing to install, always the latest" is exactly right for a
product whose site is the other half of the thing you can also download. It is
wrong for SShvTerm, whose site is where the INSTALLERS are, and it undersells
meuip.rs, whose site is the entire product. So the three strings can be
overridden per slug through `project.sites.<slug>`, and a project with no entry
keeps the generic ones — opt-in, rather than a table every project has to fill
in. The keys exist in both languages by construction, because `Lang::has()`
falls back to English and a slug translated only once would put an English
sentence on the Portuguese page.

The second block was worse. With no files uploaded it rendered "Desktop
application — in preparation", which is true of Tura Notes before its first
`files:add` and false of a project that will never ship a binary from here.
`Project::distributesFilesHere()` is the distinction, and it needs no new column
because the answer is already in the data: a project with an external site and
no files here distributes somewhere else. A hybrid with files (ShvIA) and a
project with no site at all (Tura Notes, GitHub Desktop) both keep the promise,
and it comes back on its own the day a link project starts hosting a binary here.

The downloads list is the other half of the same subject. Its card branched on
`redirect_to_site`, so a project that stopped redirecting fell through to the
download branch and advertised "Files coming soon" with a "View files" button
over a project that has no files here. It now shows the site's host and a button
into the project's own page — the page is the thing that was missing, and sending
someone off-site straight from a list is what stopped anyone reading it. The
"use online" tag narrows to a hybrid that genuinely has files here: over a
desktop SSH client, or over a GitHub repository, it describes neither.

## 0.9.0 - A project page can carry a section of its own

`/p/{slug}` renders a header, a description, an access panel, a changelog and a
list of files. That is enough for a download and not enough for a product: the
ShvIA page needed a section about the models behind it, and `show.blade.php`
grew an `@includeWhen($project->slug === 'shvia', ...)` to get one. A second
project would have made it two lines in a file that has nothing to do with
either project, and a fourth would have made it a list nobody remembers to
update — the same failure mode `Project::getMarkUrlAttribute()` already avoids
by treating the project mark as a file on disk rather than a column.

So the section is a convention now: `partials/projects/<slug>.blade.php`, picked
up by a single `@includeIf` between the description and the changelog. Adding a
section is adding a file; `show.blade.php` is not touched again.
`partials/shvia-models.blade.php` moved to `partials/projects/shvia.blade.php`
and is the first thing the convention renders, so the mechanism is exercised by
the section it replaced.

The slug comes from route-model binding and is `Str::slug`-shaped, so it cannot
carry a path separator into a view name.

`public/css/site/project-sections.css` is the other half. The two curated pages
that came before it — ai-usagebar and github-desktop — each carry their own
inline styles, which is why the site has three slightly different cards, three
code blocks and three caption styles. The new sections share one vocabulary of
token-based classes, so writing one is writing markup and no CSS.

## 0.9.0 - The English project page printed its category in Portuguese

`App\Support\Content` exists because a project's title, description and
category are written in the admin, in Portuguese, and are content rather than
interface. Every surface that renders them goes through it — the downloads list,
the structured data in the page head, the meta description. Two did not: the
`<h1>` and the category tag of `/p/{slug}`, which echoed the database column
straight out.

The category is the one that showed. The English page rendered an English
description under a tag reading "Assistente IA", and `/projects/tura-notes` put
"Notas em Markdown" above prose about Markdown notes. The title survived only
because every project is currently named the same in both languages; the day one
is not, it would have leaked the same way, which is why both go through Content
now rather than only the one that was visibly wrong.

BilingualRenderTest never saw it. It is written against
`/projects/github-desktop`, which is a `Route::view` with no database row — so
the page with no database prose is the page the leak test reads, and the pages
that have some were never checked.

## 0.8.2 - The deploy runs from a copy, because it rewrites itself

The entry below added a step and the deploy did not run it. It reported
`✅ Deploy concluido` anyway, which is the part that matters: the run looked
clean, `migrate` was followed straight by the cache rebuild, and `/p/tura-notes`
was still a 404 afterwards.

`deploy.sh` runs `git merge` on the repository that contains it, so it rewrites
itself halfway through its own execution. Bash does not load a script into
memory before running it — it reads as it executes, straight from the file. Swap
the file underneath it and what runs after the merge is no longer reliably the
file that started running. A skipped step is the good outcome; the bad one is
bash resuming mid-command and executing half a line. Reproduced at the real
file's geometry — merge near byte 6800, insertion near byte 9800 — where the line
immediately after the rewrite was swallowed and the script still exited 0.

So the script now copies itself into `/run` and re-execs from there, before the
lock and after the root check, so a run without `sudo` still gets the root
message rather than a permission error. `bash "$copy"` rather than executing it,
because `/run` is usually mounted `noexec`. An `EXIT` trap removes the copy on
every path out, including `fail`.

THE CONSEQUENCE, WRITTEN DOWN: a change to `deploy.sh` now takes effect on the
FOLLOWING deploy, never the one that delivered it — the running process executes
the version it started with, whole. That is the trade being made: a predictable
effect one deploy later instead of an unpredictable one now.

## 0.8.1 - The deploy syncs the project catalogue, not just the schema

`deploy.sh` ran `migrate` and never `db:seed`, so the catalogue reached the
server as code and never as rows — `migrate` creates tables, not content. The
entry below is what made it visible: Tura Notes shipped with its seeder entry,
its English translation and its mark, all of it live on the server, and
`/p/tura-notes` answered 404. The row did not exist, and nothing in the deploy
was ever going to create it. The same hole was waiting for every project added
after it.

Step 7 now runs `db:seed --class=ProjectsSeeder --force`, after the migrations
and before the caches are rebuilt. Naming the class is not a detail: the bare
`db:seed` runs `DatabaseSeeder`, which also calls `AdminUserSeeder` — and that
one re-hashes the admin password whenever `ADMIN_PASSWORD` is present in `.env`,
and sets `must_change_password`. On every deploy.

A failing seeder warns, notifies and lets the deploy continue. Stopping between
the migration and the cache rebuild would leave the new code running against the
routes and views compiled from the previous commit, which is worse than a
catalogue one deploy behind.

THE COST, WRITTEN DOWN: `ProjectsSeeder` is authoritative — `updateOrCreate` by
slug — so a title, description, category, icon, order or flag edited in the
admin returns to what the code says at the next deploy. That is the intent its
own docblock already declares, and the download FILES stay the admin's: nothing
in this step touches them.

## 0.8.0 - Tura Notes joins the catalogue

Tura Notes is a local-first Markdown note-taking app — you pick a folder, the
`.md` files inside it are your notes, and there is no proprietary format and no
account. It is published openly at `samirhvbr/tura-notes`, but the signed and
notarised macOS `.dmg` is **not** attached to the GitHub Release: the Developer
ID certificate lives in a keychain rather than in a CI secret, so the machine
that holds it is the machine that packages. This site is that build's
distribution channel, which is why the project belongs here and not only on
GitHub. Its `build-local.sh --publish` uploads through `php artisan files:add`,
so the file arrives on the private downloads disk, hashed and counted, like any
other.

The seeder carries it in second place, after ShvIA, with the Portuguese
description; `lang/en/content.php` carries the English, and the new category
"Notas em Markdown" is translated alongside it. Its mark is in
`public/img/projects/tura-notes/`, which is the convention the previous entry
introduced.

## 0.8.0 - A project can have a logo instead of a glyph

The catalogue drew a Font Awesome icon inside a rounded square, in four
templates — the home page's featured card and its list, the downloads card and
the project header — and each held its own copy of the markup. So a project with
an actual logo had to be taught to four places, or would show a generic glyph in
three of them.

`partials/project-mark` is now the single place that decides what goes inside
that square. It takes the square's own classes from the caller, because those
four are different sizes and radii and none of that changes; what it owns is the
content and the fall-through — logo, then icon, then nothing at all, since an
empty bordered box is worse than no box.

`Project::$mark_url` answers from a convention on disk,
`public/img/projects/<slug>/mark.svg`, rather than from a new column. Same
reason the English prose lives in `lang/` and not in the database: a column
would need a field in the admin CRUD, and the admin is out of scope. A project
with no file keeps its icon, which is what every project did before this
existed. The directory is scanned once per request rather than once per card,
because the same project is drawn on the home page, in the list and on its own
page; `forgetMarks()` is what makes that memoization testable.

## 0.7.4 - Nothing goes into the cache that has to come back as an object

The public home page was answering 500 on every request:

> The script tried to call a method on an incomplete object […] the class
> definition "Illuminate\Database\Eloquent\Collection" […] was loaded
> _before_ unserialize() gets called

`config/cache.php` carries Laravel 13's `serializable_classes => false`: the
framework refuses to unserialize any PHP class out of cache storage, so that a
leaked `APP_KEY` cannot be turned into a gadget chain. Writing an object still
works — it is the read that hands back a `__PHP_Incomplete_Class`. Two places in
this app were storing objects, and neither of them knew it.

**The projects menu.** The `layouts.app` view composer cached the published
projects as an Eloquent Collection under `nav.projects`, for six hours, on every
public page. The read after the first write turned it into a broken object and
`$navProjects->isNotEmpty()` in the layout took the site down with it — home,
project pages, downloads, every URL that renders the shell. It now caches the
raw attributes, an array of arrays, and rehydrates the models with
`Project::hydrate()` on the way out: same query, same menu, same invalidation.

**The monitor's refresh floor.** `monitor:last-refresh` stored `now()`, a Carbon
instance, and the check reading it back was `instanceof CarbonInterface` —
always false against an incomplete object. The 5-minute floor between two real
GitHub checks never fired, so every click on "Verificar agora" went straight to
an API that allows 60 unauthenticated requests per hour, which is the one thing
that block exists to prevent. The key now holds a Unix timestamp.

Nothing else in the app cached an object: the sitemap caches a string, and the
release checker and the repository suggestions cache arrays of scalars.

## 0.7.3 - the git hooks are regenerated from repodocs

Both hooks of the standard are rewritten from repodocs, and `tools/release.sh`
with them when it came from there. `commit-msg` checks the shape of the subject
(`X.Y.Z - description`), refuses a Conventional Commits prefix and a vague
message, **and checks that the subject's `X.Y.Z` is the version this commit
carries in `version.md`**. `pre-push` compares the local `version.md` against
the remote default branch for a repeated or a backwards version — **only when
the push actually updates that branch**, so a branch deletion, a tag and a topic
branch pass through.

The hook does **not** check the language and could not: what it measures is the
shape and the number.

Escape hatch, declared in both: `REPODOCS_NO_HOOK=1`. In a fresh clone, enable them with
`git config core.hooksPath tools/git-hooks`.

## 0.7.2 - the git hooks arrive from repodocs and are enabled here

Both hooks of the standard now run here: `commit-msg`, which checks the shape of
the subject (`X.Y.Z - description`), refuses a Conventional Commits prefix and a
vague message, **and checks that the subject's `X.Y.Z` is the version this commit
carries in `version.md`**; and `pre-push`, which compares the local `version.md`
against the remote default branch for a repeated version and for one that moves
backwards.

The hook does **not** check the language and could not: what it measures is the
shape and the number.

Until now the commit rule lived here only as prose in `CLAUDE.md`, and prose is
what gets forgotten at the end of a long session. On 07/09/2026 the hooks were
enabled in 3 clones out of 58, and two repositories of the fleet were measurably
off the norm with nothing to say so.

Escape hatch, declared in both: `REPODOCS_NO_HOOK=1`. It exists so the hooks stay installed —
a guard with no declared bypass gets bypassed with `--no-verify`, which switches
off every guard at once. In a fresh clone, enable them with
`git config core.hooksPath tools/git-hooks`.

## 0.7.1 - The findings the 0.7.0 review left on the table

A sweep through the medium and low priority findings of the review that
produced 0.7.0.

### Things that were declared and never happened

- `TrackPageView` skipped thirteen path prefixes for routes that do not exist
  in this app — Breeze scaffold. `bootstrap/app.php` declared a JSON exception
  renderer for `api/*` routes that were never registered. `EnsurePasswordChanged`
  exempted a route its middleware group never sees.
- `admin.github-view.repos.status` got the polling client its own docblock
  described, so a syncing repository card stops freezing until a page reload.
- `laravel/pint` had been a dev dependency since the beginning and had never
  run; it now runs over the codebase and on every push.

### Screens that paid per row, per file and per fork

- `is_mirrored` made one filesystem call per rendered file — invisible in a
  query log. One listing per request now, invalidated by the two places that
  write to the disk.
- The public nav menu queried on every page view; it is cached, and the
  `Project` model forgets the key on save, delete and restore.
- The download audit loaded every file in the table into a `<select>`.
- The monitor's "Verificar agora" fired one synchronous GitHub call per tracked
  project against a 60-per-hour unauthenticated limit. A five-minute floor now
  stands between real checks, and the screen says so.

### Correctness

- `page_views.locale` recorded the language the browser asked for instead of
  the one the page rendered in — on a bilingual site, the wrong measurement.
- "Today" was cut in UTC in two more screens, the same defect fixed for
  downloads. `AnalyticsService::todayStart()` is the panel's one definition.
- `EnsureIsAdmin` signed a valid user out for reaching a page they may not see;
  it answers 403.
- The admin file list compared versions with raw `version_compare()` while
  `App\Support\SemVer` existed and was used everywhere else.

### The head, and getting in with a keyboard

- The social card promised `summary_large_image` and shipped no image. There is
  one now, generated by `tools/make-og-card.php`, alongside `og:url`,
  `og:site_name`, the alternate locale, and JSON-LD describing each download
  page as a `SoftwareApplication`.
- The projects menu was an anchor that navigated nowhere and opened only on
  hover — absent for anyone using a keyboard. It is a button that states its
  expanded state, a skip link precedes the page, and the content sits in a
  `<main>` landmark.

### Structure

- 912 lines of CSS left thirteen inline `<style>` blocks and became cacheable
  files; the new `vasset()` helper cache-busts them by mtime. Inline `style=`
  attributes remain, so a `style-src 'self'` policy is still not possible.
- Import loops and validation left the controllers for `RepositoryImporter`,
  `ProjectRequest` and `Project::uniqueSlug()`.
- The four pure classes that decide the most — `SemVer`, `OsDetector`,
  `FilenameInspector`, `UserAgentParser` — got their first tests. The suite goes
  from 137 to 158 passing.

## 0.7.0 - English becomes the site's own language, and the catalogue tells the truth about the apps

### The URL decides the language, and the bare URL is now English

- The unprefixed paths (`/`, `/downloads`, `/p/{slug}`) are English. Portuguese
  moved to the `/pt-br` prefix. A visitor whose browser asks for Portuguese is
  still sent there; anyone else — including a browser that states no preference
  — now stays where it is instead of being redirected.
- Every `/en/…` address published since 0.6.0 answers with a 301 to its bare
  twin, and `/projetos/github-desktop` 301s to `/pt-br/projects/github-desktop`.
  Three URLs cannot be redirected and are not: `/`, `/downloads` and `/p/{slug}`
  were Portuguese and are now English at the same address.
- `/sitemap.xml` lists both languages of every public page with reciprocal
  `xhtml:link` alternates and `x-default`, and `robots.txt` points at it.
- Project cards link to the project page in the language being read.
- **Security:** the language switcher's `to` parameter accepted a
  protocol-relative URL — `?to=//evil.example` passed the `starts_with('/')`
  guard and emitted a cross-origin `Location`. It is now rebuilt from its path
  component instead of validated.
- The admin panel stays Portuguese, by decision.

### The catalogue is checked against the applications themselves

- Descriptions, feature lists and version numbers were verified against each
  application's own repository. ai-usagebar named five providers and has
  fourteen; the GitHub Desktop fork claimed two artifact types and builds six,
  and its multi-repository panel went unmentioned; SShvTerm said nothing about
  its zero-knowledge sync or its self-hostable server.
- The ai-usagebar guide stopped documenting a Windows tray app that is no
  longer in the tree — its install steps told the reader to enter a directory
  that does not exist.
- Every application has a changelog on its page, curated from its own
  changelog, in both languages.
- The seeder fills `upstream_repo`, which it never did — a fresh seed left the
  Monitor tracking nothing.

### Repository hygiene

- This `CHANGELOG.md`, which the project's own rules required and which
  `tools/release.sh` already tried to read.
- Vite and Tailwind, installed and never used, were removed — along with the
  `npm install && npm run build` that ran on every production deploy to produce
  a bundle no page loaded.
- "Downloads today" is counted in one timezone in all three places that show
  it; two of them counted in UTC on a screen that also showed the correct
  number.
- The access audit validates its filters, like the audit screen next to it.
- The admin panel's three security controls have tests, and the suite goes from
  36 passing to 75. `phpunit.xml` now names a database that does not exist, so a
  test that touches one fails instead of writing to the development database.
- The READMEs describe the download hub instead of the blog removed in 0.2.0.

## 0.6.0 - The site learns to speak English

- Bilingual routing, locale negotiation and a language switcher that keeps you
  on the page you were reading.
- The home page, the download list, the project page, the ShvIA models section,
  the GitHub Desktop page and the ai-usagebar guide all speak both languages —
  including dates, thousands separators and the comments inside command blocks.
- Database prose gets its English through `App\Support\Content`, and the
  translator's own fallback stops leaking English back onto Portuguese pages.
- The deploy runs composer when `composer.json` changes, not only when its lock
  does.

## 0.5.12 - The human gate becomes the refused push, not the commit

## 0.5.11 - The agent doc stops contradicting the COMMIT-RULE block

## 0.5.10 - COMMIT-RULE replaces the COMMITTER delegation: the agent commits again

- The COMMITTER kill-switch: the marker stays, the automation stops.

## 0.5.9 - Agent doc: the Releases rule and the English-only language rule

## 0.5.8 - Releases rule in the agent doc: the bump and the Release are one act

## 0.5.7 - Automatic releases: the `version.md` of master becomes a tag and a Release

## 0.5.6 - Fixes the AI-MEMORY module's degradation

- A real probe, a guard in two layers, the failure reason surfaced in the UI,
  and the path of ai-memory 2.x.

## 0.5.5 - A Workspaces screen in AI-MEMORY, with aggregate counts and a link from the dashboard

## 0.5.4 - Fixes the github-visualize fork paths after the reorganisation of `~/x`

- Joins the COMMITTER rollout: marker and PS block in the agent doc.

## 0.5.3 - Adopts the COMMITTER: an opt-in marker for the automatic commit cycle

## 0.5.2 - Reviews the AI-MEMORY screens, fixes the panel's pagination and turns on "live"

## 0.5.1 - Redesigns the AI-MEMORY dashboard

## 0.5.0 - Unifies GitHub View (production) with Monitor/version (local)

- A discreet version in the footer and under the user.
- GitHub View arrived across many commits: the three `github_*` tables, the
  Eloquent models, `GitHubClient` (GraphQL/REST) with an incremental upsert
  job, the listing screen, the day-by-hour commit heatmap, bulk import of a
  user's repositories, an hourly sync command, the `RepositoryOverview`
  dashboard, and organisation-repository discovery with autocomplete search.

## 0.4.4 - SShvTerm and ai-usagebar join the showcase, with Fabio Akita credited

## 0.4.3 - Upload suggests the name and version from the filename, on client and server

## 0.4.2 - The file admin sorts by most recent build, semver-aware

## 0.4.1 - Restores the animated dot-grid in the hero

## 0.4.0 - Redesigns the public showcase

- The Archivo design system, the terminal costume removed, and the SShvTerm
  agent given prominence.

## 0.3.1 - AI-MEMORY: agent and period filters, and sorting by duration in Sessions

## 0.3.0 - The AI-MEMORY module in the admin panel

- A read-only observatory over the external `ai-memory` SQLite index.

## 0.2.8 - Matomo tracking (site 2, config-driven)

- The README is translated to English; the original is preserved in
  `README_br.md`.

## 0.2.7 - The ai-usagebar page joins the menu

- Documentation and installation guides for Linux, macOS and Windows.

## 0.2.6 - A project can be a pure link or a hybrid

- `redirect_to_site` separates a project that lives on its own site from one
  that has both a site and downloads — in the menu, the admin and the showcase.

## 0.2.5 - Hybrid projects, and the ShvIA desktop app becomes downloadable

## 0.2.4 - Files: an Edit button, to correct a file's metadata

## 0.2.3 - The download list adopts the project page's layout, with per-OS badges

## 0.2.2 - Downloads are grouped by operating system

- The `os`, `arch`, `file_type` and `released_at` columns, `FilenameInspector`
  and a backfill.
- The upload form fills OS, architecture and type from the filename, with an
  override.
- OS detection from the User-Agent, version grouping and an install command.
- `/p/{slug}` grouped by OS, with a recommended card.
- Accessibility and responsive polish, plus documentation.

## 0.2.0 - The pivot: from blog to project and download hub

- Public browsing, a single-admin panel, and per-file downloads that are
  counted and audited. The blog is removed.

## 0.1.0 - MySQL replaces SQLite as the default database

- `CLAUDE.md` drops its SQLite reference and fixes the commit format.
