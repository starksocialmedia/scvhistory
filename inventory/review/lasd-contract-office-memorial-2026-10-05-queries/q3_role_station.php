foreach ([18441,29682] as $id) { $e=\craft\elements\Entry::find()->id($id)->status(null)->one();
 echo "== #$id {$e->title} [{$e->section->handle}/{$e->type->handle}]\n";
 foreach ($e->getFieldLayout()->getCustomFields() as $f) { $v=$e->getFieldValue($f->handle);
   if ($v instanceof \craft\elements\db\ElementQuery) { $ids=$v->status(null)->ids(); if($ids) echo "  {$f->handle}: ".implode(',',$ids)."\n"; }
   elseif (is_string($v)||is_numeric($v)) { if(trim((string)$v)!=='') echo "  {$f->handle}: ".mb_substr(strip_tags((string)$v),0,600)."\n"; }
   elseif (is_object($v) && method_exists($v,'__toString')) { $s=trim(strip_tags((string)$v)); if($s!=='') echo "  {$f->handle}: ".mb_substr($s,0,900)."\n"; } }
 $rel=(new \craft\db\Query())->select(['sourceId','fieldId'])->from('{{%relations}}')->where(['targetId'=>$id])->all();
 echo "  related-from: ".count($rel)." ".implode(',',array_slice(array_column($rel,'sourceId'),0,40))."\n"; }
