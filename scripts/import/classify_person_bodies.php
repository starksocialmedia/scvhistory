/**
 * Labels the person prose that came in from WordPress, and proves it first.
 *
 * A label is a claim about where text came from, so this does not label on a
 * guess. For each person carrying a body it finds the matching person post in
 * inventory/wp_content.json and compares the two, ignoring only whitespace and
 * tags. Identical means the body is that import and nothing has been edited
 * since, and it is labelled wordpress-import-unsourced. Anything that differs,
 * or has no matching post, is listed and left alone: a body somebody has since
 * worked on is not the import any more and wants a person's eye.
 *
 * On 22 September all 33 matched exactly.
 *
 * The label does the hiding. templates/persons/_entry.twig,
 * templates/articles/_entry.twig and _partials/head/meta.twig publish a
 * person's prose only when bodyAuthorship is legacy-leon or editorial-2026,
 * so wordpress-import-unsourced, mixed and unclassified are all off the front
 * end. The text stays in the database and in the control panel, where it is
 * the starting point for the editorial profile that replaces it.
 *
 * Idempotent: a record already carrying a label is left alone, so a hand
 * decision is never overwritten by a re-run.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run:  ddev craft exec "eval(file_get_contents('scripts/import/classify_person_bodies.php'))"
 * Full text of each body in the dry run:
 *       ddev craft exec '$SHOW_FULL = true; eval(file_get_contents("scripts/import/classify_person_bodies.php"));'
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$FULL = !empty($SHOW_FULL);

$LABEL = 'wordpress-import-unsourced';

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

if (!Craft::$app->getFields()->getFieldByHandle('bodyAuthorship')) {
    echo 'bodyAuthorship does not exist yet. Run add_body_authorship_field.php first.' . PHP_EOL;
    echo 'Listing what it would classify anyway:' . PHP_EOL . PHP_EOL;
}
$fieldLive = (bool)Craft::$app->getFields()->getFieldByHandle('bodyAuthorship');

$wpPath = \Craft::getAlias('@root') . '/inventory/wp_content.json';
$wp = json_decode((string)file_get_contents($wpPath), true);
$posts = [];
foreach (($wp['posts'] ?? []) as $p) { if (($p['type'] ?? '') === 'person') { $posts[$p['slug']] = $p; } }
echo 'WordPress person posts on file: ' . count($posts) . '  (' . basename($wpPath) . ')' . PHP_EOL;

$norm = fn(string $s): string => trim(preg_replace('~\s+~', ' ', strip_tags(html_entity_decode($s))));

$plan = []; $listed = []; $already = 0;
foreach (\craft\elements\Entry::find()->section('persons')->status(null)->limit(null)->each() as $p) {
    $body = trim((string)$p->getFieldValue('body'));
    $bio  = trim((string)$p->getFieldValue('authorBio'));
    if ($body === '' && $bio === '') { continue; }

    $current = $fieldLive ? (string)$p->getFieldValue('bodyAuthorship') : '';
    if ($current !== '') { $already++; continue; }

    $post = $posts[$p->slug] ?? null;
    if ($post === null) { $listed[] = [$p, 'no WordPress person post with this slug']; continue; }
    if ($norm($body) !== $norm((string)($post['body'] ?? ''))) { $listed[] = [$p, 'body differs from the WordPress text; somebody has edited it']; continue; }

    $wpBio = (string)(($post['meta'] ?? [])['author_bio'] ?? '');
    $bioSame = $norm($bio) === $norm($wpBio);
    $plan[] = ['e' => $p, 'body' => $body, 'bio' => $bio, 'bioSame' => $bioSame];
}

echo 'already classified, left alone: ' . $already . PHP_EOL;
echo 'to label ' . $LABEL . ': ' . count($plan) . PHP_EOL;
echo 'left for a person: ' . count($listed) . PHP_EOL . PHP_EOL;

printf("%-6s %-34s %7s %7s %-8s %s\n", 'id', 'person', 'body', 'bio', 'bio=wp', 'first line of the body');
foreach ($plan as $r) {
    $first = trim(strip_tags(explode("\n", $r['body'])[0]));
    printf("%-6d %-34s %7d %7d %-8s %s\n", $r['e']->id, mb_substr($r['e']->title, 0, 34),
        mb_strlen($r['body']), mb_strlen($r['bio']), $r['bioSame'] ? 'same' : 'DIFFERS',
        mb_substr($first, 0, 60));
}
foreach ($listed as [$e, $why]) { echo PHP_EOL . 'LEFT  #' . $e->id . ' ' . $e->title . ': ' . $why . PHP_EOL; }

if ($FULL) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'THE TEXT, IN FULL' . PHP_EOL;
    foreach ($plan as $r) {
        echo PHP_EOL . str_repeat('-', 78) . PHP_EOL;
        echo '#' . $r['e']->id . '  ' . $r['e']->title . '  (' . $r['e']->slug . ')' . PHP_EOL;
        echo 'AUTHOR BIO: ' . $r['bio'] . PHP_EOL . PHP_EOL;
        echo 'BODY:' . PHP_EOL . $r['body'] . PHP_EOL;
    }
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    return;
}
if (!$fieldLive) { echo 'bodyAuthorship does not exist; nothing to write to.' . PHP_EOL; return; }

$saved = 0; $failed = [];
foreach ($plan as $r) {
    $e = $r['e'];
    $e->setFieldValue('bodyAuthorship', $LABEL);
    if (Craft::$app->getElements()->saveElement($e)) { $saved++; }
    else { $failed[] = '#' . $e->id . ': ' . json_encode($e->getFirstErrors()); }
}
echo PHP_EOL . 'saved ' . $saved . ' of ' . count($plan) . PHP_EOL;
foreach ($failed as $f) { echo 'FAILED ' . $f . PHP_EOL; }

/* Read back from freshly loaded entries, and check the body is still there:
   this labels prose, it does not remove it. */
$ok = 0; $short = [];
foreach ($plan as $r) {
    $f = \craft\elements\Entry::find()->id($r['e']->id)->status(null)->one();
    $label = (string)$f->getFieldValue('bodyAuthorship');
    $bodyBack = trim((string)$f->getFieldValue('body'));
    if ($label !== $LABEL) { $short[] = '#' . $f->id . ' label reads "' . $label . '"'; continue; }
    if ($bodyBack !== $r['body']) { $short[] = '#' . $f->id . ' the body changed under the label'; continue; }
    $ok++;
}
$readback = ($short ? 'SHORT ' : 'verified ') . $ok . ' of ' . count($plan) . ' labelled, bodies unchanged';
echo 'READ-BACK ' . $readback . PHP_EOL;
foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('classify_person_bodies.php', $saved, $readback,
    $LABEL . '; ' . count($listed) . ' left for a person; prose kept, hidden from the front end');

if ($short || $failed) { throw new \RuntimeException('classify_person_bodies: the write did not land as planned.'); }
