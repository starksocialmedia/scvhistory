/**
 * Cave Johnson Couts #323 (Nathan, 1 October 2026; the request gave #16023,
 * which does not exist). Replaces the unsourced WordPress body and bio.
 *
 * THE SOURCES
 *   [SMYTHE]   William Ellsworth Smythe, History of San Diego (1907), pages
 *              268-269, as the San Diego History Center publishes it. A real
 *              archive's page, but a 1907 text: retrospective, and admiring.
 *   [REYNOLDS] Jerry Reynolds, part 20, "An Eager Market" (#861): the reason
 *              Couts is in this archive.
 *   Wikipedia was a finding aid only.
 *
 * WHAT THE CHECK FOUND. The record said that in 1849 Couts drove 700 head
 * "through Newhall Pass." Reynolds does not say that: the 1849 drive went up the
 * coast to San Jose, and it was "the next year" that 2,500 head, whose he does
 * not say, went over the Newhall Pass. The profile keeps the two apart. Reynolds's
 * figures (700 head, $20, 2,500 head) are attributed to him; his story of the
 * rooftop meeting is given as his. The birth date and place follow Smythe
 * (11 November 1821, near Springfield) rather than the unsourced 6 November,
 * Smith County. Smythe's account says nothing of how Couts treated the Native
 * workers on his ranchos; an editor note says so and that no source is held yet.
 *
 * Replaces body and authorBio only while they are the WordPress text, and
 * recordDates only while every row is derived from it. Idempotent. Dry run by
 * default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_couts_profile.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$ID = 323; $STEARNS = 309; $YGNACIO = 293;
$p = Entry::find()->id($ID)->status(null)->one();
$src = ['smythe' => $ws(json_decode((string)@file_get_contents("$root/inventory/sources/cave-johnson-couts-2026-10-01.json"), true)['sources']['sdhc']['passage'] ?? ''),
    'reynolds' => $ws(@file_get_contents("$root/inventory/legacy/fetched/reynolds-part20.txt"))];
$bad = [];
if (!$p || $p->title !== 'Cave Johnson Couts') { $bad[] = '#323 is not Couts'; }
$MUST = [
    'smythe' => ['born near Springfield, Tennessee, November 11, 1821', 'His uncle, Cave Johnson, was [Postmaster General]* under President Polk, and had him appointed to West Point, where he graduated in 1843', 'at Los Angeles, San Luis Rey, and San Diego from 1848 to 1851', 'In 1849 he conducted the Whipple expedition to the Colorado River', 'On April 5, 1851, he married Ysidora Bandini, daughter of Juan Bandini', 'In October of the same year he resigned from the army', 'member of the first grand jury September, 1850, and county judge in 1854', 'In 1853 he removed to a tract known as the Guajome grant, a wedding gift to his wife from her brother-in-law, Abel Stearns', 'able to secure all the cheap labor needed for the improvement of his property', 'San Marcos, Buena Vista, and La Jolla ranchos', 'about 20,000 acres', 'He entertained Helen Hunt Jackson while she was collecting materials for Ramona', 'He died at the Horton House, in San Diego, June 10 1874', 'Secretary of the Treasury'],
    'reynolds' => ['tumbling her into the arms of Tennessee-born Lieutenant Cave Johnson Couts', 'William Blunt Couts', 'During the spring of 1849 William and Cave Couts set out with a herd of some seven hundred head on consignment from Bandini and John "Juanito" Temple', 'up the coast to San Jose', 'finally getting twenty dollars, or about ten times what they would have brought in the days of hide and tallow', 'The next year, 2,500 head were driven north over the present-day Newhall Pass to the banks of the Santa Clara', 'a route suggested by Ygnacio del Valle', 'he charged grazing fees on his rancho'],
];
foreach ($MUST as $k => $phrases) { foreach ($phrases as $ph) { if (!str_contains($src[$k], $ph)) { $bad[] = "$k does not read \"$ph\""; } } }

$BODY = implode("\n\n", [
    'Cave Johnson Couts was a Tennessee-born army officer who settled in San Diego County after the Mexican War, married into the Bandini family and became one of the wealthiest ranchers in Southern California.[1] He belongs to this valley\'s history through the Gold Rush cattle trade, in Jerry Reynolds\'s account of how the rancheros found a market for their beef.[2]',
    'William Smythe\'s History of San Diego (1907) says he was born near Springfield, Tennessee, on 11 November 1821, and that his uncle Cave Johnson, Postmaster General under President Polk, had him appointed to West Point, where he graduated in 1843. He served on the frontier until after the Mexican War and then at Los Angeles, San Luis Rey and San Diego from 1848 to 1851, and in 1849 he conducted the Whipple expedition to the Colorado River.[1]',
    'According to Reynolds, in the spring of 1849 Couts and his brother, William Blunt Couts, drove a herd of some seven hundred head, on consignment from Juan Bandini and John Temple, up the coast to San Jose, where Cave held out for twenty dollars a head, about ten times what cattle had fetched in the days of hides and tallow. The next year, Reynolds writes, 2,500 head were driven north over the present-day Newhall Pass to the Santa Clara River, some by a route Ygnacio del Valle suggested, and del Valle charged grazing fees on his rancho. Reynolds does not say whose herds those were. The figures are his, and the drive of 1849 itself went by the coast, not through this valley.[2]',
    'He married Ysidora Bandini, Juan Bandini\'s daughter, on 5 April 1851, and resigned from the army that October.[1] Reynolds tells of their meeting, Ysidora tumbling from a roof railing into his arms as his column passed; it is Reynolds\'s story, and no earlier source for it is in the archive.[2] Smythe records that Couts sat on San Diego County\'s first grand jury in September 1850 and was county judge in 1854, and that in 1853 he moved to the Guajome grant, a wedding gift to his wife from her brother-in-law, Abel Stearns.[1]',
    'Appointed sub-agent for the San Luis Rey Indians, he was, in Smythe\'s words, "able to secure all the cheap labor needed for the improvement of his property." He bought the San Marcos, Buena Vista and La Jolla ranchos and government land, about 20,000 acres in all, and Smythe says he entertained Helen Hunt Jackson while she was gathering material for Ramona. He died at the Horton House in San Diego on 10 June 1874.[1]',
]);
$BIO = 'Cave Johnson Couts (1821-1874) was a Tennessee-born army officer who became a San Diego County rancher. Jerry Reynolds\'s history of the valley opens its chapter on the Gold Rush cattle trade with his drive of 1849, which went up the coast to San Jose.';
$NOTES = [
    'William Ellsworth Smythe, History of San Diego, 1542-1908 (San Diego: History Co., 1907), pages 268-269, as published by the San Diego History Center, "Cave Johnson Couts (1821-1874)," https://sandiegohistory.org/archives/biographysubject/cjcouts/, read 1 October 2026. The Center corrects Smythe, who had Cave Johnson as Secretary of the Treasury.',
    'Jerry Reynolds, History of the Santa Clarita Valley, web edition edited by Leon Worden for the SCV Historical Society, 1998, part 20, "An Eager Market," https://scvhistory.com/scvhistory/signal/reynolds/part20.html. Archive record: "Chapter 20. An Eager Market."',
];
$EDITOR = [
    ['heading' => 'Birth', 'position' => 'bottom', 'note' => 'This record came from WordPress with 6 November 1821, Smith County, Tennessee, for which no source has been found. Smythe gives 11 November 1821, near Springfield, and the record now follows him.'],
    ['heading' => 'What this profile does not yet cover', 'position' => 'bottom', 'note' => 'Smythe\'s 1907 account is the only biography of Couts in the archive. It is an admiring one, and it says nothing of how Couts treated the Native workers whose labor he secured as sub-agent. Wikipedia, used here only as a finding aid, reports that he was tried on several charges, including murder, and acquitted. The archive holds no source for this yet, and the profile will say more when it does.'],
    ['heading' => 'Burial', 'position' => 'bottom', 'note' => 'The burial place came with the WordPress import and has no source in the archive.'],
];
$row = fn(string $printed, string $iso, string $gran, string $label): array => ['printed' => $printed, 'iso' => $iso . ' 00:00:00', 'granularity' => $gran, 'label' => $label, 'confirmed' => false];
$DATES = [
    $row('November 11, 1821', '1821-11-11', 'day', 'born near Springfield, Tennessee, per Smythe'),
    $row('1849', '1849-01-01', 'year', 'the Couts brothers drive cattle up the coast to San Jose, per Reynolds'),
    $row('April 5, 1851', '1851-04-05', 'day', 'marries Ysidora Bandini'),
    $row('June 10, 1874', '1874-06-10', 'day', 'dies at the Horton House, San Diego'),
];
if (preg_match('~\x{2014}~u', $BODY . $BIO . implode('', $NOTES) . json_encode([$EDITOR, $DATES], JSON_UNESCAPED_UNICODE))) { $bad[] = 'an em dash in the text'; }

$OLD_START = 'Cave Johnson Couts was born in Smith County, Tennessee in 1821';
$cur = trim((string)$p?->body); $isOld = str_starts_with(trim(strip_tags($cur)), $OLD_START); $isNew = $cur === trim($BODY);
if (!$isOld && !$isNew) { $bad[] = '#323\'s body has been edited since the WordPress import'; }
$bioOld = str_contains((string)$p?->authorBio, 'drove 700 head of cattle north through Newhall Pass');
$oldPlain = $ws(strip_tags($cur));
$rows = array_values(array_filter($p?->recordDates ?? [], fn($r) => is_array($r) && trim((string)($r['printed'] ?? '')) !== ''));
$datesNew = array_column($rows, 'label') === array_column($DATES, 'label');
$datesDerived = !$rows || !array_filter($rows, fn($r) => !str_contains($oldPlain, trim(mb_substr($ws($r['label']), 3, 60))));
echo '#323 body: ' . ($isNew ? 'already the sourced profile' : 'the unsourced WordPress body -> the sourced profile (' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes)') . '; every quoted phrase checked (' . array_sum(array_map('count', $MUST)) . ')' . PHP_EOL;
echo '#323 authorBio: ' . ($bioOld ? 'the 1849-through-Newhall-Pass conflation -> corrected' : 'already corrected') . '; birth 6 Nov, Smith County -> 11 Nov 1821, near Springfield (Smythe); recordDates ' . ($datesNew ? 'already rebuilt' : ($datesDerived ? count($rows) . ' derived rows -> ' . count($DATES) : 'NOT all derived: kept')) . '; relatedPersons + #309 Stearns, #293 Ygnacio del Valle' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

$h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
$vals = [];
if (!$isNew) {
    $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'birthDate' => 'November 11, 1821', 'birthDateEdtf' => '1821-11-11',
        'birthplace' => 'near Springfield, Tennessee', 'birthEvidence' => 'retrospective', 'deathEvidence' => 'retrospective',
        'editorNotes' => array_merge(array_values(array_filter($p->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')), $EDITOR),
        'relatedPersons' => array_values(array_unique(array_merge($p->relatedPersons->status(null)->ids(), [$STEARNS, $YGNACIO]))),
        'recordProvenance' => trim((string)$p->recordProvenance . '; build_couts_profile.php, 1 Oct 2026: sourced profile (Smythe 1907 via SDHC; Reynolds part 20)', '; ')];
}
if ($bioOld) { $vals['authorBio'] = $BIO; }
if ($datesDerived && !$datesNew) { $vals['recordDates'] = $DATES; }
if ($vals) { $p->setFieldValues(array_intersect_key($vals, array_flip($h))); if (!$elements->saveElement($p)) { throw new \RuntimeException('#323: ' . json_encode($p->getFirstErrors())); } }
$s = Entry::find()->id($ID)->status(null)->one();
$ok = trim((string)$s->body) === trim($BODY) && (string)$s->birthDateEdtf === '1821-11-11';
echo 'READ-BACK ' . ($ok ? 'OK: ' . $s->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_couts_profile.php', 1, $ok ? 'verified' : 'SHORT', 'Cave Johnson Couts: sourced profile; the 1849 Newhall Pass conflation corrected');
if (!$ok) { throw new \RuntimeException('build_couts_profile: read-back failed'); }
