/**
 * Publishes the collections table at /data/collections.json and .csv.
 *
 * WHY THIS IS NOT IN build_data_exports.php
 *
 * That script writes people, places and organizations, which are the same kind
 * of thing described by the same columns: a name, some identifiers, a parent, a
 * count. A collection is not that. It has no authority identifier, no parent
 * and no subtype; what it has is a shape, a hand, a length and a span of years.
 * Sharing the writer would mean a table where half the columns are empty for
 * one set and the other half for the rest, which is a table that describes the
 * exporter rather than the material.
 *
 * WHAT THE COLUMNS ARE
 *
 *   kind        series, book, column, catalogue or topic: how it is read, not
 *               what it is about
 *   byline      the author where a collection has one hand, the editor where it
 *               has a compiler, and which of the two is named in bylineRole,
 *               because "Leon Worden" and "Leon Worden, editor" are different
 *               claims and a single name column silently picks one
 *   pieces      how many articles sit in the collection
 *   eraFrom /   the span the PIECES cover, derived from their historical eras
 *   eraTo       and never asserted of the collection itself. A collection has
 *               no era of its own; saying it does would be inventing one.
 *
 * Era span comes from the eras' own sort order where they carry one, and from
 * the first year in the title otherwise -- "Mexican Rancho Era (1821-1848)"
 * sorts by 1821 whatever its position in the category group.
 *
 * Dry run by default. Set $COLLECTIONS_APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_collections_export.php'))"
 */

$APPLY = $COLLECTIONS_APPLY ?? false;

$OUTDIR = \Craft::getAlias('@webroot') . '/data';
$SITE = rtrim(\craft\helpers\UrlHelper::siteUrl(), '/') . '/';
$LICENSE = [
    'name' => 'Creative Commons Attribution 4.0 International',
    'id' => 'CC-BY-4.0',
    'url' => 'https://creativecommons.org/licenses/by/4.0/',
    'attribution' => 'SCVHistory.com, the Santa Clarita Valley History Archive',
];

$has = function ($el, string $h): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

/* The first four-digit year in a string, for ordering eras whose sort order is
   not set. Returns null rather than 0 so "no year" and "year zero" stay apart. */
$yearOf = function (string $s): ?int {
    return preg_match('~\b(1[5-9]\d{2}|20\d{2})\b~', $s, $m) ? (int)$m[1] : null;
};

echo ($APPLY ? 'WRITING' : 'DRY RUN') . '   -> ' . $OUTDIR . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$rows = [];
foreach (\craft\elements\Entry::find()->section('collections')->status(null)
             ->orderBy('title asc')->limit(null)->all() as $c) {

    $pieces = (int)\craft\elements\Entry::find()->section('articles')
        ->relatedTo(['targetElement' => $c, 'field' => 'partOfCollection'])->status(null)->count();

    $author = $has($c, 'writtenBy') ? $c->writtenBy->one() : null;
    $editor = $has($c, 'editedBy') ? $c->editedBy->one() : null;
    $byline = $author ?: $editor;

    $eras = [];
    if ($pieces) {
        foreach (\craft\elements\Entry::find()->section('articles')
                     ->relatedTo(['targetElement' => $c, 'field' => 'partOfCollection'])
                     ->status(null)->limit(null)->all() as $p) {
            $e = $has($p, 'historicalEra') ? $p->historicalEra->one() : null;
            if ($e && !isset($eras[$e->id])) { $eras[$e->id] = $e; }
        }
    }
    $sorted = array_values($eras);
    usort($sorted, function ($a, $b) use ($yearOf) {
        $ya = $yearOf((string)$a->title); $yb = $yearOf((string)$b->title);
        if ($ya !== null && $yb !== null) { return $ya <=> $yb; }
        return ((int)($a->lft ?? 0)) <=> ((int)($b->lft ?? 0));
    });

    $rows[] = [
        'id' => $c->id,
        'title' => (string)$c->title,
        'kind' => $has($c, 'collectionKind') ? (string)($c->collectionKind->value ?? '') : '',
        'byline' => $byline ? (string)$byline->title : '',
        'bylineRole' => $byline ? ($author ? 'author' : 'editor') : '',
        'bylineUrl' => $byline ? (string)$byline->url : '',
        'pieces' => $pieces,
        'eraFrom' => $sorted ? (string)$sorted[0]->title : '',
        'eraTo' => $sorted ? (string)$sorted[count($sorted) - 1]->title : '',
        'eraCount' => count($sorted),
        'url' => (string)$c->url,
        'updated' => $c->dateUpdated ? $c->dateUpdated->format('c') : '',
    ];
}

printf("%-46s %-10s %-22s %6s %s\n", 'TITLE', 'KIND', 'BYLINE', 'PIECES', 'ERA SPAN');
foreach ($rows as $r) {
    $span = $r['eraFrom'] === '' ? ''
        : ($r['eraFrom'] === $r['eraTo'] ? $r['eraFrom'] : $r['eraFrom'] . ' .. ' . $r['eraTo']);
    printf("%-46s %-10s %-22s %6d %s\n", mb_substr($r['title'], 0, 45), $r['kind'],
        mb_substr($r['byline'] . ($r['bylineRole'] === 'editor' ? ' (ed.)' : ''), 0, 21),
        $r['pieces'], mb_substr($span, 0, 46));
}

echo str_repeat('-', 78) . PHP_EOL;
printf("%d collections, %d pieces, %d with a byline, %d with an era span\n",
    count($rows), array_sum(array_column($rows, 'pieces')),
    count(array_filter($rows, fn($r) => $r['byline'] !== '')),
    count(array_filter($rows, fn($r) => $r['eraFrom'] !== '')));
$noKind = array_values(array_filter($rows, fn($r) => $r['kind'] === ''));
if ($noKind) {
    echo 'no kind set: ' . implode(', ', array_column($noKind, 'title')) . PHP_EOL;
}

if (!$APPLY) { echo PHP_EOL . 'DRY RUN. Nothing was written. Set $COLLECTIONS_APPLY = true to write.' . PHP_EOL; return; }

if (!is_dir($OUTDIR)) { mkdir($OUTDIR, 0775, true); }

$doc = [
    'meta' => [
        'title' => 'SCVHistory.com collections',
        'source' => $LICENSE['attribution'],
        'url' => $SITE,
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/build_collections_export.php',
        'records' => count($rows),
        'license' => $LICENSE,
        'note' => 'One row per collection. kind is how a collection is read, not what it is '
            . 'about. byline is the author where a collection has one hand and the editor '
            . 'where it has a compiler; bylineRole says which. eraFrom and eraTo describe the '
            . 'PIECES, not the collection: a collection has no era of its own and asserting '
            . 'one would be inventing it. Follow url for the collection itself.',
    ],
    'records' => $rows,
];

/* 0644. PHP's umask in the container gives these 0600, which the web server
   cannot read, so the file publishes and then serves a 403. */
$jsonPath = $OUTDIR . '/collections.json';
file_put_contents($jsonPath,
    json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
@chmod($jsonPath, 0644);

$csvPath = $OUTDIR . '/collections.csv';
$fh = fopen($csvPath, 'w');
fwrite($fh, "# SCVHistory.com collections\n");
fwrite($fh, "# " . $LICENSE['name'] . " (" . $LICENSE['id'] . "), " . $LICENSE['url'] . "\n");
fwrite($fh, "# Attribution: " . $LICENSE['attribution'] . "\n");
fwrite($fh, "# Generated " . date('Y-m-d') . " from " . $SITE . ", " . count($rows) . " records\n");
$cols = ['id', 'title', 'kind', 'byline', 'bylineRole', 'bylineUrl', 'pieces',
         'eraFrom', 'eraTo', 'eraCount', 'url', 'updated'];
fputcsv($fh, $cols);
foreach ($rows as $r) {
    $line = [];
    foreach ($cols as $c) { $line[] = $r[$c]; }
    fputcsv($fh, $line);
}
fclose($fh);
@chmod($csvPath, 0644);

$backJson = json_decode(file_get_contents($jsonPath), true);
$backCsv = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$csvRows = count(array_filter($backCsv, fn($l) => $l !== '' && $l[0] !== '#')) - 1;

echo PHP_EOL . 'READ-BACK' . PHP_EOL;
printf("   %-24s %-18s %s\n", 'json records', count($backJson['records'] ?? []) . ' of ' . count($rows),
    count($backJson['records'] ?? []) === count($rows) ? 'pass' : 'FAIL');
printf("   %-24s %-18s %s\n", 'csv rows', $csvRows . ' of ' . count($rows), $csvRows === count($rows) ? 'pass' : 'FAIL');
foreach ([$jsonPath, $csvPath] as $f) {
    printf("   %-24s %-18s %s\n", basename($f), substr(sprintf('%o', fileperms($f)), -4),
        substr(sprintf('%o', fileperms($f)), -4) === '0644' ? 'pass' : 'FAIL');
}

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('build_collections_export.php', count($rows),
    (count($backJson['records'] ?? []) === count($rows) && $csvRows === count($rows) ? 'verified: ' : 'FAILED: ')
        . 'json ' . count($backJson['records'] ?? []) . ', csv ' . $csvRows,
    '/data/collections.json and .csv');
