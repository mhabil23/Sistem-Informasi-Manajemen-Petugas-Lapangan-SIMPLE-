@extends('layouts.app')

@section('title', 'Tugas & Penugasan Saya')
@section('page_title', 'Tugas Saya')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Petugas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tugas Saya</li>
@endsection

@push('styles')
<style>
    .bento-card {
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
        transition: all 0.2s ease;
    }
    .bento-card:hover {
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }
    .bento-stat-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.85);
        padding: 1.1rem 1.25rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .bento-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .btn-bps-orange {
        background-color: #F37021;
        border-color: #F37021;
        color: #ffffff;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }
    .btn-bps-orange:hover {
        background-color: #d95d12;
        border-color: #d95d12;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(243, 112, 33, 0.25);
    }
    .bento-card.card-selected {
        border-color: #005BAC !important;
        background-color: #f8fbff !important;
        box-shadow: 0 4px 18px rgba(0, 91, 172, 0.12) !important;
    }
    .btn-bulk-action {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 0.32rem 0.8rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        cursor: pointer;
    }
    .btn-bulk-action:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-briefcase-fill text-primary"></i> Daftar Penugasan Kegiatan
        </h3>
        <p class="text-muted small mb-0">Daftar kegiatan resmi dan mandat tugas khusus yang diberikan oleh Administrator untuk Anda.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('petugas.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon" style="background: #EFF6FF; color: #005BAC;">
                <i class="bi bi-briefcase"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">TOTAL TUGAS</div>
                <div class="fw-bold text-dark" style="font-size: 1.4rem;">{{ number_format($totalTasks) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Diberikan ke Anda</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon" style="background: #FFFBEB; color: #D97706;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">SEDANG BERJALAN</div>
                <div class="fw-bold text-warning" style="font-size: 1.4rem;">{{ number_format($inProgressTasks) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Tahap pelaksanaan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon" style="background: #F0FDF4; color: #16A34A;">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">TUGAS SELESAI</div>
                <div class="fw-bold text-success" style="font-size: 1.4rem;">{{ number_format($completedTasks) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Tuntas dikerjakan</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="bento-stat-card">
            <div class="bento-stat-icon" style="background: #F8FAFC; color: #64748B;">
                <i class="bi bi-clock"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">MENUNGGU (PENDING)</div>
                <div class="fw-bold text-secondary" style="font-size: 1.4rem;">{{ number_format($pendingTasks) }}</div>
                <div class="text-muted small" style="font-size: 0.72rem;">Belum dimulai</div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Toolbar Tugas Lapangan (Modern, Bersih & Elegan) -->
<div id="bulkActionBar" class="d-none bento-card p-3 mb-3 align-items-center justify-content-between flex-wrap gap-2 shadow-xs" style="background: #f8fafc; border: 1px solid #e2e8f0;">
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #005BAC; color: #ffffff; font-size: 0.78rem;">
            <i class="bi bi-check2-circle fs-6"></i>
            <span id="bulkSelectedCountBadge">0 Tugas Dipilih</span>
        </span>
        <span class="text-muted small">|</span>
        <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none p-0 d-inline-flex align-items-center gap-1" onclick="clearAllSelectedTasks()" style="font-size: 0.78rem;">
            <i class="bi bi-x-circle text-muted"></i> Batal Pilih
        </button>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <button type="button" class="btn-bulk-action" onclick="submitBulkProgress('in_progress')" title="Tandai seluruh tugas yang dicentang sedang dalam tahap pelaksanaan">
            <i class="bi bi-hourglass-split text-warning fs-6"></i> Set Sedang Berjalan
        </button>
        <button type="button" class="btn-bulk-action" onclick="submitBulkProgress('completed')" title="Tandai seluruh tugas yang dicentang tuntas dikerjakan">
            <i class="bi bi-check2-circle text-success fs-6"></i> Set Selesai
        </button>
        <button type="button" class="btn-bulk-action" onclick="submitBulkProgress('pending')" title="Kembalikan tugas yang dicentang ke status Menunggu">
            <i class="bi bi-clock text-secondary fs-6"></i> Set Menunggu
        </button>
    </div>
</div>

<!-- Task List -->
<div class="row g-3">
    @forelse($assignedTasks as $task)
        <div class="col-12">
            <div class="bento-card p-4 task-card-item">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="pt-1">
                            <input type="checkbox" 
                                   name="task_ids[]" 
                                   value="{{ $task->id }}" 
                                   class="form-check-input task-row-checkbox cursor-pointer" 
                                   style="width: 20px; height: 20px;"
                                   onchange="onTaskCheckboxChange()">
                        </div>
                        <div>
                            <span class="badge bg-light text-primary border rounded-pill px-3 py-1.5 fw-semibold mb-1.5 d-inline-flex align-items-center gap-1.5 small">
                                <i class="bi bi-calendar-check"></i>
                                {{ $task->assignment->start_date->format('d/m/Y') }} s/d {{ $task->assignment->end_date->format('d/m/Y') }}
                            </span>
                            <h4 class="fw-bold text-dark mb-1" style="font-size: 1.15rem;">{{ $task->assignment->title }}</h4>
                            <div class="text-secondary small d-flex align-items-center gap-1.5">
                                <i class="bi bi-geo-alt-fill text-danger"></i> {{ $task->assignment->location }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($task->status === 'completed')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.8rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Selesai
                            </span>
                        @elseif($task->status === 'in_progress')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.8rem;">
                                <i class="bi bi-hourglass-split me-1"></i> Sedang Berjalan
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.8rem;">
                                <i class="bi bi-clock me-1"></i> Menunggu
                            </span>
                        @endif

                        <button type="button" 
                                class="btn-bps-orange" 
                                onclick="openUpdateProgressModal({{ json_encode($task) }})">
                            <i class="bi bi-pencil-square"></i> Update Progres
                        </button>
                    </div>
                </div>

                <div class="bg-light rounded-3 p-3 mb-3 border" style="background: #F8FAFC !important;">
                    <div class="fw-bold small text-secondary mb-1 d-flex align-items-center gap-1.5" style="font-size: 0.76rem;">
                        <i class="bi bi-bullseye text-primary"></i> MANDAT TUGAS SPESIFIK ANDA:
                    </div>
                    <div class="text-dark small fw-medium" style="font-size: 0.85rem;">
                        {{ $task->specific_task ?? 'Laksanakan pendataan dan serah terima dokumen dengan responden sesuai target wilayah.' }}
                    </div>
                </div>

                @if($task->progress_notes)
                    <div class="alert alert-info py-2 px-3 mb-3 small border-0 rounded-3 d-flex align-items-start gap-2" style="font-size: 0.82rem;">
                        <i class="bi bi-chat-left-dots-fill mt-0.5"></i>
                        <div><strong>Catatan Perkembangan Terakhir:</strong> {{ $task->progress_notes }}</div>
                    </div>
                @endif

                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                    <a href="{{ route('petugas.respondents.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                        <i class="bi bi-person-plus"></i> Input Responden
                    </a>
                    <a href="{{ route('petugas.basts.respondents.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                        <i class="bi bi-file-earmark-check"></i> Buat Tanda Terima Responden
                    </a>
                    <a href="{{ route('petugas.reports.index') }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                        <i class="bi bi-clipboard2-check"></i> Tulis Laporan Kegiatan
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="bento-card text-center py-5">
                <i class="bi bi-folder2-open display-5 text-secondary opacity-50 mb-3 d-block"></i>
                <h6 class="fw-bold text-dark">Belum Ada Penugasan Aktif</h6>
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
        <div class="modal-content border-0 shadow" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold" id="updateProgressModalLabel">
                    <i class="bi bi-pencil-square text-primary me-1.5"></i> Update Progres Tugas
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateProgressForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">KEGIATAN</label>
                        <input type="text" id="prog_assignment_title" class="form-control bg-light" readonly disabled style="font-size: 0.85rem;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">STATUS PENGERJAAN TUGAS <span class="text-danger">*</span></label>
                        <select id="prog_status" name="status" class="form-select" required style="font-size: 0.85rem;">
                            <option value="pending">Menunggu (Pending)</option>
                            <option value="in_progress">Sedang Dikerjakan (In Progress)</option>
                            <option value="completed">Telah Selesai (Completed)</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary" style="font-size: 0.78rem;">CATATAN PROGRES LAPANGAN</label>
                        <textarea id="prog_notes" name="progress_notes" class="form-control" rows="3" placeholder="Contoh: Telah mengunjungi 15 dari 20 responden..." style="font-size: 0.85rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-3">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal" style="font-size: 0.82rem;">Batal</button>
                    <button type="submit" class="btn-bps-orange rounded-pill px-4">
                        <i class="bi bi-check2-circle"></i> Simpan Progres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Tersembunyi untuk Eksekusi Aksi Massal Progres Tugas Lapangan -->
<form id="bulkProgressForm" method="POST" action="{{ route('petugas.assignments.bulk-progress') }}" class="d-none">
    @csrf
    <input type="hidden" name="status" id="bulkProgressStatusInput">
    <div id="bulkProgressTaskIdsContainer"></div>
</form>
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

    // ==============================================================
    // Aksi Massal Progres Tugas Lapangan (Multi-Select)
    // ==============================================================
    function onTaskCheckboxChange() {
        const allCheckboxes = document.querySelectorAll('.task-row-checkbox');
        const checkedBoxes = document.querySelectorAll('.task-row-checkbox:checked');
        const count = checkedBoxes.length;
        const bar = document.getElementById('bulkActionBar');
        const badge = document.getElementById('bulkSelectedCountBadge');

        allCheckboxes.forEach(cb => {
            const card = cb.closest('.task-card-item');
            if (card) {
                if (cb.checked) card.classList.add('card-selected');
                else card.classList.remove('card-selected');
            }
        });

        if (badge) badge.textContent = `${count} Tugas Dipilih`;

        if (count > 0) {
            if (bar) {
                bar.classList.remove('d-none');
                bar.classList.add('d-flex');
            }
        } else {
            if (bar) {
                bar.classList.add('d-none');
                bar.classList.remove('d-flex');
            }
        }
    }

    function clearAllSelectedTasks() {
        document.querySelectorAll('.task-row-checkbox').forEach(cb => {
            cb.checked = false;
            const card = cb.closest('.task-card-item');
            if (card) card.classList.remove('card-selected');
        });
        onTaskCheckboxChange();
    }

    function submitBulkProgress(status) {
        const selected = Array.from(document.querySelectorAll('.task-row-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Pilih minimal satu tugas terlebih dahulu.');
            return;
        }

        const labels = {
            'in_progress': 'Ubah status ' + selected.length + ' tugas terpilih menjadi Sedang Berjalan?',
            'completed': 'Tandai ' + selected.length + ' tugas terpilih telah Selesai?',
            'pending': 'Kembalikan status ' + selected.length + ' tugas terpilih menjadi Menunggu?'
        };

        const confirmMsg = labels[status] || 'Perbarui status ' + selected.length + ' tugas terpilih?';
        if (!confirm(confirmMsg)) {
            return;
        }

        const form = document.getElementById('bulkProgressForm');
        const statusInput = document.getElementById('bulkProgressStatusInput');
        const container = document.getElementById('bulkProgressTaskIdsContainer');

        statusInput.value = status;
        container.innerHTML = '';
        selected.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'task_ids[]';
            hidden.value = id;
            container.appendChild(hidden);
        });

        form.submit();
    }
</script>
@endpush
