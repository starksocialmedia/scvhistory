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
 *   - a source that already points at the survivor keeps a single relation
 *     rather than gaining a duplicate
 *
 * Relations are moved by re-saving each source element through Craft, not by
 * updating the relations table. Craft 5 keeps a relation field's target ids in
 * two places, the relations table and the element's own content JSON, and a raw
 * SQL update touches only the first. The two then disagree and Craft keeps
 * reading the stale JSON, so the merge looks applied in the database and has no
 * effect on the site.
 *
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_entity_merges.php'))"
 */

$APPLY = false;

// Alias field per section, and how that field separates names. placeAliases
// and orgAliases are single line and comma separated; personAliases is
// multiline, one name per line. Presence is checked against each survivor's
// own layout, so a field named here but not yet created is reported and
// skipped rather than written to.
$ALIAS_FIELDS = [
    'persons'       => ['handle' => 'personAliases', 'separator' => "\n",  'join' => "\n"],
    'places'        => ['handle' => 'placeAliases',  'separator' => ',',   'join' => ', '],
    'organizations' => ['handle' => 'orgAliases',    'separator' => ',',   'join' => ', '],
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

    /* every source that points AT the loser, and through which field */
    $incoming = (new \craft\db\Query())
        ->select(['fieldId', 'sourceId'])
        ->distinct()
        ->from('{{%relations}}')
        ->where(['targetId' => $loseId])
        ->all();

    // canonical elements only; Craft rewrites a revision's own relations when
    // the canonical element is saved
    $work = []; $fieldNames = []; $skippedSources = 0;
    foreach ($incoming as $rel) {
        $srcId = (int)$rel['sourceId'];
        if ($srcId === $keepId) { continue; }
        $src = \craft\elements\Entry::find()->id($srcId)->status(null)->one();
        if (!$src) { $skippedSources++; continue; }
        if ($src->getIsRevision() || $src->getIsDraft()) { continue; }
        $field = Craft::$app->getFields()->getFieldById($rel['fieldId']);
        if (!$field) { $skippedSources++; continue; }
        $work[$srcId]['element'] = $src;
        $work[$srcId]['fields'][$field->handle] = true;
        $fieldNames[$field->handle] = ($fieldNames[$field->handle] ?? 0) + 1;
    }

    foreach ($fieldNames as $h => $cnt) {
        echo '      via ' . str_pad($h, 26) . $cnt . PHP_EOL;
    }
    echo '      source records to rewrite: ' . count($work)
       . ($skippedSources ? ', ' . $skippedSources . ' unreadable and skipped' : '') . PHP_EOL;

    /* alias */
    $aliasCfg = $ALIAS_FIELDS[$keepSection] ?? null;
    $aliasHandle = $aliasCfg['handle'] ?? null;
    $aliasAction = null;
    if ($aliasHandle) {
        $layout = [];
        foreach ($keep->getFieldLayout()->getCustomFields() as $f) { $layout[] = $f->handle; }
        if (in_array($aliasHandle, $layout, true)) {
            $current = '';
            try { $current = trim((string)$keep->getFieldValue($aliasHandle)); } catch (\Throwable $e) { $current = ''; }
            // split on this field's own separator, and on newlines either way,
            // so a comma field that has been hand edited onto several lines
            // still compares correctly
            $rawParts = preg_split('/[\r\n' . preg_quote($aliasCfg['separator'], '/') . ']+/u', $current);
            $parts = array_values(array_filter(array_map('trim', $rawParts), fn($x) => $x !== ''));
            $already = false;
            foreach ($parts as $p) {
                if (mb_strtolower($p) === mb_strtolower($lose->title)) { $already = true; break; }
            }
            if ($already) {
                $aliasAction = $aliasHandle . ' already lists "' . $lose->title . '"';
            } else {
                $parts[] = $lose->title;
                $newAlias = implode($aliasCfg['join'], $parts);
                $aliasAction = 'append "' . $lose->title . '" to ' . $aliasHandle
                             . ' (' . count($parts) . ' name' . (count($parts) === 1 ? '' : 's') . ')';
            }
        } else {
            $aliasAction = $aliasHandle . ' is not on this layout, so "' . $lose->title . '" will not be recorded';
            $notes[] = $aliasHandle . ' does not exist on ' . $keepSection . ' yet, so losing titles are not preserved';
        }
    } else {
        $aliasAction = 'no alias field mapped for ' . $keepSection . ', the losing title will not be recorded';
        $notes[] = $keepSection . ' has no alias field, so losing titles are not preserved';
    }
    echo '      alias: ' . $aliasAction . PHP_EOL;
    echo '      then delete ' . $lose->title . ' (id ' . $loseId . ')' . PHP_EOL;

    if (!$APPLY) { $merged++; continue; }

    $tx = $db->beginTransaction();
    try {
        foreach ($work as $srcId => $info) {
            $src = $info['element'];
            $changed = false;
            foreach (array_keys($info['fields']) as $handle) {
                $ids = $src->getFieldValue($handle)->status(null)->ids();
                if (!in_array($loseId, $ids, true)) { continue; }
                $out = [];
                foreach ($ids as $id) {
                    $id = ((int)$id === $loseId) ? $keepId : (int)$id;
                    if (!in_array($id, $out, true)) { $out[] = $id; }   // no duplicate
                }
                if ($out === array_map('intval', $ids)) { continue; }
                $src->setFieldValue($handle, $out);
                $relationsMoved++;
                if (count($out) < count($ids)) { $relationsDropped++; }
                $changed = true;
            }
            if ($changed && !$elements->saveElement($src)) {
                throw new \Exception('could not save source ' . $src->slug . ': ' . json_encode($src->getErrors()));
            }
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
    echo 'relation fields rewritten: ' . $relationsMoved . ', duplicates collapsed: ' . $relationsDropped
       . ', alias fields written: ' . $aliasWrites . PHP_EOL;
}
foreach (array_unique($notes) as $note) { echo 'NOTE: ' . $note . PHP_EOL; }
if (!$APPLY) {
    echo PHP_EOL . 'Nothing was changed. Set $APPLY = true and run again.' . PHP_EOL;
} else {
    echo 'Re-run export_entity_candidates.php to refresh the review screen.' . PHP_EOL;
}
