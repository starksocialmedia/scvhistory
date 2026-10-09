/**
 * Targets for the publisher-edit census (Nathan, 9 October 2026: "tell me how many of the archive's images are in that
 * position, publisher-edited rather than ours, as far as the credentials can tell"). Every image asset, with where its
 * master is and what its record says about edits; publisher_edits_census_2026_10_09.py opens the files.
 * Writes storage/runtime/overnight/pub-targets.json. Reads only.
 */
use craft\elements\Asset;
$root = \Craft::getAlias('@root');
$reads = require "$root/scripts/import/_reads.php";
$reads([['record', 'Craft fields legacySourcePath, sourceChecksum, sourceUrl, provenanceKind, enhancementMethod, enhancedBy, contentCredentials, filename, and whether any record uses the asset (enhancedFrom links left out of that count)', 'each asset\'s master', 'not read: this lists targets; the census opens the files']]);
$db = Craft::$app->getDb(); $used = array_flip($db->createCommand("select distinct r.targetId from scvh_relations r join scvh_elements e on e.id=r.sourceId join scvh_fields f on f.id=r.fieldId where e.revisionId is null and e.draftId is null and e.dateDeleted is null and f.handle<>'enhancedFrom'")->queryColumn());
$out = [];
foreach (Asset::find()->kind('image')->status(null)->batch(500) as $batch) {
  foreach ($batch as $a) {
    $base = \Craft::parseEnv($a->getVolume()->getFs()->path ?? '');
    $k = $a->provenanceKind; $em = $a->enhancementMethod;
    $out[] = ['id' => $a->id, 'file' => $a->filename, 'stored' => rtrim($base, '/') . '/' . $a->getPath(), 'kind' => (string)($k->value ?? $k),
      'lsp' => (string)$a->legacySourcePath, 'sum' => (string)$a->sourceChecksum, 'url' => (string)$a->sourceUrl,
      'em' => (string)(is_object($em) ? ($em->value ?? '') : $em), 'by' => (string)$a->enhancedBy, 'cc' => (string)$a->contentCredentials, 'used' => isset($used[$a->id])];
  }
}
@mkdir("$root/storage/runtime/overnight", 0777, true);
file_put_contents("$root/storage/runtime/overnight/pub-targets.json", json_encode($out, JSON_UNESCAPED_SLASHES));
$c = []; foreach ($out as $o) { $c[$o['kind'] ?: 'none'] = ($c[$o['kind'] ?: 'none'] ?? 0) + 1; }
echo count($out) . ' image assets: ' . json_encode($c) . '; on a record: ' . count(array_filter($out, fn($o) => $o['used'])) . PHP_EOL;
