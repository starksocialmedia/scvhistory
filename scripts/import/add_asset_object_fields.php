/**
 * Adds the fields that make an image an object rather than a file.
 *
 * A photograph record carries a creator, a date, a credit and a source. An
 * asset used anywhere else carries almost none of that, and the same picture is
 * therefore well described on one page and anonymous on another. These fields
 * put the description on the file, where it travels with the image into every
 * page that uses it, and into the ImageObject the page emits.
 *
 *   creator       who made the picture. A photographer, an engraver, a studio.
 *   dateAsPrinted the date as the source gives it: "about 1887", "spring 1912".
 *   dateEdtf      the same date in EDTF where the printed form parses, empty
 *                 where it does not. Never guessed.
 *   source        the repository or publication it came from.
 *   rightsHolder  who holds the rights, where that is known and is somebody.
 *   license       what may be done with it.
 *   courtesyOf    the wording a depositor asked for, verbatim.
 *
 * WHY BOTH DATE FIELDS
 *
 * "About 1887" is what the source says and is the truth about the source.
 * 1887~ is what a machine can sort and compare. Keeping only the first makes
 * the archive unsortable; keeping only the second quietly asserts a precision
 * the source never had. So both, and the printed one is authoritative.
 *
 * WHY LICENSE IS A DROPDOWN AND COURTESY IS TEXT
 *
 * There are four licence positions this archive actually takes and they are
 * worth counting, filtering and emitting as a machine-readable term. A courtesy
 * line is a sentence somebody asked for and has to be reproduced as written.
 *
 * Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_asset_object_fields.php'))"
 */

$APPLY = false;

$LICENSE = [
    ['label' => 'Public domain',                    'value' => 'public-domain', 'default' => false],
    ['label' => 'Copyright held by SCVHistory.com', 'value' => 'scvhistory',    'default' => false],
    ['label' => 'Courtesy of',                      'value' => 'courtesy',      'default' => false],
    ['label' => 'Unknown',                          'value' => 'unknown',       'default' => false],
];

$PLAIN = [
    'creator' => ['Creator', 'Who made this picture: a photographer, an engraver, a studio. The '
        . 'person or body responsible for the image itself, not the person in it.'],
    'dateAsPrinted' => ['Date as printed', 'The date exactly as the source gives it, including its '
        . 'hedges: "about 1887", "spring of 1912", "n.d.". This is what the source claims and it '
        . 'is authoritative. Do not tidy it.'],
    'dateEdtf' => ['Date (EDTF)', 'The printed date in Extended Date/Time Format, for sorting and '
        . 'comparison: 1887~ for about 1887, 1912-21 for spring 1912, 1880/1889 for a range. Leave '
        . 'empty where the printed form does not parse cleanly. Never guess: an empty field is a '
        . 'date nobody could pin down, which is a fact worth keeping.'],
    'source' => ['Source', 'The repository or publication this came from: a library, a collection, '
        . 'a newspaper and its date.'],
    'rightsHolder' => ['Rights holder', 'Who holds the rights, where that is known and is somebody '
        . 'in particular. Leave empty rather than writing "unknown" here; the licence says that.'],
    'courtesyOf' => ['Courtesy of', 'The credit wording a depositor asked for, reproduced exactly '
        . 'as they gave it. Shown verbatim wherever the image appears.'],
];

$NEIGHBOURS = ['photoCredit', 'photoDate', 'photoSourceCode', 'legacySourcePath'];

$fs = Craft::$app->getFields();
echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

$made = [];
$createdSchema = false;

foreach ($PLAIN as $handle => [$name, $instr]) {
    $f = $fs->getFieldByHandle($handle);
    if ($f !== null) { echo str_pad($handle, 18) . 'exists already, left alone' . PHP_EOL; $made[$handle] = $f; continue; }
    echo str_pad($handle, 18) . 'would create, PlainText, "' . $name . '"' . PHP_EOL;
    if ($APPLY) {
        $n = new \craft\fields\PlainText();
        $n->name = $name; $n->handle = $handle; $n->instructions = $instr;
        $n->multiline = in_array($handle, ['source', 'courtesyOf'], true);
        $n->charLimit = $n->multiline ? 500 : 255;
        $n->searchable = true;
        if (!$fs->saveField($n)) { echo 'FAILED: ' . implode('; ', $n->getFirstErrors()) . PHP_EOL; continue; }
        $made[$handle] = $fs->getFieldByHandle($handle);
        $createdSchema = true;
    }
}

$f = $fs->getFieldByHandle('license');
if ($f !== null) { echo str_pad('license', 18) . 'exists already, left alone' . PHP_EOL; $made['license'] = $f; }
else {
    echo str_pad('license', 18) . 'would create, Dropdown' . PHP_EOL;
    foreach ($LICENSE as $o) { echo str_pad('', 20) . str_pad($o['value'], 16) . $o['label'] . PHP_EOL; }
    if ($APPLY) {
        $n = new \craft\fields\Dropdown();
        $n->name = 'License'; $n->handle = 'license'; $n->options = $LICENSE;
        $n->instructions = 'What may be done with this image. "Courtesy of" means a depositor '
            . 'allowed its use on this site with a credit; the wording goes in Courtesy of.';
        if ($fs->saveField($n)) { $made['license'] = $fs->getFieldByHandle('license'); $createdSchema = true; }
        else { echo 'FAILED: ' . implode('; ', $n->getFirstErrors()) . PHP_EOL; }
    }
}

/* ------------------------------------------------------------- the layout */

echo PHP_EOL;
$vols = Craft::$app->getVolumes()->getAllVolumes();
$want = array_merge(array_keys($PLAIN), ['license']);

foreach ($vols as $vol) {
    $layout = $vol->getFieldLayout();
    $present = [];
    foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }
    $missing = array_values(array_diff($want, $present));
    printf("%-18s %d fields, would add %d\n", $vol->handle, count($present), count($missing));
    if (!$missing) { continue; }
    echo '   ' . implode(', ', $missing) . PHP_EOL;

    if (!$APPLY) { continue; }

    $tabs = $layout->getTabs();
    if (!$tabs) { continue; }
    $tabIdx = 0; $at = null;
    foreach ($tabs as $ti => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && in_array($el->getField()->handle, $NEIGHBOURS, true)) { $tabIdx = $ti; $at = $ei + 1; }
        }
    }
    $els = $tabs[$tabIdx]->getElements();
    foreach ($missing as $h) {
        if (!isset($made[$h])) { continue; }
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($made[$h])]);
        if ($at !== null) { $at++; }
    }
    $tabs[$tabIdx]->setElements($els);
    $layout->setTabs($tabs);
    $vol->setFieldLayout($layout);
    if (Craft::$app->getVolumes()->saveVolume($vol)) { echo '   saved' . PHP_EOL; $createdSchema = true; }
    else { echo '   FAILED: ' . implode('; ', $vol->getFirstErrors()) . PHP_EOL; }
}

if ($APPLY) {
    $probe = \craft\elements\Asset::find()->status(null)->one();
    $ok = 0;
    if ($probe) {
        foreach ($probe->getFieldLayout()->getCustomFields() as $f2) { if (in_array($f2->handle, $want, true)) { $ok++; } }
    }
    echo PHP_EOL . 'present on a live asset: ' . $ok . ' of ' . count($want) . PHP_EOL;
    if ($ok < count($want)) {
        echo 'READ-BACK SHORT. Treat this run as failed.' . PHP_EOL;
        $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
        $applyLog('add_asset_object_fields.php', $ok, 'FAILED: only ' . $ok . ' of ' . count($want), 'schema');
        return;
    }
    $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
    $applyLog('add_asset_object_fields.php', count($want), 'present on a live asset: ' . $ok, 'schema only');
    echo 'config/project will be dirty. Commit it before deploying.' . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
