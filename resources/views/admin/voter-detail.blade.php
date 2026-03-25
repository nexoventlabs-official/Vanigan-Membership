<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ trim(($voter->FM_NAME_EN ?? '') . ' ' . ($voter->LASTNAME_EN ?? '')) }} — Vanigan Admin</title>
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
    .navbar-nav a:hover { background: rgba(255,255,255,0.18); color: #fff; }
    .navbar-nav form button { background: rgba(255,255,255,0.15); border: none; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: inherit; transition: background 0.2s; }
    .navbar-nav form button:hover { background: rgba(255,255,255,0.25); }

    .container { max-width: 900px; margin: 0 auto; padding: 24px 20px; }
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #1565c0; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }

    .profile-header { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 24px; display: flex; align-items: center; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
    .profile-icon { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #1565c0, #1e88e5); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; flex-shrink: 0; }
    .profile-info h2 { font-size: 1.3rem; font-weight: 700; color: #1a1a1a; margin-bottom: 4px; }
    .profile-info .meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: 0.8rem; color: #888; }
    .profile-info .meta span { display: inline-flex; align-items: center; gap: 4px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.72rem; font-weight: 600; }
    .badge-blue { background: #e3f2fd; color: #1565c0; }
    .badge-pink { background: #fce4ec; color: #c62828; }
    .badge-purple { background: #f3e5f5; color: #7b1fa2; }

    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .two-col { grid-template-columns: 1fr; } }

    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
    .section-header { padding: 16px 20px; border-bottom: 1px solid #f0f2f5; }
    .section-header h3 { font-size: 0.95rem; font-weight: 700; color: #333; display: flex; align-items: center; gap: 8px; }

    .detail-item { display: flex; justify-content: space-between; align-items: flex-start; padding: 10px 20px; border-bottom: 1px solid #f5f5f5; font-size: 0.85rem; gap: 12px; }
    .detail-item:last-child { border-bottom: none; }
    .detail-label { color: #888; font-weight: 500; white-space: nowrap; min-width: 130px; }
    .detail-value { font-weight: 600; color: #333; text-align: right; word-break: break-word; flex: 1; }
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
      <a href="{{ route('admin.not_registered') }}"><i class="bi bi-person-x"></i> Not Registered</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
    </div>
  </nav>

  <div class="container">
    <a href="{{ route('admin.voters') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Voters</a>

    @php
      $firstName = trim($voter->FM_NAME_EN ?? '');
      $lastName = trim(str_replace(['-','_'], '', $voter->LASTNAME_EN ?? ''));
      $fullName = trim($firstName . ($lastName ? ' ' . $lastName : ''));
      $gender = strtoupper(substr($voter->GENDER ?? '', 0, 1));

      // Correct district & zone from zone_data config
      $zoneConfig = config('zone_data');
      $asmMap = $zoneConfig['assembly_map'] ?? [];
      $distZone = $zoneConfig['district_zone'] ?? [];
      $asmUpper = strtoupper(trim(preg_replace('/\s+/', ' ', $voter->ASSEMBLY_NAME ?? '')));
      $matchedAsm = $asmMap[$asmUpper] ?? null;
      if (!$matchedAsm) {
          $norm = preg_replace('/[\. \-\(\)]/', '', $asmUpper);
          foreach ($asmMap as $k => $v) {
              if (preg_replace('/[\. \-\(\)]/', '', $k) === $norm) { $matchedAsm = $v; break; }
          }
      }
      $correctedDistrict = $matchedAsm ? $matchedAsm['d'] : ($voter->DISTRICT_NAME ?? 'N/A');
      $correctedZone = $matchedAsm ? $matchedAsm['z'] : ($distZone[strtoupper($voter->DISTRICT_NAME ?? '')] ?? '');
    @endphp

    <div class="profile-header">
      <div class="profile-icon">{{ strtoupper(substr($fullName ?: '?', 0, 1)) }}</div>
      <div class="profile-info">
        <h2>{{ $fullName ?: 'N/A' }}</h2>
        <div class="meta">
          <span class="badge badge-blue"><i class="bi bi-fingerprint"></i> {{ $voter->EPIC_NO ?? '' }}</span>
          @if($gender === 'M')
          <span class="badge badge-blue">Male</span>
          @elseif($gender === 'F')
          <span class="badge badge-pink">Female</span>
          @else
          <span class="badge badge-purple">{{ $voter->GENDER ?? '' }}</span>
          @endif
          <span><i class="bi bi-geo-alt"></i> {{ $voter->ASSEMBLY_NAME ?? '' }}, {{ $correctedDistrict }}@if($correctedZone) — {{ $correctedZone }}@endif</span>
        </div>
      </div>
    </div>

    <div class="two-col">
      <!-- Personal Details -->
      <div class="section">
        <div class="section-header"><h3><i class="bi bi-person-fill" style="color:#1565c0;"></i> Personal Details</h3></div>
        <div class="detail-item"><span class="detail-label">First Name (EN)</span><span class="detail-value">{{ $voter->FM_NAME_EN ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="detail-label">Last Name (EN)</span><span class="detail-value">{{ $voter->LASTNAME_EN ?? 'N/A' }}</span></div>
        @if(!empty($voter->FM_NAME_V1))
        <div class="detail-item"><span class="detail-label">First Name (Tamil)</span><span class="detail-value">{{ $voter->FM_NAME_V1 }}</span></div>
        @endif
        @if(!empty($voter->LASTNAME_V1))
        <div class="detail-item"><span class="detail-label">Last Name (Tamil)</span><span class="detail-value">{{ $voter->LASTNAME_V1 }}</span></div>
        @endif
        <div class="detail-item"><span class="detail-label">EPIC No</span><span class="detail-value" style="font-family:monospace;">{{ $voter->EPIC_NO ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="detail-label">Gender</span><span class="detail-value">{{ $voter->GENDER ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="detail-label">Age</span><span class="detail-value">{{ $voter->AGE ?? 'N/A' }}</span></div>
        @if(!empty($voter->DOB))
        <div class="detail-item"><span class="detail-label">Date of Birth</span><span class="detail-value">{{ $voter->DOB }}</span></div>
        @endif
        @if(!empty($voter->MOBILE_NO))
        <div class="detail-item"><span class="detail-label">Mobile</span><span class="detail-value">{{ $voter->MOBILE_NO }}</span></div>
        @endif
      </div>

      <!-- Electoral Details -->
      <div class="section">
        <div class="section-header"><h3><i class="bi bi-building" style="color:#ef6c00;"></i> Electoral Details</h3></div>
        <div class="detail-item"><span class="detail-label">Assembly No (AC)</span><span class="detail-value">{{ $voter->AC_NO ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="detail-label">Assembly Name</span><span class="detail-value">{{ $voter->ASSEMBLY_NAME ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="detail-label">District</span><span class="detail-value">{{ $correctedDistrict }}</span></div>
        @if($correctedZone)
        <div class="detail-item"><span class="detail-label">Zone</span><span class="detail-value" style="color:#1565c0;">{{ $correctedZone }}</span></div>
        @endif
        <div class="detail-item"><span class="detail-label">Part No</span><span class="detail-value">{{ $voter->PART_NO ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="detail-label">Section No</span><span class="detail-value">{{ $voter->SECTION_NO ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="detail-label">Serial No in Part</span><span class="detail-value">{{ $voter->SLNOINPART ?? 'N/A' }}</span></div>
        @if(!empty($voter->C_HOUSE_NO))
        <div class="detail-item"><span class="detail-label">House No</span><span class="detail-value">{{ $voter->C_HOUSE_NO }}</span></div>
        @endif
        @if(!empty($voter->C_HOUSE_NO_V1))
        <div class="detail-item"><span class="detail-label">House No (Tamil)</span><span class="detail-value">{{ $voter->C_HOUSE_NO_V1 }}</span></div>
        @endif
      </div>
    </div>

    <!-- Relation Details -->
    @if(!empty($voter->RLN_FM_NM_EN) || !empty($voter->RLN_FM_NM_V1))
    <div class="section">
      <div class="section-header"><h3><i class="bi bi-people-fill" style="color:#7b1fa2;"></i> Relation Details</h3></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
        <div class="detail-item"><span class="detail-label">Relation Type</span><span class="detail-value">{{ $voter->RLN_TYPE ?? 'N/A' }}</span></div>
        @if(!empty($voter->RLN_FM_NM_EN))
        <div class="detail-item"><span class="detail-label">Name (EN)</span><span class="detail-value">{{ trim(($voter->RLN_FM_NM_EN ?? '') . ' ' . str_replace(['-','_'], '', $voter->RLN_L_NM_EN ?? '')) }}</span></div>
        @endif
        @if(!empty($voter->RLN_FM_NM_V1))
        <div class="detail-item"><span class="detail-label">Name (Tamil)</span><span class="detail-value">{{ trim(($voter->RLN_FM_NM_V1 ?? '') . ' ' . ($voter->RLN_L_NM_V1 ?? '')) }}</span></div>
        @endif
      </div>
    </div>
    @endif
  </div>
</body>
</html>
