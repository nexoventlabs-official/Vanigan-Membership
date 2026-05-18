<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sub Admin') — Vanigan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f0f2f5; color: #333; min-height: 100vh; }

    /* Navbar */
    .navbar { background: linear-gradient(135deg, #1565c0, #1e88e5); color: #fff; padding: 0 24px; display: flex; align-items: center; height: 60px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 100; }
    .navbar-brand { font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .navbar-nav { display: flex; align-items: center; gap: 4px; margin-left: auto; flex-wrap: wrap; }
    .navbar-nav a { color: rgba(255,255,255,0.85); text-decoration: none; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; transition: background 0.2s; }
    .navbar-nav a:hover, .navbar-nav a.active { background: rgba(255,255,255,0.18); color: #fff; }
    .navbar-nav form button { background: rgba(255,255,255,0.15); border: none; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: inherit; transition: background 0.2s; }
    .navbar-nav form button:hover { background: rgba(255,255,255,0.25); }

    .container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

    /* Page Header */
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .page-header h2 { font-size: 1.3rem; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .total-badge { background: #e3f2fd; color: #1565c0; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }

    /* Filter bar */
    .filter-bar { background: #fff; border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .filter-btn { padding: 8px 18px; border-radius: 8px; border: 2px solid #e0e0e0; background: #fff; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: inherit; color: #555; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .filter-btn:hover { border-color: #1565c0; color: #1565c0; }
    .filter-btn.active { background: #1565c0; color: #fff; border-color: #1565c0; }
    .filter-divider { width: 1px; height: 30px; background: #e0e0e0; margin: 0 4px; }
    .date-input { padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.82rem; font-family: inherit; outline: none; color: #333; transition: border-color 0.2s; }
    .date-input:focus { border-color: #1565c0; }
    .apply-btn { padding: 8px 18px; border-radius: 8px; border: none; background: #1565c0; color: #fff; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: inherit; transition: background 0.2s; }
    .apply-btn:hover { background: #0d47a1; }

    /* Filters (search forms) */
    .filters { background: #fff; border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
    .filters form { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .filters input, .filters select {
      padding: 10px 14px; border: 2px solid #e0e3e6; border-radius: 10px; font-size: 0.85rem;
      font-family: 'Inter', sans-serif; outline: none; transition: border-color 0.2s;
    }
    .filters input:focus, .filters select:focus { border-color: #1565c0; }
    .filters input[type="text"] { flex: 1; min-width: 200px; }
    .filters select { min-width: 160px; background: #fff; }
    .clear-btn { padding: 10px 16px; background: #f5f5f5; color: #666; border: none; border-radius: 10px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .clear-btn:hover { background: #eee; }

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
    .stat-icon.yellow { background: #fffde7; color: #f9a825; }
    .stat-icon.pink { background: #fce4ec; color: #d81b60; }
    .stat-info h3 { font-size: 1.5rem; font-weight: 800; color: #1a1a1a; line-height: 1; margin-bottom: 4px; }
    .stat-info p { font-size: 0.78rem; color: #888; font-weight: 500; }

    /* Section + tables */
    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
    .section-header { padding: 16px 20px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .section-header h3 { font-size: 0.95rem; font-weight: 700; color: #333; display: flex; align-items: center; gap: 8px; }
    .section-body { padding: 16px 20px; }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    thead th { text-align: left; padding: 10px 12px; color: #888; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f2f5; background: #fafafa; white-space: nowrap; }
    tbody td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:hover { background: #fafafa; }
    table a { color: #1565c0; text-decoration: none; font-weight: 600; }
    table a:hover { text-decoration: underline; }

    /* Avatars / photos */
    .member-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #e3f2fd; }
    .member-avatar-placeholder { width: 36px; height: 36px; border-radius: 50%; background: #e3f2fd; color: #1565c0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; }
    .member-photo { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #e3f2fd; background: #f0f2f5; }
    .member-photo-placeholder { width: 36px; height: 36px; border-radius: 50%; background: #f0f2f5; display: inline-flex; align-items: center; justify-content: center; color: #bbb; font-size: 1rem; }
    .member-name { font-weight: 600; color: #1a1a1a; }
    .member-id { font-size: 0.72rem; color: #999; font-family: monospace; }

    /* Badges */
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
    .badge-green { background: #e8f5e9; color: #2e7d32; }
    .badge-orange { background: #fff3e0; color: #f57c00; }
    .badge-blue { background: #e3f2fd; color: #1565c0; }
    .badge-red { background: #ffebee; color: #c62828; }

    /* Pagination */
    .pagination { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 16px; }
    .pagination a, .pagination span {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 36px; height: 36px; padding: 0 10px; border-radius: 10px;
      font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;
    }
    .pagination a { color: #333; background: #f5f5f5; }
    .pagination a:hover { background: #e3f2fd; color: #1565c0; }
    .pagination span.current { background: #1565c0; color: #fff; font-weight: 700; }
    .pagination span.dots { color: #999; background: none; }

    /* Misc */
    .empty-state { text-align: center; padding: 50px 20px; color: #999; font-size: 0.9rem; }
    .empty-state i { font-size: 2.5rem; color: #ccc; display: block; margin-bottom: 10px; }
    .serial { width: 28px; height: 28px; border-radius: 50%; background: #f0f2f5; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: #888; }
    .date-label { font-size: 0.78rem; color: #888; font-weight: 500; }
    .referrer-tag { font-size: 0.7rem; color: #1565c0; background: #e3f2fd; padding: 2px 8px; border-radius: 8px; font-weight: 600; white-space: nowrap; }
    .download-wrap { position: relative; display: inline-block; }
    .download-btn { padding: 8px 18px; border-radius: 8px; border: 2px solid #1565c0; background: #fff; color: #1565c0; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .download-btn:hover { background: #1565c0; color: #fff; }
    .download-menu { display: none; position: absolute; right: 0; top: calc(100% + 4px); background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: 0 6px 20px rgba(0,0,0,0.12); min-width: 150px; z-index: 60; overflow: hidden; }
    .download-menu.open { display: block; }
    .download-menu button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 14px; border: none; background: #fff; font-size: 0.85rem; font-weight: 600; font-family: inherit; text-align: left; cursor: pointer; color: #333; }
    .download-menu button + button { border-top: 1px solid #f0f2f5; }
    .download-menu button:hover { background: #f5f9ff; }
    .download-menu .dl-pdf { color: #c62828; }
    .download-menu .dl-xls { color: #1b5e20; }

    /* Bar charts */
    .chart-bar-wrap { margin-bottom: 10px; }
    .chart-bar-label { display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 4px; }
    .chart-bar-label span:first-child { font-weight: 600; color: #333; }
    .chart-bar-label span:last-child { color: #888; font-weight: 500; }
    .chart-bar-track { height: 8px; background: #f0f2f5; border-radius: 4px; overflow: hidden; }
    .chart-bar-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, #1565c0, #42a5f5); transition: width 0.6s ease; }

    /* Two-column grid */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .two-col { grid-template-columns: 1fr; } }

    /* Flash alerts */
    .flash { padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 500; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .flash-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .flash-error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }

    @media (max-width: 768px) {
      .navbar { padding: 0 12px; }
      .navbar-nav a, .navbar-nav form button { padding: 6px 10px; font-size: 0.78rem; }
      .container { padding: 16px 12px; }
      .filter-bar { flex-direction: column; align-items: stretch; }
      .filter-divider { display: none; }
      .page-header h2 { font-size: 1.1rem; }
      .filters form { flex-direction: column; }
      .filters input[type="text"], .filters select { min-width: unset; width: 100%; }
      .hide-mobile { display: none; }
    }

    @media print {
      .navbar, .filter-bar, .download-btn, .download-wrap, .no-print { display: none !important; }
      body { background: #fff; }
      .container { max-width: 100%; padding: 0; }
      .section { box-shadow: none; border: 1px solid #ddd; }
    }
  </style>
  @stack('styles')
</head>
<body>
  <nav class="navbar">
    <div class="navbar-brand"><i class="bi bi-person-badge"></i> Vanigan Sub-Admin</div>
    <div class="navbar-nav">
      @php $tab = $tab ?? ''; @endphp
      <a href="{{ route('sub_admin.dashboard') }}"      class="{{ $tab === 'dashboard'      ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> Dashboard</a>
      <a href="{{ route('sub_admin.users') }}"          class="{{ $tab === 'members'        ? 'active' : '' }}"><i class="bi bi-people"></i> Members</a>
      <a href="{{ route('sub_admin.reports') }}"        class="{{ $tab === 'reports'        ? 'active' : '' }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
      <a href="{{ route('sub_admin.loan_requests') }}"  class="{{ $tab === 'loan_requests'  ? 'active' : '' }}"><i class="bi bi-cash-coin"></i> Loan Requests</a>
      <a href="{{ route('sub_admin.not_registered') }}" class="{{ $tab === 'not_registered' ? 'active' : '' }}"><i class="bi bi-person-x"></i> Not Registered</a>
      <a href="{{ route('sub_admin.whatsapp') }}"       class="{{ $tab === 'whatsapp'       ? 'active' : '' }}"><i class="bi bi-whatsapp"></i> WhatsApp</a>
      <form action="{{ route('sub_admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    @if(session('success'))
      <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="flash flash-error"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
    @endif

    @yield('content')
  </div>

  <script>
    // Shared download-menu behaviour (used by reports, loan-requests, not-registered)
    function toggleDownloadMenu(e) {
      e.stopPropagation();
      const menu = document.getElementById('downloadMenu');
      if (!menu) return;
      menu.classList.toggle('open');
    }
    function closeDownloadMenu() {
      const menu = document.getElementById('downloadMenu');
      if (menu) menu.classList.remove('open');
    }
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.download-wrap')) closeDownloadMenu();
    });
  </script>
  @stack('scripts')
</body>
</html>
