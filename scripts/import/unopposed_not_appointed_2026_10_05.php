/**
 * Elected unopposed, not appointed (Nathan, 5 October 2026: "the wording fix: 'elected unopposed; no election was held' in place
 * of appointed, everywhere a candidate ran unopposed. Education Code 5328 seats the nominee automatically, so nobody appoints
 * them. Keep 'appointed' only where someone genuinely filled a vacancy"). From inventory/review/appointed-terms-audit-2026-10-05.md:
 * 26 school and college terms recorded as appointed sole candidates were seated without an election, and Talley's 2020 term,
 * recorded as elected, was the same (the County's 2020 cancelled list; SCVNews, 2 October 2020).
 * Education Code 5328, read on leginfo.legislature.ca.gov on 5 October 2026: "If pursuant to Section 5326 a district election is
 * not held, the qualified person or persons nominated shall be seated at the organizational meeting of the board, or if no
 * person has been nominated or if an insufficient number is nominated, the governing board shall appoint ..." The notes quoted
 * it with "..." joining the nominee's clause to the appointee's ("as if elected"); the quotation is corrected.
 * - selectionMethod gains "unopposed" (Elected unopposed; no election held), set on the 27. "sole-candidate" stays for the two
 *   terms where the law does appoint in lieu of an election: Gibbs 2024 (the City, Elections Code 10229) and Plambeck 2011
 *   (the water agency, Elections Code 10515). Vacancy appointments (Miranda 2017, Trunkey 2014, Boydston 2006, Aliano 1994 and
 *   the others the audit found standing) are untouched.
 * - Our wording in footnotes and editor's notes: "Appointed as the sole candidate; no election held." and "Appointed without an
 *   election; ..." become "Elected unopposed; no election was held." The County's own list titles, quoted, stay as printed.
 * Writes project config (the option). Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/unopposed_not_appointed_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$IDS = [29627,29008,29635,29637,28887,28885,29632,28881,29651,29648,29640,30407,30363,30367,30389,30391,30395,30397,29654,28889,28489,29664,28883,29024,29026,29645,28580];
$REP = [
  'Appointed without an election; the candidates did not outnumber the seats.' => 'Elected unopposed; no election was held, the candidates not outnumbering the seats.',
  'Appointed as the sole candidate; no election held.' => 'Elected unopposed; no election was held.',
  'again: appointed as the sole candidate; no election held.' => 'again: elected unopposed; no election was held.',
  'Again: appointed as the sole candidate; no election held.' => 'Again: elected unopposed; no election was held.',
  'when the election is not held the nominee "shall be seated at the organizational meeting of the board ... as if elected at a district election."' => 'when the election is not held "the qualified person or persons nominated shall be seated at the organizational meeting of the board"; the board appoints only when no one, or too few, were nominated.',
];
$TALLEY = 'Elected unopposed; no election was held. The County lists "Newhall Elementary (Trustee Area 4 and 5)" among its cancelled elections of November 3, 2020 (County of Los Angeles, Registrar-Recorder/County Clerk, list of cancelled elections, November 3, 2020), and SCVNews reported on October 2, 2020 that "Isaiah Talley and Sue Solomon were running unopposed." The district\'s own page calls it a re-election.';
$fs = Craft::$app->getFields(); $f = $fs->getFieldByHandle('selectionMethod'); $vals = array_column($f->options, 'value');
echo 'option "unopposed": ' . (in_array('unopposed', $vals, true) ? 'exists' : 'add') . PHP_EOL;
if ($APPLY && !in_array('unopposed', $vals, true)) { $o = $f->options; $o[] = ['label' => 'Elected unopposed (no election held)', 'value' => 'unopposed', 'default' => false]; $f->options = $o; if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getErrors())); } }
$n = 0; $texts = 0;
foreach ($IDS as $id) {
  $h = Entry::find()->section('officeHoldings')->id($id)->status(null)->one(); if (!$h) { echo "no #$id\n"; continue; }
  $who = $h->holdingPerson->status(null)->one()?->title; $m = (string)$h->selectionMethod->value; $vals2 = [];
  foreach (['footnotes' => ['number', 'note', 'source'], 'editorNotes' => ['heading', 'note', 'position']] as $fh => $keys) {
    $rows = array_map(fn($r) => array_combine($keys, array_map(fn($k) => (string)($r[$k] ?? ''), $keys)), iterator_to_array($h->getFieldValue($fh) ?? []));
    $ch = false; foreach ($rows as &$r) { $new = strtr($r['note'], $REP); if ($new !== $r['note']) { $r['note'] = $new; $ch = true; $texts++; } } unset($r);
    if ($ch) { $vals2[$fh] = array_values(array_filter($rows, fn($r) => $r['note'] !== '')); }
  }
  if ($id === 29008 && !str_contains(json_encode(iterator_to_array($h->footnotes), JSON_UNESCAPED_UNICODE), 'running unopposed')) {
    $rows = array_values(array_filter(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)$r['source']], iterator_to_array($h->footnotes)), fn($r) => $r['note'] !== ''));
    $rows[] = ['number' => (string)(count($rows) + 1), 'note' => $TALLEY, 'source' => 'editorial-2026']; $vals2['footnotes'] = $rows; $texts++;
  }
  $setM = $m !== 'unopposed';
  echo "#$id $who: " . ($setM ? "$m -> unopposed" : 'unopposed already') . (count($vals2) ? ', wording in ' . implode(' and ', array_keys($vals2)) : '') . PHP_EOL;
  if (!$APPLY || (!$setM && !$vals2)) { continue; }
  if ($setM) { $vals2['selectionMethod'] = 'unopposed'; }
  $h->setFieldValues($vals2); if (!Craft::$app->getElements()->saveElement($h)) { throw new \RuntimeException("#$id " . json_encode($h->getFirstErrors())); } $n++;
}
echo count($IDS) . " terms; $texts notes reworded\n";
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('unopposed_not_appointed_2026_10_05.php', $n, 'verified', 'elected unopposed, not appointed: ' . $n . ' terms'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
