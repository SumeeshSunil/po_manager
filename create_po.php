<?php
include 'partials/header.php';
checkLogin();
checkRole(['admin']);
?>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  .po-page {
    min-height: 100vh;
    background: #f0f2f5;
    padding: 32px 24px 60px;
    font-family: 'DM Sans', sans-serif;
  }

  .po-header {
    max-width: 900px;
    margin: 0 auto 28px;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .po-header-icon {
    width: 44px; height: 44px;
    background: #1a1a2e;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }

  .po-header-icon svg { width: 22px; height: 22px; stroke: #fff; fill: none; stroke-width: 2; }

  .po-header h2 {
    font-size: 22px;
    font-weight: 600;
    color: #1a1a2e;
    letter-spacing: -0.3px;
  }

  .po-header p {
    font-size: 13px;
    color: #888;
    margin-top: 2px;
  }

  .po-card {
    max-width: 900px;
    margin: 0 auto 20px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8eaed;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  }

  .po-card-header {
    padding: 16px 24px;
    background: #fafafa;
    border-bottom: 1px solid #e8eaed;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .po-card-header .section-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #1a1a2e;
    flex-shrink: 0;
  }

  .po-card-header span {
    font-size: 13px;
    font-weight: 600;
    color: #1a1a2e;
    text-transform: uppercase;
    letter-spacing: 0.6px;
  }

  .po-card-body { padding: 24px; }

  .upload-zone {
    border: 2px dashed #d0d5dd;
    border-radius: 12px;
    padding: 32px 24px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #fafbfc;
    position: relative;
  }

  .upload-zone:hover, .upload-zone.dragover {
    border-color: #1a1a2e;
    background: #f4f5f7;
  }

  .upload-zone input[type="file"] {
    position: absolute; inset: 0;
    opacity: 0; cursor: pointer; width: 100%; height: 100%;
  }

  .upload-icon {
    width: 48px; height: 48px;
    background: #1a1a2e;
    border-radius: 12px;
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 12px;
  }

  .upload-icon svg { width: 24px; height: 24px; stroke: #fff; fill: none; stroke-width: 1.8; }

  .upload-zone h3 { font-size: 15px; font-weight: 600; color: #1a1a2e; margin-bottom: 4px; }
  .upload-zone p  { font-size: 13px; color: #888; }

  .upload-zone .file-chosen {
    display: none;
    margin-top: 12px;
    padding: 8px 14px;
    background: #e8f5e9;
    border-radius: 8px;
    font-size: 13px;
    color: #2e7d32;
    font-weight: 500;
  }

  #pdf-status {
    margin-top: 12px;
    font-size: 13px;
    font-weight: 500;
    min-height: 20px;
  }

  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .form-grid.single { grid-template-columns: 1fr; }

  .field-group { display: flex; flex-direction: column; gap: 6px; }

  .field-group label {
    font-size: 12px;
    font-weight: 600;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .field-group input,
  .field-group select {
    height: 42px;
    padding: 0 14px;
    border: 1.5px solid #e0e3e8;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    color: #1a1a2e;
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
  }

  .field-group input:focus,
  .field-group select:focus {
    border-color: #1a1a2e;
    box-shadow: 0 0 0 3px rgba(26,26,46,0.08);
  }

  .field-group input::placeholder { color: #bbb; }

  .items-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  .items-count { font-size: 13px; color: #888; font-weight: 500; }
  .items-count span { color: #1a1a2e; font-weight: 700; }

  #items-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 14px;
  }

  .item-card {
    background: #f8f9fb;
    border: 1.5px solid #e8eaed;
    border-radius: 12px;
    padding: 16px;
    position: relative;
    transition: box-shadow 0.2s, border-color 0.2s;
    animation: cardIn 0.2s ease both;
  }

  .item-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); border-color: #d0d5dd; }

  .item-card-num {
    position: absolute; top: 12px; right: 12px;
    width: 22px; height: 22px;
    background: #1a1a2e; color: #fff;
    border-radius: 6px;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    font-family: 'DM Mono', monospace;
  }

  .item-card .field-group { margin-bottom: 10px; }
  .item-card .field-group:last-of-type { margin-bottom: 0; }

  .item-card .field-group input { height: 38px; font-size: 13px; background: #fff; }
  .item-card .field-group label { font-size: 11px; }

  .btn-remove {
    margin-top: 12px; width: 100%; padding: 7px;
    background: #fff0f0; border: 1.5px solid #ffd6d6;
    border-radius: 8px; color: #d93025;
    font-size: 12px; font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer; transition: background 0.15s;
  }

  .btn-remove:hover { background: #ffe0e0; }

  .form-actions {
    max-width: 900px; margin: 0 auto;
    display: flex; gap: 12px; justify-content: flex-end;
  }

  .btn-add {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px;
    background: #f0f2f5; border: 1.5px solid #e0e3e8;
    border-radius: 10px; font-size: 13px; font-weight: 600;
    color: #1a1a2e; font-family: 'DM Sans', sans-serif;
    cursor: pointer; transition: background 0.15s;
  }

  .btn-add:hover { background: #e4e7ec; }

  .btn-submit {
    padding: 12px 32px;
    background: #1a1a2e; color: #fff;
    border: none; border-radius: 10px;
    font-size: 14px; font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s;
    display: inline-flex; align-items: center; gap: 8px;
  }

  .btn-submit:hover { background: #2d2d4e; }
  .btn-submit:active { transform: scale(0.98); }

  .btn-secondary {
    padding: 12px 24px;
    background: #fff; color: #555;
    border: 1.5px solid #e0e3e8;
    border-radius: 10px; font-size: 14px; font-weight: 500;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer; transition: background 0.15s;
  }

  .btn-secondary:hover { background: #f5f5f5; }

  .spinner {
    display: inline-block; width: 14px; height: 14px;
    border: 2px solid #ccc; border-top-color: #1a1a2e;
    border-radius: 50%; animation: spin 0.6s linear infinite;
    vertical-align: middle; margin-right: 6px;
  }

  @keyframes spin { to { transform: rotate(360deg); } }
  @keyframes cardIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="po-page">

  <div class="po-header">
    <div class="po-header-icon">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
    </div>
    <div>
      <h2>Create Purchase Order</h2>
      <p>Upload a PDF to auto-fill, or enter details manually</p>
    </div>
  </div>

  <form method="POST" action="save_po.php" enctype="multipart/form-data" id="po-form">

    <input type="hidden" name="checked_pdf_name" id="checked_pdf_name">

    <div class="po-card">
      <div class="po-card-header">
        <div class="section-dot"></div>
        <span>Import from PDF</span>
      </div>
      <div class="po-card-body">
        <div class="upload-zone" id="upload-zone">
          <input type="file" name="po_pdf" id="po_pdf" accept=".pdf">
          <div class="upload-icon">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          <h3>Drop your PO PDF here</h3>
          <p>or click to browse — fields will auto-fill</p>
          <div class="file-chosen" id="file-chosen">📄 <span id="file-name"></span></div>
        </div>
        <div id="pdf-status"></div>
      </div>
    </div>

    <div class="po-card">
      <div class="po-card-header">
        <div class="section-dot"></div>
        <span>PO Details</span>
      </div>
      <div class="po-card-body">
        <div class="form-grid" style="margin-bottom:16px">
          <div class="field-group">
            <label>PO Number</label>
            <input type="text" name="po_number" id="po_number" placeholder="e.g. JC2PO02359" required>
          </div>
          <div class="field-group">
            <label>Platform</label>
            <select name="platform" id="platform" required>
              <option value="">Select platform</option>
              <option>Instamart</option>
              <option>Blinkit</option>
              <option>Zepto</option>
              <option>Flipkart</option>
            </select>
          </div>
        </div>
        <div class="form-grid" style="margin-bottom:16px">
          <div class="field-group">
            <label>Release Date</label>
            <input type="date" name="release_date" id="release_date" required>
          </div>
          <div class="field-group">
            <label>Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" required>
          </div>
        </div>
        <div class="form-grid single">
          <div class="field-group">
            <label>Factory / Vendor Name</label>
            <input type="text" name="factory_name" id="factory_name" placeholder="e.g. SIMFRA FROZEN FOODS PVT LTD" required>
          </div>
        </div>
      </div>
    </div>

    <div class="po-card">
      <div class="po-card-header">
        <div class="section-dot"></div>
        <span>Line Items</span>
      </div>
      <div class="po-card-body">
        <div class="items-toolbar">
          <div class="items-count">Total: <span id="item-count">1</span> item(s)</div>
          <button type="button" class="btn-add" onclick="addItem()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Item
          </button>
        </div>
        <div id="items-wrapper"></div>
      </div>
    </div>

    <div class="form-actions">
      <button type="button" class="btn-secondary" onclick="history.back()">Cancel</button>
      <button type="submit" class="btn-submit">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Save Purchase Order
      </button>
    </div>

  </form>
</div>

<script>
renderItems([{ item_code: '', item_desc: '', qty: '' }]);

if ('Notification' in window && Notification.permission === 'default') {
  Notification.requestPermission();
}

document.getElementById('po-form').addEventListener('submit', function (e) {
  const poNum = (document.getElementById('po_number').value || '').trim();
  if (poNum) sessionStorage.setItem('po_created', poNum);

  const pdfInput = document.getElementById('po_pdf');
  const checkedPdfName = document.getElementById('checked_pdf_name').value.trim();

  if (pdfInput.files.length > 0) {
    const selectedName = pdfInput.files[0].name.trim();
    if (!checkedPdfName || checkedPdfName !== selectedName) {
      e.preventDefault();
      const status = document.getElementById('pdf-status');
      status.style.color = '#c62828';
      status.innerHTML = '❌ Please wait for PDF duplicate check before saving.';
    }
  }
});

document.getElementById('po_pdf').addEventListener('change', async function () {
  const file = this.files[0];
  if (!file) return;

  const status = document.getElementById('pdf-status');
  const fileChosen = document.getElementById('file-chosen');
  const fileName = document.getElementById('file-name');

  fileName.textContent = file.name;
  fileChosen.style.display = 'block';
  document.getElementById('checked_pdf_name').value = '';

  if (!file.name.toLowerCase().endsWith('.pdf')) {
    status.style.color = '#c62828';
    status.innerHTML = '❌ Only PDF files are allowed.';
    this.value = '';
    fileChosen.style.display = 'none';
    return;
  }

  status.style.color = '#555';
  status.innerHTML = '<span class="spinner"></span> Checking whether this PDF is already uploaded…';

  try {
    const checkFormData = new FormData();
    checkFormData.append('pdf_name', file.name);

    const checkResponse = await fetch('check_pdf_exists.php', {
      method: 'POST',
      body: checkFormData
    });

    const checkData = await checkResponse.json();

    if (checkData.exists) {
      status.style.color = '#c62828';
      status.innerHTML = '❌ This PDF is already uploaded: <b>' + escapeHtml(file.name) + '</b>';
      this.value = '';
      fileChosen.style.display = 'none';
      clearHeader();
      renderItems([{ item_code: '', item_desc: '', qty: '' }]);
      return;
    }

    document.getElementById('checked_pdf_name').value = file.name;

    status.style.color = '#555';
    status.innerHTML = '<span class="spinner"></span> Extracting data from PDF…';

    const formData = new FormData();
    formData.append('po_pdf', file);

    const res = await fetch('extract_pdf_items.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();

    if (data.success) {
      fillHeader(data.header);
      if (data.items && data.items.length > 0) {
        renderItems(data.items);
        status.style.color = '#2e7d32';
        status.innerHTML = '✅ ' + data.items.length + ' item(s) extracted. Select Platform to continue.';
      } else {
        status.style.color = '#e65100';
        status.innerHTML = '⚠️ Header filled but no items found. Please add items manually.';
      }
    } else {
      status.style.color = '#c62828';
      status.innerHTML = '❌ ' + (data.message || 'Could not extract data.');
    }

  } catch (err) {
    status.style.color = '#c62828';
    status.innerHTML = '❌ Error: ' + err.message;
  }
});

const zone = document.getElementById('upload-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
zone.addEventListener('drop', () => zone.classList.remove('dragover'));

function fillHeader(h) {
  if (!h) return;
  if (h.po_number)    setVal('po_number', h.po_number);
  if (h.release_date) setVal('release_date', h.release_date);
  if (h.expiry_date)  setVal('expiry_date', h.expiry_date);
  if (h.factory_name) setVal('factory_name', h.factory_name);
}

function clearHeader() {
  setVal('po_number', '');
  setVal('release_date', '');
  setVal('expiry_date', '');
  setVal('factory_name', '');
}

function setVal(id, val) {
  const el = document.getElementById(id);
  if (el) el.value = val || '';
}

function renderItems(items) {
  const wrapper = document.getElementById('items-wrapper');
  wrapper.innerHTML = '';
  items.forEach((item, i) => wrapper.appendChild(makeCard(item, i)));
  updateCount();
}

function makeCard(item, index) {
  const div = document.createElement('div');
  div.className = 'item-card';
  div.innerHTML =
    '<div class="item-card-num">' + (index + 1) + '</div>' +
    '<div class="field-group"><label>Item Code</label>' +
    '<input type="text" name="item_code[]" value="' + esc(item.item_code) + '" placeholder="e.g. 372304" required></div>' +
    '<div class="field-group"><label>Description</label>' +
    '<input type="text" name="item_description[]" value="' + esc(item.item_desc) + '" placeholder="Item name" required></div>' +
    '<div class="field-group"><label>Qty</label>' +
    '<input type="number" name="qty[]" value="' + esc(item.qty) + '" placeholder="0" required></div>' +
    (index > 0 ? '<button type="button" class="btn-remove" onclick="removeCard(this)">✕ Remove</button>' : '');
  return div;
}

function addItem() {
  const wrapper = document.getElementById('items-wrapper');
  wrapper.appendChild(makeCard({ item_code: '', item_desc: '', qty: '' }, wrapper.children.length));
  updateCount();
}

function removeCard(btn) {
  btn.closest('.item-card').remove();
  document.querySelectorAll('.item-card').forEach((card, i) => {
    const num = card.querySelector('.item-card-num');
    if (num) num.textContent = i + 1;
    const rb = card.querySelector('.btn-remove');
    if (i === 0 && rb) rb.remove();
  });
  updateCount();
}

function updateCount() {
  document.getElementById('item-count').textContent =
    document.getElementById('items-wrapper').children.length;
}

function esc(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function escapeHtml(str) {
  return String(str).replace(/[&<>"']/g, function(m) {
    return ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    })[m];
  });
}
</script>

<?php include 'partials/footer.php'; ?>