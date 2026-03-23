<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard — Vanigan Admin</title>
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

    /* Stat Cards */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px; }
    .stat-card { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: flex-start; gap: 14px; }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .stat-icon.green { background: #e8f5e9; color: #2e7d32; }
    .stat-icon.blue { background: #e3f2fd; color: #1565c0; }
    .stat-icon.orange { background: #fff3e0; color: #ef6c00; }
    .stat-icon.purple { background: #f3e5f5; color: #7b1fa2; }
    .stat-icon.teal { background: #e0f2f1; color: #00695c; }
    .stat-icon.red { background: #fce4ec; color: #c62828; }
    .stat-info h3 { font-size: 1.5rem; font-weight: 800; color: #1a1a1a; line-height: 1; margin-bottom: 4px; }
    .stat-info p { font-size: 0.78rem; color: #888; font-weight: 500; }

    /* Section */
    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
    .section-header { padding: 18px 20px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; }
    .section-header h3 { font-size: 0.95rem; font-weight: 700; color: #333; display: flex; align-items: center; gap: 8px; }
    .section-body { padding: 16px 20px; }

    /* Table */
    table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    thead th { text-align: left; padding: 10px 12px; color: #888; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f2f5; }
    tbody td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:hover { background: #fafafa; }
    table a { color: #2e7d32; text-decoration: none; font-weight: 600; }
    table a:hover { text-decoration: underline; }
    .rank { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; font-size: 0.7rem; font-weight: 700; }
    .rank-1 { background: #fff8e1; color: #f57f17; }
    .rank-2 { background: #eceff1; color: #546e7a; }
    .rank-3 { background: #fff3e0; color: #e65100; }
    .rank-default { background: #f5f5f5; color: #999; }

    /* Bar Charts */
    .chart-bar-wrap { margin-bottom: 10px; }
    .chart-bar-label { display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 4px; }
    .chart-bar-label span:first-child { font-weight: 600; color: #333; }
    .chart-bar-label span:last-child { color: #888; font-weight: 500; }
    .chart-bar-track { height: 8px; background: #f0f2f5; border-radius: 4px; overflow: hidden; }
    .chart-bar-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, #007a38, #00a84e); transition: width 0.6s ease; }

    /* Two-column grid */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .two-col { grid-template-columns: 1fr; } }

    /* Member avatar */
    .member-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #e8f5e9; }
    .member-avatar-placeholder { width: 32px; height: 32px; border-radius: 50%; background: #e8f5e9; color: #2e7d32; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
    .badge-green { background: #e8f5e9; color: #2e7d32; }
    .badge-orange { background: #fff3e0; color: #f57c00; }
    .empty-state { text-align: center; padding: 30px; color: #999; font-size: 0.85rem; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-brand"><i class="bi bi-shield-check"></i> Vanigan Admin</div>
    <div class="navbar-nav">
      <a href="{{ route('admin.dashboard') }}" class="active"><i class="bi bi-grid-fill"></i> Dashboard</a>
      <a href="{{ route('admin.users') }}"><i class="bi bi-people"></i> Members</a>
      <a href="{{ route('admin.voters') }}"><i class="bi bi-person-vcard"></i> Voters</a>
      <a href="{{ route('admin.reports') }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <!-- Stat Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info"><h3>{{ number_format($stats['totalMembers']) }}</h3><p>Total Members</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-calendar-event"></i></div>
        <div class="stat-info"><h3>{{ number_format($stats['membersToday']) }}</h3><p>Today</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-calendar-week"></i></div>
        <div class="stat-info"><h3>{{ number_format($stats['membersThisWeek']) }}</h3><p>This Week</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon teal"><i class="bi bi-calendar-month"></i></div>
        <div class="stat-info"><h3>{{ number_format($stats['membersThisMonth']) }}</h3><p>This Month</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange"><i class="bi bi-share-fill"></i></div>
        <div class="stat-info"><h3>{{ number_format($stats['totalReferrals']) }}</h3><p>Total Referrals</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-info"><h3>{{ $stats['completionRate'] }}%</h3><p>Details Completed</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-credit-card-2-front-fill"></i></div>
        <div class="stat-info"><h3>{{ number_format($stats['cardsUploaded']) }}</h3><p>Cards Generated</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-clipboard-data-fill"></i></div>
        <div class="stat-info"><h3>{{ number_format($stats['detailsCompleted']) }}</h3><p>Profiles Complete</p></div>
      </div>
    </div>

    <!-- Top Referrers & Recent Members -->
    <div class="two-col">
      <!-- Top Referrers -->
      <div class="section">
        <div class="section-header"><h3><i class="bi bi-trophy-fill" style="color:#f57f17;"></i> Top Referrers</h3></div>
        <div class="section-body" style="padding:0;">
          @if(count($stats['topReferrers']) > 0)
          <table>
            <thead><tr><th>#</th><th>Member</th><th>Assembly</th><th style="text-align:right;">Referrals</th></tr></thead>
            <tbody>
              @foreach($stats['topReferrers'] as $i => $ref)
              <tr>
                <td><span class="rank {{ $i < 3 ? 'rank-'.($i+1) : 'rank-default' }}">{{ $i + 1 }}</span></td>
                <td><a href="{{ route('admin.user.detail', $ref['unique_id'] ?? '') }}">{{ $ref['name'] ?? 'N/A' }}</a><br><span style="font-size:0.72rem;color:#999;">{{ $ref['mobile'] ?? '' }}</span></td>
                <td style="font-size:0.8rem;">{{ $ref['assembly'] ?? '' }}</td>
                <td style="text-align:right;font-weight:700;color:#2e7d32;">{{ $ref['referral_count'] ?? 0 }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <div class="empty-state">No referrals yet</div>
          @endif
        </div>
      </div>

      <!-- Recent Members -->
      <div class="section">
        <div class="section-header">
          <h3><i class="bi bi-clock-history" style="color:#1565c0;"></i> Recent Members</h3>
          <a href="{{ route('admin.users') }}" style="font-size:0.8rem;color:#2e7d32;text-decoration:none;font-weight:600;">View All →</a>
        </div>
        <div class="section-body" style="padding:0;">
          @if(count($stats['recentMembers']) > 0)
          <table>
            <thead><tr><th></th><th>Name</th><th>Assembly</th><th>Status</th></tr></thead>
            <tbody>
              @foreach($stats['recentMembers'] as $m)
              <tr>
                <td>
                  @if(!empty($m['photo_url']))
                  <img src="{{ $m['photo_url'] }}" class="member-avatar" alt="">
                  @else
                  <div class="member-avatar-placeholder">{{ strtoupper(substr($m['name'] ?? '?', 0, 1)) }}</div>
                  @endif
                </td>
                <td><a href="{{ route('admin.user.detail', $m['unique_id'] ?? '') }}">{{ $m['name'] ?? 'N/A' }}</a><br><span style="font-size:0.72rem;color:#999;">{{ $m['unique_id'] ?? '' }}</span></td>
                <td style="font-size:0.8rem;">{{ $m['assembly'] ?? '' }}</td>
                <td>
                  @if(!empty($m['details_completed']))
                  <span class="badge badge-green">Complete</span>
                  @else
                  <span class="badge badge-orange">Pending</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <div class="empty-state">No members yet</div>
          @endif
        </div>
      </div>
    </div>

    <!-- Assembly & District Charts -->
    <div class="two-col" style="margin-top:0;">
      <!-- Members by Assembly -->
      <div class="section">
        <div class="section-header"><h3><i class="bi bi-bar-chart-fill" style="color:#2e7d32;"></i> Members by Assembly</h3></div>
        <div class="section-body">
          @if(count($stats['assemblyStats']) > 0)
            @php $maxAssembly = max(array_column($stats['assemblyStats'], 'count')); @endphp
            @foreach($stats['assemblyStats'] as $a)
            <div class="chart-bar-wrap">
              <div class="chart-bar-label"><span>{{ $a['assembly'] ?? 'Unknown' }}</span><span>{{ $a['count'] }}</span></div>
              <div class="chart-bar-track"><div class="chart-bar-fill" style="width:{{ $maxAssembly > 0 ? round(($a['count'] / $maxAssembly) * 100) : 0 }}%;"></div></div>
            </div>
            @endforeach
          @else
          <div class="empty-state">No data yet</div>
          @endif
        </div>
      </div>

      <!-- Members by District -->
      <div class="section">
        <div class="section-header"><h3><i class="bi bi-geo-alt-fill" style="color:#ef6c00;"></i> Members by District</h3></div>
        <div class="section-body">
          @if(count($stats['districtStats']) > 0)
            @php $maxDistrict = max(array_column($stats['districtStats'], 'count')); @endphp
            @foreach($stats['districtStats'] as $d)
            <div class="chart-bar-wrap">
              <div class="chart-bar-label"><span>{{ $d['district'] ?? 'Unknown' }}</span><span>{{ $d['count'] }}</span></div>
              <div class="chart-bar-track"><div class="chart-bar-fill" style="width:{{ $maxDistrict > 0 ? round(($d['count'] / $maxDistrict) * 100) : 0 }}%; background: linear-gradient(90deg, #e65100, #ff9800);"></div></div>
            </div>
            @endforeach
          @else
          <div class="empty-state">No data yet</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</body>
</html>
