#!/usr/bin/env python3
"""Phineas Banning #18714: the profile draft (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent.

Writes inventory/review/phineas-banning-profile-draft-2026-10-06.json (read by
scripts/import/build_banning_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

The Wikipedia article (Phineas_Banning) says nothing of the Santa Clarita Valley. His valley role comes from Horace
Bell's Reminiscences of a Ranger (1881, printed in Banning's lifetime by a man who rode with him), the newspapers of
1858 and 1876 as Leon Worden (2023) and Alan Pollack (2010) quote them, A.B. Perkins (1954, 1957), and his death from
the Sacramento Daily Record-Union of 10 March 1885. Bell and the Record-Union are checked against the copies saved in
inventory/sources/phineas-banning-2026-10-06/ (manifest.json there); the legacy pages against the Reggie mirror.
No quotation joins two clauses with an ellipsis, and none quotes a passage holding an em dash.
Run on the host: python3 scripts/import/draft_banning_profile_2026_10_06.py
"""
import html, json, os, re

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..')
OUT = os.path.join(ROOT, 'inventory', 'review', 'phineas-banning-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'phineas-banning-2026-10-06')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

NOTES = {
    'BELL': 'Horace Bell, Reminiscences of a Ranger; or, Early Times in Southern California (Los Angeles: Yarnell, Caystile & Mathes, 1881), chapter XXIX, "More Pioneer Staging," pages 322 to 324, read in the Internet Archive\'s copy, https://archive.org/details/reminiscencesofr00bellrich: "When Fort Tejon was established the firm of Alexander & Banning wished to run a six-horse stage over an old Mexican pack trail"; "At the time, the trail going over the San Fernando pass was a rocky acclivity, difficult of ascent by even a pack mule"; "In December \'54 Phineas Banning sat on the box of his Concord stage"; "He had succeeded in reaching the summit of the San Fernando"; Banning, after the stage had come down in a heap in the chaparral: "a beautiful descent, far less difficult than I anticipated. I intended that staging to Fort Tejon and Kern river should be a success."; "urging Don David to send fifty men immediately to repair parts of the road that he in his descent had knocked out of joint."; "years thereafter the S. P. R. R. Company cleared away the thicket in which Banning made his first stage stand", in digging "their wonderful San Fernando tunnel."; Banning\'s ride "so stimulated our angel merchants, that they raised a fund of several thousand dollars"; "that in February following Don David Alexander and the writer hereof passed over with a train of heavy ten-mule teams, which was the first train going north." A.B. Perkins reprinted the passage in "The Story of Our Valley," part 4, "Early Transportation" (The Signal, 1954 to 1955; archive article #1426, /scvhistory/signal/perkins/part04.html).',
    'RSF': 'A.B. Perkins, "Rancho San Francisco: A Study of a California Land Grant" (1957), archive article #1434, /scvhistory/perkins-rsf-1957.htm, after quoting Bell (his note 57: "Reminiscences of a Ranger," by Major Horace Bell, p. 322-4, Los Angeles 1881): "This marks the arrival of the first stage at Rancho San Francisco."',
    'CUT': 'Leon Worden\'s editor\'s note 13 to A.B. Perkins, "The Story of Our Valley," /scvhistory/signal/perkins/notes.html, on the summit Bell names: "Known, after 1863, as Beale\'s Cut. (Perkins, however, may have been among those who believe Banning\'s crossing point and Beale\'s were not the same.)"',
    'TEJON': 'Bell (as note 1), pages 327 to 328: "the gallant General Phineas Banning ran the post, as he did his supply trains"; "From Fort Tejon to Los Angeles is 120 miles"; "used to ride it in a day on horseback, leaving the fort after sunrise and arriving at Los Angeles sometimes by four o\'clock". Bell adds, "I make this statement on personal knowledge."',
    'LP23': 'Leon Worden, "La Puerta: Gateway to the Santa Clarita Valley," March 2023, /scvhistory/lapuerta2023.htm (not yet a record in the archive): "Banning had a similar experience on another run in May 1858 in the company of a newspaper reporter." The correspondent\'s words, as Worden quotes them: "somewhat more carefully than Banning\'s usual custom"; Worden: "it still took three men to ease the carriage down the cliff after locking the wheels and sending the horses down separately." His source, note 18: Daily Alta California, May 29, 1858. On the Board of Supervisors\' offer of up to $3,000 for road repairs in June 1858, when the Butterfield Overland Mail wanted the road mended, Worden: "The lenders were identified as"; the Star\'s list, as he quotes it: "Banning, Stearns, Griffin, Bachman & Co., Mellus, O.W. Childs, Andrés Pico, and Del Valle." His sources, notes 19 and 20: Los Angeles Star, June 12, 1858. The two newspapers were not read for this profile.',
    'SOLEDAD': 'A.B. Perkins, "The Story of Our Valley," part 5, "Mining," archive article #1428, /scvhistory/signal/perkins/part05.html, on the Soledad boom of the summer of 1863: "Phineas Banning succumbed and" (then, in Perkins\'s own quotation marks) bought in "to the District mines."',
    'SPIKE': 'Alan Pollack, "Golden Spike Joins Rails in Soledad Canyon: Contemporary Accounts," Heritage Junction Dispatch, September-October 2010, /scvhistory/pollack0910lang.html, quoting San Francisco\'s Daily Alta California of September 1876: after Crocker drove the spike, speeches by General D.D. Colton, Ex-Governor Downey, the two mayors and Governor Leland Stanford, "and finally Los Angeles freighting king Phineas Banning who"; the Alta, as Pollack quotes it: "at 2:20 closed the exercises with a mirthful address." The Alta itself was not read for this profile. The archive\'s event record is "Golden Spike at Lang Station."',
    'RU85': 'Sacramento Daily Record-Union, 10 March 1885, page 4, "San Francisco Items," read in the Library of Congress\'s Chronicling America, https://www.loc.gov/resource/sn82014381/1885-03-10/ed-1/?sp=4 (OCR text saved): "General Phineas Banning died Sunday"; "the Occidental Hotel"; "founded the town of Wilmington, Los Angeles county"; the item goes on that he built the railroad from Wilmington to Los Angeles (the OCR loses the line\'s last word). The paper of Tuesday, 10 March, puts the death on Sunday, 8 March 1885.',
    'BORN': 'Bell (as note 1), page 328, writing in 1881: "The General was born at Wilmington, Delaware, and is fifty-one years old." On page 130: "the founding of Wilmington not having as yet been projected by General Banning, its illustrious founder and patron."',
    'CENT': '"Lang Station Golden Spike Centennial, 1876-1976: Historical Program," Lang Station, 5 September 1976, /scvhistory/golden-spike-centennial-index.htm: "5:20 James G. Shea introduces Robert Banning, grandson of Phineas Banning. Driving of Golden Spike."',
}

BODY = [
    'Phineas Banning drove the first stagecoach over the San Fernando Pass into the Santa Clarita Valley, in December 1854. Horace Bell, who knew him, wrote that when Fort Tejon was established, Banning\'s firm, Alexander & Banning, wanted to run a six-horse stage north over an old Mexican pack trail, and that the climb over the pass was "difficult of ascent by even a pack mule." Banning took the stage to the summit himself, drove it down the far side to a wreck in the chaparral at the foot, and called it "a beautiful descent," then sent back for fifty men to repair the road he had broken.{BELL} A.B. Perkins took the ride as the arrival of the first stage at Rancho San Francisco.{RSF} Leon Worden notes that some, Perkins perhaps among them, believe Banning\'s crossing was not where Beale\'s Cut was later made.{CUT}',
    'The road through the valley and up San Francisquito Canyon carried Banning\'s stages and supply trains to Fort Tejon. By Bell\'s account the ride moved the merchants of Los Angeles to pay for work on the pass, and the next February he and Banning\'s partner, David Alexander, took the first train of ten-mule freight wagons north over it. Bell, writing from personal knowledge, held that Banning "ran the post, as he did his supply trains" and rode the 120 miles from the fort to Los Angeles in a day.{BELL}{TEJON} The pass stayed hard going. In May 1858 a reporter of the Daily Alta California who crossed it with Banning wrote that he took it "somewhat more carefully than Banning\'s usual custom," and in June 1858, when the Butterfield Overland Mail wanted the road mended, Banning was among the men who lent the County the money for the repairs, as Leon Worden reads the Los Angeles Star.{LP23} In the Soledad copper boom of 1863, Perkins wrote, he "bought in" to the district\'s mines.{SOLEDAD}',
    'He was at Lang Station in Soledad Canyon on 5 September 1876, when the Southern Pacific joined its line from San Francisco to Los Angeles. After Charles Crocker drove the golden spike and the other speakers, among them Leland Stanford and the mayors of both cities, had spoken, Banning "closed the exercises with a mirthful address," by the Daily Alta California\'s account as Alan Pollack quotes it.{SPIKE} Bell, writing in 1881, said the Southern Pacific had cleared away the thicket where Banning\'s stage came to rest in 1854 when it dug its San Fernando tunnel.{BELL}',
    'Banning was born at Wilmington, Delaware, and founded the town of Wilmington in Los Angeles County, with the railroad from it to Los Angeles; he was known as General Banning.{BORN}{RU85} He died at the Occidental Hotel in San Francisco on 8 March 1885.{RU85} At the centennial of the golden spike at Lang in 1976, his grandson Robert Banning drove the replica spike.{CENT}',
]

order = []
for p in BODY:
    for k in re.findall(r'\{([A-Z0-9]+)\}', p):
        if k not in order:
            order.append(k)
unused = set(NOTES) - set(order)
if unused:
    raise SystemExit('notes never cited: ' + ', '.join(sorted(unused)))
num = {k: i + 1 for i, k in enumerate(order)}
# 'as note 1' inside the notes refers to BELL: keep it right whatever BELL's number becomes
for k in NOTES:
    NOTES[k] = NOTES[k].replace('(as note 1)', f"(as note {num['BELL']})")
body = '\n\n'.join(re.sub(r'\{([A-Z0-9]+)\}', lambda m: f'[{num[m.group(1)]}]', p) for p in BODY)
footnotes = [{'number': str(num[k]), 'key': k, 'note': NOTES[k]} for k in order]
for t in [body] + [f['note'] for f in footnotes]:
    if '—' in t:
        raise SystemExit('an em dash in our own text: ' + t[:80])
    if re.search(r'\.\.\.|…', t):
        raise SystemExit('an ellipsis in our own text or a quotation: ' + t[:80])


def norm(t):
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"')
    return re.sub(r'\s+', ' ', t)


def plain(path, enc='utf-8'):
    t = open(path, encoding=enc, errors='replace').read()
    t = re.sub(r'(?is)<(script|style).*?</\1>', ' ', t)
    return norm(html.unescape(re.sub(r'<[^>]+>', ' ', t)))


bell = norm(open(os.path.join(SRC, 'bell-1881-reminiscences-of-a-ranger-djvu.txt'), encoding='utf-8', errors='replace').read())
ru = norm(next(iter(json.load(open(os.path.join(SRC, 'record-union-1885-03-10-p4-fulltext.json'), encoding='utf-8')).values()))['full_text'])
M = lambda rel: plain(os.path.join(MIRROR, 'scvhistory', rel), 'cp1252')
CHECK = {'BELL': bell, 'TEJON': bell, 'BORN': bell, 'RU85': ru}
if os.path.isdir(MIRROR):
    CHECK.update({'RSF': M('perkins-rsf-1957.htm'), 'CUT': M('signal/perkins/notes.html'), 'LP23': M('lapuerta2023.htm'),
                  'SOLEDAD': M('signal/perkins/part05.html'), 'SPIKE': M('pollack0910lang.html'), 'CENT': M('golden-spike-centennial-index.htm')})
    CHECK['BELL'] = bell + ' ' + M('signal/perkins/part04.html')
TITLES = ('More Pioneer Staging,', 'The Story of Our Valley,', 'Early Transportation', 'Rancho San Francisco: A Study of a California Land Grant',
          'La Puerta: Gateway to the Santa Clarita Valley,', 'Golden Spike Joins Rails in Soledad Canyon: Contemporary Accounts,', 'San Francisco Items,',
          'Lang Station Golden Spike Centennial, 1876-1976: Historical Program,', 'Reminiscences of a Ranger,', 'Golden Spike at Lang Station.')
missing = []
for k, text in CHECK.items():
    note = NOTES[k]
    # a quotation inside a quotation ("... "x" ...") is checked as its outer passage with the inner marks kept
    quotes = [q for q in re.findall(r'"([^"]+)"', note) if len(q) >= 12]
    for q in quotes:
        q2 = re.sub(r'\s+', ' ', q).strip()
        if q2 in TITLES or q2.rstrip(',') in TITLES:
            continue
        if q2.rstrip('.,') not in text:
            missing.append(f'{k}: {q2[:100]}')
for p in BODY:
    for q in [q for q in re.findall(r'"([^"]+)"', p) if len(q) >= 8]:
        if not any(q.rstrip('.,') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')
# 10 March 1885 was a Tuesday, so "Sunday" is 8 March
import datetime
if datetime.date(1885, 3, 10).strftime('%A') != 'Tuesday' or datetime.date(1885, 3, 8).strftime('%A') != 'Sunday':
    missing.append('the weekday reading of the Record-Union is wrong')
if missing:
    raise SystemExit('quotations not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_banning_profile_2026_10_06.py, Claude Code',
    'person': {'id': 18714, 'title': 'Phineas Banning'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'birthplace': 'Wilmington, Delaware',
        'deathDate': 'March 8, 1885', 'deathDateEdtf': '1885-03-08', 'deathEvidence': 'contemporary',
        'occupation': 'Stage line and freight operator',
        'wikidataId': 'Q7186337', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Phineas_Banning',
    },
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Phineas Banning #18714: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_banning_profile_2026_10_06.php) reads the .json beside this file.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'Death: the Sacramento Daily Record-Union of Tuesday, 10 March 1885, says he "died Sunday" at the Occidental Hotel; the Sunday before was 8 March 1885. deathEvidence is contemporary (a report two days later). No birth date is set: Bell (1881) gives his birthplace and his age, "fifty-one years old", which does not fix a date. Wikidata Q7186337 and the English Wikipedia sitelink Phineas_Banning are finding aids only.', '',
       '## What rests on Wikipedia alone (not in the body)', '',
       '- Birth date, "August 19, 1830" (infobox, uncited). Bell\'s "fifty-one years old" in 1881 agrees with the year but is not a date.',
       '- Death "after being knocked down and run over by a passing express wagon" and burial at Angelus-Rosedale Cemetery: the article cites the Los Angeles Times of 10 March 1885, not read. The Record-Union item says only that he died at the Occidental Hotel.',
       '- His seat in the State Senate, the Los Angeles & San Pedro Railroad sold to the Southern Pacific in 1873, the Los Angeles Common Council term of 1858 to 1859, his marriages and children, Drum Barracks and the honorary brigadier general\'s title. None is a valley matter; the article cites Krythe (1957), Queenan (1986) and a 2023 Los Angeles Times column for most of it.',
       '- Nothing in the article mentions the Santa Clarita Valley, the San Fernando Pass, Fort Tejon\'s road or Lang.', '',
       '## Read and not used', '',
       '- Jerry Reynolds, chapters 22, 27, 38, 39 and 51 (archive articles #2069, #2079, #2101, #2103, #2127): the 1854 ride (retold from Bell), Banning\'s "improvements" to the road over Fremont\'s Pass before 1862, his line to San Pedro among the rights of way Los Angeles bought for the Southern Pacific, his presence at Lang in 1876, and "the Sawmill Mountains commemorate his early milling operations" (no source given). Reynolds is a popular history with known errors on the pass (inventory/review/edward-f-beale-sources.md); the body uses the earlier accounts instead.',
       '- Leon Worden, "Movie trivia from Beale\'s Cut" (articles #12290 and #12370): "General Phineas Banning drove the first stage through the pass in 1854 when it was only 30 feet deep." The depth has no source (the Beale dossier, C11).',
       '- Clarence Cullimore, Old Adobes of Forgotten Fort Tejon (1949): Banning teamed the redwood telegraph poles of 1860 from Wilmington. Not a valley matter as stated.',
       '- The daily papers of 1854 to 1876 themselves (Los Angeles Star, Daily Alta California): the California Digital Newspaper Collection refused automated reading on 6 October 2026 and the Library of Congress holds no California daily of September 1876. They are quoted here only through Worden (2023) and Pollack (2010), and say so.', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} sources; written {os.path.relpath(OUT)}.json and .md')
