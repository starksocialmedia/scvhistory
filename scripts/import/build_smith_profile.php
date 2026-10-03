/**
 * Christy Smith #25389: a living person, so public-life facts only, two citations
 * where a second source exists, no birth date, nothing about family. Lead with
 * the valley (Nathan, 3 October 2026: "Smith's Assembly and congressional runs
 * are better documented elsewhere; what this archive holds is her school board
 * service here").
 *
 * THE SOURCES
 *   Wikipedia, "Christy Smith (politician)": a finding aid only, never cited. It
 *     led to the Secretary of State's returns, which are cited instead, and where
 *     they differ the returns win: Wikipedia gives the 2018 result as 51.2 to
 *     48.8; the Statement of Vote gives 51.5 to 48.5. Its birth date is not taken,
 *     and its Newhall line ("two terms") cites a 2016 Signal page now gone (404).
 *   CEDA, 2007, 2009 and 2013: the Newhall School District races and her ballot
 *     designations.
 *   SCVTV, "Newhall School Auditorium Reborn," 26 October 2017 (nsd102617, the
 *     Reggie mirror, manifest-matched): "Christy Smith, Newhall School Board
 *     president."
 *   SCVTV, Sierra Vista Junior High classroom dedication, 28 March 2019
 *     (sierravista20190328): "Assemblywoman Christy Smith, D-Santa Clarita."
 *   California Secretary of State: the Statements of Vote for November 2018
 *     (Assembly), November 2020 (Assembly and Congress) and November 2022
 *     (Congress), and the official results of the special general election of
 *     12 May 2020 (inventory/elections/sos/, with manifest.json).
 *
 * THE OFFICE. An officeHolding: School Board Member, Newhall School District, from
 * 2009 (elected November 3), at large, how it ended unknown: no source in hand
 * gives the date she left the board. Derived evidence (the returns, compiled).
 *
 * Her portrait is NOT imported here: see the note in the session's report. The
 * file carries "Copyright: Jeff Walters", and the Commons tag it came with
 * (PD-CAGov) rests on the Public Records Act, which does not reach the
 * Legislature (Government Code 7920.540(a)). Nathan decides.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_smith_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$ID = 25389; $p = $get($ID); $NSD = $get(21590); $ROLE = $get(26964); $ASM = $get(18313);
$F = "$root/inventory/legacy/fetched"; $SOS = "$root/inventory/elections/sos";
$bad = [];
if (!$p || $p->title !== 'Christy Smith') { $bad[] = '#25389 is not Christy Smith'; }
if (!$NSD || $NSD->title !== 'Newhall School District' || !$ROLE || $ROLE->title !== 'School Board Member' || !$ASM || $ASM->title !== 'State Assemblymember') { $bad[] = 'the district or a role is not where expected'; }
/* The mirror pages, checked against the hashes taken when they were copied. */
$V = json_decode(file_get_contents("$F/batch10-sha.json"), true)['files'];
$page = fn($k) => isset($V["$k.htm"]) && hash_file('sha256', "$F/$k.htm") === $V["$k.htm"] ? $ws(file_get_contents("$F/$k.txt")) : '';
foreach (['nsd102617' => ['This video premiered at a VIP reception at the theatre on October 26, 2017', 'Christy Smith, Newhall School Board president'], 'sierravista20190328' => ['Assemblywoman Christy Smith, D-Santa Clarita']] as $k => $ps) {
    foreach ($ps as $ph) { if (!str_contains($page($k), $ws($ph))) { $bad[] = "$k does not read \"$ph\""; } }
}
/* The state's returns, checked against the manifest and read for the figures in the text. */
$SM = json_decode(file_get_contents("$SOS/manifest.json"), true)['files'];
/* A PDF is read from the text pdftotext -layout made of it on the MacBook (the container has no pdftotext), beside it with the same name. */
$sos = function ($f) use ($SOS, $SM) { if (!isset($SM[$f]) || hash_file('sha256', "$SOS/$f") !== $SM[$f]['sha256']) { return ''; } return str_ends_with($f, '.pdf') ? (string)@file_get_contents("$SOS/" . substr($f, 0, -4) . '.txt') : strip_tags(file_get_contents("$SOS/$f")); };
$block = function ($t, $head) { $i = strpos($t, $head); return $i === false ? '' : preg_replace('~\s+~', ' ', substr($t, $i, 700)); };
$CHECK = [
    ['2018-general-68-state-assemblymember.pdf', '38th Assembly District', ['Christy Dante Smith Acosta*', 'District Totals 95,751 90,298', 'Percent 51.5% 48.5%']],
    ['2020-general-41-state-assembly.pdf', '38th Assembly District', ['Suzette Lucie Martinez Lapointe', 'Valladares Volotzky']],
    ['2020-general-24-us-reps.pdf', '25th Congressional District', ['Christy Mike', 'District Totals 169,305 169,638']],
    ['2022-general-48-congress.pdf', '27th Congressional District', ['Christy Mike', 'District Totals 91,892 104,624', 'Percent 46.8% 53.2%']],
];
foreach ($CHECK as [$f, $head, $ps]) { $b = $block($sos($f), $head); foreach ($ps as $ph) { if (!str_contains($b, $ph)) { $bad[] = "$f ($head) does not read \"$ph\""; } } }
$sp = preg_replace('~\s+~', ' ', $sos('2020-cd25-special-general-official-results.html'));
foreach (['Christy Smith, DEM', '78,721', 'Mike Garcia, REP', '95,667'] as $ph) { if (!str_contains($sp, $ph)) { $bad[] = "the special election results do not read \"$ph\""; } }
/* CEDA: the three Newhall races. */
$ceda = json_decode(file_get_contents("$root/inventory/elections/ceda-scv.json"), true)['rows'];
$race = fn($y) => array_values(array_filter($ceda, fn($r) => $r['body'] === 'newhall-school-district' && $r['year'] === $y));
$her = fn($y) => array_values(array_filter($race($y), fn($r) => $r['last'] === 'Smith' && str_starts_with($r['first'], 'Christy')))[0] ?? null;
$r07 = $her(2007); $r09 = $her(2009); $r13 = $her(2013);
$place = fn($y, $r) => count(array_filter($race($y), fn($x) => $x['votes'] > $r['votes'])) + 1;
if (!$r07 || $r07['elected'] || $r07['votes'] !== 1695 || $place(2007, $r07) !== 3 || count($race(2007)) !== 4 || $r07['seats'] !== 2) { $bad[] = 'CEDA 2007 is not third of four for two seats, 1,695'; }
if (!$r09 || !$r09['elected'] || $r09['votes'] !== 2402 || $place(2009, $r09) !== 2 || count($race(2009)) !== 5 || $r09['seats'] !== 3) { $bad[] = 'CEDA 2009 is not second of five for three seats, 2,402'; }
if (!$r13 || !$r13['elected'] || !$r13['incumbent'] || $r13['votes'] !== 2129 || $place(2013, $r13) !== 1 || count($race(2013)) !== 4) { $bad[] = 'CEDA 2013 is not first of four, incumbent, 2,129'; }
if (($r07['designation'] ?? '') !== 'Community Volunteer' || ($r09['designation'] ?? '') !== 'Community Volunteer' || ($r13['designation'] ?? '') !== 'Governing Board Member') { $bad[] = 'the ballot designations are not as the text says'; }
$cands = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'candidacyPerson'])->count();
$cands = (int)$cands;
if ($cands !== 3) { $bad[] = "the archive holds $cands candidacies for her, not 3"; }

$CEDA = fn($r) => "California Elections Data Archive (CEDA), {$r['file']}, row {$r['row']}";
$SOSN = fn($f, $what) => "California Secretary of State, $what, " . $SM[$f]['url'] . ' (inventory/elections/sos/' . $f . ').';
$BODY = implode("\n\n", [
    'Christy Smith served on the governing board of the Newhall School District. She first stood in November 2007, as a community volunteer, and came third of four for two seats. In November 2009 she was elected, second of five for three seats, and in 2013 she was re-elected at the head of the poll, first of four.[1][2][3] In October 2017, when the Newhall School auditorium reopened as the Newhall Family Theater for the Performing Arts, she was the board\'s president.[4]',
    'From the school board she went to the State Assembly. In November 2018 she won the 38th District, which takes in the Santa Clarita Valley, from the incumbent, Dante Acosta, 51.5 per cent to 48.5, and she served one term; in March 2019 she attended the dedication of new classrooms at Sierra Vista Junior High as the valley\'s Assemblywoman.[5][6][7] She then stood three times for Congress and lost each time to Mike Garcia: in the special election of May 2020 and the general election of that November for the 25th District, the second by 333 votes of more than 338,000, and in November 2022 for the 27th.[8][9][10]',
]);
$NOTES = [
    $CEDA($r07) . ': Newhall School District, November 6, 2007, two seats, not elected, 1,695 votes, third of four, ballot designation "Community Volunteer."',
    $CEDA($r09) . ': Newhall School District, November 3, 2009, three seats, Christy L. Smith, elected, 2,402 votes, second of five, "Community Volunteer."',
    $CEDA($r13) . ': Newhall School District, November 5, 2013, three seats, elected, incumbent, 2,129 votes, first of four, "Governing Board Member."',
    'SCVTV, "Newhall School Auditorium Reborn," a video that premiered on October 26, 2017, as carried on SCVHistory.com, /scvhistory/nsd102617.htm: "Christy Smith, Newhall School Board president."',
    $SOSN('2018-general-68-state-assemblymember.pdf', 'Statement of Vote, General Election, November 6, 2018, State Assemblymember, 38th Assembly District: Christy Smith (DEM) 95,751, 51.5 per cent; Dante Acosta (REP, incumbent) 90,298, 48.5 per cent'),
    $SOSN('2020-general-41-state-assembly.pdf', 'Statement of Vote, General Election, November 3, 2020, State Assembly, 38th Assembly District: Suzette Martinez Valladares and Lucie Lapointe Volotzky; Smith did not stand'),
    'SCVTV, Sierra Vista Junior High School: New Classroom Buildings Dedicated, March 28, 2019, as carried on SCVHistory.com, /scvhistory/sierravista20190328.htm: "Assemblywoman Christy Smith, D-Santa Clarita."',
    $SOSN('2020-cd25-special-general-official-results.html', 'Final Official Election Results, Congressional District 25, Special General Election, May 12, 2020: Christy Smith (DEM) 78,721, 45.14 per cent; Mike Garcia (REP) 95,667, 54.86 per cent'),
    $SOSN('2020-general-24-us-reps.pdf', 'Statement of Vote, General Election, November 3, 2020, United States Representative, 25th Congressional District: Christy Smith (DEM) 169,305; Mike Garcia (REP) 169,638'),
    $SOSN('2022-general-48-congress.pdf', 'Statement of Vote, General Election, November 8, 2022, United States Representative, 27th Congressional District: Christy Smith (DEM) 91,892, 46.8 per cent; Mike Garcia (REP) 104,624, 53.2 per cent'),
];
$holding = $p ? Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->one() : null;
$cur = trim((string)$p?->body);
echo '#25389 Christy Smith: body ' . ($cur === trim($BODY) ? 'already written' : ($cur ? 'NOT EMPTY, refusing' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes')) . PHP_EOL;
if ($cur && $cur !== trim($BODY)) { $bad[] = '#25389 has a body already'; }
echo ($holding ? "#{$holding->id} exists: " : 'create officeHolding: ') . 'School Board Member, Newhall School District, from 2009, elected, end unknown' . PHP_EOL;
echo 'roles + School Board Member, State Assemblymember; personOrganizations + Newhall School District; Wikipedia link set (finding aid)' . PHP_EOL;
echo 'Left out: her birth date and birthplace, her family, the 2016 Assembly race (no return in hand), Wikipedia\'s 51.2/48.8.' . PHP_EOL;
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
preg_match_all('~\[(\d+)\]~', $BODY, $m); if (count(array_unique($m[1])) !== count($NOTES)) { $bad[] = 'notes used ' . json_encode(array_values(array_unique($m[1]))) . ' of ' . count($NOTES); }
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    $p = $get($ID);
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'School board member; State Assemblymember',
        'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$ROLE->id, $ASM->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [$NSD->id]))),
        'personWikipediaUrl' => 'https://en.wikipedia.org/wiki/Christy_Smith_(politician)',
        'recordProvenance' => trim((string)$p->recordProvenance . '; build_smith_profile.php, 3 October 2026: body and office; public-life facts only; Wikipedia used as a finding aid, the returns cited')];
    $p->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($p)) { throw new \RuntimeException('#25389: ' . json_encode($p->getFirstErrors())); }
    if (!$holding) {
        $holding = new Entry(); $os = $svc->getSectionByHandle('officeHoldings'); $holding->sectionId = $os->id; $holding->setTypeId($svc->getEntryTypeByHandle('officeHolding')->id);
        $holding->setFieldValues(['holdingPerson' => [$ID], 'holdingOffice' => [$ROLE->id], 'holdingBody' => [$NSD->id],
            'termStart' => '2009', 'termStartEdtf' => '2009', 'selectionMethod' => 'elected', 'howEnded' => 'unknown', 'startEvidence' => 'derived',
            'footnotes' => $fn([$NOTES[1], $NOTES[2], 'Re-elected in 2013 and board president in October 2017 (' . $NOTES[3] . ') No source in hand gives the date she left the board.']),
            'recordProvenance' => 'build_smith_profile.php, 3 October 2026']);
        if (!$elements->saveElement($holding)) { throw new \RuntimeException('holding: ' . json_encode($holding->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$r = $get($ID);
$ok = trim((string)$r->body) === trim($BODY) && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $ID, 'field' => 'holdingPerson'])->exists();
echo 'READ-BACK ' . ($ok ? 'OK: ' . $r->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_smith_profile.php', 2, $ok ? 'verified' : 'SHORT', 'Christy Smith: profile and office');
if (!$ok) { throw new \RuntimeException('build_smith_profile: read-back failed'); }
