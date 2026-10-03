/**
 * Bob Kellar's portrait (Nathan, 3 October 2026: "Build the profile from
 * https://scvhistory.com/scvhistory/sc1310.htm, which is the legacy council
 * page, the same source as Weste's SC1311. Import it as a photograph record the
 * way you did hers if it carries a portrait").
 *
 * SC1310 carries his City Council portrait (2013, "19200 dpi jpeg"), on a page
 * that was never imported. As for Weste (import_weste_portrait.php): the page
 * becomes a photograph record, its caption Leon Worden's note and the City's
 * 2013 biography as the page gives them, his birth date cut to the year; the
 * scan becomes his portrait; the year, 1944, is recorded from Leon's note.
 * The page and the scan were read from the Reggie mirror and match its
 * manifest of 20 August 2026; copies are in inventory/legacy/fetched.
 *
 * Bob-Kellar.jpg, named as in inventory/incoming, was not there on 3 October.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_kellar_portrait.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$ID = 21944; $SCAN = "$root/inventory/legacy/fetched/sc1310.jpg"; $SHA = '054c6fbf904d9388f3c1d0bdfab8bd00d9348d0279271a34584b93196d6ae3f4';
$HTM = "$root/inventory/legacy/fetched/sc1310.htm"; $HTM_SHA = '611adc57011d25cc6b3c79ce936800fe9af58c4b4162ddc4d8e1989339c32f78';
$txt = $ws(@file_get_contents("$root/inventory/legacy/fetched/sc1310.txt"));
$p = Entry::find()->id($ID)->status(null)->one();
$bad = [];
if (!$p || $p->title !== 'Bob Kellar') { $bad[] = '#21944 is not Bob Kellar'; }
if (!is_file($SCAN) || hash_file('sha256', $SCAN) !== $SHA || !is_file($HTM) || hash_file('sha256', $HTM) !== $HTM_SHA) { $bad[] = 'SC1310 files missing or not the mirror\'s'; }
/* The City's 2013 biography, every paragraph as the page gives it. */
$CITY = [
    'Bob Kellar joined the City of Santa Clarita as a first-term Councilmember in April 2000, and served as Mayor in 2004 and again in 2008 when he was re-elected. Kellar\'s goals for his third term as Mayor include working to resolve the CEMEX mega-mining and the Whittaker-Bermite property clean up issues, support of senior services, the attraction of high paying jobs for residents and continued advancements in the area of economic development, support of the arts, and raising awareness about drug issues affecting youth and local organizations dedicated to helping families in crisis, among many other initiatives.',
    'Mayor Kellar began his life of public service by entering the United States Army from 1965 through 1967. This was followed by 25 years with the Los Angeles Police Department. Kellar retired from the LAPD in 1993, finishing up his career as the Supervisor in Charge of Reserve Officer Training at the Police Academy. Throughout his more than 32 years as a Santa Clarita resident, he has played an active role in the community, serving on several local non-profit boards and committees.',
    'Kellar served as President of the Canyon Country Chamber of Commerce from 1993 through its incorporation with the Santa Clarita Valley Chamber of Commerce in 1995. He was instrumental in re-shaping the Santa Clarita Valley Chamber of Commerce to include Canyon Country during this time. In 2000, Kellar served as President of the Santa Clarita Division of the Southland Regional Association of Realtors and the Santa Clarita Valley Veterans Memorial Committee. From 2003 to 2007, Kellar served on the Henry Mayo Newhall Memorial Hospital Foundation. Today, Kellar serves on the Board of Directors for the Santa Clarita Valley Senior Center Foundation.',
    'Kellar is proud to be a 20+ year member of the Santa Clarita Valley Veterans Memorial Committee. He served as past president in 1993 and 1994, and currently serves as president of the Veterans Memorial Committee. Bob feels very strong about supporting the local veterans community and the country.',
    'As a former Chair of the City\'s Planning Commission, Kellar stood strong to insure that new development follows a more sound and responsible approach to growth. As a City Councilmember, Kellar has worked hard to prevent the proposed Cemex mining operation from going through. Additionally, Kellar has been a driving force in bringing stake holders together to get the Whittaker Bermite site cleaned up and with a responsible development in place.',
    'Kellar is proud of the work he has been able to accomplish on behalf of the citizens of Santa Clarita. His success has largely been the result of his ability to bring decision-makers together and put them on a common course.',
    'Kellar considers one of his primary responsibilities to be his availability to Santa Clarita\'s citizens and to do what he can to maintain a high level quality of life for all.',
];
$NOTE = 'Robert C. "Bob" Kellar (b. 1944). Resident of Sand Canyon/Canyon Country. Santa Clarita City Council member, 2000 to present. Mayor in 2004, 2008, 2013, 2016.';
$CAPTION = array_merge([$NOTE, '[City of Santa Clarita 2013] ' . $CITY[0]], array_slice($CITY, 1), ['[Leon Worden\'s note gives his full date of birth; this archive records the year only for living people.]']);
foreach (array_merge(['Robert C. "Bob" Kellar (b. May 1, 1944). Resident of Sand Canyon/Canyon Country. Santa Clarita City Council member, 2000 to present. Mayor in 2004, 2008, 2013, 2016.', 'SC1310: 19200 dpi jpeg.'], $CITY) as $ph) {
    if (!str_contains($txt, $ws($ph))) { $bad[] = 'SC1310 does not read: ' . mb_substr($ph, 0, 60); }
}
if (preg_match('~\x{2014}~u', implode('', $CAPTION))) { $bad[] = 'an em dash in the caption'; }
$photo = Entry::find()->section('photographs')->status(null)->legacyKey('sc1310')->one();
$cur = $p?->featuredImage->one();
if ($cur && $cur->filename !== 'sc1310.jpg') { $bad[] = "#21944 already has a portrait ({$cur->filename})"; }
if (trim((string)$p?->birthDate) !== '' && trim((string)$p?->birthDate) !== '1944') { $bad[] = '#21944 birthDate is "' . $p->birthDate . '"'; }
echo ($photo ? "#{$photo->id} exists" : 'create photograph SC1310 with the scan (' . count($CAPTION) . ' caption paragraphs)') . '; #21944 portrait ' . ($cur ? $cur->filename : 'none') . ' -> sc1310.jpg; birthDate "' . $p?->birthDate . '" -> "1944" (contemporary)' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if ($photo && $cur && (string)$p->birthDate === '1944') { echo 'nothing to do' . PHP_EOL; return; }
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$photo) {
        $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
        $tmp = sys_get_temp_dir() . '/sc1310.jpg'; copy($SCAN, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename('sc1310.jpg'); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
        $a->setFieldValues(['provenanceKind' => 'legacy-mirror', 'legacySourcePath' => 'gif/sc1310.jpg', 'acquiredDate' => '2026-10-03',
            'source' => "SCVHistory.com, gif/sc1310.jpg, from the legacy mirror of 20 August 2026, SHA-256 $SHA, matching the manifest; the stored copy is re-encoded on import."]);
        if (!$elements->saveElement($a)) { throw new \RuntimeException('scan: ' . json_encode($a->getFirstErrors())); }
        $photo = new Entry(); $photo->sectionId = $svc->getSectionByHandle('photographs')->id; $photo->setTypeId($svc->getEntryTypeByHandle('photograph')->id); $photo->title = 'Bob Kellar, Santa Clarita City Council, 2013';
        $h = array_map(fn($f) => $f->handle, $photo->getFieldLayout()->getCustomFields());
        $photo->setFieldValues(array_intersect_key(['featuredImage' => [$a->id], 'body' => implode("\n\n", $CAPTION), 'photoSourceCode' => 'SC1310', 'creditRaw' => 'SC1310: 19200 dpi jpeg.', 'creditDpi' => '19200',
            'photoPeople' => [$ID], 'legacyKey' => 'sc1310', 'legacyUrl' => '/scvhistory/sc1310.htm', 'sourcePath' => 'https://scvhistory.com/scvhistory/sc1310.htm'], array_flip($h)));
        if (!$elements->saveElement($photo)) { throw new \RuntimeException('photograph: ' . json_encode($photo->getFirstErrors())); }
    }
    $s = Entry::find()->id($ID)->status(null)->one(); $img = $photo->featuredImage->one();
    $s->setFieldValues(['featuredImage' => [$img->id], 'birthDate' => '1944', 'birthDateEdtf' => '1944', 'birthEvidence' => 'contemporary']);
    if (!$elements->saveElement($s)) { throw new \RuntimeException('#21944: ' . json_encode($s->getFirstErrors())); }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$s = Entry::find()->id($ID)->status(null)->one();
$ok = $s->featuredImage->one()?->filename === 'sc1310.jpg' && (string)$s->birthDate === '1944';
echo 'READ-BACK ' . ($ok ? 'OK: ' . $s->url . ' and ' . $photo->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('import_kellar_portrait.php', 2, $ok ? 'verified' : 'SHORT', 'Bob Kellar: SC1310 council portrait from the mirror as his portrait; birth year');
if (!$ok) { throw new \RuntimeException('import_kellar_portrait: read-back failed'); }
