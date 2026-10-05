/**
 * The fourteen fallen officers, from inventory/review/fallen-officers-draft-2026-10-05.json (sources saved in
 * inventory/news/fallen-officers-2026-10-05/). Nathan, 5 October 2026: "Include all fourteen, in two groups"; the section and
 * its fields are add_fallen_officers_section_2026_10_05.php.
 * Saved DISABLED: the pages exist for Nathan to read, as the archivist's dry run of prose, and go public when he enables them
 * (or when this is re-run with $ENABLE = true).
 * The agency is related where it has a record (the Sheriff's Department, #29282); the CHP, LAPD, Burbank Police and the
 * constables have none yet, and their names go in the assignment line until Nathan decides those records. Images are not
 * imported here.
 * Refuses: an em dash, a note naming the archive's process, a marker with no note or a note never cited.
 * Idempotent: matched by title. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_fallen_officers_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$ENABLE = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|in the mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|next to try|to try next|with Nathan|Nathan\'s|Claude|image tag|commented out|read so far|search summary|page was blocked|ha(s|ve) not been (read|checked)|could not be read|[Ss]earched \d|sources searched|lists searched|release search)\b~i';
$D = json_decode(file_get_contents("$root/inventory/review/fallen-officers-draft-2026-10-05.json"), true);
$sec = Craft::$app->getEntries()->getSectionByHandle('fallenOfficers');
$flat = fn($v) => is_array($v) ? implode("\n", array_map(fn($m) => is_array($m) ? trim(implode(': ', array_filter([$m['memorial'] ?? '', $m['detail'] ?? '']))) . (trim((string)($m['notes'] ?? '')) !== '' ? ' (note' . (str_contains((string)$m['notes'], ',') ? 's ' : ' ') . trim((string)$m['notes']) . ')' : '') : (string)$m, $v)) : (string)$v;
$plan = []; $bad = [];
foreach ($D['records'] as $r) {
  $t = trim($r['title']); $why = [];
  $notes = array_values(array_map(fn($n) => trim(is_array($n) ? ($n['note'] ?? '') : $n), $r['footnotes'] ?? []));
  $body = trim((string)$r['body']);
  if (preg_match('~[\x{2013}\x{2014}]~u', $body . implode(' ', $notes))) { $why[] = 'em or en dash'; }
  foreach ($notes as $i => $n) { if (preg_match($BAD, preg_replace('~https?://\S+~', '', $n))) { $why[] = 'note ' . ($i + 1) . ' names the process'; } }
  /* A note is cited by a marker in the body, a fact row's notes, or "(note n)" in an editor's note, as on the war memorial. */
  preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_map('intval', $m[1]);
  foreach ($r['factSources'] ?? [] as $x) { foreach (explode(',', (string)$x['notes']) as $u) { if (trim($u) !== '') { $used[] = (int)$u; } } }
  foreach (is_array($r['foMemorials'] ?? null) ? $r['foMemorials'] : [] as $x) { foreach (explode(',', (string)($x['notes'] ?? '')) as $u) { if (trim($u) !== '') { $used[] = (int)$u; } } }
  foreach ($r['editorNotes'] ?? [] as $x) { preg_match_all('~notes? (\d+)(?:(?:, | and )(\d+))*~', (string)$x['note'], $mm, PREG_SET_ORDER); foreach ($mm as $g) { foreach (array_slice($g, 1) as $u) { if ($u !== '') { $used[] = (int)$u; } } } }
  $used = array_unique($used);
  foreach ($used as $u) { if ($u < 1 || $u > count($notes)) { $why[] = "marker [$u] has no note"; } }
  /* A note no marker, fact row, memorial or editor's note cites is a source listed and not used: a warning, not a refusal,
     since these records are saved disabled for Nathan to read. */
  for ($i = 1; $i <= count($notes); $i++) { if (!in_array($i, $used, true)) { echo "WARNING $t: note $i is not cited\n"; } }
  $agencyText = (string)($r['foAgency'] ?? '');
  $agencyId = preg_match('~#29282\b~', $agencyText) ? 29282 : null;
  $assign = trim((string)($r['foAssignment'] ?? ''));
  if (!$agencyId) { $name = trim(preg_split('~[;(]~', $agencyText)[0]); $assign = $assign !== '' && stripos($assign, $name) === false ? "$name; $assign" : ($assign ?: $name); }
  if ($why) { $bad[] = "$t: " . implode(', ', $why); continue; }
  $have = Entry::find()->section('fallenOfficers')->title($t)->status(null)->one();
  $plan[] = [$t, $have, [
    'foAgency' => $agencyId ? [$agencyId] : [], 'foRank' => (string)($r['foRank'] ?? ''), 'foAssignment' => $assign, 'foBadge' => (string)($r['foBadge'] ?? ''),
    'deathDate' => (string)($r['deathDate'] ?? ''), 'deathDateEdtf' => (string)($r['deathDateEdtf'] ?? ''), 'foIncidentLocation' => (string)($r['foIncidentLocation'] ?? ''),
    'foCircumstances' => (string)($r['foCircumstances'] ?? ''), 'foValleyTie' => (string)($r['foValleyTie'] ?? 'killed-here'),
    'birthDate' => (string)($r['birthDate'] ?? ''), 'birthDateEdtf' => (string)($r['birthDateEdtf'] ?? ''), 'burialPlace' => (string)($r['burialPlace'] ?? ''),
    'foMemorials' => $flat($r['foMemorials'] ?? ''), 'body' => $body,
    'footnotes' => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes),
    'factSources' => array_map(fn($x) => ['fact' => (string)$x['fact'], 'value' => (string)$x['value'], 'notes' => (string)$x['notes'], 'agreement' => (string)($x['agreement'] ?? '')], $r['factSources'] ?? []),
    'editorNotes' => array_map(fn($x) => ['heading' => (string)($x['heading'] ?? ''), 'note' => (string)$x['note'], 'position' => 'bottom'], $r['editorNotes'] ?? []),
    'legacyUrl' => (string)($r['legacyUrl'] ?? ''), 'legacyKey' => (string)($r['legacyKey'] ?? ''),
    'recordProvenance' => 'create_fallen_officers_2026_10_05.php, 5 October 2026']];
  echo "$t: " . ($have ? "#{$have->id} exists" : 'create') . ', ' . count($notes) . ' notes, ' . count($r['factSources'] ?? []) . ' facts, ' . ($agencyId ? 'agency #' . $agencyId : "agency as text: $assign") . ', ' . ($r['foValleyTie'] ?? '') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0;
foreach ($plan as [$t, $e, $v]) {
  if (!$e) { $e = new Entry(); $e->sectionId = $sec->id; $e->typeId = $sec->getEntryTypes()[0]->id; $e->title = $t; }
  $e->enabled = $ENABLE;
  $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
  $e->setFieldValues(array_intersect_key($v, array_flip($h)));
  if (!$el->saveElement($e)) { throw new \RuntimeException("$t " . json_encode($e->getFirstErrors())); } $n++;
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_fallen_officers_2026_10_05.php', $n, 'verified', "fallen officers: $n records" . ($ENABLE ? ', enabled' : ', disabled for Nathan to read'));
echo "done: $n" . PHP_EOL;
