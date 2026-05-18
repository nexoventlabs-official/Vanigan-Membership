@extends('sub_admin.layout', ['tab' => 'not_registered'])

@section('title', 'Not Registered')

@push('styles')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<style>
  .badge-step { background: #fff3e0; color: #e65100; }
  .badge-step.step-mobile { background: #fce4ec; color: #c62828; }
  .badge-step.step-otp    { background: #fff3e0; color: #e65100; }
  .badge-step.step-epic   { background: #e3f2fd; color: #1565c0; }
  .badge-step.step-photo  { background: #f3e5f5; color: #7b1fa2; }
  .badge-step.step-pin    { background: #e8f5e9; color: #2e7d32; }
  .ref-name { font-weight: 600; color: #1a1a1a; font-size: 0.78rem; }
  .ref-id   { font-size: 0.68rem; color: #999; font-family: monospace; }
</style>
@endpush

@section('content')
  @php
    $zoneConfig = config('zone_data');
    $asmMap     = $zoneConfig['assembly_map']  ?? [];
    $distZone   = $zoneConfig['district_zone'] ?? [];
    $subNrLookup = function ($assemblyName) use ($asmMap) {
        $asmUpper = strtoupper(trim(preg_replace('/\s+/', ' ', $assemblyName ?? '')));
        $matched  = $asmMap[$asmUpper] ?? null;
        if (!$matched) {
            $norm = preg_replace('/[\. \-\(\)]/', '', $asmUpper);
            foreach ($asmMap as $k => $v) {
                if (preg_replace('/[\. \-\(\)]/', '', $k) === $norm) { $matched = $v; break; }
            }
        }
        if ($matched) return ['d' => ucwords(strtolower($matched['d'])), 'z' => ucwords(strtolower($matched['z']))];
        return ['d' => null, 'z' => null];
    };
  @endphp

  <div class="page-header">
    <h2><i class="bi bi-person-x-fill" style="color:#e65100;"></i> Not Registered Members</h2>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
      <span class="total-badge">{{ number_format($total) }} incomplete</span>
      @if($filter !== 'all')
      <span class="date-label">
        <i class="bi bi-calendar-event"></i>
        @if($filter === 'today') Today: {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
        @elseif($filter === 'weekly') Weekly: {{ \Carbon\Carbon::parse($from)->format('d M') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @elseif($filter === 'monthly') Monthly: {{ \Carbon\Carbon::parse($from)->format('d M') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @elseif($filter === 'custom') {{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        @endif
      </span>
      @endif
    </div>
  </div>

  @if(!empty($stats))
  @php
    $stepLabels = [
      'mobile_entered' => ['Mobile Entered', 'red'],
      'epic_validated' => ['EPIC Validated', 'blue'],
      'photo_uploaded' => ['Photo Uploaded', 'purple'],
      'pin_set'        => ['PIN Set', 'green'],
    ];
    $totalIncomplete = array_sum($stats);
  @endphp
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon orange"><i class="bi bi-clipboard-x"></i></div>
      <div class="stat-info"><h3>{{ number_format($totalIncomplete) }}</h3><p>Total Incomplete</p></div>
    </div>
    @foreach($stepLabels as $key => $info)
      @if(isset($stats[$key]))
      <div class="stat-card">
        <div class="stat-icon {{ $info[1] }}"><i class="bi bi-pause-circle"></i></div>
        <div class="stat-info"><h3>{{ $stats[$key] }}</h3><p>Stopped at {{ $info[0] }}</p></div>
      </div>
      @endif
    @endforeach
  </div>
  @endif

  <div class="filter-bar no-print">
    <a href="{{ route('sub_admin.not_registered', array_merge(request()->except(['filter','from','to','page']), ['filter' => 'today'])) }}"   class="filter-btn {{ $filter === 'today'   ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> Today</a>
    <a href="{{ route('sub_admin.not_registered', array_merge(request()->except(['filter','from','to','page']), ['filter' => 'weekly'])) }}"  class="filter-btn {{ $filter === 'weekly'  ? 'active' : '' }}"><i class="bi bi-calendar-week"></i> Weekly</a>
    <a href="{{ route('sub_admin.not_registered', array_merge(request()->except(['filter','from','to','page']), ['filter' => 'monthly'])) }}" class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}"><i class="bi bi-calendar-month"></i> Monthly</a>
    <a href="{{ route('sub_admin.not_registered', array_merge(request()->except(['filter','from','to','page']), ['filter' => 'all'])) }}"     class="filter-btn {{ $filter === 'all'     ? 'active' : '' }}"><i class="bi bi-calendar3"></i> All Time</a>
    <div class="filter-divider"></div>
    <form action="{{ route('sub_admin.not_registered') }}" method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
      <input type="hidden" name="filter" value="custom">
      @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
      @if($step)<input type="hidden" name="step" value="{{ $step }}">@endif
      <input type="date" name="from" class="date-input" value="{{ $filter === 'custom' ? $from : '' }}" required>
      <span style="font-size:0.8rem;color:#888;">to</span>
      <input type="date" name="to" class="date-input" value="{{ $filter === 'custom' ? $to : '' }}" required>
      <button type="submit" class="apply-btn"><i class="bi bi-funnel"></i> Apply</button>
    </form>
    @if($total > 0)
    <div class="filter-divider"></div>
    <div class="download-wrap">
      <button class="download-btn" type="button" onclick="toggleDownloadMenu(event)"><i class="bi bi-download"></i> Download <i class="bi bi-chevron-down" style="font-size:0.68rem;"></i></button>
      <div class="download-menu" id="downloadMenu">
        <button type="button" class="dl-pdf" onclick="closeDownloadMenu();downloadPDF();"><i class="bi bi-file-earmark-pdf-fill"></i> PDF</button>
        <button type="button" class="dl-xls" onclick="closeDownloadMenu();downloadExcel();"><i class="bi bi-file-earmark-excel-fill"></i> Excel</button>
      </div>
    </div>
    @endif
  </div>

  <div class="filters">
    <form method="GET" action="{{ route('sub_admin.not_registered') }}">
      <input type="hidden" name="filter" value="{{ $filter }}">
      @if($filter === 'custom')
      <input type="hidden" name="from" value="{{ $from }}">
      <input type="hidden" name="to" value="{{ $to }}">
      @endif
      <input type="text" name="search" placeholder="Search mobile, name, or EPIC..." value="{{ $search }}">
      <select name="step">
        <option value="">All Steps</option>
        <option value="mobile_entered" {{ $step === 'mobile_entered' ? 'selected' : '' }}>Stopped at Mobile</option>
        <option value="epic_validated" {{ $step === 'epic_validated' ? 'selected' : '' }}>Stopped at EPIC</option>
        <option value="photo_uploaded" {{ $step === 'photo_uploaded' ? 'selected' : '' }}>Stopped at Photo</option>
        <option value="pin_set" {{ $step === 'pin_set' ? 'selected' : '' }}>Stopped at PIN</option>
      </select>
      <button type="submit" class="apply-btn"><i class="bi bi-search"></i> Search</button>
      @if($search || $step)
      <a href="{{ route('sub_admin.not_registered', ['filter' => $filter, 'from' => $filter === 'custom' ? $from : '', 'to' => $filter === 'custom' ? $to : '']) }}" class="clear-btn"><i class="bi bi-x-circle"></i> Clear</a>
      @endif
    </form>
  </div>

  <div class="section">
    @if(count($users) > 0)
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Mobile</th>
            <th>Name</th>
            <th class="hide-mobile">EPIC No</th>
            <th class="hide-mobile">Assembly</th>
            <th class="hide-mobile">District</th>
            <th class="hide-mobile">Zone</th>
            <th>Last Step</th>
            <th class="hide-mobile">Referred By</th>
            <th class="hide-mobile">Started At</th>
            <th class="hide-mobile">Last Activity</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $i => $u)
          @php
            $stepLabel = $u['last_step'] ?? 'unknown';
            $stepClass = '';
            if (str_contains($stepLabel, 'mobile')) $stepClass = 'step-mobile';
            elseif (str_contains($stepLabel, 'otp')) $stepClass = 'step-otp';
            elseif (str_contains($stepLabel, 'epic')) $stepClass = 'step-epic';
            elseif (str_contains($stepLabel, 'photo')) $stepClass = 'step-photo';
            elseif (str_contains($stepLabel, 'pin')) $stepClass = 'step-pin';

            $stepDisplay  = ucwords(str_replace('_', ' ', $stepLabel));
            $startedAt    = isset($u['started_at']) ? \Carbon\Carbon::parse($u['started_at'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') : '—';
            $lastActivity = isset($u['last_activity']) ? \Carbon\Carbon::parse($u['last_activity'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') : '—';
            $refName = $u['referrer_name'] ?? '';
            $refId   = $u['referrer_unique_id'] ?? '';
            $dz      = $subNrLookup($u['assembly'] ?? '');
          @endphp
          <tr>
            <td style="color:#999;">{{ ($page - 1) * 20 + $i + 1 }}</td>
            <td style="font-family:monospace;font-weight:600;">{{ $u['mobile'] ?? '' }}</td>
            <td>{{ $u['name'] ?? '—' }}</td>
            <td class="hide-mobile" style="font-family:monospace;font-size:0.78rem;">{{ $u['epic_no'] ?? '—' }}</td>
            <td class="hide-mobile">{{ $u['assembly'] ?? '—' }}</td>
            <td class="hide-mobile">{{ $dz['d'] ?? ucwords(strtolower($u['district'] ?? '—')) }}</td>
            <td class="hide-mobile" style="font-size:0.78rem;color:#1565c0;">{{ $dz['z'] ?? '—' }}</td>
            <td><span class="badge badge-step {{ $stepClass }}">{{ $stepDisplay }}</span></td>
            <td class="hide-mobile">
              @if($refId)
              <div class="ref-name">{{ $refName ?: '—' }}</div>
              <div class="ref-id">{{ $refId }}</div>
              @else
              <span style="color:#ccc;">—</span>
              @endif
            </td>
            <td class="hide-mobile" style="font-size:0.76rem;color:#666;">{{ $startedAt }}</td>
            <td class="hide-mobile" style="font-size:0.76rem;color:#666;">{{ $lastActivity }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($pages > 1)
    <div class="pagination">
      @if($page > 1)
        <a href="{{ route('sub_admin.not_registered', array_merge(request()->query(), ['page' => $page - 1])) }}">&laquo;</a>
      @endif

      @for($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++)
        @if($p === $page)
          <span class="current">{{ $p }}</span>
        @else
          <a href="{{ route('sub_admin.not_registered', array_merge(request()->query(), ['page' => $p])) }}">{{ $p }}</a>
        @endif
      @endfor

      @if($page < $pages)
        <a href="{{ route('sub_admin.not_registered', array_merge(request()->query(), ['page' => $page + 1])) }}">&raquo;</a>
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
@endsection

@push('scripts')
<script>
  async function fetchAllNotRegisteredSub() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    params.delete('page');
    const res = await fetch('{{ route('sub_admin.not_registered') }}?' + params.toString(), {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    });
    if (!res.ok) throw new Error('Export request failed (' + res.status + ')');
    return res.json();
  }

  function _withBusyBtnNR(fn) {
    return async function () {
      const btn = document.querySelector('.download-btn');
      const originalHtml = btn ? btn.innerHTML : '';
      if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Preparing...'; }
      try { await fn(); }
      catch (err) { console.error(err); alert('Failed: ' + err.message); }
      finally { if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; } }
    };
  }

  const downloadExcel = _withBusyBtnNR(async function () {
    if (typeof XLSX === 'undefined') { alert('Excel library not loaded.'); return; }
    const data = await fetchAllNotRegisteredSub();
    const rows = Array.isArray(data.rows) ? data.rows : [];
    const header = ['#', 'Mobile', 'Name', 'EPIC No', 'Assembly', 'District', 'Zone', 'Last Step', 'Referred By', 'Started At', 'Last Activity'];
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet([header, ...rows]);
    ws['!cols'] = [ {wch:5},{wch:14},{wch:22},{wch:18},{wch:18},{wch:16},{wch:14},{wch:20},{wch:28},{wch:22},{wch:22} ];
    XLSX.utils.book_append_sheet(wb, ws, 'Not Registered');

    const filter = data.filter || '{{ $filter }}';
    const step = data.step || '';
    let fileName = 'Not_Registered_Report';
    if (filter && filter !== 'all') fileName += '_' + filter;
    if (step) fileName += '_' + step;
    XLSX.writeFile(wb, fileName + '.xlsx');
  });

  const downloadPDF = _withBusyBtnNR(async function () {
    const data = await fetchAllNotRegisteredSub();
    const rows = Array.isArray(data.rows) ? data.rows : [];
    const total = data.total ?? rows.length;

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');

    const filter = data.filter || '{{ $filter }}';
    const from = data.from || '';
    const to = data.to || '';
    const step = data.step || '';

    let title = 'Not Registered Members Report';
    let subtitle = 'All Time';
    if (filter === 'today') subtitle = 'Today: ' + from;
    else if (filter === 'weekly') subtitle = 'Weekly: ' + from + ' to ' + to;
    else if (filter === 'monthly') subtitle = 'Monthly: ' + from + ' to ' + to;
    else if (filter === 'custom') subtitle = 'Custom: ' + from + ' to ' + to;
    if (step) subtitle += ' | Step: ' + step.replace(/_/g, ' ');

    doc.setFontSize(16); doc.setTextColor(21, 101, 192);
    doc.text(title, 14, 15);
    doc.setFontSize(10); doc.setTextColor(100);
    doc.text(subtitle, 14, 22);
    doc.text('Total: ' + total + ' incomplete registrations', 14, 28);
    doc.text('Generated: ' + new Date().toLocaleString('en-IN', { timeZone: 'Asia/Kolkata' }), 14, 34);

    doc.autoTable({
      startY: 38,
      head: [['#', 'Mobile', 'Name', 'EPIC No', 'Assembly', 'District', 'Zone', 'Last Step', 'Referred By', 'Started At', 'Last Activity']],
      body: rows,
      styles: { fontSize: 7.5, cellPadding: 2.5 },
      headStyles: { fillColor: [21, 101, 192], textColor: 255, fontStyle: 'bold', fontSize: 7.5 },
      alternateRowStyles: { fillColor: [245, 250, 255] },
      columnStyles: { 0: { cellWidth: 10 }, 1: { cellWidth: 25 }, 7: { cellWidth: 30 } },
      margin: { left: 14, right: 14 },
    });

    let fileName = 'Not_Registered_Report';
    if (filter !== 'all') fileName += '_' + filter;
    if (step) fileName += '_' + step;
    fileName += '.pdf';
    doc.save(fileName);
  });
</script>
@endpush
