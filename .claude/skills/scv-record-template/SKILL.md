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

## Before you say it works

Craft serves compiled templates from cache, so a broken template can keep
rendering and fail later. Clear `storage/runtime/compiled_templates` before
trusting any before-and-after check. Then load a real page of every type you
touched and confirm a 200, not just that the file parses.

Twig 3.21 cannot call a variable holding an arrow function. Build a plain
dictionary instead. `merge` reindexes integer keys, so use string keys when
building a lookup.
