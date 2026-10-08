/**
 * Every person record whose portrait changed on 8 October 2026, for Nathan (8 October: "every person record whose portrait
 * changed or was removed today, with the old image, the new one or none, and the one-line reason under which rule").
 * Read only. Before is the portrait in each record's last revision made before 8 October (Craft keeps a revision of
 * every save, scripts included); now is the record as it stands. A portrait whose file was replaced in place today keeps
 * its asset, so it is found by its asset instead (the assets restored to their own files). Writes
 * storage/runtime/photo-import/portrait-changes.json.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/portrait_changes_2026_10_08.php'))"
 */
use craft\elements\{Entry, Asset};
$root = \Craft::getAlias('@root');
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['record', 'Craft revisions of every person record (the featuredImage relation of each)', 'which picture each page showed', 'not read: the pages are not fetched; the relation is what the page renders from'],
  ['record', 'Craft fields source, provenanceKind, enhancementMethod, contentCredentials and filename on each asset, before and now', 'the image files', 'not read: the files were read when each change was made (the day\'s scripts and the contact sheets); here the asset records name them'],
]);
$CUT = '2026-10-08 07:00:00'; /* midnight, 8 October, Pacific time, in Craft's UTC */
$fid = (int)Craft::$app->getFields()->getFieldByHandle('featuredImage')->id;
$q = fn($sql, $p = []) => Craft::$app->getDb()->createCommand($sql, $p)->queryAll();
$sec = (int)Craft::$app->getEntries()->getSectionByHandle('persons')->id;
$people = $q("select en.id from {{%entries}} en join {{%elements}} el on el.id = en.id where en.sectionId = :s and el.revisionId is null and el.draftId is null", [':s' => $sec]);
$rows = [];
foreach ($people as $p) {
  $id = (int)$p['id'];
  $rev = $q("select el.id from {{%revisions}} r join {{%elements}} el on el.revisionId = r.id where r.canonicalId = :id and el.dateCreated < :c order by r.num desc limit 1", [':id' => $id, ':c' => $CUT]);
  $before = $rev ? array_map('intval', array_column($q("select targetId from {{%relations}} where fieldId = :f and sourceId = :s order by sortOrder", [':f' => $fid, ':s' => $rev[0]['id']]), 'targetId')) : [];
  $now = array_map('intval', array_column($q("select targetId from {{%relations}} where fieldId = :f and sourceId = :s order by sortOrder", [':f' => $fid, ':s' => $id]), 'targetId'));
  $inPlace = $now && $now === $before && ($a = Asset::find()->id($now[0])->one()) && str_contains((string)$a->getFieldValue('source'), 'Restored on 8 October 2026');
  if ($before === $now && !$inPlace) { continue; }
  $e = Entry::find()->id($id)->status(null)->one();
  $desc = function ($ids) { if (!$ids) return null; $a = Asset::find()->id($ids[0])->one(); if (!$a) return ['id' => $ids[0], 'file' => '(asset gone)'];
    $l = $a->getFieldLayout(); $g = fn($h) => $l && $l->getFieldByHandle($h) ? trim((string)$a->getFieldValue($h)) : '';
    return ['id' => $a->id, 'file' => $a->filename, 'kind' => $g('provenanceKind'), 'method' => $g('enhancementMethod'), 'cc' => $g('contentCredentials'), 'source' => $g('source')]; };
  $rows[] = ['id' => $id, 'title' => $e->title, 'url' => $e->url, 'before' => $desc($before), 'now' => $desc($now), 'inPlace' => (bool)$inPlace];
}
usort($rows, fn($x, $y) => strcmp($x['title'], $y['title']));
file_put_contents("$root/storage/runtime/photo-import/portrait-changes.json", json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
$c = ['removed' => 0, 'replaced' => 0, 'added' => 0, 'file restored in place' => 0];
foreach ($rows as $r) { $k = $r['inPlace'] ? 'file restored in place' : (!$r['now'] ? 'removed' : (!$r['before'] ? 'added' : 'replaced')); $c[$k]++;
  echo "#{$r['id']} {$r['title']}: " . ($r['before']['file'] ?? 'none') . ' -> ' . ($r['inPlace'] ? 'same asset, file restored' : ($r['now']['file'] ?? 'none')) . PHP_EOL; }
echo count($rows) . ' person records: ' . json_encode($c) . PHP_EOL;
