<div id="blankoCollectiveSection">
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label small fw-semibold text-secondary mb-0">
                JENIS LEMBAR TANDA TERIMA PETUGAS <span class="text-danger">*</span>
            </label>
            <span class="text-muted" style="font-size: 0.72rem;">
                <i class="fa-solid fa-circle-info me-1"></i> Masing-masing jenis dicetak terpisah per lembar dokumen
            </span>
        </div>
        <div class="row g-2">
            <!-- 1. Perlengkapan Pelatihan -->
            <div class="col-md-3 col-6">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatPelatihanLabel">
                    <input type="radio" name="receipt_category" id="receiptCatPelatihan" value="PELATIHAN" class="form-check-input mt-0.5" checked onchange="onReceiptCategoryChanged('PELATIHAN')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="fa-solid fa-graduation-cap" style="color: #7e22ce;"></i>
                            <span>1. Pelatihan</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            Perlengkapan pelatihan (sekalian ujicoba langsung petugas)
                        </div>
                    </div>
                </label>
            </div>

            <!-- 2. ATK Pendataan -->
            <div class="col-md-3 col-6">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatAtkLabel">
                    <input type="radio" name="receipt_category" id="receiptCatAtk" value="ATK" class="form-check-input mt-0.5" onchange="onReceiptCategoryChanged('ATK')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="fa-solid fa-pen-ruler text-primary"></i>
                            <span>2. ATK Pendataan</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            Alat tulis &amp; perlengkapan pencacahan lapangan
                        </div>
                    </div>
                </label>
            </div>

            <!-- 3. Kuesioner Pendataan -->
            <div class="col-md-3 col-6">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatKuesLabel">
                    <input type="radio" name="receipt_category" id="receiptCatKues" value="KUES" class="form-check-input mt-0.5" onchange="onReceiptCategoryChanged('KUES')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="fa-solid fa-file-lines text-success"></i>
                            <span>3. Kuesioner</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            Blanko &amp; dokumen kuesioner pendataan
                        </div>
                    </div>
                </label>
            </div>

            <!-- 4. Timbangan -->
            <div class="col-md-3 col-6">
                <label class="border rounded-3 p-2.5 d-flex align-items-start gap-2 bg-white w-100 h-100 position-relative" style="cursor: pointer;" id="receiptCatTimbanganLabel">
                    <input type="radio" name="receipt_category" id="receiptCatTimbangan" value="TIMBANGAN" class="form-check-input mt-0.5" onchange="onReceiptCategoryChanged('TIMBANGAN')">
                    <div class="lh-sm">
                        <div class="fw-bold small text-dark d-flex align-items-center gap-1">
                            <i class="fa-solid fa-scale-balanced text-warning"></i>
                            <span>4. Timbangan</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.25;">
                            Alat ukur timbangan (hanya beberapa petugas)
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <div class="card border bg-light-subtle mb-3">
        <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold small text-primary">
                <i class="fa-solid fa-heading me-1"></i> HEADER CETAK TANDA TERIMA (4 BARIS ATAS)
            </span>
            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Sesuai Format Resmi A4</span>
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
    <div class="card border mb-3">
        <div class="card-header bg-white py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <span class="fw-bold small text-dark">
                    <i class="fa-solid fa-users text-primary me-1"></i> DAFTAR PENERIMA BLANKO &amp; PERLENGKAPAN
                </span>
                <span class="badge bg-primary ms-2" id="recipientCountBadge">0 penerima</span>
            </div>
            <div class="d-flex flex-wrap gap-1.5 align-items-center">
                <select id="quickOfficerSelect" class="form-select form-select-sm" style="width: 200px;">
                    <option value="">-- Pilih 1 Petugas --</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSelectedSingleOfficer()">
                    <i class="fa-solid fa-plus me-1"></i> Tambah
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="toggleMultiPicker()">
                    <i class="fa-solid fa-list-check me-1"></i> Pilih Banyak
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="loadAssignmentOfficers()" title="Masukkan semua petugas dari kegiatan yang dipilih">
                    <i class="fa-solid fa-download me-1"></i> Dari Kegiatan
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRecipientRow({})">
                    <i class="fa-solid fa-user-plus me-1"></i> Manual
                </button>
            </div>
        </div>

        <!-- Panel Pilih Banyak Petugas -->
        <div id="multiOfficerPickerPanel" class="p-3 bg-light border-bottom d-none">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                <input type="text" id="multiPickerSearch" class="form-control form-control-sm" style="max-width: 260px;" placeholder="Cari nama petugas..." oninput="filterMultiPicker()">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-xs btn-light border" onclick="selectAllVisibleMultiPicker(true)">Centang Semua</button>
                    <button type="button" class="btn btn-xs btn-light border" onclick="selectAllVisibleMultiPicker(false)">Kosongkan</button>
                    <button type="button" class="btn btn-xs btn-primary" onclick="applyMultiPickerSelection()">
                        <i class="fa-solid fa-check me-1"></i> Masukkan ke Tabel
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
        <div class="px-3 py-2 bg-light border-top small text-muted">
            <i class="fa-solid fa-signature me-1 text-info"></i>
            Kolom <strong>Tanda Tangan (5)</strong> pada hasil cetak PDF otomatis memakai format garis titik-titik selang-seling kiri/kanan (<code>1. .........</code> / <code>2. .........</code>).
        </div>
    </div>

    <!-- BARANG & PEMBUAT DAFTAR -->
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card border h-100">
                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold small text-dark">
                        <i class="fa-solid fa-boxes-packing text-primary me-1"></i> DAFTAR PERLENGKAPAN / ATK (KIRI BAWAH) <span class="text-danger">*</span>
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBastItemRow('')">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body p-3">
                    <div id="bastItemsContainer" class="d-flex flex-column gap-2"></div>
                    <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                        Ditampilkan berurutan otomatis di pojok kiri bawah (contoh: <code>1. Map Zipper</code>, <code>2. Blocknote</code>, <code>3. Bolpoin</code>).
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card border h-100">
                <div class="card-header bg-white py-2 px-3">
                    <span class="fw-bold small text-dark">
                        <i class="fa-solid fa-user-pen text-primary me-1"></i> PEMBUAT DAFTAR (KANAN BAWAH)
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA PEMBUAT DAFTAR</label>
                        <input type="text" name="maker_name" id="bastMakerNameInput" class="form-control form-control-sm" placeholder="Contoh: Muh. Robbith Dinak, S.Tr.Stat.">
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-secondary">NIP PEMBUAT DAFTAR (OPSIONAL)</label>
                        <input type="text" name="maker_nip" id="bastMakerNipInput" class="form-control form-control-sm" placeholder="Contoh: 199907202023021001">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

