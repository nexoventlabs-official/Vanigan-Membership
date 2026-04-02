<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reports — Vanigan Admin</title>
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

    /* Page Header */
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .page-header h2 { font-size: 1.3rem; font-weight: 800; display: flex; align-items: center; gap: 10px; }

    /* Filter Bar */
    .filter-bar { background: #fff; border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .filter-btn { padding: 8px 18px; border-radius: 8px; border: 2px solid #e0e0e0; background: #fff; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: inherit; color: #555; }
    .filter-btn:hover { border-color: #2e7d32; color: #2e7d32; }
    .filter-btn.active { background: #2e7d32; color: #fff; border-color: #2e7d32; }
    .filter-divider { width: 1px; height: 30px; background: #e0e0e0; margin: 0 4px; }
    .date-input { padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.82rem; font-family: inherit; outline: none; color: #333; transition: border-color 0.2s; }
    .date-input:focus { border-color: #2e7d32; }
    .apply-btn { padding: 8px 18px; border-radius: 8px; border: none; background: #2e7d32; color: #fff; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: inherit; transition: background 0.2s; }
    .apply-btn:hover { background: #1b5e20; }

    /* Stat Cards */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 14px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: flex-start; gap: 12px; }
    .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .stat-icon.green { background: #e8f5e9; color: #2e7d32; }
    .stat-icon.blue { background: #e3f2fd; color: #1565c0; }
    .stat-icon.orange { background: #fff3e0; color: #ef6c00; }
    .stat-icon.purple { background: #f3e5f5; color: #7b1fa2; }
    .stat-info h3 { font-size: 1.4rem; font-weight: 800; color: #1a1a1a; line-height: 1; margin-bottom: 3px; }
    .stat-info p { font-size: 0.75rem; color: #888; font-weight: 500; }

    /* Section */
    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
    .section-header { padding: 16px 20px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .section-header h3 { font-size: 0.95rem; font-weight: 700; color: #333; display: flex; align-items: center; gap: 8px; }

    /* Table */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 0.82rem; min-width: 1100px; }
    thead th { text-align: left; padding: 10px 12px; color: #888; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f2f5; white-space: nowrap; }
    tbody td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:hover { background: #fafafa; }
    table a { color: #2e7d32; text-decoration: none; font-weight: 600; }
    table a:hover { text-decoration: underline; }

    /* Avatar */
    .member-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #e8f5e9; }
    .member-avatar-placeholder { width: 36px; height: 36px; border-radius: 50%; background: #e8f5e9; color: #2e7d32; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; }

    /* Badges */
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.68rem; font-weight: 600; }
    .badge-green { background: #e8f5e9; color: #2e7d32; }
    .badge-orange { background: #fff3e0; color: #f57c00; }
    .badge-blue { background: #e3f2fd; color: #1565c0; }

    .empty-state { text-align: center; padding: 50px 20px; color: #999; font-size: 0.9rem; }
    .empty-state i { font-size: 2.5rem; color: #ccc; display: block; margin-bottom: 10px; }

    /* Download Button */
    .download-btn { padding: 8px 18px; border-radius: 8px; border: 2px solid #2e7d32; background: #fff; color: #2e7d32; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .download-btn:hover { background: #2e7d32; color: #fff; }

    /* Date range label */
    .date-label { font-size: 0.78rem; color: #888; font-weight: 500; }

    /* Referrer tag */
    .referrer-tag { font-size: 0.7rem; color: #1565c0; background: #e3f2fd; padding: 2px 8px; border-radius: 8px; font-weight: 600; white-space: nowrap; }

    /* Serial number */
    .serial { width: 28px; height: 28px; border-radius: 50%; background: #f0f2f5; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: #888; }

    @media (max-width: 768px) {
      .navbar { padding: 0 12px; }
      .navbar-nav a, .navbar-nav form button { padding: 6px 10px; font-size: 0.78rem; }
      .container { padding: 16px 12px; }
      .filter-bar { flex-direction: column; align-items: stretch; }
      .filter-divider { display: none; }
      .page-header h2 { font-size: 1.1rem; }
    }

    /* Print/PDF styles */
    @media print {
      .navbar, .filter-bar, .download-btn, .no-print { display: none !important; }
      body { background: #fff; }
      .container { max-width: 100%; padding: 0; }
      .section { box-shadow: none; border: 1px solid #ddd; }
      table { font-size: 0.75rem; min-width: unset; }
      .member-avatar, .member-avatar-placeholder { width: 28px; height: 28px; }
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
      <a href="{{ route('admin.reports') }}" class="active"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
      <a href="{{ route('admin.loan_requests') }}"><i class="bi bi-cash-coin"></i> Loan Requests</a>
      <a href="{{ route('admin.not_registered') }}"><i class="bi bi-person-x"></i> Not Registered</a>
      <a href="{{ route('admin.whatsapp') }}"><i class="bi bi-whatsapp"></i> WhatsApp</a>
      <a href="{{ route('admin.flow_images') }}"><i class="bi bi-images"></i> Flow Images</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <h2><i class="bi bi-file-earmark-bar-graph-fill" style="color:#2e7d32;"></i> Reports</h2>
      <div class="date-label">
        @if($filter === 'today')
          <i class="bi bi-calendar-event"></i> Today: {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
        @elseif($filter === 'weekly')
          <i class="bi bi-calendar-week"></i> This Week: {{ \Carbon\Carbon::parse($from)->format('d M') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @elseif($filter === 'monthly')
          <i class="bi bi-calendar-month"></i> This Month: {{ \Carbon\Carbon::parse($from)->format('d M') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @elseif($filter === 'custom')
          <i class="bi bi-calendar-range"></i> {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @endif
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar no-print">
      <a href="{{ route('admin.reports', ['filter' => 'today']) }}" class="filter-btn {{ $filter === 'today' ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> Today</a>
      <a href="{{ route('admin.reports', ['filter' => 'weekly']) }}" class="filter-btn {{ $filter === 'weekly' ? 'active' : '' }}"><i class="bi bi-calendar-week"></i> Weekly</a>
      <a href="{{ route('admin.reports', ['filter' => 'monthly']) }}" class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}"><i class="bi bi-calendar-month"></i> Monthly</a>
      <div class="filter-divider"></div>
      <form action="{{ route('admin.reports') }}" method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <input type="hidden" name="filter" value="custom">
        <input type="date" name="from" class="date-input" value="{{ $filter === 'custom' ? $from : '' }}" required>
        <span style="font-size:0.8rem;color:#888;">to</span>
        <input type="date" name="to" class="date-input" value="{{ $filter === 'custom' ? $to : '' }}" max="{{ now()->format('Y-m-d') }}" required>
        <button type="submit" class="apply-btn"><i class="bi bi-funnel"></i> Apply</button>
      </form>
      <div class="filter-divider"></div>
      <form action="{{ route('admin.reports') }}" method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <input type="hidden" name="filter" value="{{ $filter }}">
        @if($filter === 'custom')
        <input type="hidden" name="from" value="{{ $from }}">
        <input type="hidden" name="to" value="{{ $to }}">
        @endif
        <select name="assembly" class="date-input" style="min-width:140px;">
          <option value="">All Assemblies</option>
          @foreach($assemblies as $a)
          <option value="{{ $a }}" {{ ($assembly ?? '') === $a ? 'selected' : '' }}>{{ $a }}</option>
          @endforeach
        </select>
        <select name="district" class="date-input" style="min-width:140px;">
          <option value="">All Districts</option>
          @foreach($districts as $d)
          <option value="{{ $d }}" {{ ($district ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
          @endforeach
        </select>
        <select name="zone" class="date-input" style="min-width:140px;">
          <option value="">All Zones</option>
          @foreach($zones ?? [] as $z)
          <option value="{{ $z }}" {{ ($zone ?? '') === $z ? 'selected' : '' }}>{{ $z }}</option>
          @endforeach
        </select>
        <button type="submit" class="apply-btn"><i class="bi bi-funnel"></i> Filter</button>
        @if(!empty($assembly) || !empty($district) || !empty($zone))
        <a href="{{ route('admin.reports', ['filter' => $filter, 'from' => $filter === 'custom' ? $from : '', 'to' => $filter === 'custom' ? $to : '']) }}" style="font-size:0.78rem;color:#c62828;text-decoration:none;font-weight:600;"><i class="bi bi-x-circle"></i> Clear</a>
        @endif
      </form>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info"><h3>{{ number_format($total) }}</h3><p>Total Registered</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-share-fill"></i></div>
        <div class="stat-info"><h3>{{ number_format($total_referral_count) }}</h3><p>Total Referrals</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-percent"></i></div>
        <div class="stat-info"><h3>{{ $total > 0 ? round(($total_referral_count / $total) * 100, 1) : 0 }}%</h3><p>Referral Rate</p></div>
      </div>
    </div>

    <!-- Members Table -->
    <div class="section">
      <div class="section-header">
        <h3><i class="bi bi-list-ul" style="color:#2e7d32;"></i> Registered Members ({{ number_format($total) }})</h3>
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
              <th>Name</th>
              <th>Unique ID</th>
              <th>Assembly</th>
              <th>District</th>
              <th>Zone</th>
              <th>Mobile</th>
              <th>Registered At</th>
              <th>Referred By</th>
              <th style="text-align:center;">Referral Count</th>
              <th>Your Members</th>
            </tr>
          </thead>
          <tbody>
            @foreach($members as $i => $m)
            <tr>
              <td><span class="serial">{{ $i + 1 }}</span></td>
              <td>
                @if(!empty($m['photo_url']))
                <img src="{{ $m['photo_url'] }}" class="member-avatar" alt="" loading="lazy">
                @else
                <div class="member-avatar-placeholder">{{ strtoupper(substr($m['name'] ?? '?', 0, 1)) }}</div>
                @endif
              </td>
              <td>
                <a href="{{ route('admin.user.detail', $m['unique_id'] ?? '') }}">{{ $m['name'] ?? 'N/A' }}</a>
              </td>
              <td><span style="font-weight:600;color:#2e7d32;font-size:0.78rem;">{{ $m['unique_id'] ?? '' }}</span></td>
              <td style="font-size:0.8rem;">{{ $m['assembly'] ?? '' }}</td>
              <td style="font-size:0.8rem;">{{ $m['district'] ?? '' }}</td>
              <td style="font-size:0.8rem;color:#1565c0;">{{ $m['zone'] ?? '' }}</td>
              <td style="font-size:0.8rem;">{{ $m['mobile'] ?? '' }}</td>
              <td style="font-size:0.75rem;white-space:nowrap;color:#555;">
                @php
                  $regDate = '';
                  if (!empty($m['created_at'])) {
                    try { $regDate = \Carbon\Carbon::parse($m['created_at'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A'); } catch (\Exception $e) { $regDate = $m['created_at']; }
                  } elseif (!empty($m['_id'])) {
                    try { $oid = $m['_id']; if (is_string($oid) && strlen($oid) === 24) { $ts = hexdec(substr($oid, 0, 8)); $regDate = \Carbon\Carbon::createFromTimestamp($ts)->setTimezone('Asia/Kolkata')->format('d M Y, h:i A'); } } catch (\Exception $e) {}
                  }
                @endphp
                {{ $regDate ?: '—' }}
              </td>
              <td>
                @if(!empty($m['referred_by']))
                  <span class="referrer-tag"><i class="bi bi-person-check"></i> {{ $m['referrer_name'] ?? $m['referred_by'] }}</span>
                @else
                  <span style="color:#ccc;font-size:0.75rem;">—</span>
                @endif
              </td>
              <td style="text-align:center;">
                @if(($m['referral_count'] ?? 0) > 0)
                  <span class="badge badge-green">{{ $m['referral_count'] }}</span>
                @else
                  <span style="color:#ccc;font-size:0.75rem;">0</span>
                @endif
              </td>
              <td>
                @if(!empty($m['referred_member_ids']))
                  <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($m['referred_member_ids'] as $refMemberId)
                      <a href="{{ route('admin.user.detail', $refMemberId) }}" class="referrer-tag" style="text-decoration:none;font-size:0.7rem;display:block;white-space:nowrap;">{{ $refMemberId }}</a>
                    @endforeach
                  </div>
                @else
                  <span style="color:#ccc;font-size:0.75rem;">—</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p>No members registered in this period</p>
        <p style="font-size:0.78rem;margin-top:6px;">Try selecting a different date range</p>
      </div>
      @endif
    </div>
  </div>

  <script>
    function downloadPDF() {
      // Build a printable document for PDF generation
      const table = document.getElementById('reportTable');
      if (!table) return;

      const filter = '{{ $filter }}';
      const from = '{{ $from }}';
      const to = '{{ $to }}';
      const total = {{ $total }};
      const totalReferralCount = {{ $total_referral_count }};
      const assemblyFilter = '{{ $assembly ?? '' }}';
      const districtFilter = '{{ $district ?? '' }}';
      const zoneFilter = '{{ $zone ?? '' }}';

      let filterLabel = 'Today';
      if (filter === 'weekly') filterLabel = 'This Week';
      else if (filter === 'monthly') filterLabel = 'This Month';
      else if (filter === 'custom') filterLabel = 'Custom Range';

      // Build filter info for header and filename
      let filterInfo = '';
      let fileNameParts = ['TNVS_Report'];
      if (assemblyFilter) {
        filterInfo += '<div style="font-size:12px;color:#007a38;font-weight:600;margin-bottom:2px;">Assembly: ' + assemblyFilter + '</div>';
        fileNameParts.push(assemblyFilter.replace(/[^a-zA-Z0-9]/g, '_'));
      } else {
        filterInfo += '<div style="font-size:12px;color:#555;margin-bottom:2px;">Assembly: All Assemblies</div>';
      }
      if (districtFilter) {
        filterInfo += '<div style="font-size:12px;color:#007a38;font-weight:600;margin-bottom:2px;">District: ' + districtFilter + '</div>';
        fileNameParts.push(districtFilter.replace(/[^a-zA-Z0-9]/g, '_'));
      } else {
        filterInfo += '<div style="font-size:12px;color:#555;margin-bottom:2px;">District: All Districts</div>';
      }
      if (zoneFilter) {
        filterInfo += '<div style="font-size:12px;color:#007a38;font-weight:600;margin-bottom:2px;">Zone: ' + zoneFilter + '</div>';
        fileNameParts.push(zoneFilter.replace(/[^a-zA-Z0-9]/g, '_'));
      } else {
        filterInfo += '<div style="font-size:12px;color:#555;margin-bottom:2px;">Zone: All Zones</div>';
      }
      fileNameParts.push(filterLabel.replace(/\s/g, '_'));
      fileNameParts.push(from + '_to_' + to);
      const fileName = fileNameParts.join('_');

      // Clone table rows for PDF (without images to keep it clean)
      let rows = '';
      const tbody = table.querySelector('tbody');
      const trs = tbody.querySelectorAll('tr');
      trs.forEach((tr, idx) => {
        const cells = tr.querySelectorAll('td');
        const name = cells[2]?.innerText?.trim() || '';
        const uid = cells[3]?.innerText?.trim() || '';
        const assembly = cells[4]?.innerText?.trim() || '';
        const district = cells[5]?.innerText?.trim() || '';
        const zone = cells[6]?.innerText?.trim() || '';
        const mobile = cells[7]?.innerText?.trim() || '';
        const registeredAt = cells[8]?.innerText?.trim() || '—';
        const referrer = cells[9]?.innerText?.trim() || '—';
        const refCount = cells[10]?.innerText?.trim() || '0';
        // Get each referred ID on its own line
        const yourMembersEl = cells[11];
        let yourMembersHtml = '—';
        if (yourMembersEl) {
          const links = yourMembersEl.querySelectorAll('a');
          if (links.length > 0) {
            yourMembersHtml = Array.from(links).map(a => a.innerText.trim()).join(',<br>');
          }
        }
        rows += '<tr>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;text-align:center;font-size:11px;">' + (idx + 1) + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:11px;">' + name + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;color:#2e7d32;font-weight:600;">' + uid + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + assembly + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + district + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;color:#1565c0;">' + zone + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + mobile + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + registeredAt + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + referrer + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;text-align:center;">' + refCount + '</td>'
          + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + yourMembersHtml + '</td>'
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
        <h1>Tamil Nadu Vanigargalin Sangamam — Members Report</h1>
        <div class="subtitle">${filterLabel}: ${from} to ${to}</div>
        ${filterInfo}
        <div class="stats">
          <div class="stat"><strong>${total}</strong><span>Total Registered</span></div>
          <div class="stat"><strong>${totalReferralCount}</strong><span>Total Referrals</span></div>
          <div class="stat"><strong>${total > 0 ? Math.round((totalReferralCount / total) * 1000) / 10 : 0}%</strong><span>Referral Rate</span></div>
        </div>
        <table>
          <thead><tr>
            <th>#</th><th>Name</th><th>Unique ID</th><th>Assembly</th><th>District</th><th>Zone</th><th>Mobile</th><th>Registered At</th><th>Referred By</th><th>Ref Count</th><th>Your Members</th>
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
