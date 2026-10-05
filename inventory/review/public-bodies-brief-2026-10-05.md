# Research brief: the missing public bodies, 5 October 2026

For the research agents. Read only: nothing is written to the database, no git, no edits to existing files. Write only your
own files under inventory/review/ and inventory/news/public-bodies-2026-10-05/<your-batch>/ (with manifest.json: file, url or
mirror path, read date, sha256, what).

Read first: inventory/review/civic-audit-2026-10-05.md (what is on /civic and why these are missing), docs/PROFILES.md
(sourcing rules: Wikipedia is a lead only and labelled; never invent a URL or citation; a "not found" records what was
searched; living people public life only), and the records for the City (#394) and SCV Water (#402) as models of a sourced
body record (read-only: ddev craft exec "echo craft\elements\Entry::find()->id(402)->one()->body;" and its footnotes).
Nathan, 5 October 2026: "source each one rather than working from general knowledge ... those are the kind of facts that feel
known and turn out wrong." Every fact in a draft rests on a document you read and saved: the body's own pages, its charter,
annual reports, board minutes, the County's or the State's records, contemporary newspapers. The legacy site's pages are on
the mounted mirror /Volumes/Reggie/SCVHistory/scvhistory.com (Python over bytes decoded cp1252, never the shell's grep; do not
crawl scvhistory.com live). Reading Wayback Machine captures is fine.

Output: inventory/review/public-bodies-<batch>-2026-10-05.json and .md. The JSON is a list of proposed organization records:
  { "title": "...", "orgType": "government" | "school" | "nonprofit" | "other", "schoolLevel": "" | "district" | "college",
    "orgLevel": "valley" | "county" | "state" | "", "civicRole": "governs" | "represents" | "polices" | "advises" | "administers" | "none",
    "parentTitle": "an existing organization's title, or ''", "dateFounded": "as written", "dateFoundedEdtf": "",
    "dateDissolved": "", "dateDissolvedEdtf": "", "orgAddress": "", "orgWebsite": "",
    "body": "plain prose, every sentence with [n] note markers written as [1][2], never [1, 2]; no em dashes; what it is and does
             in this valley first, then its history here; no praise",
    "footnotes": ["note text in the archive's citation style, quoting the words the sentence rests on"],
    "editorNotes": [{"heading": "...", "note": "..."}],
    "relatedExisting": ["existing archive records it should link to, with ids"],
    "openQuestions": ["anything Nathan must decide"] }
No note may name the archive's process (not "mirror", "inventory/", "import", "manifest", "SHA", "script", "searched <date>",
"could not be read", "Claude"). Final message: a short table of the records proposed, with the key facts and their sources.
