import paramiko
c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect("174.138.49.116", 22, "master_ykechncjba", "TkgqJ3DcqExc",
          look_for_keys=False, allow_agent=False, timeout=30)
_, o, _ = c.exec_command(
    "grep -n 'export' "
    "/home/master/applications/ewpqehegpr/public_html/app/Http/Controllers/SubAdminPanelController.php "
    "| head -5"
)
print(o.read().decode().strip())
c.close()
