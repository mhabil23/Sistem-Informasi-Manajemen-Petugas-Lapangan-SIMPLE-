@extends('layouts.app')

@section('title', 'Periode Pengisian SPKRT')
@section('page_title', 'Periode Pengisian SPKRT')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Periode SPKRT</li>
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

/* Stat Bento Item */
.bento-stat-item {
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}
.bento-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
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
.bento-table-fixed {
    table-layout: fixed;
    width: 100%;
    margin-bottom: 0;
    font-size: 0.82rem;
}
.bento-table-fixed th {
    background: #F8FAFC;
    color: #475569;
    font-weight: 700;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.85rem 0.75rem;
    border-bottom: 1px solid #E2E8F0;
    vertical-align: middle;
}
.bento-table-fixed td {
    padding: 0.85rem 0.75rem;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}
.bento-table-fixed tbody tr:hover td {
    background-color: #FBFDFF;
}

.pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    animation: pulse-green 1.8s infinite;
}
@keyframes pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- Header Section -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-range text-primary"></i> Pengaturan Periode Pengisian SPKRT
            </h3>
            <p class="text-muted small mb-0">
                Atur jadwal waktu dan kontrol status pengisian formulir SPKRT (Tanda Terima Responden) untuk petugas lapangan.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn-bps-orange shadow-sm" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
                <i class="bi bi-plus-circle-fill"></i> Tambah Periode Baru
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Terjadi kesalahan input:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Kontrol Tampil & Periode Menu Beranda Petugas -->
    @include('admin.partials._beranda_menu_control')

    <!-- Metric Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Periode -->
        <div class="col-6 col-md-3">
            <div class="bento-card-clean bento-stat-item">
                <div class="bento-stat-icon" style="background: #EFF6FF; color: #1D4ED8;">
                    <i class="bi bi-calendar-range"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Periode</div>
                    <div class="fs-4 fw-bolder text-dark">{{ $totalPeriods }}</div>
                </div>
            </div>
        </div>

        <!-- 2. Pengisian Dibuka -->
        <div class="col-6 col-md-3">
            <div class="bento-card-clean bento-stat-item">
                <div class="bento-stat-icon" style="background: #ECFDF5; color: #047857;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Dibuka Sekarang</div>
                    <div class="fs-4 fw-bolder text-success d-flex align-items-center gap-1.5">
                        {{ $totalActive }}
                        @if($totalActive > 0)
                            <span class="pulse-dot bg-success ms-1" title="Periode Aktif"></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Belum Dibuka -->
        <div class="col-6 col-md-3">
            <div class="bento-card-clean bento-stat-item">
                <div class="bento-stat-icon" style="background: #FFFBEB; color: #B45309;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Belum Dibuka</div>
                    <div class="fs-4 fw-bolder text-warning">{{ $totalNotStarted }}</div>
                </div>
            </div>
        </div>

        <!-- 4. Ditutup / Nonaktif -->
        <div class="col-6 col-md-3">
            <div class="bento-card-clean bento-stat-item">
                <div class="bento-stat-icon" style="background: #FEF2F2; color: #B91C1C;">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Telah Ditutup</div>
                    <div class="fs-4 fw-bolder text-danger">{{ $totalClosed }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bento-card-clean p-3 mb-4">
        <form method="GET" action="{{ route('admin.spkrt-periods.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" 
                           placeholder="Cari nama kegiatan atau catatan periode..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select name="status" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Semua Status Pengisian</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>🟢 Dibuka Sekarang</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>🟡 Belum Dibuka</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>🔴 Ditutup / Nonaktif</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 w-100">Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.spkrt-periods.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Main Table Card -->
    <div class="bento-table-card">
        <div class="bento-table-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #EFF6FF; color: #1D4ED8;">
                    <i class="bi bi-list-check"></i>
                </div>
                <h6 class="fw-bold text-dark mb-0">Daftar Periode Pengisian SPKRT ({{ $periods->total() }})</h6>
            </div>
            <span class="text-muted small">
                Sistem secara otomatis mengunci formulir jika tanggal selesai telah berakhir.
            </span>
        </div>

        <div class="table-responsive">
            <table class="table bento-table-fixed align-middle">
                <thead>
                    <tr>
                        <th style="width: 5%;">NO</th>
                        <th style="width: 28%;">NAMA KEGIATAN</th>
                        <th style="width: 22%;">JADWAL WAKTU</th>
                        <th style="width: 18%;">STATUS SISTEM (PETUGAS)</th>
                        <th style="width: 12%;" class="text-center">KONTROL ADMIN</th>
                        <th style="width: 15%;" class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $index => $period)
                        @php
                            $computedStatus = $period->currentStatus();
                            $statusMsg = $period->status_message;
                            $today = \Carbon\Carbon::today();
                        @endphp
                        <tr>
                            <td>{{ $periods->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $period->activity_name }}</div>
                                @if($period->description)
                                    <div class="text-muted small text-truncate" style="max-width: 320px;" title="{{ $period->description }}">
                                        {{ $period->description }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2 small">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-calendar-event me-1 text-primary"></i>
                                        {{ $period->start_date ? $period->start_date->format('d/m/Y') : '-' }}
                                    </span>
                                    <span class="text-muted">&rarr;</span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-calendar-check me-1 text-danger"></i>
                                        {{ $period->end_date ? $period->end_date->format('d/m/Y') : '-' }}
                                    </span>
                                </div>
                                @if($period->start_date && $period->end_date)
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                        Durasi: {{ $period->start_date->diffInDays($period->end_date) + 1 }} hari
                                        @if($computedStatus === 'active')
                                            &bull; <span class="text-success fw-bold">Sisa {{ $today->diffInDays($period->end_date, false) }} hari lagi</span>
                                        @elseif($computedStatus === 'not_started')
                                            &bull; <span class="text-warning fw-bold">Dimulai dalam {{ $today->diffInDays($period->start_date) }} hari</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($computedStatus === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1.5" style="font-size: 0.75rem;">
                                        <span class="pulse-dot bg-success"></span>
                                        <strong>{{ $statusMsg }}</strong>
                                    </span>
                                @elseif($computedStatus === 'not_started')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock-history"></i>
                                        <strong>{{ $statusMsg }}</strong>
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-lock-fill"></i>
                                        <strong>{{ $statusMsg }}</strong>
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.spkrt-periods.toggle-status', $period) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($period->is_active)
                                        <button type="submit" class="btn btn-sm btn-outline-success py-0.5 px-2.5 rounded-pill" style="font-size: 0.72rem;" title="Klik untuk nonaktifkan">
                                            <i class="bi bi-toggle-on fs-6 me-1 align-middle"></i> Aktif
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-secondary py-0.5 px-2.5 rounded-pill" style="font-size: 0.72rem;" title="Klik untuk aktifkan">
                                            <i class="bi bi-toggle-off fs-6 me-1 align-middle"></i> Nonaktif
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1" 
                                            title="Edit Periode" 
                                            onclick="openEditPeriodModal({{ json_encode($period) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.spkrt-periods.destroy', $period) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" title="Hapus Periode">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-x display-4 d-block mb-2 text-secondary opacity-50"></i>
                                    <div class="fw-bold">Belum ada periode pengisian SPKRT yang dibuat.</div>
                                    <p class="small text-muted mb-3">Klik tombol "Tambah Periode Baru" untuk membuka jadwal pengisian bagi petugas.</p>
                                    <button type="button" class="btn btn-sm btn-bps-orange" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
                                        <i class="bi bi-plus-circle"></i> Buat Periode Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($periods->hasPages())
            <div class="px-4 py-3 border-top bg-light">
                {{ $periods->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ==============================================================
     MODAL CREATE PERIODE SPKRT
     ============================================================== -->
<div class="modal fade" id="createPeriodModal" tabindex="-1" aria-labelledby="createPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.spkrt-periods.store') }}" class="modal-content border-0 shadow" style="border-radius: 18px; overflow: hidden;">
            @csrf
            <div class="modal-header bg-light border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark mb-0" id="createPeriodModalLabel">
                    <i class="bi bi-plus-circle-fill text-primary me-1.5"></i> Tambah Periode Pengisian SPKRT
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3.5">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="activity_name" class="form-control form-control-sm" list="assignmentSuggestions" placeholder="Contoh: Survei Pertanian SPKRT 2026" required>
                    <datalist id="assignmentSuggestions">
                        @foreach($assignments as $assign)
                            <option value="{{ $assign->title }}">
                        @endforeach
                    </datalist>
                    <div class="form-text" style="font-size: 0.72rem;">Masukkan nama kegiatan atau pilih dari daftar penugasan.</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control form-control-sm" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control form-control-sm" required value="{{ date('Y-m-d', strtotime('+14 days')) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Status Awal</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createIsActive" checked>
                        <label class="form-check-label small" for="createIsActive">Aktifkan periode (dapat diisi selama rentang tanggal)</label>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark">Catatan / Keterangan (Opsional)</label>
                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Instruksi tambahan untuk petugas..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2 px-3">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-bps-orange rounded-pill px-4">
                    <i class="bi bi-save me-1"></i> Simpan Periode
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT PERIODE SPKRT
     ============================================================== -->
<div class="modal fade" id="editPeriodModal" tabindex="-1" aria-labelledby="editPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editPeriodForm" method="POST" class="modal-content border-0 shadow" style="border-radius: 18px; overflow: hidden;">
            @csrf
            @method('PUT')
            <div class="modal-header bg-light border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark mb-0" id="editPeriodModalLabel">
                    <i class="bi bi-pencil-square text-primary me-1.5"></i> Edit Periode Pengisian SPKRT
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3.5">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="activity_name" id="editActivityName" class="form-control form-control-sm" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="editStartDate" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="editEndDate" class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Status Aktif/Nonaktif</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editIsActive">
                        <label class="form-check-label small" for="editIsActive">Periode Aktif</label>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark">Catatan / Keterangan (Opsional)</label>
                    <textarea name="description" id="editDescription" class="form-control form-control-sm" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2 px-3">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-bps-orange rounded-pill px-4">
                    <i class="bi bi-check2-circle me-1"></i> Perbarui Periode
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditPeriodModal(period) {
    document.getElementById('editPeriodForm').action = '/admin/spkrt-periods/' + period.id;
    document.getElementById('editActivityName').value = period.activity_name || '';
    
    // Format YYYY-MM-DD
    if (period.start_date) {
        document.getElementById('editStartDate').value = period.start_date.substring(0, 10);
    }
    if (period.end_date) {
        document.getElementById('editEndDate').value = period.end_date.substring(0, 10);
    }
    
    document.getElementById('editIsActive').checked = Boolean(period.is_active);
    document.getElementById('editDescription').value = period.description || '';
    
    const modal = new bootstrap.Modal(document.getElementById('editPeriodModal'));
    modal.show();
}
</script>
@endpush
@endsection
