# Cloudways deployment runbook

SCVHistory.com is live on Cloudways staging at
**https://phpstack-1656314-6593553.cloudwaysapps.com/**

This file records what was actually done, not what was planned. The first
deployment was carried out by hand on 19 September 2026. Everything below either
happened or is the standing rule that follows from it.

Labels: **iMac** is the local machine, **Server** is the Cloudways SSH session.

---

## 1. What was done, in order

The repository cloned to `~/public_html` on the server, on `templates-batch-9`:

```
Server
cd ~/public_html
git clone git@github.com:starksocialmedia/scvhistory.git .
git checkout templates-batch-9
composer install --no-dev --optimize-autoloader
```

`.env` written by hand with production values, the `scvh` table prefix and the
same `CRAFT_SECURITY_KEY` as local. The database imported from a local dump, the
assets rsynced from `web/uploads`, then:

```
Server
php craft up
php craft project-config/apply
php craft clear-caches/all
```

The webroot is `public_html/web`, which is the setting everything else depends
on. Verified after the fact and it is correct: `/.env` returns 403 and
`/scripts/import/...`, `/bootstrap.php`, `/config/db.php` and `/craft` all
return 404. See section 5.

### The two things that went wrong

**`CRAFT_DB_SERVER` must be `127.0.0.1`, not the server's public IP.** With the
public address Craft cannot connect: MySQL on Cloudways listens on the loopback
and the public address is either refused or silently firewalled. The error does
not say so.

```
CRAFT_DB_SERVER=127.0.0.1
```

**`CRAFT_DB_TABLE_PREFIX=scvh` must be set.** Without it Craft looks for
unprefixed tables and reports the `info` table missing, which reads like a
corrupt or empty database rather than a configuration mistake. Note two things
about the value. The variable is `CRAFT_DB_TABLE_PREFIX` on the server while the
local `.env` uses the older `DB_TABLE_PREFIX`; Craft accepts both spellings and
the mismatch is easy to copy past. And the value is `scvh` with no trailing
underscore: Craft appends the underscore itself, so `scvh_` here produces
`scvh__info`.

```
CRAFT_DB_TABLE_PREFIX=scvh
```

### The rest of the .env

| Key | Local | Production |
|---|---|---|
| `CRAFT_ENVIRONMENT` | `dev` | `production` |
| `PRIMARY_SITE_URL` | `https://scvhistory.ddev.site/` | the Cloudways domain, trailing slash |
| `CRAFT_SECURITY_KEY` | *a value* | **the same value, character for character** |
| `LEGACY_HOST` | `https://scvhistory.com` | unchanged: this is the old site, not this one |
| `CRAFT_DB_SERVER` | `db` | `127.0.0.1` |
| `CRAFT_DB_TABLE_PREFIX` | `scvh_` | `scvh` |
| `CRAFT_DB_DATABASE` / `_USER` / `_PASSWORD` | `db` | from Cloudways, Application → Access Details |
| `CRAFT_DEV_MODE` | `true` | `false` |
| `CRAFT_ALLOW_ADMIN_CHANGES` | `true` | `false` |
| `CRAFT_DISALLOW_ROBOTS` | `true` | `false` |

`CRAFT_SECURITY_KEY` is the one that fails quietly. Craft encrypts stored values
with it and the dump carries them already encrypted. A fresh key boots, renders,
and returns garbage on read, with no repair short of re-entering every affected
value by hand.

`CRAFT_DISALLOW_ROBOTS` is the one that is easy to miss. It is `true` locally so
the DDEV site is not indexed. Shipped true, the live site tells search engines
not to index it and nothing else looks wrong.

---

## 2. Every deploy from here

```
Server
cd ~/public_html
git pull
composer install --no-dev --optimize-autoloader   # only if composer.lock moved
php craft project-config/apply
php craft clear-caches/all
```

That is the whole of it. No database, no assets, no dump.

---

## 3. Which way data flows

**Schema goes up. Content does not move. Both directions are one-way.**

```
   SCHEMA                            CONTENT
   local control panel               local imports
        |                                 |
   config/project.yaml               local database
        |                                 |
      git push                      dump + rsync, ONCE, at first deploy
        |                                 |
      git pull                            v
   project-config/apply            production, a copy for review
```

**Local is where imports run.** The scripts under `scripts/import/` read the
inventory files, which are large and local, and they write to the local
database. Nothing in that pipeline runs on the server.

**Production is a copy for review.** It exists so the work can be looked at on a
real domain by people who are not running DDEV. It is refreshed by repeating the
first deployment: a fresh dump and rsync from local, deliberately, not
incrementally.

### What breaks if someone edits a record on production

The edit is lost, silently, at the next content refresh, because that refresh is
a whole-database import from local and it overwrites everything. There is no
merge and nothing warns anybody.

Worse, it can be lost without a refresh. An import script run locally against
the same record writes the local value, and the next dump carries it up. The
person who made the production edit sees their work disappear and has no way to
tell whether it was the refresh, a script, or something else.

So: **do not edit records on production.** Fix it locally and push the content up
again. `CRAFT_ALLOW_ADMIN_CHANGES=false` stops schema being changed there but it
does not stop content editing, and nothing does; this is a rule rather than a
control.

If production ever does become the place records are edited, that is a real
change and this section has to be rewritten first, with the pull direction
(`php craft db/backup` on the server, `ddev import-db` locally) made the normal
direction rather than the exception.

---

## 4. Checking a deployment worked

```
Server
cd ~/public_html
php craft exec "echo Craft::\$app->getSites()->getPrimarySite()->getBaseUrl();"
php craft exec "eval(file_get_contents('scripts/import/check_render.php'))"
```

The first must print the Cloudways domain. If it prints `scvhistory.ddev.site`
then `PRIMARY_SITE_URL` did not take and every URL the site emits is wrong.

The second walks one page per section, entry type and category group and asserts
200, no error signature, a `<title>` and JSON-LD that parses. It builds its URLs
from the primary site, so it checks the same thing a second way.

And from the iMac, so the site is tested from outside the server rather than
from a shell that can reach it either way:

```
iMac
H=https://phpstack-1656314-6593553.cloudwaysapps.com
for p in / /articles /persons /places /collections /war-memorial "/search?q=newhall"; do
  printf "%-24s %s\n" "$p" "$(curl -o /dev/null -sw '%{http_code}' "$H$p")"
done
curl -s $H/ | grep -i 'name="robots"'
```

The last line must find nothing, or a robots tag without `noindex`. If it says
`noindex`, `CRAFT_DISALLOW_ROBOTS` is still `true`.

---

## 5. What is reachable that should not be

Measured against the live staging site on 19 September 2026, then again after
HTTP basic auth was put in front of it. **The webroot is right**, which is the
thing that mattered most. Nothing outside `web/` is served:

| URL | | |
|---|---|---|
| `/.env` | 403 | correct |
| `/scripts/import/import_ruiz_census.php` | 404 | correct |
| `/bootstrap.php`, `/config/db.php`, `/craft` | 404 | correct |
| `/composer.json`, `/CHANGELOG.md`, `/docs/…` | 404 | correct |

So the import scripts are not exposed, and neither is the security key.

**Six things are exposed and should not be.**

| URL | status | what it hands over |
|---|---|---|
| `/review/ledger-index.json` | 200, 1.5 MB | every legacy URL and title in the archive |
| `/review/audit.json` | 200 | the record audit |
| `/review/entities.html`, `article-links.html`, `place-links.html`, `relations.html`, `dates.html` | 200 | the reconciliation queues |
| `/admin-overview` | 200, 289 KB | where the archive is thin, field by field |
| `/graph` and `/graph/data` | 200 | the whole relation graph |
| `/admin-ledger` and `/admin-ledger/data` | 200, 110 KB | every record, its edit URL and its state |

`/review/` itself returns 403, so the directory cannot be listed, but every file
inside it is served to anyone who knows or guesses a name. A 403 on the
directory is not access control.

### The fidelity files are absent by luck, not by design

`/review/fidelity/` and `/review/fidelity-summary.json` return 404 today only
because the `.txt` files are gitignored and the server has not yet pulled the
commit carrying the summary. **`fidelity-summary.json` and
`fidelity-investigation.md` are committed and will appear at a public URL on the
next `git pull`.** They carry record titles, legacy paths and extracts. The 3.3
MB of per-article files carry the full text of the archive twice over, and are
absent only because they were kept out of git for size.

Block `/review/` before the next pull, not after.

### The block

**`.htaccess` is not read.** Cloudways serves this stack with Nginx in front of
Apache, and Nginx answers for a static file like `/review/ledger-index.json`
without Apache ever seeing the request. The `<LocationMatch>` block in
`web/.htaccess` is inert. It is left in place for a future host that does read
it, and it is not the control here.

The control is one Nginx location block.

**Where it goes on a Cloudways PHP stack.** Each application has its own Nginx
include, and the file to edit is:

```
Server
/home/master/applications/<APP>/conf/server.nginx
```

`<APP>` is the application folder name, the one in the path to `public_html`.
That file is included inside the `server { }` block for this application, so a
bare `location` directive is what belongs in it, with no wrapper.

Add:

```nginx
# The review screens, the built ledger index, the fidelity files and the
# correspondence CSV. All of it is working material: legacy URLs, record
# titles, reconciliation queues, and in the fidelity files the full text of
# the archive. None of it is for the public.
location ^~ /review/ {
    deny all;
    return 404;
}
```

`^~` matters. Without it a later regex `location` for static files can win on a
`.json` or `.csv` and serve the file anyway; `^~` stops Nginx considering
regex locations at all once the prefix matches. `return 404` rather than `403`
so the directory is not advertised.

Then, from the panel, **Application → Application Settings → Restart Nginx**, or:

```
Server
sudo service nginx reload
```

Cloudways also exposes this through **Application → Application Settings →
Nginx Settings** in the panel, which writes the same file. Editing it there
survives a Cloudways stack update; editing the file directly may not, so the
panel is the safer of the two.

### The admin pages are guarded in the template, and that has landed

`/admin-overview`, `/graph`, `/graph/data`, `/admin-ledger`, `/admin-ledger/data`
and `/admin-fixes` now carry, at the top of each template:

```twig
{% requireLogin %}
{% if not currentUser.admin %}{% exit 404 %}{% endif %}
```

Anonymous requests get 302 to the login screen; a signed-in non-admin gets 404
rather than 403, so the page is not confirmed to exist to somebody who should
not see it. `check_render.php` asserts the guard on all six, so a template that
loses it fails the standing check rather than going quietly public.

This lives in the code, so it holds whatever the server config says, and a
seventh unlisted page that forgets the guard shows up as a missing line in a
diff rather than as an open URL. The Nginx block above is still wanted for
`/review/`, which is files rather than templates and cannot be guarded this way.

**On the basic auth.** Staging currently answers 401 to everything including
`/`, so the whole application sits behind HTTP basic auth. That is a blunt block
and an effective one, but it is not a substitute for either control above: it
protects the site while it is staging and it comes off the day the site goes
live, at which point the Nginx block and the template guards are what remain.

### Verifying the block

```
iMac
H=https://phpstack-1656314-6593553.cloudwaysapps.com
for p in /review/ledger-index.json /review/audit.json /review/entities.html \
         /review/fidelity-summary.json /admin-overview /graph /graph/data \
         /admin-ledger /admin-ledger/data; do
  printf "%-38s %s\n" "$p" "$(curl -s -o /dev/null -w '%{http_code}' "$H$p")"
done
```

Every line 403 or 404. A 200 means that one is still open.

### Two more, smaller

`web/review/audit.json` is tracked in git although `.gitignore` lists it: the
ignore was added after the file was committed, and ignoring does not untrack, so
it deploys with the code. `git rm --cached web/review/audit.json` stops that.

The staging domain is indexable, and `robots.txt` currently disallows only
`cpresources/` and `vendor/`. Until the block is in place, add:

```
Disallow: /review/
Disallow: /admin-overview
Disallow: /admin-ledger
Disallow: /graph
```

That is a request rather than a control, and worth nothing against anyone who
does not ask politely. Do the block as well.

---

## 6. Rollback

Decide quickly and roll back whole. A half-repaired production database is worse than
a restored one, because the next person cannot tell which half is which.

**Before you start**, take a Cloudways backup: Panel → Application → **Backup and
Restore** → *Take Backup Now*. It covers files and database together and is the only
one-click way back.

### If the database import produced a broken site

```
Server
cd ~
mysql -h 127.0.0.1 -u <db_user> -p -e "DROP DATABASE <db_name>; CREATE DATABASE <db_name> CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;"
gunzip -c before-import-<timestamp>.sql.gz | mysql -h 127.0.0.1 -u <db_user> -p <db_name>
cd public_html && php craft clear-caches/all
```

Then work out what went wrong locally, against a copy, and not on the server.

### If the site renders but records look wrong

Do not edit records to fix them. Re-import the dump:

```
Server
mysql -h 127.0.0.1 -u <db_user> -p -e "DROP DATABASE <db_name>; CREATE DATABASE <db_name> CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;"
gunzip -c scvh-<date>.sql.gz | mysql -h 127.0.0.1 -u <db_user> -p <db_name>
cd public_html && php craft up && php craft clear-caches/all
```

The local database is the source of truth **until section 9 takes effect**. After
that it is not, and this rollback is no longer available: from then on the Cloudways
backup is the only way back, which is why the first thing in section 9 is a backup
schedule.

### If the code deploy broke it

```
Server
cd ~/public_html
git log --oneline -5
git checkout -f <last_good_sha>
composer install --no-dev --optimize-autoloader
php craft up
php craft clear-caches/all
```

If `php craft up` already applied project config from the bad commit, the schema has
moved and checking out the old code is not enough. Restore the database from the
Cloudways backup as well. This is the failure mode that makes the backup in the first
line of this section non-optional.

### If the site is down and the cause is not obvious

Turn `CRAFT_DEV_MODE=true` on for exactly as long as it takes to read the error, then
turn it off. A production stack trace is public.

```
Server
tail -n 100 storage/logs/web.log
tail -n 100 ~/logs/apache_<domain>.error.log
```

---

## 7. Backups

Cloudways → Server → **Backups**: daily, retention as long as the plan allows.
Then Application → **Backup and Restore**, and confirm a backup actually exists
before relying on the schedule. An untested backup is a plan, not a backup.

Take one by hand before anything that writes: an import run against production,
a `project-config/apply` that carries a schema change, or a content refresh.

The direction of travel is in section 3 and is not repeated here.
