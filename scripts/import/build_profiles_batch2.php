/**
 * The rest of the first profile batch (Nathan, 3 October 2026: "Then the batch
 * after that: the ten you proposed as batch 1 of the profile work, minus
 * whoever the council pages cover"): Reynolds, Rioux, Harte, Whyte and
 * Rasmussen. Heidt, Darcy, Boyer, Klajic and Pederson were done with the
 * council pages (build_council_profiles_batch1.php).
 *
 * Everything cited is already in the archive; no new research.
 *
 *   Reynolds #281 (1937-1996): written from Leon Worden's Preface to the
 *     Reynolds history (#817), which the withheld WordPress body paraphrased.
 *     As for Pico (build_pico_profile.php), the WordPress body is replaced (it
 *     stays in inventory/wp_content.json and in the entry's revisions), the
 *     unsourced author bio is replaced with a sourced one, and the day of his
 *     death, which only the WordPress text gives, is marked uncited and said so
 *     in a note; the profile gives the year, from the Preface.
 *   Rioux #2585 (1943-1997): the biographical sketch from his own book (#12854),
 *     Buck McKeon's tribute in the House (#12850), Leon Worden's columns.
 *   Harte #2594, Whyte #2588, Rasmussen #2591: living. Their public life in the
 *     archive is their 1997 columns; the profiles say what they wrote, when and
 *     where, and what a second writer said of them where one did. Nothing from
 *     inside the columns about their families. Whyte has no second source: the
 *     profile says his columns are its source.
 *
 * Counts and dates of columns are computed from the byline records (articles,
 * not the collection) and the parts of the history from its live parts (Bowers
 * Cave, #2177, is disabled until consultation); the
 * script refuses if they differ from the text.
 *
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/build_profiles_batch2.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$root = \Craft::getAlias('@root'); $elements = Craft::$app->getElements();
$ws = fn($s) => trim(preg_replace('~\s+~u', ' ', str_replace(["\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}"], ["'", "'", '"', '"', ' '], html_entity_decode(strip_tags((string)$s), ENT_QUOTES))));
$fn = fn(array $notes): array => array_map(fn($i, $n) => ['number' => (string)($i + 1), 'note' => $n, 'source' => 'editorial-2026'], array_keys($notes), $notes);
$P = [];

$P[281] = ['title' => 'Jerry Reynolds', 'replaceBody' => 'Born Gerald G. Reynolds in Torrance, California, on July 16, 1937',
    'fields' => ['birthEvidence' => 'retrospective', 'deathEvidence' => 'uncited'],
    'authorBio' => 'Jerry Reynolds (1937-1996), the Santa Clarita Valley\'s second town historian, the first curator of the Santa Clarita Valley Historical Society and the author of "Santa Clarita: Valley of the Golden Dream" (1992).',
    'editorNote' => ['heading' => 'His date of death', 'position' => 'bottom', 'note' => 'The day of his death, February 26, 1996, came with the WordPress import and has no source in the archive yet. Leon Worden\'s preface gives the year.'],
    'must' => [
        817 => ['Jerry Reynolds (1937–1996), founding curator of the SCV Historical Society and author of Santa Clarita: Valley of the Golden Dream (1992)', 'It is the most complete Santa Clarita Valley history book yet written', 'published in 1992 by the Santa Clarita Valley Chamber of Commerce', 'Other works to Reynolds\' credit include A Heritage to Keep (1976); Pico Canyon Chronicles (1985)', 'Jerry Reynolds wrote for The Signal, the Santa Clarita Valley\'s daily newspaper, off and on for two decades, producing a complete series of historical "columns" in the mid-1970s and again in the mid-1980s', 'Born Gerald G. Reynolds in Torrance, California, on July 16, 1937, "Jerry" studied art history at Long Beach State College and worked as a private investigator until landing his dream job as a tour guide at William Randolph Hearst\'s castle at San Simeon', 'first as director of the Castaic Lake Visitors Center and later of the Vista del Lago Visitors Center at Lake Pyramid', 'Reynolds came to Newhall in 1971 with his wife, Myrna, and their three sons', 'promptly filled the void left by the aging A.B. Perkins, the valley\'s first "town historian."', 'By the time of Perkins\' death in 1977, Reynolds had already assumed the mantle, collecting historical photographs, committing old-timers\' oral histories to paper, helping organize the Santa Clarita Valley Historical Society, and serving as its first (and until his death its only) museum curator', 'The stories in this volume formed the backbone of Reynolds\' original Santa Clarita: Valley of the Golden Dream', 'some of the material presented here was not included in the original book, and although sections have been updated and revised, great care has been taken to preserve Reynolds\' authentic style'],
    ],
    'count' => [871, 'partOfCollection', 79],
    'body' => [
        'Jerry Reynolds was the Santa Clarita Valley\'s second town historian, after A.B. Perkins, and the author of the fullest history of the valley yet written. He helped organize the Santa Clarita Valley Historical Society and was its first museum curator, and until his death in 1996 its only one.[1]',
        'He was born Gerald G. Reynolds in Torrance, California, on July 16, 1937. He studied art history at Long Beach State College, worked as a private investigator, and then as a tour guide at Hearst Castle at San Simeon. He made his career with the California Department of Water Resources, as director of the Castaic Lake Visitors Center and then of the Vista del Lago Visitors Center at Pyramid Lake.[1]',
        'He came to Newhall in 1971 with his wife, Myrna, and their three sons, and took up the work of the aging Perkins, collecting historical photographs and writing down the recollections of the valley\'s old-timers. By the time Perkins died, in 1977, he had taken his place.[1]',
        'He wrote for The Signal off and on for two decades, with complete series of historical columns in the mid-1970s and the mid-1980s. Those columns were the backbone of "Santa Clarita: Valley of the Golden Dream," published by the Santa Clarita Valley Chamber of Commerce in 1992. His other books include "A Heritage to Keep" (1976) and "Pico Canyon Chronicles" (1985).[1] The archive holds his history in 79 parts, as revised for its digital edition, which added material the 1992 book did not include.[1][2]',
    ],
    'notes' => ['Leon Worden, "Preface," History of the Santa Clarita Valley (1998), in this archive (article #817).', 'The collection "History of the Santa Clarita Valley" in this archive (collection #871), with its 79 parts.'],
];

$P[2585] = ['title' => 'Richard Rioux',
    'fields' => ['fullName' => 'Richard Rioux', 'birthDate' => 'November 26, 1943', 'birthDateEdtf' => '1943-11-26', 'birthEvidence' => 'retrospective', 'birthplace' => 'Fall River, Massachusetts',
        'deathDate' => 'April 28, 1997', 'deathDateEdtf' => '1997-04-28', 'deathEvidence' => 'contemporary', 'occupation' => 'Rehabilitation center director; columnist; photographer'],
    'aliases' => 'Doc Rioux; Richard H. Rioux',
    'must' => [
        12854 => ['November 26, 1943 - April 28, 1997', 'BA in History, California State University, Northridge (1968)', 'MA in History, California State University, Northridge (1971)', 'Ph.D. in History, University of Southern California (1978)', 'Executive Director, Antelope Valley Rehabilitation Centers', 'Founder and President, Stevenson Ranch Town Council', 'First President, Stevenson Ranch Master Homeowners Association', 'Founder, Old Town Newhall, USA', 'Ran 26 Marathons', 'Climbed Mt. Whitney 7 times', 'Newspaper Columnist, The Signal', 'Born in Fall River, Massachusetts in 1943, Richard Rioux moved with his father and mother to Southern California in 1958', 'moved to the Santa Clarita Valley ten years later', 'Richard taught secondary school from 1966 to 1971', 'While working as an administrator in the Los Angeles County Health Department, Richard introduced a pioneering basic education program for people recovering from substance abuse', 'chairman of the steering committee for Old Town Newhall, USA, and a Sunday columnist for The Signal newspaper', 'From "Images: Sunrises, Sunsets and In Between," 1996'],
        12850 => ['passed away April 28, 1997 in Santa Clarita, California', 'Executive Director of the Los Angeles County Antelope Valley Rehabilitation Centers at Acton and Warm Springs, where he worked hands-on with more than 22,000 residents', 'he pioneered the development of an innovative literacy training program', 'Founder and first President of his beloved Stevenson Ranch Town Council', 'having run 26 marathons and climbed Mt. Whitney seven times', 'A Fullbright Scholar', 'HOWARD P. "BUCK" McKEON Member of Congress'],
        12414 => ['Rioux, who died April 28, was executive director of the Antelope Valley Rehabilitation Centers', 'Twenty-two thousand patients came through Acton and Warm Springs during Rioux\'s tenure', 'The rate is closer to 70 percent for those who participate in an on-site literacy program, which Rioux pioneered'],
        12270 => ['My fellow Signal columnist Richard Rioux invited me to spend the afternoon Friday at the alcohol and drug rehab center he runs in Acton', 'one of just two that the county directly operates -- the other being at Warm Springs, which Rioux also runs'],
        12589 => ['The late Richard Rioux came up with the name "Old Town Newhall"'],
        12472 => ['"Doc Rioux" to Signal readers -- has a new book out', '"Images: Sunrises, Sunsets and In Between" is a fabulous coffee table book celebrating the natural beauty of the Santa Clarita Valley'],
    ],
    'bylines' => [32, '1992-05-10', '1997-02-16'],
    'body' => [
        'Richard Rioux, "Doc" to readers of The Signal, directed Los Angeles County\'s alcohol and drug rehabilitation centers at Acton and Warm Springs, founded the Stevenson Ranch Town Council, gave Old Town Newhall its name, and wrote a column for The Signal until his death in 1997.[1][2][3][5]',
        'Born in Fall River, Massachusetts, in 1943, he came to Southern California with his parents in 1958. He took a B.A. in history at California State University, Northridge, in 1968 and an M.A. there in 1971, taught secondary school from 1966 to 1971, and earned a Ph.D. in history at the University of Southern California in 1978; Congressman Buck McKeon called him a Fulbright Scholar. He moved to the Santa Clarita Valley in 1976.[1][2]',
        'As an administrator in the county Health Department he introduced a basic education program for people recovering from substance abuse. As executive director of the Antelope Valley Rehabilitation Centers, the two treatment programs the county ran itself, at Acton and Warm Springs, he oversaw some 22,000 patients, and those who took part in his literacy program finished treatment at a markedly higher rate than the rest.[1][2][3][4]',
        'He was founder and first president of the Stevenson Ranch Town Council and first president of the Stevenson Ranch Master Homeowners Association, and chaired the steering committee of Old Town Newhall, USA, the effort to revive downtown Newhall. The name "Old Town Newhall" was his.[1][2][5]',
        'His column, "Richard \'Doc\' Rioux At Large," ran in The Signal on Sundays; the archive holds 32 of his pieces, from May 1992 to February 1997.[1][7] A photographer, he published "Images: Sunrises, Sunsets and In Between," a book of his photographs of the valley, in 1996.[1][6] He ran 26 marathons and climbed Mount Whitney seven times.[1][2]',
        'He died in Santa Clarita on April 28, 1997.[2][3]',
    ],
    'notes' => ['The biographical sketch of Richard Rioux, from his book "Images: Sunrises, Sunsets and In Between" (1996), in this archive (article #12854).', 'Howard P. "Buck" McKeon, Member of Congress, tribute to Dr. Richard Rioux in the House of Representatives, May 6, 1997, in this archive (article #12850).', 'Leon Worden, "Taking recovery to Al-Impian heights," June 4, 1997, in this archive (article #12414).', 'Leon Worden, "A day in the life at Acton Rehab," November 27, 1996, in this archive (article #12270).', '"Editorial: A Long And Rutted Road," Old Town Newhall Gazette, February-March 2008, in this archive (article #12589).', 'Leon Worden, "King Richard of Stevenson, etc.," December 18, 1996, in this archive (article #12472).', 'Archive records: his columns under his byline, May 10, 1992, to February 16, 1997, in the collection "Richard \'Doc\' Rioux At Large" (collection #683).'],
];

$P[2594] = ['title' => 'Pauline Harte',
    'must' => [12781 => ['This is my first "official" column', 'I received a phone call from The Signal asking if I would like to write a weekly column'], 12422 => ['Signal columnist Pauline Harte'], 681 => ['Thirty-eight pieces sit in the tree, dated between March and November of that year, on the town and its people: homelessness through the winter, the trades, the street']],
    'bylines' => [38, '1997-03-04', '1997-11-18'],
    'body' => [
        'Pauline Harte wrote a column for The Signal in 1997. The first appeared on March 4 of that year, after, she wrote, the paper had telephoned to ask whether she would write a weekly column; in April Leon Worden called her a Signal columnist.[1][2]',
        'The archive holds 38 of her pieces, from March to November 1997, as they were carried on the Old Town Newhall site under her name: on the town and its people, the trades, homelessness through the winter, and the street.[1][3]',
    ],
    'notes' => ['Archive records: her columns under her byline, March 4 to November 18, 1997, the first of them "Help! Columnist under attack by computers!" (article #12781).', 'Leon Worden, "Olde Towne Days are here again," April 23, 1997, in this archive (article #12422).', 'The collection "Pauline Harte" in this archive (collection #681).'],
];

$P[2588] = ['title' => 'Tim Whyte',
    'must' => [12935 => ['At the time of that court battle, I was something of a rookie city editor, filling in as editorial writer while the boss was on vacation'], 12925 => ['Will Fleet (our general manager), John Boston (our columnist) and I'], 685 => ['written as a local columnist rather than as a historian: traffic court, the water district, a Star Wars re-release, the death of a friend']],
    'bylines' => [37, '1997-02-23', '1997-11-16'],
    'body' => [
        'Tim Whyte wrote the column "Black \'N\' Whyte" for The Signal in 1997. The archive holds 37 of his pieces, from February to November of that year, as they were carried on the Old Town Newhall site.[1][2]',
        'He wrote from inside the paper. His columns speak of The Signal\'s general manager and columnists as colleagues, and recall an earlier time when he was "something of a rookie city editor."[1] They range over local life: traffic court, the water district, a Star Wars re-release, the death of a friend.[2] No other source in the archive describes him, so these columns are this record\'s only source.',
    ],
    'notes' => ['Archive records: his columns under his byline, February 23 to November 16, 1997, among them those of May 25 (article #12935) and October 12, 1997 (article #12925).', 'The collection "Black \'N\' Whyte" in this archive (collection #685).'],
];

$P[2591] = ['title' => 'Patti Rasmussen',
    'must' => [12520 => ['the Theatre Arts for Children Foundation, headed by fellow Signal columnist Patti Rasmussen, to restore the forsaken Newhall School Auditorium'], 679 => ['Patti Rasmussen on Santa Clarita Valley school issues', 'on school boards, graduation, adult education, PTA and the theatre programmes of the valley\'s high schools']],
    'bylines' => [24, '1997-05-24', '1997-11-08'],
    'body' => [
        'Patti Rasmussen wrote "Open Book," a column on Santa Clarita Valley school issues. The archive holds 24 of her pieces, from May to November 1997, as they were carried on the Old Town Newhall site: on school boards, graduation, adult education, the PTA and the theatre programs of the valley\'s high schools.[1][2]',
        'In May 1996 Leon Worden called her a fellow Signal columnist and the head of the Theatre Arts for Children Foundation, which was working to restore the Newhall School Auditorium.[3]',
    ],
    'notes' => ['Archive records: her columns under her byline, May 24 to November 8, 1997.', 'The collection "Open Book" in this archive (collection #679).', 'Leon Worden, "Creating a Theater District in Old Town Newhall," May 15, 1996, in this archive (article #12520).'],
];

/* ================================================================ checks */
$bad = []; $plan = [];
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    if (!$p || $p->title !== $c['title']) { $bad[] = "#$id is not {$c['title']}"; continue; }
    foreach ($c['must'] as $src => $phrases) { $e = Entry::find()->id($src)->status(null)->one(); $t = $e ? $ws($e->body) : ''; foreach ($phrases as $ph) { if (!str_contains($t, $ws($ph))) { $bad[] = "{$c['title']}: #$src does not read \"" . mb_substr($ph, 0, 60) . '"'; } } }
    if (isset($c['bylines'])) {
        [$want, $first, $last] = $c['bylines'];
        $arts = Entry::find()->section('articles')->status(null)->relatedTo(['targetElement' => $id, 'field' => 'writtenBy'])->all();
        $d = array_filter(array_map(fn($a) => (string)$a->originalPublishDateEdtf, $arts)); sort($d);
        if (count($arts) !== $want || reset($d) !== $first || end($d) !== $last) { $bad[] = "{$c['title']}: bylines are " . count($arts) . ' from ' . reset($d) . ' to ' . end($d); }
    }
    if (isset($c['count'])) { [$cid, $field, $want] = $c['count']; $n = Entry::find()->section('articles')->relatedTo(['targetElement' => $cid, 'field' => $field])->count(); if ((int)$n !== $want) { $bad[] = "{$c['title']}: #$cid holds $n parts, not $want"; } }
    $body = implode("\n\n", $c['body']);
    $cur = trim((string)$p->body); $done = $cur === trim($body);
    if ($cur !== '' && !$done && !(isset($c['replaceBody']) && str_starts_with($ws($cur), $c['replaceBody']))) { $bad[] = "{$c['title']}: has a body that is not the one this replaces"; }
    foreach ($c['fields'] ?? [] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have !== '' && $have !== $v && $k !== 'occupation') { $bad[] = "{$c['title']}: $k is \"$have\", not \"$v\""; } }
    preg_match_all('~\[(\d+)\]~', $body, $m); $used = array_unique(array_map('intval', $m[1]));
    if (count($used) !== count($c['notes']) || max($used) > count($c['notes'])) { $bad[] = "{$c['title']}: notes used " . json_encode(array_values($used)) . ' of ' . count($c['notes']); }
    if (preg_match('~\x{2014}~u', $body . implode('', $c['notes']) . ($c['authorBio'] ?? '') . json_encode($c['editorNote'] ?? ''))) { $bad[] = "{$c['title']}: an em dash"; }
    $plan[$id] = ['body' => $body, 'done' => $done];
    echo str_pad($c['title'], 16) . 'body ' . ($done ? 'already written' : ($cur ? 'WordPress -> ' : 'empty -> ') . str_word_count($body) . ' words, ' . count($c['notes']) . ' notes') . PHP_EOL;
}
echo 'REFUSED: ' . ($bad ? PHP_EOL . '  ' . implode(PHP_EOL . '  ', $bad) : 'none') . PHP_EOL;
if (!empty($SHOW)) { foreach ($P as $c) { echo PHP_EOL . "## {$c['title']}" . PHP_EOL; foreach ($c['body'] as $l) { echo "  $l" . PHP_EOL; } } }
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING: resolve the refusals first' . PHP_EOL; return; }

/* ================================================================ writes */
$short = []; $n = 0;
foreach ($P as $id => $c) {
    $p = Entry::find()->id($id)->status(null)->one();
    $h = array_map(fn($f) => $f->handle, $p->getFieldLayout()->getCustomFields());
    $vals = [];
    foreach ($c['fields'] ?? [] as $k => $v) { $have = trim((string)($p->getFieldValue($k)->value ?? $p->getFieldValue($k))); if ($have === '' || ($k === 'occupation' && $have !== $v)) { $vals[$k] = $v; } }
    if (!empty($c['aliases']) && trim((string)$p->personAliases) === '') { $vals['personAliases'] = $c['aliases']; }
    if (!$plan[$id]['done']) {
        $vals += ['body' => $plan[$id]['body'], 'footnotes' => $fn($c['notes']), 'bodyAuthorship' => 'editorial-2026', 'recordProvenance' => trim((string)$p->recordProvenance . '; build_profiles_batch2.php, 3 Oct 2026: ' . (isset($c['replaceBody']) ? 'sourced profile replacing the WordPress body' : 'profile from archive sources'), '; ')];
        if (mb_strlen($vals['recordProvenance']) > 255) { unset($vals['recordProvenance']); } /* the field holds 255; APPLIED.log records the run */
        if (!empty($c['authorBio'])) { $vals['authorBio'] = $c['authorBio']; }
        if (!empty($c['editorNote'])) {
            $rows = array_values(array_map(fn($r) => ['heading' => (string)($r['heading'] ?? ''), 'position' => (string)($r['position'] ?? 'bottom'), 'note' => (string)($r['note'] ?? '')], array_filter($p->editorNotes ?? [], fn($r) => is_array($r) && trim((string)($r['note'] ?? '')) !== '')));
            if (!in_array($c['editorNote']['note'], array_column($rows, 'note'), true)) { $rows[] = $c['editorNote']; $vals['editorNotes'] = $rows; }
        }
    }
    $vals = array_intersect_key($vals, array_flip($h));
    if ($vals) { $p->setFieldValues($vals); if (!$elements->saveElement($p)) { $short[] = "{$c['title']} (" . json_encode($p->getFirstErrors()) . ')'; continue; } }
    $r = Entry::find()->id($id)->status(null)->one();
    $ok = trim((string)$r->body) === trim($plan[$id]['body']) && ($r->bodyAuthorship->value ?? '') === 'editorial-2026';
    $ok ? $n++ : $short[] = $c['title'] . ' (read back ' . str_word_count(strip_tags((string)$r->body)) . ' words, ' . ($r->bodyAuthorship->value ?? '') . ', set ' . implode(',', array_keys($vals)) . ')';
    echo ($ok ? 'OK    ' : 'SHORT ') . $c['title'] . ' ' . $r->url . PHP_EOL;
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : "OK: $n records") . PHP_EOL;
$applyLog = require $root . '/scripts/import/_apply_log.php';
$applyLog('build_profiles_batch2.php', $n, $short ? 'SHORT' : 'verified', 'profiles from archive sources: Reynolds (replacing the WordPress body), Rioux, Harte, Whyte, Rasmussen');
if ($short) { throw new \RuntimeException('build_profiles_batch2: ' . implode(', ', $short)); }
