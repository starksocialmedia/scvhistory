/**
 * Maria Gutzeit: the person record, and the image Nathan holds of her.
 *
 * Nathan, 29 September 2026, confirmed three times: create the record and
 * attach maria-gutzeit-avatar.png, rights not established and recorded as such.
 *
 * THE RECORD. A living person, so public-life facts only, each with two
 * citations, no birth year (no official source gives one), nothing about
 * address, health, finances or family. What has two sources goes in the body;
 * what has one waits in editorNotes for the elections and officeHolding work,
 * which will carry her terms. No roles are set: the vocabulary has no term for
 * a water board director, and one is not invented here.
 *
 * THE IMAGE. Received from Nathan, who obtained it during his work on her
 * campaign. It is campaign material: the photographer, the rights holder and
 * any permission to publish are unknown. So license is 'unknown', creator and
 * rightsHolder stay empty, provenanceKind is outside, and the source field says
 * all of that plainly, with the file as received and its SHA-256. Having the
 * file is not holding the rights to it (docs/DATA-MODEL.md). Scanned for content
 * credentials before import: none; its metadata names Adobe ImageReady only.
 * acquiredDate is 28 September, the California date it arrived (29 September on
 * Nathan's Mac).
 *
 * Idempotent: the record is found by title and the asset by filename; a
 * featuredImage someone set is never replaced. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_gutzeit_record.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$TITLE = 'Maria Gutzeit';
$FILE = \Craft::getAlias('@root') . '/inventory/incoming/maria-gutzeit-avatar.png';
$SHA256 = '240250b06475be149e1abb8798ec111b918e36dcd12b47b1eef241e3ca46da0c';
$FILENAME = 'maria-gutzeit-campaign-avatar.png';
$FOLDER = 'outside';

$RELEASE_2018 = 'SCV Water, press release, 17 January 2018, https://www.yourscvwater.com/sites/default/files/SCVWA/newscenter/Press%20Release/2018/2018-01.17-Press-Release_SCVWA-Leadership_FINAL.pdf';
$BOARD_PAGE = 'SCV Water, Board of Directors, https://www.yourscvwater.com/governance/board-directors, read 29 September 2026';

$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$FIELDS = [
    'fullName' => 'Maria Gutzeit',
    'occupation' => 'Water district director',
    'bodyAuthorship' => 'editorial-2026',
    'recordProvenance' => 'editorial-2026, create_gutzeit_record.php, 29 September 2026, from official and published sources in its footnotes',
    'body' => implode("\n\n", [
        'Maria Gutzeit was elected to the board of the Newhall County Water District in 2003. She served on it and, after the district merged into SCV Water in 2018, on the SCV Water board until 2020.[1][2]',
        'In April 2022 the SCV Water board appointed her to a vacancy in Division 3,[3][4] and in November 2022 she was elected to that seat, for the term ending in January 2027.[2][5] The board chose her as its president for 2025.[2][6]',
    ]),
    'footnotes' => $fn([
        $RELEASE_2018 . ': "was initially elected to the NCWD board in 2003." The district merged into SCV Water on 1 January 2018.',
        $BOARD_PAGE . ': "Newhall County Water District and then SCV Water from 2003-2020 and 2023-present"; "Division: 3"; term expiring January 2027; "board president". The page does not mention her appointed service of 2022.',
        'SCVNews, "SCV Water appoints new member to represent District 3," 27 April 2022, https://scvnews.com/scv-water-appoints-new-member-to-represent-district-3/',
        'Santa Clarita Magazine, 28 May 2022, https://santaclaritamagazine.com/2022/05/scv-water-board-of-directors-appoints-new-member-to-represent-division-3/: selected at a special meeting on 25 April from four applicants, for the term ending 1 January 2023.',
        'Ballotpedia, https://ballotpedia.org/Maria_Gutzeit: Division 3, 8 November 2022, Gutzeit 11,190 (51.19%), Lynne Plambeck 10,668. Secondary; the LA County Registrar\'s final count has not been read.',
        'SCVNews, "SCV Water elects Gutzeit board president," 8 January 2025, https://scvnews.com/scv-water-elects-gutzeit-board-president/',
    ]),
    'editorNotes' => [[
        'heading' => 'Held for the elections work',
        'note' => 'Held back from the body, one source each, for the elections and officeHolding work: Santa Clarita City Council candidate, 8 April 2008, fifth of five, 2,800 votes (City Clerk historical results, https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/historical-results-7.pdf); candidate again 8 April 2014, seventh of thirteen, 4,472 votes (City Clerk 2014 results by precinct); final president of the Newhall County Water District (' . $RELEASE_2018 . '); SCV Water vice president from January 2018 (same release); lost the Division 3 seat in 2020 (preliminary news figures only). No year of birth: no official source gives one.',
        'position' => 'bottom',
    ]],
];
$ASSET = [
    'title' => 'Maria Gutzeit, campaign image',
    'alt' => 'Portrait of Maria Gutzeit',
    'fields' => [
        'license' => 'unknown',
        'provenanceKind' => 'outside',
        'acquiredDate' => '2026-09-28',
        'source' => 'Campaign material. Received from Nathan Imhoff, who obtained it during his work on her campaign, on 28 September 2026, as maria-gutzeit-avatar.png, SHA-256 ' . '240250b06475be149e1abb8798ec111b918e36dcd12b47b1eef241e3ca46da0c'
            . '. The photographer, the rights holder and any permission to publish are not established. The stored copy is re-encoded on import and differs from the file as received.',
    ],
];

$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();

/* ------------------------------------------------ the person */
$named = Entry::find()->section('persons')->status(null)->search('Gutzeit')->all();
$named = array_merge($named, Entry::find()->section('persons')->status(null)->title('*Gutzeit*')->all());
$p = null;
foreach ($named as $e) { if ($e->title === $TITLE) { $p = $e; } else { echo 'REFUSING: another person record names Gutzeit: #' . $e->id . ' ' . $e->title . PHP_EOL; return; } }
$layoutP = $svc->getEntryTypeByHandle('person')->getFieldLayout();
$absent = array_values(array_filter(array_merge(array_keys($FIELDS), ['featuredImage']), fn($h) => !$layoutP->getFieldByHandle($h)));
if ($absent) { echo 'REFUSING: the person layout lacks ' . implode(', ', $absent) . PHP_EOL; return; }
echo ($p ? 'person exists: #' . $p->id : 'create person "' . $TITLE . '"') . PHP_EOL;
$setP = [];
foreach ($FIELDS as $h => $v) {
    $cur = $p ? $p->getFieldValue($h) : null;
    $empty = $cur === null || (is_array($cur) ? !array_filter($cur, fn($r) => is_array($r) && array_filter($r, fn($c) => $c !== null && $c !== '')) : (is_object($cur) && property_exists($cur, 'value') ? (string)$cur->value === '' : trim((string)$cur) === ''));
    if ($empty) { $setP[$h] = $v; echo '   ' . str_pad($h, 16) . (is_array($v) ? count($v) . ' row(s)' : (mb_strlen($v) > 110 ? mb_substr($v, 0, 110) . '...' : $v)) . PHP_EOL; }
}
foreach ($FIELDS['footnotes'] as $r) { echo '      [' . $r['number'] . '] ' . mb_substr($r['note'], 0, 120) . PHP_EOL; }

/* ------------------------------------------------ the image */
if (!is_file($FILE)) { echo 'REFUSING: ' . $FILE . ' not found' . PHP_EOL; return; }
$sha = hash_file('sha256', $FILE);
if ($sha !== $SHA256) { echo 'REFUSING: ' . basename($FILE) . ' is not the file received (sha256 ' . $sha . ')' . PHP_EOL; return; }
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$absentA = array_values(array_filter(array_keys($ASSET['fields']), fn($f) => !$volume->getFieldLayout()->getFieldByHandle($f)));
if ($absentA) { echo 'REFUSING: the asset layout lacks ' . implode(', ', $absentA) . PHP_EOL; return; }
$folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => $FOLDER . '/']);
if (!$folder) { echo 'REFUSING: folder ' . $FOLDER . '/ does not exist' . PHP_EOL; return; }
$existing = Asset::find()->volumeId($volume->id)->filename($FILENAME)->one();
$current = $p ? $p->featuredImage->one() : null;
if ($current && (!$existing || $current->id !== $existing->id)) { echo 'REFUSING: featuredImage is already set to #' . $current->id . ' ' . $current->filename . PHP_EOL; return; }
echo PHP_EOL . 'image ' . basename($FILE) . ': 600 x 600, sha256 ' . $sha . ' (the file received)' . PHP_EOL . '   asset: ' . ($existing ? 'already held as #' . $existing->id : 'import as archiveMedia/' . $FOLDER . '/' . $FILENAME) . PHP_EOL;
foreach ($ASSET['fields'] as $k => $v) { echo '   ' . str_pad($k, 16) . $v . PHP_EOL; }
echo '   ' . str_pad('creator', 16) . '(empty: not known)' . PHP_EOL . '   ' . str_pad('rightsHolder', 16) . '(empty: not known)' . PHP_EOL;
$needFeatured = !$current;
echo '   featuredImage   ' . ($needFeatured ? 'set to this image' : 'already this image') . PHP_EOL;
if (!$setP && $existing && !$needFeatured) { echo PHP_EOL . 'already done. Nothing to write.' . PHP_EOL; return; }

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* ------------------------------------------------ apply */
$asset = $existing;
if (!$asset) {
    $tmp = sys_get_temp_dir() . '/' . $FILENAME;
    if (!copy($FILE, $tmp)) { throw new \RuntimeException('create_gutzeit_record: could not stage the file'); }
    $asset = new Asset();
    $asset->tempFilePath = $tmp;
    $asset->setFilename($FILENAME);
    $asset->newFolderId = $folder->id;
    $asset->setVolumeId($volume->id);
    $asset->setScenario(Asset::SCENARIO_CREATE);
    $asset->avoidFilenameConflicts = false;
    if (!$elements->saveElement($asset)) { throw new \RuntimeException('create_gutzeit_record: asset ' . json_encode($asset->getFirstErrors())); }
    $asset = Asset::find()->id($asset->id)->one();
    $asset->title = $ASSET['title'];
    $asset->alt = $ASSET['alt'];
    $asset->setFieldValues($ASSET['fields']);
    if (!$elements->saveElement($asset)) { throw new \RuntimeException('create_gutzeit_record: asset fields ' . json_encode($asset->getFirstErrors())); }
}
if (!$p) {
    $p = new Entry();
    $p->sectionId = $svc->getSectionByHandle('persons')->id;
    $p->setTypeId($svc->getEntryTypeByHandle('person')->id);
    $p->title = $TITLE;
}
$p->setFieldValues($setP);
if ($needFeatured) { $p->setFieldValue('featuredImage', [$asset->id]); }
if (!$elements->saveElement($p)) { throw new \RuntimeException('create_gutzeit_record: person ' . json_encode($p->getFirstErrors())); }

/* ------------------------------------------------ read back */
$short = [];
$b = Entry::find()->section('persons')->title($TITLE)->status(null)->one();
if (!$b) { $short[] = 'person missing'; }
else {
    if (($b->featuredImage->one()->id ?? null) !== $asset->id) { $short[] = 'featuredImage is not the image'; }
    if (trim((string)$b->body) !== $FIELDS['body'] && isset($setP['body'])) { $short[] = 'body differs'; }
    $n = count(array_filter($b->footnotes, fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (isset($setP['footnotes']) && $n !== count($FIELDS['footnotes'])) { $short[] = "footnotes read $n, expected " . count($FIELDS['footnotes']); }
}
$a = Asset::find()->id($asset->id)->one();
foreach ($ASSET['fields'] as $k => $v) {
    $got = $a->getFieldValue($k);
    $got = is_object($got) && property_exists($got, 'value') ? (string)$got->value : trim((string)$got);
    if ($got !== $v) { $short[] = "asset $k reads \"" . mb_substr($got, 0, 40) . '"'; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: #' . $b->id . ' Maria Gutzeit, image #' . $asset->id . ' with rights recorded as not established') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('create_gutzeit_record.php', 2, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'person Maria Gutzeit; campaign image, licence unknown');
if ($short) { throw new \RuntimeException('create_gutzeit_record: ' . implode('; ', $short)); }
