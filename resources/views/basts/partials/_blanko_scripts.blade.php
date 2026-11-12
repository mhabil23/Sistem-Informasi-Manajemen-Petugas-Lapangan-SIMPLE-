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

    if (!isEditModalMode && subtitleEl && subtitleEl.value.trim() === '') {
        subtitleEl.value = data.title || '';
    }
    if (!isEditModalMode && placeEl && (placeEl.value.trim() === '' || placeEl.value.trim() === 'Lasusua')) {
        placeEl.value = data.location || 'Lasusua';
    }
    if (!isEditModalMode && data.location) {
        document.querySelectorAll('.rec-origin-input').forEach(el => {
            if (el.value.trim() === '') {
                el.value = data.location;
            }
        });
    }
}

function addRecipientRow(rowData) {
    const tbody = document.getElementById('recipientTableBody');
    const tr = document.createElement('tr');
    const nameVal = rowData.recipient_name || '';
    const posVal = rowData.position_unit || 'Mitra';
    const originVal = rowData.origin_role || '';
    const userIdVal = rowData.user_id || '';

    tr.innerHTML = `
        <td class="text-center fw-semibold small rec-no-cell"></td>
        <td>
            <input type="hidden" class="rec-userid-input" value="${escapeHtmlAttr(userIdVal)}">
            <input type="text" class="form-control form-control-sm rec-name-input" value="${escapeHtmlAttr(nameVal)}" placeholder="Nama penerima">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm rec-pos-input" value="${escapeHtmlAttr(posVal)}" placeholder="Mitra / Jabatan">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm rec-origin-input" value="${escapeHtmlAttr(originVal)}" placeholder="Contoh: Katoi / PML">
        </td>
        <td class="text-center text-nowrap">
            <button type="button" class="btn btn-xs btn-light border" title="Naik" onclick="moveRecipientRow(this, -1)">
                <i class="fa-solid fa-arrow-up"></i>
            </button>
            <button type="button" class="btn btn-xs btn-light border" title="Turun" onclick="moveRecipientRow(this, 1)">
                <i class="fa-solid fa-arrow-down"></i>
            </button>
            <button type="button" class="btn btn-xs btn-light border text-danger" title="Hapus" onclick="removeRecipientRow(this)">
                <i class="fa-solid fa-xmark"></i>
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

function getSelectedReceiptCategory() {
    return document.getElementById('receiptCatKues')?.checked ? 'KUES' : 'ATK';
}

function setReceiptCategory(cat) {
    const normalized = (String(cat || 'ATK').toUpperCase() === 'KUES') ? 'KUES' : 'ATK';
    const atkRadio = document.getElementById('receiptCatAtk');
    const kuesRadio = document.getElementById('receiptCatKues');
    if (atkRadio) atkRadio.checked = (normalized === 'ATK');
    if (kuesRadio) kuesRadio.checked = (normalized === 'KUES');

    const atkLbl = document.getElementById('receiptCatAtkLabel');
    const kuesLbl = document.getElementById('receiptCatKuesLabel');
    if (atkLbl) atkLbl.classList.toggle('border-primary', normalized === 'ATK');
    if (kuesLbl) kuesLbl.classList.toggle('border-info', normalized === 'KUES');

    return normalized;
}

function onReceiptCategoryChanged(cat) {
    const normalized = setReceiptCategory(cat);
    const titleEl = document.getElementById('bastDocTitleInput');
    const defaults = ['', 'Tanda Terima Blanko & Perlengkapan', 'Tanda Terima ATK Peserta', 'Tanda Terima Kuesioner & Dokumen'];
    if (!isEditModalMode && titleEl && defaults.includes(titleEl.value.trim())) {
        titleEl.value = normalized === 'KUES'
            ? 'Tanda Terima Kuesioner & Dokumen'
            : 'Tanda Terima ATK Peserta';
    }
    updateBastItemPlaceholders();
}

function updateBastItemPlaceholders() {
    const isKues = getSelectedReceiptCategory() === 'KUES';
    const placeholders = isKues
        ? ['Contoh: Kuesioner Pencacahan (Set)', 'Contoh: Daftar Sampel / Peta WB', 'Contoh: Buku Pedoman Petugas']
        : ['Contoh: Map Zipper', 'Contoh: Blocknote', 'Contoh: Bolpoin'];
    document.querySelectorAll('#bastItemsContainer .bast-item-input').forEach((input, idx) => {
        input.placeholder = placeholders[idx] || (isKues ? 'Nama kuesioner / dokumen' : 'Nama barang / ATK');
    });
}

function addBastItemRow(value) {
    const container = document.getElementById('bastItemsContainer');
    const row = document.createElement('div');
    row.className = 'input-group input-group-sm bast-item-row';
    const idx = container.children.length;

    row.innerHTML = `
        <span class="input-group-text fw-semibold item-no-label">${idx + 1}.</span>
        <input type="text" name="items[]" class="form-control bast-item-input" value="${escapeHtmlAttr(value)}" placeholder="">
        <button type="button" class="btn btn-outline-danger" title="Hapus item" onclick="removeBastItemRow(this)">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;
    container.appendChild(row);
    renumberBastItems();
    updateBastItemPlaceholders();
}

function removeBastItemRow(btn) {
    const container = document.getElementById('bastItemsContainer');
    if (container.children.length <= 1) {
        btn.closest('.bast-item-row').querySelector('.bast-item-input').value = '';
        return;
    }
    btn.closest('.bast-item-row').remove();
    renumberBastItems();
}

function renumberBastItems() {
    document.querySelectorAll('#bastItemsContainer .bast-item-row').forEach((row, idx) => {
        row.querySelector('.item-no-label').textContent = `${idx + 1}.`;
    });
}

function resetBastModalCommon() {
    document.getElementById('recipientTableBody').innerHTML = '';
    document.getElementById('bastItemsContainer').innerHTML = '';
    document.getElementById('multiOfficerPickerPanel').classList.add('d-none');
}

function initBlankoDefaultFields(defaultBastType) {
    document.getElementById('bastTypeInput').value = defaultBastType || 'kantor_ke_petugas';
    document.getElementById('bastAssignmentInput').value = '';
    document.getElementById('bastNotesInput').value = '';

    document.getElementById('bastDocTitleInput').value = 'Tanda Terima ATK Peserta';
    setReceiptCategory('ATK');
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
    addBastItemRow('');
    renumberRecipientRows();
    toggleBastTypeSections();
}

document.getElementById('officerBastForm')?.addEventListener('submit', function (e) {
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

        const items = Array.from(document.querySelectorAll('.bast-item-input'))
            .map(i => i.value.trim())
            .filter(Boolean);
        if (items.length === 0) {
            e.preventDefault();
            alert('Isi minimal 1 item perlengkapan / blanko (misalnya: Map Zipper).');
            document.querySelector('.bast-item-input')?.focus();
            return;
        }
    }
});
</script>
