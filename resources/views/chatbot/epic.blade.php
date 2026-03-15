@extends('layouts.app')

@section('title', 'Enter EPIC Number — அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம்')

@section('content')

<!-- EPIC Hero -->
<div class="card-hero">
  <div class="hero-icon">
    <i class="bi bi-search"></i>
  </div>
  <h2>Find Your Voter Details</h2>
  <p>Enter your EPIC number to continue</p>
</div>

<!-- Result Card -->
<div class="result-card">
  <div class="result-body">

    <div class="chat-message bot">
      <div class="chat-bubble">
        ✓ Mobile number verified!
      </div>
    </div>

    <div class="chat-message bot">
      <div class="chat-bubble">
        Now, please enter your EPIC Number (Voter ID).
      </div>
    </div>

    <!-- EPIC Form -->
    <form id="epicForm" style="text-align: left;">
      @csrf
      <input 
        type="text" 
        id="epicNo" 
        name="epic_no"
        class="form-input" 
        placeholder="e.g. ABC1234567"
        maxlength="20"
        required
        autocomplete="off">
      
      <button type="submit" class="btn btn-primary mt-3">
        <i class="bi bi-arrow-right"></i> Look Up
      </button>
    </form>

    <!-- Voter Details Section (hidden initially) -->
    <div id="voterDetails" style="display:none; margin-top: 1.5rem;">
      <div class="chat-message bot">
        <div class="chat-bubble">
          ✓ Voter Found!
        </div>
      </div>

      <div style="background: #f8f9fa; border-radius: 12px; padding: 1rem; margin-top: 1rem; text-align: left;">
        <div style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
          <div style="color: #999; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Name</div>
          <div id="voterName" style="color: #333; font-weight: 700; font-size: 0.95rem;"></div>
        </div>
        <div style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
          <div style="color: #999; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">EPIC No</div>
          <div id="voterEpic" style="color: #333; font-weight: 700; font-size: 0.95rem;"></div>
        </div>
        <div style="padding: 0.5rem 0; border-bottom: 1px solid #e0e0e0;">
          <div style="color: #999; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Assembly</div>
          <div id="voterAssembly" style="color: #333; font-weight: 700; font-size: 0.95rem;"></div>
        </div>
        <div style="padding: 0.5rem 0;">
          <div style="color: #999; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">District</div>
          <div id="voterDistrict" style="color: #333; font-weight: 700; font-size: 0.95rem;"></div>
        </div>
      </div>

      <button id="continueBtn" type="button" class="btn btn-primary mt-3">
        <i class="bi bi-arrow-right"></i> Continue to PIN Setup
      </button>
    </div>

    <!-- Error Messages -->
    <div id="errorMessage" style="display:none; color: #c33; margin-top: 1rem; text-align: center;">
      <i class="bi bi-exclamation-circle"></i> <span id="errorText"></span>
    </div>

  </div>
</div>

<script>
document.getElementById('epicForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const epicNo = document.getElementById('epicNo').value.toUpperCase();
  const errorDiv = document.getElementById('errorMessage');
  const voterDetails = document.getElementById('voterDetails');
  
  try {
    const response = await fetch(`{{ route("api.voter-lookup", ":epic") }}`.replace(':epic', epicNo), {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      }
    });
    
    const data = await response.json();
    
    if (data.success && data.voter) {
      // Display voter details
      document.getElementById('voterName').textContent = data.voter.name || 'N/A';
      document.getElementById('voterEpic').textContent = epicNo;
      document.getElementById('voterAssembly').textContent = data.voter.assembly_name || 'N/A';
      document.getElementById('voterDistrict').textContent = data.voter.district || 'N/A';
      
      voterDetails.style.display = 'block';
      errorDiv.style.display = 'none';
      
      // Store in session
      sessionStorage.setItem('epic_no', epicNo);
      sessionStorage.setItem('voter_data', JSON.stringify(data.voter));
    } else {
      errorDiv.style.display = 'block';
      document.getElementById('errorText').textContent = 'Voter not found. Please check your EPIC number.';
      voterDetails.style.display = 'none';
    }
  } catch (error) {
    errorDiv.style.display = 'block';
    document.getElementById('errorText').textContent = 'Error: ' + error.message;
    voterDetails.style.display = 'none';
  }
});

// Continue button
document.getElementById('continueBtn')?.addEventListener('click', function() {
  window.location.href = '{{ route("chatbot.pin") }}';
});
</script>

@endsection
