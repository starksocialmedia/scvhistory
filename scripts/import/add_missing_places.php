/**
 * Creates the place records the corpus names but the archive does not hold.
 *
 * Nothing here is invented. Every body is built from sentences that exist in
 * inventory/legacy, every community is the one the text or the legacy page
 * title places the site in, and every haunting claim is attributed to whoever
 * made it rather than stated as fact. Coordinates are left empty deliberately:
 * a guessed coordinate is worse than none, because it looks like a measurement.
 *
 * Three records, not four. Heritage Junction already exists as "Heritage
 * Junction Historic Park", slug heritage-junction, and is left alone.
 *
 * The haunted fields are set on two of the three. Heritage Junction and Felton
 * School host Halloween events, which is a programme rather than a claim, and
 * their haunted fields stay empty.
 *
 * legacyUrl is left empty on all three, which is a decision rather than an
 * omission: see $LEGACY_CANDIDATES below.
 *
 * Idempotent. A record that already exists by slug or by title is reported and
 * left untouched; nothing here ever edits an existing record.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_missing_places.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

/**
 * legacyUrl stays empty on all three.
 *
 * legacyUrl means "the legacy page this record was migrated from". These three
 * are being made from mentions inside other people's articles, not migrated
 * from a page of their own, and the legacy site has no page that stands for any
 * of them: what it has is photographs and articles that name them. Filling the
 * field would claim a provenance the record does not have.
 *
 * The candidates are listed so the decision can be made rather than lost. Set
 * one here and re-run if you want it.
 */
$LEGACY_CANDIDATES = [
    'ruiz-cemetery' => [
        '/scvhistory/scvhs_ruizcemeterycensus1992.htm  Ruiz Cemetery Census, Eagle Scout Project, SCV Historical Society, 1992',
        '/scvhistory/signal/worden/lw050896.htm        Spooky Happenings at Ruiz Cemetery, Worden, 1996',
        '/scvhistory/lw060502a.htm                     Ruiz Cemetery on Fire, 2002',
    ],
    'newhall-ranch-house' => [
        '/scvhistory/margaretroutledge.htm             The Mysterious Gunshot Death of Margaret Routledge, 1916',
        '/scvhistory/hs7901.htm                        Newhall Ranch House in Valencia',
        '/scvhistory/hs4001.htm                        Newhall Ranch House (Original Location), n.d.',
    ],
    'felton-school' => [
        '/scvhistory/np6102.htm                        Felton Schoolhouse at Mentryville, 1961',
        '/scvhistory/ch1260.htm                        Felton School and Dance Hall',
    ],
];

$PLACES = [
    [
        'slug' => 'ruiz-cemetery',
        'title' => 'Ruiz Cemetery',
        'community' => 'San Francisquito Canyon',
        'body' => "A quarter-acre burial ground on a hill above the San Francisquito creekbed, "
            . "filled with shade trees and almost 100 grave markers. The cemetery predates the "
            . "St. Francis Dam disaster; one marker dates back to 1888, and some graves may be "
            . "older. Six members of the Ruiz family died when the dam broke on March 12, 1928: "
            . "the parents, Rosaria and Enrique, and their four children, aged eight to thirty. "
            . "They rest together with other victims of the disaster. A section known as "
            . "\"Heartbreak Corner\" is reserved for infants.",
        'bodyFrom' => 'Leon Worden, "Spooky Happenings at Ruiz Cemetery", The Signal, May 8, 1996',
        'communityFrom' => 'the article places it "on a hill above the San Francisquito creekbed"; '
            . 'the legacy page titles read "San Francisquito Canyon | Ruiz Cemetery"',
        'haunted' => [
            'status' => 'reported',
            'account' => "Joyce Ponton and her late husband Andrew reported strange events after "
                . "moving to the property, never in the cemetery itself but on the ground below "
                . "where the floodwaters had raged. A cast-iron horse trough that would have taken "
                . "a crane to lift was found moved several feet and turned to face the opposite "
                . "direction, still full of water with none spilled and no footprints or drag marks "
                . "in the sand. The handprint of a small child appeared in wet paint on a door jamb "
                . "in a house they had moved onto the site, at a time when they had no small "
                . "children; a grown daughter reported sometimes hearing a baby cry. Other canyon "
                . "residents told of figures roaming the hills at night. Mrs. Ponton, who says she "
                . "does not believe in ghosts, allowed only that the trough \"was strange. That's "
                . "one thing we've never been able to explain.\" By 1996 she said nothing had "
                . "happened for over ten years.",
            'source' => 'Leon Worden, "Spooky Happenings at Ruiz Cemetery", The Signal, May 8, 1996, '
                . 'quoting Joyce Ponton, owner of the land',
        ],
    ],
    [
        'slug' => 'newhall-ranch-house',
        'title' => 'Newhall Ranch House',
        'community' => 'Newhall',
        'body' => "After the Southern Hotel burned down on October 10, 1888, the eldest Newhall "
            . "son, Henry Gregory Newhall, added a two-story front section onto an existing ranch "
            . "house and used the structure as headquarters for the Newhall Ranch. His wife Mary "
            . "and their four children were the most frequent occupants. After Henry Gregory's "
            . "death in 1903 the house was used occasionally by a younger brother, Walter Scott "
            . "Newhall, who died in 1906. The house was later moved to Heritage Junction.",
        'bodyFrom' => 'Jerry Reynolds, "49. Reflections", History of the Santa Clarita Valley',
        'communityFrom' => 'the legacy page titles read "Heritage Junction | Newhall Ranch House", '
            . 'and Heritage Junction is in Newhall. See the note below: the house did not start there',
        'haunted' => [
            'status' => 'legend',
            'account' => "The house is said to be haunted by a figure known as the \"Blue Lady\", "
                . "and by other presences. Reynolds records the belief without naming a witness, "
                . "an incident or a date.",
            'source' => 'Jerry Reynolds, "49. Reflections", History of the Santa Clarita Valley: '
                . '"The Newhall Ranch House, as it is known, is believed to be haunted by a '
                . 'mysterious \'Blue Lady\' and other beings."',
        ],
    ],
    [
        'slug' => 'felton-school',
        'title' => 'Felton School',
        'community' => 'Pico Canyon',
        'body' => "Before the school opened, children in the Pico Canyon oil field were sent to "
            . "Newhall to be educated. The Felton School opened its doors in October 1885, named "
            . "for Charles Felton, president of the Pacific Coast Oil Company. Keeping the five "
            . "pupils the Felton School District needed to continue was a recurring difficulty "
            . "through the last years of the Pico Canyon field. The district was absorbed into the "
            . "Newhall School District in 1933, after again falling short of the five-student "
            . "minimum.",
        'bodyFrom' => 'Jerry Reynolds, "43. Boom Town"; A.B. Perkins, "6. Oil and Newhall" and the '
            . 'Editor\'s Notes to the Perkins series',
        'communityFrom' => 'the legacy page titles read "Pico Canyon | Felton Schoolhouse at '
            . 'Mentryville" and "Pico Canyon | Felton School and Dance Hall"',
        'haunted' => null,
    ],
];

$SECTION = 'places';
$elements = Craft::$app->getElements();
$svc = Craft::$app->getEntries();

$section = $svc->getSectionByHandle($SECTION);
$type = $svc->getEntryTypeByHandle('place');
if (!$section || !$type) { echo 'places section or place entry type not found' . PHP_EOL; return; }

$hasField = function (\craft\base\ElementInterface $el, string $handle): bool {
    $layout = $el->getFieldLayout();
    if (!$layout) { return false; }
    foreach ($layout->getCustomFields() as $f) { if ($f->handle === $handle) { return true; } }
    return false;
};

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$would = 0; $skipped = 0;

foreach ($PLACES as $p) {
    $existing = \craft\elements\Entry::find()->section($SECTION)->slug($p['slug'])->status(null)->one()
        ?: \craft\elements\Entry::find()->section($SECTION)->title($p['title'])->status(null)->one();
    if ($existing) {
        echo PHP_EOL . $p['title'] . ': already exists as "' . $existing->title . '" (#' . $existing->id
            . ', ' . $existing->slug . '), left alone' . PHP_EOL;
        $skipped++;
        continue;
    }

    $community = \craft\elements\Category::find()->group('neighborhood')->title($p['community'])->one();

    echo PHP_EOL . str_repeat('-', 78) . PHP_EOL;
    echo $p['title'] . '  (' . $p['slug'] . ')' . PHP_EOL;
    echo '  body        ' . mb_substr($p['body'], 0, 96) . '…' . PHP_EOL;
    echo '  drawn from  ' . $p['bodyFrom'] . PHP_EOL;
    echo '  community   ' . $p['community'] . ($community ? '' : '  *** NO SUCH COMMUNITY TERM ***') . PHP_EOL;
    echo '  because     ' . $p['communityFrom'] . PHP_EOL;
    echo '  placeLat    left empty, to be measured rather than guessed' . PHP_EOL;
    echo '  placeLng    left empty' . PHP_EOL;
    echo '  legacyUrl   left empty; candidates:' . PHP_EOL;
    foreach ($LEGACY_CANDIDATES[$p['slug']] ?? [] as $c) { echo '                ' . $c . PHP_EOL; }

    if ($p['haunted']) {
        echo '  hauntedStatus   ' . $p['haunted']['status'] . PHP_EOL;
        echo '  hauntedAccount  ' . mb_substr($p['haunted']['account'], 0, 96) . '…' . PHP_EOL;
        echo '  hauntedSource   ' . $p['haunted']['source'] . PHP_EOL;
    } else {
        echo '  haunted fields  left empty; a Halloween event is a programme, not a claim' . PHP_EOL;
    }

    $would++;
    if (!$APPLY) { continue; }

    $e = new \craft\elements\Entry();
    $e->sectionId = $section->id;
    $e->typeId = $type->id;
    $e->title = $p['title'];
    $e->slug = $p['slug'];
    $e->enabled = true;

    $values = ['body' => $p['body']];
    if ($community) { $values['neighborhood'] = [$community->id]; }
    if ($p['haunted']) {
        $values['hauntedStatus'] = $p['haunted']['status'];
        $values['hauntedAccount'] = $p['haunted']['account'];
        $values['hauntedSource'] = $p['haunted']['source'];
    }
    foreach ($values as $h => $v) {
        try { $e->setFieldValue($h, $v); }
        catch (\Throwable $ex) { echo '  set ' . $h . ' failed: ' . $ex->getMessage() . PHP_EOL; }
    }

    echo '  ' . ($elements->saveElement($e) ? 'created #' . $e->id : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
}

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
echo 'records that would be created: ' . $would . PHP_EOL;
echo 'already present, left alone:   ' . $skipped . PHP_EOL;
echo PHP_EOL . 'Coordinates are empty on all of them, deliberately. A guessed coordinate looks' . PHP_EOL;
echo 'like a measurement, and this archive is read by people who will plot it.' . PHP_EOL;
if (!$APPLY) { echo PHP_EOL . 'nothing written.' . PHP_EOL; }
