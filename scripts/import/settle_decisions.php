/**
 * Applies Nathan's settlements to records-decided.json.
 *
 * Each of these is a judgement he made on a conflict the approval gate raised.
 * They are written here rather than clicked on the screen because they are
 * corrections to decisions already taken, and because a list of them in one
 * place is auditable in a way that eight clicks are not.
 *
 * Dry run by default. The decisions file is the record of judgement, so it
 * waits for a flag like anything else.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/settle_decisions.php'))"
 */

$APPLY = false;

$FILE = \Craft::getAlias('@webroot') . '/review/records-decided.json';
$doc = json_decode(file_get_contents($FILE), true) ?: [];
$rows = $doc['decisions'] ?? [];

$norm = fn(string $v) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $v)));

/* EVERY matching row, not the first.
 *
 * The decisions file can hold a name twice: the screen writes one row per name
 * and type, and a rename or an appended recovery can leave a second. Settling
 * only the first left a second "Cowboy Festival" still merging into a target
 * that had just been parked, which the gate then refused, correctly. */
$findAll = function (string $name) use (&$rows, $norm): array {
    $out = [];
    foreach ($rows as $i => $r) {
        if (($r['type'] ?? '') === 'pair') { continue; }
        if ($norm((string)($r['name'] ?? '')) === $norm($name)) { $out[] = $i; }
    }
    return $out;
};
$find = function (string $name) use ($findAll) {
    $all = $findAll($name);
    return $all ? $all[0] : null;
};

$log = [];

/* ------------------------------------------------------------ the five */

$MERGES = [
    ['Lyons',                    15651, 'Lyons Avenue',  'the canon makes it an alias of the avenue'],
    ['Lake Elizabeth',           15994, 'Elizabeth Lake', 'the canon makes it an alias of the lake'],
    ['Councilwoman Jill Klajic', 15874, 'Jill Klajic',   'the title is the alias, the name is the record'],
];
foreach ($MERGES as [$name, $into, $intoName, $why]) {
    $i = $find($name);
    if ($i === null) { $log[] = ['-', $name, 'not in the file']; continue; }
    $was = $rows[$i]['action'] ?? '?';
    $rows[$i]['action'] = 'merged';
    $rows[$i]['into'] = $into;
    $rows[$i]['intoName'] = $intoName;
    unset($rows[$i]['intoKey'], $rows[$i]['aliases']);
    $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': ' . $why;
    $log[] = [$was . ' -> merged', $name, 'into #' . $into . ' ' . $intoName];
}

/* Skipped: it is the parked event, and the screen makes people, places and
   organizations only. */
$i = $find('Cowboy Poetry Festival');
if ($i !== null) {
    $was = $rows[$i]['action'] ?? '?';
    $rows[$i]['action'] = 'skipped';
    unset($rows[$i]['aliases'], $rows[$i]['articles']);
    $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': the parked event, not a person';
    $log[] = [$was . ' -> skipped', 'Cowboy Poetry Festival', 'the canon parks the festival'];
}

/* Created under his own name, with the titled form as an alias and the QID. */
$i = $find('President Theodore Roosevelt');
if ($i !== null) {
    $rows[$i]['name'] = 'Theodore Roosevelt';
    $rows[$i]['type'] = 'person';
    $rows[$i]['aliases'] = array_values(array_unique(array_merge(
        (array)($rows[$i]['aliases'] ?? []), ['President Theodore Roosevelt'])));
    $rows[$i]['wikidataId'] = 'Q33866';
    $rows[$i]['authority'] = ['source' => 'Wikidata', 'id' => 'Q33866', 'idField' => 'wikidataId',
                              'name' => 'Theodore Roosevelt'];
    $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': untitled name, titled form as alias';
    $log[] = ['renamed', 'President Theodore Roosevelt', 'Theodore Roosevelt, alias kept, Q33866'];
}

/* -------------------------------------------------------- and the rest */

$i = $find('Hart Mansion');
if ($i !== null) {
    $was = ($rows[$i]['type'] ?? '?') . '/' . ($rows[$i]['placeType'] ?? '-');
    $rows[$i]['type'] = 'place';
    $rows[$i]['placeType'] = 'building';
    $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': it is a building';
    $log[] = [$was . ' -> place/building', 'Hart Mansion', ''];
}

/* The containment pair, answered: two different things. */
$pairDone = false;
foreach ($rows as $i => $r) {
    if (($r['type'] ?? '') !== 'pair') { continue; }
    $s = $norm((string)($r['short'] ?? '')); $l = $norm((string)($r['long'] ?? ''));
    if (($s === 'pico canyon' && $l === 'pico canyon road') || ($s === 'pico canyon road' && $l === 'pico canyon')) {
        $rows[$i]['action'] = 'diff';
        $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': the canyon and the road up it';
        $log[] = ['pair -> diff', 'Pico Canyon / Pico Canyon Road', 'different things'];
        $pairDone = true;
    }
}
if (!$pairDone) {
    /* No stored verdict exists, so record one rather than leaving the gate to
       raise it again. */
    $rows[] = ['name' => 'PAIR Pico Canyon / Pico Canyon Road', 'type' => 'pair', 'action' => 'diff',
               'short' => 'Pico Canyon', 'long' => 'Pico Canyon Road',
               'shortKey' => 'pico canyon', 'longKey' => 'pico canyon road', 'shape' => 'contains',
               'settledBy' => 'Nathan, ' . date('Y-m-d') . ': the canyon and the road up it'];
    $log[] = ['pair added', 'Pico Canyon / Pico Canyon Road', 'different things'];
}

/* The festival's own aliases. Skipping the festival left "Cowboy Festival" and
   "Cowboy Poetry" merging into a target that is no longer created, which is a
   decision pointing at nothing. They follow it into the parked event. */
foreach (['Cowboy Festival', 'Cowboy Poetry', 'Cowboy Poetry Festival'] as $n) {
    foreach ($findAll($n) as $i) {
        $was = $rows[$i]['action'] ?? '?';
        if ($was === 'skipped') { continue; }
        $rows[$i]['action'] = 'skipped';
        unset($rows[$i]['intoKey'], $rows[$i]['intoName'], $rows[$i]['into']);
        $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': follows the parked festival';
        $log[] = [$was . ' -> skipped', $n, 'its target is the parked event'];
    }
}

/* Renaming President Theodore Roosevelt to Theodore Roosevelt turned the merge
   of the bare name into the titled one into a merge of a record into itself. */
$i = $find('Theodore Roosevelt');
if ($i !== null && ($rows[$i]['action'] ?? '') === 'merged') {
    $rows[$i]['action'] = 'skipped';
    unset($rows[$i]['intoKey'], $rows[$i]['intoName'], $rows[$i]['into']);
    $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': the rename made this a merge into itself';
    $log[] = ['merged -> skipped', 'Theodore Roosevelt', 'the titled form is now the alias'];
}

/* The stale Beale row: Edward F. Beale exists, so a bare "Beale" creates a
   second record for one man. */
$i = $find('Beale');
if ($i !== null) {
    $was = $rows[$i]['action'] ?? '?';
    $rows[$i]['action'] = 'skipped';
    $rows[$i]['settledBy'] = 'Nathan, ' . date('Y-m-d') . ': stale; Edward F. Beale is the record';
    $log[] = [$was . ' -> skipped', 'Beale', 'Edward F. Beale already covers him'];
}

/* ------------------------------------------------------------- report */

echo ($APPLY ? 'APPLYING to the decisions file' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;
printf("%-22s %-40s %s\n", 'CHANGE', 'NAME', 'NOTE');
foreach ($log as $l) { printf("%-22s %-40s %s\n", $l[0], mb_substr($l[1], 0, 39), $l[2]); }
echo PHP_EOL . 'settlements: ' . count($log) . '   decisions in the file: ' . count($rows) . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to write.' . PHP_EOL; return; }

$doc['decisions'] = array_values($rows);
$tmp = $FILE . '.tmp';
file_put_contents($tmp, json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX);
rename($tmp, $FILE);
$back = json_decode(file_get_contents($FILE), true);
echo 'read-back: ' . count($back['decisions'] ?? []) . ' decisions, '
   . count(array_filter($back['decisions'] ?? [], fn($x) => isset($x['settledBy']))) . ' carry a settlement note' . PHP_EOL;
