<?php
/**
 * Opens an asset's files and says whether they carry a generated element (Nathan, 9 October 2026: "The no-generated-image
 * check passed yesterday with Cooper's generated portrait live on his record, because it reads fields rather than files
 * ... Rewrite it to open the file"). Used by check_generated_files.php (run by check_render) and build_withheld_media.php.
 *
 *   $scan = require \Craft::getAlias('@root') . '/scripts/import/_generated_scan.php';
 *   $r = $scan($asset);   // ['read' => [paths opened], 'unread' => [why], 'class' => ..., 'why' => [...]]
 *
 * The files opened, every one of them, never a field in their place:
 *   - the master as it arrived: on Reggie at legacySourcePath, or a held file under inventory/ or storage/ whose SHA-256 or
 *     SHA-1 is the recorded sourceChecksum (hashed here; a path index is cached by size and modified time, and the matched
 *     file is hashed again before it is trusted);
 *   - the stored copy in the volume. Craft re-saves uploads, so the stored copy can lose a credential the master carries;
 *     a stored copy alone is reported as such.
 *   - a manifest held elsewhere (XMP dcterms:provenance naming Adobe's manifest store), fetched once and kept in
 *     storage/runtime/manifests/, then read from there.
 * The bytes are searched in 8 MB chunks for strings long enough not to occur by chance in compressed data.
 *
 * Classes:
 *   'generated'       a generator named (gpt-image, Midjourney, Stable Diffusion, DALL-E), or a C2PA claim declaring
 *                     trainedAlgorithmicMedia with no ingredient (nothing photographed in the chain);
 *   'text-prompt-step' a step whose Firefly operation is text_to_image, in a chain that does hold an original (Couts,
 *                     9 October; Firefly Image 5 records its prompt-driven edits this way);
 *   'generative-edit' a content credential or generative source type with an original among its ingredients, or Firefly
 *                     named, and no text-to-image step;
 *   'none-found'      nothing of the above in any file opened. This says no marker is present, not that the file is unedited.
 *   'not-read'        no file could be opened.
 */
use craft\elements\Asset;

return (function () {
    $root = \Craft::getAlias('@root');
    $REGGIE = '/mnt/reggie/scvhistory.com';
    $HELD = ['inventory', 'storage/masters', 'storage/runtime/photo-import', 'storage/runtime/titles-vs-scans'];
    $IDX = "$root/storage/runtime/master-hash-index.json";
    $MAN = "$root/storage/runtime/manifests";
    $index = null; $byHash = null;

    $grep = function (string $path, array $needles): array {
        $hit = [];
        $fh = @fopen($path, 'rb'); if (!$fh) { return ['_unreadable' => true]; }
        $tail = '';
        while (!feof($fh)) {
            $c = fread($fh, 8 << 20); if ($c === false || $c === '') { break; }
            $buf = $tail . $c;
            foreach ($needles as $n) { if (!isset($hit[$n]) && strpos($buf, $n) !== false) { $hit[$n] = true; } }
            $tail = substr($buf, -64);
        }
        fclose($fh);
        return $hit;
    };

    $buildIndex = function () use ($root, $HELD, $IDX, &$index, &$byHash) {
        $old = is_file($IDX) ? (json_decode((string)file_get_contents($IDX), true) ?: []) : [];
        $index = [];
        foreach ($HELD as $top) {
            if (!is_dir("$root/$top")) { continue; }
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("$root/$top", \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if (!$f->isFile() || !preg_match('~\.(jpe?g|png|gif|tiff?|webp|heic|avif|svg|pdf)$~i', $f->getFilename())) { continue; }
                $p = $f->getPathname(); $k = $f->getSize() . ':' . $f->getMTime();
                if (($old[$p]['k'] ?? null) === $k) { $index[$p] = $old[$p]; continue; }
                $index[$p] = ['k' => $k, 'sha256' => hash_file('sha256', $p), 'sha1' => hash_file('sha1', $p)];
            }
        }
        @file_put_contents($IDX, json_encode($index));
        $byHash = [];
        foreach ($index as $p => $h) { $byHash['sha256:' . $h['sha256']] ??= $p; $byHash['sha1:' . $h['sha1']] ??= $p; }
    };

    $manifest = function (string $url) use ($MAN): ?string {
        @mkdir($MAN, 0777, true);
        $f = "$MAN/" . preg_replace('~[^a-z0-9-]~i', '_', basename($url));
        if (is_file($f) && filesize($f) > 0) { return $f; }
        sleep(2);
        $b = @file_get_contents($url, false, stream_context_create(['http' => ['header' => "User-Agent: SCVHistory archive provenance check\r\n", 'timeout' => 30]]));
        if (!$b) { return null; }
        file_put_contents($f, $b);
        return $f;
    };

    $NEEDLES = ['c2pa.claim', 'urn:c2pa:', 'cai-manifests.adobe.com/manifests/', 'rainedAlgorithmicMedia', 'compositeWithTrainedAlgorithmicMedia',
        'text_to_image', 'c2pa.created', 'gpt-image', 'Midjourney', 'Stable Diffusion', 'DALL-E', 'DALL·E', 'Adobe Firefly', 'Firefly Image',
        'parentOf', 'inputTo', 'c2pa.ingredient'];

    return function (Asset $a) use ($root, $REGGIE, $grep, $buildIndex, $manifest, $NEEDLES, &$index, &$byHash): array {
        $L = $a->getFieldLayout();
        $f = fn($h) => ($L && $L->getFieldByHandle($h)) ? trim((string)$a->getFieldValue($h)) : '';
        $read = []; $unread = []; $hits = [];
        $paths = [];
        $lsp = $f('legacySourcePath');
        if ($lsp !== '') {
            $p = $REGGIE . '/' . ltrim(preg_replace('~^https?://[^/]+~', '', $lsp), '/');
            if (is_file($p)) { $paths['master on Reggie'] = $p; } else { $unread[] = is_dir($REGGIE) ? "master named on Reggie, not there ($lsp)" : 'Reggie not mounted'; }
        }
        $sum = strtolower($f('sourceChecksum'));
        if (!$paths && $sum !== '') {
            if ($byHash === null) { $buildIndex(); }
            $p = $byHash[$sum] ?? null;
            if ($p && is_file($p) && (str_starts_with($sum, 'sha1:') ? 'sha1:' . hash_file('sha1', $p) : 'sha256:' . hash_file('sha256', $p)) === $sum) { $paths['master held in the repo'] = $p; }
            elseif (!$p) { $unread[] = 'no held file has the recorded checksum'; }
        }
        $fs = $a->getVolume()->getFs(); $stored = rtrim(\Craft::parseEnv($fs->path ?? ''), '/') . '/' . $a->getPath();
        if (is_file($stored)) { $paths['stored copy'] = $stored; } else { $unread[] = 'stored copy missing'; }
        foreach ($paths as $what => $p) {
            $h = $grep($p, $NEEDLES);
            if (isset($h['_unreadable'])) { $unread[] = "$what could not be opened"; continue; }
            $read[] = "$what: $p"; $hits += $h;
            if (isset($h['cai-manifests.adobe.com/manifests/'])) {
                $raw = (string)file_get_contents($p);
                foreach (array_unique(preg_match_all('~https://cai-manifests\.adobe\.com/manifests/[\w-]+~', $raw, $m) ? $m[0] : []) as $u) {
                    $mf = $manifest($u);
                    if (!$mf) { $unread[] = "manifest $u could not be fetched"; continue; }
                    $read[] = "its manifest: $u"; $hits += $grep($mf, $NEEDLES);
                }
            }
        }
        $why = array_keys(array_diff_key($hits, ['parentOf' => 1, 'inputTo' => 1, 'c2pa.ingredient' => 1]));
        $ingredient = isset($hits['parentOf']) || isset($hits['inputTo']) || isset($hits['c2pa.ingredient']);
        $claim = isset($hits['c2pa.claim']) || isset($hits['urn:c2pa:']) || isset($hits['cai-manifests.adobe.com/manifests/']);
        if (!$read) { $class = 'not-read'; }
        elseif (isset($hits['gpt-image']) || isset($hits['Midjourney']) || isset($hits['Stable Diffusion']) || isset($hits['DALL-E']) || isset($hits['DALL·E'])
            || (isset($hits['rainedAlgorithmicMedia']) && !$ingredient && !isset($hits['compositeWithTrainedAlgorithmicMedia']))) { $class = 'generated'; }
        elseif (isset($hits['text_to_image'])) { $class = 'text-prompt-step'; }
        elseif ($claim || isset($hits['rainedAlgorithmicMedia']) || isset($hits['Adobe Firefly']) || isset($hits['Firefly Image'])) { $class = 'generative-edit'; }
        else { $class = 'none-found'; }
        return ['read' => $read, 'unread' => $unread, 'class' => $class, 'why' => $why, 'ingredient' => $ingredient,
            'masterRead' => (bool)array_filter(array_keys($paths), fn($k) => str_starts_with($k, 'master')), 'paths' => $paths];
    };
})();
