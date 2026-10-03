/**
 * Paul De La Cerda #25407: a living person, so public-life facts only. Nathan,
 * 3 October 2026, on the court record: "include it, factually and in one short
 * paragraph at the end ... it was at his college job, not on the school board;
 * the charge was misappropriation and embezzlement and the plea was to grand
 * theft, which is a real difference; and KHTS is the source, so attribute it.
 * Do not let it lead the profile. He served on the Saugus Union board including
 * as president and that is why he has a record here."
 *
 * THE SOURCES (inventory/news/de-la-cerda-2026-10-03/, manifest.json)
 *   The district, via SCVNews.com, 20 December 2013: third term, president for 2014.
 *   The Signal, 14 August 2018 (Wayback capture): 13 years, first elected
 *     November 2005, clerk, not standing again; his work in education since 2004.
 *   KHTS, 11 November 2014 (inventory/news/trunkey-sources-2026-10-03.json):
 *     "board President Paul De La Cerda".
 *   KHTS, 9 December 2021 and 10 June 2022: the charges and the plea.
 *   CEDA 2005 and 2013: the two contested elections; the 2009 seat has no row
 *     (uncontested), and "third term" in December 2013 accounts for it.
 *
 * THE OFFICE. School Board Member, Saugus Union School District, December 2005
 * (elected) to December 2018 (did not stand; "expired"), at large.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_de_la_cerda_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $D = "$root/inventory/news/de-la-cerda-2026-10-03";
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], (string)$s)));
$ID = 25407; $p = $get($ID); $SUSD = $get(21592); $ROLE = $get(26964);
$M = json_decode(file_get_contents("$D/manifest.json"), true)['files'];
$bad = [];
if (!$p || $p->title !== 'Paul de la Cerda') { $bad[] = '#25407 is not Paul de la Cerda'; }
if (!$SUSD || $SUSD->title !== 'Saugus Union School District' || !$ROLE || $ROLE->title !== 'School Board Member') { $bad[] = 'the district or the role is not where expected'; }
$T = [];
foreach ($M as $f => $m) { if (hash_file('sha256', "$D/$f") !== $m['sha256']) { $bad[] = "$f has changed"; continue; } $T[$f] = $ws(file_get_contents("$D/" . substr($f, 0, -5) . '.txt')); }
$k14 = array_values(array_filter(json_decode(file_get_contents("$root/inventory/news/trunkey-sources-2026-10-03.json"), true)['articles'], fn($a) => str_contains($a['url'], '4-vie')))[0] ?? null;
$T['k14'] = $ws($k14['text'] ?? '');
$MUST = [
    'scvnews-2013-12-20-president.html' => ['De la Cerda begins his third term and was elected to the position of Board President for 2014'],
    'signal-2018-08-14-wont-seek.html' => ['After 13 years of service, Paul De La Cerda has announced that he will not seek re-election in November to the Saugus Union School District Governing Board', 'He made his decision after receiving admission into the organizational change and leadership doctorate program at the University of Southern California', 'Since 2004, De La Cerda has worked in the area of education as a high school STEM instructor, college professor and college administrator', 'He was first elected to the board in November 2005, and currently serves as clerk of the board'],
    'k14' => ['said board President Paul De La Cerda'],
    'khts-2021-12-09-charged.html' => ['De La Cerda served as East Los Angeles College (ELAC) Dean until March 2021, at which time he was dismissed by the Los Angeles Community College District (LACCD) due to being under criminal investigation', 'was charged with one felony count each of misappropriation of government funds and embezzlement of government funds', 'Between March 2017 and 2019, De La Cerda is accused of overbilling East Los Angeles College roughly $1,575 for several hotel stays', 'De La Cerda and his attorney have refuted all allegations'],
    'khts-2022-06-10-plea.html' => ['pled no contest to a charge of grand theft on Friday', 'De La Cerda was sentenced to two years probation for grand theft as a result of a plea agreement for a lesser charge, the original charges being one felony count each of misappropriation of government funds and embezzlement of government funds', 'ordered to pay $1,580.45 in restitution to East Los Angeles College (ELAC)and is prohibited from holding a government job during his probationary period'],
];
foreach ($MUST as $f => $ps) { foreach ($ps as $ph) { if (!str_contains($T[$f] ?? '', $ws($ph))) { $bad[] = "$f does not read \"" . mb_substr($ph, 0, 60) . '"'; } } }
if ((new \DateTime('2022-06-10'))->format('l') !== 'Friday') { $bad[] = '10 June 2022 is not a Friday'; }
$ceda = json_decode(file_get_contents("$root/inventory/elections/ceda-scv.json"), true)['rows'];
$row = fn($y) => array_values(array_filter($ceda, fn($r) => $r['body'] === 'saugus-union-school-district' && $r['year'] === $y && stripos($r['last'], 'cerda') !== false))[0] ?? null;
$r05 = $row(2005); $r13 = $row(2013);
if (!$r05 || !$r05['elected'] || $r05['votes'] !== 6845 || !$r13 || !$r13['elected'] || !$r13['incumbent'] || $r13['votes'] !== 2633) { $bad[] = 'CEDA does not read as the text says'; }
$c = fn($f) => $M[$f]['cite'] . ', ' . $M[$f]['url'];
$BODY = implode("\n\n", [
    'Paul De La Cerda served on the governing board of the Saugus Union School District for thirteen years, from December 2005 to December 2018. He was first elected in November 2005, began his third term in December 2013, and was the board\'s president for 2014 and its clerk in 2018.[1][2][3][4][5]',
    'He had worked in education since 2004, as a high school STEM instructor, a college professor and a college administrator. In August 2018 he said he would not stand again, having been admitted to a doctoral program in organizational change and leadership at the University of Southern California.[4]',
    'After he left the board, KHTS reported that in December 2021 he was charged with one felony count each of misappropriation and embezzlement of government funds, accused of overbilling East Los Angeles College, where he was a dean until March 2021, about $1,575 for hotel stays between 2017 and 2019; he denied the allegations. In June 2022, under a plea agreement, he pleaded no contest to a lesser charge, one count of grand theft, and was sentenced to two years\' probation, ordered to pay $1,580.45 in restitution, and barred from holding a government job during his probation. The case concerned his college post, not the school board.[6][7]',
]);
$NOTES = [
    'California Elections Data Archive (CEDA), ' . $r05['file'] . ', row ' . $r05['row'] . ': Saugus Union School District, November 8, 2005, three seats, elected, 6,845 votes, ballot designation "Hospital Grants Officer."',
    'California Elections Data Archive (CEDA), ' . $r13['file'] . ', row ' . $r13['row'] . ': Saugus Union School District, November 5, 2013, elected, incumbent, 2,633 votes.',
    $c('scvnews-2013-12-20-president.html') . ': "De la Cerda begins his third term and was elected to the position of Board President for 2014."',
    $c('signal-2018-08-14-wont-seek.html') . ': "He was first elected to the board in November 2005, and currently serves as clerk of the board."',
    'Perry Smith, "4 Vie for Vacant Seat on Saugus School Board," KHTS (hometownstation.com), November 11, 2014, as carried on SCVNews.com, ' . ($k14['url'] ?? '') . ': "said board President Paul De La Cerda."',
    $c('khts-2021-12-09-charged.html') . '.',
    $c('khts-2022-06-10-plea.html') . '.',
];
$holding = $p ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->one() : null;
$cur = trim((string)$p?->body);
echo '#25407 Paul De La Cerda: body ' . ($cur === trim($BODY) ? 'already written' : ($cur ? 'NOT EMPTY, refusing' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes')) . PHP_EOL;
if ($cur && $cur !== trim($BODY)) { $bad[] = '#25407 has a body already'; }
echo ($holding ? "#{$holding->id} exists: " : 'create officeHolding: ') . 'School Board Member, Saugus Union School District, December 2005 (elected) to December 2018 (did not stand)' . PHP_EOL;
$PROV = '; build_de_la_cerda_profile.php, 3 Oct 2026: profile and office';
if (mb_strlen(trim((string)$p?->recordProvenance . $PROV)) > 255) { $bad[] = 'recordProvenance would exceed 255 characters'; }
if (preg_match('~\x{2014}|inventory/~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash or a repository path in the text'; }
preg_match_all('~\[(\d+)\]~', $BODY, $m); if (count(array_unique($m[1])) !== count($NOTES)) { $bad[] = 'notes used ' . json_encode(array_values(array_unique($m[1]))) . ' of ' . count($NOTES); }
echo PHP_EOL . $BODY . PHP_EOL . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    $p = $get($ID);
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'Educator; school board member', 'fullName' => 'Paul Nelson De La Cerda',
        'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [$SUSD->id]))),
        'recordProvenance' => trim((string)$p->recordProvenance . $PROV)];
    $p->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($p)) { throw new \RuntimeException('#25407: ' . json_encode($p->getFirstErrors())); }
    if (!$holding) {
        $holding = new Entry(); $os = $svc->getSectionByHandle('officeHoldings'); $holding->sectionId = $os->id; $holding->setTypeId($svc->getEntryTypeByHandle('officeHolding')->id);
        $holding->setFieldValues(['holdingPerson' => [$ID], 'holdingOffice' => [$ROLE->id], 'holdingBody' => [$SUSD->id],
            'termStart' => 'December 2005', 'termStartEdtf' => '2005-12', 'termEnd' => 'December 2018', 'termEndEdtf' => '2018-12', 'selectionMethod' => 'elected', 'howEnded' => 'expired', 'startEvidence' => 'retrospective', 'endEvidence' => 'contemporary',
            'footnotes' => $fn([$NOTES[0], $NOTES[3], $NOTES[2], 'He did not stand in November 2018 (' . $M['signal-2018-08-14-wont-seek.html']['cite'] . ').']),
            'recordProvenance' => 'build_de_la_cerda_profile.php, 3 October 2026']);
        if (!$elements->saveElement($holding)) { throw new \RuntimeException('holding: ' . json_encode($holding->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($ID);
$ok = trim((string)$r->body) === trim($BODY) && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->exists();
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_de_la_cerda_profile.php', 2, $ok ? 'verified' : 'SHORT', 'Paul De La Cerda: profile and office, 2005 to 2018; the 2022 plea at the end, attributed to KHTS');
if (!$ok) { throw new \RuntimeException('build_de_la_cerda_profile: read-back failed'); }
