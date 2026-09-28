#!/bin/sh
# Runs on the Mac before every `ddev start` (pre-start hook, .ddev/config.drive.yaml).
# DDEV v1.25.1 runs pre-start hooks before it writes the compose config
# (ddevapp.go, Start(): ProcessHooks("pre-start") precedes WriteDockerComposeYAML()),
# so the file this writes or removes is the one that start uses.
#
# Drive present: mount only Reggie's SCVHistory folder, read-only, at /mnt/reggie.
# Drive absent:  no mount at all, so the container starts and sees no other volume.
# Plugging the drive in later needs a restart to pick it up.
cd "$(dirname "$0")" || exit 1
if [ -d /Volumes/Reggie/SCVHistory ]; then
  cat > docker-compose.drive.yaml <<'YAML'
# Written by .ddev/drive-mount.sh because Reggie was connected at start. Not committed.
services:
  web:
    volumes:
      - "/Volumes/Reggie/SCVHistory:/mnt/reggie:ro"
YAML
  echo "Reggie connected: mounted read-only at /mnt/reggie"
else
  rm -f docker-compose.drive.yaml
  echo "Reggie not connected: starting without it"
fi
