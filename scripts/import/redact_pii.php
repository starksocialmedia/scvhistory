<?php
/**
 * Redacts telephone numbers and street addresses from a snippet of source
 * text before it is stored next to a name in a review file.
 *
 * The review files (entities*.json, records*.json and what reads them) carry
 * sentences lifted from body_text to show a name in use. A sentence like
 * "call Jane Doe at 555-0100" or "Doe was living at 123 Elm Street" then puts a
 * person, a phone number and a home side by side in a working file that is
 * copied around far more freely than the article it came from. The sentence
 * still shows the name in use without them.
 *
 * Only the stored snippet is changed. body_text, the fidelity dumps and the
 * published article are verbatim legacy text and are never touched.
 *
 *   phones     555-0100 style numbers with an optional area code in any of
 *              (661) 555-0100, 661-555-0100, 661/555-0100, 1-800-555-0100;
 *              and the old exchange form of the business directories,
 *              "Phone Newhall 62", "Phone 850", "Phone Newhall 1575 & Newhall
 *              1-1126".
 *              A digit run glued to a hyphen or a letter (a URL slug, a
 *              catalogue number, 4-20-1919) is not a phone.
 *   addresses  house number + one to three capitalised street words + a street
 *              suffix: "24514 Kansas Street", "745 Spruce St.", "23938 West
 *              Lyons Ave."; a Spanish prefix street, "23918 Via Onda"; a
 *              numbered highway, "18912 Highway 99". A four digit number from 1800 to 2030 is read as
 *              a year and left alone ("June 27, 2005 Newhall Avenue", "the
 *              1921 Cabin ... Road"), as is "Dr" (a byline: "Part 2 By Dr."),
 *              and a street with no number ("Spruce Street") is not an address.
 *
 * Usage: $redactPii = require $root . '/scripts/import/redact_pii.php';
 *        $clean = $redactPii($text);
 */

return function (string $s): string {
    /* old exchange numbers first, so the modern pattern cannot eat half of one */
    $s = preg_replace(
        '~\b(Phone|Tel\.?|Telephone)(:?\s+)(?:[A-Z][a-z]+\s+)?\d{1,5}[A-Z]?\b(?:\s*&\s*[A-Z][a-z]+\s+\d{1,2}-\d{4}\b)?~u',
        '$1$2[phone redacted]', $s);
    $s = preg_replace(
        '~(?<![\w-])(?:1[-.\s])?(?:\(\d{3}\)\s*|\d{3}[-./\s])?\d{3}[-.]\d{4}(?![\w-])~u',
        '[phone redacted]', $s);

    $SUFFIX = 'Street|Avenue|Road|Drive|Boulevard|Lane|Way|Court|Place|Circle|Terrace|Parkway|Highway'
            . '|(?:St|Ave|Rd|Blvd|Ln|Ct|Pl|Cir|Pkwy|Hwy)\b\.?';
    $WORD = "(?!(?:The|By|And|Of|In|At|On|To|A|An)\\s)(?:[A-Z][A-Za-z'\x{2019}.\\-]*|\\d+(?:st|nd|rd|th))";
    /* street words then a suffix; a Spanish prefix street (Via Onda, Calle
       Real, Camino Del Sur); a numbered highway (18912 Highway 99) */
    $PREFIX = "(?:Via|Calle|Camino|Paseo|Avenida)\\s+(?:[A-Z][A-Za-z'\x{2019}\\-]*|de|del|la|los|las)(?:\\s+[A-Z][A-Za-z'\x{2019}\\-]*)?";
    $NUMBERED = '(?:Highway|Hwy\.?|State\s+Route|Route)\s+\d{1,3}';
    return preg_replace_callback(
        '~(?<![\w-])(\d{1,6})\s+(?:(?:' . $WORD . '\s+){1,3}(?:' . $SUFFIX . ')|' . $PREFIX . '|' . $NUMBERED . ')(?![\w-])~u',
        function ($m) {
            $n = $m[1];
            if (strlen($n) === 4 && (int)$n >= 1800 && (int)$n <= 2030) { return $m[0]; }
            return '[address redacted]';
        }, $s);
};
