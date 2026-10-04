/**
 * Three more commissioners get person records (Nathan, 4 October 2026: "Yes to all three"): Nathan Keith,
 * chairperson of the Planning Commission; Di Thompson, chair of the Parks, Recreation and Community Services
 * Commission; Michael Millar, Arts Commission, its founding chair from 2009 to 2011. Public life only, from
 * the City's commission pages read 4 October 2026 (inventory/news/city-commissions-2026-10-04/); spouses,
 * children and pets on those pages are left out. Each gets a profile, the commission seat as an affiliation,
 * and, where the page gives one, a further seat: Thompson on the SCV Chamber of Commerce's board (#396),
 * Millar as the Arts Commission's founding chair, 2009 to 2011.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/record_three_commissioners_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements(); $bad = [];
$D = "$root/inventory/news/city-commissions-2026-10-04";
$PG = ['planning' => ['planning-commission', 'Planning Commission', 15939], 'parks' => ['parks-recreation-and-community-services', 'Parks, Recreation and Community Services', null], 'arts' => ['arts-commission', 'Arts Commission', null]];
$PG['parks'][2] = Entry::find()->section('organizations')->status(null)->title('Parks, Recreation and Community Services Commission')->one()?->id;
$PG['arts'][2] = Entry::find()->section('organizations')->status(null)->title('Arts Commission')->one()?->id;
$T = []; foreach ($PG as $k => $x) { $T[$k] = preg_replace('~\s+~u', ' ', (string)@file_get_contents("$D/{$x[0]}.txt")); if (!$x[2]) { $bad[] = "$k commission record missing"; } }
$cite = function ($k, $q) use (&$bad, $T, $PG) { if (!str_contains($T[$k], $q)) { $bad[] = "$k does not read: " . mb_substr($q, 0, 60); } return 'City of Santa Clarita, "' . $PG[$k][1] . '," https://santaclarita.gov/commission-information/' . $PG[$k][0] . '/, read 4 October 2026: "' . $q . '"'; };
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$P = [
    'Nathan Keith' => ['planning', 'Chairperson', 'December 2028', 'Real estate executive; planning commissioner',
        '<p>Nathan Keith chairs the City\'s Planning Commission; his term runs to December 2028. He came to Santa Clarita in 2001 to attend The Master\'s University, graduating in 2004, and joined the Tejon Ranch Company in 2007, where he became senior vice president of real estate, overseeing the entitlements for one of the company\'s master-planned communities. He sits on the boards of the Los Angeles County Business Federation and of the Building Industry Association of Southern California, and was the 2025 president of its Los Angeles/Ventura chapter.[1]</p>',
        ['Nathan Keith, Chairperson Term Expires: December 2028', 'Since joining Tejon Ranch Company in 2007, Nathan has worked through various roles, ultimately becoming the Senior Vice President of Real Estate', 'He serves on several boards, including the Los Angeles County Business Federation, the Building Industry Association of Southern California and the Building Industry Association of Los Angeles/Ventura Chapter, where he is honored to serve as the 2025 President', 'Nathan moved to Santa Clarita in 2001 to attend The Master’s University, where he graduated in 2004'], []],
    'Di Thompson' => ['parks', 'Chair', 'December 2026', 'Real estate broker; parks commissioner',
        '<p>Di Thompson chairs the City\'s Parks, Recreation and Community Services Commission; her term runs to December 2026. A resident of Santa Clarita for 24 years, she works in residential and investment real estate. She sits on the board of directors of the Santa Clarita Valley Chamber of Commerce, where she was named its 2025 chair-elect, is the inaugural chair of its Black Business Council, and is a board member of the Child &amp; Family Center.[1]</p>',
        ['Di Thompson, Chair Term Expires: December 2026', 'Di Thompson, a dedicated resident of Santa Clarita for 24 years', 'She is very involved with the Santa Clarita Valley Chamber of Commerce and will serve as the 2025 Chair Elect of the SCV Chamber of Commerce, where she currently sits on the Board of Directors. Additionally, Di serves as the inaugural Chair of the Black Business Council', 'Di is also a Board Member for the Child & Family Center'],
        [[396, 'nonprofit-board', 'Board of Directors; 2025 Chair Elect', 'serving']]],
    'Michael Millar' => ['arts', 'Commissioner', 'December 2026', 'Musician and music educator; arts commissioner',
        '<p>Michael Millar was the founding chair of the City\'s Arts Commission, from 2009 to 2011, and sits on it again, his term running to December 2026. An arts advocate in the valley since moving to Santa Clarita in 1993, he served on the City\'s Arts Advisory Committee and with the Santa Clarita Symphony.[1]</p><p>He has taught music at Cal Poly Pomona since 2004 and directed its Center for Community Engagement. A bass trombonist, he played on the Grammy-winning 2004 recording of Carlos Chávez\'s chamber works with Southwest Chamber Music.[1]</p>',
        ['Dr. Michael Millar, Commissioner Term Expires: December 2026', 'He served as the founding Chair of the Arts Commission from 2009-11', 'An active arts advocate since moving to Santa Clarita in 1993, Dr. Millar served with the City’s Arts Advisory Committee and with the Santa Clarita Symphony', 'Michael Millar has been a member of the Cal Poly Pomona music faculty since 2004 and is a past Director of the University’s Center for Community Engagement', 'He performed on the 2004 Grammy Award-winning CD “Carlos Chavez: Complete Chamber Works, Vol. 2” (Best Small Ensemble, Classical Field) as a member of Southwest Chamber Music'],
        [['self', 'member', 'Founding Chair', 'left', '2009', '2011']]],
];
$plan = [];
foreach ($P as $name => [$k, $office, $term, $occ, $body, $quotes, $extra]) {
    $c = implode('; ', array_map(fn($q) => '"' . $q . '"', $quotes)); foreach ($quotes as $q) { $cite($k, $q); }
    $note = 'City of Santa Clarita, "' . $PG[$k][1] . '," https://santaclarita.gov/commission-information/' . $PG[$k][0] . '/, read 4 October 2026: ' . $c . '.';
    $p = Entry::find()->section('persons')->status(null)->title($name)->one();
    echo "$name: " . ($p ? "#{$p->id} exists" : 'create') . ", $office to $term; profile " . str_word_count(strip_tags($body)) . ' words' . ($extra ? '; ' . count($extra) . ' further seat(s)' : '') . PHP_EOL;
    if (preg_match('~\b(wife|husband|married|children|daughters|sons?|dog|Callie|Dava|Christine|Jeffrey)\b~', strip_tags($body))) { $bad[] = "$name: family in the profile"; }
    $plan[$name] = compact('p', 'k', 'office', 'term', 'occ', 'body', 'note', 'extra');
}
echo 'REFUSED: ' . ($bad ? implode(' | ', array_unique($bad)) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$PROV = 'record_three_commissioners_2026_10_04.php, 4 October 2026: from the City\'s commission pages';
$pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person'); $aSec = $svc->getSectionByHandle('affiliations'); $aType = $svc->getEntryTypeByHandle('affiliation');
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try {
    foreach ($plan as $name => $x) {
        $p = $x['p'];
        if (!$p) { $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->setFieldValues(['fullName' => $name, 'recordProvenance' => $PROV]); if (!$el->saveElement($p)) { throw new \RuntimeException($name); } $n++; }
        if (!trim(strip_tags((string)$p->body))) { $p->setFieldValues(['body' => $x['body'], 'footnotes' => $fn([$x['note']]), 'bodyAuthorship' => 'editorial-2026', 'occupation' => $x['occ']]); if (!$el->saveElement($p)) { throw new \RuntimeException($name); } $n++; }
        $seats = array_merge([[$PG[$x['k']][2], 'member', $x['office'], 'serving', null, $x['term']]], array_map(fn($e) => $e[0] === 'self' ? [$PG[$x['k']][2], $e[1], $e[2], $e[3], $e[4] ?? null, $e[5] ?? null] : [$e[0], $e[1], $e[2], $e[3], null, null], $x['extra']));
        foreach ($seats as [$bid, $kind, $title, $ended, $ts, $te]) {
            if (Entry::find()->section('affiliations')->status(null)->relatedTo(['and', ['targetElement' => $p, 'field' => 'affiliationPerson'], ['targetElement' => $bid, 'field' => 'affiliationBody']])->affiliationTitle($title)->exists()) { continue; }
            $a = new Entry(); $a->sectionId = $aSec->id; $a->setTypeId($aType->id);
            $v = ['affiliationPerson' => [$p->id], 'affiliationBody' => [$bid], 'affiliationKind' => $kind, 'affiliationTitle' => $title, 'affiliationEnded' => $ended, 'footnotes' => $fn([$x['note']]), 'recordProvenance' => $PROV];
            if ($ts) { $v['termStart'] = $ts; $v['termStartEdtf'] = $ts; $v['startEvidence'] = 'certified'; }
            if ($te) { $v['termEnd'] = $te; if (preg_match('~^\d{4}$~', $te)) { $v['termEndEdtf'] = $te; $v['endEvidence'] = 'certified'; } }
            $a->setFieldValues($v); if (!$el->saveElement($a)) { throw new \RuntimeException("$name affiliation: " . json_encode($a->getFirstErrors())); } $n++;
        }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$ok = (bool)Entry::find()->section('persons')->status(null)->title('Michael Millar')->one()?->body;
echo 'READ-BACK ' . ($ok ? "OK: $n writes" : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('record_three_commissioners_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', 'Keith, Di Thompson, Millar: records, profiles and seats from the City\'s commission pages');
