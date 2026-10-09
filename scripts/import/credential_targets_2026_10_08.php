/**
 * The overnight credential scan's target list (Nathan, 8 October 2026, overnight run, items 1 to 3): the 2,496 assets made
 * after 4 October whose masters are on Reggie, the person portraits with no edit recorded, and every asset added since
 * 4 October. Lists each asset with its stored file and what its record says about its master and its credentials;
 * scan_credentials_2026_10_08.py opens the files. Writes storage/runtime/overnight/cred-targets.json. Reads only.
 */
use craft\elements\{Entry, Asset};
$root = \Craft::getAlias('@root');
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the unchecked-claims census\'s id lists', "$root/storage/runtime/photo-import/unchecked-claims.json"],
  ['record', 'Craft fields legacySourcePath, sourceChecksum, sourceUrl, provenanceKind, contentCredentials, enhancementMethod, enhancedFrom, filename', 'each asset\'s master and stored file', 'not read: this lists targets; the scan opens the files'],
  ['record', 'person records\' featuredImage', 'the portraits', 'read'],
]);
$U = json_decode(file_get_contents("$root/storage/runtime/photo-import/unchecked-claims.json"), true);
$reggie = $U['3a credentials never scanned (asset made after 4 October), master on Reggie'];
$portraits = [];
foreach (Entry::find()->section('persons')->status(null)->all() as $p) {
  foreach ($p->featuredImage->status(null)->all() as $a) { $portraits[$a->id] = $p->id; }
}
$since = Asset::find()->status(null)->dateCreated('>= 2026-10-04')->ids();
$ids = array_values(array_unique(array_merge($reggie, array_keys($portraits), $since)));
$out = [];
foreach (array_chunk($ids, 500) as $chunk) {
  foreach (Asset::find()->id($chunk)->status(null)->all() as $a) {
    $fs = $a->getVolume()->getFs(); $base = \Craft::parseEnv($fs->path ?? '');
    $em = $a->enhancementMethod ?? ''; $ef = $a->enhancedFrom ? $a->enhancedFrom->status(null)->ids() : [];
    $out[] = ['id' => $a->id, 'file' => $a->filename, 'stored' => rtrim($base, '/') . '/' . $a->getPath(), 'created' => $a->dateCreated->format('Y-m-d'),
      'lsp' => (string)$a->legacySourcePath, 'sum' => (string)$a->sourceChecksum, 'url' => (string)$a->sourceUrl, 'kind' => (string)($a->provenanceKind->value ?? $a->provenanceKind),
      'cc' => (string)$a->contentCredentials, 'em' => is_object($em) ? (string)($em->value ?? '') : (string)$em, 'ef' => $ef,
      'groups' => array_values(array_filter([in_array($a->id, $reggie) ? 'reggie2496' : null, isset($portraits[$a->id]) ? 'portrait' : null, in_array($a->id, $since) ? 'since4oct' : null])),
      'person' => $portraits[$a->id] ?? null];
  }
}
@mkdir("$root/storage/runtime/overnight", 0777, true);
file_put_contents("$root/storage/runtime/overnight/cred-targets.json", json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
$c = []; foreach ($out as $o) { foreach ($o['groups'] as $g) { $c[$g] = ($c[$g] ?? 0) + 1; } }
echo count($out) . ' assets: ' . json_encode($c) . PHP_EOL;
