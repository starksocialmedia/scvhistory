/**
 * The list of images the site will not serve at /media/<id> (Nathan, 9 October 2026: "Fix /media today. Thirty-four
 * generated and Firefly files publicly fetchable while off every record is the worst open item, and it blocks staging").
 *
 * Every asset's files are opened by _generated_scan.php: the master as it arrived (Reggie, or a held file matched by its
 * checksum), the stored copy, and any manifest the file points to. An asset is withheld when no live entry or category uses
 * it and either its files carry a generative marker (generated, a text-prompt step, or a generative edit) or its own record
 * says it was made or edited with a generative tool. A withheld asset's /media page answers 404
 * (templates/media/_entry.twig, through config/custom.php), and its file is left out of the staging rsync
 * (config/withheld-media.txt, docs/DEPLOY-RUNBOOK.md step 7).
 *
 * Writes config/withheld-media.json, config/withheld-media.txt and storage/runtime/generated-scan.json (every asset's
 * result). Nothing in the database is written. check_generated_files.php (run by check_render) opens the files again and
 * fails when the list is out of date.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_withheld_media.php'))"
 */
use craft\elements\{Asset, Entry, Category};
$root = \Craft::getAlias('@root');
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'every asset\'s master on Reggie, where legacySourcePath names one', '/mnt/reggie/scvhistory.com'],
  ['file', 'every image and PDF under inventory/ and storage/, hashed to find the masters held in the repo', "$root/inventory"],
  ['file', 'every asset\'s stored copy', "$root/web/uploads"],
  ['record', 'the manifests the files point to, fetched once into storage/runtime/manifests', 'each credentialed file', 'read'],
  ['record', 'Craft fields legacySourcePath, sourceChecksum, enhancementMethod, contentCredentials, source, filename on every asset', 'each asset\'s files', 'read'],
  ['record', 'the relations from live entries and categories', 'which assets a page shows', 'read'],
]);
$scan = require "$root/scripts/import/_generated_scan.php";
$FIELD = '~firefly|text prompt|text.to.image|generat|grok|\bxai\b|upsampl|upscal|trainedAlgorithmic|gpt-image|midjourney|dall.?e|stable diffusion~i';
$all = []; $c = []; $n = 0;
foreach (Asset::find()->status(null)->batch(200) as $batch) {
  foreach ($batch as $a) {
    $L = $a->getFieldLayout(); $f = fn($h) => ($L && $L->getFieldByHandle($h)) ? (string)$a->getFieldValue($h) : '';
    $r = $scan($a);
    $text = $f('enhancementMethod') . ' ' . $f('contentCredentials') . ' ' . $f('source');
    $saysGen = (bool)preg_match($FIELD, $text);
    $used = Entry::find()->relatedTo(['targetElement' => $a])->ids();
    $usedCat = Category::find()->relatedTo(['targetElement' => $a])->ids();
    $vol = $a->getVolume();
    $all[] = ['id' => $a->id, 'file' => $a->filename, 'volume' => $vol->handle, 'path' => $a->getPath(), 'class' => $r['class'], 'why' => $r['why'],
      'masterRead' => $r['masterRead'], 'read' => $r['read'], 'unread' => $r['unread'], 'recordSaysGenerative' => $saysGen,
      'usedBy' => array_merge($used, array_map(fn($x) => "category:$x", $usedCat))];
    $c[$r['class']] = ($c[$r['class']] ?? 0) + 1;
    if (++$n % 500 === 0) { echo "... $n assets opened" . PHP_EOL; }
  }
}
$withheld = []; $onRecord = [];
foreach ($all as $x) {
  $marked = in_array($x['class'], ['generated', 'text-prompt-step', 'generative-edit'], true);
  if (!$marked && !$x['recordSaysGenerative']) { continue; }
  if ($x['usedBy']) { $onRecord[] = $x; continue; }
  $withheld[] = ['id' => $x['id'], 'file' => $x['file'], 'volume' => $x['volume'], 'path' => $x['path'],
    'why' => $marked ? "the file: {$x['class']} (" . implode(', ', $x['why']) . ')' : 'its record names a generative tool; no marker in the files read'];
}
usort($withheld, fn($p, $q) => $p['id'] <=> $q['id']);
file_put_contents("$root/config/withheld-media.json", json_encode(['built' => date('Y-m-d H:i'), 'by' => 'scripts/import/build_withheld_media.php', 'assets' => $withheld], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);
$ex = ["# Files the staging rsync leaves out (docs/DEPLOY-RUNBOOK.md step 7). Built by scripts/import/build_withheld_media.php", '# on ' . date('Y-m-d H:i') . ' from config/withheld-media.json; do not edit by hand.'];
$other = [];
foreach ($withheld as $w) { if ($w['volume'] === 'archiveMedia') { $ex[] = '/' . $w['path']; } else { $other[] = $w; } }
file_put_contents("$root/config/withheld-media.txt", implode(PHP_EOL, $ex) . PHP_EOL);
file_put_contents("$root/storage/runtime/generated-scan.json", json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo PHP_EOL . "$n assets: " . json_encode($c) . PHP_EOL;
echo 'master read for ' . count(array_filter($all, fn($x) => $x['masterRead'])) . '; stored copy only for ' . count(array_filter($all, fn($x) => !$x['masterRead'] && $x['read'])) . '; nothing read for ' . count(array_filter($all, fn($x) => !$x['read'])) . PHP_EOL;
echo 'withheld (on no live record, generative in file or record): ' . count($withheld) . PHP_EOL;
foreach ($withheld as $w) { echo "  #{$w['id']} {$w['file']}: {$w['why']}" . PHP_EOL; }
if ($other) { echo 'NOT in the rsync list (another volume): ' . implode(', ', array_map(fn($w) => "#{$w['id']} {$w['volume']}", $other)) . PHP_EOL; }
echo 'generative in file or record and on a live record: ' . count($onRecord) . PHP_EOL;
foreach ($onRecord as $x) { echo "  #{$x['id']} {$x['file']}: {$x['class']}" . ($x['recordSaysGenerative'] ? ', record says generative' : '') . ' on ' . implode(', ', array_slice($x['usedBy'], 0, 4)) . PHP_EOL; }
