/**
 * Making Cents: 259 weekly pieces by Dr. Sol Taylor, into the coins collection.
 *
 * A COIN ENTRY TYPE WAS PROPOSED AND IS NOT NEEDED
 *
 * The brief asked whether a coin needs its own type with identifier,
 * denomination, mint, date, series and obverse and reverse images. It does not,
 * because these are not coins. coins.json is a weekly newspaper column ABOUT
 * coins: 259 pieces, each with a byline and a date, titled things like
 * "Sleuthing at Garage and Estate Sales", "ANA Comes to L.A. In 2009" and
 * "Q. David Bowers, America's No. 1 Numismatist". Only 35 of the 259 titles
 * name a coin at all, and not one page carries an identifier, a denomination or
 * a pair of obverse and reverse images.
 *
 * A coin type would be right for a cabinet of coins. This is a column, so it
 * imports as articles and the collection's kind is column, not catalogue.
 *
 * Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/import_coins.php'))"
 */

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will create entries' . PHP_EOL; }

$SERIES = [
    'key'        => 'coins',
    'label'      => 'Making Cents, by Dr. Sol Taylor',
    'inventory'  => 'coins',
    'collection' => 'coins',
    'script'     => 'import_coins.php',
    'pathFilter' => '#^/scvhistory/signal/coins/#',
];

echo ($APPLY ? 'APPLYING' : 'DRY RUN') . '  ' . $SERIES['label'] . PHP_EOL;
require \Craft::getAlias('@root') . '/scripts/import/_series_import.php';
