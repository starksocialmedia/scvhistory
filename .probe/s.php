$rows=[];
foreach (['articles','warMemorials','obituaries'] as $s) {
  foreach (\craft\elements\Entry::find()->section($s)->status(null)->all() as $e) {
    $rows[]=['id'=>$e->id,'slug'=>$e->slug,'section'=>$s,'title'=>(string)$e->title,
             'body'=>(string)$e->body,'pub'=>(string)($e->originalPublishDate ?? ''),
             'writtenBy'=>method_exists($e,'getFieldValue') && $e->getFieldLayout() ? (function($e){ try { return $e->writtenBy->count(); } catch (\Throwable $t) { return -1; } })($e) : -1];
  }
}
file_put_contents('.probe/bodies.json', json_encode($rows));
echo count($rows)." bodies\n";
