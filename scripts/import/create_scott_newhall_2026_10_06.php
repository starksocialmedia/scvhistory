/**
 * Scott Newhall (Nathan, 6 October 2026: "Scott Newhall has no record and should ... connect all the Newhalls properly").
 * From inventory/review/scott-newhall-draft-2026-10-06.md; the tree is in inventory/review/newhall-families-dry-run-2026-10-06.md.
 *
 * What it does:
 *   1. Creates the person record Scott Newhall (1914 to 1992): dates, the Chronicle, The Signal from 1963, the Citizen,
 *      Newhall Land director, with an editorial-2026 body and numbered footnotes quoting each source.
 *   2. spouseOf Scott Newhall <-> Ruth Newhall (#15477), both directions, as the data model stores spouses. Both are dead on
 *      the record, so the link passes the historical test and publishes.
 *   3. personOrganizations: The Santa Clarita Valley Signal (#376) and Newhall Land and Farming Company (#15691).
 *   4. Adds him to photoPeople on #4583 "Scott & Ruth Newhall Prepare to Sail Around World, 1936", whose caption names him.
 *   5. Only when $CREATE_LINE = true (Nathan decides): creates Edwin White Newhall (1856 to 1915) and Almer Mayo Newhall
 *      (1881 to 1933) and stores childOf Scott -> Almer -> Edwin -> Henry Mayo Newhall (#283). All four are dead, so every link
 *      publishes. Without them the descent is stated in Scott's body text and footnotes only, and no childOf is stored:
 *      the archive does not link Scott to #283 directly, because he is not Henry Mayo Newhall's child.
 *
 * No relation is made to any living person (his sons Skip, Tony and Jon have no records and get none here).
 * Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_scott_newhall_2026_10_06.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$CREATE_LINE = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ($CREATE_LINE ? ' (with the Edwin and Almer line)' : ' (Scott only; $CREATE_LINE is off)') . PHP_EOL;
$root = \Craft::getAlias('@root'); $el = Craft::$app->getElements(); $n = 0;

$expect = [283 => 'Henry Mayo Newhall', 15477 => 'Ruth Newhall', 376 => 'The Santa Clarita Valley Signal', 15691 => 'Newhall Land and Farming Company', 4583 => 'Scott & Ruth Newhall Prepare to Sail Around World, 1936.'];
$E = [];
foreach ($expect as $id => $t) { $E[$id] = Entry::find()->id($id)->status(null)->one(); if ($E[$id]?->title !== $t) { throw new \RuntimeException("#$id is not \"$t\""); } }
$sec = Craft::$app->getEntries()->getSectionByHandle('persons');
$fields = fn(Entry $e) => array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
$fn = fn(array $notes) => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($notes), $notes);

/* ---------------------------------------------------------------- the record */
$M = 'SCVHistory.com';
$FN = [
  $M . ', "Scott Newhall with Sons Skip, Tony, Jon and Stinson Beach, 1944," TN4401, /scvhistory/tn4401.htm (Leon Worden): "The fourth-generation San Franciscan and great-grandson of merchant-turned-land baron Henry Mayo Newhall"; "Scott was born Jan. 21, 1914, in San Francisco and joined the staff of the Chronicle in 1934 as a summer replacement photographer"; "When Scott Newhall became executive editor of the San Francisco Chronicle in 1952"; "In 1963 ... Scott and Ruth purchased it"; "Scott and Ruth rented, then purchased the Warring mansion in Piru in 1968"; "In 1971 he ran for mayor of San Francisco"; "acute pancreatitis landed Scott at Henry Mayo Newhall Memorial Hospital in Valencia, where he died Oct. 26 of that year. He was 78."',
  $M . ', "Scott Newhall: A Newspaper Editor\'s Voyage (Oral History, 1988-1989)," UC8901, /scvhistory/uc8901.htm, introduction: "They\'d purchased the paper in 1963 and stayed on after they sold a controlling interest in 1978 to the Morris Newspaper Corp."; "The minute their 10-year noncompete agreement expired in 1988, they marched down the street (Valencia Boulevard) and launched a rival newspaper, the thrice-weekly Santa Clarita Valley Citizen"; "The Citizen lasted less than nine months (September 11, 1988 - May 3, 1989)".',
  '"The Life and Times of Scott Newhall, California Publishing\'s Brilliant Barnum," California Business, July 1989, as carried on ' . $M . ', /scvhistory/califbusiness1989july.htm: "as his newspaper grew from a small weekly to a 42,000-circulation daily"; "Family members still own 38 percent of the company, including a small stake held by Scott Newhall, a Newhall Land director"; "in 1952 he was named Chronicle editor"; "in November 1963 ... he acquired The Signal for $60,000".',
  '"Scott Newhall Buys The Signal," The Newhall Signal and Saugus Enterprise, October 31, 1963, as carried on ' . $M . ', /scvhistory/sg19631031scottnewhall.htm: "Scott Newhall, executive editor of the San Francisco Chronicle and great-grandson of the man for whom the town of Newhall was named, has purchased the Newhall Signal"; "The transaction ... will become effective November 1"; "He began his career as a photographer on the San Francisco Chronicle in 1935 and prior to becoming executive editor in 1952".',
  'Patricia Farrell Aidem, "Ruth Newhall dies at 93," L.A. Daily News, November 25, 2003, obituary #28051 in this archive: "Scott, who died in 1992, was the great-grandson of pioneer Henry Mayo Newhall and a member of the Newhall Land board."',
  'Scott Newhall, interviewed by Suzanne B. Riess, "A Newspaper Editor\'s Voyage Across San Francisco Bay: San Francisco Chronicle, 1935-1971, and Other Adventures," Regional Oral History Office, The Bancroft Library, University of California, Berkeley, 1988-1989, as carried on ' . $M . ' (UC8901, /scvhistory/files/uc8901/uc8901.pdf). Page 1: "The survivor was my grandfather, Edwin Newhall"; "My grandfather had gone east to school ... and had married Fanny Hall ... She died in childbirth when my father was born." Page 4: "My great-grandfather on my father\'s side, Henry Mayo Newhall". Page 318: "And my father Almer Newhall was the result of that. And that\'s where I come from. Fanny Hall Newhall died in childbirth when my father was born."',
  $M . ', "Colma Cemeteries: Henry Mayo Newhall & Heirs," LW3325, /scvhistory/lw3325.htm (Leon Worden, 2018; photographs 31 October 2015), "Newhall family members interred at Cypress Lawn": "(4) Scott Newhall 21 Jan 1914 - 26 Oct 1992 Son of Almer Mayo Newhall and Anna Nicholson Scott Newhall Husband of Ruth Waldo Newhall"; "(3) Almer Mayo Newhall 14 May 1881 - 14 Jan 1933 Son Edwin White Newhall and Fannie Silliman Hall Newhall"; "(2) Edwin White Newhall 7 May 1856 - 28 Oct 1915 Son of Henry Mayo Newhall and Sarah Ann White Newhall".',
  'Wire photograph caption, April 6, 1936, "Scott & Ruth Newhall Prepare to Sail Around World, 1936," photograph #4583 in this archive (LW2849): "The seafaring pair are Scott Newhall, 23-year-old photographer, and his wife Ruth, 24. He is a son of the late Almer M. Nehwall, San Francisco Chamber of Commerce president." ("Nehwall" as printed.)',
  $M . ', "HMN Grandson Almer M. Newhall Makes First Long-Distance Call from S.F. to Buenos Aires, 1930," /scvhistory/lw2915.htm: "Almer M. Newhall (1881-1933) was a grandson of town founder Henry Mayo Newhall, father of San Francisco and Santa Clarita Valley newspaperman Scott Newhall".',
  '"Vigilance Forever: Our 75th, The Signal 1919-1994," The Signal anniversary edition, 1994, p. 4, as carried on ' . $M . ', /scvhistory/files/sg_vigilanceforever1994/files/basic-html/page4.html, under OWNERS: "Ray Brooks (1963) Scott Newhall (1963-1978) Charles Morris (1978-present)". Its list of publishers gives Scott Newhall 1963 to 1977 and Tony Newhall 1977 to 1988.',
];
$BODY = 'Scott Newhall owned The Newhall Signal and Saugus Enterprise from 1963 to 1978 and, with his wife, Ruth Newhall, ran it until 1988, while it grew from a small weekly into a daily known for his front-page editorials.[10][2][1][3] He bought it, effective November 1, 1963, while executive editor of the San Francisco Chronicle, and in 1968 he and Ruth moved to the Piru Mansion.[4][1] They sold a controlling interest to the Morris Newspaper Corp. in 1978 and stayed on as editors; when their ten-year noncompete expired in 1988 they started the Santa Clarita Valley Citizen, which closed on May 3, 1989.[2] He was a director of The Newhall Land and Farming Company.[3][5]

He was a great-grandson of Henry Mayo Newhall, the town\'s founder. His father was Almer Mayo Newhall, and his grandfather was Edwin White Newhall, Henry Mayo Newhall\'s son by his first wife, Sarah Ann White.[6][7][8][9] Born in San Francisco on January 21, 1914, he joined the Chronicle as a photographer, in 1934 by one account and 1935 by another, and was its executive editor from 1952; in 1971 he ran for mayor of San Francisco.[1][4] He died on October 26, 1992, at Henry Mayo Newhall Memorial Hospital in Valencia.[1]';
$NOTE = 'Not to be confused with Walter Scott Newhall (1860 to 1906), Henry Mayo Newhall\'s fourth son and Scott Newhall\'s great-uncle, or with Walter Scott Newhall (1908 to 1960), a grandson of Henry Mayo Newhall and director of Newhall Land from 1951 (SCVHistory.com LW3325 and RN0114). Neither has a record.';

$p = Entry::find()->section('persons')->status(null)->title('Scott Newhall')->one();
echo 'Scott Newhall: ' . ($p ? "exists #{$p->id}" : 'create') . PHP_EOL . '  ' . str_replace("\n\n", "\n  ", $BODY) . PHP_EOL;
foreach ($FN as $i => $t) { echo '  [' . ($i + 1) . '] ' . mb_substr($t, 0, 140) . '...' . PHP_EOL; }
if ($APPLY && !$p) {
  $p = new Entry(); $p->sectionId = $sec->id; $p->setTypeId($sec->getEntryTypes()[0]->id); $p->slug = 'scott-newhall';
  $p->setFieldValues(['fullName' => 'Scott Newhall', 'birthDate' => 'January 21, 1914', 'birthDateEdtf' => '1914-01-21', 'birthEvidence' => 'retrospective',
    'deathDate' => 'October 26, 1992', 'deathDateEdtf' => '1992-10-26', 'deathEvidence' => 'retrospective', 'birthplace' => 'San Francisco, California',
    'burialPlace' => 'Cypress Lawn Memorial Park, Colma, California', 'burialEvidence' => 'retrospective',
    'occupation' => 'Newspaper editor; publisher', 'body' => $BODY, 'bodyAuthorship' => 'editorial-2026', 'footnotes' => $fn($FN),
    'editorNotes' => [['heading' => 'Not to be confused with', 'note' => $NOTE, 'position' => 'bottom']],
    'historicalEra' => [167], 'personOrganizations' => [376, 15691],
    'recordProvenance' => 'create_scott_newhall_2026_10_06.php, 6 October 2026']);
  if (!$el->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); } $n++;
}
echo '  era: Incorporation Struggle (#167), primary; personOrganizations: #376 The Santa Clarita Valley Signal, #15691 Newhall Land and Farming Company' . PHP_EOL;

/* ------------------------------------------------------------ spouse, both ways */
$addRel = function (?Entry $from, string $h, ?Entry $to, string $label, string $fromName = '', string $toName = '') use ($APPLY, $el, &$n, $fields) {
  if (!$from || !$to) { echo "  $h " . ($from?->title ?? "$fromName [new]") . ' -> ' . ($to?->title ?? "$toName [new]") . "   [$label]  would add" . PHP_EOL; return; }
  if (!in_array($h, $fields($from), true)) { throw new \RuntimeException("{$from->title} has no $h"); }
  $ids = $from->getFieldValue($h)->status(null)->ids();
  echo "  $h {$from->title} -> {$to->title}   [$label]  " . (in_array($to->id, $ids, true) ? 'present' : 'add') . PHP_EOL;
  if ($APPLY && !in_array($to->id, $ids, true)) { $from->setFieldValue($h, array_merge($ids, [$to->id])); if (!$el->saveElement($from)) { throw new \RuntimeException("$h {$from->title}"); } $n++; }
};
echo 'Relations:' . PHP_EOL;
$addRel($p, 'spouseOf', $E[15477], 'LW3325; Ruth Newhall #15477 footnotes; both dead', 'Scott Newhall');
$addRel($E[15477], 'spouseOf', $p, 'the same, stored from her side', '', 'Scott Newhall');

/* ---------------------------------------------------------- the 1936 photograph */
$ph = $E[4583]; $pp = $ph->photoPeople->status(null)->ids();
echo '#4583 photoPeople: ' . ($p && in_array($p->id, $pp, true) ? 'Scott present' : 'add Scott Newhall') . PHP_EOL;
if ($APPLY && $p && !in_array($p->id, $pp, true)) { $ph->setFieldValue('photoPeople', array_merge($pp, [$p->id])); if (!$el->saveElement($ph)) { throw new \RuntimeException('#4583'); } $n++; }

/* ----------------------------------------------- the line to Henry Mayo Newhall */
$LINE = [
  'Edwin White Newhall' => ['slug' => 'edwin-white-newhall', 'b' => ['May 7, 1856', '1856-05-07'], 'd' => ['October 28, 1915', '1915-10-28'], 'occ' => 'Incorporator, The Newhall Land and Farming Company',
    'body' => 'Edwin White Newhall was the third of Henry Mayo Newhall\'s five sons, and the last of the three born to his first wife, Sarah Ann White.[1][2] With his brothers he incorporated The Newhall Land and Farming Company on June 1, 1883, the year after their father\'s death.[3] His first wife, Fannie Silliman Hall, died after the birth of their son, Almer Mayo Newhall; he then married Virginia Whiting.[1][2] He died on October 28, 1915.[1]',
    'fn' => [$FN[6] . ' Also: "(2) Edwin White Newhall 7 May 1856 - 28 Oct 1915 Son of Henry Mayo Newhall and Sarah Ann White Newhall Husband of (1) Fannie Silliman Hall Newhall and (2) Virginia Whiting Newhall."',
      $M . ', "Stock Certificate: California Bank (of Los Angeles), Signed by H.G. Newhall," LW3564, /scvhistory/lw3564.htm: "about two decades later, Almer Hall\'s daughter, Fannie Silliman Hall, married Henry Newhall\'s third son, Edwin White Newhall. The latter marriage ended in tragedy when Fannie died of puerperal fever ... three days after giving birth to their only child together, Almer Mayo Newhall."',
      $M . ', "Henry Mayo Newhall\'s 5 Sons," RN7301, /scvhistory/rn7301.htm: "Henry Mayo Newhall\'s five sons, photographed in 1873"; "All were at least 21 years old when they incorporated The Newhall Land and Farming Co. in 1883"; and Henry Mayo Newhall #283: "On June 1, 1883, H.M. Newhall\'s five sons incorporated the family-owned Newhall Land and Farming Company."'],
    'parent' => 283],
  'Almer Mayo Newhall' => ['slug' => 'almer-mayo-newhall', 'b' => ['May 14, 1881', '1881-05-14'], 'd' => ['January 14, 1933', '1933-01-14'], 'occ' => 'Businessman; officer, The Newhall Land and Farming Company',
    'body' => 'Almer Mayo Newhall, a grandson of Henry Mayo Newhall, was the son of Edwin White Newhall and Fannie Silliman Hall, who died three days after his birth.[1][2] At the time of the St. Francis Dam disaster in 1928 he was assistant to the president of The Newhall Land and Farming Company, his cousin George A. Newhall Jr., and in 1930, as president of the San Francisco Chamber of Commerce, he placed the first long-distance call from San Francisco to Buenos Aires.[3] He married Anna Nicholson Scott; their sons included Scott Newhall, later owner of The Signal.[1][3] He died on January 14, 1933.[1]',
    'fn' => [$FN[6] . ' Also: "(3) Almer Mayo Newhall 14 May 1881 - 14 Jan 1933 Son Edwin White Newhall and Fannie Silliman Hall Newhall Husband of Anna Nicholson Scott Newhall."',
      'LW3564 (as note 2 on Edwin White Newhall\'s record), /scvhistory/lw3564.htm: "Fannie died of puerperal fever ... three days after giving birth to their only child together, Almer Mayo Newhall. ... Almer Mayo was the father of 20th-century Signal newspaper editor Scott Newhall."',
      $FN[8] . ' And: "Two years earlier, at the time of the 1928 St. Francis Dam Disaster, Almer was assistant to the president of The Newhall Land and Farming Co., his cousin George A. Newhall Jr. (1904-1958)"; "Almer M. Newhall, president of the San Francisco Chamber of Commerce, places the first long-distance phone call from San Francisco to Buenos Aires, Argentina, April 1930."'],
    'parent' => 'Edwin White Newhall'],
];
$made = [283 => $E[283]];
echo PHP_EOL . 'The line (' . ($CREATE_LINE ? 'on' : 'off: listed, not written') . '):' . PHP_EOL;
foreach ($LINE as $t => $d) {
  $x = Entry::find()->section('persons')->status(null)->title($t)->one();
  echo "  $t ({$d['b'][1]} to {$d['d'][1]}): " . ($x ? "exists #{$x->id}" : 'create') . PHP_EOL;
  if ($APPLY && $CREATE_LINE && !$x) {
    $x = new Entry(); $x->sectionId = $sec->id; $x->setTypeId($sec->getEntryTypes()[0]->id); $x->slug = $d['slug'];
    $x->setFieldValues(['fullName' => $t, 'birthDate' => $d['b'][0], 'birthDateEdtf' => $d['b'][1], 'birthEvidence' => 'retrospective', 'deathDate' => $d['d'][0], 'deathDateEdtf' => $d['d'][1], 'deathEvidence' => 'retrospective',
      'burialPlace' => 'Cypress Lawn Memorial Park, Colma, California', 'burialEvidence' => 'retrospective', 'occupation' => $d['occ'], 'body' => $d['body'], 'bodyAuthorship' => 'editorial-2026', 'footnotes' => $fn($d['fn']),
      'recordProvenance' => 'create_scott_newhall_2026_10_06.php, 6 October 2026']);
    if (!$el->saveElement($x)) { throw new \RuntimeException(json_encode($x->getFirstErrors())); } $n++;
  }
  $made[$t] = $x;
}
$made['Scott Newhall'] = $p;
foreach ([['Scott Newhall', 'Almer Mayo Newhall', 'oral history p. 318; LW3325; 1936 wire caption; LW2915'], ['Almer Mayo Newhall', 'Edwin White Newhall', 'oral history p. 1; LW3325; LW3564'], ['Edwin White Newhall', 283, 'LW3325; LW3564; RN7301']] as [$c, $par, $src]) {
  $ce = $made[$c] ?? null; $pe = $made[$par] ?? null;
  if (!$CREATE_LINE) { echo "  childOf $c -> " . ($pe?->title ?? $par) . "   [$src]  held until the line is created" . PHP_EOL; continue; }
  $addRel($ce, 'childOf', $pe, $src . '; both dead', $c, is_string($par) ? $par : '');
}
if ($APPLY && $n) { $applyLog = require "$root/scripts/import/_apply_log.php"; $applyLog('create_scott_newhall_2026_10_06.php', $n, 'verified', 'Scott Newhall: record, spouse, Signal, Newhall Land, #4583' . ($CREATE_LINE ? '; Edwin and Almer, childOf line' : '')); }
echo ($APPLY ? "done: $n" : 'nothing written') . PHP_EOL;
