@extends('layouts.app')

@section('title', 'Voter ID Card Generator — அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம்')

@section('content')

<!-- Welcome Hero -->
<div class="card-hero">
  <div class="hero-icon">
    <i class="bi bi-chat-dots"></i>
  </div>
  <h2>Welcome to அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம்!</h2>
  <p>Your Digital Member ID Card Generator</p>
</div>

<!-- Result Card -->
<div class="result-card">
  <div class="result-body" style="text-align: center;">

    <div class="chat-message bot">
      <div class="chat-bubble">
        👋 Hello! Welcome to அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம் ID Card Generator.
      </div>
    </div>

    <div class="chat-message bot">
      <div class="chat-bubble">
        Let's create your official digital Member ID card. 
        <br><br>
        First, please enter your mobile number.
      </div>
    </div>

    <!-- Mobile Input Form -->
    <form id="mobileForm" style="text-align: left;">
      @csrf
      <input 
        type="tel" 
        id="mobile" 
        name="mobile"
        class="form-input" 
        placeholder="Enter 10-digit mobile number"
        maxlength="10"
        pattern="[0-9]{10}"
        required
        autocomplete="tel">
      
      <button type="submit" class="btn btn-primary mt-3">
        <i class="bi bi-send"></i> Send OTP
      </button>
    </form>

    <!-- Error Messages -->
    <div id="errorMessage" style="display:none; color: #c33; margin-top: 1rem;">
      <i class="bi bi-exclamation-circle"></i> <span id="errorText"></span>
    </div>

  </div>
</div>

<script>
document.getElementById('mobileForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const mobile = document.getElementById('mobile').value;
  const errorDiv = document.getElementById('errorMessage');
  
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
      // Store mobile in session storage for next step
      sessionStorage.setItem('mobile', mobile);
      window.location.href = '{{ route("chatbot.verify") }}';
    } else {
      errorDiv.style.display = 'block';
      document.getElementById('errorText').textContent = data.message || 'Failed to send OTP';
    }
  } catch (error) {
    errorDiv.style.display = 'block';
    document.getElementById('errorText').textContent = 'Error: ' + error.message;
  }
});
</script>

@endsection
