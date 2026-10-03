/**
 * Brian Walters #25391: a living person, so public-life facts only, two citations
 * where a second source exists, no birth date, nothing about family (Nathan,
 * 3 October 2026: "Thirteen years on the Newhall board when our records had him
 * as a candidate twice is exactly the undercount problem. Note on his record
 * that the district bio is self-written, so facts resting on it alone are
 * attributed").
 *
 * THE SOURCES (inventory/news/walters-2026-10-03/, manifest.json with hashes)
 *   The Signal, 13 December 2017, 12 December 2019, 15 December 2022 and
 *     13 August 2018, read from Wayback Machine captures (signalscv.com refuses
 *     automated reads; reading Wayback is fine, AGENTS.md).
 *   KHTS via SCVNews.com, 25 December 2013.
 *   The district's release of 4 November 2009 (SCVTV's copy): the vacancy.
 *   The County's 2018 list of cancelled elections: Newhall Trustee Area 1.
 *   CEDA 2013 and 2022: the two contested elections.
 *   The district's biography of him, Wayback captures of 5 March 2021 and
 *     8 November 2022. Published by the district, most likely supplied by him:
 *     what only it says is attributed in the text, never stated.
 *   Santa Clarita Magazine, 28 September 2022: campaign material; not relied on.
 *
 * THE OFFICE. An officeHolding: School Board Member, Newhall School District,
 * December 2009 (appointed) to December 2022 (lost the election), Trustee Area 1
 * from 2018. One tenure: the sources give it whole ("the last 13 years").
 * Unresolved, and said so in a footnote: how a 2009 appointment, which ran to the
 * next election (November 2011, by the district's release), carried on to 2013;
 * CEDA holds no Newhall rows for 2011.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_walters_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $D = "$root/inventory/news/walters-2026-10-03";
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], (string)$s)));
$ID = 25391; $p = $get($ID); $NSD = $get(21590); $ROLE = $get(26964); $TA1 = $get(25335);
$M = json_decode(file_get_contents("$D/manifest.json"), true)['files'];
$bad = [];
if (!$p || $p->title !== 'Brian Walters') { $bad[] = '#25391 is not Brian Walters'; }
if (!$NSD || $NSD->title !== 'Newhall School District' || !$ROLE || $ROLE->title !== 'School Board Member' || !$TA1 || $TA1->title !== 'Newhall School District, Trustee Area 1') { $bad[] = 'the district, the role or Trustee Area 1 is not where expected'; }
$T = [];
foreach ($M as $f => $m) { if (hash_file('sha256', "$D/$f") !== $m['sha256']) { $bad[] = "$f has changed"; continue; } $T[$f] = $ws(@file_get_contents("$D/" . preg_replace('~\.[a-z]+$~', '.txt', $f))); }
$MUST = [
    'wb_signal_20171214.html' => ['This is the second time Walters, first appointed in December 2009, will serve as clerk of the board. In the past he also served as the board\'s president', 'Walters works as vice president and general counsel for Cash Technologies, Inc. and Prime Asset Fund III, LLC', 'He is an active member of the Pico Canyon PTA', 'acts as a coach for youth baseball, basketball and soccer'],
    'scvtv_nsd110409.html' => ['J. Michael McGrath, Jr. won re-election to the Newhall board even though he stated prior to the election that he would be unable to serve', 'An appointed member serves until the next regularly scheduled election which will occur in November 2011'],
    'news_4.html' => ['Walters replaces Ellis as president of the Newhall School District board', 'Walters joined the board in 2009', 'He served as the board\'s clerk throughout 2013', 'He has lived in Santa Clarita since 2003', 'He is an active member of the Pico Canyon PTA'],
    'wb_signalscv.com_2018_08_few-challengers-in-school-board-races.html' => ['In the Newhall School District, Brian Walters and Isaiah Talley will claim their seats in the wake of no opposition'],
    'lavote_cancelled_2018.pdf' => ['Newhall School'],
    'wb_signal_20191213.html' => ['Board member Brian Walters was re-elected as clerk'],
    'wb_signal_20221216.html' => ['She won the seat from long-time board member Brian Walters after a tight race in the General Election', 'I\'m grateful to have served in this privileged opportunity for the last 13 years'],
    'wb_nsd_domain11_20221108075734.html' => ['Brian Walters has served on the Newhall School Governing Board since 2009', 'He is now a partner at the law firm of Poole, Shaffery & Koegle, LLP', 'He is the co-founder of the William S. Hart District (WiSH) Education Foundation', 'also serving two terms as Board Chairman', 'the first-ever graduate of the Bachelor of Arts in political science and Master of Public Administration joint degree at The George Washington University in 2000', 'He is a 2003 graduate of the UCLA School of Law', 'recognized in 2010 by the Santa Clarita Valley Business Journal and SCV Jaycees as a 40 Under Forty Honoree', 'Brian served for several years on the Executive Board of the Pico Canyon PTA', 'Current Term: 2018 - 2022'],
    'wb_nsd_domain11_20210305.html' => ['Governing Board President'],
];
foreach ($MUST as $f => $ps) { foreach ($ps as $ph) { if (!str_contains($T[$f] ?? '', $ws($ph))) { $bad[] = "$f does not read \"" . mb_substr($ph, 0, 60) . '"'; } } }
if (!preg_match('~Newhall School \(Trustee Areas 1, 3, and 4~', $T['lavote_cancelled_2018.pdf'] ?? '')) { $bad[] = 'the 2018 cancelled list does not name Newhall Trustee Areas 1, 3 and 4'; }
$ceda = json_decode(file_get_contents("$root/inventory/elections/ceda-scv.json"), true)['rows'];
$row = fn($y, $last) => array_values(array_filter($ceda, fn($r) => $r['body'] === 'newhall-school-district' && $r['year'] === $y && $r['last'] === $last))[0] ?? null;
$w13 = $row(2013, 'Walters'); $w22 = $row(2022, 'Walters'); $r22 = $row(2022, 'Robert');
if (!$w13 || !$w13['elected'] || $w13['votes'] !== 1915 || !$w22 || $w22['elected'] || $w22['votes'] !== 2015 || $w22['area'] !== '1' || !$r22 || $r22['votes'] !== 2028) { $bad[] = 'CEDA does not read as the text says'; }
$c = fn($f) => $M[$f]['cite'] . ', ' . $M[$f]['url'];
$BODY = implode("\n\n", [
    'Brian Walters served on the governing board of the Newhall School District for thirteen years, from December 2009 to December 2022. He was appointed to the seat J. Michael McGrath, Jr. left after winning re-election in November 2009 while saying he could not serve, was elected at large in November 2013, and in 2018 kept his seat, now Trustee Area 1, unopposed: the County cancelled the election for want of candidates.[1][2][3][4][5][6] In November 2022 he lost Trustee Area 1 to Donna Robert, 2,028 votes to 2,015, and left the board that December, "grateful to have served in this privileged opportunity for the last 13 years."[7][8]',
    'He was the board\'s president for 2014 and again in 2021, and its clerk in 2013, 2018 and 2020.[3][1][9][10]',
    'By profession he is a lawyer. In 2017 The Signal described him as vice president and general counsel of Cash Technologies, Inc. and Prime Asset Fund III, LLC; by 2022 he was a partner at Poole, Shaffery & Koegle.[1][11] He has lived in the Santa Clarita Valley since 2003, served on the executive board of the Pico Canyon PTA, and coached youth sports.[3][1][11]',
    'The district\'s biography of him, which he most likely supplied, adds what no other source in hand confirms: that he co-founded the William S. Hart District (WiSH) Education Foundation and twice chaired its board, that he holds a joint BA and MPA from The George Washington University (2000) and a law degree from UCLA (2003), and that the Santa Clarita Valley Business Journal and SCV Jaycees named him a 40 Under Forty honoree in 2010.[11]',
]);
$NOTES = [
    $c('wb_signal_20171214.html') . ': "first appointed in December 2009" and "In the past he also served as the board\'s president."',
    $c('scvtv_nsd110409.html') . ': McGrath "won re-election to the Newhall board even though he stated prior to the election that he would be unable to serve." The release adds that "an appointed member serves until the next regularly scheduled election which will occur in November 2011"; how his appointment carried on to the 2013 election is not established.',
    $c('news_4.html') . ': "Walters joined the board in 2009," was "re-elected in November," "served as the board\'s clerk throughout 2013," and "replaces Ellis as president" for 2014.',
    'California Elections Data Archive (CEDA), CEDA2013Data.xlsx, row ' . $w13['row'] . ': Newhall School District, November 5, 2013, three seats at large, elected, 1,915 votes, ballot designation "Governing Board Member."',
    $c('wb_signalscv.com_2018_08_few-challengers-in-school-board-races.html') . ': "Brian Walters and Isaiah Talley will claim their seats in the wake of no opposition."',
    $c('lavote_cancelled_2018.pdf') . ': Newhall School, Trustee Areas 1, 3 and 4.',
    'California Elections Data Archive (CEDA), CEDA2022Data.xlsx, rows ' . $w22['row'] . ' and ' . $r22['row'] . ': Newhall School District, Trustee Area 1, November 8, 2022: Donna Michelle Robert 2,028, Brian D. Walters 2,015.',
    $c('wb_signal_20221216.html') . ': Robert "won the seat from long-time board member Brian Walters after a tight race."',
    $c('wb_nsd_domain11_20210305.html') . ': "Governing Board President."',
    $c('wb_signal_20191213.html') . ': "Board member Brian Walters was re-elected as clerk."',
    $c('wb_nsd_domain11_20221108075734.html') . '. Published by the district; most likely supplied by him. What only it says is attributed in the text above.',
];
$holding = $p ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->one() : null;
$cur = trim((string)$p?->body);
echo '#25391 Brian Walters: body ' . ($cur === trim($BODY) ? 'already written' : ($cur ? 'NOT EMPTY, refusing' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes')) . PHP_EOL;
if ($cur && $cur !== trim($BODY)) { $bad[] = '#25391 has a body already'; }
echo ($holding ? "#{$holding->id} exists: " : 'create officeHolding: ') . 'School Board Member, Newhall School District, Trustee Area 1 (from 2018), December 2009 (appointed) to December 2022 (lost)' . PHP_EOL;
echo 'Attributed to the district biography alone: WiSH, the degrees, 40 Under Forty. Left out: family, Gibson Dunn, Eagle Scout, the 2014 Assembly honour, the campaign\'s claims.' . PHP_EOL;
$PROV = '; build_walters_profile.php, 3 Oct 2026: profile and office';
if (mb_strlen(trim((string)$p?->recordProvenance . $PROV)) > 255) { $bad[] = 'recordProvenance would exceed 255 characters'; }
if (preg_match('~\x{2014}|inventory/~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash or a repository path in the text'; }
preg_match_all('~\[(\d+)\]~', $BODY, $m); if (count(array_unique($m[1])) !== count($NOTES)) { $bad[] = 'notes used ' . json_encode(array_values(array_unique($m[1]))) . ' of ' . count($NOTES); }
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    $p = $get($ID);
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'Lawyer; school board member',
        'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [$NSD->id]))),
        'recordProvenance' => trim((string)$p->recordProvenance . $PROV)];
    $p->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($p)) { throw new \RuntimeException('#25391: ' . json_encode($p->getFirstErrors())); }
    if (!$holding) {
        $holding = new Entry(); $os = $svc->getSectionByHandle('officeHoldings'); $holding->sectionId = $os->id; $holding->setTypeId($svc->getEntryTypeByHandle('officeHolding')->id);
        $holding->setFieldValues(['holdingPerson' => [$ID], 'holdingOffice' => [$ROLE->id], 'holdingBody' => [$NSD->id], 'holdingDistrict' => [$TA1->id],
            'termStart' => 'December 2009', 'termStartEdtf' => '2009-12', 'termEnd' => 'December 2022', 'termEndEdtf' => '2022-12', 'selectionMethod' => 'appointed', 'howEnded' => 'expired', 'startEvidence' => 'retrospective', 'endEvidence' => 'contemporary',
            'footnotes' => $fn([$NOTES[0], $NOTES[1], $NOTES[5] . ' He held Trustee Area 1 from 2018; before then the board was elected at large.', $NOTES[6], $NOTES[7]]),
            'recordProvenance' => 'build_walters_profile.php, 3 October 2026']);
        if (!$elements->saveElement($holding)) { throw new \RuntimeException('holding: ' . json_encode($holding->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($ID);
$ok = trim((string)$r->body) === trim($BODY) && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->exists();
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_walters_profile.php', 2, $ok ? 'verified' : 'SHORT', 'Brian Walters: profile and office, 2009 to 2022');
if (!$ok) { throw new \RuntimeException('build_walters_profile: read-back failed'); }
