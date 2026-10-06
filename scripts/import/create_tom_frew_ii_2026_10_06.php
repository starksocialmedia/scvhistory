/**
 * Tom Frew II (Nathan, 6 October 2026: "yes, a record. Three men of one name across generations, two with records, is exactly
 * where the third is needed to keep them apart"). From inventory/review/tom-frew-2026-10-05.md.
 * Sources: SCVHistory.com TF1000 and AP0724 (the same paragraph on both: "Thomas McNaughton Frew II (April 2, 1852 - Aug. 11,
 * 1928), a native of Ardria, Scotland ... arrived in Newhall in 1900 with his blacksmithing tools ... His shop, which he
 * purchased for the lofty sum of $400"; "Back in 1913, Tom II purchased a 23-acre parcel of land adjacent to actor William S.
 * Hart's property in downtown Newhall. Today, a portion of the Frew land is known as 'Heritage Junction'"), and A.B. Perkins,
 * "History of Downtown Newhall" (archive record #1438): "T.M. Frew had taken the blacksmith shop over from Sam Smith in 1900".
 * - The person record, with the portrait TF1000 (captioned on its page "Thomas M. Frew II, Blacksmith"), copied from the mirror.
 * - "Not to be confused with" naming his son Thomas M. Frew Jr. (#28647) and grandson Tom Frew IV (#18783).
 * - "71. Requiem" (#2167), which is about him, takes him as its subject.
 * "Ardria" is as the pages print it. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_tom_frew_ii_2026_10_06.php'))"
 */
use craft\elements\{Entry, Asset};
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;
$FILE = "$root/inventory/incoming/tf1000.jpg"; $req = Entry::find()->id(2167)->status(null)->one();
if (!is_file($FILE) || $req?->title !== '71. Requiem' || !Entry::find()->id(28647)->status(null)->exists() || !Entry::find()->id(18783)->status(null)->exists()) { throw new \RuntimeException('file or records not as expected'); }
$TF = 'SCVHistory.com, "Thomas M. Frew II, Newhall Blacksmith," TF1000, /scvhistory/tf1000.htm (the same text is on AP0724, /scvhistory/ap0724.htm)';
$BODY = 'Thomas McNaughton Frew II, a native of Scotland, came to Newhall in 1900 with his blacksmithing tools and took over the blacksmith shop on Spruce Street, now Main Street, from Sam Smith, paying $400 for it.[1][2] The shop stayed in the family for three generations, his son Thomas M. Frew Jr. and his grandson Tom Frew IV after him, and closed in 1970.[1] In 1913 he bought 23 acres beside William S. Hart\'s land in downtown Newhall; part of the Frew land is now Heritage Junction, the home of the Santa Clarita Valley Historical Society.[1] He died on August 11, 1928.[1]';
$FN = [
  $TF . ': "Thomas McNaughton Frew II (April 2, 1852 - Aug. 11, 1928), a native of Ardria, Scotland, came to the United States in either 1881 or 1886 (Census records conflict) and arrived in Newhall in 1900 with his blacksmithing tools"; "His shop, which he purchased for the lofty sum of $400"; "Tom IV later ran the shop, which he ultimately shut in 1970"; "Back in 1913, Tom II purchased a 23-acre parcel of land adjacent to actor William S. Hart\'s property in downtown Newhall. Today, a portion of the Frew land is known as \'Heritage Junction,\' the home of the Santa Clarita Valley Historical Society".',
  'A.B. Perkins, "History of Downtown Newhall," archive record #1438: "T.M. Frew had taken the blacksmith shop over from Sam Smith in 1900, as well as the J.O. Newhall home next door."'];
$NOTE = 'Not to be confused with his son, Thomas M. Frew Jr. (Tom Frew III, 1895 or 1896 to 1963 or 1966), who ran the shop after him, or his grandson, Tom Frew IV (born 1928), who closed it in 1970; each has his own record. In Newhall of the 1910s to 1950s he was "T.M. Frew Sr." and his son "T.M. Frew Jr." (Ruth Newhall, "The Accidental Blacksmiths Of Old Newhall," Old Town Newhall Gazette, December 1996, /scvhistory/frew1296.htm; Newhall Chamber of Commerce roster, 1923).';
$p = Entry::find()->section('persons')->status(null)->title('Tom Frew II')->one();
echo 'Tom Frew II: ' . ($p ? "exists #{$p->id}" : 'create') . "\n  $BODY\n";
if ($APPLY && !$p) {
  $sec = Craft::$app->getEntries()->getSectionByHandle('persons'); $p = new Entry(); $p->sectionId = $sec->id; $p->setTypeId($sec->getEntryTypes()[0]->id); $p->slug = 'tom-frew-ii';
  $p->setFieldValues(['fullName' => 'Tom Frew II', 'personSearchNames' => "Thomas McNaughton Frew II\nThomas M. Frew II", 'personAliases' => 'T.M. Frew Sr.', 'birthDate' => 'April 2, 1852', 'birthDateEdtf' => '1852-04-02', 'birthEvidence' => 'retrospective',
    'deathDate' => 'August 11, 1928', 'deathDateEdtf' => '1928-08-11', 'deathEvidence' => 'retrospective', 'birthplace' => 'Scotland', 'occupation' => 'Blacksmith', 'body' => $BODY, 'bodyAuthorship' => 'editorial-2026',
    'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($FN), $FN),
    'editorNotes' => [['heading' => 'Not to be confused with', 'note' => $NOTE, 'position' => 'bottom']], 'recordProvenance' => 'create_tom_frew_ii_2026_10_06.php, 6 October 2026']);
  if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); } $n++;
}
$a = Asset::find()->filename('tf1000.jpg')->one(); echo 'portrait tf1000.jpg: ' . ($a ? "exists #{$a->id}" : 'import (800 x 1246)') . PHP_EOL;
if ($APPLY && !$a) {
  $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'legacy/']);
  $tmp = sys_get_temp_dir() . '/tf1000.jpg'; copy($FILE, $tmp);
  $a = new Asset(); $a->tempFilePath = $tmp; $a->setFilename('tf1000.jpg'); $a->newFolderId = $folder->id; $a->setVolumeId($vol->id); $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = false;
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); }
  $a = Asset::find()->id($a->id)->one(); $a->title = 'Tom Frew II, portrait'; $a->alt = 'Portrait of Tom Frew II';
  $v = ['provenanceKind' => 'legacy-mirror', 'acquiredDate' => '2026-10-06', 'license' => 'unknown', 'rightsNote' => 'No permission to republish is established.', 'legacySourcePath' => 'gif/tf1000.jpg', 'sourceChecksum' => 'sha256:' . hash_file('sha256', $FILE),
    'source' => 'SCVHistory.com, gif/tf1000.jpg, the photograph on /scvhistory/tf1000.htm, "Thomas M. Frew II, Blacksmith". Copied from the legacy mirror on 6 October 2026.', 'photoCaptionExt' => 'Thomas M. Frew II, Blacksmith. Newhall, California.', 'photoSourceCode' => 'TF1000'];
  $ah = array_map(fn($f) => $f->handle, $a->getFieldLayout()->getCustomFields()); $a->setFieldValues(array_intersect_key($v, array_flip($ah)));
  if (!$el->saveElement($a)) { throw new \RuntimeException(json_encode($a->getFirstErrors())); } $n++;
}
if ($APPLY && $p && $a && !$p->featuredImage->one()) { $p->setFieldValue('featuredImage', [$a->id]); if (!$el->saveElement($p)) { throw new \RuntimeException('portrait'); } $n++; }
$sp = $req->subjectPerson->status(null)->ids(); echo '#2167 "71. Requiem" subject: ' . ($p && in_array($p->id, $sp, true) ? 'linked already' : 'add Tom Frew II') . PHP_EOL;
if ($APPLY && $p && !in_array($p->id, $sp, true)) { $req->setFieldValue('subjectPerson', array_merge($sp, [$p->id])); if (!$el->saveElement($req)) { throw new \RuntimeException('#2167'); } $n++; }
if ($APPLY) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_tom_frew_ii_2026_10_06.php', $n, 'verified', 'Tom Frew II: record, portrait TF1000, Requiem'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
