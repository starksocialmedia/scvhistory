$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$svc = Craft::$app->getEntries();
$corrections = [
  [
    'title' => 'Cephas L. Bard',
    'slug' => 'cephas-l-bard',
    'aliases' => "Cephas R. Bard\nDr. Cephas R. Bard\nDr. Bard",
    'occupation' => 'Physician',
    'birthDate' => '1843',
    'deathDate' => '1902',
    'note' => "Perkins prints the middle initial as R. The physician was Cephas L. Bard (1843-1902), Ventura County's first doctor, president of the Southern California Medical Society, and brother of Thomas R. Bard. The initial is an error in the original and has been left as Perkins wrote it in the text.",
    'articles' => ['1-early-inhabitants'],
  ],
];
foreach ($corrections as $c) {
    $p = \craft\elements\Entry::find()->section('persons')->slug($c['slug'])->status(null)->one();
    echo ($p ? 'exists #' . $p->id : 'would create') . '  ' . $c['title'] . PHP_EOL;
    foreach ($c['articles'] as $slug) {
        $a = \craft\elements\Entry::find()->section('articles')->slug($slug)->status(null)->one();
        echo '  article: ' . ($a ? $a->title : 'NOT FOUND ' . $slug) . PHP_EOL;
    }
    if (!$APPLY) { continue; }
    if (!$p) {
        $p = new \craft\elements\Entry();
        $p->sectionId = $svc->getSectionByHandle('persons')->id;
        $p->typeId = $svc->getEntryTypeByHandle('person')->id;
        $p->slug = $c['slug'];
    }
    $p->title = $c['title'];
    $p->setFieldValue('fullName', $c['title']);
    $p->setFieldValue('personAliases', $c['aliases']);
    $p->setFieldValue('occupation', $c['occupation']);
    $p->setFieldValue('birthDate', $c['birthDate']);
    $p->setFieldValue('deathDate', $c['deathDate']);
    $p->setFieldValue('personWebmasterNoteTop', $c['note']);
    echo '  person: ' . ($el->saveElement($p) ? 'saved #' . $p->id : 'FAILED ' . json_encode($p->getErrors())) . PHP_EOL;
    foreach ($c['articles'] as $slug) {
        $a = \craft\elements\Entry::find()->section('articles')->slug($slug)->status(null)->one();
        if (!$a) { continue; }
        $ids = $a->getFieldValue('subjectPerson')->ids();
        if (!in_array($p->id, $ids, true)) { $ids[] = $p->id; $a->setFieldValue('subjectPerson', $ids); }
        $cur = trim((string)$a->getFieldValue('webmasterNoteBottom'));
        if (!str_contains($cur, 'Cephas L. Bard')) {
            $a->setFieldValue('webmasterNoteBottom', $cur === '' ? $c['note'] : $cur . "\n\n" . $c['note']);
        }
        echo '  article: ' . ($el->saveElement($a) ? 'linked and noted' : 'FAILED') . PHP_EOL;
    }
}
echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
