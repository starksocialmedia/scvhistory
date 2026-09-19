SCVHistory.com — Changelog

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: rebuilt templates/articles/_entry.twig from design/article-source.html, with the tools row and player reworked to match and the fix button restyled. Report at web/review/article-design-reproduction.md.
- Decisions: inline styles kept rather than translated to classes, because that is how site-header.twig reproduces menu-source.html and the design file says to reproduce the inline styles precisely. The lead falls back from subheadline to the first paragraph promoted, and where it is promoted the body starts at the second paragraph so the same words are not read twice. The cite box keeps its existing partial rather than being rebuilt to the design's markup, because its JavaScript is shared with eleven other record templates that are not being redesigned.
- Blockers: none. The account menu and the Save button are left out entirely as placeholders.
- Result: the design's own illustrative numbers come out as live values on chapter 9: YOU ARE ON 11 OF 80, 14%, 11 items, the gap labels 4 earlier articles and 63 more articles, and the legacy path. Article column 760px, sidebar 344px, body 595px at 34em. Listen toggles the player and its aria-expanded follows. check_render.php reports no failures.
- Next: whether the cite box should be forked to the design's markup or the shared partial changed for every record type

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: reconciled the uncertainty report's vocabulary, applied the bracketed BACK rule, added editorNotes with its box rendering, moved the listen player below the cite box on articles, and taught export_article_links.php to keep a reference whose target we do not hold. Full report at web/review/chapter-review-findings.md.
- Decisions: the uncertainty report names what it found, so "a short trailing run of non-sentence lines" now reads "CONTAINING 1 image token". Bracketed BACK is the one rule that reaches past the head and the tail, and the header says so. editorNotes and the two webmaster fields coexist rather than migrating, because the extraction records a position and never a heading, so every migrated row would arrive untitled.
- Blockers: two, both reported rather than worked around. The three scan credits cannot move to creditRaw because no entry type but photograph has that field, so it is a schema decision. And chapter 6's cross-references are not in links_out at all, so the exporter cannot reach them; that is a gap for Grok.
- Result: the fused caption is not in our data. Chapter 6 renders 9 paragraphs, longest 100 words, and "The Legend of Califa" is not in the stored body; what is being looked at is staging, whose database predates this session's cleaning. 13 lines of 300 or more words do exist, almost all in the new photographs. 1650-calif-map.jpg is asset #42, downloaded and attached to nothing, and 25 of 568 assets are in that state. Bracketed BACK removed from 40 records, 84 lines on the two notes pages alone. 100 legacy references from 41 records, chapter 9's two among them. webmasterNoteTop is used by 0 records and webmasterNoteBottom by 65; nine pages in the corpus carry more than one annotation.
- Next: Nathan runs add_editor_notes_field.php, decides the scan credit question, and the attach step of import_legacy_images.php needs looking at

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: elevated the signature and moved the rights line out of the record body into the site footer.
- Decisions: the fine print rendering was not removed wholesale, because finePrint does not only hold the site-wide rights line. 51 of the 57 non-empty values are that line and are now suppressed; the other six still print, and two of those six are rights held by somebody else, A.B. Perkins and the Historical Society of Southern California, which the footer's line does not cover and must not be taken to cover. The other four are a credit to Stan Walker and three scan lines that belong in creditRaw and have not moved yet. So the partial suppresses one known shape rather than the field.
- Blockers: none.
- Result: the signature is Playfair at the body's 17px, navy, small caps, with 52px above it instead of 34. The footer carries the rights line once with the year derived, 2026 rather than 1998. The stored value is untouched, so DC.rights and the JSON-LD still read it. Verified on four records: the Preface prints nothing in the body, the Pico Ghost Camp keeps the Perkins rights, Henry Clay Wiley keeps the Stan Walker credit, and Tiburcio Vasquez keeps its scan line. check_render.php reports no failures.
- Next: the three scan lines sitting in finePrint want moving to creditRaw

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: imported the 1,544 LW features and ran the second clean. Did not apply three of the four cleaning decisions, because the lines they name are not what my report implied.
- Decisions: the import ran because it is independent of those four article records. On the four: 39-ribbons-of-steel stays, as decided. The other three I have left alone and reported instead. In notes and editors-notes, BACK is navigation as decided, but it is not a trailing line: editors-notes carries 34 of them, one after every numbered note, so the tail walk would strip the last and leave 33. Stripping them all means removing from the middle of the prose, which is the rule the cleaner is built on, and that is a decision rather than an implementation detail. On the other two, the trailing runs contain image tokens: the parade record's run of three holds [image:1] and the record has one related image, and rancho-san-francisco's run of six holds [image:2], [image:1] and a footnote marker across three lines, against two related images. Stripping either orphans the pictures.
- Blockers: none for the import. The three cleaning cases are waiting on a second look.
- Result: 262 entries before, 1,806 after, photographs 0 to 1,544, none failed. The second clean changed all 1,544: 1,699 breadcrumbs, 1,427 gallery caption runs, 270 repeated titles, 64 block titles, 56 block publications, 42 bracket-nav, 42 bylines, 9 datelines, one copyright. A third run changes nothing and reports 1,702 clean. 282 records carry something the walk stopped at, which is the residue by design. check_render.php covers 27 pages including a photograph and reports no failures.
- Next: Nathan re-decides the three cleaning cases now that the lines are visible, and the images import needs the drive bound into DDEV

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: reworked the reading player into one tools block, and built export_fixes.php and import_fixes.php. Diagnosed the fix button: the condition and the include are both correct and it renders locally on every page type.
- Decisions: the player keeps every DOM hook the script uses, so not a line of the JavaScript changed; only the markup around it and the CSS. The import matches on the note text, because ids are not stable across two databases and the note is what a person typed. A note already present locally is left completely alone including its status: if it was marked done here and is still open in the export, the local judgement is the later one and the import has no business overruling it. Exported records are identified by legacy URL and slug rather than id, for the same reason.
- Blockers: none, but one thing could not be verified. Clicking play does not start speech in this browser, with a real mouse click or a synthetic one. The committed version behaves identically, so it is the environment and not the rework, but audio itself is unverified. Everything observable was checked: the seek bar, the arrow keys, the four speeds and the estimate recalculating.
- Result: the button question is answered. The condition tests four things, all true here, and base.twig includes the partial unconditionally before the foot block, so it renders on the home page, the indexes, search, community pages and record pages alike; record pages additionally attach fixRecord. The player is one row at full width, 50px tall against 118px for the whole tools block, the bar is 10px with a cream track and a gold fill, the speeds are one segmented pill and the times are in the mono face inside the row. The round trip was tested with two fixtures: one deleted and recreated, one marked done locally and left alone, second run created nothing. check_render.php reports no failures.
- Next: Nathan pulls on staging, which is where the fix button is missing, and says go on the five unresolved cleaning cases so the LW import can run

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: guarded the six unlisted pages in the template and taught check_render.php to assert the guard. PUBLISHED BY became the author treatment, in a partial used by articles, collection entries and the lander. Documented the exact Nginx block and where it goes on Cloudways. Took the cleaned lw-features.json from grok-bot. Stopped before the LW import, as instructed.
- Decisions: the guard is in the code rather than in the server config, so a seventh unlisted page that forgets it is a missing line in a diff rather than an open URL. check_render now treats a guarded page as passing on 302, 403 or 404 and failing on 200 or 500, so a template that loses its guard fails the standing check. PUBLISHED BY has no field saying what an organization is, the way a person has occupation, so the descriptor falls back to the body's first sentence, then the record tags, then nothing; an orgType field would fix that and is a decision rather than a template change.
- Blockers: the import is stopped at the gate. The first clean reports 0 records to change and 158 already clean, but nine records sit under "not confident". Four of those nine are the cleaner correctly declining to touch the intended layers, the [image:N] tokens and a footnote; five are genuinely unresolved. The gate says stop on anything, so it stopped. Also: the fix list has no open items anywhere I can reach. It is empty locally, and /admin-fixes on staging is 401 behind the basic auth with the templates not yet pulled there, so I could not read notes that may exist.
- Result: the five sidebar and body changes were done last turn and are unchanged. The cleaned extraction moves the leaked prose in source_note_raw from 204 pages to 32; the import would create 1,544 records and hold back 117 with no title of their own. check_render.php covers 26 pages including all six guards and reports no failures.
- Next: Nathan says go on the five unresolved cleaning cases, then the import runs; and pulls on staging so the fix list is readable

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: five changes from the staging review. The collection sidebar shows neighbours rather than the whole series; the legacy link moved to the top of every record sidebar; the signature block is a cleaner pattern and the cleaner has been applied. The band chain and the rights line were checks, and both came back clean.
- Decisions: the signature pattern requires the year. Without it, a short line of capitals at the end of a body matches "MANY MORE" and "SEE HI JOLLY'S TOMB" across the LW features and Worden, which are navigation; with it, it matches two pages in 2,036 and both are real. The name and year feed the existing byline machinery, so writtenBy and originalPublishDate get filled where they were empty and the signature renders from the record's own fields, which is where ar-signoff was already reading it from. The legacy link was moved rather than copied: it is gone from the EXTERNAL box in eleven templates.
- Blockers: none, and two of the five were already right. The band chain is intact: no article carries its own bandImage, 93 of 103 inherit one from their collection and the other 10 are in no collection, so production is empty only because two PNG files have not been deployed. And the SCVTV rights line is in finePrint on one record and in no body anywhere in the corpus; what shows on the page is finePrint rendering directly under the last paragraph, which reads as part of the article because nothing separates them.
- Result: the Reynolds sidebar went from 80 entries to at most 13. Two signatures stripped, both rendering once now. 23 records changed in the cleaner run, 0 would change on a second, 158 clean. check_render.php reports no failures.
- Next: Nathan deploys the two band PNGs, makes the two record edits he named, and says whether the fine print wants separating from the prose

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: the fix list, proposed as a section rather than a field, with add_fixes_section.php, a capture form in the footer of every page and the list at /admin-fixes. And export_correspondence.php, which writes the legacy-to-archive correspondence as CSV. Proposal at web/review/fix-list-proposal.md.
- Decisions: a field cannot hold a note about a record that does not exist, and 5,606 of the 5,791 legacy pages have no record, so most of what there is to say is out of a field's reach. It also holds one note, needs a second field for a done flag, and can only be edited in the control panel, which is the opposite of noting something in five seconds while reading. So one entry per note, with the record and the legacy URL both optional. The capture form posts to Craft's own entries/save-entry rather than an endpoint of ours, so there is no module. The CSV is regenerated on demand and gitignored: a stored copy of "what is in this archive and where did it come from" would be wrong within a day, and a wrong answer to that question is worse than none.
- Blockers: none, but two things to say plainly. The capture form is not tested end to end, because that needs the section to exist and creating it is a project config change that is Nathan's to run. And a note written while browsing staging is content, so it is lost at the next content refresh; the proposal sets out three ways round that and recommends the export-before-refresh one.
- Result: the CSV is 5,857 rows and twenty columns: 5,791 legacy pages plus 66 records with no legacy page of their own. 5,606 have no record, 185 are imported and unchecked. author, date and publication are only carried by the article, collection and photograph types, so a has_byline_fields column says whether a blank means no field or an empty field. body_cleaned is detected rather than stored. check_render.php covers /admin-fixes and reports no failures.
- Next: Nathan runs add_fixes_section.php, tries the button, and decides how fixes survive a content refresh

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: both pre-import fixes, the augustrubel repair, the pre-import audit at web/review/pre-import-audit.md, and the runbook rewritten around the real deployment with a measured exposure report.
- Decisions: the cleaner's matchers moved to _legacy_chrome_matchers.php so a dry run against an inventory uses the same code as a real run against the database. The inventory dry run loads pages into unsaved entries of the target type; they need a sectionId as well as a typeId or the field layout comes back null, every body reads as empty and the pass cheerfully reports that 1,661 pages are clean. #518 is fixed by setting its body from wmNotes rather than by the cleaner, because stripping its furniture leaves nothing.
- Blockers: none, but a correction I owe. The mid-line breadcrumb count of 317 was wrong: the regex I measured with let \s match a newline, so it was counting ordinary line-start breadcrumbs whose previous line ended in a non-space. Measured within a line the count is zero in every inventory. The rule is still in, as a guard rather than a repair, and its first version ate "Augustus" off a name, so segments are now bounded by a following marker and it cannot touch prose.
- Result: all 1,661 LW bodies would arrive dirty, none clean, 1,170,682 characters of chrome, 1,699 breadcrumbs and 1,427 gallery caption runs. 345 bodies have something the walk stops at. On the live staging site the webroot is right and nothing outside web/ is served, but six things are open: ledger-index.json at 1.5 MB, audit.json, the five review screens, /admin-overview, /graph and /admin-ledger with both data endpoints. The fidelity files are absent only because they are gitignored and the server has not pulled the commit carrying the summary; they arrive public on the next pull.
- Next: Nathan blocks /review/ before the next pull, adds the template guards, then cleans, imports the LW features and cleans again

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: investigated the 79 added lines against inventory/wp_content.json. Full report at web/review/fidelity-investigation.md.
- Decisions: the test matches a record to its WordPress post by slug and compares on words alone against the whole WP body flattened, because WordPress wraps paragraphs differently from the extraction; a line over fourteen words is probed on its first fourteen so a lightly edited line still matches. Reports go to a file from now on, at Nathan's request, with a short summary in the terminal.
- Blockers: none.
- Result: both answers are true, and the split is 71 lines to 8. 71 of 79 are in the WordPress body, across 21 records, so they are content written in WordPress before the Craft migration and the question is who wrote them. The remaining 8 are on three war memorials that have no WordPress post at all, so the test is inconclusive rather than damning, and reading them settles it: Rudy Acosta's six lines are a composed biography and Robert Cone's one line is an editor's note, both content; Augustus Rubel's one line is the legacy navigation trail sitting in the body, and that is the bug. It survived because clean_legacy_bodies.php matches a breadcrumb with ^> at the start of a line, and this trail sits after the page title on the same line. The census misses it for the same reason. 317 of the 1,661 LW pages carry that shape, and 1,340 carry the catchable one, but the larger point is that clean_legacy_bodies.php runs over articles, warMemorials and obituaries only, and the LW features import as photographs.
- Next: Nathan decides who wrote the WordPress additions, and whether the cleaner covers photographs before the 1,544 import

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: ran the fidelity audit across all four inventories, 373 pages, 38 files. Reported the corpus-wide count of text present in the record and absent from the page it came from.
- Decisions: the 38 text files are gitignored and the summary JSON is committed. 3.3 MB of generated text regenerated on every corpus change is churn a repository should not carry, and the finding is in the summary; the files themselves are on disk and over HTTP for the reviewer. Easy to reverse if they should be committed after all.
- Blockers: none, but the corpus is only two thirds tested. All 219 Worden pages have no Craft record, so B is empty for every one of them and they can contribute nothing to this count. Whatever is happening in Reynolds cannot be ruled in or out for Worden until those pages are imported.
- Result: 24 records of the 154 that have one carry text in B and not in A: 79 lines, 4,495 words. The distribution is not scattered. 20 are Reynolds chapters, and they are 20 of the 23 pages that appear in both Reynolds extractions; the three clean ones are part01, part02 and part03, the first three chapters. The other four are three war memorials and the Perkins page found last time. Checked that this is not an artefact of comparing against the wrong extraction: reynolds.json and reynolds-full.json differ by fifteen to twenty five characters per page, whitespace, so the text is in neither.
- Next: Nathan decides what to do about the Reynolds chapters, and whether Worden imports before or after that

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: export_fidelity_audit.php writes side-by-side audit files for a reviewer with no access to either site. One plain text file per batch of ten articles: the legacy URL, the fields set from the page, then the extracted body_text and the stored Craft body, both verbatim. First ten Perkins articles written to web/review/fidelity/.
- Decisions: /mnt/user-data/outputs does not exist on this machine, so the files go to web/review/fidelity/, which is where every other review artefact lives and is downloadable over HTTP. It is inside the directory the deployment runbook blocks in production, which is correct: these files are for a named reviewer, not the public. Each pair carries a line-level comparison, on words alone with case, spacing and punctuation ignored, because the import changes all three on purpose. The comparison counts and lists in both directions. Lines only in A are mostly furniture removed by design; lines only in B are the direction worth reading, because the import adds almost nothing.
- Blockers: none.
- Result: one file, 588 KB, ten articles, every one of them matched to a record. Nine of the ten lose two to four lines, all of them the series heading and the repeated headline. Rancho San Francisco 1957 loses 72, all of them the caption list under the thumbnail rail. The Birth of Newhall (Cont.) is the one that goes the other way: two sentences of real prose are in the record and not on the legacy page, about Mill Canyon and an 1876 Ventura Free Press report, and that wants a human. Also visible across the batch: originalPublishDate and publishedBy are empty on nine of ten, and subheadline on all ten.
- Next: Nathan reads the file, decides on the two Mill Canyon sentences, and says whether the remaining ten Perkins pages and the other inventories follow

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: taught import_legacy_images.php the drive and the gallery rail. It resolves every file against Reggie first and reports each fall back to the network; it skips a rail thumbnail whose links_to is a page, follows one whose links_to is a picture, and keeps one with no link at all. Scoped to lw-features-images, batched and resumable as before.
- Decisions: the rail rule is scoped to the inventory it was written for. Applied to the three earlier inventories it skipped 286 files that are already in the volume and were judged content at the time under the chrome rules, and a new rule belongs to the data it was written for. The drive guard stops an apply run when the mirror is unreadable but lets a dry run plan, because a dry run fetches nothing and refusing to plan is no protection. $DRIVE is a list of candidates now, the host path and the container path, so the same script works run either way.
- Blockers: two, and they are separate. The DDEV container has no /Volumes at all, so nothing run through ddev can see the drive however the Mac is configured; it needs a bind mount in .ddev/docker-compose.drive.yaml and the script prints the four lines to write. And the host shell cannot read /Volumes/Reggie either: it answers stat and mount but refuses to be listed, which is macOS withholding a removable volume and needs Full Disk Access. Until one of those is fixed nothing can be read from the mirror.
- Result: a correction. links_to on the rail does not point at the full-size picture. On 25,430 of 25,992 thumbnail references it points at a page and on 83 more at an .html; only 23 distinct thumbnails link to an image. The skip is right and the reason given for it is not, and the full-size versions of 2,145 rail thumbnails are in no inventory we hold. The run also found that only 98 of the 1,661 LW pages have a Craft record, so 15 images are placeable today: the page import has to run first.
- Next: bind the drive into DDEV or run on the host, run import_lw_features.php, then this

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: ran the mechanical pass. clean_text_mechanical.php fixed 1,375 things across 57 records and 71 fields, with a backup taken first and $APPLY returned to false in the commit. Added batch mode and a resume ledger to import_legacy_images.php. Reported the 126 non-community title topics. Settled source_note_raw to photoCredit.
- Decisions: the pass is licensed to make five named character substitutions and any amount of whitespace change, and nothing else, so the invariant is that the text with whitespace stripped must equal the original with those five applied and whitespace stripped. Every field is checked against it and against the counts of the six intended patterns before it is saved. Nothing was refused, and after the run em dashes still read 703, image tokens 80, footnote markers 175, inline markup 102, sentence gaps 61 and editor brackets 8, all unchanged. The blank-line rule was tightened before applying: my first draft collapsed two blank lines as well as three, which is 505 edits rather than the 102 the census advertised, and staying inside what was authorised matters more than the extra 403.
- Blockers: none. The rights line turned out to hold four characters in one slot, not three: a middle dot on 28 records, a bullet on 7, a soft hyphen on 6 and a vertical bar on 2. All four are now the middle dot. The soft hyphens had to be normalised before the generic soft hyphen rule ran, or six lines would have lost their separator and kept the spaces around it.
- Result: the mechanical class now reads zero. The 18 review records are untouched. The image importer was tested against a scratch fixture of 12 images and a seeded ledger: 6 settled, 1 retrying, 5 fetched, 1 deferred, 2 pages held whole. Of the 1,661 LW pages, 1,461 carry a topic, 697 of which name a community and 764 do not; six topics have 20 pages or more and cover 43% of them, led by William S. Hart at 106.
- Next: Nathan reads the 18 review records, decides on the six collection candidates, and says when the entity canon is ready for the LW relations

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: wrote docs/DEPLOY-RUNBOOK.md, the Cloudways deployment runbook, not executed. Wrote census_text_quality.php, which reads every stored body once and sorts what it finds into classes with a verdict on each. Fixed the ledger's duplicate line to name both records. Pushed templates-batch-9; the ledger work was not on origin.
- Decisions: the runbook opens with the webroot rather than the .env, because Cloudways points a new application at public_html and Craft's document root is web/. Left alone, .env, storage, vendor, config and every import script are served over HTTP, and nothing else in the runbook matters. The unlisted pages are blocked in the template with requireLogin and an admin check rather than by path in the server config, so a page added later that forgets the guard is a visible omission in a diff instead of an invisible one in a file nobody opens. The census carries four verdicts and the useful one is "intended": [image:2] is the illustration layer, <em> is the emphasis layer and the 703 em dashes are Reynolds writing, and a naive cleanup pass destroys all three.
- Blockers: none, and a correction. The two records claiming /scvhistory/lw2102.htm are not duplicates of each other: #950 is Walk of Western Stars Inductees, a group, and #396 is the Santa Clarita Valley Chamber of Commerce, an organization. My ledger line printed only the second title, which made them read as one name twice. Neither record should go; one of them should stop claiming the page.
- Result: the runbook covers the webroot, eleven .env keys, rsync then database then code with what breaks if reversed, a verification after each step, five things that must not be public, four rollback paths and the standing rule. The census reads 958,010 characters across 353 text fields on 262 records: 1,366 mechanical hits one pass removes with no judgement, 18 records in the review queue, and five classes marked intended that a pass must not touch. The 15 scvleon.com images sit on 15 Worden pages, 13 of them under signal/worden/old/, a 170-page subtree that is extracted and entirely unimported. check_render.php reports no failures.
- Next: Nathan follows the runbook by hand, decides which record keeps lw2102.htm, and says whether the mechanical classes go in one pass
- Late addition: inventory/legacy/lw-features.json landed mid-session, 1,661 pages, so import_lw_features.php ran its real dry run and four of my predicted mappings were wrong. The scan credit opens with the accession code and a colon on every line. The bar is rare, 62 of 1,525, not the norm. title_topic is already split out by the extraction and 126 of its values are places, people and events rather than communities. And source_note_raw holds two different things, of which 204 are body prose the extraction caught by mistake. Relations are now off by default: exact title matching catches 1,339 of 29,842 person mentions, 263 of 2,325 places and 0 of 3,378 organizations, and the person list includes "Hart Films" and "Drone Video". 1,544 pages would import, 117 held back for having no title but the site default, and 35 disagree with themselves about their own accession code.

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: the Ruiz census split, as briefed. add_grave_census_field.php creates graveCensus on the place type, eleven columns with the dates in printed/parsed pairs; import_ruiz_census.php parses the census, plans 64 table rows and 12 person records, and appends the provenance note to the place. Extended export_article_links.php into a reading list: the top 40 pages the writers link to and the archive does not hold, each with the articles that point at it and the words they used. Wrote import_lw_features.php against the lw-features schema, which reports its own field map and exercises both parsers because the file does not exist yet.
- Decisions: a date is parsed only where it can be read without guessing. Spaces inside a number are closed up, because that invents nothing; a day of 133 is not repaired, a two-digit year gets no century, and a row with one date is not assigned to birth or death by feel. Four rows are left unparsed on purpose and each keeps its printed value. Two people get a death date the census could not give, from Worden, and it is labelled as his. A month word only starts a date if it is abbreviated with a stop or is one of the full forms that actually occur, because plot 49 reads "Lebrun August 1884 1924" and a naive rule ate the man's name and both his dates. Five comments marks sit on lines of their own with the plot they belong to lost, and they are reported rather than attached to whichever row is nearest.
- Blockers: none, but three corrections to my own earlier report. The census carries 64 numbered plots, not 58; 41 carry a name, not 42. Eight people are dated 13 March 1928, not seven: plot 07 holds two Erratchuos beside the six Ruiz, which is why Worden counts six of the family. And the YES column is not military service but a comments sheet attached, since plot 01 is an eight-year-old and plot 53 an infant and both are marked YES.
- Result: 64 table rows, 41 named, 4 unparsed on purpose. 12 person records planned, with spouseOf between Rosaria and Enrique from the census's own spouse column and childOf on the four children from Worden's "ages eight to thirty", whose arithmetic the census matches exactly. Seven more plots already pass the same test and are reported rather than created. 113 pages are linked to and not held, 165 links in all; the top of the list is Sulphur Springs School at seven. check_render.php reports 25 pages and no failures. Nothing was written.
- Next: Nathan runs add_grave_census_field.php then import_ruiz_census.php, settles source_note_raw for the lw importer, and decides on the seven extra census records

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: built the migration ledger. build_ledger_index.py condenses the two crawls, every extraction, every image inventory and the 96 MB drive manifest into web/review/ledger-index.json; /admin-ledger/data counts the Craft side at request time; /admin-ledger joins the two in the browser, filters by section, kind, record type and state, searches URL and title, and carries a per-state count at the top. Also added the ledger to check_render.php.
- Decisions: the ledger is split rather than computed whole. What is static, a crawl and a disk manifest, is built; what can go stale, everything about Craft, is counted on every load, so no number here can be left behind by an import that ran afterwards. The review flag is the one stored thing, in templates/_data/ledger-review.json, read server side so everyone sees the same flags, because no query can derive a judgement. The page's marks are held under a key of its own and saved down by hand, so a review screen can never delete another's work. Page furniture is set aside using the same rules import_legacy_images.php uses to skip it, which took "pictures not imported" from 8 to 3; the other five were the army seal on war memorial pages, correctly never downloaded. A thumbnail missing from the drive is reported apart from a picture that is gone, because the mirror holds al1890.jpg but not al1890t.jpg and the importer prefers the larger file anyway.
- Blockers: none. web/review/ledger-index.json is committed rather than ignored like the other generated review files, because inventory/raw is ignored and the drive manifest lives on one disk: without the built file nobody else can see the drive column at all. It is 1.6 MB and rebuilds on demand.
- Result: 5,791 legacy pages. 5,606 have no record, 185 are imported and unchecked, 221 were extracted and never imported, 1 body still carries legacy chrome, 3 pages are missing a picture the importer should have taken, 31 reference a picture on no drive, and 65 records are born-digital. 11 records point at a legacy page no crawl has seen and /scvhistory/lw2102.htm is claimed by two records; both are on the page. 581 content pictures: 527 on the drive where the page asks, 15 only at another size, 39 nowhere, 499 imported. The crawl stopped at its own 5,000 page limit and the page says so at the top rather than implying the archive is nearly done. check_render.php reports 25 pages and no failures.
- Next: Nathan reviews rows on /admin-ledger and saves ledger-review.json down, and decides whether the 39 pictures on no drive are worth re-fetching

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: import_loose_pages.php creates the Routledge account and the 1992 Ruiz census as articles, relates each to its place through depictsPlace, and extends the Newhall Ranch House hauntedSource to cite Routledge alongside Reynolds. Also built the article-to-article trio: export_article_links.php, /review/article-links.html and apply_article_links.php.
- Decisions: links_out carries href, anchor and a flag and no context at all, so the sentence is derived from the page body with the same abbreviation masking the married-name detector needed. Relative hrefs are resolved against the page they sit on; reading them literally loses 489 of the 1,329 links. Footnote markers are separated from cross-references, because a bare number pointing at the notes page is a different apparatus from a phrase pointing at the piece that explains it, and 49 of the 63 usable links are footnotes.
- Blockers: inventory/legacy/loose-pages.json is not on this branch. It is on origin/grok-bot at 676f000 and the import says so and gives the command. I rehearsed the run against a copy through a $SOURCE override rather than bringing the file across, since that is Nathan's branch to merge.
- Result: 2 articles would be created. 1,329 links yield 63 usable, 20 distinct pairs, 10 of them cross-references and 10 footnote pairs. The best is Chapter 18. The Pathfinder and Enemy Confirms Fremont's Trek Through SCV, which link to each other. 165 links point at legacy pages the archive does not hold, and the export lists the most-linked of those.
- Next: Nathan brings loose-pages.json across, runs the import, then reviews the article links

2026-09-19

- Agent: Claude Code
- Date: 2026-09-19
- Done: wrote fill_collection_stub_bodies.php, bodies for all eleven collection placeholders, on the same pattern and guard as the place fill. Each names the evidence it draws on and the counts are re-derived rather than remembered.
- Decisions: figures come from the crawl where the crawl is complete and from the index page where it is not, and the entry says which. sitemap.json stopped at its 5,000 page limit, so it reached 37 of the coins tree against 218 dated links on the index; using the crawl figure there would have understated the series by a factor of six. Date ranges come from the datestamp in each article's filename, which is the only per-article date the crawl carries.
- Blockers: the collections are not titled. title_collections.php has $APPLY = false and has not been run; ten of the eleven are still untitled in the database and only Newsmaker of the Week carries a name, which it already had. There is also a bug in that script: it sets a body only where the body is empty, and none of these is empty, so running it as written would set the titles and legacy URLs and leave every placeholder in place.
- Result: 11 bodies would be filled, 0 blocked. Worden is the longest at 596 characters, the Old Town Newhall Gazette the shortest real one at 203, and Newsmaker of the Week is 382 and says plainly that the archive holds almost none of the series. check_render.php reports no failures.
- Next: Nathan runs title_collections.php with the flag on, then this

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: wrote the remaining eleven place bodies into fill_place_stub_bodies.php, in the order given, each naming the articles it draws on. Also answered the placeholder question: the pattern is not on organizations, groups, events or persons, but a parallel one is on collections.
- Decisions: length follows the corpus rather than a house style. Rancho Camulos runs 1,274 characters because Reynolds gives it a chapter; Lake Hughes gets 160, one sentence, because one sentence is what Reynolds wrote. The Estancia entry carries the 2006 webmaster's note that modern archaeologists do not believe it was ever raised to asistencia status, because the archive's own correction belongs in the record rather than only in the article it corrects.
- Blockers: none. Three of my slugs were wrong, estancia rather than estancia-de-san-francisco-xavier, lang rather than lang-station, magic-mountain rather than six-flags-magic-mountain, and the dry run said so by leaving them in the still-placeholder list. The guard also blocked Heritage Junction, whose body Nathan has already applied, which is the guard doing its job.
- Result: 11 bodies would be filled, 1 blocked as already written. Eleven of thirteen collections carry the same generator's other placeholder, "X is a series in the Santa Clarita Valley historical archive", and all eleven are untitled collections. check_render.php reports no failures.
- Next: Nathan applies fill_place_stub_bodies.php, and decides on the collection placeholders, which need titles first

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: made the render check part of the standing workflow rather than a schema-partial special case, in the scv-record-template skill and in my own memory, together with pulling before reporting on tree state. Wrote fill_place_stub_bodies.php and filled the fourth of the four places: Heritage Junction Historic Park existed already but carried a generated placeholder body.
- Decisions: the fill script only ever overwrites a body that still matches the placeholder exactly, and the guard is the point of it rather than a precaution around it. The other eleven placeholders are listed rather than filled, because composing those paragraphs is reading and judgement; a script can refuse to invent them but cannot write them.
- Blockers: none, and a correction to my own last report. add_missing_places.php has been applied: Ruiz Cemetery #2536, Newhall Ranch House #2538 and Felton School #2540 all exist with their bodies, communities and haunted fields as briefed. I described them as pending.
- Result: twelve of eighteen places carried the placeholder body "X is a named place in the Santa Clarita Valley historical archive". One is now written, eleven are named. check_render.php reports no failures.
- Next: Nathan applies fill_place_stub_bodies.php, and decides whether the other eleven are worth writing

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: wrote scripts/import/add_missing_places.php, dry run by default. It creates three places rather than four: Heritage Junction already exists as "Heritage Junction Historic Park". Bodies are assembled from corpus sentences with the source named, communities come from the text or the legacy page titles, coordinates are left empty, and the haunted fields are set on Ruiz Cemetery and the Newhall Ranch House with the claim attributed rather than asserted.
- Decisions: legacyUrl is left empty on all three, which is a decision rather than an omission. legacyUrl means the page a record was migrated from; these are made from mentions inside other people's articles, and the legacy site has no page that stands for any of them. The candidates are listed in the script so the choice can be made rather than lost. Ruiz Cemetery is "reported" rather than "legend" because named witnesses describe specific incidents, and the owner's own disavowal is carried in the account.
- Blockers: the Newhall Ranch House has two locations in the sources. The legacy titles read both "Heritage Junction | Newhall Ranch House in Valencia" and "Newhall Ranch House (Original Location)", so the house stood on the ranch in what is now Valencia and was later moved to Heritage Junction in Newhall. I set Newhall, where it stands, and the community note on the record says so; one building across two communities is the succession problem again.
- Result: 3 records would be created, 0 already present. check_render.php reports no failures.
- Next: Nathan reads the passages, decides the legacy URLs and the Newhall Ranch House community, then applies

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: wrote the rule, and something for it to point at. scv-record-template gains the Twig-comment trap, the requirement to load one record page of every type after any edit to the schema partial, and a note naming the class of error a diff cannot catch. scripts/import/check_render.php makes that runnable: it clears the template cache, discovers one page per section, entry type and category group from Craft, and asserts on what the server sent. The review-screen and import skills point at the same lesson.
- Decisions: the check discovers its pages from Craft rather than from a list, because a list of URLs goes stale exactly when a new entry type is added, which is when it is most needed. It asserts on the body as well as the status, since a Twig error renders inside a 200 on some routes and a status code alone would pass it.
- Blockers: none. I proved the check by injecting the exact fault I had made twice, a Twig comment inside a hash literal in the Place branch: it failed 8 pages by name. Then restored the file and confirmed no failures. Building it also turned up a fourth instance of the same class in my own code: @web is empty on the command line, so every constructed URL failed to connect while the entry URLs passed.
- Result: 23 pages, 22 JSON-LD blocks, no failures.
- Next: Nathan commits the project config from the landmark field run

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: put the existing sourceLine field on the collection entry type and recovered "The Signal / Santa Clarita Valley Chamber of Commerce" onto the Reynolds collection from the WordPress export. Applied. The citation partial takes a source param and renders it in all three styles, and the JSON-LD emits it as the publisher of the series. Residence skipped as asked.
- Decisions: sourceLine outranks the linked publishedBy record on a collection, which is the only place in the schema partial where a text line beats a record. The two answer different questions: The Signal published the series, SCVHistory.com republished it. So sourceLine is the publisher, the linked record becomes the provider, and the archive is the publisher of last resort. The source sits ahead of the section label in a citation, because a reader is being told where the work first appeared and not only where this copy lives.
- Blockers: none, but I made the same mistake twice in one session. A Twig comment cannot sit inside a hash literal, and putting one there took every record page down with a syntax error, exactly as it had an hour earlier on the NRHP listing date. The comment now sits above the branch and says why it is there.
- Result: the lander citation reads "Reynolds, Jerry. 'History of the Santa Clarita Valley.' The Signal / Santa Clarita Valley Chamber of Commerce. Collections. SCVHistory.com." in Chicago, and the equivalent in MLA and APA. 38 URLs swept, none in error.
- Next: Nathan runs add_place_landmark_fields.php and recover_wp_landmarks.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: add_place_landmark_fields.php adds scvhlNumber, nrhpReference and nrhpListedDate to the place type, all plain text, beside placeChlNumber and gnisId. _partials/head/schema.twig reads all three: the NRHP reference goes into sameAs and identifier, the SCVHL number into identifier only, and the listed date is said as an award. recover_wp_landmarks.php walks the WordPress export onto the place records and then audits all 77 meta keys for anything with no home.
- Decisions: the SCVHL number is an identifier and not a sameAs, because there is no public register with a per-landmark URL and a sameAs that links nowhere is a claim the archive cannot keep. The listed date is an award rather than a date property on the Place, which would say the ground itself dates from 1971.
- Blockers: two corrections to the brief. placeScvhlCheckbox is not missing from Craft; it exists as a lightswitch named "Place SCV Landmark", on the Identity tab, alongside placeScvhlUrl. And all three place_scvhl_checkbox values in the export are "0", so nothing was dropped at import: the designation was never set in WordPress either, and Craft already matches it exactly. The import is still worth having, since it recovers two placeChlUrl values that genuinely did not land.
- Result: 2 fields would be set, both placeChlUrl. Of 77 meta keys with a value, 6 have no home: four are all-zero WordPress toggles, and two carry real content that was lost, residence "Valencia, Ca" on Leon Worden and source "The Signal / Santa Clarita Valley Chamber of Commerce" on the Reynolds collection.
- Next: Nathan runs add_place_landmark_fields.php then recover_wp_landmarks.php, and decides on residence and the collection source line

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: proposed place relationships from the coordinates, the community terms and the bodies. export_place_links.php writes web/review/place-links.json with the evidence for each pair, place-links.html is the review screen on the same pattern, apply_place_links.php writes the confirmed ones. Also wrote design/PLACE-SUCCESSION-PROPOSAL.md for precededBy and succeededBy, as a proposal rather than a build.
- Decisions: a mention outranks proximity and proximity outranks a shared community, because somebody writing it down is evidence and nearness is not. The distance is reported on every pair whatever proposed it, since "these two are named together and are sixty-eight kilometres apart" is exactly what a reviewer should see. The succession proposal recommends storing one direction only, succeededBy, and deriving precededBy, which is the childOf lesson applied again.
- Blockers: none. The review screen reads a suffixed localStorage key when loaded with ?test, so it can be driven without touching a real review.
- Result: 14 pairs from 105 possible. 2 by mention, 3 by proximity within 2 km, 11 by shared community. The two mention pairs are the best of them and neither is near: Beale's Cut and Lyons Station both name Fort Tejon, 68 and 66 km away, because that is the stage road rather than the neighbourhood.
- Next: Nathan decides the 14 pairs, then apply_place_links.php, then re-render /graph

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: built the graph explorer at /graph, unlisted like the overview. /graph/data computes the whole graph server side in three queries and the page fetches it once. Force-directed SVG with d3-force, node size by degree, colour by type, click to open the record, hover for name and relation count, a filter per type and a search that highlights by name.
- Decisions: articles are edges rather than nodes, so two subjects of one piece are joined and the edge is that article; a collection sits in the same clique, which is what makes a series read as a cluster. War memorials and military profiles are not drawn, because each is a second record of a person the archive already has and drawing both would double the people. Edges are keyed low-high so a reciprocal pair counts once: persons and organizations point at each other through two different fields and that is one relationship. The layout spreads hard and frames itself after settling, because the interesting thing about this graph today is how much of it is not connected and a tight ball hides that.
- Blockers: I put an invented SRI hash on the d3 script tag rather than computing one, so the browser blocked it and the page rendered empty. Replaced with the hash of the actual file. The standalone d3-force build is not served by cdnjs at any path I could find, so this is the full d3 bundle.
- Result: 85 nodes, 172 edges, 28 with no connection at all. The del Valles are the dense centre the prose suggests: Ygnacio 13, Juventino 9, Antonio 7, del Valle Family 9. The Newhalls are thinner, Henry Mayo Newhall 11 but the family group only 1. Filtering to places alone draws 15 nodes and no edges: no place in the archive is related to another place.
- Next: the relation review, then re-render this and compare

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: detect the married name the source states outright. export_entity_candidates.php reads "Barbara Sitzman (Mrs. Paul Cook)" and "Russell (nee Pearl Pardee)" out of body_text and emits them as stated_married_name pairs carrying the sentence and the page. The card presents them as fact rather than judgement, Same person keeps her own name and records the marriage in the same keystroke, and apply_entity_merges.php writes the merge and the spouse together.
- Decisions: the pair is emitted even where a name is not in the extraction index, because that is the case that matters most: two of the four women are indexed only as their husband's name, and one husband is not indexed at all. Four separate defects had to be fixed to get the four pairs out cleanly: a name pattern that required every word capitalised dropped "Henry de Moss"; PREG_OFFSET_CAPTURE returns byte offsets and mb_substr sliced mid-character; splitting on a full stop cut every sentence at the "Mrs." it was about; and the same marriage written twice needed the fuller husband name to win.
- Blockers: none. Both review screens were serving a cached JSON, so a re-export did not reach the browser; both now fetch with no-store, which would have bitten Nathan after every export.
- Result: four stated marriages across the five inventory files, 396 pages. Barbara Sitzman and Paul Cook, Lois Cheney and Henry de Moss, Nicolene Cheney and Wayne Graham, Pearl Pardee and H.B. Russell. Tested end to end with a synthetic decision: the canon collapses Mrs. Paul Cook into Barbara Sitzman and records the marriage as stated by the source.
- Next: Nathan reviews entities, starting with the four stated marriages

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: applied the Beale's Cut legacy path fix, built the data overview at /admin-overview, added the community-from-evidence rule and the unlocatable-source list to RECORD-CHECKLIST.md, and re-ran the relation exporter.
- Decisions: the overview discovers relation fields from each entry type's layout rather than from a list, so a field added in the CP appears on the next request and nothing here can go stale. Relation counts come from one query against the relations table, grouped by field and entry type and filtered to canonical undeleted elements. Each section collapses to a summary line; opening the thin ones by default opened seven of ten, because the archive genuinely is thin, and a page that is mostly open is the long page again. The page reads a review screen's localStorage and never writes it.
- Blockers: two of the three 404 fixes could not be applied. otn-patti and otn-whyte have no title at all, and Craft will not save an entry without one; saving without validation to get around it would write an invalid record on purpose. Ten collections are untitled. The script reports the block and is re-runnable once they are named. Also: Beale's Cut's 404 was on placeLegacyUrl, not legacyUrl, so there was no sourcePath to correct alongside it.
- Result: page height fell from 10,594px to 3,833px collapsed. Two bugs found and fixed while building it: Twig's merge filter is array_merge, which renumbers integer keys, so a map keyed by field id read zero everywhere; and grouping relations by field alone counted the whole archive against each section, reading 193% on articles.
- Next: Nathan names the ten untitled collections, then the relation review

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: corrected Chapter 15's legacy path and re-ran the relation exporter, which attached the 10 orphaned candidates and left no page unmatched. Checked the four remaining 404s against both sitemaps and propose fixes for three. Added the burial place research list to RECORD-CHECKLIST.md and a fourth row to the research list in apply_wikidata_matches.php. Untracked web/review/audit.json, which was already in .gitignore but had been committed, so the rule was never taking effect.
- Decisions: sourcePath carried the same missing segment as legacyUrl and was corrected with it; it is the provenance line a reader would follow, and fixing one and not the other would leave the record half right. The fix script writes each field only while it still holds exactly the broken value, and reports the shape of the evidence rather than asserting it: 70 of the 71 Reynolds chapters already carried the segment.
- Blockers: one correction to the brief. The archive's own text is not silent on Pico's burial: his body reads "His burial at Mission San Fernando ties him permanently to the geography of the Santa Clarita Valley's doorstep". The field and the prose agree with each other and disagree with Find A Grave, so this is not a gap needing external evidence but a contradiction between us and them. The other three bodies are silent, as expected.
- Result: relation candidates 2,251 to 2,261, articles covered 154, pages with no record in Craft 0. Beale's Cut and both Old Town Newhall collections have a clear corrected path in the sitemaps; the Northridge Earthquake source page does not exist on the legacy site at all and has no obvious replacement.
- Next: Nathan decides the three proposed paths, the Northridge source, the Pico grave and socalhistory.org

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: added the downward walk to the family derivation. Grandchildren are whoever points at one of this record's children, the mirror of the grandparent walk, rendered under their own heading in the family box and emitted in the JSON-LD.
- Decisions: Schema.org defines no grandparent or grandchild property, so grandparents and grandchildren both go into relatedTo, which it does define, rather than inventing a term. Grandchildren dedupe after grandparents and before spouses, so a cousin marriage cannot list the same person twice.
- Blockers: none.
- Result: Rodolfo Acosta now shows Dante under Children and Rudy under Grandchildren, from nothing typed anywhere but childOf on Rudy and on Dante. 38 URLs swept, none in error.
- Next: pushed to origin/templates-batch-9 at Nathan's request

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: fixed the War Memorial badge, which asked which casualty record pointed at a family member rather than whether that member was one, so on Rudy Acosta's page it sat on his father and linked back to Rudy. It now renders only where the family member is themselves a war memorial record. Retired the Related person box wherever a family box renders, folding whoever it showed into that box under Other relations when they are not already a parent, child, sibling, spouse or grandparent. Rebuilt the family box in the Related person design: a 56px circular portrait, the name in Playfair, occupation or rank and dates beneath in the muted grey, with a cream and gold initials circle where a record has no featuredImage.
- Decisions: the badge is a marker inside the row rather than a second link, because the row already goes to that person's record and an anchor inside an anchor is invalid. Other relations reads wmRelatedPerson, relatedPersons and mpRelatedPersons both ways and dedupes against every family list in order, so a person who is both a related person and a parent appears once, under Parents. The Related person box still renders on a record with no family at all, so nothing is lost where the fold has nowhere to go.
- Blockers: none.
- Result: verified on Rudy Acosta, who shows Dante under Parents with no badge and Rodolfo under Grandparents; Dante, who shows Rudy under Children with the badge linking to Rudy's own record; Ygnacio del Valle, where Antonio del Valle has no portrait and renders as AV in the initials circle; and Jerry Reynolds, who has no family and no box. 38 URLs swept, none in error.
- Next: Nathan runs apply_wikidata_matches.php, settle_family_relations.php and add_gnis_field.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: added $FORCE_GRAVE to apply_wikidata_matches.php, false by default, which replaces personGraveUrl on the eight records whose stored URL resolves to a different person. Both ends of all eight are recorded in the script with the name, dates and cemetery each one actually serves, so the correction reads without opening anything. Also corrected two broken values, John C. Frémont's birthDate and Juan Bandini's burialPlace, and added the three disputed burial places to the output as needing research.
- Decisions: the override touches the eight listed ids only, only the personGraveUrl handle, and only while the stored URL is still the one that was checked, so it is not a general "trust Wikidata" switch and cannot fire on a value someone has since corrected. The two field fixes work the same way: each is written only while the field still holds exactly the broken string, and reports a skip with the current value otherwise. The three cemetery disagreements are printed every run and never written, because a reinterment is a question of fact rather than a broken link.
- Blockers: none. One correction to my earlier report: I wrote that William Lewis Manly was right in ours and wrong in Wikidata's. It is the other way round, as the table in the same message showed. Our URL serves Queen Anne.
- Result: with both flags off, the dry run reports 8 grave links pointing at the wrong person, 2 broken values to correct and 3 burial places needing research. With $FORCE_GRAVE on it plans 8 replacements and nothing else.
- Next: Nathan runs it with $APPLY and $FORCE_GRAVE, then settle_family_relations.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: extended settle_family_relations.php to retire mpParents, mpChildren, mpSiblings and mpSpouse alongside parentOf, table driven with a mirror rule per field, and to add childOf, siblingOf and spouseOf to the military profile layout as well as the war memorial one. Added two rules to the skills: never touch state you did not create, in scv-import-script, and never read or clear the live localStorage key while testing a review screen, in scv-review-screen. Checked all eight disputed Find A Grave memorials against the live site.
- Decisions: the retirement step now takes a table of handle, entry type, replacing field and direction, so each field is checked against the convention that actually replaces it: mpParents against childOf the same way round, mpChildren and parentOf against childOf inverted, mpSiblings and mpSpouse against siblingOf and spouseOf either way. All four mp fields hold nothing, so all four retire cleanly.
- Blockers: seven of the eight Find A Grave URLs in the archive point at the wrong person entirely, not merely a different memorial for the same man. Find A Grave resolves by number and ignores the slug, so a URL reading christopher-houston-carson served Harry Chapin's grave. Kit Carson, Cave Johnson Couts, Edward Fitzgerald Beale, Edwin Bryant, James Wilson Marshall, John C. Frémont and Juan Bandini are all wrong; William Lewis Manly is right in ours and wrong in Wikidata's. Two data errors also surfaced: John C. Frémont's birthDate holds his own name, and Juan Bandini's burialPlace reads "l Campo Santo".
- Result: curl cannot reach Find A Grave, which serves a Cloudflare challenge, so the sixteen pages were read in a real browser session.
- Next: Nathan decides the eight grave links, then runs settle_family_relations.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: added Spouse of as a fifth choice on the entity screen, key W, with probable_spouse detection in export_entity_candidates.php and a spouses block written by apply_entity_merges.php rather than a merge. Added source links to both review screens: our record and the legacy page beside it, both opening in a new tab. Wrote scripts/import/apply_wikidata_matches.php, dry run by default.
- Decisions: the married-name shape as specified catches two different things, so the card carries the exact warning asked for plus one line telling the reviewer which they are looking at. A relation candidate whose page is not an article now renders with its buttons off and a note saying why, because a decision that could never be applied is worse than no decision. The Wikidata script never overwrites a non-empty field and classifies a Find A Grave conflict by whether the memorial number differs.
- Blockers: the 297 candidates described as having no record in Craft are not that. 287 are war memorial casualty records that have been in Craft all along; the exporter was only indexing the articles section. It now indexes every section carrying a legacy URL. Exactly one legacy page is genuinely unmatched, /scvhistory/signal/reynolds/part15.html, carrying 10 candidates. Separately, 8 of the Find A Grave URLs already in the archive point at a different memorial number from the one Wikidata gives, so one of each pair is the wrong grave.
- Result: 23 pairs match the married-name shape across the four inventories, of which 11 are a woman's own given name and 12 are her husband's. apply_wikidata_matches.php would set 79 fields across 30 records, leaving 11 alone that already hold something different.
- Next: Nathan reviews the 23 spouse pairs, runs add_gnis_field.php then apply_wikidata_matches.php, and looks at the 8 disputed graves

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: settled the family convention on childOf as the single stored direction and rendered it. _layouts/base.twig derives parents, children, siblings, grandparents and spouses once per page and hands the same structure to the new _partials/record/family.twig sidebar box and to the JSON-LD, so the box and the graph cannot disagree. Every hop goes through relatedTo rather than through a field on the element, because the other end may be a casualty whose layout does not carry childOf. Added scripts/import/settle_family_relations.php to widen the relation sources, add the three fields to the war memorial layout, write instructions on all four and take parentOf off the person layout.
- Decisions: the script refuses to remove parentOf unless every one of its relations is already stored the other way, so a layout change cannot take a fact off the site. The field and its rows stay in the database either way; only the layout entry goes. siblingOf survives for the half-brother the parents do not reach and now reads both ways, so it need only be typed once. The War Memorial badge from the old box is kept.
- Blockers: the brief said the four fields are unused. Counting the relations table directly says 13 parentOf and 12 childOf, but that table carries a row per revision; against canonical, undeleted entries it is 2 childOf, 1 parentOf, 1 siblingOf and no spouseOf. The one parentOf relation is already stored as childOf, so nothing is stranded. Separately, the person record for Ygnacio del Valle lists Antonio del Valle as a sibling while its own body text says Antonio was his father; the box now shows that, so it is worth a look.
- Result: query count per person page went down, 115 to 112 on Ygnacio and 106 to 104 on Jerry Reynolds, because the old box ran four field reads and a war memorial lookup per name where the derivation runs one set.
- Next: Nathan runs settle_family_relations.php, which supersedes add_family_to_wm.php in his working tree, then link_acosta_family.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: suppressed the rest of SEOmatic's head output with the same template-level pattern, so the site emits one title, one set of Open Graph and Twitter tags and one JSON-LD block. Also fixed og:type, which said "article" for every element and so said it on a privacy page and on a place.
- Decisions: SEOmatic keeps its meta tags in four containers, not one. Switching off `general` alone left every og: and twitter: tag in place, so `opengraph`, `twitter` and `miscellaneous` are named too, along with the title, link and script containers. og:type now maps by section: article for articles and obituaries, profile for people, war memorials and military profiles, and website for everything else, which is what the Open Graph specification gives as the fallback. og:locale moved into meta.twig because it was the one tag SEOmatic emitted that we did not; robots and referrer policy did not need moving, since they are sent as HTTP headers.
- Blockers: none.
- Result: 38 pages checked for exactly one title, one og:type with the right value for its section, one og:url, one og:site_name reading SCVHistory.com, one twitter:card, one canonical and one JSON-LD block, with no twitter:creator. No failures. Sitemaps still serve, x-robots-tag and referrer-policy headers still send, sitemapsEnabled and headersEnabled untouched.
- Next: Nathan runs add_gnis_field.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: switched SEOmatic's JSON-LD container off and added scripts/import/add_gnis_field.php, which creates a plain text gnisId on the place entry type and wires it into sameAs and identifier in the schema partial. Also retired _partials/jsonld/person.twig, which predated the rebuild and emitted a second, conflicting Person block on every person page.
- Decisions: SEOmatic is switched off from the template rather than from its settings. `{% do seomatic.jsonLd.container().include(false) %}` sits in _partials/head/schema.twig next to the block that replaces it, so the reason travels with the change, it is in git rather than in the database, and sitemaps, redirects and headers are untouched. A GNIS feature ID is stored as plain text, not a number, because it is an opaque identifier and a number field would eat a leading zero.
- Blockers: SEOmatic still emits a second <title> and duplicate og:site_name, og:type, og:url, og:title, twitter:card and twitter:title, plus twitter:creator with an empty handle. Its og:site_name says "SCV History" where ours says "SCVHistory.com". That is a separate decision from the one asked for, so it is reported, not changed.
- Result: 38 URLs swept, every one carries exactly one JSON-LD block and no empty Organization stub. The old person partial's deathPlace and dateModified were folded into the schema partial first, so nothing it emitted was lost.
- Next: Nathan runs add_gnis_field.php, then decides on SEOmatic's duplicate title and Open Graph tags

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: moved the sticky header and the back to top control out of the 13 record templates and into _layouts/base.twig, so they reach every page; the collection strip stays conditional and base works out the collection and the chapter from the element's own field layout. Rebuilt the structured data: _partials/head/schema.twig emits JSON-LD per entry type and Dublin Core alongside, included from meta.twig. Article becomes ScholarlyArticle where an author exists, Person carries alternateName, birth and death dates, jobTitle and sameAs, Place carries geo and containedInPlace, a casualty record is a Person with an associated Event cross-linked by @id, and a collection is a CreativeWorkSeries whose hasPart is the articles in reading order. Every related record appears as a node with its own canonical URL.
- Decisions: a property is emitted only where the field holds a value, and a date only where it parses; the legacy text says "c. 1854" as often as it says a date, and a guessed dateline in a citation is worse than none. publisher, license and isAccessibleForFree are added only to CreativeWork types, since a Person does not define them and a validator flags what a type does not define. Coordinates are emitted as numbers. Fields are read through the element's own layout throughout, and the same trap bit once here in its category form: entry.section is defined answers true on a Category and then throws.
- Blockers: SEOmatic emits a second JSON-LD block on every page whose author, creator and copyrightHolder point at two Organization nodes carrying nothing but an @id. That predates this work and is the one thing on the page a validator will object to. Fixing it means either filling SEOmatic's identity settings or turning its JSON-LD off now that the site emits its own, and both are settings changes, so I left them alone.
- Result: 38 URLs across every section, no error, JSON-LD and Dublin Core on all of them. wikidataId and viafId are wired into sameAs but hold no values on any record yet.
- Next: Nathan decides on the SEOmatic block, and populates the authority identifiers

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: built scripts/import/link_mentions_in_prose.php, dry run by default. For every article it takes the records already related to it, gathers every spelling each answers to from the canon plus its title and aliases, and records the first occurrence per entity per paragraph as character offsets. The body is never rewritten. The links live at templates/_data/prose-links/<id>.json and _partials/prose.twig applies them at render through Twig source(), so deleting that directory reverts every link and changes no record. Also reported on the six untitled collections: boston, manzer, worden, coins and iraq all have a tree on the legacy site; newsmaker does not.
- Decisions: the layer is per article rather than one file, so a render parses only what that page needs. Offsets are characters, not bytes, because Twig slice counts characters. Every span carries the text it expects and the partial checks it before wrapping, so if the rejoin rule in prose.twig ever changes the links go quiet instead of cutting through a sentence. Matching is case sensitive and needs a word boundary, and a spelling under four characters is refused.
- Blockers: the canon and the relation decisions are not applied yet, so today's dry run only sees the relations already in Craft. 79 of 103 articles have no related record at all and were skipped.
- Result: dry run, 38 mentions across 18 articles, 46 headings and image tokens passed over, 2 matches dropped for sitting inside a link. Most inbound: Rancho San Francisco 12, Ygnacio del Valle 5, Lyons Station Stagecoach Stop 4, Jerry Reynolds 4.
- Next: Nathan applies the canon and the relations, then re-runs the linker and commits the generated layers

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: three body fixes, a sticky record header, and context on the entity screen. _partials/prose.twig now rejoins a line that does not end in sentence punctuation when the next line opens lowercase or with a comma, so an italicised species name no longer splits a sentence into three paragraphs: 46 of 158 records affected, 510 lines rejoined. clean_legacy_bodies.php now strips a bracketed index link and a byline block of the shape title, By X, publication, date, and takes the date and the author out of that block before removing it. Added the sticky compact header and the back to top control to every record template, reordered the mega menu to ARTICLES, COLLECTIONS, PEOPLE, PLACES, BY ERA, and updated design/MENU-MAPPING.md to match. export_entity_candidates.php now carries up to three sentences per name from body_text, a shared-article sentence on both sides of a pair, and the other names in play around each one; entities.html shows them behind a show context toggle on key C.
- Decisions: the title line of a byline block is only taken when a byline follows it within four lines, because a title on its own line is indistinguishable from an opening sentence. Below the byline the walk takes at most three more lines, so an unrecognised publication cannot run into the prose. On the entity screen the surname block alone was too narrow, since Don Ygnacio blocks on ygnacio and Ygnacio del Valle on valle and the two never meet, so names sharing a rare word are pulled in as well and common words like John pull in nobody.
- Blockers: nothing applied. $APPLY is false in the committed script and the cleanup has not been run against the database.
- Result: dry run over all 103 articles, 54 war memorials and 1 obituary, 158 records: 8 would change, 150 already clean. Five would gain originalPublishDate, six would gain writtenBy.
- Next: Nathan reads the dry run, then applies clean_legacy_bodies.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: repointed the entity reconciliation trio at the extraction index. export_entity_candidates.php emits 1,435 distinct names across the four inventory files with 2,081 proposed pairs from five tests; entities.html offers Same person, Different people and Skip, and asks which name survives; apply_entity_merges.php writes inventory/legacy/entity-canon.json and does not touch Craft. export_relation_candidates.php reads that canon and collapses variants into one candidate per entity per article. Added Discard as a fifth choice on the relations screen, written to inventory/legacy/discarded-entities.json.
- Decisions: the canon lives in the repository rather than in Craft because almost nothing has been promoted yet; there is nothing to merge there. Pair blocking is the surname plus the surname with one character removed, which lets Herrington meet Harrington without comparing everything to everything.
- Blockers: I reported earlier that has_legacy_page was false on all but one entity. That was wrong: 133 entities carry one, which matches the re-derivation HANDOFF describes. The relations export now shows 162 candidates promoted by that rule.
- Next: Nathan reconciles at /review/entities.html, runs apply_entity_merges.php, then re-runs export_relation_candidates.php before reviewing relations

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: added Same as as a fourth choice on the relation review screen. It links a candidate to another name of the same kind on the same article; apply_relations.php appends the spelling to the surviving record's alias field and creates nothing. Also made the dry run show what a create leads to, by giving pending creates a placeholder id so the relation and alias plans are visible before applying.
- Decisions: alias separators follow the existing convention, a newline for personAliases which is multiline and a comma for the single line place and organization fields. A placeholder id is negative and is filtered out before any write, so it can never reach the database.
- Blockers: none. Tested end to end on the Audubon pair: created John James Audubon, folded John Woodhouse Audubon into its aliases, related the survivor to the article, created no second record, then reverted.
- Next: Nathan reviews at /review/relations.html

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: built the relation adjudication trio. export_relation_candidates.php emits 1,964 candidates across 99 articles, joining entity_index to articles on the page URL. web/review/relations.html shows one article at a time with Link, Create and Tag per candidate, Grok's flags inline, and keyboard control. apply_relations.php sets subjectPerson, depictsPlace and subjectOrganization, creating only what was approved. Tested end to end with a synthetic decision file, then reverted.
- Decisions: the flags named in the brief do not exist in the data. Grok set possible_same_person and possible_place_variant, not split_given_name or honorific_variant, so those are surfaced instead, and a near_match_in_craft signal is derived here by comparing surnames against existing records. That is what puts "Anne Darcy" beside a record for a fuller form of the name.
- Blockers: has_legacy_page is false on all but one entity across all three inventories, so the strongest promotion rule never fires. inventory/legacy/sitemap.json and sitemap-2.json, which HANDOFF says the re-derivation used, are not in the repo.
- Next: Nathan reviews at /review/relations.html, downloads, then apply_relations.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: fixed the Reynolds matching bug and applied the import. 57 articles created, 23 gap-filled, no existing body touched; the collection now holds all 80 in TOC order. Added PART FIVE to collectionParts at position 24, and made add_collection_parts.php extend a collection that already has rows instead of skipping it. Cleaned the 57 new bodies, then re-ran the image import.
- Decisions: the bug was in the legacyKey step, not the title fallback. notes and part01 to part06 exist in both the Perkins and Reynolds inventories, so legacyUrl is now tried first and every candidate is rejected when it already belongs to a different collection. The cleanup was run before the image import rather than after, because the 57 new bodies carried raw legacy chrome; doing it in the order asked would have placed 136 tokens in text about to be stripped, against 31 afterwards.
- Blockers: PART FIVE has no subtitle. Naming a section of Leon's work is Nathan's call, so the label is deliberately bare.
- Result: 143 images downloaded, 25.9 MB, no failures. 144 relations across 28 records, 31 tokens in 13 bodies. The volume now holds 568 assets, 468 of them legacy. Article bodies carry 80 placement tokens; no war memorial body carries one.
- Next: relation adjudication, item 3 in HANDOFF.md

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: ran the legacy image import. 325 images downloaded at one request per second, 113.9 MB, no failed fetches. 370 new recordImages relations across 60 records, 49 [image:N] tokens placed in 17 article bodies, no token in any war memorial body. Article band cap now 52 percent at every width. Also stripped placement tokens out of the meta description, where they had started appearing in og:description.
- Decisions: none beyond those already agreed.
- Blockers: none. A second run reports 0 to download, 326 already in the volume, 0 bodies to change.
- Next: the 28 Reynolds pages still have no Craft record, so their images wait on import_reynolds.php

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: every import script that has an $APPLY flag now prints "APPLY IS ON, this will write to the database" as its first line when the flag is true; 32 scripts, all currently false. The scv-import-script skill now states that $APPLY is false in the committed file always, that the flip is local and never committed, and that the flag is checked before running rather than assumed. Article band cap tightened to 52 percent below 1000px, 60 percent at full width.
- Decisions: the guard sits immediately under the flag, which is safe because no script prints before that line. Measured the artwork rather than eyeballing it: the dense figure begins at 55.6 percent of the band, mid tone at 53.2, faintest hair at 44.9, and those fractions hold at any width because the image is wider in aspect than the band.
- Blockers: 60 percent at full width is 4.4 points inside the dense figure. It only looks clear because the text does not fill the column there; the longest line reaches 43.3 percent.
- Next: Nathan reads the image dry run before anything is applied

2026-09-18

- Agent: Claude Code
- Date: 2026-09-18
- Done: capped the article band content to 60 percent of the wrapper when a band image is present, full width when not, with text-wrap balance on the h1. The breadcrumb, kicker, title, collection, byline and chips all share the capped column, so none of them runs under the artwork. Below 640px the text takes the full width and the artwork drops to a 0.22 wash.
- Decisions: the cap holds at 60 percent all the way down rather than loosening at 900px. background-size cover scales the artwork up as the band narrows, so the subject takes more of the width, not less; 68 percent at 900px put the breadcrumb back under the hair. The other six sections were not changed: none of them renders a background artwork layer, their image sits in the portrait grid column, and the grid already holds the text to 66.6 percent.
- Blockers: at a 900px band the breadcrumb's first line still grazes the light hair at the right. The title, subtitle, byline and chips are fully clear.
- Next: Nathan decides whether the other six should gain the layered band treatment, which is what would make the cap meaningful there

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: widened the trailing gallery rule in clean_legacy_bodies.php. A caption run is now cut when the line above it is clear prose, a copyright line, an exhibit heading or another caption, and a run can absorb a short caption that ends in punctuation when three or more captions sit above it. The copyright and gallery walks now loop until neither moves, so a copyright line exposed by cutting a gallery still reaches finePrint. Applied: 10 records cleaned across two passes. The image token count falls from 170 to 49.
- Decisions: the look-ahead is what separates a caption from prose. "New Boiler 1893?" has gallery above it and goes; "R.I.P." and the lettered footnote "k. Meaning the Newhall School District." have prose above them and stay. The four byline cases were left alone as instructed.
- Blockers: $APPLY was left true in the committed copy of clean_legacy_bodies.php, so the first run of this session wrote rather than previewed. Set back to false, which is what the file's own header documents.
- Next: none

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: an article's band now falls back to the bandImage of the collection it belongs to, so a Perkins chapter wears the Perkins band and a Reynolds chapter the Reynolds band. A standalone article with no artwork stays plain cream. Two layers, matching the collection lander exactly.
- Decisions: still no fallback to featuredImage at either step, since those are title cards with lettering. The chain walks [entry, collection] and stops at the first bandImage it finds, guarding each element against its own field layout.
- Blockers: the Perkins band artwork carries legible pseudo-text on the map at the right edge, away from the headline but readable.
- Next: Nathan looks at the lettering on the Story of Our Valley artwork

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: import_legacy_images.php now carries a real contact address and skips the service seals by name and by the *logo.* shape rather than by a lower page threshold. _partials/prose.twig rejoins a line with no letters in it to the line above, fixing the split footnote markers that rendered as three paragraphs; 30 records were affected.
- Decisions: the seals are named rather than caught by a threshold of 3, which would take real content off a short series. The rejoin test is "no letters at all", which keeps [image:N] out of it by construction, with explicit guards either side.
- Blockers: the token count did not fall after the body cleanup. The three records carrying most of the tokens are the same three whose caption runs the cleanup reported as not confident and left in place, so the anchors still resolve into them.
- Next: Nathan decides on the caption runs in rancho-san-francisco-a-study, the-pico-ghost-camp and manuscript-colonization-1940s; that resolves both the cleanup report and the token clustering

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: added three skills under .claude/skills/ so the repo's conventions stop being restated in every prompt. scv-import-script covers the eval-style script pattern, the dry run and idempotency rules, the field-layout guard and the traps that have actually cost time here. scv-record-template covers the band, the design tokens, the shared partials and the two conditional rules. scv-review-screen covers the export, review, apply trio.
- Decisions: each skill is written from the code in this repo rather than from general practice, and names the reference file to read first. Kept each under 700 words so it can be read in full every time.
- Blockers: none
- Next: none

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: scripts/import/import_legacy_images.php, dry run by default. Reads the three image inventories, resolves each src against the page it came from, prefers links_to only when it is an image, downloads at one request per second with a project User-Agent, creates assets in archiveMedia/legacy, relates them through recordImages, and places [image:N] tokens in the prose. War memorial bodies get relations only. Nothing was applied; the database is untouched.
- Decisions: placement does not use position_in_body. That field is the image's ordinal on the page, 1..n on every page in all three inventories, not an offset; using it would stack the first n paragraphs with figures. Position is recovered instead from the text preceding each <img> in the source inventory's body_html, which locates 89 percent of them. An image with no recoverable anchor is related but not placed. Pages are matched on legacyUrl, not legacy_key, because part06 exists in both the Perkins and Reynolds inventories.
- Blockers: the >5-pages chrome rule matches nothing; the worst offender is armylogo.png on 4 pages. Total bytes needs a paced HEAD pass against the live site, so it is behind a flag and off by default. 28 Reynolds pages have no Craft record until import_reynolds.php runs.
- Next: run clean_legacy_bodies.php before this one, or gallery captions still in the body will attract tokens

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: two scripts, both dry run by default. backfill_provenance.php derives sourcePath, legacyKey and legacyUrl for 60 records from four evidence sources and reports 52 with nothing to derive from. clean_legacy_bodies.php strips legacy site chrome from the head and tail of article, war memorial and obituary bodies, rejoins drop caps, and moves the copyright line into finePrint; 38 records would change, 12 carry something it was not confident about and reports them instead.
- Decisions: provenance is never guessed from a slug. A legacy reference pointing at another host cannot yield a root-relative path and is reported, which is the one case in the data, Mission San Gabriel. The cleanup walks down from the first line and up from the last and stops at the first unrecognised line, so nothing can ever be removed from the middle; a byline left stranded below an unrecognised line is reported rather than reached for.
- Blockers: the WordPress export carries almost no scvhistory.com links in bodies, so the real source is its legacy_url meta on 41 posts. The entity_index cross-reference yields exactly one usable entry across all three inventories.
- Next: Nathan reads the cleanup report, then runs both scripts

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: the record player's progress bar advances continuously instead of jumping once per sentence. Progress is measured in characters and painted on an animation frame. Within a sentence the position comes from the utterance's boundary events when they arrive, and from elapsed time against an estimate when they do not. The total to the right of the slash is computed once and held, and only the remaining estimate is recalibrated.
- Decisions: the within-sentence estimate eases asymptotically toward the sentence end rather than clamping at it. A hard clamp stalled the bar whenever a sentence ran longer than estimated, which put the jumps straight back; simulation puts stalls at 48 percent of samples for a sentence running 2x long, and at zero with the easing. Changing speed recomputes the held total, since that is a deliberate act.
- Blockers: Google US English, the default voice, fires no boundary events at all, so the timer path is what almost every reader will get. The boundary path is confirmed to fire with Samantha but I could not confirm it drives the bar; the browser extension dropped mid-test.
- Next: Nathan to confirm the bar moves smoothly on a Mac using a local voice such as Samantha or Alex

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: articles/_entry.twig renders the featured image in the body only when the article is not in a collection. A collection article's featured image is a title card carrying the headline, so it is now the social and index image only. bandImage washes behind the band on any article when it is set, with no fallback to featuredImage. The collection lander band is untouched.
- Decisions: the standalone hero keeps its existing recordImages fallback, since that path is unchanged. The band image layer matches the treatment the lander settled on, one full-bleed layer with the same filter and no mask, because the fade is baked into the artwork.
- Blockers: two of the four featuredImage consumers named in the brief do not exist. The articles index has no per-article thumbnail, only a collection thumbnail in the group header, and the collection contents list has no thumbnail at all. og:image and twitter:image do use it.
- Next: Nathan decides whether the articles index and the collection contents should gain per-article thumbnails

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: removed the voice picker from the record player. No select, no localStorage preference, no list. The player is back to play/pause, progress bar, elapsed and total time, and the four speed buttons. Voice selection is now silent and automatic, keeping the existing chain: Google US English, then Siri, then any remote voice, then Samantha/Alex/Daniel/Karen, then en-US, then the first English voice.
- Decisions: the novelty filter is kept even without a picker, so a machine with little else installed still does not read in Bad News or Zarvox. The onvoiceschanged rebuild is kept, since Chrome populates the voice list asynchronously and the first call comes back empty.
- Blockers: none
- Next: none

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: reading voice order now puts Google US English first, then Siri, then any remote voice, then Samantha/Alex/Daniel/Karen, then en-US, then the first English voice. The macOS novelty voices are filtered out of the picker entirely, so they can be neither chosen nor defaulted to. The picker lists remote voices first, then the named system voices, then the rest alphabetically.
- Decisions: 15 names filtered, the 13 Nathan listed plus Albert and Superstar, both part of the same macOS novelty set. Fred, Junior, Kathy and Ralph are kept: they are old and poor but they are real reading voices, not sound effects.
- Blockers: none
- Next: say the word if the old MacinTalk voices should go too

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: _partials/record/tools.twig picks a better reading voice. Preference order is Siri, then Samantha/Alex/Daniel/Karen, then any remote voice, then en-US, then the first English voice. Added a voice picker listing the English voices by name, defaulting to the best match and remembering the choice in localStorage. The picker rebuilds on voiceschanged, since Chrome populates the list asynchronously.
- Decisions: the picker hides itself when fewer than two English voices exist. Changing voice mid-read restarts the current sentence so the change is audible.
- Blockers: uploads/reynolds-map-hero.jpg does not exist. It is not in this repo and not in the linked design project; the mirrored crop there is a CSS treatment of existing artwork, not an exported file. No image was produced.
- Next: Nathan supplies the source panorama if he wants a real 2400x1000 crop cut from it

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: scripts/import/add_wm_community.php adds neighborhood, wmFamily and wmSelectiveServiceDate to the warMemorial entry type. import_warmemorial.php maps the two new labels and skips five letter fragments as noise. war-memorial/_entry.twig renders all eight new fields. war-memorial/index.twig rebuilt: cream band, grouped by conflict chronologically, no filter and no sort, and a nameplate card face where there is no portrait.
- Decisions: the #877 duplicate is reported by the pre-flight and otherwise left alone. The external box label changed from RELATED to EXTERNAL, matching every other entry template, because that is where wmWallReference belongs. On a nameplate card the face carries the name and rank, so the strip beneath carries only branch and year; both card types still convey the same four facts.
- Blockers: the F loop in war-memorial/_entry.twig used `is defined`, which reads true for a field the entry type does not have and then throws on read. It 500ed every casualty page once wmFamily was referenced. Rewritten to read the entry's own field layout.
- Next: Nathan runs add_wm_community.php then import_warmemorial.php

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: band artwork now comes from bandImage first with featuredImage only as a fallback, in collections/_lander.twig and in the six other entry templates whose band carries an image. Each carries a one-line note that the fallback may show lettering. Lander stat blocks rebuilt: articles, chapters, publication runs and eras covered, each derived from the collection's own articles.
- Decisions: articles/_entry.twig is untouched because its band has no image; pages/_entry.twig is untouched because the page entry type has no bandImage field. A run or era count of one is omitted rather than printed, since one is not a statistic.
- Blockers: the stats compute to 23 articles, 21 chapters, 3 publication runs and 3 eras covered, not the 2 runs and 4 eras expected. The six portrait bands use a 4:5 frame with object-position top, so a wide right-composed band image will crop to its top strip there.
- Next: Nathan uploads the CD artwork to bandImage on the Reynolds collection, and decides on the run gap threshold and the era count

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: wrote scripts/import/import_warmemorial.php, eval style, dry run by default. 54 casualty pages: 18 created, 36 gap-filled on legacyKey. service_record labels mapped to the wm fields; the 45 unmapped labels go to wmServiceExtra as label and value verbatim, 69 rows, nothing dropped. wmConflict set from the legacy_key prefix. Also wrote inventory/legacy/warmemorial-images.json, 133 images, none downloaded. Verified every field write with a temporary fixture record, then hard-deleted it.
- Decisions: a trailing period is trimmed from a title only when the last word is not an abbreviation or an initial, so the four transcription artifacts are fixed and the six Jr. names are left intact. Where two labels hit one field the first in the map wins and the other overflows, the rule the brief sets for College. No value corrected: the three "Amry of the United States" pages import verbatim.
- Blockers: neighborhood is not on the warMemorial entry type, so communities_mentioned is dropped on 51 pages. Craft holds a duplicate Rudy Alexander Acosta, #877 and #526, sharing one legacy URL. The war-memorial template renders none of the eight new wm fields.
- Next: Nathan runs the script, resolves the Acosta duplicate, and decides on a neighborhood field and template rows for the new fields

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: collection lander rebuilt to the cream band spec with two masked image layers; Contents now groups by the collection's own parts in articlesInCollection order and the era chips and "No era recorded" group are gone. Added scripts/import/add_collection_parts.php (collectionParts table field, four parts for the Reynolds work). Added scripts/import/import_reynolds.php for the 80 page reynolds-full.json. Added LEGACY_HOST to .env and a new .env.example, config/custom.php exposing legacyHost, and scripts/import/normalise_legacy_urls.php. templates/_partials/legacy-url.twig now reads the host from config.
- Decisions: custom config lives in config/custom.php because Craft 5 GeneralConfig has no slot for it; general.php points at it. The normaliser leaves a legacy URL pointing at another host alone rather than stripping it to a path that would resolve against the wrong site. Reynolds matching falls back to the legacyUrl tail and then to the normalised title, because five WordPress rows have a wrong or missing legacyUrl.
- Blockers: 14 of the 23 existing Reynolds bodies differ from the legacy text and were left alone pending a call on which is authoritative. The four collectionParts rows only cover positions 1 to 23, so once all 80 land, PART FOUR swallows everything from Chapter 22 on.
- Next: Nathan runs add_collection_parts.php, normalise_legacy_urls.php and import_reynolds.php, and decides on the Reynolds body differences

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: wrote scripts/import/import_perkins.php, eval style, dry run by default. Imports the 20 wave 0 Perkins pages as Articles matched on legacyKey, wires the 13 series pages to the Story of Our Valley collection in series_position order, and writes inventory/legacy/perkins-images.json (199 images, not downloaded). Dry run reported; nothing written to the database. Verified every field write with a temporary fixture article, then hard-deleted it.
- Decisions: match falls back to legacyUrl when legacyKey is empty, so the already-imported Birth of Newhall (#869) is adopted rather than duplicated. An existing article is only gap-filled, never clobbered, unless $OVERWRITE is set. An empty extracted value never overwrites anything.
- Blockers: date_raw and subtitle are empty on all 20 pages, so originalPublishDate and subheadline cannot be set. Saugus-Valencia has no matching neighborhood term; Craft spells it Saugus/Valencia. body_text carries legacy site chrome.
- Next: Nathan decides on Saugus-Valencia, on the body_text chrome, and whether to run with $APPLY

2026-09-17

- Agent: Claude Code
- Date: 2026-09-17
- Done: rebuilt the site header and mega menu in templates/_layouts/base.twig and templates/_partials/header/site-header.twig, from design/menu-source.html for markup and design/MENU-MAPPING.md for data, targets and omissions. Five menus, live counts, feature cards, active-state mapping, hover and keyboard behaviour, 980px collapse. Whole fragment cached with Craft's cache tag keyed on the active menu.
- Decisions: items marked OMIT in MENU-MAPPING.md are absent, and a column left empty by them is dropped. Photo galleries and Documents omitted because both sections are empty. DONATE omitted because no donate page exists. Panels are all rendered and toggled rather than conditionally rendered, so the header can be cached as one fragment.
- Blockers: none
- Next: Nathan reviews the header, decides on DONATE and on showing a zero count for Newsmaker of the Week

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: reported empty titles (IDs 583-685) caused by import_places_and_series.php plus hasTitleField false. Wrote TODO.md Waiting on Nathan for 22 community Places, 10 real Places, 13 collection titles from Jordy. No database changes.
- Decisions: none executed. 7 Places listed as Open Questions. Five neighborhood terms missing (not added).
- Blockers: waiting on Nathan before any Place delete or title write
- Next: Nathan approves TODO.md then execute

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: wrote CANDIDATES.md from candidate_review (10 people, 29 places, 0 orgs). Aliases marked. No Craft import or edits.
- Decisions: none
- Blockers: none
- Next: Nathan reviews CANDIDATES.md

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: checked out grok-build. Updated CONTENT-MODEL.md (War Memorial section; Person = figures/authors; ranchos may be Place and Org; local DDEV is sample). Copied PHILOSOPHY.md from main. Extracted inventory/entities.json (4186 mentions) and draft inventory/canonical_entities.json (122 sample records, 39 candidates for review). No new Craft entries. No articles. No Cloudways.
- Decisions: outputs in ~/scvhistory; candidate_review requires 3+ distinct editorial pages; war memorial names are not Persons
- Blockers: none
- Next: Nathan reviews canonical_entities.json candidate_review list

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: wired obvious Place relations (10 Places with people, 4 with orgs, Mentryville related to Pico Canyon). Wrote PLACE-RELATIONS.md. Persons still 33. No new stubs. No Cloudways.
- Decisions: skip unsure civic mentions (Acosta, Wilk) and missing Friends of Mentryville org
- Blockers: none
- Next: Nathan reviews /places/newhall and PLACE-RELATIONS.md Unlinked list

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: Places hub live at /places (39 stubs, neighborhood filter, no map). Series live at /collections (13 Signal/OTN stubs, no articles). Templates places/_entry and collections/_entry. 301 tables stay in hub files only.
- Decisions: Place body is a one-line ID; neighborhood tagged only when the term already exists; Mentryville Place kept separate from Organization; Tataviam Culture is not a Place
- Blockers: none
- Next: Nathan reviews /places and /collections before article attach or Cloudways 301s

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: 11 memorials imported into warMemorials from Jordy; hub specs written (PLACES-HUB.md, COLLECTIONS-HUB.md); Persons unchanged (33)
- Decisions: remaining WWII profiles go to warMemorials, not Persons; no Place or Collection import
- Blockers: none
- Next: Nathan reviews 36 War Memorial entries before more HTML imports

2026-09-16

- Agent: Grok Build
- Date: 2026-09-16
- Done: added warMemorials section; moved 25 casualty records out of Persons
- Decisions: casualties are War Memorial entries; Person stays figures and authors
- Blockers: none
- Next: import remaining warmemorial HTML into warMemorials, not Persons

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: specced Places and Collections hubs; wrote PLACES-HUB.md, COLLECTIONS-HUB.md, places-candidates.json. Extracted People-category object-page candidates to people-candidates.json (50, no Craft import). War Memorial is a separate local section (`warMemorials`, URI war-memorial/{slug}); listed 25 Person slugs to move later; did not import the 36 HTML files; did not delete Persons.
- Decisions: indexes 301 to Place stubs; Signal/OTN series are Collections; Person is figures and authors only; casualty honor records belong on War Memorial; no Craft content import this session
- Blockers: none
- Next: Nathan reviews hub specs before Place stubs are imported, and reviews the 25 war-memorial Persons plus people-candidates.json before another Person import

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: exported Person Find A Grave fields to grave-audit-export.md for Grok Bot
- Decisions: none
- Blockers: none
- Next: push so Grok Bot can audit

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: exported all 58 local Person entries to grave-audit-export.md (slug, title, fullName, birthDate, deathDate, burialPlace, personGraveUrl). Abel Stearns personGraveUrl stays empty. No missing-memorial research. No Cloudways.
- Decisions: audit file lists stored URLs only; do not invent Find A Grave links
- Blockers: none
- Next: Nathan reviews grave-audit-export.md

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: Task 4 Person pilot. Extracted 25 war-memorial profiles from Jordy to JSON on the MacBook. Imported 25 new Person entries in local DDEV (created=25 skipped=0 failed=0). Local count 33 to 58. personGraveUrl left empty. No Cloudways.
- Decisions: war memorial first; skip existing slugs; no relations; no replacement grave URLs
- Blockers: none
- Next: Nathan reviews the 25 before scaling. Cloudways Abel Stearns grave URL still needs a clear if that entry exists there

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- Done: removed wrong Find A Grave URL for Abel Stearns from import scripts. Local Person entry already cleared in DDEV.
- Decisions: no replacement memorial
- Blockers: none
- Next: Cloudways Person field still needs the same clear if that entry exists there

2026-09-15

- Agent: Grok Build
- Date: 2026-09-15
- What was done: Tasks 1-3 on `grok-build`. Wrote `INVENTORY.md` from Jordy (read-only). Wrote `CONTENT-MODEL.md` from live `config/project/`. Implemented photographs and documents channels plus ingest fields in local DDEV only. No Cloudways apply. No imports.
- Decisions made: Skip indexes, empty pages, and flipbook HTML. Object pages to photographs. `files/` packages to documents. Remainder HTML to articles. War memorial profiles to persons. Mentryville is one Organization plus one Place. No Leon gate. Roles Matrix and military merge not built.
- Blockers: none
- Next step: Task 4 only when Nathan asks. Entity-first still applies. Ask before `git push`.

- Task 1 inventory of `/Volumes/Jordy/SCVHistory/scvhistory.com` (read-only). Wrote `INVENTORY.md`
- Live walk: 734,880 files, 655.59 GiB, 0 errors. Manifest (20 Aug 2026): 734,889 files
- Editorial HTML classified: 9,430. Dominant types: object/photo pages 4,871, articles/essays 2,430, obituaries 635, place/topic indexes 554
- `scvhistory/files/` is 1,043 document packages (TIFFs, PDFs, flipbook HTML, Apache indexes), not the editorial site
- TIFFs on this mirror are all under `scvhistory/files/` (about 351 GiB). `gif/` has none
- Scripts in `scripts/inventory/` are resumable and refuse to write to Jordy
- Task 2: wrote `CONTENT-MODEL.md` from live `config/project/` plus INVENTORY.md mapping
- Live schema: 9 sections, no Photograph, no Document. `legacyKey`/`legacyUrl` exist but are not on most types. `sourcePath`/`legacyHtml`/`legacyCategory` do not exist
- Proposed ingest gaps only: Photograph/Object section, Document section, global ingest fields, credit parse fields, body vs legacyHtml
- Did not redesign roles Matrix, military-on-Person, taxonomies, Award, map, haunted, On This Day, or IIIF
- Nathan approved CONTENT-MODEL.md: skip indexes/empty/flipbook HTML; object pages to photographs; files/ packages to documents; remainder HTML to articles; war memorial profiles to persons; Mentryville is one org plus one place; no Leon gate
- Task 3 (local DDEV only): photographs and documents channels; global `legacyKey`, `legacyUrl`, `sourcePath`, `legacyHtml`, `legacyCategory` on every migrated type except militaryProfiles; credit parse fields on photographs; `archivalFiles` and `documentFiles`
- Did not apply on Cloudways. Did not start imports. Did not build roles Matrix or military merge
- Blockers: none
- Next: Task 4 only after Nathan asks. Entity-first still applies

2026-04-15

- Built full 12-phase BUILDPLAN covering templates, Archive.org, API, Flutter, search, ElevenLabs, membership, legitimacy, data model, community, LOD
- Created DATA_MODEL.md — complete field reference for all 9 entry types plus 5 new entry types
- Created TAXONOMY_STANDARDS.md — LCSH/AAT/TGN alignment, CARE Principles, governance
- Created 11 taxonomy JSON files with verified Wikidata/AAT/LCSH URIs in taxonomy-import/
- Created TATAVIAM_AUDIT.md — complete Indigenous cultural audit for 6 peoples (Tataviam, Chumash, Tongva, Serrano, Kitanemuk, Vanyume)
- Verified all Indigenous Wikidata QIDs — corrected Tongva to Q1479279, Chumash confirmed Q24251468
- Created culturalSensitivityNote global field on Cloudways (field ID 174)
- Created Person JSON-LD partial template at templates/_partials/jsonld/person.twig
- Created craft-cp-field-checklist-2026-04-15.md — complete CP field creation guide
- Created IIIF_SESSION_PROMPT.md — 10-question focused prompt for dedicated IIIF session
- Decided: Groups restructured with groupType taxonomy (Family, Cultural Group, etc.)
- Decided: Roles Matrix field replaces plain text occupation on Person
- Decided: Military Profile merged into Person as conditional field group
- Decided: Obituary remains separate section with submission workflow
- Decided: Haunted places, Named places map, Walk of Fame, War Memorial as special pages
- Decided: Import pipeline is entity-first — never import articles before canonical entities exist
- Decided: IIIF Manifests are derived outputs, not stored entities
- Decided: Archive.org hosts files/tiles; Craft owns all metadata and presentation logic
- Added Phase 12 LOD roadmap — Schema.org + Wikidata light LOD first
- Fixed org section template on Cloudways — now rendering at /organizations/{slug}
- Fixed all 14 org entry titles on Cloudways (were null)
- Next: Add culturalSensitivityNote to all entry type layouts in CP, Military Profile migration, pull Cloudways DB to DDEV, build organizations/index.twig

2026-04-14

- Tested organizations/_entry.twig on Cloudways — template rendering correctly
- Fixed org section template path — was _entries/organizations, corrected to organizations/_entry via CRAFT_ALLOW_ADMIN_CHANGES=true in .env
- Fixed all 14 org entry titles — were null due to circular titleFormat reference
- Identified images not showing on Cloudways — featuredImage field empty on org entries, needs investigation
- Identified need for DB sync from Cloudways to DDEV local
- Next: Pull Cloudways DB to DDEV, fix images, build organizations/index.twig
- Received full project handoff document
- Confirmed `php craft eval` does not exist in Craft 5 — use `php craft exec`
- Confirmed Craft 5 sections API is `Craft::$app->entries` not `Craft::$app->sections`
- Built `organizations/_entry.twig` covering all 23 org fields
- Decided: local = code only, Cloudways = content + testing
- Created session management system: BUILDPLAN, CHANGELOG, ERRORLOG, SESSION_START
- SCVTalk moved to backlog — not an active workstream
- **Next:** Push org entry template to Cloudways, build organizations/index.twig

## 2026-09-17 (Claude)

- Found and fixed a root-cause bug: 8 of 12 entry types had no Title field in their layout, so Craft silently discarded titles on save. Added the native Title element to article, militaryProfile, group, organization, obituary, place, collection, event. Person keeps titleFormat {fullName}.
- War Memorial: added 13 service record fields, parsed all 36 records from stored legacyHtml, populated branch, rank, unit, home of record, dates, incident, awards, burial, and narrative. Body replaced with narrative.
- Communities: renamed the Neighborhood category group to Communities (handle stays neighborhood), added body, aliases, type, and lat/lng fields for the planned community map.
- Places: converted 22 community stubs to Community terms, deleted 3 stubs duplicating Organizations, titled 12 real Places and assigned communities. Places went 39 to 14.
- Collections: titled perkins, reynolds, newsmaker. Ten still need titles read off the legacy index pages.
- Decisions: ranchos are Organizations (the land grant), with a Place only where a site survives (Rancho Camulos). Communities are the term, not Neighborhoods or Townships. Soledad Township is a term inside Communities.
- Open: sleepy-valley and lake-hughes Places undecided. 20 relations pointed at deleted Places and need re-pointing. No placeType field exists yet.
- Next, once the drive is on Reggie: full inventory, collection titles, entity-first article import, Place prose, map coordinates.

## 2026-09-17 (Claude)

- Found and fixed a root-cause bug: 8 of 12 entry types had no Title field in their layout, so Craft silently discarded titles on save. Added the native Title element to article, militaryProfile, group, organization, obituary, place, collection, event. Person keeps titleFormat {fullName}.
- War Memorial: added 13 service record fields, parsed all 36 records from stored legacyHtml, populated branch, rank, unit, home of record, dates, incident, awards, burial, and narrative. Body replaced with narrative.
- Communities: renamed the Neighborhood category group to Communities (handle stays neighborhood), added body, aliases, type, and lat/lng fields for the planned community map.
- Places: converted 22 community stubs to Community terms, deleted 3 stubs duplicating Organizations, titled 12 real Places and assigned communities. Places went 39 to 14.
- Collections: titled perkins, reynolds, newsmaker. Ten still need titles read off the legacy index pages.
- Decisions: ranchos are Organizations (the land grant), with a Place only where a site survives (Rancho Camulos). Communities are the term, not Neighborhoods or Townships. Soledad Township is a term inside Communities.
- Open: sleepy-valley and lake-hughes Places undecided. 20 relations pointed at deleted Places and need re-pointing. No placeType field exists yet.
- Next, once the drive is on Reggie: full inventory, collection titles, entity-first article import, Place prose, map coordinates.

## 2026-09-17 (Claude, branch templates-batch-1)

- Agent: Claude
- Date: 2026-09-17
- Built entry and index templates for five sections on the articles/_entry pattern: base layout, scv-extra-css in the head block, breadcrumbs, h1, era and period chips, community chips, body through _partials/prose, right sidebar of .scv-box relation panels with a cite box. Ten template files: places, organizations, groups, events, war-memorial.
- War Memorial entry follows the WordPress version: a Service record panel (branch, rank, specialty, unit, base, operation, plus start of tour, length of service, service number) and an Incident panel (date, location), then a Life panel and an Awards panel. Index groups by wmConflict in chronological order (World War I, World War II, Korean War, Vietnam War, War on Terror), with any unlisted conflict appended and a "Conflict not recorded" bucket.
- Verified every field handle against config/project/ before use, then re-audited every handle each template reads against that entry type's field layout. All clear.
- Tested: all 78 entry pages across places, organizations, groups, events, and warMemorials return 200, plus all five indexes, the filter variants, and bogus filter values.

### Decisions

- Nathan chose to rebuild all five sections in the scv-extra-css system rather than keep two visual systems side by side. The repo had two: main.css (.scv-article-wrap, .scv-sidebar-card, .scv-tag) used by the old places and organizations templates, and _partials/scv-extra-css (.scv-doc, .scv-grid, .scv-body, .scv-box, .scv-chip) used by articles. The brief specified the second.
- The Leaflet location map from the old organizations template was carried forward, not dropped, and added to places on the same terms. It loads only when the entry has both lat and lng, with the stylesheet in the head block and the script in the foot block.
- Filter query parameter stays `neighborhood` on the places and organizations indexes, matching the category group handle, even though the group now displays as Communities.
- Legacy links use `https://scvhistory.com{{ legacyUrl }}` with no added slash, since stored values already begin with one.

### Fixed along the way

- organizations/_entry.twig was returning 500 on every entry: `Invalid transform handle: cardThumb`. The rebuild does not use an image transform.
- persons/_entry.twig and persons/index.twig were returning 500: both extended `_layout`, which does not exist in this repo. Changed to `_layouts/base` and moved the crumbs block into the content block so the breadcrumb is not silently dropped. Nathan approved this as a minimal fix only, no redesign.
- The old organizations template built legacy links as `https://scvhistory.com/{{ orgLegacyUrl }}`, producing a double slash.

### Skipped, and why

- No hero image on place, group, or event entries. `featuredImage` is not in those three field layouts. Only organization, warMemorial, person, and article have it. The panel is in place and will appear on its own if the field is ever added.
- No era, period, or community chips on War Memorial entries. `historicalEra`, `historicalPeriod`, and `neighborhood` are not in the warMemorial field layout. The conflict chip is the only one. This caused the one render failure during the build and was removed rather than worked around.
- Places index does not group by place type and Groups index does not group by group type. Only three category groups exist (historicalEra, historicalPeriod, neighborhood). Place Type, Group Type, Event Type, and Person Subject are described in DATA-ORGANIZATION.md but not built. Places and organizations filter by community; groups and events filter by era; events group by historical period.
- Persons templates still render unstyled. Their class names (.layout, .box, .body, .chip, .crumbs) belong to neither stylesheet. They no longer error, but they need a real rebuild.
- Events are listed as a later-phase type in DATA-ORGANIZATION.md section 3. Built here because the task named them directly.

### Data problems noticed, not touched

No database changes were made. Flagging for a content session:

- Duplicate entries: places has both `beales-cut` and `beales-cut-stagecoach-pass` with the same title; warMemorials has both `terror-rudyacosta` and `rudy-alexander-acosta` for Rudy Alexander Acosta.
- Place `sleepy-valley` still has a NULL title and falls back to its slug in the index.
- Place `lake-hughes` has the title "Lake Huges", likely a typo for Lake Hughes.
- Four War Memorial narratives are truncated mid sentence by the import: ww2-edwardcontreras, terror-brianprosser, korea-henryacuna, ww2-johnward.
- One War Memorial record has no wmConflict and lands in "Conflict not recorded".
- Most Place bodies are the one line import placeholder ("X is a named place in the Santa Clarita Valley historical archive"), so place pages are thin until real prose lands.

### Blockers

- None.

### Next

- Nathan reviews the five sections at scvhistory.ddev.site before this branch merges. Nothing pushed, nothing merged.
- Decide whether persons gets a full rebuild on the same pattern.
- Add the missing category groups (Place Type, Group Type, Event Type) if the indexes should group by type.
- Resolve the duplicate places and war memorial records and the truncated narratives.

## 2026-09-17 (Claude, branch templates-batch-2)

- Agent: Claude
- Date: 2026-09-17
- One sidebar and page system across every entry page, in the current design language. Reference was the persons entry page and the persons and places indexes.

### Shared sidebar partials

New `templates/_partials/sidebar/`:

- `box.twig`: white box, 3px gold top rule, small uppercase Jost heading. Used with `{% embed %}` so the caller supplies the body.
- `related-list.twig`: related entries with a 44px square featuredImage thumbnail, initials fallback in cream (two initials for people, first letter otherwise), title link, and a one-line subtitle. Subtitle by section: persons gives occupation, events give date, places give community, everything else none.
- `location.twig`: Leaflet 1.9.4 map 200px tall, scrollWheelZoom off, then Established, Address and Coordinates rows. Takes lat, lng, address, established and mapId, so places and organizations share it.
- `meta.twig`: last updated, plus read time and word count on articles.
- `cite.twig`: Chicago style, using author, collection, publish date and url.
- `external.twig`: website, Wikipedia, Find a Grave, CHL, SCV landmark, Archive.org and legacy links, only the ones with values. Handles differ per section, so the caller passes values rather than the partial guessing a handle.

### Design language

`_partials/scv-extra-css.twig` rewritten. Playfair Display headings, Jost labels, Public Sans body. Navy #17254C, gold #C4A031 and #A9842B, cream #FDF7EA, borders #E2E4E8 and #EFE6D0, page #F6F7F9. Cormorant Garamond, Inter and the old #1a2744 and #b8860b palette are gone from the file. The Google Fonts link moved into the partial so entry templates stop repeating it. Verified in the browser: computed styles match the spec exactly.

### Entry pages rebuilt

articles, places, organizations, groups, events, war-memorial, collections, and a new obituaries entry. Each: cream band with breadcrumbs, kicker, title, aliases and key facts; era, period and community chips; featured image hero below the band when the section has the field; body in a white card; right sidebar built only from the partials.

- Places get the location box first in the sidebar.
- War memorial keeps the service record and incident panels as boxes, plus life and awards.
- Articles keep the collection pager, the author bio and the in-this-collection list, and gained events, related articles and the editor.
- Every relation the previous templates showed is preserved, including the reverse lookups on depictsPlace, subjectOrganization, publishedBy, personGroups, subjectGroup and articleEvents.

### Index pages rebuilt

organizations, groups, events, war-memorial, collections, and a new obituaries index. Cream band with stats, sticky filter bar where a filter makes sense, card grid with images and initials fallback. Organizations filter by community, groups and events by era, war memorial by conflict, obituaries by era. Collections has no filter so it has no bar.

### Verified

- All 151 entry pages across nine sections return 200, plus 10 indexes and 6 filter variants including bogus filter values.
- Every field handle re-audited against its entry type layout in `config/project/`. The only out-of-layout reads are `featuredImage`, all guarded with `is defined`.
- No horizontal overflow: `scrollWidth` equals `innerWidth` on ten pages at 1280px and 390px.

### Fixed along the way

- Obituaries had no templates at all. Every obituary URL and `/obituaries` returned 404. Both now exist.
- `{% embed ... only %}` does not inherit outer variables, which broke the first place page that had a community. Variables are now passed in explicitly.

### Skipped, and why

- No hero image on place, group, event, article, collection or obituary entries, and no real card image on those indexes. `featuredImage` is only in the person, organization and warMemorial field layouts. Every reference is guarded, so images appear on their own if the field is added.
- No era, period or community chips on war memorial entries. `historicalEra`, `historicalPeriod` and `neighborhood` are not in the warMemorial layout. The conflict chip is the only one.
- Indexes still cannot group by type. Only three category groups exist (historicalEra, historicalPeriod, neighborhood). Place Type, Group Type, Event Type and Person Subject are described in DATA-ORGANIZATION.md but not built.
- The homepage, articles index, persons index and places index were left alone. They are already in this design language with their own hp, ap, pp and pl prefixes. They still use `.scv-band` and `.scv-band-in`, which the rewrite keeps.
- `militaryProfiles` still has no template. It was not in scope.

### Data problems noticed, not touched

No database changes were made.

- The one obituary body still carries WordPress import artifacts: `[caption]` shortcodes, raw img tags, and absolute links to `wordpress-1656314-6593552.cloudwaysapps.com`. The page renders, but the body needs a cleanup pass before launch.
- Everything flagged in the templates-batch-1 entry is still open: duplicate `beales-cut` places, duplicate Rudy Alexander Acosta war memorial records, the NULL title on `sleepy-valley`, "Lake Huges", four truncated war memorial narratives, and the one-line placeholder Place bodies.

### Blockers

- None.

### Next

- Nathan reviews the eight entry types and six indexes at scvhistory.ddev.site before this branch merges. Nothing pushed, nothing merged.
- Clean the obituary body, then import the rest of the obituaries.
- Decide whether militaryProfiles gets templates or is folded into war memorial.
- Add the missing category groups if indexes should group by type.

## 2026-09-17 (Claude, branch templates-batch-3)

- Agent: Claude
- Date: 2026-09-17
- A Communities section with a boundary map, plus one shared map implementation across the three map indexes.

### Category URLs

`scripts/import/setup_community_urls.php` sets the Communities group (handle `neighborhood`) to hasUrls true, uriFormat `communities/{slug}`, template `communities/_entry`, for every site the group is enabled on. Eval style, no opening tag, safe to run twice, prints settings before and after plus sample URLs. **Nathan runs it.** No `config/` was edited by hand.

Until it runs, `/communities/{slug}` returns 404 and `term.url` is null. Both templates fall back to `url('communities/' ~ slug)` so links and citations are already correct.

### Boundary data

`web/data/communities.geojson`, 57 KB, 15 polygons.

- Source: Los Angeles County Enterprise GIS, eGISBOS Countywide Statistical Areas, ArcGIS item `3abf2449cc054d72ab80e8f1968e5d94`.
- Retrieved in WGS84 with `maxAllowableOffset=0.0002` and 5 decimal places.
- Each feature carries `properties.slug` matching the Craft category slug, plus `source_name`, `city_type` and a `note` where the polygon needs one.
- Source URL, attribution, retrieval date, the exact query and the caveats are in the file's `metadata` member and repeated below.
- The ArcGIS item carries no licence statement, so `metadata.license` is marked `NEEDS_VERIFICATION`. Confirm LA County's open data terms before public launch.

**Got a polygon (15):** acton, agua-dulce, bouquet-canyon, canyon-country, castaic, lake-hughes, newhall, placerita-canyon, san-francisquito-canyon, sand-canyon, santa-clarita, saugus, stevenson-ranch, val-verde, valencia

**No polygon (20):** camulos, castaic-junction, fair-oaks-ranch, fillmore, frazier-park, haskell-canyon, hasley-canyon, lebec, mentryville, mint-canyon, mojave-desert, pico-canyon, piru, potrero-canyon, ravenna, saugus-valencia, soledad-canyon, soledad-township, tejon, towsley-canyon

Those 20 split three ways: canyons and historic townsites with no official boundary; Ventura County (camulos, fillmore, piru) and Kern County (frazier-park, lebec, tejon), which an LA County dataset does not cover; and saugus-valencia, which is our own compound term with no single CSA. No polygon was invented for any of them.

Two caveats worth knowing:

- The Newhall, Saugus, Valencia and Canyon Country CSAs cover only the **unincorporated remnants** of those communities. The bulk of each sits inside the City of Santa Clarita polygon. A reader hovering "Newhall" is seeing a fragment, not the historic community.
- san-francisquito-canyon uses the combined CSA "San Francisquito Canyon/Bouquet Canyon", which overlaps the separate bouquet-canyon polygon.

### Templates

`templates/communities/index.twig`: cream band with the community count and a live count of how many have boundaries, a 520px Leaflet 1.9.4 map from cdnjs, then a card grid of all 35 communities with name, type, alias line and counts. Polygons draw navy `#17254C` at 12 percent fill with a navy outline, turn gold `#C4A031` at 32 percent on hover, carry a tooltip with the name and counts, and navigate to the community on click.

`templates/communities/_entry.twig`: cream band with name, aliases and type; body through `_partials/prose`; a 320px map of that community's polygon or its pin; then every related record grouped by section across people, places, organizations, groups, events, articles, war memorial and obituaries. Sidebar uses the batch 2 partials: meta, cite, neighbouring communities, and a per-section count box.

The GeoJSON is fetched at runtime rather than inlined into every render. Twig has no `file_exists`, so the neighbours list is server rendered from `templates/_data/community-neighbors.json`, generated from the same GeoJSON.

Communities was added to the nav in `_layouts/base.twig` after Places. That is the only base.twig edit.

### Neighbouring communities

Derived from the polygons: two communities are neighbours when any boundary vertex of one lies within 250 m of a boundary segment of the other. This is a proximity test on generalised geometry, not a topological adjacency computation, so it can miss a narrow touch or include a near miss. The method note travels with the data in both files. Shapely was not available and installing packages needs approval, so this was done in pure Python.

### Refactor

`_partials/map-index.twig` now holds the map CSS, the map card markup and the Leaflet wiring that `places/index.twig` and `organizations/index.twig` each carried a copy of. Both are about 80 lines shorter. Behaviour is identical: same pins, same card and pin selection, same hint line, same popups, same fit links. Differences are parameters: `hint`, `fitAllLabel`, `showFitValley`, `initialValley`, and the new `polygonsUrl`, `polygonMeta`, `boundaryCount`, `mapHeight`.

Two fixes the move required:

- The shared script now sits before the card grid rather than after it, so it is wrapped in `DOMContentLoaded` and binds card handlers whatever the order. Without this the card clicks would have silently stopped working.
- Leaflet's stylesheet is registered with `registerCssFile` instead of a hand written link tag, so the partial carries its own dependency.

`cite.twig` gained an optional `url` override.

### Verified

- All 151 entry pages, all 11 indexes including the new `/communities`, and all 35 community pages return 200. Community pages were rendered through a temporary harness template, since category URLs are not on yet; the harness was deleted before committing.
- In the browser: 15 polygons drawn, navy fill at 0.12, gold at 0.32 on hover and back on mouseout, tooltips reading for example "Castaic / 8 people · 2 places · 13 articles", polygon click navigating to the community, the boundary counter filling to 15, map 520px.
- Refactor checked by diffing rendered output before and after, then in the browser: 15 pins and 16 cards on places, 14 and 14 on organizations, card clicks intercepted and selecting rather than navigating.
- Every category field handle re-audited against the `neighborhood` group layout. All six exist and all reads are guarded.

### Blockers

- None, but two things need Nathan.

### Needs Nathan

1. **Run the URL script** on **MacBook**, then commit `config/project/`:

```
ddev craft exec "eval(file_get_contents('scripts/import/setup_community_urls.php'))"
```

2. **`orgLat` and `orgLng` are set to `decimals: 0`.** Every organization pin rounds to a whole degree, so Rancho Camulos plots at 34, -119 instead of 34.407, -118.753, roughly 30 km out. This predates this branch; it came in with the organizations map. Fixing it means changing the two field settings and re-running `set_org_coords.php`. Not done here because the brief allowed no config changes beyond the URL script.

### Data problems noticed, not touched

No database changes were made.

- All 35 community terms have completely empty field content: no body, no aliases, no type, no coordinates. So community pages currently show a title, the related records and nothing else, and the map shows only the 15 polygons. The 20 communities without a polygon will stay off the map until `communityLat` and `communityLng` are filled.
- `warMemorials` has no `neighborhood` field, so its count is structurally always zero. The templates query it and simply omit the line, but no war memorial record can be filed under a community until the field is added.
- Everything flagged in batches 1 and 2 is still open.

### Next

- Nathan runs the URL script, then reviews `/communities` and a few community pages.
- Decide on the `orgLat`/`orgLng` precision fix.
- Populate community bodies, types, aliases and coordinates. Coordinates are what unlock the remaining 20 pins.
- Decide whether `neighborhood` should be added to the warMemorial layout.

## 2026-09-17 (Claude, branch templates-batch-4)

- Agent: Claude
- Date: 2026-09-17
- One record design across every entry page, shared record tools, sub-city community boundaries, and a community coordinates script.

### Merge and push

Not done. AGENTS.md says never push to main, and deploy.yml redeploys Cloudways on any push to main. There is also a gap: the workflow only runs `git pull origin main`, so production would get the community templates without the applied project config and `/communities/{slug}` would 404 there until `project-config/apply` runs on the server. Nathan chose to merge and push himself. templates-batch-4 was branched from templates-batch-3, which is identical to what main becomes, so it still merges cleanly.

### Record tools as shared partials

New `templates/_partials/record/`:

- `css.twig`: the record design system lifted from the approved war memorial page. Cream band, kicker, h1, italic subtitle, labelled facts, chips, optional portrait, two column main, cream sidebar boxes, the `.rec-rows` label/value grid.
- `tools.twig`: read time, word count, updated date, Save / Print, Listen. Listen uses SpeechSynthesis and stays hidden when the API is missing. Read time is omitted under 20 words.
- `cite.twig`: Cite this record, with Chicago, MLA 9 and APA 7. All three are built in Twig so the citation survives with JavaScript off; the toggles only swap which is visible. Chicago is default. Copy reads Copied for two seconds.
- `print.twig`: hides header, nav, footer, sidebar, tools and buttons, drops to one column, prints the Chicago citation at the end.

All nine entry templates use them. War memorial was migrated onto them too and no longer carries its own tools row, cite box, copy script or print rules; it keeps only its unit seal box and its footer CTA.

### Fixed a live 500 on the reference page

`templates/war-memorial/_entry.twig` used `{% set v = (h) => ... %}` and then called `v('handle')`. Twig 3.21 cannot call a variable holding an arrow function, so every war memorial page was erroring with `Unknown "v" function`. It had been serving from a stale compiled template cache and broke the moment the cache cleared. Both that template and the person record now build a plain `F` dictionary of guarded field values.

### Person record

Portrait at left with an initials fallback, kicker PEOPLE with the era, occupation subtitle, BORN, DIED and RESIDENCE facts, chips for period, community and group. Main column is tools, prose, cite. Sidebar: Family and relationships (child of, parent of, sibling of, spouse of, each a 44px round thumb with occupation or life dates, plus a navy War Memorial badge when a memorial's `wmRelatedPerson` points at them), Organizations, Groups, Places, Written by, Articles about, Obituary, External. Every box conditional.

### The other seven

articles, places, organizations, groups, events, obituaries and collections rebuilt to the same pattern, keeping all batch 2 content. Places keep the location map box first. Collections keep the ordered chapter list. Articles keep the previous and next pager, the in this collection list and the author bio. Organizations are the only one of the seven whose layout has featuredImage, so they are the only one with a band portrait.

### Sub-city community boundaries

The City of Santa Clarita publishes no community or planning-area layer. Searched ArcGIS Hub and ArcGIS Online for city owned content, checked the one City of Santa Clarita Layers service (Oak Trees, General Plan, Zoning) and probed four likely city portal hostnames, none of which resolve. So the ZCTA fallback applies.

Newhall, Saugus, Valencia and Canyon Country now come from 2020 Census ZIP Code Tabulation Areas via TIGERweb: 91321, 91350, 91354 + 91355, 91351 + 91387. They replace CSA fragments that covered only the unincorporated remnant and were 8 to 31 times smaller. The city wide santa-clarita polygon is kept.

Every feature carries `properties.method`, `csa` or `zcta`, shown as a one line source note in the map tooltip and under the map on the community page. That note supersedes the fragment caveat that was planned for those four, which is no longer true of them.

Check against the county shapes: Stevenson Ranch ZCTA 91381 is within 10 percent of its CSA, which supports the method. Castaic ZCTA 91384 is about a third of its CSA, because the county area sweeps in undeveloped backcountry. Both keep their CSA polygon.

Adjacency recomputed on the new geometry and is markedly better. Newhall now neighbours Canyon Country, Placerita Canyon, Saugus, Stevenson Ranch and Valencia rather than only Santa Clarita and Stevenson Ranch.

No boundary was hand drawn. The file is 84 KB, well under the 300 KB budget.

### Community coordinates

`scripts/import/set_community_coords.php`, eval style, dry run by default behind `$APPLY`. Nathan runs it.

- 15 from the area weighted centroid of that community's polygon.
- 11 from Wikipedia, with the article URL kept beside each value: camulos, castaic-junction, fillmore, frazier-park, hasley-canyon, lebec, mentryville, pico-canyon, piru, soledad-canyon, tejon.
- 3 skipped on purpose as areas rather than points: mojave-desert, saugus-valencia, soledad-township.
- 6 left alone with no coordinates: fair-oaks-ranch, haskell-canyon, mint-canyon, potrero-canyon, ravenna, towsley-canyon. None has a polygon, and none has a Wikipedia article carrying coordinates. The USGS GNIS site is a single page app with no public API, so it could not be queried. Nothing was estimated by hand.

### Verified

- All 151 entry pages, 11 indexes and 35 community pages return 200.
- Every field handle re-audited against its entry type layout. The one out of layout read is `historicalEra` on war memorial, guarded with `is defined`.
- In the browser: cite toggles switch between the three styles with Chicago default, tools row reads "5 min read · 834 words · Updated", Listen unhides, conditional boxes render only when they have content, 15 polygons draw with 11 csa and 4 zcta, tooltips carry the source note.

### Fixed along the way

- The Listen button never appeared. The tools partial renders above the prose, so its script ran before the element it reads existed. Now bound on DOMContentLoaded.
- A rejected clipboard write no longer leaves a dangling rejection.

### Blockers

- None.

### Needs Nathan

1. Merge templates-batch-3 and templates-batch-4 to main and push, then run `project-config/apply` on Cloudways, or `/communities/{slug}` will 404 in production.
2. Run the coordinates script on **MacBook**, dry run first:

```
ddev craft exec "eval(file_get_contents('scripts/import/set_community_coords.php'))"
```

### Data problems noticed, not touched

No database changes were made.

- `wmRelatedPerson` has zero relations, so no war memorial is linked to a person. The War Memorial badge on the person record is wired and tested but cannot appear until those links exist.
- All 35 community terms still have empty body, aliases and type. The coordinates script fills coordinates only.
- The one obituary body still carries WordPress import artifacts.

### Next

- Populate community bodies, aliases and types.
- Link war memorial records to their Person records so the badge appears.
- Find coordinates for the six unresolved communities from a source with real data, ideally the GNIS domestic names file rather than the web app.

## 2026-09-17 (Claude, branches templates-batch-5 and templates-batch-6)

- Agent: Claude
- Date: 2026-09-17

### templates-batch-5, partial

Two items of the quality pass landed. The rest is outstanding, listed below.

**Legacy stylesheets stripped.** main.css went from 2131 lines to 34 and layout.css from 60 to 80. Of the 350 classes those two defined, only 19 were still referenced anywhere, all header, nav, footer, lightbox, band or map; the rest styled templates that no longer exist. Real deviations fixed in the chrome: the header carried a 2px #b8860b bottom border, the footer heading, links and tagline were still set in Inter, footer text used the old rgba(245,230,192,...) cream, and the footer column was 1280px against a 1240px header so the gutters did not line up. base.twig no longer loads the Cormorant Garamond and Inter webfonts. No occurrence of Cormorant, Inter, #1a2744 or #b8860b remains under templates/.

**Cite this record moved into the sidebar.** The brief sets the sidebar order as portrait or unit box, then Cite this record, then label/value boxes, then relations, then External. Cite had been in the main column, which is where the approved war memorial reference put it; Nathan confirmed the move. cite.twig gained a sidebar variant that renders inside a .rec-box and stacks the toggles, citation and Copy for a 340px column.

**Still outstanding from batch 5**, none of it started:

- military-profiles/_entry.twig and index.twig. The section has no template at all and is the last one missing.
- templates/404.twig, a search results template at templates/search/index.twig, and scripts/import/setup_search_route.php.
- The internal link crawl for 404s, 500s, links to the old WordPress host and stray scvhistory.com links.
- Head and metadata: title tags and meta descriptions. Nathan settled the meta description as roughly the first 160 characters of the body, cut at a word boundary.
- Community boundaries drawn on the places and organizations maps.

Note that the batch 5 brief was truncated: item 4 ended mid sentence at "drawn from the first 7" and the list jumped straight to an item numbered 7, so items 5 and 6 never arrived.

### templates-batch-6

**Fields verified.** recordImages and recordDocuments are Assets fields present on all twelve entry types: article, collection, document, event, group, militaryProfile, obituary, organization, person, photograph, place, warMemorial. Neither is on the neighborhood category group, so community pages cannot carry them. Every type also has a top and bottom editor note, and there is a third handle pair the brief did not mention: militaryProfile uses mpWebmasterNoteTop and mpWebmasterNoteBottom.

**New partials in _partials/record/:**

- `images.twig`: PHOTOS section under the prose, gold rule and label above a grid of square thumbnails, each opening the full image in a `<dialog>` lightbox with caption and credit beneath. No library. Caption from the asset title, credit from its alt.
- `documents.twig`: DOCUMENTS sidebar box, each recordDocument a link with a PDF icon, its title and its file size, opening in a new tab. Sits after the relation boxes and before External.
- `note.twig`: cream box with a 3px gold left rule, 15px text, gold links. Empty notes render nothing.

`_partials/prose.twig` now replaces a line consisting only of `[image:N]` with a figure floated right at 300px, caption and credit beneath in italic 13.5px grey, unfloated below 640px. A token pointing at an image that is not there is dropped rather than printed. images.twig reads the same tokens, so an image placed inline is left out of the Photos grid and nothing appears twice.

All nine record templates wired: top note between the tools row and the prose, prose with the images passed in, Photos section, bottom note, and the documents box in the sidebar. Each passes the note handle its own type carries.

Where featuredImage is empty or absent the first record image becomes the hero. Six of the nine templates had no portrait markup at all, since their types have no featuredImage field, so the band now takes a portrait and drops the single column modifier when there is an image.

**Verified.** All 151 entry pages, 11 indexes and 35 community pages return 200. The partials were exercised against real assets through a temporary template, since the fields hold no data: `[image:2]` produced one floated figure, `[image:9]` was dropped, the Photos grid showed the remaining three, the dialog and both notes rendered, and the documents box listed extension and size. That template was deleted before committing.

### Blockers

- None.

### Data problems noticed, not touched

No database changes were made.

- recordImages and recordDocuments have zero relations across the whole site, so no Photos section, no documents box, no inline image and no hero fallback can appear until Nathan populates them. All of it is wired and tested, just unfed.
- wmRelatedPerson still has zero relations, so the War Memorial badge on person records cannot appear.
- All 35 community terms still have empty body, aliases and type.

### Next

- Finish the batch 5 items listed above.
- Populate recordImages and recordDocuments, then re-check a record page with real photos.
- Confirm whether community terms should get recordImages and recordDocuments too; they are the only content type without them.

## 2026-09-17 (Claude, batch 5 continued, on templates-batch-6)

- Agent: Claude
- Date: 2026-09-17
- The rest of the quality pass. Stylesheet strip and Cite move had already landed.

### Missing pages

`military-profiles/_entry.twig` and `index.twig` on the record pattern. The section holds no entries, so the entry page renders against the 42 field layout: service record, life, awards, the four family groups behind their toggles, relation boxes, documents and External. Its note handles are `mpWebmasterNoteTop` and `mpWebmasterNoteBottom`, a third pair beyond the two the brief named. It has no featuredImage, so the hero comes from the first record image.

`search/index.twig` groups results by section with a thumbnail, title and subtitle per row, and searches community terms too. **No route script was needed and none was written**: Craft resolves `templates/search/index.twig` at `/search` on its own and `config/routes.php` is empty. The homepage form now targets `/search` and no longer renders results inline.

`404.twig` with the search box and a card per section with live counts. It only takes effect with devMode off; the dev environment has devMode on so Craft still shows its own debug page for a missing URL, and the template renders at `/404`.

### Head and metadata

`_partials/head/meta.twig`, included once from base.twig, gives every page a title of "record title | section | site name", a canonical, a meta description of the first 160 characters of the body cut at a word boundary, Open Graph and Twitter card. The OG image is featuredImage, then the first record image, then the site seal. Index pages set `metaDescription` at template top level, reusing the lede already on the page.

Rewrote `_partials/jsonld/person.twig`. It was stale from April and had never been included anywhere: it referenced `person.sameAs` and a roles matrix that were never built, and `person.sameAs is defined` reported true and then threw `Calling unknown method: Entry::sameAs()`. It is now built with `json_encode` rather than hand written JSON and touches only handles that exist. Wired into persons and parses on all 33 person pages.

### Link and render check

Crawled all 200 pages and every internal link. Two template level defects found and fixed.

**Eight footer links pointed at pages that do not exist**, so every page carried eight 404s: `/about`, `/contact`, `/permissions`, `/photo-credits`, `/newsletter`, `/submit`, `/nonprofit`, `/privacy`. Removed and the footer rebalanced. **These are pages still to build.**

**Legacy URLs were concatenated blindly.** The data holds three shapes: `/scvhistory/lw3730.htm`, `scvhistory.com/scvhistory/lw3730.htm`, and, where a website landed in the legacy field by mistake, `sangabrielmission.org`. The last produced `https://scvhistory.comsangabrielmission.org` on the San Gabriel mission page. `_partials/legacy-url.twig` now resolves all three, applied in all ten places.

**Left for Nathan, content level, no database changes made:**

- `organizations/mission-san-gabriel-arcangel` has a website URL (`sangabrielmission.org`) sitting in its legacy URL field. The template now renders it correctly, but the value is in the wrong field.
- One broken internal link: `/person/pedro-fages/` in the body of `articles/chapter-9-the-trail-blazer`, a WordPress era link in Leon's prose.
- Three links to the old WordPress host `wordpress-1656314-6593552.cloudwaysapps.com`: two in `obituaries/in-memoriam-henry-clay-wiley-1829-1898` and one in `persons/remi-nadeau-i`.
- Of 98 links to scvhistory.com, 96 are deliberate "View on legacy site" links. Two are inside body prose on `events/northridge-earthquake`.
- `HenryMayo.com` appears as a link host with inconsistent casing.

### Accessibility

Measured the palette rather than assuming. **Gold `#A9842B` on cream is 3.27:1**, which fails AA for the 11 to 13px labels it was used for, so gold text on light backgrounds is now `#8F6E22` as specified. A second failure the brief did not mention: **`#8A919E`, the label column in every sidebar box, is 2.97:1 on cream**; it is now `#5B6472`, already in the palette at 5.60:1.

**Worth knowing: `#8F6E22` on `#FDF7EA` measures 4.45:1, still just under the 4.5 AA threshold for text below 18.66px.** `#7A5C1B` gives 5.83:1 and clears it. Left at `#8F6E22` as instructed; say the word and it is a one line change.

Every image already carried alt, with empty alt on decorative thumbnails. Every map now has `role="img"`, an accessible name and a screen-reader sentence pointing at the text equivalent below. Added a global `:focus-visible` ring, a skip link, and made `#content` focusable.

### Performance

The webfonts were requested **up to three times per page**, from base.twig plus two partials plus six index templates. One superset request now lives in base.twig; the eight duplicates are gone. Leaflet was already conditional and loads only on pages with a map, confirmed against the actual link and script tags: three stylesheets and one script on a record page, four and two on a map page. Removed three dead `.leaflet-popup` rules that shipped to every page without a map.

### Listen player

Replaced the button in `_partials/record/tools.twig`. The record is split into sentences and spoken one at a time, which is what makes progress real: the bar fills as sentences complete, the sentence being read gets a soft cream highlight and is scrolled into view, and clicking or dragging the bar restarts from that sentence. Elapsed and estimated total start from 165 words per minute, corrected against real elapsed time as sentences finish. Speed at 0.8x, 1x, 1.25x, 1.5x. Prefers an English voice, hides itself when speechSynthesis is missing, cancels on `beforeunload` and `pagehide`. The bar is a real slider: focusable, arrows seek by a sentence, space and enter toggle. Verified on a person record: 71 sentences, estimate 5:17 for 834 words, seek to half fills the bar to 49 percent and moves the highlight.

### Community boundaries on the maps

`map-index.twig` gained an outline mode. With `outlineUrl` set, every polygon draws as a bare navy 1px outline under the pins with no fill; hover fills at 8 percent and names the community; a click goes to `?community={slug}`, the same filter the chips use. With `activeSlug` set, that community draws gold at 2px and the map fits it rather than the pins. Communities with no polygon are absent from the file so nothing is drawn. The Communities page passes no `outlineUrl` and keeps its filled treatment.

### Verified

All 151 entry pages, 15 indexes and route variants, and 35 community pages return 200.

### Left for Nathan

- Build the eight footer pages, or confirm they should stay off the site.
- The content level link problems listed above.
- Five partials are now unused and can go, but AGENTS.md says ask before deleting: `_partials/cite-article.twig` (0 bytes), `_partials/search-form.twig`, `_partials/sidebar/external.twig`, `_partials/sidebar/location.twig`, `_partials/sidebar/related-list.twig`. `_partials/sidebar/box.twig`, `cite.twig` and `meta.twig` are still used by the communities page.
- Decide on `#8F6E22` versus `#7A5C1B` for small gold text on cream.
- `recordImages`, `recordDocuments` and `wmRelatedPerson` still have zero relations sitewide.

## 2026-09-17 (Claude, branch templates-batch-7)

- Agent: Claude
- Date: 2026-09-17

### Small-text gold now clears AA

`#8F6E22` measured 4.45:1 on cream, short of the 4.5 AA threshold for text under 18.66px. Small gold text is now `#7A5C1B`: 5.83:1 on cream, 6.22:1 on white, 5.81:1 on the page grey.

The swap was decided per declaration block, reading the font-size in the same rule, plus the six inline link colours in the empty-state messages, which sit in 16px text. Four display uses keep `#8F6E22`, all Playfair numerals at 22 to 30px where AA only asks 3.0: the era and feature numerals on the homepage, the article list numeral, and the collection chapter numeral.

### Pages section

`scripts/import/setup_pages_section.php`, eval style, dry run behind `$APPLY`, safe to run twice. **Nathan runs it.** It creates:

- section `pages`, channel, uriFormat `{slug}`, template `pages/_entry`
- entry type `page` with body, webmasterNoteTop, webmasterNoteBottom, featuredImage, recordImages, recordDocuments, all six verified present
- the eight entries with empty bodies: About, Contact, Permissions, Photo Credits, Newsletter, Submit a Photo or Article, Nonprofit, Privacy Policy

No prose is written by the script. The copy is Nathan's to write in the control panel.

`templates/pages/_entry.twig` on the record pattern with no relation boxes, since a page is prose rather than a record with connections: cream band, tools, top note, prose, images, bottom note; sidebar of cite, a search box and the other pages. It shows "This page has not been written yet" while a body is empty.

### Footer restored

Back to the original three columns and the bottom bar: Browse (By Era, By Collection, People, Obituaries), About (About, Contact, Permissions, Photo Credits, SCV Historical Society), Connect (Facebook Group, Newsletter, Submit a photo/article), and Nonprofit, Permissions, Privacy Policy along the bottom.

By Era and By Collection needed somewhere real to point, so the articles index gained a `view` parameter. `view=era` groups the same articles by historical era, `view=collection` keeps the existing per series grouping, and a GROUP BY chip row switches between them. Verified: `?view=era` renders Spanish Colonial, Mexican Rancho Era, American Frontier and Other articles as group headings.

**The eight page links 404 until Nathan runs the section script.** That is expected and resolves on the first run.

### Unused partials deleted

Confirmed zero references for each, then removed: `_partials/cite-article.twig` (an empty file), `_partials/search-form.twig`, `_partials/sidebar/external.twig`, `_partials/sidebar/location.twig`, `_partials/sidebar/related-list.twig`. `sidebar/box.twig`, `cite.twig` and `meta.twig` stay, since the communities page still uses them.

### Content link fixes

`scripts/import/fix_bad_links.php`, eval style, dry run behind `$APPLY`. **Nathan runs it.** Every rewrite is anchored on the exact stored URL, so a second run is a no-op.

All four link targets were checked against Craft before the script was written, and all four exist:

- `/person/pedro-fages/` in `articles/chapter-9-the-trail-blazer` to `/persons/pedro-fages`
- `wordpress-1656314.../article/henry-clay-wiley/` to `/articles/henry-clay-wiley`
- `wordpress-1656314.../article/surveyors-map-showing-lyons-station/` to `/articles/surveyors-map-showing-lyons-station`
- `wordpress-1656314.../person/tiburcio-vasquez/` to `/persons/tiburcio-vasquez`

The strip-the-anchor-keep-the-text path is implemented for targets that do not exist, but none of these four needs it.

The script also moves `sangabrielmission.org` out of the legacy URL field on `organizations/mission-san-gabriel-arcangel` and into `orgWebsite`, clearing the legacy field. It leaves `orgWebsite` alone if something is already there.

The two scvhistory.com links in `events/northridge-earthquake` are reported and left untouched, since a reference to the legacy site may well be deliberate. **Nathan decides.**

### Verified

All 151 entry pages, 16 index and route variants, and 35 community pages return 200.

### Needs Nathan

Two scripts to run on **MacBook**, both dry run first, then with `$APPLY = true`:

```
ddev craft exec "eval(file_get_contents('scripts/import/setup_pages_section.php'))"
ddev craft exec "eval(file_get_contents('scripts/import/fix_bad_links.php'))"
```

Then `project-config/write` and commit `config/project/` for the first one. Write the copy for the eight pages, and decide on the two Northridge legacy links.

### Still open

- `recordImages`, `recordDocuments` and `wmRelatedPerson` have zero relations sitewide, so photos, documents and the war memorial badge stay wired but unfed.
- Community terms still have empty body, aliases and type.

## 2026-09-17 (Claude, templates-batch-7 follow-up)

- Agent: Claude
- Date: 2026-09-17

### fix_bad_links.php crashed; guarded and re-verified

It threw from `Element->normalizeFieldValue()` and died before applying anything. It walked a fixed list of ten text handles and called `getFieldValue()` for each on every entry, so the first entry whose layout did not carry one of them killed the run.

Field access is now guarded twice, the way `import_wp_media.php` and `clean_bodies.php` do it: the handle is checked against that entry's own field layout first, and every get and set is still wrapped in try/catch. `setFieldValues` and `saveElement` are wrapped too, so a failure on one entry reports and moves on.

The dry run prints all five planned changes, the two Northridge links it leaves alone, and a per entry list of handles skipped for not being on that layout. Those skipped handles are exactly what crashed the first version: `wmNarrative`, `mpNarrative`, `authorBio` and the three note variants belonging to other entry types.

**Also fixed a hazard that was not reported:** replacements used `$target->getUrl()`, which bakes the current environment's hostname into stored content. Run locally, it would have written `scvhistory.ddev.site` links into bodies that then sync to production. Replacements are now root relative.

Idempotent on a second run, which matters because `$APPLY` is already true in Nathan's copy: each rewrite is anchored on the exact stored URL, so once fixed the pattern no longer matches, and once the legacy field is cleared there is no bare domain to move. A second run plans zero and says so.

Verified by running the dry run to completion: plans 5, writes 0.

### Pages verified

All eight pages return 200: `/about`, `/contact`, `/permissions`, `/photo-credits`, `/newsletter`, `/submit`, `/nonprofit`, `/privacy`. All 13 internal footer links return 200, including the two grouping links `/articles?view=era` and `/articles?view=collection`. Nothing 404s. A page with an empty body renders the record shell and says "This page has not been written yet".

## 2026-09-17 (Claude, branch templates-batch-8)

- Agent: Claude
- Date: 2026-09-17

### Article page rebuilt to the approved layout

`templates/articles/_entry.twig`, modelled on the Reynolds Prologue page.

Band: breadcrumbs, title in Playfair gold, collection title beneath it in gold and linked, byline of "By X · Edited by Y · date" with both names linked, chip row of the historical period and every community term.

Body column: featured image as a full-width hero with its caption beneath in italic 13.5px grey from the asset title; a previous/next bar with Jost gold labels above the chapter titles; the prose; a right-aligned author and year in small caps; the same bar again; then ABOUT THE AUTHOR with a round portrait, name, occupation, bio and a View Full Profile link.

Sidebar: tools with the listen player, cite, search, PART OF COLLECTION with the collection's featured image, IN THIS COLLECTION with every chapter in order and the current one on cream behind a gold left rule, PEOPLE IN THIS ARTICLE with thumbnails and occupations, PUBLISHED BY, the remaining relation boxes, documents, and the legacy site link.

Previous, next and the chapter list all read the same `articlesInCollection` order, so they cannot disagree. Every block is conditional: no hero without an image, no bars outside a collection, no author block without a bio, no sign-off without a four-digit year. Built on the existing record partials for tools, cite, note, images, documents and prose.

**One block could not be built: TAGS.** There is no tags field on the article layout, and no tag or keyword field anywhere in this Craft install. The band's chips carry the historical period and the communities instead. If tags are wanted, the field has to be created first.

`featuredImage` is now on the article layout, so the hero is a real featured image; it falls back to the first record image when empty. 50 of the 84 collection-linked articles carry one.

Verified: all 27 article pages return 200, and on the Prologue page the hero, both prev/next bars, the sign-off, the author block, the collection card and the chapter list with the current chapter highlighted all render.

## 2026-09-17 (Claude, branch templates-batch-9)

- Agent: Claude
- Date: 2026-09-17
- Two briefs landed under this batch number: tags, and community editing support. Both are here.

### Tags

`scripts/import/setup_tags.php`, eval style, dry run behind `$APPLY`, safe to run twice. **Nathan runs it.** It creates the `tag` category group (name Tags, uriFormat `tags/{slug}`, template `tags/_entry`, on every site), a Categories field `recordTags` pointing at it, and adds that field to the Content tab of all thirteen entry types. It creates no terms.

`templates/tags/index.twig` is a cloud sized by record count in five steps, so one very common tag cannot flatten the rest. `templates/tags/_entry.twig` lists every record carrying the tag, grouped by section, each row a thumbnail with title and subtitle.

`_partials/record/tags.twig` is the sidebar box: plain chips linking to the tag page, wired into all eleven entry templates that have a sidebar plus the community page, sitting after the relation and document boxes and before External. `documents/_entry.twig` and `photographs/_entry.twig` are still bare fifteen-line stubs with no sidebar, so they were left alone.

Nothing here needs the taxonomy to exist. `/tags` renders and says the taxonomy has not been created yet.

### I broke the site and committed it

The first version of the tags box used `entry.recordTags is defined`. That is not a safe test on an Entry: it reports true even when the field does not exist, Twig then calls `recordTags()`, and Craft throws `Calling unknown method`. Since `recordTags` does not exist until the script runs, **that took out all 151 entry pages and the eight site pages, and I committed it before the sweep finished.**

Fixed in the next commit. The only reliable test is the element's own field layout, which is what the import scripts already do, so the partial now takes the element and does that check itself. This is the third time this exact trap has bitten: `person.sameAs` in the JSON-LD partial, `recordImages` on Category elements, and now this. **`is defined` is not a safe guard for a Craft custom field. Check the field layout.**

### Community editing support

`scripts/import/add_community_media.php`, eval style, dry run behind `$APPLY`. **Nathan runs it.** It puts `recordImages` and `recordDocuments` on the Communities category group, which is the only content type in the archive without them. Both fields already exist, so it only touches the group's field layout.

`templates/communities/_entry.twig` now renders like an entry page: it pulls in `_partials/record/css` alongside `scv-extra-css`, passes `recordImages` to the prose so `[image:N]` resolves, and adds the Photos section, both editor note partials, the documents box and the tags box. `communityType` moves out of the facts row and becomes a chip in the band.

### Community field coverage, as asked

Of the 35 terms, **26 carry coordinates and nothing else**. Not one has a body, an alias, a type or a cultural sensitivity note. So the new type chip and the note partials have nothing to show yet, and the map is still the only thing on those pages besides the related records.

The nine with no field content at all: `fair-oaks-ranch`, `haskell-canyon`, `mint-canyon`, `mojave-desert`, `potrero-canyon`, `ravenna`, `saugus-valencia`, `soledad-township`, `towsley-canyon`. Those are the six that had no coordinate source in batch 4 plus the three skipped as areas rather than points.

### Verified

All 151 entry pages, 22 index and route variants including `/tags`, the eight site pages, and all 35 community pages return 200.

### Needs Nathan

Two scripts on **MacBook**, dry run first, then `$APPLY = true`:

```
ddev craft exec "eval(file_get_contents('scripts/import/setup_tags.php'))"
ddev craft exec "eval(file_get_contents('scripts/import/add_community_media.php'))"
```

Then `project-config/write` and commit `config/project/`. After that, add tag terms and start filling community bodies, aliases and types.

## 2026-09-17 (Claude, templates-batch-9, On This Day)

- Agent: Claude
- Date: 2026-09-17

### Calendar index

`scripts/import/build_calendar_index.php` walks every record's `recordDates`, keeps only rows ticked Confirmed whose precision is `day` or `month`, and writes `templates/_data/calendar.json` keyed by MM-DD. Each entry holds title, a root-relative url, section, ISO and printed dates, label, and the featuredImage url where one exists. Same generated-file pattern as `community-neighbors.json`, read with `source()` rather than queried per request.

Year and circa rows are left out deliberately: a year has no day to file it under, and an approximate date would place a record on a calendar day the source does not claim.

It reads only. It never writes to the database and never modifies a `recordDates` row. Rerun after each round of confirmations; it rewrites the file whole.

**A trap worth recording:** Craft returns the table's date column in the site timezone, and for a date-only value that rolls the day backwards. June 19, 1874 came back as `1874-06-18 16:07:02 America/Los_Angeles`. The builder converts to UTC before taking MM-DD, so the day matches what is printed in the source.

### The page

`templates/on-this-day/index.twig` shows today by default and any day via `?d=MM-DD`, with a month strip and a day strip marking which days hold entries. An empty day says so plainly and offers the nearest day that has entries, wrapping around the year end. Cards carry the image, the date as printed, the label, the section and a link; with no image the year stands in.

The homepage gains an "On this day" block of up to three of today's entries, hidden entirely when there are none. On This Day joins the footer Browse column.

### Current state, as asked

**Zero confirmed rows.** 111 records carry 432 proposal rows: 167 day, 36 month, 228 year, 1 circa. None is ticked Confirmed, so `calendar.json` is valid and empty, `/on-this-day` says no dates have been confirmed yet, and the homepage block does not render. Once rows are confirmed, rerun the builder and both appear.

### Verified

Rendering was checked against a temporary fixture: a populated day rendered four cards oldest-first with image and year fallbacks, a month-precision row rendered, an empty day offered the nearest, and the homepage block appeared with its "All 4 records for today" link. The fixture was deleted and the real generated file restored before committing.

All 158 record pages, 18 index and route variants including `/on-this-day` and bogus `?d` values, and all 35 community pages return 200.

`/places/sleepy-valley` now 404s because the entry was deleted from the database, which resolves one of the data problems flagged in batch 1. My URL list was stale, not the site.

### Note

`git add -A` briefly swept four of Nathan's untracked files into my commit: `apply_confirmed_dates.php`, `export_unconfirmed_dates.php` and `web/review/dates.{html,json}`. The commit was undone and remade with only my five files; his remain untracked and untouched.

## 2026-09-17 (Claude, templates-batch-9, entity reconciliation)

- Agent: Claude
- Date: 2026-09-17
- Built to the shape of the date workflow already in the repo: a read-only export to `web/review`, a standalone screen with localStorage and a download, and an apply script that is dry run by default.

### export_entity_candidates.php

Read only. Emits every Person, Place and Organization with id, section, title, slug, alias, mention count (from the relations table) and url, then pairs them **within a section** four ways:

- **normalised** — titles match once punctuation, accents, honorifics and a leading "the" are stripped
- **initials** — same surname, one side's initials expand to the other's given names, so "H.M. Newhall" meets "Henry Mayo Newhall"
- **substring** — one title inside the other at a word boundary, so "Newhall" meets "Henry Mayo Newhall"
- **surname** — same surname, different given names

Each pair carries both ids, both titles, the reason and a confidence of high, medium or low. It merges nothing and writes nothing to the database. The better-attested record is offered as the survivor first.

### Counts against the current data

33 Persons, 15 Places, 14 Organizations. **4 candidate pairs, all medium, all "same surname, different given names":**

| | |
|---|---|
| Juventino del Valle | Antonio del Valle |
| Ygnacio del Valle | Juventino del Valle |
| Ygnacio del Valle | Antonio del Valle |
| Rodolfo Acosta | Dante Acosta |

**All four are genuinely different people** — del Valle relatives and an Acosta father and son. That is the workflow behaving correctly: it proposes, a person rejects. There are no high-confidence pairs in the current data, which is expected at 62 hand-curated records; the matching earns its keep against thousands of extracted candidates.

### entities.html

Each pair side by side with facts and links, asks which title survives, offers Merge, Not the same and Skip. Filters by section and confidence. Keyboard: `M` merge, `N` not the same, `S` skip, `1`/`2` choose the survivor, `J`/`K` move. Decisions in localStorage, downloads `merged.json`.

### apply_entity_merges.php

Moves every relation pointing at the loser onto the survivor, appends the losing title to the survivor's alias field where the type has one, and deletes the loser, inside a transaction per pair. Dry run by default behind `$APPLY`.

Guards, since this is the destructive half: it refuses to merge a record with itself; it skips a pair where either record is gone, which is what makes a second run a no-op; it drops rather than duplicates a relation the survivor already holds; and it will not append an alias twice. The dry run prints which fields the relations come through, with counts, before anything moves.

### Persons have no alias field

The type carries only `fullName`, which is the canonical name rather than a list of other names. Export, screen and apply script all use the same section-to-alias map, so the screen never offers to record a title the apply step cannot store, and the card says so. **Merging two people therefore loses the losing title.** If that matters, a `personAliases` field would need creating first.

### Verified

A temporary fixture covered a place merge, an organization merge, a person merge with no alias field, a self-merge, a missing record and a `notsame` row. The dry run reported 3 merges and 2 skipped, named the relation fields and counts (for example Rancho Camulos would take 61 relations through `placeOrganizations`, `personOrganizations` and `subjectOrganization`, dropping 6 duplicates), moved nothing, and the record counts were identical afterwards. Fixture deleted.

`web/review/entities.json` and `merged.json` are added to `.gitignore`, matching `dates.json` and `confirmed.json`.

### Needs Nathan

```
ddev craft exec "eval(file_get_contents('scripts/import/export_entity_candidates.php'))"
```

then open `https://scvhistory.ddev.site/review/entities.html`, decide, download `merged.json` into `web/review/`, and run `apply_entity_merges.php` dry first.
