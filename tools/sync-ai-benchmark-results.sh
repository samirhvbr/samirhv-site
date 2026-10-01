#!/usr/bin/env bash
# sync-ai-benchmark-results.sh — keep samirhv/resources/data/ai-benchmark/results.json
# a byte-identical copy of results/results.json in samirhvbr/ai-benchmark, and
# samirhv/public/data/ai-benchmark/{runs,flaws}.csv byte-identical copies of the
# CSVs exported next to it (offered for download on the page).
#
# WHY THIS EXISTS: the /ai-benchmark page shows numbers that are produced and
# audited in ai-benchmark, where tools/export-results.py builds results.json
# from the scorecards. A number edited here would disagree with the scorecard
# that justifies it, so the file is copied, never edited.
#
#   tools/sync-ai-benchmark-results.sh               # copy from ai-benchmark master
#   tools/sync-ai-benchmark-results.sh --ref <sha>   # copy from a commit or branch
#   tools/sync-ai-benchmark-results.sh --from <dir>  # read a local clone, not GitHub
#                                                    # (its "master" may be stale:
#                                                    #  pass --ref origin/master)
#   tools/sync-ai-benchmark-results.sh --check       # exit 1 if the copy differs
#                                                    # from upstream
#
# A sync rewrites results.json and UPSTREAM.json. It commits nothing. Run the
# suite afterwards: AiBenchmarkPageTest fails on a file the page cannot read.
# The per-evaluation copy (highlights, caveats) lives in lang/*/ai_benchmark.php
# and is NOT synced — reread it whenever the numbers change.
#
# Exit codes: 0 in sync, or synced · 1 the copy differs (--check) · 2 could not
# measure. "Could not measure" is never 0: a check that could not reach
# upstream has not checked anything.
set -euo pipefail

UPSTREAM_URL=https://github.com/samirhvbr/ai-benchmark.git
UPSTREAM_PATH=results/results.json
ROOT=$(cd "$(dirname "$0")/.." && pwd)
DEST_DIR=$ROOT/samirhv/resources/data/ai-benchmark
DEST=$DEST_DIR/results.json
MANIFEST=$DEST_DIR/UPSTREAM.json
CSV_DIR=$ROOT/samirhv/public/data/ai-benchmark
CSVS="runs.csv flaws.csv"

MODE=sync
REF=master
FROM=""
while [ $# -gt 0 ]; do
    case "$1" in
        --check) MODE=check ;;
        --ref) REF=${2:?--ref needs a commit or branch}; shift ;;
        --from) FROM=${2:?--from needs a path}; shift ;;
        -h|--help) sed -n '2,25p' "$0"; exit 0 ;;
        *) echo "sync-ai-benchmark-results: unknown argument: $1" >&2; exit 2 ;;
    esac
    shift
done

die() { echo "sync-ai-benchmark-results: $* — did not measure" >&2; exit 2; }

TMP=$(mktemp -d)
trap 'rm -rf "$TMP"' EXIT

if [ -n "$FROM" ]; then
    GIT=(git -C "$FROM")
    "${GIT[@]}" rev-parse --git-dir >/dev/null 2>&1 || die "$FROM is not a git repository"
else
    # Bare and blob-less: only the one blob we read is fetched.
    git clone --quiet --bare --filter=blob:none "$UPSTREAM_URL" "$TMP/upstream.git" \
        || die "could not clone $UPSTREAM_URL"
    GIT=(git -C "$TMP/upstream.git")
fi

SHA=$("${GIT[@]}" rev-parse --verify --quiet "$REF^{commit}") || die "no commit '$REF' upstream"
# To a file, never through $(...): command substitution strips the trailing
# newline, and the copy has to be byte-identical.
"${GIT[@]}" show "$SHA:$UPSTREAM_PATH" > "$TMP/results.json" 2>/dev/null \
    || die "$UPSTREAM_PATH does not exist at $SHA"
python3 -m json.tool "$TMP/results.json" >/dev/null 2>&1 \
    || die "$UPSTREAM_PATH at $SHA is not valid JSON"
for f in $CSVS; do
    "${GIT[@]}" show "$SHA:results/$f" > "$TMP/$f" 2>/dev/null || die "results/$f does not exist at $SHA"
    head -n 1 "$TMP/$f" | grep -q '^edition,' || die "results/$f at $SHA has no CSV header"
done

if [ "$MODE" = check ]; then
    same=1
    [ -f "$DEST" ] && cmp -s "$TMP/results.json" "$DEST" || same=0
    for f in $CSVS; do [ -f "$CSV_DIR/$f" ] && cmp -s "$TMP/$f" "$CSV_DIR/$f" || same=0; done
    if [ "$same" = 1 ]; then
        echo "sync-ai-benchmark-results: in sync with ai-benchmark@${SHA:0:12}"
        exit 0
    fi
    echo "sync-ai-benchmark-results: results.json or a CSV differs from ai-benchmark@${SHA:0:12} — run tools/sync-ai-benchmark-results.sh" >&2
    exit 1
fi

mkdir -p "$DEST_DIR"
cp "$TMP/results.json" "$DEST"
mkdir -p "$CSV_DIR"
for f in $CSVS; do cp "$TMP/$f" "$CSV_DIR/$f"; done
printf '{\n  "repository": "samirhvbr/ai-benchmark",\n  "path": "%s",\n  "commit": "%s"\n}\n' \
    "$UPSTREAM_PATH" "$SHA" > "$MANIFEST"
echo "sync-ai-benchmark-results: copied $UPSTREAM_PATH, results/runs.csv and results/flaws.csv from ai-benchmark@${SHA:0:12}"
