/**
 * Reads web/review/relations-decided.json produced by the review screen and wires
 * the approved relations onto the articles.
 *
 * Three decisions come back per candidate. "link" relates an existing record.
 * "create" makes the record, then relates it. "tag" creates nothing and relates
 * nothing; it is recorded so a second pass does not ask again.
 *
 * Only what the reviewer approved is created. Nothing is inferred, and a
 * candidate with no decision is left alone.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_relations.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$file = \Craft::getAlias('@webroot') . '/review/relations-decided.json';
if (!file_exists($file)) {
    echo 'ERROR: web/review/relations-decided.json not found. Download it from the review screen first.' . PHP_EOL;
    return;
}
$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) { echo 'ERROR: relations-decided.json is not valid JSON' . PHP_EOL; return; }

$SECTION_FOR = ['person' => 'persons', 'place' => 'places', 'organization' => 'organizations'];
$TYPE_FOR    = ['person' => 'person', 'place' => 'place', 'organization' => 'organization'];
$FIELD_FOR   = ['person' => 'subjectPerson', 'place' => 'depictsPlace', 'organization' => 'subjectOrganization'];

$elements = Craft::$app->getElements();

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

$norm = function (string $s): string {
    $s = mb_strtolower(trim($s));
    $s = preg_replace('~[^a-z0-9 ]+~u', ' ', $s);
    return trim(preg_replace('~\s+~', ' ', $s));
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo 'decisions in the file: ' . count($data) . PHP_EOL;

/* Group by article so each one is saved once, and collect the records to make. */
$byEntry = [];   /* entryId => field => [ids] */
$toCreate = [];  /* kind|normalised name => ['kind','name','entries'=>[]] */
$tags = 0; $bad = [];

foreach ($data as $d) {
    $entryId = (int)($d['entryId'] ?? 0);
    $kind = (string)($d['kind'] ?? '');
    $name = trim((string)($d['name'] ?? ''));
    $choice = (string)($d['choice'] ?? '');
    if (!$entryId || !isset($SECTION_FOR[$kind]) || $name === '') { $bad[] = json_encode($d); continue; }

    if ($choice === 'tag') { $tags++; continue; }

    if ($choice === 'link') {
        $targetId = (int)($d['targetId'] ?? 0);
        if (!$targetId) { $bad[] = 'link with no targetId: ' . $name; continue; }
        $byEntry[$entryId][$FIELD_FOR[$kind]][] = $targetId;
        continue;
    }

    if ($choice === 'create') {
        $key = $kind . '|' . $norm($name);
        if (!isset($toCreate[$key])) { $toCreate[$key] = ['kind' => $kind, 'name' => $name, 'entries' => []]; }
        $toCreate[$key]['entries'][] = $entryId;
        continue;
    }

    $bad[] = 'unknown choice "' . $choice . '" for ' . $name;
}

/* ---- records to create ---- */

echo '=== records to create ===' . PHP_EOL;
$createdIds = [];
foreach ($toCreate as $key => $c) {
    $section = Craft::$app->getEntries()->getSectionByHandle($SECTION_FOR[$c['kind']]);
    $type = Craft::$app->getEntries()->getEntryTypeByHandle($TYPE_FOR[$c['kind']]);
    if (!$section || !$type) { echo '  section or type missing for ' . $c['kind'] . PHP_EOL; continue; }

    /* The export may be stale, so look again before making a duplicate. */
    $existing = \craft\elements\Entry::find()->section($section->handle)->status(null)->title($c['name'])->one();
    if ($existing) {
        echo '  ' . str_pad($c['kind'], 14) . str_pad($c['name'], 38) . 'already exists as #' . $existing->id . ', linking instead' . PHP_EOL;
        $createdIds[$key] = $existing->id;
        continue;
    }

    echo '  ' . str_pad($c['kind'], 14) . str_pad($c['name'], 38) . 'new, for ' . count(array_unique($c['entries'])) . ' article(s)' . PHP_EOL;

    if ($APPLY) {
        $e = new \craft\elements\Entry();
        $e->sectionId = $section->id;
        $e->setTypeId($type->id);
        $e->title = $c['name'];
        if (!$elements->saveElement($e)) {
            echo '    SAVE FAILED: ' . json_encode($e->getErrors()) . PHP_EOL;
            continue;
        }
        $createdIds[$key] = $e->id;
    }
}

foreach ($toCreate as $key => $c) {
    if (!isset($createdIds[$key])) { continue; }
    foreach (array_unique($c['entries']) as $entryId) {
        $byEntry[$entryId][$FIELD_FOR[$c['kind']]][] = $createdIds[$key];
    }
}

/* ---- relations ---- */

echo '=== articles ===' . PHP_EOL;
$touched = 0; $added = 0; $failed = 0; $skipped = [];

foreach ($byEntry as $entryId => $fields) {
    $entry = \craft\elements\Entry::find()->id($entryId)->status(null)->one();
    if (!$entry) { $skipped[] = 'entry ' . $entryId . ' no longer exists'; continue; }

    $sets = []; $lines = [];
    foreach ($fields as $handle => $ids) {
        if (!$hasField($entry, $handle)) { $skipped[] = $entry->slug . ': no ' . $handle . ' field'; continue; }
        $current = [];
        try { foreach ($entry->$handle->all() as $r) { $current[] = $r->id; } }
        catch (\Throwable $ex) { $skipped[] = $entry->slug . ': could not read ' . $handle; continue; }
        $merged = array_values(array_unique(array_merge($current, array_map('intval', $ids))));
        if ($merged === $current) { continue; }
        $sets[$handle] = $merged;
        $lines[] = $handle . ' +' . (count($merged) - count($current));
        $added += count($merged) - count($current);
    }
    if (!count($sets)) { continue; }
    $touched++;
    echo '  ' . str_pad($entry->slug, 46) . implode(', ', $lines) . PHP_EOL;

    if ($APPLY) {
        foreach ($sets as $handle => $ids) {
            try { $entry->setFieldValue($handle, $ids); }
            catch (\Throwable $ex) { echo '    set ' . $handle . ' failed: ' . $ex->getMessage() . PHP_EOL; }
        }
        if (!$elements->saveElement($entry)) {
            echo '    SAVE FAILED ' . $entry->slug . ': ' . json_encode($entry->getErrors()) . PHP_EOL;
            $failed++;
        }
    }
}

echo '=== summary ===' . PHP_EOL;
echo 'decisions read:        ' . count($data) . PHP_EOL;
echo 'left as tags:          ' . $tags . ' (nothing created, nothing related)' . PHP_EOL;
echo 'records to create:     ' . count($toCreate) . PHP_EOL;
echo 'articles to touch:     ' . $touched . PHP_EOL;
echo 'relations to add:      ' . $added . PHP_EOL;
if ($failed) { echo 'saves failed:          ' . $failed . PHP_EOL; }
if ($skipped) {
    echo 'decisions that no longer match the data:' . PHP_EOL;
    foreach ($skipped as $s) { echo '  ' . $s . PHP_EOL; }
}
if ($bad) {
    echo 'rows that could not be read:' . PHP_EOL;
    foreach (array_slice($bad, 0, 20) as $b) { echo '  ' . $b . PHP_EOL; }
}
echo 'A relation already present is never added twice, so a second run is a no-op.' . PHP_EOL;
