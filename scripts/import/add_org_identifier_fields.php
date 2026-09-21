/**
 * Adds cdsCode, ncesId and ein to the organization type.
 *
 * An organization record is worth more when it says which organization it is in
 * somebody else's terms. A CDS code ties a school to the state's own directory,
 * an NCES id ties it to the federal census, and an EIN ties a nonprofit to the
 * IRS. Between them they are what lets this archive be cited by anything else,
 * and what lets a reader check it.
 *
 * WHAT IS ALREADY THERE, AND SO NOT CREATED HERE
 *
 *   wikidataId          already on organization, person and place
 *   parentOrganization  already on organization, an Entries field
 *   hasParentOrg        already there, the switch that reveals it
 *
 * The brief asked for consistent naming with person and place, which already
 * carry wikidataId, viafId and gnisId. These follow that: a bare handle naming
 * the scheme, no org prefix, because the scheme is the same scheme wherever it
 * appears.
 *
 * FORMATS, AND WHY THEY ARE NOT VALIDATED HARD
 *
 *   cdsCode   14 digits, county-district-school
 *   ncesId    12 digits for a school, 7 for a district
 *   ein       9 digits, conventionally written 00-0000000
 *
 * The instructions say so and nothing enforces it. A historical record may
 * carry a partial code from a source that printed only part of one, and a field
 * that refuses to store what the source said is a field that loses it.
 *
 * NOTHING IS BACKFILLED HERE. propose_org_backfill.php matches the existing
 * records against the authority tables and prints what it would set, which is a
 * separate decision from having somewhere to put it.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_org_identifier_fields.php'))"
 */

$APPLY = false;

$TYPE = 'organization';

$FIELDS = [
    'cdsCode' => [
        'name' => 'CDS Code',
        'instructions' => 'The California Department of Education county-district-school code, '
            . 'fourteen digits. Identifies a school or district in the state directory. Leave '
            . 'empty for anything that is not a California public school.',
    ],
    'ncesId' => [
        'name' => 'NCES ID',
        'instructions' => 'The federal identifier from the National Center for Education '
            . 'Statistics: twelve digits for a school, seven for a district. This is the id the '
            . 'Common Core of Data uses, and the one that survives a school changing its name.',
    ],
    'ein' => [
        'name' => 'EIN',
        'instructions' => 'The Employer Identification Number the IRS assigns an exempt '
            . 'organization, nine digits. Identifies a nonprofit, a church or a chamber in the '
            . 'federal Business Master File.',
    ],
];

/* Placed beside wikidataId, wherever that is, so every identifier is in one
   place instead of scattered by the order they were added in. */
$NEIGHBOURS = ['wikidataId', 'viafId', 'orgWikipediaUrl', 'orgLegacyUrl'];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }

$layout = $type->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

echo 'already on organization: '
   . implode(', ', array_values(array_intersect(['wikidataId', 'parentOrganization', 'hasParentOrg'], $present)))
   . PHP_EOL . PHP_EOL;

$created = [];
$createdSchema = false;

foreach ($FIELDS as $handle => $spec) {
    $f = $fs->getFieldByHandle($handle);
    if ($f !== null) {
        echo str_pad($handle, 16) . 'field exists already, left alone' . PHP_EOL;
    } else {
        echo str_pad($handle, 16) . 'would create, PlainText, "' . $spec['name'] . '"' . PHP_EOL;
        if ($APPLY) {
            $n = new \craft\fields\PlainText();
            $n->name = $spec['name'];
            $n->handle = $handle;
            $n->instructions = $spec['instructions'];
            $n->multiline = false;
            $n->charLimit = 32;
            $n->searchable = true;
            if (!$fs->saveField($n)) {
                echo 'FAILED: ' . implode('; ', $n->getFirstErrors()) . PHP_EOL;
                continue;
            }
            $f = $fs->getFieldByHandle($handle);
            $createdSchema = true;
        }
    }
    $created[$handle] = $f;
}

echo PHP_EOL;

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
echo 'target tab: "' . ($tabs[$tabIdx]->name ?? '?') . '"'
   . ($at === null ? ', at the end' : ', beside wikidataId') . PHP_EOL;

$added = 0;
foreach ($FIELDS as $handle => $spec) {
    if (in_array($handle, $present, true)) {
        echo str_pad($handle, 16) . 'already on the layout' . PHP_EOL;
        continue;
    }
    echo str_pad($handle, 16) . 'would add to the layout' . PHP_EOL;
    if ($APPLY && ($created[$handle] ?? null) !== null) {
        $els = $tabs[$tabIdx]->getElements();
        array_splice($els, $at ?? count($els), 0,
            [new \craft\fieldlayoutelements\CustomField($created[$handle])]);
        $tabs[$tabIdx]->setElements($els);
        if ($at !== null) { $at++; }
        $added++;
        $createdSchema = true;
    }
}

if ($APPLY && $added) {
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    if ($svc->saveEntryType($type)) { echo PHP_EOL . 'layout saved, ' . $added . ' fields added' . PHP_EOL; }
    else { echo 'FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }

    $probe = \craft\elements\Entry::find()->section('organizations')->status(null)->one();
    $ok = 0;
    if ($probe) {
        foreach ($probe->getFieldLayout()->getCustomFields() as $f) {
            if (isset($FIELDS[$f->handle])) { $ok++; }
        }
    }
    echo 'present on a live entry after the save: ' . $ok . ' of ' . count($FIELDS) . PHP_EOL;

    $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
    $applyLog('add_org_identifier_fields.php', $added, 'present on a live entry: ' . $ok, 'schema only');
    echo PHP_EOL . 'config/project will be dirty. Commit it before deploying: see docs/DEPLOY.md step 1.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
