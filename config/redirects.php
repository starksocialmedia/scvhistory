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
    /* Mission San Francisco de Asís and Mission Santa Cruz were retired on
       3 October 2026 (fold_and_retire_records.php: nothing holds them to the
       valley); their organization addresses no longer redirect. */
    /* #16356 renamed from Bill Hart, 29 September 2026 (import_hart_portrait.php). */
    'persons/bill-hart'                           => 'persons/william-s-hart',
    /* #16052 retitled Hart High School, 30 September 2026 (retitle_hart_high.php). */
    'organizations/william-s-hart-high-school'    => 'organizations/hart-high-school',
    /* #25435 Gloria Mercado merged into #25445, 30 September 2026 (merge_held_names.php). */
    'persons/gloria-mercado'                      => 'persons/gloria-mercado-fortine',
    /* Duplicate person records retired into the full record, 29 September 2026
       (merge_duplicate_persons.php). */
    'persons/edward-f-beale'                      => 'persons/edward-fitzgerald-beale',
    'persons/kit-carson-2'                        => 'persons/kit-carson',
    'persons/henry-m-newhall'                     => 'persons/henry-mayo-newhall',
    'persons/james-marshall'                      => 'persons/james-w-marshall',
    /* #305, Reynolds's joined name for the gold discoverer and his cousin Chico,
       retired 3 October 2026 (retire_305_add_chico_lopez.php); the address
       goes to the discoverer, #18834. Chico has his own, persons/chico-lopez. */
    'persons/francisco-lopez'                     => 'persons/francisco-lopez-2',
    /* Folded 3 October 2026 (fold_and_retire_records.php): the California
       Battalion into Frémont, who holds his "buckskin battalion"; the
       Catalonian Volunteers into the Portolá Expedition they marched with. */
    'groups/california-battalion'                 => 'persons/john-c-fremont',
    'groups/catalonian-volunteers'                => 'groups/portola-expedition',
    /* Retired into the one record for the same subject, 29 September 2026
       (organize_orgs_places.php). */
    'organizations/rancho-camulos'                => 'places/rancho-camulos',
    'places/lake-hughes'                          => 'communities/lake-hughes',
    /* War memorial records renamed from their legacy page keys to their names,
       29 September 2026 (apply_review_fixes_0929.php). */
    'war-memorial/ww2-tomross'                    => 'war-memorial/thomas-milton-ross-jr',
    'war-memorial/ww2-robertfose'                 => 'war-memorial/robert-remy-fose',
    'war-memorial/ww2-robertcone'                 => 'war-memorial/robert-russell-cone',
    'war-memorial/ww2-ozalsmart'                  => 'war-memorial/ozal-r-smart',
    'war-memorial/ww2-leoncherry'                 => 'war-memorial/perry-leon-cherry',
    'war-memorial/ww2-johnward'                   => 'war-memorial/john-amos-ward',
    'war-memorial/ww2-johnnycordova'              => 'war-memorial/johnny-cordova',
    'war-memorial/ww2-jimbartlett'                => 'war-memorial/james-a-bartlett',
    'war-memorial/ww2-jamesredman'                => 'war-memorial/james-m-redmond',
    'war-memorial/ww2-jackharland'                => 'war-memorial/jack-lewis-harland',
    'war-memorial/ww2-garrywingfield'             => 'war-memorial/garry-wingfield',
    'war-memorial/korea-albertthomas'             => 'war-memorial/albert-edward-thomas',
    'war-memorial/korea-donaldmorissett'          => 'war-memorial/donald-e-morissett',
    'war-memorial/korea-gilbertmontenegro'        => 'war-memorial/gilbert-d-montenegro',
    'war-memorial/korea-henryacuna'               => 'war-memorial/henry-acuna',
    'war-memorial/korea-raymondkelly'             => 'war-memorial/raymond-gene-kelly',
    'war-memorial/korea-robertwhisler'            => 'war-memorial/robert-l-whisler',
    'war-memorial/terror-brianprosser'            => 'war-memorial/brian-cody-prosser',
    'war-memorial/terror-colelarsen'              => 'war-memorial/cole-william-larsen',
    'war-memorial/terror-deantodd'                => 'war-memorial/dean-glenn-todd-jr',
    'war-memorial/terror-dennissellen'            => 'war-memorial/dennis-lee-sellen-jr',
    'war-memorial/terror-iangelig'                => 'war-memorial/ian-timothy-d-gelig',
    'war-memorial/terror-jakesuter'               => 'war-memorial/jake-william-suter',
    'war-memorial/terror-johnconant'              => 'war-memorial/john-michael-conant',
    'war-memorial/terror-josefloresmejia'         => 'war-memorial/jose-ricardo-flores-mejia',
    'war-memorial/terror-richardslocum'           => 'war-memorial/richard-patrick-slocum',
    'war-memorial/terror-robertwilson'            => 'war-memorial/robert-michael-wilson',
    'war-memorial/terror-rudyacosta'              => 'war-memorial/rudy-alexander-acosta',
    'war-memorial/terror-stephencolley'           => 'war-memorial/stephen-edward-colley',
    'war-memorial/ww2-albertmoore'                => 'war-memorial/albert-lee-moore',
    'war-memorial/ww2-archibaldbeall'             => 'war-memorial/archibald-k-archie-beall',
    'war-memorial/ww2-augustrubel'                => 'war-memorial/augustus-a-august-rubel',
    'war-memorial/ww2-edwardcontreras'            => 'war-memorial/edward-d-contreras',
    'war-memorial/ww2-ekenaston'                  => 'war-memorial/lawrence-e-kenaston',
    'war-memorial/ww2-eugenedarr'                 => 'war-memorial/eugene-e-darr',
    'war-memorial/ww2-frankwhitmore'              => 'war-memorial/frank-pike-whitmore',
];

return array_map(fn($from, $to) => ['from' => $from, 'to' => $to, 'statusCode' => 301],
    array_keys($moved), array_values($moved));
