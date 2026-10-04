/**
 * Jason Gibbs's District 3 term (#27410), from December 2024, recorded as the County has it (Nathan, 4 October 2026:
 * "record it as the County has it, appointed in lieu of election, with a footnote that he was the only candidate.
 * The County's own category is the fact"). The Registrar-Recorder's final list of cancelled elections for 5 November
 * 2024 (archive document #25151) lists "Santa Clarita (Council District 3)" under appointment in lieu of election
 * due to insufficiency of candidates: no more candidates filed than the one seat, so no vote was taken.
 * selectionMethod becomes appointed; the start rests on the County's list (certified). The earlier footnote stays.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_gibbs_district3_appointed_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
$h = Entry::find()->id(27410)->one(); $doc = Entry::find()->id(25151)->one();
if ($h?->holdingPerson->one()?->id !== 23091 || $h->holdingDistrict->one()?->id !== 25155 || !str_contains((string)$doc?->sourcePath, 'cancelled-elections-november-2024')) { echo 'REFUSING: #27410 or #25151 not as expected' . PHP_EOL; return; }
$NOTE = 'County of Los Angeles Registrar-Recorder/County Clerk, final list of cancelled elections, November 5, 2024, ' . $doc->sourcePath . ': "Santa Clarita (Council District 3)" is listed under "appointment in lieu of election due to insufficiency of candidates." He was the only candidate, so no vote was taken and he was appointed to the seat.';
$has = in_array($NOTE, array_column($h->footnotes, 'note'), true);
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ": #27410 {$h->selectionMethod->value} -> appointed; footnote " . ($has ? 'present' : 'add') . PHP_EOL;
if (!$APPLY) { return; }
$rows = $h->footnotes; if (!$has) { $rows[] = ['number' => (string)(count($rows) + 1), 'note' => $NOTE, 'source' => 'editorial-2026']; }
$h->setFieldValues(['selectionMethod' => 'appointed', 'startEvidence' => 'certified', 'footnotes' => $rows, 'footnotesOn' => [$doc->id]]);
if (!Craft::$app->getElements()->saveElement($h)) { throw new \RuntimeException(json_encode($h->getFirstErrors())); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_gibbs_district3_appointed_2026_10_04.php', 1, 'verified', 'Gibbs, District 3 from December 2024: appointed in lieu of election, as the County lists it');
echo 'done' . PHP_EOL;
