<?php
/**
 * The shared loader for the four event drafts Nathan approved on 6 October 2026 (Cityhood, the Placerita gold discovery,
 * the golden spike, the Newhall Incident). Modelled on create_st_francis_dam_2026_10_05.php. Returns a callable; each
 * create_<slug>_event_2026_10_05.php sets its own configuration and $APPLY and calls it:
 *
 *   $run = require \Craft::getAlias('@root') . '/scripts/import/_event_from_draft_2026_10_06.php';
 *   $run($CFG, $APPLY);
 *
 * What it does, refusing the whole event on any mismatch:
 *   - reads the v2 draft, whose every quotation was rechecked on 6 October 2026, and refuses if its SHA-256 differs from
 *     the pinned one, if its quotation check records a failure, or if any text carries an em dash, an ellipsis or a
 *     literal backslash;
 *   - checks every footnote is cited and every [n] has a footnote; the content advisory, where the draft has one, must
 *     be the first editor note, in the top position;
 *   - relations only where the draft ties the target to footnotes and one of those footnotes names it (the reviewed
 *     terms are in each script); articles only where a footnote names the article by its record number;
 *   - sourceDocuments: every documents-section record a footnote or editor note cites by number. The field is being added
 *     to the event type by another step (add_event_sources_and_document_authors_2026_10_06.php). The loader reads the
 *     event type's layout: before the field exists it reports what it would set and sets nothing; run again after the
 *     field exists, it fills sourceDocuments on the event it made, if that field is still empty, and nothing else;
 *   - photographs take the event in photoEvents, appended, never replacing, only where a footnote the draft ties them to
 *     names their code or record number. The others are held and listed ($CFG['linkHeldPhotos'] = true links them too);
 *   - writes nothing to any other record.
 * Idempotent: the event is matched on title plus eventDateEdtf; a photograph already carrying the event is skipped; a
 * second run is a no-op.
 */

use craft\elements\{Entry, Category, Asset};

return function (array $CFG, bool $APPLY): void {
    $root = \Craft::getAlias('@root');
    $SCRIPT = $CFG['script'];
    $log = [];
    $say = function (string $s) use (&$log) { echo $s . PHP_EOL; $log[] = $s; };
    $say(($APPLY ? 'APPLYING' : 'DRY RUN') . " $SCRIPT");
    $say(str_repeat('=', 78));

    $bad = [];
    $textFaults = function (string $label, string $s): array {
        $b = [];
        if (preg_match('~\x{2014}~u', $s)) { $b[] = "$label: an em dash"; }
        if (preg_match('~\.\.\.|\x{2026}~u', $s)) { $b[] = "$label: an ellipsis (none survives the v2 check)"; }
        if (str_contains($s, '\\')) { $b[] = "$label: a literal backslash"; }
        return $b;
    };
    $entryFault = function (int $id, string $section, string $title, bool $prefix = true): ?string {
        $e = Entry::find()->id($id)->status(null)->one();
        if (!$e) { return "#$id missing"; }
        if ($e->section->handle !== $section) { return "#$id is in {$e->section->handle}, expected $section"; }
        if ($prefix ? !str_starts_with($title, $e->title) : $e->title !== $title) { return "#$id is \"{$e->title}\", expected \"$title\""; }
        return null;
    };

    /* ---------------------------------------------------------------- the draft */
    $raw = @file_get_contents("$root/{$CFG['v2']}");
    $v = [];
    if ($raw === false) { $bad[] = "cannot read {$CFG['v2']}"; }
    else {
        if (hash('sha256', $raw) !== $CFG['sha']) { $bad[] = "{$CFG['v2']} changed since its quotations were checked (SHA-256 differs); recheck, then pin the new hash"; }
        $v = json_decode($raw, true) ?: [];
    }
    $F = $v['fields'] ?? [];
    $TITLE = (string)($v['title'] ?? '');
    if ($TITLE !== $CFG['title']) { $bad[] = "v2 title is \"$TITLE\", expected \"{$CFG['title']}\""; }
    $qc = $v['quotationCheck']['counts'] ?? null;
    if (!$qc || ($qc['fail'] ?? 1) !== 0) { $bad[] = 'v2 does not record a quotation check with no failures'; }
    foreach ($v['quotationCheck']['results'] ?? [] as $r) { if ($r['status'] === 'FAIL') { $bad[] = "quotation FAIL at {$r['where']}: " . mb_substr($r['quotation'], 0, 60); } }
    foreach (['eventDate', 'eventDateEdtf'] as $h) { if ((string)($F[$h] ?? '') !== $CFG[$h]) { $bad[] = "v2 $h is \"" . ($F[$h] ?? '') . "\", expected \"{$CFG[$h]}\""; } }

    $BODY = (string)($F['body'] ?? '');
    $NOTES = $F['footnotes'] ?? [];
    $N = count($NOTES);
    $noteOf = fn(int $n) => (string)($NOTES[$n - 1] ?? '');
    $bad = array_merge($bad, $textFaults('body', $BODY), $textFaults('eventSignificance', (string)($F['eventSignificance'] ?? '')));
    preg_match_all('~\[(\d+)\]~', $BODY, $m); $cited = array_map('intval', $m[1]);
    foreach ($cited as $c) { if ($c < 1 || $c > $N) { $bad[] = "body: [$c] has no footnote"; } }
    foreach (range(1, max(1, $N)) as $i) { if ($N && !in_array($i, $cited, true)) { $bad[] = "body: footnote $i is never cited"; } }
    if (!$N) { $bad[] = 'no footnotes'; }
    foreach ($NOTES as $i => $t) { $bad = array_merge($bad, $textFaults('footnote ' . ($i + 1), $t)); }
    $EDITOR = $F['editorNotes'] ?? [];
    foreach ($EDITOR as $i => $r) {
        $bad = array_merge($bad, $textFaults('editor note ' . ($i + 1), $r['heading'] . ' ' . $r['note']));
        if (!in_array($r['position'] ?? '', ['top', 'bottom', 'inline'], true)) { $bad[] = 'editor note ' . ($i + 1) . ': position "' . ($r['position'] ?? '') . '"'; }
        if (stripos($r['heading'], 'advisory') !== false && ($i !== 0 || $r['position'] !== 'top')) { $bad[] = 'the content advisory is not the first editor note in the top position'; }
    }
    $hasAdvisory = isset($EDITOR[0]) && $EDITOR[0]['heading'] === 'Content advisory' && $EDITOR[0]['position'] === 'top';
    if ($CFG['advisory'] !== $hasAdvisory) { $bad[] = $CFG['advisory'] ? 'the draft has no content advisory at the top, and this event needs one' : 'a content advisory this script did not expect'; }
    $LEADS = $v['researchLeads'] ?? [];
    foreach ($LEADS as $i => $l) { $bad = array_merge($bad, $textFaults('research lead ' . ($i + 1), $l)); }

    /* ---------------------------------------------------------------- relations */
    $REL = ['eventPersons' => 'persons', 'eventPlaces' => 'places', 'eventOrganizations' => 'organizations', 'eventFallenOfficers' => 'fallenOfficers'];
    $relIds = []; $relRows = [];
    foreach ($REL as $h => $sec) {
        $relIds[$h] = [];
        foreach ($F[$h] ?? [] as $row) {
            if (!is_array($row)) { continue; }
            $id = (int)$row['id']; $relRows[$id] = $row + ['field' => $h];
            if (!isset($CFG['terms'][$id])) { $bad[] = "$h #$id: not in this script's reviewed list of ties"; continue; }
            if ($f = $entryFault($id, $sec, (string)$row['title'])) { $bad[] = "$h: $f"; continue; }
            $nums = $row['footnotes'] ?? [];
            if (!$nums) { $bad[] = "$h #$id: no footnote ties it"; continue; }
            $named = false;
            foreach ($nums as $n) {
                if ($n < 1 || $n > $N) { $bad[] = "$h #$id: footnote $n does not exist"; continue; }
                foreach ($CFG['terms'][$id] as $t) { if (str_contains($noteOf((int)$n), $t)) { $named = true; } }
            }
            if (!$named) { $bad[] = "$h #$id: none of footnotes " . implode(', ', $nums) . ' names it'; continue; }
            $relIds[$h][] = $id;
        }
    }
    foreach ($CFG['terms'] as $id => $t) { if (!isset($relRows[$id])) { $bad[] = "reviewed tie #$id is not in the draft"; } }

    /* Articles: only where a footnote names the article by its record number. */
    $articles = []; $articlesHeld = [];
    foreach ($F['eventArticles'] ?? [] as $row) {
        $id = (int)$row['id']; $where = [];
        foreach ($NOTES as $i => $t) { if (preg_match('~#' . $id . '\b~', $t)) { $where[] = $i + 1; } }
        if ($where) {
            if ($f = $entryFault($id, 'articles', (string)$row['title'])) { $bad[] = "article: $f"; continue; }
            $articles[$id] = $where;
        } else { $articlesHeld[] = $row; }
    }

    /* sourceDocuments: documents cited by number in a footnote or an editor note. */
    $docs = []; $notDocs = [];
    $scan = $NOTES; foreach ($EDITOR as $r) { $scan[] = $r['note']; }
    foreach ($scan as $i => $t) {
        preg_match_all('~#(\d{3,6})\b~', $t, $mm);
        foreach ($mm[1] as $id) {
            $id = (int)$id; $e = Entry::find()->id($id)->status(null)->one();
            $where = $i < $N ? 'note ' . ($i + 1) : 'editor note ' . ($i - $N + 1);
            if ($e && $e->section->handle === 'documents') { $docs[$id][] = $where; }
            elseif ($e) { $notDocs[$id] = $e->section->handle . ' "' . $e->title . '"'; }
            else { $bad[] = "#$id cited in $where does not exist"; }
        }
    }
    $docs = array_map(fn($w) => array_values(array_unique($w)), $docs);
    $DOC_IDS = array_keys($docs);

    /* Photographs. */
    $photos = []; $photosHeld = [];
    foreach ($F['photographs'] ?? [] as $row) {
        $id = (int)$row['id'];
        if ($f = $entryFault($id, 'photographs', (string)$row['title'], false)) { $bad[] = "photograph: $f"; continue; }
        $p = Entry::find()->id($id)->status(null)->one();
        $has = []; foreach ($p->getFieldLayout()->getCustomFields() as $fl) { $has[$fl->handle] = true; }
        if (!isset($has['photoEvents'])) { $bad[] = "photograph #$id has no photoEvents field"; continue; }
        $code = isset($has['photoSourceCode']) ? (string)$p->getFieldValue('photoSourceCode') : '';
        if ($code !== $row['photoId']) { $bad[] = "photograph #$id is \"$code\", the draft says {$row['photoId']}"; continue; }
        $named = [];
        foreach ($row['footnotes'] ?? [] as $n) {
            $t = $noteOf((int)$n);
            if (preg_match('~\b' . preg_quote($row['photoId'], '~') . '\b~i', $t) || preg_match('~#' . $id . '\b~', $t)) { $named[] = (int)$n; }
        }
        $cur = $p->photoEvents->status(null)->ids();
        $item = ['row' => $row, 'current' => $cur, 'named' => $named];
        if ($named || !empty($CFG['linkHeldPhotos'])) { $photos[$id] = $item; }
        else { $photosHeld[$id] = $item + ['why' => ($row['footnotes'] ?? []) ? 'footnote ' . implode(', ', $row['footnotes']) . ' does not name it' : 'no footnote cites it (the draft says so)']; }
    }

    /* Categories. */
    $CATS = [];
    foreach (['historicalEra' => 'historicalEra', 'historicalPeriod' => 'historicalPeriod', 'recordTags' => 'theme', 'neighborhood' => 'neighborhood'] as $h => $group) {
        $CATS[$h] = [];
        foreach ($F[$h] ?? [] as $row) {
            $c = Category::find()->id((int)$row['id'])->status(null)->one();
            if (!$c) { $bad[] = "$h: category #{$row['id']} missing"; continue; }
            if ($c->group->handle !== $group || !str_starts_with((string)$row['title'], $c->title)) { $bad[] = "$h: #{$row['id']} is {$c->group->handle} / {$c->title}, the draft says {$row['title']}"; continue; }
            $CATS[$h][] = (int)$row['id'];
        }
    }

    /* recordDates. */
    $DATES = [];
    $gran = array_column(Craft::$app->getFields()->getFieldByHandle('recordDates')->columns['col3']['options'] ?? [], 'value');
    foreach ($F['recordDates'] ?? [] as $i => $r) {
        $iso = (string)($r['iso'] ?? '');
        if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', $iso)) { $bad[] = 'recordDates row ' . ($i + 1) . ': no ISO date'; }
        if (!in_array($r['granularity'] ?? '', $gran, true)) { $bad[] = 'recordDates row ' . ($i + 1) . ': granularity "' . ($r['granularity'] ?? '') . '"'; }
        $bad = array_merge($bad, $textFaults('recordDates row ' . ($i + 1), $r['printed'] . ' ' . $r['label']));
        $DATES[] = ['printed' => $r['printed'], 'iso' => $iso . ' 00:00:00', 'granularity' => $r['granularity'], 'label' => $r['label'], 'confirmed' => (bool)($r['confirmed'] ?? false), 'rejected' => false];
    }

    /* Featured image: only an asset the draft names that is already in Craft. */
    $FEATURED = [];
    if (!empty($CFG['featuredImage'])) {
        [$aid, $fname] = $CFG['featuredImage'];
        $a = Asset::find()->id($aid)->one();
        if (!$a) { $bad[] = "featuredImage asset $aid missing"; }
        elseif ($a->filename !== $fname) { $bad[] = "featuredImage asset $aid is {$a->filename}, expected $fname"; }
        else { $FEATURED = [$aid]; }
    }

    $EVENT = [
        'body' => $BODY,
        'footnotes' => array_map(fn($i, $t) => ['number' => (string)($i + 1), 'note' => $t, 'source' => 'editorial-2026'], array_keys($NOTES), $NOTES),
        'editorNotes' => array_map(fn($r) => ['heading' => $r['heading'], 'note' => $r['note'], 'position' => $r['position']], $EDITOR),
        'researchLeads' => implode("\n\n", $LEADS),
        'eventDate' => (string)($F['eventDate'] ?? ''), 'eventDateEdtf' => (string)($F['eventDateEdtf'] ?? ''),
        'eventDateStart' => (string)($F['eventDateStart'] ?? ''), 'eventDateEnd' => (string)($F['eventDateEnd'] ?? ''),
        'startEvidence' => (string)($F['startEvidence'] ?? ''), 'eventChlNumber' => (string)($F['eventChlNumber'] ?? ''),
        'eventSignificance' => (string)($F['eventSignificance'] ?? ''),
        'recordDates' => $DATES,
        'historicalEra' => $CATS['historicalEra'], 'historicalPeriod' => $CATS['historicalPeriod'], 'recordTags' => $CATS['recordTags'], 'neighborhood' => $CATS['neighborhood'],
        'eventPersons' => $relIds['eventPersons'], 'eventPlaces' => $relIds['eventPlaces'], 'eventOrganizations' => $relIds['eventOrganizations'],
        'eventFallenOfficers' => $relIds['eventFallenOfficers'], 'eventArticles' => array_keys($articles),
        'featuredImage' => $FEATURED,
    ];
    /* Leave out what is empty: an empty value never writes. */
    $EVENT = array_filter($EVENT, fn($x) => $x !== '' && $x !== []);

    $evSec = Craft::$app->getEntries()->getSectionByHandle('events'); $evType = null;
    if ($evSec) { foreach ($evSec->getEntryTypes() as $et) { if ($et->handle === 'event') { $evType = $et; } } }
    $haveSD = false;
    if (!$evType) { $bad[] = 'no events/event type'; }
    else {
        $have = []; foreach ($evType->getFieldLayout()->getCustomFields() as $f) { $have[$f->handle] = true; }
        foreach (array_keys($EVENT) as $h) { if (!isset($have[$h])) { $bad[] = "no field $h on the event type"; } }
        $haveSD = isset($have['sourceDocuments']);
        $opts = array_column(Craft::$app->getFields()->getFieldByHandle('startEvidence')->options, 'value');
        if (!in_array($EVENT['startEvidence'] ?? '', $opts, true)) { $bad[] = 'startEvidence "' . ($EVENT['startEvidence'] ?? '') . '" is not an option'; }
        foreach (['eventDateEdtf'] as $h) {
            $lim = Craft::$app->getFields()->getFieldByHandle($h)->charLimit;
            if ($lim && mb_strlen($EVENT[$h]) > $lim) { $bad[] = "$h over its limit of $lim"; }
        }
        foreach ($EVENT as $h => $val) {
            $fl = Craft::$app->getFields()->getFieldByHandle($h);
            if (is_string($val) && $fl instanceof \craft\fields\PlainText && $fl->charLimit && mb_strlen($val) > $fl->charLimit) { $bad[] = "$h is " . mb_strlen($val) . " characters, the limit {$fl->charLimit}"; }
        }
    }
    $eventHave = null;
    foreach (Entry::find()->section('events')->status(null)->title($TITLE)->all() as $e) {
        if ((string)$e->eventDateEdtf === $CFG['eventDateEdtf']) { $eventHave = $e; }
        else { $bad[] = "event #{$e->id} \"$TITLE\" exists with another date (" . $e->eventDateEdtf . ')'; }
    }
    $sdNow = ($eventHave && $haveSD) ? $eventHave->sourceDocuments->status(null)->ids() : [];

    /* ---------------------------------------------------------------- the plan */
    $t = fn($id) => Entry::find()->id($id)->status(null)->one()?->title ?? '?';
    $words = str_word_count(preg_replace('~\[\d+\]~', '', $BODY));
    $say('EVENT: ' . ($eventHave ? "#{$eventHave->id} \"$TITLE\" exists, not recreated" : "create \"$TITLE\": {$EVENT['eventDate']} ({$EVENT['eventDateEdtf']}), $N notes, " . count($EDITOR) . " editor notes, $words words, " . count($DATES) . ' dated rows, ' . count($LEADS) . ' research leads'));
    $say('    content advisory: ' . ($hasAdvisory ? 'yes, the first editor note, top' : 'none (the draft has none)'));
    foreach (['historicalEra', 'historicalPeriod', 'recordTags', 'neighborhood'] as $h) {
        $say("    $h: " . (($CATS[$h] ?? []) ? implode('; ', array_map(fn($id) => "#$id " . Category::find()->id($id)->one()?->title, $CATS[$h])) : 'none'));
    }
    foreach (['eventPersons', 'eventPlaces', 'eventOrganizations', 'eventFallenOfficers'] as $h) {
        $say("    $h: " . ($relIds[$h] ? implode('; ', array_map(fn($id) => "#$id " . $t($id) . ' (notes ' . implode(', ', $relRows[$id]['footnotes']) . ')', $relIds[$h])) : 'none'));
    }
    $say('    eventArticles: ' . ($articles ? implode('; ', array_map(fn($id) => "#$id " . $t($id) . ' (note ' . implode(', ', $articles[$id]) . ')', array_keys($articles))) : 'none'));
    $say('    articles held, no footnote names them: ' . ($articlesHeld ? implode('; ', array_map(fn($r) => "#{$r['id']} {$r['title']}", $articlesHeld)) : 'none'));
    $say('    sourceDocuments: ' . ($DOC_IDS ? implode('; ', array_map(fn($id) => "#$id " . $t($id) . ' (' . implode(', ', $docs[$id]) . ')', $DOC_IDS)) : 'none cited'));
    $say('        ' . ($haveSD ? ($eventHave ? ($sdNow ? 'the event already has ' . json_encode($sdNow) . '; not changed' : ($DOC_IDS ? 'the field is on the event type: fill the event\'s empty sourceDocuments' : 'nothing to set')) : 'the field is on the event type: set with the event') : 'the field is NOT on the event type yet: nothing is set now; run this script again once it is, and it fills the field on the event it made'));
    $say('    cited records that are not documents (not in sourceDocuments): ' . ($notDocs ? implode('; ', array_map(fn($id, $s) => "#$id $s", array_keys($notDocs), $notDocs)) : 'none'));
    $say('    featuredImage: ' . ($FEATURED ? "asset {$FEATURED[0]} ({$CFG['featuredImage'][1]})" : 'none'));
    foreach ($photos as $id => $p) {
        $say("    photograph #$id {$p['row']['photoId']}: photoEvents " . ($eventHave && in_array($eventHave->id, $p['current']) ? 'has it' : 'append the event to ' . json_encode($p['current'])) . ($p['named'] ? ' (named in note ' . implode(', ', $p['named']) . ')' : ' (held photograph, linked by the flag)'));
    }
    foreach ($photosHeld as $id => $p) { $say("    photograph #$id {$p['row']['photoId']}: HELD, {$p['why']}"); }
    $say('    other records: nothing written');
    $say('    REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none'));

    /* ---------------------------------------------------------------- the record as it would read */
    $cell = fn($s) => str_replace(['|', "\n"], ['\\|', ' '], (string)$s);
    $md = [];
    $md[] = "# {$TITLE}: the loader's dry run, 6 October 2026";
    $md[] = '';
    $md[] = "Written by `scripts/import/$SCRIPT` (with `scripts/import/_event_from_draft_2026_10_06.php`) in " . ($APPLY ? 'an apply' : 'a dry run') . '. A dry run writes nothing to Craft. The event is read from `' . $CFG['v2'] . '` (SHA-256 `' . substr($CFG['sha'], 0, 16) . '...`), the v2 draft whose quotations were rechecked on 6 October 2026. Nathan approved the draft for applying on 6 October 2026.';
    $md[] = '';
    $md[] = '**Refusals:** ' . ($bad ? implode('; ', $bad) : 'none') . '.';
    $md[] = '';
    $md[] = '## The run';
    $md[] = '';
    $md[] = '```';
    foreach ($log as $l) { $md[] = $l; }
    $md[] = '```';
    $md[] = '';
    $md[] = '## Quotation check';
    $md[] = '';
    $md[] = sprintf('%d quotations in the v2 draft (%s) were checked word for word: %d PASS, %d CORRECTED from v1, %d FAIL. Ellipses: %s. Checked against: %s.',
        $qc['checked'] ?? 0, $v['quotationCheck']['scope'] ?? '', $qc['pass'] ?? 0, $qc['corrected'] ?? 0, $qc['fail'] ?? 0, $v['quotationCheck']['ellipses'] ?? '', $v['quotationCheck']['against'] ?? '');
    $md[] = '';
    $md[] = '"Terminal punctuation only" means the quotation stops where the source\'s sentence goes on and closes with a period or comma of its own; no word is changed.';
    $md[] = '';
    $md[] = '| # | Where | Quotation | Result | Page | Note |';
    $md[] = '| --- | --- | --- | --- | --- | --- |';
    foreach ($v['quotationCheck']['results'] ?? [] as $i => $r) {
        $q = $r['quotation']; if (mb_strlen($q) > 140) { $q = mb_substr($q, 0, 140) . ' [cut here for the table]'; }
        $md[] = '| ' . ($i + 1) . ' | ' . $cell($r['where']) . ' | ' . $cell($q) . ' | ' . $r['status'] . ' | ' . $cell($r['page']) . ' | ' . $cell($r['note']) . ' |';
    }
    $md[] = '';
    $md[] = '## Changes from v1 to v2';
    $md[] = '';
    foreach ($v['v2Changes'] ?? [] as $c) { $md[] = "{$c['n']}. **{$c['where']}.** Was: {$cell($c['was'])} Now: {$cell($c['now'])} Why: {$c['why']}"; }
    if (!($v['v2Changes'] ?? [])) { $md[] = 'None.'; }
    $md[] = '';
    $md[] = '## The event as it would read';
    $md[] = '';
    if ($hasAdvisory) { $md[] = "**Editor's note, {$EDITOR[0]['heading']} (top):** {$EDITOR[0]['note']}"; $md[] = ''; }
    foreach (['eventDate', 'eventDateEdtf', 'eventDateStart', 'eventDateEnd', 'startEvidence', 'eventChlNumber', 'eventSignificance'] as $h) { $md[] = "- **$h:** " . ($EVENT[$h] ?? '(empty)'); }
    foreach (['historicalEra', 'historicalPeriod', 'recordTags', 'neighborhood'] as $h) { $md[] = "- **$h:** " . (($CATS[$h] ?? []) ? implode('; ', array_map(fn($id) => "#$id " . Category::find()->id($id)->one()?->title, $CATS[$h])) : '(empty)'); }
    $md[] = '- **featuredImage:** ' . ($FEATURED ? "asset {$FEATURED[0]} ({$CFG['featuredImage'][1]}), " . $CFG['featuredImage'][2] : '(empty) ' . ($F['featuredImage'] ?? ''));
    $md[] = '- **bandImage:** (empty) ' . ($F['bandImage'] ?? '');
    $md[] = '';
    foreach (explode("\n\n", $BODY) as $p) { $md[] = $p; $md[] = ''; }
    foreach ($NOTES as $i => $n) { $md[] = ($i + 1) . '. ' . $n; }
    $md[] = '';
    foreach (array_slice($EDITOR, $hasAdvisory ? 1 : 0) as $r) { $md[] = "**Editor's note, {$r['heading']} ({$r['position']}):** {$r['note']}"; $md[] = ''; }
    if ($DATES) {
        $md[] = '### recordDates';
        $md[] = '';
        $md[] = '| Printed | ISO | Precision | What happened | Confirmed |';
        $md[] = '| --- | --- | --- | --- | --- |';
        foreach ($DATES as $r) { $md[] = '| ' . $cell($r['printed']) . ' | ' . substr($r['iso'], 0, 10) . ' | ' . $r['granularity'] . ' | ' . $cell($r['label']) . ' | ' . ($r['confirmed'] ? 'yes' : 'no') . ' |'; }
        $md[] = '';
    }
    $md[] = '### Relations';
    $md[] = '';
    foreach (['eventPersons', 'eventPlaces', 'eventOrganizations', 'eventFallenOfficers'] as $h) {
        foreach ($relIds[$h] as $id) { $r = $relRows[$id]; $md[] = "- **$h:** #$id " . $t($id) . ' (notes ' . implode(', ', $r['footnotes']) . (isset($r['tie']) ? '; ' . $r['tie'] : '') . ')'; }
    }
    foreach ($articles as $id => $w) { $md[] = "- **eventArticles:** #$id " . $t($id) . ' (named in note ' . implode(', ', $w) . ')'; }
    foreach ($DOC_IDS as $id) { $md[] = "- **sourceDocuments:** #$id " . $t($id) . ' (' . implode(', ', $docs[$id]) . ')' . ($haveSD ? '' : ' [field not yet on the event type; set on a later run]'); }
    foreach ($notDocs as $id => $s) { $md[] = "- **cited, not a document, not related:** #$id $s"; }
    foreach ($photos as $id => $p) { $md[] = "- **photograph #$id {$p['row']['photoId']}** takes the event in photoEvents (named in note " . implode(', ', $p['named']) . '; ' . ($p['row']['tie'] ?? '') . ')'; }
    foreach ($photosHeld as $id => $p) { $md[] = "- **Held, photograph #$id {$p['row']['photoId']}** \"{$p['row']['title']}\": {$p['why']}. Set `\$CFG['linkHeldPhotos'] = true` to link the held photographs too."; }
    foreach ($articlesHeld as $r) { $md[] = "- **Held, article #{$r['id']}** {$r['title']}" . (isset($r['note']) ? " ({$r['note']})" : '') . ': no footnote names it'; }
    $rel = $F['relatedEvents'] ?? [];
    $md[] = '- **relatedEvents:** ' . ($rel ? 'none set; the draft says: ' . implode(' ', array_map(fn($x) => is_string($x) ? $x : json_encode($x), $rel)) : 'none');
    $md[] = '- **footnotesOn:** not set. On this site it means "the notes were published on another record" (create_saugus_2019_event_2026_10_05.php); the documents the draft listed there go to sourceDocuments instead.';
    $md[] = '- **Not written:** every other record. The draft\'s forNathan recommendations about other records (new place or organization records, changes to person records, image attachments) are not acted on.';
    $md[] = '';
    $md[] = '### Research leads (researchLeads, not shown on the page)';
    $md[] = '';
    foreach ($LEADS as $l) { $md[] = '- ' . $l; }
    $md[] = '';
    $md[] = '## From the draft\'s forNathan (approved with the draft; listed for the record)';
    $md[] = '';
    foreach ($v['forNathan'] ?? [] as $l) { $md[] = '- ' . $l; }
    $md[] = '';
    file_put_contents("$root/{$CFG['out']}", implode("\n", $md));
    echo "wrote {$CFG['out']}" . PHP_EOL;

    if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written to Craft. Set $APPLY = true to apply.' . PHP_EOL; return; }
    if ($bad) { echo 'REFUSING: nothing written' . PHP_EOL; return; }

    /* ---------------------------------------------------------------- apply */
    $els = Craft::$app->getElements(); $rows = 0;
    if (!$eventHave) {
        if ($haveSD && $DOC_IDS) { $EVENT['sourceDocuments'] = $DOC_IDS; }
        $e = new Entry(); $e->sectionId = $evSec->id; $e->setTypeId($evType->id); $e->title = $TITLE;
        $e->setFieldValues($EVENT);
        if (!$els->saveElement($e)) { throw new \RuntimeException('event: ' . json_encode($e->getFirstErrors())); }
        $rows++;
        $eventHave = Entry::find()->id($e->id)->status(null)->one();
        /* status(null): keep unpublished targets (the fallen officers) when reading a relation back. */
        $ok = trim((string)$eventHave->body) === trim($BODY) && count($eventHave->footnotes ?? []) === $N && $eventHave->eventDateEdtf === $CFG['eventDateEdtf']
            && count($eventHave->editorNotes ?? []) === count($EDITOR) && count($eventHave->recordDates ?? []) === count($DATES);
        foreach (['eventPersons', 'eventPlaces', 'eventOrganizations', 'eventFallenOfficers', 'eventArticles'] as $h) {
            $ok = $ok && $eventHave->getFieldValue($h)->status(null)->ids() == ($EVENT[$h] ?? []);
        }
        if ($hasAdvisory) { $ok = $ok && ($eventHave->editorNotes[0]['position'] ?? '') === 'top'; }
        if (isset($EVENT['sourceDocuments'])) { $ok = $ok && $eventHave->sourceDocuments->status(null)->ids() == $DOC_IDS; }
        echo 'EVENT READ-BACK ' . ($ok ? "OK: #{$eventHave->id} {$eventHave->url}" : 'SHORT') . PHP_EOL;
        if (!$ok) { throw new \RuntimeException('event read-back failed; photographs not touched'); }
    } elseif ($haveSD && $DOC_IDS && !$sdNow) {
        /* The one change a later run makes: the empty sourceDocuments on the event this script made. */
        $eventHave->setFieldValue('sourceDocuments', $DOC_IDS);
        if (!$els->saveElement($eventHave)) { throw new \RuntimeException('sourceDocuments: ' . json_encode($eventHave->getFirstErrors())); }
        $rows++;
        $back = Entry::find()->id($eventHave->id)->status(null)->one()->sourceDocuments->status(null)->ids();
        echo 'sourceDocuments READ-BACK ' . ($back == $DOC_IDS ? 'OK' : 'SHORT') . PHP_EOL;
    }
    foreach ($photos as $id => $p) {
        $el = Entry::find()->id($id)->status(null)->one();
        $cur = $el->photoEvents->status(null)->ids();
        if (in_array($eventHave->id, $cur)) { echo "photograph #$id has it" . PHP_EOL; continue; }
        $el->setFieldValue('photoEvents', array_merge($cur, [$eventHave->id]));
        if (!$els->saveElement($el)) { throw new \RuntimeException("photograph #$id: " . json_encode($el->getFirstErrors())); }
        $rows++;
        $back = Entry::find()->id($id)->status(null)->one()->photoEvents->status(null)->ids();
        echo "photograph #$id READ-BACK " . ($back == array_merge($cur, [$eventHave->id]) ? 'OK' : 'SHORT') . PHP_EOL;
    }
    $applyLog = require $root . '/scripts/import/_apply_log.php';
    $applyLog($SCRIPT, $rows, 'verified', "$TITLE: event from the v2 draft; photoEvents on its cited photographs; sourceDocuments " . ($haveSD ? 'set' : 'waiting for the field'));
    echo "done: $rows saves. A second run is a no-op" . ($haveSD ? '' : ' (until sourceDocuments is on the event type: then it fills that field once)') . '.' . PHP_EOL;
};
