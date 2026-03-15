<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Verify Member — Tamil Nadu Vanigargalin Sangamam</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.12); max-width: 460px; width: 100%; overflow: hidden; }
    .card-header { background: linear-gradient(135deg, #009345, #009345); color: #fff; padding: 24px; text-align: center; }
    .card-header h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: 4px; }
    .card-header p { font-size: 0.85rem; opacity: 0.8; }
    .verified-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.2); border-radius: 20px; padding: 6px 14px; margin-top: 10px; font-size: 0.85rem; font-weight: 600; }
    .card-body { padding: 24px; }

    /* 3D Card */
    .card3d-section { margin-bottom: 20px; }
    .card3d-scene {
      width: 100%; aspect-ratio: 1/1.42; perspective: 800px;
      cursor: grab; user-select: none; margin: 0 auto; max-width: 320px;
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

    /* Details */
    .details-list { margin-top: 16px; }
    .detail-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f2f5; font-size: 0.9rem; }
    .detail-item:last-child { border-bottom: none; }
    .detail-label { color: #888; font-weight: 500; }
    .detail-value { font-weight: 600; color: #333; text-align: right; max-width: 60%; word-break: break-word; }
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
    .status-complete { background: #e8f5e9; color: #2e7d32; }
    .status-pending { background: #fff3e0; color: #f57c00; }
    .footer { text-align: center; padding: 16px 24px 24px; font-size: 0.78rem; color: #999; }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-header">
      <h2><i class="bi bi-shield-check"></i> Tamil Nadu Vanigargalin Sangamam</h2>
      <p>Member Verification</p>
      <div class="verified-badge"><i class="bi bi-patch-check-fill"></i> Verified Member</div>
    </div>
    <div class="card-body">
      <!-- 3D Card View -->
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
                  <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:14px;"><span style="font-weight:700;">DATE OF BIRTH</span><span style="font-weight:700;">:</span><span>{{ $member->dob ?? '' }}</span></div>
                  <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:14px;"><span style="font-weight:700;">AGE</span><span style="font-weight:700;">:</span><span>{{ $member->age ?? '' }}</span></div>
                  <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:14px;"><span style="font-weight:700;">BLOOD GROUP</span><span style="font-weight:700;">:</span><span>{{ $member->blood_group ?? '' }}</span></div>
                  <div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:40px;"><span style="font-weight:700;">ADDRESS</span><span style="font-weight:700;">:</span><span style="font-size:0.48rem;word-break:break-word;overflow:hidden;">{{ $member->address ?? '' }}</span></div>
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

      <!-- Member Details -->
      <div class="details-list">
        <div class="detail-item">
          <span class="detail-label">Name</span>
          <span class="detail-value">{{ $member->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Membership</span>
          <span class="detail-value">{{ $member->membership ?? 'Member' }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Member ID</span>
          <span class="detail-value" style="font-family:monospace;">{{ $member->unique_id ?? '' }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Assembly</span>
          <span class="detail-value">{{ $member->assembly ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">District</span>
          <span class="detail-value">{{ $member->district ?? 'N/A' }}</span>
        </div>
        @if(!empty($member->dob))
        <div class="detail-item">
          <span class="detail-label">DOB</span>
          <span class="detail-value">{{ $member->dob }}</span>
        </div>
        @endif
        @if(!empty($member->age))
        <div class="detail-item">
          <span class="detail-label">Age</span>
          <span class="detail-value">{{ $member->age }}</span>
        </div>
        @endif
        @if(!empty($member->blood_group))
        <div class="detail-item">
          <span class="detail-label">Blood Group</span>
          <span class="detail-value">{{ $member->blood_group }}</span>
        </div>
        @endif
        <div class="detail-item">
          <span class="detail-label">Details Status</span>
          <span class="detail-value">
            @if(!empty($member->details_completed) && $member->details_completed)
              <span class="status-badge status-complete"><i class="bi bi-check-circle"></i> Complete</span>
            @else
              <span class="status-badge status-pending"><i class="bi bi-clock"></i> Pending</span>
            @endif
          </span>
        </div>
      </div>
    </div>
    <div class="footer">
      Tamil Nadu Vanigargalin Sangamam &copy; {{ date('Y') }}
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
