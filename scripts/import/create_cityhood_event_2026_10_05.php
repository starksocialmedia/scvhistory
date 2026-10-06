/**
 * Santa Clarita Cityhood: the event record, from inventory/review/cityhood-draft-v2-2026-10-05.json, the draft Nathan
 * approved on 6 October 2026 ("apply all of them"), with every quotation rechecked word for word that day (v2: five
 * quotations corrected, two ellipses removed; recordDates given their ISO dates). The draft's SHA-256 is pinned below:
 * an edit after the check refuses the run until it is checked again.
 *
 * Creates the event: dates (December 15, 1987; the vote November 3, 1987), startEvidence contemporary, body, 18 notes,
 * three bottom editor notes (no content advisory: the draft has none), seven dated rows, research leads, era, period,
 * theme and the four communities, featuredImage asset 29678 (BW8702, the council-elect, already in Craft).
 * Relations only where a footnote ties and names them (the terms below). sourceDocuments: the seven document records the
 * notes cite (the field is being added to the event type by another step; before it is there the script sets nothing
 * and says so, and a later run fills it). Photographs take the event in photoEvents where a cited note names them;
 * the rest are held. Nothing is written to any other record: the draft's forNathan items (a LAFCO record, Art Donnelly,
 * Louis Garasi, the unattached image files) are not acted on.
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/cityhood-loader-dry-run-2026-10-05.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_cityhood_event_2026_10_05.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_cityhood_event_2026_10_05.php',
    'v2' => 'inventory/review/cityhood-draft-v2-2026-10-05.json',
    'sha' => 'e2bcc5e6d729af9a9fc461035711ad6844b24dc03223f32c2ef51cba26d01dd0',
    'out' => 'inventory/review/cityhood-loader-dry-run-2026-10-05.md',
    'title' => 'Santa Clarita Cityhood',
    'eventDate' => 'December 15, 1987', 'eventDateEdtf' => '1987-12-15',
    'advisory' => false,
    'featuredImage' => [29678, 'bw8702_orig.jpg', 'BW8702, the first City Council-elect, November 1987 (already the image of photograph #29679)'],
    'linkHeldPhotos' => false,
    /* The reviewed ties: a relation is made only when one of its footnotes contains one of these. */
    'terms' => [
        394 => ['City of Santa Clarita'], 396 => ['Santa Clarita Valley Chamber of Commerce'], 28275 => ['Board of Supervisors'],
        16418 => ['Connie Worden'], 15808 => ['Boyer'], 18791 => ['McKeon'], 15737 => ['Heidt'], 16140 => ['Darcy'], 23081 => ['Koontz'],
    ],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
