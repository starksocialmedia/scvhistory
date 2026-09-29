/**
 * The provenance pass: provenanceKind on every asset where the evidence says
 * how the archive came to hold it, and nothing where it does not.
 *
 * Needs add_asset_provenance_fields.php applied first; until then the dry run
 * still prints the plan, and an apply refuses.
 *
 * The evidence, group by group (the 126 assets with no legacySourcePath were
 * surveyed on 29 September 2026; the mirror assets are here as well so that
 * the field means the same thing everywhere):
 *
 *   legacy-mirror     every asset with a legacySourcePath: the path is the
 *                     provenance. That includes four files in the WordPress
 *                     folders that were proved byte-identical to a mirror file.
 *   legacy-mirror     the 27 files in legacy/ without a path. Only the mirror
 *                     imports ever wrote to legacy/ (4,192 of its 4,221 files
 *                     carry a path); their legacySourcePath is backfilled when
 *                     Reggie is back, by hash against the mirror, not here.
 *   (nothing)         the two Craft rename collisions in legacy/,
 *                     perkins_ab_..._nitl.jpg and jj2003a_..._lure.jpg. They are
 *                     byte-identical to files already held and are listed for
 *                     deletion by hand (fix_asset_provenance.php).
 *   legacy-wordpress  the 78 files in persons/, places/ and general/ whose
 *                     filename is an upload in the WordPress export
 *                     (inventory/wp_media_order.json, inventory/wp_content.json).
 *                     All 78 match. Their licence is left as it is: coming
 *                     through WordPress says nothing about who holds the rights.
 *   commissioned      the 7 SCVHistory logo and seal files in site/. They are
 *                     the archive's own marks, so licence 'scvhistory' where it
 *                     is empty.
 *   outside           the Hart portrait #21573, with sourceUrl the Commons file
 *                     page and acquiredDate read from the date already written
 *                     in its source field. Nothing is typed in for it here.
 *   (nothing)         the 11 files at the volume root, placed by hand on
 *                     17 September with no script, no export entry and no
 *                     mirror path behind them. Listed as the research list.
 *
 * The seal of the City of Santa Clarita (general/) is flagged: it came through
 * WordPress, but the rights are the City's and it is not used by any record.
 *
 * Never overwrites a non-empty value. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_asset_provenance.php'))"
 */

use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$root = \Craft::getAlias('@root');
$fs = Craft::$app->getFields();
$schema = $fs->getFieldByHandle('provenanceKind') && $fs->getFieldByHandle('sourceUrl') && $fs->getFieldByHandle('acquiredDate');
if (!$schema) { echo 'NOTE: provenanceKind, sourceUrl and acquiredDate do not exist yet. Apply add_asset_provenance_fields.php first; this plan is what would be written after it.' . PHP_EOL . PHP_EOL; }

/* The WordPress export: every uploaded filename it mentions. */
$wp = [];
foreach (['inventory/wp_media_order.json', 'inventory/wp_content.json'] as $p) {
    if (!is_file("$root/$p")) { echo "REFUSING: $p is missing" . PHP_EOL; return; }
    preg_match_all('~wp-content\\\\?/uploads\\\\?/\d{4}\\\\?/\d{2}\\\\?/([^"\\\\?\s]+)~', file_get_contents("$root/$p"), $m);
    foreach ($m[1] as $n) { $wp[strtolower(rawurldecode($n))] = true; }
}
$COLLISIONS = ['perkins_ab_2026-09-18-071146_nitl.jpg', 'jj2003a_2026-09-18-071338_lure.jpg'];
$HART = 21573;
$HART_URL = 'https://commons.wikimedia.org/wiki/File:Williamshart.jpg';
$MONTHS = ['january' => 1, 'february' => 2, 'march' => 3, 'april' => 4, 'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12];

$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$plan = []; $held = []; $count = []; $already = 0;
foreach (Asset::find()->volumeId($vol->id)->all() as $a) {
    $has = [];
    foreach ($a->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
    $read = function ($h) use ($a, $has) {
        if (!isset($has[$h])) { return ''; }
        try { $v = $a->getFieldValue($h); } catch (\Throwable $e) { return ''; }
        if (is_object($v) && property_exists($v, 'value')) { return (string)$v->value; }
        if ($v instanceof \craft\fields\data\LinkData) { return (string)$v->getUrl(); }
        return trim((string)$v);
    };
    $folder = $a->getFolder()->path ?: '';
    $file = $a->filename;
    $path = $read('legacySourcePath');
    $set = []; $why = '';

    if ($path !== '') { $set['provenanceKind'] = 'legacy-mirror'; $why = 'mirror path'; }
    elseif ($folder === 'legacy/' && in_array($file, $COLLISIONS, true)) { $held[] = "#{$a->id} $folder$file: rename collision, byte-identical to a held file, for deletion by hand"; continue; }
    elseif ($folder === 'legacy/') { $set['provenanceKind'] = 'legacy-mirror'; $why = 'legacy/ folder, path to be backfilled from Reggie'; }
    elseif ($folder === 'site/' && str_starts_with($file, 'scvhistory-')) { $set['provenanceKind'] = 'commissioned'; $set['license'] = 'scvhistory'; $why = "SCVHistory's own mark"; }
    elseif (in_array($folder, ['persons/', 'places/', 'general/'], true) && isset($wp[strtolower($file)])) {
        $set['provenanceKind'] = 'legacy-wordpress'; $why = 'upload in the WordPress export';
        if ($file === 'seal_of_santa_clarita_california.png') { $held[] = "#{$a->id} $folder$file: FLAG, the City's seal; the rights are the City's, unused by any record"; }
    }
    elseif ($a->id === $HART) {
        $src = $read('source');
        if (!preg_match('~obtained through .*? on (\d{1,2}) ([A-Za-z]+) (\d{4})~i', $src, $d) || !isset($MONTHS[strtolower($d[2])])) { $held[] = "#{$a->id} $file: no acquisition date in its source field, left for Nathan"; continue; }
        if (!str_contains($src, $HART_URL)) { $held[] = "#{$a->id} $file: its source field does not name $HART_URL, left for Nathan"; continue; }
        $set = ['provenanceKind' => 'outside', 'sourceUrl' => $HART_URL, 'acquiredDate' => sprintf('%04d-%02d-%02d', $d[3], $MONTHS[strtolower($d[2])], $d[1])];
        $why = 'outside, from its own source field';
    }
    else { $held[] = "#{$a->id} " . ($folder ?: '(root)/') . "$file: no evidence of how the archive came to hold it, left empty"; continue; }

    /* Fill gaps only. */
    $todo = [];
    foreach ($set as $h => $v) {
        $cur = $schema || !in_array($h, ['provenanceKind', 'sourceUrl', 'acquiredDate'], true) ? $read($h) : '';
        if ($cur === '') { $todo[$h] = $v; }
        elseif ($cur !== $v) { $held[] = "#{$a->id} $folder$file: $h is already '$cur', would have been '$v', kept"; }
    }
    if (!$todo) { $already++; continue; }
    $plan[] = [$a, $todo, $why];
    $k = $todo['provenanceKind'] ?? '(other fields)';
    $count[$k] = ($count[$k] ?? 0) + 1;
}

/* The plan, one line per asset, except the mirror, which is one line per 500. */
$mirrorN = 0;
foreach ($plan as [$a, $todo, $why]) {
    if ($why === 'mirror path') { $mirrorN++; if ($mirrorN > 3) { continue; } }
    echo '   #' . str_pad($a->id, 6) . str_pad(($a->getFolder()->path ?: '') . $a->filename, 62) . ' ' . json_encode($todo, JSON_UNESCAPED_SLASHES) . '  (' . $why . ')' . PHP_EOL;
}
if ($mirrorN > 3) { echo '   ... and ' . ($mirrorN - 3) . ' more mirror-path assets, provenanceKind legacy-mirror' . PHP_EOL; }
echo PHP_EOL . 'NOT SET (' . count($held) . ')' . PHP_EOL;
foreach ($held as $h) { echo '   ' . $h . PHP_EOL; }
echo PHP_EOL . 'SUMMARY: ' . count($plan) . ' assets to set ' . json_encode($count) . ', ' . $already . ' already done, ' . count($held) . ' not set. Nothing was invented for them.' . PHP_EOL;

if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$schema) { echo 'REFUSING: apply add_asset_provenance_fields.php first' . PHP_EOL; return; }
if (!$plan) { echo 'nothing to do; a second run is a no-op' . PHP_EOL; return; }

$els = Craft::$app->getElements(); $short = [];
foreach ($plan as [$a, $todo]) {
    foreach ($todo as $h => $v) { $a->setFieldValue($h, $h === 'sourceUrl' ? ['type' => 'url', 'value' => $v] : $v); }
    if (!$els->saveElement($a)) { $short[] = "#{$a->id} save failed: " . json_encode($a->getFirstErrors()); }
}
/* Read back. */
foreach ($plan as [$a, $todo]) {
    $b = Asset::find()->id($a->id)->one();
    foreach ($todo as $h => $v) {
        $got = $b->getFieldValue($h);
        $got = is_object($got) && property_exists($got, 'value') ? (string)$got->value : ($got instanceof \craft\fields\data\LinkData ? (string)$got->getUrl() : trim((string)$got));
        if ($got !== $v) { $short[] = "#{$a->id} $h reads back '$got', expected '$v'"; }
    }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', array_slice($short, 0, 20)) : 'OK: ' . count($plan) . ' assets') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('set_asset_provenance.php', count($plan), $short ? 'SHORT: ' . count($short) . ' mismatches' : 'verified', json_encode($count));
if ($short) { throw new \RuntimeException('set_asset_provenance: ' . count($short) . ' mismatches'); }
