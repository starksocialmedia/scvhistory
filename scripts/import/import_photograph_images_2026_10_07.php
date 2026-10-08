/**
 * The photograph images (Nathan, 7 October 2026: "Import: go. 1,413 from Reggie, 5 from Archive.org captures"). Plan and
 * counts: inventory/review/photo-import-plan-2026-10-07.md; the per-file plan storage/runtime/photo-import/plan.json, written
 * by plan.py in the container from the census (inventory/review/photo-images-census-2026-10-07.json) and Reggie.
 *
 * Web copies on the server, masters on Reggie: every new file is a web copy made by make_copies.py (2,400 pixels on the long
 * side, a long strip by pixel count, quality 82; a PDF as it is, with its first page drawn as a cover), and every asset
 * records its master: legacySourcePath (the path on the legacy site, as on Reggie) and sourceChecksum (the master's sha256
 * from the drive manifest). Where the file is already an asset in the volume (1,306, mostly the magnifier's targets, now web
 * copies themselves) that asset is linked, not duplicated.
 *
 * Per record: the first picture is featuredImage; a flipbook's further pages go to recordImages in page order; a PDF goes to
 * recordDocuments with its drawn cover as featuredImage. A record that already has a featuredImage is left alone.
 * #4435's two pictures come from Internet Archive captures (Reggie holds only the thumbnails); its assets say so.
 * Held, not imported: 15 records whose census pick does not carry the record's code (plan.json "held"), for a read by hand.
 *
 * Idempotent: an asset is found by filename in legacy/ before one is made. Dry run by default. Set $APPLY = true.
 * Optional $LIMIT for a trial on the first N records.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_photograph_images_2026_10_07.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false; $LIMIT = 0;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $NEW = "$root/storage/runtime/photo-import/new";
$plan = json_decode(file_get_contents("$root/storage/runtime/photo-import/plan.json"), true);
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
$WB = ['lw2724a.jpg' => 'https://web.archive.org/web/20160810173209/http://www.scvhistory.com/gif/lw2724a.jpg', 'lw2724b.jpg' => 'https://web.archive.org/web/20160810233435/http://www.scvhistory.com/gif/lw2724b.jpg'];
$c = ['records' => 0, 'has an image already' => 0, 'not a photograph' => 0, 'link existing assets' => 0, 'new assets' => 0, 'missing web copy' => 0, 'records written' => 0];
$missing = [];
$assetFor = function (array $it, string $file, string $role, string $legacyUrl, ?string $coverOf = null) use ($APPLY, $el, $vol, $folder, $NEW, $WB, &$c, &$missing) {
  $a = Asset::find()->volumeId($vol->id)->folderId($folder->id)->filename($file)->one();
  if ($a) { if (!$coverOf && $it['reuse']) { $c['link existing assets']++; } return $a; }
  if (!is_file("$NEW/$file")) { $c['missing web copy']++; $missing[] = $file; return null; }
  $c['new assets']++;
  if (!$APPLY) { return null; }
  $rel = ltrim($it['legacySourcePath'], '/');
  if ($coverOf) { $src = "SCVHistory.com, $rel, as published on the original site with $legacyUrl: its first page, drawn from the PDF."; }
  elseif (isset($WB[$file])) { $src = "SCVHistory.com, gif/$file, as published on the original site with $legacyUrl, from the Internet Archive's capture (the file is not in our copy of the site)."; }
  elseif ($role === 'document') { $src = "SCVHistory.com, $rel, as published on the original site with $legacyUrl."; }
  else { $src = "SCVHistory.com, $rel, as published on the original site: " . ($role === 'first' ? 'the image of ' : 'a page of ') . "$legacyUrl."; }
  $tmp = sys_get_temp_dir() . '/' . $file; copy("$NEW/$file", $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($file); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id);
  $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
  $v = ['provenanceKind' => isset($WB[$file]) ? 'outside' : 'legacy-mirror', 'legacySourcePath' => $it['legacySourcePath'], 'acquiredDate' => '2026-10-07', 'source' => $src];
  if (isset($WB[$file])) { $v['sourceUrl'] = $WB[$file]; $v['sourceChecksum'] = 'sha256:' . hash_file('sha256', $it['src']); }
  elseif (!empty($it['sha256'])) { $v['sourceChecksum'] = 'sha256:' . $it['sha256']; }
  $h = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($h)));
  if (!$el->saveElement($a)) { throw new \RuntimeException("$file " . json_encode($a->getFirstErrors())); }
  return $a;
};
$k = 0;
foreach ($plan['records'] as $p) {
  if ($LIMIT && $k >= $LIMIT) { break; } $k++; $c['records']++;
  $e = Entry::find()->id($p['id'])->status(null)->one();
  if (!$e || $e->getSection()->handle !== 'photographs') { $c['not a photograph']++; echo "#{$p['id']} not a photograph now: left" . PHP_EOL; continue; }
  if ($e->featuredImage->status(null)->exists()) { $c['has an image already']++; continue; }
  $feat = null; $pages = []; $docs = []; $first = true; $ok = true;
  foreach ($p['items'] as $it) {
    if ($it['kind'] === 'pdf') {
      $pdf = $assetFor($it, $it['web'], 'document', $p['legacyUrl']); $pdf ? $docs[] = $pdf->id : $ok = $APPLY ? false : $ok;
      if ($it['role'] !== 'document' && $first) { $cov = preg_replace('/\.pdf$/i', '', $it['web']) . '-p1.jpg';
        $ca = $assetFor($it, $cov, 'first', $p['legacyUrl'], $it['web']); if ($ca) { $feat = $ca->id; } $first = false; }
      continue;
    }
    $a = $assetFor($it, $it['web'], $first ? 'first' : 'page', $p['legacyUrl']);
    if (!$a) { if ($APPLY) { $ok = false; } $first = false; continue; }
    if ($first) { $feat = $a->id; } else { $pages[] = $a->id; } $first = false;
  }
  if (!$APPLY) { continue; }
  if (!$ok || !$feat) { echo "#{$p['id']}: a file is missing, record left" . PHP_EOL; continue; }
  $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
  $e->setFieldValue('featuredImage', [$feat]);
  if ($pages) { $e->setFieldValue('recordImages', array_values(array_unique(array_merge($e->recordImages->status(null)->ids(), $pages)))); }
  if ($docs && in_array('recordDocuments', $h, true)) { $e->setFieldValue('recordDocuments', array_values(array_unique(array_merge($e->recordDocuments->status(null)->ids(), $docs)))); }
  if (!$el->saveElement($e)) { throw new \RuntimeException("#{$p['id']} " . json_encode($e->getFirstErrors())); }
  $c['records written']++;
  if ($c['records written'] % 100 === 0) { echo $c['records written'] . ' records written' . PHP_EOL; }
}
foreach ($c as $kk => $v) { echo str_pad($kk, 22) . $v . PHP_EOL; }
echo 'held for a read by hand: ' . count($plan['held']) . PHP_EOL;
if ($missing) { echo 'missing web copies (first 10): ' . implode(', ', array_slice($missing, 0, 10)) . PHP_EOL; }
if ($APPLY && $c['records written']) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('import_photograph_images_2026_10_07.php', $c['records written'], 'verified', "{$c['records written']} photograph records given their images; {$c['new assets']} new web-copy assets"); }
