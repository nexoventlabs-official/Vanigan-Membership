<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Not Registered — Vanigan Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f0f2f5; color: #333; min-height: 100vh; }

    /* Navbar */
    .navbar { background: linear-gradient(135deg, #007a38, #00a84e); color: #fff; padding: 0 24px; display: flex; align-items: center; height: 60px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 100; }
    .navbar-brand { font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .navbar-nav { display: flex; align-items: center; gap: 4px; margin-left: auto; }
    .navbar-nav a { color: rgba(255,255,255,0.85); text-decoration: none; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; transition: background 0.2s; }
    .navbar-nav a:hover, .navbar-nav a.active { background: rgba(255,255,255,0.18); color: #fff; }
    .navbar-nav form button { background: rgba(255,255,255,0.15); border: none; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: inherit; transition: background 0.2s; }
    .navbar-nav form button:hover { background: rgba(255,255,255,0.25); }

    .container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

    /* Page header */
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .page-header h2 { font-size: 1.3rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .total-badge { background: #fff3e0; color: #e65100; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }

    /* Stats cards */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center; }
    .stat-card .stat-count { font-size: 1.6rem; font-weight: 800; color: #2e7d32; }
    .stat-card .stat-label { font-size: 0.72rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; font-weight: 600; }
    .stat-card.orange .stat-count { color: #e65100; }
    .stat-card.red .stat-count { color: #c62828; }
    .stat-card.blue .stat-count { color: #1565c0; }

    /* Filters */
    .filters { background: #fff; border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
    .filters form { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .filters input, .filters select {
      padding: 10px 14px; border: 2px solid #e0e3e6; border-radius: 10px; font-size: 0.85rem;
      font-family: 'Inter', sans-serif; outline: none; transition: border-color 0.2s;
    }
    .filters input:focus, .filters select:focus { border-color: #2e7d32; }
    .filters input[type="text"] { flex: 1; min-width: 200px; }
    .filters select { min-width: 160px; background: #fff; }
    .filter-btn {
      padding: 10px 20px; background: linear-gradient(135deg, #007a38, #00a84e); color: #fff;
      border: none; border-radius: 10px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
      font-family: inherit; transition: box-shadow 0.2s;
    }
    .filter-btn:hover { box-shadow: 0 4px 12px rgba(0,122,56,0.3); }
    .clear-btn {
      padding: 10px 16px; background: #f5f5f5; color: #666; border: none; border-radius: 10px;
      font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: inherit; text-decoration: none;
      display: inline-flex; align-items: center; gap: 4px;
    }
    .clear-btn:hover { background: #eee; }

    /* Table section */
    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    thead th { text-align: left; padding: 12px 14px; color: #888; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f2f5; background: #fafafa; }
    tbody td { padding: 12px 14px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:hover { background: #fffdf5; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
    .badge-step { background: #fff3e0; color: #e65100; }
    .badge-step.step-mobile { background: #fce4ec; color: #c62828; }
    .badge-step.step-otp { background: #fff3e0; color: #e65100; }
    .badge-step.step-epic { background: #e3f2fd; color: #1565c0; }
    .badge-step.step-photo { background: #f3e5f5; color: #7b1fa2; }
    .badge-step.step-pin { background: #e8f5e9; color: #2e7d32; }

    /* Pagination */
    .pagination { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 16px; }
    .pagination a, .pagination span {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 36px; height: 36px; padding: 0 10px; border-radius: 10px;
      font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;
    }
    .pagination a { color: #333; background: #f5f5f5; }
    .pagination a:hover { background: #e8f5e9; color: #2e7d32; }
    .pagination span.current { background: #2e7d32; color: #fff; font-weight: 700; }
    .pagination span.dots { color: #999; background: none; }

    .empty-state { text-align: center; padding: 40px 20px; color: #999; }
    .empty-state i { font-size: 2rem; display: block; margin-bottom: 8px; }

    @media (max-width: 768px) {
      .filters form { flex-direction: column; }
      .filters input[type="text"], .filters select { min-width: unset; width: 100%; }
      table { font-size: 0.78rem; }
      thead th, tbody td { padding: 8px 10px; }
      .hide-mobile { display: none; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-brand"><i class="bi bi-shield-check"></i> Vanigan Admin</div>
    <div class="navbar-nav">
      <a href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-fill"></i> Dashboard</a>
      <a href="{{ route('admin.users') }}"><i class="bi bi-people"></i> Members</a>
      <a href="{{ route('admin.voters') }}"><i class="bi bi-person-vcard"></i> Voters</a>
      <a href="{{ route('admin.reports') }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
      <a href="{{ route('admin.not_registered') }}" class="active"><i class="bi bi-person-x"></i> Not Registered</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <h2><i class="bi bi-person-x-fill" style="color:#e65100;"></i> Not Registered Members</h2>
      <span class="total-badge">{{ number_format($total) }} incomplete</span>
    </div>

    <!-- Step Stats -->
    @if(!empty($stats))
    <div class="stats-grid">
      @php
        $stepLabels = [
          'mobile_entered' => ['Mobile Entered', 'red'],
          'otp_verified' => ['OTP Verified', 'orange'],
          'epic_validated' => ['EPIC Validated', 'blue'],
          'photo_uploaded' => ['Photo Uploaded', ''],
          'pin_set' => ['PIN Set', ''],
        ];
        $totalIncomplete = array_sum($stats);
      @endphp
      <div class="stat-card orange">
        <div class="stat-count">{{ number_format($totalIncomplete) }}</div>
        <div class="stat-label">Total Incomplete</div>
      </div>
      @foreach($stepLabels as $key => $info)
        @if(isset($stats[$key]))
        <div class="stat-card {{ $info[1] }}">
          <div class="stat-count">{{ $stats[$key] }}</div>
          <div class="stat-label">Stopped at {{ $info[0] }}</div>
        </div>
        @endif
      @endforeach
    </div>
    @endif

    <!-- Filters -->
    <div class="filters">
      <form method="GET" action="{{ route('admin.not_registered') }}">
        <input type="text" name="search" placeholder="Search mobile, name, or EPIC..." value="{{ $search }}">
        <select name="step">
          <option value="">All Steps</option>
          <option value="mobile_entered" {{ $step === 'mobile_entered' ? 'selected' : '' }}>Stopped at Mobile</option>
          <option value="otp_verified" {{ $step === 'otp_verified' ? 'selected' : '' }}>Stopped at OTP</option>
          <option value="epic_validated" {{ $step === 'epic_validated' ? 'selected' : '' }}>Stopped at EPIC</option>
          <option value="photo_uploaded" {{ $step === 'photo_uploaded' ? 'selected' : '' }}>Stopped at Photo</option>
          <option value="pin_set" {{ $step === 'pin_set' ? 'selected' : '' }}>Stopped at PIN</option>
        </select>
        <button type="submit" class="filter-btn"><i class="bi bi-search"></i> Search</button>
        @if($search || $step)
        <a href="{{ route('admin.not_registered') }}" class="clear-btn"><i class="bi bi-x-circle"></i> Clear</a>
        @endif
      </form>
    </div>

    <!-- Table -->
    <div class="section">
      @if(count($users) > 0)
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Mobile</th>
            <th>Name</th>
            <th class="hide-mobile">EPIC No</th>
            <th class="hide-mobile">Assembly</th>
            <th class="hide-mobile">District</th>
            <th>Last Step</th>
            <th class="hide-mobile">Started At</th>
            <th class="hide-mobile">Last Activity</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $i => $u)
          @php
            $stepClass = '';
            $stepLabel = $u['last_step'] ?? 'unknown';
            if (str_contains($stepLabel, 'mobile')) $stepClass = 'step-mobile';
            elseif (str_contains($stepLabel, 'otp')) $stepClass = 'step-otp';
            elseif (str_contains($stepLabel, 'epic')) $stepClass = 'step-epic';
            elseif (str_contains($stepLabel, 'photo')) $stepClass = 'step-photo';
            elseif (str_contains($stepLabel, 'pin')) $stepClass = 'step-pin';

            $stepDisplay = ucwords(str_replace('_', ' ', $stepLabel));

            $startedAt = isset($u['started_at']) ? \Carbon\Carbon::parse($u['started_at'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') : '—';
            $lastActivity = isset($u['last_activity']) ? \Carbon\Carbon::parse($u['last_activity'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') : '—';
          @endphp
          <tr>
            <td style="color:#999;">{{ ($page - 1) * 20 + $i + 1 }}</td>
            <td style="font-family:monospace;font-weight:600;">{{ $u['mobile'] ?? '' }}</td>
            <td>{{ $u['name'] ?? '—' }}</td>
            <td class="hide-mobile" style="font-family:monospace;font-size:0.8rem;">{{ $u['epic_no'] ?? '—' }}</td>
            <td class="hide-mobile">{{ $u['assembly'] ?? '—' }}</td>
            <td class="hide-mobile">{{ $u['district'] ?? '—' }}</td>
            <td><span class="badge badge-step {{ $stepClass }}">{{ $stepDisplay }}</span></td>
            <td class="hide-mobile" style="font-size:0.78rem;color:#666;">{{ $startedAt }}</td>
            <td class="hide-mobile" style="font-size:0.78rem;color:#666;">{{ $lastActivity }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Pagination -->
      @if($pages > 1)
      <div class="pagination">
        @if($page > 1)
          <a href="{{ route('admin.not_registered', array_merge(request()->query(), ['page' => $page - 1])) }}">&laquo;</a>
        @endif

        @for($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++)
          @if($p === $page)
            <span class="current">{{ $p }}</span>
          @else
            <a href="{{ route('admin.not_registered', array_merge(request()->query(), ['page' => $p])) }}">{{ $p }}</a>
          @endif
        @endfor

        @if($page < $pages)
          <a href="{{ route('admin.not_registered', array_merge(request()->query(), ['page' => $page + 1])) }}">&raquo;</a>
        @endif
      </div>
      @endif

      @else
      <div class="empty-state">
        <i class="bi bi-check-circle"></i>
        <p>No incomplete registrations found.</p>
        <p style="font-size:0.8rem;margin-top:4px;">All users who started registration have completed it!</p>
      </div>
      @endif
    </div>
  </div>
</body>
</html>
