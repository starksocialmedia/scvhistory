#!/usr/bin/env python3
"""Funeral location, burial location, funeral home: verbatim spans from obituary body_text.
Conservative: only explicit phrasings. Empty string when not stated or ambiguous."""
import json, re, sys
from collections import Counter
from pathlib import Path

INV = Path("/workspace/scvhistory/inventory/legacy")
OBITS = INV / "obituaries.json"
REPORT = INV / "obituaries_funeral_report.json"

ABBR = r"(?:Bros|Ste|St|Sts|Mt|Ft|Ave|Blvd|Rd|Dr|Hwy|Jr|Sr|N|S|E|W|Co|Inc|Calif|Mrs|Mr|Rev|Fr)"
MONTHS = r"(?:January|February|March|April|May|June|July|August|September|October|November|December|Jan|Feb|Mar|Apr|Jun|Jul|Aug|Sept|Sep|Oct|Nov|Dec)"
DAYS = r"(?:Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)"
# A place span: starts with a capital (or "the"), ends before a terminator.
PLACE = rf"(?P<place>(?:the\s+)?(?:\b{ABBR}\.|[A-Z0-9])(?:\b{ABBR}\.|\b[A-Z]\.(?=[A-Z ])|[^\n,;().])*?)"
STOP = (
    rf"(?=\s*(?:[,;:(\n.]|$|\s+-\s"
    rf"|\s+on\s+[A-Z]|\s+(?:to\s+)?follow\b|\s+beside\b|\s+also\b|\s+for\s|\s+viewing\b|\s+visitation\b|\s+next\s+to\b|\s+alongside\b"
    rf"|\s+on\s+(?:{DAYS}|{MONTHS}|the\b|\d)|\s+at\s+\d|\s+from\s+\d|\s+beginning\b|\s+starting\b"
    rf"|\s+followed\b|\s+following\b|\s+with\s|\s+immediately\b|\s+in\s+(?:{MONTHS}|the\s+(?:morning|afternoon|spring|fall)|lieu)"
    rf"|\s+(?:{DAYS}|{MONTHS})\b|\s+and\s+(?:burial|interment|inurnment|a\s+reception|reception|the\s+burial)|\s+where\b|\s+under\s+the\b"
    rf"|\s+is\s+in\s+charge|\s+was\s+in\s+charge|\s+will\s+be\b|\s+was\b|\s+were\b|\s+are\s+in\b|\s+handled\b|\s+is\s+handling|\s+for\s+(?:family|friends|all)))"
)

FUNERAL_PATTERNS = [
    rf"(?i:(?:funeral|memorial|celebration[- ]of[- ]life|graveside|visitation|viewing|memorial\s+mass|funeral\s+mass|mass|services?)(?:\s+services?)?\s+(?:will|was|were|is|are)\s+(?:be\s+)?(?:held|celebrated|conducted|scheduled|planned)(?:\s+\w+){{0,6}}?\s+at)\s+{PLACE}{STOP}",
    rf"(?i:(?:funeral|memorial|celebration[- ]of[- ]life|graveside|memorial\s+mass|funeral\s+mass|mass|services?)(?:\s+services?)?\s+(?:will|was|were|is|are)\s+(?:be\s+)?(?:held|celebrated|conducted|scheduled|planned)\s+at\s+\d(?:(?!(?<![ap]\.m)\.\s+[A-Z])[^;\n]){{0,80}}?\s+at)\s+(?!\d){PLACE}{STOP}",
]
BURIAL_PATTERNS = [
    rf"(?i:(?:interment|inurnment|entombment|burial|committal)(?:\s+services?)?\s+(?:will\s+(?:be|follow|take\s+place)|was|followed|took\s+place|is)(?:\s+(?:held|private))?\s+(?:at|in))\s+{PLACE}{STOP}",
    rf"(?i:(?:interment|inurnment|entombment|burial)\s+(?:at|in))\s+{PLACE}{STOP}",
    rf"(?i:(?:was|will\s+be|were|be|is)\s+(?:buried|interred|inurned|entombed|laid\s+to\s+rest)\s+(?:at|in))\s+{PLACE}{STOP}",
]
HOME_PATTERNS = [
    rf"(?i:arrangements\s+(?:are|were|have\s+been|will\s+be)?\s*(?:being\s+)?(?:made\s+|handled\s+|entrusted\s+|provided\s+)?(?:by|through|with|to|under\s+the\s+direction\s+of|in\s+the\s+care\s+of))\s+{PLACE}{STOP}",
    rf"(?i:under\s+the\s+direction\s+of)\s+{PLACE}{STOP}",
    rf"{PLACE}\s+(?i:(?:is|was|are|were)\s+(?:in\s+charge\s+of|handling)\s+(?:the\s+)?arrangements)",
    rf"(?i:in\s+charge\s+of\s+(?:the\s+)?)(?P<place>[A-Z][^\n,;()]*?(?:Mortuary|Funeral\s+Home|Chapel|Chapels|Mortuaries))",
]
FUNERAL_HOME_WORD = re.compile(r"(?i)mortuar|funeral\s+home|funeral\s+chapel|chapels?\b|cremation|crematory|funeral\s+services|memorial\s+park|undertak|funeral\s+directors?")
PLACE_OK = re.compile(r"^(?:the\s+)?[A-Z0-9]")
BAD_PLACE = re.compile(r"(?i)^(?:the\s+)?(?:\d{1,2}(?::\d\d)?\s*(?:[ap]\b|[ap]\.|noon|tonight|o.clock)|\d{1,2}(?::\d\d)?$|\d+\s*(?:tonight|this)|(?:his|her|their|a|an)\s|home\b|a\s+later|later|this\s+time|that\s+time|\d{1,2}(?::\d\d)?\s*(?:a\.?m|p\.?m|noon))")

# A place name may continue past a comma ("Quintin, Pangasinan, Philippines",
# "Eternal Valley Memorial Park, Newhall, CA 91321"). A comma segment is kept only
# when it is a short run of capitalized place words (optional "the" and ZIP) that is
# itself followed by a terminator. It is not kept when it starts with a day, month,
# number or title, or when the clause goes on to name an officiant.
SEG_WORD = rf"(?:\b{ABBR}\.|[A-Z]\.(?=[ ]?[A-Z])|[A-Z][\w'’\-]*|(?:of|de|del|la|el|los|las|y|the)(?=[ \t]+[A-Z]))"
COMMA_SEG = re.compile(
    rf",[ \t]*(?P<seg>(?:the[ \t]+)?{SEG_WORD}(?:[ \t]+{SEG_WORD}){{0,4}}(?:[ \t]+\d{{5}})?)"
)
SEG_BAD = re.compile(
    rf"^(?:the\s+)?(?:{DAYS}|{MONTHS}|Father|Fr|Rev|Reverend|Pastor|Rabbi|Msgr|Monsignor|Bishop|Deacon|Elder|"
    rf"Mr|Mrs|Ms|Dr|Chaplain|Cantor|Officiat|Presid|Jr|Sr|Inc|Followed|Following|Reception|Interment|Burial|Visitation|Viewing|"
    rf"Services?|Friends|Family|Donations|In\s+lieu|Please|He|She|They|His|Her|Their|It|There|All|Everyone)\b"
)
OFFICIANT_AFTER = re.compile(r"(?i)^\s*,?\s*(?:\w+\s+){0,2}?(?:officiat|presid)")


def extend_commas(text, end):
    """Extend a place span across comma-separated place-name segments."""
    while True:
        m = COMMA_SEG.match(text, end)
        if not m:
            return end
        seg = m.group("seg")
        if SEG_BAD.match(seg):
            return end
        after = text[m.end():]
        if not re.match(STOP, after):
            return end
        if OFFICIANT_AFTER.match(after):
            return end
        end = m.end()


PRE_SKIP = re.compile(r"(?i)(?:\b(?:following|after)\s+the|\bprehistoric|\breception)\s*$")

def cands(text, patterns, need_home_word=False, pre_skip=False):
    out = []
    for p in patterns:
        for m in re.finditer(p, text):
            if pre_skip and PRE_SKIP.search(text[max(0, m.start() - 40):m.start()]):
                continue
            end = extend_commas(text, m.end("place"))
            s = text[m.start("place"):end].strip()
            s = re.sub(r"[\s.]+$", "", s) if not re.search(rf"\b{ABBR}\.$", s) else s.rstrip()
            if len(s) < 3 or len(s) > 120 or not PLACE_OK.match(s) or BAD_PLACE.match(s):
                continue
            if "\n" in s:
                continue
            if need_home_word and not FUNERAL_HOME_WORD.search(s):
                continue
            out.append(s)
    # dedupe preserving order
    seen = []
    for s in out:
        if s not in seen:
            seen.append(s)
    return seen

def pick(values):
    """One value when unambiguous. Several distinct non-nested values -> ambiguous -> ''."""
    if not values:
        return "", False
    keep = [v for v in values if not any(v != w and v in w for w in values)]
    if len(keep) == 1:
        return keep[0], False
    return "", True

def main():
    data = json.load(open(OBITS, encoding="utf-8"))
    pages = data["pages"]
    counts = Counter(); examples = {k: [] for k in ("funeral_location_raw", "burial_location_raw", "funeral_home_raw")}
    ambiguous = []
    for p in pages:
        t = (p.get("body_text") or "").replace("\xa0", " ")
        spec = {
            "funeral_location_raw": cands(t, FUNERAL_PATTERNS),
            "burial_location_raw": cands(t, BURIAL_PATTERNS, pre_skip=True),
            "funeral_home_raw": cands(t, HOME_PATTERNS, need_home_word=True),
        }
        for field, vals in spec.items():
            v, amb = pick(vals)
            # verbatim check against original body_text
            if v and v not in (p.get("body_text") or ""):
                v = ""  # nbsp-normalized span not verbatim; leave empty
                amb = True
            p[field] = v
            if v:
                counts[field] += 1
                if len(examples[field]) < 5:
                    examples[field].append({"legacy_key": p["legacy_key"], "source_url": p["source_url"], "value": v})
            if amb:
                ambiguous.append({"legacy_key": p["legacy_key"], "source_url": p["source_url"], "field": field, "candidates": vals})
    json.dump(data, open(OBITS, "w", encoding="utf-8"), ensure_ascii=False, indent=2)
    open(OBITS, "a").write("\n")
    report = {
        "page_count": len(pages),
        "counts": {k: counts[k] for k in examples},
        "examples": examples,
        "ambiguous_left_empty": ambiguous,
        "ambiguous_count": len(ambiguous),
        "method": ("Verbatim spans from body_text only, from explicit phrasings: 'services will be held ... at X', "
                   "'interment/burial/inurnment at|in Y', 'was buried/laid to rest at|in Y', 'arrangements by|under the "
                   "direction of Z', 'Z is in charge of arrangements'. funeral_home_raw must contain a funeral-home word "
                   "(Mortuary, Funeral Home, Chapel, Memorial Park, Cremation...). Where more than one distinct value is "
                   "stated for a field, the field is left empty and listed here. Header sponsor lines (a mortuary name printed "
                   "alone under the obituary heading) are not used: the text does not state the relationship."),
    }
    json.dump(report, open(REPORT, "w", encoding="utf-8"), ensure_ascii=False, indent=2)
    open(REPORT, "a").write("\n")
    print(json.dumps(report["counts"]), "ambiguous", len(ambiguous))

if __name__ == "__main__":
    main()
