/**
 * Five portraits on records whose files came from firefly.adobe.com while their asset records never said so (found
 * 8 October 2026 by checksum_audit_2026_10_08.py: each recorded checksum matched a file in inventory/incoming/done whose
 * download record names firefly.adobe.com; credentials read with scan_content_credentials.py). Patti Rasmussen (#29122) and
 * Brian Walters (#29118) carry text_to_image steps (Nathan, 8 October: the text-prompt portraits come off now). Audra
 * Strickland (#31465), BJ Atkins (#31443) and Jerry Gladbach (#31417) are Firefly enlargements with no original held
 * (Nathan: "A record with an edited portrait and no original is a claim we cannot support").
 * Same mechanics as pull_firefly_edits_2026_10_08.php. Dry run by default; set $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$IDS = [29122, 29118, 31465, 31443, 31417];
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
if ($APPLY && $n) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('pull_undisclosed_firefly_2026_10_08.php', $n, 'verified', "$n records: undisclosed Firefly portraits taken off"); }
