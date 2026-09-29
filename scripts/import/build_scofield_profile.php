/**
 * Demetrius G. Scofield #21584: the career, and his portrait.
 *
 * Nathan, 29 September 2026. The body written with the California Star Oil
 * Works build held his dates and the corrections to the popular accounts; this
 * replaces it with the whole career, read in the papers of 1873 to 1917
 * (California Digital Newspaper Collection, article ids in the footnotes), and
 * keeps the corrections. What the popular accounts say and the papers do not
 * is laid out as such: the Rockefeller story rests on one wire obituary and is
 * marked doubtful.
 *
 * THE PORTRAIT. Wikimedia Commons, File:DemetriusGScofield.jpg, 374 x 477,
 * SHA-1 3d95720019d02001ba6d14ba859758dd86148b1c, the Commons original checked
 * byte for byte. Public domain as published before 1929 (PD-US-1923), Nathan's
 * decision: a man born about 1843, photographed by 1911, dead in 1917, the
 * picture issued by Standard Oil. The claim rests on a 1911 publication that
 * Commons does not name, and the source field says so, so the gap stays
 * visible. The file Nathan first supplied was that portrait enlarged by Adobe
 * Firefly's generative upsampler; it was not used.
 *
 * Replaces only the body written on 29 September (refuses one edited since),
 * and its footnotes. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_scofield_profile.php'))"
 */

use craft\elements\Entry;
use craft\elements\Asset;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$ID = 21584;
$OLD_START = 'Demetrius G. Scofield was an oil man with the California Star Oil Works Company';
$cdnc = fn(string $id) => 'https://cdnc.ucr.edu/?a=d&d=' . $id;
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$CALL_1917 = 'San Francisco Call, 30 July 1917, ' . $cdnc('SFC19170730.2.2');

$BODY = implode("\n\n", [
    'Demetrius G. Scofield was a San Francisco oil man who rose through the California Star Oil Works and the Pacific Coast Oil Company to be president of Standard Oil Company (California). He was born in New York City; his age at death, 74, puts his birth between mid-1842 and mid-1843, and the date later accounts give, 28 January 1843, has not been found in a record.[1]',
    'He was in San Francisco by 1873, when a D. G. Scofield, very probably the same man, was secretary of a political committee there.[2] By January 1874 he was with F. B. Taylor & Co., importers of oil, and sailed for Yokohama on its business.[3] In 1876 he ran a stockbroking firm, D. G. Scofield & Co., which suspended and then resumed.[4] In March 1877 he toured the Ventura oil country for the Taylor firm, and by May he was named "of the Star Oil Works Company"; in the receivership fight of 1878 he refused to give up the company\'s wells.[5] He did not run its refinery: the papers of 1877 name A. G. Scott as its superintendent.[5] That year he was also a director of a Taylor company called the Standard Oil Company of California, which had nothing to do with the Standard of later years.[6]',
    'In January 1880 Taylor sold his oil interests to Charles N. Felton and Scofield.[7] With Felton and Lloyd Tevis he founded the Pacific Coast Oil Company, which held the Star company, and he was its auditor in 1880, with Felton as president.[8] He was its general manager by 1894, its vice president in 1902, when he announced the pipeline from Bakersfield to Point Richmond, and its president by 1903.[9] Standard Oil bought the Pacific Coast Oil Company in December 1900.[10] Scofield was Standard\'s general manager on the coast by 1906, when John D. Rockefeller wired him about relief for the earthquake;[11] he was vice president of Standard Oil Company (California) from its incorporation in 1906, was elected its president on 5 December 1911, and became its chairman in February 1917.[12]',
    'After Charles Alexander Mentry died in October 1900, Scofield delivered the eulogy the archive holds as record #20104.[13] He died in Oakland on 30 July 1917, and was cremated.[14]',
    'His Associated Press obituary said that he had "laid the foundation" of Standard Oil with John D. Rockefeller at Oil Creek, Pennsylvania. Nothing else supports it, and it is doubtful: the San Francisco papers have him in the city by 1873 and in the oil trade as an importer, not a producer. A memoir of 1926 by Youle has him coming from Pennsylvania and securing territory near Newhall in 1875; the papers of the time do not place him in the oil field before 1877.[15]',
    'Wikipedia and other later accounts, including the note on the eulogy\'s legacy page, say that he founded the California Star Oil Works, hired Mentry, and was the first president of Standard Oil of California. The sources of the time say otherwise. The company\'s directors of 1876 do not include him; Mentry was hired in July 1875, by the partners of the Star Oil Works, before the company existed; and in 1906 Scofield was vice president of Standard Oil of California, not its president.[16]',
]);
$FOOTNOTES = $fn([
    $CALL_1917 . ': "born in New York City." San Jose Mercury News, 31 July 1917: "74 years old." 28 January 1843 is given by Find a Grave and online encyclopedias, citing no record.',
    'Daily Alta California, 26 July 1873, ' . $cdnc('DAC18730726.2.15') . ': "D. G. Scofield, Secretary of the Executive Committee." The identity is probable, not proven.',
    'The Hebrew (San Francisco), 30 January 1874, ' . $cdnc('HBSF18740130.2.16') . ': "Their Mr. D. G. Scofield will depart for Yokohama."',
    'The Hebrew, 3 March 1876, ' . $cdnc('HBSF18760303.2.24.3') . '; Oakland Tribune, 14 August 1876, ' . $cdnc('OT18760814.1.3') . ': "the stock broker who suspended a few days ago."',
    'Ventura Free Press, 31 March 1877, ' . $cdnc('VFPW18770331.2.31') . ': "D. G. Scofield, of Taylor Co., San Francisco." Ventura Free Press, 5 May 1877, ' . $cdnc('VFPW18770505.2.17') . ': "of the Star Oil Works Company," and "A. G. Scott, superintendent of the refinery business." Santa Barbara Morning Press, 8 April 1878, ' . $cdnc('MP18780408.2.14') . ': "D. G. Scofield of the California Star Oil Company."',
    'San Jose Mercury, 7 November 1877, ' . $cdnc('SJMN18771107.2.18') . '; Ventura Free Press, 17 November 1877, ' . $cdnc('VFPW18771117.2.27') . ': "of the Standard and Star Oil Companies."',
    'Santa Barbara Morning Press, 17 January 1880, ' . $cdnc('MP18800117.2.9') . ': "to Charles N. Felton and D. G. Scofield." Daily Alta California, 18 March 1886, ' . $cdnc('DAC18860318.2.16') . ', on Scofield & Tevis, which bought Taylor\'s paint and oil business on 1 January 1880.',
    'San Jose Mercury, 24 September 1880, ' . $cdnc('SJMN18800924.2.6') . ': the company\'s founders "Lloyd Tevis ... and D. G. Scofield," with Felton. Ventura Signal, 17 April 1880, ' . $cdnc('VS18800417.2.25') . ': "D. G. Scofield, Auditor."',
    'San Jose Herald, 15 October 1894, ' . $cdnc('SJH18941015.2.3') . ': "General Manager Scofield of the Pacific Coast Oil Company." Hanford Sentinel, 13 February 1902, ' . $cdnc('HS19020213.2.91') . ', quoting the Pacific Oil Reporter: "vice president." Bakersfield Morning Echo, 8 May 1903, ' . $cdnc('BME19030508.1.4') . ': "president of the Pacific Coast Oil Company."',
    'San Francisco Call, 11 December 1900, ' . $cdnc('SFC19001211.2.27') . ': Standard "acquires all of the interests of the Pacific Coast Oil Company."',
    'Riverside Enterprise, 25 April 1906, ' . $cdnc('MPE19060425.2.17') . ': "general manager of the company."',
    $CALL_1917 . ': "became vice president ... in 1906, and was elected president in 1911"; "until February, 1917, when he became chairman." San Francisco Call, 6 December 1911: "D. G. Scofield, former vice president, was elected president."',
    'Record #20104, "Demetrius Scofield\'s Eulogy to Charles Alexander Mentry."',
    $CALL_1917 . '; San Francisco Call, 31 July 1917, ' . $cdnc('SFC19170731.2.138') . ': "The body was cremated." Santa Rosa Press Democrat, 31 July 1917, dateline "Oakland, July 30."',
    'Oakland Tribune, 30 July 1917, ' . $cdnc('OT19170730.1.1') . ', and San Jose Mercury News, 31 July 1917, ' . $cdnc('SJMN19170731.2.48') . ', the same wire copy: "at Oil Creek, Pa., many years ago with John D. Rockefeller." Youle, in the Petroleum Reporter, 21 May 1926, as quoted by the Homestead Museum blog: "securing territory near Newhall."',
    'Los Angeles Herald, 25 May 1876, ' . $cdnc('LAH18760525.2.14') . '; Sacramento Daily Union, 10 July 1876, ' . $cdnc('SDU18760710.2.16') . '; Charles W. Snell, National Park Service, 1963, https://npgallery.nps.gov/NRHP/GetAsset/NHLS/66000212_text: the Star Oil Works partners "employed C. A. Mentry ... in July 1875." On 1906, note 12.',
]);

$FILE = \Craft::getAlias('@root') . '/inventory/incoming/DemetriusGScofield-commons.jpg';
$SHA1 = '3d95720019d02001ba6d14ba859758dd86148b1c';
$FILENAME = 'demetrius-g-scofield-commons-1911.jpg';
$ASSET = [
    'title' => 'Demetrius G. Scofield, published by 1911',
    'alt' => 'Portrait of Demetrius G. Scofield',
    'fields' => [
        'license' => 'public-domain',
        'provenanceKind' => 'outside',
        'sourceUrl' => 'https://commons.wikimedia.org/wiki/File:DemetriusGScofield.jpg',
        'acquiredDate' => '2026-09-28',
        'dateAsPrinted' => '1911, publication, per Wikimedia Commons',
        'dateEdtf' => '../1911',
        'source' => 'Wikimedia Commons, File:DemetriusGScofield.jpg, which gives "Original publication: 1911," credits Standard Oil of California, and took the image from elsmerecanyon.com. The 1911 publication is not named, and the public-domain status, published before 1929 (PD-US-1923), rests on that unnamed publication: the gap is recorded here so that a later reader can close it. Received from Commons on 28 September 2026, 374 x 477, SHA-1 ' . '3d95720019d02001ba6d14ba859758dd86148b1c' . ', matching the Commons original; the stored copy is re-encoded on import and differs from the file as received.',
    ],
];

$elements = Craft::$app->getElements();
$e = Entry::find()->id($ID)->status(null)->one();
if (!$e || $e->title !== 'Demetrius G. Scofield') { echo "REFUSING: #$ID is not Demetrius G. Scofield" . PHP_EOL; return; }
$body = trim((string)$e->body);
$doBody = $body !== trim($BODY);
if ($doBody && !str_starts_with($body, $OLD_START)) { echo 'REFUSING: the body is not the one written on 29 September; someone has edited it' . PHP_EOL; return; }
if ($doBody) { foreach (explode("\n\n", $BODY) as $i => $p) { echo 'P' . ($i + 1) . ': ' . $p . PHP_EOL . PHP_EOL; } echo 'footnotes: ' . count($FOOTNOTES) . PHP_EOL; }
else { echo 'body: already written' . PHP_EOL; }

if (!is_file($FILE) || sha1_file($FILE) !== $SHA1) { echo 'REFUSING the portrait: ' . basename($FILE) . ' is missing or is not the Commons original' . PHP_EOL; return; }
$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'outside/']);
$existing = Asset::find()->volumeId($volume->id)->filename($FILENAME)->one();
$current = $e->featuredImage->one();
if ($current && (!$existing || $current->id !== $existing->id)) { echo 'REFUSING the portrait: featuredImage already holds #' . $current->id . ' ' . $current->filename . PHP_EOL; return; }
echo PHP_EOL . 'portrait: ' . ($existing ? 'held as #' . $existing->id : 'import as archiveMedia/outside/' . $FILENAME) . ($current ? ', already featured' : ', set as featuredImage') . PHP_EOL;
foreach ($ASSET['fields'] as $k => $v) { echo '   ' . str_pad($k, 15) . $v . PHP_EOL; }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$asset = $existing;
if (!$asset) {
    $tmp = sys_get_temp_dir() . '/' . $FILENAME;
    if (!copy($FILE, $tmp)) { throw new \RuntimeException('build_scofield_profile: could not stage the file'); }
    $asset = new Asset();
    $asset->tempFilePath = $tmp; $asset->setFilename($FILENAME); $asset->newFolderId = $folder->id; $asset->setVolumeId($volume->id);
    $asset->setScenario(Asset::SCENARIO_CREATE); $asset->avoidFilenameConflicts = false;
    if (!$elements->saveElement($asset)) { throw new \RuntimeException('build_scofield_profile: asset ' . json_encode($asset->getFirstErrors())); }
    $asset = Asset::find()->id($asset->id)->one();
    $asset->title = $ASSET['title']; $asset->alt = $ASSET['alt'];
    $vals = $ASSET['fields']; $vals['sourceUrl'] = ['type' => 'url', 'value' => $vals['sourceUrl']];
    $asset->setFieldValues($vals);
    if (!$elements->saveElement($asset)) { throw new \RuntimeException('build_scofield_profile: asset fields ' . json_encode($asset->getFirstErrors())); }
}
$e = Entry::find()->id($ID)->status(null)->one();
if ($doBody) { $e->setFieldValues(['body' => $BODY, 'footnotes' => $FOOTNOTES]); }
if (!$current) { $e->setFieldValue('featuredImage', [$asset->id]); }
if (!$elements->saveElement($e)) { throw new \RuntimeException('build_scofield_profile: person ' . json_encode($e->getFirstErrors())); }

$b = Entry::find()->id($ID)->status(null)->one(); $short = [];
if (trim((string)$b->body) !== trim($BODY)) { $short[] = 'body differs'; }
if (($b->featuredImage->one()->id ?? null) !== $asset->id) { $short[] = 'portrait not featured'; }
if ((string)Asset::find()->id($asset->id)->one()->getFieldValue('license')->value !== 'public-domain') { $short[] = 'licence not recorded'; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: career body, 16 footnotes, portrait #' . $asset->id) . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('build_scofield_profile.php', 2, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'Scofield career 1873-1917; Commons portrait, PD-US-1923 on an unnamed 1911 publication');
if ($short) { throw new \RuntimeException('build_scofield_profile: ' . implode('; ', $short)); }
