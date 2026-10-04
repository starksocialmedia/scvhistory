/**
 * Nathan's answers to the double-counting audit, 4 October 2026:
 *  4. "The Tataviam material stays off #913 until consultation. TATAVIAM_AUDIT.md governs it, and
 *     attributing a name meaning to Reynolds does not make it the tribe's. Hold it."
 *     The two paragraphs carrying Reynolds's names, glosses, language dating, villages, headcount and
 *     crafts move from #913's body to withheldBody, which no template shows. The contact history stays.
 *     The death-date sentence follows the audit's finding 12: the 1998 edition corrects Reynolds's 1916
 *     to 1921 from the Ventura County death certificate. Footnotes renumbered; the record's cultural
 *     sensitivity note says what is held and why.
 *  6. "Cite those chapters as the 1998 edition": chapters 59, 69 and 70, cited as Jerry Reynolds's on
 *     seven person records (nine footnotes), now cite the 1998 edition, edited by Leon Worden, naming
 *     Robert S. Birchard for chapter 59 and the reworking of chapter 70 (docs/PROFILES.md).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_audit_decisions_2026_10_04.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$el = Craft::$app->getElements(); $get = fn($id) => Entry::find()->id($id)->status(null)->one(); $bad = []; $plan = [];

/* 4. Tataviam #913 */
$T = $get(913); $b = (string)$T?->body;
$P1 = 'The Tataviam were the people of the upper Santa Clara River valley when the Spanish came. Their neighbors the Kitanemuks called them Tataviam, "dwellers on sunny slopes"; peoples they had displaced called them Allikliks, "grunters." They spoke Takic, a Uto-Aztecan language, and by Jerry Reynolds\'s account had taken the upper valley by about AD 500.[1]';
$P2 = 'They lived in some twenty-five villages: Kamulus at what is now Camulos, Piru-U-Bit on Piru Creek, Tochonanga on Newhall Creek, and Chaguayabit, a town of about five hundred, at Castaic Junction.[1] They wove fine baskets but made no pottery, and left paintings and carvings on rock overhangs and in caves.[1][2]';
$D_OLD = 'He gives 1916 for the death of the last full-blooded Tataviam; Reynolds gives 1921, at the Camulos Ranch.[4][2]';
$D_NEW = 'He gives 1916 for the death of the last full-blooded Tataviam, as Jerry Reynolds first had; the 1998 edition of Reynolds\'s history corrects it to 1921, at the Camulos Ranch, from the Ventura County death certificate.[2][4]';
$tDone = str_contains($b, $D_NEW);
if (!$tDone && (substr_count($b, $P1) !== 1 || substr_count($b, $P2) !== 1 || substr_count($b, $D_OLD) !== 1 || count($T->footnotes ?? []) !== 5)) { $bad[] = '#913 is not as read'; }
if (!$tDone) {
    $nb = str_replace([$P1, $P2], ['The Tataviam were the people of the upper Santa Clara River valley when the Spanish came.', ''], $b);
    $nb = str_replace(['[3][4]', '.[4] The mission', 'San Gabriel.[5]', $D_OLD], ['[1][2]', '.[2] The mission', 'San Gabriel.[3]', $D_NEW], $nb);
    $nb = preg_replace('~(<p>\s*</p>|\n\s*\n\s*\n)~', "\n\n", $nb);
    $fn = $T->footnotes;
    $notes = [
        ['number' => '1', 'note' => (string)$fn[2]['note'], 'source' => 'editorial-2026'],
        ['number' => '2', 'note' => (string)$fn[3]['note'], 'source' => 'editorial-2026'],
        ['number' => '3', 'note' => (string)$fn[4]['note'], 'source' => 'editorial-2026'],
        ['number' => '4', 'note' => 'Jerry Reynolds, History of the Santa Clarita Valley, 1998 edition, edited by Leon Worden, chapter 5, "Tribal Relics," and the edition\'s note 1 to it, which gives 1921 from the Ventura County death certificate; article #829 in this archive.', 'source' => 'editorial-2026'],
    ];
    $withheld = trim("Held from the page pending consultation with the Fernandeño Tataviam Band of Mission Indians (Nathan, 4 October 2026; TATAVIAM_AUDIT.md). Both paragraphs rest on Jerry Reynolds, History of the Santa Clarita Valley, chapters 4 and 5.\n\n" . strip_tags($P1) . "\n\n" . strip_tags($P2) . "\n\n" . (string)$T->withheldBody);
    $plan[913] = [$T, ['body' => $nb, 'footnotes' => $notes, 'withheldBody' => $withheld,
        'culturalSensitivityNote' => 'This record will be revised in consultation with the Fernandeño Tataviam Band of Mission Indians. Until then it leaves out the names, meanings, village sites and figures that come only from outside historians.']];
    echo "#913 Tataviam: two paragraphs to withheldBody; the date sentence on the 1998 edition; footnotes 1 to 4; the sensitivity note set\n   NEW BODY: " . preg_replace('~\s+~', ' ', strip_tags($nb)) . PHP_EOL;
} else { echo '#913: already done' . PHP_EOL; }

/* 6. Chapters 59, 69, 70 */
$ED = 'Jerry Reynolds, History of the Santa Clarita Valley, 1998 edition, edited by Leon Worden';
$CH = [
    '59. Mixville' => [2143, 'chapter 59, "Mixville," rewritten for the edition by Robert S. Birchard'],
    '69. Rebels With a Cause' => [2163, 'chapter 69, "Rebels With a Cause"'],
    '70. Birth of a City' => [2165, 'chapter 70, "Birth of a City," reworked for the edition by another contributor'],
];
foreach ([18702, 18616, 16140, 15919, 15874, 15808, 15737] as $pid) {
    $p = $get($pid); $rows = $p->footnotes ?? []; $ch = 0;
    foreach ($rows as $i => $r) {
        foreach ($CH as $t => [$aid, $label]) {
            if (str_contains((string)$r['note'], "Jerry Reynolds, \"$t,\"")) { $rows[$i]['note'] = "$ED, $label; article #$aid in this archive."; $ch++; }
        }
    }
    $body = strip_tags((string)$p->body); $flag = preg_match('~Reynolds (wrote|writes|says|said|records|gives)~', $body) ? ' (its text names Reynolds; read by hand)' : '';
    echo "#$pid {$p->title}: " . ($ch ? "$ch footnote(s) to the 1998 edition$flag" : 'nothing to change') . PHP_EOL;
    if ($ch) { $plan[$pid] = [$p, ['footnotes' => array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)$r['note'], 'source' => (string)($r['source'] ?? 'editorial-2026')], $rows)]]; }
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$n = 0; $tx = Craft::$app->getDb()->beginTransaction();
try { foreach ($plan as $id => [$e, $v]) { $e->setFieldValues($v); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id: " . json_encode($e->getFirstErrors())); } $n++; } $tx->commit(); }
catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$ok = !str_contains((string)$get(913)->body, 'dwellers on sunny slopes') && str_contains(json_encode($get(15808)->footnotes), '1998 edition');
echo 'READ-BACK ' . ($ok ? "OK: $n records" : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('apply_audit_decisions_2026_10_04.php', $n, $ok ? 'verified' : 'SHORT', 'Tataviam ethnography held off #913 pending consultation; chapters 59, 69, 70 cited as the 1998 edition');
if (!$ok) { throw new \RuntimeException('apply_audit_decisions: read-back failed'); }
