<?php
/**
 * A very small module whose only job is to let the review screens save a
 * decision the moment it is made.
 *
 * The screens are static HTML under <project>/review, outside the web root. They had no server side at all,
 * so every decision lived in localStorage, keyed by the queue key. Both halves
 * of that were wrong: the queue key changes whenever the queue is regenerated,
 * and localStorage is per browser and can be cleared by anything, including by
 * an assistant testing the page. Decisions were lost.
 *
 * This gives them somewhere to write. It is deliberately tiny: one controller,
 * two actions, one JSON file. No database table, because the decisions file is
 * the thing that gets reviewed, edited by hand and fed to the importer, and
 * putting it in the database would hide it.
 */

namespace modules\reviewstore;

use Craft;
use yii\base\Module;

class ReviewStore extends Module
{
    public function init(): void
    {
        Craft::setAlias('@modules/reviewstore', __DIR__);
        $this->controllerNamespace = 'modules\\reviewstore\\controllers';
        parent::init();
    }
}
