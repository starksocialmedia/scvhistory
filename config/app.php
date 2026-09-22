<?php
/**
 * Yii Application Config
 *
 * Edit this file at your own risk!
 *
 * The array returned by this file will get merged with
 * vendor/craftcms/cms/src/config/app.php and app.[web|console].php, when
 * Craft's bootstrap script is defining the configuration for the entire
 * application.
 *
 * You can define custom modules and system components, and even override the
 * built-in system components.
 *
 * If you want to modify the application config for *only* web requests or
 * *only* console requests, create an app.web.php or app.console.php file in
 * your config/ folder, alongside this one.
 *
 * Read more about application configuration:
 * @link https://craftcms.com/docs/5.x/reference/config/app.html
 */

use craft\helpers\App;

return [
    'id' => App::env('CRAFT_APP_ID') ?: 'CraftCMS',

    /* The review screens need somewhere to save a decision the moment it is
       made. They are static HTML with no server side, so every decision lived
       in localStorage keyed by the queue key, and both halves of that lost
       work: the key moves when the queue is regenerated, and localStorage is
       per browser and can be cleared by anything. */
    'modules' => [
        'reviewstore' => \modules\reviewstore\ReviewStore::class,
        /* Refuses console writes to entries in a collection whose
           collectionFrozen is on, so a re-run import can't overwrite the
           hand edits made after the freeze. */
        'collectionfreeze' => \modules\collectionfreeze\CollectionFreeze::class,
        /* craft.quality, for /admin-quality. */
        'quality' => \modules\quality\Quality::class,
    ],
    'bootstrap' => ['reviewstore', 'collectionfreeze', 'quality'],
];
