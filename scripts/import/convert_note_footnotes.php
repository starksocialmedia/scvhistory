/**
 * Webmaster notes that are really a list of footnotes.
 *
 * Found while restyling record/note.twig: winifred-westover-hoover-art-co
 * -photograph-1918-1919 has a webmasterNoteBottom reading "1. York (Penn.)
 * Dispatch, July 3, 1900; 2. Los Angeles Times, December 12, 1946; ..." That is
 * not a note about the record, it is the record's citations in the wrong field.
 *
 * It is one record of 77. I reported it as a fifth source of footnotes and it
 * is a single instance, which is worth saying plainly rather than leaving the
 * impression of a pattern. This exists because the same shape will turn up
 * again as more of the archive imports, and because a converter is cheaper to
 * write now than to remember later.
 *
 * A note counts as a list only when it opens at 1, is followed by 2, and has at
 * least two items. "In 1901. the depot opened" does not qualify and neither
 * does a note that merely contains a numbered reference part way through.
 *
 * The note is split on the numbers, each item becomes a footnotes row, and the
 * source field is cleared. Nothing is written where the record already has
 * footnotes rows, so a second run is a no-op and a hand-edited record is safe.
 *
 * It does not add markers to the prose. The body has no [N] to anchor these to,
 * and inventing marker positions would be guessing at where the author cited
 * them. The notes render as the numbered list at the foot, which is where they
 * already were, in a field that means it.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/convert_note_footnotes.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLES = ['webmasterNoteBottom', 'webmasterNoteTop', 'personWebmasterNoteBottom',
            'obitWebmasterNoteBottom', 'mpWebmasterNoteBottom', 'wmNotes'];

$REPORT = \Craft::getAlias('@webroot') . '/review/note-footnotes.md';
$elements = Craft::$app->getElements();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

$plan = []; $seen = 0;

foreach (\craft\elements\Entry::find()->limit(null)->status(null)->all() as $e) {
    $layout = $e->getFieldLayout();
    if (!$layout) { continue; }
    $handles = [];
    foreach ($layout->getCustomFields() as $f) { $handles[] = $f->handle; }
    if (!in_array('footnotes', $handles, true)) { continue; }

    foreach ($layout->getCustomFields() as $f) {
        if (!in_array($f->handle, $HANDLES, true)) { continue; }
        $v = trim((string)$e->getFieldValue($f->handle));
        if ($v === '') { continue; }
        $seen++;

        /* Split on a number that opens an item: at the start, or after a
           semicolon, a newline or a sentence end. */
        $parts = preg_split('/(?:^|[;\n]|(?<=\.)\s)\s*(\d{1,3})[.)]\s+/', $v, -1,
                            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $rows = []; $expect = 1;
        for ($i = 0; $i < count($parts) - 1; $i++) {
            if (!preg_match('/^\d{1,3}$/', $parts[$i])) { continue; }
            if ((int)$parts[$i] !== $expect) { continue; }
            $text = trim($parts[$i + 1], " \t\n;.");
            if ($text === '') { continue; }
            $rows[] = ['number' => (string)$expect, 'note' => $text];
            $expect++;
        }
        if (count($rows) < 2) { continue; }

        $existing = $e->getFieldValue('footnotes');
        if (is_array($existing) && count($existing)) {
            echo $e->slug . ': already has footnotes rows, left alone' . PHP_EOL;
            continue;
        }
        $plan[] = ['e' => $e, 'field' => $f->handle, 'rows' => $rows, 'was' => $v];
    }
}

echo 'note fields with text: ' . $seen . PHP_EOL;
echo 'that are a numbered list: ' . count($plan) . PHP_EOL;
echo 'notes to write: ' . array_sum(array_map(fn($p) => count($p['rows']), $plan)) . PHP_EOL;

$written = 0; $failed = [];
foreach ($plan as $p) {
    echo PHP_EOL . $p['e']->slug . '  [' . $p['field'] . ']  ' . count($p['rows']) . ' notes' . PHP_EOL;
    foreach ($p['rows'] as $r) {
        echo '   [' . $r['number'] . '] ' . mb_substr(preg_replace('/\s+/', ' ', $r['note']), 0, 88) . PHP_EOL;
    }
    if ($APPLY) {
        $p['e']->setFieldValue('footnotes', $p['rows']);
        $p['e']->setFieldValue($p['field'], '');
        if ($elements->saveElement($p['e'])) { $written++; }
        else { $failed[] = $p['e']->slug . ': ' . json_encode($p['e']->getErrors()); }
    }
}
if ($APPLY) {
    echo PHP_EOL . 'saved: ' . $written . PHP_EOL;

/* ------------------------------------------------- read the writes back -----
 *
 * A save that reports success and changes nothing is worse than one that fails,
 * because the counter says the work is done. add_footnote_source_column.php
 * printed "saved: 5" on every run and persisted nothing for two runs: it wrote a
 * Table row keyed by handle when Craft stores it keyed by column, so the value
 * was discarded at serialisation and the element still reported success.
 *
 * So every script that saves now reads its own writes back from a freshly
 * loaded element and fails loudly when the count does not match. */
    $back = 0; $short = [];
    foreach ($plan as $p) {
        $fresh = \craft\elements\Entry::find()->id($p['e']->id)->status(null)->one();
        if (!$fresh) { $short[] = $p['e']->slug . ': gone after save'; continue; }
        $got = $fresh->getFieldValue('footnotes');
        $n = is_array($got) ? count($got) : 0;
        if ($n !== count($p['rows'])) { $short[] = $p['e']->slug . ': wrote ' . count($p['rows']) . ' rows, read back ' . $n; }
        if (trim((string)$fresh->getFieldValue($p['field'])) !== '') {
            $short[] = $p['e']->slug . ': ' . $p['field'] . ' was not cleared';
        }
        $back += $n;
    }
    echo 'read back: ' . $back . ' footnote rows' . PHP_EOL;
    if ($short) {
        echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
        foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }
        echo 'Do not re-run until this is understood.' . PHP_EOL;
        return;
    }
    echo 'verified.' . PHP_EOL;
}
foreach ($failed as $f) { echo 'FAILED ' . $f . PHP_EOL; }

$out = ['# Webmaster notes that are footnote lists', '',
        'Generated by scripts/import/convert_note_footnotes.php on ' . date('Y-m-d H:i'),
        ($APPLY ? 'Mode: APPLIED' : 'Mode: DRY RUN, nothing written') . '.', '',
        '| | |', '|---|---:|',
        '| note fields carrying text | ' . $seen . ' |',
        '| that are a numbered list | ' . count($plan) . ' |',
        '| notes to write | ' . array_sum(array_map(fn($p) => count($p['rows']), $plan)) . ' |', ''];
foreach ($plan as $p) {
    $out[] = '## ' . $p['e']->title;
    $out[] = '';
    $out[] = '- ' . $p['e']->url;
    $out[] = '- field `' . $p['field'] . '`, cleared after conversion';
    $out[] = '';
    foreach ($p['rows'] as $r) { $out[] = '**[' . $r['number'] . ']** ' . preg_replace('/\s+/', ' ', $r['note']); $out[] = ''; }
}
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
