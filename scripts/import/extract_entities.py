"""Build entities.json and a draft canonical_entities.json. Read-only on Jordy."""

from __future__ import annotations

import csv
import json
import re
import sys
from collections import defaultdict
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "inventory"))
from common import REPO_ROOT, RAW_DIR, INVENTORY_DIR, SITE_ROOT, require_drive

CRAFT = INVENTORY_DIR / "extracts" / "craft-entities.json"
CSV_PATH = RAW_DIR / "page-types.csv"
OUT_ENTITIES = INVENTORY_DIR / "entities.json"
OUT_CANON = INVENTORY_DIR / "canonical_entities.json"
MIN_RECUR = 3

SKIP_TYPES = {
    "apache_listing",
    "flipbook_html",
    "yearbook_package_html",
    "unclassified",
    "unreadable",
    "orig_copy",
    "home",
    "obituary_index",
    "war_memorial_index",
}

NOT_PLACE_CATS = {
    "people",
    "film-arts",
    "film/arts",
    "tataviam culture",
    "st. francis dam disaster",
    "st. francis dam",
    "powerhouse fire",
    "william s. hart",
    "law enforcement",
    "plane crashes",
    "aircraft down",
    "1994 earthquake",
    "1994 northridge earthquake",
    "1971 earthquake",
}

SKIP_HEAD = re.compile(
    r"(token|advertisement|family:|qb |playoff|prescription|auctioneer|billiard|yearbook)",
    re.I,
)
NAME_CHUNK = re.compile(r"^[A-Z][A-Za-z'.\-]+(?: [A-Z][A-Za-z'.\-]+){1,3}$")
NAME_STOP = {
    "president", "england", "sons", "family", "company", "portrait",
    "biography", "railroad", "original", "kids", "prize", "horse",
    "historian", "receipt", "press", "photo", "collection",
}


def slugify(name: str) -> str:
    s = name.lower()
    s = re.sub(r"['’]", "", s)
    s = re.sub(r"[^a-z0-9]+", "-", s)
    return s.strip("-")


def parse_people_names(headline: str) -> list[str]:
    if SKIP_HEAD.search(headline):
        return []
    head = re.split(r"[:]", headline, maxsplit=1)[0]
    head = re.split(r",", head, maxsplit=1)[0]
    head = re.sub(r"\([^)]*\)", " ", head)
    head = re.sub(r"\s+", " ", head).strip(" .")
    names = []
    for chunk in re.split(r"\s*(?:&| and )\s*", head):
        chunk = chunk.strip(" .")
        if not chunk or not NAME_CHUNK.match(chunk):
            continue
        words = chunk.lower().replace(".", "").split()
        if any(w in NAME_STOP for w in words):
            continue
        names.append(chunk)
    return names


def title_headline(title: str) -> str:
    parts = [p.strip() for p in title.split("|") if p.strip()]
    return parts[-1] if parts else title


def load_craft() -> dict:
    data = json.loads(CRAFT.read_text())
    by_key = {}
    for typ, key in (
        ("person", "persons"),
        ("place", "places"),
        ("organization", "organizations"),
        ("warMemorial", "warMemorials"),
    ):
        for row in data.get(key, []):
            slug = row["slug"]
            by_key[(slug, typ)] = {
                "slug": slug,
                "name": row["title"],
                "type": typ,
                "status": "canonical_sample",
                "inCraft": True,
                "legacyUrl": row.get("legacyUrl") or "",
                "sourcePaths": [],
                "mentionCount": 0,
            }
    return by_key


def add_mention(mentions: list, slug: str, name: str, typ: str, path: str, key: str) -> None:
    mentions.append(
        {
            "name": name,
            "slug": slug,
            "typeGuess": typ,
            "sourcePath": f"scvhistory.com/{path}",
            "legacyKey": key,
        }
    )


PLACE_JUNK = re.compile(
    r"(collection|school|song|coming soon|maps|business|exonumia|club|"
    r"council|museum|park|cafe|products|interpretive|july|4th)",
    re.I,
)
PLACE_TOPIC = {
    "film",
    "nature",
    "roads",
    "obituaries",
    "unknown",
    "prohibition",
    "organizations",
    "transportation",
    "visual-art",
    "general-interest",
    "early-california",
    "native-cultures",
    "phone-books",
    "patents-inventions",
    "scv-healthcare",
    "walk-of-western-stars",
    "gene-autry",
    "calarts",
    "college-of-the-canyons",
    "modern-tataviam-artwork",
    "1929-saugus-train-robbery",
    "st-francis-dam-aftermath",
    "el-nino-1997-98",
    "newhall-incident",
    "1962-melody-ranch-fire",
    "u-s-camel-corps",
    "l-a-county-fire",
    "southern-pacific-company",
    "the-ridge-route-rambler",
    "hart-high",
    "scv-chamber",
    "newhall-municipal-court",
    "bonelli-stadium",
    "saugus-rodeo",
}
PLACE_GEO = re.compile(
    r"(canyon|lake|ranch|pass|junction|rocks|aqueduct|ridge|cut|dam|springs)$",
    re.I,
)


def main() -> int:
    require_drive()
    if not SITE_ROOT.is_dir():
        print("STOP: Jordy site missing", file=sys.stderr)
        return 3
    if not CSV_PATH.is_file() or not CRAFT.is_file():
        print("STOP: need page-types.csv and craft-entities.json", file=sys.stderr)
        return 2

    craft = load_craft()
    mentions: list[dict] = []
    counts: dict[tuple[str, str], set[str]] = defaultdict(set)
    display: dict[tuple[str, str], str] = {(k[0], k[1]): v["name"] for k, v in craft.items()}

    with CSV_PATH.open(newline="", errors="replace") as f:
        for row in csv.DictReader(f):
            typ = row["type"]
            if typ in SKIP_TYPES:
                continue
            path = row["path"]
            key = (row.get("item_id") or "").strip() or Path(path).stem
            cat = (row.get("category") or "").strip()
            headline = title_headline(row.get("title") or "")

            if typ == "war_memorial_profile":
                name = re.split(r",", headline, maxsplit=1)[0].strip()
                slug = slugify(Path(path).stem.replace("_", "-"))
                add_mention(mentions, slug, name, "warMemorial", path, key)
                counts[(slug, "warMemorial")].add(path)
                display.setdefault((slug, "warMemorial"), name)
                continue

            if cat and cat.lower() not in NOT_PLACE_CATS:
                pslug = slugify(cat)
                add_mention(mentions, pslug, cat, "place", path, key)
                counts[(pslug, "place")].add(path)
                display.setdefault((pslug, "place"), cat)

            if typ == "object_page" and cat.lower() == "people":
                for name in parse_people_names(headline):
                    slug = slugify(name)
                    add_mention(mentions, slug, name, "person", path, key)
                    counts[(slug, "person")].add(path)
                    display.setdefault((slug, "person"), name)

            if typ == "newspaper_signal":
                series = path.split("/")
                if len(series) >= 3 and series[1] == "signal":
                    slug = slugify(series[2])
                    add_mention(mentions, slug, series[2].title(), "collection", path, key)
                    counts[(slug, "collection")].add(path)
                    display.setdefault((slug, "collection"), series[2].title())

    OUT_ENTITIES.parent.mkdir(parents=True, exist_ok=True)
    OUT_ENTITIES.write_text(
        json.dumps(
            {
                "source": "Jordy editorial titles plus local Craft names",
                "mentionCount": len(mentions),
                "mentions": mentions,
            },
            indent=2,
        )
        + "\n"
    )

    canonical = []
    seen = set()
    for key, rec in sorted(craft.items(), key=lambda x: (x[1]["type"], x[1]["slug"])):
        n = len(counts.get(key, set()))
        rec = dict(rec)
        rec["mentionCount"] = n
        rec["sourcePaths"] = sorted(counts.get(key, set()))[:20]
        canonical.append(rec)
        seen.add(key)

    candidates = []
    mention_only = 0
    for (slug, typ), paths in sorted(counts.items(), key=lambda x: (-len(x[1]), x[0][0])):
        if (slug, typ) in seen:
            continue
        if typ in {"warMemorial", "collection"}:
            mention_only += 1
            continue
        if typ == "place":
            label = display.get((slug, typ), slug)
            if (
                slug in PLACE_TOPIC
                or slug in {"tataviam-culture", "tataviam-indians"}
                or PLACE_JUNK.search(label)
                or len(label.split()) > 4
                or not (PLACE_GEO.search(slug) or len(label.split()) <= 2)
            ):
                mention_only += 1
                continue
        n = len(paths)
        if n >= MIN_RECUR and typ in {"person", "place", "organization"}:
            candidates.append(
                {
                    "slug": slug,
                    "name": display.get((slug, typ), slug),
                    "type": typ,
                    "status": "candidate_review",
                    "inCraft": False,
                    "mentionCount": n,
                    "sourcePaths": sorted(paths)[:20],
                    "note": "Appears on 3+ editorial pages. Recurring-role rule still needs Nathan/Leon.",
                }
            )
            seen.add((slug, typ))
        else:
            mention_only += 1

    payload = {
        "rule": "PHILOSOPHY.md 2.3: a full record only when the entity has a meaningful, recurring role in SCV history. Passing mentions stay in body.",
        "minDistinctPagesForCandidate": MIN_RECUR,
        "canonical_sample": canonical,
        "candidate_review": candidates,
        "mentionOnlyDistinct": mention_only,
        "counts": {
            "canonical_sample": len(canonical),
            "candidate_review": len(candidates),
            "mentions": len(mentions),
        },
    }
    OUT_CANON.write_text(json.dumps(payload, indent=2) + "\n")
    print(
        f"mentions={len(mentions)} canonical_sample={len(canonical)} "
        f"candidate_review={len(candidates)} mention_only={mention_only}"
    )
    print(f"wrote {OUT_ENTITIES}")
    print(f"wrote {OUT_CANON}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
