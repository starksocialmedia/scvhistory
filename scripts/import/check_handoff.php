<?php
/**
 * HANDOFF.md stays a state document, current and rule-free (Nathan, 3 October
 * 2026: "HANDOFF being from 18 September means every session for two weeks opened
 * on a stale document, and a rule that is no longer true is worse than no rule").
 *
 * Every session reads HANDOFF first. When it restated rules that live elsewhere,
 * its copy went stale and beat the source: the image rule came back twice from
 * HANDOFF after DATA-MODEL had changed. So HANDOFF says where things stand and
 * points at the rules; each rule lives in one place.
 *
 * Fails when:
 *   - HANDOFF.md's dated heading is more than 2 days older than the newest dated
 *     entry in CHANGELOG.md (the end-of-session duty in AGENTS.md rewrites it);
 *   - HANDOFF.md has a section of rules ("Rules", "Rules learned the hard way");
 *   - a line outside the "Where the rules live" section states a rule in the
 *     imperative: it begins "Never", "Always", "Do not", "Don't", or "Must".
 * Read-only. Called by check_render.php; also runnable alone:
 *   ddev craft exec "eval(substr(file_get_contents('scripts/import/check_handoff.php'), 5))"
 */
$root = \Craft::getAlias('@root');
$fails = [];
$h = (string)@file_get_contents("$root/HANDOFF.md");
$c = (string)@file_get_contents("$root/CHANGELOG.md");
if ($h === '') { $fails[] = 'HANDOFF.md is missing'; }
preg_match('~^#\s.*?(\d{4}-\d{2}-\d{2})~m', $h, $hd);
preg_match_all('~^(\d{4}-\d{2}-\d{2})~m', $c, $cd);
$hDate = $hd[1] ?? null; $cDate = $cd[1] ? max($cd[1]) : null;
if (!$hDate) { $fails[] = 'HANDOFF.md has no dated heading (# Handoff, YYYY-MM-DD)'; }
elseif ($cDate && (strtotime($cDate) - strtotime($hDate)) > 2 * 86400) { $fails[] = "HANDOFF.md is dated $hDate; the newest CHANGELOG entry is $cDate. Rewrite it (AGENTS.md, End of every session)"; }
if (preg_match('~^#{1,3}\s*Rules\b~mi', $h)) { $fails[] = 'HANDOFF.md has a rules section; rules live in their own documents, and HANDOFF points at them'; }
$inPointers = false;
foreach (preg_split('~\R~', $h) as $i => $line) {
    if (preg_match('~^#{1,3}\s~', $line)) { $inPointers = (bool)preg_match('~where the rules live~i', $line); continue; }
    if ($inPointers) { continue; }
    if (preg_match('~^\s*(?:[-*]|\d+\.)?\s*\**(Never|Always|Do not|Don\'t|Must)\b~', $line)) { $fails[] = 'HANDOFF.md line ' . ($i + 1) . ' states a rule: "' . mb_substr(trim($line), 0, 80) . '". Move it to its home document and point at it'; }
}
echo ($fails ? 'HANDOFF ' . implode(PHP_EOL . 'HANDOFF ', $fails) : "HANDOFF.md current ($hDate; newest CHANGELOG $cDate) and states no rules") . PHP_EOL;
return ['ok' => !$fails, 'fails' => $fails];
