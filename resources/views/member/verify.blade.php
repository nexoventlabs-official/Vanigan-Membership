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

    /* PIN Input */
    .pin-box {
      width: 52px; height: 58px; text-align: center; font-size: 1.5rem; font-weight: 700;
      border: 2px solid #dfe1e5; border-radius: 12px; outline: none; font-family: monospace;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .pin-box:focus { border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46,125,50,0.15); }
    .pin-submit-btn {
      width: 100%; padding: 14px; background: linear-gradient(135deg, #009345, #009345); color: #fff;
      border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer;
      font-family: inherit; transition: background 0.2s, transform 0.1s;
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    }
    .pin-submit-btn:hover { background: linear-gradient(135deg, #007a38, #007a38); }
    .pin-submit-btn:active { transform: scale(0.98); }
    .pin-submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }
  </style>
</head>
<body>
  @php $detailsCompleted = !empty($member->details_completed) && $member->details_completed; @endphp

  <!-- PIN Entry Section (hidden if details completed) -->
  <div class="card" id="pinSection" style="{{ $detailsCompleted ? 'display:none;' : '' }}">
    <div class="card-header">
      <h2><i class="bi bi-shield-lock"></i> <span class="t" data-en="Tamil Nadu Vanigargalin Sangamam" data-ta="தமிழ்நாடு வணிகர்களின் சங்கமம்">Tamil Nadu Vanigargalin Sangamam</span></h2>
      <p><span class="t" data-en="Member Verification" data-ta="உறுப்பினர் சரிபார்ப்பு">Member Verification</span></p>
      <div class="verified-badge"><i class="bi bi-lock-fill"></i> <span class="t" data-en="Enter Secret PIN" data-ta="ரகசிய PIN ஐ உள்ளிடவும்">Enter Secret PIN</span></div>
    </div>
    <div class="card-body" style="text-align:center;">
      <div style="margin-bottom:20px;">
        <i class="bi bi-person-badge" style="font-size:3rem;color:#2e7d32;"></i>
        <h3 style="font-size:1.1rem;font-weight:700;color:#333;margin:10px 0 4px;">{{ $member->name ?? '' }}</h3>
        <p style="font-size:0.82rem;color:#888;font-family:monospace;">{{ $member->unique_id ?? '' }}</p>
      </div>
      <p style="font-size:0.9rem;color:#555;margin-bottom:16px;"><span class="t" data-en="Enter your 4-digit secret PIN to view the ID card" data-ta="அடையாள அட்டையைக் காண உங்கள் 4 இலக்க ரகசிய PIN ஐ உள்ளிடவும்">Enter your 4-digit secret PIN to view the ID card</span></p>
      <div style="display:flex;justify-content:center;gap:10px;margin-bottom:16px;" id="pinInputs">
        <input type="tel" maxlength="1" class="pin-box" data-idx="0" autocomplete="off" inputmode="numeric">
        <input type="tel" maxlength="1" class="pin-box" data-idx="1" autocomplete="off" inputmode="numeric">
        <input type="tel" maxlength="1" class="pin-box" data-idx="2" autocomplete="off" inputmode="numeric">
        <input type="tel" maxlength="1" class="pin-box" data-idx="3" autocomplete="off" inputmode="numeric">
      </div>
      <div id="pinError" style="color:#d32f2f;font-size:0.85rem;margin-bottom:12px;display:none;"></div>
      <button id="pinSubmitBtn" class="pin-submit-btn" onclick="verifyPin()">
        <i class="bi bi-unlock-fill"></i> <span class="t" data-en="Verify PIN" data-ta="PIN ஐ சரிபார்">Verify PIN</span>
      </button>
    </div>
    <div class="footer"><span class="t" data-en="Tamil Nadu Vanigargalin Sangamam" data-ta="தமிழ்நாடு வணிகர்களின் சங்கமம்">Tamil Nadu Vanigargalin Sangamam</span> &copy; {{ date('Y') }}</div>
  </div>

  <!-- Card Section (shown directly if details completed, otherwise hidden until PIN verified) -->
  <div class="card" id="cardSection" style="{{ $detailsCompleted ? '' : 'display:none;' }}">
    <div class="card-header">
      <h2><i class="bi bi-shield-check"></i> <span class="t" data-en="Tamil Nadu Vanigargalin Sangamam" data-ta="தமிழ்நாடு வணிகர்களின் சங்கமம்">Tamil Nadu Vanigargalin Sangamam</span></h2>
      <p><span class="t" data-en="Member Verification" data-ta="உறுப்பினர் சரிபார்ப்பு">Member Verification</span></p>
      <div class="verified-badge"><i class="bi bi-patch-check-fill"></i> <span class="t" data-en="Member ID Card" data-ta="உறுப்பினர் அடையாள அட்டை">Member ID Card</span></div>
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
          <span class="card3d-hint"><i class="bi bi-hand-index-thumb"></i> <span class="t" data-en="Drag to rotate" data-ta="சுழற்ற இழுக்கவும்">Drag to rotate</span></span>
          <button class="card3d-btn" onclick="rotate3d(1)" title="Rotate Right"><i class="bi bi-arrow-clockwise"></i></button>
        </div>
      </div>

      <!-- Member Details -->
      <div class="details-list">
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="Name" data-ta="பெயர்">Name</span></span>
          <span class="detail-value">{{ $member->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="Membership" data-ta="உறுப்பினர்">Membership</span></span>
          <span class="detail-value">{{ $member->membership ?? 'Member' }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="Member ID" data-ta="உறுப்பினர் எண்">Member ID</span></span>
          <span class="detail-value" style="font-family:monospace;">{{ $member->unique_id ?? '' }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="Assembly" data-ta="சட்டமன்றத் தொகுதி">Assembly</span></span>
          <span class="detail-value">{{ $member->assembly ?? 'N/A' }}@if(!empty($member->assembly)) <span style="display:inline-block;font-size:0.65rem;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.4px;">Assm</span>@endif</span>
        </div>
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="District" data-ta="மாவட்டம்">District</span></span>
          <span class="detail-value">{{ $member->district ?? 'N/A' }}@if(!empty($member->district)) <span style="display:inline-block;font-size:0.65rem;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.4px;">Dist</span>@endif</span>
        </div>
        @if(!empty($member->zone))
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="Zone" data-ta="மண்டலம்">Zone</span></span>
          <span class="detail-value">{{ $member->zone }}</span>
        </div>
        @endif
        @if(!empty($member->dob))
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="DOB" data-ta="பிறந்த தேதி">DOB</span></span>
          <span class="detail-value">{{ $member->dob }}</span>
        </div>
        @endif
        @if(!empty($member->age))
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="Age" data-ta="வயது">Age</span></span>
          <span class="detail-value">{{ $member->age }}</span>
        </div>
        @endif
        @if(!empty($member->blood_group))
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="Blood Group" data-ta="இரத்தக் குழு">Blood Group</span></span>
          <span class="detail-value">{{ $member->blood_group }}</span>
        </div>
        @endif
        <div class="detail-item">
          <span class="detail-label"><span class="t" data-en="Details Status" data-ta="விவரங்கள் நிலை">Details Status</span></span>
          <span class="detail-value">
            @if(!empty($member->details_completed) && $member->details_completed)
              <span class="status-badge status-complete"><i class="bi bi-check-circle"></i> <span class="t" data-en="Complete" data-ta="நிரப்பப்பட்டது">Complete</span></span>
            @else
              <span class="status-badge status-pending"><i class="bi bi-clock"></i> <span class="t" data-en="Pending" data-ta="நிலுவையில்">Pending</span></span>
            @endif
          </span>
        </div>
      </div>
    </div>
    <!-- Download Card Button -->
    <div style="padding: 0 24px 8px; display: flex; gap: 10px;">
      <button id="downloadCardBtn" onclick="downloadCard()" style="flex:1;padding:12px;background:linear-gradient(135deg,#009345,#007a38);color:#fff;border:none;border-radius:12px;font-size:0.92rem;font-weight:600;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:transform 0.1s;">
        <i class="bi bi-download"></i> <span class="t" data-en="Download Card" data-ta="அட்டையை பதிவிறக்கம்">Download Card</span>
      </button>
    </div>
    <div class="footer">
      <span class="t" data-en="Tamil Nadu Vanigargalin Sangamam" data-ta="தமிழ்நாடு வணிகர்களின் சங்கமம்">Tamil Nadu Vanigargalin Sangamam</span> &copy; {{ date('Y') }}
    </div>
  </div>

  <script>
    // Language system - read from localStorage (shared with chatbot)
    const lang = localStorage.getItem('vanigam_lang') || 'en';
    if (lang === 'ta') {
      document.querySelectorAll('.t').forEach(el => {
        if (el.dataset.ta) el.textContent = el.dataset.ta;
      });
    }
  </script>
  <script>
    const detailsCompleted = {{ $detailsCompleted ? 'true' : 'false' }};

    // PIN input handling (only if details not completed)
    const pinBoxes = document.querySelectorAll('.pin-box');
    if (!detailsCompleted) {
      pinBoxes.forEach((box, i) => {
        box.addEventListener('input', function() {
          this.value = this.value.replace(/[^0-9]/g, '');
          if (this.value && i < 3) pinBoxes[i + 1].focus();
        });
        box.addEventListener('keydown', function(e) {
          if (e.key === 'Backspace' && !this.value && i > 0) pinBoxes[i - 1].focus();
          if (e.key === 'Enter') verifyPin();
        });
      });
      pinBoxes[0].focus();
    } else {
      // Details completed — card is already visible, init 3D card
      initCard3d();
    }

    async function verifyPin() {
      const pin = Array.from(pinBoxes).map(b => b.value).join('');
      if (pin.length !== 4) {
        document.getElementById('pinError').textContent = lang === 'ta' ? 'அனைத்து 4 இலக்கங்களையும் உள்ளிடவும்.' : 'Please enter all 4 digits.';
        document.getElementById('pinError').style.display = 'block';
        return;
      }
      const btn = document.getElementById('pinSubmitBtn');
      btn.disabled = true;
      btn.innerHTML = '<span style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.6s linear infinite;vertical-align:middle;margin-right:6px;"></span> ' + (lang === 'ta' ? 'சரிபார்க்கிறது...' : 'Verifying...');
      document.getElementById('pinError').style.display = 'none';

      try {
        const res = await fetch('/api/vanigam/verify-member-pin', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ unique_id: '{{ $unique_id }}', pin: pin })
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById('pinSection').style.display = 'none';
          document.getElementById('cardSection').style.display = 'block';
          initCard3d();
        } else {
          document.getElementById('pinError').textContent = data.message || (lang === 'ta' ? 'தவறான PIN.' : 'Invalid PIN.');
          document.getElementById('pinError').style.display = 'block';
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-unlock-fill"></i> ' + (lang === 'ta' ? 'PIN ஐ சரிபார்' : 'Verify PIN');
          pinBoxes.forEach(b => b.value = '');
          pinBoxes[0].focus();
        }
      } catch(e) {
        document.getElementById('pinError').textContent = lang === 'ta' ? 'நெட்வொர்க் பிழை. மீண்டும் முயற்சிக்கவும்.' : 'Network error. Please try again.';
        document.getElementById('pinError').style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-unlock-fill"></i> ' + (lang === 'ta' ? 'PIN ஐ சரிபார்' : 'Verify PIN');
      }
    }

    // 3D Card rotation
    let angle = 0;
    function rotate3d(dir) {
      angle += dir * 180;
      document.getElementById('card3dInner').style.transform = 'rotateY(' + angle + 'deg)';
    }

    function initCard3d() {
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
        const snap = Math.round(angle / 180) * 180;
        angle = snap;
        inner.style.transform = 'rotateY(' + angle + 'deg)';
      }
      document.addEventListener('mouseup', endDrag);
      document.addEventListener('touchend', endDrag);
    }
  </script>
  <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
  <script>
    async function downloadCard() {
      const btn = document.getElementById('downloadCardBtn');
      const origText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.6s linear infinite;"></span> Downloading...';

      try {
        const frontUrl = @json($member->card_front_url ?? '');
        const backUrl = @json($member->card_back_url ?? '');

        if (frontUrl && backUrl) {
          // Use pre-generated card images from Cloudinary
          const frontImg = await loadImage(frontUrl);
          const backImg = await loadImage(backUrl);

          const gap = 40;
          const canvas = document.createElement('canvas');
          canvas.width = frontImg.width + backImg.width + gap;
          canvas.height = Math.max(frontImg.height, backImg.height) + 50;
          const ctx = canvas.getContext('2d');
          ctx.clearRect(0, 0, canvas.width, canvas.height);

          // Labels
          ctx.font = 'bold 28px Inter, sans-serif';
          ctx.fillStyle = '#333';
          ctx.textAlign = 'center';
          ctx.fillText('Front', frontImg.width / 2, 35);
          ctx.fillText('Back', frontImg.width + gap + backImg.width / 2, 35);

          // Draw cards
          ctx.drawImage(frontImg, 0, 50);
          ctx.drawImage(backImg, frontImg.width + gap, 50);

          const link = document.createElement('a');
          link.download = '{{ $member->unique_id ?? "card" }}_card.png';
          link.href = canvas.toDataURL('image/png', 1.0);
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } else {
          alert(lang === 'ta' ? 'அட்டை படங்கள் இன்னும் உருவாக்கப்படவில்லை.' : 'Card images not yet generated. Please try from the chatbot.');
        }
      } catch(e) {
        console.error('Download error:', e);
        alert('Download failed. Please try again.');
      }

      btn.disabled = false;
      btn.innerHTML = origText;
    }

    function loadImage(url) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => resolve(img);
        img.onerror = () => reject(new Error('Failed to load image'));
        img.src = url;
      });
    }
  </script>
</body>
</html>
