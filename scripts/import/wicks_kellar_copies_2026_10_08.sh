# Web copies of Randy Wicks's and Bob Kellar's legacy files on Reggie, for wicks_kellar_lopez_pattern_2026_10_08.php
# (8 October 2026). Runs in the container; reads /mnt/reggie, writes storage/runtime/photo-import/restore/. Each master is
# hashed and checked against the drive manifest before a copy is made; the import's rule: 2,400 pixels on the long side
# at most, quality 82, never enlarged.
set -e
OUT=/var/www/html/storage/runtime/photo-import/restore; mkdir -p $OUT
echo 'READ, before any number:'
echo '  the file itself: each master on Reggie, hashed here (sha256sum) and decoded for the copy'
echo '  a record about a file: the drive manifest checksums below, describing the masters; the files were read too'
while read f lsp sum; do
  src=/mnt/reggie/scvhistory.com/${lsp#/}; [ -f "$src" ] || src="$src?"
  got=$(sha256sum "$src" | cut -d' ' -f1)
  [ "sha256:$got" = "$sum" ] && ok=match || ok="MISMATCH $got"
  convert "$src[0]" -auto-orient -colorspace sRGB -resize '2400x2400>' -quality 82 "$OUT/$f"
  echo "$f $(identify -format '%wx%h' "$src[0]") -> $(identify -format '%wx%h' "$OUT/$f") checksum $ok"
done <<LIST
randywicks1995_karzinphoto_large.jpg gif/randywicks1995_karzinphoto_large.jpg sha256:81353d9d60bc161b519e3c0ab7287d3522a26ff9441ad59478daae79f6b7c4e5
sc1310.jpg gif/sc1310.jpg sha256:054c6fbf904d9388f3c1d0bdfab8bd00d9348d0279271a34584b93196d6ae3f4
LIST
