#!/usr/bin/env bash
# Is the archive's working material readable by the public?
#
# The review screens and their queues are working material: reconciliation
# queues, the ledger index, 8,359 unmatched names in photo-links.json, and in
# the fidelity files the full text of the archive. They live at <project>/review,
# outside the web root, so no host can serve them. This checks that it stayed
# that way, two ways, because each one misses what the other catches:
#
#   1. the repository: nothing under web/ may hold them, or the next deploy
#      publishes whatever was added there. Local, deterministic, no credentials.
#   2. the live host: nothing may be readable, including files an earlier deploy
#      left behind that git no longer tracks.
#
# See docs/DEPLOY-RUNBOOK.md section 5.
#
# Basic auth is not the control. While staging is closed every URL answers 401,
# which looks like safety and hides whether the block exists; the day the site
# opens, the 401 goes and whatever is unblocked is public. So this asks twice:
# once as a stranger, once with the credentials. A 200 with credentials means
# the file is really served and only basic auth was hiding it.
#
#   HOST=https://phpstack-1656314-6593553.cloudwaysapps.com \
#   AUTH=user:password scripts/deploy/check_review_exposure.sh
#
# Exit 0  every path refused with credentials
# Exit 1  something is readable: do not deploy
# Exit 2  cannot prove it either way (no credentials given, or the host is
#         unreachable). Not a pass.
set -uo pipefail

HOST="${HOST:-}"
AUTH="${AUTH:-}"
if [ -z "$HOST" ]; then echo "set HOST, e.g. HOST=https://phpstack-1656314-6593553.cloudwaysapps.com"; exit 2; fi

PATHS=(
  /review/
  /review/photo-links.json
  /review/audit.json
  /review/ledger-index.json
  /review/entities.html
  /review/records.html
  /review/fidelity-summary.json
  /review/correspondence.csv
  /admin-overview
  /admin-ledger
  /admin-ledger/data
  /admin-quality
  /admin-fixes
  /graph
  /graph/data
)

# ---------------------------------------------------------------- the repo
repo_fail=0
if [ -d .git ] || git rev-parse --git-dir >/dev/null 2>&1; then
  tracked=$(git ls-files 'web/review' 'web/review/*' | wc -l | tr -d ' ')
  present=$([ -e web/review ] && echo yes || echo no)
  echo "repository: files tracked under web/review: $tracked; directory present: $present"
  if [ "$tracked" != "0" ]; then
    echo "FAIL: $tracked file(s) under web/review are tracked and would be published by the next deploy."
    echo "They belong at <project>/review, outside the web root. See docs/DEPLOY-RUNBOOK.md section 5."
    repo_fail=1
  fi
  if [ "$present" = "yes" ]; then
    echo "WARNING: web/review exists locally. Untracked, so it will not deploy, but a script is writing to the old path."
  fi
else
  echo "repository: not a git checkout, skipping the tracked-files test"
fi
echo

echo "checking $HOST"
[ -n "$AUTH" ] && echo "with credentials" || echo "WITHOUT credentials: 401 everywhere proves nothing"
printf '%-34s %-12s %s\n' path anonymous 'with credentials'

exposed=0; unproven=0; unreachable=0
for p in "${PATHS[@]}"; do
  anon=$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "$HOST$p" || echo 000)
  if [ -n "$AUTH" ]; then
    authed=$(curl -s -u "$AUTH" -o /dev/null -w '%{http_code}' --max-time 20 "$HOST$p" || echo 000)
  else
    authed='-'
  fi
  note=''
  if [ "$anon" = "000" ] || [ "$authed" = "000" ]; then note='UNREACHABLE'; unreachable=$((unreachable+1))
  elif [ "$authed" = "200" ]; then note='READABLE - do not deploy'; exposed=$((exposed+1))
  elif [ "$authed" = "-" ]; then
    if [ "$anon" = "200" ]; then note='READABLE - do not deploy'; exposed=$((exposed+1))
    else note='unproven, ask with credentials'; unproven=$((unproven+1)); fi
  fi
  printf '%-34s %-12s %-6s %s\n' "$p" "$anon" "$authed" "$note"
done

echo
if [ "$repo_fail" -gt 0 ]; then
  echo "FAIL: the repository would publish working material; see above."
  exit 1
fi
if [ "$exposed" -gt 0 ]; then
  echo "FAIL: $exposed path(s) served to somebody holding the staging password."
  echo "Add the location block from docs/DEPLOY-RUNBOOK.md section 5 in the Cloudways panel"
  echo "(Application > Application Settings > Nginx Settings), restart Nginx, and run this again."
  exit 1
fi
if [ "$unreachable" -gt 0 ]; then echo "FAIL: $unreachable path(s) unreachable; this proved nothing."; exit 2; fi
if [ "$unproven" -gt 0 ]; then
  echo "NOT PROVEN: every path was refused, but without credentials that may be basic auth alone."
  echo "Run again with AUTH=user:password before deploying."
  exit 2
fi
echo "OK: every working path refused with credentials in hand."
