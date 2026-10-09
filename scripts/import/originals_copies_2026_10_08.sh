# Web copies of the unedited photographs found for the 19 people whose portraits came off on 8 October (Nathan: "find the
# originals ... Any that turn up go straight back on as the portrait, unedited"). Runs in the container. Three masters are on
# Reggie (checked against the drive manifest here); sixteen were downloaded on 8 October and are kept, as downloaded, in
# inventory/incoming/done/originals-2026-10-08/. The import's rule: 2,400 pixels on the long side at most, never enlarged,
# sRGB (George Runner's is a CMYK JPEG), JPEG at quality 82; a PNG stays a PNG. Writes storage/runtime/photo-import/originals-web/.
set -e
echo 'READ, before any number:'
echo '  the file itself: each master (Reggie or inventory/incoming/done/originals-2026-10-08), hashed and decoded'
echo '  a record about a file: the drive manifest checksums for the three on Reggie; the files were read too'
OUT=/var/www/html/storage/runtime/photo-import/originals-web; IN=/var/www/html/inventory/incoming/done/originals-2026-10-08; mkdir -p $OUT
while read web src sum; do
  case $src in reggie:*) p=/mnt/reggie/scvhistory.com/${src#reggie:}; got=$(sha256sum "$p" | cut -d' ' -f1); [ "$got" = "$sum" ] && ok=match || ok="MISMATCH $got";; *) p=$IN/$src; ok="kept as downloaded, sha256 $(sha256sum "$p" | cut -c1-12)";; esac
  case $web in *.png) convert "$p[0]" -auto-orient -colorspace sRGB -resize '2400x2400>' "$OUT/$web";; *) convert "$p[0]" -auto-orient -colorspace sRGB -resize '2400x2400>' -quality 82 "$OUT/$web";; esac
  echo "$web $(identify -format '%wx%h %[colorspace]' "$p[0]") -> $(identify -format '%wx%h' "$OUT/$web") $ok"
done <<LIST
billmiranda2017_large.jpg reggie:gif/billmiranda2017_large.jpg ac61097d2157becf90cc5f422480e5059d9fd70c9ecf187dd6b6cf7606c864a1
sc1313_large.jpg reggie:gif/sc1313_large.jpg 3a9e1066be8113f4e54dff66c2791723ee24697ef2129245d794f51c5955036a
sw_hssc0402wiley_large.png reggie:gif/sw_hssc0402wiley_large.png 1408963a44d01586ce9a360959b91f8b9cc620113512fceaf8a341e205f6571a
jason-gibbs-city-2023.png jason-gibbs-city-2023.png -
patsy-ayala-city-2024.png patsy-ayala-city-2024.png -
bill-cooper-campaign-2026.png bill-cooper-campaign-2026.png -
alan-ferdman-khts-2020.jpg alan-ferdman-khts-2020.jpg -
steve-knight-congress-2015.jpg steve-knight-congress-2015.jpg -
george-runner-boe-2011.jpg george-runner-boe-2011.jpg -
sharon-runner-assembly-2007.jpg sharon-runner-assembly-2007.jpg -
bob-jensen-hart-2016.jpg bob-jensen-hart-2016.jpg -
joe-messina-hart-2016.jpg joe-messina-hart-2016.jpg -
cherise-moore-hart.jpg cherise-moore-hart.jpg -
erin-wilson-hart-2023.jpg erin-wilson-hart-2023.jpg -
audra-strickland-assembly.jpg audra-strickland-assembly.jpg -
bj-atkins-scvnews-2012.jpg bj-atkins-scvnews-2012.jpg -
jerry-gladbach-acwa-2022.jpg jerry-gladbach-acwa-2022.jpg -
patti-rasmussen-city.jpg patti-rasmussen-city.jpg -
brian-walters-scvnews-2013.jpg brian-walters-scvnews-2013.jpg -
LIST
