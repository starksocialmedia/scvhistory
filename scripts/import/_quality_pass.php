<?php
/**
 * The shared runner for the quality passes. Require it and call what it returns.
 *
 *   $run = require \Craft::getAlias('@root') . '/scripts/import/_quality_pass.php';
 *   $run([
 *       'script'  => 'quality_nav_junk.php',
 *       'label'   => 'Nav junk',
 *       'apply'   => $APPLY,
 *       'classes' => ['nav'],
 *       'propose' => function (\craft\elements\Entry $e, int $collId, array &$listed): ?array {
 *           return ['set' => ['body' => '...'], 'show' => ['- a removed line']];
 *       },
 *   ]);
 *
 * WHY ONE RUNNER
 *
 * The quality phase is five passes, each changing one fault class across the
 * collections, and each has to answer the same three questions before anybody
 * applies it: which pieces, what exactly changes, and what the report reads
 * afterwards. Answering the third honestly in a dry run means scoring the
 * proposed values rather than guessing. So the runner asks QualityReport for
 * the counts twice, once as stored and once with the proposals laid over the
 * stored values in memory, and prints both. Nothing is written for that.
 *
 * On apply, the counts are taken a third time from the database, and a
 * difference from the dry run's prediction is reported as loudly as a short
 * read-back, because it means the pass did not do what it said.
 *
 * SCOPE
 *
 * Pieces of collections only, by partOfCollection. Pieces of a frozen
 * collection are left out and counted. The guard in modules/collectionfreeze
 * would refuse them anyway, but a dry run that proposes changes it can never
 * make is a misleading dry run.
 *
 * A propose() returns null for nothing to do, or 'set' (handle => new value)
 * and 'show' (lines for the report). It can add to $listed: a piece it looked
 * at and would not touch, with the reason. Those are the editorial remainder.
 *
 * Every pass writes its whole plan to web/review/quality-<pass>.md.
 */

use craft\elements\Entry;

/* Remove body lines by index, and a blank line with each where the removal
   would leave two blank lines together. An emptied [lines] block goes too.
   Nothing else in the body is touched, so a diff shows only the lines named. */
$qpRemoveLines = function (string $body, array $drop): string {
    $L = explode("\n", $body);
    foreach (array_keys($drop) as $i) { $L[$i] = null; }
    $out = [];
    foreach ($L as $l) {
        if ($l === null) { continue; }
        if (trim($l) === '' && $out && trim(end($out)) === '') { continue; }
        $out[] = $l;
    }
    $s = implode("\n", $out);
    $s = preg_replace('~\[lines\]\s*\[/lines\]\n?~', '', $s);
    return preg_replace('~\A\s*\n~', '', $s);
};

return function (array $cfg): void {
    $APPLY   = (bool)$cfg['apply'];
    $script  = $cfg['script'];
    $label   = $cfg['label'];
    $classes = $cfg['classes'];
    $propose = $cfg['propose'];

    echo ($APPLY ? 'APPLYING' : 'DRY RUN') . '  ' . $label . PHP_EOL;
    echo str_repeat('=', 78) . PHP_EOL;

    $fields = Craft::$app->getFields();
    $hasFrozen = (bool)$fields->getFieldByHandle('collectionFrozen');

    /* ------------------------------------------------------------ scope */
    $collOf = []; $collSlug = []; $frozenSkipped = 0;
    foreach (Entry::find()->section('collections')->status(null)->limit(null)->all() as $c) {
        $collSlug[(int)$c->id] = $c->slug;
        $ids = Entry::find()->relatedTo(['targetElement' => $c, 'field' => 'partOfCollection'])
            ->status(null)->limit(null)->ids();
        if ($hasFrozen && $c->getFieldValue('collectionFrozen')) {
            $frozenSkipped += count($ids);
            echo 'frozen, left out: ' . $c->slug . ' (' . count($ids) . ' pieces)' . PHP_EOL;
            continue;
        }
        foreach ($ids as $id) { $collOf[(int)$id] ??= (int)$c->id; }
    }
    echo 'pieces in scope: ' . count($collOf) . ($frozenSkipped ? ', ' . $frozenSkipped . ' frozen left out' : '') . PHP_EOL;

    /* ------------------------------------------------------------- plan */
    $plan = []; $listed = [];
    foreach (Entry::find()->id(array_keys($collOf))->status(null)->limit(null)->each() as $e) {
        $cid = $collOf[(int)$e->id];
        $mine = [];
        $p = $propose($e, $cid, $mine);
        foreach ($mine as $why) { $listed[] = ['id' => (int)$e->id, 'coll' => $collSlug[$cid], 'title' => $e->title, 'why' => $why]; }
        if (!$p || empty($p['set'])) { continue; }

        /* Keep only what differs from the stored value. */
        $set = [];
        foreach ($p['set'] as $h => $v) {
            $cur = $e->getFieldValue($h);
            if (is_object($cur) && method_exists($cur, 'ids')) { $cur = $cur->status(null)->ids(); }
            if (is_array($v) ? array_values($v) != array_values((array)$cur) : (string)$v !== (string)$cur) { $set[$h] = $v; }
        }
        if (!$set) { continue; }
        $plan[(int)$e->id] = ['id' => (int)$e->id, 'coll' => $collSlug[$cid], 'title' => $e->title,
                              'set' => $set, 'show' => $p['show'] ?? []];
    }

    $byColl = [];
    foreach ($plan as $p) { $byColl[$p['coll']] = ($byColl[$p['coll']] ?? 0) + 1; }
    $byField = [];
    foreach ($plan as $p) { foreach (array_keys($p['set']) as $h) { $byField[$h] = ($byField[$h] ?? 0) + 1; } }
    echo 'pieces to change: ' . count($plan) . PHP_EOL;
    foreach ($byColl as $c => $n) { echo '   ' . str_pad($c, 38) . $n . PHP_EOL; }
    echo 'fields written: ' . ($byField ? implode(', ', array_map(fn($h, $n) => "$h $n", array_keys($byField), $byField)) : 'none') . PHP_EOL;
    echo 'left for a person: ' . count($listed) . PHP_EOL;

    /* ------------------------------------------------ before and after */
    $report = new \modules\quality\QualityReport();
    $before = $report->build();
    $over = [];
    foreach ($plan as $id => $p) { $over[$id] = $p['set']; }
    $after = $report->build($over);

    $print = function (array $a, array $b, string $headB) use ($classes): void {
        $names = $a['classes'];
        echo PHP_EOL . str_pad('class', 26) . str_pad('before', 9) . str_pad($headB, 9) . 'change' . PHP_EOL;
        foreach ($names as $k => $c) {
            $d = $b['totals'][$k] - $a['totals'][$k];
            printf("%s%-24s %-8d %-8d %s\n", in_array($k, $classes, true) ? '> ' : '  ', $c[0],
                $a['totals'][$k], $b['totals'][$k], $d === 0 ? '' : sprintf('%+d', $d));
        }
        echo PHP_EOL . 'by collection, for the classes this pass targets:' . PHP_EOL;
        $bRows = [];
        foreach ($b['rows'] as $r) { $bRows[$r['slug']] = $r; }
        foreach ($a['rows'] as $r) {
            if (!$r['pieces']) { continue; }
            $cells = [];
            foreach ($classes as $k) { $cells[] = $k . ' ' . $r['counts'][$k] . ' -> ' . $bRows[$r['slug']]['counts'][$k]; }
            echo '   ' . str_pad($r['slug'], 38) . implode('   ', $cells) . PHP_EOL;
        }
    };
    $print($before, $after, $APPLY ? 'predict' : 'after');

    /* ------------------------------------------------------ the report */
    $slug = preg_replace('~^quality_|\.php$~', '', $script);
    $md = ['# Quality pass: ' . $label, '',
           'Generated by scripts/import/' . $script . ' on ' . date('Y-m-d H:i') . '. '
           . ($APPLY ? 'Mode: APPLIED.' : 'Mode: DRY RUN, nothing written.'), '',
           '| class | before | after |', '|---|---:|---:|'];
    foreach ($before['classes'] as $k => $c) {
        $md[] = '| ' . (in_array($k, $classes, true) ? '**' . $c[0] . '**' : $c[0]) . ' | '
              . $before['totals'][$k] . ' | ' . $after['totals'][$k] . ' |';
    }
    $md[] = ''; $md[] = '## Changes (' . count($plan) . ')'; $md[] = '';
    foreach ($plan as $p) {
        $md[] = '### #' . $p['id'] . ' ' . $p['title'] . ' (' . $p['coll'] . ')';
        $md[] = '';
        $md[] = '```';
        foreach ($p['show'] as $l) { $md[] = $l; }
        $md[] = '```';
        $md[] = '';
    }
    $md[] = '## Left for a person (' . count($listed) . ')'; $md[] = '';
    foreach ($listed as $x) { $md[] = '- #' . $x['id'] . ' ' . $x['title'] . ' (' . $x['coll'] . '): ' . $x['why']; }
    $path = \Craft::getAlias('@webroot') . '/review/quality-' . $slug . '.md';
    @mkdir(dirname($path), 0775, true);
    file_put_contents($path, implode("\n", $md) . "\n");

    echo PHP_EOL . 'sample of the changes:' . PHP_EOL;
    foreach (array_slice($plan, 0, 12) as $p) {
        echo '  #' . $p['id'] . ' ' . mb_substr($p['title'], 0, 60) . ' (' . $p['coll'] . ')' . PHP_EOL;
        foreach (array_slice($p['show'], 0, 4) as $l) { echo '      ' . mb_substr($l, 0, 110) . PHP_EOL; }
    }
    if ($listed) {
        echo PHP_EOL . 'left for a person:' . PHP_EOL;
        foreach (array_slice($listed, 0, 25) as $x) { echo '  #' . $x['id'] . ' (' . $x['coll'] . ') ' . mb_substr($x['why'], 0, 100) . PHP_EOL; }
        if (count($listed) > 25) { echo '  ... and ' . (count($listed) - 25) . ' more in the report' . PHP_EOL; }
    }
    echo PHP_EOL . 'full plan: web/review/quality-' . $slug . '.md' . PHP_EOL;

    if (!$APPLY) {
        echo PHP_EOL . str_repeat('=', 78) . PHP_EOL . 'nothing was written. Pass $QUALITY_APPLY = true to apply.' . PHP_EOL;
        return;
    }

    /* ------------------------------------------------------------ write */
    $elements = Craft::$app->getElements();
    $saved = 0; $failed = [];
    foreach ($plan as $p) {
        $e = Entry::find()->id($p['id'])->status(null)->one();
        foreach ($p['set'] as $h => $v) { $e->setFieldValue($h, $v); }
        if ($elements->saveElement($e)) { $saved++; }
        else { $failed[] = '#' . $p['id'] . ': ' . json_encode($e->getFirstErrors()); }
    }
    echo PHP_EOL . 'saved ' . $saved . ' of ' . count($plan) . PHP_EOL;
    foreach ($failed as $f) { echo 'FAILED ' . $f . PHP_EOL; }

    /* ------------------------------------------------------- read back */
    $ok = 0; $short = [];
    foreach ($plan as $p) {
        $f = Entry::find()->id($p['id'])->status(null)->one();
        $bad = [];
        foreach ($p['set'] as $h => $v) {
            $got = $f->getFieldValue($h);
            if (is_object($got) && method_exists($got, 'ids')) {
                if (array_map('intval', $got->status(null)->ids()) != array_map('intval', (array)$v)) { $bad[] = $h; }
            } elseif (is_array($v)) {
                $got = array_values((array)$got);
                if (count($got) !== count($v)) { $bad[] = $h . ' rows ' . count($got) . '/' . count($v); continue; }
                foreach (array_values($v) as $i => $row) {
                    foreach ($row as $col => $cell) {
                        $handle = ['col1' => 'number', 'col2' => 'note', 'col3' => 'source'][$col] ?? $col;
                        if ((string)($got[$i][$handle] ?? $got[$i][$col] ?? '') !== (string)$cell) { $bad[] = "$h row $i $handle"; break 2; }
                    }
                }
            } elseif ((string)$got !== (string)$v) {
                $bad[] = $h;
            }
        }
        if ($bad) { $short[] = '#' . $p['id'] . ' ' . implode(', ', $bad); } else { $ok++; }
    }
    $readback = ($short ? 'SHORT ' : 'verified ') . $ok . ' of ' . count($plan);
    echo 'READ-BACK ' . $readback . PHP_EOL;

    $actual = $report->build();
    $mismatch = [];
    foreach ($after['totals'] as $k => $n) { if ($actual['totals'][$k] !== $n) { $mismatch[] = "$k predicted $n got " . $actual['totals'][$k]; } }
    $print($before, $actual, 'actual');
    echo PHP_EOL . ($mismatch ? 'REPORT DIFFERS FROM THE DRY RUN: ' . implode('; ', $mismatch) : 'report matches the dry run') . PHP_EOL;

    $applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
    $moved = [];
    foreach ($classes as $k) { $moved[] = $k . ' ' . $before['totals'][$k] . '->' . $actual['totals'][$k]; }
    $applyLog($script, $saved, $readback . ($mismatch ? '; REPORT MISMATCH' : '; report as predicted'),
        implode(', ', $moved) . '; ' . count($listed) . ' left for a person');

    if ($short || $failed || $mismatch) {
        foreach ($short as $m) { echo '  ' . $m . PHP_EOL; }
        throw new \RuntimeException($script . ': the write did not land as planned. Do not run the next pass until this is understood.');
    }
};
