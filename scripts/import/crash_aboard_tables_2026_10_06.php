/**
 * The people aboard as tables, and no pointing words in the prose (Nathan, 6 October 2026: "Western Air Express Flight 7 is the
 * page. Build the table of people aboard and take the pointing words out of the prose. Do United Flight 34 the same way: name the
 * nine passengers if the sources name them, and say so if they do not").
 *  - Flight 7 (#31914): the thirteen aboard from Ron Kraus's chart as Leon Worden's photograph pages carry it (new note 10), with
 *    when each of the dead died from notes 3 to 5; the paragraph that walked through the dead one sentence at a time is shortened,
 *    and "the note at the foot of this page" and "Photographs taken that morning show them" are gone.
 *  - Flight 34 (#31907): the sources name all nine passengers (SCVHistory.com's account, already quoted in note 1), so the twelve
 *    aboard are a table, names as printed; the crew's homes are blank except Yvonne Trego's (note 5).
 * The new bodies are in inventory/review/crash-aboard-tables-2026-10-06.json, with the checksum of each body they replace; a body
 * that has changed since is refused. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/crash_aboard_tables_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$D = json_decode(file_get_contents("$root/inventory/review/crash-aboard-tables-2026-10-06.json"), true);
foreach ($D as $id => $d) {
  $e = Entry::find()->id((int)$id)->status(null)->one(); $cur = (string)$e->body;
  if ($cur === $d['body']) { echo "#$id {$e->title}: done already\n"; continue; }
  if (hash('sha256', $cur) !== $d['old_sha']) { echo "#$id {$e->title}: REFUSED, the body has changed since it was read\n"; continue; }
  $v = ['body' => $d['body']];
  if (!empty($d['fn10'])) { $rows = array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)($r['source'] ?? '')], iterator_to_array($e->footnotes));
    if (!in_array('10', array_column($rows, 'number'), true)) { $rows[] = ['number' => '10', 'note' => $d['fn10'], 'source' => 'editorial-2026']; $v['footnotes'] = $rows; } }
  echo "#$id {$e->title}: body " . mb_strlen($cur) . ' -> ' . mb_strlen($d['body']) . ' chars, table rows ' . (substr_count($d['body'], "\n") - substr_count($cur, "\n")) . (isset($v['footnotes']) ? '; note 10 added' : '') . PHP_EOL;
  if ($APPLY) { $e->setFieldValues($v); if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++; }
}
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('crash_aboard_tables_2026_10_06.php', $n, 'verified', 'Flight 7 and Flight 34: the people aboard as tables'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
