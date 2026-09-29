/**
 * Hart Park changed hands, and 49 records still say the County runs it.
 *
 * Nathan, 29 September 2026. Forty-six photograph records close with Leon's
 * paragraph "Today, the Parks and Recreation Department of Los Angeles County
 * operates and maintains William S. Hart Park ... The Natural History Museum of
 * Los Angeles County is responsible for the interpretation ...". Three more
 * pieces say the same thing in their own words, each true when written:
 * #2759 (the museum "operates the Hart Museum"), #2167 (Reynolds, chapter 71,
 * "the Los Angeles County parks department also maintains"), and #12286 ("the
 * same county park staffers who run Hart Park").
 *
 * Leon's prose is not rewritten. Each of the 49 gets a dated correcting note,
 * the lw3407 pattern, because a reader in 2030 will not know when the text was
 * written. And the park's own record, #15764, which says nothing about who runs
 * it, is linked to the City of Santa Clarita #394, with the sources.
 *
 * The sources, from Grok's Hart dossier (inventory/review/william-s-hart-
 * sources.md, C21), all official: the County's approval of 6 August 2024, the
 * City's notices of 13 May 2025 (probate court) and 14 July 2025 (ownership).
 *
 * The 49 are found, not listed: every record whose body carries one of the four
 * passages. A record that already has the note is skipped. Idempotent.
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_hart_park_custody_notes.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;

$PARK = 15764; $CITY = 394;
$BARGER = 'https://kathrynbarger.lacounty.gov/l-a-county-supervisors-green-light-transfer-of-historic-william-s-hart-park-and-museum-to-city-of-santa-clarita/';
$CITY_MAY = 'https://santaclarita.gov/blog/2025/05/13/city-moves-forward-with-transfer-of-ownership-of-william-s-hart-park-following-probate-court-petition/';
$CITY_JULY = 'https://santaclarita.gov/blog/2025/07/14/william-s-hart-park-officially-opens-as-citys-40th-park/';
$HEADING = 'Since 14 July 2025';
$NOTE = 'This record describes William S. Hart Park and the Hart Museum as they were run when it was written. On 6 August 2024 the Los Angeles County Board of Supervisors approved their transfer to the City of Santa Clarita (' . $BARGER . '); the Superior Court accepted the probate petition in May 2025 (' . $CITY_MAY . '); and on 14 July 2025 the City took ownership (' . $CITY_JULY . '). The City now owns and operates the park and the museum.';
$PASSAGES = [
    'Today, the Parks and Recreation Department of Los Angeles County operates and maintains William S. Hart Park',
    'which operates the Hart Museum',
    'The Los Angeles County parks department also maintains picnic areas',
    'the same county park staffers who run Hart Park',
];

$elements = Craft::$app->getElements();
$rows = (new \craft\db\Query())->select(['es.elementId'])->from(['es' => '{{%elements_sites}}'])->innerJoin(['el' => '{{%elements}}'], 'el.id = es.elementId')
    ->where(['el.revisionId' => null, 'el.draftId' => null, 'el.dateDeleted' => null])
    ->andWhere(['or', ...array_map(fn($p) => ['like', 'es.content', $p], $PASSAGES)])->column();
$plan = []; $done = []; $count = [];
foreach (array_unique($rows) as $id) {
    $e = Entry::find()->id($id)->status(null)->one();
    if (!$e || !$e->getFieldLayout()->getFieldByHandle('editorNotes')) { continue; }
    $body = (string)$e->body;
    $hit = array_values(array_filter($PASSAGES, fn($p) => str_contains($body, $p)));
    if (!$hit) { continue; }
    $notes = array_values(array_filter($e->editorNotes ?? [], fn($r) => trim((string)($r['note'] ?? '')) !== ''));
    if (array_filter($notes, fn($r) => ($r['heading'] ?? '') === $HEADING)) { $done[] = $id; continue; }
    $plan[$id] = $notes;
    $count[$e->section->handle] = ($count[$e->section->handle] ?? 0) + 1;
    echo '   #' . str_pad($id, 6) . str_pad($e->section->handle, 12) . mb_substr($e->title, 0, 60) . PHP_EOL;
}
echo PHP_EOL . 'NOTE (' . $HEADING . '): ' . $NOTE . PHP_EOL;

$park = Entry::find()->id($PARK)->status(null)->one();
$city = Entry::find()->id($CITY)->status(null)->one();
$parkSet = [];
if (!$park || $park->title !== 'William S. Hart Park' || !$city) { echo PHP_EOL . 'REFUSING the park: #' . $PARK . ' or #' . $CITY . ' is not as seen' . PHP_EOL; }
else {
    $orgs = array_map('intval', $park->placeOrganizations->status(null)->ids());
    if (!in_array($CITY, $orgs, true)) { $parkSet['placeOrganizations'] = array_merge($orgs, [$CITY]); }
    if (!array_filter($park->footnotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')) {
        $parkSet['footnotes'] = [['number' => '1', 'source' => 'editorial-2026', 'note' => 'Owned and operated by the City of Santa Clarita since 14 July 2025: ' . $CITY_JULY . '. The County approved the transfer on 6 August 2024: ' . $BARGER . '. Probate court: ' . $CITY_MAY . '.']];
    }
    echo PHP_EOL . "PARK #$PARK William S. Hart Park: " . ($parkSet ? implode(', ', array_map(fn($k) => $k === 'placeOrganizations' ? 'placeOrganizations + #394 The City of Santa Clarita' : 'a footnote with the three sources', array_keys($parkSet))) : 'already linked') . PHP_EOL;
}
echo PHP_EOL . 'SUMMARY: ' . count($plan) . ' records get the note ' . json_encode($count) . ', ' . count($done) . ' already have it; the park ' . ($parkSet ? 'is linked' : 'unchanged') . '.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if (!$plan && !$parkSet) { echo 'nothing to do; a second run is a no-op' . PHP_EOL; return; }

$short = [];
foreach ($plan as $id => $notes) {
    $e = Entry::find()->id($id)->status(null)->one();
    $notes[] = ['heading' => $HEADING, 'note' => $NOTE, 'position' => 'bottom'];
    $e->setFieldValue('editorNotes', $notes);
    if (!$elements->saveElement($e)) { $short[] = "#$id save failed"; continue; }
    $b = Entry::find()->id($id)->status(null)->one();
    if (!array_filter($b->editorNotes ?? [], fn($r) => ($r['heading'] ?? '') === $HEADING)) { $short[] = "#$id note missing"; }
}
if ($parkSet) {
    $park->setFieldValues($parkSet);
    if (!$elements->saveElement($park)) { $short[] = 'park save failed'; }
    elseif (!in_array($CITY, array_map('intval', Entry::find()->id($PARK)->status(null)->one()->placeOrganizations->status(null)->ids()), true)) { $short[] = 'park not linked to the City'; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode('; ', $short) : 'OK: ' . count($plan) . ' notes; park linked') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_hart_park_custody_notes.php', count($plan) + ($parkSet ? 1 : 0), $short ? 'SHORT: ' . implode('; ', $short) : 'verified', 'Hart Park custody notes; #15764 linked to the City');
if ($short) { throw new \RuntimeException('add_hart_park_custody_notes: ' . implode('; ', $short)); }
