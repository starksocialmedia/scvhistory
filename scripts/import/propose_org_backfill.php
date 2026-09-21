/**
 * Proposes identifiers, types and parents for the organization records the
 * archive already holds, by matching them against the three authority tables.
 *
 * Read only. It writes nothing and has no $APPLY, because what it produces is a
 * proposal for a person to read, not a change waiting on a flag. Anything
 * accepted here is applied by whatever script is written for the accepted set.
 *
 * WHAT IT PROPOSES
 *
 *   orgType, schoolLevel   from the authority's own classification
 *   cdsCode, ncesId, ein   the identifier, in the field that names its scheme
 *   wikidataId             where Wikidata holds the body
 *   parentOrganization     for a school, its district, and whether the archive
 *                          already holds that district as a record
 *
 * HOW IT REFUSES
 *
 * The same blunt scoring as the review screen: identical, identical without a
 * legal suffix, every word in order, every word, or a prefix. A proposal below
 * that is not printed at all. These are records that already exist and that
 * somebody wrote by hand, and a wrong identifier attached to one of them is
 * worse than an empty field: the field claims the record has been checked.
 *
 * A proposal where several candidates tie is printed as a question rather than
 * an answer, with all of them listed.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/propose_org_backfill.php'))"
 */

$ROOT = \Craft::getAlias('@root');
$AUTH = $ROOT . '/inventory/legacy/authorities';

$LEGAL = '/\b(the|inc|incorporated|corp|corporation|company|co|llc|ltd|limited|lp|foundation|assn|association)\b/';
$norm = function (string $s): string {
    $s = mb_strtolower(trim($s));
    $s = str_replace('&', ' and ', $s);
    $s = preg_replace('~[^a-z0-9 ]+~', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};
$bare = function (string $s) use ($norm, $LEGAL): string {
    return trim(preg_replace('~\s+~', ' ', preg_replace($LEGAL, ' ', $norm($s))));
};
$words = fn(string $s) => preg_split('~\s+~', trim($s), -1, PREG_SPLIT_NO_EMPTY) ?: [];

$score = function (string $a, string $b) use ($norm, $bare, $words): array {
    $an = $norm($a); $bn = $norm($b);
    if ($an === '' || $bn === '') { return [0, '']; }
    if ($an === $bn) { return [100, 'identical']; }
    $ab = $bare($a); $bb = $bare($b);
    if ($ab !== '' && $ab === $bb) { return [92, 'identical without the legal suffix']; }
    $aw = $words($an); $bw = $words($bn);
    if ($aw && count($aw) <= count($bw)) {
        $i = 0;
        foreach ($bw as $w) { if ($i < count($aw) && $w === $aw[$i]) { $i++; } }
        if ($i === count($aw)) {
            return array_slice($bw, 0, count($aw)) === $aw
                ? [70, 'the authority name begins with ours']
                : [85, 'every word appears, in order'];
        }
        if (!array_diff($aw, $bw)) { return [78, 'every word appears']; }
    }
    return [0, ''];
};

$load = function (string $f) use ($AUTH) {
    $p = $AUTH . '/' . $f;
    return file_exists($p) ? (json_decode(file_get_contents($p), true) ?: []) : null;
};
$schools = $load('schools-ca.json');
$irs     = $load('nonprofits-scv.json');
$wd      = $load('orgs-wikidata.json');

echo 'tables: NCES ' . ($schools ? count($schools['schools'] ?? []) : 0)
   . ' schools and ' . ($schools ? count($schools['districts'] ?? []) : 0) . ' districts, '
   . 'IRS ' . ($irs ? count($irs['organizations'] ?? []) : 0) . ', '
   . 'Wikidata ' . ($wd ? count($wd['organizations'] ?? []) : 0) . PHP_EOL;

$hasField = function ($el, string $h): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

/* Districts the archive already holds, so a proposed parent can say whether it
   points at a record or at a name we would have to create. */
$heldByName = [];
foreach (\craft\elements\Entry::find()->section('organizations')->status(null)->limit(null)->all() as $e) {
    $heldByName[$norm((string)$e->title)] = $e;
}

$orgs = \craft\elements\Entry::find()->section('organizations')->status(null)
    ->orderBy('title asc')->limit(null)->all();

echo 'organization records: ' . count($orgs) . PHP_EOL . PHP_EOL;

$proposed = 0; $none = []; $ambiguous = 0;

foreach ($orgs as $e) {
    $title = (string)$e->title;
    $cands = [];

    foreach (($schools['schools'] ?? []) as $s) {
        [$sc, $why] = $score($title, $s['name']);
        if ($sc >= 70) {
            $cands[] = ['source' => 'NCES', 'score' => $sc, 'why' => $why, 'name' => $s['name'],
                /* NCES carries a state id that is sometimes all zeros or a stub. A
                   code that is not a code is worse than none: it reads as
                   checked. */
                'fields' => array_filter([
                    'ncesId' => $s['ncesId'],
                    'cdsCode' => (preg_match('~^0+$~', (string)$s['stateId']) || strlen((string)$s['stateId']) < 7)
                        ? '' : $s['stateId'],
                ]),
                'orgType' => 'school', 'schoolLevel' => $s['level'] ?? '',
                'parent' => $s['district'] ?? '',
                'extra' => trim(($s['status'] ?? '') . ($s['closed'] ? ' in ' . $s['closed'] : ''))];
        }
    }
    foreach (($schools['districts'] ?? []) as $d) {
        [$sc, $why] = $score($title, $d['name']);
        if ($sc >= 70) {
            $cands[] = ['source' => 'NCES', 'score' => $sc, 'why' => $why, 'name' => $d['name'],
                'fields' => array_filter([
                    'ncesId' => $d['leaid'],
                    'cdsCode' => (preg_match('~^0+$~', (string)($d['stateId'] ?? '')) || strlen((string)($d['stateId'] ?? '')) < 7)
                        ? '' : $d['stateId'],
                ]),
                'orgType' => 'school', 'schoolLevel' => 'district', 'parent' => '', 'extra' => 'district'];
        }
    }
    foreach (($irs['organizations'] ?? []) as $o) {
        [$sc, $why] = $score($title, $o['name']);
        if ($sc >= 70) {
            $cands[] = ['source' => 'IRS', 'score' => $sc, 'why' => $why, 'name' => $o['name'],
                'fields' => array_filter(['ein' => $o['ein']]),
                'orgType' => $o['orgType'] ?: 'nonprofit', 'schoolLevel' => '', 'parent' => '',
                'extra' => $o['subsectionLabel'] ?? ''];
        }
    }
    foreach (($wd['organizations'] ?? []) as $o) {
        [$sc, $why] = $score($title, $o['name']);
        if ($sc >= 70) {
            $desc = (string)($o['description'] ?? '');
            $local = (bool)preg_match('~\b(california|santa clarita|newhall|valencia|los angeles)\b~i', $desc);
            if (($o['foundBy'] ?? []) === ['name'] && !$local) {
                $sc = (int)floor($sc / 2);
                $why .= ', but nothing places it here';
            }
            $cands[] = ['source' => 'Wikidata', 'score' => $sc, 'why' => $why, 'name' => $o['name'],
                'fields' => array_filter(['wikidataId' => $o['qid'], 'ein' => $o['ein'] ?? '',
                                          'ncesId' => $o['ncesId'] ?? '']),
                'orgType' => '', 'schoolLevel' => '', 'parent' => '', 'extra' => $desc];
        }
    }

    if (!$cands) { $none[] = $title; continue; }

    usort($cands, fn($a, $b) => $b['score'] <=> $a['score']);
    $top = $cands[0];
    $ties = array_values(array_filter($cands, fn($c) => $c['score'] === $top['score']));

    $proposed++;
    printf("%-44s #%d\n", mb_substr($title, 0, 43), $e->id);

    foreach (array_slice($cands, 0, 3) as $i => $c) {
        $mark = $i === 0 ? '  ->' : '    ';
        printf("%s %-9s %-40s %3d%%  %s\n", $mark, $c['source'], mb_substr($c['name'], 0, 40),
            $c['score'], $c['why']);
        if ($i === 0) {
            $sets = [];
            foreach ($c['fields'] as $k => $v) {
                $have = $hasField($e, $k) ? trim((string)$e->getFieldValue($k)) : null;
                $sets[] = $k . '=' . $v . ($have === null ? ' (no such field yet)' : ($have !== '' ? ' (HELD: ' . $have . ')' : ''));
            }
            if ($c['orgType']) { $sets[] = 'orgType=' . $c['orgType']; }
            if ($c['schoolLevel']) { $sets[] = 'schoolLevel=' . $c['schoolLevel']; }
            if ($sets) { echo '       ' . implode('  ', $sets) . PHP_EOL; }
            if ($c['parent'] !== '') {
                $held = $heldByName[$norm($c['parent'])] ?? null;
                echo '       parentOrganization=' . $c['parent']
                   . ($held ? '  (held, #' . $held->id . ')' : '  (NOT held; the district would have to be created)') . PHP_EOL;
            }
            if ($c['extra']) { echo '       ' . mb_substr($c['extra'], 0, 84) . PHP_EOL; }
        }
    }
    if (count($ties) > 1) {
        $ambiguous++;
        echo '       ' . count($ties) . ' candidates tie. This is a question, not a proposal.' . PHP_EOL;
    }
    echo PHP_EOL;
}

echo str_repeat('-', 74) . PHP_EOL;
echo 'with a proposal: ' . $proposed . '   of which tied: ' . $ambiguous . PHP_EOL;
echo 'no authority match: ' . count($none) . PHP_EOL;
foreach ($none as $n) { echo '   ' . $n . PHP_EOL; }
echo PHP_EOL . 'Nothing was written. There is no $APPLY here on purpose.' . PHP_EOL;
