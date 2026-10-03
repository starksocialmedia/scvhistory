/**
 * Rose (Rosemarie) Koscielny #25397: no longer living. Nathan, 3 October 2026:
 * "confirm the 2026 obituary yourself before changing her from living to dead."
 *
 * Confirmed from two reports read on 3 October 2026 (inventory/news/
 * koscielny-obituary-2026-10-03.json): KHTS, hometownstation.com, published
 * 20 August 2026, and SCVNews.com, published 20 August 2026. Both: died
 * Thursday, August 13 [2026], at 74; born July 8, 1952; on the Saugus board
 * twenty years, stepping down in late 2016. The Signal's report
 * (signalscv.com/2026/08/former-saugus-district-board-member-rose-koscielny-dies)
 * refuses automated reads and is not cited.
 *
 * A death date is what makes a record historical (_partials/record/historical.twig);
 * with it her full birth date may be shown, as Boyer's, Darcy's and Pederson's
 * are. The death date is contemporary evidence; the birth date, given in the
 * obituary, retrospective. Her family, named in both reports, is not taken.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/set_koscielny_death.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$root = \Craft::getAlias('@root');
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}"], ["'", "'", '"', '"'], (string)$s)));
$src = json_decode(file_get_contents("$root/inventory/news/koscielny-obituary-2026-10-03.json"), true)['articles'];
$bad = [];
if (count($src) !== 2) { $bad[] = 'not two reports'; }
foreach ($src as $a) {
    foreach (['died on Thursday, Aug. 13', 'at the age of 74', 'born July 8, 1952'] as $ph) { if (!str_contains($ws($a['text']), $ph)) { $bad[] = "{$a['url']} does not read \"$ph\""; } }
    if (!str_starts_with($a['published'], '2026-08-2')) { $bad[] = "{$a['url']} is not dated August 2026"; }
}
/* August 13, 2026 was a Thursday, as both say. */
if ((new \DateTime('2026-08-13'))->format('l') !== 'Thursday') { $bad[] = '13 August 2026 is not a Thursday'; }
$p = Entry::find()->id(25397)->status(null)->one();
if (!$p || $p->title !== 'Rose Koscielny') { $bad[] = '#25397 is not Rose Koscielny'; }
if ($p && trim((string)$p->deathDate) !== '' && trim((string)$p->deathDate) !== 'August 13, 2026') { $bad[] = '#25397 already has another death date: ' . $p->deathDate; }
$PROV = '; set_koscielny_death.php, 3 Oct 2026: died 13 Aug 2026 (KHTS, SCVNews)';
if ($p && mb_strlen(trim((string)$p->recordProvenance . $PROV)) > 255) { $bad[] = 'recordProvenance would exceed 255 characters'; }
$done = $p && $p->deathDateEdtf === '2026-08-13';
echo '#25397 Rose Koscielny: ' . ($done ? 'already set' : 'death August 13, 2026 (contemporary), birth July 8, 1952 (retrospective), full name Rosemarie Koscielny') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $done) { echo 'nothing was written.' . ($APPLY ? '' : ' Set $APPLY = true to apply.') . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
$vals = ['deathDate' => 'August 13, 2026', 'deathDateEdtf' => '2026-08-13', 'deathEvidence' => 'contemporary', 'birthDate' => 'July 8, 1952', 'birthDateEdtf' => '1952-07-08', 'birthEvidence' => 'retrospective',
    'fullName' => 'Rosemarie Koscielny', 'personAliases' => 'Rose Koscielny', 'recordProvenance' => trim((string)$p->recordProvenance . $PROV)];
$p->setFieldValues(array_intersect_key($vals, array_flip($h)));
if (!Craft::$app->getElements()->saveElement($p)) { throw new \RuntimeException(json_encode($p->getFirstErrors())); }
$r = Entry::find()->id(25397)->status(null)->one(); $ok = $r->deathDateEdtf === '2026-08-13' && $r->birthDateEdtf === '1952-07-08';
echo 'READ-BACK ' . ($ok ? 'OK' : 'SHORT') . PHP_EOL;
$applyLog = require "$root/scripts/import/_apply_log.php";
$applyLog('set_koscielny_death.php', 1, $ok ? 'verified' : 'SHORT', 'Rose Koscielny: died 13 August 2026, confirmed from two reports');
if (!$ok) { throw new \RuntimeException('set_koscielny_death: read-back failed'); }
