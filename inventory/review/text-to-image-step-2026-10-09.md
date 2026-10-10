# The text_to_image step: every chain read (9 October 2026, evening)

Claude, for Nathan. This replaces the afternoon version of the same file.

What you asked (Nathan, 9 October, evening):
- for each of the 14, Couts and the seven: does the chain open a parent file or start from nothing; is text_to_image the first action or a later one; what does each step sit next to; does the manifest name the original;
- two groups, with the evidence, and no recommendation.

Apart from Couts (item 2 of your brief, below), nothing was changed for any of them.

## What was read, before any number

- **The 22 manifests, manifest by manifest.** Each file's provenance link names a manifest store. A store holds one manifest per save. `scripts/import/manifest_steps_2026_10_09.py --chains` reads each manifest's ingredients and actions as CBOR values:
  - for each ingredient, its `relationship` and `dc:format`, and whether it carries a credential of its own (`activeManifest` with `validationResults`);
  - for each action: `action`, the software agent's `name`, `com.adobe.firefly.operation`, `digitalSourceType`.
  - Copies are in storage/runtime/manifests, fetched once by `_generated_scan.php`.
- **The 22 pairs of files.** Each original (the asset the enhanced file's enhancedFrom or source names) and each enhanced file, as stored, was put side by side and looked at:
  - sheets in inventory/review/text-to-image-pairs-2026-10-09/: pairsheet-0 to 3, all 22 small; pairbig-0 to 4, ten at a larger size;
  - made by make-sheets.php.txt with PHP GD.
  - The stored copies were used. Craft re-encodes uploads, but the pictures are the same pictures.

## What a chain looks like

A manifest's ingredient is one of two things:
- **The previous save in the same chain.** It carries a credential.
- **A file brought in from outside the chain.** It carries none.

The first manifest's outside ingredient is the file the work started from.
- `parentOf` is the image being edited.
- `inputTo` is a file fed to a tool. At the root of an upscale it is the image upscaled. Beside a Generate Fill it is the fill's mask, a PNG.
- **No manifest names the parent file**: there is no `dc:title` and no thumbnail. What the manifest gives is its format (JPEG or PNG) and an instance ID. Which archive file the parent was is shown by the pictures, not by the manifest.

## Group 1: the chain opens a file brought in from outside it: all 22

**The 14 on records:**

| Asset | Record | Manifests | Root opens | First action | text_to_image at | Before / after the step |
|---|---|---:|---|---|---|---|
| #27381 Perkins | #333 | 5 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / Remove Object, then the upsampler |
| #27383 Scofield | #21584 | 5 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / Generate Fill, then the upsampler |
| #31387 ap1334 | #20224 | 3 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31391 lw2054 | #16432 | 2 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31395 lw2529 | #18726 | 2 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31398 rn3002 | #15477 | 2 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31404 Kevin Gary Lynch | #30219 | 2 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31406 Manly | #321 | 3 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31408 lw2317a | #18702 | 2 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31423 rr1 | #2585 | 4 | inputTo JPEG | created (upsampler) | manifest 3, action 2 | the upscaled upload / the upsampler |
| #31427 sc9010 | #15808 | 3 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31447 Keith Richman | #29316 | 6 | parentOf PNG, inputTo PNG (a mask) | opened | manifest 5, action 2 | two Generate Fills / the upsampler |
| #31449 sc9501 | #16140 | 3 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31472 lw2178 | #15919 | 3 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |

**Couts:** #27387, on #323 again since this evening (see "Couts", below).
- One manifest.
- The root opens a JPEG, parentOf. The first action is opened.
- text_to_image is action 2, the Image 5 edit of that file.
- Nothing follows it.

**The seven held:**

| Asset | Manifests | Root opens | First action | text_to_image at | Before / after the step |
|---|---:|---|---|---|---|
| #31451 sc9612 | 4 | parentOf PNG, inputTo PNG (a mask) | opened | manifest 3, action 2 | Generate Fill / the upsampler |
| #31445 Brathwaite | 8 | inputTo JPEG | created (upsampler) | manifests 2 and 8 | the upscaled upload; later Generate Fill and a second Image 5 edit |
| #31429 lw9501 | 3 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31425 Nadeau and Chrisman | 4 | inputTo JPEG | created (upsampler) | manifest 3, action 2 | the upscaled upload / the upsampler |
| #31402 tf1000 | 3 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |
| #31393 lw2452 | 5 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / Generate Fill, then the upsampler |
| #31389 ap2222 | 3 | parentOf JPEG | opened | manifest 1, action 2 | the opened upload / the upsampler |

In every one of the 22:
- The text_to_image step is a `c2pa.edited` action by "Adobe Firefly Image 5", with source type `compositeWithTrainedAlgorithmicMedia` (a composite of the photograph and generated content).
- It acts on a file the chain opened. Either it is the second action after the opened upload, or it follows earlier saves that go back to that upload.
- In no chain is text_to_image the first action. In no chain is there a `c2pa.created` with no ingredient.

## Group 2: a chain with no parent: none

No manifest of the 22 starts from nothing.

The one generated image found this week that does is Bill Cooper's campaign file. Its manifest is OpenAI's, created by gpt-image with no ingredient, and it is off his record.

## What the manifests cannot say, and the pictures do

Your test was this:

> "If every one of the 14 opens a real original, then the step is telling us which model did the work rather than that a face was invented."

On the first half, the manifests are clear: every chain opens an original. On the second half they say nothing, because a `compositeWithTrainedAlgorithmicMedia` edit records that generated content was combined with the photograph, not how much. The pictures answer it pair by pair.

What I see, at the sizes on the sheets. This is my reading by eye, for you to check against the sheets:

- **Close to the original: cleaned, retoned, sharpened or upscaled, the face the same face.**
  - Of the 14: Perkins #27381, lw2529 #31395, Kevin Gary Lynch #31404, Manly #31406 (his signature removed), rr1 #31423 (recoloured), sc9010 #31427, sc9501 #31449, lw2178 #31472 (the signature and the vignette removed).
  - Couts #27387.
  - Of the seven: sc9612 #31451, Brathwaite #31445, lw9501 #31429, Nadeau and Chrisman #31425, ap2222 #31389, lw2452 #31393 (sharpened hard).
- **The face drawn again in detail the original does not hold.**
  - **#27383 Scofield.** Most clearly. The enhanced face wears round wire spectacles; in the original I see none, or rimless ones. The face, collar and jacket are re-rendered as a modern studio photograph.
  - **#31387 ap1334 (#20224).** Just as clearly. The original is a faint, soft photograph. The enhanced is a sharp, younger-looking man whose hair, brow and face shape differ. If I did not know it was made from the original, I would not say it is the same person.
  - **#31391 lw2054, #31398 rn3002, #31408 lw2317a, #31402 tf1000.** Recognisably the same person, but each face is drawn at a resolution the original does not have. rn3002 and lw2317a are tight crops of small faces in full-length photographs, enlarged into head-and-shoulders portraits.
- **Something added outside the photograph:** **#31447 Keith Richman.** The original is cropped at the collar. The enhanced shows a jacket, an open shirt collar and a patterned tie, painted in by the two Generate Fills ("edges filled").

So, as evidence:
- **The manifests:** no group of chains starts from nothing.
- **The pictures:** the line between an alteration of what the photograph shows and a generated element does not fall at the chain's root. It falls inside the Image 5 edit, pair by pair. Some edits altered tone and detail; at least two replaced the face.

## Couts (item 2 of your brief)

- **His chain opens a real original.** The one ingredient is a JPEG brought in from outside the chain. The Image 5 edit acts on that file.
- **The pictures agree.** #12 and #27387 side by side are the same daguerreotype, the edit lightening the vignette and the tones.
- **He is back as an enhanced pair:**
  - `couts_pair_back_2026_10_09.php`, snapshot before-couts-pair-back-2026-10-09;
  - #27387 his portrait, enhancedFrom #12, #12 among his related images;
  - the credential line says what the chain opens.
- **The note on the asset says it was taken off on a ruling made before its manifest was read.** As you asked: the morning ruling was made on incomplete evidence, the overnight report's claim that Couts alone carried the step.

## How the parser made the false split (item 3)

- **Three readings of the same 22 credentials, one after another, each built on the last:**
  - `scan_content_credentials.py` (1 and 6 October) summarised each chain into the asset's contentCredentials field;
  - `restore_enhanced_pairs_2026_10_08.php` (8 October) sorted on those summaries;
  - the overnight scan (8 to 9 October) used the same labelling.
- **How the labeller worked.** For each action it searched the next 700 bytes of the manifest for two patterns: an `operation` parameter, or a software agent's `name`. It used the operation if it found one, else the name.
  - The manifest is CBOR, which does not fix the order of a map's keys, and the operation regex was written against one key order.
  - On some manifests it matched and the step was summarised "(text_to_image)". On the rest it missed and the step was summarised "(Firefly Image 5)".
  - The underlying step was the same in all of them.
- **What rested on the label:**
  - the seven were held as "text-prompt" portraits (6 October onward);
  - the seventeen were restored as enhanced pairs (8 October);
  - the overnight scan singled out Couts (9 October);
  - your Couts ruling followed (this morning).
  - Every portrait decision about Firefly edits this week used this split, and the split came from the labeller, not from the manifests.
- **The sixth instance of the pattern this week:** a summary written into a record, read in place of the source it summarised. ERRORLOG has it as its own row.
- **The fix:**
  - `manifest_steps_2026_10_09.py` reads every key as a CBOR value;
  - `_generated_scan.php` opens the files themselves;
  - neither writes a summary that a later step could sort on.

## Where things stand

- **Nothing was applied to the fourteen or the seven.**
- **check_generated_files.php lists all fifteen on records as held:** the fourteen, plus Couts with his reason.
- **The sheets are in inventory/review/text-to-image-pairs-2026-10-09/.**
