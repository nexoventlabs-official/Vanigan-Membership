"""
v2 deployer: targets the LIVE vanigan.digital application directly
(`/home/master/applications/ewpqehegpr/public_html`), uses SSH `cat >`
for file writes, and verifies each upload by SHA-256.

No MySQL changes. No git operations.
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

# IMPORTANT: This is the live vanigan.digital app folder, confirmed by
# /home/master/applications/ewpqehegpr/conf/server.nginx containing
# `vanigan.digital`.
APP_ROOT = "/home/master/applications/ewpqehegpr/public_html"

LOCAL_ROOT = Path(__file__).resolve().parent.parent

FILES_TO_UPLOAD = [
    "bootstrap/app.php",
    "config/services.php",
    "routes/web.php",
    "app/Http/Middleware/SubAdminAuthMiddleware.php",
    "app/Http/Controllers/SubAdminPanelController.php",
    "resources/views/sub_admin/layout.blade.php",
    "resources/views/sub_admin/login.blade.php",
    "resources/views/sub_admin/dashboard.blade.php",
    "resources/views/sub_admin/users.blade.php",
    "resources/views/sub_admin/user-detail.blade.php",
    "resources/views/sub_admin/reports.blade.php",
    "resources/views/sub_admin/loan-requests.blade.php",
    "resources/views/sub_admin/not-registered.blade.php",
    "resources/views/sub_admin/whatsapp.blade.php",
]

ENV_BLOCK = (
    "\n"
    "# ========================================\n"
    "# SUB-ADMIN\n"
    "# ========================================\n"
    "# Login URL: /sub-admin/login\n"
    "SUB_ADMIN_USERNAME=0000011111\n"
    "SUB_ADMIN_PASSWORD=0011\n"
    "SUB_ADMIN_PASSWORD_HASH=\n"
)


def log(tag: str, msg: str) -> None:
    print(f"[{tag}] {msg}", flush=True)


def ssh_run(ssh: paramiko.SSHClient, cmd: str, *,
            check: bool = True, quiet: bool = False) -> tuple[int, str, str]:
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


def upload_via_b64(ssh: paramiko.SSHClient, local: Path, remote: str) -> None:
    """Upload by base64-piping through SSH `cat`. More reliable than SFTP
    when destination already exists with strict permissions."""
    data = local.read_bytes()
    expected_sha = hashlib.sha256(data).hexdigest()
    expected_size = len(data)
    b64 = base64.b64encode(data).decode("ascii")

    # Ensure parent dir exists
    parent = posixpath.dirname(remote)
    ssh_run(ssh, f"mkdir -p {parent}", quiet=True)

    # Write via base64 decode in a single shell command
    cmd = f"echo '{b64}' | base64 -d > {remote}"
    ssh_run(ssh, cmd, quiet=True)

    # Verify size + sha256
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


def patch_env(ssh: paramiko.SSHClient) -> None:
    env_path = posixpath.join(APP_ROOT, ".env")
    rc, out, _ = ssh_run(
        ssh,
        f"grep -c '^SUB_ADMIN_USERNAME=' {env_path} || true",
        quiet=True,
    )
    count = int((out.strip() or "0").splitlines()[0])
    if count > 0:
        log("info", "SUB_ADMIN_USERNAME already present in .env — skipping append.")
        return

    # Backup
    ssh_run(ssh, f"cp -n {env_path} {env_path}.bak.subadmin")

    # Append the block
    block_b64 = base64.b64encode(ENV_BLOCK.encode()).decode()
    ssh_run(ssh, f"echo '{block_b64}' | base64 -d >> {env_path}")
    log("ok", "Appended SUB_ADMIN_* keys to .env")

    # Show last lines for confirmation
    ssh_run(ssh, f"tail -10 {env_path}")


def rebuild_caches(ssh: paramiko.SSHClient) -> None:
    # NOTE: We deliberately skip `php artisan config:cache` on this server
    # because the multi-line FLOW_PRIVATE_KEY in .env causes APP_KEY to be
    # cached as the placeholder from .env.production (now disabled), which
    # breaks the encrypter. Running config:clear and leaving the cache empty
    # is the correct/working state for this app.
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


def verify_routes(ssh: paramiko.SSHClient) -> None:
    log("info", "Listing sub-admin routes ...")
    ssh_run(
        ssh,
        f"cd {APP_ROOT} && php artisan route:list --columns=method,uri,name "
        f"| grep -iE 'sub.admin|sub_admin' || echo 'NO_SUB_ADMIN_ROUTES_FOUND'",
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
        # 0. Sanity check the target
        log("info", f"Target app root: {APP_ROOT}")
        ssh_run(ssh, f"test -f {APP_ROOT}/artisan && echo OK_HAS_ARTISAN")
        ssh_run(ssh, f"grep -E '^(APP_NAME|APP_URL)=' {APP_ROOT}/.env")
        ssh_run(ssh, f"grep -E 'laravel/framework' {APP_ROOT}/composer.json")

        # 1. Upload all files
        log("info", "Uploading files via base64-over-SSH ...")
        for rel in FILES_TO_UPLOAD:
            local_path = LOCAL_ROOT / rel
            if not local_path.exists():
                raise SystemExit(f"Local file missing: {local_path}")
            remote_path = posixpath.join(APP_ROOT, rel)
            upload_via_b64(ssh, local_path, remote_path)

        # 2. Patch .env
        patch_env(ssh)

        # 3. Cache rebuild
        rebuild_caches(ssh)

        # 4. Verify
        verify_routes(ssh)

        log("done", "Sub-Admin Panel deployed successfully on ewpqehegpr.")
        log("done", "Login: https://vanigan.digital/sub-admin/login  (0000011111 / 0011)")
    finally:
        ssh.close()
    return 0


if __name__ == "__main__":
    sys.exit(main())
