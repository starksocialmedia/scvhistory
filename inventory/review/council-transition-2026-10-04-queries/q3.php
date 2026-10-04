$out=[];
foreach (\craft\elements\Entry::find()->section(['officeHoldings','candidacies','elections'])->status(null)->limit(null)->each() as $e) {
  $fl=$e->getFieldLayout(); $h=[]; foreach($fl->getCustomFields() as $f) $h[$f->handle]=1;
  $txt=''; $fn=[];
  if(isset($h['footnotes'])) foreach(($e->getFieldValue('footnotes')?:[]) as $r){ $n=$r['note']??($r['col2']??''); $fn[]=$n; }
  $prov = isset($h['recordProvenance']) ? (string)$e->getFieldValue('recordProvenance') : '';
  $notes=''; if(isset($h['editorNotes'])) foreach(($e->getFieldValue('editorNotes')?:[]) as $r) $notes.=' '.($r['note']??'');
  $all=implode("\n",$fn)."\n".$prov."\n".$notes;
  if(!preg_match('/cancel|in lieu|insufficien/i',$all)) continue;
  $g=fn($k)=>isset($h[$k])?$e->getFieldValue($k):null;
  $rel=fn($k)=>isset($h[$k])?implode('; ',array_map(fn($x)=>'#'.$x->id.' '.$x->title,$e->getFieldValue($k)->status(null)->all())):null;
  $o=['id'=>$e->id,'section'=>$e->section->handle,'status'=>$e->status,'title'=>$e->title,
   'person'=>$rel('holdingPerson'),'body'=>$rel('holdingBody'),'office'=>$rel('holdingOffice'),'seatLabel'=>(string)$g('seatLabel'),
   'termStart'=>(string)($g('termStartEdtf')?:''),'termEnd'=>(string)($g('termEndEdtf')?:''),'selectionMethod'=>(string)$g('selectionMethod'),'howEnded'=>(string)$g('howEnded'),
   'startEvidence'=>(string)$g('startEvidence'),
   'provenance'=>$prov,'hits'=>array_values(array_filter($fn,fn($x)=>preg_match('/cancel|in lieu|insufficien/i',$x))),'noteHit'=>preg_match('/cancel|in lieu|insufficien/i',$notes)?trim($notes):null];
  echo json_encode($o,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),"\n";
}
