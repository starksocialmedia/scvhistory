# Find A Grave audit (personGraveUrl)

- Date: 2026-09-15 PT
- Agent: Grok Bot (research)
- Source: origin/grok-build grave-audit-export.md @ 80c8ac0 (copied to /workspace/grave-audit-export.md)
- Method: For each non-empty personGraveUrl, fetched the memorial with curl -sL (browser User-Agent), followed redirects, extracted living memorial name/birth/death/burial, compared to export title/fullName, birthDate, deathDate, burialPlace. Pass only on clear same-person match (name plus birth/death or burial). Fail on 404, different person, merged-away, or cemetery index page. No scvhistory.com crawl, no Craft edits, no invented replacement URLs, no lookups for Missing.

## Summary

- Wrong or mismatched: 11
- Correct: 4
- Missing (empty personGraveUrl, excluding Abel Stearns): 43

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

People with empty personGraveUrl. No URLs. No memorial lookups.

| slug | name |
| --- | --- |
| albert-edward-thomas | Albert Edward Thomas |
| albert-lee-moore | Albert Lee Moore |
| antonio-del-valle | Antonio del Valle |
| archibald-k-archie-beall | Archibald K. 'Archie' Beall |
| arthur-b-perkins | Arthur Burnett Perkins |
| augustus-a-august-rubel | Augustus A. (August) Rubel |
| brian-cody-prosser | Brian Cody Prosser |
| cole-william-larsen | Cole William Larsen |
| dante-acosta | Dante Acosta |
| dean-glenn-todd-jr | Dean Glenn Todd Jr |
| dennis-lee-sellen-jr | Dennis Lee Sellen Jr |
| donald-e-morissett | Donald E. Morissett |
| edward-d-contreras | Edward D. Contreras |
| eugene-e-darr | Eugene E. Darr |
| father-francisco-garces | Father Francisco Garcés |
| frank-pike-whitmore | Frank Pike Whitmore |
| gaspar-de-portola | Gaspar de Portolá |
| gilbert-d-montenegro | Gilbert D. Montenegro |
| henry-acuna | Henry Acuna |
| henry-clay-wiley | Henry Clay Wiley |
| ian-timothy-d-gelig | Ian Timothy D. Gelig |
| jake-william-suter | Jake William Suter |
| john-michael-conant | John Michael Conant |
| john-gifford | John Timothy Gifford |
| jose-antonio-aguirre | José Antonio Aguirre |
| jose-ricardo-flores-mejia | Jose Ricardo Flores-Mejia |
| juan-bautista-de-anza | Juan Bautista de Anza |
| juan-crespi | Juan Crespí |
| francisco-lopez | Juan José Francisco de Gracia ("Chico") Lopez |
| junipero-serra | Junípero Serra |
| juventino-del-valle | Juventino del Valle |
| lawrence-e-kenaston | Lawrence E. Kenaston |
| leon-worden | Leon Worden |
| pedro-fages | Pedro Fages |
| raymond-gene-kelly | Raymond Gene Kelly |
| remi-nadeau-i | Remi Allen Nadeau |
| richard-patrick-slocum | Richard Patrick Slocum |
| robert-l-whisler | Robert L. Whisler |
| robert-michael-wilson | Robert Michael Wilson |
| rudy-alexander-acosta | Rudy Alexander Acosta |
| scott-wilk | Scott Thomas Wilk Sr. |
| stephen-edward-colley | Stephen Edward Colley |
| thomas-o-larkin | Thomas O. Larkin |
