/**
 * Every script in scripts/import says what it read before it reports a number (Nathan, 8 October 2026: "Before a
 * census reports a number it states what it read, and reading a Craft field about a file is not reading the file. Where a
 * census could read the file itself and does not, it says so in its own output. Write it as a rule that constrains the
 * scripts."). The helper is _reads.php (_reads.py for Python). Run by check_render; returns ['ok' => bool].
 *
 * Holds every .php, .py and .sh in scripts/import that is not in reads-baseline.txt (the 650 scripts that existed when the
 * rule was made) and does not start with an underscore. Such a script fails when:
 *   1. it prints anything and never calls reads() (a .sh prints "READ, before any number:" itself);
 *   2. it prints before it calls reads();
 *   3. its code names a Craft field that describes a file (FIELDS below) and its reads() call does not list that field
 *      as a record, so a field about a file cannot be read without the output saying it was a record;
 *   4. it declares a file read and contains no call that reads a file.
 * What this cannot see: whether the file a script opens is the one it declares, or a field read under a renamed key in a
 * JSON dump (the dump script that wrote the key is held to rule 3 instead). Those stay with the reader of the output.
 */
$root = \Craft::getAlias('@root') . '/scripts/import/';
$reads = require $root . '_reads.php';
$reads([['file', 'every script in scripts/import not in reads-baseline.txt, as text', $root],
        ['file', 'reads-baseline.txt', $root . 'reads-baseline.txt']]);
$FIELDS = ['sourceChecksum', 'legacySourcePath', 'sourceUrl', 'provenanceKind', 'contentCredentials', 'enhancementMethod', 'enhancedFrom', 'filename'];
$FILECALL = [
  'php' => '~\b(hash_file|file_get_contents|fopen|getimagesize|scandir|glob|exec|shell_exec|filesize|exif_read_data|replaceAssetFile|copy)\s*\(~',
  'py' => '~\b(open|os\.walk|os\.listdir|os\.scandir|os\.path\.getsize|os\.stat|Image\.open|subprocess\.\w+)\s*\(|hashlib~',
  'sh' => '~\b(sha256sum|shasum|identify|exiftool|convert|pdfinfo|find|stat|cat)\b~',
];
$base = array_flip(array_filter(array_map('trim', file($root . 'reads-baseline.txt')), fn($l) => $l !== '' && $l[0] !== '#'));
$bad = []; $held = 0;
foreach (glob($root . '*.{php,py,sh}', GLOB_BRACE) as $path) {
  $f = basename($path);
  /* This check names the fields as text to look for; it reads no field. */
  if ($f[0] === '_' || isset($base[$f]) || $f === 'check_census_reads.php') { continue; }
  $held++;
  $ext = pathinfo($f, PATHINFO_EXTENSION); $src = file_get_contents($path);
  $code = $ext === 'php' ? preg_replace(['~/\*.*?\*/~s', '~^\s*(//|#).*$~m'], '', $src) : preg_replace('~^\s*#.*$~m', '', $src);
  if ($ext === 'sh') {
    $first = preg_match('~\becho\b~', $code, $m, PREG_OFFSET_CAPTURE) ? $m[0][1] : null;
    $decl = strpos($code, 'READ, before any number:');
    if ($first !== null && ($decl === false || $decl > $first + 40)) { $bad[] = "$f: prints before it says what it read"; }
    if ($first !== null && $decl !== false && !preg_match('~the file itself:|A RECORD, NOT THE FILE:|NOT READ:~', $code)) { $bad[] = "$f: its READ block lists nothing"; }
    continue;
  }
  $printRe = $ext === 'php' ? '~\b(echo|print|printf|print_r|var_dump)\b~' : '~\bprint\s*\(~';
  $callRe = $ext === 'php' ? '~\$reads\s*\(~' : '~(?<![\w.])reads\s*\(~';
  $first = preg_match($printRe, $code, $m, PREG_OFFSET_CAPTURE) ? $m[0][1] : null;
  $call = preg_match($callRe, $code, $m, PREG_OFFSET_CAPTURE) ? $m[0][1] : null;
  if ($first === null) { continue; }
  if ($call === null) { $bad[] = "$f: prints and never calls reads()"; continue; }
  if ($first < $call) { $bad[] = "$f: prints before it calls reads()"; }
  /* The reads() call's own text: from the call to the matching close of its list. */
  $decl = substr($code, $call, 4000); $depth = 0;
  for ($i = strpos($decl, '('); $i < strlen($decl); $i++) { if ($decl[$i] === '(') $depth++; elseif ($decl[$i] === ')' && --$depth === 0) { $decl = substr($decl, 0, $i + 1); break; } }
  $rest = str_replace($decl, '', $code);
  foreach ($FIELDS as $h) {
    if (preg_match('~\b' . $h . '\b~', $rest) && !preg_match('~[\'"]record[\'"][^\]\)]*\b' . $h . '\b~', $decl)) { $bad[] = "$f: reads $h, a field about a file, and does not list it as a record"; }
  }
  if (preg_match('~[\'"]file[\'"]\s*,~', $decl) && !preg_match($FILECALL[$ext], $rest)) { $bad[] = "$f: declares a file read and has no call that reads a file"; }
}
echo 'census reads: ' . $held . ' scripts made since the rule, ' . ($bad ? count($bad) . ' failing' : 'each says what it read before any number') . PHP_EOL;
foreach ($bad as $b) { echo "  CENSUS READS FAIL: $b" . PHP_EOL; }
return ['ok' => !$bad];
