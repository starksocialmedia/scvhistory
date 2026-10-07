/**
 * The Sylmar Earthquake of February 9, 1971: the event record, from inventory/review/sylmar-earthquake-1971-draft-2026-10-06.json.
 * THE DRAFT IS NOT YET APPROVED BY NATHAN. Every quotation in it was checked word for word against the mirror page or
 * Craft record it cites on 6 October 2026 (73 PASS, none corrected, none failed). The draft's SHA-256 is pinned below:
 * an edit after the check refuses the run until it is checked again.
 *
 * Creates the event: February 9, 1971, startEvidence contemporary (the wire photographs captioned that day), body,
 * 17 notes, a content advisory (top) and three bottom editor notes (the time and magnitude, the college library, the
 * photographs), five dated rows, research leads, era, period, Sand Canyon and Newhall. No theme: none fits (forNathan 3).
 * featuredImage asset 11610 (lw3158_large.jpg, the Newhall Pass collapse, in archiveMedia; forNathan 2).
 * Relations only where a footnote ties and names them (the terms below). sourceDocuments: none, the notes cite no
 * document record (the script still reads the layout and reports). Photographs take the event in photoEvents where a
 * cited note names them (nine); LW2547a, the 1973 album cover, is held. relatedEvents (the Northridge Earthquake) is
 * not set. Nothing is written to any other record.
 * The shared steps are in _event_from_draft_2026_10_06.php. Writes inventory/review/sylmar-earthquake-1971-loader-dry-run-2026-10-06.md.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_sylmar_earthquake_1971_event_2026_10_06.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$CFG = [
    'script' => 'create_sylmar_earthquake_1971_event_2026_10_06.php',
    'v2' => 'inventory/review/sylmar-earthquake-1971-draft-2026-10-06.json',
    'sha' => '3e201a163fd695163ab201fe91831457bac853d379439d5d756e15977a9531dd',
    'out' => 'inventory/review/sylmar-earthquake-1971-loader-dry-run-2026-10-06.md',
    'provenance' => 'the draft of 6 October 2026, written in the v2 shape with every quotation checked word for word that day. Nathan approved it for applying on 6 October 2026.',
    'title' => 'Sylmar Earthquake',
    'eventDate' => 'February 9, 1971', 'eventDateEdtf' => '1971-02-09',
    'advisory' => true,
    'featuredImage' => [11610, 'lw3158_large.jpg', 'LW3158, UPI Telephoto: the westbound Interstate 210 overpass fallen onto Interstate 5 in the Newhall Pass, February 9, 1971 (in archiveMedia; not attached to photograph #4973)'],
    'linkHeldPhotos' => false,
    /* The reviewed ties: a relation is made only when one of its footnotes contains one of these. */
    'terms' => [
        934 => ['Newhall Pass'], 380 => ['Henry Mayo Newhall Memorial Hospital'], 29850 => ['College of the Canyons'],
    ],
];
$run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
$run($CFG, $APPLY);
