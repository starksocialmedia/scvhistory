/**
 * Aliases the legacy site uses, so a search by name finds what Leon Worden
 * wrote (Nathan, 3 October 2026: "add the aliases first, then rerun. The
 * Signal finding nothing because Leon calls it 'The Signal' means the survey
 * is undercounting").
 *
 * Each alias below was counted in the Reggie mirror's text before it was
 * listed (pages containing it, in brackets); none under three pages is added,
 * and ambiguous forms are left out ("Hart House"; "Thomas Frew", who is the
 * blacksmith of the 1920s and not the Historical Society president of this
 * record). Appended to placeAliases, orgAliases or personAliases, one per line;
 * an alias already there is not repeated.
 * Idempotent. Dry run by default. Set $APPLY = true to write.
 * Run: ddev craft exec "eval(file_get_contents('scripts/import/add_aliases_2026_10_03.php'))"
 */

use craft\elements\Entry;

$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
echo ($APPLY ? 'APPLYING' : 'DRY RUN') . PHP_EOL . str_repeat('=', 78) . PHP_EOL;
$A = [
    ['organizations', 'The Santa Clarita Valley Signal', ['The Signal' => 1095, 'Newhall Signal' => 397, 'The Newhall Signal' => 363, 'The Newhall Signal and Saugus Enterprise' => 153]],
    ['organizations', 'Rancho El Tejon', ['El Tejon' => 67, 'Rancho El Tejón' => 36, 'Rancho Tejon' => 4]],
    ['organizations', 'Newhall Land and Farming Company', ['Newhall Land' => 603, 'Newhall Land and Farming Co.' => 332]],
    ['organizations', 'Standard Oil Company', ['Standard Oil Co.' => 271, 'Standard Oil of California' => 19]],
    ['organizations', 'Hart High School', ['Hart High' => 589, 'William S. Hart High School' => 74]],
    ['organizations', 'Valencia High School', ['Valencia High' => 56]],
    ['organizations', 'Saugus High School', ['Saugus High' => 98]],
    ['organizations', 'Canyon High School', ['Canyon High' => 102]],
    ['organizations', 'Union Oil Company', ['Union Oil' => 78, 'Union Oil Co.' => 22]],
    ['organizations', 'Heritage Auction Galleries', ['Heritage Auctions' => 3]],
    ['organizations', 'Santa Clarita Valley Historical Society', ['SCV Historical Society' => 626, 'SCVHS' => 116]],
    ['organizations', 'Mission San Gabriel Arcángel', ['Mission San Gabriel' => 71]],
    ['organizations', 'William S. Hart Union High School District', ['Hart Union High School District' => 111, 'Hart District' => 97, 'Hart School District' => 26]],
    ['organizations', 'Philadelphia and California Petroleum Company', ['Philadelphia & California Petroleum' => 7, 'Philadelphia and California Petroleum Co.' => 3]],
    ['organizations', 'Downtown Newhall Merchants Association', ['Newhall Merchants Association' => 17]],
    ['places', 'William S. Hart Park', ['Hart Park' => 591]],
    ['places', 'Hart Mansion', ['La Loma de los Vientos' => 96]],
    ['places', 'Elizabeth Lake', ['Lake Elizabeth' => 196]],
    ['places', 'Downtown Newhall', ['Old Town Newhall' => 523]],
    ['places', 'Pioneer Oil Refinery', ['Pioneer Refinery' => 46]],
    ['places', 'Placerita Canyon Nature Center', ['Placerita Nature Center' => 45]],
    ['places', 'Lyons Avenue', ['Lyons Ave.' => 37]],
    ['places', 'San Fernando Road', ['San Fernando Rd.' => 7]],
    ['places', 'Saugus Cafe', ['Saugus Café' => 173]],
    ['persons', 'Connie Worden', ['Connie Worden-Roberts' => 98]],
    ['persons', 'Edward Fitzgerald Beale', ['Ned Beale' => 28]],
    ['persons', 'Doña Jacoba', ['Jacoba Feliz' => 12]],
    ['persons', 'Father Francisco Garcés', ['Francisco Garces' => 26, 'Father Garces' => 3]],
    ['persons', 'Henry Mayo Newhall', ['Henry M. Newhall' => 65, 'H.M. Newhall' => 44]],
    ['persons', 'Junípero Serra', ['Father Junipero Serra' => 18]],
    ['persons', 'Dan Hon', ['Daniel Hon' => 4]],
    ['persons', 'Ruth Newhall', ['Ruth Waldo Newhall' => 93]],
    ['persons', 'Harry Carey', ['Harry Carey Sr.' => 118]],
    ['persons', 'George Caravalho', ['George A. Caravalho' => 9]],
    ['persons', 'John Timothy Gifford', ['John T. Gifford' => 13, 'John Gifford' => 14]],
];
$FIELD = ['organizations' => 'orgAliases', 'places' => 'placeAliases', 'persons' => 'personAliases'];
$bad = []; $plan = [];
foreach ($A as [$sec, $title, $als]) {
    $e = Entry::find()->section($sec)->status(null)->title($title)->one();
    if (!$e) { $bad[] = "no $sec record \"$title\""; continue; }
    $f = $FIELD[$sec]; $cur = trim((string)$e->getFieldValue($f));
    $have = array_map(fn($x) => mb_strtolower(trim($x)), preg_split('~[;\n]~', $cur));
    $add = array_values(array_filter(array_keys($als), fn($a) => !in_array(mb_strtolower($a), $have, true) && mb_strtolower($a) !== mb_strtolower($title)));
    if (!$add) { echo "#{$e->id} $title: nothing to add\n"; continue; }
    $plan[$e->id] = [$f, trim($cur . "\n" . implode("\n", $add))];
    echo "#{$e->id} $title ($f): + " . implode('; ', $add) . PHP_EOL;
}
echo count($plan) . ' records' . PHP_EOL . 'REFUSED: ' . ($bad ? implode(' | ', $bad) : 'none') . PHP_EOL;
if (!$APPLY) { echo str_repeat('=', 78) . PHP_EOL . 'nothing was written. Set $APPLY = true to apply.' . PHP_EOL; return; }
if ($bad) { echo 'REFUSING' . PHP_EOL; return; }
$short = [];
foreach ($plan as $id => [$f, $v]) {
    $e = Entry::find()->id($id)->status(null)->one(); $e->setFieldValue($f, $v);
    if (!Craft::$app->getElements()->saveElement($e) || trim((string)Entry::find()->id($id)->status(null)->one()->getFieldValue($f)) !== $v) { $short[] = "#$id"; }
}
echo 'READ-BACK ' . ($short ? 'SHORT: ' . implode(', ', $short) : 'OK: ' . count($plan) . ' records') . PHP_EOL;
$applyLog = require \Craft::getAlias('@root') . '/scripts/import/_apply_log.php';
$applyLog('add_aliases_2026_10_03.php', count($plan), $short ? 'SHORT' : 'verified', 'aliases the legacy site uses (The Signal, Newhall Land, Hart Park, Ned Beale ...), each counted in the mirror');
if ($short) { throw new \RuntimeException('add_aliases: ' . implode(', ', $short)); }
