/**
 * #865 (lw2304a, merged 7 October 2026) held "Unknown" in originalPublishDate. A date field shows only a date (Nathan, 4 October
 * 2026), so the word was never shown; the map's own date, filed August 2, 1875, is in its recordDates and caption. Cleared.
 * Idempotent. Dry run by default. Set $APPLY = true.
 */
$APPLY = false;
$e = \craft\elements\Entry::find()->id(865)->status(null)->one(); $v = trim((string)$e->getFieldValue('originalPublishDate'));
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ": #865 originalPublishDate \"$v\"" . ($v === 'Unknown' ? ' -> empty' : ' (left)') . PHP_EOL;
if ($APPLY && $v === 'Unknown') { $e->setFieldValue('originalPublishDate', ''); if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException('save'); }
  $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_865_date_2026_10_07.php', 1, 'verified', '#865 originalPublishDate "Unknown" cleared'); }
