/**
 * IMDb as an authority ID on person records, beside Wikidata, VIAF and Bioguide
 * (Nathan, 2 October 2026: "Add his IMDb ID as an authority field alongside
 * Wikidata and Bioguide").
 *
 *   imdbId   PlainText, on the person layout: the IMDb name id, "nm0366586".
 *            Emitted as schema.org sameAs and as an identifier
 *            (templates/_partials/head/schema.twig), linked in the person page's
 *            EXTERNAL box, documented in DATA-MODEL's identifier table
 *            (Wikidata P345).
 *
 * Its own script, before the profile that fills it. Idempotent. Dry run by
 * default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_imdb_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to project config' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields(); $svc = Craft::$app->getEntries();
$type = $svc->getEntryTypeByHandle('person');
$f = $fs->getFieldByHandle('imdbId');
$inLayout = in_array('imdbId', array_map(fn($x) => $x->handle, $type->getFieldLayout()->getCustomFields()), true);
echo 'imdbId: ' . ($f ? 'exists' : 'create (PlainText)') . ($inLayout ? ', in the person layout' : ', add to the person layout') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$f) {
    $f = new \craft\fields\PlainText(); $f->name = 'IMDb ID'; $f->handle = 'imdbId';
    $f->instructions = 'The IMDb name id, as nm0366586. An authority ID, emitted as sameAs.';
    if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getErrors())); }
    $fs->refreshFields(); $f = $fs->getFieldByHandle('imdbId');
}
if (!$inLayout) {
    $type = $svc->getEntryTypeByHandle('person'); $layout = $type->getFieldLayout(); $tabs = $layout->getTabs();
    $els = $tabs[0]->getElements(); $els[] = new \craft\fieldlayoutelements\CustomField($f); $tabs[0]->setElements($els);
    $layout->setTabs($tabs); $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { throw new \RuntimeException(json_encode($type->getErrors())); }
}
$fs->refreshFields();
$ok = in_array('imdbId', array_map(fn($x) => $x->handle, $svc->getEntryTypeByHandle('person')->getFieldLayout()->getCustomFields()), true);
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_imdb_field.php', 1, $ok ? 'verified' : 'SHORT', 'imdbId on the person layout');
if (!$ok) { throw new \RuntimeException('add_bioguide_field: read-back failed'); }
