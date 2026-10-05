/**
 * Jan Heidt (#15737): the City's text names her "Janice Heidt", as the 1987 election results do; the archive's record is "Jan Heidt"
 * (Nathan, 4 October 2026: "Confirm which the sources use and whether they are the same person"). The same person: her 1987
 * candidacy (#21958, "Janice Heidt") is tied to her record, and Leon Worden's SC8801 caption has "Janice H. Heidt". The City's text
 * now reads "Janice (Jan) Heidt"; "Janice H. Heidt" joins her aliases.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 */
$APPLY = false;
$c = craft\elements\Entry::find()->id(394)->one(); $p = craft\elements\Entry::find()->id(15737)->one();
$doC = str_contains((string)$c->body, 'Janice Heidt, 8,402'); $doP = !str_contains((string)$p->personAliases, 'Janice H. Heidt');
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ": City text " . ($doC ? 'fix' : 'done') . "; aliases " . ($doP ? 'add' : 'done') . PHP_EOL;
if (!$APPLY) { return; } $n = 0;
if ($doC) { $c->setFieldValue('body', str_replace('Janice Heidt, 8,402', 'Janice (Jan) Heidt, 8,402', (string)$c->body)); if (!Craft::$app->getElements()->saveElement($c)) { throw new \RuntimeException('City'); } $n++; }
if ($doP) { $p->setFieldValue('personAliases', trim((string)$p->personAliases . "\nJanice H. Heidt")); if (!Craft::$app->getElements()->saveElement($p)) { throw new \RuntimeException('Heidt'); } $n++; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_heidt_name_2026_10_04.php', $n, 'verified', 'Janice (Jan) Heidt in the City\'s text; Janice H. Heidt among her aliases');
echo "done: $n writes" . PHP_EOL;
