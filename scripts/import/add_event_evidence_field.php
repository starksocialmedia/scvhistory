/**
 * Puts startEvidence on the event layout.
 *
 * An event's date carries a claim about how we know it, the same as a
 * person's birth or an office holder's term: certified, contemporary,
 * retrospective, roster or uncited. The field exists (add_office_holding.php
 * made it for terms) and events never had it, so the Lyon, Wiley and Jenkins
 * well, known only from accounts written 37 to 129 years later, could not say
 * so. The existing field goes on the layout, beside the dates.
 *
 * Schema only. Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_event_evidence_field.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$svc = Craft::$app->getEntries();
$type = $svc->getEntryTypeByHandle('event');
$field = Craft::$app->getFields()->getFieldByHandle('startEvidence');
if (!$type || !$field) { echo 'missing: ' . (!$type ? 'event type ' : '') . (!$field ? 'startEvidence' : '') . PHP_EOL; return; }

$layout = $type->getFieldLayout();
if ($layout->getFieldByHandle('startEvidence')) { echo 'startEvidence is already on the event layout; nothing to do.' . PHP_EOL; return; }

/* After eventDateEdtf, on whichever tab holds it. */
$tabs = $layout->getTabs();
$target = $tabs[0]; $at = null;
foreach ($tabs as $tab) {
    foreach ($tab->getElements() as $i => $el) {
        if ($el instanceof \craft\fieldlayoutelements\CustomField && $el->attribute() === 'eventDateEdtf') { $target = $tab; $at = $i; }
    }
}
echo 'would add startEvidence to the event layout, tab "' . $target->name . '"' . ($at !== null ? ', after eventDateEdtf' : '') . PHP_EOL;
if (!$APPLY) { echo 'DRY RUN' . PHP_EOL; return; }

$els = array_values($target->getElements());
$new = new \craft\fieldlayoutelements\CustomField($field);
if ($at !== null) { array_splice($els, $at + 1, 0, [$new]); } else { $els[] = $new; }
$target->setElements($els);
$layout->setTabs($tabs);
$type->setFieldLayout($layout);
if (!$svc->saveEntryType($type)) { throw new \RuntimeException('add_event_evidence_field: ' . json_encode($type->getErrors())); }

$ok = (bool)$svc->getEntryTypeByHandle('event')->getFieldLayout()->getFieldByHandle('startEvidence');
echo 'READ-BACK ' . ($ok ? 'OK: startEvidence is on the event layout' : 'FAIL: not on the layout after save') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_event_evidence_field.php', 1, $ok ? 'verified: on the event layout' : 'FAILED: not on the layout', 'schema only; an event date can say how it is known');
if (!$ok) { throw new \RuntimeException('add_event_evidence_field: read-back failed'); }
