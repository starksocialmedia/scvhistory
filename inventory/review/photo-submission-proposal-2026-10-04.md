# Sending a photograph to the archive: proposal

4 October 2026. Claude Code, for Nathan. Nothing in this proposal is built. Today the memorial
records with no likeness say "No photograph of him is known to the archive" in the lead image
slot; the invitation to send one waits on this.

## What a reader sees

On every memorial record and person record with no likeness, under the panel:

> Do you have a photograph of him? [Send it to the archive]

The link opens a page on the site (`/send?for=<record id>`), not an email address. The form there
says:

- **The picture.** At least 1,200 pixels on its longer side (a phone photograph of an original
  print is fine; lay the print flat in daylight, no flash). JPEG, PNG, TIFF or HEIC, up to 25 MB.
  Up to five files.
- **About it.** Who is in it, and which is him if there are several. When it was taken, even
  roughly. Who took it, if known. Who holds the original print or file now.
- **About you.** Your name, how you would like to be credited (or not at all), and an email
  address so the archivist can reply. The address is never published or shown on the site.
- **Permission.** Two boxes: "I hold this photograph, or the person who does has agreed to my
  sending it" and "The archive may publish it with the credit I have given."
- **The promise, in plain words:** "Nothing you send is published until the archivist has reviewed
  it. If it is used, the record will say who sent it and who holds the original, as you have told
  us."

The same partial carries a different line wherever the archive says something is missing: a
date not recorded, a source not found, a present holder not known. Each line names what is
missing and links to the same page with the record and the gap filled in. The memorial and the
portrait come first; the other gaps follow once this one has run for a while.

## How it works

1. **The form posts to a small controller in the site's own module.** I recommend this route: no
   third party sees the submission, and there is no plugin to install. The first-party Guest
   Entries plugin would also work, but it is a package install (ask first) and less able to
   check files.
2. **Each submission becomes an entry in a new `submissions` section.** The section has no URLs,
   and its entries are disabled, so nothing is public.
   - Fields: the record it is for; what kind of thing it is (photograph for now); the fields above;
     the status (new, accepted, declined, spam).
   - The files go to a private volume stored outside `web/`, so they have no public address until
     you accept them.
3. **The controller checks each file before saving it.**
   - It must be a real image (read with the image library, not trusted by its name), at least
     1,200 pixels on the long side, and within the size cap.
   - Location and camera metadata are stripped from the stored copy; the original bytes' checksum
     is recorded.
4. **The review queue is an admin page** (like /admin-ledger: guarded, editors only). It lists new
   submissions with the image, the record, the details, and three actions:
   - **Accept** hands the file to the existing import pattern: archive media, provenance, credit
     as given, attached to the record as its portrait or among its images. Nothing is attached
     without your click.
   - **Decline** asks for a one-line reason, kept for the record.
   - **Spam** deletes the files and marks the entry.
5. **You learn about new ones from the queue page**, and later, if you want, from a daily email to
   your own address sent by the server. That needs outgoing mail set up on Cloudways, which only
   you can do.

## What it costs

**Moderation.** The volume will be low: a local archive's request for photographs of 34 named men
and the people pages might bring a handful a month, and spikes after the Signal or a Memorial Day
mention. Each real submission is 10 to 20 minutes of your time:
- check that the picture is who it says;
- read the permission;
- decide portrait or related image;
- write or correct the caption;
- reply to the sender.

That is the same work the portraits you send me take now; the queue only collects it in one place.

**Spam.**
- A public form gets bot posts within weeks of going live. Three free defences stop nearly all
  of them, without a puzzle for the reader:
  - a hidden field bots fill and people cannot see;
  - a minimum time between page load and submission (bots post instantly);
  - a limit of five submissions per address per day.
- A file is required, and anything that is not an image is refused before it is stored. That
  removes most of what gets through.
- If spam still arrives, Cloudflare's Turnstile check can be added: free, invisible to most
  readers. It needs a key from the Cloudflare account, which only you can create; the agents do
  not touch Cloudflare.

**Risk.**
- An upload form is the one place a stranger can put a file on the server. Four things together
  keep that safe:
  - the private volume;
  - the image check;
  - the size cap;
  - no file ever served before you accept it.
- The email addresses are personal data. I recommend:
  - they are kept only until the submission is decided, plus a year;
  - then they are cleared from the entry, keeping the credit line the sender chose.

**Build.**
- The work:
  - the section;
  - the private volume (a project-config change, ask first);
  - the controller and its checks;
  - the form page;
  - the invitation partial;
  - the queue page;
  - the Accept action, wired to the existing import scripts;
  - check_render coverage.
- About a day of agent work, tested on DDEV.
- Production needs the PHP upload limit raised to 25 MB and the private volume's folder created
  outside the web root on Cloudways (yours to do; the runbook will say how).

## Decisions for Nathan

1. **The route:** the site's own controller (recommended), or the Guest Entries plugin.
2. **The minimum:** 1,200 pixels on the long side. Lower lets in more phone snapshots of small
   prints. Higher keeps the archive cleaner.
3. **Who may send:** anyone, or only people who give an email address? I recommend requiring the
   email, since a photograph with no way to ask about it is hard to use.
4. **Notification:** the queue page only at first (recommended), the daily email later.
5. **Where it goes next after the memorial and portraits:** dates, sources, present holders.
