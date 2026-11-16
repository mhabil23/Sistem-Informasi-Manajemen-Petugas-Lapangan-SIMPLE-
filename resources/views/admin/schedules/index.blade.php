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
    overflow: hidden;
}
.bento-table-fixed {
    table-layout: fixed;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.8rem;
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
        margin: 0.75rem;
        padding: 0.85rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
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

        <div class="col-lg-3 col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Berlangsung</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-2">
            <select name="officer_id" class="form-select">
                <option value="">-- Semua Petugas --</option>
                @foreach($officers as $officer)
                    <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-bps-orange flex-fill justify-content-center py-2">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'officer_id']))
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
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                Halaman {{ $schedules->currentPage() }} dari {{ max(1, $schedules->lastPage()) }}
            </span>
            @if(auth()->user()->isAdmin())
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
                                <span class="badge bg-light text-primary border rounded-pill px-2 py-0.5 text-truncate d-inline-block" style="font-size: 0.72rem; max-width: 100%;" title="{{ $item->officer->name }}">
                                    <i class="bi bi-person-fill me-1"></i> {{ $item->officer->name }}
                                </span>
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
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA / JUDUL AGENDA <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Briefing Tim & Pembagian Peta Kerja Lapangan" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KEGIATAN TERKAIT</label>
                            <select name="assignment_id" class="form-select">
                                <option value="">-- Tidak Terikat Kegiatan Tertentu --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">PETUGAS TERKAIT</label>
                            <select name="officer_id" class="form-select">
                                <option value="">-- Seluruh Petugas (Umum) --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                @endforeach
                            </select>
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
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">LOKASI / RUANG PERTEMUAN <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: Ruang Rapat Kantor Wilayah & Zoom Meeting" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">STATUS JADWAL <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="scheduled" selected>Terjadwal</option>
                                <option value="in_progress">Sedang Berlangsung</option>
                                <option value="completed">Selesai</option>
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
                            <select id="edit_sched_assignment" name="assignment_id" class="form-select">
                                <option value="">-- Tidak Terikat Kegiatan Tertentu --</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">PETUGAS TERKAIT</label>
                            <select id="edit_sched_officer" name="officer_id" class="form-select">
                                <option value="">-- Seluruh Petugas (Umum) --</option>
                                @foreach($officers as $officer)
                                    <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                @endforeach
                            </select>
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
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">LOKASI / RUANG PERTEMUAN <span class="text-danger">*</span></label>
                            <input type="text" id="edit_sched_location" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">STATUS JADWAL <span class="text-danger">*</span></label>
                            <select id="edit_sched_status" name="status" class="form-select" required>
                                <option value="scheduled">Terjadwal</option>
                                <option value="in_progress">Sedang Berlangsung</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
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
@endif
@endsection

@push('scripts')
<script>
    function openEditScheduleModal(item) {
        document.getElementById('editScheduleForm').action = '/admin/schedules/' + item.id;
        document.getElementById('edit_sched_title').value = item.title;
        document.getElementById('edit_sched_assignment').value = item.assignment_id || '';
        document.getElementById('edit_sched_officer').value = item.officer_id || '';
        document.getElementById('edit_sched_start').value = item.start_time.substring(0, 16);
        document.getElementById('edit_sched_end').value = item.end_time.substring(0, 16);
        document.getElementById('edit_sched_location').value = item.location;
        document.getElementById('edit_sched_status').value = item.status;
        document.getElementById('edit_sched_notes').value = item.notes || '';

        const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
        modal.show();
    }
</script>
@endpush
