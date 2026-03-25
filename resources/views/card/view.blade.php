<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vanigan ID Card</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
      * { box-sizing: border-box; }
      body {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        font-family: 'Outfit', sans-serif;
        padding: 40px 18px;
        color: #fff;
        margin: 0;
        min-height: 100vh;
      }

      .toolbar {
        max-width: 900px;
        margin: 0 auto 30px;
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        background: rgba(255, 255, 255, 0.05);
        padding: 16px 20px;
        border-radius: 20px;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      }

      .toolbar button,
      .toolbar a {
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
      }

      .toolbar button:hover, .toolbar a:hover { 
        background: rgba(255, 255, 255, 0.2); 
        transform: translateY(-2px);
      }
      
      .toolbar .btn-primary-action {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
      }
      
      .toolbar .btn-primary-action:hover {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
      }

      .card-wrap {
        max-width: 560px;
        margin: 0 auto;
      }

      .meta {
        color: #94a3b8;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 500;
        font-size: 13px;
        background: rgba(0, 0, 0, 0.2);
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.05);
      }

      .card-face {
        width: 421px;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
      }

      /* Use <img> tags for backgrounds so they render in print & download */
      .card-bg {
        display: block;
        width: 421px;
        pointer-events: none;
      }

      .front-photo-wrap {
        position: absolute;
        top: 182px;
        left: 50%;
        transform: translateX(-50%);
        width: 137px;
      }

      .front-stack {
        position: absolute;
        top: 328px;
        left: 28px;
        right: 28px;
        text-align: center;
      }

      .front-stack > * + * {
        margin-top: 6px;
      }

      .front-meta {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
      }

      .photo {
        display: block;
        margin: 0 auto;
        border: 5px solid #009245;
        border-radius: 22px !important;
        width: 137px;
        height: 136px;
        object-fit: cover;
        padding: 0;
      }

      .name { font-size: 23px; font-weight: 700; color: #009245; line-height: 1.08; margin: 0; font-family: Arial, sans-serif; }
      .designation, .detail-line, .id-number { font-size: 19px; font-weight: 700; text-transform: capitalize; line-height: 1.06; margin: 0; font-family: Arial, sans-serif; color: #111; }
      .front-line { text-align: center; word-break: break-word; padding: 0 18px; }
      .id-number { font-size: 18px; letter-spacing: 0.2px; margin-top: 2px; }
      .suffix-tag { display: inline-block; font-size: 10px; font-weight: 700; color: #fff; background: #009245; border-radius: 4px; padding: 1px 5px; margin-left: 4px; vertical-align: middle; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.4; }

      .back-content {
        position: absolute;
        top: 234px;
        left: 22px;
        right: 20px;
        color: #111;
        font-family: Arial, sans-serif;
      }

      .back-details {
        transform: translateY(-60px);
      }

      .back-row {
        display: grid;
        grid-template-columns: 46% 6% 48%;
        align-items: start;
        margin-bottom: 10px;
        overflow: hidden;
      }
      .back-row.row-single { height: 20px; }
      .back-row.row-address { height: 76px; }

      .back-label { font-size: 14px; font-weight: 700; text-transform: uppercase; }
      .back-sep { font-size: 26px; line-height: 0.7; text-align: center; font-weight: 700; }
      .back-value { font-size: 17px; font-weight: 700; line-height: 1.12; word-break: break-word; }
      .back-value.address { line-height: 1.12; }

      .back-bottom {
        display: grid;
        grid-template-columns: 40% 60%;
        align-items: start;
        margin-top: 10px;
      }

      .qr-wrap { padding-left: 20px; }
      .sign-wrap { text-align: center; padding-right: 10px; }

      .contact-value {
        background: rgba(255, 255, 255, 0.78);
        display: inline-block;
        padding: 0 4px;
      }

      .signature-name { text-align: center; margin: 2px 0 0; font-size: 14px; font-weight: 700; }
      .small { font-size: 12px; font-weight: bold; line-height: 1.1; margin: 0; }

      .no-data {
        text-align: center; padding: 60px 20px; color: #94a3b8;
      }
      .no-data i { font-size: 4rem; color: #475569; display: block; margin-bottom: 16px; animation: float 3s ease-in-out infinite; }
      @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

      /* Downloading overlay */
      .dl-overlay {
        display: none;
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.8); z-index: 999;
        justify-content: center; align-items: center;
        backdrop-filter: blur(5px);
      }
      .dl-overlay.show { display: flex; }
      .dl-spinner { color: #fff; font-size: 1.2rem; text-align: center; font-weight: 500; font-family: 'Outfit'; }
      .dl-spinner i { font-size: 2.5rem; display: block; margin-bottom: 15px; animation: spin 1s linear infinite; color: #3b82f6; }
      @keyframes spin { 100% { transform: rotate(360deg); } }

      /* Print */
      @media print {
        @page { size: A4; margin: 10mm; }
        body { background: #fff !important; padding: 0; margin: 0; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .toolbar, .meta, .dl-overlay { display: none !important; }
        .card-face { transform: none !important; margin: 10px auto !important; break-inside: avoid; page-break-inside: avoid; box-shadow: none !important; }
        .card-bg { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        img { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        #page2-div { page-break-before: auto; margin-top: 20px !important; }
      }

      @media (max-width: 520px) {
        body { padding: 20px 10px; }
        .toolbar { flex-wrap: wrap; justify-content: center; padding: 12px; border-radius: 16px; margin-bottom: 20px; }
        .toolbar button, .toolbar a { padding: 8px 14px; font-size: 13px; flex: 1; justify-content: center; min-width: 130px; }
        .card-face { transform: scale(0.85); transform-origin: top center; margin-bottom: -60px; }
      }
    </style>
  </head>
  <body>
    <div class="toolbar" id="toolbar">
      <button type="button" class="btn-primary-action" onclick="downloadCard('both')"><i class="bi bi-images"></i> Download Both Sides</button>
      <button type="button" onclick="downloadCard('front')"><i class="bi bi-download"></i> Front</button>
      <button type="button" onclick="downloadCard('back')"><i class="bi bi-download"></i> Back</button>
      <button type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
      <a href="/"><i class="bi bi-arrow-left"></i> Home</a>
    </div>

    <div class="card-wrap" id="cardWrap">
      <p class="meta" id="metaText"></p>

      <!-- FRONT -->
      <div id="page1-div" class="card-face">
        <img src="https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232516/vanigan/templates/ID_Front.png"
             class="card-bg" crossorigin="anonymous" alt="Front" />
        <div class="front-photo-wrap">
          <img id="memberPhoto" src="" crossorigin="anonymous" class="rounded img-thumbnail photo" style="display:none;" />
        </div>
        <div class="front-stack">
          <div class="front-line"><p class="name" id="memberName"></p></div>
          <div class="front-meta">
            <div class="front-line"><p class="detail-line" id="memberAssembly"></p></div>
            <div class="front-line"><p class="detail-line" id="memberDistrict"></p></div>
            <div class="front-line"><p class="detail-line" id="memberZone"></p></div>
            <div class="front-line"><p class="id-number" id="memberUniqueId"></p></div>
          </div>
        </div>
      </div>

      <!-- BACK -->
      <div id="page2-div" class="card-face" style="margin-top: 20px;">
        <img src="https://res.cloudinary.com/dqndhcmu2/image/upload/v1773232519/vanigan/templates/ID_Back.png"
             class="card-bg" crossorigin="anonymous" alt="Back" />
        <div class="back-content">
          <div class="back-details">
            <div class="back-row row-single">
              <div class="back-label">DATE OF BIRTH</div>
              <div class="back-sep">:</div>
              <div class="back-value" id="memberDob"></div>
            </div>
            <div class="back-row row-single">
              <div class="back-label">AGE</div>
              <div class="back-sep">:</div>
              <div class="back-value" id="memberAge"></div>
            </div>
            <div class="back-row row-single">
              <div class="back-label">BLOOD GROUP</div>
              <div class="back-sep">:</div>
              <div class="back-value" id="memberBlood"></div>
            </div>
            <div class="back-row row-address">
              <div class="back-label">ADDRESS</div>
              <div class="back-sep">:</div>
              <div class="back-value address" id="memberAddress"></div>
            </div>
            <div class="back-row row-single">
              <div class="back-label">CONTACT</div>
              <div class="back-sep">:</div>
              <div class="back-value"><span class="contact-value" id="memberContact"></span></div>
            </div>
          </div>
          <div class="back-bottom">
            <div class="qr-wrap">
              <img id="memberQr" src="" width="96" height="88" alt="QR Code" crossorigin="anonymous" />
            </div>
            <div class="sign-wrap">
              <img src="/signature.png" style="width:80px;height:auto;margin-bottom:2px;" crossorigin="anonymous" />
              <p class="signature-name">SENTHIL KUMAR N</p>
              <p class="small">Founder &amp; State President</p>
              <p class="small">Tamilnadu Vanigargalin Sangamam</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="dl-overlay" id="dlOverlay">
      <div class="dl-spinner">
        <i class="bi bi-arrow-repeat"></i>
        Generating image...
      </div>
    </div>

    <script>
      // Load member data from localStorage
      function getMemberFromStorage() {
        try {
          const data = JSON.parse(localStorage.getItem('vanigam_member') || 'null');
          return data && data.memberData ? data.memberData : null;
        } catch(e) { return null; }
      }

      // Fetch member data from API by unique_id
      async function getMemberFromApi(uid) {
        try {
          const res = await fetch('/api/vanigam/member/' + encodeURIComponent(uid));
          const data = await res.json();
          if (data.success && data.member) return data.member;
          return null;
        } catch(e) { return null; }
      }

      // Cached member for use by other functions
      let cachedMember = null;
      function getMember() { return cachedMember; }

      async function loadAndPopulate() {
        const params = new URLSearchParams(window.location.search);
        const uid = params.get('uid');

        // Try API fetch first if uid is provided, then fall back to localStorage
        if (uid) {
          cachedMember = await getMemberFromApi(uid);
        }
        if (!cachedMember) {
          cachedMember = getMemberFromStorage();
        }
        // If we fetched from API, also update localStorage so autosave works
        if (cachedMember && uid) {
          try {
            localStorage.setItem('vanigam_member', JSON.stringify({
              memberData: cachedMember,
              hasCard: true,
              mobile: cachedMember.mobile,
              epic: cachedMember.epic_no
            }));
          } catch(e) {}
        }

        populate();
      }

      function populate() {
        const m = cachedMember;
        if (!m) {
          document.getElementById('cardWrap').innerHTML = '<div class="no-data"><i class="bi bi-person-badge"></i><h4>No Card Data Found</h4><p>Please generate a membership card first from the <a href="/">chat page</a>.</p></div>';
          document.getElementById('toolbar').style.display = 'none';
          return;
        }

        document.title = 'Vanigan ID Card - ' + (m.unique_id || '');
        document.getElementById('metaText').textContent = 'Generated: ' + new Date().toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

        // Front
        if (m.photo_url) {
          const photo = document.getElementById('memberPhoto');
          photo.src = m.photo_url;
          photo.style.display = 'block';
        }
        function toTitleCase(s) { return s ? s.toLowerCase().replace(/(?:^|\s)\S/g, function(a){ return a.toUpperCase(); }) : ''; }
        document.getElementById('memberName').textContent = m.name || '';
        document.getElementById('memberAssembly').innerHTML = m.assembly ? m.assembly + ' <span class="suffix-tag">Assm</span>' : '';
        document.getElementById('memberDistrict').innerHTML = m.district ? toTitleCase(m.district) + ' <span class="suffix-tag">Dist</span>' : '';
        document.getElementById('memberZone').innerHTML = m.zone ? toTitleCase(m.zone) : '';
        document.getElementById('memberUniqueId').textContent = m.unique_id || '';

        // Back - show XXXXXXX for empty fields
        document.getElementById('memberDob').textContent = m.dob || 'xxxxxx';
        document.getElementById('memberAge').textContent = m.age || 'xxxxxx';
        document.getElementById('memberBlood').textContent = m.blood_group || 'xxxxxx';
        document.getElementById('memberAddress').textContent = m.address || 'xxxxxx';
        document.getElementById('memberContact').textContent = m.contact_number || ('+91 ' + (m.mobile || ''));

        // QR
        document.getElementById('memberQr').src = '/api/vanigam/qr/' + (m.unique_id || '');
      }

      // Download card as high-quality PNG
      async function downloadCard(which) {
        const overlay = document.getElementById('dlOverlay');
        overlay.classList.add('show');
        const m = getMember();
        const uid = (m && m.unique_id) ? m.unique_id : 'card';

        try {
          const opts = {
            scale: 3,
            useCORS: true,
            allowTaint: false,
            backgroundColor: null,
            logging: false,
          };

          if (which === 'front') {
            const frontCanvas = await html2canvas(document.getElementById('page1-div'), opts);
            triggerDownload(frontCanvas, uid + '_front.png');
          } else if (which === 'back') {
            const backCanvas = await html2canvas(document.getElementById('page2-div'), opts);
            triggerDownload(backCanvas, uid + '_back.png');
          } else if (which === 'both') {
            const frontCanvas = await html2canvas(document.getElementById('page1-div'), opts);
            await new Promise(r => setTimeout(r, 300));
            const backCanvas = await html2canvas(document.getElementById('page2-div'), opts);

            // Combine front and back side-by-side
            const gap = 40 * opts.scale;
            const comboCanvas = document.createElement('canvas');
            comboCanvas.width = frontCanvas.width + backCanvas.width + gap;
            comboCanvas.height = Math.max(frontCanvas.height, backCanvas.height) + 24 * opts.scale;
            const ctx = comboCanvas.getContext('2d');
            // Transparent background (no white bg)
            ctx.clearRect(0, 0, comboCanvas.width, comboCanvas.height);
            // Draw "Front" and "Back" labels
            ctx.font = 'bold ' + (14 * opts.scale) + 'px Inter, sans-serif';
            ctx.fillStyle = '#333';
            ctx.textAlign = 'center';
            ctx.fillText('Front', frontCanvas.width / 2, 16 * opts.scale);
            ctx.fillText('Back', frontCanvas.width + gap + backCanvas.width / 2, 16 * opts.scale);
            // Draw cards
            ctx.drawImage(frontCanvas, 0, 20 * opts.scale);
            ctx.drawImage(backCanvas, frontCanvas.width + gap, 20 * opts.scale);

            triggerDownload(comboCanvas, uid + '_card.png');
          }
        } catch(e) {
          alert('Download failed: ' + e.message);
        }

        overlay.classList.remove('show');
      }

      function triggerDownload(canvas, filename) {
        const link = document.createElement('a');
        link.download = filename;
        link.href = canvas.toDataURL('image/png', 1.0);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }

      // Wait for all images in a container to fully load
      function waitForImages(container, timeoutMs) {
        return new Promise((resolve) => {
          const imgs = container.querySelectorAll('img');
          if (imgs.length === 0) { resolve(); return; }
          let loaded = 0;
          const total = imgs.length;
          const done = () => { loaded++; if (loaded >= total) resolve(); };
          imgs.forEach(img => {
            if (img.complete && img.naturalWidth > 0) { done(); return; }
            img.addEventListener('load', done);
            img.addEventListener('error', done);
          });
          setTimeout(resolve, timeoutMs || 10000);
        });
      }

      // Auto-save card images to Cloudinary when loaded with ?autosave=1
      async function autoSaveCardImages(retryCount) {
        const params = new URLSearchParams(window.location.search);
        if (params.get('autosave') !== '1') return;

        const m = getMember();
        if (!m || !m.unique_id) return;

        retryCount = retryCount || 0;

        const opts = { scale: 3, useCORS: true, allowTaint: false, backgroundColor: null, logging: false };

        try {
          // Wait for all images to fully load (up to 10 seconds)
          const frontDiv = document.getElementById('page1-div');
          const backDiv = document.getElementById('page2-div');
          await waitForImages(frontDiv, 10000);
          await waitForImages(backDiv, 10000);
          // Extra buffer for rendering
          await new Promise(r => setTimeout(r, 1500));

          const frontCanvas = await html2canvas(frontDiv, opts);
          const backCanvas  = await html2canvas(backDiv, opts);

          // Verify canvases have actual content (not blank)
          const frontData = frontCanvas.toDataURL('image/png', 1.0);
          const backData = backCanvas.toDataURL('image/png', 1.0);

          if (frontData.length < 5000 || backData.length < 5000) {
            console.warn('Card images seem blank, retrying...');
            if (retryCount < 3) {
              await new Promise(r => setTimeout(r, 3000));
              return autoSaveCardImages(retryCount + 1);
            }
          }

          const res = await fetch('/api/vanigam/upload-card-images', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              unique_id:   m.unique_id,
              front_image: frontData,
              back_image:  backData,
            }),
          });
          const data = await res.json();
          if (data.success) {
            m.card_front_url = data.front_url;
            m.card_back_url  = data.back_url;
            localStorage.setItem('vanigam_member', JSON.stringify({ memberData: m, hasCard: true, mobile: m.mobile, epic: m.epic_no }));
            console.log('Card images saved to Cloudinary successfully.');
          } else if (retryCount < 3) {
            console.warn('Upload failed, retrying...', data);
            await new Promise(r => setTimeout(r, 3000));
            return autoSaveCardImages(retryCount + 1);
          }
        } catch(e) {
          console.error('Auto-save card images failed:', e);
          if (retryCount < 3) {
            await new Promise(r => setTimeout(r, 3000));
            return autoSaveCardImages(retryCount + 1);
          }
        }
      }

      // Init - load member data (from API or localStorage), then auto-save
      loadAndPopulate().then(() => autoSaveCardImages());
    </script>
  </body>
</html>
