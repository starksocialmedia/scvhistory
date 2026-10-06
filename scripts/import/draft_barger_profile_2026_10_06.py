#!/usr/bin/env python3
"""Kathryn Barger #29288: the profile draft (Nathan, 6 October 2026: "The Wikipedia census: Import the 12 and build
profiles from the primary sources those articles point at. Label anything resting on Wikipedia alone."). Claude Code,
research subagent.

Writes inventory/review/kathryn-barger-profile-draft-2026-10-06.json (read by
scripts/import/build_barger_profile_2026_10_06.php) and the .md beside it for reading.
The body carries {KEY} markers; they are numbered here by first appearance, one [n] per note.

She is living: public life only. No birth fields, no family beyond what her own County biography says of her career.
Every quotation below was read on 6 October 2026 in the copy saved in inventory/sources/kathryn-barger-2026-10-06/
(manifest.json there), or, for Leon Worden's page LW3109, in the Reggie mirror. The Statements of Votes Cast are
checked by figure against the pdftotext copies beside the PDFs. No quotation joins two clauses with an ellipsis.
Run on the host: python3 scripts/import/draft_barger_profile_2026_10_06.py
"""
import html, json, os, re

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..')
OUT = os.path.join(ROOT, 'inventory', 'review', 'kathryn-barger-profile-draft-2026-10-06')
SRC = os.path.join(ROOT, 'inventory', 'sources', 'kathryn-barger-2026-10-06')
MIRROR = '/Volumes/Reggie/SCVHistory/scvhistory.com'

NOTES = {
    'LW3109': 'Leon Worden, "Kathryn Barger, Los Angeles County 5th District Supervisor, 2016-," SCVHistory.com LW3109, /scvhistory/lw3109.htm (her portrait, asset #31241, is its photograph; the page is not yet a record). "Kathyrn Barger was elected in November 2016 to succeed Michael D. Antonovich as supervisor for Los Angeles County\'s 5th District, which includes the Santa Clarita Valley." "Antonovich held the office for 36 years and was forced into retirement under term limits that were approved by voters in 2002." "Barger was his longtime deputy and chief of staff." "Barger\'s election, which coincided with that of 4th District Supervisor Janice Hahn, gave the Board of Supervisors a female majority (and a 4-1 supermajority) for the first time." "She is limited to three 4-year terms." The page spells her first name "Kathyrn" twice, as quoted.',
    'COUNTY': 'Office of Supervisor Kathryn Barger, County of Los Angeles, "Meet Supervisor Kathryn Barger," https://kathrynbarger.lacounty.gov/meet-kathryn/ (read 6 October 2026): "Supervisor Kathryn Barger proudly serves the residents of the 5th District"; "spanning over 2,785 square miles, which includes 20 cities and 83 unincorporated communities in the Antelope, San Gabriel, San Fernando, Crescenta, and Santa Clarita Valleys."; "Kathryn began her career in public service as a college intern in the office of former Supervisor Antonovich and rose to become his chief deputy in 2001, where she served until her election to the Board of Supervisors in 2016."; "She both served as Chair of the Board and was reelected for her second term in 2020." The same office\'s page "The Fifth District," https://kathrynbarger.lacounty.gov/get-to-know-fifth-district/, lists among her field offices "Santa Clarita Valley 27441 Tourney Road Suite 120 Santa Clarita, CA 91355".',
    'SOV16P': 'Los Angeles County Registrar-Recorder/County Clerk, Final Official Statement of Votes Cast, Presidential Primary Election, 7 June 2016, by supervisorial district, Supervisor, 5th District, 5th Supervisorial, total: Kathryn Barger 105,520; Mitchell Englander 42,823; Ara James Najarian 46,587; Elan Carr 40,580; Rajpal Kahlon 4,285; Bob Huff 52,359; Billy Malone 8,701; Darrell Park 55,185 (356,040 votes for the office; Barger 29.6 per cent), https://www.lavote.gov/documents/SVC/980_SVCDistrict.pdf.',
    'SOV16G': 'Los Angeles County Registrar-Recorder/County Clerk, Final Official Statement of Votes Cast, General Election, 8 November 2016, Supervisor, 5th District, https://www.lavote.gov/documents/SVC/3496_SVC_bydistrict.pdf: by supervisorial district, 5th Supervisorial, total, Darrell Park 255,165, Kathryn Barger 350,998 (57.9 per cent); by community, City of Santa Clarita, total, Park 27,052, Barger 42,080. Her term is the archive\'s office holding #29290.',
    'SOV20': 'Los Angeles County Registrar-Recorder/County Clerk, Final Official Statement of Votes Cast, Presidential Primary Election, 3 March 2020, Supervisor, 5th District, 5th Supervisorial, total: John C. Harabedian 84,199, Darrell Park 84,611, Kathryn Barger 240,403 (58.7 per cent of 409,213), https://www.lavote.gov/docs/rrcc/svc/4085_SVC_District_Final.pdf.',
    'SOV24': 'Los Angeles County Registrar-Recorder/County Clerk, Final Official Statement of Votes Cast, Presidential Primary Election, 5 March 2024, Supervisor 5th District, 5th Supervisorial, total: Perry Goldberg 26,588, Chris Holden 76,429, K Anthony 39,801, Kathryn Barger 198,083, Marlon Marroquin 7,767 (Barger 56.8 per cent of 348,668), https://content.lavote.gov/docs/rrcc/svc/4316_final_svc_districts.pdf. The column order was read from the PDF\'s word positions, since the names are printed above the figures.',
    'PR17': 'Office of Supervisor Kathryn Barger, "County Certifies Environmental Impact Reports for Landmark Village and Mission Village," 21 July 2017, https://kathrynbarger.lacounty.gov/county-certifies-environmental-impact-reports-for-landmark-village-and-mission-village/: "On a 4-0 vote with Supervisor Kuehl abstaining, the Board of Supervisors certified the environmental impact reports for Mission Village and Landmark Village, part of the Newhall Ranch Specific Plan in the Santa Clarita Valley." Barger: "Having adopted measures to protect wildlife and water resources, the projects provide needed housing".',
    'PROLD': 'Office of Supervisor Kathryn Barger, "L.A. County Invests $250M to Improve Old Road in Santa Clarita Region," 14 March 2024, https://kathrynbarger.lacounty.gov/l-a-county-invests-250m-to-improve-old-road-in-santa-clarita-region/: "The proposed multi-year road improvement project targets the stretch of the road along the unincorporated communities by Stevenson Ranch"; "The project would reconstruct and widen portions of the Old Road to six lanes and add a protected bicycle lane in each direction."; "including the Old Road over the Santa Clara River bridge, which is currently classified as structurally deficient by the Federal Highway Administration". Barger: "It has taken six years to get to this point".',
    'PRHART': 'Office of Supervisor Kathryn Barger, "L.A. County Supervisors Green Light Transfer of Historic William S. Hart Park and Museum to City of Santa Clarita," 6 August 2024, https://kathrynbarger.lacounty.gov/l-a-county-supervisors-green-light-transfer-of-historic-william-s-hart-park-and-museum-to-city-of-santa-clarita/: "Today, the Los Angeles County Board of Supervisors unanimously approved transferring ownership of William S. Hart Park and the Hart Museum to the City of Santa Clarita."; "Supervisor Kathryn Barger, who introduced the motion that details the terms of the transfer"; Barger: "Los Angeles County is not losing a park, but is instead gaining a partner to steward this beautiful and historic site."; "Terms of the transfer include the continued posting of hours of operation on the park\'s website and on-site to ensure awareness of public access and not enacting differential program fees, rental fees, or priority reservations for city or non-city residents."',
    'PRCHIQ': 'Office of Supervisor Kathryn Barger, "L.A. County Addresses Chiquita Canyon Landfill Closure: Planning for Impact and Protection," 31 December 2024, https://kathrynbarger.lacounty.gov/l-a-county-addresses-chiquita-canyon-landfill-closure-planning-for-impact-and-protection/: "Chiquita Canyon, LLC has announced that the Chiquita Canyon Landfill is closing active waste disposal operations effective January 1, 2025."; "said Los Angeles County Board of Supervisors Chair Kathryn Barger"; Barger: "I will introduce a motion at the next Board of Supervisors meeting directing Public Works to conduct a comprehensive assessment of the closure\'s implications."; "my top priority, though, continues to be bringing relief to the community that continues being afflicted by the landfill\'s noxious odors."; "The lawsuit Los Angeles County has filed against Chiquita Canyon Landfill\'s owners and operators, pursuing relief for impacted communities, seeks to right that wrong."',
}

BODY = [
    'Kathryn Barger has represented the Santa Clarita Valley on the Los Angeles County Board of Supervisors since 2016, as supervisor for the 5th District.{LW3109}{COUNTY} The district, the County\'s largest at more than 2,785 square miles, takes in 20 cities and 83 unincorporated communities in the Antelope, San Gabriel, San Fernando, Crescenta and Santa Clarita valleys, and her office keeps a Santa Clarita Valley field office on Tourney Road.{COUNTY} She led a field of eight in the primary of 7 June 2016 with 105,520 votes, and on 8 November 2016 she beat Darrell Park across the district, 350,998 votes to 255,165; in the City of Santa Clarita the count was 42,080 to 27,052.{SOV16P}{SOV16G} She was re-elected in 2020, winning the primary of 3 March with 240,403 of the 409,213 votes cast for the office, and in the primary of 5 March 2024 she took 198,083 of 348,668.{COUNTY}{SOV20}{SOV24}',
    'She succeeded Michael D. Antonovich, who had held the office for 36 years and could not run again under the term limits the voters approved in 2002. She had worked for him since her college years, starting as an intern in his office and rising in 2001 to be his chief deputy, and she held that post until her own election.{LW3109}{COUNTY} Her election, with Janice Hahn\'s in the 4th District, gave the Board a female majority for the first time. She is limited to three four-year terms.{LW3109} She has served as Chair of the Board, and held the chair at the end of 2024.{COUNTY}{PRCHIQ}',
    'Her office has announced several of the Board\'s decisions on the valley. In July 2017 the Board certified, 4 to 0, the environmental impact reports for Mission Village and Landmark Village, part of the Newhall Ranch Specific Plan; she said the projects "provide needed housing" with measures to protect wildlife and water.{PR17} In March 2024 her office announced the County\'s plan to rebuild and widen The Old Road beside Stevenson Ranch to six lanes and to replace its bridge over the Santa Clara River, which the Federal Highway Administration classed as structurally deficient; she said it had taken six years to reach that point.{PROLD} In August 2024 the Board unanimously approved her motion to transfer William S. Hart Park and the Hart Museum to the City of Santa Clarita, on terms that keep the park open to every County resident on the same fees; "Los Angeles County is not losing a park," she said, "but is instead gaining a partner to steward this beautiful and historic site."{PRHART} When the Chiquita Canyon Landfill stopped taking waste on 1 January 2025, she said she would ask Public Works to study what the closing meant, and that her first concern remained relief for the neighbors still troubled by the landfill\'s odors, for which the County had sued its owners and operators.{PRCHIQ}',
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
body = '\n\n'.join(re.sub(r'\{([A-Z0-9]+)\}', lambda m: f'[{num[m.group(1)]}]', p) for p in BODY)
footnotes = [{'number': str(num[k]), 'key': k, 'note': NOTES[k]} for k in order]
for t in [body] + [f['note'] for f in footnotes]:
    if '—' in t:
        raise SystemExit('an em dash in our own text: ' + t[:80])
    if re.search(r'\.\.\.|…', t):
        raise SystemExit('an ellipsis in our own text or a quotation: ' + t[:80])


def plain(path, enc='utf-8'):
    t = open(path, encoding=enc, errors='replace').read()
    t = re.sub(r'(?is)<(script|style).*?</\1>', ' ', t)
    t = html.unescape(re.sub(r'<[^>]+>', ' ', t))
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace('–', '-')
    return re.sub(r'\s+', ' ', t)


def wp(name):
    d = json.load(open(os.path.join(SRC, name), encoding='utf-8'))[0]
    t = html.unescape(re.sub(r'<[^>]+>', ' ', d['content']['rendered']))
    t = t.replace('’', "'").replace('‘', "'").replace('“', '"').replace('”', '"').replace('–', '-')
    return re.sub(r'\s+', ' ', t)


CHECK = {
    'COUNTY': plain(os.path.join(SRC, 'barger-meet-kathryn.html')) + ' ' + plain(os.path.join(SRC, 'barger-get-to-know-fifth-district.html')),
    'PR17': wp('barger-pr-county-certifies-environmental-impact-reports-for-landmark-village-and-mission-village.json'),
    'PROLD': wp('barger-pr-l-a-county-invests-250m-to-improve-old-road-in-santa-clarita-region.json'),
    'PRHART': wp('barger-pr-l-a-county-supervisors-green-light-transfer-of-historic-william-s-hart-park-and-museum-to-city-of-santa-clarita.json'),
    'PRCHIQ': wp('barger-pr-l-a-county-addresses-chiquita-canyon-landfill-closure-planning-for-impact-and-protection.json'),
}
lp = os.path.join(MIRROR, 'scvhistory', 'lw3109.htm')
if os.path.exists(lp):
    CHECK['LW3109'] = plain(lp, 'cp1252')
TITLES = ('Kathryn Barger, Los Angeles County 5th District Supervisor, 2016-,', 'Meet Supervisor Kathryn Barger,', 'The Fifth District,',
          'County Certifies Environmental Impact Reports for Landmark Village and Mission Village,', 'L.A. County Invests $250M to Improve Old Road in Santa Clarita Region,',
          'L.A. County Supervisors Green Light Transfer of Historic William S. Hart Park and Museum to City of Santa Clarita,',
          'L.A. County Addresses Chiquita Canyon Landfill Closure: Planning for Impact and Protection,')
missing = []
for k, text in CHECK.items():
    for q in [q for q in re.findall(r'"([^"]+)"', NOTES[k]) if len(q) >= 12]:
        q2 = re.sub(r'\s+', ' ', q)
        if q2 in TITLES:
            continue
        if q2.rstrip('.,') not in text:
            missing.append(f'{k}: {q2[:90]}')
for p in BODY:
    for q in [q for q in re.findall(r'"([^"]+)"', p) if len(q) >= 8]:
        if not any(q.rstrip('.,') in NOTES[k] for k in NOTES):
            missing.append(f'body quotation not in any note: {q}')

# The vote figures, against the pdftotext copies.
def sov(name):
    return open(os.path.join(SRC, name), encoding='utf-8', errors='replace').read()
FIG = {
    'lavote-980-2016-06-07-svc-district.txt': r'TOTAL\s+1039765\s+446222\s+105520\s+42823\s+46587\s+40580\s+4285\s+52359\s+8701\s+55185',
    'lavote-3496-2016-11-08-svc-bydistrict.txt': r'TOTAL\s+1093450\s+791616\s+255165\s+350998',
    'lavote-4085-2020-03-03-svc-district.txt': r'TOTAL\s+1179667\s+499399\s+84199\s+84611\s+240403',
    'lavote-4316-2024-03-05-svc-districts.txt': r'TOTAL\s+1,158,940\s+408,067\s+26,588\s+76,429\s+39,801\s+198,083\s+7,767',
}
for f, rx in FIG.items():
    if not re.search(rx, sov(f)):
        missing.append(f'figures not found in {f}')
if not re.search(r'CITY OF SANTA CLARITA\s+117972\s+53293\s+16568\s+24105\s+VOTE BY MAIL\s+0\s+37654\s+10484\s+17975\s+TOTAL\s+117972\s+90947\s+27052\s+42080', sov('lavote-3496-2016-11-08-svc-bydistrict.txt')):
    missing.append('the City of Santa Clarita row of 8 November 2016 not found')
if round(240403 / 409213 * 100, 1) != 58.7 or round(198083 / 348668 * 100, 1) != 56.8 or round(350998 / (350998 + 255165) * 100, 1) != 57.9 or round(105520 / 356040 * 100, 1) != 29.6:
    missing.append('a percentage does not compute')
if 105520 + 42823 + 46587 + 40580 + 4285 + 52359 + 8701 + 55185 != 356040 or 84199 + 84611 + 240403 != 409213 or 26588 + 76429 + 39801 + 198083 + 7767 != 348668:
    missing.append('a total does not add up')
if missing:
    raise SystemExit('quotations or figures not found in their sources:\n' + '\n'.join(missing))

draft = {
    'drafted': '2026-10-06', 'draftedBy': 'scripts/import/draft_barger_profile_2026_10_06.py, Claude Code',
    'person': {'id': 29288, 'title': 'Kathryn Barger'},
    'body': body, 'footnotes': footnotes,
    'fields': {
        'bodyAuthorship': 'editorial-2026',
        'occupation': 'County supervisor',
        'wikidataId': 'Q28086178', 'personWikipediaUrl': 'https://en.wikipedia.org/wiki/Kathryn_Barger',
        'relatedPersonsAdd': [29284],
    },
    'holdingNotes': {
        '29290': 'Elected on 8 November 2016 over Darrell Park, 350,998 votes to 255,165 (Los Angeles County Registrar-Recorder/County Clerk, Final Official Statement of Votes Cast, General Election, 8 November 2016, Supervisor, 5th District, https://www.lavote.gov/documents/SVC/3496_SVC_bydistrict.pdf). Re-elected in the primaries of 3 March 2020 (240,403 of 409,213 votes, https://www.lavote.gov/docs/rrcc/svc/4085_SVC_District_Final.pdf) and 5 March 2024 (198,083 of 348,668, https://content.lavote.gov/docs/rrcc/svc/4316_final_svc_districts.pdf). Succeeded Michael D. Antonovich (Leon Worden, SCVHistory.com LW3109).',
    },
}
json.dump(draft, open(OUT + '.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

md = ['# Kathryn Barger #29288: the profile draft, 6 October 2026', '',
      'For Nathan to read before the loader is applied. Body first, then the notes as they will be numbered. Nothing here is written to Craft; the loader (scripts/import/build_barger_profile_2026_10_06.php) reads the .json beside this file. She is living: public life only, no birth fields.', '',
      '## Body', '', body, '', '## Notes', '']
md += [f"{f['number']}. {f['note']}" for f in footnotes]
md += ['', '## Other fields', '']
md += [f'- {k}: {v}' for k, v in draft['fields'].items()]
md += ['', 'relatedPersons gains Michael D. Antonovich #29284, her predecessor (LW3109). wikidataId and personWikipediaUrl are finding aids only.', '',
       '## Note added to an office holding', '']
md += [f'- #{k}: {v}' for k, v in draft['holdingNotes'].items()]
md += ['', '## What rests on Wikipedia alone (not in the body)', '',
       '- Sworn in on 5 December 2016, and the full name "Kathryn Ann Barger-Leibrich" (the article\'s opening; it cites the Daily News and Pasadena Now of 5 December 2016, not read). The holding keeps the year only.',
       '- The years of her two chairmanships, "December 3, 2019" to "December 8, 2020" and "December 3, 2024" to "December 2, 2025" (infobox; the first is cited to her own post on X). Her County biography says only that she "served as Chair of the Board", and her office\'s release of 31 December 2024 calls her "Board of Supervisors Chair", which is all the body says.',
       '- Her votes on the public defender registration fee (2017) and the business registration program, the Blue Ribbon Commissions on Homelessness and Public Safety, the San Dimas senior housing (2024) and her request after the January 2025 fires to waive state housing laws: cited by the article to newspapers not read here, and none is a valley matter.',
       '- Personal: born 1960, Ohio Wesleyan University, married to a retired sheriff\'s deputy, lives in San Marino, her brother John M. Barger and her father Richards D. Barger. Leon Worden\'s LW3109 also gives "Born June 2, 1960", Ohio Wesleyan in 1983 and San Marino, and her County biography mentions the marriage. Left out: she is living, and the profile keeps to public life.', '',
       '## Read and not used', '',
       '- Her office\'s summary of the Conditional Use Permit for the Chiquita Canyon Landfill expansion, 27 June 2017 (saved): it lists the permit\'s conditions but does not say what part she took, so the body does not use it.',
       '- Her office\'s releases listing "Santa Clarita" number about fifty (wp-json search, 6 October 2026). The body takes four with a lasting mark on the valley: Newhall Ranch\'s Mission and Landmark villages, The Old Road, Hart Park, and the Chiquita Canyon Landfill. Others for a later pass: the Castaic and Quartz Hill skate parks (2017), bookmobiles for the Santa Clarita and Antelope valleys (2018), $47 million for Interstate 5 in the valley (2018), the Placerita Canyon trail repairs (2019), Del Valle and Richard Rioux parks (2020), Honor Ranch (2022), the arts and veterans center with the City (2025), the Castaic Sports Complex renamed for Deputy Ryan Clinkunbroomer (2026).', '']
open(OUT + '.md', 'w', encoding='utf-8').write('\n'.join(md) + '\n')
print(f'{len(body.split())} words, {len(footnotes)} notes; quotations checked against {len(CHECK)} saved sources and figures against 4 Statements of Votes Cast; written {os.path.relpath(OUT)}.json and .md')
