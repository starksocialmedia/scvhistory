/**
 * Three things Nathan approved on 5 October 2026.
 * 1. Mentry's birth year as a source fault ("The Mentry note about software that now exists: do the work rather than moving
 *    the note"). His death certificate (#20102) gives his birth as 27 March 1847 and an age at death, 4 October 1900, of 52
 *    years, 6 months and 8 days, which counts back to 27 March 1848. A SourceFault on his record, field birthDate, carries
 *    the disagreement; the person page says it under the date. The reading is not decided: the date stays 1847?-03-27,
 *    uncertain in the year alone. The editor's note keeps the reader's account and loses its "held ... until" sentence.
 * 2. "the original page on SCVHistory.com" for "the legacy page" in every editor's note ("Legacy is our word, not a
 *    reader's"): 22 memorial notes and 5 on the fallen officers.
 * 3. A Public Information Officer role ("the station has had spokesmen for a century"), Wikidata Q3106887, "public information
 *    officer", a subclass of spokesperson (Q17221), checked 5 October 2026.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/source_fault_mentry_birth_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $es = Craft::$app->getEntries(); $n = 0;
$m = Entry::find()->id(18648)->status(null)->one(); $cert = Entry::find()->id(20102)->status(null)->one();
if (!$m || !$cert || $cert->title !== "Charles Alexander Mentry's Death Certificate") { throw new \RuntimeException('Mentry or his certificate not found'); }

/* 1. The source fault */
$have = Entry::find()->section('sourceFaults')->status(null)->relatedTo(['targetElement' => $m, 'field' => 'faultRecord'])->all();
$have = array_filter($have, fn($f) => (string)$f->faultField === 'birthDate');
echo 'Mentry source fault: ' . ($have ? 'exists' : 'create') . PHP_EOL;
if ($APPLY && !$have) {
  $sec = $es->getSectionByHandle('sourceFaults'); $f = new Entry(); $f->sectionId = $sec->id; $f->setTypeId($sec->getEntryTypes()[0]->id);
  $f->title = '27 March 1847 → 1847 or 1848';
  $f->setFieldValues(['asPrinted' => 'born 27 March 1847; aged 52 years, 6 months and 8 days at death on 4 October 1900',
    'reading' => '27 March 1847 or 1848: the year is not decided',
    'basis' => 'The death certificate gives this date and an age at death that counts back to 27 March 1848.',
    'decidedBy' => 'source_fault_mentry_birth_2026_10_05.php, 5 October 2026', 'faultRecord' => [$m->id, $cert->id], 'faultField' => 'birthDate',
    'footnotes' => [['number' => '1', 'note' => 'Archive record: "' . $cert->title . '" (#' . $cert->id . '), as the original page on SCVHistory.com quotes it; the scan has not yet been read against the quotation.', 'source' => 'editorial-2026']]]);
  if (!$el->saveElement($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++;
}
$HELD = ' Held as 1847?-03-27, uncertain in the year alone, until the sourceFault type exists to carry it.';
$rows = array_values(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($m->editorNotes ?? [])));
$hit = false; foreach ($rows as &$r) { if (str_contains($r['note'], $HELD)) { $r['note'] = trim(str_replace($HELD, '', $r['note'])); $hit = true; } } unset($r);
echo 'Mentry note: ' . ($hit ? 'drop the "held until" sentence' : 'done already') . PHP_EOL;
if ($APPLY && $hit) { $m->setFieldValue('editorNotes', $rows); if (!$el->saveElement($m)) { throw new \RuntimeException('#18648'); } $n++; }

/* 2. "the legacy page" */
$k = 0;
foreach (Entry::find()->section(['warMemorials', 'fallenOfficers'])->status(null)->all() as $e) {
  $rows = array_values(array_map(fn($r) => ['heading' => (string)$r['heading'], 'note' => (string)$r['note'], 'position' => (string)$r['position'] ?: 'bottom'], iterator_to_array($e->editorNotes ?? [])));
  $ch = false; foreach ($rows as &$r) { $new = str_replace(['the legacy page', 'The legacy page'], ['the original page on SCVHistory.com', 'The original page on SCVHistory.com'], $r['note']); if ($new !== $r['note']) { $r['note'] = $new; $ch = true; $k++; } } unset($r);
  if (!$ch) { continue; }
  if (!$APPLY) { continue; } $e->setFieldValue('editorNotes', $rows); if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id}"); } $n++;
}
echo "notes saying the legacy page: $k" . PHP_EOL;

/* 3. The role */
$role = Entry::find()->section('roles')->status(null)->title('Public Information Officer')->one();
echo 'role: ' . ($role ? "exists #{$role->id}" : 'create') . PHP_EOL;
if ($APPLY && !$role) {
  $sec = $es->getSectionByHandle('roles'); $role = new Entry(); $role->sectionId = $sec->id; $role->setTypeId($sec->getEntryTypes()[0]->id);
  $role->title = 'Public Information Officer'; $role->slug = 'public-information-officer';
  $role->setFieldValues(['roleWikidataId' => 'Q3106887', 'roleMatch' => 'exact']);
  if (!$el->saveElement($role)) { throw new \RuntimeException(json_encode($role->getFirstErrors())); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('source_fault_mentry_birth_2026_10_05.php', $n, 'verified', 'Mentry birth source fault; "the original page on SCVHistory.com"; Public Information Officer role'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
