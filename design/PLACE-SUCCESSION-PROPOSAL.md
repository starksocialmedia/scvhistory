# Proposal: precededBy and succeededBy on Place

Not built. This is a proposal to read and decide on.

## What is missing

The archive can say where a place is and what community it sits in. It cannot
say that one piece of ground carried a different name before, or after. That
is the shape of most of this valley's history:

- Lyon's Station becomes Newhall
- Rancho San Francisco becomes Newhall Ranch becomes Valencia
- Rancho Placeritos becomes the Monogram Ranch becomes Melody Ranch
- Spruce Street becomes San Fernando Road
- The Swall Hotel becomes Newhall Pharmacy

Following one piece of ground through its names is the story a reader comes for,
and the graph cannot draw it today.

## Where that history currently lives

Two places: the alias field, and the prose.

| Record | placeAliases |
|---|---|
| Beale's Cut Stagecoach Pass | Fremont Pass, San Fernando Pass, Newhall Cut |
| Lyons Station Stagecoach Stop | Lyon's Station, Hart's Station, Wiley's Station |

Those are not aliases. An alias is a name a thing is also known by, at the same
time; these are names it was known by **in sequence**, and the order is the
information. Flattening them into a comma-separated field loses which came
first, and loses the fact that Wiley's Station became Hart's Station because
Wiley sold it.

In the article bodies, 29 sentences describe a renaming: "renamed", "later
known as", "now called", "formerly". That is the evidence a fill would draw on,
and it is already written down.

## The proposal

Two Entries fields on the place entry type, pointing at places:

| Field | Name | Note |
|---|---|---|
| `precededBy` | Preceded by | the place this one was before it was this |
| `succeededBy` | Succeeded by | what this place became |

Sources: `places` only.

Instructions on `precededBy`: *"The same ground under an earlier name. Lyon's
Station precedes Newhall. Only where the source says so; a place that merely
came before in time is not its predecessor."*

### Why two fields and not one

One field plus a direction flag would be fewer moving parts, and it is the wrong
trade. The pair reads correctly from either end in the CP without anybody
remembering which way round the flag points, and the render side wants both
directions anyway: a place page shows "before this" above and "after this"
below.

### Whether to store both directions

No. **Store `succeededBy` only and derive `precededBy`**, exactly as `childOf`
now works for family. One stored direction, one place to type, nothing to
contradict. The field table above therefore becomes one field on the layout,
`succeededBy`, with `precededBy` hidden and derived.

That is a change from the obvious design, and it is the same lesson: two stored
directions for one fact is two places for it to disagree.

## What it would take

1. `add_place_succession.php`, on the pattern of `add_gnis_field.php`: create
   `succeededBy` as an Entries field scoped to places, add it to the place
   layout in an Identity tab, write the instructions.
2. Derive `precededBy` in `_layouts/base.twig` beside the family derivation,
   from `relatedTo({ targetElement: entry, field: 'succeededBy' })`.
3. Render a succession strip on the place record: *Lyon's Station → **Newhall**
   → Valencia*, each name a link, the current record unlinked. This is the part
   worth building; the field on its own shows nothing.
4. Emit it in the JSON-LD. Schema.org has no succession property for a Place.
   The honest mapping is `additionalProperty` with a `PropertyValue`, or
   `sameAs` where the predecessor has its own authority record. It should not be
   forced into `containedInPlace`, which means something else.
5. Draw it in `/graph` as a distinct edge kind, so a chain of names reads as a
   chain rather than as another undifferentiated line.
6. Seed it from the two alias fields above, which are succession chains already,
   and propose the rest from the 29 renaming sentences through a review screen
   on the same pattern as place links. Nothing auto-applied: "now known as" in a
   body is evidence, not a decision.

## What it costs

The alias fields would need splitting, and that is a content decision per
record: which of "Fremont Pass, San Fernando Pass, Newhall Cut" are successive
names and which are contemporaneous variants. For Beale's Cut I would guess
those are three names for one cut in one period, which is an alias list and
should stay one. For Lyons Station the three are successive owners, which is a
chain. So the two examples that prompted this need opposite treatment, and
neither should be decided by a script.

## Recommendation

Worth building, after the relation review rather than before it. The succession
chains are a small, high-value set — a dozen or two across the valley — and they
are the spine a reader follows. But they are hand-made facts, and the review
queue is already the bottleneck. Adding a field now with nothing in it makes the
audit report a new gap on fifteen records and shows nobody anything.
