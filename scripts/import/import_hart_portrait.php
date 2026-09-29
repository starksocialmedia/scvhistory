/**
 * William S. Hart: the person record under his name, and his portrait.
 *
 * One pass, so it goes in once (Nathan, 29 September 2026):
 *
 *   1. #16356 "Bill Hart", the only Hart person record, becomes William S. Hart
 *      with Bill Hart as an alias (the rename of rename_hart_person.php, done here
 *      so the two changes land together).
 *   2. inventory/incoming/Williamshart.jpg is imported and set as his
 *      featuredImage.
 *
 * THE IMAGE AND ITS RIGHTS. Public domain. The source is the Library of
 * Congress, Prints and Photographs Division, digital ID cph.3c03842; the
 * photographer is Chircosta; the date is about 7 February 1918. It was obtained
 * through Wikimedia Commons, File:Williamshart.jpg, whose file is byte-identical
 * to this one (sha1 b7ba40e94a0b95ec5f674f3b082ea5a2a7e45785). Commons is the
 * route; the Library is the source.
 *
 * It is the first asset in the archive that did not come from the Reggie mirror,
 * so it must not look like a sourced scan: legacySourcePath and photoSourceCode
 * stay empty, and it goes in the folder outside/, not legacy/.
 *
 * THE MINIMUM, NOT THE SCHEMA. The asset layout already has license, creator,
 * source, dateAsPrinted and dateEdtf, which is enough. It has no sourceUrl,
 * acquiredDate or provenanceKind; until those exist, the source sentence carries
 * the Commons URL and the date obtained in words. Owed: those three fields, and
 * permission / cc-by / cc-by-sa options on license.
 *
 * Idempotent: an asset with this filename is found and not imported twice.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_hart_portrait.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$PERSON = 16356;
$TITLE = 'William S. Hart';
$SLUG = 'william-s-hart';
$ALIAS = 'Bill Hart';
$FILE = \Craft::getAlias('@root') . '/inventory/incoming/Williamshart.jpg';
$SHA1 = 'b7ba40e94a0b95ec5f674f3b082ea5a2a7e45785';
$FILENAME = 'william-s-hart-loc-cph-3c03842.jpg';
$FOLDER = 'outside';
$ASSET = [
    'title' => 'William S. Hart, about 1918',
    'alt' => 'Portrait of William S. Hart, about 1918',
    'fields' => [
        'creator' => 'Chircosta',
        'dateAsPrinted' => 'c. February 7, 1918',
        'dateEdtf' => '1918-02-07~',
        'license' => 'public-domain',
        'source' => 'Library of Congress, Prints and Photographs Division, digital ID cph.3c03842. '
            . 'Obtained through Wikimedia Commons, https://commons.wikimedia.org/wiki/File:Williamshart.jpg, '
            . 'on 29 September 2026; the Commons file is byte-identical to this one.',
    ],
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();

/* ------------------------------------------------ 1. the person, the guard */
$named = [];
foreach (\craft\elements\Entry::find()->section(['persons', 'warMemorials', 'militaryProfiles'])->status(null)->all() as $e) {
    $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
    $text = $e->title . ' ' . (in_array('fullName', $h, true) ? $e->fullName : '') . ' ' . (in_array('personAliases', $h, true) ? $e->personAliases : '');
    if (preg_match('~\bHart\b~', $text) && !preg_match('~\bHarte\b~', $text)) { $named[$e->id] = $e; }
}
if (count($named) !== 1 || !isset($named[$PERSON])) {
    echo 'REFUSING: expected only #' . $PERSON . ' to name Hart; found ' . implode(', ', array_map(fn($e) => '#' . $e->id, $named)) . '. That is a merge.' . PHP_EOL;
    return;
}
$p = $named[$PERSON];
$has = [];
foreach ($p->getFieldLayout()->getCustomFields() as $f) { $has[$f->handle] = true; }
foreach (['fullName', 'personAliases', 'featuredImage'] as $need) {
    if (!isset($has[$need])) { echo 'REFUSING: the person layout lacks ' . $need . PHP_EOL; return; }
}
$clash = \craft\elements\Entry::find()->section('persons')->slug($SLUG)->status(null)->one();
if ($clash && $clash->id !== $PERSON) { echo 'REFUSING: slug ' . $SLUG . ' belongs to #' . $clash->id . PHP_EOL; return; }
$aliases = array_values(array_filter(array_map('trim', explode("\n", (string)$p->personAliases))));
$newAliases = in_array($ALIAS, $aliases, true) ? $aliases : array_merge($aliases, [$ALIAS]);
echo 'person #' . $PERSON . ': "' . $p->title . '" -> "' . $TITLE . '", slug ' . $p->slug . ' -> ' . $SLUG
    . ', fullName ' . (trim((string)$p->fullName) ?: '(empty)') . ' -> ' . (trim((string)$p->fullName) ?: $TITLE)
    . ', aliases -> ' . implode(' / ', $newAliases) . PHP_EOL;
$inbound = (int)\craft\elements\Entry::find()->status(null)->relatedTo(['targetElement' => $PERSON])->count();
echo '   inbound links, unchanged: ' . $inbound . PHP_EOL;
$current = $p->featuredImage->one();
/* Already done: a second run is a no-op and saves nothing. */
if ($current && $current->filename === $FILENAME && $p->title === $TITLE && $p->slug === $SLUG && in_array($ALIAS, $aliases, true)) {
    echo 'already done: #' . $PERSON . ' is William S. Hart with the portrait (asset #' . $current->id . '). Nothing to write.' . PHP_EOL;
    return;
}
if ($current) { echo '   REFUSING: featuredImage is already set to another asset (#' . $current->id . ' ' . $current->filename . '); a value someone set is never replaced' . PHP_EOL; return; }

/* ------------------------------------------------ 2. the file */
if (!is_file($FILE)) { echo 'REFUSING: ' . $FILE . ' not found' . PHP_EOL; return; }
$sha = sha1_file($FILE);
echo PHP_EOL . 'file ' . basename($FILE) . ': ' . filesize($FILE) . ' bytes, sha1 ' . $sha . ($sha === $SHA1 ? ' (matches Commons)' : ' DOES NOT MATCH Commons ' . $SHA1) . PHP_EOL;
if ($sha !== $SHA1) { echo 'REFUSING: not the file whose rights were established' . PHP_EOL; return; }
[$w, $h] = getimagesize($FILE);
echo '   ' . $w . ' x ' . $h . PHP_EOL;

$volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$layout = $volume->getFieldLayout();
$absent = array_values(array_filter(array_keys($ASSET['fields']), fn($f) => !$layout->getFieldByHandle($f)));
if ($absent) { echo 'REFUSING: the asset layout lacks ' . implode(', ', $absent) . PHP_EOL; return; }
$existing = \craft\elements\Asset::find()->volumeId($volume->id)->filename($FILENAME)->one();
$root = Craft::$app->getAssets()->getRootFolderByVolumeId($volume->id);
$folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => $FOLDER . '/']);
echo '   asset: ' . ($existing ? 'already held as #' . $existing->id : 'import as archiveMedia/' . $FOLDER . '/' . $FILENAME . ($folder ? '' : ' (folder ' . $FOLDER . '/ to be created)')) . PHP_EOL;
foreach ($ASSET['fields'] as $k => $v) { echo '   ' . str_pad($k, 14) . $v . PHP_EOL; }
echo '   ' . str_pad('legacySourcePath', 14) . '(empty: not from the mirror)' . PHP_EOL;
echo PHP_EOL . 'owed, not added here: sourceUrl, acquiredDate, provenanceKind; license options permission, cc-by, cc-by-sa' . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* ------------------------------------------------ apply */
$assets = Craft::$app->getAssets();
if (!$folder) {
    $folder = new \craft\models\VolumeFolder(['parentId' => $root->id, 'name' => $FOLDER, 'volumeId' => $volume->id, 'path' => $FOLDER . '/']);
    $assets->createFolder($folder);
    $folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => $FOLDER . '/']);
    if (!$folder) { throw new \RuntimeException('import_hart_portrait: could not create folder ' . $FOLDER . '/'); }
}
$asset = $existing;
if (!$asset) {
    $tmp = sys_get_temp_dir() . '/' . $FILENAME;
    if (!copy($FILE, $tmp)) { throw new \RuntimeException('import_hart_portrait: could not stage the file'); }
    $asset = new \craft\elements\Asset();
    $asset->tempFilePath = $tmp;
    $asset->setFilename($FILENAME);
    $asset->newFolderId = $folder->id;
    $asset->setVolumeId($volume->id);
    $asset->setScenario(\craft\elements\Asset::SCENARIO_CREATE);
    $asset->avoidFilenameConflicts = false;
    if (!$elements->saveElement($asset)) { throw new \RuntimeException('import_hart_portrait: asset ' . json_encode($asset->getFirstErrors())); }
}
/* Reloaded before the fields are set: a create-scenario asset refuses a second save. */
$asset = \craft\elements\Asset::find()->id($asset->id)->one();
$asset->title = $ASSET['title'];
$asset->alt = $ASSET['alt'];
$asset->setFieldValues($ASSET['fields']);
if (!$elements->saveElement($asset)) { throw new \RuntimeException('import_hart_portrait: asset fields ' . json_encode($asset->getFirstErrors())); }

$p = \craft\elements\Entry::find()->id($PERSON)->status(null)->one();
$p->title = $TITLE;
$p->slug = $SLUG;
if (trim((string)$p->fullName) === '') { $p->setFieldValue('fullName', $TITLE); }
$p->setFieldValue('personAliases', implode("\n", $newAliases));
$p->setFieldValue('featuredImage', [$asset->id]);
if (!$elements->saveElement($p)) { throw new \RuntimeException('import_hart_portrait: person ' . json_encode($p->getFirstErrors())); }

/* ------------------------------------------------ read back */
$short = [];
$a = \craft\elements\Asset::find()->id($asset->id)->one();
if (!$a) { $short[] = 'asset missing'; }
else {
    foreach ($ASSET['fields'] as $k => $v) {
        $got = $a->getFieldValue($k);
        $got = is_object($got) && property_exists($got, 'value') ? (string)$got->value : trim((string)$got);
        if ($got !== $v) { $short[] = "asset $k reads \"" . mb_substr($got, 0, 40) . '"'; }
    }
    if (trim((string)$a->getFieldValue('legacySourcePath')) !== '') { $short[] = 'asset legacySourcePath is set; it must be empty'; }
    if (sha1_file($FILE) !== $SHA1) { $short[] = 'source file changed'; }
}
$b = \craft\elements\Entry::find()->id($PERSON)->status(null)->one();
if ($b->title !== $TITLE) { $short[] = "person title reads \"{$b->title}\""; }
if ($b->slug !== $SLUG) { $short[] = "person slug reads \"{$b->slug}\""; }
if (!in_array($ALIAS, array_map('trim', explode("\n", (string)$b->personAliases)), true)) { $short[] = 'alias Bill Hart missing'; }
if (($b->featuredImage->one()->id ?? null) !== $asset->id) { $short[] = 'featuredImage is not the portrait'; }
$after = (int)\craft\elements\Entry::find()->status(null)->relatedTo(['targetElement' => $PERSON])->count();
if ($after !== $inbound) { $short[] = "inbound reads $after, expected $inbound"; }
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: portrait #' . $asset->id . ' with its rights; #' . $PERSON . ' renamed, aliased, featured; ' . $inbound . ' inbound intact') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('import_hart_portrait.php', 2, $short ? 'SHORT: ' . implode('; ', $short) : 'verified: portrait and rename',
    'asset #' . $asset->id . ' LoC cph.3c03842, public domain, via Commons; #16356 Bill Hart -> William S. Hart');
if ($short) { throw new \RuntimeException('import_hart_portrait: ' . implode('; ', $short)); }
