/**
 * Splits the historical era list into two taxonomies that answer two different
 * questions: WHEN a thing happened, and WHAT IT WAS ABOUT.
 *
 * WHY THE CURRENT LIST CANNOT BE USED FOR EITHER
 *
 * It overlaps. St. Francis Dam Era (1926-1928) sits inside Silent Film Era
 * (1910-1929); Northridge Recovery (1994-2000) sits inside Mall & Growth Era
 * (1994-2010). Every adjacent pair also shares its boundary year: 1821 belongs
 * to Spanish Colonial and to Mexican Rancho Era both. So a date cannot pick an
 * era, and 71 of the 764 articles land in an overlap on their subject years
 * alone.
 *
 * The overlaps are not a mistake in the ranges. They are two kinds of thing in
 * one list. Silent Film Era, Railroad & Oil Era and Northridge Recovery are
 * subjects wearing a date; Great Depression and World War II are periods. A
 * list that mixes them can never be partitioned, because the film era and the
 * dam disaster genuinely do coincide.
 *
 * WHAT THIS PROPOSES
 *
 *   ERAS      contiguous, exclusive, gapless. Every year belongs to exactly
 *             one. An article carries ONE. Ends are pulled back by a year so
 *             no boundary is shared: Spanish Colonial ends 1820, not 1821.
 *
 *   THEMES    a new category group. An article carries ANY NUMBER, or none.
 *             The two demoted eras become themes, which is what they always
 *             were: the dam collapse is a subject, not a period of valley
 *             history, and it is discussed in articles printed across sixty
 *             years.
 *
 * The fifteen themes were measured against the corpus before being proposed,
 * not invented: each one matches between 2% and 22% of the 764 articles. A
 * theme nothing is about is a term somebody has to read past forever.
 *
 * NOTHING IS DELETED. The two demoted eras are reported with their usage and
 * left in place; removing a category that an entry points at is a decision for
 * the control panel, after the themes exist to move them to.
 *
 * Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_era_partition_and_themes.php'))"
 */

$APPLY = false;

/* The partition. from, to inclusive; to is the year before the next one starts. */
$ERAS = [
    ['Tataviam & Native Peoples (to 1768)',   null, 1768, 'Tataviam & Native Peoples (Pre-1769)'],
    ['Spanish Colonial (1769–1820)',          1769, 1820, 'Spanish Colonial (1769–1821)'],
    ['Mexican Rancho Era (1821–1847)',        1821, 1847, 'Mexican Rancho Era (1821–1848)'],
    ['American Frontier (1848–1875)',         1848, 1875, 'American Frontier (1848–1876)'],
    ['Railroad & Oil Era (1876–1909)',        1876, 1909, 'Railroad & Oil Era (1876–1910)'],
    ['Silent Film Era (1910–1928)',           1910, 1928, 'Silent Film Era (1910–1929)'],
    ['Great Depression (1929–1940)',          1929, 1940, 'Great Depression (1929–1941)'],
    ['World War II (1941–1945)',              1941, 1945, 'World War II (1941–1945)'],
    ['Postwar Boom (1946–1964)',              1946, 1964, 'Postwar Boom (1945–1965)'],
    ['Incorporation Struggle (1965–1986)',    1965, 1986, 'Incorporation Struggle (1965–1987)'],
    ['Cityhood Era (1987–1993)',              1987, 1993, 'Cityhood Era (1987–1994)'],
    ['Mall & Growth Era (1994–2009)',         1994, 2009, 'Mall & Growth Era (1994–2010)'],
    ['Contemporary (2010–present)',           2010, null, 'Contemporary (2010–Present)'],
];

/* Demoted out of the era list, because each is a subject and not a period. */
$DEMOTE = ['St. Francis Dam Era (1926–1928)', 'Northridge Recovery (1994–2000)'];

/* Measured against the corpus before being proposed. The percentage is how
   many of the 764 articles mention the theme at all, which is a ceiling on how
   many will carry it, not a prediction. */
$THEMES = [
    'Incorporation & Cityhood'  => '22% of articles mention it',
    'Schools & Education'       => '21%',
    'Film & Television'         => '20%; what Silent Film Era was really naming',
    'Railroad'                  => '18%; half of what Railroad & Oil Era was naming',
    'Ranching & Agriculture'    => '18%',
    'Mining & Gold'             => '15%',
    'Development & Growth'      => '10%; what Mall & Growth Era was really naming',
    'Oil'                       => '8%; the other half of Railroad & Oil Era',
    'Water & Aqueduct'          => '7%',
    'Stagecoach & Early Roads'  => '6%',
    'Native Peoples'            => '6%',
    'Fire & Flood'              => '5%',
    'Aerospace'                 => '3%',
    'St. Francis Dam'           => '2%; demoted from the era list',
    'Northridge Earthquake'     => '2%; demoted from the era list',
];

$FIELD = 'articleThemes';
$GROUP = 'theme';
$ON_TYPES = ['article'];

$cats = \Craft::$app->getCategories();
$fs = \Craft::$app->getFields();
$svc = \Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING, this writes to the database and to project config' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 80) . PHP_EOL;

/* ------------------------------------------------- what is in use today */

$byTitle = [];
foreach (\craft\elements\Category::find()->group('historicalEra')->status(null)->limit(null)->all() as $c) {
    $byTitle[(string)$c->title] = $c;
}

$usage = [];
foreach ($byTitle as $t => $c) {
    $n = 0;
    foreach (['articles', 'persons', 'places', 'organizations', 'photographs'] as $sec) {
        $n += (int)\craft\elements\Entry::find()->section($sec)
            ->relatedTo(['targetElement' => $c, 'field' => 'historicalEra'])->status(null)->count();
    }
    $usage[$t] = $n;
}

echo 'ERA PARTITION' . PHP_EOL;
printf("   %-40s %-14s %s\n", 'TITLE AFTER', 'SPAN', 'CHANGE');
$prevTo = null; $gaps = [];
foreach ($ERAS as [$new, $from, $to, $old]) {
    $span = ($from === null ? '…' : $from) . '–' . ($to === null ? 'now' : $to);
    $c = $byTitle[$old] ?? null;
    $change = $c === null ? 'OLD TITLE NOT FOUND: ' . $old
        : ($new === $old ? 'unchanged' : 'retitle, ' . $usage[$old] . ' entries keep pointing at it');
    printf("   %-40s %-14s %s\n", $new, $span, $change);
    if ($prevTo !== null && $from !== null && $from !== $prevTo + 1) {
        $gaps[] = $prevTo . ' to ' . $from;
    }
    if ($to !== null) { $prevTo = $to; }
}
echo '   ' . ($gaps ? 'GAPS: ' . implode(', ', $gaps) : 'contiguous, no gaps, no shared boundary years') . PHP_EOL;

echo PHP_EOL . 'DEMOTED FROM THE ERA LIST (not deleted)' . PHP_EOL;
foreach ($DEMOTE as $t) {
    $c = $byTitle[$t] ?? null;
    printf("   %-40s %s\n", $t, $c === null ? 'not found'
        : ($usage[$t] . ' entries point at it; becomes a theme, move them in the CP'));
}

echo PHP_EOL . 'THEMES, a new category group "' . $GROUP . '"' . PHP_EOL;
$g = $cats->getGroupByHandle($GROUP);
printf("   group: %s\n", $g ? 'exists already' : 'would create');
foreach ($THEMES as $t => $why) { printf("   %-28s %s\n", $t, $why); }

echo PHP_EOL . 'FIELD' . PHP_EOL;
$f = $fs->getFieldByHandle($FIELD);
printf("   %-28s %s\n", $FIELD, $f ? 'exists already' : 'would create, Categories, multiple, source ' . $GROUP);
foreach ($ON_TYPES as $th) {
    $et = $svc->getEntryTypeByHandle($th);
    $on = false;
    if ($et) { foreach ($et->getFieldLayout()->getCustomFields() as $cf) { if ($cf->handle === $FIELD) { $on = true; } } }
    printf("   %-28s %s\n", 'on ' . $th, $on ? 'already on the layout' : 'would add');
}

echo PHP_EOL . str_repeat('-', 80) . PHP_EOL;
printf("eras after: %d, partitioned   themes: %d   demoted: %d\n",
    count($ERAS), count($THEMES), count($DEMOTE));
echo 'articles carrying an era today: '
   . (int)\craft\elements\Entry::find()->section('articles')
        ->relatedTo(['targetElement' => array_values($byTitle), 'field' => 'historicalEra'])
        ->status(null)->count() . ' of 764' . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

/* retitle in place, so every existing relation survives */
$retitled = 0;
foreach ($ERAS as [$new, $from, $to, $old]) {
    $c = $byTitle[$old] ?? null;
    if (!$c || $new === $old) { continue; }
    $c->title = $new;
    if (\Craft::$app->elements->saveElement($c)) { $retitled++; }
    else { echo 'FAILED retitling ' . $old . ': ' . json_encode($c->getErrors()) . PHP_EOL; }
}

if (!$g) {
    $g = new \craft\models\CategoryGroup();
    $g->name = 'Theme';
    $g->handle = $GROUP;
    $g->setSiteSettings([
        (new \craft\models\CategoryGroup_SiteSettings([
            'siteId' => \Craft::$app->sites->getPrimarySite()->id,
            'hasUrls' => false,
        ])),
    ]);
    $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Category::class]);
    $g->setFieldLayout($layout);
    if (!$cats->saveGroup($g)) { echo 'FAILED creating the group: ' . json_encode($g->getErrors()) . PHP_EOL; return; }
    $g = $cats->getGroupByHandle($GROUP);
}

$made = 0;
foreach ($THEMES as $t => $why) {
    $ex = \craft\elements\Category::find()->group($GROUP)->title($t)->status(null)->one();
    if ($ex) { continue; }
    $c = new \craft\elements\Category();
    $c->groupId = $g->id;
    $c->title = $t;
    if (\Craft::$app->elements->saveElement($c)) { $made++; }
    else { echo 'FAILED creating theme ' . $t . ': ' . json_encode($c->getErrors()) . PHP_EOL; }
}

if (!$f) {
    $f = new \craft\fields\Categories();
    $f->name = 'Themes';
    $f->handle = $FIELD;
    $f->source = 'group:' . $g->uid;
    $f->branchLimit = null;
    $f->instructions = 'What the article is about, as against when it happened. Any number, '
        . 'or none. The era says when; a theme says what.';
    if (!$fs->saveField($f)) { echo 'FAILED creating the field: ' . json_encode($f->getErrors()) . PHP_EOL; return; }
    $f = $fs->getFieldByHandle($FIELD);
}

$added = 0;
foreach ($ON_TYPES as $th) {
    $et = $svc->getEntryTypeByHandle($th);
    if (!$et) { continue; }
    $layout = $et->getFieldLayout();
    $on = false;
    foreach ($layout->getCustomFields() as $cf) { if ($cf->handle === $FIELD) { $on = true; } }
    if ($on) { continue; }
    $tabs = $layout->getTabs();
    $ti = 0; $at = null;
    foreach ($tabs as $i => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && $el->getField()->handle === 'historicalEra') { $ti = $i; $at = $ei + 1; }
        }
    }
    $els = $tabs[$ti]->getElements();
    array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($f)]);
    $tabs[$ti]->setElements($els);
    $layout->setTabs($tabs);
    $et->setFieldLayout($layout);
    if ($svc->saveEntryType($et)) { $added++; }
    else { echo 'FAILED adding to ' . $th . ': ' . json_encode($et->getErrors()) . PHP_EOL; }
}

/* Read back from fresh queries and from a live entry, not from what was set. */
$eraTitles = array_map(fn($e) => $e[0], $ERAS);
$backEras = array_map(fn($c) => (string)$c->title,
    \craft\elements\Category::find()->group('historicalEra')->status(null)->limit(null)->all());
$partitioned = count(array_intersect($eraTitles, $backEras));
$backThemes = (int)\craft\elements\Category::find()->group($GROUP)->status(null)->count();
$probe = \craft\elements\Entry::find()->section('articles')->status(null)->one();
$live = false;
if ($probe) { foreach ($probe->getFieldLayout()->getCustomFields() as $cf) { if ($cf->handle === $FIELD) { $live = true; } } }

echo PHP_EOL . 'READ-BACK' . PHP_EOL;
printf("   %-28s %-18s %s\n", 'eras retitled', $retitled . ' written', 'info');
printf("   %-28s %-18s %s\n", 'partition present', $partitioned . ' of ' . count($ERAS), $partitioned === count($ERAS) ? 'pass' : 'FAIL');
printf("   %-28s %-18s %s\n", 'themes in the group', $backThemes . ' of ' . count($THEMES), $backThemes >= count($THEMES) ? 'pass' : 'FAIL');
printf("   %-28s %-18s %s\n", 'field on a live article', $live ? 'yes' : 'no', $live ? 'pass' : 'FAIL');

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_era_partition_and_themes.php', $retitled + $made + $added,
    ($partitioned === count($ERAS) && $live ? 'verified: ' : 'FAILED: ')
        . 'eras ' . $partitioned . '/' . count($ERAS) . ', themes ' . $backThemes . ', field ' . ($live ? 'live' : 'missing'),
    'two demoted eras left in place for the CP');
echo PHP_EOL . 'config/project will be dirty. Commit it before deploying: see docs/DEPLOY.md step 1.' . PHP_EOL;
