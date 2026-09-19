$tot=['none'=>0,'some'=>0];
$rows=[];
foreach (Craft::$app->entries->getAllSections() as $s) {
    foreach (\craft\elements\Entry::find()->section($s->handle)->status(null)->all() as $e) {
        $have = array_map(fn($f)=>$f->handle, $e->getFieldLayout()->getCustomFields());
        $n=0;
        foreach (['featuredImage','recordImages','bandImage'] as $fh) {
            if (in_array($fh,$have,true)) { $n += (int)$e->$fh->count(); }
        }
        $rows[$s->handle][$n>0?'some':'none'] = ($rows[$s->handle][$n>0?'some':'none']??0)+1;
    }
}
foreach ($rows as $sec=>$r) echo str_pad($sec,18).'with images: '.str_pad((string)($r['some']??0),5).'without: '.($r['none']??0)."\n";
echo "\ntotal assets: ".\craft\elements\Asset::find()->status(null)->count()."\n";
$attached = (int)(new \craft\db\Query())->select('COUNT(DISTINCT r.targetId)')->from('{{%relations}} r')
  ->innerJoin('{{%elements}} te','te.id = r.targetId')->innerJoin('{{%assets}} a','a.id = r.targetId')
  ->where(['te.dateDeleted'=>null])->scalar();
echo "assets attached to something: $attached\n";
