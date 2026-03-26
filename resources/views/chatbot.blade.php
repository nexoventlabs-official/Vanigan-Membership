<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

  <title>Tamil Nadu Vanigargalin Sangamam — Digital Member ID Card Generator</title>
  <meta name="description" content="Tamil Nadu Vanigargalin Sangamam Card Generator. Generate your free digital member ID card.">

  <!-- Preconnect for faster font loading -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Cropper.js for photo crop/rotate -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js" defer></script>
  <!-- html2canvas for card image capture -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" defer></script>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ── CSS Custom Properties ── */
    :root {
      --color-primary: #2e7d32;
      --color-primary-dark: #1b5e20;
      --color-primary-light: #4caf50;
      --color-header-start: #007a38;
      --color-header-end: #00a84e;
      --color-accent-green: #a5d6a7;
      --color-green-bg: #e8f5e9;
      --color-green-bg-alt: #c8e6c9;

      --color-bg: #efeae2;
      --color-surface: #fff;
      --color-surface-alt: #f0f2f5;
      --color-surface-hover: #e0e3e6;
      --color-text: #000;
      --color-text-secondary: #666;
      --color-text-muted: #999;
      --color-text-heading: #222;
      --color-border: #dfe1e5;
      --color-border-light: #eef0f2;
      --color-divider: #f5f5f5;

      --color-bubble-bot: rgba(255,255,255,0.75);
      --color-bubble-user: rgba(200,230,201,0.8);
      --color-bubble-border: rgba(255,255,255,0.6);
      --color-bubble-user-border: rgba(255,255,255,0.4);
      --color-bubble-text: #000;
      --color-date-chip-bg: rgba(0,0,0,0.15);
      --color-date-chip-text: #fff;
      --color-time: #999;
      --color-typing-dot: #b0b4b8;

      --color-danger: #d32f2f;
      --color-warning: #ff9800;
      --color-warning-dark: #f57c00;
      --color-disabled-btn: #a5d6a7;

      --color-reply-bg: #f0f4f9;
      --color-reply-text: #2e7d32;
      --color-reply-border: rgba(46,125,50,0.3);

      --color-details-bg: #f7f9fa;
      --color-details-border: #eef0f2;
      --color-details-row-border: #e1e5e8;
      --color-details-label: #7f8b94;
      --color-details-value: #222;

      --color-input-bg: #fff;
      --color-input-area-bg: rgba(255,255,255,0.85);

      --color-sidebar-bg: #fff;
      --color-sidebar-divider: #eee;
      --color-sidebar-menu-hover: #f5f8f5;
      --color-sidebar-menu-border: #f5f5f5;
      --color-sidebar-label: #888;
      --color-sidebar-profile-value: #222;
      --color-sidebar-profile-border: #f0f0f0;

      --shadow-sm: 0 1px 2px rgba(0,0,0,0.08);
      --shadow-md: 0 2px 8px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.06);
      --shadow-lg: 0 4px 16px rgba(0,0,0,0.12), 0 2px 6px rgba(0,0,0,0.06);
      --shadow-btn: 0 2px 6px rgba(46,125,50,0.25);
      --shadow-btn-hover: 0 4px 12px rgba(46,125,50,0.35), 0 2px 4px rgba(46,125,50,0.15);
      --shadow-bubble: 0 4px 15px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.04);
      --shadow-header: 0 2px 8px rgba(0,0,0,0.15), 0 1px 3px rgba(0,0,0,0.08);
      --shadow-sidebar: -4px 0 20px rgba(0,0,0,0.15), -1px 0 6px rgba(0,0,0,0.06);
      --shadow-input: 0 -1px 3px rgba(0,0,0,0.03);
      --shadow-card: 0 4px 16px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.06);
    }

    /* ── Dark Mode Variable Overrides ── */
    body.dark-mode {
      --color-bg: #0b141a;
      --color-surface: #202c33;
      --color-surface-alt: #2a3942;
      --color-surface-hover: rgba(255,255,255,0.05);
      --color-text: #e9edef;
      --color-text-secondary: #8696a0;
      --color-text-muted: #aaa;
      --color-text-heading: #e9edef;
      --color-border: #2a3942;
      --color-border-light: #2a3942;
      --color-divider: #2a3942;

      --color-bubble-bot: rgba(32,44,51,0.7);
      --color-bubble-user: rgba(30,80,30,0.7);
      --color-bubble-border: rgba(255,255,255,0.08);
      --color-bubble-user-border: rgba(255,255,255,0.08);
      --color-bubble-text: #e9edef;
      --color-date-chip-bg: rgba(255,255,255,0.15);
      --color-date-chip-text: #fff;
      --color-time: #aaa;
      --color-typing-dot: #8696a0;

      --color-reply-bg: #262626;
      --color-reply-text: #4caf50;
      --color-reply-border: #444;

      --color-details-bg: #202c33;
      --color-details-border: #2a3942;
      --color-details-row-border: #444;
      --color-details-label: #8696a0;
      --color-details-value: #e9edef;

      --color-input-bg: #2a3942;
      --color-input-area-bg: rgba(32,44,51,0.85);

      --color-header-start: #1a3a1a;
      --color-header-end: #2a4a2a;

      --color-green-bg: #1a2e1f;
      --color-green-bg-alt: #1a3a1a;
      --color-accent-green: #a5d6a7;
      --color-primary-dark: #a5d6a7;

      --color-sidebar-bg: #1a2e1f;
      --color-sidebar-divider: #2a3942;
      --color-sidebar-menu-hover: rgba(255,255,255,0.05);
      --color-sidebar-menu-border: #2a3942;
      --color-sidebar-label: #8696a0;
      --color-sidebar-profile-value: #e9edef;
      --color-sidebar-profile-border: #2a3942;

      --shadow-header: 0 2px 8px rgba(0,0,0,0.3), 0 1px 3px rgba(0,0,0,0.15);
      --shadow-bubble: 0 4px 15px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08);
    }
    body.dark-mode .user-avatar-svg { background: #1b5e20; }
    body.dark-mode .upload-area { border-color: var(--color-primary-light); background: rgba(76,175,80,0.05); }
    body.dark-mode .upload-area p { color: var(--color-text-secondary); }
    body.dark-mode .message.bot .bubble strong { color: var(--color-text); }

    /* ── Reset ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; overflow: hidden; font-family: 'Inter', sans-serif; background: var(--color-bg); -webkit-text-size-adjust: 100%; }

    .chat-app {
      display: flex; flex-direction: column; height: 100vh; height: 100dvh;
      position: relative; overflow: hidden; max-width: 100vw;
    }
    .chat-wallpaper {
      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(135deg, var(--color-green-bg) 0%, var(--color-green-bg-alt) 50%, var(--color-accent-green) 100%);
      opacity: 0.4; z-index: 0;
    }

    /* Header */
    .chat-header {
      background: linear-gradient(135deg, var(--color-header-start), var(--color-header-end));
      color: #fff; padding: max(12px, env(safe-area-inset-top)) 16px 12px 16px;
      display: flex; align-items: center; gap: 14px; z-index: 10;
      flex-shrink: 0; box-shadow: var(--shadow-header);
    }
    .chat-header .avatar {
      width: 44px; height: 44px; border-radius: 50%;
      background: var(--color-surface); display: flex; align-items: center; justify-content: center;
      font-size: 1.5rem; flex-shrink: 0; border: 2px solid var(--color-accent-green); overflow: hidden;
    }
    .chat-header .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .chat-header .info { flex: 1; }
    .chat-header .info h4 { font-size: 1.1rem; font-weight: 700; margin: 0; color: #fff; }
    .chat-header .info .status { font-size: 0.82rem; font-weight: 500; color: var(--color-accent-green); margin-top: 2px; }
    .chat-header .actions { display: flex; gap: 16px; color: #fff; font-size: 1.3rem; }

    /* Messages Area */
    .chat-messages {
      flex: 1; overflow-y: auto; padding: 20px 10%; z-index: 1;
      position: relative; display: flex; flex-direction: column;
      scrollbar-width: thin;
      scrollbar-color: rgba(0,0,0,0.15) transparent;
    }

    /* Date chip */
    .date-chip {
      align-self: center; background: var(--color-date-chip-bg); color: var(--color-date-chip-text);
      font-size: 0.75rem; font-weight: 500; padding: 4px 12px;
      border-radius: 12px; margin: 12px 0; letter-spacing: 0.3px;
    }

    /* Message Bubbles */
    .message { margin-bottom: 8px; display: flex; align-items: flex-end; gap: 10px; animation: slideUp 0.3s ease-out; max-width: 100%; }
    .bot-avatar-img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: var(--shadow-sm); }
    .user-avatar-svg {
      width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; background: var(--color-primary);
      color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
      box-shadow: var(--shadow-sm);
    }
    .message.bot { justify-content: flex-start; }
    .message.user { justify-content: flex-end; }
    .message .bubble {
      max-width: 600px; padding: 10px 14px; border-radius: 14px; font-size: 0.95rem;
      line-height: 1.5; position: relative; word-wrap: break-word; color: var(--color-bubble-text);
      backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
      box-shadow: var(--shadow-bubble); border: 1px solid var(--color-bubble-border);
    }
    .message.bot .bubble { background: var(--color-bubble-bot); border-bottom-left-radius: 4px; }
    .message.user .bubble { background: var(--color-bubble-user); border-bottom-right-radius: 4px; border-color: var(--color-bubble-user-border); }
    .bubble .time { font-size: 0.68rem; color: var(--color-time); float: right; margin: 8px 0 -4px 12px; }

    /* Banner */
    .banner-message .bubble { padding: 0; overflow: hidden; max-width: 600px; }
    .banner-message .bubble img { width: 100%; display: block; aspect-ratio: 3/1; object-fit: cover; }
    .banner-message .bubble .banner-text { padding: 12px 14px; font-size: 0.95rem; line-height: 1.5; }
    .banner-action { display: flex; padding: 0 14px 14px 14px; }
    .btn-reply {
      background: var(--color-reply-bg); color: var(--color-reply-text); border: 1px solid var(--color-reply-border);
      border-radius: 12px; padding: 10px 0; font-weight: 600; font-size: 0.95rem;
      cursor: pointer; width: 100%; text-align: center;
      transition: background 0.2s ease, color 0.2s ease, transform 0.1s ease;
    }
    .btn-reply:hover:not([disabled]) { background: var(--color-green-bg); }
    .btn-reply:active:not([disabled]) { transform: scale(0.97); }
    .btn-reply[disabled] { opacity: 0.6; cursor: not-allowed; }

    /* Typing */
    .typing-indicator .bubble { padding: 12px 18px; }
    .typing-dots { display: flex; gap: 6px; }
    .typing-dots span { width: 8px; height: 8px; border-radius: 50%; background: var(--color-typing-dot); animation: typing 1.4s infinite; }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing { 0%,60%,100% { opacity: 0.4; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-4px); } }
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(12px) scale(0.98); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes successPulse {
      0%   { transform: scale(1); }
      50%  { transform: scale(1.15); }
      100% { transform: scale(1); }
    }
    .success-pulse { animation: successPulse 0.4s ease-out; }

    /* Details Card */
    .voter-details-card { background: var(--color-details-bg); border-radius: 8px; border: 1px solid var(--color-details-border); padding: 12px 14px; margin-top: 10px; }
    .voter-details-card .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 0.85rem; border-bottom: 1px solid var(--color-details-row-border); }
    .voter-details-card .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .voter-details-card .detail-label { color: var(--color-details-label); }
    .voter-details-card .detail-value { font-weight: 600; color: var(--color-details-value); text-align: right; max-width: 65%; }

    /* Action Buttons */
    .action-buttons { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
    .action-btn {
      border: none; padding: 10px 24px; border-radius: 20px; font-size: 0.9rem;
      font-weight: 600; cursor: pointer; display: inline-flex;
      align-items: center; gap: 6px;
      transition: background 0.2s ease, color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
    }
    .action-btn:active { transform: scale(0.97); }
    .action-btn.confirm { background: var(--color-primary); color: #fff; box-shadow: var(--shadow-btn); }
    .action-btn.confirm:hover { background: var(--color-primary-dark); box-shadow: var(--shadow-btn-hover); }
    .action-btn.cancel { background: var(--color-surface-alt); color: #444; }
    .action-btn.cancel:hover { background: var(--color-surface-hover); }
    .action-btn.skip { background: var(--color-warning); color: #fff; box-shadow: 0 2px 6px rgba(255,152,0,0.25); }
    .action-btn.skip:hover { background: var(--color-warning-dark); }

    /* Upload Area */
    .upload-area { margin-top: 10px; border: 2px dashed var(--color-primary); border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: rgba(46,125,50,0.03); }
    .upload-area:hover { background: rgba(46,125,50,0.08); }
    .upload-area i { font-size: 2.2rem; color: var(--color-primary); }
    .upload-area p { margin: 8px 0 0; font-size: 0.85rem; color: var(--color-text-secondary); }

    /* Card Preview */
    .card-preview { position: relative; max-width: 320px; margin-top: 10px; border-radius: 8px; }
    .card-preview img { width: 100%; border-radius: 8px; box-shadow: var(--shadow-card); display: block; }

    /* Photo preview */
    .photo-thumb { max-width: 200px; border-radius: 8px; margin-bottom: 6px; display: block; box-shadow: var(--shadow-sm); }

    /* Input Area */
    .chat-input-area {
      background: var(--color-input-area-bg);
      backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
      border-top: 1px solid rgba(255,255,255,0.3);
      padding: 12px 10% max(12px, env(safe-area-inset-bottom));
      display: flex; align-items: center; gap: 12px; z-index: 10; flex-shrink: 0;
      box-shadow: var(--shadow-input);
    }
    .chat-input-area .attach-btn {
      width: 48px; height: 48px; border: none; background: var(--color-surface-alt); color: #777;
      font-size: 1.4rem; cursor: pointer; border-radius: 50%; display: none;
      align-items: center; justify-content: center;
      transition: background 0.2s ease, color 0.2s ease, transform 0.1s ease;
    }
    .chat-input-area .attach-btn:hover { background: var(--color-surface-hover); color: var(--color-primary); }
    .chat-input-area .attach-btn:active { transform: scale(0.95); }
    .chat-input-area .attach-btn.visible { display: flex; }
    .input-wrapper { flex: 1; position: relative; display: flex; align-items: center; }
    .input-wrapper input {
      width: 100%; border: 1px solid var(--color-border); background: var(--color-input-bg); border-radius: 24px;
      padding: 14px 20px; font-size: 0.95rem; outline: none; color: var(--color-text);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .input-wrapper input:focus { border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(46,125,50,0.12); }
    .input-wrapper input::placeholder { color: var(--color-text-muted); }
    .chat-input-area .send-btn {
      width: 48px; height: 48px; border: none; background: var(--color-primary); color: #fff;
      border-radius: 50%; display: flex; align-items: center; justify-content: center;
      font-size: 1.25rem; cursor: pointer;
      transition: background 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
      flex-shrink: 0; box-shadow: var(--shadow-btn);
    }
    .chat-input-area .send-btn:hover { background: var(--color-primary-dark); transform: scale(1.05); box-shadow: var(--shadow-btn-hover); }
    .chat-input-area .send-btn:active { transform: scale(0.95); }
    .chat-input-area .send-btn:disabled { background: var(--color-disabled-btn); cursor: not-allowed; box-shadow: none; }

    /* Focus-visible outlines */
    .send-btn:focus-visible,
    .attach-btn:focus-visible,
    .btn-reply:focus-visible,
    .action-btn:focus-visible,
    .card-action-btn:focus-visible,
    .card3d-btn:focus-visible {
      outline: 2px solid var(--color-primary);
      outline-offset: 2px;
    }
    .input-wrapper input:focus-visible {
      border-color: var(--color-primary);
      box-shadow: 0 0 0 3px rgba(46,125,50,0.15);
    }

    /* Spinner */
    .gen-spinner {
      display: inline-block; width: 18px; height: 18px;
      border: 3px solid rgba(46,125,50,0.3); border-top-color: var(--color-primary);
      border-radius: 50%; animation: spin 0.8s linear infinite;
      vertical-align: middle; margin-right: 6px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Hidden inputs */
    #photoInput, #cameraInput { display: none; }
    .photo-upload-btn, .photo-camera-btn { min-width: 140px; justify-content: center; }

    /* Webcam Capture Modal (PC) */
    .webcam-modal-overlay {
      display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.85); z-index: 10000;
      align-items: center; justify-content: center; flex-direction: column;
    }
    .webcam-modal-overlay.active { display: flex; }
    .webcam-modal {
      background: #1a1a1a; border-radius: 20px; overflow: hidden;
      width: 95%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .webcam-modal-header {
      padding: 14px 20px; color: #fff; font-weight: 600; font-size: 0.95rem;
      display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #333;
    }
    .webcam-video-wrap {
      position: relative; width: 100%; aspect-ratio: 1; overflow: hidden; background: #000;
    }
    .webcam-video-wrap video {
      width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);
    }
    .webcam-video-wrap canvas { display: none; }
    .webcam-guide {
      position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
      width: 60%; aspect-ratio: 1; border: 3px dashed rgba(255,255,255,0.4);
      border-radius: 50%; pointer-events: none;
    }
    .webcam-actions {
      display: flex; align-items: center; justify-content: center; gap: 16px;
      padding: 16px 20px; background: #111;
    }
    .webcam-capture-btn {
      width: 64px; height: 64px; border-radius: 50%; border: 4px solid #fff;
      background: transparent; cursor: pointer; position: relative; transition: all 0.2s;
    }
    .webcam-capture-btn::after {
      content: ''; position: absolute; top: 4px; left: 4px; right: 4px; bottom: 4px;
      border-radius: 50%; background: #fff; transition: all 0.15s;
    }
    .webcam-capture-btn:hover::after { background: #e0e0e0; }
    .webcam-capture-btn:active::after { transform: scale(0.9); }
    .webcam-cancel-btn {
      padding: 10px 20px; border-radius: 10px; border: none;
      background: rgba(255,255,255,0.15); color: #fff; font-size: 0.85rem;
      font-weight: 600; cursor: pointer; font-family: Inter, sans-serif;
    }
    .webcam-cancel-btn:hover { background: rgba(255,255,255,0.25); }

    /* Photo Crop Modal */
    .crop-modal-overlay {
      position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.85); z-index: 200;
      display: none; flex-direction: column; align-items: center; justify-content: center;
      padding: 16px;
    }
    .crop-modal-overlay.open { display: flex; }
    .crop-modal {
      background: var(--color-surface); border-radius: 16px; width: 100%; max-width: 420px;
      overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.4);
    }
    .crop-modal-header {
      background: linear-gradient(135deg, var(--color-header-start), var(--color-header-end));
      color: #fff; padding: 14px 18px; font-weight: 700; font-size: 0.95rem;
      display: flex; align-items: center; gap: 8px;
    }
    .crop-modal-body { padding: 12px; position: relative; background: #111; }
    .crop-modal-body img { display: block; max-width: 100%; max-height: 55vh; margin: 0 auto; }
    .crop-modal-controls {
      display: flex; justify-content: center; gap: 10px; padding: 12px 16px;
      background: var(--color-surface-alt); border-top: 1px solid var(--color-border);
    }
    .crop-ctrl-btn {
      width: 44px; height: 44px; border: none; border-radius: 50%;
      background: var(--color-surface); color: var(--color-text);
      font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;
      transition: background 0.2s, transform 0.1s; box-shadow: var(--shadow-sm);
    }
    .crop-ctrl-btn:hover { background: var(--color-surface-hover); }
    .crop-ctrl-btn:active { transform: scale(0.93); }
    .crop-modal-actions {
      display: flex; gap: 10px; padding: 14px 16px;
      background: var(--color-surface); border-top: 1px solid var(--color-border);
    }
    .crop-modal-actions button {
      flex: 1; padding: 12px; border: none; border-radius: 12px;
      font-size: 0.92rem; font-weight: 600; cursor: pointer;
      transition: background 0.2s, transform 0.1s;
    }
    .crop-modal-actions button:active { transform: scale(0.97); }
    .crop-modal-actions .crop-cancel { background: var(--color-surface-alt); color: var(--color-text); }
    .crop-modal-actions .crop-confirm { background: var(--color-primary); color: #fff; box-shadow: var(--shadow-btn); }
    .crop-modal-actions .crop-confirm:hover { background: var(--color-primary-dark); }

    /* Scrollbar (WebKit) */
    .chat-messages::-webkit-scrollbar { width: 6px; }
    .chat-messages::-webkit-scrollbar-track { background: transparent; }
    .chat-messages::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 4px; }

    /* Member Summary Card */
    .member-summary { background: linear-gradient(135deg, var(--color-green-bg), var(--color-green-bg-alt)); border-radius: 12px; padding: 16px; margin-top: 10px; border: 1px solid var(--color-accent-green); }
    .member-summary h4 { color: var(--color-primary-dark); margin-bottom: 8px; font-size: 1rem; }
    .member-summary .row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 0.85rem; }
    .member-summary .row .lbl { color: #555; }
    .member-summary .row .val { font-weight: 600; color: var(--color-primary-dark); }

    /* Card Preview in Chat */
    .card-preview-wrap { margin-top: 12px; }
    .card-preview-wrap .card-label { font-size: 0.75rem; font-weight: 700; color: var(--color-primary-dark); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .card-preview-wrap .card-img { width: 100%; max-width: 320px; border-radius: 10px; box-shadow: var(--shadow-card); display: block; margin-bottom: 8px; cursor: pointer; transition: transform 0.2s; }
    .card-preview-wrap .card-img:hover { transform: scale(1.02); }
    .card-actions { display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
    .card-actions .card-action-btn {
      border: none; padding: 8px 16px; border-radius: 20px; font-size: 0.82rem;
      font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
      transition: background 0.2s ease, color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
    }
    .card-actions .card-action-btn:active { transform: scale(0.97); }
    .card-actions .card-action-btn.primary { background: var(--color-primary); color: #fff; box-shadow: var(--shadow-btn); }
    .card-actions .card-action-btn.primary:hover { background: var(--color-primary-dark); box-shadow: var(--shadow-btn-hover); }
    .card-actions .card-action-btn.secondary { background: var(--color-surface-alt); color: #333; }
    .card-actions .card-action-btn.secondary:hover { background: var(--color-surface-hover); }

    /* Sidebar Overlay */
    .sidebar-overlay {
      position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4);
      z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s;
    }
    .sidebar-overlay.open { opacity: 1; pointer-events: auto; }

    /* Sidebar Panel */
    .sidebar-panel {
      position: fixed; top: 0; right: -360px; width: 340px; max-width: 90vw; height: 100%;
      background: var(--color-sidebar-bg); z-index: 101; transition: right 0.35s cubic-bezier(0.4,0,0.2,1);
      display: flex; flex-direction: column; box-shadow: var(--shadow-sidebar);
    }
    .sidebar-panel.open { right: 0; }

    .sidebar-header {
      background: linear-gradient(135deg, var(--color-header-start), var(--color-header-end)); color: #fff;
      padding: 20px 18px; display: flex; align-items: center; gap: 12px; flex-shrink: 0;
    }
    .sidebar-header .close-btn { background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer; margin-left: auto; padding: 4px; }
    .sidebar-header .sb-avatar {
      width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.2);
      display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;
    }
    .sidebar-header .sb-info h4 { margin: 0; font-size: 1.05rem; font-weight: 700; }
    .sidebar-header .sb-info p { margin: 2px 0 0; font-size: 0.78rem; color: var(--color-accent-green); }

    .sidebar-body { flex: 1; overflow-y: auto; padding: 0; }

    .sb-section { padding: 18px; border-bottom: 1px solid var(--color-sidebar-divider); }
    .sb-section-title { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: var(--color-sidebar-label); letter-spacing: 0.8px; margin-bottom: 12px; }

    .sb-menu-item {
      display: flex; align-items: center; gap: 14px; padding: 12px 18px;
      cursor: pointer; transition: background 0.15s; border-bottom: 1px solid var(--color-sidebar-menu-border);
    }
    .sb-menu-item:hover { background: var(--color-sidebar-menu-hover); }
    .sb-menu-item i { font-size: 1.2rem; color: var(--color-primary); width: 24px; text-align: center; }
    .sb-menu-item .sb-menu-text { flex: 1; }
    .sb-menu-item .sb-menu-text h5 { margin: 0; font-size: 0.92rem; font-weight: 600; color: var(--color-text-heading); }
    .sb-menu-item .sb-menu-text p { margin: 2px 0 0; font-size: 0.78rem; color: var(--color-sidebar-label); }
    .sb-menu-item .sb-menu-arrow { color: #ccc; font-size: 0.9rem; }

    /* Sidebar Card Preview */
    .sb-card-preview { padding: 18px; text-align: center; }
    .sb-card-preview img { width: 100%; max-width: 280px; border-radius: 10px; box-shadow: var(--shadow-card); margin-bottom: 10px; }
    .sb-card-preview .sb-card-actions { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; margin-top: 8px; }
    .sb-card-preview .sb-card-actions button {
      border: none; padding: 8px 16px; border-radius: 20px; font-size: 0.82rem;
      font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
      transition: background 0.2s ease, transform 0.1s ease;
    }
    .sb-card-preview .sb-card-actions button:active { transform: scale(0.97); }
    .sb-card-preview .sb-card-actions .dl-btn { background: var(--color-primary); color: #fff; }
    .sb-card-preview .sb-card-actions .dl-btn:hover { background: var(--color-primary-dark); }
    .sb-card-preview .sb-card-actions .pr-btn { background: var(--color-surface-alt); color: #333; }

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
      font-size: 0.72rem; color: var(--color-text-muted); margin-top: 4px;
      display: flex; align-items: center; justify-content: center; gap: 4px;
    }
    .card3d-btn {
      border: none; background: none; cursor: pointer;
      font-size: 1.2rem; color: var(--color-primary); padding: 4px 8px;
      border-radius: 50%; transition: background 0.2s;
    }
    .card3d-btn:hover { background: rgba(46,125,50,0.1); }
    .card3d-controls {
      display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 6px;
    }

    /* Profile section in sidebar */
    .sb-profile { padding: 18px; }
    .sb-profile .profile-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--color-sidebar-profile-border); font-size: 0.88rem; }
    .sb-profile .profile-row:last-child { border-bottom: none; }
    .sb-profile .profile-row .p-label { color: var(--color-sidebar-label); }
    .sb-profile .profile-row .p-value { font-weight: 600; color: var(--color-sidebar-profile-value); text-align: right; max-width: 60%; }

    /* ═══════════════════════════════════════════════════════════════
       RESPONSIVE DESIGN - Full UI/UX Audit & Fix for All Screen Sizes
       Mobile (320px-480px), Tablet (768px-1024px), Laptop (1024px-1440px), Desktop (1440px+)
       ═══════════════════════════════════════════════════════════════ */

    /* Large Desktop (1440px+) */
    @media (min-width: 1440px) {
      .chat-messages { padding: 24px 12%; }
      .chat-input-area { padding: 14px 12% max(14px, env(safe-area-inset-bottom)); }
      .message .bubble { max-width: 650px; }
      .sidebar-panel { width: 380px; }
    }

    /* Laptop (1024px-1440px) */
    @media (min-width: 1024px) and (max-width: 1439px) {
      .chat-messages { padding: 20px 8%; }
      .chat-input-area { padding: 12px 8% max(12px, env(safe-area-inset-bottom)); }
      .message .bubble { max-width: 580px; }
    }

    /* Tablet (768px-1024px) */
    @media (min-width: 768px) and (max-width: 1023px) {
      .chat-messages { padding: 18px 5%; }
      .chat-input-area { padding: 12px 5% max(12px, env(safe-area-inset-bottom)); }
      .message .bubble { max-width: 520px; font-size: 0.95rem; }
      .sidebar-panel { width: 340px; }
      .card3d-scene { width: 180px; height: 252px; }
    }

    /* Small Tablet / Large Mobile (600px-768px) */
    @media (min-width: 600px) and (max-width: 767px) {
      .chat-messages { padding: 15px 4%; }
      .chat-input-area { padding: 10px 4% max(10px, env(safe-area-inset-bottom)); }
      .message .bubble { max-width: 90%; }
      .sidebar-panel { width: 320px; }
    }

    /* Mobile (480px-600px) */
    @media (min-width: 480px) and (max-width: 599px) {
      .chat-messages { padding: 12px 10px; }
      .chat-input-area { padding: 10px 12px max(10px, env(safe-area-inset-bottom)); gap: 10px; }
      .message .bubble { max-width: 88%; font-size: 0.9rem; padding: 10px 14px; }
      .banner-message .bubble { max-width: 95%; }
      .chat-header { padding: max(12px, env(safe-area-inset-top)) 14px 12px 14px; gap: 12px; }
      .chat-header .avatar { width: 40px; height: 40px; }
      .chat-header .info h4 { font-size: 0.95rem; }
      .chat-header .info .status { font-size: 0.78rem; }
      .chat-input-area .send-btn, .chat-input-area .attach-btn {
        width: 44px; height: 44px; font-size: 1.2rem; /* 44px minimum touch target */
      }
      .input-wrapper input { padding: 12px 16px; font-size: 0.92rem; min-height: 44px; }
      .action-btn { padding: 10px 18px; font-size: 0.85rem; min-height: 44px; }
      .sidebar-panel { width: 300px; max-width: 85vw; }
    }

    /* Small Mobile (320px-480px) */
    @media (max-width: 479px) {
      .chat-messages { padding: 10px 8px; }
      .chat-input-area { padding: 8px 10px max(8px, env(safe-area-inset-bottom)); gap: 8px; }
      .message .bubble { max-width: 92%; font-size: 0.88rem; padding: 8px 12px; }
      .banner-message .bubble { max-width: 98%; }
      .chat-header { padding: max(10px, env(safe-area-inset-top)) 10px 10px 10px; gap: 10px; }
      .chat-header .avatar { width: 36px; height: 36px; }
      .chat-header .info h4 { font-size: 0.88rem; line-height: 1.2; }
      .chat-header .info .status { font-size: 0.72rem; }
      .chat-header .actions { font-size: 1.2rem; }
      .chat-input-area .send-btn, .chat-input-area .attach-btn {
        width: 44px; height: 44px; font-size: 1.15rem; /* 44px minimum touch target */
        flex-shrink: 0;
      }
      .input-wrapper input {
        padding: 10px 14px;
        font-size: 16px; /* Prevents iOS zoom on focus */
        min-height: 44px;
      }
      .bot-avatar-img { width: 28px; height: 28px; }
      .user-avatar-svg { width: 28px; height: 28px; font-size: 0.9rem; }
      .card-preview-wrap .card-img { max-width: 100%; }
      .action-buttons { gap: 6px; flex-wrap: wrap; }
      .action-btn {
        padding: 10px 14px;
        font-size: 0.82rem;
        min-height: 44px; /* Touch target */
        flex: 1 1 calc(50% - 3px);
        justify-content: center;
      }
      .voter-details-card { padding: 10px 12px; }
      .voter-details-card .detail-row { font-size: 0.82rem; }
      .member-summary { padding: 14px; }
      .member-summary .row { font-size: 0.83rem; }
      .sidebar-panel { width: 300px; max-width: 90vw; }
      .sb-menu-item { padding: 14px 16px; gap: 12px; }
      .sb-menu-item i { font-size: 1.15rem; }
      .upload-area { padding: 16px; }
      .upload-area i { font-size: 1.8rem; }
      .upload-area p { font-size: 0.82rem; }
      /* Card preview adjustments for small screens */
      .card-preview-wrap > div > div { min-width: 100% !important; max-width: 100% !important; }
    }

    /* Extra Small Mobile (320px-380px) */
    @media (max-width: 380px) {
      .chat-messages { padding: 8px 6px; }
      .chat-input-area { padding: 6px 8px max(6px, env(safe-area-inset-bottom)); gap: 6px; }
      .message .bubble { max-width: 94%; font-size: 0.85rem; padding: 8px 10px; }
      .chat-header .info h4 { font-size: 0.82rem; }
      .chat-header .info .status { font-size: 0.68rem; }
      .chat-header { gap: 8px; }
      .input-wrapper input { padding: 10px 12px; font-size: 16px; }
      .action-btn { padding: 10px 12px; font-size: 0.8rem; }
      .btn-reply { padding: 12px 8px; font-size: 0.9rem; }
      .date-chip { font-size: 0.7rem; padding: 3px 10px; }
      .sidebar-panel { width: 280px; max-width: 92vw; }
      .card3d-scene { width: 160px; height: 224px; }
    }

    /* Ensure no horizontal overflow at any breakpoint */
    html, body, .chat-app {
      overflow-x: hidden;
      max-width: 100vw;
    }
    .message .bubble {
      overflow-wrap: break-word;
      word-wrap: break-word;
      hyphens: auto;
    }

    /* Fix z-index stacking for sidebar and overlays */
    .chat-wallpaper { z-index: 0; }
    .chat-messages { z-index: 1; }
    .chat-header { z-index: 10; }
    .chat-input-area { z-index: 10; }
    .sidebar-overlay { z-index: 100; }
    .sidebar-panel { z-index: 101; }

    /* Safe area support for notched devices */
    @supports (padding: max(0px)) {
      .chat-header {
        padding-top: max(12px, env(safe-area-inset-top));
        padding-left: max(16px, env(safe-area-inset-left));
        padding-right: max(16px, env(safe-area-inset-right));
      }
      .chat-input-area {
        padding-bottom: max(12px, env(safe-area-inset-bottom));
        padding-left: max(10px, env(safe-area-inset-left));
        padding-right: max(10px, env(safe-area-inset-right));
      }
      .sidebar-panel {
        padding-top: max(0px, env(safe-area-inset-top));
      }
    }

    /* ID Card - Always English, must not break on smaller devices */
    .card-preview-wrap {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    .card-preview-wrap > div {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }
    @media (max-width: 500px) {
      .card-preview-wrap > div {
        flex-direction: column;
      }
      .card-preview-wrap > div > div {
        width: 100%;
        min-width: 100%;
      }
    }

    /* Auto dark mode from OS preference */
    @media (prefers-color-scheme: dark) {
      :root:not(:has(body:not(.dark-mode))) {
        --color-bg: #0b141a;
        --color-surface: #202c33;
        --color-surface-alt: #2a3942;
        --color-surface-hover: rgba(255,255,255,0.05);
        --color-text: #e9edef;
        --color-text-secondary: #8696a0;
        --color-text-muted: #aaa;
        --color-text-heading: #e9edef;
        --color-border: #2a3942;
        --color-border-light: #2a3942;
        --color-divider: #2a3942;
        --color-bubble-bot: rgba(32,44,51,0.7);
        --color-bubble-user: rgba(30,80,30,0.7);
        --color-bubble-border: rgba(255,255,255,0.08);
        --color-bubble-user-border: rgba(255,255,255,0.08);
        --color-bubble-text: #e9edef;
        --color-date-chip-bg: rgba(255,255,255,0.15);
        --color-time: #aaa;
        --color-typing-dot: #8696a0;
        --color-reply-bg: #262626;
        --color-reply-text: #4caf50;
        --color-reply-border: #444;
        --color-details-bg: #202c33;
        --color-details-border: #2a3942;
        --color-details-row-border: #444;
        --color-details-label: #8696a0;
        --color-details-value: #e9edef;
        --color-input-bg: #2a3942;
        --color-input-area-bg: rgba(32,44,51,0.85);
        --color-header-start: #1a3a1a;
        --color-header-end: #2a4a2a;
        --color-green-bg: #1a2e1f;
        --color-green-bg-alt: #1a3a1a;
        --color-accent-green: #a5d6a7;
        --color-primary-dark: #a5d6a7;
        --color-sidebar-bg: #1a2e1f;
        --color-sidebar-divider: #2a3942;
        --color-sidebar-menu-hover: rgba(255,255,255,0.05);
        --color-sidebar-menu-border: #2a3942;
        --color-sidebar-label: #8696a0;
        --color-sidebar-profile-value: #e9edef;
        --color-sidebar-profile-border: #2a3942;
        --shadow-header: 0 2px 8px rgba(0,0,0,0.3), 0 1px 3px rgba(0,0,0,0.15);
        --shadow-bubble: 0 4px 15px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08);
      }
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
      }
      .message { animation: none; }
      .typing-dots span { animation: none; opacity: 0.7; }
      .card3d-inner { transition: none; }
    }
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

  <!-- Photo Crop/Rotate Modal -->
  <div class="crop-modal-overlay" id="cropModalOverlay">
    <div class="crop-modal">
      <div class="crop-modal-header">
        <i class="bi bi-crop"></i> Crop & Adjust Photo
      </div>
      <div class="crop-modal-body">
        <img id="cropImage" src="" alt="Crop preview">
      </div>
      <div class="crop-modal-controls">
        <button class="crop-ctrl-btn" onclick="cropRotate(-90)" title="Rotate Left"><i class="bi bi-arrow-counterclockwise"></i></button>
        <button class="crop-ctrl-btn" onclick="cropRotate(90)" title="Rotate Right"><i class="bi bi-arrow-clockwise"></i></button>
        <button class="crop-ctrl-btn" onclick="cropFlipH()" title="Flip Horizontal"><i class="bi bi-symmetry-vertical"></i></button>
        <button class="crop-ctrl-btn" onclick="cropZoomIn()" title="Zoom In"><i class="bi bi-zoom-in"></i></button>
        <button class="crop-ctrl-btn" onclick="cropZoomOut()" title="Zoom Out"><i class="bi bi-zoom-out"></i></button>
        <button class="crop-ctrl-btn" onclick="cropReset()" title="Reset"><i class="bi bi-arrow-repeat"></i></button>
      </div>
      <div class="crop-modal-actions">
        <button class="crop-cancel" onclick="cropCancel()"><i class="bi bi-x-lg"></i> Cancel</button>
        <button class="crop-confirm" onclick="cropConfirm()"><i class="bi bi-check-lg"></i> Use Photo</button>
      </div>
    </div>
  </div>

  <!-- Webcam Capture Modal (PC) -->
  <div class="webcam-modal-overlay" id="webcamModalOverlay">
    <div class="webcam-modal">
      <div class="webcam-modal-header"><i class="bi bi-camera-video-fill"></i> Take a Photo</div>
      <div class="webcam-video-wrap">
        <video id="webcamVideo" autoplay playsinline muted></video>
        <canvas id="webcamCanvas"></canvas>
        <div class="webcam-guide"></div>
      </div>
      <div class="webcam-actions">
        <button class="webcam-cancel-btn" onclick="closeWebcam()"><i class="bi bi-x-lg"></i> Cancel</button>
        <button class="webcam-capture-btn" onclick="captureWebcam()" title="Capture"></button>
      </div>
    </div>
  </div>

  <script>
    // Global helpers (needed by captureAndUploadCard in separate script tag)
    function toTitleCase(str) {
      if (!str) return '';
      return str.toLowerCase().replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
    }
    var JS_ZONE_DATA = @php echo json_encode(config('zone_data.assembly_map'), JSON_UNESCAPED_UNICODE); @endphp;
    function lookupDistrictZone(assemblyName) {
      if (!assemblyName || !JS_ZONE_DATA) return { district: '', zone: '' };
      var key = assemblyName.trim().toUpperCase().replace(/\s+/g, ' ');
      var matched = JS_ZONE_DATA[key] || null;
      if (!matched) {
        var norm = key.replace(/[\. \-\(\)]/g, '').replace(/\s+/g, ' ').trim();
        for (var k in JS_ZONE_DATA) {
          if (JS_ZONE_DATA.hasOwnProperty(k) && k.replace(/[\. \-\(\)]/g, '').replace(/\s+/g, ' ').trim() === norm) { matched = JS_ZONE_DATA[k]; break; }
        }
      }
      if (matched) return { district: toTitleCase(matched.d), zone: toTitleCase(matched.z) };
      return { district: '', zone: '' };
    }
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
        AWAIT_RETURNING_PIN: 15,
        LOAN_BUSINESS_NAME: 16,
        AWAIT_MANUAL_NAME: 17,
        AWAIT_MANUAL_ASSEMBLY: 18,
        MANUAL_CONFIRM: 19
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

      /* ── Language System ── */
      let currentLang = localStorage.getItem('vanigam_lang') || 'ta';
      const T = {
        // Header
        header_title: { en: 'Tamil Nadu Vanigargalin Sangamam', ta: 'தமிழ்நாடு வணிகர்களின் சங்கமம்' },
        header_subtitle: { en: 'Digital Member ID Card', ta: 'டிஜிட்டல் உறுப்பினர் அடையாள அட்டை' },

        // Banner / Welcome
        banner_welcome: { en: '<strong>Welcome to Tamil Nadu Vanigargalin Sangamam!</strong><br>Your Digital Member ID Card Generator', ta: '<strong>தமிழ்நாடு வணிகர்களின் சங்கமத்திற்கு வரவேற்கிறோம்!</strong><br>உங்கள் டிஜிட்டல் உறுப்பினர் அடையாள அட்டை உருவாக்கி' },
        banner_hello: {
          en: '\uD83D\uDC4B Hello! Generate your <strong>Tamil Nadu Vanigargalin Sangamam Card</strong> in minutes.',
          ta: '\uD83D\uDC4B வணக்கம்! உங்கள் <strong>தமிழ்நாடு வணிகர்களின் சங்கமம் அட்டையை</strong> சில நிமிடங்களில் உருவாக்குங்கள்.<br><br>வணிகர்களுக்கு அரசியலில் அதிகாரமும் அங்கீகாரமும் கிடைக்கப் பெற வேண்டும், அதற்காக தமிழ்நாடு வணிகர்களின் சங்கமம் கட்டமைப்பில் சில மாற்றங்களை மேற்கொண்டுள்ளது.<br><br>2 சட்டமன்ற தொகுதிக்கு ஒரு மாவட்ட நிர்வாகத்தை ஏற்படுத்துவது மற்றும் 30 சார்பு தொழில் முனைவோர் பிரிவுகளை ஏற்படுத்தி உறுப்பினர் சேர்க்கையை விரிவுபடுத்துவது.<br><br>டிஜிட்டல் முறையில் உறுப்பினர் அட்டையை இணையம் வழியாக வழங்குவது.<br><br>2026 ஆம் ஆண்டு டிசம்பர் மாத இறுதிக்குள் உள்ளாட்சி அமைப்புகளில் அதிகாரமும் அங்கீகாரமும் பெரும் வகையில் ஒரு கோடி உறுப்பினர்களை இணைக்கத் திட்டமிடப்பட்டுள்ளது.<br><br>வாக்காளர் எண்ணை வைத்து உறுப்பினர் அட்டையைப் பெற ஏற்பாடு செய்யப்பட்டுள்ளது.'
        },
        banner_tap_start: { en: 'Tap Start to begin the registration process.', ta: 'பதிவு செயல்முறையைத் தொடங்க Start ஐ அழுத்தவும்.' },
        btn_start: { en: 'Start', ta: 'தொடங்கு' },
        btn_starting: { en: 'Starting...', ta: 'தொடங்குகிறது...' },

        // Mobile
        ask_mobile: {
          en: '\uD83D\uDCF1 <div style="margin-bottom:12px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;font-size:0.88rem;line-height:1.7;color:#333;"><strong>Persons holding Private Limited (PVT LTD) companies</strong><br><br><strong>Persons holding Partnership (Partnership Deed) companies</strong><br><br><strong>Persons holding Import & Export business companies</strong><br><br>may contact the Sangamam to receive an <strong>interest-free loan of ₹25 Lakhs</strong>. Thank you.</div>Please enter your <strong>10-digit mobile number</strong> to verify:',
          ta: '\uD83D\uDCF1 <div style="margin-bottom:12px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;font-size:0.88rem;line-height:1.7;color:#333;"><strong>பிரைவேட் லிமிடெட் (PVT LTD) நிறுவனம் வைத்திருக்கும் நபர்கள்</strong><br><br><strong>பார்ட்னர்ஷிப் (Partnership Deed) நிறுவனம் வைத்திருக்கும் நபர்கள்</strong><br><br><strong>ஏற்றுமதி இறக்குமதி தொழில் செய்யும் நிறுவனங்கள் வைத்து இருக்கும் நபர்கள்</strong><br><br>தொடர்பு கொள்ளவும், சங்கமத்தில் இருந்து <strong>வட்டியில்லா கடனாக ரூபாய் 25 லட்சம்</strong> பெற்றுத் தரப்படும். நன்றி.</div>சங்கமத்தில் இருந்து தங்களைத் தொடர்புகொள்ள மற்றும் உறுப்பினர் அடையாள அட்டையில் பதிவு செய்ய தங்களின் <strong>10 இலக்க மொபைல் எண்ணைப்</strong> பதிவிடவும்:'
        },
        invalid_mobile: { en: '\u274C Please enter a valid <strong>10-digit mobile number</strong>.', ta: '\u274C சரியான <strong>10 இலக்க மொபைல் எண்ணை</strong> உள்ளிடவும்.' },
        ph_mobile: { en: 'Enter 10-digit mobile number...', ta: '10 இலக்க மொபைல் எண்ணை உள்ளிடவும்...' },
        self_referral_title: { en: 'Don\'t use your same number for Dummy referrals', ta: 'போலி பரிந்துரைகளுக்கு உங்கள் எண்ணைப் பயன்படுத்த வேண்டாம்' },
        self_referral_msg: { en: 'Please use a different mobile number to register.', ta: 'பதிவு செய்ய வேறு மொபைல் எண்ணைப் பயன்படுத்தவும்.' },
        btn_enter_another: { en: 'Enter Another Number', ta: 'வேறு எண்ணை உள்ளிடவும்' },
        already_registered_title: { en: 'This number is already registered', ta: 'இந்த எண் ஏற்கனவே பதிவு செய்யப்பட்டுள்ளது' },
        already_registered_msg: { en: 'This mobile number already has a membership card.', ta: 'இந்த மொபைல் எண்ணில் ஏற்கனவே உறுப்பினர் அட்டை உள்ளது.' },

        // OTP
        otp_sent: { en: '\uD83D\uDCDE An <strong>OTP</strong> has been sent to <strong>+91 {mobile}</strong>.<br><br>Please enter the <strong>6-digit OTP</strong>:', ta: '\uD83D\uDCDE <strong>+91 {mobile}</strong> எண்ணுக்கு <strong>OTP</strong> அனுப்பப்பட்டுள்ளது.<br><br><strong>6 இலக்க OTP</strong>ஐ உள்ளிடவும்:' },
        invalid_otp: { en: '\u274C Please enter a valid <strong>6-digit OTP</strong>.', ta: '\u274C சரியான <strong>6 இலக்க OTP</strong>ஐ உள்ளிடவும்.' },
        otp_resent: { en: '\u2705 OTP Resent!', ta: '\u2705 OTP மீண்டும் அனுப்பப்பட்டது!' },
        otp_resend_fail: { en: '\u274C Failed to resend.', ta: '\u274C மீண்டும் அனுப்ப இயலவில்லை.' },
        otp_resend_error: { en: '\u274C Error resending.', ta: '\u274C மீண்டும் அனுப்புவதில் பிழை.' },
        btn_resend_otp: { en: 'Resend OTP', ta: 'OTP மீண்டும் அனுப்பு' },
        btn_change_mobile: { en: 'Change Mobile Number', ta: 'மொபைல் எண்ணை மாற்று' },
        ph_otp: { en: 'Enter 6-digit OTP...', ta: '6 இலக்க OTP உள்ளிடவும்...' },
        otp_send_fail: { en: 'Could not send OTP. Please try again.', ta: 'OTP அனுப்ப இயலவில்லை. மீண்டும் முயற்சிக்கவும்.' },
        invalid_otp_retry: { en: 'Invalid OTP. Please try again.', ta: 'தவறான OTP. மீண்டும் முயற்சிக்கவும்.' },
        verification_failed: { en: 'Verification failed. Please try again.', ta: 'சரிபார்ப்பு தோல்வி. மீண்டும் முயற்சிக்கவும்.' },

        // Returning user
        welcome_back_pin: { en: '\uD83D\uDC4B Welcome back{name}!<br><br>\uD83D\uDD12 Please enter your <strong>4-digit security PIN</strong> to access your card:', ta: '\uD83D\uDC4B மீண்டும் வரவேற்கிறோம்{name}!<br><br>\uD83D\uDD12 உங்கள் அட்டையை அணுக <strong>4 இலக்க பாதுகாப்பு PIN</strong>ஐ உள்ளிடவும்:' },
        ph_pin: { en: 'Enter 4-digit PIN...', ta: '4 இலக்க PIN உள்ளிடவும்...' },
        welcome_back_card: { en: '\uD83D\uDC4B <strong>Welcome back!</strong> Your Tamil Nadu Vanigargalin Sangamam card is ready.', ta: '\uD83D\uDC4B <strong>மீண்டும் வரவேற்கிறோம்!</strong> உங்கள் தமிழ்நாடு வணிகர்களின் சங்கமம் அட்டை தயாராக உள்ளது.' },
        mobile_verified_existing: { en: '\u2705 Mobile verified! Your <strong>Tamil Nadu Vanigargalin Sangamam</strong> card was already generated.', ta: '\u2705 மொபைல் சரிபார்க்கப்பட்டது! உங்கள் <strong>தமிழ்நாடு வணிகர்களின் சங்கமம்</strong> அட்டை ஏற்கனவே உருவாக்கப்பட்டுள்ளது.' },
        details_incomplete_hint: { en: 'Your additional details are incomplete. Type anything to update them.', ta: 'உங்கள் கூடுதல் விவரங்கள் முழுமையடையவில்லை. புதுப்பிக்க ஏதாவது தட்டச்சு செய்யவும்.' },

        // EPIC
        mobile_verified_epic: { en: '\u2705 Mobile number verified!<br><br>Please enter your <strong>EPIC Number</strong> (Voter ID):', ta: '\u2705 மொபைல் எண் சரிபார்க்கப்பட்டது!<br><br>உங்கள் <strong>EPIC எண்ணை</strong> (வாக்காளர் அடையாள எண்) உள்ளிடவும்:' },
        ph_epic: { en: 'Enter EPIC Number...', ta: 'EPIC எண்ணை உள்ளிடவும்...' },
        voter_found: { en: '\u2705 <strong>Voter Found!</strong>', ta: '\u2705 <strong>வாக்காளர் கண்டறியப்பட்டார்!</strong>' },
        is_this_correct: { en: 'Is this correct?', ta: 'இது சரியா?' },
        btn_yes_correct: { en: 'Yes, Correct', ta: 'ஆம், சரி' },
        btn_no_reenter: { en: 'No, Re-enter', ta: 'இல்லை, மீண்டும் உள்ளிடு' },
        reenter_epic: { en: 'Okay! Please enter your <strong>EPIC Number</strong> again:', ta: 'சரி! உங்கள் <strong>EPIC எண்ணை</strong> மீண்டும் உள்ளிடவும்:' },
        epic_not_found: { en: 'EPIC Number not found. Please check and try again.', ta: 'EPIC எண் கிடைக்கவில்லை. சரிபார்த்து மீண்டும் முயற்சிக்கவும்.' },
        invalid_epic_format: { en: '❌ Please enter a valid EPIC number (3 letters + 7 numbers, e.g., AYR0489518).', ta: '❌ சரியான EPIC எண்ணை உள்ளிடவும் (3 எழுத்துகள் + 7 எண்கள், எ.கா., AYR0489518).' },
        epic_not_found_manual: {
          en: 'Your EPIC details are not found in our database. Would you like to:<br><strong>1. Try again</strong> - Re-enter your EPIC number<br><strong>2. Add manually</strong> - Enter your details manually and proceed',
          ta: 'உங்கள் EPIC விவரங்கள் எங்கள் தரவுத்தளத்தில் கிடைக்கவில்லை. நீங்கள் என்ன செய்ய விரும்புகிறீர்கள்:<br><strong>1. மீண்டும் முயற்சி செய்</strong> - உங்கள் EPIC எண்ணை மீண்டும் உள்ளிடவும்<br><strong>2. கைமுறையாக சேர்</strong> - உங்கள் விவரங்களை கைமுறையாக உள்ளிடவும்'
        },
        btn_try_again: { en: 'Try Again', ta: 'மீண்டும் முயற்சி செய்' },
        btn_add_manually: { en: 'Add Manually', ta: 'கைமுறையாக சேர்' },
        enter_name: { en: 'Please enter your <strong>full name</strong> (max 50 characters):', ta: '<strong>உங்கள் முழு பெயரை</strong> உள்ளிடவும் (அதிகபட்சம் 50 எழுத்துகள்):' },
        ph_name: { en: 'Enter your full name...', ta: 'உங்கள் முழு பெயரை உள்ளிடவும்...' },
        name_too_long: { en: '❌ Name must be 50 characters or less. Please re-enter.', ta: '❌ பெயர் 50 எழுத்துகள் அல்லது அதற்கு குறைவாக இருக்க வேண்டும்.' },
        enter_assembly: { en: 'Please select your <strong>assembly/taluk</strong>:', ta: '<strong>உங்கள் சட்டமன்றத் தொகுதி</strong>ஐ தேர்ந்தெடுக்கவும்:' },
        ph_assembly: { en: 'Type to search assembly...', ta: 'சட்டமன்றத்தைத் தேட தட்டச்சு செய்...' },
        manual_epic_info: { en: 'Your EPIC Number: <strong>{epic}</strong>', ta: 'உங்கள் EPIC எண்: <strong>{epic}</strong>' },
        manual_confirm_title: { en: 'Please confirm your details:', ta: 'உங்கள் விவரங்களை உறுதிப்படுத்தவும்:' },
        manual_confirm_note: { en: 'These details will be saved with manual verification flag', ta: 'இந்த விவரங்கள் கைமுறை சரிபார்ப்பு கொடியுடன் சேமிக்கப்படும்' },
        btn_confirm_proceed: { en: 'Confirm & Proceed', ta: 'உறுதிப்படுத்து & தொடர்' },
        invalid_name: { en: '❌ Please enter a valid name (at least 2 characters)', ta: '❌ சரியான பெயரை உள்ளிடவும் (குறைந்தது 2 எழுத்துகள்)' },
        invalid_assembly: { en: '❌ Please enter a valid assembly/taluk (at least 2 characters)', ta: '❌ சரியான சட்டமன்றத் தொகுதியை உள்ளிடவும் (குறைந்தது 2 எழுத்துகள்)' },
        confirm_to_proceed: { en: '❌ Please type "Confirm" to proceed, or click the confirm button above.', ta: '❌ தொடர "உறுதிப்படுத்து" என தட்டச்சு செய்யவும், அல்லது மேலே உள்ள உறுதிப்படுத்து பொத்தானை அழுத்தவும்.' },
        validate_fail: { en: 'Could not validate. Please try again.', ta: 'சரிபார்க்க இயலவில்லை. மீண்டும் முயற்சிக்கவும்.' },
        yes_or_no: { en: 'Please type <strong>Yes</strong> or <strong>No</strong>.', ta: '<strong>ஆம்</strong> அல்லது <strong>இல்லை</strong> என தட்டச்சு செய்யவும்.' },

        // Photo
        upload_photo: { en: '\uD83D\uDCF7 Now please <strong>upload your photo</strong> using the buttons below.', ta: '\uD83D\uDCF7 கீழே உள்ள பொத்தான்களைப் பயன்படுத்தி உங்கள் <strong>புகைப்படத்தைப் பதிவேற்றவும்</strong>.' },
        btn_upload_photo: { en: 'Upload Photo', ta: 'புகைப்படம் பதிவேற்று' },
        btn_camera: { en: 'Camera', ta: 'கேமரா' },
        please_upload_photo: { en: '\uD83D\uDCF7 Please use the buttons below to <strong>upload your photo</strong>.', ta: '\uD83D\uDCF7 உங்கள் <strong>புகைப்படத்தைப் பதிவேற்ற</strong> கீழே உள்ள பொத்தான்களைப் பயன்படுத்தவும்.' },
        photo_uploaded: { en: 'Photo uploaded', ta: 'புகைப்படம் பதிவேற்றப்பட்டது' },
        photo_too_large: { en: '\u274C File size exceeds <strong>5 MB</strong>. Please upload a smaller photo.', ta: '\u274C கோப்பு அளவு <strong>5 MB</strong>ஐ தாண்டிவிட்டது. சிறிய புகைப்படத்தைப் பதிவேற்றவும்.' },
        photo_invalid_format: { en: '❌ Invalid file format. Please upload a JPG or PNG photo.', ta: '❌ தவறான கோப்பு வடிவம். JPG அல்லது PNG புகைப்படத்தைப் பதிவேற்றவும்.' },
        photo_validation_failed: { en: '❌ <strong>Photo validation failed:</strong><br>Please ensure your face is clear and visible in the photo.', ta: '❌ <strong>புகைப்பட சரிபார்ப்பு தோல்வி:</strong><br>உங்கள் முகம் புகைப்படத்தில் தெளிவாகவும் தெரியும்படியும் இருப்பதை உறுதிப்படுத்தவும்.' },
        photo_upload_failed: { en: '\u274C Photo upload failed. Please try again.', ta: '\u274C புகைப்படம் பதிவேற்றம் தோல்வி. மீண்டும் முயற்சிக்கவும்.' },
        btn_reupload: { en: 'Re-upload Photo', ta: 'மீண்டும் பதிவேற்று' },
        ph_upload: { en: 'Click the upload button below...', ta: 'கீழே உள்ள பதிவேற்று பொத்தானை அழுத்தவும்...' },

        // PIN
        set_pin: { en: '<i class="bi bi-shield-lock"></i> Please set a <strong>4-digit PIN</strong> for your membership.', ta: '<i class="bi bi-shield-lock"></i> உங்கள் உறுப்பினருக்கான <strong>4 இலக்க PIN</strong>ஐ உருவாக்குங்கள்.' },
        pin_hint: { en: 'This PIN will be used to verify your identity when accessing your card from another device.', ta: 'தாங்கள் ஒவ்வொரு முறையும் பயன்படுத்தும் போது தங்கள் மொபைல் எண் மற்றும் தாங்கள் உருவாக்கும் கடவுச்சொல் பயன்படுத்தி உள்ளீடு செய்ய முடியும். பிறகு அதை உறுதிப்படுத்துங்கள்.' },
        pin_4digits: { en: 'Please enter exactly <strong>4 digits</strong> for your PIN.', ta: 'உங்கள் PIN க்கு சரியாக <strong>4 இலக்கங்களை</strong> உள்ளிடவும்.' },
        weak_pin: { en: '<i class="bi bi-exclamation-triangle"></i> That PIN is too easy to guess (e.g. 1234, 0000, 1111). Please choose a <strong>stronger PIN</strong>.', ta: '<i class="bi bi-exclamation-triangle"></i> அந்த PIN மிகவும் எளிதானது (எ.கா. 1234, 0000, 1111). <strong>வலுவான PIN</strong> ஐ தேர்ந்தெடுக்கவும்.' },
        confirm_pin: { en: '<i class="bi bi-shield-check"></i> Please <strong>re-enter your PIN</strong> to confirm:', ta: '<i class="bi bi-shield-check"></i> உறுதிப்படுத்த உங்கள் <strong>PIN ஐ மீண்டும் உள்ளிடவும்</strong>:' },
        ph_reenter_pin: { en: 'Re-enter PIN to confirm...', ta: 'PIN ஐ மீண்டும் உள்ளிடவும்...' },
        pin_mismatch: { en: '<i class="bi bi-x-circle"></i> PINs do not match. Please set your <strong>4-digit PIN</strong> again:', ta: '<i class="bi bi-x-circle"></i> PIN பொருந்தவில்லை. உங்கள் <strong>4 இலக்க PIN</strong>ஐ மீண்டும் அமைக்கவும்:' },
        pin_set_success: { en: '<i class="bi bi-check-circle"></i> PIN set successfully!', ta: '<i class="bi bi-check-circle"></i> PIN வெற்றிகரமாக அமைக்கப்பட்டது!' },
        enter_4digit_pin: { en: 'Please enter your <strong>4-digit PIN</strong>.', ta: 'உங்கள் <strong>4 இலக்க PIN</strong>ஐ உள்ளிடவும்.' },
        invalid_pin: { en: 'Invalid PIN.', ta: 'தவறான PIN.' },

        // Additional Details
        ask_additional: { en: '\uD83D\uDCDD Would you like to add <strong>additional details</strong>?', ta: '\uD83D\uDCDD <strong>கூடுதல் விவரங்களைச்</strong> சேர்க்க விரும்புகிறீர்களா?' },
        additional_hint: { en: 'Adding details (DOB, Blood Group, Address) will complete your membership card. You can skip and fill them later via QR code.', ta: 'விவரங்களைச் (பிறந்த தேதி, இரத்தக் குழு, முகவரி) சேர்ப்பது உங்கள் உறுப்பினர் அட்டையை முழுமையாக்கும். இப்போது தவிர்த்து பின்னர் QR குறியீடு மூலம் நிரப்பலாம்.' },
        btn_add_details: { en: 'Add Details', ta: 'விவரங்களைச் சேர்' },
        btn_skip: { en: 'Skip', ta: 'தவிர்' },
        tap_add_or_skip: { en: 'Please tap <strong>Add Details</strong> or <strong>Skip</strong>.', ta: '<strong>விவரங்களைச் சேர்</strong> அல்லது <strong>தவிர்</strong> அழுத்தவும்.' },

        // DOB
        select_dob: { en: '\uD83C\uDF82 Please select your <strong>Date of Birth</strong>:', ta: '\uD83C\uDF82 உங்கள் <strong>பிறந்த தேதியைத்</strong> தேர்ந்தெடுக்கவும்:' },
        btn_confirm_dob: { en: 'Confirm DOB', ta: 'பிறந்த தேதியை உறுதிப்படுத்து' },
        select_date: { en: '\u274C Please select a date.', ta: '\u274C ஒரு தேதியைத் தேர்ந்தெடுக்கவும்.' },
        use_calendar: { en: 'Please use the <strong>calendar picker</strong> above to select your date of birth.', ta: 'உங்கள் பிறந்த தேதியைத் தேர்ந்தெடுக்க மேலே உள்ள <strong>நாட்காட்டியைப்</strong> பயன்படுத்தவும்.' },

        // Blood Group
        select_blood: { en: '\uD83E\uDE78 Please select your <strong>Blood Group</strong>:', ta: '\uD83E\uDE78 உங்கள் <strong>இரத்தக் குழுவைத்</strong> தேர்ந்தெடுக்கவும்:' },
        use_blood_buttons: { en: 'Please tap one of the <strong>blood group buttons</strong> above to select.', ta: 'தேர்ந்தெடுக்க மேலே உள்ள <strong>இரத்தக் குழு பொத்தான்களில்</strong> ஒன்றை அழுத்தவும்.' },

        // Address
        enter_address: { en: '\uD83C\uDFE0 Please enter your <strong>full address</strong> <span style="color:#888;font-size:0.8rem;">(max 70 characters)</span>:', ta: '\uD83C\uDFE0 உங்கள் <strong>முழு முகவரியை</strong> உள்ளிடவும் <span style="color:#888;font-size:0.8rem;">(அதிகபட்சம் 70 எழுத்துகள்)</span>:' },
        ph_address: { en: 'Enter your address...', ta: 'உங்கள் முகவரியை உள்ளிடவும்...' },
        btn_confirm_address: { en: 'Confirm Address', ta: 'முகவரியை உறுதிப்படுத்து' },
        enter_address_err: { en: '\u274C Please enter your address.', ta: '\u274C உங்கள் முகவரியை உள்ளிடவும்.' },
        use_address_box: { en: 'Please use the <strong>address box</strong> above and tap <strong>Confirm Address</strong>.', ta: 'மேலே உள்ள <strong>முகவரிப் பெட்டியைப்</strong> பயன்படுத்தி <strong>முகவரியை உறுதிப்படுத்து</strong> அழுத்தவும்.' },

        // Confirmation
        confirm_details: { en: '\uD83D\uDCCB <strong>Please confirm your details:</strong>', ta: '\uD83D\uDCCB <strong>உங்கள் விவரங்களை உறுதிப்படுத்தவும்:</strong>' },
        confirm_updated: { en: '\uD83D\uDCCB <strong>Please confirm your updated details:</strong>', ta: '\uD83D\uDCCB <strong>உங்கள் புதுப்பிக்கப்பட்ட விவரங்களை உறுதிப்படுத்தவும்:</strong>' },
        save_these: { en: 'Save these details?', ta: 'இந்த விவரங்களைச் சேமிக்கவா?' },
        ready_generate: { en: 'Ready to generate your <strong>Tamil Nadu Vanigargalin Sangamam Card</strong>?', ta: 'உங்கள் <strong>தமிழ்நாடு வணிகர்களின் சங்கமம் அட்டையை</strong> உருவாக்க தயாரா?' },
        btn_confirm_generate: { en: 'Confirm & Generate', ta: 'உறுதிப்படுத்து & உருவாக்கு' },
        btn_save_details: { en: 'Save Details', ta: 'விவரங்களைச் சேமி' },
        btn_cancel: { en: 'Cancel', ta: 'ரத்துசெய்' },
        yes_to_confirm: { en: 'Please type <strong>Yes</strong> to confirm or <strong>No</strong> to cancel.', ta: 'உறுதிப்படுத்த <strong>ஆம்</strong> அல்லது ரத்து செய்ய <strong>இல்லை</strong> என தட்டச்சு செய்யவும்.' },
        cancelled_start_over: { en: 'Cancelled. Please enter your <strong>EPIC Number</strong> to start over:', ta: 'ரத்து செய்யப்பட்டது. மீண்டும் தொடங்க உங்கள் <strong>EPIC எண்ணை</strong> உள்ளிடவும்:' },
        update_cancelled: { en: 'Update cancelled. You can update your details anytime from the sidebar menu.', ta: 'புதுப்பிப்பு ரத்து செய்யப்பட்டது. பக்கப்பட்டி மெனுவில் இருந்து எப்போது வேண்டுமானாலும் விவரங்களைப் புதுப்பிக்கலாம்.' },
        ph_confirm: { en: 'Type "yes" to confirm...', ta: '"ஆம்" என தட்டச்சு செய்யவும்...' },
        details_pending: { en: 'Details Pending', ta: 'விவரங்கள் நிலுவையில்' },
        lbl_update_details: { en: 'Update Details', ta: 'விவரங்களைப் புதுப்பி' },

        // Card Generation
        uploading_photo: { en: '\u2B06\uFE0F Uploading your photo...', ta: '\u2B06\uFE0F உங்கள் புகைப்படம் பதிவேற்றப்படுகிறது...' },
        generating_card: { en: '\u2699\uFE0F Generating your <strong>Tamil Nadu Vanigargalin Sangamam Card</strong>... Please wait.', ta: '\u2699\uFE0F உங்கள் <strong>தமிழ்நாடு வணிகர்களின் சங்கமம் அட்டை</strong> உருவாக்கப்படுகிறது... காத்திருக்கவும்.' },
        card_generated: { en: '\uD83C\uDF89 <strong>Your Tamil Nadu Vanigargalin Sangamam Card has been generated!</strong>', ta: '\uD83C\uDF89 <strong>உங்கள் தமிழ்நாடு வணிகர்களின் சங்கமம் அட்டை உருவாக்கப்பட்டது!</strong>' },
        card_gen_fail: { en: 'Card generation failed. Please try again.', ta: 'அட்டை உருவாக்கம் தோல்வி. மீண்டும் முயற்சிக்கவும்.' },
        details_skipped: { en: 'Some details were skipped.', ta: 'சில விவரங்கள் தவிர்க்கப்பட்டன.' },
        btn_update_now: { en: 'Update Details Now', ta: 'இப்போது விவரங்களைப் புதுப்பி' },
        details_updated: { en: '<i class="bi bi-check-circle" style="color:#2e7d32;"></i> <strong>Details updated successfully!</strong>', ta: '<i class="bi bi-check-circle" style="color:#2e7d32;"></i> <strong>விவரங்கள் வெற்றிகரமாகப் புதுப்பிக்கப்பட்டன!</strong>' },

        // Card preview buttons
        btn_view_card: { en: 'View Full Card', ta: 'முழு அட்டையைக் காண்க' },
        btn_download: { en: 'Download', ta: 'பதிவிறக்கு' },
        btn_share: { en: 'Share', ta: 'பகிர்' },
        btn_view_card_short: { en: 'View Card', ta: 'அட்டையைக் காண்க' },
        btn_download_card: { en: 'Download Card', ta: 'அட்டையைப் பதிவிறக்கு' },

        // Done state
        ready_another: { en: '\uD83D\uDC4B Ready for another card?<br><br>\uD83D\uDCF1 Please enter your <strong>10-digit mobile number</strong>:', ta: '\uD83D\uDC4B மற்றொரு அட்டைக்குத் தயாரா?<br><br>\uD83D\uDCF1 உங்கள் <strong>10 இலக்க மொபைல் எண்ணை</strong> உள்ளிடவும்:' },
        type_anything_hint: { en: 'Type anything to generate another card, or use the <strong>menu</strong> (<i class="bi bi-list"></i>) for more options.', ta: 'மற்றொரு அட்டையை உருவாக்க ஏதாவது தட்டச்சு செய்யவும், அல்லது மேலும் விருப்பங்களுக்கு <strong>மெனுவைப்</strong> (<i class="bi bi-list"></i>) பயன்படுத்தவும்.' },
        ph_type_msg: { en: 'Type a message...', ta: 'செய்தி தட்டச்சு செய்யவும்...' },
        ph_add_or_skip: { en: 'Type "add" or "skip"...', ta: '"சேர்" அல்லது "தவிர்" என தட்டச்சு...' },

        // Update details
        lets_complete: { en: '<i class="bi bi-pencil-square"></i> Let\'s complete your membership details!', ta: '<i class="bi bi-pencil-square"></i> உங்கள் உறுப்பினர் விவரங்களை நிரப்புவோம்!' },

        // Sidebar
        sb_profile: { en: 'Profile', ta: 'சுயவிவரம்' },
        sb_name: { en: 'Name', ta: 'பெயர்' },
        sb_member_id: { en: 'Member ID', ta: 'உறுப்பினர் எண்' },
        sb_epic: { en: 'EPIC No', ta: 'EPIC எண்' },
        sb_mobile: { en: 'Mobile', ta: 'மொபைல்' },
        sb_membership: { en: 'Membership', ta: 'உறுப்பினர்' },
        sb_assembly: { en: 'Assembly', ta: 'சட்டமன்றத் தொகுதி' },
        sb_district: { en: 'District', ta: 'மாவட்டம்' },
        sb_dob: { en: 'DOB', ta: 'பிறந்த தேதி' },
        sb_age: { en: 'Age', ta: 'வயது' },
        sb_blood: { en: 'Blood Group', ta: 'இரத்தக் குழு' },
        sb_address: { en: 'Address', ta: 'முகவரி' },
        sb_not_registered: { en: 'Not registered', ta: 'பதிவு செய்யவில்லை' },
        sb_vanigam_member: { en: 'Vanigam Member', ta: 'வணிகம் உறுப்பினர்' },
        sb_no_card: { en: 'No membership card yet.<br>Complete the registration to see your profile.', ta: 'உறுப்பினர் அட்டை இன்னும் இல்லை.<br>உங்கள் சுயவிவரத்தைக் காண பதிவை நிறைவு செய்யவும்.' },
        sb_referrals: { en: 'Referrals', ta: 'பரிந்துரைகள்' },
        sb_organizer_ready: { en: 'Organizer Ready', ta: 'நிர்வாகி தகுதி பெற்றவர்' },
        sb_more: { en: 'more', ta: 'மேலும்' },
        sb_to_organizer: { en: 'to become Organizer', ta: 'நிர்வாகி ஆக' },
        sb_update_details: { en: 'Update Details', ta: 'விவரங்களைப் புதுப்பி' },
        sb_update_details_desc: { en: 'Complete your membership details', ta: 'உங்கள் உறுப்பினர் விவரங்களை நிரப்பவும்' },
        sb_refer: { en: 'Refer a Friend', ta: 'நண்பரைப் பரிந்துரை செய்' },
        sb_refer_desc: { en: 'Share your referral link', ta: 'உங்கள் பரிந்துரை இணைப்பைப் பகிரவும்' },
        sb_organizer: { en: 'Become an Organizer', ta: 'நிர்வாகியாக இணைய' },
        sb_organizer_desc: { en: 'Need 25+ referrals to qualify', ta: 'தகுதி பெற 25+ பரிந்துரைகள் தேவை' },
        sb_help: { en: 'Help & Support', ta: 'உதவி & ஆதரவு' },
        sb_help_desc: { en: 'Get assistance or report issues', ta: 'உதவி பெறுங்கள் அல்லது சிக்கல்களைத் தெரிவிக்கவும்' },
        sb_logout: { en: 'Logout', ta: 'வெளியேறு' },
        sb_logout_desc: { en: 'Sign out and clear session', ta: 'வெளியேறி அமர்வை அழிக்கவும்' },
        sb_drag_hint: { en: 'Drag to rotate', ta: 'சுழற்ற இழுக்கவும்' },

        // Your Members
        sb_your_members: { en: 'Your Members', ta: 'உங்கள் உறுப்பினர்கள்' },
        sb_your_members_desc: { en: 'View members you referred', ta: 'நீங்கள் பரிந்துரைத்த உறுப்பினர்களைக் காணவும்' },
        your_members_title: { en: 'Your Referred Members', ta: 'உங்கள் பரிந்துரை உறுப்பினர்கள்' },
        your_members_count: { en: '{count} members referred', ta: '{count} உறுப்பினர்கள் பரிந்துரைக்கப்பட்டனர்' },
        no_referred_members: { en: 'You don\'t have any referred members yet', ta: 'உங்களிடம் இதுவரை பரிந்துரைக்கப்பட்ட உறுப்பினர்கள் இல்லை' },
        no_referred_hint: { en: 'Share your referral link with friends and family to grow your network!', ta: 'உங்கள் நெட்வொர்க்கை வளர்க்க நண்பர்கள் மற்றும் குடும்பத்தினருடன் உங்கள் பரிந்துரை இணைப்பைப் பகிரவும்!' },

        // Referral
        ref_your_link: { en: 'Your Referral Link', ta: 'உங்கள் பரிந்துரை இணைப்பு' },
        ref_copy: { en: 'Copy Link', ta: 'இணைப்பை நகலெடு' },
        ref_share: { en: 'Share', ta: 'பகிர்' },
        ref_share_link: { en: 'Share Link', ta: 'இணைப்பைப் பகிர்' },
        ref_copied: { en: '\u2705 Referral link copied!', ta: '\u2705 பரிந்துரை இணைப்பு நகலெடுக்கப்பட்டது!' },
        ref_copied_toast: { en: '✅ Referral link copied!', ta: '✅ பரிந்துரை இணைப்பு நகலெடுக்கப்பட்டது!' },
        ref_complete_first: { en: '\u274C Please complete registration first to get your referral link.', ta: '\u274C உங்கள் பரிந்துரை இணைப்பைப் பெற முதலில் பதிவை நிறைவு செய்யவும்.' },
        ref_fail: { en: 'Could not generate referral link', ta: 'பரிந்துரை இணைப்பை உருவாக்க இயலவில்லை' },
        ref_share_text: { en: 'Join Vanigam and get your free membership card!', ta: 'வணிகர்களின் சங்கமத்தில் இணைந்து உங்கள் இலவச உறுப்பினர் அட்டையைப் பெறுங்கள்!' },
        ref_share_text_personal: {
          en: 'I have joined Tamil Nadu Vanigargalin Sangamam for the growth and protection of traders. I request you also to join using this link and get many benefits.',
          ta: '{name} ஆகிய நான் வணிகர்களின் வளர்ச்சிக்கும் பாதுகாப்பிற்கும் வணிகர்கள் அரசியலில் அங்கீகாரத்தையும் அதிகாரத்தையும் பெற வேண்டி தமிழ்நாடு வணிகர்களின் சங்கமத்தில் இணைந்துள்ளேன். தங்களையும் இந்த link ஐ வைத்து இணைத்துக் கொண்டு பல்வேறு பயன்களைப் பெறக் கேட்டுக் கொள்கிறேன்.'
        },

        // Organizer
        org_title: { en: 'Become an Organizer', ta: 'நிர்வாகியாக இணைய' },
        org_congrats: { en: 'Congratulations!', ta: 'வாழ்த்துக்கள்!' },
        org_qualify: { en: 'You have <strong>{count} referrals</strong> and qualify to become an Organizer. Our team will contact you soon.', ta: 'உங்களிடம் <strong>{count} பரிந்துரைகள்</strong> உள்ளன, நிர்வாகியாக இணையத் தகுதி பெற்றுள்ளீர்கள். எங்கள் குழு விரைவில் தொடர்பு கொள்ளும்.' },
        org_need_more: { en: 'You need <strong>{count} more referrals</strong> to become an Organizer.', ta: 'நிர்வாகியாக இணைய <strong>{count} மேலும் பரிந்துரைகள்</strong> தேவை.' },
        org_share_hint: { en: 'Share your referral link to invite more members!', ta: 'மேலும் உறுப்பினர்களை அழைக்க உங்கள் பரிந்துரை இணைப்பைப் பகிரவும்!' },
        org_interest_welcome: {
          en: 'Those who are interested in joining as an organizer in Tamil Nadu Vanigargalin Sangamam are welcome.',
          ta: 'தமிழ்நாடு வணிகர்களின் சங்கமத்தில் நிர்வாகியாக இணைய விருப்பம் உள்ளவர்கள் வரவேற்கப்படுகிறார்கள்.'
        },
        org_min_25: {
          en: 'We request you to add at least 25 members using your referral link.',
          ta: 'தங்களுக்கான link ஐ பயன்படுத்தி குறைந்தபட்சம் 25 உறுப்பினர்களை இணைக்கக் கேட்டுக்கொள்கிறோம்.'
        },
        org_complete_first: { en: '\u274C Please complete registration first.', ta: '\u274C முதலில் பதிவை நிறைவு செய்யவும்.' },

        // Help
        help_title: { en: 'Help & Support', ta: 'உதவி & ஆதரவு' },
        help_contact_hint: { en: 'For any issues with card generation, referrals, or membership, please contact us via the above channels.', ta: 'அட்டை உருவாக்கம், பரிந்துரைகள் அல்லது உறுப்பினர் தொடர்பான ஏதேனும் சிக்கல்களுக்கு, மேலே உள்ள வழிகளில் எங்களைத் தொடர்பு கொள்ளவும்.' },

        // General
        something_wrong: { en: '\u274C Something went wrong. Please try again.', ta: '\u274C ஏதோ பிழை ஏற்பட்டது. மீண்டும் முயற்சிக்கவும்.' },
        failed_save: { en: 'Failed to save details.', ta: 'விவரங்களைச் சேமிக்கத் தோல்வி.' },

        // Wings List
        sb_wings: { en: 'Wings List', ta: 'பிரிவுகள் பட்டியல்' },
        sb_wings_desc: { en: 'View all wings of the Sangamam', ta: 'சங்கமத்தின் அனைத்து பிரிவுகளையும் காண்க' },
        wings_title: {
          en: 'Tamil Nadu Vanigargalin Sangamam (WINGS LIST)',
          ta: 'தமிழ்நாடு வணிகர்களின் சங்கமத்தின் பிரிவுகள்(WINGS LIST)'
        },
        wings_general: { en: 'General Wing', ta: 'பொது பிரிவு' },

        // Visit Website & Download App
        sb_website: { en: 'Visit Website', ta: 'இணையதளத்தைப் பார்க்க' },
        sb_website_desc: { en: 'Go to vanigan.org', ta: 'vanigan.org க்கு செல்க' },
        website_title: { en: 'Visit Our Website', ta: 'எங்கள் இணையதளத்தைப் பார்க்கவும்' },
        website_msg: { en: 'Visit the official Tamil Nadu Vanigargalin Sangamam website for more information, updates, and resources.', ta: 'மேலும் தகவல்கள், புதுப்பிப்புகள் மற்றும் ஆதாரங்களுக்கு தமிழ்நாடு வணிகர்களின் சங்கமத்தின் அதிகாரப்பூர்வ இணையதளத்தைப் பார்க்கவும்.' },
        website_btn: { en: 'Visit vanigan.org', ta: 'vanigan.org பார்க்க' },
        sb_download: { en: 'Download App', ta: 'ஆப் பதிவிறக்கம்' },
        sb_download_desc: { en: 'Get the Android app', ta: 'ஆண்ட்ராய்டு ஆப் பதிவிறக்கம்' },
        download_title: { en: 'Download Our App', ta: 'எங்கள் ஆப்பை பதிவிறக்கவும்' },
        download_msg: { en: 'Download the official Vanigan app from Google Play Store for the best experience on your Android device.', ta: 'உங்கள் ஆண்ட்ராய்டு சாதனத்தில் சிறந்த அனுபவத்திற்கு Google Play Store இல் இருந்து அதிகாரப்பூர்வ வணிகம் ஆப்பை பதிவிறக்கவும்.' },
        download_btn: { en: 'Download on Play Store', ta: 'Play Store இல் பதிவிறக்கம்' },

        // Language toggle
        lang_label: { en: 'Language', ta: 'மொழி' },
        lang_en: { en: 'English', ta: 'English' },
        lang_ta: { en: 'தமிழ்', ta: 'தமிழ்' },

        // Unverified user prompts
        unverified_refer_msg: {
          en: 'Please verify your mobile number and complete registration to access referral features.',
          ta: 'பரிந்துரை அம்சங்களை அணுக உங்கள் மொபைல் எண்ணைச் சரிபார்த்து பதிவை முடிக்கவும்.'
        },
        unverified_organizer_msg: {
          en: 'Please verify your mobile number and complete registration to become an organizer.',
          ta: 'நிர்வாகியாக இணைய உங்கள் மொபைல் எண்ணைச் சரிபார்த்து பதிவை முடிக்கவும்.'
        },
        btn_verify_mobile: { en: 'Verify Mobile Number', ta: 'மொபைல் எண் சரிபார்' },
        btn_register: { en: 'Register', ta: 'பதிவு செய்' },

        // Request Loan
        btn_request_loan: { en: 'Request Loan', ta: 'கடன் கோரிக்கை' },
        loan_intro: {
          en: 'We have dedicated schemes for Pvt Ltd companies, Partnership Businesses & Import Export businesses, wherein we provide upto 25L interest free loan.<br><br>Do you have any of these businesses: Pvt Ltd companies, Partnership Businesses or Import Export businesses?',
          ta: 'Pvt Ltd நிறுவனங்கள், கூட்டாண்மை வணிகங்கள் மற்றும் இறக்குமதி ஏற்றுமதி வணிகங்களுக்கு பிரத்யேக திட்டங்கள் உள்ளன, இதில் 25 லட்சம் வரை வட்டியில்லா கடன் வழங்குகிறோம்.<br><br>உங்களிடம் Pvt Ltd நிறுவனங்கள், கூட்டாண்மை வணிகங்கள் அல்லது இறக்குமதி ஏற்றுமதி வணிகங்கள் உள்ளதா?'
        },
        btn_yes: { en: 'Yes', ta: 'ஆம்' },
        btn_no: { en: 'No', ta: 'இல்லை' },
        loan_great: {
          en: 'Great! Please select your business type:',
          ta: 'அருமை! உங்கள் வணிக வகையைத் தேர்ந்தெடுக்கவும்:'
        },
        loan_no_eligible: {
          en: 'Currently, our interest-free loan schemes are available only for Pvt Ltd companies, Partnership Businesses & Import Export businesses. We will notify you when new schemes are available.',
          ta: 'தற்போது, வட்டியில்லா கடன் திட்டங்கள் Pvt Ltd நிறுவனங்கள், கூட்டாண்மை வணிகங்கள் மற்றும் இறக்குமதி ஏற்றுமதி வணிகங்களுக்கு மட்டுமே கிடைக்கும். புதிய திட்டங்கள் வரும்போது உங்களுக்குத் தெரிவிப்போம்.'
        },
        btn_pvt_ltd: { en: 'Pvt Ltd Company', ta: 'Pvt Ltd நிறுவனம்' },
        btn_partnership: { en: 'Partnership Business', ta: 'கூட்டாண்மை வணிகம்' },
        btn_import_export: { en: 'Import Export Business', ta: 'இறக்குமதி ஏற்றுமதி வணிகம்' },
        loan_enter_business_name: {
          en: 'Please share your business name:',
          ta: 'உங்கள் வணிகப் பெயரைப் பகிரவும்:'
        },
        btn_submit: { en: 'Submit', ta: 'சமர்ப்பி' },
        loan_thanks: {
          en: 'Thanks for applying for the 25L interest-free loan! Our team will contact you soon.',
          ta: '25 லட்சம் வட்டியில்லா கடனுக்கு விண்ணப்பித்ததற்கு நன்றி! எங்கள் குழு விரைவில் தொடர்பு கொள்ளும்.'
        },
        loan_already_applied: {
          en: 'You have already applied for the 25L interest-free loan. Our team will contact you soon.',
          ta: '25 லட்சம் வட்டியில்லா கடனுக்கு நீங்கள் ஏற்கனவே விண்ணப்பித்துள்ளீர்கள். எங்கள் குழு விரைவில் தொடர்பு கொள்ளும்.'
        },

        // Help & Support buttons
        btn_email: { en: 'Email Us', ta: 'மின்னஞ்சல் அனுப்பு' },
        btn_call: { en: 'Call Us', ta: 'அழைக்க' },

        // Summary labels
        lbl_name: { en: 'Name', ta: 'பெயர்' },
        lbl_member_id: { en: 'Member ID', ta: 'உறுப்பினர் எண்' },
        lbl_epic: { en: 'EPIC No', ta: 'EPIC எண்' },
        lbl_assembly: { en: 'Assembly', ta: 'சட்டமன்றத் தொகுதி' },
        lbl_district: { en: 'District', ta: 'மாவட்டம்' },
        lbl_mobile: { en: 'Mobile', ta: 'மொபைல்' },
        lbl_dob: { en: 'DOB', ta: 'பிறந்த தேதி' },
        lbl_blood: { en: 'Blood Group', ta: 'இரத்தக் குழு' },
        lbl_address: { en: 'Address', ta: 'முகவரி' },
        lbl_status: { en: 'Status', ta: 'நிலை' },
      };

      function L(key, replacements) {
        const entry = T[key];
        if (!entry) return key;
        let text = entry[currentLang] || entry['en'] || key;
        if (replacements) {
          for (const [k, v] of Object.entries(replacements)) {
            text = text.replace('{' + k + '}', v);
          }
        }
        return text;
      }

      function setLang(lang) {
        currentLang = lang;
        localStorage.setItem('vanigam_lang', lang);
        // Update static header text
        document.querySelector('.chat-header .info h4').textContent = L('header_title');
        document.querySelector('.chat-header .info .status').textContent = L('header_subtitle');
        // Clear chat and re-render from current state
        chatEl.innerHTML = '';
        init();
        // Refresh sidebar
        updateSidebarContent();
      }

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
          sbName.textContent = m.name || L('sb_vanigam_member');
          sbId.textContent = m.unique_id || L('sb_membership');

          // Update sidebar avatar with user photo
          const sbAvatar = document.querySelector('.sidebar-header .sb-avatar');
          if (sbAvatar && m.photo_url) {
            sbAvatar.innerHTML = '<img src="' + m.photo_url + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
          }

          // Profile Section
          html += '<div class="sb-section"><div class="sb-section-title">' + L('sb_profile') + '</div>';
          html += '<div class="sb-profile">';
          const sbDz = lookupDistrictZone(m.assembly);
          const fields = [
            [L('sb_name'), m.name], [L('sb_member_id'), m.unique_id], [L('sb_epic'), m.epic_no],
            [L('sb_mobile'), '+91 ' + (user.mobile || m.mobile || '')],
            [L('sb_assembly'), m.assembly], [L('sb_district'), sbDz.district || toTitleCase(m.district)],
            ['Zone', sbDz.zone || toTitleCase(m.zone || '')]
          ];
          if (m.dob) fields.push([L('sb_dob'), m.dob]);
          if (m.age) fields.push([L('sb_age'), m.age]);
          if (m.blood_group) fields.push([L('sb_blood'), m.blood_group]);
          if (m.address) fields.push([L('sb_address'), m.address]);
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
          const sb3dDz = lookupDistrictZone(m.assembly);
          html += '<p style="font-size:0.62rem;font-weight:700;color:#009245;margin:0;">' + (m.name || '') + '</p>';
          html += '<p style="font-size:0.44rem;margin:1px 0 0;">' + (m.assembly ? m.assembly + ' <span style="display:inline-block;font-size:0.28rem;font-weight:700;color:#fff;background:#009245;border-radius:3px;padding:0 3px;vertical-align:middle;letter-spacing:0.3px;line-height:1.4;">Assm</span>' : '') + '</p>';
          html += '<p style="font-size:0.44rem;margin:1px 0 0;">' + ((sb3dDz.district || toTitleCase(m.district)) ? (sb3dDz.district || toTitleCase(m.district)) + ' <span style="display:inline-block;font-size:0.28rem;font-weight:700;color:#fff;background:#009245;border-radius:3px;padding:0 3px;vertical-align:middle;letter-spacing:0.3px;line-height:1.4;">Dist</span>' : '') + '</p>';
          html += '<p style="font-size:0.44rem;margin:1px 0 0;">' + ((sb3dDz.zone || toTitleCase(m.zone)) || '') + '</p>';
          html += '<p style="font-size:0.42rem;margin:2px 0 0;letter-spacing:0.3px;">' + (m.unique_id || '') + '</p>';
          html += '</div></div></div>';

          // Back face
          html += '<div class="card3d-face card3d-back">';
          html += '<div style="position:relative;width:100%;height:100%;background:url(https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png) center/contain no-repeat;">';
          // Back fields
          html += '<div style="position:absolute;top:28%;left:6%;right:6%;font-size:0.38rem;line-height:1.2;display:flex;flex-direction:column;gap:2px;overflow:hidden;">';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:10px;"><span style="font-weight:700;">DATE OF BIRTH</span><span style="font-weight:700;">:</span><span>' + (m.dob || 'xxxxxx') + '</span></div>';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:10px;"><span style="font-weight:700;">AGE</span><span style="font-weight:700;">:</span><span>' + (m.age || 'xxxxxx') + '</span></div>';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:10px;"><span style="font-weight:700;">BLOOD GROUP</span><span style="font-weight:700;">:</span><span>' + (m.blood_group || 'xxxxxx') + '</span></div>';
          html += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:30px;"><span style="font-weight:700;">ADDRESS</span><span style="font-weight:700;">:</span><span style="font-size:0.34rem;word-break:break-word;overflow:hidden;">' + (m.address || 'xxxxxx') + '</span></div>';
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
          html += '<span class="card3d-hint"><i class="bi bi-hand-index-thumb"></i> ' + L('sb_drag_hint') + '</span>';
          html += '<button class="card3d-btn" onclick="rotate3dCard(1)" title="Rotate Right"><i class="bi bi-arrow-clockwise"></i></button>';
          html += '</div>';

          // Action buttons
          html += '<div class="sb-card-actions" style="margin-top:12px;">';
          html += '<button class="dl-btn" onclick="window.open(\'' + cardUrl + '\',\'_blank\')"><i class="bi bi-eye"></i> ' + L('btn_view_card_short') + '</button>';
          html += '<button class="dl-btn" onclick="window.open(\'' + cardUrl + '\',\'_blank\')"><i class="bi bi-download"></i> ' + L('btn_download_card') + '</button>';
          html += '</div></div></div>';
        } else {
          sbName.textContent = L('sb_vanigam_member');
          sbId.textContent = L('sb_not_registered');
          html += '<div class="sb-section" style="text-align:center;padding:40px 18px;">';
          html += '<i class="bi bi-person-badge" style="font-size:3rem;color:#ccc;"></i>';
          html += '<p style="color:#888;margin-top:12px;font-size:0.9rem;">' + L('sb_no_card') + '</p>';
          html += '</div>';
        }

        // Referral Stats (show at top if member exists)
        if (user && user.memberData && user.memberData.unique_id) {
          const rc = user.memberData.referral_count || 0;
          html += '<div style="display:flex;align-items:center;justify-content:center;gap:10px;padding:14px 18px;background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:12px;margin:12px 16px;">';
          html += '<i class="bi bi-people-fill" style="font-size:1.5rem;color:#2e7d32;"></i>';
          html += '<div style="text-align:center;">';
          html += '<div style="font-size:1.4rem;font-weight:800;color:#1b5e20;">' + rc + '</div>';
          html += '<div style="font-size:0.72rem;color:#555;font-weight:500;">' + L('sb_referrals') + '</div>';
          html += '</div>';
          html += '<div style="width:1px;height:30px;background:#aed581;"></div>';
          html += '<div style="text-align:center;">';
          if (rc >= 25) {
            html += '<i class="bi bi-star-fill" style="font-size:1.2rem;color:#f9a825;"></i>';
            html += '<div style="font-size:0.72rem;color:#f57f17;font-weight:600;">' + L('sb_organizer_ready') + '</div>';
          } else {
            html += '<div style="font-size:0.85rem;font-weight:700;color:#555;">' + (25 - rc) + ' ' + L('sb_more') + '</div>';
            html += '<div style="font-size:0.68rem;color:#888;">' + L('sb_to_organizer') + '</div>';
          }
          html += '</div></div>';
        }

        // Menu Items
        if (user && user.memberData && !user.memberData.details_completed) {
          html += '<div class="sb-menu-item" onclick="doMenuUpdateDetails()" style="background:rgba(255,152,0,0.08);border:1px solid rgba(255,152,0,0.3);"><i class="bi bi-pencil-square" style="color:#e65100;"></i><div class="sb-menu-text"><h5 style="color:#e65100;">' + L('sb_update_details') + '</h5><p>' + L('sb_update_details_desc') + '</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        }
        html += '<div class="sb-menu-item" style="flex-wrap:wrap;">';
        html += '<i class="bi bi-share"></i><div class="sb-menu-text" onclick="doMenuRefer()" style="cursor:pointer;"><h5>' + L('sb_refer') + '</h5><p>' + L('sb_refer_desc') + '</p></div>';
        html += '<div style="display:flex;gap:6px;">';
        html += '<button onclick="event.stopPropagation();sidebarCopyRef()" style="border:none;background:#e8f5e9;color:#2e7d32;width:34px;height:34px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;" title="Copy Referral Link"><i class="bi bi-clipboard"></i></button>';
        html += '<button onclick="event.stopPropagation();sidebarShareRef()" style="border:none;background:#e8f5e9;color:#2e7d32;width:34px;height:34px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;" title="Share Referral Link"><i class="bi bi-send"></i></button>';
        html += '</div></div>';
        html += '<div class="sb-menu-item" onclick="doMenuYourMembers()"><i class="bi bi-people"></i><div class="sb-menu-text"><h5>' + L('sb_your_members') + '</h5><p>' + L('sb_your_members_desc') + '</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        html += '<div class="sb-menu-item" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i><div class="sb-menu-text"><h5>' + L('sb_organizer') + '</h5><p>' + L('sb_organizer_desc') + '</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        html += '<div class="sb-menu-item" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i><div class="sb-menu-text"><h5>' + L('sb_wings') + '</h5><p>' + L('sb_wings_desc') + '</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        html += '<div class="sb-menu-item" onclick="doMenuHelp()"><i class="bi bi-question-circle"></i><div class="sb-menu-text"><h5>' + L('sb_help') + '</h5><p>' + L('sb_help_desc') + '</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        html += '<div class="sb-menu-item" onclick="doMenuWebsite()"><i class="bi bi-globe2"></i><div class="sb-menu-text"><h5>' + L('sb_website') + '</h5><p>' + L('sb_website_desc') + '</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        html += '<div class="sb-menu-item" onclick="doMenuDownload()"><i class="bi bi-google-play"></i><div class="sb-menu-text"><h5>' + L('sb_download') + '</h5><p>' + L('sb_download_desc') + '</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        // Language Toggle
        html += '<div class="sb-menu-item" style="cursor:default;"><i class="bi bi-translate"></i><div class="sb-menu-text"><h5>' + L('lang_label') + '</h5><p>English / தமிழ்</p></div>';
        html += '<div style="position:relative;width:48px;height:26px;border-radius:13px;cursor:pointer;transition:background 0.3s;background:' + (currentLang === 'ta' ? '#2e7d32' : '#ccc') + ';flex-shrink:0;" onclick="toggleLang()">';
        html += '<div style="position:absolute;top:2px;width:22px;height:22px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,0.3);transition:left 0.3s;left:' + (currentLang === 'ta' ? '24px' : '2px') + ';"></div>';
        html += '</div></div>';
        html += '<div class="sb-menu-item" onclick="doMenuLogout()"><i class="bi bi-box-arrow-right" style="color:#d32f2f;"></i><div class="sb-menu-text"><h5 style="color:#d32f2f;">' + L('sb_logout') + '</h5><p>' + L('sb_logout_desc') + '</p></div><span class="sb-menu-arrow"><i class="bi bi-chevron-right"></i></span></div>';

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

      window.toggleLang = function () {
        setLang(currentLang === 'en' ? 'ta' : 'en');
      };

      window.doMenuRefer = async function () {
        closeSidebar();
        const user = getUser();
        if (!user || !user.memberData || !user.memberData.unique_id) {
          let h = '<i class="bi bi-exclamation-circle" style="color:#f9a825;font-size:1.2rem;"></i> <strong>' + L('sb_refer') + '</strong>';
          h += '<div style="margin-top:10px;padding:14px;background:#fff8e1;border-radius:12px;border:1px solid #ffe082;font-size:0.9rem;line-height:1.6;">';
          h += '<p>' + L('unverified_refer_msg') + '</p>';
          h += '</div>';
          h += '<div class="action-buttons" style="margin-top:12px;">';
          h += '<button class="action-btn confirm" onclick="doStartVerify()"><i class="bi bi-telephone"></i> ' + L('btn_verify_mobile') + '</button>';
          h += '<button class="action-btn confirm" onclick="doStartRegister()"><i class="bi bi-person-plus"></i> ' + L('btn_register') + '</button>';
          h += '</div>';
          await botReply(h, 600);
          return;
        }
        try {
          showTyping();
          const res = await api('/api/vanigam/get-referral', { unique_id: user.memberData.unique_id });
          hideTyping();
          if (res.success) {
            const link = res.referral_link;
            let h = '<i class="bi bi-share" style="color:#2e7d32;"></i> <strong>' + L('ref_your_link') + '</strong>';
            h += '<div style="margin-top:10px;padding:12px;background:#f0f9f1;border-radius:10px;border:1px solid #c8e6c9;">';
            h += '<code style="word-break:break-all;font-size:0.85rem;color:#1b5e20;">' + link + '</code>';
            h += '</div>';
            h += '<div style="margin-top:8px;display:flex;gap:8px;">';
            h += '<button class="action-btn confirm" onclick="copyReferral(\'' + link + '\')" style="flex:1;"><i class="bi bi-clipboard"></i> ' + L('ref_copy') + '</button>';
            h += '<button class="action-btn confirm" onclick="shareReferral(\'' + link + '\')" style="flex:1;"><i class="bi bi-send"></i> ' + L('ref_share') + '</button>';
            h += '</div>';
            h += '<div style="margin-top:8px;font-size:0.8rem;color:#666;"><i class="bi bi-people"></i> ' + L('sb_referrals') + ': <strong>' + res.referral_count + '</strong> / 25</div>';
            const refLoanApplied = await checkHasAppliedLoan();
            h += '<div class="action-buttons" style="margin-top:8px;">';
            h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
            h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
            h += '</div>';
            h += '<div class="action-buttons" style="margin-top:8px;">';
            h += '<button class="action-btn confirm" onclick="doMenuYourMembers()"><i class="bi bi-people"></i> ' + L('sb_your_members') + '</button>';
            if (!refLoanApplied) h += buildLoanBtn();
            h += '</div>';
            await botReply(h, 800);
          } else {
            console.error('Referral API error:', res);
            await botReply('\u274C ' + L('ref_fail') + ': ' + (res.message || ''), 600);
          }
        } catch(e) { hideTyping(); console.error('Referral exception:', e); await botReply(L('something_wrong') + ' ' + e.message, 600); }
      };

      window.copyReferral = function (link) {
        navigator.clipboard.writeText(link).then(() => { botMsg(L('ref_copied')); });
      };

      window.shareReferral = function (link) {
        const user = getUser();
        const memberName = (user && user.memberData) ? user.memberData.name || '' : '';
        const shareText = currentLang === 'ta' ? L('ref_share_text_personal', { name: memberName }) : L('ref_share_text');
        if (navigator.share) {
          navigator.share({ title: L('header_title'), text: shareText, url: link });
        } else {
          navigator.clipboard.writeText(link).then(() => { botMsg(L('ref_copied')); });
        }
      };

      // Sidebar quick copy/share referral helpers
      window.sidebarCopyRef = async function () {
        const user = getUser();
        if (!user || !user.memberData || !user.memberData.unique_id) return;
        try {
          const res = await api('/api/vanigam/get-referral', { unique_id: user.memberData.unique_id });
          if (res.success) {
            navigator.clipboard.writeText(res.referral_link).then(() => {
              const toast = document.createElement('div');
              toast.textContent = L('ref_copied_toast');
              toast.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#1b5e20;color:#fff;padding:10px 20px;border-radius:20px;font-size:0.85rem;font-weight:600;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.2);';
              document.body.appendChild(toast);
              setTimeout(() => toast.remove(), 2000);
            });
          }
        } catch(e) {}
      };
      window.sidebarShareRef = async function () {
        const user = getUser();
        if (!user || !user.memberData || !user.memberData.unique_id) return;
        try {
          const res = await api('/api/vanigam/get-referral', { unique_id: user.memberData.unique_id });
          if (res.success) {
            const memberName = user.memberData.name || '';
            const shareText = currentLang === 'ta' ? L('ref_share_text_personal', { name: memberName }) : L('ref_share_text');
            if (navigator.share) {
              navigator.share({ title: L('header_title'), text: shareText, url: res.referral_link });
            } else {
              navigator.clipboard.writeText(res.referral_link).then(() => {
                const toast = document.createElement('div');
                toast.textContent = L('ref_copied_toast');
                toast.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#1b5e20;color:#fff;padding:10px 20px;border-radius:20px;font-size:0.85rem;font-weight:600;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.2);';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
              });
            }
          }
        } catch(e) {}
      };

      window.doMenuOrganizer = async function () {
        closeSidebar();
        const user = getUser();
        if (!user || !user.memberData || !user.memberData.unique_id) {
          let h = '<i class="bi bi-exclamation-circle" style="color:#f9a825;font-size:1.2rem;"></i> <strong>' + L('sb_organizer') + '</strong>';
          h += '<div style="margin-top:10px;padding:14px;background:#fff8e1;border-radius:12px;border:1px solid #ffe082;font-size:0.9rem;line-height:1.6;">';
          h += '<p>' + L('unverified_organizer_msg') + '</p>';
          h += '</div>';
          h += '<div class="action-buttons" style="margin-top:12px;">';
          h += '<button class="action-btn confirm" onclick="doStartVerify()"><i class="bi bi-telephone"></i> ' + L('btn_verify_mobile') + '</button>';
          h += '<button class="action-btn confirm" onclick="doStartRegister()"><i class="bi bi-person-plus"></i> ' + L('btn_register') + '</button>';
          h += '</div>';
          await botReply(h, 600);
          return;
        }
        const rc = user.memberData.referral_count || 0;
        let refLink = '';
        try {
          const res = await api('/api/vanigam/get-referral', { unique_id: user.memberData.unique_id });
          if (res.success) refLink = res.referral_link;
        } catch(e) {}

        let h = '<i class="bi bi-briefcase" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('org_title') + '</strong>';
        h += '<div style="margin-top:10px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;">';
        h += '<p style="font-size:0.85rem;color:#555;margin-bottom:10px;">' + L('org_interest_welcome') + '</p>';
        h += '<p style="font-size:0.82rem;color:#666;margin-bottom:10px;">' + L('org_min_25') + '</p>';
        if (rc >= 25) {
          h += '<div style="text-align:center;">';
          h += '<i class="bi bi-star-fill" style="font-size:2rem;color:#f9a825;"></i>';
          h += '<p style="font-weight:700;color:#1b5e20;margin:8px 0 4px;">' + L('org_congrats') + '</p>';
          h += '<p style="font-size:0.85rem;color:#555;">' + L('org_qualify', { count: rc }) + '</p>';
          h += '</div>';
        } else {
          h += '<div style="text-align:center;">';
          h += '<div style="font-size:2rem;font-weight:800;color:#1b5e20;">' + rc + ' / 25</div>';
          h += '<div style="width:100%;height:8px;background:#e0e0e0;border-radius:4px;margin:10px 0;overflow:hidden;">';
          h += '<div style="width:' + Math.min(100, (rc / 25) * 100) + '%;height:100%;background:linear-gradient(90deg,#4caf50,#2e7d32);border-radius:4px;"></div>';
          h += '</div>';
          h += '<p style="font-size:0.85rem;color:#555;">' + L('org_need_more', { count: 25 - rc }) + '</p>';
          h += '<p style="font-size:0.8rem;color:#888;margin-top:6px;">' + L('org_share_hint') + '</p>';
          h += '</div>';
        }
        h += '</div>';
        // Add copy/share referral buttons
        if (refLink) {
          h += '<div style="margin-top:10px;display:flex;gap:8px;">';
          h += '<button class="action-btn confirm" onclick="copyReferral(\'' + refLink + '\')" style="flex:1;"><i class="bi bi-clipboard"></i> ' + L('ref_copy') + '</button>';
          h += '<button class="action-btn confirm" onclick="shareReferral(\'' + refLink + '\')" style="flex:1;"><i class="bi bi-send"></i> ' + L('ref_share_link') + '</button>';
          h += '</div>';
        }
        const orgLoanApplied = await checkHasAppliedLoan();
        h += '<div class="action-buttons" style="margin-top:8px;">';
        h += '<button class="action-btn confirm" onclick="doMenuYourMembers()"><i class="bi bi-people"></i> ' + L('sb_your_members') + '</button>';
        h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
        if (!orgLoanApplied) h += buildLoanBtn();
        h += '</div>';
        await botReply(h, 800);
      };

      window.doMenuYourMembers = async function () {
        closeSidebar();
        const user = getUser();
        if (!user || !user.memberData || !user.memberData.unique_id) {
          let h = '<i class="bi bi-exclamation-circle" style="color:#f9a825;font-size:1.2rem;"></i> <strong>' + L('sb_your_members') + '</strong>';
          h += '<div style="margin-top:10px;padding:14px;background:#fff8e1;border-radius:12px;border:1px solid #ffe082;font-size:0.9rem;line-height:1.6;">';
          h += '<p>' + L('unverified_refer_msg') + '</p>';
          h += '</div>';
          h += '<div class="action-buttons" style="margin-top:12px;">';
          h += '<button class="action-btn confirm" onclick="doStartVerify()"><i class="bi bi-telephone"></i> ' + L('btn_verify_mobile') + '</button>';
          h += '<button class="action-btn confirm" onclick="doStartRegister()"><i class="bi bi-person-plus"></i> ' + L('btn_register') + '</button>';
          h += '</div>';
          await botReply(h, 600);
          return;
        }
        try {
          showTyping();
          const res = await api('/api/vanigam/get-referred-members', { unique_id: user.memberData.unique_id });
          hideTyping();
          if (res.success && res.members && res.members.length > 0) {
            let h = '<i class="bi bi-people-fill" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('your_members_title') + '</strong>';
            h += '<div style="margin-top:6px;font-size:0.8rem;color:#555;">' + L('your_members_count', { count: res.count }) + '</div>';
            h += '<div style="margin-top:12px;display:flex;flex-direction:column;gap:10px;">';
            for (let i = 0; i < res.members.length; i++) {
              const m = res.members[i];
              h += '<div style="display:flex;align-items:center;gap:12px;padding:12px;background:linear-gradient(135deg,#f0f9f1,#e8f5e9);border-radius:12px;border:1px solid #c8e6c9;">';
              // Photo
              if (m.photo_url) {
                h += '<img src="' + m.photo_url + '" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #2e7d32;flex-shrink:0;">';
              } else {
                h += '<div style="width:48px;height:48px;border-radius:50%;background:#c8e6c9;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.2rem;color:#2e7d32;"><i class="bi bi-person-fill"></i></div>';
              }
              // Info
              h += '<div style="flex:1;min-width:0;">';
              h += '<div style="font-weight:700;font-size:0.9rem;color:#1b5e20;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + (m.name || 'Member') + '</div>';
              h += '<div style="font-size:0.75rem;color:#555;margin-top:2px;"><span style="font-weight:600;color:#2e7d32;">' + (m.unique_id || '') + '</span></div>';
              h += '<div style="font-size:0.72rem;color:#888;margin-top:1px;">';
              if (m.epic_no && !m.epic_no.startsWith('MANUAL_')) h += 'EPIC: ' + m.epic_no + ' &bull; ';
              if (m.assembly) h += m.assembly;
              const mlDz = lookupDistrictZone(m.assembly);
              if (mlDz.district || m.district) h += ', ' + (mlDz.district || toTitleCase(m.district));
              h += '</div>';
              h += '</div>';
              // Serial number
              h += '<div style="font-size:0.7rem;font-weight:700;color:#aaa;flex-shrink:0;">#' + (i + 1) + '</div>';
              h += '</div>';
            }
            h += '</div>';
            // Reply buttons
            const ymLoanApplied = await checkHasAppliedLoan();
            h += '<div class="action-buttons" style="margin-top:12px;">';
            h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
            h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
            if (!ymLoanApplied) h += buildLoanBtn();
            h += '</div>';
            await botReply(h, 800);
          } else {
            // No referred members — show message + share/copy buttons
            let refLink = '';
            try {
              const refRes = await api('/api/vanigam/get-referral', { unique_id: user.memberData.unique_id });
              if (refRes.success) refLink = refRes.referral_link;
            } catch(e) {}

            let h = '<i class="bi bi-people" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('your_members_title') + '</strong>';
            h += '<div style="margin-top:14px;text-align:center;padding:24px 16px;background:#f0f9f1;border-radius:14px;border:1px solid #c8e6c9;">';
            h += '<i class="bi bi-person-x" style="font-size:2.5rem;color:#a5d6a7;"></i>';
            h += '<p style="font-weight:700;color:#1b5e20;margin:12px 0 6px;font-size:0.95rem;">' + L('no_referred_members') + '</p>';
            h += '<p style="font-size:0.82rem;color:#666;line-height:1.5;">' + L('no_referred_hint') + '</p>';
            h += '</div>';
            if (refLink) {
              h += '<div style="margin-top:10px;padding:12px;background:#f0f9f1;border-radius:10px;border:1px solid #c8e6c9;">';
              h += '<code style="word-break:break-all;font-size:0.82rem;color:#1b5e20;">' + refLink + '</code>';
              h += '</div>';
              h += '<div style="margin-top:10px;display:flex;gap:8px;">';
              h += '<button class="action-btn confirm" onclick="copyReferral(\'' + refLink + '\')" style="flex:1;"><i class="bi bi-clipboard"></i> ' + L('ref_copy') + '</button>';
              h += '<button class="action-btn confirm" onclick="shareReferral(\'' + refLink + '\')" style="flex:1;"><i class="bi bi-send"></i> ' + L('ref_share') + '</button>';
              h += '</div>';
            } else {
              h += '<div style="margin-top:10px;display:flex;gap:8px;">';
              h += '<button class="action-btn confirm" onclick="doMenuRefer()" style="flex:1;"><i class="bi bi-share"></i> ' + L('ref_share') + '</button>';
              h += '<button class="action-btn confirm" onclick="doMenuRefer()" style="flex:1;"><i class="bi bi-clipboard"></i> ' + L('ref_copy') + '</button>';
              h += '</div>';
            }
            const ymNoLoanApplied = await checkHasAppliedLoan();
            h += '<div class="action-buttons" style="margin-top:8px;">';
            h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
            h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
            if (!ymNoLoanApplied) h += buildLoanBtn();
            h += '</div>';
            await botReply(h, 800);
          }
        } catch(e) { hideTyping(); console.error('Your Members error:', e); await botReply(L('something_wrong'), 600); }
      };

      window.doMenuWings = async function () {
        closeSidebar();
        const wings = [
          { en: "Women's - Entrepreneur Wing", ta: 'மகளிர் - தொழில்முனைவோர் பிரிவு' },
          { en: "Auditor's Wing", ta: 'பட்டய கணக்காளர்கள் பிரிவு' },
          { en: "Doctor's Wing", ta: 'மருத்துவர் பிரிவு' },
          { en: 'Advocate Wing', ta: 'வழக்கறிஞர் பிரிவு' },
          { en: 'Agriculture Wing', ta: 'விவசாய பிரிவு' },
          { en: 'Information Technology Wing', ta: 'தகவல் தொழில்நுட்ப பிரிவு' },
          { en: 'Engineer Wing', ta: 'பொறியாளர் பிரிவு' },
          { en: 'Labor Wing', ta: 'தொழிலாளர் பிரிவு' },
          { en: 'Differently Abled Wing', ta: 'மாற்றுத்திறனாளிகள் பிரிவு' },
          { en: 'Young Entrepreneur Wing', ta: 'இளைய தொழில் முனைவோர் பிரிவு' },
          { en: "Spokesperson's Wing", ta: 'செய்தி தொடர்பாளர் பிரிவு' },
          { en: "Distributor's Wing", ta: 'விநியோகஸ்தர் பிரிவு' },
          { en: "Manufacturer's Wing", ta: 'உற்பத்தியாளர் பிரிவு' },
          { en: 'Real Estate Industry Wing', ta: 'மனைத்தொழில் பிரிவு' },
          { en: 'Pharmacist & Pharma Business Wing', ta: 'மருந்தாளுனர் & மருந்து வணிகப் பிரிவு' },
          { en: "Educator's Wing", ta: 'கல்வியாளர் பிரிவு' },
          { en: 'Import & Export Business Wing', ta: 'இறக்குமதி / ஏற்றுமதி வணிக பிரிவு' },
          { en: "Third Gender Entrepreneur's Wing", ta: 'மூன்றாம் பாலினத்தவர் தொழில் முனைவோர் பிரிவு' },
          { en: "Shop Owner's Wing", ta: 'கடை உரிமையாளர் பிரிவு' },
          { en: 'Central Government Relationship Wing', ta: 'மத்திய அரசு உறவுப் பிரிவு' },
          { en: 'State Government Relationship Wing', ta: 'மாநில அரசு உறவுப் பிரிவு' },
          { en: "Restaurant Owner's Wing", ta: 'உணவக உரிமையாளர் பிரிவு' },
          { en: 'Tourism & Transport Business Wing', ta: 'சுற்றுலா மற்றும் போக்குவரத்து வணிக பிரிவு' },
          { en: 'Sports & Sports Business Wing', ta: 'விளையாட்டு & விளையாட்டு வணிகப் பிரிவு' },
          { en: 'Marine Based Business Wing', ta: 'கடல் சார்ந்த வணிகப் பிரிவு' },
          { en: "Tribe's - Entrepreneur's Wing", ta: 'பழங்குடியினர் - தொழில்முனைவோர் பிரிவு' },
          { en: "Digital Promoter's Wing", ta: 'டிஜிட்டல் விளம்பரதாரர் பிரிவு' },
          { en: 'Printing & Press Business Wing', ta: 'அச்சக தொழில் பிரிவு' },
          { en: 'Computer & Mobile Business Wing', ta: 'கணினி மற்றும் அலைபேசி தொழில் பிரிவு' },
          { en: 'Weaver Business Wing', ta: 'நெசவாளர் வணிக பிரிவு' },
          { en: 'Finance, Insurance & Chit Fund Business Wing', ta: 'காப்பீடு, நிதி மற்றும் சீட்டு தொழில்முனைவோர் பிரிவு' },
          { en: "Roadside Vendor's Wing", ta: 'சாலையோர வியாபாரிகள் பிரிவு' },
          { en: 'Lodging Business Wing', ta: 'தங்கும் விடுதி வணிகப் பிரிவு' },
          { en: 'Beautician & Fitness Business Wing', ta: 'அழகுக்கலை மற்றும் உடற்பயிற்சி வணிகப் பிரிவு' },
        ];
        let h = '<i class="bi bi-diagram-3" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('wings_title') + '</strong>';
        h += '<div style="margin-top:12px;border-radius:12px;overflow:hidden;border:1px solid #c8e6c9;">';
        // General Wing header
        h += '<div style="background:linear-gradient(135deg,#1b5e20,#2e7d32);color:#fff;padding:12px 14px;font-weight:700;font-size:0.95rem;text-align:center;">';
        h += L('wings_general');
        h += '</div>';
        // Wings table
        h += '<table style="width:100%;border-collapse:collapse;font-size:0.82rem;">';
        for (let i = 0; i < wings.length; i++) {
          const bg = i % 2 === 0 ? '#f0f9f1' : '#fff';
          const w = wings[i][currentLang] || wings[i]['en'];
          h += '<tr style="background:' + bg + ';"><td style="padding:9px 12px;border-bottom:1px solid #e8f5e9;">';
          h += '<span style="color:#2e7d32;font-weight:600;margin-right:6px;">' + (i + 1) + '.</span> ' + w;
          h += '</td></tr>';
        }
        h += '</table></div>';
        // Reply buttons
        const wingsLoanApplied = await checkHasAppliedLoan();
        h += '<div class="action-buttons" style="margin-top:12px;">';
        h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
        h += '<button class="action-btn confirm" onclick="doMenuYourMembers()"><i class="bi bi-people"></i> ' + L('sb_your_members') + '</button>';
        h += '</div>';
        h += '<div class="action-buttons" style="margin-top:8px;">';
        h += '<button class="action-btn confirm" onclick="doMenuRefer()"><i class="bi bi-share"></i> ' + L('sb_refer') + '</button>';
        if (!wingsLoanApplied) h += buildLoanBtn();
        h += '</div>';
        await botReply(h, 800);
      };

      window.doMenuHelp = async function () {
        closeSidebar();
        let h = '<i class="bi bi-question-circle" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('help_title') + '</strong>';
        h += '<div style="margin-top:10px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;font-size:0.9rem;line-height:1.6;">';
        h += '<p><i class="bi bi-envelope"></i> <strong>Email:</strong> support@vanigan.org</p>';
        h += '<p><i class="bi bi-telephone"></i> <strong>Phone:</strong> +91 9876543210</p>';
        h += '<p><i class="bi bi-globe"></i> <strong>Website:</strong> <a href="https://vanigan.org" target="_blank" style="color:#2e7d32;">vanigan.org</a></p>';
        h += '<hr style="border:none;border-top:1px solid #e0e0e0;margin:10px 0;">';
        h += '<p style="font-size:0.82rem;color:#888;">' + L('help_contact_hint') + '</p>';
        h += '</div>';
        h += '<div class="action-buttons" style="margin-top:12px;">';
        h += '<a href="mailto:support@vanigan.org" class="action-btn confirm" style="text-decoration:none;"><i class="bi bi-envelope"></i> ' + L('btn_email') + '</a>';
        h += '<a href="tel:+919876543210" class="action-btn confirm" style="text-decoration:none;"><i class="bi bi-telephone"></i> ' + L('btn_call') + '</a>';
        h += '</div>';
        const helpLoanApplied = await checkHasAppliedLoan();
        h += '<div class="action-buttons" style="margin-top:8px;">';
        h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
        h += '<button class="action-btn confirm" onclick="doMenuRefer()"><i class="bi bi-share"></i> ' + L('sb_refer') + '</button>';
        h += '</div>';
        h += '<div class="action-buttons" style="margin-top:8px;">';
        h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
        if (!helpLoanApplied) h += buildLoanBtn();
        h += '</div>';
        await botReply(h, 800);
      };

      window.doMenuWebsite = async function () {
        closeSidebar();
        let h = '<i class="bi bi-globe2" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('website_title') + '</strong>';
        h += '<div style="margin-top:10px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;">';
        h += '<p style="font-size:0.9rem;color:#555;margin-bottom:12px;">' + L('website_msg') + '</p>';
        h += '<a href="https://vanigan.org/" target="_blank" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#2e7d32,#43a047);color:#fff;padding:12px 22px;border-radius:10px;text-decoration:none;font-weight:700;font-size:0.95rem;box-shadow:0 2px 8px rgba(46,125,50,0.3);">';
        h += '<i class="bi bi-globe2" style="font-size:1.1rem;"></i> ' + L('website_btn');
        h += '</a></div>';
        await botReply(h, 800);
      };

      window.doMenuDownload = async function () {
        closeSidebar();
        let h = '<i class="bi bi-phone" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('download_title') + '</strong>';
        h += '<div style="margin-top:10px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;">';
        h += '<p style="font-size:0.9rem;color:#555;margin-bottom:12px;">' + L('download_msg') + '</p>';
        h += '<a href="https://play.google.com/store/apps/details?id=io.vanigan.ai" target="_blank" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#1a1a1a,#333);color:#fff;padding:12px 22px;border-radius:10px;text-decoration:none;font-weight:700;font-size:0.95rem;box-shadow:0 2px 8px rgba(0,0,0,0.3);">';
        h += '<i class="bi bi-google-play" style="font-size:1.1rem;"></i> ' + L('download_btn');
        h += '</a></div>';
        await botReply(h, 800);
      };

      window.doMenuLogout = function () {
        closeSidebar();
        clearUser();
        state = S.WELCOME;
        mobile = ''; epic = ''; voter = null; photoFile = null; photoUrl = '';
        dob = ''; bloodGroup = ''; address = ''; pin = ''; skippedDetails = false; isUpdatingDetails = false;
        chatEl.innerHTML = '';
        // Reset sidebar avatar back to default icon
        const sbAvatar = document.querySelector('.sidebar-header .sb-avatar');
        if (sbAvatar) sbAvatar.innerHTML = '<i class="bi bi-person-fill"></i>';
        setText(L('ph_type_msg'));
        hideAttach();
        addDateChip();
        addBanner();
      };

      /* ── Helper Functions for Unverified Users ── */
      window.doStartVerify = async function () {
        userMsg('<i class="bi bi-telephone"></i> ' + L('btn_verify_mobile'));
        state = S.AWAIT_MOBILE;
        unlockInput();
        setMobileInput();
        await botReply(L('ask_mobile'), 700);
      };

      window.doStartRegister = async function () {
        userMsg('<i class="bi bi-person-plus"></i> ' + L('btn_register'));
        state = S.AWAIT_MOBILE;
        unlockInput();
        setMobileInput();
        await botReply(L('ask_mobile'), 700);
      };

      /* ── Request Loan Flow ── */
      let loanBusinessType = '';
      let loanBusinessName = '';

      window.doRequestLoan = async function () {
        const user = getUser();
        if (!user || !user.memberData || !user.memberData.unique_id) {
          let h = '<i class="bi bi-exclamation-circle" style="color:#f9a825;font-size:1.2rem;"></i> <strong>' + L('btn_request_loan') + '</strong>';
          h += '<div style="margin-top:10px;padding:14px;background:#fff8e1;border-radius:12px;border:1px solid #ffe082;font-size:0.9rem;line-height:1.6;">';
          h += '<p>' + L('unverified_refer_msg') + '</p>';
          h += '</div>';
          h += '<div class="action-buttons" style="margin-top:12px;">';
          h += '<button class="action-btn confirm" onclick="doStartVerify()"><i class="bi bi-telephone"></i> ' + L('btn_verify_mobile') + '</button>';
          h += '<button class="action-btn confirm" onclick="doStartRegister()"><i class="bi bi-person-plus"></i> ' + L('btn_register') + '</button>';
          h += '</div>';
          await botReply(h, 600);
          return;
        }

        // Check if user has already applied for loan from API (by mobile number)
        userMsg('<i class="bi bi-cash-coin"></i> ' + L('btn_request_loan'));
        showTyping();
        try {
          const memberMobile = user.memberData.mobile || user.memberData.contact_number || user.mobile || mobile;
          const res = await api('/api/vanigam/check-loan-status', {
            unique_id: user.memberData.unique_id,
            mobile: memberMobile
          });
          hideTyping();
          if (res && res.success && res.has_applied === true) {
            let h = '<i class="bi bi-check-circle" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('btn_request_loan') + '</strong>';
            h += '<div style="margin-top:10px;padding:14px;background:#e8f5e9;border-radius:12px;border:1px solid #c8e6c9;font-size:0.9rem;line-height:1.6;">';
            h += '<p>' + L('loan_already_applied') + '</p>';
            if (res.loan_request && res.loan_request.business_name) {
              h += '<p style="margin-top:8px;font-size:0.8rem;color:#666;"><strong>Business:</strong> ' + res.loan_request.business_name + ' (' + res.loan_request.business_type + ')</p>';
            }
            h += '</div>';
            h += '<div class="action-buttons" style="margin-top:12px;">';
            h += '<a href="mailto:support@vanigan.org" class="action-btn confirm" style="text-decoration:none;"><i class="bi bi-envelope"></i> ' + L('btn_email') + '</a>';
            h += '<a href="tel:+919876543210" class="action-btn confirm" style="text-decoration:none;"><i class="bi bi-telephone"></i> ' + L('btn_call') + '</a>';
            h += '</div>';
            h += '<div class="action-buttons" style="margin-top:8px;">';
            h += '<button class="action-btn confirm" onclick="doMenuRefer()"><i class="bi bi-share"></i> ' + L('sb_refer') + '</button>';
            h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
            h += '</div>';
            h += '<div class="action-buttons" style="margin-top:8px;">';
            h += '<button class="action-btn confirm" onclick="doMenuYourMembers()"><i class="bi bi-people"></i> ' + L('sb_your_members') + '</button>';
            h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
            h += '</div>';
            await botReply(h, 600);
            return;
          }
        } catch(e) {
          hideTyping();
          console.error('Loan status check error:', e);
          // Continue if API fails
        }

        let h = '<i class="bi bi-cash-coin" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('btn_request_loan') + '</strong>';
        h += '<div style="margin-top:10px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;font-size:0.9rem;line-height:1.6;">';
        h += '<p>' + L('loan_intro') + '</p>';
        h += '</div>';
        h += '<div class="action-buttons" style="margin-top:12px;">';
        h += '<button class="action-btn confirm" onclick="doLoanYes()"><i class="bi bi-check-lg"></i> ' + L('btn_yes') + '</button>';
        h += '<button class="action-btn cancel" onclick="doLoanNo()"><i class="bi bi-x-lg"></i> ' + L('btn_no') + '</button>';
        h += '</div>';
        await botReply(h, 800);
      };

      window.doLoanYes = async function () {
        userMsg('<i class="bi bi-check-lg"></i> ' + L('btn_yes'));
        let h = '<i class="bi bi-building" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('loan_great') + '</strong>';
        h += '<div class="action-buttons" style="margin-top:12px;flex-direction:column;">';
        h += '<button class="action-btn confirm" onclick="doSelectBusinessType(\'Pvt Ltd Company\')" style="width:100%;justify-content:center;"><i class="bi bi-building"></i> ' + L('btn_pvt_ltd') + '</button>';
        h += '<button class="action-btn confirm" onclick="doSelectBusinessType(\'Partnership Business\')" style="width:100%;justify-content:center;"><i class="bi bi-people"></i> ' + L('btn_partnership') + '</button>';
        h += '<button class="action-btn confirm" onclick="doSelectBusinessType(\'Import Export Business\')" style="width:100%;justify-content:center;"><i class="bi bi-globe2"></i> ' + L('btn_import_export') + '</button>';
        h += '</div>';
        await botReply(h, 800);
      };

      window.doLoanNo = async function () {
        userMsg('<i class="bi bi-x-lg"></i> ' + L('btn_no'));
        let h = '<i class="bi bi-info-circle" style="color:#2e7d32;font-size:1.2rem;"></i>';
        h += '<div style="margin-top:10px;padding:14px;background:#f0f9f1;border-radius:12px;border:1px solid #c8e6c9;font-size:0.9rem;line-height:1.6;">';
        h += '<p>' + L('loan_no_eligible') + '</p>';
        h += '</div>';
        h += buildQuickActions();
        await botReply(h, 800);
      };

      window.doSelectBusinessType = async function (type) {
        loanBusinessType = type;
        const displayType = type === 'Pvt Ltd Company' ? L('btn_pvt_ltd') : type === 'Partnership Business' ? L('btn_partnership') : L('btn_import_export');
        userMsg('<i class="bi bi-building"></i> ' + displayType);
        state = S.LOAN_BUSINESS_NAME;
        let h = '<i class="bi bi-pencil" style="color:#2e7d32;font-size:1.2rem;"></i> <strong>' + L('loan_enter_business_name') + '</strong>';
        h += '<div style="margin-top:10px;">';
        h += '<input type="text" id="businessNameInput" placeholder="Enter business name" style="width:100%;padding:12px 16px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;">';
        h += '</div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="doSubmitBusinessName()" style="width:100%;"><i class="bi bi-check-lg"></i> ' + L('btn_submit') + '</button></div>';
        await botReply(h, 800);
        setTimeout(() => { const inp = document.getElementById('businessNameInput'); if (inp) inp.focus(); }, 900);
      };

      window.doSubmitBusinessName = async function () {
        const inp = document.getElementById('businessNameInput');
        if (!inp || !inp.value.trim()) {
          inp.style.borderColor = '#d32f2f';
          inp.focus();
          return;
        }
        loanBusinessName = inp.value.trim();
        userMsg('<i class="bi bi-building"></i> ' + loanBusinessName);
        state = S.DONE;
        showTyping();

        // Submit loan request to API
        const user = getUser();
        let submitSuccess = false;
        try {
          const res = await api('/api/vanigam/loan-request', {
            unique_id: user.memberData.unique_id,
            business_type: loanBusinessType,
            business_name: loanBusinessName
          });
          if (res && res.success) {
            submitSuccess = true;
          }
        } catch(e) {
          console.error('Loan submission error:', e);
        }

        hideTyping();

        let h = '<i class="bi bi-check-circle" style="color:#2e7d32;font-size:1.5rem;"></i> <strong style="color:#2e7d32;">' + L('loan_thanks') + '</strong>';
        h += '<div class="action-buttons" style="margin-top:12px;">';
        h += '<button class="action-btn confirm" onclick="doMenuRefer()"><i class="bi bi-share"></i> ' + L('sb_refer') + '</button>';
        h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
        h += '</div>';
        h += '<div class="action-buttons" style="margin-top:8px;">';
        h += '<button class="action-btn confirm" onclick="doMenuYourMembers()"><i class="bi bi-people"></i> ' + L('sb_your_members') + '</button>';
        h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
        h += '</div>';
        await botReply(h, 1000);
        loanBusinessType = '';
        loanBusinessName = '';
      };

      async function checkHasAppliedLoan() {
        const user = getUser();
        if (!user || !user.memberData || !user.memberData.unique_id) return false;
        try {
          const memberMobile = user.memberData.mobile || user.memberData.contact_number || user.mobile || mobile;
          const res = await api('/api/vanigam/check-loan-status', { unique_id: user.memberData.unique_id, mobile: memberMobile });
          return !!(res && res.success && res.has_applied === true);
        } catch(e) { return false; }
      }

      function buildLoanBtn() {
        return '<button class="action-btn confirm" onclick="doRequestLoan()"><i class="bi bi-cash-coin"></i> ' + L('btn_request_loan') + '</button>';
      }

      function buildQuickActions() {
        let h = '<div class="action-buttons" style="margin-top:12px;">';
        h += '<button class="action-btn confirm" onclick="doMenuRefer()"><i class="bi bi-share"></i> ' + L('sb_refer') + '</button>';
        h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
        h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
        h += '</div>';
        return h;
      }

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
        let h = L('lets_complete') + '<br><br>';
        h += L('select_dob');
        h += '<div style="margin-top:10px;"><input type="date" id="dobPicker" max="' + new Date().toISOString().split('T')[0] + '" style="width:100%;padding:10px 14px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;-webkit-appearance:none;appearance:none;height:48px;line-height:1.2;"></div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitDob()" style="width:100%;"><i class="bi bi-check-lg"></i> ' + L('btn_confirm_dob') + '</button></div>';
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
        let h = L('lets_complete') + '<br><br>';
        h += L('select_dob');
        h += '<div style="margin-top:10px;"><input type="date" id="dobPicker" max="' + new Date().toISOString().split('T')[0] + '" style="width:100%;padding:10px 14px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;-webkit-appearance:none;appearance:none;height:48px;line-height:1.2;"></div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitDob()" style="width:100%;"><i class="bi bi-check-lg"></i> ' + L('btn_confirm_dob') + '</button></div>';
        await botReply(h, 700);
      };

      window.doSaveUpdatedDetails = async function () {
        userMsg('<i class="bi bi-check-lg"></i> Save Details');
        lockInput(); showTyping();
        try {
          const _user = getUser();
          const res = await api('/api/vanigam/save-details', {
            epic_no: epic,
            dob: dob,
            blood_group: bloodGroup,
            address: address,
            unique_id: (_user && _user.memberData) ? _user.memberData.unique_id : '',
          });
          hideTyping();
          if (res.success && res.member) {
            state = S.DONE;
            unlockInput();
            isUpdatingDetails = false;
            saveUser({ mobile, epic, hasCard: true, memberData: res.member });
            let h = L('details_updated');
            h += buildCardPreviewHtml(res.member);
            await botReply(h, 1200);
            updateSidebarContent();
            // Capture and upload card images directly (no iframe)
            captureAndUploadCard(res.member);
          } else {
            unlockInput();
            await botReply('<i class="bi bi-x-circle"></i> ' + (res.message || L('failed_save')), 600);
          }
        } catch(e) {
          hideTyping(); unlockInput();
          await botReply(L('something_wrong'), 600);
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
          '<div class="bot-avatar-img" style="width:38px;height:38px;border-radius:50%;flex-shrink:0;overflow:hidden;"><img src="/vaniganlogo.png" alt="Vanigam" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div>' +
          '<div class="bubble">' +
          '<div class="banner-text">' + L('banner_welcome') + '<br><br>' +
          '<div style="margin:10px 0;border-radius:12px;overflow:hidden;border:1px solid #c8e6c9;"><video src="https://res.cloudinary.com/de3qyhqfg/video/upload/v1773815558/vanigan/welcome_video.mp4" controls playsinline preload="metadata" style="width:100%;display:block;border-radius:12px;"></video></div>' +
          L('banner_hello') + '<br><br>' +
          '<em style="color:#667781;font-size:0.85rem;">' + L('banner_tap_start') + '</em>' +
          '<span class="time">' + now() + '</span></div>' +
          '<div class="banner-action"><button class="btn-reply" id="bannerStartBtn"><i class="bi bi-play-circle-fill me-1"></i> ' + L('btn_start') + '</button></div>' +
          '</div>';
        chatEl.appendChild(div);
        document.getElementById('bannerStartBtn').onclick = function () {
          this.disabled = true;
          this.innerHTML = '<span class="gen-spinner"></span> ' + L('btn_starting');
          // Directly transition to AWAIT_MOBILE state, skipping "Hi" message
          state = S.AWAIT_MOBILE;
          setMobileInput();
          unlockInput();
          setTimeout(() => {
            botReply(L('ask_mobile'), 800);
          }, 500);
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
        typingEl.innerHTML = '<div class="bot-avatar-img" style="width:38px;height:38px;border-radius:50%;flex-shrink:0;overflow:hidden;"><img src="/vaniganlogo.png" alt="Vanigam" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div><div class="bubble"><div class="typing-dots"><span></span><span></span><span></span></div></div>';
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

      function setNumeric(ph) { input.type = 'tel'; input.inputMode = 'numeric'; input.pattern = '[0-9]*'; input.placeholder = ph || ''; input.oninput = null; input.onkeyup = null; }
      function setText(ph) { input.type = 'text'; input.inputMode = 'text'; input.autocapitalize = 'off'; input.removeAttribute('pattern'); input.placeholder = ph || ''; input.oninput = null; input.onkeyup = null; }

      /* ── EPIC Format Validation (3 Alpha + 7 Numeric) ──
       * Format: Exactly 3 uppercase alphabetic characters followed by exactly 7 numeric digits
       * Example: AYR0489518
       * Regex: /^[A-Z]{3}[0-9]{7}$/
       */
      function isValidEpicFormat(epic) {
        // Strict validation: exactly 3 uppercase letters + exactly 7 digits = 10 chars total
        const epicRegex = /^[A-Z]{3}[0-9]{7}$/;
        return epic && epic.length === 10 && epicRegex.test(epic);
      }

      function setEpicInput(ph) {
        input.type = 'text';
        input.inputMode = 'text';
        input.autocapitalize = 'characters';
        input.removeAttribute('pattern');
        input.placeholder = ph || '';
        input.maxLength = 10; // Block input beyond 10 characters
        sendBtn.disabled = true; // Button DISABLED by default

        // Clear any previous handlers
        input.oninput = null;
        input.onkeyup = null;

        // Add real-time validation listener on every keystroke
        input.oninput = function () {
          let val = input.value.toUpperCase();
          // Only allow A-Z for first 3 chars, then 0-9 for remaining 7 chars
          let alphaChars = '';
          let numChars = '';

          for (let i = 0; i < val.length && (alphaChars.length + numChars.length) < 10; i++) {
            const char = val[i];
            if (alphaChars.length < 3) {
              // First 3 chars must be A-Z only
              if (/[A-Z]/.test(char)) {
                alphaChars += char;
              }
            } else if (numChars.length < 7) {
              // Last 7 chars must be 0-9 only
              if (/[0-9]/.test(char)) {
                numChars += char;
              }
            }
          }

          input.value = alphaChars + numChars;

          // Enable send button ONLY when exactly 10 chars in correct format (3 alpha + 7 numeric)
          // Partial matches (6, 8, 9 chars) should remain DISABLED
          sendBtn.disabled = !isValidEpicFormat(input.value);
        };
      }

      function setMobileInput() {
        setNumeric(L('ph_mobile'));
        input.onkeyup = null;
        input.oninput = null;
        sendBtn.disabled = false;
      }

      function isWeakPin(p) {
        const blacklist = ['0000','1111','2222','3333','4444','5555','6666','7777','8888','9999','1234','4321','1212','2121','1122','2211','0123','3210','5678','8765','6789','9876','0987','7890','1010','2020','6969','1357','2468'];
        if (blacklist.includes(p)) return true;
        if (/^(\d)\1{3}$/.test(p)) return true;
        return false;
      }

      function showAttach() { attachBtn.classList.add('visible'); }
      function hideAttach() { attachBtn.classList.remove('visible'); }
      function lockInput() { input.disabled = true; sendBtn.disabled = true; }
      function unlockInput() { input.disabled = false; if (state !== S.AWAIT_EPIC && state !== S.AWAIT_PIN && state !== S.AWAIT_PIN_CONFIRM && state !== S.AWAIT_RETURNING_PIN) sendBtn.disabled = false; input.focus(); }

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

      /* ── Registration Tracking (fire-and-forget, non-blocking) ── */
      function trackStep(step, extra = {}) {
        if (!mobile) return;
        const body = { mobile, step, ...extra };
        if (referrerUniqueId) body.referrer_unique_id = referrerUniqueId;
        fetch('/api/vanigam/track-step', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body)
        }).catch(() => {}); // silently ignore tracking errors
      }

      /* ── OTP Resend ── */
      window.doResendOtp = async function () {
        const btn = document.getElementById('resendOtpBtn');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }
        lockInput(); showTyping();
        try {
          const res = await api('/api/vanigam/send-otp', { mobile });
          hideTyping();
          if (res.success) { botMsg(L('otp_resent')); startResendTimer(); }
          else { botMsg(L('otp_resend_fail')); if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> ' + L('btn_resend_otp'); } }
        } catch(e) { hideTyping(); botMsg(L('otp_resend_error')); if (btn) { btn.disabled = false; btn.style.opacity = '1'; } }
        unlockInput();
      };

      window.doChangeMobile = function () {
        if (window.resendTimer) clearInterval(window.resendTimer);
        const rBtn = document.getElementById('resendOtpBtn');
        const cBtn = document.getElementById('changeMobileBtn');
        if (rBtn) { rBtn.disabled = true; rBtn.removeAttribute('id'); }
        if (cBtn) { cBtn.disabled = true; cBtn.removeAttribute('id'); }
        state = S.AWAIT_MOBILE; mobile = '';
        setNumeric(L('ph_mobile'));
        botReply(L('ask_mobile'), 500);
      };

      function startResendTimer() {
        let timeLeft = 30;
        const btn = document.getElementById('resendOtpBtn');
        if (!btn) return;
        btn.disabled = true;
        btn.innerText = L('btn_resend_otp') + ' (' + timeLeft + 's)';
        if (window.resendTimer) clearInterval(window.resendTimer);
        window.resendTimer = setInterval(() => {
          timeLeft--;
          const cur = document.getElementById('resendOtpBtn');
          if (!cur) { clearInterval(window.resendTimer); return; }
          if (timeLeft <= 0) { clearInterval(window.resendTimer); cur.disabled = false; cur.style.opacity = '1'; cur.innerHTML = '<i class="bi bi-arrow-clockwise"></i> ' + L('btn_resend_otp'); }
          else { cur.innerHTML = '<i class="bi bi-arrow-clockwise"></i> ' + L('btn_resend_otp') + ' (' + timeLeft + 's)'; }
        }, 1000);
      }

      /* ── Init ── */
      async function init() {
        // Apply stored language on load
        if (currentLang !== 'en') {
          document.querySelector('.chat-header .info h4').textContent = L('header_title');
          document.querySelector('.chat-header .info .status').textContent = L('header_subtitle');
        }
        addDateChip();
        const saved = getUser();
        if (saved && saved.mobile && saved.hasCard && saved.memberData) {
          mobile = saved.mobile;
          epic = saved.epic || '';
          state = S.DONE;
          setText(L('ph_type_msg'));
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

          let h = L('welcome_back_card');
          h += buildCardPreviewHtml(m);
          if (!m.details_completed) {
            h += '<div style="margin-top:12px;padding:12px;background:rgba(255,152,0,0.1);border-radius:10px;border:1px solid rgba(255,152,0,0.3);">';
            h += '<span style="color:#e65100;font-size:0.9rem;"><i class="bi bi-exclamation-triangle"></i> ' + L('details_skipped') + '</span>';
            h += '<div style="margin-top:8px;">';
            h += '<button class="action-btn confirm" onclick="doUpdateDetailsFromCard()" style="font-size:0.85rem;padding:8px 16px;">';
            h += '<i class="bi bi-pencil-square"></i> ' + L('btn_update_now');
            h += '</button>';
            h += '</div></div>';
          }
          h += '<div style="margin-top:12px;"><em style="color:#667781;font-size:0.85rem;">' + L('type_anything_hint') + '</em></div>';
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
          setNumeric(L('ph_mobile'));
          await botReply(L('ask_mobile'), 900);

        /* ── AWAIT MOBILE ── */
        } else if (state === S.AWAIT_MOBILE) {
          const m = txt.replace(/\D/g, '');
          if (m.length !== 10 || !/^[6-9]/.test(m)) {
            userMsg(txt);
            await botReply(L('invalid_mobile'), 600);
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
              h += '<i class="bi bi-exclamation-octagon" style="color:#d32f2f;font-size:1.2rem;"></i> <strong style="color:#d32f2f;">' + L('self_referral_title') + '</strong>';
              h += '<p style="margin-top:6px;font-size:0.85rem;color:#555;">' + L('self_referral_msg') + '</p>';
              h += '<button class="action-btn confirm" onclick="doChangeMobile()" style="margin-top:10px;font-size:0.85rem;padding:8px 16px;"><i class="bi bi-telephone"></i> ' + L('btn_enter_another') + '</button>';
              h += '</div>';
              await botReply(h, 800);
              return;
            }

            // Already registered check (during referral flow)
            if (referrerUniqueId && checkRes.success && checkRes.exists) {
              mobile = '';
              unlockInput();
              let h = '<div style="padding:14px;background:rgba(255,152,0,0.08);border-radius:12px;border:1px solid rgba(255,152,0,0.3);">';
              h += '<i class="bi bi-person-check" style="color:#e65100;font-size:1.2rem;"></i> <strong style="color:#e65100;">' + L('already_registered_title') + '</strong>';
              h += '<p style="margin-top:6px;font-size:0.85rem;color:#555;">' + L('already_registered_msg') + '</p>';
              h += '<button class="action-btn confirm" onclick="doChangeMobile()" style="margin-top:10px;font-size:0.85rem;padding:8px 16px;"><i class="bi bi-telephone"></i> ' + L('btn_enter_another') + '</button>';
              h += '</div>';
              await botReply(h, 800);
              return;
            }

            if (checkRes.success && checkRes.exists && checkRes.has_pin) {
              // Returning user with PIN — skip OTP, ask PIN directly
              state = S.AWAIT_RETURNING_PIN;
              setNumeric(L('ph_pin'));
              unlockInput();
              sendBtn.disabled = true;
              let welcomeText = L('welcome_back_pin', { name: checkRes.name ? ', <strong>' + checkRes.name + '</strong>' : '' });
              await botReply(welcomeText, 800);
            } else {
              // New user or no PIN — send OTP
              showTyping();
              const res = await api('/api/vanigam/send-otp', { mobile: m });
              hideTyping();
              if (res.success) {
                trackStep('mobile_entered');
                state = S.AWAIT_OTP;
                setNumeric(L('ph_otp'));
                unlockInput();
                let askText = L('otp_sent', { mobile: m });
                askText += '<div class="action-buttons" style="margin-top:12px;flex-direction:column;">';
                askText += '<button class="action-btn confirm" id="resendOtpBtn" onclick="doResendOtp()" disabled style="font-size:0.85rem;padding:8px 16px;opacity:0.6;"><i class="bi bi-arrow-clockwise"></i> ' + L('btn_resend_otp') + ' (30s)</button>';
                askText += '<button class="action-btn confirm" id="changeMobileBtn" onclick="doChangeMobile()" style="font-size:0.85rem;padding:8px 16px;"><i class="bi bi-telephone"></i> ' + L('btn_change_mobile') + '</button>';
                askText += '</div>';
                await botReply(askText, 800);
                startResendTimer();
              } else {
                unlockInput();
                await botReply('\u274C ' + (res.message || L('otp_send_fail')), 600);
              }
            }
          } catch (e) { hideTyping(); unlockInput(); await botReply(L('something_wrong'), 600); }

        /* ── AWAIT OTP ── */
        } else if (state === S.AWAIT_OTP) {
          const o = txt.replace(/\D/g, '');
          if (o.length !== 6) {
            userMsg(txt);
            await botReply(L('invalid_otp'), 600);
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
                setText(L('ph_type_msg'));
                hideAttach();
                unlockInput();
                epic = res.member.epic_no || '';
                saveUser({ mobile, epic, hasCard: true, memberData: res.member });
                let h = L('mobile_verified_existing');
                h += '<div class="member-summary"><h4>\uD83C\uDFAA ' + L('sb_vanigam_member') + '</h4>';
                h += '<div class="row"><span class="lbl">Name</span><span class="val">' + (res.member.name || '') + '</span></div>';
                h += '<div class="row"><span class="lbl">Member ID</span><span class="val">' + (res.member.unique_id || '') + '</span></div>';
                h += '</div>';
                if (!res.member.details_completed) {
                  h += '<br><em style="color:#667781;">' + L('details_incomplete_hint') + '</em>';
                }
                await botReply(h, 1000);
              } else {
                // New member — ask EPIC
                trackStep('otp_verified');
                state = S.AWAIT_EPIC;
                setEpicInput(L('ph_epic'));
                unlockInput();
                await botReply(L('mobile_verified_epic'), 800);
              }
            } else {
              unlockInput();
              await botReply('\u274C ' + (res.message || L('invalid_otp_retry')), 600);
            }
          } catch (e) { hideTyping(); unlockInput(); await botReply(L('verification_failed'), 600); }

        /* ── AWAIT EPIC ── */
        } else if (state === S.AWAIT_EPIC) {
          const ep = txt.trim().toUpperCase();
          // Validate EPIC format: exactly 3 alpha + 7 numeric (10 chars total)
          if (!isValidEpicFormat(ep)) {
            userMsg(ep || txt);
            await botReply(L('invalid_epic_format'), 600);
            return;
          }
          userMsg(ep);
          lockInput();
          try {
            showTyping();
            const res = await api('/api/vanigam/validate-epic', { epic_no: ep });
            hideTyping();
            if (res.success) {
              epic = ep;
              voter = res.voter;
              trackStep('epic_validated', { epic_no: ep, name: voter.name || '', assembly: voter.assembly_name || '', district: voter.district || '' });
              let h = L('voter_found') + '<div class="voter-details-card">';
              const fields = [
                ['Name', voter.name || ''],
                ['EPIC No', voter.epic_no || ep],
                ['Assembly', voter.assembly_name || ''],
                ['District', voter.district || ''],
                ['Zone', voter.zone || '']
              ];
              for (const [lbl, v] of fields) {
                if (!v || !v.trim()) continue;
                h += '<div class="detail-row"><span class="detail-label">' + lbl + '</span><span class="detail-value">' + v.trim() + '</span></div>';
              }
              h += '</div>';
              h += '<br>' + L('is_this_correct');
              h += '<div class="action-buttons">';
              h += '<button class="action-btn confirm" onclick="confirmVoter()"><i class="bi bi-check-lg"></i> ' + L('btn_yes_correct') + '</button>';
              h += '<button class="action-btn cancel" onclick="rejectVoter()"><i class="bi bi-x-lg"></i> ' + L('btn_no_reenter') + '</button>';
              h += '</div>';
              state = S.VOTER_CONFIRM;
              unlockInput();
              await botReply(h, 1000);
            } else {
              // EPIC not found - offer options to try again or add manually
              epic = ep; // Save entered EPIC for manual flow
              state = S.AWAIT_EPIC;
              unlockInput();
              let h = L('epic_not_found_manual');
              h += '<div class="action-buttons" style="margin-top:12px;">';
              h += '<button class="action-btn confirm" onclick="retryEpic()" style="font-size:0.9rem;"><i class="bi bi-arrow-clockwise"></i> ' + L('btn_try_again') + '</button>';
              h += '<button class="action-btn cancel" onclick="startManualEntry()" style="font-size:0.9rem;"><i class="bi bi-pencil-square"></i> ' + L('btn_add_manually') + '</button>';
              h += '</div>';
              await botReply(h, 700);
            }
          } catch (e) { hideTyping(); unlockInput(); await botReply('\u274C ' + L('validate_fail'), 600); }

        /* ── VOTER CONFIRM ── */
        } else if (state === S.VOTER_CONFIRM) {
          const lo = txt.toLowerCase();
          if (lo === 'yes' || lo === 'y' || lo === 'correct') {
            userMsg(txt);
            await startPhotoUpload();
          } else if (lo === 'no' || lo === 'n') {
            userMsg(txt);
            state = S.AWAIT_EPIC;
            setEpicInput(L('ph_epic'));
            await botReply(L('reenter_epic'), 500);
          } else {
            userMsg(txt);
            await botReply(L('yes_or_no'), 400);
          }

        /* ── AWAIT PHOTO ── */
        } else if (state === S.AWAIT_PHOTO) {
          // User typed instead of uploading photo
          userMsg(txt);
          await botReply(L('please_upload_photo'), 500);

        /* ── AWAIT PIN ── */
        } else if (state === S.AWAIT_PIN) {
          const p = txt.replace(/\D/g, '');
          if (p.length !== 4) {
            userMsg(txt);
            await botReply(L('pin_4digits'), 500);
            return;
          }
          if (isWeakPin(p)) {
            userMsg('\u2022\u2022\u2022\u2022');
            await botReply(L('weak_pin'), 500);
            return;
          }
          userMsg('\u2022\u2022\u2022\u2022');
          pin = p;
          state = S.AWAIT_PIN_CONFIRM;
          setNumeric(L('ph_reenter_pin'));
          await botReply(L('confirm_pin'), 700);

        } else if (state === S.AWAIT_PIN_CONFIRM) {
          const p = txt.replace(/\D/g, '');
          userMsg('\u2022\u2022\u2022\u2022');
          if (p !== pin) {
            state = S.AWAIT_PIN;
            setNumeric(L('ph_pin'));
            pin = '';
            await botReply(L('pin_mismatch'), 600);
            return;
          }
          trackStep('pin_set');
          await botReply(L('pin_set_success'), 500);
          await askAdditionalDetails();

        /* ── AWAIT RETURNING PIN ── */
        } else if (state === S.AWAIT_RETURNING_PIN) {
          const p = txt.replace(/\D/g, '');
          if (p.length !== 4) {
            userMsg(txt);
            await botReply(L('enter_4digit_pin'), 500);
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
              let h = L('welcome_back_card');
              h += buildCardPreviewHtml(m);
              if (!m.details_completed) {
                h += '<div style="margin-top:12px;padding:12px;background:rgba(255,152,0,0.1);border-radius:10px;border:1px solid rgba(255,152,0,0.3);">';
                h += '<span style="color:#e65100;font-size:0.9rem;"><i class="bi bi-exclamation-triangle"></i> ' + L('details_skipped') + '</span>';
                h += '<div style="margin-top:8px;">';
                h += '<button class="action-btn confirm" onclick="doUpdateDetailsFromCard()" style="font-size:0.85rem;padding:8px 16px;">';
                h += '<i class="bi bi-pencil-square"></i> ' + L('btn_update_now');
                h += '</button>';
                h += '</div></div>';
              }
              await botReply(h, 1200);
            } else {
              unlockInput();
              await botReply('<i class="bi bi-x-circle"></i> ' + (res.message || L('invalid_pin')) + ' ' + L('verification_failed').replace('\u274C ', ''), 600);
            }
          } catch(e) {
            hideTyping(); unlockInput();
            await botReply(L('verification_failed'), 600);
          }

        /* ── ASK ADDITIONAL ── */
        } else if (state === S.ASK_ADDITIONAL) {
          // Handled by buttons
          userMsg(txt);
          const lo = txt.toLowerCase();
          if (lo === 'add' || lo === 'yes') { await startAdditionalDetails(); }
          else if (lo === 'skip') { await skipAdditionalDetails(); }
          else { await botReply(L('tap_add_or_skip'), 400); }

        /* ── AWAIT DOB ── */
        } else if (state === S.AWAIT_DOB) {
          // Handled by calendar picker button (submitDob)
          if (!txt) return;
          userMsg(txt);
          await botReply(L('use_calendar'), 400);

        /* ── AWAIT BLOOD ── */
        } else if (state === S.AWAIT_BLOOD) {
          // Handled by blood group buttons (submitBloodGroup)
          if (!txt) return;
          userMsg(txt);
          await botReply(L('use_blood_buttons'), 400);

        /* ── AWAIT ADDRESS ── */
        } else if (state === S.AWAIT_ADDRESS) {
          // Handled by address textarea (submitAddress)
          if (!txt) return;
          userMsg(txt);
          await botReply(L('use_address_box'), 400);

        /* ── CONFIRM ALL ── */
        } else if (state === S.CONFIRM_ALL) {
          const lo = txt.toLowerCase();
          if (lo === 'yes' || lo === 'confirm' || lo === 'y') {
            userMsg(txt);
            await doGenerateCard();
          } else if (lo === 'no' || lo === 'cancel' || lo === 'n') {
            userMsg(txt);
            state = S.AWAIT_EPIC;
            setEpicInput(L('ph_epic'));
            hideAttach();
            photoFile = null; photoUrl = ''; dob = ''; bloodGroup = ''; address = ''; skippedDetails = false;
            await botReply(L('cancelled_start_over'), 700);
          } else {
            userMsg(txt);
            await botReply(L('yes_to_confirm'), 500);
          }

        /* ── AWAIT MANUAL NAME ── */
        } else if (state === S.AWAIT_MANUAL_NAME) {
          // Name is handled via inline input + submitManualName button
          userMsg(txt);
          await botReply(L('enter_name'), 500);

        /* ── AWAIT MANUAL ASSEMBLY ── */
        } else if (state === S.AWAIT_MANUAL_ASSEMBLY) {
          // Assembly is handled via inline search + submitManualAssembly button
          userMsg(txt);
          await botReply(L('enter_assembly'), 500);

        /* ── MANUAL CONFIRM ── */
        } else if (state === S.MANUAL_CONFIRM) {
          const lo = txt.toLowerCase();
          if (lo === 'yes' || lo === 'confirm' || lo === 'y' || lo === 'ஆம்' || lo === 'உறுதிப்படுத்து') {
            userMsg('✓ ' + L('btn_confirm_proceed'));
            await startPhotoUpload();
          } else {
            userMsg(txt);
            await botReply(L('confirm_to_proceed'), 500);
          }

        /* ── DONE ── */
        } else if (state === S.DONE) {
          userMsg(txt);
          state = S.AWAIT_MOBILE;
          setMobileInput();
          hideAttach();
          photoFile = null; photoUrl = ''; dob = ''; bloodGroup = ''; address = ''; skippedDetails = false;
          await botReply(L('ready_another'), 800);
        }
      }

      /* ── Confirm voter details ── */
      window.confirmVoter = async function () {
        userMsg('<i class="bi bi-check-lg"></i> ' + L('btn_yes_correct'));
        await startPhotoUpload();
      };
      window.rejectVoter = async function () {
        userMsg('<i class="bi bi-x-lg"></i> ' + L('btn_no_reenter'));
        state = S.AWAIT_EPIC;
        setEpicInput(L('ph_epic'));
        await botReply(L('reenter_epic'), 500);
      };

      /* ── Manual Entry (when EPIC not found) ── */
      window.retryEpic = async function () {
        userMsg(L('btn_try_again'));
        state = S.AWAIT_EPIC;
        setEpicInput(L('ph_epic'));
        await botReply(L('mobile_verified_epic'), 600);
      };

      window.startManualEntry = async function () {
        userMsg(L('btn_add_manually'));
        state = S.AWAIT_MANUAL_NAME;
        lockInput();
        let h = '<i class="bi bi-person-badge" style="color:#2e7d32;font-size:1.2rem;"></i> ' + L('manual_epic_info').replace('{epic}', epic);
        h += '<div style="margin-top:10px;">' + L('enter_name') + '</div>';
        h += '<div style="margin-top:10px;position:relative;">';
        h += '<input type="text" id="manualNameInput" maxlength="50" placeholder="' + L('ph_name') + '" style="width:100%;padding:12px 16px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;">';
        h += '<div id="nameCharCount" style="text-align:right;font-size:0.75rem;color:#888;margin-top:2px;">0 / 50</div>';
        h += '</div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitManualName()" style="width:100%;"><i class="bi bi-check-lg"></i> ' + L('btn_submit') + '</button></div>';
        await botReply(h, 600);
        setTimeout(() => {
          const inp = document.getElementById('manualNameInput');
          if (inp) {
            inp.focus();
            inp.addEventListener('input', function() {
              const cnt = document.getElementById('nameCharCount');
              if (cnt) { cnt.textContent = inp.value.length + ' / 50'; cnt.style.color = inp.value.length >= 45 ? '#e65100' : '#888'; }
            });
          }
        }, 700);
      };

      window.submitManualName = async function () {
        const inp = document.getElementById('manualNameInput');
        if (!inp || !inp.value.trim()) return;
        const name = inp.value.trim().substring(0, 50);
        if (name.length < 2) { await botReply(L('invalid_name'), 500); return; }
        inp.disabled = true;
        const btn = inp.closest('.bot').querySelector('.action-btn');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.5'; }
        userMsg(name);
        voter = { name: name, assembly_name: '', district: '', manually_entered: true, epic_no: epic };
        state = S.AWAIT_MANUAL_ASSEMBLY;
        lockInput();
        // Build assembly live search
        let h = L('enter_assembly');
        h += '<div style="margin-top:10px;position:relative;">';
        h += '<input type="text" id="assemblySearchInput" autocomplete="off" placeholder="' + L('ph_assembly') + '" style="width:100%;padding:12px 16px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;">';
        h += '<div id="assemblySuggestions" style="display:none;position:absolute;top:100%;left:0;right:0;max-height:200px;overflow-y:auto;background:#fff;border:2px solid #2e7d32;border-top:none;border-radius:0 0 10px 10px;z-index:100;box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>';
        h += '</div>';
        h += '<div id="assemblyDistrictZone" style="display:none;margin-top:8px;padding:10px 14px;background:#e8f5e9;border-radius:10px;font-size:0.85rem;color:#1b5e20;"></div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" id="assemblySubmitBtn" onclick="submitManualAssembly()" style="width:100%;opacity:0.5;" disabled><i class="bi bi-check-lg"></i> ' + L('btn_submit') + '</button></div>';
        await botReply(h, 600);
        setTimeout(() => { initAssemblySearch(); }, 700);
      };

      function initAssemblySearch() {
        const inp = document.getElementById('assemblySearchInput');
        const sugBox = document.getElementById('assemblySuggestions');
        const dzBox = document.getElementById('assemblyDistrictZone');
        const submitBtn = document.getElementById('assemblySubmitBtn');
        if (!inp || !sugBox) return;
        // Build assembly list from JS_ZONE_DATA
        const assemblies = Object.keys(JS_ZONE_DATA).map(k => ({ name: toTitleCase(k), key: k }));
        let selectedAssembly = '';

        inp.addEventListener('input', function() {
          const q = inp.value.trim().toLowerCase();
          selectedAssembly = '';
          if (submitBtn) { submitBtn.disabled = true; submitBtn.style.opacity = '0.5'; }
          if (dzBox) dzBox.style.display = 'none';
          if (q.length < 1) { sugBox.style.display = 'none'; return; }
          const matches = assemblies.filter(a => a.name.toLowerCase().includes(q)).slice(0, 15);
          if (matches.length === 0) { sugBox.style.display = 'none'; return; }
          let sh = '';
          matches.forEach(m => {
            const dz = JS_ZONE_DATA[m.key];
            sh += '<div class="asm-suggestion" style="padding:10px 14px;cursor:pointer;border-bottom:1px solid #e8f5e9;font-size:0.9rem;transition:background 0.15s;" onmouseover="this.style.background=\'#e8f5e9\'" onmouseout="this.style.background=\'#fff\'" onclick="selectAssembly(\'' + m.key.replace(/'/g, "\\'") + '\')">';
            sh += '<strong style="color:#1b5e20;">' + m.name + '</strong>';
            if (dz) sh += ' <span style="font-size:0.75rem;color:#666;">- ' + toTitleCase(dz.d) + ', ' + toTitleCase(dz.z) + '</span>';
            sh += '</div>';
          });
          sugBox.innerHTML = sh;
          sugBox.style.display = 'block';
        });

        inp.addEventListener('focus', function() {
          if (inp.value.trim().length >= 1) inp.dispatchEvent(new Event('input'));
        });

        window.selectAssembly = function(key) {
          const dz = JS_ZONE_DATA[key];
          selectedAssembly = key;
          inp.value = toTitleCase(key);
          sugBox.style.display = 'none';
          if (dz && dzBox) {
            dzBox.innerHTML = '<i class="bi bi-geo-alt-fill" style="color:#2e7d32;"></i> <strong>District:</strong> ' + toTitleCase(dz.d) + ' &nbsp;|&nbsp; <strong>Zone:</strong> ' + toTitleCase(dz.z);
            dzBox.style.display = 'block';
          }
          if (submitBtn) { submitBtn.disabled = false; submitBtn.style.opacity = '1'; }
        };

        window.submitManualAssembly = function() {
          if (!selectedAssembly) return;
          const dz = JS_ZONE_DATA[selectedAssembly];
          inp.disabled = true;
          if (submitBtn) { submitBtn.disabled = true; submitBtn.style.opacity = '0.5'; }
          const assemblyName = toTitleCase(selectedAssembly);
          userMsg(assemblyName);
          voter.assembly_name = assemblyName;
          if (dz) { voter.district = toTitleCase(dz.d); voter.zone = toTitleCase(dz.z); }
          // Show confirmation
          state = S.MANUAL_CONFIRM;
          let h = '<strong>' + L('manual_confirm_title') + '</strong><div class="voter-details-card" style="margin-top:10px;">';
          h += '<div class="detail-row"><span class="detail-label">EPIC No</span><span class="detail-value">' + epic + '</span></div>';
          h += '<div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">' + voter.name + '</span></div>';
          h += '<div class="detail-row"><span class="detail-label">Assembly</span><span class="detail-value">' + voter.assembly_name + '</span></div>';
          if (voter.district) h += '<div class="detail-row"><span class="detail-label">District</span><span class="detail-value">' + voter.district + '</span></div>';
          if (voter.zone) h += '<div class="detail-row"><span class="detail-label">Zone</span><span class="detail-value">' + voter.zone + '</span></div>';
          h += '</div>';
          h += '<div class="action-buttons" style="margin-top:12px;">';
          h += '<button class="action-btn confirm" onclick="confirmManualDetails()" style="width:100%;"><i class="bi bi-check-lg"></i> ' + L('btn_confirm_proceed') + '</button>';
          h += '</div>';
          botReply(h, 700);
        };
      }

      window.confirmManualDetails = async function () {
        userMsg('✓ ' + L('btn_confirm_proceed'));
        // Mark as manually entered — keep original EPIC
        voter.manually_entered = true;
        voter.epic_no = epic;
        await startPhotoUpload();
      };

      /* ── Start Photo Upload step ── */
      async function startPhotoUpload() {
        state = S.AWAIT_PHOTO;
        setText(L('ph_upload'));
        showAttach();
        let h = L('upload_photo');
        h += '<div class="action-buttons" style="margin-top:10px;">';
        h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> ' + L('btn_upload_photo') + '</button>';
        h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> ' + L('btn_camera') + '</button>';
        h += '</div>';
        await botReply(h, 800);
      }

      /* ── PIN Setup ── */
      async function askPinSetup() {
        state = S.AWAIT_PIN;
        setNumeric(L('ph_pin'));
        input.disabled = false;
        input.value = '';
        let h = L('set_pin');
        h += '<br><br><em style="color:#667781;font-size:0.85rem;">' + L('pin_hint') + '</em>';
        await botReply(h, 800);
        input.focus();
      }

      /* ── Ask for Additional Details ── */
      async function askAdditionalDetails() {
        state = S.ASK_ADDITIONAL;
        setText(L('ph_add_or_skip'));
        let h = L('ask_additional') + '<br><br>';
        h += '<em style="color:#667781;font-size:0.85rem;">' + L('additional_hint') + '</em>';
        h += '<div class="action-buttons" style="margin-top:12px;">';
        h += '<button class="action-btn confirm" onclick="startAdditionalDetails()"><i class="bi bi-plus-circle"></i> ' + L('btn_add_details') + '</button>';
        h += '<button class="action-btn skip" onclick="skipAdditionalDetails()"><i class="bi bi-skip-forward"></i> ' + L('btn_skip') + '</button>';
        h += '</div>';
        await botReply(h, 900);
      }

      window.startAdditionalDetails = async function () {
        state = S.AWAIT_DOB;
        skippedDetails = false;
        lockInput();
        let h = L('select_dob');
        h += '<div style="margin-top:10px;"><input type="date" id="dobPicker" max="' + new Date().toISOString().split('T')[0] + '" style="width:100%;padding:10px 14px;border:2px solid #2e7d32;border-radius:10px;font-size:1rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;-webkit-appearance:none;appearance:none;height:48px;line-height:1.2;"></div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitDob()" style="width:100%;"><i class="bi bi-check-lg"></i> ' + L('btn_confirm_dob') + '</button></div>';
        await botReply(h, 700);
      };

      /* ── Submit DOB from calendar picker ── */
      window.submitDob = async function () {
        const picker = document.getElementById('dobPicker');
        if (!picker || !picker.value) {
          await botReply(L('select_date'), 400);
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
        let h = L('select_blood');
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
        let h = L('enter_address');
        h += '<div style="margin-top:10px;position:relative;">';
        h += '<textarea id="addressInput" maxlength="70" rows="3" placeholder="' + L('ph_address') + '" style="width:100%;padding:10px 14px;border:2px solid #2e7d32;border-radius:10px;font-size:0.95rem;font-family:Inter,sans-serif;outline:none;color:#333;background:#f8fff8;resize:none;" oninput="updateAddrCount()"></textarea>';
        h += '<div id="addrCount" style="text-align:right;font-size:0.75rem;color:#888;margin-top:2px;">0 / 70</div>';
        h += '</div>';
        h += '<div style="margin-top:8px;"><button class="action-btn confirm" onclick="submitAddress()" style="width:100%;"><i class="bi bi-check-lg"></i> ' + L('btn_confirm_address') + '</button></div>';
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
          await botReply(L('enter_address_err'), 400);
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
        userMsg('<i class="bi bi-skip-forward"></i> ' + L('btn_skip'));
        skippedDetails = true;
        dob = ''; bloodGroup = ''; address = '';
        await showConfirmation();
      };

      /* ── Show Confirmation ── */
      async function showConfirmation() {
        state = S.CONFIRM_ALL;
        setText(L('ph_confirm'));
        hideAttach();

        if (isUpdatingDetails) {
          // Update details mode
          let h = L('confirm_updated');
          h += '<div class="member-summary"><h4>\uD83C\uDFAA ' + L('lbl_update_details') + '</h4>';
          if (dob) h += '<div class="row"><span class="lbl">' + L('lbl_dob') + '</span><span class="val">' + dob + '</span></div>';
          if (bloodGroup) h += '<div class="row"><span class="lbl">' + L('lbl_blood') + '</span><span class="val">' + bloodGroup + '</span></div>';
          if (address) h += '<div class="row"><span class="lbl">' + L('lbl_address') + '</span><span class="val">' + address + '</span></div>';
          h += '</div>';
          h += '<br>' + L('save_these');
          h += '<div class="action-buttons">';
          h += '<button class="action-btn confirm" onclick="doSaveUpdatedDetails()"><i class="bi bi-check-lg"></i> ' + L('btn_save_details') + '</button>';
          h += '<button class="action-btn cancel" onclick="doCancelUpdate()"><i class="bi bi-x-lg"></i> ' + L('btn_cancel') + '</button>';
          h += '</div>';
          await botReply(h, 1000);
        } else {
          // Normal card generation mode
          let h = L('confirm_details');
          h += '<div class="member-summary"><h4>\uD83C\uDFAA ' + L('header_title') + '</h4>';
          h += '<div class="row"><span class="lbl">Name</span><span class="val">' + (voter ? voter.name : '') + '</span></div>';
          h += '<div class="row"><span class="lbl">EPIC No</span><span class="val">' + epic + '</span></div>';
          h += '<div class="row"><span class="lbl">Assembly</span><span class="val">' + (voter ? (voter.assembly_name || '') : '') + '</span></div>';
          h += '<div class="row"><span class="lbl">District</span><span class="val">' + (voter ? toTitleCase(voter.district || '') : '') + '</span></div>';
          if (voter && voter.zone) h += '<div class="row"><span class="lbl">Zone</span><span class="val">' + toTitleCase(voter.zone) + '</span></div>';
          h += '<div class="row"><span class="lbl">Mobile</span><span class="val">+91 ' + mobile + '</span></div>';
          if (dob) h += '<div class="row"><span class="lbl">Date of Birth</span><span class="val">' + dob + '</span></div>';
          if (bloodGroup) h += '<div class="row"><span class="lbl">' + L('lbl_blood') + '</span><span class="val">' + bloodGroup + '</span></div>';
          if (address) h += '<div class="row"><span class="lbl">' + L('lbl_address') + '</span><span class="val">' + address + '</span></div>';
          if (skippedDetails) h += '<div class="row"><span class="lbl">' + L('lbl_status') + '</span><span class="val" style="color:#ff9800;">' + L('details_pending') + '</span></div>';
          h += '</div>';
          h += '<br>' + L('ready_generate');
          h += '<div class="action-buttons">';
          h += '<button class="action-btn confirm" onclick="doConfirmGenerate()"><i class="bi bi-check-lg"></i> ' + L('btn_confirm_generate') + '</button>';
          h += '<button class="action-btn cancel" onclick="doCancelAll()"><i class="bi bi-x-lg"></i> ' + L('btn_cancel') + '</button>';
          h += '</div>';
          await botReply(h, 1000);
        }
      }

      window.doConfirmGenerate = async function () {
        userMsg('\u2705 ' + L('btn_confirm_generate'));
        await doGenerateCard();
      };
      window.doCancelUpdate = async function () {
        userMsg('\u274C ' + L('btn_cancel'));
        state = S.DONE;
        isUpdatingDetails = false;
        await botReply(L('update_cancelled'), 600);
      };
      window.doCancelAll = async function () {
        userMsg('\u274C ' + L('btn_cancel'));
        state = S.AWAIT_EPIC;
        setEpicInput(L('ph_epic'));
        hideAttach();
        photoFile = null; photoUrl = ''; dob = ''; bloodGroup = ''; address = ''; skippedDetails = false;
        await botReply(L('cancelled_start_over'), 700);
      };

      /* ── Reusable Card Preview HTML ── */
      function buildCardPreviewHtml(m) {
        const cardUrl = '/card-view';
        let h = '<div class="card-preview-wrap">';
        h += '<div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px;">';
        // Front Card with download icon
        h += '<div style="flex:1;min-width:220px;max-width:380px;">';
        h += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">';
        h += '<div class="card-label" style="margin-bottom:0;">Front</div>';
        h += '<button onclick="window.open(\'' + cardUrl + '\',\'_blank\')" style="border:none;background:#2e7d32;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.85rem;box-shadow:0 2px 6px rgba(46,125,50,0.3);" title="Download"><i class="bi bi-download"></i></button>';
        h += '</div>';
        h += '<div style="position:relative;width:100%;padding-bottom:146%;background:url(https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232516/vanigan/templates/ID_Front.png) center/contain no-repeat;border-radius:10px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.12);cursor:pointer;" onclick="window.open(\'' + cardUrl + '\',\'_blank\')">';
        if (m.photo_url) h += '<img src="' + m.photo_url + '" style="position:absolute;top:31.8%;left:50%;transform:translateX(-50%);width:32.5%;border-radius:16px;border:3px solid #009245;aspect-ratio:1;object-fit:cover;">';
        h += '<div style="position:absolute;top:57%;left:0;right:0;text-align:center;padding:0 12px;">';
        const cpDz = lookupDistrictZone(m.assembly);
        const cpDist = cpDz.district || toTitleCase(m.district);
        const cpZone = cpDz.zone || toTitleCase(m.zone);
        h += '<p style="font-size:1rem;font-weight:700;color:#009245;margin:0;line-height:1.1;">' + (m.name || '') + '</p>';
        h += '<p style="font-size:0.75rem;margin:2px 0 0;">' + (m.assembly ? m.assembly + ' <span style="display:inline-block;font-size:0.45rem;font-weight:700;color:#fff;background:#009245;border-radius:3px;padding:0 4px;vertical-align:middle;letter-spacing:0.3px;line-height:1.4;">Assm</span>' : '') + '</p>';
        h += '<p style="font-size:0.75rem;margin:1px 0 0;">' + (cpDist ? cpDist + ' <span style="display:inline-block;font-size:0.45rem;font-weight:700;color:#fff;background:#009245;border-radius:3px;padding:0 4px;vertical-align:middle;letter-spacing:0.3px;line-height:1.4;">Dist</span>' : '') + '</p>';
        h += '<p style="font-size:0.75rem;margin:1px 0 0;">' + (cpZone || '') + '</p>';
        h += '<p style="font-size:0.7rem;margin:3px 0 0;letter-spacing:0.3px;">' + (m.unique_id || '') + '</p>';
        h += '</div></div></div>';
        // Back Card with download icon
        h += '<div style="flex:1;min-width:220px;max-width:380px;">';
        h += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">';
        h += '<div class="card-label" style="margin-bottom:0;">Back</div>';
        h += '<button onclick="window.open(\'' + cardUrl + '\',\'_blank\')" style="border:none;background:#2e7d32;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.85rem;box-shadow:0 2px 6px rgba(46,125,50,0.3);" title="Download"><i class="bi bi-download"></i></button>';
        h += '</div>';
        h += '<div style="position:relative;width:100%;padding-bottom:146%;background:url(https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png) center/contain no-repeat;border-radius:10px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.12);cursor:pointer;" onclick="window.open(\'' + cardUrl + '\',\'_blank\')">';
        h += '<div style="position:absolute;top:28%;left:6%;right:6%;font-size:0.65rem;line-height:1.3;display:flex;flex-direction:column;gap:4px;">';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:16px;"><span style="font-weight:700;">DATE OF BIRTH</span><span style="font-weight:700;">:</span><span>' + (m.dob || 'xxxxxx') + '</span></div>';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:16px;"><span style="font-weight:700;">AGE</span><span style="font-weight:700;">:</span><span>' + (m.age || 'xxxxxx') + '</span></div>';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:16px;"><span style="font-weight:700;">BLOOD GROUP</span><span style="font-weight:700;">:</span><span>' + (m.blood_group || 'xxxxxx') + '</span></div>';
        h += '<div style="display:grid;grid-template-columns:48% 5% 47%;align-items:start;min-height:50px;"><span style="font-weight:700;">ADDRESS</span><span style="font-weight:700;">:</span><span style="font-size:0.55rem;word-break:break-word;">' + (m.address || 'xxxxxx') + '</span></div>';
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
        // WhatsApp-style reply buttons
        h += '<div class="action-buttons" style="margin-top:12px;">';
        h += '<button class="action-btn confirm" onclick="doMenuRefer()"><i class="bi bi-share"></i> ' + L('sb_refer') + '</button>';
        h += '<button class="action-btn confirm" onclick="doMenuOrganizer()"><i class="bi bi-briefcase"></i> ' + L('sb_organizer') + '</button>';
        h += '</div>';
        h += '<div class="action-buttons" style="margin-top:8px;">';
        h += '<button class="action-btn confirm" onclick="doMenuWings()"><i class="bi bi-diagram-3"></i> ' + L('sb_wings') + '</button>';
        h += '<button class="action-btn confirm" onclick="doRequestLoan()"><i class="bi bi-cash-coin"></i> ' + L('btn_request_loan') + '</button>';
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
            await botReply(L('uploading_photo'), 400);
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
              let h = L('photo_upload_failed');
              h += '<div class="action-buttons" style="margin-top:10px;">';
              h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> ' + L('btn_reupload') + '</button>';
              h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> ' + L('btn_camera') + '</button>';
              h += '</div>';
              await botReply(h, 600);
              return;
            }
            photoUrl = uploadRes.photo_url;
            trackStep('photo_uploaded');
          }

          // Step 2: Generate card
          await botReply(L('generating_card'), 400);
          showTyping();

          const cardData = {
            mobile: mobile,
            epic_no: epic,
            photo_url: photoUrl,
            name: voter ? voter.name : '',
            assembly: voter ? (voter.assembly_name || '') : '',
            district: voter ? (voter.district || '') : '',
            zone: voter ? (voter.zone || '') : '',
            dob: dob,
            blood_group: bloodGroup,
            address: address,
            skipped_details: skippedDetails,
            pin: pin,
            manually_entered: voter && voter.manually_entered ? true : false,
            referrer_unique_id: referrerUniqueId || ''
          };

          const res = await api('/api/vanigam/generate-card', cardData);
          hideTyping();

          if (res.success && res.member) {
            state = S.DONE;
            setText(L('ph_type_msg'));
            hideAttach();
            unlockInput();

            saveUser({ mobile, epic, hasCard: true, memberData: res.member });

            const m = res.member;

            let h = L('card_generated');
            h += buildCardPreviewHtml(m);

            if (skippedDetails) {
              h += '<div style="margin-top:12px;padding:12px;background:rgba(255,152,0,0.1);border-radius:10px;border:1px solid rgba(255,152,0,0.3);">';
              h += '<span style="color:#e65100;font-size:0.9rem;"><i class="bi bi-exclamation-triangle"></i> ' + L('details_skipped') + '</span>';
              h += '<div style="margin-top:8px;">';
              h += '<button class="action-btn confirm" onclick="doUpdateDetailsFromCard()" style="font-size:0.85rem;padding:8px 16px;">';
              h += '<i class="bi bi-pencil-square"></i> ' + L('btn_update_now');
              h += '</button>';
              h += '</div></div>';
            }

            await botReply(h, 1200);
            photoFile = null;

            // Capture and upload card images directly (no iframe)
            captureAndUploadCard(m);

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
            await botReply('\u274C ' + (res.message || L('card_gen_fail')), 700);
          }
        } catch (e) {
          hideTyping();
          state = S.CONFIRM_ALL;
          unlockInput();
          await botReply(L('something_wrong'), 600);
        }
      }

      /* ── Photo Upload / Camera ── */
      const MAX_PHOTO_SIZE = 15 * 1024 * 1024;

      window.triggerPhotoUpload = function () { if (state === S.AWAIT_PHOTO) photoInput.click(); };

      // Detect mobile: has touch AND small screen (true mobile device)
      function isMobileDevice() {
        return /Android|iPhone|iPad|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
      }

      let webcamStream = null;
      window.triggerCamera = function () {
        if (state !== S.AWAIT_PHOTO) return;
        if (isMobileDevice()) {
          // Mobile: use native camera capture input
          cameraInput.click();
        } else {
          // PC: open webcam modal
          openWebcam();
        }
      };

      async function openWebcam() {
        const overlay = document.getElementById('webcamModalOverlay');
        const video = document.getElementById('webcamVideo');
        overlay.classList.add('active');
        try {
          webcamStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 720 }, height: { ideal: 720 } }, audio: false });
          video.srcObject = webcamStream;
        } catch (e) {
          overlay.classList.remove('active');
          // Fallback to file input if camera access denied
          cameraInput.click();
        }
      }

      window.closeWebcam = function () {
        const overlay = document.getElementById('webcamModalOverlay');
        const video = document.getElementById('webcamVideo');
        overlay.classList.remove('active');
        if (webcamStream) {
          webcamStream.getTracks().forEach(t => t.stop());
          webcamStream = null;
        }
        video.srcObject = null;
      };

      window.captureWebcam = function () {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        // Capture square frame from video center
        const size = Math.min(video.videoWidth, video.videoHeight);
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');
        // Mirror the image (video is mirrored via CSS)
        ctx.translate(size, 0);
        ctx.scale(-1, 1);
        const sx = (video.videoWidth - size) / 2;
        const sy = (video.videoHeight - size) / 2;
        ctx.drawImage(video, sx, sy, size, size, 0, 0, size, size);
        canvas.toBlob(function (blob) {
          if (!blob) return;
          const file = new File([blob], 'webcam_photo.jpg', { type: 'image/jpeg' });
          closeWebcam();
          // Feed into the crop modal (same as file input)
          openCropModal(file);
        }, 'image/jpeg', 0.92);
      };

      attachBtn.addEventListener('click', () => { if (state === S.AWAIT_PHOTO) photoInput.click(); });

      /* ── Cropper.js Integration ── */
      let cropperInstance = null;
      let cropperOriginalFile = null;
      const cropOverlay = document.getElementById('cropModalOverlay');
      const cropImage = document.getElementById('cropImage');

      function openCropModal(file) {
        cropperOriginalFile = file;
        const reader = new FileReader();
        reader.onload = (ev) => {
          cropImage.src = ev.target.result;
          cropOverlay.classList.add('open');
          // Destroy previous instance if any
          if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
          // Wait for image to load before initializing Cropper
          cropImage.onload = function () {
            cropperInstance = new Cropper(cropImage, {
              aspectRatio: 137 / 136, // ID card photo ratio (nearly square)
              viewMode: 1,
              dragMode: 'move',
              autoCropArea: 0.85,
              responsive: true,
              restore: false,
              guides: true,
              center: true,
              highlight: false,
              cropBoxMovable: true,
              cropBoxResizable: true,
              toggleDragModeOnDblclick: false,
              background: true,
            });
          };
        };
        reader.readAsDataURL(file);
      }

      window.cropRotate = function (deg) { if (cropperInstance) cropperInstance.rotate(deg); };
      window.cropFlipH = function () {
        if (!cropperInstance) return;
        const d = cropperInstance.getData();
        cropperInstance.scaleX(d.scaleX === -1 ? 1 : -1);
      };
      window.cropZoomIn = function () { if (cropperInstance) cropperInstance.zoom(0.1); };
      window.cropZoomOut = function () { if (cropperInstance) cropperInstance.zoom(-0.1); };
      window.cropReset = function () { if (cropperInstance) cropperInstance.reset(); };

      window.cropCancel = function () {
        cropOverlay.classList.remove('open');
        if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
        cropperOriginalFile = null;
        // Re-enable upload buttons
        state = S.AWAIT_PHOTO;
        showAttach();
      };

      window.cropConfirm = function () {
        if (!cropperInstance) return;
        const canvas = cropperInstance.getCroppedCanvas({
          width: 400,
          height: 400,
          imageSmoothingEnabled: true,
          imageSmoothingQuality: 'high',
        });
        cropOverlay.classList.remove('open');
        cropperInstance.destroy();
        cropperInstance = null;

        canvas.toBlob(function (blob) {
          if (!blob) { cropCancel(); return; }
          const croppedFile = new File([blob], cropperOriginalFile.name, { type: 'image/jpeg', lastModified: Date.now() });
          cropperOriginalFile = null;
          processCroppedPhoto(croppedFile, canvas.toDataURL('image/jpeg', 0.92));
        }, 'image/jpeg', 0.92);
      };

      async function processCroppedPhoto(file, dataUrl) {
        photoFile = file;
        userMsg('<img src="' + dataUrl + '" class="photo-thumb" alt="Photo"><br>' + L('photo_uploaded'));
        hideAttach();

        // Validate photo on backend BEFORE asking to set PIN
        lockInput();
        showTyping();
        try {
          const formData = new FormData();
          formData.append('photo', file);
          formData.append('epic_no', epic || '');
          const validateRes = await fetch('/api/vanigam/validate-photo', { method: 'POST', body: formData }).then(r => r.json());
          hideTyping();

          if (validateRes.success) {
            await askPinSetup();
          } else {
            unlockInput();
            state = S.AWAIT_PHOTO;
            let h = validateRes.message ? ('❌ <strong>' + L('photo_validation_failed').split('<br>')[0].replace('❌ ', '') + '</strong><br>' + validateRes.message) : L('photo_validation_failed');
            h += '<div class="action-buttons" style="margin-top:10px;">';
            h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> ' + L('btn_upload_photo') + '</button>';
            h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> ' + L('btn_camera') + '</button>';
            h += '</div>';
            botReply(h, 600);
            photoFile = null;
          }
        } catch (e) {
          hideTyping();
          unlockInput();
          state = S.AWAIT_PHOTO;
          let h = L('photo_upload_failed');
          h += '<div class="action-buttons" style="margin-top:10px;">';
          h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> ' + L('btn_reupload') + '</button>';
          h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> ' + L('btn_camera') + '</button>';
          h += '</div>';
          botReply(h, 600);
          photoFile = null;
        }
      }

      function handlePhotoFile(file) {
        if (!file) return;

        // Check file format
        const validFormats = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validFormats.includes(file.type)) {
          let h = L('photo_invalid_format');
          h += '<div class="action-buttons" style="margin-top:10px;">';
          h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> ' + L('btn_upload_photo') + '</button>';
          h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> ' + L('btn_camera') + '</button>';
          h += '</div>';
          botReply(h, 600);
          return;
        }

        if (file.size > MAX_PHOTO_SIZE) {
          let h = L('photo_too_large');
          h += '<div class="action-buttons" style="margin-top:10px;">';
          h += '<button class="action-btn confirm photo-upload-btn" onclick="triggerPhotoUpload()"><i class="bi bi-image"></i> ' + L('btn_upload_photo') + '</button>';
          h += '<button class="action-btn confirm photo-camera-btn" onclick="triggerCamera()"><i class="bi bi-camera-fill"></i> ' + L('btn_camera') + '</button>';
          h += '</div>';
          botReply(h, 600);
          return;
        }

        // Open crop/rotate modal instead of directly processing
        openCropModal(file);
      }

      photoInput.addEventListener('change', (e) => { handlePhotoFile(e.target.files[0]); photoInput.value = ''; });
      cameraInput.addEventListener('change', (e) => { handlePhotoFile(e.target.files[0]); cameraInput.value = ''; });

      /* ── PIN input handling ──
       * PIN states (AWAIT_PIN, AWAIT_PIN_CONFIRM, AWAIT_RETURNING_PIN):
       * Limit to exactly 4 digits only
       */
      input.addEventListener('input', function () {
        // Handle PIN input states
        if (state === S.AWAIT_PIN || state === S.AWAIT_PIN_CONFIRM || state === S.AWAIT_RETURNING_PIN) {
          input.value = input.value.replace(/[^0-9]/g, '').slice(0, 4);
          // Enable button only when exactly 4 digits are entered
          sendBtn.disabled = input.value.length !== 4;
          return;
        }
        // EPIC validation is handled entirely by setEpicInput's oninput handler
        // No additional handling needed here for AWAIT_EPIC state
      });

      /* ── Events ── */
      sendBtn.addEventListener('click', handleSend);
      input.addEventListener('keydown', (e) => { if (e.key === 'Enter') handleSend(); });

      /* ── Boot ── */
      window.addEventListener('load', init);
    })();
  </script>

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
          <div style="text-align:center;padding:0 18px;"><p id="capAssembly" style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;"></p></div>
          <div style="text-align:center;padding:0 18px;"><p id="capDistrict" style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;"></p></div>
          <div style="text-align:center;padding:0 18px;"><p id="capZone" style="font-size:19px;font-weight:700;text-transform:capitalize;line-height:1.06;margin:0;color:#111;"></p></div>
          <div style="text-align:center;padding:0 18px;"><p id="capUniqueId" style="font-size:18px;font-weight:700;letter-spacing:0.2px;margin:0;color:#111;"></p></div>
        </div>
      </div>
    </div>
    <div id="captureBack" style="width:421px;position:relative;overflow:hidden;font-family:Arial,sans-serif;margin-top:20px;">
      <img id="capBgBack" src="https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png" style="display:block;width:421px;" crossorigin="anonymous" />
      <div style="position:absolute;top:234px;left:22px;right:20px;color:#111;">
        <div style="transform:translateY(-60px);">
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">DATE OF BIRTH</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div id="capDob" style="font-size:17px;font-weight:700;line-height:1.12;"></div>
          </div>
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">AGE</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div id="capAge" style="font-size:17px;font-weight:700;line-height:1.12;"></div>
          </div>
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">BLOOD GROUP</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div id="capBlood" style="font-size:17px;font-weight:700;line-height:1.12;"></div>
          </div>
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:76px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">ADDRESS</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div id="capAddress" style="font-size:17px;font-weight:700;line-height:1.12;word-break:break-word;"></div>
          </div>
          <div style="display:grid;grid-template-columns:46% 6% 48%;align-items:start;margin-bottom:10px;height:20px;overflow:hidden;">
            <div style="font-size:14px;font-weight:700;text-transform:uppercase;">CONTACT</div>
            <div style="font-size:26px;line-height:0.7;text-align:center;font-weight:700;">:</div>
            <div style="font-size:17px;font-weight:700;line-height:1.12;"><span id="capContact" style="background:rgba(255,255,255,0.78);display:inline-block;padding:0 4px;"></span></div>
          </div>
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
    // Direct card image capture and upload to Cloudinary (replaces unreliable iframe approach)
    async function captureAndUploadCard(member) {
      if (!member || !member.unique_id) return;
      try {
        // Populate hidden card
        const photo = document.getElementById('capPhoto');
        if (member.photo_url) { photo.src = member.photo_url; photo.style.display = 'block'; }
        document.getElementById('capName').textContent = member.name || '';
        const capDz = lookupDistrictZone(member.assembly);
        const capDistVal = capDz.district || toTitleCase(member.district);
        const capZoneVal = capDz.zone || toTitleCase(member.zone);
        document.getElementById('capAssembly').innerHTML = member.assembly ? member.assembly + ' <span style="display:inline-block;font-size:10px;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 5px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.5px;line-height:1.4;">Assm</span>' : '';
        document.getElementById('capDistrict').innerHTML = capDistVal ? capDistVal + ' <span style="display:inline-block;font-size:10px;font-weight:700;color:#fff;background:#009245;border-radius:4px;padding:1px 5px;margin-left:4px;vertical-align:middle;text-transform:uppercase;letter-spacing:0.5px;line-height:1.4;">Dist</span>' : '';
        document.getElementById('capZone').innerHTML = capZoneVal || '';
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
          member.card_front_url = data.front_url;
          member.card_back_url = data.back_url;
          try { localStorage.setItem('vanigam_member', JSON.stringify({ memberData: member, hasCard: true, mobile: member.mobile, epic: member.epic_no })); } catch(e) {}
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
