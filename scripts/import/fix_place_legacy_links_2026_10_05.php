/**
 * Silent-faults audit finding 7 (inventory/review/silent-faults-audit-2026-10-05.md). Seven places carry, as their
 * "original page" (legacyUrl, legacyKey, sourcePath), a photograph page that merely sits in the place's category:
 * Rancho Camulos links to lw3903, a liquor tax certificate. Nathan: "the kind of thing a reader notices and loses
 * trust over."
 *
 * WHERE THE VALUES CAME FROM. Not canonical_entities.json: extract_entities.py copies legacyUrl into that file from
 * the Craft export, so it is downstream of the fault, and no script reads it to write Craft. The source is
 * places-candidates.json, built by build_hub_candidates.py:190-204, which took the first object page whose title
 * category maps to a place ("SCVHistory.com LW3903 | Rancho Camulos | ...") as that place's own page;
 * import_places_and_series.php copied it when it created the places. That importer skips a place that already
 * exists, and backfill_provenance.php fills only empty fields and has no evidence for any of the seven (no WordPress
 * post with their slugs, no entity_index entry with a legacy page), so nothing re-writes the values once corrected.
 *
 * THE RULE. A page counts only if its title and content are about that place. The evidence is
 * inventory/review/legacy-evidence-2026-10-05.json (extract_legacy_evidence_2026_10_05.py), read from the mirror:
 * each current page's title and the breadcrumb Leon put on it, and each candidate's title. Where Leon's breadcrumb
 * for the place leads to a page titled for the place, that page is proposed. Where it leads to a page about
 * somewhere else (Valencia, Saugus) or to a section of another place's page (ridge.htm#forttejon), the three fields
 * are cleared.
 *
 * Written: legacyUrl, legacyKey and sourcePath, in the form these records already use ("/scvhistory/x.htm", "x",
 * "scvhistory.com/scvhistory/x.htm"). legacyCategory, Leon's category name, is right and is left alone.
 * Refuses: a proposed page whose title in the evidence does not name the place, a proposed URL another record
 * already holds, an em dash. Idempotent: a place already at its target is skipped and not saved.
 * Writes inventory/review/place-legacy-links-dry-run-2026-10-05.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_place_legacy_links_2026_10_05.php'))"
 */
use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$root = \Craft::getAlias('@root');
$evPath = "$root/inventory/review/legacy-evidence-2026-10-05.json";
if (!file_exists($evPath)) { throw new \RuntimeException('run scripts/import/extract_legacy_evidence_2026_10_05.py first'); }
$EV = json_decode(file_get_contents($evPath), true)['places'] ?? [];

/* id => [current key, proposed key or null, the words the proposed title must contain, why] */
$PLAN = [
  631 => ['lw3903', 'camulos', 'Rancho Camulos', 'Leon\'s breadcrumb on lw3903 ("> RANCHO CAMULOS MUSEUM") leads to camulos.htm, his Rancho Camulos page: visiting, tours, modern photos and videos of the rancho.'],
  635 => ['lw3795', 'ridge', 'Ridge Route', 'The breadcrumb ("> RIDGE ROUTE") leads to ridge.htm, his Ridge Route page: articles and photographs of the road.'],
  605 => ['lw3789', 'heritage', 'Heritage Junction', 'The breadcrumb ("> HERITAGE JUNCTION") leads to heritage.htm, his page for the park, now the Santa Clarita History Center: site map, museum items, photo galleries.'],
  659 => ['lw3730', 'aguadulce', 'Vasquez Rocks', 'The breadcrumb ("> VASQUEZ ROCKS") leads to aguadulce.htm, titled "Agua Dulce & Vasquez Rocks", which carries the Vasquez Rocks material (the CA-LAN-361 study, Sarah Brewer\'s Vasquez Rocks history). It is a shared page for Agua Dulce and Vasquez Rocks; no Agua Dulce record exists to share it.'],
  613 => ['lw3790', null, null, 'The breadcrumb ("> MAGIC MOUNTAIN") leads to valencia.htm, Leon\'s Valencia page, which is about Valencia. No page in the mirror is titled for the park as a whole; the titled pages (colossus060314.htm, sfmm_goldrusher2014.htm and others) are single pieces about a ride or an event.'],
  597 => ['lw3698', null, null, 'The breadcrumb ("> FORT TEJON") leads to ridge.htm#forttejon, a section of the Ridge Route page. The pages titled for Fort Tejon are single pieces (cullimore_oldadobes.htm, Clarence Cullimore\'s 1949 book; forttejonparkinventory2015.htm, an inventory of the park\'s collection; films and articles), not Leon\'s page for the fort.'],
  643 => ['lw3271', null, null, 'The breadcrumb ("> SAUGUS SPEEDWAY") leads to saugus.htm#speedway, a section of the Saugus page. speedwaychronology.htm, "Saugus Speedway Chronology", is a reprint of a 1982 souvenir booklet\'s chronology, a source document rather than a page about the speedway; the other titled pages are single films, programs and articles.'],
];

$has = function ($el, string $h): bool { foreach ($el->getFieldLayout()?->getCustomFields() ?? [] as $f) { if ($f->handle === $h) { return true; } } return false; };
$plan = []; $bad = []; $done = [];
foreach ($PLAN as $id => [$curKey, $newKey, $must, $why]) {
  $e = Entry::find()->id($id)->section('places')->status(null)->one();
  if (!$e) { $bad[] = "#$id: no place"; continue; }
  foreach (['legacyUrl', 'legacyKey', 'sourcePath'] as $h) { if (!$has($e, $h)) { $bad[] = "#$id: no field $h"; continue 2; } }
  $cur = ['legacyUrl' => (string)$e->getFieldValue('legacyUrl'), 'legacyKey' => (string)$e->getFieldValue('legacyKey'), 'sourcePath' => (string)$e->getFieldValue('sourcePath')];
  $new = $newKey === null ? ['legacyUrl' => '', 'legacyKey' => '', 'sourcePath' => '']
       : ['legacyUrl' => "/scvhistory/$newKey.htm", 'legacyKey' => $newKey, 'sourcePath' => "scvhistory.com/scvhistory/$newKey.htm"];
  if ($cur === $new) { $done[] = "#$id {$e->title}"; continue; }
  if ($cur['legacyKey'] !== $curKey) { $bad[] = "#$id {$e->title}: holds {$cur['legacyKey']}, expected $curKey; changed since the audit, not touched"; continue; }
  $curEv = $EV[$curKey] ?? null; $newEv = $newKey ? ($EV[$newKey] ?? null) : null;
  if (!$curEv) { $bad[] = "#$id: no evidence for $curKey"; continue; }
  if ($newKey !== null) {
    if (!$newEv) { $bad[] = "#$id: no evidence for $newKey"; continue; }
    if (stripos($newEv['title'], $must) === false) { $bad[] = "#$id: $newKey title \"{$newEv['title']}\" does not name $must"; continue; }
    $other = Entry::find()->status(null)->legacyUrl($new['legacyUrl'])->id(['not', $id])->ids();
    if ($other) { $bad[] = "#$id: {$new['legacyUrl']} already held by #" . implode(', #', $other); continue; }
  }
  if (preg_match('~[\x{2013}\x{2014}]~u', implode(' ', $new))) { $bad[] = "#$id: em dash"; continue; }
  $plan[] = compact('e', 'cur', 'new', 'curEv', 'newEv', 'why', 'curKey', 'newKey');
}

foreach ($plan as $p) {
  echo "#{$p['e']->id} {$p['e']->title}" . PHP_EOL;
  echo "   now:  {$p['cur']['legacyUrl']}  \"{$p['curEv']['title']}\"" . PHP_EOL;
  echo '   new:  ' . ($p['newKey'] ? "{$p['new']['legacyUrl']}  \"{$p['newEv']['title']}\"" : 'cleared') . PHP_EOL;
}
foreach ($done as $d) { echo "already right: $d" . PHP_EOL; }
foreach ($bad as $b) { echo "REFUSED $b" . PHP_EOL; }

$o = [];
$o[] = '# Place legacy links, dry run, 5 October 2026';
$o[] = '';
$o[] = 'Generated by `scripts/import/fix_place_legacy_links_2026_10_05.php` (' . ($APPLY ? 'APPLIED' : 'DRY RUN, nothing written') . '). Silent-faults audit finding 7.';
$o[] = '';
$o[] = 'The rule: a page counts only if its title and content are about the place. Titles and breadcrumbs were read from the mirror into `inventory/review/legacy-evidence-2026-10-05.json`. The three fields (legacyUrl, legacyKey, sourcePath) move together; legacyCategory is left as it is.';
$o[] = '';
$o[] = 'Proposed: ' . count(array_filter($plan, fn($p) => $p['newKey'])) . '. Cleared: ' . count(array_filter($plan, fn($p) => !$p['newKey'])) . '. Already right: ' . count($done) . '. Refused: ' . count($bad) . '.';
$o[] = '';
$o[] = '| Place | Current link (page title) | Proposed | Why |';
$o[] = '|---|---|---|---|';
$esc = fn($t) => str_replace('|', '\\|', $t);
foreach ($plan as $p) {
  $p['curEv']['title'] = $esc($p['curEv']['title']); if ($p['newEv']) { $p['newEv']['title'] = $esc($p['newEv']['title']); }
  $o[] = "| #{$p['e']->id} {$p['e']->title} | `{$p['cur']['legacyUrl']}` (\"{$p['curEv']['title']}\") | "
       . ($p['newKey'] ? "`{$p['new']['legacyUrl']}` (\"{$p['newEv']['title']}\")" : 'cleared') . " | {$p['why']} |";
}
$o[] = '';
if ($bad) { $o[] = '## Refused'; $o[] = ''; foreach ($bad as $b) { $o[] = "- $b"; } $o[] = ''; }
$o[] = '## Can a script write the bad values back?';
$o[] = '';
$o[] = '- `inventory/canonical_entities.json` is not the source and nothing reads it to write Craft. `scripts/import/extract_entities.py` builds it, copying each place\'s legacyUrl from the Craft export (`inventory/extracts/craft-entities.json`); a rerun after this fix copies the corrected values.';
$o[] = '- The values came from `places-candidates.json`, built by `scripts/import/build_hub_candidates.py:190-204`: the first photograph page whose title category maps to a place became the place\'s own page. `import_places_and_series.php` copied them, but it skips any place that exists (line 46), so it cannot write them again.';
$o[] = '- `backfill_provenance.php` fills only empty fields. For the three cleared places it finds no evidence: no WordPress post with their slug, and no entity_index entry with a legacy page. A rerun leaves them empty.';
$o[] = '- To stop it at the source: correct or drop the seven rows in `places-candidates.json`, and change `build_hub_candidates.py` so an object page never becomes a place\'s legacyUrl (leave it empty). Not done here; Nathan\'s call.';
$o[] = '';
$o[] = 'Idempotent: a place already at its target is skipped and not saved.';
file_put_contents("$root/inventory/review/place-legacy-links-dry-run-2026-10-05.md", implode("\n", $o) . "\n");
echo 'wrote inventory/review/place-legacy-links-dry-run-2026-10-05.md' . PHP_EOL;

if (!$APPLY || $bad) { echo ($bad ? 'refusals above: nothing written' : 'nothing written') . PHP_EOL; return; }
$el = \Craft::$app->getElements(); $n = 0; $short = [];
foreach ($plan as $p) {
  $e = $p['e']; $e->setFieldValues($p['new']);
  if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); }
  $n++;
  $r = Entry::find()->id($e->id)->status(null)->one();
  foreach ($p['new'] as $h => $v) { if ((string)$r->getFieldValue($h) !== $v) { $short[] = "#{$e->id} $h"; } }
}
if ($short) { echo 'READ-BACK FAILED: ' . implode(', ', $short) . PHP_EOL; return; }
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('fix_place_legacy_links_2026_10_05.php', $n, "verified $n of $n", 'seven places: original-page link corrected or cleared');
echo "done: $n" . PHP_EOL;
