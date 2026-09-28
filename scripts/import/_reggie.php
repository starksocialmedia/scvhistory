<?php
/**
 * Where the Reggie mirror is, or a clear stop when it is not.
 *
 *   $MIRROR = (require \Craft::getAlias('@root') . '/scripts/import/_reggie.php')('scvhistory.com');
 *
 * Reggie is an external drive and is regularly unplugged. DDEV starts without
 * it: .ddev/drive-mount.sh, a pre-start hook, mounts it at /mnt/reggie only when
 * it is connected at start, and mounts nothing otherwise. A script that reads the
 * mirror calls this first, and gets the absolute path, or a RuntimeException
 * that says what is missing and what to do, before it has done anything.
 */

return function (string $sub = ''): string {
    $root = '/mnt/reggie';
    $path = rtrim($root . '/' . ltrim($sub, '/'), '/');
    if (is_dir($root . '/scvhistory.com')) {
        if ($sub === '' || file_exists($path)) { return $path; }
        throw new \RuntimeException("Reggie is connected, but $path is not on it.");
    }
    throw new \RuntimeException('Reggie is not connected: there is no /mnt/reggie in the container. This script reads the legacy mirror, '
        . 'so it cannot run without it. Plug the drive in, then restart DDEV (warn first, and wait for `ddev mutagen status` to read ok); '
        . '.ddev/drive-mount.sh mounts it at start.');
};
