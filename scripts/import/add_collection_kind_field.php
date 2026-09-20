/**
 * Adds collectionKind, so a collection can say what shape of thing it is.
 *
 * The section holds two different objects under one name. History of the Santa
 * Clarita Valley is a book: 80 chapters in five parts, written to be read from
 * the beginning, and "Start reading" is the right control for it. Selections
 * from Leon Worden is a column: 219 pieces written weekly over fourteen years,
 * where the reader wants the year and the subject and there is no beginning to
 * start at. Making Cents is a column too, 259 weekly pieces by one author. The
 * Old Town Newhall Gazette is a periodical, thirty issues. Abu Ghraib is a
 * topic, sixty pieces by different hands gathered because they are about one
 * thing.
 *
 * Today the only distinction is collectionIsMajor, a checkbox that decides
 * which of two templates renders. It answers "does this deserve a landing
 * page", which is a question about effort, not about the material. A column run
 * with 219 pieces deserves a landing page and must not get a Start reading
 * button.
 *
 *   book        a work written to be read in order. Chapters, parts, a first
 *               page. Start reading belongs here and nowhere else.
 *   column      a run by one author over time. Chronological by year, the
 *               author is the band, no beginning.
 *   catalogue   discrete numbered items: issues of a periodical, a series of
 *               objects. Listed by number or date.
 *   topic       pieces by different hands gathered because they share a
 *               subject. No order but relevance.
 *
 * This creates the field and sets nothing. The value for each of the thirteen
 * is proposed in web/review/collections-inventory-map.md and is a judgement, so
 * it wants a person rather than a script.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_collection_kind_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$HANDLE = 'collectionKind';
$NAME   = 'Collection Kind';
$TYPE   = 'collection';
$NEIGHBOURS = ['collectionIsMajor', 'collectionParts', 'collectionGroups'];

$INSTR =
    'What shape of thing this collection is, which decides how it is listed and '
    . 'which controls it offers. Book: written to be read in order, so it has '
    . 'chapters and a first page, and it is the only kind that offers "Start '
    . 'reading". Column: a run by one author over time, listed by year, with no '
    . 'beginning. Catalogue: discrete numbered items such as issues of a '
    . 'periodical. Topic: pieces by different hands gathered because they share '
    . 'a subject.';

$OPTIONS = [
    ['label' => 'Book, read in order',        'value' => 'book',      'default' => false],
    ['label' => 'Column, one author over time','value' => 'column',   'default' => false],
    ['label' => 'Catalogue, numbered items',  'value' => 'catalogue', 'default' => false],
    ['label' => 'Topic, gathered by subject', 'value' => 'topic',     'default' => false],
];

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 74) . PHP_EOL;

$field = $fs->getFieldByHandle($HANDLE);
if ($field === null) {
    echo $HANDLE . '  would create, Dropdown, "' . $NAME . '"' . PHP_EOL;
    foreach ($OPTIONS as $o) { echo '   ' . str_pad($o['value'], 11) . $o['label'] . PHP_EOL; }
    if ($APPLY) {
        $new = new \craft\fields\Dropdown();
        $new->name = $NAME;
        $new->handle = $HANDLE;
        $new->instructions = $INSTR;
        $new->options = $OPTIONS;
        if (!$fs->saveField($new)) {
            echo 'FAILED: ' . implode('; ', $new->getFirstErrors()) . PHP_EOL;
            return;
        }
        $field = $fs->getFieldByHandle($HANDLE);
        echo 'created' . PHP_EOL;
    }
} else {
    echo $HANDLE . '  exists already, left alone' . PHP_EOL;
}

echo PHP_EOL;
$type = $svc->getEntryTypeByHandle($TYPE);
if (!$type) { echo 'entry type ' . $TYPE . ' NOT FOUND' . PHP_EOL; return; }

$layout = $type->getFieldLayout();
$present = [];
foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }

if (in_array($HANDLE, $present, true)) {
    echo 'the collection layout already carries ' . $HANDLE . PHP_EOL;
} else {
    $tabs = $layout->getTabs();
    $tabIdx = 0; $at = null;
    foreach ($tabs as $ti => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && in_array($el->getField()->handle, $NEIGHBOURS, true)) {
                $tabIdx = $ti; $at = $ei;
            }
        }
    }
    $tabName = $tabs[$tabIdx]->name ?? ('tab ' . ($tabIdx + 1));
    echo 'would add ' . $HANDLE . ' to "' . $tabName . '"'
       . ($at === null ? ' at the end' : ' beside collectionIsMajor') . PHP_EOL;

    if ($APPLY && $field !== null) {
        $els = $tabs[$tabIdx]->getElements();
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($field)]);
        $tabs[$tabIdx]->setElements($els);
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if ($svc->saveEntryType($type)) { echo 'added to the layout' . PHP_EOL; }
        else { echo 'FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }
    }
}

echo PHP_EOL . 'Proposed values, for a person to confirm rather than a script to set:' . PHP_EOL;
$PROPOSED = [
    'history-of-the-santa-clarita-valley' => 'book',
    'story-of-our-valley'                 => 'book',
    'worden'     => 'column',   'boston'      => 'column',
    'manzer'     => 'column',   'coins'       => 'column',
    'newsmaker'  => 'column',   'otn-patti'   => 'column',
    'otn-pauline'=> 'column',   'otn-rioux'   => 'column',
    'otn-whyte'  => 'column',   'otn-gazette' => 'catalogue',
    'iraq'       => 'topic',
];
foreach (\craft\elements\Entry::find()->section('collections')->status(null)->limit(null)->all() as $e) {
    printf("   %-38s %-10s %s\n", $e->slug, $PROPOSED[$e->slug] ?? '?', $e->title);
}

echo PHP_EOL . str_repeat('=', 74) . PHP_EOL;
echo $APPLY ? 'done' : 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
