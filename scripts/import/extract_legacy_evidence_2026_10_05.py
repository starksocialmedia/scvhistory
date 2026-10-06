"""Reads the legacy pages behind three silent-faults fixes and writes what they say to
inventory/review/legacy-evidence-2026-10-05.json, because DDEV cannot see the mirror.

Reads only the pages named below, never the whole tree. Read-only on the mirror.

1. The 13 Hart Park images (fix_hart_park_captions_2026_10_05.php). For each page: its <title>,
   the line of the main image, the first caption element (class "caption" or a figcaption) that
   sits between the main image and the next image (the image's own caption, if the page gives
   one), the first paragraph of text under the image (for reference: it is the page's body text,
   which the photograph record already holds), and the line of the "Biography by Friends of Hart
   Park" element the crawl took.
2. The place pages (fix_place_legacy_links_2026_10_05.php): the <title> of the current and the
   proposed page for each of the seven places, and the breadcrumb Leon put on the current page.

Run (MacBook, with Reggie mounted):
python3 scripts/import/extract_legacy_evidence_2026_10_05.py
"""

from __future__ import annotations

import html
import json
import re
import sys
from pathlib import Path

MIRROR = Path("/Volumes/Reggie/SCVHistory/scvhistory.com/scvhistory")
REPO = Path(__file__).resolve().parents[2]
OUT = REPO / "inventory" / "review" / "legacy-evidence-2026-10-05.json"

HART = {  # image code -> legacy page key
    "lw3654": "lw3654", "lw3542": "lw3542", "lw3528": "lw3528", "lw3512": "lw3512",
    "lw3475": "lw3475", "lw3428": "lw3428", "lw3023": "lw3023", "lw2938": "lw2938",
    "lw2927": "lw2927", "lw2748": "lw2748", "lw2584": "lw2584", "lw2467": "lw2467",
    "lw3445c": "lw3445",
}
PLACE_PAGES = [
    "lw3903", "lw3795", "lw3790", "lw3789", "lw3730", "lw3698", "lw3271",
    "camulos", "ridge", "heritage", "aguadulce", "valencia", "saugus", "speedwaychronology",
]


def text(fragment: str) -> str:
    s = re.sub(r"(?is)<(script|style).*?</\1>", " ", fragment)
    s = re.sub(r"<[^>]+>", " ", s)
    return " ".join(html.unescape(s).split())


def read(key: str) -> str:
    return (MIRROR / f"{key}.htm").read_text(encoding="latin-1")


def title_of(page: str) -> str:
    m = re.search(r"(?is)<title>(.*?)</title>", page)
    return text(m.group(1)) if m else ""


def line_of(page: str, pos: int) -> int:
    return page.count("\n", 0, pos) + 1


def hart(code: str, key: str) -> dict:
    page = read(key)
    start = page.find("XWP-BEGIN-CONTENT")
    img = re.compile(r'<img[^>]*src="[^"]*gif/' + re.escape(code) + r'\.jpg"[^>]*>', re.I)
    m = img.search(page, max(start, 0))
    if not m:
        return {"page": f"scvhistory/{key}.htm", "title": title_of(page), "error": "main image not found"}
    nxt = re.compile(r"<img\b", re.I).search(page, m.end())
    # The image's own block ends at the next image, a rule, or the start of the page's body text or a
    # section heading. A caption element further down belongs to something else: on these pages the
    # one the crawl took heads the Friends of Hart Park biography, after a rule and a subhed.
    end = re.compile(r'<img\b|<hr\b|class="bodyserif"|class="subhed', re.I).search(page, m.end())
    window = page[m.end(): end.start() if end else len(page)]
    cap = re.search(r'(?is)<(div|span|p|font|figcaption)[^>]*class="[^"]*caption[^"]*"[^>]*>(.*?)</\1>|<figcaption[^>]*>(.*?)</figcaption>', window)
    rest = page[m.end(): nxt.start() if nxt else len(page)]
    body = re.search(r'(?is)<div class="bodyserif">\s*(?:<p>)?\s*(.*?)(?:<p>|<div|</div>)', rest)
    bio = re.search(r'<div class="caption">\s*Biography by Friends of Hart Park\s*</div>', page)
    return {
        "page": f"scvhistory/{key}.htm",
        "title": title_of(page),
        "imageLine": line_of(page, m.start()),
        "imageTag": " ".join(m.group(0).split()),
        "captionUnderImage": text(cap.group(2) or cap.group(3) or "") if cap else "",
        "captionLine": line_of(page, m.end() + cap.start()) if cap else None,
        "firstParagraphUnderImage": text(body.group(1)) if body else "",
        "firstParagraphLine": line_of(page, m.end() + body.start(1)) if body else None,
        "bylineLine": line_of(page, bio.start()) if bio else None,
        "bylineAfterAnotherImage": bool(bio and nxt and bio.start() > nxt.start()),
    }


def place_page(key: str) -> dict:
    page = read(key)
    start = max(page.find("XWP-BEGIN-CONTENT"), 0)
    crumbs = []
    for h, x in re.findall(r'(?is)<a\s+href\s*=\s*"([^"]+)"[^>]*>(.*?)</a>', page[start:start + 3000]):
        if text(x).startswith(">"):
            crumbs.append({"href": h, "label": text(x)})
    return {"page": f"scvhistory/{key}.htm", "title": title_of(page), "crumbs": crumbs}


def main() -> int:
    if not MIRROR.is_dir():
        print(f"mirror not mounted: {MIRROR}", file=sys.stderr)
        return 1
    out = {
        "generated_by": "scripts/import/extract_legacy_evidence_2026_10_05.py",
        "mirror": str(MIRROR),
        "hart": {code: hart(code, key) for code, key in HART.items()},
        "places": {key: place_page(key) for key in PLACE_PAGES},
    }
    OUT.write_text(json.dumps(out, indent=1, ensure_ascii=False) + "\n", encoding="utf-8")
    print(f"wrote {OUT}")
    for code, r in out["hart"].items():
        print(code, r.get("imageLine"), repr(r.get("captionUnderImage")), r.get("bylineLine"), r.get("bylineAfterAnotherImage"))
    for key, r in out["places"].items():
        print(key, "|", r["title"], "|", r["crumbs"])
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
