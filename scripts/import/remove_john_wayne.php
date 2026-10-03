/**
 * Removes the John Wayne person record (#16235) (Nathan, 3 October 2026: "John
 * Wayne: remove the record. The 12 links go, and the name stays in 417 records
 * as text, which is accurate. He filmed here. That is a location credit, not a
 * connection to the valley").
 *
 * This reverses the locality ruling of 21 September, which kept him as "filmed
 * at Melody Ranch" (mark_external_people.php, $KEPT). The rule now is that a
 * person belongs for what they did here (docs/DATA-MODEL.md, "A person belongs
 * for what they did here").
 *
 * Four steps, in this order, so nothing rebuilds him:
 *  1. review/records-decided.json: his row is still "approved", and a rerun of
 *     the review batch would create him again (the trap the Kit Carson merge
 *     closed). It becomes "external".
 *  2. inventory/legacy/name-canon.json: added as external, so the prose linker
 *     does not look for a record. No Wikidata id: the record held none, and
 *     none is supplied from memory.
 *  3. The 12 relations (11 articles as subjectPerson, photograph #5249 as
 *     photoPeople) are taken off the canonical entries. Their text is not
 *     touched. Revisions keep their history.
 *  4. The record is deleted the ordinary way: it goes to Craft's trash and can
 *     be restored for 30 days (softDeleteDuration). It is never hard-deleted.
 * check_removed_claims.php then fails if a person by that name is live again
 * (scripts/import/removed-claims.json, removedRecords).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/remove_john_wayne.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$ID = 16235; $NAME = 'John Wayne';
$WHY = 'He filmed here (Placerita Canyon, Beale\'s Cut, Hoot Gibson\'s rodeo near Bouquet Junction): a location credit, not a connection to the valley. Nathan, 3 October 2026.';
$root = \Craft::getAlias('@root');
$DEC = \Craft::getAlias('@review') . '/records-decided.json';
$CANON = "$root/inventory/legacy/name-canon.json";
$norm = fn(string $v) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $v)));

$doc = json_decode(file_get_contents($DEC), true); $rows = $doc['decisions'] ?? [];
$decIdx = [];
foreach ($rows as $i => $r) { if (($r['type'] ?? '') !== 'pair' && $norm((string)($r['name'] ?? '')) === $norm($NAME) && ($r['action'] ?? '') !== 'external') { $decIdx[] = $i; } }
$canon = json_decode(file_get_contents($CANON), true);
$inCanon = (bool)array_filter($canon['canon'], fn($c) => $c['canonical'] === $NAME);

$p = Entry::find()->id($ID)->status(null)->one();
if ($p && $p->title !== $NAME) { echo "REFUSING: #$ID is {$p->title}" . PHP_EOL; return; }
$links = [];
if ($p) {
    foreach (Entry::find()->relatedTo(['targetElement' => $p])->status(null)->limit(null)->all() as $e) {
        foreach ($e->getFieldLayout()->getCustomFields() as $f) {
            if (!$f instanceof \craft\fields\BaseRelationField) { continue; }
            if (in_array($ID, $e->getFieldValue($f->handle)->ids(), true)) { $links[] = [$e, $f->handle]; }
        }
    }
}
echo 'decision rows to mark external: ' . count($decIdx) . PHP_EOL;
echo 'name canon: ' . ($inCanon ? 'already listed' : 'add as external') . PHP_EOL;
echo 'record: ' . ($p ? "#$ID live, " . count($links) . ' relations to take off' : 'already removed') . PHP_EOL;
foreach ($links as [$e, $h]) { echo "   {$e->section->handle} #{$e->id} $h  {$e->title}" . PHP_EOL; }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

foreach ($decIdx as $i) {
    $rows[$i]['action'] = 'external'; $rows[$i]['wikidataId'] = '';
    unset($rows[$i]['articles'], $rows[$i]['aliases']);
    $rows[$i]['settledBy'] = 'Nathan, 2026-10-03: removed; ' . $WHY;
}
if ($decIdx) { $doc['decisions'] = array_values($rows); file_put_contents("$DEC.tmp", json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX); rename("$DEC.tmp", $DEC); @chmod($DEC, 0644); }
if (!$inCanon) {
    $canon['canon'][] = ['canonical' => $NAME, 'type' => 'person', 'external' => true, 'wikidataId' => '', 'aliases' => [],
        'note' => 'External. ' . $WHY . ' The archive holds no record; the name stays in the text.'];
    file_put_contents($CANON, json_encode($canon, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
}
$els = Craft::$app->getElements(); $short = [];
foreach ($links as [$e, $h]) {
    $e = Entry::find()->id($e->id)->status(null)->one();
    $e->setFieldValue($h, array_values(array_diff($e->getFieldValue($h)->ids(), [$ID])));
    if (!$els->saveElement($e)) { $short[] = "#{$e->id} " . json_encode($e->getFirstErrors()); }
}
if ($p && !$short) { if (!$els->deleteElement($p)) { $short[] = "delete #$ID"; } }

$left = Entry::find()->relatedTo(['targetElement' => $ID])->status(null)->count();
$live = Entry::find()->id($ID)->status(null)->exists();
$trashed = Entry::find()->id($ID)->status(null)->trashed()->exists();
$decOk = !array_filter(json_decode(file_get_contents($DEC), true)['decisions'], fn($r) => ($r['type'] ?? '') !== 'pair' && $norm((string)($r['name'] ?? '')) === $norm($NAME) && ($r['action'] ?? '') !== 'external');
echo "READ-BACK relations left $left; live " . ($live ? 'yes' : 'no') . '; in trash ' . ($trashed ? 'yes' : 'no') . '; decision external ' . ($decOk ? 'yes' : 'no') . PHP_EOL;
$ok = !$short && $left === 0 && !$live && $trashed && $decOk;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('remove_john_wayne.php', count($links), $ok ? 'verified' : 'SHORT', 'John Wayne #16235 to the trash; relations off; decision and canon external');
if (!$ok) { throw new \RuntimeException('remove_john_wayne: ' . implode(', ', $short)); }
