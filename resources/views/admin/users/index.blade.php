@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
@endsection

@section('content')
<!-- Header & Quick Actions -->
<div class="row align-items-center mb-3">
    <div class="col-md-7">
        <h4 class="fw-bold text-dark mb-1">Manajemen Pengguna</h4>
        <p class="text-muted small mb-0">Kelola akun, hak akses peran, nomor kontak, serta status aktif/nonaktif akun sistem SIMPLE.</p>
    </div>
    <div class="col-md-5 text-md-end mt-2 mt-md-0">
        <button type="button" class="btn btn-sm btn-sp-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna Baru
        </button>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Total Pengguna</div>
                <div class="sp-stat-value">{{ number_format($totalUsers) }}</div>
                <div class="sp-stat-desc">Semua akun terdaftar</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Administrator</div>
                <div class="sp-stat-value">{{ number_format($totalAdmins) }}</div>
                <div class="sp-stat-desc">Akses penuh sistem</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Petugas Lapangan</div>
                <div class="sp-stat-value">{{ number_format($totalOfficers) }}</div>
                <div class="sp-stat-desc">Pelaksana tugas & BAST</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="sp-stat-card">
            <div class="sp-stat-icon-wrap" style="background: {{ $totalInactive > 0 ? '#fee2e2' : '#f1f5f9' }}; color: {{ $totalInactive > 0 ? '#dc2626' : '#64748b' }};">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <div class="sp-stat-info">
                <div class="sp-stat-label">Akun Nonaktif</div>
                <div class="sp-stat-value {{ $totalInactive > 0 ? 'text-danger' : '' }}">{{ number_format($totalInactive) }}</div>
                <div class="sp-stat-desc">Akses ditangguhkan</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="sp-card mb-3">
    <div class="sp-card-body p-2.5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm border-start-0 ps-0" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, username, email, nomor HP...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-sp-primary flex-fill">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
            <i class="fa-solid fa-address-book text-primary"></i>
            Daftar Pengguna Sistem ({{ $users->total() }})
        </h5>
    </div>
    <div class="sp-card-body p-0">
        <div class="table-responsive">
            <table class="table sp-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 28%;">Pengguna</th>
                        <th style="width: 15%;">Role</th>
                        <th style="width: 22%;">Kontak</th>
                        <th style="width: 13%;">Status Akun</th>
                        <th style="width: 12%;">Terdaftar</th>
                        <th class="text-end" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="sp-user-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        <div class="text-muted small">
                                            @<span>{{ $user->username }}</span>
                                            @if($user->nik)
                                                &bull; <span class="font-monospace">NIK: {{ $user->nik }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->isAdmin() ? 'badge-role-admin' : 'badge-role-petugas' }}">
                                    <i class="fa-solid {{ $user->isAdmin() ? 'fa-shield-halved' : 'fa-user-check' }} me-1"></i>
                                    {{ $user->isAdmin() ? 'Administrator' : 'Petugas' }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-dark"><i class="fa-regular fa-envelope me-1 text-muted"></i> {{ $user->email }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $user->phone ?? '-' }}</div>
                            </td>
                            <td>
                                @if($user->isActive())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-circle-check me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $user->created_at->format('d/m/Y') }}
                                <div style="font-size: 0.72rem;">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="sp-action-btn-group">
                                    <!-- Edit -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-primary" 
                                            title="Edit Data Pengguna" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditModal({{ json_encode($user) }})">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- Reset Password -->
                                    <button type="button" 
                                            class="btn btn-light border sp-action-btn text-warning" 
                                            title="Reset Password" 
                                            data-bs-toggle="tooltip"
                                            onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                        <i class="fa-solid fa-key"></i>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <!-- Toggle Status Aktif / Nonaktif -->
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline" onsubmit="return confirm('Ubah status akun {{ addslashes($user->name) }} menjadi {{ $user->isActive() ? 'Nonaktif' : 'Aktif' }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-light border sp-action-btn {{ $user->isActive() ? 'text-secondary' : 'text-success' }}" 
                                                    title="{{ $user->isActive() ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                    data-bs-toggle="tooltip">
                                                <i class="fa-solid {{ $user->isActive() ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>

                                        <!-- Hapus -->
                                        <button type="button" 
                                                class="btn btn-light border sp-action-btn text-danger" 
                                                title="Hapus Pengguna" 
                                                data-bs-toggle="tooltip"
                                                onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-semibold">Tidak ada pengguna ditemukan.</div>
                                <div class="small">Coba ubah kata kunci pencarian atau filter yang dipilih.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="sp-card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="createUserModalLabel">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i> Tambah Pengguna Baru
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

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KATA SANDI AWAL <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">ROLE / HAK AKSES <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="petugas" selected>Petugas Lapangan</option>
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
                    <button type="submit" class="btn btn-sp-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengguna
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
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="editUserModalLabel">
                    <i class="fa-solid fa-user-pen text-primary me-2"></i> Edit Data Pengguna
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

                    <div class="p-3 bg-light rounded border">
                        <label class="form-label small fw-semibold text-secondary mb-1">UBAH PASSWORD (OPSIONAL)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <small class="text-muted" style="font-size: 0.72rem;">Isi kolom ini hanya jika ingin mengganti password akun.</small>
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
     MODAL RESET PASSWORD
     ============================================================== -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">
                    <i class="fa-solid fa-key text-warning me-2"></i> Reset Password Pengguna
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
                        <label class="form-label small fw-semibold text-secondary">PASSWORD BARU</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-semibold">
                        <i class="fa-solid fa-check me-1"></i> Reset Password
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
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                </div>
                <h5 class="fw-bold mb-2">Hapus Pengguna?</h5>
                <p class="text-muted small mb-4">
                    Apakah Anda yakin ingin menghapus akun <strong id="delete_user_name" class="text-dark"></strong>? Data akan diarsipkan menggunakan soft delete.
                </p>
                <form id="deleteUserForm" method="POST">
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
    // Initialize tooltips
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

