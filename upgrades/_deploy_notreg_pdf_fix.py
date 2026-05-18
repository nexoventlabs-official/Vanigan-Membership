"""
Deploy fix for the admin "Not Registered" PDF download:
 - downloads ALL filtered records (not just current page) by using
   a new `?export=1` JSON mode in AdminPanelController::notRegistered.

Uploads two files:
  - app/Http/Controllers/AdminPanelController.php
  - resources/views/admin/not-registered.blade.php

Then clears caches (no config:cache - same as v2 deployer).
"""

from __future__ import annotations

import base64
import hashlib
import posixpath
import sys
from pathlib import Path

import paramiko

HOST     = "174.138.49.116"
PORT     = 22
USERNAME = "master_ykechncjba"
PASSWORD = "TkgqJ3DcqExc"

APP_ROOT = "/home/master/applications/ewpqehegpr/public_html"
LOCAL_ROOT = Path(__file__).resolve().parent.parent

FILES_TO_UPLOAD = [
    "app/Http/Controllers/AdminPanelController.php",
    "resources/views/admin/not-registered.blade.php",
]


def log(tag: str, msg: str) -> None:
    print(f"[{tag}] {msg}", flush=True)


def ssh_run(ssh, cmd, *, check=True, quiet=False):
    if not quiet:
        log("ssh", cmd)
    _, stdout, stderr = ssh.exec_command(cmd, timeout=120)
    out = stdout.read().decode(errors="replace")
    err = stderr.read().decode(errors="replace")
    rc = stdout.channel.recv_exit_status()
    if out.strip() and not quiet:
        print(out.rstrip())
    if err.strip() and not quiet:
        print("STDERR:", err.rstrip())
    if check and rc != 0:
        raise SystemExit(f"Command failed (rc={rc}): {cmd}")
    return rc, out, err


def upload_via_b64(ssh, local: Path, remote: str) -> None:
    data = local.read_bytes()
    expected_sha = hashlib.sha256(data).hexdigest()
    expected_size = len(data)
    b64 = base64.b64encode(data).decode("ascii")

    parent = posixpath.dirname(remote)
    ssh_run(ssh, f"mkdir -p {parent}", quiet=True)

    # Backup (keep first backup; -n won't overwrite an existing .bak)
    ssh_run(ssh, f"cp -n {remote} {remote}.bak.notregpdf 2>/dev/null || true",
            quiet=True, check=False)

    cmd = f"echo '{b64}' | base64 -d > {remote}"
    ssh_run(ssh, cmd, quiet=True)

    rc, out, _ = ssh_run(
        ssh,
        f"stat -c '%s' {remote} && sha256sum {remote} | awk '{{print $1}}'",
        quiet=True,
    )
    lines = [l.strip() for l in out.splitlines() if l.strip()]
    if len(lines) < 2:
        raise SystemExit(f"Verification output malformed for {remote}: {out!r}")
    remote_size = int(lines[0])
    remote_sha = lines[1]
    if remote_size != expected_size or remote_sha != expected_sha:
        raise SystemExit(
            f"Verification FAILED for {remote}\n"
            f"  expected size={expected_size} sha256={expected_sha}\n"
            f"  got      size={remote_size} sha256={remote_sha}"
        )
    log("ok", f"{remote}  ({remote_size} bytes)")


def rebuild_caches(ssh) -> None:
    cmds = [
        "php artisan config:clear",
        "php artisan route:clear",
        "php artisan view:clear",
        "php artisan cache:clear",
        "php artisan route:cache",
        "php artisan view:cache",
    ]
    for c in cmds:
        ssh_run(ssh, f"cd {APP_ROOT} && {c}")


def smoke_check(ssh) -> None:
    log("info", "Checking export endpoint responds with JSON ...")
    # Just confirm the route exists; we cannot auth as admin from CLI here.
    ssh_run(
        ssh,
        f"cd {APP_ROOT} && php artisan route:list --columns=method,uri,name "
        f"| grep -E 'admin/not-registered' || echo 'ROUTE_NOT_FOUND'",
        check=False,
    )


def main() -> int:
    log("info", f"Connecting to {USERNAME}@{HOST}:{PORT} ...")
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(
        hostname=HOST, port=PORT,
        username=USERNAME, password=PASSWORD,
        look_for_keys=False, allow_agent=False, timeout=30,
    )
    try:
        log("info", f"Target app root: {APP_ROOT}")
        ssh_run(ssh, f"test -f {APP_ROOT}/artisan && echo OK_HAS_ARTISAN")

        log("info", "Uploading files ...")
        for rel in FILES_TO_UPLOAD:
            local_path = LOCAL_ROOT / rel
            if not local_path.exists():
                raise SystemExit(f"Local file missing: {local_path}")
            remote_path = posixpath.join(APP_ROOT, rel)
            upload_via_b64(ssh, local_path, remote_path)

        rebuild_caches(ssh)
        smoke_check(ssh)

        log("done", "Not-Registered PDF fix deployed.")
    finally:
        ssh.close()
    return 0


if __name__ == "__main__":
    sys.exit(main())
