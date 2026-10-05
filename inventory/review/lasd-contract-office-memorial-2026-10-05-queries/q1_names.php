// Read-only: find entries whose title or body mentions sheriffs, fallen officers, the contract.
$terms = ['Kuredjian','Pelino','De Moranville','Demoranville','Pilcher','Ed Brown','Harnischfeger','McCoy Pyle','David March','Pavelka','Emma Benson','Newhall Incident','Frago','Alleyn','Pence','Twining','Twinning','Kness',
 'Biscailuz','Pitchess','Sherman Block','Baca','McDonnell','Villanueva','Robert Luna','Rowland','Hammel','Traeger','Cline','John L. Scott','Contract Law Enforcement','contract city','Deputy Jake'];
foreach ($terms as $t) {
  $rows = (new \craft\db\Query())->select(['e.id','es.title','s.handle'])
    ->from(['e'=>'{{%entries}}'])
    ->innerJoin(['es'=>'{{%elements_sites}}'],'es.elementId=e.id')
    ->innerJoin(['el'=>'{{%elements}}'],'el.id=e.id')
    ->leftJoin(['s'=>'{{%sections}}'],'s.id=e.sectionId')
    ->where(['el.dateDeleted'=>null,'el.revisionId'=>null,'el.draftId'=>null])
    ->andWhere(['or',['like','es.title',$t],['like','es.content',$t]])
    ->limit(60)->all();
  echo "== $t: ".count($rows)."\n";
  foreach ($rows as $r) echo "   #{$r['id']} [{$r['handle']}] {$r['title']}\n";
}
