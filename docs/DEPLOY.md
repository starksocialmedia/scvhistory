# Deploying to Cloudways staging

Written 2026-09-20, revised 2026-09-22 against everything committed since.
**Nothing in this file has been run.** It is the sequence, in order, with the
machine each command belongs to.

Every command is labelled **MacBook** or **Server**. The MacBook is the machine
running DDEV: it holds the local database, `web/uploads/archive-media` and the
Reggie bind. Run the steps in the order given; several of them fail harmlessly
out of order and two of them fail destructively.

---

## What is wrong with the deploy today

`.github/workflows/deploy.yml` is two lines of work:

```yaml
on: push: branches: [main]
script: |
  cd /home/676057.cloudwaysapps.com/ufppzhwvbk/public_html
  git pull origin main
```

Six things are wrong with it, in order of how much damage they do.

1. **It pulls code and never applies the schema.** Craft keeps fields, entry
   types and section settings in `config/project/*.yaml`, and pulling those
   files changes nothing until `project-config/apply` runs.

2. **There is no content.** Every record lives in the local database. A code
   deploy makes the templates newer and the content no fresher.

3. **There are no images.** `web/uploads/archive-media` is gitignored, correctly,
   because it is binary: 5,065 files and 7.2 GB on 22 September, none of which
   git will ever carry.

4. **It authenticates with a password.** `CLOUDWAYS_PASSWORD` in a GitHub secret,
   used for SSH. A key is what this should use.

5. **Nothing checks whether it worked.** `git pull` on a dirty server tree fails
   and the step still reports success.

6. **It has not fired.** It triggers on push to `main`, and main was last touched
   17 September. Check how far behind it is with:

   ```
   git rev-list --left-right --count main...templates-batch-9
   ```

   On 22 September, at 79e6d25, that was `0 223`: nothing on main that is not
   on the branch, so the merge is a fast-forward.

---

## What the deploy carries, and how

Every change since this runbook was first written travels by one of three
routes. Nothing needs a route of its own.

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
| `/media/<id>` pages | git (`config/routes.php`, `templates/media`); files by step 6 |
| `reviewstore` module | git; autoloaded by `composer install` in step 3 |

**Review is local only.** The `reviewstore` controller answers 403 to every
request unless `CRAFT_ENVIRONMENT` is `dev`, and a write also needs an admin
session. The review screens load `readonly.js`, which sees the 403, shows a
read-only banner, and reads the tracked `*-decided.json` files directly. The
decisions files stay in git and are never written on the server, so the
server's tree stays clean for the next `git pull`.

---

## Before the dump: the local checklist  — **MacBook**

Run in this order. Each step changes what the next one reads.

### 1. Settle the control panel

- **Event #875 "Northridge Earthquake"** carries Northridge Recovery as its only
  era. Set it to **Mall & Growth Era (1994–2009)** first, or deleting the old era
  leaves it with none.
- Delete the two demoted eras: **St. Francis Dam Era (1926–1928)** (#163, no
  entries) and **Northridge Recovery (1994–2000)** (#169). Their subjects live on
  as the themes St. Francis Dam and Northridge Earthquake.
- Delete the **Roles Probe** section. It holds no entries and nothing refers to
  it; it is left over from probing the roles schema.

### 2. Regenerate the data model

The section and the eras just deleted are named in it.

```
cd ~/scvhistory
ddev craft exec "eval(file_get_contents('scripts/import/generate_data_model.php'))"
ddev craft exec "eval(file_get_contents('scripts/import/check_data_model.php'))"
```

### 3. Rebuild the exports

`web/data` is built from the local database and committed. Rebuild it after
the last change to the data and before the deploy commit, or the server serves
exports from an older state than its own records.

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
`config/project` means stop**: those files declare the fields the templates are
about to expect.

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

`--ff-only` is deliberate. If it refuses, main has moved and this runbook's
assumption is stale; stop and look rather than making a merge commit.

Pushing main fires the workflow, which will `git pull` on the server. That is
harmless and insufficient; the steps below are the rest of it.

### 2. Dump the database  — **MacBook**

```
cd ~/scvhistory
ddev export-db --gzip=false --file=/tmp/scvh-$(date +%Y%m%d).sql
gzip -9 /tmp/scvh-$(date +%Y%m%d).sql
scp /tmp/scvh-$(date +%Y%m%d).sql.gz <user>@<host>:~/
```

### 3. Pull and install  — **Server**

```
ssh <user>@<host>
cd /home/676057.cloudwaysapps.com/ufppzhwvbk/public_html
grep -E '^CRAFT_(ENVIRONMENT|DB_SERVER|DB_TABLE_PREFIX)=' .env
git status --short
git pull origin main
composer install --no-dev --optimize-autoloader
```

The `grep` must show `CRAFT_ENVIRONMENT=staging`, `CRAFT_DB_SERVER=127.0.0.1`
(not the public IP) and `CRAFT_DB_TABLE_PREFIX=scvh`. **`dev` here would open
the review store to anyone**, as well as turning on dev mode. `git status` must
be empty before the pull.

### 4. Import the database  — **Server**

Take a backup first, because this overwrites everything:

```
cd /home/676057.cloudwaysapps.com/ufppzhwvbk/public_html
mysqldump -u <db_user> -p<db_pass> <db_name> | gzip -9 > ~/before-import-$(date +%Y%m%d).sql.gz
gunzip -c ~/scvh-<date>.sql.gz | mysql -u <db_user> -p<db_pass> <db_name>
```

The database comes before the config on purpose. The server's own database is
from September; applying config to it would push the whole batch's schema
through a database that is about to be replaced. The imported one already
carries the schema the committed config declares.

### 5. Apply the config, expecting nothing  — **Server**

```
php craft project-config/diff
php craft project-config/apply
```

The imported database was dumped from the same state the committed config
describes, so **the diff should report no changes and the apply should be a
no-op. If the diff shows anything, stop and report it before applying.** A
difference means the dump and the commit do not match, which is checklist step 5
not having been clean.

### 6. Move the images  — **MacBook**

```
cd ~/scvhistory
rsync -avz --partial --progress \
  web/uploads/archive-media/ \
  <user>@<host>:/home/676057.cloudwaysapps.com/ufppzhwvbk/public_html/web/uploads/archive-media/
```

Trailing slashes on both paths: without them rsync nests the directory inside
itself. `--partial` lets a dropped connection resume. Do **not** add `--delete`;
the server may hold files this machine does not.

### 7. Clear and verify  — **Server**, then anywhere

```
cd /home/676057.cloudwaysapps.com/ufppzhwvbk/public_html
php craft clear-caches/all
```

Then:

```
S=https://phpstack-1656314-6593553.cloudwaysapps.com
curl -sI $S/ | head -1
curl -s  $S/articles/chapter-5-tribal-relics | grep -c 'rec-band'
curl -sI $S/review/ | head -1
curl -s -o /dev/null -w '%{http_code}\n' -X POST $S/actions/reviewstore/decisions/save
curl -s -o /dev/null -w '%{http_code}\n' $S/data/collections.json
```

Expect `200`, `1`, `401` or `403`, `403`, `200`. The fourth is the review store
refusing a write on staging; anything else there means `CRAFT_ENVIRONMENT` is
wrong.

### 8. The `/review/` guard  — **Server**, once

Cloudways serves with Nginx. Add to the app's Nginx config, inside the `server`
block, before the `location /`:

```
location ^~ /review/ {
    auth_basic "SCVHistory working files";
    auth_basic_user_file /home/676057.cloudwaysapps.com/ufppzhwvbk/.htpasswd-review;
    try_files $uri $uri/ =404;
}
```

Create the password file once:

```
htpasswd -c /home/676057.cloudwaysapps.com/ufppzhwvbk/.htpasswd-review review
```

This guards the static review files. It does not cover `/actions/`, which is
why the review store refuses off dev in code rather than relying on this.

---

## What this does not cover

- **Rolling back.** The backup in step 4 is the rollback for content. For code
  it is `git checkout <previous sha>` on the server and re-running steps 3 and 7.
  There is no scripted rollback and there should be one.
- **The mirror.** Reggie is bound into DDEV on the MacBook at `/mnt/reggie`
  (`/Volumes/Reggie/SCVHistory`, read-only). The server has no such mount and will
  not get one, which is why images are imported into the volume locally and
  rsynced, rather than served from the drive.
- **Fixing the workflow.** Steps 3 to 7 could be the workflow instead of one
  `git pull`. That is worth doing and is a separate change, because a deploy
  script that also imports a database is a deploy script that can destroy one.
