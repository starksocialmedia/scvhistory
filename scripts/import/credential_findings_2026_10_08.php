/**
 * The overnight credential scan's findings set against the records (Nathan, 8 October 2026, overnight run, items 1 to 3:
 * "Any text-to-image or undescribed edit on a record goes on a list"; for the portraits, "how many carry a credential their
 * record does not mention"). Reads storage/runtime/overnight/cred-scan.json (scan_credentials_2026_10_08.py, which opened
 * the files) and, for each asset with a marker, where it is used and what its record says. Reads only.
 * Writes storage/runtime/overnight/cred-findings.json.
 */
use craft\elements\Asset;
$root = \Craft::getAlias('@root');
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the scan\'s report: markers read from each master and stored copy', "$root/storage/runtime/overnight/cred-scan.json"],
  ['record', 'Craft fields contentCredentials, enhancementMethod, enhancedFrom, and the relations that use each asset', 'what each record says about its file', 'read'],
]);
$S = json_decode(file_get_contents("$root/storage/runtime/overnight/cred-scan.json"), true);
$db = Craft::$app->getDb(); $out = []; $tally = [];
foreach ($S as $r) {
  $m = array_merge($r['masterMarkers'] ?? [], $r['storedMarkers'] ?? []);
  if (!$m) { continue; }
  $txt = implode(' ', $m);
  $t2i = str_contains($txt, 'text_to_image') || str_contains($txt, 'GENERATED');
  $uses = $db->createCommand("select r.sourceId, f.handle, es.title, s.handle sec from scvh_relations r join scvh_fields f on f.id=r.fieldId join scvh_elements e on e.id=r.sourceId join scvh_elements_sites es on es.elementId=e.id left join scvh_entries en on en.id=e.id left join scvh_sections s on s.id=en.sectionId where r.targetId=:id and e.revisionId is null and e.draftId is null and e.dateDeleted is null and f.handle<>'enhancedFrom'", [':id' => $r['id']])->queryAll();
  $said = trim($r['cc'] . ' ' . $r['em']);
  $class = $t2i ? 'text-to-image step in the credential' : 'edited, generative steps';
  $class .= $said === '' ? '; record says nothing' : '; record mentions it';
  $class .= $uses ? '; ON A RECORD' : '; on no record';
  $tally[$class] = ($tally[$class] ?? 0) + 1;
  $out[] = ['id' => $r['id'], 'file' => $r['file'], 'groups' => $r['groups'], 'class' => $class, 'recordSays' => $said, 'markers' => $m,
    'uses' => array_map(fn($u) => "#{$u['sourceId']} {$u['title']} ({$u['sec']}.{$u['handle']})", $uses)];
}
usort($out, fn($a, $b) => strcmp($a['class'], $b['class']));
file_put_contents("$root/storage/runtime/overnight/cred-findings.json", json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
foreach ($tally as $k => $v) { echo "$v  $k" . PHP_EOL; }
foreach ($out as $o) { if (str_contains($o['class'], 'ON A RECORD')) { echo "#{$o['id']} {$o['file']} | {$o['class']} | " . implode('; ', $o['uses']) . PHP_EOL; } }
$P = array_filter($S, fn($r) => in_array('portrait', $r['groups']));
$pm = array_filter($P, fn($r) => ($r['masterMarkers'] ?? []) || ($r['storedMarkers'] ?? []));
$pu = array_filter($pm, fn($r) => trim($r['cc'] . $r['em']) === '');
$noedit = array_filter($P, fn($r) => trim($r['em']) === '');
echo 'portraits: ' . count($P) . ' read; with no edit recorded ' . count($noedit) . ', of which a credential or marker found: ' . count(array_filter($noedit, fn($r) => ($r['masterMarkers'] ?? []) || ($r['storedMarkers'] ?? []))) . '; any portrait with a marker its record does not mention: ' . count($pu) . PHP_EOL;
$how = []; foreach ($noedit as $r) { $how[$r['masterRead']] = ($how[$r['masterRead']] ?? 0) + 1; } echo 'no-edit portraits, how the master was read: ' . json_encode($how) . PHP_EOL;
