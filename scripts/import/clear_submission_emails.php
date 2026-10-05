/**
 * Clears the sender's email address from submissions decided more than a year ago (the photo submission proposal,
 * approved 4 October 2026: addresses are kept only until the submission is decided, plus a year; the credit line the
 * sender chose stays). The decision date is the last "YYYY-MM-DD:" line in submissionDecision, written by
 * modules/submissions when the archivist accepts or declines; spam is cleared when it is marked.
 * Run it every few months, or before a staging refresh. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/clear_submission_emails.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$cut = (new DateTime('-1 year'))->format('Y-m-d'); $n = 0; $due = [];
foreach (Entry::find()->section('submissions')->status(null)->all() as $e) {
    if ((string)$e->submissionEmail === '' || $e->submissionStatus->value === 'new') { continue; }
    preg_match_all('~^(\d{4}-\d{2}-\d{2}):~m', (string)$e->submissionDecision, $m);
    $last = end($m[1]) ?: null;
    if ($last && $last < $cut) { $due[] = $e; }
}
echo count($due) . " addresses due to clear (decided before $cut)" . PHP_EOL;
if (!$APPLY) { return; }
foreach ($due as $e) { $e->setFieldValue('submissionEmail', ''); if (Craft::$app->getElements()->saveElement($e)) { $n++; } }
if ($n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('clear_submission_emails.php', $n, 'verified', 'submission addresses cleared a year after the decision'); }
echo "done: $n cleared" . PHP_EOL;
