/**
 * READ ONLY. Writes storage/runtime/asset_origins.json: every archiveMedia asset
 * with its volume path, its provenanceKind if set, and where its original is,
 * for scan_content_credentials.py, which runs on the host.
 *
 * The originals matter because Craft re-encodes a file on upload (the Hart
 * portrait arrived progressive and is stored baseline, different bytes), so a
 * content credential in the file that arrived is gone from the file stored.
 *
 *   mirror     legacySourcePath, relative to the mirror's scvhistory.com
 *   wordpress  the upload URL in the WordPress export, when the filename is one
 *   none       nothing known: the volume copy is all there is
 *
 * Writes nothing to the database.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_asset_origins.php'))"
 */
use craft\elements\Asset;
$root = \Craft::getAlias('@root');
$wp = [];
foreach (['inventory/wp_media_order.json', 'inventory/wp_content.json'] as $p) {
    preg_match_all('~(https?:\\\\?/\\\\?/[^"\s]+?wp-content\\\\?/uploads\\\\?/\d{4}\\\\?/\d{2}\\\\?/([^"\\\\?\s]+))~', file_get_contents("$root/$p"), $m, PREG_SET_ORDER);
    foreach ($m as $x) { $wp[strtolower(rawurldecode($x[2]))] = str_replace('\\/', '/', $x[1]); }
}
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$fsPath = rtrim(\Craft::getAlias($vol->getFs()->path ?? '@webroot/uploads/archive-media'), '/');
$rel = ltrim(str_replace($root, '', $fsPath), '/');
$out = []; $n = ['mirror' => 0, 'wordpress' => 0, 'none' => 0];
foreach (Asset::find()->volumeId($vol->id)->all() as $a) {
    $has = [];
    foreach ($a->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
    $path = isset($has['legacySourcePath']) ? trim((string)$a->getFieldValue('legacySourcePath')) : '';
    $kind = isset($has['provenanceKind']) ? (string)($a->getFieldValue('provenanceKind')->value ?? '') : '';
    $o = $path !== '' ? ['mirror', $path] : (isset($wp[strtolower($a->filename)]) ? ['wordpress', $wp[strtolower($a->filename)]] : ['none', '']);
    $n[$o[0]]++;
    $out[] = ['id' => $a->id, 'volumeFile' => $rel . '/' . ($a->getFolder()->path ?? '') . $a->filename, 'kind' => $kind, 'origin' => $o[0], 'original' => $o[1]];
}
file_put_contents("$root/storage/runtime/asset_origins.json", json_encode($out, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
echo count($out) . ' assets written to storage/runtime/asset_origins.json: ' . json_encode($n) . PHP_EOL;
