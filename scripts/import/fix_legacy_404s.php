/**
 * Corrects three legacy URLs that answer 404, where the sitemaps give an
 * unambiguous replacement that was requested and answers 200.
 *
 *   Beale's Cut Stagecoach Pass  placeLegacyUrl
 *     /st_lat110440.htm -> /scvhistory/st_lat110440.htm
 *     The same missing /scvhistory/ segment that broke Chapter 15. The sitemap
 *     has the corrected path titled "SCVHistory.com | Beale's Cut | Landmark
 *     Status for Beale's Cut". This record's legacyUrl is a different page,
 *     bealescut.htm, which resolves and is left alone.
 *
 *   otn-patti and otn-whyte  legacyUrl and sourcePath
 *     /oldtownnewhall/<name>/index.html -> /oldtownnewhall/<name>/
 *     index.html never existed for either. The sibling collections pauline and
 *     rioux carry both a directory and an index.htm; patti and whyte carry only
 *     the directory, so index.html was a guess when the records were made.
 *
 * The Northridge Earthquake record is deliberately not here. Its stored path,
 * /scvhistory/newhallpass.htm, answers 404 and appears in neither sitemap, and
 * nothing on the legacy site is a plausible substitute. A record can carry no
 * legacy URL; it should not carry a wrong one. RECORD-CHECKLIST.md records it
 * as a source that could not be located.
 *
 * Each field is written only while it still holds exactly the broken value, so
 * a correction made by hand in the meantime is never overwritten, and a second
 * run does nothing.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_legacy_404s.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/* section, slug, [handle, was, now] ... */
$FIX = [
    ['places', 'beales-cut-stagecoach-pass', [
        ['placeLegacyUrl', '/st_lat110440.htm', '/scvhistory/st_lat110440.htm'],
    ]],
    ['collections', 'otn-patti', [
        ['legacyUrl',  '/oldtownnewhall/patti/index.html', '/oldtownnewhall/patti/'],
        ['sourcePath', 'scvhistory.com/oldtownnewhall/patti/index.html', 'scvhistory.com/oldtownnewhall/patti/'],
    ]],
    ['collections', 'otn-whyte', [
        ['legacyUrl',  '/oldtownnewhall/whyte/index.html', '/oldtownnewhall/whyte/'],
        ['sourcePath', 'scvhistory.com/oldtownnewhall/whyte/index.html', 'scvhistory.com/oldtownnewhall/whyte/'],
    ]],
];

$elements = Craft::$app->getElements();
$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$changedFields = 0; $changedRecords = 0; $blocked = [];

foreach ($FIX as [$section, $slug, $fields]) {
    $e = \craft\elements\Entry::find()->section($section)->slug($slug)->status(null)->one();
    if (!$e) { echo $slug . ': NOT FOUND' . PHP_EOL . PHP_EOL; continue; }

    echo ($e->title ?: $slug) . '  (' . $section . ' #' . $e->id . ')' . PHP_EOL;
    $here = 0;

    foreach ($fields as [$handle, $was, $now]) {
        if (!$hasField($e, $handle)) { echo '  ' . str_pad($handle, 16) . 'no such field' . PHP_EOL; continue; }
        $current = trim((string)$e->getFieldValue($handle));

        if ($current === $now) { echo '  ' . str_pad($handle, 16) . 'already corrected' . PHP_EOL; continue; }
        if ($current !== $was) {
            echo '  ' . str_pad($handle, 16) . 'SKIP, holds "' . $current . '"' . PHP_EOL;
            echo '  ' . str_pad('', 16) . 'neither the broken value nor the fix; decide by hand' . PHP_EOL;
            continue;
        }
        echo '  ' . str_pad($handle, 16) . 'was:  ' . $current . PHP_EOL;
        echo '  ' . str_pad('', 16) . 'now:  ' . $now . PHP_EOL;
        $here++; $changedFields++;
        if ($APPLY) { $e->setFieldValue($handle, $now); }
    }

    if ($here) { $changedRecords++; }

    /* Craft will not save an entry with a blank title, and ten collections have
       one. Saying so is more use than a validation error, and saving without
       validation to get around it would write an invalid record on purpose. The
       URL fix is trivially re-runnable once the record has a title. */
    if ($APPLY && $here) {
        if (trim((string)$e->title) === '') {
            echo '  BLOCKED: this record has no title, and Craft will not save an entry' . PHP_EOL;
            echo '           without one. Title it first, then run this again. Nothing' . PHP_EOL;
            echo '           was written.' . PHP_EOL;
            $blocked[] = $section . '/' . $slug;
            $changedRecords--;
        } else {
            echo '  ' . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
        }
    }
    echo PHP_EOL;
}

echo str_repeat('=', 74) . PHP_EOL;
echo 'records that would change: ' . $changedRecords . PHP_EOL;
echo 'fields that would change:  ' . $changedFields . PHP_EOL;
if (!$APPLY) { echo 'nothing written.' . PHP_EOL; }
if ($blocked) {
    echo PHP_EOL . 'BLOCKED by a blank title, ' . count($blocked) . ':' . PHP_EOL;
    foreach ($blocked as $b) { echo '  ' . $b . PHP_EOL; }
    echo 'Ten collections carry no title at all. Naming a collection is a decision' . PHP_EOL;
    echo 'about the archive, not something an importer should guess, so these wait.' . PHP_EOL;
}

echo PHP_EOL . 'Not touched: Northridge Earthquake. Its legacy path 404s and the legacy' . PHP_EOL;
echo 'site has no replacement, so it stays as a recorded gap rather than a guess.' . PHP_EOL;
