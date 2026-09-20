/**
 * Where the footnotes are, for every record that prints a marker.
 *
 * Grok's survey of the legacy site found 347 pages carrying markers, about 16
 * with recoverable note text and 331 orphans: the notes were kept on a shared
 * notes.html for a whole series and most of them did not survive. This counts
 * the same thing on our side, against what Craft actually holds, and sorts every
 * record into the three states the templates have to render.
 *
 *   held     the note text is on this record, under a heading the legacy page
 *            used for it. It can be printed in place.
 *   elsewhere  the notes live on another record we hold, usually one notes or
 *            editors-notes page shared by a series. The marker links to it.
 *   orphan   the note text is not in the archive. The marker prints without a
 *            link and the record says so once, rather than offering a link to
 *            nothing.
 *
 * A marker is [N] with no word character before it and no digit after, so a
 * date written as "In December [18]54" by the extraction is not a footnote.
 * That error inflated an earlier count of these by two.
 *
 * Whitespace is allowed inside the brackets, and that is not cosmetic. The
 * extraction broke a marker across lines: /articles/6-oil-and-newhall stores
 * "[", "21", "]" on three lines of their own, and prose.twig rejoins them when
 * it renders. A scan for a literal [21] reads that record as carrying no
 * markers while the page it produces carries five. An earlier count of 229 was
 * made that way and was too low.
 *
 * Read only. Writes a report and touches nothing.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/survey_footnotes.php'))"
 */

$REPORT = \Craft::getAlias('@webroot') . '/review/footnotes.md';

/* No word character before, so "[18]54" inside a date is not a marker, and no
   digit after the bracket, so a year split across the extraction's lines is
   not one either. */
$MARKER = '/(?<![A-Za-z0-9])\[\s*(\d{1,3})\s*\](?!\d)/';

/* The headings the legacy pages put above a block of notes. Matched on a line
   of its own, near the end of a body. */
$NOTES_HEADING = '/^\s*(notes?|footnotes?|end\s?notes?|references?|sources?\s+and\s+notes|editor\'?s?\s+notes?)\s*:?\s*$/i';

/* A record that exists to hold the notes for a series. */
$NOTES_SLUG = '/(^|-)(notes|footnotes|endnotes|editors-notes|editor-notes)($|-)/i';

/* Every long text field on the record, joined. The prose does not live under
   one handle: articles keep it in body, war memorials in wmNarrative, military
   profiles in mpNarrative, and several types carry a second narrative in a
   webmaster note. Reading only body undercounted this by nine records. */
$bodyOf = function (\craft\elements\Entry $e): string {
    $parts = [];
    $layout = $e->getFieldLayout();
    if (!$layout) { return ''; }
    foreach ($layout->getCustomFields() as $f) {
        if (!($f instanceof \craft\fields\PlainText)) { continue; }
        $v = $e->getFieldValue($f->handle);
        if (!is_string($v) || trim($v) === '') { continue; }
        $parts[] = $v;
    }
    return implode("\n\n", $parts);
};

/* The collection a record belongs to, or null. */
$collOf = function (\craft\elements\Entry $e) {
    $layout = $e->getFieldLayout();
    if (!$layout) { return null; }
    foreach ($layout->getCustomFields() as $f) {
        if ($f->handle === 'partOfCollection') { return $e->getFieldValue('partOfCollection')->one(); }
    }
    return null;
};

$entries = \craft\elements\Entry::find()->limit(null)->status(null)->all();
echo 'entries: ' . count($entries) . PHP_EOL;

/* Pass one: the records that could hold notes for others, and the records that
   carry markers. */
$notesRecords = [];   /* id => entry, records that are a notes page */
$carry        = [];   /* records with markers */

foreach ($entries as $e) {
    $body = $bodyOf($e);
    /* A notes page, not a record that merely mentions notes in its title. The
       slug has to be the word and little else: some-notes-about-alec-mentrys
       -birth-name is an article about a name, not a set of endnotes. */
    if (preg_match($NOTES_SLUG, (string)$e->slug) && substr_count((string)$e->slug, '-') <= 1) {
        $notesRecords[$e->id] = $e;
    }
    if ($body === '') { continue; }
    if (!preg_match_all($MARKER, $body, $m)) { continue; }
    $carry[] = ['entry' => $e, 'body' => $body, 'markers' => array_values(array_unique($m[1]))];
}

$notesByColl = [];
foreach ($notesRecords as $nr) {
    $nc = $collOf($nr);
    if ($nc) { $notesByColl[$nc->id] = $nr; }
}

echo 'records that are a notes page: ' . count($notesRecords)
   . ' (' . count($notesByColl) . ' of them inside a collection)' . PHP_EOL;
echo 'records carrying at least one marker: ' . count($carry) . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

/* Pass two: sort them. A record holds its own notes when a notes heading sits
   on a line of its own and is followed by numbered lines. */
$held = []; $elsewhere = []; $orphan = [];

foreach ($carry as $c) {
    $e = $c['entry'];
    $lines = preg_split('/\r?\n/', $c['body']);
    $headingAt = null;
    foreach ($lines as $i => $l) {
        if (preg_match($NOTES_HEADING, $l)) { $headingAt = $i; }
    }
    $numbered = 0;
    if ($headingAt !== null) {
        for ($i = $headingAt + 1, $n = count($lines); $i < $n; $i++) {
            if (preg_match('/^\s*\[?\s*(\d{1,3})\s*[\].]\s+\S/', $lines[$i])) { $numbered++; }
        }
    }

    if ($headingAt !== null && $numbered >= 2) {
        $held[] = ['e' => $e, 'markers' => count($c['markers']), 'notes' => $numbered];
        continue;
    }

    /* A notes record it could point at: the one in the same collection. The
       legacy site kept one notes page per series and every chapter's markers
       referred to it, which is exactly what a collection is here. */
    $target = null;
    $coll = $collOf($e);
    if ($coll && isset($notesByColl[$coll->id])) { $target = $notesByColl[$coll->id]; }

    if ($target) {
        $elsewhere[] = ['e' => $e, 'markers' => count($c['markers']), 'to' => $target];
    } else {
        $orphan[] = ['e' => $e, 'markers' => count($c['markers'])];
    }
}

$totalMarkers = array_sum(array_map(fn($c) => count($c['markers']), $carry));

echo 'held on the record itself:   ' . count($held) . PHP_EOL;
echo 'held on another record:      ' . count($elsewhere) . PHP_EOL;
echo 'orphan, note text not held:  ' . count($orphan) . PHP_EOL;
echo 'distinct markers in total:   ' . $totalMarkers . PHP_EOL;

$out = [];
$out[] = '# Footnotes: where the notes are';
$out[] = '';
$out[] = 'Generated by scripts/import/survey_footnotes.php on ' . date('Y-m-d H:i') . '. Read only.';
$out[] = '';
$out[] = '| state | records | what the template does |';
$out[] = '|---|---:|---|';
$out[] = '| held on the record | ' . count($held) . ' | prints the note under the piece, marker links to it |';
$out[] = '| held on another record | ' . count($elsewhere) . ' | marker links to that record |';
$out[] = '| orphan | ' . count($orphan) . ' | marker prints unlinked, one line says the notes are not in the source |';
$out[] = '| **total carrying markers** | **' . count($carry) . '** | |';
$out[] = '';
$out[] = 'Distinct markers across all of them: ' . $totalMarkers . '.';
$out[] = '';
$out[] = '## Held on the record (' . count($held) . ')';
$out[] = '';
foreach ($held as $h) {
    $out[] = '- [' . $h['e']->title . '](' . $h['e']->url . ') - ' . $h['markers']
           . ' markers, ' . $h['notes'] . ' numbered notes';
}
$out[] = '';
$out[] = '## Held on another record (' . count($elsewhere) . ')';
$out[] = '';
foreach ($elsewhere as $x) {
    $out[] = '- [' . $x['e']->title . '](' . $x['e']->url . ') - ' . $x['markers']
           . ' markers, notes on [' . $x['to']->title . '](' . $x['to']->url . ')';
}
$out[] = '';
$out[] = '## Orphan (' . count($orphan) . ')';
$out[] = '';
foreach ($orphan as $o) {
    $out[] = '- [' . $o['e']->title . '](' . $o['e']->url . ') - ' . $o['markers'] . ' markers';
}
$out[] = '';

@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
