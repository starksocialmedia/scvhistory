/**
 * The Placerita gold discovery: the event record, from inventory/review/placerita-gold-discovery-draft-v2-2026-10-05.json,
 * the draft Nathan approved on 6 October 2026 ("apply all of them"), with every quotation rechecked word for word that
 * day (v2: nine quotations corrected, five ellipses removed; a research lead's figure unquoted; recordDates given their
 * ISO dates). The draft's SHA-256 is pinned below: an edit after the check refuses the run until it is checked again.
 *
 * Creates the event: March 9, 1842 (the petition's own date), startEvidence contemporary, Landmark No. 168 on the event
 * (forNathan 9), body, 18 notes, four bottom editor notes (no content advisory: the draft has none), ten dated rows,
 * research leads, era, period, theme, Placerita Canyon. No featured image: the draft's choice (LW2217) is not yet
 * attached anywhere. Relations only where a footnote ties and names them (the terms below). sourceDocuments: document
 * #26983 (Stearns's letter), which notes 2 and 7 cite (the field is being added to the event type by another step;
 * before it is there the script sets nothing and says so, and a later run fills it). Photographs take the event in
 * photoEvents where a cited note names them; the rest are held.
 * No place record is made: forNathan 6 proposes one for the Oak of the Golden Dream, to be linked after, not as a
 * prerequisite of this event. Nothing is written to any other record (forNathan 4 and 5, on #309 and #18834, wait).
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/placerita-gold-discovery-loader-dry-run-2026-10-05.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_placerita_gold_discovery_event_2026_10_05.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_placerita_gold_discovery_event_2026_10_05.php',
    'v2' => 'inventory/review/placerita-gold-discovery-draft-v2-2026-10-05.json',
    'sha' => '86bb0287e96619b8b2b5631a6ac9b62c5f845133f0921c5fe7c387f1a28b1793',
    'out' => 'inventory/review/placerita-gold-discovery-loader-dry-run-2026-10-05.md',
    'title' => 'The Placerita Gold Discovery',
    'eventDate' => 'March 9, 1842', 'eventDateEdtf' => '1842-03-09',
    'advisory' => false,
    'featuredImage' => null,
    'linkHeldPhotos' => false,
    'terms' => [
        18834 => ['Francisco Lopez'], 309 => ['Stearns'], 293 => ['Ygnacio del Valle'],
        16446 => ['place of San Francisco'], 18565 => ['Placerita Canyon'],
    ],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
