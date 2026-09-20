/**
 * Fields for an enhanced derivative of a scan.
 *
 * An upscaled, denoised or colourised version of a photograph is a new object,
 * not a better copy of the old one. It is useful to look at and it is not
 * evidence: the pixels were invented by a model, however plausible they are. An
 * archive that quietly replaces the scan with the enhancement has destroyed the
 * only thing it was holding.
 *
 * So the enhancement is its own asset, related back to the original, and the
 * original stays the record. The four fields go on the enhanced file:
 *
 *   enhancedFrom        the original asset. This is what makes it a derivative
 *                       rather than a second scan, and it is the direction that
 *                       matters: the derivative points at its source, so the
 *                       original needs no field and nothing about it changes.
 *   enhancementMethod   what was done and with what. "Topaz Gigapixel 7,
 *                       4x, Standard v2" rather than "AI upscale". A reader
 *                       cannot judge the result without knowing the process,
 *                       and neither can we in five years.
 *   enhancedBy          who did it.
 *   enhancedDate        when.
 *
 * What the templates do with them, and this is the part that matters:
 *
 *   the caption prints the method and date under the image
 *   an "Enhanced" chip sits on the picture itself
 *   the lightbox offers both and opens the ORIGINAL first
 *   the citation and the JSON-LD name the ORIGINAL, always
 *
 * The last one is not a detail. Structured data is an assertion to the rest of
 * the web about what this archive holds, and the thing it holds is the scan.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_enhanced_derivative_fields.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$VOLUME = 'archiveMedia';

$FIELDS = [
    ['handle' => 'enhancedFrom', 'name' => 'Enhanced From', 'type' => 'assets',
     'instructions' => 'The original scan this file was made from. Setting this marks the file as a '
                     . 'derivative: the archive holds the original as the record, and this as a '
                     . 'rendering of it. Leave it empty on an original.'],
    ['handle' => 'enhancementMethod', 'name' => 'Enhancement Method', 'type' => 'text',
     'instructions' => 'What was done and with what, precisely enough to be judged and repeated. '
                     . '"Topaz Gigapixel 7, 4x, Standard v2" rather than "AI upscale". Include the '
                     . 'model and settings where there are any.'],
    ['handle' => 'enhancedBy', 'name' => 'Enhanced By', 'type' => 'text',
     'instructions' => 'Who produced the enhancement.'],
    ['handle' => 'enhancedDate', 'name' => 'Enhanced Date', 'type' => 'date',
     'instructions' => 'When the enhancement was produced.'],
];

$fs = Craft::$app->getFields();
$vs = Craft::$app->getVolumes();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$made = [];
foreach ($FIELDS as $def) {
    $f = $fs->getFieldByHandle($def['handle']);
    if ($f !== null) {
        echo str_pad($def['handle'], 22) . 'exists already, left alone' . PHP_EOL;
        $made[$def['handle']] = $f;
        continue;
    }
    echo str_pad($def['handle'], 22) . 'would create, ' . $def['type'] . ', "' . $def['name'] . '"' . PHP_EOL;
    if (!$APPLY) { continue; }

    switch ($def['type']) {
        case 'assets':
            $new = new \craft\fields\Assets();
            $new->maxRelations = 1;
            $new->allowSelfRelations = false;
            break;
        case 'date':
            $new = new \craft\fields\Date();
            $new->showTime = false;
            break;
        default:
            $new = new \craft\fields\PlainText();
            $new->multiline = false;
    }
    $new->name = $def['name'];
    $new->handle = $def['handle'];
    $new->instructions = $def['instructions'];
    if (!$fs->saveField($new)) {
        echo '   FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
        return;
    }
    $made[$def['handle']] = $fs->getFieldByHandle($def['handle']);
    echo '   created' . PHP_EOL;
}

/* ------------------------------------------------------- the asset layout */

echo PHP_EOL;
$volume = $vs->getVolumeByHandle($VOLUME);
if (!$volume) { echo 'volume ' . $VOLUME . ' NOT FOUND' . PHP_EOL; return; }

$layout = $volume->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

$want = [];
foreach ($FIELDS as $def) { if (!in_array($def['handle'], $present, true)) { $want[] = $def['handle']; } }

if (!$want) {
    echo 'the ' . $VOLUME . ' layout already carries all four' . PHP_EOL;
} else {
    echo 'would add to the ' . $VOLUME . ' asset layout: ' . implode(', ', $want) . PHP_EOL;
    if ($APPLY) {
        $tabs = $layout->getTabs();
        /* Their own tab. They describe the file's provenance rather than its
           subject, and mixing them into Content would invite somebody to fill
           them in on an original. */
        $tab = null;
        foreach ($tabs as $t) { if ($t->name === 'Enhancement') { $tab = $t; } }
        if ($tab === null) {
            $tab = new \craft\models\FieldLayoutTab(['name' => 'Enhancement', 'sortOrder' => count($tabs) + 1]);
            $tab->setElements([]);
            $tabs[] = $tab;
        }
        $els = $tab->getElements();
        foreach ($want as $h) {
            if (isset($made[$h])) { $els[] = new \craft\fieldlayoutelements\CustomField($made[$h]); }
        }
        $tab->setElements($els);
        $layout->setTabs($tabs);
        $volume->setFieldLayout($layout);
        if ($vs->saveVolume($volume)) { echo 'added to the layout' . PHP_EOL; }
        else { echo 'FAILED: ' . implode('; ', $volume->getFirstErrors()) . PHP_EOL; }
    }
}

/* ------------------------------------------------------------- the state */

echo PHP_EOL;
$derivatives = 0; $total = 0;
foreach (\craft\elements\Asset::find()->limit(null)->all() as $a) {
    $total++;
    $h = [];
    foreach (($a->getFieldLayout() ? $a->getFieldLayout()->getCustomFields() : []) as $f) { $h[] = $f->handle; }
    if (!in_array('enhancedFrom', $h, true)) { continue; }
    if ($a->getFieldValue('enhancedFrom')->count()) { $derivatives++; }
}
echo 'assets in the volume: ' . $total . PHP_EOL;
echo 'already marked as a derivative: ' . $derivatives . PHP_EOL;

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
echo 'The templates read a missing enhancedFrom as "this is an original", so' . PHP_EOL;
echo 'every existing asset behaves unchanged before and after.' . PHP_EOL;
