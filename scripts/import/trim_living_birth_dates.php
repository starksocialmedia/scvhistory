/**
 * Living people carry a year of birth at most (Nathan, 2 October 2026: "cut
 * Scott Wilk's birth date to the year. He is living and the rule holds whoever
 * created the record. Check every living person for the same").
 *
 * WHO IS LIVING: the test in templates/_partials/record/historical.twig, which
 * fails closed. A person is historical only with a death date, an obituary, a
 * war memorial record, or a birth more than 120 years ago; everyone else is
 * treated as living. Persons and military profiles are checked.
 *
 * WHAT IS CUT, for each living or undetermined person:
 *   birthDate, birthDateEdtf, mpDateOfBirth  finer than a year -> the year
 *   recordDates rows that date the birth to a day or month -> removed (the
 *       birth year stays in the birth fields; these rows are machine-extracted
 *       sentence fragments, none confirmed)
 *   body: "born on <Month D, YYYY>" -> "born in YYYY", in records that are not
 *       legacy prose (a legacy record's text would be held and listed instead)
 * The sweep of 2 October found two: Wilk (#335) and Cameron Smyth (#16380).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/trim_living_birth_dates.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$NOW = (int)date('Y');
$MON = '(?:January|February|March|April|May|June|July|August|September|October|November|December|Jan\.?|Feb\.?|Aug\.?|Sept?\.?|Oct\.?|Nov\.?|Dec\.?)';
$BORN = '~\bborn(\s+on)?\s+' . $MON . '\s+\d{1,2},?\s+(\d{4})~u';
$plan = []; $held = [];
foreach (Entry::find()->section(['persons', 'militaryProfiles'])->status(null)->all() as $e) {
    $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    $g = function ($k) use ($e, $h) { try { return in_array($k, $h) ? trim((string)$e->getFieldValue($k)) : ''; } catch (\Throwable $t) { return ''; } };
    /* historical.twig, in PHP: any one of these makes a person historical. */
    if (preg_match('~\d{3}~', $g('deathDate') . $g('deathDateEdtf') . $g('mpDateOfDeath'))) { continue; }
    if (in_array('personObituaries', $h) && $e->personObituaries->exists()) { continue; }
    if (Entry::find()->section('obituaries')->status(null)->relatedTo(['targetElement' => $e, 'field' => 'obitSubject'])->exists()) { continue; }
    $by = null; foreach (['birthDateEdtf', 'birthDate', 'mpDateOfBirth'] as $k) { if (preg_match('~\b(1[6-9]\d\d|20\d\d)\b~', $g($k), $m)) { $by = $m[1]; break; } }
    if ($by && (int)$by < $NOW - 120) { continue; }
    $set = []; $what = [];
    foreach (['birthDate', 'birthDateEdtf', 'mpDateOfBirth'] as $k) {
        $v = $g($k);
        if ($v !== '' && !preg_match('~^\D*\d{4}\??\D*$~', $v) && preg_match('~\b(1[6-9]\d\d|20\d\d)\b~', $v, $m)) { $set[$k] = $m[1]; $what[] = "$k \"$v\" -> \"{$m[1]}\""; }
    }
    if (in_array('recordDates', $h)) {
        $rows = array_values(array_filter($e->recordDates ?? [], 'is_array'));
        $keep = array_values(array_filter($rows, fn($r) => !(in_array($r['granularity'] ?? '', ['day', 'month']) && preg_match('~\bborn\b[^.]{0,40}' . preg_quote((string)($r['printed'] ?? '#'), '~') . '~i', (string)($r['label'] ?? '')))));
        if (count($keep) !== count($rows)) {
            /* Rebuilt with plain keys, so the save does not keep the old colN values. */
            $set['recordDates'] = array_map(fn($r) => ['printed' => $r['printed'] ?? '', 'iso' => $r['iso'] ?? null, 'granularity' => $r['granularity'] ?? '', 'label' => $r['label'] ?? '', 'confirmed' => (bool)($r['confirmed'] ?? false)], $keep);
            $what[] = (count($rows) - count($keep)) . ' recordDates birth row(s) removed';
        }
    }
    $body = (string)$g('body');
    if ($body !== '' && preg_match($BORN, $body)) {
        if ($g('legacyUrl') !== '' || $g('legacyKey') !== '') { $held[] = "#{$e->id} {$e->title}: legacy prose carries a full birth date"; }
        else { $set['body'] = preg_replace($BORN, 'born in $2', $body); $what[] = 'body "born on <date>" -> "born in <year>"'; }
    }
    if ($set) { $plan[$e->id] = $set; echo "#{$e->id} {$e->title}" . PHP_EOL . '    ' . implode(PHP_EOL . '    ', $what) . PHP_EOL; }
}
echo PHP_EOL . count($plan) . ' living or undetermined records to trim' . PHP_EOL . 'HELD (legacy prose, not rewritten): ' . ($held ? implode(' | ', $held) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => $set) {
    $e = Entry::find()->id($id)->status(null)->one(); $e->setFieldValues($set);
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id save"; continue; }
    $b = Entry::find()->id($id)->status(null)->one();
    foreach (['birthDate', 'birthDateEdtf', 'mpDateOfBirth'] as $k) { if (isset($set[$k]) && trim((string)$b->getFieldValue($k)) !== $set[$k]) { $short[] = "#$id $k"; } }
    if (isset($set['body']) && preg_match($BORN, (string)$b->body)) { $short[] = "#$id body"; }
    if (isset($set['recordDates']) && count($b->recordDates ?? []) !== count($set['recordDates'])) { $short[] = "#$id recordDates"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records trimmed to a birth year') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('trim_living_birth_dates.php', count($plan), $short ? 'SHORT' : 'verified', 'living people: birth dates cut to the year in fields, recordDates and body');
if ($short) { throw new \RuntimeException('trim_living_birth_dates: ' . implode(', ', $short)); }
