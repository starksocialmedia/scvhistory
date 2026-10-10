/**
 * The fourteen-day audit (Nathan, 8 October 2026, item 10): "Every record created or changed in the last fourteen days,
 * checked against the rule set as it now stands. Not a diff of what we did, an audit of whether the current state is
 * right." The rules are those in inventory/review/overnight-2026-10-08/rules-in-force-2026-10-08.md.
 * Read only: nothing is saved, no file outside storage/runtime/overnight-audit is written.
 * The universe: every live entry, asset and category (not a draft, revision or trashed element) whose dateCreated or
 * dateUpdated is on or after 24 September 2026, midnight Pacific (07:00 UTC). Each check says which part of it it covers.
 * Writes storage/runtime/overnight-audit/fourteen-day-audit.json.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_fourteen_days_2026_10_08.php'))"
 */
use craft\elements\{Entry, Asset, Category};
ini_set('memory_limit', '4G');
$root = \Craft::getAlias('@root');
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['record', 'Craft fields enhancementMethod, enhancedFrom, enhancedBy, enhancedDate, contentCredentials, source, sourceChecksum, provenanceKind, license, assetRole and filename on every asset', 'the image files and their content credentials', 'not read: this audit checks what the records say against the rules, except IMG-1 and IMG-2, which open the files (below)'],
  ['file', 'IMG-1 and IMG-2 (9 October): the master on Reggie, the master held in the repo, the stored copy and the manifest of every asset they cover, through _generated_scan.php', '/mnt/reggie/scvhistory.com'],
  ['record', 'Craft entries, their fields and relations, and Craft revisions of them (titles, featuredImage, recordImages, body)', 'the rendered pages', 'not read: no page is fetched here; the relations are what the pages render from'],
  ['file', 'web/banners, the banner folder', '@webroot/banners'],
  ['file', 'templates/_data/banners.json, the banner registry', '@root/templates/_data/banners.json'],
  ['file', 'inventory/source-searches.json, where searches behind no-source notes are recorded', '@root/inventory/source-searches.json'],
  ['file', 'scripts/import, every script as text (for User-Agent headers that carry a person)', '@root/scripts/import'],
]);
$SINCE = '2026-09-24 07:00:00';
$db = Craft::$app->getDb(); $q = fn($s, $p = []) => $db->createCommand($s, $p)->queryAll();
$fid = fn($h) => (int)Craft::$app->getFields()->getFieldByHandle($h)->id;
$R = [];   /* rule id => ['rule' => text, 'applies' => n, 'pass' => n, 'fail' => [rows], 'note' => text] */
$add = function ($id, $rule, $applies, $fails, $note = '', $extra = []) use (&$R) {
  $R[$id] = ['rule' => $rule, 'applies' => $applies, 'pass' => $applies - count($fails), 'fail' => array_values($fails), 'note' => $note] + $extra;
  echo str_pad($id, 8) . " applies $applies, pass " . ($applies - count($fails)) . ', fail ' . count($fails) . " | $rule" . PHP_EOL;
};
$g = function ($el, $h) { $l = $el->getFieldLayout(); if (!$l || !$l->getFieldByHandle($h)) { return null; } return $el->getFieldValue($h); };
$s = function ($el, $h) use ($g) { $v = $g($el, $h); if ($v === null) return ''; if ($v instanceof \DateTime) return $v->format('Y-m-d');
  if (is_object($v) && property_exists($v, 'value')) return (string)$v->value; if (is_object($v) && method_exists($v, '__toString')) return trim((string)$v); return is_scalar($v) ? trim((string)$v) : ''; };
$ids = function ($el, $h) use ($g) { $v = $g($el, $h); if ($v === null) return []; return array_map('intval', (clone $v)->status(null)->ids()); };
$lab = fn($el) => '#' . $el->id . ' ' . ($el instanceof Asset ? $el->filename : ($el->title ?: '(no title)')) . ($el instanceof Entry ? ' [' . $el->section->handle . ($el->enabled ? '' : ', disabled') . ']' : '');

/* The universe */
$win = ['or', ['>=', 'elements.dateCreated', $SINCE], ['>=', 'elements.dateUpdated', $SINCE]];
$E = Entry::find()->status(null)->andWhere($win)->all();
$A = Asset::find()->andWhere($win)->all();
$C = Category::find()->status(null)->andWhere($win)->all();
$Eid = []; foreach ($E as $e) { $Eid[$e->id] = $e; }
$Aid = []; foreach ($A as $a) { $Aid[$a->id] = $a; }
$bySec = []; foreach ($E as $e) { $bySec[$e->section->handle][] = $e; }
echo 'universe: ' . count($E) . ' entries, ' . count($A) . ' assets, ' . count($C) . ' categories changed or created since 24 September 2026' . PHP_EOL;
$R['_universe'] = ['entries' => count($E), 'assets' => count($A), 'categories' => count($C), 'bySection' => array_map('count', $bySec),
  'enabledEntries' => count(array_filter($E, fn($e) => $e->enabled))];

/* Live relations to each asset, from live sources */
$rel = [];
foreach ($q("select r.targetId t, r.sourceId s, f.handle h from {{%relations}} r join {{%fields}} f on f.id = r.fieldId join {{%elements}} e on e.id = r.sourceId
  where e.revisionId is null and e.draftId is null and e.dateDeleted is null") as $r) { $rel[(int)$r['t']][] = [(int)$r['s'], $r['h']]; }
$usedBy = function ($aid) use ($rel) { return array_values(array_filter($rel[$aid] ?? [], fn($x) => $x[1] !== 'enhancedFrom')); };

/* Classify an edit from its recorded method and credential */
$kind = function ($a) use ($s) {
  $m = $s($a, 'enhancementMethod') . ' ' . $s($a, 'contentCredentials');
  $k = [];
  if (preg_match('~text prompt|text to image|text_to_image~i', $m)) $k[] = 'text-prompt';
  if (preg_match('~fill~i', $m)) $k[] = 'fill';
  if (preg_match('~remov|clean~i', $m)) $k[] = 'removal-or-clean';
  if (preg_match('~Image 5|edited with Adobe Firefly \(generative|one Firefly edit|Firefly edit|composite~i', $m)) $k[] = 'model-edit-undescribed';
  if (preg_match('~upsampler|upscal|enlarg~i', $m)) $k[] = 'enlarge';
  if (preg_match('~^\s*Cropped~i', $s($a, 'enhancementMethod')) && !preg_match('~firefly|fill|upscal|remov|clean~i', $m)) $k = ['crop'];
  return $k;
};
$RESTORED17 = [333 => 27381, 323 => 27387, 21584 => 27383, 279 => 27396, 29316 => 31447, 307 => 28814, 15808 => 31427, 18726 => 31395,
  15919 => 31472, 16140 => 31449, 30219 => 31404, 2585 => 31423, 15477 => 31398, 20224 => 31387, 18702 => 31408, 321 => 31406, 16432 => 31391];
$KEPT_ENLARGE = [31474, 38450, 38452]; /* López, and the Wicks and Kellar enlargements as their own assets (8 October, continued) */

/* IMG-1: no generated image of a real person, place or event anywhere (8 October). Rewritten 9 October 2026 to open the
   files (Nathan: "Rewrite it to open the file"): the field test it replaces passed with Bill Cooper's generated portrait on
   his record, because his asset's fields were silent and the file was not (ERRORLOG, 9 October). */
$scan = require "$root/scripts/import/_generated_scan.php";
$gen = []; $genApplies = 0; $scanned = [];
foreach ($A as $a) {
  $u = $usedBy($a->id); if (!$u) continue;
  $genApplies++; $r = $scanned[$a->id] = $scan($a);
  if (in_array($r['class'], ['generated', 'text-prompt-step'], true)) $gen[] = ['id' => $a->id, 'label' => $lab($a), 'why' => $r['class'] . ': ' . implode(', ', $r['why']), 'read' => implode(' | ', $r['read']), 'usedBy' => array_map(fn($x) => '#' . $x[0] . ' ' . $x[1], $u)];
}
$bfiles = is_dir("$root/web/banners") ? array_values(array_filter(scandir("$root/web/banners"), fn($f) => $f[0] !== '.')) : [];
$breg = json_decode((string)@file_get_contents("$root/templates/_data/banners.json"), true) ?: [];
$bentries = array_filter(array_keys($breg), fn($k) => preg_match('~^[a-z]+:\d+$~', $k));
foreach ($bfiles as $f) $gen[] = ['id' => 0, 'label' => "web/banners/$f", 'why' => 'a banner file on the web root', 'usedBy' => []];
foreach ($bentries as $k) $gen[] = ['id' => 0, 'label' => "banners.json $k", 'why' => 'a registry entry', 'usedBy' => []];
$add('IMG-1', 'No generated image of a real person, place or event on any record (DATA-MODEL, 8 October): every asset changed in the window and related to a live record has its files opened (master, stored copy, manifest); none generated, none with a text_to_image step; web/banners and the registry empty', $genApplies + count($bfiles) + count($bentries), $gen,
  'Files, not fields (9 October): _generated_scan.php opens the master on Reggie or held in the repo, the stored copy, and the manifest each points to. Relations as enhancedFrom are not counted as use.');

/* IMG-2: the enhancement rule (DATA-MODEL, Nathan, 9 October), which reconciles the Firefly rule of 8 October: an edited
   image on a person's record is published as a pair with its original held and on the same record, the edit named, and no
   generated element in its file. Replaces the 8 October rule as written, which failed exactly the 17 Nathan restored. */
$f2 = []; $f2app = 0;
foreach ($bySec['persons'] ?? [] as $e) {
  $fi = $ids($e, 'featuredImage'); if (!$fi) continue; $a = Asset::find()->id($fi[0])->one(); if (!$a) continue;
  $r = $scanned[$a->id] ?? ($scanned[$a->id] = $scan($a));
  $k = $kind($a);
  if ((!$k || $k === ['crop']) && $r['class'] === 'none-found') continue;
  $f2app++; $p = [];
  if (in_array($r['class'], ['generated', 'text-prompt-step'], true)) $p[] = 'the file holds a generated element (' . $r['class'] . ')';
  $from = $ids($a, 'enhancedFrom');
  if (!$from) $p[] = 'no original linked';
  elseif (!in_array($from[0], array_merge($ids($e, 'recordImages'), $fi), true)) $p[] = 'original #' . $from[0] . ' not on the record';
  if ($s($a, 'enhancementMethod') === '') $p[] = 'the edit is not named (enhancementMethod empty)';
  if (in_array($a->id, $KEPT_ENLARGE, true) && $p === ['no original linked']) continue;
  if ($p) $f2[] = ['id' => $e->id, 'label' => $lab($e), 'asset' => $a->id . ' ' . $a->filename, 'file' => $r['class'], 'problems' => implode('; ', $p)];
}
$add('IMG-2', 'The enhancement rule (DATA-MODEL, 9 October; reconciles the Firefly rule of 8 October): an edited portrait is published beside its original, the original held and on the same record, the edit named, and no generated element in the file', $f2app, $f2,
  'Applies to person records changed in the window whose portrait is edited by its record or its file. The file is opened (_generated_scan.php). The López, Wicks and Kellar enlargements are kept by the 8 October ruling and pass when their only fault is no original linked.');

/* IMG-3: the enhanced pair (DATA-MODEL, 6 October) on every record whose featuredImage records an edit */
$f3 = []; $f3app = 0; $pairs = [];
foreach ($E as $e) {
  $fi = $ids($e, 'featuredImage'); if (!$fi) continue; $a = Asset::find()->id($fi[0])->one(); if (!$a) continue;
  $k = $kind($a); if (!$k) continue;
  $f3app++;
  $from = $ids($a, 'enhancedFrom'); $ri = $ids($e, 'recordImages'); $p = [];
  if (!$from) $p[] = 'no enhancedFrom (original not linked)';
  elseif (!in_array($from[0], $ri, true)) $p[] = 'original #' . $from[0] . ' not among the related images';
  foreach (['enhancedBy', 'enhancedDate', 'enhancementMethod'] as $h) if ($s($a, $h) === '') $p[] = "$h empty";
  if ($k !== ['crop'] && $s($a, 'contentCredentials') === '') $p[] = 'contentCredentials empty';
  $pairs[] = $e->id;
  if ($p) $f3[] = ['id' => $e->id, 'label' => $lab($e), 'asset' => $a->id . ' ' . $a->filename, 'edits' => implode(', ', $k), 'problems' => implode('; ', $p)];
}
$add('IMG-3', 'The enhanced pair (DATA-MODEL, 6 October; called 5 October elsewhere): an edited portrait or featured image links its original by enhancedFrom, the original is among the record\'s related images, and who, when, how and the credential are recorded', $f3app, $f3,
  'Applies to every entry changed in the window whose featured image records an edit, crops included (crops follow the same keep-both rule of 4 October).');

/* IMG-4: an edited asset records the edit (DATA-MODEL, 1 October; keep both, 4 October) */
$f4 = []; $f4app = 0;
foreach ($A as $a) {
  $em = $s($a, 'enhancementMethod'); $cc = $s($a, 'contentCredentials');
  $edited = $em !== '' || preg_match('~firefly|upsampler|generative|trainedAlgorithmic|composite|text_to_image~i', $cc);
  if (!$edited) continue; $f4app++; $p = [];
  if ($em === '') $p[] = 'credential records an edit but enhancementMethod is empty';
  if ($s($a, 'enhancedBy') === '') $p[] = 'enhancedBy empty';
  if ($s($a, 'enhancedDate') === '') $p[] = 'enhancedDate empty';
  if ($s($a, 'source') === '') $p[] = 'source empty';
  if (!$ids($a, 'enhancedFrom') && !preg_match('~not held|no original|original is not|not in the archive|asset #\d+|original~i', $s($a, 'source') . ' ' . $em)) $p[] = 'no original linked and the record does not say none is held';
  if ($p) $f4[] = ['id' => $a->id, 'label' => $lab($a), 'used' => $usedBy($a->id) ? 'on ' . count($usedBy($a->id)) . ' relation(s)' : 'on no record', 'problems' => implode('; ', $p)];
}
$add('IMG-4', 'An edited image records what was done, who, when and the credential, and links its original or says none is held (DATA-MODEL, 1 and 4 October)', $f4app, $f4,
  'Applies to every asset changed in the window whose record names an edit. Assets on no record are listed too: the rule is on the asset, not its use.');

/* IMG-5: a portrait swap keeps the old portrait as a related image (3 October), and the 8 October pulls take it off every field */
$ff = $fid('featuredImage');
$hist = [];
foreach ($q("select rv.canonicalId c, r.targetId t from {{%relations}} r join {{%elements}} el on el.id = r.sourceId join {{%revisions}} rv on rv.id = el.revisionId
  where r.fieldId = :f and el.dateCreated >= :s", [':f' => $ff, ':s' => $SINCE]) as $r) { $hist[(int)$r['c']][(int)$r['t']] = true; }
foreach ($q("select rv.canonicalId c, r.targetId t from {{%relations}} r join {{%elements}} el on el.id = r.sourceId join {{%revisions}} rv on rv.id = el.revisionId
  where r.fieldId = :f and rv.id in (select max(rv2.id) from {{%revisions}} rv2 join {{%elements}} el2 on el2.revisionId = rv2.id where el2.dateCreated < :s group by rv2.canonicalId)", [':f' => $ff, ':s' => $SINCE]) as $r) { $hist[(int)$r['c']][(int)$r['t']] = true; }
$f5 = []; $f5app = 0; $pulledOk = [];
foreach ($E as $e) {
  if (!isset($hist[$e->id])) continue; $now = $ids($e, 'featuredImage'); $ri = $ids($e, 'recordImages');
  $old = array_diff(array_keys($hist[$e->id]), $now); if (!$old) continue;
  $f5app++;
  foreach ($old as $oid) {
    if (in_array($oid, $ri, true)) continue;
    $a = Asset::find()->id($oid)->one();
    if (!$a) { $f5[] = ['id' => $e->id, 'label' => $lab($e), 'old' => "#$oid (asset gone)", 'class' => 'old portrait deleted']; continue; }
    $src = $s($a, 'source'); $k = $kind($a);
    $pulled = preg_match('~taken off|off every record|off its record~i', $src) || array_intersect($k, ['text-prompt']);
    if ($pulled) { $pulledOk[] = ['id' => $e->id, 'label' => $lab($e), 'old' => $a->id . ' ' . $a->filename, 'edits' => implode(', ', $k)]; continue; }
    $f5[] = ['id' => $e->id, 'label' => $lab($e), 'old' => $a->id . ' ' . $a->filename, 'class' => ($usedBy($a->id) ? 'still on other records' : 'on no record now') . ($k ? '; edits: ' . implode(', ', $k) : '')];
  }
}
$add('IMG-5', 'A replaced portrait stays on the record as a related image (3 October), except an image the 8 October rules took off every field', $f5app, $f5,
  'Applies to entries changed in the window whose featured image differs from one it held at any revision since 24 September (or the last before). Read from Craft revisions.',
  ['exempt' => $pulledOk]);

/* IMG-6: provenance recorded at receipt: a checksum of the file as received (3 October) */
$f6 = []; $f6app = 0;
foreach ($A as $a) {
  if ($a->dateCreated->format('Y-m-d H:i:s') < $SINCE) continue; $f6app++;
  if ($s($a, 'sourceChecksum') === '') $f6[] = ['id' => $a->id, 'label' => $lab($a), 'kind' => $s($a, 'provenanceKind') ?: '(none)', 'used' => $usedBy($a->id) ? 'on a record' : 'on no record'];
}
$add('IMG-6', 'An asset made since 24 September records the SHA-256 of the file as received, in sourceChecksum (DATA-MODEL, Provenance)', $f6app, $f6, 'Applies to assets created in the window, not those only updated.');

/* IMG-7: a supplied or outside file is read for its credential before it enters (ERRORLOG, 8 October) */
$f7 = []; $f7app = 0;
foreach ($A as $a) {
  if ($a->dateCreated->format('Y-m-d H:i:s') < $SINCE) continue;
  $pk = $s($a, 'provenanceKind'); if (!in_array($pk, ['outside', 'commissioned', ''], true)) continue; $f7app++;
  if ($s($a, 'contentCredentials') === '') $f7[] = ['id' => $a->id, 'label' => $lab($a), 'kind' => $pk ?: '(none)', 'used' => $usedBy($a->id) ? 'on a record' : 'on no record'];
}
$add('IMG-7', 'A file from outside or supplied by hand has its content credential read and recorded before it becomes anything (ERRORLOG, 8 October; scan_content_credentials.py)', $f7app, $f7,
  'Partly checkable: an empty contentCredentials can mean the scan found nothing as well as that no scan ran; the scan writes no "none found" note. So a listed asset is unverified, not proved unread.');

/* IMG-8: marks: currentMark is current-mark, identifying-use, never the featured image (3 October) */
$f8 = []; $f8app = 0;
foreach ($bySec['organizations'] ?? [] as $e) {
  $cm = $ids($e, 'currentMark'); if (!$cm) continue; $f8app++; $a = Asset::find()->id($cm[0])->one(); $p = [];
  if ($s($a, 'assetRole') !== 'current-mark') $p[] = 'assetRole ' . ($s($a, 'assetRole') ?: 'empty');
  if ($s($a, 'license') !== 'identifying-use') $p[] = 'licence ' . ($s($a, 'license') ?: 'empty');
  if (in_array($cm[0], $ids($e, 'featuredImage'), true)) $p[] = 'also the featured image';
  if ($kind($a)) $p[] = 'mark carries a Firefly edit (marks not ruled; listed, not failed)';
  $real = array_filter($p, fn($x) => !str_contains($x, 'not ruled'));
  if ($p) $f8[] = ['id' => $e->id, 'label' => $lab($e), 'asset' => $a->id . ' ' . $a->filename, 'problems' => implode('; ', $p), 'counts' => (bool)$real];
}
$f8fail = array_values(array_filter($f8, fn($x) => $x['counts']));
$add('IMG-8', 'A body\'s current mark is role current-mark, licence identifying-use, and never its featured image (DATA-MODEL, 3 October)', $f8app, $f8fail, 'Marks edited with Firefly are listed separately: not ruled.', ['firefly_marks' => array_values(array_filter($f8, fn($x) => !$x['counts']))]);

/* IMG-9: licence on images from outside used on records (an empty licence is not permission) */
$f9 = []; $f9app = 0;
foreach ($A as $a) {
  if ($s($a, 'provenanceKind') !== 'outside' || !$usedBy($a->id)) continue; $f9app++;
  $l = $s($a, 'license'); if ($l === '' || $l === 'unknown') $f9[] = ['id' => $a->id, 'label' => $lab($a), 'license' => $l ?: 'empty', 'rightsHolder' => $s($a, 'rightsHolder') ?: 'empty'];
}
$add('IMG-9', 'An image from outside shown on a record has a licence recorded ("an empty licence is not permission", DATA-MODEL, Provenance and rights)', $f9app, $f9,
  'Not a settled failure: the rule says an empty licence is not permission; it does not say such a file may not be shown. Listed for Nathan.');

/* AUTH: the authorship basis (7 October) */
$fa = []; $fb = []; $fc = []; $fd = []; $aapp = 0; $bapp = 0; $capp = 0;
foreach ($E as $e) {
  if (!in_array($e->section->handle, ['articles', 'documents', 'obituaries', 'collections'], true)) continue;
  $wb = $ids($e, 'writtenBy'); $basis = $s($e, 'authorshipBasis'); $note = $s($e, 'authorshipBasisNote');
  if ($wb) { $aapp++; if ($basis === '') $fa[] = ['id' => $e->id, 'label' => $lab($e), 'authors' => implode(',', $wb)];
    if ($basis !== '' && $basis !== 'printed-byline') { $bapp++; if ($note === '') $fb[] = ['id' => $e->id, 'label' => $lab($e), 'basis' => $basis]; } }
  elseif ($basis !== '' || $note !== '') { $fc[] = ['id' => $e->id, 'label' => $lab($e), 'basis' => $basis]; }
  if ($wb) { $capp++; $both = array_intersect($wb, $ids($e, 'subjectPerson')); if ($both) $fd[] = ['id' => $e->id, 'label' => $lab($e), 'person' => implode(',', $both)]; }
}
$add('AUTH-1', 'Every record with an author link carries an authorship basis (Nathan, 7 October: "add a basis to authorship ... Set it on all")', $aapp, $fa, 'Articles, documents, obituaries and collections changed in the window with writtenBy set.');
$add('AUTH-2', 'A basis other than a printed byline says from what, in authorshipBasisNote (7 October)', $bapp, $fb);
$add('AUTH-3', 'No authorship basis stands without an author link', $aapp + count($fc), $fc, 'Applies to the same records plus any with a basis and no author.');
$add('AUTH-4', 'Author wins when someone is both author and subject (2 October)', $capp, $fd, 'A person in both writtenBy and subjectPerson on one record is listed; the rule may be read as allowing both on an autobiography, so read before acting.');

/* TTL: titles */
$t1 = []; $t1app = 0; $t2 = []; $t2app = 0; $t3 = []; $t3app = 0; $t4 = []; $t4app = 0; $t5 = []; $t6 = [];
$orgTitles = array_map('mb_strtolower', array_column($q("select es.title from {{%entries}} en join {{%elements}} e on e.id = en.id join {{%elements_sites}} es on es.elementId = e.id
  join {{%sections}} sc on sc.id = en.sectionId where sc.handle = 'organizations' and e.revisionId is null and e.draftId is null and e.dateDeleted is null"), 'title'));
foreach ($E as $e) {
  if (trim((string)$e->title) === '') $t5[] = ['id' => $e->id, 'label' => $lab($e)];
  $h = $e->section->handle;
  if ($h === 'documents') { $t1app++;
    if (preg_match('~\(([^()]*,){1,}[^()]*\b(1[6-9]|20)\d\d\)\s*$~', $e->title)) $t1[] = ['id' => $e->id, 'label' => $lab($e)];
    if (preg_match('~:\s*(.+)$~', $e->title, $m) && in_array(mb_strtolower(trim($m[1])), $orgTitles, true)) $t6[] = ['id' => $e->id, 'label' => $lab($e), 'after colon' => $m[1]]; }
  if ($h === 'photographs' && ($s($e, 'legacyUrl') !== '' || $s($e, 'legacyKey') !== '')) { $t2app++; if ($s($e, 'catalogueCaption') === '') $t2[] = ['id' => $e->id, 'label' => $lab($e)]; }
  if ($h === 'persons') { $t4app++; if (preg_match('~^(Dr\.?|Doctor|Capt\.?|Captain|Col\.?|Colonel|Congressman|Congresswoman|Councilman|Councilwoman|Mayor|Supervisor|Sheriff|Judge|Senator|Sen\.|Rev\.?|Reverend|Father|Chief|Gen\.?|General|Hon\.?)\s~', $e->title)) $t4[] = ['id' => $e->id, 'label' => $lab($e)]; }
}
/* Retitled articles and documents with a legacy page keep Leon's page headline (7 October, night) */
$base = []; foreach ($q("select rv.canonicalId c, es.title t from {{%revisions}} rv join {{%elements}} el on el.revisionId = rv.id join {{%elements_sites}} es on es.elementId = el.id
  where rv.id in (select max(rv2.id) from {{%revisions}} rv2 join {{%elements}} el2 on el2.revisionId = rv2.id where el2.dateCreated < :s group by rv2.canonicalId)", [':s' => $SINCE]) as $r) { $base[(int)$r['c']] = (string)$r['t']; }
foreach ($E as $e) {
  if (!in_array($e->section->handle, ['articles', 'documents'], true) || !isset($base[$e->id]) || $base[$e->id] === (string)$e->title) continue;
  if ($s($e, 'legacyUrl') === '' && $s($e, 'legacyKey') === '' && $s($e, 'sourcePath') === '') continue;
  $t3app++; if ($s($e, 'legacyHeadline') === '') $t3[] = ['id' => $e->id, 'label' => $lab($e), 'was' => $base[$e->id]];
}
$add('TTL-1', 'A document\'s title carries no "(Author, Publication, Date)" suffix (Nathan, 7 October: "Author, publication and date are fields")', $t1app, $t1);
$add('TTL-2', 'A photograph with a legacy page keeps Leon\'s whole catalogue entry in catalogueCaption (7 October, evening: "kept, every word, never discarded")', $t2app, $t2, 'The 20 left as they were on 7 October (7 not on Reggie, 4 multi-piece, 9 no headline) are expected among any listed.');
$add('TTL-3', 'An article or document retitled since 24 September that had a legacy page keeps Leon\'s page headline in legacyHeadline (7 October, night)', $t3app, $t3,
  'Read from Craft revisions: the title in the last revision before 24 September against the title now. The rule was made on 7 October, night; the 54 articles retitled that evening under the earlier rule are in this set, and for them a missing legacyHeadline may be what the earlier rule left, not a fault of the later.');
$add('TTL-4', 'No honorific or civic title in a person\'s title (DATA-MODEL, the name policy)', $t4app, $t4);
$add('TTL-5', 'No record has an empty title (ERRORLOG, open since 16 September)', count($E), $t5);
$add('TTL-6', 'An agency belongs in fields, not after a colon in the title (Nathan, 7 October, on #26573)', $t1app, $t6, 'Documents whose title ends with ": " and the exact title of an organization record.');

/* TYPE: an article that was a photograph says how it is held (7 October) */
$ty = []; $tyapp = 0;
foreach ($bySec['articles'] ?? [] as $e) {
  if ($s($e, 'catalogueCaption') === '' && $s($e, 'photoSourceCode') === '' && $s($e, 'creditRaw') === '') continue; $tyapp++;
  if ($s($e, 'heldAs') === '') $ty[] = ['id' => $e->id, 'label' => $lab($e)];
}
$add('TYPE-1', 'An article holding photograph details (once filed as a photograph) records heldAs (7 October, evening)', $tyapp, $ty, 'The type rule itself ("type follows the thing") cannot be checked by query; see the report.');

/* PER: person records */
$cand = []; foreach ($q("select r.targetId p, r.sourceId c from {{%relations}} r join {{%fields}} f on f.id = r.fieldId join {{%elements}} e on e.id = r.sourceId
  where f.handle = 'candidacyPerson' and e.revisionId is null and e.draftId is null and e.dateDeleted is null") as $r) { $cand[(int)$r['p']][] = (int)$r['c']; }
$inbound = []; foreach ($q("select r.targetId p, f.handle h, r.sourceId s from {{%relations}} r join {{%fields}} f on f.id = r.fieldId join {{%elements}} e on e.id = r.sourceId
  where e.revisionId is null and e.draftId is null and e.dateDeleted is null") as $r) { $inbound[(int)$r['p']][$r['h']] = true; }
$p1 = []; $p2 = []; $p3 = []; $p4 = []; $p5 = []; $papp = 0;
$personTitles = [];
foreach ($q("select en.id, es.title from {{%entries}} en join {{%elements}} e on e.id = en.id join {{%elements_sites}} es on es.elementId = e.id join {{%sections}} sc on sc.id = en.sectionId
  where sc.handle = 'persons' and e.revisionId is null and e.draftId is null and e.dateDeleted is null") as $r) { $personTitles[mb_strtolower(trim($r['title']))][] = (int)$r['id']; }
$norm = fn($x) => mb_strtolower(trim(preg_replace('~[\s.,"\']+~u', ' ', $x)));
foreach ($bySec['persons'] ?? [] as $e) {
  $papp++;
  $in = array_keys($inbound[$e->id] ?? []); $out = [];
  foreach (['body', 'featuredImage', 'personOrganizations', 'articlesAbout', 'personObituaries', 'relatedMilitary', 'personEvents', 'recordImages'] as $h) { $v = $g($e, $h); if ($v === null) continue; if (is_string($v) || is_object($v) && method_exists($v, '__toString') && !method_exists($v, 'ids')) { if (trim((string)$v) !== '') $out[] = $h; } elseif (is_object($v) && method_exists($v, 'ids') && (clone $v)->status(null)->ids()) $out[] = $h; }
  $onlyCand = $in && !array_diff($in, ['candidacyPerson']) && !$out;
  if ($onlyCand) { $won = false; foreach ($cand[$e->id] ?? [] as $cid) { $c = Entry::find()->id($cid)->status(null)->one(); if ($c && $s($c, 'outcome') === 'elected') $won = true; }
    if (!$won) $p1[] = ['id' => $e->id, 'label' => $lab($e)]; }
  $onlyOffice = $in && !array_diff($in, ['holdingPerson', 'candidacyPerson']) && !$out;
  if ($onlyOffice && in_array('holdingPerson', $in, true)) $p2[] = ['id' => $e->id, 'label' => $lab($e)];
  $bd = $s($e, 'birthDate'); $dd = $s($e, 'deathDate');
  if ($dd === '' && preg_match('~\b(19[1-9]\d|20[0-2]\d)\b~', $bd, $m) && preg_match('~[A-Za-z]{3,}\.? \d{1,2}|\d{1,2} [A-Za-z]{3,}|\d{4}-\d{2}-\d{2}|\d{1,2}/\d{1,2}/~', $bd)) $p3[] = ['id' => $e->id, 'label' => $lab($e), 'birthDate' => $bd];
  $t = $norm($e->title);
  foreach (['personAliases' => 'shown alias', 'personSearchNames' => 'search name'] as $h => $what) {
    foreach (preg_split('~[\r\n;|]+~', $s($e, $h)) as $al) { $al = trim($al); if ($al === '') continue;
      if ($h === 'personAliases' && $norm($al) === $t) $p4[] = ['id' => $e->id, 'label' => $lab($e), 'alias' => $al, 'why' => 'same as the title'];
      foreach ($personTitles[mb_strtolower($al)] ?? [] as $other) if ($other !== $e->id) $p5[] = ['id' => $e->id, 'label' => $lab($e), 'alias' => "$al ($what)", 'why' => "another person's title, #$other"]; }
  }
}
$add('PER-1', 'No person record for someone with nothing but losing candidacies (DATA-MODEL, 1 October)', $papp, $p1, 'Persons changed in the window whose only inbound links are candidacies, none elected, with no body, portrait or other link.');
$add('PER-2', 'An office held alone is a row, not a record, unless an exception holds (PROFILES, 6 October)', $papp, $p2,
  'Only a candidate list: persons whose only inbound links are office holdings and candidacies and who have no body, portrait or other link. Whether "sources exist to write from", or whether they resigned, died in office, held higher office or sat on a first board, cannot be read by query. DATA-MODEL still says an office holding alone keeps a record: see the contradictions.');
$add('PER-3', 'A living person\'s birth date is cut to the year (2 October)', $papp, $p3, 'Persons with no death date, born 1910 or later, whose birthDate prints a day.');
$add('PER-4', 'Shown aliases differ from the title (5 and 6 October: same-name forms go to search names)', $papp, $p4);
$add('PER-5', 'A name form that is another person\'s name is neither alias nor search name (DATA-MODEL, 6 October)', $papp, $p5, 'Exact match on another person record\'s title only.');

/* Office holdings */
$o1 = []; $o2 = []; $oapp = 0; $sel = [];
foreach ($bySec['officeHoldings'] ?? [] as $e) {
  $oapp++;
  if (!$ids($e, 'holdingPerson') && $s($e, 'holderName') === '') $o1[] = ['id' => $e->id, 'label' => $lab($e)];
  $he = $s($e, 'howEnded'); $te = $s($e, 'termEndEdtf') ?: $s($e, 'termEnd');
  if ($he === 'serving' && preg_match('~^(\d{4})(-(\d{2}))?(-(\d{2}))?~', $te, $m)) { $end = $m[1] . '-' . ($m[3] ?? '12') . '-' . ($m[5] ?? '28'); if ($end < '2026-10-08') $o2[] = ['id' => $e->id, 'label' => $lab($e), 'termEnd' => $te]; }
  $sm = $s($e, 'selectionMethod'); $sel[$sm ?: '(empty)'] = ($sel[$sm ?: '(empty)'] ?? 0) + 1;
}
$add('OFF-1', 'A term with no person linked names its holder in holderName (6 October)', $oapp, $o1);
$add('OFF-2', 'One rule for current: a term marked serving has no end date already passed (4 October)', $oapp, $o2, '', ['selectionMethods' => $sel]);

/* EVENTS: content advisories, consequences */
$adv = []; $advapp = 0;
$advTargets = [];
foreach ($bySec['fallenOfficers'] ?? [] as $e) $advTargets[$e->id] = $e;
foreach ($Eid as $e) if ($e->section->handle === 'events' && preg_match('~shoot|kill|murder|standoff|massacre|incident|siege|dam|crash|flight~i', $e->title)) { $advTargets[$e->id] = $e; foreach ($ids($e, 'sourceDocuments') as $d) { $de = Entry::find()->id($d)->status(null)->one(); if ($de) $advTargets[$de->id] = $de; } }
foreach ($advTargets as $e) {
  $advapp++; $has = false;
  foreach ((array)$g($e, 'editorNotes') as $row) { if (($row['position'] ?? $row['col3'] ?? '') === 'top' && preg_match('~^This record concerns~', trim((string)($row['note'] ?? $row['col2'] ?? '')))) $has = true; }
  if (!$has) $adv[] = ['id' => $e->id, 'label' => $lab($e)];
}
$add('EV-1', 'A record about killing, violent injury or a suicide carries the one-line advisory as a top editor note (DATA-MODEL, 5 October)', $advapp, $adv,
  'Applies to fallen officers and to events changed in the window whose title names a shooting, killing, standoff, siege, incident, dam, crash or flight, with their source records. Whether each concerns killing or injury is a reading; an air crash or the dam may or may not be meant (the rule names the dam, the Newhall Incident and the Kuredjian standoff).');
$c1 = []; $c1app = 0;
foreach ($bySec['events'] ?? [] as $e) foreach ((array)$g($e, 'eventConsequences') as $i => $row) { $c1app++; if (trim((string)($row['source'] ?? $row['col3'] ?? '')) === '') $c1[] = ['id' => $e->id, 'label' => $lab($e), 'row' => $i + 1]; }
$add('EV-2', 'Each consequence on an event quotes the source that makes the link (DATA-MODEL, 7 October)', $c1app, $c1, 'Counted per row.');
$fo = []; foreach ($bySec['fallenOfficers'] ?? [] as $e) if ($e->enabled) $fo[] = ['id' => $e->id, 'label' => $lab($e)];
$add('EV-3', 'Fallen officers stay disabled until Nathan has read them (5 October)', count($bySec['fallenOfficers'] ?? []), $fo, 'A state rule: it lapses when Nathan reads them; TODO still lists the reading as open.');

/* NOTES */
$searches = json_decode((string)@file_get_contents("$root/inventory/source-searches.json"), true) ?: [];
$skeys = []; array_walk_recursive($searches, function ($v, $k) use (&$skeys) { if (is_numeric($k)) $skeys[(int)$k] = true; if (is_string($v) && preg_match_all('~#(\d{2,6})\b~', $v, $m)) foreach ($m[1] as $x) $skeys[(int)$x] = true; });
foreach (array_keys($searches['records'] ?? []) as $k) if (preg_match('~:(\d+)$~', (string)$k, $m)) $skeys[(int)$m[1]] = true;
$n1 = []; $n1app = 0; $n2 = []; $n2app = 0; $n3 = []; $n4 = 0; $blank = [];
foreach ($E as $e) {
  $notes = [];
  foreach ((array)$g($e, 'editorNotes') as $row) $notes[] = ['editor note', (string)($row['note'] ?? $row['col2'] ?? '')];
  foreach ((array)$g($e, 'footnotes') as $row) { $nt = (string)($row['note'] ?? $row['col2'] ?? ''); $notes[] = ['footnote', $nt]; if (trim($nt) === '') $blank[$e->id] = true; }
  foreach ($notes as [$kindN, $nt]) {
    if (preg_match('~.{0,90}(\bno (other )?source\b|\bnot (been )?found\b|\bnothing (in|on) the\b|\bno record (of|names|in)\b|\bnone (is|was) found\b).{0,60}~iu', $nt, $mm)) { $n1app++; if (!isset($skeys[$e->id])) $n1[] = ['id' => $e->id, 'label' => $lab($e), 'where' => $kindN, 'text' => trim($mm[0])]; }
    if (preg_match('~wikipedia~i', $nt)) { $n2app++; $n2[] = ['id' => $e->id, 'label' => $lab($e), 'where' => $kindN, 'text' => mb_substr(trim($nt), 0, 200)]; }
    if (preg_match('~(\w)\s*(\.\.\.|…)\s*(\w)~u', $nt) && preg_match('~Code|Ordinance|Resolution|section|§~', $nt)) $n3[] = ['id' => $e->id, 'label' => $lab($e), 'text' => mb_substr(trim($nt), 0, 160)];
  }
}
$add('NOTE-1', 'A note saying no source exists or a fact was not found has its search recorded in inventory/source-searches.json (PROFILES, 3 October)', $n1app, $n1, 'Matched by wording; a note phrased otherwise is missed, and a record listed in the file under another key reads as a failure. Counted per note.');
$wk = [];
foreach ($q("select es.elementId id, es.content c from {{%elements_sites}} es join {{%elements}} e on e.id = es.elementId where e.revisionId is null and e.draftId is null and e.dateDeleted is null
  and (e.dateCreated >= :s1 or e.dateUpdated >= :s2) and lower(es.content) like '%wikipedia%'", [':s1' => $SINCE, ':s2' => $SINCE]) as $r) {
  $c = preg_replace('~https?:\\\\?/\\\\?/[^"\s]+~', '', (string)$r['c']);
  if (preg_match_all('~.{0,100}wikipedia.{0,80}~iu', $c, $m)) $wk[] = ['id' => (int)$r['id'], 'label' => isset($Eid[(int)$r['id']]) ? $lab($Eid[(int)$r['id']]) : '#' . $r['id'], 'text' => implode(' || ', array_slice($m[0], 0, 2))];
}
$n2app = count($wk); $n2 = [];
$add('NOTE-2', 'Wikipedia is a lead, never the citation (memory, 4 October; 6 October profiles: Wikipedia-only facts left out)', $n2app, $n2, 'Every record changed in the window whose text names Wikipedia outside a link, read by eye in the report; none is failed by the script.', ['toRead' => $wk]);
$add('NOTE-3', 'An ellipsis never joins two provisions (PROFILES, 5 October)', count($n3), [], 'Cannot be judged by query. Quotations of statutes or ordinances with an ellipsis, on records changed in the window, listed for reading.', ['toRead' => $n3]);
$add('NOTE-4', 'No row builder writes an empty footnote row (ERRORLOG, 5 October)', count($E), array_map(fn($id) => ['id' => $id, 'label' => $lab($Eid[$id])], array_keys($blank)), 'The template filters them, so none shows; the rule says not to write them.');

/* Misc: recordTags to entries, withheld WordPress bodies, election documents' subject organization */
$rt = [];
foreach ($q("select r.sourceId s, r.targetId t from {{%relations}} r join {{%fields}} f on f.id = r.fieldId join {{%elements}} t on t.id = r.targetId join {{%elements}} e on e.id = r.sourceId
  where f.handle = 'recordTags' and t.type <> :c and e.revisionId is null and e.draftId is null and e.dateDeleted is null", [':c' => Category::class]) as $r) $rt[] = ['id' => (int)$r['s'], 'label' => '#' . $r['s'] . ' tags element #' . $r['t']];
$add('MISC-1', 'recordTags (a Categories field) relates categories only (ERRORLOG, 7 October: check a relation field\'s type before relating)', count($E), $rt, 'All live sources, not only the window.');
$wp = []; $wpapp = 0;
foreach ($E as $e) { if ($s($e, 'bodyAuthorship') !== 'wordpress-import-unsourced' || $e->section->handle === 'persons') continue; $wpapp++; if ($s($e, 'body') !== '') $wp[] = ['id' => $e->id, 'label' => $lab($e)]; }
$add('MISC-2', 'A WordPress body that is not sourced stays withheld (3 October): body empty, text in withheldBody', $wpapp, $wp, 'Records outside persons; a person\'s unsourced WordPress body is withheld by the template from bodyAuthorship, which this audit does not render.');
$sd = []; $sdapp = 0; $docElections = [];
foreach ($q("select r.targetId d, r.sourceId el from {{%relations}} r join {{%fields}} f on f.id = r.fieldId join {{%entries}} en on en.id = r.sourceId join {{%sections}} sc on sc.id = en.sectionId join {{%elements}} e on e.id = r.sourceId
  where f.handle = 'sourceDocuments' and sc.handle = 'elections' and e.revisionId is null and e.draftId is null and e.dateDeleted is null") as $r) $docElections[(int)$r['d']][] = (int)$r['el'];
foreach ($docElections as $d => $els) { $de = $Eid[$d] ?? null; if (!$de || $de->section->handle !== 'documents') continue; $sdapp++; if (!$ids($de, 'subjectOrganization')) $sd[] = ['id' => $d, 'label' => $lab($de), 'elections' => count($els)]; }
$add('MISC-3', 'A document that does not say whose it is names its body in subjectOrganization (8 October, on the election documents)', $sdapp, $sd, 'Documents changed in the window that an election cites as a source. The 8 October ruling filled 14; a document cited by several bodies\' elections may rightly name several.');
$cat = []; foreach ($C as $c) { $cat[] = ['id' => $c->id, 'label' => '#' . $c->id . ' ' . $c->title . ' (' . $c->group->handle . ')']; }
$R['_categories'] = $cat;

/* Scripts: a fetch's User-Agent never carries a person (6 October) */
$ua = [];
foreach (glob("$root/scripts/import/*.{php,py,sh}", GLOB_BRACE) as $p) { foreach (file($p) as $i => $line) if (preg_match('~user.?agent~i', $line) && preg_match('~@[a-z0-9-]+\.(com|org|net)|imhoff|nathan~i', $line) && !preg_match('~scvhistory~i', $line)) $ua[] = ['id' => 0, 'label' => basename($p) . ':' . ($i + 1), 'text' => trim(mb_substr($line, 0, 140))]; }
$add('SCR-1', 'A fetch\'s User-Agent names the project, never a person (ERRORLOG and memory, 6 October)', count(glob("$root/scripts/import/*.{php,py,sh}", GLOB_BRACE)), $ua, 'Every script, as text; a header assembled from variables elsewhere is missed.');

file_put_contents("$root/storage/runtime/overnight-audit/fourteen-day-audit.json", json_encode($R, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo 'written: storage/runtime/overnight-audit/fourteen-day-audit.json; nothing saved to the database' . PHP_EOL;
