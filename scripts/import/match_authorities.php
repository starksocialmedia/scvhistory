/**
 * Matches every queue name against the authority tables and writes the best
 * candidate onto the row, so the review screen can show a name, an id and a
 * source beside the sentence the name came from.
 *
 * Read only as far as the database goes. It rewrites web/review/records*.json
 * in place, adding an "authority" block to each row and nothing else.
 *
 * WHAT IT MATCHES AGAINST
 *
 *   schools-ca.json      NCES public schools and districts, Los Angeles County
 *   nonprofits-scv.json  IRS exempt organizations in the valley
 *   orgs-wikidata.json   Wikidata organizations, by place and by corpus name
 *   gnis-classes.json    the USGS gazetteer, for places
 *
 * SCORING, AND WHY IT REFUSES MORE THAN IT ACCEPTS
 *
 * A match is only worth showing if a reviewer would agree with it at a glance.
 * The score is deliberately blunt:
 *
 *   100  the normalised names are identical
 *    92  identical once a legal suffix is dropped: Company, Inc, Corporation
 *    85  every word of the queue name appears in the authority name, in order
 *    78  every word appears, in any order
 *    70  the authority name starts with the whole queue name
 *     0  anything else
 *
 * There is no fuzzy distance and no partial credit. "Newhall Hardware" against
 * "Newhall Land and Farming" would score on a token overlap and be wrong, and a
 * card showing a confident wrong answer is worse than a card showing none: the
 * reviewer stops reading and starts confirming.
 *
 * THE GENERIC NAME PROBLEM
 *
 * The Wikidata table was built partly by matching the corpus's own names, so it
 * contains a Canadian planning commission, three Southern Hotels and a band
 * called The Motors. A bare "Planning Commission" therefore scores 100 against
 * something in Ontario. Where a candidate has a location and the location is
 * not California, the score is halved and the reason says so. Where several
 * candidates tie, the row carries all of them rather than picking, because
 * picking is the reviewer's job and the tie is the useful information.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/match_authorities.php'))"
 *      ddev craft exec "$MATCH_SUFFIX='-full'; eval(file_get_contents('scripts/import/match_authorities.php'))"
 */

$ROOT   = \Craft::getAlias('@root');
$REVIEW = \Craft::getAlias('@webroot') . '/review';
$SUFFIX = $MATCH_SUFFIX ?? '';
$AUTH   = $ROOT . '/inventory/legacy/authorities';

$queuePath = $REVIEW . '/records' . $SUFFIX . '.json';
if (!file_exists($queuePath)) { echo 'no ' . $queuePath . PHP_EOL; return; }

$LEGAL = '/\b(the|inc|incorporated|corp|corporation|company|co|llc|ltd|limited|lp|foundation|assn|association)\b/';

$norm = function (string $s) use ($LEGAL): string {
    $s = mb_strtolower(trim($s));
    $s = str_replace(['&'], [' and '], $s);
    $s = preg_replace('~[^a-z0-9 ]+~', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};
$bare = function (string $s) use ($norm, $LEGAL): string {
    $s = preg_replace($LEGAL, ' ', $norm($s));
    return trim(preg_replace('~\s+~', ' ', $s));
};
$words = fn(string $s) => preg_split('~\s+~', trim($s), -1, PREG_SPLIT_NO_EMPTY) ?: [];

/* ----------------------------------------------------------- the tables */

$load = function (string $file) use ($AUTH) {
    $p = $AUTH . '/' . $file;
    return file_exists($p) ? (json_decode(file_get_contents($p), true) ?: []) : null;
};

$schools   = $load('schools-ca.json');
$nonprofit = $load('nonprofits-scv.json');
$wikidata  = $load('orgs-wikidata.json');
$gnisDoc   = file_exists($ROOT . '/inventory/legacy/gnis-classes.json')
    ? json_decode(file_get_contents($ROOT . '/inventory/legacy/gnis-classes.json'), true) : null;

$candidates = [];   /* normalised name => list of candidates */
$add = function (string $name, array $cand) use (&$candidates, $norm, $bare) {
    $n = $norm($name);
    if ($n === '') { return; }
    $candidates[$n][] = $cand;
    $b = $bare($name);
    if ($b !== '' && $b !== $n) { $candidates[$b][] = $cand + ['viaBare' => true]; }
};

if ($schools) {
    foreach (($schools['schools'] ?? []) as $s) {
        $add($s['name'], ['source' => 'NCES', 'id' => $s['ncesId'], 'idField' => 'ncesId',
            'name' => $s['name'], 'orgType' => 'school', 'schoolLevel' => $s['level'] ?? '',
            'parent' => $s['district'] ?? '', 'stateId' => $s['stateId'] ?? '',
            'status' => $s['status'] ?? '', 'opened' => $s['opened'] ?? '', 'closed' => $s['closed'] ?? '',
            'state' => 'CA']);
    }
    foreach (($schools['districts'] ?? []) as $d) {
        $add($d['name'], ['source' => 'NCES', 'id' => $d['leaid'], 'idField' => 'ncesId',
            'name' => $d['name'], 'orgType' => 'school', 'schoolLevel' => 'district',
            'parent' => '', 'stateId' => $d['stateId'] ?? '', 'state' => 'CA']);
    }
}
if ($nonprofit) {
    foreach (($nonprofit['organizations'] ?? []) as $o) {
        $add($o['name'], ['source' => 'IRS', 'id' => $o['ein'], 'idField' => 'ein',
            'name' => $o['name'], 'orgType' => $o['orgType'] ?: 'nonprofit',
            'schoolLevel' => '', 'parent' => '', 'city' => $o['city'] ?? '', 'state' => 'CA',
            'detail' => $o['subsectionLabel'] ?? '']);
        if (!empty($o['sortName'])) { $add($o['sortName'], ['source' => 'IRS', 'id' => $o['ein'],
            'idField' => 'ein', 'name' => $o['name'], 'orgType' => $o['orgType'] ?: 'nonprofit',
            'schoolLevel' => '', 'parent' => '', 'state' => 'CA', 'detail' => 'matched on the sort name']); }
    }
}
if ($wikidata) {
    foreach (($wikidata['organizations'] ?? []) as $o) {
        /* Wikidata rows found only by name may be anywhere on earth. The
           description is the only locality signal the table carries. */
        $desc = (string)($o['description'] ?? '');
        $local = (bool)preg_match('~\b(california|santa clarita|newhall|valencia|saugus|castaic|acton|los angeles)\b~i', $desc);
        $foreign = !$local && (bool)preg_match('~\b(canada|ontario|england|london|australia|austria|germany|france|india|ireland|scotland|wales|winnipeg|toronto|vancouver)\b~i', $desc);
        $add($o['name'], ['source' => 'Wikidata', 'id' => $o['qid'], 'idField' => 'wikidataId',
            'name' => $o['name'], 'orgType' => '', 'schoolLevel' => '', 'parent' => '',
            'detail' => $desc, 'inception' => $o['inception'] ?? '',
            'wikipedia' => $o['wikipedia'] ?? '',
            'local' => $local, 'foreign' => $foreign,
            'foundBy' => $o['foundBy'] ?? [],
            'ein' => $o['ein'] ?? '', 'ncesId' => $o['ncesId'] ?? '', 'gnisId' => $o['gnisId'] ?? '']);
    }
}

/* The gazetteer's class, mapped to the archive's placeType, so an approval can
   set both the id and the kind from one match. */
$GNIS_TYPE = ['stream'=>'natural','valley'=>'natural','summit'=>'natural','spring'=>'natural',
  'lake'=>'natural','flat'=>'natural','ridge'=>'natural','cliff'=>'natural','gap'=>'natural',
  'basin'=>'natural','island'=>'natural','swamp'=>'natural','falls'=>'natural','bend'=>'natural',
  'bar'=>'natural','bay'=>'natural','beach'=>'natural','cape'=>'natural','arch'=>'natural',
  'channel'=>'natural','crater'=>'natural','glacier'=>'natural','gut'=>'natural','isthmus'=>'natural',
  'lava'=>'natural','pillar'=>'natural','plain'=>'natural','range'=>'natural','rapids'=>'natural',
  'sea'=>'natural','slope'=>'natural','woods'=>'natural','arroyo'=>'natural','bench'=>'natural',
  'canal'=>'site','census'=>'site','civil'=>'site','crossing'=>'site','levee'=>'site',
  'military'=>'site','populated place'=>'site','reservoir'=>'site','area'=>'site'];

/* The valley and the counties it touches. A gazetteer name like Grapevine
   Canyon occurs ten times across California, and nine of them are irrelevant. */
$NEAR_COUNTIES = ['los angeles' => 1, 'ventura' => 1, 'kern' => 1];

$gnis = [];
if ($gnisDoc) {
    foreach (($gnisDoc['classes'] ?? []) as $fid => $row) {
        $n = $norm((string)($row['name'] ?? ''));
        if ($n === '') { continue; }
        $gnis[$n][] = ['source' => 'GNIS', 'id' => (string)$fid, 'idField' => 'gnisId',
            'name' => $row['name'], 'class' => $row['class'], 'county' => $row['county'],
            'placeType' => $GNIS_TYPE[strtolower((string)$row['class'])] ?? ''];
    }
}

echo 'tables: '
   . 'NCES ' . ($schools ? count($schools['schools'] ?? []) : 'missing') . ', '
   . 'IRS ' . ($nonprofit ? count($nonprofit['organizations'] ?? []) : 'missing') . ', '
   . 'Wikidata ' . ($wikidata ? count($wikidata['organizations'] ?? []) : 'missing') . ', '
   . 'GNIS ' . ($gnis ? count($gnis) : 'missing') . PHP_EOL;

/* ------------------------------------------------------------ the scoring */

$score = function (string $queue, string $authority) use ($norm, $bare, $words): array {
    $qn = $norm($queue); $an = $norm($authority);
    if ($qn === '' || $an === '') { return [0, '']; }
    if ($qn === $an) { return [100, 'the names are identical']; }

    $qb = $bare($queue); $ab = $bare($authority);
    if ($qb !== '' && $qb === $ab) { return [92, 'identical once the legal suffix is dropped']; }

    $qw = $words($qn); $aw = $words($an);
    if (count($qw) && count($qw) <= count($aw)) {
        /* in order */
        $i = 0;
        foreach ($aw as $w) { if ($i < count($qw) && $w === $qw[$i]) { $i++; } }
        if ($i === count($qw)) {
            if (array_slice($aw, 0, count($qw)) === $qw) {
                return [70, 'the authority name begins with this name'];
            }
            return [85, 'every word appears, in order'];
        }
        if (!array_diff($qw, $aw)) { return [78, 'every word appears']; }
    }
    return [0, ''];
};

/* --------------------------------------------------------------- matching */

$doc = json_decode(file_get_contents($queuePath), true);
$rows = $doc['names'] ?? [];
$matched = 0; $bySource = []; $ambiguous = 0;

foreach ($rows as $i => $r) {
    $guess = (string)($r['guess'] ?? '');
    $pool = [];

    if ($guess === 'organization') {
        $keys = array_unique([$norm($r['name']), $bare($r['name'])]);
        foreach ($keys as $k) {
            foreach (($candidates[$k] ?? []) as $c) { $pool[] = $c; }
        }
        /* Also try the name as the authority might write it, with the words in
           order: "Newhall School" against "Newhall Elementary". */
        foreach ($candidates as $k => $list) {
            if (count($pool) > 40) { break; }
            [$sc, ] = $score($r['name'], $k);
            if ($sc >= 78) { foreach ($list as $c) { $pool[] = $c; } }
        }
    } elseif ($guess === 'place') {
        $k = $norm($r['name']);
        foreach (($gnis[$k] ?? []) as $c) { $pool[] = $c; }
    } else {
        continue;
    }

    if (!$pool) { continue; }

    $scored = [];
    foreach ($pool as $c) {
        [$sc, $why] = $score($r['name'], (string)$c['name']);
        if ($sc <= 0) { continue; }
        if (!empty($c['foreign'])) { $sc = (int)floor($sc / 2); $why .= ', but it is not in California'; }
        if (!empty($c['viaBare'])) { $why .= ' (on the shortened form)'; }

        /* A Wikidata row that entered the table only because its label matched
           a corpus name, and whose description says nothing about California,
           is a blind hit. "Planning Commission" is identical to a government
           agency somewhere and the identity is the whole of the evidence. */
        if ($c['source'] === 'Wikidata'
            && ($c['foundBy'] ?? []) === ['name']
            && empty($c['local'])) {
            $sc = (int)floor($sc / 2);
            $why .= ', but it is in the table only because the name matched and nothing places it here';
        }

        /* A gazetteer name repeats across the state. Grapevine Canyon is ten
           features in ten counties, and the archive means the one here. A
           feature in this valley's counties keeps its score; one elsewhere is
           halved, which breaks the tie without discarding the alternative. */
        if ($c['source'] === 'GNIS') {
            $cty = mb_strtolower((string)($c['county'] ?? ''));
            if (isset($NEAR_COUNTIES[$cty])) { $sc += 5; $why .= ', in ' . $c['county'] . ' County'; }
            else { $sc = (int)floor($sc / 2); $why .= ', but it is in ' . ($c['county'] ?: 'another') . ' County'; }
        }

        /* The IRS file is full of the bodies that orbit a school rather than
           the school: the booster club, the PTA, the scout pack that meets in
           its hall. "Castaic School" matched CASTAIC HIGH SCHOOL PACK on every
           word appearing, which is true and useless. */
        if ($c['source'] === 'IRS'
            && preg_match('~\b(pta|ptsa|ptso|booster|boosters|pack|troop|auxiliary|alumni|friends of|foundation|parent|parents|advisory|council|club)\b~i', (string)$c['name'])
            && !preg_match('~\b(pta|ptsa|booster|pack|troop|auxiliary|alumni|friends|foundation|parent|advisory|council|club)\b~i', $r['name'])) {
            $sc = (int)floor($sc / 3);
            $why .= ', but this is a support group rather than the body itself';
        }
        $key = $c['source'] . '|' . $c['id'];
        if (!isset($scored[$key]) || $scored[$key]['score'] < $sc) {
            $scored[$key] = $c + ['score' => $sc, 'why' => $why];
        }
    }
    if (!$scored) { continue; }

    usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
    $top = $scored[0];
    $ties = array_values(array_filter($scored, fn($x) => $x['score'] === $top['score']));

    $rows[$i]['authority'] = [
        'top' => $top,
        'alternates' => array_slice(array_values($scored), 1, 3),
        'tied' => count($ties) > 1 ? count($ties) : 0,
    ];
    $matched++;
    $bySource[$top['source']] = ($bySource[$top['source']] ?? 0) + 1;
    if (count($ties) > 1) { $ambiguous++; }
}

$doc['names'] = $rows;
$doc['meta']['authorityMatched'] = $matched;
$doc['meta']['authorityMatchedAt'] = (new DateTime())->format('c');
file_put_contents($queuePath, json_encode($doc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

$orgTotal = count(array_filter($rows, fn($r) => ($r['guess'] ?? '') === 'organization'));
$plTotal  = count(array_filter($rows, fn($r) => ($r['guess'] ?? '') === 'place'));
$undecided = count(array_filter($rows, fn($r) => empty($r['decidedAs']) && ($r['articleCount'] ?? 0) >= 2));

echo PHP_EOL . 'rows: ' . count($rows) . '  organizations: ' . $orgTotal . '  places: ' . $plTotal . PHP_EOL;
echo 'matched to an authority: ' . $matched . PHP_EOL;
foreach ($bySource as $s => $n) { echo '   ' . str_pad($s, 10) . $n . PHP_EOL; }
echo 'rows where the best score is tied between candidates: ' . $ambiguous . PHP_EOL;
echo 'undecided rows with 2+ articles: ' . $undecided . PHP_EOL;
echo 'wrote ' . $queuePath . PHP_EOL;
