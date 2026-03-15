@extends('layouts.app')

@section('title', 'Your Member ID Card — அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம்')

@section('content')

<!-- Success Hero -->
<div class="card-hero">
  <div class="hero-icon">
    <i class="bi bi-check-circle-fill"></i>
  </div>
  <h2>Card Generated Successfully!</h2>
  <p>Your Member ID card is ready to download</p>
</div>

<!-- Result Card -->
<div class="result-card">
  <div class="result-body">

    <!-- Flash Messages -->
    @if ($errors->any())
    <div class="mb-3">
      <div class="alert alert-danger py-2" style="font-size:0.9rem;">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    </div>
    @endif

    @if (session('success'))
    <div class="mb-3">
      <div class="alert alert-success py-2" style="font-size:0.9rem;">
        {{ session('success') }}
      </div>
    </div>
    @endif

    <!-- Generation count badge -->
    @if($genCount ?? false)
    <div style="display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #fff8e1, #fff3e0); color: #e65100; padding: 6px 16px; border-radius: 24px; font-size: 0.82rem; font-weight: 700; margin-bottom: 1.2rem; border: 1px solid rgba(230, 81, 0, 0.15); box-shadow: 0 2px 6px rgba(230, 81, 0, 0.08);">
      <i class="bi bi-arrow-repeat"></i> Generated {{ $genCount }} time{{ $genCount != 1 ? 's' : '' }}
    </div>
    @endif

    <!-- Card Image -->
    <div style="background: linear-gradient(135deg, #f5f5f5, #eeeeee); border-radius: 16px; padding: 1rem; margin-bottom: 1.5rem; border: 1px solid #e8e8e8;">
      <img src="{{ $cardUrl }}" alt="Member ID Card — {{ $epicNo }}" 
           style="max-width: 100%; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12); transition: transform 0.3s ease;">
    </div>

    <!-- Voter Details -->
    <div style="background: #fafafa; border-radius: 14px; padding: 0; margin-bottom: 1.5rem; text-align: left; overflow: hidden; border: 1px solid #e8e8e8;">
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.7rem 1.2rem; border-bottom: 1px solid #f0f0f0;">
        <span style="color: #999; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600;">Name</span>
        <span style="font-weight: 700; color: #333; font-size: 0.92rem;">{{ $voter['name'] ?? 'N/A' }}</span>
      </div>
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.7rem 1.2rem; border-bottom: 1px solid #f0f0f0;">
        <span style="color: #999; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600;">EPIC No</span>
        <span style="font-weight: 700; color: #333; font-size: 0.92rem;">{{ $epicNo }}</span>
      </div>
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.7rem 1.2rem; border-bottom: 1px solid #f0f0f0;">
        <span style="color: #999; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600;">Assembly</span>
        <span style="font-weight: 700; color: #333; font-size: 0.92rem;">{{ $voter['assembly_name'] ?? $voter['assembly'] ?? 'N/A' }}</span>
      </div>
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.7rem 1.2rem;">
        <span style="color: #999; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600;">District</span>
        <span style="font-weight: 700; color: #333; font-size: 0.92rem;">{{ $voter['district'] ?? 'N/A' }}</span>
      </div>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; margin-bottom: 1.5rem;">
      <a href="{{ $cardUrl }}" target="_blank" download
         style="background: linear-gradient(135deg, #1565c0 0%, #1976d2 50%, #42a5f5 100%); border: none; color: #fff; font-weight: 700; padding: 0.8rem 2rem; border-radius: 14px; font-size: 1rem; box-shadow: 0 4px 15px rgba(21, 101, 192, 0.3); transition: all 0.3s ease; text-decoration: none; display: inline-block;">
        <i class="bi bi-download me-2"></i>Download Card
      </a>
      <a href="{{ route('home') }}"
         style="border: 2px solid #e0e0e0; color: #555; font-weight: 700; padding: 0.8rem 2rem; border-radius: 14px; font-size: 1rem; background: #fff; transition: all 0.3s ease; text-decoration: none; display: inline-block;">
        <i class="bi bi-arrow-left me-2"></i>Generate Another
      </a>
    </div>

    <!-- Share Section -->
    <div style="background: linear-gradient(135deg, #f8f9fa, #f5f5f5); border-radius: 14px; padding: 1rem 1.2rem; margin-top: 1.5rem; border: 1px solid #e8e8e8;">
      <div style="font-size: 0.78rem; font-weight: 600; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Share your card</div>
      <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
        <a href="https://wa.me/?text=Check%20out%20my%20அகில%20இந்திய%20புரட்சித்%20தலைவர்%20மக்கள்%20முன்னேற்றக்%20கழகம்%20Member%20ID%20Card!" target="_blank"
           style="width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #fff; background: #25D366; border: none; transition: transform 0.2s, box-shadow 0.2s; text-decoration: none;" title="Share on WhatsApp">
          <i class="bi bi-whatsapp"></i>
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php" target="_blank"
           style="width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #fff; background: #1877F2; border: none; transition: transform 0.2s, box-shadow 0.2s; text-decoration: none;" title="Share on Facebook">
          <i class="bi bi-facebook"></i>
        </a>
        <a href="https://twitter.com/intent/tweet?text=I%20just%20got%20my%20அகில%20இந்திய%20புரட்சித்%20தலைவர்%20மக்கள்%20முன்னேற்றக்%20கழகம்%20Member%20ID%20Card!" target="_blank"
           style="width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #fff; background: #1DA1F2; border: none; transition: transform 0.2s, box-shadow 0.2s; text-decoration: none;" title="Share on Twitter">
          <i class="bi bi-twitter-x"></i>
        </a>
        <a href="{{ $cardUrl }}" target="_blank"
           style="width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #fff; background: #555; border: none; transition: transform 0.2s, box-shadow 0.2s; text-decoration: none;" title="Download">
          <i class="bi bi-download"></i>
        </a>
      </div>
    </div>

  </div>
</div>

<!-- Footer Note -->
<div style="text-align: center; padding: 1.2rem; font-size: 0.8rem; color: #aaa;">
  <i class="bi bi-shield-check" style="color: #43a047; margin-right: 3px;"></i> Card generated successfully &bull; Save your card for offline use
</div>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/" target="_blank" title="Chat on WhatsApp"
   style="position: fixed; bottom: 24px; right: 24px; width: 56px; height: 56px; background: #25D366; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 4px 12px rgba(0, 0, 0, .25); z-index: 9999; transition: transform .2s, box-shadow .2s; text-decoration: none;">
  <i class="bi bi-whatsapp"></i>
</a>

@endsection
