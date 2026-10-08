/**
 * Andrés Pico's portrait (Nathan, 8 October 2026: "make the 1850 photograph his portrait. It is a real unedited photograph
 * of the man on his own record"). Asset #4, andres_pico_circa_1850.jpg, already among his related images, becomes
 * featuredImage on #317. Dry run by default; set $APPLY = true.
 */
$APPLY = false;
$e = craft\elements\Entry::find()->id(317)->status(null)->one(); $a = craft\elements\Asset::find()->id(4)->one();
$feat = $e->featuredImage->status(null)->ids(); $imgs = $e->recordImages->status(null)->ids();
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . "#317 {$e->title}: featuredImage now " . json_encode($feat) . "; #4 {$a->filename} in recordImages: " . (in_array(4, $imgs) ? 'yes' : 'no') . PHP_EOL;
if ($feat === [4]) { echo 'already set' . PHP_EOL; return; }
if (!$APPLY) return;
$e->setFieldValue('featuredImage', [4]);
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('pico_portrait_2026_10_08.php', 1, 'verified', "Pico's 1850 photograph as his portrait");
