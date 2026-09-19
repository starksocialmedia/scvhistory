$want = ['legacyUrl','legacyKey','legacyHtml','sourcePath','placeLegacyUrl','personLegacyUrl','orgLegacyUrl','groupLegacyUrl','eventLegacyUrl','obitLegacyUrl','mpLegacyUrl','legacyCategory'];
foreach (Craft::$app->entries->getAllEntryTypes() as $t) {
    $h = [];
    foreach ($t->getFieldLayout()->getCustomFields() as $f) {
        if (in_array($f->handle, $want, true)) $h[] = $f->handle;
    }
    if ($h) echo str_pad($t->handle, 20) . implode(', ', $h) . "\n";
}
echo "\n-- sample values --\n";
foreach ([['articles','legacyUrl'],['places','placeLegacyUrl'],['persons','personLegacyUrl'],['warMemorials','mpLegacyUrl'],['organizations','orgLegacyUrl'],['groups','groupLegacyUrl'],['collections','legacyUrl']] as [$sec,$fh]) {
    $e = \craft\elements\Entry::find()->section($sec)->status(null)->limit(3)->all();
    foreach ($e as $x) {
        $v = null; try { $v = $x->$fh; } catch (\Throwable $ex) { $v = 'ERR'; }
        echo str_pad($sec,15) . str_pad($fh,18) . '#' . $x->id . '  ' . substr((string)$v,0,70) . "  |key=" . (in_array('legacyKey', array_map(fn($f)=>$f->handle, $x->getFieldLayout()->getCustomFields())) ? substr((string)$x->legacyKey,0,30) : '-') . "\n";
    }
}
