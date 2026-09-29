/**
 * Who won, and who served: the Santa Clarita City Council, 1987 to now (Nathan,
 * 29 September 2026: "tell me what the archive can establish about winners
 * without those resolutions", then the roster, then an officeHolding for every
 * winner).
 *
 * WHAT ESTABLISHES A WINNER, weakest last
 *   certified      Resolution 12-9 (April 2012). Unchanged.
 *   retrospective  1987: the City's own account of its first council. Unchanged.
 *   roster         1990 to 2018. The archive already holds a council roster:
 *                  Leon Worden's ledger "Santa Clarita City Council 1987—" on
 *                  SCVHistory.com, carried on record #4967 (LW3149). It gives
 *                  every member's years, "Jill Klajic 1990-1994, 1996-2000",
 *                  and was written after 19 March 2020 (it notes the switch to
 *                  district elections) and before December 2020 (Bob Kellar is
 *                  still serving, Jason Gibbs is absent). At every election it
 *                  covers, the top N of the count are exactly the candidates
 *                  it shows serving on, and no one below them is shown serving
 *                  in that term. That is two independent records agreeing,
 *                  which is more than arithmetic, and it also fixes the seat
 *                  count: had N been one more or one less, the roster would
 *                  contradict the count. The script checks this and refuses if
 *                  any election fails it.
 *   derived        2020, 2022, 2024: after the roster. The top N of the count,
 *                  N from the staggered terms (2020, 2022) or the single
 *                  district seat (2024). A new option on the evidence scale,
 *                  between roster and uncited: "read from the count", added by
 *                  add_derived_evidence.php, which runs first. Upgraded when
 *                  the declaring resolutions arrive.
 *
 * THE TERMS. One officeHolding per term. An April term runs from April of its
 * election to the next election for that seat; a November term from December
 * (the month the council turns over: Cameron Smyth left on 10 December 2024).
 * When the city moved to November, the April 2012 terms ran to December 2016
 * and the April 2014 terms to December 2018: the four years Resolution 12-9
 * states were extended, and the Kellar and Boydston holdings, which said April
 * 2016, are corrected (the end becomes derived; the start stays certified).
 * The roster adds two appointments no election shows: TimBen Boydston 2006-08,
 * to the seat Cameron Smyth left for the Assembly, and Bill Miranda from 2017,
 * to Dante Acosta's. Holdings that already exist (the Smyths) are kept, not
 * duplicated.
 *
 * PEOPLE. Every winner becomes a person record, a new trigger beside "stood
 * more than once" and "already in the archive": an elected officeholder is a
 * public figure. Seven are created, public facts only: Dennis Koontz, Frank
 * Ferry, Marsha McLean, Laurie Ender, Bill Miranda, Jason Gibbs, Patsy Ayala.
 * Candidacies then link on the full first name and surname, as before, which
 * now also reaches "Janice Heidt" (the alias Nathan confirmed) and "George
 * L.Pederson" (printed with no space after the initial).
 *
 * 2014 prints surnames only. The outcomes stand on the roster either way. The
 * links from those candidacies to people are behind $LINK_2014, off: Weste,
 * McLean and Acosta each match exactly one member of the roster, and Maria
 * Gutzeit's own record says she stood in April 2014, but that is a surname
 * plus a second record, not a full first name, and the rule is Nathan's.
 *
 * Also: Dante Acosta's body says he was elected "in November 2014"; the
 * election was on 8 April 2014 (City Clerk, 2014 results by precinct).
 *
 * Idempotent: holdings by person, office and start; people by title; every
 * other write compares before saving. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/council_winners_and_terms.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }
$LINK_2014 = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($LINK_2014 ? ' (with the 2014 surname links)' : '') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements(); $fs = Craft::$app->getFields();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$CITY = 394; $COUNCIL = 18327; $LEDGER_REC = 4967; $refused = [];
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);

/* ------------------------------------------------ the roster, checked against its record */
$LEDGER = [
    'Buck McKeon' => '1987-1992', 'Jan Heidt' => '1987-2000', 'Jo Anne Darcy' => '1987-2002', 'Carl Boyer' => '1987-1998',
    'Dennis Koontz' => '1987-1990', 'Jill Klajic' => '1990-1994, 1996-2000', 'George Pederson' => '1992-1996', 'Clyde Smyth' => '1994-1998',
    'Frank Ferry' => '1998-2014', 'Laurene Weste' => '1998—', 'Cameron Smyth' => '2000-2006, 2016—', 'Bob Kellar' => '2000—',
    'Marsha McLean' => '2002—', 'TimBen Boydston' => '2006-08, 2012-16', 'Laurie Ender' => '2008-2012', 'Dante Acosta' => '2014-2016', 'Bill Miranda' => '2017—',
];
$LEDGER_THROUGH = 2019;   /* written after 19 March 2020, before the November 2020 election */
$LEDGER_CITE = 'Leon Worden, "Santa Clarita City Council 1987—", the council ledger on SCVHistory.com, carried on the page for LW3149 (archive record #' . $LEDGER_REC . ')';
$ledgerText = preg_replace('~\s+~u', ' ', strip_tags(str_replace(['[lines]', '[/lines]'], ' ', (string)$get($LEDGER_REC)?->body)));
$ranges = [];
foreach ($LEDGER as $name => $yrs) {
    if (!str_contains($ledgerText, "$name $yrs")) { $refused[] = "the ledger on #$LEDGER_REC does not read \"$name $yrs\""; }
    foreach (explode(', ', $yrs) as $r) {
        [$s, $e] = array_pad(preg_split('~[-—]~u', $r), 2, '');
        $e = $e === '' ? null : (strlen($e) === 2 ? (int)(substr($s, 0, 2) . $e) : (int)$e);
        $ranges[$name][] = [(int)$s, $e];
    }
}
$serving = fn(string $name, int $y) => (bool)array_filter($ranges[$name] ?? [], fn($r) => $r[0] <= $y && ($r[1] === null || $r[1] > $y));

/* ------------------------------------------------ names */
$norm = fn($s) => strtolower(trim(preg_replace('~[^A-Za-z -]~', '', \craft\helpers\StringHelper::toAscii($s))));
$keysOf = function (string $name) use ($norm): array {
    $name = preg_replace('~\.(?=[A-Za-z]{2})~', '. ', $name);                    /* "George L.Pederson" */
    $name = preg_replace('~,?\s*\b(Jr|Sr|II|III|IV)\b\.?~', '', $name);
    $nicks = []; if (preg_match_all('~[“"]([^”"]+)[”"]~u', $name, $m)) { $nicks = $m[1]; }
    $plain = preg_replace(['~[“"][^”"]+[”"]~u', '~\([^)]*\)~'], ' ', $name);
    $t = array_values(array_filter(preg_split('~\s+~', trim($plain)), fn($w) => $w !== ''));
    if (count($t) < 2) { return []; }
    $last = $norm(end($t));
    $firsts = array_values(array_filter(array_slice($t, 0, -1), fn($w) => !preg_match('~^[A-Za-z]\.?$~', $w)));   /* never an initial */
    $keys = [];
    if ($firsts) { $keys[] = $norm($firsts[0]) . ' ' . $last; }
    foreach ($nicks as $n) { $keys[] = $norm($n) . ' ' . $last; }
    return array_unique($keys);
};
/* The printed name a candidacy carries, to the roster's name for that person. */
$ALIASES = ['Janice Heidt' => 'Jan Heidt', 'Timothy Ben Boydston' => 'TimBen Boydston'];
$ledgerKey = [];
foreach (array_keys($LEDGER) as $n) { foreach ($keysOf($n) as $k) { $ledgerKey[$k] = $n; } }
foreach ($ALIASES as $a => $n) { foreach ($keysOf($a) as $k) { $ledgerKey[$k] = $n; } }
$ledgerSurname = [];
foreach (array_keys($LEDGER) as $n) { $ledgerSurname[$norm(substr($n, strrpos($n, ' ') + 1))][] = $n; }
$toLedger = function (string $printed, bool $surnameOnly) use ($keysOf, $ledgerKey, $ledgerSurname, $norm) {
    if ($surnameOnly) { $s = $ledgerSurname[$norm($printed)] ?? []; return count($s) === 1 ? $s[0] : null; }
    foreach ($keysOf($printed) as $k) { if (isset($ledgerKey[$k])) { return $ledgerKey[$k]; } }
    return null;
};

/* The people: the roster's names to records, existing or to create. */
$PERSON = ['Buck McKeon' => 18791, 'Jan Heidt' => 15737, 'Jo Anne Darcy' => 16140, 'Carl Boyer' => 15808, 'Jill Klajic' => 15874,
    'George Pederson' => 18726, 'Clyde Smyth' => 15985, 'Laurene Weste' => 15929, 'Cameron Smyth' => 16380, 'Bob Kellar' => 21944,
    'TimBen Boydston' => 21946, 'Dante Acosta' => 341];
$NEW = ['Dennis Koontz' => 'Dennis M. Koontz', 'Frank Ferry' => '', 'Marsha McLean' => '', 'Laurie Ender' => '', 'Bill Miranda' => '', 'Jason Gibbs' => '', 'Patsy Ayala' => ''];
foreach ($PERSON as $n => $id) { $p = $get($id); if (!$p || !in_array($p->title, [$n, 'H. ' . $n], true)) { $refused[] = "#$id is not $n"; } }
echo 'PEOPLE' . PHP_EOL;
foreach ($NEW as $n => $alias) {
    $have = Entry::find()->section('persons')->status(null)->title($n)->one();
    $clash = array_filter(Entry::find()->section('persons')->status(null)->all(), fn($p) => $p->id !== $have?->id && array_intersect($keysOf($p->title), $keysOf($n)));
    if ($clash) { $refused[] = "a person already matches $n: " . implode(', ', array_map(fn($p) => '#' . $p->id . ' ' . $p->title, $clash)); }
    echo '   ' . ($have ? "#{$have->id} exists: $n" : "create $n, City council member" . ($alias ? ", also printed \"$alias\"" : '')) . PHP_EOL;
    if ($have) { $PERSON[$n] = $have->id; }
}
$heidt = $get(15737);
$needHeidtAlias = !in_array('Janice Heidt', array_map('trim', preg_split('~\n~', (string)$heidt->personAliases)), true);
if ($needHeidtAlias) { echo '   #15737 Jan Heidt: alias "Janice Heidt" (Nathan, 29 September)' . PHP_EOL; }

/* ------------------------------------------------ the elections, and the check */
$els = Entry::find()->section('elections')->status(null)->orderBy('electionDateEdtf asc')->all();
$cands = []; foreach ($els as $e) {
    $c = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->all();
    usort($c, fn($a, $b) => ($b->votes ?? 0) <=> ($a->votes ?? 0)); $cands[$e->id] = $c;
}
$year = fn($e) => (int)substr($e->electionDateEdtf, 0, 4);
$isNov = fn($e) => substr($e->electionDateEdtf, 5, 2) === '11';
$ord = fn($i) => ['first', 'second', 'third', 'fourth', 'fifth', 'sixth'][$i] ?? ($i + 1) . 'th';
echo PHP_EOL . 'WINNERS, election by election' . PHP_EOL;
$plan = [];    /* election id => [evidence for outcomes, evidence for seats, winners (ledger names), notes] */
foreach ($els as $e) {
    $y = $year($e); $surnameOnly = $e->electionDateEdtf === '2014-04-08'; $n = (int)$e->seatsUp;
    $win = array_slice($cands[$e->id], 0, $n); $lose = array_slice($cands[$e->id], $n);
    $winNames = array_map(fn($c) => $toLedger((string)$c->nameAsPrinted, $surnameOnly), $win);
    $cur = (string)$e->seatsUpEvidence->value;
    if (in_array($y, [1987, 2012], true)) { $plan[$e->id] = null; echo "   {$e->electionDateEdtf}  unchanged: " . ($y === 2012 ? 'certified, Resolution 12-9' : 'retrospective, the City\'s account of the first council') . PHP_EOL; continue; }
    if (array_filter($win, fn($c) => (string)$c->outcome->value !== 'elected') || array_filter($lose, fn($c) => (string)$c->outcome->value !== 'not-elected')) { $refused[] = "{$e->electionDateEdtf}: the stored outcomes are not the top $n of the count"; }
    if ($y <= $LEDGER_THROUGH) {
        $bad = [];
        foreach ($win as $i => $c) { if (!$winNames[$i] || !$serving($winNames[$i], $y)) { $bad[] = $c->nameAsPrinted . ' won but the roster does not show them serving from ' . $y; } }
        foreach ($lose as $c) { $ln = $toLedger((string)$c->nameAsPrinted, $surnameOnly); if ($ln && $serving($ln, $y)) { $bad[] = $c->nameAsPrinted . ' lost but the roster shows them serving from ' . $y; } }
        if ($bad) { $refused[] = $e->electionDateEdtf . ': ' . implode('; ', $bad); }
        $shown = implode('; ', array_map(fn($ln) => "$ln " . $LEDGER[$ln], $winNames));
        $next = $lose ? $lose[0]->nameAsPrinted : null;
        $seatNote = "Seats: $n. No document here states the number. The council has five seats and four-year staggered terms, filled three and two at alternate elections, and Resolution 12-9 certifies two for 2012, which fixes the alternation. The council roster held in the archive confirms it for this election: it shows the first $n of this count serving on from $y" . ($next ? ", and not $next, who came " . $ord($n) : '') . '. ' . $LEDGER_CITE . '.';
        $winNote = 'Winners: the first ' . $n . ' of this count. No declaring resolution is held. The council roster shows each of them serving from this election (' . $shown . '), and none of the other candidates serving in the term that followed. ' . $LEDGER_CITE . '.';
        $plan[$e->id] = ['roster', 'roster', $winNames, $seatNote, $winNote];
    } else {
        $seatWhy = $cur === 'contemporary' ? 'the single district seat on this ballot' : "the staggered terms, as in every election since 1990; the council roster held in the archive ends in 2020 and cannot confirm it";
        $seatNote = $cur === 'contemporary' ? null : "Seats: $n. No document here states the number. It is derived from the council's four-year staggered terms, filled three and two at alternate elections, anchored on the two seats Resolution 12-9 certifies for 2012.";
        $winNote = 'Winners: the first ' . $n . ' of this count, with ' . $n . ' seat' . ($n === 1 ? '' : 's') . ' up (' . $seatWhy . '). No declaring document is held, so the outcome is derived from the count alone and will be upgraded when the declaring resolution is obtained.';
        $plan[$e->id] = ['derived', $cur === 'contemporary' ? 'contemporary' : 'derived', $winNames, $seatNote, $winNote];
    }
    [$ev, $sev] = $plan[$e->id];
    echo '   ' . $e->electionDateEdtf . '  ' . str_pad("$n seat" . ($n === 1 ? '' : 's') . ", $sev", 22) . str_pad("outcomes $ev", 22) . implode(', ', array_map(fn($c) => $c->nameAsPrinted, $win)) . PHP_EOL;
}

/* After the roster, winners are named by their printed full names. */
$AFTER = ['Cameron M Smyth' => 'Cameron Smyth', 'Jason Gibbs' => 'Jason Gibbs', 'Laurene Weste' => 'Laurene Weste', 'Bill Miranda' => 'Bill Miranda', 'Marsha McLean' => 'Marsha McLean', 'Patsy Ayala' => 'Patsy Ayala'];
foreach ($els as $e) { if ($year($e) > $LEDGER_THROUGH) { foreach (array_slice($cands[$e->id], 0, (int)$e->seatsUp) as $i => $c) {
    $nm = $AFTER[(string)$c->nameAsPrinted] ?? null;
    if (!$nm || !array_intersect($keysOf((string)$c->nameAsPrinted), $keysOf($nm))) { $refused[] = "no person for the {$e->electionDateEdtf} winner {$c->nameAsPrinted}"; }
    $plan[$e->id][2][$i] = $nm;
} } }

/* ------------------------------------------------ the terms */
$termOf = function ($e, bool $nov) { $y = (int)substr($e->electionDateEdtf, 0, 4); return $nov ? ['December ' . $y, $y . '-12'] : ['April ' . $y, $y . '-04']; };
$byYear = []; foreach ($els as $e) { $byYear[$year($e)] = $e; }
/* The next election for the same seats. 2020's seats went to districts in 2024,
   and the one district canvass held (District 1) cannot say whose seat it was. */
$classNext = function (int $y) use ($byYear) { return $y === 2020 ? null : ($byYear[$y + 4] ?? null); };
$TERMS = [];   /* [person name, startPrinted, startEdtf, endPrinted|null, endEdtf|null, method, howEnded, startEv, endEv, notes[]] */
$e87 = $byYear[1987];
foreach (array_slice($cands[$e87->id], 0, 5) as $c) {
    $ln = $toLedger((string)$c->nameAsPrinted, false);
    $endY = in_array($ln, ['Buck McKeon', 'Jan Heidt'], true) ? 1992 : 1990;
    $again = in_array($ln, $plan[$byYear[$endY]->id][2], true);
    $TERMS[] = [$ln, 'December 15, 1987', '1987-12-15', 'April ' . $endY, $endY . '-04', 'elected', $again ? 'reelected' : 'expired', 'retrospective', 'roster',
        ['Elected on 3 November 1987, ' . $ord(array_search($c, $cands[$e87->id], true)) . ' of ' . count($cands[$e87->id]) . ' candidates for the five seats of the first council (City Clerk, Historical Election Results). The council took office on 15 December 1987 (Santa Clarita Valley Magazine, Winter 1987-88, archive record #4967).',
         "The first council's terms were staggered: the three who stood again in April 1990 and the two who stood in April 1992. $ln's " . ($endY === 1990 ? 'seat was on the 1990 ballot' : 'seat was on the 1992 ballot') . ". The roster gives $ln " . $LEDGER[$ln] . '. ' . $LEDGER_CITE . '.']];
}
foreach ($els as $e) {
    $y = $year($e); if ($y === 1987) { continue; }
    $p = $plan[$e->id] ?? null; $n = (int)$e->seatsUp;
    $winNames = $p ? $p[2] : array_map(fn($c) => $toLedger((string)$c->nameAsPrinted, false), array_slice($cands[$e->id], 0, $n));
    $ev = $p ? $p[0] : 'certified';
    [$sp, $se] = $termOf($e, $isNov($e));
    $nx = $classNext($y);
    foreach ($winNames as $i => $ln) {
        $c = $cands[$e->id][$i];
        $endP = null; $endE = null; $how = 'serving'; $endEv = null;
        if ($nx) {
            [$endP, $endE] = $termOf($nx, $isNov($nx));
            $again = in_array($ln, $plan[$nx->id][2] ?? [], true) || ($nx->electionDateEdtf === '2012-04-10' && in_array($ln, ['Bob Kellar', 'TimBen Boydston'], true));
            $how = $again ? 'reelected' : 'expired';
            $endEv = $isNov($nx) && !$isNov($e) ? 'derived' : ((int)substr($nx->electionDateEdtf, 0, 4) <= $LEDGER_THROUGH ? 'roster' : 'derived');
        } elseif (!in_array($y, [2022, 2024], true)) {
            /* 2020: the seat went to a district in 2024, whose canvass is not held */
            [$endP, $endE] = ['December ' . ($y + 4), ($y + 4) . '-12']; $how = 'unknown'; $endEv = 'derived';
        }
        /* The roster's mid-term departures. */
        if ($ln === 'Dante Acosta') { [$endP, $endE, $how, $endEv] = ['2016', '2016', 'left', 'roster']; }
        $notes = ["Elected on {$e->electionDate}, " . $ord($i) . ' of ' . count($cands[$e->id]) . " candidates for $n seat" . ($n === 1 ? '' : 's') . ', with ' . number_format((int)$c->votes) . ' votes.'];
        if ($ev === 'roster') { $notes[] = "The roster gives $ln " . $LEDGER[$ln] . '. ' . $LEDGER_CITE . '.'; }
        if ($ev === 'derived') { $notes[] = 'No declaring document is held: the outcome is read from the count, with the seats up derived from the staggered terms' . ($n === 1 ? ' (here, the single district seat on the ballot)' : '') . '.'; }
        if ($endEv === 'derived' && $endP) {
            $notes[] = "The term's end is derived: " . ($nx
                ? 'the seat was next filled at the election of ' . $nx->electionDate . ($isNov($nx) && !$isNov($e) ? ', after the city moved its elections to November, which extended the April term beyond four years' : '')
                : 'four years from its start. In 2024 the council began electing by district, and the District 3 canvass, which may name this seat, is not held') . '. Terms turn over in December: Cameron Smyth left the council on 10 December 2024.';
        }
        if ($ln === 'Dante Acosta') { $notes[] = 'He left the council in 2016 for the State Assembly (his record, #341); the roster gives him 2014-2016.'; }
        $TERMS[] = [$ln, $sp, $se, $endP, $endE, 'elected', $how, $ev, $endEv, array_values(array_filter($notes))];
    }
}
/* The roster's appointments. */
$TERMS[] = ['TimBen Boydston', '2006', '2006', 'April 2008', '2008-04', 'appointed', 'expired', 'roster', 'roster',
    ['Appointed. The roster gives TimBen Boydston "2006-08, 2012-16"; he won no election in 2006, and the only council vacancy of those years is the seat Cameron Smyth left in 2006 for the State Assembly. The seat was next filled at the election of April 8, 2008, at which he did not stand. ' . $LEDGER_CITE . '.']];
$TERMS[] = ['Bill Miranda', '2017', '2017', 'December 2018', '2018-12', 'appointed', 'expired', 'roster', 'derived',
    ['Appointed. The roster gives Bill Miranda "2017—"; he won no election before November 2018, and the vacancy is the seat Dante Acosta left in 2016 for the State Assembly. He was elected to a full term on November 6, 2018. ' . $LEDGER_CITE . '.']];

/* Two council portraits in the archive state their subjects' terms: a second source. */
$CAPTIONS = ['Dennis Koontz' => [4009, 'Santa Clarita City Council member, 1987-1990.'], 'George Pederson' => [4007, 'Santa Clarita City Council member, 1992-1996.']];
foreach ($TERMS as &$t) { if (isset($CAPTIONS[$t[0]]) && $t[5] === 'elected') {
    [$pid, $cap] = $CAPTIONS[$t[0]];
    if (!str_contains(preg_replace('~\s+~', ' ', strip_tags((string)$get($pid)->body)), $cap)) { $refused[] = "#$pid does not read \"$cap\""; continue; }
    $t[9][] = 'The caption of the council portrait, archive record #' . $pid . ': "' . $cap . '"';
} } unset($t);

/* Existing holdings are kept: skip any term that one already covers. */
$existing = [];
foreach (Entry::find()->section('officeHoldings')->status(null)->all() as $h) {
    $pid = $h->holdingPerson->status(null)->ids()[0] ?? null; $oid = $h->holdingOffice->status(null)->ids()[0] ?? null;
    $existing[] = ['id' => $h->id, 'pid' => $pid, 'oid' => $oid, 's' => (string)$h->termStartEdtf, 'e' => (string)$h->termEndEdtf, 'h' => $h];
}
$covered = function ($pid, string $s) use ($existing, $COUNCIL) {
    foreach ($existing as $x) { if ($x['pid'] == $pid && $x['oid'] == $COUNCIL && substr($x['s'], 0, 4) <= substr($s, 0, 4) && ($x['e'] === '' || substr($s, 0, 4) < substr($x['e'], 0, 4) || $x['s'] === $s)) { return $x['id']; } }
    return null;
};
echo PHP_EOL . 'OFFICE HOLDINGS, City Council Member, The City of Santa Clarita' . PHP_EOL;
$newTerms = []; $skipped = [];
usort($TERMS, fn($a, $b) => [$a[0], $a[2]] <=> [$b[0], $b[2]]);
foreach ($TERMS as $t) {
    $pid = $PERSON[$t[0]] ?? null;
    $cov = $pid ? $covered($pid, $t[2]) : null;
    if ($cov) { $skipped[] = "$t[0] from $t[1] (held by #$cov)"; continue; }
    $newTerms[] = $t;
    echo '   ' . str_pad($t[0], 16) . str_pad($t[1] . ' to ' . ($t[3] ?? 'now'), 38) . str_pad($t[5] . ', ' . $t[6], 22) . "start $t[7]" . ($t[8] ? ", end $t[8]" : '') . PHP_EOL;
}
echo '   kept, already held: ' . implode('; ', $skipped) . PHP_EOL;

/* Corrections to holdings that exist. */
$fixes = [];
foreach ($existing as $x) {
    $h = $x['h'];
    if (in_array($x['pid'], [21944, 21946], true) && $x['s'] === '2012-04' && $x['e'] === '2016-04') {
        $fixes[] = [$h->id, ['termEnd' => 'December 2016', 'termEndEdtf' => '2016-12', 'endEvidence' => 'derived', 'howEnded' => $x['pid'] === 21944 ? 'reelected' : 'expired'],
            'end April 2016 (certified) -> December 2016 (derived): the city moved its elections to November, and the seat was next filled on 8 November 2016' . ($x['pid'] === 21944 ? '; howEnded -> reelected' : '')];
    }
    if ($x['pid'] === 15985 && $x['s'] === '1994' && (string)$h->howEnded->value === 'unknown') {
        $fixes[] = [$h->id, ['howEnded' => 'expired'], 'howEnded unknown -> expired: he did not stand in 1998, and the roster gives him 1994-1998'];
    }
}
foreach ($fixes as [$id, , $why]) { echo "   #$id " . $get($id)->title . ": $why" . PHP_EOL; }
$acosta = $get(341);
$acostaFix = str_contains((string)$acosta->body, 'City Council in November 2014');
if ($acostaFix) { echo '   #341 Dante Acosta body: "Elected to the Santa Clarita City Council in November 2014" -> "in April 2014"' . PHP_EOL; }

/* ------------------------------------------------ candidacies to people */
$links = [];
foreach ($els as $e) { foreach ($cands[$e->id] as $c) {
    if ($c->candidacyPerson->status(null)->ids()) { continue; }
    $surnameOnly = $e->electionDateEdtf === '2014-04-08';
    if ($surnameOnly) {
        $ln = $toLedger((string)$c->nameAsPrinted, true);
        $target = $ln ?? ((string)$c->nameAsPrinted === 'Gutzeit' ? 'Maria Gutzeit' : null);
        if ($target) { $links[] = [$c, $target, true]; }
        continue;
    }
    $hit = null;
    foreach (array_merge(array_keys($PERSON), array_keys($NEW)) as $n) { if (array_intersect($keysOf((string)$c->nameAsPrinted), array_merge($keysOf($n), $n === 'Jan Heidt' ? $keysOf('Janice Heidt') : []))) { $hit = $n; } }
    if ($hit) { $links[] = [$c, $hit, false]; }
} }
$PERSON['Maria Gutzeit'] = 21582;
echo PHP_EOL . 'CANDIDACIES LINKED TO PEOPLE (' . count(array_filter($links, fn($l) => !$l[2])) . ')' . PHP_EOL;
foreach ($links as [$c, $n, $s]) { if (!$s) { echo '   ' . $c->candidacyElection->status(null)->one()->electionDateEdtf . ' ' . $c->nameAsPrinted . " -> $n" . PHP_EOL; } }
echo '2014, surname only, ' . ($LINK_2014 ? 'LINKING' : 'NOT LINKED ($LINK_2014 is off)') . ': ' . implode('; ', array_map(fn($l) => $l[0]->nameAsPrinted . " -> {$l[1]}", array_filter($links, fn($l) => $l[2]))) . PHP_EOL;

/* ------------------------------------------------ the scale */
$noDerived = array_filter(['outcomeEvidence', 'seatsUpEvidence', 'startEvidence', 'endEvidence'], fn($h) => !in_array('derived', array_map(fn($o) => $o['value'], $fs->getFieldByHandle($h)->options), true));
if ($noDerived) { echo PHP_EOL . 'NEEDS add_derived_evidence.php first: no "derived" option yet on ' . implode(', ', $noDerived) . PHP_EOL; }

echo PHP_EOL . 'SUMMARY: ' . count(array_filter($NEW, fn($a, $n) => !Entry::find()->section('persons')->status(null)->title($n)->exists(), ARRAY_FILTER_USE_BOTH)) . ' people, '
    . count($newTerms) . ' holdings, ' . count($fixes) . ' holding corrections, ' . count(array_filter($plan)) . ' elections re-evidenced, '
    . count(array_filter($links, fn($l) => !$l[2])) . ' candidacies linked' . ($LINK_2014 ? ' plus ' . count(array_filter($links, fn($l) => $l[2])) . ' from 2014' : '') . '.' . PHP_EOL;
echo 'REFUSED: ' . ($refused ? implode(' | ', $refused) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($refused) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
if ($noDerived) { echo 'REFUSING: run add_derived_evidence.php first' . PHP_EOL; return; }

/* ------------------------------------------------ apply, in one transaction: all of it lands or none */
$tx = Craft::$app->getDb()->beginTransaction();
try {
$short = [];
$pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
foreach ($NEW as $n => $alias) {
    if (Entry::find()->section('persons')->status(null)->title($n)->exists()) { $PERSON[$n] = Entry::find()->section('persons')->status(null)->title($n)->one()->id; continue; }
    $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->title = $n;
    $p->setFieldValues(['fullName' => $n, 'personAliases' => $alias, 'occupation' => 'City council member', 'roles' => [$COUNCIL],
        'recordProvenance' => 'council_winners_and_terms.php, 29 September 2026: elected to the Santa Clarita City Council; public facts only']);
    if (!$elements->saveElement($p)) { throw new \RuntimeException("person $n: " . json_encode($p->getFirstErrors())); }
    $PERSON[$n] = $p->id;
}
if ($needHeidtAlias) { $h = $get(15737); $h->setFieldValue('personAliases', trim((string)$h->personAliases . "\nJanice Heidt")); if (!$elements->saveElement($h)) { $short[] = 'Heidt alias'; } }
/* #4009, the portrait of Dennis Koontz, 1987-1990, now has its subject. */
$ph = $get(4009);
if (!in_array($PERSON['Dennis Koontz'], $ph->photoPeople->status(null)->ids())) { $ph->setFieldValue('photoPeople', array_merge($ph->photoPeople->status(null)->ids(), [$PERSON['Dennis Koontz']])); if (!$elements->saveElement($ph)) { $short[] = 'photo #4009'; } }

foreach ($els as $e) {
    $p = $plan[$e->id] ?? null; if (!$p) { continue; }
    [$ev, $sev, , $seatNote, $winNote] = $p;
    $notes = array_values(array_filter(array_map(fn($r) => (string)($r['note'] ?? ''), $e->footnotes ?? []), fn($t) => $t !== '' && !str_starts_with($t, 'Seats:') && !str_starts_with($t, 'Winners:')));
    $notes = array_merge($notes, array_filter([$seatNote, $winNote]));
    $e->setFieldValues(['seatsUpEvidence' => $sev, 'footnotes' => $fn($notes)]);
    if (!$elements->saveElement($e)) { $short[] = 'election ' . $e->electionDateEdtf; }
    foreach ($cands[$e->id] as $c) { if ((string)$c->outcomeEvidence->value !== $ev) { $c->setFieldValue('outcomeEvidence', $ev); if (!$elements->saveElement($c)) { $short[] = 'candidacy #' . $c->id; } } }
}
foreach ($links as [$c, $n, $s]) {
    if ($s && !$LINK_2014) { continue; }
    $c->setFieldValue('candidacyPerson', [$PERSON[$n]]); if (!$elements->saveElement($c)) { $short[] = 'link #' . $c->id; }
}
$ohSec = $svc->getSectionByHandle('officeHoldings'); $ohType = $svc->getEntryTypeByHandle('officeHolding');
foreach ($newTerms as [$n, $sp, $se, $ep, $ee, $method, $how, $sev, $eev, $notes]) {
    $pid = $PERSON[$n];
    if (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $pid, 'field' => 'holdingPerson'])->termStartEdtf($se)->exists()) { continue; }
    $h = new Entry(); $h->sectionId = $ohSec->id; $h->setTypeId($ohType->id);
    $h->setFieldValues(['holdingPerson' => [$pid], 'holdingOffice' => [$COUNCIL], 'holdingBody' => [$CITY], 'termStart' => $sp, 'termStartEdtf' => $se,
        'selectionMethod' => $method, 'howEnded' => $how, 'startEvidence' => $sev,
        'footnotes' => $fn($notes), 'recordProvenance' => 'council_winners_and_terms.php, 29 September 2026']
        + ($ep ? ['termEnd' => $ep, 'termEndEdtf' => $ee, 'endEvidence' => $eev] : []));
    if (!$elements->saveElement($h)) { throw new \RuntimeException("holding $n $sp: " . json_encode($h->getFirstErrors())); }
}
foreach ($fixes as [$id, $vals]) {
    $h = $get($id); $h->setFieldValues($vals);
    if (isset($vals['endEvidence'])) { $h->setFieldValue('footnotes', array_merge(array_map(fn($r) => ['number' => $r['number'] ?? '', 'note' => $r['note'] ?? '', 'source' => $r['source'] ?? ''], $h->footnotes ?? []),
        [['number' => (string)(count($h->footnotes ?? []) + 1), 'note' => 'The resolution gives a term of four years. The city then moved its elections to November, the seat was next filled at the election of November 8, 2016, and terms turn over in December, so the term ran to December 2016. The end is derived; the start is certified.', 'source' => 'editorial-2026']])); }
    if (!$elements->saveElement($h)) { $short[] = "fix #$id"; }
}
if ($acostaFix) {
    $a = $get(341); $a->setFieldValue('body', str_replace('City Council in November 2014', 'City Council in April 2014', (string)$a->body));
    $a->setFieldValue('editorNotes', array_merge(array_values(array_filter($a->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')),
        [['heading' => 'Corrected', 'position' => 'bottom', 'note' => 'This page said he was elected in November 2014. The council election was on 8 April 2014 (City of Santa Clarita, City Clerk, 2014 Election Results by Precinct).']]));
    if (!$elements->saveElement($a)) { $short[] = 'Acosta body'; }
}

if ($short) { throw new \RuntimeException('writes refused: ' . implode('; ', $short)); }
$tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }

/* Read back. */
$short = [];
foreach ($newTerms as [$n, , $se]) { if (!Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $PERSON[$n], 'field' => 'holdingPerson'])->termStartEdtf($se)->exists()) { $short[] = "holding $n $se missing"; } }
foreach ($els as $e) { $p = $plan[$e->id] ?? null; if (!$p) { continue; }
    $bad = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'candidacyElection'])->all();
    if (array_filter($bad, fn($c) => (string)$c->outcomeEvidence->value !== $p[0])) { $short[] = $e->electionDateEdtf . ' outcome evidence'; } }
$nH = (int)Entry::find()->section('officeHoldings')->status(null)->count();
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : "OK: $nH holdings in all") . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('council_winners_and_terms.php', count($newTerms) + count($fixes) + count($links), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'council winners on the roster and the count; a holding for every term; derived on the evidence scale');
if ($short) { throw new \RuntimeException('council_winners_and_terms: ' . implode('; ', $short)); }
