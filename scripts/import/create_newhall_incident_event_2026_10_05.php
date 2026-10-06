/**
 * The Newhall Incident: the event record, from inventory/review/newhall-incident-draft-v2-2026-10-05.json, the draft
 * Nathan approved on 6 October 2026: "apply all of them ... The Newhall Incident naming the gunmen once, as every source
 * does, is right." Every quotation was rechecked word for word that day (v2: three quotations corrected; the four
 * fallen officers' ties now include note 6, which cites their records, since notes 1 and 2 do not name Alleyn). The
 * draft's SHA-256 is pinned below: an edit after the check refuses the run until it is checked again.
 *
 * Creates the event: April 5 to 6, 1970 (EDTF 1970-04-05/1970-04-06), startEvidence contemporary, body, 13 notes, the
 * content advisory as the first editor note in the top position, three bottom editor notes, four dated rows, research
 * leads, era, period, Valencia; no theme (forNathan 9) and no featured image (forNathan: none from Craft as it stands).
 * The four fallen officers in eventFallenOfficers (that section is disabled; the relation reads back with status(null)),
 * the CHP and the Sheriff's Department, Pico Canyon Road. sourceDocuments: none, the notes cite no document record (the
 * script still reads the layout and reports). Photographs take the event in photoEvents where a cited note names them.
 * Nothing is written to any other record.
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/newhall-incident-loader-dry-run-2026-10-05.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_newhall_incident_event_2026_10_05.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_newhall_incident_event_2026_10_05.php',
    'v2' => 'inventory/review/newhall-incident-draft-v2-2026-10-05.json',
    'sha' => '18546276d37145a7c62d71e6be3c172243017415d8f659615db4b0423b562f65',
    'out' => 'inventory/review/newhall-incident-loader-dry-run-2026-10-05.md',
    'title' => 'The Newhall Incident',
    'eventDate' => 'April 5 to 6, 1970', 'eventDateEdtf' => '1970-04-05/1970-04-06',
    'advisory' => true,
    'featuredImage' => null,
    'linkHeldPhotos' => false,
    'terms' => [
        16248 => ['Pico Canyon Road'],
        29768 => ['Frago', '#29768'], 29770 => ['Gore', '#29770'], 29772 => ['Pence', '#29772'], 29774 => ['Alleyn', '#29774'],
        29792 => ['California Highway Patrol'], 29282 => ["Los Angeles County Sheriff"],
    ],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
