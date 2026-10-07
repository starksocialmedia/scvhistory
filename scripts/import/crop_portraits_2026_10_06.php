/**
 * Three portraits cropped from group photographs (Nathan, 6 October 2026: "The three crops: yes under the edited-image rule,
 * original kept and linked"). From the portrait search of 6 October (inventory/review/portrait-search-2026-10-06.md).
 * Each original is taken from the original site's files (the Reggie drive) and imported as its own asset; the crop is a second
 * asset with enhancedFrom = the original, enhancedBy, enhancedDate and enhancementMethod saying it is a crop and nothing else;
 * the crop becomes the portrait and the original goes on the record as a related image. The person page then says "Cropped
 * from the original" with a link (templates/persons/_entry.twig).
 *  - Michele R. Jenkins (#30233): CO1501c, "Doreetha Daniels and COC Board President Michele Jenkins | Photo by Jesse Munoz/COC"
 *    (gif/galleries/co1501/images/co1501c.jpg, 5 June 2015); she is at right.
 *  - Tom Frew IV (#18783): HS9019, "Former SCV Historical Society President Tom Frew (third from right) receives a key to the city"
 *    (gif/hs9019.jpg, April 8, 2003); third from right, white-haired, in glasses.
 *  - John Boston (#2576): sg030506b-honby, "Thirty-eight years later, John Boston views a 1968 photo of the Honby Men's Club"
 *    (gif/sg030506b-honby.jpg, March 5, 2006); a photo illustration by Bryan Kneiding, so the original is itself a composite.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/crop_portraits_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0; $IN = "$root/inventory/incoming";
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
$import = function ($file, $title, $alt, $vals) use ($IN, $vol, $folder, $el) {
  $tmp = sys_get_temp_dir() . '/' . $file; copy("$IN/$file", $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($file); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
  if (!$el->saveElement($a)) { throw new \RuntimeException("$file " . json_encode($a->getFirstErrors())); }
  $a = Asset::find()->id($a->id)->one(); $a->title = $title; $a->alt = $alt;
  $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($vals, array_flip($ah)));
  if (!$el->saveElement($a)) { throw new \RuntimeException("$file " . json_encode($a->getFirstErrors())); }
  return $a;
};
$C = [
  [30233, 'Michele R. Jenkins', 'co1501c.jpg', 'gif/galleries/co1501/images/co1501c.jpg', 'CO1501c', 'Doreetha Daniels and COC Board President Michele Jenkins, College of the Canyons commencement, June 5, 2015. Photo by Jesse Munoz/COC.', 'michele-jenkins-cropped-from-co1501c.jpg', 'her, at right, from the two-person photograph'],
  [18783, 'Tom Frew IV', 'hs9019.jpg', 'gif/hs9019.jpg', 'HS9019', 'April 8, 2003: former SCV Historical Society President Tom Frew (third from right) receives a key to the city.', 'tom-frew-iv-cropped-from-hs9019.jpg', 'him, third from right, from the group photograph'],
  [2576, 'John Boston', 'sg030506b-honby.jpg', 'gif/sg030506b-honby.jpg', '', 'March 5, 2006: thirty-eight years later, John Boston views a 1968 photo of the Honby Men\'s Club. Photo illustration by Bryan Kneiding.', 'john-boston-cropped-from-sg030506b-honby.jpg', 'him, at left, from the photo illustration (itself a composite)'],
];
foreach ($C as [$pid, $name, $orig, $path, $code, $cap, $crop, $what]) {
  $p = Entry::find()->id($pid)->status(null)->one(); if ($p?->title !== $name) { throw new \RuntimeException("#$pid is not $name"); }
  $o = Asset::find()->filename($orig)->one(); $c = Asset::find()->filename($crop)->one();
  echo "#$pid $name: original $orig " . ($o ? "#{$o->id}" : 'import') . "; crop $crop " . ($c ? "#{$c->id}" : 'import') . "; portrait " . ($p->featuredImage->one()?->id ?? 'none') . PHP_EOL;
  if (!$APPLY) { continue; }
  if (!$o) { $o = $import($orig, $name . ($code ? ", $code" : ', 2006'), $cap, ['provenanceKind' => 'legacy-mirror', 'acquiredDate' => '2026-10-06', 'license' => 'unknown', 'rightsNote' => 'No permission to republish is established.',
    'legacySourcePath' => $path, 'sourceChecksum' => 'sha256:' . hash_file('sha256', "$IN/$orig"), 'photoSourceCode' => $code, 'photoCaptionExt' => $cap,
    'source' => "SCVHistory.com, $path. Taken from the original site's files on 6 October 2026."]); $n++; }
  if (!$c) { $c = $import($crop, "$name, cropped", "Portrait of $name, cropped from a group photograph", ['enhancedFrom' => [$o->id], 'enhancedBy' => 'Claude Code, on Nathan Imhoff\'s word', 'enhancedDate' => '2026-10-06',
    'enhancementMethod' => "Cropped to $what; nothing else changed.", 'photoCaptionExt' => "A crop of the original photograph, which the archive holds and links here. $cap", 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-06',
    'license' => 'unknown', 'rightsNote' => 'No permission to republish is established; the rights are the original photograph\'s.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', "$IN/$crop"),
    'source' => "Cropped on October 6, 2026 from the original, $path, held in the archive."]); $n++; }
  $imgs = $p->recordImages->status(null)->ids(); $v = [];
  if (!in_array($o->id, $imgs, true)) { $v['recordImages'] = array_merge($imgs, [$o->id]); }
  if ($p->featuredImage->one()?->id !== $c->id) { $v['featuredImage'] = [$c->id]; }
  if ($v) { $p->setFieldValues($v); if (!$el->saveElement($p)) { throw new \RuntimeException("#$pid " . json_encode($p->getFirstErrors())); } $n++; }
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('crop_portraits_2026_10_06.php', $n, 'verified', 'three portraits cropped from group photographs, originals kept and linked'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
