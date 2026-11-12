@extends('layouts.app')

@section('title', 'Laporan Petugas')
@section('page_title', 'Verifikasi Laporan Petugas Lapangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Laporan Petugas</li>
@endsection

@section('content')
<!-- Header -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Verifikasi & Penilaian Laporan Lapangan</h4>
        <p class="text-muted small mb-0">Tinjau laporan kegiatan lapangan dari petugas, periksa bukti dokumentasi foto/berkas, dan berikan status persetujuan atau catatan revisi.</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        @if($submittedCount > 0 || ($draftCount ?? 0) > 0)
            <a href="{{ route('admin.reports.index', ['status' => 'submitted']) }}" class="badge bg-warning text-dark px-2.5 py-1.5 fs-7 shadow-sm text-decoration-none">
                <i class="fa-solid fa-bell me-1"></i> {{ $submittedCount }} Menunggu ACC
                @if(($draftCount ?? 0) > 0)
                    &bull; {{ $draftCount }} Draft Baru
                @endif
            </a>
        @endif
        <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#basisSettingsModal">
            <i class="fa-solid fa-scale-balanced me-1"></i> Atur B. Dasar Pelaksanaan
        </button>
    </div>
</div>

@if(($unreadNotifCount ?? 0) > 0)
    <div class="alert alert-warning border-warning d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 py-2 px-3 shadow-sm">
        <div class="small text-dark">
            <i class="fa-solid fa-bell text-warning me-1"></i>
            <strong>Informasi Baru:</strong> Terdapat <strong>{{ $unreadNotifCount }} pemberitahuan</strong> laporan perjadin baru/diperbarui dari petugas lapangan. Silakan klik ikon <strong>Lihat</strong> (<i class="fa-solid fa-eye"></i>), <strong>Edit Inputan</strong> (<i class="fa-regular fa-pen-to-square"></i>), atau <strong>ACC</strong> (<i class="fa-solid fa-check-double text-success"></i>).
        </div>
        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-xs btn-outline-dark py-1 px-2" style="font-size: 0.72rem;">
                <i class="fa-solid fa-check me-1"></i> Tandai Dibaca
            </button>
        </form>
    </div>
@endif

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Laporan</div>
                <div class="sp-stat-value">{{ number_format($totalReports) }}</div>
                <div class="sp-stat-desc">Semua laporan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-inbox"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Menunggu</div>
                <div class="sp-stat-value text-warning">{{ number_format($submittedCount) }}</div>
                <div class="sp-stat-desc">Perlu verifikasi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Diperiksa</div>
                <div class="sp-stat-value text-primary">{{ number_format($reviewingCount) }}</div>
                <div class="sp-stat-desc">Dalam evaluasi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Perlu Revisi</div>
                <div class="sp-stat-value text-danger">{{ number_format($revisionNeededCount) }}</div>
                <div class="sp-stat-desc">Dikembalikan ke petugas</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Telah Disetujui</div>
                <div class="sp-stat-value text-success">{{ number_format($approvedCount) }}</div>
                <div class="sp-stat-desc">Laporan valid & sah</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-3">
    <div class="sp-card-body p-2.5">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nomor, judul, uraian...">
                </div>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="all">-- Semua Status --</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan (Menunggu)</option>
                    <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Dalam Pemeriksaan</option>
                    <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Perlu Revisi</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="officer_id" class="form-select form-select-sm">
                    <option value="">-- Semua Petugas --</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="assignment_id" class="form-select form-select-sm">
                    <option value="">-- Semua Kegiatan --</option>
                    @foreach($assignments as $assignment)
                        <option value="{{ $assignment->id }}" {{ request('assignment_id') == $assignment->id ? 'selected' : '' }}>{{ $assignment->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'officer_id', 'assignment_id']))
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-4">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-clipboard-check text-primary"></i>
            Daftar Laporan Kegiatan Lapangan ({{ $reports->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 14%;">No. Laporan</th>
                        <th style="width: 16%;">Petugas Lapangan</th>
                        <th style="width: 21%;">Kegiatan &amp; Tanggal</th>
                        <th style="width: 20%;">Uraian Singkat</th>
                        <th class="text-center" style="width: 7%;">Lampiran</th>
                        <th class="text-center" style="width: 9%;">Status</th>
                        <th class="text-end" style="min-width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $rep)
                        <tr>
                            <td>
                                <span class="fw-bold font-monospace text-dark">{{ $rep->report_number ?? 'LAP-'.$rep->id }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $rep->user ? $rep->user->name : '-' }}</div>
                                <small class="text-muted">{{ $rep->user ? $rep->user->email : '' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $rep->activity_title }}</div>
                                <div class="small text-muted">
                                    <i class="fa-regular fa-calendar me-1"></i> {{ $rep->activity_date->format('d/m/Y') }}
                                    @if($rep->assignment)
                                        &bull; <span class="text-secondary">{{ $rep->assignment->title }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="small text-secondary text-truncate" style="max-width: 250px;" title="{{ $rep->work_description }}">
                                    {{ $rep->work_description }}
                                </div>
                                @if($rep->admin_notes)
                                    <div class="small text-danger mt-1">
                                        <i class="fa-solid fa-comment-dots me-1"></i> Catatan: {{ \Illuminate\Support\Str::limit($rep->admin_notes, 40) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($rep->attachments->isNotEmpty())
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-paperclip text-primary me-1"></i> {{ $rep->attachments->count() }} Berkas
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $rep->status_badge }} px-2 py-1">
                                    {{ $rep->status_label }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group d-inline-flex align-items-center gap-1">
                                    <!-- 1. Melihat Laporan Lengkap -->
                                    <button type="button"
                                            class="btn btn-light border sp-action-btn text-info"
                                            title="Lihat Detail &amp; Pratinjau Laporan"
                                            data-bs-toggle="tooltip"
                                            onclick='openViewReportModal(@json($rep))'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- 2. Edit Inputan Laporan Petugas -->
                                    <button type="button"
                                            class="btn btn-light border sp-action-btn text-warning"
                                            title="Edit Inputan Laporan"
                                            data-bs-toggle="tooltip"
                                            onclick='openAdminEditModal(@json($rep))'>
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- 3. ACC Laporan (Setujui Cepat) -->
                                    @if($rep->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.reports.approve', $rep) }}" class="d-inline" onsubmit="return confirm('ACC (Setujui) laporan {{ addslashes($rep->activity_title) }} agar petugas dapat mengunduh PDF resminya?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-success btn-sm px-2 py-1 fw-semibold"
                                                    title="ACC / Setujui Laporan"
                                                    data-bs-toggle="tooltip"
                                                    style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-check-double me-0.5"></i> ACC
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-1.5" style="font-size: 0.68rem;" title="Sudah Di-ACC (Petugas dapat download PDF)">
                                            <i class="fa-solid fa-circle-check me-0.5"></i> ACC
                                        </span>
                                    @endif

                                    <!-- Verifikasi / Revisi dengan Catatan -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-primary" 
                                            title="Verifikasi / Catatan Revisi" 
                                            data-bs-toggle="tooltip"
                                            onclick='openReviewModal(@json($rep))'>
                                        <i class="fa-solid fa-file-circle-check"></i>
                                    </button>

                                    <!-- Pratinjau / Cetak PDF -->
                                    <a href="{{ route('admin.reports.preview', $rep) }}" 
                                       target="_blank"
                                       class="btn btn-light border sp-action-btn text-secondary" 
                                       title="Pratinjau / Cetak Dokumen PDF" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-print"></i>
                                    </a>

                                    <!-- Unduh PDF Resmi -->
                                    <a href="{{ route('admin.reports.download', $rep) }}" 
                                       class="btn btn-light border sp-action-btn text-danger" 
                                       title="Unduh PDF Resmi" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>

                                    <!-- Hapus Laporan -->
                                    <form method="POST" action="{{ route('admin.reports.destroy', $rep) }}" class="d-inline" onsubmit="return confirm('Hapus laporan {{ addslashes($rep->activity_title) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-light border sp-action-btn text-danger" 
                                                title="Hapus Laporan" 
                                                data-bs-toggle="tooltip">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-clipboard-check fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada laporan kegiatan ditemukan.</div>
                                <div class="small">Laporan dari petugas lapangan akan otomatis tampil di sini untuk diverifikasi.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($reports->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $reports->firstItem() }} sampai {{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
            </div>
            <div>
                {{ $reports->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL REVIEW / VERIFIKASI LAPORAN
     ============================================================== -->
<div class="modal fade" id="reviewReportModal" tabindex="-1" aria-labelledby="reviewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="reviewReportModalLabel">
                    <i class="fa-solid fa-clipboard-check text-primary me-2"></i> Periksa & Verifikasi Laporan Kegiatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewReportForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Rincian Laporan -->
                    <div class="row g-3 mb-3 bg-light p-3 rounded border">
                        <div class="col-sm-6">
                            <div class="text-muted small">Nomor Laporan:</div>
                            <div class="fw-bold font-monospace text-dark" id="rev_number"></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Petugas Lapangan:</div>
                            <div class="fw-bold text-dark" id="rev_officer"></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Judul Kegiatan:</div>
                            <div class="fw-bold text-primary" id="rev_title"></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Tanggal Pelaksanaan:</div>
                            <div class="fw-semibold text-dark" id="rev_date"></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small">Lokasi Pelaksanaan:</div>
                            <div class="fw-semibold text-primary" id="rev_location"></div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small mb-1">Uraian Pekerjaan / Hasil Lapangan:</div>
                            <div class="p-3 bg-white border rounded text-dark small" style="white-space: pre-wrap; max-height: 180px; overflow-y: auto;" id="rev_description"></div>
                        </div>
                    </div>

                    <!-- Galeri Lampiran -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">
                            <i class="fa-solid fa-paperclip text-primary me-1"></i> BUKTI DOKUMENTASI &amp; LAMPIRAN:
                        </label>
                        <div id="rev_attachments_container" class="row g-2">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- B. Dasar Pelaksanaan (Dapat disesuaikan Admin per Laporan) -->
                    @include('admin.partials._basis_fields', ['prefix' => 'rev', 'defaults' => [], 'required' => false])

                    <!-- Aksi Verifikasi Admin -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-gavel text-primary me-2"></i> Keputusan Verifikasi Admin</h6>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS PERSETUJUAN <span class="text-danger">*</span></label>
                            <select id="rev_status_select" name="status" class="form-select" required>
                                <option value="approved">✅ Disetujui (Laporan Sah & Sesuai)</option>
                                <option value="revision_needed">⚠️ Perlu Revisi (Kembalikan ke Petugas)</option>
                                <option value="reviewing">🔍 Dalam Pemeriksaan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">CATATAN / FEEDBACK UNTUK PETUGAS</label>
                            <textarea id="rev_admin_notes" name="admin_notes" class="form-control" rows="3" placeholder="Tuliskan catatan perbaikan atau rekomendasi untuk petugas lapangan..."></textarea>
                            <div class="form-text small">Wajib diisi jika status dipilih 'Perlu Revisi'.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top d-flex justify-content-between">
                    <div class="d-flex gap-2">
                        <a id="rev_preview_link" href="#" target="_blank" class="btn btn-outline-primary">
                            <i class="fa-solid fa-print me-1"></i> Cetak / Preview PDF
                        </a>
                        <a id="rev_download_link" href="#" class="btn btn-outline-danger">
                            <i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-sp-primary">
                            <i class="fa-solid fa-check-double me-1"></i> Simpan Hasil Verifikasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ==============================================================
     MODAL PENGATURAN DEFAULT B. DASAR PELAKSANAAN (ROLE ADMIN)
     ============================================================== -->
<div class="modal fade" id="basisSettingsModal" tabindex="-1" aria-labelledby="basisSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold" id="basisSettingsModalLabel">
                        <i class="fa-solid fa-scale-balanced text-primary me-2"></i> Atur B. Dasar Pelaksanaan (Role Admin)
                    </h5>
                    <div class="small text-muted">Nilai DIPA &amp; Surat Tugas di sini otomatis diterapkan saat petugas mengisi Laporan Perjadin &amp; Pengeluaran Riil.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.reports.basis-settings') }}">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    @include('admin.partials._basis_fields', ['prefix' => 'master', 'defaults' => $basisSettings, 'required' => true])
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengaturan Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL LIHAT DETAIL & PRATINJAU DOKUMEN (ADMIN)
     ============================================================== -->
<div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="viewReportModalLabel">
                        <i class="fa-solid fa-eye text-info me-2"></i> Lihat Detail &amp; Dokumen Laporan Petugas
                    </h5>
                    <span id="det_status_badge" class="badge"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                @include('reports._document_preview_body')
            </div>
            <div class="modal-footer bg-light border-top d-flex flex-wrap justify-content-between gap-2">
                <div class="d-flex gap-2">
                    <a id="adm_view_preview_link" href="#" target="_blank" class="btn btn-outline-primary">
                        <i class="fa-solid fa-print me-1"></i> Cetak / Preview PDF
                    </a>
                    <a id="adm_view_download_link" href="#" class="btn btn-outline-danger">
                        <i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <button type="button" class="btn btn-warning text-dark fw-semibold" onclick="switchAdminViewToEdit()">
                        <i class="fa-regular fa-pen-to-square me-1"></i> Edit Inputan Laporan
                    </button>
                    <form id="adm_view_approve_form" method="POST" action="" class="d-inline" onsubmit="return confirm('ACC (Setujui) laporan ini agar petugas dapat mengunduh PDF resminya?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success fw-semibold">
                            <i class="fa-solid fa-check-double me-1"></i> ACC / Setujui Laporan
                        </button>
                    </form>
                    <span id="adm_view_already_acc_badge" class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 d-none">
                        <i class="fa-solid fa-circle-check me-1"></i> Sudah Di-ACC Admin
                    </span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT INPUTAN LAPORAN PETUGAS (ROLE ADMIN)
     ============================================================== -->
<div class="modal fade" id="adminEditReportModal" tabindex="-1" aria-labelledby="adminEditReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="adminEditReportModalLabel">
                        <i class="fa-regular fa-pen-to-square text-warning me-2"></i> Edit Inputan Laporan Perjadin Petugas
                    </h5>
                    <div class="small text-muted">Administrator dapat mengoreksi seluruh isian laporan, rincian pengeluaran riil, maupun dasar pelaksanaan, lalu menyimpan atau langsung ACC.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adminEditReportForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- A. Identitas Pelaksana -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-user-tie text-primary me-1"></i> A. YANG MELAKUKAN PERJALANAN DINAS
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="adm_edit_officer_name" name="officer_name" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">Jenis Identitas</label>
                                    <select id="adm_edit_id_type" name="id_type" class="form-select">
                                        <option value="NIK">NIK (KTP)</option>
                                        <option value="NIP">NIP (Pegawai/PNS)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Nomor NIK / NIP</label>
                                    <input type="text" id="adm_edit_officer_nip" name="officer_nip" class="form-control font-monospace">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Pangkat / Golongan</label>
                                    <input type="text" id="adm_edit_officer_rank" name="officer_rank" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Jabatan</label>
                                    <input type="text" id="adm_edit_officer_position" name="officer_position" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- B. Dasar Pelaksanaan -->
                    @include('admin.partials._basis_fields', ['prefix' => 'adm_edit', 'defaults' => $basisSettings, 'required' => false])

                    <!-- C & D. Tujuan & Waktu -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-map-location-dot text-primary me-1"></i> C &amp; D. TUJUAN &amp; WAKTU PERJALANAN DINAS
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small fw-semibold text-secondary">Judul Kegiatan / Agenda <span class="text-danger">*</span></label>
                                    <input type="text" id="adm_edit_title" name="activity_title" class="form-control" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Penugasan Terkait</label>
                                    <select id="adm_edit_assignment" name="assignment_id" class="form-select" onchange="autoFillLocationAndExpense(this, 'adm_edit_activity_location', 'adm_edit_expense_list')">
                                        <option value="" data-location="">-- Kegiatan Mandiri / Umum --</option>
                                        @foreach($assignments as $asgn)
                                            <option value="{{ $asgn->id }}" data-location="{{ $asgn->location }}">{{ $asgn->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-secondary">Lokasi / Wilayah Tugas / Asal Keberangkatan</label>
                                    <input type="text" id="adm_edit_activity_location" name="activity_location" class="form-control" oninput="syncDefaultExpenseDesc('adm_edit_activity_location', 'adm_edit_expense_list')">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">C. Tujuan Perjalanan Dinas (Teks Lengkap)</label>
                                <textarea id="adm_edit_destination_purpose" name="destination_purpose" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">D. Durasi / Lama Waktu Perjalanan</label>
                                    <input type="text" id="adm_edit_duration_text" name="duration_text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Tanggal Pelaksanaan Kegiatan <span class="text-danger">*</span></label>
                                    <input type="date" id="adm_edit_date" name="activity_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E & F. Hasil, Kendala, Solusi & Saran -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-file-lines text-primary me-1"></i> E &amp; F. HASIL PERJALANAN DINAS &amp; SARAN
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">E. Uraian Hasil Perjalanan Dinas <span class="text-danger">*</span></label>
                                <textarea id="adm_edit_description" name="work_description" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kendala</label>
                                    <textarea id="adm_edit_obstacles" name="obstacles" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Solusi</label>
                                    <textarea id="adm_edit_solutions" name="solutions" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-secondary">F. Saran dan Rekomendasi / Tindak Lanjut</label>
                                <textarea id="adm_edit_recommendations" name="recommendations" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Pengeluaran Riil -->
                    <div class="card mb-3 border border-success">
                        <div class="card-header bg-success-subtle text-success-emphasis py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-receipt me-1"></i> DAFTAR PENGELUARAN RIIL</span>
                            <button type="button" class="btn btn-xs btn-success py-0.5 px-2" style="font-size: 0.72rem;" onclick="addExpenseRow('adm_edit_expense_list', 'adm_edit_expense_total', 'adm_edit_expense_terbilang')">
                                <i class="fa-solid fa-plus me-1"></i> Tambah Baris Biaya
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive mb-2">
                                <table class="table table-bordered table-sm align-middle mb-0">
                                    <thead class="table-light text-center small">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Perincian Biaya</th>
                                            <th style="width: 180px;">Jumlah (Rp)</th>
                                            <th style="width: 45px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="adm_edit_expense_list"></tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="2" class="text-center small">
                                                JUMLAH (<span id="adm_edit_expense_terbilang" class="fst-italic fw-semibold text-success">Nol Rupiah</span>)
                                            </th>
                                            <th class="text-end font-monospace text-dark" id="adm_edit_expense_total">Rp. 0,-</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Pejabat Pembuat Komitmen (Menyetujui)</label>
                                    <input type="text" id="adm_edit_ppk_name" name="ppk_name" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">NIP Pejabat Pembuat Komitmen</label>
                                    <input type="text" id="adm_edit_ppk_nip" name="ppk_nip" class="form-control form-control-sm font-monospace">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Penandatanganan & Opsional -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-signature text-primary me-1"></i> PENGESAHAN &amp; LAMPIRAN OPSIONAL
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kota Penandatanganan</label>
                                    <input type="text" id="adm_edit_signing_city" name="signing_city" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Tanggal Tanda Tangan</label>
                                    <input type="date" id="adm_edit_signing_date" name="signing_date" class="form-control">
                                </div>
                            </div>

                            <div id="adm_edit_current_sig_container" class="mb-3 d-none">
                                <div class="small fw-semibold text-secondary mb-1">Tanda Tangan Digital Petugas (Tersimpan):</div>
                                <div class="p-2 bg-light border rounded d-inline-block">
                                    <img id="adm_edit_current_sig_img" src="" alt="Tanda Tangan Petugas" style="max-height: 65px;">
                                </div>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="include_vehicle_statement" value="1" id="adm_edit_vehicle_stmt">
                                <label class="form-check-label fw-semibold text-dark small" for="adm_edit_vehicle_stmt">
                                    Sertakan Surat Pernyataan Tidak Menggunakan Kendaraan Dinas
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Foto Dokumentasi Tersimpan (Admin dapat meninjau atau menghapus foto tidak sesuai) -->
                    <div id="adm_edit_existing_photos_box" class="card mb-3 border d-none">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="fa-solid fa-camera text-primary me-1"></i> DOKUMENTASI FOTO TERSIMPAN
                        </div>
                        <div class="card-body p-3">
                            <div id="adm_edit_existing_photos_list" class="row g-2"></div>
                        </div>
                    </div>

                    <!-- Catatan Opsional Admin -->
                    <div>
                        <label class="form-label small fw-semibold text-secondary">Catatan Admin (Opsional)</label>
                        <textarea id="adm_edit_admin_notes" name="admin_notes" class="form-control form-control-sm" rows="2" placeholder="Catatan perbaikan oleh admin atau persetujuan (opsional)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top d-flex flex-wrap justify-content-between gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" name="after_action" value="save" class="btn btn-sp-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                        </button>
                        <button type="submit" name="after_action" value="approve" class="btn btn-success fw-semibold">
                            <i class="fa-solid fa-check-double me-1"></i> Simpan &amp; ACC Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ADMIN_BASIS_DEFAULTS = @json($basisSettings);

    function openReviewModal(rep) {
        document.getElementById('reviewReportForm').action = '/admin/reports/' + rep.id + '/review';
        document.getElementById('rev_number').innerText = rep.report_number || ('LAP-' + rep.id);
        document.getElementById('rev_officer').innerText = rep.user ? rep.user.name : '-';
        document.getElementById('rev_title').innerText = rep.activity_title;
        document.getElementById('rev_date').innerText = rep.activity_date ? rep.activity_date.substring(0, 10) : '-';
        document.getElementById('rev_location').innerText = rep.activity_location || (rep.assignment ? rep.assignment.location : '-');
        document.getElementById('rev_description').innerText = rep.work_description;
        document.getElementById('rev_admin_notes').value = rep.admin_notes || '';

        // B. Dasar Pelaksanaan laporan ini (diisi/disesuaikan oleh Admin)
        document.getElementById('rev_basis_dipa').value = rep.basis_dipa || (rep.assignment ? rep.assignment.basis_dipa : '') || ADMIN_BASIS_DEFAULTS.basis_dipa || '';
        document.getElementById('rev_st_number').value = rep.st_number || (rep.assignment ? rep.assignment.st_number : '') || ADMIN_BASIS_DEFAULTS.st_number || '';
        document.getElementById('rev_st_date').value = rep.st_date
            ? rep.st_date.substring(0, 10)
            : ((rep.assignment && rep.assignment.st_date) ? rep.assignment.st_date.substring(0, 10) : (ADMIN_BASIS_DEFAULTS.st_date || ''));
        if (rep.basis_st) {
            document.getElementById('rev_basis_st').value = rep.basis_st;
        } else {
            syncAdminBasisSt('rev');
        }

        // Select status
        const statusSelect = document.getElementById('rev_status_select');
        if (['approved', 'revision_needed', 'reviewing'].includes(rep.status)) {
            statusSelect.value = rep.status;
        } else {
            statusSelect.value = 'approved';
        }

        // Attachments (termasuk koordinat GPS & waktu real-time kamera)
        const container = document.getElementById('rev_attachments_container');
        container.innerHTML = '';
        if (rep.attachments && rep.attachments.length > 0) {
            rep.attachments.forEach(att => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4';

                const card = document.createElement('div');
                card.className = 'card h-100 border text-center p-2';

                const isImg = att.file_type && att.file_type.startsWith('image/');
                if (isImg) {
                    const a = document.createElement('a');
                    a.href = '/storage/' + att.file_path;
                    a.target = '_blank';
                    const img = document.createElement('img');
                    img.src = '/storage/' + att.file_path;
                    img.alt = att.file_name || 'Foto Dokumentasi';
                    img.className = 'img-fluid rounded mb-2';
                    img.style.maxHeight = '110px';
                    img.style.objectFit = 'cover';
                    a.appendChild(img);
                    card.appendChild(a);
                } else {
                    const pdfIcon = document.createElement('div');
                    pdfIcon.className = 'py-3';
                    pdfIcon.innerHTML = '<i class="fa-regular fa-file-pdf fa-3x text-danger"></i>';
                    card.appendChild(pdfIcon);
                }

                const nameEl = document.createElement('div');
                nameEl.className = 'small text-truncate fw-medium';
                nameEl.title = att.file_name || '';
                nameEl.textContent = att.file_name || '';
                card.appendChild(nameEl);

                const meta = document.createElement('div');
                meta.className = 'text-start mt-1 pt-1 border-top';
                meta.style.fontSize = '0.72rem';

                if (att.captured_at) {
                    const d = new Date(att.captured_at);
                    const timeTxt = isNaN(d.getTime())
                        ? att.captured_at
                        : new Intl.DateTimeFormat('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric',
                            hour: '2-digit', minute: '2-digit', second: '2-digit',
                            timeZoneName: 'short'
                        }).format(d);
                    const tEl = document.createElement('div');
                    tEl.className = 'fw-semibold text-dark';
                    tEl.textContent = timeTxt;
                    meta.appendChild(tEl);
                }

                if (att.location_address) {
                    const addrEl = document.createElement('div');
                    addrEl.className = 'text-secondary text-truncate';
                    addrEl.title = att.location_address;
                    addrEl.textContent = att.location_address;
                    meta.appendChild(addrEl);
                }

                if (att.latitude !== null && att.latitude !== undefined && att.longitude !== null && att.longitude !== undefined) {
                    const lat = Number(att.latitude).toFixed(6);
                    const lng = Number(att.longitude).toFixed(6);
                    const coordEl = document.createElement('div');
                    coordEl.className = 'font-monospace text-muted';
                    coordEl.textContent = `${lat}, ${lng}`;
                    meta.appendChild(coordEl);

                    const mapLink = document.createElement('a');
                    mapLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
                    mapLink.target = '_blank';
                    mapLink.className = 'badge bg-info-subtle text-info-emphasis border border-info-subtle text-decoration-none mt-1';
                    mapLink.innerHTML = '<i class="fa-solid fa-map-location-dot me-1"></i> Lihat Peta';
                    meta.appendChild(mapLink);
                }
                card.appendChild(meta);

                const openBtn = document.createElement('a');
                openBtn.href = '/storage/' + att.file_path;
                openBtn.target = '_blank';
                openBtn.className = 'btn btn-sm btn-link text-decoration-none mt-1';
                openBtn.innerHTML = '<i class="fa-solid fa-arrow-up-right-from-square"></i> Buka File';
                card.appendChild(openBtn);

                col.appendChild(card);
                container.appendChild(col);
            });
        } else {
            container.innerHTML = '<div class="col-12 text-muted small fst-italic">Tidak ada berkas / foto dokumentasi yang dilampirkan.</div>';
        }

        // Set action download / preview links
        document.getElementById('rev_preview_link').href = '/admin/reports/' + rep.id + '/preview';
        document.getElementById('rev_download_link').href = '/admin/reports/' + rep.id + '/download';

        const modal = new bootstrap.Modal(document.getElementById('reviewReportModal'));
        modal.show();
    }

    let currentInspectedReport = null;

    function openViewReportModal(rep) {
        currentInspectedReport = rep;
        populateReportDocumentPreview(rep, ADMIN_BASIS_DEFAULTS);

        document.getElementById('adm_view_preview_link').href = '/admin/reports/' + rep.id + '/preview';
        document.getElementById('adm_view_download_link').href = '/admin/reports/' + rep.id + '/download';

        const approveForm = document.getElementById('adm_view_approve_form');
        const alreadyBadge = document.getElementById('adm_view_already_acc_badge');
        if (rep.status === 'approved') {
            approveForm.classList.add('d-none');
            alreadyBadge.classList.remove('d-none');
        } else {
            approveForm.action = '/admin/reports/' + rep.id + '/approve';
            approveForm.classList.remove('d-none');
            alreadyBadge.classList.add('d-none');
        }

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewReportModal'));
        modal.show();
    }

    function switchAdminViewToEdit() {
        const viewEl = document.getElementById('viewReportModal');
        const viewInst = bootstrap.Modal.getInstance(viewEl);
        if (viewInst) viewInst.hide();
        if (currentInspectedReport) {
            setTimeout(() => openAdminEditModal(currentInspectedReport), 220);
        }
    }

    function openAdminEditModal(rep) {
        document.getElementById('adminEditReportForm').action = '/admin/reports/' + rep.id;

        document.getElementById('adm_edit_officer_name').value = rep.resolved_officer_name || rep.officer_name || (rep.user ? rep.user.name : '') || '';
        document.getElementById('adm_edit_id_type').value = rep.id_type || 'NIK';
        document.getElementById('adm_edit_officer_nip').value = rep.resolved_officer_nik || rep.officer_nip || (rep.user ? rep.user.nik : '') || '';
        document.getElementById('adm_edit_officer_rank').value = rep.officer_rank || (rep.user ? rep.user.rank : '') || '-';
        document.getElementById('adm_edit_officer_position').value = rep.officer_position || (rep.user ? rep.user.position : '') || 'Mitra BPS Kabupaten Kolaka Utara';

        document.getElementById('adm_edit_basis_dipa').value = rep.basis_dipa || (rep.assignment ? rep.assignment.basis_dipa : '') || ADMIN_BASIS_DEFAULTS.basis_dipa || '';
        document.getElementById('adm_edit_st_number').value = rep.st_number || (rep.assignment ? rep.assignment.st_number : '') || ADMIN_BASIS_DEFAULTS.st_number || '';
        document.getElementById('adm_edit_st_date').value = rep.st_date
            ? rep.st_date.substring(0, 10)
            : ((rep.assignment && rep.assignment.st_date) ? rep.assignment.st_date.substring(0, 10) : (ADMIN_BASIS_DEFAULTS.st_date || ''));
        if (rep.basis_st) {
            document.getElementById('adm_edit_basis_st').value = rep.basis_st;
        } else {
            syncAdminBasisSt('adm_edit');
        }

        document.getElementById('adm_edit_title').value = rep.activity_title || '';
        document.getElementById('adm_edit_assignment').value = rep.assignment_id || '';
        const actLoc = rep.activity_location || (rep.assignment ? rep.assignment.location : '') || '';
        document.getElementById('adm_edit_activity_location').value = actLoc;
        document.getElementById('adm_edit_destination_purpose').value = rep.destination_purpose || '';
        document.getElementById('adm_edit_duration_text').value = rep.duration_text || 'Selama 1 (satu) Hari';
        document.getElementById('adm_edit_date').value = rep.activity_date ? rep.activity_date.substring(0, 10) : '';

        document.getElementById('adm_edit_description').value = rep.work_description || '';
        document.getElementById('adm_edit_obstacles').value = rep.obstacles || '';
        document.getElementById('adm_edit_solutions').value = rep.solutions || '';
        document.getElementById('adm_edit_recommendations').value = rep.recommendations || '-';

        const tbody = document.getElementById('adm_edit_expense_list');
        tbody.innerHTML = '';
        const items = (Array.isArray(rep.resolved_expense_items) && rep.resolved_expense_items.length)
            ? rep.resolved_expense_items
            : (Array.isArray(rep.expense_items) && rep.expense_items.length
                ? rep.expense_items
                : [{ description: buildDefaultExpenseDesc(actLoc), amount: 50000 }]);
        items.forEach(item => {
            addExpenseRow('adm_edit_expense_list', 'adm_edit_expense_total', 'adm_edit_expense_terbilang', item.description || '', Number(item.amount || 0));
        });

        document.getElementById('adm_edit_ppk_name').value = rep.ppk_name || 'Hidayatullah, SE';
        document.getElementById('adm_edit_ppk_nip').value = rep.ppk_nip || '198510292011011009';
        document.getElementById('adm_edit_signing_city').value = rep.signing_city || 'Lasusua';
        document.getElementById('adm_edit_signing_date').value = rep.signing_date ? rep.signing_date.substring(0, 10) : (rep.activity_date ? rep.activity_date.substring(0, 10) : '');
        document.getElementById('adm_edit_vehicle_stmt').checked = (rep.include_vehicle_statement === true || rep.include_vehicle_statement === 1);
        document.getElementById('adm_edit_admin_notes').value = rep.admin_notes || '';

        const sigBox = document.getElementById('adm_edit_current_sig_container');
        const sigImg = document.getElementById('adm_edit_current_sig_img');
        if (rep.signature_path) {
            sigBox.classList.remove('d-none');
            sigImg.src = '/storage/' + rep.signature_path;
        } else {
            sigBox.classList.add('d-none');
        }

        const existBox = document.getElementById('adm_edit_existing_photos_box');
        const existList = document.getElementById('adm_edit_existing_photos_list');
        existList.innerHTML = '';
        if (Array.isArray(rep.attachments) && rep.attachments.length > 0) {
            existBox.classList.remove('d-none');
            rep.attachments.forEach(att => {
                existList.appendChild(buildExistingEditAttachmentCard(att));
            });
        } else {
            existBox.classList.add('d-none');
        }

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('adminEditReportModal'));
        modal.show();
    }

    @if(isset($autoOpenReport) && $autoOpenReport)
        document.addEventListener('DOMContentLoaded', function() {
            const autoRep = @json($autoOpenReport);
            @if(($autoOpenMode ?? 'view') === 'edit')
                openAdminEditModal(autoRep);
            @else
                openViewReportModal(autoRep);
            @endif
        });
    @endif
</script>
@include('reports._shared_js')
@endpush

