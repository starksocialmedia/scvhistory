/**
 * The Powerhouse Fire: the event record, from inventory/review/powerhouse-fire-draft-2026-10-06.json, a draft by Claude
 * of 6 October 2026 that Nathan has NOT yet approved. Every quotation was checked word for word against the mirror on
 * 6 October 2026 (58 checked, 58 PASS; no ellipsis). The draft's SHA-256 is pinned below: an edit after the check
 * refuses the run until it is checked again.
 *
 * Creates the event: May 30 to June 11, 2013 (2013-05-30/2013-06-11; the end date is the Governor's proclamation's),
 * startEvidence contemporary, body, 14 notes, a content advisory (top) and three bottom editor notes (the count of homes,
 * when the fire ended, deaths), seven dated rows, research leads, era, period, theme, and the neighborhoods San
 * Francisquito Canyon and Lake Hughes. No featured image: no photograph or asset of the fire is in Craft.
 * Relations only where a footnote ties and names them (the terms below): the County Fire Department (#29866).
 * sourceDocuments: none, the notes cite no document record. Photographs: none, the LW2382 series is not in Craft.
 * Nothing is written to any other record (forNathan: the held places, photograph #2741's code).
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/powerhouse-fire-loader-dry-run-2026-10-06.md.
 * Dry run by default. Set $APPLY = true to write, only after Nathan approves the draft.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_powerhouse_fire_event_2026_10_06.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_powerhouse_fire_event_2026_10_06.php',
    'v2' => 'inventory/review/powerhouse-fire-draft-2026-10-06.json',
    'sha' => 'c0f06a8c1c47869bd5902e918baff47776e549bbc452ef84155d5f354acba897',
    'out' => 'inventory/review/powerhouse-fire-loader-dry-run-2026-10-06.md',
    'provenance' => 'the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan has NOT yet approved this draft; it is not to be applied until he does.',
    'title' => 'Powerhouse Fire',
    'eventDate' => 'May 30 to June 11, 2013', 'eventDateEdtf' => '2013-05-30/2013-06-11',
    'advisory' => true,
    'featuredImage' => null,
    'linkHeldPhotos' => false,
    /* The reviewed ties: a relation is made only when one of its footnotes contains one of these. */
    'terms' => [
        29866 => ['L.A. County Fire'],
    ],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
