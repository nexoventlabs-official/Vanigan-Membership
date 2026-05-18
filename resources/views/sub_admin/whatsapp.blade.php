@extends('sub_admin.layout', ['tab' => 'whatsapp'])

@section('title', 'WhatsApp Users')

@push('styles')
<style>
  .badge-pending   { background: #fff3e0; color: #e65100; }
  .badge-welcome   { background: #e3f2fd; color: #1565c0; }
  .badge-existing  { background: #e8f5e9; color: #2e7d32; }
  .badge-registered { background: #f3e5f5; color: #7b1fa2; }
  .phone-cell { display: inline-flex; align-items: center; gap: 6px; font-family: monospace; font-weight: 600; }
  .wa-icon { color: #25D366; font-size: 1rem; }
</style>
@endpush

@section('content')
  <div class="page-header">
    <h2><i class="bi bi-whatsapp" style="color:#25D366;"></i> WhatsApp Users</h2>
    <span class="total-badge">{{ number_format($total) }} users</span>
  </div>

  @if(!empty($stats))
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
      <div class="stat-info"><h3>{{ number_format($stats['total'] ?? 0) }}</h3><p>Total Users</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
      <div class="stat-info"><h3>{{ $stats['pending'] ?? 0 }}</h3><p>Pending</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-send"></i></div>
      <div class="stat-info"><h3>{{ $stats['welcome_sent'] ?? 0 }}</h3><p>Welcome Sent</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
      <div class="stat-info"><h3>{{ $stats['existing_member'] ?? 0 }}</h3><p>Existing Members</p></div>
    </div>
  </div>
  @endif

  <div class="filters">
    <form method="GET" action="{{ route('sub_admin.whatsapp') }}">
      <input type="text" name="search" placeholder="Search phone or name..." value="{{ $search ?? '' }}">
      <select name="status">
        <option value="">All Status</option>
        <option value="pending"         {{ ($status ?? '') === 'pending'         ? 'selected' : '' }}>Pending</option>
        <option value="welcome_sent"    {{ ($status ?? '') === 'welcome_sent'    ? 'selected' : '' }}>Welcome Sent</option>
        <option value="existing_member" {{ ($status ?? '') === 'existing_member' ? 'selected' : '' }}>Existing Member</option>
      </select>
      <button type="submit" class="apply-btn"><i class="bi bi-search"></i> Search</button>
      @if(($search ?? '') || ($status ?? ''))
      <a href="{{ route('sub_admin.whatsapp') }}" class="clear-btn"><i class="bi bi-x-circle"></i> Clear</a>
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
            <th>Phone</th>
            <th>Contact Name</th>
            <th class="hide-mobile">Last Message</th>
            <th>Status</th>
            <th class="hide-mobile">Messages</th>
            <th class="hide-mobile">First Contact</th>
            <th>Last Activity</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $i => $u)
          @php
            $statusClass = 'badge-pending';
            $statusLabel = ucwords(str_replace('_', ' ', $u['status'] ?? 'pending'));
            if (($u['status'] ?? '') === 'welcome_sent')    $statusClass = 'badge-welcome';
            elseif (($u['status'] ?? '') === 'existing_member') $statusClass = 'badge-existing';
            elseif (($u['status'] ?? '') === 'registered')      $statusClass = 'badge-registered';

            $firstContact = isset($u['first_contact']) ? \Carbon\Carbon::parse($u['first_contact'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') : '—';
            $lastActivity = isset($u['last_activity']) ? \Carbon\Carbon::parse($u['last_activity'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') : '—';
          @endphp
          <tr>
            <td style="color:#999;">{{ ($page - 1) * 20 + $i + 1 }}</td>
            <td>
              <div class="phone-cell">
                <i class="bi bi-whatsapp wa-icon"></i>
                {{ $u['phone'] ?? '' }}
              </div>
            </td>
            <td>{{ $u['contact_name'] ?? '—' }}</td>
            <td class="hide-mobile" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $u['last_message'] ?? '—' }}</td>
            <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
            <td class="hide-mobile" style="text-align:center;">{{ $u['message_count'] ?? 0 }}</td>
            <td class="hide-mobile" style="font-size:0.76rem;color:#666;">{{ $firstContact }}</td>
            <td style="font-size:0.76rem;color:#666;">{{ $lastActivity }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($pages > 1)
    <div class="pagination">
      @if($page > 1)
        <a href="{{ route('sub_admin.whatsapp', array_merge(request()->query(), ['page' => $page - 1])) }}">&laquo;</a>
      @endif

      @for($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++)
        @if($p === $page)
          <span class="current">{{ $p }}</span>
        @else
          <a href="{{ route('sub_admin.whatsapp', array_merge(request()->query(), ['page' => $p])) }}">{{ $p }}</a>
        @endif
      @endfor

      @if($page < $pages)
        <a href="{{ route('sub_admin.whatsapp', array_merge(request()->query(), ['page' => $page + 1])) }}">&raquo;</a>
      @endif
    </div>
    @endif

    @else
    <div class="empty-state">
      <i class="bi bi-whatsapp"></i>
      <p>No WhatsApp users found.</p>
      <p style="font-size:0.8rem;margin-top:4px;">Users who contact via WhatsApp will appear here.</p>
    </div>
    @endif
  </div>
@endsection
