/**
 * Cave Johnson Couts's enhanced portrait off his record (Nathan, 9 October 2026: "Couts joins the seven. If his credential
 * records text_to_image he is in that class whoever made him. Off now.").
 * The asset, #27387 cave-johnson-couts-us-army-edited.png, was restored to the enhanced pair on 8 October with the other 16
 * (restore_enhanced_pairs_2026_10_08.php). Its record summarised the credential as "edited (Firefly Image 5)"; the manifest
 * the file points to (XMP dcterms:provenance) records text_to_image steps (scan_credentials_2026_10_08.py, ERRORLOG
 * 9 October). Here the master's provenance link is read again and the manifest fetched again, and the script stops unless
 * text_to_image is in it.
 * Same mechanics as the seven: off every field, the original (#12) made the portrait, enhancedFrom cleared and the original
 * named in the asset's source, the credential's steps written as the manifest records them. Nothing deleted.
 * Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$M = "$root/inventory/incoming/done/cave-johnson-couts-portrait-us-army.png";
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the master as Nathan supplied it, its XMP provenance link read here', $M],
  ['record', 'the manifest that link names, fetched from Adobe\'s manifest store', 'the image file', 'read'],
  ['record', 'Couts\'s featuredImage and recordImages; the asset\'s filename, source, enhancedFrom and contentCredentials, the last three written here', 'the portrait file', 'read'],
]);
$b = file_get_contents($M);
if (hash('sha256', $b) !== '7639b6e30ab172090fcaf2561a56e3d97f157fcc2d204a85e291feed0c9fac54') { throw new \RuntimeException('master is not the file the asset records; stopped'); }
if (!preg_match('~https://cai-manifests\.adobe\.com/manifests/urn-c2pa-[0-9a-f-]+-adobe~', $b, $u)) { throw new \RuntimeException('no provenance link in the master; stopped'); }
$man = @file_get_contents($u[0], false, stream_context_create(['http' => ['header' => "User-Agent: SCVHistory archive provenance check\r\n", 'timeout' => 30]]));
if (!$man) { throw new \RuntimeException("could not fetch {$u[0]}; stopped"); }
$tti = substr_count($man, 'text_to_image');
echo "manifest {$u[0]}: " . strlen($man) . " bytes, text_to_image named $tti times" . PHP_EOL;
if (!$tti) { throw new \RuntimeException('the manifest does not name text_to_image; stopped'); }

$p = Entry::find()->id(323)->status(null)->one(); $a = Asset::find()->id(27387)->one(); $o = Asset::find()->id(12)->one();
if ($p?->title !== 'Cave Johnson Couts' || !$a || !$o) { throw new \RuntimeException('#323, #27387 or #12 not as expected'); }
$NOTE = ' Taken off every record on 9 October 2026: its credential records text-to-image steps, so it joins the seven held text-prompt portraits (Nathan). Made from asset #12, ' . $o->filename . '; its enhancedFrom link was cleared so the original\'s page no longer offers it.';
$CC = "Adobe content credential (C2PA), {$u[0]}\nSteps the manifest records (read 9 October 2026): opened (text_to_image) > edited (text_to_image), 2026-10-01 08:20 UTC, Adobe Firefly; an original is among its ingredients.\nThe line written here on 1 October, \"edited (Firefly Image 5)\", summarised the credential and left out the text-to-image steps (ERRORLOG, 9 October 2026).";
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;

$users = Entry::find()->relatedTo(['targetElement' => $a])->status(null)->all();
foreach ($users as $e) {
  $h = [];
  foreach ($e->getFieldLayout()->getCustomFields() as $f) {
    if (!($f instanceof craft\fields\Assets)) continue;
    $ids = $e->getFieldValue($f->handle)->status(null)->ids();
    if (in_array(27387, $ids)) { $h[$f->handle] = array_values(array_diff($ids, [27387])); }
  }
  if ($e->id === 323 && isset($h['featuredImage']) && !$h['featuredImage']) {
    $h['featuredImage'] = [12];
    $h['recordImages'] = array_values(array_diff($h['recordImages'] ?? $e->recordImages->status(null)->ids(), [12]));
  }
  echo "#{$e->id} {$e->title}: " . json_encode($h) . PHP_EOL;
  if (!$APPLY || !$h) continue;
  foreach ($h as $k => $v) $e->setFieldValue($k, $v);
  if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); } $n++;
}
if (!$users) { echo '#27387 on no record already' . PHP_EOL; }
$src = (string)$a->getFieldValue('source');
// The source field holds 500 characters: the 8 October off-and-back sentence is shortened to make room.
$newSrc = str_contains($src, 'Taken off every record on 9 October 2026') ? $src : trim(str_replace(' Taken off its record on 8 October 2026 under a rule on Firefly edits, and put back the same day on Nathan\'s word (the enhanced-pair rule of 6 October 2026).', ' Off its record and back on 8 October 2026 (the enhanced-pair rule of 6 October 2026).', $src) . $NOTE);
if (mb_strlen($newSrc) > 500) { throw new \RuntimeException('source note over 500 characters: ' . mb_strlen($newSrc)); }
$change = $newSrc !== $src || (string)$a->contentCredentials !== $CC || $a->getFieldValue('enhancedFrom')->ids();
echo '#27387: ' . ($change ? 'source note, credential steps, enhancedFrom cleared' : 'done already') . PHP_EOL;
if ($APPLY && $change) {
  $a->setFieldValues(['source' => $newSrc, 'contentCredentials' => $CC, 'enhancedFrom' => []]);
  if (!$el->saveElement($a)) { throw new \RuntimeException('#27387 ' . json_encode($a->getFirstErrors())); } $n++;
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('couts_text_prompt_off_2026_10_09.php', $n, 'verified', 'Couts\'s text-prompt portrait off his record; the original his portrait'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
