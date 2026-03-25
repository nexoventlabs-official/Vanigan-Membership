<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Members — Vanigan Admin</title>
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
    .total-badge { background: #e8f5e9; color: #2e7d32; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }

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
    tbody tr:hover { background: #f8fdf8; cursor: pointer; }
    tbody tr a.row-link { display: contents; color: inherit; text-decoration: none; }

    .member-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #e8f5e9; }
    .member-avatar-placeholder { width: 36px; height: 36px; border-radius: 50%; background: #e8f5e9; color: #2e7d32; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; }
    .member-name { font-weight: 600; color: #1a1a1a; }
    .member-id { font-size: 0.72rem; color: #999; font-family: monospace; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
    .badge-green { background: #e8f5e9; color: #2e7d32; }
    .badge-orange { background: #fff3e0; color: #f57c00; }
    .referral-count { display: inline-flex; align-items: center; gap: 4px; font-weight: 600; color: #2e7d32; }

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
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-brand"><i class="bi bi-shield-check"></i> Vanigan Admin</div>
    <div class="navbar-nav">
      <a href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-fill"></i> Dashboard</a>
      <a href="{{ route('admin.users') }}" class="active"><i class="bi bi-people"></i> Members</a>
      <a href="{{ route('admin.voters') }}"><i class="bi bi-person-vcard"></i> Voters</a>
      <a href="{{ route('admin.reports') }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
      <a href="{{ route('admin.not_registered') }}"><i class="bi bi-person-x"></i> Not Registered</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <h2><i class="bi bi-people-fill" style="color:#2e7d32;"></i> All Members</h2>
      <span class="total-badge">{{ number_format($total) }} total</span>
    </div>

    <!-- Filters -->
    <div class="filters">
      <form method="GET" action="{{ route('admin.users') }}">
        <input type="text" name="search" placeholder="Search name, EPIC, mobile, or ID..." value="{{ $search }}">
        <select name="assembly">
          <option value="">All Assemblies</option>
          @foreach($assemblies as $a)
          <option value="{{ $a }}" {{ $assembly === $a ? 'selected' : '' }}>{{ $a }}</option>
          @endforeach
        </select>
        <select name="district">
          <option value="">All Districts</option>
          @foreach($districts as $d)
          <option value="{{ $d }}" {{ $district === $d ? 'selected' : '' }}>{{ $d }}</option>
          @endforeach
        </select>
        <button type="submit" class="filter-btn"><i class="bi bi-search"></i> Search</button>
        @if($search || $assembly || $district)
        <a href="{{ route('admin.users') }}" class="clear-btn"><i class="bi bi-x-circle"></i> Clear</a>
        @endif
      </form>
    </div>

    <!-- Members Table -->
    <div class="section">
      @if(count($members) > 0)
      <table>
        <thead>
          <tr>
            <th></th>
            <th>Member</th>
            <th class="hide-mobile">EPIC No</th>
            <th>Assembly</th>
            <th class="hide-mobile">District</th>
            <th class="hide-mobile">Zone</th>
            <th class="hide-mobile">Referrals</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($members as $m)
          <tr onclick="window.location='{{ route('admin.user.detail', $m['unique_id'] ?? '') }}'">
            <td>
              @if(!empty($m['photo_url']))
              <img src="{{ $m['photo_url'] }}" class="member-avatar" alt="">
              @else
              <div class="member-avatar-placeholder">{{ strtoupper(substr($m['name'] ?? '?', 0, 1)) }}</div>
              @endif
            </td>
            <td>
              <div class="member-name">{{ $m['name'] ?? 'N/A' }}</div>
              <div class="member-id">{{ $m['unique_id'] ?? '' }}</div>
            </td>
            <td class="hide-mobile" style="font-family:monospace;font-size:0.8rem;">{{ $m['epic_no'] ?? '' }}</td>
            <td>{{ $m['assembly'] ?? '' }}</td>
            <td class="hide-mobile">{{ $m['district'] ?? '' }}</td>
            <td class="hide-mobile" style="font-size:0.8rem;color:#1565c0;">{{ $m['zone'] ?? '' }}</td>
            <td class="hide-mobile">
              @if(($m['referral_count'] ?? 0) > 0)
              <span class="referral-count"><i class="bi bi-share-fill"></i> {{ $m['referral_count'] }}</span>
              @else
              <span style="color:#ccc;">0</span>
              @endif
            </td>
            <td>
              @if(!empty($m['details_completed']))
              <span class="badge badge-green"><i class="bi bi-check-circle"></i> Complete</span>
              @else
              <span class="badge badge-orange"><i class="bi bi-clock"></i> Pending</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Pagination -->
      @if($pages > 1)
      <div class="pagination">
        @if($page > 1)
        <a href="{{ route('admin.users', array_merge(request()->query(), ['page' => $page - 1])) }}"><i class="bi bi-chevron-left"></i></a>
        @endif

        @for($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++)
          @if($p === $page)
          <span class="current">{{ $p }}</span>
          @else
          <a href="{{ route('admin.users', array_merge(request()->query(), ['page' => $p])) }}">{{ $p }}</a>
          @endif
        @endfor

        @if($page < $pages)
        <a href="{{ route('admin.users', array_merge(request()->query(), ['page' => $page + 1])) }}"><i class="bi bi-chevron-right"></i></a>
        @endif
      </div>
      @endif
      @else
      <div class="empty-state">
        <i class="bi bi-search"></i>
        <p>No members found{{ $search ? ' matching "' . e($search) . '"' : '' }}</p>
      </div>
      @endif
    </div>
  </div>
</body>
</html>
