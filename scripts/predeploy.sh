#!/bin/bash
# Run before every push that deploys (DEPLOY-RUNBOOK section 2): the render check,
# then every page whose record has a body, checked for its words. About eight minutes.
# Exits non-zero if either fails, so `scripts/predeploy.sh && git push` never ships a broken page.
cd "$(dirname "$0")/.." || exit 1

# Large files (Nathan, 3 October 2026). GitHub rejects a file over 100 MB and warns
# over 50 MB; a 143 MB PDF in an unpushed commit blocked the push and the staging
# refresh. Fails if any commit not yet pushed adds a file over 50 MB, unless that
# path is already in the pushed history (lw-features.json, 53 MB, has been on
# GitHub since September and is under the hard limit). Runs first: it takes seconds.
echo "large files..."
LIMIT=52428800
if UP=$(git rev-parse --abbrev-ref --symbolic-full-name '@{upstream}' 2>/dev/null); then BASE="$UP"; else BASE=origin/main; fi
big=$(git rev-list --objects "$BASE"..HEAD | git cat-file --batch-check='%(objecttype) %(objectsize) %(rest)' |
  awk -v L=$LIMIT '$1=="blob" && $2>L {s=$2; $1=""; $2=""; sub(/^  /,""); print s "\t" $0}')
fail_big=""
while IFS=$'\t' read -r size path; do
  [ -z "$path" ] && continue
  if [ -n "$(git rev-list -1 "$BASE" -- "$path" 2>/dev/null)" ]; then
    echo "   $(( size / 1048576 )) MB  $path: over 50 MB, already in the pushed history, allowed"
  else
    echo "   LARGE FILE $(( size / 1048576 )) MB  $path: added by a commit not yet pushed"
    fail_big=1
  fi
done <<< "$big"
if [ -n "$fail_big" ]; then
  echo "PREDEPLOY FAILED: a commit not yet pushed adds a file over 50 MB. Take it out of the commits and gitignore it; keep its text and a manifest (see inventory/legacy/fetched/nara-*/manifest.json)."
  exit 1
fi
echo "no file over 50 MB added since $BASE"

echo "check_render..."
out=$(ddev craft exec "eval(file_get_contents('scripts/import/check_render.php'))" 2>&1)
echo "$out" | grep -E "checked|NAV COVERAGE|nav links|FAIL|SYNTAX|UNREACHABLE|no failures"
echo "$out" | grep -q "^no failures" || { echo "PREDEPLOY FAILED: check_render"; exit 1; }
echo "rendered bodies, every record (about seven minutes)..."
out=$(ddev craft exec '$SAMPLE = 0; eval(file_get_contents("scripts/import/check_rendered_bodies.php"));' 2>&1)
echo "$out" | grep -E '^[a-zA-Z]+ +\{|FAILURES|^MISSING|^NO PAGE|every checked body'
echo "$out" | grep -q "every checked body that should show, shows" || { echo "PREDEPLOY FAILED: a body is missing from its page"; exit 1; }
echo "PREDEPLOY OK"
