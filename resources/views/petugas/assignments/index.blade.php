@extends('layouts.app')

@section('title', 'Tugas & Penugasan Saya')
@section('page_title', 'Tugas Saya')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tugas Saya</li>
@endsection

@section('content')
<!-- Header -->
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark mb-1">Daftar Penugasan Kegiatan</h3>
        <p class="text-muted small mb-0">Berikut adalah daftar kegiatan resmi dan mandat tugas khusus yang diberikan oleh koordinator/Administrator kepada Anda.</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-briefcase"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Tugas</div>
                <div class="sp-stat-value">{{ number_format($totalTasks) }}</div>
                <div class="sp-stat-desc">Diberikan ke Anda</div>
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
                <div class="sp-stat-value text-warning">{{ number_format($inProgressTasks) }}</div>
                <div class="sp-stat-desc">Tahap pelaksanaan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Tugas Selesai</div>
                <div class="sp-stat-value text-success">{{ number_format($completedTasks) }}</div>
                <div class="sp-stat-desc">Tuntas dikerjakan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #f1f5f9; color: #64748b;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Menunggu (Pending)</div>
                <div class="sp-stat-value">{{ number_format($pendingTasks) }}</div>
                <div class="sp-stat-desc">Belum dimulai</div>
            </div>
        </div>
    </div>
</div>

<!-- Task List -->
<div class="row g-4">
    @forelse($assignedTasks as $task)
        <div class="col-12">
            <div class="sp-card mb-0">
                <div class="sp-card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <span class="badge bg-light text-primary border mb-1">
                                <i class="fa-regular fa-calendar-check me-1"></i>
                                {{ $task->assignment->start_date->format('d/m/Y') }} s/d {{ $task->assignment->end_date->format('d/m/Y') }}
                            </span>
                            <h4 class="fw-bold text-dark mb-1">{{ $task->assignment->title }}</h4>
                            <div class="text-secondary small">
                                <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $task->assignment->location }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($task->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                                    <i class="fa-solid fa-check me-1"></i> Selesai
                                </span>
                            @elseif($task->status === 'in_progress')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 fs-6">
                                    <i class="fa-solid fa-spinner fa-spin me-1"></i> Sedang Berjalan
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 fs-6">
                                    <i class="fa-solid fa-clock me-1"></i> Menunggu
                                </span>
                            @endif

                            <button type="button" 
                                    class="btn btn-sp-primary btn-sm" 
                                    onclick="openUpdateProgressModal({{ json_encode($task) }})">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Update Progres
                            </button>
                        </div>
                    </div>

                    <div class="bg-light rounded p-3 mb-3 border">
                        <div class="fw-bold small text-secondary mb-1">
                            <i class="fa-solid fa-bullseye text-primary me-1"></i> MANDAT TUGAS SPESIFIK ANDA:
                        </div>
                        <div class="text-dark small fw-medium">
                            {{ $task->specific_task ?? 'Laksanakan pendataan dan serah terima dokumen dengan responden sesuai target wilayah.' }}
                        </div>
                    </div>

                    @if($task->progress_notes)
                        <div class="alert alert-info py-2 px-3 mb-3 small border-0">
                            <strong>Catatan Perkembangan Terakhir:</strong> {{ $task->progress_notes }}
                        </div>
                    @endif

                    <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                        <a href="{{ route('petugas.respondents.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-user-plus me-1"></i> Input Responden untuk Kegiatan Ini
                        </a>
                        <a href="{{ route('petugas.basts.respondents.index') }}" class="btn btn-sm btn-outline-success">
                            <i class="fa-solid fa-file-contract me-1"></i> Buat Tanda Terima Responden
                        </a>
                        <a href="{{ route('petugas.reports.index') }}" class="btn btn-sm btn-outline-warning text-dark">
                            <i class="fa-solid fa-clipboard-list me-1"></i> Tulis Laporan Kegiatan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="sp-card text-center py-5">
                <i class="fa-solid fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                <h5 class="fw-bold text-dark">Belum Ada Penugasan Aktif</h5>
                <p class="text-muted small max-w-md mx-auto mb-0">
                    Administrator belum memberikan penugasan baru untuk akun Anda. Silakan hubungi admin atau periksa kembali secara berkala.
                </p>
            </div>
        </div>
    @endforelse
</div>

<!-- ==============================================================
     MODAL UPDATE PROGRES TUGAS (PETUGAS)
     ============================================================== -->
<div class="modal fade" id="updateProgressModal" tabindex="-1" aria-labelledby="updateProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="updateProgressModalLabel">
                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Update Progres Tugas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateProgressForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">KEGIATAN</label>
                        <input type="text" id="prog_assignment_title" class="form-control bg-light" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">STATUS PENGERJAAN TUGAS <span class="text-danger">*</span></label>
                        <select id="prog_status" name="status" class="form-select" required>
                            <option value="pending">Menunggu (Pending)</option>
                            <option value="in_progress">Sedang Dikerjakan (In Progress)</option>
                            <option value="completed">Telah Selesai (Completed)</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">CATATAN PROGRES LAPANGAN</label>
                        <textarea id="prog_notes" name="progress_notes" class="form-control" rows="3" placeholder="Contoh: Telah mengunjungi 15 dari 20 responden. Kendala: alamat responden 5 berpindah lokasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Progres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openUpdateProgressModal(task) {
        document.getElementById('updateProgressForm').action = '/petugas/assignments/' + task.id + '/progress';
        document.getElementById('prog_assignment_title').value = task.assignment.title;
        document.getElementById('prog_status').value = task.status;
        document.getElementById('prog_notes').value = task.progress_notes || '';

        const modal = new bootstrap.Modal(document.getElementById('updateProgressModal'));
        modal.show();
    }
</script>
@endpush

