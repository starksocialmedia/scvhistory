/**
 * No generated image on any record, checked by opening the files (Nathan, 9 October 2026: "The no-generated-image check
 * passed yesterday with Cooper's generated portrait live on his record, because it reads fields rather than files. Fifth
 * instance of the pattern this week and the first inside a check built to catch exactly this. Rewrite it to open the
 * file"). It replaces the field test in audit_fourteen_days_2026_10_08.php (IMG-1), which read enhancementMethod,
 * contentCredentials and source and so could not see a file whose record is silent.
 *
 * Every asset's files are opened through _generated_scan.php (master, stored copy, the manifest it points to). Fails when:
 *   1. a generated file is on a live entry or category (a generator named, or trainedAlgorithmicMedia with no ingredient);
 *   2. a file with a text_to_image step is on a live record, unless it is in $HELD below with Nathan's open question;
 *   3. a generatively edited file is on a live record without the enhancement rule's pair (docs/DATA-MODEL.md, 9 October:
 *      enhancedFrom names the original, the original is on the same record, enhancementMethod names the edit), unless it
 *      is in $RULED below with the ruling that keeps it;
 *   4. an asset generative in its file or its record is on no live record and is missing from config/withheld-media.json,
 *      or one listed there is on a live record again, or config/withheld-media.txt lacks its file;
 *   5. a listed asset's /media page answers anything but 404.
 * Run by check_render; returns ['ok' => bool]. Reads only. On its own:
 *   ddev craft exec "eval(file_get_contents('scripts/import/check_generated_files.php'))"
 */
use craft\elements\{Asset, Entry, Category};
$root = \Craft::getAlias('@root');
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'every asset\'s master on Reggie, where legacySourcePath names one', '/mnt/reggie/scvhistory.com'],
  ['file', 'the masters held in the repo, matched by checksum and hashed again before use', "$root/inventory"],
  ['file', 'every asset\'s stored copy', "$root/web/uploads"],
  ['record', 'the manifests the files point to (storage/runtime/manifests, fetched once)', 'each credentialed file', 'read'],
  ['record', 'Craft fields legacySourcePath, sourceChecksum, enhancedFrom, enhancementMethod, enhancedBy, contentCredentials, source, filename', 'each asset\'s files', 'read'],
  ['file', 'the withheld list', "$root/config/withheld-media.json"],
  ['file', 'the rsync exclude list', "$root/config/withheld-media.txt"],
]);
/* Text-prompt steps on a live record, each waiting on Nathan. A new one fails the check. */
$Q = 'the manifest records an Adobe Firefly Image 5 edit whose operation is text_to_image, the same step as Couts\'s (9 October); Nathan\'s ruling on Couts read literally takes it off, and he gave it believing Couts was the only one. Held on the record for his word (TODO, 9 October afternoon)';
$HELD = [
  27387 => 'Cave Johnson Couts: off on 9 October morning, put back the same evening on Nathan\'s word once his manifest was read (it opens an uploaded photograph and the Image 5 edit acts on it); the same open question as the fourteen',
  27381 => $Q,
  27383 => $Q,
  31387 => $Q,
  31391 => $Q,
  31395 => $Q,
  31398 => $Q,
  31404 => $Q,
  31406 => $Q,
  31408 => $Q,
  31423 => $Q,
  31427 => $Q,
  31447 => $Q,
  31449 => $Q,
  31472 => $Q,
];
/* Generative edits on a live record that the pair test does not cover, each with the ruling or open question that keeps it there. */
$M = 'a mark edited in Photoshop with Firefly; DATA-MODEL: generated marks and ornament not yet ruled, and the mark is on its record pending that (branch-to-staging report, blocker list 2)';
$RULED = [
  29696 => $M,
  29439 => $M,
  29400 => $M,
  29298 => $M,
];

$scan = require "$root/scripts/import/_generated_scan.php";
$FIELD = '~firefly|text prompt|text.to.image|generat|grok|\bxai\b|upsampl|upscal|trainedAlgorithmic|gpt-image|midjourney|dall.?e|stable diffusion~i';
$W = json_decode((string)@file_get_contents("$root/config/withheld-media.json"), true)['assets'] ?? [];
$wIds = array_column($W, 'id');
$ex = array_flip(array_filter(array_map('trim', @file("$root/config/withheld-media.txt") ?: []), fn($l) => $l !== '' && $l[0] !== '#'));
$bad = []; $held = []; $c = []; $notRead = 0; $onRec = 0; $n = 0;
foreach (Asset::find()->status(null)->batch(200) as $batch) {
  foreach ($batch as $a) {
    $n++;
    $L = $a->getFieldLayout(); $f = fn($h) => ($L && $L->getFieldByHandle($h)) ? trim((string)$a->getFieldValue($h)) : '';
    $users = Entry::find()->relatedTo(['targetElement' => $a])->all();
    $cats = Category::find()->relatedTo(['targetElement' => $a])->ids();
    $used = $users || $cats;
    $saysGen = (bool)preg_match($FIELD, $f('enhancementMethod') . ' ' . $f('contentCredentials') . ' ' . $f('source'));
    $r = $scan($a);
    $c[$r['class']] = ($c[$r['class']] ?? 0) + 1;
    $lab = "#{$a->id} {$a->filename}";
    if ($used) { $onRec++; if ($r['class'] === 'not-read') { $notRead++; } }
    $marked = in_array($r['class'], ['generated', 'text-prompt-step', 'generative-edit'], true);
    if ($used && $r['class'] === 'generated') { $bad[] = "$lab is GENERATED (" . implode(', ', $r['why']) . ') and is on ' . implode(', ', array_map(fn($e) => "#{$e->id}", $users)); }
    elseif ($used && $r['class'] === 'text-prompt-step') {
      if (isset($HELD[$a->id])) { $held[] = "$lab: {$HELD[$a->id]}"; }
      else { $bad[] = "$lab has a text_to_image step and is on " . implode(', ', array_map(fn($e) => "#{$e->id} {$e->title}", $users)); }
    } elseif ($used && $r['class'] === 'generative-edit') {
      $from = ($L && $L->getFieldByHandle('enhancedFrom')) ? $a->getFieldValue('enhancedFrom')->status(null)->ids() : [];
      $pairOk = $from && $f('enhancementMethod') !== '' && $users;
      foreach ($users as $e) {
        $ids = [];
        foreach (['featuredImage', 'recordImages'] as $h) { if ($e->getFieldLayout()?->getFieldByHandle($h)) { $ids = array_merge($ids, $e->getFieldValue($h)->status(null)->ids()); } }
        if (in_array($a->id, $ids) && !in_array($from[0] ?? 0, $ids)) { $pairOk = false; }
      }
      if (!$pairOk) {
        if (isset($RULED[$a->id])) { $held[] = "$lab: {$RULED[$a->id]}"; }
        else { $bad[] = "$lab is generatively edited and is on " . implode(', ', array_map(fn($e) => "#{$e->id}", $users)) . ($cats ? ' and categories' : '') . ' without the enhancement pair (original linked and on the same record, edit named)'; }
      }
    }
    if (!$used && ($marked || $saysGen)) {
      if (!in_array($a->id, $wIds)) { $bad[] = "$lab is " . ($marked ? $r['class'] . ' in its file' : 'generative by its record') . ', on no record, and NOT in config/withheld-media.json: /media serves it (run build_withheld_media.php)'; }
      elseif (!isset($ex['/' . $a->getPath()])) { $bad[] = "$lab is withheld but config/withheld-media.txt lacks its file: the rsync would ship it"; }
    }
    if ($used && in_array($a->id, $wIds)) { $bad[] = "$lab is in config/withheld-media.json but is on a live record: its /media page 404s; rebuild the list"; }
  }
}
/* 5. The pages themselves. */
$base = rtrim((string)Craft::$app->getSites()->getPrimarySite()->getBaseUrl(), '/');
$served = 0;
foreach ($wIds as $id) {
  $ch = curl_init("$base/media/$id");
  curl_setopt_array($ch, [CURLOPT_NOBODY => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
  curl_exec($ch); $st = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch);
  if ($st !== 404) { $bad[] = "/media/$id answers $st, not 404"; } else { $served++; }
}
echo "generated files: $n assets opened (" . implode(', ', array_map(fn($k, $v) => "$v $k", array_keys($c), $c)) . "); $onRec on a live record, $notRead of them with no file readable; "
  . count($wIds) . " withheld, $served of their /media pages answer 404" . ($bad ? '' : '; none fails') . PHP_EOL;
foreach ($held as $h) { echo "  held on a record by a ruling: $h" . PHP_EOL; }
foreach ($bad as $b) { echo "  GENERATED FILES FAIL: $b" . PHP_EOL; }
return ['ok' => !$bad];
