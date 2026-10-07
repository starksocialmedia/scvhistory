/**
 * A term that ended when the lines moved (Nathan, 7 October 2026: "The archive records his service to this valley, not his whole
 * career, and the end of that service is when the lines moved"; approved: "add the howEnded option, check each of the thirteen
 * against its footnote"). New howEnded option "lines-moved", "Lines moved: the district no longer held the valley", after
 * "left". Of the thirteen terms whose footnotes mention the lines, three ended that way and take it: Kevin McCarthy's (#29525, to
 * 2013: "from 2013 his district held none of it"), Henry Stern's (#29521, to 2024: "re-elected in 2024 for a redrawn 27th District
 * that holds no part of the valley") and Jeff Gorell's (#29503, to 2012: "From 2012 he sat for the 44th District, which held no
 * part of the valley"). The rest keep their values: McKeon's (#26980) and Richman's 2002 (#29497) terms were split at the new lines
 * and his service went on ("reelected" is right); Bill Thomas retired; the others' notes are about how a term began or a renumbering;
 * Loberg's and King's matched only "Deadlines".
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/lines_moved_2026_10_07.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $el = Craft::$app->getElements(); $n = 0;
$f = $fs->getFieldByHandle('howEnded');
$opts = array_map(fn($o) => is_array($o) ? $o : ['label' => (string)$o->label, 'value' => (string)$o->value, 'default' => (bool)$o->default], $f->options);
if (in_array('lines-moved', array_column($opts, 'value'), true)) { echo "option lines-moved: exists\n"; }
else { echo "option lines-moved: add after \"left\"\n";
  if ($APPLY) { $i = array_search('left', array_column($opts, 'value'), true); array_splice($opts, $i + 1, 0, [['label' => 'Lines moved: the district no longer held the valley', 'value' => 'lines-moved', 'default' => false]]);
    $f->options = $opts; if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++; } }
foreach ([29525 => 'Kevin McCarthy', 29521 => 'Henry Stern', 29503 => 'Jeff Gorell'] as $id => $name) {
  $h = Entry::find()->id($id)->status(null)->one(); if (!str_starts_with($h->title, $name)) { throw new \RuntimeException("#$id is not $name's"); }
  echo "#$id {$h->title} ({$h->termStartEdtf} to {$h->termEndEdtf}): howEnded {$h->howEnded->value} -> lines-moved\n";
  if ($APPLY && $h->howEnded->value !== 'lines-moved') { $h->setFieldValue('howEnded', 'lines-moved'); if (!$el->saveElement($h)) { throw new \RuntimeException("#$id"); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('lines_moved_2026_10_07.php', $n, 'verified', 'howEnded lines-moved; McCarthy, Stern and Gorell'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
