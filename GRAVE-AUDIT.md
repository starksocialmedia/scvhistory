# Find A Grave audit (personGraveUrl) - Persons only

- Date: 2026-09-16 PT
- Agent: Grok Bot (research)
- Scope: Remaining Persons only (33), per WAR-MEMORIAL.md "Moved out of Persons". The 25 old Person slugs that are now War Memorials are excluded from Wrong, Correct, and Missing. Prior full-export Missing casualties are removed from scope.
- Source: /tmp/grave-audit-export.md (also on origin/grok-build); skip list from /tmp/WAR-MEMORIAL.md
- Method: For each non-empty personGraveUrl among remaining Persons, fetched the memorial with curl -sL (browser User-Agent), followed redirects, extracted living memorial name/birth/death/burial, compared to export title/fullName, birthDate, deathDate, burialPlace. Pass only on clear same-person match (name plus birth/death or burial). Fail on 404, different person, merged-away, or cemetery index page. Abel Stearns (empty URL): Wrong as removed; recommend clear keep cleared. Missing: empty personGraveUrl among remaining Persons except Abel; slug and name only; no lookups. No scvhistory.com crawl, no Craft edits, no invented replacement URLs.

## Excluded (25 war memorials, not audited)

albert-edward-thomas, donald-e-morissett, gilbert-d-montenegro, henry-acuna, raymond-gene-kelly, robert-l-whisler, brian-cody-prosser, cole-william-larsen, dean-glenn-todd-jr, dennis-lee-sellen-jr, ian-timothy-d-gelig, jake-william-suter, john-michael-conant, jose-ricardo-flores-mejia, richard-patrick-slocum, robert-michael-wilson, rudy-alexander-acosta, stephen-edward-colley, albert-lee-moore, archibald-k-archie-beall, augustus-a-august-rubel, edward-d-contreras, lawrence-e-kenaston, eugene-e-darr, frank-pike-whitmore

## Summary (among 33 remaining Persons)

- Wrong or mismatched: 11
- Correct: 4
- Missing (empty personGraveUrl, excluding Abel Stearns): 18

## Wrong or mismatched

| slug | current URL | why it fails | action |
| --- | --- | --- | --- |
| abel-stearns | (empty) | already cleared / removed | recommend clear (keep cleared) |
| andres-pico | https://www.findagrave.com/memorial/5913/andres-pico | different person: living memorial is Julius Ochs Adler (1892-1955); ID reassigned | recommend clear |
| cave-johnson-couts | https://www.findagrave.com/memorial/9372/cave-johnson-couts | different person: living memorial is Davison Alexander Dalziel (1852-1928); ID reassigned | recommend clear |
| kit-carson | https://www.findagrave.com/memorial/1819/christopher-houston-carson | different person: living memorial is Harry Chapin (1942-1981); ID reassigned | recommend clear |
| edward-fitzgerald-beale | https://www.findagrave.com/memorial/5144/edward-fitzgerald-beale | different person: living memorial is Simon Bolivar Buckner Sr. (1823-1914); ID reassigned | recommend clear |
| edwin-bryant | https://www.findagrave.com/memorial/8906/edwin-bryant | different person: living memorial is James Francis Edward Stuart (1688-1766); ID reassigned | recommend clear |
| james-w-marshall | https://www.findagrave.com/memorial/1144/james-wilson-marshall | different person: living memorial is George S. Patton (1885-1945); ID reassigned | recommend clear |
| jerry-reynolds | https://www.findagrave.com/memorial/14716645/jerry-hugh-reynolds | not clear same person: name/burial close but birth mismatch (export 1937 vs memorial 1947) | recommend clear |
| john-c-fremont | https://www.findagrave.com/memorial/834/john-charles-fremont | different person: living memorial is Dick Powell (1904-1963); ID reassigned | recommend clear |
| juan-bandini | https://www.findagrave.com/memorial/9366/juan-bandini | different person: living memorial is Mary Scott Hogarth (1819-1837); ID reassigned | recommend clear |
| william-lewis-manly | https://www.findagrave.com/memorial/1979/william-lewis-manly | different person: living memorial is Queen Anne (1665-1714); ID reassigned | recommend clear |

## Correct

| slug | URL |
| --- | --- |
| henry-mayo-newhall | http://findagrave.com/memorial/5308/henry_mayo-newhall |
| rodolfo-acosta | https://www.findagrave.com/memorial/19542/rodolfo-acosta |
| tiburcio-vasquez | https://www.findagrave.com/memorial/5334/tiburcio-vasquez |
| ygnacio-del-valle | https://www.findagrave.com/memorial/136176316/ignacio_ramn_de_jess-del_valle |

## Missing

Remaining Persons with empty personGraveUrl (Abel excluded). No URLs. No memorial lookups. Prior war-memorial casualty Missing rows removed from scope.

| slug | name |
| --- | --- |
| antonio-del-valle | Antonio del Valle |
| arthur-b-perkins | Arthur Burnett Perkins |
| dante-acosta | Dante Acosta |
| father-francisco-garces | Father Francisco Garcés |
| gaspar-de-portola | Gaspar de Portolá |
| henry-clay-wiley | Henry Clay Wiley |
| john-gifford | John Timothy Gifford |
| jose-antonio-aguirre | José Antonio Aguirre |
| juan-bautista-de-anza | Juan Bautista de Anza |
| juan-crespi | Juan Crespí |
| francisco-lopez | Juan José Francisco de Gracia ("Chico") Lopez |
| junipero-serra | Junípero Serra |
| juventino-del-valle | Juventino del Valle |
| leon-worden | Leon Worden |
| pedro-fages | Pedro Fages |
| remi-nadeau-i | Remi Allen Nadeau |
| scott-wilk | Scott Thomas Wilk Sr. |
| thomas-o-larkin | Thomas O. Larkin |
