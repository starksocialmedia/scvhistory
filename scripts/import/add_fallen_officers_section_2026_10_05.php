/**
 * Fallen officers: a section of its own (Nathan, 5 October 2026, approving inventory/review/officers-memorial-plan-2026-10-05.md:
 * "Include all fourteen, in two groups"; "its own section at /fallen-officers ... an officer killed on duty is not a war
 * casualty"; "'Fallen officers'. It is what Leon called it and what people say").
 * The record follows the war memorial's: the shared fields as they are used there, and its own fields in place of the wm
 * fields, which are about military service:
 *   foAgency            the agency, related to its organization record
 *   foRank, foAssignment (station or office), foBadge (where published)
 *   foIncidentLocation, foCircumstances (one line)
 *   foValleyTie         killed in the valley / served the valley / of the valley, killed elsewhere / of the valley, off duty
 *   foMemorials         the streets, highways, parks and plaques named for them
 * Section `fallenOfficers`, channel, URLs fallen-officers/{slug}, template fallen-officers/_entry. Writes project config.
 * Safe to run twice. Dry run by default; set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_fallen_officers_section_2026_10_05.php'))"
 */
$APPLY = false;
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $es = Craft::$app->getEntries(); $n = 0; $bad = [];
$TEXT = ['foRank' => ['Officer: rank', false], 'foAssignment' => ['Officer: assignment', false], 'foBadge' => ['Officer: badge', false],
  'foIncidentLocation' => ['Officer: where', false], 'foCircumstances' => ['Officer: circumstances', true], 'foMemorials' => ['Officer: memorials named for them', true]];
foreach ($TEXT as $h => [$name, $multi]) { $f = $fs->getFieldByHandle($h); echo "$h: " . ($f ? 'exists' : 'create') . PHP_EOL;
  if ($APPLY && !$f) { $f = new \craft\fields\PlainText(['name' => $name, 'handle' => $h, 'multiline' => $multi]); if (!$fs->saveField($f)) { throw new \RuntimeException($h); } $n++; } }
if (!$fs->getFieldByHandle('foValleyTie')) { echo "foValleyTie: create\n";
  if ($APPLY) { $f = new \craft\fields\Dropdown(['name' => 'Officer: tie to the valley', 'handle' => 'foValleyTie', 'options' => [
    ['label' => 'Killed in the valley', 'value' => 'killed-here', 'default' => true], ['label' => 'Served the valley', 'value' => 'served-here', 'default' => false],
    ['label' => 'Of the valley, killed elsewhere', 'value' => 'resident-elsewhere', 'default' => false], ['label' => 'Of the valley, off duty', 'value' => 'off-duty', 'default' => false],
    ['label' => 'Killed in the valley on the way to work', 'value' => 'en-route', 'default' => false]]]);
    if (!$fs->saveField($f)) { throw new \RuntimeException('foValleyTie'); } $n++; } } else { echo "foValleyTie: exists\n"; }
if (!$fs->getFieldByHandle('foAgency')) { echo "foAgency: create\n";
  if ($APPLY) { $os = $es->getSectionByHandle('organizations'); $f = new \craft\fields\Entries(['name' => 'Officer: agency', 'handle' => 'foAgency', 'maxRelations' => 1, 'sources' => ['section:' . $os->uid]]);
    if (!$fs->saveField($f)) { throw new \RuntimeException('foAgency'); } $n++; } } else { echo "foAgency: exists\n"; }
$ORDER = ['featuredImage', 'foAgency', 'foRank', 'foAssignment', 'foBadge', 'deathDate', 'deathDateEdtf', 'foIncidentLocation', 'foCircumstances', 'foValleyTie',
  'birthDate', 'birthDateEdtf', 'burialPlace', 'foMemorials', 'body', 'withheldBody', 'footnotes', 'footnotesOn', 'factSources', 'editorNotes',
  'webmasterNoteTop', 'webmasterNoteBottom', 'recordImages', 'recordDocuments', 'neighborhood', 'recordTags', 'legacyKey', 'legacyUrl', 'sourcePath', 'legacyHtml'];
foreach ($ORDER as $h) { if (!$fs->getFieldByHandle($h) && !str_starts_with($h, 'fo')) { $bad[] = "no field $h"; } }
$et = $es->getEntryTypeByHandle('fallenOfficer'); echo 'entry type: ' . ($et ? 'exists' : 'create') . PHP_EOL;
$sec = $es->getSectionByHandle('fallenOfficers'); echo 'section: ' . ($sec ? 'exists' : 'create, fallen-officers/{slug}') . PHP_EOL;
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
if (!$et) {
  $et = new \craft\models\EntryType(['name' => 'Fallen officer', 'handle' => 'fallenOfficer', 'hasTitleField' => true]);
  $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
  $tab = new \craft\models\FieldLayoutTab(['layout' => $layout, 'name' => 'Officer', 'sortOrder' => 1]);
  $els = [new \craft\fieldlayoutelements\entries\EntryTitleField()]; foreach ($ORDER as $h) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h)); }
  $tab->setElements($els); $layout->setTabs([$tab]); $et->setFieldLayout($layout);
  if (!$es->saveEntryType($et)) { throw new \RuntimeException('entry type ' . json_encode($et->getErrors())); } $n++;
}
if (!$sec) {
  $sec = new \craft\models\Section(['name' => 'Fallen officers', 'handle' => 'fallenOfficers', 'type' => \craft\models\Section::TYPE_CHANNEL, 'enableVersioning' => true]);
  $ss = []; foreach (Craft::$app->getSites()->getAllSites() as $site) { $ss[$site->id] = new \craft\models\Section_SiteSettings(['siteId' => $site->id, 'enabledByDefault' => true, 'hasUrls' => true, 'uriFormat' => 'fallen-officers/{slug}', 'template' => 'fallen-officers/_entry']); }
  $sec->setSiteSettings($ss); $sec->setEntryTypes([$et]);
  if (!$es->saveSection($sec)) { throw new \RuntimeException('section ' . json_encode($sec->getErrors())); } $n++;
}
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_fallen_officers_section_2026_10_05.php', $n, 'verified', 'the fallenOfficers section, its entry type and eight fields');
echo "done: $n\n";
