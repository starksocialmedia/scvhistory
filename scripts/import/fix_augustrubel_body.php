/**
 * ww2-augustrubel #518: the one record whose body is furniture.
 *
 * Every other war memorial carries narrative prose in body. This one carries
 * the page title, then the site navigation, then the service-record labels that
 * already live in their own fields. Its narrative sits in wmNotes, which only
 * nine of the fifty-four records use at all.
 *
 * So this is not a cleaning problem and the cleaner cannot fix it. Strip the
 * navigation and the service-record labels remain; strip those and nothing is
 * left. The body has to be set to the narrative, which the record already holds.
 *
 * wmNotes is left alone rather than cleared. Nine records carry both, so both is
 * within the convention, and deleting a field of somebody's text to tidy a
 * duplicate is not a call for a script.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_augustrubel_body.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$ID = 518;
$MARK = 'WAR MEMORIAL HOME';

$e = \craft\elements\Entry::find()->id($ID)->status(null)->one();
if (!$e) { echo 'entry #' . $ID . ' not found' . PHP_EOL; return; }

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo $e->title . '  #' . $e->id . '  ' . $e->slug . PHP_EOL;

$body = (string)$e->getFieldValue('body');
$notes = (string)$e->getFieldValue('wmNotes');

if (!str_contains($body, $MARK)) {
    echo PHP_EOL . 'The body no longer carries the navigation trail, so this has already been' . PHP_EOL;
    echo 'fixed or the record has changed. Nothing to do.' . PHP_EOL;
    return;
}
if (trim($notes) === '') {
    echo PHP_EOL . 'wmNotes is empty, so there is no narrative to put in the body. Stopping' . PHP_EOL;
    echo 'rather than guessing: the text is in warmemorial.json under ww2_augustrubel' . PHP_EOL;
    echo 'and a person should choose it.' . PHP_EOL;
    return;
}

$flat = fn(string $s) => mb_substr(preg_replace('~\s+~', ' ', $s), 0, 108);
echo PHP_EOL . 'body now    ' . strlen($body) . ' chars   ' . $flat($body) . PHP_EOL;
echo 'body after  ' . strlen($notes) . ' chars   ' . $flat($notes) . PHP_EOL;
echo 'wmNotes     unchanged, ' . strlen($notes) . ' chars' . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$e->setFieldValue('body', $notes);
echo PHP_EOL . (Craft::$app->getElements()->saveElement($e)
    ? 'saved #' . $e->id
    : 'FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
