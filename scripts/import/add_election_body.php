/**
 * Elections for more than one body (Nathan, 29 September 2026: "build the body
 * field on the election record and the district records for trustee areas and
 * water divisions").
 *
 *   electionBody      Entries -> organizations, one. The council's elections
 *                     take the City (#394), the body its officeHoldings name;
 *                     a school board's take its district.
 *   electionDistrict  Entries -> places, one. The trustee area, water division
 *                     or council district a contest was for; empty at large.
 *                     A district is a place record (districtKind, districtNumber),
 *                     as candidacyDistrict and holdingDistrict already expect.
 *   districtKind      two options: council-district, water-division. The
 *                     existing trustee-area covers the school boards.
 *   title format      the body and district in the title, so that five school
 *                     boards on one November ballot are five titles, not one:
 *                     "City Council election, April 10, 2012". A district's
 *                     label is the part of its title after the comma, so every
 *                     district title reads "Body, Trustee Area 3".
 *                     "Saugus Union School District board election, Trustee
 *                     Area 3, November 8, 2016". Slugs do not change: Craft keeps
 *                     a slug once set, so every published election URL stands.
 *
 * Its own script, run before the imports that use it, because a record saved in
 * the same request as a new option or field validates against the old schema.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_election_body.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to project config' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$fs = Craft::$app->getFields(); $svc = Craft::$app->getEntries();
$NEW = [
    'electionBody' => ['The body', 'organizations', 'The body the election filled seats on: the City for the council, a district for its board.'],
    'electionDistrict' => ['The district', 'places', 'The trustee area, water division or council district the contest was for. Empty for an at-large seat.'],
];
$KINDS = ['council-district' => 'Council district', 'water-division' => 'Water board division'];
$FORMAT = "{% set b = object.electionBody.one() %}{% set d = object.electionDistrict.one() %}{{ b and b.slug != 'city-of-santa-clarita' ? b.title ~ ' board' : 'City Council' }} election{{ d ? ', ' ~ d.title|split(', ')|last }}, {{ object.electionDate }}";   /* under 255 characters: the column's limit (a longer one failed in test) */
$type = $svc->getEntryTypeByHandle('Election');
if (!$type) { echo 'REFUSING: no Election type' . PHP_EOL; return; }
$inLayout = array_map(fn($f) => $f->handle, $type->getFieldLayout()->getCustomFields());
$todo = 0;
foreach ($NEW as $h => [$label, $sec]) {
    $f = $fs->getFieldByHandle($h);
    echo str_pad($h, 18) . ($f ? 'exists' : "create: Entries -> $sec, one") . (in_array($h, $inLayout, true) ? ', in the Election layout' : ', add to the Election layout') . PHP_EOL;
    if (!$f || !in_array($h, $inLayout, true)) { $todo++; }
}
$dk = $fs->getFieldByHandle('districtKind');
$haveKinds = array_map(fn($o) => $o['value'], $dk->options);
foreach ($KINDS as $v => $l) { echo str_pad('districtKind', 18) . (in_array($v, $haveKinds, true) ? "has $v" : "add $v ($l)") . PHP_EOL; if (!in_array($v, $haveKinds, true)) { $todo++; } }
echo str_pad('title format', 18) . ($type->titleFormat === $FORMAT ? 'set' : 'from "' . $type->titleFormat . '" to the body-and-district format') . PHP_EOL;
if ($type->titleFormat !== $FORMAT) { $todo++; }
/* What the titles will be, rendered now against real records. */
$v = Craft::$app->getView();
foreach (['april-10-2012-election', 'november-5-2024-election'] as $slug) {
    $e = Entry::find()->section('elections')->slug($slug)->one();
    if ($e) { echo '   ' . str_pad($e->title, 34) . '-> ' . $v->renderObjectTemplate($FORMAT, $e) . ' (with the body set: City Council)' . PHP_EOL; }
}
echo 'Existing slugs are kept; only titles change.' . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . "nothing was written ($todo changes). Set \$APPLY = true to apply." . PHP_EOL; return; }

foreach ($NEW as $h => [$label, $sec, $instr]) {
    if (!$fs->getFieldByHandle($h)) {
        $f = new \craft\fields\Entries(); $f->name = $label; $f->handle = $h; $f->instructions = $instr;
        $f->sources = ['section:' . $svc->getSectionByHandle($sec)->uid]; $f->maxRelations = 1;
        if (!$fs->saveField($f)) { throw new \RuntimeException("field $h: " . json_encode($f->getErrors())); }
    }
}
$fs->refreshFields();
$type = $svc->getEntryTypeByHandle('Election');
$layout = $type->getFieldLayout(); $tabs = $layout->getTabs(); $els = $tabs[0]->getElements();
foreach (array_keys($NEW) as $h) { if (!in_array($h, array_map(fn($f) => $f->handle, $layout->getCustomFields()), true)) { $els[] = new \craft\fieldlayoutelements\CustomField($fs->getFieldByHandle($h)); } }
$tabs[0]->setElements($els); $layout->setTabs($tabs); $type->setFieldLayout($layout);
$type->titleFormat = $FORMAT;
if (!$svc->saveEntryType($type)) { throw new \RuntimeException('Election type: ' . json_encode($type->getErrors())); }
$dk = $fs->getFieldByHandle('districtKind');
$opts = $dk->options;
foreach ($KINDS as $val => $l) { if (!in_array($val, array_map(fn($o) => $o['value'], $opts), true)) { $opts[] = ['label' => $l, 'value' => $val, 'default' => false]; } }
if (count($opts) !== count($dk->options)) { $dk->options = $opts; if (!$fs->saveField($dk)) { throw new \RuntimeException('districtKind: ' . json_encode($dk->getErrors())); } }

$fs->refreshFields();
$t2 = $svc->getEntryTypeByHandle('Election');
$have = array_map(fn($f) => $f->handle, $t2->getFieldLayout()->getCustomFields());
$short = array_merge(array_diff(array_keys($NEW), $have), array_diff(array_keys($KINDS), array_map(fn($o) => $o['value'], $fs->getFieldByHandle('districtKind')->options)), $t2->titleFormat === $FORMAT ? [] : ['title format']);
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_election_body.php', $todo, $short ? 'SHORT: ' . implode(', ', $short) : 'verified', 'electionBody and electionDistrict; council-district and water-division; the title format');
if ($short) { throw new \RuntimeException('add_election_body: ' . implode(', ', $short)); }
