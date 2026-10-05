/**
 * The college district trustees' terms and the links from their candidacies (Nathan, 5 October 2026: "The four cancelled
 * contests become appointed terms once the trustees exist ... The 1973 contest with no return found: record the term if we
 * know who held it, and say the return was not found"). From inventory/review/coc-trustee-terms-2026-10-05.json, structured
 * from the roster (inventory/review/coc-trustees-roster-2026-10-05.json). Each term is an office holding under the College
 * Trustee role (#18329) and the district; a contest cancelled for want of candidates is a sole-candidate term, as the
 * archive's other boards record them. Each candidacy in the district's elections that belongs to a trustee is linked to them.
 * Refuses an option value the fields do not hold, a person who does not exist, an em dash, or a note naming the archive's
 * process. Idempotent: a term is matched by person, start and seat. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_coc_trustee_terms_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $fs = Craft::$app->getFields();
$J = json_decode(file_get_contents("$root/inventory/review/coc-trustee-terms-2026-10-05.json"), true);
$TERMS = $J['terms'] ?? (array_is_list($J) ? $J : []); $LINKS = $J['candidacyLinks'] ?? [];
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|in the mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|Claude|could not be read|[Ss]earched \d)\b~i';
$opt = fn($h) => array_column($fs->getFieldByHandle($h)->options, 'value');
$SEL = array_merge([''], $opt('selectionMethod')); $HOW = $opt('howEnded'); $EV = array_merge([''], $opt('startEvidence'));
$coc = Entry::find()->section('organizations')->title('Santa Clarita Community College District')->one();
/* EDTF the templates can read: they take a year from the first four characters, so a set ("[1982,1983]", either year) becomes
   the interval "1982/1983", and a bound ("[..1992]", that year or before) is left out of the date field, the term's text
   keeping the words. */
$edtf = function ($x) { $x = trim((string)$x); if (preg_match('~^\[(\d{4}(?:-\d{2})?),\s*(\d{4}(?:-\d{2})?)\]$~', $x, $m)) { return "$m[1]/$m[2]"; } return str_starts_with($x, '[') ? '' : $x; };
$bad = []; $plan = [];
foreach ($TERMS as $i => $t) {
  $why = []; $p = Entry::find()->section('persons')->id((int)$t['personId'])->status(null)->one();
  if (!$p) { $why[] = 'no person #' . $t['personId']; }
  if (!in_array($t['selectionMethod'] ?? '', $SEL, true)) { $why[] = 'selectionMethod ' . ($t['selectionMethod'] ?? ''); }
  if (!in_array($t['howEnded'] ?? '', $HOW, true)) { $why[] = 'howEnded ' . ($t['howEnded'] ?? ''); }
  foreach (['startEvidence', 'endEvidence'] as $h) { if (!in_array($t[$h] ?? '', $EV, true)) { $why[] = "$h " . ($t[$h] ?? ''); } }
  $txt = implode(' ', $t['footnotes'] ?? []) . json_encode($t['editorNotes'] ?? []);
  if (preg_match('~[\x{2013}\x{2014}]~u', $txt)) { $why[] = 'em dash'; }
  if (preg_match($BAD, preg_replace('~https?://\S+~', '', $txt))) { $why[] = 'process wording'; }
  if ($why) { $bad[] = ($t['personName'] ?? '?') . ' term ' . ($i + 1) . ': ' . implode(', ', $why); continue; }
  $plan[] = [$p, $t];
}
foreach ($LINKS as $l) { if (!Entry::find()->section('candidacies')->id((int)$l['candidacyId'])->status(null)->exists()) { $bad[] = 'no candidacy #' . $l['candidacyId']; } }
echo count($plan) . ' terms, ' . count($LINKS) . " candidacy links\n" . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad || !$coc) { if (!$coc) { echo "no district record\n"; } return; }
$sec = Craft::$app->getEntries()->getSectionByHandle('officeHoldings'); $n = 0;
foreach ($plan as [$p, $t]) {
  $have = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $p->id, 'field' => 'holdingPerson'], ['targetElement' => $coc->id, 'field' => 'holdingBody']])->all();
  $have = array_filter($have, fn($h) => (string)$h->termStartEdtf === $edtf($t['termStartEdtf'] ?? '') && (string)$h->seatLabel === (string)($t['seatLabel'] ?? '') && (string)$h->termStart === (string)($t['termStart'] ?? ''));
  if ($have) { continue; }
  $h = new Entry(); $h->sectionId = $sec->id; $h->setTypeId($sec->getEntryTypes()[0]->id);
  $notes = array_values($t['footnotes'] ?? []);
  $v = ['holdingPerson' => [$p->id], 'holdingOffice' => [18329], 'holdingBody' => [$coc->id], 'termStart' => (string)($t['termStart'] ?? ''), 'termStartEdtf' => $edtf($t['termStartEdtf'] ?? ''),
    'termEnd' => (string)($t['termEnd'] ?? ''), 'termEndEdtf' => $edtf($t['termEndEdtf'] ?? ''), 'seatLabel' => (string)($t['seatLabel'] ?? ''), 'selectionMethod' => $t['selectionMethod'], 'howEnded' => $t['howEnded'],
    'startEvidence' => $t['startEvidence'] ?? '', 'endEvidence' => $t['endEvidence'] ?? '',
    'footnotes' => array_map(fn($i, $x) => ['number' => (string)($i + 1), 'note' => $x, 'source' => 'editorial-2026'], array_keys($notes), $notes),
    'editorNotes' => array_map(fn($x) => ['heading' => (string)($x['heading'] ?? ''), 'note' => (string)$x['note'], 'position' => 'bottom'], $t['editorNotes'] ?? []),
    'recordProvenance' => 'create_coc_trustee_terms_2026_10_05.php, 5 October 2026'];
  $lay = array_map(fn($f) => $f->handle, $h->getFieldLayout()->getCustomFields());
  $h->setFieldValues(array_intersect_key($v, array_flip($lay)));
  if (!$el->saveElement($h)) { throw new \RuntimeException($p->title . ' ' . json_encode($h->getFirstErrors())); } $n++;
}
foreach ($LINKS as $l) { $c = Entry::find()->section('candidacies')->id((int)$l['candidacyId'])->status(null)->one();
  if ((int)($c->candidacyPerson->status(null)->one()?->id ?? 0) === (int)$l['personId']) { continue; }
  $c->setFieldValue('candidacyPerson', [(int)$l['personId']]); if (!$el->saveElement($c)) { throw new \RuntimeException('candidacy #' . $c->id); } $n++; }
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_coc_trustee_terms_2026_10_05.php', $n, 'verified', 'college district trustees: terms and candidacy links');
echo "done: $n\n";
