@extends('layouts.app')

@section('title', 'Kegiatan & Penugasan')
@section('page_title', 'Kegiatan & Penugasan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kegiatan & Penugasan</li>
@endsection

@section('content')
<!-- Header & Quick Actions -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Kegiatan & Pembagian Tugas</h4>
        <p class="text-muted small mb-0">Kelola kegiatan administrasi resmi, tentukan jadwal & lokasi, serta bagikan tugas kepada masing-masing petugas lapangan.</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
            <i class="fa-solid fa-calendar-plus me-1"></i> Buat Kegiatan Baru
        </button>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Kegiatan</div>
                <div class="sp-stat-value">{{ number_format($totalAssignments) }}</div>
                <div class="sp-stat-desc">Semua penugasan resmi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-spinner"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Sedang Berjalan</div>
                <div class="sp-stat-value text-warning">{{ number_format($totalOngoing) }}</div>
                <div class="sp-stat-desc">Aktif di lapangan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Selesai</div>
                <div class="sp-stat-value text-success">{{ number_format($totalCompleted) }}</div>
                <div class="sp-stat-desc">Telah tuntas</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #f1f5f9; color: #64748b;">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Draft / Persiapan</div>
                <div class="sp-stat-value">{{ number_format($totalDraft) }}</div>
                <div class="sp-stat-desc">Belum dimulai</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-3">
    <div class="sp-card-body p-2.5">
        <form method="GET" action="{{ route('admin.assignments.index') }}" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari judul kegiatan, lokasi wilayah, atau uraian deskripsi...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.assignments.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="sp-card mb-3">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-briefcase text-primary"></i>
            Daftar Kegiatan & Penugasan ({{ $assignments->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 28%;">Nama Kegiatan</th>
                        <th style="width: 17%;">Jadwal Pelaksanaan</th>
                        <th style="width: 20%;">Lokasi</th>
                        <th class="text-center" style="width: 10%;">Petugas</th>
                        <th class="text-center" style="width: 9%;">Responden</th>
                        <th class="text-center" style="width: 8%;">Status</th>
                        <th class="text-end" style="width: 8%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $item)
                        <tr>
                            <td>
                                <a href="{{ route('admin.assignments.show', $item) }}" class="fw-bold text-dark text-decoration-none text-truncate d-block" style="max-width: 320px;" title="{{ $item->title }}">
                                    {{ $item->title }}
                                </a>
                                <small class="text-muted text-truncate d-block" style="max-width: 320px;">
                                    {{ Str::limit($item->description, 50) ?? 'Tidak ada deskripsi tambahan.' }}
                                </small>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                    {{ $item->start_date->format('d/m/Y') }} - {{ $item->end_date->format('d/m/Y') }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ $item->start_date->diffInDays($item->end_date) + 1 }} Hari Kerja
                                </div>
                            </td>
                            <td>
                                <div class="small text-secondary">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                    {{ $item->location }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border px-2 py-1">
                                    <i class="fa-solid fa-user-gear me-1"></i> {{ $item->officers_count }} Petugas
                                </span>
                            </td>
                            <td class="text-center">
                                <div>
                                    <span class="badge bg-light text-primary border px-2 py-1">
                                        <i class="fa-solid fa-id-card me-1"></i> {{ $item->respondents_count }}
                                        @if($item->target_respondents > 0)
                                            <span class="text-muted">/ {{ number_format($item->target_respondents) }}</span>
                                        @endif
                                    </span>
                                </div>
                                @if($item->target_respondents > 0)
                                    @php
                                        $pct = round(($item->respondents_count / $item->target_respondents) * 100);
                                    @endphp
                                    <div class="progress mt-1 mx-auto" style="height: 4px; width: 68px;" title="{{ $pct }}% dari target {{ number_format($item->target_respondents) }} responden">
                                        <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning') }}" style="width: {{ min(100, $pct) }}%;"></div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($item->status === 'ongoing')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Berjalan
                                    </span>
                                @elseif($item->status === 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-check me-1"></i> Selesai
                                    </span>
                                @elseif($item->status === 'draft')
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        <i class="fa-solid fa-file-pen me-1"></i> Draft
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        Batal
                                    </span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Detail & Pembagian Petugas -->
                                    <a href="{{ route('admin.assignments.show', $item) }}" 
                                       class="btn btn-light border sp-action-btn text-primary" 
                                       title="Detail & Pembagian Tugas" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-list-check"></i>
                                    </a>

                                    <!-- Edit -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-warning" 
                                            title="Edit Kegiatan" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditAssignmentModal({{ json_encode($item) }})">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- Hapus -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-danger" 
                                            title="Hapus Kegiatan" 
                                            data-bs-toggle="tooltip"
                                            onclick="openDeleteAssignmentModal({{ $item->id }}, '{{ addslashes($item->title) }}')">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada kegiatan ditemukan.</div>
                                <div class="small">Klik tombol "Buat Kegiatan Baru" untuk memulai penugasan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($assignments->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $assignments->firstItem() }} sampai {{ $assignments->lastItem() }} dari {{ $assignments->total() }} kegiatan
            </div>
            <div>
                {{ $assignments->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL BUAT KEGIATAN BARU
     ============================================================== -->
<div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createAssignmentModalLabel">
                    <i class="fa-solid fa-calendar-plus text-primary me-2"></i> Buat Kegiatan & Penugasan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.assignments.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA / JUDUL KEGIATAN <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Pendataan Lapangan Profil Responden Pertanian 2026" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', now()->addDays(14)->toDateString()) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">LOKASI / WILAYAH PELAKSANAAN <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Contoh: Kecamatan Sukamaju & Sekitarnya" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">
                                TARGET RESPONDEN (KUOTA)
                            </label>
                            <div class="input-group">
                                <input type="number" name="target_respondents" class="form-control" value="{{ old('target_respondents', 100) }}" min="0" placeholder="Contoh: 680">
                                <span class="input-group-text bg-light small text-muted">Sampel</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS KEGIATAN <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="ongoing" selected>Sedang Berjalan</option>
                                <option value="draft">Draft / Rencana</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">URAIAN / DESKRIPSI KEGIATAN</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan tujuan, sasaran responden, dan instruksi umum kegiatan..."></textarea>
                    </div>

                    <div class="p-3 bg-light rounded border">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <label class="form-label small fw-semibold text-secondary mb-0">
                                <i class="fa-solid fa-users text-primary me-1"></i> PILIH PETUGAS AWAL (OPSIONAL)
                            </label>
                            <div class="d-flex align-items-center gap-1">
                                <input type="text" id="officerSearchInput" class="form-control form-control-sm py-0.5 px-2" placeholder="Cari nama petugas..." style="width: 170px; font-size: 0.75rem;" onkeyup="filterOfficers(this.value)">
                                <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.72rem;" onclick="toggleAllOfficers(true)">Pilih Semua</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.72rem;" onclick="toggleAllOfficers(false)">Batal</button>
                            </div>
                        </div>
                        <div class="row g-2" id="officersContainer" style="max-height: 160px; overflow-y: auto;">
                            @forelse($officers as $officer)
                                <div class="col-md-6 officer-check-item" data-name="{{ strtolower($officer->name) }} {{ strtolower($officer->phone ?? '') }}">
                                    <div class="form-check">
                                        <input class="form-check-input officer-checkbox" type="checkbox" name="officer_ids[]" value="{{ $officer->id }}" id="officer_{{ $officer->id }}">
                                        <label class="form-check-label small" for="officer_{{ $officer->id }}">
                                            <strong>{{ $officer->name }}</strong> ({{ $officer->phone ?? $officer->username }})
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
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Kegiatan
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editAssignmentModalLabel">
                    <i class="fa-regular fa-pen-to-square text-warning me-2"></i> Edit Data Kegiatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAssignmentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA / JUDUL KEGIATAN <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title" name="title" class="form-control" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" id="edit_start_date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" id="edit_end_date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">LOKASI / WILAYAH PELAKSANAAN <span class="text-danger">*</span></label>
                            <input type="text" id="edit_location" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">TARGET RESPONDEN (KUOTA)</label>
                            <div class="input-group">
                                <input type="number" id="edit_target_respondents" name="target_respondents" class="form-control" min="0" placeholder="Contoh: 680">
                                <span class="input-group-text bg-light small text-muted">Sampel</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS KEGIATAN <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="ongoing">Sedang Berjalan</option>
                                <option value="completed">Selesai</option>
                                <option value="draft">Draft</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">URAIAN / DESKRIPSI KEGIATAN</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
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

<!-- ==============================================================
     MODAL HAPUS KEGIATAN
     ============================================================== -->
<div class="modal fade" id="deleteAssignmentModal" tabindex="-1" aria-labelledby="deleteAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                </div>
                <h5 class="fw-bold mb-2">Hapus Kegiatan?</h5>
                <p class="text-muted small mb-4">
                    Apakah Anda yakin ingin menghapus kegiatan <strong id="delete_assignment_title" class="text-dark"></strong>? Seluruh data penugasan terkait akan diarsipkan menggunakan soft delete.
                </p>
                <form id="deleteAssignmentForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    function openEditAssignmentModal(item) {
        document.getElementById('editAssignmentForm').action = '/admin/assignments/' + item.id;
        document.getElementById('edit_title').value = item.title;
        document.getElementById('edit_start_date').value = item.start_date.substring(0, 10);
        document.getElementById('edit_end_date').value = item.end_date.substring(0, 10);
        document.getElementById('edit_location').value = item.location;
        document.getElementById('edit_target_respondents').value = item.target_respondents || 0;
        document.getElementById('edit_status').value = item.status;
        document.getElementById('edit_description').value = item.description || '';

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

