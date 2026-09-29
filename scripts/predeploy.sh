#!/bin/bash
# Run before every push that deploys (DEPLOY-RUNBOOK section 2): the render check,
# then every page whose record has a body, checked for its words. About eight minutes.
# Exits non-zero if either fails, so `scripts/predeploy.sh && git push` never ships a broken page.
cd "$(dirname "$0")/.." || exit 1
echo "check_render..."
out=$(ddev craft exec "eval(file_get_contents('scripts/import/check_render.php'))" 2>&1)
echo "$out" | grep -E "checked|NAV COVERAGE|nav links|FAIL|SYNTAX|UNREACHABLE|no failures"
echo "$out" | grep -q "^no failures" || { echo "PREDEPLOY FAILED: check_render"; exit 1; }
echo "rendered bodies, every record (about seven minutes)..."
out=$(ddev craft exec '$SAMPLE = 0; eval(file_get_contents("scripts/import/check_rendered_bodies.php"));' 2>&1)
echo "$out" | grep -E '^[a-zA-Z]+ +\{|FAILURES|^MISSING|^NO PAGE|every checked body'
echo "$out" | grep -q "every checked body that should show, shows" || { echo "PREDEPLOY FAILED: a body is missing from its page"; exit 1; }
echo "PREDEPLOY OK"
