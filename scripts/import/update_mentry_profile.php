/**
 * The profile of Charles Alexander Mentry, #18648.
 *
 * The record was created from the review screen on 21 September as "Alex
 * Mentry" and holds nothing: no dates, no occupation, no body, no relations.
 * This fills it from the sources import_mentry_sources.php brought in and the
 * legacy ch1070 page, and gives it an editorial body (editorial-2026) with a
 * footnote for every claim.
 *
 * THE NAME. By the name policy the title is the fullest form the corpus uses,
 * Charles Alexander Mentry; the usage forms become aliases. The slug stays
 * alex-mentry so no link breaks.
 *
 * THE BIRTH YEAR IS A SOURCE FAULT. The death certificate states 27 March
 * 1847, then gives his age at death on 4 October 1900 as 52 years, 6 months
 * and 8 days, which counts back to 27 March 1848. Month and day agree; the
 * year does not. Scofield's "in the fifty-fourth year of his age" fits 1847,
 * and the 1889 biography's arrival "with his father" in 1854 fits either. So
 * birthDateEdtf is 1847?-03-27, the qualifier on the year alone, and the
 * fault is written up in editorNotes until the sourceFault type exists.
 *
 * THE PRIORITY CLAIM IS A DISAGREEMENT, NOT A FACT. The sources claim four
 * different things for the well: first or oldest in the world, in the West,
 * west of Pennsylvania, and in California. The body lays them out with their
 * sources rather than choosing one. Scofield (1900) makes the narrowest
 * claim, "paying quantities" in this state. Two sources hedge: the Los
 * Angeles Times in 1954 ("credited with") and Perkins once, in 1962; his
 * 1954 and 1958 wording is not hedged.
 *
 * NO FAMILY RELATIONS. His father Peter, his wife and his children have no
 * records, so they are in the prose with citations and nothing is stored.
 *
 * Only empty fields are written; nothing a person has typed is replaced.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/update_mentry_profile.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$ID = 18648;
$PORTRAIT = 2324;   /* ch1070.jpg, 800 x 1170; no master exists on Reggie */
$TITLE = 'Charles Alexander Mentry';

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$elements = Craft::$app->getElements();
$e = \craft\elements\Entry::find()->id($ID)->section('persons')->status(null)->one();
$portrait = \craft\elements\Asset::find()->id($PORTRAIT)->one();
$mentryville = \craft\elements\Category::find()->group('neighborhood')->slug('mentryville')->one();
if (!$e || !$portrait || !$mentryville) { echo 'missing: person, portrait or community' . PHP_EOL; return; }

/* The sources, by the records that now hold them. */
$doc = function (string $key) {
    $d = \craft\elements\Entry::find()->section('documents')->status(null)->legacyKey($key)->one();
    return $d ? 'record #' . $d->id : 'NOT YET IMPORTED (' . $key . ')';
};
$PEN   = '"C.A. Mentry," Pen Pictures From the Garden of the World, Lewis Publishing Co., 1889, pp. 539-540, ' . $doc('penpictures_mentry') . '. Written in his lifetime.';
$CERT  = 'His death certificate, ' . $doc('as0001') . ', as the legacy page quotes it; the scan has not yet been read against the quotation.';
$SCOF  = 'Demetrius G. Scofield, eulogy, 1900, ' . $doc('scofield') . '.';
$LEON  = 'Leon Worden, /scvhistory/ch1070.htm.';

$footnotes = [
    $SCOF . ' "It was not until he had completed the first producing well in Pico Cañon ... that the petroleum oil business could be said to have attained its start in this state." "He was for over twenty years the superintendent of the Pacific Coast Oil Company."',
    $PEN . ' "Mr. Mentry was born in France, and came to this country in 1854 with his father, Peter Mentry." ' . $SCOF . ' "came to the United States when only seven years of age."',
    $CERT . ' "The certificate gives a birth date of March 27, 1847, making him 52 years, 6 months and 8 days old at time of death." Counted back from 4 October 1900, that age gives 27 March 1848.',
    $LEON . ' "Born Charles Alexander Menetrier (maybe) in France on March 27, 1847 (probably)." See also photograph record #4187, "Some Notes About Alec Mentry\'s Birth Name."',
    $PEN . ' Venango County 1864, Greene County, Pithole 1865; "In November, 1873, he came to California"; and, in its first sentence, "came to this State in August, 1875."',
    $PEN . ' "in company with J.G. Baker and D.C. Scott, they obtained a lease of Beal [sic] & Baker, at an eighth royalty, for two years, in Pico Canyon"; "He put down a well thirty-five feet, with a spring pole."',
    $PEN . ' "The first steam drilling was begun in 1876." ' . $LEON . ' "on September 26, 1876, from a depth of 617 feet, a mighty geyser of oil shot through the 5 5/8-inch casing of his No. 4 well."',
    $LEON . ' "Scofield purchased Mentry\'s claim in 1876 and convinced the oil driller to come into his employ." ' . $SCOF . ' "At Mentryville, the little settlement of employees named in his honor."',
    'The world: plaque of 26 September 2001, /scvhistory/sg092701a.htm, "At that time, it was the oldest producing well in the world"; Reynolds, record #2089, "longest continually-operating oil well in the world."',
    'The West: A.B. Perkins, Story of Our Valley, 1954, record #1430, "first commercially successful oil well of the West"; ' . $LEON . ' "the first commercially successful oil well in the western United States."',
    'West of Pennsylvania: Santa Clarita Valley Historical Society, 1977, /scvhistory/cn7701.htm, and records #12490, #12546 and #12360.',
    'California: A.B. Perkins, 1958, record #1440, "California\'s first successful commercial oil well"; Warren (Pa.) Times Mirror, 4 February 1931, ' . $doc('lp_warrenpatimesmirror020431') . ', "the first [sic] oil well in California"; the Standard Oiler, August 1953, /scvhistory/standardoiler195308pico.htm, "the first commercially successful oil well completed in California"; Los Angeles Times, 17 March 1954, ' . $doc('lp_lat031754') . ', "credited with locating and drilling the first oil well in California."',
    $SCOF . ' "While wells had been drilled for oil in California as early as 1865, no satisfactory results had been obtained"; "the first to demonstrate the existence of petroleum in paying quantities."',
    'Los Angeles Times, 17 March 1954, ' . $doc('lp_lat031754') . ': "credited with locating and drilling the first oil well in California." A.B. Perkins, "The Pico Ghost Camp," Newhall Signal, January 1962, record #1446: "probably the first commercially successful oil well in California." His 1954 and 1958 wording, in notes 10 and 12, is not hedged.',
    'See the event "Lyon, Wiley and Jenkins Drill at Pico Canyon," and the Hughes well: Mining and Scientific Press, 25 July 1874, /scvhistory/sw_miningpress072574.htm.',
    $PEN . ' "In 1878 he married Miss May Lake, of Pennsylvania. They have two children: Irene and Arthur." ' . $LEON . ' "Alex Mentry married Flora May Lake of New York, who gave him three sons and a daughter."',
    'Los Angeles Herald, 11 November 1886, ' . $doc('sw_herald111186') . '; Los Angeles Herald and Los Angeles Times, 17 March 1899, ' . $doc('sw_herald031799') . ' and ' . $doc('sw_lat031799') . '. The papers spell him Mentry, Mentre and Mentriey.',
    $CERT . ' "Alec Mentry died at California Hospital, 1414 S. Hope St., as a result of typhoid fever, with chronic nephritis as a contributing factor." His occupation is given as "speculator."',
    $LEON . ' "He was laid to rest in the Evergreen Cemetery in Boyle Heights"; cemetery information from Jack and Joan Beitzel.',
];

$body = implode("\n\n", [
    'Charles Alexander Mentry drilled Pico No. 4, the 1876 well in Pico Canyon that his employer said began the oil business in California, and ran the Pico field for the Pacific Coast Oil Company for more than twenty years.[1]',
    'He was born in France and came to the United States as a boy of seven with his father, Peter.[2] His death certificate gives his birth date as 27 March 1847, then gives an age at death that counts back to 1848.[3] Leon Worden gives the family name as Menetrier, and says "maybe."[4] He worked the Pennsylvania oil regions from 1864, in Venango County, Greene County and at Pithole, and came to California in November 1873, though the same 1889 biography also gives August 1875.[5]',
    'In 1875 he and two partners leased the Pico claim, and he began with a spring pole.[6] Steam drilling began in 1876, and on 26 September 1876 his No. 4 well came in at 617 feet.[7] Demetrius Scofield bought the claim and kept Mentry on, and the camp that grew up around the wells took his name.[8]',
    'What the well was first at depends on who is asked, and the claims differ in scope. The widest is the world: by 2001 it was "the oldest producing well in the world."[9] Then the West.[10] Then everything west of Pennsylvania.[11] Then California.[12] Scofield, in 1900, claimed less than any of them: that Mentry was the first to show oil in California "in paying quantities," after others had drilled since 1865.[13] Two sources hedge: the Los Angeles Times in 1954, which has his father "credited with" the first well in California, and Perkins, once, in 1962: "probably the first commercially successful oil well in California."[14] Others had drilled in Pico Canyon before him: the Hughes well in 1865 and, by later accounts, Sanford Lyon.[15]',
    'He married May Lake in 1878. His 1889 biography names two children, Irene and Arthur; Leon Worden gives his wife as Flora May Lake of New York, and four children.[16] His father walked out of Pico Canyon in 1886 and was not found until 1899.[17]',
    'He died at California Hospital in Los Angeles on 4 October 1900, of typhoid fever, with chronic nephritis contributing.[18] He is buried in Evergreen Cemetery, Boyle Heights.[19]',
]);

$row = fn(string $printed, string $iso, string $gran, string $label): array =>
    ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];

$fields = [
    'fullName' => 'Charles Alexander Mentry',
    'personAliases' => "Alex Mentry\nAlec Mentry\nC.A. Mentry\nAlexander Mentry\nCharles Alexander Menetrier",
    'birthDate' => 'March 27, 1847',
    'birthDateEdtf' => '1847?-03-27',
    'birthEvidence' => 'certified',
    'birthplace' => 'France',
    'deathDate' => 'October 4, 1900',
    'deathDateEdtf' => '1900-10-04',
    'deathEvidence' => 'certified',
    'burialPlace' => 'Evergreen Cemetery, Boyle Heights, Los Angeles',
    'burialEvidence' => 'retrospective',
    'occupation' => 'Oil driller; superintendent, Pacific Coast Oil Company',
    'featuredImage' => [$portrait->id],
    'neighborhood' => [$mentryville->id],
    'legacyKey' => 'ch1070',
    'legacyUrl' => '/scvhistory/ch1070.htm',
    'sourcePath' => 'https://scvhistory.com/scvhistory/ch1070.htm',
    'bodyAuthorship' => 'editorial-2026',
    'body' => $body,
    'footnotes' => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($footnotes), $footnotes),
    'editorNotes' => [[
        'heading' => 'Source fault: the birth year',
        'note' => 'The death certificate states the birth date as 27 March 1847 and the age at death, 4 October 1900, as 52 years, 6 months and 8 days, which counts back to 27 March 1848. The month and day agree; the year does not. Held as 1847?-03-27, uncertain in the year alone, until the sourceFault type exists to carry it.',
        'position' => 'bottom',
    ]],
    'recordDates' => [
        $row('March 27, 1847', '1847-03-27', 'day', 'born, as the death certificate states it'),
        $row('52 years, 6 months, 8 days', '1848-03-27', 'day', 'born, as the certificate\'s age at death computes'),
        $row('1854', '1854-01-01', 'year', 'came to the United States with his father, per Pen Pictures 1889'),
        $row('November, 1873', '1873-11-01', 'month', 'came to California, per Pen Pictures 1889'),
        $row('September 26, 1876', '1876-09-26', 'day', 'Pico No. 4 comes in, per Leon Worden'),
        $row('1878', '1878-01-01', 'year', 'married May Lake, per Pen Pictures 1889'),
        $row('Oct. 4, 1900', '1900-10-04', 'day', 'died, per the death certificate'),
    ],
];

/* ---------------------------------------------------------------- report */

$layout = $e->getFieldLayout();
$absent = array_values(array_filter(array_keys($fields), fn($h) => !$layout->getFieldByHandle($h)));
$write = []; $kept = [];
foreach ($fields as $h => $v) {
    if (in_array($h, $absent, true)) { continue; }
    $cur = $e->getFieldValue($h);
    if ($cur instanceof \craft\elements\db\ElementQuery) { $cur = $cur->status(null)->ids(); }
    elseif ($cur instanceof \craft\fields\data\SingleOptionFieldData) { $cur = $cur->value; }
    $empty = is_array($cur) ? count(array_filter($cur, fn($r) => is_array($r) ? array_filter($r) : $r)) === 0 : trim((string)$cur) === '';
    if ($empty) { $write[$h] = $v; } else { $kept[] = $h; }
}
$missingDocs = substr_count($body . implode(' ', $footnotes), 'NOT YET IMPORTED');

echo '#' . $ID . '  "' . $e->title . '"  ->  "' . $TITLE . '"   (slug stays ' . $e->slug . ')' . PHP_EOL;
foreach ($write as $h => $v) {
    if ($h === 'body') { foreach (explode("\n\n", $v) as $p) { echo '   | ' . wordwrap($p, 96, "\n   | ") . PHP_EOL; } continue; }
    if ($h === 'footnotes') { foreach ($v as $f) { echo '   [' . $f['number'] . '] ' . wordwrap($f['note'], 92, "\n       ") . PHP_EOL; } continue; }
    $show = is_array($v) ? json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$v;
    echo '   ' . str_pad($h, 16) . mb_substr(str_replace("\n", ' / ', $show), 0, 110) . PHP_EOL;
}
if ($kept) { echo 'already filled, left alone: ' . implode(', ', $kept) . PHP_EOL; }
if ($absent) { echo 'NOT ON THE LAYOUT: ' . implode(', ', $absent) . PHP_EOL; }
if ($missingDocs) { echo 'WARNING: ' . $missingDocs . ' citation(s) point at documents not yet imported' . PHP_EOL; }

if (!$APPLY) { echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($missingDocs) { echo 'refusing: run import_mentry_sources.php first' . PHP_EOL; return; }

$e->title = $TITLE;
$e->setFieldValues($write);
if (!$elements->saveElement($e)) { throw new \RuntimeException('update_mentry_profile: ' . json_encode($e->getErrors())); }

$f = \craft\elements\Entry::find()->id($ID)->status(null)->one();
$short = [];
if ($f->title !== $TITLE) { $short[] = 'title'; }
foreach ($write as $h => $v) {
    $got = $f->getFieldValue($h);
    if ($got instanceof \craft\elements\db\ElementQuery) { if ($got->status(null)->ids() != $v) { $short[] = $h; } }
    elseif (is_array($v)) { if (count((array)$got) !== count($v)) { $short[] = $h; } }
    elseif (trim((string)($got instanceof \craft\fields\data\SingleOptionFieldData ? $got->value : $got)) !== trim((string)$v)) { $short[] = $h; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: title and ' . count($write) . ' fields') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('update_mentry_profile.php', 1, $short ? 'SHORT: ' . implode(', ', $short) : 'verified: title and ' . count($write) . ' fields',
    '#' . $ID . ' Charles Alexander Mentry; birth 1847?-03-27 (source fault); priority claim as four scopes');
if ($short) { throw new \RuntimeException('update_mentry_profile: read-back failed'); }
