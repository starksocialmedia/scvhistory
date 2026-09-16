"""Extract up to 25 Person records from Jordy. Read-only on the drive."""

from __future__ import annotations

import csv
import json
import re
import sys
from html import unescape
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "inventory"))
from common import SITE_ROOT, INVENTORY_DIR, RAW_DIR, drive_ok, require_drive

EXTRACT_DIR = INVENTORY_DIR / "extracts"
CSV_PATH = RAW_DIR / "page-types.csv"
CAP = 25
BODY_CHARS = 500

SKIP_HEADLINE = re.compile(
    r"(token|advertisement|family:|qb |playoff|prescription|auctioneer|"
    r"billiard|&| and |,)",
    re.I,
)
NAME_OK = re.compile(r"^[A-Z][A-Za-z'.\-]+(?: [A-Z][A-Za-z'.\-]+){1,3}$")
DEATH_RE = re.compile(
    r"\b(?:d\.|MIA|KIA|died)\s*(\d{1,2}-\d{1,2}-\d{2,4})",
    re.I,
)
YEARS_RE = re.compile(r"\b(1[7-9]\d{2}|20\d{2})-(\d{4})\b")
SCRIPT_RE = re.compile(r"<script[\s\S]*?</script>", re.I)
STYLE_RE = re.compile(r"<style[\s\S]*?</style>", re.I)
NOSCRIPT_RE = re.compile(r"<noscript[\s\S]*?</noscript>", re.I)
TAG_RE = re.compile(r"<[^>]+>")


def slugify(name: str) -> str:
    s = name.lower()
    s = re.sub(r"['’]", "", s)
    s = re.sub(r"[^a-z0-9]+", "-", s)
    return s.strip("-")


def load_existing(path: Path) -> set[str]:
    if not path.is_file():
        return set()
    return {line.strip() for line in path.read_text().splitlines() if line.strip()}


def load_csv() -> tuple[list[dict], list[dict]]:
    wm: list[dict] = []
    people: list[dict] = []
    with CSV_PATH.open(newline="", errors="replace") as f:
        for row in csv.DictReader(f):
            if row["type"] == "war_memorial_profile":
                wm.append(row)
            elif row["type"] == "object_page" and row.get("category", "").strip() == "People":
                people.append(row)
    wm.sort(key=lambda r: r["path"])
    people.sort(key=lambda r: r["path"])
    return wm, people


def title_parts(title: str) -> list[str]:
    return [p.strip() for p in title.split("|") if p.strip()]


def parse_war_memorial(row: dict) -> dict | None:
    parts = title_parts(row["title"])
    if not parts:
        return None
    category = parts[1] if len(parts) >= 2 else "War Memorial"
    head = parts[-1]
    death = ""
    m = DEATH_RE.search(head)
    if m:
        death = m.group(1)
    birth = ""
    y = YEARS_RE.search(head)
    if y:
        birth = y.group(1)
        if not death:
            death = y.group(2)
    name = re.split(r",", head, maxsplit=1)[0].strip(" .")
    name = re.sub(r"\s+", " ", name)
    if len(name.split()) < 2:
        return None
    return {
        "fullName": name,
        "legacyCategory": category,
        "birthDate": birth,
        "deathDate": death,
        "occupation": "",
    }


def parse_people_headline(row: dict) -> dict | None:
    parts = title_parts(row["title"])
    if len(parts) < 3:
        return None
    head = parts[-1]
    if SKIP_HEADLINE.search(head):
        return None
    name = re.split(r"[,:(]", head, maxsplit=1)[0].strip(" .")
    name = re.sub(r"\s+", " ", name)
    if not NAME_OK.match(name):
        return None
    return {
        "fullName": name,
        "legacyCategory": "People",
        "birthDate": "",
        "deathDate": "",
        "occupation": "",
    }


def strip_chrome(html: str) -> str:
    html = SCRIPT_RE.sub("", html)
    html = STYLE_RE.sub("", html)
    html = NOSCRIPT_RE.sub("", html)
    return html.strip()


def text_extract(html: str, name: str = "") -> str:
    text = TAG_RE.sub(" ", html)
    text = unescape(text)
    text = re.sub(r"\s+", " ", text).strip()
    if name:
        idx = text.find(name)
        if idx >= 0:
            text = text[idx:]
    return text[:BODY_CHARS]


def read_page(rel: str, name: str) -> tuple[str, str]:
    abs_path = SITE_ROOT / rel
    raw = abs_path.read_bytes()
    html = raw.decode("windows-1252", errors="replace")
    stripped = strip_chrome(html)
    return stripped, text_extract(stripped, name)


def record_for(row: dict, parsed: dict) -> dict:
    rel = row["path"]
    name = parsed["fullName"]
    stripped, body = read_page(rel, name)
    key = (row.get("item_id") or "").strip() or Path(rel).stem
    url_path = "/" + rel.lstrip("/")
    return {
        "slug": slugify(name),
        "title": name,
        "fullName": name,
        "birthDate": parsed.get("birthDate") or "",
        "deathDate": parsed.get("deathDate") or "",
        "birthplace": "",
        "burialPlace": "",
        "occupation": parsed.get("occupation") or "",
        "body": body,
        "personLegacyUrl": url_path,
        "legacyUrl": url_path,
        "legacyKey": key,
        "sourcePath": f"scvhistory.com/{rel}",
        "legacyHtml": stripped,
        "legacyCategory": parsed.get("legacyCategory") or "",
        "sourceType": row["type"],
        "sourcePathRel": rel,
    }


def main() -> int:
    require_drive()
    if not CSV_PATH.is_file():
        print(f"STOP: missing {CSV_PATH}", file=sys.stderr)
        return 2
    EXTRACT_DIR.mkdir(parents=True, exist_ok=True)
    existing = load_existing(EXTRACT_DIR / "existing-person-slugs.txt")
    wm_rows, people_rows = load_csv()
    picked: list[dict] = []
    seen: set[str] = set()
    skipped: list[dict] = []

    def consider(row: dict, parsed: dict | None, reason_if_none: str) -> None:
        if len(picked) >= CAP:
            return
        if parsed is None:
            skipped.append({"path": row["path"], "reason": reason_if_none})
            return
        slug = slugify(parsed["fullName"])
        if slug in existing or slug in seen:
            skipped.append({"path": row["path"], "slug": slug, "reason": "slug-exists"})
            return
        if not drive_ok():
            raise RuntimeError("dismount")
        rec = record_for(row, parsed)
        seen.add(rec["slug"])
        picked.append(rec)

    try:
        for row in wm_rows:
            if len(picked) >= CAP:
                break
            consider(row, parse_war_memorial(row), "unparsed-war-memorial")
        for row in people_rows:
            if len(picked) >= CAP:
                break
            consider(row, parse_people_headline(row), "skipped-people-headline")
    except RuntimeError as e:
        if str(e) == "dismount":
            print("STOP: drive gone during extract", file=sys.stderr)
            return 3
        raise

    out = EXTRACT_DIR / "persons-pilot-25.json"
    manifest = EXTRACT_DIR / "persons-pilot-25.manifest.json"
    payload = {
        "cap": CAP,
        "count": len(picked),
        "persons": picked,
    }
    out.write_text(json.dumps(payload, indent=2) + "\n")
    manifest.write_text(
        json.dumps(
            {
                "picked": [
                    {"slug": r["slug"], "path": r["sourcePathRel"], "type": r["sourceType"]}
                    for r in picked
                ],
                "skipped": skipped[:80],
                "existingSlugCount": len(existing),
            },
            indent=2,
        )
        + "\n"
    )
    print(f"wrote {len(picked)} persons to {out}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
