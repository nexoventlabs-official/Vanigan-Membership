<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Complete Your Details — Tamil Nadu Vanigargalin Sangamam</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
    }
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
  </style>
</head>
<body>
  <div class="card">
    <div class="card-header">
      <h2><i class="bi bi-pencil-square"></i> Complete Your Details</h2>
      <p>Fill in missing information for your Tamil Nadu Vanigargalin Sangamam</p>
    </div>
    <div class="card-body">
      <div class="member-info">
        <div class="name">{{ $member->name ?? 'N/A' }}</div>
        <div class="id">{{ $member->unique_id ?? '' }} &bull; {{ $member->epic_no ?? '' }}</div>
      </div>

      @if(!empty($member->details_completed) && $member->details_completed)
        <div class="already-done">
          <i class="bi bi-check-circle-fill"></i>
          <h3>All Details Complete!</h3>
          <p>Your membership details are already filled in. No further action needed.</p>
        </div>
      @else
        <div class="alert alert-success" id="successAlert">
          <i class="bi bi-check-circle"></i> Details saved successfully! Your membership card will be updated.
        </div>
        <div class="alert alert-error" id="errorAlert">
          <i class="bi bi-exclamation-triangle"></i> <span id="errorMsg">Something went wrong.</span>
        </div>

        <form id="detailsForm">
          <div class="form-group">
            <label><i class="bi bi-calendar3"></i> Date of Birth</label>
            <input type="date" id="dob" name="dob" value="{{ $member->dob ?? '' }}" required>
          </div>
          <div class="form-group">
            <label><i class="bi bi-droplet-fill"></i> Blood Group</label>
            <select id="blood_group" name="blood_group">
              <option value="">Select Blood Group</option>
              @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                <option value="{{ $bg }}" {{ (($member->blood_group ?? '') === $bg) ? 'selected' : '' }}>{{ $bg }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label><i class="bi bi-geo-alt-fill"></i> Full Address</label>
            <textarea id="address" name="address" placeholder="Enter your full address...">{{ $member->address ?? '' }}</textarea>
          </div>
          <button type="submit" class="submit-btn" id="submitBtn">
            <i class="bi bi-check-lg"></i> Save Details
          </button>
        </form>
      @endif
    </div>
    <div class="footer">
      Tamil Nadu Vanigargalin Sangamam &copy; {{ date('Y') }}
    </div>
  </div>

  @if(empty($member->details_completed) || !$member->details_completed)
  <script>
    document.getElementById('detailsForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('submitBtn');
      const successAlert = document.getElementById('successAlert');
      const errorAlert = document.getElementById('errorAlert');

      btn.disabled = true;
      btn.innerHTML = '<span style="display:inline-block;width:18px;height:18px;border:3px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.8s linear infinite;vertical-align:middle;margin-right:6px;"></span> Saving...';
      successAlert.classList.remove('show');
      errorAlert.classList.remove('show');

      try {
        const res = await fetch('/api/vanigam/save-details', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            epic_no: '{{ $member->epic_no ?? "" }}',
            dob: document.getElementById('dob').value,
            blood_group: document.getElementById('blood_group').value,
            address: document.getElementById('address').value,
          })
        });
        const data = await res.json();
        if (data.success) {
          successAlert.classList.add('show');
          btn.innerHTML = '<i class="bi bi-check-lg"></i> Saved!';
          btn.style.background = '#4caf50';
          setTimeout(() => { window.location.reload(); }, 2000);
        } else {
          document.getElementById('errorMsg').textContent = data.message || 'Failed to save details.';
          errorAlert.classList.add('show');
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-check-lg"></i> Save Details';
        }
      } catch(err) {
        document.getElementById('errorMsg').textContent = 'Network error. Please try again.';
        errorAlert.classList.add('show');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Save Details';
      }
    });
  </script>
  <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
  @endif
</body>
</html>
