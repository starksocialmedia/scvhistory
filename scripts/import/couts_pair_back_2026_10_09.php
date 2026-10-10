/**
 * Cave Johnson Couts's enhanced pair put back (Nathan, 9 October 2026, evening: "If the chains show Couts opens a real
 * original, say so plainly and put his pair back, noting that I ruled wrongly on incomplete evidence").
 * The morning ruling ("If his credential records text_to_image he is in that class ... Off now") was given on the overnight
 * scan's report that Couts alone among the seventeen carried the step. Read manifest by manifest on 9 October
 * (manifest_steps_2026_10_09.py --chains): his manifest's one ingredient is a JPEG with no credential of its own, related
 * parentOf, so a file brought in from outside the chain; the actions are opened (that file), then edited by Adobe Firefly
 * Image 5 with operation text_to_image and source type compositeWithTrainedAlgorithmicMedia. Fourteen enhanced portraits
 * still on records carry the same step from the same kind of root. The two files, the original #12 and the enhanced #27387,
 * were opened and compared by eye the same evening: the same daguerreotype, the edit lightening the vignette and the tones.
 * What this does: the enhanced asset his portrait again, enhancedFrom naming #12, #12 among his related images, the 9 October
 * removal sentence in the asset's source replaced by one saying it was taken off and put back, and the credential line
 * saying what the chain opens. Undoes couts_text_prompt_off_2026_10_09.php. Idempotent. Dry run by default; $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$MAN = "$root/storage/runtime/manifests/urn-c2pa-5a9a615a-55ca-4b8b-b52d-bb2d7a90070c-adobe";
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['record', 'the manifest the master points to, held in storage/runtime/manifests and read here again', 'the enhanced file', 'read'],
  ['file', 'the original as stored (#12), opened and compared by eye with the enhanced file on 9 October', "$root/web/uploads/archive-media/persons/cave-johnson-couts-portrait-us-army.jpg"],
  ['record', 'Couts\'s featuredImage and recordImages; the asset\'s filename, source, enhancedFrom and contentCredentials, the last three written here', 'the portrait file', 'read'],
]);
$m = file_get_contents($MAN);
$okChain = str_contains($m, 'parentOf') && str_contains($m, 'c2pa.opened') && str_contains($m, 'text_to_image') && !str_contains($m, 'activeManifest');
echo 'manifest: one ingredient, parentOf, no credential of its own; opened then an Image 5 edit: ' . ($okChain ? 'yes' : 'NO') . PHP_EOL;
if (!$okChain) { throw new \RuntimeException('the manifest does not read as an edit of an uploaded file; stopped'); }
$p = Entry::find()->id(323)->status(null)->one(); $a = Asset::find()->id(27387)->one(); $o = Asset::find()->id(12)->one();
if ($p?->title !== 'Cave Johnson Couts' || !$a || $o?->filename !== 'cave-johnson-couts-portrait-us-army.jpg') { throw new \RuntimeException('#323, #27387 or #12 not as expected'); }
$src = (string)$a->getFieldValue('source');
$newSrc = trim(preg_replace('/\s*Taken off every record on 9 October 2026:.*$/s', '', $src));
/* The note's first wording named "its manifest", which check_note_wording bars from public notes; reworded the same evening. */
$newSrc = str_replace(' Off its record on 9 October 2026 on a ruling made before its manifest was read, and back the same day on Nathan\'s word: the chain opens an uploaded photograph and the Firefly edit acts on it.', '', $newSrc);
if (!str_contains($newSrc, 'back the same day on Nathan\'s word: its content credential shows')) $newSrc .= ' Off its record on 9 October 2026 on a ruling made before its content credential was read in full, and back the same day on Nathan\'s word: its content credential shows the edit made to an uploaded photograph.';
if (mb_strlen($newSrc) > 500) { throw new \RuntimeException('source over 500 characters: ' . mb_strlen($newSrc)); }
$CC = "Adobe content credential (C2PA), https://cai-manifests.adobe.com/manifests/urn-c2pa-5a9a615a-55ca-4b8b-b52d-bb2d7a90070c-adobe\nSteps the manifest records (read field by field 9 October 2026): opened a JPEG brought in from outside the chain (its one ingredient, parentOf, no credential of its own), then edited by Adobe Firefly Image 5, operation text_to_image, source type compositeWithTrainedAlgorithmicMedia, 2026-10-01 08:20 UTC.\nEvery Firefly Image 5 edit in the archive records its operation as text_to_image (inventory/review/text-to-image-step-2026-10-09.md).";
$feat = $p->featuredImage->status(null)->ids(); $imgs = $p->recordImages->status(null)->ids();
$done = $feat === [27387] && in_array(12, $imgs) && $a->getFieldValue('enhancedFrom')->ids() === [12] && $newSrc === $src && (string)$a->contentCredentials === $CC;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . "#323 portrait " . json_encode($feat) . ' -> [27387]; related ' . json_encode($imgs) . ' -> with #12' . PHP_EOL . "#27387 source: $newSrc" . PHP_EOL . ($done ? 'done already' : '') . PHP_EOL;
if ($APPLY && !$done) {
  $a->setFieldValues(['source' => $newSrc, 'contentCredentials' => $CC, 'enhancedFrom' => [12]]);
  if (!$el->saveElement($a)) { throw new \RuntimeException('#27387 ' . json_encode($a->getFirstErrors())); } $n++;
  $p->setFieldValue('featuredImage', [27387]);
  $p->setFieldValue('recordImages', array_values(array_unique(array_merge([12], array_diff($imgs, [27387])))));
  if (!$el->saveElement($p)) { throw new \RuntimeException('#323 ' . json_encode($p->getFirstErrors())); } $n++;
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('couts_pair_back_2026_10_09.php', $n, 'verified', 'Couts\'s enhanced pair put back: the chain opens an uploaded photograph'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
