/**
 * Six marks handed over on 4 October 2026 and left unimported until now (lasd.png added on Nathan's word that the copy in incoming is the Sheriff's star) (Nathan: "The Hart High and
 * Canyon High logos are not showing on the site"; earlier the same day: the Hart High, City, LA County and
 * LASD marks on their records, City seal #41 kept as superseded; canyon-high-logo.png "from the school's
 * site, retrieved 4 October 2026"; CSUN_Seal.png "for the CSUN record").
 * Each becomes an asset in archiveMedia/marks/, set as currentMark on its record, the way the district marks
 * and Castaic High's were: assetRole current-mark, provenanceKind outside, licence identifying-use, rights
 * held by the body. Where the page a file came from is not recorded, the source says so. Asset #41 (the old
 * City seal PNG) is left as it is: no record uses it, and it keeps its rights holder.
 * Added the same day: the seals of the United States Congress, the State Assembly and the State Senate (Nathan:
 * "All as currentMark, labelled Seal, supplied to the archive since no source page is recorded"). The Assembly's is
 * a WebP, which the volume accepts and GD and Imagick both read, so it is kept as handed over.
 * Idempotent: matched by filename. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_marks_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $bad = [];
$from = 'from its website, retrieved 4 October 2026 (Nathan Imhoff); the page it was taken from is not recorded with the file.';
$MARKS = [
    ['hart-high.svg', 'hart-high-school-logo.svg', 16052, 'Hart High School', 'Logo of Hart High School', "Hart High School's logo, $from"],
    ['canyon-high-logo.png', 'canyon-high-school-logo.png', 21779, 'Canyon High School', 'Logo of Canyon High School', "Canyon High School's logo, $from"],
    ['CSUN_Seal.png', 'csun-seal.png', 16347, 'California State University, Northridge', 'Seal of California State University, Northridge', 'The seal of California State University, Northridge, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file.'],
    ['lasd.png', 'lasd-star.png', 29282, 'Los Angeles County Sheriff\'s Department', 'Star of the Los Angeles County Sheriff\'s Department', "The Sheriff's Department's star, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file."],
    ['la-county-seal.svg', 'los-angeles-county-seal.svg', 29279, 'County of Los Angeles', 'Seal of the County of Los Angeles', 'The seal of the County of Los Angeles, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file.'],
    ['city-of-santa-clarita.svg', 'city-of-santa-clarita-seal.svg', 394, 'City of Santa Clarita', 'Seal of the City of Santa Clarita', 'The seal of the City of Santa Clarita, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. It replaces the earlier PNG (asset #41) as the City\'s mark.'],
    ['Seal_of_the_United_States_Congress.svg', 'united-states-congress-seal.svg', 29393, 'United States Congress', 'Seal of the United States Congress', 'The seal of the United States Congress, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file.'],
    ['california-assembly.webp', 'california-state-assembly-seal.webp', 28271, 'California State Assembly', 'Seal of the California State Assembly', 'The seal of the California State Assembly, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file.'],
    ['california-state-senate.png', 'california-state-senate-seal.png', 28273, 'California State Senate', 'Seal of the California State Senate', 'The seal of the California State Senate, supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file.'],
];
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'marks/']);
foreach ($MARKS as [$in, $fn, $id, $holder, $title, $src]) {
    $file = is_file("$root/inventory/incoming/$in") ? "$root/inventory/incoming/$in" : "$root/inventory/incoming/done/$in"; $rec = Entry::find()->id($id)->status(null)->one(); $has = Asset::find()->filename($fn)->one();
    if (!is_file($file)) { $bad[] = "$in missing"; }
    if (!$rec || !$rec->getFieldLayout()->getFieldByHandle('currentMark')) { $bad[] = "#$id has no currentMark slot"; }
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
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('import_marks_2026_10_04.php', $n, 'verified', 'Marks: Hart High, Canyon High, CSUN, LASD, Congress, Assembly, Senate, County of Los Angeles, City of Santa Clarita');
echo implode(PHP_EOL, $done) . PHP_EOL . "done: $n writes" . PHP_EOL;
