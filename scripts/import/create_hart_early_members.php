/**
 * A person record and an office holding for every trustee on Leon Worden's roster of the Hart board
 * who has neither yet: the 39 members whose service ended before 1995, William S. Dinsenbacher (1987
 * to 1997, defeated 1997) and the two 2013 appointees, Chris Fall and Robert P. Hall.
 *
 * Nathan, 3 October 2026: "Yes, create the 39 pre-1995 members plus Dinsenbacher and the 2013
 * appointees, as a separate pass with its own dry run and name-variant check. Holding office
 * qualifies and the roster is the source." And: "Keep the two Frews apart, with a note on each saying
 * the other exists and why they are held separate. The 1923 Chamber minutes naming a T.M. Frew Jr. is
 * good supporting evidence for the trustee."
 *
 * THE SOURCE. inventory/legacy/hart-board-roster.json (extract_hart_roster.py), checked against the
 * page's manifest hash as derive_board_holdings.php checks it. Student board members are not trustees
 * and are not created.
 *
 * THE PEOPLE. Title and fullName as derive_board_holdings.php makes them: given name and surname,
 * middle initials dropped ("Gerald Heidt"), initials kept where nothing else is printed ("S. S.
 * Donaldson"), "Dr." dropped, "Jr." kept. Every other printing is an alias. Role School Board Member
 * (#26964). Historical era by the assign_person_eras.php rule (two thirds of the years in office);
 * otherwise left for that pass. Public facts only: name, office, years.
 *
 * THE HOLDINGS. One per continuous tenure on the roster: the roster's consecutive terms joined where
 * one ends as the next begins. Body #21588, office #26964. Start and end EDTF from the roster rows;
 * selectionMethod only where the roster says (elected, appointed; the five of March 9, 1945 sit on a
 * board it heads "First Board Elected"); howEnded from the roster's wording: resigned (on the member's
 * row, or "who resigned" on the successor's), recalled, defeated or did not seek reelection (expired),
 * otherwise unknown. startEvidence and endEvidence "roster". Footnotes quote the rows.
 *
 * DINSENBACHER'S 1997 LOSS. His CEDA candidacy of November 4, 1997 is linked to the new record and
 * cited in the holding's footnotes.
 *
 * THE TWO FREWS. Thomas M. Frew Jr. (trustee 1945 to 1951) is created; #18783 Tom Frew (the Tom Frew
 * of Reynolds and of the 1997 and 1998 columns) is not touched except for an editor's note. Each gets a
 * note naming the other and saying why they are kept apart.
 *
 * THE NAME-VARIANT CHECK. derive_board_holdings.php's: each new name against every existing person and
 * every other new name, nicknames folded, initials matched; Jr. set aside only in the exact-key match
 * (decided merges); Sr, II, III and IV keep names apart. Anything flagged is printed, not merged.
 *
 * Idempotent: people by title (a title already held by a record this script did not make is refused),
 * holdings by person, body and termStartEdtf, the link when the candidacy has no person, the editor's
 * notes by heading. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_hart_early_members.php'))"
 */

use craft\elements\{Entry, Category};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$svc = Craft::$app->getEntries(); $elements = Craft::$app->getElements();
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$bad = [];
$PROV = 'create_hart_early_members.php, 3 October 2026';
$HART = 21588; $RS = 26964; $TOMFREW = 18783; $DINS_CAND = 25845;
foreach ([$HART => 'William S. Hart Union High School District', $RS => 'School Board Member', $TOMFREW => 'Tom Frew'] as $id => $t) { if ($get($id)?->title !== $t) { $bad[] = "#$id is not $t"; } }

/* ------------------------------------------------ the roster */
$R = json_decode(@file_get_contents("$root/inventory/legacy/hart-board-roster.json"), true);
$RHTM = "$root/inventory/legacy/fetched/hartschoolboardmembers.htm";
if (!$R || !is_file($RHTM) || hash_file('sha256', $RHTM) !== ($R['meta']['sha256'] ?? '') || empty($R['meta']['manifest_matched'])) { $bad[] = 'the Hart roster or its page is missing, or differs from the manifest'; $R = ['meta' => [], 'boards' => [], 'members' => []]; }
$C_ROSTER = 'Leon Worden, William S. Hart Union High School District Governing Board Members, SCVHistory.com, /scvhistory/hartschoolboardmembers.htm';
$C_5017 = 'California Education Code, section 5017: a board member elected at a regular election holds office "for a term of four years commencing on the second Friday in December next succeeding his or her election" (the first Friday until AB 2449 took effect in 2019).';
$RLASTB = end($R['boards'])['label'] ?? '';
$prE = function (?string $e) {
    if (!$e) { return ''; }
    $p = explode('-', $e);
    return count($p) === 1 ? $p[0] : (count($p) === 2 ? date('F Y', strtotime("$e-01")) : date('F j, Y', strtotime($e)));
};
$students = count(array_filter($R['members'], fn($m) => $m['student']));

/* ------------------------------------------------ names (derive_board_holdings.php) */
$norm = fn($s) => strtolower(trim(preg_replace('~\s+~', ' ', preg_replace('~[^A-Za-z -]~', '', \craft\helpers\StringHelper::toAscii((string)$s)))));
$keysOf = function (string $name) use ($norm): array {
    $name = preg_replace(['~\.(?=[A-Za-z]{2})~', '~,?\s*\b(Jr|P\.E)\b\.?~i'], ['. ', ''], $name);
    $nicks = []; if (preg_match_all('~[“"]([^”"]+)[”"]~u', $name, $m)) { $nicks = $m[1]; }
    $plain = preg_replace(['~[“"][^”"]+[”"]~u', '~\([^)]*\)~'], ' ', $name);
    $t = array_values(array_filter(preg_split('~\s+~', trim($plain)), fn($w) => $w !== ''));
    if (count($t) < 2) { return []; }
    $li = count($t) - 1; while ($li > 1 && preg_match('~^(de|la|del|van|von)$~i', $t[$li - 1])) { $li--; }
    $last = str_replace(' ', '', $norm(implode(' ', array_slice($t, $li))));
    $firsts = array_values(array_filter(array_slice($t, 0, $li), fn($w) => !preg_match('~^[A-Za-z]\.?$~', $w)));
    if (!$firsts) { return [$norm(implode(' ', $t))]; }
    $keys = [$norm(rtrim($firsts[0], '.')) . ' ' . $last];
    foreach ($nicks as $n) { $keys[] = $norm($n) . ' ' . $last; }
    return array_unique($keys);
};
/* Title: the printing with the longest given name; "Dr." and middle initials dropped; initials kept where
   no given name is printed ("S.S. Donaldson" -> "S. S. Donaldson", as "E. G. Gladbach"); "Jr." kept. */
$titleOf = function (array $printings) {
    $best = null; $bestLen = -1;
    foreach ($printings as $p) {
        $q = preg_replace(['~^Dr\.?\s+~', '~[“"][^”"]+[”"]~u', '~\.(?=[A-Za-z])~'], ['', ' ', '. '], $p);
        $jr = (bool)preg_match('~,?\s*\bJr\b\.?$~', $q); $q = preg_replace('~,?\s*\bJr\b\.?$~', '', $q);
        $w = array_values(array_filter(preg_split('~\s+~', trim($q)), fn($x) => $x !== ''));
        $last = array_pop($w);
        $given = array_values(array_filter($w, fn($x) => !preg_match('~^[A-Za-z]\.?$~', $x)));
        $t = $given ? $given[0] . ' ' . $last : implode(' ', array_map(fn($x) => rtrim($x, '.') . '.', $w)) . ' ' . $last;
        if ($jr) { $t .= ' Jr.'; }
        $len = $given ? strlen($given[0]) : 0;
        if ($len > $bestLen) { $best = $t; $bestLen = $len; }
    }
    return $best;
};

$pIndex = []; $allPeople = []; $byTitle = [];
foreach (Entry::find()->section('persons')->status(null)->all() as $p) {
    $names = [$p->title, (string)$p->fullName]; foreach (preg_split('~\n~', (string)$p->personAliases) as $a) { if (trim($a)) { $names[] = trim($a); } }
    $allPeople[$p->id] = ['title' => $p->title, 'names' => array_values(array_unique(array_filter($names))), 'prov' => (string)$p->recordProvenance];
    $byTitle[$p->title][] = $p->id;
    foreach ($names as $n) { foreach ($keysOf($n) as $k) { $pIndex[$k][$p->id] = $p->title; } }
}
$hartHolders = [];
foreach (Entry::find()->section('officeHoldings')->status(null)->relatedTo(['targetElement' => $HART, 'field' => 'holdingBody'])->all() as $h) {
    foreach ($h->holdingPerson->status(null)->ids() as $pid) { $hartHolders[$pid][] = ['id' => $h->id, 's' => (string)$h->termStartEdtf]; }
}

/* ------------------------------------------------ who is to be created */
$targets = []; $linked = []; $ambig = [];
foreach ($R['members'] as $m) {
    if ($m['student']) { continue; }
    $hits = [];
    foreach ($m['printings'] as $pn) { foreach ($keysOf($pn) as $k) { foreach (array_keys($pIndex[$k] ?? []) as $id) { $hits[$id] = true; } } }
    $hits = array_keys($hits);
    $mine = array_values(array_filter($hits, fn($id) => str_starts_with($allPeople[$id]['prov'], $PROV)));
    $tied = array_values(array_filter($hits, fn($id) => isset($hartHolders[$id]) && !in_array($id, $mine, true)));
    if ($tied) { $linked[] = $m['printings'][0] . ' = #' . implode(', #', $tied); continue; }
    if (array_diff($hits, $mine)) { $ambig[] = $m['printings'][0] . ' matches existing ' . implode(', ', array_map(fn($id) => "#$id " . $allPeople[$id]['title'], array_diff($hits, $mine))) . ', which has no Hart holding'; }
    $targets[$m['key']] = $m;
}
foreach ($ambig as $a) { $bad[] = "roster member $a: decide before creating"; }
$pre = array_filter($targets, fn($m) => (end($m['terms'])['end'] ?? '9999') < '1995');
$named = array_diff_key($targets, $pre);
if (count($pre) !== 39 || array_keys($named) !== ['dinsenbacher|W', 'fall|C', 'hall|R']) {
    $bad[] = 'expected the 39 pre-1995 members plus Dinsenbacher, Fall and Hall; found ' . count($pre) . ' and ' . implode(', ', array_keys($named));
}

/* ------------------------------------------------ tenures */
$boardIdx = []; foreach ($R['boards'] as $i => $b) { $boardIdx[$b['label']] = $i; }
$allRows = []; foreach ($R['members'] as $m) { if (!$m['student']) { foreach ($m['rows'] as $r) { $allRows[] = $r + ['mkey' => $m['key']]; } } }
$P = [];   /* key => person plan */
$TEN = []; /* holdings */
foreach ($targets as $mk => $m) {
    $printings = $m['printings'];
    $surname = explode('|', $mk)[0];
    $extra = [];
    if ($mk === 'frew|T') { $extra[] = 'T.M. Frew Jr.'; }   /* the 1923 Chamber minutes (Nathan: supporting evidence) */
    if ($mk === 'dinsenbacher|W') { $extra[] = 'William "Bill" Dinsenbacher'; }   /* CEDA 1997 */
    $title = $titleOf($printings);
    $aliases = array_values(array_unique(array_filter(array_merge($printings, $extra), fn($a) => $norm($a) !== $norm($title))));
    $P[$mk] = ['key' => $mk, 'title' => $title, 'aliases' => $aliases, 'printings' => array_merge($printings, $extra), 'tenures' => []];
    /* join the roster's consecutive terms */
    $groups = []; $cur = [];
    foreach ($m['terms'] as $t) {
        if ($cur && end($cur)['end'] === $t['start'] && end($cur)['howEnded'] === 'reelected') { $cur[] = $t; continue; }
        if ($cur) { $groups[] = $cur; } $cur = [$t];
    }
    if ($cur) { $groups[] = $cur; }
    foreach ($groups as $g) {
        $first = $g[0]; $last = end($g);
        $name = $title;
        $sel = $first['selection'];
        $first1945 = $first['startBoard'] === 'March 9, 1945 (First Board Elected)';
        if (!$sel && $first1945) { $sel = 'elected'; }
        $how = $last['howEnded'];
        $end = $last['end'];
        $notes = [];
        /* the rows quoted: the start, each reelection or event row, the end */
        $q = [[$first['startBoard'], $first['startBasis']]];
        foreach (array_slice($g, 1) as $t) { $q[] = [$t['startBoard'], $t['startBasis']]; }
        if ($end !== null) { $q[] = [$last['endBoard'], $last['endBasis']]; }
        $seen = []; $q = array_values(array_filter($q, function ($x) use (&$seen) { $k = $x[0] . '|' . $x[1]; if (isset($seen[$k])) { return false; } $seen[$k] = true; return true; }));
        $parts = array_map(fn($x) => "on the board of {$x[0]} as \"{$x[1]}\"", $q);
        if (count($parts) === 1) { $s = $parts[0]; }
        else { $lastP = array_pop($parts); $s = implode('; ', $parts) . '; and ' . ($end === null ? '' : 'last ') . $lastP; }
        if ($end === null) { $s .= ($q[count($q) - 1][0] === $RLASTB ? '' : ", and on every board after it to the last it prints, $RLASTB"); }
        $notes[] = "Leon Worden's roster of the Hart board lists $name $s ($C_ROSTER).";
        if ($first1945) {
            $bn = $R['boards'][0]['notes'][0] ?? '';
            $notes[] = 'The roster heads this board "March 9, 1945 (First Board Elected)"' . ($bn !== '' ? ' and notes: "' . preg_replace('~^Note:\s*~', '', $bn) . '"' : '') . (preg_match('~[.!?]$~', $bn) ? '' : '.');
        }
        if (($first['startNote'] ?? null) && $sel === 'appointed') {
            $notes[] = 'The roster prints no date for the appointment' . (($first['predecessorRow'] ?? null) ? ', which completed the term of the member it lists as "' . $first['predecessorRow'] . '"; the year given here is that resignation\'s.' : '; the year given here is the first of the board that lists it.');
        }
        /* how it ended */
        $en = (string)($last['endNote'] ?? '');
        $succ = null;
        if ($end !== null && $how === 'unknown') {
            foreach ($allRows as $r) { if ($r['mkey'] !== $mk && preg_match('~unexpired term of ' . preg_quote(ucfirst($surname), '~') . ', who resigned~', $r['raw'])) { $succ = $r; } }
            if ($succ) { $how = 'resigned'; }
        }
        if (str_starts_with($en, 'not listed on the next board')) {
            $nb = preg_replace('~^not listed on the next board, ~', '', $en);
            $notes[] = "The next board printed, $nb, does not list $name: the end given here is that board's start" . ($succ ? '.' : ', and the roster does not say how the service ended.');
        }
        if ($succ) { $notes[] = "The roster gives the reason on the successor's row, on the board of {$succ['boardLabel']}: \"{$succ['raw']}\"."; }
        if (str_starts_with($en, 'no date printed')) { $notes[] = 'The roster prints no date for the resignation; the year given here is that of the board that notes it.'; }
        if ($end === null) { $notes[] = "The roster stops with the board of $RLASTB. When this service ended, and how, is not recorded here."; $how = 'unknown'; }
        /* the one misprint the extractor groups: "D.R. Huntsinger" (1968), between C.R. Huntsinger boards */
        $mis = $mk === 'huntsinger|C' ? array_values(array_filter($m['printings'], fn($pn) => $pn === 'D.R. Huntsinger')) : [];
        foreach ($mis as $pn) { $mr = array_values(array_filter($m['rows'], fn($r) => $r['name'] === $pn))[0] ?? null; if ($mr) { $notes[] = "On the board of {$mr['boardLabel']}, between boards that print \"C.R. Huntsinger\", the roster prints \"{$mr['raw']}\"; it is taken as the same member."; } }
        if ($sel === 'elected' && strlen($first['start']) === 7 && $first['start'] >= '1979-12' && substr($first['start'], 5, 2) === '12') { $notes[] = $C_5017; }
        $TEN[] = ['mk' => $mk, 'se' => $first['start'], 'sp' => $prE($first['start']), 'ee' => $end, 'ep' => $prE($end), 'sel' => $sel, 'how' => $how,
            'sev' => 'roster', 'eev' => $end !== null ? 'roster' : null, 'notes' => $notes, 'terms' => count($g)];
    }
}
$howMap = ['expired' => 1, 'reelected' => 1, 'resigned' => 1, 'died' => 1, 'recalled' => 1, 'unknown' => 1];
foreach ($TEN as $t) {
    if (!isset($howMap[$t['how']]) || $t['how'] === 'reelected') { $bad[] = "{$P[$t['mk']]['title']} {$t['se']}: howEnded \"{$t['how']}\" at the end of a tenure"; }
    if ($t['ee'] !== null && $t['ee'] < $t['se']) { $bad[] = "{$P[$t['mk']]['title']} {$t['se']}: ends before it starts"; }
}

/* ------------------------------------------------ Dinsenbacher's 1997 candidacy */
$links = [];
$dc = $get($DINS_CAND);
$dEl = $dc?->candidacyElection->status(null)->one();
if (!$dc || $dc->section->handle !== 'candidacies' || (string)$dc->nameAsPrinted !== 'William "Bill" Dinsenbacher' || !$dEl || (string)$dEl->electionDateEdtf !== '1997-11-04' || ($dEl->electionBody->status(null)->ids()[0] ?? null) !== $HART) {
    $bad[] = "#$DINS_CAND is not William \"Bill\" Dinsenbacher's Hart candidacy of November 4, 1997";
} else {
    $cs = Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $dEl, 'field' => 'candidacyElection'])->all();
    usort($cs, fn($a, $b) => ($b->votes ?? 0) <=> ($a->votes ?? 0));
    $place = array_search($DINS_CAND, array_map(fn($c) => $c->id, $cs), true);
    $ord = ['first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth'][$place] ?? ($place + 1) . 'th';
    $fnote = implode(' ', array_map(fn($r) => (string)($r['note'] ?? ''), $dc->footnotes ?? []));
    $row = preg_match('~CEDA 1997, row (\d+)~', $fnote, $mm) ? $mm[1] : null;
    if (!$row || (string)$dc->outcome->value !== 'not-elected') { $bad[] = "#$DINS_CAND: no CEDA row in its footnotes, or not marked not-elected"; }
    $seats = (int)$dEl->seatsUp;
    if ($dEl->electionDistrict->status(null)->exists()) { $bad[] = "#{$dEl->id}: the 1997 contest names a trustee area; the footnote says at large"; }
    $dn = "He stood at the election of November 4, 1997 and lost: $ord of " . count($cs) . ' candidates for ' . ($seats > 1 ? "$seats seats" : 'the seat') . ' at large, with ' . number_format((int)$dc->votes) . ' votes (California Elections Data Archive (CEDA), CEDA1997Data.xls, row ' . $row . ', a compilation of the County\'s returns).';
    foreach ($TEN as &$t) { if ($t['mk'] === 'dinsenbacher|W') { $t['notes'][] = $dn; } } unset($t);
    if (!$dc->candidacyPerson->status(null)->ids()) { $links[] = [$DINS_CAND, 'dinsenbacher|W']; }
    elseif (!str_starts_with($allPeople[$dc->candidacyPerson->status(null)->ids()[0]]['prov'] ?? '', $PROV)) { $bad[] = "#$DINS_CAND is already linked to another person"; }
}

/* ------------------------------------------------ the two Frews */
$FREW_HEAD = 'Two men named Thomas Frew';
$C_1923 = 'Newhall Chamber of Commerce, "Minutes of 2-21-1923 (First) Meeting," as carried on SCVHistory.com, /scvhistory/chamber1923-022123.htm';
$m1923 = @file_get_contents("$root/inventory/legacy/fetched/chamber1923-022123.txt");
if (!$m1923 || !str_contains($m1923, 'T.M. Frew Jr. (withdrew)')) { $bad[] = 'the 1923 Chamber minutes do not read "T.M. Frew Jr. (withdrew)"'; }
$NOTE_JR = ['heading' => $FREW_HEAD, 'position' => 'bottom',
    'note' => 'This is the Thomas M. Frew Jr. of the Hart district\'s first board, elected March 9, 1945, who resigned in 1951; Leon Worden\'s roster of the board notes that he "had retired from the Newhall board" when the board was formed. A "T.M. Frew Jr." was nominated for secretary-treasurer, and withdrew, at the first meeting of the Newhall Chamber of Commerce on February 21, 1923 (' . $C_1923 . '). The record named Tom Frew is a later man of the family: the Tom Frew whom Jerry Reynolds thanks for proofreading Santa Clarita: Valley of the Golden Dream (Preface), and whom he lists as "Tom Frew IV" (Bibliography), and whom Leon Worden\'s Signal columns of 1997 and 1998 name as president of the Santa Clarita Valley Historical Society. The two are kept apart: nearly fifty years separate their recorded activity, the sources give them different suffixes, and no source joins them.'];
$NOTE_TOM = ['heading' => $FREW_HEAD, 'position' => 'bottom',
    'note' => 'This Tom Frew is the one in sources of the 1990s: Jerry Reynolds thanks him for proofreading Santa Clarita: Valley of the Golden Dream (Preface) and lists "Tom Frew IV" among those he consulted (Bibliography), and Leon Worden\'s Signal columns of 1997 and 1998 name him as president of the Santa Clarita Valley Historical Society. A separate record, Thomas Frew Jr., is the Thomas M. Frew Jr. who sat on the first board of the Hart district from 1945 to 1951 (Leon Worden, William S. Hart Union High School District Governing Board Members, SCVHistory.com, /scvhistory/hartschoolboardmembers.htm), the "T.M. Frew Jr." of the Newhall Chamber of Commerce minutes of February 21, 1923. The two are kept apart: nearly fifty years separate their recorded activity, the sources give them different suffixes, and no source joins them.'];
$tf = $get($TOMFREW);
$tfLayout = []; foreach ($tf?->getFieldLayout()->getCustomFields() ?? [] as $f) { $tfLayout[$f->handle] = true; }
if (!isset($tfLayout['editorNotes'])) { $bad[] = "#$TOMFREW has no editorNotes field"; }
$cleanNotes = fn($rows) => array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom') ?: 'bottom'],
    array_filter((array)$rows, fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$tfHas = isset($tfLayout['editorNotes']) && array_filter($cleanNotes($tf->editorNotes), fn($r) => $r['heading'] === $FREW_HEAD);
$tfCited = $tf ? array_map(fn($e) => $e->id, Entry::find()->status(null)->relatedTo($tf)->all()) : [];
foreach ([817, 2167, 2171, 12422, 12252, 12184] as $cid) { if (!in_array($cid, $tfCited, true)) { $bad[] = "#$TOMFREW is no longer related to #$cid"; } }

/* ------------------------------------------------ eras (assign_person_eras.php rule) */
$eras = [];
foreach (Category::find()->group('historicalEra')->all() as $cat) {
    if (in_array($cat->id, [163, 169], true) || !preg_match('~\((?:to )?(\d{4})?[^\d]*(\d{4}|present)\)~u', $cat->title, $mm)) { continue; }
    $eras[$cat->id] = [$mm[1] !== '' ? (int)$mm[1] : -10000, $mm[2] === 'present' ? (int)date('Y') : (int)$mm[2], $cat->title];
}
$eraOf = function (int $y) use ($eras) { foreach ($eras as $id => [$f, $to]) { if ($y >= $f && $y <= $to) { return $id; } } return null; };
$yr = fn($s) => (int)substr((string)$s, 0, 4);
foreach ($P as $mk => &$np) {
    $anch = [];
    foreach ($TEN as $t) { if ($t['mk'] !== $mk) { continue; } $s = $yr($t['se']); $e = $t['ee'] ? $yr($t['ee']) : 2014; for ($q = $s; $q <= max($s, $e); $q++) { $anch[] = $q; } }
    $cnt = array_count_values(array_filter(array_map($eraOf, $anch)));
    arsort($cnt); $top = array_key_first($cnt);
    $np['era'] = ($top && $cnt[$top] * 3 >= count($anch) * 2) ? $top : null;
    $np['eraWhy'] = $top ? $eras[$top][2] . ' ' . $cnt[$top] . ' of ' . count($anch) : 'none';
} unset($np);

/* ------------------------------------------------ idempotence */
$pid = [];   /* mk => existing id made by this script */
foreach ($P as $mk => $np) {
    $ids = $byTitle[$np['title']] ?? [];
    $mine = array_values(array_filter($ids, fn($id) => str_starts_with($allPeople[$id]['prov'], $PROV)));
    if ($mine) { $pid[$mk] = $mine[0]; }
    elseif ($ids) { $bad[] = "the title \"{$np['title']}\" is already held by #" . implode(', #', $ids) . ', which this script did not make'; }
}
$plan = []; $held = [];
foreach ($TEN as $t) {
    if (isset($pid[$t['mk']]) && Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $pid[$t['mk']], 'field' => 'holdingPerson'], ['targetElement' => $HART, 'field' => 'holdingBody']])->termStartEdtf($t['se'])->exists()) { $held[] = $t; continue; }
    $plan[] = $t;
}
$newP = array_filter($P, fn($np) => !isset($pid[$np['key']]));
$frewJrNeedsNote = true;
if (isset($pid['frew|T'])) { $fj = $get($pid['frew|T']); $frewJrNeedsNote = !array_filter($cleanNotes($fj->editorNotes), fn($r) => $r['heading'] === $FREW_HEAD); }

/* ------------------------------------------------ wording and lengths */
$BAD = '~\b(WordPress|the import|on import|imported from|migrated|migration|legacy mirror|in the mirror|inventory/|SHA-?(1|256)|checksums?|manifest|dry run|the script|scripts? (that|which)|with Nathan|Nathan\'s|Claude)\b~i';
$texts = [];
foreach ($TEN as $t) { foreach ($t['notes'] as $n) { $texts[] = [$P[$t['mk']]['title'] . ' ' . $t['se'], $n]; } }
$texts[] = ['note on Thomas Frew Jr.', $NOTE_JR['note']]; $texts[] = ["note on #$TOMFREW", $NOTE_TOM['note']];
foreach ($texts as [$who, $n]) {
    $u = preg_replace('~https?://\S+|\S+\.(?:com|org|gov|net)/\S*~', ' ', $n);
    if (preg_match($BAD, $u, $mm) || preg_match('~\x{2014}|\x{2013}|inventory/|scripts/|\.json|\.php~u', $n, $mm)) { $bad[] = "$who: a note reads \"{$mm[0]}\""; }
}
$pProv = "$PROV: Hart board member on Leon Worden's roster (holding office qualifies); public facts only";
$hProv = "$PROV: a tenure from Leon Worden's Hart board roster, hartschoolboardmembers.htm";
foreach ([$pProv, $hProv] as $s) { if (mb_strlen($s) > 255) { $bad[] = 'a recordProvenance runs over 255 characters: ' . $s; } }
foreach ($P as $np) { if (mb_strlen($np['title']) > 255) { $bad[] = "title too long: {$np['title']}"; } }

/* ------------------------------------------------ the report */
echo PHP_EOL . "SOURCE: Leon Worden's roster, sha256 " . substr($R['meta']['sha256'] ?? '?', 0, 16) . '..., manifest matched: ' . json_encode($R['meta']['manifest_matched'] ?? null) . '; '
    . ($R['meta']['board_count'] ?? 0) . ' boards, ' . ($R['meta']['member_count'] ?? 0) . ' members, ' . $students . ' student members (not trustees, not created).' . PHP_EOL;
echo 'Members already with a record and a Hart holding (' . count($linked) . '): ' . implode('; ', $linked) . PHP_EOL;
echo 'To create: ' . count($pre) . ' pre-1995-only members, plus ' . implode(', ', array_map(fn($k) => $P[$k]['title'], array_keys($named))) . '.' . PHP_EOL;

echo PHP_EOL . '(1) PEOPLE TO CREATE (' . count($newP) . ')' . ($pid ? '; already made by this script: ' . count($pid) : '') . PHP_EOL;
$sorted = $P;
$firstStart = []; foreach ($TEN as $t) { $firstStart[$t['mk']] ??= $t['se']; }
uasort($sorted, fn($a, $b) => [$firstStart[$a['key']], $a['title']] <=> [$firstStart[$b['key']], $b['title']]);
foreach ($sorted as $mk => $np) {
    echo '   ' . str_pad($np['title'] . (isset($pid[$mk]) ? " (#{$pid[$mk]}, exists)" : ''), 26) . 'era ' . ($np['era'] ? $eras[$np['era']][2] : 'left for assign_person_eras.php') . ' (' . $np['eraWhy'] . ')' . ($np['aliases'] ? '; aliases: ' . implode('; ', $np['aliases']) : '') . PHP_EOL;
}
echo '   role: School Board Member (#26964) on each; fullName = title; recordProvenance: "' . $pProv . '" (' . mb_strlen($pProv) . ' chars)' . PHP_EOL;

echo PHP_EOL . '(2) HOLDINGS TO CREATE (' . count($plan) . '; ' . count($held) . ' already held), body #21588, office #26964' . PHP_EOL;
printf("   %-24s %-11s -> %-11s %-10s %-9s %-6s %-6s %s\n", 'person', 'start', 'end', 'selection', 'howEnded', 'sEv', 'eEv', 'terms joined');
foreach ($sorted as $mk => $np) { foreach ($TEN as $t) { if ($t['mk'] !== $mk) { continue; }
    printf("   %-24s %-11s -> %-11s %-10s %-9s %-6s %-6s %d%s\n", mb_substr($np['title'], 0, 24), $t['se'], $t['ee'] ?? '(blank)', $t['sel'] ?? '(blank)', $t['how'], $t['sev'], $t['eev'] ?? '-', $t['terms'], in_array($t, $held, true) ? '  ALREADY HELD' : '');
} }
$hw = array_count_values(array_map(fn($t) => $t['how'], $plan)); ksort($hw);
$sw = array_count_values(array_map(fn($t) => $t['sel'] ?? '(blank)', $plan)); ksort($sw);
echo '   howEnded: ' . implode(', ', array_map(fn($k, $v) => "$k $v", array_keys($hw), $hw)) . '. selectionMethod: ' . implode(', ', array_map(fn($k, $v) => "$k $v", array_keys($sw), $sw)) . '.' . PHP_EOL;
echo '   recordProvenance: "' . $hProv . '" (' . mb_strlen($hProv) . ' chars)' . PHP_EOL;

echo PHP_EOL . 'FOOTNOTES, as they would be written:' . PHP_EOL;
foreach ($sorted as $mk => $np) { foreach ($TEN as $t) { if ($t['mk'] !== $mk) { continue; }
    echo '   ' . $np['title'] . ' ' . $t['se'] . ' to ' . ($t['ee'] ?? '(blank)') . PHP_EOL;
    foreach ($t['notes'] as $i => $n) { echo '      ' . ($i + 1) . '. ' . $n . PHP_EOL; }
} }

echo PHP_EOL . '(3) CANDIDACY LINKS (' . count($links) . '): ' . ($links ? implode('; ', array_map(fn($l) => "#{$l[0]} \"" . $get($l[0])->nameAsPrinted . '", Hart, November 4, 1997, lost -> ' . $P[$l[1]]['title'], $links)) : 'none to make') . PHP_EOL;

echo PHP_EOL . '(4) THE TWO FREWS (editorNotes, heading "' . $FREW_HEAD . '", position bottom)' . PHP_EOL;
echo '   on Thomas Frew Jr. (new): ' . ($frewJrNeedsNote ? 'ADD' : 'already there') . PHP_EOL . '      ' . $NOTE_JR['note'] . PHP_EOL;
echo "   on #$TOMFREW Tom Frew: " . ($tfHas ? 'already there' : 'ADD (existing rows kept; ' . count($cleanNotes($tf?->editorNotes)) . ' non-empty now)') . PHP_EOL . '      ' . $NOTE_TOM['note'] . PHP_EOL;
echo "   #$TOMFREW is related to: " . implode(', ', array_map(fn($id) => "#$id " . $get($id)->title, $tfCited)) . PHP_EOL;
echo '   NOTE: "71. Requiem" (#2167), related to #18783, names "Tom Frew II", who bought the Spruce Street blacksmith shop in 1900: neither the 1990s Tom Frew nor, on this evidence, the trustee. Not changed here.' . PHP_EOL;

/* (5) the name-variant check (derive_board_holdings.php) */
$NICK = ['bob' => 'robert', 'rob' => 'robert', 'bobby' => 'robert', 'bill' => 'william', 'will' => 'william', 'billy' => 'william', 'chris' => 'christopher', 'dave' => 'david', 'mike' => 'michael', 'micheal' => 'michael',
    'kathy' => 'katherine', 'kathye' => 'katherine', 'kate' => 'katherine', 'katie' => 'katherine', 'kathryn' => 'katherine', 'catherine' => 'katherine', 'sue' => 'susan', 'suzan' => 'susan', 'suzanne' => 'susan', 'shelley' => 'rochelle', 'shelly' => 'rochelle',
    'ken' => 'kenneth', 'kenny' => 'kenneth', 'dan' => 'daniel', 'danny' => 'daniel', 'ed' => 'edward', 'eddie' => 'edward', 'tom' => 'thomas', 'steve' => 'steven', 'stephen' => 'steven', 'jim' => 'james', 'jimmy' => 'james',
    'joe' => 'joseph', 'jon' => 'jonathan', 'greg' => 'gregory', 'vic' => 'victor', 'liz' => 'elizabeth', 'beth' => 'elizabeth', 'pat' => 'patricia', 'patti' => 'patricia', 'phil' => 'philip', 'phillip' => 'philip',
    'larry' => 'lawrence', 'rose' => 'rosemarie', 'rosemary' => 'rosemarie', 'judy' => 'judith', 'cassie' => 'cassandra', 'matt' => 'matthew', 'denis' => 'dennis', 'doug' => 'douglas', 'rick' => 'richard', 'dick' => 'richard',
    'jeff' => 'jeffrey', 'tim' => 'timothy', 'andy' => 'andrew', 'drew' => 'andrew', 'gary' => 'gary', 'lori' => 'lorraine', 'les' => 'lester', 'bj' => 'bj', 'rj' => 'rj',
    'sandie' => 'sandra', 'sandy' => 'sandra', 'pete' => 'peter', 'walt' => 'walter', 'chet' => 'chester', 'charlie' => 'charles', 'chuck' => 'charles', 'gerry' => 'gerald', 'jerry' => 'gerald', 'lou' => 'louis', 'ernie' => 'ernest',
    'howie' => 'howard', 'millie' => 'mildred', 'ted' => 'edward', 'em' => 'emmett', 'davey' => 'david', 'sam' => 'samuel', 'al' => 'albert'];
$parts = function (string $name) use ($norm, $NICK) {
    $n = preg_replace(['~^Dr\.?\s+~', '~\.(?=[A-Za-z])~', '~,?\s*\b(Jr|Sr|II|III|IV|P\.E|CPA|PhD|Dr)\b\.?~i', '~[“"]([^”"]+)[”"]~u'], ['', '. ', '', ' $1 '], $name);
    $t = array_values(array_filter(preg_split('~\s+~', trim($n)), fn($w) => $w !== ''));
    if (count($t) < 2) { return null; }
    $li = count($t) - 1; while ($li > 1 && preg_match('~^(de|la|del|van|von)$~i', $t[$li - 1])) { $li--; }
    $last = str_replace(' ', '', $norm(implode(' ', array_slice($t, $li))));
    $firsts = array_map(fn($w) => $norm(rtrim($w, '.')), array_slice($t, 0, $li));
    $full = array_values(array_filter($firsts, fn($w) => strlen($w) > 1));
    $f = $full[0] ?? ($firsts[0] ?? '');
    $sfx = preg_match('~\b(Sr|II|III|IV)\b~', $name, $sm) ? $sm[1] : '';
    return ['last' => $last, 'lasts' => array_unique(array_merge([$last], explode('-', $last))), 'first' => $NICK[$f] ?? $f, 'init' => substr($f, 0, 1), 'allFirst' => array_map(fn($w) => $NICK[$w] ?? $w, $full), 'flat' => $f . ' ' . $last, 'sfx' => $sfx];
};
$verdict = function ($a, $b) {
    if (!$a || !$b) { return null; }
    if (!array_intersect($a['lasts'], $b['lasts'])) {
        foreach ($a['lasts'] as $l1) { foreach ($b['lasts'] as $l2) { if ($a['first'] === $b['first'] && strlen($a['first']) > 1 && levenshtein($l1, $l2) <= 3) { return 'same given name, surnames close (a married name or a misprint?)'; } } }
        return null;
    }
    $sx = ($a['sfx'] !== $b['sfx']) ? ' (suffixes differ: "' . ($a['sfx'] ?: 'none') . '" and "' . ($b['sfx'] ?: 'none') . '", kept apart)' : '';
    if ($a['first'] === $b['first']) { return 'same first name (nicknames folded) and surname' . $sx; }
    if (array_intersect($a['allFirst'], $b['allFirst'])) { return 'a shared given name and surname' . $sx; }
    if (levenshtein($a['first'], $b['first']) <= 1 && strlen($a['first']) > 3) { return 'given names one letter apart, same surname' . $sx; }
    if (strlen($a['first']) === 1 || strlen($b['first']) === 1) { return $a['init'] === $b['init'] ? 'an initial matching a given name, same surname' . $sx : null; }
    return null;
};
$DECIDED = ["Thomas Frew Jr.|$TOMFREW" => 'decided: kept apart (Nathan, 3 October 2026), a note on each'];
echo PHP_EOL . '(5) NAME VARIANTS: each new name against every existing person and every other new name' . PHP_EOL;
$flags = [];
foreach ($P as $mk => $np) {
    $mine = array_filter(array_map($parts, array_merge([$np['title']], $np['aliases'])));
    $scores = [];
    foreach ($allPeople as $id => $ap) {
        if (($pid[$mk] ?? null) === $id) { continue; }
        $best = 99; $why = null;
        foreach ($ap['names'] as $n) { $o = $parts($n); if (!$o) { continue; } foreach ($mine as $m) { $best = min($best, levenshtein($m['flat'], $o['flat'])); $why ??= $verdict($m, $o); } }
        $scores[] = [$best, '#' . $id . ' ' . $ap['title']];
        if ($why) { $flags[] = "{$np['title']} (new) and #$id {$ap['title']}: $why" . (isset($DECIDED["{$np['title']}|$id"]) ? '. ' . $DECIDED["{$np['title']}|$id"] : ''); }
    }
    usort($scores, fn($a, $b) => $a[0] <=> $b[0]);
    $others = [];
    foreach ($P as $k2 => $np2) { if ($k2 === $mk) { continue; }
        $best = 99; $why = null;
        foreach (array_filter(array_map($parts, array_merge([$np2['title']], $np2['aliases']))) as $o) { foreach ($mine as $m) { $best = min($best, levenshtein($m['flat'], $o['flat'])); $why ??= $verdict($m, $o); } }
        $others[] = [$best, $np2['title']];
        if ($why && $mk < $k2) { $flags[] = "{$np['title']} (new) and {$np2['title']} (new): $why"; }
    }
    usort($others, fn($a, $b) => $a[0] <=> $b[0]);
    echo '   ' . str_pad($np['title'], 24) . 'closest existing: ' . implode('; ', array_map(fn($s) => "{$s[1]} ({$s[0]})", array_slice($scores, 0, 2))) . ' | closest new: ' . implode('; ', array_map(fn($s) => "{$s[1]} ({$s[0]})", array_slice($others, 0, 2))) . PHP_EOL;
}
echo '   FLAGGED, might be one person (nothing merged): ' . ($flags ? PHP_EOL . '      ' . implode(PHP_EOL . '      ', array_unique($flags)) : 'none') . PHP_EOL;

echo PHP_EOL . 'SUMMARY: ' . count($newP) . ' people, ' . count($plan) . ' holdings, ' . count($links) . ' candidacy link, ' . (($frewJrNeedsNote ? 1 : 0) + ($tfHas ? 0 : 1)) . ' editor\'s notes; ' . $students . ' student members not created. A second run writes nothing.' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', array_unique($bad)) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

/* ------------------------------------------------ apply, in one transaction */
$tx = Craft::$app->getDb()->beginTransaction();
try {
    $pSec = $svc->getSectionByHandle('persons'); $pType = $svc->getEntryTypeByHandle('person');
    $ids = $pid;
    foreach ($newP as $mk => $np) {
        $p = new Entry(); $p->sectionId = $pSec->id; $p->setTypeId($pType->id); $p->title = $np['title'];
        $p->setFieldValues(['fullName' => $np['title'], 'personAliases' => implode("\n", $np['aliases']), 'roles' => [$RS], 'recordProvenance' => $pProv] + ($np['era'] ? ['historicalEra' => [$np['era']]] : []));
        if (!$elements->saveElement($p)) { throw new \RuntimeException("person {$np['title']}: " . json_encode($p->getFirstErrors())); }
        $ids[$mk] = $p->id;
    }
    $os = $svc->getSectionByHandle('officeHoldings'); $ot = $svc->getEntryTypeByHandle('officeHolding');
    foreach ($plan as $t) {
        $h = new Entry(); $h->sectionId = $os->id; $h->setTypeId($ot->id);
        $v = ['holdingPerson' => [$ids[$t['mk']]], 'holdingOffice' => [$RS], 'holdingBody' => [$HART],
            'termStart' => $t['sp'], 'termStartEdtf' => $t['se'], 'howEnded' => $t['how'], 'startEvidence' => $t['sev'], 'footnotes' => $fn($t['notes']), 'recordProvenance' => $hProv];
        if ($t['sel']) { $v['selectionMethod'] = $t['sel']; }
        if ($t['ee'] !== null) { $v['termEnd'] = $t['ep']; $v['termEndEdtf'] = $t['ee']; $v['endEvidence'] = $t['eev']; }
        $h->setFieldValues($v);
        if (!$elements->saveElement($h)) { throw new \RuntimeException('holding ' . $P[$t['mk']]['title'] . ' ' . $t['se'] . ': ' . json_encode($h->getFirstErrors())); }
    }
    foreach ($links as [$cid, $mk]) {
        $c = $get($cid); if ($c->candidacyPerson->status(null)->ids()) { continue; }
        $c->setFieldValue('candidacyPerson', [$ids[$mk]]);
        if (!$elements->saveElement($c)) { throw new \RuntimeException("candidacy #$cid"); }
    }
    foreach ([[$ids['frew|T'], $NOTE_JR], [$TOMFREW, $NOTE_TOM]] as [$id, $NOTE]) {
        $e = $get($id); $rows = $cleanNotes($e->editorNotes);
        if (array_filter($rows, fn($r) => $r['heading'] === $FREW_HEAD)) { continue; }
        $e->setFieldValue('editorNotes', array_merge($rows, [$NOTE]));
        if (!$elements->saveElement($e)) { throw new \RuntimeException("editor's note #$id: " . json_encode($e->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $e) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $e->getMessage() . PHP_EOL; throw $e; }

/* Read back. */
$short = [];
foreach ($P as $mk => $np) { $p = $get($ids[$mk] ?? 0); if (!$p || $p->title !== $np['title'] || !in_array($RS, $p->roles->ids(), true)) { $short[] = "person {$np['title']}"; } }
foreach ($TEN as $t) {
    $h = Entry::find()->section('officeHoldings')->status(null)->relatedTo(['and', ['targetElement' => $ids[$t['mk']], 'field' => 'holdingPerson'], ['targetElement' => $HART, 'field' => 'holdingBody']])->termStartEdtf($t['se'])->one();
    if (!$h || (string)$h->termEndEdtf !== (string)($t['ee'] ?? '') || (string)$h->howEnded->value !== $t['how'] || count($h->footnotes ?? []) !== count($t['notes'])) { $short[] = 'holding ' . $P[$t['mk']]['title'] . ' ' . $t['se']; }
}
if (($get($DINS_CAND)->candidacyPerson->status(null)->ids()[0] ?? null) !== $ids['dinsenbacher|W']) { $short[] = "link #$DINS_CAND"; }
foreach ([$ids['frew|T'], $TOMFREW] as $id) { if (!array_filter($cleanNotes($get($id)->editorNotes), fn($r) => $r['heading'] === $FREW_HEAD)) { $short[] = "editor's note #$id"; } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($P) . ' people, ' . count($TEN) . ' holdings, the link and both notes') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('create_hart_early_members.php', count($newP) + count($plan) + count($links) + 2, $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'Hart trustees from Leon Worden\'s roster: the pre-1995 members, Dinsenbacher, Fall, Hall; the two Frews noted');
if ($short) { throw new \RuntimeException('create_hart_early_members: ' . implode('; ', $short)); }
