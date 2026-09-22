/**
 * The footnotes Source column: adds the editorial option and stops defaulting
 * to Leon.
 *
 * The column offers Editor (Leon Worden), Original author, Webmaster note and
 * In the source document, and the first is the default. That default was
 * harmless while every note came from the legacy site. It stops being harmless
 * the moment SCVHistory writes its own notes in 2026: a row saved without a
 * thought is a row attributing an editorial note to a named person who did not
 * write it, and nothing on the page would say otherwise.
 *
 * So: a fifth option, and no default at all. Somebody chooses.
 *
 * Nothing is rewritten. Rows already carrying "editor" keep it, because this
 * script cannot know which of them Leon wrote; on 22 September there were no
 * real footnote rows in the archive to rewrite anyway (42 person records and
 * 661 article records carry a footnotes row, and every one is an empty
 * placeholder, so the whole archive holds four real notes).
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_footnote_source_options.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }

$HANDLE = 'footnotes';
$COLUMN = 'source';
$NEW = ['label' => 'SCVHistory editorial, 2026', 'value' => 'editorial-2026'];

$fs = Craft::$app->getFields();
$field = $fs->getFieldByHandle($HANDLE);
if (!$field instanceof \craft\fields\Table) { echo $HANDLE . ' is not a Table field' . PHP_EOL; return; }

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$columns = $field->columns;
$colKey = null;
foreach ($columns as $k => $c) { if (($c['handle'] ?? '') === $COLUMN) { $colKey = $k; } }
if ($colKey === null) { echo 'no column with handle ' . $COLUMN . PHP_EOL; return; }

echo 'column ' . $colKey . ' "' . $columns[$colKey]['heading'] . '", type ' . $columns[$colKey]['type'] . PHP_EOL;
echo 'now:' . PHP_EOL;
foreach ($columns[$colKey]['options'] as $o) {
    echo '   ' . str_pad($o['value'], 18) . str_pad($o['label'], 34) . (!empty($o['default']) ? 'DEFAULT' : '') . PHP_EOL;
}

$options = $columns[$colKey]['options'];
$have = false;
foreach ($options as $o) { if ($o['value'] === $NEW['value']) { $have = true; } }
if (!$have) { $options[] = ['label' => $NEW['label'], 'value' => $NEW['value'], 'default' => false]; }
foreach ($options as $i => $o) { $options[$i]['default'] = false; }

echo PHP_EOL . 'after:' . PHP_EOL;
foreach ($options as $o) {
    $mark = $o['value'] === $NEW['value'] && !$have ? '  <- new' : '';
    echo '   ' . str_pad($o['value'], 18) . str_pad($o['label'], 34) . (!empty($o['default']) ? 'DEFAULT' : 'no default') . $mark . PHP_EOL;
}

/* What is already filed under each value, so the change is made with the
   existing rows in view rather than in the abstract. */
$counts = []; $real = 0;
foreach (['articles', 'persons', 'photographs', 'documents', 'obituaries', 'warMemorials', 'militaryProfiles'] as $s) {
    if (!Craft::$app->getEntries()->getSectionByHandle($s)) { continue; }
    foreach (\craft\elements\Entry::find()->section($s)->status(null)->limit(null)->each() as $e) {
        if (!$e->getFieldLayout()?->getFieldByHandle('footnotes')) { continue; }
        foreach ((array)$e->getFieldValue('footnotes') as $row) {
            $note = trim((string)($row['note'] ?? $row['col2'] ?? ''));
            if ($note === '') { continue; }
            $real++;
            $v = (string)($row['source'] ?? $row['col3'] ?? '');
            $counts[$v === '' ? '(empty)' : $v] = ($counts[$v === '' ? '(empty)' : $v] ?? 0) + 1;
        }
    }
}
echo PHP_EOL . 'footnote rows carrying real note text: ' . $real . PHP_EOL;
foreach ($counts as $v => $n) { echo '   ' . str_pad($v, 18) . $n . PHP_EOL; }
echo 'none of them is rewritten by this script.' . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$columns[$colKey]['options'] = $options;
$field->columns = $columns;
if (!$fs->saveField($field)) { echo 'FAILED: ' . implode('; ', $field->getFirstErrors()) . PHP_EOL; return; }

Craft::$app->getFields()->refreshFields();
$fresh = Craft::$app->getFields()->getFieldByHandle($HANDLE);
$opts = [];
foreach ($fresh->columns as $c) { if (($c['handle'] ?? '') === $COLUMN) { $opts = $c['options']; } }
$values = array_column($opts, 'value');
$defaults = array_filter($opts, fn($o) => !empty($o['default']));
$ok = in_array($NEW['value'], $values, true) && count($defaults) === 0;
echo PHP_EOL . 'READ-BACK ' . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
echo '   options: ' . implode(', ', $values) . PHP_EOL;
echo '   defaults: ' . (count($defaults) ? implode(', ', array_column($defaults, 'value')) : 'none') . PHP_EOL;
if (!$ok) { throw new \RuntimeException('fix_footnote_source_options read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('fix_footnote_source_options.php', 1,
    'verified: ' . count($values) . ' options, no default',
    'added editorial-2026; default was "editor" (Leon Worden); ' . $real . ' real rows untouched');
