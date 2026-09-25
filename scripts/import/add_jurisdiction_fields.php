/**
 * What a body and a district need to say, and currently cannot.
 *
 * ORGANIZATIONS. The type already nests (parentOrganization), dates its
 * founding, types itself government, and knows schoolLevel district. Four
 * things are missing:
 *
 *   dateDissolved / Edtf  There is a founding date and no ending one. Castaic
 *                         Lake Water Agency needs one: it merged into Santa
 *                         Clarita Valley Water in 2018.
 *   precededBy            parentOrganization cannot say "became". A merger is
 *   succeededBy           not a parent and a successor is not a child.
 *   seatCount             How many seats the body has. Without it a roster page
 *                         cannot say "four of five seats accounted for in 1994";
 *                         it can only list what it has and imply that is all.
 *
 * PLACES. A district is a place: it has boundaries, a number and a lifespan,
 * and it is not a body. placeType gains district, and four fields carry what a
 * district is:
 *
 *   districtNumber        "38", "5". Text, not a number: some are named.
 *   districtKind          assembly, senate, congressional, supervisorial,
 *                         trustee-area, other.
 *   effectiveFrom / To    the redistricting cycle this drawing belongs to. The
 *                         valley's Assembly district was the 38th and is now the
 *                         40th; both are real places with different boundaries,
 *                         and a term cites the one in force at the time.
 *
 * Boundaries are not typed in. The published GeoJSON or the certified map goes
 * in recordDocuments, where it keeps its provenance.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_jurisdiction_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }

$ORG_FIELDS = [
    'dateDissolved'     => ['plain', 'Dissolved or merged, as printed', 'organization'],
    'dateDissolvedEdtf' => ['plain', 'Dissolved, EDTF', 'organization'],
    'precededBy'        => ['entries', 'Preceded by', 'organization'],
    'succeededBy'       => ['entries', 'Succeeded by', 'organization'],
    'seatCount'         => ['number', 'Seats on this body', 'organization'],
];
$PLACE_FIELDS = [
    'districtNumber'  => ['plain', 'District number', 'place'],
    'districtKind'    => ['dropdown', 'Kind of district', 'place'],
    'effectiveFrom'   => ['plain', 'Boundaries in force from', 'place'],
    'effectiveTo'     => ['plain', 'Boundaries in force to', 'place'],
];
$DISTRICT_KINDS = ['assembly' => 'State Assembly', 'senate' => 'State Senate',
    'congressional' => 'Congressional', 'supervisorial' => 'County supervisorial',
    'trustee-area' => 'School trustee area', 'other' => 'Other'];

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

foreach ([['organizations', $ORG_FIELDS], ['places', $PLACE_FIELDS]] as [$label, $set]) {
    echo PHP_EOL . $label . ':' . PHP_EOL;
    foreach ($set as $handle => [$kind, $name, $type]) {
        $have = $fs->getFieldByHandle($handle);
        $onLayout = false;
        if ($t = $svc->getEntryTypeByHandle($type)) {
            $onLayout = (bool)$t->getFieldLayout()->getFieldByHandle($handle);
        }
        printf("   %-20s %-9s %s\n", $handle, $kind,
            $have ? ($onLayout ? 'exists, already on the layout' : 'field exists, would add to the layout') : 'would create and add');
    }
}

/* placeType gains district */
$pt = $fs->getFieldByHandle('placeType');
$values = $pt instanceof \craft\fields\Dropdown ? array_column($pt->options, 'value') : [];
echo PHP_EOL . 'placeType: ' . implode(', ', $values) . PHP_EOL;
echo '   ' . (in_array('district', $values, true) ? 'district is already an option' : 'would add "district"') . PHP_EOL;
echo 'districtKind options: ' . implode(', ', array_keys($DISTRICT_KINDS)) . PHP_EOL;

/* What this is for, counted. */
echo PHP_EOL . 'bodies the archive holds that these fields describe:' . PHP_EOL;
foreach (\craft\elements\Entry::find()->section('organizations')->status(null)->limit(null)->orderBy('title')->all() as $o) {
    $type = (string)$o->getFieldValue('orgType');
    $isGov = $type === 'government' || $type === 'school';
    if (!$isGov) { continue; }
    $kids = \craft\elements\Entry::find()->section('organizations')->status(null)
        ->relatedTo(['targetElement' => $o, 'field' => 'parentOrganization'])->count();
    printf("   %-44s %-11s sub-bodies=%d\n", mb_substr($o->title, 0, 44), $type, $kids);
}
$untypedGov = [];
foreach (['Santa Clarita Valley Water', 'William S. Hart High School', 'Felton School', 'Newhall Redevelopment Committee', 'California State University, Northridge'] as $t) {
    $o = \craft\elements\Entry::find()->section('organizations')->status(null)->title($t)->one();
    if ($o && (string)$o->getFieldValue('orgType') === '') { $untypedGov[] = $t; }
}
echo PHP_EOL . 'government or school bodies carrying no orgType yet (a separate pass, by hand): ' . PHP_EOL;
foreach ($untypedGov as $t) { echo '   ' . $t . PHP_EOL; }
echo PHP_EOL . 'places: ' . \craft\elements\Entry::find()->section('places')->status(null)->count()
   . ', of which districts: ' . (in_array('district', $values, true)
        ? \craft\elements\Entry::find()->section('places')->status(null)->placeType('district')->count() : 0) . PHP_EOL;

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}

$created = 0;
$add = function (string $handle, string $kind, string $name, string $typeHandle) use ($fs, $svc, $DISTRICT_KINDS, &$created): bool {
    $f = $fs->getFieldByHandle($handle);
    if (!$f) {
        $f = match ($kind) {
            'entries' => new \craft\fields\Entries(),
            'number' => new \craft\fields\Number(),
            'dropdown' => new \craft\fields\Dropdown(),
            default => new \craft\fields\PlainText(),
        };
        $f->name = $name;
        $f->handle = $handle;
        if ($kind === 'entries') {
            $src = $svc->getSectionByHandle('organizations');
            $f->sources = ['section:' . $src->uid];
            $f->maxRelations = 1;
        }
        if ($kind === 'number') { $f->min = 0; $f->decimals = 0; }
        if ($kind === 'dropdown') {
            $f->options = array_map(fn($v, $l) => ['label' => $l, 'value' => $v, 'default' => false],
                array_keys($DISTRICT_KINDS), array_values($DISTRICT_KINDS));
        }
        if (!$fs->saveField($f)) { echo 'FAILED field ' . $handle . ': ' . json_encode($f->getErrors()) . PHP_EOL; return false; }
        $created++;
        $f = $fs->getFieldByHandle($handle);
    }
    $type = $svc->getEntryTypeByHandle($typeHandle);
    $layout = $type->getFieldLayout();
    if ($layout->getFieldByHandle($handle)) { return true; }
    $tabs = $layout->getTabs();
    $els = $tabs[0]->getElements();
    $els[] = new \craft\fieldlayoutelements\CustomField($f);
    $tabs[0]->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { echo 'FAILED layout ' . $typeHandle . ': ' . json_encode($type->getErrors()) . PHP_EOL; return false; }
    return true;
};

foreach ($ORG_FIELDS as $h => [$k, $n, $t]) { if (!$add($h, $k, $n, $t)) { return; } }
foreach ($PLACE_FIELDS as $h => [$k, $n, $t]) { if (!$add($h, $k, $n, $t)) { return; } }

if ($pt instanceof \craft\fields\Dropdown && !in_array('district', array_column($pt->options, 'value'), true)) {
    $opts = $pt->options;
    $opts[] = ['label' => 'District', 'value' => 'district', 'default' => false];
    $pt->options = $opts;
    if (!$fs->saveField($pt)) { echo 'FAILED placeType: ' . json_encode($pt->getErrors()) . PHP_EOL; return; }
    $created++;
}

Craft::$app->getFields()->refreshFields();
$missing = [];
foreach ([['organization', $ORG_FIELDS], ['place', $PLACE_FIELDS]] as [$typeHandle, $set]) {
    $t = $svc->getEntryTypeByHandle($typeHandle);
    foreach (array_keys($set) as $h) { if (!$t->getFieldLayout()->getFieldByHandle($h)) { $missing[] = $typeHandle . '.' . $h; } }
}
$pt2 = Craft::$app->getFields()->getFieldByHandle('placeType');
$hasDistrict = in_array('district', array_column($pt2->options, 'value'), true);
$ok = !$missing && $hasDistrict;
echo PHP_EOL . 'READ-BACK ' . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
echo '   placeType carries district: ' . ($hasDistrict ? 'yes' : 'NO') . PHP_EOL;
if ($missing) { echo '   not on a layout: ' . implode(', ', $missing) . PHP_EOL; }
if (!$ok) { throw new \RuntimeException('add_jurisdiction_fields read-back failed'); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_jurisdiction_fields.php', $created, 'verified: org and place fields on their layouts, placeType has district',
    'dissolution, succession, seat counts; districts as places');
