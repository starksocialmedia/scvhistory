/**
 * The Smith and Trunkey portraits (#28210, #28197), imported 3 October 2026 with
 * source sentences in the older style: a received file name and a checksum.
 * simplify_media_provenance.php set the rule the same morning (Nathan: "Keep the
 * checksum in the data, not the sentence"), and check_render's provenance
 * wording check caught both. The checksum moves to sourceChecksum; the sentence
 * keeps where the file came from and what is not established.
 * Each stored sentence is exact; the script refuses if it is not as read.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_portrait_provenance_2026_10_03.php'))"
 */

use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$FIX = [
    28210 => ['sha256:8f28b40b78e78fad463c12adcb769353ad1ca40f78d030226b90dffd07af4bdc',
        'Received from Nathan Imhoff on 3 October 2026 as Christy_Smith_CA_Assembly_official_photo.jpg, SHA-256 8f28b40b78e78fad463c12adcb769353ad1ca40f78d030226b90dffd07af4bdc, a reduced copy of Wikimedia Commons, File:Christy Smith CA Assembly official photo.jpg (3082 x 4315, source https://a38.asmdc.org/biography). Embedded notice: Copyright: Jeff Walters. The stored copy is re-encoded on import.',
        'The California State Assembly\'s official portrait of 2018, from a reduced copy of the file on Wikimedia Commons ("Christy Smith CA Assembly official photo.jpg", taken from https://a38.asmdc.org/biography), supplied by Nathan Imhoff. The file names Jeff Walters as copyright holder.'],
    28197 => ['sha256:0e5ec9d6aca808d793697cfdd07a38bd57bb441e9aee401fc3e45945dba1de6f',
        'Campaign material. Received from Nathan Imhoff on 3 October 2026 as Trunkey-Chris-scaled.jpg, SHA-256 0e5ec9d6aca808d793697cfdd07a38bd57bb441e9aee401fc3e45945dba1de6f. Its embedded metadata: Artist "Jennifer Emery", Copyright "(c)Jennifer Emery", description "Christopher Trunkey Campaign", created 12 July 2016. The stored copy is re-encoded on import and differs from the file as received.',
        'Campaign material, supplied by Nathan Imhoff: a photograph by Jennifer Emery for his campaign, July 2016, as the file\'s own notice records. Permission to publish is not established.'],
];
$el = Craft::$app->getElements(); $n = 0; $bad = [];
foreach ($FIX as $id => [$sum, $old, $new]) {
    $a = Asset::find()->id($id)->one();
    if (!$a) { $bad[] = "#$id missing"; continue; }
    $h = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields());
    if (!in_array('sourceChecksum', $h)) { $bad[] = "#$id has no sourceChecksum field"; continue; }
    $cur = (string)$a->source;
    if ($cur === $new && (string)$a->sourceChecksum === $sum) { echo "#$id already done" . PHP_EOL; continue; }
    if ($cur !== $old) { $bad[] = "#$id source is not as read"; continue; }
    echo "#$id {$a->filename}: checksum to sourceChecksum; source -> $new" . PHP_EOL;
    if ($APPLY) { $a->setFieldValues(['source' => $new, 'sourceChecksum' => $sum]); if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
        $r = Asset::find()->id($id)->one(); if ((string)$r->source === $new && (string)$r->sourceChecksum === $sum) { $n++; } else { $bad[] = "#$id read-back short"; } }
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_portrait_provenance_2026_10_03.php', $n, $bad ? 'SHORT' : 'verified', 'Smith and Trunkey portraits: checksum to sourceChecksum, plain source sentences');
if ($bad) { throw new \RuntimeException(implode(' | ', $bad)); }
