/**
 * Real, unedited photographs back on the 19 person records whose portraits came off on 8 October (Nathan, 8 October 2026:
 * "find the originals. Check Reggie, check inventory/incoming and done, check Archive.org captures of their pages, check
 * the old WordPress build ... Any that turn up go straight back on as the portrait, unedited.").
 * Found: three on Reggie, published on the original site; sixteen on the web (Wayback captures, the bodies' own pages,
 * Wikimedia Commons, SCVNews, KHTS, ACWA, a campaign site), downloaded on 8 October and kept as downloaded in
 * inventory/incoming/done/originals-2026-10-08/. Each was looked at beside the edit it replaces. The files in incoming/done
 * named for these people were all Firefly outputs; the old WordPress build held none of the 19 except Wiley's PDF.
 * Each becomes a new asset from its web copy (originals_copies_2026_10_08.sh), with the master's checksum, where it was
 * published, and when it was taken; then the record's portrait. A record that already has a portrait is refused.
 * Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$WEB = "$root/storage/runtime/photo-import/originals-web"; $IN = "$root/inventory/incoming/done/originals-2026-10-08";
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the nineteen web copies', $WEB],
  ['file', 'the sixteen downloaded masters, hashed here for their checksums', $IN],
  ['record', 'the drive manifest checksums of the three masters on Reggie (sourceChecksum, legacySourcePath; sourceUrl and provenanceKind are written, not read)', 'the masters on Reggie', 'read'],
  ['record', 'each record\'s featuredImage, and assets found by filename', 'the stored files', 'not read: an existing asset with the same filename is reused only if it is the same web copy, checked by hash below'],
]);
$W = 'downloaded on 8 October 2026';
/* person id => [web copy, master (reggie: path | file in $IN), sourceUrl, source sentence, credit, license, rights note] */
$P = [
  23089 => ['billmiranda2017_large.jpg', 'reggie:gif/billmiranda2017_large.jpg|ac61097d2157becf90cc5f422480e5059d9fd70c9ecf187dd6b6cf7606c864a1', '',
    'SCVHistory.com, gif/billmiranda2017_large.jpg, the enlargement linked from the photograph on /scvhistory/sc1709.htm, credited there "City of Santa Clarita, 2017". Taken from the original site\'s files on 8 October 2026.', 'City of Santa Clarita, 2017', 'unknown', ''],
  23085 => ['sc1313_large.jpg', 'reggie:gif/sc1313_large.jpg|3a9e1066be8113f4e54dff66c2791723ee24697ef2129245d794f51c5955036a', '',
    'SCVHistory.com, gif/sc1313_large.jpg, the enlargement linked from the photograph on /scvhistory/sc1313.htm, credited there "City of Santa Clarita 2013". Taken from the original site\'s files on 8 October 2026.', 'City of Santa Clarita, 2013', 'unknown', ''],
  331 => ['sw_hssc0402wiley_large.png', 'reggie:gif/sw_hssc0402wiley_large.png|1408963a44d01586ce9a360959b91f8b9cc620113512fceaf8a341e205f6571a', '',
    'SCVHistory.com, gif/sw_hssc0402wiley_large.png, linked from /scvhistory/sw_hssc0402wiley.htm: the plate "Henry C. Wiley" in the Historical Society of Southern California Annual, Vol. 4 Part 2, 1898 (JSTOR 41167721). Taken from the original site\'s files on 8 October 2026.', 'Historical Society of Southern California Annual, Vol. 4, 1898', 'public-domain', 'Published in 1898.'],
  23091 => ['jason-gibbs-city-2023.png', 'jason-gibbs-city-2023.png', 'https://santaclarita.gov/city-council/wp-content/uploads/sites/39/2023/09/JasonGibbs.png',
    "The City of Santa Clarita's council portrait, on its council page, as captured by the Internet Archive on 17 September 2024 (web.archive.org/web/20240917165254), the City's own file at full size; $W.", 'City of Santa Clarita', 'unknown', 'No permission to republish is established.'],
  23093 => ['patsy-ayala-city-2024.png', 'patsy-ayala-city-2024.png', 'https://santaclarita.gov/city-council/wp-content/uploads/sites/39/2024/12/PatsyAyala.png',
    "The City of Santa Clarita's council portrait, on its council page, as captured by the Internet Archive on 31 March 2025 (web.archive.org/web/20250331125013); the capture is the copy the City's web host recompressed, not the City's own file, which no capture holds; $W.", 'City of Santa Clarita', 'unknown', 'No permission to republish is established.'],
  26946 => ['bill-cooper-campaign-2026.png', 'bill-cooper-campaign-2026.png', 'https://assets.cdn.filesafe.space/AidIjHnPNKMgx52CYspc/media/6a2c622fadacee7c3e745585.png',
    "From Bill Cooper's 2026 campaign site for the SCV Water board (votebillcooper.com), the photograph beside his biography; campaign material; $W.", 'Bill Cooper campaign, 2026', 'unknown', 'Campaign material. No permission to republish is established.'],
  25191 => ['alan-ferdman-khts-2020.jpg', 'alan-ferdman-khts-2020.jpg', 'https://2021media.s3.amazonaws.com/2020/03/AlanFerdman1.jpg',
    "KHTS (hometownstation.com), \"2020 SCV Man and Woman of the Year nominees\", March 2020, the largest size published; $W.", 'KHTS, 2020', 'unknown', 'No permission to republish is established.'],
  29328 => ['steve-knight-congress-2015.jpg', 'steve-knight-congress-2015.jpg', 'https://upload.wikimedia.org/wikipedia/commons/e/e5/Steve_Knight_official_congressional_photo.jpeg',
    "His official portrait for the 114th Congress, taken 26 January 2015 (United States Congress), from Wikimedia Commons, File:Steve Knight official congressional photo.jpeg; $W.", 'United States Congress, 2015', 'public-domain', 'A work of the United States Congress (Commons: PD-USGov-Congress).'],
  18747 => ['george-runner-boe-2011.jpg', 'george-runner-boe-2011.jpg', 'http://www.boe.ca.gov/runner/images/runner_bio_lg.jpg',
    "His 2011 official portrait as a member of the State Board of Equalization, from the Board's site as captured by the Internet Archive on 15 July 2015 (web.archive.org/web/20150715213719), at full size; $W.", 'California State Board of Equalization, 2011', 'unknown', 'Wikimedia Commons tags a smaller copy PD-CAGov; not established by the archive.'],
  29324 => ['sharon-runner-assembly-2007.jpg', 'sharon-runner-assembly-2007.jpg', 'https://upload.wikimedia.org/wikipedia/commons/7/7f/SR_medium_shot_color.jpg',
    "Taken 19 July 2007, credited to the California State Assembly, from Wikimedia Commons, File:SR medium shot color.jpg; $W.", 'California State Assembly, 2007', 'unknown', 'Commons now tags it PD-CAGov; its first upload, in 2010, was tagged CC BY-SA 3.0. Not established by the archive.'],
  28322 => ['bob-jensen-hart-2016.jpg', 'bob-jensen-hart-2016.jpg', 'http://www.hartdistrict.org/apps/download/oVlXOYlmuuwm0DdcrKubhnOYVYaEWjLnYvTRNLcIXyNlyyi0.jpg/2016%20Bob%20Jensen.jpg',
    "The William S. Hart Union High School District's board portrait (\"2016 Bob Jensen\"), shown on its governing board page 2017 to 2022, as captured by the Internet Archive on 28 May 2017 (web.archive.org/web/20170528112308), at full size; $W.", 'William S. Hart Union High School District', 'unknown', 'No permission to republish is established.'],
  26549 => ['joe-messina-hart-2016.jpg', 'joe-messina-hart-2016.jpg', 'http://www.hartdistrict.org/apps/download/lyfofKthNyzky6r5dT2DIggxIelETKvkA5qN4nxhYao4amDx.jpg/2016%20Joe%20Messina.jpg',
    "The William S. Hart Union High School District's board portrait (\"2016 Joe Messina\"), shown on its governing board page 2017 to 2022, as captured by the Internet Archive on 28 May 2017 (web.archive.org/web/20170528124155), at full size; $W.", 'William S. Hart Union High School District', 'unknown', 'No permission to republish is established.'],
  28558 => ['cherise-moore-hart.jpg', 'cherise-moore-hart.jpg', 'https://1.cdn.edl.io/kEjWJf4QQSgw8KNhXifHm8pRN9tPUhrwo4lITo0OFvE51buN.jpg',
    "The William S. Hart Union High School District's board portrait, shown on its governing board page as \"Dr. Cherise Moore\" 2017 to 2022; the same bytes as the Internet Archive's capture of 28 May 2017; $W.", 'William S. Hart Union High School District', 'unknown', 'No permission to republish is established.'],
  28560 => ['erin-wilson-hart-2023.jpg', 'erin-wilson-hart-2023.jpg', 'https://3.files.edl.io/7e72/23/06/28/222251-0f03fdb8-59be-43f6-aff0-9ddc59e9f632.jpg',
    "The William S. Hart Union High School District's release \"Hart District Governing Board Selects Erin McKeon Wilson to Fill Vacant Seat\" (June 2023); $W. The board page's own portrait survives only as a 100 by 150 thumbnail.", 'William S. Hart Union High School District, 2023', 'unknown', 'No permission to republish is established.'],
  29450 => ['audra-strickland-assembly.jpg', 'audra-strickland-assembly.jpg', 'http://republican.assembly.ca.gov/members/images/mempic_Strickland.jpg',
    "Her member photograph on the Assembly Republican Caucus site, 37th District page, as captured by the Internet Archive on 26 October 2008 (web.archive.org/web/20081026042811), the size published; $W.", 'California State Assembly Republican Caucus', 'unknown', 'No permission to republish is established.'],
  28316 => ['bj-atkins-scvnews-2012.jpg', 'bj-atkins-scvnews-2012.jpg', 'https://scvnews.com/wp-content/uploads/2012/01/mug_bjatkins.jpg',
    "SCVNews, \"Gutzeit, Atkins Fill Top Slots on Water Board\", 18 January 2012; $W.", 'SCVNews.com, 2012', 'unknown', 'No permission to republish is established.'],
  28336 => ['jerry-gladbach-acwa-2022.jpg', 'jerry-gladbach-acwa-2022.jpg', 'https://www.acwa.com/wp-content/uploads/2022/07/Jerry-Gladbach.jpg',
    "Association of California Water Agencies, \"ACWA Past President Jerry Gladbach Passes Away\", 18 July 2022, the article's portrait; the same sitting as his SCV Water director photograph; $W.", 'Association of California Water Agencies', 'unknown', 'No permission to republish is established.'],
  2591 => ['patti-rasmussen-city.jpg', 'patti-rasmussen-city.jpg', 'https://santaclarita.gov/city-clerk/wp-content/uploads/sites/8/2023/06/635835750691630000.jpeg',
    "The City of Santa Clarita's Arts Commission page, captioned \"Patti Rasmussen, Commissioner\"; $W.", 'City of Santa Clarita', 'unknown', 'No permission to republish is established.'],
  25391 => ['brian-walters-scvnews-2013.jpg', 'brian-walters-scvnews-2013.jpg', 'https://scvnews.com/wp-content/uploads/2013/12/brianwalters.jpg',
    "SCVNews, \"Newhall School Board Selects Officers for 2014\", 25 December 2013; $W.", 'SCVNews.com, 2013', 'unknown', 'No permission to republish is established.'],
];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $as = Craft::$app->getAssets();
foreach ($P as $pid => [$web, $master, $url, $src, $credit, $lic, $rights]) {
  $e = Entry::find()->id($pid)->status(null)->one();
  if (!$e || $e->getSection()->handle !== 'persons') { echo "#$pid: not a person record, left" . PHP_EOL; continue; }
  $legacy = str_starts_with($master, 'reggie:');
  [$mpath, $msum] = $legacy ? explode('|', substr($master, 7)) : [null, hash_file('sha256', "$IN/$master")];
  $folder = $as->findFolder(['volumeId' => $vol->id, 'path' => $legacy ? 'legacy/' : 'outside/']);
  $a = Asset::find()->volumeId($vol->id)->folderId($folder->id)->filename($web)->one();
  $feat = $e->featuredImage->status(null)->ids();
  echo "#$pid {$e->title}: " . ($a ? "asset exists #{$a->id}" : "new asset $web") . '; portrait ' . ($feat ? ($a && $feat === [$a->id] ? 'set already' : 'has ' . json_encode($feat) . ', refused') : 'set') . PHP_EOL;
  if (!$APPLY || ($feat && !($a && $feat === [$a->id]))) { continue; }
  if (!$a) {
    $tmp = sys_get_temp_dir() . "/$web"; copy("$WEB/$web", $tmp);
    $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($web); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id);
    $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
    if (!$el->saveElement($a)) { throw new \RuntimeException("$web " . json_encode($a->getFirstErrors())); }
    $a = Asset::find()->id($a->id)->one(); $a->title = "{$e->title}, portrait"; $a->alt = "Portrait of {$e->title}";
    $v = ['provenanceKind' => $legacy ? 'legacy-mirror' : 'outside', 'acquiredDate' => '2026-10-08', 'sourceChecksum' => "sha256:$msum",
      'source' => mb_substr($src, 0, 500), 'photoCredit' => $credit, 'license' => $lic, 'rightsNote' => $rights];
    if ($legacy) { $v['legacySourcePath'] = $mpath; } else { $v['sourceUrl'] = $url; }
    $h = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($h)));
    if (!$el->saveElement($a)) { throw new \RuntimeException("$web " . json_encode($a->getFirstErrors())); } $n++;
  }
  if (!$feat) { $e->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($e)) { throw new \RuntimeException("#$pid " . json_encode($e->getFirstErrors())); } $n++; }
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('originals_portraits_2026_10_08.php', $n, 'verified', 'real unedited photographs back as portraits on the 19 records'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
