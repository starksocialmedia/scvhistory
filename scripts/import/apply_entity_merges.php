/**
 * Reads web/review/merged.json produced by the entity review screen and merges
 * each approved pair: every relation pointing at the losing record is moved
 * onto the survivor, the losing title is appended to the survivor's alias field
 * where the type has one, and the loser is deleted.
 *
 * This is the destructive half of the workflow, so:
 *   - dry run by default; it prints exactly what it would move before moving it
 *   - it refuses to merge a record with itself
 *   - it skips any pair where either record no longer exists, which is what
 *     makes a second run over the same file a no-op
 *   - a title already present in the alias field is not appended twice
 *   - relations are moved one at a time and any that would duplicate an
 *     existing relation on the survivor are dropped rather than duplicated
 *
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_entity_merges.php'))"
 */

$APPLY = false;

$ALIAS_FIELDS = [
    'places'        => 'placeAliases',
    'organizations' => 'orgAliases',
    // persons has no alias field, only fullName, which is the canonical name
    // rather than a list, so a losing person title is reported and not stored.
];

$file = \Craft::getAlias('@webroot') . '/review/merged.json';
if (!file_exists($file)) {
    echo 'ERROR: web/review/merged.json not found. Download it from the review screen first.' . PHP_EOL;
    return;
}
$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) { echo 'ERROR: merged.json is not valid JSON' . PHP_EOL; return; }

$elements = Craft::$app->getElements();
$db = Craft::$app->getDb();

$merged = 0; $skipped = 0; $relationsMoved = 0; $relationsDropped = 0; $aliasWrites = 0;
$notes = [];

echo ($APPLY ? '=== APPLYING, this deletes records ===' : '=== DRY RUN, set $APPLY = true to write ===') . PHP_EOL . PHP_EOL;

foreach ($data as $n => $row) {
    if (($row['action'] ?? '') !== 'merge') { continue; }

    $keepId = (int)($row['keepId'] ?? 0);
    $loseId = (int)($row['loseId'] ?? 0);

    if (!$keepId || !$loseId) {
        echo '  row ' . $n . ': missing keepId or loseId, skipping' . PHP_EOL; $skipped++; continue;
    }
    if ($keepId === $loseId) {
        echo '  row ' . $n . ': keepId equals loseId, refusing to merge a record with itself' . PHP_EOL; $skipped++; continue;
    }

    $keep = \craft\elements\Entry::find()->id($keepId)->status(null)->one();
    $lose = \craft\elements\Entry::find()->id($loseId)->status(null)->one();

    if (!$keep || !$lose) {
        echo sprintf('  %s <- %s: %s no longer exists, skipping (already merged?)',
            $row['keepTitle'] ?? $keepId, $row['loseTitle'] ?? $loseId,
            !$keep ? 'survivor' : 'loser') . PHP_EOL;
        $skipped++; continue;
    }

    $keepSection = $keep->getSection()->handle;
    $loseSection = $lose->getSection()->handle;
    if ($keepSection !== $loseSection) {
        echo sprintf('  %s <- %s: different sections (%s, %s), skipping',
            $keep->title, $lose->title, $keepSection, $loseSection) . PHP_EOL;
        $skipped++; continue;
    }

    echo sprintf('  %s  <-  %s   [%s]', $keep->title, $lose->title, $keepSection) . PHP_EOL;

    /* relations pointing AT the loser */
    $incoming = (new \craft\db\Query())
        ->select(['id', 'fieldId', 'sourceId', 'sourceSiteId', 'sortOrder'])
        ->from('{{%relations}}')
        ->where(['targetId' => $loseId])
        ->all();

    $moveIds = []; $dropIds = [];
    foreach ($incoming as $rel) {
        if ((int)$rel['sourceId'] === $keepId) { $dropIds[] = $rel['id']; continue; }
        $exists = (new \craft\db\Query())
            ->from('{{%relations}}')
            ->where([
                'fieldId' => $rel['fieldId'],
                'sourceId' => $rel['sourceId'],
                'targetId' => $keepId,
            ])
            ->exists();
        if ($exists) { $dropIds[] = $rel['id']; } else { $moveIds[] = $rel['id']; }
    }

    $fieldNames = [];
    foreach ($incoming as $rel) {
        $f = Craft::$app->getFields()->getFieldById($rel['fieldId']);
        $h = $f ? $f->handle : ('field ' . $rel['fieldId']);
        $fieldNames[$h] = ($fieldNames[$h] ?? 0) + 1;
    }
    foreach ($fieldNames as $h => $cnt) {
        echo '      via ' . str_pad($h, 26) . $cnt . PHP_EOL;
    }
    echo '      relations to move: ' . count($moveIds) . ', to drop as duplicates: ' . count($dropIds) . PHP_EOL;

    /* alias */
    $aliasHandle = $ALIAS_FIELDS[$keepSection] ?? null;
    $aliasAction = null;
    if ($aliasHandle) {
        $layout = [];
        foreach ($keep->getFieldLayout()->getCustomFields() as $f) { $layout[] = $f->handle; }
        if (in_array($aliasHandle, $layout, true)) {
            $current = '';
            try { $current = trim((string)$keep->getFieldValue($aliasHandle)); } catch (\Throwable $e) { $current = ''; }
            $parts = array_values(array_filter(array_map('trim', explode(',', $current))));
            $already = false;
            foreach ($parts as $p) {
                if (mb_strtolower($p) === mb_strtolower($lose->title)) { $already = true; break; }
            }
            if ($already) {
                $aliasAction = 'already lists "' . $lose->title . '"';
            } else {
                $parts[] = $lose->title;
                $aliasAction = 'set ' . $aliasHandle . ' to "' . implode(', ', $parts) . '"';
                $newAlias = implode(', ', $parts);
            }
        } else {
            $aliasAction = $aliasHandle . ' not on this layout, nothing recorded';
        }
    } else {
        $aliasAction = 'no alias field on ' . $keepSection . ', the losing title will not be recorded';
        $notes[] = $keepSection . ' has no alias field, so "' . $lose->title . '" is lost on merge';
    }
    echo '      alias: ' . $aliasAction . PHP_EOL;
    echo '      then delete ' . $lose->title . ' (id ' . $loseId . ')' . PHP_EOL;

    if (!$APPLY) { $merged++; continue; }

    $tx = $db->beginTransaction();
    try {
        if ($moveIds) {
            $db->createCommand()->update('{{%relations}}', ['targetId' => $keepId], ['id' => $moveIds])->execute();
            $relationsMoved += count($moveIds);
        }
        if ($dropIds) {
            $db->createCommand()->delete('{{%relations}}', ['id' => $dropIds])->execute();
            $relationsDropped += count($dropIds);
        }
        if (isset($newAlias)) {
            $keep->setFieldValue($aliasHandle, $newAlias);
            if (!$elements->saveElement($keep)) {
                throw new \Exception('could not save survivor: ' . json_encode($keep->getErrors()));
            }
            $aliasWrites++;
            unset($newAlias);
        }
        if (!$elements->deleteElement($lose)) {
            throw new \Exception('could not delete loser');
        }
        $tx->commit();
        $merged++;
        echo '      done' . PHP_EOL;
    } catch (\Throwable $e) {
        $tx->rollBack();
        echo '      FAILED, rolled back: ' . $e->getMessage() . PHP_EOL;
        $skipped++;
    }
    unset($newAlias);
}

echo PHP_EOL . ($APPLY ? 'APPLIED' : 'DRY RUN') . ': ' . $merged . ' merge' . ($merged === 1 ? '' : 's')
   . ', ' . $skipped . ' skipped' . PHP_EOL;
if ($APPLY) {
    echo 'relations moved: ' . $relationsMoved . ', duplicates dropped: ' . $relationsDropped
       . ', alias fields written: ' . $aliasWrites . PHP_EOL;
}
foreach (array_unique($notes) as $note) { echo 'NOTE: ' . $note . PHP_EOL; }
if (!$APPLY) {
    echo PHP_EOL . 'Nothing was changed. Set $APPLY = true and run again.' . PHP_EOL;
} else {
    echo 'Re-run export_entity_candidates.php to refresh the review screen.' . PHP_EOL;
}
