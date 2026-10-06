/**
 * Aliases into search (Nathan, 5 October 2026: Johnson's former name "is in the data but not on the page, so anyone who knows
 * her as Duzick finds nothing ... make sure the site search finds her by it"). Every alias field was saved with searchable
 * false, so the site search (templates/search/index.twig, craft.entries().search()) never saw a name a record keeps for its
 * subject. Sets searchable on personAliases, placeAliases, orgAliases, groupAliases and communityAliases; the search index is
 * then rebuilt with `ddev craft resave/entries --update-search-index` per section. Writes project config. Idempotent. Dry run
 * by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/make_aliases_searchable_2026_10_05.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $n = 0;
foreach (['personAliases', 'placeAliases', 'orgAliases', 'groupAliases', 'communityAliases'] as $h) {
  $f = $fs->getFieldByHandle($h); if (!$f) { echo "$h: no such field\n"; continue; }
  echo "$h: " . ($f->searchable ? 'searchable already' : 'make searchable') . "\n";
  if ($APPLY && !$f->searchable) { $f->searchable = true; if (!$fs->saveField($f)) { throw new \RuntimeException("$h " . json_encode($f->getErrors())); } $n++; }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('make_aliases_searchable_2026_10_05.php', $n, 'verified', 'alias fields searchable'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
