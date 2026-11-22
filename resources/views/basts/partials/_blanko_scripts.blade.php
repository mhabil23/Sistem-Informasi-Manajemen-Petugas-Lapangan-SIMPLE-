@php
    $assignmentPayload = $assignments->mapWithKeys(fn ($a) => [
        $a->id => [
            'title' => $a->title,
            'location' => $a->location,
            'officers' => $a->officers->map(fn ($o) => [
                'user_id' => $o->id,
                'recipient_name' => $o->name,
                'position_unit' => $o->position ?: ($o->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
                'origin_role' => $a->location ?? '',
            ])->values()->all(),
        ],
    ])->all();

    $officersPayload = $officers->map(fn ($o) => [
        'user_id' => $o->id,
        'recipient_name' => $o->name,
        'position_unit' => $o->position ?: ($o->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
        'phone' => $o->phone ?: $o->username,
        'role_label' => $o->roleLabel(),
    ])->values()->all();
@endphp

<script>
const BAST_ASSIGNMENTS_MAP = @json($assignmentPayload);
const BAST_OFFICERS_LIST = @json($officersPayload);
const BAST_DEFAULT_MAKER = {
    name: @json($defaultMakerName ?? ''),
    nip: @json($defaultMakerNip ?? ''),
};
const BAST_DEFAULT_RECIPIENT = @json($defaultRecipient ?? null);
let isEditModalMode = false;

function escapeHtmlAttr(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function getOfficerById(userId) {
    return BAST_OFFICERS_LIST.find(o => String(o.user_id) === String(userId)) || null;
}

function toggleBastTypeSections() {
    const type = document.getElementById('bastTypeInput').value;
    const isBlanko = (type === 'kantor_ke_petugas');

    document.getElementById('blankoCollectiveSection').classList.toggle('d-none', !isBlanko);
    document.getElementById('terisiSingleSection').classList.toggle('d-none', isBlanko);

    const singleOfficer = document.getElementById('bastSingleOfficerInput');
    const singleDesc = document.getElementById('bastSingleDescriptionInput');
    if (singleOfficer) {
        singleOfficer.disabled = isBlanko;
        singleOfficer.required = !isBlanko;
    }
    if (singleDesc) {
        singleDesc.disabled = isBlanko;
        singleDesc.required = !isBlanko;
    }

    document.querySelectorAll('#blankoCollectiveSection input, #blankoCollectiveSection select').forEach(el => {
        if (!el.classList.contains('multi-officer-cb') && el.id !== 'quickOfficerSelect' && el.id !== 'multiPickerSearch') {
            el.disabled = !isBlanko;
        }
    });
}

function onAssignmentSelected() {
    const assignId = document.getElementById('bastAssignmentInput').value;
    const data = BAST_ASSIGNMENTS_MAP[assignId];
    if (!data) return;

    const subtitleEl = document.getElementById('bastActivitySubtitleInput');
    const placeEl = document.getElementById('bastDocPlaceInput');
    const workNameEl = document.getElementById('terisiActivityWorkName');

    if (!isEditModalMode && subtitleEl && subtitleEl.value.trim() === '') {
        subtitleEl.value = data.title || '';
    }
    if (!isEditModalMode && placeEl && (placeEl.value.trim() === '' || placeEl.value.trim() === 'Lasusua')) {
        placeEl.value = data.location || 'Lasusua';
    }
    if (!isEditModalMode && workNameEl && workNameEl.value.trim() === '') {
        const curYear = new Date().getFullYear();
        workNameEl.value = `Pendataan Lapangan ${data.title || 'Kegiatan'} Tahun ${curYear} pada Badan Pusat Statistik Kabupaten Kolaka Utara`;
    }
    if (!isEditModalMode && data.location) {
        document.querySelectorAll('.rec-origin-input').forEach(el => {
            if (el.value.trim() === '') {
                el.value = data.location;
            }
        });
    }
}

function onTerisiOfficerChanged(selectEl) {
    if (!selectEl) return;
    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt) return;

    const name = opt.getAttribute('data-name') || '';
    const nik = opt.getAttribute('data-nik') || '';
    const address = opt.getAttribute('data-address') || '';

    const nameInput = document.getElementById('terisiSecondPartyName');
    const nikInput = document.getElementById('terisiSecondPartyNik');
    const addrInput = document.getElementById('terisiSecondPartyAddress');

    if (nameInput) nameInput.value = name;
    if (nikInput) nikInput.value = nik;
    if (addrInput) addrInput.value = address || 'Kecamatan Lasusua';
}

function onTerisiPegawaiSelected(selectEl) {
    if (!selectEl) return;
    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) return;

    const name = opt.getAttribute('data-name') || opt.value;
    const nip = opt.getAttribute('data-nip') || '';
    const pos = opt.getAttribute('data-position') || '';

    const nameInput = document.getElementById('terisiMakerName');
    const nipInput = document.getElementById('terisiMakerNip');
    const posInput = document.getElementById('terisiFirstPartyPosition');

    if (nameInput) nameInput.value = name;
    if (nipInput) nipInput.value = nip;
    if (posInput && (name.includes('Hidayatullah') || pos.includes('Keuangan') || pos.includes('PPK'))) {
        posInput.value = 'Pejabat Pembuat Komitmen Badan Pusat Statistik Kabupaten Kolaka Utara';
    }
}

function onBlankoPegawaiSelected(selectEl) {
    if (!selectEl) return;
    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) return;

    const name = opt.getAttribute('data-name') || opt.value;
    const nip = opt.getAttribute('data-nip') || '';

    const nameInput = document.getElementById('bastMakerNameInput');
    const nipInput = document.getElementById('bastMakerNipInput');

    if (nameInput) nameInput.value = name;
    if (nipInput) nipInput.value = nip;
}

function addRecipientRow(rowData) {
    const tbody = document.getElementById('recipientTableBody');
    const tr = document.createElement('tr');
    const nameVal = rowData.recipient_name || '';
    const posVal = rowData.position_unit || 'Mitra';
    const originVal = rowData.origin_role || '';
    const userIdVal = rowData.user_id || '';
    const recIdVal = rowData.recipient_id || rowData.id || '';
    const isSigned = Boolean(rowData.is_signed || rowData.signature_path);

    tr.innerHTML = `
        <td class="text-center fw-semibold small rec-no-cell"></td>
        <td>
            <input type="hidden" class="rec-id-input" value="${escapeHtmlAttr(recIdVal)}">
            <input type="hidden" class="rec-userid-input" value="${escapeHtmlAttr(userIdVal)}">
            <div class="d-flex align-items-center gap-1">
                <input type="text" class="form-control form-control-sm rec-name-input flex-grow-1" value="${escapeHtmlAttr(nameVal)}" placeholder="Nama penerima">
                ${isSigned ? `<span class="badge bg-success-subtle text-success border border-success-subtle flex-shrink-0" style="font-size: 0.65rem;" title="Petugas ini sudah menandatangani digital. Tanda tangan terkunci permanen & tidak akan hilang jika BAST diedit."><i class="bi bi-shield-check me-0.5"></i>TTD Aman</span>` : ''}
            </div>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm rec-pos-input" value="${escapeHtmlAttr(posVal)}" placeholder="Mitra / Jabatan">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm rec-origin-input" value="${escapeHtmlAttr(originVal)}" placeholder="Contoh: Katoi / PML">
        </td>
        <td class="text-center text-nowrap">
            <button type="button" class="btn btn-xs btn-light border" title="Naik" onclick="moveRecipientRow(this, -1)">
                <i class="bi bi-arrow-up"></i>
            </button>
            <button type="button" class="btn btn-xs btn-light border" title="Turun" onclick="moveRecipientRow(this, 1)">
                <i class="bi bi-arrow-down"></i>
            </button>
            <button type="button" class="btn btn-xs btn-light border text-danger" title="Hapus" onclick="removeRecipientRow(this)">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    renumberRecipientRows();
}

function renumberRecipientRows() {
    const rows = document.querySelectorAll('#recipientTableBody tr');
    rows.forEach((tr, idx) => {
        tr.querySelector('.rec-no-cell').textContent = idx + 1;
        const idInput = tr.querySelector('.rec-id-input');
        if (idInput) idInput.name = `recipients[${idx}][recipient_id]`;
        tr.querySelector('.rec-userid-input').name = `recipients[${idx}][user_id]`;
        tr.querySelector('.rec-name-input').name = `recipients[${idx}][recipient_name]`;
        tr.querySelector('.rec-pos-input').name = `recipients[${idx}][position_unit]`;
        tr.querySelector('.rec-origin-input').name = `recipients[${idx}][origin_role]`;
    });
    document.getElementById('recipientCountBadge').textContent = `${rows.length} penerima`;
}

function moveRecipientRow(btn, dir) {
    const tr = btn.closest('tr');
    const tbody = tr.parentNode;
    if (dir < 0 && tr.previousElementSibling) {
        tbody.insertBefore(tr, tr.previousElementSibling);
    } else if (dir > 0 && tr.nextElementSibling) {
        tbody.insertBefore(tr.nextElementSibling, tr);
    }
    renumberRecipientRows();
}

function removeRecipientRow(btn) {
    btn.closest('tr').remove();
    renumberRecipientRows();
}

function hasExistingUserId(userId) {
    if (!userId) return false;
    return Array.from(document.querySelectorAll('.rec-userid-input'))
        .some(input => String(input.value) === String(userId));
}

function addSelectedSingleOfficer() {
    const sel = document.getElementById('quickOfficerSelect');
    const officer = getOfficerById(sel.value);
    if (!officer) return;

    if (hasExistingUserId(officer.user_id)) {
        alert(`Petugas "${officer.recipient_name}" sudah ada di tabel penerima.`);
        return;
    }

    const assignId = document.getElementById('bastAssignmentInput').value;
    const assignLoc = BAST_ASSIGNMENTS_MAP[assignId]?.location || '';

    addRecipientRow({
        user_id: officer.user_id,
        recipient_name: officer.recipient_name,
        position_unit: officer.position_unit,
        origin_role: assignLoc,
    });
    sel.value = '';
}

function loadAssignmentOfficers() {
    const assignId = document.getElementById('bastAssignmentInput').value;
    if (!assignId || !BAST_ASSIGNMENTS_MAP[assignId]) {
        alert('Pilih Kegiatan terlebih dahulu.');
        return;
    }

    const officers = BAST_ASSIGNMENTS_MAP[assignId].officers || [];
    if (officers.length === 0) {
        alert('Belum ada petugas yang terdaftar pada kegiatan ini.');
        return;
    }

    let added = 0;
    officers.forEach(item => {
        if (!hasExistingUserId(item.user_id)) {
            addRecipientRow(item);
            added++;
        }
    });

    if (added === 0) {
        alert('Semua petugas pada kegiatan ini sudah berada di tabel.');
    }
}

function toggleMultiPicker() {
    document.getElementById('multiOfficerPickerPanel').classList.toggle('d-none');
}

function filterMultiPicker() {
    const q = (document.getElementById('multiPickerSearch').value || '').toLowerCase().trim();
    document.querySelectorAll('.multi-picker-item').forEach(el => {
        const name = el.getAttribute('data-name') || '';
        el.classList.toggle('d-none', q !== '' && !name.includes(q));
    });
}

function selectAllVisibleMultiPicker(checked) {
    document.querySelectorAll('.multi-picker-item:not(.d-none) .multi-officer-cb').forEach(cb => {
        cb.checked = checked;
    });
}

function applyMultiPickerSelection() {
    const assignId = document.getElementById('bastAssignmentInput').value;
    const assignLoc = BAST_ASSIGNMENTS_MAP[assignId]?.location || '';

    document.querySelectorAll('.multi-officer-cb:checked').forEach(cb => {
        const officer = getOfficerById(cb.value);
        if (officer && !hasExistingUserId(officer.user_id)) {
            addRecipientRow({
                user_id: officer.user_id,
                recipient_name: officer.recipient_name,
                position_unit: officer.position_unit,
                origin_role: assignLoc,
            });
        }
        cb.checked = false;
    });
    document.getElementById('multiOfficerPickerPanel').classList.add('d-none');
}

function toggleBastExcelUploadPanel(forceState) {
    const panel = document.getElementById('bastExcelUploadPanel');
    if (!panel) return;
    const isHidden = panel.classList.contains('d-none');
    const shouldShow = (forceState !== undefined) ? forceState : isHidden;
    panel.classList.toggle('d-none', !shouldShow);
    if (shouldShow) {
        document.getElementById('multiOfficerPickerPanel')?.classList.add('d-none');
        document.getElementById('bastExcelFileInput')?.focus();
    }
}

function processBastExcelUpload() {
    const fileInput = document.getElementById('bastExcelFileInput');
    const statusEl = document.getElementById('bastExcelUploadStatus');
    const btn = document.getElementById('btnProcessBastExcel');

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        statusEl.className = 'mt-2 alert alert-warning p-2 small mb-0';
        statusEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Silakan pilih berkas Excel (.xlsx) atau CSV terlebih dahulu.';
        statusEl.classList.remove('d-none');
        fileInput?.focus();
        return;
    }

    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append('excel_file', file);
    formData.append('_token', @json(csrf_token()));

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Membaca Excel...';
    statusEl.className = 'mt-2 alert alert-info p-2 small mb-0';
    statusEl.innerHTML = '<span class="spinner-border spinner-border-sm text-primary me-1"></span> Sedang memproses dan membaca data penerima dari berkas Excel...';
    statusEl.classList.remove('d-none');

    fetch(@json(route('admin.basts.officers.parse-recipients-excel')), {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Gagal memproses berkas Excel.');
        }
        return data;
    })
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Generate ke Tabel Sekarang';

        const mode = document.querySelector('input[name="bast_excel_mode"]:checked')?.value || 'replace';
        if (mode === 'replace') {
            document.getElementById('recipientTableBody').innerHTML = '';
        }

        const recipients = data.recipients || [];
        let addedCount = 0;

        recipients.forEach(r => {
            if (r.user_id && hasExistingUserId(r.user_id) && mode === 'append') {
                return;
            }
            addRecipientRow({
                user_id: r.user_id,
                recipient_name: r.recipient_name,
                position_unit: r.position_unit,
                origin_role: r.origin_role,
            });
            addedCount++;
        });

        renumberRecipientRows();

        statusEl.className = 'mt-2 alert alert-success p-2 small mb-0';
        statusEl.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Berhasil!</strong> ${addedCount} penerima berhasil digenerate ke dalam tabel.`;

        setTimeout(() => {
            toggleBastExcelUploadPanel(false);
            fileInput.value = '';
            statusEl.classList.add('d-none');
        }, 2200);
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Generate ke Tabel Sekarang';
        statusEl.className = 'mt-2 alert alert-danger p-2 small mb-0';
        statusEl.innerHTML = `<i class="bi bi-x-circle-fill text-danger me-1"></i> ${escapeHtmlAttr(err.message)}`;
    });
}

const BAST_CATEGORY_DEFAULTS = {
    PELATIHAN: {
        title: 'Tanda Terima Perlengkapan Petugas',
        items: [
            { name: 'Rompi / Seragam Petugas', qty: 1, unit: 'Buah' },
            { name: 'Tas Perlengkapan Petugas', qty: 1, unit: 'Buah' },
            { name: 'Tanda Pengenal / ID Card Petugas', qty: 1, unit: 'Buah' },
            { name: 'Buku Panduan / Modul Pelatihan', qty: 1, unit: 'Eks' },
        ],
        placeholders: ['Contoh: Rompi / Seragam Petugas', 'Contoh: Tas Perlengkapan Petugas', 'Contoh: ID Card Petugas', 'Contoh: Modul Pelatihan'],
        genericPlaceholder: 'Nama perlengkapan petugas',
    },
    ATK: {
        title: 'Tanda Terima ATK Pendataan',
        items: [
            { name: 'Map Zipper / Papan Ujian', qty: 1, unit: 'Buah' },
            { name: 'Pensil 2B & Penghapus', qty: 1, unit: 'Set' },
            { name: 'Rautan Pensil', qty: 1, unit: 'Buah' },
            { name: 'Balpoin / Spidol', qty: 1, unit: 'Buah' },
            { name: 'Tas Lapangan Petugas', qty: 1, unit: 'Buah' },
        ],
        placeholders: ['Contoh: Map Zipper / Papan Ujian', 'Contoh: Pensil 2B & Penghapus', 'Contoh: Balpoin / Spidol', 'Contoh: Tas Lapangan'],
        genericPlaceholder: 'Nama barang / ATK pendataan',
    },
    KUES: {
        title: 'Tanda Terima Kuesioner Pendataan',
        items: [
            { name: 'Kuesioner Utama Pencacahan', qty: 1, unit: 'Set' },
            { name: 'Daftar Sampel Ruta / Peta WB', qty: 1, unit: 'Set' },
            { name: 'Buku Pedoman Pencacah', qty: 1, unit: 'Eks' },
        ],
        placeholders: ['Contoh: Kuesioner Pencacahan (Set)', 'Contoh: Daftar Sampel Ruta / Peta WB', 'Contoh: Buku Pedoman Pencacah'],
        genericPlaceholder: 'Nama dokumen kuesioner',
    },
    ATK_RESPONDEN: {
        title: 'Tanda Terima ATK dan Dokumen untuk Responden',
        items: [
            { name: 'Souvenir / Cinderamata Responden', qty: 1, unit: 'Paket' },
            { name: 'Leaflet / Brosur Informasi Kegiatan', qty: 1, unit: 'Lembar' },
            { name: 'Surat Pemberitahuan Sensus / Survei', qty: 1, unit: 'Lembar' },
            { name: 'Stiker Sampel Responden', qty: 1, unit: 'Lembar' },
        ],
        placeholders: ['Contoh: Souvenir / Cinderamata Responden', 'Contoh: Leaflet Informasi', 'Contoh: Surat Pemberitahuan', 'Contoh: Stiker Sampel'],
        genericPlaceholder: 'Nama ATK / dokumen untuk responden',
    },
    TIMBANGAN: {
        title: 'Tanda Terima Timbangan',
        items: [
            { name: 'Timbangan Digital Portable', qty: 1, unit: 'Unit' },
            { name: 'Baterai Cadangan Timbangan', qty: 1, unit: 'Set' },
            { name: 'Tas / Box Pelindung Timbangan', qty: 1, unit: 'Buah' },
        ],
        placeholders: ['Contoh: Timbangan Digital Portable', 'Contoh: Baterai Cadangan', 'Contoh: Box Pelindung Timbangan'],
        genericPlaceholder: 'Rincian unit timbangan / aksesoris',
    },
};

function getSelectedReceiptCategory() {
    if (document.getElementById('receiptCatPelatihan')?.checked) return 'PELATIHAN';
    if (document.getElementById('receiptCatKues')?.checked) return 'KUES';
    if (document.getElementById('receiptCatResponden')?.checked) return 'ATK_RESPONDEN';
    if (document.getElementById('receiptCatTimbangan')?.checked) return 'TIMBANGAN';
    return 'ATK';
}

function setReceiptCategory(cat) {
    let normalized = String(cat || 'PELATIHAN').toUpperCase();
    if (normalized === 'RESPONDEN') normalized = 'ATK_RESPONDEN';
    if (normalized === 'PERLENGKAPAN') normalized = 'PELATIHAN';
    if (!['PELATIHAN', 'ATK', 'KUES', 'ATK_RESPONDEN', 'TIMBANGAN'].includes(normalized)) {
        normalized = 'PELATIHAN';
    }

    const radios = {
        PELATIHAN: document.getElementById('receiptCatPelatihan'),
        ATK: document.getElementById('receiptCatAtk'),
        KUES: document.getElementById('receiptCatKues'),
        ATK_RESPONDEN: document.getElementById('receiptCatResponden'),
        TIMBANGAN: document.getElementById('receiptCatTimbangan'),
    };
    const labels = {
        PELATIHAN: document.getElementById('receiptCatPelatihanLabel'),
        ATK: document.getElementById('receiptCatAtkLabel'),
        KUES: document.getElementById('receiptCatKuesLabel'),
        ATK_RESPONDEN: document.getElementById('receiptCatRespondenLabel'),
        TIMBANGAN: document.getElementById('receiptCatTimbanganLabel'),
    };

    Object.keys(radios).forEach(key => {
        if (radios[key]) {
            radios[key].checked = (normalized === key);
        }
        if (labels[key]) {
            labels[key].classList.remove('border-primary', 'border-success', 'border-warning', 'border-info', 'border-purple', 'shadow-sm');
            labels[key].style.borderColor = '';
            labels[key].style.backgroundColor = '#ffffff';
        }
    });

    const activeLbl = labels[normalized];
    if (activeLbl) {
        activeLbl.classList.add('shadow-sm');
        if (normalized === 'PELATIHAN') {
            activeLbl.style.borderColor = '#7e22ce';
            activeLbl.style.backgroundColor = '#faf5ff';
        } else if (normalized === 'ATK') {
            activeLbl.style.borderColor = '#0284c7';
            activeLbl.style.backgroundColor = '#f0f9ff';
        } else if (normalized === 'KUES') {
            activeLbl.style.borderColor = '#16a34a';
            activeLbl.style.backgroundColor = '#f0fdf4';
        } else if (normalized === 'ATK_RESPONDEN') {
            activeLbl.style.borderColor = '#e11d48';
            activeLbl.style.backgroundColor = '#fff1f2';
        } else if (normalized === 'TIMBANGAN') {
            activeLbl.style.borderColor = '#d97706';
            activeLbl.style.backgroundColor = '#fffbeb';
        }
    }

    return normalized;
}

function onReceiptCategoryChanged(cat) {
    const normalized = setReceiptCategory(cat);
    const titleEl = document.getElementById('bastDocTitleInput');
    const knownTitles = [
        '',
        'Tanda Terima Blanko & Perlengkapan',
        'Tanda Terima ATK Peserta',
        'Tanda Terima Perlengkapan Pelatihan',
        'Tanda Terima Perlengkapan Petugas',
        'Tanda Terima ATK Pendataan',
        'Tanda Terima Kuesioner Pendataan',
        'Tanda Terima Kuesioner & Dokumen',
        'Tanda Terima ATK dan Dokumen untuk Responden',
        'Tanda Terima Timbangan',
        'Tanda Terima Timbangan Petugas',
    ];

    if (!isEditModalMode && titleEl && knownTitles.includes(titleEl.value.trim())) {
        titleEl.value = BAST_CATEGORY_DEFAULTS[normalized]?.title || 'Tanda Terima Petugas';
    }

    // Bila item masih kosong atau cuma 1 baris kosong, auto-isi dengan rekomendasi template
    const currentInputs = Array.from(document.querySelectorAll('#bastItemsContainer .bast-item-name'));
    const filledValues = currentInputs.map(i => i.value.trim()).filter(Boolean);
    if (!isEditModalMode && filledValues.length <= 1) {
        const defaultItems = BAST_CATEGORY_DEFAULTS[normalized]?.items || [''];
        const container = document.getElementById('bastItemsContainer');
        container.innerHTML = '';
        defaultItems.forEach(item => addBastItemRow(item));
    } else {
        updateBastItemPlaceholders();
    }
}

function updateBastItemPlaceholders() {
    const cat = getSelectedReceiptCategory();
    const config = BAST_CATEGORY_DEFAULTS[cat] || BAST_CATEGORY_DEFAULTS.PELATIHAN;
    document.querySelectorAll('#bastItemsContainer .bast-item-name').forEach((input, idx) => {
        input.placeholder = config.placeholders[idx] || config.genericPlaceholder;
    });
}

function addBastItemRow(itemData) {
    const container = document.getElementById('bastItemsContainer');
    const row = document.createElement('div');
    row.className = 'input-group input-group-sm bast-item-row';
    const idx = container.children.length;

    let name = '';
    let qty = '1';
    let unit = '';

    if (itemData && typeof itemData === 'object') {
        name = itemData.name || '';
        qty = (itemData.qty !== undefined && itemData.qty !== null && itemData.qty !== '') ? itemData.qty : '1';
        unit = itemData.unit || '';
    } else if (typeof itemData === 'string' && itemData.trim() !== '') {
        const raw = itemData.trim();
        const matchParens = raw.match(/^(.*?)\s*\(([0-9]+(?:\.[0-9]+)?)\s*([a-zA-Z\/\.\s]*)\)$/);
        if (matchParens) {
            name = matchParens[1].trim();
            qty = matchParens[2].trim();
            unit = matchParens[3].trim();
        } else {
            const matchPrefix = raw.match(/^([0-9]+(?:\.[0-9]+)?)\s*([a-zA-Z\/\.]+)?\s*[-–:]\s*(.*)$/);
            if (matchPrefix) {
                qty = matchPrefix[1].trim();
                unit = (matchPrefix[2] || '').trim();
                name = matchPrefix[3].trim();
            } else {
                name = raw;
                qty = '1';
                unit = '';
            }
        }
    }

    row.innerHTML = `
        <span class="input-group-text fw-semibold item-no-label">${idx + 1}.</span>
        <input type="text" name="items[${idx}][name]" class="form-control bast-item-name" value="${escapeHtmlAttr(name)}" placeholder="Nama barang / perlengkapan" required>
        <span class="input-group-text bg-light text-muted small px-1.5" style="font-size: 0.72rem;">Jml</span>
        <input type="number" name="items[${idx}][qty]" class="form-control text-center bast-item-qty" value="${escapeHtmlAttr(qty)}" min="1" step="1" placeholder="1" style="max-width: 65px;" title="Jumlah barang" required>
        <input type="text" name="items[${idx}][unit]" class="form-control bast-item-unit" value="${escapeHtmlAttr(unit)}" placeholder="Satuan" list="bastItemUnitsList" style="max-width: 85px;" title="Satuan (Buah, Set, Eks, dll)">
        <button type="button" class="btn btn-outline-danger" title="Hapus item" onclick="removeBastItemRow(this)">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    container.appendChild(row);
    renumberBastItems();
    updateBastItemPlaceholders();
}

function removeBastItemRow(btn) {
    const container = document.getElementById('bastItemsContainer');
    if (container.children.length <= 1) {
        const row = btn.closest('.bast-item-row');
        row.querySelector('.bast-item-name').value = '';
        row.querySelector('.bast-item-qty').value = '1';
        row.querySelector('.bast-item-unit').value = '';
        return;
    }
    btn.closest('.bast-item-row').remove();
    renumberBastItems();
}

function renumberBastItems() {
    document.querySelectorAll('#bastItemsContainer .bast-item-row').forEach((row, idx) => {
        row.querySelector('.item-no-label').textContent = `${idx + 1}.`;
        const nameInput = row.querySelector('.bast-item-name');
        const qtyInput = row.querySelector('.bast-item-qty');
        const unitInput = row.querySelector('.bast-item-unit');
        if (nameInput) nameInput.name = `items[${idx}][name]`;
        if (qtyInput) qtyInput.name = `items[${idx}][qty]`;
        if (unitInput) unitInput.name = `items[${idx}][unit]`;
    });
}

let makerSigPad = null;
let makerHasDrawn = false;

function setupMakerSignaturePad() {
    const canvas = document.getElementById('bastMakerSignatureCanvas');
    if (!canvas) return null;
    const ctx = canvas.getContext('2d');
    let isDrawing = false;

    function resize() {
        const rect = canvas.getBoundingClientRect();
        if (rect.width > 0 && rect.width !== canvas.width) {
            const temp = makerHasDrawn ? canvas.toDataURL() : null;
            canvas.width = rect.width;
            canvas.height = 125;
            ctx.lineWidth = 3.2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#02182b';
            if (temp) {
                const img = new Image();
                img.onload = () => ctx.drawImage(img, 0, 0);
                img.src = temp;
            }
        } else {
            ctx.lineWidth = 3.2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#02182b';
        }
    }

    resize();

    function getCoords(e) {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length > 0) {
            return {
                x: e.touches[0].clientX - rect.left,
                y: e.touches[0].clientY - rect.top
            };
        }
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    function startDraw(e) {
        isDrawing = true;
        makerHasDrawn = true;
        const ph = document.getElementById('bastMakerSignPlaceholder');
        if (ph) ph.style.display = 'none';
        const pos = getCoords(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        if (e.cancelable && e.type.startsWith('touch')) e.preventDefault();
    }

    function draw(e) {
        if (!isDrawing) return;
        const pos = getCoords(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        if (e.cancelable && e.type.startsWith('touch')) e.preventDefault();
    }

    function stopDraw() {
        if (!isDrawing) return;
        isDrawing = false;
        ctx.closePath();
        const input = document.getElementById('bastMakerSignatureData');
        if (input && makerHasDrawn) {
            input.value = canvas.toDataURL('image/png');
        }
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);

    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw);

    return {
        clear: function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            makerHasDrawn = false;
            const input = document.getElementById('bastMakerSignatureData');
            if (input) input.value = '';
            const ph = document.getElementById('bastMakerSignPlaceholder');
            if (ph) ph.style.display = 'block';
        },
        resize: resize,
        hasDrawn: function() { return makerHasDrawn; },
        toDataURL: function() { return canvas.toDataURL('image/png'); }
    };
}

function clearMakerSignPad() {
    if (makerSigPad) {
        makerSigPad.clear();
    }
    const input = document.getElementById('bastMakerSignatureData');
    if (input) input.value = '';
}

function enableMakerSignPadEdit() {
    document.getElementById('bastMakerSignExistingPreview')?.classList.add('d-none');
    document.getElementById('bastMakerSignCanvasWrapper')?.classList.remove('d-none');
    if (!makerSigPad) {
        makerSigPad = setupMakerSignaturePad();
    } else {
        makerSigPad.clear();
        makerSigPad.resize();
    }
}

document.getElementById('officerBastFormModal')?.addEventListener('shown.bs.modal', function () {
    setTimeout(() => {
        if (!makerSigPad) {
            makerSigPad = setupMakerSignaturePad();
        } else {
            makerSigPad.resize();
        }
    }, 150);
});

function resetBastModalCommon() {
    document.getElementById('recipientTableBody').innerHTML = '';
    document.getElementById('bastItemsContainer').innerHTML = '';
    document.getElementById('multiOfficerPickerPanel')?.classList.add('d-none');
    document.getElementById('bastExcelUploadPanel')?.classList.add('d-none');
    const f = document.getElementById('bastExcelFileInput');
    if (f) f.value = '';

    if (makerSigPad) {
        makerSigPad.clear();
    }
    const sigData = document.getElementById('bastMakerSignatureData');
    if (sigData) sigData.value = '';
    document.getElementById('bastMakerSignExistingPreview')?.classList.add('d-none');
    document.getElementById('bastMakerSignCanvasWrapper')?.classList.remove('d-none');
}

function initBlankoDefaultFields(defaultBastType) {
    document.getElementById('bastTypeInput').value = defaultBastType || 'kantor_ke_petugas';
    document.getElementById('bastAssignmentInput').value = '';
    document.getElementById('bastNotesInput').value = '';

    document.getElementById('bastDocTitleInput').value = BAST_CATEGORY_DEFAULTS.PELATIHAN.title;
    setReceiptCategory('PELATIHAN');
    document.getElementById('bastActivitySubtitleInput').value = '';
    document.getElementById('bastInstitutionInput').value = @json('BPS Kabupaten Kolaka Utara Tahun ' . now()->format('Y'));
    document.getElementById('bastDocPlaceInput').value = 'Lasusua';
    document.getElementById('bastMakerNameInput').value = BAST_DEFAULT_MAKER.name || '';
    document.getElementById('bastMakerNipInput').value = BAST_DEFAULT_MAKER.nip || '';

    const singleOfficer = document.getElementById('bastSingleOfficerInput');
    if (singleOfficer) singleOfficer.value = '';
    const singleDesc = document.getElementById('bastSingleDescriptionInput');
    if (singleDesc) singleDesc.value = '';

    if (BAST_DEFAULT_RECIPIENT) {
        addRecipientRow(BAST_DEFAULT_RECIPIENT);
    }
    const container = document.getElementById('bastItemsContainer');
    container.innerHTML = '';
    BAST_CATEGORY_DEFAULTS.PELATIHAN.items.forEach(item => addBastItemRow(item));
    renumberRecipientRows();
    toggleBastTypeSections();
}

document.getElementById('officerBastForm')?.addEventListener('submit', function (e) {
    if (makerSigPad && makerSigPad.hasDrawn()) {
        const sigInput = document.getElementById('bastMakerSignatureData');
        if (sigInput) sigInput.value = makerSigPad.toDataURL();
    }

    const type = document.getElementById('bastTypeInput').value;
    if (type === 'kantor_ke_petugas') {
        const names = Array.from(document.querySelectorAll('.rec-name-input'))
            .map(i => i.value.trim())
            .filter(Boolean);
        if (names.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 petugas / peserta penerima ke dalam daftar.');
            return;
        }

        const items = Array.from(document.querySelectorAll('.bast-item-name'))
            .map(i => i.value.trim())
            .filter(Boolean);
        if (items.length === 0) {
            e.preventDefault();
            alert('Isi minimal 1 item perlengkapan / blanko (misalnya: Map Zipper).');
            document.querySelector('.bast-item-name')?.focus();
            return;
        }
    }
});
</script>
