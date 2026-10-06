/**
 * Pete Knight's portrait (Nathan, 6 October 2026: "Swap his portrait for pete-knight.jpg"). The file is Nathan's enhancement
 * (Adobe Firefly, 6 October 2026: text to image, then a Firefly save; its content credential in
 * inventory/review/enhanced-portraits-credentials-2026-10-06.json) of a photograph of him in a pressure suit beside an X-15; the
 * original is not in the folder, so enhancedFrom is empty and the caption says the original is not held. His portrait until now,
 * the 2004 Newsmaker of the Week photograph (sg042504.jpg, #31240), stays on his record as a related image (the rule for portrait
 * swaps). Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/knight_portrait_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$FILE = "$root/inventory/incoming/pete-knight.jpg"; $C = json_decode(file_get_contents("$root/inventory/review/enhanced-portraits-credentials-2026-10-06.json"), true)['pete-knight.jpg'];
$k = Entry::find()->id(29314)->status(null)->one(); $old = Asset::find()->id(31240)->one();
if ($k?->title !== 'Pete Knight' || $old?->filename !== 'sg042504.jpg' || !is_file($FILE)) { throw new \RuntimeException('not as expected'); }
$a = Asset::find()->filename('pete-knight.jpg')->one(); echo 'pete-knight.jpg: ' . ($a ? "exists #{$a->id}" : 'import (3552 x 4736)') . PHP_EOL;
if ($APPLY && !$a) {
  $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
  $tmp = sys_get_temp_dir() . '/pete-knight.jpg'; copy($FILE, $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename('pete-knight.jpg'); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
  $a = Asset::find()->id($a->id)->one(); $a->title = 'Pete Knight, with the X-15, enhanced'; $a->alt = 'Pete Knight in a pressure suit beside an X-15';
  $v = ['enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => '2026-10-06', 'enhancementMethod' => 'Adobe Firefly: regenerated in part from a text prompt (Firefly text to image). Generative steps can add detail the photograph did not record.',
    'contentCredentials' => $C['manifest'] . ' : ' . $C['says'], 'photoCaptionExt' => 'Pete Knight in a pressure suit beside an X-15. An enhanced version of a photograph whose original the archive does not hold.',
    'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-06', 'license' => 'unknown', 'rightsNote' => 'No permission to republish is established; the rights are the original photograph\'s.',
    'sourceChecksum' => 'sha256:' . hash_file('sha256', $FILE), 'source' => 'Enhanced by Nathan Imhoff in Adobe Firefly on October 6, 2026, and supplied by him the same day. Where the original photograph was published is not recorded with it.'];
  $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++;
}
$cur = $k->featuredImage->one(); echo 'portrait: ' . ($a && $cur?->id === $a->id ? 'set already' : 'swap, sg042504.jpg to related images') . PHP_EOL;
if ($APPLY && $cur?->id !== $a->id) { $imgs = $k->recordImages->status(null)->ids(); if (!in_array(31240, $imgs, true)) { $imgs[] = 31240; } $k->setFieldValues(['featuredImage' => [$a->id], 'recordImages' => $imgs]); if (!$el->saveElement($k)) { throw new \RuntimeException('#29314'); } $n++; }
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('knight_portrait_2026_10_06.php', $n, 'verified', 'Pete Knight: the X-15 portrait, enhanced; the Newsmaker photograph kept'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
