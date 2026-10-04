/**
 * Castaic High School: a record, and its current mark (Nathan, 4 October 2026: "Castaic-High.png is in
 * inventory/incoming, as currentMark on the Castaic High record"; the same treatment as the Hart High and
 * Canyon High marks: "the school's own current mark, from the school's site, retrieved 4 October 2026").
 * The archive held no record for the school, which the federal directory lists as a Hart district high
 * school, grades 9 to 12, first listed in 2018 (templates/_data/school-directory.json, NCES 064251014393).
 * The record is made as the other Hart high schools are: a school, high, under the district.
 * The mark: assetRole current-mark, provenanceKind outside, licence identifying-use, rights held by the school;
 * the page it came from is not recorded with the file. Never the featured image.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_castaic_high_and_mark_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $svc = Craft::$app->getEntries(); $bad = [];
$FILE = "$root/inventory/incoming/Castaic-High.png"; $FN = 'castaic-high-school-logo.png';
if (!is_file($FILE)) { $bad[] = 'the file is missing'; }
$sha = is_file($FILE) ? hash_file('sha256', $FILE) : '';
$dir = json_decode(file_get_contents("$root/templates/_data/school-directory.json"), true);
$row = array_values(array_filter($dir['districts']['0642510']['open'] ?? [], fn($s) => $s['nces'] === '064251014393'))[0] ?? null;
if (($row['name'] ?? '') !== 'Castaic High') { $bad[] = 'the directory does not list Castaic High under 064251014393'; }
$rec = Entry::find()->section('organizations')->status(null)->title('Castaic High School')->one();
$has = Asset::find()->filename($FN)->one();
echo 'Castaic High School: ' . ($rec ? "#{$rec->id} exists" : 'create (school, high, under the Hart district, NCES 064251014393)') . '; mark ' . ($has ? "#{$has->id} exists" : "import $FN") . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0;
if (!$rec) {
    $os = $svc->getSectionByHandle('organizations'); $rec = new Entry(); $rec->sectionId = $os->id; $rec->setTypeId($os->getEntryTypes()[0]->id); $rec->title = 'Castaic High School';
    $rec->setFieldValues(['orgType' => 'school', 'schoolLevel' => 'high', 'parentOrganization' => [21588], 'ncesId' => '064251014393', 'gradeSpan' => '9 to 12', 'recordProvenance' => 'create_castaic_high_and_mark_2026_10_04.php, 4 October 2026: from the federal school directory, for its mark']);
    if (!$el->saveElement($rec)) { throw new \RuntimeException('record: ' . json_encode($rec->getFirstErrors())); } $n++;
}
if (!$has) {
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'marks/']);
    $tmp = sys_get_temp_dir() . "/$FN"; copy($FILE, $tmp);
    $has = new Asset(); $has->tempFilePath = $tmp; $has->setFilename($FN); $has->newFolderId = $folder->id; $has->setVolumeId($volume->id); $has->setScenario(Asset::SCENARIO_CREATE); $has->avoidFilenameConflicts = false;
    if (!$el->saveElement($has)) { throw new \RuntimeException('asset: ' . json_encode($has->getFirstErrors())); }
    $has = Asset::find()->id($has->id)->one(); $has->title = 'Logo of Castaic High School'; $has->alt = 'Logo of Castaic High School';
    $v = ['assetRole' => 'current-mark', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'license' => 'identifying-use', 'rightsHolder' => 'Castaic High School',
        'source' => 'Castaic High School\'s logo, from the school\'s website, retrieved 4 October 2026 (Nathan Imhoff); the page it was taken from is not recorded with the file.',
        'rightsNote' => 'The school\'s own mark. The archive shows it only to identify the school on the school\'s own record, as a reference work does; it implies no endorsement.', 'sourceChecksum' => 'sha256:' . $sha];
    $ah = array_map(fn($f) => $f->handle, $has->getFieldLayout()->getCustomFields()); $has->setFieldValues(array_intersect_key($v, array_flip($ah)));
    if (!$el->saveElement($has)) { throw new \RuntimeException('asset fields: ' . json_encode($has->getFirstErrors())); } $n++;
}
if ($rec->currentMark->one()?->id !== $has->id) { $rec->setFieldValue('currentMark', [$has->id]); if (!$el->saveElement($rec)) { throw new \RuntimeException('currentMark'); } $n++; }
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_castaic_high_and_mark_2026_10_04.php', $n, 'verified', 'Castaic High School record and its current mark');
echo "done: $n writes" . PHP_EOL;
