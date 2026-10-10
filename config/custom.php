<?php
/**
 * Custom config settings.
 *
 * Craft 5 has no slot for arbitrary values on GeneralConfig, so custom settings
 * live here and are read as Craft::$app->config->custom->x in PHP and
 * craft.app.config.custom.x in Twig.
 *
 * @link https://craftcms.com/docs/5.x/configure.html#custom-settings
 */

use craft\helpers\App;

return [
    // Base URL of the legacy archive. Stored legacy URL fields are root-relative
    // paths; this is the host they resolve against. Everything goes through
    // templates/_partials/legacy-url.twig so the host changes in one place.
    'legacyHost' => rtrim(App::env('LEGACY_HOST') ?: 'https://scvhistory.com', '/'),
    // Assets the site does not serve at /media/<id> (Nathan, 9 October 2026): on no live record, and generated or
    // generatively edited by their own files or records. Built by scripts/import/build_withheld_media.php, which opens
    // the files; check_generated_files.php (run by check_render) fails when the list is out of date.
    'withheldMedia' => array_map(fn($a) => (int)$a['id'], (json_decode((string)@file_get_contents(__DIR__ . '/withheld-media.json'), true) ?: [])['assets'] ?? []),
];
