/**
 * William Wirt Jenkins (#20226): his death certificate and his obituary are on Reggie, and his record said the certificate
 * was not held (Nathan's overnight brief of 10 October 2026, item 7: "Import it, correct the record";
 * inventory/review/single-source-second-sources-2026-10-09.md, "Read from a description", 1).
 * - The certificate: gif/tlp1601_large.jpg, page /scvhistory/tlp1601.htm ("TLP1601: 19200 dpi jpeg courtesy of Tricia Lemon
 *   Putnam"). A document record, as Mentry's certificate #20102 (as0001): the scan as its featured image, Leon's page text as
 *   the top note, his credit line as the bottom note. Read off the scan on 10 October 2026: California State Board of Health,
 *   Standard Certificate of Death, Local Registered No. 5087; William W. Jenkins; 1823 S. Flower St., Los Angeles; date of
 *   death October 19 1916; date of birth October 12 1835; birthplace Ohio; informant June J. Owens; filed Oct 21 1916.
 * - The obituary: gif/tlp_lat102016.jpg, page /scvhistory/tlp_lat102016.htm. An obituary record, as Boston's two of
 *   9 October. The clipping prints the headline and decks but no masthead and no date; the page gives "Los Angeles Times |
 *   October 20, 1916", and publicationDetails says the attribution is the page's. The body is transcribed from the scan here,
 *   not from the page's transcription, which adds two [sic] notes; one word ("Castiac") is blurred on the scan and is read as
 *   the page reads it, which the bottom note says.
 * - #20226: footnote 6 no longer says the certificate is not held; footnotes 1 and 5 add the two as second sources;
 *   deathEvidence retrospective -> certified (the certificate is the state's own record of the death); birthEvidence stays
 *   retrospective (an informant's statement of a birth 81 years before). The birth range [1833, 1835] is not narrowed: Reynolds
 *   still gives 1833, and a date decision is Nathan's. The featured image is not touched.
 * The certificate reaches his page through its subjectPerson, not through his recordDocuments, which is an asset field (the
 * first apply set it to the document's entry id, which Craft dropped, and read back SHORT for that reason only; ERRORLOG).
 * Idempotent: records matched by legacyKey, assets by filename; footnote text by exact equality. Dry run by default.
 */
use craft\elements\Entry;
use craft\elements\Asset;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0; $bad = [];
$G = '/mnt/reggie/scvhistory.com/gif/'; $P = '/mnt/reggie/scvhistory.com/scvhistory/';
$CERT = $G . 'tlp1601_large.jpg'; $OBIT = $G . 'tlp_lat102016.jpg';
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the death certificate scan, read by eye for the fields quoted here', $CERT],
  ['file', 'the obituary clipping scan, transcribed by eye for the body', $OBIT],
  ['file', 'the certificate\'s legacy page, for Leon\'s text and credit', $P . 'tlp1601.htm'],
  ['file', 'the obituary\'s legacy page, for the paper and date it gives', $P . 'tlp_lat102016.htm'],
  ['record', 'Craft fields footnotes, deathEvidence, recordDocuments and personObituaries on #20226', 'the two scans', 'read'],
  ['record', 'Craft asset fields sourceChecksum, legacySourcePath, provenanceKind and filename on the two new scans', 'the two scans', 'not read: written here from the files hashed above; filename is only matched to find an asset already made'],
]);
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$sums = [$CERT => 'ec179b5670f3f6c5ed2d067e78ceba29657e01e098b702bcc113de4406ea020a', $OBIT => 'e6a45cc1ce8d6dcf6a31d8c95e771e35410a8c446d21b8af4e8b300cbc19dbc7'];
foreach ($sums as $f => $s) { if (hash_file('sha256', $f) !== $s) { $bad[] = "$f is not the file read on 10 October"; } }
$text = function ($file) { $h = iconv('ISO-8859-1', 'UTF-8', file_get_contents($file)); $h = preg_replace('~<(script|style)\b.*?</\1>~is', '', $h);
  $t = html_entity_decode(strip_tags(preg_replace('~<(br|p|div|td|tr|h\d)\b[^>]*>~i', "\n", $h)), ENT_QUOTES | ENT_HTML5); return array_values(array_filter(array_map('trim', explode("\n", $t)), fn($l) => $l !== '' && $l !== "\u{a0}")); };
$cl = $text($P . 'tlp1601.htm'); $i0 = array_search('Click image to enlarge', $cl); $i1 = null;
foreach ($cl as $i => $l) { if (str_starts_with($l, 'TLP1601:')) { $i1 = $i; break; } }
$top = ($i0 !== false && $i1) ? implode("\n\n", array_slice($cl, $i0 + 1, $i1 - $i0 - 1)) : '';
$credit = $i1 ? $cl[$i1] : '';
if (!str_starts_with($top, 'As violent as his adult life was') || substr_count($top, "\n\n") !== 4) { $bad[] = 'the certificate page\'s text did not parse into its five paragraphs'; }
$ol = $text($P . 'tlp_lat102016.htm');
if (!in_array('Los Angeles Times | October 20, 1916.', $ol, true)) { $bad[] = 'the obituary page does not give "Los Angeles Times | October 20, 1916."'; }
$obitBody = implode("\n\n", [
  'In the death yesterday of W. W. Jenkins, 81 years old, California lost one of its oldest pioneers, a man who saw the State develop from a few scattered mining camps into a great State. Mr. Jenkins was born at Circleville, O., in 1835, and fifty-one years ago came to California. He was always active in the affairs of Los Angeles county and for six years, 1858-64, served as under sheriff. He was a lieutenant in the California rangers during the early years when the bad men of the world congregated here and life was held cheap. During these stirring years Mr. Jenkins took an active part in cleaning out the "bad" men who made this county their headquarters.',
  'He was closely associated with the late former Gov. Downey and was also a close friend of the firm of Temple & Workman, who were the only bankers in the county up until 1873 when they were wiped out by the memorable panic. Late in life, Mr. Jenkins became a rancher on an extensive scale and at the time of his death owned extensive lands in Castiac Canyon, where his home was located.',
  'Mr. Jenkins was visiting relatives in this city when he became suddenly ill and died yesterday morning. The funeral will be held Saturday morning at 10:30 o\'clock at the Brown & Company undertaking chapel. The body will be cremated. In addition to a widow, Mr. Jenkins leaves four children, Charles and Lee Jenkins, city employees, and Mrs. Charles Kellogg of Saugus, and Mrs. June Owens of this city.',
]);
$obitTitle = 'California Loses an Old Resident';
$obitTop = 'Printed above it: "At Peace." Decks: "W. W. Jenkins, Who Saw State Develop, Passes Away." "Well-known Official of Early Days will be Buried Saturday Morning--A Widow and Four Children, All Near this City, Survive Him."';
$obitBottom = 'Transcribed from the clipping, gif/tlp_lat102016.jpg ("News story courtesy of Tricia Lemon Putnam," /scvhistory/tlp_lat102016.htm). The clipping prints no masthead or date; the paper and date are the SCVHistory.com page\'s. The canyon\'s name is blurred on the scan and is given as the page reads it. The page notes that "fifty-one years ago" should be 61; his death certificate gives 65 years in California.';
$certTitle = 'William W. Jenkins\'s Death Certificate';

$J = Entry::find()->id(20226)->section('persons')->status(null)->one();
if ($J?->title !== 'William Wirt Jenkins') { $bad[] = '#20226 is not William Wirt Jenkins'; }
if ($J && (string)$J->bodyAuthorship !== 'editorial-2026') { $bad[] = '#20226 body is not editorial-2026'; }
$LAT = Entry::find()->id(30518)->section('organizations')->status(null)->one(); if ($LAT?->title !== 'Los Angeles Times') { $bad[] = '#30518 is not the Los Angeles Times'; }
$cert = Entry::find()->section('documents')->status(null)->legacyKey('tlp1601')->one();
$obit = Entry::find()->section('obituaries')->status(null)->legacyKey('tlp_lat102016')->one();
foreach ([$certTitle, $obitTitle, $obitTop, $obitBottom] as $s) { if (preg_match('~\x{2014}~u', $s)) { $bad[] = 'an em dash in our own text'; } }

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'certificate document: ' . ($cert ? "exists #{$cert->id}" : "create \"$certTitle\"") . "; top note " . str_word_count($top) . " words from the page; bottom note \"$credit\"" . PHP_EOL;
echo 'obituary: ' . ($obit ? "exists #{$obit->id}" : "create \"$obitTitle\"") . '; ' . str_word_count($obitBody) . ' words transcribed from the clipping' . PHP_EOL;

$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
$asset = function ($src, $code, $title, $alt) use ($vol, $folder, $el, $sums, &$n) {
  $name = basename($src); $a = Asset::find()->volumeId($vol->id)->folderId($folder->id)->filename($name)->one(); if ($a) { return $a; }
  $tmp = sys_get_temp_dir() . "/$name"; copy($src, $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($name); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
  $a = Asset::find()->id($a->id)->one(); $a->title = $title; $a->alt = $alt;
  $h = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key(['photoSourceCode' => $code, 'legacySourcePath' => '/gif/' . $name, 'sourceChecksum' => 'sha256:' . $sums[$src], 'provenanceKind' => 'legacy-mirror', 'photoCredit' => 'Courtesy of Tricia Lemon Putnam'], array_flip($h)));
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++; return $a;
};
if (!$APPLY || $bad) { echo ($bad ? 'REFUSED: ' . implode(' | ', $bad) : 'nothing written') . PHP_EOL; return; }

$ca = $asset($CERT, 'TLP1601', 'William W. Jenkins, death certificate, 1916', 'Standard Certificate of Death of William W. Jenkins, California State Board of Health, 1916');
$oa = $asset($OBIT, 'TLP_LAT102016', 'William W. Jenkins, obituary, 1916', 'Newspaper clipping: California Loses an Old Resident');
if (!$cert) {
  $sec = Craft::$app->getEntries()->getSectionByHandle('documents'); $cert = new Entry(); $cert->sectionId = $sec->id; $cert->setTypeId($sec->getEntryTypes()[0]->id); $cert->title = $certTitle;
  $cert->setFieldValues(['featuredImage' => [$ca->id], 'webmasterNoteTop' => $top, 'webmasterNoteBottom' => $credit, 'subjectPerson' => [20226],
    'legacyKey' => 'tlp1601', 'legacyUrl' => '/scvhistory/tlp1601.htm', 'sourcePath' => 'https://scvhistory.com/scvhistory/tlp1601.htm']);
  if (!$el->saveElement($cert)) { throw new \RuntimeException('certificate ' . json_encode($cert->getFirstErrors())); } $n++;
}
if (!$obit) {
  $sec = Craft::$app->getEntries()->getSectionByHandle('obituaries'); $obit = new Entry(); $obit->sectionId = $sec->id; $obit->setTypeId(Craft::$app->getEntries()->getEntryTypeByHandle('obituary')->id); $obit->title = $obitTitle;
  $obit->setFieldValues(['featuredImage' => [$oa->id], 'body' => $obitBody, 'obitWebmasterNoteTop' => $obitTop, 'obitWebmasterNoteBottom' => $obitBottom,
    'publicationDetails' => 'Los Angeles Times, October 20, 1916, as the SCVHistory.com page gives it; the clipping prints no masthead or date. No byline.',
    'obitDatePublished' => 'October 20, 1916', 'obitDateOfDeath' => 'October 19, 1916', 'obitPublishedIn' => [30518], 'obitSubject' => [20226],
    'obitLegacyUrl' => '/scvhistory/tlp_lat102016.htm', 'legacyUrl' => '/scvhistory/tlp_lat102016.htm', 'legacyKey' => 'tlp_lat102016', 'sourcePath' => 'https://scvhistory.com/scvhistory/tlp_lat102016.htm']);
  if (!$el->saveElement($obit)) { throw new \RuntimeException('obituary ' . json_encode($obit->getFirstErrors())); } $n++;
}
$C = $cert->id; $O = $obit->id;
$add = [
  1 => " Second source: his obituary, Los Angeles Times, 20 October 1916 (archive obituary #$O): \"for six years, 1858-64, served as under sheriff\" and \"at the time of his death owned extensive lands in Castiac Canyon, where his home was located.\" It gives no year for his settling there; 1878 is Reynolds's alone.",
  5 => " His death certificate (archive document #$C) gives \"Date of birth October 12 1835\" and \"Birthplace Ohio\", the informant his daughter June J. Owens; his obituary (#$O): \"born at Circleville, O., in 1835.\"",
];
$fn6 = "His death certificate, California State Board of Health, Standard Certificate of Death, Local Registered No. 5087 (archive document #$C; the scan courtesy of Tricia Lemon Putnam, /scvhistory/tlp1601.htm): \"Date of death October 19 1916\", at 1823 S. Flower St., Los Angeles. His obituary, Los Angeles Times, 20 October 1916 (#$O): \"In the death yesterday of W. W. Jenkins, 81 years old\". Jerry Reynolds, record #2107 has him dying five years after a shooting in 1916, which the certificate contradicts.";
$rows = array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)($r['source'] ?? '')], iterator_to_array($J->footnotes));
foreach ($add as $k => $s) { if (!str_contains($rows[$k - 1]['note'], "(archive obituary #$O)") && !str_contains($rows[$k - 1]['note'], "(archive document #$C)")) { $rows[$k - 1]['note'] .= $s; } }
if (!str_starts_with($rows[5]['note'], 'The legacy page cites his death certificate') && $rows[5]['note'] !== $fn6) { throw new \RuntimeException('footnote 6 is not the text read'); }
$rows[5]['note'] = $fn6;
$J->setFieldValue('footnotes', $rows); $J->setFieldValue('deathEvidence', 'certified');
$J->setFieldValue('personObituaries', array_values(array_unique(array_merge($J->personObituaries->status(null)->ids(), [$O]))));
if (!$el->saveElement($J)) { throw new \RuntimeException('#20226 ' . json_encode($J->getFirstErrors())); } $n++;
$r = Entry::find()->id(20226)->status(null)->one(); $f = iterator_to_array($r->footnotes);
$ok = $f[5]['note'] === $fn6 && (string)$r->deathEvidence === 'certified' && in_array($O, $r->personObituaries->status(null)->ids());
echo "certificate #$C, obituary #$O, assets #{$ca->id} #{$oa->id}; #20226 read-back " . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('jenkins_certificate_obituary_2026_10_10.php', $n, $ok ? 'verified' : 'SHORT', "Jenkins's death certificate #$C and obituary #$O imported from Reggie; his record corrected");
