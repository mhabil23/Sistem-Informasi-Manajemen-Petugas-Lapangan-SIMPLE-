<!-- ==============================================================
     MODAL IMPORT KEGIATAN & SAMPEL DARI EXCEL / CSV
     ============================================================== -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #198754; width: 32px; height: 32px;">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="importExcelModalLabel" style="font-size: 1rem;">
                            Import Kegiatan, Penugasan &amp; Sampel Responden dari Excel
                        </h5>
                        <div class="text-muted" style="font-size: 0.72rem;">Unggah berkas spreadsheet (.xlsx / .csv) sesuai format database BPS</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.assignments.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Template Download Card -->
                    <div class="p-3 mb-4 rounded-3 border bg-light-subtle d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <i class="bi bi-file-earmark-excel fs-3 text-success"></i>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">Gunakan Format Template Resmi (Kolom Kosong Siap Diisi)</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Template memuat kolom: ID SOBAT, Nama Petugas, Nama Kegiatan, DIPA, Penugasan, Wilayah, &amp; Nama Sampel KRT.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1.5 shrink-0">
                            <a href="{{ route('admin.assignments.template.download', ['format' => 'xlsx']) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5" style="font-size: 0.78rem;" title="Unduh Template Excel Kosong (.xlsx)">
                                <i class="bi bi-file-earmark-excel-fill"></i>
                                <span>Unduh Template (.xlsx)</span>
                            </a>
                            <a href="{{ route('admin.assignments.template.download', ['format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1.5 d-flex align-items-center gap-1.5" style="font-size: 0.78rem;" title="Unduh Template CSV Kosong (.csv)">
                                <i class="bi bi-filetype-csv"></i>
                                <span>(.csv)</span>
                            </a>
                        </div>
                    </div>

                    <!-- File Upload Input -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">
                            PILIH BERKAS EXCEL / CSV <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text" style="font-size: 0.72rem;">Mendukung format <code>.xlsx</code> (Excel modern seperti Google Sheets SPKRT) dan <code>.csv</code>. Maksimal 10 MB.</div>
                    </div>

                    <!-- Jadwal Pelaksanaan (Opsional) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL MULAI KEGIATAN</label>
                            <input type="date" name="start_date" class="form-control" value="{{ now()->toDateString() }}">
                            <div class="form-text" style="font-size: 0.72rem;">Default: Hari ini (dapat diubah)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL SELESAI KEGIATAN</label>
                            <input type="date" name="end_date" class="form-control" value="{{ now()->addDays(14)->toDateString() }}">
                            <div class="form-text" style="font-size: 0.72rem;">Default: 14 hari ke depan</div>
                        </div>
                    </div>

                    <!-- Surat Tugas (Opsional) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-dark mb-1">NOMOR SURAT TUGAS (OPSIONAL)</label>
                            <input type="text" name="st_number" class="form-control" placeholder="Contoh: B-1812/74080/HM.300/2026">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL SURAT TUGAS</label>
                            <input type="date" name="st_date" class="form-control" value="{{ now()->toDateString() }}">
                        </div>
                    </div>

                    <!-- Informasi Cara Kerja Otomatis -->
                    <div class="alert alert-info border-0 rounded-3 p-3 mb-0" style="font-size: 0.75rem; background-color: #f0fdf4; border-left: 4px solid #16a34a !important;">
                        <div class="fw-bold text-success mb-1 d-flex align-items-center gap-1.5">
                            <i class="bi bi-lightning-charge-fill text-warning"></i>
                            Otomatisasi Sistem Sekali Klik:
                        </div>
                        <ul class="mb-0 ps-3 text-secondary" style="line-height: 1.5;">
                            <li><strong>Nama Kegiatan &amp; DIPA</strong> otomatis dibuatkan atau ditautkan ke kegiatan yang relevan.</li>
                            <li><strong>Petugas Lapangan</strong> otomatis ditugaskan; jika akun petugas belum terdaftar di sistem, akun baru akan dibuatkan secara instan (password default: <code>bps7408</code>).</li>
                            <li><strong>Sampel Kepala Keluarga (KRT)</strong> langsung dialokasikan ke masing-masing petugas sesuai baris wilayah tugasnya.</li>
                            <li>Petugas langsung dapat membuka aplikasi SIMPEL di HP untuk melakukan pendataan dan tanda tangan digital BAST responden.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" style="font-size: 0.85rem;">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Mulai Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
