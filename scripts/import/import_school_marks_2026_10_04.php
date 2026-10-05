/**
 * Two school marks held up by a Terminal permission problem and handed over after a restart (Nathan, 4 October 2026:
 * "SCCS.png, Santa Clarita Christian School's mark; newhall-elementary.png, Newhall Elementary's mark ... Import the
 * two marks").
 * Each becomes an asset in archiveMedia/marks/, set as currentMark on its record, the way the marks imported earlier
 * the same day were: assetRole current-mark, provenanceKind outside, licence identifying-use, rights held by the school.
 * Neither carries content credentials. SCCS.png's own metadata says it was made in Adobe Photoshop in July 2017.
 * newhall-elementary.jpeg, handed over with them, is the same Newhall Elementary art at 407 by 491 pixels on white,
 * not a photograph; it is not imported (inventory/incoming/OUTSTANDING.md).
 * Idempotent: matched by filename. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_school_marks_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $bad = [];
$MARKS = [
    ['SCCS.png', 'santa-clarita-christian-school-logo.png', 21783, 'Santa Clarita Christian School', 'Logo of Santa Clarita Christian School', "Santa Clarita Christian School's logo, the C with the Cardinal's head, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. Its embedded metadata records it as made in Adobe Photoshop in July 2017."],
    ['newhall-elementary.png', 'newhall-elementary-school-logo.png', 15958, 'Newhall Elementary School', 'Logo of Newhall Elementary School', "Newhall Elementary School's logo, the N with \"Eagles\", supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. A 407 by 491 pixel JPEG of the same art was handed over with it."],
];
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'marks/']);
foreach ($MARKS as [$in, $fn, $id, $holder, $title, $src]) {
    $file = is_file("$root/inventory/incoming/$in") ? "$root/inventory/incoming/$in" : "$root/inventory/incoming/done/$in"; $rec = Entry::find()->id($id)->status(null)->one(); $has = Asset::find()->filename($fn)->one();
    if (!is_file($file)) { $bad[] = "$in missing"; }
    if (!$rec || !$rec->getFieldLayout()->getFieldByHandle('currentMark')) { $bad[] = "#$id has no currentMark slot"; }
    if ($rec && $rec->title !== $holder) { $bad[] = "#$id is {$rec->title}, not $holder"; }
    echo "$in -> $fn on #$id " . ($rec?->title ?? '?') . ': ' . ($has ? "asset #{$has->id} exists" : 'import') . '; record mark now: ' . ($rec?->currentMark->one()?->filename ?? 'none') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $done = [];
foreach ($MARKS as [$in, $fn, $id, $holder, $title, $src]) {
    $file = is_file("$root/inventory/incoming/$in") ? "$root/inventory/incoming/$in" : "$root/inventory/incoming/done/$in"; $rec = Entry::find()->id($id)->status(null)->one(); $has = Asset::find()->filename($fn)->one();
    if (!$has) {
        $tmp = sys_get_temp_dir() . "/$fn"; copy($file, $tmp);
        $has = new Asset(); $has->tempFilePath = $tmp; $has->setFilename($fn); $has->newFolderId = $folder->id; $has->setVolumeId($volume->id); $has->setScenario(Asset::SCENARIO_CREATE); $has->avoidFilenameConflicts = false;
        if (!$el->saveElement($has)) { throw new \RuntimeException("$fn: " . json_encode($has->getFirstErrors())); }
        $has = Asset::find()->id($has->id)->one(); $has->title = $title; $has->alt = $title;
        $v = ['assetRole' => 'current-mark', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'license' => 'identifying-use', 'rightsHolder' => $holder, 'source' => $src,
            'rightsNote' => "$holder's own mark. The archive shows it only to identify the body on its own record, as a reference work does; it implies no endorsement.", 'sourceChecksum' => 'sha256:' . hash_file('sha256', $file)];
        $ah = array_map(fn($f) => $f->handle, $has->getFieldLayout()->getCustomFields()); $has->setFieldValues(array_intersect_key($v, array_flip($ah)));
        if (!$el->saveElement($has)) { throw new \RuntimeException("$fn fields: " . json_encode($has->getFirstErrors())); } $n++;
    }
    if ($rec->currentMark->one()?->id !== $has->id) { $rec->setFieldValue('currentMark', [$has->id]); if (!$el->saveElement($rec)) { throw new \RuntimeException("#$id currentMark"); } $n++; }
    $done[] = "$in -> #{$has->id} on #$id";
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('import_school_marks_2026_10_04.php', $n, 'verified', 'Marks: Santa Clarita Christian School, Newhall Elementary School');
echo implode(PHP_EOL, $done) . PHP_EOL . "done: $n writes" . PHP_EOL;
