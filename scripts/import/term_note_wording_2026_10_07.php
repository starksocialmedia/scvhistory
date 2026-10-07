/**
 * Term notes become visible on 7 October 2026 (the offices box prints them; Nathan: "print term notes in the offices box"). One of
 * the 1,224 names the archive's own files: Kevin McCarthy's term #29525, note 5, "(templates/_data/valley-districts.json)", and a
 * term number a reader cannot follow ("office holding #29523": terms have no page). Reworded to a reader's words; the facts stay.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/term_note_wording_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$h = Entry::find()->id(29525)->status(null)->one();
$rows = array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)($r['source'] ?? '')], iterator_to_array($h->footnotes));
$new = array_map(fn($r) => ['note' => str_replace([' (templates/_data/valley-districts.json)', '; office holding #29523'], [' (the archive\'s count of the valley\'s people by district)', ''], $r['note'])] + $r, $rows);
$ch = array_filter(array_keys($rows), fn($i) => $rows[$i]['note'] !== $new[$i]['note']);
foreach ($ch as $i) { echo "#29525 note {$rows[$i]['number']}: " . mb_substr($new[$i]['note'], 0, 400) . "\n"; }
if (!$ch) { echo "#29525: done already\n"; }
if ($APPLY && $ch) { $h->setFieldValue('footnotes', $new); if (!$el->saveElement($h)) { throw new \RuntimeException('#29525'); } $n++; }
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('term_note_wording_2026_10_07.php', $n, 'verified', 'McCarthy term note in a reader\'s words'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
