@extends('layouts.app')

@section('title', 'Detail Kegiatan & Pembagian Tugas')
@section('page_title', 'Detail Kegiatan & Penugasan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">SIMPEL</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.assignments.index') }}" class="text-decoration-none">Kegiatan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail &amp; Pembagian Tugas</li>
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

/* Fixed Bento Table Card (No Horizontal Scroll / Anti-Geser) */
.bento-table-card {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
    width: 100%;
}
.bento-table-header {
    padding: 1rem 1.25rem;
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

.user-avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    background: #FFF4ED;
    color: #F37021;
    border: 1px solid rgba(243, 112, 33, 0.2);
    flex-shrink: 0;
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
<!-- Back Button & Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('admin.assignments.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 mb-2 d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kegiatan
        </a>
        <h4 class="fw-bold text-dark mb-0" style="letter-spacing: -0.015em;">{{ $assignment->title }}</h4>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('admin.assignments.toggle-admin', $assignment) }}" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm {{ $assignment->is_enabled_by_admin ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 py-1.5 fw-semibold">
                    <i class="bi {{ $assignment->is_enabled_by_admin ? 'bi-toggle-on' : 'bi-toggle-off' }} me-1"></i>
                    {{ $assignment->is_enabled_by_admin ? 'Nonaktifkan Penugasan (ON)' : 'Aktifkan Penugasan (OFF)' }}
                </button>
            </form>
            <button type="button" class="btn-bps-orange shadow-sm" data-bs-toggle="modal" data-bs-target="#assignOfficerModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tugaskan Petugas Baru
            </button>
        @endif
    </div>
</div>

<!-- Overview Cards Row -->
<div class="row g-3 mb-4">
    <!-- Informasi Kegiatan -->
    <div class="col-lg-8">
        <div class="bento-card-clean h-100 p-4">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #F37021; width: 34px; height: 34px;">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Informasi Rinci Kegiatan</h6>
                        <span class="text-muted" style="font-size: 0.72rem;">Detail jadwal, cakupan wilayah, dan dasar pelaksanaan</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-1.5">
                    <span class="badge {{ $assignment->visibility_status_badge_class }} px-3 py-1.5 rounded-pill" style="font-size: 0.74rem;">
                        <i class="bi bi-eye-fill me-1"></i> {{ $assignment->visibility_status_label }}
                    </span>
                    @if($assignment->status === 'ongoing')
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 rounded-pill" style="font-size: 0.74rem;">
                            <i class="bi bi-play-circle-fill me-1"></i> Sedang Berjalan
                        </span>
                    @elseif($assignment->status === 'completed')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill" style="font-size: 0.74rem;">
                            <i class="bi bi-check-circle-fill me-1"></i> Selesai
                        </span>
                    @elseif($assignment->status === 'draft')
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1.5 rounded-pill" style="font-size: 0.74rem;">
                            <i class="bi bi-pencil-square me-1"></i> Draft
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill" style="font-size: 0.74rem;">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <div class="text-muted small mb-1"><i class="bi bi-calendar3 me-1 text-primary"></i> Waktu Pelaksanaan</div>
                    <div class="fw-bold text-dark small text-truncate">
                        {{ $assignment->start_date->translatedFormat('d F Y') }} &ndash; {{ $assignment->end_date->translatedFormat('d F Y') }}
                    </div>
                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.68rem;">
                        {{ $assignment->start_date->diffInDays($assignment->end_date) + 1 }} Hari Kerja
                    </span>
                </div>
                <div class="col-sm-4">
                    <div class="text-muted small mb-1"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> Lokasi Wilayah</div>
                    <div class="fw-bold text-dark small text-truncate">
                        {{ $assignment->location }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="text-muted small mb-1"><i class="bi bi-pie-chart-fill me-1 text-success"></i> Target Kuota Responden</div>
                    <div class="fw-bold text-dark">
                        @if($assignment->target_respondents > 0)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">
                                {{ number_format($assignment->target_respondents) }} Sampel
                            </span>
                        @else
                            <span class="text-muted small">Belum Ditentukan</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($assignment->basis_dipa || $assignment->st_number || $assignment->basis_st)
                <div class="border-top pt-3 mb-3">
                    <div class="text-muted small fw-bold mb-2">
                        <i class="bi bi-journal-check text-primary me-1"></i> Dasar Pelaksanaan (Laporan &amp; Pengeluaran Riil):
                    </div>
                    <div class="small text-dark mb-1">
                        <span class="text-muted">1. DIPA:</span> <strong>{{ $assignment->basis_dipa ?? '-' }}</strong>
                    </div>
                    <div class="small text-dark">
                        <span class="text-muted">2. Surat Tugas:</span> <strong>{{ $assignment->basis_st ?: ($assignment->st_number ?? '-') }}</strong>
                    </div>
                </div>
            @endif

            <div class="border-top pt-3">
                <div class="text-muted small mb-1 fw-bold">Uraian / Deskripsi Petunjuk Teknis:</div>
                <p class="text-secondary small mb-0" style="white-space: pre-line; line-height: 1.6;">
                    {{ $assignment->description ?? 'Tidak ada uraian deskripsi tambahan.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Ringkasan Progres -->
    <div class="col-lg-4">
        <div class="bento-card-clean h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 pb-3 mb-3 border-bottom">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #16A34A; width: 34px; height: 34px;">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Ringkasan Progres</h6>
                        <span class="text-muted" style="font-size: 0.72rem;">Statistik capaian real-time</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small"><i class="bi bi-people me-1 text-primary"></i> Petugas Ditugaskan:</span>
                    <strong class="text-dark fs-6">{{ $assignment->assignmentOfficers->count() }} Orang</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small"><i class="bi bi-file-earmark-person me-1 text-info"></i> Responden Terdata:</span>
                    <div>
                        <strong class="text-primary fs-6">{{ $assignment->respondents->count() }}</strong>
                        @if($assignment->target_respondents > 0)
                            <span class="text-muted small">/ {{ number_format($assignment->target_respondents) }}</span>
                        @else
                            <span class="text-muted small">Responden</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small"><i class="bi bi-file-earmark-check me-1 text-success"></i> BAST Diterbitkan:</span>
                    <strong class="text-success fs-6">{{ $assignment->respondentBasts->count() + $assignment->officerBasts->count() }} Dokumen</strong>
                </div>

                @if($assignment->target_respondents > 0)
                    @php
                        $respPercent = round(($assignment->respondents->count() / $assignment->target_respondents) * 100);
                    @endphp
                    <div class="border-top pt-3 mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1.5">
                            <span style="font-size: 0.76rem;">Pencapaian Kuota Responden:</span>
                            <strong class="{{ $respPercent >= 100 ? 'text-success' : 'text-primary' }}">{{ $respPercent }}%</strong>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 999px;">
                            <div class="progress-bar {{ $respPercent >= 100 ? 'bg-success' : ($respPercent >= 50 ? 'bg-primary' : 'bg-warning') }}" role="progressbar" style="width: {{ min(100, $respPercent) }}%;"></div>
                        </div>
                    </div>
                @endif

                @php
                    $totalOfficers = $assignment->assignmentOfficers->count();
                    $completedOfficers = $assignment->assignmentOfficers->where('status', 'completed')->count();
                    $percent = $totalOfficers > 0 ? round(($completedOfficers / $totalOfficers) * 100) : 0;
                @endphp
                <div class="{{ $assignment->target_respondents > 0 ? '' : 'border-top pt-3' }}">
                    <div class="d-flex justify-content-between small text-muted mb-1.5">
                        <span style="font-size: 0.76rem;">Penyelesaian Tugas Petugas:</span>
                        <strong class="text-dark">{{ $percent }}%</strong>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 999px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3 pt-3 border-top text-center text-muted" style="font-size: 0.72rem;">
                <i class="bi bi-clock-history me-1"></i> Data diperbarui otomatis secara real-time
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     BAGIAN 1: PEMBAGIAN TUGAS PETUGAS LAPANGAN (ANTI-GESER)
     ============================================================== -->
<div class="bento-table-card mb-4">
    <div class="bento-table-header">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #2563EB; width: 34px; height: 34px;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0">Daftar Petugas &amp; Rincian Tugas</h6>
                <span class="text-muted" id="officerCountText" style="font-size: 0.72rem;">Total {{ $assignment->assignmentOfficers->count() }} petugas ditugaskan pada kegiatan ini</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
            <!-- Filter Status Tugas -->
            <select id="filterOfficerStatus" class="form-select form-select-sm rounded-pill" style="width: 130px; font-size: 0.78rem;" onchange="filterOfficerTable()">
                <option value="">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="in_progress">Berjalan</option>
                <option value="completed">Selesai</option>
            </select>

            <!-- Filter Nama / Nomor HP Petugas -->
            <div class="input-group input-group-sm" style="width: 220px;">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       id="filterOfficerName" 
                       class="form-control border-start-0 ps-1" 
                       placeholder="Cari nama petugas..." 
                       style="font-size: 0.78rem;"
                       oninput="filterOfficerTable()">
                <button type="button" class="btn btn-outline-secondary border-start-0 text-muted" id="btnResetOfficerFilter" onclick="resetOfficerFilter()" title="Hapus pencarian" style="display: none;">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>

            @if(auth()->user()->isAdmin())
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#assignOfficerModal">
                    <i class="bi bi-plus-lg"></i> Tambah Petugas
                </button>
            @endif
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="table bento-table-fixed align-middle mb-0" id="officersTable">
            <thead>
                <tr>
                    <th style="width: 30%;">Petugas Lapangan</th>
                    <th style="width: 28%;">Tugas Spesifik</th>
                    <th class="text-center" style="width: 14%;">Status Tugas</th>
                    <th style="width: 16%;">Catatan Progres</th>
                    @if(auth()->user()->isAdmin())
                        <th class="text-end pe-3" style="width: 12%;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($assignment->assignmentOfficers as $officerAssigned)
                    @php
                        $officerUser = $officerAssigned->user;
                        $officerName = $officerUser ? $officerUser->name : 'Petugas Lapangan (Dihapus / Tidak Ditemukan)';
                        $officerPhone = $officerUser ? ($officerUser->phone ?? '-') : '-';
                        $officerInitial = strtoupper(substr($officerName, 0, 1));
                    @endphp
                    <tr class="officer-table-row" 
                        data-name="{{ strtolower($officerName) }}" 
                        data-phone="{{ strtolower($officerPhone !== '-' ? $officerPhone : '') }}" 
                        data-status="{{ $officerAssigned->status }}"
                        data-task="{{ strtolower($officerAssigned->specific_task ?? '') }}">
                        <td>
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <div class="user-avatar-circle">
                                    {{ $officerInitial }}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.86rem;" title="{{ $officerName }}">
                                        {{ $officerName }}
                                    </div>
                                    <div class="small text-muted text-truncate"><i class="bi bi-telephone me-1"></i> {{ $officerPhone }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small text-dark fw-medium text-truncate" title="{{ $officerAssigned->specific_task }}">
                                {{ $officerAssigned->specific_task ?? 'Pelaksana pendataan dan verifikasi lapangan.' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if($officerAssigned->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-check-circle-fill me-0.5"></i> Selesai
                                </span>
                            @elseif($officerAssigned->status === 'in_progress')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-play-circle-fill me-0.5"></i> Berjalan
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-clock me-0.5"></i> Menunggu
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="small text-muted fst-italic text-truncate" title="{{ $officerAssigned->progress_notes }}">
                                {{ $officerAssigned->progress_notes ?? 'Belum ada catatan progres.' }}
                            </div>
                        </td>
                        @if(auth()->user()->isAdmin())
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1 align-items-center justify-content-end">
                                    <!-- Edit Rincian Tugas -->
                                    <button type="button" 
                                            class="bento-action-btn btn-edit" 
                                            title="Edit Rincian Tugas" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditOfficerModal({{ json_encode($officerAssigned) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Batalkan / Hapus Penugasan -->
                                    <form method="POST" 
                                          action="{{ route('admin.assignments.officers.remove', [$assignment, $officerAssigned]) }}" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Batalkan penugasan untuk {{ addslashes($officerName) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bento-action-btn btn-delete" 
                                                title="Batalkan Penugasan" 
                                                data-bs-toggle="tooltip">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? '5' : '4' }}" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x display-5 text-secondary opacity-50 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Belum ada petugas yang ditugaskan pada kegiatan ini.</div>
                            <div class="small">Klik tombol "Tambah Petugas" di atas untuk menambahkan petugas penanggung jawab.</div>
                        </td>
                    </tr>
                @endforelse

                <!-- Baris Saat Pencarian Petugas Tidak Ditemukan -->
                <tr id="officerSearchNotFoundRow" style="display: none;">
                    <td colspan="{{ auth()->user()->isAdmin() ? '5' : '4' }}" class="text-center py-4 text-muted">
                        <i class="bi bi-search text-secondary opacity-50 d-block mb-1 fs-3"></i>
                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">Tidak ada petugas yang cocok</div>
                        <div class="small text-muted">Coba ubah kata kunci pencarian nama atau filter status.</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ==============================================================
     BAGIAN 2: RESPONDEN TERKAIT KEGIATAN INI (ANTI-GESER)
     ============================================================== -->
<div class="bento-table-card mb-4">
    <div class="bento-table-header">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #0D9488; width: 34px; height: 34px;">
                <i class="bi bi-file-earmark-person-fill"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0">Responden yang Terdata pada Kegiatan Ini</h6>
                <span class="text-muted" id="respondentCountText" style="font-size: 0.72rem;">Total {{ $assignment->respondents->count() }} responden terdaftar</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
            <!-- Filter Nama / Alamat Responden -->
            <div class="input-group input-group-sm" style="width: 240px;">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       id="filterRespondentSearch" 
                       class="form-control border-start-0 ps-1" 
                       placeholder="Cari nama responden / alamat..." 
                       style="font-size: 0.78rem;"
                       oninput="filterRespondentTable()">
                <button type="button" class="btn btn-outline-secondary border-start-0 text-muted" id="btnResetRespondentFilter" onclick="resetRespondentFilter()" title="Hapus pencarian" style="display: none;">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="table bento-table-fixed align-middle mb-0" id="respondentsTable">
            <thead>
                <tr>
                    <th style="width: 25%;">Nama Responden</th>
                    <th style="width: 20%;">Instansi</th>
                    <th style="width: 27%;">Alamat</th>
                    <th style="width: 16%;">Petugas Penginput</th>
                    <th style="width: 12%;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignment->respondents as $resp)
                    @php
                        $respOfficerName = $resp->officer ? $resp->officer->name : 'Petugas Tidak Diketahui';
                    @endphp
                    <tr class="respondent-table-row"
                        data-name="{{ strtolower($resp->name) }}"
                        data-agency="{{ strtolower($resp->agency ?? '') }}"
                        data-address="{{ strtolower($resp->address ?? '') }}"
                        data-officer="{{ strtolower($respOfficerName) }}">
                        <td>
                            <div class="fw-bold text-dark text-truncate" title="{{ $resp->name }}">{{ $resp->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border rounded-pill px-2 py-0.5 text-truncate d-inline-block" style="max-width: 100%;">
                                {{ $resp->agency }}
                            </span>
                        </td>
                        <td>
                            <div class="small text-secondary text-truncate" title="{{ $resp->address }}">{{ $resp->address }}</div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark text-truncate d-flex align-items-center gap-1">
                                <i class="bi bi-person text-secondary flex-shrink-0"></i>
                                <span class="text-truncate">{{ $respOfficerName }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $resp->input_date ? $resp->input_date->format('d/m/Y') : '-' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted small">
                            <i class="bi bi-inbox text-secondary opacity-50 d-block mb-1" style="font-size: 1.5rem;"></i>
                            Belum ada responden yang diinput untuk kegiatan ini.
                        </td>
                    </tr>
                @endforelse

                <!-- Baris Saat Pencarian Responden Tidak Ditemukan -->
                <tr id="respondentSearchNotFoundRow" style="display: none;">
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-search text-secondary opacity-50 d-block mb-1 fs-3"></i>
                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">Tidak ada responden yang cocok</div>
                        <div class="small text-muted">Coba ubah kata kunci pencarian nama atau alamat.</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@if(auth()->user()->isAdmin())
<!-- ==============================================================
     MODAL TUGASKAN PETUGAS BARU
     ============================================================== -->
<div class="modal fade" id="assignOfficerModal" tabindex="-1" aria-labelledby="assignOfficerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #F37021; width: 32px; height: 32px;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="assignOfficerModalLabel" style="font-size: 1rem;">
                        Tugaskan Petugas Baru
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.assignments.officers.assign', $assignment) }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">PILIH PETUGAS LAPANGAN <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Pilih Petugas --</option>
                            @foreach($availableOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->phone ?? $officer->username }})</option>
                            @endforeach
                        </select>
                        @if($availableOfficers->isEmpty())
                            <small class="text-danger mt-1 d-block" style="font-size: 0.72rem;">Semua petugas aktif telah ditugaskan pada kegiatan ini.</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">DESKRIPSI TUGAS SPESIFIK <span class="text-danger">*</span></label>
                        <textarea name="specific_task" class="form-control" rows="3" placeholder="Contoh: Pendataan responden wilayah Dusun Timur, target: 25 responden." required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-dark mb-1">CATATAN AWAL (OPSIONAL)</label>
                        <input type="text" name="progress_notes" class="form-control" placeholder="Contoh: Telah berkoordinasi dengan kepala desa setempat.">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3.5" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-bps-orange" {{ $availableOfficers->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-check2-circle me-1"></i> Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT RINCIAN TUGAS PETUGAS
     ============================================================== -->
<div class="modal fade" id="editOfficerTaskModal" tabindex="-1" aria-labelledby="editOfficerTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #D97706; width: 32px; height: 32px;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="editOfficerTaskModalLabel" style="font-size: 1rem;">
                        Perbarui Tugas Petugas
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editOfficerTaskForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">NAMA PETUGAS</label>
                        <input type="text" id="edit_officer_name" class="form-control bg-light" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">TUGAS SPESIFIK <span class="text-danger">*</span></label>
                        <textarea id="edit_officer_task" name="specific_task" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark mb-1">STATUS PENYELESAIAN <span class="text-danger">*</span></label>
                            <select id="edit_officer_status" name="status" class="form-select" required>
                                <option value="pending">Menunggu (Pending)</option>
                                <option value="in_progress">Sedang Berjalan (In Progress)</option>
                                <option value="completed">Selesai (Completed)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-dark mb-1">CATATAN PROGRES</label>
                        <textarea id="edit_officer_notes" name="progress_notes" class="form-control" rows="2" placeholder="Catatan perkembangan tugas..."></textarea>
                    </div>
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
@endif
@endsection

@push('scripts')
<script>
    function openEditOfficerModal(officerAssigned) {
        document.getElementById('editOfficerTaskForm').action = '/admin/assignments/{{ $assignment->id }}/officers/' + officerAssigned.id;
        document.getElementById('edit_officer_name').value = (officerAssigned.user && officerAssigned.user.name) ? officerAssigned.user.name : 'Petugas Lapangan';
        document.getElementById('edit_officer_task').value = officerAssigned.specific_task || '';
        document.getElementById('edit_officer_status').value = officerAssigned.status;
        document.getElementById('edit_officer_notes').value = officerAssigned.progress_notes || '';

        const modal = new bootstrap.Modal(document.getElementById('editOfficerTaskModal'));
        modal.show();
    }

    // Filter Petugas Lapangan (Nama, Telepon, Tugas, Status)
    function filterOfficerTable() {
        const query = (document.getElementById('filterOfficerName').value || '').toLowerCase().trim();
        const status = (document.getElementById('filterOfficerStatus').value || '').trim();
        const rows = document.querySelectorAll('.officer-table-row');
        const notFoundRow = document.getElementById('officerSearchNotFoundRow');
        const resetBtn = document.getElementById('btnResetOfficerFilter');
        const countText = document.getElementById('officerCountText');
        const totalCount = {{ $assignment->assignmentOfficers->count() }};
        let visibleCount = 0;

        if (resetBtn) {
            resetBtn.style.display = (query !== '') ? 'inline-block' : 'none';
        }

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const phone = row.getAttribute('data-phone') || '';
            const rowStatus = row.getAttribute('data-status') || '';
            const task = row.getAttribute('data-task') || '';

            const matchesQuery = !query || name.includes(query) || phone.includes(query) || task.includes(query);
            const matchesStatus = !status || rowStatus === status;

            if (matchesQuery && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (notFoundRow) {
            notFoundRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }

        if (countText) {
            if (query || status) {
                countText.innerHTML = `Menampilkan <strong class="text-primary">${visibleCount}</strong> dari ${totalCount} petugas`;
            } else {
                countText.innerText = `Total ${totalCount} petugas ditugaskan pada kegiatan ini`;
            }
        }
    }

    function resetOfficerFilter() {
        const input = document.getElementById('filterOfficerName');
        if (input) {
            input.value = '';
            input.focus();
        }
        filterOfficerTable();
    }

    // Filter Responden (Nama, Instansi, Alamat, Petugas)
    function filterRespondentTable() {
        const query = (document.getElementById('filterRespondentSearch').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.respondent-table-row');
        const notFoundRow = document.getElementById('respondentSearchNotFoundRow');
        const resetBtn = document.getElementById('btnResetRespondentFilter');
        const countText = document.getElementById('respondentCountText');
        const totalCount = {{ $assignment->respondents->count() }};
        let visibleCount = 0;

        if (resetBtn) {
            resetBtn.style.display = (query !== '') ? 'inline-block' : 'none';
        }

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const agency = row.getAttribute('data-agency') || '';
            const address = row.getAttribute('data-address') || '';
            const officer = row.getAttribute('data-officer') || '';

            const matches = !query || name.includes(query) || agency.includes(query) || address.includes(query) || officer.includes(query);

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (notFoundRow) {
            notFoundRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }

        if (countText) {
            if (query) {
                countText.innerHTML = `Menampilkan <strong class="text-teal text-success">${visibleCount}</strong> dari ${totalCount} responden`;
            } else {
                countText.innerText = `Total ${totalCount} responden terdaftar`;
            }
        }
    }

    function resetRespondentFilter() {
        const input = document.getElementById('filterRespondentSearch');
        if (input) {
            input.value = '';
            input.focus();
        }
        filterRespondentTable();
    }
</script>
@endpush
