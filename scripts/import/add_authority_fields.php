$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
$defs = [
  'wikidataId' => ['Wikidata ID', 'The Q-number, e.g. Q1234567. Links this record to the global authority file.', ['person','place','organization','event','warMemorial','group']],
  'viafId' => ['VIAF ID', 'Virtual International Authority File identifier, for people who appear in library catalogues.', ['person']],
];
foreach ($defs as $h => [$name, $instr, $types]) {
    $f = $fs->getFieldByHandle($h);
    if ($f === null) {
        $f = new \craft\fields\PlainText();
        $f->name = $name; $f->handle = $h; $f->instructions = $instr;
        echo str_pad($h, 14) . 'would create' . PHP_EOL;
        if ($APPLY) { $fs->saveField($f); $f = $fs->getFieldByHandle($h); }
    } else { echo str_pad($h, 14) . 'exists' . PHP_EOL; }
    if (!$APPLY || !$f) { continue; }
    foreach ($types as $th) {
        $t = $svc->getEntryTypeByHandle($th);
        if (!$t) { continue; }
        $l = $t->getFieldLayout();
        foreach ($l->getCustomFields() as $c) { if ($c->handle === $h) { continue 2; } }
        $tabs = $l->getTabs(); $els = $tabs[0]->getElements();
        $els[] = new \craft\fieldlayoutelements\CustomField($f);
        $tabs[0]->setElements($els); $l->setTabs($tabs); $t->setFieldLayout($l);
        echo '  ' . str_pad($th, 16) . ($svc->saveEntryType($t) ? 'added' : 'FAILED') . PHP_EOL;
    }
}
echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
