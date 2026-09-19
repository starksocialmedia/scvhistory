/**
 * Pulls every fix note out as JSON, so the list survives a content refresh.
 *
 * The workflow this exists for: browse staging, note problems there, export
 * before a content refresh, import into local. Staging is where the reviewing
 * happens and local is where the work happens, and the notes have to cross that
 * gap because a content refresh is a whole-database import from local and
 * overwrites everything on the server.
 *
 * Read only. Writes web/review/fixes.json and touches nothing in Craft.
 *
 * The record a note points at is exported by its legacy URL and its slug, not
 * by its id. Ids are not stable across the two databases: entry #819 on staging
 * and entry #819 on local are the same record today only because one is a copy
 * of the other, and the moment a record is created on either side that stops
 * being true. import_fixes.php re-finds the record by those instead.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/export_fixes.php'))"
 */

$OUT = \Craft::getAlias('@webroot') . '/review/fixes.json';

$section = Craft::$app->entries->getSectionByHandle('fixes');
if (!$section) {
    echo 'the fixes section does not exist here. Nothing to export.' . PHP_EOL;
    return;
}

$hasField = function (\craft\base\ElementInterface $el, string $h): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

$URL_FIELDS = ['legacyUrl', 'placeLegacyUrl', 'personLegacyUrl', 'orgLegacyUrl',
               'groupLegacyUrl', 'eventLegacyUrl', 'obitLegacyUrl', 'mpLegacyUrl'];

$rows = [];
foreach (\craft\elements\Entry::find()->section('fixes')->status(null)->limit(null)
             ->orderBy('dateCreated asc')->all() as $f) {
    $rec = $f->fixRecord->one();
    $about = null;
    if ($rec) {
        $legacy = '';
        foreach ($URL_FIELDS as $h) {
            if ($legacy === '' && $hasField($rec, $h)) {
                $v = trim((string)$rec->getFieldValue($h));
                if ($v !== '') { $legacy = $v; }
            }
        }
        $about = [
            'section' => $rec->section->handle,
            'slug' => $rec->slug,
            'title' => (string)$rec->title,
            'legacyUrl' => $legacy,
        ];
    }
    $rows[] = [
        'note' => (string)$f->fixNote,
        'status' => $f->fixStatus ? $f->fixStatus->value : 'open',
        'legacyUrl' => (string)$f->fixLegacyUrl,
        'seenOn' => (string)$f->fixSeenOn,
        'created' => $f->dateCreated ? $f->dateCreated->format('c') : '',
        'about' => $about,
    ];
}

file_put_contents($OUT, json_encode([
    'meta' => [
        'generated' => (new DateTime())->format('c'),
        'generated_by' => 'scripts/import/export_fixes.php',
        'from' => Craft::$app->getSites()->getPrimarySite()->getBaseUrl(),
        'count' => count($rows),
    ],
    'fixes' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo 'wrote web/review/fixes.json, ' . count($rows) . ' notes' . PHP_EOL;
$byStatus = [];
foreach ($rows as $r) { $byStatus[$r['status']] = ($byStatus[$r['status']] ?? 0) + 1; }
foreach ($byStatus as $k => $n) { echo '   ' . str_pad($k, 10) . $n . PHP_EOL; }
$orphan = count(array_filter($rows, fn($r) => $r['about'] === null));
if ($orphan) { echo '   ' . str_pad('no record', 10) . $orphan . '  (kept: a note about a page with no record is the point)' . PHP_EOL; }
echo PHP_EOL . 'Copy it to the other machine and run import_fixes.php there.' . PHP_EOL;
