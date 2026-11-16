@extends('layouts.app')

@section('title', 'Perjadin Saya')
@section('page_title', 'Perjadin Saya')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengisian Laporan Perjadin</li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .bento-table-container-fixed {
        width: 100%;
        overflow: hidden;
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
        padding: 0.75rem 0.65rem;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .bento-table tbody td {
        padding: 0.75rem 0.65rem;
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
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }
    .btn-bps-orange:hover, .btn-bps-orange:focus {
        background-color: #d95d13;
        border-color: #d95d13;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);
    }
    .btn-bps-filter {
        background-color: #F0F6FF;
        border: 1px solid #BAE6FD;
        color: #005BAC;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }
    .btn-bps-filter:hover, .btn-bps-filter:focus {
        background-color: #005BAC;
        border-color: #005BAC;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 91, 172, 0.25);
    }
    /* Compact Bento Action Buttons */
    .bento-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        transition: all 0.15s ease;
        font-size: 0.85rem;
        padding: 0;
        flex-shrink: 0;
        text-decoration: none;
    }
    .bento-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    .bento-action-btn.btn-preview { color: #0284C7; border-color: #BAE6FD; background: #F0F9FF; }
    .bento-action-btn.btn-preview:hover { background: #0284C7; color: #FFFFFF; }
    .bento-action-btn.btn-print { color: #4F46E5; border-color: #C7D2FE; background: #EEF2FF; }
    .bento-action-btn.btn-print:hover { background: #4F46E5; color: #FFFFFF; }
    .bento-action-btn.btn-pdf { color: #DC2626; border-color: #FECACA; background: #FEF2F2; }
    .bento-action-btn.btn-pdf:hover { background: #DC2626; color: #FFFFFF; }
    .bento-action-btn.btn-edit { color: #D97706; border-color: #FDE68A; background: #FFFBEB; }
    .bento-action-btn.btn-edit:hover { background: #D97706; color: #FFFFFF; }
    .bento-action-btn.btn-delete { color: #DC2626; border-color: #FECACA; background: #FEF2F2; }
    .bento-action-btn.btn-delete:hover { background: #DC2626; color: #FFFFFF; }
    .bento-action-btn.btn-locked { color: #94A3B8; border-color: #E2E8F0; background: #F8FAFC; cursor: not-allowed; }
</style>
@endpush

@section('content')
<!-- Banner Pemberitahuan Pengiriman ke Admin (Menunggu ACC sebelum Unduh PDF) -->
@if(!auth()->user()->isPegawai() && session('pending_approval_report_id'))
    <div class="alert alert-info border-info d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 shadow-sm rounded-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-hourglass-split display-6 me-3 text-info"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Laporan Tersimpan &amp; Pemberitahuan Terkirim ke Administrator</h6>
                <div class="small">Laporan perjalanan dinas Anda sedang <strong>menunggu pemeriksaan / ACC Administrator</strong>. Tombol unduh &amp; cetak PDF akan otomatis aktif setelah disetujui.</div>
            </div>
        </div>
        <div>
            <span class="badge bg-info text-dark px-3 py-2 fw-semibold rounded-pill">
                <i class="bi bi-lock-fill me-1"></i> Menunggu ACC Admin
            </span>
        </div>
    </div>
@endif

<!-- Alert Perlu Revisi (Jika ada) -->
@if($revisionCount > 0)
    <div class="alert alert-warning border-warning d-flex align-items-center mb-4 shadow-sm rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill display-6 me-3 text-warning"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Perhatian: Ada {{ $revisionCount }} Laporan Memerlukan Perbaikan / Revisi!</h6>
            <div class="small">Administrator telah mengembalikan laporan dengan catatan evaluasi. Klik tombol perbaiki (ikon pensil) pada tabel di bawah untuk membaca catatan dan mengirimkan revisi.</div>
        </div>
    </div>
@endif



<!-- Search & Filter Card -->
<div class="bento-card mb-4 p-3">
    <form method="GET" action="{{ route('petugas.reports.index') }}" class="row g-2 align-items-center">
        <div class="col-md-7 col-lg-7">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       name="search" 
                       class="form-control border-start-0 ps-1" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nomor laporan, judul, uraian pekerjaan...">
            </div>
        </div>

        <div class="col-md-3 col-lg-3">
            <select name="status" class="form-select">
                <option value="all">-- Semua Status --</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan</option>
                <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Dalam Pemeriksaan</option>
                <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
            </select>
        </div>

        <div class="col-md-2 col-lg-2 d-flex gap-2">
            <button type="submit" class="btn btn-bps-orange rounded-pill w-100 fw-semibold d-flex align-items-center justify-content-center gap-1.5 shadow-sm">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('petugas.reports.index') }}" class="btn btn-outline-orange rounded-pill px-2.5 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bento-card mb-4 overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom gap-2 bg-white">
        <div>
            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.98rem;">
                Daftar Laporan Aktivitas Saya
            </h6>
            <span class="text-muted" style="font-size: 0.72rem;">Total {{ $reports->total() }} laporan kegiatan tersimpan</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 font-monospace" style="font-size: 0.75rem;">
                Halaman {{ $reports->currentPage() }} dari {{ $reports->lastPage() ?: 1 }}
            </span>
            <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#createReportModal" style="font-size: 0.82rem;">
                <i class="bi bi-plus-circle me-1"></i> Isi Laporan Perjadin Baru
            </button>
        </div>
    </div>

    <!-- MODE DESKTOP & TABLET: Tabel Anti-Geser -->
    <div class="d-none d-md-block bento-table-container-fixed">
        <table class="bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 15%;">NOMOR LAPORAN</th>
                    <th style="width: 25%;">JUDUL &amp; KEGIATAN</th>
                    <th style="width: 12%;">TANGGAL</th>
                    <th style="width: 23%;">URAIAN PEKERJAAN</th>
                    <th class="text-center" style="width: 12%;">STATUS</th>
                    <th class="text-end pe-3" style="width: 13%;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $rep)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2.5 py-1.5 rounded-2" style="font-size: 0.78rem; font-weight: 600;">
                                {{ $rep->report_number ?? 'LAP-'.$rep->id }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.84rem;">{{ $rep->activity_title }}</div>
                            <small class="text-muted text-truncate d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                <i class="bi bi-briefcase text-secondary"></i> {{ $rep->assignment ? $rep->assignment->title : 'Kegiatan Mandiri' }}
                            </small>
                        </td>
                        <td>
                            <div class="small fw-semibold text-secondary d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                <i class="bi bi-calendar3 text-primary"></i> {{ $rep->activity_date->format('d/m/Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="small text-secondary text-truncate" style="font-size: 0.76rem;" title="{{ $rep->work_description }}">
                                {{ $rep->work_description }}
                            </div>
                            @if($rep->admin_notes && $rep->status === 'revision_needed')
                                <div class="small text-danger fw-semibold mt-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-exclamation-triangle-fill me-0.5"></i> Evaluasi: {{ \Illuminate\Support\Str::limit($rep->admin_notes, 35) }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($rep->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                                    <i class="bi bi-check2-circle me-1"></i> Disetujui
                                </span>
                            @elseif($rep->status === 'revision_needed')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Revisi
                                </span>
                            @elseif($rep->status === 'reviewing' || $rep->status === 'submitted')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                                    <i class="bi bi-send-fill me-1"></i> Diajukan
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                                    <i class="bi bi-file-earmark me-1"></i> Draft
                                </span>
                            @endif
                            @if(! $rep->canBeDownloadedByOfficer())
                                <div class="text-muted mt-1" style="font-size: 0.65rem;">
                                    <i class="bi bi-lock me-0.5"></i> {{ $rep->status === 'draft' ? 'Draft' : 'Tunggu ACC' }}
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-inline-flex gap-1 align-items-center">
                                <!-- Detail Modal Preview -->
                                <button type="button" 
                                        class="bento-action-btn btn-preview" 
                                        title="Lihat Format Dokumen" 
                                        data-bs-toggle="tooltip"
                                        onclick="openDetailModal({{ json_encode($rep) }})">
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if($rep->canBeDownloadedByOfficer(auth()->user()))
                                    <!-- Preview & Cetak PDF -->
                                    <a href="{{ route('petugas.reports.preview', $rep) }}" 
                                       target="_blank"
                                       class="bento-action-btn btn-print" 
                                       title="Cetak / Preview PDF" 
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-printer"></i>
                                    </a>

                                    <!-- Unduh PDF Resmi -->
                                    <a href="{{ route('petugas.reports.download', $rep) }}" 
                                       class="bento-action-btn btn-pdf" 
                                       title="Unduh PDF Resmi" 
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                @else
                                    <button type="button"
                                            class="bento-action-btn btn-locked"
                                            disabled
                                            title="Unduh PDF menunggu ACC Administrator"
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-lock"></i>
                                    </button>
                                @endif

                                <!-- Edit / Perbaiki jika Draft atau Perlu Revisi -->
                                @if(in_array($rep->status, ['draft', 'revision_needed']))
                                    <button type="button" 
                                            class="bento-action-btn btn-edit" 
                                            title="{{ $rep->status === 'revision_needed' ? 'Perbaiki Laporan' : 'Edit Draft' }}" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditModal({{ json_encode($rep) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                @endif

                                <!-- Hapus jika Draft -->
                                @if($rep->status === 'draft')
                                    <form method="POST" action="{{ route('petugas.reports.destroy', $rep) }}" class="d-inline" onsubmit="return confirm('Hapus draft laporan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bento-action-btn btn-delete" 
                                                title="Hapus Draft" 
                                                data-bs-toggle="tooltip">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 64px; height: 64px; background: rgba(0, 91, 172, 0.08); color: #005BAC;">
                                    <i class="bi bi-clipboard2-plus fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Belum Ada Laporan Perjalanan Dinas</h6>
                                <p class="text-muted small mb-3" style="max-width: 440px; margin: 0 auto; line-height: 1.5;">
                                    Anda belum memiliki riwayat pengisian laporan kegiatan perjadin. Mulai buat laporan aktivitas lapangan Anda dengan mudah dan cepat.
                                </p>
                                <button type="button" class="btn btn-bps-orange rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createReportModal">
                                    <i class="bi bi-plus-circle me-1.5"></i> Buat Laporan Pertama
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODE MOBILE / HP: Daftar Kartu Laporan Responsif -->
    <div class="d-block d-md-none p-3">
        @forelse($reports as $rep)
            <div class="bento-card p-3 mb-3 border">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-light text-dark font-monospace border mb-1" style="font-size: 0.72rem;">
                            {{ $rep->report_number ?? 'LAP-'.$rep->id }}
                        </span>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">{{ $rep->activity_title }}</h6>
                        <small class="text-primary fw-medium" style="font-size: 0.75rem;">
                            {{ $rep->assignment ? $rep->assignment->title : 'Kegiatan Mandiri' }}
                        </small>
                    </div>
                    @if($rep->status === 'approved')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.68rem;">
                            <i class="bi bi-check2-circle me-1"></i> Disetujui
                        </span>
                    @elseif($rep->status === 'revision_needed')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill" style="font-size: 0.68rem;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Revisi
                        </span>
                    @elseif($rep->status === 'reviewing' || $rep->status === 'submitted')
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size: 0.68rem;">
                            <i class="bi bi-send-fill me-1"></i> Diajukan
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill" style="font-size: 0.68rem;">
                            <i class="bi bi-file-earmark me-1"></i> Draft
                        </span>
                    @endif
                </div>

                <div class="small text-secondary mb-2" style="font-size: 0.75rem;">
                    <div class="mb-1">
                        <i class="bi bi-calendar3 text-primary me-1"></i>
                        <span class="fw-medium text-dark">{{ $rep->activity_date->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="mb-1 text-truncate">
                        <i class="bi bi-card-text text-muted me-1"></i>
                        <span>{{ Str::limit($rep->work_description, 80) }}</span>
                    </div>
                    @if($rep->attachments->isNotEmpty())
                        <div class="mb-1">
                            <i class="bi bi-paperclip text-primary me-1"></i>
                            <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">
                                {{ $rep->attachments->count() }} Berkas Lampiran
                            </span>
                        </div>
                    @endif
                </div>

                @if($rep->admin_notes && $rep->status === 'revision_needed')
                    <div class="alert alert-warning py-1.5 px-2 mb-2 small border-warning rounded-3" style="font-size: 0.75rem;">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                        <strong>Catatan Admin:</strong> {{ $rep->admin_notes }}
                    </div>
                @endif

                <div class="d-flex justify-content-end align-items-center gap-1 pt-2 border-top">
                    <button type="button" 
                            class="bento-action-btn btn-preview" 
                            title="Detail" 
                            onclick="openDetailModal({{ json_encode($rep) }})">
                        <i class="bi bi-eye"></i>
                    </button>
                    @if($rep->canBeDownloadedByOfficer(auth()->user()))
                        <a href="{{ route('petugas.reports.preview', $rep) }}" 
                           target="_blank" 
                           class="bento-action-btn btn-print" 
                           title="Cetak">
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('petugas.reports.download', $rep) }}" 
                           class="bento-action-btn btn-pdf" 
                           title="Unduh PDF">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                    @else
                        <button type="button" class="bento-action-btn btn-locked" disabled title="Unduh PDF menunggu ACC Administrator">
                            <i class="bi bi-lock"></i>
                        </button>
                    @endif
                    @if(in_array($rep->status, ['draft', 'revision_needed']))
                        <button type="button" 
                                class="bento-action-btn btn-edit" 
                                title="{{ $rep->status === 'revision_needed' ? 'Perbaiki' : 'Edit' }}" 
                                onclick="openEditModal({{ json_encode($rep) }})">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    @endif
                    @if($rep->status === 'draft')
                        <form method="POST" action="{{ route('petugas.reports.destroy', $rep) }}" class="d-inline" onsubmit="return confirm('Hapus draft laporan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bento-action-btn btn-delete" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bento-card p-4 text-center border">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 56px; height: 56px; background: rgba(0, 91, 172, 0.08); color: #005BAC;">
                    <i class="bi bi-clipboard2-plus fs-2"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Laporan</h6>
                <p class="text-muted small mb-3">Anda belum memiliki riwayat pengisian laporan kegiatan perjadin.</p>
                <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-2 w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#createReportModal">
                    <i class="bi bi-plus-circle me-1"></i> Isi Laporan Sekarang
                </button>
            </div>
        @endforelse
    </div>

    @if($reports->hasPages())
        <div class="bg-white border-top p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted" style="font-size: 0.78rem;">
                Menampilkan {{ $reports->firstItem() }} sampai {{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
            </div>
            <div>
                {{ $reports->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL BUAT LAPORAN BARU (FORMAT RESMI SPD BPS)
     ============================================================== -->
<div class="modal fade" id="createReportModal" tabindex="-1" aria-labelledby="createReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="createReportModalLabel">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i> Pengisian Laporan Perjalanan Dinas Lapangan
                    </h5>
                    <div class="small text-muted">Isi detail kegiatan, tanda tangan digital (1x), dan foto kamera lapangan. Identitas, Dasar Pelaksanaan &amp; Pengeluaran Riil otomatis dilengkapi Admin.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createReportForm" method="POST" action="{{ route('petugas.reports.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-primary border-opacity-25 py-2 px-3 mb-3 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill text-primary me-2 fs-5"></i>
                        <div class="small text-dark">
                            <strong>Formulir Ringkas Petugas:</strong> Identitas Anda (Nama/NIK), <em>B. Dasar Pelaksanaan</em>, dan <em>Daftar Pengeluaran Riil</em> otomatis terisi dari akun Anda &amp; diisi/disesuaikan oleh Administrator. Anda cukup mengisi laporan kegiatan, <strong>1x tanda tangan digital</strong>, dan foto kamera lapangan.
                        </div>
                    </div>

                    <!-- Informasi Kegiatan, Lokasi & Tujuan Perjalanan Dinas -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="bi bi-geo-alt-fill text-primary me-1"></i> KEGIATAN, LOKASI &amp; TUJUAN PERJALANAN DINAS
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small fw-semibold text-secondary">Judul Kegiatan / Agenda <span class="text-danger">*</span></label>
                                    <input type="text" name="activity_title" class="form-control" placeholder="Contoh: Pertemuan Tim Sensus Ekonomi {{ date('Y') }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Penugasan Terkait</label>
                                    <select name="assignment_id" id="create_assignment_id" class="form-select" onchange="autoFillLocation(this, 'create_activity_location')">
                                        <option value="" data-location="">-- Kegiatan Mandiri / Umum --</option>
                                        @foreach($myAssignments as $asgn)
                                            <option value="{{ $asgn->id }}"
                                                data-location="{{ $asgn->location }}">{{ $asgn->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-secondary">
                                        <i class="bi bi-geo-alt-fill text-primary me-1"></i> Lokasi / Wilayah Tugas / Asal Keberangkatan
                                    </label>
                                    <input type="text" name="activity_location" id="create_activity_location" class="form-control" placeholder="Contoh: Kecamatan Batu Putih">
                                    <div class="form-text small">Digunakan pada tujuan perjalanan dinas dan informasi wilayah pada halaman dokumentasi.</div>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-semibold text-secondary">Tujuan Perjalanan Dinas (Opsional / Ringkasan)</label>
                                <textarea name="destination_purpose" class="form-control" rows="2" placeholder="Contoh: Perjalanan Dinas Biasa dalam kota sampai dengan 8 jam dalam rangka melaksanakan pendataan di Kecamatan Batu Putih."></textarea>
                                <div class="form-text small">Durasi otomatis 1 (satu) hari dan tanggal pelaksanaan diisi/disesuaikan oleh Administrator.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Perjalanan Dinas, Kendala, Solusi & Saran -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="bi bi-card-checklist text-primary me-1"></i> HASIL PERJALANAN DINAS, KENDALA, SOLUSI &amp; SARAN
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Uraian Hasil Perjalanan Dinas <span class="text-danger">*</span></label>
                                <textarea name="work_description" class="form-control" rows="4" placeholder="Menuju lokasi kegiatan..., melakukan koordinasi..., melaksanakan pendataan..., submit data..." required></textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kendala (Opsional)</label>
                                    <textarea name="obstacles" class="form-control" rows="3" placeholder="- Masih ada petugas yang belum...&#10;- Beberapa desa belum memiliki jaringan yang baik."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Solusi (Opsional)</label>
                                    <textarea name="solutions" class="form-control" rows="3" placeholder="- Menyarankan petugas melakukan submit data...&#10;- Melakukan log in terlebih dahulu di daerah dengan jaringan bagus."></textarea>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-secondary">Saran dan Rekomendasi / Tindak Lanjut</label>
                                <textarea name="recommendations" class="form-control" rows="2" placeholder="Ketik '-' jika tidak ada">-</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tanda Tangan & Penandatanganan -->
                    <div class="card mb-3 border border-primary">
                        <div class="card-header bg-primary text-white py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-pen-fill me-1"></i> TANDA TANGAN DIGITAL (1x INPUT UNTUK KEDUA DOKUMEN)</span>
                            <span class="badge bg-light text-primary">Muncul di Perjadin &amp; Pengeluaran Riil</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kota Tempat Penandatanganan</label>
                                    <input type="text" name="signing_city" class="form-control" value="Lasusua" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Tanggal Penandatanganan</label>
                                    <input type="text" class="form-control bg-light text-secondary" value="{{ now(\App\Models\OfficerReport::LOCAL_TIMEZONE)->translatedFormat('d F Y') }} (Otomatis tanggal input)" readonly>
                                </div>
                            </div>

                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="bi bi-pen-fill text-primary me-1"></i> Input Tanda Tangan Pelaksana (1 Kali Saja):
                            </label>
                            
                            <!-- Tab Pilihan Tanda Tangan -->
                            <ul class="nav nav-pills nav-fill mb-2" id="createSigTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 px-3 small" id="create-draw-tab" data-bs-toggle="pill" data-bs-target="#create-draw-pane" type="button" role="tab">
                                        <i class="bi bi-pen-fill me-1"></i> Gores Tanda Tangan (Layar HP / Mouse)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3 small" id="create-upload-tab" data-bs-toggle="pill" data-bs-target="#create-upload-pane" type="button" role="tab">
                                        <i class="bi bi-upload me-1"></i> Unggah File Gambar Tanda Tangan
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="createSigTabContent">
                                <!-- Gores Langsung -->
                                <div class="tab-pane fade show active" id="create-draw-pane" role="tabpanel">
                                    <div class="p-2 bg-light border rounded text-center">
                                        <canvas id="create_sig_canvas" class="border bg-white rounded shadow-sm" width="500" height="160" style="touch-action: none; cursor: crosshair; max-width: 100%; display: inline-block;"></canvas>
                                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                            <small class="text-muted"><i class="bi bi-cursor-fill me-1"></i> Goreskan tanda tangan di kotak atas — otomatis tampil di 2 dokumen sekaligus</small>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCreateCanvas()">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Hapus / Ulangi
                                            </button>
                                        </div>
                                        <input type="hidden" name="signature_data" id="create_signature_data">
                                    </div>
                                </div>
                                <!-- Upload File -->
                                <div class="tab-pane fade" id="create-upload-pane" role="tabpanel">
                                    <div class="p-3 bg-light border rounded">
                                        <input type="file" name="signature_file" class="form-control" accept="image/png,image/jpeg">
                                        <div class="form-text small mt-1">Pilih file tanda tangan (format PNG/JPG latar transparan atau putih, maks. 3MB).</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Foto Dokumentasi Langsung Kamera (Lokasi & Waktu Real-Time) -->
                    @include('petugas.reports._camera_capture', ['prefix' => 'create', 'showExisting' => false])

                    <!-- Opsi Tambahan Surat Pernyataan Tidak Menggunakan Kendaraan Dinas -->
                    <div class="card mb-3 border">
                        <div class="card-body p-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="include_vehicle_statement" value="1" id="create_vehicle_stmt">
                                <label class="form-check-label fw-semibold text-dark small" for="create_vehicle_stmt">
                                    <i class="bi bi-file-earmark-text text-secondary me-1"></i> Lampirkan juga Surat Pernyataan Tidak Menggunakan Kendaraan Dinas (Opsional)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi Tindakan Simpan -->
                    <div class="p-3 bg-light rounded border">
                        <label class="form-label small fw-semibold text-secondary mb-2">PILIH TINDAKAN PENYIMPANAN:</label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action_type" id="actionSubmit" value="submitted" checked>
                                <label class="form-check-label fw-bold text-primary" for="actionSubmit">
                                    <i class="bi bi-send-fill me-1"></i> Ajukan ke Admin (Menunggu ACC untuk Unduh PDF)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action_type" id="actionDraft" value="draft">
                                <label class="form-check-label text-muted" for="actionDraft">
                                    <i class="bi bi-bookmark me-1"></i> Simpan Sebagai Draft Sementara
                                </label>
                            </div>
                        </div>
                        <div class="small text-muted mt-2">
                            <i class="bi bi-info-circle-fill text-info me-1"></i> Administrator akan otomatis menerima informasi laporan Anda. File PDF resmi baru dapat diunduh setelah disetujui (ACC) Administrator.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-send-fill me-1"></i> Simpan &amp; Kirim Pemberitahuan ke Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT / PERBAIKI LAPORAN (FORMAT RESMI SPD BPS)
     ============================================================== -->
<div class="modal fade" id="editReportModal" tabindex="-1" aria-labelledby="editReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="editReportModalLabel">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Perbaiki & Lengkapi Laporan Perjalanan Dinas
                    </h5>
                    <div class="small text-muted">Sesuaikan data laporan perjalanan dinas resmi BPS Kabupaten Kolaka Utara</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReportForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Catatan Evaluasi Admin (jika status revision_needed) -->
                    <div id="edit_admin_notes_box" class="alert alert-danger border-danger p-3 mb-3 d-none">
                        <div class="fw-bold small text-danger mb-1">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> CATATAN REVISI DARI ADMINISTRATOR:
                        </div>
                        <div id="edit_admin_notes_text" class="small text-dark"></div>
                    </div>

                    <!-- Informasi Kegiatan, Lokasi & Tujuan Perjalanan Dinas -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="bi bi-geo-alt-fill text-primary me-1"></i> KEGIATAN, LOKASI &amp; TUJUAN PERJALANAN DINAS
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small fw-semibold text-secondary">Judul Kegiatan / Agenda <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_title" name="activity_title" class="form-control" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Penugasan Terkait</label>
                                    <select id="edit_assignment" name="assignment_id" class="form-select" onchange="autoFillLocation(this, 'edit_activity_location')">
                                        <option value="" data-location="">-- Kegiatan Mandiri / Umum --</option>
                                        @foreach($myAssignments as $asgn)
                                            <option value="{{ $asgn->id }}"
                                                data-location="{{ $asgn->location }}">{{ $asgn->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-secondary">
                                        <i class="bi bi-geo-alt-fill text-primary me-1"></i> Lokasi / Tempat Pelaksanaan &amp; Asal Keberangkatan
                                    </label>
                                    <input type="text" id="edit_activity_location" name="activity_location" class="form-control" placeholder="Contoh: Kecamatan Batu Putih">
                                    <div class="form-text small">Digunakan pada tujuan perjalanan dinas dan informasi wilayah pada halaman dokumentasi.</div>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-semibold text-secondary">Tujuan Perjalanan Dinas (Opsional / Ringkasan)</label>
                                <textarea id="edit_destination_purpose" name="destination_purpose" class="form-control" rows="2"></textarea>
                                <div class="form-text small">Durasi otomatis 1 (satu) hari dan tanggal pelaksanaan diisi/disesuaikan oleh Administrator.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Perjalanan Dinas, Kendala, Solusi & Saran -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="bi bi-card-checklist text-primary me-1"></i> HASIL PERJALANAN DINAS, KENDALA, SOLUSI &amp; SARAN
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Uraian Hasil Perjalanan Dinas <span class="text-danger">*</span></label>
                                <textarea id="edit_description" name="work_description" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kendala (Opsional)</label>
                                    <textarea id="edit_obstacles" name="obstacles" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Solusi (Opsional)</label>
                                    <textarea id="edit_solutions" name="solutions" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-semibold text-secondary">Saran dan Rekomendasi / Tindak Lanjut</label>
                                <textarea id="edit_recommendations" name="recommendations" class="form-control" rows="2">-</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tanda Tangan & Penandatanganan -->
                    <div class="card mb-3 border border-warning">
                        <div class="card-header bg-warning text-dark py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-pen-fill me-1"></i> TANDA TANGAN DIGITAL (1x INPUT UNTUK KEDUA DOKUMEN)</span>
                            <span class="badge bg-dark text-white">Muncul di Perjadin &amp; Pengeluaran Riil</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Kota Tempat Penandatanganan</label>
                                    <input type="text" id="edit_signing_city" name="signing_city" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Tanggal Penandatanganan</label>
                                    <input type="text" id="edit_signing_date_display" class="form-control bg-light text-secondary" readonly>
                                </div>
                            </div>

                            <!-- Preview Tanda Tangan Saat Ini jika ada -->
                            <div id="edit_current_sig_container" class="mb-3 d-none">
                                <label class="form-label small fw-semibold text-muted">Tanda Tangan Saat Ini:</label>
                                <div class="p-2 border rounded bg-light text-center d-inline-block">
                                    <img id="edit_current_sig_img" src="" style="max-height: 60px; max-width: 160px;" alt="Tanda Tangan">
                                </div>
                                <div class="small text-muted mt-1 fst-italic">Abaikan goresan di bawah jika tidak ingin mengganti tanda tangan ini.</div>
                            </div>

                            <label class="form-label small fw-semibold text-dark mb-1">
                                <i class="bi bi-pen-fill text-primary me-1"></i> Perbarui Tanda Tangan (Opsional):
                            </label>

                            <!-- Tab Pilihan Tanda Tangan Edit -->
                            <ul class="nav nav-pills nav-fill mb-2" id="editSigTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 px-3 small" id="edit-draw-tab" data-bs-toggle="pill" data-bs-target="#edit-draw-pane" type="button" role="tab">
                                        <i class="bi bi-pen-fill me-1"></i> Gores Ulang Tanda Tangan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3 small" id="edit-upload-tab" data-bs-toggle="pill" data-bs-target="#edit-upload-pane" type="button" role="tab">
                                        <i class="bi bi-upload me-1"></i> Unggah File Gambar Baru
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="editSigTabContent">
                                <!-- Gores Langsung Edit -->
                                <div class="tab-pane fade show active" id="edit-draw-pane" role="tabpanel">
                                    <div class="p-2 bg-light border rounded text-center">
                                        <canvas id="edit_sig_canvas" class="border bg-white rounded shadow-sm" width="500" height="160" style="touch-action: none; cursor: crosshair; max-width: 100%; display: inline-block;"></canvas>
                                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                            <small class="text-muted"><i class="bi bi-cursor-fill me-1"></i> Goreskan tanda tangan baru jika ingin mengganti</small>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearEditCanvas()">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Hapus / Ulangi
                                            </button>
                                        </div>
                                        <input type="hidden" name="signature_data" id="edit_signature_data">
                                    </div>
                                </div>
                                <!-- Upload File Edit -->
                                <div class="tab-pane fade" id="edit-upload-pane" role="tabpanel">
                                    <div class="p-3 bg-light border rounded">
                                        <input type="file" name="signature_file" class="form-control" accept="image/png,image/jpeg">
                                        <div class="form-text small mt-1">Pilih gambar scan tanda tangan baru jika ingin mengganti.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi Surat Pernyataan Tidak Menggunakan Kendaraan Dinas -->
                    <div class="card mb-3 border border-info bg-info bg-opacity-10">
                        <div class="card-body p-3">
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" name="include_vehicle_statement" value="1" id="edit_vehicle_stmt">
                                <label class="form-check-label fw-bold text-dark" for="edit_vehicle_stmt">
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i> Sertakan Surat Pernyataan Tidak Menggunakan Kendaraan Dinas
                                </label>
                            </div>
                            <div class="small text-muted ms-4">
                                Otomatis menghasilkan lampiran resmi Surat Pernyataan Tidak Menggunakan Kendaraan Dinas bertanda tangan pelaksana untuk klaim biaya transpor BPS Kolaka Utara.
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Foto Dokumentasi Langsung Kamera (Lokasi & Waktu Real-Time) -->
                    @include('petugas.reports._camera_capture', ['prefix' => 'edit', 'showExisting' => true])

                    <!-- Tindakan Penyimpanan -->
                    <div class="p-3 bg-light rounded border">
                        <label class="form-label small fw-semibold text-secondary mb-2">TINDAKAN PENYIMPANAN:</label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action_type" id="editActionSubmit" value="submitted" checked>
                                <label class="form-check-label fw-bold text-primary" for="editActionSubmit">
                                    <i class="bi bi-send-fill me-1"></i> Ajukan ke Admin untuk Diperiksa Ulang
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action_type" id="editActionDraft" value="draft">
                                <label class="form-check-label text-muted" for="editActionDraft">
                                    <i class="bi bi-bookmark me-1"></i> Simpan Sebagai Draft Sementara
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-check2 me-1"></i> Simpan Perubahan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL DETAIL & PREVIEW DOKUMEN LAPORAN RESMI BPS
     ============================================================== -->
<div class="modal fade" id="detailReportModal" tabindex="-1" aria-labelledby="detailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="detailReportModalLabel">
                        <i class="bi bi-file-text text-info me-2"></i> Pratinjau Dokumen Laporan Perjalanan Dinas
                    </h5>
                    <span id="det_status_badge" class="badge"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                @include('reports._document_preview_body')
            </div>
            <div class="modal-footer bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span id="det_locked_pdf_notice" class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-2 d-none">
                        <i class="bi bi-lock-fill me-1"></i> PDF validasi hanya dapat diunduh setelah ACC Administrator
                    </span>
                    <a id="det_preview_link" href="#" target="_blank" class="btn btn-outline-primary d-none">
                        <i class="bi bi-printer me-1"></i> Cetak / Preview PDF
                    </a>
                    <a id="det_download_link" href="#" class="btn btn-bps-orange d-none">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF Resmi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('reports._shared_js')
<script>
    let createSigPad = null;
    let editSigPad = null;
    const CAN_DIRECT_DOWNLOAD_PDF = {{ auth()->user()->isPegawai() ? 'true' : 'false' }};

    function setupSignatureCanvas(canvasId, hiddenInputId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let hasDrawn = false;

        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#0d2142';

        function getCoords(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        function startDraw(e) {
            isDrawing = true;
            hasDrawn = true;
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
            const hiddenInput = document.getElementById(hiddenInputId);
            if (hiddenInput && hasDrawn) {
                hiddenInput.value = canvas.toDataURL('image/png');
            }
        }

        // Mouse events
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);

        // Touch events
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        return {
            clear: function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawn = false;
                const hiddenInput = document.getElementById(hiddenInputId);
                if (hiddenInput) hiddenInput.value = '';
            },
            hasDrawn: function() { return hasDrawn; },
            toDataURL: function() { return canvas.toDataURL('image/png'); }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        createSigPad = setupSignatureCanvas('create_sig_canvas', 'create_signature_data');
        editSigPad = setupSignatureCanvas('edit_sig_canvas', 'edit_signature_data');

        // Form submit handlers to guarantee signature sync
        const createForm = document.getElementById('createReportForm');
        if (createForm) {
            createForm.addEventListener('submit', function() {
                if (createSigPad && createSigPad.hasDrawn()) {
                    document.getElementById('create_signature_data').value = createSigPad.toDataURL();
                }
            });
        }

        const editForm = document.getElementById('editReportForm');
        if (editForm) {
            editForm.addEventListener('submit', function() {
                if (editSigPad && editSigPad.hasDrawn()) {
                    document.getElementById('edit_signature_data').value = editSigPad.toDataURL();
                }
            });
        }
    });

    const ADMIN_BASIS_DEFAULTS = @json($basisDefaults);
    const AUTH_OFFICER_NAME = @json(auth()->user()->name);

    function clearCreateCanvas() {
        if (createSigPad) createSigPad.clear();
    }

    function clearEditCanvas() {
        if (editSigPad) editSigPad.clear();
    }

    function openEditModal(rep) {
        document.getElementById('editReportForm').action = '/petugas/reports/' + rep.id;

        // Tujuan & Waktu
        document.getElementById('edit_title').value = rep.activity_title || '';
        document.getElementById('edit_assignment').value = rep.assignment_id || '';
        const actLoc = rep.activity_location || (rep.assignment ? rep.assignment.location : '') || '';
        document.getElementById('edit_activity_location').value = actLoc;
        document.getElementById('edit_destination_purpose').value = rep.destination_purpose || '';

        // Uraian, Kendala, Solusi & Rekomendasi
        document.getElementById('edit_description').value = rep.work_description || '';
        document.getElementById('edit_obstacles').value = rep.obstacles || '';
        document.getElementById('edit_solutions').value = rep.solutions || '';
        document.getElementById('edit_recommendations').value = rep.recommendations || '-';

        // Penandatanganan
        document.getElementById('edit_signing_city').value = rep.signing_city || 'Lasusua';
        const editSignRaw = rep.resolved_signing_date || (rep.signing_date ? rep.signing_date.substring(0, 10) : '');
        document.getElementById('edit_signing_date_display').value = (editSignRaw ? formatIndonesianDate(editSignRaw) : '-') + ' (Otomatis tanggal input)';

        // Surat Pernyataan Checkbox (opsional)
        document.getElementById('edit_vehicle_stmt').checked = (rep.include_vehicle_statement === true || rep.include_vehicle_statement === 1);

        // Preview Signature jika sudah ada
        const sigContainer = document.getElementById('edit_current_sig_container');
        const sigImg = document.getElementById('edit_current_sig_img');
        if (rep.signature_path) {
            sigContainer.classList.remove('d-none');
            sigImg.src = storageMediaUrl(rep.signature_path);
        } else {
            sigContainer.classList.add('d-none');
        }

        // Reset kamera edit & tampilkan foto tersimpan sebelumnya
        perjadinCamera.reset('edit');
        const existBox = document.getElementById('edit_existing_photos_box');
        const existList = document.getElementById('edit_existing_photos_list');
        if (existBox && existList) {
            existList.innerHTML = '';
            if (Array.isArray(rep.attachments) && rep.attachments.length > 0) {
                existBox.classList.remove('d-none');
                rep.attachments.forEach(att => {
                    existList.appendChild(buildExistingEditAttachmentCard(att));
                });
            } else {
                existBox.classList.add('d-none');
            }
        }

        // Reset canvas gores
        if (editSigPad) editSigPad.clear();

        const notesBox = document.getElementById('edit_admin_notes_box');
        const notesText = document.getElementById('edit_admin_notes_text');
        if (rep.admin_notes && rep.status === 'revision_needed') {
            notesBox.classList.remove('d-none');
            notesText.innerText = rep.admin_notes;
        } else {
            notesBox.classList.add('d-none');
        }

        const modal = new bootstrap.Modal(document.getElementById('editReportModal'));
        modal.show();
    }

    function openDetailModal(rep) {
        populateReportDocumentPreview(rep, ADMIN_BASIS_DEFAULTS);

        const canDownloadPdf = CAN_DIRECT_DOWNLOAD_PDF || rep.status === 'approved';
        const previewBtn = document.getElementById('det_preview_link');
        const downloadBtn = document.getElementById('det_download_link');
        const lockedNotice = document.getElementById('det_locked_pdf_notice');

        if (canDownloadPdf) {
            previewBtn.href = '/petugas/reports/' + rep.id + '/preview';
            downloadBtn.href = '/petugas/reports/' + rep.id + '/download';
            previewBtn.classList.remove('d-none');
            downloadBtn.classList.remove('d-none');
            lockedNotice.classList.add('d-none');
        } else {
            previewBtn.href = '#';
            downloadBtn.href = '#';
            previewBtn.classList.add('d-none');
            downloadBtn.classList.add('d-none');
            lockedNotice.classList.remove('d-none');
        }

        const modal = new bootstrap.Modal(document.getElementById('detailReportModal'));
        modal.show();
    }

    const perjadinCamera = (function() {
        const states = {};
        const geocodeCache = {};

        function getState(prefix) {
            if (!states[prefix]) {
                states[prefix] = {
                    stream: null,
                    watchId: null,
                    clockTimer: null,
                    facing: 'environment',
                    coords: null,
                    address: '',
                    lastGeocodeKey: '',
                    captured: []
                };
            }
            return states[prefix];
        }

        function formatNowParts(dateObj) {
            const fullClock = new Intl.DateTimeFormat('id-ID', {
                weekday: 'long',
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZoneName: 'short'
            }).format(dateObj);

            const stamp = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZoneName: 'short'
            }).format(dateObj);

            return { fullClock, stamp };
        }

        function getFieldActivityLocation(prefix) {
            const locEl = document.getElementById(prefix + '_activity_location');
            return locEl ? locEl.value.trim() : '';
        }

        function getFieldOfficerName() {
            return AUTH_OFFICER_NAME || '';
        }

        function getEffectiveLocationLabel(prefix) {
            const st = getState(prefix);
            if (st.address) return st.address;
            const formLoc = getFieldActivityLocation(prefix);
            if (formLoc) return formLoc;
            if (st.coords) {
                return `Koordinat ${st.coords.latitude.toFixed(5)}, ${st.coords.longitude.toFixed(5)}`;
            }
            return 'Lokasi GPS belum terkunci';
        }

        function updateTelemetryUI(prefix) {
            const st = getState(prefix);
            const parts = formatNowParts(new Date());

            const clockEl = document.getElementById(prefix + '_live_clock_text');
            const hudTimeEl = document.getElementById(prefix + '_hud_time');
            const hudLocEl = document.getElementById(prefix + '_hud_loc');
            const hudCoordsEl = document.getElementById(prefix + '_hud_coords');
            const gpsBadge = document.getElementById(prefix + '_gps_badge');
            const addrEl = document.getElementById(prefix + '_gps_address_text');

            if (clockEl) clockEl.textContent = parts.fullClock;
            if (hudTimeEl) hudTimeEl.textContent = 'Waktu: ' + parts.stamp;

            const locLabel = getEffectiveLocationLabel(prefix);
            if (hudLocEl) hudLocEl.textContent = 'Lokasi: ' + locLabel;
            if (addrEl) addrEl.textContent = st.address || getFieldActivityLocation(prefix) || '';

            if (st.coords) {
                const latStr = st.coords.latitude.toFixed(6);
                const lngStr = st.coords.longitude.toFixed(6);
                const accStr = st.coords.accuracy ? Math.round(st.coords.accuracy) : '?';
                if (gpsBadge) {
                    gpsBadge.className = 'badge bg-success-subtle text-success border border-success-subtle';
                    gpsBadge.innerHTML = `<i class="bi bi-broadcast-pin me-1"></i> GPS Aktif: ${latStr}, ${lngStr} (±${accStr} m)`;
                }
                if (hudCoordsEl) {
                    hudCoordsEl.textContent = `GPS: Lat ${latStr}, Lng ${lngStr} (±${accStr} m)`;
                }
            }
        }

        async function reverseGeocode(prefix, lat, lng) {
            const st = getState(prefix);
            const key = `${lat.toFixed(4)},${lng.toFixed(4)}`;
            if (st.lastGeocodeKey === key && st.address) return;
            st.lastGeocodeKey = key;

            if (geocodeCache[key]) {
                st.address = geocodeCache[key];
                updateTelemetryUI(prefix);
                return;
            }

            try {
                const ctrl = new AbortController();
                const timer = setTimeout(() => ctrl.abort(), 3500);
                const resp = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&zoom=16&addressdetails=1`,
                    { signal: ctrl.signal, headers: { 'Accept': 'application/json' } }
                );
                clearTimeout(timer);
                if (!resp.ok) return;
                const data = await resp.json();
                const addr = data.address || {};
                const parts = [
                    addr.village || addr.suburb || addr.hamlet || addr.town || addr.road,
                    addr.city_district || addr.municipality,
                    addr.county || addr.city || addr.state_district
                ].filter(Boolean);
                const resolved = parts.length ? parts.join(', ') : (data.display_name ? data.display_name.split(',').slice(0, 3).join(',').trim() : '');
                if (resolved) {
                    geocodeCache[key] = resolved;
                    st.address = resolved;
                    updateTelemetryUI(prefix);
                }
            } catch (e) {
                // Offline / timeout: tetap gunakan koordinat GPS + lokasi tugas
            }
        }

        function handleGeoSuccess(prefix, pos) {
            const st = getState(prefix);
            st.coords = {
                latitude: pos.coords.latitude,
                longitude: pos.coords.longitude,
                accuracy: pos.coords.accuracy
            };
            updateTelemetryUI(prefix);
            reverseGeocode(prefix, pos.coords.latitude, pos.coords.longitude);
        }

        function handleGeoError(prefix, err) {
            const gpsBadge = document.getElementById(prefix + '_gps_badge');
            const hudCoords = document.getElementById(prefix + '_hud_coords');
            let msg = 'GPS tidak aktif';
            if (err && err.code === 1) msg = 'Izin lokasi GPS ditolak';
            else if (err && err.code === 2) msg = 'Sinyal GPS tidak tersedia';
            else if (err && err.code === 3) msg = 'Waktu pencarian GPS habis';

            if (gpsBadge) {
                gpsBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle';
                gpsBadge.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${msg}`;
            }
            if (hudCoords) {
                hudCoords.textContent = `GPS: ${msg}`;
            }
        }

        function startGps(prefix) {
            const st = getState(prefix);
            if (!('geolocation' in navigator)) {
                handleGeoError(prefix, null);
                return;
            }
            if (st.watchId !== null) {
                navigator.geolocation.clearWatch(st.watchId);
                st.watchId = null;
            }
            st.watchId = navigator.geolocation.watchPosition(
                pos => handleGeoSuccess(prefix, pos),
                err => handleGeoError(prefix, err),
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        }

        function refreshGps(prefix) {
            if (!('geolocation' in navigator)) {
                handleGeoError(prefix, null);
                return;
            }
            const gpsBadge = document.getElementById(prefix + '_gps_badge');
            if (gpsBadge) {
                gpsBadge.className = 'badge bg-warning text-dark';
                gpsBadge.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Memperbarui GPS...';
            }
            navigator.geolocation.getCurrentPosition(
                pos => handleGeoSuccess(prefix, pos),
                err => handleGeoError(prefix, err),
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
            );
        }

        function startClock(prefix) {
            const st = getState(prefix);
            if (st.clockTimer) clearInterval(st.clockTimer);
            updateTelemetryUI(prefix);
            st.clockTimer = setInterval(() => updateTelemetryUI(prefix), 1000);
        }

        function stopStreamOnly(prefix) {
            const st = getState(prefix);
            if (st.stream) {
                st.stream.getTracks().forEach(t => t.stop());
                st.stream = null;
            }
            const video = document.getElementById(prefix + '_cam_video');
            if (video) {
                video.srcObject = null;
                video.classList.add('d-none');
            }
            const hud = document.getElementById(prefix + '_cam_hud');
            const snapWrap = document.getElementById(prefix + '_snap_wrapper');
            const placeholder = document.getElementById(prefix + '_cam_placeholder');
            if (hud) hud.classList.add('d-none');
            if (snapWrap) snapWrap.classList.add('d-none');
            if (placeholder) placeholder.classList.remove('d-none');
        }

        async function start(prefix) {
            const st = getState(prefix);
            startClock(prefix);
            startGps(prefix);

            const statusMsg = document.getElementById(prefix + '_cam_status_msg');
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                if (statusMsg) {
                    statusMsg.textContent = window.isSecureContext
                        ? 'Browser tidak mendukung kamera langsung (WebRTC).'
                        : 'Akses kamera memerlukan koneksi aman (HTTPS / localhost).';
                }
                return;
            }

            stopStreamOnly(prefix);
            if (statusMsg) statusMsg.textContent = 'Mengakses kamera perangkat...';

            try {
                let stream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: st.facing }, width: { ideal: 1280 }, height: { ideal: 720 } },
                        audio: false
                    });
                } catch (constraintErr) {
                    if (constraintErr && (constraintErr.name === 'OverconstrainedError' || constraintErr.name === 'NotFoundError')) {
                        stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    } else {
                        throw constraintErr;
                    }
                }

                st.stream = stream;
                const video = document.getElementById(prefix + '_cam_video');
                const placeholder = document.getElementById(prefix + '_cam_placeholder');
                const hud = document.getElementById(prefix + '_cam_hud');
                const snapWrap = document.getElementById(prefix + '_snap_wrapper');
                const switchBtn = document.getElementById(prefix + '_btn_switch_cam');

                if (video) {
                    video.srcObject = stream;
                    video.classList.remove('d-none');
                    await video.play();
                }
                if (placeholder) placeholder.classList.add('d-none');
                if (hud) hud.classList.remove('d-none');
                if (snapWrap) snapWrap.classList.remove('d-none');
                if (switchBtn) switchBtn.classList.remove('d-none');
                updateTelemetryUI(prefix);
            } catch (err) {
                if (statusMsg) {
                    const reason = err && err.name === 'NotAllowedError'
                        ? 'Izin kamera ditolak oleh browser. Mohon izinkan akses kamera lalu klik coba lagi.'
                        : (err && err.name === 'NotFoundError'
                            ? 'Kamera tidak ditemukan pada perangkat ini.'
                            : 'Gagal membuka kamera: ' + (err && err.message ? err.message : 'Periksa izin perangkat.'));
                    statusMsg.textContent = reason;
                }
            }
        }

        function stop(prefix) {
            const st = getState(prefix);
            stopStreamOnly(prefix);
            if (st.watchId !== null && 'geolocation' in navigator) {
                navigator.geolocation.clearWatch(st.watchId);
                st.watchId = null;
            }
            if (st.clockTimer) {
                clearInterval(st.clockTimer);
                st.clockTimer = null;
            }
        }

        function switchFacing(prefix) {
            const st = getState(prefix);
            st.facing = (st.facing === 'environment') ? 'user' : 'environment';
            start(prefix);
        }

        function drawWatermark(ctx, w, h, lines) {
            const fontSize = Math.max(13, Math.round(w * 0.017));
            const lineHeight = Math.round(fontSize * 1.42);
            const padX = Math.round(w * 0.02);
            const padY = Math.round(lineHeight * 0.55);
            const boxHeight = (lines.length * lineHeight) + (padY * 2);
            const startY = h - boxHeight;

            ctx.fillStyle = 'rgba(8, 15, 28, 0.74)';
            ctx.fillRect(0, startY, w, boxHeight);

            ctx.textBaseline = 'top';
            lines.forEach((line, i) => {
                ctx.font = (i === 0 ? 'bold ' : '') + fontSize + 'px Arial, sans-serif';
                ctx.fillStyle = i === 0 ? '#fbbf24' : '#ffffff';
                ctx.fillText(line.text, padX, startY + padY + (i * lineHeight), w - (padX * 2));
            });
        }

        function capture(prefix) {
            const st = getState(prefix);
            const video = document.getElementById(prefix + '_cam_video');
            if (!video || !video.videoWidth || !video.videoHeight) {
                return;
            }

            const maxW = 1280;
            const scale = video.videoWidth > maxW ? (maxW / video.videoWidth) : 1;
            const targetW = Math.round(video.videoWidth * scale);
            const targetH = Math.round(video.videoHeight * scale);

            const canvas = document.createElement('canvas');
            canvas.width = targetW;
            canvas.height = targetH;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, targetW, targetH);

            const now = new Date();
            const parts = formatNowParts(now);
            const locLabel = getEffectiveLocationLabel(prefix);
            const officer = getFieldOfficerName(prefix) || 'Petugas Lapangan';
            const lat = st.coords ? st.coords.latitude : null;
            const lng = st.coords ? st.coords.longitude : null;
            const acc = st.coords ? st.coords.accuracy : null;
            const gpsLine = (lat !== null && lng !== null)
                ? `GPS: Lat ${lat.toFixed(6)}, Lng ${lng.toFixed(6)} (Akurasi ±${acc ? Math.round(acc) : '?'} m)`
                : 'GPS: Koordinat tidak terdeteksi (Pastikan izin lokasi aktif)';

            drawWatermark(ctx, targetW, targetH, [
                { text: 'Lokasi : ' + locLabel },
                { text: gpsLine },
                { text: `Waktu : ${parts.stamp}  |  Petugas: ${officer}` }
            ]);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
            const timezone = (Intl.DateTimeFormat().resolvedOptions().timeZone) || 'Asia/Makassar';

            st.captured.push({
                id: 'cap_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
                dataUrl,
                lat,
                lng,
                acc,
                capturedAtIso: now.toISOString(),
                capturedLabel: parts.stamp,
                locationName: locLabel,
                timezone
            });

            renderCaptured(prefix);
        }

        function removeCaptured(prefix, capId) {
            const st = getState(prefix);
            st.captured = st.captured.filter(item => item.id !== capId);
            renderCaptured(prefix);
        }

        function createHiddenInput(name, value) {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = name;
            inp.value = (value === null || value === undefined) ? '' : String(value);
            return inp;
        }

        function renderCaptured(prefix) {
            const st = getState(prefix);
            const grid = document.getElementById(prefix + '_captured_grid');
            const empty = document.getElementById(prefix + '_captured_empty');
            const inputs = document.getElementById(prefix + '_captured_inputs');
            const countBadge = document.getElementById(prefix + '_captured_count');

            if (!grid || !inputs) return;
            grid.innerHTML = '';
            inputs.innerHTML = '';

            if (countBadge) {
                countBadge.textContent = st.captured.length + ' Foto Baru';
            }

            if (st.captured.length === 0) {
                if (empty) empty.classList.remove('d-none');
                return;
            }
            if (empty) empty.classList.add('d-none');

            st.captured.forEach((item, idx) => {
                // Hidden inputs synchronized with PHP array fields
                inputs.appendChild(createHiddenInput('camera_photos[]', item.dataUrl));
                inputs.appendChild(createHiddenInput('camera_latitudes[]', item.lat !== null ? item.lat : ''));
                inputs.appendChild(createHiddenInput('camera_longitudes[]', item.lng !== null ? item.lng : ''));
                inputs.appendChild(createHiddenInput('camera_accuracies[]', item.acc !== null ? item.acc : ''));
                inputs.appendChild(createHiddenInput('camera_captured_ats[]', item.capturedAtIso));
                inputs.appendChild(createHiddenInput('camera_location_names[]', item.locationName));
                inputs.appendChild(createHiddenInput('camera_timezones[]', item.timezone));

                // Preview card
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4';

                const card = document.createElement('div');
                card.className = 'card h-100 border border-success shadow-sm p-2';

                const img = document.createElement('img');
                img.src = item.dataUrl;
                img.className = 'img-fluid rounded mb-2';
                img.style.maxHeight = '140px';
                img.style.objectFit = 'cover';
                img.alt = 'Foto Kamera ' + (idx + 1);
                card.appendChild(img);

                const timeDiv = document.createElement('div');
                timeDiv.className = 'small fw-semibold text-dark';
                timeDiv.style.fontSize = '0.72rem';
                timeDiv.textContent = item.capturedLabel;
                card.appendChild(timeDiv);

                const locDiv = document.createElement('div');
                locDiv.className = 'small text-secondary text-truncate';
                locDiv.style.fontSize = '0.7rem';
                locDiv.title = item.locationName;
                locDiv.textContent = item.locationName;
                card.appendChild(locDiv);

                if (item.lat !== null && item.lng !== null) {
                    const coordDiv = document.createElement('div');
                    coordDiv.className = 'font-monospace text-muted';
                    coordDiv.style.fontSize = '0.68rem';
                    coordDiv.textContent = `${item.lat.toFixed(6)}, ${item.lng.toFixed(6)}`;
                    card.appendChild(coordDiv);
                }

                const rmBtn = document.createElement('button');
                rmBtn.type = 'button';
                rmBtn.className = 'btn btn-xs btn-outline-danger mt-2 py-0.5';
                rmBtn.style.fontSize = '0.72rem';
                rmBtn.innerHTML = '<i class="bi bi-trash3 me-1"></i> Hapus Foto';
                rmBtn.addEventListener('click', () => removeCaptured(prefix, item.id));
                card.appendChild(rmBtn);

                col.appendChild(card);
                grid.appendChild(col);
            });
        }

        function reset(prefix) {
            const st = getState(prefix);
            st.captured = [];
            renderCaptured(prefix);
        }

        return { start, stop, switchFacing, refreshGps, capture, reset };
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const createModalEl = document.getElementById('createReportModal');
        if (createModalEl) {
            createModalEl.addEventListener('shown.bs.modal', function() {
                perjadinCamera.reset('create');
                perjadinCamera.start('create');
            });
            createModalEl.addEventListener('hidden.bs.modal', function() {
                perjadinCamera.stop('create');
            });
        }

        const editModalEl = document.getElementById('editReportModal');
        if (editModalEl) {
            editModalEl.addEventListener('shown.bs.modal', function() {
                perjadinCamera.start('edit');
            });
            editModalEl.addEventListener('hidden.bs.modal', function() {
                perjadinCamera.stop('edit');
            });
        }
    });
</script>
@endpush

