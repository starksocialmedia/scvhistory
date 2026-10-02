/**
 * Laurene Weste's portrait (Nathan, 2 October 2026: "Laurene Weste has no
 * portrait. Tell me whether a file exists for her").
 *
 * No file was supplied: Weste.jpg in inventory/incoming is her banner. But the
 * legacy site holds her City Council portrait, SC1311 (2013, "19200 dpi jpeg"),
 * on a page that was never imported. This imports the page as a photograph
 * record, its caption Leon Worden's note and the City's 2013 biography as the
 * page gives them, with her birth date cut to the year; sets the scan as her
 * portrait; and records the year, 1948, from Leon's note. Both files match the
 * mirror's manifest of 20 August 2026.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_weste_portrait.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$ID = 15929; $SCAN = "$root/inventory/legacy/fetched/sc1311.jpg"; $SHA = 'c6533edf0cb14efc66101bba16ac811846ddab38a89715f29691890c3583b991';
$HTM = "$root/inventory/legacy/fetched/sc1311.htm"; $HTM_SHA = '488298ece87335d11dc3336105fe16a4cce309596970f948e15b1d19cfec331e';
$txt = $ws(@file_get_contents("$root/inventory/legacy/fetched/sc1311.txt"));
$p = Entry::find()->id($ID)->status(null)->one();
$bad = [];
if (!$p || $p->title !== 'Laurene Weste') { $bad[] = '#15929 is not Weste'; }
if (!is_file($SCAN) || hash_file('sha256', $SCAN) !== $SHA || !is_file($HTM) || hash_file('sha256', $HTM) !== $HTM_SHA) { $bad[] = 'SC1311 files missing or not the mirror\'s'; }
$CAPTION = [
    'Laurene Weste (b. 1948). Resident of Placerita Canyon/Newhall. Santa Clarita City Council member, 1998 to present. Mayor in 2001, 2006, 2010, 2014.',
    '[City of Santa Clarita 2013] As a prior Commissioner for the City Parks and Recreation Commission, Laurene has overseen the establishment of numerous parks, the preservation of thousands of acres of open space, and the construction of a cross-town trail system that is widely heralded as a crowning achievement of our young City.',
    'As a Santa Monica Mountains Conservancy Advisory Board Member, Laurene spearheaded the drive to save historic 800-acre Mentryville, which is the first oil town in California, for future generations.',
    'As Director of the Santa Clarita Valley Committee on Aging, Laurene has helped safeguard the programs that assist our most experienced citizens and give them the dignity they deserve.',
    '[Leon Worden\'s note gives her full date of birth; this archive records the year only for living people.]',
];
foreach (['Laurene Weste (b. Oct. 26, 1948). Resident of Placerita Canyon/Newhall. Santa Clarita City Council member, 1998 to present. Mayor in 2001, 2006, 2010, 2014.', 'SC1311: 19200 dpi jpeg.', $CAPTION[2], $CAPTION[3], substr($CAPTION[1], strlen('[City of Santa Clarita 2013] '))] as $ph) {
    if (!str_contains($txt, $ws($ph))) { $bad[] = 'SC1311 does not read: ' . mb_substr($ph, 0, 60); }
}
$photo = Entry::find()->section('photographs')->status(null)->legacyKey('sc1311')->one();
$cur = $p?->featuredImage->one();
echo ($photo ? "#{$photo->id} exists" : 'create photograph SC1311 with the scan') . '; #15929 portrait ' . ($cur ? "#{$cur->id}" : 'none') . ' -> SC1311; birthDate "' . $p?->birthDate . '" -> "1948"' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$photo) {
        $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
        $tmp = sys_get_temp_dir() . '/sc1311.jpg'; copy($SCAN, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename('sc1311.jpg'); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
        $a->setFieldValues(['provenanceKind' => 'legacy-mirror', 'legacySourcePath' => 'gif/sc1311.jpg', 'acquiredDate' => '2026-10-02',
            'source' => "SCVHistory.com, gif/sc1311.jpg, from the legacy mirror of 20 August 2026, SHA-256 $SHA, matching the manifest; the stored copy is re-encoded on import."]);
        if (!$elements->saveElement($a)) { throw new \RuntimeException('scan: ' . json_encode($a->getFirstErrors())); }
        $photo = new Entry(); $photo->sectionId = $svc->getSectionByHandle('photographs')->id; $photo->setTypeId($svc->getEntryTypeByHandle('photograph')->id); $photo->title = 'Laurene Weste, Santa Clarita City Council, 2013';
        $h = array_map(fn($f) => $f->handle, $photo->getFieldLayout()->getCustomFields());
        $photo->setFieldValues(array_intersect_key(['featuredImage' => [$a->id], 'body' => implode("\n\n", $CAPTION), 'photoSourceCode' => 'SC1311', 'creditRaw' => 'SC1311: 19200 dpi jpeg.', 'creditDpi' => '19200',
            'photoPeople' => [$ID], 'legacyKey' => 'sc1311', 'legacyUrl' => '/scvhistory/sc1311.htm', 'sourcePath' => 'https://scvhistory.com/scvhistory/sc1311.htm'], array_flip($h)));
        if (!$elements->saveElement($photo)) { throw new \RuntimeException('photograph: ' . json_encode($photo->getFirstErrors())); }
    }
    $s = Entry::find()->id($ID)->status(null)->one(); $img = $photo->featuredImage->one();
    $s->setFieldValues(['featuredImage' => [$img->id], 'birthDate' => '1948', 'birthDateEdtf' => '1948', 'birthEvidence' => 'contemporary']);
    if (!$elements->saveElement($s)) { throw new \RuntimeException('#15929: ' . json_encode($s->getFirstErrors())); }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$s = Entry::find()->id($ID)->status(null)->one();
$ok = $s->featuredImage->one()?->filename === 'sc1311.jpg' && (string)$s->birthDate === '1948';
echo 'READ-BACK ' . ($ok ? 'OK: ' . $s->url . ' and ' . $photo->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('import_weste_portrait.php', 2, $ok ? 'verified' : 'SHORT', 'Laurene Weste: SC1311 council portrait from the mirror as her portrait; birth year');
if (!$ok) { throw new \RuntimeException('import_weste_portrait: read-back failed'); }
