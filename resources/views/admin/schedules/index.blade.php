@extends('layouts.app')

@section('title', 'Jadwal Kegiatan')
@section('page_title', 'Jadwal Kegiatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Jadwal Kegiatan</li>
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
</style>
@endpush

@section('content')
<!-- Bento Search & Filter Card -->
<div class="bento-card-clean p-3 mb-3">
    <form method="GET" action="{{ route('admin.schedules.index') }}" class="row g-2 align-items-center">
        <div class="col-lg-5 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       name="search" 
                       class="form-control border-start-0 ps-1" 
                       value="{{ request('search') }}" 
                       placeholder="Cari judul agenda, lokasi, atau catatan...">
            </div>
        </div>

        <!-- Filter Kegiatan -->
        <div class="col-lg-3 col-md-6">
            <select name="assignment_id" id="filter_sched_assignment" class="form-select">
                <option value="">-- Pilih Kegiatan --</option>
                @foreach($assignments as $assignment)
                    <option value="{{ $assignment->id }}" {{ request('assignment_id') == $assignment->id ? 'selected' : '' }}>{{ $assignment->title }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Peran Petugas -->
        <div class="col-xl-2 col-lg-2 col-md-4">
            <select name="officer_role" id="filter_sched_officer_role" class="form-select">
                <option value="" {{ request('officer_role') === '' || request('officer_role') === null ? 'selected' : '' }}>Seluruh petugas</option>
                <option value="PCL" {{ request('officer_role') === 'PCL' ? 'selected' : '' }}>Hanya PCL</option>
                <option value="PML" {{ request('officer_role') === 'PML' ? 'selected' : '' }}>Hanya PML</option>
                <option value="PPL" {{ request('officer_role') === 'PPL' ? 'selected' : '' }}>Hanya PPL</option>
                <option value="PPL_PML" {{ request('officer_role') === 'PPL_PML' ? 'selected' : '' }}>PPL &amp; PML</option>
            </select>
        </div>

        <!-- Filter Status Petugas -->
        <div class="col-xl-2 col-lg-2 col-md-4">
            <select name="officer_status" class="form-select">
                <option value="">-- Pilih Status Petugas --</option>
                <option value="active" {{ request('officer_status') === 'active' ? 'selected' : '' }}>Petugas Aktif</option>
                <option value="inactive" {{ request('officer_status') === 'inactive' ? 'selected' : '' }}>Petugas Tidak Aktif</option>
            </select>
        </div>

        <!-- Filter Status Jadwal -->
        <div class="col-xl-2 col-lg-2 col-md-4">
            <select name="status" class="form-select">
                <option value="">-- Pilih Status Jadwal --</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Berlangsung</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <!-- Tombol Filter & Reset -->
        <div class="col-xl-1 col-lg-1 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-bps-orange flex-fill justify-content-center py-2" title="Terapkan Filter">
                <i class="bi bi-funnel-fill"></i>
            </button>
            @if(request()->hasAny(['search', 'status', 'officer_status', 'officer_role', 'assignment_id']))
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary rounded-pill px-3 d-flex align-items-center justify-content-center" title="Reset Filter">
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
                Daftar Agenda Kegiatan &amp; Jadwal Lapangan
            </h6>
            <span class="text-muted" style="font-size: 0.7rem;">Total {{ $schedules->total() }} agenda terdaftar dalam sistem</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                Halaman {{ $schedules->currentPage() }} dari {{ max(1, $schedules->lastPage()) }}
            </span>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn btn-outline-success rounded-pill px-3 py-1.5 shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#uploadScheduleExcelModal" title="Upload berkas Excel (.xlsx / .csv) untuk generate banyak jadwal kegiatan sekaligus">
                    <i class="bi bi-file-earmark-excel-fill text-success"></i> Upload Excel Jadwal
                </button>
                <button type="button" class="btn-bps-orange shadow-sm py-1.5 px-3" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                    <i class="bi bi-calendar-plus-fill me-1"></i> Buat Jadwal Baru
                </button>
            @endif
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="table bento-table-fixed align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 18%;"><i class="bi bi-calendar-event me-1 text-primary"></i> Waktu &amp; Tanggal</th>
                    <th style="width: 22%;"><i class="bi bi-journal-text me-1 text-muted"></i> Nama Agenda</th>
                    <th style="width: 17%;"><i class="bi bi-briefcase me-1 text-muted"></i> Kegiatan Terkait</th>
                    <th style="width: 14%;"><i class="bi bi-people-fill me-1 text-primary"></i> Petugas</th>
                    <th style="width: 13%;"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> Lokasi</th>
                    <th class="text-center" style="width: 7%;"><i class="bi bi-activity me-1 text-warning"></i> Status</th>
                    <th class="text-end pe-3" style="width: 9%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $item)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark text-truncate d-flex align-items-center gap-1" style="font-size: 0.8rem;">
                                <i class="bi bi-calendar-check text-primary flex-shrink-0"></i>
                                <span>{{ $item->start_time->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="text-muted text-truncate d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.72rem;">
                                <i class="bi bi-clock text-secondary flex-shrink-0"></i>
                                <span>{{ $item->start_time->format('H:i') }} &ndash; {{ $item->end_time->format('H:i') }} WIB</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.82rem;" title="{{ $item->title }}">
                                {{ $item->title }}
                            </div>
                            @if($item->notes)
                                <div class="text-muted text-truncate" style="font-size: 0.7rem;" title="{{ $item->notes }}">
                                    {{ $item->notes }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($item->assignment)
                                <a href="{{ route('admin.assignments.show', $item->assignment) }}" class="small fw-semibold text-dark text-decoration-none text-truncate d-block" style="font-size: 0.76rem;" title="{{ $item->assignment->title }}">
                                    {{ $item->assignment->title }}
                                </a>
                            @else
                                <span class="text-muted small" style="font-size: 0.74rem;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->officer)
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-light text-primary border rounded-pill px-2 py-0.5 text-truncate d-inline-block" style="font-size: 0.72rem; max-width: 100%;" title="{{ $item->officer->name }} ({{ $item->officer->field_role ?? 'PCL' }})">
                                        <i class="bi bi-person-fill me-1"></i> {{ $item->officer->name }}
                                        <span class="badge {{ ($item->officer->field_role ?? 'PCL') === 'PML' ? 'bg-info-subtle text-info-emphasis border border-info-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} rounded-pill ms-1" style="font-size: 0.62rem;">{{ $item->officer->field_role ?? 'PCL' }}</span>
                                    </span>
                                    <div>
                                        @if(($item->officer_status ?? 'active') === 'inactive')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Status Petugas pada agenda ini: Tidak Aktif">
                                                <i class="bi bi-person-x-fill me-1"></i> Tidak Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Status Petugas pada agenda ini: Aktif Bertugas">
                                                <i class="bi bi-person-check-fill me-1"></i> Aktif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                    <i class="bi bi-people-fill me-1"></i> Seluruh Petugas
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark text-truncate d-flex align-items-center gap-1" style="font-size: 0.76rem;" title="{{ $item->location }}">
                                <i class="bi bi-geo-alt-fill text-danger flex-shrink-0"></i>
                                <span class="text-truncate">{{ $item->location }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($item->status === 'scheduled')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    Terjadwal
                                </span>
                            @elseif($item->status === 'in_progress')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    Berlangsung
                                </span>
                            @elseif($item->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    Selesai
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    Batal
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            @if(auth()->user()->isAdmin())
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <!-- Edit -->
                                    <button type="button" 
                                            class="bento-action-btn btn-edit" 
                                            title="Edit Jadwal" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditScheduleModal({{ json_encode($item) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.schedules.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus jadwal agenda ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bento-action-btn btn-delete" 
                                                title="Hapus Jadwal" 
                                                data-bs-toggle="tooltip">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="d-inline-flex p-3 rounded-circle bg-light text-secondary mb-2">
                                <i class="bi bi-calendar-x fs-1 opacity-50"></i>
                            </div>
                            <div class="fw-semibold text-dark">Belum ada agenda jadwal kegiatan.</div>
                            <div class="small text-muted">Klik tombol "Buat Jadwal Baru" untuk menambahkan agenda.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schedules->hasPages())
        <div class="p-3 bg-white border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted" style="font-size: 0.75rem;">
                Menampilkan {{ $schedules->firstItem() }} sampai {{ $schedules->lastItem() }} dari {{ $schedules->total() }} agenda
            </div>
            <div>
                {{ $schedules->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@if(auth()->user()->isAdmin())
<!-- ==============================================================
     MODAL BUAT JADWAL BARU
     ============================================================== -->
<div class="modal fade" id="createScheduleModal" tabindex="-1" aria-labelledby="createScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: var(--bps-orange, #F37021); width: 32px; height: 32px;">
                        <i class="bi bi-calendar-plus-fill"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="createScheduleModalLabel">
                        Tambah Jadwal Kegiatan Baru
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.schedules.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <!-- Banner Shortcut Upload Excel jika banyak data -->
                    <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center justify-content-between rounded-3 border-info border-opacity-25 bg-info bg-opacity-10">
                        <div class="small text-dark">
                            <i class="bi bi-file-earmark-excel-fill text-success me-1"></i>
                            Punya banyak agenda kegiatan? Gunakan fitur <strong>Upload Excel Jadwal</strong> untuk meng-generate puluhan jadwal sekaligus secara otomatis tanpa input satu per satu.
                        </div>
                        <button type="button" class="btn btn-xs btn-success rounded-pill px-2.5 py-1 text-nowrap ms-2" style="font-size: 0.75rem;" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadScheduleExcelModal">
                            <i class="bi bi-upload me-1"></i> Buka Upload Excel
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA / JUDUL AGENDA <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Briefing Tim & Pembagian Peta Kerja Lapangan" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KEGIATAN TERKAIT</label>
                            <select name="assignment_id" id="create_sched_assignment" class="form-select" onchange="syncScheduleOfficerDropdown('create_sched_assignment', 'create_sched_officer', 'create_sched_officer_hint')">
                                <option value="">-- Tidak Terikat Kegiatan Tertentu --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">PETUGAS TERKAIT</label>
                                <span id="create_sched_officer_badge" class="badge bg-light text-secondary border" style="font-size: 0.68rem;">Semua Petugas</span>
                            </div>
                            <select name="officer_id" id="create_sched_officer" class="form-select">
                                <option value="">-- Seluruh Petugas (Umum) --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->field_role }})</option>
                                @endforeach
                            </select>
                            <div id="create_sched_officer_hint" class="form-text small text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-people me-1"></i> Menampilkan seluruh petugas (jadwal umum BPS).
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">WAKTU MULAI <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_time" class="form-control" value="{{ now()->addDays(1)->setTime(8,30)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">WAKTU SELESAI <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_time" class="form-control" value="{{ now()->addDays(1)->setTime(11,30)->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">LOKASI / RUANG PERTEMUAN <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: Ruang Rapat Kantor Wilayah & Zoom Meeting" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS JADWAL <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="scheduled" selected>Terjadwal</option>
                                <option value="in_progress">Sedang Berlangsung</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS PETUGAS <span class="text-danger">*</span></label>
                            <select name="officer_status" class="form-select" required>
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN / PERLENGKAPAN YANG WAJIB DIBAWA</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan instruksi untuk petugas..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn-bps-orange py-1.5 px-3" style="font-size: 0.82rem;">
                        <i class="bi bi-check-lg me-1"></i> Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT JADWAL
     ============================================================== -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center bg-warning" style="width: 32px; height: 32px;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="editScheduleModalLabel">
                        Edit Jadwal Kegiatan
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editScheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA / JUDUL AGENDA <span class="text-danger">*</span></label>
                        <input type="text" id="edit_sched_title" name="title" class="form-control" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KEGIATAN TERKAIT</label>
                            <select id="edit_sched_assignment" name="assignment_id" class="form-select" onchange="syncScheduleOfficerDropdown('edit_sched_assignment', 'edit_sched_officer', 'edit_sched_officer_hint')">
                                <option value="">-- Tidak Terikat Kegiatan Tertentu --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold text-secondary mb-0">PETUGAS TERKAIT</label>
                                <span id="edit_sched_officer_badge" class="badge bg-light text-secondary border" style="font-size: 0.68rem;">Semua Petugas</span>
                            </div>
                            <select id="edit_sched_officer" name="officer_id" class="form-select">
                                <option value="">-- Seluruh Petugas (Umum) --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->field_role }})</option>
                                @endforeach
                            </select>
                            <div id="edit_sched_officer_hint" class="form-text small text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-people me-1"></i> Menampilkan seluruh petugas (jadwal umum BPS).
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">WAKTU MULAI <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="edit_sched_start" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">WAKTU SELESAI <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="edit_sched_end" name="end_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">LOKASI / RUANG PERTEMUAN <span class="text-danger">*</span></label>
                            <input type="text" id="edit_sched_location" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS JADWAL <span class="text-danger">*</span></label>
                            <select id="edit_sched_status" name="status" class="form-select" required>
                                <option value="scheduled">Terjadwal</option>
                                <option value="in_progress">Sedang Berlangsung</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS PETUGAS <span class="text-danger">*</span></label>
                            <select id="edit_sched_officer_status" name="officer_status" class="form-select" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN / PERLENGKAPAN</label>
                        <textarea id="edit_sched_notes" name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn-bps-orange py-1.5 px-3" style="font-size: 0.82rem;">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ==============================================================
     MODAL UPLOAD EXCEL JADWAL KEGIATAN
     ============================================================== -->
<div class="modal fade" id="uploadScheduleExcelModal" tabindex="-1" aria-labelledby="uploadScheduleExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center bg-success" style="width: 32px; height: 32px;">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="uploadScheduleExcelModalLabel">
                            Upload &amp; Generate Massal Jadwal Kegiatan dari Excel
                        </h6>
                        <span class="small text-muted" style="font-size: 0.72rem;">Otomatis membaca puluhan/ratusan jadwal dan meng-generate ke database sekaligus</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.schedules.template.download', ['format' => 'xlsx']) }}" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Unduh Format Template Excel (.xlsx)">
                        <i class="bi bi-download me-1"></i> Template (.xlsx)
                    </a>
                    <a href="{{ route('admin.schedules.template.download', ['format' => 'csv']) }}" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Unduh Format Template CSV (.csv)">
                        <i class="bi bi-filetype-csv me-1"></i> (.csv)
                    </a>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <form id="importScheduleExcelForm" action="{{ route('admin.schedules.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Area Pilih File & Mode -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-7">
                                <label class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-folder2-open me-1 text-primary"></i> PILIH BERKAS EXCEL ATAU CSV <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="file" name="excel_file" id="scheduleExcelFileInput" class="form-control" accept=".xlsx,.xls,.csv,.txt" required>
                                    <button type="button" id="btnPreviewScheduleExcel" class="btn btn-primary px-3" onclick="previewScheduleExcel()">
                                        <i class="bi bi-eye-fill me-1"></i> Baca &amp; Pratinjau
                                    </button>
                                </div>
                                <div class="form-text small text-muted mt-1">
                                    Mendukung format <code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code> (Maks. 10 MB).
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-gear-fill me-1 text-secondary"></i> OPSI IMPORT / GENERATE
                                </label>
                                <div class="p-2 bg-white rounded border">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="radio" name="import_mode" id="schedModeAppend" value="append" checked>
                                        <label class="form-check-label small" for="schedModeAppend">
                                            <strong>Tambahkan</strong> ke daftar jadwal yang sudah ada (Append)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="import_mode" id="schedModeReplace" value="replace">
                                        <label class="form-check-label small text-danger" for="schedModeReplace">
                                            <strong>Gantikan</strong> seluruh jadwal lama (Reset)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Alert AJAX -->
                        <div id="scheduleExcelStatusAlert" class="mt-2.5 alert d-none mb-0 p-2.5 small" role="alert"></div>
                    </div>

                    <!-- Panel Pratinjau Hasil Pembacaan Excel -->
                    <div id="schedulePreviewContainer" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success rounded-pill px-2.5 py-1" id="previewCountBadge">0 Agenda Terbaca</span>
                                <span class="small text-muted">Periksa data jadwal sebelum di-generate ke database</span>
                            </div>
                            <span class="small text-muted fst-italic">Format kolom terbaca otomatis</span>
                        </div>

                        <div class="table-responsive rounded-3 border" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 45px;" class="text-center">No.</th>
                                        <th style="min-width: 200px;">Nama Agenda</th>
                                        <th style="min-width: 170px;">Kegiatan Terkait</th>
                                        <th style="min-width: 150px;">Petugas</th>
                                        <th style="min-width: 160px;">Waktu Pelaksanaan</th>
                                        <th style="min-width: 150px;">Lokasi</th>
                                        <th style="width: 110px;" class="text-center">Status</th>
                                        <th style="min-width: 160px;">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody id="schedulePreviewTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between align-items-center">
                    <div class="small text-muted" id="scheduleImportHint">
                        <i class="bi bi-info-circle me-1"></i> Data diproses dalam batch cepat sehingga puluhan jadwal dapat ter-generate dalam beberapa detik.
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                        <button type="submit" id="btnSubmitImportSchedule" class="btn btn-success rounded-pill px-4 py-1.5 shadow-sm" style="font-size: 0.82rem; font-weight: 600;">
                            <i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Simpan &amp; Generate Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    const assignmentOfficersMap = @json($assignmentOfficersMap ?? []);
    const allOfficers = @json($officers->map(fn($u) => ['id' => $u->id, 'name' => $u->name . ' (' . ($u->field_role ?? 'PCL') . ')']));

    function syncScheduleOfficerDropdown(assignmentSelectId, officerSelectId, hintId, selectedOfficerId = null) {
        const assignmentSelect = document.getElementById(assignmentSelectId);
        const officerSelect = document.getElementById(officerSelectId);
        const hintEl = hintId ? document.getElementById(hintId) : null;
        const badgeEl = document.getElementById(officerSelectId.replace('_officer', '_officer_badge'));

        if (!assignmentSelect || !officerSelect) return;

        const assignmentId = assignmentSelect.value;
        const currentOfficerVal = selectedOfficerId !== null && selectedOfficerId !== undefined 
            ? String(selectedOfficerId) 
            : officerSelect.value;

        // Clear options
        officerSelect.innerHTML = '';

        if (!assignmentId) {
            // General / No specific assignment: show all officers
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Seluruh Petugas (Umum) --';
            officerSelect.appendChild(defaultOpt);

            allOfficers.forEach(off => {
                const opt = document.createElement('option');
                opt.value = off.id;
                opt.textContent = off.name;
                if (String(off.id) === String(currentOfficerVal)) {
                    opt.selected = true;
                }
                officerSelect.appendChild(opt);
            });

            if (hintEl) {
                hintEl.className = 'form-text small text-muted';
                hintEl.innerHTML = '<i class="bi bi-people me-1"></i> Menampilkan seluruh petugas (jadwal umum BPS).';
            }
            if (badgeEl) {
                badgeEl.className = 'badge bg-light text-secondary border';
                badgeEl.textContent = 'Semua Petugas';
            }
        } else {
            // Specific assignment chosen: show ONLY officers involved in this assignment
            const involved = assignmentOfficersMap[assignmentId] || [];

            if (involved.length > 0) {
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = '-- Seluruh Petugas di Kegiatan Ini (Umum) --';
                officerSelect.appendChild(defaultOpt);

                let officerFound = false;
                involved.forEach(off => {
                    const opt = document.createElement('option');
                    opt.value = off.id;
                    opt.textContent = off.name;
                    if (String(off.id) === String(currentOfficerVal)) {
                        opt.selected = true;
                        officerFound = true;
                    }
                    officerSelect.appendChild(opt);
                });

                if (currentOfficerVal && !officerFound) {
                    officerSelect.value = '';
                }

                if (hintEl) {
                    hintEl.className = 'form-text small text-success';
                    hintEl.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> Menampilkan <strong>${involved.length} petugas</strong> yang terlibat pada kegiatan ini.`;
                }
                if (badgeEl) {
                    badgeEl.className = 'badge bg-success-subtle text-success border border-success-subtle';
                    badgeEl.textContent = `${involved.length} Petugas Terlibat`;
                }
            } else {
                // No officers assigned to this activity yet
                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = '-- Belum Ada Petugas Terdaftar di Kegiatan Ini --';
                officerSelect.appendChild(emptyOpt);

                if (hintEl) {
                    hintEl.className = 'form-text small text-warning';
                    hintEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Belum ada petugas yang dialokasikan pada kegiatan ini.';
                }
                if (badgeEl) {
                    badgeEl.className = 'badge bg-warning-subtle text-warning border border-warning-subtle';
                    badgeEl.textContent = '0 Petugas Terlibat';
                }
            }
        }
    }

    function openEditScheduleModal(item) {
        document.getElementById('editScheduleForm').action = '/admin/schedules/' + item.id;
        document.getElementById('edit_sched_title').value = item.title;
        document.getElementById('edit_sched_assignment').value = item.assignment_id || '';
        
        // Sync officer dropdown based on the schedule's assignment and set selected officer
        syncScheduleOfficerDropdown('edit_sched_assignment', 'edit_sched_officer', 'edit_sched_officer_hint', item.officer_id || '');

        document.getElementById('edit_sched_start').value = item.start_time.substring(0, 16);
        document.getElementById('edit_sched_end').value = item.end_time.substring(0, 16);
        document.getElementById('edit_sched_location').value = item.location;
        document.getElementById('edit_sched_status').value = item.status;
        document.getElementById('edit_sched_officer_status').value = item.officer_status || 'active';
        document.getElementById('edit_sched_notes').value = item.notes || '';

        const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
        modal.show();
    }

    // Create modal listener for auto syncing officers based on assignment
    document.addEventListener('DOMContentLoaded', function() {
        const createModal = document.getElementById('createScheduleModal');
        if (createModal) {
            createModal.addEventListener('show.bs.modal', function() {
                syncScheduleOfficerDropdown('create_sched_assignment', 'create_sched_officer', 'create_sched_officer_hint');
            });
        }
    });

    function previewScheduleExcel() {
        const fileInput = document.getElementById('scheduleExcelFileInput');
        const alertBox = document.getElementById('scheduleExcelStatusAlert');
        const btn = document.getElementById('btnPreviewScheduleExcel');
        const previewContainer = document.getElementById('schedulePreviewContainer');
        const tbody = document.getElementById('schedulePreviewTableBody');
        const countBadge = document.getElementById('previewCountBadge');

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            alertBox.className = 'mt-2.5 alert alert-warning p-2.5 small mb-0';
            alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Silakan pilih berkas Excel (.xlsx) atau CSV terlebih dahulu.';
            alertBox.classList.remove('d-none');
            fileInput?.focus();
            return;
        }

        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('excel_file', file);
        formData.append('_token', @json(csrf_token()));

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Membaca...';

        alertBox.className = 'mt-2.5 alert alert-info p-2.5 small mb-0';
        alertBox.innerHTML = '<span class="spinner-border spinner-border-sm text-primary me-1"></span> Sedang memproses dan membaca data jadwal dari berkas Excel...';
        alertBox.classList.remove('d-none');

        fetch(@json(route('admin.schedules.preview-excel')), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Gagal membaca berkas Excel.');
            }
            return data;
        })
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-eye-fill me-1"></i> Baca &amp; Pratinjau';

            const schedules = data.schedules || [];
            tbody.innerHTML = '';

            schedules.forEach(item => {
                const tr = document.createElement('tr');
                const statusBadgeClass = item.status === 'completed' 
                    ? 'bg-success-subtle text-success border border-success-subtle' 
                    : (item.status === 'in_progress' ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : (item.status === 'cancelled' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle'));
                
                tr.innerHTML = `
                    <td class="text-center fw-semibold text-secondary">${item.no}</td>
                    <td><strong class="text-dark">${escapeHtmlAttr(item.title)}</strong></td>
                    <td><span class="badge bg-light text-secondary border">${escapeHtmlAttr(item.assignment_title)}</span></td>
                    <td>
                        <span class="small">${escapeHtmlAttr(item.officer_name)}</span>
                        ${item.officer_status === 'inactive' ? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-1.5 py-0.5 ms-1" style="font-size:0.65rem;">Tidak Aktif</span>' : '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-1.5 py-0.5 ms-1" style="font-size:0.65rem;">Aktif</span>'}
                    </td>
                    <td><small class="text-muted"><i class="bi bi-calendar3 me-1"></i>${escapeHtmlAttr(item.time_range_formatted)}</small></td>
                    <td><small class="text-muted"><i class="bi bi-geo-alt me-1"></i>${escapeHtmlAttr(item.location)}</small></td>
                    <td class="text-center"><span class="badge rounded-pill ${statusBadgeClass}">${escapeHtmlAttr(item.status_label)}</span></td>
                    <td><small class="text-muted">${escapeHtmlAttr(item.notes || '-')}</small></td>
                `;
                tbody.appendChild(tr);
            });

            countBadge.textContent = `${schedules.length} Agenda Terbaca`;
            previewContainer.classList.remove('d-none');

            alertBox.className = 'mt-2.5 alert alert-success p-2.5 small mb-0';
            alertBox.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Berhasil!</strong> ${schedules.length} agenda kegiatan berhasil dibaca. Klik tombol <strong>"Simpan & Generate Sekarang"</strong> di bawah untuk memasukkan ke database.`;
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-eye-fill me-1"></i> Baca &amp; Pratinjau';
            alertBox.className = 'mt-2.5 alert alert-danger p-2.5 small mb-0';
            alertBox.innerHTML = `<i class="bi bi-x-circle-fill text-danger me-1"></i> ${escapeHtmlAttr(err.message)}`;
        });
    }

    function escapeHtmlAttr(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    document.getElementById('importScheduleExcelForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitImportSchedule');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Sedang Meng-generate...';
        }
    });
</script>
@endpush
