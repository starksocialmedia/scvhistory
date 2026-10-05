/**
 * Newhall Elementary School's mark: the unedited original held beside the edited file (Nathan, 4 October 2026: "Keep the
 * JPEG as the unedited original. Where we hold both, the archive should hold the version before the edit, with the edited
 * one as the mark ... when an edited image is imported and the original exists, keep both and record which is which").
 * newhall-elementary.jpeg (407 by 491, no content credential) becomes an archive asset in marks/; the edited PNG (#29696,
 * Adobe Firefly, recorded by record_newhall_elementary_edit_2026_10_04.php) stays the school's currentMark and points at
 * it through enhancedFrom, as the Perkins and Scofield portraits do, so the page's JSON-LD describes the original.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/hold_newhall_elementary_original_2026_10_04.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements();
$IN = 'newhall-elementary.jpeg'; $FILE = is_file("$root/inventory/incoming/$IN") ? "$root/inventory/incoming/$IN" : "$root/inventory/incoming/done/$IN";
$FN = 'newhall-elementary-school-logo-original.jpg'; $orig = Asset::find()->filename($FN)->one();
$ed = Asset::find()->filename('newhall-elementary-school-logo.png')->one(); $rec = Entry::find()->id(15958)->status(null)->one();
$bad = [];
if (!is_file($FILE)) { $bad[] = 'file missing'; }
if (!$ed || trim((string)$ed->contentCredentials) === '') { $bad[] = 'the edited mark or its recorded edit is missing'; }
if (!$rec || $rec->currentMark->one()?->id !== $ed?->id) { $bad[] = 'Newhall Elementary\'s mark is not the edited file'; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ": $IN -> $FN " . ($orig ? "#{$orig->id} exists" : 'import') . "; #{$ed?->id} enhancedFrom now: " . ($ed?->enhancedFrom->one()?->id ?? 'none') . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0;
if (!$orig) {
    $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $volume->id, 'path' => 'marks/']);
    $tmp = sys_get_temp_dir() . "/$FN"; copy($FILE, $tmp);
    $orig = new Asset(); $orig->tempFilePath = $tmp; $orig->setFilename($FN); $orig->newFolderId = $folder->id; $orig->setVolumeId($volume->id); $orig->setScenario(Asset::SCENARIO_CREATE); $orig->avoidFilenameConflicts = false;
    if (!$el->saveElement($orig)) { throw new \RuntimeException(json_encode($orig->getFirstErrors())); }
    $orig = Asset::find()->id($orig->id)->one(); $orig->title = 'Logo of Newhall Elementary School, unedited original'; $orig->alt = 'Logo of Newhall Elementary School';
    $v = ['provenanceKind' => 'outside', 'acquiredDate' => '2026-10-04', 'license' => 'identifying-use', 'rightsHolder' => 'Newhall Elementary School',
        'source' => "Newhall Elementary School's logo, the N with \"Eagles\", supplied by Nathan Imhoff on 4 October 2026 as the unedited original of the mark shown on the school's record (asset #{$ed->id}, edited with Adobe Firefly); where it was taken from is not recorded with the file. It carries no content credential.",
        'rightsNote' => $ed->rightsNote, 'sourceChecksum' => 'sha256:' . hash_file('sha256', $FILE)];
    $ah = array_map(fn($f) => $f->handle, $orig->getFieldLayout()->getCustomFields()); $orig->setFieldValues(array_intersect_key($v, array_flip($ah)));
    if (!$el->saveElement($orig)) { throw new \RuntimeException(json_encode($orig->getFirstErrors())); } $n++;
}
if ($ed->enhancedFrom->one()?->id !== $orig->id) {
    $ed->setFieldValue('enhancedFrom', [$orig->id]);
    $ed->setFieldValue('source', "Newhall Elementary School's logo, the N with \"Eagles\", supplied by Nathan Imhoff on 4 October 2026 after editing; where it was taken from is not recorded with the file. The unedited original, a 407 by 491 pixel JPEG with no content credential, is held as asset #{$orig->id}.");
    if (!$el->saveElement($ed)) { throw new \RuntimeException(json_encode($ed->getFirstErrors())); } $n++;
}
$ed = Asset::find()->id($ed->id)->one(); if ($ed->enhancedFrom->one()?->id !== $orig->id) { throw new \RuntimeException('enhancedFrom not read back'); }
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('hold_newhall_elementary_original_2026_10_04.php', $n, 'verified', "Newhall Elementary mark: unedited original held (#{$orig->id}), the edited mark's enhancedFrom");
echo "original #{$orig->id}; #{$ed->id} enhancedFrom #{$orig->id}; done: $n writes" . PHP_EOL;
