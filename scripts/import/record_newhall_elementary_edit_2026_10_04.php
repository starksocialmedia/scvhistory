/**
 * The edit on Newhall Elementary School's mark, missed on import (import_school_marks_2026_10_04.php). newhall-elementary.png
 * carries an Adobe content credential embedded as a PNG chunk, which exiftool did not report and scan_content_credentials.py
 * found only as bytes: created in Adobe Photoshop 27.6.0 on 4 October 2026 at 21:13 UTC and edited with Adobe Firefly, digital
 * source type compositeWithTrainedAlgorithmicMedia. It entered on Nathan's word; the edit is recorded here the way it was
 * on the Newhall School District mark (water_chain_and_edited_marks_2026_10_04.php). newhall-elementary.jpeg, handed over
 * with it, is the same art at 407 by 491 with no credential, probably the file it was made from; it is not imported.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_newhall_elementary_edit_2026_10_04.php'))"
 */
use craft\elements\Asset;
$APPLY = false;
$el = Craft::$app->getElements(); $FN = 'newhall-elementary-school-logo.png'; $URN = 'urn:c2pa:1918e031-e68b-4f24-9d6f-a26cad626f5e:adobe';
$CC = "Adobe content credential (C2PA), https://cai-manifests.adobe.com/manifests/" . str_replace(':', '-', $URN) . "\nSteps recorded (UTC): 2026-10-04 21:13 created (Adobe Photoshop 27.6.0); edited with Adobe Firefly, digital source type compositeWithTrainedAlgorithmicMedia (composite with trained algorithmic media).";
$SRC = "Newhall Elementary School's logo, the N with \"Eagles\", supplied by Nathan Imhoff on 4 October 2026 after editing; where it was taken from is not recorded with the file. A 407 by 491 pixel JPEG of the same art, with no content credential, was handed over with it and is probably the unedited original; it is not held.";
$a = Asset::find()->filename($FN)->one();
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ": $FN " . ($a ? "#{$a->id}, credential " . (trim((string)$a->contentCredentials) === '' ? 'not recorded' : 'recorded') : 'missing') . PHP_EOL;
if (!$APPLY || !$a) { return; }
$n = 0;
if (trim((string)$a->contentCredentials) === '') {
    $a->setFieldValues(['enhancementMethod' => 'Edited in Adobe Photoshop 27.6.0 with Adobe Firefly (generative; the credential records a composite with trained algorithmic media)', 'enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => '2026-10-04', 'contentCredentials' => $CC, 'source' => $SRC]);
    if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++;
}
$a = Asset::find()->id($a->id)->one(); if (!str_contains((string)$a->contentCredentials, '1918e031')) { throw new \RuntimeException('not read back'); }
$root = \Craft::getAlias('@root'); $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('record_newhall_elementary_edit_2026_10_04.php', $n, 'verified', 'Newhall Elementary mark: its Firefly edit recorded');
echo "done: $n writes" . PHP_EOL;
