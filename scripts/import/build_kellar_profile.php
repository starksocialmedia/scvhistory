/**
 * Bob Kellar #21944, Santa Clarita City Council 2000 to 2020 (Nathan,
 * 3 October 2026: "Build the profile from ... sc1310.htm ... He is living, so
 * public life only, two citations where a second source exists, no birth date
 * beyond a year").
 *
 * THE SOURCES, all already in the archive
 *   [LEON]     Leon Worden's note on SC1310 (photograph #27853): mayor in 2004,
 *              2008, 2013 and 2016.
 *   [CITY]     The City's 2013 biography, as SC1310 carries it: the Army, the
 *              LAPD, the chambers, the realtors, the Veterans Memorial
 *              Committee, the hospital and senior center foundations, the
 *              Planning Commission, CEMEX and Whittaker-Bermite. His own
 *              City's account of a sitting member, and said to be.
 *   [RETURNS]  His five candidacies, 2000 to 2016, and five terms.
 *   [RES]      Resolution No. 12-9 (document #21936), certifying 2012.
 *   [COLUMNS]  Leon Worden's columns of 27 August 1997 (#12408), 9 July 1998
 *              (#12402) and 10 September 1999 (#12160): written at the time,
 *              the second source for the LAPD, the chamber and the Planning
 *              Commission. Nothing else in the City's account has a second
 *              source in the archive, and the text says whose account it is.
 *
 * Not used: his residence (on the photograph's caption, where Leon put it),
 * the City's words of praise, and the 2013 mayoral goals, which were goals.
 *
 * Fills an empty body only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_kellar_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
$ws = fn($s) => preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}"], ["'", "'", '"', '"'], html_entity_decode(strip_tags((string)$s), ENT_QUOTES)));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 21944; $PHOTO = 27853; $RES = 21936; $CITY_BODY = 394;
$p = Entry::find()->id($ID)->status(null)->one();
$bad = [];
if (!$p || $p->title !== 'Bob Kellar') { $bad[] = '#21944 is not Bob Kellar'; }
/* Every claim, read where it is cited. */
$MUST = [
    $PHOTO => ['Mayor in 2004, 2008, 2013, 2016.', 'served as Mayor in 2004 and again in 2008', 'goals for his third term as Mayor', 'entering the United States Army from 1965 through 1967', '25 years with the Los Angeles Police Department', 'retired from the LAPD in 1993, finishing up his career as the Supervisor in Charge of Reserve Officer Training at the Police Academy',
        'President of the Canyon Country Chamber of Commerce from 1993 through its incorporation with the Santa Clarita Valley Chamber of Commerce in 1995', 'instrumental in re-shaping the Santa Clarita Valley Chamber of Commerce to include Canyon Country',
        'In 2000, Kellar served as President of the Santa Clarita Division of the Southland Regional Association of Realtors and the Santa Clarita Valley Veterans Memorial Committee', 'From 2003 to 2007, Kellar served on the Henry Mayo Newhall Memorial Hospital Foundation', 'Board of Directors for the Santa Clarita Valley Senior Center Foundation',
        '20+ year member of the Santa Clarita Valley Veterans Memorial Committee. He served as past president in 1993 and 1994, and currently serves as president', 'former Chair of the City\'s Planning Commission', 'worked hard to prevent the proposed Cemex mining operation', 'bringing stake holders together to get the Whittaker Bermite site cleaned up'],
    12408 => ['Bob Kellar, retired Los Angeles police officer and current chairman of the Canyon Country Committee of the SCV Chamber of Commerce'],
    12402 => ['recently appointed city Planning Commissioner Bob Kellar, who doubles as the chamber\'s Canyon Country committee chairman'],
    12160 => ['Bob Kellar, chairman of the chamber\'s business assistance committee'],
    /* The resolution has no transcription; its Section 4 is quoted in the 2012 term's footnote. */
    22418 => ['Resolution No. 12-9, Section 4: "Bob Kellar was elected as member of the City Council for the full term of four years', 'The resolution is document #21936.'],
];
if (!str_starts_with((string)Entry::find()->id($RES)->status(null)->one()?->title, 'Resolution No. 12-9')) { $bad[] = "#$RES is not Resolution No. 12-9"; }
foreach ($MUST as $id => $phrases) {
    $e = Entry::find()->id($id)->status(null)->one();
    $t = $e ? $ws($e->section->handle === 'officeHoldings' ? implode(' ', array_column($e->footnotes ?? [], 'note')) : $e->body) : '';
    foreach ($phrases as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "#$id does not read \"" . mb_substr($ph, 0, 70) . '"'; } }
}
/* The returns, as the text gives them: votes, outcome, place, field. */
$VOTES = ['2000-04-11' => [4844, 'elected', 2, 11], '2004-04-13' => [5777, 'elected', 2, 3], '2008-04-08' => [6135, 'elected', 2, 5], '2012-04-10' => [7519, 'elected', 1, 5], '2016-11-08' => [32216, 'elected', 1, 11]];
foreach (Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'candidacyPerson'])->all() as $c) {
    $e = $c->candidacyElection->one(); $all = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->orderBy('votes desc')->ids();
    $got = [(int)$c->votes, (string)$c->outcome->value, array_search($c->id, $all) + 1, count($all)];
    if (($VOTES[$e->electionDateEdtf] ?? null) !== $got) { $bad[] = "the {$e->electionDateEdtf} candidacy reads " . json_encode($got); }
    unset($VOTES[$e->electionDateEdtf]);
}
if ($VOTES) { $bad[] = 'no candidacy for ' . implode(', ', array_keys($VOTES)); }
/* The terms: five, April 2000 to December 2020, all on the council. */
$terms = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->all();
$span = array_map(fn($h) => (string)$h->termStartEdtf, $terms); sort($span);
$ends = array_map(fn($h) => (string)$h->termEndEdtf, $terms); rsort($ends);
if (count($terms) !== 5 || ($span[0] ?? '') !== '2000-04' || ($ends[0] ?? '') !== '2020-12' || array_filter($terms, fn($h) => $h->holdingBody->one()?->id !== $CITY_BODY)) { $bad[] = 'the terms are not five on the council from 2000-04 to 2020-12: ' . json_encode([$span, $ends]); }

$BODY = implode("\n\n", [
    'Bob Kellar served five terms on the Santa Clarita City Council, from April 2000 to December 2020, and was the city\'s mayor in 2004, 2008, 2013 and 2016.[1][2][3]',
    'He was elected in April 2000, second of eleven candidates for two seats, with 4,844 votes, and re-elected in 2004, second of three with 5,777; in 2008, second of five with 6,135; in 2012, first of five with 7,519, the result the council certified in Resolution No. 12-9; and, after the city moved its elections to November, in 2016, first of eleven with 32,216.[3][4]',
    'Before the council he served in the United States Army from 1965 to 1967 and then spent 25 years with the Los Angeles Police Department, retiring in 1993 as supervisor in charge of reserve officer training at the Police Academy, as the City\'s 2013 biography gives it.[2] A column of August 1997 calls him a retired Los Angeles police officer.[5]',
    'By the City\'s account he was president of the Canyon Country Chamber of Commerce from 1993 until it joined the Santa Clarita Valley Chamber of Commerce in 1995, and helped bring Canyon Country into the valley chamber.[2] Columns written at the time find him chairing the valley chamber\'s Canyon Country committee in 1997 and 1998 and its business assistance committee in 1999.[5][6][7] He had been appointed to the City\'s Planning Commission by July 1998, and later chaired it.[6][2]',
    'The City\'s biography lists him as president of the Santa Clarita Division of the Southland Regional Association of Realtors in 2000; a member of the Santa Clarita Valley Veterans Memorial Committee for more than twenty years by 2013, and its president in 1993 and 1994, in 2000 and again in 2013; on the Henry Mayo Newhall Memorial Hospital Foundation from 2003 to 2007; and, in 2013, on the board of the Santa Clarita Valley Senior Center Foundation.[2]',
    'On the council, the City says, he worked against the proposed CEMEX mining operation and to bring the parties together over the cleanup of the Whittaker-Bermite site.[2]',
]);
$NOTES = [
    'Leon Worden\'s note on his City Council portrait, SC1310, in this archive (photograph #' . $PHOTO . ').',
    'City of Santa Clarita, his biography of 2013, as carried on SC1310 (photograph #' . $PHOTO . '): the City\'s own account of a sitting member. It gives his mayoralties of 2004 and 2008 and his third, in 2013.',
    'Archive records: the City Council elections of April 11, 2000, April 13, 2004, April 8, 2008, April 10, 2012 and November 8, 2016, with their returns, and his office holdings for each term.',
    'Resolution No. 12-9, declaring the results of the General Municipal Election of April 10, 2012, Section 4, in this archive (document #' . $RES . ').',
    'Leon Worden, "Valley Fair attracts families, not gangs," August 27, 1997, in this archive (article #12408).',
    'Leon Worden, "Canyon Country festival starts today," July 9, 1998, in this archive (article #12402): "recently appointed city Planning Commissioner Bob Kellar."',
    'Leon Worden, "Retail or e-tail, help\'s on the way," September 10, 1999, in this archive (article #12160).',
];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
$cur = trim((string)$p?->body);
if ($cur && $cur !== trim($BODY)) { $bad[] = '#21944 has a body already'; }
echo '#21944 body: ' . ($cur === trim($BODY) ? 'already written' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes') . '; ' . array_sum(array_map('count', $MUST)) . ' phrases, 5 returns and 5 terms checked' . PHP_EOL;
foreach (explode("\n\n", $BODY) as $para) { echo '  ' . $para . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if ($cur === trim($BODY)) { echo 'nothing to do' . PHP_EOL; return; }
$h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
$p->setFieldValues(array_intersect_key(['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026',
    'recordProvenance' => trim((string)$p->recordProvenance . '; build_kellar_profile.php, 3 Oct 2026: public-life profile', '; ')], array_flip($h)));
if (!$elements->saveElement($p)) { throw new \RuntimeException('#21944: ' . json_encode($p->getFirstErrors())); }
$ok = trim((string)Entry::find()->id($ID)->status(null)->one()->body) === trim($BODY);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $p->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_kellar_profile.php', 1, $ok ? 'verified' : 'SHORT', 'Bob Kellar: public-life profile from SC1310, the returns and three contemporary columns');
if (!$ok) { throw new \RuntimeException('build_kellar_profile: read-back failed'); }
