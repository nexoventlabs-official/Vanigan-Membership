@extends('layouts.app')

@section('title', 'Verify OTP — அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம்')

@section('content')

<!-- Verification Hero -->
<div class="card-hero">
  <div class="hero-icon">
    <i class="bi bi-shield-check"></i>
  </div>
  <h2>Verify Your Mobile</h2>
  <p>Complete your registration</p>
</div>

<!-- Result Card -->
<div class="result-card">
  <div class="result-body">

    <div class="chat-message bot">
      <div class="chat-bubble">
        ✓ OTP sent to your mobile +91 {{ substr($mobile ?? '', -10) }}
      </div>
    </div>

    <div class="chat-message bot">
      <div class="chat-bubble">
        Please enter the 6-digit OTP you received.
      </div>
    </div>

    <!-- OTP Form -->
    <form id="otpForm" style="text-align: left;">
      @csrf
      <input type="hidden" name="mobile" value="{{ $mobile ?? '' }}">
      
      <input 
        type="text" 
        id="otp" 
        name="otp"
        class="form-input" 
        placeholder="Enter 6-digit OTP"
        maxlength="6"
        pattern="[0-9]{6}"
        required
        inputmode="numeric">
      
      <div style="margin-top: 1rem; display: flex; gap: 8px;">
        <button type="submit" class="btn btn-primary" style="flex: 1;">
          <i class="bi bi-check-circle"></i> Verify OTP
        </button>
      </div>

      <!-- Resend & Change Options -->
      <div style="margin-top: 1rem; text-align: center;">
        <a href="#" id="resendBtn" style="color: #3390ec; text-decoration: none; font-size: 0.9rem;">
          <i class="bi bi-arrow-clockwise"></i> Resend OTP (55s)
        </a>
        <br>
        <a href="{{ route('home') }}" style="color: #999; text-decoration: none; font-size: 0.9rem; margin-top: 0.5rem; display: inline-block;">
          <i class="bi bi-arrow-left"></i> Change Mobile Number
        </a>
      </div>
    </form>

    <!-- Error Messages -->
    <div id="errorMessage" style="display:none; color: #c33; margin-top: 1rem; text-align: center;">
      <i class="bi bi-exclamation-circle"></i> <span id="errorText"></span>
    </div>

  </div>
</div>

<script>
// OTP Timer
let resendTimer = 60;
const resendBtn = document.getElementById('resendBtn');

function updateResendBtn() {
  if (resendTimer > 0) {
    resendBtn.style.pointerEvents = 'none';
    resendBtn.style.opacity = '0.5';
    resendBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Resend OTP (${resendTimer}s)`;
    resendTimer--;
    setTimeout(updateResendBtn, 1000);
  } else {
    resendBtn.style.pointerEvents = 'auto';
    resendBtn.style.opacity = '1';
    resendBtn.innerHTML = `<i class="bi bi-arrow-clockwise"></i> Resend OTP`;
  }
}

updateResendBtn();

// Resend OTP
resendBtn.addEventListener('click', async function(e) {
  e.preventDefault();
  if (resendTimer > 0) return;
  
  const mobile = document.querySelector('input[name="mobile"]').value;
  
  try {
    const response = await fetch('{{ route("api.send-otp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      },
      body: JSON.stringify({ mobile })
    });
    
    const data = await response.json();
    if (data.success) {
      resendTimer = 60;
      updateResendBtn();
    }
  } catch (error) {
    console.error('Error:', error);
  }
});

// OTP Form Submission
document.getElementById('otpForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const mobile = document.querySelector('input[name="mobile"]').value;
  const otp = document.getElementById('otp').value;
  const errorDiv = document.getElementById('errorMessage');
  
  try {
    const response = await fetch('{{ route("api.verify-otp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      },
      body: JSON.stringify({ mobile, otp })
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Store mobile in session
      sessionStorage.setItem('mobile', mobile);
      sessionStorage.setItem('verified', 'true');
      
      // Check if user has existing card
      if (data.has_card) {
        window.location.href = '{{ route("card.show") }}';
      } else {
        window.location.href = '{{ route("chatbot.epic") }}';
      }
    } else {
      errorDiv.style.display = 'block';
      document.getElementById('errorText').textContent = data.message || 'Invalid OTP';
    }
  } catch (error) {
    errorDiv.style.display = 'block';
    document.getElementById('errorText').textContent = 'Error: ' + error.message;
  }
});

// Auto-focus and format OTP input
document.getElementById('otp').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
  if (this.value.length === 6) {
    // Auto-submit when 6 digits entered
    document.getElementById('otpForm').dispatchEvent(new Event('submit'));
  }
});
</script>

@endsection
