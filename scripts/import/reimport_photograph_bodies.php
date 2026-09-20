/**
 * Rebuilds the 1,544 photograph bodies from the crawl's source HTML.
 *
 * The fidelity audit found 28,256 lines in the legacy pages that are in no
 * record here, across 1,517 of the 1,544, with 99 records holding under a third
 * of their source. The Camulos Cemetery Census is 93 lines on the legacy page
 * and 218 characters in the database: the eighty names on it are simply gone.
 * Nothing was invented, so the fault is entirely one of loss.
 *
 * THE MECHANISM, AND WHY THIS ONE
 *
 * The three options were a separate field for list content, a render-time rule
 * in prose.twig that recognises a run of short lines as a list, or detecting
 * the shape at import. None of them is right, and the reason is visible in the
 * source:
 *
 *     <p>Ynacio Aceves
 *     </p><p>Beniono Acosta
 *     </p><p>Bernabe Acosta
 *
 * Every name on that census is its own <p>. The source already states the
 * structure. There is nothing to detect, nothing to infer and nothing to guess
 * at render time. The previous importer lost those lines because it treated
 * short paragraphs as noise; the fix is to stop doing that, not to build
 * machinery that re-derives what the HTML already says.
 *
 * So: transcribe the source's own structure and infer nothing.
 *
 *   <p> <h1..h6> <blockquote>   a paragraph, blank line separated, its text
 *                               reflowed onto one line
 *   <br> inside a block         a hard line break, kept by wrapping the block
 *                               in [lines] ... [/lines] so the newlines inside
 *                               it are the author's and not the reflow's
 *   <li>                        a line prefixed "- "; consecutive items are one
 *                               list. Rare, 565 items on 31 pages, but real
 *   <tr> with two or more cells a table row: one tab separated line inside a
 *                               [table] ... [/table] fence. Row width is a fact
 *                               about the source, not a guess about the content
 *   <tr> with one cell,         unwrapped. 1,553 of the 1,661 pages sit inside
 *   <div> <center> <font>       a single-cell layout table, and representing
 *   <span>                      those as tables would invent 1,553 tables that
 *                               are not tables
 *
 * A separate field was rejected because it cannot hold position: lw2575c is
 * prose, then a transcribed letter, then prose again, and a field beside the
 * body cannot say where the letter went. A render-time rule was rejected
 * because "a run of short lines" is also a poem, an address block and a stack
 * of captions, and because guessing at render is the same class of mistake that
 * caused this loss.
 *
 * WHAT IS NOT TOUCHED
 *
 * Entry IDs, slugs, relations, provenance, every field but the body. This
 * updates bodies in place and creates nothing. A record whose current body
 * carries an [image:N] token is skipped and reported rather than rebuilt,
 * because the rebuild cannot know where the token belonged; none currently do.
 *
 * Navigation is removed here rather than at render, because it is in the meta
 * description on all 1,544 and a template filter cannot reach that.
 *
 * Dry run by default. The dry run writes every rebuilt body to
 * web/review/reimport-dryrun.json so the fidelity audit can be run against the
 * output rather than against the database.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/reimport_photograph_bodies.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will rewrite bodies in the database' . PHP_EOL; }

$SECTION   = 'photographs';
$INVENTORY = \Craft::getAlias('@root') . '/inventory/legacy/lw-features.json';
$DRYRUN    = \Craft::getAlias('@webroot') . '/review/reimport-dryrun.json';
$REPORT    = \Craft::getAlias('@webroot') . '/review/photograph-reimport.md';

if (!file_exists($INVENTORY)) { echo 'not found: ' . $INVENTORY . PHP_EOL; return; }

echo 'reading ' . basename($INVENTORY) . PHP_EOL;
$data = json_decode(file_get_contents($INVENTORY), true);
$src = [];
foreach (($data['pages'] ?? []) as $row) {
    $k = strtolower(trim((string)($row['legacy_key'] ?? '')));
    if ($k !== '') { $src[$k] = $row; }
}
unset($data);
echo 'source pages: ' . count($src) . PHP_EOL;

/* ------------------------------------------------------------ navigation */

/* The instruction that used to sit under a thumbnail, and the bar it hung off.
   Whole lines only. A sentence that happens to contain the word "click" is
   prose and is left alone. */
$NAV = ['click image to enlarge', 'click to enlarge', 'click each image to enlarge',
        'click images to enlarge', 'click map to enlarge', 'click photo to enlarge',
        'download archival scan', 'download archival scans', 'click image for more',
        'click for more', 'click image to see more', 'click here for extra large',
        'click image to supersize', 'enlarge', 'full view', 'closeup', 'ultra closeup'];

$isNav = function (string $line) use ($NAV): bool {
    $n = mb_strtolower(trim($line), 'UTF-8');
    $n = trim($n, " \t.|:;-–—[]");
    if ($n === '') { return false; }
    return in_array($n, $NAV, true);
};

/* ------------------------------------------------------- html to blocks */

$BLOCK = 'p|h1|h2|h3|h4|h5|h6|blockquote|li|div|tr|table|center|ul|ol';

/* A row with more than one cell is a table. A row with one cell is the layout
   the legacy pages are built on, and unwrapping it is right. That distinction
   is a fact about the source and not a guess about the content: 1,553 of the
   1,661 pages sit inside a single-cell table, and the 1923 Newhall telephone
   directory is a genuine two-column one. */
$ROW = '#<tr\b[^>]*>(.*?)(?=</tr>|<tr\b)#is';
$CELL = '#<t[dh]\b[^>]*>(.*?)(?=</t[dh]>|<t[dh]\b|$)#is';

$toBlocks = function (string $html) use ($BLOCK, $ROW, $CELL): array {
    /* Anything that is not text. */
    $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', ' ', $html);
    $html = preg_replace('/<!--.*?-->/s', ' ', $html);

    /* A break is kept as a sentinel so it survives the tag strip below and can
       be told apart from the whitespace the markup is full of. */
    $html = preg_replace('#<br\b[^>]*>#i', "\x01", $html);

    /* An image is dropped from the prose. The images are related to the record
       as assets and are listed from there; a bare filename in the middle of a
       sentence is not text. */
    $html = preg_replace('#<img\b[^>]*>#i', ' ', $html);

    /* List items are marked before the block split so the marker survives. */
    $html = preg_replace('#<li\b[^>]*>#i', "\x02", $html);

    /* Multi-cell rows are lifted out first and rewritten as a single tab
       separated line inside a fence, so the generic block pass cannot split a
       name away from its telephone number. A cell that itself holds paragraphs
       is left alone: that is a layout cell whatever the row looks like. */
    $html = preg_replace_callback($ROW, function ($m) use ($CELL) {
        preg_match_all($CELL, $m[1], $cm);
        $cells = [];
        foreach ($cm[1] as $c) {
            if (preg_match('#<(p|div|table|ul|ol|h[1-6])\b#i', $c)) { return $m[0]; }
            $t = trim(preg_replace('/\s+/u', ' ', strip_tags(str_replace("\x01", ' ', $c))));
            $cells[] = $t;
        }
        $filled = array_filter($cells, fn($c) => $c !== '');
        if (count($filled) < 2) { return $m[0]; }
        return "\x03\x04" . implode("\x05", $cells) . "\x03";
    }, $html);

    /* Every block boundary becomes a sentinel of its own. Opening and closing
       both, because the legacy markup opens a <p> and closes it around the next
       one as often as it nests properly. */
    $html = preg_replace('#</?(' . $BLOCK . ')\b[^>]*>#i', "\x03", $html);

    /* What is left is inline: <a>, <font>, <b>, <span>. Strip to the text. */
    $html = preg_replace('/<[^>]+>/', '', $html);
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $html = str_replace("\xC2\xA0", ' ', $html);

    $out = [];
    foreach (explode("\x03", $html) as $chunk) {
        if (str_contains($chunk, "\x04")) {
            $cells = explode("\x05", str_replace("\x04", '', $chunk));
            $cells = array_map(fn($c) => trim(html_entity_decode($c, ENT_QUOTES | ENT_HTML5, 'UTF-8')), $cells);
            if (implode('', $cells) === '') { continue; }
            $out[] = ['lines' => [implode("\t", $cells)], 'item' => false, 'row' => true];
            continue;
        }
        $isItem = str_contains($chunk, "\x02");
        $chunk = str_replace("\x02", '', $chunk);

        /* Reflow each hard-broken line on its own, so the wrapping the legacy
           HTML happens to use disappears and the author's breaks do not. */
        $lines = [];
        foreach (explode("\x01", $chunk) as $piece) {
            $piece = trim(preg_replace('/\s+/u', ' ', $piece));
            if ($piece !== '') { $lines[] = $piece; }
        }
        if (!$lines) { continue; }
        $out[] = ['lines' => $lines, 'item' => $isItem, 'row' => false];
    }
    return $out;
};

/* ----------------------------------------------------------- the rebuild */

$entries = \craft\elements\Entry::find()->section($SECTION)->status(null)->limit(null)->all();
echo 'records: ' . count($entries) . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

$elements = Craft::$app->getElements();
$dry = []; $skipped = []; $noSource = 0; $unchanged = 0;
$beforeChars = 0; $afterChars = 0; $beforeLines = 0; $afterLines = 0;

foreach ($entries as $e) {
    $key = strtolower(trim((string)($e->legacyKey ?? '')));
    if ($key === '' || !isset($src[$key])) { $noSource++; continue; }

    $current = (string)$e->body;

    if (preg_match('/\[image:\d+\]/', $current)) {
        $skipped[] = $e->slug . ': the current body carries [image:N] tokens and the rebuild '
                   . 'cannot know where they belonged';
        continue;
    }

    $blocks = $toBlocks((string)($src[$key]['body_html'] ?? ''));

    /* The record's own title, so the page's headline is not repeated as the
       first line of its own body. */
    $titleNorm = preg_replace('/[^a-z0-9]/', '', mb_strtolower((string)$e->title, 'UTF-8'));

    $parts = []; $pendingList = []; $pendingRows = [];
    $flushRows = function () use (&$parts, &$pendingRows) {
        if (!$pendingRows) { return; }
        $parts[] = "[table]\n" . implode("\n", $pendingRows) . "\n[/table]";
        $pendingRows = [];
    };
    foreach ($blocks as $b) {
        $lines = array_values(array_filter($b['lines'], fn($l) => !$isNav($l)));
        if (!$lines) { continue; }

        if (!empty($b['row'])) { $pendingRows[] = $lines[0]; continue; }
        $flushRows();

        if ($b['item']) {
            foreach ($lines as $l) { $pendingList[] = '- ' . $l; }
            continue;
        }
        if ($pendingList) { $parts[] = implode("\n", $pendingList); $pendingList = []; }

        $flat = implode(' ', $lines);
        $flatNorm = preg_replace('/[^a-z0-9]/', '', mb_strtolower($flat, 'UTF-8'));
        if ($flatNorm !== '' && $flatNorm === $titleNorm) { continue; }

        if (count($lines) > 1) {
            /* The author's own line breaks. Fenced so a later reflow, here or
               in a template, cannot quietly join them back up. */
            $parts[] = "[lines]\n" . implode("\n", $lines) . "\n[/lines]";
        } else {
            $parts[] = $lines[0];
        }
    }
    $flushRows();
    if ($pendingList) { $parts[] = implode("\n", $pendingList); }

    $new = trim(implode("\n\n", $parts));
    if ($new === '') { $skipped[] = $e->slug . ': the rebuild came out empty, left alone'; continue; }
    if ($new === trim($current)) { $unchanged++; continue; }

    $beforeChars += mb_strlen($current); $afterChars += mb_strlen($new);
    $beforeLines += count(array_filter(explode("\n", $current), fn($l) => trim($l) !== ''));
    $afterLines  += count(array_filter(explode("\n", $new), fn($l) => trim($l) !== ''));

    $dry[] = ['id' => $e->id, 'slug' => $e->slug, 'key' => $key, 'before' => $current, 'after' => $new];
}

echo 'records to rebuild: ' . count($dry) . PHP_EOL;
echo 'already identical: ' . $unchanged . PHP_EOL;
echo 'no source page: ' . $noSource . PHP_EOL;
echo 'skipped: ' . count($skipped) . PHP_EOL;
echo 'characters before: ' . number_format($beforeChars) . PHP_EOL;
echo 'characters after:  ' . number_format($afterChars) . PHP_EOL;
echo 'lines before: ' . number_format($beforeLines) . PHP_EOL;
echo 'lines after:  ' . number_format($afterLines) . PHP_EOL;
foreach ($skipped as $s) { echo '  SKIP ' . $s . PHP_EOL; }

@mkdir(dirname($DRYRUN), 0775, true);
file_put_contents($DRYRUN, json_encode($dry));
echo PHP_EOL . 'dry run bodies: ' . $DRYRUN . PHP_EOL;

/* And one file per record where Twig can reach it, so /admin-preview can render
   a rebuilt body as the page it would become without writing it. source() goes
   through the template loader and cannot resolve @webroot, and an 18MB file is
   not something to parse on every request. */
$PREVIEW = \Craft::getAlias('@root') . '/templates/_data/reimport';
@mkdir($PREVIEW, 0775, true);
foreach (glob($PREVIEW . '/*.json') as $old) { @unlink($old); }
foreach ($dry as $d) {
    file_put_contents($PREVIEW . '/' . $d['key'] . '.json',
        json_encode(['id' => $d['id'], 'slug' => $d['slug'], 'after' => $d['after']]));
}
echo 'preview bodies: ' . $PREVIEW . ' (' . count($dry) . ' files)' . PHP_EOL;

$written = 0; $failed = [];
if ($APPLY) {
    foreach ($dry as $d) {
        $e = \craft\elements\Entry::find()->id($d['id'])->status(null)->one();
        if (!$e) { continue; }
        $e->setFieldValue('body', $d['after']);
        if ($elements->saveElement($e)) { $written++; }
        else { $failed[] = $d['slug'] . ': ' . json_encode($e->getErrors()); }
    }
    echo 'saved: ' . $written . PHP_EOL;
    foreach ($failed as $f) { echo '  FAILED ' . $f . PHP_EOL; }

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
    foreach ($dry as $d) {
        $fresh = \craft\elements\Entry::find()->id($d['id'])->status(null)->one();
        if (!$fresh) { $short[] = $d['slug'] . ': gone after save'; continue; }
        if (trim((string)$fresh->body) === trim($d['after'])) { $back++; }
        else { $short[] = $d['slug'] . ': stored body does not match what was written'; }
    }
    echo 'read back: ' . $back . ' of ' . count($dry) . ' bodies match' . PHP_EOL;
    if ($short) {
        echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
        foreach (array_slice($short, 0, 10) as $m) { echo '  ' . $m . PHP_EOL; }
        echo 'Do not re-run until this is understood.' . PHP_EOL;
        return;
    }
    echo 'verified.' . PHP_EOL;
}

/* ------------------------------------------------- the before and after */

$showKeys = ['lw2717', 'lw2575c'];
$out = [];
$out[] = '# Rebuilding the photograph bodies from the source HTML';
$out[] = '';
$out[] = 'Generated by scripts/import/reimport_photograph_bodies.php on ' . date('Y-m-d H:i');
$out[] = ($APPLY ? 'Mode: APPLIED' : 'Mode: DRY RUN, nothing written') . '.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| records in the section | ' . count($entries) . ' |';
$out[] = '| records to rebuild | ' . count($dry) . ' |';
$out[] = '| already identical | ' . $unchanged . ' |';
$out[] = '| no source page | ' . $noSource . ' |';
$out[] = '| skipped | ' . count($skipped) . ' |';
$out[] = '| characters before | ' . number_format($beforeChars) . ' |';
$out[] = '| characters after | ' . number_format($afterChars) . ' |';
$out[] = '| non-empty lines before | ' . number_format($beforeLines) . ' |';
$out[] = '| non-empty lines after | ' . number_format($afterLines) . ' |';
$out[] = '';
foreach ($showKeys as $k) {
    foreach ($dry as $d) {
        if ($d['key'] !== $k) { continue; }
        $out[] = '## ' . $k . ' — ' . $d['slug'];
        $out[] = '';
        $out[] = '### Before (' . mb_strlen($d['before']) . ' characters)';
        $out[] = '';
        $out[] = '```';
        $out[] = $d['before'];
        $out[] = '```';
        $out[] = '';
        $out[] = '### After (' . mb_strlen($d['after']) . ' characters)';
        $out[] = '';
        $out[] = '```';
        $out[] = $d['after'];
        $out[] = '```';
        $out[] = '';
    }
}
if ($skipped) {
    $out[] = '## Skipped';
    $out[] = '';
    foreach ($skipped as $s) { $out[] = '- ' . $s; }
    $out[] = '';
}
@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo 'report: ' . $REPORT . PHP_EOL;
