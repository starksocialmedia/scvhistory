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
foreach ([15958, 21590, 21783] as $id) { $e = \craft\elements\Entry::find()->id($id)->status(null)->one(); echo json_encode($dump($e), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), "\n"; }
echo "== organizations matching sheriff/LASD/station/baptist/church titles\n";
foreach (\craft\elements\Entry::find()->section('organizations')->status(null)->limit(null)->all() as $e) {
  if (preg_match('/sheriff|lasd|station|baptist|christian|church/i', $e->title)) echo "  #{$e->id} {$e->title} [", $fmt($e->getFieldValue('orgType')) ?? '', "] ", $e->status, "\n";
}
echo "== organizations of orgType school (all)\n";
$names = ['McGrath','Meadows','Newhall Elementary','Oak Hills','Old Orchard','Peachland','Pico Canyon','Stevenson Ranch','Valencia Valley','Wiley Canyon'];
foreach (\craft\elements\Entry::find()->section('organizations')->status(null)->limit(null)->all() as $e) {
  $t = $fmt($e->getFieldLayout()->getFieldByHandle('orgType') ? $e->getFieldValue('orgType') : null);
  $ts = is_array($t) ? implode(',', array_map(fn($x)=>is_string($x)?$x:json_encode($x),$t)) : (string)$t;
  $hit = false; foreach ($names as $n) if (stripos($e->title, $n) !== false) $hit = true;
  if (stripos($ts,'school') !== false && $hit) echo "  #{$e->id} {$e->title} [$ts] {$e->status}\n";
  elseif ($hit) echo "  (not school type) #{$e->id} {$e->title} [$ts] {$e->status}\n";
}
