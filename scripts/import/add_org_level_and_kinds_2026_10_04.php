/**
 * The level of government a body sits at, and the kinds the /organizations groups need (Nathan, 4 October 2026: "Yes to
 * the level field. A list in a template would go stale and the grouping is a property of the body"; the groups "Valley
 * government" and "County, state and federal"; "Rename to 'Schools and colleges', add 'Missions and churches', add
 * 'Ranchos'"; "ranchos to Ranchos, the three businesses to Businesses, Felton School to Schools").
 *
 *  1. orgLevel, a Dropdown on the organization layout beside orgType: valley, county, state, federal. Set on every
 *     government body: the City and its commissions, SCV Water and the former water and redevelopment bodies are the
 *     valley's; the County, its Board of Supervisors and its Sheriff's Department the county's; the Assembly and Senate
 *     the state's; Congress and the House federal.
 *  2. orgType gains "rancho". Rancho El Tejon and Rancho San Francisco become ranchos; Acton Hotel, Southern Hotel and
 *     Valencia Marketplace businesses; Felton School a school. Porta Bella is left untyped, for Nathan.
 * Writes project config (a new field, a new option, the layout). Idempotent. Dry run by default. Set $APPLY = true.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_org_level_and_kinds_2026_10_04.php'))"
 */
use craft\elements\Entry;
$APPLY = false;
echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
$fs = Craft::$app->getFields(); $svc = Craft::$app->getEntries(); $el = Craft::$app->getElements();
$LEVEL = ['valley' => ['The City of Santa Clarita', 'Arts Commission', 'Parks, Recreation and Community Services Commission', 'Planning Commission', 'Santa Clarita Valley Water', 'Castaic Lake Water Agency', 'Newhall County Water District', 'Newhall Redevelopment Committee'],
    'county' => ['County of Los Angeles', 'Los Angeles County Board of Supervisors', 'Los Angeles County Sheriff\'s Department'],
    'state' => ['California State Assembly', 'California State Senate'], 'federal' => ['United States Congress', 'United States House of Representatives']];
$TYPE = ['rancho' => ['Rancho El Tejon', 'Rancho San Francisco'], 'business' => ['Acton Hotel', 'Southern Hotel', 'Valencia Marketplace'], 'school' => ['Felton School']];
$find = fn($t) => Entry::find()->section('organizations')->status(null)->title($t)->one();
$bad = [];
foreach ($LEVEL as $lv => $ts) { foreach ($ts as $t) { $e = $find($t); if (!$e) { $bad[] = "no $t"; } elseif (($e->orgType->value ?? '') !== 'government') { $bad[] = "$t is not typed government"; } } }
foreach ($TYPE as $k => $ts) { foreach ($ts as $t) { $e = $find($t); if (!$e) { $bad[] = "no $t"; } elseif ($e->orgType->value && $e->orgType->value !== $k) { $bad[] = "$t already typed {$e->orgType->value}"; } } }
$gov = Entry::find()->section('organizations')->status(null)->orgType('government')->all(); $named = array_merge(...array_values($LEVEL));
foreach ($gov as $g) { if (!in_array($g->title, $named, true)) { $bad[] = "government body with no level given: {$g->title}"; } }
echo 'orgLevel: ' . ($fs->getFieldByHandle('orgLevel') ? 'exists' : 'create') . '; orgType rancho: ' . (in_array('rancho', array_column($fs->getFieldByHandle('orgType')->options, 'value')) ? 'exists' : 'add') . PHP_EOL;
foreach ($LEVEL as $lv => $ts) { echo "  $lv: " . implode('; ', $ts) . PHP_EOL; } foreach ($TYPE as $k => $ts) { echo "  type $k: " . implode('; ', $ts) . PHP_EOL; }
echo 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY || $bad) { return; }
$n = 0;
$f = $fs->getFieldByHandle('orgLevel');
if (!$f) { $f = new \craft\fields\Dropdown(); $f->name = 'Level'; $f->handle = 'orgLevel'; $f->instructions = 'For a government body: the level it sits at. The valley\'s own bodies; the County; the State; the federal government. Sets the body\'s group on /organizations.';
    $f->options = [['label' => '', 'value' => '', 'default' => true], ['label' => 'The valley', 'value' => 'valley', 'default' => false], ['label' => 'County', 'value' => 'county', 'default' => false], ['label' => 'State', 'value' => 'state', 'default' => false], ['label' => 'Federal', 'value' => 'federal', 'default' => false]];
    if (!$fs->saveField($f)) { throw new \RuntimeException(json_encode($f->getFirstErrors())); } $f = $fs->getFieldByHandle('orgLevel'); $n++; }
$ot = $fs->getFieldByHandle('orgType');
if (!in_array('rancho', array_column($ot->options, 'value'))) { $o = $ot->options; $o[] = ['label' => 'Rancho', 'value' => 'rancho', 'default' => false]; $ot->options = $o; if (!$fs->saveField($ot)) { throw new \RuntimeException('orgType'); } $n++; }
$type = $svc->getEntryTypeByHandle('organization'); $layout = $type->getFieldLayout();
if (!in_array('orgLevel', array_map(fn($c) => $c->handle, $layout->getCustomFields()), true)) {
    $tabs = $layout->getTabs(); $ti = 0; $at = null;
    foreach ($tabs as $i => $tab) { foreach ($tab->getElements() as $j => $e) { if ($e instanceof \craft\fieldlayoutelements\CustomField && $e->getField()->handle === 'orgType') { $ti = $i; $at = $j + 1; } } }
    $els = $tabs[$ti]->getElements(); array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($f)]); $tabs[$ti]->setElements($els); $layout->setTabs($tabs); $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { throw new \RuntimeException(json_encode($type->getFirstErrors())); } $n++;
}
foreach ($LEVEL as $lv => $ts) { foreach ($ts as $t) { $e = $find($t); if (($e->orgLevel->value ?? '') !== $lv) { $e->setFieldValue('orgLevel', $lv); if (!$el->saveElement($e)) { throw new \RuntimeException($t); } $n++; } } }
foreach ($TYPE as $k => $ts) { foreach ($ts as $t) { $e = $find($t); if (($e->orgType->value ?? '') !== $k) { $e->setFieldValue('orgType', $k); if (!$el->saveElement($e)) { throw new \RuntimeException($t . ' ' . json_encode($e->getFirstErrors())); } $n++; } } }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php'; $applyLog('add_org_level_and_kinds_2026_10_04.php', $n, 'verified', 'orgLevel on organizations, set on every government body; orgType rancho; seven untyped records typed (Porta Bella left)');
echo "done: $n writes" . PHP_EOL;
