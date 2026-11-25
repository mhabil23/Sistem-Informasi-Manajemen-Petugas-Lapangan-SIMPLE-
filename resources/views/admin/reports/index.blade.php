@extends('layouts.app')

@section('title', 'Progres Pengolahan Dokumen')
@section('page_title', 'Progres Pengolahan Dokumen &amp; Verifikasi Laporan Petugas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Progres Pengolahan Dokumen</li>
@endsection

@push('styles')
<style>
/* Bento Grid Layout & Cards */
.bento-card-clean {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    transition: all 0.22s ease;
}

/* Action Buttons */
.btn-bps-orange {
    background: #F37021;
    color: #FFFFFF !important;
    border: none;
    font-weight: 700;
    border-radius: 999px;
    padding: 0.55rem 1.25rem;
    box-shadow: 0 4px 14px rgba(243, 112, 33, 0.25);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
}
.btn-bps-orange:hover {
    background: #E05D0E;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(243, 112, 33, 0.35);
}
.btn-outline-orange {
    color: #F37021 !important;
    border: 1px solid #F37021;
    background: transparent;
    font-weight: 700;
    border-radius: 999px;
    padding: 0.55rem 1rem;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}
.btn-outline-orange:hover {
    background-color: #F37021;
    border-color: #F37021;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);
}

/* Fixed Bento Table Card (Compact, Anti-Geser) */
.bento-table-card {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    width: 100%;
}
.bento-table-header {
    padding: 0.9rem 1.25rem;
    border-bottom: 1px solid #E2E8F0;
    background: #FAFAFA;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.bento-table-container-fixed {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}
.bento-table-fixed {
    table-layout: fixed;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.8rem;
}
@media (min-width: 768px) and (max-width: 1279.98px) {
    .bento-table-card .bento-table-fixed {
        min-width: 820px;
    }
}
.bento-table-fixed th {
    background: #F8FAFC;
    color: #475569;
    font-weight: 700;
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid #E2E8F0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.bento-table-fixed td {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #F1F5F9;
    font-size: 0.8rem;
    overflow: hidden;
}
.bento-table-fixed tbody tr:hover {
    background-color: #F8FAFC;
}

/* Action buttons in table */
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
    text-decoration: none;
    font-size: 0.78rem;
    flex-shrink: 0;
}
.bento-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
}
.bento-action-btn.btn-view { color: #0284C7; }
.bento-action-btn.btn-view:hover { background: #E0F2FE; border-color: #BAE6FD; }
.bento-action-btn.btn-edit { color: #D97706; }
.bento-action-btn.btn-edit:hover { background: #FFFBEB; border-color: #FDE68A; }
.bento-action-btn.btn-review { color: #4F46E5; }
.bento-action-btn.btn-review:hover { background: #EEF2FF; border-color: #C7D2FE; }
.bento-action-btn.btn-print { color: #2563EB; }
.bento-action-btn.btn-print:hover { background: #EFF6FF; border-color: #BFDBFE; }
.bento-action-btn.btn-download { color: #DC2626; }
.bento-action-btn.btn-download:hover { background: #FEF2F2; border-color: #FECACA; }
.bento-action-btn.btn-delete { color: #DC2626; }
.bento-action-btn.btn-delete:hover { background: #FEF2F2; border-color: #FECACA; }

/* Mobile Responsive */
@media (max-width: 767.98px) {
    .bento-table-card .bento-table-fixed thead {
        display: none;
    }
    .bento-table-card .bento-table-fixed,
    .bento-table-card .bento-table-fixed tbody,
    .bento-table-card .bento-table-fixed tr,
    .bento-table-card .bento-table-fixed td {
        display: block;
        width: 100% !important;
        text-align: left !important;
    }
    .bento-table-card .bento-table-fixed tr {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        margin: 0.6rem auto;
        width: calc(100% - 1.2rem) !important;
        padding: 0.85rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
        box-sizing: border-box;
    }
    .bento-table-card .bento-table-fixed td {
        border: none;
        padding: 0.25rem 0;
    }
    .bento-table-card .bento-table-fixed td:last-child {
        border-top: 1px dashed #E2E8F0;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        display: flex;
        justify-content: flex-end;
    }
    .bento-table-card .bento-table-fixed td[colspan] {
        border-top: none;
        margin-top: 0;
        justify-content: center;
        text-align: center !important;
    }
}

/* Bulk Action Buttons - Clean, Modern & Elegant */
.btn-bulk-action {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 0.32rem 0.8rem;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    cursor: pointer;
}
.btn-bulk-action:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
}
.btn-bulk-action-danger {
    background: #ffffff;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 0.32rem 0.8rem;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    cursor: pointer;
}
.btn-bulk-action-danger:hover {
    background: #fef2f2;
    border-color: #f87171;
    color: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(220, 38, 38, 0.15);
}
.bento-table-fixed tbody tr.row-selected td {
    background-color: #f0f7ff !important;
}
</style>
@endpush

@section('content')
@if(auth()->user()->isAdmin() && ($unreadNotifCount ?? 0) > 0)
    <div class="alert alert-warning border-warning d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 py-2 px-3 shadow-sm rounded-4">
        <div class="small text-dark">
            <i class="bi bi-bell-fill text-warning me-1"></i>
            <strong>Informasi Baru:</strong> Terdapat <strong>{{ $unreadNotifCount }} pemberitahuan</strong> laporan perjadin baru/diperbarui dari petugas lapangan.
        </div>
        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-xs btn-outline-dark py-1 px-2.5 rounded-pill" style="font-size: 0.72rem;">
                <i class="bi bi-check-lg me-1"></i> Tandai Dibaca
            </button>
        </form>
    </div>
@endif

<!-- Bento Search & Filter Card -->
<div class="bento-card-clean p-3 mb-3">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-center">
        <div class="col-lg-3 col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       name="search" 
                       class="form-control border-start-0 ps-1" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nomor, judul, uraian...">
            </div>
        </div>

        <div class="col-lg-2 col-md-2">
            <select name="status" class="form-select">
                <option value="all">-- Pilih Status --</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan (Menunggu)</option>
                <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Dalam Pemeriksaan</option>
                <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>

        <div class="col-lg-3 col-md-3">
            <select name="officer_id" class="form-select">
                <option value="">-- Pilih Petugas --</option>
                @foreach($officers as $officer)
                    <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-2">
            <select name="assignment_id" class="form-select">
                <option value="">-- Pilih Kegiatan --</option>
                @foreach($assignments as $assignment)
                    <option value="{{ $assignment->id }}" {{ request('assignment_id') == $assignment->id ? 'selected' : '' }}>{{ $assignment->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-bps-orange flex-fill justify-content-center py-2 shadow-sm">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'officer_id', 'assignment_id']))
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-orange px-3 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Bento Table Card (Anti-Geser, 100% Pas Layar) -->
<div class="bento-table-card mb-4">
    <div class="bento-table-header">
        <div>
            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">
                Daftar Laporan Kegiatan Lapangan
            </h6>
            <span class="text-muted" style="font-size: 0.7rem;">Total {{ $reports->total() }} laporan terdaftar dalam sistem</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($submittedCount > 0 || ($draftCount ?? 0) > 0)
                <a href="{{ route('admin.reports.index', ['status' => 'submitted']) }}" class="badge bg-warning text-dark px-2.5 py-1.5 shadow-sm text-decoration-none rounded-pill" style="font-size: 0.72rem;">
                    <i class="bi bi-bell-fill me-1"></i> {{ $submittedCount }} Menunggu ACC
                    @if(($draftCount ?? 0) > 0)
                        &bull; {{ $draftCount }} Draft
                    @endif
                </a>
            @endif
            <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                Halaman {{ $reports->currentPage() }} dari {{ max(1, $reports->lastPage()) }}
            </span>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn-bps-orange shadow-sm py-1.5 px-3" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#basisSettingsModal">
                    <i class="bi bi-sliders me-1"></i> Atur B. Dasar Pelaksanaan
                </button>
            @endif
        </div>
    </div>

    @if(auth()->user()->canAccessMonitoring())
        <!-- Bulk Action Toolbar (Modern, Bersih & Elegan) -->
        <div id="bulkActionBar" class="d-none px-3 py-2.5 border-bottom align-items-center justify-content-between flex-wrap gap-2" style="background: #f8fafc; border-color: #e2e8f0 !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #005BAC; color: #ffffff; font-size: 0.78rem;">
                    <i class="bi bi-check2-circle fs-6"></i>
                    <span id="bulkSelectedCountBadge">0 Laporan Dipilih</span>
                </span>
                <span class="text-muted small">|</span>
                <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none p-0 d-inline-flex align-items-center gap-1" onclick="clearAllSelectedReports()" style="font-size: 0.78rem;">
                    <i class="bi bi-x-circle text-muted"></i> Batal Pilih
                </button>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn-bulk-action" onclick="submitBulkAction('approve')" title="ACC / Setujui seluruh laporan yang dicentang secara serentak">
                    <i class="bi bi-check2-circle text-success fs-6"></i> ACC / Setujui Terpilih
                </button>
                @if(auth()->user()->isAdmin())
                    <button type="button" class="btn-bulk-action-danger" onclick="submitBulkAction('delete')" title="Hapus seluruh laporan yang dicentang">
                        <i class="bi bi-trash fs-6"></i> Hapus
                    </button>
                @endif
            </div>
        </div>
    @endif

    <div class="bento-table-container-fixed">
        <table class="table bento-table-fixed align-middle mb-0">
            <thead>
                <tr>
                    @if(auth()->user()->canAccessMonitoring())
                        <th style="width: 38px;" class="text-center">
                            <input type="checkbox" id="selectAllReportsCheckbox" class="form-check-input" onchange="toggleSelectAllReports(this)" title="Pilih Semua Laporan di Halaman Ini">
                        </th>
                    @endif
                    <th style="width: 12%;">NOMOR LAPORAN</th>
                    <th style="width: 14%;">PETUGAS LAPANGAN</th>
                    <th style="width: 18%;">KEGIATAN &amp; TANGGAL</th>
                    <th style="width: 17%;">URAIAN SINGKAT</th>
                    <th class="text-center" style="width: 8%;">LAMPIRAN</th>
                    <th class="text-center" style="width: 10%;">STATUS</th>
                    <th class="text-end pe-2" style="width: 18%;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $rep)
                    <tr>
                        @if(auth()->user()->canAccessMonitoring())
                            <td class="text-center" data-label="Pilih">
                                <input type="checkbox" 
                                       name="report_ids[]" 
                                       value="{{ $rep->id }}" 
                                       class="form-check-input report-row-checkbox" 
                                       data-title="{{ $rep->activity_title }}"
                                       onchange="onReportRowCheckboxChange()">
                            </td>
                        @endif
                        <td>
                            <span class="fw-bold font-monospace text-dark text-truncate d-block" style="font-size: 0.8rem;" title="{{ $rep->report_number ?? 'LAP-'.$rep->id }}">
                                {{ $rep->report_number ?? 'LAP-'.$rep->id }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.82rem;" title="{{ $rep->user ? $rep->user->name : '-' }}">
                                {{ $rep->user ? $rep->user->name : '-' }}
                            </div>
                            <small class="text-muted text-truncate d-block" style="font-size: 0.7rem;">{{ $rep->user ? $rep->user->email : '' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-primary text-truncate mb-0.5" style="font-size: 0.8rem;" title="{{ $rep->activity_title }}">
                                {{ $rep->activity_title }}
                            </div>
                            <div class="small text-muted text-truncate d-flex align-items-center gap-1" style="font-size: 0.7rem;">
                                <i class="bi bi-calendar-event text-secondary flex-shrink-0"></i>
                                <span>{{ $rep->activity_date->format('d/m/Y') }}</span>
                                @if($rep->assignment)
                                    &bull; <span class="text-secondary text-truncate">{{ $rep->assignment->title }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="small text-secondary text-truncate" style="font-size: 0.74rem;" title="{{ $rep->work_description }}">
                                {{ $rep->work_description }}
                            </div>
                            @if($rep->admin_notes)
                                <div class="small text-danger text-truncate mt-0.5" style="font-size: 0.7rem;" title="{{ $rep->admin_notes }}">
                                    <i class="bi bi-chat-left-dots-fill me-0.5"></i> Catatan: {{ \Illuminate\Support\Str::limit($rep->admin_notes, 30) }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($rep->attachments->isNotEmpty())
                                <span class="badge bg-light text-dark border rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">
                                    <i class="bi bi-paperclip text-primary me-0.5"></i> {{ $rep->attachments->count() }} Berkas
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $rep->status_badge }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                {{ $rep->status_label }}
                            </span>
                        </td>
                        <td class="text-end pe-2">
                            <div class="d-flex align-items-center justify-content-end gap-1 flex-wrap">
                                <!-- 1. Melihat Laporan Lengkap -->
                                <button type="button"
                                        class="bento-action-btn btn-view"
                                        title="Lihat Detail &amp; Pratinjau Laporan"
                                        data-bs-toggle="tooltip"
                                        onclick='openViewReportModal(@json($rep))'>
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if(auth()->user()->isAdmin())
                                    <!-- 2. Edit Inputan Laporan Petugas -->
                                    <button type="button"
                                            class="bento-action-btn btn-edit"
                                            title="Edit Inputan Laporan"
                                            data-bs-toggle="tooltip"
                                            onclick='openAdminEditModal(@json($rep))'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- 3. ACC Laporan (Setujui Cepat) -->
                                    @if($rep->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.reports.approve', $rep) }}" class="d-inline" onsubmit="return confirm('ACC (Setujui) laporan {{ addslashes($rep->activity_title) }} agar petugas dapat mengunduh PDF resminya?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-success btn-sm px-2 py-0.5 fw-semibold rounded-pill d-inline-flex align-items-center gap-0.5 shadow-none"
                                                    title="ACC / Setujui Laporan"
                                                    data-bs-toggle="tooltip"
                                                    style="font-size: 0.7rem; height: 28px;">
                                                <i class="bi bi-check-all"></i> ACC
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-1.5" style="font-size: 0.65rem;" title="Sudah Di-ACC">
                                            <i class="bi bi-check-circle-fill me-0.5"></i> ACC
                                        </span>
                                    @endif

                                    <!-- Verifikasi / Revisi dengan Catatan -->
                                    <button type="button" 
                                            class="bento-action-btn btn-review text-primary" 
                                            title="Verifikasi / Catatan Revisi" 
                                            data-bs-toggle="tooltip"
                                            onclick='openReviewModal(@json($rep))'>
                                        <i class="bi bi-file-earmark-check"></i>
                                    </button>
                                @endif

                                <!-- Pratinjau / Cetak PDF -->
                                <a href="{{ route('admin.reports.preview', $rep) }}" 
                                   target="_blank"
                                   class="bento-action-btn btn-print" 
                                   title="Pratinjau / Cetak Dokumen PDF" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-printer"></i>
                                </a>

                                <!-- Unduh PDF Resmi -->
                                <a href="{{ route('admin.reports.download', $rep) }}" 
                                   class="bento-action-btn btn-download text-danger" 
                                   title="Unduh PDF Resmi" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <!-- Hapus Laporan -->
                                    <form method="POST" action="{{ route('admin.reports.destroy', $rep) }}" class="d-inline" onsubmit="return confirm('Hapus laporan {{ addslashes($rep->activity_title) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bento-action-btn btn-delete" 
                                                title="Hapus Laporan" 
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
                        <td colspan="{{ auth()->user()->canAccessMonitoring() ? 8 : 7 }}" class="text-center py-5 text-muted">
                            <div class="d-inline-flex p-3 rounded-circle bg-light text-secondary mb-2">
                                <i class="bi bi-clipboard2-check fs-1 opacity-50"></i>
                            </div>
                            <div class="fw-semibold text-dark">Belum ada laporan kegiatan ditemukan.</div>
                            <div class="small text-muted">Laporan dari petugas lapangan akan otomatis tampil di sini untuk diverifikasi.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div class="p-3 bg-white border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted" style="font-size: 0.75rem;">
                Menampilkan {{ $reports->firstItem() }} sampai {{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
            </div>
            <div>
                {{ $reports->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@if(auth()->user()->isAdmin())
<!-- ==============================================================
     MODAL REVIEW / VERIFIKASI LAPORAN
     ============================================================== -->
<div class="modal fade" id="reviewReportModal" tabindex="-1" aria-labelledby="reviewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center bg-primary" style="width: 32px; height: 32px;">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="reviewReportModalLabel">
                        Periksa &amp; Verifikasi Laporan Kegiatan
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewReportForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Rincian Laporan -->
                    <div class="row g-3 mb-3 bg-light p-3 rounded-3 border">
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
                            <div class="p-3 bg-white border rounded-3 text-dark small" style="white-space: pre-wrap; max-height: 180px; overflow-y: auto;" id="rev_description"></div>
                        </div>
                    </div>

                    <!-- Galeri Lampiran -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">
                            <i class="bi bi-paperclip text-primary me-1"></i> BUKTI DOKUMENTASI &amp; LAMPIRAN:
                        </label>
                        <div id="rev_attachments_container" class="row g-2">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- B. Dasar Pelaksanaan (Dapat disesuaikan Admin per Laporan) -->
                    @include('admin.partials._basis_fields', ['prefix' => 'rev', 'defaults' => [], 'required' => false])

                    <!-- Aksi Verifikasi Admin -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-primary me-1"></i> Keputusan Verifikasi Admin</h6>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS PERSETUJUAN <span class="text-danger">*</span></label>
                            <select id="rev_status_select" name="status" class="form-select" required>
                                <option value="approved">Disetujui (Laporan Sah & Sesuai)</option>
                                <option value="revision_needed">Perlu Revisi (Kembalikan ke Petugas)</option>
                                <option value="reviewing">Dalam Pemeriksaan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">CATATAN / FEEDBACK UNTUK PETUGAS</label>
                            <textarea id="rev_admin_notes" name="admin_notes" class="form-control" rows="3" placeholder="Tuliskan catatan perbaikan atau rekomendasi untuk petugas lapangan..."></textarea>
                            <div class="form-text small">Wajib diisi jika status dipilih 'Perlu Revisi'.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between flex-wrap gap-2">
                    <div class="d-flex gap-2">
                        <a id="rev_preview_link" href="#" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" style="font-size: 0.82rem;">
                            <i class="bi bi-printer me-1"></i> Cetak / Preview
                        </a>
                        <a id="rev_download_link" href="#" class="btn btn-outline-danger rounded-pill px-3 py-1.5" style="font-size: 0.82rem;">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Tutup</button>
                        <button type="submit" class="btn-bps-orange py-1.5 px-3" style="font-size: 0.82rem;">
                            <i class="bi bi-check-all me-1"></i> Simpan Hasil Verifikasi
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
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: var(--bps-orange, #F37021); width: 32px; height: 32px;">
                        <i class="bi bi-sliders"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="basisSettingsModalLabel">
                            Atur B. Dasar Pelaksanaan (Role Admin)
                        </h6>
                        <div class="small text-muted" style="font-size: 0.72rem;">Nilai DIPA &amp; Surat Tugas otomatis diterapkan saat petugas mengisi Laporan Perjadin.</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.reports.basis-settings') }}">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    @include('admin.partials._basis_fields', ['prefix' => 'master', 'defaults' => $basisSettings, 'required' => true])
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn-bps-orange py-1.5 px-3" style="font-size: 0.82rem;">
                        <i class="bi bi-check-lg me-1"></i> Simpan Pengaturan
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
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center bg-info" style="width: 32px; height: 32px;">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="viewReportModalLabel">
                        Lihat Detail &amp; Dokumen Laporan Petugas
                    </h6>
                    <span id="det_status_badge" class="badge rounded-pill ms-2"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                @include('reports._document_preview_body')
            </div>
            <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex flex-wrap justify-content-between gap-2">
                <div class="d-flex gap-2">
                    <a id="adm_view_preview_link" href="#" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" style="font-size: 0.82rem;">
                        <i class="bi bi-printer me-1"></i> Cetak / Preview
                    </a>
                    <a id="adm_view_download_link" href="#" class="btn btn-outline-danger rounded-pill px-3 py-1.5" style="font-size: 0.82rem;">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <button type="button" class="btn btn-warning text-dark fw-semibold rounded-pill px-3 py-1.5" style="font-size: 0.82rem;" onclick="switchAdminViewToEdit()">
                        <i class="bi bi-pencil-square me-1"></i> Edit Inputan
                    </button>
                    <form id="adm_view_approve_form" method="POST" action="" class="d-inline" onsubmit="return confirm('ACC (Setujui) laporan ini agar petugas dapat mengunduh PDF resminya?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success fw-semibold rounded-pill px-3 py-1.5" style="font-size: 0.82rem;">
                            <i class="bi bi-check-all me-1"></i> ACC / Setujui
                        </button>
                    </form>
                    <span id="adm_view_already_acc_badge" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-2 px-3 d-none">
                        <i class="bi bi-check-circle-fill me-1"></i> Sudah Di-ACC Admin
                    </span>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Tutup</button>
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
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="adminEditReportModalLabel">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Edit Inputan Laporan Perjadin Petugas
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
                            <i class="bi bi-person-badge text-primary me-1"></i> A. YANG MELAKUKAN PERJALANAN DINAS
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
                            <i class="bi bi-geo-alt text-primary me-1"></i> C &amp; D. TUJUAN &amp; WAKTU PERJALANAN DINAS
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
                                    <label class="form-label small fw-semibold text-secondary">D. Tanggal Pelaksanaan Kegiatan <span class="text-danger">*</span></label>
                                    <input type="date" id="adm_edit_date" name="activity_date" class="form-control" required>
                                    <div class="form-text small">Durasi otomatis ditetapkan selama 1 (satu) hari.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E & F. Hasil, Kendala, Solusi & Saran -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2 px-3 fw-bold text-dark small">
                            <i class="bi bi-file-earmark-text text-primary me-1"></i> E &amp; F. HASIL PERJALANAN DINAS &amp; SARAN
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
                            <span><i class="bi bi-receipt me-1"></i> DAFTAR PENGELUARAN RIIL</span>
                            <button type="button" class="btn btn-xs btn-success py-0.5 px-2" style="font-size: 0.72rem;" onclick="addExpenseRow('adm_edit_expense_list', 'adm_edit_expense_total', 'adm_edit_expense_terbilang')">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Biaya
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
                            <div class="mb-2 p-2 rounded-2 bg-light border border-primary-subtle shadow-sm mt-2">
                                <label class="form-label small fw-bold text-primary mb-1 d-flex align-items-center justify-content-between">
                                    <span><i class="bi bi-person-check-fill me-1"></i> PILIH PEGAWAI PPK (PENANDATANGAN)</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-normal" style="font-size: 0.65rem;">Otomatis Nama &amp; NIP</span>
                                </label>
                                <select id="adm_edit_ppk_select" class="form-select form-select-sm" onchange="onAdminReportPpkSelected(this)">
                                    <option value="">-- Pilih Pegawai BPS atau Ketik Manual --</option>
                                    @foreach((\App\Models\User::getPegawaiSigners()) as $p)
                                        <option value="{{ $p['name'] }}" data-name="{{ $p['name'] }}" data-nip="{{ $p['nip'] }}">
                                            {{ $p['name'] }} {{ $p['nip'] ? '('.$p['nip'].')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-2 mt-1">
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
                            <i class="bi bi-pen text-primary me-1"></i> PENGESAHAN &amp; LAMPIRAN OPSIONAL
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
                            <i class="bi bi-camera text-primary me-1"></i> DOKUMENTASI FOTO TERSIMPAN
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
                        <button type="submit" name="after_action" value="save" class="btn btn-bps-orange">
                            <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                        </button>
                        <button type="submit" name="after_action" value="approve" class="btn btn-success fw-semibold">
                            <i class="bi bi-check2-all me-1"></i> Simpan &amp; ACC Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Form Tersembunyi untuk Eksekusi Aksi Massal Checkbox Tabel Laporan -->
<form id="bulkActionForm" method="POST" action="{{ route('admin.reports.bulk-action') }}" class="d-none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <div id="bulkActionReportIdsContainer"></div>
</form>
@endsection

@push('scripts')
<script>
    // ==============================================================
    // Aksi Massal (Checkbox Multi-Select Laporan Petugas)
    // ==============================================================
    function toggleSelectAllReports(master) {
        const checkboxes = document.querySelectorAll('.report-row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = master.checked;
            const tr = cb.closest('tr');
            if (tr) {
                if (master.checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            }
        });
        onReportRowCheckboxChange();
    }

    function onReportRowCheckboxChange() {
        const allCheckboxes = document.querySelectorAll('.report-row-checkbox');
        const checkedBoxes = document.querySelectorAll('.report-row-checkbox:checked');
        const count = checkedBoxes.length;
        const bar = document.getElementById('bulkActionBar');
        const badge = document.getElementById('bulkSelectedCountBadge');
        const master = document.getElementById('selectAllReportsCheckbox');

        allCheckboxes.forEach(cb => {
            const tr = cb.closest('tr');
            if (tr) {
                if (cb.checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            }
        });

        if (badge) badge.textContent = `${count} Laporan Dipilih`;

        if (count > 0) {
            if (bar) {
                bar.classList.remove('d-none');
                bar.classList.add('d-flex');
            }
        } else {
            if (bar) {
                bar.classList.add('d-none');
                bar.classList.remove('d-flex');
            }
            if (master) master.checked = false;
        }
    }

    function clearAllSelectedReports() {
        document.querySelectorAll('.report-row-checkbox').forEach(cb => {
            cb.checked = false;
            const tr = cb.closest('tr');
            if (tr) tr.classList.remove('row-selected');
        });
        const master = document.getElementById('selectAllReportsCheckbox');
        if (master) master.checked = false;
        onReportRowCheckboxChange();
    }

    function submitBulkAction(actionType) {
        const selected = Array.from(document.querySelectorAll('.report-row-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Pilih minimal satu laporan terlebih dahulu.');
            return;
        }

        const labels = {
            'approve': 'ACC / Setujui ' + selected.length + ' laporan perjalanan dinas terpilih secara serentak?',
            'delete': 'Hapus ' + selected.length + ' laporan perjalanan dinas terpilih? Data akan diarsipkan menggunakan soft delete.'
        };

        const confirmMsg = labels[actionType] || 'Jalankan tindakan ini pada ' + selected.length + ' laporan terpilih?';
        if (!confirm(confirmMsg)) {
            return;
        }

        const form = document.getElementById('bulkActionForm');
        const actionInput = document.getElementById('bulkActionInput');
        const container = document.getElementById('bulkActionReportIdsContainer');

        actionInput.value = actionType;
        container.innerHTML = '';
        selected.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'report_ids[]';
            hidden.value = id;
            container.appendChild(hidden);
        });

        form.submit();
    }
</script>
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
                const mediaUrl = '/media/' + String(att.file_path || '').replace(/^\/+/, '');
                if (isImg) {
                    const a = document.createElement('a');
                    a.href = mediaUrl;
                    a.target = '_blank';
                    const img = document.createElement('img');
                    img.src = mediaUrl;
                    img.alt = att.file_name || 'Foto Dokumentasi';
                    img.className = 'img-fluid rounded mb-2';
                    img.style.maxHeight = '110px';
                    img.style.objectFit = 'cover';
                    a.appendChild(img);
                    card.appendChild(a);
                } else {
                    const pdfIcon = document.createElement('div');
                    pdfIcon.className = 'py-3';
                    pdfIcon.innerHTML = '<i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>';
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
                    mapLink.innerHTML = '<i class="bi bi-geo-alt me-1"></i> Lihat Peta';
                    meta.appendChild(mapLink);
                }
                card.appendChild(meta);

                const openBtn = document.createElement('a');
                openBtn.href = mediaUrl;
                openBtn.target = '_blank';
                openBtn.className = 'btn btn-sm btn-link text-decoration-none mt-1';
                openBtn.innerHTML = '<i class="bi bi-box-arrow-up-right me-1"></i> Buka File';
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

    function onAdminReportPpkSelected(selectEl) {
        if (!selectEl) return;
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || !opt.value) return;

        const name = opt.getAttribute('data-name') || opt.value;
        const nip = opt.getAttribute('data-nip') || '';

        const nameInput = document.getElementById('adm_edit_ppk_name');
        const nipInput = document.getElementById('adm_edit_ppk_nip');

        if (nameInput) nameInput.value = name;
        if (nipInput) nipInput.value = nip;
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
        if (document.getElementById('adm_edit_ppk_select')) {
            document.getElementById('adm_edit_ppk_select').value = rep.ppk_name || '';
        }
        document.getElementById('adm_edit_signing_city').value = rep.signing_city || 'Lasusua';
        document.getElementById('adm_edit_signing_date').value = rep.resolved_signing_date || (rep.signing_date ? rep.signing_date.substring(0, 10) : '');
        document.getElementById('adm_edit_vehicle_stmt').checked = (rep.include_vehicle_statement === true || rep.include_vehicle_statement === 1);
        document.getElementById('adm_edit_admin_notes').value = rep.admin_notes || '';

        const sigBox = document.getElementById('adm_edit_current_sig_container');
        const sigImg = document.getElementById('adm_edit_current_sig_img');
        if (rep.signature_path) {
            sigBox.classList.remove('d-none');
            sigImg.src = storageMediaUrl(rep.signature_path);
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
            @if(auth()->user()->isAdmin() && ($autoOpenMode ?? 'view') === 'edit')
                openAdminEditModal(autoRep);
            @else
                openViewReportModal(autoRep);
            @endif
        });
    @endif
</script>
@include('reports._shared_js')
@endpush

