/**
 * The Newhall School District's mark replaced by a larger file (Nathan, 4 October 2026: "Newhall-School-District.png
 * is in inventory/incoming, a higher-resolution version of the Newhall School District mark. Replace the existing
 * one, which was the 102-pixel file from their site. Keep the old one as a superseded asset rather than deleting it").
 * The new file (2700 by 2700, transparent) becomes the district's currentMark, supplied to the archive, no source
 * page recorded. The old asset (newhall-school-district-logo.png, 102 by 100) stays in the volume with its own
 * source; its title and source say it was superseded and by what, and it no longer claims the current-mark role.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/replace_newhall_mark_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$D = Entry::find()->section('organizations')->title('Newhall School District')->one(); $old = Asset::find()->filename('newhall-school-district-logo.png')->one();
$FILE = is_file("$root/inventory/incoming/Newhall-School-District.png") ? "$root/inventory/incoming/Newhall-School-District.png" : "$root/inventory/incoming/done/Newhall-School-District.png";
$FN = 'newhall-school-district-logo-2700.png'; $new = Asset::find()->filename($FN)->one();
$bad = []; if (!$D || !$old || $D->currentMark->one()?->id !== ($new?->id ?? $old->id)) { $bad[] = 'the district or its present mark is not as expected'; } if (!is_file($FILE)) { $bad[] = 'file missing'; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ": #{$D?->id} mark now #{$D?->currentMark->one()?->id}; old #{$old?->id} superseded; new $FN " . ($new ? "#{$new->id} exists" : 'import') . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0;
if (!$new) {
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'marks/']);
    $tmp = sys_get_temp_dir() . "/$FN"; copy($FILE, $tmp);
    $new = new Asset(); $new->tempFilePath = $tmp; $new->setFilename($FN); $new->newFolderId = $folder->id; $new->setVolumeId($volume->id); $new->setScenario(Asset::SCENARIO_CREATE); $new->avoidFilenameConflicts = false;
    if (!$el->saveElement($new)) { throw new \RuntimeException(json_encode($new->getFirstErrors())); }
    $new = Asset::find()->id($new->id)->one(); $new->title = 'Logo of the Newhall School District'; $new->alt = 'Logo of the Newhall School District';
    $v = ['assetRole' => 'current-mark', 'provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'license' => 'identifying-use', 'rightsHolder' => 'Newhall School District',
        'source' => 'The Newhall School District\'s logo, a larger version supplied by Nathan Imhoff on 4 October 2026; where it was taken from is not recorded with the file. It replaces the 102-pixel file from the district\'s website (asset #' . $old->id . ').',
        'rightsNote' => $old->rightsNote ?: 'Newhall School District\'s own mark. The archive shows it only to identify the body on its own record, as a reference work does; it implies no endorsement.', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $FILE)];
    $ah = array_map(fn($f) => $f->handle, $new->getFieldLayout()->getCustomFields()); $new->setFieldValues(array_intersect_key($v, array_flip($ah)));
    if (!$el->saveElement($new)) { throw new \RuntimeException(json_encode($new->getFirstErrors())); } $n++;
}
if ($D->currentMark->one()?->id !== $new->id) { $D->setFieldValue('currentMark', [$new->id]); if (!$el->saveElement($D)) { throw new \RuntimeException('district'); } $n++; }
if (!str_contains((string)$old->title, 'superseded')) {
    $old->title = $old->title . ' (superseded 4 October 2026)';
    $old->setFieldValues(['assetRole' => '', 'source' => rtrim((string)$old->source) . ' Superseded on 4 October 2026 as the district\'s current mark by a larger version of the same logo (asset #' . $new->id . '); kept, not deleted.']);
    if (!$el->saveElement($old)) { throw new \RuntimeException('old: ' . json_encode($old->getFirstErrors())); } $n++;
}
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('replace_newhall_mark_2026_10_04.php', $n, 'verified', 'Newhall School District mark replaced by the 2700-pixel file; the 102-pixel asset kept as superseded');
echo "done: $n writes" . PHP_EOL;
