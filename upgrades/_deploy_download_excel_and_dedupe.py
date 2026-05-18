"""
Deploy two related fixes:

  1. Loan-request duplicates fix:
       - VanigamController::loanRequest now returns "already applied" instead of
         inserting a duplicate row.
       - MongoService::getAllLoanRequests de-duplicates by unique_id on read
         (keeps the most recent record per member).

  2. Download menu (PDF + Excel) on admin + sub-admin:
       - reports, loan-requests, not-registered pages (6 blades total).
       - SubAdminPanelController::notRegistered gains an `?export=1` JSON mode
         that mirrors the admin version for client-side Excel/PDF generation.
       - sub-admin layout preloads SheetJS and the toggle helper JS.

Uploads via SSH stdin-stream (bypasses Cloudways chrooted SFTP).
Then clears + re-caches Laravel caches.
"""

from __future__ import annotations

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
    # Backend
    "app/Http/Controllers/VanigamController.php",
    "app/Http/Controllers/SubAdminPanelController.php",
    "app/Services/MongoService.php",
    # Admin views
    "resources/views/admin/reports.blade.php",
    "resources/views/admin/loan-requests.blade.php",
    "resources/views/admin/not-registered.blade.php",
    # Sub-admin views
    "resources/views/sub_admin/layout.blade.php",
    "resources/views/sub_admin/reports.blade.php",
    "resources/views/sub_admin/loan-requests.blade.php",
    "resources/views/sub_admin/not-registered.blade.php",
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


def upload_via_stdin(ssh, local: Path, remote: str) -> None:
    """Stream raw bytes through `cat > file` via ssh stdin (no ARG_MAX limit)."""
    data = local.read_bytes()
    expected_sha = hashlib.sha256(data).hexdigest()
    expected_size = len(data)

    parent = posixpath.dirname(remote)
    ssh_run(ssh, f"mkdir -p {parent}", quiet=True)
    ssh_run(ssh, f"cp -n {remote} {remote}.bak.dlexcel 2>/dev/null || true",
            quiet=True, check=False)

    tmp = remote + ".uploading"
    stdin, stdout, stderr = ssh.exec_command(f"cat > {tmp}", timeout=300)
    chan = stdout.channel
    view = memoryview(data)
    CHUNK = 32 * 1024
    for i in range(0, len(view), CHUNK):
        stdin.write(view[i:i+CHUNK])
        stdin.flush()
    stdin.channel.shutdown_write()
    rc = chan.recv_exit_status()
    err = stderr.read().decode(errors="replace")
    if rc != 0:
        raise SystemExit(f"stdin upload failed for {remote} (rc={rc}): {err}")

    ssh_run(ssh, f"mv -f {tmp} {remote}", quiet=True)

    _, out, _ = ssh_run(
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


def quick_grep(ssh) -> None:
    log("info", "Sanity-checking deployed files ...")
    checks = [
        (
            "already_applied",
            "app/Http/Controllers/VanigamController.php",
            "OK_VANIGAM_DEDUP_GUARD",
        ),
        (
            "seenUniqueIds",
            "app/Services/MongoService.php",
            "OK_MONGO_DEDUP",
        ),
        (
            "downloadExcel",
            "resources/views/admin/reports.blade.php",
            "OK_ADMIN_REPORTS_EXCEL",
        ),
        (
            "downloadExcel",
            "resources/views/admin/loan-requests.blade.php",
            "OK_ADMIN_LOAN_EXCEL",
        ),
        (
            "downloadExcel",
            "resources/views/admin/not-registered.blade.php",
            "OK_ADMIN_NR_EXCEL",
        ),
        (
            "xlsx.full.min.js",
            "resources/views/sub_admin/layout.blade.php",
            "OK_SUBADMIN_LAYOUT_SHEETJS",
        ),
        (
            "downloadExcel",
            "resources/views/sub_admin/reports.blade.php",
            "OK_SUB_REPORTS_EXCEL",
        ),
        (
            "downloadExcel",
            "resources/views/sub_admin/loan-requests.blade.php",
            "OK_SUB_LOAN_EXCEL",
        ),
        (
            "downloadExcel",
            "resources/views/sub_admin/not-registered.blade.php",
            "OK_SUB_NR_EXCEL",
        ),
        (
            "export === '1'",
            "app/Http/Controllers/SubAdminPanelController.php",
            "OK_SUBADMIN_EXPORT",
        ),
    ]
    for needle, relpath, ok_tag in checks:
        remote = posixpath.join(APP_ROOT, relpath)
        ssh_run(
            ssh,
            f"grep -Fq {shell_quote(needle)} {remote} && echo {ok_tag} || echo MISSING_{ok_tag}",
            check=False,
        )


def shell_quote(s: str) -> str:
    return "'" + s.replace("'", "'\\''") + "'"


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

        log("info", "Uploading files via SSH stdin stream ...")
        for rel in FILES_TO_UPLOAD:
            local_path = LOCAL_ROOT / rel
            if not local_path.exists():
                raise SystemExit(f"Local file missing: {local_path}")
            remote_path = posixpath.join(APP_ROOT, rel)
            upload_via_stdin(ssh, local_path, remote_path)

        rebuild_caches(ssh)
        quick_grep(ssh)

        log("done", "Duplicate-loan fix + PDF/Excel download menu deployed.")
    finally:
        ssh.close()
    return 0


if __name__ == "__main__":
    sys.exit(main())
