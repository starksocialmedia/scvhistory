/**
 * The Signal's founding date (2 October 2026). Nathan asked for every
 * organization with a founding date checked after SCV Water's "1913" turned
 * out to be its oldest predecessor's. The audit (every organization's
 * dateFounded against its chain and its own text) found one more wrong date:
 * The Santa Clarita Valley Signal, "founded 2019", a century off. Jerry
 * Reynolds, "61. The Chroniclers" (#2147): "Edwin H. Brown founded The Newhall
 * Signal on February 7, 1919." Reynolds wrote decades later, so the evidence
 * is retrospective. The field is corrected with a footnote and a Correction
 * note; the record has no text of its own.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_signal_founded.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$bad = [];
$src = Entry::find()->id(2147)->status(null)->one();
if (!$src || !str_contains(preg_replace('~\s+~', ' ', strip_tags((string)$src->body)), 'Edwin H. Brown founded The Newhall Signal on February 7, 1919.')) { $bad[] = 'Reynolds part 61 does not read as expected'; }
$e = Entry::find()->id(376)->status(null)->one();
if (!$e || $e->title !== 'The Santa Clarita Valley Signal') { $bad[] = '#376 is not the Signal'; }
$NOTE = 'Jerry Reynolds, "61. The Chroniclers," in this archive: "Edwin H. Brown founded The Newhall Signal on February 7, 1919." The first edition was a tabloid.';
$CORR = 'The Signal was founded on February 7, 1919, as The Newhall Signal, not in 2019 as this record gave it (note 1).';
$done = trim((string)$e->dateFounded) === 'February 7, 1919';
if (!$done && trim((string)$e->dateFounded) !== '2019') { $bad[] = '#376 dateFounded is "' . $e->dateFounded . '"'; }
echo $done ? "#376 already corrected\n" : "#376 dateFounded \"2019\" -> \"February 7, 1919\" (retrospective), footnote 1, Correction note\n";
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if ($done) { return; }
$fn = array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? '')], array_filter($e->footnotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== ''));
$fn[] = ['number' => (string)(count($fn) + 1), 'note' => $NOTE, 'source' => 'editorial-2026'];
$ed = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($e->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
$ed[] = ['heading' => 'Correction, 2026', 'position' => 'bottom', 'note' => $CORR];
$e->setFieldValues(['dateFounded' => 'February 7, 1919', 'dateFoundedEdtf' => '1919-02-07', 'foundedEvidence' => 'retrospective', 'footnotes' => array_values($fn), 'editorNotes' => $ed]);
$ok = Craft::$app->getElements()->saveElement($e) && (string)Entry::find()->id(376)->status(null)->one()->dateFounded === 'February 7, 1919';
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_signal_founded.php', 1, $ok ? 'verified' : 'SHORT', 'the Signal founded February 7, 1919 (Reynolds), not 2019');
if (!$ok) { throw new \RuntimeException('fix_signal_founded: read-back failed'); }
