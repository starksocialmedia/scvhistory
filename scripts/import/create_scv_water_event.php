/**
 * The first anchor event on /events: the SCV Water consolidation of 1 January
 * 2018 (Nathan, 2 October 2026, approving inventory/review/site-proposals-
 * 2026-10-02.md item 24: "The SCV Water consolidation is the natural first
 * event").
 *
 * THE SOURCES: SCV Water's own history (inventory/sources/scv-water-history-
 * 2026-10-02.json) and its "Who We Are" page, both read 2 October 2026 and
 * already the notes on the Newhall County Water District record (#27534), whose
 * sentence on the merger this repeats. Phrases are checked in both.
 *
 * NOT LINKED: Scott Wilk. Nothing in hand says he carried SB 634; the bill's
 * author waits for the bill itself (leginfo), not memory. The Valencia Water
 * Company has no record yet, so it is named in the text and not related.
 *
 * Era: Contemporary (171), period 2010-2019 (184). Idempotent: matched on title.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/create_scv_water_event.php'))"
 */

use craft\elements\{Entry, Category};

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root');
$ws = fn($s) => preg_replace('~\s+~u', ' ', (string)$s);
$SCVW = 402; $CLWA = 26563; $NCWD = 27534; $ERA = 171; $PERIOD = 184; $TEMPLATE = 875;
$TITLE = 'The SCV Water Consolidation';
$bad = [];
$h = $ws(json_decode((string)@file_get_contents("$root/inventory/sources/scv-water-history-2026-10-02.json"), true)['sources']['history']['passage'] ?? '');
foreach (['Formed in 2018 (Senate Bill 634) by an act of the State Legislature', 'three water divisions: Newhall Water Division, Santa Clarita Water Division, Valencia Water Division, as well as Castaic Lake Water Agency, the regional water wholesaler'] as $ph) {
    if (!str_contains($h, $ph)) { $bad[] = 'the history does not read "' . mb_substr($ph, 0, 60) . '"'; }
}
$n = Entry::find()->id($NCWD)->status(null)->one();
if (!$n || $n->title !== 'Newhall County Water District') { $bad[] = "#$NCWD is not the NCWD"; }
elseif (!str_contains($ws(strip_tags((string)$n->body)), 'Senate Bill 634 merged the district, the Castaic Lake Water Agency and its Santa Clarita Water Division, and the Valencia Water Company into SCV Water, effective 1 January 2018')) { $bad[] = 'NCWD does not carry the merger sentence'; }
elseif (!str_contains(implode(' ', array_column($n->footnotes ?? [], 'note')), 'created January 1, 2018 by Senate Bill 634, an act of the State Legislature, which merged three water agencies')) { $bad[] = 'NCWD note 2 does not carry the Who We Are quotation'; }
foreach ([$SCVW => 'Santa Clarita Valley Water', $CLWA => 'Castaic Lake Water Agency'] as $id => $t) { if (Entry::find()->id($id)->status(null)->one()?->title !== $t) { $bad[] = "#$id is not $t"; } }
if (!str_starts_with((string)Category::find()->id($ERA)->one()?->title, 'Contemporary') || Category::find()->id($PERIOD)->one()?->title !== '2010-2019') { $bad[] = 'era or period ids moved'; }

$BODY = implode("\n\n", [
    'On 1 January 2018 the valley\'s public water agencies became one. Senate Bill 634, an act of the State Legislature, created the Santa Clarita Valley Water Agency, SCV Water, and merged into it the Castaic Lake Water Agency, the regional wholesaler that bought State Water Project water, with its Santa Clarita Water Division; the Newhall County Water District; and the Valencia Water Company.[1][2]',
    'They continue inside SCV Water as its Newhall, Santa Clarita and Valencia water divisions. The oldest of them, the Newhall Water Division, traces its roots to the Newhall Water System of 1913.[1]',
]);
$NOTES = [
    'Santa Clarita Valley Water Agency, "History of SCV Water," https://yourscvwater.com/who-we-are/history, read 2 October 2026: "Formed in 2018 (Senate Bill 634) by an act of the State Legislature."',
    'Santa Clarita Valley Water Agency, "Who We Are," https://yourscvwater.com/who-we-are, read 2 October 2026: "created January 1, 2018 by Senate Bill 634, an act of the State Legislature, which merged three water agencies in the Santa Clarita Valley."',
];
$SIG = 'One public water agency for the valley in place of three';
if (preg_match('~\x{2014}~u', $BODY . implode('', $NOTES) . $SIG)) { $bad[] = 'an em dash in the text'; }
$have = Entry::find()->section('events')->status(null)->title($TITLE)->one();
echo ($have ? "#{$have->id} exists, nothing to do" : "create \"$TITLE\": 1 January 2018; relates #$SCVW, #$CLWA, #$NCWD; era #$ERA, period #$PERIOD") . '; ' . str_word_count($BODY) . ' words, ' . count($NOTES) . ' notes' . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
if ($have) { return; }
$tmpl = Entry::find()->id($TEMPLATE)->status(null)->one();
$e = new Entry(); $e->sectionId = $tmpl->sectionId; $e->setTypeId($tmpl->typeId); $e->title = $TITLE;
$e->setFieldValues(['body' => $BODY, 'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($NOTES), $NOTES),
    'eventDate' => 'January 1, 2018', 'eventDateEdtf' => '2018-01-01', 'eventSignificance' => $SIG,
    'eventOrganizations' => [$SCVW, $CLWA, $NCWD], 'historicalEra' => [$ERA], 'historicalPeriod' => [$PERIOD],
    'recordProvenance' => 'create_scv_water_event.php, 2 October 2026']);
if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException('event: ' . json_encode($e->getFirstErrors())); }
$b = Entry::find()->id($e->id)->status(null)->one();
$ok = trim((string)$b->body) === trim($BODY) && $b->eventDateEdtf === '2018-01-01' && count($b->eventOrganizations->ids()) === 3 && $b->historicalEra->ids() === [$ERA];
echo 'READ-BACK ' . ($ok ? 'OK: ' . $b->url : 'SHORT') . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('create_scv_water_event.php', 1, $ok ? 'verified' : 'SHORT', 'event: the SCV Water consolidation, 1 January 2018, the first anchor event');
if (!$ok) { throw new \RuntimeException('create_scv_water_event: read-back failed'); }
