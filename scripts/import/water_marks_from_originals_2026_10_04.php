/**
 * The former marks of the Newhall County Water District, the Castaic Lake Water Agency and the Valencia Water Company from
 * unedited originals (Nathan, 4 October 2026: "For the two historical marks, NCWD and Castaic Lake, find unedited originals if
 * they exist. A dissolved body's mark should be the mark as it was"; "Valencia-Water-Company.png ... its historical mark").
 * All three are rendered from the vector art in the 2008 Santa Clarita Valley Annual Water Quality Report, the joint report of
 * the valley's water suppliers, as CLWA posted it (Wayback Machine capture of 14 December 2009), with a transparent ground
 * (inventory/review/water-marks-2026-10-04.md; files and manifest in inventory/sources/water-marks-2026-10-04/). Valencia's
 * is its droplet-and-pinwheel symbol, used from at least 2002 to 2018; a later "VWC" lockup of about 2017 is noted in the source.
 * The Firefly-edited files supplied earlier (newhall-county-water-district-logo.png, castaic-lake-water-agency-logo.png) are
 * kept, with their edit recorded, titled as superseded and taken out of the records' related images; their role is cleared.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/water_marks_from_originals_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $D = "$root/inventory/sources/water-marks-2026-10-04";
$SRC = 'Rendered from the vector art on page 1 of the Santa Clarita Valley 2008 Annual Water Quality Report, the joint report of the Castaic Lake Water Agency, its Santa Clarita Water Division, the Newhall County Water District and the Valencia Water Company (made 5 June 2008), as posted by the Castaic Lake Water Agency and captured by the Wayback Machine on 14 December 2009; rendered with a transparent ground, the report\'s own caption under the mark left out. No edit beyond the crop.';
$M = [
    ['ncwd', 27534, 'newhall-county-water-district-logo-2008.png', 'Logo of the Newhall County Water District', '1953-01-13/2017-12-31', 'Newhall County Water District (its rights passed to the Santa Clarita Valley Water Agency)', 'newhall-county-water-district-logo.png'],
    ['clwa', 26563, 'castaic-lake-water-agency-logo-2008.png', 'Logo of the Castaic Lake Water Agency', '1970/2017-12-31', 'Castaic Lake Water Agency (its rights passed to the Santa Clarita Valley Water Agency)', 'castaic-lake-water-agency-logo.png'],
    ['vwc', null, 'valencia-water-company-logo-2008.png', 'Logo of the Valencia Water Company', '1954-04-07/2018-01', 'Valencia Water Company (its assets passed to the Santa Clarita Valley Water Agency)', null],
];
$V = Entry::find()->section('organizations')->status(null)->title('Valencia Water Company')->one(); $M[2][1] = $V?->id;
$bad = []; foreach ($M as [$k, $oid, $fn]) { if (!is_file("$D/$k-2008-wqr-transparent-2400dpi.png")) { $bad[] = "$k render missing"; } if (!$oid) { $bad[] = "$k: no record"; } }
foreach ($M as [$k, $oid, $fn, $t, , , $old]) { echo "$t: " . (Asset::find()->filename($fn)->exists() ? 'exists' : 'import') . ($old ? "; supersede $old" : '') . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0; $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'marks/']);
foreach ($M as [$k, $oid, $fn, $title, $edtf, $holder, $old]) {
    $path = "$D/$k-2008-wqr-transparent-2400dpi.png"; $a = Asset::find()->filename($fn)->one();
    if (!$a) { $tmp = sys_get_temp_dir() . "/$fn"; copy($path, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($fn); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } }
    if (($a->assetRole->value ?? '') !== 'former-mark') { $a = Asset::find()->id($a->id)->one(); $a->title = $title; $a->alt = $title;
        $v = ['assetRole' => 'former-mark', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'license' => 'identifying-use', 'rightsHolder' => $holder, 'dateEdtf' => $edtf, 'source' => $SRC,
            'sourceUrl' => 'https://web.archive.org/web/20091214202408/http://www.clwa.org/', 'rightsNote' => 'A dissolved body\'s own mark, shown only to identify the body on its own record, as a reference work does; it implies no endorsement.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $path)];
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
        if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++; }
    $o = Entry::find()->id($oid)->status(null)->one(); $ids = $o->recordImages->ids();
    $oldA = $old ? Asset::find()->filename($old)->one() : null;
    $want = array_values(array_unique(array_merge([$a->id], array_filter($ids, fn($i) => !$oldA || $i != $oldA->id))));
    if ($ids != $want) { $o->setFieldValue('recordImages', $want); if (!$el->saveElement($o)) { throw new \RuntimeException("#$oid"); } $n++; }
    if ($oldA && ($oldA->assetRole->value ?? '') === 'former-mark') { $oldA->title = $oldA->title . ' (edited copy, superseded 4 October 2026)';
        $oldA->setFieldValues(['assetRole' => '', 'source' => rtrim((string)$oldA->source) . ' Superseded on 4 October 2026 by an unedited original rendered from the 2008 Annual Water Quality Report (' . $fn . '); kept with its edit recorded.']);
        if (!$el->saveElement($oldA)) { throw new \RuntimeException('old ' . json_encode($oldA->getFirstErrors())); } $n++; }
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('water_marks_from_originals_2026_10_04.php', $n, 'verified', 'NCWD, CLWA and VWC former marks from the 2008 report\'s vector art; the Firefly-edited copies superseded');
echo "done: $n writes" . PHP_EOL;
