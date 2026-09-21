# Deploying to Cloudways staging

Written 2026-09-20. **Nothing in this file has been run.** It is the sequence, in
order, with the machine each command belongs to.

Every command is labelled **iMac**, **MacBook** or **Server**. Run them in the
order given; several of them fail harmlessly out of order and two of them fail
destructively.

---

## What is wrong with the deploy today

`.github/workflows/deploy.yml` is four lines of work:

```yaml
on: push: branches: [main]
script: |
  cd /home/676057.cloudwaysapps.com/ufppzhwvbk/public_html
  git pull origin main
```

Six things are wrong with it, in order of how much damage they do.

1. **It pulls code and never applies the schema.** Craft keeps fields, entry
   types and section settings in `config/project/*.yaml`, and pulling those
   files changes nothing until `project-config/apply` runs. Deploying this batch
   through that workflow would give the server new templates reading fields that
   do not exist on it, which is a 500 on every record page, not a degraded one.

2. **There is no content.** Every record lives in the local database. The server
   has whatever was there in September and no way to get the rest. A code deploy
   makes the templates newer and the content no fresher.

3. **There are no images.** `web/uploads/archive-media` is gitignored, correctly,
   because it is binary. So it has never been deployed at all: 568 assets now
   and 4,271 after the four mirror passes, none of which git will ever carry.

4. **It authenticates with a password.** `CLOUDWAYS_PASSWORD` in a GitHub secret,
   used for SSH. A key is what this should use.

5. **Nothing checks whether it worked.** `git pull` on a dirty server tree fails
   and the step still reports success, because the script's exit status is the
   last command's and nothing looks at it.

6. **It has not fired.** It triggers on push to `main`, and **main is 156
   commits behind**, last touched 17 September. Every deploy for three days has
   been a no-op on stale code.

## Merging to main

`main` is an ancestor of `templates-batch-9`, so this is a **fast-forward with
no conflicts**:

```
git rev-list --left-right --count main...templates-batch-9
0   156
```

Zero commits on main that are not on the branch. Nothing to reconcile.

**Do not merge until the applies in WAITING ON NATHAN have been run locally and
the project config they write has been committed.** Merging first puts templates
on main that expect fields the committed config does not yet declare, and the
workflow will happily deploy them.

---

## The sequence

### 1. Finish locally  — **iMac**

```
cd ~/scvhistory
git status --short
```

Stop if `config/project/` is dirty. Those files are written by the applies and
must be committed before anything is deployed.

```
git add config/project
git commit -m "Project config after the collections batch"
git push
```

### 2. Merge to main  — **iMac**

```
cd ~/scvhistory
git checkout main
git pull
git merge --ff-only templates-batch-9
git push origin main
```

`--ff-only` is deliberate. If it refuses, main has moved and this runbook's
assumption is stale; stop and look rather than making a merge commit.

Pushing main fires the workflow, which will `git pull` on the server. That is
harmless and insufficient; steps 3 to 6 are the rest of it.

### 3. Pull and apply on the server  — **Server**

```
ssh <user>@<host>
cd /home/676057.cloudwaysapps.com/ufppzhwvbk/public_html
git pull origin main
composer install --no-dev --optimize-autoloader
php craft up
php craft project-config/apply
```

`craft up` runs pending migrations; `project-config/apply` writes the fields and
entry types. **In that order.** Applying config against an unmigrated schema is
how a Craft install breaks in a way that needs a database restore.

### 4. Move the database  — **iMac**, then **Server**

Content does not travel in git. Dump locally, copy, import.

**iMac**

```
cd ~/scvhistory
ddev export-db --gzip=false --file=/tmp/scvh-$(date +%Y%m%d).sql
gzip -9 /tmp/scvh-$(date +%Y%m%d).sql
scp /tmp/scvh-$(date +%Y%m%d).sql.gz <user>@<host>:~/
```

**Server**, and take a backup first because this overwrites everything:

```
cd /home/676057.cloudwaysapps.com/ufppzhwvbk/public_html
mysqldump -u <db_user> -p<db_pass> <db_name> | gzip -9 > ~/before-import-$(date +%Y%m%d).sql.gz
gunzip -c ~/scvh-<date>.sql.gz | mysql -u <db_user> -p<db_pass> <db_name>
php craft up
php craft project-config/apply
```

The second `project-config/apply` is not a typo. The imported database carries
the local install's config state, and applying again reconciles it with the
`config/project` files that were just pulled.

**`CRAFT_DB_SERVER` must be `127.0.0.1`, not the public IP, and
`CRAFT_DB_TABLE_PREFIX=scvh` must be set.** The `.env` on the server already has
both; this is here because it is the thing that breaks first if `.env` is ever
rebuilt.

### 5. Move the images  — **MacBook**

The uploads directory is gitignored and has never been deployed. This is the
only step that carries it.

```
cd ~/scvhistory
rsync -avz --partial --progress \
  web/uploads/archive-media/ \
  <user>@<host>:/home/676057.cloudwaysapps.com/ufppzhwvbk/public_html/web/uploads/archive-media/
```

Note the trailing slashes on both paths: without them rsync nests the directory
inside itself.

`--partial` matters. This is 568 files now and roughly 4,300 after the mirror
passes, and a dropped connection two thirds of the way through should resume
rather than restart.

Do **not** add `--delete`. The server may hold files this machine does not, and
a mirror-with-delete would remove them silently.

### 6. Clear and verify  — **Server**

```
cd /home/676057.cloudwaysapps.com/ufppzhwvbk/public_html
php craft clear-caches/all
php craft gc --delete-all-trashed
```

Then check, from anywhere:

```
curl -sI https://phpstack-1656314-6593553.cloudwaysapps.com/ | head -1
curl -s  https://phpstack-1656314-6593553.cloudwaysapps.com/articles/chapter-5-tribal-relics | grep -c 'rec-band'
curl -sI https://phpstack-1656314-6593553.cloudwaysapps.com/review/ | head -1
```

The first should be 200, the second 1, and **the third should be 401 or 403**.
`/review/` holds the working reports and must not be public.

### 7. The `/review/` guard  — **Server**

Cloudways serves with Nginx, so the `.htaccess` in `web/review` is not read. Add
to the app's Nginx config, inside the `server` block, before the `location /`:

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

The admin pages guard themselves in the template with `{% requireLogin %}` and
a 404 for non-admins, so they need no server rule. `/review/` is static files
and does.

---

## What this does not cover

- **Rolling back.** The database backup in step 4 is the rollback for content.
  For code it is `git checkout <previous sha>` on the server and re-running
  steps 3 and 6. There is no scripted rollback and there should be one.
- **The mirror.** `/mnt/reggie` is a DDEV bind on the iMac. The server has no
  such mount and will not get one, which is why the images are imported into the
  volume locally and rsynced, rather than served from the drive.
- **Fixing the workflow.** Steps 3 to 6 could be the workflow instead of two
  lines of `git pull`. That is worth doing and is a separate change, because a
  deploy script that also imports a database is a deploy script that can destroy
  one.
