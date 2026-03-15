<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம் - Member ID Card')</title>
  <meta name="description" content="Your official அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம் digital membership ID card">
  <meta name="robots" content="noindex, nofollow">
  <link rel="canonical" href="{{ route('home') }}">
  <link rel="icon" type="image/jpeg" href="{{ asset('favicon.ico') }}">
  <link rel="shortcut icon" type="image/jpeg" href="{{ asset('favicon.ico') }}">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <!-- Custom Styles -->
  <style>
    :root {
      --brand: #3390ec;
    }

    body {
      background: #e9ebee;
      min-height: 100vh;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      padding: 0;
    }

    /* Navigation Bar */
    .site-navbar {
      background: #fff;
      border-bottom: 2px solid #e0e0e0;
      padding: 0;
      position: sticky;
      top: 0;
      z-index: 1040;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .site-navbar .nav-link {
      color: #333;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 14px 22px;
      transition: color 0.2s, background 0.2s;
      white-space: nowrap;
    }

    .site-navbar .nav-link:hover,
    .site-navbar .nav-link.active {
      color: #3390ec;
      background: rgba(51, 144, 236, 0.06);
    }

    .site-navbar .nav-link i {
      margin-right: 4px;
    }

    .navbar-toggler {
      border: none;
      padding: 8px 12px;
    }

    .navbar-toggler:focus {
      box-shadow: none;
    }

    /* Hero Section */
    .card-hero {
      background: linear-gradient(135deg, #1565c0 0%, #1976d2 50%, #42a5f5 100%);
      padding: 2.5rem 0 4rem;
      text-align: center;
      color: #fff;
      position: relative;
      overflow: hidden;
    }

    .card-hero::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 70% 30%, rgba(255, 255, 255, 0.08) 0%, transparent 40%);
      pointer-events: none;
    }

    .card-hero .hero-icon {
      width: 72px;
      height: 72px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 0.8rem;
      backdrop-filter: blur(4px);
      border: 2px solid rgba(255, 255, 255, 0.25);
      animation: pop-in 0.5s ease-out;
    }

    @keyframes pop-in {
      0% {
        transform: scale(0.5);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

    .card-hero h2 {
      font-weight: 800;
      font-size: 1.5rem;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
      margin-bottom: 0.3rem;
    }

    .card-hero p {
      opacity: 0.85;
      font-size: 0.92rem;
      margin: 0;
    }

    /* Main Result Card */
    .result-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 40px rgba(0, 0, 0, 0.10), 0 1px 3px rgba(0, 0, 0, 0.06);
      max-width: 560px;
      width: 100%;
      margin: -2.5rem auto 2rem;
      position: relative;
      z-index: 10;
      overflow: hidden;
      border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .result-body {
      padding: 2rem 1.8rem 2.2rem;
    }

    /* Form Input Styles */
    .form-input {
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      font-size: 1rem;
      transition: border-color 0.3s, box-shadow 0.3s;
      font-family: inherit;
      width: 100%;
    }

    .form-input:focus {
      border-color: #3390ec;
      box-shadow: 0 0 0 3px rgba(51, 144, 236, 0.1);
      outline: none;
    }

    /* Button Styles */
    .btn-primary {
      background: linear-gradient(135deg, #1565c0 0%, #1976d2 50%, #42a5f5 100%);
      border: none;
      color: #fff;
      font-weight: 700;
      padding: 0.8rem 2rem;
      border-radius: 14px;
      font-size: 1rem;
      box-shadow: 0 4px 15px rgba(21, 101, 192, 0.3);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      width: 100%;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(21, 101, 192, 0.4);
      color: #fff;
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
      transition: left 0.5s ease;
    }

    .btn-primary:hover::before {
      left: 100%;
    }

    /* Chat Message Styles */
    .chat-message {
      margin-bottom: 1rem;
      animation: slideIn 0.3s ease-out;
    }

    .chat-message.bot {
      text-align: left;
    }

    .chat-message.user {
      text-align: right;
    }

    .chat-bubble {
      display: inline-block;
      max-width: 80%;
      padding: 12px 16px;
      border-radius: 18px;
      font-size: 0.95rem;
      line-height: 1.4;
      word-wrap: break-word;
    }

    .chat-message.bot .chat-bubble {
      background: #f0f0f0;
      color: #333;
      border-bottom-left-radius: 4px;
      text-align: left;
    }

    .chat-message.user .chat-bubble {
      background: #3390ec;
      color: #fff;
      border-bottom-right-radius: 4px;
      text-align: right;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Chat Container */
    .chat-container {
      min-height: 400px;
      max-height: 500px;
      overflow-y: auto;
      background: #fff;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .chat-container::-webkit-scrollbar {
      width: 6px;
    }

    .chat-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .chat-container::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 10px;
    }

    .chat-container::-webkit-scrollbar-thumb:hover {
      background: #999;
    }

    @media (max-width: 768px) {
      .site-navbar .nav-link {
        padding: 10px 18px;
        font-size: 0.9rem;
      }

      .card-hero {
        padding: 2rem 1rem 3.5rem;
      }

      .card-hero h2 {
        font-size: 1.25rem;
      }

      .result-card {
        margin: -2rem 12px 1.5rem;
        border-radius: 16px;
      }

      .result-body {
        padding: 1.4rem 1.2rem 1.6rem;
      }

      .chat-bubble {
        max-width: 90%;
      }

      .form-input {
        font-size: 16px; /* Prevents zoom on iOS */
      }
    }
  </style>

  @yield('extra_css')
</head>

<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg site-navbar">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 me-3" href="{{ route('home') }}">
        <img src="{{ asset('favicon.jpg') }}" alt="Logo" 
             style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid #3390ec;">
        <img src="{{ asset('name-logo.png') }}" alt="அகில இந்திய புரட்சித் தலைவர் மக்கள் முன்னேற்றக் கழகம்" 
             style="height:32px;width:auto;max-width:200px;">
      </a>
      <div class="ms-auto">
        <a href="{{ route('home') }}" class="nav-link"><i class="bi bi-house-door"></i> Home</a>
      </div>
    </div>
  </nav>

  @yield('content')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @yield('extra_js')
</body>

</html>
