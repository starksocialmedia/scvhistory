<?php
/**
 * Exposes the collection quality report to templates as craft.quality, for
 * /admin-quality. The report itself is QualityReport, which the terminal
 * script reads too.
 */

namespace modules\quality;

use Craft;
use craft\web\twig\variables\CraftVariable;
use yii\base\Event;
use yii\base\Module;

class Quality extends Module
{
    public function init(): void
    {
        parent::init();
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $e) {
            $e->sender->set('quality', QualityVariable::class);
        });
    }
}
