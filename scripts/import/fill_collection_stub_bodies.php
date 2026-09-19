/**
 * Replaces the generated placeholder body on a collection with prose the
 * evidence supports.
 *
 * Eleven of the thirteen collections carry a body of the form "X is a series in
 * the Santa Clarita Valley historical archive." It is the same importer that
 * left "X is a named place…" on twelve of the places, and it is what a reader
 * gets today on every one of the Signal and Old Town Newhall series.
 *
 * The evidence is the two sitemaps, which between them hold 5,751 crawled paths
 * with titles and kinds, and the index pages read from the live legacy site.
 * Counts and date ranges below are derived from those rather than remembered:
 * article counts from the crawl, date ranges from the datestamp in each
 * article's filename, printed titles and authors from the index pages. Where a
 * figure comes from the index rather than the crawl the entry says so, because
 * sitemap.json is truncated at 5,000 pages and undercounts the larger trees.
 *
 * Same guard as fill_place_stub_bodies.php: a body is written only while it
 * still matches the placeholder exactly. Anything somebody has written is never
 * touched.
 *
 * Where the evidence is thin the entry is short. Newsmaker of the Week gets two
 * sentences, because its tree redirects off the archive and two pages are all
 * that is here.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/fill_collection_stub_bodies.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }

$STUB = '~^.{0,60}\s+is a series in the Santa Clarita Valley historical archive\.?~u';

$FILL = [
    'worden' => [
        'body' => "Columns and features by Leon Worden, published in The Signal and gathered on "
            . "the legacy site as \"Selections From Leon Worden\". The run reaches from February "
            . "1995 to November 2009 and is the largest single-author series in the archive: 197 "
            . "article pages in the tree, and 219 recorded in the extraction once the /old/ "
            . "subdirectory is included. Worden wrote local history as reporting, going to the "
            . "people who were there, and the by-line \"By Leon Worden\" appears on 143 of the "
            . "219 pages. Subjects run from the St. Francis Dam and the Ruiz Cemetery to the "
            . "naming of streets and the fate of buildings.",
        'from' => [
            'index page /scvhistory/signal/worden/index.htm, printed title "Selections From Leon Worden"',
            'sitemap crawl: 197 article pages under /scvhistory/signal/worden/',
            'inventory/legacy/worden.json: 219 pages, and "By Leon Worden" on 143 of them',
            'date range 1995-02-01 to 2009-11-22, from the datestamps in the article filenames',
        ],
    ],
    'boston' => [
        'body' => "Santa Clarita Valley history by the Signal columnist John Boston, gathered on "
            . "the legacy site under the heading \"Santa Clarita Valley History by John Boston\". "
            . "It is a short run: the index lists six pieces, of which five sit in the tree, dated "
            . "between June 2000 and October 2001. Among them is a three-part account of law and "
            . "order in the early valley, \"Laying Down the Law in Early Santa Clarita\". The "
            . "index carries the notice ©2000-2003 John Boston & SCV Historical Society.",
        'from' => [
            'index page /scvhistory/signal/boston/jbindex.htm, printed heading "SANTA CLARITA '
                . 'VALLEY HISTORY BY JOHN BOSTON" and the copyright line',
            'sitemap crawl: 5 article pages in the tree; the index lists a sixth that links outside it',
            'date range 2000-06-18 to 2001-10-04, from the article filenames',
        ],
    ],
    'manzer' => [
        'body' => "Columns by Darryl Manzer, who grew up in Mentryville, written in 2006 and "
            . "carried on the legacy site as \"Darryl Manzer: 'Way Back When' in the Santa Clarita "
            . "Valley\". Twenty-two pieces sit in the tree, dated between April and December 2006, "
            . "on subjects from Mentryville and the oil field to local politics. The index goes on "
            . "listing entries to 2014, but those later columns were published at scvnews.com and "
            . "are not part of this archive.",
        'from' => [
            'index page /scvhistory/signal/manzer/index.htm, printed title "Darryl Manzer: \'Way '
                . 'Back When\' in the Santa Clarita Valley"',
            'sitemap crawl: 22 article pages, date range 2006-04-16 to 2006-12-10 from the filenames',
            'the index carries 79 links to scvnews.com for the later run',
        ],
    ],
    'coins' => [
        'body' => "\"Making Cents\", the weekly coin column of Dr. Sol Taylor, carried by The "
            . "Signal and gathered on the legacy site under the heading \"Dr. Sol Taylor, the "
            . "Lincoln Cent Expert\". The index lists 218 dated columns running from March 2005 to "
            . "January 2010, on the Lincoln cent above all and on mints, auctions, grading and "
            . "collecting generally. The column is not local history and sits here because The "
            . "Signal ran it.",
        'from' => [
            'index page /scvhistory/signal/coins/index.htm, printed headings "DR. SOL TAYLOR / The '
                . 'Lincoln Cent Expert" and "Making Cents: Weekly Coin Columns by Dr. Taylor"',
            '218 dated article links counted on that index; the sitemap crawl reached only 37 of '
                . 'them, because sitemap.json stopped at its 5,000 page limit',
            'date range 2005-03-05 to 2010-01-23, from the dates printed beside each link',
        ],
    ],
    'iraq' => [
        'body' => "A topic dossier rather than an authored series: Signal reporting and wire "
            . "pickups on the Abu Ghraib prison abuse scandal and its Santa Clarita Valley "
            . "connections, gathered under the heading \"Abu Ghraib Prison Abuse Scandal Hits the "
            . "Santa Clarita Valley\". Sixty-four pieces are indexed, dated between 2004 and "
            . "November 2005, each captioned with where it first appeared. No author is named on "
            . "the index.",
        'from' => [
            'index page /scvhistory/signal/iraq/index.htm, printed heading "Abu Ghraib Prison Abuse '
                . 'Scandal Hits the Santa Clarita Valley" and the captions "Published in The Signal"',
            'sitemap crawl: 64 article pages; 47 carry a datestamp, running 2004-05-11 to 2005-11-13',
        ],
    ],
    'otn-rioux' => [
        'body' => "\"Richard 'Doc' Rioux At Large\", the column of Richard H. Rioux, carried on "
            . "the Old Town Newhall site. Forty-three pieces sit in the tree, the earliest dated "
            . "May 1992 and the latest February 1997, on downtown Newhall, its revitalisation and "
            . "the life of the town. The tree also holds a biographical sketch of Rioux.",
        'from' => [
            'index page /oldtownnewhall/rioux/index.htm, printed title "Richard \'Doc\' Rioux At Large"',
            'sitemap crawl: 43 article pages, including "Richard \'Doc\' Rioux · Biographical Sketch"',
            'date range 1992-05-10 to 1997-02-16, from the article filenames',
        ],
    ],
    'otn-whyte' => [
        'body' => "\"Black 'N' Whyte\", the column of Tim Whyte, carried on the Old Town Newhall "
            . "site through 1997. Thirty-seven pieces sit in the tree, dated between February and "
            . "November of that year, written as a local columnist rather than as a historian: "
            . "traffic court, the water district, a Star Wars re-release, the death of a friend.",
        'from' => [
            'index page /oldtownnewhall/whyte/, printed title "Black \'N\' Whyte"',
            'sitemap crawl: 37 article pages, date range 1997-02-23 to 1997-11-16 from the filenames',
        ],
    ],
    'otn-patti' => [
        'body' => "\"Open Book\", Patti Rasmussen on Santa Clarita Valley school issues, carried "
            . "on the Old Town Newhall site and headed there \"'Open Book' - Santa Clarita Valley "
            . "School Issues with Patti Rasmussen\". Twenty-six pieces sit in the tree, dated "
            . "between May and November 1997, on school boards, graduation, adult education, PTA "
            . "and the theatre programmes of the valley's high schools.",
        'from' => [
            'index page /oldtownnewhall/patti/, printed title "\'Open Book\' - Santa Clarita Valley '
                . 'School Issues with Patti Rasmussen"',
            'sitemap crawl: 26 article pages, date range 1997-05-24 to 1997-11-15 from the filenames',
        ],
    ],
    'otn-pauline' => [
        'body' => "The column of Pauline Harte, carried on the Old Town Newhall site through 1997 "
            . "and headed there simply \"Pauline Harte\". Thirty-eight pieces sit in the tree, "
            . "dated between March and November of that year, on the town and its people: "
            . "homelessness through the winter, the trades, the street.",
        'from' => [
            'index page /oldtownnewhall/pauline/index.htm, printed title "Pauline Harte"',
            'sitemap crawl: 38 article pages, date range 1997-03-04 to 1997-11-18 from the filenames',
        ],
    ],
    'otn-gazette' => [
        'body' => "The Old Town Newhall Gazette, a multi-author periodical issued in two-month "
            . "numbers and carried on the Old Town Newhall site. Thirty-five issues sit in the "
            . "tree, from November-December 2005 through 2008.",
        'from' => [
            'sitemap crawl: 35 article pages titled "Old Town Newhall Gazette, <month>-<month> <year>"',
            'the years named in those titles run 2005 to 2008; the earliest is November-December 2005',
        ],
    ],
    'newsmaker' => [
        'body' => "The Signal's \"Newsmaker of the Week\" interview. The archive holds almost "
            . "none of it: the legacy tree at /scvhistory/signal/newsmaker/ redirects off the site "
            . "to scvtv.com, and two pages remain, an interview with Harry Carey Jr. and Cappy "
            . "Carey from November 2005 and a piece on Ray Bradbury. Other Newsmaker interviews "
            . "survive scattered through the archive rather than gathered here.",
        'from' => [
            'the tree and its index both return a 301 redirect to https://scvtv.com/category/news/nm/',
            'sitemap crawl: 2 article pages, "Newsmaker of the Week: Harry Carey Jr. & Cappy Carey" '
                . 'and "Ray Bradbury on Mars"',
            'a Newsmaker interview also sits inside the Abu Ghraib tree as sg111305-nm.htm, which is '
                . 'why the series is described as scattered',
        ],
    ],
];

$elements = Craft::$app->getElements();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$filled = 0; $blocked = 0;

foreach ($FILL as $slug => $spec) {
    $e = \craft\elements\Entry::find()->section('collections')->slug($slug)->status(null)->one();
    if (!$e) { echo $slug . ': no collection record' . PHP_EOL; continue; }

    $current = trim((string)$e->body);
    echo PHP_EOL . ($e->title ?: '(untitled) ' . $e->slug) . '  (#' . $e->id . ')' . PHP_EOL;

    if (!preg_match($STUB, $current)) {
        echo '  BLOCKED: the body is not the placeholder any more, so it is left alone.' . PHP_EOL;
        echo '  it reads: "' . mb_substr($current, 0, 88) . '…"' . PHP_EOL;
        $blocked++;
        continue;
    }

    echo '  was:  "' . mb_substr($current, 0, 88) . '"' . PHP_EOL;
    echo '  now:  ' . mb_substr($spec['body'], 0, 100) . '…' . PHP_EOL;
    echo '  ' . strlen($spec['body']) . ' characters, drawn from:' . PHP_EOL;
    foreach ($spec['from'] as $f) { echo '      ' . $f . PHP_EOL; }
    $filled++;

    if ($APPLY) {
        $e->setFieldValue('body', $spec['body']);
        echo '  ' . ($elements->saveElement($e) ? 'saved' : 'SAVE FAILED: ' . json_encode($e->getErrors())) . PHP_EOL;
    }
}

$remaining = [];
foreach (\craft\elements\Entry::find()->section('collections')->status(null)->orderBy('slug asc')->limit(null)->all() as $e) {
    if (isset($FILL[$e->slug])) { continue; }
    if (preg_match($STUB, trim((string)$e->body))) { $remaining[] = ($e->title ?: $e->slug); }
}

echo PHP_EOL . str_repeat('=', 78) . PHP_EOL;
echo 'bodies that would be filled: ' . $filled . PHP_EOL;
echo 'blocked because somebody had written one: ' . $blocked . PHP_EOL;
if ($remaining) {
    echo 'still placeholders, not covered here: ' . implode(', ', $remaining) . PHP_EOL;
}
if (!$APPLY) { echo PHP_EOL . 'nothing written.' . PHP_EOL; }
