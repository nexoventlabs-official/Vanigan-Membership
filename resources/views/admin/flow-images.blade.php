<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flow Images — Vanigan Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f0f2f5; color: #333; min-height: 100vh; }

    .navbar { background: linear-gradient(135deg, #007a38, #00a84e); color: #fff; padding: 0 24px; display: flex; align-items: center; height: 60px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 100; }
    .navbar-brand { font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .navbar-nav { display: flex; align-items: center; gap: 4px; margin-left: auto; }
    .navbar-nav a { color: rgba(255,255,255,0.85); text-decoration: none; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; transition: background 0.2s; }
    .navbar-nav a:hover, .navbar-nav a.active { background: rgba(255,255,255,0.18); color: #fff; }

    .container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

    .section { background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
    .section-header { padding: 18px 20px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; }
    .section-header h3 { font-size: 0.95rem; font-weight: 700; color: #333; display: flex; align-items: center; gap: 8px; }

    .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 0.85rem; font-weight: 500; }
    .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .alert-error { background: #fce4ec; color: #c62828; border: 1px solid #f8bbd0; }

    .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; padding: 20px; }

    .image-card { background: #fafafa; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden; transition: box-shadow 0.2s; }
    .image-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .image-card-header { padding: 12px 16px; background: #f5f5f5; border-bottom: 1px solid #e0e0e0; }
    .image-card-header h4 { font-size: 0.85rem; font-weight: 700; color: #333; margin-bottom: 2px; }
    .image-card-header p { font-size: 0.75rem; color: #888; }
    .image-card-body { padding: 16px; text-align: center; }
    .image-card-body img { max-width: 100%; max-height: 150px; border-radius: 8px; object-fit: contain; margin-bottom: 12px; border: 1px solid #eee; }
    .image-card-body .no-image { padding: 30px; color: #bbb; font-size: 2rem; }

    .upload-form { display: flex; flex-direction: column; gap: 8px; }
    .upload-form input[type="file"] { font-size: 0.8rem; }
    .upload-form .btn { padding: 8px 16px; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-upload { background: #2e7d32; color: #fff; }
    .btn-upload:hover { background: #1b5e20; }
    .btn-delete { background: #ef5350; color: #fff; font-size: 0.78rem; padding: 6px 12px; }
    .btn-delete:hover { background: #c62828; }
    .btn-row { display: flex; gap: 8px; justify-content: center; }

    .key-badge { display: inline-block; background: #e3f2fd; color: #1565c0; padding: 2px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 600; font-family: monospace; }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="navbar-brand">🖼️ Vanigan Admin</div>
    <div class="navbar-nav">
      <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="{{ route('admin.users') }}"><i class="bi bi-people"></i> Users</a>
      <a href="{{ route('admin.whatsapp') }}"><i class="bi bi-whatsapp"></i> WhatsApp</a>
      <a href="{{ route('admin.flow_images') }}" class="active"><i class="bi bi-images"></i> Flow Images</a>
      <form action="{{ route('admin.logout') }}" method="POST" style="display:inline">
        @csrf
        <button type="submit" style="background:rgba(255,255,255,0.15);border:none;color:#fff;padding:8px 14px;border-radius:8px;font-size:0.85rem;font-weight:500;cursor:pointer;">Logout</button>
      </form>
    </div>
  </nav>

  <div class="container">
    @if(session('success'))
      <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">❌ {{ session('error') }}</div>
    @endif

    <div class="section">
      <div class="section-header">
        <h3><i class="bi bi-images"></i> WhatsApp Flow Images</h3>
      </div>

      <div class="image-grid">
        @php
          $existingImages = collect($images)->keyBy('key');
        @endphp

        @foreach($requiredKeys as $key => $label)
          @php $existing = $existingImages->get($key); @endphp
          <div class="image-card">
            <div class="image-card-header">
              <h4>{{ $label }}</h4>
              <p><span class="key-badge">{{ $key }}</span></p>
            </div>
            <div class="image-card-body">
              @if($existing && !empty($existing['url']))
                <img src="{{ $existing['url'] }}" alt="{{ $label }}">
                <div class="btn-row">
                  <form action="{{ route('admin.flow_images.delete') }}" method="POST" onsubmit="return confirm('Delete this image?')">
                    @csrf
                    <input type="hidden" name="key" value="{{ $key }}">
                    <button type="submit" class="btn btn-delete"><i class="bi bi-trash"></i> Delete</button>
                  </form>
                </div>
              @else
                <div class="no-image"><i class="bi bi-image"></i></div>
              @endif

              <form action="{{ route('admin.flow_images.upload') }}" method="POST" enctype="multipart/form-data" class="upload-form" style="margin-top:12px">
                @csrf
                <input type="hidden" name="key" value="{{ $key }}">
                <input type="hidden" name="label" value="{{ $label }}">
                <input type="file" name="image" accept="image/*" required>
                <button type="submit" class="btn btn-upload"><i class="bi bi-upload"></i> {{ $existing ? 'Replace' : 'Upload' }}</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</body>
</html>
