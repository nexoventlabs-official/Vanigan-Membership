@extends('sub_admin.layout', ['tab' => 'loan_requests'])

@section('title', 'Loan Requests')

@section('content')
  <div class="page-header">
    <h2><i class="bi bi-cash-coin" style="color:#1565c0;"></i> Loan Requests</h2>
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

  <div class="filter-bar no-print">
    <a href="{{ route('sub_admin.loan_requests', ['filter' => 'today']) }}"   class="filter-btn {{ $filter === 'today'   ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> Today</a>
    <a href="{{ route('sub_admin.loan_requests', ['filter' => 'weekly']) }}"  class="filter-btn {{ $filter === 'weekly'  ? 'active' : '' }}"><i class="bi bi-calendar-week"></i> Weekly</a>
    <a href="{{ route('sub_admin.loan_requests', ['filter' => 'monthly']) }}" class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}"><i class="bi bi-calendar-month"></i> Monthly</a>
    <a href="{{ route('sub_admin.loan_requests', ['filter' => 'all']) }}"     class="filter-btn {{ $filter === 'all'     ? 'active' : '' }}"><i class="bi bi-calendar"></i> All</a>
    <div class="filter-divider"></div>
    <form action="{{ route('sub_admin.loan_requests') }}" method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
      <input type="hidden" name="filter" value="custom">
      <input type="date" name="from" class="date-input" value="{{ $filter === 'custom' ? $from : '' }}" required>
      <span style="font-size:0.8rem;color:#888;">to</span>
      <input type="date" name="to" class="date-input" value="{{ $filter === 'custom' ? $to : '' }}" max="{{ now()->format('Y-m-d') }}" required>
      <button type="submit" class="apply-btn"><i class="bi bi-funnel"></i> Apply</button>
    </form>
  </div>

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

  <div class="section">
    <div class="section-header">
      <h3><i class="bi bi-list-ul" style="color:#1565c0;"></i> Loan Requests ({{ number_format($total) }})</h3>
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
      <table id="reportTable" style="min-width:800px;">
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
            <td style="font-weight:600;">{{ $r['member_name'] ?? 'N/A' }}</td>
            <td><span style="font-weight:600;color:#1565c0;font-size:0.78rem;">{{ $r['unique_id'] ?? '' }}</span></td>
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
    </div>
    @endif
  </div>
@endsection

@push('scripts')
<script>
  function downloadExcel() {
    const table = document.getElementById('reportTable');
    if (!table || typeof XLSX === 'undefined') { alert('Excel library not loaded.'); return; }

    const filter = '{{ $filter }}', from = '{{ $from }}', to = '{{ $to }}';
    let label = 'Today';
    if (filter === 'weekly') label = 'This Week';
    else if (filter === 'monthly') label = 'This Month';
    else if (filter === 'custom') label = 'Custom Range';
    else if (filter === 'all') label = 'All Time';

    const header = ['#', 'Member Name', 'Unique ID', 'Mobile', 'Business Type', 'Business Name', 'Status', 'Requested At'];
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
      ]);
    });

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(rows);
    ws['!cols'] = [ {wch:5},{wch:24},{wch:16},{wch:14},{wch:22},{wch:24},{wch:12},{wch:22} ];
    XLSX.utils.book_append_sheet(wb, ws, 'Loan Requests');

    const parts = ['TNVS_Loan_Requests', label.replace(/\s/g, '_')];
    if (from && to) parts.push(from + '_to_' + to);
    XLSX.writeFile(wb, parts.join('_') + '.xlsx');
  }

  function downloadPDF() {
    const table = document.getElementById('reportTable');
    if (!table) return;
    const filter = '{{ $filter }}', from = '{{ $from }}', to = '{{ $to }}', total = {{ $total }};
    let label = 'Today';
    if (filter === 'weekly') label = 'This Week';
    else if (filter === 'monthly') label = 'This Month';
    else if (filter === 'custom') label = 'Custom Range';
    else if (filter === 'all') label = 'All Time';

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
        + '</tr>';
    });

    const html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>TNVS_Loan_Requests</title>
      <style>
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        h1 { font-size: 18px; color: #1565c0; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { padding: 8px; border: 1px solid #ddd; background: #1565c0; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
        @media print { body { padding: 10px; } }
      </style>
    </head><body>
      <h1>TNVS — Loan Requests (${label})</h1>
      <div style="font-size:12px;color:#666;margin-bottom:12px;">${from && to ? from + ' to ' + to : 'All Time'} • Total: ${total}</div>
      <table>
        <thead><tr>
          <th>#</th><th>Name</th><th>Unique ID</th><th>Mobile</th><th>Biz Type</th><th>Biz Name</th><th>Status</th><th>Requested At</th>
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
