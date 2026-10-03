# How people, organizations and groups connect: the map, and a proposed model

3 October 2026. Claude Code, for Nathan. Read-only: counts are live records in local DDEV on 3 October, after today's 23 office holdings and the Walters apply.

The archive holds 131 people, 36 organizations, 6 groups, 54 war memorial casualties (people kept in their own section), 81 office holdings, 463 candidacies, 10 education records and 83 roles.

## 1. The map: every way a person is joined to a body, a group or another person today

| Join | Where it lives | What it is meant to say | Live links | Notes |
|---|---|---|---|---|
| Office holding | `officeHoldings` section: holdingPerson, holdingOffice (a role), holdingBody (an organization), holdingDistrict (a place), terms, how chosen, how ended, evidence | held this office in this body, from to | 81 holdings, 41 people | The only join that says what the connection was, when, and on what evidence. 36 people in one body, 4 in two, McKeon in three. McKeon's Congress holding has no body: there is no record for the House, the Assembly or the Senate. |
| Candidacy | `candidacies` section: candidacyPerson, candidacyElection (election to body), votes, outcome | stood for election to | 463 candidacies, 193 linked to a person | Most candidacies are names on a ballot with no person record, by design. 23 people stood but hold no office. |
| Role | `roles` on persons, to the `roles` section (83 titles) | what the person was | 106 links | Two kinds mixed in one list: offices (Mayor, Congressman, Councilwoman, School Board Member, 21 titles) and occupations (Rancher, Outlaw, Diarist, 48 titles); one organization ("SCVTV"). Gendered duplicates (Councilman and Councilwoman, Assemblyman, Assemblywoman and State Assemblymember). An office role on a person says nothing about where or when; three people carry one with no holding behind it (Wilk, Perkins as Justice of the Peace, Bryant as Alcalde). |
| Occupation | `occupation`, plain text on persons | what they did for a living | 72 of 131 | Free text, overlaps roles. |
| Affiliation, person side | `personOrganizations` on persons | "affiliation" (DATA-MODEL) | 16 | Says nothing about the kind. 7 of the 16 duplicate an office holding at the same body. |
| Affiliation, organization side | `orgAssociatedPersons` on organizations | the same, from the other end | 17 | Only 5 links agree with the person side; 11 exist only on the person, 12 only on the organization. Two fields for one statement, kept by hand, have drifted. |
| Founder | `orgFoundedBy` on organizations | founded | 3 | |
| Named for | `namedFor` on organizations (4) and places (9) | the body or place bears this person's name | 13 | Clear and useful. |
| Group membership, person side | `personGroups` on persons | member of | 10 | |
| Group membership, group side | `groupPersons` on groups | the same | 9 | 9 agree, 1 one-sided. |
| Education | `educations` section: educationPerson, educationSchool, years | attended | 10 records | 9 of the 10 point at war memorial casualties, 1 at a person: the casualties live in their own section, so "a person" is two kinds of record. |
| Family | `childOf` (persons 4, war memorials 1), `spouseOf`, `siblingOf` | kinship | 5 | Shown only between historical people (the family rule, 1 October). |
| Related person | `relatedPersons` | unspecified | 4 | Says nothing about the kind. |
| Subject of | articles `subjectPerson` 380, `subjectOrganization` 281, `subjectGroup` 5; photographs `photoPeople` 52, `photoOrganizations` 53, `photoGroups` 2; documents `subjectPerson` 11; obituaries `obitSubject` 7; places `placePeople` 22, `placeOrganizations` 37; events `eventPersons` 3, `eventOrganizations` 3; persons `articlesAbout` 20 | the record is about them | about 870 | Sound as "about". `articlesAbout` on persons (20) is the reverse of `subjectPerson` kept by hand. `placePeople` mixes "lived here", "owned this" and "is about". |
| Author, editor, publisher | `writtenBy` 381, `editedBy` 15, `publishedBy` 39 | wrote, edited, published | 435 | Sound. 8 people are in the archive only as authors. |
| War memorial | `wmRelatedPerson` 1 | related | 1 | |
| Body to body | `parentOrganization` 7, `feedsInto` 4, `succeededBy` 2, `precededBy` 1 | part of, feeds, became | 14 | `succeededBy` and `precededBy` are the same statement twice. |

## 2. Where two joins say overlapping things

1. **personOrganizations and orgAssociatedPersons** state one relationship from both ends and disagree in 23 of 28 links.
2. **personGroups and groupPersons**: the same, 1 disagreement.
3. **articlesAbout (persons) and subjectPerson (articles)**: the same, by hand.
4. **succeededBy and precededBy**: the same.
5. **An office role (roles) and an office holding**: a role "Mayor" and a holding as Mayor say the same thing, and the role says less. 7 affiliations also duplicate a holding.
6. **roles and occupation**: two places for what someone did.
7. **persons and warMemorials**: two sections for people. Education and family already reach across both.

Craft can show a relation from either end without storing it twice (`relatedTo` with the field). Every two-field pair above can be one field read from both ends.

## 3. Real relationships with nowhere to live

- **Worked for** (an employee, a superintendent, a ranch hand, a newspaper's editor): only the bare `personOrganizations`.
- **Owned** (a rancho, a business, a mine, land): nowhere for organizations; `placePeople` for places, unlabelled.
- **Office in a body the archive has no record for**: the House, the State Assembly, the Senate, the County Board of Supervisors, the courts. McKeon's Congress holding has no body; Wilk's offices are roles only.
- **Appointed, not elected, positions**: commissions (Connie Worden on the first Planning Commission), oversight committees (Trunkey on Measure V), boards of nonprofits. No body record, so nowhere for the holding.
- **Took part in an event** with a part played: `eventPersons` says only that they appear.
- **Gave to the archive**: Connie Worden's papers. The photo key says "CW xxxx = Materials from the collection of Connie Worden-Roberts"; there is no join from a photograph or document to the person whose collection it came from. A source of the archive, not a subject of it.
- **Kinds of group**: nowhere to say a group is a family, an expedition, a party of travellers, a people or a list of honorees (see 5.1).

## 4. The relationships the archive needs

Nathan's list, with what I would change:

| Relationship | Keep? | Where it should live |
|---|---|---|
| Held office in | yes | `officeHoldings`, as now. Every office gets a body record, including the House, the Assembly, the Senate and the Supervisors, so no holding is bodiless. Retire office titles from `roles`. |
| Stood for election to | yes | `candidacies`, as now. |
| Worked for | yes | a new **affiliation** record (below), kind "employed by", with years. |
| Was a member of | yes, for organizations | the affiliation record, kind "member of" (a club, a society, a commission when it is not an office). For groups, see 5.1. |
| Founded | yes | the affiliation record, kind "founder", replacing `orgFoundedBy`. |
| Owned | yes | the affiliation record for organizations; for land, a kind on the place-person join. |
| Is named for | yes | `namedFor`, as now. It is the body or place pointing at the person, not something the person did. |
| Attended | yes | `educations`, as now, pointing at people in either section. |
| Was a subject of | yes, but it is not a connection to a body | the subject fields on articles, photographs, documents and obituaries, as now. Kept out of "connections". |
| **Wrote, edited, published** (add) | yes | `writtenBy`, `editedBy`, `publishedBy`, as now. |
| **Contributed to the archive** (add) | yes | a new join from a photograph, document or collection to the person or body whose papers it came from (`fromCollectionOf`). Connie Worden's is the case. |
| **Took part in** (add) | yes | event participation, with the part played, if events grow. |
| **Family** (keep apart) | yes | kinship fields, under the family rule. |
| "Related person", "associated person" (drop) | no | each existing link is re-read and moved to a kind, or dropped. |

**The affiliation record.** One section, `affiliations`, on the officeHoldings pattern: person, body (organization), kind (employed by, member of, founder, owner, board member of a nonprofit, volunteer), title as printed, years, evidence, footnotes. It replaces `personOrganizations`, `orgAssociatedPersons` and `orgFoundedBy`. An office stays an office holding; everything else between a person and a body is an affiliation with a kind and a source. That is the same discipline the holdings brought to office: the join says what, when, and how we know.

## 5. The three questions

### 5.1 What is a group for, as against an organization?

An **organization** is a body that acts and lasts: it has members or officers, a name of its own, and outlives any one person in it (a district, a company, a church, a society). A **group** is a set of people the archive names because they did something together or are spoken of together, with no standing as a body. The six groups are four kinds:

- a **family** (del Valle Family, Newhall Family): kinship, and best told through the family fields; the group record is the family's own page.
- an **expedition or party** (Portolá Expedition, Bennett-Arcan Party): a group defined by one event. Each is close to an event with participants; I would keep them as groups with a kind, and link each to its event when events exist.
- a **people** (Tataviam): not a group in this sense at all. A people is a community with its own history, land and descendants today; TATAVIAM_AUDIT.md governs it, and it should be its own kind (or its own section), never a list of members.
- a **list** (Walk of Western Stars Inductees): an honor roll, which belongs to the Walk (an organization or event) as its inductees, not a group.

Recommendation: a required `groupKind` on groups (family, expedition or party, people, other), the Walk's inductees moved to an affiliation or honor on the Walk, and the Tataviam record reviewed under the audit before anything links people to it.

### 5.2 Should a body's page show its people, and from which relationships?

Yes, in labelled lists, never one merged list:
- **Office holders**: from officeHoldings, by term, newest first (trustees, directors, council members).
- **Staff and leadership**: from affiliations of kind employed by (superintendents, managers, editors).
- **Founders**: affiliation kind founder.
- **Members**: affiliation kind member of, where the archive holds them.
- **Named for**: from namedFor, as a line under the title ("named for William S. Hart"), not a list of people.
- **In the archive's records**: articles and photographs about the body, as now, separate from people.

Alumni (educations) belong on a school's page only when the archive has them; today the 9 are war memorial casualties, which is a list worth showing on their schools.

### 5.3 Does a person's page need one "connections" view?

Yes. Today a person's connections are spread over the roles line, the occupation line, "Organizations", "Groups", "Related people", the family box and the office lines, and the same body can appear twice (an affiliation and a holding). One **Connections** block, built from the holdings, candidacies, affiliations, education, group memberships and namedFor, each line saying the kind and the years:

> School Board Member, Newhall School District, 2009 to 2022 (appointed; Trustee Area 1 from 2018)
> Stood for Trustee Area 1, 2022: not elected
> Partner, Poole, Shaffery & Koegle (affiliation, from 2022 source)

Family stays its own box under its rule. "About this person" (articles, photographs, documents) stays separate: being written about is not a connection.

## 6. What this means for the person index

The index can group by what the model holds once it holds it:
- **Office**, from holdings: the City (council, mayors), each school district, the water boards, and State and Federal once those bodies exist. 41 people today; 5 in more than one body.
- **No office**: 90 people. 23 are candidates only, 8 are authors only, and 62 have no role but an office or none at all. Their roles split into many small kinds (9 military officers, 6 ranchers, 4 explorers, 3 historians, 3 authors and dozens of one-offs). Those are not yet consistent enough to group by honestly; the affiliation record and a cleaned occupation list would make them so. Until then, alphabetical, by era.
- **Someone in several bodies** appears once, under the body of the office they held longest or most recently (their primary, as with eras), with the others named on the line ("also Hart board, Congress"), not repeated.

## 7. Order of work, if approved

1. Bodies for the state and federal offices (House, Assembly, Senate, Supervisors), and McKeon's Congress holding given its body. Office titles retired from roles; occupations cleaned to one list.
2. The `affiliations` section; the 16 + 17 + 3 links re-read one by one into kinds, with a source each, and the two-ended fields retired (shown from either end with relatedTo).
3. `groupKind`; the Walk's inductees moved; the Tataviam record under the audit.
4. `fromCollectionOf` and Connie Worden's collection labelled.
5. The Connections block on person pages; the people lists on body pages.
6. Then the person index.

The board-terms derivation already approved feeds step 5 and does not wait on this.
