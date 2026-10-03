/**
 * A body's current logo, held apart from the archive's records (Nathan,
 * 3 October 2026: "Yes to the separate currentMark field ... A featured image
 * reads as 'this is the record' and a logo is not that. Build it as proposed:
 * shown small beside the title, captioned with the source and retrieval date,
 * kept out of /media and the archive counts, emitted as schema.org logo"; "what
 * distinguishes a logo is what the file is, not where it came from").
 *
 *   assetRole     (assets) what the file is: empty for an archive record, the
 *                 default; current-mark for a body's own present logo;
 *                 decoration for a page ornament. Provenance (provenanceKind)
 *                 stays where it came from: a logo fetched from the body's own
 *                 site is "outside".
 *   rightsNote    (assets) a sentence on the rights a reader should know, shown
 *                 with the file: for Hart, that its site restricts use of the
 *                 logo and that the archive shows it only to identify the
 *                 district.
 *   license       a new option, identifying-use: "Shown to identify the body
 *                 whose mark it is".
 *   currentMark   (organizations) one asset, the body's current logo. Never the
 *                 featured image; a historical letterhead or past logo is an
 *                 archive record in the record's media, not here.
 * Schema only. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_current_mark_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $vs = Craft::$app->getVolumes();
$vol = $vs->getVolumeByHandle('archiveMedia'); $org = $es->getEntryTypeByHandle('organization');
$lic = $fs->getFieldByHandle('license');
$licHas = (bool)array_filter($lic->options, fn($o) => ($o['value'] ?? '') === 'identifying-use');
$plan = [
    'assetRole field' => !$fs->getFieldByHandle('assetRole'),
    'rightsNote field' => !$fs->getFieldByHandle('rightsNote'),
    'currentMark field' => !$fs->getFieldByHandle('currentMark'),
    'license option identifying-use' => !$licHas,
    'assetRole on archiveMedia' => !$vol->getFieldLayout()->getFieldByHandle('assetRole'),
    'rightsNote on archiveMedia' => !$vol->getFieldLayout()->getFieldByHandle('rightsNote'),
    'currentMark on organization' => !$org->getFieldLayout()->getFieldByHandle('currentMark'),
];
foreach ($plan as $k => $todo) { echo str_pad($k, 34) . ($todo ? 'to do' : 'held') . PHP_EOL; }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$save = function ($f) use ($fs) { if (!$fs->saveField($f)) { throw new \RuntimeException($f->handle . ': ' . json_encode($f->getFirstErrors())); } };
if ($plan['assetRole field']) {
    $save(new \craft\fields\Dropdown(['name' => 'Role', 'handle' => 'assetRole',
        'instructions' => 'What this file is. Empty means an archive record, which is nearly everything. A current mark is a body\'s own present logo, shown beside its record\'s title and kept out of /media and the archive\'s counts. Where it came from is Provenance, not this.',
        'options' => [['label' => 'An archive record', 'value' => '', 'default' => true], ['label' => 'A body\'s current mark (logo)', 'value' => 'current-mark', 'default' => false], ['label' => 'Decoration', 'value' => 'decoration', 'default' => false]]]));
}
if ($plan['rightsNote field']) {
    $save(new \craft\fields\PlainText(['name' => 'Rights note', 'handle' => 'rightsNote', 'multiline' => true, 'initialRows' => 2,
        'instructions' => 'A sentence on the rights a reader should know, shown with the file. Say what the rights holder states and on what footing the archive shows it.']));
}
if ($plan['currentMark field']) {
    $save(new \craft\fields\Assets(['name' => 'Current mark', 'handle' => 'currentMark', 'maxRelations' => 1, 'sources' => ['volume:' . $vol->uid], 'viewMode' => 'large',
        'instructions' => 'The body\'s own present logo, from its own website, with Role set to current mark. Not the featured image, and not a historical letterhead or past logo: those are archive records.']));
}
if ($plan['license option identifying-use']) {
    $opts = $lic->options; $opts[] = ['label' => 'Shown to identify the body whose mark it is', 'value' => 'identifying-use', 'default' => false];
    $lic->options = $opts; $save($lic);
}
$addAfter = function ($layoutOwner, array $adds, callable $saveOwner) use ($fs) {
    $layout = $layoutOwner->getFieldLayout(); $tabs = $layout->getTabs(); $done = false;
    foreach ($tabs as $tab) {
        $els = array_values($tab->getElements()); $out = [];
        foreach ($els as $e) {
            $out[] = $e;
            foreach ($adds as $after => $new) {
                if ($e instanceof \craft\fieldlayoutelements\CustomField && $e->getField()->handle === $after && !$layout->getFieldByHandle($new)) { $out[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($new)); $done = true; }
            }
        }
        $tab->setElements($out);
    }
    if ($done) { $layout->setTabs($tabs); $layoutOwner->setFieldLayout($layout); $saveOwner($layoutOwner); }
};
$addAfter($vol, ['provenanceKind' => 'assetRole', 'license' => 'rightsNote'], function ($v) use ($vs) { if (!$vs->saveVolume($v)) { throw new \RuntimeException('archiveMedia: ' . json_encode($v->getFirstErrors())); } });
$addAfter($org, ['featuredImage' => 'currentMark'], function ($t) use ($es) { if (!$es->saveEntryType($t)) { throw new \RuntimeException('organization: ' . json_encode($t->getFirstErrors())); } });

$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $org = Craft::$app->getEntries()->getEntryTypeByHandle('organization');
$ok = $vol->getFieldLayout()->getFieldByHandle('assetRole') && $vol->getFieldLayout()->getFieldByHandle('rightsNote') && $org->getFieldLayout()->getFieldByHandle('currentMark')
    && array_filter(Craft::$app->getFields()->getFieldByHandle('license')->options, fn($o) => ($o['value'] ?? '') === 'identifying-use');
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_current_mark_fields.php', count(array_filter($plan)), $ok ? 'verified' : 'SHORT', 'assetRole and rightsNote on assets, currentMark on organizations, license option identifying-use');
if (!$ok) { throw new \RuntimeException('add_current_mark_fields: short'); }
