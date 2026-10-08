// Read-only dump for checksum_audit_2026_10_08.py: every asset's volume path, legacySourcePath, sourceChecksum, sourceUrl
// and provenanceKind.
$out=[];
foreach (craft\elements\Asset::find()->batch(1000) as $b) foreach ($b as $a) {
  $l=$a->getFieldLayout(); $g=fn($h)=>$l && $l->getFieldByHandle($h) ? trim((string)$a->getFieldValue($h)) : '';
  $fs=$a->getVolume()->getFs(); $root=method_exists($fs,'getRootPath') ? $fs->getRootPath() : '';
  $out[]=['id'=>$a->id,'path'=>rtrim($root,'/').'/'.$a->folderPath.$a->filename,'lsp'=>$g('legacySourcePath'),'sum'=>$g('sourceChecksum'),'url'=>$g('sourceUrl'),'kind'=>$g('provenanceKind'),'used'=>craft\elements\Entry::find()->relatedTo(['targetElement'=>$a])->status(null)->exists()];
}
file_put_contents('/var/www/html/storage/runtime/photo-import/checksum-dump.json',json_encode($out));
echo count($out)," assets, ",count(array_filter($out,fn($x)=>$x['sum']!=='')), " record a checksum\n";
