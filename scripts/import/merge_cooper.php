/**
 * Bill Cooper and William Cooper are one person (Nathan, 1 October 2026: "merge,
 * same as the others"). A record titled Bill Cooper, as the others take their
 * shortest printing, with "William Cooper" as an alias; his two water board
 * candidacies, Castaic Lake at large 2016 ("WILLIAM COOPER") and SCV Water
 * Division 1 2022 ("BILL COOPER"), link to it.
 *
 * And what the merge settles: SCV Water's own list of directors names "William
 * Cooper", Division 1, term expiring January 2027, which is the seat elected in
 * November 2022. With the two names one person, that list confirms the 2022
 * Division 1 winner, so the contest goes from derived to roster, its note says
 * why, and the list joins its sources.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/merge_cooper.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$el16 = Entry::find()->section('elections')->status(null)->slug('clwa-board-election-november-8-2016-at-large')->one();
$el22 = Entry::find()->section('elections')->status(null)->slug('scv-water-board-election-november-8-2022-division-1')->one();
if (!$el16 || !$el22) { echo 'REFUSING: the water elections are not imported' . PHP_EOL; return; }
$c16 = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $el16, 'field' => 'candidacyElection'])->nameAsPrinted('WILLIAM COOPER')->one();
$c22 = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $el22, 'field' => 'candidacyElection'])->nameAsPrinted('BILL COOPER')->one();
if (!$c16 || !$c22) { echo 'REFUSING: a Cooper candidacy is missing' . PHP_EOL; return; }
if ((string)$c22->outcome->value !== 'elected') { echo 'REFUSING: BILL COOPER is not the 2022 Division 1 winner' . PHP_EOL; return; }
$dirDoc = Entry::find()->section('documents')->status(null)->title('Santa Clarita Valley Water Agency: Board of Directors (web page, read 30 September 2026)')->one();
$dir = json_decode(file_get_contents(\Craft::getAlias('@root') . '/inventory/elections/scv-water-directors-2026-09-30.json'), true);
$listed = array_values(array_filter($dir['directors'], fn($d) => $d['name'] === 'William Cooper' && $d['division'] === '1' && $d['expires'] === 'January 2027'));
if (!$listed || !$dirDoc) { echo 'REFUSING: the directors list does not name William Cooper, Division 1, to January 2027' . PHP_EOL; return; }
$p = Entry::find()->section('persons')->status(null)->title('Bill Cooper')->one();
$other = array_filter([$c16, $c22], fn($c) => ($id = $c->candidacyPerson->status(null)->ids()[0] ?? null) && $id !== $p?->id);
if ($other) { echo 'REFUSING: a Cooper candidacy is linked to someone else' . PHP_EOL; return; }
$cands22 = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $el22, 'field' => 'candidacyElection'])->all();
$lift = array_filter($cands22, fn($c) => (string)$c->outcomeEvidence->value === 'derived');
echo ($p ? "#{$p->id} exists: " : 'create ') . 'Bill Cooper, also "William Cooper"; link 2016 WILLIAM COOPER (CLWA at large) and 2022 BILL COOPER (SCV Water Division 1)' . PHP_EOL;
echo "#{$el22->id} {$el22->title}: " . count($lift) . ' candidacies derived -> roster; SCV Water\'s list names William Cooper, Division 1, to January 2027' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$p) {
        $p = new Entry(); $p->sectionId = $svc->getSectionByHandle('persons')->id; $p->setTypeId($svc->getEntryTypeByHandle('person')->id); $p->title = 'Bill Cooper';
        $p->setFieldValues(['fullName' => 'Bill Cooper', 'personAliases' => "William Cooper",
            'recordProvenance' => 'merge_cooper.php, 1 October 2026: stood for the Castaic Lake and SCV Water boards; the two printings are one person (Nathan); public facts only']);
        if (!$elements->saveElement($p)) { throw new \RuntimeException('Bill Cooper: ' . json_encode($p->getFirstErrors())); }
    }
    foreach ([$c16, $c22] as $c) { $c = $get($c->id); $c->setFieldValue('candidacyPerson', [$p->id]); if (!$elements->saveElement($c)) { throw new \RuntimeException("#{$c->id}"); } }
    foreach ($lift as $c) { $c = $get($c->id); $c->setFieldValue('outcomeEvidence', 'roster'); if (!$elements->saveElement($c)) { throw new \RuntimeException("#{$c->id}"); } }
    $e = $get($el22->id);
    $notes = array_values(array_filter(array_map(fn($r) => (string)($r['note'] ?? ''), $e->footnotes ?? []), fn($t) => $t !== '' && !str_starts_with($t, 'Winners:')));
    $notes[] = 'Winners: the first of the count. SCV Water\'s own list of its directors names William Cooper, Division 1, his term expiring in January 2027, the term this election began; the ballot printed BILL COOPER, and the two are one person (Nathan, 1 October 2026). Upgraded to certified when the board\'s declaration is obtained.';
    $e->setFieldValues(['footnotes' => $fn($notes), 'sourceDocuments' => array_values(array_unique(array_merge($e->sourceDocuments->status(null)->ids(), [$dirDoc->id])))]);
    if (!$elements->saveElement($e)) { throw new \RuntimeException('election: ' . json_encode($e->getFirstErrors())); }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

$short = [];
foreach ([$c16, $c22] as $c) { if (($get($c->id)->candidacyPerson->status(null)->ids()[0] ?? null) !== $p->id) { $short[] = "#{$c->id} not linked"; } }
foreach ($cands22 as $c) { if ((string)$get($c->id)->outcomeEvidence->value !== 'roster') { $short[] = "#{$c->id} not roster"; } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('merge_cooper.php', 2 + count($lift), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'Bill and William Cooper one person; 2022 Division 1 to roster');
if ($short) { throw new \RuntimeException('merge_cooper: ' . implode('; ', $short)); }
