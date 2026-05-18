@extends('sub_admin.layout', ['tab' => 'reports'])

@section('title', 'Reports')

@section('content')
  <div class="page-header">
    <h2><i class="bi bi-file-earmark-bar-graph-fill" style="color:#1565c0;"></i> Reports</h2>
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

  <div class="filter-bar no-print">
    <a href="{{ route('sub_admin.reports', ['filter' => 'today']) }}"   class="filter-btn {{ $filter === 'today'   ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> Today</a>
    <a href="{{ route('sub_admin.reports', ['filter' => 'weekly']) }}"  class="filter-btn {{ $filter === 'weekly'  ? 'active' : '' }}"><i class="bi bi-calendar-week"></i> Weekly</a>
    <a href="{{ route('sub_admin.reports', ['filter' => 'monthly']) }}" class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}"><i class="bi bi-calendar-month"></i> Monthly</a>
    <div class="filter-divider"></div>
    <form action="{{ route('sub_admin.reports') }}" method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
      <input type="hidden" name="filter" value="custom">
      <input type="date" name="from" class="date-input" value="{{ $filter === 'custom' ? $from : '' }}" required>
      <span style="font-size:0.8rem;color:#888;">to</span>
      <input type="date" name="to" class="date-input" value="{{ $filter === 'custom' ? $to : '' }}" max="{{ now()->format('Y-m-d') }}" required>
      <button type="submit" class="apply-btn"><i class="bi bi-funnel"></i> Apply</button>
    </form>
    <div class="filter-divider"></div>
    <form action="{{ route('sub_admin.reports') }}" method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
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
      <a href="{{ route('sub_admin.reports', ['filter' => $filter, 'from' => $filter === 'custom' ? $from : '', 'to' => $filter === 'custom' ? $to : '']) }}" style="font-size:0.78rem;color:#c62828;text-decoration:none;font-weight:600;"><i class="bi bi-x-circle"></i> Clear</a>
      @endif
    </form>
  </div>

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

  <div class="section">
    <div class="section-header">
      <h3><i class="bi bi-list-ul" style="color:#1565c0;"></i> Registered Members ({{ number_format($total) }})</h3>
      @if($total > 0)
      <div class="download-wrap">
        <button class="download-btn" type="button" onclick="toggleDownloadMenu(event)"><i class="bi bi-download"></i> Download <i class="bi bi-chevron-down" style="font-size:0.68rem;"></i></button>
        <div class="download-menu" id="downloadMenu">
          <button type="button" class="dl-pdf" onclick="closeDownloadMenu();downloadPDF();"><i class="bi bi-file-earmark-pdf-fill"></i> PDF</button>
          <button type="button" class="dl-xls" onclick="closeDownloadMenu();downloadExcel();"><i class="bi bi-file-earmark-excel-fill"></i> Excel</button>
        </div>
      </div>
      @endif
    </div>

    @if($total > 0)
    <div class="table-wrap">
      <table id="reportTable" style="min-width:1100px;">
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
          </tr>
        </thead>
        <tbody>
          @foreach($members as $i => $m)
          @php $uid = $m['unique_id'] ?? ''; @endphp
          <tr @if($uid) class="clickable-row" data-href="{{ route('sub_admin.user.detail', $uid) }}" style="cursor:pointer;" @endif>
            <td><span class="serial">{{ $i + 1 }}</span></td>
            <td>
              @if(!empty($m['photo_url']))
              <img src="{{ $m['photo_url'] }}" class="member-avatar" alt="" loading="lazy">
              @else
              <div class="member-avatar-placeholder">{{ strtoupper(substr($m['name'] ?? '?', 0, 1)) }}</div>
              @endif
            </td>
            <td><span class="member-name">{{ $m['name'] ?? 'N/A' }}</span></td>
            <td><span style="font-weight:600;color:#1565c0;font-size:0.78rem;">{{ $m['unique_id'] ?? '' }}</span></td>
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
@endsection

@push('scripts')
<script>
  // Clickable member rows -> sub_admin.user.detail
  document.querySelectorAll('tr.clickable-row[data-href]').forEach(function(row){
    row.addEventListener('click', function(e){
      if (e.target.closest('a, button')) return;
      window.location.href = row.dataset.href;
    });
    row.addEventListener('mouseenter', function(){ row.style.background = '#f5f9ff'; });
    row.addEventListener('mouseleave', function(){ row.style.background = ''; });
  });

  function downloadExcel() {
    const table = document.getElementById('reportTable');
    if (!table || typeof XLSX === 'undefined') { alert('Excel library not loaded.'); return; }

    const filter = '{{ $filter }}';
    const from = '{{ $from }}';
    const to = '{{ $to }}';

    let filterLabel = 'Today';
    if (filter === 'weekly') filterLabel = 'This Week';
    else if (filter === 'monthly') filterLabel = 'This Month';
    else if (filter === 'custom') filterLabel = 'Custom Range';

    const header = ['#', 'Name', 'Unique ID', 'Assembly', 'District', 'Zone', 'Mobile', 'Registered At', 'Referred By', 'Referral Count'];
    const rows = [header];
    table.querySelectorAll('tbody tr').forEach((tr, idx) => {
      const c = tr.querySelectorAll('td');
      rows.push([
        idx + 1,
        c[2]?.innerText?.trim() || '',
        c[3]?.innerText?.trim() || '',
        c[4]?.innerText?.trim() || '',
        c[5]?.innerText?.trim() || '',
        c[6]?.innerText?.trim() || '',
        c[7]?.innerText?.trim() || '',
        c[8]?.innerText?.trim() || '',
        c[9]?.innerText?.trim() || '',
        c[10]?.innerText?.trim() || '0',
      ]);
    });

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(rows);
    ws['!cols'] = [ {wch:5},{wch:24},{wch:16},{wch:18},{wch:16},{wch:16},{wch:14},{wch:20},{wch:22},{wch:8} ];
    XLSX.utils.book_append_sheet(wb, ws, 'Members');

    const parts = ['TNVS_Report', filterLabel.replace(/\s/g, '_')];
    if (from && to) parts.push(from + '_to_' + to);
    XLSX.writeFile(wb, parts.join('_') + '.xlsx');
  }

  function downloadPDF() {
    const table = document.getElementById('reportTable');
    if (!table) return;

    const filter = '{{ $filter }}';
    const from = '{{ $from }}';
    const to = '{{ $to }}';
    const total = {{ $total }};
    const totalReferralCount = {{ $total_referral_count }};

    let filterLabel = 'Today';
    if (filter === 'weekly') filterLabel = 'This Week';
    else if (filter === 'monthly') filterLabel = 'This Month';
    else if (filter === 'custom') filterLabel = 'Custom Range';

    let rows = '';
    table.querySelectorAll('tbody tr').forEach((tr, idx) => {
      const c = tr.querySelectorAll('td');
      rows += '<tr>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;text-align:center;font-size:11px;">' + (idx + 1) + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:11px;">' + (c[2]?.innerText?.trim() || '') + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;color:#1565c0;font-weight:600;">' + (c[3]?.innerText?.trim() || '') + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + (c[4]?.innerText?.trim() || '') + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + (c[5]?.innerText?.trim() || '') + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + (c[6]?.innerText?.trim() || '') + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + (c[7]?.innerText?.trim() || '') + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + (c[8]?.innerText?.trim() || '') + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;">' + (c[9]?.innerText?.trim() || '—') + '</td>'
        + '<td style="padding:6px 8px;border:1px solid #ddd;font-size:10px;text-align:center;">' + (c[10]?.innerText?.trim() || '0') + '</td>'
        + '</tr>';
    });

    const html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>TNVS_Report</title>
      <style>
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        h1 { font-size: 18px; color: #1565c0; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { padding: 8px; border: 1px solid #ddd; background: #1565c0; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
        @media print { body { padding: 10px; } }
      </style>
    </head><body>
      <h1>TNVS — Members Report (${filterLabel})</h1>
      <div style="font-size:12px;color:#666;margin-bottom:12px;">${from && to ? from + ' to ' + to : ''} • Total: ${total} • Referrals: ${totalReferralCount}</div>
      <table>
        <thead><tr>
          <th>#</th><th>Name</th><th>Unique ID</th><th>Assembly</th><th>District</th><th>Zone</th><th>Mobile</th><th>Registered</th><th>Referred By</th><th>Refs</th>
        </tr></thead>
        <tbody>${rows}</tbody>
      </table>
      <div style="margin-top:20px;font-size:10px;color:#999;text-align:center;">Generated on ${new Date().toLocaleString('en-IN')} — Vanigan Sub-Admin</div>
      <script>window.onload = function() { window.print(); }<\/script>
    </body></html>`;

    const win = window.open('', '_blank');
    win.document.write(html);
    win.document.close();
  }
</script>
@endpush
