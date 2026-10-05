<?php
/**
 * Sending a photograph to the archive (inventory/review/photo-submission-proposal-2026-10-04.md, approved by Nathan
 * on 4 October 2026 as proposed: the site's own controller, no third party, no plugin; a hidden field, a timing check
 * and a rate limit against bots, no Turnstile).
 *
 * A reader posts the form on /send. Each submission becomes a disabled entry in the submissions section, which has no
 * URLs, and its files are kept under storage/submissions/, outside the web root, with no address until the archivist
 * accepts one on /admin-submissions. Nothing is published, attached or emailed by the form itself.
 */

namespace modules\submissions;

use Craft;
use yii\base\Module;

class Submissions extends Module
{
    public function init(): void
    {
        Craft::setAlias('@modules/submissions', __DIR__);
        $this->controllerNamespace = 'modules\\submissions\\controllers';
        parent::init();
    }
}
