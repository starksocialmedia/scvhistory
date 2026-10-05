/**
 * Person records for the trustees of the Santa Clarita Community College District who had none (Nathan, 5 October 2026:
 * "create records for all forty. Same rule as the school boards. Holding elected office qualifies"). The roster is
 * inventory/review/coc-trustees-roster-2026-10-05.json: 35 trustees from 1967, the district's board history and the
 * archive's own sources (Don Allen, whom the district's list omits). Carl Boyer (#15808) and Scott Wilk (#335) exist and
 * are not touched. Sharlene Rose Johnson is made as her own record: that she may be the "Sharlene (Rose) Duzick" of two
 * Saugus Union candidacies rests on one unread headline, which stays a lead.
 * Each record: the name, its variants as aliases, provenance. Terms (create_coc_trustee_terms) and profiles (drafted and read
 * by Nathan first) follow. Idempotent: matched by title. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_coc_trustees_2026_10_05.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$T = json_decode(file_get_contents("$root/inventory/review/coc-trustees-roster-2026-10-05.json"), true)['trustees'];
$sec = Craft::$app->getEntries()->getSectionByHandle('persons'); $n = 0; $made = [];
foreach ($T as $t) {
  if ($t['match'] === 'SAME') { echo "exists #{$t['personId']}: {$t['name']}\n"; continue; }
  $name = trim($t['name']);
  $e = Entry::find()->section('persons')->title($name)->status(null)->one();
  if (!$e) { foreach ($t['variants'] ?? [] as $v) { $e = $e ?: Entry::find()->section('persons')->title($v)->status(null)->one(); } }
  echo ($e ? "exists #{$e->id}" : 'create') . ": $name" . ($t['match'] === 'POSSIBLE' ? ' (possible match noted, not linked)' : '') . PHP_EOL;
  if (!$APPLY || $e) { continue; }
  $e = new Entry(); $e->sectionId = $sec->id; $e->setTypeId($sec->getEntryTypes()[0]->id); $e->title = $name;
  $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
  $v = ['personAliases' => implode("\n", array_diff($t['variants'] ?? [], [$name])), 'recordProvenance' => 'create_coc_trustees_2026_10_05.php, 5 October 2026: a trustee of the Santa Clarita Community College District'];
  $e->setFieldValues(array_intersect_key($v, array_flip($h)));
  if (!$el->saveElement($e)) { throw new \RuntimeException("$name " . json_encode($e->getFirstErrors())); } $n++; $made[$name] = $e->id;
}
if ($APPLY) {
  file_put_contents("$root/inventory/review/coc-trustees-ids-2026-10-05.json", json_encode($made, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
  $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_coc_trustees_2026_10_05.php', $n, 'verified', "college district trustees: $n person records");
}
echo ($APPLY ? "done: $n (ids in inventory/review/coc-trustees-ids-2026-10-05.json)" : 'nothing written') . PHP_EOL;
