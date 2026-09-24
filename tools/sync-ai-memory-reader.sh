#!/usr/bin/env bash
# sync-ai-memory-reader.sh — keep samirhv/app/Services/AiMemory/*.php a
# byte-identical copy of the reader classes of samirhvbr/ai-memory-web.
#
# WHY THIS EXISTS: the admin's AI-MEMORY module and the standalone ai-memory-web
# app run the same eleven classes. Until 24/09/2026 they were two forks, and they
# had drifted 217 lines apart. The owner then chose one source (ai-memory-web)
# and a copy that is never edited by hand. The decision is ADR-006 in
# ai-memory-web, and samirhv/docs/AI-MEMORY.md §6 here.
#
#   tools/sync-ai-memory-reader.sh               # copy from ai-memory-web master
#   tools/sync-ai-memory-reader.sh --ref <sha>   # copy from a commit or branch
#   tools/sync-ai-memory-reader.sh --from <dir>  # read a local clone, not GitHub
#                                                # (its "master" may be stale:
#                                                #  pass --ref origin/master)
#   tools/sync-ai-memory-reader.sh --check       # exit 1 if the copy differs
#                                                # from upstream (CI runs this)
#
# A sync rewrites the .php files and UPSTREAM.json, and deletes a class that
# upstream no longer has. It commits nothing. Run the suite afterwards:
# ReaderCopyTest names any string missing from lang/pt_BR.json and any config
# key missing from config/aimemory.php.
#
# Exit codes: 0 in sync, or synced · 1 the copy differs (--check) · 2 could not
# measure. "Could not measure" is never 0: a check that could not reach
# upstream has not checked anything.
set -euo pipefail

UPSTREAM_URL=https://github.com/samirhvbr/ai-memory-web.git
UPSTREAM_WEB=https://github.com/samirhvbr/ai-memory-web
UPSTREAM_DIR=app/Services/AiMemory
ROOT=$(cd "$(dirname "$0")/.." && pwd)
DEST=$ROOT/samirhv/app/Services/AiMemory
MANIFEST=$DEST/UPSTREAM.json

MODE=sync
REF=master
FROM=""
while [ $# -gt 0 ]; do
    case "$1" in
        --check) MODE=check ;;
        --ref) REF=${2:?--ref needs a commit or branch}; shift ;;
        --from) FROM=${2:?--from needs a path}; shift ;;
        -h|--help) sed -n '2,27p' "$0"; exit 0 ;;
        *) echo "sync-ai-memory-reader: unknown argument: $1" >&2; exit 2 ;;
    esac
    shift
done

die() { echo "sync-ai-memory-reader: $* — did not measure" >&2; exit 2; }

if [ -n "$FROM" ]; then
    GIT=(git -C "$FROM")
    "${GIT[@]}" rev-parse --git-dir >/dev/null 2>&1 || die "$FROM is not a git repository"
else
    TMP=$(mktemp -d)
    trap 'rm -rf "$TMP"' EXIT
    # Bare and blob-less: the history is a few hundred KiB, and only the
    # eleven blobs we read are fetched.
    git clone --quiet --bare --filter=blob:none "$UPSTREAM_URL" "$TMP/upstream.git" \
        || die "could not clone $UPSTREAM_URL"
    GIT=(git -C "$TMP/upstream.git")
fi

SHA=$("${GIT[@]}" rev-parse --verify --quiet "$REF^{commit}") || die "ref '$REF' not found upstream"
FILES=$("${GIT[@]}" ls-tree --name-only "$SHA" "$UPSTREAM_DIR/" | sed -n "s#^$UPSTREAM_DIR/\\(.*\\.php\\)\$#\\1#p")
[ -n "$FILES" ] || die "no .php under $UPSTREAM_DIR at $SHA"
VERSION=$("${GIT[@]}" show "$SHA:version.md" | grep -oE '[0-9]+\.[0-9]+\.[0-9]+' | head -n1) \
    || die "no version.md at $SHA"

if [ "$MODE" = check ]; then
    problems=()
    for f in $FILES; do
        if [ ! -f "$DEST/$f" ]; then
            problems+=("missing here:  $f")
        elif ! "${GIT[@]}" show "$SHA:$UPSTREAM_DIR/$f" | cmp -s - "$DEST/$f"; then
            problems+=("differs:       $f")
        fi
    done
    for path in "$DEST"/*.php; do
        [ -e "$path" ] || continue
        name=$(basename "$path")
        grep -qxF "$name" <<<"$FILES" || problems+=("only here:     $name")
    done

    if [ ${#problems[@]} -eq 0 ]; then
        echo "in sync with ai-memory-web $VERSION ($SHA)"
        exit 0
    fi

    printf '%s\n' "${problems[@]}" >&2
    pinned=$(sed -n 's/.*"commit": *"\([0-9a-f]*\)".*/\1/p' "$MANIFEST" 2>/dev/null || true)
    echo >&2
    echo "The copy differs from ai-memory-web $VERSION ($SHA)." >&2
    [ -n "$pinned" ] && echo "UPSTREAM.json pins $pinned." >&2
    echo "These files are a copy: change a class in ai-memory-web, never here. Then run" >&2
    echo "tools/sync-ai-memory-reader.sh and the suite, and commit the result." >&2
    exit 1
fi

mkdir -p "$DEST"
for path in "$DEST"/*.php; do
    [ -e "$path" ] || continue
    name=$(basename "$path")
    if ! grep -qxF "$name" <<<"$FILES"; then
        rm -- "$path"
        echo "removed   $name (no longer upstream)"
    fi
done
for f in $FILES; do
    "${GIT[@]}" show "$SHA:$UPSTREAM_DIR/$f" >"$DEST/$f"
done

php -r '
    [, $dest, $sha, $version, $web] = $argv;
    $files = [];
    foreach (glob($dest."/*.php") as $p) {
        $files[basename($p)] = hash_file("sha256", $p);
    }
    ksort($files);
    echo json_encode([
        "rule" => "Do not edit these .php files here. Change them in ai-memory-web, then run tools/sync-ai-memory-reader.sh (ADR-006 there, docs/AI-MEMORY.md §6 here).",
        "source" => $web,
        "path" => "app/Services/AiMemory",
        "version" => $version,
        "commit" => $sha,
        "files" => $files,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "\n";
' "$DEST" "$SHA" "$VERSION" "$UPSTREAM_WEB" >"$MANIFEST"

echo "synced $(wc -w <<<"$FILES" | tr -d ' ') files from ai-memory-web $VERSION ($SHA)"
echo "next: cd samirhv && php artisan test — ReaderCopyTest names any string or config key this app still lacks"
