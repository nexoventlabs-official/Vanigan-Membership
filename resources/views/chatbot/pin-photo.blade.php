@extends('layouts.app')

@section('title', 'Set PIN & Upload Photo — அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம்')

@section('content')

<!-- PIN Hero -->
<div class="card-hero">
  <div class="hero-icon">
    <i class="bi bi-lock"></i>
  </div>
  <h2>Secure Your Account</h2>
  <p>Set a 4-digit PIN and upload your photo</p>
</div>

<!-- Result Card -->
<div class="result-card">
  <div class="result-body">

    <!-- PIN Setup Step -->
    <div id="pinStep">
      <div class="chat-message bot">
        <div class="chat-bubble">
          ✓ Voter details verified!
        </div>
      </div>

      <div class="chat-message bot">
        <div class="chat-bubble">
          Now, set a 4-digit PIN. You'll need this PIN to login next time.
        </div>
      </div>

      <form id="pinForm" style="text-align: left;">
        @csrf
        <label style="display: block; color: #999; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase;">Create PIN</label>
        <input 
          type="password" 
          id="pin" 
          name="pin"
          class="form-input" 
          placeholder="••••"
          maxlength="4"
          pattern="[0-9]{4}"
          inputmode="numeric"
          required>
        
        <label style="display: block; color: #999; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem; margin-top: 1rem; text-transform: uppercase;">Confirm PIN</label>
        <input 
          type="password" 
          id="pinConfirm" 
          name="pin_confirm"
          class="form-input" 
          placeholder="••••"
          maxlength="4"
          pattern="[0-9]{4}"
          inputmode="numeric"
          required>
        
        <button type="submit" class="btn btn-primary mt-3">
          <i class="bi bi-check-circle"></i> Set PIN
        </button>
      </form>

      <div id="pinError" style="display:none; color: #c33; margin-top: 1rem; text-align: center;">
        <i class="bi bi-exclamation-circle"></i> <span id="pinErrorText"></span>
      </div>
    </div>

    <!-- Photo Upload Step (shown after PIN) -->
    <div id="photoStep" style="display:none;">
      <div class="chat-message bot">
        <div class="chat-bubble">
          ✓ PIN set successfully!
        </div>
      </div>

      <div class="chat-message bot">
        <div class="chat-bubble">
          Now, please upload your photo for the ID card.
          <br><br>
          <small>Photo should be clear and in landscape orientation.</small>
        </div>
      </div>

      <form id="photoForm" style="text-align: left;">
        @csrf
        
        <!-- Photo Preview -->
        <div id="photoPreview" style="display:none; margin-bottom: 1rem;">
          <div style="background: #f8f9fa; border-radius: 12px; padding: 1rem; text-align: center;">
            <img id="photoImg" style="max-width: 100%; border-radius: 8px; max-height: 200px;">
          </div>
        </div>

        <!-- File Upload -->
        <input 
          type="file" 
          id="photo" 
          name="photo"
          class="form-input" 
          accept="image/*"
          capture="environment"
          required
          style="padding: 0;">
        
        <div style="margin-top: 1rem; display: flex; gap: 8px;">
          <button type="submit" class="btn btn-primary" style="flex: 1;">
            <i class="bi bi-cloud-upload"></i> Generate Card
          </button>
        </div>
      </form>

      <div id="photoError" style="display:none; color: #c33; margin-top: 1rem; text-align: center;">
        <i class="bi bi-exclamation-circle"></i> <span id="photoErrorText"></span>
      </div>
    </div>

  </div>
</div>

<script>
// PIN Setup
document.getElementById('pinForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const pin = document.getElementById('pin').value;
  const pinConfirm = document.getElementById('pinConfirm').value;
  const pinError = document.getElementById('pinError');
  
  if (pin !== pinConfirm) {
    pinError.style.display = 'block';
    document.getElementById('pinErrorText').textContent = 'PINs do not match!';
    return;
  }
  
  if (pin.length !== 4) {
    pinError.style.display = 'block';
    document.getElementById('pinErrorText').textContent = 'PIN must be 4 digits!';
    return;
  }
  
  try {
    const response = await fetch('{{ route("api.pin-setup") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      },
      body: JSON.stringify({ pin, pin_confirm: pinConfirm })
    });
    
    const data = await response.json();
    
    if (data.success) {
      pinError.style.display = 'none';
      sessionStorage.setItem('pin_set', 'true');
      
      // Show photo step
      document.getElementById('pinStep').style.display = 'none';
      document.getElementById('photoStep').style.display = 'block';
    } else {
      pinError.style.display = 'block';
      document.getElementById('pinErrorText').textContent = data.message || 'Error setting PIN';
    }
  } catch (error) {
    pinError.style.display = 'block';
    document.getElementById('pinErrorText').textContent = 'Error: ' + error.message;
  }
});

// Photo Upload Preview
document.getElementById('photo').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('photoImg').src = event.target.result;
      document.getElementById('photoPreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
});

// Photo Form Submission (Generate Card)
document.getElementById('photoForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData();
  formData.append('photo', document.getElementById('photo').files[0]);
  formData.append('epic_no', sessionStorage.getItem('epic_no'));
  formData.append('_token', document.querySelector('input[name="_token"]').value);
  
  try {
    const response = await fetch('{{ route("api.generate-card") }}', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      sessionStorage.setItem('job_id', data.job_id);
      // Redirect to card generation/display page
      window.location.href = '{{ route("card.show") }}';
    } else {
      document.getElementById('photoError').style.display = 'block';
      document.getElementById('photoErrorText').textContent = data.message || 'Error generating card';
    }
  } catch (error) {
    document.getElementById('photoError').style.display = 'block';
    document.getElementById('photoErrorText').textContent = 'Error: ' + error.message;
  }
});

// Prevent space input in PIN
['pin', 'pinConfirm'].forEach(id => {
  document.getElementById(id).addEventListener('keypress', function(e) {
    if (!/[0-9]/.test(e.key)) {
      e.preventDefault();
    }
  });
});
</script>

@endsection
