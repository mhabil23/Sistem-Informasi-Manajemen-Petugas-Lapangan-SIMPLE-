@extends('layouts.app')

@section('title', 'Kegiatan & Penugasan')
@section('page_title', 'Kegiatan & Penugasan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">SIMPLE</a></li>
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

/* 4 Bento KPI Stat Cards */
.bento-stat-card {
    background: #FFFFFF;
    border-radius: 18px;
    border: 1px solid #E2E8F0;
    padding: 1.15rem 1.25rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.02);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 1rem;
    height: 100%;
}
.bento-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
}
.bento-stat-icon-circle,
.bento-stat-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}
.bento-stat-number {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0F172A;
    line-height: 1.1;
    margin-bottom: 2px;
}
.bento-stat-label-text {
    font-size: 0.74rem;
    font-weight: 700;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 2px;
}
.bento-stat-desc-text {
    font-size: 0.72rem;
    color: #94A3B8;
    margin: 0;
    line-height: 1.2;
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

/* Table Card */
.bento-table-card {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    overflow: hidden;
}
.bento-table-header {
    padding: 1.15rem 1.35rem;
    border-bottom: 1px solid #E2E8F0;
    background: #FAFAFA;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.bento-table th {
    background: #F8FAFC;
    color: #475569;
    font-weight: 700;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.95rem 1.15rem;
    border-bottom: 1px solid #E2E8F0;
}
.bento-table td {
    padding: 1rem 1.15rem;
    vertical-align: middle;
    border-bottom: 1px solid #F1F5F9;
    font-size: 0.84rem;
}
.bento-table tbody tr:hover {
    background-color: #F8FAFC;
}

/* Action buttons in table */
.bento-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #E2E8F0;
    background: #FFFFFF;
    transition: all 0.15s ease;
    text-decoration: none;
    font-size: 0.85rem;
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
</style>
@endpush

@section('content')
<!-- Header & Quick Actions -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.015em;">Kegiatan &amp; Pembagian Tugas</h4>
        <p class="text-muted small mb-0">Kelola kegiatan administrasi resmi, tentukan jadwal &amp; lokasi, serta bagikan tugas kepada masing-masing petugas lapangan.</p>
    </div>
    <div>
        @if(auth()->user()->isAdmin())
            <button type="button" class="btn-bps-orange shadow-sm" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                <i class="bi bi-calendar-plus-fill me-1"></i> Buat Kegiatan Baru
            </button>
        @endif
    </div>
</div>

<!-- 4 Bento KPI Stat Cards -->
<div class="row g-3 mb-4">
    <!-- 1. Total Kegiatan -->
    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon-circle" style="background: #E0F2FE; color: #0284C7;">
                <i class="bi bi-collection-fill"></i>
            </div>
            <div>
                <div class="bento-stat-label-text">Total Kegiatan</div>
                <div class="bento-stat-number">{{ number_format($totalAssignments) }}</div>
                <div class="bento-stat-desc-text">Semua penugasan resmi</div>
            </div>
        </div>
    </div>

    <!-- 2. Sedang Berjalan -->
    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon-circle" style="background: #FFF4ED; color: #F37021;">
                <i class="bi bi-play-circle-fill"></i>
            </div>
            <div>
                <div class="bento-stat-label-text">Sedang Berjalan</div>
                <div class="bento-stat-number text-warning-emphasis">{{ number_format($totalOngoing) }}</div>
                <div class="bento-stat-desc-text">Aktif di lapangan</div>
            </div>
        </div>
    </div>

    <!-- 3. Selesai -->
    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon-circle" style="background: #DCFCE7; color: #16A34A;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="bento-stat-label-text">Selesai</div>
                <div class="bento-stat-number text-success">{{ number_format($totalCompleted) }}</div>
                <div class="bento-stat-desc-text">Telah tuntas</div>
            </div>
        </div>
    </div>

    <!-- 4. Draft / Persiapan -->
    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon-circle" style="background: #F1F5F9; color: #64748B;">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div>
                <div class="bento-stat-label-text">Draft / Persiapan</div>
                <div class="bento-stat-number">{{ number_format($totalDraft) }}</div>
                <div class="bento-stat-desc-text">Belum dimulai</div>
            </div>
        </div>
    </div>
</div>

<!-- Bento Search & Filter Card -->
<div class="bento-card-clean p-3 mb-4">
    <form method="GET" action="{{ route('admin.assignments.index') }}" class="row g-2 align-items-center">
        <div class="col-lg-7 col-md-6">
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

        <div class="col-lg-3 col-md-4">
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Sedang Berjalan</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="col-lg-2 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-bps-orange flex-fill justify-content-center py-2">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.assignments.index') }}" class="btn btn-outline-secondary rounded-pill px-3 d-flex align-items-center justify-content-center" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Bento Table Card -->
<div class="bento-table-card mb-4">
    <div class="bento-table-header">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: var(--bps-orange, #F37021); width: 34px; height: 34px;">
                <i class="bi bi-briefcase-fill"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">
                    Daftar Kegiatan &amp; Penugasan Lapangan
                </h6>
                <span class="text-muted" style="font-size: 0.72rem;">Total {{ $assignments->total() }} kegiatan terdaftar dalam sistem</span>
            </div>
        </div>
        <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill" style="font-size: 0.72rem;">
            Halaman {{ $assignments->currentPage() }} dari {{ max(1, $assignments->lastPage()) }}
        </span>
    </div>

    <div class="table-responsive">
        <table class="table bento-table align-middle mb-0">
            <thead>
                <tr>
                    <th style="min-width: 280px;"><i class="bi bi-journal-text me-1 text-muted"></i> Nama Kegiatan</th>
                    <th style="min-width: 170px;"><i class="bi bi-calendar3 me-1 text-muted"></i> Jadwal Pelaksanaan</th>
                    <th style="min-width: 160px;"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> Lokasi Wilayah</th>
                    <th class="text-center" style="min-width: 110px;"><i class="bi bi-people-fill me-1 text-primary"></i> Petugas</th>
                    <th class="text-center" style="min-width: 130px;"><i class="bi bi-pie-chart-fill me-1 text-success"></i> Responden</th>
                    <th class="text-center" style="min-width: 110px;"><i class="bi bi-activity me-1 text-warning"></i> Status</th>
                    <th class="text-end pe-3" style="min-width: 110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $item)
                    <tr>
                        <td>
                            <a href="{{ route('admin.assignments.show', $item) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate mb-0.5" style="max-width: 340px; font-size: 0.88rem;" title="{{ $item->title }}">
                                {{ $item->title }}
                            </a>
                            <small class="text-muted text-truncate d-block" style="max-width: 340px; font-size: 0.75rem;">
                                {{ Str::limit($item->description, 60) ?? 'Tidak ada deskripsi tambahan.' }}
                            </small>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark d-flex align-items-center gap-1.5">
                                <i class="bi bi-calendar-event text-secondary"></i>
                                <span>{{ $item->start_date->format('d/m/Y') }} &ndash; {{ $item->end_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="text-muted mt-0.5" style="font-size: 0.72rem;">
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5">
                                    {{ $item->start_date->diffInDays($item->end_date) + 1 }} Hari Kerja
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark d-flex align-items-center gap-1">
                                <i class="bi bi-geo-alt-fill text-danger"></i>
                                <span>{{ $item->location }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.74rem;">
                                <i class="bi bi-people-fill me-1"></i> {{ $item->officers_count }} Petugas
                            </span>
                        </td>
                        <td class="text-center">
                            <div>
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-pill font-monospace" style="font-size: 0.74rem;">
                                    {{ $item->respondents_count }}
                                    @if($item->target_respondents > 0)
                                        <span class="text-muted">/ {{ number_format($item->target_respondents) }}</span>
                                    @endif
                                </span>
                            </div>
                            @if($item->target_respondents > 0)
                                @php
                                    $pct = round(($item->respondents_count / $item->target_respondents) * 100);
                                @endphp
                                <div class="progress mt-1.5 mx-auto" style="height: 5px; width: 75px; border-radius: 999px;" title="{{ $pct }}% dari target {{ number_format($item->target_respondents) }} responden">
                                    <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning') }}" style="width: {{ min(100, $pct) }}%;"></div>
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->status === 'ongoing')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    <i class="bi bi-play-circle-fill me-1"></i> Berjalan
                                </span>
                            @elseif($item->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                </span>
                            @elseif($item->status === 'draft')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    <i class="bi bi-pencil-square me-1"></i> Draft
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    Batal
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3 text-nowrap">
                            <div class="d-inline-flex gap-1.5 align-items-center">
                                <!-- Detail & Pembagian Petugas -->
                                <a href="{{ route('admin.assignments.show', $item) }}" 
                                   class="bento-action-btn btn-view" 
                                   title="Detail &amp; Pembagian Tugas" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-list-check"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
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
                        <td colspan="7" class="text-center py-5 text-muted">
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
            <form method="POST" action="{{ route('admin.assignments.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">NAMA / JUDUL KEGIATAN <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Pendataan Lapangan Profil Responden Pertanian 2026" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', now()->addDays(14)->toDateString()) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-dark mb-1">LOKASI / WILAYAH PELAKSANAAN <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Contoh: Kecamatan Sukamaju & Sekitarnya" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">TARGET RESPONDEN (KUOTA)</label>
                            <div class="input-group">
                                <input type="number" name="target_respondents" class="form-control" value="{{ old('target_respondents', 100) }}" min="0" placeholder="Contoh: 680">
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

    function openDeleteAssignmentModal(id, title) {
        document.getElementById('deleteAssignmentForm').action = '/admin/assignments/' + id;
        document.getElementById('delete_assignment_title').innerText = title;

        const modal = new bootstrap.Modal(document.getElementById('deleteAssignmentModal'));
        modal.show();
    }
</script>
@endpush
