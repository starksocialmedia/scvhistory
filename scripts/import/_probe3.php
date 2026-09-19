$FIELDS = ['legacyUrl','placeLegacyUrl','personLegacyUrl','orgLegacyUrl','groupLegacyUrl','eventLegacyUrl','obitLegacyUrl','mpLegacyUrl'];
$tot = 0; $byField = [];
$shapes = [];
foreach (Craft::$app->entries->getAllSections() as $s) {
    $entries = \craft\elements\Entry::find()->section($s->handle)->status(null)->all();
    foreach ($entries as $e) {
        $have = array_map(fn($f)=>$f->handle, $e->getFieldLayout()->getCustomFields());
        foreach ($FIELDS as $fh) {
            if (!in_array($fh, $have, true)) continue;
            $v = trim((string)$e->$fh);
            if ($v === '') continue;
            $byField[$s->handle.'.'.$fh] = ($byField[$s->handle.'.'.$fh] ?? 0) + 1;
            $tot++;
            $k = preg_replace('~[^/]+~','X', parse_url($v, PHP_URL_PATH) ?? $v);
            $pre = preg_match('~^https?://~', $v) ? 'ABS' : (str_starts_with($v,'/') ? 'ROOT' : 'REL');
            $shapes[$pre] = ($shapes[$pre] ?? 0) + 1;
        }
    }
}
echo "total non-empty legacy url values: $tot\n\n";
ksort($byField); foreach ($byField as $k=>$n) echo str_pad($k,34).$n."\n";
echo "\nshapes: "; print_r($shapes);
echo "\n-- legacyKey non-empty per section --\n";
foreach (Craft::$app->entries->getAllSections() as $s) {
    $n=0; $m=0;
    foreach (\craft\elements\Entry::find()->section($s->handle)->status(null)->all() as $e) {
        $have = array_map(fn($f)=>$f->handle, $e->getFieldLayout()->getCustomFields());
        if (!in_array('legacyKey',$have,true)) continue;
        $m++; if (trim((string)$e->legacyKey)!=='') $n++;
    }
    echo str_pad($s->handle,20)."$n of $m\n";
}
