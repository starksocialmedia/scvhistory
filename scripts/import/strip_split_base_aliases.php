/**
 * Removes a split's base name from the alias list of every record the split
 * produced.
 *
 * WHY THIS IS WRONG DATA AND NOT A TIDY-UP
 *
 * The canon splits four bare names by context: "Soledad" in one sentence is the
 * canyon, in the next it is the road, and which one is decided by the words
 * nearest it. The exporter then wrote the base name onto every target as an
 * alias, so the corpus ended up asserting that "Soledad" IS Soledad Canyon and
 * ALSO IS Soledad Canyon Road.
 *
 * That is the ambiguity the split exists to resolve, stored as though it had
 * been resolved. A prose linker reading the alias list finds two records and no
 * way to choose between them, and the per-sentence resolution -- the entire
 * point of the split -- is discarded at the last step. One alias pointing at
 * two records is not an alias.
 *
 * Where a target IS the base name, the base is that record's title, and a title
 * is not its own alias; those are left alone by comparing against the title.
 *
 * THE SOURCE IS ALREADY FIXED
 *
 * export_entity_candidates.php no longer writes it, and both queues have been
 * regenerated clean. This exists because batch three applied at 20:31, two
 * minutes before the queues were rebuilt, so seven records carry the alias in
 * the database where no regeneration can reach them.
 *
 * The bases come from the canon, so a split added later is covered without
 * anyone remembering this file exists.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/strip_split_base_aliases.php'))"
 */

$APPLY = false;

$ALIAS_FOR = ['persons' => 'personAliases', 'places' => 'placeAliases',
              'organizations' => 'orgAliases'];

$canon = json_decode(file_get_contents(
    \Craft::getAlias('@root') . '/inventory/legacy/name-canon.json'), true) ?: [];
$bases = [];
foreach (($canon['splits'] ?? []) as $sp) {
    $b = trim((string)($sp['name'] ?? ''));
    if ($b !== '') { $bases[mb_strtolower($b)] = $b; }
}

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;
echo 'split bases from the canon: ' . implode(', ', $bases) . PHP_EOL . PHP_EOL;

$targets = [];
foreach ($ALIAS_FOR as $sec => $fh) {
    foreach (\craft\elements\Entry::find()->section($sec)->status(null)->limit(null)->all() as $e) {
        $has = false;
        foreach ($e->getFieldLayout()->getCustomFields() as $f) { if ($f->handle === $fh) { $has = true; } }
        if (!$has) { continue; }
        $raw = (string)$e->getFieldValue($fh);
        if (trim($raw) === '') { continue; }
        $al = array_values(array_filter(array_map('trim', preg_split('~[\r\n]+~', $raw))));
        $keep = array_values(array_filter($al, fn($a) =>
            !isset($bases[mb_strtolower($a)]) || mb_strtolower($a) === mb_strtolower((string)$e->title)));
        if (count($keep) === count($al)) { continue; }
        $targets[] = ['entry' => $e, 'field' => $fh, 'was' => $al, 'keep' => $keep,
                      'drop' => array_values(array_diff($al, $keep))];
    }
}

printf("   %-8s %-34s %-22s %s\n", 'ID', 'TITLE', 'DROPPING', 'ALIASES AFTER');
foreach ($targets as $t) {
    printf("   #%-7d %-34s %-22s %s\n", $t['entry']->id, mb_substr($t['entry']->title, 0, 33),
        implode(', ', $t['drop']), $t['keep'] ? implode(', ', $t['keep']) : '(none)');
}
echo PHP_EOL . 'records to change: ' . count($targets) . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$saved = 0;
foreach ($targets as $t) {
    $t['entry']->setFieldValue($t['field'], implode("\n", $t['keep']));
    if (\Craft::$app->elements->saveElement($t['entry'])) { $saved++; }
}

/* Read back from a fresh query, not from the objects just saved: an element
   held in memory reports what was set on it whether or not it reached the
   database. */
$still = 0;
foreach ($targets as $t) {
    $e = \craft\elements\Entry::find()->id($t['entry']->id)->status(null)->one();
    $al = array_map('trim', preg_split('~[\r\n]+~', (string)$e->getFieldValue($t['field'])));
    foreach ($t['drop'] as $d) { if (in_array($d, $al, true)) { $still++; } }
}

echo PHP_EOL . 'READ-BACK' . PHP_EOL;
printf("   %-24s %-16s %s\n", 'records saved', $saved . ' of ' . count($targets),
    $saved === count($targets) ? 'pass' : 'FAIL');
printf("   %-24s %-16s %s\n", 'base aliases remaining', (string)$still, $still === 0 ? 'pass' : 'FAIL');

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('strip_split_base_aliases.php', $saved,
    ($saved === count($targets) && $still === 0 ? 'verified: ' : 'FAILED: ')
        . $saved . ' of ' . count($targets) . ' saved, ' . $still . ' base aliases left',
    'split bases: ' . implode(', ', $bases));
