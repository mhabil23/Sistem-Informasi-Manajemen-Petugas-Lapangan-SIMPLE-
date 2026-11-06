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
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        @if($submittedCount > 0)
            <span class="badge bg-warning text-dark px-2.5 py-1.5 fs-7 shadow-sm">
                <i class="fa-solid fa-bell me-1"></i> {{ $submittedCount }} Laporan Perlu Diverifikasi
            </span>
        @endif
    </div>
</div>

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
                        <th style="width: 15%;">No. Laporan</th>
                        <th style="width: 17%;">Petugas Lapangan</th>
                        <th style="width: 22%;">Kegiatan &amp; Tanggal</th>
                        <th style="width: 22%;">Uraian Singkat</th>
                        <th class="text-center" style="width: 7%;">Lampiran</th>
                        <th class="text-center" style="width: 9%;">Status</th>
                        <th class="text-end" style="width: 8%;">Aksi</th>
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
                                <div class="sp-action-btn-group">
                                    <!-- Tombol Tinjau / Verifikasi -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-primary" 
                                            title="Periksa & Verifikasi Laporan" 
                                            data-bs-toggle="tooltip"
                                            onclick="openReviewModal({{ json_encode($rep) }})">
                                        <i class="fa-solid fa-file-circle-check"></i>
                                    </button>

                                    <!-- Pratinjau / Cetak PDF -->
                                    <a href="{{ route('admin.reports.preview', $rep) }}" 
                                       target="_blank"
                                       class="btn btn-light border sp-action-btn text-info" 
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
                            <i class="fa-solid fa-paperclip text-primary me-1"></i> BUKTI DOKUMENTASI & LAMPIRAN:
                        </label>
                        <div id="rev_attachments_container" class="row g-2">
                            <!-- Populated by JS -->
                        </div>
                    </div>

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
@endsection

@push('scripts')
<script>
    function openReviewModal(rep) {
        document.getElementById('reviewReportForm').action = '/admin/reports/' + rep.id + '/review';
        document.getElementById('rev_number').innerText = rep.report_number || ('LAP-' + rep.id);
        document.getElementById('rev_officer').innerText = rep.user ? rep.user.name : '-';
        document.getElementById('rev_title').innerText = rep.activity_title;
        document.getElementById('rev_date').innerText = rep.activity_date ? rep.activity_date.substring(0, 10) : '-';
        document.getElementById('rev_location').innerText = rep.activity_location || (rep.assignment ? rep.assignment.location : '-');
        document.getElementById('rev_description').innerText = rep.work_description;
        document.getElementById('rev_admin_notes').value = rep.admin_notes || '';

        // Select status
        const statusSelect = document.getElementById('rev_status_select');
        if (['approved', 'revision_needed', 'reviewing'].includes(rep.status)) {
            statusSelect.value = rep.status;
        } else {
            statusSelect.value = 'approved';
        }

        // Attachments
        const container = document.getElementById('rev_attachments_container');
        if (rep.attachments && rep.attachments.length > 0) {
            let html = '';
            rep.attachments.forEach(att => {
                const isImg = att.file_type && att.file_type.startsWith('image/');
                html += `<div class="col-6 col-md-4">
                            <div class="card h-100 border text-center p-2">
                                ${isImg 
                                    ? `<a href="/storage/${att.file_path}" target="_blank">
                                         <img src="/storage/${att.file_path}" class="img-fluid rounded mb-2" style="max-height: 110px; object-fit: cover;" alt="${att.file_name}">
                                       </a>` 
                                    : `<div class="py-3"><i class="fa-regular fa-file-pdf fa-3x text-danger"></i></div>`
                                }
                                <div class="small text-truncate" title="${att.file_name}">${att.file_name}</div>
                                <a href="/storage/${att.file_path}" target="_blank" class="btn btn-sm btn-link text-decoration-none mt-1">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka File
                                </a>
                            </div>
                         </div>`;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="col-12 text-muted small fst-italic">Tidak ada berkas / foto dokumentasi yang dilampirkan.</div>';
        }

        // Set action download / preview links
        document.getElementById('rev_preview_link').href = '/admin/reports/' + rep.id + '/preview';
        document.getElementById('rev_download_link').href = '/admin/reports/' + rep.id + '/download';

        const modal = new bootstrap.Modal(document.getElementById('reviewReportModal'));
        modal.show();
    }
</script>
@endpush

