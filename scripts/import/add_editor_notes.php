/**
 * Renames webmasterNoteTop/Bottom to "Editor's note (top/bottom)" and adds them
 * to every entry type that has no note pair of its own.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_editor_notes.php'))"
 */

$fieldsSvc = Craft::$app->getFields();
$entriesSvc = Craft::$app->getEntries();

$want = [
    'webmasterNoteTop' => ['name' => "Editor's note (top)", 'instructions' => 'Shown in a cream box above the record text. Context, corrections, provenance.'],
    'webmasterNoteBottom' => ['name' => "Editor's note (bottom)", 'instructions' => 'Shown in a cream box below the record text. Sources, courtesy lines, download notes.'],
];
$fields = [];
foreach ($want as $handle => $d) {
    $f = $fieldsSvc->getFieldByHandle($handle);
    if (!$f) {
        $f = new \craft\fields\PlainText();
        $f->handle = $handle;
        $f->multiline = true;
        $f->initialRows = 4;
    }
    $f->name = $d['name'];
    $f->instructions = $d['instructions'];
    echo $handle . ': ' . ($fieldsSvc->saveField($f) ? 'ok' : 'FAILED ' . json_encode($f->getErrors())) . PHP_EOL;
    $fields[$handle] = $fieldsSvc->getFieldByHandle($handle);
}

$anyNote = '/(webmaster|Webmaster)Note(Top|Bottom)$/';

foreach ($entriesSvc->getAllEntryTypes() as $type) {
    $layout = $type->getFieldLayout();
    $handles = [];
    foreach ($layout->getCustomFields() as $cf) { $handles[] = $cf->handle; }
    $hasPair = count(array_filter($handles, fn($h) => preg_match($anyNote, $h))) >= 2;
    if ($hasPair) { echo str_pad($type->handle, 18) . 'has notes' . PHP_EOL; continue; }

    $tabs = $layout->getTabs();
    $first = $tabs[0];
    $els = $first->getElements();
    $bodyAt = null;
    foreach ($els as $i => $el) {
        if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->getField()->handle === 'body') { $bodyAt = $i; }
    }
    $top = new \craft\fieldlayoutelements\CustomField($fields['webmasterNoteTop']);
    $bottom = new \craft\fieldlayoutelements\CustomField($fields['webmasterNoteBottom']);
    if ($bodyAt !== null) {
        array_splice($els, $bodyAt + 1, 0, [$bottom]);
        array_splice($els, $bodyAt, 0, [$top]);
    } else {
        $els[] = $top; $els[] = $bottom;
    }
    $first->setElements($els);
    $tabs[0] = $first;
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    echo str_pad($type->handle, 18) . ($entriesSvc->saveEntryType($type) ? 'added notes around body' : 'FAILED ' . json_encode($type->getErrors())) . PHP_EOL;
}
