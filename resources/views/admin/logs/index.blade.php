@extends('layouts.app')

@section('title', 'Audit Log Aktivitas')
@section('page_title', 'Audit Trail & Log Aktivitas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Audit Log Aktivitas</li>
@endsection

@section('content')
<!-- Header & Cleanup Action -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Audit Trail & Log Aktivitas Sistem</h4>
        <p class="text-muted small mb-0">Rekam jejak seluruh mutasi data, login pengguna, penerbitan BAST, verifikasi laporan, dan aktivitas administrasi secara transparan & aman.</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        <form method="POST" action="{{ route('admin.logs.clear') }}" class="d-inline" onsubmit="return confirm('Bersihkan log aktivitas yang lebih dari 30 hari?');">
            @csrf
            <input type="hidden" name="days" value="30">
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-broom me-1"></i> Bersihkan Log > 30 Hari
            </button>
        </form>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Audit Log</div>
                <div class="sp-stat-value">{{ number_format($totalLogs) }}</div>
                <div class="sp-stat-desc">Rekaman aktivitas</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Hari Ini</div>
                <div class="sp-stat-value text-success">{{ number_format($todayLogs) }}</div>
                <div class="sp-stat-desc">Aktivitas baru</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-database"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Mutasi Data</div>
                <div class="sp-stat-value text-warning">{{ number_format($mutationLogs) }}</div>
                <div class="sp-stat-desc">Create / Edit / Hapus</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Sesi Autentikasi</div>
                <div class="sp-stat-value text-primary">{{ number_format($loginLogs) }}</div>
                <div class="sp-stat-desc">Riwayat masuk akun</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-4">
    <div class="sp-card-body p-3">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari deskripsi, IP address, user...">
                </div>
            </div>

            <div class="col-md-2">
                <select name="module" class="form-select">
                    <option value="all">-- Semua Modul --</option>
                    <option value="auth" {{ request('module') == 'auth' ? 'selected' : '' }}>Autentikasi</option>
                    <option value="users" {{ request('module') == 'users' ? 'selected' : '' }}>Pengguna</option>
                    <option value="assignments" {{ request('module') == 'assignments' ? 'selected' : '' }}>Kegiatan</option>
                    <option value="respondents" {{ request('module') == 'respondents' ? 'selected' : '' }}>Responden</option>
                    <option value="basts" {{ request('module') == 'basts' ? 'selected' : '' }}>Dokumen BAST</option>
                    <option value="reports" {{ request('module') == 'reports' ? 'selected' : '' }}>Laporan Petugas</option>
                    <option value="trainings" {{ request('module') == 'trainings' ? 'selected' : '' }}>Pelatihan</option>
                    <option value="templates" {{ request('module') == 'templates' ? 'selected' : '' }}>Template</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="action" class="form-select">
                    <option value="all">-- Semua Aksi --</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Tambah Data (Create)</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Ubah Data (Update)</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Hapus Data (Delete)</option>
                    <option value="review" {{ request('action') == 'review' ? 'selected' : '' }}>Verifikasi / Review</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login Akun</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout Akun</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="user_id" class="form-select">
                    <option value="">-- Semua Pengguna --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'module', 'action', 'user_id']))
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
            <i class="fa-solid fa-list-check text-primary"></i>
            Daftar Jejak Audit Sistem ({{ $logs->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 15%;">Waktu</th>
                        <th style="width: 16%;">Pengguna</th>
                        <th class="text-center" style="width: 8%;">Aksi</th>
                        <th class="text-center" style="width: 9%;">Modul</th>
                        <th style="width: 38%;">Deskripsi Aktivitas</th>
                        <th style="width: 10%;">IP Address</th>
                        <th class="text-end" style="width: 4%;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark small">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark small">{{ $log->user ? $log->user->name : 'Sistem / Tamu' }}</div>
                                <span class="badge {{ $log->user && $log->user->isAdmin() ? 'bg-primary' : 'bg-info text-dark' }}" style="font-size: 0.68rem;">
                                    {{ $log->user ? ucfirst($log->user->role) : 'Guest' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $actionBadges = [
                                        'create' => 'bg-success-subtle text-success border border-success-subtle',
                                        'update' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                        'delete' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        'review' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                        'login' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        'logout' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                    ];
                                @endphp
                                <span class="badge {{ $actionBadges[$log->action] ?? 'bg-secondary' }} px-2 py-1">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-dark text-truncate" style="max-width: 380px;" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace small text-muted">{{ $log->ip_address ?: '127.0.0.1' }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <button type="button" 
                                        class="btn btn-light border sp-action-btn text-info" 
                                        title="Rincian Lengkap Log" 
                                        data-bs-toggle="tooltip"
                                        onclick="openLogDetailModal({{ json_encode($log) }})">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-clock-rotate-left fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Belum ada catatan log aktivitas ditemukan.</div>
                                <div class="small">Aktivitas operasi sistem akan otomatis terekam di sini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $logs->firstItem() }} sampai {{ $logs->lastItem() }} dari {{ $logs->total() }} catatan log
            </div>
            <div>
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL DETAIL LOG
     ============================================================== -->
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="logDetailModalLabel">
                    <i class="fa-solid fa-info-circle text-info me-2"></i> Rincian Audit Log
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3 bg-light p-3 rounded border">
                    <div class="col-sm-6">
                        <div class="text-muted small">Waktu Kejadian:</div>
                        <div class="fw-bold text-dark" id="log_time"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Pengguna:</div>
                        <div class="fw-bold text-primary" id="log_user"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Aksi & Modul:</div>
                        <div class="fw-semibold text-dark" id="log_action_module"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Alamat IP:</div>
                        <div class="font-monospace text-dark" id="log_ip"></div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small mb-1">Deskripsi Aktivitas:</div>
                        <div class="p-2 bg-white border rounded small text-dark" id="log_desc"></div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small mb-1">User Agent (Browser / Device):</div>
                        <div class="small text-muted font-monospace bg-white border rounded p-2" id="log_ua"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openLogDetailModal(log) {
        document.getElementById('log_time').innerText = log.created_at;
        document.getElementById('log_user').innerText = log.user ? `${log.user.name} (${log.user.email})` : 'Sistem / Tamu';
        document.getElementById('log_action_module').innerText = `${log.action.toUpperCase()} pada modul [${log.module}]`;
        document.getElementById('log_ip').innerText = log.ip_address || '127.0.0.1';
        document.getElementById('log_desc').innerText = log.description;
        document.getElementById('log_ua').innerText = log.user_agent || '-';

        const modal = new bootstrap.Modal(document.getElementById('logDetailModal'));
        modal.show();
    }
</script>
@endpush

