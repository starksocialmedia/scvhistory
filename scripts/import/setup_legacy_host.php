$path = Craft::getAlias('@root') . '/config/general.php';
echo 'Set LEGACY_HOST in .env, then reference it in templates as craft.app.config.custom.legacyHost' . PHP_EOL;
echo 'Current legacyUrl values, first 10:' . PHP_EOL;
$n = 0;
foreach (Craft::$app->getEntries()->getAllSections() as $s) {
    foreach (\craft\elements\Entry::find()->section($s->handle)->status(null)->all() as $e) {
        foreach (['legacyUrl','placeLegacyUrl','orgLegacyUrl','personLegacyUrl','obitLegacyUrl','groupLegacyUrl','eventLegacyUrl','mpLegacyUrl'] as $h) {
            $has = false;
            foreach ($e->getFieldLayout()->getCustomFields() as $f) { if ($f->handle === $h) { $has = true; break; } }
            if (!$has) { continue; }
            try { $v = trim((string)$e->getFieldValue($h)); } catch (\Throwable $x) { continue; }
            if ($v === '') { continue; }
            $shape = str_starts_with($v, 'http') ? 'ABSOLUTE' : (str_starts_with($v, '/') ? 'root-relative' : 'bare');
            if ($n < 10) { echo '  ' . str_pad($shape, 15) . $v . PHP_EOL; }
            $n++;
        }
    }
}
echo 'total legacy URLs stored: ' . $n . PHP_EOL;
