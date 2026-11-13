@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
@endsection

@push('styles')
<style>
/* Bento Users Page Styling */
.bento-users-wrapper {
    max-width: 1400px;
    margin: 0 auto;
}

.bento-card-clean {
    background: #FFFFFF;
    border-radius: 18px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    transition: all 0.22s ease;
}

.bento-card-clean:hover {
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}

/* 5 KPI Stat Cards */
.bento-stat-user {
    background: #FFFFFF;
    border-radius: 16px;
    border: 1px solid #E2E8F0;
    padding: 1.15rem 1.25rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.02);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 1rem;
    height: 100%;
}

.bento-stat-user:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
}

.bento-stat-icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.bento-stat-number {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0F172A;
    line-height: 1.1;
    margin-bottom: 2px;
}

.bento-stat-label-text {
    font-size: 0.74rem;
    font-weight: 700;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 2px;
}

.bento-stat-desc-text {
    font-size: 0.72rem;
    color: #94A3B8;
    margin: 0;
    line-height: 1.2;
}

/* Action Buttons */
.btn-bps-orange {
    background: #F37021;
    color: #FFFFFF;
    border: none;
    font-weight: 700;
    border-radius: 999px;
    padding: 0.55rem 1.25rem;
    box-shadow: 0 4px 14px rgba(243, 112, 33, 0.25);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.btn-bps-orange:hover {
    background: #E05D0E;
    color: #FFFFFF;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(243, 112, 33, 0.35);
}

/* Modern Bento Table */
.bento-table {
    margin-bottom: 0;
    vertical-align: middle;
}

.bento-table thead th {
    background: #F8FAFC;
    color: #64748B;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 0.95rem 1.25rem;
    border-bottom: 1px solid #E2E8F0;
    border-top: none;
}

.bento-table tbody td {
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid #F1F5F9;
    color: #334155;
    background: #FFFFFF;
}

.bento-table tbody tr:hover td {
    background: #FAFBFD;
}

.bento-table tbody tr:last-child td {
    border-bottom: none;
}

/* User Avatar & Info */
.user-avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1rem;
    flex-shrink: 0;
    color: #FFFFFF;
}

.user-avatar-admin {
    background: linear-gradient(135deg, #7C3AED 0%, #5B21B6 100%);
    box-shadow: 0 4px 10px rgba(124, 58, 237, 0.2);
}

.user-avatar-pegawai {
    background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%);
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.2);
}

.user-avatar-petugas {
    background: linear-gradient(135deg, #F37021 0%, #D95D12 100%);
    box-shadow: 0 4px 10px rgba(243, 112, 33, 0.2);
}

/* Action Circle Buttons */
.bento-act-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #E2E8F0;
    background: #FFFFFF;
    color: #64748B;
    transition: all 0.18s ease;
    font-size: 0.88rem;
    padding: 0;
}

.bento-act-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
}

.bento-act-btn.act-edit:hover {
    background: #EFF6FF;
    color: #2563EB;
    border-color: #BFDBFE;
}

.bento-act-btn.act-key:hover {
    background: #FFFBEB;
    color: #D97706;
    border-color: #FDE68A;
}

.bento-act-btn.act-toggle:hover {
    background: #F1F5F9;
    color: #0F172A;
    border-color: #CBD5E1;
}

.bento-act-btn.act-delete:hover {
    background: #FEF2F2;
    color: #DC2626;
    border-color: #FECACA;
}
</style>
@endpush

@section('content')
<div class="bento-users-wrapper">

    <!-- 1. Header & Quick Actions -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Manajemen Pengguna</h1>
            <p class="text-muted small mb-0">Kelola akun, hak akses peran, nomor kontak, serta status aktif/nonaktif akun sistem SIMPLE.</p>
        </div>
        <div>
            <button type="button" class="btn btn-bps-orange" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Bento Stat Cards (5 KPI Cards) -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-3 mb-4">
        <!-- Total Pengguna -->
        <div class="col">
            <div class="bento-stat-user">
                <div class="bento-stat-icon-circle" style="background: #E0F2FE; color: #0284C7;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="bento-stat-number">{{ number_format($totalUsers) }}</div>
                    <div class="bento-stat-label-text">Total Pengguna</div>
                    <div class="bento-stat-desc-text">Semua akun terdaftar</div>
                </div>
            </div>
        </div>

        <!-- Administrator -->
        <div class="col">
            <div class="bento-stat-user">
                <div class="bento-stat-icon-circle" style="background: #EDE9FE; color: #7C3AED;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <div class="bento-stat-number">{{ number_format($totalAdmins) }}</div>
                    <div class="bento-stat-label-text">Administrator</div>
                    <div class="bento-stat-desc-text">Akses penuh sistem</div>
                </div>
            </div>
        </div>

        <!-- Pegawai / PML -->
        <div class="col">
            <div class="bento-stat-user">
                <div class="bento-stat-icon-circle" style="background: #E0F2FE; color: #0369A1;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="bento-stat-number">{{ number_format($totalPegawai) }}</div>
                    <div class="bento-stat-label-text">Pegawai / PML</div>
                    <div class="bento-stat-desc-text">Lapangan &amp; monitoring</div>
                </div>
            </div>
        </div>

        <!-- Petugas Lapangan -->
        <div class="col">
            <div class="bento-stat-user">
                <div class="bento-stat-icon-circle" style="background: #DCFCE7; color: #16A34A;">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <div>
                    <div class="bento-stat-number">{{ number_format($totalOfficers) }}</div>
                    <div class="bento-stat-label-text">Petugas Lapangan</div>
                    <div class="bento-stat-desc-text">Pelaksana tugas &amp; BAST</div>
                </div>
            </div>
        </div>

        <!-- Akun Nonaktif -->
        <div class="col">
            <div class="bento-stat-user">
                <div class="bento-stat-icon-circle" style="background: {{ $totalInactive > 0 ? '#FEE2E2' : '#F1F5F9' }}; color: {{ $totalInactive > 0 ? '#DC2626' : '#64748B' }};">
                    <i class="bi bi-person-slash"></i>
                </div>
                <div>
                    <div class="bento-stat-number {{ $totalInactive > 0 ? 'text-danger' : '' }}">{{ number_format($totalInactive) }}</div>
                    <div class="bento-stat-label-text">Akun Nonaktif</div>
                    <div class="bento-stat-desc-text">Akses ditangguhkan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Search & Filter Bento Card -->
    <div class="bento-card-clean p-3 mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm border-start-0 ps-2" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, username, email, nomor HP..."
                           style="border-radius: 0 10px 10px 0;">
                </div>
            </div>

            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm" style="border-radius: 10px;">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="pegawai" {{ request('role') === 'pegawai' ? 'selected' : '' }}>Pegawai / PML</option>
                    <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" style="border-radius: 10px;">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark flex-fill fw-semibold d-flex align-items-center justify-content-center gap-1.5" style="border-radius: 10px; background: #0F172A;">
                    <i class="bi bi-funnel-fill"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 10px; width: 36px;" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Table Bento Card -->
    <div class="bento-card-clean overflow-hidden mb-4">
        <div class="px-4 py-3 bg-white border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-circle p-1.5" style="background: rgba(243, 112, 33, 0.12); color: #F37021;">
                    <i class="bi bi-person-lines-fill"></i>
                </span>
                <h5 class="fw-bold text-dark mb-0 fs-6">Daftar Pengguna Sistem</h5>
                <span class="badge rounded-pill bg-light text-secondary border fw-normal" style="font-size: 0.72rem;">{{ $users->total() }} Pengguna</span>
            </div>
            <div class="small text-muted">
                Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table bento-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 30%;">Pengguna</th>
                        <th style="width: 15%;">Role</th>
                        <th style="width: 23%;">Kontak</th>
                        <th style="width: 12%;">Status Akun</th>
                        <th style="width: 10%;">Terdaftar</th>
                        <th class="text-end" style="width: 10%;">Aksi</th>
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
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar-circle {{ $avatarClass }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                            <span class="text-muted small">@<span>{{ $user->username }}</span></span>
                                            @if($user->nik)
                                                <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                    NIK: {{ $user->nik }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-weight: 600; font-size: 0.73rem;">
                                        <i class="bi bi-shield-check me-1"></i> Administrator
                                    </span>
                                @elseif($user->role === 'pegawai')
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-weight: 600; font-size: 0.73rem;">
                                        <i class="bi bi-person-badge me-1"></i> Pegawai / PML
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: #FFF4ED; color: #C2410C; border: 1px solid #FFEDD5; font-weight: 600; font-size: 0.73rem;">
                                        <i class="bi bi-person-check me-1"></i> Petugas
                                    </span>
                                @endif
                            </td>

                            <!-- Kontak -->
                            <td>
                                <div class="small text-dark text-truncate mb-0.5" title="{{ $user->email }}">
                                    <i class="bi bi-envelope text-muted me-1.5"></i> {{ $user->email }}
                                </div>
                                <div class="small text-muted text-truncate">
                                    <i class="bi bi-telephone text-muted me-1.5"></i> {{ $user->phone ?: '-' }}
                                </div>
                            </td>

                            <!-- Status Akun -->
                            <td>
                                @if($user->isActive())
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; font-weight: 600; font-size: 0.73rem;">
                                        <i class="bi bi-check-circle-fill me-1 text-success"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; font-weight: 600; font-size: 0.73rem;">
                                        <i class="bi bi-x-circle-fill me-1 text-danger"></i> Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Terdaftar -->
                            <td>
                                <div class="small fw-semibold text-dark">{{ $user->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ $user->created_at->diffForHumans() }}</div>
                            </td>

                            <!-- Aksi -->
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- Edit -->
                                    <button type="button" 
                                            class="bento-act-btn act-edit" 
                                            title="Edit Data Pengguna" 
                                            data-bs-toggle="tooltip"
                                            onclick="openEditModal({{ json_encode($user) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Reset Password -->
                                    <button type="button" 
                                            class="bento-act-btn act-key" 
                                            title="Reset Password" 
                                            data-bs-toggle="tooltip"
                                            onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                        <i class="bi bi-key-fill"></i>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <!-- Toggle Status Aktif / Nonaktif -->
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline" onsubmit="return confirm('Ubah status akun {{ addslashes($user->name) }} menjadi {{ $user->isActive() ? 'Nonaktif' : 'Aktif' }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="bento-act-btn act-toggle {{ $user->isActive() ? 'text-secondary' : 'text-success' }}" 
                                                    title="{{ $user->isActive() ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                    data-bs-toggle="tooltip">
                                                <i class="bi {{ $user->isActive() ? 'bi-person-slash' : 'bi-person-check' }}"></i>
                                            </button>
                                        </form>

                                        <!-- Hapus -->
                                        <button type="button" 
                                                class="bento-act-btn act-delete" 
                                                title="Hapus Pengguna" 
                                                data-bs-toggle="tooltip"
                                                onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                            <i class="bi bi-trash3-fill"></i>
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
            <div class="px-4 py-3 bg-white border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} data
                </div>
                <div>
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- ==============================================================
     MODAL TAMBAH PENGGUNA
     ============================================================== -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="createUserModalLabel">
                    <span class="badge rounded-circle p-1.5" style="background: rgba(243, 112, 33, 0.12); color: #F37021;">
                        <i class="bi bi-person-plus-fill"></i>
                    </span>
                    <span>Tambah Pengguna Baru</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NAMA LENGKAP <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Ahmad Fauzi, S.Kom" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">USERNAME <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Contoh: ahmad_fauzi" required style="border-radius: 10px;">
                            <small class="text-muted" style="font-size: 0.72rem;">Digunakan untuk login ke sistem.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">EMAIL RESMI <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contoh: ahmad@simple.go.id" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">NIK / NIP PETUGAS</label>
                            <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="Contoh: 7408011505950001" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">PANGKAT / GOLONGAN</label>
                            <input type="text" name="rank" class="form-control" value="{{ old('rank', '-') }}" placeholder="Contoh: - atau Pengatur, II/d" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">JABATAN</label>
                            <input type="text" name="position" class="form-control" value="{{ old('position', 'Mitra BPS Kabupaten Kolaka Utara') }}" placeholder="Jabatan petugas" style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KATA SANDI AWAL <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">ROLE / HAK AKSES <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required style="border-radius: 10px;">
                                <option value="petugas" selected>Petugas Lapangan</option>
                                <option value="pegawai">Pegawai / PML</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STATUS AKUN <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required style="border-radius: 10px;">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-2.5">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy-fill"></i> Simpan Pengguna
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
        <div class="modal-content border-0 shadow" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="editUserModalLabel">
                    <span class="badge rounded-circle p-1.5" style="background: rgba(2, 132, 199, 0.12); color: #0284C7;">
                        <i class="bi bi-pencil-square"></i>
                    </span>
                    <span>Edit Data Pengguna</span>
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
                            <input type="text" id="edit_name" name="name" class="form-control" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">USERNAME <span class="text-danger">*</span></label>
                            <input type="text" id="edit_username" name="username" class="form-control" required style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">EMAIL RESMI <span class="text-danger">*</span></label>
                            <input type="email" id="edit_email" name="email" class="form-control" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">NIK / NIP PETUGAS</label>
                            <input type="text" id="edit_nik" name="nik" class="form-control" placeholder="Contoh: 7408011505950001" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">PANGKAT / GOLONGAN</label>
                            <input type="text" id="edit_rank" name="rank" class="form-control" placeholder="-" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">JABATAN</label>
                            <input type="text" id="edit_position" name="position" class="form-control" placeholder="Mitra BPS Kabupaten Kolaka Utara" style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ROLE / HAK AKSES <span class="text-danger">*</span></label>
                            <select id="edit_role" name="role" class="form-select" required style="border-radius: 10px;">
                                <option value="petugas">Petugas Lapangan</option>
                                <option value="pegawai">Pegawai / PML</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">STATUS AKUN <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required style="border-radius: 10px;">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <label class="form-label small fw-semibold text-secondary mb-1">UBAH PASSWORD (OPSIONAL)</label>
                        <input type="password" name="password" class="form-control bg-white" placeholder="Kosongkan jika tidak ingin mengubah password" style="border-radius: 10px;">
                        <small class="text-muted" style="font-size: 0.72rem;">Isi kolom ini hanya jika ingin mengganti password akun.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-2.5">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-bps-orange">
                        <i class="bi bi-floppy-fill"></i> Simpan Perubahan
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
        <div class="modal-content border-0 shadow" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="resetPasswordModalLabel">
                    <span class="badge rounded-circle p-1.5" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                        <i class="bi bi-key-fill"></i>
                    </span>
                    <span>Reset Password</span>
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
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-2.5">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4">
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
        <div class="modal-content border-0 shadow" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="badge rounded-circle p-3" style="background: #FEE2E2; color: #DC2626; font-size: 2rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
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
