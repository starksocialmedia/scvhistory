<?php
/**
 * Redirection Rules
 *
 * Evaluated only after Craft would otherwise 404, so a rule for a live page
 * does nothing until that page goes away. That is what the organization rules
 * below rely on: each fires once its organization is disabled.
 *
 * Organizations moved to places, 25 September 2026
 * (scripts/import/convert_orgs_to_places.php). The organization records were
 * public for four days from 21 September and may be indexed, so each old URL
 * sends a 301 to the place that now holds what it held. The three missions
 * redirect once add_place_website_field.php has run and they retire; until then
 * their organization pages are live and these rules are dormant.
 *
 * Not here: Rancho Camulos (#384) is still live by decision, and Harvey Stack
 * (#18806) was a person misfiled as an organization and has no successor.
 *
 * @link https://craftcms.com/docs/5.x/system/routing.html#redirection
 */

$moved = [
    'organizations/acton-hotel'                   => 'places/acton-hotel',
    'organizations/southern-hotel'                => 'places/southern-hotel',
    'organizations/pioneer-oil-refinery'          => 'places/pioneer-oil-refinery',
    'organizations/porta-bella'                   => 'places/porta-bella',
    'organizations/valencia-marketplace'          => 'places/valencia-marketplace',
    'organizations/felton-school'                 => 'places/felton-school',
    'organizations/rancho-san-francisco'          => 'places/rancho-san-francisco',
    'organizations/rancho-el-tejon'               => 'places/rancho-el-tejon',
    'organizations/mission-san-gabriel-arcangel'  => 'places/mission-san-gabriel-arcángel',
    'organizations/mission-san-francisco-de-asis' => 'places/mission-san-francisco-de-asís',
    'organizations/mission-santa-cruz'            => 'places/mission-santa-cruz',
    /* #16356 renamed from Bill Hart, 29 September 2026 (import_hart_portrait.php). */
    'persons/bill-hart'                           => 'persons/william-s-hart',
    /* Duplicate person records retired into the full record, 29 September 2026
       (merge_duplicate_persons.php). */
    'persons/edward-f-beale'                      => 'persons/edward-fitzgerald-beale',
    'persons/kit-carson-2'                        => 'persons/kit-carson',
    'persons/henry-m-newhall'                     => 'persons/henry-mayo-newhall',
    'persons/james-marshall'                      => 'persons/james-w-marshall',
];

return array_map(fn($from, $to) => ['from' => $from, 'to' => $to, 'statusCode' => 301],
    array_keys($moved), array_values($moved));
