<?php
/**
 * The per-collection quality report: for each collection, how many of its
 * pieces carry each known fault class, worst collection first.
 *
 * One class, two readers. /admin-quality renders it and
 * scripts/import/report_collection_quality.php prints it, so the page and the
 * terminal can't disagree about what a fault is.
 *
 * Read only. Nothing here writes.
 *
 * A piece is an entry in the collection by either list the guard in
 * modules/collectionfreeze reads: partOfCollection on the piece, or
 * articlesInCollection on the collection. Counts are of pieces, not of
 * occurrences. A piece with forty nav lines is one piece with nav junk.
 *
 * Every pattern below was measured against the stored text on 22 September
 * before it went in, and the notes say what it found. The bodies are plain
 * text with a few markers of their own ([image:N], [table], [lines], [N]), not
 * HTML, so a class described in HTML terms is looked for in the form the
 * extraction actually left it in.
 */

namespace modules\quality;

use Craft;
use craft\db\Query;
use craft\elements\Asset;
use craft\elements\Entry;

class QualityReport
{
    /** Fault classes, in the order the report prints them. */
    public const CLASSES = [
        'layout'     => ['Layout residue', 'Tabs outside a [table] block: cells of a layout table flattened into the prose.'],
        'tags'       => ['Font/center tags', 'A raw or escaped HTML tag other than the inline ones prose.twig renders: font, center, div, span, table.'],
        'nav'        => ['Nav junk', 'A legacy navigation line: a "> A > B" breadcrumb, a "| Home | ... |" bar, "Click here", "Return to", "More ... News", "comments powered by Disqus".'],
        'imgBroken'  => ['Broken images', 'An [image:N] with no Nth image, or an attached image whose file is not on disk.'],
        'imgMissing' => ['Missing images', 'The legacy page had more content pictures than the record holds. Chrome (logos, buttons, images on five or more pages) is not counted.'],
        'undated'    => ['Undated', 'No publication date, printed or EDTF.'],
        'dateProse'  => ['Date in prose', 'A dateline left in the first or last three lines of the body: a short line that is a date, or a byline and a date.'],
        'footnotes'  => ['Unconverted footnotes', 'A notes heading still in the body, or [N] markers with no footnotes rows and no footnotesOn.'],
        'noLinks'    => ['No people or places', 'No subjectPerson and no depictsPlace.'],
        'noEra'      => ['No era', 'historicalEra is empty.'],
        'noThemes'   => ['No themes', 'articleThemes is empty.'],
    ];

    private const RELATION_FIELDS = ['subjectPerson', 'depictsPlace', 'historicalEra', 'articleThemes',
                                     'footnotesOn', 'recordImages', 'featuredImage'];

    /** @var array<string,bool> every crawled page key, for the coverage note */
    private array $_crawled = [];
    private int $_matched = 0;

    private const MONTH = '(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec)[a-z]*\.?';

    /**
     * @return array{built:string, classes:array, rows:array, totals:array, notes:array}
     */
    public function build(): array
    {
        $fields = Craft::$app->getFields();
        $partOf = $fields->getFieldByHandle('partOfCollection');
        $listed = $fields->getFieldByHandle('articlesInCollection');
        $notes = [];

        $collections = Entry::find()->section('collections')->status(null)->limit(null)->all();
        $collIds = array_map(fn($c) => (int)$c->id, $collections);

        /* Membership, from the relations table against canonical rows only. The
           table carries a row per revision too, and a revision's sourceId is its
           own element, so the joins on elements drop them, along with anything
           pointing at a deleted target. */
        $members = array_fill_keys($collIds, []);
        if ($partOf) {
            foreach ($this->_canonicalRelations(['r.fieldId' => $partOf->id, 'r.targetId' => $collIds]) as $r) {
                $members[(int)$r['targetId']][(int)$r['sourceId']] = true;
            }
        }
        if ($listed) {
            foreach ($this->_canonicalRelations(['r.fieldId' => $listed->id, 'r.sourceId' => $collIds]) as $r) {
                $members[(int)$r['sourceId']][(int)$r['targetId']] = true;
            }
        }
        $allIds = [];
        foreach ($members as $m) { $allIds += $m; }
        $allIds = array_keys($allIds);

        /* Every relation the classes read, for every piece, in one query. */
        $fieldIds = [];
        foreach (self::RELATION_FIELDS as $h) {
            if ($f = $fields->getFieldByHandle($h)) { $fieldIds[$f->id] = $h; }
        }
        $rel = [];
        if ($allIds && $fieldIds) {
            foreach ($this->_canonicalRelations(['r.fieldId' => array_keys($fieldIds), 'r.sourceId' => $allIds]) as $r) {
                $rel[(int)$r['sourceId']][$fieldIds[(int)$r['fieldId']]][] = (int)$r['targetId'];
            }
        }

        /* Image files. A missing file is a broken image whatever the record says. */
        $assetIds = [];
        foreach ($rel as $byField) {
            foreach (['recordImages', 'featuredImage'] as $h) { foreach ($byField[$h] ?? [] as $a) { $assetIds[$a] = true; } }
        }
        $fileMissing = [];
        $checkedFiles = 0;
        if ($assetIds) {
            foreach (Asset::find()->id(array_keys($assetIds))->limit(null)->all() as $a) {
                try {
                    $fs = $a->getVolume()->getFs();
                    if ($fs instanceof \craft\fs\Local) {
                        $checkedFiles++;
                        $path = rtrim(Craft::parseEnv($fs->path), '/') . '/' . $a->getPath();
                        if (!is_file($path)) { $fileMissing[(int)$a->id] = true; }
                    }
                } catch (\Throwable $t) {
                    $fileMissing[(int)$a->id] = true;
                }
            }
        }
        $notes[] = 'Image files checked on this server\'s disk: ' . $checkedFiles . '.';

        [$wanted, $invNote] = $this->_legacyImages();
        $notes[] = $invNote;

        /* The pieces themselves. */
        $pieces = [];
        if ($allIds) {
            foreach (Entry::find()->id($allIds)->status(null)->limit(null)->each() as $e) {
                $pieces[(int)$e->id] = $this->_faults($e, $rel[(int)$e->id] ?? [], $fileMissing, $wanted);
            }
        }

        if ($wanted) {
            $notes[] = 'Pieces matched to a crawled page by legacyKey or legacyUrl: ' . $this->_matched . ' of ' . count($pieces)
                     . '. An unmatched piece can\'t be counted as missing images, so this column is a floor.';
        }

        /* Roll up by collection. */
        $rows = [];
        $totals = ['pieces' => 0, 'any' => 0] + array_fill_keys(array_keys(self::CLASSES), 0);
        foreach ($collections as $c) {
            $ids = array_keys($members[(int)$c->id]);
            $row = [
                'id' => (int)$c->id, 'slug' => $c->slug, 'title' => $c->title,
                'cpUrl' => $c->getCpEditUrl(),
                'frozen' => (bool)($c->getFieldLayout()?->getFieldByHandle('collectionFrozen') ? $c->getFieldValue('collectionFrozen') : false),
                'pieces' => count($ids), 'any' => 0, 'score' => 0.0,
                'counts' => array_fill_keys(array_keys(self::CLASSES), 0),
                'examples' => array_fill_keys(array_keys(self::CLASSES), []),
            ];
            foreach ($ids as $id) {
                $f = $pieces[$id] ?? null;
                if ($f === null) { continue; }
                if ($f['faults']) { $row['any']++; }
                foreach ($f['faults'] as $k) {
                    $row['counts'][$k]++;
                    if (count($row['examples'][$k]) < 5) { $row['examples'][$k][] = ['id' => $id, 'title' => $f['title'], 'cpUrl' => $f['cpUrl']]; }
                }
            }
            /* Worst first means the largest share of pieces at fault, summed over
               the classes. A share, so a collection of twenty is not ranked
               below one of two hundred for being small. */
            if ($row['pieces']) {
                foreach ($row['counts'] as $n) { $row['score'] += $n / $row['pieces']; }
            }
            $rows[] = $row;
            $totals['pieces'] += $row['pieces'];
            $totals['any'] += $row['any'];
            foreach ($row['counts'] as $k => $n) { $totals[$k] += $n; }
        }
        usort($rows, fn($a, $b) => [$b['pieces'] > 0, $b['score'], $b['pieces']] <=> [$a['pieces'] > 0, $a['score'], $a['pieces']]);

        return [
            'built' => (new \DateTime())->format('Y-m-d H:i:s T'),
            'classes' => self::CLASSES,
            'rows' => $rows,
            'totals' => $totals,
            'notes' => $notes,
        ];
    }

    /** @return string[] the fault classes this piece carries */
    private function _faults(Entry $e, array $rel, array $fileMissing, array $wanted): array
    {
        $out = [];
        $has = fn(string $h) => (bool)$e->getFieldLayout()?->getFieldByHandle($h);
        $body = $has('body') ? (string)$e->getFieldValue('body') : '';
        $lines = array_values(array_filter(array_map('trim', explode("\n", $body)), 'strlen'));

        /* Layout residue. The three [table] blocks in the collections are real
           data tables (coin prices) and keep their tabs on purpose. */
        $outsideTables = preg_replace('~\[table\].*?\[/table\]~s', '', $body);
        if (str_contains($outsideTables, "\t")) { $out[] = 'layout'; }

        /* Tags. Measured at zero on 22 September: the extraction strips markup.
           Kept so that a reimport which lets it through shows up here. */
        if (preg_match('~</?(font|center|div|span|table|tr|td|th|tbody|p)\b[^>]*>|&lt;/?(font|center|table|td)\b~i', $body)) {
            $out[] = 'tags';
        }

        foreach ($lines as $l) {
            if (preg_match('~^>\s*\S~u', $l)
                || preg_match('~^\|.*\|.*\|~u', $l)
                || preg_match('~^[>\[\(]*\s*click here\b|^return to\b|^more .{0,30}\bnews\b|^go home to\b|^comments powered by disqus~iu', $l)) {
                $out[] = 'nav';
                break;
            }
        }

        $imgs = $rel['recordImages'] ?? [];
        $held = array_unique(array_merge($imgs, $rel['featuredImage'] ?? []));
        $broken = false;
        if (preg_match_all('~^\s*\[image:(\d+)\]\s*$~m', $body, $m)) {
            foreach ($m[1] as $n) { if ((int)$n < 1 || (int)$n > count($imgs)) { $broken = true; } }
        }
        foreach ($held as $a) { if (isset($fileMissing[$a])) { $broken = true; } }
        if ($broken) { $out[] = 'imgBroken'; }

        $key = $this->_pathKey((string)($has('legacyKey') ? $e->getFieldValue('legacyKey') : ''))
            ?: $this->_pathKey((string)($has('legacyUrl') ? $e->getFieldValue('legacyUrl') : ''));
        if ($key !== '' && isset($this->_crawled[$key])) { $this->_matched++; }
        if ($key !== '' && isset($wanted[$key]) && count($held) < $wanted[$key]) { $out[] = 'imgMissing'; }

        $date = '';
        foreach (['originalPublishDate', 'originalPublishDateEdtf', 'photoDate', 'photoDateEdtf'] as $h) {
            if ($has($h)) { $date .= trim((string)$e->getFieldValue($h)); }
        }
        if ($date === '') { $out[] = 'undated'; }

        $edge = array_merge(array_slice($lines, 0, 3), array_slice($lines, -3));
        foreach ($edge as $l) {
            if (mb_strlen($l) > 70) { continue; }
            $rest = preg_replace('~\(?\s*(?:(?:Mon|Tues|Wednes|Thurs|Fri|Satur|Sun)day,?\s+)?' . self::MONTH . '\s+\d{1,2},?\s+(?:18|19|20)\d\d\s*\)?\.?~u', '', $l, 1, $hit);
            if ($hit && str_word_count(preg_replace('~[^\pL\s]~u', ' ', $rest)) <= 4) { $out[] = 'dateProse'; break; }
        }

        $markers = preg_match('/(?<![A-Za-z0-9])\[\s*\d{1,3}\s*\](?!\d)/', $body);
        $notesHeading = preg_match('/^\s*(notes?|footnotes?|end\s?notes?|references?|sources?\s+and\s+notes)\s*:?\s*$/im', $body);
        $fnRows = $has('footnotes') ? (array)$e->getFieldValue('footnotes') : [];
        if ($notesHeading || ($markers && !$fnRows && empty($rel['footnotesOn']))) { $out[] = 'footnotes'; }

        if (empty($rel['subjectPerson']) && empty($rel['depictsPlace'])) { $out[] = 'noLinks'; }
        if (empty($rel['historicalEra'])) { $out[] = 'noEra'; }
        if (empty($rel['articleThemes'])) { $out[] = 'noThemes'; }

        return ['title' => $e->title, 'cpUrl' => $e->getCpEditUrl(), 'faults' => $out];
    }

    private function _canonicalRelations(array $where): array
    {
        return (new Query())
            ->select(['r.sourceId', 'r.targetId', 'r.fieldId'])
            ->from(['r' => '{{%relations}}'])
            ->innerJoin(['e' => '{{%elements}}'], '[[e.id]] = [[r.sourceId]]')
            ->innerJoin(['t' => '{{%elements}}'], '[[t.id]] = [[r.targetId]]')
            ->where($where)
            ->andWhere(['e.canonicalId' => null, 'e.draftId' => null, 'e.revisionId' => null, 'e.dateDeleted' => null])
            ->andWhere(['t.draftId' => null, 't.revisionId' => null, 't.dateDeleted' => null])
            ->all();
    }

    /**
     * Content pictures per legacy page, from the crawl inventories. Chrome is
     * anything named like a control, and anything that appears on five or more
     * pages: otnlogo.gif is on 35 Gazette pages and is not a picture of Newhall.
     *
     * @return array{0: array<string,int>, 1: string}
     */
    private function _legacyImages(): array
    {
        $dir = Craft::getAlias('@root') . '/inventory/legacy';
        $files = glob($dir . '/*.json') ?: [];
        $pages = [];
        foreach ($files as $f) {
            if (filesize($f) > 20 * 1024 * 1024) { continue; }
            $d = json_decode((string)file_get_contents($f), true);
            if (!is_array($d)) { continue; }
            $list = array_is_list($d) ? $d
                  : array_merge($d['pages'] ?? [], $d['series_pages'] ?? [], $d['related_pages'] ?? []);
            foreach ($list as $p) {
                if (!is_array($p) || !isset($p['images']) || !is_array($p['images'])) { continue; }
                $key = $this->_pathKey((string)($p['legacy_path'] ?? '')) ?: $this->_pathKey((string)($p['source_url'] ?? ''));
                if ($key === '') { continue; }
                $names = [];
                foreach ($p['images'] as $im) {
                    $src = is_array($im) ? (string)($im['src_raw'] ?? '') : (string)$im;
                    $n = strtolower(basename((string)(parse_url($src, PHP_URL_PATH) ?: '')));
                    if ($n !== '') { $names[$n] = true; }
                }
                $pages[$key] = array_keys($names);
                $this->_crawled[$key] = true;
            }
        }
        if (!$pages) { return [[], 'Missing images not counted: no crawl inventory under inventory/legacy on this server.']; }

        $freq = [];
        foreach ($pages as $names) { foreach ($names as $n) { $freq[$n] = ($freq[$n] ?? 0) + 1; } }
        $chrome = '~logo|clikhere|click|spacer|button|btn|arrow|bullet|banner|^small|^top|^home|^back|^next|^prev|^line|^dot~';
        $wanted = [];
        foreach ($pages as $key => $names) {
            $n = 0;
            foreach ($names as $name) { if ($freq[$name] < 5 && !preg_match($chrome, $name)) { $n++; } }
            if ($n) { $wanted[$key] = $n; }
        }
        return [$wanted, 'Missing images read from ' . count($pages) . ' crawled pages in inventory/legacy.'];
    }

    private function _pathKey(string $s): string
    {
        $s = trim($s);
        if ($s === '') { return ''; }
        $p = parse_url($s, PHP_URL_PATH);
        $p = strtolower(trim((string)($p ?: $s), '/'));
        return str_contains($p, '/') || str_contains($p, '.') ? $p : '';
    }
}
