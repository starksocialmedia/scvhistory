/**
 * Fails if docs/DATA-MODEL.md has fallen behind the schema.
 *
 * A data model that drifts is worse than none, because it is consulted and
 * believed. This compares every field handle in the live schema against the
 * document and reports any the document does not mention, and any the document
 * mentions that no longer exist.
 *
 * Called from check_render.php, so a schema script that adds a field without
 * regenerating the document cannot pass.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/check_data_model.php'))"
 */

$DOC = \Craft::getAlias('@root') . '/docs/DATA-MODEL.md';
$svc = Craft::$app->getEntries();

if (!file_exists($DOC)) {
    echo 'DATA-MODEL FAIL: docs/DATA-MODEL.md does not exist. Run generate_data_model.php.' . PHP_EOL;
    return ['ok' => false, 'missing' => [], 'stale' => []];
}
$doc = file_get_contents($DOC);

$live = [];
foreach ($svc->getAllSections() as $sec) {
    foreach ($sec->getEntryTypes() as $et) {
        foreach ($et->getFieldLayout()->getCustomFields() as $f) { $live[$f->handle] = true; }
    }
}

$missing = [];
foreach (array_keys($live) as $h) {
    if (!str_contains($doc, '`' . $h . '`')) { $missing[] = $h; }
}

/* The other direction: a handle the document documents and the schema no
   longer has. Only checked inside the field tables, so prose naming a field
   that was removed on purpose does not trip it. */
/* Only the field tables under "Entry types". The identifier table has the same
   row shape and names fields that are planned rather than present, and reading
   it here reports a document that is ahead of the schema as one that is behind
   it. */
$documented = [];
$from = strpos($doc, '## Entry types');
$to   = strpos($doc, '## Controlled values');
if ($from !== false && $to !== false && $to > $from) {
    $body = substr($doc, $from, $to - $from);
    if (preg_match_all('~^\| `([a-zA-Z0-9_]+)` \|~m', $body, $m)) {
        foreach ($m[1] as $h) { $documented[$h] = true; }
    }
}
$stale = array_values(array_diff(array_keys($documented), array_keys($live)));

$ok = !$missing && !$stale;
if ($ok) {
    echo 'DATA-MODEL ok: ' . count($live) . ' field handles, all documented' . PHP_EOL;
} else {
    echo 'DATA-MODEL FAIL' . PHP_EOL;
    if ($missing) {
        echo '  in the schema, absent from the document (' . count($missing) . '):' . PHP_EOL;
        foreach (array_slice($missing, 0, 20) as $h) { echo '    ' . $h . PHP_EOL; }
        echo '  run: ddev craft exec "eval(file_get_contents(\'scripts/import/generate_data_model.php\'))"' . PHP_EOL;
    }
    if ($stale) {
        echo '  in the document, absent from the schema (' . count($stale) . '):' . PHP_EOL;
        foreach (array_slice($stale, 0, 20) as $h) { echo '    ' . $h . PHP_EOL; }
    }
}

return ['ok' => $ok, 'missing' => $missing, 'stale' => $stale];
