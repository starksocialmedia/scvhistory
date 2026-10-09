/**
 * Bill Cooper's portrait off his record under the rule that no generated image of a real person is anywhere in the archive
 * (docs/DATA-MODEL.md). The file set on 8 October as "the photograph beside his biography" on his 2026 campaign site
 * (originals_portraits_2026_10_08.php, asset #38464) carries an embedded C2PA manifest, read from the file by
 * scan_credentials_2026_10_08.py on 9 October: c2pa.created 2026-06-12 by softwareAgent "gpt-image" version 2.0, digital
 * source type trainedAlgorithmicMedia, signed by OpenAI, then c2pa.converted and c2pa.watermarked; no ingredient, so no
 * original photograph in the chain. A generated image, not an edit. It was set on the strength of where it was published,
 * without the file being read (ERRORLOG, 9 October).
 * The asset stays (not deleted) with what its credential says; the record's portrait is emptied. Nothing else changes.
 * Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$M = "$root/inventory/incoming/done/originals-2026-10-08/bill-cooper-campaign-2026.png";
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the downloaded file, its embedded C2PA manifest read here again', $M],
  ['record', 'Cooper\'s featuredImage; the asset\'s contentCredentials (written here)', 'the portrait file', 'read'],
]);
$b = file_get_contents($M);
$gen = str_contains($b, 'gpt-image') && str_contains($b, 'digitalsourcetype/trainedAlgorithmicMedia') && str_contains($b, 'c2pa.created') && !str_contains($b, 'ingredient');
if (!$gen) { throw new \RuntimeException('the manifest does not read as generated; stopped'); }
$p = Entry::find()->id(26946)->status(null)->one(); $a = Asset::find()->id(38464)->one();
if ($p?->title !== 'Bill Cooper' || !$a) { throw new \RuntimeException('#26946 or #38464 not as expected'); }
$CC = 'C2PA manifest embedded in the file (read 9 October 2026): created 2026-06-12 by OpenAI gpt-image 2.0, digital source type trainedAlgorithmicMedia (generated), then converted and watermarked; signed by OpenAI; no ingredient, so no original photograph in the chain. A generated image of a real person: not shown on any record (docs/DATA-MODEL.md).';
$feat = $p->featuredImage->status(null)->ids();
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . "manifest reads as generated: yes" . PHP_EOL;
echo "#26946 Bill Cooper: portrait " . json_encode($feat) . (in_array(38464, $feat) ? ' -> empty' : ' (not #38464, left)') . PHP_EOL;
echo "#38464 contentCredentials: " . ((string)$a->contentCredentials === $CC ? 'set already' : 'set') . PHP_EOL;
if (!$APPLY) { echo 'nothing written' . PHP_EOL; return; }
if (in_array(38464, $feat)) { $p->setFieldValue('featuredImage', array_values(array_diff($feat, [38464]))); if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); } $n++; }
if ((string)$a->contentCredentials !== $CC) { $a->setFieldValue('contentCredentials', $CC); if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++; }
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('cooper_generated_off_2026_10_09.php', $n, 'verified', 'Bill Cooper\'s generated campaign image off his record; its credential recorded');
echo "done: $n" . PHP_EOL;
