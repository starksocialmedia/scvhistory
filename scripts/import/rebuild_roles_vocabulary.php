/**
 * Deletes every untitled entry in the roles section and rebuilds the
 * vocabulary from name-canon's sibling, roles-wikidata.json.
 *
 * WHY A DELETION AND NOT A REPAIR IN PLACE
 *
 * Three runs of add_roles_schema.php created entries against an entry type with
 * no title field. There are now 96 of them where the vocabulary has 80: the
 * duplicates are terms created twice by runs that could not find the first copy,
 * because a lookup by title cannot find an entry that has none.
 *
 * An earlier version of this repair matched entries to terms by position, which
 * worked while there were exactly 80 in exactly the right order. At 96 with
 * duplicates that is no longer true, and position would now quietly title the
 * wrong terms. Nothing points at these entries, so deleting them costs nothing
 * and removes the question.
 *
 * WHAT IT WILL NOT DELETE
 *
 * Anything with a title. If somebody has typed a term by hand it stays, and if
 * it is not in the vocabulary file this script says so and stops, because a
 * hand-made term is a decision and this script is not entitled to it.
 *
 * WHAT THE RELATIONS COST
 *
 * Nothing today: there are none. Every run stopped at the guard before the
 * migration, so no person points at a role. The free-text occupation is
 * untouched on all 32 people, which is what the migration reads, so the
 * relations are rebuilt from the source rather than recovered.
 *
 * WHY THE TITLE FIELD WOULD NOT TURN ON
 *
 * Setting hasTitleField = true and saving looked like it worked and did
 * nothing: saveEntryType returned ok, the model read back false, and the
 * database row stayed 0. In Craft 5 the flag is derived from the field layout,
 * not stored independently. A layout with no EntryTitleField element means an
 * entry type with no title, whatever the property says, and the save silently
 * corrects the property to match the layout.
 *
 * So the layout gains the element first. Proved in a rolled-back transaction
 * below, which this script runs before it reports anything, because the last
 * three attempts all printed success.
 *
 * Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/rebuild_roles_vocabulary.php'))"
 */

$APPLY = false;

$svc = Craft::$app->getEntries();
$fs  = Craft::$app->getFields();
$section = $svc->getSectionByHandle('roles');

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

if (!$section) { echo 'no roles section. Run add_roles_schema.php.' . PHP_EOL; return; }

$vocab = json_decode(file_get_contents(
    \Craft::getAlias('@root') . '/inventory/legacy/authorities/roles-wikidata.json'), true)['roles'] ?? [];
if (!$vocab) { echo 'no vocabulary file.' . PHP_EOL; return; }

$type = null;
foreach ($section->getEntryTypes() as $t) { $type = $t; }

/* --------------------------------------------- the title field, checked

   NOT probed in a transaction. Project config writes are not transactional:
   they go to config/project on disk immediately, and a rollback undoes the
   database and leaves the YAML behind. That is exactly how this entry type
   ended up with config saying hasTitleField true, the database column saying
   0, and project-config/diff reporting nothing pending, because Craft's own
   config store agreed with the YAML it had just written. Three runs then
   created untitled entries against a type that looked fixed.

   So the database column is read directly, and if it disagrees with the config
   this script stops and says which command reconciles them. It does not try to
   fix schema itself any more. */
$dbTitle = (int)(new \craft\db\Query())->select(['hasTitleField'])
    ->from('{{%entrytypes}}')->where(['uid' => $type->uid])->scalar();
$pcCfg = Craft::$app->getProjectConfig()->get('entryTypes.' . $type->uid) ?: [];
$pcTitle = !empty($pcCfg['hasTitleField']);
$pcHasElement = false;
foreach (($pcCfg['fieldLayouts'] ?? []) as $l) {
    foreach (($l['tabs'] ?? []) as $tab) {
        foreach (($tab['elements'] ?? []) as $el) {
            if (($el['type'] ?? '') === 'craft\\fieldlayoutelements\\entries\\EntryTitleField') { $pcHasElement = true; }
        }
    }
}

echo 'THE TITLE FIELD' . PHP_EOL;
printf("   %-46s %s\n", 'database column entrytypes.hasTitleField', $dbTitle === 1 ? '1' : (string)$dbTitle);
printf("   %-46s %s\n", 'project config hasTitleField', $pcTitle ? 'true' : 'false');
printf("   %-46s %s\n", 'project config carries an EntryTitleField', $pcHasElement ? 'yes' : 'no');

if ($dbTitle !== 1) {
    echo PHP_EOL;
    if ($pcTitle && $pcHasElement) {
        echo 'Config is correct and the database has not caught up. Nothing here can fix that:' . PHP_EOL;
        echo '   ddev craft project-config/apply --force' . PHP_EOL;
        echo 'Then run this again. It will not delete or create anything until the column reads 1,' . PHP_EOL;
        echo 'because every entry made under a type that cannot hold a title has to be deleted again.' . PHP_EOL;
    } else {
        echo 'Neither the database nor the config has the title field. The entry type needs an' . PHP_EOL;
        echo 'EntryTitleField element in its layout; add it in the control panel and run this again.' . PHP_EOL;
    }
    return;
}
echo PHP_EOL;



$all = \craft\elements\Entry::find()->section('roles')->status(null)->limit(null)->all();
$untitled = []; $titled = [];
foreach ($all as $r) {
    if (trim((string)$r->title) === '') { $untitled[] = $r; } else { $titled[$r->title] = $r; }
}

echo 'entry type ' . $type->handle . ': title field ' . ($type->hasTitleField ? 'present' : 'ABSENT') . PHP_EOL;
echo 'entries in the section: ' . count($all) . '   untitled: ' . count($untitled)
   . '   titled: ' . count($titled) . PHP_EOL;
echo 'vocabulary terms: ' . count($vocab) . PHP_EOL;

/* A titled entry that the vocabulary does not know is somebody's decision. */
$vocabTerms = array_column($vocab, 'term');
$strangers = array_values(array_diff(array_keys($titled), $vocabTerms));
if ($strangers) {
    echo PHP_EOL . 'TITLED ENTRIES NOT IN THE VOCABULARY (' . count($strangers) . '):' . PHP_EOL;
    foreach ($strangers as $x) { echo '   ' . $x . PHP_EOL; }
    echo 'These were typed by hand. Stopping rather than deciding what to do with them.' . PHP_EOL;
    return;
}

/* What points at what is about to go. */
$pointing = 0;
foreach ($untitled as $r) {
    $pointing += \craft\elements\Entry::find()->relatedTo(['targetElement' => $r, 'field' => 'roles'])->status(null)->count();
}
echo PHP_EOL . 'relations pointing at an untitled entry: ' . $pointing . PHP_EOL;
if ($pointing) {
    echo '   These are lost by the deletion and rebuilt from the free-text occupation below.' . PHP_EOL;
}

/* The migration, read from the source that survives all of this. */
$norm = fn(string $s) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $s)));
$index = [];
foreach ($vocab as $v) { $index[$norm($v['term'])] = $v['term']; }

$people = \craft\elements\Entry::find()->section('persons')->status(null)->limit(null)->all();
$plan = []; $unmatched = [];
foreach ($people as $p) {
    $h = [];
    foreach ($p->getFieldLayout()->getCustomFields() as $f) { $h[] = $f->handle; }
    if (!in_array('occupation', $h, true)) { continue; }
    $raw = trim((string)$p->getFieldValue('occupation'));
    if ($raw === '') { continue; }
    $terms = [];
    foreach (preg_split('~\s*[;·,]\s*~u', $raw) as $part) {
        $t = trim($part);
        if ($t === '') { continue; }
        if (isset($index[$norm($t)])) { $terms[] = $index[$norm($t)]; }
        else { $unmatched[$t] = ($unmatched[$t] ?? 0) + 1; }
    }
    if ($terms) { $plan[] = ['person' => $p, 'terms' => array_values(array_unique($terms))]; }
}
$links = array_sum(array_map(fn($x) => count($x['terms']), $plan));

echo PHP_EOL . 'THE PLAN' . PHP_EOL;
echo '   delete            ' . count($untitled) . ' untitled entries' . PHP_EOL;
echo '   keep              ' . count($titled) . ' titled entries' . PHP_EOL;
echo '   enable            ' . ($type->hasTitleField ? 'nothing, the title field is present' : 'the title field on the entry type') . PHP_EOL;
echo '   create            ' . count(array_diff($vocabTerms, array_keys($titled))) . ' vocabulary entries, titled' . PHP_EOL;
echo '   relate            ' . $links . ' roles across ' . count($plan) . ' people' . PHP_EOL;

if ($unmatched) {
    echo PHP_EOL . 'occupation text matching no term (' . count($unmatched) . '):' . PHP_EOL;
    foreach ($unmatched as $t => $n) { printf("   %-40s %d\n", mb_substr($t, 0, 39), $n); }
}

if (!$APPLY) {
    echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

/* ------------------------------------------------------------- applying */

/* Now the deletion. Every untitled entry, whatever the count. */
$deleted = 0;
foreach ($untitled as $r) {
    if (\Craft::$app->elements->deleteElement($r, true)) { $deleted++; }
}
echo 'deleted: ' . $deleted . ' untitled entries' . PHP_EOL;

$byTerm = [];
$created = 0;
foreach ($vocab as $v) {
    /* Matched by term. The qid is not a key: sixteen local terms have none, and
       three pairs share one, which is how a previous run created duplicates of
       exactly those. */
    $e = \craft\elements\Entry::find()->section('roles')->title($v['term'])->status(null)->one();
    if (!$e) {
        $e = new \craft\elements\Entry();
        $e->sectionId = $section->id;
        $e->typeId = $type->id;
        $e->title = $v['term'];
        $e->enabled = true;
        $created++;
    }
    $e->setFieldValues(['roleWikidataId' => $v['qid'] ?? '', 'roleMatch' => $v['confidence']]);
    if (\Craft::$app->elements->saveElement($e)) { $byTerm[$v['term']] = $e->id; }
}
echo 'created: ' . $created . '  vocabulary now: ' . count($byTerm) . PHP_EOL;

$blank = 0;
foreach (\craft\elements\Entry::find()->section('roles')->status(null)->limit(null)->all() as $r) {
    if (trim((string)$r->title) === '') { $blank++; }
}
if ($blank) {
    echo 'STILL ' . $blank . ' UNTITLED. The title field did not take; treat this run as failed.' . PHP_EOL;
    $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
    $applyLog('rebuild_roles_vocabulary.php', 0, 'FAILED: ' . $blank . ' entries still untitled', 'stopped');
    return;
}

$linked = 0;
foreach ($plan as $x) {
    $p = \craft\elements\Entry::find()->id($x['person']->id)->status(null)->one();
    $ids = [];
    foreach ($x['terms'] as $t) { if (isset($byTerm[$t])) { $ids[] = $byTerm[$t]; } }
    $p->setFieldValue('roles', $ids);
    if (\Craft::$app->elements->saveElement($p)) { $linked += count($ids); }
}

$verified = 0;
foreach ($plan as $x) {
    $p = \craft\elements\Entry::find()->id($x['person']->id)->status(null)->one();
    $got = array_filter(array_map(fn($r) => trim((string)$r->title), $p->roles->all()));
    if (count($got) === count($x['terms'])) { $verified++; }
}
/* (int). Entry::find()->count() returns a STRING from the database driver, and
   '80' === 80 is false. The run of 19:59 deleted 80, created 80, wrote 93
   relations and verified 32 of 32 people, all correctly, and then reported
   READ-BACK SHORT on a type mismatch in its own check. An hour was spent
   looking for a fault in the data because the check could not say which of its
   three conditions had failed. */
$total = (int)\craft\elements\Entry::find()->section('roles')->status(null)->count();

/* Every check, with its numbers, every time. A verdict without them sends a
   person to the control panel to find out what the script already knew. */
$checks = [
    ['entries titled',  $blank === 0,                     ($total - $blank) . ' of ' . $total],
    ['vocabulary size', $total === count($vocab),          $total . ' of ' . count($vocab) . ' terms'],
    ['people related',  $verified === count($plan),        $verified . ' of ' . count($plan)],
    ['relations written', $linked > 0,                     (string)$linked],
];
echo PHP_EOL . 'READ-BACK' . PHP_EOL;
foreach ($checks as [$name, $pass, $detail]) {
    printf("   %-20s %-22s %s\n", $name, $detail, $pass ? 'pass' : 'FAIL');
}
$failed = array_values(array_filter($checks, fn($c) => !$c[1]));
$ok = !$failed;
if (!$ok) {
    echo PHP_EOL . 'READ-BACK SHORT on: ' . implode(', ', array_column($failed, 0)) . PHP_EOL;
    echo 'Treat this run as failed.' . PHP_EOL;
}
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('rebuild_roles_vocabulary.php', $created,
    ($ok ? 'verified: ' : 'FAILED on ' . implode('/', array_column($failed, 0)) . ': ')
    . 'entries ' . $total . '/' . count($vocab) . ', titled ' . ($total - $blank)
    . ', people ' . $verified . '/' . count($plan),
    'deleted ' . $deleted . ' untitled, wrote ' . $linked . ' relations');
