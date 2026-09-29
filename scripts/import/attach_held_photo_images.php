/**
 * Photographs whose images the archive already holds but never attached.
 *
 * Grok's live check of the 226 imageless photograph records (inventory/review/
 * photographs-without-image-livecheck.json, 29 September 2026) found that most
 * pages show their picture directly, from /gif/, under a filename that is not
 * the record's code: lw9801 shows sg19980426ssusd.jpg. The photograph page
 * finds an image by featuredImage, then recordImages, then a file named after
 * the code, so a picture under any other name is never found, however long it
 * has been in the volume.
 *
 * This matches each record's page images (role page_img, under /gif/) against
 * the volume by filename, preferring the _large version where the volume holds
 * it (usually 1600-2400 px against the 800 px display copy), and attaches them:
 * the first as featuredImage, the rest as recordImages, in page order.
 *
 * Matched on the page KEY, not the code: six records have a key that differs
 * from their code (lw2274, not la2274), and the live page always uses the key.
 * A record is matched by its id and must be a photograph whose legacyKey is the
 * key or the code; anything else is reported.
 *
 * Not done here: the 88 records whose images live only under /scvhistory/files/
 * (Grok is crawling those), records whose page images the volume does not hold
 * (they are listed, for the mirror), and the 13 with no picture at all.
 *
 * Fills empty fields only. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/attach_held_photo_images.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$root = \Craft::getAlias('@root');
$check = json_decode(file_get_contents($root . '/inventory/review/photographs-without-image-livecheck.json'), true);
if (!$check) { echo 'REFUSING: the live check file is missing' . PHP_EOL; return; }
$held = [];
foreach (Asset::find()->volume('archiveMedia')->all() as $a) { $held[strtolower($a->filename)] ??= $a; }

$plan = []; $cls = []; $none = []; $partial = []; $odd = []; $done = 0;
foreach ($check as $r) {
    $pi = array_values(array_filter($r['images'], fn($i) => ($i['role'] ?? '') === 'page_img' && str_contains($i['src'], '/gif/')));
    if (!$pi) { $cls[$r['class'] === 'live_image_ok' ? 'images only under /files/ (Grok crawling)' : $r['class']][] = $r['id']; continue; }
    $e = Entry::find()->id($r['id'])->section('photographs')->status(null)->one();
    $lk = $e ? strtolower(trim((string)$e->legacyKey)) : '';
    if (!$e || !in_array($lk, [strtolower($r['key']), strtolower($r['code'])], true)) { $odd[] = $r['id'] . ' ' . $r['key'] . ($e ? ' (legacyKey ' . $lk . ')' : ' (no photograph record)'); continue; }
    if ($e->featuredImage->exists() || $e->recordImages->exists()) { $done++; continue; }
    $picked = []; $missing = [];
    foreach ($pi as $i) {
        $f = strtolower(basename(parse_url($i['src'], PHP_URL_PATH)));
        $lg = preg_replace('~\.(jpe?g|png|gif)$~', '_large.$1', $f);
        $a = $held[$lg] ?? $held[$f] ?? null;
        if ($a) { if (!in_array($a->id, array_map(fn($x) => $x->id, $picked), true)) { $picked[] = $a; } } else { $missing[] = $f; }
    }
    if (!$picked) { $none[] = $r['id'] . ' ' . $r['key'] . ': ' . implode(', ', $missing); continue; }
    if ($missing) { $partial[] = $r['id'] . ' ' . $r['key'] . ' lacks ' . implode(', ', $missing); }
    $plan[$e->id] = $picked;
    echo '   #' . str_pad($e->id, 5) . str_pad($r['key'], 11) . 'featured ' . str_pad($picked[0]->filename . ' ' . $picked[0]->width . 'px', 34) . (count($picked) > 1 ? ' + ' . (count($picked) - 1) . ' more: ' . implode(', ', array_map(fn($a) => $a->filename, array_slice($picked, 1))) : '') . PHP_EOL;
}
echo PHP_EOL . 'ATTACH: ' . count($plan) . ' records, ' . array_sum(array_map('count', $plan)) . ' images; ' . count(array_filter($plan, fn($p) => str_contains($p[0]->filename, '_large'))) . ' featured at the _large size.' . PHP_EOL;
echo 'ALREADY ATTACHED: ' . $done . PHP_EOL;
echo PHP_EOL . 'PARTLY HELD (' . count($partial) . '), attached as far as held:' . PHP_EOL; foreach ($partial as $x) { echo '   ' . $x . PHP_EOL; }
echo PHP_EOL . 'PAGE IMAGE NOT IN THE VOLUME (' . count($none) . '), for the mirror:' . PHP_EOL; foreach ($none as $x) { echo '   ' . $x . PHP_EOL; }
echo PHP_EOL . 'NOT MATCHED TO A RECORD (' . count($odd) . '):' . PHP_EOL; foreach ($odd as $x) { echo '   ' . $x . PHP_EOL; }
echo PHP_EOL . 'OTHER CLASSES: ' . json_encode(array_map('count', $cls)) . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$plan) { echo 'nothing to do; a second run is a no-op' . PHP_EOL; return; }

$short = [];
foreach ($plan as $id => $picked) {
    $e = Entry::find()->id($id)->status(null)->one();
    $e->setFieldValue('featuredImage', [$picked[0]->id]);
    if (count($picked) > 1) { $e->setFieldValue('recordImages', array_map(fn($a) => $a->id, array_slice($picked, 1))); }
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id save failed"; continue; }
    if ((Entry::find()->id($id)->status(null)->one()->featuredImage->one()->id ?? null) !== $picked[0]->id) { $short[] = "#$id featuredImage not set"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', array_slice($short, 0, 20)) : 'OK: ' . count($plan) . ' records') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('attach_held_photo_images.php', count($plan), $short ? 'SHORT: ' . count($short) : 'verified', 'held page images attached to imageless photographs');
if ($short) { throw new \RuntimeException('attach_held_photo_images: ' . count($short) . ' failures'); }
