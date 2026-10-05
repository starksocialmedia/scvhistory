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
  $o = ['id'=>$e->id,'section'=>$e->section->handle,'title'=>$e->title,'status'=>$e->status];
  foreach ($e->getFieldLayout()->getCustomFields() as $f) { $v = $fmt($e->getFieldValue($f->handle)); if ($v!==null && $v!=='' && $v!==[]) $o[$f->handle]=$v; }
  return $o;
};
$e = \craft\elements\Entry::find()->id(5485)->status(null)->one();
$d = $dump($e); foreach ($d as $k=>$v) echo $k, ': ', mb_substr(json_encode($v, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),0,200), "\n";
