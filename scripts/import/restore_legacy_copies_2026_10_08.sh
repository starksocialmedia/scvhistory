# Web copies of eight legacy portraits on Reggie, for restore_legacy_files_2026_10_08.php (8 October 2026). Runs in the
# container; reads /mnt/reggie, writes storage/runtime/photo-import/restore/. Checks each master against the checksum the
# asset records, then the import's rule: 2,400 pixels on the long side at most, quality 82, never enlarged.
set -e
OUT=/var/www/html/storage/runtime/photo-import/restore; mkdir -p $OUT
while read f lsp sum; do
  src=/mnt/reggie/scvhistory.com/${lsp#/}; [ -f "$src" ] || src="$src?"
  got=$(sha256sum "$src" | cut -d' ' -f1)
  [ "sha256:$got" = "$sum" ] && ok=match || ok="MISMATCH $got"
  convert "$src[0]" -auto-orient -colorspace sRGB -resize '2400x2400>' -quality 82 "$OUT/$f"
  echo "$f $(identify -format '%wx%h' "$src[0]") -> $(identify -format '%wx%h' "$OUT/$f") checksum $ok"
done <<LIST
adrian-w-adams-hm7301_large.jpg gif/hm7301_large.jpg sha256:5f8273342770bb60fa564da2bce6d9ea76c791b0f4b3debbfbb0e96c3d5df437
darrylmanzer2020.jpg gif/mugs/darrylmanzer2020.jpg sha256:12d77b6f5251888fdc3e35b321d20dae4dd157a4c7fc660691aa54ab6ff5d1b2
stroup_clara.jpg gif/mugs/stroup_clara.jpg sha256:0a4663b73f46210856b29a1b4140a2d6aae20367c51d0a3a3b96b974472286cd
lw2427_large.jpg gif/lw2427_large.jpg sha256:0552281d523170cd7c61536ed6f4cb7e5d9649713de932989b8398989b61083b
sg19720614claffey01_large.jpg gif/sg19720614claffey01_large.jpg sha256:01e39af65a0cbe6d4ecfc20d83ae7b48424debac163943af9b5c9b62a6e32d57
sc9611.jpg gif/sc9611.jpg sha256:5c0ca4e8fd6360c9e408746ae64c9b5bb06cbecfe691ba19ddb0df8479070261
sk5003_large.jpg gif/sk5003_large.jpg sha256:20352e31668d35435fedc2a6cac368acefd52bc92461897820fc41f66a2ace4a
danhon.jpg /gif/danhon.jpg sha256:8130fe68887f405a56d3ee07f38dd840dae1e61b0f668b433c1aff09225cc8cc
LIST
