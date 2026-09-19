/**
 * Reads fixes.json back in, so notes taken on staging survive into local.
 *
 * Matches on the note text. A note is the thing a person typed and it is what
 * makes one note the same as another; ids are not stable across two databases
 * and dates are not either, since a re-export carries the original created
 * date but an import cannot set it.
 *
 * So: a note whose text already exists here is left completely alone, including
 * its status. If it was marked done locally and is still open in the export, it
 * stays done, because the local judgement is the later one and the import has
 * no business overruling it.
 *
 * The record a note points at is re-found by legacy URL first, then by section
 * and slug. A note whose record cannot be found here still imports, with the
 * record left empty and the legacy URL kept, because a note about something
 * that does not exist here is exactly the kind a field could never hold.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_fixes.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$IN = \Craft::getAlias('@webroot') . '/review/fixes.json';

$section = Craft::$app->entries->getSectionByHandle('fixes');
$type = Craft::$app->entries->getEntryTypeByHandle('fix');
if (!$section || !$type) {
    echo 'the fixes section does not exist here. Run add_fixes_section.php first.' . PHP_EOL;
    return;
}
if (!file_exists($IN)) {
    echo 'not found: web/review/fixes.json' . PHP_EOL;
    echo 'Export it on the other machine with export_fixes.php and copy it here.' . PHP_EOL;
    return;
}

$doc = json_decode(file_get_contents($IN), true);
$incoming = $doc['fixes'] ?? [];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'read ' . count($incoming) . ' notes from fixes.json'
    . (isset($doc['meta']['from']) ? ', exported from ' . $doc['meta']['from'] : '') . PHP_EOL;

/* What is here already, keyed on the note text, normalised so a difference of
   whitespace or case is not a difference of note. */
$key = fn(string $s): string => preg_replace('~\s+~u', ' ', mb_strtolower(trim($s)));
$existing = [];
foreach (\craft\elements\Entry::find()->section('fixes')->status(null)->limit(null)->all() as $f) {
    $existing[$key((string)$f->fixNote)] = $f;
}
echo 'notes already here: ' . count($existing) . PHP_EOL . PHP_EOL;

$URL_FIELDS = ['legacyUrl', 'placeLegacyUrl', 'personLegacyUrl', 'orgLegacyUrl',
               'groupLegacyUrl', 'eventLegacyUrl', 'obitLegacyUrl', 'mpLegacyUrl'];

$findRecord = function (?array $about) use ($URL_FIELDS): ?\craft\elements\Entry {
    if (!$about) { return null; }
    if (!empty($about['legacyUrl'])) {
        foreach ($URL_FIELDS as $h) {
            $e = \craft\elements\Entry::find()->status(null)->$h($about['legacyUrl'])->one();
            if ($e) { return $e; }
        }
    }
    if (!empty($about['section']) && !empty($about['slug'])) {
        $e = \craft\elements\Entry::find()->section($about['section'])->slug($about['slug'])->status(null)->one();
        if ($e) { return $e; }
    }
    return null;
};

$new = []; $skipped = []; $noRecord = [];
foreach ($incoming as $r) {
    $note = trim((string)($r['note'] ?? ''));
    if ($note === '') { continue; }
    if (isset($existing[$key($note)])) {
        $skipped[] = $note;
        continue;
    }
    $rec = $findRecord($r['about'] ?? null);
    if (($r['about'] ?? null) && !$rec) { $noRecord[] = $note . '  -> ' . ($r['about']['title'] ?? '?'); }
    $new[] = ['row' => $r, 'note' => $note, 'rec' => $rec];
}

echo 'would create: ' . count($new) . PHP_EOL;
foreach ($new as $n) {
    echo '  + ' . str_pad($n['row']['status'] ?? 'open', 9)
        . mb_substr(preg_replace('~\s+~', ' ', $n['note']), 0, 74) . PHP_EOL;
    echo '      ' . ($n['rec']
        ? 'attached to ' . $n['rec']->section->handle . '/' . $n['rec']->slug . ' #' . $n['rec']->id
        : (($n['row']['legacyUrl'] ?? '') ?: 'nothing attached')) . PHP_EOL;
}
echo PHP_EOL . 'already here, left completely alone including status: ' . count($skipped) . PHP_EOL;
foreach (array_slice($skipped, 0, 8) as $s) {
    echo '  = ' . mb_substr(preg_replace('~\s+~', ' ', $s), 0, 80) . PHP_EOL;
}
if (count($skipped) > 8) { echo '  and ' . (count($skipped) - 8) . ' more' . PHP_EOL; }

if ($noRecord) {
    echo PHP_EOL . 'pointed at a record that does not exist here, imported without one: ' . count($noRecord) . PHP_EOL;
    foreach ($noRecord as $r) { echo '  ! ' . mb_substr($r, 0, 90) . PHP_EOL; }
}

if (!$APPLY) {
    echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$elements = Craft::$app->getElements();
$made = 0; $failed = 0;
foreach ($new as $n) {
    $e = new \craft\elements\Entry();
    $e->sectionId = $section->id;
    $e->setTypeId($type->id);
    $set = [
        'fixNote' => $n['note'],
        'fixStatus' => $n['row']['status'] ?? 'open',
        'fixLegacyUrl' => (string)($n['row']['legacyUrl'] ?? ''),
        'fixSeenOn' => (string)($n['row']['seenOn'] ?? ''),
    ];
    if ($n['rec']) { $set['fixRecord'] = [$n['rec']->id]; }
    $e->setFieldValues($set);
    if ($elements->saveElement($e)) { $made++; }
    else { $failed++; echo 'FAILED: ' . json_encode($e->getErrors()) . PHP_EOL; }
}
echo PHP_EOL . 'created ' . $made . ', failed ' . $failed . ', left alone ' . count($skipped) . PHP_EOL;
echo 'A second run creates nothing: every note is now matched by its text.' . PHP_EOL;
