# design/article-source.html, reproduced

`templates/articles/_entry.twig` rebuilt from the design. Inline styles kept, as
`site-header.twig` keeps them from `menu-source.html`: 39 inline styles, no
`{% css %}` block. Verified on `chapter-9-the-trail-blazer`, which is the record
the design was drawn from.

The design's own illustrative numbers come out as live values: **YOU ARE ON 11
OF 80**, **14%**, **11 items**, gap labels **4 earlier articles** and **63 more
articles**, legacy path `/scvhistory/signal/reynolds/part09.html`. Article
column 760px, sidebar 344px, body 595px at 34em.

## The ten, each verified

1. **Band.** One image layer at `background-position: right center`, one
   `linear-gradient(100deg, …)` over it. The two-layer mask is gone. Breadcrumb
   inside the band above the kicker; kicker is ARTICLES, a 22px gold rule, the
   era. Title, collection in Playfair italic 22px as a link, byline, chips.
2. **Chips.** Era outlined gold on `rgba(255,255,255,.55)`; communities solid
   navy, white text. Rendered: `Pre-1850` gold, `Agua Dulce` `Castaic` `Lebec`
   navy.
3. **Tools row collapses.** Listen, the meta line, Print and Share. The player
   is `hidden` until Listen is pressed, then a cream box below. Toggling
   verified: `playerHidden true → false → true`, `aria-expanded` following.
   Pressing Listen also starts playback; pressing it again pauses and hides.
4. **Lead.** Playfair 24px navy at 34em. `subheadline` where one exists, else
   the first paragraph promoted out of the body and the body started at the
   second, else nothing. On chapter 9 it is the Fages identification, as drawn.
5. **Body.** 34em, 17.5px, `display: flex; flex-direction: column; gap: 22px`.
6. **Signature.** A 1px rule, then `Jerry Reynolds · 1976` in Playfair
   small-caps 17px navy.
7. **Photos and documents.** 4:5 cards, `minmax(150px, 1fr)`, under a 3px gold
   rule with the count. 11 cards on chapter 9.
8. **About the author.** 96px circular portrait with a 2px gold border, name in
   Playfair 25px, occupation, the person's prose, VIEW FULL PROFILE.
9. **Sidebar rebuilt.** Collection card with the band image cropped at
   `object-position: 72% center`, title, author and count, YOU ARE ON with a
   percentage and a progress bar. Windowed chapter list with gap labels, the
   current one with a gold left rule and set in Playfair. Previous and Next
   buttons. People with 44px portraits. Cite. Record facts with the legacy link
   and the path in mono beneath.
10. **NOTE A FIX** restyled to the design: navy, gold border, `.14em` Jost,
    `11px 17px`, fixed at `right: 20px; bottom: 20px; z-index: 30`. It is the
    existing capture, not a new button.

## What I could not reproduce faithfully, and why

**The cite box.** The design draws its own markup: tabs as bordered buttons,
the citation on `#FBFAF6` with a gold left rule, a copy button beneath. I kept
`_partials/record/cite.twig`, which renders the same three tabs and the same
copy behaviour in slightly different markup. Rebuilding it to the design's
markup means moving its JavaScript too, and that JavaScript is shared with
eleven other record templates that are not being redesigned. Doing it here
would either fork the partial or change every other record page. Say which and
I will do it.

**The photo cards are links to the asset, not `image-slot` placeholders.** The
design uses `<image-slot>` custom elements with placeholder captions. Those are
a drawing device; I rendered real assets, cropped to 4:5, opening the existing
lightbox.

**The header and footer are the existing partials**, which already reproduce
`menu-source.html`. The design file includes them for context and they match,
except for the account menu.

**Left out entirely, as instructed:** the account menu with its avatar and
saved/notes/drafts panel, and the Save button in the tools row.

## One thing the design implies that the data does not carry

The design's Record facts table is five rows. On chapter 9 the live record
yields five: PUBLISHED, AS PRINTED, PUBLISHER, ERA, UPDATED. On a record with
no publisher or era it will be shorter, and on a standalone article with no
collection the whole first sidebar card is absent. That is data, not layout,
and the design has no drawn state for it; the card simply does not render.
