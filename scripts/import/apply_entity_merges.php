/**
 * Reads web/review/entity-merges.json from the reconciliation screen and writes
 * a canonical name table to inventory/legacy/entity-canon.json.
 *
 * A Spouse of decision is written to the same file under `spouses` rather than
 * as a merge. "Mrs. George LeBrun" beside "George LeBrun" is a married woman
 * named by her husband's name, standard in nineteenth century sources: two
 * people, not one, and merging them erases her from the archive. Both names
 * stay distinct in the canon and the pair is recorded, so the relation screen
 * creates a record for each and apply_relations.php sets spouseOf on both.
 *
 * It does not touch Craft. Almost nothing has been promoted to a record yet, so
 * there is nothing to merge there; what the archive needs first is a decision
 * about which names are the same thing. The canon is that decision, and it lives
 * in the repository beside the extraction it reconciles.
 *
 * Merges are transitive: "Dr. Bard" = "Cephas R. Bard" and "Cephas R. Bard" =
 * "Dr. Cephas R. Bard" makes one person with three names. The survivor of the
 * group is the one the reviewer kept; when a group has been given more than one
 * survivor the conflict is reported and the group is left out.
 *
 * Dry run by default. Set $APPLY = true to write the canon file.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_entity_merges.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$root = \Craft::getAlias('@root');
$file = \Craft::getAlias('@webroot') . '/review/entity-merges.json';
$canonPath = $root . '/inventory/legacy/entity-canon.json';

if (!file_exists($file)) {
    echo 'ERROR: web/review/entity-merges.json not found. Download it from the review screen first.' . PHP_EOL;
    return;
}
$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) { echo 'ERROR: entity-merges.json is not valid JSON' . PHP_EOL; return; }

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'decisions in the file: ' . count($data) . PHP_EOL;

/* ---- union find over the merged pairs, per kind ---- */

$parent = [];
$find = function (string $x) use (&$parent, &$find): string {
    if (!isset($parent[$x])) { $parent[$x] = $x; return $x; }
    while ($parent[$x] !== $x) { $parent[$x] = $parent[$parent[$x]]; $x = $parent[$x]; }
    return $x;
};
$union = function (string $a, string $b) use (&$parent, $find) {
    $ra = $find($a); $rb = $find($b);
    if ($ra !== $rb) { $parent[$ra] = $rb; }
};

$survivorOf = [];   /* name key => the name the reviewer kept */
$apart = [];        /* pairs explicitly kept apart */
$spouses = [];      /* kind => [ ['wife'=>, 'husband'=>] ], two people, married */
$merges = 0; $noSurvivor = 0; $bad = [];

foreach ($data as $d) {
    $kind = (string)($d['kind'] ?? '');
    $a = trim((string)($d['a'] ?? ''));
    $b = trim((string)($d['b'] ?? ''));
    $choice = (string)($d['choice'] ?? '');
    if ($kind === '' || $a === '' || $b === '') { $bad[] = json_encode($d); continue; }

    $ka = $kind . '|' . $a; $kb = $kind . '|' . $b;

    if ($choice === 'diff') { $apart[] = [$kind, $a, $b]; continue; }

    /* "Mrs. George LeBrun" beside "George LeBrun" is two people, married, and
       merging them would erase her. This records the marriage instead: both
       names stay distinct in the canon, and the relation screen creates two
       records and sets spouseOf on each. The union-find below is never called,
       so neither name can be folded into the other later by another decision. */
    if ($choice === 'spouse') {
        $wife = trim((string)($d['wife'] ?? ''));
        $husband = trim((string)($d['husband'] ?? ''));
        if ($wife === '' || $husband === '') { $wife = $a; $husband = $b; }
        $spouses[$kind][] = ['wife' => $wife, 'husband' => $husband];
        $apart[] = [$kind, $a, $b];
        continue;
    }

    if ($choice !== 'same') { $bad[] = 'unknown choice "' . $choice . '" for ' . $a . ' / ' . $b; continue; }

    $s = trim((string)($d['survivor'] ?? ''));
    if ($s === '') { $noSurvivor++; continue; }

    $union($ka, $kb);
    $merges++;
    $survivorOf[$kind . '|' . $s] = true;
}

/* ---- groups ---- */

$groups = [];
foreach (array_keys($parent) as $k) { $groups[$find($k)][] = $k; }

$canon = [];        /* kind => [ ['canonical'=>, 'variants'=>[]] ] */
$conflicts = [];

foreach ($groups as $root_ => $members) {
    if (count($members) < 2) { continue; }
    $kind = explode('|', $members[0], 2)[0];
    $names = array_map(fn($m) => explode('|', $m, 2)[1], $members);
    sort($names);

    $kept = array_values(array_filter($names, fn($n) => isset($survivorOf[$kind . '|' . $n])));
    if (count($kept) > 1) {
        $conflicts[] = $kind . ': ' . implode(' / ', $names) . '  survivors chosen: ' . implode(', ', $kept);
        continue;
    }
    $canonical = $kept[0] ?? null;
    if ($canonical === null) {
        /* every member was only ever the losing side; take the longest name */
        usort($names, fn($x, $y) => mb_strlen($y) <=> mb_strlen($x));
        $canonical = $names[0];
    }
    $canon[$kind][] = [
        'canonical' => $canonical,
        'variants' => array_values(array_filter($names, fn($n) => $n !== $canonical)),
    ];
}

foreach ($canon as $kind => &$list) {
    usort($list, fn($a, $b) => strcmp($a['canonical'], $b['canonical']));
}
unset($list);

/* ---- report ---- */

echo '=== canon ===' . PHP_EOL;
$totalNames = 0; $totalPeople = 0;
foreach ($canon as $kind => $list) {
    $names = 0;
    foreach ($list as $g) { $names += 1 + count($g['variants']); }
    $totalNames += $names; $totalPeople += count($list);
    echo str_pad($kind, 16) . str_pad((string)$names, 6) . 'names collapse to ' . count($list) . PHP_EOL;
    foreach (array_slice($list, 0, 8) as $g) {
        echo '  ' . str_pad($g['canonical'], 34) . '<- ' . implode(', ', $g['variants']) . PHP_EOL;
    }
    if (count($list) > 8) { echo '  ... and ' . (count($list) - 8) . ' more groups' . PHP_EOL; }
}

echo '=== summary ===' . PHP_EOL;
echo 'merges accepted:       ' . $merges . PHP_EOL;
echo 'pairs kept apart:      ' . count($apart) . PHP_EOL;
echo 'merges with no survivor chosen, ignored: ' . $noSurvivor . PHP_EOL;

if ($spouses) {
    echo PHP_EOL . '=== married, not merged ===' . PHP_EOL;
    foreach ($spouses as $kind => $list) {
        foreach ($list as $pair) {
            echo '  ' . str_pad($pair['wife'], 34) . 'wife of  ' . $pair['husband'] . PHP_EOL;
        }
    }
    echo 'Both names stay in the canon. The relation screen creates a record for each' . PHP_EOL;
    echo 'and apply_relations.php sets spouseOf on both.' . PHP_EOL;
}
echo 'names in the canon:    ' . $totalNames . PHP_EOL;
echo 'they collapse to:      ' . $totalPeople . PHP_EOL;
if ($conflicts) {
    echo 'groups given more than one survivor, left out:' . PHP_EOL;
    foreach ($conflicts as $c) { echo '  ' . $c . PHP_EOL; }
}
if ($bad) {
    echo 'rows that could not be read:' . PHP_EOL;
    foreach (array_slice($bad, 0, 10) as $b) { echo '  ' . $b . PHP_EOL; }
}

$out = [
    'meta' => [
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/apply_entity_merges.php',
        'source' => 'web/review/entity-merges.json',
        'names' => $totalNames,
        'entities' => $totalPeople,
        'note' => 'Canonical names for the legacy extraction. export_relation_candidates.php reads this to collapse variants into one candidate per entity per article.',
    ],
    'canon' => $canon,
    'spouses' => $spouses,
    'keptApart' => array_map(fn($p) => ['kind' => $p[0], 'a' => $p[1], 'b' => $p[2]], $apart),
];

if ($APPLY) {
    file_put_contents($canonPath, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
    echo 'wrote inventory/legacy/entity-canon.json' . PHP_EOL;
} else {
    echo 'would write inventory/legacy/entity-canon.json' . PHP_EOL;
}
echo 'Craft is not touched. The canon is a file in the repository, and the relations export reads it.' . PHP_EOL;
