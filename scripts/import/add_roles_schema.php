/**
 * Makes roles a thing the archive holds rather than a string on a person.
 *
 * occupation is free text: "Military Officer; Rancher; Politician". A reader
 * cannot ask who else was a rancher, a machine cannot say hasOccupation, and
 * the same job is spelled three ways across sixty-six records. It also cannot
 * carry the civic titles the name policy strips: a man is Congressman for four
 * years, and until now that survived only as an alias, which says what he was
 * called and never what he did.
 *
 * WHAT THIS BUILDS
 *
 *   a roles section        one entry per term, carrying wikidataId and the
 *                          source of the match, from roles-wikidata.json
 *   a roles field          an Entries relation on person, pointing at them
 *   the migration          each person's free-text occupation split and matched
 *                          to the vocabulary
 *
 * THE PRINTED STRING STAYS
 *
 * occupation is not emptied and not rewritten. "President & CEO, SCVTV;
 * Journalist; Historian" is what somebody wrote about Leon Worden and it reads
 * better than three chips will. The relation is for asking questions of; the
 * string is for reading. Where the two disagree the string is what a person
 * put there.
 *
 * A TERM WITH NO WIKIDATA ITEM IS STILL A TERM
 *
 * Sixteen are local: Civic Leader, Land Grantee, Rancho Administrator,
 * Father-Presidente. They get entries with an empty wikidataId, because the
 * archive knowing a thing that Wikidata does not is not a defect.
 *
 * Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_roles_schema.php'))"
 */

$APPLY = false;

$VOCAB = \Craft::getAlias('@root') . '/inventory/legacy/authorities/roles-wikidata.json';
if (!file_exists($VOCAB)) { echo 'no roles-wikidata.json. Run derive_roles_wikidata.py first.' . PHP_EOL; return; }
$vocab = json_decode(file_get_contents($VOCAB), true)['roles'] ?? [];

$fs   = Craft::$app->getFields();
$svc  = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

/* ------------------------------------------------------------ the section */

$section = $svc->getSectionByHandle('roles');
if ($section) {
    $n = \craft\elements\Entry::find()->section('roles')->status(null)->count();
    echo 'section roles       exists already (' . $n . ' entries)' . PHP_EOL;

    /* Reported here, in the part a person reads, and not only in the applying
       half below the dry-run return. A restore left this section standing with
       no title field, the re-run took this path, and the only sign was a guard
       firing at the very end. */
    $t0 = null;
    foreach ($section->getEntryTypes() as $et0) { $t0 = $et0; }
    if ($t0 && !$t0->hasTitleField) {
        $blank0 = 0;
        foreach (\craft\elements\Entry::find()->section('roles')->status(null)->limit(null)->all() as $r0) {
            if (trim((string)$r0->title) === '') { $blank0++; }
        }
        echo '                    its entry type has NO TITLE FIELD, so ' . $blank0 . ' of ' . $n
           . ' entries are untitled' . PHP_EOL;
        echo '                    this run would enable it and name them by their Wikidata id' . PHP_EOL;
    }
} else {
    echo 'section roles       would create, channel, no URLs' . PHP_EOL;
    echo '                    a controlled vocabulary is not a page; it is a list records point at' . PHP_EOL;
}

/* ------------------------------------------------------------- the fields */

$NEW = [
    'roleWikidataId' => ['Role Wikidata ID', 'The Wikidata occupation or position item this role '
        . 'is, such as Q10841764 for Rancher. Empty where no Wikidata item covers the term, which '
        . 'is true of sixteen of them and is not a defect.'],
    'roleMatch' => ['Role match', 'How the Wikidata item was arrived at: exact, pinned, accepted, '
        . 'nearest, or local. Recorded because a label match is not a meaning match, and the ones '
        . 'set by hand are the ones a search got confidently wrong.'],
];
foreach ($NEW as $h => [$n, $i]) {
    echo str_pad($h, 20) . ($fs->getFieldByHandle($h) ? 'exists already' : 'would create, PlainText') . PHP_EOL;
}
echo str_pad('roles', 20) . ($fs->getFieldByHandle('roles') ? 'exists already' : 'would create, Entries, person -> roles') . PHP_EOL;

/* ------------------------------------------------------- the vocabulary */

echo PHP_EOL . 'VOCABULARY: ' . count($vocab) . ' terms' . PHP_EOL;
$byConf = [];
foreach ($vocab as $v) { $byConf[$v['confidence']] = ($byConf[$v['confidence']] ?? 0) + 1; }
foreach ($byConf as $k => $n) { printf("   %-10s %d\n", $k, $n); }

/* --------------------------------------------------------- the migration */

$norm = fn(string $s) => trim(mb_strtolower(preg_replace('~[^a-z0-9 ]~i', ' ', $s)));
$index = [];
foreach ($vocab as $v) { $index[$norm($v['term'])] = $v; }

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
        $k = $norm($t);
        if (isset($index[$k])) { $terms[] = $index[$k]['term']; }
        else { $unmatched[$t] = ($unmatched[$t] ?? 0) + 1; }
    }
    if ($terms) { $plan[] = ['person' => $p, 'terms' => array_values(array_unique($terms)), 'raw' => $raw]; }
}

echo PHP_EOL . 'MIGRATION: ' . count($plan) . ' of ' . count($people) . ' people carry an occupation that maps' . PHP_EOL;
printf("%-30s %s\n", 'PERSON', 'ROLES');
foreach (array_slice($plan, 0, 14) as $x) {
    printf("%-30s %s\n", mb_substr($x['person']->title, 0, 29), implode(' · ', $x['terms']));
}
if (count($plan) > 14) { echo '   ... and ' . (count($plan) - 14) . ' more' . PHP_EOL; }

$links = array_sum(array_map(fn($x) => count($x['terms']), $plan));
echo PHP_EOL . 'role relations to write: ' . $links . PHP_EOL;

if ($unmatched) {
    echo PHP_EOL . 'OCCUPATION TEXT THAT MATCHES NO TERM (' . count($unmatched) . '):' . PHP_EOL;
    foreach ($unmatched as $t => $n) { printf("   %-40s %d\n", mb_substr($t, 0, 39), $n); }
    echo '   The printed string keeps these; they simply gain no relation.' . PHP_EOL;
}

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* ------------------------------------------------------------- applying */

/* The fields first: the entry type's layout is built from them, and the entry
   type has to exist before the section will save. */
$made = [];
foreach ($NEW as $h => [$n, $i]) {
    $f = $fs->getFieldByHandle($h);
    if (!$f) {
        $x = new \craft\fields\PlainText();
        $x->name = $n; $x->handle = $h; $x->instructions = $i; $x->charLimit = 120; $x->searchable = true;
        if (!$fs->saveField($x)) { echo 'FAILED ' . $h . PHP_EOL; continue; }
        $f = $fs->getFieldByHandle($h);
    }
    $made[$h] = $f;
}

/* THE ORDER CRAFT 5 WANTS.
 *
 * A section will not save without at least one entry type, and an entry type
 * is an element of its own that must exist first. The first version built the
 * section and let Craft make the type, which is how Craft 4 worked; Craft 5
 * refuses with "Entry Types cannot be blank".
 *
 * So: create the field layout, create the entry type carrying it, save the
 * entry type, then create the section with that type attached.
 */
if (!$section) {
    /* 1. the layout, then the tab holding a reference BACK to it. A tab built
          before its layout throws "Field layout tab is missing its field
          layout", which is the second thing this got wrong and is invisible
          until something tries to save. */
    $rolesLayout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
    $tab = new \craft\models\FieldLayoutTab(['name' => 'Term', 'layout' => $rolesLayout]);
    $els = [];
    foreach ($made as $h => $f) { $els[] = new \craft\fieldlayoutelements\CustomField($f); }
    $tab->setElements($els);
    $rolesLayout->setTabs([$tab]);

    /* 2. the entry type, saved on its own */
    $roleType = new \craft\models\EntryType();
    $roleType->name = 'Role';
    $roleType->handle = 'role';
    /* Craft 5 defaults this to false, and a vocabulary of eighty entries called
       nothing is what that produces. The run that made them reported "verified
       32 of 32" because it checked the relations and never looked at what it
       had named them. */
    $roleType->hasTitleField = true;
    $roleType->titleFormat = null;
    $roleType->setFieldLayout($rolesLayout);
    if (!$svc->saveEntryType($roleType)) {
        echo 'FAILED entry type: ' . implode('; ', $roleType->getFirstErrors()) . PHP_EOL;
        return;
    }
    echo 'entry type role created' . PHP_EOL;

    /* 3. the section, with the type already attached */
    $section = new \craft\models\Section();
    $section->name = 'Roles';
    $section->handle = 'roles';
    $section->type = \craft\models\Section::TYPE_CHANNEL;
    $section->setEntryTypes([$roleType]);
    $section->setSiteSettings([ (new \craft\models\Section_SiteSettings([
        'siteId' => Craft::$app->sites->getPrimarySite()->id,
        'hasUrls' => false, 'enabledByDefault' => true,
    ])) ]);
    if (!$svc->saveSection($section)) { echo 'FAILED section: ' . implode('; ', $section->getFirstErrors()) . PHP_EOL; return; }
    echo 'section created with its entry type attached' . PHP_EOL;
}

if (!isset($roleType) || !$roleType) {
    foreach ($section->getEntryTypes() as $et) { $roleType = $et; }
}

/* AN EXISTING TYPE THAT CANNOT HOLD A TITLE.
 *
 * The fix for the title field only ran when this script created the section.
 * A restore left the section standing with hasTitleField false, so the re-run
 * took the existing-section path, made eighty more untitled entries and
 * stopped at the guard. A script that can only fix a fault it has not yet
 * caused is not a fix.
 *
 * This repairs the type in place, which is the state the archive is actually
 * in, and then the entries below can carry their names. */
if ($roleType && !$roleType->hasTitleField) {
    echo 'entry type ' . $roleType->handle . ' has no title field' . PHP_EOL;
    if ($APPLY) {
        $roleType->hasTitleField = true;
        $roleType->titleFormat = null;
        if ($svc->saveEntryType($roleType)) {
            echo '   enabled it' . PHP_EOL;
            \Craft::$app->getElements()->invalidateAllCaches();
            \Craft::$app->getFields()->refreshFields();
        } else {
            echo '   FAILED: ' . implode('; ', $roleType->getFirstErrors()) . PHP_EOL;
            return;
        }
    } else {
        echo '   would enable it, so the vocabulary can carry names' . PHP_EOL;
    }
}

if ($roleType) {
    $layout = $roleType->getFieldLayout();
    $present = [];
    foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }
    $tabs = $layout->getTabs();
    if (!$tabs) { $tabs = [new \craft\models\FieldLayoutTab(['name' => 'Content', 'layout' => $layout])]; }
    $els = $tabs[0]->getElements();
    foreach ($made as $h => $f) {
        if (in_array($h, $present, true)) { continue; }
        $els[] = new \craft\fieldlayoutelements\CustomField($f);
    }
    $tabs[0]->setElements($els);
    $layout->setTabs($tabs);
    $roleType->setFieldLayout($layout);
    $svc->saveEntryType($roleType);
}

/* the vocabulary entries */
$roleEntry = [];
$vmade = 0;
foreach ($vocab as $v) {
    $e = \craft\elements\Entry::find()->section('roles')->title($v['term'])->status(null)->one();

    /* An entry made before the title field existed cannot be found by title,
       so look it up by its Wikidata id before creating a second one. Without
       this a re-run doubles the vocabulary every time. */
    if (!$e && ($v['qid'] ?? '') !== '') {
        foreach (\craft\elements\Entry::find()->section('roles')->status(null)->limit(null)->all() as $cand) {
            if (trim((string)$cand->title) === '' && trim((string)$cand->roleWikidataId) === $v['qid']) { $e = $cand; break; }
        }
    }
    if ($e && trim((string)$e->title) === '') { $e->title = $v['term']; $e->slug = null; }

    if (!$e) {
        $e = new \craft\elements\Entry();
        $e->sectionId = $section->id;
        $e->typeId = $roleType->id;
        $e->title = $v['term'];
        $e->enabled = true;
        $vmade++;
    }
    $e->setFieldValues(['roleWikidataId' => $v['qid'] ?? '', 'roleMatch' => $v['confidence']]);
    if (\Craft::$app->elements->saveElement($e)) { $roleEntry[$v['term']] = $e->id; }
}
echo 'vocabulary entries created: ' . $vmade . ', total ' . count($roleEntry) . PHP_EOL;

/* A vocabulary entry with no title is not a term. Checked here rather than
   left for the JSON-LD to emit as an empty occupation name. */
$blankTitles = 0;
foreach (\craft\elements\Entry::find()->section('roles')->status(null)->limit(null)->all() as $r) {
    if (trim((string)$r->title) === '') { $blankTitles++; }
}
if ($blankTitles) {
    echo 'BLANK TITLES: ' . $blankTitles . ' of ' . count($roleEntry) . '. The entry type has no '
       . 'title field, so the vocabulary is unusable. Run repair_role_titles.php.' . PHP_EOL;
    $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
    $applyLog('add_roles_schema.php', 0, 'FAILED: ' . $blankTitles . ' vocabulary entries have no title', 'stopped');
    return;
}

/* the relation field */
$rf = $fs->getFieldByHandle('roles');
if (!$rf) {
    $rf = new \craft\fields\Entries();
    $rf->name = 'Roles';
    $rf->handle = 'roles';
    $rf->instructions = 'What this person did, from the controlled vocabulary. The printed '
        . 'occupation stays as written; this is the version a machine can ask questions of.';
    $rf->sources = ['section:' . $section->uid];
    if (!$fs->saveField($rf)) { echo 'FAILED roles field: ' . implode('; ', $rf->getFirstErrors()) . PHP_EOL; return; }
    $rf = $fs->getFieldByHandle('roles');
}

$personType = $svc->getEntryTypeByHandle('person');
$layout = $personType->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }
if (!in_array('roles', $present, true)) {
    $tabs = $layout->getTabs();
    $tabIdx = 0; $at = null;
    foreach ($tabs as $ti => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->getField()->handle === 'occupation') {
                $tabIdx = $ti; $at = $ei + 1;
            }
        }
    }
    $els = $tabs[$tabIdx]->getElements();
    array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($rf)]);
    $tabs[$tabIdx]->setElements($els);
    $layout->setTabs($tabs);
    $personType->setFieldLayout($layout);
    $svc->saveEntryType($personType);
}

\Craft::$app->getElements()->invalidateAllCaches();
\Craft::$app->getFields()->refreshFields();

$linked = 0;
foreach ($plan as $x) {
    $p = \craft\elements\Entry::find()->id($x['person']->id)->status(null)->one();
    $h = [];
    foreach ($p->getFieldLayout()->getCustomFields() as $f) { $h[] = $f->handle; }
    if (!in_array('roles', $h, true)) { continue; }
    $ids = [];
    foreach ($x['terms'] as $t) { if (isset($roleEntry[$t])) { $ids[] = $roleEntry[$t]; } }
    $p->setFieldValue('roles', $ids);
    if (\Craft::$app->elements->saveElement($p)) { $linked += count($ids); }
}

$verified = 0;
foreach ($plan as $x) {
    $p = \craft\elements\Entry::find()->id($x['person']->id)->status(null)->one();
    $h = [];
    foreach ($p->getFieldLayout()->getCustomFields() as $f) { $h[] = $f->handle; }
    if (in_array('roles', $h, true) && count($p->roles->all()) === count($x['terms'])) { $verified++; }
}
echo PHP_EOL . 'role relations written: ' . $linked . '  people verified: ' . $verified . ' of ' . count($plan) . PHP_EOL;
if ($verified < count($plan)) { echo 'READ-BACK SHORT. Treat this run as failed.' . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_roles_schema.php', $linked,
    ($verified < count($plan) ? 'FAILED: ' : '') . 'verified ' . $verified . ' of ' . count($plan),
    count($roleEntry) . ' vocabulary entries');
