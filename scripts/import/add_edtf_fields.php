/**
 * Adds an EDTF field beside every printed date, and derives it where the
 * printed form parses cleanly.
 *
 * THE TWO FIELDS
 *
 * "About 1887" is what the source says, and it is the truth about the source.
 * "1887~" is what a machine can sort, filter and compare. Keeping only the
 * first makes the archive unsortable. Keeping only the second quietly asserts a
 * precision the source never had, which for an archive is the worse error: a
 * reader would have no way to know that "1887" was a guess somebody made in
 * 1974.
 *
 * So both, and the printed one is authoritative. Where the printed form does
 * not parse, the EDTF field stays EMPTY. It is never guessed, because an empty
 * field is a date nobody could pin down, which is itself a fact worth keeping.
 *
 * WHAT PARSES
 *
 *   1887                     -> 1887
 *   March 1887               -> 1887-03
 *   12 March 1884            -> 1884-03-12
 *   March 12, 1884           -> 1884-03-12
 *   about 1887 / c. 1887     -> 1887~        (approximate)
 *   1880s                    -> 188X         (decade)
 *   1880-1889 / 1880 to 1889 -> 1880/1889    (interval)
 *   spring 1912              -> 1912-21      (season, EDTF level 2)
 *   before 1900              -> ../1900      (open start)
 *   after 1900               -> 1900/..      (open end)
 *   n.d. / undated / unknown -> (empty, deliberately)
 *
 * Anything else is left empty and counted, so the report says how much of the
 * archive's dating a machine can and cannot read.
 *
 * Idempotent. Dry run by default.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_edtf_fields.php'))"
 */

$APPLY = false;

/* entryType => [printed field => the EDTF field beside it] */
$PAIRS = [
    'article'      => ['originalPublishDate' => 'originalPublishDateEdtf'],
    'event'        => ['eventDate' => 'eventDateEdtf'],
    'organization' => ['dateFounded' => 'dateFoundedEdtf'],
    'person'       => ['birthDate' => 'birthDateEdtf', 'deathDate' => 'deathDateEdtf'],
    'photograph'   => ['photoDate' => 'photoDateEdtf'],
    'place'        => ['dateEstablished' => 'dateEstablishedEdtf'],
    'warMemorial'  => ['deathDate' => 'deathDateEdtf'],
];

$MONTHS = ['january'=>1,'february'=>2,'march'=>3,'april'=>4,'may'=>5,'june'=>6,'july'=>7,
           'august'=>8,'september'=>9,'october'=>10,'november'=>11,'december'=>12,
           'jan'=>1,'feb'=>2,'mar'=>3,'apr'=>4,'jun'=>6,'jul'=>7,'aug'=>8,'sep'=>9,'sept'=>9,
           'oct'=>10,'nov'=>11,'dec'=>12];
$SEASONS = ['spring'=>21,'summer'=>22,'autumn'=>23,'fall'=>23,'winter'=>24];

/* Returns [edtf, why] or [null, why-not]. Never guesses. */
$toEdtf = function (string $raw) use ($MONTHS, $SEASONS): array {
    $s = trim(mb_strtolower($raw));
    if ($s === '') { return [null, 'empty']; }
    if (preg_match('~^(n\.?d\.?|undated|unknown|not dated|\?)$~', $s)) { return [null, 'the source says it does not know']; }

    /* Tidy first, and only in ways that change nothing about what the source
       says. The first run left 968 dates unparsed and nearly all of them were
       "Saturday, August 9, 2008" or "July 19, 2006," or "Conducted November 27,
       2006": a weekday, a stray comma, a verb. Those are full dates with a word
       in front, not dates nobody could pin down, and reporting them as
       unparseable would have understated what the archive knows by a factor of
       three. A weekday adds nothing EDTF can carry and is dropped; the printed
       field keeps it, as it keeps everything. */
    /* A bullet or a pipe starts the bibliographic tail of a periodical line:
       "November-December 2005 • Year 11, Number 6". The date is everything
       before it. */
    $s = preg_split('~\s*[•|]\s*~u', $s)[0] ?? $s;
    $s = trim($s, " ()[]\t\n\r");
    $s = preg_replace('~\b(monday|tuesday|wednesday|thursday|friday|saturday|sunday|mon|tue|tues|wed|thu|thur|thurs|fri|sat|sun)\b\.?,?\s*~', '', $s);
    $s = preg_replace('~^(conducted|published|printed|dated|taken|recorded|written|issued|filed|photographed|as of|on)\s+~', '', $s);
    /* "Feb. 24, 2004": the abbreviation's full stop is not punctuation at the
       end of the string, so the general trim never reached it. */
    $s = preg_replace('~\b(jan|feb|mar|apr|jun|jul|aug|sep|sept|oct|nov|dec)\.~', '$1', $s);
    $s = trim($s, " \t\n\r\0\x0B.,;:");
    $s = trim(preg_replace('~\s+~', ' ', $s));

    $approx = (bool)preg_match('~\b(about|abt|circa|c\.|ca\.|approximately|around)\b~', $s);
    $before = (bool)preg_match('~\b(before|prior to|by)\b~', $s);
    $after  = (bool)preg_match('~\b(after|since|from)\b~', $s);
    $s = preg_replace('~\b(about|abt|circa|c\.|ca\.|approximately|around|before|prior to|after|since)\b~', ' ', $s);
    $s = trim(preg_replace('~\s+~', ' ', $s));

    $suffix = $approx ? '~' : '';

    /* interval: years, months of one year, or two full dates. A lifespan
       "November 26, 1943 - April 28, 1995" is an interval and EDTF says so
       exactly; leaving it empty would drop the best-dated records in the war
       memorial section. */
    if (preg_match('~^(\d{4})\s*(?:-|–|—|to|until)\s*(\d{4})$~', $s, $m)) {
        return [$m[1] . '/' . $m[2], 'a range of years'];
    }
    if (preg_match('~^([a-z]+)\s*(?:-|–|—|to)\s*([a-z]+)\s+(\d{4})$~', $s, $m)
        && isset($MONTHS[$m[1]]) && isset($MONTHS[$m[2]])) {
        return [sprintf('%04d-%02d/%04d-%02d', $m[3], $MONTHS[$m[1]], $m[3], $MONTHS[$m[2]]), 'a range of months'];
    }
    if (preg_match('~^([a-z]+)\s+(\d{1,2}),?\s+(\d{4})\s*(?:-|–|—|to)\s*([a-z]+)\s+(\d{1,2}),?\s+(\d{4})$~', $s, $m)
        && isset($MONTHS[$m[1]]) && isset($MONTHS[$m[4]])) {
        return [sprintf('%04d-%02d-%02d/%04d-%02d-%02d', $m[3], $MONTHS[$m[1]], $m[2], $m[6], $MONTHS[$m[4]], $m[5]),
                'a span of full dates'];
    }
    /* decade */
    if (preg_match('~^(\d{3})0s$~', $s, $m)) { return [$m[1] . 'X', 'a decade']; }
    /* season year */
    foreach ($SEASONS as $word => $code) {
        if (preg_match('~^' . $word . '\s+(?:of\s+)?(\d{4})$~', $s, $m)) {
            return [$m[1] . '-' . $code . $suffix, 'a season'];
        }
    }
    /* day month year, either order */
    if (preg_match('~^(\d{1,2})\s+([a-z]+),?\s+(\d{4})$~', $s, $m) && isset($MONTHS[$m[2]])) {
        return [sprintf('%04d-%02d-%02d', $m[3], $MONTHS[$m[2]], $m[1]) . $suffix, 'a full date'];
    }
    if (preg_match('~^([a-z]+)\s+(\d{1,2}),?\s+(\d{4})$~', $s, $m) && isset($MONTHS[$m[1]])) {
        return [sprintf('%04d-%02d-%02d', $m[3], $MONTHS[$m[1]], $m[2]) . $suffix, 'a full date'];
    }
    /* month year */
    if (preg_match('~^([a-z]+),?\s+(\d{4})$~', $s, $m) && isset($MONTHS[$m[1]])) {
        return [sprintf('%04d-%02d', $m[2], $MONTHS[$m[1]]) . $suffix, 'a month'];
    }
    /* iso */
    if (preg_match('~^(\d{4})-(\d{2})-(\d{2})$~', $s)) { return [$s . $suffix, 'already a date']; }
    /* bare year */
    if (preg_match('~^(\d{4})$~', $s, $m)) {
        if ($before) { return ['../' . $m[1], 'open start']; }
        if ($after)  { return [$m[1] . '/..', 'open end']; }
        return [$m[1] . $suffix, $approx ? 'an approximate year' : 'a year'];
    }
    return [null, 'does not parse'];
};

$fs  = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 78) . PHP_EOL;

$wanted = [];
foreach ($PAIRS as $t => $m) { foreach ($m as $from => $to) { $wanted[$to] = $from; } }

$createdSchema = false;
foreach ($wanted as $to => $from) {
    if ($fs->getFieldByHandle($to) !== null) { echo str_pad($to, 26) . 'exists already' . PHP_EOL; continue; }
    echo str_pad($to, 26) . 'would create, PlainText, beside ' . $from . PHP_EOL;
    if (!$APPLY) { continue; }
    $n = new \craft\fields\PlainText();
    $n->name = ucfirst(preg_replace('~(?<!^)[A-Z]~', ' $0', $to));
    $n->handle = $to;
    $n->instructions = 'The date in ' . $from . ', expressed in Extended Date/Time Format for '
        . 'sorting and comparison. Derived where the printed form parses; empty where it does '
        . 'not. The printed form is authoritative: this is a convenience, not a correction.';
    $n->multiline = false; $n->charLimit = 64; $n->searchable = true;
    if ($fs->saveField($n)) { $createdSchema = true; }
    else { echo '   FAILED: ' . implode('; ', $n->getFirstErrors()) . PHP_EOL; }
}

echo PHP_EOL;
foreach ($PAIRS as $typeHandle => $map) {
    $type = $svc->getEntryTypeByHandle($typeHandle);
    if (!$type) { echo str_pad($typeHandle, 18) . 'NOT FOUND' . PHP_EOL; continue; }
    $layout = $type->getFieldLayout();
    $present = [];
    foreach ($layout->getCustomFields() as $c) { $present[] = $c->handle; }
    $add = array_values(array_diff(array_values($map), $present));
    printf("%-18s would add %d: %s\n", $typeHandle, count($add), $add ? implode(', ', $add) : '-');

    if (!$APPLY || !$add) { continue; }
    $tabs = $layout->getTabs();
    $tabIdx = 0; $at = null;
    foreach ($tabs as $ti => $tab) {
        foreach ($tab->getElements() as $ei => $el) {
            if ($el instanceof \craft\fieldlayoutelements\CustomField
                && in_array($el->getField()->handle, array_keys($map), true)) { $tabIdx = $ti; $at = $ei + 1; }
        }
    }
    $els = $tabs[$tabIdx]->getElements();
    foreach ($add as $h) {
        $f = $fs->getFieldByHandle($h);
        if (!$f) { continue; }
        array_splice($els, $at ?? count($els), 0, [new \craft\fieldlayoutelements\CustomField($f)]);
        if ($at !== null) { $at++; }
    }
    $tabs[$tabIdx]->setElements($els);
    $layout->setTabs($tabs);
    $type->setFieldLayout($layout);
    if (!$svc->saveEntryType($type)) { echo '   FAILED: ' . implode('; ', $type->getFirstErrors()) . PHP_EOL; }
    else { $createdSchema = true; }
}

/* ------------------------------------------------------- what would parse */

echo PHP_EOL . 'DERIVING from what the records actually hold:' . PHP_EOL;
$SECTION_OF = ['article' => 'articles', 'event' => 'events', 'organization' => 'organizations',
               'person' => 'persons', 'photograph' => 'photographs', 'place' => 'places',
               'warMemorial' => 'warMemorials'];

$parsed = 0; $unparsed = 0; $blank = 0; $samples = []; $fails = [];
$plan = [];

foreach ($PAIRS as $typeHandle => $map) {
    $sec = $SECTION_OF[$typeHandle] ?? null;
    if (!$sec) { continue; }
    foreach (\craft\elements\Entry::find()->section($sec)->status(null)->limit(null)->all() as $e) {
        $h = [];
        foreach ($e->getFieldLayout()->getCustomFields() as $f) { $h[] = $f->handle; }
        foreach ($map as $from => $to) {
            if (!in_array($from, $h, true)) { continue; }
            $raw = trim((string)$e->getFieldValue($from));
            if ($raw === '') { $blank++; continue; }
            [$edtf, $why] = $toEdtf($raw);
            if ($edtf === null) { $unparsed++; if (count($fails) < 14) { $fails[$raw] = $why; } continue; }
            $parsed++;
            if (count($samples) < 12) { $samples[$raw] = $edtf . '   (' . $why . ')'; }
            if (in_array($to, $h, true)) {
                $cur = trim((string)$e->getFieldValue($to));
                if ($cur === '') { $plan[] = [$e, $to, $edtf]; }
            }
        }
    }
}

echo '  printed dates that parse:     ' . $parsed . PHP_EOL;
echo '  printed dates that do not:    ' . $unparsed . PHP_EOL;
echo '  records with no printed date: ' . $blank . PHP_EOL;
echo PHP_EOL . '  a sample of what parses:' . PHP_EOL;
foreach ($samples as $raw => $out) { printf("     %-34s -> %s\n", mb_substr($raw, 0, 33), $out); }
if ($fails) {
    echo PHP_EOL . '  a sample of what does not, left empty on purpose:' . PHP_EOL;
    foreach ($fails as $raw => $why) { printf("     %-34s    %s\n", mb_substr($raw, 0, 33), $why); }
}
echo PHP_EOL . '  would write: ' . count($plan) . ' EDTF values' . PHP_EOL;

if (!$APPLY) { echo PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }

$ok = 0;
foreach ($plan as [$e, $to, $edtf]) {
    $e->setFieldValue($to, $edtf);
    if (\Craft::$app->elements->saveElement($e)) { $ok++; }
}
$verified = 0;
foreach ($plan as [$e, $to, $edtf]) {
    $c = \craft\elements\Entry::find()->id($e->id)->status(null)->one();
    if ($c && trim((string)$c->getFieldValue($to)) === $edtf) { $verified++; }
}
echo PHP_EOL . 'wrote: ' . $ok . '  verified: ' . $verified . ' of ' . count($plan) . PHP_EOL;
if ($verified < count($plan)) { echo 'READ-BACK SHORT. Treat this run as failed.' . PHP_EOL; }
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_edtf_fields.php', $ok,
    ($verified < count($plan) ? 'FAILED: ' : '') . 'verified ' . $verified . ' of ' . count($plan),
    $createdSchema ? 'created the fields and derived' : 'derived only');
