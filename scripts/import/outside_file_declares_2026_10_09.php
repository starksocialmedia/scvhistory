/**
 * What each outside image's own file declares about its making, recorded on the asset (Nathan, 9 October 2026: "Yes to
 * recording what the 115 outside assets declare"; DATA-MODEL, "Edited by its publisher before it reached us": "Where the
 * edit can be seen, in the file's metadata, its credential or the picture itself, the asset says so in rightsNote or
 * source ... The archive discloses what it can tell").
 *
 * For every image asset whose provenanceKind is outside, the master as it arrived is opened (Reggie at legacySourcePath, or
 * the held download whose hash is the recorded checksum, through _generated_scan.php) and read with exiftool: the program
 * that saved it (Software, CreatorTool, the XMP history's software agents), the camera (Make, Model), and whether a content
 * credential is present. One sentence is appended to rightsNote (source is held to 500 characters and many are near it).
 * An asset whose only file is Craft's re-saved copy gets nothing: that copy's metadata says nothing about the publisher's.
 * Skipped: assets whose record already names our own edit or a credential (enhancementMethod or contentCredentials set),
 * since those carry a fuller account; and the five whose files carry a Firefly credential their records do not mention
 * (Strickland, Atkins, Gladbach, Rasmussen, Walters), which are our own downloads on the asset-note list in TODO. Nothing about the picture itself (a composite seen by eye) is written here.
 * Idempotent: the sentence is found by its opening words. Dry run by default; set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/outside_file_declares_2026_10_09.php'))"
 */
use craft\elements\Asset;
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$ET = "perl $root/storage/runtime/tools/et/exiftool";
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'each outside image\'s master on Reggie, where legacySourcePath names one', '/mnt/reggie/scvhistory.com'],
  ['file', 'each outside image\'s held download, matched by its recorded checksum and hashed again', "$root/inventory/incoming/done"],
  ['record', 'Craft fields provenanceKind, legacySourcePath, sourceChecksum, enhancementMethod, contentCredentials, rightsNote, filename', 'each outside image\'s master', 'read'],
]);
$scan = require "$root/scripts/import/_generated_scan.php";
$HEAD = 'What the file as received declares (read 9 October 2026):';
$c = ['written' => 0, 'already' => 0, 'stored copy only' => 0, 'our edit or a credential on the record' => 0, 'credential in the file, record silent: on the asset-note list' => 0, 'not an image' => 0];
$rows = [];
foreach (Asset::find()->status(null)->provenanceKind('outside')->all() as $a) {
  if ($a->kind !== 'image') { $c['not an image']++; continue; }
  if (trim((string)$a->enhancementMethod) !== '' || trim((string)$a->contentCredentials) !== '') { $c['our edit or a credential on the record']++; continue; }
  $r = $scan($a);
  $master = null; foreach ($r['paths'] as $k => $p) { if (str_starts_with($k, 'master')) { $master = $p; break; } }
  if (!$master) { $c['stored copy only']++; $rows[] = "#{$a->id} {$a->filename}: stored copy only, nothing written"; continue; }
  $m = json_decode((string)shell_exec("$ET -j -q -q -Software -CreatorTool -HistorySoftwareAgent -Make -Model " . escapeshellarg($master)), true)[0] ?? [];
  $flat = fn($v) => is_array($v) ? array_map('strval', $v) : ($v === null || $v === '' ? [] : [(string)$v]);
  $progs = array_values(array_unique(array_filter(array_map('trim', array_merge($flat($m['Software'] ?? null), $flat($m['CreatorTool'] ?? null), $flat($m['HistorySoftwareAgent'] ?? null))))));
  $cam = trim(implode(' ', array_merge($flat($m['Make'] ?? null), $flat($m['Model'] ?? null))));
  if (in_array($r['class'], ['generated', 'text-prompt-step', 'generative-edit'], true)) { $c['credential in the file, record silent: on the asset-note list']++; $rows[] = "#{$a->id} {$a->filename}: {$r['class']} in its file and its record silent; on the asset-note list (TODO), nothing written here"; continue; }
  $parts = [];
  $parts[] = $progs ? 'software named, ' . implode('; ', array_slice($progs, 0, 4)) . (count($progs) > 4 ? ' and ' . (count($progs) - 4) . ' more' : '') : 'no software named';
  $parts[] = $cam !== '' ? "camera, $cam" : 'no camera named';
  $parts[] = 'no content credential';
  $line = "$HEAD " . implode('; ', $parts) . '.' . ($progs ? ' Software named in a file says it saved the file, not what it changed.' : '');
  $old = trim((string)$a->rightsNote);
  if (str_contains($old, $HEAD)) { $c['already']++; continue; }
  $rows[] = "#{$a->id} {$a->filename} ({$master}): $line";
  $c['written']++;
  if ($APPLY) {
    $a->setFieldValue('rightsNote', trim($old . ($old !== '' ? "\n" : '') . $line));
    if (!$el->saveElement($a)) { throw new \RuntimeException("#{$a->id} " . json_encode($a->getFirstErrors())); } $n++;
  }
}
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . json_encode($c) . PHP_EOL . implode(PHP_EOL, $rows) . PHP_EOL;
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('outside_file_declares_2026_10_09.php', $n, 'verified', "$n outside images: what the file declares, in rightsNote"); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
