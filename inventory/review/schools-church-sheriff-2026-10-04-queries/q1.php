// Read-only: entries whose title or content mentions each subject term.
$terms = [
 'Santa Clarita Christian', 'SCCS', 'Santa Clarita Baptist', 'Temple Baptist', 'First Baptist Church of Saugus', 'Saugus First Baptist', 'Canyon Country Christian', 'Luther Dr',
 'Newhall Elementary', 'Newhall School', 'Newhall Grammar',
 'Sheriff\'s Station', 'Sheriff Station', 'Sheriffs Station', 'Substation No. 6', 'Golden Valley Road', '23740 Magic Mountain', 'Barclay', 'Justin Diez',
];
$db = \Craft::$app->db;
foreach ($terms as $t) {
  $like = '%' . str_replace(['%','_'], ['\%','\_'], $t) . '%';
  $rows = (new \craft\db\Query())
    ->select(['e.id','e.type','es.title','es.slug','e.enabled','sec.handle AS section','grp.handle AS catgroup'])
    ->from(['es' => '{{%elements_sites}}'])
    ->innerJoin(['e' => '{{%elements}}'], 'e.id = es.elementId')
    ->leftJoin(['en' => '{{%entries}}'], 'en.id = e.id')
    ->leftJoin(['sec' => '{{%sections}}'], 'sec.id = en.sectionId')
    ->leftJoin(['c' => '{{%categories}}'], 'c.id = e.id')
    ->leftJoin(['grp' => '{{%categorygroups}}'], 'grp.id = c.groupId')
    ->where(['e.dateDeleted' => null, 'e.draftId' => null, 'e.revisionId' => null])
    ->andWhere(['or', ['like', 'es.title', $t], ['like', 'es.content', $t]])
    ->andWhere(['not', ['e.type' => ['craft\\elements\\MatrixBlock']]])
    ->limit(400)->all();
  echo "== '$t': ", count($rows), "\n";
  foreach ($rows as $r) {
    if ($r['type'] === 'craft\\elements\\Entry' && !$r['section']) { $owner = (new \craft\db\Query())->select(['primaryOwnerId'])->from('{{%entries}}')->where(['id'=>$r['id']])->scalar(); $r['section'] = 'nested(owner#'.$owner.')'; }
    echo "  #{$r['id']} [", ($r['section'] ?: $r['catgroup'] ?: basename(str_replace('\\','/',$r['type']))), "] ", $r['title'], " /", $r['slug'], ($r['enabled'] ? '' : ' (disabled)'), "\n";
  }
}
