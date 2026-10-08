/**
 * Generated faces off real people's records (Nathan, 8 October 2026: "Take all ten of the text-prompt portraits off their
 * records now ... An invented face on a real person's record is worse than a missing portrait by a wide margin";
 * "Henry Clay Wiley #1658: pull it too, until we know what it is").
 * The ten: every asset whose recorded provenance or content credential says part of it was made from a text prompt
 * (Firefly text to image), found by storage/runtime/photo-import/t2i.php; and #1658, downloaded from firefly.adobe.com with
 * no source recorded. Added the same day: #31255 (sk5003_large.jpg, Earl Schmidt) and #14933 (danhon.jpg, Dan Hon), legacy
 * files replaced in place on 6 October by Firefly outputs whose credentials record text_to_image steps. Each comes off every field of every entry that carries it. Where the asset's enhancedFrom names the
 * unedited original and the record holds it, the original becomes featuredImage (a real photograph, already on the record).
 * The assets are not deleted: they stay in the volume, related to nothing, with a note in their own record of why.
 * Dry run by default; set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/pull_generated_portraits_2026_10_08.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$IDS = [31451, 31445, 31429, 31425, 31414, 31402, 31400, 31393, 31389, 28816, 1658, 31255, 14933];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL; $el = Craft::$app->getElements(); $n = 0;
foreach ($IDS as $aid) {
  $a = Asset::find()->id($aid)->one(); if (!$a) { echo "#$aid not found" . PHP_EOL; continue; }
  $orig = $a->getFieldLayout()->getFieldByHandle('enhancedFrom') ? $a->enhancedFrom->one() : null;
  foreach (Entry::find()->relatedTo(['targetElement' => $a])->status(null)->all() as $e) {
    $msg = []; $h = [];
    foreach ($e->getFieldLayout()->getCustomFields() as $f) {
      if (!($f instanceof craft\fields\Assets)) continue;
      $ids = $e->getFieldValue($f->handle)->status(null)->ids();
      if (in_array($aid, $ids)) { $h[$f->handle] = array_values(array_diff($ids, [$aid])); $msg[] = "off {$f->handle}"; }
    }
    $feat = $h['featuredImage'] ?? $e->featuredImage->status(null)->ids();
    $recImgs = $h['recordImages'] ?? ($e->getFieldLayout()->getFieldByHandle('recordImages') ? $e->recordImages->status(null)->ids() : []);
    if (!$feat && $orig && in_array($orig->id, $recImgs)) { $h['featuredImage'] = [$orig->id]; $msg[] = "original #{$orig->id} {$orig->filename} becomes the portrait"; }
    elseif (!$feat) { $msg[] = 'no portrait left'; }
    echo "asset #$aid {$a->filename} on #{$e->id} {$e->title}: " . implode('; ', $msg) . PHP_EOL;
    if (!$APPLY) continue;
    foreach ($h as $k => $v) $e->setFieldValue($k, $v);
    if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); }
    $n++;
  }
  // The original's page offers its enhanced copy through the reverse of enhancedFrom (the lightbox's data-enhanced), so the
  // link is cleared too, and the original it was made from is written into the asset's own source text instead.
  $note = ' Taken off every record on 8 October 2026: part of the image was generated (Nathan: no generated image of a real person anywhere in the archive).' . ($orig ? " Made from asset #{$orig->id}, {$orig->filename}; its enhancedFrom link was cleared so the original's page no longer offers it." : '');
  $src = (string)($a->getFieldLayout()->getFieldByHandle('source') ? $a->getFieldValue('source') : '');
  if (!str_contains($src, 'Taken off every record on 8 October 2026')) {
    echo "asset #$aid: " . ($orig ? "enhancedFrom #{$orig->id} cleared; " : '') . 'note added to source' . PHP_EOL;
    if ($APPLY) { if ($orig) $a->setFieldValue('enhancedFrom', []); if ($a->getFieldLayout()->getFieldByHandle('source')) $a->setFieldValue('source', trim($src . $note)); if (!$el->saveElement($a)) { throw new \RuntimeException("asset #$aid " . json_encode($a->getFirstErrors())); } }
  }
}
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('pull_generated_portraits_2026_10_08.php', $n, 'verified', "$n records: generated portraits taken off"); }
