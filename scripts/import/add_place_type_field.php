/**
 * Adds placeType, so a place can say what kind of place it is.
 *
 * The places section holds one shape today and the archive holds seven. Beale's
 * Cut is a cut through a ridge, San Fernando Road is a road, Rancho San
 * Francisco is a ranch, the Pioneer Oil Refinery is a building, Hart Park is a
 * park, Mentryville is a site, and the Ridge Route is a trail. Listed together
 * they are an undifferentiated list of names, and the places index cannot offer
 * a reader the one question they actually arrive with, which is "what is near
 * me" or "what can I go and look at".
 *
 *   natural   made by the land: a river, a canyon, a summit, a spring
 *   road      a named road, street or highway
 *   ranch     a rancho or working ranch, the unit the valley was organised in
 *   building  a structure: a refinery, a schoolhouse, a depot, a church
 *   park      land set aside and open to the public
 *   site      a place something happened or stood, with or without remains
 *   trail     a route travelled rather than a road: the Ridge Route, a stage road
 *
 * THE DEFAULT, AND WHY IT SETS NOTHING TODAY
 *
 * Where a record carries a GNIS id the feature class should decide this, since
 * that is a federal gazetteer's own judgement about the same place and it is
 * better than ours. The mapping from GNIS classes to these seven is below and is
 * the durable part of this script.
 *
 * We do not hold the feature classes. gnisId stores the number only, and of the
 * eighteen places, four have one: Vasquez Rocks, Rancho Camulos, Lake Hughes,
 * Fort Tejon. The class is not in the Wikidata match file either, which carries
 * descriptions ("park in Kern County") rather than the GNIS vocabulary. The
 * public endpoint at edits.nationalmap.gov serves a JavaScript application
 * rather than an API, and returns the same page shell for every id.
 *
 * So the script reads classes from inventory/legacy/gnis-classes.json if that
 * file exists, and sets nothing where it does not. Today it sets nothing for all
 * four, which is what "empty otherwise" asks for. Filling it means the USGS
 * Domestic Names download, one tab-separated file with FEATURE_ID and
 * FEATURE_CLASS columns, which is a separate decision about fetching from an
 * outside source and is not taken here.
 *
 * Everything else is left empty on purpose. A place's type is a judgement, the
 * review screen offers it where a name is guessed to be a place, and eighteen
 * records is not enough work to justify guessing on anybody's behalf.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_place_type_field.php'))"
 */

$APPLY = false;

$HANDLE = 'placeType';
$NAME   = 'Place Type';
$TYPE   = 'place';
$NEIGHBOURS = ['placeFeatured', 'placeAliases', 'dateEstablished', 'placeAddress'];

$INSTR =
    'What kind of place this is, which decides how it is grouped and filtered on '
    . 'the places index. Natural: made by the land, such as a river, canyon or '
    . 'summit. Road: a named road, street or highway. Ranch: a rancho or working '
    . 'ranch. Building: a structure. Park: land set aside and open to the public. '
    . 'Site: somewhere a thing happened or stood, with or without remains. Trail: '
    . 'a route travelled rather than a road. Leave empty if none of them fits.';

$OPTIONS = [
    ['label' => 'Natural feature',  'value' => 'natural',  'default' => false],
    ['label' => 'Road or street',   'value' => 'road',     'default' => false],
    ['label' => 'Ranch or rancho',  'value' => 'ranch',    'default' => false],
    ['label' => 'Building',         'value' => 'building', 'default' => false],
    ['label' => 'Park',             'value' => 'park',     'default' => false],
    ['label' => 'Site',             'value' => 'site',     'default' => false],
    ['label' => 'Trail or route',   'value' => 'trail',    'default' => false],
];

/* GNIS feature class to placeType.
 *
 * GNIS classifies physical and cultural features, so it is rich where the land
 * is concerned and thin where our categories are about human use. It has no
 * class for a road, because roads are not named features in the gazetteer, and
 * none for a ranch, which it files under Locale with everything else that is a
 * place without a structure. Those two can only come from a person or from the
 * name, and this mapping does not pretend otherwise. */
$GNIS_TO_TYPE = [
    'arroyo' => 'natural', 'bar' => 'natural', 'basin' => 'natural', 'bay' => 'natural',
    'beach' => 'natural', 'bend' => 'natural', 'cape' => 'natural', 'cliff' => 'natural',
    'crater' => 'natural', 'falls' => 'natural', 'flat' => 'natural', 'forest' => 'natural',
    'gap' => 'natural', 'glacier' => 'natural', 'gut' => 'natural', 'island' => 'natural',
    'isthmus' => 'natural', 'lake' => 'natural', 'lava' => 'natural', 'pillar' => 'natural',
    'plain' => 'natural', 'range' => 'natural', 'rapids' => 'natural', 'ridge' => 'natural',
    'sea' => 'natural', 'slope' => 'natural', 'spring' => 'natural', 'stream' => 'natural',
    'summit' => 'natural', 'swamp' => 'natural', 'valley' => 'natural', 'woods' => 'natural',
    'arch' => 'natural', 'channel' => 'natural',

    'building' => 'building', 'church' => 'building', 'school' => 'building',
    'hospital' => 'building', 'post office' => 'building', 'tower' => 'building',
    'airport' => 'building',

    'park' => 'park',
    'trail' => 'trail',

    'locale' => 'site', 'cemetery' => 'site', 'mine' => 'site', 'dam' => 'site',
    'reservoir' => 'site', 'well' => 'site', 'oilfield' => 'site', 'military' => 'site',
    'census' => 'site', 'populated place' => 'site', 'civil' => 'site', 'reserve' => 'site',
    'harbor' => 'site', 'crossing' => 'site', 'tunnel' => 'site', 'canal' => 'site',
    'levee' => 'site', 'bridge' => 'site',
];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

/* ------------------------------------------------------------- the field */

$field = $fs->getFieldByHandle($HANDLE);
if ($field === null) {
    echo $HANDLE . '  would create, Dropdown, "' . $NAME . '"' . PHP_EOL;
    foreach ($OPTIONS as $o) { echo '   ' . str_pad($o['value'], 10) . $o['label'] . PHP_EOL; }
    if ($APPLY) {
        $new = new \craft\fields\Dropdown();
        $new->name = $NAME;
        $new->handle = $HANDLE;
        $new->instructions = $INSTR;
        $new->options = $OPTIONS;
        if (!$fs->saveField($new)) {
            echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
            return;
        }
        $field = $fs->getFieldByHandle($HANDLE);
        echo 'created' . PHP_EOL;
    }
} else {
    echo $HANDLE . '  exists already, left alone' . PHP_EOL;
}

/* ------------------------------------------------------------ the layout */

echo PHP_EOL;
$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }

$layout = $type->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

if (in_array($HANDLE, $present, true)) {
    echo 'the place layout already carries ' . $HANDLE . PHP_EOL;
} else {
    $tabs = $layout->getTabs();
    $tabIdx = 0; $at = null;
    foreach ($tabs as $ti => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && in_array($el->getField()->handle, $NEIGHBOURS, true)) {
                $tabIdx = $ti; $at = $ei + 1;
            }
        }
    }
    $tabName = $tabs[$tabIdx]->name ?? ('tab ' . ($tabIdx + 1));
    echo 'would add ' . $HANDLE . ' to "' . $tabName . '"'
       . ($at === null ? ' at the end' : ' beside the other place fields') . PHP_EOL;

    if ($APPLY && $field !== null) {
        $els = $tabs[$tabIdx]->getElements();
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($field)]);
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if ($svc->saveEntryType($type)) { echo 'added to the layout' . PHP_EOL; }
        else { echo 'FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }
    }
}

/* ------------------------------------------------------- the GNIS default */

$classPath = \Craft::getAlias('@root') . '/inventory/legacy/gnis-classes.json';
$gnis = file_exists($classPath) ? (json_decode(file_get_contents($classPath), true) ?: []) : [];
$classes = $gnis['classes'] ?? [];
$prov = $gnis['provenance'] ?? [];

echo PHP_EOL . 'GNIS defaults' . PHP_EOL;
if ($classes) {
    echo 'feature classes on file: ' . number_format(count($classes)) . PHP_EOL;
    echo 'from: ' . ($prov['source'] ?? '?') . ', downloaded ' . ($prov['downloaded'] ?? '?') . PHP_EOL;
} else {
    echo 'feature classes on file: none, ' . basename($classPath) . ' does not exist' . PHP_EOL;
}

$hasField = function ($el, string $h): bool {
    $l = $el->getFieldLayout();
    if (!$l) { return false; }
    foreach ($l->getCustomFields() as $f) { if ($f->handle === $h) { return true; } }
    return false;
};

$wouldSet = []; $noClass = []; $noGnis = 0;
foreach (\craft\elements\Entry::find()->section('places')->status(null)->limit(null)->all() as $e) {
    if (!$hasField($e, 'gnisId')) { $noGnis++; continue; }
    $gid = trim((string)$e->getFieldValue('gnisId'));
    if ($gid === '') { $noGnis++; continue; }

    $row = $classes[$gid] ?? null;
    $cls = strtolower(trim((string)($row['class'] ?? '')));
    if ($cls === '') {
        /* An id that is not in the gazetteer at all. Reported rather than
           passed over: it means the number we hold is wrong or belongs to a
           dataset this file is not, and that is worth knowing about a field
           we are about to take defaults from. */
        $noClass[] = [$e->title, $gid . ($row === null ? '  not in the California gazetteer' : '')];
        continue;
    }

    $t = $GNIS_TO_TYPE[$cls] ?? null;
    if ($t === null) { $noClass[] = [$e->title, $gid . ' class "' . $cls . '" has no mapping']; continue; }

    /* Never overwrite a value a person set. */
    $cur = $hasField($e, $HANDLE) ? trim((string)$e->getFieldValue($HANDLE)) : '';
    if ($cur !== '') { continue; }
    $wouldSet[] = [$e, $t, $row['class'], $row['name']];
}

echo 'places with no GNIS id, left empty: ' . $noGnis . PHP_EOL;
if ($noClass) {
    echo 'places with a GNIS id and no class on file, left empty: ' . count($noClass) . PHP_EOL;
    foreach ($noClass as $n) { echo '   ' . str_pad($n[0], 26) . $n[1] . PHP_EOL; }
}
if ($wouldSet) {
    echo 'would set from the feature class: ' . count($wouldSet) . PHP_EOL;
    foreach ($wouldSet as $w) {
        printf("   %-26s %-10s from GNIS \"%s\" (%s)\n", $w[0]->title, $w[1], $w[2], $w[3]);
    }
} else {
    echo 'would set from the feature class: 0' . PHP_EOL;
}

if ($APPLY && $wouldSet) {
    /* The field has to be on the layout before a value can land in it. When the
       field is created in the same run this is already true; when the script is
       re-run for the defaults alone it is the thing most likely to be wrong,
       and setFieldValue on a handle the layout does not carry writes nothing
       and reports success. */
    $probe = \craft\elements\Entry::find()->section('places')->status(null)->one();
    if (!$probe || !$hasField($probe, $HANDLE)) {
        echo PHP_EOL . 'refusing to set defaults: the place layout does not carry ' . $HANDLE . ' yet.' . PHP_EOL;
        echo str_repeat('=', 74) . PHP_EOL;
        return;
    }
    $ok = 0;
    foreach ($wouldSet as $w) {
        $w[0]->setFieldValue($HANDLE, $w[1]);
        if (\Craft::$app->elements->saveElement($w[0])) { $ok++; }
    }
    /* Read back, because a save that reports success and stores nothing has
       happened on this project more than once. */
    $verified = 0;
    foreach ($wouldSet as $w) {
        $e = \craft\elements\Entry::find()->id($w[0]->id)->status(null)->one();
        if ($e && trim((string)$e->getFieldValue($HANDLE)) === $w[1]) { $verified++; }
    }
    echo PHP_EOL . 'set: ' . $ok . '  verified on read-back: ' . $verified . ' of ' . count($wouldSet) . PHP_EOL;
}

if ($APPLY) {
    echo PHP_EOL . 'config/project will be dirty. Commit it before deploying: see docs/DEPLOY.md step 1.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
