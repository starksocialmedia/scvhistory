/**
 * The automatic decisions on the recordDates queue (Nathan, 2 October 2026:
 * "run the 43 confirmations and 48 rejections"; inventory/review/on-this-day-
 * 2026-10-02.md).
 *
 * CONFIRM a day row when the record already holds the same exact date in one
 * of its own date fields (birth, death, event, founding, establishment): the
 * record asserts it, so the row adds nothing to check. Not for a living person's
 * birth, which never goes on the calendar (historical.twig, failing closed).
 *
 * REJECT ("Not for the calendar") a day row that is:
 *   - a newspaper dateline or cutline: a paper's name, a bar, a weekday and
 *     the date ("The Newhall Signal and Saugus Enterprise | Thursday, July 19,
 *     1945"), which records when the paper ran, not what happened;
 *   - one date in a list of dates ("Cityhood Application 12/17/1985 Boundary
 *     Map 1/2/1986"); not on war memorial records, whose fact lines ("Start
 *     Tour: 11/17/1967 Incident Date: 02/25/1968") hold the date of death;
 *   - an article's own publication date, matched to originalPublishDateEdtf.
 *     These were counted among the 43 in the proposal, but a publication date
 *     is not an event: they reach On This Day on the separate "published on
 *     this day" line, from the field, so confirming them would list the same
 *     date twice, once mislabelled as an event.
 * Everything else stays in the queue for a person. Rows are kept, never
 * deleted, so the proposer does not offer them again.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/decide_record_dates.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$NOW = (int)date('Y');
$EVENT_FIELDS = ['birthDateEdtf', 'deathDateEdtf', 'eventDateEdtf', 'dateFoundedEdtf', 'dateDissolvedEdtf', 'dateEstablishedEdtf', 'electionDateEdtf', 'photoDateEdtf', 'termStartEdtf'];
$PAPER = '~\b(Signal|Enterprise|Times|Gazette|News|Mirror|Herald|Examiner|Express|Record|Mailer|Tribune)\b[^|.]{0,60}\|\s*(Mon|Tues|Wednes|Thurs|Fri|Satur|Sun)day,?\s*';
$parse = fn($p) => ($t = strtotime(preg_replace(['~^(on|in)\s+~i', '~\b(Sept)\.?~i', '~(\d)-(\d)~'], ['', 'Sep', '$1/$2'], trim((string)$p)))) ? date('Y-m-d', $t) : null;
$plan = []; $n = ['confirm' => 0, 'reject: dateline' => 0, 'reject: list' => 0, 'reject: publication date' => 0];
foreach (Entry::find()->status(null)->all() as $e) {
    $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    if (!in_array('recordDates', $h)) { continue; }
    $get = function ($k) use ($e, $h) { if (!in_array($k, $h)) { return ''; } try { return trim((string)$e->getFieldValue($k)); } catch (\Throwable $t) { return ''; } };
    $dead = preg_match('~\d{3}~', $get('deathDate') . $get('deathDateEdtf') . $get('mpDateOfDeath')) || $e->section->handle === 'warMemorials';
    $by = preg_match('~\b(1[6-9]\d\d|20\d\d)\b~', $get('birthDateEdtf') . ' ' . $get('birthDate'), $m) ? (int)$m[1] : null;
    $historical = $dead || ($by && $by < $NOW - 120) || ($e->section->handle !== 'persons' && $e->section->handle !== 'militaryProfiles');
    $exact = []; foreach ($EVENT_FIELDS as $k) { $v = $get($k); if (preg_match('~^\d{4}-\d{2}-\d{2}$~', $v)) { $exact[$v] = $k; } }
    $pub = $get('originalPublishDateEdtf');
    $rows = array_values(array_filter($e->recordDates ?? [], 'is_array')); $changed = false;
    foreach ($rows as $i => $r) {
        if (($r['granularity'] ?? '') !== 'day' || !empty($r['confirmed']) || !empty($r['rejected'])) { continue; }
        $d = $parse($r['printed'] ?? ''); $label = (string)($r['label'] ?? ''); $printed = trim((string)($r['printed'] ?? ''));
        $why = null;
        if ($d && isset($exact[$d]) && !($exact[$d] === 'birthDateEdtf' && !$historical)) { $why = 'confirm'; }
        elseif ($d && $pub === $d) { $why = 'reject: publication date'; }
        elseif (preg_match($PAPER . preg_quote($printed, '~') . '~u', $label)) { $why = 'reject: dateline'; }
        elseif ($e->section->handle !== 'warMemorials' && preg_match('~^\d{1,2}[/-]\d{1,2}[/-]\d{2,4}$~', $printed) && preg_match_all('~\b\d{1,2}[/-]\d{1,2}[/-]\d{2,4}\b~', $label) >= 2) { $why = 'reject: list'; }
        if (!$why) { continue; }
        $n[$why]++; $changed = true;
        if ($why === 'confirm') { $rows[$i]['confirmed'] = true; } else { $rows[$i]['rejected'] = true; }
        echo str_pad($why, 26) . "#{$e->id} " . str_pad($printed, 20) . ($why === 'confirm' ? "= {$exact[$d]}" : '') . ' :: ' . mb_substr(preg_replace('~\s+~', ' ', $label), 0, 90) . PHP_EOL;
    }
    if ($changed) { $plan[$e->id] = $rows; }
}
echo PHP_EOL . json_encode($n) . ' across ' . count($plan) . ' records' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => $rows) {
    $e = Entry::find()->id($id)->status(null)->one();
    /* Plain keys, so the save does not keep stale colN values; iso goes back as read. */
    $e->setFieldValue('recordDates', array_map(fn($r) => ['printed' => $r['printed'] ?? '', 'iso' => $r['iso'] ?? null, 'granularity' => $r['granularity'] ?? '', 'label' => $r['label'] ?? '', 'confirmed' => (bool)($r['confirmed'] ?? false), 'rejected' => (bool)($r['rejected'] ?? false)], $rows));
    if (!Craft::$app->getElements()->saveElement($e)) { $short[] = "#$id"; continue; }
    $back = Entry::find()->id($id)->status(null)->one()->recordDates ?? [];
    $isoOf = fn($x) => ($x['iso'] ?? null) instanceof \DateTimeInterface ? $x['iso']->format('Y-m-d') : (string)($x['iso'] ?? '');
    foreach ($rows as $i => $r) { if (!isset($back[$i]) || (bool)($back[$i]['confirmed'] ?? false) !== (bool)($r['confirmed'] ?? false) || (bool)($back[$i]['rejected'] ?? false) !== (bool)($r['rejected'] ?? false) || $isoOf($back[$i]) !== $isoOf($r)) { $short[] = "#$id row $i"; break; } }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records, dates unmoved') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('decide_record_dates.php', array_sum($n), $short ? 'SHORT' : 'verified', 'recordDates: ' . json_encode($n));
if ($short) { throw new \RuntimeException('decide_record_dates: ' . implode(', ', $short)); }
