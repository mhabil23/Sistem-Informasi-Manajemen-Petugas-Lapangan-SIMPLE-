@extends('layouts.app')

@section('title', 'Administrasi Pelatihan')
@section('page_title', 'Administrasi Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Administrasi Pelatihan</li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
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
        padding: 0.75rem 0.55rem;
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .bento-table tbody td {
        padding: 0.75rem 0.55rem;
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
    }
    .btn-bps-orange:hover, .btn-bps-orange:focus {
        background-color: #d95d13;
        border-color: #d95d13;
        color: #ffffff;
    }
    /* Compact Action Buttons */
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
        font-size: 0.8rem;
        padding: 0;
        flex-shrink: 0;
    }
    .bento-action-btn.btn-view { color: #0284C7; border-color: #BAE6FD; background: #F0F9FF; }
    .bento-action-btn.btn-view:hover { background: #0284C7; color: #FFFFFF; }
    .bento-action-btn.btn-edit { color: #D97706; border-color: #FDE68A; background: #FFFBEB; }
    .bento-action-btn.btn-edit:hover { background: #D97706; color: #FFFFFF; }
    .bento-action-btn.btn-delete { color: #DC2626; border-color: #FECACA; background: #FEF2F2; }
    .bento-action-btn.btn-delete:hover { background: #DC2626; color: #FFFFFF; }
    @media (max-width: 767.98px) {
        .bento-table thead {
            display: none;
        }
        .bento-table, .bento-table tbody, .bento-table tr, .bento-table td {
            display: block;
            width: 100% !important;
        }
        .bento-table tr {
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.5rem;
            background: #fff;
        }
        .bento-table td {
            text-align: right;
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .bento-table td:last-child {
            border-bottom: none;
        }
        .bento-table td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: #64748b;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Search & Filter Card -->
<div class="bento-card mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.trainings.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
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
                <button type="submit" class="btn btn-bps-orange flex-fill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.trainings.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="bento-card mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #F37021 0%, #d95d13 100%);">
                <i class="bi bi-mortarboard fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Daftar Kegiatan Pelatihan & Bimtek ({{ $trainings->total() }})</h5>
                <span class="badge bg-light text-secondary border mt-1">Halaman {{ $trainings->currentPage() }} dari {{ $trainings->lastPage() ?: 1 }}</span>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#createTrainingModal" style="font-size: 0.85rem;">
                <i class="bi bi-plus-circle me-1"></i> Buat Agenda Pelatihan
            </button>
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 29%;">NAMA KEGIATAN PELATIHAN</th>
                    <th style="width: 19%;">JADWAL PELAKSANAAN</th>
                    <th style="width: 19%;">LOKASI PELATIHAN</th>
                    <th class="text-center" style="width: 10%;">PESERTA</th>
                    <th class="text-center" style="width: 8%;">STATUS</th>
                    <th class="text-end pe-3" style="width: 15%;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trainings as $tr)
                    <tr>
                        <td data-label="Nama Kegiatan Pelatihan">
                            <a href="{{ route('admin.trainings.show', $tr) }}" class="fw-bold text-primary text-decoration-none d-block">
                                {{ $tr->title }}
                            </a>
                            <small class="text-secondary text-truncate d-block" style="max-width: 280px;">
                                {{ $tr->description ?? 'Tidak ada deskripsi tambahan' }}
                            </small>
                        </td>
                        <td data-label="Jadwal Pelaksanaan">
                            <div class="small fw-semibold text-dark">
                                <i class="bi bi-calendar-check text-primary me-1"></i>
                                {{ $tr->start_date->format('d/m/Y') }} - {{ $tr->end_date->format('d/m/Y') }}
                            </div>
                        </td>
                        <td data-label="Lokasi Pelatihan">
                            <div class="small text-secondary">
                                <i class="bi bi-geo-alt text-danger me-1"></i> {{ $tr->location }}
                            </div>
                        </td>
                        <td class="text-center" data-label="Peserta">
                            <span class="badge bg-light text-primary border px-2 py-1">
                                <i class="bi bi-people me-1"></i> {{ $tr->participants_count }} / {{ $tr->quota ?: '∞' }}
                            </span>
                        </td>
                        <td class="text-center" data-label="Status">
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
                        <td class="text-end text-nowrap pe-3" data-label="Aksi">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                <!-- Kelola Peserta & Daftar Hadir -->
                                <a href="{{ route('admin.trainings.show', $tr) }}" 
                                   class="bento-action-btn btn-view" 
                                   title="Kelola Peserta & Presensi" 
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-person-gear"></i>
                                </a>

                                <!-- Edit -->
                                <button type="button" 
                                        class="bento-action-btn btn-edit" 
                                        title="Edit Kegiatan" 
                                        data-bs-toggle="tooltip"
                                        onclick="openEditTrainingModal({{ json_encode($tr) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Hapus -->
                                <form method="POST" action="{{ route('admin.trainings.destroy', $tr) }}" class="d-inline" onsubmit="return confirm('Hapus kegiatan pelatihan {{ addslashes($tr->title) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bento-action-btn btn-delete" 
                                            title="Hapus Pelatihan" 
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-mortarboard fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                            <div class="fw-semibold">Belum ada agenda pelatihan / bimtek.</div>
                            <div class="small">Klik tombol "Buat Agenda Pelatihan" untuk menjadwalkan kegiatan pelatihan baru.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($trainings->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
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
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createTrainingModalLabel">
                    <i class="bi bi-mortarboard text-primary me-2"></i> Buat Agenda Pelatihan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.trainings.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA / JUDUL PELATIHAN <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Bimbingan Teknis Metodologi Survei & Pendataan Lapangan SPKRT" required>
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
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy me-1"></i> Simpan Agenda Pelatihan
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
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editTrainingModalLabel">
                    <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Pelatihan
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
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy me-1"></i> Simpan Perubahan
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
