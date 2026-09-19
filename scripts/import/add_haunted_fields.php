$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();
$defs = [
  'hauntedStatus' => ['Reported haunted', 'dropdown'],
  'hauntedAccount' => ['The haunting account', 'text'],
  'hauntedSource' => ['Source of the account', 'text'],
];
$made = [];
foreach ($defs as $h => [$name, $kind]) {
    $f = $fs->getFieldByHandle($h);
    if ($f === null) {
        if ($kind === 'dropdown') {
            $f = new \craft\fields\Dropdown();
            $f->options = [
                ['label' => 'Not reported', 'value' => '', 'default' => true],
                ['label' => 'Reported haunted', 'value' => 'reported'],
                ['label' => 'Local legend', 'value' => 'legend'],
                ['label' => 'Claim investigated or disputed', 'value' => 'disputed'],
            ];
        } else {
            $f = new \craft\fields\PlainText();
            $f->multiline = ($h === 'hauntedAccount');
            $f->initialRows = 4;
        }
        $f->name = $name;
        $f->handle = $h;
        $f->instructions = $h === 'hauntedAccount'
            ? 'What is said to happen there, in the words of the account. Attribute it rather than asserting it.'
            : ($h === 'hauntedSource' ? 'Where the account comes from: a newspaper story, a book, an oral history, a local tradition.' : 'Whether a haunting has been reported at this site. The archive records the claim, not the phenomenon.');
        echo str_pad($h, 18) . 'would create' . PHP_EOL;
        if ($APPLY) { $fs->saveField($f); $f = $fs->getFieldByHandle($h); }
    } else { echo str_pad($h, 18) . 'exists' . PHP_EOL; }
    $made[$h] = $f;
}
if (!$APPLY) { echo 'DRY RUN' . PHP_EOL; return; }
foreach (['place', 'organization'] as $th) {
    $t = $svc->getEntryTypeByHandle($th);
    if (!$t) { continue; }
    $l = $t->getFieldLayout();
    $have = [];
    foreach ($l->getCustomFields() as $c) { $have[] = $c->handle; }
    $tabs = $l->getTabs();
    $els = $tabs[0]->getElements();
    $added = [];
    foreach ($made as $h => $f) {
        if (in_array($h, $have, true) || !$f) { continue; }
        $els[] = new \craft\fieldlayoutelements\CustomField($f);
        $added[] = $h;
    }
    if (!count($added)) { echo str_pad($th, 18) . 'has them' . PHP_EOL; continue; }
    $tabs[0]->setElements($els);
    $l->setTabs($tabs);
    $t->setFieldLayout($l);
    echo str_pad($th, 18) . ($svc->saveEntryType($t) ? 'added ' . implode(', ', $added) : 'FAILED') . PHP_EOL;
}
