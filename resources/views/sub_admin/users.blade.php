@extends('sub_admin.layout', ['tab' => 'members'])

@section('title', 'Members')

@section('content')
  <div class="page-header">
    <h2><i class="bi bi-people-fill" style="color:#1565c0;"></i> All Members</h2>
    <span class="total-badge">{{ number_format($total) }} total</span>
  </div>

  <div class="filters">
    <form method="GET" action="{{ route('sub_admin.users') }}">
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
      <button type="submit" class="apply-btn"><i class="bi bi-search"></i> Search</button>
      @if($search || $assembly || $district)
      <a href="{{ route('sub_admin.users') }}" class="clear-btn"><i class="bi bi-x-circle"></i> Clear</a>
      @endif
    </form>
  </div>

  <div class="section">
    @if(count($members) > 0)
    <div class="table-wrap">
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
            <th class="hide-mobile">Joined Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($members as $m)
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
              <div class="member-name">{{ $m['name'] ?? 'N/A' }}</div>
              <div class="member-id">{{ $uid }}</div>
            </td>
            <td class="hide-mobile" style="font-family:monospace;font-size:0.8rem;">{{ $m['epic_no'] ?? '' }}</td>
            <td>{{ $m['assembly'] ?? '' }}</td>
            <td class="hide-mobile">{{ $m['district'] ?? '' }}</td>
            <td class="hide-mobile" style="font-size:0.8rem;color:#1565c0;">{{ $m['zone'] ?? '' }}</td>
            <td class="hide-mobile">
              @if(($m['referral_count'] ?? 0) > 0)
              <span style="display:inline-flex;align-items:center;gap:4px;font-weight:600;color:#1565c0;"><i class="bi bi-share-fill"></i> {{ $m['referral_count'] }}</span>
              @else
              <span style="color:#ccc;">0</span>
              @endif
            </td>
            <td class="hide-mobile" style="font-size:0.78rem;color:#555;white-space:nowrap;">{{ $joined ?: '—' }}</td>
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
    </div>

    @if($pages > 1)
    <div class="pagination">
      @if($page > 1)
      <a href="{{ route('sub_admin.users', array_merge(request()->query(), ['page' => $page - 1])) }}"><i class="bi bi-chevron-left"></i></a>
      @endif

      @for($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++)
        @if($p === $page)
        <span class="current">{{ $p }}</span>
        @else
        <a href="{{ route('sub_admin.users', array_merge(request()->query(), ['page' => $p])) }}">{{ $p }}</a>
        @endif
      @endfor

      @if($page < $pages)
      <a href="{{ route('sub_admin.users', array_merge(request()->query(), ['page' => $page + 1])) }}"><i class="bi bi-chevron-right"></i></a>
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
@endsection

@push('scripts')
<script>
  document.querySelectorAll('tr.clickable-row[data-href]').forEach(function(row){
    row.addEventListener('click', function(e){
      if (e.target.closest('a, button')) return;
      window.location.href = row.dataset.href;
    });
    row.addEventListener('mouseenter', function(){ row.style.background = '#f5f9ff'; });
    row.addEventListener('mouseleave', function(){ row.style.background = ''; });
  });
</script>
@endpush
