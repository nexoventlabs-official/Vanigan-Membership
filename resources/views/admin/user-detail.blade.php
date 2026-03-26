<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $member->name ?? 'Member' }} — Vanigan Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f0f2f5; color: #333; min-height: 100vh; }

    /* Navbar */
    .navbar { background: linear-gradient(135deg, #007a38, #00a84e); color: #fff; padding: 0 24px; display: flex; align-items: center; height: 60px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 100; }
    .navbar-brand { font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .navbar-nav { display: flex; align-items: center; gap: 4px; margin-left: auto; }
    .navbar-nav a { color: rgba(255,255,255,0.85); text-decoration: none; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; transition: background 0.2s; }
    .navbar-nav a:hover { background: rgba(255,255,255,0.18); color: #fff; }
    .navbar-nav form button { background: rgba(255,255,255,0.15); border: none; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: inherit; transition: background 0.2s; }
    .navbar-nav form button:hover { background: rgba(255,255,255,0.25); }

    .container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

    /* Back link */
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #2e7d32; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }

    /* Profile Header */
    .profile-header { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 24px; display: flex; align-items: center; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
    .profile-photo { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #e8f5e9; }
    .profile-photo-placeholder { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #007a38, #00a84e); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; }
    .profile-info h2 { font-size: 1.3rem; font-weight: 700; color: #1a1a1a; margin-bottom: 4px; }
    .profile-info .meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: 0.8rem; color: #888; }
    .profile-info .meta span { display: inline-flex; align-items: center; gap: 4px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.72rem; font-weight: 600; }
    .badge-green { background: #e8f5e9; color: #2e7d32; }
    .badge-orange { background: #fff3e0; color: #f57c00; }
    .badge-blue { background: #e3f2fd; color: #1565c0; }

    /* Two-column layout */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

    /* Section */
    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
    .section-header { padding: 16px 20px; border-bottom: 1px solid #f0f2f5; }
    .section-header h3 { font-size: 0.95rem; font-weight: 700; color: #333; display: flex; align-items: center; gap: 8px; }
    .section-body { padding: 16px 20px; }

    /* Details list */
    .detail-item { display: flex; justify-content: space-between; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f5f5f5; font-size: 0.85rem; gap: 12px; }
    .detail-item:last-child { border-bottom: none; }
    .detail-label { color: #888; font-weight: 500; white-space: nowrap; min-width: 120px; }
    .detail-value { font-weight: 600; color: #333; text-align: right; word-break: break-word; flex: 1; }

    /* 3D Card */
    .card3d-section { margin-bottom: 20px; }
    .card3d-scene {
      width: 100%; aspect-ratio: 1/1.42; perspective: 800px;
      cursor: grab; user-select: none; margin: 0 auto; max-width: 300px;
    }
    .card3d-scene:active { cursor: grabbing; }
    .card3d-inner {
      position: relative; width: 100%; height: 100%;
      transform-style: preserve-3d;
      transition: transform 0.6s ease;
    }
    .card3d-inner.dragging { transition: none; }
    .card3d-face {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      backface-visibility: hidden; border-radius: 12px;
    }
    .card3d-face img.card-bg {
      width: 100%; height: 100%; object-fit: contain;
      border-radius: 12px; margin: 0;
    }
    .card3d-back { transform: rotateY(180deg); }
    .card3d-controls {
      display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 8px;
    }
    .card3d-btn {
      border: none; background: none; cursor: pointer;
      font-size: 1.2rem; color: #2e7d32; padding: 4px 8px;
      border-radius: 50%; transition: background 0.2s;
    }
    .card3d-btn:hover { background: rgba(46,125,50,0.1); }
    .card3d-hint { font-size: 0.75rem; color: #999; display: flex; align-items: center; gap: 4px; }

    /* Referral section */
    .referral-box { background: #f8fdf8; border: 1px solid #c8e6c9; border-radius: 12px; padding: 16px; margin-bottom: 12px; }
    .referral-stat { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .referral-stat .num { font-size: 1.5rem; font-weight: 800; color: #2e7d32; }
    .referral-stat .label { font-size: 0.8rem; color: #666; }
    .referral-id { font-family: monospace; background: #fff; padding: 6px 12px; border-radius: 8px; border: 1px solid #e0e3e6; font-size: 0.85rem; display: inline-block; }

    /* Referred members table */
    table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    thead th { text-align: left; padding: 10px 12px; color: #888; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f2f5; }
    tbody td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:hover { background: #fafafa; }
    table a { color: #2e7d32; text-decoration: none; font-weight: 600; }
    table a:hover { text-decoration: underline; }

    .member-avatar-sm { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 2px solid #e8f5e9; }
    .member-avatar-sm-placeholder { width: 30px; height: 30px; border-radius: 50%; background: #e8f5e9; color: #2e7d32; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; }

    .empty-state { text-align: center; padding: 24px; color: #999; font-size: 0.85rem; }

    /* Action buttons */
    .action-bar { display: flex; gap: 10px; margin-left: auto; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; border: none; transition: all 0.2s; text-decoration: none; }
    .btn-edit { background: #e3f2fd; color: #1565c0; }
    .btn-edit:hover { background: #bbdefb; box-shadow: 0 2px 8px rgba(21,101,192,0.2); }
    .btn-delete { background: #ffebee; color: #c62828; }
    .btn-delete:hover { background: #ffcdd2; box-shadow: 0 2px 8px rgba(198,40,40,0.2); }

    /* Flash messages */
    .flash-msg { padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 500; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .flash-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .flash-error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }

    /* Modal */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal { background: #fff; border-radius: 16px; width: 95%; max-width: 600px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: #fff; border-radius: 16px 16px 0 0; z-index: 1; }
    .modal-header h3 { font-size: 1.05rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .modal-close { border: none; background: #f5f5f5; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; color: #666; transition: background 0.2s; }
    .modal-close:hover { background: #eee; }
    .modal-body { padding: 20px 24px; }
    .modal-footer { padding: 16px 24px; border-top: 1px solid #f0f2f5; display: flex; justify-content: flex-end; gap: 10px; position: sticky; bottom: 0; background: #fff; border-radius: 0 0 16px 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 0.8rem; font-weight: 600; color: #666; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 2px solid #e0e3e6; border-radius: 10px; font-size: 0.9rem; font-family: 'Inter', sans-serif; outline: none; transition: border-color 0.2s; color: #333; background: #fafafa; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #2e7d32; background: #fff; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
    .btn-save { background: linear-gradient(135deg, #007a38, #00a84e); color: #fff; }
    .btn-save:hover { box-shadow: 0 4px 12px rgba(0,122,56,0.3); }
    .btn-cancel { background: #f5f5f5; color: #666; }
    .btn-cancel:hover { background: #eee; }
    .btn-delete-confirm { background: #c62828; color: #fff; }
    .btn-delete-confirm:hover { background: #b71c1c; box-shadow: 0 4px 12px rgba(198,40,40,0.3); }

    /* Delete confirmation modal */
    .delete-warning { background: #fff3e0; border: 1px solid #ffe082; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
    .delete-warning p { font-size: 0.88rem; color: #e65100; line-height: 1.6; }
    .delete-info { background: #f5f5f5; border-radius: 10px; padding: 12px 16px; font-size: 0.85rem; color: #555; }
    .delete-info strong { color: #333; }
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
      <a href="{{ route('admin.reports') }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
      <a href="{{ route('admin.loan_requests') }}"><i class="bi bi-cash-coin"></i> Loan Requests</a>
      <a href="{{ route('admin.not_registered') }}"><i class="bi bi-person-x"></i> Not Registered</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <a href="{{ route('admin.users') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Members</a>

    @if(session('success'))
    <div class="flash-msg flash-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash-msg flash-error"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
    @endif

    <!-- Profile Header -->
    <div class="profile-header">
      @if(!empty($member->photo_url))
      <img src="{{ $member->photo_url }}" class="profile-photo" alt="">
      @else
      <div class="profile-photo-placeholder">{{ strtoupper(substr($member->name ?? '?', 0, 1)) }}</div>
      @endif
      <div class="profile-info">
        <h2>{{ $member->name ?? 'N/A' }}</h2>
        <div class="meta">
          <span class="badge badge-blue">{{ $member->membership ?? 'Member' }}</span>
          <span><i class="bi bi-fingerprint"></i> {{ $member->unique_id ?? '' }}</span>
          <span><i class="bi bi-geo-alt"></i> {{ $member->assembly ?? '' }}, {{ $member->district ?? '' }}</span>
          @if(!empty($member->details_completed))
          <span class="badge badge-green"><i class="bi bi-check-circle"></i> Complete</span>
          @else
          <span class="badge badge-orange"><i class="bi bi-clock"></i> Pending</span>
          @endif
        </div>
      </div>
      <div class="action-bar">
        <button class="btn btn-edit" onclick="openEditModal()"><i class="bi bi-pencil-square"></i> Edit</button>
        <button class="btn btn-delete" onclick="openDeleteModal()"><i class="bi bi-trash3"></i> Delete</button>
      </div>
    </div>

    <div class="two-col">
      <!-- Left: Member Details -->
      <div>
        <div class="section">
          <div class="section-header"><h3><i class="bi bi-person-lines-fill" style="color:#2e7d32;"></i> Member Details</h3></div>
          <div class="section-body">
            <div class="detail-item"><span class="detail-label">Full Name</span><span class="detail-value">{{ $member->name ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">Member ID</span><span class="detail-value" style="font-family:monospace;">{{ $member->unique_id ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">EPIC No</span><span class="detail-value" style="font-family:monospace;">{{ $member->epic_no ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">Mobile</span><span class="detail-value">{{ $member->contact_number ?? $member->mobile ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">Assembly</span><span class="detail-value">{{ $member->assembly ?? 'N/A' }}@if(!empty($member->assembly)) <span style="display:inline-block;font-size:0.65rem;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.4px;">Assm</span>@endif</span></div>
            <div class="detail-item"><span class="detail-label">District</span><span class="detail-value">{{ $member->district ?? 'N/A' }}@if(!empty($member->district)) <span style="display:inline-block;font-size:0.65rem;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.4px;">Dist</span>@endif</span></div>
            @if(!empty($member->zone))
            <div class="detail-item"><span class="detail-label">Zone</span><span class="detail-value">{{ $member->zone }}</span></div>
            @endif
            @if(!empty($member->dob))
            <div class="detail-item"><span class="detail-label">Date of Birth</span><span class="detail-value">{{ $member->dob }}</span></div>
            @endif
            @if(!empty($member->age))
            <div class="detail-item"><span class="detail-label">Age</span><span class="detail-value">{{ $member->age }}</span></div>
            @endif
            @if(!empty($member->blood_group))
            <div class="detail-item"><span class="detail-label">Blood Group</span><span class="detail-value">{{ $member->blood_group }}</span></div>
            @endif
            @if(!empty($member->address))
            <div class="detail-item"><span class="detail-label">Address</span><span class="detail-value">{{ $member->address }}</span></div>
            @endif
            @if(!empty($member->created_at))
            <div class="detail-item"><span class="detail-label">Registered</span><span class="detail-value">{{ $member->created_at }}</span></div>
            @endif
            @if(!empty($member->updated_at))
            <div class="detail-item"><span class="detail-label">Last Updated</span><span class="detail-value">{{ $member->updated_at }}</span></div>
            @endif
          </div>
        </div>

        <!-- Loan Request Section -->
        @if($loan_request)
        <div class="section">
          <div class="section-header"><h3><i class="bi bi-cash-coin" style="color:#ef6c00;"></i> Loan Application</h3></div>
          <div class="section-body">
            <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:12px;padding:16px;">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <span class="badge badge-orange" style="font-size:0.8rem;padding:6px 12px;">Applied for 25L Loan</span>
                <span class="badge badge-green" style="font-size:0.75rem;">{{ ucfirst($loan_request->status ?? 'pending') }}</span>
              </div>
              <div class="detail-item"><span class="detail-label">Business Type</span><span class="detail-value">{{ $loan_request->business_type ?? 'N/A' }}</span></div>
              <div class="detail-item"><span class="detail-label">Business Name</span><span class="detail-value">{{ $loan_request->business_name ?? 'N/A' }}</span></div>
              @if(!empty($loan_request->created_at))
              <div class="detail-item"><span class="detail-label">Applied On</span><span class="detail-value">{{ $loan_request->created_at }}</span></div>
              @endif
            </div>
          </div>
        </div>
        @endif

        <!-- Referral Section -->
        <div class="section">
          <div class="section-header"><h3><i class="bi bi-share-fill" style="color:#ef6c00;"></i> Referral Info</h3></div>
          <div class="section-body">
            <div class="referral-box">
              <div class="referral-stat">
                <div class="num">{{ $member->referral_count ?? 0 }}</div>
                <div class="label">Members Referred</div>
              </div>
              @if(!empty($member->referral_id))
              <div style="margin-bottom:6px;font-size:0.8rem;color:#666;">Referral ID</div>
              <div class="referral-id">{{ $member->referral_id }}</div>
              @endif
              @if(!empty($member->referred_by))
              <div style="margin-top:12px;font-size:0.8rem;color:#666;margin-bottom:8px;">Referred by</div>
              @if(!empty($referred_by_member))
              <a href="{{ route('admin.user.detail', $member->referred_by) }}" style="text-decoration:none;display:flex;align-items:center;gap:12px;padding:10px 12px;background:#fff;border:1px solid #e0e3e6;border-radius:12px;">
                @if(!empty($referred_by_member->photo_url))
                <img src="{{ $referred_by_member->photo_url }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #e8f5e9;flex-shrink:0;">
                @else
                <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#007a38,#00a84e);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($referred_by_member->name ?? '?', 0, 1)) }}</div>
                @endif
                <div style="flex:1;min-width:0;">
                  <div style="font-weight:700;color:#1a1a1a;font-size:0.88rem;">{{ $referred_by_member->name ?? 'N/A' }}</div>
                  <div style="font-size:0.75rem;color:#888;font-family:monospace;">{{ $referred_by_member->unique_id ?? '' }}</div>
                  <div style="font-size:0.72rem;color:#666;margin-top:2px;">{{ $referred_by_member->assembly ?? '' }} &bull; {{ $referred_by_member->district ?? '' }}</div>
                </div>
                <i class="bi bi-chevron-right" style="color:#ccc;"></i>
              </a>
              @else
              <div style="margin-top:4px;"><a href="{{ route('admin.user.detail', $member->referred_by) }}" style="color:#2e7d32;font-weight:600;text-decoration:none;">{{ $member->referred_by }}</a></div>
              @endif
              @endif
            </div>

            @if(count($referred_members) > 0)
            <h4 style="font-size:0.85rem;font-weight:600;margin:16px 0 10px;color:#555;">Members Referred ({{ count($referred_members) }})</h4>
            <table>
              <thead><tr><th></th><th>Name</th><th>Assembly</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($referred_members as $rm)
                <tr>
                  <td>
                    @if(!empty($rm['photo_url']))
                    <img src="{{ $rm['photo_url'] }}" class="member-avatar-sm" alt="">
                    @else
                    <div class="member-avatar-sm-placeholder">{{ strtoupper(substr($rm['name'] ?? '?', 0, 1)) }}</div>
                    @endif
                  </td>
                  <td><a href="{{ route('admin.user.detail', $rm['unique_id'] ?? '') }}">{{ $rm['name'] ?? 'N/A' }}</a><br><span style="font-size:0.7rem;color:#999;">{{ $rm['unique_id'] ?? '' }}</span></td>
                  <td style="font-size:0.8rem;">{{ $rm['assembly'] ?? '' }}</td>
                  <td>
                    @if(!empty($rm['details_completed']))
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
            <div class="empty-state" style="padding:16px;">No referred members yet</div>
            @endif
          </div>
        </div>
      </div>

      <!-- Right: 3D Card -->
      <div>
        <div class="section">
          <div class="section-header"><h3><i class="bi bi-credit-card-2-front-fill" style="color:#1565c0;"></i> ID Card Preview</h3></div>
          <div class="section-body">
            <div class="card3d-section">
              <div class="card3d-scene" id="card3dScene">
                <div class="card3d-inner" id="card3dInner">
                  <!-- Front Face -->
                  <div class="card3d-face">
                    <div style="position:relative;width:100%;height:100%;background:url('https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232516/vanigan/templates/ID_Front.png') center/contain no-repeat;border-radius:12px;">
                      @if(!empty($member->photo_url))
                      <img src="{{ $member->photo_url }}" style="position:absolute;top:31.8%;left:50%;transform:translateX(-50%);width:32.5%;border-radius:16px;border:3px solid #009245;aspect-ratio:1;object-fit:cover;">
                      @endif
                      <div style="position:absolute;top:57%;left:0;right:0;text-align:center;padding:0 12px;">
                        <p style="font-size:0.9rem;font-weight:700;color:#009245;margin:0;line-height:1.1;">{{ $member->name ?? '' }}</p>
                        <p style="font-size:0.65rem;margin:2px 0 0;">{{ $member->assembly ?? '' }}@if(!empty($member->assembly)) <span style="display:inline-block;font-size:0.38rem;font-weight:700;color:#fff;background:#009245;border-radius:3px;padding:1px 4px;margin-left:2px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.3px;line-height:1.4;">Assm</span>@endif</p>
                        <p style="font-size:0.65rem;margin:1px 0 0;">{{ $member->district ?? '' }}@if(!empty($member->district)) <span style="display:inline-block;font-size:0.38rem;font-weight:700;color:#fff;background:#009245;border-radius:3px;padding:1px 4px;margin-left:2px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.3px;line-height:1.4;">Dist</span>@endif</p>
                        <p style="font-size:0.65rem;margin:1px 0 0;">{{ $member->zone ?? '' }}</p>
                        <p style="font-size:0.6rem;margin:3px 0 0;letter-spacing:0.3px;">{{ $member->unique_id ?? '' }}</p>
                      </div>
                    </div>
                  </div>
                  <!-- Back Face -->
                  <div class="card3d-face card3d-back">
                    <div style="position:relative;width:100%;height:100%;background:url('https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png') center/contain no-repeat;border-radius:12px;">
                      <div style="position:absolute;top:28%;left:6%;right:6%;font-size:0.55rem;line-height:1.3;display:flex;flex-direction:column;gap:3px;overflow:hidden;">
                        <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:14px;"><span style="font-weight:700;">DATE OF BIRTH</span><span style="font-weight:700;">:</span><span>{{ !empty($member->dob) ? $member->dob : 'xxxxxx' }}</span></div>
                        <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:14px;"><span style="font-weight:700;">AGE</span><span style="font-weight:700;">:</span><span>{{ !empty($member->age) ? $member->age : 'xxxxxx' }}</span></div>
                        <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:14px;"><span style="font-weight:700;">BLOOD GROUP</span><span style="font-weight:700;">:</span><span>{{ !empty($member->blood_group) ? $member->blood_group : 'xxxxxx' }}</span></div>
                        <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:40px;"><span style="font-weight:700;">ADDRESS</span><span style="font-weight:700;">:</span><span style="font-size:0.48rem;word-break:break-word;overflow:hidden;">{{ !empty($member->address) ? $member->address : 'xxxxxx' }}</span></div>
                        <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:14px;"><span style="font-weight:700;">CONTACT</span><span style="font-weight:700;">:</span><span>{{ $member->contact_number ?? '' }}</span></div>
                      </div>
                      <div style="position:absolute;bottom:18%;left:5%;right:5%;display:flex;align-items:flex-end;justify-content:space-between;">
                        <div><img src="/api/vanigam/qr/{{ $member->unique_id ?? '' }}" style="width:50px;height:46px;border-radius:4px;"></div>
                        <div style="text-align:center;font-size:0.4rem;line-height:1.3;">
                          <img src="/signature.png" style="width:45px;height:auto;margin-bottom:1px;">
                          <p style="margin:0;font-weight:700;font-size:0.44rem;">SENTHIL KUMAR N</p>
                          <p style="margin:0;">Founder & State President</p>
                          <p style="margin:0;">Tamilnadu Vanigargalin Sangamam</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card3d-controls">
                <button class="card3d-btn" onclick="rotate3d(-1)" title="Rotate Left"><i class="bi bi-arrow-counterclockwise"></i></button>
                <span class="card3d-hint"><i class="bi bi-hand-index-thumb"></i> Drag to rotate</span>
                <button class="card3d-btn" onclick="rotate3d(1)" title="Rotate Right"><i class="bi bi-arrow-clockwise"></i></button>
              </div>
            </div>

            @if(empty($member->card_front_url) || empty($member->card_back_url))
            @if(!empty($member->photo_url))
            <div id="regenBanner" style="margin-top:12px;padding:12px 16px;background:#fff3e0;border:1px solid #ffe082;border-radius:10px;display:flex;align-items:center;gap:8px;font-size:0.82rem;color:#e65100;">
              <div style="width:18px;height:18px;border:3px solid #e65100;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;flex-shrink:0;"></div>
              Regenerating card images... Please wait.
              <style>@keyframes spin{to{transform:rotate(360deg);}}</style>
            </div>
            @endif
            @endif

            @if(!empty($member->card_front_url) && !empty($member->card_back_url))
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f0f2f5;">
              <h4 style="font-size:0.85rem;font-weight:600;color:#555;margin-bottom:10px;">Generated Card Images</h4>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                  <p style="font-size:0.72rem;color:#999;margin-bottom:4px;">Front</p>
                  <img src="{{ $member->card_front_url }}" style="width:100%;border-radius:8px;border:1px solid #eee;">
                </div>
                <div>
                  <p style="font-size:0.72rem;color:#999;margin-bottom:4px;">Back</p>
                  <img src="{{ $member->card_back_url }}" style="width:100%;border-radius:8px;border:1px solid #eee;">
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal-overlay" id="editModal">
    <div class="modal">
      <div class="modal-header">
        <h3><i class="bi bi-pencil-square" style="color:#1565c0;"></i> Edit Member Details</h3>
        <button class="modal-close" onclick="closeEditModal()">&times;</button>
      </div>
      <form action="{{ route('admin.user.update', $member->unique_id ?? '') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="{{ $member->name ?? '' }}" maxlength="100" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>EPIC No</label>
              <input type="text" name="epic_no" value="{{ $member->epic_no ?? '' }}" maxlength="20">
            </div>
            <div class="form-group">
              <label>Member ID</label>
              <input type="text" value="{{ $member->unique_id ?? '' }}" disabled style="background:#f0f0f0;color:#999;">
            </div>
          </div>
          <div class="form-group">
            <label>Assembly</label>
            <input type="text" name="assembly" value="{{ $member->assembly ?? '' }}" maxlength="100">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>District</label>
              <input type="text" name="district" value="{{ $member->district ?? '' }}" maxlength="100">
            </div>
            <div class="form-group">
              <label>Zone</label>
              <input type="text" name="zone" value="{{ $member->zone ?? '' }}" maxlength="100">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Date of Birth</label>
              <input type="text" name="dob" value="{{ $member->dob ?? '' }}" placeholder="DD/MM/YYYY" maxlength="20">
            </div>
            <div class="form-group">
              <label>Blood Group</label>
              <select name="blood_group">
                <option value="">Select</option>
                @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                <option value="{{ $bg }}" {{ ($member->blood_group ?? '') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3" maxlength="300">{{ $member->address ?? '' }}</textarea>
          </div>
          <div style="background:#e3f2fd;border-radius:10px;padding:12px 16px;font-size:0.82rem;color:#1565c0;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-info-circle-fill"></i> Saving will delete existing card images and they will be regenerated with the updated details.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Cancel</button>
          <button type="submit" class="btn btn-save"><i class="bi bi-check-lg"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal-overlay" id="deleteModal">
    <div class="modal" style="max-width:480px;">
      <div class="modal-header">
        <h3><i class="bi bi-exclamation-triangle-fill" style="color:#c62828;"></i> Delete Member</h3>
        <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="delete-warning">
          <p><strong>⚠️ This action cannot be undone.</strong><br>The following data will be permanently deleted:</p>
        </div>
        <div class="delete-info">
          <div style="display:flex;flex-direction:column;gap:6px;">
            <div><i class="bi bi-person-x" style="color:#c62828;"></i> <strong>{{ $member->name ?? 'N/A' }}</strong> ({{ $member->unique_id ?? '' }})</div>
            <div><i class="bi bi-database-x" style="color:#c62828;"></i> MongoDB: member record, manual entry (if any)</div>
            <div><i class="bi bi-cloud-minus" style="color:#c62828;"></i> Cloudinary: uploaded photo, generated ID card images, card folder</div>
            @if($loan_request)
            <div><i class="bi bi-cash-coin" style="color:#c62828;"></i> Loan request application</div>
            @endif
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-cancel" onclick="closeDeleteModal()">Cancel</button>
        <form action="{{ route('admin.user.delete', $member->unique_id ?? '') }}" method="POST" style="margin:0;">
          @csrf
          <button type="submit" class="btn btn-delete-confirm"><i class="bi bi-trash3"></i> Yes, Delete Permanently</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    function openEditModal() { document.getElementById('editModal').classList.add('active'); }
    function closeEditModal() { document.getElementById('editModal').classList.remove('active'); }
    function openDeleteModal() { document.getElementById('deleteModal').classList.add('active'); }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.remove('active'); }
    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(el => {
      el.addEventListener('click', e => { if (e.target === el) el.classList.remove('active'); });
    });

    // 3D Card rotation
    let angle = 0;
    function rotate3d(dir) {
      angle += dir * 180;
      document.getElementById('card3dInner').style.transform = 'rotateY(' + angle + 'deg)';
    }

    // Drag to rotate
    (function() {
      const scene = document.getElementById('card3dScene');
      const inner = document.getElementById('card3dInner');
      if (!scene || !inner) return;
      let dragging = false, startX = 0, startAngle = 0;

      scene.addEventListener('mousedown', e => { dragging = true; startX = e.clientX; startAngle = angle; inner.classList.add('dragging'); });
      scene.addEventListener('touchstart', e => { dragging = true; startX = e.touches[0].clientX; startAngle = angle; inner.classList.add('dragging'); }, { passive: true });

      document.addEventListener('mousemove', e => {
        if (!dragging) return;
        const dx = e.clientX - startX;
        angle = startAngle + dx * 0.5;
        inner.style.transform = 'rotateY(' + angle + 'deg)';
      });
      document.addEventListener('touchmove', e => {
        if (!dragging) return;
        const dx = e.touches[0].clientX - startX;
        angle = startAngle + dx * 0.5;
        inner.style.transform = 'rotateY(' + angle + 'deg)';
      }, { passive: true });

      function endDrag() {
        if (!dragging) return;
        dragging = false;
        inner.classList.remove('dragging');
        // Snap to nearest 180 degrees
        const snap = Math.round(angle / 180) * 180;
        angle = snap;
        inner.style.transform = 'rotateY(' + angle + 'deg)';
      }
      document.addEventListener('mouseup', endDrag);
      document.addEventListener('touchend', endDrag);
    })();
  </script>

  <!-- Hidden card capture elements for regeneration -->
  <div id="adminCardCapture" style="position:fixed;left:-9999px;top:0;z-index:-1;background:#fff;">
    <div id="adminCapFront" style="width:421px;position:relative;overflow:hidden;font-family:Arial,sans-serif;">
      <img src="https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232516/vanigan/templates/ID_Front.png" style="display:block;width:421px;" crossorigin="anonymous" />
      <div style="position:absolute;top:182px;left:50%;transform:translateX(-50%);width:137px;">
        @if(!empty($member->photo_url))
        <img src="{{ $member->photo_url }}" crossorigin="anonymous" style="border:5px solid #009245;border-radius:22px;width:137px;height:136px;object-fit:cover;" />
        @endif
      </div>
      <div style="position:absolute;top:328px;left:28px;right:28px;text-align:center;">
        <p style="font-size:23px;font-weight:700;color:#009245;line-height:1.08;margin:0;">{{ $member->name ?? '' }}</p>
        <div style="display:flex;flex-direction:column;align-items:center;gap:8px;margin-top:6px;">
          @if(!empty($member->assembly))
          <div style="text-align:center;padding:0 18px;"><p style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;">{{ $member->assembly }} <span style="display:inline-block;font-size:10px;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 5px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.5px;line-height:1.4;">Assm</span></p></div>
          @endif
          @if(!empty($member->district))
          <div style="text-align:center;padding:0 18px;"><p style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;">{{ $member->district }} <span style="display:inline-block;font-size:10px;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 5px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.5px;line-height:1.4;">Dist</span></p></div>
          @endif
          @if(!empty($member->zone))
          <div style="text-align:center;padding:0 18px;"><p style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;">{{ $member->zone }}</p></div>
          @endif
          <div style="text-align:center;padding:0 18px;"><p style="font-size:18px;font-weight:700;letter-spacing:0.2px;margin:0;color:#111;">{{ $member->unique_id ?? '' }}</p></div>
        </div>
      </div>
    </div>
    <div id="adminCapBack" style="width:421px;position:relative;overflow:hidden;font-family:Arial,sans-serif;margin-top:20px;">
      <img src="https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png" style="display:block;width:421px;" crossorigin="anonymous" />
      <div style="position:absolute;top:234px;left:22px;right:20px;color:#111;">
        <div style="transform:translateY(-60px);">
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">DATE OF BIRTH</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div style="font-size:17px;font-weight:700;line-height:1.12;">{{ $member->dob ?? 'xxxxxx' }}</div>
          </div>
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">AGE</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div style="font-size:17px;font-weight:700;line-height:1.12;">{{ $member->age ?? 'xxxxxx' }}</div>
          </div>
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">BLOOD GROUP</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div style="font-size:17px;font-weight:700;line-height:1.12;">{{ $member->blood_group ?? 'xxxxxx' }}</div>
          </div>
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:76px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">ADDRESS</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div style="font-size:17px;font-weight:700;line-height:1.12;word-break:break-word;">{{ $member->address ?? 'xxxxxx' }}</div>
          </div>
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">CONTACT</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div style="font-size:17px;font-weight:700;line-height:1.12;"><span style="background:rgba(255,255,255,0.78);display:inline-block;padding:0 4px;">{{ !empty($member->contact_number) ? '+91 ' . $member->contact_number : (!empty($member->mobile) ? '+91 ' . $member->mobile : '') }}</span></div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:40% 60%;align-items:start;margin-top:10px;">
          <div style="padding-left:20px;"><img src="/api/vanigam/qr/{{ $member->unique_id ?? '' }}" width="96" height="88" crossorigin="anonymous" /></div>
          <div style="text-align:center;padding-right:10px;">
            <img src="/signature.png" style="width:80px;height:auto;margin-bottom:2px;" crossorigin="anonymous" />
            <p style="text-align:center;margin:2px 0 0;font-size:14px;font-weight:700;">SENTHIL KUMAR N</p>
            <p style="font-size:12px;font-weight:bold;line-height:1.1;margin:0;">Founder &amp; State President</p>
            <p style="font-size:12px;font-weight:bold;line-height:1.1;margin:0;">Tamilnadu Vanigargalin Sangamam</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Auto-regenerate card images if card URLs are empty (after admin edit)
    (function() {
      var needsRegen = {{ (empty($member->card_front_url) || empty($member->card_back_url)) && !empty($member->photo_url) ? 'true' : 'false' }};
      if (!needsRegen) return;

      // Wait for images to load
      window.addEventListener('load', function() {
        setTimeout(function() { captureAndUploadAdminCard(); }, 1500);
      });

      async function captureAndUploadAdminCard() {
        try {
          var frontEl = document.getElementById('adminCapFront');
          var backEl = document.getElementById('adminCapBack');
          if (!frontEl || !backEl) return;

          // Move into view temporarily for html2canvas
          var wrap = document.getElementById('adminCardCapture');
          wrap.style.left = '0';
          wrap.style.opacity = '0.01';

          var frontCanvas = await html2canvas(frontEl, { useCORS: true, allowTaint: false, scale: 2, backgroundColor: '#ffffff' });
          var backCanvas = await html2canvas(backEl, { useCORS: true, allowTaint: false, scale: 2, backgroundColor: '#ffffff' });

          wrap.style.left = '-9999px';

          var frontData = frontCanvas.toDataURL('image/png');
          var backData = backCanvas.toDataURL('image/png');

          var res = await fetch('{{ route("admin.user.regenerate_card", $member->unique_id ?? "") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ front_image: frontData, back_image: backData })
          });
          var data = await res.json();
          if (data.success) {
            console.log('Card regenerated:', data);
            // Reload to show updated card images
            window.location.reload();
          } else {
            console.error('Card regen failed:', data.message);
          }
        } catch (e) {
          console.error('Card capture error:', e);
        }
      }
    })();
  </script>
</body>
</html>
