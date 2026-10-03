/**
 * Chris Trunkey #25409: a living person, so public-life facts only, two citations
 * where a second source exists, no birth date, nothing about family (Nathan,
 * 3 October 2026: "Use it as the primary and attribute anything that only
 * appears in the magazine piece"; "Leave out the wife, the children and the
 * email address").
 *
 * THE SOURCES, and what each may carry
 *   The Saugus Union School District's governing board page, read 3 October 2026
 *     (inventory/elections/saugus-board-2026-10-03.json keeps the names, areas
 *     and offices and his biography without its family sentence): the primary.
 *   KHTS (Perry Smith, hometownstation.com, as carried on SCVNews.com), 11 and
 *     19 November 2014: the vacancy, the four applicants, his appointment and
 *     swearing-in, West Creek's site council, CFO of Phoenix Pictures, and his
 *     2013 loss to Bryce "by 49 votes". Independent news.
 *   CEDA, the 2013, 2016, 2018 and 2022 files: the votes and ballot designations.
 *   SCVNews.com, 12 October 2012, from the Hart district: what the Measure V
 *     Citizens' Oversight Committee is (the 2001 bond, $158 million).
 *   Santa Clarita Magazine, 28 September 2022: campaign material, written for
 *     his campaign. Quoted and attributed only; nothing rests on it alone.
 *
 * THE RECORDS RECONCILED. The archive has him standing in 2013 and 2018; the
 *   district says appointed November 2014, re-elected 2016, 2018 and 2022.
 *   2013: the same man. KHTS says the appointee "was narrowly defeated by Bryce
 *     in last November's school board election"; CEDA's 2013 row is Chris
 *     Trunkey, designation Chief Financial Officer, fourth of five for three
 *     seats. He stood and lost, then was appointed. KHTS's margin (49) is not
 *     the final count's (2,102 - 2,044 = 58); the body gives both.
 *   2016: the district says re-elected; CEDA holds no Saugus Union contest that
 *     year but Trustee Area 3. Not resolved; the body shows both.
 *   2022: the archive holds the race (#25751) and his candidacy (#25753, as
 *     Christopher Trunkey) but the candidacy was never joined to him. Joined here.
 *
 * THE OFFICE. An officeHolding: School Board Member, Saugus Union School
 * District, Trustee Area 5, from 18 November 2014 (the Tuesday KHTS reports),
 * appointed, serving, contemporary evidence. Trustee areas came later than 2014,
 * so the area is the one he holds now (the district's page, CEDA 2018 and 2022).
 *
 * THE CROSS-LINK. personOrganizations gains the William S. Hart Union High School
 * District, for his seat on its Measure V Citizens' Oversight Committee; the
 * archive has no record for the committee, and the body names it.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_trunkey_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}", "\u{2013}"], ["'", "'", '"', '"', ' ', '-'], (string)$s)));
$ID = 25409; $p = $get($ID);
$SUSD = $get(21592); $HART = $get(21588); $TA5 = $get(25333); $ROLE = $get(26964); $C22 = $get(25753); $E22 = $get(25751);
$board = json_decode(file_get_contents("$root/inventory/elections/saugus-board-2026-10-03.json"), true);
$news = array_column(json_decode(file_get_contents("$root/inventory/news/trunkey-sources-2026-10-03.json"), true)['articles'], null, 'url');
$ceda = json_decode(file_get_contents("$root/inventory/elections/ceda-scv.json"), true)['rows'];
$row = fn($y, $last) => array_values(array_filter($ceda, fn($r) => $r['body'] === 'saugus-union-school-district' && $r['year'] === $y && $r['last'] === $last))[0] ?? null;
$K19 = 'https://scvnews.com/trunkey-to-fill-vacancy-on-saugus-school-board/'; $K11 = 'https://scvnews.com/2014/11/11/4-vie-for-vacant-seat-on-saugus-school-board/'; $MV = 'https://scvnews.com/3-spots-up-for-grabs-on-hart-bond-oversight-panel/';

$bad = [];
if (!$p || $p->title !== 'Chris Trunkey') { $bad[] = '#25409 is not Chris Trunkey'; }
if (!$SUSD || !$HART || !$TA5 || !$ROLE || $SUSD->title !== 'Saugus Union School District' || $HART->title !== 'William S. Hart Union High School District' || $TA5->title !== 'Saugus Union School District, Trustee Area 5' || $ROLE->title !== 'School Board Member') { $bad[] = 'the district, Hart, Trustee Area 5 or the role is not where expected'; }
if (!$C22 || $C22->candidacyElection->one()?->id !== 25751 || (int)$C22->votes !== 3183) { $bad[] = '#25753 is not his 2022 candidacy'; }
if ($C22 && ($x = $C22->candidacyPerson->one()) && $x->id !== $ID) { $bad[] = '#25753 is joined to someone else, #' . $x->id; }
/* Every phrase the text leans on, read from the saved sources. */
$MUST = [
    'board' => [$board['trunkeyBiography'], ['appointed to the SUSD Board of Trustees in November 2014 and reelected in 2016, 2018, and 2022', 'volunteer positions at both West Creek Academy and Bouquet Canyon Elementary, including site council, PTA executive board member, and longtime PTA volunteer', 'recognized by the California State PTA with its Honorary Service Award in 2013', 'delegate representing the Santa Clarita and Antelope Valleys to the California School Boards Association (CSBA) from 2021-2025', 'serving on the CSBA President\'s Legislative Committee in 2026', 'completed the CSBA Masters in Governance program in November 2017', 'finance professional in the entertainment industry for over 25 years', 'executive vice president and chief financial officer for Phoenix Pictures, a film production company based in Los Angeles', 'treasurer for the Stephen Friedman Multiple Myeloma Fund, board member of William S. Hart Baseball, and on the William S. Hart School District Measure v. Citizens Oversight Committee', 'graduate of Drake University', 'A longtime resident of Saugus']],
    'k19' => [$news[$K19]['text'] ?? '', ['named Chris Trunkey as the newest member of their governing board Tuesday, filling a seat left vacant by Doug Bryce\'s departure in September', 'Trunkey, a film executive who was narrowly defeated by Bryce in last November\'s school board election, was sworn in', 'Trunkey is on the site council for West Creek Academy', 'He was chosen over Fox, Julie Coate and Mathew Ferguson', 'Trunkey lost to Bryce by 49 votes last November', 'Trunkey is CFO for Phoenix Pictures']],
    'k11' => [$news[$K11]['text'] ?? '', ['Bruce Fox, Julie C. Coate, Mathew Clarke Ferguson and Christopher Trunkey applied for the vacancy']],
    'mv' => [$news[$MV]['text'] ?? '', ['Citizen\'s Oversight Committee formed to review expenditures of Measure V school bond funds', 'approved by local voters in 2001 for $158 million']],
];
foreach ($MUST as $k => [$t, $ps]) { foreach ($ps as $ph) { if (!str_contains($ws($t), $ws($ph))) { $bad[] = "$k does not read \"" . mb_substr($ph, 0, 60) . '"'; } } }
if (($news[$K19]['published'] ?? '') !== '2014-11-19T18:04:54+00:00') { $bad[] = 'KHTS 19 November is not dated as expected'; }
$r13 = $row(2013, 'Trunkey'); $b13 = $row(2013, 'Bryce'); $r18 = $row(2018, 'Trunkey'); $d18 = $row(2018, 'Duzick'); $r22 = $row(2022, 'Trunkey'); $d22 = $row(2022, 'Duzick');
$s16 = array_values(array_filter($ceda, fn($r) => $r['body'] === 'saugus-union-school-district' && $r['year'] === 2016));
if (!$r13 || $r13['votes'] !== 2044 || $r13['elected'] || $r13['designation'] !== 'Chief Financial Officer' || !$b13 || $b13['votes'] !== 2102 || !$b13['elected']) { $bad[] = 'CEDA 2013 does not read as the text says'; }
if (!$r18 || $r18['votes'] !== 3561 || !$r18['elected'] || $r18['area'] !== '5' || !$d18 || $d18['votes'] !== 3368) { $bad[] = 'CEDA 2018 does not read as the text says'; }
if (!$r22 || $r22['votes'] !== 3183 || !$r22['elected'] || $r22['first'] !== 'Christopher' || !$d22 || $d22['votes'] !== 2984) { $bad[] = 'CEDA 2022 does not read as the text says'; }
if (count($s16) !== 2 || array_unique(array_column($s16, 'area')) !== ['3']) { $bad[] = 'CEDA 2016 holds more than the Trustee Area 3 race'; }
$n13 = count(array_filter($ceda, fn($r) => $r['body'] === 'saugus-union-school-district' && $r['year'] === 2013));
if ($n13 !== 5 || $r13['seats'] !== 3) { $bad[] = 'CEDA 2013 is not five candidates for three seats'; }

$BOARDPAGE = 'Saugus Union School District, "Governing Board," https://www.saugususd.org/governing-board, read 3 October 2026';
$CEDA = fn($r) => "California Elections Data Archive (CEDA), {$r['file']}, row {$r['row']}";
$BODY = implode("\n\n", [
    'Chris Trunkey has sat on the governing board of the Saugus Union School District since November 18, 2014, when the board appointed him to the seat Doug Bryce had left vacant in September, choosing him over three other applicants.[1][2][3] He represents Trustee Area 5, which he won in November 2018, 3,561 votes to Sharlene Duzick\'s 3,368, and again in November 2022, 3,183 to 2,984.[4][5][2]',
    'He had first stood for the board in November 2013, when five candidates sought three seats. He came fourth with 2,044 votes, 58 behind Bryce in the final count; KHTS, reporting his appointment a year later, gave the margin as 49.[6][1] The district\'s biography says he was also re-elected in 2016. The County\'s returns for that year, as the California Elections Data Archive compiles them, hold no Saugus Union contest but the one for Trustee Area 3, so the archive has no record of a 2016 race for his seat.[2][7]',
    'The district\'s biography lists volunteer work at West Creek Academy and Bouquet Canyon Elementary, on the site council and the PTA executive board, and a California State PTA Honorary Service Award in 2013; KHTS noted his place on West Creek\'s site council when he was appointed.[2][1] He represented the Santa Clarita and Antelope valleys as a delegate to the California School Boards Association from 2021 to 2025, completed its Masters in Governance program in November 2017, and sits on its President\'s Legislative Committee in 2026.[2] He has also served on the William S. Hart Union High School District\'s Measure V Citizens\' Oversight Committee, which reviews spending of the $158 million school bond the high school district\'s voters approved in 2001, and as treasurer of the Stephen Friedman Multiple Myeloma Fund and a board member of William S. Hart Baseball.[2][8]',
    'By profession he is a finance executive in the entertainment industry, executive vice president and chief financial officer of Phoenix Pictures, a Los Angeles production company. He was its CFO when he was appointed in 2014, and stood in 2013 as "Chief Financial Officer."[2][1][6] He is a graduate of Drake University and a longtime resident of Saugus.[2] In his 2022 candidate statement in Santa Clarita Magazine he wrote that it had been "an honor to represent you on the Saugus Union School District Governing Board since 2014."[9]',
]);
$NOTES = [
    'Perry Smith, "Trunkey to Fill Vacancy on Saugus School Board," KHTS (hometownstation.com), November 19, 2014, as carried on SCVNews.com, ' . $K19 . ': "named Chris Trunkey as the newest member of their governing board Tuesday, filling a seat left vacant by Doug Bryce\'s departure in September."',
    $BOARDPAGE . ': "appointed to the SUSD Board of Trustees in November 2014 and reelected in 2016, 2018, and 2022." The district\'s own biography of him.',
    'Perry Smith, "4 Vie for Vacant Seat on Saugus School Board," KHTS (hometownstation.com), November 11, 2014, as carried on SCVNews.com, ' . $K11 . '.',
    $CEDA($r18) . ': Saugus Union School District, Trustee Area 5, November 6, 2018, elected, 3,561 votes; Sharlene Duzick 3,368 (row ' . $d18['row'] . ').',
    $CEDA($r22) . ': Saugus Union School District, Trustee Area 5, November 8, 2022, Christopher Trunkey, elected, 3,183 votes; Sharlene Rose Duzick 2,984 (row ' . $d22['row'] . ').',
    $CEDA($r13) . ': Saugus Union School District, November 5, 2013, three seats, not elected, 2,044 votes, ballot designation "Chief Financial Officer"; Douglas Bryce, elected third, 2,102 (row ' . $b13['row'] . ').',
    'California Elections Data Archive (CEDA), CEDA2016Data.xlsx, rows ' . implode(' and ', array_column($s16, 'row')) . ': the only Saugus Union School District contest of November 8, 2016, Trustee Area 3.',
    'William S. Hart Union High School District, as carried on SCVNews.com, October 12, 2012, ' . $MV . ': the "Citizen\'s Oversight Committee formed to review expenditures of Measure V school bond funds ... approved by local voters in 2001 for $158 million."',
    'Santa Clarita Magazine, "Meet the Candidate: Chris Trunkey, Running for Saugus Union School District," September 28, 2022, https://santaclaritamagazine.com/2022/09/meet-the-candidate-chris-trunkey-running-for-saugus-union-school-district-2/ (campaign material).',
];
$pageDoc = Entry::find()->section('documents')->status(null)->sourcePath('https://www.saugususd.org/governing-board')->one();
$holding = $p ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->one() : null;
$cur = trim((string)$p?->body);
echo '#25409 Chris Trunkey: body ' . ($cur === trim($BODY) ? 'already written' : ($cur ? 'NOT EMPTY, refusing' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes')) . PHP_EOL;
if ($cur && $cur !== trim($BODY)) { $bad[] = '#25409 has a body already'; }
echo ($pageDoc ? "#{$pageDoc->id} exists: " : 'create document: ') . 'the Saugus Union district\'s Governing Board page, read 3 October 2026' . PHP_EOL;
echo ($holding ? "#{$holding->id} exists: " : 'create officeHolding: ') . 'School Board Member, Saugus Union School District, Trustee Area 5, 2014-11-18 to now, appointed, contemporary' . PHP_EOL;
echo ($C22?->candidacyPerson->one() ? '#25753 already joined' : 'join candidacy #25753 (2022, Christopher Trunkey) to #25409') . PHP_EOL;
echo (in_array(21588, $p?->personOrganizations->ids() ?? []) ? 'Hart already related' : 'relate #21588 William S. Hart Union High School District (personOrganizations), for the Measure V committee') . PHP_EOL;
echo 'Left out: his wife and children, his email address. Resting on campaign material alone: nothing (the magazine is quoted, attributed).' . PHP_EOL;
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
preg_match_all('~\[(\d+)\]~', $BODY, $m); if (count(array_unique($m[1])) !== count($NOTES)) { $bad[] = 'notes used ' . json_encode(array_values(array_unique($m[1]))) . ' of ' . count($NOTES); }
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$pageDoc) {
        $pageDoc = new Entry(); $ds = $svc->getSectionByHandle('documents'); $pageDoc->sectionId = $ds->id; $pageDoc->setTypeId($svc->getEntryTypeByHandle('document')->id);
        $pageDoc->title = 'Saugus Union School District: Governing Board (web page, read 3 October 2026)';
        $pageDoc->setFieldValues(['sourcePath' => 'https://www.saugususd.org/governing-board', 'sourceLine' => 'Saugus Union School District', 'originalPublishDate' => 'read 3 October 2026', 'originalPublishDateEdtf' => '2026-10-03', 'publishedBy' => [$SUSD->id],
            'body' => 'The district\'s own list of its board, with a biography of most trustees. Read on 3 October 2026 it named ' . implode('; ', array_map(fn($m) => "{$m['name']}, Trustee Area {$m['area']}" . ($m['office'] !== 'Member' ? ", {$m['office']}" : ''), $board['members'])) . '. The archive keeps the names, areas and offices, and Christopher Trunkey\'s biography without its family sentence (inventory/elections/saugus-board-2026-10-03.json), not the contact details or the other biographies.',
            'recordProvenance' => 'build_trunkey_profile.php, 3 October 2026: page SHA-256 ' . $board['htmlSha256']]);
        if (!$elements->saveElement($pageDoc)) { throw new \RuntimeException('page document: ' . json_encode($pageDoc->getFirstErrors())); }
    }
    $p = $get($ID);
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'Film finance executive; school board member', 'fullName' => 'Christopher Trunkey', 'personAliases' => 'Christopher Trunkey',
        'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [$SUSD->id, $HART->id]))),
        'recordProvenance' => trim((string)$p->recordProvenance . '; build_trunkey_profile.php, 3 October 2026: body, office, the 2022 candidacy joined, the Hart district related; public-life facts only')];
    $p->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($p)) { throw new \RuntimeException('#25409: ' . json_encode($p->getFirstErrors())); }
    if (!$C22->candidacyPerson->one()) { $C22->setFieldValue('candidacyPerson', [$ID]); if (!$elements->saveElement($C22)) { throw new \RuntimeException('#25753: ' . json_encode($C22->getFirstErrors())); } }
    if (!$holding) {
        $holding = new Entry(); $os = $svc->getSectionByHandle('officeHoldings'); $holding->sectionId = $os->id; $holding->setTypeId($svc->getEntryTypeByHandle('officeHolding')->id);
        $holding->setFieldValues(['holdingPerson' => [$ID], 'holdingOffice' => [$ROLE->id], 'holdingBody' => [$SUSD->id], 'holdingDistrict' => [$TA5->id],
            'termStart' => 'November 18, 2014', 'termStartEdtf' => '2014-11-18', 'selectionMethod' => 'appointed', 'howEnded' => 'serving', 'startEvidence' => 'contemporary',
            'footnotes' => $fn([$NOTES[0], $NOTES[1], 'Trustee Area 5 is the seat he has held since the 2018 election (CEDA 2018 and 2022, and the district\'s page); his 2014 appointment filled an at-large seat.']),
            'recordProvenance' => 'build_trunkey_profile.php, 3 October 2026']);
        if (!$elements->saveElement($holding)) { throw new \RuntimeException('holding: ' . json_encode($holding->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($ID);
$ok = trim((string)$r->body) === trim($BODY) && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->exists() && $get(25753)->candidacyPerson->one()?->id === $ID && in_array(21588, $r->personOrganizations->ids());
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_trunkey_profile.php', 4, $ok ? 'verified' : 'SHORT', 'Chris Trunkey: profile, office, 2022 candidacy, Hart district');
if (!$ok) { throw new \RuntimeException('build_trunkey_profile: read-back failed'); }
