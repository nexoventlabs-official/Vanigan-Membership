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
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <a href="{{ route('admin.users') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Members</a>

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
            <div class="detail-item"><span class="detail-label">Assembly</span><span class="detail-value">{{ $member->assembly ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">District</span><span class="detail-value">{{ $member->district ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">Membership</span><span class="detail-value">{{ $member->membership ?? 'Member' }}</span></div>
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
                        <p style="font-size:0.7rem;font-weight:600;margin:3px 0 0;">{{ $member->membership ?? 'Member' }}</p>
                        <p style="font-size:0.65rem;margin:2px 0 0;">{{ $member->assembly ?? '' }}</p>
                        <p style="font-size:0.65rem;margin:1px 0 0;">{{ $member->district ?? '' }}</p>
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

  <script>
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
</body>
</html>
