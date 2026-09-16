<?php

use craft\elements\Entry;

$abel = Entry::find()->section('persons')->slug('abel-stearns')->status(null)->one();
if ($abel && trim((string) $abel->personGraveUrl) !== '') {
    $abel->setFieldValues(['personGraveUrl' => '']);
    if (!Craft::$app->getElements()->saveElement($abel)) {
        echo "STOP: could not clear Abel Stearns personGraveUrl\n";
        return;
    }
    echo "cleared Abel Stearns personGraveUrl\n";
}

$people = Entry::find()->section('persons')->status(null)->orderBy('title ASC')->all();

function cell($value): string
{
    $s = str_replace(["\r", "\n", "|"], [" ", " ", "\\|"], trim((string) $value));
    return $s;
}

$lines = [];
$lines[] = '# Person grave URL audit';
$lines[] = '';
$lines[] = 'Local DDEV export. Values are copied from Craft. Empty grave URL means none stored. No URLs were invented.';
$lines[] = '';
$lines[] = 'Count: ' . count($people);
$lines[] = '';
$lines[] = '| slug | title | fullName | birthDate | deathDate | burialPlace | personGraveUrl |';
$lines[] = '| --- | --- | --- | --- | --- | --- | --- |';

foreach ($people as $e) {
    $lines[] = '| ' . implode(' | ', [
        cell($e->slug),
        cell($e->title),
        cell($e->fullName),
        cell($e->birthDate),
        cell($e->deathDate),
        cell($e->burialPlace),
        cell($e->personGraveUrl),
    ]) . ' |';
}

$lines[] = '';
$path = Craft::getAlias('@root') . '/grave-audit-export.md';
file_put_contents($path, implode("\n", $lines) . "\n");
echo "wrote {$path} rows=" . count($people) . "\n";
