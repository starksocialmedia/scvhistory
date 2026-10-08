/**
 * #5475's printed copy into its catalogue entry (Nathan, 8 October 2026: "yes to #5475's printed copy in the catalogue
 * entry"), as at #5541: advertising copy is not a title, so the description stays and the copy is recorded. Read by eye
 * from the scan, legacy/lw3632_large.jpg. Dry run by default; set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/catalogue_5475_2026_10_08.php'))"
 */
$APPLY = false;
$add = 'Printed on the blotter: "Your Naborhood Druggist is your \'Friend in Need\'" and "We sell and recommend STERLING BRAND".';
$e = craft\elements\Entry::find()->id(5475)->status(null)->one();
$cat = trim((string)$e->getFieldValue('catalogueCaption'));
if (str_contains($cat, 'Printed on the blotter')) { echo "already there" . PHP_EOL; return; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . "now: $cat" . PHP_EOL . "new: $cat $add" . PHP_EOL;
if (!$APPLY) { return; }
$e->setFieldValue('catalogueCaption', "$cat $add");
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('catalogue_5475_2026_10_08.php', 1, 'verified', "#5475's printed copy in its catalogue entry");
