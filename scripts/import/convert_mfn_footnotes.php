/**
 * Turns the [mfn] shortcode blocks into footnotes rows.
 *
 * [mfn]the note text[/mfn] is the WordPress Modern Footnotes plugin, used on
 * the previous site. The shortcode survived the migration, so the note text is
 * sitting in the middle of the prose as literal characters and reading as part
 * of the sentence it was meant to annotate.
 *
 * 7 blocks on 4 records, all balanced, none carrying shortcode attributes, and
 * none of the four also carrying a bare [N] marker. That last point is what
 * makes the numbering safe: the notes are numbered from 1 in document order and
 * nothing already claims those numbers. A record where both appear is reported
 * and skipped rather than renumbered, because deciding whether [3] and the
 * third shortcode are the same note is not something a script should guess.
 *
 * For each block the note text moves to a footnotes row and the block is
 * replaced, in place, by [N]. In place matters: the marker has to end up where
 * the shortcode was, which is where the author put it, and not at the end of
 * the sentence or the paragraph.
 *
 * Anything malformed is reported rather than repaired:
 *   unclosed        [mfn] with no [/mfn] after it
 *   unopened        [/mfn] with nothing opening it
 *   empty           a block with no text in it
 *   mixed           the record also carries bare [N] markers
 *   attributes      the shortcode carries attributes we have not seen before
 *
 * The rendering does not depend on this having been run. prose.twig reads the
 * shortcode directly and numbers it the same way, so a record looks the same
 * before and after. What this buys is the note in a field where it can be
 * edited, searched and cited, rather than in the middle of a sentence.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/convert_mfn_footnotes.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$REPORT = \Craft::getAlias('@review') . '/mfn-footnotes.md';

$OPEN   = '/\[mfn\b([^\]]*)\]/i';
$MARKER = '/(?<![A-Za-z0-9])\[\s*\d{1,3}\s*\](?!\d)/';

$elements = Craft::$app->getElements();

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

$plan = []; $problems = []; $blocks = 0; $scanned = 0;

foreach (\craft\elements\Entry::find()->limit(null)->status(null)->all() as $e) {
    $layout = $e->getFieldLayout();
    if (!$layout) { continue; }

    foreach ($layout->getCustomFields() as $f) {
        if (!($f instanceof \craft\fields\PlainText)) { continue; }
        $body = $e->getFieldValue($f->handle);
        if (!is_string($body) || stripos($body, '[mfn') === false) { continue; }
        $scanned++;

        $opens  = preg_match_all($OPEN, $body, $om);
        $closes = preg_match_all('/\[\/mfn\]/i', $body);

        if ($opens !== $closes) {
            $problems[] = ['e' => $e, 'field' => $f->handle,
                           'why' => ($opens > $closes ? 'unclosed' : 'unopened')
                                  . ': ' . $opens . ' opening and ' . $closes . ' closing'];
            continue;
        }

        foreach ($om[1] as $attr) {
            if (trim($attr) !== '') {
                $problems[] = ['e' => $e, 'field' => $f->handle,
                               'why' => 'the shortcode carries an attribute: ' . trim($attr)];
                continue 2;
            }
        }

        if (preg_match($MARKER, $body)) {
            $problems[] = ['e' => $e, 'field' => $f->handle,
                           'why' => 'the record also carries bare [N] markers, so numbering from 1 '
                                  . 'would collide. Left alone for a person to decide.'];
            continue;
        }

        /* Rebuilt rather than preg_replace_callback, because the replacement
           number depends on how many blocks came before it and the text has to
           be reassembled in order anyway. */
        $out = ''; $rest = $body; $rows = []; $n = 0; $bad = false;
        while (($p = preg_match($OPEN, $rest, $m, PREG_OFFSET_CAPTURE)) === 1) {
            $at    = $m[0][1];
            $after = $at + strlen($m[0][0]);
            $end   = stripos($rest, '[/mfn]', $after);
            if ($end === false) { $bad = true; break; }

            $note = trim(substr($rest, $after, $end - $after));
            if ($note === '') {
                $problems[] = ['e' => $e, 'field' => $f->handle, 'why' => 'an empty block, nothing between the tags'];
                $bad = true;
                break;
            }

            $n++;
            $out .= substr($rest, 0, $at) . '[' . $n . ']';
            $rest = substr($rest, $end + 6);
            $rows[] = ['number' => (string)$n, 'note' => $note];
        }
        if ($bad) { continue; }
        $out .= $rest;

        $blocks += count($rows);
        $plan[] = ['e' => $e, 'field' => $f->handle, 'rows' => $rows, 'body' => $out];
    }
}

echo 'fields containing the shortcode: ' . $scanned . PHP_EOL;
echo 'records to convert: ' . count($plan) . PHP_EOL;
echo 'notes to write: ' . $blocks . PHP_EOL;
echo 'records with a problem: ' . count($problems) . PHP_EOL;

$written = 0; $failed = [];
foreach ($plan as $p) {
    $e = $p['e'];
    echo PHP_EOL . $e->slug . '  ' . count($p['rows']) . ' note' . (count($p['rows']) == 1 ? '' : 's') . PHP_EOL;
    foreach ($p['rows'] as $r) {
        echo '   [' . $r['number'] . '] ' . mb_substr(preg_replace('/\s+/', ' ', $r['note']), 0, 90) . PHP_EOL;
    }

    if (!$hasField($e, 'footnotes')) {
        echo '   SKIPPED: this entry type has no footnotes field' . PHP_EOL;
        continue;
    }
    $existing = $e->getFieldValue('footnotes');
    if (is_array($existing) && count($existing)) {
        echo '   SKIPPED: footnotes already has ' . count($existing) . ' rows, left alone' . PHP_EOL;
        continue;
    }

    if ($APPLY) {
        $e->setFieldValue('footnotes', $p['rows']);
        $e->setFieldValue($p['field'], $p['body']);
        if ($elements->saveElement($e)) { $written++; }
        else { $failed[] = $e->slug . ': ' . json_encode($e->getErrors()); }
    }
}

foreach ($problems as $pr) {
    echo PHP_EOL . 'PROBLEM ' . $pr['e']->slug . ' [' . $pr['field'] . ']: ' . $pr['why'] . PHP_EOL;
}
foreach ($failed as $f) { echo 'FAILED ' . $f . PHP_EOL; }
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
        if (stripos((string)$fresh->getFieldValue($p['field']), '[mfn') !== false) {
            $short[] = $p['e']->slug . ': the shortcode is still in ' . $p['field'];
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

$out = [];
$out[] = '# The [mfn] shortcode blocks';
$out[] = '';
$out[] = 'Generated by scripts/import/convert_mfn_footnotes.php on ' . date('Y-m-d H:i');
$out[] = ($APPLY ? 'Mode: APPLIED' : 'Mode: DRY RUN, nothing written') . '.';
$out[] = '';
$out[] = '[mfn]...[/mfn] is the WordPress Modern Footnotes plugin from the previous';
$out[] = 'site. The shortcode survived the migration, so the note text has been sitting';
$out[] = 'in the middle of the prose, reading as part of the sentence it annotates.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| fields containing the shortcode | ' . $scanned . ' |';
$out[] = '| records to convert | ' . count($plan) . ' |';
$out[] = '| notes to write | ' . $blocks . ' |';
$out[] = '| records with a problem | ' . count($problems) . ' |';
$out[] = '';
$out[] = 'Numbering runs from 1 in document order. That is safe only because none of';
$out[] = 'these records carries a bare [N] marker as well; one that does is reported and';
$out[] = 'skipped rather than renumbered.';
$out[] = '';
foreach ($plan as $p) {
    $out[] = '## ' . $p['e']->title;
    $out[] = '';
    $out[] = '- ' . $p['e']->url;
    $out[] = '- field `' . $p['field'] . '`, ' . count($p['rows']) . ' notes';
    $out[] = '';
    foreach ($p['rows'] as $r) {
        $out[] = '**[' . $r['number'] . ']** ' . preg_replace('/\s+/', ' ', $r['note']);
        $out[] = '';
    }
}
if ($problems) {
    $out[] = '## Problems, reported rather than repaired';
    $out[] = '';
    foreach ($problems as $pr) { $out[] = '- **' . $pr['e']->slug . '** [`' . $pr['field'] . '`]: ' . $pr['why']; }
    $out[] = '';
}
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
