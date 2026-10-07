/**
 * sourceDocuments takes articles as well as documents (Nathan, 7 October 2026: "A source link that only accepts documents cannot
 * cite a newspaper article, which is most of what this archive is made of. Widen it to accept articles rather than mistyping
 * articles as documents to fit it"). The handle stays (templates and 138 links read it); the name becomes "Sources".
 * Used on elections (114 links) and events (24). Templates read a document's file only where the record has documentFiles.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/widen_source_links_2026_10_07.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $n = 0;
$f = $fs->getFieldByHandle('sourceDocuments');
$want = array_map(fn($h) => 'section:' . $es->getSectionByHandle($h)->uid, ['documents', 'articles']);
$have = is_array($f->sources) ? $f->sources : [$f->sources];
$todo = array_values(array_diff($want, $have));
echo 'sourceDocuments "' . $f->name . '": accepts ' . count($have) . ' source(s); ' . ($todo ? 'add articles, rename to "Sources"' : 'already takes articles') . PHP_EOL;
if ($APPLY && ($todo || $f->name !== 'Sources')) {
  $f->sources = array_values(array_unique(array_merge($have, $want))); $f->name = 'Sources';
  $f->instructions = 'The records this one rests on: articles (most of the archive, including newspaper pieces held as scans) and documents.';
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('widen_source_links_2026_10_07.php', $n, 'verified', 'sourceDocuments takes articles; named Sources'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
