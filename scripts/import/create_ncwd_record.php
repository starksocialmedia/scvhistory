/**
 * Newhall County Water District (Nathan, 1 October 2026: "If Castaic Lake Water
 * Agency has a record, Newhall County Water District needs one too ... founded,
 * dissolved, succeeded by SCV Water, with its elections attached").
 *
 * THE SOURCES: SCV Water's own history of its Newhall Water Division and its
 * "Who We Are" page, read 2 October 2026 (inventory/sources/scv-water-history-
 * 2026-10-02.json). The history names all four predecessors: Newhall Water
 * Division (this district), Santa Clarita Water Division, Valencia Water
 * Division, and the Castaic Lake Water Agency.
 *
 * ALSO: the same history settles a conflict left open on A.B. Perkins's profile
 * (#333): he came in 1919 as general manager and bought the company a year
 * later, so "to manage" and "to buy" are both right, in that order. The
 * sentence saying nothing in the archive settles it is replaced, with a note.
 *
 * Not yet: the district's board elections of 1983 to 2009, which are in the
 * county scans deferred on 30 September; the record says so.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_ncwd_record.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements(); $svc = Craft::$app->getEntries();
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$SCVW = 402; $CLWA = 26563; $PERKINS = 333; $TITLE = 'Newhall County Water District';
$h = $ws(json_decode((string)@file_get_contents("$root/inventory/sources/scv-water-history-2026-10-02.json"), true)['sources']['history']['passage'] ?? '');
$bad = [];
foreach (['three water divisions: Newhall Water Division, Santa Clarita Water Division, Valencia Water Division, as well as Castaic Lake Water Agency', 'Newhall Water System was established in 1913', 'H. Clay Needham and M.W. Atwood', 'In 1919, with a population in the Santa Clarita Valley at 600, Arthur B. “Perk” Perkins became the general manager of the new company. A year later, he bought the company', 'On June 1, 1930, the small water company became a corporation', 'he stepped down from his post in 1948', 'with the population of 3,500', 'On Jan. 13, 1953, an election was held to form the Newhall County Water District', 'Although there was no such place as “Newhall County,” the name stuck', 'Voters elected five governing board members', 'revenue bonds in the amount of $425,000', 'for $130,000', 'In 1966, Castaic and Pinetree (Canyon Country) were incorporated into the district', 'State Water Project purchased by Castaic Lake Water Agency', 'the Tesoro del Valle area was added'] as $ph) {
    if (!str_contains($h, $ph)) { $bad[] = 'the history does not read "' . mb_substr($ph, 0, 60) . '"'; }
}
if (Entry::find()->id($CLWA)->status(null)->one()?->title !== 'Castaic Lake Water Agency' || Entry::find()->id($SCVW)->status(null)->one()?->title !== 'Santa Clarita Valley Water') { $bad[] = 'CLWA or SCV Water is not who it should be'; }
$BODY = implode("\n\n", [
    'Newhall County Water District was the public water agency for Newhall, and later for Castaic and Pinetree in Canyon Country, from 1953 until it was merged into the Santa Clarita Valley Water Agency on 1 January 2018. It continues there as the Newhall Water Division.[1][2]',
    'It grew out of a private company. The Newhall Water System was established in 1913 by H. Clay Needham and M.W. Atwood; A.B. Perkins became its general manager in 1919 and bought it a year later, when it had about 125 customers; and on 1 June 1930 it was incorporated as the Newhall Water Company. Perkins stepped down as general manager in 1948.[1]',
    'With Newhall at about 3,500 people, the town\'s business and civic leaders proposed a public district, and voters formed it at an election on 13 January 1953, choosing a board of five. Later that year the district issued $425,000 in revenue bonds and bought the Newhall Water Company from Perkins for $130,000. There was no Newhall County; as SCV Water\'s history puts it, "the name stuck."[1]',
    'Castaic and Pinetree were added to the district in 1966, and the Tesoro del Valle area in the early 2000s. It pumped its own wells and bought State Water Project water through the Castaic Lake Water Agency.[1]',
    'Senate Bill 634 merged the district, the Castaic Lake Water Agency and its Santa Clarita Water Division, and the Valencia Water Company into SCV Water, effective 1 January 2018.[1][2]',
]);
$NOTES = [
    'Santa Clarita Valley Water Agency, "History of SCV Water," Newhall Water Division section, https://yourscvwater.com/who-we-are/history, read 2 October 2026.',
    'Santa Clarita Valley Water Agency, "Who We Are," https://yourscvwater.com/who-we-are, read 2 October 2026: "created January 1, 2018 by Senate Bill 634, an act of the State Legislature, which merged three water agencies in the Santa Clarita Valley."',
];
$EDITOR = [['heading' => 'Elections', 'position' => 'bottom', 'note' => 'The district\'s board elections from 1983 to 2009 are in the County\'s records and are not yet in the archive.']];
/* Perkins: the conflict the history settles. */
$P_OLD = 'The two may both be true, and no deed or company record in the archive settles it.';
$P_NEW = 'SCV Water\'s own history of the company settles it: he came as its general manager in 1919 and bought it a year later.[8]';
$P_NOTE = 'Santa Clarita Valley Water Agency, "History of SCV Water," Newhall Water Division section, https://yourscvwater.com/who-we-are/history, read 2 October 2026.';
$pk = Entry::find()->id($PERKINS)->status(null)->one();
$pkTodo = str_contains((string)$pk->body, $P_OLD);
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES) . $P_NEW)) { $bad[] = 'an em dash in the text'; }
$have = Entry::find()->section('organizations')->status(null)->title($TITLE)->one();
echo ($have ? "#{$have->id} exists" : "create $TITLE: founded 13 January 1953, dissolved 1 January 2018, succeeded by #402") . '; ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes' . PHP_EOL;
echo '#333 Perkins: ' . ($pkTodo ? 'the "no record settles it" sentence -> settled by SCV Water\'s history, note 8' : 'already settled') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$tx = Craft::$app->getDb()->beginTransaction();
try {
    if (!$have) {
        $tmpl = Entry::find()->id($CLWA)->status(null)->one();
        $have = new Entry(); $have->sectionId = $tmpl->sectionId; $have->setTypeId($tmpl->typeId); $have->title = $TITLE;
        $have->setFieldValues(['body' => $BODY, 'footnotes' => $fn($NOTES), 'editorNotes' => $EDITOR, 'orgAliases' => 'NCWD; Newhall Water Division', 'orgType' => 'government',
            'dateFounded' => 'January 13, 1953', 'dateFoundedEdtf' => '1953-01-13', 'dateDissolved' => 'January 1, 2018', 'dateDissolvedEdtf' => '2018-01-01',
            'succeededBy' => [$SCVW], 'orgAssociatedPersons' => [$PERKINS], 'recordProvenance' => 'create_ncwd_record.php, 2 October 2026']);
        if (!$elements->saveElement($have)) { throw new \RuntimeException('NCWD: ' . json_encode($have->getFirstErrors())); }
    }
    if ($pkTodo) {
        $notes = array_map(fn($r) => ['number' => (string)($r['number'] ?? ''), 'note' => (string)($r['note'] ?? ''), 'source' => (string)($r['source'] ?? '')], $pk->footnotes ?? []);
        if (count($notes) !== 7) { throw new \RuntimeException('Perkins has ' . count($notes) . ' notes, not 7'); }
        $notes[] = ['number' => '8', 'note' => $P_NOTE, 'source' => 'editorial-2026'];
        $pk->setFieldValues(['body' => str_replace($P_OLD, $P_NEW, (string)$pk->body), 'footnotes' => $notes]);
        if (!$elements->saveElement($pk)) { throw new \RuntimeException('#333'); }
    }
    $tx->commit();
} catch (\Throwable $t) { $tx->rollBack(); echo 'ROLLED BACK, nothing was written: ' . $t->getMessage() . PHP_EOL; throw $t; }
$n = Entry::find()->section('organizations')->status(null)->title($TITLE)->one();
$ok = $n && trim((string)$n->body) === trim($BODY) && in_array($SCVW, $n->succeededBy->ids()) && str_contains((string)Entry::find()->id($PERKINS)->status(null)->one()->body, $P_NEW);
echo 'READ-BACK ' . ($ok ? 'OK: ' . $n->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('create_ncwd_record.php', 2, $ok ? 'verified' : 'SHORT', 'Newhall County Water District record; Perkins water-company conflict settled');
if (!$ok) { throw new \RuntimeException('create_ncwd_record: read-back failed'); }
