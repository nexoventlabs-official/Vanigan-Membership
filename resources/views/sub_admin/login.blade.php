<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sub-Admin Login — Vanigan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 50%, #90caf9 100%); padding: 20px; }
    .login-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.12); width: 100%; max-width: 420px; overflow: hidden; }
    .login-header { background: linear-gradient(135deg, #1565c0, #1e88e5); color: #fff; padding: 32px 24px; text-align: center; }
    .login-header .icon { width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.2); display: inline-flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 12px; }
    .login-header h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: 4px; }
    .login-header p { font-size: 0.85rem; opacity: 0.85; }
    .login-body { padding: 32px 28px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #333; margin-bottom: 6px; }
    .form-group .input-wrap { position: relative; }
    .form-group input {
      width: 100%; padding: 12px 16px; border: 2px solid #e0e3e6; border-radius: 12px;
      font-size: 0.95rem; font-family: 'Inter', sans-serif; outline: none; transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus { border-color: #1565c0; box-shadow: 0 0 0 3px rgba(21,101,192,0.12); }
    .toggle-pw { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #888; cursor: pointer; font-size: 1.1rem; }
    .toggle-pw:hover { color: #1565c0; }
    .login-btn {
      width: 100%; padding: 14px; background: linear-gradient(135deg, #1565c0, #1e88e5); color: #fff;
      border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer;
      transition: transform 0.1s, box-shadow 0.2s; box-shadow: 0 4px 12px rgba(21,101,192,0.3);
    }
    .login-btn:hover { box-shadow: 0 6px 20px rgba(21,101,192,0.4); }
    .login-btn:active { transform: scale(0.98); }
    .alert { padding: 12px 16px; border-radius: 10px; font-size: 0.85rem; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
    .alert-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .footer { text-align: center; padding: 0 24px 24px; font-size: 0.78rem; color: #999; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <div class="icon"><i class="bi bi-person-badge-fill"></i></div>
      <h2>Vanigan Sub-Admin Panel</h2>
      <p>Tamil Nadu Vanigargalin Sangamam</p>
    </div>
    <div class="login-body">
      @if($errors->any())
      <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}</div>
      @endif
      @if(session('success'))
      <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
      @endif
      <form action="{{ route('sub_admin.login.submit') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" placeholder="Enter username" value="{{ old('username') }}" required autofocus>
        </div>
        <div class="form-group">
          <label>Password</label>
          <div class="input-wrap">
            <input type="password" name="password" id="pwInput" placeholder="Enter password" required>
            <button type="button" class="toggle-pw" onclick="togglePw()"><i class="bi bi-eye" id="pwIcon"></i></button>
          </div>
        </div>
        <button type="submit" class="login-btn"><i class="bi bi-box-arrow-in-right"></i> Sign In</button>
      </form>
    </div>
    <div class="footer">Tamil Nadu Vanigargalin Sangamam &copy; {{ date('Y') }}</div>
  </div>
  <script>
    function togglePw() {
      const inp = document.getElementById('pwInput');
      const icon = document.getElementById('pwIcon');
      if (inp.type === 'password') { inp.type = 'text'; icon.className = 'bi bi-eye-slash'; }
      else { inp.type = 'password'; icon.className = 'bi bi-eye'; }
    }
  </script>
</body>
</html>
