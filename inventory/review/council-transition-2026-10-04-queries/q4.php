$body=\craft\elements\Entry::find()->id(394)->status(null)->one();
$els=\craft\elements\Entry::find()->section('elections')->status(null)->relatedTo($body)->limit(null)->all();
echo count($els)," elections\n";
foreach($els as $e){ $h=[]; foreach($e->getFieldLayout()->getCustomFields() as $f) $h[$f->handle]=1;
  $v=[]; foreach(array_keys($h) as $k){ $x=$e->getFieldValue($k); if($x instanceof \craft\elements\db\ElementQuery) $x=implode('; ',array_map(fn($y)=>'#'.$y->id.' '.$y->title,$x->status(null)->all())); elseif(is_object($x)&&!method_exists($x,'__toString')) continue; $x=(string)(is_array($x)?json_encode($x):$x); if($x!=='') $v[$k]=mb_substr($x,0,300);} 
  echo json_encode(['id'=>$e->id,'title'=>$e->title,'status'=>$e->status]+$v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),"\n";
  $c=\craft\elements\Entry::find()->section('candidacies')->status(null)->relatedTo($e)->limit(null)->all();
  foreach($c as $x) if(preg_match('/Gibbs|Weste|Ayala/',$x->title)) echo "   cand #",$x->id," ",$x->title,"\n";
}
