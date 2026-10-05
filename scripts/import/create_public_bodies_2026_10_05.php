/**
 * The public bodies /civic was missing (Nathan, 5 October 2026: "Create the missing bodies, but source each one rather than
 * working from general knowledge"; inventory/review/civic-audit-2026-10-05.md). Drafts from the research agents, to
 * inventory/review/public-bodies-brief-2026-10-05.md: inventory/review/public-bodies-<batch>-2026-10-05.json.
 * Saved DISABLED until Nathan has read them; the dry run prints each record as a reader will see it to
 * inventory/review/public-bodies-dry-run-2026-10-05.md. Refuses a draft with "[1, 2]" markers, an em dash, a note naming the
 * archive's process, a marker with no note, an unknown type, role or parent, or a title that already exists.
 * $ONLY limits the run to some titles; $ENABLE saves them enabled. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_public_bodies_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false; $ENABLE = false; $ONLY = [];
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fs = Craft::$app->getFields();
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|in the mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|next to try|to try next|with Nathan|Nathan\'s|Claude|image tag|commented out|read so far|search summary|page was blocked|ha(s|ve) not been (read|checked)|could not be read|[Ss]earched \d|sources searched|lists searched|release search)\b~i';
$opts = fn($h) => array_column($fs->getFieldByHandle($h)->options, 'value');
$TYPES = $opts('orgType'); $ROLES = $opts('civicRole'); $LEVELS = array_merge([''], $opts('orgLevel')); $SCH = array_merge([''], $opts('schoolLevel'));
$P = [];
foreach (glob("$root/inventory/review/public-bodies-*-2026-10-05.json") as $f) { foreach (json_decode(file_get_contents($f), true) ?: [] as $r) { if (isset($r['title'])) { $P[] = $r + ['_file' => basename($f)]; } } }
$os = Craft::$app->getEntries()->getSectionByHandle('organizations'); $plan = []; $bad = []; $out = [];
foreach ($P as $r) {
  $t = trim($r['title']); if ($ONLY && !in_array($t, $ONLY, true)) { continue; } $why = [];
  $body = trim((string)$r['body']); $notes = array_values(array_map(fn($n) => trim(is_array($n) ? ($n['note'] ?? '') : $n), $r['footnotes'] ?? []));
  if (preg_match('~\[\d+, ?\d+~', $body)) { $why[] = 'combined note markers'; }
  if (preg_match('~[\x{2013}\x{2014}]~u', $body . implode(' ', $notes))) { $why[] = 'em or en dash'; }
  foreach ($notes as $i => $n) { if (preg_match($BAD, preg_replace('~https?://\S+~', '', $n))) { $why[] = 'note ' . ($i + 1) . ' names the process'; } }
  preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_unique(array_map('intval', $m[1]));
  foreach ($used as $u) { if ($u < 1 || $u > count($notes)) { $why[] = "marker [$u] has no note"; } }
  foreach (['orgType' => $TYPES, 'civicRole' => $ROLES, 'orgLevel' => $LEVELS, 'schoolLevel' => $SCH] as $h => $ok) { if (!in_array((string)($r[$h] ?? ''), $ok, true)) { $why[] = "$h '" . ($r[$h] ?? '') . "' unknown"; } }
  $parent = trim((string)($r['parentTitle'] ?? '')) !== '' ? Entry::find()->section('organizations')->title(trim($r['parentTitle']))->status(null)->one() : null;
  if (trim((string)($r['parentTitle'] ?? '')) !== '' && !$parent) { $why[] = "parent '{$r['parentTitle']}' not found"; }
  $have = Entry::find()->section('organizations')->title($t)->status(null)->one();
  if ($have && !str_contains((string)$have->recordProvenance, 'create_public_bodies_2026_10_05')) { $why[] = "a record titled this exists (#{$have->id})"; }
  if ($why) { $bad[] = "$t: " . implode(', ', $why); continue; }
  $v = ['orgType' => $r['orgType'], 'schoolLevel' => $r['schoolLevel'] ?? '', 'orgLevel' => $r['orgLevel'] ?? '', 'civicRole' => $r['civicRole'],
    'hasParentOrg' => (bool)$parent, 'parentOrganization' => $parent ? [$parent->id] : [], 'dateFounded' => (string)($r['dateFounded'] ?? ''), 'dateFoundedEdtf' => (string)($r['dateFoundedEdtf'] ?? ''),
    'dateDissolved' => (string)($r['dateDissolved'] ?? ''), 'dateDissolvedEdtf' => (string)($r['dateDissolvedEdtf'] ?? ''), 'orgAddress' => (string)($r['orgAddress'] ?? ''),
    'body' => $body, 'footnotes' => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes),
    'editorNotes' => array_map(fn($x) => ['heading' => (string)($x['heading'] ?? ''), 'note' => (string)$x['note'], 'position' => 'bottom'], $r['editorNotes'] ?? []),
    'recordProvenance' => 'create_public_bodies_2026_10_05.php, 5 October 2026, from ' . $r['_file']];
  $plan[] = [$t, $have, $v, (string)($r['orgWebsite'] ?? '')];
  $out[] = "## $t\n\n{$r['orgType']}" . (($r['schoolLevel'] ?? '') ? "/{$r['schoolLevel']}" : '') . ", level " . ($r['orgLevel'] ?: 'none') . ", role {$r['civicRole']}" . ($parent ? ", under {$parent->title}" : '') . ". Founded " . ($r['dateFounded'] ?: 'not given') . ".\n\n$body\n\n"
    . implode("\n", array_map(fn($i, $n) => '[' . ($i + 1) . "] $n", array_keys($notes), $notes))
    . (($r['editorNotes'] ?? []) ? "\n\nEditor notes:\n- " . implode("\n- ", array_map(fn($x) => ($x['heading'] ?? '') . ': ' . $x['note'], $r['editorNotes'])) : '')
    . (($r['openQuestions'] ?? []) ? "\n\nFor Nathan:\n- " . implode("\n- ", $r['openQuestions']) : '') . "\n";
}
file_put_contents("$root/inventory/review/public-bodies-dry-run-2026-10-05.md", "# The missing public bodies: the dry run\n\n" . count($plan) . " records ready, " . count($bad) . " refused. Saved disabled until Nathan has read them.\n\n" . ($bad ? "Refused:\n- " . implode("\n- ", $bad) . "\n\n" : '') . implode("\n", $out));
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ': ' . count($plan) . ' ready, ' . count($bad) . ' refused' . PHP_EOL; foreach ($bad as $b) { echo "REFUSED $b\n"; }
if (!$APPLY) { return; }
$n = 0;
foreach ($plan as [$t, $e, $v, $web]) {
  if (!$e) { $e = new Entry(); $e->sectionId = $os->id; $e->setTypeId($os->getEntryTypes()[0]->id); $e->title = $t; }
  $e->enabled = $ENABLE;
  $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
  $e->setFieldValues(array_intersect_key($v, array_flip($h)));
  if ($web !== '' && in_array('orgWebsite', $h, true)) { $e->setFieldValue('orgWebsite', ['type' => 'url', 'value' => $web]); }
  if (!$el->saveElement($e)) { throw new \RuntimeException("$t " . json_encode($e->getFirstErrors())); } $n++;
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_public_bodies_2026_10_05.php', $n, 'verified', "missing public bodies: $n" . ($ENABLE ? '' : ', disabled'));
echo "done: $n\n";
