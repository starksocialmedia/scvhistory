/**
 * The City's lead paragraph, made plain on the two things readers get wrong (Nathan, 4 October 2026: "It is a general-law
 * city with a council-manager government, not a charter city, and the mayor rotates annually among the council rather than
 * being elected ... the rotating mayoralty is the thing most readers get wrong"). The first paragraph only; the rest of the
 * text and the footnotes are unchanged. The sources are the existing notes 1 (Community Profile), 2 (the City Council page)
 * and 5 (Ordinance 23-4).
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fix_city_lead_2026_10_04.php'))"
 */
$APPLY = false;
$e = craft\elements\Entry::find()->id(394)->one(); $b = (string)$e->body;
$OLD = "It is a general-law city with a council-manager government: five council members, elected to staggered four-year terms, who each December choose one of themselves to serve a year as mayor.[1][2]";
$NEW = "It is a general-law city, not a charter city: it runs under the state's general laws rather than a charter of its own. Its government is council-manager: five council members, elected to staggered four-year terms, make policy, and a city manager they appoint runs the City's departments.[1] The mayor is not elected by the voters. Each December the council chooses one of its own members to serve a year as mayor and another as Mayor Pro Tem, so the mayoralty rotates.[2] The council was elected at large until 2024; it is now elected by district, from five districts.[5]";
$has = str_contains($b, $NEW); $can = str_contains($b, $OLD);
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . ': ' . ($has ? 'already done' : ($can ? 'replace the lead sentence' : 'REFUSED: the lead sentence is not as expected')) . PHP_EOL;
if (!$APPLY || $has || !$can) { return; }
$e->setFieldValue('body', str_replace($OLD, $NEW, $b)); if (!Craft::$app->getElements()->saveElement($e)) { throw new \RuntimeException(json_encode($e->getFirstErrors())); }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('fix_city_lead_2026_10_04.php', 1, 'verified', 'The City\'s lead: general-law, council-manager, the rotating mayoralty, by district');
echo 'done' . PHP_EOL;
