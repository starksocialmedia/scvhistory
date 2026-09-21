<?php
/**
 * Site URL Rules
 *
 * You can define custom site URL rules here, which Craft will check in addition
 * to routes defined in Settings → Routes.
 *
 * Read about Craft’s routing behavior (and this file’s structure), here:
 * @link https://craftcms.com/docs/5.x/system/routing.html
 */

return [
    /* The image's own page. An asset used on six articles has no page of its
       own: it is a file behind a lightbox, and everything known about it lives
       on whichever record happens to use it. /media/<id> is where the picture
       is the subject rather than the illustration, and it is what the lightbox
       and the ImageObject both point at when no photograph record exists. */
    'media/<assetId:\\d+>' => ['template' => 'media/_entry'],
];
