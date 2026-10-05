foreach ([4473, 3153, 2540, 21594] as $id) { $e = \craft\elements\Entry::find()->id($id)->status(null)->one();
  echo "== #$id {$e->title} [{$e->section->handle}]\n";
  foreach ($e->getFieldLayout()->getCustomFields() as $f) { if (in_array($f->handle, ['body','legacyKey','orgAliases','recordProvenance','foundedText'])) { $v = $e->getFieldValue($f->handle); echo $f->handle, ': ', mb_substr(strip_tags((string)$v), 0, 1500), "\n"; } }
}
