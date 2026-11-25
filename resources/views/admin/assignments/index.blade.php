@extends('layouts.app')

@section('title', 'Kegiatan & Penugasan')
@section('page_title', 'Kegiatan & Penugasan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kegiatan &amp; Penugasan</li>
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

/* Fixed Bento Table Card (Compact / Zoom-out & Anti-Geser) */
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
.bento-action-btn.btn-view {
    color: #2563EB;
}
.bento-action-btn.btn-view:hover {
    background: #EFF6FF;
    border-color: #BFDBFE;
}
.bento-action-btn.btn-edit {
    color: #D97706;
}
.bento-action-btn.btn-edit:hover {
    background: #FFFBEB;
    border-color: #FDE68A;
}
.bento-action-btn.btn-delete {
    color: #DC2626;
}
.bento-action-btn.btn-delete:hover {
    background: #FEF2F2;
    border-color: #FECACA;
}

/* Mobile Responsive: Stack without scroll */
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
@if(auth()->user()->isAdmin())
<!-- Panel Kontrol Visibilitas Penugasan Lapangan (Admin) -->
<div class="bento-card-clean p-3.5 mb-3 border" style="{{ $globalAssignmentEnabled ? 'border-color: #86EFAC !important; background: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%);' : 'border-color: #FECACA !important; background: linear-gradient(135deg, #FFFFFF 0%, #FEF2F2 100%);' }}">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-4 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 48px; height: 48px; background: {{ $globalAssignmentEnabled ? '#16A34A' : '#DC2626' }}; box-shadow: 0 4px 12px {{ $globalAssignmentEnabled ? 'rgba(22, 163, 74, 0.25)' : 'rgba(220, 38, 38, 0.25)' }};">
                <i class="bi {{ $globalAssignmentEnabled ? 'bi-broadcast' : 'bi-eye-slash-fill' }} fs-4"></i>
            </div>
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">Kontrol Tampil Penugasan Lapangan (Dashboard Petugas)</span>
                    @if($globalAssignmentEnabled)
                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-check-circle-fill me-1"></i> Status Global: AKTIF
                        </span>
                    @else
                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-lock-fill me-1"></i> Status Global: NONAKTIF
                        </span>
                    @endif
                </div>
                <div class="text-secondary small" style="font-size: 0.78rem;">
                    @if($globalAssignmentEnabled)
                        Penugasan muncul ke petugas jika <strong>Aktif Manual (Admin)</strong> ATAU <strong>Periode Sedang Aktif</strong>.
                        (<span class="fw-semibold text-success">{{ $totalVisibleToOfficers }} tampil ke petugas</span> &bull; {{ $totalManualActive }} Aktif Manual &bull; {{ $totalPeriodActive }} Aktif Periode)
                    @else
                        <strong>Seluruh penugasan sedang disembunyikan</strong> dari dashboard &amp; portal petugas. Aktifkan kembali untuk menampilkan sesuai aturan sistem.
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="POST" action="{{ route('admin.assignments.global-toggle') }}" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="enabled" value="1">
                <button type="submit" class="btn btn-sm {{ $globalAssignmentEnabled ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3 py-1.5 fw-semibold" {{ $globalAssignmentEnabled ? 'disabled' : '' }}>
                    <i class="bi bi-play-circle-fill me-1"></i> Aktifkan Penugasan
                </button>
            </form>
            <form method="POST" action="{{ route('admin.assignments.global-toggle') }}" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="enabled" value="0">
                <button type="submit" class="btn btn-sm {{ !$globalAssignmentEnabled ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 py-1.5 fw-semibold" {{ !$globalAssignmentEnabled ? 'disabled' : '' }}>
                    <i class="bi bi-pause-circle-fill me-1"></i> Nonaktifkan Penugasan
                </button>
            </form>
            <a href="{{ route('admin.assignments.history') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold">
                <i class="bi bi-clock-history me-1"></i> Riwayat Penugasan
            </a>
        </div>
    </div>
</div>
@else
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.assignments.history') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold">
        <i class="bi bi-clock-history me-1"></i> Riwayat Penugasan
    </a>
</div>
@endif

<!-- Bento Search & Filter Card -->
<div class="bento-card-clean p-3 mb-3">
    <form method="GET" action="{{ route('admin.assignments.index') }}" class="row g-2 align-items-center">
        <div class="col-lg-5 col-md-12">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       name="search" 
                       class="form-control border-start-0 ps-1" 
                       value="{{ request('search') }}" 
                       placeholder="Cari judul kegiatan, lokasi wilayah, atau uraian deskripsi...">
            </div>
        </div>

        <div class="col-lg-2 col-md-4">
            <select name="status" class="form-select">
                <option value="">-- Pilih Status --</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Sedang Berjalan</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="col-lg-3 col-md-4">
            <select name="visibility_status" class="form-select">
                <option value="">-- Status Tampil (Petugas) --</option>
                <option value="manual_active" {{ request('visibility_status') === 'manual_active' ? 'selected' : '' }}>Aktif Manual (Admin)</option>
                <option value="period_active" {{ request('visibility_status') === 'period_active' ? 'selected' : '' }}>Aktif Periode</option>
                <option value="inactive" {{ request('visibility_status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                <option value="ended" {{ request('visibility_status') === 'ended' ? 'selected' : '' }}>Berakhir</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-bps-orange flex-fill justify-content-center py-2">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'visibility_status']))
                <a href="{{ route('admin.assignments.index') }}" class="btn btn-outline-secondary rounded-pill px-3 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Bento Table Card (Compact, Zoom-out, Anti-Geser) -->
<div class="bento-table-card mb-4">
    <div class="bento-table-header">
        <div>
            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">
                Daftar Kegiatan &amp; Penugasan Lapangan
            </h6>
            <span class="text-muted" style="font-size: 0.7rem;">Total {{ $assignments->total() }} kegiatan terdaftar dalam sistem</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                Halaman {{ $assignments->currentPage() }} dari {{ max(1, $assignments->lastPage()) }}
            </span>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn btn-outline-success shadow-sm py-1.5 px-3 rounded-pill" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Import Excel Kegiatan &amp; Sampel
                </button>
                <button type="button" class="btn-bps-orange shadow-sm py-1.5 px-3" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                    <i class="bi bi-calendar-plus-fill me-1"></i> Buat Kegiatan Baru
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
                    <span id="bulkSelectedCountBadge">0 Kegiatan Dipilih</span>
                </span>
                <span class="text-muted small">|</span>
                <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none p-0 d-inline-flex align-items-center gap-1" onclick="clearAllSelectedAssignments()" style="font-size: 0.78rem;">
                    <i class="bi bi-x-circle text-muted"></i> Batal Pilih
                </button>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn-bulk-action" onclick="submitBulkAction('set_ongoing')" title="Ubah status seluruh kegiatan yang dicentang menjadi Sedang Berjalan (Aktif)">
                    <i class="bi bi-play-circle text-primary fs-6"></i> Set Berjalan
                </button>
                <button type="button" class="btn-bulk-action" onclick="submitBulkAction('set_completed')" title="Ubah status seluruh kegiatan yang dicentang menjadi Selesai (Tuntas)">
                    <i class="bi bi-check2-circle text-success fs-6"></i> Set Selesai
                </button>
                <button type="button" class="btn-bulk-action" onclick="submitBulkAction('set_draft')" title="Ubah status seluruh kegiatan yang dicentang menjadi Draft">
                    <i class="bi bi-file-earmark-text text-secondary fs-6"></i> Set Draft
                </button>
                <button type="button" class="btn-bulk-action" onclick="openBulkAssignModal()" title="Tugaskan petugas/pegawai ke seluruh kegiatan yang dicentang sekaligus">
                    <i class="bi bi-person-plus text-info fs-6"></i> Tugaskan Petugas / Pegawai
                </button>
                @if(auth()->user()->isAdmin())
                    <button type="button" class="btn-bulk-action" onclick="submitBulkAction('enable_admin')" title="Aktifkan penugasan secara manual untuk seluruh item terpilih">
                        <i class="bi bi-toggle-on text-success fs-6"></i> Aktifkan Manual
                    </button>
                    <button type="button" class="btn-bulk-action" onclick="submitBulkAction('disable_admin')" title="Nonaktifkan aktif manual untuk seluruh item terpilih">
                        <i class="bi bi-toggle-off text-secondary fs-6"></i> Nonaktifkan Manual
                    </button>
                    <button type="button" class="btn-bulk-action-danger" onclick="submitBulkAction('delete')" title="Hapus seluruh kegiatan yang dicentang">
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
                            <input type="checkbox" id="selectAllAssignmentsCheckbox" class="form-check-input" onchange="toggleSelectAllAssignments(this)" title="Pilih Semua Kegiatan di Halaman Ini">
                        </th>
                    @endif
                    <th style="width: 21%;"><i class="bi bi-journal-text me-1 text-muted"></i> Kegiatan</th>
                    <th style="width: 15%;"><i class="bi bi-calendar3 me-1 text-muted"></i> Periode</th>
                    <th style="width: 12%;"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> Wilayah</th>
                    <th class="text-center" style="width: 7%;"><i class="bi bi-people-fill me-1 text-primary"></i> Petugas</th>
                    <th class="text-center" style="width: 9%;"><i class="bi bi-pie-chart-fill me-1 text-success"></i> Sampel</th>
                    <th class="text-center" style="width: 15%;"><i class="bi bi-activity me-1 text-warning"></i> Status Tampil</th>
                    <th class="text-end pe-3" style="width: 17%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $item)
                    <tr>
                        @if(auth()->user()->canAccessMonitoring())
                            <td class="text-center" data-label="Pilih">
                                <input type="checkbox" 
                                       name="assignment_ids[]" 
                                       value="{{ $item->id }}" 
                                       class="form-check-input assignment-row-checkbox" 
                                       data-title="{{ $item->title }}"
                                       onchange="onAssignmentRowCheckboxChange()">
                            </td>
                        @endif
                        <td>
                            <a href="{{ route('admin.assignments.show', $item) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate mb-0.5" style="font-size: 0.84rem;" title="{{ $item->title }}">
                                {{ $item->title }}
                            </a>
                            <div class="text-muted text-truncate" style="font-size: 0.7rem;" title="{{ $item->description }}">
                                {{ $item->description ?: 'Tidak ada deskripsi tambahan.' }}
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark text-truncate d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                <i class="bi bi-calendar-event text-secondary flex-shrink-0"></i>
                                <span class="text-truncate">{{ $item->start_date->format('d/m/y') }} &ndash; {{ $item->end_date->format('d/m/y') }}</span>
                            </div>
                            <div class="mt-0.5">
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                    {{ $item->start_date->diffInDays($item->end_date) + 1 }} Hari
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark text-truncate d-flex align-items-center gap-1" style="font-size: 0.76rem;" title="{{ $item->location }}">
                                <i class="bi bi-geo-alt-fill text-danger flex-shrink-0"></i>
                                <span class="text-truncate">{{ $item->location }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill text-truncate" style="font-size: 0.7rem;">
                                <i class="bi bi-people-fill me-0.5"></i> {{ $item->officers_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-1.5 py-0.5 rounded-pill font-monospace" style="font-size: 0.68rem;">
                                {{ $item->respondents_count }}@if($item->target_respondents > 0)<span class="text-muted">/{{ number_format($item->target_respondents) }}</span>@endif
                            </span>
                            @if($item->target_respondents > 0)
                                @php
                                    $pct = round(($item->respondents_count / $item->target_respondents) * 100);
                                @endphp
                                <div class="progress mt-1 mx-auto" style="height: 4px; width: 48px; border-radius: 999px;" title="{{ $pct }}% dari target {{ number_format($item->target_respondents) }} responden">
                                    <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning') }}" style="width: {{ min(100, $pct) }}%;"></div>
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $visStatus = $item->visibilityStatus();
                                $visLabel = $item->visibility_status_label;
                                $visBadgeClass = $item->visibility_status_badge_class;
                            @endphp
                            <span class="badge {{ $visBadgeClass }} px-2 py-0.5 rounded-pill d-inline-block mb-1" style="font-size: 0.68rem;">
                                @if($visStatus === 'manual_active')
                                    <i class="bi bi-toggle-on me-0.5"></i>
                                @elseif($visStatus === 'period_active')
                                    <i class="bi bi-calendar-check me-0.5"></i>
                                @elseif($visStatus === 'ended')
                                    <i class="bi bi-calendar-x me-0.5"></i>
                                @else
                                    <i class="bi bi-pause-circle me-0.5"></i>
                                @endif
                                {{ $visLabel }}
                            </span>
                            @if(auth()->user()->isAdmin())
                                <div>
                                    <form method="POST" action="{{ route('admin.assignments.toggle-admin', $item) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-xs {{ $item->is_enabled_by_admin ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill py-0 px-2" style="font-size: 0.64rem;" title="{{ $item->is_enabled_by_admin ? 'Nonaktifkan kontrol manual Admin' : 'Aktifkan penugasan secara manual (ON)' }}">
                                            {{ $item->is_enabled_by_admin ? 'ON (Manual)' : 'OFF (Periode)' }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-inline-flex gap-1 align-items-center justify-content-end">
                                <!-- Detail & Pembagian Petugas -->
                                <a href="{{ route('admin.assignments.show', $item) }}" 
                                   class="bento-action-btn btn-view" 
                                   title="Detail &amp; Pembagian Tugas" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-list-check"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <!-- Atur Periode Penugasan -->
                                    <button type="button" 
                                            class="bento-action-btn text-primary" 
                                            title="Atur Periode &amp; Kontrol Admin" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditPeriodModal({{ json_encode($item) }})">
                                        <i class="bi bi-calendar-range"></i>
                                    </button>

                                    <!-- Edit -->
                                    <button type="button" 
                                            class="bento-action-btn btn-edit" 
                                            title="Edit Data Kegiatan" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditAssignmentModal({{ json_encode($item) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <button type="button" 
                                            class="bento-action-btn btn-delete" 
                                            title="Hapus Kegiatan" 
                                            data-bs-toggle="tooltip"
                                            onclick="openDeleteAssignmentModal({{ $item->id }}, '{{ addslashes($item->title) }}')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 8 : 7 }}" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x display-5 text-secondary opacity-50 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Belum ada kegiatan ditemukan.</div>
                            <div class="small">Klik tombol "Buat Kegiatan Baru" untuk memulai alokasi penugasan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($assignments->hasPages())
        <div class="p-3 bg-white border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Menampilkan {{ $assignments->firstItem() }} sampai {{ $assignments->lastItem() }} dari {{ $assignments->total() }} kegiatan
            </div>
            <div>
                {{ $assignments->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@if(auth()->user()->isAdmin())
<!-- ==============================================================
     MODAL BUAT KEGIATAN BARU
     ============================================================== -->
<div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #F37021; width: 32px; height: 32px;">
                        <i class="bi bi-calendar-plus-fill"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="createAssignmentModalLabel" style="font-size: 1rem;">
                        Buat Kegiatan &amp; Penugasan Baru
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.assignments.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Upload File Excel (Auto-Fill & Import Sampel) -->
                    <div class="p-3 mb-3 rounded-4 border" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1.5px dashed #10b981 !important;">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <label class="form-label small fw-bold text-success mb-0 d-flex align-items-center gap-1.5">
                                <i class="bi bi-file-earmark-excel-fill fs-5 text-success"></i>
                                <span>Upload File Excel / Spreadsheet (Auto-Fill &amp; Import Sampel)</span>
                            </label>
                            <div class="d-flex align-items-center gap-1.5">
                                <a href="{{ route('admin.assignments.template.download', ['format' => 'xlsx']) }}" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-0.5 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;" title="Unduh Template Excel Kosong (.xlsx)">
                                    <i class="bi bi-file-earmark-excel-fill"></i>
                                    <span>Unduh Template (.xlsx)</span>
                                </a>
                                <a href="{{ route('admin.assignments.template.download', ['format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;" title="Unduh Template CSV Kosong (.csv)">
                                    <i class="bi bi-filetype-csv"></i>
                                    <span>(.csv)</span>
                                </a>
                            </div>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="file" id="modalCreateExcelFile" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" onchange="previewAndAutoFillExcel()">
                            <button type="button" class="btn btn-success px-3 fw-semibold" id="btnPreviewExcel" onclick="previewAndAutoFillExcel()">
                                <i class="bi bi-magic me-1"></i> Baca File Excel
                            </button>
                        </div>
                        <div class="form-text text-muted mt-1.5" style="font-size: 0.72rem;">
                            <i class="bi bi-info-circle-fill text-success me-1"></i>Pilih file Excel (<code>.xlsx</code> / <code>.csv</code>). Sistem akan otomatis membaca judul kegiatan, DIPA, wilayah tugas, target kuota sampel, dan mencentang nama petugas.
                        </div>

                        <!-- Indicator Loading -->
                        <div id="excelPreviewLoading" class="mt-2.5 d-none align-items-center gap-2 small">
                            <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                            <span class="text-dark fw-medium" style="font-size: 0.78rem;">Sedang membaca dan menganalisis berkas Excel...</span>
                        </div>

                        <!-- Indicator Feedback Banner -->
                        <div id="excelPreviewAlert" class="mt-2.5 d-none alert alert-success py-2 px-3 mb-0 rounded-3 small border-0 shadow-sm" style="font-size: 0.78rem; background-color: #d1fae5; color: #065f46;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <div>
                                    <strong id="excelPreviewHeading">Berkas Excel Berhasil Dibaca!</strong>
                                    <div id="excelPreviewText" class="mt-0.5"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Indicator Error Banner -->
                        <div id="excelPreviewError" class="mt-2.5 d-none alert alert-danger py-2 px-3 mb-0 rounded-3 small border-0 shadow-sm" style="font-size: 0.78rem;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-6 mt-0.5"></i>
                                <div>
                                    <strong>Gagal Membaca File:</strong>
                                    <div id="excelPreviewErrorText" class="mt-0.5"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">NAMA / JUDUL KEGIATAN <span class="text-danger">*</span></label>
                        <input type="text" id="create_title" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Pendataan Lapangan Profil Responden Pertanian 2026" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" id="create_start_date" name="start_date" class="form-control" value="{{ old('start_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" id="create_end_date" name="end_date" class="form-control" value="{{ old('end_date', now()->addDays(14)->toDateString()) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-dark mb-1">LOKASI / WILAYAH PELAKSANAAN <span class="text-danger">*</span></label>
                            <input type="text" id="create_location" name="location" class="form-control" value="{{ old('location') }}" placeholder="Contoh: Kecamatan Sukamaju & Sekitarnya" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">TARGET RESPONDEN (KUOTA)</label>
                            <div class="input-group">
                                <input type="number" id="create_target_respondents" name="target_respondents" class="form-control" value="{{ old('target_respondents', 100) }}" min="0" placeholder="Contoh: 680">
                                <span class="input-group-text bg-light small text-muted">Sampel</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark mb-1">STATUS KEGIATAN <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="ongoing" selected>Sedang Berjalan</option>
                                <option value="draft">Draft / Rencana</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>
                    </div>

                    @include('admin.partials._basis_fields', ['prefix' => 'create_asgn', 'defaults' => $basisDefaults, 'required' => false])

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">URAIAN / DESKRIPSI KEGIATAN</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan tujuan, sasaran responden, dan instruksi umum kegiatan..."></textarea>
                    </div>

                    <div class="p-3 mb-3 rounded-4 border bg-light-subtle">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_enabled_by_admin" value="1" id="create_is_enabled_by_admin">
                            <label class="form-check-label small fw-bold text-dark" for="create_is_enabled_by_admin">
                                Aktifkan Secara Manual (Admin ON)
                            </label>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.72rem;">
                            Jika diaktifkan (ON), penugasan langsung tampil ke petugas meskipun di luar rentang tanggal mulai–selesai. Jika OFF, penugasan tampil otomatis hanya saat periode tanggal aktif.
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-4 border">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <label class="form-label small fw-bold text-dark mb-0">
                                <i class="bi bi-people-fill text-primary me-1"></i> PILIH PETUGAS AWAL (OPSIONAL)
                            </label>
                            <div class="d-flex align-items-center gap-1.5">
                                <input type="text" id="officerSearchInput" class="form-control form-control-sm py-1 px-2.5 rounded-pill" placeholder="Cari nama petugas..." style="width: 170px; font-size: 0.75rem;" onkeyup="filterOfficers(this.value)">
                                <button type="button" class="btn btn-outline-primary btn-sm py-0.5 px-2.5 rounded-pill" style="font-size: 0.72rem;" onclick="toggleAllOfficers(true)">Semua</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-2.5 rounded-pill" style="font-size: 0.72rem;" onclick="toggleAllOfficers(false)">Batal</button>
                            </div>
                        </div>
                        <div class="row g-2" id="officersContainer" style="max-height: 160px; overflow-y: auto;">
                            @forelse($officers as $officer)
                                <div class="col-md-6 officer-check-item" data-name="{{ strtolower($officer->name) }} {{ strtolower($officer->phone ?? '') }}">
                                    <div class="form-check">
                                        <input class="form-check-input officer-checkbox" type="checkbox" name="officer_ids[]" value="{{ $officer->id }}" id="officer_{{ $officer->id }}">
                                        <label class="form-check-label small" for="officer_{{ $officer->id }}">
                                            <strong>{{ $officer->name }}</strong> <span class="text-muted">({{ $officer->phone ?? $officer->username }})</span>
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted small">Belum ada petugas aktif terdaftar.</div>
                            @endforelse
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                            * Anda juga dapat menentukan rincian tugas spesifik untuk setiap petugas setelah kegiatan dibuat.
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-bps-orange">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Kegiatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

                    @php
                        $modalAssignments = $assignments ?? \App\Models\Assignment::orderBy('title')->get();
                        $modalAgendas = $agendas ?? \App\Models\Schedule::orderBy('start_time', 'desc')->get();
                    @endphp

                    <!-- Pilihan Kegiatan & Agenda (Wajib) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                PILIH KEGIATAN <span class="text-danger">*</span>
                            </label>
                            <select name="assignment_id" id="import_asgn_assignment" class="form-select" required>
                                <option value="">-- Pilih Kegiatan Sensus / Survei * --</option>
                                @foreach($modalAssignments as $asgn)
                                    <option value="{{ $asgn->id }}">
                                        {{ $asgn->title }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text" style="font-size: 0.72rem;">Wajib dipilih untuk menentukan kegiatan dari data responden.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                PILIH AGENDA <span class="text-danger">*</span>
                            </label>
                            <select name="schedule_id" id="import_asgn_schedule" class="form-select" required>
                                <option value="">-- Pilih Agenda / Jadwal Kegiatan * --</option>
                                @foreach($modalAgendas as $ag)
                                    <option value="{{ $ag->id }}" data-assignment="{{ $ag->assignment_id }}">
                                        {{ $ag->title }} @if($ag->start_time)({{ \Carbon\Carbon::parse($ag->start_time)->format('d M Y') }})@endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text" style="font-size: 0.72rem;">Wajib dipilih untuk menautkan data dengan agenda jadwal lapangan.</div>
                        </div>
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

<!-- ==============================================================
     MODAL EDIT KEGIATAN
     ============================================================== -->
<div class="modal fade" id="editAssignmentModal" tabindex="-1" aria-labelledby="editAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #D97706; width: 32px; height: 32px;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="editAssignmentModalLabel" style="font-size: 1rem;">
                        Edit Data Kegiatan
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAssignmentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">NAMA / JUDUL KEGIATAN <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title" name="title" class="form-control" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" id="edit_start_date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" id="edit_end_date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-dark mb-1">LOKASI / WILAYAH PELAKSANAAN <span class="text-danger">*</span></label>
                            <input type="text" id="edit_location" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">TARGET RESPONDEN (KUOTA)</label>
                            <div class="input-group">
                                <input type="number" id="edit_target_respondents" name="target_respondents" class="form-control" min="0" placeholder="Contoh: 680">
                                <span class="input-group-text bg-light small text-muted">Sampel</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark mb-1">STATUS KEGIATAN <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="ongoing">Sedang Berjalan</option>
                                <option value="completed">Selesai</option>
                                <option value="draft">Draft</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">URAIAN / DESKRIPSI KEGIATAN</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="p-3 mb-3 rounded-4 border bg-light-subtle">
                        <input type="hidden" name="is_enabled_by_admin" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_enabled_by_admin" value="1" id="edit_is_enabled_by_admin">
                            <label class="form-check-label small fw-bold text-dark" for="edit_is_enabled_by_admin">
                                Aktifkan Secara Manual (Admin ON)
                            </label>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.72rem;">
                            Centang untuk menampilkan penugasan secara manual ke dashboard petugas meskipun di luar periode tanggal.
                        </div>
                    </div>

                    @include('admin.partials._basis_fields', ['prefix' => 'edit_asgn', 'defaults' => [], 'required' => false])
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-bps-orange">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL HAPUS KEGIATAN
     ============================================================== -->
<div class="modal fade" id="deleteAssignmentModal" tabindex="-1" aria-labelledby="deleteAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill display-4"></i>
                </div>
                <h5 class="fw-bold mb-2 text-dark">Hapus Kegiatan?</h5>
                <p class="text-muted small mb-4">
                    Apakah Anda yakin ingin menghapus kegiatan <strong id="delete_assignment_title" class="text-dark"></strong>? Seluruh data penugasan terkait akan diarsipkan menggunakan soft delete.
                </p>
                <form id="deleteAssignmentForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL TUGASKAN PETUGAS MASSAL (MULTI-ASSIGN)
     ============================================================== -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="bulkAssignModalLabel">
                    <i class="bi bi-people text-primary"></i> Tugaskan Petugas & Pegawai Sekaligus
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.assignments.bulk-action') }}">
                @csrf
                <input type="hidden" name="action" value="assign_officers">
                <div id="bulkAssignModalIdsContainer"></div>

                <div class="modal-body px-4 py-3">
                    <div class="alert alert-info py-2 px-3 small border-0 d-flex align-items-center gap-2 mb-3" style="border-radius: 12px; background: #eff6ff; color: #1e40af;">
                        <i class="bi bi-info-circle-fill fs-6 flex-shrink-0"></i>
                        <div>Petugas & Pegawai yang dipilih akan ditugaskan ke <strong id="bulkAssignSelectedCountText">0 kegiatan</strong> terpilih.</div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <label class="form-label small fw-bold text-dark mb-0">PILIH PETUGAS & PEGAWAI LAPANGAN <span class="text-danger">*</span></label>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2" style="font-size: 0.72rem;" onclick="toggleAllBulkOfficers(true)">Pilih Semua</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.72rem;" onclick="toggleAllBulkOfficers(false)">Batal</button>
                            </div>
                        </div>

                        <!-- Filter & Search box -->
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="bulkOfficerSearchInput" class="form-control" placeholder="Cari nama pegawai atau petugas..." onkeyup="filterBulkOfficers(this.value)">
                        </div>

                        <!-- Scrollable List of Officers -->
                        <div class="border rounded-3 p-2 bg-white" style="max-height: 220px; overflow-y: auto;">
                            @foreach($officers as $officer)
                                @php
                                    $isPeg = $officer->role === 'pegawai';
                                    $badgeClass = $isPeg ? 'bg-primary-subtle text-primary border-primary-subtle' : 'bg-warning-subtle text-warning-emphasis border-warning-subtle';
                                    $roleText = $isPeg ? 'Pegawai BPS' : 'Petugas Lapangan';
                                @endphp
                                <div class="form-check py-1.5 px-3 border-bottom border-light bulk-officer-item d-flex align-items-center gap-2" data-name="{{ strtolower($officer->name) }}">
                                    <input class="form-check-input bulk-officer-checkbox" type="checkbox" name="officer_ids[]" value="{{ $officer->id }}" id="bulk_officer_{{ $officer->id }}">
                                    <label class="form-check-label w-100 cursor-pointer small d-flex justify-content-between align-items-center" for="bulk_officer_{{ $officer->id }}">
                                        <span class="fw-semibold text-dark">{{ $officer->name }}</span>
                                        <div class="d-flex align-items-center gap-1">
                                            @if($officer->district)
                                                <span class="text-muted" style="font-size: 0.68rem;"><i class="bi bi-geo-alt"></i> {{ $officer->district }}</span>
                                            @endif
                                            <span class="badge border rounded-pill {{ $badgeClass }}" style="font-size: 0.65rem;">
                                                {{ $roleText }}
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">TUGAS SPESIFIK / PERAN</label>
                        <input type="text" name="specific_task" class="form-control" placeholder="Contoh: Pencacahan Lapangan, Pengawasan Lapangan, dsb." value="Pencacahan Lapangan">
                        <div class="form-text small">Tugas ini akan dicatat untuk setiap alokasi penugasan yang dibuat.</div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-2.5 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-1.5 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL ATUR PERIODE & KONTROL ADMIN PENUGASAN
     ============================================================== -->
<div class="modal fade" id="editPeriodModal" tabindex="-1" aria-labelledby="editPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="editPeriodModalLabel">
                    <i class="bi bi-calendar-range text-primary"></i> Atur Periode &amp; Kontrol Penugasan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPeriodForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">NAMA PENUGASAN</label>
                        <input type="text" id="period_title" class="form-control bg-light" readonly disabled>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" id="period_start_date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" id="period_end_date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="p-3 rounded-4 border bg-light-subtle">
                        <input type="hidden" name="is_enabled_by_admin" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_enabled_by_admin" value="1" id="period_is_enabled_by_admin">
                            <label class="form-check-label small fw-bold text-dark" for="period_is_enabled_by_admin">
                                Aktifkan Manual oleh Admin (ON)
                            </label>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.72rem;">
                            Penugasan tampil ke Petugas jika Admin ON <strong>ATAU</strong> tanggal hari ini berada di dalam rentang periode di atas.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-2.5">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-bps-orange">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Tersembunyi untuk Eksekusi Aksi Massal Checkbox Tabel -->
<form id="bulkActionForm" method="POST" action="{{ route('admin.assignments.bulk-action') }}" class="d-none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <div id="bulkActionAssignmentIdsContainer"></div>
</form>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    const ADMIN_BASIS_DEFAULTS = @json($basisDefaults);

    function openEditAssignmentModal(item) {
        document.getElementById('editAssignmentForm').action = '/admin/assignments/' + item.id;
        document.getElementById('edit_title').value = item.title;
        document.getElementById('edit_start_date').value = item.start_date.substring(0, 10);
        document.getElementById('edit_end_date').value = item.end_date.substring(0, 10);
        document.getElementById('edit_location').value = item.location;
        document.getElementById('edit_target_respondents').value = item.target_respondents || 0;
        document.getElementById('edit_status').value = item.status;
        document.getElementById('edit_description').value = item.description || '';
        const editAdminCb = document.getElementById('edit_is_enabled_by_admin');
        if (editAdminCb) editAdminCb.checked = Boolean(item.is_enabled_by_admin);

        document.getElementById('edit_asgn_basis_dipa').value = item.basis_dipa || ADMIN_BASIS_DEFAULTS.basis_dipa || '';
        document.getElementById('edit_asgn_st_number').value = item.st_number || ADMIN_BASIS_DEFAULTS.st_number || '';
        document.getElementById('edit_asgn_st_date').value = item.st_date ? item.st_date.substring(0, 10) : (ADMIN_BASIS_DEFAULTS.st_date || '');
        if (item.basis_st) {
            document.getElementById('edit_asgn_basis_st').value = item.basis_st;
        } else {
            syncAdminBasisSt('edit_asgn');
        }

        const modal = new bootstrap.Modal(document.getElementById('editAssignmentModal'));
        modal.show();
    }

    function toggleAllOfficers(selectAll) {
        document.querySelectorAll('.officer-checkbox').forEach(cb => {
            const parent = cb.closest('.officer-check-item');
            if (!parent || parent.style.display !== 'none') {
                cb.checked = selectAll;
            }
        });
    }

    function filterOfficers(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.officer-check-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            if (!q || name.includes(q)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function openEditPeriodModal(item) {
        document.getElementById('editPeriodForm').action = '/admin/assignments/' + item.id + '/period';
        document.getElementById('period_title').value = item.title || '';
        document.getElementById('period_start_date').value = item.start_date ? item.start_date.substring(0, 10) : '';
        document.getElementById('period_end_date').value = item.end_date ? item.end_date.substring(0, 10) : '';
        document.getElementById('period_is_enabled_by_admin').checked = Boolean(item.is_enabled_by_admin);

        const modal = new bootstrap.Modal(document.getElementById('editPeriodModal'));
        modal.show();
    }

    function openDeleteAssignmentModal(id, title) {
        document.getElementById('deleteAssignmentForm').action = '/admin/assignments/' + id;
        document.getElementById('delete_assignment_title').innerText = title;

        const modal = new bootstrap.Modal(document.getElementById('deleteAssignmentModal'));
        modal.show();
    }

    function previewAndAutoFillExcel() {
        const fileInput = document.getElementById('modalCreateExcelFile');
        const loadingEl = document.getElementById('excelPreviewLoading');
        const alertEl = document.getElementById('excelPreviewAlert');
        const errorEl = document.getElementById('excelPreviewError');
        const alertText = document.getElementById('excelPreviewText');
        const errorText = document.getElementById('excelPreviewErrorText');

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            return;
        }

        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('excel_file', file);
        formData.append('_token', '{{ csrf_token() }}');

        loadingEl.classList.remove('d-none');
        loadingEl.classList.add('d-flex');
        alertEl.classList.add('d-none');
        errorEl.classList.add('d-none');

        fetch('{{ route('admin.assignments.preview-excel') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async (response) => {
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Terjadi kesalahan saat memproses file Excel.');
            }
            return data;
        })
        .then((data) => {
            loadingEl.classList.add('d-none');
            loadingEl.classList.remove('d-flex');

            // 1. Auto-fill form fields
            if (data.title && data.title.trim() !== '') {
                const titleEl = document.getElementById('create_title') || document.querySelector('#createAssignmentModal input[name="title"]');
                if (titleEl) titleEl.value = data.title;
            }

            if (data.location && data.location.trim() !== '') {
                const locEl = document.getElementById('create_location') || document.querySelector('#createAssignmentModal input[name="location"]');
                if (locEl) locEl.value = data.location;
            }

            if (data.total_samples && data.total_samples > 0) {
                const targetEl = document.getElementById('create_target_respondents') || document.querySelector('#createAssignmentModal input[name="target_respondents"]');
                if (targetEl) targetEl.value = data.total_samples;
            }

            if (data.basis_dipa && data.basis_dipa.trim() !== '') {
                const dipaEl = document.getElementById('create_asgn_basis_dipa') || document.querySelector('#createAssignmentModal textarea[name="basis_dipa"]');
                if (dipaEl) dipaEl.value = data.basis_dipa;
            }

            // 2. Auto-check matching officers
            let matchedCount = 0;
            if (data.matched_officer_ids && data.matched_officer_ids.length > 0) {
                data.matched_officer_ids.forEach(id => {
                    const cb = document.getElementById('officer_' + id);
                    if (cb) {
                        cb.checked = true;
                        matchedCount++;
                    }
                });
            }

            // 3. Display informative success feedback
            let summaryMsg = '';
            if (data.title && data.title.trim() !== '') {
                summaryMsg = `Kegiatan <strong>"${data.title}"</strong> terdeteksi dengan <strong>${data.total_samples} sampel responden</strong> dan <strong>${data.officers ? data.officers.length : 0} petugas lapangan</strong>. Kolom form berhasil diisi otomatis!`;
            } else {
                summaryMsg = `Berhasil membaca <strong>${data.officers ? data.officers.length : 0} petugas lapangan</strong> dari berkas Excel. Wilayah tugas dan daftar petugas berhasil dicentang otomatis!`;
            }
            
            if (matchedCount > 0) {
                summaryMsg += `<br><span class="badge bg-success-subtle text-success border border-success-subtle mt-1 me-1"><i class="bi bi-check2"></i> ${matchedCount} Petugas terdaftar otomatis dicentang</span>`;
            }
            if (data.unmatched_officers && data.unmatched_officers.length > 0) {
                summaryMsg += `<br><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1"><i class="bi bi-person-plus-fill"></i> ${data.unmatched_officers.length} Petugas baru (${data.unmatched_officers.slice(0, 3).join(', ')}${data.unmatched_officers.length > 3 ? '...' : ''}) akan otomatis dibuatkan akun saat kegiatan disimpan</span>`;
            }

            alertText.innerHTML = summaryMsg;
            alertEl.classList.remove('d-none');
        })
        .catch((err) => {
            loadingEl.classList.add('d-none');
            loadingEl.classList.remove('d-flex');
            errorText.innerText = err.message || 'Gagal membaca berkas Excel.';
            errorEl.classList.remove('d-none');
        });
    }

    // ==============================================================
    // Aksi Massal (Checkbox Multi-Select Kegiatan & Penugasan)
    // ==============================================================
    function toggleSelectAllAssignments(master) {
        const checkboxes = document.querySelectorAll('.assignment-row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = master.checked;
            const tr = cb.closest('tr');
            if (tr) {
                if (master.checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            }
        });
        onAssignmentRowCheckboxChange();
    }

    function onAssignmentRowCheckboxChange() {
        const allCheckboxes = document.querySelectorAll('.assignment-row-checkbox');
        const checkedBoxes = document.querySelectorAll('.assignment-row-checkbox:checked');
        const count = checkedBoxes.length;
        const bar = document.getElementById('bulkActionBar');
        const badge = document.getElementById('bulkSelectedCountBadge');
        const master = document.getElementById('selectAllAssignmentsCheckbox');

        allCheckboxes.forEach(cb => {
            const tr = cb.closest('tr');
            if (tr) {
                if (cb.checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            }
        });

        if (badge) badge.textContent = `${count} Kegiatan Dipilih`;

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

    function clearAllSelectedAssignments() {
        document.querySelectorAll('.assignment-row-checkbox').forEach(cb => {
            cb.checked = false;
            const tr = cb.closest('tr');
            if (tr) tr.classList.remove('row-selected');
        });
        const master = document.getElementById('selectAllAssignmentsCheckbox');
        if (master) master.checked = false;
        onAssignmentRowCheckboxChange();
    }

    function submitBulkAction(actionType) {
        const selected = Array.from(document.querySelectorAll('.assignment-row-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Pilih minimal satu kegiatan terlebih dahulu.');
            return;
        }

        const labels = {
            'set_ongoing': 'Ubah status ' + selected.length + ' kegiatan menjadi Sedang Berjalan (Aktif)?',
            'set_completed': 'Ubah status ' + selected.length + ' kegiatan menjadi Selesai (Tuntas)?',
            'set_draft': 'Ubah status ' + selected.length + ' kegiatan menjadi Draft?',
            'enable_admin': 'Aktifkan secara manual (Admin ON) untuk ' + selected.length + ' penugasan terpilih?',
            'disable_admin': 'Nonaktifkan aktif manual untuk ' + selected.length + ' penugasan terpilih?',
            'delete': 'Hapus ' + selected.length + ' kegiatan terpilih? Data akan diarsipkan menggunakan soft delete.'
        };

        const confirmMsg = labels[actionType] || 'Jalankan tindakan ini pada ' + selected.length + ' kegiatan terpilih?';
        if (!confirm(confirmMsg)) {
            return;
        }

        const form = document.getElementById('bulkActionForm');
        const actionInput = document.getElementById('bulkActionInput');
        const container = document.getElementById('bulkActionAssignmentIdsContainer');

        actionInput.value = actionType;
        container.innerHTML = '';
        selected.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'assignment_ids[]';
            hidden.value = id;
            container.appendChild(hidden);
        });

        form.submit();
    }

    function openBulkAssignModal() {
        const selected = Array.from(document.querySelectorAll('.assignment-row-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Pilih minimal satu kegiatan terlebih dahulu.');
            return;
        }

        const countText = document.getElementById('bulkAssignSelectedCountText');
        if (countText) countText.innerText = selected.length + ' kegiatan';

        const container = document.getElementById('bulkAssignModalIdsContainer');
        container.innerHTML = '';
        selected.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'assignment_ids[]';
            hidden.value = id;
            container.appendChild(hidden);
        });

        const modal = new bootstrap.Modal(document.getElementById('bulkAssignModal'));
        modal.show();
    }

    function toggleAllBulkOfficers(selectAll) {
        document.querySelectorAll('.bulk-officer-checkbox').forEach(cb => {
            const parent = cb.closest('.bulk-officer-item');
            if (!parent || parent.style.display !== 'none') {
                cb.checked = selectAll;
            }
        });
    }

    function filterBulkOfficers(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.bulk-officer-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            if (!q || name.includes(q)) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });
    }
</script>
@endpush
