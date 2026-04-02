<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>WhatsApp Users — Vanigan Admin</title>
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

    .container { max-width: 1400px; margin: 0 auto; padding: 24px 20px; }

    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .page-header h2 { font-size: 1.3rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .page-header h2 i { color: #25D366; }
    .header-right { display: flex; align-items: center; gap: 10px; }
    .total-badge { background: #e8f5e9; color: #2e7d32; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center; }
    .stat-card .stat-count { font-size: 1.6rem; font-weight: 800; color: #25D366; }
    .stat-card .stat-label { font-size: 0.72rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; font-weight: 600; }
    .stat-card.orange .stat-count { color: #e65100; }
    .stat-card.blue .stat-count { color: #1565c0; }
    .stat-card.green .stat-count { color: #2e7d32; }

    .filters { background: #fff; border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
    .filters form { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .filters input, .filters select {
      padding: 10px 14px; border: 2px solid #e0e3e6; border-radius: 10px; font-size: 0.85rem;
      font-family: 'Inter', sans-serif; outline: none; transition: border-color 0.2s;
    }
    .filters input:focus, .filters select:focus { border-color: #25D366; }
    .filters input[type="text"] { flex: 1; min-width: 200px; }
    .filters select { min-width: 160px; background: #fff; }
    .search-btn {
      padding: 10px 20px; background: linear-gradient(135deg, #25D366, #128C7E); color: #fff;
      border: none; border-radius: 10px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
      font-family: inherit; transition: box-shadow 0.2s;
    }
    .search-btn:hover { box-shadow: 0 4px 12px rgba(37,211,102,0.3); }
    .clear-btn {
      padding: 10px 16px; background: #f5f5f5; color: #666; border: none; border-radius: 10px;
      font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: inherit; text-decoration: none;
      display: inline-flex; align-items: center; gap: 4px;
    }
    .clear-btn:hover { background: #eee; }

    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    thead th { text-align: left; padding: 12px 12px; color: #888; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f2f5; background: #fafafa; white-space: nowrap; }
    tbody td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:hover { background: #f0fff4; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
    .badge-pending { background: #fff3e0; color: #e65100; }
    .badge-welcome { background: #e3f2fd; color: #1565c0; }
    .badge-existing { background: #e8f5e9; color: #2e7d32; }
    .badge-registered { background: #c8e6c9; color: #1b5e20; }

    .phone-cell { font-family: monospace; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .phone-cell .wa-icon { color: #25D366; font-size: 1.1rem; }

    .pagination { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 16px; }
    .pagination a, .pagination span {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 36px; height: 36px; padding: 0 10px; border-radius: 10px;
      font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;
    }
    .pagination a { color: #333; background: #f5f5f5; }
    .pagination a:hover { background: #e8f5e9; color: #2e7d32; }
    .pagination span.current { background: #25D366; color: #fff; font-weight: 700; }

    .empty-state { text-align: center; padding: 40px 20px; color: #999; }
    .empty-state i { font-size: 2rem; display: block; margin-bottom: 8px; color: #25D366; }

    .webhook-info { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-radius: 14px; padding: 20px; margin-bottom: 20px; border: 1px solid #a5d6a7; }
    .webhook-info h4 { font-size: 0.95rem; font-weight: 700; color: #1b5e20; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .webhook-info .info-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 0.85rem; }
    .webhook-info .info-label { font-weight: 600; color: #2e7d32; min-width: 120px; }
    .webhook-info .info-value { font-family: monospace; background: #fff; padding: 6px 12px; border-radius: 6px; word-break: break-all; flex: 1; }
    .webhook-info .copy-btn {
      padding: 4px 10px; background: #2e7d32; color: #fff; border: none; border-radius: 6px;
      font-size: 0.75rem; cursor: pointer; font-family: inherit;
    }
    .webhook-info .copy-btn:hover { background: #1b5e20; }

    @media (max-width: 768px) {
      .filters form { flex-direction: column; }
      .filters input[type="text"], .filters select { min-width: unset; width: 100%; }
      table { font-size: 0.75rem; }
      thead th, tbody td { padding: 8px 8px; }
      .hide-mobile { display: none; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .webhook-info .info-row { flex-direction: column; align-items: flex-start; }
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
      <a href="{{ route('admin.loan_requests') }}"><i class="bi bi-cash-coin"></i> Loan Requests</a>
      <a href="{{ route('admin.not_registered') }}"><i class="bi bi-person-x"></i> Not Registered</a>
      <a href="{{ route('admin.whatsapp') }}" class="active"><i class="bi bi-whatsapp"></i> WhatsApp</a>
      <a href="{{ route('admin.flow_images') }}"><i class="bi bi-images"></i> Flow Images</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <div class="page-header">
      <h2><i class="bi bi-whatsapp"></i> WhatsApp Users</h2>
      <div class="header-right">
        <span class="total-badge">{{ number_format($total) }} users</span>
      </div>
    </div>

    <div class="webhook-info">
      <h4><i class="bi bi-link-45deg"></i> Meta WhatsApp Webhook Configuration</h4>
      <div class="info-row">
        <span class="info-label">Callback URL:</span>
        <span class="info-value" id="callbackUrl">{{ config('app.url') }}/webhook/whatsapp</span>
        <button class="copy-btn" onclick="copyToClipboard('callbackUrl')"><i class="bi bi-clipboard"></i> Copy</button>
      </div>
      <div class="info-row">
        <span class="info-label">Verify Token:</span>
        <span class="info-value" id="verifyToken">{{ config('whatsapp.verify_token') }}</span>
        <button class="copy-btn" onclick="copyToClipboard('verifyToken')"><i class="bi bi-clipboard"></i> Copy</button>
      </div>
    </div>

    @if(!empty($stats))
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-count">{{ number_format($stats['total'] ?? 0) }}</div>
        <div class="stat-label">Total Users</div>
      </div>
      <div class="stat-card orange">
        <div class="stat-count">{{ $stats['pending'] ?? 0 }}</div>
        <div class="stat-label">Pending</div>
      </div>
      <div class="stat-card blue">
        <div class="stat-count">{{ $stats['welcome_sent'] ?? 0 }}</div>
        <div class="stat-label">Welcome Sent</div>
      </div>
      <div class="stat-card green">
        <div class="stat-count">{{ $stats['existing_member'] ?? 0 }}</div>
        <div class="stat-label">Existing Members</div>
      </div>
    </div>
    @endif

    <div class="filters">
      <form method="GET" action="{{ route('admin.whatsapp') }}">
        <input type="text" name="search" placeholder="Search phone or name..." value="{{ $search ?? '' }}">
        <select name="status">
          <option value="">All Status</option>
          <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="welcome_sent" {{ ($status ?? '') === 'welcome_sent' ? 'selected' : '' }}>Welcome Sent</option>
          <option value="existing_member" {{ ($status ?? '') === 'existing_member' ? 'selected' : '' }}>Existing Member</option>
        </select>
        <button type="submit" class="search-btn"><i class="bi bi-search"></i> Search</button>
        @if(($search ?? '') || ($status ?? ''))
        <a href="{{ route('admin.whatsapp') }}" class="clear-btn"><i class="bi bi-x-circle"></i> Clear</a>
        @endif
      </form>
    </div>

    <div class="section">
      @if(count($users) > 0)
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
            if (($u['status'] ?? '') === 'welcome_sent') $statusClass = 'badge-welcome';
            elseif (($u['status'] ?? '') === 'existing_member') $statusClass = 'badge-existing';
            elseif (($u['status'] ?? '') === 'registered') $statusClass = 'badge-registered';

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

      @if($pages > 1)
      <div class="pagination">
        @if($page > 1)
          <a href="{{ route('admin.whatsapp', array_merge(request()->query(), ['page' => $page - 1])) }}">&laquo;</a>
        @endif

        @for($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++)
          @if($p === $page)
            <span class="current">{{ $p }}</span>
          @else
            <a href="{{ route('admin.whatsapp', array_merge(request()->query(), ['page' => $p])) }}">{{ $p }}</a>
          @endif
        @endfor

        @if($page < $pages)
          <a href="{{ route('admin.whatsapp', array_merge(request()->query(), ['page' => $page + 1])) }}">&raquo;</a>
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
  </div>

  <script>
  function copyToClipboard(elementId) {
    const text = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(text).then(() => {
      alert('Copied to clipboard!');
    });
  }
  </script>
</body>
</html>
