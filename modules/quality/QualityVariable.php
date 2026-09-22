<?php

namespace modules\quality;

use Craft;

class QualityVariable
{
    /**
     * The report, cached for ten minutes. It reads every piece in every
     * collection and checks every image file on disk, a few seconds of work,
     * and the page has no reason to repeat that on each load. Pass fresh to
     * rebuild now.
     */
    public function report(bool $fresh = false): array
    {
        $cache = Craft::$app->getCache();
        if ($fresh) { $cache->delete('quality-report'); }
        return $cache->getOrSet('quality-report', fn() => (new QualityReport())->build(), 600);
    }
}
