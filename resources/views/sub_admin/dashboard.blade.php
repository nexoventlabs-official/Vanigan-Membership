@extends('sub_admin.layout', ['tab' => 'dashboard'])

@section('title', 'Dashboard')

@section('content')
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
    <div class="stat-card">
      <div class="stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
      <div class="stat-info"><h3>{{ number_format($stats['pendingMembers'] ?? 0) }}</h3><p>Pending</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon pink"><i class="bi bi-cash-coin"></i></div>
      <div class="stat-info"><h3>{{ number_format($stats['totalLoanRequests'] ?? 0) }}</h3><p>Loan Requests</p></div>
    </div>
  </div>

  <!-- Top Referrers & Recent Members -->
  <div class="two-col">
    <div class="section">
      <div class="section-header"><h3><i class="bi bi-trophy-fill" style="color:#f57f17;"></i> Top Referrers</h3></div>
      <div class="section-body" style="padding:0;">
        @if(count($stats['topReferrers']) > 0)
        <table>
          <thead><tr><th>#</th><th>Member</th><th>Assembly</th><th style="text-align:right;">Referrals</th></tr></thead>
          <tbody>
            @foreach($stats['topReferrers'] as $i => $ref)
            @php $refUid = $ref['unique_id'] ?? ''; @endphp
            <tr @if($refUid) class="clickable-row" data-href="{{ route('sub_admin.user.detail', $refUid) }}" style="cursor:pointer;" @endif>
              <td><span class="serial">{{ $i + 1 }}</span></td>
              <td>
                <span class="member-name">{{ $ref['name'] ?? 'N/A' }}</span><br>
                <span style="font-size:0.72rem;color:#999;">{{ $ref['mobile'] ?? '' }}</span>
              </td>
              <td style="font-size:0.8rem;">{{ $ref['assembly'] ?? '' }}</td>
              <td style="text-align:right;font-weight:700;color:#1565c0;">{{ $ref['referral_count'] ?? 0 }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div class="empty-state"><p>No referrals yet</p></div>
        @endif
      </div>
    </div>

    <div class="section">
      <div class="section-header">
        <h3><i class="bi bi-clock-history" style="color:#1565c0;"></i> Recent Members</h3>
        <a href="{{ route('sub_admin.users') }}" style="font-size:0.8rem;color:#1565c0;text-decoration:none;font-weight:600;">View All →</a>
      </div>
      <div class="section-body" style="padding:0;">
        @if(count($stats['recentMembers']) > 0)
        <table>
          <thead><tr><th></th><th>Name</th><th>Assembly</th><th>Joined</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($stats['recentMembers'] as $m)
            @php
              $uid = $m['unique_id'] ?? '';
              $joined = '';
              if (!empty($m['created_at'])) {
                try { $joined = \Carbon\Carbon::parse($m['created_at'])->setTimezone('Asia/Kolkata')->format('d M Y'); } catch (\Exception $e) { $joined = (string) $m['created_at']; }
              } elseif (!empty($m['_id'])) {
                try { $oid = (string) $m['_id']; if (strlen($oid) === 24) { $ts = hexdec(substr($oid, 0, 8)); $joined = \Carbon\Carbon::createFromTimestamp($ts)->setTimezone('Asia/Kolkata')->format('d M Y'); } } catch (\Exception $e) {}
              }
            @endphp
            <tr @if($uid) class="clickable-row" data-href="{{ route('sub_admin.user.detail', $uid) }}" style="cursor:pointer;" @endif>
              <td>
                @if(!empty($m['photo_url']))
                <img src="{{ $m['photo_url'] }}" class="member-avatar" alt="">
                @else
                <div class="member-avatar-placeholder">{{ strtoupper(substr($m['name'] ?? '?', 0, 1)) }}</div>
                @endif
              </td>
              <td>
                <span class="member-name">{{ $m['name'] ?? 'N/A' }}</span><br>
                <span style="font-size:0.72rem;color:#999;">{{ $uid }}</span>
              </td>
              <td style="font-size:0.8rem;">{{ $m['assembly'] ?? '' }}</td>
              <td style="font-size:0.75rem;color:#555;white-space:nowrap;">{{ $joined ?: '—' }}</td>
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
        <div class="empty-state"><p>No members yet</p></div>
        @endif
      </div>
    </div>
  </div>

  <!-- Assembly & District Charts -->
  <div class="two-col">
    <div class="section">
      <div class="section-header"><h3><i class="bi bi-bar-chart-fill" style="color:#1565c0;"></i> Members by Assembly</h3></div>
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
        <div class="empty-state"><p>No data yet</p></div>
        @endif
      </div>
    </div>

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
        <div class="empty-state"><p>No data yet</p></div>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Make table rows tagged with .clickable-row navigate to data-href on click
  document.querySelectorAll('tr.clickable-row[data-href]').forEach(function(row){
    row.addEventListener('click', function(e){
      // Ignore clicks that originate from links/buttons inside the row
      if (e.target.closest('a, button')) return;
      window.location.href = row.dataset.href;
    });
    row.addEventListener('mouseenter', function(){ row.style.background = '#f5f9ff'; });
    row.addEventListener('mouseleave', function(){ row.style.background = ''; });
  });
</script>
@endpush
