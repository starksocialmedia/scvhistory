/**
 * United Air Lines Flight 34 Crash in Rice Canyon: the event record, from
 * inventory/review/united-flight-34-draft-2026-10-06.json, drafted by Claude on 6 October 2026 and written directly in
 * the checked form (every quotation checked word for word against the mirror page it cites that day; quotationCheck).
 * The draft is NOT yet approved by Nathan. The draft's SHA-256 is pinned below: an edit after the check refuses the run
 * until it is checked again.
 *
 * Creates the event: December 27, 1936, startEvidence contemporary, body, 9 notes, a content advisory (top) and three
 * bottom editor notes (the last words from the plane; names and numbers in the first reports; where it came down),
 * three dated rows, research leads, era and period. No theme or neighborhood (none fits; forNathan 4).
 * featuredImage asset 10737 (lw2448a_large.jpg, the ACME wirephoto of the victims' watches, in archiveMedia; forNathan 1).
 * No relations: the sources name no place, person, organization, article or document record in Craft (research leads).
 * sourceDocuments: none, the notes cite no document record (the script still reads the layout and reports).
 * relatedEvents waits for the Western Air Express Flight 7 record. Photographs take the event in photoEvents where a
 * cited note names them: #3691 LW2448a, #3693 LW2448b, #4555 LW2826a, #4557 LW2826b, #4651 LW2897. Nothing is written
 * to any other record (forNathan 2 and 3, the photoDates and image attachments, are separate fixes).
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/united-flight-34-loader-dry-run-2026-10-06.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_united_flight_34_event_2026_10_06.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_united_flight_34_event_2026_10_06.php',
    'v2' => 'inventory/review/united-flight-34-draft-2026-10-06.json',
    'sha' => '25af612c0c22cb27f5888edf4b130520af2a63bad2506f81d9f0c7bd20b1a5e8',
    'out' => 'inventory/review/united-flight-34-loader-dry-run-2026-10-06.md',
    'provenance' => 'the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan approved it for applying on 6 October 2026.',
    'title' => 'United Air Lines Flight 34 Crash in Rice Canyon',
    'eventDate' => 'December 27, 1936', 'eventDateEdtf' => '1936-12-27',
    'advisory' => true,
    'featuredImage' => [10737, 'lw2448a_large.jpg', 'the ACME wirephoto of December 31, 1936, of the three victims\' broken watches (LW2448a, photograph #3691; in archiveMedia, not yet attached to the photograph)'],
    'linkHeldPhotos' => false,
    /* The reviewed ties: none. The sources name no Craft record to relate. */
    'terms' => [],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
