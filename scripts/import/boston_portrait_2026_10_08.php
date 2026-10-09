/**
 * John Boston's portrait: the photograph that ran beside his column on the original site (Nathan, 8 October 2026: "use the
 * SCVHistory column mug, not The Signal's. It is small, but it is from the site we are migrating and it is the picture that
 * ran beside his column there ... If you can find a capture of the SCVHistory mug at a larger size than 150 by 166, use that.").
 * gif/mugs/boston_john.jpg is not on Reggie. The Internet Archive holds it at 2003, 2005 and 2007, all three byte-identical
 * at 150 by 166 (the 2016 entry is a revisit of the same), and the page shows it at that size; no larger copy exists in the
 * captures. Kept as downloaded in inventory/incoming/done/originals-2026-10-08/boston-john-scvhistory-mug.jpg; below the
 * import rule's long side, so the web copy is the same file.
 * The group-photograph crop (#31938) moves from portrait to related image beside the group photograph (#31937).
 * The Signal's 2018 mug is kept off the record entirely. The Signal's two 1970s photographs are not imported; an editor's
 * note at the bottom says they exist and where (Nathan: "Note on the record that they exist and where").
 * Idempotent. Dry run by default; set $APPLY = true.
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$web = 'boston-john-scvhistory-mug.jpg'; $WEB = "$root/storage/runtime/photo-import/originals-web/$web"; $M = "$root/inventory/incoming/done/originals-2026-10-08/$web";
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the downloaded master, hashed here for its checksum', $M],
  ['file', 'the web copy', $WEB],
  ['record', 'the new asset\'s sourceChecksum, sourceUrl and provenanceKind (written here, not read)', 'the column photograph', 'not read: written from the master hashed above'],
  ['record', 'Boston\'s featuredImage, recordImages and editorNotes; an asset found by filename', 'the stored files', 'not read: the record fields are what is changed; the asset is new (the dry run says so if one exists)'],
]);
$e = Entry::find()->id(2576)->status(null)->one();
if ($e?->title !== 'John Boston') { throw new \RuntimeException('#2576 not as expected'); }
$sum = hash_file('sha256', $M);
if ($sum !== hash_file('sha256', $WEB)) { throw new \RuntimeException('web copy is not the master'); }
$vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $as = Craft::$app->getAssets();
$folder = $as->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
$a = Asset::find()->volumeId($vol->id)->folderId($folder->id)->filename($web)->one();
$feat = $e->featuredImage->status(null)->ids(); $imgs = $e->recordImages->status(null)->ids();
$NOTE = ['heading' => 'Other photographs of John Boston',
  'note' => 'The Signal holds two earlier photographs of him, not reproduced here: one taken in 1975 in The Signal\'s backshop by Marshall LaPlante, and one of him in his high school tux, captioned there as from the mid-1970s, when he was briefly the paper\'s society editor. Both are printed with his own "Signal 100: Chapter 1, The story of John Boston", The Signal, May 2019 (<a href="https://signalscv.com/2019/05/signal-100-chapter-1-the-story-of-john-boston/">signalscv.com</a>).',
  'position' => 'bottom'];
$notes = $e->editorNotes ?: []; $hasNote = (bool)array_filter($notes, fn($r) => ($r['heading'] ?? '') === $NOTE['heading']);
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo "master $web 150x166 sha256:$sum" . PHP_EOL;
echo 'asset: ' . ($a ? "exists #{$a->id}" : 'new, in outside/') . PHP_EOL;
echo 'portrait now ' . json_encode($feat) . ' -> the column photograph; related images now ' . json_encode($imgs) . ' -> add #31938 (the crop)' . PHP_EOL;
echo 'editor\'s note (bottom): ' . ($hasNote ? 'there already' : 'add') . PHP_EOL . '    ' . $NOTE['heading'] . ': ' . strip_tags($NOTE['note']) . PHP_EOL;
if (!$APPLY) { echo 'nothing written' . PHP_EOL; return; }
if (!$a) {
  $tmp = sys_get_temp_dir() . "/$web"; copy($WEB, $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename($web); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id);
  $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
  $a = Asset::find()->id($a->id)->one(); $a->title = 'John Boston, column photograph'; $a->alt = 'Portrait of John Boston';
  $v = ['provenanceKind' => 'outside', 'acquiredDate' => '2026-10-08', 'sourceChecksum' => "sha256:$sum", 'sourceUrl' => 'http://www.scvhistory.com/gif/mugs/boston_john.jpg',
    'source' => 'SCVHistory.com, gif/mugs/boston_john.jpg, the photograph beside the contents of his column on /scvhistory/signal/boston/jbindex.htm, shown there at 150 by 166. Not on the Reggie drive; taken from the Internet Archive\'s capture of 24 October 2007 (web.archive.org/web/20071024025157), byte-identical to its captures of 2003 and 2005; downloaded on 8 October 2026.',
    'photoCredit' => 'SCVHistory.com', 'license' => 'unknown', 'rightsNote' => 'The original site names no photographer.'];
  $h = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($h)));
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++;
}
$e->setFieldValue('featuredImage', [$a->id]);
$e->setFieldValue('recordImages', array_values(array_unique(array_merge($imgs, [31938]))));
if (!$hasNote) { $notes[] = $NOTE; $e->setFieldValue('editorNotes', $notes); }
if (!$el->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); } $n++;
$applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('boston_portrait_2026_10_08.php', $n, 'verified', 'Boston\'s SCVHistory column photograph as portrait, the crop a related image, the Signal photographs noted');
echo "done: $n" . PHP_EOL;
