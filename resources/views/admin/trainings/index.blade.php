@extends('layouts.app')

@section('title', 'Administrasi Pelatihan')
@section('page_title', 'Administrasi Pelatihan & Bimtek')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Administrasi Pelatihan</li>
@endsection

@section('content')
<!-- Header & Actions -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Administrasi Pelatihan & Bimtek</h4>
        <p class="text-muted small mb-0">Kelola agenda pelatihan, rekapitulasi peserta (DPW, Magang Mitra, Pegawai), presensi daftar hadir, serta generate dokumen administrasi.</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createTrainingModal">
            <i class="fa-solid fa-calendar-plus me-1"></i> Buat Agenda Pelatihan
        </button>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Pelatihan</div>
                <div class="sp-stat-value">{{ number_format($totalEvents) }}</div>
                <div class="sp-stat-desc">Semua kegiatan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Peserta</div>
                <div class="sp-stat-value text-primary">{{ number_format($totalParticipants) }}</div>
                <div class="sp-stat-desc">Terdaftar di sistem</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-play"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Sedang Berjalan</div>
                <div class="sp-stat-value text-warning">{{ number_format($ongoingCount) }}</div>
                <div class="sp-stat-desc">Aktif sekarang</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Pelatihan Selesai</div>
                <div class="sp-stat-value text-success">{{ number_format($completedCount) }}</div>
                <div class="sp-stat-desc">Arsip dokumentasi</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-4">
    <div class="sp-card-body p-3">
        <form method="GET" action="{{ route('admin.trainings.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari judul pelatihan, lokasi, atau deskripsi...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all">-- Semua Status Pelatihan --</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Telah Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.trainings.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
            <i class="fa-solid fa-graduation-cap text-primary"></i>
            Daftar Kegiatan Pelatihan & Bimtek ({{ $trainings->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 32%;">Nama Kegiatan Pelatihan</th>
                        <th style="width: 20%;">Jadwal Pelaksanaan</th>
                        <th style="width: 22%;">Lokasi Pelatihan</th>
                        <th class="text-center" style="width: 9%;">Peserta</th>
                        <th class="text-center" style="width: 9%;">Status</th>
                        <th class="text-end" style="width: 8%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $tr)
                        <tr>
                            <td>
                                <a href="{{ route('admin.trainings.show', $tr) }}" class="fw-bold text-primary text-decoration-none d-block">
                                    {{ $tr->title }}
                                </a>
                                <small class="text-secondary text-truncate d-block" style="max-width: 250px;">
                                    {{ $tr->description ?? 'Tidak ada deskripsi tambahan' }}
                                </small>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <i class="fa-regular fa-calendar-check text-primary me-1"></i>
                                    {{ $tr->start_date->format('d/m/Y') }} - {{ $tr->end_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="small text-secondary">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $tr->location }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border px-2 py-1">
                                    <i class="fa-solid fa-user-group me-1"></i> {{ $tr->participants_count }} / {{ $tr->quota ?: '∞' }} Peserta
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'upcoming' => 'bg-secondary',
                                        'ongoing' => 'bg-warning text-dark',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                    ];
                                    $statusLabels = [
                                        'upcoming' => 'Akan Datang',
                                        'ongoing' => 'Sedang Berjalan',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="badge {{ $statusBadges[$tr->status] ?? 'bg-secondary' }} px-2 py-1">
                                    {{ $statusLabels[$tr->status] ?? ucfirst($tr->status) }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Kelola Peserta & Daftar Hadir -->
                                    <a href="{{ route('admin.trainings.show', $tr) }}" 
                                       class="btn btn-light border sp-action-btn text-primary" 
                                       title="Kelola Peserta & Presensi" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-users-gear"></i>
                                    </a>

                                    <!-- Edit -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-warning" 
                                            title="Edit Kegiatan" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditTrainingModal({{ json_encode($tr) }})">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('admin.trainings.destroy', $tr) }}" class="d-inline" onsubmit="return confirm('Hapus kegiatan pelatihan {{ addslashes($tr->title) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-light border sp-action-btn text-danger" 
                                                title="Hapus Pelatihan" 
                                                data-bs-toggle="tooltip">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-graduation-cap fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada agenda pelatihan / bimtek.</div>
                                <div class="small">Klik tombol "Buat Agenda Pelatihan" untuk menjadwalkan kegiatan pelatihan baru.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($trainings->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $trainings->firstItem() }} sampai {{ $trainings->lastItem() }} dari {{ $trainings->total() }} kegiatan
            </div>
            <div>
                {{ $trainings->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL TAMBAH PELATIHAN
     ============================================================== -->
<div class="modal fade" id="createTrainingModal" tabindex="-1" aria-labelledby="createTrainingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createTrainingModalLabel">
                    <i class="fa-solid fa-graduation-cap text-primary me-2"></i> Buat Agenda Pelatihan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.trainings.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA / JUDUL PELATIHAN <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Bimbingan Teknis Metodologi Survei & Pendataan Lapangan SP4RT" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">LOKASI / RUANG PELATIHAN <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: Aula Gedung Dinas Pertanian Lt. 2 / Zoom Meeting" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">TARGET KUOTA PESERTA</label>
                            <input type="number" name="quota" class="form-control" value="50" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">STATUS PELATIHAN <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="upcoming">Akan Datang</option>
                            <option value="ongoing">Sedang Berlangsung</option>
                            <option value="completed">Telah Selesai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">DESKRIPSI & MATERI PELATIHAN</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Uraian singkat tujuan pelatihan, materi pokok, dan instruktur..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Agenda Pelatihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT PELATIHAN
     ============================================================== -->
<div class="modal fade" id="editTrainingModal" tabindex="-1" aria-labelledby="editTrainingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editTrainingModalLabel">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit Data Pelatihan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTrainingForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA / JUDUL PELATIHAN <span class="text-danger">*</span></label>
                        <input type="text" id="edit_tr_title" name="title" class="form-control" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" id="edit_tr_start" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" id="edit_tr_end" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">LOKASI / RUANG PELATIHAN <span class="text-danger">*</span></label>
                            <input type="text" id="edit_tr_location" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">TARGET KUOTA PESERTA</label>
                            <input type="number" id="edit_tr_quota" name="quota" class="form-control" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">STATUS PELATIHAN <span class="text-danger">*</span></label>
                        <select id="edit_tr_status" name="status" class="form-select" required>
                            <option value="upcoming">Akan Datang</option>
                            <option value="ongoing">Sedang Berlangsung</option>
                            <option value="completed">Telah Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">DESKRIPSI</label>
                        <textarea id="edit_tr_desc" name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditTrainingModal(tr) {
        document.getElementById('editTrainingForm').action = '/admin/trainings/' + tr.id;
        document.getElementById('edit_tr_title').value = tr.title;
        document.getElementById('edit_tr_start').value = tr.start_date ? tr.start_date.substring(0, 10) : '';
        document.getElementById('edit_tr_end').value = tr.end_date ? tr.end_date.substring(0, 10) : '';
        document.getElementById('edit_tr_location').value = tr.location;
        document.getElementById('edit_tr_quota').value = tr.quota || 0;
        document.getElementById('edit_tr_status').value = tr.status;
        document.getElementById('edit_tr_desc').value = tr.description || '';

        const modal = new bootstrap.Modal(document.getElementById('editTrainingModal'));
        modal.show();
    }
</script>
@endpush

