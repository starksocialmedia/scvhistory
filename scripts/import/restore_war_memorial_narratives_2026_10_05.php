/**
 * Restores the eight war memorial narratives that parse_war_memorials.php cut to their first source line (silent-faults
 * audit, 5 October 2026, finding 3; Nathan: "a memorial record that stops mid-sentence is a broken record of a dead man").
 *
 * The cause: the legacy pages wrap the narrative across many source lines inside one HTML paragraph. The parser turned the
 * page into text without folding those line breaks, then read only the line that began "Narrative:"; its multi-line
 * fallback ran only when no such line was found, so it never ran. populate_war_memorials.php wrote that first line to both
 * wmNarrative and body. Four stop mid-sentence (#570 Ward, #552 Acuna, #546 Prosser, #516 Contreras); four lose their later
 * sentences or paragraphs (#566 Bartlett, #564 Redmond, #532 Flores-Mejia, #514 Kenaston).
 *
 * The full narrative, verbatim, is read from two places and must agree, or the record is refused:
 *   1. inventory/review/war-memorial-narratives-2026-10-05.json, written on the host from the Reggie mirror page by
 *      extract_war_memorial_narratives_2026_10_05.py (run it first);
 *   2. the record's own stored legacyHtml (the same legacy page), re-extracted here by the same rule.
 * The rule: the HTML after "<b>Narrative:</b>" up to the first block end after it; paragraphs are the page's <p> breaks;
 * source line breaks inside a paragraph are whitespace, folded to one space as a browser shows them; tags dropped (link
 * text kept); entities decoded to the characters they print. Leon Worden's words, punctuation and em dashes are kept as
 * printed: this script writes no text of its own, so the em-dash refusal applies to nothing here.
 *
 * Word-for-word check: the restored text's words must appear contiguously and in order in the mirror page's text (read
 * from /mnt/reggie when the container can see it, with the page's sha256 compared with the extractor's), and in the
 * stored legacyHtml's text. Either failing refuses the record.
 *
 * Refusals: a record whose restored text does not begin with the current truncated text (so nothing already there is
 * lost); a field (wmNarrative or body) whose current value is neither the truncated text nor already the restored text.
 * Each field is replaced only where it holds the truncated text. Paragraphs are separated by a blank line, which
 * _partials/prose.twig renders as paragraphs. wmAwards is not touched; the report lists award names the restored text
 * gives that wmAwards lacks, for Nathan.
 *
 * Idempotent: a field already holding the restored text is skipped, and a record with nothing to set is not saved.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/restore_war_memorial_narratives_2026_10_05.php'))"
 */
use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . PHP_EOL;

$root = \Craft::getAlias('@root');
$IDS = [570, 552, 546, 516, 566, 564, 532, 514];
$jsonPath = "$root/inventory/review/war-memorial-narratives-2026-10-05.json";
if (!file_exists($jsonPath)) { throw new \RuntimeException("$jsonPath missing: run extract_war_memorial_narratives_2026_10_05.py on the host first"); }
$J = [];
foreach (json_decode(file_get_contents($jsonPath), true) as $r) { $J[(int)$r['id']] = $r; }

/* The mirror, when the container can see it. When it cannot, that is said in the report, and the mirror check stands on
   the extractor's (same rule, run on the host against the mirror file whose sha256 the JSON records). */
$MIRROR = null; $mirrorNote = '';
try { $MIRROR = (require "$root/scripts/import/_reggie.php")('scvhistory.com'); if (!is_readable($MIRROR)) { throw new \RuntimeException('not readable'); } }
catch (\Throwable $t) { $MIRROR = null; $mirrorNote = 'The mirror is not visible inside the container (' . $t->getMessage() . '). The mirror check below is the extractor\'s, run on the host against the file whose sha256 is shown.'; }

$textOf = function (string $h): string {
    $h = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', ' ', $h);
    $h = preg_replace('#<[^>]+>#', ' ', $h);
    $h = html_entity_decode($h, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $h = str_replace("\xc2\xa0", ' ', $h);
    return trim(preg_replace('/\s+/u', ' ', $h));
};
$utf8 = fn(string $s): string => mb_check_encoding($s, 'UTF-8') ? $s : mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
$extract = function (string $page) use ($textOf): ?string {
    if (!preg_match('#<b>\s*Narrative:\s*</b>#i', $page, $m, PREG_OFFSET_CAPTURE)) { return null; }
    $rest = substr($page, $m[0][1] + strlen($m[0][0]));
    if (preg_match('#<div\s+style\s*=\s*"height:\s*20px|</div>|<hr\b|<b>\s*[A-Z][A-Za-z ]{1,30}:\s*</b>|<!--\s*XWP-END#i', $rest, $e, PREG_OFFSET_CAPTURE)) {
        $rest = substr($rest, 0, $e[0][1]);
    }
    $paras = array_values(array_filter(array_map($textOf, preg_split('#<p\b[^>]*>|</p>#i', $rest)), fn($p) => $p !== ''));
    return $paras ? implode("\n\n", $paras) : null;
};
$inOrder = function (string $needle, string $hay): bool {
    $n = preg_split('/\s+/u', trim($needle)); $h = preg_split('/\s+/u', trim($hay)); $c = count($n);
    for ($i = 0, $max = count($h) - $c; $i <= $max; $i++) { if ($h[$i] === $n[0] && array_slice($h, $i, $c) === $n) { return true; } }
    return false;
};
$awardTerms = ['Medal of Honor', 'Distinguished Service Cross', 'Navy Cross', 'Silver Star', 'Legion of Merit', 'Distinguished Flying Cross',
    'Bronze Star', 'Purple Heart', 'Air Medal', 'Combat Medical Badge', 'Combat Infantryman Badge', 'Commendation Medal', 'Good Conduct Medal',
    'Prisoner of War Medal', 'Achievement Medal', 'Croix de Guerre'];

echo "# War memorial narratives: dry run, 5 October 2026\n\n";
echo "Script: `scripts/import/restore_war_memorial_narratives_2026_10_05.php`. Source: the silent-faults audit, finding 3.\n\n";
if ($mirrorNote) { echo "$mirrorNote\n\n"; }

$plan = []; $refused = []; $done = 0;
foreach ($IDS as $id) {
    $e = Entry::find()->id($id)->section('warMemorials')->status(null)->one();
    if (!$e) { $refused[] = "#$id: no warMemorials entry"; continue; }
    $has = []; foreach ($e->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
    foreach (['wmNarrative', 'body', 'legacyHtml', 'sourcePath'] as $h) { if (!isset($has[$h])) { $refused[] = "#$id: no $h field in the layout"; continue 2; } }
    $j = $J[$id] ?? null;
    if (!$j) { $refused[] = "#$id: not in the extractor's JSON"; continue; }
    $sp = (string)$e->getFieldValue('sourcePath');
    echo "## #$id {$e->title}\n\n";
    echo "- Legacy page: `$sp` (legacyUrl `" . (string)$e->getFieldValue('legacyUrl') . "`)\n";
    if ($sp !== $j['sourcePath']) { $refused[] = "#$id: sourcePath $sp is not the extractor's {$j['sourcePath']}"; echo "- REFUSED: sourcePath differs from the extractor's\n\n"; continue; }

    $restored = $j['narrative'];
    $fromStored = $extract($utf8((string)$e->getFieldValue('legacyHtml')));
    $agree = $fromStored === $restored;

    if ($MIRROR !== null) {
        $raw = (string)@file_get_contents($MIRROR . '/' . explode('/', $sp, 2)[1]);
        $shaOk = hash('sha256', $raw) === $j['mirrorSha256'];
        $mirrorOk = $raw !== '' && $inOrder($restored, $textOf($utf8($raw))) && $extract($utf8($raw)) === $restored;
        $mirrorLine = 'read in the container: ' . ($mirrorOk ? 'yes' : 'NO') . '; sha256 matches the extractor\'s: ' . ($shaOk ? 'yes' : 'NO');
    } else {
        $shaOk = true; $mirrorOk = (bool)$j['inOrderInMirrorPage'];
        $mirrorLine = 'extractor on the host: ' . ($mirrorOk ? 'yes' : 'NO') . ' (sha256 ' . substr($j['mirrorSha256'], 0, 16) . '...)';
    }
    $storedOk = $inOrder($restored, $textOf($utf8((string)$e->getFieldValue('legacyHtml'))));

    $cur = ['wmNarrative' => (string)$e->getFieldValue('wmNarrative'), 'body' => (string)$e->getFieldValue('body')];
    $trunc = trim($cur['wmNarrative']) !== '' ? $cur['wmNarrative'] : $cur['body'];
    echo "\n**Current text** (" . mb_strlen($trunc) . " characters, wmNarrative and body " . ($cur['wmNarrative'] === $cur['body'] ? 'identical' : 'DIFFER') . "):\n\n> " . str_replace("\n", "\n> ", $trunc) . "\n\n";
    echo "**Restored text** (" . mb_strlen($restored) . " characters, " . count(explode("\n\n", $restored)) . " paragraph(s), " . count(preg_split('/\s+/u', $restored)) . " words):\n\n> " . str_replace("\n", "\n> ", $restored) . "\n\n";

    $starts = $trunc !== '' && str_starts_with($restored, rtrim($trunc));
    echo "**Checks**\n\n";
    echo "- Restored text in order, word for word, in the mirror page: $mirrorLine\n";
    echo "- Restored text in order, word for word, in the stored legacyHtml: " . ($storedOk ? 'yes' : 'NO') . "\n";
    echo "- Same narrative re-extracted from the stored legacyHtml: " . ($agree ? 'yes' : 'NO') . "\n";
    echo "- Restored text begins with the current text: " . ($starts ? 'yes' : 'NO') . "\n";
    echo "- Ends with closing punctuation: " . (preg_match('/[.!?"\x{201D})]$/u', $restored) ? 'yes' : 'NO') . "\n";

    $why = [];
    if (!$mirrorOk || !$shaOk) { $why[] = 'not found in order in the mirror page'; }
    if (!$storedOk) { $why[] = 'not found in order in the stored legacyHtml'; }
    if (!$agree) { $why[] = 'the stored legacyHtml gives a different narrative'; }
    if (!$starts) { $why[] = 'does not begin with the current text'; }
    $sets = [];
    foreach ($cur as $h => $v) {
        if ($v === $restored) { echo "- $h: already restored, skipped\n"; continue; }
        if ($v === $trunc || ($v !== '' && str_starts_with($restored, rtrim($v)))) { $sets[$h] = $restored; continue; }
        if ($v === '') { echo "- $h: empty, left empty\n"; continue; }
        $why[] = "$h holds other text";
    }
    if ($why) { $refused[] = "#$id {$e->title}: " . implode('; ', $why); echo "- **REFUSED**: " . implode('; ', $why) . "\n\n"; continue; }

    $aw = isset($has['wmAwards']) ? (string)$e->getFieldValue('wmAwards') : '';
    $named = array_values(array_filter($awardTerms, fn($t) => stripos($restored, $t) !== false));
    $missing = array_values(array_filter($named, fn($t) => stripos($aw, $t) === false));
    echo "- Would set: " . ($sets ? implode(', ', array_keys($sets)) : 'nothing') . "\n";
    echo "- wmAwards now: " . ($aw !== '' ? '"' . $aw . '"' : '(empty)') . ($missing ? '. The restored text also names: ' . implode(', ', $missing) . ' (a name in the text, not necessarily his award: not written; for Nathan)' : '') . "\n\n";
    if (!$sets) { continue; }
    $plan[$id] = [$e, $sets];
}

if ($APPLY) {
    foreach ($plan as $id => [$e, $sets]) {
        $e->setFieldValues($sets);
        if (!\Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); }
        $back = Entry::find()->id($id)->status(null)->one();
        foreach ($sets as $h => $v) { if ((string)$back->getFieldValue($h) !== $v) { throw new \RuntimeException("#$id $h did not read back"); } }
        $done++;
    }
    $applyLog = require "$root/scripts/import/_apply_log.php";
    $applyLog('restore_war_memorial_narratives_2026_10_05.php', $done, "read back $done of " . count($plan), 'war memorial narratives restored from the legacy pages');
}

echo "## Summary\n\n";
echo "- Records to restore: " . count($plan) . ($plan ? ' (' . implode(', ', array_map(fn($i) => "#$i", array_keys($plan))) . ')' : '') . "\n";
echo "- Refused: " . count($refused) . "\n";
foreach ($refused as $r) { echo "  - $r\n"; }
echo "- " . ($APPLY ? "Written: $done." : 'Nothing written.') . " A second run skips every field already restored.\n";
