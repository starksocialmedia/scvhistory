# War Memorial section

Nathan decision, 2026-09-15: War Memorial is a separate section. Person is for important figures and authors only. Do not use Persons for casualty honor records. Military Profile merge into Person is unchanged and is not this job. Do not import these records into `militaryProfiles`.

No HTML import of the 36 profiles in this session. Schema only, local DDEV.

## Section

| | |
| --- | --- |
| Section handle | `warMemorials` |
| Section type | channel |
| Entry type handle | `warMemorial` |
| Entry URI | `war-memorial/{slug}` |
| Index | `/war-memorial` via `templates/war-memorial/index.twig` |
| Entry template | `templates/war-memorial/_entry.twig` |

One entry per inventory war-memorial **profile** (36). The four indexes are the section index, not entries:

- `warmemorial/home.htm`
- `warmemorial/ww2casualties-index.htm`
- `warmemorial/koreacasualties-index.htm`
- `warmemorial/terrorcasualties-index.htm`

Those four 301 to `/war-memorial`.

## Fields

Reuse existing: `title`, `featuredImage`, `body`, `deathDate`, `burialPlace`, `legacyUrl`, `sourcePath`, `legacyHtml`, `legacyKey`.

New on this type only (do not reuse Military Profile `mp*` handles):

| Handle | Type | Required | Notes |
| --- | --- | --- | --- |
| wmBranch | Plain Text | no | U.S. Army, U.S. Navy, and so on |
| wmRank | Plain Text | no | |
| wmUnit | Plain Text | no | |
| wmConflict | Plain Text | no | World War II, Korean War, War on Terror |
| wmHomeOfRecord | Plain Text | no | As written on the page |
| wmRelatedPerson | Entries, Persons, max 1 | no | Only if that person is independently in Persons as a figure or author. Leave empty for honor records |

Do not set `personGraveUrl` here. Do not put casualty honor records in Persons.

## 301 table

Indexes:

| Legacy path | Craft URI |
| --- | --- |
| `/warmemorial/home.htm` | `/war-memorial` |
| `/warmemorial/ww2casualties-index.htm` | `/war-memorial` |
| `/warmemorial/koreacasualties-index.htm` | `/war-memorial` |
| `/warmemorial/terrorcasualties-index.htm` | `/war-memorial` |

Profiles (slug from filename):

| Legacy path | Craft URI |
| --- | --- |
| `/warmemorial/korea_albertthomas.htm` | `/war-memorial/korea-albertthomas` |
| `/warmemorial/korea_donaldmorissett.htm` | `/war-memorial/korea-donaldmorissett` |
| `/warmemorial/korea_gilbertmontenegro.htm` | `/war-memorial/korea-gilbertmontenegro` |
| `/warmemorial/korea_henryacuna.htm` | `/war-memorial/korea-henryacuna` |
| `/warmemorial/korea_raymondkelly.htm` | `/war-memorial/korea-raymondkelly` |
| `/warmemorial/korea_robertwhisler.htm` | `/war-memorial/korea-robertwhisler` |
| `/warmemorial/terror_brianprosser.htm` | `/war-memorial/terror-brianprosser` |
| `/warmemorial/terror_colelarsen.htm` | `/war-memorial/terror-colelarsen` |
| `/warmemorial/terror_deantodd.htm` | `/war-memorial/terror-deantodd` |
| `/warmemorial/terror_dennissellen.htm` | `/war-memorial/terror-dennissellen` |
| `/warmemorial/terror_iangelig.htm` | `/war-memorial/terror-iangelig` |
| `/warmemorial/terror_jakesuter.htm` | `/war-memorial/terror-jakesuter` |
| `/warmemorial/terror_johnconant.htm` | `/war-memorial/terror-johnconant` |
| `/warmemorial/terror_josefloresmejia.htm` | `/war-memorial/terror-josefloresmejia` |
| `/warmemorial/terror_richardslocum.htm` | `/war-memorial/terror-richardslocum` |
| `/warmemorial/terror_robertwilson.htm` | `/war-memorial/terror-robertwilson` |
| `/warmemorial/terror_rudyacosta.htm` | `/war-memorial/terror-rudyacosta` |
| `/warmemorial/terror_stephencolley.htm` | `/war-memorial/terror-stephencolley` |
| `/warmemorial/ww2_albertmoore.htm` | `/war-memorial/ww2-albertmoore` |
| `/warmemorial/ww2_archibaldbeall.htm` | `/war-memorial/ww2-archibaldbeall` |
| `/warmemorial/ww2_augustrubel.htm` | `/war-memorial/ww2-augustrubel` |
| `/warmemorial/ww2_edwardcontreras.htm` | `/war-memorial/ww2-edwardcontreras` |
| `/warmemorial/ww2_ekenaston.htm` | `/war-memorial/ww2-ekenaston` |
| `/warmemorial/ww2_eugenedarr.htm` | `/war-memorial/ww2-eugenedarr` |
| `/warmemorial/ww2_frankwhitmore.htm` | `/war-memorial/ww2-frankwhitmore` |
| `/warmemorial/ww2_garrywingfield.htm` | `/war-memorial/ww2-garrywingfield` |
| `/warmemorial/ww2_jackharland.htm` | `/war-memorial/ww2-jackharland` |
| `/warmemorial/ww2_jamesredman.htm` | `/war-memorial/ww2-jamesredman` |
| `/warmemorial/ww2_jimbartlett.htm` | `/war-memorial/ww2-jimbartlett` |
| `/warmemorial/ww2_johnnycordova.htm` | `/war-memorial/ww2-johnnycordova` |
| `/warmemorial/ww2_johnward.htm` | `/war-memorial/ww2-johnward` |
| `/warmemorial/ww2_leoncherry.htm` | `/war-memorial/ww2-leoncherry` |
| `/warmemorial/ww2_ozalsmart.htm` | `/war-memorial/ww2-ozalsmart` |
| `/warmemorial/ww2_robertcone.htm` | `/war-memorial/ww2-robertcone` |
| `/warmemorial/ww2_robertfose.htm` | `/war-memorial/ww2-robertfose` |
| `/warmemorial/ww2_tomross.htm` | `/war-memorial/ww2-tomross` |

## Person slugs to move later (do not delete this session)

These 25 Person entries were created from war-memorial HTML in the Task 4 pilot. They should become War Memorial entries after Nathan approves, then be removed from Persons. They stay in Craft for now.

| Person slug | Legacy path |
| --- | --- |
| albert-edward-thomas | warmemorial/korea_albertthomas.htm |
| donald-e-morissett | warmemorial/korea_donaldmorissett.htm |
| gilbert-d-montenegro | warmemorial/korea_gilbertmontenegro.htm |
| henry-acuna | warmemorial/korea_henryacuna.htm |
| raymond-gene-kelly | warmemorial/korea_raymondkelly.htm |
| robert-l-whisler | warmemorial/korea_robertwhisler.htm |
| brian-cody-prosser | warmemorial/terror_brianprosser.htm |
| cole-william-larsen | warmemorial/terror_colelarsen.htm |
| dean-glenn-todd-jr | warmemorial/terror_deantodd.htm |
| dennis-lee-sellen-jr | warmemorial/terror_dennissellen.htm |
| ian-timothy-d-gelig | warmemorial/terror_iangelig.htm |
| jake-william-suter | warmemorial/terror_jakesuter.htm |
| john-michael-conant | warmemorial/terror_johnconant.htm |
| jose-ricardo-flores-mejia | warmemorial/terror_josefloresmejia.htm |
| richard-patrick-slocum | warmemorial/terror_richardslocum.htm |
| robert-michael-wilson | warmemorial/terror_robertwilson.htm |
| rudy-alexander-acosta | warmemorial/terror_rudyacosta.htm |
| stephen-edward-colley | warmemorial/terror_stephencolley.htm |
| albert-lee-moore | warmemorial/ww2_albertmoore.htm |
| archibald-k-archie-beall | warmemorial/ww2_archibaldbeall.htm |
| augustus-a-august-rubel | warmemorial/ww2_augustrubel.htm |
| edward-d-contreras | warmemorial/ww2_edwardcontreras.htm |
| lawrence-e-kenaston | warmemorial/ww2_ekenaston.htm |
| eugene-e-darr | warmemorial/ww2_eugenedarr.htm |
| frank-pike-whitmore | warmemorial/ww2_frankwhitmore.htm |

Eleven profiles were not imported as Persons (John Ward, Johnny Cordova, and the remaining WWII names). They still belong in this section on a later HTML import.
