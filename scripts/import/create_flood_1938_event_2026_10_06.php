/**
 * The Great Flood of 1938: the event record, from inventory/review/flood-1938-draft-2026-10-06.json, a draft written by
 * Claude on 6 October 2026 and NOT yet approved by Nathan. Every quotation was checked word for word against the mirror
 * that day (quotationCheck in the draft; no ellipsis, none failed). The draft's SHA-256 is pinned below: an edit after
 * the check refuses the run until it is checked again.
 *
 * Creates the event: March 2, 1938 (Leon Worden's chronology), eventDateStart February 27, 1938 (the storm's start),
 * no end date (no source gives one), startEvidence contemporary, body, 14 notes, a content advisory at the top, two
 * bottom editor notes (the dates; the number of dead), seven dated rows, research leads, era, period, theme and five
 * communities. No featuredImage: the one flood photograph in Craft is the derailment of March 25 (LW3067); JN3801, the
 * rodeo grounds after the flood, is not in Craft. Relations only where a footnote ties and names them (the terms below).
 * eventArticles: #1444 (Perkins, named in note 9). sourceDocuments: none, the notes cite no document record (the script
 * still reads the layout and reports). Photograph #4871 (LW3067), named in note 11, takes the event in photoEvents.
 * Nothing is written to any other record: the draft's forNathan items (Bonelli, Paul Hill, the photographs not in
 * Craft) are not acted on.
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/flood-1938-loader-dry-run-2026-10-06.md.
 * Dry run by default. Set $APPLY = true to write, only after Nathan approves the draft.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_flood_1938_event_2026_10_06.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_flood_1938_event_2026_10_06.php',
    'v2' => 'inventory/review/flood-1938-draft-2026-10-06.json',
    'sha' => 'ccb319416f54b92f5b0856dce38b3751bffb5430475a9631e4a668ff69f920e6',
    'out' => 'inventory/review/flood-1938-loader-dry-run-2026-10-06.md',
    'provenance' => 'the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan has NOT yet approved this draft; it is not to be applied until he does.',
    'title' => 'Great Flood of 1938',
    'eventDate' => 'March 2, 1938', 'eventDateEdtf' => '1938-03-02',
    'advisory' => true,
    'featuredImage' => null,
    'linkHeldPhotos' => false,
    /* The reviewed ties: a relation is made only when one of its footnotes contains one of these. */
    'terms' => [
        15596 => ['Santa Clara River'], 643 => ['Saugus Speedway'], 15886 => ['Castaic Creek'], 18529 => ['Soledad Canyon'],
        18565 => ['Placerita Canyon'], 16101 => ['Southern Pacific'],
    ],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
