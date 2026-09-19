# Cloudways deployment runbook

First deployment of SCVHistory.com from local DDEV to Cloudways. Follow it by hand,
top to bottom. Every step has a verification, and no step is finished until its
verification passes.

Measured against the tree at the time of writing: Craft 5.11.2, PHP 8.4, 262 records,
568 assets, **204 MB and 893 files** under `web/uploads`, a **21 MB** database dump
(4 MB gzipped), table prefix `scvh_`.

Labels used below: **iMac** is the local machine, **Server** is the Cloudways SSH
session. Nothing in this file is run by an agent.

---

## 0. Before anything: the webroot

This is the single setting that decides whether the deployment is safe, and it is the
one Cloudways gets wrong by default.

Craft's document root is `web/`, not the project root. Cloudways points a new
application at `public_html`. If that is left alone, then `/.env`, `/storage`,
`/vendor`, `/config`, `/scripts` and `/craft` are all served over HTTP, and the
security key, the database password and every import script are public.

**Cloudways panel → Application → Application Settings → General → Webroot →
`public_html/web`.** Then Save, and wait for the panel to confirm.

Verify, once the code is up there in step 4, that the answer to the first is 404 and
the second is 200:

```
Server
curl -sI https://<domain>/.env | head -1
curl -sI https://<domain>/index.php | head -1
```

If `/.env` returns anything but 404 or 403, stop, fix the webroot, and rotate
`CRAFT_SECURITY_KEY`, the database password and any other credential in that file.

Also set, in the same panel section: PHP 8.4, and MySQL 8. Craft 5.11 will refuse to
boot on PHP below 8.2.

---

## 1. The `.env`

`.env` is never committed and never copied wholesale. Create it on the server by hand
from `.env.example` and set these.

| Key | Local | Production | Why |
|---|---|---|---|
| `CRAFT_ENVIRONMENT` | `dev` | `production` | Selects the `production` block in `config/general.php`. |
| `PRIMARY_SITE_URL` | `https://scvhistory.ddev.site/` | the Cloudways domain, with a trailing slash | Everything derives from this: `@web`, canonical URLs, JSON-LD `@id`, sitemaps, and the asset URLs. |
| `CRAFT_SECURITY_KEY` | *a value* | **the same value, character for character** | See below. |
| `LEGACY_HOST` | `https://scvhistory.com` | `https://scvhistory.com`, unchanged | This is the *old* site the legacy URL fields resolve against. It is not this site and it does not become the Cloudways domain. |
| `DB_SERVER` | `db` | `localhost` | Cloudways runs MySQL on the same host. |
| `DB_PORT` | `3306` | `3306` | |
| `DB_DATABASE` | `db` | from Cloudways | Panel → Application → **Access Details** → MySQL. |
| `DB_USER` | `db` | from Cloudways | Same place. |
| `DB_PASSWORD` | `db` | from Cloudways | Same place. Never typed into a commit, a commit message or a chat window. |
| `DB_TABLE_PREFIX` | `scvh_` | `scvh_`, unchanged | The dump carries `scvh_`-prefixed table names. A different prefix here and Craft looks for tables that are not there. |
| `DB_DRIVER` | `mysql` | `mysql` | |
| `CRAFT_APP_ID` | *a value* | the same value | Cheap to keep the same; changing it only invalidates sessions and caches. |
| `CRAFT_DEV_MODE` | `true` | `false` | Leaving it true puts a full stack trace with file paths on every error page. |
| `CRAFT_ALLOW_ADMIN_CHANGES` | `true` | `false` | This is what makes the standing rule in section 9 real rather than a promise. With it false, the control panel will not let anyone add a field on production. |
| `CRAFT_DISALLOW_ROBOTS` | `true` | `false` | **Easy to miss.** It is true locally so the DDEV site is not indexed. Ship it true and the live site tells Google not to index it, and nothing else about the deployment will look wrong. |

### `CRAFT_SECURITY_KEY` must be copied verbatim

Craft encrypts stored values with this key, and the database dump carries them
already encrypted. A fresh key on the server does not fail loudly: Craft boots, the
site renders, and then anything encrypted comes back as garbage or throws on read.
There is no repair afterwards short of re-entering every affected value by hand.

Copy it from the local `.env` and paste it. Do not let Craft generate one, do not
retype it, and check the paste has no trailing newline or wrapped space:

```
iMac
grep '^CRAFT_SECURITY_KEY=' .env
```

```
Server
cd /home/master/applications/<APP>/public_html
grep '^CRAFT_SECURITY_KEY=' .env
```

Compare the two lines character for character before going on.

### Verify the .env

```
Server
cd /home/master/applications/<APP>/public_html
php craft db/backup --help >/dev/null && echo "Craft boots and the database connects"
php craft exec "echo Craft::\$app->getConfig()->getGeneral()->devMode ? 'devMode ON (wrong)' : 'devMode off (right)';"
php craft exec "echo Craft::\$app->getSites()->getPrimarySite()->getBaseUrl();"
```

The last line must print the Cloudways domain. If it prints `scvhistory.ddev.site`,
`PRIMARY_SITE_URL` did not take, and every URL the site emits will be wrong.

---

## 2. Assets first, by rsync

204 MB, 893 files, under `web/uploads/archive-media`. Thirteen of them are in git
(the site logos and the service seals, kept by
`web/uploads/archive-media/.gitignore`); the other 880 are not, and rsync is the only
thing that brings them.

```
iMac
cd ~/scvhistory
rsync -avz --partial --progress \
  --exclude '.DS_Store' \
  web/uploads/archive-media/ \
  <master_user>@<server_ip>:/home/master/applications/<APP>/public_html/web/uploads/archive-media/
```

The trailing slashes on both paths matter: without them rsync nests the directory
inside itself.

Re-runnable. If it drops, run it again; `--partial` resumes the file it was on.

### Verify

```
Server
cd /home/master/applications/<APP>/public_html
find web/uploads/archive-media -type f | wc -l     # expect 893
du -sh web/uploads/archive-media                   # expect ~204M
```

```
iMac
find web/uploads/archive-media -type f | wc -l     # must match
```

If the counts differ, run rsync again and compare a checksum of a sample rather than
assuming:

```
iMac
shasum web/uploads/archive-media/legacy/lw3457t.jpg
```
```
Server
shasum web/uploads/archive-media/legacy/lw3457t.jpg
```

Permissions, since Cloudways runs PHP as the application user:

```
Server
chown -R <app_user>:www-data web/uploads
find web/uploads -type d -exec chmod 775 {} \;
find web/uploads -type f -exec chmod 664 {} \;
```

---

## 3. Then the database

```
iMac
cd ~/scvhistory
ddev export-db --file=/tmp/scvh-$(date +%Y%m%d).sql.gz
scp /tmp/scvh-$(date +%Y%m%d).sql.gz <master_user>@<server_ip>:/home/master/applications/<APP>/
```

Take a backup of the empty production database first, so step 8 has something to go
back to even at this stage:

```
Server
cd /home/master/applications/<APP>
mysqldump -h localhost -u <db_user> -p <db_name> | gzip > before-import-$(date +%Y%m%d-%H%M).sql.gz
gunzip -c scvh-<date>.sql.gz | mysql -h localhost -u <db_user> -p <db_name>
```

Then bring the schema to where the code expects it:

```
Server
cd /home/master/applications/<APP>/public_html
php craft up
```

`php craft up` runs pending migrations and applies `config/project.yaml`. It is safe
to run twice.

### Verify: the counts must match local

```
iMac
ddev craft exec "foreach (Craft::\$app->entries->getAllSections() as \$s) { echo str_pad(\$s->handle, 20) . \craft\elements\Entry::find()->section(\$s->handle)->status(null)->count() . PHP_EOL; } echo 'assets ' . \craft\elements\Asset::find()->status(null)->count() . PHP_EOL;"
```

```
Server
cd /home/master/applications/<APP>/public_html
php craft exec "foreach (Craft::\$app->entries->getAllSections() as \$s) { echo str_pad(\$s->handle, 20) . \craft\elements\Entry::find()->section(\$s->handle)->status(null)->count() . PHP_EOL; } echo 'assets ' . \craft\elements\Asset::find()->status(null)->count() . PHP_EOL;"
```

At the time of writing that is articles 103, warMemorials 54, persons 41, places 18,
organizations 14, collections 13, groups 9, pages 8, events 1, obituaries 1,
assets 568. Re-read it locally rather than trusting those numbers; what matters is
that the two lists are identical.

Then rebuild the search index, which the dump carries but which is cheap to redo and
expensive to have wrong:

```
Server
php craft resave/entries --update-search-index
php craft index-assets/all
```

`index-assets/all` also confirms the files from step 2 are where Craft thinks they
are: it reports missing files rather than silently skipping them.

---

## 4. Then the code

```
Server
cd /home/master/applications/<APP>/public_html
git clone git@github.com:starksocialmedia/scvhistory.git .
git checkout templates-batch-9
composer install --no-dev --optimize-autoloader
php craft up
php craft clear-caches/all
```

`--no-dev` matters: the dev dependencies include tooling that has no business on a
public server.

Writable directories:

```
Server
chown -R <app_user>:www-data storage web/cpresources
find storage -type d -exec chmod 775 {} \;
find web/cpresources -type d -exec chmod 775 {} \;
```

### Verify: a record page, and its JSON-LD

The repository already carries the check. It walks one page per section, entry type
and category group, asserts 200, no error signature, a `<title>`, and JSON-LD that
parses:

```
Server
cd /home/master/applications/<APP>/public_html
php craft exec "eval(file_get_contents('scripts/import/check_render.php'))"
```

Expect `checked 25 pages, 23 JSON-LD blocks parsed` and `no failures`. It builds its
URLs from the primary site's base URL, so this is also a second check that
`PRIMARY_SITE_URL` is right.

By hand, one of each:

```
Server
curl -o /dev/null -sw "%{http_code}\n" https://<domain>/
curl -o /dev/null -sw "%{http_code}\n" https://<domain>/articles/bowers-cave
curl -o /dev/null -sw "%{http_code}\n" https://<domain>/uploads/archive-media/legacy/lw3457t.jpg
```

Three 200s. The third is the whole of step 2 in one line: if the assets did not land,
or the webroot is wrong, or the permissions are wrong, it is not a 200.

The JSON-LD on a single page, parsed rather than eyeballed:

```
Server
curl -s https://<domain>/articles/bowers-cave \
  | sed -n 's/.*<script type="application\/ld+json">\(.*\)<\/script>.*/\1/p' \
  | python3 -m json.tool > /dev/null && echo "JSON-LD parses"
```

And that the page is not telling search engines to go away:

```
Server
curl -s https://<domain>/ | grep -i 'name="robots"'
```

Nothing, or a robots tag without `noindex`. If it says `noindex`,
`CRAFT_DISALLOW_ROBOTS` is still `true`.

---

## 5. Why this order, and what breaks if it is wrong

**Assets, then database, then code.**

*Database before assets.* Craft's asset records point at files. Import the database
first and, for as long as rsync is still running, every asset record refers to a file
that is not there. `index-assets` will mark them missing, a resave can null the
relations, and the front end renders broken images. Worse, if anyone opens the
control panel in that window and Craft offers to clean up missing assets, accepting
deletes the records and the relations to them, and the relations are hours of work
that the files coming back will not restore.

*Code before database.* `php craft up` applies `config/project.yaml` to whatever
schema is in the database. Against an empty database it builds the whole schema from
project config, and then the dump import collides with tables that already exist and
either fails halfway or leaves a hybrid. Against the imported dump it does the right
thing: it applies only what is genuinely newer than the dump.

*Code before assets.* Harmless in itself, but `git clone` into a non-empty
`public_html` fails, so the clone has to be either first or done with the
`git init` + `git remote add` + `git fetch` + `git checkout -f` dance. Putting assets
under `web/uploads` first and cloning second means dealing with that. Doing the
assets first anyway is worth it for the reason above; clone with:

```
Server
cd /home/master/applications/<APP>/public_html
git init
git remote add origin git@github.com:starksocialmedia/scvhistory.git
git fetch origin templates-batch-9
git checkout -f -b templates-batch-9 origin/templates-batch-9
```

which leaves the untracked upload files alone.

*The rsync last.* If the files land after the search and asset indexes have been
built, everything is indexed as missing and both have to be rebuilt. Not fatal,
just work done twice.

---

## 6. What must not be public

Five things. Each is blocked differently and each needs checking after deployment,
because "it is not linked from anywhere" is not a control.

### a. `web/review/`

13 MB of review screens and their JSON, sitting inside the document root, so it is
served by default. It holds `ledger-index.json` (every legacy URL and title),
`audit.json`, and whichever decision files have been generated.

Note that `web/review/audit.json` is **tracked in git** despite being listed in
`.gitignore` — the ignore was added after the file was committed, and ignoring does
not untrack. It will deploy. So will `ledger-index.json`, which is committed on
purpose.

Block it in `web/.htaccess`, before the existing rewrite block:

```apache
<IfModule mod_authz_core.c>
    <LocationMatch "^/review">
        Require all denied
    </LocationMatch>
</IfModule>
```

Cloudways serves through Nginx in front of Apache, and Nginx wins for static files,
so `.htaccess` alone is not enough. Add, in the Cloudways panel under
**Application → Application Settings → Nginx / Apache custom rules** (or
`/home/master/applications/<APP>/conf/server.nginx` if editing directly):

```nginx
location ^~ /review/ { deny all; return 404; }
```

Verify:

```
Server
curl -o /dev/null -sw "%{http_code}\n" https://<domain>/review/ledger-index.json
curl -o /dev/null -sw "%{http_code}\n" https://<domain>/review/entities.html
```

Both 403 or 404.

### b. `/admin-overview`, `/graph`, `/admin-ledger`

These are Craft templates. They render for anyone who knows the path. `noindex` keeps
them out of search results and does nothing else; it is not access control and was
never meant to be.

Two ways, and the second is the one to use.

*Quick:* the same Nginx deny, by path. Works, but `/graph/data` and
`/admin-ledger/data` are separate paths and both must be listed, and a fourth such
page added later will be public until somebody remembers this file.

*Right:* require a logged-in admin, in the template itself. At the top of
`templates/admin-overview/index.twig`, `templates/graph/index.twig`,
`templates/graph/data.twig`, `templates/admin-ledger/index.twig` and
`templates/admin-ledger/data.twig`:

```twig
{% requireLogin %}
{% if not currentUser.admin %}{% exit 404 %}{% endif %}
```

`{% requireLogin %}` stops an anonymous visitor. With no front-end `loginPath`
configured, as here, it returns 404 rather than redirecting, which is the better of
the two outcomes: it does not confirm the page exists. The second line does the same
for a logged-in user who is not an admin. Tested locally on a throwaway template: a
request with no session cookie gets 404.
This travels with the code, so a page added later that forgets it is a visible
omission in the diff rather than an invisible one in a server config file.

The browser fetches in `/admin-ledger` inherit the session cookie, so the data
endpoints keep working for an admin and stop working for everyone else.

Verify, logged out, in a private window or with curl:

```
Server
for p in /admin-overview /graph /graph/data /admin-ledger /admin-ledger/data; do
  printf "%-22s %s\n" "$p" "$(curl -o /dev/null -sw '%{http_code}' https://<domain>$p)"
done
```

Expect 404 on every one, or 302 if a front-end `loginPath` is ever configured. A 200
means the guard is missing from that template.

### c. `scripts/import/`

Sixty-odd import scripts. They are at the project root, a sibling of `web/`, so with
the webroot set correctly in step 0 they are outside the document root and
unreachable. **With the webroot left at `public_html` they are all readable**, and
several of them print enough about the schema and the data model to be a map for
somebody.

Most are eval-style with no opening `<?php`, so a web request would return their
source as plain text rather than run them, which is a disclosure problem rather than
an execution one. But `reconnect_person_images.php` and `bootstrap.php` at the
project root **do** open with `<?php` and would execute.

The fix is step 0 and nothing else. Verify:

```
Server
for p in /scripts/import/import_ruiz_census.php /bootstrap.php /craft /composer.json /config/db.php; do
  printf "%-44s %s\n" "$p" "$(curl -o /dev/null -sw '%{http_code}' https://<domain>$p)"
done
```

Every one 404. Any 200 here means the webroot is wrong and the `.env` is public too.

### d. The control panel

Craft's `cpTrigger` is `admin` by default. Leave it, but make sure there is no test
account: after the database import, production carries whatever users the local
database had.

```
Server
php craft exec "foreach (\craft\elements\User::find()->status(null)->all() as \$u) { echo str_pad(\$u->username, 24) . str_pad(\$u->email, 34) . (\$u->admin ? 'ADMIN ' : '      ') . \$u->status . PHP_EOL; }"
```

Delete or suspend anything that is not a real person, and change the password of
every account that survives, because the local passwords were never chosen with a
public server in mind.

### e. Directory listing

```
Server
curl -s https://<domain>/uploads/archive-media/ | head -3
```

Should not list files. If it does, add `Options -Indexes` to `web/.htaccess` and
`autoindex off;` to the Nginx rules.

---

## 7. The whole-deployment check

After all four steps, run the check once more and read it rather than glancing at it:

```
Server
cd /home/master/applications/<APP>/public_html
php craft exec "eval(file_get_contents('scripts/import/check_render.php'))"
php craft exec "eval(file_get_contents('scripts/import/audit_records.php'))"
```

And from the iMac, so it is tested from outside the server:

```
iMac
for p in / /articles /persons /places /collections /war-memorial /search?q=newhall; do
  printf "%-24s %s\n" "$p" "$(curl -o /dev/null -sw '%{http_code}' "https://<domain>$p")"
done
```

---

## 8. Rollback

Decide quickly and roll back whole. A half-repaired production database is worse than
a restored one, because the next person cannot tell which half is which.

**Before you start**, take a Cloudways backup: Panel → Application → **Backup and
Restore** → *Take Backup Now*. It covers files and database together and is the only
one-click way back.

### If the database import produced a broken site

```
Server
cd /home/master/applications/<APP>
mysql -h localhost -u <db_user> -p -e "DROP DATABASE <db_name>; CREATE DATABASE <db_name> CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;"
gunzip -c before-import-<timestamp>.sql.gz | mysql -h localhost -u <db_user> -p <db_name>
cd public_html && php craft clear-caches/all
```

Then work out what went wrong locally, against a copy, and not on the server.

### If the site renders but records look wrong

Do not edit records to fix them. Re-import the dump:

```
Server
mysql -h localhost -u <db_user> -p -e "DROP DATABASE <db_name>; CREATE DATABASE <db_name> CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;"
gunzip -c scvh-<date>.sql.gz | mysql -h localhost -u <db_user> -p <db_name>
cd public_html && php craft up && php craft clear-caches/all
```

The local database is the source of truth **until section 9 takes effect**. After
that it is not, and this rollback is no longer available: from then on the Cloudways
backup is the only way back, which is why the first thing in section 9 is a backup
schedule.

### If the code deploy broke it

```
Server
cd /home/master/applications/<APP>/public_html
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
tail -n 100 /home/master/applications/<APP>/logs/apache_<domain>.error.log
```

---

## 9. The standing rule, once it is live

**Schema moves through git. Content does not move again.**

From the moment production is serving, the two halves travel in opposite directions
and never in the same direction as each other.

### Schema: local → git → production

A field, a section, an entry type, a category group, a field layout: made locally in
the control panel, which writes `config/project.yaml`, committed, pushed, pulled on
the server, applied.

```
iMac
git add config/project/ && git commit -m "..." && git push
```
```
Server
cd /home/master/applications/<APP>/public_html
git pull
php craft project-config/apply
php craft clear-caches/all
```

`CRAFT_ALLOW_ADMIN_CHANGES=false` on production is what enforces this. With it set,
the production control panel does not offer the settings screens at all, so the rule
cannot be broken by accident or by someone who has not read this file.

### Content: production is the only copy

Records, bodies, relations, images, review flags. Edited on production, in the
production control panel, and nowhere else. The local database stops being the source
of truth the moment the first record is edited live.

**Going the other way, when local needs current data:**

```
Server
cd /home/master/applications/<APP>/public_html
php craft db/backup /home/master/applications/<APP>/pull-$(date +%Y%m%d).sql
```
```
iMac
scp <master_user>@<server_ip>:/home/master/applications/<APP>/pull-<date>.sql /tmp/
ddev import-db --file=/tmp/pull-<date>.sql
rsync -avz <master_user>@<server_ip>:/home/master/applications/<APP>/public_html/web/uploads/archive-media/ web/uploads/archive-media/
```

That direction is a **pull, always, and never a push**. There is no supported way to
send a local database up once production is live, and an import script that has been
tested only against a local copy is not evidence that it is safe against the real one.

### Import scripts after go-live

The scripts under `scripts/import/` still have a job: there are 5,606 legacy pages
with no record yet. They run **on production**, against the live database, which is a
change from how they have been used so far. So:

1. Pull production down to local first, using the commands above.
2. Dry-run the script locally against that copy, and read the output.
3. Take a Cloudways backup.
4. Run it on the server with `$APPLY = true`, set on the server and never committed.
5. Run `check_render.php` and compare record counts before and after.

`$APPLY = false` in every committed script remains the rule, and it matters more now
than it did, because the copy it would otherwise write to is the only one.

### Backups

Cloudways → Server → **Backups**: daily, retention as long as the plan allows. Then
Application → Backup and Restore, and confirm a backup actually exists before relying
on the schedule. An untested backup is a plan, not a backup.
