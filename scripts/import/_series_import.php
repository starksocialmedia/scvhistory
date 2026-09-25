<?php
/**
 * The engine behind import_worden_columns.php, import_old_town_newhall.php and
 * import_coins.php. Not run directly: each of those sets $SERIES and includes
 * this.
 *
 * It exists because the three imports are the same job. Three copies of a body
 * rebuilder is three places for the fidelity standard to drift, and the standard
 * is the point.
 *
 * WHAT IT DOES, AND ON WHAT TERMS
 *
 * Structure is transcribed from the crawl's body_html, never inferred. This is
 * the rule the photograph rebuild settled: every name on the Camulos census was
 * its own <p>, so there was nothing to detect. <p> and headings become
 * paragraphs; a <br> run becomes a [lines] fence whose newlines are the
 * author's; <li> becomes a "- " line; a <tr> with two or more cells becomes a
 * tab-separated row in a [table] fence; a <tr> with one cell is the layout the
 * legacy pages are built on and is unwrapped.
 *
 * Navigation is stripped at import, not at render, because it reaches the meta
 * description where no template filter can.
 *
 * Footnotes go to the footnotes table with source = editor. Everything in these
 * runs is Leon annotating somebody's column or his own, never the original
 * author writing about their own text.
 *
 * Matching is by legacy PATH. The key is not unique: Perkins numbers his
 * chapters part01 to part06 and so does Reynolds, and matching on the key once
 * reported six Reynolds chapters as Perkins pages 96% destroyed. There are 18
 * duplicated legacyKeys in the corpus. The path is where the file actually was.
 *
 * Fidelity is measured in words, not lines. The rebuild puts each source
 * paragraph on one line while the crawl keeps its wrapping, so a five-line
 * wrapped paragraph reads as five lines lost and one added when not a word has
 * changed.
 *
 * Nothing is created for a path that already has a record: that record is
 * reported and skipped. An importer that makes a second copy of something is
 * worse than one that does nothing.
 */

$MIRROR = '/mnt/reggie/scvhistory.com';
$root   = \Craft::getAlias('@root');

$NAV = ['click image to enlarge', 'click to enlarge', 'click each image to enlarge',
        'click images to enlarge', 'click map to enlarge', 'click photo to enlarge',
        'download archival scan', 'download archival scans', 'click image for more',
        'click for more', 'click here', 'back', 'return to index', 'index',
        'previous', 'next', 'home'];

$isNav = function (string $line) use ($NAV): bool {
    $n = trim(mb_strtolower($line, 'UTF-8'), " \t.|:;-–—[]<>");
    return $n !== '' && in_array($n, $NAV, true);
};

$BLOCK = 'p|h1|h2|h3|h4|h5|h6|blockquote|li|div|tr|table|center|ul|ol';
$ROW   = '#<tr\b[^>]*>(.*?)(?=</tr>|<tr\b)#is';
$CELL  = '#<t[dh]\b[^>]*>(.*?)(?=</t[dh]>|<t[dh]\b|$)#is';

$toBlocks = function (string $html) use ($BLOCK, $ROW, $CELL): array {
    $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', ' ', $html);
    $html = preg_replace('/<!--.*?-->/s', ' ', $html);
    $html = preg_replace('#<br\b[^>]*>#i', "\x01", $html);
    $html = preg_replace('#<img\b[^>]*>#i', ' ', $html);
    $html = preg_replace('#<li\b[^>]*>#i', "\x02", $html);

    $html = preg_replace_callback($ROW, function ($m) use ($CELL) {
        preg_match_all($CELL, $m[1], $cm);
        $cells = [];
        foreach ($cm[1] as $c) {
            if (preg_match('#<(p|div|table|ul|ol|h[1-6])\b#i', $c)) { return $m[0]; }
            $cells[] = trim(preg_replace('/\s+/u', ' ', strip_tags(str_replace("\x01", ' ', $c))));
        }
        if (count(array_filter($cells, fn($c) => $c !== '')) < 2) { return $m[0]; }
        return "\x03\x04" . implode("\x05", $cells) . "\x03";
    }, $html);

    $html = preg_replace('#</?(' . $BLOCK . ')\b[^>]*>#i', "\x03", $html);
    $html = preg_replace('/<[^>]+>/', '', $html);
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $html = str_replace("\xC2\xA0", ' ', $html);

    $out = [];
    foreach (explode("\x03", $html) as $chunk) {
        if (str_contains($chunk, "\x04")) {
            $cells = array_map(fn($c) => trim(html_entity_decode($c, ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                               explode("\x05", str_replace("\x04", '', $chunk)));
            if (implode('', $cells) === '') { continue; }
            $out[] = ['lines' => [implode("\t", $cells)], 'item' => false, 'row' => true];
            continue;
        }
        $isItem = str_contains($chunk, "\x02");
        $chunk = str_replace("\x02", '', $chunk);
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

/* [mfn] and numbered footnote markers, pulled out of the prose into rows. */
$pullFootnotes = function (string $body): array {
    $rows = []; $n = 0; $out = ''; $rest = $body;
    while (($p = preg_match('/\[mfn\b[^\]]*\]/i', $rest, $m, PREG_OFFSET_CAPTURE)) === 1) {
        $at = $m[0][1]; $after = $at + strlen($m[0][0]);
        $end = stripos($rest, '[/mfn]', $after);
        if ($end === false) { break; }
        $note = trim(substr($rest, $after, $end - $after));
        if ($note === '') { $rest = substr($rest, $end + 6); continue; }
        $n++;
        $out .= substr($rest, 0, $at) . '[' . $n . ']';
        $rest = substr($rest, $end + 6);
        $rows[] = ['col1' => (string)$n, 'col2' => $note, 'col3' => 'editor'];
    }
    return [$out . $rest, $rows];
};

$bag = function (string $t): array {
    $s = preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower($t, 'UTF-8'));
    $b = [];
    foreach (preg_split('/\s+/', trim($s)) as $w) { if ($w !== '') { $b[$w] = ($b[$w] ?? 0) + 1; } }
    return $b;
};

/* ------------------------------------------------------------- the pages */

$invPath = $root . '/inventory/legacy/' . $SERIES['inventory'] . '.json';
if (!file_exists($invPath)) { echo 'not found: ' . $invPath . PHP_EOL; return; }
$inv = json_decode(file_get_contents($invPath), true);

$pages = [];
foreach (['pages', 'series_pages', 'related_pages'] as $L) {
    foreach (($inv[$L] ?? []) as $r) {
        if (!is_array($r)) { continue; }
        $path = strtolower(preg_replace('#^https?://[^/]+#', '', (string)($r['legacy_path'] ?? $r['source_url'] ?? '')));
        if ($path === '') { continue; }
        if (isset($SERIES['pathFilter']) && !preg_match($SERIES['pathFilter'], $path)) { continue; }
        $pages[] = $r + ['_path' => $path];
    }
}
echo 'pages in ' . $SERIES['inventory'] . ' matching this series: ' . count($pages) . PHP_EOL;

/* Records already in Craft, by path, so nothing is duplicated. */
$byPath = [];
foreach (\craft\elements\Entry::find()->limit(null)->status(null)->all() as $e) {
    foreach (['legacyUrl', 'sourcePath'] as $h) {
        foreach ($e->getFieldLayout()->getCustomFields() as $f) {
            if ($f->handle !== $h) { continue; }
            $v = strtolower(trim((string)$e->getFieldValue($h)));
            if ($v === '') { continue; }
            $v = preg_replace('#^https?://[^/]+#', '', $v);
            if ($v !== '' && !isset($byPath[$v])) { $byPath[$v] = $e; }
        }
    }
}

/* The mirror, for the images each page will need. */
$mirror = [];
if (is_dir($MIRROR . '/gif')) {
    foreach (new \DirectoryIterator($MIRROR . '/gif') as $f) {
        if ($f->isFile()) { $mirror[strtolower($f->getFilename())] = true; }
    }
}

$coll = \craft\elements\Entry::find()->section('collections')->slug($SERIES['collection'])->status(null)->one();

echo 'collection: ' . ($coll ? $coll->slug . ' #' . $coll->id : 'NOT FOUND (' . $SERIES['collection'] . ')') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$plan = []; $exists = 0; $noHtml = 0;
$srcWords = 0; $ourWords = 0; $lostWords = 0;
$imagesWanted = []; $imagesOnMirror = 0; $imagesMissing = 0;
$footnoteRows = 0; $tableBlocks = 0; $listBlocks = 0; $lineBlocks = 0;
$candidatePairs = 0;

foreach ($pages as $p) {
    if (isset($byPath[$p['_path']])) { $exists++; continue; }
    $html = (string)($p['body_html'] ?? '');
    if (trim($html) === '') { $noHtml++; continue; }

    $blocks = $toBlocks($html);
    $title = trim((string)($p['title'] ?? ''));
    $titleNorm = preg_replace('/[^a-z0-9]/', '', mb_strtolower($title, 'UTF-8'));

    $parts = []; $pendingList = []; $pendingRows = [];
    $flushRows = function () use (&$parts, &$pendingRows) {
        if ($pendingRows) { $parts[] = "[table]\n" . implode("\n", $pendingRows) . "\n[/table]"; $pendingRows = []; }
    };
    foreach ($blocks as $b) {
        $lines = array_values(array_filter($b['lines'], fn($l) => !$isNav($l)));
        if (!$lines) { continue; }
        if (!empty($b['row'])) { $pendingRows[] = $lines[0]; continue; }
        $flushRows();
        if ($b['item']) { foreach ($lines as $l) { $pendingList[] = '- ' . $l; } continue; }
        if ($pendingList) { $parts[] = implode("\n", $pendingList); $pendingList = []; }
        $flat = implode(' ', $lines);
        if (preg_replace('/[^a-z0-9]/', '', mb_strtolower($flat, 'UTF-8')) === $titleNorm && $titleNorm !== '') { continue; }
        $parts[] = count($lines) > 1 ? ("[lines]\n" . implode("\n", $lines) . "\n[/lines]") : $lines[0];
    }
    $flushRows();
    if ($pendingList) { $parts[] = implode("\n", $pendingList); }

    $body = trim(implode("\n\n", $parts));
    if ($body === '') { $noHtml++; continue; }

    [$body, $fnRows] = $pullFootnotes($body);
    $footnoteRows += count($fnRows);
    $tableBlocks += substr_count($body, '[table]');
    $listBlocks  += preg_match_all('/^- /m', $body);
    $lineBlocks  += substr_count($body, '[lines]');

    $a = $bag((string)($p['body_text'] ?? ''));
    $b2 = $bag($body);
    $lost = 0;
    foreach ($a as $w => $n) { $d = $n - ($b2[$w] ?? 0); if ($d > 0) { $lost += $d; } }
    $srcWords += array_sum($a); $ourWords += array_sum($b2); $lostWords += $lost;

    foreach (($p['images'] ?? []) as $im) {
        $fn = strtolower(basename(parse_url((string)($im['src_raw'] ?? ''), PHP_URL_PATH) ?: ''));
        if ($fn === '') { continue; }
        if (isset($imagesWanted[$fn])) { continue; }
        $imagesWanted[$fn] = true;
        if (isset($mirror[$fn])) { $imagesOnMirror++; } else { $imagesMissing++; }
    }

    $candidatePairs += count($p['people_mentioned'] ?? []) + count($p['places_mentioned'] ?? [])
                     + count($p['orgs_mentioned'] ?? []);

    $plan[] = ['p' => $p, 'title' => $title, 'body' => $body, 'fn' => $fnRows,
               'src' => array_sum($a), 'our' => array_sum($b2), 'lost' => $lost,
               'date' => trim((string)($p['date_raw'] ?? '')),
               'byline' => trim((string)($p['byline_raw'] ?? ''))];
}

$pct = $srcWords ? (100 * (1 - $lostWords / $srcWords)) : 0;

echo 'to import: ' . count($plan) . PHP_EOL;
echo 'already in Craft at that path: ' . $exists . PHP_EOL;
echo 'no usable body_html: ' . $noHtml . PHP_EOL;
echo str_repeat('-', 74) . PHP_EOL;
echo 'source words: ' . number_format($srcWords) . ', ours: ' . number_format($ourWords) . PHP_EOL;
echo 'words missing: ' . number_format($lostWords) . PHP_EOL;
printf("FIDELITY: %.2f%%%s\n", $pct, $pct < 97 ? '   *** UNDER 97, STOPPING ***' : '');
echo 'structure kept: ' . $tableBlocks . ' tables, ' . $listBlocks . ' list items, ' . $lineBlocks . ' line blocks' . PHP_EOL;
echo 'footnote rows to write: ' . $footnoteRows . PHP_EOL;
echo 'distinct images referenced: ' . count($imagesWanted)
   . ' (' . $imagesOnMirror . ' on the mirror, ' . $imagesMissing . ' not)' . PHP_EOL;
echo 'new candidate pairs for the entity review queue: ' . number_format($candidatePairs) . PHP_EOL;

usort($plan, fn($x, $y) => $y['lost'] <=> $x['lost']);
echo PHP_EOL . 'the ten worst by words missing:' . PHP_EOL;
foreach (array_slice($plan, 0, 10) as $r) {
    printf("  %-44s src=%-6d ours=%-6d missing=%-5d %5.1f%%\n",
        mb_substr($r['title'], 0, 44), $r['src'], $r['our'], $r['lost'],
        $r['src'] ? 100 * $r['lost'] / $r['src'] : 0);
}

/* The images this import will need, so pass 3 can be extended to cover them. */
$wantFile = \Craft::getAlias('@review') . '/images-wanted-' . $SERIES['key'] . '.json';
@mkdir(dirname($wantFile), 0775, true);
file_put_contents($wantFile, json_encode(array_keys($imagesWanted)));
echo PHP_EOL . 'images this import needs: ' . $wantFile . PHP_EOL;

/* A twenty-record before and after. */
$REPORT = \Craft::getAlias('@review') . '/import-' . $SERIES['key'] . '.md';
$out = [];
$out[] = '# ' . $SERIES['label'];
$out[] = '';
$out[] = 'Generated by scripts/import/' . $SERIES['script'] . ' on ' . date('Y-m-d H:i');
$out[] = ($APPLY ? 'Mode: APPLIED' : 'Mode: DRY RUN, nothing written') . '.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| pages in the inventory | ' . count($pages) . ' |';
$out[] = '| to import | ' . count($plan) . ' |';
$out[] = '| already in Craft | ' . $exists . ' |';
$out[] = '| no usable body_html | ' . $noHtml . ' |';
$out[] = '| source words | ' . number_format($srcWords) . ' |';
$out[] = '| our words | ' . number_format($ourWords) . ' |';
$out[] = '| words missing | ' . number_format($lostWords) . ' |';
$out[] = '| **fidelity** | **' . number_format($pct, 2) . '%** |';
$out[] = '| tables kept | ' . $tableBlocks . ' |';
$out[] = '| list items kept | ' . $listBlocks . ' |';
$out[] = '| footnote rows | ' . $footnoteRows . ' |';
$out[] = '| images referenced | ' . count($imagesWanted) . ' |';
$out[] = '| of those on the mirror | ' . $imagesOnMirror . ' |';
$out[] = '| new candidate pairs for the review queue | ' . number_format($candidatePairs) . ' |';
$out[] = '';
$out[] = '## Ten records, before and after';
$out[] = '';
foreach (array_slice($plan, 0, 10) as $r) {
    $out[] = '### ' . $r['title'];
    $out[] = '';
    $out[] = '- `' . $r['p']['_path'] . '`, date as printed: `' . ($r['date'] ?: '(none)') . '`';
    $out[] = '- ' . $r['our'] . ' words against ' . $r['src'] . ' in the source, ' . $r['lost'] . ' missing';
    $out[] = '';
    $out[] = '**Before**, the crawl\'s body_text:';
    $out[] = '';
    $out[] = '```';
    $out[] = mb_substr(trim((string)($r['p']['body_text'] ?? '')), 0, 700);
    $out[] = '```';
    $out[] = '';
    $out[] = '**After**, what would be stored:';
    $out[] = '';
    $out[] = '```';
    $out[] = mb_substr($r['body'], 0, 700);
    $out[] = '```';
    $out[] = '';
}
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo 'report: ' . $REPORT . PHP_EOL;

if ($pct < 97) {
    echo PHP_EOL . 'Fidelity is under 97%. Nothing will be written even with $APPLY on.' . PHP_EOL;
    return;
}
if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$coll) { echo 'cannot apply: the collection does not exist' . PHP_EOL; return; }

/* ---------------------------------------------------------------- apply */

$section = Craft::$app->getEntries()->getSectionByHandle('articles');
$type = Craft::$app->getEntries()->getEntryTypeByHandle('article');
$elements = Craft::$app->getElements();
$made = 0; $failed = []; $order = $coll->articlesInCollection->ids();

foreach ($plan as $r) {
    $e = new \craft\elements\Entry();
    $e->sectionId = $section->id;
    $e->typeId = $type->id;
    $e->title = $r['title'] !== '' ? $r['title'] : basename($r['p']['_path']);
    $e->enabled = true;
    $h = [];
    foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[] = $f->handle; }
    $set = function ($k, $v) use ($e, $h) { if (in_array($k, $h, true)) { $e->setFieldValue($k, $v); } };
    $set('body', $r['body']);
    $set('legacyKey', $r['p']['_path']);
    $set('legacyUrl', $r['p']['_path']);
    $set('sourcePath', (string)($r['p']['source_url'] ?? ''));
    $set('originalPublishDate', $r['date']);
    $set('partOfCollection', [$coll->id]);
    if ($r['fn']) { $set('footnotes', $r['fn']); }
    if (!$elements->saveElement($e)) { $failed[] = $e->title . ': ' . json_encode($e->getErrors()); continue; }
    $made++;
    $order[] = $e->id;
}
$coll->setFieldValue('articlesInCollection', $order);
if (!$elements->saveElement($coll)) { $failed[] = 'collection: ' . json_encode($coll->getErrors()); }

echo PHP_EOL . 'saved: ' . $made . PHP_EOL;
foreach (array_slice($failed, 0, 10) as $f) { echo '  FAILED ' . $f . PHP_EOL; }

$back = 0; $short = [];
foreach ($plan as $r) {
    $fresh = null;
    foreach (\craft\elements\Entry::find()->section('articles')->status(null)->limit(null)->all() as $x) {
        if (strtolower(trim((string)$x->legacyUrl)) === $r['p']['_path']) { $fresh = $x; break; }
    }
    if (!$fresh) { $short[] = $r['p']['_path'] . ': not found after import'; continue; }
    if (trim((string)$fresh->body) === '') { $short[] = $r['p']['_path'] . ': body empty'; continue; }
    $c = $fresh->partOfCollection->one();
    if (!$c || $c->id !== $coll->id) { $short[] = $r['p']['_path'] . ': not in the collection'; continue; }
    $back++;
}
echo 'read back: ' . $back . ' of ' . count($plan) . PHP_EOL;
if ($short) {
    echo PHP_EOL . 'THE WRITE DID NOT PERSIST' . PHP_EOL;
    foreach (array_slice($short, 0, 10) as $m) { echo '  ' . $m . PHP_EOL; }
    return;
}
echo 'verified.' . PHP_EOL;
