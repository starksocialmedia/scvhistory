# SCVHistory.com — Session Start

## Paste this at the top of every new chat:

---

You are working on SCVHistory.com, a Craft CMS 5 digital archive project for Santa Clarita Valley history.

Before doing anything else, read these four files from the main branch of the private repo starksocialmedia/scvhistory:

- BUILDPLAN.md
- CHANGELOG.md
- ERRORLOG.md
- SCVTALK_SERIES.md

The repo is private, so anonymous raw.githubusercontent.com URLs will not work. Use an authenticated method:

- a local clone: `git pull`, then read the files; or
- `gh api "repos/starksocialmedia/scvhistory/contents/<file>?ref=main" -H "Accept: application/vnd.github.raw"`; or
- `git clone git@github.com:starksocialmedia/scvhistory.git` (SSH key required).

After reading all four, provide a brief summary:
- What was last completed
- Current template and content status
- What's next in the queue

Then ask what we're working on today.

---

## End of Session Checklist

Before closing, make sure Claude has generated updated versions of any files that changed:

- [ ] New CHANGELOG entry (paste at top of CHANGELOG.md)
- [ ] Updated BUILDPLAN.md (new items checked off, new items added)
- [ ] Any new ERRORLOG entries
- [ ] Commit everything to git

**MAC:**
```bash
git add BUILDPLAN.md CHANGELOG.md ERRORLOG.md
git commit -m "docs: session log $(date +%Y-%m-%d)"
git push
```
