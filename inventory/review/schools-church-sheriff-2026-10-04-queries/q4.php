// Read-only: archive entries whose legacy key matches pages on the mirror's Newhall Schools and Law Enforcement indexes.
$keys = ['newhallschool','brunner1940newhallschool','brunner1940nsd','brunner1940newhallschoolbus','brunner1940dillcontract','dalbey1933','jstevens','ap0729','tlp_sg062933','ns6001','ap0113','ap0124','hssc1906myers','lp_laherald060490school','tlp_latimes093001','hs0731','ap1705','ap1710','ap1011','ap1708','lw3640','rl0100','rl1401','rl1502','ap0527','gr0220','ku2501','ku2502','tlp_signal082025','tlp_signal091025','lw2638','tlp_signal082025b','tlp_signal052726','ns2801','ku2902a','ns3001','pc3201','pw3301','pc3501','pc3502','pc3601','pc3602','pc3702','pc3701','pc3801','gr0221','tlp_lat021539pg2','tlp_signal021739pg3','gr0222','pc3901a','tr5501','hg5601','mcgrath20170807','nsdmeasuree102814','lw2534','ns1501','nsd_paulcordeiro','sg19290425school','ms0003','sg19941113auditorium','ns9601','lw3118','pr110897','sg19990224auditorium','sg113003b','lw2663','lw2664','lw2961','nsd102617','lw9801','lw030597',
 'sc1806','lw110896','sd0100','sd0110','sd0115','sd0120','sd0120a','lw3347','lw7001','sd1901','obituary_jeremyideconklin','lasdhistory2011','lasdjurisdiction050713','lacountysheriffs','al3025','al3026','sw5201','sw5202','sd0200','lw2533'];
$rows = (new \craft\db\Query())->select(['e.id','es.title','es.content','sec.handle AS section'])
  ->from(['es'=>'{{%elements_sites}}'])->innerJoin(['e'=>'{{%elements}}'],'e.id=es.elementId')
  ->innerJoin(['en'=>'{{%entries}}'],'en.id=e.id')->innerJoin(['sec'=>'{{%sections}}'],'sec.id=en.sectionId')
  ->where(['e.dateDeleted'=>null,'e.draftId'=>null,'e.revisionId'=>null])->all();
$found = [];
foreach ($rows as $r) {
  $c = json_decode($r['content'] ?? '{}', true) ?: [];
  $flat = json_encode($c);
  foreach ($keys as $k) {
    if (preg_match('~(?:"|/)' . preg_quote($k,'~') . '(?:\.html?|")~i', $flat)) { $found[$k][] = "#{$r['id']} [{$r['section']}] {$r['title']}"; }
  }
}
foreach ($keys as $k) echo str_pad($k, 28), isset($found[$k]) ? implode(' ; ', array_unique($found[$k])) : '-- not in archive', "\n";
