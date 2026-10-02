/**
 * William S. Hart #16356, the archive's biggest subject (Nathan, 2 October 2026:
 * "his profile should be the best thing on the site"). Scope as agreed: thorough
 * on his life in this valley, brief on the film career, pointing outward to the
 * AFI Catalog and the Library of Congress for it.
 *
 * Built from Grok's dossier (inventory/review/william-s-hart-sources.md) and the
 * pages it names, read from the mirror (inventory/legacy/fetched/hart/, all 34
 * matching the manifest of 20 August 2026), the will read from its scan, and the
 * City's release of July 2025. Wikipedia and Britannica were finding aids only.
 *
 * THE CONFLICTS, LAID OUT RATHER THAN SETTLED
 *   Birth: 1880 census age 16 (1864) against the Examiner's 1862, Reynolds's
 *     "around December 6, 1870," and Hart's own "I'll be 64 in December" (1936).
 *   Death: California Lutheran Hospital, Los Angeles, 23 June 1946 (the
 *     Examiner), not Newhall.
 *   Remains: cremated, the ashes to Green-Wood (Herald-Express), not sent east
 *     by train.
 *   The ranch: leased 1918, first lots February 1921, complete 21 October 1933
 *     (Sitton's deed survey), against "254 acres in 1921."
 *   The mansion: "contemplating building" on 12 July 1926; Photoplay's "new ranch
 *     home" in May 1928.
 *   The dam: the Signal's wreath and the boy's burial at Oakwood against the
 *     cowboy-suit legend (Reynolds part 54).
 *   "Rags" and the Lindbergh rumor are left out, with an editor note.
 *
 * ALSO: Wikidata and IMDb IDs; Hart Park, the mansion, Hart High and the Hart
 * district named for him; the park and the mansion as places he lived. Needs
 * add_imdb_field.php and add_named_for_fields.php.
 *
 * Fills an empty body only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_hart_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $H = "$root/inventory/legacy/fetched/hart";
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$get = fn($id) => Entry::find()->id($id)->status(null)->one();
$ID = 16356; $PARK = 15764; $MANSION = 16404; $HIGH = 16052; $DISTRICT = 21588;
$p = $get($ID);
$t = fn($k) => $ws(@file_get_contents("$H/$k.txt"));
$j = json_decode((string)@file_get_contents("$root/inventory/sources/william-s-hart-2026-10-02.json"), true);
$src = ['will' => $ws($j['will']['passage'] ?? ''), 'city' => $ws($j['city2025']['passage'] ?? ''), 'lae' => $t('lae62446a') . ' ' . $t('lae62446c'), 'lahe' => $t('lahe06261946b') . ' ' . $t('lahe06261946c'),
    'fohp' => $t('lw2616'), 'census' => $t('lw3336'), 'r53' => $t('reynolds-part53'), 'r54' => $t('reynolds-part54'), 'r65' => $t('reynolds-part65'), 'lat36' => $t('lat19360719hart'),
    'sitton' => $t('mu8901'), 'kelly' => $t('hartmansion-const'), 'fh2701' => $t('fh2701'), 'photoplay' => $t('lw2271'), 'dam' => $t('sg051703'), 'crowl' => $t('ap1516'), 'contest' => $t('lw2291'), 'perkins58' => $t('perkins-newhall-1958')];
$bad = [];
if (!$p || $p->title !== 'William S. Hart') { $bad[] = '#16356 is not William S. Hart'; }
foreach (['imdbId', 'namedFor'] as $f) { if (!Craft::$app->getFields()->getFieldByHandle($f)) { $bad[] = "field $f missing"; } }
$MUST = [
    'will' => ['consisting of approximately 200 acres of land', 'exclusively as a public park and pleasure grounds', 'a charge or fee shall never be made of the public for admittance', 'shall be established by ordinance to be "WILLIAM S. HART PARK"', 'for the benefit of the American Public of every race and creed', 'development of oil, gas or other hydrocarbon', 'revert, and shall go and be distributed, to the State of California', 'All the domestic animals which I may own at the time of my death shall be allowed to spend their remaining days in the Park', 'One Hundred Fifty Thousand Dollars ($150,000.00)'],
    'city' => ['official opening of its 40th park', 'now under full City ownership'],
    'lae' => ['William S. Hart\'s gallant fighting heart stopped beat', 'at 11:20 o\'clock last night', 'Death came to the veteran Western film star at California Lutheran Hospital', 'He had been hospitalized only since June 5', 'Born in New York in 1862', 'Hart was born in Newburgh, N. Y., December 6, 1862', 'He became a leading man for such stars as Helena Modjeska, Ada Rheahan and Julia Arthur, and in 1914 came West after a triumph in the plays "The Squaw Man" and "The Virginian."', 'He retired in 1925', 'his 80-acre ranch in Newhall', 'since the death of his "darling sister," Mary'],
    'lahe' => ['The services, held in the den of the ranch home', 'Little Church of the Recessional at Forest Lawn', '"The Last Round-up" was sung by Rudy Vallee', 'Tomorrow evening Hart will be cremated', 'laid to rest in Greenwood Cemetery, Brooklyn, beside those of his sister, Mary Ellen'],
    'fohp' => ['Saturday, September 20, 1958', 'Dedication of William S. Hart Park', 'appearing in or producing more than 60 movies over an eleven-year span', 'After completing Tumbleweeds (1925), his final film', 'La Loma de los Vientos (Hill of the Winds)', '"While I was making pictures, the people gave me their nickels, dimes, and quarters. When I am gone, I want them to have my home."', 'William Surrey Hart was born in Newburgh, New York, probably in 1864'],
    'census' => ['William\'s listed age is 16', 'he was born in December of 1864'],
    'r53' => ['Newburgh, New York around December 6, 1870', 'Back in February 1921, Hart had purchased the 254-acre Horseshoe Ranch from Babcock Smith'],
    'r54' => ['Hart dressed him with his own trembling hands in a little co'],
    'r65' => ['It opened in 1945 and was renamed prior to his death'],
    'lat36' => ['"I\'ll be 64 in December.', 'Bill Hart\'s home centers an estate of about 200 acres'],
    'sitton' => ['When William S. Hart first came to Newhall in 1918, he leased property from George Babcock Smith which became the core of the Horseshoe Ranch', 'In February 1921, Hart purchased lots 19 and 20 of Tract 1059 from George Babcock Smith', 'These lots were the site of the Ranchhouse and "log cabin"', 'In July, Hart made two additional purchases', 'From 1925 to 1927 he attempted to purchase all available land surrounding the hill upon which his mansion was built', 'recorded on 21 October 1933', 'With the purchase of these three lots, the Horseshoe Ranch was complete', 'By Tom Sitton. Natural History Museum of Los Angeles County, February 1989'],
    'kelly' => ['July 12, 1926', 'I am contemplating building a Spanish style home on my Ranch at Newhall', 'Miss Hart is an invalid and has to have a large, airy bedroom'],
    'fh2701' => ['Hart Mansion under construction, October(?) 1927', 'the wheelchair-bound Mary Ellen', 'Mary Ellen died in 1943', 'unsuccessfully contested by his estranged son, William Jr.', 'the mansion is a component of the Natural History Museum of Los Angeles County system'],
    'photoplay' => ['Bill Hart\'s new ranch home at Newhall, Calif., escaped in the big dam disaster, being on the very edge of the flood. His ranch home has been used as a center for relief work.', 'May 1928 edition of Photoplay'],
    'dam' => ['"Only those who were in it or near it can realize how tragic it all was. ... At one time there were 78 bodies in the little shack that had been converted into a morgue."', '\'To the little Unknown Soldier, from a Newhall Cowboy.\'', 'That Hart had dressed the boy in a little cowboy outfit is an oft-repeated local legend', 'redirected the boy\'s body to Oakwood Cemetery in Chatsworth', 'By Leon Worden Saturday, May 17, 2003', 'March 29, 1928, Newhall Signal'],
    'crowl' => ['He proposed to donate 3 lots at the corner of Spruce and 11th St.', 'On November 7, 1940', 'fulfilled on May 23, 1941', 'until 1965', 'Bill Crowl, president of the Friends of William S. Hart Park (1999)', 'S. Charles Lee'],
    'contest' => ['Mrs. Winifred Westover Hart, 51, sobs as she testifies', 'the will is under contest in Superior Court'],
    'perkins58' => ['Mary Pickford and William S. Hart filmed "Rags"'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k], $ws($ph))) { $bad[] = "$k does not read \"" . mb_substr($ph, 0, 70) . '"'; } } }
foreach (['lae62446a', 'lahe06261946c', 'mu8901', 'hartmansion-const', 'sg051703', 'ap1516', 'lw2616', 'lw3336', 'reynolds-part53'] as $f) { if (!is_file("$H/$f.txt")) { $bad[] = "$f not fetched"; } }

$BODY = implode("\n\n", [
    'William S. Hart was the leading Western star of the silent screen and, for the last two decades of his life, Newhall\'s most famous resident. He left his ranch there, with his house and everything in it, to the public, and William S. Hart Park and the Hart Museum are the result.[1][2]',
    'He came to films from the stage. The Los Angeles Examiner\'s obituary says he was a leading man to Helena Modjeska and Julia Arthur and made his name in The Squaw Man and The Virginian before coming west in 1914 to make motion pictures; the biography the Friends of Hart Park printed for the park\'s dedication counts more than sixty films in eleven years, ending with Tumbleweeds in 1925.[3][4] That career is documented better elsewhere: in the American Film Institute\'s catalog, at the Library of Congress, which holds the Gatewood W. Dunston Collection of his films and papers, and in IMDb\'s filmography.[5][6]',
    'He was born in Newburgh, New York, on 6 December, and the year is disputed, largely because he shaded it himself. The 1880 census lists him as sixteen, which fits 1864, the year most accounts now give. The Examiner\'s obituary gave 1862; Jerry Reynolds wrote "around December 6, 1870"; and in 1936 Hart told the Los Angeles Times, "I\'ll be 64 in December," which would make it 1872.[7][3][8][9]',
    'He came to Newhall in 1918, leasing the property of George Babcock Smith, which became the core of his Horseshoe Ranch. In February 1921 he bought the first of it, two lots that held the ranch house and a log cabin, and that July he bought two more parcels. From 1925 to 1927 he bought what land he could around the hill where he meant to build, and he added lots in 1932 and 1933; with the last, recorded on 21 October 1933, "the Horseshoe Ranch was complete," in the words of Tom Sitton\'s survey of the deeds for the Natural History Museum.[10] So the often-repeated purchase of a 254-acre ranch from Babcock Smith in 1921 does not fit the deeds: the ranch was assembled over twelve years.[10][8] The archive\'s other figures describe it at other times: "about 200 acres" in 1936, approximately 200 acres in his will of 1944, and an "80-acre ranch" in the Examiner\'s obituary.[9][1][3]',
    'On 12 July 1926 he wrote to the Los Angeles architect Arthur Kelly that he was "contemplating building a Spanish style home on my Ranch at Newhall." The house, which he named La Loma de los Vientos, the Hill of the Winds, was planned around his sister Mary Ellen, who used a wheelchair; Kelly\'s notes say she needed "a large, airy bedroom." It was under construction in 1927, and Photoplay in May 1928 called it his "new ranch home."[11][12][13][4] The photographs and captions in the archive disagree about when it was finished; the letter of July 1926 and Photoplay\'s notice of May 1928 bracket it. Mary Ellen lived there with him until her death in 1943.[12][3]',
    'He took part in the life of the town. After the St. Francis Dam failed on the night of 12 March 1928, Photoplay reported that his ranch home had been used as a center for relief work, and he wrote to his friend Wyatt Earp that "at one time there were 78 bodies in the little shack that had been converted into a morgue."[13][14] The Newhall Signal reported that he laid a wreath for an unidentified small boy, inscribed "To the little Unknown Soldier, from a Newhall Cowboy," and arranged the boy\'s burial. The story that he dressed the boy in a cowboy suit, which Reynolds tells, is a later legend, and the boy, tentatively identified, was buried at Oakwood Cemetery in Chatsworth.[14][15]',
    'In 1940 he gave three lots at Spruce and Eleventh streets, with money to build and furnish a theater, to American Legion Post 507; he signed over the deed on 7 November 1940, and the American, designed by S. Charles Lee, opened on 23 May 1941 and served as the town\'s theater until 1965.[16] The valley\'s first high school, which opened in 1945, was named for him in his lifetime.[17]',
    'He died at California Lutheran Hospital in Los Angeles at 11:20 p.m. on 23 June 1946, having been in the hospital since 5 June; the reference works that give Newhall as the place of his death are mistaken.[3] His funeral was held first in the den of the ranch house and then at the Little Church of the Recessional at Forest Lawn, where Rudy Vallee sang "The Last Round-Up." He was cremated, and his ashes were to be laid in Green-Wood Cemetery in Brooklyn beside those of Mary Ellen; he was not, as one account has it, sent east by train.[18]',
    'His will, made at Newhall on 9 September 1944, left the ranch, "approximately 200 acres," with his home and its contents, to the County of Los Angeles, to be used "exclusively as a public park and pleasure grounds," never with a charge for admission, under the name "WILLIAM S. HART PARK," and with a tablet reading "This Park has been dedicated by WILLIAM S. HART for the benefit of the American Public of every race and creed." It barred oil and gas development, let his animals live out their days in the park, left $150,000 in trust for its upkeep, and provided that if the County ever failed in those conditions the property should go to the State of California.[1] The Friends of Hart Park give his reason in his words: "When I am gone, I want them to have my home."[4] The will was contested, by his son and in 1950 by his former wife, and it stood.[12][19]',
    'The County dedicated William S. Hart Park on 20 September 1958 and ran it for more than six decades, with the mansion part of the Natural History Museum.[4][12] In July 2025 the park passed to the City of Santa Clarita, which opened it as its fortieth park.[2]',
]);
$NOTES = [
    'Last Will and Testament of William S. Hart, Newhall, 9 September 1944, clauses Fourth (A, B, C, I, J, L) and Fifth, read from the scan, https://scvhistory.com/scvhistory/cp19440909.htm. Archive record: "Last Will and Testament of William S. Hart, 9-9-1944."',
    'City of Santa Clarita, "William S. Hart Park Officially Opens as City\'s 40th Park," 14 July 2025, https://santaclarita.gov/blog/2025/07/14/william-s-hart-park-officially-opens-as-citys-40th-park/, read 2 October 2026.',
    '"Two-Gun William S. Hart Dies," Los Angeles Examiner, 24 June 1946, pages 1 to 4, from the collection of Alan Pollack, https://scvhistory.com/scvhistory/lae62446a.htm. It gives his birth as 6 December 1862; Leon Worden\'s note on the page: "By most accounts, Hart was born in 1864."',
    'Invitation to the dedication of William S. Hart Park, 20 September 1958, with the Friends of Hart Park\'s biography of Hart, https://scvhistory.com/scvhistory/lw2616.htm.',
    'American Film Institute, AFI Catalog of Feature Films, https://catalog.afi.com/ ; IMDb, "William S. Hart," https://www.imdb.com/name/nm0366586/.',
    'Library of Congress, Moving Image Research Center, guide to collections, https://guides.loc.gov/moving-image-research/collections (the Gatewood W. Dunston Collection).',
    'Leon Worden, the 1880 census of the Hart family, New York City, SCVHistory.com LW3336, https://scvhistory.com/scvhistory/lw3336.htm.',
    'Jerry Reynolds, History of the Santa Clarita Valley, web edition 1998, part 53, "Two-Gun Bill," https://scvhistory.com/scvhistory/signal/reynolds/part53.html.',
    'Jimmy Toland, "Two-Gun Man Lives Atop Mountain in Newhall," Los Angeles Times, 19 July 1936, https://scvhistory.com/scvhistory/lat19360719hart.htm.',
    'Tom Sitton, "Survey of the Acquisition of Property Comprising William S. Hart Park," Natural History Museum of Los Angeles County, February 1989, citing the County\'s deed books and the Newhall Signal, https://scvhistory.com/scvhistory/mu8901.htm.',
    'William S. Hart to Arthur Kelly, 12 July 1926, and Kelly\'s notes, https://scvhistory.com/scvhistory/hartmansion-const.htm.',
    'Leon Worden, caption to "Hart Mansion Under Construction, 1927," SCVHistory.com FH2701, https://scvhistory.com/scvhistory/fh2701.htm.',
    'Photoplay, May 1928, page 6, as reproduced at SCVHistory.com LW2271, https://scvhistory.com/scvhistory/lw2271.htm.',
    'Leon Worden, "Requiem to a Little Soldier," The Signal, 17 May 2003, drawing on the Newhall Signal of 29 March and 5 April 1928 and Hart\'s letter to Wyatt Earp, https://scvhistory.com/scvhistory/sg051703.htm.',
    'Jerry Reynolds, History of the Santa Clarita Valley, web edition 1998, part 54, https://scvhistory.com/scvhistory/signal/reynolds/part54.html.',
    'Bill Crowl, "William S. Hart and the American," Friends of William S. Hart Park, 1999, at SCVHistory.com AP1516, https://scvhistory.com/scvhistory/ap1516.htm.',
    'Jerry Reynolds, part 65, "Transitions," with the editor\'s correction on the page: "It opened in 1945 and was renamed prior to his death." https://scvhistory.com/scvhistory/signal/reynolds/part65.html.',
    '"Cowpokes See Bill Hart Head For Last Roundup," Los Angeles Evening Herald-Express, 26 June 1946, from the collection of Alan Pollack, https://scvhistory.com/scvhistory/lahe06261946b.htm.',
    'Associated Press photograph and cutline, 27 February 1950, at SCVHistory.com LW2291, https://scvhistory.com/scvhistory/lw2291.htm.',
];
$EDITOR = [
    ['heading' => 'Birth year', 'position' => 'bottom', 'note' => 'Every year from 1862 to 1874 appears in the archive. The 1880 census (age 16) and the press ages of 1940, 1946 and 1950 fit 1864; the Examiner\'s obituary gives 1862; Reynolds gives about 1870; Hart himself gave 1868, 1871, 1872, 1873 and 1874 at different times. The record gives 1864, the best supported, and says why.'],
    ['heading' => 'Middle name', 'position' => 'bottom', 'note' => 'Surrey is the usual form and the Friends of Hart Park use it. His son\'s birth certificate of 1922 reads Surry, and Photoplay in 1921 gave Shakespeare. No birth record has been seen.'],
    ['heading' => 'Left out', 'position' => 'bottom', 'note' => 'A.B. Perkins wrote in 1958 that Mary Pickford and Hart filmed "Rags" in Newhall; the cast lists for Rags (1915) in the AFI Catalog do not include Hart. The rumor that Charles Lindbergh hid at the Hart house after the kidnapping of 1932 has no source. Neither is stated here.'],
];
$row = fn(string $printed, string $iso, string $gran, string $label): array => ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];
$DATES = [
    $row('December 6, 1864', '1864-12-06', 'day', 'born at Newburgh, New York (the year disputed)'),
    $row('1918', '1918-01-01', 'year', 'leases the Babcock Smith property in Newhall'),
    $row('February 1921', '1921-02-01', 'month', 'first purchase of the Horseshoe Ranch'),
    $row('July 12, 1926', '1926-07-12', 'day', 'writes to Arthur Kelly about building a house at Newhall'),
    $row('October 21, 1933', '1933-10-21', 'day', 'the last purchase completing the Horseshoe Ranch is recorded'),
    $row('May 23, 1941', '1941-05-23', 'day', 'the American Theater, his gift to the Legion, opens'),
    $row('September 9, 1944', '1944-09-09', 'day', 'signs the will leaving the ranch as a public park'),
    $row('June 23, 1946', '1946-06-23', 'day', 'dies at California Lutheran Hospital, Los Angeles'),
    $row('September 20, 1958', '1958-09-20', 'day', 'William S. Hart Park dedicated'),
];
$NAMED = [
    $PARK => 'Named under his will, which required the County to establish the name "WILLIAM S. HART PARK" by ordinance and keep it as the official name, on pain of the property passing to the State (Last Will and Testament of William S. Hart, 9 September 1944, clauses B and J).',
    $MANSION => 'His house, which he named La Loma de los Vientos, the Hill of the Winds, and which is known as the Hart Mansion (Hart to Arthur Kelly, 12 July 1926; Friends of Hart Park, 1958).',
    $HIGH => 'Named for him in his lifetime: the school opened in 1945 and was named before his death (editor\'s correction to Jerry Reynolds, part 65).',
    $DISTRICT => 'Named for William S. Hart. When and how the district took the name is not yet sourced in the archive.',
];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES) . implode('', $NAMED) . json_encode([$EDITOR, $DATES], JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }
$cur = trim((string)$p?->body); if ($cur && $cur !== trim($BODY)) { $bad[] = '#16356 has a body already'; }
echo '#16356 body: ' . ($cur ? 'already written' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes') . '; ' . array_sum(array_map('count', $MUST)) . ' phrases checked in ' . count($MUST) . ' sources' . PHP_EOL;
foreach ($NAMED as $pid => $n) { $e = $get($pid); echo "#$pid {$e?->title}: namedFor " . (in_array($ID, $e?->namedFor->ids() ?? []) ? 'already Hart' : '-> Hart') . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$cur) {
        $s = $get($ID); $h = array_map(fn($f) => $f->handle, $s->getFieldLayout()->getCustomFields());
        $s->setFieldValues(array_intersect_key(['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'editorNotes' => $EDITOR, 'recordDates' => $DATES,
            'birthDate' => 'December 6, 1864', 'birthDateEdtf' => '1864-12-06', 'birthplace' => 'Newburgh, New York', 'birthEvidence' => 'retrospective',
            'deathDate' => 'June 23, 1946', 'deathDateEdtf' => '1946-06-23', 'deathEvidence' => 'contemporary',
            'burialPlace' => 'Green-Wood Cemetery, Brooklyn, New York (his ashes)', 'burialEvidence' => 'contemporary',
            'personAliases' => 'Bill Hart; William Surrey Hart; Two-Gun Bill', 'occupation' => 'Actor; filmmaker; author; rancher',
            'wikidataId' => 'Q636680', 'imdbId' => 'nm0366586',
            'recordProvenance' => trim((string)$s->recordProvenance . '; build_hart_profile.php, 2 Oct 2026: sourced profile from the dossier and the mirror', '; ')], array_flip($h)));
        if (!$elements->saveElement($s)) { throw new \RuntimeException('#16356: ' . json_encode($s->getFirstErrors())); }
    }
    foreach ($NAMED as $pid => $note) {
        $e = $get($pid); $h = array_map(fn($f) => $f->handle, $e->getFieldLayout()->getCustomFields());
        $vals = ['namedFor' => [$ID], 'namingNote' => $note];
        if (in_array('placePeople', $h) && in_array($pid, [$PARK, $MANSION])) { $vals['placePeople'] = array_values(array_unique(array_merge($e->placePeople->ids(), [$ID]))); }
        $e->setFieldValues(array_intersect_key($vals, array_flip($h)));
        if (!$elements->saveElement($e)) { throw new \RuntimeException("#$pid: " . json_encode($e->getFirstErrors())); }
    }
    $tx->commit();
} catch (\Throwable $t2) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t2->getMessage() . PHP_EOL; throw $t2; }
$s = $get($ID);
$ok = trim((string)$s->body) === trim($BODY) && (string)$s->imdbId === 'nm0366586' && !array_filter(array_keys($NAMED), fn($pid) => !in_array($ID, $get($pid)->namedFor->ids()));
echo 'READ-BACK ' . ($ok ? 'OK: ' . $s->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_hart_profile.php', 5, $ok ? 'verified' : 'SHORT', 'William S. Hart: sourced profile, conflicts laid out; IMDb and Wikidata; park, mansion, Hart High and district named for him');
if (!$ok) { throw new \RuntimeException('build_hart_profile: read-back failed'); }
