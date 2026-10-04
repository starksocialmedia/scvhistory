/**
 * Two profiles said "Jerry Reynolds writes" of chapters 69 and 70, which the 1998 edition reworked (Nathan,
 * 4 October 2026: cite those chapters as the 1998 edition). Dan Hon #18616 (chapter 69) and Jill Klajic
 * #15874 (chapter 70): the sentence now names the edition. Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_reworked_chapter_wording_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements(); $OLD = 'Jerry Reynolds writes that'; $NEW = 'The 1998 edition of Jerry Reynolds\'s history says that'; $n = 0; $bad = [];
foreach ([18616, 15874] as $id) {
    $p = Entry::find()->id($id)->status(null)->one(); $b = (string)$p->body;
    if (str_contains($b, $NEW)) { echo "#$id already\n"; continue; }
    if (substr_count($b, $OLD) !== 1) { $bad[] = "#$id not as read"; continue; }
    echo "#$id {$p->title}: '$OLD' -> '$NEW'\n";
    if ($APPLY) { $p->setFieldValue('body', str_replace($OLD, $NEW, $b)); if (!$el->saveElement($p)) { throw new \RuntimeException("#$id"); } $n++; }
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_reworked_chapter_wording_2026_10_04.php', $n, 'verified', 'Hon and Klajic: chapters 69 and 70 named as the 1998 edition'); }
