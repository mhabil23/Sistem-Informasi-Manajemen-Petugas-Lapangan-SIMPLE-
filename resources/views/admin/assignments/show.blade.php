@extends('layouts.app')

@section('title', 'Detail Kegiatan & Pembagian Tugas')
@section('page_title', 'Detail Kegiatan & Penugasan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.assignments.index') }}">Kegiatan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail & Pembagian Tugas</li>
@endsection

@section('content')
<!-- Back Button & Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('admin.assignments.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Kegiatan
        </a>
        <h3 class="fw-bold text-dark mb-0">{{ $assignment->title }}</h3>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sp-primary" data-bs-toggle="modal" data-bs-target="#assignOfficerModal">
            <i class="fa-solid fa-user-plus me-1"></i> Tugaskan Petugas Baru
        </button>
    </div>
</div>

<!-- Overview Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="sp-card h-100 mb-0">
            <div class="sp-card-header">
                <h5 class="sp-card-title">
                    <i class="fa-solid fa-circle-info text-primary"></i>
                    Informasi Kegiatan
                </h5>
                @if($assignment->status === 'ongoing')
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1">
                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Sedang Berjalan
                    </span>
                @elseif($assignment->status === 'completed')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                        <i class="fa-solid fa-check me-1"></i> Selesai
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1">
                        {{ ucfirst($assignment->status) }}
                    </span>
                @endif
            </div>
            <div class="sp-card-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <div class="text-muted small">Waktu Pelaksanaan:</div>
                        <div class="fw-bold text-dark">
                            <i class="fa-regular fa-calendar-days text-primary me-1"></i>
                            {{ $assignment->start_date->translatedFormat('d F Y') }} s/d {{ $assignment->end_date->translatedFormat('d F Y') }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Lokasi Wilayah:</div>
                        <div class="fw-bold text-dark">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                            {{ $assignment->location }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Target Kuota Responden:</div>
                        <div class="fw-bold text-dark">
                            <i class="fa-solid fa-bullseye text-primary me-1"></i>
                            @if($assignment->target_respondents > 0)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-7">{{ number_format($assignment->target_respondents) }} Sampel</span>
                            @else
                                <span class="text-muted">Belum Ditentukan</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <div class="text-muted small mb-1">Deskripsi & Petunjuk Teknis:</div>
                    <p class="text-secondary small mb-0" style="white-space: pre-line;">
                        {{ $assignment->description ?? 'Tidak ada uraian deskripsi tambahan.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="sp-card h-100 mb-0">
            <div class="sp-card-header">
                <h5 class="sp-card-title">
                    <i class="fa-solid fa-chart-pie text-success"></i>
                    Ringkasan Progres
                </h5>
            </div>
            <div class="sp-card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Petugas Ditugaskan:</span>
                    <strong class="text-dark fs-5">{{ $assignment->assignmentOfficers->count() }} Orang</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Responden Terdata:</span>
                    <div>
                        <strong class="text-primary fs-5">{{ $assignment->respondents->count() }}</strong>
                        @if($assignment->target_respondents > 0)
                            <span class="text-muted small">/ {{ number_format($assignment->target_respondents) }} Kuota</span>
                        @else
                            <span class="text-muted small">Responden</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">BAST Diterbitkan:</span>
                    <strong class="text-success fs-5">{{ $assignment->respondentBasts->count() + $assignment->officerBasts->count() }} Dokumen</strong>
                </div>

                @if($assignment->target_respondents > 0)
                    @php
                        $respPercent = round(($assignment->respondents->count() / $assignment->target_respondents) * 100);
                    @endphp
                    <div class="border-top pt-2 mb-2">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Pencapaian Kuota Responden:</span>
                            <strong class="{{ $respPercent >= 100 ? 'text-success' : 'text-primary' }}">{{ $respPercent }}%</strong>
                        </div>
                        <div class="progress" style="height: 6px;">
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
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Penyelesaian Tugas Petugas:</span>
                        <strong class="text-dark">{{ $percent }}%</strong>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     BAGIAN 1: PEMBAGIAN TUGAS PETUGAS LAPANGAN
     ============================================================== -->
<div class="sp-card mb-4">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-user-gear text-primary"></i>
            Daftar Petugas & Pembagian Tugas ({{ $assignment->assignmentOfficers->count() }})
        </h5>
        <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#assignOfficerModal">
            <i class="fa-solid fa-plus me-1"></i> Tambah Petugas
        </button>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Petugas Lapangan</th>
                        <th style="min-width: 260px;">Tugas Spesifik yang Diberikan</th>
                        <th>Status Tugas</th>
                        <th style="min-width: 200px;">Catatan Progres Petugas</th>
                        <th class="text-end" style="min-width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignment->assignmentOfficers as $officerAssigned)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="sp-user-avatar" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($officerAssigned->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $officerAssigned->user->name }}</div>
                                        <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $officerAssigned->user->phone ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small text-dark fw-medium">
                                    {{ $officerAssigned->specific_task ?? 'Pelaksana pendataan dan verifikasi lapangan.' }}
                                </div>
                            </td>
                            <td>
                                @if($officerAssigned->status === 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-circle-check me-1"></i> Selesai
                                    </span>
                                @elseif($officerAssigned->status === 'in_progress')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Berjalan
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        <i class="fa-solid fa-clock me-1"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-muted fst-italic">
                                    {{ $officerAssigned->progress_notes ?? 'Belum ada catatan progres.' }}
                                </div>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Edit Rincian Tugas -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-warning" 
                                            title="Edit Rincian Tugas" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditOfficerModal({{ json_encode($officerAssigned) }})">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- Batalkan / Hapus Penugasan -->
                                    <form method="POST" 
                                          action="{{ route('admin.assignments.officers.remove', [$assignment, $officerAssigned]) }}" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Batalkan penugasan untuk {{ addslashes($officerAssigned->user->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-light border sp-action-btn text-danger" 
                                                title="Batalkan Penugasan" 
                                                data-bs-toggle="tooltip">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-user-xmark fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada petugas yang ditugaskan pada kegiatan ini.</div>
                                <div class="small">Klik tombol "Tugaskan Petugas Baru" di atas untuk menambahkan petugas penanggung jawab.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==============================================================
     BAGIAN 2: RESPONDEN TERKAIT KEGIATAN INI
     ============================================================== -->
<div class="sp-card mb-4">
    <div class="sp-card-header">
        <h5 class="sp-card-title">
            <i class="fa-solid fa-id-card text-info"></i>
            Responden yang Terdata pada Kegiatan Ini ({{ $assignment->respondents->count() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th>Nama Responden</th>
                        <th>Instansi / Lembaga</th>
                        <th>Alamat</th>
                        <th>Petugas Penginput</th>
                        <th>Tanggal Input</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignment->respondents as $resp)
                        <tr>
                            <td class="fw-bold text-dark">{{ $resp->name }}</td>
                            <td><span class="badge bg-light text-primary border">{{ $resp->agency }}</span></td>
                            <td class="small text-secondary">{{ Str::limit($resp->address, 45) }}</td>
                            <td>
                                <span class="small fw-semibold text-dark">{{ $resp->officer->name }}</span>
                            </td>
                            <td class="small text-muted">{{ $resp->input_date->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">
                                Belum ada responden yang diinput untuk kegiatan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL TUGASKAN PETUGAS BARU
     ============================================================== -->
<div class="modal fade" id="assignOfficerModal" tabindex="-1" aria-labelledby="assignOfficerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="assignOfficerModalLabel">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i> Tugaskan Petugas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.assignments.officers.assign', $assignment) }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">PILIH PETUGAS LAPANGAN <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Pilih Petugas --</option>
                            @foreach($availableOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->phone ?? $officer->username }})</option>
                            @endforeach
                        </select>
                        @if($availableOfficers->isEmpty())
                            <small class="text-danger" style="font-size: 0.72rem;">Semua petugas aktif telah ditugaskan pada kegiatan ini.</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">DESKRIPSI TUGAS SPESIFIK <span class="text-danger">*</span></label>
                        <textarea name="specific_task" class="form-control" rows="3" placeholder="Contoh: Pendataan responden wilayah Dusun Timur, target: 25 responden." required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN AWAL (OPSIONAL)</label>
                        <input type="text" name="progress_notes" class="form-control" placeholder="Contoh: Telah berkoordinasi dengan kepala desa setempat.">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary" {{ $availableOfficers->isEmpty() ? 'disabled' : '' }}>
                        <i class="fa-solid fa-check me-1"></i> Simpan Penugasan
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editOfficerTaskModalLabel">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Perbarui Tugas Petugas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editOfficerTaskForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA PETUGAS</label>
                        <input type="text" id="edit_officer_name" class="form-control bg-light" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">TUGAS SPESIFIK <span class="text-danger">*</span></label>
                        <textarea id="edit_officer_task" name="specific_task" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">STATUS PENYELESAIAN <span class="text-danger">*</span></label>
                            <select id="edit_officer_status" name="status" class="form-select" required>
                                <option value="pending">Menunggu (Pending)</option>
                                <option value="in_progress">Sedang Berjalan (In Progress)</option>
                                <option value="completed">Selesai (Completed)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN PROGRES</label>
                        <textarea id="edit_officer_notes" name="progress_notes" class="form-control" rows="2" placeholder="Catatan perkembangan tugas..."></textarea>
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
    function openEditOfficerModal(officerAssigned) {
        document.getElementById('editOfficerTaskForm').action = '/admin/assignments/{{ $assignment->id }}/officers/' + officerAssigned.id;
        document.getElementById('edit_officer_name').value = officerAssigned.user.name;
        document.getElementById('edit_officer_task').value = officerAssigned.specific_task || '';
        document.getElementById('edit_officer_status').value = officerAssigned.status;
        document.getElementById('edit_officer_notes').value = officerAssigned.progress_notes || '';

        const modal = new bootstrap.Modal(document.getElementById('editOfficerTaskModal'));
        modal.show();
    }
</script>
@endpush

