/**
 * Turns collectionFrozen on or off for one collection, from the console.
 *
 * The freeze was one-way until 22 September: the guard in
 * modules/collectionfreeze refused every console save of a frozen collection,
 * including the save that would unfreeze it. Mentryville #12135 was frozen as
 * a test and could not be released. The guard now lets a save through when the
 * only thing it changes is the switch going off, and this is the script that
 * does it.
 *
 * Freezing needs no exemption: at the moment of the save the collection is not
 * yet frozen, so the guard has nothing to refuse.
 *
 * Reads back from a freshly loaded entry and fails loudly on a short read-back.
 *
 * Dry run by default.
 * Run:
 *   ddev craft exec '$FREEZE_SLUG = "mentryville"; eval(file_get_contents("scripts/import/set_collection_frozen.php"));'
 * Apply:
 *   ddev craft exec '$FREEZE_APPLY = true; $FREEZE_SLUG = "mentryville"; $FREEZE_ON = false; eval(file_get_contents("scripts/import/set_collection_frozen.php"));'
 */

$APPLY = false;
if (!empty($FREEZE_APPLY)) { $APPLY = true; echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$SLUG = $FREEZE_SLUG ?? '';
$ON   = isset($FREEZE_ON) ? (bool)$FREEZE_ON : false;

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

if (!Craft::$app->getFields()->getFieldByHandle('collectionFrozen')) {
    echo 'collectionFrozen does not exist. Run add_collection_frozen_field.php first.' . PHP_EOL;
    return;
}

/* Every collection and where it stands, so a run says what the state was even
   when it changes nothing. */
$all = \craft\elements\Entry::find()->section('collections')->status(null)->limit(null)->all();
echo 'collections:' . PHP_EOL;
foreach ($all as $c) {
    $pieces = \craft\elements\Entry::find()->relatedTo(['targetElement' => $c, 'field' => 'partOfCollection'])->status(null)->count();
    printf("   %-38s #%-7d %-8s %d pieces\n", $c->slug, $c->id, $c->getFieldValue('collectionFrozen') ? 'FROZEN' : 'open', $pieces);
}

if ($SLUG === '') {
    echo PHP_EOL . 'Set $FREEZE_SLUG to a collection slug. Nothing to do.' . PHP_EOL;
    return;
}

$c = \craft\elements\Entry::find()->section('collections')->slug($SLUG)->status(null)->one();
if (!$c) { echo PHP_EOL . 'no collection with slug ' . $SLUG . PHP_EOL; return; }

$was = (bool)$c->getFieldValue('collectionFrozen');
echo PHP_EOL . $c->slug . ' #' . $c->id . ': ' . ($was ? 'frozen' : 'open')
   . ' -> ' . ($ON ? 'frozen' : 'open') . PHP_EOL;

if ($was === $ON) { echo 'already there, nothing to write.' . PHP_EOL; return; }
if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $FREEZE_APPLY = true to apply.' . PHP_EOL; return; }

$c->setFieldValue('collectionFrozen', $ON);
if (!Craft::$app->getElements()->saveElement($c)) {
    echo 'FAILED: ' . json_encode($c->getErrors()) . PHP_EOL;
    throw new \RuntimeException('set_collection_frozen: the save was refused');
}

$fresh = \craft\elements\Entry::find()->section('collections')->id($c->id)->status(null)->one();
$now = (bool)$fresh->getFieldValue('collectionFrozen');
$ok = $now === $ON;
echo 'READ-BACK ' . ($ok ? 'OK' : 'FAIL') . ': reads ' . ($now ? 'frozen' : 'open')
   . ', expected ' . ($ON ? 'frozen' : 'open') . PHP_EOL;
if (!$ok) { throw new \RuntimeException('set_collection_frozen: read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('set_collection_frozen.php', 1, 'verified: reads ' . ($now ? 'frozen' : 'open'),
    $c->slug . ' #' . $c->id . ' ' . ($was ? 'frozen' : 'open') . ' -> ' . ($ON ? 'frozen' : 'open'));
