/**
 * Scott Wilk (#335) trimmed to public life (Nathan, 2 October 2026: "remove
 * the family line, the inline Wikipedia citation and the garbled sentence").
 *
 * The body came in from WordPress (bodyAuthorship wordpress-import-unsourced)
 * with a chatbot's source labels pasted inline. Each change is an exact
 * replacement, refused unless the text is there as expected:
 *   FAMILY   his wife's name and heritage; "He and Vanessa reside in Santa
 *            Clarita and have two adult children"; "the first in his family
 *            to graduate from college" (the degree is kept).
 *   CITATION the pasted source labels "Wikipedia" (twice), "LinkedIn",
 *            "Santa Clarita Valley Signal.", "ANCA Western Region" and a
 *            trailing "CA". Where a label sat mid-sentence the sentence is
 *            mended with the fewest words.
 *   GARBLED  "He co-founded the California Armenian Legislative Caucus in
 *            2015, the Santa Clarita Valley Signal, to ensure ..." (a source
 *            label fused into the sentence), removed whole.
 * The same text is cut from the unconfirmed recordDates labels that quote it,
 * so a later confirmation cannot publish it. Nothing else in the body is
 * rewritten here; its claims remain unsourced, as bodyAuthorship says.
 * The sweep of every living person's body on 2 October found these problems
 * on Wilk's record only (1 of 23 WordPress-imported records carries the
 * citation labels; the other living person's family mentions are public
 * office, within DATA-MODEL's exception).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/trim_wilk_to_public_life.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$ID = 335;
$EDITS = [
    ['FAMILY', 'He was the first in his family to graduate from college, earning a B.A. in political science', 'He earned a B.A. in political science'],
    ['CITATION', 'representing the 38th District. Wikipedia. He served', 'representing the 38th District. He served'],
    ['CITATION', 'the Santa Clarita Valley Water Agency. LinkedIn Unifying', 'the Santa Clarita Valley Water Agency. Unifying'],
    ['FAMILY', ' His wife, Vanessa Safoyan Wilk, is Armenian-American, and Wilk brought that family connection into his public service with moral clarity and consistency.', ''],
    ['GARBLED', ' He co-founded the California Armenian Legislative Caucus in 2015, the Santa Clarita Valley Signal, to ensure that Armenian-American voices had a platform in Sacramento.', ''],
    ['CITATION', 'to prevent future genocides. Santa Clarita Valley Signal. In 2022', 'to prevent future genocides. In 2022'],
    ['CITATION', 'should federal sanctions be enacted. ANCA Western Region — legislation that was signed by Governor Newsom.', 'should federal sanctions be enacted; it was signed by Governor Newsom.'],
    ['CITATION', 'Armenian National Committee of America-Western Region Wikipedia — alongside', 'Armenian National Committee of America-Western Region, alongside'],
    ['CITATION', 'over party ideology. CA', 'over party ideology.'],
    ['FAMILY', ' He and Vanessa reside in Santa Clarita and have two adult children.', ''],
];
$e = Entry::find()->id($ID)->status(null)->one();
if (!$e || $e->title !== 'Scott Thomas Wilk Sr.') { echo "REFUSING: #$ID is not Wilk" . PHP_EOL; return; }
$body = (string)$e->body; $todo = []; $bad = [];
foreach ($EDITS as $i => [$kind, $old, $new]) {
    $c = substr_count($body, $old);
    if ($c === 1) { $todo[] = $i; echo str_pad($kind, 9) . '"' . mb_substr(trim($old), 0, 70) . '" -> "' . mb_substr($new, 0, 50) . '"' . PHP_EOL; }
    elseif ($c === 0 && ($new === '' || str_contains($body, $new))) { echo str_pad($kind, 9) . 'already done: "' . mb_substr(trim($old), 0, 50) . '"' . PHP_EOL; }
    else { $bad[] = "edit $i found $c times"; }
}
foreach ($todo as $i) { $body = str_replace($EDITS[$i][1], $EDITS[$i][2], $body); }
$body = preg_replace("~\n{3,}~", "\n\n", $body);
/* The same text in the unconfirmed recordDates labels. */
$CUT = ['Vanessa', 'two adult children', 'first in his family', 'Wikipedia', 'LinkedIn', 'the Santa Clarita Valley Signal, to ensure'];
$rows = array_values(array_filter($e->recordDates ?? [], 'is_array')); $labelFix = 0;
foreach ($rows as $k => $r) {
    $l = (string)($r['label'] ?? ''); $l2 = $l;
    foreach ($todo as $i) { $l2 = str_replace(trim($EDITS[$i][1]), trim($EDITS[$i][2]), $l2); }
    foreach ($CUT as $c) { if (str_contains($l2, $c)) { $l2 = trim(preg_replace('~[^.]*' . preg_quote($c, '~') . '[^.]*\.?~u', '', $l2)); } }
    if ($l2 !== $l) { $rows[$k]['label'] = $l2; $labelFix++; }
}
foreach ($CUT as $c) { if (str_contains($body, $c)) { $bad[] = "body still carries \"$c\""; } }
if (preg_match('~\bCA\s*$~', trim(strip_tags($body)))) { $bad[] = 'trailing CA remains'; }
echo count($todo) . " body edits; $labelFix recordDates labels cleaned" . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if (!$todo && !$labelFix) { echo 'nothing to do' . PHP_EOL; return; }
$e->setFieldValues(['body' => $body, 'recordDates' => array_map(fn($r) => ['printed' => $r['printed'] ?? '', 'iso' => $r['iso'] ?? null, 'granularity' => $r['granularity'] ?? '', 'label' => $r['label'] ?? '', 'confirmed' => (bool)($r['confirmed'] ?? false), 'rejected' => (bool)($r['rejected'] ?? false)], $rows)]);
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException('#335: ' . json_encode($e->getFirstErrors())); }
$b = Entry::find()->id($ID)->status(null)->one();
$ok = (string)$b->body === $body && !array_filter($CUT, fn($c) => str_contains(json_encode($b->recordDates) . $b->body, $c));
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('trim_wilk_to_public_life.php', 1, $ok ? 'verified' : 'SHORT', 'Wilk: family, pasted citation labels and the garbled sentence removed');
if (!$ok) { throw new \RuntimeException('trim_wilk_to_public_life: read-back failed'); }
