/**
 * Cause and consequence on events (Nathan, 7 October 2026: "add the cause-and-consequence field to the model: the Dam to its flood,
 * Sylmar and Northridge each to the interchange, Northridge to the Greenbrier fires, the 1938 flood to the Saugus derailment. That
 * relationship is real and the sources make it"). From the disaster figures audit (inventory/review/disaster-figures-audit-2026-10-07.md).
 * A table, eventConsequences, on the event type: what followed (in words), the record it is (an id, when the archive holds one: a
 * place, a photograph, an event), and the source that makes the link, quoted. A table and not a relation, because most consequences
 * are not records (the Dam's flood is part of the Dam's own record; the Greenbrier fires have none). The event page shows the rows in a
 * "What followed" box (templates/events/_entry.twig).
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/event_consequences_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $n = 0;
$f = $fs->getFieldByHandle('eventConsequences');
echo 'field eventConsequences: ' . ($f ? 'exists' : 'create (table: what followed, record, source)') . PHP_EOL;
if ($APPLY && !$f) {
  $f = new \craft\fields\Table(['handle' => 'eventConsequences', 'name' => 'What followed', 'instructions' => 'A consequence the sources tie to this event: in words, the record it is (an entry id) when the archive holds one, and the source that makes the link, quoted.',
    'columns' => ['col1' => ['heading' => 'What followed', 'handle' => 'consequence', 'type' => 'multiline'], 'col2' => ['heading' => 'Record id', 'handle' => 'record', 'type' => 'singleline'], 'col3' => ['heading' => 'Source', 'handle' => 'source', 'type' => 'multiline']], 'defaults' => []]);
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++;
}
$t = $es->getEntryTypeByHandle('event') ?? $es->getSectionByHandle('events')->getEntryTypes()[0]; $layout = $t->getFieldLayout();
$has = in_array('eventConsequences', array_map(fn($x) => $x->handle, $layout->getCustomFields()), true);
echo 'event layout: ' . ($has ? 'has it' : 'add after relatedEvents') . PHP_EOL;
if ($APPLY && $f && !$has) { $tab = $layout->getTabs()[0]; $els = []; $done = false;
  foreach ($tab->getElements() as $x) { $els[] = $x; if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'relatedEvents') { $els[] = new \craft\fieldlayoutelements\CustomField($f); $done = true; } }
  if (!$done) { $els[] = new \craft\fieldlayoutelements\CustomField($f); } $tab->setElements($els); $layout->setTabs($layout->getTabs()); $t->setFieldLayout($layout);
  if (!$es->saveEntryType($t)) { throw new \RuntimeException(json_encode($t->getFirstErrors())); } $n++; }
$ROWS = [
  31342 => [['The flood: the water ran down San Francisquito Canyon to the Santa Clara River and west to the sea.', '', 'Caption to AL3024a, "Dam Under Construction" (photograph, collection of Alan Pollack), as carried on SCVHistory.com, /scvhistory/al3024a.htm: "At 11:57:30 on the night of March 12, 1928, half of the dam suddenly collapsed." "Floodwaters met the Santa Clara River at Castaic Junction and headed west toward the Pacific Ocean."']],
  31893 => [['The Newhall Pass interchange collapsed: the westbound Interstate 210 overpass fell onto Interstate 5.', '934', 'Caption to LW3158, "Collapsed 210 Freeway Bridge in Newhall Pass, 2-9-1971" (photograph #4973 in this archive), /scvhistory/lw3158.htm, dated February 9, 1971: "The westbound Interstate 210 overpass has fallen onto Interstate 5 in the Newhall Pass. UPI Telephoto."']],
  875 => [['The Newhall Pass interchange fell again: freeway bridges at the same place were destroyed.', '934', 'Caption to LW2749, "I-5 Freeway Overpass" (photograph #4457 in this archive), /scvhistory/lw2749.htm, dated January 20, 1994: "Interstate 5 Freeway overpass as seen from The Old Road in the Newhall Pass. Destroyed in the Northridge earthquake of Jan. 17, 1994."'],
          ['Fires in the Greenbrier mobile home park in western Canyon Country: mobile homes burned when the earthquake ruptured gas lines.', '5753', '"Greenbrier Mobile Home Park Damage," 1994 Northridge earthquake, photograph #5753 in this archive (note 5 of this record).']],
  31904 => [['A Southern Pacific locomotive returning from flood repairs derailed near Saugus on March 25, 1938.', '4871', 'Caption to LW3067, "Southern Pacific Locomotive Derailed, Overturned 3-25-1938" (photograph #4871 in this archive), /scvhistory/lw3067.htm: "Collateral damage from the Great Flood of March 2, 1938: A Southern Pacific locomotive rests on its side after it hit an open switch outside of Saugus."']],
];
foreach ($ROWS as $id => $rows) {
  $e = Entry::find()->id($id)->status(null)->one(); foreach ($rows as $r) { if ($r[1] !== '' && !Entry::find()->id((int)$r[1])->status(null)->exists()) { throw new \RuntimeException("record #{$r[1]} missing"); } }
  $cur = $f && $has ? array_values(array_filter(iterator_to_array($e->getFieldValue('eventConsequences') ?? []), fn($x) => trim((string)($x['consequence'] ?? '')) !== '')) : [];
  echo "#$id {$e->title}: " . ($cur ? count($cur) . ' rows already' : count($rows) . ' rows: ' . implode(' | ', array_map(fn($r) => mb_substr($r[0], 0, 70) . ($r[1] ? " (#{$r[1]})" : ''), $rows))) . PHP_EOL;
  if ($APPLY && !$cur) { $e = Entry::find()->id($id)->status(null)->one(); $e->setFieldValue('eventConsequences', array_map(fn($r) => ['consequence' => $r[0], 'record' => $r[1], 'source' => $r[2]], $rows)); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('event_consequences_2026_10_07.php', $n, 'verified', 'eventConsequences on events; five links the sources make'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
