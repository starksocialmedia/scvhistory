/**
 * Focal points on the landscape images that person cards crop to 3:4.
 *
 * A card crops its image with object-fit: cover. Anchored at the top, a
 * portrait keeps its face; a landscape frame loses its sides instead, and a
 * sitter who is off centre is cut in half. Cameron Smyth stands at about 30%
 * across a 3128 x 2086 frame, so the card showed trees and half a shirt
 * (Nathan, 29 September 2026). The cards now anchor on the asset's focal point
 * where one is set (templates/_partials/record/img-pos.twig), and this sets it
 * on the five landscape images any person card uses, each read by eye from the
 * picture: the centre of the face, as fractions of width and height.
 *
 * The other 30 person card images are portrait or near-square and crop from
 * the top correctly; they get no focal point. A focal point already set is
 * never replaced.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_person_focal_points.php'))"
 */

use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

/* asset id => [filename, x, y, whose face] */
$FACES = [
    21579 => ['cameron-smyth-nathan-imhoff.jpg', 0.30, 0.24, 'Cameron Smyth, standing left of centre'],
    3 => ['juventino-del-valle-black-walnut-tree-camulos-1910s.jpg', 0.57, 0.35, 'Juventino del Valle, seated under the walnut'],
    18 => ['junipero_serra.jpg', 0.60, 0.36, 'Junípero Serra'],
    26 => ['thomas-o-larkin-portrait-california-state-library.jpg', 0.53, 0.40, 'Thomas O. Larkin'],
    8 => ['jose-antonio-aguirre-portrait-san-diego-history.jpg', 0.51, 0.37, 'José Antonio Aguirre'],
];

$plan = []; $done = []; $refused = [];
foreach ($FACES as $id => [$file, $x, $y, $who]) {
    $a = Asset::find()->id($id)->one();
    if (!$a || $a->filename !== $file) { $refused[] = "#$id is not $file"; continue; }
    if ($a->getHasFocalPoint()) {
        $fp = $a->getFocalPoint();
        if (abs($fp['x'] - $x) < 0.005 && abs($fp['y'] - $y) < 0.005) { $done[] = "#$id already set"; }
        else { $refused[] = "#$id already has a focal point " . json_encode($fp) . ', kept'; }
        continue;
    }
    $plan[$id] = [$x, $y];
    echo "   #$id $file: focal point ($x, $y), $who" . PHP_EOL;
}
echo PHP_EOL . 'ALREADY DONE: ' . ($done ? implode('; ', $done) : 'none') . PHP_EOL . 'NOT SET: ' . ($refused ? implode('; ', $refused) : 'none') . PHP_EOL;
echo 'SUMMARY: ' . count($plan) . ' focal point(s) to set.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$plan) { echo 'nothing to do; a second run is a no-op' . PHP_EOL; return; }

$short = [];
foreach ($plan as $id => [$x, $y]) {
    $a = Asset::find()->id($id)->one();
    $a->setFocalPoint(['x' => $x, 'y' => $y]);
    if (!Craft::$app->getElements()->saveElement($a)) { $short[] = "#$id save failed: " . json_encode($a->getFirstErrors()); continue; }
    $b = Asset::find()->id($id)->one();
    $fp = $b->getHasFocalPoint() ? $b->getFocalPoint() : null;
    if (!$fp || abs($fp['x'] - $x) > 0.005 || abs($fp['y'] - $y) > 0.005) { $short[] = "#$id reads back " . json_encode($fp); }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($plan) . ' focal points') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('set_person_focal_points.php', count($plan), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'focal points on 5 landscape person images');
if ($short) { throw new \RuntimeException('set_person_focal_points: ' . implode('; ', $short)); }
