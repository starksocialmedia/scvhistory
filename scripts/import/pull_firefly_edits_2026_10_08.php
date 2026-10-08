/**
 * Firefly-edited portraits off their records (Nathan, 8 October 2026): "Any portrait where Firefly filled, removed, cleaned,
 * or made an edit the credential does not describe comes off the record ... A credential that will not say what changed
 * cannot support a claim that the face is the person's"; "The 14 with no original held: those come off too ... A record
 * with no portrait is honest"; "Where an unedited original exists, restore it as the portrait"; López stays.
 * The 41 held (inventory/review/generated-images-2026-10-08.md, section 4; the side-by-side sheet): 13 with fill, removal or
 * cleaning (Fremont among them), 18 undescribed Firefly Image 5 edits, and 7 enlargements with no original held come off.
 * Six of these were legacy files replaced in place; restore_legacy_files_2026_10_08.php gave them back their own files, so
 * they are not in this list. 32 assets here. Three enlargements stay: Lopez, Randy Wicks, Bob Kellar.
 * Same mechanics as pull_generated_portraits_2026_10_08.php: off every field, the unedited original made the portrait
 * wherever the archive holds it (on the record or not: Scofield's and Perkins's were not), enhancedFrom cleared and the original named in the asset's source. Dry run by default; set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/pull_firefly_edits_2026_10_08.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$IDS = [31472, 31449, 31447, 31427, 31423, 31410, 31408, 31406, 31404, 31398, 31395, 31391, 31387, 29330, 29326, 29124, 28978, 28976, 28974, 28972, 28814, 27402, 27400, 27398, 27396, 27394, 27391, 27389, 27387, 27385, 27383, 27381];
// Originals matched by eye on the side-by-side sheet, held on the record without an enhancedFrom link.
$ORIG = [27396 => 23, 27387 => 12, 28814 => 10];
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL; $el = Craft::$app->getElements(); $n = 0;
foreach ($IDS as $aid) {
  $a = Asset::find()->id($aid)->one(); if (!$a) { echo "#$aid not found" . PHP_EOL; continue; }
  $orig = $a->getFieldLayout()->getFieldByHandle('enhancedFrom') ? $a->enhancedFrom->one() : null;
  if (!$orig && isset($ORIG[$aid])) { $orig = Asset::find()->id($ORIG[$aid])->one(); }
  foreach (Entry::find()->relatedTo(['targetElement' => $a])->status(null)->all() as $e) {
    $msg = []; $h = [];
    foreach ($e->getFieldLayout()->getCustomFields() as $f) {
      if (!($f instanceof craft\fields\Assets)) continue;
      $ids = $e->getFieldValue($f->handle)->status(null)->ids();
      if (in_array($aid, $ids)) { $h[$f->handle] = array_values(array_diff($ids, [$aid])); $msg[] = "off {$f->handle}"; }
    }
    $feat = $h['featuredImage'] ?? $e->featuredImage->status(null)->ids();
    $recImgs = $h['recordImages'] ?? ($e->getFieldLayout()->getFieldByHandle('recordImages') ? $e->recordImages->status(null)->ids() : []);
    if (!$feat && $orig) { $h['featuredImage'] = [$orig->id]; $msg[] = "original #{$orig->id} {$orig->filename} becomes the portrait"; }
    elseif (!$feat) { $msg[] = 'no portrait left'; }
    echo "asset #$aid {$a->filename} on #{$e->id} {$e->title}: " . implode('; ', $msg) . PHP_EOL;
    if (!$APPLY) continue;
    foreach ($h as $k => $v) $e->setFieldValue($k, $v);
    if (!$el->saveElement($e)) { throw new \RuntimeException("#{$e->id} " . json_encode($e->getFirstErrors())); }
    $n++;
  }
  // The original's page offers its enhanced copy through the reverse of enhancedFrom (the lightbox's data-enhanced), so the
  // link is cleared too, and the original it was made from is written into the asset's own source text instead.
  $note = ' Taken off every record on 8 October 2026: a Firefly fill, removal, cleaning or undescribed model edit, or an edit with no original held (Nathan: the archive does not publish what it cannot check).' . ($orig ? " Made from asset #{$orig->id}, {$orig->filename}; its enhancedFrom link was cleared so the original's page no longer offers it." : '');
  $src = (string)($a->getFieldLayout()->getFieldByHandle('source') ? $a->getFieldValue('source') : '');
  if (!str_contains($src, 'Taken off every record on 8 October 2026')) {
    echo "asset #$aid: " . ($orig ? "enhancedFrom #{$orig->id} cleared; " : '') . 'note added to source' . PHP_EOL;
    if ($APPLY) { if ($orig) $a->setFieldValue('enhancedFrom', []); if ($a->getFieldLayout()->getFieldByHandle('source')) $a->setFieldValue('source', trim($src . $note)); if (!$el->saveElement($a)) { throw new \RuntimeException("asset #$aid " . json_encode($a->getFirstErrors())); } }
  }
}
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('pull_firefly_edits_2026_10_08.php', $n, 'verified', "$n records: Firefly edits taken off"); }
