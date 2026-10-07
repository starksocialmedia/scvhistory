/**
 * Cephas L. Bard (#2532) leaves the person records (Nathan, 6 October 2026: "Cephas Bard: make him a row. No sourced valley role
 * means he fails the rule"). He holds no office, so there is no term to keep as a row; what points at him is "1. Early
 * Inhabitants" (#1420), as its subject, where Perkins quotes his 1894 address. That link is removed and his name stays in the
 * article's text, as a passing mention does. The record is deleted (Craft's soft delete, restorable). His Commons portrait
 * (asset #31754, Men of California, 1901) stays in the asset library, attached to nothing.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/remove_cephas_bard_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $n = 0;
$p = Entry::find()->id(2532)->status(null)->one();
if (!$p) { echo "#2532: removed already\n"; }
else {
  if ($p->title !== 'Cephas L. Bard') { throw new \RuntimeException("#2532 is {$p->title}"); }
  $other = array_diff(Entry::find()->status(null)->relatedTo(['targetElement' => $p])->ids(), [1420]);
  if ($other) { throw new \RuntimeException('also pointed at by ' . implode(', ', $other)); }
  $a = Entry::find()->id(1420)->status(null)->one(); $sp = $a->subjectPerson->status(null)->ids();
  echo "#1420 {$a->title}: subjectPerson " . implode(',', $sp) . ' -> ' . (implode(',', array_diff($sp, [2532])) ?: 'none') . "\n#2532 Cephas L. Bard: delete\n";
  if ($APPLY) {
    $a->setFieldValue('subjectPerson', array_values(array_diff($sp, [2532]))); if (!$el->saveElement($a)) { throw new \RuntimeException('#1420'); } $n++;
    if (!$el->deleteElement($p)) { throw new \RuntimeException('#2532 not deleted'); } $n++;
  }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('remove_cephas_bard_2026_10_06.php', $n, 'verified', 'Cephas L. Bard removed under the person rule; #1420 unlinked'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
