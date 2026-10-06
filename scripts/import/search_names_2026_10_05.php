/**
 * Search names, and the maiden names back (Nathan, 5 October 2026: "Confirm the 181 removed aliases included none that was the only
 * way a reader would find that person: maiden names, names they published under, names a source uses that the title does not").
 * They did. After remove_same_name_aliases_2026_10_05.php the site search finds nobody for "A.B. Perkins" (his byline), "D.G.
 * Scofield", "W.W. Jenkins", "Bill Hart", "Joseph Messina", "Ruth Waldo Newhall" or "Judy Egan Umeck" (the County's printing):
 * the search matches every word, and a word that is in neither the title nor an alias matches nothing.
 * - personSearchNames, a new plain-text field on the person type, searchable and not shown on the page (classed internal): the
 *   same-name forms come out of the header, as Nathan ruled, and stay findable. Filled from
 *   inventory/review/aliases-same-name-2026-10-05.json, the lines the removal took, less two that would make a search land on
 *   the wrong man: "Francisco Lopez" on Chico López (#28132; the gold discoverer, #18834, has that name) and the bare
 *   "Thomas M. Frew" (#28647; it names Tom Frew II and Tom Frew IV as well).
 * - Four removed lines carry a maiden name, which Nathan names as a true alias, and go back to personAliases, shown:
 *   Joan Whaling MacGregor, Judy Egan Umeck, Linda Hovis Storli, Ruth Waldo Newhall.
 * Writes project config (the field). Idempotent. Dry run by default. Set $APPLY = true; then
 * `ddev craft resave/entries --section=persons --update-search-index`.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/search_names_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $n = 0;
$L = json_decode(file_get_contents(\Craft::getAlias('@root') . '/inventory/review/aliases-same-name-2026-10-05.json'), true);
$MAIDEN = ['Joan Whaling MacGregor', 'Judy Egan Umeck', 'Linda Hovis Storli', 'Ruth Waldo Newhall'];
$TRAP = [[28132, 'Francisco Lopez'], [28647, 'Thomas M. Frew']];
$f = $fs->getFieldByHandle('personSearchNames'); echo 'personSearchNames: ' . ($f ? 'exists' : 'create (plain text, searchable, internal)') . PHP_EOL;
if ($APPLY && !$f) {
  $f = new \craft\fields\PlainText(['name' => 'Search names', 'handle' => 'personSearchNames', 'multiline' => true, 'initialRows' => 3, 'searchable' => true,
    'instructions' => 'Not shown on the page. Forms of the person\'s own name that the title does not carry (a full middle name, initials, a short form, a byline) so the site search finds them. A different name a reader would know them by (a maiden or married name, a stage name) is an alias, shown, not a search name.']);
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getErrors())); } $n++;
}
foreach ($es->getSectionByHandle('persons')->getEntryTypes() as $et) {
  $layout = $et->getFieldLayout(); $have = array_map(fn($x) => $x->handle, $layout->getCustomFields());
  if (in_array('personSearchNames', $have, true)) { continue; } echo "{$et->handle}: add after personAliases\n"; if (!$APPLY) { continue; }
  $tabs = $layout->getTabs(); $done = false;
  foreach ($tabs as $tab) { $els = []; foreach ($tab->getElements() as $x) { $els[] = $x; if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'personAliases') { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle('personSearchNames')); $done = true; } } $tab->setElements($els); }
  if (!$done) { throw new \RuntimeException('no personAliases'); } $layout->setTabs($tabs); $et->setFieldLayout($layout); if (!$es->saveEntryType($et)) { throw new \RuntimeException(json_encode($et->getErrors())); } $n++;
}
$by = []; foreach ($L as [$pid, $t, $a]) { if (in_array([$pid, $a], $TRAP, true)) { echo "  left out (a trap): #$pid $a\n"; continue; } $by[$pid][] = $a; }
$sn = 0; $mn = 0;
foreach ($by as $pid => $names) {
  $e = Entry::find()->id($pid)->status(null)->one(); if (!$e) { echo "no #$pid\n"; continue; }
  $maid = array_values(array_intersect($names, $MAIDEN)); $search = array_values(array_diff($names, $MAIDEN));
  $curS = $APPLY || $f ? trim((string)($e->getFieldLayout()->getFieldByHandle('personSearchNames') ? $e->personSearchNames : '')) : '';
  $curA = trim((string)$e->personAliases);
  $addS = array_values(array_filter($search, fn($x) => !in_array($x, preg_split('~\R~', $curS), true)));
  $addA = array_values(array_filter($maid, fn($x) => !in_array($x, preg_split('~\R~', $curA), true)));
  if (!$addS && !$addA) { continue; }
  $sn += count($addS); $mn += count($addA);
  echo "#$pid {$e->title}: " . ($addA ? 'alias back: ' . implode(' | ', $addA) . '; ' : '') . ($addS ? 'search: ' . implode(' | ', $addS) : '') . PHP_EOL;
  if (!$APPLY) { continue; }
  $v = []; if ($addS) { $v['personSearchNames'] = trim($curS . "\n" . implode("\n", $addS)); } if ($addA) { $v['personAliases'] = trim($curA . "\n" . implode("\n", $addA)); }
  $e->setFieldValues($v); if (!$el->saveElement($e)) { throw new \RuntimeException("#$pid"); } $n++;
}
echo "$sn search names, $mn aliases restored\n";
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('search_names_2026_10_05.php', $n, 'verified', 'personSearchNames; four maiden names back as aliases'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
