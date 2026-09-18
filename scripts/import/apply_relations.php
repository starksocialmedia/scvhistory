/**
 * Reads web/review/relations-decided.json produced by the review screen and wires
 * the approved relations onto the articles.
 *
 * Four decisions come back per candidate. "link" relates an existing record.
 * "create" makes the record, then relates it. "same" says this spelling is a
 * variant of another name on the same article: nothing is created, and the
 * spelling is appended to the surviving record's alias field instead. "tag"
 * creates nothing and relates nothing; it is recorded so a second pass does not
 * ask again.
 *
 * "same" is the common case, not an edge case. The extraction raises
 * possible_same_person 155 times, and without it every one of those is two
 * decisions producing either a duplicate record or a silent tag.
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
$ALIAS_FOR   = ['person' => 'personAliases', 'place' => 'placeAliases', 'organization' => 'orgAliases'];
/* personAliases is multiline, one name per line. The place and organization
   alias fields are single line and hold a comma separated list. */
$ALIAS_SEP   = ['person' => "\n", 'place' => ', ', 'organization' => ', '];
$TYPE_FOR    = ['person' => 'person', 'place' => 'place', 'organization' => 'organization'];
$FIELD_FOR   = ['person' => 'subjectPerson', 'place' => 'depictsPlace', 'organization' => 'subjectOrganization'];

$elements = Craft::$app->getElements();
$skippedAlias = []; $failedAlias = 0;

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
$sameAs = [];    /* the variants, resolved after creates so a new record can take one */
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

    if ($choice === 'same') {
        $survivor = trim((string)($d['sameAs'] ?? ''));
        if ($survivor === '') { $bad[] = 'same as with no name chosen: ' . $name; continue; }
        $sameAs[] = [
            'kind' => $kind,
            'variant' => $name,
            'survivor' => $survivor,
            'survivorId' => (int)($d['sameAsTargetId'] ?? 0),
            'entryId' => $entryId,
        ];
        continue;
    }

    $bad[] = 'unknown choice "' . $choice . '" for ' . $name;
}

/* ---- records to create ---- */

echo '=== records to create ===' . PHP_EOL;
$createdIds = [];
$pending = [];   /* dry run only: placeholder id => the record it stands for */
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
    } else {
        /* A dry run that cannot show what a create leads to is not worth
           reading, so pending creates get a negative placeholder id. Nothing is
           written, and the relation and alias plans below become visible. */
        $createdIds[$key] = -(count($pending) + 1);
        $pending[$createdIds[$key]] = $c;
    }
}

foreach ($toCreate as $key => $c) {
    if (!isset($createdIds[$key])) { continue; }
    foreach (array_unique($c['entries']) as $entryId) {
        $byEntry[$entryId][$FIELD_FOR[$c['kind']]][] = $createdIds[$key];
    }
}

/* ---- variants: append to the survivor's aliases, create nothing ---- */

echo '=== spellings folded into another record ===' . PHP_EOL;
$aliasPlan = [];   /* survivorEntryId => ['kind'=>, 'names'=>[]] */
$noSurvivor = [];

foreach ($sameAs as $v) {
    $id = $v['survivorId'];
    if (!$id) {
        /* The survivor may be one of this run's creates, or already in Craft. */
        $key = $v['kind'] . '|' . $norm($v['survivor']);
        if (isset($createdIds[$key])) { $id = $createdIds[$key]; }
        else {
            $hit = \craft\elements\Entry::find()->section($SECTION_FOR[$v['kind']])->status(null)->title($v['survivor'])->one();
            if ($hit) { $id = $hit->id; }
        }
    }
    if (!$id) {
        $noSurvivor[] = '"' . $v['variant'] . '" is a spelling of "' . $v['survivor'] . '", which has no record and was not created';
        continue;
    }
    if (!isset($aliasPlan[$id])) { $aliasPlan[$id] = ['kind' => $v['kind'], 'names' => []]; }
    $aliasPlan[$id]['names'][] = $v['variant'];
}

$aliasAdded = 0;
foreach ($aliasPlan as $survivorId => $plan) {
    if ($survivorId < 0) {
        $p = $pending[$survivorId] ?? null;
        echo '  ' . str_pad($p ? $p['name'] : '(pending)', 38) . $ALIAS_FOR[$plan['kind']]
            . ' += ' . implode(', ', array_unique($plan['names'])) . '   (on the record this run would create)' . PHP_EOL;
        $aliasAdded += count(array_unique($plan['names']));
        continue;
    }
    $rec = \craft\elements\Entry::find()->id($survivorId)->status(null)->one();
    if (!$rec) { $skippedAlias[] = 'record ' . $survivorId . ' no longer exists'; continue; }
    $handle = $ALIAS_FOR[$plan['kind']];
    $sep = $ALIAS_SEP[$plan['kind']];
    if (!$hasField($rec, $handle)) { $skippedAlias[] = $rec->title . ': no ' . $handle . ' field'; continue; }

    $current = '';
    try { $current = trim((string)$rec->getFieldValue($handle)); } catch (\Throwable $ex) {}
    $have = [];
    foreach (preg_split('~\r\n|\n|\r|,~', $current) as $piece) {
        $piece = trim($piece);
        if ($piece !== '') { $have[$norm($piece)] = true; }
    }
    $have[$norm((string)$rec->title)] = true;

    $add = [];
    foreach (array_unique($plan['names']) as $n) {
        if (!isset($have[$norm($n)])) { $add[] = $n; $have[$norm($n)] = true; }
    }
    if (!count($add)) { continue; }

    $next = $current === '' ? implode($sep, $add) : $current . $sep . implode($sep, $add);
    $aliasAdded += count($add);
    echo '  ' . str_pad($rec->title, 38) . $handle . ' += ' . implode(', ', $add) . PHP_EOL;

    if ($APPLY) {
        try { $rec->setFieldValue($handle, $next); }
        catch (\Throwable $ex) { echo '    set failed: ' . $ex->getMessage() . PHP_EOL; continue; }
        if (!$elements->saveElement($rec)) {
            echo '    SAVE FAILED: ' . json_encode($rec->getErrors()) . PHP_EOL;
            $failedAlias++;
        }
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
        if (!$APPLY) {
            /* placeholders count toward the plan but are never saved */
            $merged = array_values(array_unique(array_merge($current, array_map('intval', $ids))));
        }
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
            $ids = array_values(array_filter($ids, fn($i) => (int)$i > 0));
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
echo 'folded as spellings:   ' . count($sameAs) . ', adding ' . $aliasAdded . ' alias(es)' . PHP_EOL;
echo 'records to create:     ' . count($toCreate) . PHP_EOL;
echo 'articles to touch:     ' . $touched . PHP_EOL;
echo 'relations to add:      ' . $added . PHP_EOL;
if ($failed) { echo 'saves failed:          ' . $failed . PHP_EOL; }
if ($noSurvivor) {
    echo 'spellings whose survivor has no record, nothing done:' . PHP_EOL;
    foreach ($noSurvivor as $n) { echo '  ' . $n . PHP_EOL; }
}
if ($skippedAlias) {
    echo 'aliases that could not be written:' . PHP_EOL;
    foreach ($skippedAlias as $n) { echo '  ' . $n . PHP_EOL; }
}
if ($skipped) {
    echo 'decisions that no longer match the data:' . PHP_EOL;
    foreach ($skipped as $s) { echo '  ' . $s . PHP_EOL; }
}
if ($bad) {
    echo 'rows that could not be read:' . PHP_EOL;
    foreach (array_slice($bad, 0, 20) as $b) { echo '  ' . $b . PHP_EOL; }
}
if ($failedAlias) { echo 'alias saves failed:    ' . $failedAlias . PHP_EOL; }
echo 'A relation already present is never added twice, and an alias already held is' . PHP_EOL;
echo 'never appended again, so a second run is a no-op.' . PHP_EOL;
