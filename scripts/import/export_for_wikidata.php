$rows = [['id','kind','title','aliases','birth','death','occupation','community','lat','lng','url']];
$map = [
  'persons' => ['person', ['birthDate','deathDate','occupation']],
  'places' => ['place', ['dateEstablished','','']],
  'organizations' => ['organization', ['dateFounded','','']],
];
foreach ($map as $section => [$kind, $f]) {
    foreach (\craft\elements\Entry::find()->section($section)->status(null)->orderBy('title asc')->all() as $e) {
        $layout = [];
        foreach ($e->getFieldLayout()->getCustomFields() as $c) { $layout[] = $c->handle; }
        $get = function ($h) use ($e, $layout) {
            if (!$h || !in_array($h, $layout, true)) { return ''; }
            try { $v = $e->getFieldValue($h); } catch (\Throwable $x) { return ''; }
            if (is_object($v) && method_exists($v, 'all')) { return implode('; ', array_map(fn($r) => $r->title, $v->all())); }
            return trim((string)$v);
        };
        $alias = $get('personAliases') ?: $get('placeAliases') ?: $get('orgAliases');
        $rows[] = [
            $e->id, $kind, $e->title, str_replace("\n", '; ', $alias),
            $get($f[0]), $get($f[1] ?? ''), $get($f[2] ?? ''),
            $get('neighborhood'),
            $get('placeLat') ?: $get('orgLat'), $get('placeLng') ?: $get('orgLng'),
            $e->getUrl(),
        ];
    }
}
$path = \Craft::getAlias('@root') . '/inventory/for-wikidata.csv';
$fh = fopen($path, 'w');
foreach ($rows as $r) { fputcsv($fh, $r); }
fclose($fh);
echo 'wrote ' . (count($rows) - 1) . ' records to inventory/for-wikidata.csv' . PHP_EOL;
