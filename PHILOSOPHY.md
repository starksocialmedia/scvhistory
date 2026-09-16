# SCVHistory.com: Goals and Design Philosophy

Read this before making any decision about structure, content, or design. When a task seems to conflict with this document, stop and ask Nathan. This file explains *why*; BUILDPLAN.md and DATA_MODEL.md explain *what*.

---

## 1. What we are building

SCVHistory.com is nearly 30 years of Santa Clarita Valley history, researched and written by Leon Worden (journalist, historian). For most of that time it was a hand-built site maintained by one person. The rebuild turns it from a personal website into a **permanent community resource**, stewarded by a nonprofit (Santa Clarita History Archives), launching in **2027 for the site's 30th anniversary**.

The North Star: **a world-class digital archive of a regional history**, with three goals held together:

1. **Academic credibility.** A model digital humanities project that researchers, students, and CSUN faculty can cite with confidence.
2. **Community adoption.** Useful and inviting to the general public, schools, and longtime residents who want to see their valley's story and contribute to it.
3. **Award-worthy design.** It should look and feel like a serious archive, not a blog.

Audiences: general public, students, researchers, and the CSUN graduate and digital humanities community.

---

## 2. Core principles

### 2.1 The data outlives the platform

We are building for the next 50 years of SCV history. Craft CMS is today's interface to the data, not the data itself.

- Three layers: **Data** (structured, relational, open; this should rarely change shape) → **API** (the contract) → **Presentation** (Craft templates today; apps or anything else later).
- Content must stay exportable as clean structured data. Import artifacts (`inventory.json`, `entities.json`, `canonical_entities.json`, `article_relations.json`) are committed to git as permanent records.
- Prefer open standards to custom inventions (see 2.8).

### 2.2 Interconnection is the whole point

The legacy site is a huge collection of standalone pages. The value of the rebuild is **connecting them**: people ↔ places ↔ organizations ↔ events ↔ articles ↔ photos ↔ sources. Every article should get smarter as the archive grows. A reader on an article about the 1842 gold discovery should be one click from Francisco Lopez and Placerita Canyon.

### 2.3 The entity threshold: records for what recurs

Not every name deserves a record. Nathan's rule:

> **A full record (Person, Place, Organization, etc.) is created only when the entity has a meaningful, recurring role in SCV history.** A passing mention stays in the body text (or a tag). It does not get a record.

Being named in an article is not enough. Over-creating stub records is a known failure mode; it has happened before and Nathan rejected it. When in doubt, list the candidate under "Questions for Leon" instead of creating it.

### 2.4 Entities first, articles second

Canonical entities are imported and deduplicated **before** any article. Articles then link to entities that already exist. Never import articles first and backfill relations later.

### 2.5 Relationships carry meaning

A relationship records **why** two things connect, not just that they do: a person *witnessed*, *founded*, *was named after*, *is parent of*. Relationships are explicit and bidirectional (e.g., Rodolfo parentOf Dante, Dante childOf Rodolfo). Structured fields beat free text: the Roles Matrix (title, organization, start year, end year, primary role) replaced a plain-text occupation field for this reason.

### 2.6 Honesty about uncertainty

History is messy. The archive shows what we know and how well we know it.

- **Dates carry precision**: exact, month, year, decade, circa, range, or unknown. Never turn "c. 1920" into "1920-01-01."
- **Claims carry confidence**: confirmed (primary source), probable (strong secondary), uncertain (limited evidence), disputed (conflicting sources).
- **Sources are cited.** Primary sources linked wherever they exist.
- **Never invent anything**: no fabricated dates, facts, identities, or linked-data URIs. Unknown is a valid value. Unverified URIs are marked `NEEDS_VERIFICATION`.

### 2.7 Leon's work and voice are preserved

The archive's credibility is inseparable from Leon's. Migration is **faithful**, not a rewrite.

- Do not rewrite, summarize over, or "improve" Leon's articles during migration. Cleanup means removing broken markup and cruft, not editing prose.
- Keep his bylines, original titles, fine print, and webmaster notes.
- Leon is the authority on who and what an entity is. Ambiguities go to him.
- Planned public pages (Editorial Standards, Corrections Policy, visible "last revised" dates) exist to make that credibility visible.

### 2.8 Permanence and citability

Researchers cite URLs. A broken link destroys credibility years later.

- Entry slugs are permanent once published. Changes require redirects.
- Every migrated entry keeps its **legacy URL** so old links and citations 301 to the new page.
- Leon's existing systems are structured data; preserve them rather than replace them:
  - Legacy article URL prefixes (`lw`, `sg`, `wr`, `ws`, `wy`) encode author, date, batch, and sequence
  - Photo IDs are a two-letter donor prefix + 4-digit number (e.g., `LW0891`); the donor key (`key.htm`) becomes a proper contributor/source registry, not a lookup table
- Standards used: Schema.org JSON-LD, Dublin Core, Wikidata, Getty AAT and TGN, LCSH, IIIF.

### 2.9 Indigenous history, prominently and respectfully

Tataviam, Chumash, Tongva, Serrano, Kitanemuk, and Vanyume history gets prominent, careful treatment. Follow `TATAVIAM_AUDIT.md` and the CARE Principles for Indigenous data governance. Taxonomy for these peoples is developed in consultation with community representatives. Do not guess, generalize, or collapse distinct peoples together.

### 2.10 Humans decide; AI assists

AI speeds up the work; it does not make editorial calls.

- Entity extraction, deduplication, and entity linking produce **suggestions** that a person approves.
- Anything identifying a real person (e.g., face matching in yearbooks) is editor-confirmed only.
- Decisions belong to Nathan and Leon. Agents propose, document, and ask.

### 2.11 Build the core before the extras

Many features are planned for later phases: inline entity tooltips, On This Day, shared-birthday, named-places map, IIIF yearbook reader, haunted places, knowledge-graph visualizations. They depend on a clean core. **The current priority is the content model and moving content in.** Do not build ahead.

### 2.12 Reusable structure, SCV-specific content

The data model, import pipeline, and template patterns could someday become an open starter kit for other regional archives. Keep structure generic where it costs nothing; keep SCV content and taxonomy terms separate from reusable code.

---

## 3. Design philosophy

Nathan is the Creative Director. Agents follow the design system; they do not invent new visual direction.

- **Archival-grade typography.** Cormorant Garamond for headings, Inter for body and UI.
- **Palette with historical resonance:** Navy `#1a2744`, Gold `#b8860b`, Cream `#faf6ef`, Warm Gray `#6b6560`.
- **Photography first.** Historical photos presented with care: credit, source, caption, and ID always shown.
- **Era-based storytelling.** Time is a primary way to explore the valley.
- **No illustrated or SVG portraits of real people.** Typographic and ornamental treatments are for collection mastheads only.
- **Mobile-first and fast.**
- **Accessible (WCAG 2.1 AA minimum).** Semantic landmarks (`main`, `article`, `nav`, `aside`), keyboard-navigable interactive elements, transcripts required for all audio and video. Check gold text contrast; it may fail AA on white.

---

## 4. How the environments are used

- **Code and schema:** built locally in DDEV → committed to git → applied on Cloudways with `project-config/apply`.
- **Content:** lives on Cloudways (production). Local is for code, not content.
- **Large files** (TIFFs, yearbooks, video): Archive.org, which provides permanent storage, IIIF, and OCR. Craft stores the metadata, relations, and rights.
- **Agents never touch Cloudways.** They build and test import scripts and artifacts; Nathan runs imports on production.

## 5. The import pipeline (already designed)

1. **Inventory:** scan the legacy site on Jordy → `inventory.json` (every file catalogued)
2. **Entity extraction:** find entity mentions → `entities.json`
3. **Deduplication with human review** → `canonical_entities.json`
4. **Craft import:** entities first, then articles, with relations populated from the canonical entity list → `article_relations.json`

## 6. What "getting lost" looks like (avoid these)

- Creating records for one-off mentions
- Importing articles before their entities exist
- Faking date precision or confidence
- Inventing facts, identities, or URIs
- Editing Leon's prose
- Changing slugs or dropping legacy URLs
- Relitigating settled decisions in BUILDPLAN.md, DATA_MODEL.md, or CHANGELOG.md
- Adding visual styles outside the design system
- Building later-phase features before the core is done
- Treating the live site as something to crawl instead of using the copy on Jordy

## 7. Where the details live

| File | Purpose |
|---|---|
| `PHILOSOPHY.md` | Why (this file) |
| `BUILDPLAN.md` | Roadmap, status, decisions log |
| `DATA_MODEL.md` | Every entry type, field, relation, taxonomy, and open questions |
| `TAXONOMY_STANDARDS.md` | LCSH/AAT/TGN alignment, CARE, controlled vocabulary rules |
| `TATAVIAM_AUDIT.md` | Indigenous cultural audit |
| `craft-cp-field-checklist-2026-04-15.md` | Field-by-field build checklist for the Craft CP |
| `CONTENT-MODEL.md` | Model as built vs. as planned, and proposed gaps |
| `INVENTORY.md` | What is in the legacy site on Jordy |
| `COLLECTIONS-HUB.md`, `PLACES-HUB.md`, `PLACE-RELATIONS.md`, `WAR-MEMORIAL.md` | Section-specific plans and decisions |
| `IIIF_SESSION_PROMPT.md` | IIIF work (later phase) |
| `grave-audit-export.md` | Grave audit data export |
| `CHANGELOG.md` | What happened, session by session |
| `ERRORLOG.md` | Known errors and patterns to avoid |
| `HANDOFF.md` | Current tasks for agents |
| `AGENTS.md` | Rules for AI agents |
