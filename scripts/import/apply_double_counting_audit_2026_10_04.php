/**
 * The double-counting audit's remaining findings (inventory/review/double-counting-audit-2026-10-04.md), worked
 * (Nathan, 4 October 2026: "The double-counting audit's 27 findings ... work the rest").
 * Already done before this script, checked against the live text on 4 October: 1, 2 and 6 (attributed, the sum noted,
 * by Nathan's ruling of 3 October in fix_own_text_figures_2026_10_04.php, which this script does not reverse: the del
 * Valle figures and Beale's $5,000 stay, attributed); 11 and 12 (Tataviam, held pending consultation); 14, 15 for Klajic
 * and Hon, and 25 (the 1998 edition) in apply_audit_decisions_2026_10_04.php.
 * Here: 3, 4 (the troops and the depth, attributed), 5, 7, 8, 9, 10, 13, 15 (Heidt), 16 to 24, 26 and 27, on the
 * audit's drafts, cut back to what each record's own notes support. Where the audit's draft brought in a source the
 * record does not cite (the Star's $16,000 to $18,000 on Beale's Cut, held as live error CE52; the toll road's end about
 * 1884), it is left out. A fact taken out is said in an editor's note. #16347 drops the site's name from its body and
 * note, as the Tataviam material is held pending consultation (TATAVIAM_AUDIT.md).
 * Each change is an exact replacement; a record whose text has moved is refused, not guessed at. Footnote and editor-note
 * rows are rewritten with handle keys only (ERRORLOG, 4 October).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/apply_double_counting_audit_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$el = Craft::$app->getElements();
$R = "Jerry Reynolds";
// [finding, record, old body text, new body text]
$B = [
 [3, 16446, 'held the western Santa Clarita Valley: 48,829 acres, with part of Ventura County as far as Piru.', 'held the western Santa Clarita Valley: eleven square leagues, with part of Ventura County as far as Piru, patented in 1875 at 48,611.88 acres.[5]'],
 [4, 932, "Andrés Pico began improving the road over the pass in the winter of 1862-63, but floods washed the work out. Edward F. Beale took over Pico's franchise from the Los Angeles supervisors, with, by Jerry Reynolds's account, five thousand dollars to do the work, and called out troops from Fort Tejon to dig a ninety-foot slash through the mountain with picks and shovels. On September 19, 1863, he lent two thousand dollars to A.A. Hudson and Oliver P. Robbins, who built a toll house below the cut, and the pass was a toll road for twenty-one years, until it reverted to the county.[1]",
   "Jerry Reynolds tells the story of the cut: that Andrés Pico's work on the road over the pass was washed out by floods in the winter of 1862-63; that Edward F. Beale took over Pico's franchise from the Los Angeles supervisors, with five thousand dollars to do the work, and called out troops from Fort Tejon to dig a ninety-foot slash through the mountain with picks and shovels; and that on September 19, 1863, Beale lent two thousand dollars to A.A. Hudson and Oliver P. Robbins, who built a toll house below the cut, and the pass was a toll road for twenty-one years, until it reverted to the county.[1]"],
 [7, 327, 'He was superintendent of Indian affairs for California and Nevada from 1853,', 'He was superintendent of Indian affairs for California from 1853,'],
 [8, 327, 'He bought Rancho La Liebre on August 8, 1855, and added', "He bought Rancho La Liebre in 1855, on August 8 by Reynolds's date, and added"],
 [9, 287, "In the spring of 1772 six of his soldiers deserted, and Fages went after them by way of the Mojave River and the Antelope Valley, coming down through the Sierra Pelona to the head of the Santa Clara River. His first camp in the valley was probably near Agua Dulce Springs, which he named for its sweet water. Where Castaic Creek joins the Santa Clara, the spot where he had stood with Portolá and Crespí three years before, the chief of the Tataviam community led him to believe the men were up Castaic Canyon. After two days' rest the party climbed the canyon, camping at the Cienaga where Fish Creek joins Castaic and at Laguna, which Jerry Reynolds identifies as Lake Elizabeth, and crossed Tejon Pass, which Fages called the Cañada de las Uvas for its wild grapes. He never found the deserters.[1]",
   "In 1772 he came back after deserters.[2] Jerry Reynolds tells the pursuit in detail: six soldiers gone, a route by the Mojave River and the Antelope Valley and down through the Sierra Pelona, a first camp probably near Agua Dulce Springs, which Fages named for its sweet water, and, on the word of the chief of the Tataviam community where Castaic Creek joins the Santa Clara, a climb up Castaic Canyon by the Cienaga and by Laguna, which Reynolds identifies as Lake Elizabeth, to Tejon Pass, which Fages called the Cañada de las Uvas for its wild grapes. He never found the deserters.[1]"],
 [10, 287, 'Born about 1734, a Catalonian, he had led the twenty-five Catalonian soldiers on the 1769 march; he later fought Apaches on the Sonoran frontier, served again as governor until 1791, and died in 1796.[1]',
   "A Catalonian, he had led the Catalonian Volunteers on the 1769 march, twenty-five of them by Jerry Reynolds's count; he later fought Apaches on the Sonoran frontier and served again as governor until 1791.[1]"],
 [13, 938, 'His soldiers included the twenty-five Catalonian Volunteers under Lieutenant Pedro Fages.[1][4]', "His soldiers included the Catalonian Volunteers under Lieutenant Pedro Fages, twenty-five of them by Jerry Reynolds's count.[4]"],
 [13, 938, 'leaving Father Junípero Serra behind. His soldiers', 'leaving Father Junípero Serra behind.[1][2] His soldiers'],
 [15, 15737, 'Jerry Reynolds counts her among the three cityhood leaders', 'the 1998 History of the Santa Clarita Valley counts her among the three cityhood leaders'],
 [17, 20226, 'William Wirt Jenkins was a California Ranger and later a county undersheriff, and from 1878 ranched on Castaic Creek.[1]', "William Wirt Jenkins was a California Ranger.[2] Jerry Reynolds adds that he was later a county undersheriff and ranched on Castaic Creek from 1878.[1]"],
 [18, 20224, 'born in Machias, Maine, on 20 November 1831.[2]', "born in Machias, Maine, in 1831, on 20 November by Jerry Reynolds's date.[2]"],
 [19, 311, 'Thomas O. Larkin, the American consul at Monterey, wrote to the New York Sun that', 'A.B. Perkins wrote that Thomas O. Larkin, the American consul at Monterey, told the New York Sun that'],
 [20, 948, 'Manly, twenty-nine, and Rogers, twenty-two, had set out on November 4 with a canteen of water and some jerky.', "Manly and Rogers had set out on November 4, by Jerry Reynolds's account with a canteen of water and some jerky; he gives their ages as twenty-nine and twenty-two."],
 [21, 315, 'who guided John C. Frémont on his expeditions to the Far West; the press called Frémont "the Pathfinder," though Carson found most of the paths.[1][2]', 'who guided John C. Frémont, "the Pathfinder," on his expeditions to the Far West.[1][2]'],
 [21, 307, 'he was an Army explorer the press called "the Pathfinder," though Kit Carson, his guide, found most of the paths, and he was', 'he was an Army explorer the press called "the Pathfinder," with Kit Carson as his guide, and he was'],
 [22, 307, 'On January 9, 1847, Frémont and his hundred-man "buckskin battalion" reached Castaic Junction from the north and probably stopped overnight at the del Valle ranch house.', 'By the account Leon Worden and Jerry Reynolds both give, in the same words, Frémont and his hundred-man "buckskin battalion" reached Castaic Junction from the north on January 9, 1847, and probably stopped overnight at the del Valle ranch house.'],
 [23, 291, 'the grant that took in most of the Santa Clarita Valley. He died on the rancho two years later without a will, and his heirs held it in undivided shares until it was partitioned in 1870.[1][2]', 'the grant that took in most of the Santa Clarita Valley.[1][2] He died on the rancho two years later without a will, and his heirs held it in undivided shares until it was partitioned in 1870.[1]'],
 [23, 291, 'His birth year is given as 1788: Reynolds has him forty-six in 1834 and fifty-three when he died.[2]', "Jerry Reynolds's ages for him, forty-six in 1834 and fifty-three at his death, would put his birth about 1788.[2]"],
 [24, 297, "Father Juan Crespí's diary is the first written account of the Santa Clarita Valley and its people.", "Father Juan Crespí's diary, with Miguel Costansó's, is among the earliest written accounts of the Santa Clarita Valley and its people."],
 [26, 323, 'Jerry Reynolds quotes the letter in his history of this valley; the drive reached San Jose on July 12, and', 'Jerry Reynolds quotes the letter in his history of this valley and has the drive reaching San Jose on July 12;'],
 [27, 16347, "Archaeologists from CSUN and UCLA recovered 70 items from Elderberry Canyon, in the Castaic Reservoir area, in 1970, and much of the material from the valley's prehistoric sites went into the university's collections.[2][3]", "Jerry Reynolds, who ran the Castaic Lake visitors center, recorded 70 items recovered by archaeologists from CSUN and UCLA in the Castaic Reservoir area in 1970, and much of the material from the valley's prehistoric sites went into the university's collections.[2][3]"],
];
// [finding, record, old note text, new note text] on footnotes; null old = append as the next note
$N = [
 [3, 16446, null, 'United States patent to Rancho San Francisco, 12 February 1875, 48,611.88 acres; Land Case 303 SD, Bancroft Library, as cited on Antonio del Valle\'s record (person #291, note 4). A.B. Perkins (note 1) gives the grant as eleven leagues. The 48,829 acres sometimes given is Jerry Reynolds\'s figure for the grant\'s nominal size.'],
 [27, 16347, '"consists of 70 items recovered from Elderberry Canyon by archaeologists from UCLA and CSUN in 1970."', '"consists of 70 items recovered ... by archaeologists from UCLA and CSUN in 1970." The site\'s name is left out while the archive\'s Tataviam material awaits consultation with the tribe.'],
];
// [finding, record, heading, note] appended to editorNotes
$E = [
 [7, 327, 'Indian affairs', 'This record formerly called him superintendent of Indian affairs for California and Nevada, as the Air Force biography (note 1) does. His appointment of 1853 was for California; Nevada was not a separate jurisdiction until 1861.'],
 [10, 287, 'His birth and death', 'This record formerly gave his birth as about 1734 and his death as 1796. Neither is in the source cited here, and the year of his death is given differently elsewhere; no source held in the archive has been found for either (searched 4 October 2026: the chapters of Jerry Reynolds\'s history and Leon Worden\'s landmark page cited here).'],
];
$OCC = [17, 20226, 'California Ranger; undersheriff; rancher', "California Ranger; rancher; undersheriff, by Jerry Reynolds's account"];
$EDS = [16, 376, 'The Signal was founded on February 7, 1919, as The Newhall Signal, not in 2019 as this record gave it (note 1).', "The Signal was founded as The Newhall Signal in 1919, on February 7 by Jerry Reynolds's date, not in 2019 as this record gave it (note 1)."];

$rows = fn($rs, $keys) => array_map(fn($r) => array_combine($keys, array_map(fn($k) => (string)($r[$k] ?? ''), $keys)), iterator_to_array($rs ?? []));
$plan = []; $bad = [];
$get = function ($id) use (&$plan) { if (!isset($plan[$id])) { $e = Entry::find()->id($id)->status(null)->one(); $plan[$id] = ['e' => $e, 'body' => (string)$e->body, 'fn' => $rows_ = null, 'ch' => []]; } return $plan[$id]; };
foreach ($B as [$f, $id, $old, $new]) { $get($id); $b = $plan[$id]['body'];
  if (str_contains($b, $new)) { echo "F$f #$id done already\n"; continue; }
  if (substr_count($b, $old) !== 1) { $bad[] = "F$f #$id: text not found once"; continue; }
  $plan[$id]['body'] = str_replace($old, $new, $b); $plan[$id]['ch'][] = "F$f body"; }
foreach ($N as [$f, $id, $old, $new]) { $get($id); $fn = $plan[$id]['fn'] ?? $rows($plan[$id]['e']->footnotes, ['number', 'note', 'source']);
  $all = implode("\n", array_column($fn, 'note'));
  if (str_contains($all, $new)) { echo "F$f #$id note done already\n"; continue; }
  if ($old === null) { $fn[] = ['number' => (string)(count($fn) + 1), 'note' => $new, 'source' => 'editorial-2026']; }
  else { $hit = 0; foreach ($fn as &$r) { if (str_contains($r['note'], $old)) { $r['note'] = str_replace($old, $new, $r['note']); $hit++; } } unset($r); if ($hit !== 1) { $bad[] = "F$f #$id: note text not found once"; continue; } }
  $plan[$id]['fn'] = $fn; $plan[$id]['ch'][] = "F$f note"; }
foreach ($E as [$f, $id, $h, $note]) { $get($id); $en = $plan[$id]['en'] ?? array_values(array_filter($rows($plan[$id]['e']->editorNotes, ['heading', 'note', 'position']), fn($r) => $r['note'] !== ''));
  if (in_array($note, array_column($en, 'note'), true)) { echo "F$f #$id editor note done already\n"; continue; }
  $en[] = ['heading' => $h, 'note' => $note, 'position' => 'bottom']; $plan[$id]['en'] = $en; $plan[$id]['ch'][] = "F$f editor note"; }
[$f, $id, $old, $new] = $EDS; $get($id); $en = array_values(array_filter($rows($plan[$id]['e']->editorNotes, ['heading', 'note', 'position']), fn($r) => $r['note'] !== ''));
if (in_array($new, array_column($en, 'note'), true)) { echo "F$f #$id done already\n"; } elseif (count(array_keys(array_column($en, 'note'), $old, true)) === 1) { foreach ($en as &$r) { if ($r['note'] === $old) { $r['note'] = $new; } } unset($r); $plan[$id]['en'] = $en; $plan[$id]['ch'][] = "F$f editor note"; } else { $bad[] = "F$f #$id: editor note not found"; }
[$f, $id, $old, $new] = $OCC; $get($id); $occ = (string)$plan[$id]['e']->occupation;
if ($occ === $new) { echo "F$f #$id occupation done already\n"; } elseif ($occ === $old) { $plan[$id]['occ'] = $new; $plan[$id]['ch'][] = "F$f occupation"; } else { $bad[] = "F$f #$id: occupation is '$occ'"; }
foreach ($plan as $id => $p) { if ($p['ch']) { echo "#$id {$p['e']->title}: " . implode(', ', $p['ch']) . PHP_EOL; } }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0;
foreach ($plan as $id => $p) { if (!$p['ch']) { continue; } $e = $p['e'];
  $e->setFieldValue('body', $p['body']);
  if (isset($p['fn'])) { $e->setFieldValue('footnotes', $p['fn']); }
  if (isset($p['en'])) { $e->setFieldValue('editorNotes', $p['en']); }
  if (isset($p['occ'])) { $e->setFieldValue('occupation', $p['occ']); }
  if (!$el->saveElement($e)) { throw new \RuntimeException("#$id " . json_encode($e->getFirstErrors())); } $n++;
  $back = Entry::find()->id($id)->status(null)->one(); if ((string)$back->body !== $p['body']) { throw new \RuntimeException("#$id body not read back"); } }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('apply_double_counting_audit_2026_10_04.php', $n, 'verified', 'double-counting audit: findings 3, 4, 5, 7-10, 13, 15 (Heidt), 16-24, 26, 27 worked; 1, 2, 6, 11, 12, 14, 15, 25 already done');
echo "done: $n records" . PHP_EOL;
