/**
 * Renames the collectionKind value book -> series, and keeps book for what the
 * word actually means.
 *
 * WHY THE VALUE WAS WRONG
 *
 * Both records carrying "book" are newspaper serials. "Story of Our Valley" ran
 * in The Signal across the 1940s and 1950s; "History of the Santa Clarita
 * Valley" ran as columns across two decades. Neither was published as a volume.
 * What they have in common -- and what the reader needs to know -- is that the
 * pieces are meant to be read in order, which is what "series" says and what
 * "book" only implied by accident.
 *
 * Book stays in the dropdown, because the archive will hold actual published
 * books and Carl Boyer's is the first candidate. A value that is wrong for
 * today's two records is not a value that should be deleted; it is one that
 * should be given to the right records later.
 *
 * ORDER
 *
 * The option list is saved with BOTH values present before any entry is moved.
 * A Dropdown stores its raw value in the content row and the options are only a
 * list of what the control panel will offer, so an entry holding a value no
 * longer in the list reads back as that value and displays as blank -- it does
 * not error, which is worse, because nothing says anything is wrong.
 *
 * WHY THE FIRST RUN MOVED NOTHING
 *
 * It collected the entries, saved the new option list, then wrote to the
 * entries it already had in hand. Those carry the field layout as it was when
 * they were loaded, and a Dropdown validates its value against that instance's
 * options -- which did not yet contain series. Both saves failed validation
 * with "Collection Kind is invalid", $moved stayed at zero, and the read-back
 * reported FAILED, which is the one part that behaved.
 *
 * So the entries are re-fetched after the field is saved, and a failed save now
 * prints its validation errors instead of being counted as a silent zero. A
 * count that says nothing happened does not say why.
 *
 * Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/rename_collection_kind_book_to_series.php'))"
 */

$APPLY = false;

$HANDLE = 'collectionKind';
$FROM = 'book';
$TO = 'series';

/* The list as it should end up, in the order the control panel offers it. */
$OPTIONS = [
    ['label' => 'Series, read in order',        'value' => 'series'],
    ['label' => 'Book, a published volume',     'value' => 'book'],
    ['label' => 'Column, one author over time', 'value' => 'column'],
    ['label' => 'Catalogue, numbered items',    'value' => 'catalogue'],
    ['label' => 'Topic, gathered by subject',   'value' => 'topic'],
];

$fs = \Craft::$app->getFields();
$f = $fs->getFieldByHandle($HANDLE);

echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

if (!$f) { echo 'field ' . $HANDLE . ' NOT FOUND' . PHP_EOL; return; }

echo 'OPTIONS NOW:' . PHP_EOL;
foreach (($f->options ?? []) as $o) {
    printf("   %-12s %s\n", $o['value'], $o['label']);
}
echo PHP_EOL . 'OPTIONS AFTER:' . PHP_EOL;
foreach ($OPTIONS as $o) {
    $had = false;
    foreach (($f->options ?? []) as $x) { if ($x['value'] === $o['value']) { $had = true; } }
    printf("   %-12s %-32s %s\n", $o['value'], $o['label'], $had ? '' : 'new');
}

$moving = [];
foreach (\craft\elements\Entry::find()->section('collections')->status(null)->limit(null)->all() as $e) {
    $has = false;
    foreach ($e->getFieldLayout()->getCustomFields() as $c) { if ($c->handle === $HANDLE) { $has = true; } }
    if (!$has) { continue; }
    if (($e->{$HANDLE}->value ?? '') === $FROM) { $moving[] = $e; }
}

echo PHP_EOL . 'RECORDS MOVING ' . $FROM . ' -> ' . $TO . ' (' . count($moving) . '):' . PHP_EOL;
foreach ($moving as $e) { printf("   #%-7d %s\n", $e->id, $e->title); }
if (!$moving) { echo '   none; already migrated or none carried the value' . PHP_EOL; }

echo PHP_EOL . 'staying on book: none today. Carl Boyer\'s is the first candidate.' . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$f->options = $OPTIONS;
if (!$fs->saveField($f)) {
    echo 'FAILED saving the field: ' . implode('; ', $f->getFirstErrors()) . PHP_EOL;
    return;
}
echo PHP_EOL . 'options saved' . PHP_EOL;

/* Re-fetched AFTER the field is saved, so each entry carries the option list it
   is about to be validated against. */
$ids = array_map(fn($e) => $e->id, $moving);
$moved = 0;
foreach ($ids as $id) {
    $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
    if (!$e) { echo 'entry #' . $id . ' vanished between the read and the write' . PHP_EOL; continue; }
    $e->setFieldValue($HANDLE, $TO);
    if (\Craft::$app->elements->saveElement($e)) {
        $moved++;
    } else {
        echo 'FAILED #' . $id . ' ' . $e->title . ': '
           . implode('; ', array_merge(...array_values($e->getErrors()) ?: [[]])) . PHP_EOL;
    }
}

/* Read back from fresh queries: an element in memory reports what was set on
   it whether or not it reached the database. */
$back = $fs->getFieldByHandle($HANDLE);
$vals = array_column($back->options ?? [], 'value');
$still = 0; $now = 0;
foreach (\craft\elements\Entry::find()->section('collections')->status(null)->limit(null)->all() as $e) {
    $has = false;
    foreach ($e->getFieldLayout()->getCustomFields() as $c) { if ($c->handle === $HANDLE) { $has = true; } }
    if (!$has) { continue; }
    $v = $e->{$HANDLE}->value ?? '';
    if ($v === $FROM) { $still++; }
    if ($v === $TO) { $now++; }
}

echo PHP_EOL . 'READ-BACK' . PHP_EOL;
printf("   %-26s %-18s %s\n", 'entries moved', $moved . ' of ' . count($moving), $moved === count($moving) ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'now on ' . $TO, (string)$now, $now === count($moving) ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'left on ' . $FROM, (string)$still, $still === 0 ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'series in the options', in_array($TO, $vals, true) ? 'yes' : 'no', in_array($TO, $vals, true) ? 'pass' : 'FAIL');
printf("   %-26s %-18s %s\n", 'book kept in the options', in_array($FROM, $vals, true) ? 'yes' : 'no', in_array($FROM, $vals, true) ? 'pass' : 'FAIL');

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('rename_collection_kind_book_to_series.php', $moved,
    ($moved === count($moving) && $still === 0 && in_array($FROM, $vals, true) ? 'verified: ' : 'FAILED: ')
        . $moved . ' moved, ' . $still . ' left on book, options ' . count($vals),
    'book kept for published volumes');
echo PHP_EOL . 'config/project will be dirty. Commit it before deploying: see docs/DEPLOY.md step 1.' . PHP_EOL;
