# Collections hub

Nathan decides. No Leon gate. No Craft import in this session.

## Live Collections section

| | |
| --- | --- |
| Section handle | `collections` |
| Section type | structure |
| Entry type | `collection` |
| URI | `collections/{slug}` |
| Configured template | `_entries/collections` |
| Entries in local DDEV | 0 |

### Fields on the live layout

`body`, `legacyUrl`, `archiveUrl`, `culturalSensitivityNote`, `writtenBy`, `editedBy`, `publishedBy`, `collectionGroups` (Groups), `articlesInCollection` (Articles), `historicalEra`, `historicalPeriod`, `neighborhood`, plus ingest `legacyKey`, `sourcePath`, `legacyHtml`, `legacyCategory`.

Articles later set `partOfCollection` to these Collection entries. Do not import articles now.

## Signal series (one Collection each)

Paths under `scvhistory.com/scvhistory/signal/`.

| Collection title | Slug | Series folder | Index page |
| --- | --- | --- | --- |
| Perkins | perkins | `scvhistory/signal/perkins/` | `index.html` (also `contents.html`) |
| Reynolds | reynolds | `scvhistory/signal/reynolds/` | `index.html` (also `contents.html`) |
| Worden | worden | `scvhistory/signal/worden/` | `index.htm` / `index.html` |
| Boston | boston | `scvhistory/signal/boston/` | `jbindex.htm` |
| Manzer | manzer | `scvhistory/signal/manzer/` | `index.htm` |
| Newsmaker | newsmaker | `scvhistory/signal/newsmaker/` | `index.htm` |
| Coins | coins | `scvhistory/signal/coins/` | none named index; use folder URL if needed later |
| Iraq | iraq | `scvhistory/signal/iraq/` | `index.htm` / `index.html` |

Yearbook landings stay Documents. Do not make a Collection per yearbook.

## Old Town Newhall (one Collection per run)

| Collection title | Slug | Folder | Index page |
| --- | --- | --- | --- |
| Old Town Newhall Gazette | otn-gazette | `oldtownnewhall/gazette/` | no single index.htm; 301 the minisite home if needed |
| Patti | otn-patti | `oldtownnewhall/patti/` | `index.html` |
| Pauline | otn-pauline | `oldtownnewhall/pauline/` | `index.htm` / `index.html` |
| Rioux | otn-rioux | `oldtownnewhall/rioux/` | `index.htm` / `index.html` |
| Whyte | otn-whyte | `oldtownnewhall/whyte/` | `index.html` |

The minisite home `oldtownnewhall/index.htm` is not a Collection. It can 301 to `/collections` later or stay a landing. Not this session.

## Mentryville

One Organization (Friends of Mentryville) plus one Place (`mentryville`) already decided. Story pages (`mentryville/mstory.htm` and similar) become Articles later, in a Collection `mentryville-stories` if needed. Do not import those articles now.

## 301 table (series index pages only)

Skip Apache listings and flipbook HTML.

| Legacy path | Craft URI |
| --- | --- |
| `/scvhistory/signal/perkins/index.html` | `/collections/perkins` |
| `/scvhistory/signal/perkins/contents.html` | `/collections/perkins` |
| `/scvhistory/signal/reynolds/index.html` | `/collections/reynolds` |
| `/scvhistory/signal/reynolds/contents.html` | `/collections/reynolds` |
| `/scvhistory/signal/worden/index.htm` | `/collections/worden` |
| `/scvhistory/signal/worden/index.html` | `/collections/worden` |
| `/scvhistory/signal/boston/jbindex.htm` | `/collections/boston` |
| `/scvhistory/signal/manzer/index.htm` | `/collections/manzer` |
| `/scvhistory/signal/newsmaker/index.htm` | `/collections/newsmaker` |
| `/scvhistory/signal/iraq/index.htm` | `/collections/iraq` |
| `/scvhistory/signal/iraq/index.html` | `/collections/iraq` |
| `/oldtownnewhall/patti/index.html` | `/collections/otn-patti` |
| `/oldtownnewhall/pauline/index.htm` | `/collections/otn-pauline` |
| `/oldtownnewhall/pauline/index.html` | `/collections/otn-pauline` |
| `/oldtownnewhall/rioux/index.htm` | `/collections/otn-rioux` |
| `/oldtownnewhall/rioux/index.html` | `/collections/otn-rioux` |
| `/oldtownnewhall/whyte/index.html` | `/collections/otn-whyte` |

Coins has no index file. Do not invent a 301.

Gazette issues have no series index.htm. Do not 301 every gazette story to the Collection. Individual articles 301 later.

## Missing templates

| Template | URI | What it lists |
| --- | --- | --- |
| `templates/collections/index.twig` | `/collections` | Collection entries (Signal series, OTN runs) |
| `_entries/collections` | `collections/{slug}` | Configured on the section but the file is missing. Add that file or switch to `collections/_entry` |

`templates/collections/_entry.twig` does not exist. The section currently points at `_entries/collections`, which is also absent.
