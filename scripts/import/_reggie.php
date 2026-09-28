<?php
/**
 * Where the Reggie mirror is, or a clear stop when it is not.
 *
 *   $MIRROR = (require \Craft::getAlias('@root') . '/scripts/import/_reggie.php')('scvhistory.com');
 *
 * Reggie is an external drive and is regularly unplugged. DDEV starts without
 * it (.ddev/docker-compose.drive.yaml mounts /Volumes, and /mnt/reggie is a
 * symlink that dangles while the drive is absent). A script that reads the
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
    $seen = is_dir('/mnt/volumes/Reggie')
        ? 'The drive is mounted on the Mac but /mnt/volumes/Reggie/SCVHistory is missing: check the folder name.'
        : 'The Mac does not see the drive (/Volumes/Reggie is absent).';
    throw new \RuntimeException("Reggie is not connected. $seen This script reads the legacy mirror, so it cannot run without it. "
        . 'Plug the drive in; if it is in and this persists, run: ddev restart');
};
