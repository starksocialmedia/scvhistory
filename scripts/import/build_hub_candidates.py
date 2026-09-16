"""Build places-candidates.json and people-candidates.json. Read-only on Jordy."""

from __future__ import annotations

import csv
import json
import re
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "inventory"))
from common import SITE_ROOT, REPO_ROOT, RAW_DIR, require_drive

CSV_PATH = RAW_DIR / "page-types.csv"
GRAVE = REPO_ROOT / "grave-audit-export.md"
CAP_PEOPLE = 50

INDEX_TO_PLACE = {
    "acton.htm": ("acton", "Acton", "Place-named History in Pictures index"),
    "asistencia.htm": ("estancia", "Estancia", "Index titled Estancia"),
    "bealescut.htm": ("beales-cut", "Beale's Cut", "Place-named index"),
    "bouquet.htm": ("bouquet-canyon", "Bouquet Canyon", "Place-named index"),
    "carey.htm": ("harry-carey-ranch", "Harry Carey Ranch", "Place-named index"),
    "castaic.htm": ("castaic", "Castaic", "Place-named index"),
    "haskell.htm": ("haskell-canyon", "Haskell Canyon", "Place-named index"),
    "hasley.htm": ("hasley-canyon", "Hasley Canyon", "Place-named index"),
    "melody.htm": ("melody-ranch", "Melody Ranch", "Place-named index"),
    "mojave.htm": ("mojave-desert", "Mojave Desert", "Place-named index"),
    "newhalldt.htm": ("newhall", "Newhall", "Downtown Newhall index; 301 to Newhall Place"),
    "newhall4th.htm": ("newhall", "Newhall", "Newhall 4th of July index; 301 to Newhall Place"),
    "newhallsch.htm": ("newhall", "Newhall", "Newhall Schools index; 301 to Newhall Place"),
    "pico.htm": ("pico-canyon", "Pico Canyon", "Index titled Pico Canyon / Mentryville; Mentryville is a separate Place"),
    "piru.htm": ("piru", "Piru", "Place-named index"),
    "potrero.htm": ("potrero-canyon", "Potrero Canyon", "Place-named index"),
    "rancho.htm": ("rancho-san-francisco", "Rancho San Francisco", "Place-named index"),
    "saugus.htm": ("saugus", "Saugus", "Place-named index"),
    "soledad.htm": ("soledad-canyon", "Soledad Canyon", "Place-named index"),
    "towsley.htm": ("towsley-canyon", "Towsley Canyon", "Place-named index"),
    "valencia.htm": ("valencia", "Valencia", "Place-named index"),
    "valverde.htm": ("val-verde", "Val Verde", "Place-named index"),
}

CAT_TO_PLACE = {
    "Newhall": ("newhall", "Newhall"),
    "Newhall Schools": ("newhall", "Newhall"),
    "Newhall School": ("newhall", "Newhall"),
    "Newhall 4th of July": ("newhall", "Newhall"),
    "Newhall July 4th": ("newhall", "Newhall"),
    "Saugus": ("saugus", "Saugus"),
    "Acton": ("acton", "Acton"),
    "Pico Canyon": ("pico-canyon", "Pico Canyon"),
    "Pico-Wiley Canyons": ("pico-canyon", "Pico Canyon"),
    "Valencia": ("valencia", "Valencia"),
    "Canyon Country": ("canyon-country", "Canyon Country"),
    "Ridge Route": ("ridge-route", "Ridge Route"),
    "Rancho Camulos": ("rancho-camulos", "Rancho Camulos"),
    "Melody Ranch": ("melody-ranch", "Melody Ranch"),
    "Mojave Desert": ("mojave-desert", "Mojave Desert"),
    "San Francisquito Canyon": ("san-francisquito-canyon", "San Francisquito Canyon"),
    "Soledad Canyon": ("soledad-canyon", "Soledad Canyon"),
    "Castaic": ("castaic", "Castaic"),
    "Heritage Junction": ("heritage-junction", "Heritage Junction"),
    "Agua Dulce": ("agua-dulce", "Agua Dulce"),
    "Placerita Canyon": ("placerita-canyon", "Placerita Canyon"),
    "Saugus Speedway": ("saugus-speedway", "Saugus Speedway"),
    "Mentryville": ("mentryville", "Mentryville"),
    "Lang": ("lang", "Lang"),
    "Magic Mountain": ("magic-mountain", "Magic Mountain"),
    "Piru": ("piru", "Piru"),
    "Vasquez Rocks": ("vasquez-rocks", "Vasquez Rocks"),
    "Lake Hughes": ("lake-hughes", "Lake Hughes"),
    "City of Santa Clarita": ("santa-clarita", "Santa Clarita"),
    "Harry Carey Ranch": ("harry-carey-ranch", "Harry Carey Ranch"),
    "Lebec": ("lebec", "Lebec"),
    "Bouquet Canyon": ("bouquet-canyon", "Bouquet Canyon"),
    "Towsley Canyon": ("towsley-canyon", "Towsley Canyon"),
    "Tejon Ranch": ("tejon-ranch", "Tejon Ranch"),
    "Tejon": ("tejon", "Tejon"),
    "Fort Tejon": ("fort-tejon", "Fort Tejon"),
    "Sleepy Valley": ("sleepy-valley", "Sleepy Valley"),
    "Hasley Canyon": ("hasley-canyon", "Hasley Canyon"),
    "Rancho San Francisco": ("rancho-san-francisco", "Rancho San Francisco"),
    "Beale's Cut": ("beales-cut", "Beale's Cut"),
    "Val Verde": ("val-verde", "Val Verde"),
}

SKIP_HEADLINE = re.compile(
    r"(token|advertisement|family:|qb |playoff|prescription|auctioneer|billiard)",
    re.I,
)
NAME_CHUNK = re.compile(r"^[A-Z][A-Za-z'.\-]+(?: [A-Z][A-Za-z'.\-]+){1,3}$")
NAME_STOP = {
    "president",
    "england",
    "sons",
    "family",
    "company",
    "co",
    "land",
    "water",
    "investor",
    "promoter",
    "owner",
    "newhall",
    "saugus",
    "acton",
    "california",
    "portrait",
    "biography",
    "railroad",
    "original",
    "kids",
    "prize",
    "horse",
    "historian",
    "prohibitionist",
    "receipt",
    "press",
    "photo",
}
YEAR_RE = re.compile(r"\b((?:1[7-9]|20)\d{2})\b")
DEATH_RE = re.compile(r"\b(?:d\.|died|death)\s*([A-Za-z0-9 ,./-]+)", re.I)
BIRTH_RE = re.compile(r"\b(?:b\.|born)\s*([A-Za-z0-9 ,./-]+)", re.I)


def slugify(name: str) -> str:
    s = name.lower()
    s = re.sub(r"['’]", "", s)
    s = re.sub(r"[^a-z0-9]+", "-", s)
    return s.strip("-")


def existing_person_slugs() -> tuple[set[str], list[str]]:
    slugs = set()
    names = []
    if not GRAVE.is_file():
        return slugs, names
    for line in GRAVE.read_text().splitlines():
        if not line.startswith("|"):
            continue
        cols = [c.strip() for c in line.strip("|").split("|")]
        if len(cols) < 3 or cols[0] in {"slug", "---"}:
            continue
        slugs.add(cols[0])
        slugs.add(slugify(cols[1]))
        slugs.add(slugify(cols[2]))
        if cols[2]:
            names.append(cols[2].lower())
        if cols[1]:
            names.append(cols[1].lower())
    return slugs, names


def load_rows() -> list[dict]:
    with CSV_PATH.open(newline="", errors="replace") as f:
        return list(csv.DictReader(f))


def build_places(rows: list[dict], existing_place: set[str]) -> list[dict]:
    by_slug: dict[str, dict] = {}

    def add(slug: str, title: str, legacy_url: str, source_path: str, cat: str, notes: str) -> None:
        if slug in existing_place:
            return
        rec = by_slug.get(slug)
        if rec is None:
            by_slug[slug] = {
                "slug": slug,
                "title": title,
                "legacyUrl": legacy_url,
                "sourcePath": source_path,
                "legacyCategory": cat,
                "notes": notes,
            }
            return
        extra = []
        if legacy_url and legacy_url not in rec["legacyUrl"]:
            extra.append("also " + legacy_url)
        if notes and notes not in rec["notes"]:
            extra.append(notes)
        if extra:
            rec["notes"] = (rec["notes"] + "; " if rec["notes"] else "") + "; ".join(extra)

    for name, (slug, title, notes) in INDEX_TO_PLACE.items():
        rel = f"scvhistory/{name}"
        add(slug, title, "/" + rel, f"scvhistory.com/{rel}", title, notes)

    for row in rows:
        if row["type"] != "object_page":
            continue
        cat = (row.get("category") or "").strip()
        mapped = CAT_TO_PLACE.get(cat)
        if not mapped:
            continue
        slug, title = mapped
        rel = row["path"]
        add(
            slug,
            title,
            "/" + rel if not by_slug.get(slug) else by_slug[slug]["legacyUrl"],
            f"scvhistory.com/{rel}" if slug not in by_slug else by_slug[slug]["sourcePath"],
            cat,
            "Object-page title category maps to this Place slug",
        )
    return [by_slug[k] for k in sorted(by_slug)]


def parse_people_names(headline: str) -> list[str]:
    if SKIP_HEADLINE.search(headline):
        return []
    head = re.split(r"[:]", headline, maxsplit=1)[0]
    head = re.split(r",", head, maxsplit=1)[0]
    head = re.split(r"\s+[-–—]\s+", head, maxsplit=1)[0]
    head = re.sub(r"\([^)]*\)", " ", head)
    head = re.sub(r"\s+", " ", head).strip(" .")
    chunks = re.split(r"\s*(?:&| and )\s*", head)
    names = []
    for chunk in chunks:
        chunk = chunk.strip(" .")
        if not chunk or not NAME_CHUNK.match(chunk):
            continue
        words = chunk.lower().replace(".", "").split()
        if any(w in NAME_STOP for w in words):
            continue
        names.append(chunk)
    return names


def dates_from_title(headline: str) -> tuple[str, str]:
    birth = ""
    death = ""
    bm = BIRTH_RE.search(headline)
    if bm:
        birth = bm.group(1).strip(" .,")
    dm = DEATH_RE.search(headline)
    if dm:
        death = dm.group(1).strip(" .,")
    years = YEAR_RE.findall(headline)
    if len(years) == 1 and not birth and not death:
        pass
    if len(years) >= 2 and not birth and not death:
        birth, death = years[0], years[1]
    return birth, death


def build_people(rows: list[dict], skip: set[str], existing_names: list[str]) -> list[dict]:
    picked: list[dict] = []
    seen: set[str] = set()
    people_rows = [
        r
        for r in rows
        if r["type"] == "object_page" and (r.get("category") or "").strip() == "People"
    ]
    people_rows.sort(key=lambda r: r["path"])
    for row in people_rows:
        if len(picked) >= CAP_PEOPLE:
            break
        parts = [p.strip() for p in row["title"].split("|")]
        headline = parts[-1] if parts else row["title"]
        low = headline.lower()
        if any(n and n in low for n in existing_names):
            continue
        names = parse_people_names(headline)
        if not names:
            continue
        birth, death = dates_from_title(headline)
        usable = []
        for name in names:
            slug = slugify(name)
            if slug in skip or slug in seen:
                continue
            usable.append((slug, name))
        if not usable:
            continue
        rel = row["path"]
        rec = {
            "legacyKey": (row.get("item_id") or "").strip() or Path(rel).stem,
            "headline": headline,
            "legacyUrl": "/" + rel,
            "sourcePath": f"scvhistory.com/{rel}",
            "names": [n for _, n in usable],
            "slugs": [s for s, _ in usable],
            "birthDate": birth,
            "deathDate": death,
        }
        for slug, _ in usable:
            seen.add(slug)
        picked.append(rec)
    return picked


def main() -> int:
    require_drive()
    if not CSV_PATH.is_file():
        print(f"STOP: missing {CSV_PATH}", file=sys.stderr)
        return 2
    rows = load_rows()
    places = build_places(rows, set())
    skip_slugs, existing_names = existing_person_slugs()
    people = build_people(rows, skip_slugs, existing_names)
    (REPO_ROOT / "places-candidates.json").write_text(
        json.dumps({"count": len(places), "candidates": places}, indent=2) + "\n"
    )
    (REPO_ROOT / "people-candidates.json").write_text(
        json.dumps({"count": len(people), "cap": CAP_PEOPLE, "candidates": people}, indent=2)
        + "\n"
    )
    print(f"places={len(places)} people={len(people)}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
