/**
 * Obituaries get an author and their companion pieces (Nathan, 5 October 2026, on Connie Worden-Roberts's two obituaries:
 * "If two, keep both and link them to each other ... The second is Carl Goldman's. Set him as its author"). The obituary type
 * had no author field and no field to relate one piece on a death to another.
 * - writtenBy, the existing person field articles use (schema.org author), added to the obituary type after publicationDetails.
 * - obitCompanions, a new Entries field (obituaries, articles, documents): other pieces on the same death, where the sources
 *   themselves join them (Connie's three legacy pages list one another). Shown as ALSO ON THIS DEATH.
 * Both classed "page" in scripts/import/field-display.json; the template shows them (templates/obituaries/_entry.twig).
 * Writes project config. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/obituary_author_and_companions_2026_10_05.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $n = 0;
$secUid = fn($h) => 'section:' . $es->getSectionByHandle($h)->uid;
$c = $fs->getFieldByHandle('obitCompanions'); echo 'obitCompanions: ' . ($c ? 'exists' : 'create') . PHP_EOL;
if ($APPLY && !$c) {
  $c = new \craft\fields\Entries(['name' => 'Also on this death', 'handle' => 'obitCompanions', 'sources' => [$secUid('obituaries'), $secUid('articles'), $secUid('documents')],
    'instructions' => 'Other obituaries, tributes and reports on the same death, only where the sources themselves join them.']);
  if (!$fs->saveField($c)) { throw new \RuntimeException(json_encode($c->getErrors())); } $n++;
}
$wb = $fs->getFieldByHandle('writtenBy'); if (!$wb) { throw new \RuntimeException('no writtenBy'); }
foreach ($es->getSectionByHandle('obituaries')->getEntryTypes() as $et) {
  $layout = $et->getFieldLayout(); $have = array_map(fn($x) => $x->handle, $layout->getCustomFields());
  $add = array_values(array_filter(['writtenBy', 'obitCompanions'], fn($h) => !in_array($h, $have, true)));
  echo "{$et->handle}: " . ($add ? 'add ' . implode(', ', $add) : 'has both') . PHP_EOL;
  if (!$APPLY || !$add) { continue; }
  $tabs = $layout->getTabs(); $done = false;
  foreach ($tabs as $tab) { $els = []; foreach ($tab->getElements() as $x) { $els[] = $x;
    if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'publicationDetails') { foreach ($add as $h) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h)); } $done = true; } }
    $tab->setElements($els); }
  if (!$done) { throw new \RuntimeException('no publicationDetails to place them after'); }
  $layout->setTabs($tabs); $et->setFieldLayout($layout); if (!$es->saveEntryType($et)) { throw new \RuntimeException(json_encode($et->getErrors())); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('obituary_author_and_companions_2026_10_05.php', $n, 'verified', 'writtenBy and obitCompanions on obituaries'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
