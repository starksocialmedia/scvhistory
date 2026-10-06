/**
 * The fourth batch of 6 October 2026 (Nathan: "Two enhanced, so the enhanced rule: replace the portrait, keep and link the
 * original"): Harry Carey (lw2178-enhance.jpg, from LW2178, his portrait, #31249) and Francisco "Chico" Lopez
 * (us8502_orig-enhanced.jpg, from US8502, imported this morning, #31220). Credentials in
 * inventory/review/enhanced-portraits-credentials-3-2026-10-06.json. The first batch's notes follow.
 *
 * Enhanced portraits, the standing pattern (Nathan, 6 October 2026: "the enhanced image becomes the portrait, the original stays on
 * the record as a related image, and the two are linked so a reader seeing the enhanced one can reach the original. Record on the
 * enhanced asset that I enhanced it, with the tool and the date. The credential scanner reports, it does not block. Caption the
 * enhanced one so a reader can tell at a glance, with a link to the original").
 * The files are Nathan's, from his "Person Profiles" folder, in inventory/incoming. Each enhanced asset: enhancedFrom = the original,
 * enhancedBy "Nathan Imhoff", enhancedDate 6 October 2026, enhancementMethod in plain words read from its own content credential,
 * contentCredentials the manifest and its steps as scan_content_credentials.py reported them
 * (inventory/review/enhanced-portraits-credentials-3-2026-10-06.json). The person page says under an enhanced portrait that it is
 * enhanced, with a link to the original (templates/persons/_entry.twig).
 * The originals already in the archive (matched by their size; Nathan's copies are re-saved downloads, not byte-identical) are
 * used, not imported again; RN3002 is new and is the mirror's own file (gif/rn3002_large.jpg, checksum equal).
 * Ruth Newhall: RN3002's enhancement becomes her portrait; RN3004 (her portrait until now) and its enhancement stay as related.
 * Tom Mix: the enhancement is the portrait; the original and a version with the newspaper art department's crop marks and
 * handwritten instructions removed are related.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/enhanced_portrait_pairs_4_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0; $bad = [];
$CRED = json_decode(file_get_contents("$root/inventory/review/enhanced-portraits-credentials-3-2026-10-06.json"), true);
$IN = "$root/inventory/incoming";
/* [record, original asset id (or null: import the file named), enhanced file, role: portrait|related, extra caption] */
$P = [
  [15919, 31249, 'lw2178-enhance.jpg', 'portrait', ''],
  [28132, 31220, 'us8502_orig-enhanced.jpg', 'portrait', ''],
];
$method = function ($says) {
  $p = [];
  if (str_contains($says, 'text_to_image')) { $p[] = 'regenerated in part from a text prompt (Firefly text to image)'; }
  if (str_contains($says, 'Generate Fill')) { $p[] = 'with generative fill'; }
  if (str_contains($says, 'Firefly Image 5')) { $p[] = 'edited with the Firefly Image 5 model'; }
  if (str_contains($says, 'creative-upsampler')) { $p[] = 'enlarged with Firefly\'s creative upsampler'; }
  return 'Adobe Firefly: ' . ($p ? implode('; ', $p) : 'edited') . '. Generative steps can add detail the photograph did not record.';
};
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
$importFile = function ($file, $folderPath, $title, $alt, $vals) use ($IN, $vol, $el) {
  $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => $folderPath]) ?? Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
  $tmp = sys_get_temp_dir() . '/' . $file; copy("$IN/$file", $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($file); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
  if (!$el->saveElement($a)) { throw new \RuntimeException("$file " . json_encode($a->getFirstErrors())); }
  $a = Asset::find()->id($a->id)->one(); $a->title = $title; $a->alt = $alt;
  $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($vals, array_flip($ah)));
  if (!$el->saveElement($a)) { throw new \RuntimeException("$file " . json_encode($a->getFirstErrors())); }
  return $a;
};
foreach ($P as [$rid, $orig, $enh, $role, $extra]) {
  $r = Entry::find()->id($rid)->status(null)->one(); if (!$r) { $bad[] = "no record #$rid"; continue; }
  if (!is_file("$IN/$enh") || !isset($CRED[$enh])) { $bad[] = "$enh: file or credential missing"; continue; }
  /* the original */
  if (is_int($orig)) { $o = Asset::find()->id($orig)->one(); if (!$o) { $bad[] = "no asset #$orig"; continue; } }
  else {
    $o = Asset::find()->filename($orig)->one();
    if (!$o && hash_file('sha256', "$IN/$orig") !== hash_file('sha256', '/dev/null') && $APPLY) {
      $o = $importFile($orig, 'legacy/', 'Ruth Newhall, RN3002', 'Ruth Newhall', ['provenanceKind' => 'legacy-mirror', 'acquiredDate' => '2026-10-06', 'license' => 'unknown', 'rightsNote' => 'No permission to republish is established.',
        'legacySourcePath' => 'gif/rn3002_large.jpg', 'sourceChecksum' => 'sha256:' . hash_file('sha256', "$IN/$orig"), 'photoSourceCode' => 'RN3002',
        'source' => 'SCVHistory.com, gif/rn3002_large.jpg, the enlargement linked from /scvhistory/rn3002.htm. Taken from the original site\'s files on 6 October 2026.']); $n++;
    }
  }
  $origLabel = $o ? "#{$o->id} {$o->filename}" : "import $orig";
  /* the enhanced */
  $e = Asset::find()->filename($enh)->one();
  $c = $CRED[$enh]; $date = preg_match('~(\d{4}-\d{2}-\d{2})~', $c['says'], $m) ? $m[1] : '2026-10-06';
  $vals = ['enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => $date, 'enhancementMethod' => $method($c['says']), 'contentCredentials' => $c['manifest'] . ' : ' . $c['says'],
    'photoCaptionExt' => trim('An enhanced version of the original photograph, which the archive holds and links here. ' . $extra), 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-06',
    'license' => 'unknown', 'rightsNote' => 'No permission to republish is established; the rights are the original photograph\'s.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', "$IN/$enh"),
    'source' => 'Enhanced by Nathan Imhoff in Adobe Firefly on ' . (new \DateTime($date))->format('F j, Y') . ', from the original held in the archive, and supplied by him the same day.'];
  echo "#$rid {$r->title}: $enh (" . ($e ? "exists #{$e->id}" : 'import') . ") from $origLabel, as the $role; " . $vals['enhancementMethod'] . PHP_EOL;
  if (!$APPLY) { continue; }
  if (!$o) { throw new \RuntimeException("$enh: no original"); }
  if (!$e) {
    $folderPath = trim(dirname(str_replace('\\', '/', $o->getPath())), './') . '/';
    $e = $importFile($enh, $folderPath === '/' ? 'legacy/' : $folderPath, trim(($o->title ?: $r->title) . ', enhanced'), 'Portrait of ' . $r->title . ', enhanced', $vals + ['enhancedFrom' => [$o->id]]); $n++;
  }
  /* the record */
  $imgs = $r->recordImages->status(null)->ids(); $cur = $r->featuredImage->status(null)->one();
  $want = $imgs;
  if ($role === 'portrait') { foreach ([$o->id, $cur?->id] as $x) { if ($x && $x !== $e->id && !in_array($x, $want, true)) { $want[] = $x; } } }
  else { foreach ([$o->id, $e->id] as $x) { if (!in_array($x, $want, true)) { $want[] = $x; } } }
  $vals2 = []; if ($want !== $imgs) { $vals2['recordImages'] = $want; } if ($role === 'portrait' && $cur?->id !== $e->id) { $vals2['featuredImage'] = [$e->id]; }
  if ($vals2) { $r->setFieldValues($vals2); if (!$el->saveElement($r)) { throw new \RuntimeException("#$rid " . json_encode($r->getFirstErrors())); } $n++; }
}
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('enhanced_portrait_pairs_4_2026_10_06.php', $n, 'verified', 'enhanced portraits with their originals: Harry Carey and Chico Lopez'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
