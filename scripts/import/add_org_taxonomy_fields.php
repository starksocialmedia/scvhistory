/**
 * Adds orgType and schoolLevel to the organization type.
 *
 * The organizations section holds a newspaper, a hospital, three missions, two
 * ranchos, a school, a chamber of commerce and an oil company under one shape.
 * Listed together they are a list of names, and the index cannot answer the
 * question a reader arrives with, which is usually "what schools were there"
 * or "who reported on this".
 *
 *   school      a school, a district, a college
 *   government  a city, a county body, a commission, a department
 *   business    a company trading for profit
 *   nonprofit   a society, a foundation, a chamber, a charity
 *   church      a congregation or a mission
 *   club        a lodge, a booster club, a social or sporting body
 *   media       a newspaper, a radio station, a television station
 *   military    a fort, a post, a unit
 *   other       none of the above, chosen deliberately rather than left blank
 *
 * schoolLevel is elementary, middle, high, college or district, and is only
 * meaningful when orgType is school. The field layout carries a condition so it
 * appears in the control panel only then; if this version of Craft refuses the
 * condition the script says so and adds the field unconditioned, because a
 * field that is always visible is a smaller problem than no field.
 *
 * THE DEFAULT
 *
 * orgType is guessed from the name where the name says it outright, and the
 * guess is reported with the word that produced it. Everything else is left
 * empty: a guess a person cannot see is a guess they cannot overrule, and
 * "business" applied silently to two hundred records would be indistinguishable
 * from a decision.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_org_taxonomy_fields.php'))"
 */

$APPLY = false;

$TYPE = 'organization';

$ORG_TYPE_OPTIONS = [
    ['label' => 'School, district or college', 'value' => 'school',     'default' => false],
    ['label' => 'Government body',             'value' => 'government', 'default' => false],
    ['label' => 'Business',                    'value' => 'business',   'default' => false],
    ['label' => 'Nonprofit or society',        'value' => 'nonprofit',  'default' => false],
    ['label' => 'Church or mission',           'value' => 'church',     'default' => false],
    ['label' => 'Club or lodge',               'value' => 'club',       'default' => false],
    ['label' => 'Newspaper or broadcaster',    'value' => 'media',      'default' => false],
    ['label' => 'Military',                    'value' => 'military',   'default' => false],
    ['label' => 'Other',                       'value' => 'other',      'default' => false],
];

$LEVEL_OPTIONS = [
    ['label' => 'Elementary', 'value' => 'elementary', 'default' => false],
    ['label' => 'Middle',     'value' => 'middle',     'default' => false],
    ['label' => 'High',       'value' => 'high',       'default' => false],
    ['label' => 'College',    'value' => 'college',    'default' => false],
    ['label' => 'District',   'value' => 'district',   'default' => false],
];

/* Words that name the type outright. Order matters: "Newhall School District"
   is a district before it is a school, and "The Signal" is media before it is
   anything else. Each entry is the word, the type it proves, and whether it
   must be the last word to count. */
$NAME_RULES = [
    ['district',    'school',     'level' => 'district'],
    ['school',      'school',     'level' => ''],
    ['academy',     'school',     'level' => ''],
    ['college',     'school',     'level' => 'college'],
    ['university',  'school',     'level' => 'college'],
    ['city of',     'government', 'level' => ''],
    ['county of',   'government', 'level' => ''],
    ['commission',  'government', 'level' => ''],
    ['department',  'government', 'level' => ''],
    ['board of',    'government', 'level' => ''],
    ['church',      'church',     'level' => ''],
    ['mission',     'church',     'level' => ''],
    ['chapel',      'church',     'level' => ''],
    ['society',     'nonprofit',  'level' => ''],
    ['foundation',  'nonprofit',  'level' => ''],
    ['chamber of',  'nonprofit',  'level' => ''],
    ['club',        'club',       'level' => ''],
    ['lodge',       'club',       'level' => ''],
    ['signal',      'media',      'level' => ''],
    ['gazette',     'media',      'level' => ''],
    ['times',       'media',      'level' => ''],
    ['herald',      'media',      'level' => ''],
    ['newspaper',   'media',      'level' => ''],
    ['fort',        'military',   'level' => ''],
];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$hasField = function ($el, string $h): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

$createdSchema = false;

$makeDropdown = function (string $handle, string $name, string $instr, array $options)
        use ($fs, $APPLY, &$createdSchema) {
    $f = $fs->getFieldByHandle($handle);
    if ($f !== null) { echo str_pad($handle, 20) . 'exists already, left alone' . PHP_EOL; return $f; }
    echo str_pad($handle, 20) . 'would create, Dropdown' . PHP_EOL;
    foreach ($options as $o) { echo str_pad('', 22) . str_pad($o['value'], 12) . $o['label'] . PHP_EOL; }
    if (!$APPLY) { return null; }
    $n = new \craft\fields\Dropdown();
    $n->name = $name; $n->handle = $handle; $n->instructions = $instr; $n->options = $options;
    if (!$fs->saveField($n)) { echo 'FAILED: ' . implode('; ', $n->getFirstErrors()) . PHP_EOL; return null; }
    $createdSchema = true;
    return $fs->getFieldByHandle($handle);
};

$orgTypeField = $makeDropdown('orgType', 'Organisation Type',
    'What kind of body this is, which decides how it is grouped and filtered on the '
    . 'organizations index and what it is called in structured data. Choose Other '
    . 'deliberately rather than leaving it empty.', $ORG_TYPE_OPTIONS);

$levelField = $makeDropdown('schoolLevel', 'School Level',
    'Only meaningful when the type is School. Elementary, middle, high, college, or '
    . 'district for the body that runs the schools rather than a school itself.',
    $LEVEL_OPTIONS);

/* ------------------------------------------------------------- the layout */

echo PHP_EOL;
$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }
$layout = $type->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

$wanted = ['orgType' => $orgTypeField, 'schoolLevel' => $levelField];
$tabs = $layout->getTabs();

/* Taxonomy is where recordTags and the era categories live, which is where a
   reader of the control panel would look for "what kind of thing is this". */
$tabIdx = 0;
foreach ($tabs as $ti => $t) { if (strcasecmp($t->name, 'Taxonomy') === 0) { $tabIdx = $ti; } }
echo 'target tab: "' . ($tabs[$tabIdx]->name ?? '?') . '"' . PHP_EOL;

foreach ($wanted as $handle => $field) {
    if (in_array($handle, $present, true)) {
        echo str_pad($handle, 20) . 'already on the layout' . PHP_EOL;
        continue;
    }
    echo str_pad($handle, 20) . 'would add to the layout'
       . ($handle === 'schoolLevel' ? ', shown only when orgType is School' : '') . PHP_EOL;

    if (!$APPLY || $field === null) { continue; }

    $el = new \craft\fieldlayoutelements\CustomField($field);

    if ($handle === 'schoolLevel' && $orgTypeField !== null) {
        /* Craft can hide a layout element behind a condition on another field.
           Built by hand here because there is no API for "add this field, shown
           only when that one says school". If the classes are not present in
           this version the field is added unconditioned rather than not at
           all. */
        try {
            $cond = new \craft\elements\conditions\entries\EntryCondition();
            $cond->elementType = \craft\elements\Entry::class;
            $rule = new \craft\fields\conditions\OptionsFieldConditionRule();
            $rule->setFieldUid($orgTypeField->uid);
            $rule->operator = 'in';
            $rule->setValues(['school']);
            $cond->addConditionRule($rule);
            $el->elementCondition = $cond;
            echo str_pad('', 22) . 'condition attached' . PHP_EOL;
        } catch (\Throwable $e) {
            echo str_pad('', 22) . 'CONDITION REFUSED (' . $e->getMessage() . '), adding it unconditioned' . PHP_EOL;
        }
    }

    $els = $tabs[$tabIdx]->getElements();
    $els[] = $el;
    $tabs[$tabIdx]->setElements($els);
    $createdSchema = true;
}

if ($APPLY) {
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    if ($svc->saveEntryType($type)) { echo 'layout saved' . PHP_EOL; }
    else { echo 'FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }
}

/* ------------------------------------------------------- the name guesses */

/* The level is read separately from the type, because the word that proves the
   type is rarely the word that gives the level. "Newhall Elementary School" is
   a school on the word school and elementary on the word elementary, and a
   rule table keyed only on the first would file it with no level at all. */
$LEVEL_WORDS = [
    'district' => 'district', 'unified' => 'district', 'union' => 'district',
    'elementary' => 'elementary', 'primary' => 'elementary', 'grammar' => 'elementary',
    'middle' => 'middle', 'junior high' => 'middle', 'intermediate' => 'middle',
    'high' => 'high', 'senior high' => 'high',
    'college' => 'college', 'university' => 'college',
];

$guess = function (string $name) use ($NAME_RULES, $LEVEL_WORDS): array {
    $n = ' ' . mb_strtolower(trim($name)) . ' ';
    $type = ''; $level = ''; $word = '';
    foreach ($NAME_RULES as $r) {
        [$needle, $t] = $r;
        if (str_contains($n, ' ' . $needle . ' ') || str_contains($n, ' ' . $needle)) {
            $type = $t; $level = $r['level']; $word = $needle;
            break;
        }
    }
    if ($type === 'school') {
        foreach ($LEVEL_WORDS as $needle => $lv) {
            if (str_contains($n, ' ' . $needle . ' ') || str_contains($n, ' ' . $needle)) {
                $level = $lv;
                if ($needle !== $word) { $word .= '" + "' . $needle; }
                break;
            }
        }
    }
    return [$type, $level, $word];
};

echo PHP_EOL . 'NAME GUESSES for the ' . \craft\elements\Entry::find()->section('organizations')->status(null)->count()
   . ' organization records' . PHP_EOL;
printf("  %-46s %-12s %-10s %s\n", 'RECORD', 'ORGTYPE', 'LEVEL', 'FROM THE WORD');

$set = 0; $blank = 0;
foreach (\craft\elements\Entry::find()->section('organizations')->status(null)->orderBy('title asc')->limit(null)->all() as $e) {
    [$t, $lv, $word] = $guess((string)$e->title);
    $cur = $hasField($e, 'orgType') ? trim((string)$e->getFieldValue('orgType')) : '';
    if ($cur !== '') { continue; }
    if ($t === '') { $blank++; continue; }
    printf("  %-46s %-12s %-10s \"%s\"\n", mb_substr($e->title, 0, 45), $t, $lv ?: '-', $word);
    $set++;

    if ($APPLY && $orgTypeField !== null && $hasField($e, 'orgType')) {
        $vals = ['orgType' => $t];
        if ($lv !== '' && $hasField($e, 'schoolLevel')) { $vals['schoolLevel'] = $lv; }
        $e->setFieldValues($vals);
        \Craft::$app->elements->saveElement($e);
    }
}
echo PHP_EOL . 'would set: ' . $set . '   left empty, the name does not say: ' . $blank . PHP_EOL;

if ($APPLY) {
    $verified = 0;
    foreach (\craft\elements\Entry::find()->section('organizations')->status(null)->limit(null)->all() as $e) {
        if ($hasField($e, 'orgType') && trim((string)$e->getFieldValue('orgType')) !== '') { $verified++; }
    }
    echo 'carrying an orgType on read-back: ' . $verified . PHP_EOL;
    $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
    $applyLog('add_org_taxonomy_fields.php', $set, 'orgType present on ' . $verified,
        $createdSchema ? 'created orgType and schoolLevel' : 'guesses only');
    echo PHP_EOL . 'config/project will be dirty. Commit it before deploying: see docs/DEPLOY.md step 1.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
