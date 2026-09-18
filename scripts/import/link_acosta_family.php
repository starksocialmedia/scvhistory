$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$rodolfo = \craft\elements\Entry::find()->id(343)->status(null)->one();
$dante = \craft\elements\Entry::find()->id(341)->status(null)->one();
$rudy = \craft\elements\Entry::find()->section('warMemorials')->search('Acosta')->status(null)->one();
if (!$rodolfo || !$dante || !$rudy) { echo 'missing one of them' . PHP_EOL; return; }
echo 'Rodolfo #' . $rodolfo->id . ' -> Dante #' . $dante->id . ' -> Rudy #' . $rudy->id . PHP_EOL;
$pairs = [[$dante, $rodolfo], [$rudy, $dante]];
foreach ($pairs as [$child, $parent]) {
    $ok = false;
    foreach ($child->getFieldLayout()->getCustomFields() as $f) { if ($f->handle === 'childOf') { $ok = true; } }
    if (!$ok) { echo $child->title . ': no childOf field on this entry type' . PHP_EOL; continue; }
    $ids = $child->getFieldValue('childOf')->ids();
    if (in_array($parent->id, $ids, true)) { echo $child->title . ': already child of ' . $parent->title . PHP_EOL; continue; }
    echo $child->title . ' childOf ' . $parent->title . PHP_EOL;
    if (!$APPLY) { continue; }
    $ids[] = $parent->id;
    $child->setFieldValue('childOf', $ids);
    echo '  ' . ($el->saveElement($child) ? 'saved' : 'FAILED ' . json_encode($child->getErrors())) . PHP_EOL;
}
echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
