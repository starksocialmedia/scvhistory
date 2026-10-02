/**
 * Adds a sixth column to recordDates: "Not for the calendar" (handle
 * rejected), a checkbox (Nathan, 2 October 2026, approving the automatic
 * rejections in inventory/review/on-this-day-2026-10-02.md).
 *
 * Why a column and not a deletion: apply_confirmed_dates.php deletes a row it
 * is told to, and propose_record_dates.php skips only dates already present,
 * so a deleted row comes back on the next proposal run. A row marked rejected
 * stays, is skipped by the builder and the review export, and is never
 * proposed again. Existing rows read the new column as unticked.
 *
 * Schema only. Run in its own request, before decide_record_dates.php.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_record_dates_rejected.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fields = Craft::$app->getFields();
$f = $fields->getFieldByHandle('recordDates');
if (!$f instanceof \craft\fields\Table) { echo 'recordDates is missing or not a table; nothing to do' . PHP_EOL; return; }
$cols = $f->columns;
$have = array_filter($cols, fn($c) => ($c['handle'] ?? '') === 'rejected');
echo 'columns now: ' . implode(', ', array_map(fn($c) => $c['handle'], $cols)) . PHP_EOL;
if ($have) { echo 'rejected: already there, nothing to do' . PHP_EOL; return; }
echo 'would add col6: "Not for the calendar" (rejected), checkbox' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$cols['col6'] = ['heading' => 'Not for the calendar', 'handle' => 'rejected', 'width' => '', 'type' => 'checkbox'];
$f->columns = $cols;
$f->instructions = 'Dates this record is about, for the on-this-day index. Enter the date as it is printed in the source, then the ISO date and how precise it is. Tick Confirmed once a person has checked the row; tick Not for the calendar for a date that is not an event (a dateline, a list) so it is never proposed again.';
if (!$fields->saveField($f)) { throw new \RuntimeException('recordDates: ' . json_encode($f->getErrors())); }
$back = $fields->getFieldByHandle('recordDates');
$ok = (bool)array_filter($back->columns, fn($c) => ($c['handle'] ?? '') === 'rejected');
echo 'READ-BACK ' . ($ok ? 'OK: rejected column present' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_record_dates_rejected.php', 1, $ok ? 'verified' : 'SHORT', 'recordDates: "Not for the calendar" column');
if (!$ok) { throw new \RuntimeException('add_record_dates_rejected: read-back failed'); }
