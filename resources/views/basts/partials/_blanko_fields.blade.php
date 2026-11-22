<div id="blankoCollectiveSection">
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label small fw-semibold text-secondary mb-0">
                JENIS LEMBAR TANDA TERIMA PETUGAS <span class="text-danger">*</span>
            </label>
            <span class="text-muted" style="font-size: 0.72rem;">
                <i class="bi bi-info-circle-fill me-1"></i> Masing-masing jenis dicetak terpisah per lembar dokumen
            </span>
        </div>
        <div class="row g-2">
            <!-- 1. Tanda Terima Perlengkapan Petugas -->
            <div class="col-12 col-sm-6 col-lg">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatPelatihanLabel" title="1. Tanda Terima Perlengkapan Petugas">
                    <input type="radio" name="receipt_category" id="receiptCatPelatihan" value="PELATIHAN" class="form-check-input mt-0.5" checked onchange="onReceiptCategoryChanged('PELATIHAN')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-person-badge-fill" style="color: #7e22ce;"></i>
                            <span>1. Perlengkapan Petugas</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            Perlengkapan petugas (seragam, tas, ID card &amp; modul)
                        </div>
                    </div>
                </label>
            </div>

            <!-- 2. Tanda Terima ATK Pendataan -->
            <div class="col-12 col-sm-6 col-lg">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatAtkLabel" title="2. Tanda Terima ATK Pendataan">
                    <input type="radio" name="receipt_category" id="receiptCatAtk" value="ATK" class="form-check-input mt-0.5" onchange="onReceiptCategoryChanged('ATK')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-pencil-ruler text-primary"></i>
                            <span>2. ATK Pendataan</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            Alat tulis &amp; perlengkapan pencacahan lapangan
                        </div>
                    </div>
                </label>
            </div>

            <!-- 3. Tanda Terima Kuesioner Pendataan -->
            <div class="col-12 col-sm-6 col-lg">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatKuesLabel" title="3. Tanda Terima Kuesioner Pendataan">
                    <input type="radio" name="receipt_category" id="receiptCatKues" value="KUES" class="form-check-input mt-0.5" onchange="onReceiptCategoryChanged('KUES')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-file-earmark-text text-success"></i>
                            <span>3. Kuesioner Pendataan</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            Blanko &amp; dokumen kuesioner pendataan
                        </div>
                    </div>
                </label>
            </div>

            <!-- 4. Tanda Terima ATK dan Dokumen untuk Responden -->
            <div class="col-12 col-sm-6 col-lg">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatRespondenLabel" title="4. Tanda Terima ATK dan Dokumen untuk Responden">
                    <input type="radio" name="receipt_category" id="receiptCatResponden" value="ATK_RESPONDEN" class="form-check-input mt-0.5" onchange="onReceiptCategoryChanged('ATK_RESPONDEN')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-box2-heart-fill" style="color: #e11d48;"></i>
                            <span>4. ATK &amp; Dok. Responden</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            ATK, cinderamata &amp; dokumen berkas untuk responden
                        </div>
                    </div>
                </label>
            </div>

            <!-- 5. Tanda Terima Timbangan -->
            <div class="col-12 col-sm-6 col-lg">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatTimbanganLabel" title="5. Tanda Terima Timbangan">
                    <input type="radio" name="receipt_category" id="receiptCatTimbangan" value="TIMBANGAN" class="form-check-input mt-0.5" onchange="onReceiptCategoryChanged('TIMBANGAN')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-sliders text-warning"></i>
                            <span>5. Timbangan</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            Alat ukur timbangan (hanya beberapa petugas)
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <div class="card border bg-light-subtle mb-3 rounded-3">
        <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold small text-primary">
                <i class="bi bi-type-h1 me-1"></i> HEADER CETAK TANDA TERIMA (4 BARIS ATAS)
            </span>
            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">Sesuai Format Resmi A4</span>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">BARIS 1: JUDUL DOKUMEN</label>
                    <input type="text" name="document_title" id="bastDocTitleInput" class="form-control form-control-sm" placeholder="Contoh: Tanda Terima ATK Peserta / Blanko & Perlengkapan">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">BARIS 2: SUBJUDUL KEGIATAN / PELATIHAN</label>
                    <input type="text" name="activity_subtitle" id="bastActivitySubtitleInput" class="form-control form-control-sm" placeholder="Contoh: Pelatihan Petugas Sensus Ekonomi 2026 Gelombang II Kelas C">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">BARIS 3: INSTANSI &amp; TAHUN</label>
                    <input type="text" name="institution_name" id="bastInstitutionInput" class="form-control form-control-sm" placeholder="Contoh: BPS Kabupaten Kolaka Utara Tahun {{ now()->format('Y') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">BARIS 4: TEMPAT PELAKSANAAN</label>
                    <input type="text" name="document_place" id="bastDocPlaceInput" class="form-control form-control-sm" placeholder="Contoh: Hotel Berlian Lasusua">
                    <div class="form-text" style="font-size: 0.72rem;">
                        Tanggal otomatis mengikuti tanggal pembuatan dokumen (contoh: <em>Hotel Berlian Lasusua, {{ now()->translatedFormat('j F Y') }}</em>).
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DAFTAR PENERIMA -->
    <div class="card border mb-3 rounded-3">
        <div class="card-header bg-white py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <span class="fw-bold small text-dark">
                    <i class="bi bi-people-fill text-primary me-1"></i> DAFTAR PENERIMA BLANKO &amp; PERLENGKAPAN
                </span>
                <span class="badge bg-primary ms-2 rounded-pill" id="recipientCountBadge">0 penerima</span>
            </div>
            <div class="d-flex flex-wrap gap-1.5 align-items-center">
                <select id="quickOfficerSelect" class="form-select form-select-sm" style="width: 200px;">
                    <option value="">-- Pilih 1 Petugas --</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5" onclick="addSelectedSingleOfficer()">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </button>
                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2.5" onclick="toggleMultiPicker()">
                    <i class="bi bi-check2-square me-1"></i> Pilih Banyak
                </button>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2.5" onclick="loadAssignmentOfficers()" title="Masukkan semua petugas dari kegiatan yang dipilih">
                    <i class="bi bi-download me-1"></i> Dari Kegiatan
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" onclick="addRecipientRow({})">
                    <i class="bi bi-person-plus-fill me-1"></i> Manual
                </button>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2.5" onclick="toggleBastExcelUploadPanel()" title="Unggah file Excel untuk generate daftar penerima sekaligus">
                    <i class="bi bi-file-earmark-excel-fill text-success me-1"></i> Upload Excel
                </button>
            </div>
        </div>

        <!-- Panel Upload Excel Penerima -->
        <div id="bastExcelUploadPanel" class="p-3 bg-light border-bottom d-none">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-file-earmark-excel-fill me-1"></i>Import Excel / CSV</span>
                    <span class="fw-bold small text-dark">Generate Otomatis Daftar Penerima dari File Excel</span>
                </div>
                <div class="d-flex align-items-center gap-1.5">
                    <a href="{{ route('admin.basts.officers.template.download', ['format' => 'xlsx']) }}" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Unduh Template Excel (.xlsx)">
                        <i class="bi bi-download me-1"></i> Template (.xlsx)
                    </a>
                    <a href="{{ route('admin.basts.officers.template.download', ['format' => 'csv']) }}" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Unduh Template CSV (.csv)">
                        <i class="bi bi-filetype-csv me-1"></i> (.csv)
                    </a>
                    <button type="button" class="btn-close ms-2" onclick="toggleBastExcelUploadPanel(false)" aria-label="Tutup"></button>
                </div>
            </div>

            <div class="p-3 bg-white rounded-3 border">
                <div class="row g-2 align-items-center">
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold text-secondary mb-1">PILIH BERKAS EXCEL ATAU CSV</label>
                        <input type="file" id="bastExcelFileInput" class="form-control form-control-sm" accept=".xlsx,.xls,.csv,.txt">
                        <div class="form-text" style="font-size: 0.72rem;">
                            Format kolom didukung: <code>Nama</code>, <code>Penugasan</code> / <code>Jabatan</code>, <code>Kecamatan Asal</code> (seperti format SPKRT). Maksimal 10 MB.
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-secondary mb-1">MODE PENGISIAN</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check small mb-0">
                                <input class="form-check-input" type="radio" name="bast_excel_mode" id="bastExcelModeReplace" value="replace" checked>
                                <label class="form-check-label cursor-pointer" for="bastExcelModeReplace">Gantikan daftar saat ini</label>
                            </div>
                            <div class="form-check small mb-0">
                                <input class="form-check-input" type="radio" name="bast_excel_mode" id="bastExcelModeAppend" value="append">
                                <label class="form-check-label cursor-pointer" for="bastExcelModeAppend">Tambahkan ke daftar</label>
                            </div>
                        </div>
                        <div class="mt-2 text-md-end">
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" id="btnProcessBastExcel" onclick="processBastExcelUpload()">
                                <i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Generate ke Tabel Sekarang
                            </button>
                        </div>
                    </div>
                </div>
                <div id="bastExcelUploadStatus" class="mt-2 d-none"></div>
            </div>
        </div>

        <!-- Panel Pilih Banyak Petugas -->
        <div id="multiOfficerPickerPanel" class="p-3 bg-light border-bottom d-none">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                <input type="text" id="multiPickerSearch" class="form-control form-control-sm" style="max-width: 260px;" placeholder="Cari nama petugas..." oninput="filterMultiPicker()">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-xs btn-light border rounded-pill px-2" onclick="selectAllVisibleMultiPicker(true)">Centang Semua</button>
                    <button type="button" class="btn btn-xs btn-light border rounded-pill px-2" onclick="selectAllVisibleMultiPicker(false)">Kosongkan</button>
                    <button type="button" class="btn btn-xs btn-primary rounded-pill px-2" onclick="applyMultiPickerSelection()">
                        <i class="bi bi-check-lg me-1"></i> Masukkan ke Tabel
                    </button>
                </div>
            </div>
            <div class="row g-2" style="max-height: 175px; overflow-y: auto;" id="multiPickerList">
                @foreach($officers as $officer)
                    <div class="col-md-4 col-sm-6 multi-picker-item" data-name="{{ strtolower($officer->name) }}">
                        <label class="d-flex align-items-center gap-2 p-2 bg-white border rounded small cursor-pointer mb-0">
                            <input type="checkbox" class="form-check-input mt-0 multi-officer-cb" value="{{ $officer->id }}">
                            <span class="text-truncate" title="{{ $officer->name }}">{{ $officer->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-info sticky-top">
                    <tr class="text-center small">
                        <th style="width: 45px;">No.</th>
                        <th style="min-width: 180px;">Nama</th>
                        <th style="min-width: 145px;">Jabatan &amp; Unit Kerja</th>
                        <th style="min-width: 165px;">Asal / Bertugas Sebagai</th>
                        <th style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="recipientTableBody"></tbody>
            </table>
        </div>
        <div class="px-3 py-2 bg-light border-top small text-muted d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <i class="bi bi-shield-check text-success me-1"></i>
                <strong>Tanda Tangan Terkunci:</strong> Petugas yang sudah menandatangani dokumen ini tanda tangannya <strong>tetap tersimpan permanen</strong> dan tidak akan hilang/harus tanda tangan ulang meskipun ada perubahan rincian dokumen.
            </div>
            <div class="text-secondary small">
                <i class="bi bi-printer text-muted me-1"></i> Kolom TTD cetak PDF: TTD digital otomatis terpasang atau format titik-titik (<code>1. ....... / 2. .......</code>).
            </div>
        </div>
    </div>

    <!-- BARANG & PEMBUAT DAFTAR -->
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card border h-100 rounded-3">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold small text-dark">
                        <i class="bi bi-box-seam text-primary me-1"></i> DAFTAR PERLENGKAPAN / ATK (KIRI BAWAH) <span class="text-danger">*</span>
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5" onclick="addBastItemRow('')">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1 px-1 text-muted" style="font-size: 0.7rem; font-weight: 600;">
                        <span style="flex: 1 1 auto; padding-left: 28px;">NAMA BARANG / PERLENGKAPAN</span>
                        <span style="width: 175px; text-align: center;">JUMLAH &amp; SATUAN</span>
                        <span style="width: 32px;"></span>
                    </div>
                    <div id="bastItemsContainer" class="d-flex flex-column gap-2"></div>
                    <datalist id="bastItemUnitsList">
                        <option value="Buah">
                        <option value="Set">
                        <option value="Eks">
                        <option value="Pcs">
                        <option value="Pak">
                        <option value="Rim">
                        <option value="Lembar">
                        <option value="Kotak">
                        <option value="Unit">
                    </datalist>
                    <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                        Ditampilkan berurutan otomatis di pojok kiri bawah BAST PDF (contoh: <code>1. Map Zipper (1 Buah)</code>, <code>2. Buku Panduan (1 Eks)</code>).
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card border h-100 rounded-3">
                <div class="card-header bg-white py-2 px-3">
                    <span class="fw-bold small text-dark">
                        <i class="bi bi-person-gear text-primary me-1"></i> PEMBUAT DAFTAR (KANAN BAWAH)
                    </span>
                </div>
                <div class="card-body p-3">
                    <!-- Dropdown Pilih Pegawai BPS Pembuat Daftar -->
                    <div class="mb-2.5 p-2 rounded-2 bg-light border border-primary-subtle shadow-sm">
                        <label class="form-label small fw-bold text-primary mb-1 d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-person-check-fill me-1"></i> PILIH PEGAWAI BPS (PEMBUAT)</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-normal" style="font-size: 0.65rem;">Otomatis Nama &amp; NIP</span>
                        </label>
                        <select id="blankoPegawaiSelect" class="form-select form-select-sm" onchange="onBlankoPegawaiSelected(this)">
                            <option value="">-- Pilih Pegawai BPS atau Ketik Manual --</option>
                            @foreach(($pegawaiList ?? \App\Models\User::getPegawaiSigners()) as $p)
                                <option value="{{ $p['name'] }}" data-name="{{ $p['name'] }}" data-nip="{{ $p['nip'] }}">
                                    {{ $p['name'] }} {{ $p['nip'] ? '('.$p['nip'].')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text mt-1 text-muted" style="font-size: 0.68rem;">
                            <i class="bi bi-info-circle me-0.5"></i> Memilih nama pegawai otomatis mengisi <strong>Nama</strong> &amp; <strong>NIP</strong> di bawah.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA PEMBUAT DAFTAR</label>
                        <input type="text" name="maker_name" id="bastMakerNameInput" class="form-control form-control-sm" placeholder="Contoh: Muh. Robbith Dinak, S.Tr.Stat.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NIP PEMBUAT DAFTAR (OPSIONAL)</label>
                        <input type="text" name="maker_nip" id="bastMakerNipInput" class="form-control form-control-sm" placeholder="Contoh: 199907202023021001">
                    </div>

                    <!-- Tanda Tangan Digital Pembuat Daftar -->
                    <div class="pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <label class="form-label small fw-semibold text-secondary mb-0">
                                <i class="bi bi-pen-fill text-primary me-1"></i> TANDA TANGAN DIGITAL (OPSIONAL)
                            </label>
                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-0.5" style="font-size: 0.7rem;" onclick="clearMakerSignPad()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Ulangi
                            </button>
                        </div>
                        <input type="hidden" name="maker_signature_data" id="bastMakerSignatureData">

                        <div class="border rounded-3 bg-light position-relative" id="bastMakerSignCanvasWrapper" style="height: 125px; overflow: hidden;">
                            <canvas id="bastMakerSignatureCanvas" class="w-100 h-100" style="cursor: crosshair; touch-action: none;"></canvas>
                            <div id="bastMakerSignPlaceholder" class="position-absolute top-50 start-50 translate-middle text-muted small user-select-none pointer-events-none text-center" style="font-size: 0.72rem; width: 90%;">
                                <i class="bi bi-pen display-6 mb-1 d-block opacity-50" style="font-size: 1.2rem;"></i>
                                Goreskan tanda tangan di sini (touchscreen / mouse)
                            </div>
                        </div>

                        <!-- Pratinjau Tanda Tangan Tersimpan saat Edit -->
                        <div id="bastMakerSignExistingPreview" class="d-none mt-2 p-2 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <img id="bastMakerExistingSignImg" src="" style="max-height: 38px; max-width: 120px; object-fit: contain;">
                                <span class="small text-muted" style="font-size: 0.72rem;">Tanda tangan tersimpan</span>
                            </div>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5" style="font-size: 0.7rem;" onclick="enableMakerSignPadEdit()">
                                <i class="bi bi-pencil me-1"></i> Ubah TTD
                            </button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-1 text-muted" style="font-size: 0.68rem;">
                            <span>Tercetak otomatis di atas nama pembuat pada PDF</span>
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Otomatis Ditrim</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
