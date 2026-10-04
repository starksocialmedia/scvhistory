$fmt = function($v) use (&$fmt) {
  if ($v === null) return null;
  if ($v instanceof \craft\elements\db\ElementQuery) { return array_map(fn($e)=>'#'.$e->id.' '.($e->title ?? ''), $v->status(null)->all()); }
  if ($v instanceof \Illuminate\Support\Collection) return $v->map(fn($e)=>is_object($e)&&isset($e->id)?'#'.$e->id.' '.($e->title??''):$e)->all();
  if ($v instanceof \DateTime) return $v->format('Y-m-d');
  if ($v instanceof \craft\fields\data\SingleOptionFieldData) return $v->value;
  if (is_object($v) && method_exists($v,'__toString')) return (string)$v;
  if (is_array($v)) return array_map($fmt,$v);
  if (is_object($v)) return get_class($v);
  return $v;
};
$dump = function($e) use ($fmt) {
  $o = ['id'=>$e->id,'section'=>$e->section->handle,'title'=>$e->title,'status'=>$e->status,'uri'=>$e->uri];
  foreach ($e->getFieldLayout()->getCustomFields() as $f) { $v = $fmt($e->getFieldValue($f->handle)); if ($v!==null && $v!=='' && $v!==[]) $o[$f->handle]=$v; }
  return $o;
};
foreach ([15929, 23091] as $pid) {
  $p = \craft\elements\Entry::find()->id($pid)->status(null)->one();
  echo "PERSON ", json_encode($dump($p), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), "\n";
  foreach (\craft\elements\Entry::find()->section(['officeHoldings','candidacies','elections'])->status(null)->relatedTo($p)->limit(null)->all() as $e)
    echo json_encode($dump($e), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), "\n";
}
echo "SECTIONS\n"; foreach (\Craft::$app->entries->getAllSections() as $s) echo $s->handle," ";
