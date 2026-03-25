<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>Complete Your Details — Tamil Nadu Vanigargalin Sangamam</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.12); max-width: 420px; width: 100%; overflow: hidden; }
    .card-header { background: linear-gradient(135deg, #009345, #009345); color: #fff; padding: 24px; text-align: center; }
    .card-header h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: 4px; }
    .card-header p { font-size: 0.85rem; opacity: 0.8; }
    .card-body { padding: 24px; }
    .member-info { text-align: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f0f2f5; }
    .member-info .name { font-size: 1.1rem; font-weight: 700; color: #1b5e20; }
    .member-info .id { font-size: 0.82rem; color: #888; font-family: monospace; margin-top: 4px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #444; margin-bottom: 6px; }
    .form-group input, .form-group select, .form-group textarea {
      width: 100%; padding: 12px 14px; border: 1px solid #dfe1e5; border-radius: 12px;
      font-size: 0.95rem; outline: none; transition: border-color 0.2s; font-family: inherit;
      -webkit-appearance: none; appearance: none;
    }
    .form-group input[type="date"] { height: 48px; line-height: 1.2; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #2e7d32; }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .submit-btn {
      width: 100%; padding: 14px; background: linear-gradient(135deg, #009345, #009345); color: #fff;
      border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer;
      transition: all 0.2s; margin-top: 8px;
    }
    .submit-btn:hover { opacity: 0.9; transform: translateY(-1px); }
    .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .alert { padding: 12px 14px; border-radius: 10px; font-size: 0.9rem; margin-bottom: 16px; display: none; }
    .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .alert-error { background: #fbe9e7; color: #d32f2f; border: 1px solid #ef9a9a; }
    .alert.show { display: block; }
    .already-done { text-align: center; padding: 20px; }
    .already-done i { font-size: 3rem; color: #2e7d32; }
    .already-done h3 { margin-top: 12px; color: #1b5e20; }
    .already-done p { color: #666; margin-top: 8px; font-size: 0.9rem; }
    .footer { text-align: center; padding: 16px 24px 24px; font-size: 0.78rem; color: #999; }
    .pin-box {
      width: 52px; height: 58px; text-align: center; font-size: 1.5rem; font-weight: 700;
      border: 2px solid #dfe1e5; border-radius: 12px; outline: none; font-family: monospace;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .pin-box:focus { border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46,125,50,0.15); }
  </style>
</head>
<body>
  @if(!empty($member->details_completed) && $member->details_completed)
  <script>window.location.replace('/member/verify/{{ $unique_id }}');</script>
  @endif
  <script>
    // Fallback: API check in case Varnish serves stale cached page
    (function(){
      var uid = '{{ $unique_id }}';
      fetch('/api/vanigam/member/' + encodeURIComponent(uid))
        .then(function(r){ return r.json(); })
        .then(function(d){
          if(d.success && d.member && d.member.details_completed) {
            window.location.replace('/member/verify/' + encodeURIComponent(uid));
          }
        }).catch(function(){});
    })();
  </script>
  <!-- PIN Entry Section -->
  <div class="card" id="pinSection">
    <div class="card-header">
      <h2><i class="bi bi-shield-lock"></i> <span class="t" data-en="Verify Your Identity" data-ta="உங்கள் அடையாளத்தைச் சரிபார்க்கவும்">Verify Your Identity</span></h2>
      <p><span class="t" data-en="Enter your secret PIN to continue" data-ta="தொடர உங்கள் ரகசிய PIN ஐ உள்ளிடவும்">Enter your secret PIN to continue</span></p>
    </div>
    <div class="card-body" style="text-align:center;">
      <div style="margin-bottom:20px;">
        <i class="bi bi-person-badge" style="font-size:3rem;color:#2e7d32;"></i>
        <h3 style="font-size:1.1rem;font-weight:700;color:#333;margin:10px 0 4px;">{{ $member->name ?? '' }}</h3>
        <p style="font-size:0.82rem;color:#888;font-family:monospace;">{{ $member->unique_id ?? '' }}</p>
      </div>
      <p style="font-size:0.9rem;color:#555;margin-bottom:16px;"><span class="t" data-en="Enter your 4-digit secret PIN" data-ta="உங்கள் 4 இலக்க ரகசிய PIN ஐ உள்ளிடவும்">Enter your 4-digit secret PIN</span></p>
      <div style="display:flex;justify-content:center;gap:10px;margin-bottom:16px;" id="pinInputs">
        <input type="tel" maxlength="1" class="pin-box" data-idx="0" autocomplete="off" inputmode="numeric">
        <input type="tel" maxlength="1" class="pin-box" data-idx="1" autocomplete="off" inputmode="numeric">
        <input type="tel" maxlength="1" class="pin-box" data-idx="2" autocomplete="off" inputmode="numeric">
        <input type="tel" maxlength="1" class="pin-box" data-idx="3" autocomplete="off" inputmode="numeric">
      </div>
      <div id="pinError" style="color:#d32f2f;font-size:0.85rem;margin-bottom:12px;display:none;"></div>
      <button id="pinSubmitBtn" class="submit-btn" onclick="verifyPin()">
        <i class="bi bi-unlock-fill"></i> <span class="t" data-en="Verify PIN" data-ta="PIN ஐ சரிபார்">Verify PIN</span>
      </button>
    </div>
    <div class="footer"><span class="t" data-en="Tamil Nadu Vanigargalin Sangamam" data-ta="தமிழ்நாடு வணிகர்களின் சங்கமம்">Tamil Nadu Vanigargalin Sangamam</span> &copy; {{ date('Y') }}</div>
  </div>

  <!-- Details Form Section (hidden until PIN verified) -->
  <div class="card" id="detailsSection" style="display:none;">
    <div class="card-header">
      <h2><i class="bi bi-pencil-square"></i> <span class="t" data-en="Complete Your Details" data-ta="உங்கள் விவரங்களை நிரப்பவும்">Complete Your Details</span></h2>
      <p><span class="t" data-en="Fill in missing information for your Tamil Nadu Vanigargalin Sangamam" data-ta="உங்கள் தமிழ்நாடு வணிகர்களின் சங்கமத்திற்கான விடுபட்ட தகவல்களை நிரப்பவும்">Fill in missing information for your Tamil Nadu Vanigargalin Sangamam</span></p>
    </div>
    <div class="card-body">
      <div class="member-info">
        <div class="name">{{ $member->name ?? 'N/A' }}</div>
        <div class="id">{{ $member->unique_id ?? '' }} &bull; {{ $member->epic_no ?? '' }}</div>
      </div>

      @if(!empty($member->details_completed) && $member->details_completed)
        <div class="already-done">
          <i class="bi bi-check-circle-fill"></i>
          <h3><span class="t" data-en="All Details Complete!" data-ta="அனைத்து விவரங்களும் நிரப்பப்பட்டுள்ளன!">All Details Complete!</span></h3>
          <p><span class="t" data-en="Your membership details are already filled in. No further action needed." data-ta="உங்கள் உறுப்பினர் விவரங்கள் ஏற்கனவே நிரப்பப்பட்டுள்ளன. மேலும் எந்த நடவடிக்கையும் தேவையில்லை.">Your membership details are already filled in. No further action needed.</span></p>
        </div>
      @else
        <div class="alert alert-success" id="successAlert">
          <i class="bi bi-check-circle"></i> <span class="t" data-en="Details saved successfully! Your membership card will be updated." data-ta="விவரங்கள் வெற்றிகரமாகச் சேமிக்கப்பட்டன! உங்கள் உறுப்பினர் அட்டை புதுப்பிக்கப்படும்.">Details saved successfully! Your membership card will be updated.</span>
        </div>
        <div class="alert alert-error" id="errorAlert">
          <i class="bi bi-exclamation-triangle"></i> <span id="errorMsg"><span class="t" data-en="Something went wrong." data-ta="ஏதோ பிழை ஏற்பட்டது.">Something went wrong.</span></span>
        </div>

        <form id="detailsForm">
          <div class="form-group">
            <label><i class="bi bi-calendar3"></i> <span class="t" data-en="Date of Birth" data-ta="பிறந்த தேதி">Date of Birth</span></label>
            <input type="date" id="dob" name="dob" value="{{ $member->dob ?? '' }}" required>
          </div>
          <div class="form-group">
            <label><i class="bi bi-droplet-fill"></i> <span class="t" data-en="Blood Group" data-ta="இரத்தக் குழு">Blood Group</span></label>
            <select id="blood_group" name="blood_group">
              <option value=""><span class="t" data-en="Select Blood Group" data-ta="இரத்தக் குழுவைத் தேர்ந்தெடுக்கவும்">Select Blood Group</span></option>
              @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                <option value="{{ $bg }}" {{ (($member->blood_group ?? '') === $bg) ? 'selected' : '' }}>{{ $bg }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label><i class="bi bi-geo-alt-fill"></i> <span class="t" data-en="Full Address" data-ta="முழு முகவரி">Full Address</span></label>
            <textarea id="address" name="address" placeholder="Enter your full address...">{{ $member->address ?? '' }}</textarea>
          </div>
          <button type="submit" class="submit-btn" id="submitBtn">
            <i class="bi bi-check-lg"></i> <span class="t" data-en="Save Details" data-ta="விவரங்களைச் சேமி">Save Details</span>
          </button>
        </form>
      @endif
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
    // PIN input handling
    const pinBoxes = document.querySelectorAll('.pin-box');
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
          document.getElementById('detailsSection').style.display = 'block';
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
  </script>

  @if(empty($member->details_completed) || !$member->details_completed)
  <script>
    document.getElementById('detailsForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('submitBtn');
      const successAlert = document.getElementById('successAlert');
      const errorAlert = document.getElementById('errorAlert');

      btn.disabled = true;
      btn.innerHTML = '<span style="display:inline-block;width:18px;height:18px;border:3px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.8s linear infinite;vertical-align:middle;margin-right:6px;"></span> ' + (lang === 'ta' ? 'சேமிக்கிறது...' : 'Saving...');
      successAlert.classList.remove('show');
      errorAlert.classList.remove('show');

      try {
        const res = await fetch('/api/vanigam/save-details', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            epic_no: '{{ $member->epic_no ?? "" }}',
            unique_id: '{{ $unique_id }}',
            dob: (function(){ const v = document.getElementById('dob').value; if (!v) return ''; const p = v.split('-'); return p[2] + '/' + p[1] + '/' + p[0]; })(),
            blood_group: document.getElementById('blood_group').value,
            address: document.getElementById('address').value,
          })
        });
        const data = await res.json();
        if (data.success) {
          successAlert.classList.add('show');
          btn.innerHTML = '<i class="bi bi-check-lg"></i> ' + (lang === 'ta' ? 'சேமிக்கப்பட்டது! அட்டை உருவாக்கப்படுகிறது...' : 'Saved! Generating card...');
          btn.style.background = '#4caf50';
          // Capture and upload card images directly
          if (data.member) {
            try {
              await captureAndUploadCard(data.member);
            } catch(e) { console.error('Card capture error:', e); }
          }
          setTimeout(() => { window.location.href = '/member/verify/{{ $unique_id }}'; }, 3000);
        } else {
          document.getElementById('errorMsg').textContent = data.message || (lang === 'ta' ? 'விவரங்களைச் சேமிக்கத் தோல்வி.' : 'Failed to save details.');
          errorAlert.classList.add('show');
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-check-lg"></i> ' + (lang === 'ta' ? 'விவரங்களைச் சேமி' : 'Save Details');
        }
      } catch(err) {
        document.getElementById('errorMsg').textContent = lang === 'ta' ? 'நெட்வொர்க் பிழை. மீண்டும் முயற்சிக்கவும்.' : 'Network error. Please try again.';
        errorAlert.classList.add('show');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> ' + (lang === 'ta' ? 'விவரங்களைச் சேமி' : 'Save Details');
      }
    });
  </script>
  <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
  @endif
<!-- Hidden full-size card for image capture -->
<div id="cardCaptureWrap" style="position:fixed;left:-9999px;top:0;z-index:-1;background:#fff;">
  <div id="captureFront" style="width:421px;position:relative;overflow:hidden;font-family:Arial,sans-serif;">
    <img id="capBgFront" src="https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232516/vanigan/templates/ID_Front.png" style="display:block;width:421px;" crossorigin="anonymous" />
    <div style="position:absolute;top:182px;left:50%;transform:translateX(-50%);width:137px;">
      <img id="capPhoto" src="" crossorigin="anonymous" style="display:none;border:5px solid #009245;border-radius:22px;width:137px;height:136px;object-fit:cover;" />
    </div>
    <div style="position:absolute;top:328px;left:28px;right:28px;text-align:center;">
      <p id="capName" style="font-size:23px;font-weight:700;color:#009245;line-height:1.08;margin:0;"></p>
      <div style="display:flex;flex-direction:column;align-items:center;gap:8px;margin-top:6px;">
        <div style="text-align:center;padding:0 18px;"><p id="capMembership" style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;"></p></div>
        <div style="text-align:center;padding:0 18px;"><p id="capAssembly" style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;"></p></div>
        <div style="text-align:center;padding:0 18px;"><p id="capDistrict" style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;"></p></div>
        <div style="text-align:center;padding:0 18px;"><p id="capUniqueId" style="font-size:18px;font-weight:700;letter-spacing:0.2px;margin:0;color:#111;"></p></div>
      </div>
    </div>
  </div>
  <div id="captureBack" style="width:421px;position:relative;overflow:hidden;font-family:Arial,sans-serif;margin-top:20px;">
    <img id="capBgBack" src="https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png" style="display:block;width:421px;" crossorigin="anonymous" />
    <div style="position:absolute;top:234px;left:22px;right:20px;color:#111;">
      <div style="transform:translateY(-60px);">
        <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;"><div style="font-size:14px;font-weight:700;text-transform:uppercase;">DATE OF BIRTH</div><div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div><div id="capDob" style="font-size:17px;font-weight:700;line-height:1.12;"></div></div>
        <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;"><div style="font-size:14px;font-weight:700;text-transform:uppercase;">AGE</div><div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div><div id="capAge" style="font-size:17px;font-weight:700;line-height:1.12;"></div></div>
        <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;"><div style="font-size:14px;font-weight:700;text-transform:uppercase;">BLOOD GROUP</div><div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div><div id="capBlood" style="font-size:17px;font-weight:700;line-height:1.12;"></div></div>
        <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:76px;overflow:hidden;"><div style="font-size:14px;font-weight:700;text-transform:uppercase;">ADDRESS</div><div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div><div id="capAddress" style="font-size:17px;font-weight:700;line-height:1.12;word-break:break-word;"></div></div>
        <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;"><div style="font-size:14px;font-weight:700;text-transform:uppercase;">CONTACT</div><div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div><div style="font-size:17px;font-weight:700;line-height:1.12;"><span id="capContact" style="background:rgba(255,255,255,0.78);display:inline-block;padding:0 4px;"></span></div></div>
      </div>
      <div style="display:grid;grid-template-columns:40% 60%;align-items:start;margin-top:10px;">
        <div style="padding-left:20px;"><img id="capQr" src="" width="96" height="88" crossorigin="anonymous" /></div>
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
async function captureAndUploadCard(member) {
  if (!member || !member.unique_id) return;
  try {
    const photo = document.getElementById('capPhoto');
    if (member.photo_url) { photo.src = member.photo_url; photo.style.display = 'block'; }
    document.getElementById('capName').textContent = member.name || '';
    document.getElementById('capMembership').textContent = member.membership || 'Member';
    document.getElementById('capAssembly').innerHTML = member.assembly ? member.assembly + ' <span style="display:inline-block;font-size:10px;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 5px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.5px;line-height:1.4;">LA</span>' : '';
    document.getElementById('capDistrict').innerHTML = member.district ? member.district + ' <span style="display:inline-block;font-size:10px;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 5px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.5px;line-height:1.4;">Dist</span>' : '';
    document.getElementById('capUniqueId').textContent = member.unique_id || '';
    document.getElementById('capDob').textContent = member.dob || 'xxxxxx';
    document.getElementById('capAge').textContent = member.age || 'xxxxxx';
    document.getElementById('capBlood').textContent = member.blood_group || 'xxxxxx';
    document.getElementById('capAddress').textContent = member.address || 'xxxxxx';
    document.getElementById('capContact').textContent = member.contact_number || ('+91 ' + (member.mobile || ''));
    document.getElementById('capQr').src = '/api/vanigam/qr/' + member.unique_id;

    // Wait for images to load
    const wrap = document.getElementById('cardCaptureWrap');
    const imgs = wrap.querySelectorAll('img');
    await Promise.all(Array.from(imgs).map(img => {
      if (img.complete && img.naturalWidth > 0) return Promise.resolve();
      return new Promise(r => { img.onload = r; img.onerror = r; setTimeout(r, 8000); });
    }));
    await new Promise(r => setTimeout(r, 1000));

    const opts = { scale: 3, useCORS: true, allowTaint: false, backgroundColor: null, logging: false };
    const frontCanvas = await html2canvas(document.getElementById('captureFront'), opts);
    const backCanvas = await html2canvas(document.getElementById('captureBack'), opts);

    const frontData = frontCanvas.toDataURL('image/png', 1.0);
    const backData = backCanvas.toDataURL('image/png', 1.0);

    if (frontData.length < 5000 || backData.length < 5000) {
      console.warn('Card capture: images seem blank, skipping upload');
      return;
    }

    const res = await fetch('/api/vanigam/upload-card-images', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ unique_id: member.unique_id, front_image: frontData, back_image: backData }),
    });
    const data = await res.json();
    if (data.success) {
      console.log('Card images captured and uploaded successfully.');
    } else {
      console.warn('Card upload failed:', data);
    }
  } catch(e) {
    console.error('captureAndUploadCard error:', e);
  }
}
</script>
</body>
</html>
