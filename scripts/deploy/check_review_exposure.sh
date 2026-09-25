#!/usr/bin/env bash
# Is the archive's working material readable by the public?
#
# web/review holds the reconciliation queues, the ledger index, the fidelity
# files and photo-links.json: legacy URLs, record titles, 8,359 unmatched
# names, and in places the full text of the archive. A git pull on the server
# publishes whatever is in that directory, and on this host the only thing that
# can stop it being served is an Nginx location block, because Cloudways puts
# Nginx in front of Apache and never reads web/.htaccess. See
# docs/DEPLOY-RUNBOOK.md section 5.
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
