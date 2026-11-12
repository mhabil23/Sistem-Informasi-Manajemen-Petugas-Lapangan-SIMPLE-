@extends('layouts.app')

@section('title', 'Jadwal Kegiatan')
@section('page_title', 'Jadwal Kegiatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Jadwal Kegiatan</li>
@endsection

@section('content')
<!-- Header & Quick Actions -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Jadwal Kegiatan & Agenda Lapangan</h4>
        <p class="text-muted small mb-0">Atur kalender kegiatan, tentukan waktu mulai & selesai, lokasi pertemuan, serta petugas penanggung jawab agenda.</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                <i class="fa-regular fa-calendar-plus me-1"></i> Buat Jadwal Baru
            </button>
        @endif
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-regular fa-calendar-days"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Jadwal</div>
                <div class="sp-stat-value">{{ number_format($totalSchedules) }}</div>
                <div class="sp-stat-desc">Semua agenda tercatat</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Jadwal Mendatang</div>
                <div class="sp-stat-value text-primary">{{ number_format($totalUpcoming) }}</div>
                <div class="sp-stat-desc">Akan datang</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Sedang Berlangsung</div>
                <div class="sp-stat-value text-warning">{{ number_format($totalInProgress) }}</div>
                <div class="sp-stat-desc">Aktif hari ini</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Selesai</div>
                <div class="sp-stat-value text-success">{{ number_format($totalCompleted) }}</div>
                <div class="sp-stat-desc">Agenda tuntas</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-3">
    <div class="sp-card-body p-2.5">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari judul agenda, lokasi, atau catatan...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="officer_id" class="form-select form-select-sm">
                    <option value="">-- Semua Petugas --</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'officer_id']))
                    <a href="{{ route('admin.schedules.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
            <i class="fa-solid fa-calendar-check text-primary"></i>
            Daftar Agenda Kegiatan ({{ $schedules->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 17%;">Waktu &amp; Tanggal</th>
                        <th style="width: 25%;">Nama Agenda</th>
                        <th style="width: 18%;">Kegiatan Terkait</th>
                        <th style="width: 15%;">Petugas</th>
                        <th style="width: 15%;">Lokasi</th>
                        <th class="text-center" style="width: 5%;">Status</th>
                        <th class="text-end" style="width: 5%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark small">
                                    <i class="fa-regular fa-calendar text-primary me-1"></i>
                                    {{ $item->start_time->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    {{ $item->start_time->format('H:i') }} - {{ $item->end_time->format('H:i') }} WIB
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 280px;" title="{{ $item->title }}">
                                    {{ $item->title }}
                                </div>
                                @if($item->notes)
                                    <small class="text-muted text-truncate d-block" style="max-width: 280px;">
                                        {{ $item->notes }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="small fw-medium text-dark text-truncate d-block" style="max-width: 180px;">
                                    {{ $item->assignment ? $item->assignment->title : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($item->officer)
                                    <span class="badge bg-light text-primary border">
                                        <i class="fa-solid fa-user me-1"></i> {{ $item->officer->name }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border">
                                        <i class="fa-solid fa-users me-1"></i> Seluruh Petugas
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-secondary">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                    {{ $item->location }}
                                </div>
                            </td>
                            <td>
                                @if($item->status === 'scheduled')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        Terjadwal
                                    </span>
                                @elseif($item->status === 'in_progress')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Berlangsung
                                    </span>
                                @elseif($item->status === 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-check me-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        Batal
                                    </span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                @if(auth()->user()->isAdmin())
                                    <div class="sp-action-btn-group">
                                        <!-- Edit -->
                                        <button type="button" 
                                                class="btn btn-light border sp-action-btn text-warning" 
                                                title="Edit Jadwal" 
                                                data-bs-toggle="tooltip"
                                                onclick="openEditScheduleModal({{ json_encode($item) }})">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        <!-- Hapus -->
                                        <form method="POST" action="{{ route('admin.schedules.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus jadwal agenda ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-light border sp-action-btn text-danger" 
                                                    title="Hapus Jadwal" 
                                                    data-bs-toggle="tooltip">
                                                <i class="fa-regular fa-trash-can"></i>
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
                                <i class="fa-regular fa-calendar-xmark fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada agenda jadwal kegiatan.</div>
                                <div class="small">Klik tombol "Buat Jadwal Baru" untuk menambahkan agenda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($schedules->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createScheduleModalLabel">
                    <i class="fa-regular fa-calendar-plus text-primary me-2"></i> Tambah Jadwal Kegiatan Baru
                </h5>
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
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Jadwal
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editScheduleModalLabel">
                    <i class="fa-regular fa-pen-to-square text-warning me-2"></i> Edit Jadwal Kegiatan
                </h5>
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

