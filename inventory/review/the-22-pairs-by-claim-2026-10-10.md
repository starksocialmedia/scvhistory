# The 22 enhanced pairs, grouped by the claim each makes (10 October 2026)

Claude, for Nathan. Nothing was changed on any of the 22.

## What was read

- **The stored copies of all 22 pairs**, as on the sheets in text-to-image-pairs-2026-10-09/. Dimensions were read with `sips`.
- **For the three guesses:** the stored copies of Perkins (#6 and #27381), lw9501 (#30549 and #31429) and rr1 (#31251 and #31423).
  - For each pair, the enhanced file was placed on the original by a brute-force search: the scale and offset that best match the face. For Perkins, a grid of mean differences followed, cell by cell, plus zoomed crops of the same region in both files.
  - Scripts: storage/runtime/scratch/align.php, diffmap.php, zoom.php. Images: storage/runtime/scratch/guesses/.
- **The originals' masters on Reggie were not opened for this.** The stored copies are what the sheets show.

## 1. The three guesses, settled by measurement

**Perkins (#27381 from #6). Mostly the crop; the remainder painted over.**
- The original is 800 by 804. The enhanced file covers the original's x 80 to 672 and y 8 to 800, at a match of 4.3 grey levels on the face.
- So the crop removed the woman's hair and almost all of her. Her shoulder starts at about x 640 in the original.
- What the crop left of her is a strip of patterned dress about 30 pixels wide at the bottom right, from y 640 to 800. In that strip the difference grid jumps from 3 to between 48 and 99. The zoomed crops show the patterned dress in the original and the jacket's black continued over it in the enhanced file.
- Answer: cropped out, and the sliver the crop kept was painted over with jacket. The chain's Remove Object step fits that.

**lw9501 (#31429 from #30549). The credit was removed, not cropped.**
- The original is 1,883 by 2,400. The enhanced file covers x 40 to 1,840 at full height, a match of 1.6, so it is nearly the whole frame.
- "Photo by Gary Choppe'" is printed at about x 30 to 360, y 2,270 to 2,310, well inside the enhanced frame.
- In the enhanced file that region is the red light streaks and the dark road, continued with no lettering.

**rr1 (#31423 from #31251). The light is the original's; a haze was added to it.**
- The original's top right shows bright sky through a gap in the trees. The enhanced file has the same gap in the same place.
- What is new is a soft haze spreading from it down the right side, over the trees and his shoulder. It reads as a light source added to light that was already there.
- The enhanced file crops the original's bottom: it ends below the belt, where the original runs to mid-thigh.
- One more thing about rr1, which bears on the groups below: the original is 225 by 311, and the enhanced file is 3,552 by 4,736, 15 times larger. His face in the original is about 45 pixels wide.

## 2. The 22 by the claim each makes

These are your groups, as you set them.

**A. The face redrawn.** The enhanced face is not the face the photograph holds.
- Scofield #27383 (original #21761): round wire spectacles are drawn on, and the face and jacket are redone as a modern studio portrait.
- ap1334 #31387 (original #2316, record #20224): a younger, leaner face with the hair in a side part, and a cabinet-card mount added.
- tf1000 #31402 (original #31356): deeper lines and a fuller smile, and the suit re-rendered.

**B. A face enlarged out of a smaller original.** A head-and-shoulders portrait made from a small figure in a full-length photograph.
- rn3002 #31398 (original #31397): a seated, full-length snapshot.
- lw2317a #31408 (original #31250): a full-length press print. Its grease-pencil marks also put it in D.

**C. Content added beyond the frame.**
- Keith Richman #31447 (original #31239): a jacket, shirt and patterned tie below the original's collar-line crop.
- sc9612 #31451 (original #27869): more wall, flag and lilies at the top and sides, from two Generate Fills.

**D. Content removed.**
- Perkins #27381: the woman, by crop and paint-over (above).
- Manly #31406: the signature and the engraving's border.
- lw9501 #31429: the photographer's credit (above).
- lw2178 #31472: the signature and the foxed vignette.
- lw2317a #31408: the crop marks and handwriting.

**E. Tone and sharpness only.**
- lw2054 #31391, lw2529 #31395, Kevin Gary Lynch #31404, rr1 #31423, sc9010 #31427, sc9501 #31449.
- Couts #27387, Brathwaite #31445, Nadeau and Chrisman #31425, lw2452 #31393, ap2222 #31389.

## 3. Measurements that bear on B against E

How much larger each enhanced file is than its original, height for height. A large factor means detail the original does not hold had to be supplied.

| Pair | Original | Enhanced | Larger by | Group |
|---|---|---|---:|---|
| rr1 #31423 | 225 x 311 | 3552 x 4736 | 15.2 | E |
| Nadeau and Chrisman #31425 | 334 x 504 | 3552 x 4736 | 9.4 | E |
| sc9612 #31451 | 800 x 706 | 3552 x 4736 | 6.7 | C |
| Scofield #27383 | 374 x 477 | 2167 x 2892 | 6.1 | A |
| Keith Richman #31447 | 800 x 781 | 3520 x 4736 | 6.1 | C |
| Brathwaite #31445 | 150 x 200 | 880 x 1184 | 5.9 | E |
| Perkins #27381 | 800 x 804 | 3035 x 4051 | 5.0 | D |
| sc9010, sc9501, lw2452, lw2529, ap1334, Lynch, lw2054, lw2178 | about 800 wide | about 3,400 to 3,600 wide | 4.4 to 4.6 | E, A |
| Manly #31406 | 700 x 963 | 2881 x 3843 | 4.0 | D |
| tf1000 #31402 | 800 x 1246 | 3296 x 4394 | 3.5 | A |
| lw2317a #31408 | 1600 x 2133 | 3552 x 4736 | 2.2 for the frame; more for the face, which fills far less of the original | B |
| rn3002 #31398 | 2100 x 2400 | 3552 x 4736 | 2.0 for the frame; likewise | B |
| lw9501 #31429 | 1883 x 2400 | 3456 x 4608 | 1.9 | D |
| Couts #27387 | 927 x 1360 | 1696 x 2480 | 1.8 | E |
| ap2222 #31389 | 1595 x 2400 | 3328 x 4438 | 1.8 | E |

Three in E may make the B claim by this measure, though the frame was not cropped:
- **rr1:** a 225-pixel-wide snapshot enlarged 15 times.
- **Nadeau and Chrisman:** a small full-length figure, 334 pixels wide, enlarged 9 times. His face is a few dozen pixels across in the original.
- **Brathwaite:** a 150-by-200 file enlarged 6 times.

Whether they stay in E or move to B is yours.

Two cautions:
- The "originals" here are the archive's stored copies. A larger master on Reggie, or in your own files, would lower these factors.
- Most of the files about 800 pixels wide are the legacy site's display size, not a scan's.
