/**
 * The golden spike at Lang Station: the event record, from inventory/review/golden-spike-draft-v2-2026-10-05.json, the
 * draft Nathan approved on 6 October 2026 ("apply all of them"), with every quotation rechecked word for word that day
 * (v2: no quotation needed correcting; the spike photograph's code corrected to LW2248a, as Craft and the mirror have
 * it). The draft's SHA-256 is pinned below: an edit after the check refuses the run until it is checked again.
 *
 * Creates the event: September 5, 1876, startEvidence contemporary, Landmark No. 590, body, 15 notes, four bottom editor
 * notes (no content advisory: the draft has none), research leads, era, period, theme, Soledad Canyon, and
 * featuredImage asset 1193 (lang.jpg, already Lang Station's image, titled as the 1926 reenactment; forNathan 1).
 * No recordDates: the draft gives none. relatedEvents waits for the San Fernando Tunnel record. Relations only where a
 * footnote ties and names them (the terms below). sourceDocuments: none, the notes cite no document record (the script
 * still reads the layout and reports). Photographs take the event in photoEvents where a cited note names them; the
 * rest are held. Nothing is written to any other record (forNathan 2, the plate's photoDate, is a separate fix).
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/golden-spike-loader-dry-run-2026-10-05.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_golden_spike_event_2026_10_05.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_golden_spike_event_2026_10_05.php',
    'v2' => 'inventory/review/golden-spike-draft-v2-2026-10-05.json',
    'sha' => 'a52a0c89a8ecbd55c4ee539981b3fcef6d624f0f6a4454a5d5211bd86a2abbf5',
    'out' => 'inventory/review/golden-spike-loader-dry-run-2026-10-05.md',
    'title' => 'Golden Spike at Lang Station',
    'eventDate' => 'September 5, 1876', 'eventDateEdtf' => '1876-09-05',
    'advisory' => false,
    'featuredImage' => [1193, 'lang.jpg', 'titled in Craft "William Crocker drives a rail spike at the re-enactment of the Southern Pacific completion at Lang Station, September 1926." (the 1926 reenactment, not the event; already Lang Station\'s image)'],
    'linkHeldPhotos' => false,
    'terms' => [
        609 => ['Lang Station'], 18529 => ['Soledad Canyon'], 16388 => ['Crocker'], 18820 => ['John Lang'],
        16101 => ['Southern Pacific'], 15493 => ['Santa Clarita Valley Historical Society'], 392 => ['Historical Society of Southern California'],
    ],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
