/**
 * Patsy Ayala #23093, Santa Clarita City Council, District 1 (Nathan,
 * 1 October 2026). A living person and a sitting member: public life only, no
 * birth date, no family.
 *
 * THE SOURCES
 *   [CITY]     City of Santa Clarita, "Mayor Pro Tem Patsy Ayala," the City's own
 *              biography of a sitting member, read 1 October 2026. Her career
 *              before the council rests on it alone and is given as the City's
 *              account; "served in the California State Assembly and Senate" is
 *              quoted, not restated, since it reads as staff service, not office.
 *   [ARCHIVE]  The election of 5 November 2024 and her office holding (#23401).
 *
 * The portrait is PatsyAyala.jpg, imported on Nathan's word: an upscale he made
 * (a Firefly upscale of a WebP, per its content credential), source per Nathan
 * Imhoff.
 *
 * Fills an empty body only. Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_ayala_profile.php'))"
 */

use craft\elements\{Entry, Asset};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 23093; $CAND = 22404; $HOLDING = 23401;
$FILE = 'PatsyAyala.jpg'; $SHA = '2b0160f429c290711d7284b9bc620e03da6266a48f9ae233238c1cb11df50c06'; $AS = 'patsy-ayala-edited.jpg';
$p = Entry::find()->id($ID)->status(null)->one();
$city = json_decode((string)@file_get_contents("$root/inventory/sources/patsy-ayala-2026-10-01.json"), true)['sources']['city']['passage'] ?? '';
$bad = [];
if (!$p || $p->title !== 'Patsy Ayala') { $bad[] = '#23093 is not Patsy Ayala'; }
$c = Entry::find()->id($CAND)->status(null)->one();
if (!$c || (int)$c->votes !== 4563 || $c->outcome->value !== 'elected') { $bad[] = 'candidacy #22404 does not read 4,563, elected'; }
$others = $c ? array_map(fn($x) => [$x->nameAsPrinted, (int)$x->votes], Entry::find()->section('candidacies')->status(null)->relatedTo(['targetElement' => $c->candidacyElection->one(), 'field' => 'candidacyElection'])->orderBy('votes desc')->all()) : [];
if ($others !== [['Patsy Ayala', 4563], ['Bryce Jepsen', 4142], ['Tim Burkhart', 4108]]) { $bad[] = 'the District 1 field is not as written: ' . json_encode($others); }
if ((string)Entry::find()->id($HOLDING)->status(null)->one()?->termStart !== 'December 2024') { $bad[] = 'holding #23401 does not start December 2024'; }
foreach (['serves as Mayor Pro Tem of the City of Santa Clarita', 'Director of the Los Angeles County North County Transportation Coalition (NCTC)', 'more than a decade of public service and transportation policy experience', 'she served in the California State Assembly and Senate, where she worked extensively on transportation legislation', 'Metro Business Interruption Fund', 'Eat Shop Play initiative', 'named the 21st Senatorial District Woman of the Year in 2013', 'bachelor’s degree in Computer Systems Management and certificates of study from Yale University and The Wharton School'] as $ph) {
    if (!str_contains($city, $ph)) { $bad[] = "the City's page does not read \"$ph\""; }
}
$path = "$root/inventory/incoming/$FILE";
if (!is_file($path) || hash_file('sha256', $path) !== $SHA) { $bad[] = "$FILE is missing or not the file received"; }

$BODY = implode("\n\n", [
    'Patsy Ayala was elected to the Santa Clarita City Council from District 1 on 5 November 2024 and took her seat in December 2024.[1][2] When the City\'s biography of her was read on 1 October 2026, she was serving as Mayor Pro Tem.[1]',
    'She won the District 1 race with 4,563 votes, ahead of Bryce Jepsen with 4,142 and Tim Burkhart with 4,108.[2]',
    'The City\'s biography describes more than a decade in public service and transportation policy before the council. It says she "served in the California State Assembly and Senate," working on transportation legislation with Caltrans, Metro and Metrolink, and that she worked with Metro on its programs for businesses, among them the Metro Business Interruption Fund and the Eat Shop Play initiative. It says she represents Santa Clarita as a director of the North County Transportation Coalition, and that she was named the 21st Senatorial District Woman of the Year in 2013. It gives her education as a bachelor\'s degree in computer systems management, with certificates of study from Yale University and the Wharton School of the University of Pennsylvania.[1]',
]);
$NOTES = [
    'City of Santa Clarita, "Mayor Pro Tem Patsy Ayala," https://santaclarita.gov/city-council/patsy-ayala/, read 1 October 2026: the City\'s own biography of a sitting member.',
    'Archive records: "City Council election, District 1, November 5, 2024," and her office holding, City Council Member, The City of Santa Clarita, from December 2024.',
];
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES))) { $bad[] = 'an em dash in the text'; }
$cur = trim((string)$p?->body);
if ($cur && $cur !== trim($BODY)) { $bad[] = '#23093 has a body already'; }
$have = Asset::find()->filename($AS)->one(); $port = $p?->featuredImage->one();
echo '#23093 body: ' . ($cur === trim($BODY) ? 'already written' : 'empty -> ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes') . PHP_EOL;
echo "portrait: " . ($have ? "#{$have->id} exists" : "import $FILE as outside/$AS") . '; ' . ($port && $have && $port->id === $have->id ? 'already the portrait' : 'set as the portrait') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$have) {
        $vol = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia'); $folder = Craft::$app->getAssets()->findFolder(['volumeId' => $vol->id, 'path' => 'outside/']);
        $tmp = sys_get_temp_dir() . '/' . $AS; copy($path, $tmp);
        $have = new Asset(); $have->tempFilePath = $tmp; $have->setFilename($AS); $have->newFolderId = $folder->id; $have->setVolumeId($vol->id); $have->setScenario(Asset::SCENARIO_CREATE); $have->avoidFilenameConflicts = false;
        $have->setFieldValues(['license' => 'unknown', 'provenanceKind' => 'commissioned', 'acquiredDate' => '2026-10-01', 'enhancedBy' => 'Nathan Imhoff', 'enhancedDate' => new \DateTime('2026-10-01'),
            'enhancementMethod' => 'Upscaled by Nathan Imhoff (a Firefly upscale of a WebP, per the content credential).',
            'contentCredentials' => "Adobe content credential (C2PA), https://cai-manifests.adobe.com/manifests/urn-c2pa-a69a6aea-a5ef-419e-b3ed-17c8457bc0bf-adobe\nSteps recorded (UTC): 2026-10-01 14:47 upscaled (Firefly creative upsampler), from a WebP.\nA note, not a refusal.",
            'source' => "Source per Nathan Imhoff. Upscaled by Nathan Imhoff. Received as $FILE, SHA-256 $SHA; the stored copy is re-encoded on import."]);
        if (!$elements->saveElement($have)) { throw new \RuntimeException('asset: ' . json_encode($have->getFirstErrors())); }
    }
    $p = Entry::find()->id($ID)->status(null)->one(); $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = ['featuredImage' => [$have->id]];
    if (trim((string)$p->body) === '') { $vals += ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'City council member',
        'recordProvenance' => trim((string)$p->recordProvenance . '; build_ayala_profile.php, 1 Oct 2026: profile from the City\'s biography; portrait per Nathan Imhoff', '; ')]; }
    $p->setFieldValues(array_intersect_key($vals, array_flip($h)));
    if (!$elements->saveElement($p)) { throw new \RuntimeException('#23093: ' . json_encode($p->getFirstErrors())); }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$s = Entry::find()->id($ID)->status(null)->one();
$ok = trim((string)$s->body) === trim($BODY) && $s->featuredImage->one()?->filename === $AS;
echo 'READ-BACK ' . ($ok ? 'OK: ' . $s->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_ayala_profile.php', 2, $ok ? 'verified' : 'SHORT', 'Patsy Ayala: public-life profile and portrait');
if (!$ok) { throw new \RuntimeException('build_ayala_profile: read-back failed'); }
