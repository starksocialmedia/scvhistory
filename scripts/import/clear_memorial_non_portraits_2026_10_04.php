/**
 * featuredImage on a memorial record is the man's likeness. On 18 of the 54 it held something else,
 * which the index cropped into a card face (Nathan, 4 October 2026: "The Vietnam cards use photographs
 * of the memorial wall as portraits, and they are ugly cropped letters. Those images belong on the
 * memorial record itself, as the photograph of the name on the wall, not as a stand-in portrait ...
 * Check which of the 54 this affects, since Vietnam may not be the only one"):
 *   10 photographs of the name on the Traveling Vietnam Memorial Wall (Westfield Valencia, 2013);
 *   6 photographs of a headstone or grave; John Amos Ward's, the colonel's letter of 9 March 1945
 *   to his widow; Perry Leon Cherry's, a yearbook clipping that is mostly text.
 * Read by eye on 4 October 2026. Each image is already among its record's images (recordImages), so
 * clearing featuredImage leaves it on the record, captioned, and the card shows no face instead of a
 * crop. Nothing is deleted. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/clear_memorial_non_portraits_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
/* record id => the featured image's filename, as read */
$NOT = [
    1356 => 'wall_brucestlouis.jpg', 1362 => 'wall_davidreeder.jpg', 1366 => 'wall_frankortega.jpg', 1370 => 'wall_garyturnbull.jpg',
    1368 => 'wall_garymonteleone.jpg', 1374 => 'wall_johnborders.jpg', 1376 => 'wall_josephgodwin.jpg', 1378 => 'wall_michaelfay.jpg',
    1380 => 'wall_stephenpeterson.jpg', 1382 => 'wall_terrygemas.jpg',
    516 => 'ww2_edwardcontreras_headstone.jpg', 552 => 'korea_henryacuna_grave.jpg', 564 => 'ww2_jamesmredmond_grave.jpg',
    534 => 'johnmconant_grave.jpg', 568 => 'johncordova_grave.jpg', 1407 => 'ww2_williamernestpineau_headstone_large.jpg',
    570 => 'johnamosward030945.jpg', 572 => 'tlp_leoncherrymug_large.jpg',
];
$el = Craft::$app->getElements(); $bad = []; $todo = [];
foreach ($NOT as $id => $file) {
    $e = Entry::find()->id($id)->section('warMemorials')->status(null)->one();
    $a = $e?->featuredImage->one();
    if (!$e) { $bad[] = "#$id is not a memorial record"; continue; }
    if (!$a) { echo "#$id {$e->title}: already cleared" . PHP_EOL; continue; }
    if ($a->filename !== $file) { $bad[] = "#$id featured image is {$a->filename}, not $file"; continue; }
    if (!in_array($a->id, $e->recordImages->ids())) { $bad[] = "#$id: $file is not among its record images"; continue; }
    $todo[$id] = $e; echo "#$id {$e->title}: clear featuredImage $file (stays in its images)" . PHP_EOL;
}
echo count($todo) . ' to clear. REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$tx = Craft::$app->getDb()->beginTransaction();
try { foreach ($todo as $id => $e) { $e->setFieldValue('featuredImage', []); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } } $tx->commit(); }
catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$short = array_keys(array_filter($NOT, fn($f, $id) => Entry::find()->id($id)->status(null)->one()->featuredImage->exists(), ARRAY_FILTER_USE_BOTH));
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($todo) . ' cleared') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('clear_memorial_non_portraits_2026_10_04.php', count($todo), $short ? 'SHORT' : 'verified', '18 memorial records whose featured image was a wall name, a grave, a letter or a clipping: cleared, kept in their images');
