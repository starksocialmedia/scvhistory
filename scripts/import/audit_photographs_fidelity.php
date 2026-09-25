/**
 * The 1,544 photographs against the pages they were imported from.
 *
 * export_fidelity_audit.php writes side-by-side text files for a reviewer, and
 * it covers perkins, reynolds, the war memorial and worden. It does not cover
 * lw-features, which is the photographs section and is larger than all four put
 * together. Side-by-side files for 1,544 records would be 155 files nobody
 * reads, so this counts instead and lists only what is worth looking at.
 *
 * The comparison is by line, on words alone, ignoring case, spacing and
 * punctuation, because the import deliberately changes all three. Two figures
 * come out of it and they are not equally interesting:
 *
 *   LOST      lines in the legacy page that appear nowhere in ours. Usually
 *             navigation and chrome that the import was right to drop, so a
 *             number here is a place to look rather than a fault.
 *
 *   ADDED     lines in ours that appear nowhere in the legacy page. This is the
 *             one that matters. An archive may lose the site's furniture; it
 *             may not invent text. Anything here is either a cleaning step that
 *             rewrote a line rather than removing it, or two records that were
 *             merged, and both are faults.
 *
 * A line is compared whole. A line that was reflowed rather than changed will
 * read as both lost and added, so the report gives the text of every added line
 * and not just the count: the eye tells reflow from invention in a second and a
 * counter cannot.
 *
 * Read only. It writes a report and touches nothing.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_photographs_fidelity.php'))"
 */

/* Set to the dry-run file to audit the rebuilt bodies instead of the stored
   ones. That is the only honest way to check a fix before it is applied: the
   database still holds the damage, so the comparison has to be against what the
   importer would write rather than against what is there. */
$FROM_DRYRUN = \Craft::getAlias('@review') . '/reimport-dryrun.json';
$USE_DRYRUN  = false;

$SECTION   = 'photographs';
$INVENTORY = \Craft::getAlias('@root') . '/inventory/legacy/lw-features.json';
$REPORT    = \Craft::getAlias('@review') . '/photographs-fidelity.md';
$LIST_TOP  = 40;   /* how many records to write out in full */

if (!file_exists($INVENTORY)) { echo 'not found: ' . $INVENTORY . PHP_EOL; return; }

echo 'reading ' . basename($INVENTORY) . ', this takes a moment' . PHP_EOL;
$data = json_decode(file_get_contents($INVENTORY), true);
if (!is_array($data)) { echo 'could not parse the inventory' . PHP_EOL; return; }

$legacy = [];
foreach (($data['pages'] ?? []) as $row) {
    $k = strtolower(trim((string)($row['legacy_key'] ?? '')));
    if ($k === '') { continue; }
    $legacy[$k] = (string)($row['body_text'] ?? '');
}
unset($data);
echo 'legacy pages: ' . count($legacy) . PHP_EOL;

/* Words only, so "Newhall, Cal." and "newhall cal" are the same line. A line
   that reduces to nothing, a rule or a row of dots, is not compared. */
$norm = function (string $line): string {
    $s = mb_strtolower($line, 'UTF-8');
    $s = preg_replace('/&[a-z]+;|&#\d+;/', ' ', $s);
    $s = preg_replace('/[^a-z0-9]+/', ' ', $s);
    return trim(preg_replace('/\s+/', ' ', $s));
};

/* The navigation the import is supposed to drop. Counted apart from the rest so
   that a page losing eleven lines of furniture does not look like a page losing
   eleven lines of history. */
$isChrome = function (string $n): bool {
    if ($n === '') { return true; }
    foreach (['click image to enlarge', 'click to enlarge', 'download archival scan',
              'back', 'home', 'scvhistory com', 'santa clarita valley history',
              'click here', 'next', 'previous', 'site index', 'search',
              'copyright', 'all rights reserved'] as $c) {
        if ($n === $c || str_starts_with($n, $c . ' ')) { return true; }
    }
    return preg_match('/^(lw|ap|ch|ms|sg|jj|gs|tap|ttl)\d+[a-z]?$/', $n) === 1;
};

$entries = \craft\elements\Entry::find()->section($SECTION)->status(null)->limit(null)->all();
echo 'records: ' . count($entries) . PHP_EOL;

$rebuilt = [];
if ($USE_DRYRUN) {
    if (!file_exists($FROM_DRYRUN)) { echo 'not found: ' . $FROM_DRYRUN . PHP_EOL; return; }
    foreach (json_decode(file_get_contents($FROM_DRYRUN), true) as $d) { $rebuilt[$d['id']] = $d['after']; }
    echo 'auditing the DRY RUN output, ' . count($rebuilt) . ' rebuilt bodies' . PHP_EOL;
}
echo str_repeat('=', 72) . PHP_EOL;

$rows = []; $noSource = 0; $totalAdded = 0; $totalLost = 0; $clean = 0;
$totalSrcWords = 0; $totalOurWords = 0; $totalWordsLost = 0; $totalWordsAdded = 0; $wordGutted = 0;

foreach ($entries as $e) {
    $key = strtolower(trim((string)($e->legacyKey ?? '')));
    if ($key === '' || !isset($legacy[$key])) { $noSource++; continue; }

    $aLines = []; $bLines = [];
    foreach (preg_split('/\r?\n/', $legacy[$key]) as $l) {
        $n = $norm($l);
        /* The original line, not true. The classification below reads this
           value, and a boolean counts as one word however long the line. */
        if ($n !== '') { $aLines[$n] = $l; }
    }
    $mine = $rebuilt[$e->id] ?? (string)$e->body;
    /* The fences are ours, not the source's, so they are not lines to compare. */
    $mine = preg_replace('/^\s*\[\/?lines\]\s*$/m', '', $mine);
    foreach (preg_split('/\r?\n/', $mine) as $l) {
        $l = preg_replace('/^\s*-\s+/', '', $l);
        $n = $norm($l);
        if ($n !== '') { $bLines[$n] = $l; }
    }

    /* Words, not lines. The line comparison was adequate while nothing was
       reflowed; a rebuild that puts each source paragraph on one line makes it
       useless, because a paragraph wrapped over five lines in the source reads
       as five lines lost and one line added when not a word has changed. The
       word multiset is immune to wrapping and is the figure that answers
       whether anything was actually lost. */
    $wordsOf = function (string $t) use ($norm): array {
        $bag = [];
        foreach (preg_split('/\s+/', $norm($t)) as $w) {
            if ($w !== '') { $bag[$w] = ($bag[$w] ?? 0) + 1; }
        }
        return $bag;
    };
    $aw = $wordsOf($legacy[$key]);
    $bw = $wordsOf($mine);
    $wLost = 0; $wAdded = 0;
    foreach ($aw as $w => $n) { $d = $n - ($bw[$w] ?? 0); if ($d > 0) { $wLost += $d; } }
    foreach ($bw as $w => $n) { $d = $n - ($aw[$w] ?? 0); if ($d > 0) { $wAdded += $d; } }
    $srcWords = array_sum($aw); $ourWords = array_sum($bw);
    $totalSrcWords += $srcWords; $totalOurWords += $ourWords;
    $totalWordsLost += $wLost; $totalWordsAdded += $wAdded;
    if ($srcWords > 40 && $ourWords < $srcWords / 3) { $wordGutted++; }

    $added = []; $lost = 0; $chrome = 0; $lostShort = 0; $lostProse = 0; $lostSample = [];
    foreach ($bLines as $n => $raw) {
        if (!isset($aLines[$n])) { $added[] = trim($raw); }
    }
    foreach ($aLines as $n => $raw) {
        if (isset($bLines[$n])) { continue; }
        if ($isChrome($n)) { $chrome++; continue; }
        $lost++;
        /* A list entry against a sentence. A census of names one per line and a
           paragraph of narrative are both "lost lines" and are not the same
           loss, so they are counted apart. */
        if (str_word_count($raw) < 6) { $lostShort++; } else { $lostProse++; }
        if (count($lostSample) < 8) { $lostSample[] = trim($raw); }
    }

    $totalAdded += count($added);
    $totalLost  += $lost;
    if (!$added && !$lost) { $clean++; }

    if ($added || $lost) {
        $rows[] = ['e' => $e, 'key' => $key, 'added' => $added, 'lost' => $lost,
                   'short' => $lostShort, 'prose' => $lostProse, 'sample' => $lostSample,
                   'chrome' => $chrome, 'aCount' => count($aLines), 'bCount' => count($bLines),
                   'wLost' => $wLost, 'wAdded' => $wAdded,
                   'srcWords' => $srcWords, 'ourWords' => $ourWords,
                   /* The actual missing words, so the residue can be named
                      rather than guessed at. */
                   'missing' => (function () use ($aw, $bw) {
                       $out = [];
                       foreach ($aw as $w => $n) {
                           $d = $n - ($bw[$w] ?? 0);
                           if ($d > 0) { $out[$w] = $d; }
                       }
                       arsort($out);
                       return array_slice($out, 0, 25, true);
                   })()];
    }
}

/* Added first and by weight, because that is the direction that matters. Then
   by what was lost, because on this section almost nothing was added and the
   losses are the story. */
usort($rows, fn($x, $y) => count($y['added']) <=> count($x['added']) ?: $y['lost'] <=> $x['lost']);

$lostShortTotal = array_sum(array_column($rows, 'short'));
$lostProseTotal = array_sum(array_column($rows, 'prose'));
$gutted = count(array_filter($rows, fn($r) => $r['aCount'] > 20 && $r['bCount'] < $r['aCount'] / 3));

$withAdded = count(array_filter($rows, fn($r) => $r['added']));

echo 'records matched to a legacy page: ' . (count($entries) - $noSource) . PHP_EOL;
echo 'no legacy page under that key:    ' . $noSource . PHP_EOL;
echo 'identical line for line:          ' . $clean . PHP_EOL;
echo 'records with ADDED lines:         ' . $withAdded . PHP_EOL;
echo 'records with lost lines:          ' . count(array_filter($rows, fn($r) => $r['lost'])) . PHP_EOL;
echo 'added lines in total:             ' . $totalAdded . PHP_EOL;
echo 'lost lines in total:              ' . $totalLost . PHP_EOL;
echo '  of those, list entries:         ' . $lostShortTotal . PHP_EOL;
echo '  of those, prose lines:          ' . $lostProseTotal . PHP_EOL;
echo 'records holding under a third of the source, by line: ' . $gutted . PHP_EOL;
echo str_repeat('-', 72) . PHP_EOL;
echo 'WORDS, which wrapping cannot distort' . PHP_EOL;
echo '  words in the source pages: ' . number_format($totalSrcWords) . PHP_EOL;
echo '  words in ours:             ' . number_format($totalOurWords) . PHP_EOL;
echo '  source words missing from ours: ' . number_format($totalWordsLost)
   . ' (' . ($totalSrcWords ? number_format(100 * $totalWordsLost / $totalSrcWords, 1) : '0') . '%)' . PHP_EOL;
echo '  our words not in the source:    ' . number_format($totalWordsAdded)
   . ' (' . ($totalOurWords ? number_format(100 * $totalWordsAdded / $totalOurWords, 1) : '0') . '%)' . PHP_EOL;
echo '  records holding under a third of the source words: ' . $wordGutted . PHP_EOL;

$out = [];
$out[] = '# The photographs against their legacy pages';
$out[] = '';
$out[] = 'Generated by scripts/import/audit_photographs_fidelity.php on ' . date('Y-m-d H:i') . '. Read only.';
$out[] = '';
$out[] = 'Lines compared on their words alone, ignoring case, spacing and punctuation,';
$out[] = 'because the import changes all three on purpose. ADDED is the direction that';
$out[] = 'matters: an archive may drop the old site\'s furniture, it may not invent text.';
$out[] = 'Navigation lines are counted separately from lost history, so a page that shed';
$out[] = 'eleven lines of chrome does not read like a page that shed eleven lines of the';
$out[] = 'record.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| records in the section | ' . count($entries) . ' |';
$out[] = '| matched to a legacy page | ' . (count($entries) - $noSource) . ' |';
$out[] = '| no legacy page under that key | ' . $noSource . ' |';
$out[] = '| identical line for line | ' . $clean . ' |';
$out[] = '| **records with added lines** | **' . $withAdded . '** |';
$out[] = '| records with lost lines | ' . count(array_filter($rows, fn($r) => $r['lost'])) . ' |';
$out[] = '| added lines in total | ' . $totalAdded . ' |';
$out[] = '| lost lines in total | ' . $totalLost . ' |';
$out[] = '| of those, list entries (under 6 words) | ' . $lostShortTotal . ' |';
$out[] = '| of those, prose lines | ' . $lostProseTotal . ' |';
$out[] = '| records holding under a third of their source, by line | ' . $gutted . ' |';
$out[] = '| words in the source pages | ' . number_format($totalSrcWords) . ' |';
$out[] = '| words in ours | ' . number_format($totalOurWords) . ' |';
$out[] = '| **source words missing from ours** | **' . number_format($totalWordsLost) . '** |';
$out[] = '| our words not in the source | ' . number_format($totalWordsAdded) . ' |';
$out[] = '| **records holding under a third of the source words** | **' . $wordGutted . '** |';
$out[] = '';
/* Under a third by line, and the worst by missing words. Both named, because
   "5,820 words missing" is a number and not an answer. */
$thin = array_values(array_filter($rows, fn($r) => $r['aCount'] > 20 && $r['bCount'] < $r['aCount'] / 3));
usort($thin, fn($x, $y) => ($y['aCount'] - $y['bCount']) <=> ($x['aCount'] - $x['bCount']));
$out[] = '## Still under a third of their source, by line (' . count($thin) . ')';
$out[] = '';
$out[] = '| record | source lines | ours | source words | our words | words missing |';
$out[] = '|---|---:|---:|---:|---:|---:|';
foreach ($thin as $r) {
    $out[] = '| [' . $r['e']->title . '](' . $r['e']->url . ') `' . $r['key'] . '` | '
           . $r['aCount'] . ' | ' . $r['bCount'] . ' | ' . $r['srcWords'] . ' | '
           . $r['ourWords'] . ' | ' . $r['wLost'] . ' |';
}
$out[] = '';

$byWords = $rows;
usort($byWords, fn($x, $y) => $y['wLost'] <=> $x['wLost']);
$out[] = '## The 20 records missing the most words';
$out[] = '';
foreach (array_slice($byWords, 0, 20) as $r) {
    $out[] = '### ' . $r['e']->title . ' (' . $r['wLost'] . ' words missing of ' . $r['srcWords'] . ')';
    $out[] = '';
    $out[] = '- ' . $r['e']->url . ' `' . $r['key'] . '`';
    $out[] = '- source ' . $r['aCount'] . ' lines / ' . $r['srcWords'] . ' words, ours '
           . $r['bCount'] . ' lines / ' . $r['ourWords'] . ' words';
    $out[] = '- missing words, most frequent first: `'
           . implode('`, `', array_map(fn($w, $n) => $w . ' x' . $n,
                                       array_keys($r['missing']), array_values($r['missing']))) . '`';
    if ($r['sample']) {
        $out[] = '- lost lines, a sample:';
        foreach (array_slice($r['sample'], 0, 5) as $l) { $out[] = '  - `' . str_replace('`', "'", mb_substr($l, 0, 140)) . '`'; }
    }
    $out[] = '';
}

$out[] = '## The ' . min($LIST_TOP, count($rows)) . ' worst by line, added first';
$out[] = '';
foreach (array_slice($rows, 0, $LIST_TOP) as $r) {
    $out[] = '### ' . $r['e']->title;
    $out[] = '';
    $out[] = '- ' . $r['e']->url;
    $out[] = '- legacy key `' . $r['key'] . '`, ' . $r['aCount'] . ' source lines, ' . $r['bCount'] . ' ours';
    $out[] = '- **' . count($r['added']) . ' added**, ' . $r['lost'] . ' lost ('
           . $r['short'] . ' list entries, ' . $r['prose'] . ' prose), '
           . $r['chrome'] . ' navigation dropped';
    if ($r['sample']) {
        $out[] = '';
        $out[] = 'Lost lines, a sample:';
        $out[] = '';
        foreach ($r['sample'] as $l) { $out[] = '- `' . str_replace('`', "'", mb_substr($l, 0, 160)) . '`'; }
    }
    if ($r['added']) {
        $out[] = '';
        $out[] = 'Added lines, in full:';
        $out[] = '';
        foreach (array_slice($r['added'], 0, 12) as $a) {
            $out[] = '- `' . str_replace('`', "'", mb_substr($a, 0, 200)) . '`';
        }
        if (count($r['added']) > 12) { $out[] = '- ...and ' . (count($r['added']) - 12) . ' more'; }
    }
    $out[] = '';
}

@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
