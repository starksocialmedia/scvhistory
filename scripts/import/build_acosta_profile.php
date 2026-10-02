/**
 * Dante Acosta #341: Santa Clarita City Council 2014-2016, State Assembly
 * 2016-2018 (Nathan, 1 October 2026). A living person: public life only, year of
 * birth at most, no family. The WordPress body gave his full birth date and his
 * wife, children and son's death; all of it goes.
 *
 * THE SOURCES
 *   [SC1401]  Leon Worden's page SC1401, with his note on Acosta's career and
 *             the City's 2013 biography: imported here as a photograph record
 *             (Gary Choppé's portrait), its family sentences left out and the
 *             cuts marked. Mirror copy matches the manifest of 20 August 2026.
 *   [ARCHIVE] The council election of 8 April 2014.
 *   [BALLOTPEDIA] Assembly District 38, the returns of 2016 and 2018 (citing the
 *             Secretary of State), committees, the 2012 congressional primary.
 *   [CITY]    The City's page for Bill Miranda, who took Acosta's seat.
 *   [IMDB]    Acting credits.
 *   Wikipedia was a finding aid only.
 *
 * Kept visible: the record's birthplace, Sacramento, has no source, and the
 * City's biography says he was born and raised in Southern California; the
 * birthplace is cleared with a note. Leon gives 3 January 1963 and the record
 * 1 January; the year alone is kept.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_acosta_profile.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 341; $CAND = 22278; $SCAN = "$root/inventory/legacy/fetched/sc1401.jpg"; $SCAN_SHA = '332d8472e44648d24435e82c73183b1a1d65171b222bb0d52c8f20361dd74853';
$p = Entry::find()->id($ID)->status(null)->one();
$sc = $ws(@file_get_contents("$root/inventory/legacy/fetched/sc1401.txt"));
$j = json_decode((string)@file_get_contents("$root/inventory/sources/dante-acosta-2026-10-02.json"), true)['sources'] ?? [];
$miranda = json_decode((string)@file_get_contents("$root/inventory/sources/santa-clarita-council-2026-10-01.json"), true)['sources']['miranda']['passage'] ?? '';
$src = ['sc1401' => $sc, 'bp' => $ws($j['ballotpedia']['passage'] ?? ''), 'imdb' => $ws($j['imdb']['passage'] ?? ''), 'city' => $miranda];
$bad = [];
if (!$p || $p->title !== 'Dante Acosta') { $bad[] = '#341 is not Dante Acosta'; }
if (!is_file($SCAN) || hash_file('sha256', $SCAN) !== $SCAN_SHA) { $bad[] = 'the SC1401 scan is missing or not the mirror\'s'; }
$c = Entry::find()->id($CAND)->status(null)->one();
$field = $c ? Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $c->candidacyElection->one(), 'field' => 'candidacyElection'])->orderBy('votes desc')->ids() : [];
if (!$c || (int)$c->votes !== 4937 || array_search($CAND, $field) !== 2 || count($field) !== 13) { $bad[] = 'the 2014 candidacy is not 4,937 votes, third of thirteen'; }
$MUST = [
    'sc1401' => ['Dante Acosta (b. Jan. 3, 1963)', 'Santa Clarita City Council member, 2014-2016. Elected November 2016 to the state Assembly, lost reelection in 2018.', 'Appointed January 2019 to SCV Water Agency board; resigned in August 2019 and moved to Texas to take position as district director of U.S. Small Business Administration in El Paso region.', 'He was born and raised in Southern California', 'new car salesperson at a San Fernando Valley Chevrolet dealership', 'General Sales Manager', 'Dean Witter office, serving as a Financial Advisor', 'Wells Fargo Investments', 'Prudential Financial, The Hartford, ING Financial Services, and Ameriprise Financial', 'docents at the William S. Hart Museum', 'Santa Clarita Valley Rotary Club and Old Town Newhall Association, been a board member with Circle of Hope Inc., and coached little league and drama', 'North Los Angeles County Republican National Hispanic Assembly', 'legislative chair of the National Association of Insurance and Financial Advisors in Los Angeles', 'appointed by Assemblyman Scott Wilk as a delegate to the State Republican Party', 'SC1401: 19200 dpi jpeg from digital image by Gary Choppé.'],
    'bp' => ['He left office on December 3, 2018', 'Acosta ran unsuccessfully for the 25th Congressional District of California in 2012', 'Natural Resources, Vice chair', 'Christy Smith (D) 51.5 95,751; Dante Acosta (R) 48.5 90,298', 'Incumbent Scott Wilk (R) did not seek re-election', 'Republican Dante Acosta 52.87% 102,977; Democratic Christy Smith 47.13% 91,801', 'Source: California Secretary of State', 'He was defeated in the open primary on June 5, 2012'],
    'imdb' => ['A Deadly Dance (2019), The Rally (2010) and Big Time Rush (2009)'],
    'city' => ['appointed to the council seat vacated by Dante Acosta in January 2017'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k], $ws($ph))) { $bad[] = "$k does not read \"$ph\""; } } }

/* The photograph record: Leon's note and the City's 2013 biography, family sentences cut and marked. */
$CAPTION = [
    'Dante Acosta (b. Jan. 3, 1963). Resident of Canyon Country. Santa Clarita City Council member, 2014-2016. Elected November 2016 to the state Assembly, lost reelection in 2018.',
    'Appointed January 2019 to SCV Water Agency board; resigned in August 2019 and moved to Texas to take position as district director of U.S. Small Business Administration in El Paso region.',
    '[City of Santa Clarita 2013] Dante Acosta joined the City Council in 2014. He was born and raised in Southern California [...]',
    'Dante began working while in high school, serving as an assistant manager of an auto parts store. While attending California State University, Northridge, Dante worked as a new car salesperson at a San Fernando Valley Chevrolet dealership. Throughout his tenure with the dealership, Dante worked his way to the top of the organization serving as General Sales Manager. Under his leadership, his dealership was routinely ranked number one in customer satisfaction in Southern California.',
    'After years in the auto industry, Dante began his career in financial services with the local Dean Witter office, serving as a Financial Advisor. There he earned his Series 7, Series 63 and Insurance licenses. Dante was recruited three years later to Wells Fargo Investments. He went on to serve in various management roles with Prudential Financial, The Hartford, ING Financial Services, and Ameriprise Financial, using his 20 years of experience to advise individuals and small-business owners on their insurance, investments, financial, retirement and estate planning needs.',
    '[...] Dante has served as a member of the Santa Clarita Valley Rotary Club and Old Town Newhall Association, been a board member with Circle of Hope Inc., and coached little league and drama. Dante is a member of the North Los Angeles County Republican National Hispanic Assembly, served as the former legislative chair of the National Association of Insurance and Financial Advisors in Los Angeles, and was appointed by Assemblyman Scott Wilk as a delegate to the State Republican Party.',
    '[Sentences about his family are omitted from this archive\'s copy: SCVHistory.com publishes the public life of living people only.]',
];
foreach (array_slice($CAPTION, 0, 6) as $para) { foreach (array_filter(array_map('trim', explode('[...]', preg_replace('~^\[City of Santa Clarita 2013\] ~', '', $para)))) as $piece) { if (!str_contains($sc, $ws($piece))) { $bad[] = 'the caption is not verbatim: ' . mb_substr($piece, 0, 60); } } }

$BODY = implode("\n\n", [
    'Dante Acosta was a member of the Santa Clarita City Council from 2014 to 2016 and represented the 38th District in the California State Assembly from 2016 to 2018.[1][3]',
    'He was elected to the council on 8 April 2014, third of thirteen candidates with 4,937 votes.[2] He left it on his election to the Assembly, and in January 2017 the council appointed Bill Miranda to the seat he had vacated.[1][4]',
    'In 2016 the 38th District\'s assemblyman, Scott Wilk, did not seek re-election, and Acosta won the seat in November, defeating Christy Smith with 102,977 votes to her 91,801. In the 2017 session he was vice chair of the Natural Resources Committee. Smith defeated him in November 2018, 95,751 to 90,298, and he left office on 3 December 2018. He had run for Congress in 2012, in the 25th District, and lost in the primary.[3][1]',
    'Leon Worden\'s note records that he was appointed to the board of the SCV Water Agency in January 2019, resigned that August, and moved to Texas as district director of the U.S. Small Business Administration for the El Paso region.[1]',
    'The City\'s biography of 2013 says he worked his way up at a San Fernando Valley Chevrolet dealership to general sales manager while attending California State University, Northridge, then spent some twenty years in financial services: as a financial advisor with Dean Witter, then with Wells Fargo Investments, and in management with Prudential Financial, The Hartford, ING Financial Services and Ameriprise Financial. It lists his civic work, including docent service at the William S. Hart Museum, the Santa Clarita Valley Rotary Club, the Old Town Newhall Association, the board of Circle of Hope, coaching little league and drama, the North Los Angeles County Republican National Hispanic Assembly, and appointment by Assemblyman Scott Wilk as a delegate to the State Republican Party.[1] IMDb credits him with acting roles, among them A Deadly Dance (2019) and The Rally (2010).[5]',
]);
$NOTES = [
    'Leon Worden\'s note and the City of Santa Clarita\'s biography of 2013, on SCVHistory.com page SC1401, https://scvhistory.com/scvhistory/sc1401.htm. Archive record: "Dante Acosta, Santa Clarita City Council, 2014."',
    'Archive record: "City Council election, April 8, 2014," with its returns.',
    'Ballotpedia, "Dante Acosta," https://ballotpedia.org/Dante_Acosta, read 2 October 2026; the returns are the California Secretary of State\'s.',
    'City of Santa Clarita, "Councilmember Bill Miranda," https://santaclarita.gov/city-council/bill-miranda/, read 1 October 2026.',
    'IMDb, "Dante Acosta (I)," https://www.imdb.com/name/nm3408102/, read 2 October 2026.',
];
$BIO = 'Dante Acosta was a Santa Clarita City Council member from 2014 to 2016 and represented the 38th District in the California State Assembly from 2016 to 2018.';
$EDITOR = [['heading' => 'Birthplace', 'position' => 'bottom', 'note' => 'This record came from WordPress with "Sacramento, California," for which no source has been found; the City\'s 2013 biography says he was born and raised in Southern California. The birthplace is left empty until a source settles it.']];
$row = fn(string $printed, string $iso, string $gran, string $label): array => ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];
$DATES = [$row('April 8, 2014', '2014-04-08', 'day', 'elected to the Santa Clarita City Council'), $row('November 8, 2016', '2016-11-08', 'day', 'elected to the State Assembly, 38th District'),
    $row('December 3, 2018', '2018-12-03', 'day', 'leaves the Assembly')];
if (preg_match('~\x{2014}~u', $BODY . $BIO . implode('', $NOTES) . implode('', $CAPTION) . json_encode([$EDITOR, $DATES], JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }
if (preg_match('~\b(Carolyn|Rudy|Alexandra|Doran|wife|son|children|Gold Star|Rodolfo)\b~i', $BODY . $BIO)) { $bad[] = 'family detail in the body'; }
$cur = trim((string)$p?->body); $isOld = str_starts_with(trim(strip_tags($cur)), 'Dante Acosta (b. January 1, 1963)'); $isNew = $cur === trim($BODY);
if (!$isOld && !$isNew) { $bad[] = '#341\'s body has been edited since the WordPress import'; }
$photo = Entry::find()->section('photographs')->status(null)->legacyKey('sc1401')->one();
echo ($photo ? "#{$photo->id} exists" : 'create photograph SC1401 with the scan, Leon\'s note and the City\'s 2013 biography (family cut, marked)') . PHP_EOL;
echo '#341 body: ' . ($isNew ? 'already the profile' : 'WordPress body (full birth date, wife, children, son) -> public-life profile (' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes)') . '; birthDate "' . $p?->birthDate . '" -> "1963"; birthplace cleared; authorBio without family; ' . array_sum(array_map('count', $MUST)) . ' phrases checked' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$photo) {
        $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
        $tmp = sys_get_temp_dir() . '/sc1401.jpg'; copy($SCAN, $tmp);
        $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename('sc1401.jpg'); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
        $a->setFieldValues(['provenanceKind' => 'legacy-mirror', 'legacySourcePath' => 'gif/sc1401.jpg', 'creator' => 'Gary Choppé', 'acquiredDate' => '2026-10-02',
            'source' => "SCVHistory.com, gif/sc1401.jpg, from the legacy mirror of 20 August 2026, SHA-256 $SCAN_SHA, matching the manifest; the stored copy is re-encoded on import."]);
        if (!$elements->saveElement($a)) { throw new \RuntimeException('scan: ' . json_encode($a->getFirstErrors())); }
        $photo = new Entry(); $photo->sectionId = $svc->getSectionByHandle('photographs')->id; $photo->setTypeId($svc->getEntryTypeByHandle('photograph')->id); $photo->title = 'Dante Acosta, Santa Clarita City Council, 2014';
        $h = array_map(fn($f) => $f->handle, $photo->getFieldLayout()->getCustomFields());
        $photo->setFieldValues(array_intersect_key(['featuredImage' => [$a->id], 'body' => implode("\n\n", $CAPTION), 'photoCredit' => 'Gary Choppé', 'creditName' => 'Gary Choppé', 'photoSourceCode' => 'SC1401',
            'creditRaw' => 'SC1401: 19200 dpi jpeg from digital image by Gary Choppé.', 'creditDpi' => '19200', 'photoPeople' => [$ID], 'legacyKey' => 'sc1401', 'legacyUrl' => '/scvhistory/sc1401.htm', 'sourcePath' => 'https://scvhistory.com/scvhistory/sc1401.htm'], array_flip($h)));
        if (!$elements->saveElement($photo)) { throw new \RuntimeException('photograph: ' . json_encode($photo->getFirstErrors())); }
    }
    if (!$isNew) {
        $s = Entry::find()->id($ID)->status(null)->one(); $h = array_map(fn($f) => $f->handle, $s->getFieldLayout()->getCustomFields());
        $s->setFieldValues(array_intersect_key(['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'birthDate' => '1963', 'birthDateEdtf' => '1963', 'birthplace' => '', 'birthEvidence' => 'contemporary',
            'authorBio' => $BIO, 'recordDates' => $DATES, 'occupation' => 'Politician; financial advisor',
            'editorNotes' => array_merge(array_values(array_filter($s->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')), $EDITOR),
            'recordProvenance' => trim((string)$s->recordProvenance . '; build_acosta_profile.php, 2 Oct 2026: public-life profile; SC1401 imported; WordPress family detail and full birth date removed', '; ')], array_flip($h)));
        if (!$elements->saveElement($s)) { throw new \RuntimeException('#341: ' . json_encode($s->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$s = Entry::find()->id($ID)->status(null)->one(); $ph = Entry::find()->section('photographs')->status(null)->legacyKey('sc1401')->one();
$ok = trim((string)$s->body) === trim($BODY) && (string)$s->birthDate === '1963' && $ph && $ph->featuredImage->one() && in_array($ID, $ph->photoPeople->status(null)->ids());
echo 'READ-BACK ' . ($ok ? 'OK: ' . $s->url . ' and ' . $ph->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_acosta_profile.php', 2, $ok ? 'verified' : 'SHORT', 'Dante Acosta: public-life profile; SC1401 as a photograph record');
if (!$ok) { throw new \RuntimeException('build_acosta_profile: read-back failed'); }
