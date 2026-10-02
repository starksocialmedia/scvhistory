/**
 * Namesakes (Nathan, 1 October 2026: "things named after people should link to
 * them. Smyth Drive for Clyde Smyth, Pico Canyon for Andrés Pico, Mentryville
 * for Alex Mentry, Newhall for Henry Mayo Newhall ... Build the relation").
 *
 *   namedFor    Entries (persons), on places and organizations: the person the
 *               place or body is named for. Not placePeople, which says who was
 *               associated with a place, not whose name it carries.
 *   namingNote  PlainText: what the sources say about the naming, with the
 *               source, and every explanation where they differ (Pico Canyon
 *               has three).
 *
 * The person page lists what is named for them; the place page says whom it is
 * named for. Schema only; run in its own request before anything sets them.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_named_for_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields(); $svc = Craft::$app->getEntries();
$persons = $svc->getSectionByHandle('persons');
$TYPES = ['place', 'organization'];
$plan = [];
if (!$fs->getFieldByHandle('namedFor')) { $plan[] = 'create namedFor (Entries, persons)'; }
if (!$fs->getFieldByHandle('namingNote')) { $plan[] = 'create namingNote (PlainText, multi-line)'; }
$types = [];
foreach ($svc->getAllEntryTypes() as $t) { if (in_array($t->handle, $TYPES, true)) { $types[] = $t; } }
foreach ($types as $t) { $have = array_map(fn($f) => $f->handle, $t->getFieldLayout()->getCustomFields()); foreach (['namedFor', 'namingNote'] as $h) { if (!in_array($h, $have, true)) { $plan[] = "add $h to the {$t->handle} layout"; } } }
if (count($types) !== count($TYPES)) { echo 'REFUSING: entry types found: ' . implode(', ', array_map(fn($t) => $t->handle, $types)) . PHP_EOL; return; }
echo ($plan ? implode(PHP_EOL, array_map(fn($x) => '   ' . $x, $plan)) : 'nothing to do') . PHP_EOL;
if (!$plan) { return; }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$fs->getFieldByHandle('namedFor')) {
    $f = new \craft\fields\Entries(['name' => 'Named for', 'handle' => 'namedFor', 'sources' => ['section:' . $persons->uid],
        'instructions' => 'The person this place or body is named for. Not the people associated with it: that is placePeople. Say what the sources say about the naming in namingNote.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('namedFor: ' . json_encode($f->getFirstErrors())); }
}
if (!$fs->getFieldByHandle('namingNote')) {
    $f = new \craft\fields\PlainText(['name' => 'Naming note', 'handle' => 'namingNote', 'multiline' => true, 'initialRows' => 3,
        'instructions' => 'What the sources say about how this got its name, with each source; where they differ, every explanation.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('namingNote: ' . json_encode($f->getFirstErrors())); }
}
foreach ($types as $t) {
    $t = $svc->getEntryTypeById($t->id); $layout = $t->getFieldLayout(); $tabs = $layout->getTabs();
    $have = array_map(fn($f) => $f->handle, $layout->getCustomFields());
    $els = array_values($tabs[0]->getElements());
    foreach (['namedFor', 'namingNote'] as $h) { if (!in_array($h, $have, true)) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h)); } }
    $tabs[0]->setElements($els); $layout->setTabs($tabs); $t->setFieldLayout($layout);
    if (!$svc->saveEntryType($t)) { throw new \RuntimeException("{$t->handle}: " . json_encode($t->getFirstErrors())); }
}
$short = [];
foreach ($TYPES as $h) { $t = $svc->getEntryTypeByHandle($h); $have = array_map(fn($f) => $f->handle, $t->getFieldLayout()->getCustomFields()); foreach (['namedFor', 'namingNote'] as $x) { if (!in_array($x, $have, true)) { $short[] = "$h lacks $x"; } } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: namedFor and namingNote on places and organizations') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_named_for_fields.php', count($plan), $short ? 'SHORT' : 'verified', 'namedFor and namingNote on places and organizations');
if ($short) { throw new \RuntimeException('add_named_for_fields: ' . implode('; ', $short)); }
