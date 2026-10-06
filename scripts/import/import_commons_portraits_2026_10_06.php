/**
 * The census portraits (Nathan, 6 October 2026: "The Wikipedia census: Import the 12"). The Wikipedia census
 * (inventory/review/wikipedia-census-2026-10-06.md) found twelve records with no portrait whose article's lead image is a
 * free Wikimedia Commons file. Audra Strickland (#29450) has had a portrait since audra_strickland_portrait_2026_10_06.php
 * and is skipped, so eleven remain. Each Commons original was downloaded to inventory/incoming/<slug>-commons.jpg on
 * 6 October 2026, its SHA-1 checked against the one Commons reports (inventory/sources/commons-portraits-2026-10-06/
 * manifest.json, with the Commons API response saved beside it).
 *
 * Each becomes an asset in archiveMedia/outside/ and the record's featuredImage: provenanceKind outside, sourceUrl the
 * Commons file page, source naming the page and its author, sourceChecksum the file as received, license and rightsNote
 * as exact as the evidence allows.
 *
 * LICENCES. Federal works (the House portraits) are public domain under 17 U.S.C. 105. The three legislature portraits
 * tagged PD-CAGov on Commons (Henry Stern, Cathie Wright, Jeff Gorell) enter with licence unknown, as Christy Smith's did
 * (import_smith_portrait.php, 3 October 2026): the tag rests on the Public Records Act, which excludes the Legislature
 * (Government Code 7920.540(a)); Stern's file also carries "Senate Rules (c)2016". Fran Pavley's photograph is CC BY 2.0,
 * and the license field has no CC BY 2.0 option: her row is REFUSED until Nathan adds one (a schema change), never
 * entered under a different licence.
 *
 * Only for a record that still has no portrait; a record that has gained one is reported and skipped. Idempotent: an
 * asset already imported under its file name is reused, and a record already showing it is left alone.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_commons_portraits_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $as = Craft::$app->getAssets(); $n = 0;
$MAN = json_decode(file_get_contents("$root/inventory/sources/commons-portraits-2026-10-06/manifest.json"), true);
$man = []; foreach ($MAN['files'] as $f) { $man[$f['record']] = $f; }
$FED = 'a work of the United States government (17 U.S.C. 105)';
$CAGOV = 'Wikimedia Commons tags it public domain as a California state work (PD-CAGov), but that tag rests on the Public Records Act, which excludes the Legislature (Government Code 7920.540(a)).';
$NOPERM = 'Public domain is not established; no permission to publish is in hand.';
$dl = 'Downloaded from Wikimedia Commons on October 6, 2026; the stored copy is re-encoded.';

/* record id => [name, Commons file name, creator, dateAsPrinted, dateEdtf, license, rightsNote, rightsHolder, photoCredit, caption, source] */
$R = [
  29466 => ['Kevin McCarthy', 'Kevin McCarthy, official portrait, speaker.jpg', 'US House Photography', 'January 7, 2023, per Wikimedia Commons', '2023-01-07', 'public-domain',
    "Public domain: an official portrait by the House of Representatives' photographers, $FED. Wikimedia Commons licence tag: PD-USGov-Congress-Speaker.", '', 'US House Photography, via Wikimedia Commons',
    'Kevin McCarthy, official portrait as Speaker of the House, 2023.',
    'Wikimedia Commons, File:Kevin McCarthy, official portrait, speaker.jpg, the original (4178 x 5222). Author on Commons: "US House Photography"; taken from speaker.gov. ' . $dl],
  29464 => ['Bill Thomas', 'Bill Thomas, official photo portrait color.jpg', 'Not named (Wikimedia Commons: "Unknown author")', '', '', 'public-domain',
    'Public domain per Wikimedia Commons (PD-USGov-Congress), as an official photograph published on his House website, billthomas.house.gov. The photographer is not named and the date is not given.', '', 'U.S. House of Representatives, via Wikimedia Commons',
    'Bill Thomas, official photograph as a member of Congress.',
    'Wikimedia Commons, File:Bill Thomas, official photo portrait color.jpg, the original (1530 x 1927). Author on Commons: "Unknown author"; taken from billthomas.house.gov/media/photos/WMTPress_lg.jpg. ' . $dl],
  29462 => ['Henry Stern', 'Official portrait of Henry Stern.jpg', 'Lorie Leilani Shelley', 'November 15, 2016, per the file\'s own metadata', '2016-11-15', 'unknown',
    "The official Senate portrait of 2016, by Lorie Leilani Shelley. $CAGOV The file's own notice reads \"Senate Rules (c)2016\". $NOPERM", 'Senate Rules Committee, by the file\'s own notice ("Senate Rules (c)2016")', 'Lorie Leilani Shelley, California State Senate',
    'Henry Stern, official California State Senate portrait, 2016.',
    'Wikimedia Commons, File:Official portrait of Henry Stern.jpg, the original (1216 x 1824). Author on Commons: "Government of California"; taken from sd27.senate.ca.gov/biography. The file names Lorie Leilani Shelley as photographer. ' . $dl],
  29460 => ['Fran Pavley', 'Fran Pavley 2012.jpg', 'Edward Headington', 'October 10, 2012, per Wikimedia Commons', '2012-10-10', 'cc-by-2.0',
    'Creative Commons Attribution 2.0 Generic (CC BY 2.0), https://creativecommons.org/licenses/by/2.0/, as the photographer published it on Flickr (confirmed by a Commons Flickr review on November 1, 2012). Attribution required: "Photo: Edward Headington, CC BY 2.0".', 'Edward Headington', 'Photo: Edward Headington, CC BY 2.0, via Wikimedia Commons',
    'Fran Pavley, 2012. The photographer titled it "Fran Pavley-Democratic Club-805-Simi Valley-AD38-Headquarters-DEM-Opening".',
    'Wikimedia Commons, File:Fran Pavley 2012.jpg, the original (1333 x 1871). Author on Commons: Edward Headington, from Flickr, https://www.flickr.com/photos/headingtonmedia/8073083417/. ' . $dl],
  29458 => ['Cathie Wright', 'Cathie Wright, 2000.jpg', 'California State Senate (as given on Wikimedia Commons)', '2000, per Wikimedia Commons', '2000', 'unknown',
    "$CAGOV The Commons file was taken from SCVNews.com's report of her death, not from a Senate original. $NOPERM", '', 'California State Senate, via Wikimedia Commons',
    'Cathie Wright, State Senator for the 19th District, 2000.',
    'Wikimedia Commons, File:Cathie Wright, 2000.jpg, the original (964 x 1284). Author on Commons: "California State Senate"; taken from https://scvnews.com/cathie-wright-former-state-senator-dies-at-82/. ' . $dl],
  29452 => ['Jeff Gorell', 'California State Assembly Member Jeff Gorell.jpg', 'California State Assembly photographer (as given on Wikimedia Commons)', '', '', 'unknown',
    "$CAGOV $NOPERM", '', 'California State Assembly, via Wikimedia Commons',
    'Jeff Gorell, California State Assembly portrait.',
    'Wikimedia Commons, File:California State Assembly Member Jeff Gorell.jpg, the original (1877 x 2201). Author on Commons: "California State Assembly", "Directly from a California State Assembly Photographer", taken from the State Assembly Gallery. ' . $dl],
  29446 => ['Tom McClintock', 'Tom McClintock portrait (118th Congress).jpg', 'U.S. House of Representatives, Office of the Clerk', 'November 15, 2022, per the file\'s own metadata', '2022-11-15', 'public-domain',
    "Public domain: the House Clerk's official member photograph, $FED. Wikimedia Commons licence tag: PD-USGov-Congress.", '', 'Office of the Clerk, U.S. House of Representatives, via Wikimedia Commons',
    'Tom McClintock, official photograph, 118th Congress.',
    'Wikimedia Commons, File:Tom McClintock portrait (118th Congress).jpg, the original, which is small (335 x 410). Author on Commons: "U.S. House of Representatives"; taken from clerk.house.gov/members/M001177. ' . $dl],
  29336 => ['George Whitesides', 'George T. Whitesides, official portrait (119th Congress) (1).jpg', 'Ike Hayman', 'March 27, 2025, per Wikimedia Commons', '2025-03-27', 'public-domain',
    'Public domain: published on his House website, whose copyright page reads "Except as otherwise noted in this website, all of the content of the website constitutes a work of the Federal government under sections 105 and 403 of title 17 of the U.S. Code" (Wayback Machine, June 1, 2025). Wikimedia Commons licence tag: PD-USGov-Congress. Photographer: Ike Hayman.', '', 'Ike Hayman, U.S. House of Representatives, via Wikimedia Commons',
    'George Whitesides, official portrait, 119th Congress, 2025.',
    'Wikimedia Commons, File:George T. Whitesides, official portrait (119th Congress) (1).jpg, the original (1638 x 2048). Author on Commons: Ike Hayman; taken from whitesides.house.gov/about. ' . $dl],
  29334 => ['Mike Garcia', 'Mike Garcia, official portrait, 116th Congress (cropped1).jpg', 'House Creative Services (the file names Kristie Baxter)', 'July 28, 2020, per Wikimedia Commons', '2020-07-28', 'public-domain',
    "Public domain: an official portrait by House Creative Services, $FED. Wikimedia Commons licence tag: PD-USGov-Congress.", '', 'House Creative Services, via Wikimedia Commons',
    'Mike Garcia, official portrait, 116th Congress, 2020.',
    'Wikimedia Commons, File:Mike Garcia, official portrait, 116th Congress (cropped1).jpg, the original (2659 x 3547), a crop Commons made of the full portrait. Author on Commons: "House Creative Services"; taken from mikegarcia.house.gov. ' . $dl],
  18714 => ['Phineas Banning', 'Phineas Banning.jpg', 'Unknown photographer', 'before 1885, per Wikimedia Commons', '', 'public-domain',
    'Public domain: a photograph made in Banning\'s lifetime, before 1885 (Wikimedia Commons licence tag: PD-US). The Commons file was taken from a Press-Enterprise web page of 2013; the original print and its holder are not identified.', '', 'Unknown photographer, via Wikimedia Commons',
    'Phineas Banning (1830-1885).',
    'Wikimedia Commons, File:Phineas Banning.jpg, the original, which is small (416 x 553). Author on Commons: "Unknown photographer"; taken from https://www.pressenterprise.com/2013/05/14/banning-pass-chorale-to-present-centennial-concert/. ' . $dl],
  2532 => ['Cephas L. Bard', 'Cephas L. Bard, MD.jpg', 'Not named', 'c. 1901, per Wikimedia Commons', '1901~', 'public-domain',
    'Public domain: published in Men of California (Pacific Art Company, 1901), page 206 (Wikimedia Commons licence tag: PD-US).', '', 'Men of California (1901), via Wikimedia Commons',
    'Cephas L. Bard, M.D., from Men of California, 1901.',
    'Wikimedia Commons, File:Cephas L. Bard, MD.jpg, the original (1559 x 2185). Author on Commons: "unknown"; source on Commons: Men of California, published by Pacific Art Company (1901), page 206. ' . $dl],
];
$SKIP = [29450 => 'Audra Strickland: has a portrait already (audra-strickland.jpg, audra_strickland_portrait_2026_10_06.php); the Commons file is not imported'];

/* field limits and licence options, read from the schema rather than assumed */
$fields = Craft::$app->getFields();
$lim = []; foreach (['creator', 'dateAsPrinted', 'dateEdtf', 'rightsNote', 'rightsHolder', 'photoCredit', 'photoCaptionExt', 'source'] as $h) { $lim[$h] = $fields->getFieldByHandle($h)?->charLimit; }
$licOpts = array_map(fn($o) => (string)($o['value'] ?? ''), $fields->getFieldByHandle('license')->options);
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = $as->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
if (!$vol || !$folder) { throw new \RuntimeException('archiveMedia/outside/ not found'); }

$plan = []; $refused = []; $skipped = []; $done = [];
foreach ($SKIP as $rid => $why) { $skipped[] = "#$rid $why"; }
foreach ($R as $rid => [$name, $cfile, $creator, $dap, $edtf, $lic, $rn, $holder, $credit, $cap, $src]) {
    $why = [];
    $p = Entry::find()->id($rid)->status(null)->one();
    if (!$p) { $skipped[] = "#$rid $name: the record no longer exists"; continue; }
    if ($p->title !== $name) { $why[] = "#$rid is \"{$p->title}\", not $name"; }
    $m = $man[$rid] ?? null; $slug = $m ? basename($m['file'], '-commons.jpg') : '?';
    $file = $m ? "$root/{$m['file']}" : '';
    if (!$m || !is_file($file)) { $why[] = 'file missing'; }
    elseif (sha1_file($file) !== $m['commonsSha1']) { $why[] = 'file is not the Commons original (SHA-1 differs)'; }
    elseif (hash_file('sha256', $file) !== $m['sha256']) { $why[] = 'file differs from the manifest (SHA-256)'; }
    if ($m && str_replace(' ', '_', $cfile) !== rawurldecode(substr($m['commonsPage'], strlen('https://commons.wikimedia.org/wiki/File:')))) { $why[] = 'manifest page does not match the Commons file name'; }
    if (!in_array($lic, $licOpts, true)) { $why[] = "the license field has no option \"$lic\" (options: " . implode(', ', array_filter($licOpts)) . '); add it before applying'; }
    $fn = basename((string)$file);
    $vals = ['provenanceKind' => 'outside', 'acquiredDate' => '2026-10-06', 'license' => $lic, 'rightsNote' => $rn, 'rightsHolder' => $holder, 'creator' => $creator,
        'photoCredit' => $credit, 'photoCaptionExt' => $cap, 'dateAsPrinted' => $dap, 'dateEdtf' => $edtf, 'source' => $src,
        'sourceUrl' => ['type' => 'url', 'value' => $m['commonsPage'] ?? ''], 'sourceChecksum' => $m ? 'sha256:' . $m['sha256'] : ''];
    $vals = array_filter($vals, fn($v) => $v !== '' && $v !== null);
    foreach ($lim as $h => $l) { if ($l && isset($vals[$h]) && mb_strlen($vals[$h]) > $l) { $why[] = "$h is " . mb_strlen($vals[$h]) . " chars, over its limit of $l"; } }
    if (preg_match('~\x{2014}~u', implode(' ', array_filter($vals, 'is_string')))) { $why[] = 'an em dash in our text'; }
    $have = $p->featuredImage->status(null)->one();
    $a = Asset::find()->filename($fn)->volume('archiveMedia')->one();
    if ($have && $a && $have->id === $a->id) { $done[] = "#$rid $name: done already (asset #{$a->id} $fn)"; continue; }
    if ($have) { $skipped[] = "#$rid $name: has a portrait now ({$have->filename}); not imported"; continue; }
    if ($why) { $refused[$rid] = "#$rid $name: " . implode('; ', $why); }
    $plan[$rid] = [$p, $name, $file, $fn, $a, $vals, $m];
}

$out = ["# The census portraits: dry run, 6 October 2026\n", 'Eleven Wikimedia Commons originals, each the lead image of the person\'s Wikipedia article, for records with no portrait. Files in inventory/incoming/, manifest and Commons metadata in inventory/sources/commons-portraits-2026-10-06/. Nothing is written by a dry run.' . "\n"];
foreach ($plan as $rid => [$p, $name, $file, $fn, $a, $vals, $m]) {
    $out[] = "## #$rid $name" . (isset($refused[$rid]) ? ' (REFUSED)' : '') . "\n";
    $out[] = '- file: ' . ($m['file'] ?? '?') . ' (' . ($m['width'] ?? '?') . ' x ' . ($m['height'] ?? '?') . ', ' . ($m['bytes'] ?? '?') . ' bytes); asset: ' . ($a ? "exists #{$a->id}, reused" : "import as archiveMedia/outside/$fn") . '; featuredImage: set it';
    $out[] = "- title: $name, portrait; alt: Portrait of $name";
    foreach ($vals as $h => $v) { $out[] = "- $h: " . (is_array($v) ? $v['value'] : $v); }
    if (isset($refused[$rid])) { $out[] = '- REFUSED: ' . substr($refused[$rid], strlen("#$rid $name: ")); }
    $out[] = '';
}
$out[] = "## Skipped\n\n" . ($skipped ? '- ' . implode("\n- ", $skipped) : '- none') . "\n";
$out[] = "## Done already\n\n" . ($done ? '- ' . implode("\n- ", $done) : '- none') . "\n";
$out[] = 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . "\n";
file_put_contents("$root/inventory/review/commons-portraits-dry-run-2026-10-06.md", implode("\n", $out));
foreach ($plan as $rid => [$p, $name, $file, $fn, $a, $vals]) {
    echo "#$rid $name: " . ($a ? "reuse asset #{$a->id}" : "import $fn") . ", licence {$vals['license']}" . (isset($refused[$rid]) ? '  REFUSED' : '') . PHP_EOL;
}
foreach ($skipped as $s) { echo "SKIP $s" . PHP_EOL; }
foreach ($done as $s) { echo "DONE $s" . PHP_EOL; }
echo 'to import: ' . (count($plan) - count($refused)) . '; refused: ' . count($refused) . '; skipped: ' . count($skipped) . '; done already: ' . count($done) . PHP_EOL;
echo 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . PHP_EOL . 'printed to inventory/review/commons-portraits-dry-run-2026-10-06.md' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply (refused rows are left out).' . PHP_EOL; return; }

$ok = 0;
foreach ($plan as $rid => [$p, $name, $file, $fn, $a, $vals]) {
    if (isset($refused[$rid])) { continue; }
    if (!$a) {
        $tmp = sys_get_temp_dir() . '/' . $fn; copy($file, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($fn); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
        if (!$el->saveElement($a)) { throw new \RuntimeException("$fn: " . json_encode($a->getFirstErrors())); }
        $a = Asset::find()->id($a->id)->one(); $a->title = "$name, portrait"; $a->alt = "Portrait of $name";
        $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields());
        $miss = array_diff(array_keys($vals), $ah); if ($miss) { throw new \RuntimeException("$fn: not on the asset layout: " . implode(', ', $miss)); }
        $a->setFieldValues($vals);
        if (!$el->saveElement($a)) { throw new \RuntimeException("$fn: " . json_encode($a->getFirstErrors())); } $n++;
    }
    $p = Entry::find()->id($rid)->status(null)->one();
    if (!$p->featuredImage->status(null)->exists()) { $p->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($p)) { throw new \RuntimeException("#$rid: " . json_encode($p->getFirstErrors())); } $n++; }
    $chk = Entry::find()->id($rid)->status(null)->one()->featuredImage->status(null)->one();
    $ra = Asset::find()->id($a->id)->one();
    if ($chk?->id === $a->id && (string)$ra->getFieldValue('license') === $vals['license'] && (string)$ra->sourceChecksum === $vals['sourceChecksum']) { $ok++; } else { echo "READ-BACK SHORT #$rid $name" . PHP_EOL; }
}
$want = count($plan) - count($refused);
echo 'READ-BACK ' . ($ok === $want ? "OK: $ok of $want" : "SHORT: $ok of $want") . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('import_commons_portraits_2026_10_06.php', $n, $ok === $want ? 'verified' : 'SHORT', "census portraits from Wikimedia Commons: $ok of $want" . ($refused ? '; refused: ' . implode(', ', array_keys($refused)) : ''));
echo "done: $n" . PHP_EOL;
