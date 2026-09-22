/**
 * Audits every person record against the locality policy.
 *
 * A person record requires a Santa Clarita Valley connection the articles
 * document: lived, worked, owned, built, founded, filmed, buried or acted here.
 * A national figure written about by a local columnist is a subject, not a
 * resident, and Q. David Bowers has no more business being a record here than
 * he would in a Vermont town's archive that also ran his column.
 *
 * WHAT IT MEASURES
 *
 * For each person: how many articles name them, which collections those
 * articles belong to, and whether ANY of them sits outside a collection whose
 * subject is national. Coins is the obvious one, being 259 columns about
 * numismatics by a man who happened to live here.
 *
 * A person named only inside such a collection is a candidate for removal. A
 * person named anywhere else is not, however national they are: John Wayne is
 * in the archive because he was at Melody Ranch, and the article that says so
 * is not a coin column.
 *
 * It proposes and does not decide. Read only.
 *
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/audit_person_locality.php'))"
 */

/* Collections whose subject is national rather than local. A person named only
   here has not been shown to have been here. */
$NATIONAL = ['coins'];

$ARTICLE_FIELDS = ['subjectPerson', 'writtenBy', 'editedBy', 'relatedPersons'];

$people = \craft\elements\Entry::find()->section('persons')->status(null)
    ->orderBy('title asc')->limit(null)->all();

$todayProv = 'created from review, 2026-09-21';

$rows = [];
foreach ($people as $p) {
    $articles = [];
    foreach ($ARTICLE_FIELDS as $fh) {
        foreach (\craft\elements\Entry::find()->section('articles')
                     ->relatedTo(['targetElement' => $p, 'field' => $fh])->status(null)->limit(null)->all() as $a) {
            $articles[$a->id] = $a;
        }
    }

    $colls = []; $outside = 0; $outsideEg = '';
    foreach ($articles as $a) {
        $c = null;
        foreach ($a->getFieldLayout()->getCustomFields() as $f) {
            if ($f->handle === 'partOfCollection') { $c = $a->partOfCollection->one(); }
        }
        $slug = $c ? $c->slug : '(none)';
        $colls[$slug] = ($colls[$slug] ?? 0) + 1;
        if (!in_array($slug, $NATIONAL, true)) {
            $outside++;
            if ($outsideEg === '') { $outsideEg = $a->title; }
        }
    }

    $prov = '';
    foreach ($p->getFieldLayout()->getCustomFields() as $f) {
        if ($f->handle === 'recordProvenance') { $prov = trim((string)$p->getFieldValue('recordProvenance')); }
    }

    $rows[] = [
        'entry' => $p,
        'articles' => count($articles),
        'collections' => $colls,
        'outside' => $outside,
        'outsideEg' => $outsideEg,
        'new' => $prov === $todayProv,
        'wikidataId' => (function ($p) {
            foreach ($p->getFieldLayout()->getCustomFields() as $f) {
                if ($f->handle === 'wikidataId') { return trim((string)$p->getFieldValue('wikidataId')); }
            }
            return '';
        })($p),
    ];
}

$new = array_values(array_filter($rows, fn($r) => $r['new']));
$old = array_values(array_filter($rows, fn($r) => !$r['new']));

echo 'PERSON RECORDS: ' . count($rows) . '   created today: ' . count($new)
   . '   older: ' . count($old) . PHP_EOL;
echo 'collections treated as national: ' . implode(', ', $NATIONAL) . PHP_EOL . PHP_EOL;

$candidates = array_values(array_filter($rows, fn($r) => $r['articles'] > 0 && $r['outside'] === 0));
$noArticles = array_values(array_filter($rows, fn($r) => $r['articles'] === 0));

echo 'NAMED ONLY INSIDE A NATIONAL COLLECTION (' . count($candidates) . '):' . PHP_EOL;
printf("   %-32s %-6s %-9s %s\n", 'PERSON', 'ARTS', 'WIKIDATA', 'COLLECTIONS');
foreach ($candidates as $r) {
    printf("   %-32s %-6d %-9s %s\n", mb_substr($r['entry']->title, 0, 31), $r['articles'],
        $r['wikidataId'] ?: '-',
        implode(', ', array_map(fn($k, $v) => $k . ' ' . $v, array_keys($r['collections']), $r['collections'])));
}

echo PHP_EOL . 'NO ARTICLES AT ALL (' . count($noArticles) . '):' . PHP_EOL;
foreach ($noArticles as $r) {
    printf("   %-32s %s\n", mb_substr($r['entry']->title, 0, 31), $r['new'] ? 'created today' : '');
}

$kept = array_values(array_filter($rows, fn($r) => $r['outside'] > 0));
echo PHP_EOL . 'DOCUMENTED OUTSIDE A NATIONAL COLLECTION, so kept (' . count($kept) . ')' . PHP_EOL;
printf("   %-32s %-6s %-6s %s\n", 'PERSON', 'ARTS', 'LOCAL', 'AN ARTICLE THAT IS NOT A COIN COLUMN');
foreach (array_slice($kept, 0, 18) as $r) {
    printf("   %-32s %-6d %-6d %s\n", mb_substr($r['entry']->title, 0, 31), $r['articles'],
        $r['outside'], mb_substr($r['outsideEg'], 0, 40));
}
if (count($kept) > 18) { echo '   ... and ' . (count($kept) - 18) . ' more' . PHP_EOL; }

echo PHP_EOL . 'Nothing was written. This proposes; the call is a person\'s.' . PHP_EOL;
