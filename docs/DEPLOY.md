# Deploying to Cloudways staging

Written 2026-09-20. Revised 2026-09-22, **after the sequence ran against
staging**, to record what worked rather than what was planned.

State at the time of writing, 22 September:

- Database imported: 764 articles, 81 roles.
- `project-config/diff` clean, `php craft up` clean.
- Uploads rsynced: 4,327 files on the server. About 7.5 GB on the first run.
- Config diff clean after the import. Staging fully deployed.

Every block below is labelled **MacBook** or **Server**. The MacBook is the
machine running DDEV. It holds the local database, `web/uploads/archive-media`
and the Reggie bind. Run the steps in the order given. Several of them fail
harmlessly out of order, and two of them fail destructively.

---

## The rule for every block: check the prompt first

Before you paste a block, **look at the prompt and confirm which machine you are
on.** The Mac and the server both have `~/scvhistory`-shaped trees, `git` and
`php craft`, so a block pasted on the wrong machine will often run and do the
wrong thing without an error. The dump and import steps are the dangerous
ones. `gunzip | mysql` on the MacBook, or `ddev export-db` expected on the
server, is how a database gets overwritten.

If the prompt doesn't make it obvious, run:

```
hostname; pwd
```

The MacBook is `N8s-MacBook-2`. The server's prompt carries the Cloudways
application user and a path under `/home/1656314.cloudwaysapps.com/`.

---

## The facts that differed from the plan

These are the things the first revision of this file got wrong. Each was found
on 22 September while the steps were running.

| | Planned | Actual |
| --- | --- | --- |
| Server path | `/home/676057.cloudwaysapps.com/ufppzhwvbk` | **`/home/1656314.cloudwaysapps.com/ufppzhwvbk`** |
| Where dumps go | `~/` on the server | **`private_html/`** under the app path. The app user's home is read-only |
| `CRAFT_ENVIRONMENT` on the server | assumed `staging` | **was `production`**. Now set to `staging` |
| Branch checked out on the server | assumed `main` | **`templates-batch-9`** |
| The `curl` checks | anonymous | **need the Cloudways basic auth.** Staging answers 401 to everything without it |
| The rsync | whole tree | **excludes `_*/`**, which are Craft's image transform directories |

Two consequences follow from the path:

- **`.github/workflows/deploy.yml` `cd`'d into the `676057` path**, which
  does not exist, so the workflow can't have worked on this server. Fixed on
  22 September to the real path and branch, and left **disabled** until Nathan
  enables it. See "What this does not cover".
- `docs/DEPLOY-RUNBOOK.md` refers to the path as `~/public_html`, which is
  only right if the shell's home is the app directory. Use the full path.

In this file:

```
APP=/home/1656314.cloudwaysapps.com/ufppzhwvbk
```

---

## MacBook restart routine

After the MacBook restarts, DDEV and the Reggie bind are both down. Before any
**MacBook** block:

1. Open Docker Desktop and wait for it to report running.
2. Start DDEV:

   ```
   cd ~/scvhistory
   ddev start
   ```

3. Check that Reggie is mounted and visible inside the container:

   ```
   ls /Volumes/Reggie/SCVHistory | head -3
   ddev exec ls /mnt/reggie | head -3
   ```

   Both must list files. If the first is empty, plug the drive in and mount it,
   then `ddev restart`. The bind is set up at container start, so a drive that
   mounts after `ddev start` shows up as an empty `/mnt/reggie`.

---

## When the container writes files  — **MacBook**

Anything that imports images writes them from inside the DDEV container:
`import_mirror_images.php`, a control panel upload, a generated transform. The
host copy is what `rsync` ships to staging, so a write that does not reach the
host is a write that does not exist, and on 23 September 2026 eighteen of them
were lost in silence.

Three things to know, because none of them announces itself.

**`upload_dirs` is resolved against the docroot, not the project root.** The
docroot here is `web`, so the correct value is `uploads/archive-media`, and
`web/uploads/archive-media` makes DDEV mount the host directory on
`/var/www/html/web/web/uploads/archive-media`.

**A wrong path fails silently, by mounting somewhere nothing reads.** There is
no error. The bind mount is made, it is simply made in the wrong place, and the
real uploads directory is left to Mutagen instead of being shared directly.
`.ddev/mutagen/mutagen.yml` inherits the same wrong path in its ignore list, so
the directory is not excluded from syncing either. Both faults point the same
way and neither is visible from Craft.

**A container write to a synced path can be reverted with no error.** Mutagen
runs in `two-way-resolved` mode, where the host wins a conflict. With its
watcher in a problem state the change is not propagated, and at the next
reconcile the container's copy is replaced by the host's. Craft reports a
successful save, the database keeps the new metadata, and the bytes go back to
what they were. That is how eighteen image replacements were reported as done
while every file kept its old contents.

Before any apply that writes files:

```
ddev exec mount | grep archive-media     # must show /var/www/html/web/uploads/archive-media
ddev mutagen status                      # must not say "problems"
```

If the mount is on the doubled path, fix `upload_dirs` in `.ddev/config.yaml`
and `ddev restart`; `mutagen.yml` is `#ddev-generated` and corrects itself.

---

## What the deploy carries, and how

| Change | Route |
| --- | --- |
| Roles section, `roles` field, `roleWikidataId`, `roleMatch` | config, step 5 |
| Role entries (the vocabulary) and person-to-role relations | database, step 4 |
| Theme category group, `articleThemes` field | config, step 5 |
| Theme terms, era retitles and partition | database, step 4 |
| `recordProvenance`, `orgType`, `schoolLevel` | config, step 5 (values: database) |
| Identifier fields: `cdsCode`, `ncesId`, `ein`, `viafId`, `nrhpReference` | config, step 5 (values: database) |
| Asset object fields: `creator`, `dateAsPrinted`, `courtesyOf`, `rightsHolder`, `license`, `legacySourcePath` | config, step 5 (values: database) |
| EDTF fields | config, step 5 (values: database) |
| `/data` exports (`web/data/*.json`, `.csv`, `.geojson`) | git, built locally in checklist step 3 |
| `/media/<id>` pages | git (`config/routes.php`, `templates/media`). Files go in step 6 |
| `reviewstore` module | git. Autoloaded by `composer install` in step 3 |

**Review is local only.** The `reviewstore` controller answers 403 to every
request unless `CRAFT_ENVIRONMENT` is `dev`, and a write also needs an admin
session. With the server on `production` or `staging` that holds either way.
The decisions files stay in git and are never written on the server.

---

## Before the dump: the local checklist  — **MacBook**

Run the restart routine first if the MacBook has restarted.

### 1. Settle the control panel

- **Event #875 "Northridge Earthquake"** had Northridge Recovery as its only
  era. Set it to **Mall & Growth Era (1994–2009)** before deleting the old era.
- Delete the two demoted eras, **St. Francis Dam Era (1926–1928)** (#163) and
  **Northridge Recovery (1994–2000)** (#169).
- Delete the **Roles Probe** section.

### 2. Regenerate the data model

```
cd ~/scvhistory
ddev craft exec "eval(file_get_contents('scripts/import/generate_data_model.php'))"
ddev craft exec "eval(file_get_contents('scripts/import/check_data_model.php'))"
```

### 3. Rebuild the exports

```
ddev craft exec '$EXPORT_APPLY = true; eval(file_get_contents("scripts/import/build_data_exports.php"));'
ddev craft exec '$COLLECTIONS_APPLY = true; eval(file_get_contents("scripts/import/build_collections_export.php"));'
```

### 4. Render check

```
ddev craft exec "eval(file_get_contents('scripts/import/check_render.php'))"
```

### 5. The deploy commit

```
git status --short config/project web/data docs
git add config/project web/data docs/DATA-MODEL.md scripts/import/APPLIED.log
git commit -m "Pre-deploy: config, exports and data model"
git push
git status --short
```

The last `git status` must print nothing. **Anything uncommitted under
`config/project` means stop.**

---

## The sequence

### 1. Merge to main  — **MacBook**

```
cd ~/scvhistory
git checkout main
git pull
git merge --ff-only templates-batch-9
git push origin main
git checkout templates-batch-9
```

On 22 September main and `templates-batch-9` were level after this (`0 0`).

### 2. Dump the database  — **MacBook**

```
cd ~/scvhistory
D=$(date +%Y%m%d)
ddev export-db --gzip=false --file=/tmp/scvh-$D.sql
gzip -9 /tmp/scvh-$D.sql
scp /tmp/scvh-$D.sql.gz <user>@<host>:/home/1656314.cloudwaysapps.com/ufppzhwvbk/private_html/
```

**The target is `private_html/`, not `~/`.** The app user's home is read-only
and the `scp` into it fails. `private_html` sits beside `public_html` and is
not served.

### 3. Prove `/review/` is blocked  — **MacBook**, before the pull

The pull publishes whatever `web/review` holds that day. On 24 September that
included `photo-links.json`, 1.2 MB listing 8,359 names, and the only thing in
front of it was the site-wide basic auth: `web/.htaccess` is inert on this host
and `server.nginx-vhosts.conf` carried no `/review/` rule.

```
HOST=https://phpstack-1656314-6593553.cloudwaysapps.com \
AUTH=user:password scripts/deploy/check_review_exposure.sh
```

It must exit 0. Exit 2 means it could not prove anything — usually the
credentials were left out, and a 401 is not a pass. Exit 1 means a working file
is served to anyone holding the staging password: fix it before pulling, with
the location block in docs/DEPLOY-RUNBOOK.md section 5, added through
**Application → Application Settings → Nginx Settings** in the Cloudways panel.

### 4. Check the tree and pull  — **Server**

```
ssh <user>@<host>
APP=/home/1656314.cloudwaysapps.com/ufppzhwvbk
cd $APP/public_html
grep -E '^CRAFT_(ENVIRONMENT|DB_SERVER|DB_TABLE_PREFIX)=' .env
git branch --show-current
git status --short
```

Check three things before going on:

- **`CRAFT_ENVIRONMENT=staging`.** On 22 September it said `production`, left
  over from the first deploy on the 19th, and was changed to `staging` by hand.
  `dev` here would open the review store to anyone. `CRAFT_DB_SERVER` must be
  `127.0.0.1` and `CRAFT_DB_TABLE_PREFIX` must be `scvh`.
- **The branch.** It was `templates-batch-9`, not `main`, because the first
  deploy (DEPLOY-RUNBOOK.md §1) checked that branch out. Pull the branch that
  is checked out. Don't pull `main` into a `templates-batch-9` checkout.
  While the two are level it makes no difference to the code, but it makes a
  merge commit on the server and dirties the history there.
- **`git status` empty.**

Then:

```
git pull
composer install --no-dev --optimize-autoloader
```

### 5. Import the database  — **Server**

Take a backup first, because this overwrites everything. It goes in
`private_html` for the same reason as the dump.

```
APP=/home/1656314.cloudwaysapps.com/ufppzhwvbk
cd $APP/private_html
mysqldump -h 127.0.0.1 -u <db_user> -p <db_name> | gzip -9 > before-import-$(date +%Y%m%d).sql.gz
gunzip -c scvh-<date>.sql.gz | mysql -h 127.0.0.1 -u <db_user> -p <db_name>
```

The database comes before the config on purpose. The imported database already
carries the schema the committed config declares.

### 6. Migrations and config, expecting nothing  — **Server**

```
cd /home/1656314.cloudwaysapps.com/ufppzhwvbk/public_html
php craft up
php craft project-config/diff
```

On 22 September both were clean: `craft up` had nothing to run and the diff
reported no changes. **If the diff shows anything, stop and report it before
applying.** A difference means the dump and the commit don't match.

### 7. Move the images  — **MacBook**

```
cd ~/scvhistory
rsync -avz --partial --progress \
  --exclude='_*/' \
  web/uploads/archive-media/ \
  <user>@<host>:/home/1656314.cloudwaysapps.com/ufppzhwvbk/public_html/web/uploads/archive-media/
```

- **The first run is about 7.5 GB.** Measured on 22 September: 4,319 files,
  7.66 GB (7.14 GiB). Staging held 4,327 files after it, the extra eight being
  files the server already had. Later runs send only what changed. Six of the
  7.5 GB are the `_large` and `_orig` enlarge masters; see "The masters" below.
- **`--exclude='_*/'`** skips Craft's image transform directories (`_1200x800_crop_center-center_…/`
  and the like). They are generated per server and rebuilt on demand. They
  are small (748 files, 0.02 GB on 22 September), so the exclude is about not
  shipping stale transforms, not about size.
- Trailing slashes on both paths. Without them rsync nests the directory
  inside itself.
- `--partial` lets a dropped connection resume. Re-running the same command
  picks up where it stopped.
- Do **not** add `--delete`.

#### The masters

1,769 files in `web/uploads/archive-media/legacy` are `_large` or `_orig`
masters, 6.4 GB of the 7.5. They are not idle copies: 1,765 of them are the
files the magnifier opens (`templates/_data/enlarge.json`), so moving them off
the web root without replacing them would turn the magnifier off again. The
median is 3 MB. The problem is the top: 50 of them are over 10 MB (789 MB
together), up to 71 MB and 9,600 pixels wide for `lw3808_large.jpg`, and that
is what a reader's browser downloads on a click.

The fix is not in this deploy. Keep those masters on Reggie, where they
already are, and serve a web derivative of each as the enlarge target
instead. Four masters are not referenced by `enlarge.json` at all
(`lw3775_large.jpg`, `lw3624bullocks_large.jpg`, `lw2377e_large.jpg`,
`lw2554b_large.jpg`, 8.7 MB) and can simply stay off the web root.

### 8. Clear and verify  — **Server**, then **MacBook**

```
cd /home/1656314.cloudwaysapps.com/ufppzhwvbk/public_html
php craft clear-caches/all
```

Then from the MacBook. **Every request needs the basic auth**, because staging
answers 401 to everything without it, and a 401 on every line looks like the
checks failing. Keep the credentials out of shell history by reading them in:

```
read -s "AUTH?user:password for staging basic auth: "; echo
S=https://phpstack-1656314-6593553.cloudwaysapps.com
curl -u "$AUTH" -sI $S/ | head -1
curl -u "$AUTH" -s  $S/articles/chapter-5-tribal-relics | grep -c 'rec-band'
curl -u "$AUTH" -sI $S/review/ | head -1
curl -u "$AUTH" -s -o /dev/null -w '%{http_code}\n' -X POST $S/actions/reviewstore/decisions/save
curl -u "$AUTH" -s -o /dev/null -w '%{http_code}\n' $S/data/collections.json
```

(`read -s "VAR?prompt"` is the zsh form. In bash it's `read -sp 'prompt' AUTH`.)

Expect `200`, `1`, `403` or `404`, `403`, `200`. The fourth is the review store
refusing a write off `dev`. Anything else there means `CRAFT_ENVIRONMENT` is
wrong.

### 9. The `/review/` guard  — **Server**, once

See DEPLOY-RUNBOOK.md §5. Use the panel's **Application → Application Settings
→ Nginx Settings** so the block survives a stack update.

---

## What this does not cover

- **The workflow.** `.github/workflows/deploy.yml` now has the real path and
  branch, a preflight (branch, clean tree, `CRAFT_ENVIRONMENT=staging`), a
  database backup, then `craft up` and a cache clear, with key authentication.
  It carries code and schema only, never the database or uploads. It is
  **disabled** twice over: manual trigger only, and `if: false` on the job.
  Enabling it means adding the `CLOUDWAYS_SSH_KEY` secret, deleting `if: false`
  and restoring the push trigger, as the file's header says.
- **Rolling back.** The backup in step 4, in `private_html/`, is the rollback
  for content. For code it is `git checkout <previous sha>` on the server and
  re-running steps 3, 5 and 7.
- **The mirror.** Reggie is bound into DDEV on the MacBook only. The server
  has no such mount, which is why images are imported locally and rsynced.
