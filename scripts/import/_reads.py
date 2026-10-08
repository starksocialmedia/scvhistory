# What a script read, stated before it reports any number. The Python side of _reads.php; same entries, same output.
#
#   import sys; sys.path.insert(0, '/var/www/html/scripts/import'); from _reads import reads
#   reads([('file', 'every folder on Reggie, walked', '/mnt/reggie/scvhistory.com'),
#          ('record', 'Craft field legacySourcePath', 'the master on Reggie', 'read')])
#
# Nathan, 8 October 2026: "Before a census reports a number it states what it read, and reading a Craft field about a
# file is not reading the file. Where a census could read the file itself and does not, it says so in its own output."
# Entries: ('file', what, path); ('record', what, describes, 'read' | 'not read: why' | 'cannot be read: why').
# A file entry whose path is not there prints NOT READ. check_census_reads.php (run by check_render) holds every new
# script in scripts/import to this.
import os


def reads(entries):
    out, bad = ['READ, before any number:'], []
    for e in entries:
        kind, what = e[0], e[1]
        if kind == 'file':
            p = e[2] if len(e) > 2 else ''
            out.append(f'  the file itself: {what}' if p and os.path.exists(p) else f'  NOT READ: {what} ({p} is not there)')
        elif kind == 'record':
            about, state = (e[2] if len(e) > 2 else ''), (e[3] if len(e) > 3 else '')
            if state == 'read':
                out.append(f'  a record about a file: {what}, describing {about}; the file was read too')
            elif state.startswith('not read: ') and len(state) > 12:
                out.append(f'  A RECORD, NOT THE FILE: {what} describes {about}; the file could be read and was not ({state[10:]})')
            elif state.startswith('cannot be read: ') and len(state) > 18:
                out.append(f'  A RECORD, NOT THE FILE: {what} describes {about}; the file cannot be read here ({state[16:]})')
            else:
                bad.append(f'{what}: say whether {about} was read, and if not, why')
        else:
            bad.append(f'{what}: kind must be file or record')
    if bad:
        raise SystemExit('reads(): ' + '; '.join(bad))
    print('\n'.join(out))
