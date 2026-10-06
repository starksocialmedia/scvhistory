
# War memorial narratives: dry run, 5 October 2026

Script: `scripts/import/restore_war_memorial_narratives_2026_10_05.php`. Source: the silent-faults audit, finding 3.

The mirror is not visible inside the container (Reggie is not connected: there is no /mnt/reggie in the container. This script reads the legacy mirror, so it cannot run without it. Plug the drive in, then restart DDEV (warn first, and wait for `ddev mutagen status` to read ok); .ddev/drive-mount.sh mounts it at start.). The mirror check below is the extractor's, run on the host against the file whose sha256 is shown.

## #570 John Amos Ward

- Legacy page: `scvhistory.com/warmemorial/ww2_johnward.htm` (legacyUrl `/warmemorial/ww2_johnward.htm`)

**Current text** (59 characters, wmNarrative and body identical):

> PVT John Amos Ward fought with the 110th Infantry Regiment,

**Restored text** (1665 characters, 1 paragraph(s), 284 words):

> PVT John Amos Ward fought with the 110th Infantry Regiment, 28th Infantry Division. The 28th had been trained for the invasion of Normandy and landed there on July 22, 1944, six weeks after D-Day. It was sent to the front and fought with distinction in the Normandy campaign and represented the United States during ceremonies marking the liberation of Paris. In September 1944 it became the first Allied division to cross Germany's Siegfried line. In December the 28th was sent to the front at the Ardennes, with the 110th assigned to defend the center section of the 25-mile front line when the Germans attacked. After three weeks of heavy fighting and heavy losses in this "Battle of the Bulge" (Dec. 16, 1944-Jan. 2, 1945), the 110th held the Germans back from Neufchâteau, France. "Five enemy divisions drove through the 110th's sector, but the units of the regiment held so firmly at all costs that the Germans' plan was disrupted and their schedule thrown off balance," the commanding officer, COL Daniel B. Strickler, later said of the 110th's participation in the Bulge. On Jan. 2, 1945, the 110th took up a new defensive line along the Meuse River. In mid-January it received 2,500 reinforcements and traveled by boxcars 250 miles south to the Vosges Mountains in central Alsace where the Germans were holding the so-called Colmar Pocket — the last major French area in German hands. It was probably here that PVT John Amos Ward was killed in action on February 1, 1945. After his initial burial in a French American Military Cemetery, his remains were returned to the United States in 1949 and he was laid to rest at Valhalla Cemetery in North Hollywood.

**Checks**

- Restored text in order, word for word, in the mirror page: extractor on the host: yes (sha256 a0df4472ffd5f828...)
- Restored text in order, word for word, in the stored legacyHtml: yes
- Same narrative re-extracted from the stored legacyHtml: yes
- Restored text begins with the current text: yes
- Ends with closing punctuation: yes
- Would set: wmNarrative, body
- wmAwards now: (empty)

## #552 Henry Acuna

- Legacy page: `scvhistory.com/warmemorial/korea_henryacuna.htm` (legacyUrl `/warmemorial/korea_henryacuna.htm`)

**Current text** (167 characters, wmNarrative and body identical):

> Private Acuna was reported Missing In Action on July 31, 1950. The fact of his death was ascertained two and a half months later. Approximately a year after his death,

**Restored text** (355 characters, 1 paragraph(s), 62 words):

> Private Acuna was reported Missing In Action on July 31, 1950. The fact of his death was ascertained two and a half months later. Approximately a year after his death, in June 1951, his body was shipped to San Francisco and returned to his family for burial. His mother, Micile Acuna, lived in Saugus. His sister, Sally Acuna Augiula, lived in Northridge.

**Checks**

- Restored text in order, word for word, in the mirror page: extractor on the host: yes (sha256 7a9b4603d1216f3a...)
- Restored text in order, word for word, in the stored legacyHtml: yes
- Same narrative re-extracted from the stored legacyHtml: yes
- Restored text begins with the current text: yes
- Ends with closing punctuation: yes
- Would set: wmNarrative, body
- wmAwards now: (empty)

## #546 Brian Cody Prosser

- Legacy page: `scvhistory.com/warmemorial/terror_brianprosser.htm` (legacyUrl `/warmemorial/terror_brianprosser.htm`)

**Current text** (360 characters, wmNarrative and body identical):

> SSG Brian Cody Prosser of Frazier Park was a Green Beret soldier assigned to the 3rd Battalion, 5th Special Forces Group. At age 28, SSG Prosser was killed by friendly fire when an Air Force B-52 dropped a 1-ton bomb after an airstrike was called in on nearby Taliban forces in Kandahar, Afghanistan, on December 5, 2001. The bomb fell about 100 yards from the

**Restored text** (778 characters, 1 paragraph(s), 130 words):

> SSG Brian Cody Prosser of Frazier Park was a Green Beret soldier assigned to the 3rd Battalion, 5th Special Forces Group. At age 28, SSG Prosser was killed by friendly fire when an Air Force B-52 dropped a 1-ton bomb after an airstrike was called in on nearby Taliban forces in Kandahar, Afghanistan, on December 5, 2001. The bomb fell about 100 yards from the troop position. SSG Prosser and two other Army Special Forces soldiers were killed in the blast, as were five anti-Taliban Afghan fighters. Twenty U.S. soldiers and 20 Afghans were wounded. SSG Prosser was awarded the Bronze Star w/V Device, Army Commendation Medal, 8 Army Achievement Medals, 3 Good Conduct Medals, Purple Heart, and numerous other citations. He is buried in Arlington National Cemetery in Virginia.

**Checks**

- Restored text in order, word for word, in the mirror page: extractor on the host: yes (sha256 d5c36425c1ead665...)
- Restored text in order, word for word, in the stored legacyHtml: yes
- Same narrative re-extracted from the stored legacyHtml: yes
- Restored text begins with the current text: yes
- Ends with closing punctuation: yes
- Would set: wmNarrative, body
- wmAwards now: (empty). The restored text also names: Bronze Star, Purple Heart, Commendation Medal, Good Conduct Medal, Achievement Medal (a name in the text, not necessarily his award: not written; for Nathan)

## #516 Edward D. Contreras

- Legacy page: `scvhistory.com/warmemorial/ww2_edwardcontreras.htm` (legacyUrl `/warmemorial/ww2_edwardcontreras.htm`)

**Current text** (178 characters, wmNarrative and body identical):

> SGT Ed Contreras served with the 30th Infantry Regiment, which was assigned to the 3rd Division during World War II. The 3rd Division had the distinction of engaging the enemy on

**Restored text** (2092 characters, 8 paragraph(s), 368 words):

> SGT Ed Contreras served with the 30th Infantry Regiment, which was assigned to the 3rd Division during World War II. The 3rd Division had the distinction of engaging the enemy on all European fronts — North Africa, Sicily, Italy, France, Germany, Austria — 531 consecutive days of fighting.
> 
> Enlisting in January 1941 as a private (roughly a year before the U.S. entered the war), Contreras would see much of it. The action started Nov. 8, 1942, when the 3rd Division joined the invasion of North Africa and captured half of French Morocco. After the Casablanca Conference in January 1943, Allied leaders decided to liberate Sicily in July — the 3rd made an amphibious assault on the 10th — and then cross over to the mainland and start pushing up the boot.
> 
> On Sept. 18, 1943, with American airborne units reinforcing the beachhead, the 3rd landed at Salerno and methodically fought its way northward. On Jan. 22, 1944, the 3rd participated in the landing at Anzio as part of VI Corps of British and American units. Here the 3rd would dig in for four months as the Germans furiously counterattacked.
> 
> And here, in the vicinity of Anzio, is where 26-year-old SGT Contreras fell on Feb. 9.
> 
> Rifleman James Arness of the 7th Infantry Regiment sustained a severe leg wound in the Anzio landing on the 22nd that would pester him on "Gunsmoke." LT Audie Murphy of the 15th Infantry Regiment would earn the Bronze Star with V Device when he crawled out of an abandoned farmhouse on March 2 to destroy a German tank with rifle grenades.
> 
> Meanwhile on Feb. 29, three German divisions simultaneously attacked U.S. positions. The 3rd Division lost 900 men on that single day — the most of any U.S. division in one day during World War II.
> 
> Finally on May 11 the Allies were able to break out from the beachhead when they penetrated Germany's Gustav Line. Rome fell on June 4 and the Germans were now in full retreat.
> 
> SGT Contreras was awarded the Purple Heart and was laid to rest in the Sicily-Rome American Cemetery in Nettuno, Italy, with a cross marking his burial site in Block J, Row 4, Grave 7.

**Checks**

- Restored text in order, word for word, in the mirror page: extractor on the host: yes (sha256 ee983ee030bbaf63...)
- Restored text in order, word for word, in the stored legacyHtml: yes
- Same narrative re-extracted from the stored legacyHtml: yes
- Restored text begins with the current text: yes
- Ends with closing punctuation: yes
- Would set: wmNarrative, body
- wmAwards now: "Purple Heart Medal". The restored text also names: Bronze Star (a name in the text, not necessarily his award: not written; for Nathan)

## #566 James A. Bartlett

- Legacy page: `scvhistory.com/warmemorial/ww2_jimbartlett.htm` (legacyUrl `/warmemorial/ww2_jimbartlett.htm`)

**Current text** (281 characters, wmNarrative and body identical):

> Jim Bartlett of Soledad Township enlisted in the US Army in January 1943 and was trained for aerial photography. At age 29, Bartlett was aboard a US Transport ship in the Mediterranean Sea — probably the SS Paul Hamilton — when it was struck by an aerial torpedo on April 20, 1944.

**Restored text** (445 characters, 1 paragraph(s), 79 words):

> Jim Bartlett of Soledad Township enlisted in the US Army in January 1943 and was trained for aerial photography. At age 29, Bartlett was aboard a US Transport ship in the Mediterranean Sea — probably the SS Paul Hamilton — when it was struck by an aerial torpedo on April 20, 1944. His remains were unrecovered. He is memorialized in the Tablets of the Missing at the North Africa American Cemetery, Tunisia, and he was awarded the Purple Heart.

**Checks**

- Restored text in order, word for word, in the mirror page: extractor on the host: yes (sha256 d0a4f435eff2652e...)
- Restored text in order, word for word, in the stored legacyHtml: yes
- Same narrative re-extracted from the stored legacyHtml: yes
- Restored text begins with the current text: yes
- Ends with closing punctuation: yes
- Would set: wmNarrative, body
- wmAwards now: (empty). The restored text also names: Purple Heart (a name in the text, not necessarily his award: not written; for Nathan)

## #564 James M. Redmond

- Legacy page: `scvhistory.com/warmemorial/ww2_jamesredman.htm` (legacyUrl `/warmemorial/ww2_jamesredman.htm`)

**Current text** (398 characters, wmNarrative and body identical):

> SSG James M. Redmond of Soledad Township was killed in action during savage fighting in Belgium. His outfit was previously in the thick of operations on D-Day, June 1944, and his group received a citation for slashing a 50-yard gap through barbed wire, underwater obstacles and mines on the coast of Normandy when the big push started. SSG Redmond had participated in battles in France and Holland.

**Restored text** (471 characters, 1 paragraph(s), 80 words):

> SSG James M. Redmond of Soledad Township was killed in action during savage fighting in Belgium. His outfit was previously in the thick of operations on D-Day, June 1944, and his group received a citation for slashing a 50-yard gap through barbed wire, underwater obstacles and mines on the coast of Normandy when the big push started. SSG Redmond had participated in battles in France and Holland. He was awarded the Bronze Star, Purple Heart and French Croix de Guerre.

**Checks**

- Restored text in order, word for word, in the mirror page: extractor on the host: yes (sha256 414fa377b9993cba...)
- Restored text in order, word for word, in the stored legacyHtml: yes
- Same narrative re-extracted from the stored legacyHtml: yes
- Restored text begins with the current text: yes
- Ends with closing punctuation: yes
- Would set: wmNarrative, body
- wmAwards now: (empty). The restored text also names: Bronze Star, Purple Heart, Croix de Guerre (a name in the text, not necessarily his award: not written; for Nathan)

## #532 Jose Ricardo Flores-Mejia

- Legacy page: `scvhistory.com/warmemorial/terror_josefloresmejia.htm` (legacyUrl `/warmemorial/terror_josefloresmejia.htm`)

**Current text** (115 characters, wmNarrative and body identical):

> PFC José Ricardo Flores-Mejia of Santa Clarita served with the 25th Transportation Company, 25th Infantry Division.

**Restored text** (594 characters, 1 paragraph(s), 94 words):

> PFC José Ricardo Flores-Mejia of Santa Clarita served with the 25th Transportation Company, 25th Infantry Division. At age 21, PFC Flores-Mejia was killed November 16, 2004, in Mosul, Iraq, when an improvised explosive device hit his convoy. He was awarded the Bronze Star and Purple Heart, among numerous other citations. PFC Flores-Mejia's son was born just days before he was killed. His grandmother, Eva Carrillo, died from injuries sustained in an automobile accident in Mexico while she was en route to his funeral. PFC Flores-Mejia is buried at Eternal Valley Cemetery in Newhall, Calif.

**Checks**

- Restored text in order, word for word, in the mirror page: extractor on the host: yes (sha256 9c53a896f7bccf42...)
- Restored text in order, word for word, in the stored legacyHtml: yes
- Same narrative re-extracted from the stored legacyHtml: yes
- Restored text begins with the current text: yes
- Ends with closing punctuation: yes
- Would set: wmNarrative, body
- wmAwards now: (empty). The restored text also names: Bronze Star, Purple Heart (a name in the text, not necessarily his award: not written; for Nathan)

## #514 Lawrence E. Kenaston

- Legacy page: `scvhistory.com/warmemorial/ww2_ekenaston.htm` (legacyUrl `/warmemorial/ww2_ekenaston.htm`)

**Current text** (157 characters, wmNarrative and body identical):

> CPL Lawrence E. Kenaston was a veteran of Guadalcanal, and six weeks after his return home took his own life at his stepsister's house in Newhall. He was 34.

**Restored text** (587 characters, 2 paragraph(s), 108 words):

> CPL Lawrence E. Kenaston was a veteran of Guadalcanal, and six weeks after his return home took his own life at his stepsister's house in Newhall. He was 34. A suicide note revealed in anguished terms that he was hopelessly in love with her.
> 
> CPL Kenaston was born in Washington State. He was in Washington in 1920; in the Pearl Harbor Naval Reserve in 1930; and he is on the Navy muster rolls in 1940. Career military, he may have had no permanent home. His mother lived in Ventura County. His stepsister grew up in Ventura County, married and lived in Newhall at the time of his death.

**Checks**

- Restored text in order, word for word, in the mirror page: extractor on the host: yes (sha256 7116c7aa0ddd54ed...)
- Restored text in order, word for word, in the stored legacyHtml: yes
- Same narrative re-extracted from the stored legacyHtml: yes
- Restored text begins with the current text: yes
- Ends with closing punctuation: yes
- Would set: wmNarrative, body
- wmAwards now: (empty)

## Summary

- Records to restore: 8 (#570, #552, #546, #516, #566, #564, #532, #514)
- Refused: 0
- Nothing written. A second run skips every field already restored.


