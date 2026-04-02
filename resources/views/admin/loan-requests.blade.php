<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loan Requests — Vanigan Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f0f2f5; color: #333; min-height: 100vh; }

    .navbar { background: linear-gradient(135deg, #007a38, #00a84e); color: #fff; padding: 0 24px; display: flex; align-items: center; height: 60px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 100; }
    .navbar-brand { font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .navbar-nav { display: flex; align-items: center; gap: 4px; margin-left: auto; }
    .navbar-nav a { color: rgba(255,255,255,0.85); text-decoration: none; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; transition: background 0.2s; }
    .navbar-nav a:hover, .navbar-nav a.active { background: rgba(255,255,255,0.18); color: #fff; }
    .navbar-nav form button { background: rgba(255,255,255,0.15); border: none; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: inherit; transition: background 0.2s; }
    .navbar-nav form button:hover { background: rgba(255,255,255,0.25); }

    .container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .page-header h2 { font-size: 1.3rem; font-weight: 800; display: flex; align-items: center; gap: 10px; }

    .filter-bar { background: #fff; border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .filter-btn { padding: 8px 18px; border-radius: 8px; border: 2px solid #e0e0e0; background: #fff; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: inherit; color: #555; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .filter-btn:hover { border-color: #2e7d32; color: #2e7d32; }
    .filter-btn.active { background: #2e7d32; color: #fff; border-color: #2e7d32; }
    .filter-divider { width: 1px; height: 30px; background: #e0e0e0; margin: 0 4px; }
    .date-input { padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.82rem; font-family: inherit; outline: none; color: #333; transition: border-color 0.2s; }
    .date-input:focus { border-color: #2e7d32; }
    .apply-btn { padding: 8px 18px; border-radius: 8px; border: none; background: #2e7d32; color: #fff; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: inherit; transition: background 0.2s; }
    .apply-btn:hover { background: #1b5e20; }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 14px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: flex-start; gap: 12px; }
    .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .stat-icon.green { background: #e8f5e9; color: #2e7d32; }
    .stat-icon.orange { background: #fff3e0; color: #ef6c00; }
    .stat-info h3 { font-size: 1.4rem; font-weight: 800; color: #1a1a1a; line-height: 1; margin-bottom: 3px; }
    .stat-info p { font-size: 0.75rem; color: #888; font-weight: 500; }

    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
    .section-header { padding: 16px 20px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .section-header h3 { font-size: 0.95rem; font-weight: 700; color: #333; display: flex; align-items: center; gap: 8px; }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 0.82rem; min-width: 800px; }
    thead th { text-align: left; padding: 10px 12px; color: #888; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f2f5; white-space: nowrap; }
    tbody td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:hover { background: #fafafa; }
    table a { color: #2e7d32; text-decoration: none; font-weight: 600; }
    table a:hover { text-decoration: underline; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.68rem; font-weight: 600; }
    .badge-orange { background: #fff3e0; color: #f57c00; }
    .badge-green { background: #e8f5e9; color: #2e7d32; }
    .badge-red { background: #ffebee; color: #c62828; }

    .serial { width: 28px; height: 28px; border-radius: 50%; background: #f0f2f5; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: #888; }
    .member-photo { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #e8f5e9; background: #f0f2f5; }
    .member-photo-placeholder { width: 36px; height: 36px; border-radius: 50%; background: #f0f2f5; display: inline-flex; align-items: center; justify-content: center; color: #bbb; font-size: 1rem; }

    .download-btn { padding: 8px 18px; border-radius: 8px; border: 2px solid #2e7d32; background: #fff; color: #2e7d32; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .download-btn:hover { background: #2e7d32; color: #fff; }

    .date-label { font-size: 0.78rem; color: #888; font-weight: 500; }

    .empty-state { text-align: center; padding: 50px 20px; color: #999; font-size: 0.9rem; }
    .empty-state i { font-size: 2.5rem; color: #ccc; display: block; margin-bottom: 10px; }

    @media (max-width: 768px) {
      .navbar { padding: 0 12px; }
      .navbar-nav a, .navbar-nav form button { padding: 6px 10px; font-size: 0.78rem; }
      .container { padding: 16px 12px; }
      .filter-bar { flex-direction: column; align-items: stretch; }
      .filter-divider { display: none; }
      .page-header h2 { font-size: 1.1rem; }
    }

    @media print {
      .navbar, .filter-bar, .download-btn, .no-print { display: none !important; }
      body { background: #fff; }
      .container { max-width: 100%; padding: 0; }
      .section { box-shadow: none; border: 1px solid #ddd; }
      table { font-size: 0.75rem; min-width: unset; }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="navbar-brand"><i class="bi bi-shield-check"></i> Vanigan Admin</div>
    <div class="navbar-nav">
      <a href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-fill"></i> Dashboard</a>
      <a href="{{ route('admin.users') }}"><i class="bi bi-people"></i> Members</a>
      <a href="{{ route('admin.voters') }}"><i class="bi bi-person-vcard"></i> Voters</a>
      <a href="{{ route('admin.reports') }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
      <a href="{{ route('admin.loan_requests') }}" class="active"><i class="bi bi-cash-coin"></i> Loan Requests</a>
      <a href="{{ route('admin.not_registered') }}"><i class="bi bi-person-x"></i> Not Registered</a>
      <a href="{{ route('admin.whatsapp') }}"><i class="bi bi-whatsapp"></i> WhatsApp</a>
      <a href="{{ route('admin.flow_images') }}"><i class="bi bi-images"></i> Flow Images</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <div class="page-header">
      <h2><i class="bi bi-cash-coin" style="color:#2e7d32;"></i> Loan Requests</h2>
      <div class="date-label">
        @if($filter === 'today')
          <i class="bi bi-calendar-event"></i> Today: {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
        @elseif($filter === 'weekly')
          <i class="bi bi-calendar-week"></i> This Week: {{ \Carbon\Carbon::parse($from)->format('d M') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @elseif($filter === 'monthly')
          <i class="bi bi-calendar-month"></i> This Month: {{ \Carbon\Carbon::parse($from)->format('d M') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @elseif($filter === 'custom')
          <i class="bi bi-calendar-range"></i> {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @elseif($filter === 'all')
          <i class="bi bi-calendar"></i> All Time
        @endif
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar no-print">
      <a href="{{ route('admin.loan_requests', ['filter' => 'today']) }}" class="filter-btn {{ $filter === 'today' ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> Today</a>
      <a href="{{ route('admin.loan_requests', ['filter' => 'weekly']) }}" class="filter-btn {{ $filter === 'weekly' ? 'active' : '' }}"><i class="bi bi-calendar-week"></i> Weekly</a>
      <a href="{{ route('admin.loan_requests', ['filter' => 'monthly']) }}" class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}"><i class="bi bi-calendar-month"></i> Monthly</a>
      <a href="{{ route('admin.loan_requests', ['filter' => 'all']) }}" class="filter-btn {{ $filter === 'all' ? 'active' : '' }}"><i class="bi bi-calendar"></i> All</a>
      <div class="filter-divider"></div>
      <form action="{{ route('admin.loan_requests') }}" method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <input type="hidden" name="filter" value="custom">
        <input type="date" name="from" class="date-input" value="{{ $filter === 'custom' ? $from : '' }}" required>
        <span style="font-size:0.8rem;color:#888;">to</span>
        <input type="date" name="to" class="date-input" value="{{ $filter === 'custom' ? $to : '' }}" max="{{ now()->format('Y-m-d') }}" required>
        <button type="submit" class="apply-btn"><i class="bi bi-funnel"></i> Apply</button>
      </form>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-cash-coin"></i></div>
        <div class="stat-info"><h3>{{ number_format($total) }}</h3><p>Total Loan Requests</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-info">
          <h3>{{ number_format(collect($requests)->where('status', 'pending')->count()) }}</h3>
          <p>Pending</p>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="section">
      <div class="section-header">
        <h3><i class="bi bi-list-ul" style="color:#2e7d32;"></i> Loan Requests ({{ number_format($total) }})</h3>
        @if($total > 0)
        <button class="download-btn" onclick="downloadPDF()"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
        @endif
      </div>

      @if($total > 0)
      <div class="table-wrap">
        <table id="reportTable">
          <thead>
            <tr>
              <th>#</th>
              <th></th>
              <th>Member Name</th>
              <th>Unique ID</th>
              <th>Mobile</th>
              <th>Business Type</th>
              <th>Business Name</th>
              <th>Status</th>
              <th>Requested At</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requests as $i => $r)
            <tr>
              <td><span class="serial">{{ $i + 1 }}</span></td>
              <td>
                @if(!empty($r['photo_url']))
                <img src="{{ $r['photo_url'] }}" class="member-photo" alt="">
                @else
                <span class="member-photo-placeholder"><i class="bi bi-person-fill"></i></span>
                @endif
              </td>
              <td style="font-weight:600;">
                @if(!empty($r['unique_id']))
                <a href="{{ route('admin.user.detail', $r['unique_id']) }}">{{ $r['member_name'] ?? 'N/A' }}</a>
                @else
                {{ $r['member_name'] ?? 'N/A' }}
                @endif
              </td>
              <td><span style="font-weight:600;color:#2e7d32;font-size:0.78rem;">{{ $r['unique_id'] ?? '' }}</span></td>
              <td style="font-size:0.8rem;">{{ $r['mobile'] ?? '' }}</td>
              <td style="font-size:0.8rem;">{{ $r['business_type'] ?? '' }}</td>
              <td style="font-size:0.8rem;">{{ $r['business_name'] ?? '' }}</td>
              <td>
                @php $status = $r['status'] ?? 'pending'; @endphp
                @if($status === 'approved')
                <span class="badge badge-green"><i class="bi bi-check-circle"></i> Approved</span>
                @elseif($status === 'rejected')
                <span class="badge badge-red"><i class="bi bi-x-circle"></i> Rejected</span>
                @else
                <span class="badge badge-orange"><i class="bi bi-hourglass-split"></i> Pending</span>
                @endif
              </td>
              <td style="font-size:0.75rem;white-space:nowrap;color:#555;">
                @php
                  $reqDate = '';
                  if (!empty($r['created_at'])) {
                    try { $reqDate = \Carbon\Carbon::parse($r['created_at'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A'); } catch (\Exception $e) { $reqDate = $r['created_at']; }
                  }
                @endphp
                {{ $reqDate ?: '—' }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p>No loan requests found in this period</p>
        <p style="font-size:0.78rem;margin-top:6px;">Try selecting a different date range</p>
      </div>
      @endif
    </div>
  </div>

  <script>
    function downloadPDF() {
      const table = document.getElementById('reportTable');
      if (!table) return;

      const filter = '{{ $filter }}';
      const from = '{{ $from }}';
      const to = '{{ $to }}';
      const total = {{ $total }};

      let filterLabel = 'Today';
      if (filter === 'weekly') filterLabel = 'This Week';
      else if (filter === 'monthly') filterLabel = 'This Month';
      else if (filter === 'custom') filterLabel = 'Custom Range';
      else if (filter === 'all') filterLabel = 'All Time';

      let fileNameParts = ['TNVS_Loan_Requests'];
      fileNameParts.push(filterLabel.replace(/\s/g, '_'));
      if (from && to) fileNameParts.push(from + '_to_' + to);
      const fileName = fileNameParts.join('_');

      let rows = '';
      const tbody = table.querySelector('tbody');
      const trs = tbody.querySelectorAll('tr');
      trs.forEach((tr, idx) => {
        const cells = tr.querySelectorAll('td');
        const name = cells[2]?.innerText?.trim() || '';
        const uid = cells[3]?.innerText?.trim() || '';
        const mobile = cells[4]?.innerText?.trim() || '';
        const bizType = cells[5]?.innerText?.trim() || '';
        const bizName = cells[6]?.innerText?.trim() || '';
        const status = cells[7]?.innerText?.trim() || '';
        const reqAt = cells[8]?.innerText?.trim() || '—';
        rows += '<tr>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;text-align:center;font-size:11px;">' + (idx + 1) + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:11px;">' + name + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;color:#2e7d32;font-weight:600;">' + uid + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + mobile + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + bizType + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + bizName + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + status + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + reqAt + '</td>'
          + '</tr>';
      });

      const html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${fileName}</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
          h1 { font-size: 18px; color: #007a38; margin-bottom: 4px; }
          .subtitle { font-size: 12px; color: #666; margin-bottom: 16px; }
          .stats { display: flex; gap: 20px; margin-bottom: 16px; }
          .stat { background: #f8f8f8; padding: 10px 16px; border-radius: 8px; }
          .stat strong { font-size: 18px; color: #007a38; }
          .stat span { display: block; font-size: 10px; color: #888; margin-top: 2px; }
          table { width: 100%; border-collapse: collapse; margin-top: 10px; }
          th { padding: 8px; border: 1px solid #ddd; background: #007a38; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
          @media print { body { padding: 10px; } }
        </style>
      </head><body>
        <h1>Tamil Nadu Vanigargalin Sangamam — Loan Requests Report</h1>
        <div class="subtitle">${filterLabel}: ${from && to ? from + ' to ' + to : 'All Time'}</div>
        <div class="stats">
          <div class="stat"><strong>${total}</strong><span>Total Loan Requests</span></div>
        </div>
        <table>
          <thead><tr>
            <th>#</th><th>Member Name</th><th>Unique ID</th><th>Mobile</th><th>Business Type</th><th>Business Name</th><th>Status</th><th>Requested At</th>
          </tr></thead>
          <tbody>${rows}</tbody>
        </table>
        <div style="margin-top:20px;font-size:10px;color:#999;text-align:center;">Generated on ${new Date().toLocaleString('en-IN')} — Vanigan Admin Panel</div>
        <script>window.onload = function() { window.print(); }<\/script>
      </body></html>`;

      const win = window.open('', '_blank');
      win.document.write(html);
      win.document.close();
    }
  </script>
</body>
</html>
