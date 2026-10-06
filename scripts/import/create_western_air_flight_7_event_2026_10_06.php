/**
 * Western Air Express Flight 7 Crash: the event record, from inventory/review/western-air-flight-7-draft-2026-10-06.json,
 * drafted by Claude on 6 October 2026. The draft is NOT yet approved by Nathan. Every quotation in it was checked word
 * for word against the mirror page or Craft record it cites that day (93 PASS, none corrected, no ellipses). The draft's
 * SHA-256 is pinned below: an edit after the check refuses the run until it is checked again.
 *
 * Creates the event: January 12, 1937, startEvidence contemporary, body, 9 notes, a content advisory (the first editor
 * note, top) and five bottom editor notes (the number of dead, the time, the place, the aircraft, the names), seven dated
 * rows, research leads, era, period and neighborhood Placerita Canyon (forNathan 3). featuredImage asset 11759
 * (lw3345_large.jpg, Osa Johnson placed in a wagon, January 13, 1937; already in Craft, attached to no record;
 * forNathan 2). No relations: Craft has no record for Martin or Osa Johnson, Western Air Express, the Bureau of Air
 * Commerce or the crash site, so the terms list is empty. sourceDocuments: none, the notes cite no document record (the
 * script still reads the layout and reports). Photographs take the event in photoEvents where a cited note names them
 * (note 8 names all eight: LW3182, LW3345, LW3346, LW2784a, LW2784b, LW2431a, LW2431b and LW2443, the airplane in 1933).
 * Nothing is written to any other record. relatedEvents waits for the United Air Lines Flight 34 record.
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/western-air-flight-7-loader-dry-run-2026-10-06.md.
 * Dry run by default. Set $APPLY = true to write, only after Nathan approves the draft.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_western_air_flight_7_event_2026_10_06.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_western_air_flight_7_event_2026_10_06.php',
    'v2' => 'inventory/review/western-air-flight-7-draft-2026-10-06.json',
    'sha' => '50c9049c0bd6310e8b2a34d189e010dbd9e570b3004bb58740df8cb08c98dc84',
    'out' => 'inventory/review/western-air-flight-7-loader-dry-run-2026-10-06.md',
    'provenance' => 'the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan has NOT yet approved this draft; it is not to be applied until he does.',
    'title' => 'Western Air Express Flight 7 Crash',
    'eventDate' => 'January 12, 1937', 'eventDateEdtf' => '1937-01-12',
    'advisory' => true,
    'featuredImage' => [11759, 'lw3345_large.jpg', 'LW3345, Osa Johnson placed in a wagon for the trip down the mountain, January 13, 1937 (ACME); in archiveMedia, attached to no record'],
    'linkHeldPhotos' => false,
    /* The reviewed ties: none. The draft relates no person, place or organization (see its forNathan 3 and 5). */
    'terms' => [],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
