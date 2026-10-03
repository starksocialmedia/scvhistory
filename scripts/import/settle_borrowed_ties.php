/**
 * Four people whose tie to the valley ran through someone else's story
 * (Nathan, 3 October 2026):
 *
 *   Cave Johnson Couts (#323)  "keep, and add the 1852 letter. Writing to
 *        Stearns about the nest of thieves in the Santa Clara is his own
 *        observation of this place." The profile now opens with it (Reynolds,
 *        chapter 21, #863), then the cattle drives, then his life in San Diego;
 *        same sentences otherwise, notes renumbered in order.
 *   Juan Bandini (#325)  "remove. His tie is his son-in-law."
 *   José Antonio Aguirre (#329)  "remove. The Tejon is outside the valley."
 *        Both: nothing points at either and no review decision names them;
 *        deleted the ordinary way (Craft's trash, 30 days) and listed in
 *        removed-claims.json. No redirect: neither has a successor.
 *   Thomas O. Larkin (#311)  "a line. He reported on the San Feliciano placers
 *        from Monterey, which is about here even if he was not." His dates came
 *        from the WordPress file and no source here gives them: marked unsourced
 *        with a note.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/settle_borrowed_ties.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $els = Craft::$app->getElements();
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$body = fn($id) => $ws(Entry::find()->id($id)->status(null)->one()?->body);
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$bad = [];
$must = [863 => ['In spring, 1852, Cave Couts wrote to Abel Stearns: "The nest of thieves in the Santa Clara did all they knew how to make me loose [sic] a lot, but not all.', 'I learned that they got about one hundred head of Forester, fifty from José Antonio Arguello, seventy of Machado\'s, all of Castro\'s, and others in proportion', 'On July 12 the Couts brothers arrived at San Jose'],
    1428 => ['in the San Feliciano Canyon tributary of Piru Creek', 'The American Consul Larkin, at Monterey, wrote the New York Sun that a common laborer could pick up $2 a day']];
foreach ($must as $id => $ps) { $t = $body($id); foreach ($ps as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "#$id does not read \"" . mb_substr($ph, 0, 60) . '"'; } } }

/* ---------------------------------------------------------------- Couts */
$C = Entry::find()->id(323)->status(null)->one();
$cParas = array_values(array_filter(array_map('trim', preg_split("~\n\s*\n~", (string)$C->body))));
$oldOpen = 'Cave Johnson Couts was a Tennessee-born army officer who settled in San Diego County after the Mexican War, married into the Bandini family and became one of the wealthiest ranchers in Southern California.[1] He belongs to this valley\'s history through the Gold Rush cattle trade, in Jerry Reynolds\'s account of how the rancheros found a market for their beef.[2]';
$cNew = null;
$lead = 'Cave Johnson Couts, a San Diego County rancher, drove cattle north to the Gold Rush markets. In the spring of 1852 he wrote to Abel Stearns: "The nest of thieves in the Santa Clara did all they knew how to make me loose [sic] a lot, but not all." Others fared worse; he heard that the thieves had taken about a hundred head of Forester\'s, fifty of José Antonio Arguello\'s, seventy of Machado\'s and all of Castro\'s. Jerry Reynolds quotes the letter in his history of this valley; the drive reached San Jose on July 12.[1]';
if (count($cParas) === 5 && $cParas[0] === $oldOpen) {
    $renum = fn($p) => preg_replace_callback('~\[(\d)\]~', fn($m) => '[' . ['1' => '3', '2' => '2'][$m[1]] . ']', $p);
    $p3 = 'A Tennessee-born army officer, he settled in San Diego County after the Mexican War, married into the Bandini family and became one of the wealthiest ranchers in Southern California.[3] ' . $renum($cParas[1]);
    $cNew = implode("\n\n", [$lead, $renum($cParas[2]), $p3, $renum($cParas[3]), $renum($cParas[4])]);
    $old = array_values(array_map(fn($f) => (string)$f['note'], $C->footnotes));
    $cNotes = ['Jerry Reynolds, "Chapter 21. Buttons and Bows," History of the Santa Clarita Valley, article #863 in this archive, quoting Couts\'s letter to Abel Stearns.', $old[1], $old[0]];
    echo "Couts #323: lead with the 1852 letter; " . str_word_count(strip_tags((string)$C->body)) . ' -> ' . str_word_count($cNew) . ' words' . PHP_EOL;
} elseif (str_starts_with((string)$C->body, $lead)) { echo 'Couts #323: already leads with the letter' . PHP_EOL; }
else { $bad[] = 'Couts #323: body is not the one this restructures'; }

/* ---------------------------------------------------------------- Larkin */
$L = Entry::find()->id(311)->status(null)->one();
$lBody = 'Thomas O. Larkin, the American consul at Monterey, wrote to the New York Sun that a common laborer could pick up $2 a day at the San Feliciano placers, in a canyon off Piru Creek.[1] No source in this archive places him in the Santa Clarita Valley.';
$lNotes = ['A.B. Perkins, "5. Mining," The Story of Our Valley, article #1428 in this archive.'];
$lNote = ['His dates', 'No source has been found for his dates of birth and death as given here.'];
$lDone = trim((string)$L->body) === $lBody;
if (!$L || $L->title !== 'Thomas O. Larkin') { $bad[] = '#311 is not Larkin'; }
echo 'Larkin #311: ' . ($lDone ? 'already a line' : str_word_count(strip_tags((string)$L->body)) . ' words -> ' . str_word_count($lBody)) . PHP_EOL;

/* ---------------------------------------------------------------- Bandini, Aguirre */
$REG = "$root/scripts/import/removed-claims.json"; $reg = json_decode(file_get_contents($REG), true);
$RM = [325 => ['Juan Bandini', 'A San Diego ranchero whose tie to the valley ran through his son-in-law, Cave Couts, whose 1849 drive with Bandini\'s cattle went by the coast. His tie is his son-in-law\'s, not his own (Nathan, 3 October 2026).'],
       329 => ['José Antonio Aguirre', 'A San Diego merchant who received the Tejon grant with Ygnacio del Valle in 1843. The Tejon is outside the valley (Nathan, 3 October 2026).']];
$rm = [];
foreach ($RM as $id => [$t, $why]) {
    $e = Entry::find()->id($id)->status(null)->one();
    $inReg = (bool)array_filter($reg['removedRecords'], fn($x) => $x['record'] === $id);
    if ($e && $e->title !== $t) { $bad[] = "#$id is {$e->title}"; continue; }
    $in = $e ? Entry::find()->relatedTo(['targetElement' => $e])->status(null)->count() : 0;
    if ($in) { $bad[] = "$t: $in records point at it"; }
    $rm[$id] = [$e, $t, $why, $inReg];
    echo "$t #$id: " . ($e ? "live, $in inbound; to the trash" : 'already removed') . '; registry ' . ($inReg ? 'listed' : 'to add') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }

$short = [];
if ($cNew) { $C->setFieldValues(['body' => $cNew, 'footnotes' => $fn($cNotes)]); if (!$els->saveElement($C)) { $short[] = 'Couts ' . json_encode($C->getFirstErrors()); } }
if (!$lDone) {
    $rows = array_values(array_filter($L->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''));
    $rows = array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)$r['note']], $rows);
    if (!in_array($lNote[1], array_column($rows, 'note'), true)) { $rows[] = ['heading' => $lNote[0], 'position' => 'bottom', 'note' => $lNote[1]]; }
    $L->setFieldValues(['body' => $lBody, 'footnotes' => $fn($lNotes), 'bodyAuthorship' => 'editorial-2026', 'birthEvidence' => 'uncited', 'deathEvidence' => 'uncited', 'editorNotes' => $rows]);
    if (!$els->saveElement($L)) { $short[] = 'Larkin ' . json_encode($L->getFirstErrors()); }
}
foreach ($rm as $id => [$e, $t, $why, $inReg]) {
    if (!$inReg) { $reg['removedRecords'][] = ['record' => $id, 'title' => $t, 'section' => 'persons', 'why' => $why, 'removed' => '2026-10-03', 'by' => 'scripts/import/settle_borrowed_ties.php']; }
    if ($e && !$els->deleteElement($e)) { $short[] = "delete #$id"; }
}
file_put_contents($REG, json_encode($reg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

$ok = ['Couts leads with the letter' => str_starts_with((string)Entry::find()->id(323)->status(null)->one()->body, $lead),
    'Larkin a line' => trim((string)Entry::find()->id(311)->status(null)->one()->body) === $lBody];
foreach ($RM as $id => [$t]) { $ok["$t removed"] = !Entry::find()->id($id)->status(null)->exists() && (bool)array_filter(json_decode(file_get_contents($REG), true)['removedRecords'], fn($x) => $x['record'] === $id); }
foreach ($ok as $k => $v) { echo ($v ? 'OK    ' : 'SHORT ') . $k . PHP_EOL; if (!$v) { $short[] = $k; } }
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('settle_borrowed_ties.php', count(array_filter($ok)), $short ? 'SHORT' : 'verified', 'Couts leads with his 1852 letter; Larkin a line; Bandini and Aguirre to the trash and the registry');
if ($short) { throw new \RuntimeException('settle_borrowed_ties: ' . implode(', ', $short)); }
