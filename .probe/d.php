foreach (['1-early-inhabitants','bowers-cave'] as $s) {
  $e = \craft\elements\Entry::find()->section('articles')->slug($s)->status(null)->one();
  if (!$e) { echo "$s NOT FOUND\n"; continue; }
  file_put_contents(".probe/$s.txt", (string)$e->body);
  echo "$s: ".mb_strlen((string)$e->body)." chars, pubDate=".var_export((string)$e->originalPublishDate,true).", writtenBy=".$e->writtenBy->count()."\n";
}
