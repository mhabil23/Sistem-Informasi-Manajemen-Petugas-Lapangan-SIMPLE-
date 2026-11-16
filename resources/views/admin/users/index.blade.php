@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
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
    .user-avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.82rem;
        flex-shrink: 0;
        color: #FFFFFF;
    }
    .user-avatar-admin {
        background: linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%);
    }
    .user-avatar-pegawai {
        background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%);
    }
    .user-avatar-petugas {
        background: linear-gradient(135deg, #F37021 0%, #D95D12 100%);
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
    .bento-action-btn.btn-edit { color: #D97706; border-color: #FDE68A; background: #FFFBEB; }
    .bento-action-btn.btn-edit:hover { background: #D97706; color: #FFFFFF; }
    .bento-action-btn.btn-key { color: #4F46E5; border-color: #C7D2FE; background: #EEF2FF; }
    .bento-action-btn.btn-key:hover { background: #4F46E5; color: #FFFFFF; }
    .bento-action-btn.btn-toggle { color: #059669; border-color: #A7F3D0; background: #ECFDF5; }
    .bento-action-btn.btn-toggle:hover { background: #059669; color: #FFFFFF; }
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
<!-- Search & Filter Bento Card -->
<div class="bento-card mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, username, SOBAT ID, kecamatan, email...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="pegawai" {{ request('role') === 'pegawai' ? 'selected' : '' }}>Pegawai / PML</option>
                    <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-bps-orange flex-fill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Bento Card -->
<div class="bento-card mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Daftar Pengguna Sistem</h5>
            <div class="d-flex align-items-center gap-1 mt-1">
                <span class="badge bg-light text-secondary border">Total Pengguna: {{ $totalUsers }}</span>
                <span class="badge bg-light text-secondary border">Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() ?: 1 }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ asset('exports/daftar_akun_petugas_simple.csv') }}" class="btn btn-outline-success rounded-pill px-3 py-1.5" style="font-size: 0.85rem;" download="daftar_akun_petugas_simple.csv" title="Unduh seluruh 366 data akun petugas beserta username dan password default">
                <i class="bi bi-file-earmark-excel me-1"></i> Unduh Akun Petugas (.CSV)
            </a>
            <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#createUserModal" style="font-size: 0.85rem;">
                <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
            </button>
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 25%;">PENGGUNA</th>
                    <th style="width: 14%;">ROLE</th>
                    <th style="width: 21%;">KONTAK</th>
                    <th class="text-center" style="width: 11%;">STATUS AKUN</th>
                    <th style="width: 10%;">TERDAFTAR</th>
                    <th class="text-end pe-3" style="width: 19%;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $avatarClass = match($user->role) {
                            'admin' => 'user-avatar-admin',
                            'pegawai' => 'user-avatar-pegawai',
                            default => 'user-avatar-petugas',
                        };
                    @endphp
                    <tr>
                        <!-- Pengguna -->
                        <td data-label="Pengguna">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar-circle {{ $avatarClass }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="text-muted small">@<span>{{ $user->username }}</span></span>
                                        @if($user->district)
                                            <span class="badge bg-light text-primary border px-1.5 py-0.5" style="font-size: 0.65rem;" title="Wilayah: {{ $user->district }} @if($user->village) - {{ $user->village }} @endif">
                                                <i class="bi bi-geo-alt me-0.5"></i>{{ $user->district }}
                                            </span>
                                        @elseif($user->nik)
                                            <span class="badge bg-light text-secondary border font-monospace px-1 py-0.2" style="font-size: 0.65rem;">
                                                NIK: {{ $user->nik }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td data-label="Role">
                            @if($user->role === 'admin')
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-weight: 600; font-size: 0.72rem;">
                                    <i class="bi bi-shield-check me-1"></i> Administrator
                                </span>
                            @elseif($user->role === 'pegawai')
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-weight: 600; font-size: 0.72rem;">
                                    <i class="bi bi-person-badge me-1"></i> Pegawai / PML
                                </span>
                            @else
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #FFF4ED; color: #C2410C; border: 1px solid #FFEDD5; font-weight: 600; font-size: 0.72rem;">
                                    <i class="bi bi-person-check me-1"></i> Petugas
                                </span>
                            @endif
                        </td>

                        <!-- Kontak -->
                        <td data-label="Kontak">
                            <div class="small text-dark text-truncate mb-0.5" title="{{ $user->email }}">
                                <i class="bi bi-envelope text-muted me-1"></i> {{ $user->email }}
                            </div>
                            <div class="small text-muted text-truncate">
                                <i class="bi bi-telephone text-muted me-1"></i> {{ $user->phone ?: '-' }}
                            </div>
                        </td>

                        <!-- Status Akun -->
                        <td class="text-center" data-label="Status Akun">
                            @if($user->isActive())
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; font-weight: 600; font-size: 0.72rem;">
                                    <i class="bi bi-check-circle-fill me-1 text-success"></i> Aktif
                                </span>
                            @else
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; font-weight: 600; font-size: 0.72rem;">
                                    <i class="bi bi-x-circle-fill me-1 text-danger"></i> Nonaktif
                                </span>
                            @endif
                        </td>

                        <!-- Terdaftar -->
                        <td data-label="Terdaftar">
                            <div class="small fw-semibold text-dark">{{ $user->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $user->created_at->diffForHumans() }}</div>
                        </td>

                        <!-- Aksi -->
                        <td class="text-end text-nowrap pe-3" data-label="Aksi">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                <!-- Edit -->
                                <button type="button" 
                                        class="bento-action-btn btn-edit" 
                                        title="Edit Data Pengguna" 
                                        data-bs-toggle="tooltip"
                                        onclick="openEditModal({{ json_encode($user) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Reset Password -->
                                <button type="button" 
                                        class="bento-action-btn btn-key" 
                                        title="Reset Password" 
                                        data-bs-toggle="tooltip"
                                        onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                    <i class="bi bi-key"></i>
                                </button>

                                @if($user->id !== auth()->id())
                                    <!-- Toggle Status Aktif / Nonaktif -->
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline" onsubmit="return confirm('Ubah status akun {{ addslashes($user->name) }} menjadi {{ $user->isActive() ? 'Nonaktif' : 'Aktif' }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="bento-action-btn btn-toggle {{ $user->isActive() ? 'text-secondary' : 'text-success' }}" 
                                                title="{{ $user->isActive() ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                data-bs-toggle="tooltip">
                                            <i class="bi {{ $user->isActive() ? 'bi-person-slash' : 'bi-person-check' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Hapus -->
                                    <button type="button" 
                                            class="bento-action-btn btn-delete" 
                                            title="Hapus Pengguna" 
                                            data-bs-toggle="tooltip"
                                            onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                            <div class="fw-semibold">Tidak ada pengguna ditemukan.</div>
                            <div class="small">Coba ubah kata kunci pencarian atau filter yang dipilih.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} data
            </div>
            <div>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ==============================================================
     MODAL TAMBAH PENGGUNA
     ============================================================== -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark" id="createUserModalLabel">
                    <i class="bi bi-person-plus text-primary me-2"></i> Tambah Pengguna Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NAMA LENGKAP <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Ahmad Fauzi, S.Kom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">USERNAME <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Contoh: ahmad_fauzi" required>
                            <small class="text-muted" style="font-size: 0.72rem;">Digunakan untuk login ke sistem.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">EMAIL RESMI <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contoh: ahmad@simple.go.id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Contoh: 081234567890">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">NIK / NIP PETUGAS</label>
                            <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="Contoh: 7408011505950001">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">PANGKAT / GOLONGAN</label>
                            <input type="text" name="rank" class="form-control" value="{{ old('rank', '-') }}" placeholder="Contoh: - atau Pengatur, II/d">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">JABATAN</label>
                            <input type="text" name="position" class="form-control" value="{{ old('position', 'Mitra BPS Kabupaten Kolaka Utara') }}" placeholder="Jabatan petugas">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KATA SANDI AWAL <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">ROLE / HAK AKSES <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="petugas" selected>Petugas Lapangan</option>
                                <option value="pegawai">Pegawai / PML</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS AKUN <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL EDIT PENGGUNA
     ============================================================== -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark" id="editUserModalLabel">
                    <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NAMA LENGKAP <span class="text-danger">*</span></label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">USERNAME <span class="text-danger">*</span></label>
                            <input type="text" id="edit_username" name="username" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">EMAIL RESMI <span class="text-danger">*</span></label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">NIK / NIP PETUGAS</label>
                            <input type="text" id="edit_nik" name="nik" class="form-control" placeholder="Contoh: 7408011505950001">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">PANGKAT / GOLONGAN</label>
                            <input type="text" id="edit_rank" name="rank" class="form-control" placeholder="-">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">JABATAN</label>
                            <input type="text" id="edit_position" name="position" class="form-control" placeholder="Mitra BPS Kabupaten Kolaka Utara">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ROLE / HAK AKSES <span class="text-danger">*</span></label>
                            <select id="edit_role" name="role" class="form-select" required>
                                <option value="petugas">Petugas Lapangan</option>
                                <option value="pegawai">Pegawai / PML</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">STATUS AKUN <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <label class="form-label small fw-semibold text-secondary mb-1">UBAH PASSWORD (OPSIONAL)</label>
                        <input type="password" name="password" class="form-control bg-white" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <small class="text-muted" style="font-size: 0.72rem;">Isi kolom ini hanya jika ingin mengganti password akun.</small>
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

<!-- ==============================================================
     MODAL RESET PASSWORD
     ============================================================== -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark" id="resetPasswordModalLabel">
                    <i class="bi bi-key text-warning me-2"></i> Reset Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resetPasswordForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">
                        Atur kata sandi baru untuk pengguna <strong id="reset_user_name" class="text-dark"></strong>:
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">PASSWORD BARU <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-check-lg me-1"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL KONFIRMASI HAPUS
     ============================================================== -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="badge rounded-circle p-3" style="background: #FEE2E2; color: #DC2626; font-size: 2rem;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-2 text-dark">Hapus Pengguna?</h5>
                <p class="text-muted small mb-4">
                    Apakah Anda yakin ingin menghapus akun <strong id="delete_user_name" class="text-dark"></strong>? Data akan diarsipkan menggunakan soft delete.
                </p>
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Ya, Hapus</button>
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

    function openEditModal(user) {
        document.getElementById('editUserForm').action = '/admin/users/' + user.id;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_nik').value = user.nik || '';
        document.getElementById('edit_rank').value = user.rank || '';
        document.getElementById('edit_position').value = user.position || '';
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_status').value = user.status;

        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }

    function openResetPasswordModal(userId, userName) {
        document.getElementById('resetPasswordForm').action = '/admin/users/' + userId + '/reset-password';
        document.getElementById('reset_user_name').innerText = userName;

        const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        modal.show();
    }

    function openDeleteModal(userId, userName) {
        document.getElementById('deleteUserForm').action = '/admin/users/' + userId;
        document.getElementById('delete_user_name').innerText = userName;

        const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        modal.show();
    }
</script>
@endpush
