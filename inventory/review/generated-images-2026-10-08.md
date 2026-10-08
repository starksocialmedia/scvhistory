# Generated images in the archive (8 October 2026)

For Nathan ("check whether any other banner or decoration came from Firefly or Grok ... I want the list"). Claude. The rule, written into docs/DATA-MODEL.md today: no generated image of a real person, place or event anywhere in the archive, decoration included; marks and ornament not ruled.

## What it read

- The banner registry (templates/_data/banners.json) and each banner's original in inventory/incoming/done: the macOS download record on the file (the site it came from) and the bytes for a generator's mark.
- Every image in the web root, its metadata segments only, for a generator's mark (Grok, xAI, Firefly, C2PA, trained algorithmic media). None found: Craft strips metadata when it re-saves an upload, so the files cannot answer this, and the list rests on the next two.
- Every asset's own recorded provenance (enhancementMethod, source, contentCredentials and the other text fields), for Firefly, Grok, xAI, generated or generative (storage/runtime/photo-import/genai_assets.php).
- The origins list of inventory/incoming (sole-copies-origins-2026-10-08.md): what each Firefly and Grok download became.
- The site's own images outside the uploads: there are none.

## 1. Banners: all Grok portraits of real people. Taken off today

Fifteen files on seventeen pages, every one a Grok-generated drawing of a real person. Each original's download record names grok.com; the two PNGs carried xAI's mark. Off the site and out of web/banners; the registry is empty but for its rule. A page that lost its banner shows its portrait in the band.

William S. Hart (#16356); Maria Gutzeit (#21582); Laurene Weste (#15929); Cameron Smyth (#16380); Abel Stearns (#309); Aakash Ahuja (#25449); Alan Ferdman (#25191); Buck McKeon (#18791); H. Clyde Smyth (#15985); Cave Johnson Couts (#323); Leon Worden (#279); Andrés Pico (#317); Bill Cooper (#26946); Jerry Reynolds (#281) and his collection History of the Santa Clarita Valley (#871); Arthur B. Perkins (#333) and his collection Story of Our Valley (#873).

## 2. Taken off their records today: thirteen

The ten text-prompt portraits, Henry Clay Wiley's, and two more found today among files replaced in place on 6 October (below) whose credentials record text_to_image. Each is off every field of every record; the files stay in the volume, related to nothing, each with a note in its own source. Where the record held the unedited original, the original is now the portrait. The link from an original to its enhanced copy (enhancedFrom) was cleared too, because the original's page offered the enhanced file in its viewer. Script: pull_generated_portraits_2026_10_08.php; snapshot pre-pull-generated-2026-10-08.

How long: from each record's revision history (when the image first appears on it). The DDEV site is local; staging was refreshed by Nathan on 22 September and on 3 October (between 14:02 and 15:35, from the commits around it), and nothing of this build is on the public site.

| Record | Asset | What made it | On the record from | Published | Portrait now |
|---|---|---|---|---|---|
| #331 Henry Clay Wiley | #1658 henry-clay-wiley-portrait.jpg | Firefly download, no source recorded | 17 September, 23:43 | about three weeks; on staging (refreshed 22 September and 3 October) | none: no portrait |
| #285 Tiburcio Vasquez | #28816 tiburcio-vasquez-1874-upscaled.jpg | credential: text_to_image (the record called it an upscale) | 3 October, 13:46 | five days; probably on staging (refreshed later that day, between 14:02 and 15:35) | none: no portrait |
| #15874 Jill Klajic | #31451 sc9612-enhanced.jpg | text prompt, generative fill | 6 October, 09:04 | two days; local only | sc9612.jpg |
| #28703 Louis Brathwaite | #31445 brathwaite-louis-enhance.png | text prompt | 6 October, 09:04 | two days; local only | brathwaite-louis.jpg |
| #16418 Connie Worden | #31429 lw9501_large_enhanced.jpg | text prompt | 6 October, 08:29 | two days; local only | lw9501_large.jpg |
| #18869 Remi Nadeau | #31425 reminadeau-chrisman-enhance.jpg | text prompt, after an upscale | 6 October, 08:29 | two days; local only | reminadeau-chrisman.jpg |
| #29314 Pete Knight | #31414 pete-knight.jpg | text prompt | 6 October, 07:28 | two days; local only | none: no portrait |
| #31354 Tom Frew II | #31402 tf1000-enhanced.jpg | text prompt | 6 October, 07:26 | two days; local only | tf1000.jpg |
| #15477 Ruth Newhall | #31400 rn3004-enhanced.jpg | text prompt (a related image, not the portrait) | 6 October, 07:26 | two days; local only | her portrait unchanged |
| #16439 Vincent Gelcich | #31393 lw2452-enhanced.jpg | text prompt | 6 October, 07:26 | two days; local only | lw2452.jpg |
| #20226 William Wirt Jenkins | #31389 ap2222_large_enhanced.jpg | text prompt | 6 October, 07:26 | two days; local only | ap2222_large.jpg |
| #28675 Earl Schmidt | #31255 sk5003_large.jpg | text_to_image, replaced the legacy file in place | 6 October | two days; local only | none: no portrait |
| #18616 Dan Hon and #12480 | #14933 danhon.jpg | text_to_image and Generate Fill, replaced the legacy file in place | 6 October | two days; local only | none: no portrait |

## 3. Replaced in place on 6 October, recorded as "a better copy": ten

Ten legacy portraits had their file replaced in place on 6 October by a file Nathan supplied, each recorded as "a better copy of the same image". Every one was downloaded from firefly.adobe.com, and every content credential (read today from Adobe's manifest server) records generative steps. Two had text_to_image steps and are off (above). The other eight stay in place for your decision, and each record now says what was done (record_replaced_firefly_edits_2026_10_08.php): Adams (Generate Fill), Darryl Manzer (Firefly Image 5 edit, Generate Fill, upsampler), Clara Stroup (Image 5 edit, Generate Fill), Jan Heidt sc9611 (Image 5 edit, Generate Fill, upsampler), Michael D. Antonovich lw2427 (Image 5 edit), Francis T. Claffey (Image 5 edit, upsampler), Randy Wicks and Bob Kellar sc1310 (upsampler only). The unedited images are on Reggie at each asset's legacySourcePath. The Adams master committed to inventory/sole-copies is this Firefly output; its README entry is corrected.

## 4. Firefly edits: 41, ruled 8 October (late)

Applied: Nathan's rule ("Any portrait where Firefly filled, removed, cleaned, or made an edit the credential does not describe comes off the record"; the 14 with no original come off too; where an unedited original exists it becomes the portrait). 38 off: six replaced-in-place files restored to the original site's (restore_legacy_files_2026_10_08.php) and 32 pulled (pull_firefly_edits_2026_10_08.php), the original made the portrait on 17 of them. Three plain enlargements stay: Chico López, Randy Wicks, Bob Kellar. 106 of 255 person records have a portrait now; 17 lost theirs today.

As held before the ruling:

Grouped by what the credential or the recorded method says. A side-by-side sheet of each with its original: https://claude.ai/artifact/G3ksC59M8TWuNhyik6mcRA (private). No original is held for 14: Steve Knight, Sharon Runner, George Runner, the four Hart district portraits, the four City council portraits, Alan Ferdman, Bill Cooper and Andrés Pico.

### Fill, removal or cleaning: a model painted in what was not there: 13

- #28814 outside/john-c-fremont-california-state-library-upscaled.jpg: #307 John C. Frémont. enlarge recorded; the original is an oval carte de visite and the corners outside the oval were painted in (seen on the sheet).
- #31454 legacy/adrian-w-adams-hm7301_large.jpg: #28667 Adrian W. Adams. fill, enlarge.
- #31447 legacy/obituary_keithrichman-enhanced.jpg: #29316 Keith Richman. fill, model edit.
- #31410 legacy/lw2317a_large-writing-removed.jpg: #18702 Tom Mix. model edit, removal/clean, enlarge.
- #31254 legacy/darrylmanzer2020.jpg: #2579 Darryl Manzer. fill, model edit, enlarge.
- #31243 legacy/stroup_clara.jpg: #28713 Clara Stroup. fill, model edit.
- #27865 legacy/sc9611.jpg: #27866 Jan Heidt; #15737 Jan Heidt. fill, model edit, enlarge.
- #27396 outside/leon-worden-edited.jpg: #279 Leon Worden. fill, enlarge.
- #27391 outside/alan-ferdman-edited.jpg: #25191 Alan Ferdman. fill, enlarge.
- #27387 outside/cave-johnson-couts-us-army-edited.png: #323 Cave Johnson Couts. fill, model edit, removal/clean, enlarge.
- #27385 outside/andres-pico-commons-edited.jpg: #317 Andrés Pico. fill, model edit, enlarge.
- #27383 outside/demetrius-g-scofield-1911-edited.jpg: #21584 Demetrius G. Scofield. fill, model edit, removal/clean, enlarge.
- #27381 outside/arthur-b-perkins-outstanding-citizen-1964-edited.jpg: #333 Arthur Buckingham Perkins. model edit, removal/clean, enlarge.

### A Firefly Image 5 edit: the credential does not say what was edited: 18

- #31472 legacy/lw2178-enhance.jpg: #15919 Harry Carey. model edit, enlarge.
- #31449 legacy/sc9501-enhanced.jpg: #16140 Jo Anne Darcy. model edit.
- #31427 legacy/sc9010-enhanced.jpg: #15808 Carl Boyer. model edit.
- #31423 legacy/rr1-enhanced.jpg: #2585 Richard Rioux. model edit.
- #31408 legacy/lw2317a_large-enhanced.jpg: #18702 Tom Mix. model edit, enlarge.
- #31406 persons/william-lewis-manly-portrait-1890s-enhanced.jpg: #321 William Lewis Manly. model edit.
- #31404 legacy/obituary_kevingarylynch-enhanced.jpg: #30219 Kevin Lynch. model edit, enlarge.
- #31398 legacy/rn3002_large-enhanced.jpg: #15477 Ruth Newhall. model edit, enlarge.
- #31395 legacy/lw2529-enhanced.jpg: #18726 George Pederson. model edit, enlarge.
- #31391 legacy/lw2054-enhanced.jpg: #16432 William Mulholland. model edit.
- #31387 legacy/ap1334-enhanced.jpg: #20224 Sanford Lyon. model edit, enlarge.
- #31242 legacy/lw2427_large.jpg: #29284 Michael D. Antonovich. model edit, enlarge.
- #31236 legacy/sg19720614claffey01_large.jpg: #30211 Francis T. Claffey. model edit, enlarge.
- #29330 outside/steve-knight.jpg: #29328 Steve Knight. model edit.
- #29326 outside/sharon-runner.jpg: #29324 Sharon Runner. model edit.
- #29124 outside/george-runner.jpg: #18747 George Runner. model edit.
- #28978 outside/joe-messina-hart-district.jpg: #26549 Joe Messina. model edit.
- #28976 outside/bob-jensen-hart-district.jpg: #28322 Bob Jensen. model edit.

### Enlargement only (Firefly's generative upsampler): 10

- #31474 legacy/us8502_orig-enhanced.jpg: #28132 Francisco "Chico" López. enlarge.
- #31252 legacy/randywicks1995_karzinphoto_large.jpg: #18663 Randy Wicks. enlarge.
- #28974 outside/cherise-moore-hart-district.jpg: #28558 Cherise Moore. enlarge.
- #28972 outside/erin-wilson-hart-district.jpg: #28560 Erin Wilson. enlarge.
- #27852 legacy/sc1310.jpg: #27853 Bob Kellar; #21944 Bob Kellar. enlarge.
- #27402 outside/bill-miranda-edited.jpg: #23089 Bill Miranda. enlarge.
- #27400 outside/jason-gibbs-edited.jpg: #23091 Jason Gibbs. enlarge.
- #27398 outside/marsha-mclean-edited.jpg: #23085 Marsha McLean. enlarge.
- #27394 outside/patsy-ayala-edited.jpg: #23093 Patsy Ayala. enlarge.
- #27389 outside/bill-cooper-edited.jpg: #26946 Bill Cooper. enlarge.

## 5. Marks edited with Firefly: 6, and one original (not ruled)

- #29698 marks/newhall-elementary-school-logo-original.jpg: not on any record. The unedited original, kept beside the edited mark; not itself edited.
- #29696 marks/newhall-elementary-school-logo.png: #15958 Newhall Elementary School.
- #29563 marks/castaic-lake-water-agency-logo.png: not on any record.
- #29561 marks/newhall-county-water-district-logo.png: not on any record.
- #29439 marks/newhall-school-district-logo-2700.png: #21590 Newhall School District.
- #29400 marks/california-state-senate-seal.png: #28273 California State Senate.
- #29298 marks/canyon-high-school-logo.png: #21779 Canyon High School.

## 6. Not in the archive

In inventory/incoming, never imported: the other Firefly outputs (the 37 in the origins list less those that became the assets above), the 9 Grok images, and the three Firefly images committed in inventory/sole-copies as generated banners (Firefly.jpg, Firefly (1).jpg, Firefly (2).jpg). No page uses them. Asset #30544 (Sharlene Rose Johnson) names Firefly only to say its enlarged copy is not used.
