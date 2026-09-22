/**
 * Builds the public data exports at /data/.
 *
 * An archive that cannot be read by a machine is a website. These are the
 * records as a table: one row per record, the identifiers that tie it to other
 * people's data, and the URL to come back to. Somebody writing about the valley
 * should be able to take the whole person list, join it to Wikidata on the QID,
 * and get on with their work without scraping anything.
 *
 * JSON and CSV. JSON carries nesting and types; CSV opens in the spreadsheet
 * the county historical society actually uses.
 *
 * WHAT IS IN A ROW
 *
 * The identifiers, the type and level, the parent, how many articles name it,
 * and the canonical URL. Not the body text: this is a table of what the archive
 * holds, not a copy of it, and a reader who wants the text follows the URL.
 *
 * THE HEADER
 *
 * Both formats carry a provenance block naming the source, the licence, the
 * date and the count. A dataset without one becomes an orphan the first time
 * somebody copies it, and then turns up years later with nobody able to say
 * where it came from.
 *
 * Read only as far as the database goes: it writes files under web/data.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_data_exports.php'))"
 */

$APPLY = $EXPORT_APPLY ?? false;

$OUTDIR = \Craft::getAlias('@webroot') . '/data';
$SITE = rtrim(\craft\helpers\UrlHelper::siteUrl(), '/') . '/';

$SETS = [
    'people' => ['section' => 'persons', 'type' => 'person'],
    'places' => ['section' => 'places', 'type' => 'place'],
    'organizations' => ['section' => 'organizations', 'type' => 'organization'],
];

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
$val = function ($el, string $h) use ($has) {
    if (!$has($el, $h)) { return ''; }
    $v = $el->getFieldValue($h);
    if (is_object($v) && isset($v->value)) { return (string)$v->value; }
    return is_string($v) ? trim($v) : (is_numeric($v) ? (string)$v : '');
};

/* How many articles name this record, counted across every field an article
   uses to name one. */
$ARTICLE_FIELDS = ['subjectPerson', 'subjectOrganization', 'subjectGroup', 'depictsPlace',
                   'writtenBy', 'editedBy', 'publishedBy', 'articleEvents'];

echo ($APPLY ? 'WRITING' : 'DRY RUN') . '   -> ' . $OUTDIR . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

$summary = [];

foreach ($SETS as $name => $spec) {
    $rows = [];
    foreach (\craft\elements\Entry::find()->section($spec['section'])->status(null)
                 ->orderBy('title asc')->limit(null)->all() as $e) {

        $articles = 0;
        foreach ($ARTICLE_FIELDS as $fh) {
            $articles += \craft\elements\Entry::find()->section('articles')
                ->relatedTo(['targetElement' => $e, 'field' => $fh])->status(null)->count();
        }

        $parent = '';
        $parentUrl = '';
        if ($has($e, 'parentOrganization')) {
            $p = $e->parentOrganization->one();
            if ($p) { $parent = $p->title; $parentUrl = $p->url; }
        }

        $aliasField = ['person' => 'personAliases', 'place' => 'placeAliases',
                       'organization' => 'orgAliases'][$spec['type']];
        $aliases = array_values(array_filter(array_map('trim',
            preg_split('~\r\n|\n|\r~', $val($e, $aliasField)) ?: [])));

        $rows[] = [
            'id' => $e->id,
            'name' => (string)$e->title,
            'type' => $spec['type'],
            'subtype' => $val($e, $spec['type'] === 'place' ? 'placeType' : 'orgType'),
            'level' => $val($e, 'schoolLevel'),
            'aliases' => $aliases,
            'parent' => $parent,
            'parentUrl' => $parentUrl,
            'wikidataId' => $val($e, 'wikidataId'),
            'viafId' => $val($e, 'viafId'),
            'gnisId' => $val($e, 'gnisId'),
            'ein' => $val($e, 'ein'),
            'cdsCode' => $val($e, 'cdsCode'),
            'ncesId' => $val($e, 'ncesId'),
            'nrhpReference' => $val($e, 'nrhpReference'),
            'articleCount' => $articles,
            'url' => (string)$e->url,
            'provenance' => $val($e, 'recordProvenance'),
            'updated' => $e->dateUpdated->format('Y-m-d'),
        ];
    }

    $doc = [
        'provenance' => [
            'title' => 'SCVHistory.com ' . $name,
            'source' => 'SCVHistory.com, the Santa Clarita Valley History Archive',
            'url' => $SITE,
            'generated' => (new DateTime())->format('c'),
            'generated_by' => 'scripts/import/build_data_exports.php',
            'records' => count($rows),
            'license' => $LICENSE,
            'note' => 'One row per record. Identifiers are the authority files this archive has '
                . 'matched a record to, and an empty one means no match has been made, not that '
                . 'none exists. articleCount is how many articles name the record through any '
                . 'relation. Follow url for the record itself; this table is what the archive '
                . 'holds, not a copy of it.',
        ],
        'records' => $rows,
    ];

    $summary[$name] = [
        'rows' => count($rows),
        'withId' => count(array_filter($rows, fn($r) => $r['wikidataId'] || $r['gnisId'] || $r['ein'] || $r['ncesId'] || $r['viafId'])),
        'withParent' => count(array_filter($rows, fn($r) => $r['parent'] !== '')),
        'withSubtype' => count(array_filter($rows, fn($r) => $r['subtype'] !== '')),
        'articles' => array_sum(array_column($rows, 'articleCount')),
    ];

    if (!$APPLY) { continue; }

    if (!is_dir($OUTDIR)) { mkdir($OUTDIR, 0775, true); }

    /* 0644. PHP's umask in the container gives these 0600, which the web server
       cannot read, so the export published fine and served a 403: a public
       dataset nobody outside the container could open. */
    $jsonPath = $OUTDIR . '/' . $name . '.json';
    file_put_contents($jsonPath,
        json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
    @chmod($jsonPath, 0644);

    /* CSV carries the provenance as comment lines above the header. A
       spreadsheet shows them as a first column and a reader can see where the
       file came from without opening anything else. */
    $fh = fopen($OUTDIR . '/' . $name . '.csv', 'w');
    fwrite($fh, "# SCVHistory.com " . $name . "\n");
    fwrite($fh, "# " . $LICENSE['name'] . " (" . $LICENSE['id'] . "), " . $LICENSE['url'] . "\n");
    fwrite($fh, "# Attribution: " . $LICENSE['attribution'] . "\n");
    fwrite($fh, "# Generated " . date('Y-m-d') . " from " . $SITE . ", " . count($rows) . " records\n");
    $cols = ['id', 'name', 'type', 'subtype', 'level', 'aliases', 'parent', 'wikidataId',
             'viafId', 'gnisId', 'ein', 'cdsCode', 'ncesId', 'nrhpReference',
             'articleCount', 'url', 'provenance', 'updated'];
    fputcsv($fh, $cols);
    foreach ($rows as $r) {
        $line = [];
        foreach ($cols as $c) { $line[] = is_array($r[$c]) ? implode('; ', $r[$c]) : $r[$c]; }
        fputcsv($fh, $line);
    }
    fclose($fh);
    @chmod($OUTDIR . '/' . $name . '.csv', 0644);
}

printf("%-16s %-7s %-10s %-10s %-10s %s\n", 'SET', 'ROWS', 'WITH ID', 'SUBTYPE', 'PARENT', 'ARTICLE LINKS');
foreach ($summary as $n => $s) {
    printf("%-16s %-7d %-10d %-10d %-10d %d\n", $n, $s['rows'], $s['withId'], $s['withSubtype'], $s['withParent'], $s['articles']);
}

if (!$APPLY) {
    echo PHP_EOL . 'DRY RUN. Nothing was written. Set $EXPORT_APPLY = true to write.' . PHP_EOL;
    return;
}
echo PHP_EOL . 'wrote:' . PHP_EOL;
foreach (array_keys($SETS) as $n) {
    foreach (['json', 'csv'] as $ext) {
        $p = $OUTDIR . '/' . $n . '.' . $ext;
        printf("   %-28s %-12s mode %s\n", '/data/' . $n . '.' . $ext,
            file_exists($p) ? number_format(filesize($p)) . ' bytes' : 'MISSING',
            file_exists($p) ? substr(sprintf('%o', fileperms($p)), -4) : '-');
    }
}
