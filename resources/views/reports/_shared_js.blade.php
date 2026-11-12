<script>
    const ID_MONTHS = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    function formatIndonesianDate(dateStr) {
        if (!dateStr) return '-';
        const clean = dateStr.substring(0, 10);
        const parts = clean.split('-');
        if (parts.length !== 3) return dateStr;
        const year = parts[0];
        const monthIdx = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        if (isNaN(day) || monthIdx < 0 || monthIdx > 11) return dateStr;
        return day + ' ' + ID_MONTHS[monthIdx] + ' ' + year;
    }

    function formatRupiah(angka) {
        const num = Math.max(0, Math.round(Number(angka) || 0));
        return 'Rp. ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',-';
    }

    function terbilangInt(angka) {
        const n = Math.floor(Math.abs(Number(angka) || 0));
        const words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if (n < 12) return words[n];
        if (n < 20) return terbilangInt(n - 10) + ' Belas';
        if (n < 100) return (terbilangInt(Math.floor(n / 10)) + ' Puluh ' + terbilangInt(n % 10)).trim();
        if (n < 200) return ('Seratus ' + terbilangInt(n - 100)).trim();
        if (n < 1000) return (terbilangInt(Math.floor(n / 100)) + ' Ratus ' + terbilangInt(n % 100)).trim();
        if (n < 2000) return ('Seribu ' + terbilangInt(n - 1000)).trim();
        if (n < 1000000) return (terbilangInt(Math.floor(n / 1000)) + ' Ribu ' + terbilangInt(n % 1000)).trim();
        if (n < 1000000000) return (terbilangInt(Math.floor(n / 1000000)) + ' Juta ' + terbilangInt(n % 1000000)).trim();
        if (n < 1000000000000) return (terbilangInt(Math.floor(n / 1000000000)) + ' Miliar ' + terbilangInt(n % 1000000000)).trim();
        return n.toString();
    }

    function terbilangRupiah(angka) {
        const n = Math.round(Number(angka) || 0);
        if (n <= 0) return 'Nol Rupiah';
        return terbilangInt(n).replace(/\s+/g, ' ').trim() + ' Rupiah';
    }

    function buildDefaultExpenseDesc(loc) {
        const cleanLoc = (loc && loc.trim()) ? loc.trim() : 'Wilayah Tugas';
        return `Biaya transport dari ${cleanLoc} ke Kantor BPS Kabupaten Kolaka Utara (P.P.) selama 1 hari`;
    }

    function syncDefaultExpenseDesc(locInputId, tbodyId) {
        const locEl = document.getElementById(locInputId);
        const tbody = document.getElementById(tbodyId);
        if (!locEl || !tbody) return;
        const firstDesc = tbody.querySelector('tr:first-child .expense-desc-input');
        if (!firstDesc) return;
        const val = firstDesc.value.trim();
        if (!val || (val.startsWith('Biaya transport dari ') && val.includes('ke Kantor BPS Kabupaten Kolaka Utara'))) {
            firstDesc.value = buildDefaultExpenseDesc(locEl.value);
        }
    }

    function autoFillLocation(selectElem, targetInputId) {
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        if (selectedOpt) {
            const loc = selectedOpt.getAttribute('data-location');
            const targetInput = document.getElementById(targetInputId);
            if (targetInput && loc) {
                targetInput.value = loc;
            }
        }
    }

    function autoFillLocationAndExpense(selectElem, targetInputId, tbodyId) {
        autoFillLocation(selectElem, targetInputId);
        syncDefaultExpenseDesc(targetInputId, tbodyId);
    }

    function renumberExpenseRows(tbodyId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        tbody.querySelectorAll('tr').forEach((tr, idx) => {
            const noCell = tr.querySelector('td:first-child');
            if (noCell) noCell.textContent = idx + 1;
        });
    }

    function addExpenseRow(tbodyId, totalId, terbilangId, desc = '', amount = 0) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const rowNum = tbody.querySelectorAll('tr').length + 1;

        const tr = document.createElement('tr');
        const tdNo = document.createElement('td');
        tdNo.className = 'text-center small fw-bold';
        tdNo.textContent = rowNum;

        const tdDesc = document.createElement('td');
        const inpDesc = document.createElement('input');
        inpDesc.type = 'text';
        inpDesc.name = 'expense_descriptions[]';
        inpDesc.className = 'form-control form-control-sm expense-desc-input';
        inpDesc.value = desc;
        inpDesc.placeholder = 'Contoh: Biaya transport dari Kantor ke lokasi kegiatan (P.P.) selama 1 hari';
        inpDesc.required = true;
        tdDesc.appendChild(inpDesc);

        const tdAmt = document.createElement('td');
        const inpAmt = document.createElement('input');
        inpAmt.type = 'number';
        inpAmt.name = 'expense_amounts[]';
        inpAmt.className = 'form-control form-control-sm text-end fw-semibold expense-amt-input';
        inpAmt.min = '0';
        inpAmt.value = Number(amount) || 0;
        inpAmt.required = true;
        inpAmt.addEventListener('input', function() {
            recalcExpenses(tbodyId, totalId, terbilangId);
        });
        tdAmt.appendChild(inpAmt);

        const tdAct = document.createElement('td');
        tdAct.className = 'text-center';
        const btnDel = document.createElement('button');
        btnDel.type = 'button';
        btnDel.className = 'btn btn-sm btn-outline-danger py-0 px-1.5';
        btnDel.title = 'Hapus baris';
        btnDel.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        btnDel.addEventListener('click', function() {
            removeExpenseRow(btnDel, tbodyId, totalId, terbilangId);
        });
        tdAct.appendChild(btnDel);

        tr.appendChild(tdNo);
        tr.appendChild(tdDesc);
        tr.appendChild(tdAmt);
        tr.appendChild(tdAct);
        tbody.appendChild(tr);

        recalcExpenses(tbodyId, totalId, terbilangId);
    }

    function removeExpenseRow(btn, tbodyId, totalId, terbilangId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const rows = tbody.querySelectorAll('tr');
        if (rows.length <= 1) {
            const amt = rows[0].querySelector('.expense-amt-input');
            if (amt) amt.value = 0;
        } else {
            const tr = btn.closest('tr');
            if (tr) tr.remove();
            renumberExpenseRows(tbodyId);
        }
        recalcExpenses(tbodyId, totalId, terbilangId);
    }

    function recalcExpenses(tbodyId, totalId, terbilangId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        let total = 0;
        tbody.querySelectorAll('.expense-amt-input').forEach(inp => {
            total += Math.max(0, Math.round(Number(inp.value) || 0));
        });
        const totalEl = document.getElementById(totalId);
        const terbEl = document.getElementById(terbilangId);
        if (totalEl) totalEl.textContent = formatRupiah(total);
        if (terbEl) terbEl.textContent = terbilangRupiah(total);
    }

    function buildStPhrase(rep) {
        if (rep.resolved_st_text) return rep.resolved_st_text;
        const actDate = rep.activity_date ? rep.activity_date.substring(0, 10) : '';
        if (rep.st_number) {
            const stDate = rep.st_date ? rep.st_date.substring(0, 10) : actDate;
            return `Surat Tugas Nomor ${rep.st_number} Tanggal ${formatIndonesianDate(stDate)}`;
        }
        const raw = (rep.basis_st || '').trim().replace(/\.$/, '');
        if (!raw) {
            const year = actDate ? actDate.substring(0, 4) : new Date().getFullYear();
            return `Surat Tugas Nomor B-1812/74080/HM.300/${year} Tanggal ${formatIndonesianDate(actDate)}`;
        }
        return raw.replace(/^(Surat Tugas|Berdasarkan Surat Tugas)\s+(Kepala BPS[^N]*?)?(?=Nomor)/i, 'Surat Tugas ');
    }

    function formatCapturedTime(isoStr) {
        if (!isoStr) return '';
        const d = new Date(isoStr);
        if (isNaN(d.getTime())) return isoStr;
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZoneName: 'short'
        }).format(d);
    }

    function buildAttachmentMetaBlock(att) {
        const meta = document.createElement('div');
        meta.className = 'text-start mt-1 pt-1 border-top';
        meta.style.fontSize = '0.72rem';

        const timeLabel = formatCapturedTime(att.captured_at);
        if (timeLabel) {
            const tDiv = document.createElement('div');
            tDiv.className = 'text-dark fw-semibold';
            tDiv.innerHTML = '<i class="fa-regular fa-clock text-primary me-1"></i>';
            tDiv.appendChild(document.createTextNode(timeLabel));
            meta.appendChild(tDiv);
        }

        if (att.location_address) {
            const lDiv = document.createElement('div');
            lDiv.className = 'text-secondary text-truncate';
            lDiv.title = att.location_address;
            lDiv.innerHTML = '<i class="fa-solid fa-location-dot text-danger me-1"></i>';
            lDiv.appendChild(document.createTextNode(att.location_address));
            meta.appendChild(lDiv);
        }

        if (att.latitude !== null && att.latitude !== undefined && att.longitude !== null && att.longitude !== undefined) {
            const lat = Number(att.latitude).toFixed(6);
            const lng = Number(att.longitude).toFixed(6);
            const cDiv = document.createElement('div');
            cDiv.className = 'font-monospace text-muted';
            cDiv.textContent = `${lat}, ${lng}`;
            meta.appendChild(cDiv);

            const mapLink = document.createElement('a');
            mapLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
            mapLink.target = '_blank';
            mapLink.className = 'badge bg-info-subtle text-info-emphasis border border-info-subtle text-decoration-none mt-1';
            mapLink.innerHTML = '<i class="fa-solid fa-map-location-dot me-1"></i> Lihat Peta GPS';
            meta.appendChild(mapLink);
        } else if (att.caption) {
            const capDiv = document.createElement('div');
            capDiv.className = 'text-muted';
            capDiv.textContent = att.caption;
            meta.appendChild(capDiv);
        }

        return meta;
    }

    function buildPreviewAttachmentCard(att) {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4';

        const card = document.createElement('div');
        card.className = 'card h-100 border p-2 shadow-sm text-center';

        const isImg = att.file_type && att.file_type.startsWith('image/');
        if (isImg) {
            const a = document.createElement('a');
            a.href = '/storage/' + att.file_path;
            a.target = '_blank';
            const img = document.createElement('img');
            img.src = '/storage/' + att.file_path;
            img.alt = att.file_name || 'Foto Dokumentasi';
            img.className = 'img-fluid rounded mb-2';
            img.style.maxHeight = '120px';
            img.style.objectFit = 'cover';
            a.appendChild(img);
            card.appendChild(a);
        } else {
            const pdfBox = document.createElement('div');
            pdfBox.className = 'py-3';
            pdfBox.innerHTML = '<i class="fa-regular fa-file-pdf fa-3x text-danger"></i>';
            card.appendChild(pdfBox);
        }

        const nameEl = document.createElement('div');
        nameEl.className = 'small text-truncate fw-medium text-dark';
        nameEl.title = att.file_name || '';
        nameEl.textContent = att.file_name || '';
        card.appendChild(nameEl);

        card.appendChild(buildAttachmentMetaBlock(att));

        const openBtn = document.createElement('a');
        openBtn.href = '/storage/' + att.file_path;
        openBtn.target = '_blank';
        openBtn.className = 'btn btn-sm btn-link text-decoration-none mt-auto pt-1';
        openBtn.innerHTML = '<i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Foto';
        card.appendChild(openBtn);

        col.appendChild(card);
        return col;
    }

    function buildExistingEditAttachmentCard(att) {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4';

        const card = document.createElement('div');
        card.className = 'card h-100 border p-2 bg-light-subtle';

        const isImg = att.file_type && att.file_type.startsWith('image/');
        if (isImg) {
            const img = document.createElement('img');
            img.src = '/storage/' + att.file_path;
            img.alt = att.file_name || 'Foto Tersimpan';
            img.className = 'img-fluid rounded mb-2 w-100';
            img.style.maxHeight = '105px';
            img.style.objectFit = 'cover';
            card.appendChild(img);
        }

        card.appendChild(buildAttachmentMetaBlock(att));

        const chkWrap = document.createElement('div');
        chkWrap.className = 'form-check mt-2 pt-1 border-top';
        const chk = document.createElement('input');
        chk.type = 'checkbox';
        chk.className = 'form-check-input border-danger';
        chk.name = 'delete_attachment_ids[]';
        chk.value = att.id;
        chk.id = 'del_att_' + att.id;

        const lbl = document.createElement('label');
        lbl.className = 'form-check-label small text-danger fw-semibold';
        lbl.setAttribute('for', chk.id);
        lbl.textContent = 'Hapus foto ini';

        chkWrap.appendChild(chk);
        chkWrap.appendChild(lbl);
        card.appendChild(chkWrap);

        col.appendChild(card);
        return col;
    }

    function populateReportDocumentPreview(rep, basisDefaults = {}) {
        const badge = document.getElementById('det_status_badge');
        if (badge) {
            badge.className = 'badge ' + (rep.status_badge || 'bg-secondary');
            badge.innerText = rep.status_label || rep.status;
        }

        const officerName = rep.resolved_officer_name || rep.officer_name || (rep.user ? rep.user.name : '-') || '-';
        const officerNik = rep.resolved_officer_nik || rep.officer_nip || (rep.user ? rep.user.nik : '-') || '-';
        const officerRank = rep.officer_rank || (rep.user ? rep.user.rank : '-') || '-';
        const officerPosition = rep.officer_position || (rep.user ? rep.user.position : '') || 'Mitra BPS Kabupaten Kolaka Utara';
        const idType = rep.id_type || 'NIK';

        document.getElementById('det_officer_name').innerText = officerName;
        document.getElementById('det_label_id').innerText = idType;
        document.getElementById('det_officer_nip').innerText = officerNik;
        document.getElementById('det_officer_rank').innerText = officerRank;
        document.getElementById('det_officer_position').innerText = officerPosition;

        const year = rep.activity_date ? new Date(rep.activity_date).getFullYear() : new Date().getFullYear();
        const actDateRaw = rep.activity_date ? rep.activity_date.substring(0, 10) : '';
        const actDateFormatted = actDateRaw ? formatIndonesianDate(actDateRaw) : '-';
        document.getElementById('det_basis_dipa').innerText = rep.basis_dipa || basisDefaults.basis_dipa || ('DIPA Satuan Kerja Badan Pusat Statistik Kabupaten Kolaka Utara Tahun Anggaran ' + year + ' Nomor SP DIPA-054.01.2.667477/' + year + ' tanggal 1 Desember ' + (year - 1) + '.');
        document.getElementById('det_basis_st').innerText = rep.basis_st || basisDefaults.basis_st || ('Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor: B-1812/74080/HM.300/' + year + ' Tanggal ' + actDateFormatted + '.');

        document.getElementById('det_destination_purpose').innerText = rep.destination_purpose || ('Perjalanan Dinas Biasa dalam kota lebih dari 8 jam dalam rangka pendataan ' + rep.activity_title + '.');
        document.getElementById('det_duration_info').innerText = (rep.duration_text || 'Selama 1 (satu) Hari') + ', pada tanggal ' + actDateFormatted;
        document.getElementById('det_work_description').innerText = rep.work_description || '-';

        const obsBox = document.getElementById('det_obstacles_box');
        if (rep.obstacles && rep.obstacles.trim()) {
            obsBox.classList.remove('d-none');
            document.getElementById('det_obstacles').innerText = rep.obstacles;
        } else {
            obsBox.classList.add('d-none');
        }

        const solBox = document.getElementById('det_solutions_box');
        if (rep.solutions && rep.solutions.trim()) {
            solBox.classList.remove('d-none');
            document.getElementById('det_solutions').innerText = rep.solutions;
        } else {
            solBox.classList.add('d-none');
        }

        document.getElementById('det_recommendations').innerText = rep.recommendations || '-';

        const signCity = rep.signing_city || 'Lasusua';
        const signDateRaw = rep.signing_date ? rep.signing_date.substring(0, 10) : actDateRaw;
        const signPlaceDate = signCity + ', ' + formatIndonesianDate(signDateRaw);
        const signImgHtml = rep.signature_path
            ? `<img src="/storage/${rep.signature_path}" style="max-height: 60px; max-width: 170px;" alt="Tanda Tangan">`
            : `<div style="height: 55px;"></div>`;

        document.getElementById('det_signing_place_date').innerText = signPlaceDate;
        document.getElementById('det_sign_name').innerText = officerName;
        document.getElementById('det_sign_nip').innerText = (officerNik && officerNik !== '-') ? (idType + '. ' + officerNik) : '';
        document.getElementById('det_sign_img_container').innerHTML = signImgHtml;

        document.getElementById('dpr_officer_name').innerText = officerName;
        document.getElementById('dpr_officer_nik').innerText = officerNik;
        document.getElementById('dpr_officer_position').innerText = officerPosition;
        document.getElementById('dpr_st_text').innerText = buildStPhrase(rep);

        const dprTbody = document.getElementById('dpr_expense_tbody');
        dprTbody.innerHTML = '';
        const locFallback = rep.activity_location || (rep.assignment ? rep.assignment.location : '') || '';
        const expenseItems = (Array.isArray(rep.resolved_expense_items) && rep.resolved_expense_items.length)
            ? rep.resolved_expense_items
            : (Array.isArray(rep.expense_items) && rep.expense_items.length
                ? rep.expense_items
                : [{ description: buildDefaultExpenseDesc(locFallback), amount: 50000 }]);

        let totalExpense = 0;
        expenseItems.forEach((item, idx) => {
            const amt = Math.max(0, Math.round(Number(item.amount) || 0));
            totalExpense += amt;
            const tr = document.createElement('tr');

            const tdNo = document.createElement('td');
            tdNo.className = 'text-center';
            tdNo.style.borderColor = '#000';
            tdNo.textContent = idx + 1;

            const tdDesc = document.createElement('td');
            tdDesc.style.borderColor = '#000';
            tdDesc.textContent = item.description || '-';

            const tdAmt = document.createElement('td');
            tdAmt.className = 'text-center';
            tdAmt.style.borderColor = '#000';
            tdAmt.textContent = formatRupiah(amt);

            tr.appendChild(tdNo);
            tr.appendChild(tdDesc);
            tr.appendChild(tdAmt);
            dprTbody.appendChild(tr);
        });

        document.getElementById('dpr_total_amount').innerText = formatRupiah(totalExpense);
        document.getElementById('dpr_total_terbilang').innerText = terbilangRupiah(totalExpense);
        document.getElementById('dpr_ppk_name').innerText = rep.ppk_name || 'Hidayatullah, SE';
        document.getElementById('dpr_ppk_nip').innerText = rep.ppk_nip || '198510292011011009';
        document.getElementById('dpr_signing_place_date').innerText = signPlaceDate;
        document.getElementById('dpr_sign_img_container').innerHTML = signImgHtml;
        document.getElementById('dpr_sign_name').innerText = officerName;
        document.getElementById('dpr_sign_nip').innerText = (officerNik && officerNik !== '-') ? ('NIK/NIP. ' + officerNik) : '';

        const stmtPaper = document.getElementById('det_vehicle_stmt_paper');
        if (rep.include_vehicle_statement === true || rep.include_vehicle_statement === 1) {
            stmtPaper.classList.remove('d-none');
            document.getElementById('stmt_officer_name').innerText = officerName;
            document.getElementById('stmt_officer_nip').innerText = officerNik;
            document.getElementById('stmt_officer_rank').innerText = officerRank;
            document.getElementById('stmt_officer_position').innerText = officerPosition;

            const loc = locFallback || 'Kabupaten Kolaka Utara';
            document.getElementById('stmt_body_text').innerText = `Menerangkan bahwa dalam rangka ${rep.activity_title} di ${loc} sesuai Surat Perjalanan Dinas ${rep.basis_st || basisDefaults.basis_st || 'Nomor Surat Tugas BPS Kolaka Utara'}. Saya benar-benar tidak menggunakan kendaraan dinas pada tanggal ${actDateFormatted}.`;

            document.getElementById('stmt_signing_place_date').innerText = signPlaceDate;
            document.getElementById('stmt_sign_name').innerText = officerName;
            document.getElementById('stmt_sign_nip').innerText = (officerNik && officerNik !== '-') ? (idType + '. ' + officerNik) : '';
            document.getElementById('stmt_sign_img_container').innerHTML = signImgHtml;
        } else {
            stmtPaper.classList.add('d-none');
        }

        const notesBox = document.getElementById('det_admin_notes_box');
        const notesText = document.getElementById('det_admin_notes_text');
        if (rep.admin_notes) {
            notesBox.classList.remove('d-none');
            notesText.innerText = rep.admin_notes;
        } else {
            notesBox.classList.add('d-none');
        }

        const container = document.getElementById('det_attachments_container');
        container.innerHTML = '';
        if (rep.attachments && rep.attachments.length > 0) {
            rep.attachments.forEach(att => {
                container.appendChild(buildPreviewAttachmentCard(att));
            });
        } else {
            container.innerHTML = '<div class="col-12 text-muted small fst-italic">Tidak ada lampiran foto dokumentasi.</div>';
        }
    }
</script>

