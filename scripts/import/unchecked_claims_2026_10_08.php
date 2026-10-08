/**
 * The shape of what is still unchecked (Nathan, 8 October 2026: "tell me the shape of what is still unchecked: how many
 * claims in the archive rest on a record describing a file rather than the file"). Read only. Each class counts assets
 * whose claim about their file comes from a record (a Craft field, a manifest, a file name, a scan of a copy) and was
 * never set against the file itself. Writes storage/runtime/photo-import/unchecked-claims.json.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/unchecked_claims_2026_10_08.php'))"
 */
use craft\elements\{Asset, Entry};
$root = \Craft::getAlias('@root');
$MAN = ["$root/inventory/raw/scvhistory-manifest-2026-08-20.sha256", '/mnt/reggie/scvhistory-manifest-addendum-2026-10-04.sha256'];
$reads = require "$root/scripts/import/_reads.php";
$reads([
  ['file', 'every stored asset file, hashed here', "$root/web/uploads/archive-media"],
  ['file', 'the drive manifests (a record of the masters, made from Reggie)', $MAN[0]],
  ['file', 'the manifest addendum on Reggie', $MAN[1]],
  ['file', 'every master on Reggie that an asset names, checked for presence', '/mnt/reggie/scvhistory.com'],
  ['record', 'the 6 October credential scan\'s output (storage/runtime/asset_origins.json, content_credentials.json)', 'each asset\'s original', 'not read: no file is opened for its credential here; the scan\'s report says which it opened'],
  ['record', 'Craft fields sourceChecksum, legacySourcePath, sourceUrl, provenanceKind, contentCredentials, enhancementMethod, enhancedFrom, filename', 'each asset\'s stored file and its master', 'not read: the masters are not opened for content credentials or compared with their web copies here; that is what this census counts'],
]);
$man = [];
foreach ($MAN as $mf) { if (is_file($mf)) { foreach (file($mf, FILE_IGNORE_NEW_LINES) as $l) { if (preg_match('~^([0-9a-f]{64})  \./(.+)$~', $l, $m)) { $man[strtolower(rtrim($m[2], '?'))][$m[1]] = true; } } } }
$origins = []; foreach (json_decode(file_get_contents("$root/storage/runtime/asset_origins.json"), true) as $o) { $origins[$o['id']] = $o['origin']; }
$cc = json_decode(file_get_contents("$root/storage/runtime/content_credentials.json"), true);
$ccHow = []; foreach ($cc['assets'] as $r) { if (preg_match('~^#(\d+)~', $r['key'], $m)) { $ccHow[(int)$m[1]] = $r['scanned']; } }
$used = []; foreach ((new \craft\db\Query())->select(['targetId'])->distinct()->from('{{%relations}}')->column() as $t) { $used[(int)$t] = true; }
$C = []; $ids = [];
$add = function (string $k, int $id) use (&$C, &$ids, $used) { $C[$k] = ($C[$k] ?? 0) + 1; $ids[$k][] = $id; if (isset($used[$id])) { $C["$k | on a record"] = ($C["$k | on a record"] ?? 0) + 1; } };
$total = 0;
foreach (Asset::find()->batch(500) as $batch) foreach ($batch as $a) {
  $total++; $l = $a->getFieldLayout(); $g = fn($h) => $l && $l->getFieldByHandle($h) ? trim((string)$a->getFieldValue($h)) : '';
  $sum = strtolower(preg_replace('/^sha256:/i', '', $g('sourceChecksum'))); $lsp = strtolower(rtrim(ltrim($g('legacySourcePath'), '/'), '?')); $kind = $g('provenanceKind');
  $fs = $a->getVolume()->getFs(); $p = rtrim(method_exists($fs, 'getRootPath') ? $fs->getRootPath() : '', '/') . '/' . $a->folderPath . $a->filename;
  $isImg = $a->kind === 'image';
  /* 1. Picture against master: the checksum is the master's, but the stored picture is a web copy made from it; nothing has compared the two. */
  if ($sum !== '' && $lsp !== '' && isset($man[$lsp][$sum])) {
    if (is_file($p) && hash_file('sha256', $p) === $sum) { $add('1a stored file is the master, byte for byte', $a->id); }
    else { $add('1b stored file a web copy of its master; the copy never compared with it', $a->id); }
  }
  /* 2. No checksum at all: what the file is rests on its source sentence alone. */
  if ($sum === '') { $add("2 no checksum recorded (provenanceKind: " . ($kind ?: 'none') . ')', $a->id); }
  /* 3. Content credentials: what the 6 October scan read for this asset. */
  $how = $ccHow[$a->id] ?? null;
  if ($how === null) { $add('3a credentials never scanned (asset made after 4 October)' . ($lsp !== '' ? ', master on Reggie' : ', no master named'), $a->id); }
  elseif (str_starts_with($how, 'volume copy only')) { $add('3b credentials looked for only in the stored copy, which Craft re-saved without metadata', $a->id); }
  /* 4. A master named that is not on Reggie. */
  if ($lsp !== '' && !isset($man[$lsp]) && !is_file('/mnt/reggie/scvhistory.com/' . $lsp)) { $add('4 legacySourcePath names a file not on Reggie', $a->id); }
  /* 5. Derived-from claims. */
  if ($l && $l->getFieldByHandle('enhancedFrom') && $a->getFieldValue('enhancedFrom')->exists()) { $add('5 enhancedFrom: a claim this file was made from another', $a->id); }
  /* 6. Made 7 and 8 October from the original site by the photograph import and the folder pass: given to a record by file and folder name. */
  if ($kind === 'legacy-mirror' && $a->dateCreated->format('Y-m-d') >= '2026-10-07') { $add('6 attached 7 or 8 October by file or folder name', $a->id); }
}
ksort($C);
echo "$total assets" . PHP_EOL; foreach ($C as $k => $v) { echo str_pad((string)$v, 6, ' ', STR_PAD_LEFT) . "  $k" . PHP_EOL; }
file_put_contents("$root/storage/runtime/photo-import/unchecked-claims.json", json_encode($ids));
