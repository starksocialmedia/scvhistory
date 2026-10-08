# Documents that do not say whose they are: a proposal (8 October 2026)

For Nathan's word. Claude. Nothing written.

## The fault

#26573, "Final Official Election Returns, November 08, 2016 General Election", is the County's certified count for the Castaic Lake Water Agency board contests. Batch 1 took "Castaic Lake Water Agency" out of the title, as ruled ("The agency name belongs in fields, not after a colon in the title"). No field on the document holds it. The agency is reachable only backwards, through the four election records that link to the document (#26599, #26605, #26611, #26617). Someone who opens the document page alone cannot tell whose contest it is. My attempt to fix that through recordTags failed (ERRORLOG, 7 October), because recordTags is a Categories field and cannot hold an entry.

The fault is not confined to #26573. 14 documents are linked from election records. Only 3 have publishedBy set (the City's own three). Of the other 11, #26573 has lost its body's name, #25146 never had one, and 9 name their body only in the title (8 after a colon), where the title rule will take it out:

| Document | Body it concerns (from the election records that link to it) |
|---|---|
| #26573 Final Official Election Returns, November 08, 2016 General Election | Castaic Lake Water Agency (#26563) |
| #26576, #26579, #26581, #26583 (SVC/OER 2020, 2022, 2024; Board of Directors) | Santa Clarita Valley Water Agency |
| #21918, #21921, #21924, #21927, #21930 (County returns, 2016 to 2024) | The City of Santa Clarita |
| #25146 CEDA candidate files, 1995 to 2024 | Six bodies (Hart, Sulphur Springs, Saugus, Newhall and Castaic districts; the City) |
| #21933, #21936, #21939 (the City's own) | The City of Santa Clarita, already in publishedBy |

## The fix (one path)

1. **Schema.** Add the existing field `subjectOrganization` ("Subject Organization", an Entries field limited to Organizations, already on the article type) to the document type, after `subjectPerson`. This is a field-layout change only: no new field and no change to existing data. Project config, so it needs your approval.
2. **Data.** Fill it on all 14. Each value is computed from the organizations on the election records that link to the document, not typed by hand, so #26573 gets Castaic Lake Water Agency (#26563). The City's three get it too, beside publishedBy, so every election document reads the same way. Dry run first, then apply, with a snapshot before.
3. **Page.** In the document template, show the organization in the band under the title (where publishers show now) and as a "CONCERNS" row in the Publication box, linked to the organization's page, copying the existing PUBLISHER row. Also emit it as schema.org `about` in the JSON-LD. Show it only when it is filled, and read it through the field layout (the Twig trap). check_render before the commit.

The issuer is a separate, smaller gap. The County's Registrar-Recorder/County Clerk is named in `sourceLine` as text, which the page already shows as the credit line. No organization record exists for it, so `publishedBy` stays empty. Creating that record is a question for the entity list, not part of this fix.

With this in place, the remaining colon titles (#26576 to #26583, #21918 to #21930) can lose their agency under the same rule without losing information. They belong in the titles batches.
