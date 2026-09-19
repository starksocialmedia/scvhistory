foreach (['about-the-namesakes-of-the-kingsburry-house','first-presbyterian-church'] as $slug) {
    $e = \craft\elements\Entry::find()->slug($slug)->status(null)->one();
    if(!$e) { echo "no $slug\n"; continue; }
    echo "=== $slug ===\n";
    $b=(string)$e->body;
    foreach (explode("\n",$b) as $l) {
        if (preg_match('~^\s*\[[^\]]{2,}\]\s*$~u',$l) || preg_match('~^>\s*\S~u',$l)) echo '   HIT: '.substr(trim($l),0,110)."\n";
    }
}
echo "\n=== place bodies (first line) ===\n";
foreach (\craft\elements\Entry::find()->section('places')->status(null)->all() as $e) {
    echo str_pad($e->slug,40).substr(preg_replace('~\s+~',' ',(string)$e->body),0,90)."\n";
}
echo "\n=== collection bodies (first line) ===\n";
foreach (\craft\elements\Entry::find()->section('collections')->status(null)->all() as $e) {
    echo str_pad($e->slug,28).substr(preg_replace('~\s+~',' ',(string)$e->body),0,80)."\n";
}
