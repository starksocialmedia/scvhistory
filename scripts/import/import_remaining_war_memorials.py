"""Extract remaining 11 WWII memorials from Jordy. Read-only on the drive."""

from __future__ import annotations

import json
import re
import sys
from html import unescape
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "inventory"))
from common import SITE_ROOT, INVENTORY_DIR, require_drive, drive_ok

EXTRACT_DIR = INVENTORY_DIR / "extracts"
FILES = [
    "ww2_garrywingfield.htm",
    "ww2_jackharland.htm",
    "ww2_jamesredman.htm",
    "ww2_jimbartlett.htm",
    "ww2_johnnycordova.htm",
    "ww2_johnward.htm",
    "ww2_leoncherry.htm",
    "ww2_ozalsmart.htm",
    "ww2_robertcone.htm",
    "ww2_robertfose.htm",
    "ww2_tomross.htm",
]
SCRIPT_RE = re.compile(r"<script[\s\S]*?</script>", re.I)
STYLE_RE = re.compile(r"<style[\s\S]*?</style>", re.I)
TAG_RE = re.compile(r"<[^>]+>")


def strip_chrome(html: str) -> str:
    html = SCRIPT_RE.sub("", html)
    html = STYLE_RE.sub("", html)
    return html.strip()


def text_of(html: str) -> str:
    text = TAG_RE.sub(" ", html)
    text = unescape(text)
    return re.sub(r"\s+", " ", text).strip()


def first_match(pattern: str, html: str) -> str:
    m = re.search(pattern, html, re.I | re.S)
    return unescape(m.group(1)).strip() if m else ""


def extract_one(name: str) -> dict:
    rel = f"warmemorial/{name}"
    path = SITE_ROOT / rel
    raw = path.read_bytes().decode("windows-1252", errors="replace")
    stripped = strip_chrome(raw)
    title = first_match(r'class="altheadline">\s*([^<]+)', stripped)
    if not title:
        title = first_match(r"<title>([^<]+)</title>", stripped)
        parts = [p.strip() for p in title.split("|")]
        title = parts[-1] if parts else title
        title = re.split(r",", title, maxsplit=1)[0].strip()
    branch = first_match(r'class="byline">\s*([^<]+)', stripped)
    death = first_match(r'class="dateline">\s*\|?\s*d\.\s*([^<]+)', stripped)
    if not death:
        death = first_match(r"\bd\.\s*(\d{1,2}-\d{1,2}-\d{2,4})", stripped)
    home = first_match(r"Home of Record:</B>\s*([^<]+)", stripped)
    rank = first_match(r"Rank:\s*([^<]+)", stripped)
    unit = first_match(r"Unit:\s*([^<]+)", stripped)
    text = text_of(stripped)
    if title and title in text:
        text = text[text.find(title) :]
    body = text[:500]
    stem = Path(name).stem
    slug = stem.replace("_", "-").lower()
    return {
        "slug": slug,
        "title": title,
        "body": body,
        "wmBranch": branch,
        "wmRank": rank,
        "wmUnit": unit,
        "wmConflict": "World War II",
        "deathDate": death,
        "wmHomeOfRecord": home,
        "legacyKey": stem,
        "legacyUrl": f"/{rel}",
        "sourcePath": f"scvhistory.com/{rel}",
        "legacyHtml": stripped,
    }


def main() -> int:
    require_drive()
    wm = SITE_ROOT / "warmemorial"
    if not wm.is_dir():
        print("STOP: warmemorial folder missing", file=sys.stderr)
        return 3
    EXTRACT_DIR.mkdir(parents=True, exist_ok=True)
    records = []
    for name in FILES:
        if not drive_ok():
            print("STOP: drive gone", file=sys.stderr)
            return 3
        rec = extract_one(name)
        records.append(rec)
        print(rec["slug"], "|", rec["title"], "|", rec["wmBranch"], "|", rec["deathDate"])
    out = EXTRACT_DIR / "war-memorial-remaining-11.json"
    out.write_text(json.dumps({"count": len(records), "persons": records}, indent=2) + "\n")
    print(f"wrote {len(records)} to {out}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
