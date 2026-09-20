#!/usr/bin/env python3
"""Wave 1 Jerry Reynolds legacy crawl — writes inventory/legacy/reynolds.json

Craft comparison set only: preface, prologue, part01–part21 (23 pages).
"""
from __future__ import annotations

import json
import re
import time
import urllib.parse
import urllib.request
from collections import defaultdict
from html import unescape
from pathlib import Path

from bs4 import BeautifulSoup, Comment, Tag

UA = (
    "SCVHistory-Legacy-Extract/1.0 "
    "(+https://github.com/starksocialmedia/scvhistory; contact: nathan@starksocial.com)"
)
INDEX_URL = "https://scvhistory.com/scvhistory/signal/reynolds/contents.html"
BASE = "https://scvhistory.com/scvhistory/signal/reynolds/"
CRAWLED = "2026-09-17"
OUT_PATH = Path("/workspace/scvhistory/inventory/legacy/reynolds.json")
CACHE_DIR = Path("/tmp/reynolds_pages")
SLEEP = 1.0

# Exactly these 23, in series_position order
EXPECTED_KEYS = (
    ["preface", "prologue"]
    + [f"part{i:02d}" for i in range(1, 22)]
)
TOC_TITLES = {
    "preface": "Preface",
    "prologue": "Prologue",
    "part01": "1. A Valley Takes Shape",
    "part02": "2. Where Eagles Dare",
    "part03": "3. Man Arrives",
    "part04": "4. Children of Nature",
    "part05": "5. Tribal Relics",
    "part06": "6. Winds of Change",
    "part07": "7. Spain Reconnoiters",
    "part08": "8. The Feast",
    "part09": "9. The Trail Blazer",
    "part10": "10. Solitary Hiker",
    "part11": "11. Ferdinand's Grasp",
    "part12": "12. Staking Claim",
    "part13": "13. Insurrection",
    "part14": "14. Lord and Master",
    "part15": "15. Family Squabbles",
    "part16": "16. Golden Dreams",
    "part17": "17. Yanks Infiltrate",
    "part18": "18. The Pathfinder",
    "part19": "19. Paradise Found",
    "part20": "20. An Eager Market",
    "part21": "21. Buttons and Bows",
}

COMMUNITIES = [
    "Acton", "Agua Dulce", "Bouquet Canyon", "Camulos", "Canyon Country", "Castaic",
    "Castaic Junction", "Fair Oaks Ranch", "Fillmore", "Frazier Park", "Haskell Canyon",
    "Hasley Canyon", "Lake Hughes", "Lebec", "Mentryville", "Mint Canyon", "Mojave Desert",
    "Newhall", "Pico Canyon", "Piru", "Placerita Canyon", "Potrero Canyon", "Ravenna",
    "San Francisquito Canyon", "Sand Canyon", "Santa Clarita", "Saugus", "Saugus-Valencia",
    "Soledad Canyon", "Soledad Township", "Stevenson Ranch", "Tejon", "Towsley Canyon",
    "Val Verde", "Valencia",
]
COMMUNITY_SET = set(COMMUNITIES)

CHROME_ANCHORS = {
    "NEXT", "PREVIOUS", "CONTENTS", "SEARCH", "RETURN TO TOP", "RETURN TO MAIN INDEX",
    "PHOTO CREDITS", "BIBLIOGRAPHY", "BOOKS FOR SALE",
    "comments powered by Disqus.",
}

DATE_PATTERNS = [
    re.compile(
        r"\b(?:January|February|March|April|May|June|July|August|September|October|November|December)"
        r"\s+\d{1,2}(?:st|nd|rd|th)?,?\s+\d{4}\b"
    ),
    re.compile(
        r"\b(?:Jan\.|Feb\.|Mar\.|Apr\.|Jun\.|Jul\.|Aug\.|Sept\.|Sep\.|Oct\.|Nov\.|Dec\.)"
        r"\s+\d{1,2},?\s+\d{4}\b"
    ),
    re.compile(
        r"\b(?:January|February|March|April|May|June|July|August|September|October|November|December)"
        r"\s+\d{4}\b"
    ),
    re.compile(r"\bc\.\s*\d{4}\b", re.I),
    re.compile(r"\bcirca\s+\d{4}\b", re.I),
    re.compile(r"\b(?:about|around|early|late|mid[- ]?)\s+\d{4}\b", re.I),
    re.compile(r"\b\d{1,2}[-/]\d{1,2}[-/]\d{2,4}\b"),
    re.compile(r"\b(?:in|by|of|since|until|from|after|before)\s+(?:the\s+)?(?:year\s+)?(\d{4})\b", re.I),
    re.compile(r"\b(18\d{2}|19\d{2}|20\d{2})\b"),
    re.compile(r"\b(?:Spring|Summer|Fall|Autumn|Winter)\s+(?:of\s+)?\d{4}\b", re.I),
    re.compile(r"\b(?:the following spring|the following year|the next year|that year)\b", re.I),
]

SKIP_IMG_SUBSTR = [
    "scvhist2.gif", "pixel.quantserve", "favicon", "engine1/", "banner",
    "pagead", "doubleclick", "googlesyndication",
]

DISALLOWED_PREFIXES = [
    "/beta/", "/scvhistory/temp/", "/scvhistory/westways0301_files/",
    "/scvhistory/westways0799a_files/", "/scvhistory/westways0799b_files/",
    "/scvhistory/0000/", "/scvhistory/freebooks/", "/freebooks/",
]

HONORIFICS_FULL = {
    "Mr", "Mrs", "Miss", "Ms", "Don", "Doña", "Dona", "Señor", "Senor", "Señora", "Senora",
    "Father", "Fr", "Judge", "Col", "Capt", "Dr", "Gen", "Lt", "Rev", "Major", "Mayor",
    "Governor", "Gov", "General", "Captain", "Colonel", "Lieutenant", "Reverend",
    "Sister", "Brother",
}
HONORIFIC_SINGLE_OK = {
    "Don", "Doña", "Dona", "Señor", "Senor", "Señora", "Senora",
    "Father", "Fr", "Governor", "Gov", "General", "Gen", "Judge",
    "Colonel", "Col", "Captain", "Capt", "Major", "Mayor", "Lieutenant", "Lt",
    "Reverend", "Rev", "Sister", "Brother",
}
HONORIFIC_NEEDS_SURNAME_STYLE = {"Mr", "Mrs", "Miss", "Ms", "Dr"}

STOP_WORD = {
    "The", "A", "An", "And", "Or", "But", "In", "On", "At", "Of", "To", "For", "From",
    "With", "By", "As", "This", "That", "These", "Those", "Then", "When", "Where",
    "Which", "Who", "Early", "Late", "First", "Last", "Next", "Click", "Enlarge",
    "Return", "Top", "Index", "Contents", "Story", "Valley", "Part", "Chapter",
    "Notes", "Introduction", "Bibliography", "Books", "Photo", "Credits", "See",
    "Also", "Note", "Continued", "End", "Page", "Figure", "Volume", "Section",
    "Article", "Essay", "Manuscript", "Editors", "Editor", "Author", "Courtesy",
    "Collection", "Museum", "Library", "University", "College", "Institute",
    "Historical", "Society", "Southern", "Northern", "Western", "Eastern", "Central",
    "Mission", "Rancho", "County", "City", "Town", "River", "Creek", "Canyon", "Lake",
    "Pass", "Road", "Highway", "Station", "Depot", "Camp", "Ranch", "Farm", "Mine",
    "Oil", "Gold", "Silver", "Spanish", "Mexican", "American", "Indian", "Native",
    "California", "Californian", "English", "French", "Chinese", "Overland",
    "Butterfield", "Pacific", "Union", "Signal", "Enterprise", "Sentinel", "Parade",
    "High", "School", "Church", "Court", "House", "Hotel", "Store", "Stage",
    "Stagecoach", "Stages", "Transportation", "Highways", "Records", "Range",
    "Slate", "Kern", "Tulare", "Bernardino", "Francisco", "Fernando", "Buenaventura",
    "Clara", "Barbara", "Angeles", "Ventura", "Sacramento", "Mexico", "Spain",
    "United", "States", "New", "Old", "North", "South", "East", "West", "Upper",
    "Lower", "Big", "Little", "Great", "Grand", "Santa", "San", "Las", "Los", "El",
    "La", "Del", "De", "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December", "Monday",
    "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday", "Spring",
    "Summer", "Fall", "Winter", "Autumn", "Christmas", "Surrey", "Carriage",
    "Wagon", "Coach", "Train", "Railroad", "Railway", "Company", "Corporation",
    "Association", "District", "Township", "Colony", "Grant", "Treaty", "War",
    "Battle", "Army", "Navy", "Fort", "Main", "Pine", "Spruce", "Oak", "Maple",
    "Sacred", "Camino", "Real", "Street", "Avenue", "Boulevard", "Drive", "Lane",
    "Club", "Kiwanis", "Rotary", "Expedition", "Party", "Works",
    "Star", "Route", "Ridge", "Cut", "Junction", "Our", "His", "Her",
    "Their", "Its", "Some", "Many", "Most", "Such", "Each", "Every", "Both",
    "About", "After", "Before", "During", "Under", "Over", "Into", "Upon",
    "Near", "Along", "Through", "Between", "Among", "Against", "Without",
    "Within", "Across", "Around", "Toward", "Towards", "Until", "While",
    "Though", "Although", "Because", "Since", "Unless", "Whether", "Either",
    "Neither", "Nor", "Not", "Only", "Just", "Even", "Still", "Also", "Too",
    "Very", "More", "Less", "Least", "Much", "Few", "Several",
    "Other", "Another", "Same", "Different", "Own", "All", "Any", "No", "Yes",
    "Here", "There", "Now", "Once", "Again", "Ever", "Never", "Always",
    "Often", "Sometimes", "Usually", "Probably", "Perhaps", "Maybe", "Quite",
    "Rather", "Almost", "Already", "Yet", "Soon", "Later", "Earlier", "Today",
    "Yesterday", "Tomorrow", "Tonight", "Morning", "Evening", "Night", "Noon",
    "Midnight", "Week", "Month", "Year", "Century", "Decade", "Age", "Era",
    "Period", "Time", "Day", "Days", "Years", "Months", "Weeks", "Hours",
    "Mile", "Miles", "Acre", "Acres", "Foot", "Feet", "Inch", "Inches",
    "Side", "End", "Line", "Point", "Place",
    "Area", "Region", "Country", "State", "Nation", "People", "Person", "Man",
    "Men", "Woman", "Women", "Child", "Children", "Family", "Families",
    "Settler", "Settlers", "Pioneer", "Pioneers", "Soldier", "Soldiers",
    "Priest", "Priests", "Padre", "Padres", "Indians", "Tribe",
    "Tribes", "Band", "Bands", "Chief", "Chiefs", "King", "Queen", "Prince",
    "Land", "Lands", "Property", "Deed", "Deeds", "Title", "Titles", "Claim",
    "Claims", "Survey", "Surveys", "Map", "Maps", "Book", "Letter",
    "Letters", "Paper", "Papers", "Document", "Documents", "Report", "Reports",
    "History", "Preface", "Prologue", "Buttons", "Bows", "Golden", "Dreams",
    "Trail", "Blazer", "Eager", "Market", "Family", "Squabbles", "Children",
    "Nature", "Tribal", "Relics", "Winds", "Change", "Spain", "Reconnoiters",
    "Feast", "Solitary", "Hiker", "Ferdinand", "Grasp", "Staking", "Insurrection",
    "Lord", "Master", "Yanks", "Infiltrate", "Pathfinder", "Paradise", "Found",
    "Where", "Eagles", "Dare", "Man", "Arrives", "Takes", "Shape",
}

STOP_SURNAME = STOP_WORD | {
    "Street", "Avenue", "Boulevard", "Drive", "Lane", "Road", "Highway", "Way",
    "Club", "Expedition", "Real", "Society", "Company", "Railroad", "Railway",
    "Valley", "Canyon", "Creek", "River", "Lake", "Pass", "Station", "Depot",
    "Ranch", "Camp", "Mine", "Hotel", "Store", "School", "Church", "Court",
    "County", "City", "Town", "Township", "District", "Colony", "Grant",
    "Stages", "Stage", "Mail", "Express", "Line", "Works", "Oil", "Land",
    "Farming", "Water", "Bank", "Association", "Corporation", "Institute",
    "University", "College", "Library", "Museum", "Collection", "Signal",
    "Enterprise", "Sentinel", "Parade", "High", "Cut", "Junction", "Springs",
    "Peak", "Hills", "Hill", "Mount", "Mountain", "Desert", "Ocean", "Sea",
    "Island", "Bay", "Beach", "Harbor", "Port", "Bridge", "Tunnel", "Trail",
    "Route", "Turnpike", "Plaza", "Square", "Park", "Garden", "Field", "Fields",
    "Grove", "Forest", "Woods", "Meadow", "Flat", "Flats", "Landing",
    "Crossing", "Ferry", "Ford", "Dam", "Reservoir", "Canal", "Ditch", "Well",
    "Wells", "Spring", "Hollow", "Gulch", "Wash", "Arroyo", "Barranca",
    "Party", "Library", "Cave",
}

# Extra false-positive tokens observed in Reynolds prose / chrome
EXTRA_STOP = {
    "Born", "Actually", "Visitors", "Center", "Volunteers", "Cenotaph", "Destiny",
    "Hidalgo", "Manifest", "Presidente", "Treaty", "Dream", "Dreams", "Golden",
    "Buttons", "Bows", "Eager", "Market", "Pathfinder", "Paradise", "Found",
    "Insurrection", "Squabbles", "Relics", "Reconnoiters", "Infiltrate", "Blazer",
    "Solitary", "Hiker", "Ferdinand", "Grasp", "Staking", "Claim", "Yanks",
    "Children", "Nature", "Tribal", "Winds", "Change", "Feast", "Lord", "Master",
    "Family", "Where", "Eagles", "Dare", "Arrives", "Takes", "Shape", "History",
    "Preface", "Prologue", "Chapter", "Part", "Valley", "Santa", "Clarita",
    "Gerald",  # "Born Gerald" fragment — Gerald alone not recorded; Born+Gerald rejected via Born
}
STOP_WORD |= EXTRA_STOP
STOP_SURNAME |= EXTRA_STOP | {
    "Center", "Volunteers", "Cenotaph", "Destiny", "Hidalgo", "Presidente",
    "Treaty", "Visitors", "Dream", "Dreams",
}



HONORIFIC_RE = re.compile(
    r"\b((?:Mr|Mrs|Miss|Ms|Don|Doña|Dona|Señor|Senora|Señora|Senor|"
    r"Father|Fr|Judge|Col|Capt|Dr|Gen|Lt|Rev|Major|Mayor|Governor|Gov|"
    r"General|Captain|Colonel|Lieutenant|Reverend|Sister|Brother)\.?"
    r"\s+[A-Z][A-Za-z'\-]+(?:\s+[A-Z]\.?)?(?:\s+[A-Z][A-Za-z'\-]+){0,2})\b"
)

NAME_RE = re.compile(
    r"\b([A-Z][a-z]{2,}(?:\s+[A-Z]\.)?\s+[A-Z][a-z]{2,}(?:\s+[A-Z][a-z]{2,})?)\b"
)

# Local / adjacent SCV places (NOT closed-list communities — those go to communities)
LOCAL_PLACES = {
    "Placerita", "Soledad", "San Francisquito", "Rancho San Francisco",
    "Pico", "Pico Camp", "Lang", "Lang Station", "Lyon's Station", "Lyons Station",
    "Lyons", "Andrews Station", "Beale's Cut", "Beales Cut", "San Fernando Pass",
    "Fremont Pass", "Fort Tejon", "Elizabeth Lake", "Newhall Ranch", "Railroad Canyon",
    "Grapevine", "Grapevine Canyon", "Ridge Route", "Hart High", "Hart High School",
    "Santa Clara River", "Honby", "Whites Canyon", "Wiley Canyon", "Rice Canyon",
    "Gavin Canyon", "Tunnel Station", "Newhall Pass", "Lake Elizabeth", "Hughes Lake",
    "Liebre", "Tejon Ranch", "Tejon Pass", "Gorman", "Castac", "Castaic Creek",
    "San Fernando Road", "San Fernando road", "Elderberry Canyon", "Piru Creek",
    "Bouquet Reservoir", "San Andreas", "Oak of the Golden Dream",
    "Melody Ranch", "Monogram Ranch", "William S. Hart Park", "Hart Park",
    "Pioneer Oil Refinery", "Sierra Pelona", "Santa Susana Mountains",
    "San Gabriel Mountains", "La Liebre", "Tehachapi",
}

SHORT_FORMS = {
    "Pico": ["Pico Canyon", "Pico Camp"],
    "Soledad": ["Soledad Canyon", "Soledad Township"],
    "Lyons": ["Lyons Station", "Lyon's Station"],
    "Lang": ["Lang Station"],
    "San Francisquito": ["San Francisquito Canyon"],
    "Placerita": ["Placerita Canyon"],
    "Grapevine": ["Grapevine Canyon"],
    "Hart High": ["Hart High School"],
    "Castac": ["Castaic", "Castaic Junction", "Castaic Creek"],
}

EXTERNAL_SCAN = [
    "California", "Mexico", "Spain", "Los Angeles", "San Francisco", "Santa Barbara",
    "Ventura", "Sacramento", "San Fernando Valley", "San Buenaventura", "San Bernardino",
    "San Fernando", "Mission San Fernando", "Antelope Valley", "Santa Clara Valley",
    "Southern California", "Northern California", "United States", "Highway 6", "Highway 99",
    "Bakersfield", "Monterey", "San Diego", "Arizona", "Nevada", "Oregon",
    "Kern River", "Tulare", "Fresno", "Stockton", "Pasadena", "Santa Monica", "England",
    "France", "China", "Texas", "Utah", "New Mexico", "Sonora", "Baja California",
    "Sea of Cortez", "Pacific Ocean", "Americas", "Europe", "Asia",
    "Santa Susana", "San Gabriels", "Tehachapi", "La Liebre",
]

ORG_RE = re.compile(
    r"\b("
    r"(?:Southern|Central|Union)\s+Pacific(?:\s+Railroad)?"
    r"|"
    r"Butterfield\s+(?:Overland\s+)?(?:Mail|Stage|Stages)"
    r"|"
    r"Newhall\s+Land(?:\s+and\s+Farming)?(?:\s+Company)?"
    r"|"
    r"California\s+Star\s+Oil(?:\s+Works)?"
    r"|"
    r"Newhall\s+Signal(?:\s+and\s+Saugus\s+Enterprise)?"
    r"|"
    r"Saugus\s+Enterprise"
    r"|"
    r"Standard\s+Oil(?:\s+Company)?"
    r"|"
    r"Union\s+Oil(?:\s+Company)?"
    r"|"
    r"Wells\s+Fargo"
    r"|"
    r"Santa\s+Clarita\s+Valley\s+Historical\s+Society"
    r"|"
    r"SCV\s+Historical\s+Society"
    r"|"
    r"[A-Z][A-Za-z'\-]+(?:\s+[A-Z][A-Za-z'\-]+){0,3}\s+"
    r"(?:Railroad|Railway|Company|Corporation|Association|Society|"
    r"Church|School|College|University|Bank|Hotel|"
    r"Oil\s+Company|Land\s+Company|Water\s+Company)"
    r")\b"
)

PERSON_RULE = (
    "A person is recorded only when the mention looks like a person: (1) a historical "
    "English/Spanish given name followed by a surname, or (2) an honorific "
    "(Mr., Mrs., Miss, Ms., Don, Doña, Señor, Father, Fr., Judge, Col., Capt., Dr., Gen., "
    "Lt., Rev., Major, Mayor, Governor, General, Captain, Colonel, Lieutenant, Reverend, "
    "Sister, Brother) followed by a name. Bare capitalized words are not people. "
    "Possessives, truncated forms, and common-noun tails (Dam, Party, Library, Cave, "
    "Crossing, Street, etc.) are rejected. Surname-only tokens are not recorded."
)
PLACE_RULE = (
    "A place is recorded only if it lies within or immediately adjacent to the Santa "
    "Clarita Valley (roughly Piru/Fillmore west, Lake Hughes/Elizabeth Lake north, "
    "Acton/Agua Dulce east, San Fernando Pass south, plus Fort Tejon and the Ridge Route "
    "corridor). Geography outside that bound goes in external_places. Closed-list "
    "community names go in communities_mentioned / entity_index.community_mentions, not "
    "places. Capitalized common nouns (e.g. Surrey the carriage) are not places. Bare "
    "short forms (Pico, Soledad, Lyons, Lang, San Francisquito, Placerita, Grapevine, "
    "Hart High, Castac) kept only when standalone; otherwise folded into longer names."
)


def log(msg: str) -> None:
    print(msg, flush=True)


def allowed_url(url: str) -> bool:
    try:
        p = urllib.parse.urlparse(url)
    except Exception:
        return False
    if p.scheme not in ("http", "https"):
        return False
    path = p.path or "/"
    for d in DISALLOWED_PREFIXES:
        if path.startswith(d):
            return False
    return True


def fetch(url: str, depth: int = 0) -> tuple[str, str, bytes]:
    if depth > 5:
        raise RuntimeError(f"Too many redirects: {url}")
    if not allowed_url(url):
        raise RuntimeError(f"robots disallow: {url}")
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=60) as resp:
        final = resp.geturl()
        raw = resp.read()
        ctype = resp.headers.get_content_charset() or ""
    text_probe = raw.decode("utf-8", errors="replace")
    m = re.search(
        r'<meta[^>]+http-equiv\s*=\s*["\']?refresh["\']?[^>]*content\s*=\s*["\']?\s*\d+\s*;\s*url\s*=\s*([^"\'>\s]+)',
        text_probe,
        re.I,
    )
    if not m:
        m = re.search(
            r'<meta[^>]+content\s*=\s*["\']?\s*\d+\s*;\s*url\s*=\s*([^"\'>\s]+)[^>]*http-equiv\s*=\s*["\']?refresh',
            text_probe,
            re.I,
        )
    if m:
        target = unescape(m.group(1).strip().strip("'\""))
        next_url = urllib.parse.urljoin(final, target)
        log(f"  meta-refresh -> {next_url}")
        time.sleep(SLEEP)
        return fetch(next_url, depth + 1)
    return final, ctype, raw


def decode_html(raw: bytes, charset_hint: str) -> str:
    head = raw[:4096].decode("ascii", errors="replace")
    m = re.search(r'charset\s*=\s*["\']?([a-zA-Z0-9_\-]+)', head, re.I)
    enc = (m.group(1) if m else "") or charset_hint or "windows-1252"
    enc = enc.strip().lower()
    aliases = {"x-cp1252": "windows-1252", "cp1252": "windows-1252", "iso-8859-1": "latin-1"}
    enc = aliases.get(enc, enc)
    try:
        return raw.decode(enc)
    except Exception:
        try:
            return raw.decode("latin-1")
        except Exception:
            return raw.decode("windows-1252", errors="replace")


def extract_between_markers(html: str) -> str:
    s = html.find("<!-- XWP-BEGIN-CONTENT -->")
    e = html.find("<!-- XWP-END-CONTENT -->")
    if s >= 0 and e >= 0:
        return html[s + len("<!-- XWP-BEGIN-CONTENT -->") : e]
    return html


def marker_field(html: str, name: str) -> str:
    m = re.search(
        rf"<!--START:{name}-->(.*?)<!--END:{name}-->",
        html,
        re.S | re.I,
    )
    if not m:
        return ""
    inner = m.group(1)
    text = BeautifulSoup(inner, "lxml").get_text(" ", strip=True)
    return " ".join(text.split())


def legacy_from_url(url: str) -> tuple[str, str]:
    p = urllib.parse.urlparse(url)
    path = p.path
    legacy_path = path if path.startswith("/") else "/" + path
    base = path.rsplit("/", 1)[-1]
    key = re.sub(r"\.(html?|php)$", "", base, flags=re.I)
    return legacy_path, key


def sentence_containing(text: str, start: int, end: int) -> str:
    left = max(text.rfind(".", 0, start), text.rfind("?", 0, start), text.rfind("!", 0, start))
    right_candidates = [text.find(c, end) for c in ".?!"]
    right_candidates = [r for r in right_candidates if r >= 0]
    right = min(right_candidates) if right_candidates else len(text)
    if left < 0:
        left = 0
    else:
        left += 1
    if right < len(text) and text[right] in ".?!":
        right += 1
    return " ".join(text[left:right].strip().split())


def extract_dates(body_text: str) -> list[dict]:
    found: list[dict] = []
    seen_spans: list[tuple[int, int]] = []
    for pat in DATE_PATTERNS:
        for m in pat.finditer(body_text):
            span = m.span()
            raw = m.group(0)
            overlap = False
            for a, b in seen_spans:
                if not (span[1] <= a or span[0] >= b):
                    overlap = True
                    break
            if overlap:
                continue
            ctx = sentence_containing(body_text, span[0], span[1]) or raw
            found.append({"text_raw": raw, "context": ctx})
            seen_spans.append(span)
    return found


def is_internal(href: str) -> bool:
    if href.startswith("#") or href.startswith("mailto:"):
        return True
    if href.startswith("/") or not re.match(r"^[a-zA-Z][a-zA-Z0-9+.-]*:", href):
        return True
    host = urllib.parse.urlparse(urllib.parse.urljoin("https://scvhistory.com/", href)).netloc
    return "scvhistory.com" in host or host == ""


def clean_body_soup(content_html: str) -> BeautifulSoup:
    soup = BeautifulSoup(content_html, "lxml")
    for tag in soup.find_all(["script", "style", "noscript"]):
        tag.decompose()
    for c in soup.find_all(string=lambda x: isinstance(x, Comment)):
        c.extract()
    return soup


def extract_body_region(content_html: str) -> tuple[str, str, Tag]:
    soup = clean_body_soup(content_html)
    root = soup.find("body") or soup
    for img in list(root.find_all("img")):
        src = img.get("src") or ""
        if any(s in src for s in SKIP_IMG_SUBSTR):
            parent = img.parent
            if parent and parent.name == "a" and parent.find("img") is img and len(parent.find_all(True)) == 1:
                parent.decompose()
            else:
                img.decompose()
    for a in list(root.find_all("a")):
        t = a.get_text(" ", strip=True).upper()
        if t in CHROME_ANCHORS:
            parent = a.parent
            a.decompose()
            if parent and parent.name in ("font", "span", "p", "div") and not parent.get_text(strip=True):
                parent.decompose()
    for el in list(root.find_all(["span", "div", "p", "font"])):
        t = el.get_text(" ", strip=True)
        tu = t.upper()
        if len(t) > 400:
            continue
        if "RIGHTS RESERVED" in tu or t.startswith("SCVHistory.com is another service"):
            el.decompose()
    # Strip Disqus chrome
    for el in list(root.find_all(id=re.compile(r"disqus", re.I))):
        el.decompose()
    body_html = root.decode_contents() if hasattr(root, "decode_contents") else str(root)
    body_text = root.get_text("\n", strip=False)
    body_text = body_text.replace("\xa0", " ").replace("\r\n", "\n").replace("\r", "\n")
    return body_html, body_text, root


def extract_images(root: Tag) -> list[dict]:
    images = []
    pos = 0
    for img in root.find_all("img"):
        src = img.get("src")
        if src is None:
            continue
        src_raw = src
        if any(s in src_raw for s in SKIP_IMG_SUBSTR):
            continue
        pos += 1
        alt = img.get("alt") or ""
        caption = ""
        credit = ""
        links_to = ""
        parent = img.parent
        if parent and parent.name == "a" and parent.get("href"):
            links_to = parent.get("href") or ""
        container = img.find_parent(["td", "figure", "div", "p"]) or parent
        if container:
            cap = container.find(class_=re.compile(r"caption", re.I))
            if cap:
                caption = " ".join(cap.get_text(" ", strip=True).split())
            cred = container.find(class_=re.compile(r"credit", re.I))
            if cred and "whitelink" not in " ".join(cred.get("class", [])):
                credit = " ".join(cred.get_text(" ", strip=True).split())
        # Nearby italic/small text as caption fallback
        if not caption and parent:
            nxt = parent.find_next_sibling(["font", "p", "div", "i", "em"])
            if nxt:
                ct = " ".join(nxt.get_text(" ", strip=True).split())
                if ct and len(ct) < 200 and "NEXT" not in ct.upper():
                    caption = ct
        images.append({
            "src_raw": src_raw,
            "alt": alt,
            "caption": caption,
            "credit_raw": credit,
            "position_in_body": pos,
            "links_to": links_to,
        })
    return images


def extract_links(root: Tag) -> list[dict]:
    out = []
    for a in root.find_all("a", href=True):
        href_raw = a["href"]
        anchor = " ".join(a.get_text(" ", strip=True).split())
        if anchor.upper() in CHROME_ANCHORS:
            continue
        if not href_raw or href_raw.startswith("javascript:"):
            continue
        out.append({
            "href_raw": href_raw,
            "anchor_text": anchor,
            "is_internal": is_internal(href_raw),
        })
    return out



def extract_related_reading_links(html: str, base_url: str = "") -> list[dict]:
    """Leon's related-reading / sidebar apparatus (inversecaption / thumbcaption columns).

    These often sit in a narrow right-hand TD beside the prose. Main-column extractors
    drop them; body-region extractors may keep them when they fall inside XWP markers.
    Always run this on the full page HTML (or full XWP content) and merge into links_out.
    """
    if not html or not re.search(r"inversecaption|thumbcaption", html, re.I):
        return []
    soup = BeautifulSoup(html, "lxml")
    out: list[dict] = []
    chrome_href = re.compile(
        r"(?i)(key\.htm|bibliography\.htm|publications\.htm|googlesearch|disqus\.com)"
    )
    for td in soup.find_all("td"):
        if not td.find(class_=re.compile(r"inversecaption|thumbcaption", re.I)):
            continue
        for a in td.find_all("a", href=True):
            href_raw = (a.get("href") or "").strip()
            anchor = " ".join(a.get_text(" ", strip=True).split())
            if not href_raw or href_raw.startswith("javascript:") or href_raw.startswith("mailto:"):
                continue
            if anchor.upper() in CHROME_ANCHORS:
                continue
            if chrome_href.search(href_raw):
                continue
            if href_raw.startswith("#") and not anchor:
                continue
            out.append({
                "href_raw": href_raw,
                "anchor_text": anchor,
                "is_internal": is_internal(href_raw),
                "role": "related_reading",
            })
    # Deduplicate by resolved path + anchor
    seen: set[tuple[str, str]] = set()
    deduped: list[dict] = []
    for L in out:
        absu = urllib.parse.urljoin(base_url or "https://scvhistory.com/", L["href_raw"])
        path = urllib.parse.urlparse(absu).path.rstrip("/").lower()
        path = re.sub(r"/index\.html?$", "", path, flags=re.I)
        key = (path, L["anchor_text"])
        if key in seen:
            continue
        seen.add(key)
        deduped.append(L)
    return deduped


def merge_links_out(existing: list[dict], related: list[dict], base_url: str = "") -> list[dict]:
    """Merge related-reading links into links_out without dropping existing entries."""
    def norm(href: str) -> str:
        absu = urllib.parse.urljoin(base_url or "https://scvhistory.com/", href or "")
        path = urllib.parse.urlparse(absu).path.rstrip("/").lower()
        return re.sub(r"/index\.html?$", "", path, flags=re.I)

    out = list(existing or [])
    have = {norm(L.get("href_raw") or "") for L in out}
    for L in related or []:
        n = norm(L.get("href_raw") or "")
        if not n or n in have:
            # still tag existing match with role if missing
            if n in have:
                for e in out:
                    if norm(e.get("href_raw") or "") == n and "role" not in e:
                        e["role"] = "related_reading"
                        break
            continue
        have.add(n)
        out.append(L)
    return out



def extract_editor_notes(root: Tag, body_text: str) -> list[dict]:
    notes = []
    for m in re.finditer(r"\[(?:Ed\.?|Editor|Note|sic|emphasis added)[^\]]*\]", body_text, re.I):
        notes.append({"position": "inline", "text": m.group(0)})
    for p in root.find_all(["p", "div", "font", "span"]):
        t = " ".join(p.get_text(" ", strip=True).split())
        if re.match(r"^\[.*(Ed\.|Editor|note).*\]$", t, re.I):
            if not any(n["text"] == t for n in notes):
                notes.append({"position": "top", "text": t})
    return notes


def extract_fine_print(content_html: str) -> str:
    soup = BeautifulSoup(content_html, "lxml")
    credits = []
    for el in soup.find_all(class_=re.compile(r"credit", re.I)):
        t = " ".join(el.get_text(" ", strip=True).split())
        if t and "SCVHistory.com is another service" not in t:
            credits.append(t)
    return "\n".join(credits)


def clean_name(name: str) -> str:
    return re.sub(r"\s+", " ", name.strip()).rstrip(".,;:")


def looks_like_person_tokens(parts: list[str]) -> bool:
    if len(parts) < 2:
        return False
    i = 0
    if parts[0].rstrip(".") in HONORIFICS_FULL:
        i = 1
    name_parts = parts[i:]
    if not name_parts:
        return False
    for p in name_parts:
        core = p.rstrip(".")
        if core in STOP_WORD or core in STOP_SURNAME:
            return False
        if len(core) < 2:
            return False
    if name_parts[-1].rstrip(".") in STOP_SURNAME:
        return False
    return True


def extract_people(text: str) -> dict[str, int]:
    counts: dict[str, int] = defaultdict(int)
    COMMON_GIVEN_ONLY = {
        "John", "Mary", "James", "William", "Robert", "George", "Charles",
        "Joseph", "Thomas", "Henry", "Edward", "Frank", "Richard", "Harry",
        "Walter", "Arthur", "Fred", "Albert", "Joe", "Bill", "Jack", "Tom",
        "Jim", "Bob", "Sam", "Will", "Mac", "Ann", "Anna", "Jane", "Helen",
    }
    for m in HONORIFIC_RE.finditer(text):
        name = clean_name(m.group(1))
        parts = name.split()
        if len(parts) < 2:
            continue
        hon = parts[0].rstrip(".")
        rest = parts[1:]
        if not looks_like_person_tokens(parts):
            continue
        if hon in HONORIFIC_NEEDS_SURNAME_STYLE:
            if len(rest) == 1 and rest[0].rstrip(".") in COMMON_GIVEN_ONLY:
                continue
        elif hon not in HONORIFIC_SINGLE_OK and len(rest) < 2:
            continue
        # reject if name is a community
        if name in COMMUNITY_SET or any(p in COMMUNITY_SET for p in [name]):
            continue
        counts[name] += 1

    for m in NAME_RE.finditer(text):
        name = clean_name(m.group(1))
        parts = name.split()
        if parts[0].rstrip(".") in HONORIFICS_FULL:
            continue
        if not looks_like_person_tokens(parts):
            continue
        given = parts[0]
        surname = parts[-1]
        if given in STOP_WORD or surname in STOP_SURNAME:
            continue
        if re.search(
            r"\b(Canyon|Pass|Station|Ranch|Road|Creek|River|Valley|Lake|Cut|"
            r"Junction|Company|Railroad|Society|School|Church|Street|Avenue|"
            r"Club|Expedition|Hotel|Store|Mine|Camp|Depot|Highway|Trail|Route|"
            r"Dam|Party|Library|Cave|Crossing|Mountains|Mountain)\b",
            name,
        ):
            continue
        if given in {"San", "Santa", "Los", "Las", "El", "La", "Fort", "Lake", "Mission", "Rancho", "Mount", "Mt"}:
            continue
        if name in COMMUNITY_SET:
            continue
        # Reject ALL-CAPS chrome
        letters = re.sub(r"[^A-Za-z]", "", name)
        if letters and letters == letters.upper() and len(parts) >= 2:
            continue
        counts[name] += 1

    # Drop obvious non-persons / fragments
    drop = []
    for name in counts:
        parts = name.split()
        if any(p.rstrip('.') in EXTRA_STOP for p in parts):
            drop.append(name)
            continue
        if re.search(
            r"\b(Center|Volunteers|Cenotaph|Destiny|Treaty|Visitors|Museum|Park|"
            r"Creek|Canyon|River|Valley|Pass|Station|Ranch|Road|Highway|Trail|"
            r"Company|Society|School|Church|Hotel|Bank|Railroad|Army|Navy)\b",
            name,
        ):
            drop.append(name)
            continue
        # Reject duplicated token like "Pedro Fages Fages"
        if len(parts) >= 3 and parts[-1] == parts[-2]:
            drop.append(name)
            continue
        # Reject "Father Presidente" style (title + title)
        if parts[0].rstrip('.') in HONORIFICS_FULL and parts[-1].rstrip('.') in {
            "Presidente", "President", "Father", "Padre", "Reverend", "Bishop",
        }:
            drop.append(name)
            continue
    for name in drop:
        counts.pop(name, None)
    return dict(counts)


def count_wb(text: str, name: str) -> int:
    return len(re.findall(r"\b" + re.escape(name) + r"\b", text))


def standalone_short_count(text: str, short: str, longers: list[str]) -> int:
    """Count short-form hits that are NOT part of a longer name on this page."""
    total = count_wb(text, short)
    if total <= 0:
        return 0
    covered = 0
    for longer in longers:
        # Each longer occurrence covers one short occurrence
        covered += count_wb(text, longer)
    # Also cover community longer forms already in COMMUNITY_SET
    return max(0, total - covered)


def extract_places_and_external(text: str) -> tuple[dict[str, int], dict[str, int], list[dict]]:
    """Return (local_places, external_places, needs_review entries for short forms)."""
    local: dict[str, int] = defaultdict(int)
    needs_review: list[dict] = []

    # Match all local places longest-first
    for loc in sorted(LOCAL_PLACES, key=len, reverse=True):
        n = count_wb(text, loc)
        if n:
            local[loc] += n

    # Fold / flag short forms
    for short, longers in SHORT_FORMS.items():
        if short not in local:
            continue
        stand = standalone_short_count(text, short, longers)
        if stand <= 0:
            # all hits inside longer names — drop short entry
            local.pop(short, None)
        else:
            local[short] = stand
            needs_review.append({
                "reason": "possible_place_variant",
                "detail": (
                    f"'{short}' appears {stand} time(s) standing alone; "
                    f"possible variant of {', '.join(longers)}"
                ),
            })

    # Never keep Surrey as a place
    local.pop("Surrey", None)

    # Communities must not appear in places
    for c in list(local):
        if c in COMMUNITY_SET:
            local.pop(c, None)

    external: dict[str, int] = defaultdict(int)
    for ext in sorted(EXTERNAL_SCAN, key=len, reverse=True):
        if ext in COMMUNITY_SET:
            continue
        n = count_wb(text, ext)
        if n <= 0:
            continue
        if ext == "San Fernando":
            for longer in [
                "San Fernando Pass", "San Fernando Road", "San Fernando road",
                "San Fernando Valley", "Mission San Fernando",
            ]:
                n -= count_wb(text, longer)
            n = max(0, n)
        if ext == "San Francisco":
            n -= count_wb(text, "Rancho San Francisco")
            n = max(0, n)
        if ext == "Santa Susana":
            n -= count_wb(text, "Santa Susana Mountains")
            n = max(0, n)
        if n:
            external[ext] += n

    # Don't double-count locals in external
    for loc in list(external):
        if loc in local or loc in COMMUNITY_SET or loc in LOCAL_PLACES:
            # San Fernando Pass etc. are local; bare San Fernando is external
            if loc in LOCAL_PLACES or loc in COMMUNITY_SET:
                external.pop(loc, None)

    return dict(local), dict(external), needs_review


def extract_orgs(text: str) -> dict[str, int]:
    counts: dict[str, int] = defaultdict(int)
    for m in ORG_RE.finditer(text):
        name = clean_name(m.group(1))
        if len(name) < 5:
            continue
        if name.lower() in {"the signal"}:
            name = "The Signal"
        parts = name.split()
        if parts and parts[0] in STOP_WORD and parts[0] not in {
            "Southern", "Central", "Union", "Newhall", "California", "Butterfield",
            "Saugus", "Standard", "Wells", "Santa", "SCV",
        }:
            continue
        counts[name] += 1
    return dict(counts)


def extract_communities(text: str) -> list[str]:
    """Record a community only where prose places something there.

    Never infer community from a place/org name (Saugus Cafe, Newhall Land,
    Castaic Creek, Hyatt Valencia, etc.). Prefer longest closed-list match; do
    not also record a shorter community that is only a prefix of a longer list
    entry.
    """
    if not text:
        return []
    name_tail = re.compile(
        r"^\s*(?:"
        r"Cafe|Caf[eé]|Hotel|Saloon|Ranch|Company|Land|Farming|Station|Depot|"
        r"Speedway|School|High|Elementary|College|Church|Bank|Mill|Store|"
        r"Lake|Dam|Creek|River|Wash|Pass|Road|Ave|Avenue|Blvd|Boulevard|"
        r"Street|St\.?|Hwy|Highway|Freeway|Park|Plaza|Mall|Center|Centre|"
        r"Market|Hardware|Hospital|Library|Museum|Cemetery|Airport|"
        r"&|and"
        r")\b",
        re.I,
    )
    name_head = re.compile(
        r"(?:"
        r"Cafe|Caf[eé]|Hotel|Saloon|Ranch|Company|Land|Farming|Station|Depot|"
        r"Speedway|School|Hyatt|Fort|Mount|Mt\.?|Lake|Rancho|Plaza|Mall|"
        r"Museum|Library|Hospital|Airport|Ranch"
        r")\s+$",
        re.I,
    )
    cap_cont = re.compile(
        r"(?i)^(Land|Farming|Cafe|Hotel|Lake|Junction|Canyon|Station|"
        r"Speedway|School|Ranch|Creek|Pass|Park|Ave|Avenue|Road)$"
    )
    masked = text
    found = []
    for name in sorted(COMMUNITIES, key=len, reverse=True):
        pat = re.compile(
            r"(?<![A-Za-z0-9])" + re.escape(name) + r"(?![A-Za-z0-9])",
            re.I,
        )
        if not pat.search(masked):
            continue
        keep = False
        for m in pat.finditer(masked):
            after = masked[m.end() : m.end() + 48]
            before = masked[max(0, m.start() - 48) : m.start()]
            if name_tail.match(after):
                continue
            if name_head.search(before):
                continue
            m2 = re.match(r"^\s+([A-Z][A-Za-z0-9'\-]+)", after)
            if m2 and m2.group(1).lower() not in {
                "the", "a", "an", "in", "on", "at", "to", "for", "from",
                "with", "by", "and", "or", "of",
            }:
                if cap_cont.match(m2.group(1)):
                    continue
            keep = True
            break
        if keep:
            found.append(name)
            masked = pat.sub(" " + ("_" * len(name)) + " ", masked)
    order = {n: i for i, n in enumerate(COMMUNITIES)}
    return sorted(set(found), key=lambda n: order[n])



def mention_list(counts: dict[str, int]) -> list[dict]:
    return [
        {"name_raw": k, "count": v, "linked_to": ""}
        for k, v in sorted(counts.items(), key=lambda x: (-x[1], x[0].lower()))
    ]


def process_page(url: str, series_position: int, toc_title: str, html: str, final_url: str) -> dict:
    legacy_path, legacy_key = legacy_from_url(final_url)
    content_html = extract_between_markers(html)
    title = marker_field(html, "SLUG") or toc_title
    if not title:
        tsoup = BeautifulSoup(html, "lxml")
        if tsoup.title and tsoup.title.string:
            title = tsoup.title.string.strip()
            # strip site prefix
            title = re.sub(r"^SCVHistory\.com\s*\|\s*", "", title)
            title = re.sub(r"^History of the Santa Clarita Valley by Jerry Reynolds\s*\|\s*", "", title, flags=re.I)
            title = title.strip()
    # altheadline fallback
    if not title or title.lower() in ("preface", "prologue") and toc_title:
        # keep toc/slug; also try altheadline
        m = re.search(r'class="altheadline"[^>]*>([^<]+)', content_html, re.I)
        if m and m.group(1).strip():
            title = m.group(1).strip()
    byline_raw = marker_field(html, "BYLINE")
    date_raw = marker_field(html, "DATE")
    subtitle = marker_field(html, "SUBTITLE") or marker_field(html, "DECK") or ""

    body_html, body_text, root = extract_body_region(content_html)
    images = extract_images(root)
    links_out = extract_links(root)
    # Related-reading sidebars (often a narrow TD) — merge from full page HTML
    related = extract_related_reading_links(html, final_url)
    if not related:
        related = extract_related_reading_links(content_html, final_url)
    links_out = merge_links_out(links_out, related, final_url)
    editor_notes = extract_editor_notes(root, body_text)
    fine_print_raw = extract_fine_print(content_html)
    dates_mentioned = extract_dates(body_text)
    communities_mentioned = extract_communities(body_text)

    people = extract_people(body_text)
    # Enrich linked_to from internal person-ish links
    for link in links_out:
        text = link["anchor_text"].strip()
        href = link["href_raw"]
        if text in people and href and is_internal(href) and not people.get("_linked"):
            # store via rebuild of mention later
            pass

    local, external, short_nr = extract_places_and_external(body_text)
    orgs = extract_orgs(body_text)

    # Attach linked_to for people/places when anchor matches
    people_mentions = mention_list(people)
    for ment in people_mentions:
        for link in links_out:
            if link["anchor_text"].strip() == ment["name_raw"] and link["is_internal"]:
                ment["linked_to"] = link["href_raw"]
                break

    places_mentions = mention_list(local)
    for ment in places_mentions:
        for link in links_out:
            if link["anchor_text"].strip() == ment["name_raw"] and link["is_internal"]:
                ment["linked_to"] = link["href_raw"]
                break

    orgs_mentions = mention_list(orgs)
    external_mentions = mention_list(external)

    needs_review = list(short_nr)

    # Flag possible same-person surname groups
    last_groups: dict[str, list[str]] = defaultdict(list)
    for n in people:
        parts = n.replace(".", "").split()
        if parts:
            last_groups[parts[-1]].append(n)
    for last, group in last_groups.items():
        uniq = sorted(set(group))
        if len(uniq) >= 2 and last[0].isupper() and len(last) > 2:
            needs_review.append({
                "reason": "possible_same_person",
                "detail": f"Multiple name forms with surname {last}: " + "; ".join(uniq),
            })

    page = {
        "source_url": final_url,
        "legacy_path": legacy_path,
        "legacy_key": legacy_key,
        "title": title or toc_title or "",
        "subtitle": subtitle or "",
        "byline_raw": byline_raw or "",
        "date_raw": date_raw or "",
        "series_position": series_position,
        "body_text": body_text,
        "body_html": body_html,
        "editor_notes": editor_notes,
        "images": images,
        "links_out": links_out,
        "dates_mentioned": dates_mentioned,
        "people_mentioned": people_mentions,
        "places_mentioned": places_mentions,
        "orgs_mentioned": orgs_mentions,
        "communities_mentioned": communities_mentioned,
        "external_places_mentioned": external_mentions,
        "fine_print_raw": fine_print_raw or "",
        "needs_review": needs_review,
    }
    # No nulls
    for k, v in list(page.items()):
        if v is None:
            page[k] = "" if k not in (
                "editor_notes", "images", "links_out", "dates_mentioned",
                "people_mentioned", "places_mentioned", "orgs_mentioned",
                "communities_mentioned", "external_places_mentioned", "needs_review",
            ) else []
    return page


def build_entity_index(pages: list[dict]) -> dict:
    def agg(key: str) -> list[dict]:
        by_name: dict[str, dict] = {}
        for page in pages:
            url = page["source_url"]
            for m in page.get(key) or []:
                name = m["name_raw"]
                ent = by_name.setdefault(
                    name,
                    {
                        "name_raw": name,
                        "name_variants": [],
                        "mention_count": 0,
                        "pages": [],
                        "has_legacy_page": False,
                        "legacy_page_url": "",
                    },
                )
                ent["mention_count"] += m.get("count") or 0
                if url not in ent["pages"]:
                    ent["pages"].append(url)
                linked = m.get("linked_to") or ""
                if linked and not ent["has_legacy_page"]:
                    if re.search(r"\.(html?|php)(\?|$)", linked, re.I) or (
                        (linked.startswith("/") or "scvhistory" in linked)
                        and not re.search(r"\.(jpe?g|gif|png|css|js)(\?|$)", linked, re.I)
                    ):
                        leaf = linked.rsplit("/", 1)[-1].lower()
                        if leaf not in (
                            "contents.html", "index.html", "scvhistory.htm",
                            "googlesearch.htm", "key.htm", "bibliography.htm",
                            "publications.htm",
                        ):
                            abs_linked = urllib.parse.urljoin(url, linked)
                            crawled = {p["source_url"] for p in pages}
                            if abs_linked in crawled:
                                ent["has_legacy_page"] = True
                                ent["legacy_page_url"] = abs_linked
        return sorted(by_name.values(), key=lambda e: (-e["mention_count"], e["name_raw"].lower()))

    # community_mentions: aggregate from string lists with counts from body
    by_comm: dict[str, dict] = {}
    for page in pages:
        url = page["source_url"]
        text = page.get("body_text") or ""
        for name in page.get("communities_mentioned") or []:
            cnt = count_wb(text, name)
            ent = by_comm.setdefault(
                name,
                {
                    "name_raw": name,
                    "name_variants": [],
                    "mention_count": 0,
                    "pages": [],
                    "has_legacy_page": False,
                    "legacy_page_url": "",
                },
            )
            ent["mention_count"] += max(1, cnt)
            if url not in ent["pages"]:
                ent["pages"].append(url)
    community_mentions = sorted(
        by_comm.values(), key=lambda e: (-e["mention_count"], e["name_raw"].lower())
    )

    return {
        "people": agg("people_mentioned"),
        "places": agg("places_mentioned"),
        "organizations": agg("orgs_mentioned"),
        "external_places": agg("external_places_mentioned"),
        "community_mentions": community_mentions,
    }


def assert_no_nulls(obj, path="root"):
    if obj is None:
        raise AssertionError(f"null at {path}")
    if isinstance(obj, dict):
        for k, v in obj.items():
            assert_no_nulls(v, f"{path}.{k}")
    elif isinstance(obj, list):
        for i, v in enumerate(obj):
            assert_no_nulls(v, f"{path}[{i}]")


def main() -> None:
    CACHE_DIR.mkdir(parents=True, exist_ok=True)
    notes: list[str] = []
    failures: list[str] = []
    pages: list[dict] = []
    found_keys: list[str] = []

    # Fetch index first (for notes / robots check); use cached reynolds-contents if desired
    log(f"Fetching index: {INDEX_URL}")
    time.sleep(SLEEP)
    try:
        final, enc, raw = fetch(INDEX_URL)
        index_html = decode_html(raw, enc)
        (CACHE_DIR / "contents.html").write_bytes(raw)
        notes.append(
            "Index fetched OK; full TOC has 71 chapters + preface/prologue/dedication/"
            "epilogue/bibliography/notes etc.; this wave crawls Craft comparison set only "
            "(preface, prologue, chapters 1–21 = 23 pages)."
        )
    except Exception as e:
        notes.append(f"Index fetch failed (continuing with fixed URL list): {e}")
        index_html = ""

    for i, key in enumerate(EXPECTED_KEYS, 1):
        url = BASE + key + ".html"
        toc_title = TOC_TITLES.get(key, key)
        log(f"[{i}/23] Fetching {url}")
        time.sleep(SLEEP)
        try:
            final, enc, raw = fetch(url)
            html = decode_html(raw, enc)
            safe = re.sub(r"[^\w.-]+", "_", urllib.parse.urlparse(final).path.strip("/"))
            (CACHE_DIR / safe).write_bytes(raw)
            page = process_page(url, i, toc_title, html, final)
            # Force legacy_key to expected filename key (in case of redirect)
            # Prefer expected key for Craft comparison
            expected_path = f"/scvhistory/signal/reynolds/{key}.html"
            if page["legacy_key"] != key and "reynolds" in (page["legacy_path"] or ""):
                # keep redirected path but note
                notes.append(f"{key} resolved to {final} (legacy_key={page['legacy_key']})")
            elif page["legacy_key"] != key:
                page["legacy_key"] = key
                page["legacy_path"] = expected_path
            pages.append(page)
            found_keys.append(page["legacy_key"])
            log(
                f"  OK key={page['legacy_key']} title={page['title']!r} "
                f"body_len={len(page['body_text'])} people={len(page['people_mentioned'])} "
                f"places={len(page['places_mentioned'])} communities={page['communities_mentioned']}"
            )
        except Exception as e:
            msg = f"Failed series_position={i} key={key} url={url}: {e}"
            log("  ERROR " + msg)
            failures.append(msg)
            notes.append(msg)

    missing = [k for k in EXPECTED_KEYS if k not in found_keys]
    entity_index = build_entity_index(pages)

    crawler_notes = (
        f"Wave 1 Jerry Reynolds Craft comparison crawl. "
        f"expected_craft_set=preface,prologue,part01–part21 (23 pages). "
        f"Crawled {len(pages)} pages with 1.0s delay; UA SCVHistory-Legacy-Extract/1.0; "
        f"robots.txt respected; meta-refresh followed; HTML decoded via charset/latin-1. "
        f"Single pages array (no series/related split). "
        f"Entity rules match accepted Perkins Wave 0: people=given+surname OR honorific+name; "
        f"closed-list communities separate from places; external_places for outside geography; "
        f"short place forms folded or flagged possible_place_variant; Surrey not a place. "
        f"body_text/body_html from XWP content region; images src_raw not absolutized; no downloads. "
        + (" ".join(notes) if notes else "")
    )
    if failures:
        crawler_notes += f" Failures: {len(failures)}."

    doc = {
        "meta": {
            "section": "reynolds",
            "crawled": CRAWLED,
            "index_url": INDEX_URL,
            "page_count": len(pages),
            "crawler_notes": crawler_notes,
            "expected_craft_pages": 23,
            "expected_craft_set": list(EXPECTED_KEYS),
            "found_pages": len(pages),
            "missing_from_expected": missing,
            "person_rule": PERSON_RULE,
            "place_rule": PLACE_RULE,
        },
        "pages": pages,
        "entity_index": entity_index,
    }

    assert_no_nulls(doc)

    OUT_PATH.parent.mkdir(parents=True, exist_ok=True)
    with OUT_PATH.open("w", encoding="utf-8") as f:
        json.dump(doc, f, ensure_ascii=False, indent=2)
        f.write("\n")

    # Verify read-back
    with OUT_PATH.open(encoding="utf-8") as f:
        check = json.load(f)
    assert len(check["pages"]) == len(pages)
    assert_no_nulls(check)

    ei = check["entity_index"]
    log(f"Wrote {OUT_PATH} bytes={OUT_PATH.stat().st_size}")
    log(f"pages={len(check['pages'])} missing={missing} failures={len(failures)}")
    log(
        f"entity_index people={len(ei['people'])} places={len(ei['places'])} "
        f"orgs={len(ei['organizations'])} external={len(ei['external_places'])} "
        f"communities={len(ei['community_mentions'])}"
    )
    log("legacy_keys=" + ",".join(p["legacy_key"] for p in check["pages"]))




def reprocess_from_cache() -> None:
    """Rebuild JSON from /tmp/reynolds_pages without network."""
    CACHE_DIR.mkdir(parents=True, exist_ok=True)
    notes = ["Reprocessed from /tmp/reynolds_pages cache after entity filter fixes."]
    pages = []
    failures = []
    found_keys = []
    cache_files = {p.name: p for p in CACHE_DIR.iterdir() if p.is_file()}
    for i, key in enumerate(EXPECTED_KEYS, 1):
        url = BASE + key + ".html"
        toc_title = TOC_TITLES.get(key, key)
        leaf = key + ".html"
        found = None
        for name, fp in cache_files.items():
            if name.endswith(leaf) or leaf.replace(".", "_") in name or name.endswith(key + ".html"):
                found = fp
                break
        if not found:
            # underscore form
            needle = f"signal_reynolds_{key}.html"
            for name, fp in cache_files.items():
                if key in name and name.endswith(".html") or name.endswith(key + "_html") or f"_{key}_html" in name or name.endswith(f"_{key}.html"):
                    found = fp
                    break
            if not found:
                for name, fp in cache_files.items():
                    if key in name:
                        found = fp
                        break
        if not found:
            msg = f"cache miss {key}"
            log(msg)
            failures.append(msg)
            continue
        raw = found.read_bytes()
        html = decode_html(raw, "")
        final_url = url
        log(f"[{i}/23] Reprocess {found.name}")
        page = process_page(url, i, toc_title, html, final_url)
        if page["legacy_key"] != key:
            page["legacy_key"] = key
            page["legacy_path"] = f"/scvhistory/signal/reynolds/{key}.html"
            page["source_url"] = url
        pages.append(page)
        found_keys.append(key)
        log(f"  OK people={len(page['people_mentioned'])} places={len(page['places_mentioned'])}")
    missing = [k for k in EXPECTED_KEYS if k not in found_keys]
    entity_index = build_entity_index(pages)
    crawler_notes = (
        f"Wave 1 Jerry Reynolds Craft comparison crawl (cache reprocess). "
        f"expected_craft_set=preface,prologue,part01–part21 (23 pages). "
        f"Crawled/reprocessed {len(pages)} pages; UA SCVHistory-Legacy-Extract/1.0; "
        f"robots.txt respected; meta-refresh followed; HTML decoded via charset/latin-1. "
        f"Single pages array. Entity rules match accepted Perkins Wave 0. "
        + " ".join(notes)
    )
    doc = {
        "meta": {
            "section": "reynolds",
            "crawled": CRAWLED,
            "index_url": INDEX_URL,
            "page_count": len(pages),
            "crawler_notes": crawler_notes,
            "expected_craft_pages": 23,
            "expected_craft_set": list(EXPECTED_KEYS),
            "found_pages": len(pages),
            "missing_from_expected": missing,
            "person_rule": PERSON_RULE,
            "place_rule": PLACE_RULE,
        },
        "pages": pages,
        "entity_index": entity_index,
    }
    assert_no_nulls(doc)
    OUT_PATH.parent.mkdir(parents=True, exist_ok=True)
    with OUT_PATH.open("w", encoding="utf-8") as f:
        json.dump(doc, f, ensure_ascii=False, indent=2)
        f.write("\n")
    log(f"Wrote {OUT_PATH} bytes={OUT_PATH.stat().st_size} pages={len(pages)} missing={missing}")


if __name__ == "__main__":
    import sys
    if len(sys.argv) > 1 and sys.argv[1] == "--from-cache":
        reprocess_from_cache()
    else:
        main()
