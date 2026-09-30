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
