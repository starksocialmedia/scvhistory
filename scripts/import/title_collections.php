$APPLY = false;
if ($APPLY) { echo 'APPLY IS ON, this will write to the database' . PHP_EOL; }
$el = Craft::$app->getElements();
$map = [
  'otn-rioux'   => ["Richard 'Doc' Rioux At Large", 'Weekly column in The Signal, 1993 to 1997, by Richard H. Rioux, Ph.D.', '/oldtownnewhall/rioux/'],
  'otn-whyte'   => ["Black 'N' Whyte", 'Column by Tim Whyte, 1997.', '/oldtownnewhall/whyte/'],
  'otn-patti'   => ["Open Book", "Santa Clarita Valley school issues, by Patti Rasmussen, 1997.", '/oldtownnewhall/patti/'],
  'otn-pauline' => ['Pauline Harte', 'Column by Pauline Harte, 1997.', '/oldtownnewhall/pauline/'],
  'otn-gazette' => ['Old Town Newhall Gazette', 'Multi-author periodical, 2005 to 2008.', '/oldtownnewhall/oldtownnews.htm'],
  'boston'      => ['Santa Clarita Valley History by John Boston', 'Columns by John Boston, 2000 to 2003.', '/scvhistory/signal/boston/jbindex.htm'],
  'manzer'      => ['Now and Then in the Santa Clarita Valley', 'Columns by Darryl Manzer, 2006. The later run of this column was published at scvnews.com and is not part of this archive.', '/scvhistory/signal/manzer/index.htm'],
  'worden'      => ['Selections from Leon Worden', 'Columns and features by Leon Worden, 1995 to 2009.', '/scvhistory/signal/worden/index.htm'],
  'coins'       => ['Making Cents', 'Weekly coin columns by Dr. Sol Taylor, 2005 to 2010.', '/scvhistory/signal/coins/index.htm'],
  'iraq'        => ['Abu Ghraib Prison Abuse Scandal Hits the Santa Clarita Valley', 'Signal coverage and wire reports, 2004 to 2005. A topic dossier rather than an authored series.', '/scvhistory/signal/iraq/index.htm'],
];
foreach ($map as $slug => [$title, $body, $url]) {
    $c = \craft\elements\Entry::find()->section('collections')->slug($slug)->status(null)->one();
    if (!$c) { echo str_pad($slug, 16) . 'NOT FOUND' . PHP_EOL; continue; }
    echo str_pad($slug, 16) . 'would set: ' . $title . PHP_EOL;
    if (!$APPLY) { continue; }
    $c->title = $title;
    if (trim((string)$c->getFieldValue('body')) === '') { $c->setFieldValue('body', $body); }
    if (trim((string)$c->getFieldValue('legacyUrl')) === '') { $c->setFieldValue('legacyUrl', $url); }
    echo '  ' . ($el->saveElement($c) ? 'saved' : 'FAILED') . PHP_EOL;
}
echo ($APPLY ? 'APPLIED' : 'DRY RUN') . PHP_EOL;
