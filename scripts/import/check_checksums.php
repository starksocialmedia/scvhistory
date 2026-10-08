/**
 * Every recorded master checksum is checked against the master, not against the file it sits on (Nathan, 8 October
 * 2026: "Yes, the checksum audit into check_render, failing on 'matches neither the master named nor the file'"; "A check
 * that cannot fail is worse than no check, because it reports safety"). The standing form of checksum_audit_2026_10_08.py.
 * Run by check_render; returns ['ok' => bool]. Read only.
 *
 * An asset that names a master on the original site (legacySourcePath) and records a checksum is held to the drive
 * manifest, which was made from Reggie, not from Craft. It fails when the recorded checksum is not the manifest's:
 *   - "matches neither the master named nor the file": the 6 October shape, a substitute file's hash filed as the master's;
 *   - "the stored file's own hash, not the master named": the same fault where the substitute is the stored file itself.
 * An asset that names no master is compared with its stored file and reported, not failed: its checksum describes a
 * download kept or not kept outside Craft (inventory/review/checksum-audit-2026-10-08.md has the full classes).
 * Shown to fail: run against the ten assets swapped on 6 October as they stood that morning, it fails all ten.
 */
use craft\elements\Asset;
$root = \Craft::getAlias('@root');
$MAN = ["$root/inventory/raw/scvhistory-manifest-2026-08-20.sha256", '/mnt/reggie/scvhistory-manifest-addendum-2026-10-04.sha256'];
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'the drive manifest of 20 August, a list of every master\'s checksum made from Reggie', $MAN[0]],
  ['file', 'the manifest addendum of 4 October, on Reggie', $MAN[1]],
  ['record', 'Craft fields sourceChecksum and legacySourcePath on every asset', 'the master on Reggie', 'not read: the manifest stands for the masters; hashing 4,000 masters on every render check is the full audit\'s job (checksum_audit_2026_10_08.py)'],
  ['record', 'Craft field sourceChecksum, filename and provenanceKind', 'the stored file', 'read'],
  ['record', 'the same fields', 'originals kept outside Craft (inventory/incoming, sole-copies, elections, storage/masters)', 'not read: they decide no failure here; the full audit hashes them'],
]);
$man = [];
foreach ($MAN as $mf) {
  if (!is_file($mf)) { continue; }
  foreach (file($mf, FILE_IGNORE_NEW_LINES) as $l) {
    if (preg_match('~^([0-9a-f]{64})  \./(.+)$~', $l, $m)) { $man[strtolower(rtrim($m[2], '?'))][$m[1]] = true; }
  }
}
$c = ['match their master' => 0, 'master named, not in the manifests read' => 0, 'no master named: the stored file\'s own hash' => 0, 'no master named: a download kept or not kept outside Craft' => 0, 'stored file missing' => 0];
$bad = [];
foreach (Asset::find()->batch(500) as $batch) {
  foreach ($batch as $a) {
    $l = $a->getFieldLayout();
    if (!$l || !$l->getFieldByHandle('sourceChecksum')) { continue; }
    $sum = strtolower(preg_replace('/^sha256:/i', '', trim((string)$a->getFieldValue('sourceChecksum'))));
    if ($sum === '') { continue; }
    $lsp = $l->getFieldByHandle('legacySourcePath') ? strtolower(rtrim(ltrim(trim((string)$a->getFieldValue('legacySourcePath')), '/'), '?')) : '';
    $fs = $a->getVolume()->getFs(); $p = rtrim(method_exists($fs, 'getRootPath') ? $fs->getRootPath() : '', '/') . '/' . $a->folderPath . $a->filename;
    if ($lsp !== '' && isset($man[$lsp])) {
      if (isset($man[$lsp][$sum])) { $c['match their master']++; continue; }
      $own = is_file($p) ? hash_file('sha256', $p) : null;
      $bad[] = "#{$a->id} {$a->filename}: " . ($own === $sum ? "the stored file's own hash, not the master named ($lsp)" : "matches neither the master named ($lsp) nor the file");
      continue;
    }
    if ($lsp !== '') { $c['master named, not in the manifests read']++; continue; }
    if (!is_file($p)) { $c['stored file missing']++; continue; }
    $c[hash_file('sha256', $p) === $sum ? 'no master named: the stored file\'s own hash' : 'no master named: a download kept or not kept outside Craft']++;
  }
}
echo 'checksums: ' . implode(', ', array_map(fn($k, $v) => "$v $k", array_keys($c), $c)) . ($bad ? '' : '; none fails') . PHP_EOL;
foreach ($bad as $b) { echo "  CHECKSUM FAIL: $b" . PHP_EOL; }
return ['ok' => !$bad];
