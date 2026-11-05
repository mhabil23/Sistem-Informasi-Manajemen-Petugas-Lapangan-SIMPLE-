@extends('layouts.app')

@section('title', 'Template Dokumen')
@section('page_title', 'Manajemen Template Dokumen')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Template Dokumen</li>
@endsection

@section('content')
<!-- Header -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Manajemen Template Dokumen</h4>
        <p class="text-muted small mb-0">Kelola format dokumen resmi administrasi SPKRT, teks standar kop, klausul BAST, serta tag variabel dinamis untuk generate otomatis.</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
            <i class="fa-solid fa-file-circle-plus me-1"></i> Buat Template Baru
        </button>
    </div>
</div>

<!-- Quick Tag Cheatsheet -->
<div class="alert alert-info border-info-subtle mb-3 shadow-sm py-2 px-3" role="alert">
    <div class="d-flex align-items-center mb-1">
        <i class="fa-solid fa-tags text-primary me-2"></i>
        <strong class="text-dark small">Panduan Tag Variabel Dinamis (Placeholders):</strong>
    </div>
    <div class="small text-secondary" style="font-size: 0.75rem;">
        Gunakan tag berikut di dalam isi template agar terisi otomatis saat generate dokumen:
        <span class="badge bg-light text-primary border mx-1"><code>{NAMA}</code></span>
        <span class="badge bg-light text-primary border mx-1"><code>{NIP}</code></span>
        <span class="badge bg-light text-primary border mx-1"><code>{INSTANSI}</code></span>
        <span class="badge bg-light text-primary border mx-1"><code>{TANGGAL}</code></span>
        <span class="badge bg-light text-primary border mx-1"><code>{NOMOR_DOKUMEN}</code></span>
        <span class="badge bg-light text-primary border mx-1"><code>{NAMA_PETUGAS}</code></span>
        <span class="badge bg-light text-primary border mx-1"><code>{LOKASI}</code></span>
        <span class="badge bg-light text-primary border mx-1"><code>{URAIAN}</code></span>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Template</div>
                <div class="sp-stat-value">{{ number_format($totalTemplates) }}</div>
                <div class="sp-stat-desc">Tersimpan di sistem</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Template Aktif</div>
                <div class="sp-stat-value text-success">{{ number_format($activeCount) }}</div>
                <div class="sp-stat-desc">Siap digunakan</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Engine Generator</div>
                <div class="sp-stat-value text-warning">DomPDF</div>
                <div class="sp-stat-desc">Format A4 Standar</div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-file-code text-primary"></i>
            Daftar Template Dokumen Administrasi ({{ $templates->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 22%;">Kode Template</th>
                        <th style="width: 30%;">Nama Template</th>
                        <th style="width: 20%;">Tipe Dokumen</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th style="width: 10%;">Diperbarui</th>
                        <th class="text-end" style="width: 8%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $tpl)
                        <tr>
                            <td>
                                <span class="badge bg-light text-primary font-monospace border px-2 py-1">
                                    {{ $tpl->code }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $tpl->name }}</div>
                                <small class="text-muted text-truncate d-block" style="max-width: 280px;">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($tpl->content), 50) }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border">
                                    {{ ucfirst(str_replace('_', ' ', $tpl->type)) }}
                                </span>
                            </td>
                            <td>
                                @if($tpl->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-check me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted border px-2 py-1">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-muted">{{ $tpl->updated_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Preview Modal -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-info" 
                                            title="Pratinjau Isi Template" 
                                            data-bs-toggle="tooltip"
                                            onclick="openPreviewModal({{ json_encode($tpl) }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- Edit -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-warning" 
                                            title="Edit Template" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditTemplateModal({{ json_encode($tpl) }})">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- Toggle Status -->
                                    <form method="POST" action="{{ route('admin.templates.toggle-status', $tpl) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-light border sp-action-btn {{ $tpl->is_active ? 'text-secondary' : 'text-success' }}" 
                                                title="{{ $tpl->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" 
                                                data-bs-toggle="tooltip">
                                            <i class="fa-solid fa-power-off"></i>
                                        </button>
                                    </form>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.templates.destroy', $tpl) }}" class="d-inline" onsubmit="return confirm('Hapus template {{ addslashes($tpl->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-light border sp-action-btn text-danger" 
                                                title="Hapus Template" 
                                                data-bs-toggle="tooltip">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-file-circle-question fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada template dokumen.</div>
                                <div class="small">Klik tombol "Buat Template Baru" untuk menambahkan template format dokumen.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($templates->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createTemplateModalLabel">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i> Tambah Template Dokumen Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.templates.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">KODE IDENTIFIER <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="bast_custom_2026" required>
                            <div class="form-text small">Huruf kecil, angka, atau underscore.</div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">NAMA TEMPLATE <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Berita Acara Verifikasi Responden" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TIPE DOKUMEN <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="bast">Berita Acara Serah Terima (BAST)</option>
                                <option value="attendance">Daftar Hadir / Presensi</option>
                                <option value="certificate">Sertifikat / Piagam</option>
                                <option value="report">Format Laporan</option>
                                <option value="general">Umum / Surat Resmi</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="tpl_active" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark" for="tpl_active">Status Template Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TEKS KOP / HEADER SURAT (OPSIONAL)</label>
                        <input type="text" name="header_text" class="form-control" placeholder="Kop Surat Pemerintah Kabupaten...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">ISI FORMAT TEMPLATE <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control font-monospace" rows="8" placeholder="Tuliskan format teks dengan tag dinamis, misal: Pada hari ini {TANGGAL}, telah dilakukan serah terima berkas untuk {NAMA} ({INSTANSI})..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TEKS FOOTER / CATATAN KAKI (OPSIONAL)</label>
                        <input type="text" name="footer_text" class="form-control" placeholder="Dokumen ini diterbitkan sah melalui sistem SIMPLE SPKRT.">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Template
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editTemplateModalLabel">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit Template Dokumen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTemplateForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
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
                        <div class="col-md-6 d-flex align-items-center mt-4">
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
                        <label class="form-label small fw-semibold text-secondary">ISI FORMAT TEMPLATE <span class="text-danger">*</span></label>
                        <textarea id="edit_tpl_content" name="content" class="form-control font-monospace" rows="8" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TEKS FOOTER</label>
                        <input type="text" id="edit_tpl_footer" name="footer_text" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="previewTemplateModalLabel">
                    <i class="fa-solid fa-eye text-info me-2"></i> Pratinjau Format Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <h5 class="fw-bold text-dark" id="prev_name"></h5>
                    <div class="text-muted small">
                        Kode: <span class="font-monospace text-primary" id="prev_code"></span> &bull; 
                        Tipe: <span id="prev_type"></span>
                    </div>
                </div>

                <div class="bg-light p-3 border rounded">
                    <div class="fw-bold text-secondary small border-bottom pb-1 mb-2">HEADER:</div>
                    <div id="prev_header" class="small text-muted fst-italic mb-3"></div>

                    <div class="fw-bold text-secondary small border-bottom pb-1 mb-2">ISI TEMPLATE:</div>
                    <div id="prev_content" class="p-3 bg-white border rounded font-monospace small" style="white-space: pre-wrap; max-height: 250px; overflow-y: auto;"></div>

                    <div class="fw-bold text-secondary small border-bottom pb-1 mb-2 mt-3">FOOTER:</div>
                    <div id="prev_footer" class="small text-muted fst-italic"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditTemplateModal(tpl) {
        document.getElementById('editTemplateForm').action = '/admin/templates/' + tpl.id;
        document.getElementById('edit_tpl_code').value = tpl.code;
        document.getElementById('edit_tpl_name').value = tpl.name;
        document.getElementById('edit_tpl_type').value = tpl.type;
        document.getElementById('edit_tpl_active').checked = !!tpl.is_active;
        document.getElementById('edit_tpl_header').value = tpl.header_text || '';
        document.getElementById('edit_tpl_content').value = tpl.content;
        document.getElementById('edit_tpl_footer').value = tpl.footer_text || '';

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

        const modal = new bootstrap.Modal(document.getElementById('previewTemplateModal'));
        modal.show();
    }
</script>
@endpush

