/**
 * Where an image came from, for images that did not come from the mirror.
 *
 * Every mirror asset carries legacySourcePath, which says where on the legacy
 * site it was. An image from anywhere else carried nothing that told it apart
 * from a sourced scan once it was in the volume (Nathan, 29 September 2026).
 * The rights fields were already there (license, creator, source, rightsHolder,
 * courtesyOf, dateAsPrinted, dateEdtf); three things were not:
 *
 *   provenanceKind  how the archive came to hold it: the legacy mirror, the old
 *                   WordPress site's media library, an outside source, made for
 *                   SCVHistory, or given by a depositor. Empty means not yet
 *                   established, and says so.
 *   sourceUrl       the page it was obtained from, for an outside source: the
 *                   catalogue record or the file page, not the image file.
 *   acquiredDate    the date the archive obtained it, YYYY-MM-DD.
 *
 * And license gains the options an outside image needs: written permission, and
 * Creative Commons BY and BY-SA at the two versions met in practice (3.0, 4.0).
 * A new version is added when an image carrying one arrives, never guessed.
 * There is no option for a generated image: a generated image does not enter
 * the archive (the Firefly file, 29 September).
 *
 * The media page and the JSON-LD map each licence value to its licence URL; the
 * new values are added to both maps in templates/media/_entry.twig and
 * templates/_partials/head/schema.twig, in the same commit.
 *
 * Schema only: no asset is changed. Idempotent. Dry run by default.
 * Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_asset_provenance_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$fs = Craft::$app->getFields();
$KIND = [
    ['label' => 'Not yet established', 'value' => '', 'default' => true],
    ['label' => 'The legacy site mirror (Reggie)', 'value' => 'legacy-mirror', 'default' => false],
    ['label' => 'The WordPress site\'s media library', 'value' => 'legacy-wordpress', 'default' => false],
    ['label' => 'An outside source', 'value' => 'outside', 'default' => false],
    ['label' => 'Made for SCVHistory', 'value' => 'commissioned', 'default' => false],
    ['label' => 'Given by a depositor', 'value' => 'donated', 'default' => false],
];
$NEW_LICENSES = [
    ['label' => 'Used with written permission', 'value' => 'permission', 'default' => false],
    ['label' => 'Creative Commons Attribution 4.0 (CC BY 4.0)', 'value' => 'cc-by-4.0', 'default' => false],
    ['label' => 'Creative Commons Attribution-ShareAlike 4.0 (CC BY-SA 4.0)', 'value' => 'cc-by-sa-4.0', 'default' => false],
    ['label' => 'Creative Commons Attribution 3.0 (CC BY 3.0)', 'value' => 'cc-by-3.0', 'default' => false],
    ['label' => 'Creative Commons Attribution-ShareAlike 3.0 (CC BY-SA 3.0)', 'value' => 'cc-by-sa-3.0', 'default' => false],
];

/* The fields. */
$plan = [];
if (!$fs->getFieldByHandle('provenanceKind')) { $plan['provenanceKind'] = 'create Dropdown: ' . implode(', ', array_map(fn($o) => $o['value'] ?: '(empty)', $KIND)); }
if (!$fs->getFieldByHandle('sourceUrl')) { $plan['sourceUrl'] = 'create Link (URL only)'; }
if (!$fs->getFieldByHandle('acquiredDate')) { $plan['acquiredDate'] = 'create PlainText, YYYY-MM-DD'; }
$lic = $fs->getFieldByHandle('license');
if (!$lic) { echo 'REFUSING: the license field does not exist' . PHP_EOL; return; }
$have = array_map(fn($o) => $o['value'], $lic->options);
$addLic = array_values(array_filter($NEW_LICENSES, fn($o) => !in_array($o['value'], $have, true)));
if ($addLic) { $plan['license'] = 'add options ' . implode(', ', array_map(fn($o) => $o['value'], $addLic)) . ' (existing kept: ' . implode(', ', $have) . ')'; }
foreach ($plan as $h => $what) { echo '   ' . str_pad($h, 16) . $what . PHP_EOL; }

$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$layout = $vol->getFieldLayout();
$onLayout = array_map(fn($f) => $f->handle, $layout->getCustomFields());
$toLayout = array_values(array_diff(['provenanceKind', 'sourceUrl', 'acquiredDate'], $onLayout));
if ($toLayout) { echo '   layout          add ' . implode(', ', $toLayout) . ' to archiveMedia, after license' . PHP_EOL; }
if (!$plan && !$toLayout) { echo 'nothing to do' . PHP_EOL; return; }

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

if (isset($plan['provenanceKind'])) {
    $f = new \craft\fields\Dropdown(['name' => 'Provenance', 'handle' => 'provenanceKind', 'options' => $KIND,
        'instructions' => 'How the archive came to hold this image. Empty means it has not been established yet, which is itself a fact worth seeing. There is no value for a generated image: generated images do not enter the archive.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('provenanceKind: ' . json_encode($f->getFirstErrors())); }
}
if (isset($plan['sourceUrl'])) {
    $f = new \craft\fields\Link(['name' => 'Source URL', 'handle' => 'sourceUrl', 'types' => ['url'], 'maxLength' => 255, 'showLabelField' => false,
        'instructions' => 'Where an outside image was obtained: the catalogue record or the file page, not the image file itself. Empty for the mirror, whose path is in legacySourcePath.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('sourceUrl: ' . json_encode($f->getFirstErrors())); }
}
if (isset($plan['acquiredDate'])) {
    $f = new \craft\fields\PlainText(['name' => 'Acquired', 'handle' => 'acquiredDate',
        'instructions' => 'The date the archive obtained this file, YYYY-MM-DD. Not the date of the picture: that is dateAsPrinted and dateEdtf.']);
    if (!$fs->saveField($f)) { throw new \RuntimeException('acquiredDate: ' . json_encode($f->getFirstErrors())); }
}
if ($addLic) {
    $lic->options = array_merge($lic->options, $addLic);
    if (!$fs->saveField($lic)) { throw new \RuntimeException('license: ' . json_encode($lic->getFirstErrors())); }
}
if ($toLayout) {
    $layout = $vol->getFieldLayout();
    $tabs = $layout->getTabs();
    $ti = 0; $at = null;
    foreach ($tabs as $i => $tab) {
        foreach (array_values($tab->getElements()) as $j => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->attribute() === 'license') { $ti = $i; $at = $j + 1; }
        }
    }
    $els = array_values($tabs[$ti]->getElements());
    foreach ($toLayout as $h) {
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h))]);
        if ($at !== null) { $at++; }
    }
    $tabs[$ti]->setElements($els);
    $layout->setTabs($tabs);
    $vol->setFieldLayout($layout);
    if (!Craft::$app->getVolumes()->saveVolume($vol)) { throw new \RuntimeException('layout: ' . json_encode($vol->getFirstErrors())); }
}

/* Read back. */
$short = [];
$fl = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia')->getFieldLayout();
foreach (['provenanceKind', 'sourceUrl', 'acquiredDate', 'license'] as $h) { if (!$fl->getFieldByHandle($h)) { $short[] = "$h is not on the archiveMedia layout"; } }
$now = array_map(fn($o) => $o['value'], $fs->getFieldByHandle('license')->options);
foreach ($NEW_LICENSES as $o) { if (!in_array($o['value'], $now, true)) { $short[] = 'license lacks ' . $o['value']; } }
foreach ($have as $v) { if (!in_array($v, $now, true)) { $short[] = 'license lost existing option ' . $v; } }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: three fields on the layout, license has ' . count($now) . ' options') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_asset_provenance_fields.php', count($plan) + ($toLayout ? 1 : 0), $short ? 'SHORT: ' . implode('; ', $short) : 'verified: fields, layout, licence options',
    'provenanceKind, sourceUrl, acquiredDate; permission and CC BY / BY-SA 3.0 and 4.0');
if ($short) { throw new \RuntimeException('add_asset_provenance_fields: ' . implode('; ', $short)); }
