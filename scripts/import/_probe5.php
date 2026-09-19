$t = Craft::$app->entries->getEntryTypeByHandle('article');
foreach ($t->getFieldLayout()->getCustomFields() as $f) {
    echo str_pad($f->handle,26).get_class($f)."\n";
}
