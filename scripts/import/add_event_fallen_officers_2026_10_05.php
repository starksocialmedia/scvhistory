/**
 * eventFallenOfficers: an event's fallen officers (Nathan, 5 October 2026: "Yes, add the field for fallen officers on
 * events. The Newhall Incident and the Kuredjian standoff both need it"). eventPersons takes only the persons section, and a
 * fallen officer's record is in its own section, so an event could not name the officers killed in it. An Entries field
 * limited to the fallenOfficers section, placed after eventPersons on the event type, shown on the page as FALLEN OFFICERS
 * (templates/events/_entry.twig) and classed "page" in scripts/import/field-display.json. Writes project config.
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_event_fallen_officers_2026_10_05.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $n = 0;
$sec = $es->getSectionByHandle('fallenOfficers'); if (!$sec) { throw new \RuntimeException('no fallenOfficers section'); }
$f = $fs->getFieldByHandle('eventFallenOfficers'); echo 'eventFallenOfficers: ' . ($f ? 'exists' : 'create') . PHP_EOL;
if ($APPLY && !$f) {
  $f = new \craft\fields\Entries(['name' => 'Fallen officers', 'handle' => 'eventFallenOfficers', 'sources' => ['section:' . $sec->uid],
    'instructions' => 'Officers killed in this event, from the Fallen officers section. Only where a source ties the officer to the event.']);
  if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getErrors())); } $n++;
}
$ev = $es->getSectionByHandle('events');
foreach ($ev->getEntryTypes() as $et) {
  $layout = $et->getFieldLayout(); $handles = array_map(fn($x) => $x->handle, $layout->getCustomFields());
  if (in_array('eventFallenOfficers', $handles, true)) { echo "{$et->handle}: has it\n"; continue; }
  echo "{$et->handle}: add after eventPersons\n"; if (!$APPLY) { continue; }
  $tabs = $layout->getTabs(); $done = false;
  foreach ($tabs as $tab) { $els = []; foreach ($tab->getElements() as $x) { $els[] = $x; if (!$done && $x instanceof \craft\fieldlayoutelements\CustomField && $x->getField()->handle === 'eventPersons') { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle('eventFallenOfficers')); $done = true; } } $tab->setElements($els); }
  if (!$done) { throw new \RuntimeException("{$et->handle}: no eventPersons to place it after"); }
  $layout->setTabs($tabs); $et->setFieldLayout($layout);
  if (!$es->saveEntryType($et)) { throw new \RuntimeException(json_encode($et->getErrors())); } $n++;
}
if ($APPLY) { $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_event_fallen_officers_2026_10_05.php', $n, 'verified', 'eventFallenOfficers on the event type'); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
