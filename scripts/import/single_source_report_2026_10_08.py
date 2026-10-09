# READ ONLY. Writes inventory/review/overnight-2026-10-08/single-source-claims-2026-10-08.md from the census JSON
# (single_source_claims_2026_10_08.py). Adds one flag to each claim: whether the archive's own sentence attributes or
# hedges it ("Reynolds gives", "by the City's account", "the sources differ") or states it plainly.
import json, os, re, sys, collections

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(os.path.dirname(HERE))
sys.path.insert(0, HERE)
from _reads import reads

SRC = os.path.join(ROOT, 'inventory/review/overnight-2026-10-08/single-source-claims-2026-10-08.json')
MD = os.path.join(ROOT, 'inventory/review/overnight-2026-10-08/single-source-claims-2026-10-08.md')
reads([
    ('file', 'the census JSON', SRC),
    ('record', 'claim texts and footnote texts copied into it from Craft', 'the sources the footnotes cite', 'not read: the report lists citations, it does not check them against their sources'),
])
r = json.load(open(SRC))
C = r['claims']
HEDGE = re.compile(r"\b(says?|said|writes?|wrote|according to|account|accounts|gives?|calls?|credits?|reports?|reported|believes?|thought|disagree|differ|differs|uncertain|perhaps|probably|may have|is said|claimed|its own|attribut\w*|in the words|quotes?|lists?|names? him|names? her|records? (him|her|that)|by one|by another|not known|unknown|unclear)\b", re.I)
for c in C:
    c['hedged'] = bool(c['kind'] == 'body' and HEDGE.search(c['claim']))
    c['plain'] = not c['hedged']
json.dump(r, open(SRC, 'w'), indent=1, ensure_ascii=False)

PR = 'Perkins/Reynolds (one source, PROFILES.md)'
one = [c for c in C if c['sourceCount'] == 1]
zero = [c for c in C if c['sourceCount'] == 0]
kinds = ['body', 'field', 'kin', 'calendar', 'term', 'affiliation', 'education', 'candidacy']
KLABEL = {'body': 'Profile text (citation spans)', 'field': 'Birth, death, birthplace, burial fields', 'kin': 'Kinship links', 'calendar': 'Confirmed On This Day dates', 'term': 'Office terms', 'affiliation': 'Affiliations', 'education': 'Education records', 'candidacy': 'Candidacies (election results)'}
tot = collections.Counter(c['kind'] for c in C)
n1 = collections.Counter(c['kind'] for c in one)
n0 = collections.Counter(c['kind'] for c in zero)
plain1 = collections.Counter(c['kind'] for c in one if c['plain'])
persons_with_one = len({c['personId'] for c in one})

def cut(s, n=230):
    s = re.sub(r'\s+', ' ', s or '').strip().replace('|', '/')
    return s if len(s) <= n else s[:n - 1].rsplit(' ', 1)[0] + '...'

REF = r.get('refTitles', {})
def label(f):
    if f.startswith('archive record #'):
        t = REF.get(f.split('#')[1])
        return f'{f}, "{t}"' if t else f
    if f.startswith('work '):
        return '"' + f[5:].rstrip(' ,:;') + '"'
    if f.startswith('URL '):
        return f[4:]
    return f

def src(c):
    return label(c['sources'][0]) if c['sources'] else ''

L = []
w = L.append
w('# Claims on person records that rest on a single source, 8 October 2026')
w('')
w('Item 11 of the overnight run. Nathan: "Every claim on a person record that rests on a single source, listed. After the Perkins trust rule and the del Valle dates I want to know how much of the archive stands on one leg."')
w('')
w('Read only. Nothing in the database was changed. Built by `scripts/import/dump_person_claims_2026_10_08.php` (the Craft records, to `_person-claims-dump.json`), `scripts/import/single_source_claims_2026_10_08.py` (the census, to the JSON beside this file) and `scripts/import/single_source_report_2026_10_08.py` (this report). Every claim, with its footnotes and what each footnote was counted as, is in `single-source-claims-2026-10-08.json`.')
w('')
w('**What was read.** The Craft records: 254 live person records and the 423 office holdings, 30 affiliations, 10 education records, 539 candidacies and 127 elections that point at them. The census counts the citations each record makes, by the archive\'s own rules of independence. It did not open any cited source, so it says how many legs a claim has, not whether any leg holds.')
w('')
w('## Headline')
w('')
w(f'**{len(C)} claims** were found on person records and the records that hang from them. **{len(one)} ({100*len(one)//len(C)} per cent) rest on one source**, on {persons_with_one} people. A further **{len(zero)} carry no source the census could count**: a date field with no footnote anywhere on the record, a candidacy with no footnote on it or its election, a sentence whose only note is reasoning. {len(C)-len(one)-len(zero)} rest on two or more independent sources.')
w('')
w('| Kind of claim | Claims | One source | of which stated plainly | No source counted | Two or more |')
w('| --- | ---: | ---: | ---: | ---: | ---: |')
for k in kinds:
    if tot[k]:
        w(f'| {KLABEL[k]} | {tot[k]} | {n1[k]} | {plain1[k]} | {n0[k]} | {tot[k]-n1[k]-n0[k]} |')
w(f'| **All** | **{len(C)}** | **{len(one)}** | **{sum(plain1.values())}** | **{len(zero)}** | **{len(C)-len(one)-len(zero)}** |')
w('')
w('"Stated plainly" is a test on the profile sentence only: a sentence that attributes its point ("Reynolds gives", "by the City\'s account", "the sources differ") is counted as hedged. Fields, terms and candidacies have no sentence to hedge, so every one-source field, term and candidacy counts as plain.')
w('')
fam = collections.Counter(src(c) for c in one)
w('### What the one-source claims rest on')
w('')
w('| The one source | Claims |')
w('| --- | ---: |')
for f, n in fam.most_common(30):
    w(f'| {f.replace("|", "/")} | {n} |')
rest = sum(n for f, n in fam.most_common()[30:])
w(f'| {len(fam) - 30} other sources, each under 4 claims | {rest} |')
w('')
w('Six sources carry more than two fifths of the weight: the County\'s election returns (in CEDA\'s compilation or the County\'s own statement), Perkins and Reynolds counted together as the PROFILES.md rule requires, Leon Worden\'s roster of the Hart board, the City\'s own biographies of its council members, the Secretary of State\'s Statements of Vote, and Leon Worden\'s City Council ledger. The returns and the Statements are official counts, and one leg is what an election result normally has. The Hart roster and Perkins/Reynolds are the sources the archive\'s own rules mark as needing a second witness. The City\'s biographies are a member\'s office describing the member.')
w('')
w('## How a claim was counted')
w('')
w('- **Profile text.** A claim is a citation span: the text from the previous marker group, or the start of the paragraph, to a marker group such as `[1][3]`. It is the writer\'s own unit of citation, and may hold more than one sentence. Only bodies that publish (`editorial-2026`, `legacy-leon`) are counted.')
w('- **Fields.** `birthDate` and `deathDate` are found in the spans that hold the year and a word of birth or death; `birthplace` and `burialPlace` in the spans that hold the place\'s first word. If no span holds it, a footnote naming the date or place is used; failing both, the field is "no citation located".')
w('- **Kinship.** Each `spouseOf`, `childOf` and `siblingOf` link, sourced from the spans that name the relative.')
w('- **On This Day.** Only `recordDates` rows ticked Confirmed, the ones that publish.')
w('- **Terms, affiliations, education, candidacies.** All the footnotes on the record; for a candidacy, also those on its election.')
w('')
w('**Independence**, from docs/PROFILES.md and DATA-MODEL.md:')
w('')
w('- Perkins and Reynolds are one source (Nathan, 3 October 2026). A Leon Worden column cited beside either is the same account unless its footnote names another source (4 October 2026).')
w('- CEDA compiles the County\'s returns, so CEDA and the County\'s statement are one source.')
w('- The City\'s biographies of a council member (1998, 2008, 2013 and the current council page) are one source: the City\'s own account.')
w('- A page and its Wayback copy are one source.')
w('- A note that names no document is reasoning, not a witness: a term length from the Education Code, "the seat was next filled at the election of...", a district-lines remark. It is listed on the claim in the JSON and not counted.')
w('')
w('## Limits')
w('')
w('- The census reads the citations, not the sources. Two citations it counts as independent may not be: a City biography and a magazine profile drawn from the same press kit, two papers carrying one wire story, a Worden caption that repeats Reynolds without naming him. It can only undercount single-source claims, never overcount them.')
w('- Field location is by year and place word. A field whose year appears in a span about something else would be credited with that span\'s sources. Spot-checked on 20 fields: three had been credited with a neighbouring span\'s sources by year alone (Rioux, Reynolds, Knight), so the census now prefers a span holding the whole date as printed; two of the three then matched their own sentence. It is still a heuristic.')
w('- A span is the writer\'s unit of citation, and a long span with many markers credits all of them to every point in it. Where one marker in such a span is the only witness to one of its points, the census cannot tell, so it undercounts.'); w('')
w('- A term whose footnotes cite CEDA and the Hart roster counts as two sources. Where the roster is the only source for its dates and CEDA only for the election, that is generous.')
w('- Candidacies that are not linked to a person record (297 of 539) are not person claims and are not counted.')
w('')

# Priority lists.
def table(rows, cols):
    w('| ' + ' | '.join(h for h, _ in cols) + ' |')
    w('| ' + ' | '.join('---' for _ in cols) + ' |')
    for c in rows:
        w('| ' + ' | '.join(str(fn(c)) for _, fn in cols) + ' |')
    w('')

w('## The legs the archive\'s rules already doubt')
w('')
w('### Perkins or Reynolds alone')
w('')
w('Under the reliability rule, Perkins is trusted only where he transcribes a document; his summaries, hearsay, "firsts", family labels and lesser-event dates are unverified until a primary is found, and Reynolds repeating him is not a second witness. These claims have nothing else under them.')
w('')
pr = [c for c in one if c['sources'] == [PR]]
table(sorted(pr, key=lambda c: (c['person'], c['kind'])), [('Person', lambda c: f"{c['person']} #{c['personId']}"), ('Kind', lambda c: c['kind']), ('Claim', lambda c: cut(c['claim'] if c['kind'] == 'body' else f"{c['claim']}: {c.get('value')}")), ('Hedged in text', lambda c: 'yes' if c['hedged'] else 'no')])
w('### The Hart board roster alone')
w('')
w('PROFILES.md: the roster contradicts itself at least once (Hanrion) and was wrong twice about who stood (Loberg, King); "where it is the only source for a row and the row is unclear or disagrees with a neighbouring row, the holding footnotes both and states neither", and a whole-roster pass for self-contradictions "is worth doing before any profile rests on a roster row alone." These rest on a roster row alone.')
w('')
hr = [c for c in one if c['sources'][0].startswith('Hart board roster')]
table(sorted(hr, key=lambda c: (c['person'], c.get('value') or '')), [('Person', lambda c: f"{c['person']} #{c['personId']}"), ('Record', lambda c: f"{c['recordSection']} #{c['recordId']}" if c.get('recordId') else 'profile text'), ('Term or claim', lambda c: cut(c.get('value') or c['claim'], 120)), ('Evidence', lambda c: f"{c.get('evidence', '')}/{c.get('endEvidence', '')}")])
w('### Find a Grave, the City\'s own account, a commission page')
w('')
other = [c for c in one if c['sources'][0] in ('Find a Grave', 'City of Santa Clarita biographies (the City\'s own account of a member)') or c['sources'][0].startswith('URL santaclarita.gov/commission')]
table(sorted(other, key=lambda c: (src(c), c['person'])), [('Person', lambda c: f"{c['person']} #{c['personId']}"), ('Kind', lambda c: c['kind'] + (f" #{c['recordId']}" if c.get('recordId') else '')), ('Claim', lambda c: cut(c['claim'] if c['kind'] == 'body' else f"{c['claim']}: {c.get('value')}", 160)), ('Source', lambda c: src(c))])
w('No claim on a person record rests on Wikipedia alone; no footnote on a person record cites Wikipedia at all.')
w('')

w('## Fields with no source located on the record')
w('')
w('These print on the person page (birth, death, birthplace, burial) and no footnote on the record names them. Several have an evidence level set, which says someone rated a source the record does not cite. 25 of them sit on the seven people whose WordPress profile is withheld (Wilk, Manly, Marshall, Bryant, Anza, Portolá, Garcés): their fields came in with the withheld text.')
w('')
fz = [c for c in zero if c['kind'] == 'field']
table(sorted(fz, key=lambda c: (c['person'], c['claim'])), [('Person', lambda c: f"{c['person']} #{c['personId']}"), ('Field', lambda c: c['claim']), ('Value', lambda c: cut(c.get('value'), 90)), ('Evidence set', lambda c: c.get('evidence') or '(none)')])
oz = [c for c in zero if c['kind'] != 'field']
w('### Other claims with no source counted')
w('')
table(sorted(oz, key=lambda c: (c['kind'], c['person'])), [('Person', lambda c: f"{c['person']} #{c['personId']}"), ('Kind', lambda c: c['kind'] + (f" #{c['recordId']}" if c.get('recordId') else '')), ('Claim', lambda c: cut(c['claim'] if c['kind'] in ('body', 'calendar') else f"{c['claim']}: {c.get('value')}", 200)), ('What its notes are', lambda c: ', '.join(sorted({f['kind'] for f in c['footnotes']})) or 'no footnote')])

w('## Read from a description')
w('')
w('Claims stated as fact where the record\'s own footnote says the point was read from something about the source, not the source. Listed, not fixed.')
w('')
w('1. **Antonio del Valle #291, body, the patent acreage.** "The United States patent of 1875, issued after the heirs\' claim was confirmed, was for 48,611.88 acres." Footnote 4: "As read for the archive\'s del Valle source review of 29 September 2026; the patent itself is not in the archive." The figure comes from the source review\'s account of the patent (a dossier), not the patent. Its span also cites Reynolds, but Reynolds gives the diseño\'s 48,829, not the patent\'s figure, so for 48,611.88 the review is the only leg.')
w('2. **Sanford Lyon #20224, body.** "He was at the Pico Canyon oil seeps by 1866, when a Los Angeles paper reported that \'Mr. S. Lyon is busy dipping the oil from the holes.\'" Footnote: "Los Angeles Semi-Weekly News, 1 June 1866, quoted in A.B. Perkins, \'History of Pico Canyon Oil Production\'... record #1440, note 16." The 1866 paper has not been read; the quotation is Perkins\'s. A Perkins transcription is what the trust rule accepts, so this is within the rule, but the sentence presents the paper, not Perkins, as the witness.')
w('3. **Antonio del Valle #291, deathDate "on or before 3 June 1841" and the body sentence "He died without a will on or before 3 June 1841, the day he was buried at Mission San Fernando."** One source: the ECPP index, "a typed transcription of the registers, not the register page, which has not been seen", and the identification of "Antonio Valle" with Antonio del Valle "is an inference". The caveats are on the record (footnote 7), but the date field states the date with `deathEvidence` contemporary, and it is the date that replaced two published ones on 4 October. It stands on an index of the register, and on an inference, alone.')
w('4. **Katie Hill #29332, body.** The civil statute under which she sued is quoted "as quoted in the ruling cited in the next note"; the Civil Code section itself was not read. Minor: the ruling is a court\'s own quotation.')
w('')
w('## Found on the way')
w('')
w('- **Juan Bautista de Anza #301: two fields are broken.** `birthDate` reads "July 7, 1736 Death Date: December 19, 1" (the death label and part of the death date ran into the field) and `birthplace` reads "ronteras, Sonora, New Spain" (first letter lost; Fronteras). His body is withheld WordPress text, but the fields print.')
cal = [c for c in zero if c['kind'] == 'calendar']
w(f'- **On This Day publishes {len(cal)} dates from person records that cite nothing for them.** They are `recordDates` rows ticked Confirmed, so they reach the calendar, and no footnote on the record names them. Eleven of them carry as their label a sentence of a withheld WordPress profile, on seven people (Garcés twice, Henry Mayo Newhall, Anza, Crespí twice, Serra twice, Rodolfo Acosta twice, Ygnacio del Valle), several in the form of an encyclopedia lead, "Juan Bautista de Anza (July 7, 1736 - December 19, 1788) was a Spanish colonial military officer..."; where they came from is not recorded, so they are unsourced leads, not facts. Henry Mayo Newhall\'s is "Born May 13, 1825", the day his dossier holds open against 23 May.')
w('- **Henry Mayo Newhall #283, `birthDate` "May 13, 1825", no footnote on the record.** The HMN dossier (inventory/review/henry-mayo-newhall-sources.md, C1) records 13 May against 23 May as open. Passed to the disputed-as-fact report (item 12).')
w('- **Abel Stearns #309, `birthDate` "February 9, 1798", `birthEvidence` uncited.** His own editor note says no source has been found for the day; the field still prints it as a full date.')
w(f'- **Bodies with no footnote markers: {len(r["unfootnotedBodies"])}.** Seven are withheld WordPress text; two publish as Leon\'s own (`legacy-leon`): Ygnacio del Valle #293 and Henry Mayo Newhall #283. Each is one source, Leon, for every sentence in it, and neither record carries a footnote.')
w(f'- **Uncited closing sentences in footnoted profiles: {len(r["uncitedTails"])}**, all the archive\'s own remarks ("No source in this archive places him in the Santa Clarita Valley."), listed in the JSON.')
w('')

w('## Every one-source claim, by person')
w('')
w('Profile spans, fields, kinship and calendar dates by person; then terms, affiliations, education and candidacies by the source they rest on. "H" marks a profile sentence that attributes or hedges its point.')
w('')
byp = collections.defaultdict(list)
for c in one:
    if c['kind'] in ('body', 'field', 'kin', 'calendar'):
        byp[(c['person'], c['personId'])].append(c)
for (name, pid), cs in sorted(byp.items()):
    w(f'### {name} (#{pid}): {len(cs)}')
    w('')
    for c in cs:
        if c['kind'] == 'body':
            w(f'- {"H " if c["hedged"] else ""}"{cut(c["claim"], 260)}" ({src(c)})')
        else:
            w(f'- {c["kind"]} `{c["claim"][:60]}` = {cut(c.get("value"), 80)} ({src(c)})')
    w('')
w('### Terms, affiliations, education and candidacies resting on one source')
w('')
recs = [c for c in one if c['kind'] in ('term', 'affiliation', 'education', 'candidacy')]
byf = collections.defaultdict(list)
for c in recs:
    byf[src(c)].append(c)
for f, cs in sorted(byf.items(), key=lambda x: -len(x[1])):
    w(f'#### {f}: {len(cs)}')
    w('')
    for c in sorted(cs, key=lambda c: (c['kind'], c['person'])):
        w(f'- {c["kind"]} #{c.get("recordId")}: {cut(c["claim"], 140)}; {cut(c.get("value"), 60)}; evidence {c.get("evidence") or "(none)"}')
    w('')
open(MD, 'w').write(re.sub(r'\s*\u2014\s*', lambda m: ': ' if m.group(0).startswith(' ') else '-', '\n'.join(L)) + '\n')
print(f'{len(C)} claims; {len(one)} on one source ({sum(plain1.values())} stated plainly); {len(zero)} with none counted')
print('wrote', MD)
