/**
 * The two collection band images are AI-generated engraved illustrations, of
 * Jerry Reynolds and A. B. Perkins, uploaded as archive assets on 17 September,
 * before the rule that a generated image is never an asset or a record
 * (docs/DATA-MODEL.md, Generated images). They are now banners in web/banners,
 * registered for the two collections and for both men's person records
 * (Nathan, 1 October 2026), with the credit on the image. This takes them out of
 * the archive: bandImage is cleared on #871 and #873, and assets #1409 and #1413
 * go to the trash, restorable. Nothing else refers to either.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/detach_ai_collection_bands.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$PAIRS = [871 => [1409, 'collection-band-2400x1000.png', 'jerry-reynolds.png'], 873 => [1413, 'story-of-our-valley-band-2400x1000.png', 'arthur-b-perkins.png']];
$bad = [];
foreach ($PAIRS as $cid => [$aid, $fname, $banner]) {
    $c = Entry::find()->id($cid)->status(null)->one(); $a = Asset::find()->id($aid)->one();
    /* Live records only: old revisions of the collection point at it too. */
    $other = (new \craft\db\Query())->from(['r' => '{{%relations}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = r.sourceId')
        ->where(['r.targetId' => $aid, 'el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])->andWhere(['not', ['r.sourceId' => $cid]])->count();
    if (!is_file(\Craft::getAlias('@root') . "/web/banners/$banner")) { $bad[] = "web/banners/$banner is missing"; }
    if ($a && $a->filename !== $fname) { $bad[] = "#$aid is not $fname"; }
    if ($other) { $bad[] = "#$aid is used by $other other record(s)"; }
    echo "#$cid {$c->title}: bandImage " . ($c->bandImage->ids() ? 'cleared' : 'already clear') . '; asset ' . ($a ? "#$aid $fname to the trash" : "#$aid already gone") . "; shown instead from web/banners/$banner" . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$el = Craft::$app->getElements();
foreach ($PAIRS as $cid => [$aid]) {
    $c = Entry::find()->id($cid)->status(null)->one(); if ($c->bandImage->ids()) { $c->setFieldValue('bandImage', []); if (!$el->saveElement($c)) { throw new \RuntimeException("#$cid"); } }
    if ($a = Asset::find()->id($aid)->one()) { if (!$el->deleteElement($a)) { throw new \RuntimeException("asset #$aid"); } }
}
$ok = !Asset::find()->id([1409, 1413])->exists() && !Entry::find()->id(871)->one()->bandImage->ids() && !Entry::find()->id(873)->one()->bandImage->ids();
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('detach_ai_collection_bands.php', 4, $ok ? 'verified' : 'SHORT', 'AI collection bands out of the archive, now registered banners');
if (!$ok) { throw new \RuntimeException('detach_ai_collection_bands: read-back failed'); }
