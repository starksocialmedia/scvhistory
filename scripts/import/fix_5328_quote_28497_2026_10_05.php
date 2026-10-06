/**
 * The last Education Code 5328 splice (inventory/review/spliced-quotations-audit-2026-10-05.md): Denis DeFigueiredo's 2007 term
 * (#28497) still quotes the statute with "..." joining the nominee's clause to the appointee's. The quotation is corrected as on
 * the other 27 (unopposed_not_appointed_2026_10_05.php). The term's own claim, that the 2007 election was not held, rests only
 * on the contest's absence from CEDA (the appointed-terms audit: UNSUPPORTED) and is left for Nathan. Idempotent. Dry run by
 * default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_5328_quote_28497_2026_10_05.php'))"
 */
$APPLY = false;
$OLD = 'when the election is not held the nominee "shall be seated at the organizational meeting of the board ... as if elected at a district election."';
$NEW = 'when the election is not held "the qualified person or persons nominated shall be seated at the organizational meeting of the board"; the board appoints only when no one, or too few, were nominated.';
$h = craft\elements\Entry::find()->id(28497)->status(null)->one();
$rows = array_values(array_filter(array_map(fn($r) => ['number' => (string)$r['number'], 'note' => (string)$r['note'], 'source' => (string)$r['source']], iterator_to_array($h->footnotes)), fn($r) => $r['note'] !== ''));
$hit = 0; foreach ($rows as &$r) { if (str_contains($r['note'], $OLD)) { $r['note'] = str_replace($OLD, $NEW, $r['note']); $hit++; } } unset($r);
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ": #28497 {$h->title}: " . ($hit ? "$hit quotation corrected" : 'corrected already') . PHP_EOL;
if ($APPLY && $hit) { $h->setFieldValue('footnotes', $rows); if (!Craft::$app->getElements()->saveElement($h)) { throw new \RuntimeException('#28497'); } $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_5328_quote_28497_2026_10_05.php', 1, 'verified', 'the last 5328 splice'); }
