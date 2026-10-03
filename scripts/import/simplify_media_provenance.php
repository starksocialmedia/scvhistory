/**
 * Provenance sentences for a reader (Nathan, 3 October 2026: "Simplify the
 * media provenance texts. Keep the checksum in the data, not the sentence. A
 * reader wants to know where it came from, not that it was re-encoded").
 *
 * Every asset whose source sentence named a checksum, the mirror, a manifest,
 * a received file name or the re-encoding: the checksum moves to
 * sourceChecksum ("sha256:" or "sha1:"), and the sentence keeps where the file
 * came from, who made or edited it, and what is not established. "Source per
 * Nathan Imhoff" becomes the source itself where it is named (the City's
 * council portrait) and "supplied by Nathan Imhoff" where it is not; asset and
 * record numbers become words.
 * Each sentence is exact; the script refuses if the stored one is not as read.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/simplify_media_provenance.php'))"
 */

use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$CITY = 'The City of Santa Clarita\'s council portrait, upscaled by Nathan Imhoff.';
$NEW = [
    27402 => $CITY, 27400 => $CITY, 27398 => $CITY,
    27396 => 'Supplied and edited by Nathan Imhoff; the original photograph is not recorded.',
    27394 => 'Supplied and upscaled by Nathan Imhoff; the original photograph is not recorded.',
    27391 => 'Supplied and edited by Nathan Imhoff; the original photograph is not recorded.',
    27389 => 'Edited by Nathan Imhoff from a photograph on Bill Cooper\'s campaign site or the SCV Water board page (campaign or agency material); which page is not recorded.',
    27387 => 'Edited by Nathan Imhoff from a U.S. Army portrait; where the copy came from is not recorded.',
    27385 => 'Edited by Nathan Imhoff from a photograph on Wikimedia Commons; the Commons file is not recorded.',
    27383 => 'Edited by Nathan Imhoff from the Wikimedia Commons original held on this record (File:DemetriusGScofield.jpg, published 1911). Public domain, as the original is.',
    27381 => 'Edited by Nathan Imhoff from the archive\'s own 1964 photograph of Perkins as Outstanding Citizen.',
    27350 => 'Campaign material, received 1 October 2026 as a file with no author, copyright or caption. The photographer, the rights holder and any permission to publish are not established.',
    26976 => 'Wikimedia Commons, File:Buck McKeon 2011.jpeg, the original, 2630 x 3944: "United States Congress," from his House website\'s high-resolution file. Public domain as a work of the federal government.',
    21863 => 'Wikimedia Commons, File:CSUN Central Campus.JPG, by Cbl62, https://commons.wikimedia.org/wiki/File:CSUN_Central_Campus.JPG, CC BY-SA 3.0; the original, 3264 x 1946, Pentax Optio V10. Received 29 September 2026.',
    21861 => 'Flickr, tkksummers, "Santa Clarita City Hall," https://www.flickr.com/photos/tkksummers/2600036728/, CC BY-SA 2.0; the original, 3648 x 2736, Kodak EasyShare V1003. Received 29 September 2026.',
    21761 => 'Wikimedia Commons, File:DemetriusGScofield.jpg: "Original publication: 1911," credited to Standard Oil of California. Public domain as published before 1929; the 1911 publication is not named (see the editor note on Demetrius G. Scofield\'s record). Received 28 September 2026.',
    21581 => 'Campaign material, received 28 September 2026. The photographer, the rights holder and any permission to publish are not established.',
    21579 => 'Photograph by Nathan Imhoff, who holds the copyright and licenses it to SCVHistory. Received from him on 28 September 2026.',
];
$bad = []; $plan = [];
foreach (Asset::find()->each(500) as $a) {
    $h = []; foreach ($a->getFieldLayout()?->getCustomFields() ?? [] as $f) { $h[$f->handle] = true; }
    if (!isset($h['source'], $h['sourceChecksum'])) { continue; }
    $s = (string)$a->getFieldValue('source');
    if (!preg_match('~SHA-|manifest|re-encoded|legacy mirror~i', $s)) { continue; }
    preg_match('~SHA-(256|1) ([0-9a-f]{40,64})~', $s, $m);
    $sum = $m ? 'sha' . $m[1] . ':' . $m[2] : '';
    if (preg_match('~^SCVHistory\.com, (gif/\S+?), from the legacy mirror of 20 August 2026, SHA-256 [0-9a-f]{64}, matching the manifest; the stored copy is re-encoded on import\.$~', $s, $g)) { $new = "SCVHistory.com, {$g[1]}, as published on the original site."; }
    elseif (isset($NEW[$a->id])) { $new = $NEW[$a->id]; }
    else { $bad[] = "#{$a->id} {$a->filename}: no rewrite for \"" . mb_substr($s, 0, 80) . '"'; continue; }
    if (!$sum) { $bad[] = "#{$a->id}: no checksum found"; }
    $have = trim((string)$a->getFieldValue('sourceChecksum'));
    if ($have !== '' && $have !== $sum) { $bad[] = "#{$a->id}: sourceChecksum already \"$have\""; }
    if (preg_match('~SHA-|manifest|re-encoded|mirror|\x{2014}~iu', $new)) { $bad[] = "#{$a->id}: the new sentence still has process words or an em dash"; }
    $plan[$a->id] = [$new, $sum];
    echo "#{$a->id} {$a->filename}\n   + $new\n   checksum -> $sum\n";
}
echo count($plan) . ' assets' . PHP_EOL . 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => [$new, $sum]) {
    $a = Asset::find()->id($id)->one();
    $a->setFieldValues(['source' => $new, 'sourceChecksum' => $sum]);
    if (!Craft::$app->getElements()->saveElement($a)) { $short[] = "#$id " . json_encode($a->getFirstErrors()); continue; }
    $r = Asset::find()->id($id)->one();
    if ((string)$r->getFieldValue('source') !== $new || (string)$r->getFieldValue('sourceChecksum') !== $sum) { $short[] = "#$id read-back"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' assets') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('simplify_media_provenance.php', count($plan), $short ? 'SHORT' : 'verified', 'provenance sentences for readers; checksums moved to sourceChecksum');
if ($short) { throw new \RuntimeException('simplify_media_provenance: ' . implode(', ', $short)); }
