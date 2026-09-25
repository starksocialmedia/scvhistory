<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * Custom values (legacyHost and anything else the site needs) live in
 * config/custom.php, because GeneralConfig has no slot for them. Read them as
 * craft.app.config.custom.legacyHost.
 *
 * @see \craft\config\GeneralConfig
 * @link https://craftcms.com/docs/5.x/reference/config/general.html
 */

use craft\config\GeneralConfig;
use craft\helpers\App;

return GeneralConfig::create()
    // Set the default week start day for date pickers (0 = Sunday, 1 = Monday, etc.)
    ->defaultWeekStartDay(1)
    // Prevent generated URLs from including "index.php"
    ->omitScriptNameInUrls()
    // Preload Single entries as Twig variables
    ->preloadSingles()
    // Prevent user enumeration attacks
    ->preventUserEnumeration()
    // Enable the Twig sandbox for system messages, etc.
    ->enableTwigSandbox()
    // Set the @webroot alias so the clear-caches command knows where to find CP resources
    ->aliases([
        '@webroot' => dirname(__DIR__) . '/web',
        /* The review screens and the queues they read are working material:
           reconciliation queues, the ledger index, 8,359 unmatched names in
           photo-links.json, and in the fidelity files the full text of the
           archive. They sit outside the web root so that no host can serve
           them, because on Cloudways nothing in web/.htaccess is read and the
           panel on this application offers no Nginx settings to block a path.
           Local browsing at /review/ is a DDEV-only alias: .ddev/nginx/review.conf. */
        '@review' => dirname(__DIR__) . '/review',
    ])
;
