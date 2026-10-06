# Latent relation fixes, 5 October 2026

Source: `inventory/review/silent-faults-audit-2026-10-05.md`, finding 28. A Craft 5 relation query returns enabled targets only, so a script that reads a relation with `->ids()` or `->all()` and writes the result back unlinks every disabled target. Each read that feeds a write now carries `->status(null)`. Drafts and revisions stay excluded: `status(null)` leaves the query's `drafts` and `revisions` at false (checked live).

Each changed file has one comment line above its first change:

```php
/* status(null): keep unpublished targets when rewriting a relation (silent-faults audit, 5 October 2026). */
```

Line numbers are those of the file before the comment was inserted; in the file now, add one. Nothing else in any script changed. No script was run with `$APPLY = true`; every changed file passes `php -l`.

Live check (read-only): collection #871, History of the Santa Clarita Valley, holds 80 articles, 79 enabled. A rerun of any script that rewrote its `articlesInCollection` without `status(null)` would have dropped the disabled one.

## The 13 scripts in the audit

The audit lists 13 rows; the last row names two files, so 14 files. Lines the audit did not list but which feed the same write are marked.

### fold_and_retire_records.php

Line 54: finds which relations point at a record to retire; a disabled source holding it would be missed.

```php
- if ($f instanceof \craft\fields\BaseRelationField && in_array($id, $s->getFieldValue($f->handle)->ids(), true)) { $links[] = [$s, $f->handle]; }
+ if ($f instanceof \craft\fields\BaseRelationField && in_array($id, $s->getFieldValue($f->handle)->status(null)->ids(), true)) { $links[] = [$s, $f->handle]; }
```

Line 64: idempotency check for the write on line 71.

```php
- $needFremont = !in_array(307, $a857->subjectPerson->ids(), true);
+ $needFremont = !in_array(307, $a857->subjectPerson->status(null)->ids(), true);
```

Line 71.

```php
- if ($needFremont) { $a857->setFieldValue('subjectPerson', array_merge($a857->subjectPerson->ids(), [307])); if (!$els->saveElement($a857)) { $short[] = '#857'; } }
+ if ($needFremont) { $a857->setFieldValue('subjectPerson', array_merge($a857->subjectPerson->status(null)->ids(), [307])); if (!$els->saveElement($a857)) { $short[] = '#857'; } }
```

Line 75.

```php
- $s->setFieldValue($h, array_values(array_diff($s->getFieldValue($h)->ids(), [$id])));
+ $s->setFieldValue($h, array_values(array_diff($s->getFieldValue($h)->status(null)->ids(), [$id])));
```

### fix_country_fair_and_folder_attribution_2026_10_04.php

Line 51: dry-run report of the check on line 73.

```php
- echo 'Open Book description: ' . (str_contains((string)$OB->body, $obOld) ? '"Twenty-six" -> "Twenty-five"' : 'already') . '; Rasmussen profile: ' . (str_contains((string)$RA->body, $raOld) ? '"24" -> "25"' : 'already') . '; #12656 byline: ' . (in_array(2591, $C15->writtenBy->ids()) ? 'already' : 'Patti Rasmussen') . PHP_EOL;
+ echo 'Open Book description: ' . (str_contains((string)$OB->body, $obOld) ? '"Twenty-six" -> "Twenty-five"' : 'already') . '; Rasmussen profile: ' . (str_contains((string)$RA->body, $raOld) ? '"24" -> "25"' : 'already') . '; #12656 byline: ' . (in_array(2591, $C15->writtenBy->status(null)->ids()) ? 'already' : 'Patti Rasmussen') . PHP_EOL;
```

Line 65.

```php
- $v = ['partOfCollection' => array_values(array_diff($a->partOfCollection->ids(), [$o['col']]))];
+ $v = ['partOfCollection' => array_values(array_diff($a->partOfCollection->status(null)->ids(), [$o['col']]))];
```

Line 66.

```php
- if ($o['who'] && !in_array($o['who'], $a->writtenBy->ids())) { $v['writtenBy'] = array_values(array_unique(array_merge($a->writtenBy->ids(), [$o['who']]))); }
+ if ($o['who'] && !in_array($o['who'], $a->writtenBy->status(null)->ids())) { $v['writtenBy'] = array_values(array_unique(array_merge($a->writtenBy->status(null)->ids(), [$o['who']]))); }
```

Line 70.

```php
- foreach ($cols as $cid => $ids) { $c = $get($cid); $c->setFieldValue('articlesInCollection', array_values(array_diff($c->articlesInCollection->ids(), $ids))); if (!$el->saveElement($c)) { throw new \RuntimeException("collection #$cid: " . json_encode($c->getFirstErrors())); } $n++; }
+ foreach ($cols as $cid => $ids) { $c = $get($cid); $c->setFieldValue('articlesInCollection', array_values(array_diff($c->articlesInCollection->status(null)->ids(), $ids))); if (!$el->saveElement($c)) { throw new \RuntimeException("collection #$cid: " . json_encode($c->getFirstErrors())); } $n++; }
```

Line 73.

```php
- if (!in_array(2591, $C15->writtenBy->ids())) { $C15->setFieldValue('writtenBy', array_values(array_merge($C15->writtenBy->ids(), [2591]))); if (!$el->saveElement($C15)) { throw new \RuntimeException('#12656'); } $n++; }
+ if (!in_array(2591, $C15->writtenBy->status(null)->ids())) { $C15->setFieldValue('writtenBy', array_values(array_merge($C15->writtenBy->status(null)->ids(), [2591]))); if (!$el->saveElement($C15)) { throw new \RuntimeException('#12656'); } $n++; }
```

### apply_article_links.php

Line 66.

```php
- $current = $e->relatedArticles->ids();
+ $current = $e->relatedArticles->status(null)->ids();
```

### apply_place_links.php

Line 63.

```php
- $current = $e->relatedPlaces->ids();
+ $current = $e->relatedPlaces->status(null)->ids();
```

### _series_import.php

Line 330.

```php
- $made = 0; $failed = []; $order = $coll->articlesInCollection->ids();
+ $made = 0; $failed = []; $order = $coll->articlesInCollection->status(null)->ids();
```

### attach_perkins_collection.php

Line 112: decides whether an article is already in the collection before it is rewritten.

```php
- $cur = $e->partOfCollection->one();
+ $cur = $e->partOfCollection->status(null)->one();
```

Line 145.

```php
- $order = $coll->articlesInCollection->ids();
+ $order = $coll->articlesInCollection->status(null)->ids();
```

### restore_california_battalion.php

Line 95: printed as OLD before groupPersons is replaced; now shows a disabled member that the write would drop.

```php
- $memOld = $g->groupPersons->ids(); $MEM = [307, 315, 313];
+ $memOld = $g->groupPersons->status(null)->ids(); $MEM = [307, 315, 313];
```

Line 101: not in the audit: the current targets that linkPlan merges and writes.

```php
- $ids = $s->getFieldValue($h)->ids(); $has = in_array($ID, $ids, true);
+ $ids = $s->getFieldValue($h)->status(null)->ids(); $has = in_array($ID, $ids, true);
```

Line 139: read-back, listed by the audit.

```php
- $ok = $r && trim((string)$r->body) === $BODY && $r->groupPersons->ids() == $MEM
+ $ok = $r && trim((string)$r->body) === $BODY && $r->groupPersons->status(null)->ids() == $MEM
```

Line 140: read-back of the line 101 write, same statement as 139.

```php
- && !array_filter(array_keys($LINK), fn($sid) => !in_array($ID, $get($sid)->getFieldValue($LINK[$sid])->ids(), true))
+ && !array_filter(array_keys($LINK), fn($sid) => !in_array($ID, $get($sid)->getFieldValue($LINK[$sid])->status(null)->ids(), true))
```

### import_ruiz_census.php

Line 771: not in the audit: the "never overwrite" emptiness test; a field holding only disabled targets looked empty and was overwritten.

```php
- if ($cur instanceof \craft\elements\db\ElementQuery) { $cur = $cur->ids(); }
+ if ($cur instanceof \craft\elements\db\ElementQuery) { $cur = $cur->status(null)->ids(); }
```

Line 803.

```php
- $ids = $e->spouseOf->ids();
+ $ids = $e->spouseOf->status(null)->ids();
```

Line 816.

```php
- $ids = $e->childOf->ids();
+ $ids = $e->childOf->status(null)->ids();
```

### create_scv_water_event.php

Line 66: read-back, listed by the audit.

```php
- $ok = trim((string)$b->body) === trim($BODY) && $b->eventDateEdtf === '2018-01-01' && count($b->eventOrganizations->ids()) === 3 && $b->historicalEra->ids() === [$ERA];
+ $ok = trim((string)$b->body) === trim($BODY) && $b->eventDateEdtf === '2018-01-01' && count($b->eventOrganizations->status(null)->ids()) === 3 && $b->historicalEra->ids() === [$ERA];
```

### record_city_commissions_2026_10_04.php

Line 127.

```php
- if ($name === 'Patti Rasmussen' || $name === 'Jeri Seratti') { $v['personEvents'] = array_values(array_unique(array_merge($p->personEvents->ids(), [875]))); }
+ if ($name === 'Patti Rasmussen' || $name === 'Jeri Seratti') { $v['personEvents'] = array_values(array_unique(array_merge($p->personEvents->status(null)->ids(), [875]))); }
```

Line 135: not in the audit: emptiness test before candidacyPerson is set.

```php
- if ($name === 'Tim Burkhart' && !$BC->candidacyPerson->exists()) { $BC->setFieldValue('candidacyPerson', [$p->id]); if (!$el->saveElement($BC)) { throw new \RuntimeException('candidacy: ' . json_encode($BC->getFirstErrors())); } $n++; }
+ if ($name === 'Tim Burkhart' && !$BC->candidacyPerson->status(null)->exists()) { $BC->setFieldValue('candidacyPerson', [$p->id]); if (!$el->saveElement($BC)) { throw new \RuntimeException('candidacy: ' . json_encode($BC->getFirstErrors())); } $n++; }
```

### create_redevelopment_agency_2026_10_04.php

Line 54.

```php
- $ids = $w->subjectOrganization->ids(); if (!in_array($a->id, $ids)) { $w->setFieldValue('subjectOrganization', array_merge($ids, [$a->id])); if (!$el->saveElement($w)) { throw new \RuntimeException('#12302'); } $n++; }
+ $ids = $w->subjectOrganization->status(null)->ids(); if (!in_array($a->id, $ids)) { $w->setFieldValue('subjectOrganization', array_merge($ids, [$a->id])); if (!$el->saveElement($w)) { throw new \RuntimeException('#12302'); } $n++; }
```

### fix_gibbs_current_term.php

Line 44: copied into the new holding.

```php
- $vals = ['holdingPerson' => [$GIBBS], 'holdingOffice' => $old->holdingOffice->ids(), 'holdingBody' => $old->holdingBody->ids(), 'holdingDistrict' => $old->holdingDistrict->ids(),
+ $vals = ['holdingPerson' => [$GIBBS], 'holdingOffice' => $old->holdingOffice->status(null)->ids(), 'holdingBody' => $old->holdingBody->status(null)->ids(), 'holdingDistrict' => $old->holdingDistrict->status(null)->ids(),
```

### water_chain_and_edited_marks_2026_10_04.php

Line 76: not in the audit: recordImages read and merged (assets).

```php
- $o = Entry::find()->id($oid)->status(null)->one(); $ids = $o->recordImages->ids();
+ $o = Entry::find()->id($oid)->status(null)->one(); $ids = $o->recordImages->status(null)->ids();
```

Line 91.

```php
- $S = Entry::find()->id(402)->one(); $want = array_values(array_unique(array_merge($S->precededBy->ids(), [26563, 27534], $ids)));
+ $S = Entry::find()->id(402)->one(); $want = array_values(array_unique(array_merge($S->precededBy->status(null)->ids(), [26563, 27534], $ids)));
```

Line 92.

```php
- if ($S->precededBy->ids() != $want) { $S->setFieldValue('precededBy', $want); if (!$el->saveElement($S)) { throw new \RuntimeException('SCV Water'); } $n++; }
+ if ($S->precededBy->status(null)->ids() != $want) { $S->setFieldValue('precededBy', $want); if (!$el->saveElement($S)) { throw new \RuntimeException('SCV Water'); } $n++; }
```

### fix_sb634_two_bodies_2026_10_04.php

Line 63: comparison before a full replace; the replace itself is deliberate.

```php
- $pre = array_values(array_unique(array_merge([26563, 27534, $V->id]))); if ($S->precededBy->ids() != $pre) { $save($S, ['precededBy' => $pre]); }
+ $pre = array_values(array_unique(array_merge([26563, 27534, $V->id]))); if ($S->precededBy->status(null)->ids() != $pre) { $save($S, ['precededBy' => $pre]); }
```

## Found beyond the audit

From a sweep of `scripts/import/*.php` for a relation read without `status(null)` that feeds `setFieldValue` or `setFieldValues`. The audit's note "several profile builders do the same with personOrganizations and roles" is these.

### apply_hart_2022_area2_and_smyth.php

Line 46: idempotency check for line 69.

```php
- $smDone = $sm && in_array(21579, $sm->recordImages->ids());
+ $smDone = $sm && in_array(21579, $sm->recordImages->status(null)->ids());
```

Line 69.

```php
- if (!$smDone) { $sm->setFieldValue('recordImages', array_values(array_unique(array_merge($sm->recordImages->ids(), [21579])))); if (!$el->saveElement($sm)) { throw new \RuntimeException('Smyth: ' . json_encode($sm->getFirstErrors())); } }
+ if (!$smDone) { $sm->setFieldValue('recordImages', array_values(array_unique(array_merge($sm->recordImages->status(null)->ids(), [21579])))); if (!$el->saveElement($sm)) { throw new \RuntimeException('Smyth: ' . json_encode($sm->getFirstErrors())); } }
```

### apply_era_decisions.php

Line 31: emptiness test before an overwrite.

```php
- $cur = $p->historicalEra->ids();
+ $cur = $p->historicalEra->status(null)->ids();
```

### apply_multi_eras.php

Line 37.

```php
- $cur = $p->historicalEra->ids(); if (!$cur) { continue; }
+ $cur = $p->historicalEra->status(null)->ids(); if (!$cur) { continue; }
```

### assign_person_eras.php

Line 80: emptiness test before an overwrite (line 109).

```php
- $cur = $p->historicalEra->ids();
+ $cur = $p->historicalEra->status(null)->ids();
```

### attach_wiley_obituary_clippings.php

Line 25.

```php
- foreach ($WANT as $h => $ids) { $have = $e->getFieldValue($h)->ids(); $add = array_diff($ids, $have); if ($add) { $vals[$h] = array_values(array_merge($have, $add)); echo "   $h + " . implode(', ', array_map(fn($i) => "#$i {$NAMES[$i]}", $add)) . PHP_EOL; } }
+ foreach ($WANT as $h => $ids) { $have = $e->getFieldValue($h)->status(null)->ids(); $add = array_diff($ids, $have); if ($add) { $vals[$h] = array_values(array_merge($have, $add)); echo "   $h + " . implode(', ', array_map(fn($i) => "#$i {$NAMES[$i]}", $add)) . PHP_EOL; } }
```

### build_ahuja_profile.php

Line 113.

```php
- $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'Psychiatrist; school board member', 'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$role->id]))),
+ $vals = ['body' => $BODY, 'footnotes' => $fn($NOTES), 'bodyAuthorship' => 'editorial-2026', 'occupation' => 'Psychiatrist; school board member', 'roles' => array_values(array_unique(array_merge($p->roles->status(null)->ids(), [$role->id]))),
```

### build_city_of_santa_clarita.php

Line 88.

```php
- $have = array_map('intval', $c->neighborhood->ids());
+ $have = array_map('intval', $c->neighborhood->status(null)->ids());
```

Line 91: printed list of the communities kept.

```php
- if ($add) { $set['neighborhood'] = array_merge($have, array_map(fn($t) => $t->id, $add)); echo 'communities: keeps ' . implode(', ', array_map(fn($t) => $t->title, $c->neighborhood->all())) . '; adds ' . implode(', ', array_map(fn($t) => $t->title, $add)) . PHP_EOL; }
+ if ($add) { $set['neighborhood'] = array_merge($have, array_map(fn($t) => $t->id, $add)); echo 'communities: keeps ' . implode(', ', array_map(fn($t) => $t->title, $c->neighborhood->status(null)->all())) . '; adds ' . implode(', ', array_map(fn($t) => $t->title, $add)) . PHP_EOL; }
```

### build_council_profiles_batch1.php

Line 374.

```php
- if (!in_array($id, $ph->photoPeople->ids(), true)) { $vals['photoPeople'] = array_merge($ph->photoPeople->ids(), [$id]); }
+ if (!in_array($id, $ph->photoPeople->status(null)->ids(), true)) { $vals['photoPeople'] = array_merge($ph->photoPeople->status(null)->ids(), [$id]); }
```

### build_hart_profile.php

Line 156.

```php
- if (in_array('placePeople', $h) && in_array($pid, [$PARK, $MANSION])) { $vals['placePeople'] = array_values(array_unique(array_merge($e->placePeople->ids(), [$ID]))); }
+ if (in_array('placePeople', $h) && in_array($pid, [$PARK, $MANSION])) { $vals['placePeople'] = array_values(array_unique(array_merge($e->placePeople->status(null)->ids(), [$ID]))); }
```

### build_cooper_profile.php

Line 88.

```php
- 'personOrganizations' => array_values(array_unique(array_merge($s->personOrganizations->ids(), [$SCVW]))),
+ 'personOrganizations' => array_values(array_unique(array_merge($s->personOrganizations->status(null)->ids(), [$SCVW]))),
```

### build_lackey_profile_2026_10_04.php

Line 37.

```php
- 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [28271]))),
+ 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [28271]))),
```

### build_de_la_cerda_profile.php

Line 92.

```php
- 'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [$SUSD->id]))),
+ 'roles' => array_values(array_unique(array_merge($p->roles->status(null)->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [$SUSD->id]))),
```

### build_mckeon_profile.php

Line 119.

```php
- 'roles' => array_values(array_unique(array_merge($e->roles->ids(), [$CONGRESSMAN, $role->id]))),
+ 'roles' => array_values(array_unique(array_merge($e->roles->status(null)->ids(), [$CONGRESSMAN, $role->id]))),
```

### build_smith_profile.php

Line 125.

```php
- 'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$ROLE->id, $ASM->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [$NSD->id]))),
+ 'roles' => array_values(array_unique(array_merge($p->roles->status(null)->ids(), [$ROLE->id, $ASM->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [$NSD->id]))),
```

### build_schiavo_profile_2026_10_04.php

Line 42.

```php
- 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [28271]))),
+ 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [28271]))),
```

### build_trunkey_profile.php

Line 137.

```php
- 'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [$SUSD->id, $HART->id]))),
+ 'roles' => array_values(array_unique(array_merge($p->roles->status(null)->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [$SUSD->id, $HART->id]))),
```

### build_walters_profile.php

Line 106.

```php
- 'roles' => array_values(array_unique(array_merge($p->roles->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [$NSD->id]))),
+ 'roles' => array_values(array_unique(array_merge($p->roles->status(null)->ids(), [$ROLE->id]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [$NSD->id]))),
```

### build_valladares_profile_2026_10_04.php

Line 51.

```php
- 'roles' => array_values(array_unique(array_merge($p->roles->ids(), [18313, 18387]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->ids(), [28271, 28273]))),
+ 'roles' => array_values(array_unique(array_merge($p->roles->status(null)->ids(), [18313, 18387]))), 'personOrganizations' => array_values(array_unique(array_merge($p->personOrganizations->status(null)->ids(), [28271, 28273]))),
```

### build_worden_profile.php

Line 110.

```php
- if ($old && in_array('recordImages', $h)) { $vals['recordImages'] = array_values(array_unique(array_merge($s->recordImages->ids(), [$old->id]))); }
+ if ($old && in_array('recordImages', $h)) { $vals['recordImages'] = array_values(array_unique(array_merge($s->recordImages->status(null)->ids(), [$old->id]))); }
```

### import_edited_portraits.php

Line 106.

```php
- if ($cur && $cur->id !== $s['from'] && in_array('recordImages', $h)) { $p->setFieldValue('recordImages', array_values(array_unique(array_merge($p->recordImages->ids(), [$cur->id])))); }
+ if ($cur && $cur->id !== $s['from'] && in_array('recordImages', $h)) { $p->setFieldValue('recordImages', array_values(array_unique(array_merge($p->recordImages->status(null)->ids(), [$cur->id])))); }
```

### import_legacy_images.php

Line 710.

```php
- try { foreach ($entry->recordImages->all() as $a) { $existing[] = $a->id; } } catch (\Throwable $e) {}
+ try { foreach ($entry->recordImages->status(null)->all() as $a) { $existing[] = $a->id; } } catch (\Throwable $e) {}
```

### link_acosta_family.php

Line 14.

```php
- $ids = $child->getFieldValue('childOf')->ids();
+ $ids = $child->getFieldValue('childOf')->status(null)->ids();
```

### link_images_to_records.php

Line 206.

```php
- $current = $e->getFieldValue($field)->ids();
+ $current = $e->getFieldValue($field)->status(null)->ids();
```

### merge_rudy.php

Line 13.

```php
- $img = $drop->getFieldValue('featuredImage')->ids();
+ $img = $drop->getFieldValue('featuredImage')->status(null)->ids();
```

Line 14: emptiness test on the kept record before the moved value is written.

```php
- if (count($img) && !$keep->getFieldValue('featuredImage')->count()) { $moves['featuredImage'] = $img; }
+ if (count($img) && !$keep->getFieldValue('featuredImage')->status(null)->count()) { $moves['featuredImage'] = $img; }
```

### organize_orgs_places.php

Line 119.

```php
- $p->setFieldValue('neighborhood', array_values(array_unique(array_merge(array_map('intval', $p->neighborhood->ids()), [196]))));
+ $p->setFieldValue('neighborhood', array_values(array_unique(array_merge(array_map('intval', $p->neighborhood->status(null)->ids()), [196]))));
```

Line 125.

```php
- if ($img) { $com->setFieldValue('recordImages', array_values(array_unique(array_merge(array_map('intval', $com->recordImages->ids()), [$img->id])))); }
+ if ($img) { $com->setFieldValue('recordImages', array_values(array_unique(array_merge(array_map('intval', $com->recordImages->status(null)->ids()), [$img->id])))); }
```

### record_city_mayors_2026_10_04.php

Line 84: idempotency check for line 103.

```php
- $wHas = $W && $MV && in_array($MV->id, $W->neighborhood->ids());
+ $wHas = $W && $MV && in_array($MV->id, $W->neighborhood->status(null)->ids());
```

Line 103.

```php
- if (!$wHas) { $W->setFieldValue('neighborhood', array_values(array_unique(array_merge($W->neighborhood->ids(), [$MV->id])))); if (!$el->saveElement($W)) { throw new \RuntimeException('Weste: ' . json_encode($W->getFirstErrors())); } $n++; }
+ if (!$wHas) { $W->setFieldValue('neighborhood', array_values(array_unique(array_merge($W->neighborhood->status(null)->ids(), [$MV->id])))); if (!$el->saveElement($W)) { throw new \RuntimeException('Weste: ' . json_encode($W->getFirstErrors())); } $n++; }
```

### remove_john_wayne.php

Line 55: finds which relations point at the record (same shape as fold_and_retire line 54).

```php
- if (in_array($ID, $e->getFieldValue($f->handle)->ids(), true)) { $links[] = [$e, $f->handle]; }
+ if (in_array($ID, $e->getFieldValue($f->handle)->status(null)->ids(), true)) { $links[] = [$e, $f->handle]; }
```

Line 79.

```php
- $e->setFieldValue($h, array_values(array_diff($e->getFieldValue($h)->ids(), [$ID])));
+ $e->setFieldValue($h, array_values(array_diff($e->getFieldValue($h)->status(null)->ids(), [$ID])));
```

### repoint_to_larger.php

Line 80.

```php
- foreach ($e->recordImages->all() as $a) { $ids[] = $a->id === $x['small']->id ? $x['large']->id : $a->id; }
+ foreach ($e->recordImages->status(null)->all() as $a) { $ids[] = $a->id === $x['small']->id ? $x['large']->id : $a->id; }
```

### set_place_coords.php

Line 42.

```php
- $from = $dupe->getFieldValue($h)->ids();
+ $from = $dupe->getFieldValue($h)->status(null)->ids();
```

Line 43.

```php
- $to = $keep->getFieldValue($h)->ids();
+ $to = $keep->getFieldValue($h)->status(null)->ids();
```

### swap_fremont_vasquez_portraits.php

Line 72.

```php
- $p->setFieldValue('recordImages', array_values(array_unique(array_merge($p->recordImages->ids(), [$s['old']]))));
+ $p->setFieldValue('recordImages', array_values(array_unique(array_merge($p->recordImages->status(null)->ids(), [$s['old']]))));
```

### smyth_family_and_roles.php

Line 85: held childOf, merged and written at line 178.

```php
- $held = array_map(fn($e) => $e->id, $cam->childOf->all());
+ $held = array_map(fn($e) => $e->id, $cam->childOf->status(null)->all());
```

Line 137.

```php
- $has = array_map(fn($x) => $x->id, $e->roles->all());
+ $has = array_map(fn($x) => $x->id, $e->roles->status(null)->all());
```

### source_corrections.php

Line 42.

```php
- $ids = $a->getFieldValue('subjectPerson')->ids();
+ $ids = $a->getFieldValue('subjectPerson')->status(null)->ids();
```

### move_war_memorial_persons.php

Line 62.

```php
- foreach ($person->featuredImage->all() as $asset) {
+ foreach ($person->featuredImage->status(null)->all() as $asset) {
```

### create_aadusd_2026_10_04.php

Line 33.

```php
- foreach (Craft::$app->getCategories()->getGroupByHandle('neighborhood') ? craft\elements\Category::find()->group('neighborhood')->slug(['acton', 'agua-dulce'])->all() : [] as $c) { $ids = $e->neighborhood->ids(); if (!in_array($c->id, $ids)) { $e->setFieldValue('neighborhood', array_merge($ids, [$c->id])); if (!$el->saveElement($e)) { throw new \RuntimeException('communities'); } $n++; } }
+ foreach (Craft::$app->getCategories()->getGroupByHandle('neighborhood') ? craft\elements\Category::find()->group('neighborhood')->slug(['acton', 'agua-dulce'])->all() : [] as $c) { $ids = $e->neighborhood->status(null)->ids(); if (!in_array($c->id, $ids)) { $e->setFieldValue('neighborhood', array_merge($ids, [$c->id])); if (!$el->saveElement($e)) { throw new \RuntimeException('communities'); } $n++; } }
```

### fix_city_seats_and_body_headers_2026_10_04.php

Line 41.

```php
- $e = Entry::find()->id($o)->status(null)->one(); $ids = $e->recordImages->ids();
+ $e = Entry::find()->id($o)->status(null)->one(); $ids = $e->recordImages->status(null)->ids();
```

### water_marks_from_originals_2026_10_04.php

Line 40.

```php
- $o = Entry::find()->id($oid)->status(null)->one(); $ids = $o->recordImages->ids();
+ $o = Entry::find()->id($oid)->status(null)->one(); $ids = $o->recordImages->status(null)->ids();
```

### import_first_council_photos_2026_10_04.php

Line 51.

```php
- $City = Entry::find()->id(394)->one(); $cur = $City->recordImages->ids(); $want = array_values(array_unique(array_merge([$ids['sc8801'], $ids['bw8702']], $cur)));
+ $City = Entry::find()->id(394)->one(); $cur = $City->recordImages->status(null)->ids(); $want = array_values(array_unique(array_merge([$ids['sc8801'], $ids['bw8702']], $cur)));
```

### record_hart_superintendents_and_board_2026_10_04.php

Line 119: one() of a relation copied into a new holding.

```php
- $h->setFieldValues(['holdingPerson' => [$me->holdingPerson->one()->id], 'holdingOffice' => [$RS->id], 'holdingBody' => [21588], 'holdingDistrict' => [25323], 'seatLabel' => 'Trustee Area 5',
+ $h->setFieldValues(['holdingPerson' => [$me->holdingPerson->status(null)->one()->id], 'holdingOffice' => [$RS->id], 'holdingBody' => [21588], 'holdingDistrict' => [25323], 'seatLabel' => 'Trustee Area 5',
```

## Seen and left alone

These read a relation without `status(null)` but do not build a written relation value from it, so they cannot unlink anything:

- Read-backs after a write (for example `attach_perkins_collection.php` 172 and 177, `_series_import.php` 366, `link_images_to_records.php` 230, `apply_hart_2022_area2_and_smyth.php` 92, `build_hart_profile.php` 163, `build_pico_profile.php` 102). A disabled target makes them report SHORT, loudly; it loses nothing.
- Displays and dry-run reports (`build_hart_profile.php` 136, `build_pico_profile.php` 81, `build_trunkey_profile.php` 115, `swap_fremont_vasquez_portraits.php` 49, `detach_ai_collection_bands.php` 31, the `fix_delvalle_family.php` and `fix_notes_article.php` echoes).
- Checks before a write that replaces the whole value with a fixed one: `build_pico_profile.php` 96, `set_namesakes.php` 42, `saugus_2019_entities_2026_10_05.php` 65, `detach_ai_collection_bands.php` 38, `clear_memorial_non_portraits_2026_10_04.php` 36, the `currentMark` and `parentOrganization` checks in the marks and county scripts. The replace is the intended write.
- Emptiness checks before setting a single value (`featuredImage->exists()` in `import_city_hall_csun_photos.php` 93 and `apply_review_fixes_0929.php` 120, `candidacyPerson->one()` in `build_trunkey_profile.php` 141, `writtenBy->count()` in `set_collection_authors.php` 16). A disabled target would be replaced, not merged; left for Nathan to decide.
- Filters and lookups that do not write the relation (`council_ceda.php`, `import_ceda_school_boards.php` 150, `import_water_boards.php` 138, `import_reynolds.php` 196, `repoint_to_larger.php` 50, `set_wm_lead_images.php` 48, `import_first_council_photos_2026_10_04.php` 49), and read-only exports and previews.

## The check

`scripts/import/check_relation_status.php` greps `scripts/import/*.php` for a relation read without `status(null)` that feeds a write: inside a `setFieldValue(s)` call, inside a keyed `array_merge`/`array_diff`/`array_unique` near a write, or read into a variable (or one assigned from it) that a `setFieldValue(s)` call within forty lines passes. Runnable alone (`php scripts/import/check_relation_status.php`) or by eval from Craft, returning `['ok', 'fails']` like `check_handoff.php`. Not wired into `check_render.php`.

Against the scripts as committed before these fixes it flags 48 lines, every one of them among the fixes above; against the fixed tree it flags none. It is a heuristic and misses the longer chains this sweep found by reading: a value passed through two variables (`set_place_coords.php`, `merge_rudy.php`, `import_legacy_images.php`, `smyth_family_and_roles.php` 137, `apply_multi_eras.php`), a write far below the read (`smyth_family_and_roles.php` 85), a value placed in a multi-line `setFieldValues([...])` (`move_war_memorial_persons.php` 62), `one()` reads, and the idempotency and emptiness checks.

Totals: 30 lines in 14 audit files, 46 lines in 38 further files.
