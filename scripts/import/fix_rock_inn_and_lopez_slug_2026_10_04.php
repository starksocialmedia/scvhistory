/**
 * Two faults the duplicate audit found (inventory/review/duplicate-slugs-2026-10-04.md):
 *  #4911, the Rock Inn photograph from lw3098, carried photoSourceCode LW2823, so its page showed #4549's
 *  picture. Set to LW3098, its own page's code.
 *  #18834 Francisco López had the slug francisco-lopez-2: the base slug belonged to #305, trashed on 3
 *  October when Chico López got his own record. The -2 comes off; the old address redirects.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_rock_inn_and_lopez_slug_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false; echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL; $el = Craft::$app->getElements(); $bad = []; $n = 0;
$r = Entry::find()->id(4911)->status(null)->one();
if (!str_contains((string)$r?->legacyUrl, 'lw3098')) { $bad[] = '#4911 is not lw3098'; }
echo "#4911 {$r?->title}: photoSourceCode {$r?->photoSourceCode} -> LW3098" . PHP_EOL;
$l = Entry::find()->id(18834)->status(null)->one(); $taken = Entry::find()->section($l?->section->handle)->slug('francisco-lopez')->status(null)->exists();
if ($l?->slug !== 'francisco-lopez-2' && $l?->slug !== 'francisco-lopez') { $bad[] = '#18834 slug not as read'; }
if ($taken && $l?->slug !== 'francisco-lopez') { $bad[] = 'francisco-lopez is taken by a live record'; }
echo "#18834 {$l?->title}: slug {$l?->slug} -> francisco-lopez" . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
if ($r->photoSourceCode !== 'LW3098') { $r->setFieldValue('photoSourceCode', 'LW3098'); if (!$el->saveElement($r)) { throw new \RuntimeException('#4911'); } $n++; }
if ($l->slug !== 'francisco-lopez') { $l->slug = 'francisco-lopez'; if (!$el->saveElement($l)) { throw new \RuntimeException('#18834 ' . json_encode($l->getFirstErrors())); } $n++; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_rock_inn_and_lopez_slug_2026_10_04.php', $n, 'verified', 'Rock Inn photo code LW3098; Francisco López slug without -2');
echo "done $n" . PHP_EOL;
