---
name: scv-record-template
description: The record page pattern for SCVHistory entry templates. Use when building or changing any templates/<section>/_entry.twig, or the shared partials under _partials/record/ and _partials/sidebar/.
---

# Record pages

Every entry template follows one shape, lifted from the approved war memorial
record. Read an existing one before writing a new one:
`templates/war-memorial/_entry.twig` is the reference, `templates/persons/_entry.twig`
the one with a portrait, `templates/articles/_entry.twig` the one without.

## Design language

Type: Playfair Display for headings and italic subtitles, Jost for labels and
kickers, Public Sans for body.

Palette: navy `#17254C`, gold `#C4A031`, deep gold `#A9842B` and `#7A5C1B` for
small text, cream `#FDF7EA`, page `#F6F7F9`, borders `#E2E4E8` and `#EFE6D0`,
muted `#5B6472`, faint `#8A919E`.

Do not invent visual design. If the brief does not specify it, copy the nearest
existing implementation.

## Structure

```twig
{% extends "_layouts/base" %}
{% block head %}
  {% include "_partials/record/css" %}
  {% include "_partials/record/print" %}
{% endblock %}
```

Then a cream `.rec-band` holding `.rec-crumbs`, `.rec-kick`, `h1`, `.rec-sub`,
`.rec-facts`, `.rec-chips` and an optional `.rec-portrait` at the right. Then
`.rec-wrap > .rec-main`, a two column grid of the prose and a `.rec-side` of
sidebar boxes. `.rec-head--noart` collapses the band to one column when there is
no portrait.

The band is cream. A band image, where the type has one, is `bandImage` first and
`featuredImage` only as a fallback, because most featured images are title cards
with the headline lettered into them. On an article in a collection there is no
fallback at all.

## Reading fields

Build the field layout map once, then read through it. Never use `is defined`.

```twig
{% set present = {} %}
{% for f in entry.fieldLayout.customFields %}
  {% set present = present|merge({ (f.handle): true }) %}
{% endfor %}
{% set F = {} %}
{% for h in ['body', 'burialPlace', 'wmRank'] %}
  {% set v = present[h] is defined ? attribute(entry, h) : null %}
  {% set F = F|merge({ (h): v is not empty ? v|trim : '' }) %}
{% endfor %}
```

`entry.someHandle is defined` returns true for a field the entry type does not
have, then throws when read. It has broken the site four times.

## The two conditional rules

**A row renders only when its field has a value.** Build rows as pairs and filter:

```twig
{% set svc = [['BRANCH', F.wmBranch], ['RANK', F.wmRank], ['UNIT', F.wmUnit]]|filter(r => r[1]) %}
```

**A box renders only when it has content.** Never an empty panel, never a label
with nothing under it:

```twig
{% if svc|length %}
  <div class="rec-box"><div class="rec-lab">SERVICE RECORD</div>
    <div class="rec-rows">{% for r in svc %}<div class="k">{{ r[0] }}</div><div class="v">{{ r[1] }}</div>{% endfor %}</div>
  </div>
{% endif %}
```

The same applies to relation boxes: resolve the relation, then render only when
the result is non-empty.

## The shared partials

`_partials/record/` — include these rather than reimplementing:

- `css` the design system, `print` the print stylesheet
- `tools` the tools row and the read-aloud player, `cite` Chicago, MLA 9 and APA 7
- `note` an editor's note, called separately for the top and bottom notes; the
  handle differs by type so the caller passes the value
- `images` the Photos section with a lightbox, skipping anything already placed
  inline; `documents` the documents box; `tags` the tags box
- `_partials/prose` turns blank-line separated text into paragraphs and replaces a
  line of `[image:N]` with a figure floated right, N being the 1-based index into
  recordImages

`_partials/sidebar/box.twig` is an embed for a white card with a gold top rule.
`_partials/legacy-url.twig` resolves every legacy link; the host comes from
`LEGACY_HOST` through `craft.app.config.custom.legacyHost`, so never hardcode
scvhistory.com in a template. `sourcePath` is provenance and keeps its full host,
so it does not go through that helper.

## A Twig comment cannot go inside a hash literal

This is a syntax error, and it takes down every page that uses the partial:

```twig
{% set main = {
  '@type': 'Place',
  {#- explaining the next line -#}
  'name': el.title,
} %}
```

Comments go **above the branch**, never between a key and a value. The compiler
does not complain until something renders, `php -l` cannot see it because the
file is not PHP, and the diff reads perfectly well. It has taken the whole site
down twice in one session, both times in `_partials/head/schema.twig`, both
times while adding a comment that explained a correct change.

**Run `check_render.php` before reporting any change that touches a template.**
Not only the schema partial, and not only when something feels risky. It is the
last step of the job, in the same breath as the commit. A partial is shared by
more pages than the one you were looking at, a base layout is shared by all of
them, and the failure is total at render and invisible in the diff. The command
is under "Before you say it works" below; it takes seconds and it has already
caught a fault that two careful readings of the diff did not.

## Before you say it works

Craft serves compiled templates from cache, so a broken template can keep
rendering and fail later. Clear `storage/runtime/compiled_templates` before
trusting any before-and-after check.

Then run:

```
ddev craft exec "eval(file_get_contents('scripts/import/check_render.php'))"
```

It clears the template cache, discovers one page per section, entry type and
category group from Craft rather than from a list that can go stale, adds the
indexes and the unlisted pages, and asserts on what the server actually sent: a
200, no error signature in the body, a `<title>`, and that every JSON-LD block
parses. It ends in `no failures` or it names each page and what was wrong.

Twig 3.21 cannot call a variable holding an arrow function. Build a plain
dictionary instead. `merge` reindexes integer keys, so use string keys when
building a lookup.

## The class of error a diff cannot catch

Three times in one session a change was committed that read correctly and was
wrong at render:

| What was written | What the diff showed | What the output did |
|---|---|---|
| An SRI hash typed from memory rather than computed | a plausible `sha512-…` | the browser blocked the script and the page came up empty |
| A lookup keyed by field id, built with `merge` | a correct-looking map | `merge` renumbered the integer keys, so every number read zero |
| A Twig comment inside a hash literal | a helpful comment | a syntax error, every record page a 500 |

The common thread is that **the code reads correctly and the output is wrong**,
so the check has to be on the output. Reading the diff again does not help;
neither does `php -l`, which only proves a PHP file parses. A value that was not
computed, a structure the language quietly reshaped, and a construct the parser
rejects all look fine on the page you are editing.

The rule that follows: when a change touches something every page uses, or
carries a value that came from anywhere but a computation you just ran, the last
step before reporting is to fetch the thing and read what came back. Never
report work as done on the strength of the diff alone.
