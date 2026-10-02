/**
 * READ ONLY. Dates a century out, and other impossible dates (Nathan,
 * 2 October 2026: "The Signal founding date reading 2019 for 1919 is the kind
 * of error that reads as current and is a century out. Check every date in the
 * archive for the same transposition shape").
 *
 * The Signal's record had no text to contradict its "2019"; what gave it away
 * was everything else the archive holds about the paper. So the checks look
 * across records as well as within one:
 *
 *   BEFORE IT BEGAN  a photograph, article, document or event related to a
 *                    record is dated (by its own date field) more than a year
 *                    before that record's founding, establishment or birth.
 *   CENTURY          a date field's year does not appear in the record's text,
 *                    but the year exactly 100 earlier or later does.
 *   ORDER            death before birth; dissolved before founded; a term that
 *                    ends before it starts; a memorial age at loss more than a
 *                    year off the birth and death years.
 *   PERIOD           a founding, establishment, event or photograph year
 *                    lies about a century from the record's own period or era
 *                    category (the Signal's "2019" against its 1910s period).
 *   MISMATCH         a printed date and its EDTF field disagree on the year.
 *   FUTURE           a date later than this year.
 * Writes inventory/review/date-century-audit-2026-10-02.md.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_date_centuries.php'))"
 */

use craft\elements\Entry;

ini_set('memory_limit', '2048M');
$NOW = (int)date('Y');
$PAIRS = [['birthDate', 'birthDateEdtf', 'born'], ['deathDate', 'deathDateEdtf', 'died'], ['dateFounded', 'dateFoundedEdtf', 'founded'], ['dateEstablished', 'dateEstablishedEdtf', 'established'], ['dateDissolved', 'dateDissolvedEdtf', 'dissolved'], ['eventDate', 'eventDateEdtf', 'event'], ['photoDate', 'photoDateEdtf', 'photographed'], ['originalPublishDate', 'originalPublishDateEdtf', 'published'], ['termStart', 'termStartEdtf', 'term began'], ['termEnd', 'termEndEdtf', 'term ended'], ['electionDate', 'electionDateEdtf', 'election'], ['wmDateOfBirth', null, 'born']];
$Y = fn($s) => preg_match('~\b(1[5-9]\d\d|20\d\d)\b~', (string)$s, $m) ? (int)$m[1] : null;
$found = ['PERIOD' => [], 'BEFORE IT BEGAN' => [], 'CENTURY' => [], 'ORDER' => [], 'MISMATCH' => [], 'FUTURE' => []];
$startOf = [];
foreach (Entry::find()->status(null)->each(200) as $e) {
    $h = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[$f->handle] = true; }
    $g = function ($k) use ($e, $h) { if (!$k || !isset($h[$k])) { return ''; } try { $v = $e->getFieldValue($k); return $v instanceof \DateTimeInterface ? $v->format('Y-m-d') : trim((string)$v); } catch (\Throwable $t) { return ''; } };
    $label = "{$e->section->handle} #{$e->id} {$e->title}";
    $text = preg_replace('~\s+~u', ' ', strip_tags($g('body') . ' ' . $g('wmNarrative') . ' ' . $g('authorBio')));
    $years = [];
    foreach ($PAIRS as [$p, $x, $what]) {
        $py = $Y($g($p)); $xy = $Y($g($x)); $yr = $xy ?? $py;
        if ($py && $xy && $py !== $xy) { $found['MISMATCH'][] = "$label: $what printed \"" . $g($p) . "\", EDTF \"" . $g($x) . '"'; }
        if ($yr && $yr > $NOW) { $found['FUTURE'][] = "$label: $what $yr"; }
        if ($yr && $text !== '' && !str_contains($text, (string)$yr)) {
            foreach ([$yr - 100, $yr + 100] as $alt) { if ($alt >= 1500 && $alt <= $NOW && preg_match('~\b' . $alt . '\b~', $text)) { $found['CENTURY'][] = "$label: $what $yr; the text has $alt and not $yr"; break; } }
        }
        if ($yr) { $years[$what] = $yr; }
    }
    /* The record's own period and era categories, as year ranges. */
    $ranges = [];
    foreach (['historicalPeriod', 'historicalEra'] as $ch) { if (!isset($h[$ch])) { continue; } foreach ($e->getFieldValue($ch)->all() as $c) { if (preg_match('~(\d{4})\D+(\d{4}|[Pp]resent)~', $c->title, $m)) { $ranges[] = [(int)$m[1], strtolower($m[2]) === 'present' ? $NOW : (int)$m[2], $c->title]; } } }
    /* Not births: a person is filed by the era of their acts, which can come a lifetime after their birth (DATA-MODEL, Eras). */
    foreach (['founded', 'established', 'event', 'photographed'] as $k) {
        if (!isset($years[$k]) || !$ranges) { continue; }
        $in = array_filter($ranges, fn($r) => $years[$k] >= $r[0] - 5 && $years[$k] <= $r[1] + 5);
        $off = array_filter($ranges, fn($r) => ($years[$k] - 100 >= $r[0] - 5 && $years[$k] - 100 <= $r[1] + 5) || ($years[$k] + 100 >= $r[0] - 5 && $years[$k] + 100 <= $r[1] + 5));
        if (!$in && $off) { $found['PERIOD'][] = "$label: $k {$years[$k]}, but filed under " . implode(', ', array_map(fn($r) => $r[2], $off)); }
    }
    if (isset($years['born'], $years['died']) && $years['died'] < $years['born']) { $found['ORDER'][] = "$label: died {$years['died']} before born {$years['born']}"; }
    if (isset($years['founded'], $years['dissolved']) && $years['dissolved'] < $years['founded']) { $found['ORDER'][] = "$label: dissolved {$years['dissolved']} before founded {$years['founded']}"; }
    if (isset($years['term began'], $years['term ended']) && $years['term ended'] < $years['term began']) { $found['ORDER'][] = "$label: term ended {$years['term ended']} before it began {$years['term began']}"; }
    if ($e->section->handle === 'warMemorials' && isset($years['born']) && ($dy = $Y($g('deathDate')) ?? $Y($g('wmIncidentDate')))) {
        $age = (int)$g('wmAgeAtLoss');
        if ($age && abs(($dy - $years['born']) - $age) > 1) { $found['ORDER'][] = "$label: age at loss $age, but born {$years['born']} and died $dy"; }
    }
    foreach (['founded', 'established', 'born'] as $k) { if (isset($years[$k])) { $startOf[$e->id] = [$years[$k], $k, $label]; break; } }
}
/* BEFORE IT BEGAN: dated items related to a record, earlier than its start. */
foreach ($startOf as $id => [$start, $what, $label]) {
    $early = [];
    foreach (Entry::find()->section(['photographs', 'articles', 'documents', 'events'])->status(null)->relatedTo($id)->all() as $r) {
        $rl = []; foreach ($r->getFieldLayout()->getCustomFields() as $f) { $rl[$f->handle] = true; }
        foreach (['photoDateEdtf', 'originalPublishDateEdtf', 'eventDateEdtf'] as $k) {
            if (!isset($rl[$k])) { continue; }
            $ry = $Y($r->getFieldValue($k));
            if ($ry && $ry < $start - 1) { $early[] = "{$r->section->handle} #{$r->id} ($ry)"; }
            break;
        }
    }
    /* A person's record relates photographs of their parents and town before they were born; a body cannot be written about before it began.
       So for people only a long gap counts. */
    if ($early && ($what !== 'born' || count($early) >= 3)) { $found['BEFORE IT BEGAN'][] = "$label: $what $start; " . count($early) . ' related item(s) dated earlier: ' . implode(', ', array_slice($early, 0, 5)); }
}
$md = ['# Dates a century out, and other impossible dates, 2 October 2026', '', 'Generated by scripts/import/audit_date_centuries.php (read only). The checks are in its header. A line here is a lead to read, not a verdict: an item about a person dated before their birth can be a photograph of their family.', ''];
foreach ($found as $k => $rows) { $md[] = "## $k (" . count($rows) . ')'; $md[] = ''; foreach ($rows as $r) { $md[] = "- $r"; } if (!$rows) { $md[] = 'None.'; } $md[] = ''; }
file_put_contents(\Craft::getAlias('@root') . '/inventory/review/date-century-audit-2026-10-02.md', implode(PHP_EOL, $md) . PHP_EOL);
foreach ($found as $k => $rows) { echo str_pad($k, 18) . count($rows) . PHP_EOL; }
echo 'wrote inventory/review/date-century-audit-2026-10-02.md' . PHP_EOL;
