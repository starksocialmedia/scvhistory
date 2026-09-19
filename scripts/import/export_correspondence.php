/**
 * The correspondence between the legacy site and the archive, as CSV.
 *
 * One row per legacy page. It is the answer to "what is in this archive and
 * where did it come from", and it is meant to be handed to somebody who has no
 * access to the site, the control panel or the repository: opened in a
 * spreadsheet, sorted, filtered, sent to Leon.
 *
 * Regenerated on demand and never committed. A stored copy of this would be
 * wrong within a day, and a wrong answer to that question is worse than none.
 *
 * Read only. Writes web/review/correspondence.csv and touches nothing in Craft.
 *
 * The legacy half comes from web/review/ledger-index.json, built by
 * build_ledger_index.py from the two crawls and every extraction. The archive
 * half is counted here, as this runs, so the two halves cannot drift.
 *
 * Three columns need explaining.
 *
 *   author, date, publication   Only the article, collection and photograph
 *                               types carry these. On a place or a person they
 *                               are blank because the type has no such field,
 *                               not because nobody filled it in, and the
 *                               has_byline_fields column says which it is.
 *
 *   body_cleaned                Detected, not stored. A body still carrying a
 *                               breadcrumb, a footer row, a copyright line or a
 *                               series heading reads "no". Nothing records that
 *                               a cleaning pass ran, so this asks the text.
 *
 *   reviewed                    The one stored judgement, from
 *                               templates/_data/ledger-review.json.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_correspondence.php'))"
 */

$OUT = \Craft::getAlias('@webroot') . '/review/correspondence.csv';
$INDEX = \Craft::getAlias('@webroot') . '/review/ledger-index.json';
$REVIEW = \Craft::getAlias('@root') . '/templates/_data/ledger-review.json';

if (!file_exists($INDEX)) {
    echo 'ledger-index.json not found. Build it first:' . PHP_EOL;
    echo '  python3 scripts/import/build_ledger_index.py' . PHP_EOL;
    return;
}

$URL_FIELDS = ['legacyUrl', 'placeLegacyUrl', 'personLegacyUrl', 'orgLegacyUrl',
               'groupLegacyUrl', 'eventLegacyUrl', 'obitLegacyUrl', 'mpLegacyUrl'];
$IMAGE_FIELDS = ['featuredImage', 'recordImages', 'bandImage'];

/* Legacy chrome still in a body. Same signatures clean_legacy_bodies.php removes. */
$CHROME = [
    '~^>\s*\S~mu',
    '~^\s*(RETURN TO TOP|RETURN TO MAIN INDEX|MAIN INDEX|PHOTO CREDITS|BIBLIOGRAPHY|BOOKS FOR SALE)\s*$~imu',
    '~^\s*(\x{00a9}|\(c\)\s*copyright|copyright\s+[12]\d{3})~imu',
    '~^\s*(THE STORY OF OUR VALLEY BY A\.B\. PERKINS|HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS)\s*$~mu',
    '~\S[^\n>]*(?:>[ \t]*[A-Z][^\n>]{2,40}){2,}~',
];

$hasField = function (\craft\base\ElementInterface $el, string $h): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};
$norm = function (string $v): string {
    $v = trim($v);
    if ($v === '') { return ''; }
    if (preg_match('~^https?://~i', $v)) { $v = parse_url($v, PHP_URL_PATH) ?: $v; }
    $v = explode('#', explode('?', $v)[0])[0];
    if ($v !== '' && $v[0] !== '/') { $v = '/' . $v; }
    return strtolower(preg_replace('~/{2,}~', '/', $v));
};
$names = function ($q): string {
    if (!($q instanceof \craft\elements\db\ElementQuery)) { return ''; }
    return implode('; ', array_map(fn($e) => (string)$e->title, $q->all()));
};

/* ------------------------------------------------------------ the archive */

$byPath = [];
$bornDigital = [];
foreach (Craft::$app->entries->getAllSections() as $section) {
    foreach (\craft\elements\Entry::find()->section($section->handle)->status(null)->limit(null)->all() as $e) {
        $path = '';
        foreach ($URL_FIELDS as $fh) {
            if ($path === '' && $hasField($e, $fh)) {
                $v = $norm((string)$e->getFieldValue($fh));
                if ($v !== '') { $path = $v; }
            }
        }

        $body = $hasField($e, 'body') ? (string)$e->getFieldValue('body') : '';
        $bodyState = 'no body field';
        if ($hasField($e, 'body')) {
            if (trim($body) === '') { $bodyState = 'empty'; }
            else {
                $bodyState = 'yes';
                foreach ($CHROME as $pat) { if (preg_match($pat, $body)) { $bodyState = 'no'; break; } }
            }
        }

        $images = 0;
        foreach ($IMAGE_FIELDS as $fh) { if ($hasField($e, $fh)) { $images += (int)$e->getFieldValue($fh)->count(); } }

        $hasByline = $hasField($e, 'writtenBy') || $hasField($e, 'photoDate') || $hasField($e, 'publishedBy');
        $author = $hasField($e, 'writtenBy') ? $names($e->getFieldValue('writtenBy')) : '';
        $date = '';
        foreach (['originalPublishDate', 'photoDate'] as $fh) {
            if ($date === '' && $hasField($e, $fh)) { $date = trim((string)$e->getFieldValue($fh)); }
        }
        $publication = '';
        if ($hasField($e, 'publishedBy')) { $publication = $names($e->getFieldValue('publishedBy')); }
        if ($publication === '' && $hasField($e, 'sourceLine')) { $publication = trim((string)$e->getFieldValue('sourceLine')); }
        if ($publication === '' && $hasField($e, 'photoCredit')) { $publication = trim((string)$e->getFieldValue('photoCredit')); }

        $row = [
            'id' => $e->id, 'title' => (string)$e->title, 'url' => (string)$e->url,
            'section' => $section->handle, 'type' => $e->type->handle,
            'author' => $author, 'date' => $date, 'publication' => $publication,
            'hasByline' => $hasByline ? 'yes' : 'no',
            'images' => $images, 'body' => $bodyState,
        ];
        if ($path === '') { $bornDigital[] = $row; }
        elseif (!isset($byPath[$path])) { $byPath[$path] = $row; }
        else { $bornDigital[] = $row + ['dup' => $path]; }
    }
}

$review = [];
if (file_exists($REVIEW)) {
    $review = json_decode(file_get_contents($REVIEW), true)['pages'] ?? [];
}

/* -------------------------------------------------------------- the rows */

$index = json_decode(file_get_contents($INDEX), true);
$fh = fopen($OUT, 'w');
/* A BOM, so a spreadsheet opens the accented names as Rubel rather than RÃ¼bel. */
fwrite($fh, "\xEF\xBB\xBF");
fputcsv($fh, [
    'legacy_url', 'legacy_title', 'legacy_kind', 'legacy_section', 'legacy_words', 'legacy_images',
    'our_url', 'our_title', 'section', 'entry_type', 'record_id',
    'author', 'date', 'publication', 'has_byline_fields',
    'our_images', 'body_cleaned', 'reviewed', 'reviewed_at', 'state',
]);

$counts = ['no record' => 0, 'imported, unchecked' => 0, 'reviewed' => 0, 'skipped' => 0];
$rows = 0;
foreach ($index['pages'] as $p) {
    $path = $p['p'];
    $rec = $byPath[$path] ?? null;
    $r = $review[$path] ?? null;
    $state = $r && ($r['state'] ?? '') === 'reviewed' ? 'reviewed'
        : ($r && ($r['state'] ?? '') === 'skipped' ? 'skipped'
        : ($rec ? 'imported, unchecked' : 'no record'));
    $counts[$state]++;
    $rows++;
    fputcsv($fh, [
        $p['u'] ?? ('https://scvhistory.com' . $path),
        $p['t'] ?? '', $p['k'] ?? '', $p['s'] ?? '', $p['w'] ?? '', $p['ic'] ?? '',
        $rec['url'] ?? '', $rec['title'] ?? '', $rec['section'] ?? '', $rec['type'] ?? '', $rec['id'] ?? '',
        $rec['author'] ?? '', $rec['date'] ?? '', $rec['publication'] ?? '', $rec['hasByline'] ?? '',
        $rec['images'] ?? '', $rec['body'] ?? '',
        $r ? ($r['state'] ?? '') : 'no', $r['at'] ?? '', $state,
    ]);
}

/* Records with no legacy page of their own still belong in the answer to
   "what is in this archive": they are the part of it that is not migrated. */
foreach ($bornDigital as $rec) {
    $rows++;
    fputcsv($fh, [
        '', '', '', '', '', '',
        $rec['url'], $rec['title'], $rec['section'], $rec['type'], $rec['id'],
        $rec['author'], $rec['date'], $rec['publication'], $rec['hasByline'],
        $rec['images'], $rec['body'], '', '',
        isset($rec['dup']) ? 'second record for ' . $rec['dup'] : 'born digital',
    ]);
}
fclose($fh);

echo 'wrote web/review/correspondence.csv, ' . number_format($rows) . ' rows, '
    . number_format(filesize($OUT)) . ' bytes' . PHP_EOL;
echo PHP_EOL . 'legacy pages: ' . count($index['pages']) . PHP_EOL;
foreach ($counts as $k => $n) { echo '   ' . str_pad($k, 22) . $n . PHP_EOL; }
echo 'records with no legacy page: ' . count($bornDigital) . PHP_EOL;
echo PHP_EOL . 'The crawl behind the legacy half stopped at its own limit, so this covers the' . PHP_EOL;
echo 'pages that have been crawled or extracted, not every page on the legacy site.' . PHP_EOL;
echo 'Rebuild that half with: python3 scripts/import/build_ledger_index.py' . PHP_EOL;
