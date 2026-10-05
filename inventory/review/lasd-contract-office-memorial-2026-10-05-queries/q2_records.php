$persons = \craft\elements\Entry::find()->section('persons')->status(null)->limit(null)->all();
echo "persons total: ".count($persons)."\n";
foreach ($persons as $p) { $t=$p->title; if (preg_match('/Rowland|Biscailuz|Pitchess|Block|Baca|McDonnell|Villanueva|Luna|Scott|Hammel|Traeger|Cline|Aguirre|Kuredjian|Pelino|Moranville|Pilcher|Brown|Pyle|Harnischfeger|March|Frago|Gore|Pence|Alleyn|Kness|Stewart|Haisch|Enger|Spierer|Pardee/i',$t)) echo "  person #{$p->id} {$t} status=".$p->status."\n"; }
foreach ([29282,394] as $id) { $e=\craft\elements\Entry::find()->id($id)->status(null)->one(); echo "== #$id ".($e?$e->title.' ['.$e->section->handle.'] status='.$e->status:'MISSING')."\n"; if($e){ $b=(string)($e->getFieldLayout()->getFieldByHandle('body') ? $e->body : ''); echo "  body: ".mb_substr(strip_tags($b),0,1500)."\n"; } }
$orgs = \craft\elements\Entry::find()->section('organizations')->status(null)->search('sheriff')->all();
foreach ($orgs as $o) echo "  org #{$o->id} {$o->title} status={$o->status}\n";
$st = \craft\elements\Entry::find()->status(null)->title('*Sheriff*')->limit(50)->all();
foreach ($st as $o) echo "  title-match #{$o->id} [{$o->section->handle}] {$o->title}\n";
