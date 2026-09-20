/**
 * The paragraphs of 300 or more words, and what they actually are.
 *
 * 13 of them were counted during the photographs import and never looked at.
 * The question that matters is not how many there are but whether the breaks
 * were lost upstream, in the crawl, or here, in the import. If upstream, this
 * is Grok's to fix and nothing should be done to the stored bodies; if here,
 * it is ours.
 *
 * So this reports the shape rather than a count. For each one it gives the
 * record, the word count, and the evidence either way:
 *
 *   sentences        how many sentence ends are inside the run. A genuine long
 *                    paragraph has a handful. One that swallowed six paragraphs
 *                    has thirty.
 *   double spaces    the legacy pages separated sentences with two spaces, and
 *                    a lost <p> often leaves that doubling behind where the
 *                    break used to be.
 *   inline markers   [image:N] and footnote markers inside the run. A paragraph
 *                    with pictures in the middle of it was several paragraphs.
 *   source blocks    how many text blocks the crawled source HTML separates,
 *                    counting a <p>, a <br><br> pair and a block tag as one
 *                    break each. This is the decisive column: if the source
 *                    separated forty blocks and our body holds four paragraphs,
 *                    the loss is ours.
 *
 * The source HTML is not in Craft. legacyHtml is empty on every record that
 * carries a long paragraph, so the evidence comes from the crawl instead, via
 * inventory/legacy/html-index.json. Run build_legacy_html_index.py first; it
 * reduces 55 MB of body_html to four numbers a page.
 *
 * Read only. It writes a report and touches nothing.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/report_long_paragraphs.php'))"
 */

/* Point this at the re-import dry run to test the rebuilt bodies instead of the
   stored ones, which is the only way to check a fix before applying it. */
$FROM_DRYRUN = \Craft::getAlias('@webroot') . '/review/reimport-dryrun.json';
$USE_DRYRUN  = false;

$MIN_WORDS = 300;
$REPORT = \Craft::getAlias('@webroot') . '/review/long-paragraphs.md';

$INDEX = \Craft::getAlias('@root') . '/inventory/legacy/html-index.json';
if (!file_exists($INDEX)) {
    echo 'not found: ' . $INDEX . PHP_EOL;
    echo 'Run python3 scripts/import/build_legacy_html_index.py first.' . PHP_EOL;
    return;
}
$HTML = json_decode(file_get_contents($INDEX), true);
if (!is_array($HTML)) { echo 'could not parse the html index' . PHP_EOL; return; }
echo 'source pages indexed: ' . count($HTML) . PHP_EOL;

$rebuilt = [];
if ($USE_DRYRUN && file_exists($FROM_DRYRUN)) {
    foreach (json_decode(file_get_contents($FROM_DRYRUN), true) as $d) { $rebuilt[$d['id']] = $d['after']; }
    echo 'using the re-import dry run for ' . count($rebuilt) . ' bodies' . PHP_EOL;
}

$rows = [];
$scanned = 0;
$paragraphs = 0;

foreach (\craft\elements\Entry::find()->limit(null)->status(null)->all() as $e) {
    $layout = $e->getFieldLayout();
    if (!$layout) { continue; }

    $prose = [];
    $key = '';
    foreach ($layout->getCustomFields() as $f) {
        if (!($f instanceof \craft\fields\PlainText)) { continue; }
        $v = $e->getFieldValue($f->handle);
        if (!is_string($v) || trim($v) === '') { continue; }
        if ($f->handle === 'legacyKey') { $key = strtolower(trim($v)); continue; }
        if (in_array($f->handle, ['body', 'wmNarrative', 'mpNarrative'], true)) { $prose[$f->handle] = $v; }
    }
    $src = $key !== '' && isset($HTML[$key]) ? $HTML[$key] : null;
    if (isset($rebuilt[$e->id])) {
        /* The fences and list markers are ours and are not paragraph text. */
        $b = preg_replace('/^\s*\[\/?lines\]\s*$/m', '', $rebuilt[$e->id]);
        $prose = ['body' => preg_replace('/^\s*-\s+/m', '', $b)];
    }
    if (!$prose) { continue; }
    $scanned++;

    foreach ($prose as $handle => $text) {
        foreach (preg_split('/\n\s*\n/', $text) as $i => $para) {
            $para = trim($para);
            if ($para === '') { continue; }
            $paragraphs++;
            $words = str_word_count(strip_tags($para));
            if ($words < $MIN_WORDS) { continue; }

            $rows[] = [
                'entry'     => $e,
                'field'     => $handle,
                'index'     => $i,
                'words'     => $words,
                /* A sentence end is . ! or ? followed by whitespace and a capital,
                   so "Mr. Perkins" and "9600 dpi." mid-line are not counted. */
                'sentences' => preg_match_all('/[.!?]\s+[A-Z"\x{201C}]/u', $para),
                'dblSpace'  => substr_count($para, '  '),
                'images'    => preg_match_all('/\[image:\d+\]/', $para),
                'notes'     => preg_match_all('/(?<![A-Za-z0-9])\[\s*\d{1,3}\s*\](?!\d)/', $para),
                'key'       => $key,
                'hasHtml'   => $src !== null,
                'htmlP'     => $src['p'] ?? 0,
                'htmlBr'    => $src['br'] ?? 0,
                'blocks'    => $src['blocks'] ?? 0,
                'srcWords'  => $src['words'] ?? 0,
                'bodyParas' => count(array_filter(preg_split('/\n\s*\n/', $text), fn($p) => trim($p) !== '')),
                'head'      => mb_substr(preg_replace('/\s+/', ' ', $para), 0, 150),
            ];
        }
    }
}

usort($rows, fn($a, $b) => $b['words'] <=> $a['words']);

echo 'records with prose scanned: ' . $scanned . PHP_EOL;
echo 'paragraphs seen: ' . $paragraphs . PHP_EOL;
echo 'paragraphs of ' . $MIN_WORDS . '+ words: ' . count($rows) . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;

/* The verdict per row, and the only one that can be reached from here. A
   paragraph whose source HTML carries more <p> than our body carries
   paragraphs lost its breaks in our import. One whose source HTML has no
   paragraph markup at all never had them, and that is upstream. */
$ours = 0; $upstream = 0; $genuine = 0; $unknown = 0;
foreach ($rows as &$r) {
    if (!$r['hasHtml']) {
        $r['verdict'] = 'the crawl has no page under legacyKey ' . ($r['key'] ?: '(empty)') . ', cannot tell';
        $unknown++;
    } elseif ($r['htmlP'] > $r['bodyParas']) {
        /* The <p> count on its own, not the block count. Blocks include table
           cells and layout divs, and a page built on a table would look like it
           had lost breaks when it never had any. <p> against our paragraphs is
           the conservative test and it is the one the verdict turns on. */
        $r['verdict'] = 'OURS: the source has ' . $r['htmlP']
                      . ' <p> and our body holds ' . $r['bodyParas'] . ' paragraphs';
        $ours++;
    } elseif ($r['blocks'] <= 2) {
        $r['verdict'] = 'UPSTREAM: the source separates nothing either';
        $upstream++;
    } else {
        $r['verdict'] = 'genuine long paragraph, the source agrees at '
                      . $r['blocks'] . ' blocks to our ' . $r['bodyParas'];
        $genuine++;
    }
}
unset($r);

echo 'breaks lost in our import:   ' . $ours . PHP_EOL;
echo 'never had breaks upstream:   ' . $upstream . PHP_EOL;
echo 'genuinely one paragraph:     ' . $genuine . PHP_EOL;
echo 'not in the crawl, cannot judge: ' . $unknown . PHP_EOL;

$out = [];
$out[] = '# Paragraphs of ' . $MIN_WORDS . ' words or more';
$out[] = '';
$out[] = 'Generated by scripts/import/report_long_paragraphs.php on ' . date('Y-m-d H:i') . '. Read only.';
$out[] = '';
$out[] = 'The question is whether the paragraph breaks were lost upstream in the crawl';
$out[] = 'or here in the import. legacyHtml is empty on every one of these records, so';
$out[] = 'the evidence comes from the crawl instead: inventory/legacy/html-index.json';
$out[] = 'reduces each page\'s body_html to the number of text blocks its markup';
$out[] = 'separates, and that is set against the paragraph count in our stored body.';
$out[] = '';
$out[] = '| | |';
$out[] = '|---|---:|';
$out[] = '| records with prose scanned | ' . $scanned . ' |';
$out[] = '| paragraphs seen | ' . $paragraphs . ' |';
$out[] = '| paragraphs of ' . $MIN_WORDS . '+ words | ' . count($rows) . ' |';
$out[] = '| breaks lost in our import | ' . $ours . ' |';
$out[] = '| never had breaks upstream | ' . $upstream . ' |';
$out[] = '| genuinely one paragraph | ' . $genuine . ' |';
$out[] = '| not in the crawl, cannot judge | ' . $unknown . ' |';
$out[] = '';
foreach ($rows as $r) {
    $e = $r['entry'];
    $out[] = '## ' . $e->title . ' (' . $r['words'] . ' words)';
    $out[] = '';
    $out[] = '- ' . $e->url;
    $out[] = '- section ' . $e->section->handle . ', field `' . $r['field'] . '`, paragraph ' . ($r['index'] + 1)
           . ' of ' . $r['bodyParas'];
    $out[] = '- ' . $r['sentences'] . ' sentence ends, ' . $r['dblSpace'] . ' double spaces, '
           . $r['images'] . ' image tokens, ' . $r['notes'] . ' footnote markers';
    $out[] = '- source: ' . ($r['hasHtml']
        ? ($r['blocks'] . ' blocks separated, ' . $r['htmlP'] . ' <p>, ' . $r['htmlBr'] . ' <br>, '
           . $r['srcWords'] . ' words')
        : 'the crawl has no page under this legacyKey');
    $out[] = '- **' . $r['verdict'] . '**';
    $out[] = '';
    $out[] = '> ' . $r['head'] . '...';
    $out[] = '';
}

@mkdir(dirname($REPORT), 0775, true);
file_put_contents($REPORT, implode("\n", $out) . "\n");
echo PHP_EOL . 'report: ' . $REPORT . PHP_EOL;
