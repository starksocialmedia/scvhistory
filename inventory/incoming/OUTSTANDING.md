# inventory/incoming: what is outstanding

4 October 2026. **Rule from now on:** once a file is imported, it moves to `done/`, and
`done/MANIFEST.json` records what it became. A file counts as imported when its exact bytes match an
asset's `sourceChecksum` or a file under `web/uploads` or `web/banners`. 50 files moved on 4 October.

**Checked from 4 October 2026:** `scripts/import/check_incoming.php`, run by check_render, fails on any file
here that is neither in `done/MANIFEST.json` nor named below with its reason. A new file is imported when it
arrives, on its own, or listed here with why it waits.

The files left here fall into four groups.

## 0. Screenshots of the archive's own pages (checked 5 October 2026)

Sixteen files named .jpg are PNG page captures about 3,066 pixels wide, full-page screenshots of record pages on the new
site (Northridge Earthquake, Dante Acosta, Henry Clay Wiley, Reynolds's chapters and others). They are not photographs and
nothing waits on them; where they are listed below under an earlier reading, this is what they are:
beales-cut-sta.jpg, chapter-21-b.jpg, dante-acosta.jpg, getting-closer.jpg, henry-clay-w.jpg,
history-of-the-santa-clarita-va.jpg, in-memoriam-henry-clay-wiley-182.jpg, in-memoriam.jpg, newhall-pass-i.jpg,
northridge-ear.jpg, not-even-close.jpg, prologue-his.jpg, rancho.jpg, rodolfo-acost.jpg, rudy.jpg, the-birth-of.jpg.

## 1. The record already has an image from another file

The record named below already has its picture, so this file is probably the original of an image
imported after editing, or an alternative. Keep it or drop it; nothing waits on it.

| File | Record | Image the record already shows |
|---|---|---|
| Buck_McKeon_2011.jpeg, Buck_McKeon_2011-commons-original.jpeg | Buck McKeon | buck-mckeon-official-portrait-2011.jpg |
| DemetriusGScofield-commons.jpg | Demetrius G. Scofield | demetrius-g-scofield-1911-edited.jpg |
| Williamshart.jpg, WilliamS.jpg | William S. Hart | william-s-hart-loc-cph-3c03842.jpg |
| henry-clay-w.jpg, henry-clay-wiley-portrait.jpg, in-memoriam-henry-clay-wiley-182.jpg, in-memoriam.jpg | Henry Clay Wiley | henry-clay-wiley-portrait.jpg |
| reynolds_jerry.jpg | Jerry Reynolds | jerry-reynolds.jpg |
| dante-acosta.jpg | Dante Acosta | dante-acosta.jpg (different bytes) |
| rudy.jpg | Rudy Alexander Acosta | mug_rudyacostaarmy.jpg |
| rodolfo-acost.jpg | Rodolfo Acosta | rodolfo_acosta_in_one-eyed_jacks.jpg |
| bob-keller.jpg | Bob Kellar | sc1310.jpg |
| Cameron-Smyth.jpg | Cameron Smyth | cameron-smyth-2017.jpg |
| CSUN.jpg, csun-central-campus-commons.jpg | California State University, Northridge | csun-oviatt-library-commons.jpg |
| city-hall.jpg, santa-clarita-city-hall-flickr-2600036728.jpg | The City of Santa Clarita | santa-clarita-city-hall-2008-flickr.jpg |
| beales-cut-sta.jpg | Beale's Cut | the 1923 Tom Mix photograph |
| northridge-ear.jpg | Northridge Earthquake | the Newhall Pass freeway collapse, 1994 |
| newhall-pass-i.jpg | Newhall Pass interchange | the 2016 photograph |
| chapter-21-b.jpg, prologue-his.jpg, history-of-the-santa-clarita-va.jpg, the-birth-of.jpg, reynolds=map.jpg, reynolds=map-2.jpg, reynolds=map-3.jpg, reynolds=map-4.jpg | Jerry Reynolds's chapters | each chapter carries an image already (for example chapter 15) |

## 2. Not imported, by decision or waiting on one

| File | Why |
|---|---|
| Sharlene-Duzick.jpg | The Adobe Firefly upscale (creative upsampler, 4 October 2026) of sharlene-headshot.jpg, which is now Sharlene Rose Johnson's portrait (#30544, 5 October 2026: the losing-candidate decision reversed, she being a sitting college trustee). The archive uses the unedited original. |
| BOM-pg14-shutterstock-185944559.jpg | A Shutterstock image. Its licence would need to be in hand before use. |
| Firefly.jpg, Firefly (1).jpg, Firefly (2).jpg, grok-image-fb994b75-1760-4be5-a4da-3623d2bc4605.jpg | Generated images. Under the banner rule they are decoration only, in web/banners and never assets. None matches a banner file now. |

## 3. Not identified

The file name does not say what these are for. Nathan to say, or move them to `done/` if they are
no longer wanted:

- 1572388199414-710-817.jpg
- 8-pioneer-oil-refinery-california-star-oil-works700x450.jpg (perhaps for California Star Oil Works,
  #16039)
- IMG_2226 2.jpg, IMG_4024-2048x1983.jpg
- eMU3V.jpg
- edited-image.jpg, edited-image (1).jpg
- getting-closer.jpg, not-even-close.jpg
- tank-guy.jpg
- rancho.jpg
- lw2184.jpg

## 4. Portrait batch of 5 October 2026

Applied on 6 October 2026; its files, Chico López's US8502 and Tom Frew II's TF1000 are in done/ with MANIFEST.json.
