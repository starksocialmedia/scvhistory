/**
 * elections, candidacies, sourceFaults: the three record types the election
 * history needs beyond officeHolding.
 *
 * WHY AN ELECTION IS A RECORD. officeHolding says Bob Kellar held a seat from
 * 2012. It cannot say what happened on 10 April 2012, because the event had
 * five candidates, two winners, 15,390 ballots and a growth-control measure on
 * the same paper, and only two of those facts belong to Kellar. The election is
 * the event; the holding is one consequence of it.
 *
 * WHY A CANDIDACY IS NOT A TERM. Eleven of the thirteen candidates in April
 * 2014 took no office, so officeHolding has nothing to hold them and they
 * vanish, which is the failure this section exists to prevent: an archive that
 * records only winners reports the result of every election as inevitable.
 * A candidacy is a person standing, whatever came of it.
 *
 * candidacyPerson is deliberately optional. Most losing candidates get no
 * person record: the rule is two triggers, they stood more than once, or they
 * already appear somewhere else in the archive. A single run with nothing
 * attached stays a candidacy record and a name on the election page, which is
 * the correct weight for it. The trigger that matters is the second: Michael
 * Cruz stood in 2006 and is a 2021 CVRA plaintiff, Lynne Plambeck stood in 2006
 * and is an SCV environmental figure, Maria Gutzeit stood in 2008 and later
 * served on a water board. A count threshold misses all three.
 *
 * NAMES AND VOTES AS PRINTED. nameAsPrinted and votesAsPrinted hold exactly
 * what the canvass says, including a spelling the archive believes is wrong.
 * votes holds the number we read. The two never overwrite each other, because
 * the canvass is the evidence and our reading is an editorial act.
 *
 * TURNOUT AND VBM ARE NOT STORED. Both are ratios of four numbers already on
 * the record, so storing them would create a fifth number that can disagree
 * with the other four. They are computed at render. The election page also has
 * to say what a November turnout figure means: ballotsCast counts everyone who
 * voted in the consolidated general election, not everyone who marked the
 * council race, so 2016's 77.1% measures the electorate the council race was
 * moved into, not interest in the council race.
 *
 * sourceFault records a transcription fault and never silently repairs one.
 * asPrinted is what the document says, reading is what we publish instead,
 * basis is why, decidedBy is who decided. A fault with no basis does not
 * render, the same rule the office-holding dates use.
 *
 * TWO FIELDS BEYOND WHAT WAS APPROVED, both on sourceFault, both easy to
 * strike: faultRecord, the record the fault is in, and faultField, the handle
 * of the field it is in. Without them a fault is a floating pair of strings
 * that nothing can find. Say so and they come out.
 *
 * Idempotent: every section, type and field is created only when missing, and a
 * second run writes nothing. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_elections_schema.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database and to project config' . PHP_EOL; }

/* The five evidence levels, the same vocabulary officeHolding uses, so a
   certified date and a certified result mean the same thing on both. */
$EVIDENCE = [
    'certified' => 'Certified: the body\'s own record',
    'contemporary' => 'Contemporary report',
    'retrospective' => 'Retrospective account',
    'roster' => 'Undated roster',
    'uncited' => 'Uncited',
];

$MEASURE_COLUMNS = [
    'col1' => ['heading' => 'Letter',    'handle' => 'letter',  'width' => '8%',  'type' => 'singleline'],
    'col2' => ['heading' => 'Subject',   'handle' => 'subject', 'width' => '',    'type' => 'singleline'],
    'col3' => ['heading' => 'Yes',       'handle' => 'yes',     'width' => '10%', 'type' => 'number'],
    'col4' => ['heading' => 'No',        'handle' => 'no',      'width' => '10%', 'type' => 'number'],
    'col5' => ['heading' => 'Carried',   'handle' => 'carried', 'width' => '8%',  'type' => 'lightswitch'],
];

/* handle => [kind, label, settings]. Relation sources name their section by
   handle; 'elections' is created by this same script, so the candidacy fields
   are built after the elections section exists. */
$SECTIONS = [
    'elections' => [
        'name' => 'Elections',
        'type' => 'Election',
        'typeName' => 'Election',
        'urls' => true,
        'uri' => 'elections/{slug}',
        'template' => 'elections/_entry',
        'titleFormat' => null, /* titled by hand: "April 10, 2012 general municipal" */
        'tab' => 'The election',
        'fields' => [
            'electionDate'      => ['plain', 'Election date, as printed', []],
            'electionDateEdtf'  => ['plain', 'Election date, EDTF', []],
            'electionKind'      => ['dropdown', 'Kind of election', ['options' => [
                'general' => 'General municipal', 'special' => 'Special municipal',
                'recall' => 'Recall', 'runoff' => 'Runoff']]],
            'consolidatedWith'  => ['plain', 'Consolidated with', []],
            'registeredVoters'  => ['number', 'Registered voters', []],
            'ballotsCast'       => ['number', 'Ballots cast', []],
            'votesByMail'       => ['number', 'Ballots cast by mail', []],
            'votesAtPrecinct'   => ['number', 'Ballots cast at precincts', []],
            'seatsUp'           => ['number', 'Seats up', []],
            'seatsUpEvidence'   => ['dropdown', 'Evidence for the seat count', ['options' => $EVIDENCE]],
            'sourceDocuments'   => ['entries', 'Source documents', ['sources' => 'documents']],
            'ballotMeasures'    => ['table', 'Ballot measures', ['columns' => $MEASURE_COLUMNS,
                'addRowLabel' => 'Add a measure',
                'instructions' => 'Measures on the same ballot. Carried is ticked only where the source says so; leave it clear rather than inferring it from the two vote columns, because a measure can need a supermajority.']],
        ],
    ],
    'candidacies' => [
        'name' => 'Candidacies',
        'type' => 'Candidacy',
        'typeName' => 'Candidacy',
        'urls' => false,
        'uri' => null,
        'template' => null,
        'titleFormat' => '{nameAsPrinted|default(\'Unnamed candidate\')}'
            . ' — {candidacyElection.one().title ?? \'unknown election\'}',
        'tab' => 'The candidacy',
        'fields' => [
            'candidacyElection' => ['entries', 'Election', ['sources' => 'elections', 'maxRelations' => 1, 'required' => true]],
            'candidacyPerson'   => ['entries', 'Person', ['sources' => 'persons', 'maxRelations' => 1]],
            'nameAsPrinted'     => ['plain', 'Name, as printed on the canvass', ['required' => true]],
            'votesAsPrinted'    => ['plain', 'Votes, as printed', []],
            'votes'             => ['number', 'Votes, as we read them', []],
            'outcome'           => ['dropdown', 'Outcome', ['options' => [
                'unknown' => 'Unknown', 'elected' => 'Elected', 'not-elected' => 'Not elected',
                'withdrew' => 'Withdrew', 'disqualified' => 'Disqualified'], 'default' => 'unknown']],
            'outcomeEvidence'   => ['dropdown', 'Evidence for the outcome', ['options' => $EVIDENCE]],
            'candidacyDistrict' => ['entries', 'District', ['sources' => 'places', 'maxRelations' => 1]],
        ],
    ],
    'sourceFaults' => [
        'name' => 'Source Faults',
        'type' => 'SourceFault',
        'typeName' => 'Source Fault',
        'urls' => false,
        'uri' => null,
        'template' => null,
        'titleFormat' => '{asPrinted|default(\'?\')} → {reading|default(\'?\')}',
        'tab' => 'The fault',
        'fields' => [
            'asPrinted'   => ['plain', 'As printed in the source', ['required' => true]],
            'reading'     => ['plain', 'What we publish instead', []],
            'basis'       => ['plain', 'On what basis', ['multiline' => true, 'required' => true]],
            'decidedBy'   => ['plain', 'Decided by', []],
            /* The two beyond the approved four. Strike both and a fault is
               still a record, just one nothing can find. */
            'faultRecord' => ['entries', 'The record it is in', ['sources' => ['elections', 'candidacies', 'documents'], 'maxRelations' => 1]],
            'faultField'  => ['plain', 'The field it is in', []],
        ],
    ],
];

/* Reused as they are, so these cite the way everything else does. */
$REUSED = ['footnotes', 'footnotesOn', 'editorNotes', 'recordProvenance'];

$fs = Craft::$app->getFields();
$svc = Craft::$app->getEntries();

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL;
echo str_repeat('=', 76) . PHP_EOL;

/* --------------------------------------------------------------- the plan */
$problems = [];
foreach ($SECTIONS as $handle => $def) {
    $existing = $svc->getSectionByHandle($handle);
    echo PHP_EOL . strtoupper($handle) . ': ' . ($existing ? 'section exists' : 'would create, channel')
       . ', URLs ' . ($def['urls'] ? 'ON at /' . $def['uri'] . ' -> ' . $def['template'] . '.twig' : 'OFF') . PHP_EOL;

    if ($def['urls']) {
        $tpl = \Craft::getAlias('@root') . '/templates/' . $def['template'] . '.twig';
        echo '   template ' . $def['template'] . '.twig: ' . (is_file($tpl) ? 'exists' : 'NOT BUILT YET, the section renders 404 until it is') . PHP_EOL;
    }

    foreach ($def['fields'] as $fh => [$kind, $label, $opts]) {
        $have = $fs->getFieldByHandle($fh);
        $note = '';
        if ($kind === 'entries') {
            foreach ((array)$opts['sources'] as $src) {
                /* elections and candidacies may not exist yet on a first run;
                   they are created earlier in this same script, in order. */
                $found = $svc->getSectionByHandle($src) || isset($SECTIONS[$src]);
                if (!$found) { $problems[] = $handle . '.' . $fh . ' wants section ' . $src; }
                $note .= ($note ? ', ' : '  -> ') . $src . ($svc->getSectionByHandle($src) ? '' : ' (created by this run)');
            }
            if (($opts['maxRelations'] ?? null) === 1) { $note .= ', one only'; }
        }
        if ($kind === 'dropdown') { $note = '  ' . implode(', ', array_keys($opts['options'])); }
        if ($kind === 'table') { $note = '  ' . implode(', ', array_column($opts['columns'], 'heading')); }
        if (!empty($opts['required'])) { $note .= '  REQUIRED'; }
        printf("   %-18s %-9s %s%s\n", $fh, $kind, $have ? 'exists already' : 'would create', $note);
    }
}

echo PHP_EOL . 'reused on all three: '
   . implode(', ', array_map(fn($h) => $h . ($fs->getFieldByHandle($h) ? '' : ' (MISSING)'), $REUSED)) . PHP_EOL;

/* ------------------------------------------- what it would hold on day one */
echo PHP_EOL . 'what the nine City Clerk PDFs would fill, on the readings taken 25 September:' . PHP_EOL;
$known = [
    ['1987-04-14 to 2010-04-13', '12 elections', 'the 1987-2012 summary: names and vote totals, no canvass'],
    ['2012-04-10', '5 candidates, 2 seats', 'Resolution 12-9 DECLARES Kellar and Boydston elected; 15,390 ballots'],
    ['2014-04-08', '13 candidates', 'City results by precinct; 111,661 registered, 15,871 ballots, 14.2%'],
    ['2016-11-08', 'consolidated', 'County statement of votes cast; 117,972 / 90,947; no winner named'],
    ['2018-11-06', 'consolidated', 'County statement of votes cast; 125,206 / 85,607; no winner named'],
    ['2020-11-03', 'consolidated', 'County statement of votes cast, scan; 142,880 / 121,458; no winner named'],
    ['2022-11-08', 'consolidated', 'County statement of votes cast, scan; 145,251 / 79,288; no winner named'],
    ['2024-11-05', 'District 1 only', 'County statement of votes cast; 23,054 / 15,508; District 3 NOT HELD'],
];
foreach ($known as [$when, $what, $note]) { printf("   %-24s %-16s %s\n", $when, $what, $note); }
echo PHP_EOL . '   so: one election of the seven canvasses carries a certified outcome. Every' . PHP_EOL;
echo '   November winner would import with outcomeEvidence = uncited until the' . PHP_EOL;
echo '   declaring resolutions are obtained. That is the point of the field.' . PHP_EOL;

$people = \craft\elements\Entry::find()->section('persons')->status(null)->count();
$docs = \craft\elements\Entry::find()->section('documents')->status(null)->count();
echo PHP_EOL . '   person records that candidacies could match against: ' . $people . PHP_EOL;
echo '   documents to hang sourceDocuments on: ' . $docs . ' (the nine PDFs are not among them yet)' . PHP_EOL;

if ($problems) {
    throw new \RuntimeException('add_elections_schema: ' . implode('; ', $problems));
}

if (!$APPLY) {
    echo PHP_EOL . str_repeat('=', 76) . PHP_EOL;
    echo 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL;
    echo 'after the apply: the nine PDFs become document records, then the canvass parse,' . PHP_EOL;
    echo 'then templates/elections/_entry.twig and the index.' . PHP_EOL;
    return;
}

/* ------------------------------------------------------------- creating

   Order, and it is not cosmetic. Craft 5 validates a section against its entry
   types, so a section saved before its type returns "Entry Types cannot be
   blank" — that is what failed add_office_holding on 24 September. Within a
   section: fields, then the type carrying the layout, then the section. Across
   sections: elections before candidacies, because candidacyElection needs the
   elections section UID to point at. Every failure throws; a script that prints
   FAILED and exits 0 lets an && chain carry on over the top of it. */
$created = 0;

$makeField = function (string $handle, array $spec) use ($fs, $svc, &$created) {
    [$kind, $label, $opts] = $spec;
    $f = $fs->getFieldByHandle($handle);
    if ($f) { return $f; }

    $f = match ($kind) {
        'entries' => new \craft\fields\Entries(),
        'dropdown' => new \craft\fields\Dropdown(),
        'number' => new \craft\fields\Number(),
        'table' => new \craft\fields\Table(),
        default => new \craft\fields\PlainText(),
    };
    $f->name = $label;
    $f->handle = $handle;
    if (!empty($opts['instructions'])) { $f->instructions = $opts['instructions']; }

    if ($kind === 'entries') {
        $sources = [];
        foreach ((array)$opts['sources'] as $srcHandle) {
            $src = $svc->getSectionByHandle($srcHandle);
            if (!$src) {
                throw new \RuntimeException('add_elections_schema: ' . $handle . ' needs section ' . $srcHandle . ', which does not exist at the moment the field is made');
            }
            $sources[] = 'section:' . $src->uid;
        }
        $f->sources = $sources;
        $f->maxRelations = $opts['maxRelations'] ?? null;
    }
    if ($kind === 'dropdown') {
        $default = $opts['default'] ?? null;
        $f->options = array_map(
            fn($v, $l) => ['label' => $l, 'value' => $v, 'default' => $v === $default],
            array_keys($opts['options']), array_values($opts['options'])
        );
    }
    if ($kind === 'number') { $f->decimals = 0; $f->min = 0; $f->max = null; }
    if ($kind === 'table') {
        $f->columns = $opts['columns'];
        $f->defaults = [];
        $f->addRowLabel = $opts['addRowLabel'] ?? 'Add a row';
    }
    if ($kind === 'plain' && !empty($opts['multiline'])) { $f->multiline = true; }

    if (!$fs->saveField($f)) {
        throw new \RuntimeException('add_elections_schema: field ' . $handle . ' refused: ' . json_encode($f->getErrors()));
    }
    $created++;
    return $fs->getFieldByHandle($handle);
};

foreach ($SECTIONS as $handle => $def) {
    $section = $svc->getSectionByHandle($handle);

    $layoutFields = [];
    $required = [];
    foreach ($def['fields'] as $fh => $spec) {
        $layoutFields[] = $makeField($fh, $spec);
        if (!empty($spec[2]['required'])) { $required[$fh] = true; }
    }
    foreach ($REUSED as $rh) { if ($f = $fs->getFieldByHandle($rh)) { $layoutFields[] = $f; } }
    echo $handle . ': ' . count($layoutFields) . ' fields ready' . PHP_EOL;

    $type = $svc->getEntryTypeByHandle($def['type']);
    if (!$type) {
        $type = new \craft\models\EntryType([
            'name' => $def['typeName'],
            'handle' => $def['type'],
            'hasTitleField' => $def['titleFormat'] === null,
            'titleFormat' => $def['titleFormat'],
        ]);
        $layout = new \craft\models\FieldLayout(['type' => \craft\elements\Entry::class]);
        $tab = new \craft\models\FieldLayoutTab(['name' => $def['tab'], 'layout' => $layout]);
        $tab->setElements(array_map(function ($f) use ($required) {
            $el = new \craft\fieldlayoutelements\CustomField($f);
            if (isset($required[$f->handle])) { $el->required = true; }
            return $el;
        }, $layoutFields));
        $layout->setTabs([$tab]);
        $type->setFieldLayout($layout);
        if (!$svc->saveEntryType($type)) {
            throw new \RuntimeException('add_elections_schema: entry type ' . $def['type'] . ' refused: ' . json_encode($type->getErrors()));
        }
        $created++;
        $type = $svc->getEntryTypeByHandle($def['type']);
        echo $handle . ': created the entry type' . PHP_EOL;
    }

    if (!$section) {
        $section = new \craft\models\Section([
            'name' => $def['name'],
            'handle' => $handle,
            'type' => \craft\models\Section::TYPE_CHANNEL,
            'enableVersioning' => true,
            'siteSettings' => array_map(fn($site) => new \craft\models\Section_SiteSettings([
                'siteId' => $site->id,
                'enabledByDefault' => true,
                'hasUrls' => $def['urls'],
                'uriFormat' => $def['uri'],
                'template' => $def['template'],
            ]), Craft::$app->getSites()->getAllSites()),
        ]);
        $section->setEntryTypes([$type]);
        if (!$svc->saveSection($section)) {
            throw new \RuntimeException('add_elections_schema: section ' . $handle . ' refused: ' . json_encode($section->getErrors()));
        }
        $created++;
        echo $handle . ': created the section with its entry type attached' . PHP_EOL;
    } else {
        $have = array_map(fn($t) => $t->handle, $section->getEntryTypes());
        if (!in_array($def['type'], $have, true)) {
            $section->setEntryTypes(array_merge($section->getEntryTypes(), [$type]));
            if (!$svc->saveSection($section)) {
                throw new \RuntimeException('add_elections_schema: attaching ' . $def['type'] . ' refused: ' . json_encode($section->getErrors()));
            }
            echo $handle . ': attached the entry type to the existing section' . PHP_EOL;
        }
    }
}

/* -------------------------------------------------------------- read back */
Craft::$app->getFields()->refreshFields();
$fails = [];
foreach ($SECTIONS as $handle => $def) {
    $s = $svc->getSectionByHandle($handle);
    $t = $svc->getEntryTypeByHandle($def['type']);
    $present = $t ? array_map(fn($c) => $c->handle, $t->getFieldLayout()->getCustomFields()) : [];
    $want = array_merge(array_keys($def['fields']), array_filter($REUSED, fn($h) => (bool)$fs->getFieldByHandle($h)));
    $missing = array_values(array_diff($want, $present));
    $urlsRight = $s && (bool)array_filter($s->getSiteSettings(), fn($ss) => $ss->hasUrls) === $def['urls'];

    echo PHP_EOL . $handle . ': section ' . ($s ? 'present' : 'MISSING')
       . ', type ' . ($t ? 'present' : 'MISSING')
       . ', fields on the layout ' . count($present) . ' of ' . count($want)
       . ', URLs ' . ($urlsRight ? 'as intended' : 'WRONG') . PHP_EOL;
    if ($missing) { echo '   missing: ' . implode(', ', $missing) . PHP_EOL; }
    if (!$s || !$t || $missing || !$urlsRight) { $fails[] = $handle; }
}

/* One number checked against something already true: the candidacy relation
   must point at the elections section this run made, not at whatever else
   happens to be called elections. */
$cf = $fs->getFieldByHandle('candidacyElection');
$elections = $svc->getSectionByHandle('elections');
$pointsRight = $cf && $elections && in_array('section:' . $elections->uid, (array)$cf->sources, true);
echo PHP_EOL . 'candidacyElection points at the elections section: ' . ($pointsRight ? 'yes' : 'NO') . PHP_EOL;
if (!$pointsRight) { $fails[] = 'candidacyElection source'; }

echo PHP_EOL . 'READ-BACK ' . ($fails ? 'FAIL' : 'OK') . PHP_EOL;
if ($fails) { throw new \RuntimeException('add_elections_schema read-back failed: ' . implode(', ', $fails)); }

$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_elections_schema.php', $created,
    'verified: 3 sections, 3 types, 26 new fields, candidacyElection resolved',
    'the event, the standing and the transcription fault, each its own record; turnout computed at render');
