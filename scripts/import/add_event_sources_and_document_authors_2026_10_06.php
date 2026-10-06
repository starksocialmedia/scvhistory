/**
 * Two joins Nathan asked for on 6 October 2026: "Events having no source-documents field is a gap. Add one rather than citing in
 * footnotes, since a reader should see what an event rests on"; "add writtenBy to documents".
 * - sourceDocuments, the field elections use (document records), added to the event type after eventArticles; the event page
 *   lists them as SOURCES (templates/events/_entry.twig).
 * - writtenBy, the person field articles and obituaries use, added to the document type after publishedBy; the document page
 *   names the author (templates/documents/_entry.twig).
 * Writes project config. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_event_sources_and_document_authors_2026_10_06.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $n = 0;
foreach ([['events', 'sourceDocuments', 'eventArticles'], ['documents', 'writtenBy', 'publishedBy']] as [$sec, $h, $after]) {
  $f = $fs->getFieldByHandle($h); if (!$f) { throw new \RuntimeException("no field $h"); }
  foreach ($es->getSectionByHandle($sec)->getEntryTypes() as $et) {
    $layout = $et->getFieldLayout(); $have = array_map(fn($x) => $x->handle, $layout->getCustomFields());
    if (in_array($h, $have, true)) { echo "$sec/{$et->handle}: has $h\n"; continue; }
    echo "$sec/{$et->handle}: add $h after $after\n"; if (!$APPLY) { continue; }
    $tabs = $layout->getTabs(); $done = false;
    foreach ($tabs as $tab) { $els = []; foreach ($tab->getElements() as $x) { $els[] = $x; if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === $after) { $els[] = new \craft\fieldlayoutelements\CustomField($f); $done = true; } } $tab->setElements($els); }
    if (!$done) { throw new \RuntimeException("$sec: no $after"); }
    $layout->setTabs($tabs); $et->setFieldLayout($layout); if (!$es->saveEntryType($et)) { throw new \RuntimeException(json_encode($et->getErrors())); } $n++;
  }
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_event_sources_and_document_authors_2026_10_06.php', $n, 'verified', 'sourceDocuments on events; writtenBy on documents'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
