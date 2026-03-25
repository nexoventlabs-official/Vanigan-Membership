@extends('layouts.app')

@section('title', 'Your Member ID Card — அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம்')

@section('extra_css')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  body {
    background: #f0f4f8;
    font-family: 'Outfit', sans-serif;
  }

  /* Premium Hero Area */
  .premium-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    position: relative;
    padding: 4rem 1rem 7rem;
    text-align: center;
    color: #fff;
    overflow: hidden;
  }

  .premium-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: 
      radial-gradient(circle at 20% 30%, rgba(56, 189, 248, 0.15) 0%, transparent 40%),
      radial-gradient(circle at 80% 70%, rgba(14, 165, 233, 0.15) 0%, transparent 40%);
    animation: rotateGradient 20s linear infinite;
    pointer-events: none;
  }

  @keyframes rotateGradient {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .success-bubble {
    width: 80px;
    height: 80px;
    background: rgba(16, 185, 129, 0.1);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #10b981;
    margin-bottom: 1.2rem;
    border: 1px solid rgba(16, 185, 129, 0.3);
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
    animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
  }

  @keyframes popIn {
    0% { transform: scale(0); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
  }

  .premium-hero h2 {
    font-weight: 800;
    font-size: 1.8rem;
    letter-spacing: -0.5px;
    margin-bottom: 0.4rem;
    background: linear-gradient(to right, #fff, #cbd5e1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .premium-hero p {
    opacity: 0.8;
    font-size: 1.05rem;
    font-weight: 400;
  }

  /* Main Card Body */
  .premium-state-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
    max-width: 580px;
    width: calc(100% - 24px);
    margin: -4rem auto 3rem;
    position: relative;
    z-index: 10;
    border: 1px solid rgba(255, 255, 255, 0.5);
    padding: 2.5rem 2rem;
  }

  /* The generated card display */
  .id-card-display {
    perspective: 1000px;
    margin-bottom: 2rem;
  }

  .id-card-display img {
    border-radius: 18px;
    width: 100%;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
  }

  .id-card-display:hover img {
    transform: translateY(-5px) rotateX(2deg) rotateY(2deg);
    box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
  }

  /* Info List Elements */
  .detail-group {
    background: #f8fafc;
    border-radius: 16px;
    padding: 1.2rem;
    border: 1px solid #e2e8f0;
    margin-bottom: 2rem;
  }

  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 0;
    border-bottom: 1px dashed #cbd5e1;
  }

  .detail-row:last-child {
    border-bottom: none;
  }

  .detail-label {
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .detail-value {
    color: #0f172a;
    font-size: 1rem;
    font-weight: 700;
    text-align: right;
  }

  /* Action Buttons */
  .action-wrap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .btn-premium {
    padding: 1.1rem;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
  }

  .btn-download {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #fff;
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
  }

  .btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 25px rgba(37, 99, 235, 0.3);
    color: #fff;
  }

  .btn-secondary-act {
    background: #fff;
    color: #334155;
    border: 2px solid #e2e8f0;
  }

  .btn-secondary-act:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    color: #0f172a;
    transform: translateY(-2px);
  }

  /* Social Shares */
  .share-box {
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
  }

  .share-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
  }

  .social-icons {
    display: flex;
    justify-content: center;
    gap: 12px;
  }

  .social-btn {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .social-btn:hover {
    transform: translateY(-3px) scale(1.05);
  }

  .soc-wa { background: linear-gradient(135deg, #25D366, #128C7E); box-shadow: 0 6px 15px rgba(37, 211, 102, 0.3); }
  .soc-fb { background: linear-gradient(135deg, #1877F2, #0d5aab); box-shadow: 0 6px 15px rgba(24, 119, 242, 0.3); }
  .soc-tw { background: linear-gradient(135deg, #1DA1F2, #1a8cd3); box-shadow: 0 6px 15px rgba(29, 161, 242, 0.3); }
  .soc-dl { background: linear-gradient(135deg, #475569, #334155); box-shadow: 0 6px 15px rgba(71, 85, 105, 0.3); }

  @media (max-width: 600px) {
    .premium-state-card {
      padding: 2rem 1.2rem;
    }
    .action-wrap {
      grid-template-columns: 1fr;
    }
  }

  .badge-count {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fffbeb;
    color: #d97706;
    padding: 6px 16px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(245, 158, 11, 0.2);
    box-shadow: 0 2px 10px rgba(245, 158, 11, 0.05);
  }
</style>
@endsection

@section('content')

<!-- Success Hero -->
<div class="premium-hero">
  <div class="success-bubble">
    <i class="bi bi-check-lg"></i>
  </div>
  <h2>Card Generated!</h2>
  <p>Your official Membership ID is ready</p>
</div>

<!-- Result Card -->
<div class="premium-state-card">
  
  @if ($errors->any())
  <div class="mb-4">
    <div class="alert alert-danger" style="border-radius: 12px;">
      @foreach ($errors->all() as $error)
        <div><i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}</div>
      @endforeach
    </div>
  </div>
  @endif

  @if (session('success'))
  <div class="mb-4">
    <div class="alert alert-success" style="border-radius: 12px;">
      <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
  </div>
  @endif

  <!-- Generation count badge -->
  @if($genCount ?? false)
  <div class="text-center">
    <div class="badge-count">
      <i class="bi bi-clock-history"></i> Generated {{ $genCount }} time{{ $genCount != 1 ? 's' : '' }}
    </div>
  </div>
  @endif

  <!-- Card Image -->
  <div class="id-card-display">
    <img src="{{ $cardUrl }}" alt="Member ID Card — {{ $epicNo }}">
  </div>

  <!-- Voter Details -->
  <div class="detail-group">
    <div class="detail-row">
      <div class="detail-label"><i class="bi bi-person text-primary"></i> Name</div>
      <div class="detail-value">{{ $voter['name'] ?? 'N/A' }}</div>
    </div>
    <div class="detail-row">
      <div class="detail-label"><i class="bi bi-card-heading text-primary"></i> EPIC No</div>
      <div class="detail-value">{{ $epicNo }}</div>
    </div>
    <div class="detail-row">
      <div class="detail-label"><i class="bi bi-bank text-primary"></i> Assembly</div>
      <div class="detail-value">{{ $voter['assembly_name'] ?? $voter['assembly'] ?? 'N/A' }}</div>
    </div>
    <div class="detail-row">
      <div class="detail-label"><i class="bi bi-geo-alt text-primary"></i> District</div>
      <div class="detail-value">{{ $voter['district'] ?? 'N/A' }}</div>
    </div>
    @if(!empty($voter['zone']))
    <div class="detail-row">
      <div class="detail-label"><i class="bi bi-globe2 text-primary"></i> Zone</div>
      <div class="detail-value">{{ $voter['zone'] }}</div>
    </div>
    @endif
  </div>

  <!-- Action Buttons -->
  <div class="action-wrap">
    <a href="{{ $cardUrl }}" target="_blank" download class="btn-premium btn-download">
      <i class="bi bi-cloud-download"></i> Save Card
    </a>
    <a href="{{ route('home') }}" class="btn-premium btn-secondary-act">
      <i class="bi bi-plus-circle"></i> Create New
    </a>
  </div>

  <!-- Share Section -->
  <div class="share-box">
    <div class="share-title">Share Your Card</div>
    <div class="social-icons">
      <a href="https://wa.me/?text=Check%20out%20my%20அகில%20இந்திய%20புரட்சித்%20தலைவர்%20மக்கள்%20முன்னேற்றக்%20கழகம்%20Member%20ID%20Card!" target="_blank" class="social-btn soc-wa" title="Share on WhatsApp">
        <i class="bi bi-whatsapp"></i>
      </a>
      <a href="https://www.facebook.com/sharer/sharer.php" target="_blank" class="social-btn soc-fb" title="Share on Facebook">
        <i class="bi bi-facebook"></i>
      </a>
      <a href="https://twitter.com/intent/tweet?text=I%20just%20got%20my%20அகில%20இந்திய%20புரட்சித்%20தலைவர்%20மக்கள்%20முன்னேற்றக்%20கழகம்%20Member%20ID%20Card!" target="_blank" class="social-btn soc-tw" title="Share on Twitter">
        <i class="bi bi-twitter-x"></i>
      </a>
    </div>
  </div>

</div>

<!-- Footer Note -->
<div style="text-align: center; padding: 1.5rem; font-size: 0.85rem; color: #64748b; font-weight: 500;">
  <i class="bi bi-shield-check" style="color: #10b981; margin-right: 4px; font-size: 1rem;"></i> Securly generated via official portal
</div>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/" target="_blank" title="Chat on WhatsApp"
   style="position: fixed; bottom: 24px; right: 24px; width: 60px; height: 60px; background: linear-gradient(135deg, #25D366, #128C7E); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4); z-index: 9999; transition: transform .3s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none;"
   onmouseover="this.style.transform='scale(1.1) translateY(-5px)';" onmouseout="this.style.transform='scale(1) translateY(0)';">
  <i class="bi bi-whatsapp"></i>
</a>

@endsection
