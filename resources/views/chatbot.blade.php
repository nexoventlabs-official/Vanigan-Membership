<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>Tamil Nadu Vanigargalin Sangamam — Digital Member ID Card Generator</title>
  <meta name="description" content="Tamil Nadu Vanigargalin Sangamam Card Generator. Generate your free digital member ID card.">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; overflow: hidden; font-family: 'Inter', sans-serif; background: #efeae2; }

    .chat-app {
      display: flex; flex-direction: column; height: 100dvh; max-height: 100vh;
      position: relative; overflow: hidden;
    }
    .chat-wallpaper {
      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 50%, #a5d6a7 100%);
      opacity: 0.4; z-index: 0;
    }

    /* Header */
    .chat-header {
      background: linear-gradient(135deg, #009345, #009345);
      color: #fff; padding: max(12px, env(safe-area-inset-top)) 16px 12px 16px;
      display: flex; align-items: center; gap: 14px; z-index: 10;
      flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .chat-header .avatar {
      width: 44px; height: 44px; border-radius: 50%;
      background: #fff; display: flex; align-items: center; justify-content: center;
      font-size: 1.5rem; flex-shrink: 0; border: 2px solid #a5d6a7; overflow: hidden;
    }
    .chat-header .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .chat-header .info { flex: 1; }
    .chat-header .info h4 { font-size: 1.1rem; font-weight: 700; margin: 0; color: #fff; }
    .chat-header .info .status { font-size: 0.82rem; font-weight: 500; color: #a5d6a7; margin-top: 2px; }
    .chat-header .actions { display: flex; gap: 16px; color: #fff; font-size: 1.3rem; }

    /* Messages Area */
    .chat-messages {
      flex: 1; overflow-y: auto; padding: 20px 10%; z-index: 1;
      position: relative; display: flex; flex-direction: column;
    }

    /* Date chip */
    .date-chip {
      align-self: center; background: rgba(0,0,0,0.15); color: #fff;
      font-size: 0.75rem; font-weight: 500; padding: 4px 12px;
      border-radius: 12px; margin: 12px 0; letter-spacing: 0.3px;
    }

    /* Message Bubbles */
    .message { margin-bottom: 8px; display: flex; align-items: flex-end; gap: 10px; animation: fadeIn 0.3s ease; max-width: 100%; }
    .bot-avatar-img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
    .user-avatar-svg {
      width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; background: #2e7d32;
      color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
      box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .message.bot { justify-content: flex-start; }
    .message.user { justify-content: flex-end; }
    .message .bubble {
      max-width: 600px; padding: 10px 14px; border-radius: 14px; font-size: 0.95rem;
      line-height: 1.5; position: relative; word-wrap: break-word; color: #000;
      backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.6);
    }
    .message.bot .bubble { background: rgba(255,255,255,0.75); border-bottom-left-radius: 4px; }
    .message.user .bubble { background: rgba(200,230,201,0.8); border-bottom-right-radius: 4px; border-color: rgba(255,255,255,0.4); }
    .bubble .time { font-size: 0.68rem; color: #999; float: right; margin: 8px 0 -4px 12px; }

    /* Banner */
    .banner-message .bubble { padding: 0; overflow: hidden; max-width: 600px; }
    .banner-message .bubble img { width: 100%; display: block; aspect-ratio: 3/1; object-fit: cover; }
    .banner-message .bubble .banner-text { padding: 12px 14px; font-size: 0.95rem; line-height: 1.5; }
    .banner-action { display: flex; padding: 0 14px 14px 14px; }
    .btn-reply {
      background: #f0f4f9; color: #2e7d32; border: 1px solid rgba(46,125,50,0.3);
      border-radius: 12px; padding: 10px 0; font-weight: 600; font-size: 0.95rem;
      cursor: pointer; width: 100%; text-align: center; transition: all 0.2s ease;
    }
    .btn-reply:hover:not([disabled]) { background: #e8f5e9; }
    .btn-reply[disabled] { opacity: 0.6; cursor: not-allowed; }

    /* Typing */
    .typing-indicator .bubble { padding: 12px 18px; }
    .typing-dots { display: flex; gap: 6px; }
    .typing-dots span { width: 8px; height: 8px; border-radius: 50%; background: #b0b4b8; animation: typing 1.4s infinite; }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing { 0%,60%,100% { opacity: 0.4; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-4px); } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

    /* Details Card */
    .voter-details-card { background: #f7f9fa; border-radius: 8px; border: 1px solid #eef0f2; padding: 12px 14px; margin-top: 10px; }
    .voter-details-card .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 0.85rem; border-bottom: 1px solid #e1e5e8; }
    .voter-details-card .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .voter-details-card .detail-label { color: #7f8b94; }
    .voter-details-card .detail-value { font-weight: 600; color: #222; text-align: right; max-width: 65%; }

    /* Action Buttons */
    .action-buttons { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
    .action-btn {
      border: none; padding: 10px 24px; border-radius: 20px; font-size: 0.9rem;
      font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex;
      align-items: center; gap: 6px;
    }
    .action-btn.confirm { background: #2e7d32; color: #fff; box-shadow: 0 2px 6px rgba(46,125,50,0.25); }
    .action-btn.confirm:hover { background: #1b5e20; box-shadow: 0 4px 10px rgba(46,125,50,0.35); }
    .action-btn.cancel { background: #f0f2f5; color: #444; }
    .action-btn.cancel:hover { background: #e0e3e6; }
    .action-btn.skip { background: #ff9800; color: #fff; box-shadow: 0 2px 6px rgba(255,152,0,0.25); }
    .action-btn.skip:hover { background: #f57c00; }

    /* Upload Area */
    .upload-area { margin-top: 10px; border: 2px dashed #2e7d32; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: rgba(46,125,50,0.03); }
    .upload-area:hover { background: rgba(46,125,50,0.08); }
    .upload-area i { font-size: 2.2rem; color: #2e7d32; }
    .upload-area p { margin: 8px 0 0; font-size: 0.85rem; color: #666; }

    /* Card Preview */
    .card-preview { position: relative; max-width: 320px; margin-top: 10px; border-radius: 8px; }
    .card-preview img { width: 100%; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: block; }

    /* Photo preview */
    .photo-thumb { max-width: 200px; border-radius: 8px; margin-bottom: 6px; display: block; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }

    /* Input Area */
    .chat-input-area {
      background: #fff; padding: 12px 10% max(12px, env(safe-area-inset-bottom));
      display: flex; align-items: center; gap: 12px; z-index: 10; flex-shrink: 0;
      box-shadow: 0 -1px 3px rgba(0,0,0,0.03);
    }
    .chat-input-area .attach-btn {
      width: 48px; height: 48px; border: none; background: #f0f2f5; color: #777;
      font-size: 1.4rem; cursor: pointer; border-radius: 50%; display: none;
      align-items: center; justify-content: center; transition: background 0.2s, color 0.2s;
    }
    .chat-input-area .attach-btn:hover { background: #e0e3e6; color: #2e7d32; }
    .chat-input-area .attach-btn.visible { display: flex; }
    .input-wrapper { flex: 1; position: relative; display: flex; align-items: center; }
    .input-wrapper input {
      width: 100%; border: 1px solid #dfe1e5; background: #fff; border-radius: 24px;
      padding: 14px 20px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;
    }
    .input-wrapper input:focus { border-color: #2e7d32; }
    .input-wrapper input::placeholder { color: #999; }
    .chat-input-area .send-btn {
      width: 48px; height: 48px; border: none; background: #2e7d32; color: #fff;
      border-radius: 50%; display: flex; align-items: center; justify-content: center;
      font-size: 1.25rem; cursor: pointer; transition: background 0.2s, transform 0.1s;
      flex-shrink: 0; box-shadow: 0 2px 6px rgba(46,125,50,0.3);
    }
    .chat-input-area .send-btn:hover { background: #1b5e20; transform: scale(1.05); }
    .chat-input-area .send-btn:active { transform: scale(0.95); }
    .chat-input-area .send-btn:disabled { background: #b0c9df; cursor: not-allowed; box-shadow: none; }

    /* Spinner */
    .gen-spinner {
      display: inline-block; width: 18px; height: 18px;
      border: 3px solid rgba(46,125,50,0.3); border-top-color: #2e7d32;
      border-radius: 50%; animation: spin 0.8s linear infinite;
      vertical-align: middle; margin-right: 6px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Hidden inputs */
    #photoInput, #cameraInput { display: none; }
    .photo-upload-btn, .photo-camera-btn { min-width: 140px; justify-content: center; }

    /* Scrollbar */
    .chat-messages::-webkit-scrollbar { width: 6px; }
    .chat-messages::-webkit-scrollbar-track { background: transparent; }
    .chat-messages::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 4px; }

    /* Member Summary Card */
    .member-summary { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-radius: 12px; padding: 16px; margin-top: 10px; border: 1px solid #a5d6a7; }
    .member-summary h4 { color: #1b5e20; margin-bottom: 8px; font-size: 1rem; }
    .member-summary .row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 0.85rem; }
    .member-summary .row .lbl { color: #555; }
    .member-summary .row .val { font-weight: 600; color: #1b5e20; }

    /* Card Preview in Chat */
    .card-preview-wrap { margin-top: 12px; }
    .card-preview-wrap .card-label { font-size: 0.75rem; font-weight: 700; color: #1b5e20; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .card-preview-wrap .card-img { width: 100%; max-width: 320px; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); display: block; margin-bottom: 8px; cursor: pointer; transition: transform 0.2s; }
    .card-preview-wrap .card-img:hover { transform: scale(1.02); }
    .card-actions { display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
    .card-actions .card-action-btn {
      border: none; padding: 8px 16px; border-radius: 20px; font-size: 0.82rem;
      font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
      transition: all 0.2s;
    }
    .card-actions .card-action-btn.primary { background: #2e7d32; color: #fff; box-shadow: 0 2px 6px rgba(46,125,50,0.25); }
    .card-actions .card-action-btn.primary:hover { background: #1b5e20; }
    .card-actions .card-action-btn.secondary { background: #f0f2f5; color: #333; }
    .card-actions .card-action-btn.secondary:hover { background: #e0e3e6; }

    /* Sidebar Overlay */
    .sidebar-overlay {
      position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4);
      z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s;
    }
    .sidebar-overlay.open { opacity: 1; pointer-events: auto; }

    /* Sidebar Panel */
    .sidebar-panel {
      position: fixed; top: 0; right: -360px; width: 340px; max-width: 90vw; height: 100%;
      background: #fff; z-index: 101; transition: right 0.35s cubic-bezier(0.4,0,0.2,1);
      display: flex; flex-direction: column; box-shadow: -4px 0 20px rgba(0,0,0,0.15);
    }
    .sidebar-panel.open { right: 0; }

    .sidebar-header {
      background: linear-gradient(135deg, #009345, #009345); color: #fff;
      padding: 20px 18px; display: flex; align-items: center; gap: 12px; flex-shrink: 0;
    }
    .sidebar-header .close-btn { background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer; margin-left: auto; padding: 4px; }
    .sidebar-header .sb-avatar {
      width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.2);
      display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;
    }
    .sidebar-header .sb-info h4 { margin: 0; font-size: 1.05rem; font-weight: 700; }
    .sidebar-header .sb-info p { margin: 2px 0 0; font-size: 0.78rem; color: #a5d6a7; }

    .sidebar-body { flex: 1; overflow-y: auto; padding: 0; }

    .sb-section { padding: 18px; border-bottom: 1px solid #eee; }
    .sb-section-title { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #888; letter-spacing: 0.8px; margin-bottom: 12px; }

    .sb-menu-item {
      display: flex; align-items: center; gap: 14px; padding: 12px 18px;
      cursor: pointer; transition: background 0.15s; border-bottom: 1px solid #f5f5f5;
    }
    .sb-menu-item:hover { background: #f5f8f5; }
    .sb-menu-item i { font-size: 1.2rem; color: #2e7d32; width: 24px; text-align: center; }
    .sb-menu-item .sb-menu-text { flex: 1; }
    .sb-menu-item .sb-menu-text h5 { margin: 0; font-size: 0.92rem; font-weight: 600; color: #222; }
    .sb-menu-item .sb-menu-text p { margin: 2px 0 0; font-size: 0.78rem; color: #888; }
    .sb-menu-item .sb-menu-arrow { color: #ccc; font-size: 0.9rem; }

    /* Sidebar Card Preview */
    .sb-card-preview { padding: 18px; text-align: center; }
    .sb-card-preview img { width: 100%; max-width: 280px; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); margin-bottom: 10px; }
    .sb-card-preview .sb-card-actions { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; margin-top: 8px; }
    .sb-card-preview .sb-card-actions button {
      border: none; padding: 8px 16px; border-radius: 20px; font-size: 0.82rem;
      font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
    }
    .sb-card-preview .sb-card-actions .dl-btn { background: #2e7d32; color: #fff; }
    .sb-card-preview .sb-card-actions .dl-btn:hover { background: #1b5e20; }
    .sb-card-preview .sb-card-actions .pr-btn { background: #f0f2f5; color: #333; }

    /* 3D Card */
    .card3d-scene {
      width: 200px; height: 280px;
      perspective: 800px;
      margin: 0 auto 12px;
      cursor: grab;
    }
    .card3d-scene:active { cursor: grabbing; }
    .card3d-inner {
      position: relative; width: 100%; height: 100%;
      transform-style: preserve-3d;
      transition: transform 0.8s cubic-bezier(0.4,0,0.2,1);
    }
    .card3d-inner.dragging { transition: none; }
    .card3d-face {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      backface-visibility: hidden;
      border-radius: 12px;
    }
    .card3d-face img {
      width: 100%; height: 100%; object-fit: contain;
      border-radius: 12px; margin: 0;
    }
    .card3d-back {
      transform: rotateY(180deg);
    }
    .card3d-hint {
      font-size: 0.72rem; color: #999; margin-top: 4px;
      display: flex; align-items: center; justify-content: center; gap: 4px;
    }
    .card3d-btn {
      border: none; background: none; cursor: pointer;
      font-size: 1.2rem; color: #2e7d32; padding: 4px 8px;
      border-radius: 50%; transition: background 0.2s;
    }
    .card3d-btn:hover { background: rgba(46,125,50,0.1); }
    .card3d-controls {
      display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 6px;
    }

    /* Profile section in sidebar */
    .sb-profile { padding: 18px; }
    .sb-profile .profile-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 0.88rem; }
    .sb-profile .profile-row:last-child { border-bottom: none; }
    .sb-profile .profile-row .p-label { color: #888; }
    .sb-profile .profile-row .p-value { font-weight: 600; color: #222; text-align: right; max-width: 60%; }

    /* Dark mode sidebar */
    body.dark-mode .sidebar-panel { background: #1a2e1f; }
    body.dark-mode .sb-menu-item:hover { background: rgba(255,255,255,0.05); }
    body.dark-mode .sb-menu-item .sb-menu-text h5 { color: #e9edef; }
    body.dark-mode .sb-menu-item .sb-menu-text p { color: #8696a0; }
    body.dark-mode .sb-section { border-bottom-color: #2a3942; }
    body.dark-mode .sb-menu-item { border-bottom-color: #2a3942; }
    body.dark-mode .sb-profile .profile-row { border-bottom-color: #2a3942; }
    body.dark-mode .sb-profile .profile-row .p-value { color: #e9edef; }

    /* Responsive */
    @media (max-width: 900px) {
      .chat-messages { padding: 15px 5%; }
      .chat-input-area { padding: 10px 5% max(10px, env(safe-area-inset-bottom)); }
    }
    @media (max-width: 600px) {
      .chat-messages { padding: 12px; }
      .chat-input-area { padding: 8px 12px max(8px, env(safe-area-inset-bottom)); gap: 8px; }
      .message .bubble { max-width: 85%; font-size: 0.9rem; }
      .banner-message .bubble { max-width: 95%; }
      .chat-header { padding: max(10px, env(safe-area-inset-top)) 14px 10px 14px; gap: 10px; }
      .chat-header .avatar { width: 40px; height: 40px; }
      .chat-header .info h4 { font-size: 1rem; }
      .chat-input-area .send-btn, .chat-input-area .attach-btn { width: 42px; height: 42px; font-size: 1.15rem; }
      .input-wrapper input { padding: 12px 16px; font-size: 0.9rem; }
      .bot-avatar-img { width: 32px; height: 32px; }
    }

    /* Dark Mode */
    body.dark-mode, body.dark-mode .chat-app, body.dark-mode .chat-wallpaper { background: #0b141a !important; }
    body.dark-mode .chat-header { background: linear-gradient(135deg, #1a3a1a, #2a4a2a) !important; }
    body.dark-mode .message.bot .bubble { background: rgba(32,44,51,0.7) !important; border-color: rgba(255,255,255,0.08) !important; color: #e9edef !important; }
    body.dark-mode .message.user .bubble { background: rgba(30,80,30,0.7) !important; border-color: rgba(255,255,255,0.08) !important; color: #e9edef !important; }
    body.dark-mode .chat-input-area { background: #202c33 !important; }
    body.dark-mode .input-wrapper input { background: #2a3942 !important; border-color: #2a3942 !important; color: #fff !important; }
    body.dark-mode .voter-details-card { background: #202c33 !important; border-color: #2a3942 !important; color: #aebac1 !important; }
    body.dark-mode .voter-details-card .detail-row { border-bottom-color: #444 !important; }
    body.dark-mode .voter-details-card .detail-value { color: #e9edef !important; }
    body.dark-mode .member-summary { background: linear-gradient(135deg, #1a2e1f, #1a3a1a) !important; border-color: #2a4a2a !important; }
    body.dark-mode .member-summary h4 { color: #a5d6a7 !important; }
    body.dark-mode .member-summary .row .val { color: #a5d6a7 !important; }
    body.dark-mode .btn-reply { background: #262626 !important; border-color: #444 !important; color: #4caf50 !important; }
    body.dark-mode .user-avatar-svg { background: #1b5e20; }
    body.dark-mode .bubble .time { color: #aaa !important; }
    body.dark-mode .date-chip { background: rgba(255,255,255,0.15) !important; color: #fff !important; }
    body.dark-mode .typing-dots span { background: #8696a0; }
    body.dark-mode .message.bot .bubble strong { color: #e9edef; }
    body.dark-mode .upload-area { border-color: #4caf50; background: rgba(76,175,80,0.05); }
    body.dark-mode .upload-area p { color: #8696a0; }
  </style>
</head>

<body>

  <div class="chat-app">
    <div class="chat-wallpaper"></div>

    <!-- Header -->
    <div class="chat-header">
      <div class="avatar">
        <img src="/vaniganlogo.png" alt="Vanigam Logo" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
      </div>
      <div class="info">
        <h4>Tamil Nadu Vanigargalin Sangamam</h4>
        <div class="status">Digital Member ID Card</div>
      </div>
      <div class="actions">
        <i class="bi bi-moon-stars" id="themeToggle" style="cursor:pointer;" title="Toggle Theme"></i>
        <i class="bi bi-list" id="menuToggle" style="cursor:pointer;" title="Menu"></i>
      </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar Panel -->
    <div class="sidebar-panel" id="sidebarPanel">
      <div class="sidebar-header">
        <div class="sb-avatar"><i class="bi bi-person-fill"></i></div>
        <div class="sb-info">
          <h4 id="sbName">Vanigam Member</h4>
          <p id="sbMemberId">Not registered</p>
        </div>
        <button class="close-btn" id="sidebarClose"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="sidebar-body" id="sidebarBody">
        <!-- Dynamic content injected by JS -->
      </div>
    </div>

    <!-- Messages -->
    <div class="chat-messages" id="chatMessages"></div>

    <!-- Input -->
    <div class="chat-input-area" id="inputArea">
      <button class="attach-btn" id="attachBtn" title="Upload Photo">
        <i class="bi bi-paperclip"></i>
      </button>
      <div class="input-wrapper">
        <input type="text" id="messageInput" placeholder="Type a message..." autocomplete="off">
      </div>
      <button class="send-btn" id="sendBtn">
        <i class="bi bi-send-fill"></i>
      </button>
    </div>

    <input type="file" id="photoInput" accept="image/png,image/jpeg,image/jpg,image/bmp">
    <input type="file" id="cameraInput" accept="image/png,image/jpeg,image/jpg" capture="environment">
  </div>

  <script>
    (function () {
      /* ────────────────────────────────────────────────────────
         State Machine for Tamil Nadu Vanigargalin Sangamam Chat Flow
         ──────────────────────────────────────────────────────── */
      const S = {
        WELCOME: 0,
        AWAIT_MOBILE: 1,
        AWAIT_OTP: 2,
        AWAIT_EPIC: 3,
        VOTER_CONFIRM: 4,
        AWAIT_PHOTO: 5,
        AWAIT_PIN: 6,
        AWAIT_PIN_CONFIRM: 7,
        ASK_ADDITIONAL: 8,
        AWAIT_DOB: 9,
        AWAIT_BLOOD: 10,
        AWAIT_ADDRESS: 11,
        CONFIRM_ALL: 12,
        GENERATING: 13,
        DONE: 14,
        AWAIT_RETURNING_PIN: 15
      };

      let state = S.WELCOME;
      let mobile = '', epic = '', voter = null, photoFile = null, photoUrl = '';
      let dob = '', bloodGroup = '', address = '', pin = '';
      let skippedDetails = false;
      let isUpdatingDetails = false;

      // Referral tracking from URL params
      const urlParams = new URLSearchParams(window.location.search);
      let referrerUniqueId = urlParams.get('ref') || '';
      let referrerRefId = urlParams.get('ref_id') || '';

      /* ── localStorage ── */
      const LS_KEY = 'vanigam_member';
      function saveUser(data) {
        try {
          const e = JSON.parse(localStorage.getItem(LS_KEY) || '{}');
          localStorage.setItem(LS_KEY, JSON.stringify(Object.assign(e, data)));
        } catch(e) {}
      }
      function getUser() {
        try { return JSON.parse(localStorage.getItem(LS_KEY) || 'null'); } catch(e) { return null; }
      }
      function clearUser() { localStorage.removeItem(LS_KEY); }

      /* ── DOM ── */
      const chatEl = document.getElementById('chatMessages');
      const input = document.getElementById('messageInput');
      const sendBtn = document.getElementById('sendBtn');
      const attachBtn = document.getElementById('attachBtn');
      const photoInput = document.getElementById('photoInput');
      const cameraInput = document.getElementById('cameraInput');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      const sidebarPanel = document.getElementById('sidebarPanel');
      const sidebarBody = document.getElementById('sidebarBody');

      /* ── Sidebar ── */
      function openSidebar() {
        updateSidebarContent();
        sidebarOverlay.classList.add('open');
        sidebarPanel.classList.add('open');
      }
      function closeSidebar() {
        sidebarOverlay.classList.remove('open');
        sidebarPanel.classList.remove('open');
      }
      sidebarOverlay.addEventListener('click', closeSidebar);
      document.getElementById('sidebarClose').addEventListener('click', closeSidebar);
      document.getElementById('menuToggle').addEventListener('click', openSidebar);

      function updateSidebarContent() {
        const user = getUser();
        const sbName = document.getElementById('sbName');
        const sbId = document.getElementById('sbMemberId');
        let html = '';

        if (user && user.hasCard && user.memberData) {
          const m = user.memberData;
          sbName.textContent = m.name || 'Vanigam Member';
          sbId.textContent = m.unique_id || 'Member';

          // Profile Section
          html += '<div class="sb-section"><div class="sb-section-title">Profile</div>';
          html += '<div class="sb-profile">';
          const fields = [
            ['Name', m.name], ['Member ID', m.unique_id], ['EPIC No', m.epic_no],
            ['Mobile', '+91 ' + (user.mobile || m.mobile || '')],
            ['Membership', m.membership || 'Member'],
            ['Assembly', m.assembly], ['District', m.district]
          ];
          if (m.dob) fields.push(['DOB', m.dob]);
          if (m.age) fields.push(['Age', m.age]);
          if (m.blood_group) fields.push(['Blood Group', m.blood_group]);
          if (m.address) fields.push(['Address', m.address]);
          for (const [lbl, val] of fields) {
            if (!val) continue;
            html += '<div class="profile-row"><span class="p-label">' + lbl + '</span><span class="p-value">' + val + '</span></div>';
          }
          html += '</div></div>';

          // 3D Card Preview
          const cardUrl = '/card-view';
          html += '<div class="sb-section">';
          html += '<div class="sb-card-preview">';

          // 3D Card Scene
          html += '<div class="card3d-scene" id="card3dScene">';
          html += '<div class="card3d-inner" id="card3dInner">';

          // Front face
          html += '<div class="card3d-face card3d-front">';
          html += '<div style="position:relative;width:100%;height:100%;background:url(https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232516/vanigan/templates/ID_Front.png) center/contain no-repeat;">';
          if (m.photo_url) html += '<img src="' + m.photo_url + '" style="position:absolute;top:31.8%;left:50%;transform:translateX(-50%);width:32.5%;border-radius:12px;border:2px solid #009245;aspect-ratio:1;object-fit:cover;height:auto;">';
          html += '<div style="position:absolute;top:57%;left:0;right:0;text-align:center;padding:0 8px;">';
          html += '<p style="font-size:0.62rem;font-weight:700;color:#009245;margin:0;">' + (m.name || '') + '</p>';
          html += '<p style="font-size:0.48rem;font-weight:600;margin:2px 0 0;">' + (m.membership || 'Member') + '</p>';
          html += '<p style="font-size:0.44rem;margin:1px 0 0;">' + (m.assembly || '') + '</p>';
          html += '<p style="font-size:0.44rem;margin:1px 0 0;">' + (m.district || '') + '</p>';
          html += '<p style="font-size:0.42rem;margin:2px 0 0;letter-spacing:0.3px;">' + (m.unique_id || '') + '</p>';
          html += '</div></div></div>';

          // Back face
          html += '<div class="card3d-face card3d-back">';
          html += '<div style="position:relative;width:100%;height:100%;background:url(https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png) center/contain no-repeat;">';
          // Back fields
          html += '<div style="position:absolute;top:28%;left:6%;right:6%;font-size:0.38rem;line-height:1.2;display:flex;flex-direction:column;gap:2px;overflow:hidden;">';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:10px;"><span style="font-weight:700;">DATE OF BIRTH</span><span style="font-weight:700;">:</span><span>' + (m.dob || '') + '</span></div>';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:10px;"><span style="font-weight:700;">AGE</span><span style="font-weight:700;">:</span><span>' + (m.age || '') + '</span></div>';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:10px;"><span style="font-weight:700;">BLOOD GROUP</span><span style="font-weight:700;">:</span><span>' + (m.blood_group || '') + '</span></div>';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:30px;"><span style="font-weight:700;">ADDRESS</span><span style="font-weight:700;">:</span><span style="font-size:0.34rem;word-break:break-word;overflow:hidden;">' + (m.address || '') + '</span></div>';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:10px;"><span style="font-weight:700;">CONTACT</span><span style="font-weight:700;">:</span><span>' + (m.contact_number || '') + '</span></div>';
          html += '</div>';
          // QR + Signature
          html += '<div style="position:absolute;bottom:15%;left:5%;right:5%;display:flex;align-items:flex-end;justify-content:space-between;">';
          html += '<div><img src="/api/vanigam/qr/' + m.unique_id + '" style="width:38px;height:35px;border-radius:3px;"></div>';
          html += '<div style="text-align:center;font-size:0.3rem;line-height:1.3;">';
          html += '<img src="/signature.png" style="width:32px;height:auto;margin-bottom:1px;">';
          html += '<p style="margin:0;font-weight:700;font-size:0.32rem;">SENTHIL KUMAR N</p>';
          html += '<p style="margin:0;">Founder & State President</p>';
          html += '<p style="margin:0;">Tamilnadu Vanigargalin Sangamam</p>';
          html += '</div></div>';
          html += '</div></div>';

          html += '</div></div>';

          // Controls
          html += '<div class="card3d-controls">';
          html += '<button class="card3d-btn" onclick="rotate3dCard(-1)" title="Rotate Left"><i class="bi bi-arrow-counterclockwise"></i></button>';
          html += '<span class="card3d-hint"><i class="bi bi-hand-index-thumb"></i> Drag to rotate</span>';
          html += '<button class="card3d-btn" onclick="rotate3dCard(1)" title="Rotate Right"><i class="bi bi-arrow-clockwise"></i></button>';
          html += '</div>';

          // Action buttons
          html += '<div class="sb-card-actions" style="margin-top:12px;">';
          html += '<button class="dl-btn" onclick="window.open(\'' + cardUrl + '\',\'_blank\')"><i class="bi bi-eye"></i> View Card</button>';
          html += '<button class="dl-btn" onclick="window.open(\'' + cardUrl + '\',\'_blank\')"><i class="bi bi-download"></i> Download Card</button>';
          html += '</div></div></div>';
        } else {
          sbName.textContent = 'Vanigam Member';
          sbId.textContent = 'Not registered';
          html += '<div class="sb-section" style="text-align:center;padding:40px 18px;">';
          html += '<i class="bi bi-person-badge" style="font-size:3rem;color:#ccc;"></i>';
          html += '<p style="color:#888;margin-top:12px;font-size:0.9rem;">No membership card yet.<br>Complete the registration to see your profile.</p>';
          html += '</div>';
        }

        // Referral Stats (show at top if member exists)
        if (user && user.memberData && user.memberData.unique_id) {
          const rc = user.memberData.referral_count || 0;
          html += '<div style="display:flex;align-items:center;justify-content:center;gap:10px;padding:14px 18px;background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:12px;margin:12px 16px;">';
          html += '<i class="bi bi-people-fill" style="font-size:1.5rem;color:#2e7d32;"></i>';
          html += '<div style="text-align:center;">';
          html += '<div style="font-size:1.4rem;font-weight:800;color:#1b5e20;">' + rc + '</div>';
          html += '<div style="font-size:0.72rem;color:#555;font-weight:500;">Referrals</div>';
          html += '</div>';
          html += '<div style="width:1px;height:30px;background:#aed581;"></div>';
          html += '<div style="text-align:center;">';
          if (rc >= 25) {
            html += '<i class="bi bi-star-fill" style="font-size:1.2rem;color:#f9a825;"></i>';
            html += '<div style="font-size:0.72rem;color:#f57f17;font-weight:600;">Organizer Ready</div>';
          } else {
            html += '<div style="font-size:0.85rem;font-weight:700;color:#555;">' + (25 - rc) + ' more</div>';
            html += '<div style="font-size:0.68rem;color:#888;">to become Organizer</div>';
          }
          html += '</div></div>';
        }

        // Menu Items
        if (user && user.memberData && !user.memberData.details_completed) {
          html += '<div class="sb-menu-item" onclick="doMenuUpdateDetails()" style="background:rgba(255,152,0,0.08);border:1px solid rgba(255,152,0,0.3);"><i class="bi bi-pencil-square" style="color:#e65100;"></i><div class="sb-menu-text"><h5 style="color:#e65100;">Update Details</h5><p>Complete your membership details</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        }
        html += '<div class="sb-menu-item" onclick="doMenuRefer()"><i class="bi bi-share"></i><div class="sb-menu-text"><h5>Refer a Friend</h5><p>Share your referral link</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        html += '<div class="sb-menu-item" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i><div class="sb-menu-text"><h5>Become an Organizer</h5><p>Need 25+ referrals to qualify</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        html += '<div class="sb-menu-item" onclick="doMenuHelp()"><i class="bi bi-question-circle"></i><div class="sb-menu-text"><h5>Help & Support</h5><p>Get assistance or report issues</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        html += '<div class="sb-menu-item" onclick="doMenuLogout()"><i class="bi bi-box-arrow-right" style="color:#d32f2f;"></i><div class="sb-menu-text"><h5 style="color:#d32f2f;">Logout</h5><p>Sign out and clear session</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';

        sidebarBody.innerHTML = html;
        init3dCard();
      }

      // 3D Card rotation
      let card3dAngle = 0;
      function rotate3dCard(dir) {
        card3dAngle += dir * 180;
        const inner = document.getElementById('card3dInner');
        if (inner) inner.style.transform = 'rotateY(' + card3dAngle + 'deg)';
      }
      function init3dCard() {
        const scene = document.getElementById('card3dScene');
        const inner = document.getElementById('card3dInner');
        if (!scene || !inner) return;
        let dragging = false, startX = 0, startAngle = 0;
        scene.addEventListener('mousedown', function(e) {
          dragging = true; startX = e.clientX; startAngle = card3dAngle;
          inner.classList.add('dragging'); e.preventDefault();
        });
        scene.addEventListener('touchstart', function(e) {
          dragging = true; startX = e.touches[0].clientX; startAngle = card3dAngle;
          inner.classList.add('dragging');
        }, {passive: true});
        document.addEventListener('mousemove', function(e) {
          if (!dragging) return;
          card3dAngle = startAngle + (e.clientX - startX) * 0.8;
          inner.style.transform = 'rotateY(' + card3dAngle + 'deg)';
        });
        document.addEventListener('touchmove', function(e) {
          if (!dragging) return;
          card3dAngle = startAngle + (e.touches[0].clientX - startX) * 0.8;
          inner.style.transform = 'rotateY(' + card3dAngle + 'deg)';
        }, {passive: true});
        document.addEventListener('mouseup', function() {
          if (!dragging) return; dragging = false;
          inner.classList.remove('dragging');
          // Snap to nearest 180deg
          let snap = Math.round(card3dAngle / 180) * 180;
          card3dAngle = snap;
          inner.style.transform = 'rotateY(' + card3dAngle + 'deg)';
        });
        document.addEventListener('touchend', function() {
          if (!dragging) return; dragging = false;
          inner.classList.remove('dragging');
          let snap = Math.round(card3dAngle / 180) * 180;
          card3dAngle = snap;
          inner.style.transform = 'rotateY(' + card3dAngle + 'deg)';
        });
      }

      window.doMenuRefer = async function () {
        closeSidebar();
        const user = getUser();
        if (!user || !user.memberData || !user.memberData.unique_id) {
          await botReply('\u274C Please complete registration first to get your referral link.', 600);
          return;
        }
        try {
          showTyping();
          const res = await api('/api/vanigam/get-referral', { unique_id: user.memberData.unique_id });
          hideTyping();
          if (res.success) {
            const link = res.referral_link;
            let h = '<i class="bi bi-share" style="color:#2e7d32;"></i> <strong>Your Referral Link</strong>';
            h += '<div style="margin-top:10px;padding:12px;background:#f0f9f1;border-radius:10px;border:1px solid #c8e6c9;">';
            h += '<code style="word-break:break-all;font-size:0.85rem;color:#1b5e20;">' + link + '</code>';
            h += '</div>';
            h += '<div style="margin-top:8px;display:flex;gap:8px;">';
            h += '<button class="action-btn confirm" onclick="copyReferral(\'' + link + '\')" style="flex:1;"><i class="bi bi-clipboard"></i> Copy Link</button>';
            h += '<button class="action-btn confirm" onclick="shareReferral(\'' + link + '\')" style="flex:1;"><i class="bi bi-send"></i> Share</button>';
            h += '</div>';
            h += '<div style="margin-top:8px;font-size:0.8rem;color:#666;"><i class="bi bi-people"></i> Referrals: <strong>' + res.referral_count + '</strong> / 25</div>';
            await botReply(h, 800);
          } else {
            console.error('Referral API error:', res);
            await botReply('\u274C Could not generate referral link: ' + (res.message || 'Unknown error'), 600);
          }
        } catch(e) { hideTyping(); console.error('Referral exception:', e); await botReply('\u274C Something went wrong: ' + e.message, 600); }
      };

      window.copyReferral = function (link) {
        navigator.clipboard.writeText(link).then(() => { botMsg('\u2705 Referral link copied!'); });
      };

      window.shareReferral = function (link) {
        if (navigator.share) {
          navigator.share({ title: 'Tamil Nadu Vanigargalin Sangamam', text: 'Join Vanigam and get your free membership card!', url: link });
        } else {
          navigator.clipboard.writeText(link).then(() => { botMsg('\u2705 Referral link copied!'); });
        }
      };

      window.doMenuOrganizer = async function () {
        closeSidebar();
        const user = getUser();
        const rc = (user && user.memberData) ? (user.memberData.referral_count || 0) : 0;
        let h = '<i class="bi bi-briefcase" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>Become an Organizer</strong>';
        h += '<div style="margin-top:10px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;">';
        if (rc >= 25) {
          h += '<div style="text-align:center;">';
          h += '<i class="bi bi-star-fill" style="font-size:2rem;color:#f9a825;"></i>';
          h += '<p style="font-weight:700;color:#1b5e20;margin:8px 0 4px;">Congratulations!</p>';
          h += '<p style="font-size:0.85rem;color:#555;">You have <strong>' + rc + ' referrals</strong> and qualify to become an Organizer. Our team will contact you soon.</p>';
          h += '</div>';
        } else {
          h += '<div style="text-align:center;">';
          h += '<div style="font-size:2rem;font-weight:800;color:#1b5e20;">' + rc + ' / 25</div>';
          h += '<div style="width:100%;height:8px;background:#e0e0e0;border-radius:4px;margin:10px 0;overflow:hidden;">';
          h += '<div style="width:' + Math.min(100, (rc / 25) * 100) + '%;height:100%;background:linear-gradient(90deg,#4caf50,#2e7d32);border-radius:4px;"></div>';
          h += '</div>';
          h += '<p style="font-size:0.85rem;color:#555;">You need <strong>' + (25 - rc) + ' more referrals</strong> to become an Organizer.</p>';
          h += '<p style="font-size:0.8rem;color:#888;margin-top:6px;">Share your referral link to invite more members!</p>';
          h += '</div>';
        }
        h += '</div>';
        await botReply(h, 800);
      };

      window.doMenuHelp = async function () {
        closeSidebar();
        let h = '<i class="bi bi-question-circle" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>Help & Support</strong>';
        h += '<div style="margin-top:10px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;font-size:0.9rem;line-height:1.6;">';
        h += '<p><i class="bi bi-envelope"></i> <strong>Email:</strong> support@vanigan.org</p>';
        h += '<p><i class="bi bi-telephone"></i> <strong>Phone:</strong> +91 9876543210</p>';
        h += '<p><i class="bi bi-globe"></i> <strong>Website:</strong> <a href="https://vanigan.org" target="_blank" style="color:#2e7d32;">vanigan.org</a></p>';
        h += '<hr style="border:none;border-top:1px solid #e0e0e0;margin:10px 0;">';
        h += '<p style="font-size:0.82rem;color:#888;">For any issues with card generation, referrals, or membership, please contact us via the above channels.</p>';
        h += '</div>';
        await botReply(h, 800);
      };

      window.doMenuLogout = function () {
        closeSidebar();
        clearUser();
        state = S.WELCOME;
        mobile = ''; epic = ''; voter = null; photoFile = null; photoUrl = '';
        dob = ''; bloodGroup = ''; address = ''; pin = ''; skippedDetails = false; isUpdatingDetails = false;
        chatEl.innerHTML = '';
        setText('Type a message...');
        hideAttach();
        addDateChip();
        addBanner();
      };

      /* ── Update Details Handlers ── */
      window.doMenuUpdateDetails = async function () {
        closeSidebar();
        const user = getUser();
        if (!user || !user.memberData) return;
        mobile = user.mobile || user.memberData.mobile || '';
        epic = user.memberData.epic_no || '';
        isUpdatingDetails = true;
        skippedDetails = false;
        state = S.AWAIT_DOB;
        lockInput();
        let h = '<i class="bi bi-pencil-square"></i> Let\'s complete your membership details!<br><br>';
        h += '\uD83C\uDF82 Please select your <strong>Date of Birth</strong>:';
        h += '<div style="margin-top:10px;"><input type="date" id="dobPicker" max="' + new Date().toISOString().split('T')[0] + '" style="width:100%;padding:10px 14px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;"></div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitDob()" style="width:100%;"><i class="bi bi-check-lg"></i> Confirm DOB</button></div>';
        await botReply(h, 700);
      };

      window.doUpdateDetailsFromCard = async function () {
        const user = getUser();
        if (!user || !user.memberData) return;
        mobile = user.mobile || user.memberData.mobile || '';
        epic = user.memberData.epic_no || '';
        isUpdatingDetails = true;
        skippedDetails = false;
        state = S.AWAIT_DOB;
        lockInput();
        let h = '<i class="bi bi-pencil-square"></i> Let\'s complete your membership details!<br><br>';
        h += '\uD83C\uDF82 Please select your <strong>Date of Birth</strong>:';
        h += '<div style="margin-top:10px;"><input type="date" id="dobPicker" max="' + new Date().toISOString().split('T')[0] + '" style="width:100%;padding:10px 14px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;"></div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitDob()" style="width:100%;"><i class="bi bi-check-lg"></i> Confirm DOB</button></div>';
        await botReply(h, 700);
      };

      window.doSaveUpdatedDetails = async function () {
        userMsg('<i class="bi bi-check-lg"></i> Save Details');
        lockInput(); showTyping();
        try {
          const res = await api('/api/vanigam/save-details', {
            epic_no: epic,
            dob: dob,
            blood_group: bloodGroup,
            address: address,
          });
          hideTyping();
          if (res.success && res.member) {
            state = S.DONE;
            unlockInput();
            isUpdatingDetails = false;
            saveUser({ mobile, epic, hasCard: true, memberData: res.member });
            let h = '<i class="bi bi-check-circle" style="color:#2e7d32;"></i> <strong>Details updated successfully!</strong>';
            h += buildCardPreviewHtml(res.member);
            await botReply(h, 1200);
            updateSidebarContent();
            // Auto-save updated card images
            const iframe = document.createElement('iframe');
            iframe.style.cssText = 'position:absolute;left:-9999px;width:600px;height:1200px;';
            iframe.src = '/card-view?autosave=1';
            document.body.appendChild(iframe);
          } else {
            unlockInput();
            await botReply('<i class="bi bi-x-circle"></i> ' + (res.message || 'Failed to save details.'), 600);
          }
        } catch(e) {
          hideTyping(); unlockInput();
          await botReply('<i class="bi bi-x-circle"></i> Something went wrong. Please try again.', 600);
        }
      };

      /* ── Helpers ── */
      const now = () => new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      const scroll = () => setTimeout(() => chatEl.scrollTop = chatEl.scrollHeight, 50);

      function addBubble(html, type, extraClass) {
        const div = document.createElement('div');
        div.className = 'message ' + type + (extraClass ? ' ' + extraClass : '');
        if (type === 'bot') {
          const img = document.createElement('div');
          img.className = 'bot-avatar-img';
          img.style.cssText = 'width:38px;height:38px;border-radius:50%;flex-shrink:0;overflow:hidden;';
          img.innerHTML = '<img src="/vaniganlogo.png" alt="Vanigam" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
          div.appendChild(img);
        }
        const b = document.createElement('div');
        b.className = 'bubble';
        b.innerHTML = html + '<span class="time">' + now() + '</span>';
        div.appendChild(b);
        if (type === 'user') {
          const ui = document.createElement('div');
          ui.className = 'user-avatar-svg';
          ui.innerHTML = '<i class="bi bi-person-fill"></i>';
          div.appendChild(ui);
        }
        chatEl.appendChild(div);
        scroll();
      }
      const botMsg = (h, cls) => addBubble(h, 'bot', cls);
      const userMsg = (h, cls) => addBubble(h, 'user', cls);

      function addBanner() {
        const div = document.createElement('div');
        div.className = 'message bot banner-message';
        div.innerHTML =
          '<div style="width:38px;height:38px;border-radius:50%;background:#2e7d32;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;"><i class="bi bi-shop"></i></div>' +
          '<div class="bubble">' +
          '<div class="banner-text"><strong>Welcome to Tamil Nadu Vanigargalin Sangamam!</strong><br>Your Digital Member ID Card Generator<br><br>' +
          '\uD83D\uDC4B Hello! Generate your <strong>Tamil Nadu Vanigargalin Sangamam Card</strong> in minutes.<br><br>' +
          '<em style="color:#667781;font-size:0.85rem;">Tap Start to begin the registration process.</em>' +
          '<span class="time">' + now() + '</span></div>' +
          '<div class="banner-action"><button class="btn-reply" id="bannerStartBtn"><i class="bi bi-play-circle-fill me-1"></i> Start</button></div>' +
          '</div>';
        chatEl.appendChild(div);
        document.getElementById('bannerStartBtn').onclick = function () {
          this.disabled = true;
          this.innerHTML = '<span class="gen-spinner"></span> Starting...';
          input.value = 'Hi';
          handleSend();
        };
        scroll();
      }

      function addDateChip() {
        const d = document.createElement('div');
        d.className = 'date-chip';
        d.textContent = new Date().toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' });
        chatEl.appendChild(d);
      }

      let typingEl = null;
      function showTyping() {
        if (typingEl) return;
        typingEl = document.createElement('div');
        typingEl.className = 'message bot typing-indicator';
        typingEl.innerHTML = '<div style="width:38px;height:38px;border-radius:50%;background:#2e7d32;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;"><i class="bi bi-shop"></i></div><div class="bubble"><div class="typing-dots"><span></span><span></span><span></span></div></div>';
        chatEl.appendChild(typingEl);
        scroll();
      }
      function hideTyping() { if (typingEl) { typingEl.remove(); typingEl = null; } }

      function botReply(html, delay) {
        return new Promise(resolve => {
          showTyping();
          setTimeout(() => { hideTyping(); botMsg(html); resolve(); }, delay || 800);
        });
      }

      function setNumeric(ph) { input.type = 'tel'; input.inputMode = 'numeric'; input.pattern = '[0-9]*'; input.placeholder = ph || ''; }
      function setText(ph) { input.type = 'text'; input.inputMode = 'text'; input.autocapitalize = 'off'; input.removeAttribute('pattern'); input.placeholder = ph || ''; }
      function setEpicInput(ph) { input.type = 'text'; input.inputMode = 'text'; input.autocapitalize = 'characters'; input.removeAttribute('pattern'); input.placeholder = ph || ''; }
      function showAttach() { attachBtn.classList.add('visible'); }
      function hideAttach() { attachBtn.classList.remove('visible'); }
      function lockInput() { input.disabled = true; sendBtn.disabled = true; }
      function unlockInput() { input.disabled = false; sendBtn.disabled = false; input.focus(); }

      /* ── API wrapper ── */
      async function api(url, body, isForm) {
        const opts = { method: 'POST' };
        if (isForm) {
          opts.body = body;
        } else {
          opts.headers = { 'Content-Type': 'application/json' };
          opts.body = JSON.stringify(body);
        }
        const r = await fetch(url, opts);
        return r.json();
      }

      /* ── OTP Resend ── */
      window.doResendOtp = async function () {
        const btn = document.getElementById('resendOtpBtn');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }
        lockInput(); showTyping();
        try {
          const res = await api('/api/vanigam/send-otp', { mobile });
          hideTyping();
          if (res.success) { botMsg('\u2705 OTP Resent!'); startResendTimer(); }
          else { botMsg('\u274C Failed to resend.'); if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Resend OTP'; } }
        } catch(e) { hideTyping(); botMsg('\u274C Error resending.'); if (btn) { btn.disabled = false; btn.style.opacity = '1'; } }
        unlockInput();
      };

      window.doChangeMobile = function () {
        if (window.resendTimer) clearInterval(window.resendTimer);
        const rBtn = document.getElementById('resendOtpBtn');
        const cBtn = document.getElementById('changeMobileBtn');
        if (rBtn) { rBtn.disabled = true; rBtn.removeAttribute('id'); }
        if (cBtn) { cBtn.disabled = true; cBtn.removeAttribute('id'); }
        state = S.AWAIT_MOBILE; mobile = '';
        setNumeric('Enter 10-digit mobile number...');
        botReply('\uD83D\uDCF1 Please enter your <strong>10-digit mobile number</strong> to verify:', 500);
      };

      function startResendTimer() {
        let timeLeft = 30;
        const btn = document.getElementById('resendOtpBtn');
        if (!btn) return;
        btn.disabled = true;
        btn.innerText = 'Resend OTP (' + timeLeft + 's)';
        if (window.resendTimer) clearInterval(window.resendTimer);
        window.resendTimer = setInterval(() => {
          timeLeft--;
          const cur = document.getElementById('resendOtpBtn');
          if (!cur) { clearInterval(window.resendTimer); return; }
          if (timeLeft <= 0) { clearInterval(window.resendTimer); cur.disabled = false; cur.style.opacity = '1'; cur.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Resend OTP'; }
          else { cur.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Resend OTP (' + timeLeft + 's)'; }
        }, 1000);
      }

      /* ── Init ── */
      async function init() {
        addDateChip();
        const saved = getUser();
        if (saved && saved.mobile && saved.hasCard && saved.memberData) {
          mobile = saved.mobile;
          epic = saved.epic || '';
          state = S.DONE;
          setText('Type a message...');
          hideAttach();

          // Fetch latest member data from MongoDB to stay in sync
          let m = saved.memberData;
          if (m.unique_id) {
            try {
              const freshRes = await fetch('/api/vanigam/member/' + m.unique_id).then(r => r.json());
              if (freshRes.success && freshRes.member) {
                m = freshRes.member;
                epic = m.epic_no || epic;
                saveUser({ mobile, epic, hasCard: true, memberData: m });
              }
            } catch(e) { /* use cached data if fetch fails */ }
          }

          let h = '\uD83D\uDC4B <strong>Welcome back!</strong> Your Tamil Nadu Vanigargalin Sangamam card is ready.';
          h += buildCardPreviewHtml(m);
          if (!m.details_completed) {
            h += '<div style="margin-top:12px;padding:12px;background:rgba(255,152,0,0.1);border-radius:10px;border:1px solid rgba(255,152,0,0.3);">';
            h += '<span style="color:#e65100;font-size:0.9rem;"><i class="bi bi-exclamation-triangle"></i> Some details were skipped.</span>';
            h += '<div style="margin-top:8px;">';
            h += '<button class="action-btn confirm" onclick="doUpdateDetailsFromCard()" style="font-size:0.85rem;padding:8px 16px;">';
            h += '<i class="bi bi-pencil-square"></i> Update Details Now';
            h += '</button>';
            h += '</div></div>';
          }
          h += '<div style="margin-top:12px;"><em style="color:#667781;font-size:0.85rem;">Type anything to generate another card, or use the <strong>menu</strong> (<i class="bi bi-list"></i>) for more options.</em></div>';
          botMsg(h);
          updateSidebarContent();
        } else {
          addBanner();
        }
      }

      /* ────────────────────────────────────────────────────────
         MAIN HANDLER — Tamil Nadu Vanigargalin Sangamam Chat Flow
         ──────────────────────────────────────────────────────── */
      async function handleSend() {
        const txt = input.value.trim();
        if (!txt && state !== S.AWAIT_PHOTO) return;
        input.value = '';

        /* ── WELCOME ── */
        if (state === S.WELCOME) {
          userMsg(txt);
          state = S.AWAIT_MOBILE;
          setNumeric('Enter 10-digit mobile number...');
          await botReply('\uD83D\uDCF1 Please enter your <strong>10-digit mobile number</strong> to verify:', 900);

        /* ── AWAIT MOBILE ── */
        } else if (state === S.AWAIT_MOBILE) {
          const m = txt.replace(/\D/g, '');
          if (m.length !== 10 || !/^[6-9]/.test(m)) {
            userMsg(txt);
            await botReply('\u274C Please enter a valid <strong>10-digit mobile number</strong>.', 600);
            return;
          }
          userMsg(m);
          mobile = m;
          lockInput();

          try {
            showTyping();
            // Check if returning user first (also checks self-referral)
            const checkBody = { mobile: m };
            if (referrerUniqueId) checkBody.referrer_unique_id = referrerUniqueId;
            const checkRes = await api('/api/vanigam/check-member', checkBody);
            hideTyping();

            // Self-referral check — same number as referrer
            if (checkRes.success && checkRes.is_self_referral) {
              mobile = '';
              unlockInput();
              let h = '<div style="padding:14px;background:rgba(244,67,54,0.08);border-radius:12px;border:1px solid rgba(244,67,54,0.3);">';
              h += '<i class="bi bi-exclamation-octagon" style="color:#d32f2f;font-size:1.2rem;"></i> <strong style="color:#d32f2f;">Don\'t use your same number for Dummy referrals</strong>';
              h += '<p style="margin-top:6px;font-size:0.85rem;color:#555;">Please use a different mobile number to register.</p>';
              h += '<button class="action-btn confirm" onclick="doChangeMobile()" style="margin-top:10px;font-size:0.85rem;padding:8px 16px;"><i class="bi bi-telephone"></i> Enter Another Number</button>';
              h += '</div>';
              await botReply(h, 800);
              return;
            }

            // Already registered check (during referral flow)
            if (referrerUniqueId && checkRes.success && checkRes.exists) {
              mobile = '';
              unlockInput();
              let h = '<div style="padding:14px;background:rgba(255,152,0,0.08);border-radius:12px;border:1px solid rgba(255,152,0,0.3);">';
              h += '<i class="bi bi-person-check" style="color:#e65100;font-size:1.2rem;"></i> <strong style="color:#e65100;">This number is already registered</strong>';
              h += '<p style="margin-top:6px;font-size:0.85rem;color:#555;">This mobile number already has a membership card.</p>';
              h += '<button class="action-btn confirm" onclick="doChangeMobile()" style="margin-top:10px;font-size:0.85rem;padding:8px 16px;"><i class="bi bi-telephone"></i> Enter Another Number</button>';
              h += '</div>';
              await botReply(h, 800);
              return;
            }

            if (checkRes.success && checkRes.exists && checkRes.has_pin) {
              // Returning user with PIN — skip OTP, ask PIN directly
              state = S.AWAIT_RETURNING_PIN;
              setNumeric('Enter 4-digit PIN...');
              unlockInput();
              let welcomeText = '\uD83D\uDC4B Welcome back' + (checkRes.name ? ', <strong>' + checkRes.name + '</strong>' : '') + '!';
              welcomeText += '<br><br>\uD83D\uDD12 Please enter your <strong>4-digit security PIN</strong> to access your card:';
              await botReply(welcomeText, 800);
            } else {
              // New user or no PIN — send OTP
              showTyping();
              const res = await api('/api/vanigam/send-otp', { mobile: m });
              hideTyping();
              if (res.success) {
                state = S.AWAIT_OTP;
                setNumeric('Enter 6-digit OTP...');
                unlockInput();
                let askText = '\uD83D\uDCDE An <strong>OTP</strong> has been sent to <strong>+91 ' + m + '</strong>.<br><br>Please enter the <strong>6-digit OTP</strong>:';
                askText += '<div class="action-buttons" style="margin-top:12px;flex-direction:column;">';
                askText += '<button class="action-btn confirm" id="resendOtpBtn" onclick="doResendOtp()" disabled style="font-size:0.85rem;padding:8px 16px;opacity:0.6;"><i class="bi bi-arrow-clockwise"></i> Resend OTP (30s)</button>';
                askText += '<button class="action-btn confirm" id="changeMobileBtn" onclick="doChangeMobile()" style="font-size:0.85rem;padding:8px 16px;"><i class="bi bi-telephone"></i> Change Mobile Number</button>';
                askText += '</div>';
                await botReply(askText, 800);
                startResendTimer();
              } else {
                unlockInput();
                await botReply('\u274C ' + (res.message || 'Could not send OTP. Please try again.'), 600);
              }
            }
          } catch (e) { hideTyping(); unlockInput(); await botReply('\u274C Something went wrong. Please try again.', 600); }

        /* ── AWAIT OTP ── */
        } else if (state === S.AWAIT_OTP) {
          const o = txt.replace(/\D/g, '');
          if (o.length !== 6) {
            userMsg(txt);
            await botReply('\u274C Please enter a valid <strong>6-digit OTP</strong>.', 600);
            return;
          }
          userMsg(o);
          lockInput();
          try {
            showTyping();
            const res = await api('/api/vanigam/verify-otp', { mobile, otp: o });
            hideTyping();
            if (res.success) {
              // Disable OTP buttons
              const rBtn = document.getElementById('resendOtpBtn');
              const cBtn = document.getElementById('changeMobileBtn');
              if (rBtn) { rBtn.disabled = true; rBtn.removeAttribute('id'); }
              if (cBtn) { cBtn.disabled = true; cBtn.removeAttribute('id'); }
              if (window.resendTimer) clearInterval(window.resendTimer);

              if (res.has_membership && res.member) {
                // Already has membership
                state = S.DONE;
                setText('Type a message...');
                hideAttach();
                unlockInput();
                epic = res.member.epic_no || '';
                saveUser({ mobile, epic, hasCard: true, memberData: res.member });
                let h = '\u2705 Mobile verified! Your <strong>Tamil Nadu Vanigargalin Sangamam</strong> card was already generated.';
                h += '<div class="member-summary"><h4>\uD83C\uDFAA Vanigam Member</h4>';
                h += '<div class="row"><span class="lbl">Name</span><span class="val">' + (res.member.name || '') + '</span></div>';
                h += '<div class="row"><span class="lbl">Member ID</span><span class="val">' + (res.member.unique_id || '') + '</span></div>';
                h += '</div>';
                if (!res.member.details_completed) {
                  h += '<br><em style="color:#667781;">Your additional details are incomplete. Type anything to update them.</em>';
                }
                await botReply(h, 1000);
              } else {
                // New member — ask EPIC
                state = S.AWAIT_EPIC;
                setEpicInput('Enter EPIC Number...');
                unlockInput();
                await botReply('\u2705 Mobile number verified!<br><br>Please enter your <strong>EPIC Number</strong> (Voter ID):', 800);
              }
            } else {
              unlockInput();
              await botReply('\u274C ' + (res.message || 'Invalid OTP. Please try again.'), 600);
            }
          } catch (e) { hideTyping(); unlockInput(); await botReply('\u274C Verification failed. Please try again.', 600); }

        /* ── AWAIT EPIC ── */
        } else if (state === S.AWAIT_EPIC) {
          const ep = txt.trim().toUpperCase();
          if (!ep || ep.length < 3) return;
          userMsg(ep);
          lockInput();
          try {
            showTyping();
            const res = await api('/api/vanigam/validate-epic', { epic_no: ep });
            hideTyping();
            if (res.success) {
              epic = ep;
              voter = res.voter;
              let h = '\u2705 <strong>Voter Found!</strong><div class="voter-details-card">';
              const fields = [
                ['Name', voter.name || ''],
                ['EPIC No', voter.epic_no || ep],
                ['Assembly', voter.assembly_name || ''],
                ['District', voter.district || '']
              ];
              for (const [lbl, v] of fields) {
                if (!v || !v.trim()) continue;
                h += '<div class="detail-row"><span class="detail-label">' + lbl + '</span><span class="detail-value">' + v.trim() + '</span></div>';
              }
              h += '</div>';
              h += '<br>Is this correct?';
              h += '<div class="action-buttons">';
              h += '<button class="action-btn confirm" onclick="confirmVoter()"><i class="bi bi-check-lg"></i> Yes, Correct</button>';
              h += '<button class="action-btn cancel" onclick="rejectVoter()"><i class="bi bi-x-lg"></i> No, Re-enter</button>';
              h += '</div>';
              state = S.VOTER_CONFIRM;
              unlockInput();
              await botReply(h, 1000);
            } else {
              unlockInput();
              await botReply('\u274C ' + (res.message || 'EPIC Number not found. Please check and try again.'), 600);
            }
          } catch (e) { hideTyping(); unlockInput(); await botReply('\u274C Could not validate. Please try again.', 600); }

        /* ── VOTER CONFIRM ── */
        } else if (state === S.VOTER_CONFIRM) {
          const lo = txt.toLowerCase();
          if (lo === 'yes' || lo === 'y' || lo === 'correct') {
            userMsg(txt);
            await startPhotoUpload();
          } else if (lo === 'no' || lo === 'n') {
            userMsg(txt);
            state = S.AWAIT_EPIC;
            setEpicInput('Enter EPIC Number...');
            await botReply('Okay! Please enter your <strong>EPIC Number</strong> again:', 500);
          } else {
            userMsg(txt);
            await botReply('Please type <strong>Yes</strong> or <strong>No</strong>.', 400);
          }

        /* ── AWAIT PHOTO ── */
        } else if (state === S.AWAIT_PHOTO) {
          // User typed instead of uploading photo
          userMsg(txt);
          await botReply('\uD83D\uDCF7 Please use the buttons below to <strong>upload your photo</strong>.', 500);

        /* ── AWAIT PIN ── */
        } else if (state === S.AWAIT_PIN) {
          const p = txt.replace(/\D/g, '');
          if (p.length !== 4) {
            userMsg(txt);
            await botReply('Please enter exactly <strong>4 digits</strong> for your PIN.', 500);
            return;
          }
          userMsg('\u2022\u2022\u2022\u2022');
          pin = p;
          state = S.AWAIT_PIN_CONFIRM;
          setNumeric('Re-enter PIN to confirm...');
          await botReply('<i class="bi bi-shield-check"></i> Please <strong>re-enter your PIN</strong> to confirm:', 700);

        } else if (state === S.AWAIT_PIN_CONFIRM) {
          const p = txt.replace(/\D/g, '');
          userMsg('\u2022\u2022\u2022\u2022');
          if (p !== pin) {
            state = S.AWAIT_PIN;
            setNumeric('Enter 4-digit PIN...');
            pin = '';
            await botReply('<i class="bi bi-x-circle"></i> PINs do not match. Please set your <strong>4-digit PIN</strong> again:', 600);
            return;
          }
          await botReply('<i class="bi bi-check-circle"></i> PIN set successfully!', 500);
          await askAdditionalDetails();

        /* ── AWAIT RETURNING PIN ── */
        } else if (state === S.AWAIT_RETURNING_PIN) {
          const p = txt.replace(/\D/g, '');
          if (p.length !== 4) {
            userMsg(txt);
            await botReply('Please enter your <strong>4-digit PIN</strong>.', 500);
            return;
          }
          userMsg('\u2022\u2022\u2022\u2022');
          lockInput(); showTyping();
          try {
            const res = await api('/api/vanigam/verify-pin', { mobile, pin: p });
            hideTyping();
            if (res.success && res.member) {
              state = S.DONE;
              unlockInput();
              const m = res.member;
              epic = m.epic_no || '';
              saveUser({ mobile, epic, hasCard: true, memberData: m });
              let h = '\uD83D\uDC4B <strong>Welcome back!</strong> Your Tamil Nadu Vanigargalin Sangamam card is ready.';
              h += buildCardPreviewHtml(m);
              if (!m.details_completed) {
                h += '<div style="margin-top:12px;padding:12px;background:rgba(255,152,0,0.1);border-radius:10px;border:1px solid rgba(255,152,0,0.3);">';
                h += '<span style="color:#e65100;font-size:0.9rem;"><i class="bi bi-exclamation-triangle"></i> Some details were skipped.</span>';
                h += '<div style="margin-top:8px;">';
                h += '<button class="action-btn confirm" onclick="doUpdateDetailsFromCard()" style="font-size:0.85rem;padding:8px 16px;">';
                h += '<i class="bi bi-pencil-square"></i> Update Details Now';
                h += '</button>';
                h += '</div></div>';
              }
              await botReply(h, 1200);
            } else {
              unlockInput();
              await botReply('<i class="bi bi-x-circle"></i> ' + (res.message || 'Invalid PIN.') + ' Please try again.', 600);
            }
          } catch(e) {
            hideTyping(); unlockInput();
            await botReply('Verification failed. Please try again.', 600);
          }

        /* ── ASK ADDITIONAL ── */
        } else if (state === S.ASK_ADDITIONAL) {
          // Handled by buttons
          userMsg(txt);
          const lo = txt.toLowerCase();
          if (lo === 'add' || lo === 'yes') { await startAdditionalDetails(); }
          else if (lo === 'skip') { await skipAdditionalDetails(); }
          else { await botReply('Please tap <strong>Add Details</strong> or <strong>Skip</strong>.', 400); }

        /* ── AWAIT DOB ── */
        } else if (state === S.AWAIT_DOB) {
          // Handled by calendar picker button (submitDob)
          if (!txt) return;
          userMsg(txt);
          await botReply('Please use the <strong>calendar picker</strong> above to select your date of birth.', 400);

        /* ── AWAIT BLOOD ── */
        } else if (state === S.AWAIT_BLOOD) {
          // Handled by blood group buttons (submitBloodGroup)
          if (!txt) return;
          userMsg(txt);
          await botReply('Please tap one of the <strong>blood group buttons</strong> above to select.', 400);

        /* ── AWAIT ADDRESS ── */
        } else if (state === S.AWAIT_ADDRESS) {
          // Handled by address textarea (submitAddress)
          if (!txt) return;
          userMsg(txt);
          await botReply('Please use the <strong>address box</strong> above and tap <strong>Confirm Address</strong>.', 400);

        /* ── CONFIRM ALL ── */
        } else if (state === S.CONFIRM_ALL) {
          const lo = txt.toLowerCase();
          if (lo === 'yes' || lo === 'confirm' || lo === 'y') {
            userMsg(txt);
            await doGenerateCard();
          } else if (lo === 'no' || lo === 'cancel' || lo === 'n') {
            userMsg(txt);
            state = S.AWAIT_EPIC;
            setEpicInput('Enter EPIC Number...');
            hideAttach();
            photoFile = null; photoUrl = ''; dob = ''; bloodGroup = ''; address = ''; skippedDetails = false;
            await botReply('Cancelled. Please enter your <strong>EPIC Number</strong> to start over:', 700);
          } else {
            userMsg(txt);
            await botReply('Please type <strong>Yes</strong> to confirm or <strong>No</strong> to cancel.', 500);
          }

        /* ── DONE ── */
        } else if (state === S.DONE) {
          userMsg(txt);
          state = S.AWAIT_MOBILE;
          setNumeric('Enter 10-digit mobile number...');
          hideAttach();
          photoFile = null; photoUrl = ''; dob = ''; bloodGroup = ''; address = ''; skippedDetails = false;
          await botReply('\uD83D\uDC4B Ready for another card?<br><br>\uD83D\uDCF1 Please enter your <strong>10-digit mobile number</strong>:', 800);
        }
      }

      /* ── Confirm voter details ── */
      window.confirmVoter = async function () {
        userMsg('<i class="bi bi-check-lg"></i> Yes, Correct');
        await startPhotoUpload();
      };
      window.rejectVoter = async function () {
        userMsg('<i class="bi bi-x-lg"></i> No, Re-enter');
        state = S.AWAIT_EPIC;
        setEpicInput('Enter EPIC Number...');
        await botReply('Okay! Please enter your <strong>EPIC Number</strong> again:', 500);
      };

      /* ── Start Photo Upload step ── */
      async function startPhotoUpload() {
        state = S.AWAIT_PHOTO;
        setText('Click the upload button below...');
        showAttach();
        let h = '\uD83D\uDCF7 Now please <strong>upload your photo</strong> using the buttons below.';
        h += '<div class="action-buttons" style="margin-top:10px;">';
        h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> Upload Photo</button>';
        h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> Camera</button>';
        h += '</div>';
        await botReply(h, 800);
      }

      /* ── PIN Setup ── */
      async function askPinSetup() {
        state = S.AWAIT_PIN;
        setNumeric('Enter 4-digit PIN...');
        let h = '<i class="bi bi-shield-lock"></i> Please set a <strong>4-digit PIN</strong> for your membership.';
        h += '<br><br><em style="color:#667781;font-size:0.85rem;">This PIN will be used to verify your identity when accessing your card from another device.</em>';
        await botReply(h, 800);
      }

      /* ── Ask for Additional Details ── */
      async function askAdditionalDetails() {
        state = S.ASK_ADDITIONAL;
        setText('Type "add" or "skip"...');
        let h = '\uD83D\uDCDD Would you like to add <strong>additional details</strong>?<br><br>';
        h += '<em style="color:#667781;font-size:0.85rem;">Adding details (DOB, Blood Group, Address) will complete your membership card. You can skip and fill them later via QR code.</em>';
        h += '<div class="action-buttons" style="margin-top:12px;">';
        h += '<button class="action-btn confirm" onclick="startAdditionalDetails()"><i class="bi bi-plus-circle"></i> Add Details</button>';
        h += '<button class="action-btn skip" onclick="skipAdditionalDetails()"><i class="bi bi-skip-forward"></i> Skip</button>';
        h += '</div>';
        await botReply(h, 900);
      }

      window.startAdditionalDetails = async function () {
        state = S.AWAIT_DOB;
        skippedDetails = false;
        lockInput();
        let h = '\uD83C\uDF82 Please select your <strong>Date of Birth</strong>:';
        h += '<div style="margin-top:10px;"><input type="date" id="dobPicker" max="' + new Date().toISOString().split('T')[0] + '" style="width:100%;padding:10px 14px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;"></div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitDob()" style="width:100%;"><i class="bi bi-check-lg"></i> Confirm DOB</button></div>';
        await botReply(h, 700);
      };

      /* ── Submit DOB from calendar picker ── */
      window.submitDob = async function () {
        const picker = document.getElementById('dobPicker');
        if (!picker || !picker.value) {
          await botReply('\u274C Please select a date.', 400);
          return;
        }
        const parts = picker.value.split('-');
        dob = parts[2] + '/' + parts[1] + '/' + parts[0]; // DD/MM/YYYY
        userMsg(dob);
        // Disable picker after selection
        picker.disabled = true;
        const btn = picker.closest('.bot').querySelector('.action-btn');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.5'; }
        await showBloodGroupPicker();
      };

      /* ── Blood Group Picker ── */
      async function showBloodGroupPicker() {
        state = S.AWAIT_BLOOD;
        const groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        let h = '\uD83E\uDE78 Please select your <strong>Blood Group</strong>:';
        h += '<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px;margin-top:10px;">';
        groups.forEach(g => {
          h += '<button class="action-btn confirm" onclick="submitBloodGroup(\'' + g + '\')" style="padding:10px 0;font-size:0.95rem;border-radius:10px;">' + g + '</button>';
        });
        h += '</div>';
        await botReply(h, 700);
      }

      /* ── Submit Blood Group ── */
      window.submitBloodGroup = async function (bg) {
        bloodGroup = bg;
        userMsg(bg);
        // Disable all blood group buttons
        document.querySelectorAll('.bot:last-child .action-btn').forEach(b => { b.disabled = true; b.style.opacity = '0.5'; });
        await showAddressInput();
      };

      /* ── Address Input with 70 char limit ── */
      async function showAddressInput() {
        state = S.AWAIT_ADDRESS;
        let h = '\uD83C\uDFE0 Please enter your <strong>full address</strong> <span style="color:#888;font-size:0.8rem;">(max 70 characters)</span>:';
        h += '<div style="margin-top:10px;position:relative;">';
        h += '<textarea id="addressInput" maxlength="70" rows="3" placeholder="Enter your address..." style="width:100%;padding:10px 14px;border:2px solid #2e7d32;border-radius:10px;font-size:0.95rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;resize:none;" oninput="updateAddrCount()"></textarea>';
        h += '<div id="addrCount" style="text-align:right;font-size:0.75rem;color:#888;margin-top:2px;">0 / 70</div>';
        h += '</div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitAddress()" style="width:100%;"><i class="bi bi-check-lg"></i> Confirm Address</button></div>';
        await botReply(h, 700);
        setTimeout(() => { const ta = document.getElementById('addressInput'); if (ta) ta.focus(); }, 900);
      }

      /* ── Address char counter ── */
      window.updateAddrCount = function () {
        const ta = document.getElementById('addressInput');
        const counter = document.getElementById('addrCount');
        if (ta && counter) {
          const len = ta.value.length;
          counter.textContent = len + ' / 70';
          counter.style.color = len >= 65 ? '#e65100' : '#888';
        }
      };

      /* ── Submit Address ── */
      window.submitAddress = async function () {
        const ta = document.getElementById('addressInput');
        if (!ta || !ta.value.trim()) {
          await botReply('\u274C Please enter your address.', 400);
          return;
        }
        address = ta.value.trim().substring(0, 70);
        userMsg(address);
        ta.disabled = true;
        const btn = ta.closest('.bot').querySelector('.action-btn');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.5'; }
        await showConfirmation();
      };

      window.skipAdditionalDetails = async function () {
        userMsg('<i class="bi bi-skip-forward"></i> Skip');
        skippedDetails = true;
        dob = ''; bloodGroup = ''; address = '';
        await showConfirmation();
      };

      /* ── Show Confirmation ── */
      async function showConfirmation() {
        state = S.CONFIRM_ALL;
        setText('Type "yes" to confirm...');
        hideAttach();

        if (isUpdatingDetails) {
          // Update details mode
          let h = '\uD83D\uDCCB <strong>Please confirm your updated details:</strong>';
          h += '<div class="member-summary"><h4>\uD83C\uDFAA Update Details</h4>';
          if (dob) h += '<div class="row"><span class="lbl">DOB</span><span class="val">' + dob + '</span></div>';
          if (bloodGroup) h += '<div class="row"><span class="lbl">Blood Group</span><span class="val">' + bloodGroup + '</span></div>';
          if (address) h += '<div class="row"><span class="lbl">Address</span><span class="val">' + address + '</span></div>';
          h += '</div>';
          h += '<br>Save these details?';
          h += '<div class="action-buttons">';
          h += '<button class="action-btn confirm" onclick="doSaveUpdatedDetails()"><i class="bi bi-check-lg"></i> Save Details</button>';
          h += '<button class="action-btn cancel" onclick="doCancelUpdate()"><i class="bi bi-x-lg"></i> Cancel</button>';
          h += '</div>';
          await botReply(h, 1000);
        } else {
          // Normal card generation mode
          let h = '\uD83D\uDCCB <strong>Please confirm your details:</strong>';
          h += '<div class="member-summary"><h4>\uD83C\uDFAA Tamil Nadu Vanigargalin Sangamam</h4>';
          h += '<div class="row"><span class="lbl">Name</span><span class="val">' + (voter ? voter.name : '') + '</span></div>';
          h += '<div class="row"><span class="lbl">EPIC No</span><span class="val">' + epic + '</span></div>';
          h += '<div class="row"><span class="lbl">Assembly</span><span class="val">' + (voter ? (voter.assembly_name || '') : '') + '</span></div>';
          h += '<div class="row"><span class="lbl">District</span><span class="val">' + (voter ? (voter.district || '') : '') + '</span></div>';
          h += '<div class="row"><span class="lbl">Mobile</span><span class="val">+91 ' + mobile + '</span></div>';
          if (dob) h += '<div class="row"><span class="lbl">DOB</span><span class="val">' + dob + '</span></div>';
          if (bloodGroup) h += '<div class="row"><span class="lbl">Blood Group</span><span class="val">' + bloodGroup + '</span></div>';
          if (address) h += '<div class="row"><span class="lbl">Address</span><span class="val">' + address + '</span></div>';
          if (skippedDetails) h += '<div class="row"><span class="lbl">Status</span><span class="val" style="color:#ff9800;">Details Pending</span></div>';
          h += '</div>';
          h += '<br>Ready to generate your <strong>Tamil Nadu Vanigargalin Sangamam Card</strong>?';
          h += '<div class="action-buttons">';
          h += '<button class="action-btn confirm" onclick="doConfirmGenerate()"><i class="bi bi-check-lg"></i> Confirm & Generate</button>';
          h += '<button class="action-btn cancel" onclick="doCancelAll()"><i class="bi bi-x-lg"></i> Cancel</button>';
          h += '</div>';
          await botReply(h, 1000);
        }
      }

      window.doConfirmGenerate = async function () {
        userMsg('\u2705 Confirm & Generate');
        await doGenerateCard();
      };
      window.doCancelUpdate = async function () {
        userMsg('\u274C Cancel');
        state = S.DONE;
        isUpdatingDetails = false;
        await botReply('Update cancelled. You can update your details anytime from the sidebar menu.', 600);
      };
      window.doCancelAll = async function () {
        userMsg('\u274C Cancel');
        state = S.AWAIT_EPIC;
        setEpicInput('Enter EPIC Number...');
        hideAttach();
        photoFile = null; photoUrl = ''; dob = ''; bloodGroup = ''; address = ''; skippedDetails = false;
        await botReply('Cancelled. Please enter your <strong>EPIC Number</strong> to start over:', 700);
      };

      /* ── Reusable Card Preview HTML ── */
      function buildCardPreviewHtml(m) {
        const cardUrl = '/card-view';
        let h = '<div class="card-preview-wrap">';
        h += '<div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px;">';
        // Front Card
        h += '<div style="flex:1;min-width:220px;max-width:380px;">';
        h += '<div class="card-label">Front</div>';
        h += '<div style="position:relative;width:100%;padding-bottom:146%;background:url(https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232516/vanigan/templates/ID_Front.png) center/contain no-repeat;border-radius:10px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.12);cursor:pointer;" onclick="window.open(\'' + cardUrl + '\',\'_blank\')">';
        if (m.photo_url) h += '<img src="' + m.photo_url + '" style="position:absolute;top:31.8%;left:50%;transform:translateX(-50%);width:32.5%;border-radius:16px;border:3px solid #009245;aspect-ratio:1;object-fit:cover;">';
        h += '<div style="position:absolute;top:57%;left:0;right:0;text-align:center;padding:0 12px;">';
        h += '<p style="font-size:1rem;font-weight:700;color:#009245;margin:0;line-height:1.1;">' + (m.name || '') + '</p>';
        h += '<p style="font-size:0.8rem;font-weight:600;margin:3px 0 0;">' + (m.membership || 'Member') + '</p>';
        h += '<p style="font-size:0.75rem;margin:2px 0 0;">' + (m.assembly || '') + '</p>';
        h += '<p style="font-size:0.75rem;margin:1px 0 0;">' + (m.district || '') + '</p>';
        h += '<p style="font-size:0.7rem;margin:3px 0 0;letter-spacing:0.3px;">' + (m.unique_id || '') + '</p>';
        h += '</div></div></div>';
        // Back Card
        h += '<div style="flex:1;min-width:220px;max-width:380px;">';
        h += '<div class="card-label">Back</div>';
        h += '<div style="position:relative;width:100%;padding-bottom:146%;background:url(https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png) center/contain no-repeat;border-radius:10px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.12);cursor:pointer;" onclick="window.open(\'' + cardUrl + '\',\'_blank\')">';
        h += '<div style="position:absolute;top:28%;left:6%;right:6%;font-size:0.65rem;line-height:1.3;display:flex;flex-direction:column;gap:4px;">';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:16px;"><span style="font-weight:700;">DATE OF BIRTH</span><span style="font-weight:700;">:</span><span>' + (m.dob || '') + '</span></div>';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:16px;"><span style="font-weight:700;">AGE</span><span style="font-weight:700;">:</span><span>' + (m.age || '') + '</span></div>';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:16px;"><span style="font-weight:700;">BLOOD GROUP</span><span style="font-weight:700;">:</span><span>' + (m.blood_group || '') + '</span></div>';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:50px;"><span style="font-weight:700;">ADDRESS</span><span style="font-weight:700;">:</span><span style="font-size:0.55rem;word-break:break-word;">' + (m.address || '') + '</span></div>';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:16px;"><span style="font-weight:700;">CONTACT</span><span style="font-weight:700;">:</span><span>' + (m.contact_number || '') + '</span></div>';
        h += '</div>';
        h += '<div style="position:absolute;bottom:15%;left:5%;right:5%;display:flex;align-items:flex-end;justify-content:space-between;">';
        h += '<div><img src="/api/vanigam/qr/' + m.unique_id + '" style="width:60px;height:55px;border-radius:4px;"></div>';
        h += '<div style="text-align:center;font-size:0.48rem;line-height:1.3;">';
        h += '<img src="/signature.png" style="width:55px;height:auto;margin-bottom:1px;">';
        h += '<p style="margin:0;font-weight:700;font-size:0.52rem;">SENTHIL KUMAR N</p>';
        h += '<p style="margin:0;">Founder & State President</p>';
        h += '<p style="margin:0;">Tamilnadu Vanigargalin Sangamam</p>';
        h += '</div></div>';
        h += '</div></div>';
        h += '</div>';
        // Action buttons
        h += '<div class="card-actions">';
        h += '<button class="card-action-btn primary" onclick="window.open(\'/card-view\',\'_blank\')"><i class="bi bi-eye"></i> View Full Card</button>';
        h += '<button class="card-action-btn primary" onclick="window.open(\'/card-view\',\'_blank\')"><i class="bi bi-download"></i> Download</button>';
        h += '<button class="card-action-btn secondary" onclick="doMenuRefer()"><i class="bi bi-share"></i> Share</button>';
        h += '</div>';
        h += '</div>';
        return h;
      }

      /* ── Generate Card ── */
      async function doGenerateCard() {
        state = S.GENERATING;
        lockInput();

        try {
          // Step 1: Upload photo if we have a file but no URL yet
          if (photoFile && !photoUrl) {
            await botReply('\u2B06\uFE0F Uploading your photo...', 400);
            showTyping();
            const fd = new FormData();
            fd.append('photo', photoFile);
            fd.append('epic_no', epic);
            const uploadRes = await api('/api/vanigam/upload-photo', fd, true);
            hideTyping();
            if (!uploadRes.success || !uploadRes.photo_url) {
              state = S.AWAIT_PHOTO;
              showAttach();
              unlockInput();
              let h = '\u274C Photo upload failed. Please try again.';
              h += '<div class="action-buttons" style="margin-top:10px;">';
              h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> Re-upload Photo</button>';
              h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> Camera</button>';
              h += '</div>';
              await botReply(h, 600);
              return;
            }
            photoUrl = uploadRes.photo_url;
          }

          // Step 2: Generate card
          await botReply('\u2699\uFE0F Generating your <strong>Tamil Nadu Vanigargalin Sangamam Card</strong>... Please wait.', 400);
          showTyping();

          const cardData = {
            mobile: mobile,
            epic_no: epic,
            photo_url: photoUrl,
            name: voter ? voter.name : '',
            assembly: voter ? (voter.assembly_name || '') : '',
            district: voter ? (voter.district || '') : '',
            dob: dob,
            blood_group: bloodGroup,
            address: address,
            skipped_details: skippedDetails,
            pin: pin
          };

          const res = await api('/api/vanigam/generate-card', cardData);
          hideTyping();

          if (res.success && res.member) {
            state = S.DONE;
            setText('Type a message...');
            hideAttach();
            unlockInput();

            saveUser({ mobile, epic, hasCard: true, memberData: res.member });

            const m = res.member;

            let h = '\uD83C\uDF89 <strong>Your Tamil Nadu Vanigargalin Sangamam Card has been generated!</strong>';
            h += buildCardPreviewHtml(m);

            if (skippedDetails) {
              h += '<div style="margin-top:12px;padding:12px;background:rgba(255,152,0,0.1);border-radius:10px;border:1px solid rgba(255,152,0,0.3);">';
              h += '<span style="color:#e65100;font-size:0.9rem;"><i class="bi bi-exclamation-triangle"></i> Some details were skipped.</span>';
              h += '<div style="margin-top:8px;">';
              h += '<button class="action-btn confirm" onclick="doUpdateDetailsFromCard()" style="font-size:0.85rem;padding:8px 16px;">';
              h += '<i class="bi bi-pencil-square"></i> Update Details Now';
              h += '</button>';
              h += '</div></div>';
            }

            await botReply(h, 1200);
            photoFile = null;

            // Auto-save card images to Cloudinary in the background
            const iframe = document.createElement('iframe');
            iframe.style.cssText = 'position:absolute;left:-9999px;width:600px;height:1200px;';
            iframe.src = '/card-view?autosave=1';
            document.body.appendChild(iframe);
            setTimeout(() => iframe.remove(), 20000);

            // Increment referral count if came via referral link
            if (referrerUniqueId) {
              try {
                await api('/api/vanigam/increment-referral', { referrer_unique_id: referrerUniqueId });
              } catch(e) { /* silently fail */ }
              referrerUniqueId = '';
              referrerRefId = '';
            }
          } else {
            state = S.CONFIRM_ALL;
            unlockInput();
            await botReply('\u274C ' + (res.message || 'Card generation failed. Please try again.'), 700);
          }
        } catch (e) {
          hideTyping();
          state = S.CONFIRM_ALL;
          unlockInput();
          await botReply('\u274C Something went wrong. Please try again.', 600);
        }
      }

      /* ── Photo Upload / Camera ── */
      const MAX_PHOTO_SIZE = 5 * 1024 * 1024;

      window.triggerPhotoUpload = function () { if (state === S.AWAIT_PHOTO) photoInput.click(); };
      window.triggerCamera = function () { if (state === S.AWAIT_PHOTO) cameraInput.click(); };
      attachBtn.addEventListener('click', () => { if (state === S.AWAIT_PHOTO) photoInput.click(); });

      function handlePhotoFile(file) {
        if (!file) return;
        if (file.size > MAX_PHOTO_SIZE) {
          let h = '\u274C File size exceeds <strong>5 MB</strong>. Please upload a smaller photo.';
          h += '<div class="action-buttons" style="margin-top:10px;">';
          h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> Upload Photo</button>';
          h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> Camera</button>';
          h += '</div>';
          botReply(h, 600);
          return;
        }
        photoFile = file;
        const reader = new FileReader();
        reader.onload = async (ev) => {
          userMsg('<img src="' + ev.target.result + '" class="photo-thumb" alt="Photo"><br>Photo uploaded');
          hideAttach();
          // Now ask to set PIN
          await askPinSetup();
        };
        reader.readAsDataURL(file);
      }

      photoInput.addEventListener('change', (e) => { handlePhotoFile(e.target.files[0]); photoInput.value = ''; });
      cameraInput.addEventListener('change', (e) => { handlePhotoFile(e.target.files[0]); cameraInput.value = ''; });

      /* ── EPIC auto-formatting ── */
      input.addEventListener('input', function () {
        if (state !== S.AWAIT_EPIC) return;
        let val = this.value;
        let prefix = val.slice(0, 3).toUpperCase().replace(/[^A-Z]/g, '');
        let rest = val.slice(3).replace(/[^0-9]/g, '');
        this.value = prefix + rest;
        if (prefix.length >= 3 && this.inputMode !== 'numeric') { this.inputMode = 'numeric'; this.autocapitalize = 'off'; }
        else if (prefix.length < 3 && this.inputMode !== 'text') { this.inputMode = 'text'; this.autocapitalize = 'characters'; }
      });

      /* ── Events ── */
      sendBtn.addEventListener('click', handleSend);
      input.addEventListener('keydown', (e) => { if (e.key === 'Enter') handleSend(); });

      /* ── Theme Toggle ── */
      document.getElementById('themeToggle').addEventListener('click', function () {
        document.body.classList.toggle('dark-mode');
        this.className = document.body.classList.contains('dark-mode') ? 'bi bi-sun-fill' : 'bi bi-moon-stars';
      });

      /* ── Boot ── */
      window.addEventListener('load', init);
    })();
  </script>
</body>

</html>
