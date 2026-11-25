@extends('layouts.app')

@section('title', 'Template Dokumen')
@section('page_title', 'Template Dokumen')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Template Dokumen</li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .bento-table-container-fixed {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }
    .bento-table {
        table-layout: fixed;
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.8rem;
    }
    .bento-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 0.55rem;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .bento-table tbody td {
        padding: 0.75rem 0.55rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8rem;
        color: #334155;
        background: #ffffff;
        transition: background 0.2s ease;
    }
    .bento-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .bento-table tbody tr:last-child td {
        border-bottom: none;
    }
    .btn-bps-orange {
        background-color: #F37021;
        border-color: #F37021;
        color: #ffffff;
        font-weight: 600;
    }
    .btn-bps-orange:hover, .btn-bps-orange:focus {
        background-color: #d95d13;
        border-color: #d95d13;
        color: #ffffff;
    }
    /* Compact Action Buttons */
    .bento-action-btn {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        transition: all 0.15s ease;
        font-size: 0.8rem;
        padding: 0;
        flex-shrink: 0;
    }
    .bento-action-btn.btn-view { color: #0284C7; border-color: #BAE6FD; background: #F0F9FF; }
    .bento-action-btn.btn-view:hover { background: #0284C7; color: #FFFFFF; }
    .bento-action-btn.btn-edit { color: #D97706; border-color: #FDE68A; background: #FFFBEB; }
    .bento-action-btn.btn-edit:hover { background: #D97706; color: #FFFFFF; }
    .bento-action-btn.btn-toggle { color: #059669; border-color: #A7F3D0; background: #ECFDF5; }
    .bento-action-btn.btn-toggle:hover { background: #059669; color: #FFFFFF; }
    .bento-action-btn.btn-delete { color: #DC2626; border-color: #FECACA; background: #FEF2F2; }
    .bento-action-btn.btn-delete:hover { background: #DC2626; color: #FFFFFF; }
    .bento-action-btn.btn-word { color: #2563EB; border-color: #BFDBFE; background: #EFF6FF; }
    .bento-action-btn.btn-word:hover { background: #2563EB; color: #FFFFFF; }
    .bento-action-btn.btn-pdf { color: #DC2626; border-color: #FECACA; background: #FEF2F2; }
    .bento-action-btn.btn-pdf:hover { background: #DC2626; color: #FFFFFF; }
    @media (min-width: 768px) and (max-width: 1279.98px) {
        .bento-table {
            min-width: 820px;
        }
    }
    @media (max-width: 767.98px) {
        .bento-table {
            table-layout: auto !important;
        }
        .bento-table thead {
            display: none;
        }
        .bento-table, .bento-table tbody, .bento-table tr, .bento-table td {
            display: block;
            width: 100% !important;
        }
        .bento-table tbody tr {
            margin: 0.65rem auto;
            width: calc(100% - 1.25rem) !important;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0;
            background: #fff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            box-sizing: border-box;
        }
        .bento-table tbody td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            text-align: right !important;
            padding: 0.6rem 0.85rem !important;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal !important;
        }
        .bento-table tbody td > div,
        .bento-table tbody td > small {
            max-width: 66%;
            min-width: 0;
        }
        .bento-table tbody td:last-child {
            border-bottom: none;
            background: #f8fafc;
            justify-content: flex-end;
        }
        .bento-table tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            font-size: 0.68rem;
            letter-spacing: 0.03em;
            text-align: left;
            flex-shrink: 0;
        }
        .bento-table tbody td[colspan] {
            display: block;
            text-align: center !important;
            background: transparent !important;
        }
        .bento-table tbody td[colspan]::before {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<!-- Guidance & Cheatsheet Card -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="bento-card p-3 h-100 bg-white border-primary-subtle shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-primary-subtle text-primary p-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <strong class="text-dark" style="font-size: 0.95rem;">Panduan Solusi Perubahan Template Setelah Hosting</strong>
            </div>
            <p class="text-secondary small mb-2" style="line-height: 1.5;">
                Aplikasi SIMPEL memiliki <strong>5 Template Dokumen Resmi</strong> yang aktif digunakan oleh generator PDF (BAST Petugas, BAST Blanko, BAST Responden, Laporan Perjalanan Dinas, &amp; Presensi Pelatihan).
            </p>
            <div class="d-flex flex-column gap-2 small text-muted">
                <div class="p-2 rounded bg-light border">
                    <strong class="text-success d-block mb-1"><i class="bi bi-pencil-square me-1"></i> 1. Solusi Cepat: Edit Teks via Web (Tanpa Akses Server)</strong>
                    Admin dapat mengubah redaksi teks/klausul pernyataan langsung melalui tombol <span class="badge bg-warning-subtle text-warning-emphasis border"><i class="bi bi-pencil-square"></i> Edit</span> di bawah atau unggah berkas Word (<code>.docx</code>). Dokumen cetak PDF akan langsung mengikuti perubahan teks tersebut.
                </div>
                <div class="p-2 rounded bg-light border">
                    <strong class="text-primary d-block mb-1"><i class="bi bi-file-earmark-code me-1"></i> 2. Solusi Layout Presisi: Edit Berkas Blade di Hosting</strong>
                    Jika memerlukan perubahan tata letak presisi (margin kertas dinas, posisi logo BPS, garis tabel ganda, posisi tanda tangan ganda), berkas template terletak di folder <code>resources/views/pdf/*.blade.php</code>. Di server hosting dapat di-update melalui <strong>Git (<code>git pull</code>)</strong> atau <strong>cPanel File Manager</strong>.
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="bento-card p-3 h-100 bg-light border-info-subtle shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-tags text-primary me-2 fs-5"></i>
                <strong class="text-dark small">Panduan Tag Variabel Dinamis (Placeholders):</strong>
            </div>
            <div class="small text-secondary mb-2" style="font-size: 0.75rem;">
                Gunakan tag berikut di dalam isi template agar terisi otomatis saat generate dokumen:
            </div>
            <div class="d-flex flex-wrap gap-1 mb-3">
                <span class="badge bg-white text-primary border" title="Judul Kegiatan Lapangan"><code>{KEGIATAN}</code></span>
                <span class="badge bg-white text-primary border" title="Nomor SPK / Perjanjian Kerja"><code>{NOMOR_KONTRAK}</code></span>
                <span class="badge bg-white text-primary border" title="Tanggal Kontrak Kerja"><code>{TANGGAL_KONTRAK}</code></span>
                <span class="badge bg-white text-primary border" title="Jumlah Dokumen Hasil Lapangan"><code>{JUMLAH_DOKUMEN}</code></span>
                <span class="badge bg-white text-primary border" title="Nama Satuan Kerja BPS"><code>{INSTANSI}</code></span>
                <span class="badge bg-white text-primary border" title="Tanggal Kegiatan / Dokumen"><code>{TANGGAL}</code></span>
                <span class="badge bg-white text-primary border" title="Nama Responden / Pihak Kedua"><code>{NAMA}</code></span>
                <span class="badge bg-white text-primary border" title="Nama Petugas Lapangan"><code>{NAMA_PETUGAS}</code></span>
                <span class="badge bg-white text-primary border" title="Nomor Resmi Dokumen"><code>{NOMOR_DOKUMEN}</code></span>
                <span class="badge bg-white text-primary border" title="Lokasi / Wilayah Tugas"><code>{LOKASI}</code></span>
            </div>
            <div class="p-2 rounded bg-white border border-info-subtle small">
                <i class="bi bi-lightbulb text-warning me-1"></i>
                <span class="text-secondary" style="font-size: 0.73rem;">
                    Klik ikon <i class="bi bi-file-earmark-pdf text-danger"></i> pada kolom aksi untuk melihat langsung dokumen cetak PDF resmi dari masing-masing template.
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="bento-card mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Daftar Template Dokumen Administrasi ({{ $templates->total() }})</h5>
            <span class="badge bg-light text-secondary border mt-1">Halaman {{ $templates->currentPage() }} dari {{ $templates->lastPage() ?: 1 }}</span>
        </div>
        <div>
            <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#createTemplateModal" style="font-size: 0.85rem;">
                <i class="bi bi-plus-circle me-1"></i> Buat Template Baru
            </button>
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 17%;">KODE TEMPLATE</th>
                    <th style="width: 28%;">NAMA TEMPLATE</th>
                    <th style="width: 16%;">TIPE DOKUMEN</th>
                    <th class="text-center" style="width: 10%;">STATUS</th>
                    <th style="width: 10%;">DIPERBARUI</th>
                    <th class="text-end pe-3" style="width: 19%;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $tpl)
                    <tr>
                        <td data-label="Kode Template">
                            <span class="badge bg-light text-primary font-monospace border px-2 py-1">
                                {{ $tpl->code }}
                            </span>
                            @if(in_array($tpl->code, ['bast_officer', 'bast_blanko', 'bast_respondent', 'officer_report', 'training_attendance']))
                                <div class="mt-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.65rem;" title="Terhubung langsung dengan generator berkas PDF resmi">
                                        <i class="bi bi-file-earmark-pdf-fill me-0.5"></i> PDF RESMI
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td data-label="Nama Template">
                            <div style="max-width: min(260px, 100%);">
                                <div class="fw-bold text-dark text-truncate">{{ $tpl->name }}</div>
                                <small class="text-muted text-truncate d-block">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($tpl->content), 50) }}
                                </small>
                            </div>
                        </td>
                        <td data-label="Tipe Dokumen">
                            <span class="badge bg-secondary-subtle text-dark border">
                                {{ ucfirst(str_replace('_', ' ', $tpl->type)) }}
                            </span>
                            @if($tpl->hasFile())
                                <div class="mt-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.65rem;" title="Berkas Word Asli Tersimpan">
                                        <i class="bi bi-file-earmark-word-fill me-0.5"></i> DOCX
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="text-center" data-label="Status">
                            @if($tpl->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check2 me-1"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-muted border px-2 py-1">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td data-label="Diperbarui">
                            <div class="small text-muted">{{ $tpl->updated_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="text-end text-nowrap pe-3" data-label="Aksi">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                @if($tpl->hasFile())
                                    <!-- Unduh Berkas Asli Word -->
                                    <a href="{{ route('admin.templates.download-file', $tpl) }}" 
                                       class="bento-action-btn btn-word" 
                                       title="Unduh Berkas Asli Word (.docx)" 
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-file-earmark-word"></i>
                                    </a>
                                @endif

                                <!-- Preview PDF Asli -->
                                <a href="{{ route('admin.templates.preview-pdf', $tpl) }}" 
                                   target="_blank" 
                                   class="bento-action-btn btn-pdf" 
                                   title="Buka Pratinjau Dokumen PDF Cetak Resmi" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>

                                <!-- Preview Modal -->
                                <button type="button" 
                                        class="bento-action-btn btn-view" 
                                        title="Pratinjau Isi Teks Template" 
                                        data-bs-toggle="tooltip"
                                        onclick="openPreviewModal({{ json_encode($tpl) }})">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Edit -->
                                <button type="button" 
                                        class="bento-action-btn btn-edit" 
                                        title="Edit Template" 
                                        data-bs-toggle="tooltip"
                                        onclick="openEditTemplateModal({{ json_encode($tpl) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Toggle Status -->
                                <form method="POST" action="{{ route('admin.templates.toggle-status', $tpl) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="bento-action-btn btn-toggle {{ $tpl->is_active ? 'text-secondary' : 'text-success' }}" 
                                            title="{{ $tpl->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" 
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>

                                <!-- Hapus -->
                                <form method="POST" action="{{ route('admin.templates.destroy', $tpl) }}" class="d-inline" onsubmit="return confirm('Hapus template {{ addslashes($tpl->name) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bento-action-btn btn-delete" 
                                            title="Hapus Template" 
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                            <div class="fw-semibold">Belum ada template dokumen.</div>
                            <div class="small">Klik tombol "Buat Template Baru" untuk menambahkan template format dokumen.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($templates->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Menampilkan {{ $templates->firstItem() }} sampai {{ $templates->lastItem() }} dari {{ $templates->total() }} template
            </div>
            <div>
                {{ $templates->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL TAMBAH TEMPLATE
     ============================================================== -->
<div class="modal fade" id="createTemplateModal" tabindex="-1" aria-labelledby="createTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createTemplateModalLabel">
                    <i class="bi bi-file-earmark-plus text-primary me-2"></i> Tambah Template Dokumen Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.templates.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Kotak Impor Berkas Word (.docx) -->
                    <div class="p-3 mb-3 rounded-3 border border-primary-subtle bg-primary-subtle bg-opacity-10">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 p-2 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                                    <i class="bi bi-file-earmark-word-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">Impor Format dari Microsoft Word (.docx)</h6>
                                    <div class="text-secondary small" style="font-size: 0.72rem;">Ekstrak otomatis teks, kop, footer, &amp; tag dinamis {TAG} langsung ke formulir</div>
                                </div>
                            </div>
                            <div>
                                <input type="file" id="create_word_file" name="word_file" accept=".docx,.doc" class="d-none" onchange="handleWordFileSelect(this, 'create')">
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm" onclick="document.getElementById('create_word_file').click()">
                                    <i class="bi bi-upload me-1"></i> Pilih Berkas Word (.docx)
                                </button>
                            </div>
                        </div>

                        <!-- Status Ekstraksi Word -->
                        <div id="create_word_status" class="d-none small mt-2"></div>

                        <!-- Tag Dinamis Terdeteksi dari Word -->
                        <div id="create_word_tags" class="d-none small mt-2 pt-2 border-top">
                            <span class="text-secondary fw-semibold me-1"><i class="bi bi-tags text-primary"></i> Tag terdeteksi dari Word:</span>
                            <span id="create_word_tags_list" class="d-inline-flex flex-wrap gap-1"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">KODE IDENTIFIER <span class="text-danger">*</span></label>
                            <input type="text" id="create_code" name="code" class="form-control font-monospace" placeholder="bast_custom_2026" required>
                            <div class="form-text small">Huruf kecil, angka, atau underscore.</div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">NAMA TEMPLATE <span class="text-danger">*</span></label>
                            <input type="text" id="create_name" name="name" class="form-control" placeholder="Contoh: Berita Acara Verifikasi Responden" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TIPE DOKUMEN <span class="text-danger">*</span></label>
                            <select id="create_type" name="type" class="form-select" required>
                                <option value="bast">Berita Acara Serah Terima (BAST)</option>
                                <option value="attendance">Daftar Hadir / Presensi</option>
                                <option value="certificate">Sertifikat / Piagam</option>
                                <option value="report">Format Laporan</option>
                                <option value="general">Umum / Surat Resmi</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center mt-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="tpl_active" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark" for="tpl_active">Status Template Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TEKS KOP / HEADER SURAT (OPSIONAL)</label>
                        <input type="text" id="create_header" name="header_text" class="form-control" placeholder="Kop Surat Pemerintah Kabupaten...">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-semibold text-secondary mb-0">ISI FORMAT TEMPLATE <span class="text-danger">*</span></label>
                            <div class="small text-muted" style="font-size: 0.72rem;">
                                Sisipkan tag: 
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('create_content', '{TANGGAL}')">{TANGGAL}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('create_content', '{NAMA}')">{NAMA}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('create_content', '{INSTANSI}')">{INSTANSI}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('create_content', '{NOMOR_DOKUMEN}')">{NOMOR_DOKUMEN}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('create_content', '{NAMA_PETUGAS}')">{NAMA_PETUGAS}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none" onclick="insertTag('create_content', '{LOKASI}')">{LOKASI}</a>
                            </div>
                        </div>
                        <textarea id="create_content" name="content" class="form-control font-monospace" rows="8" placeholder="Tuliskan format teks dengan tag dinamis, misal: Pada hari ini {TANGGAL}, telah dilakukan serah terima berkas untuk {NAMA} ({INSTANSI})..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TEKS FOOTER / CATATAN KAKI (OPSIONAL)</label>
                        <input type="text" id="create_footer" name="footer_text" class="form-control" placeholder="Dokumen ini diterbitkan sah melalui sistem SIMPEL.">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy me-1"></i> Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT TEMPLATE
     ============================================================== -->
<div class="modal fade" id="editTemplateModal" tabindex="-1" aria-labelledby="editTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editTemplateModalLabel">
                    <i class="bi bi-pencil-square text-warning me-2"></i> Edit Template Dokumen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTemplateForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Kotak Impor / Ganti Berkas Word (.docx) -->
                    <div class="p-3 mb-3 rounded-3 border border-primary-subtle bg-primary-subtle bg-opacity-10">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 p-2 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                                    <i class="bi bi-file-earmark-word-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">Impor / Ganti Berkas Word (.docx)</h6>
                                    <div class="text-secondary small" style="font-size: 0.72rem;">Unggah berkas Word baru untuk memperbarui isi teks &amp; tag secara otomatis</div>
                                </div>
                            </div>
                            <div>
                                <input type="file" id="edit_word_file" name="word_file" accept=".docx,.doc" class="d-none" onchange="handleWordFileSelect(this, 'edit')">
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm" onclick="document.getElementById('edit_word_file').click()">
                                    <i class="bi bi-upload me-1"></i> Pilih Berkas Word (.docx)
                                </button>
                            </div>
                        </div>

                        <!-- Link Berkas Word Saat Ini (jika ada) -->
                        <div id="edit_current_file_wrapper" class="d-none small mt-2">
                            <span class="text-secondary me-1">Berkas Word tersimpan:</span>
                            <a id="edit_current_file_link" href="#" target="_blank" class="badge bg-white text-primary border px-2.5 py-1 text-decoration-none shadow-sm">
                                <i class="bi bi-file-earmark-word me-1"></i> <span id="edit_current_file_name">Unduh Berkas Asli Word (.docx)</span>
                            </a>
                        </div>

                        <!-- Status Ekstraksi Word -->
                        <div id="edit_word_status" class="d-none small mt-2"></div>

                        <!-- Tag Dinamis Terdeteksi dari Word -->
                        <div id="edit_word_tags" class="d-none small mt-2 pt-2 border-top">
                            <span class="text-secondary fw-semibold me-1"><i class="bi bi-tags text-primary"></i> Tag terdeteksi dari Word:</span>
                            <span id="edit_word_tags_list" class="d-inline-flex flex-wrap gap-1"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">KODE IDENTIFIER <span class="text-danger">*</span></label>
                            <input type="text" id="edit_tpl_code" name="code" class="form-control font-monospace" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">NAMA TEMPLATE <span class="text-danger">*</span></label>
                            <input type="text" id="edit_tpl_name" name="name" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TIPE DOKUMEN <span class="text-danger">*</span></label>
                            <select id="edit_tpl_type" name="type" class="form-select" required>
                                <option value="bast">Berita Acara Serah Terima (BAST)</option>
                                <option value="attendance">Daftar Hadir / Presensi</option>
                                <option value="certificate">Sertifikat / Piagam</option>
                                <option value="report">Format Laporan</option>
                                <option value="general">Umum / Surat Resmi</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center mt-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_tpl_active" value="1">
                                <label class="form-check-label fw-semibold text-dark" for="edit_tpl_active">Status Template Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TEKS KOP / HEADER</label>
                        <input type="text" id="edit_tpl_header" name="header_text" class="form-control">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-semibold text-secondary mb-0">ISI FORMAT TEMPLATE <span class="text-danger">*</span></label>
                            <div class="small text-muted" style="font-size: 0.72rem;">
                                Sisipkan tag: 
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('edit_tpl_content', '{TANGGAL}')">{TANGGAL}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('edit_tpl_content', '{NAMA}')">{NAMA}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('edit_tpl_content', '{INSTANSI}')">{INSTANSI}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('edit_tpl_content', '{NOMOR_DOKUMEN}')">{NOMOR_DOKUMEN}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none me-1" onclick="insertTag('edit_tpl_content', '{NAMA_PETUGAS}')">{NAMA_PETUGAS}</a>
                                <a href="javascript:void(0)" class="badge bg-light text-primary border text-decoration-none" onclick="insertTag('edit_tpl_content', '{LOKASI}')">{LOKASI}</a>
                            </div>
                        </div>
                        <textarea id="edit_tpl_content" name="content" class="form-control font-monospace" rows="8" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TEKS FOOTER</label>
                        <input type="text" id="edit_tpl_footer" name="footer_text" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL PREVIEW TEMPLATE
     ============================================================== -->
<div class="modal fade" id="previewTemplateModal" tabindex="-1" aria-labelledby="previewTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="previewTemplateModalLabel">
                    <i class="bi bi-eye text-info me-2"></i> Pratinjau Format Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-1" id="prev_name"></h5>
                        <div class="text-muted small">
                            Kode: <span class="font-monospace text-primary fw-bold" id="prev_code"></span> &bull; 
                            Tipe: <span id="prev_type" class="badge bg-light text-dark border"></span>
                        </div>
                    </div>
                    <div id="prev_word_download_wrapper" class="d-none">
                        <a id="prev_word_download_link" href="#" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold">
                            <i class="bi bi-file-earmark-word me-1"></i> Unduh Berkas Word Asli (.docx)
                        </a>
                    </div>
                </div>

                <div class="bg-light p-3 border rounded-3">
                    <div class="fw-bold text-secondary small border-bottom pb-1 mb-2">HEADER:</div>
                    <div id="prev_header" class="small text-muted fst-italic mb-3"></div>

                    <div class="fw-bold text-secondary small border-bottom pb-1 mb-2">ISI TEMPLATE:</div>
                    <div id="prev_content" class="p-3 bg-white border rounded font-monospace small" style="white-space: pre-wrap; max-height: 250px; overflow-y: auto;"></div>

                    <div class="fw-bold text-secondary small border-bottom pb-1 mb-2 mt-3">FOOTER:</div>
                    <div id="prev_footer" class="small text-muted fst-italic"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.85rem;">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    function insertTag(textareaId, tag) {
        const textarea = document.getElementById(textareaId);
        if (!textarea) return;
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const before = text.substring(0, start);
        const after = text.substring(end, text.length);
        
        textarea.value = before + tag + after;
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
        textarea.focus();
    }

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '_')
            .replace(/^-+|-+$/g, '')
            .substring(0, 45);
    }

    function handleWordFileSelect(input, mode) {
        const file = input.files[0];
        if (!file) return;

        const statusEl = document.getElementById(mode + '_word_status');
        const tagsContainer = document.getElementById(mode + '_word_tags');
        const tagsListEl = document.getElementById(mode + '_word_tags_list');

        statusEl.className = 'small mt-2 alert alert-info py-1.5 px-2 mb-0 d-flex align-items-center';
        statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-2 text-primary" role="status"></span> <span>Mengekstrak teks &amp; tag dinamis dari <strong>' + file.name + '</strong>...</span>';
        tagsContainer.className = 'd-none';

        const formData = new FormData();
        formData.append('word_file', file);
        formData.append('_token', csrfToken);

        fetch('{{ route("admin.templates.extract-word") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status !== 200 || !body.success) {
                statusEl.className = 'small mt-2 alert alert-danger py-1.5 px-2 mb-0';
                statusEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + (body.message || 'Gagal mengekstrak berkas Word.');
                return;
            }

            statusEl.className = 'small mt-2 alert alert-success py-1.5 px-2 mb-0 d-flex align-items-center justify-content-between';
            statusEl.innerHTML = '<div><i class="bi bi-check-circle-fill me-1.5 text-success"></i> Berhasil mengekstrak teks dari <strong>' + body.filename + '</strong> (' + body.lines_count + ' baris/paragraf).</div>';

            // Masukkan ke Textarea
            const contentTextarea = document.getElementById(mode === 'create' ? 'create_content' : 'edit_tpl_content');
            if (contentTextarea) {
                contentTextarea.value = body.content;
            }

            // Jika mode tambah baru, bantu isi nama, kode, header, footer
            if (mode === 'create') {
                const nameInput = document.getElementById('create_name');
                if (nameInput && (!nameInput.value || nameInput.value.trim() === '')) {
                    nameInput.value = body.title || file.name.replace(/\.[^/.]+$/, "");
                }
                const codeInput = document.getElementById('create_code');
                if (codeInput && (!codeInput.value || codeInput.value.trim() === '')) {
                    codeInput.value = slugify(nameInput.value || file.name.replace(/\.[^/.]+$/, ""));
                }
                const headerInput = document.getElementById('create_header');
                if (headerInput && body.header && (!headerInput.value || headerInput.value.trim() === '')) {
                    headerInput.value = body.header;
                }
                const footerInput = document.getElementById('create_footer');
                if (footerInput && body.footer && (!footerInput.value || footerInput.value.trim() === '')) {
                    footerInput.value = body.footer;
                }
            }

            // Render tags dinamis yang terdeteksi
            if (body.tags && body.tags.length > 0) {
                tagsListEl.innerHTML = '';
                const targetTextarea = mode === 'create' ? 'create_content' : 'edit_tpl_content';
                body.tags.forEach(tag => {
                    const tagBadge = document.createElement('a');
                    tagBadge.href = 'javascript:void(0)';
                    tagBadge.className = 'badge bg-white text-primary border text-decoration-none shadow-sm';
                    tagBadge.style.cursor = 'pointer';
                    tagBadge.innerText = tag;
                    tagBadge.title = 'Klik untuk menyisipkan ' + tag;
                    tagBadge.onclick = function() {
                        insertTag(targetTextarea, tag);
                    };
                    tagsListEl.appendChild(tagBadge);
                });
                tagsContainer.className = 'small mt-2 pt-2 border-top';
            } else {
                tagsContainer.className = 'd-none';
            }
        })
        .catch(err => {
            statusEl.className = 'small mt-2 alert alert-danger py-1.5 px-2 mb-0';
            statusEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan saat membaca berkas Word: ' + err.message;
        });
    }

    function openEditTemplateModal(tpl) {
        document.getElementById('editTemplateForm').action = '/admin/templates/' + tpl.id;
        document.getElementById('edit_tpl_code').value = tpl.code;
        document.getElementById('edit_tpl_name').value = tpl.name;
        document.getElementById('edit_tpl_type').value = tpl.type;
        document.getElementById('edit_tpl_active').checked = !!tpl.is_active;
        document.getElementById('edit_tpl_header').value = tpl.header_text || '';
        document.getElementById('edit_tpl_content').value = tpl.content;
        document.getElementById('edit_tpl_footer').value = tpl.footer_text || '';

        // Reset word status
        document.getElementById('edit_word_status').className = 'd-none';
        document.getElementById('edit_word_tags').className = 'd-none';
        document.getElementById('edit_word_file').value = '';

        // Current file link
        const currentFileWrapper = document.getElementById('edit_current_file_wrapper');
        const currentFileLink = document.getElementById('edit_current_file_link');
        if (tpl.file_path) {
            currentFileWrapper.className = 'small mt-2';
            currentFileLink.href = '/admin/templates/' + tpl.id + '/download-file';
        } else {
            currentFileWrapper.className = 'd-none';
        }

        const modal = new bootstrap.Modal(document.getElementById('editTemplateModal'));
        modal.show();
    }

    function openPreviewModal(tpl) {
        document.getElementById('prev_name').innerText = tpl.name;
        document.getElementById('prev_code').innerText = tpl.code;
        document.getElementById('prev_type').innerText = tpl.type;
        document.getElementById('prev_header').innerText = tpl.header_text || '(Tidak ada header khusus)';
        document.getElementById('prev_content').innerText = tpl.content;
        document.getElementById('prev_footer').innerText = tpl.footer_text || '(Tidak ada footer khusus)';

        const downloadWrapper = document.getElementById('prev_word_download_wrapper');
        const downloadLink = document.getElementById('prev_word_download_link');
        if (tpl.file_path) {
            downloadWrapper.className = 'd-block';
            downloadLink.href = '/admin/templates/' + tpl.id + '/download-file';
        } else {
            downloadWrapper.className = 'd-none';
        }

        const modal = new bootstrap.Modal(document.getElementById('previewTemplateModal'));
        modal.show();
    }
</script>
@endpush
