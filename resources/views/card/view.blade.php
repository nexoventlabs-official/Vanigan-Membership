<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vanigan ID Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
      * { box-sizing: border-box; }
      body {
        background: #dce8df;
        font-family: Arial, Helvetica, sans-serif;
        padding: 18px;
        color: #111;
        margin: 0;
      }

      .toolbar {
        max-width: 900px;
        margin: 0 auto 14px;
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
      }

      .toolbar button,
      .toolbar a {
        border: none;
        border-radius: 999px;
        padding: 10px 18px;
        background: #0a8a43;
        color: #fff;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
      }

      .toolbar a {
        background: #244b3d;
      }

      .toolbar button:hover { background: #077a3a; }
      .toolbar a:hover { background: #1a3d30; }

      .card-wrap {
        max-width: 560px;
        margin: 0 auto;
      }

      .meta {
        color: #2e5741;
        margin-bottom: 10px;
        text-align: center;
        font-weight: 700;
        font-size: 12px;
      }

      .card-face {
        width: 421px;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
        border-radius: 10px;
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

      .name { font-size: 23px; font-weight: 700; color: #009245; line-height: 1.08; margin: 0; }
      .designation, .detail-line, .id-number { font-size: 19px; font-weight: 700; text-transform: capitalize; line-height: 1.06; margin: 0; }
      .front-line { text-align: center; word-break: break-word; padding: 0 18px; }
      .id-number { font-size: 18px; letter-spacing: 0.2px; margin-top: 2px; }

      .back-content {
        position: absolute;
        top: 234px;
        left: 22px;
        right: 20px;
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
        text-align: center; padding: 60px 20px; color: #888;
      }
      .no-data i { font-size: 4rem; color: #ccc; display: block; margin-bottom: 16px; }

      /* Downloading overlay */
      .dl-overlay {
        display: none;
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 999;
        justify-content: center; align-items: center;
      }
      .dl-overlay.show { display: flex; }
      .dl-spinner { color: #fff; font-size: 1.2rem; text-align: center; }
      .dl-spinner i { font-size: 2rem; display: block; margin-bottom: 10px; animation: spin 1s linear infinite; }
      @keyframes spin { 100% { transform: rotate(360deg); } }

      /* Print */
      @media print {
        @page { size: A4; margin: 10mm; }
        body { background: #fff !important; padding: 0; margin: 0; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .toolbar, .meta, .dl-overlay { display: none !important; }
        .card-face { transform: none !important; margin: 10px auto !important; break-inside: avoid; page-break-inside: avoid; }
        .card-bg { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        img { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        #page2-div { page-break-before: auto; margin-top: 20px !important; }
      }

      @media (max-width: 520px) {
        body { padding: 10px; }
        .card-face { transform: scale(0.85); transform-origin: top center; margin-bottom: -60px; }
      }
    </style>
  </head>
  <body>
    <div class="toolbar" id="toolbar">
      <button type="button" onclick="downloadCard('front')"><i class="bi bi-download"></i> Download Front</button>
      <button type="button" onclick="downloadCard('back')"><i class="bi bi-download"></i> Download Back</button>
      <button type="button" onclick="downloadCard('both')"><i class="bi bi-images"></i> Download Both</button>
      <button type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
      <a href="/"><i class="bi bi-arrow-left"></i> Back</a>
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
            <div class="front-line"><p class="designation" id="memberMembership"></p></div>
            <div class="front-line"><p class="detail-line" id="memberAssembly"></p></div>
            <div class="front-line"><p class="detail-line" id="memberDistrict"></p></div>
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
      function getMember() {
        try {
          const data = JSON.parse(localStorage.getItem('vanigam_member') || 'null');
          return data && data.memberData ? data.memberData : null;
        } catch(e) { return null; }
      }

      function populate() {
        const m = getMember();
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
        document.getElementById('memberName').textContent = m.name || '';
        document.getElementById('memberMembership').textContent = m.membership || 'Member';
        document.getElementById('memberAssembly').textContent = m.assembly || '';
        document.getElementById('memberDistrict').textContent = m.district || '';
        document.getElementById('memberUniqueId').textContent = m.unique_id || '';

        // Back
        document.getElementById('memberDob').textContent = m.dob || '';
        document.getElementById('memberAge').textContent = m.age || '';
        document.getElementById('memberBlood').textContent = m.blood_group || '';
        document.getElementById('memberAddress').textContent = m.address || '';
        document.getElementById('memberContact').textContent = m.contact_number || ('+91 ' + (m.mobile || ''));

        // QR
        document.getElementById('memberQr').src = '/api/vanigam/qr/' + (m.unique_id || '');
      }

      // Download card as high-quality PNG
      async function downloadCard(which) {
        const overlay = document.getElementById('dlOverlay');
        overlay.classList.add('show');

        try {
          const opts = {
            scale: 3,
            useCORS: true,
            allowTaint: false,
            backgroundColor: null,
            logging: false,
          };

          if (which === 'front' || which === 'both') {
            const frontCanvas = await html2canvas(document.getElementById('page1-div'), opts);
            triggerDownload(frontCanvas, 'vanigam_card_front.png');
          }

          if (which === 'back' || which === 'both') {
            if (which === 'both') await new Promise(r => setTimeout(r, 300));
            const backCanvas = await html2canvas(document.getElementById('page2-div'), opts);
            triggerDownload(backCanvas, 'vanigam_card_back.png');
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

      // Auto-save card images to Cloudinary when loaded with ?autosave=1
      async function autoSaveCardImages() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('autosave') !== '1') return;

        const m = getMember();
        if (!m || !m.unique_id) return;

        const opts = { scale: 3, useCORS: true, allowTaint: false, backgroundColor: null, logging: false };

        try {
          // Wait a moment for images to fully load
          await new Promise(r => setTimeout(r, 2000));

          const frontCanvas = await html2canvas(document.getElementById('page1-div'), opts);
          const backCanvas  = await html2canvas(document.getElementById('page2-div'), opts);

          const res = await fetch('/api/vanigam/upload-card-images', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              unique_id:   m.unique_id,
              front_image: frontCanvas.toDataURL('image/png', 1.0),
              back_image:  backCanvas.toDataURL('image/png', 1.0),
            }),
          });
          const data = await res.json();
          if (data.success) {
            m.card_front_url = data.front_url;
            m.card_back_url  = data.back_url;
            localStorage.setItem('vanigam_member', JSON.stringify({ memberData: m }));
            console.log('Card images saved to Cloudinary successfully.');
          }
        } catch(e) {
          console.error('Auto-save card images failed:', e);
        }
      }

      // Init
      populate();
      autoSaveCardImages();
    </script>
  </body>
</html>
