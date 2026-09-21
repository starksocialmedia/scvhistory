/**
 * Applies the canon's pattern rules to decisions already made.
 *
 * A rule added today does not reach a decision made yesterday. "Rancho San
 * Francisco" was approved as a place before the rule existed and may carry the
 * wrong subtype, or have been approved as an organization outright. The rules
 * are meant to be the archive's settled answer, and an answer that applies only
 * to future decisions is not settled.
 *
 * Reads web/review/records-decided.json, retypes what the rules cover, and
 * reports every change. Dry run by default: the decisions file is a record of
 * judgement and this rewrites it, so it waits for a flag like anything else.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/retype_decisions_by_canon.php'))"
 */

$APPLY = false;

$ROOT = \Craft::getAlias('@root');
$FILE = \Craft::getAlias('@webroot') . '/review/records-decided.json';
$CANON = $ROOT . '/inventory/legacy/name-canon.json';

if (!file_exists($FILE))  { echo 'no records-decided.json' . PHP_EOL; return; }
if (!file_exists($CANON)) { echo 'no name-canon.json' . PHP_EOL; return; }

$canon = json_decode(file_get_contents($CANON), true) ?: [];
$doc   = json_decode(file_get_contents($FILE), true) ?: [];
$rows  = $doc['decisions'] ?? [];

echo ($APPLY ? 'APPLYING to the decisions file' : 'DRY RUN') . PHP_EOL;
echo 'decisions on file: ' . count($rows) . '   rules: ' . count($canon['rules'] ?? []) . PHP_EOL . PHP_EOL;

$changed = 0; $already = 0;

foreach ($rows as $i => $r) {
    $name = trim((string)($r['name'] ?? ''));
    if ($name === '' || ($r['type'] ?? '') === 'pair') { continue; }

    foreach (($canon['rules'] ?? []) as $rule) {
        $pat = '~' . str_replace('~', '\~', (string)$rule['pattern']) . '~i';
        if (!preg_match($pat, $name)) { continue; }

        $wantType  = (string)($rule['type'] ?? '');
        $wantPlace = (string)($rule['placeType'] ?? '');
        $isType    = (string)($r['type'] ?? '');
        $isPlace   = (string)($r['placeType'] ?? '');

        if ($isType === $wantType && ($wantPlace === '' || $isPlace === $wantPlace)) {
            $already++;
            break;
        }

        printf("%-40s %s\n", mb_substr($name, 0, 39), '(' . $rule['pattern'] . ')');
        printf("    type       %-14s -> %s\n", $isType ?: '(none)', $wantType);
        if ($wantPlace !== '') {
            printf("    placeType  %-14s -> %s\n", $isPlace ?: '(none)', $wantPlace);
        }
        if ($isType !== '' && $isType !== $wantType) {
            printf("    note       it was decided as %s; the rule says the archive treats these as %s\n",
                $isType, $wantType);
        }
        echo PHP_EOL;

        $rows[$i]['type'] = $wantType;
        if ($wantPlace !== '') { $rows[$i]['placeType'] = $wantPlace; }
        $rows[$i]['retypedBy'] = 'canon rule ' . $rule['pattern'];
        $changed++;
        break;
    }
}

echo str_repeat('-', 70) . PHP_EOL;
echo 'retyped: ' . $changed . '   already correct: ' . $already . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to write the decisions file.' . PHP_EOL; return; }

$doc['decisions'] = $rows;
$tmp = $FILE . '.tmp';
file_put_contents($tmp, json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX);
rename($tmp, $FILE);

$back = json_decode(file_get_contents($FILE), true);
echo 'read-back: ' . count($back['decisions'] ?? []) . ' decisions, '
   . count(array_filter($back['decisions'] ?? [], fn($x) => isset($x['retypedBy']))) . ' carry a retype note' . PHP_EOL;
