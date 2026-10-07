/**
 * What each author link rests on (Nathan, 7 October 2026: "add a basis to authorship: printed byline / series attribution /
 * closing tagline / derived, with a note saying from what. Set it on all 320, not just the 58, so a reader can tell what each
 * link rests on." And: "A series heading is not a byline. Reynolds almost certainly wrote all 50, but what the archive knows is
 * that his series carried them").
 *
 * Two fields beside writtenBy on every entry type that has it (article, obituary, collection, document):
 *   authorshipBasis      dropdown: printed-byline, series-attribution, closing-tagline, derived
 *   authorshipBasisNote  plain text: from what, quoting the words the link was read from and where
 * Per record, not per link: every record carrying a basis has exactly one writtenBy (checked below; a record with two authors
 * resting on different grounds would need a table instead, and the script refuses it).
 *
 * Second pass, the same day (Nathan: "Yes to the same pass on the other 410"): every older writtenBy link, from
 * inventory/review/authorship-basis-older-2026-10-07.json (build_authorship_basis_older_2026_10_07.py): 426 links, 409 of them
 * articles. Six are derived, and say from what.
 * Reads inventory/review/authorship-basis-2026-10-07.json (built by scripts/import/build_authorship_basis_2026_10_07.py from the
 * 5 October link census, the legacy pages on Reggie and the Internet Archive captures). Each row names the entry, the person
 * linked, the basis and the note. Refuses a row whose writtenBy is not that one person. Never overwrites a basis already set
 * differently; reports it.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/authorship_basis_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $n = 0;
$OPTIONS = [
  ['label' => 'Printed byline', 'value' => 'printed-byline', 'default' => false],
  ['label' => 'Series attribution', 'value' => 'series-attribution', 'default' => false],
  ['label' => 'Closing tagline', 'value' => 'closing-tagline', 'default' => false],
  ['label' => 'Derived', 'value' => 'derived', 'default' => false],
];
$DEF = [
  'authorshipBasis' => fn() => new \craft\fields\Dropdown(['handle' => 'authorshipBasis', 'name' => 'Authorship basis',
    'instructions' => 'What the Written By link rests on. Printed byline: the piece prints the name as its author, above it. Series attribution: the series heading names the author; the piece itself carries no byline. Closing tagline: the name appears only in a line at the end. Derived: inferred from something else, said in the note. Empty means nobody has recorded it.',
    'options' => array_merge([['label' => '', 'value' => '', 'default' => true]], $OPTIONS)]),
  'authorshipBasisNote' => fn() => new \craft\fields\PlainText(['handle' => 'authorshipBasisNote', 'name' => 'Authorship basis note',
    'instructions' => 'From what: the words the author link was read from, quoted, and where they are printed.', 'multiline' => true, 'initialRows' => 2]),
];
$F = [];
foreach ($DEF as $h => $make) {
  $f = $fs->getFieldByHandle($h); echo "field $h: " . ($f ? 'exists' : 'create') . PHP_EOL;
  if ($APPLY && !$f) { $f = $make(); if (!$fs->saveField($f)) { throw new \RuntimeException("$h " . json_encode($f->getFirstErrors())); } $n++; }
  $F[$h] = $f;
}
foreach ($es->getAllEntryTypes() as $t) {
  $layout = $t->getFieldLayout(); $handles = array_map(fn($x) => $x->handle, $layout->getCustomFields());
  if (!in_array('writtenBy', $handles, true)) { continue; }
  $missing = array_values(array_diff(array_keys($DEF), $handles));
  echo "type {$t->handle}: " . ($missing ? 'add ' . implode(', ', $missing) . ' after writtenBy' : 'has both') . PHP_EOL;
  if (!$APPLY || !$missing) { continue; }
  foreach ($layout->getTabs() as $tab) { $els = []; $hit = false;
    foreach ($tab->getElements() as $x) { $els[] = $x;
      if ($x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'writtenBy') { $hit = true; foreach ($missing as $h) { $els[] = new \craft\fieldlayoutelements\CustomField($F[$h]); } } }
    if ($hit) { $tab->setElements($els); }
  }
  $layout->setTabs($layout->getTabs()); $t->setFieldLayout($layout);
  if (!$es->saveEntryType($t)) { throw new \RuntimeException($t->handle . ' ' . json_encode($t->getFirstErrors())); } $n++;
}
$rows = [];
foreach (['authorship-basis-2026-10-07.json', 'authorship-basis-older-2026-10-07.json'] as $file) {
  $part = json_decode((string)file_get_contents(\Craft::getAlias('@root') . "/inventory/review/$file"), true)['rows'] ?? null;
  if (!$part) { throw new \RuntimeException("$file missing or unreadable"); }
  $rows = array_merge($rows, $part);
}
$valid = array_column($OPTIONS, 'value'); $count = ['set' => 0, 'done' => 0, 'differs' => 0, 'refused' => 0]; $by = [];
foreach ($rows as $r) {
  $id = (int)$r['entryId']; $tag = str_pad("#$id", 8) . mb_substr($r['title'], 0, 44);
  if (!in_array($r['basis'], $valid, true) || trim($r['note']) === '') { echo "$tag  REFUSED: bad basis or empty note" . PHP_EOL; $count['refused']++; continue; }
  $e = Entry::find()->id($id)->status(null)->one();
  if (!$e) { echo "$tag  REFUSED: not found" . PHP_EOL; $count['refused']++; continue; }
  $w = $e->getFieldValue('writtenBy')->status(null)->ids();
  if ($w !== [(int)$r['personId']]) { echo "$tag  REFUSED: writtenBy is [" . implode(',', $w) . "], the row says #{$r['personId']}" . PHP_EOL; $count['refused']++; continue; }
  $by[$r['basis']] = ($by[$r['basis']] ?? 0) + 1;
  if ($F['authorshipBasis'] && in_array('authorshipBasis', array_map(fn($x) => $x->handle, $e->getFieldLayout()->getCustomFields()), true)) {
    $cur = (string)$e->getFieldValue('authorshipBasis')->value; $curN = trim((string)$e->getFieldValue('authorshipBasisNote'));
    if ($cur === $r['basis'] && $curN === trim($r['note'])) { $count['done']++; continue; }
    if ($cur !== '' && ($cur !== $r['basis'] || $curN !== '')) { echo "$tag  DIFFERS: has $cur \"" . mb_substr($curN, 0, 60) . "\"; the row says {$r['basis']}. Left alone." . PHP_EOL; $count['differs']++; continue; }
  }
  $count['set']++;
  if ($APPLY) { $e->setFieldValue('authorshipBasis', $r['basis']); $e->setFieldValue('authorshipBasisNote', $r['note']);
    if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
echo 'rows ' . count($rows) . ': ' . json_encode($count) . PHP_EOL . 'by basis: ' . json_encode($by) . PHP_EOL;
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('authorship_basis_2026_10_07.php', $n, 'verified', 'authorshipBasis and its note: the 320 author links of 5 October and the 426 older ones'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
