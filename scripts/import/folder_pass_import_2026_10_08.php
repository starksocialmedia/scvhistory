/**
 * The folder pass (Nathan, 8 October 2026: "Then the second pass, dry run first"). What each record's own folder on Reggie
 * holds and Craft does not, measured by walking the folders (folder_census_2026_10_08.py; inventory/review/
 * folder-census-2026-10-08.md), planned by folder_pass_plan_2026_10_08.py (storage/runtime/photo-import/plan2.json, with
 * the files held for Nathan and why), web copies by folder_pass_copies_2026_10_08.py (storage/runtime/photo-import/new2/).
 *
 * Adds, never replaces: pictures go after what the record holds (featuredImage only if it has none, then recordImages in
 * folder order); PDFs and office files go to recordDocuments, or documentFiles on a document. A PDF becomes the record's
 * picture through its drawn cover only when the record has no picture. Every asset records its master as the import did:
 * provenanceKind legacy-mirror, legacySourcePath, sourceChecksum from the drive manifest, acquiredDate, a source sentence.
 *
 * Idempotent: an asset is found by filename in legacy/ before one is made, and an id already on the record is not added
 * twice. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/folder_pass_import_2026_10_08.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $NEW = "$root/storage/runtime/photo-import/new2";
$plan = json_decode(file_get_contents("$root/storage/runtime/photo-import/plan2.json"), true);
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
$c = ['records' => 0, 'records gone' => 0, 'new assets' => 0, 'existing assets linked' => 0, 'missing web copy' => 0, 'pictures added' => 0, 'documents added' => 0, 'featured set' => 0, 'records written' => 0];
$missing = []; $report = [];
$assetFor = function (array $it, string $file, string $legacyUrl, bool $cover = false) use ($APPLY, $el, $vol, $folder, $NEW, &$c, &$missing) {
  $a = Asset::find()->volumeId($vol->id)->folderId($folder->id)->filename($file)->one();
  if ($a) { $c['existing assets linked']++; return $a; }
  if (!is_file("$NEW/$file")) { $c['missing web copy']++; $missing[] = $file; return null; }
  $c['new assets']++;
  if (!$APPLY) { return 'new'; }
  $rel = ltrim($it['legacySourcePath'], '/');
  $src = $cover ? "SCVHistory.com, $rel, from the record's own folder on the original site ($legacyUrl): its first page, drawn from the PDF."
                : "SCVHistory.com, $rel, from the record's own folder on the original site, published with $legacyUrl.";
  $tmp = sys_get_temp_dir() . '/' . $file; copy("$NEW/$file", $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($file); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id);
  $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
  $v = ['provenanceKind' => 'legacy-mirror', 'legacySourcePath' => $it['legacySourcePath'], 'acquiredDate' => '2026-10-08', 'source' => $src];
  if (!empty($it['sha256'])) { $v['sourceChecksum'] = 'sha256:' . $it['sha256']; }
  $h = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($h)));
  if (!$el->saveElement($a)) { throw new \RuntimeException("$file " . json_encode($a->getFirstErrors())); }
  return $a;
};
$idOf = fn($a) => $a instanceof Asset ? $a->id : null;
foreach ($plan['records'] as $p) {
  $c['records']++;
  $e = Entry::find()->id($p['id'])->status(null)->one();
  if (!$e) { $c['records gone']++; echo "#{$p['id']} not found: left" . PHP_EOL; continue; }
  $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
  $docField = in_array('documentFiles', $h, true) ? 'documentFiles' : 'recordDocuments';
  $hasFeat = $e->featuredImage->status(null)->exists();
  $imgs = $e->recordImages->status(null)->ids(); $docs = in_array($docField, $h, true) ? $e->getFieldValue($docField)->status(null)->ids() : [];
  $feat = null; $addI = 0; $addD = 0; $ok = true;
  foreach ($p['items'] as $it) {
    if ($it['kind'] !== 'image') {
      $a = $assetFor($it, $it['web'], $p['legacyUrl']); if (!$a) { $ok = false; continue; }
      $addD++; if ($id = $idOf($a)) { $docs[] = $id; }
      if ($it['kind'] === 'pdf' && !$hasFeat && !$feat) {
        $cv = $assetFor($it, preg_replace('/\.pdf$/i', '', $it['web']) . '-p1.jpg', $p['legacyUrl'], true);
        if ($cv) { $feat = $idOf($cv) ?? 'new'; }
      }
      continue;
    }
    $a = $assetFor($it, $it['web'], $p['legacyUrl']); if (!$a) { $ok = false; continue; }
    if (!$hasFeat && !$feat) { $feat = $idOf($a) ?? 'new'; } else { $addI++; if ($id = $idOf($a)) { $imgs[] = $id; } }
  }
  $c['pictures added'] += $addI + ($feat && !$hasFeat ? 1 : 0); $c['documents added'] += $addD; if ($feat) { $c['featured set']++; }
  $report[] = "#{$p['id']} {$e->getSection()->handle}: " . ($feat ? 'picture set, ' : '') . "$addI more pictures, $addD documents" . ($ok ? '' : ' (a web copy is missing: record would be left)');
  if (!$APPLY) { continue; }
  if (!$ok) { echo "#{$p['id']}: a web copy is missing, record left" . PHP_EOL; continue; }
  if ($feat) { $e->setFieldValue('featuredImage', [$feat]); }
  $e->setFieldValue('recordImages', array_values(array_unique($imgs)));
  if (in_array($docField, $h, true)) { $e->setFieldValue($docField, array_values(array_unique($docs))); }
  if (!$el->saveElement($e)) { throw new \RuntimeException("#{$p['id']} " . json_encode($e->getFirstErrors())); }
  $c['records written']++;
}
file_put_contents("$root/storage/runtime/photo-import/folder-pass-" . ($APPLY ? 'apply' : 'dry') . '.txt', implode(PHP_EOL, $report) . PHP_EOL);
foreach ($c as $kk => $v) { echo str_pad($kk, 24) . $v . PHP_EOL; }
echo 'held for Nathan: ' . count($plan['held']) . PHP_EOL;
if ($missing) { echo 'missing web copies (first 10): ' . implode(', ', array_slice($missing, 0, 10)) . PHP_EOL; }
if ($APPLY && $c['records written']) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('folder_pass_import_2026_10_08.php', $c['records written'], 'verified', "{$c['records written']} records given what their own folder holds; {$c['new assets']} new web-copy assets"); }
