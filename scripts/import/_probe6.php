/* what do bodies actually look like: sample stub-ish and chrome-ish */
$pat = [
  'breadcrumb' => '~^>\s*\S~mu',
  'footer'     => '~^\s*(RETURN TO TOP|RETURN TO MAIN INDEX|MAIN INDEX|PHOTO CREDITS|BIBLIOGRAPHY|BOOKS FOR SALE)\s*$~imu',
  'copyright'  => '~^\s*(©|\(c\)\s*copyright|copyright\s+\d{4})~imu',
  'series'     => '~^\s*(THE STORY OF OUR VALLEY BY A\.B\. PERKINS|HISTORY OF THE SANTA CLARITA VALLEY BY JERRY REYNOLDS)\s*$~mu',
  'bracketnav' => '~^\s*\[[^\]]{2,}\]\s*$~mu',
  'stubplace'  => '~is a named place in the Santa Clarita Valley historical archive~u',
  'stubseries' => '~is a series in the Santa Clarita Valley historical archive~u',
];
$tally = []; $ex = [];
foreach (Craft::$app->entries->getAllSections() as $s) {
    foreach (\craft\elements\Entry::find()->section($s->handle)->status(null)->all() as $e) {
        $have = array_map(fn($f)=>$f->handle, $e->getFieldLayout()->getCustomFields());
        if (!in_array('body', $have, true)) { $tally[$s->handle.' :: NO BODY FIELD'] = ($tally[$s->handle.' :: NO BODY FIELD']??0)+1; continue; }
        $b = (string)$e->body;
        if (trim($b) === '') { $tally[$s->handle.' :: empty'] = ($tally[$s->handle.' :: empty']??0)+1; continue; }
        $hits = [];
        foreach ($pat as $n=>$p) { if (preg_match($p, $b)) $hits[] = $n; }
        $k = $s->handle.' :: '.($hits ? implode('+',$hits) : 'clean');
        $tally[$k] = ($tally[$k]??0)+1;
        if ($hits && !isset($ex[$k])) $ex[$k] = $e->slug;
    }
}
ksort($tally);
foreach ($tally as $k=>$n) echo str_pad($k, 62).$n.'   '.($ex[$k]??'')."\n";
