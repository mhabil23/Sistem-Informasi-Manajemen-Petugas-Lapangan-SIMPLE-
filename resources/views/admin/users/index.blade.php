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

    /* Bulk Action Buttons - Clean, Modern & Elegant */
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
    .btn-bulk-action-danger {
        background: #ffffff;
        border: 1px solid #fecaca;
        color: #dc2626;
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
    .btn-bulk-action-danger:hover {
        background: #fef2f2;
        border-color: #f87171;
        color: #b91c1c;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(220, 38, 38, 0.15);
    }
    .bento-table tbody tr.row-selected td {
        background-color: #f0f7ff !important;
    }

    /* Mobile Responsive Card Layout */
    .user-stats-scroll {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding-bottom: 2px;
    }
    .user-stats-scroll::-webkit-scrollbar {
        display: none;
    }

    @media (max-width: 767.98px) {
        .user-action-buttons {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 0.45rem !important;
            width: 100% !important;
            margin-top: 0.6rem;
        }
        .user-action-buttons .btn {
            width: 100%;
            padding: 0.42rem 0.5rem !important;
            font-size: 0.76rem !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bento-table-container-fixed {
            padding: 0.5rem !important;
        }
        .bento-table thead {
            display: none !important;
        }
        .bento-table, .bento-table tbody, .bento-table tr, .bento-table td {
            display: block;
            width: 100% !important;
        }
        .bento-table tr {
            position: relative;
            margin-bottom: 0.85rem;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0.85rem;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            text-align: left !important;
        }
        .bento-table tbody tr.row-selected {
            background-color: #f0f7ff !important;
            border-color: #93c5fd !important;
        }

        /* Checkbox Pilihan (Top-Right of card) */
        .bento-table td[data-label="Pilih"] {
            position: absolute;
            top: 0.85rem;
            right: 0.85rem;
            width: auto !important;
            padding: 0 !important;
            border: none !important;
            text-align: right !important;
            z-index: 2;
        }
        .bento-table td[data-label="Pilih"]::before {
            display: none !important;
        }
        .bento-table td[data-label="Pilih"] .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            cursor: pointer;
        }

        /* Pengguna Section (Header of card) */
        .bento-table td[data-label="Pengguna"] {
            padding: 0 2.2rem 0.5rem 0 !important;
            border: none !important;
            border-bottom: 1px solid #f1f5f9 !important;
            text-align: left !important;
        }
        .bento-table td[data-label="Pengguna"]::before {
            display: none !important;
        }
        .bento-table td[data-label="Pengguna"] .user-avatar-circle {
            width: 38px;
            height: 38px;
            font-size: 0.95rem;
        }
        .bento-table td[data-label="Pengguna"] .fw-bold {
            font-size: 0.92rem;
            color: #0f172a;
        }

        /* Role & Status (Inline Badges Row) */
        .bento-table td[data-label="Role"] {
            display: inline-block !important;
            width: auto !important;
            padding: 0.45rem 0.4rem 0.25rem 0 !important;
            border: none !important;
            text-align: left !important;
        }
        .bento-table td[data-label="Role"]::before {
            display: none !important;
        }

        .bento-table td[data-label="Status Akun"] {
            display: inline-block !important;
            width: auto !important;
            padding: 0.45rem 0 0.25rem 0 !important;
            border: none !important;
            text-align: left !important;
        }
        .bento-table td[data-label="Status Akun"]::before {
            display: none !important;
        }

        /* Kontak */
        .bento-table td[data-label="Kontak"] {
            display: block !important;
            padding: 0.45rem 0 !important;
            border: none !important;
            border-top: 1px dashed #f1f5f9 !important;
            text-align: left !important;
            font-size: 0.78rem !important;
        }
        .bento-table td[data-label="Kontak"]::before {
            display: none !important;
        }

        /* Terdaftar */
        .bento-table td[data-label="Terdaftar"] {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            padding: 0.35rem 0 !important;
            border: none !important;
            border-top: 1px dashed #f1f5f9 !important;
            font-size: 0.72rem !important;
            color: #64748b;
        }
        .bento-table td[data-label="Terdaftar"]::before {
            content: "Terdaftar:" !important;
            font-weight: 500;
            color: #94a3b8;
            float: none !important;
        }
        .bento-table td[data-label="Terdaftar"] .small {
            display: inline-flex;
            gap: 0.3rem;
            align-items: center;
        }

        /* Aksi */
        .bento-table td[data-label="Aksi"] {
            display: block !important;
            padding: 0.5rem 0 0 0 !important;
            border: none !important;
            border-top: 1px solid #f1f5f9 !important;
            margin-top: 0.3rem;
            text-align: right !important;
        }
        .bento-table td[data-label="Aksi"]::before {
            display: none !important;
        }
        .bento-table td[data-label="Aksi"] .d-inline-flex {
            width: 100%;
            justify-content: flex-end;
            gap: 0.45rem;
        }
        .bento-table td[data-label="Aksi"] .bento-action-btn {
            width: 32px;
            height: 32px;
            font-size: 0.85rem;
            border-radius: 8px;
        }

        /* Bulk Action Bar Mobile */
        #bulkActionBar {
            padding: 0.5rem 0.75rem !important;
        }
        #bulkActionBar .btn-bulk-action,
        #bulkActionBar .btn-bulk-action-danger {
            padding: 0.28rem 0.6rem;
            font-size: 0.72rem;
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
                    <option value="dual_role" {{ request('role') === 'dual_role' ? 'selected' : '' }}>Dual Role (+Admin)</option>
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
            <div class="user-stats-scroll gap-1 mt-1">
                <span class="badge bg-light text-secondary border">Total Pengguna: {{ $totalUsers }}</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Pegawai: {{ $totalPegawai }}</span>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Dual Role (+Admin): {{ $totalDualRole }}</span>
                <span class="badge bg-light text-secondary border">Petugas: {{ $totalOfficers }}</span>
                <span class="badge bg-light text-secondary border">Admin: {{ $totalAdmins }}</span>
            </div>
        </div>
        <div class="user-action-buttons d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-success rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#importMitraModal" style="font-size: 0.82rem;" title="Upload file Excel daftar mitra untuk membuat/memperbarui akun petugas secara otomatis">
                <i class="bi bi-file-earmark-excel-fill text-success me-1"></i> Import Mitra
            </button>
            <button type="button" class="btn btn-bps-orange rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#createUserModal" style="font-size: 0.82rem;">
                <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
            </button>
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#managePegawaiAdminModal" style="font-size: 0.82rem;" title="Pilih pegawai sekaligus untuk diberikan atau dicabut hak akses Administrator">
                <i class="bi bi-shield-check text-primary me-1"></i> Hak Admin Pegawai
            </button>
            <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#revokeAllPegawaiAdminModal" style="font-size: 0.82rem;" title="Tutup seluruh akses Administrator untuk semua pegawai secara serentak">
                <i class="bi bi-shield-slash me-1"></i> Tutup Akses Admin
            </button>
        </div>
    </div>

    <!-- Bulk Action Toolbar (Modern, Bersih & Elegan) -->
    <div id="bulkActionBar" class="d-none px-3 py-2.5 border-bottom border-top align-items-center justify-content-between flex-wrap gap-2" style="background: #f8fafc; border-color: #e2e8f0 !important;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #005BAC; color: #ffffff; font-size: 0.78rem;">
                <i class="bi bi-check2-circle fs-6"></i>
                <span id="bulkSelectedCountBadge">0 Pengguna Dipilih</span>
            </span>
            <span class="text-muted small">|</span>
            <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none p-0 d-inline-flex align-items-center gap-1" onclick="clearAllSelectedUsers()" style="font-size: 0.78rem;">
                <i class="bi bi-x-circle text-muted"></i> Batal Pilih
            </button>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn-bulk-action" onclick="submitBulkAction('grant_admin')" title="Berikan hak akses Administrator (Dual Role) untuk pegawai yang dicentang">
                <i class="bi bi-shield-plus text-success fs-6"></i> Beri Akses Admin
            </button>
            <button type="button" class="btn-bulk-action" onclick="submitBulkAction('revoke_admin')" title="Cabut hak akses Administrator dari pengguna yang dicentang">
                <i class="bi bi-shield-minus text-warning fs-6"></i> Cabut Akses Admin
            </button>
            <button type="button" class="btn-bulk-action" onclick="submitBulkAction('activate')" title="Aktifkan status akun yang dicentang">
                <i class="bi bi-person-check text-info fs-6"></i> Aktifkan
            </button>
            <button type="button" class="btn-bulk-action" onclick="submitBulkAction('deactivate')" title="Nonaktifkan status akun yang dicentang">
                <i class="bi bi-person-slash text-secondary fs-6"></i> Nonaktifkan
            </button>
            <button type="button" class="btn-bulk-action-danger" onclick="submitBulkAction('delete')" title="Hapus pengguna yang dicentang">
                <i class="bi bi-trash fs-6"></i> Hapus
            </button>
        </div>
    </div>

    <div class="bento-table-container-fixed">
        <table class="bento-table align-middle">
            <thead>
                <tr>
                    <th style="width: 38px;" class="text-center">
                        <input type="checkbox" id="selectAllUsersCheckbox" class="form-check-input" onchange="toggleSelectAllUsers(this)" title="Pilih Semua Pengguna di Halaman Ini">
                    </th>
                    <th style="width: 24%;">PENGGUNA</th>
                    <th style="width: 14%;">ROLE</th>
                    <th style="width: 20%;">KONTAK</th>
                    <th class="text-center" style="width: 11%;">STATUS AKUN</th>
                    <th style="width: 10%;">TERDAFTAR</th>
                    <th class="text-end pe-3" style="width: 18%;">AKSI</th>
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
                        <!-- Checkbox Pilihan -->
                        <td class="text-center" data-label="Pilih">
                            <input type="checkbox" 
                                   name="user_ids[]" 
                                   value="{{ $user->id }}" 
                                   class="form-check-input user-row-checkbox" 
                                   data-name="{{ $user->name }}"
                                   data-is-master="{{ $user->isMasterAdmin() ? '1' : '0' }}"
                                   onchange="onUserRowCheckboxChange()">
                        </td>

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
                                        @if($user->sobat_id)
                                            <span class="badge bg-light text-success border font-monospace px-1.5 py-0.5" style="font-size: 0.65rem;" title="ID SOBAT: {{ $user->sobat_id }}">
                                                <i class="bi bi-person-vcard me-0.5"></i>SOBAT: {{ $user->sobat_id }}
                                            </span>
                                        @endif
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
                            @if($user->isMasterAdmin())
                                <span class="badge rounded-pill px-2.5 py-1" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E; border: 1px solid #F59E0B; font-weight: 700; font-size: 0.72rem;">
                                    <i class="bi bi-shield-shaded me-1 text-warning"></i> Master Admin
                                </span>
                            @elseif($user->role === 'admin')
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-weight: 600; font-size: 0.72rem;">
                                    <i class="bi bi-shield-check me-1"></i> Administrator
                                </span>
                            @elseif($user->role === 'pegawai')
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-weight: 600; font-size: 0.72rem;">
                                    <i class="bi bi-person-badge me-1"></i> Pegawai / PML
                                </span>
                                @if($user->can_switch_role)
                                    <div class="mt-1">
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Memiliki hak Dual Role (bisa beralih peran jadi Admin)">
                                            <i class="bi bi-arrow-left-right me-0.5 text-primary"></i> Dual Role (+Admin)
                                        </span>
                                    </div>
                                @endif
                            @else
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #FFF4ED; color: #C2410C; border: 1px solid #FFEDD5; font-weight: 600; font-size: 0.72rem;">
                                    <i class="bi bi-person-check me-1"></i> Petugas
                                </span>
                                @if($user->can_switch_role)
                                    <div class="mt-1">
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Memiliki hak Dual Role (bisa beralih peran jadi Admin)">
                                            <i class="bi bi-arrow-left-right me-0.5 text-primary"></i> Dual Role (+Admin)
                                        </span>
                                    </div>
                                @endif
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
                                @if($user->isMasterAdmin())
                                    @if(auth()->user()->isMasterAdmin())
                                        <!-- Edit Master Admin (Hanya Master Admin Sendiri) -->
                                        <button type="button" 
                                                class="bento-action-btn btn-edit" 
                                                title="Edit Data Master Admin" 
                                                data-bs-toggle="tooltip"
                                                onclick="openEditModal({{ json_encode($user) }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <!-- Reset Password Master Admin -->
                                        <button type="button" 
                                                class="bento-action-btn btn-key" 
                                                title="Reset Password Master Admin" 
                                                data-bs-toggle="tooltip"
                                                onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    @else
                                        <!-- Jika Dilihat Oleh Akun Admin Lain / Petugas Admin -->
                                        <span class="badge rounded-pill px-2.5 py-1 text-muted border bg-light d-inline-flex align-items-center" style="font-size: 0.72rem;" title="Akun Master Admin Terlindungi &amp; Hak Akses Permanen">
                                            <i class="bi bi-shield-lock-fill text-warning me-1"></i> Terlindungi
                                        </span>
                                    @endif
                                @else
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
                                        @if($user->can_switch_role || $user->role === 'admin')
                                            <!-- Tutup Akses Administrator -->
                                            <form method="POST" action="{{ route('admin.users.revoke-admin', $user) }}" class="d-inline" onsubmit="return confirm('Tutup akses Administrator untuk {{ addslashes($user->name) }}? Pengguna tidak akan bisa lagi mengakses atau beralih ke Mode Admin.');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="bento-action-btn" 
                                                        style="color: #DC2626; border-color: #FECACA; background: #FEF2F2;"
                                                        title="Tutup Akses Administrator" 
                                                        data-bs-toggle="tooltip">
                                                    <i class="bi bi-shield-slash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Buka Akses Administrator (Dual Role) -->
                                            <form method="POST" action="{{ route('admin.users.grant-admin', $user) }}" class="d-inline" onsubmit="return confirm('Buka akses Administrator (Dual Role) untuk {{ addslashes($user->name) }}? Pengguna akan dapat beralih ke Mode Admin.');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="bento-action-btn" 
                                                        style="color: #059669; border-color: #A7F3D0; background: #ECFDF5;"
                                                        title="Buka Akses Administrator (Dual Role)" 
                                                        data-bs-toggle="tooltip">
                                                    <i class="bi bi-shield-plus"></i>
                                                </button>
                                            </form>
                                        @endif

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
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
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
     MODAL IMPORT EXCEL MITRA LAPANGAN
     ============================================================== -->
<div class="modal fade" id="importMitraModal" tabindex="-1" aria-labelledby="importMitraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 text-white d-flex align-items-center justify-content-center" style="background: #198754; width: 34px; height: 34px;">
                        <i class="bi bi-file-earmark-excel-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="importMitraModalLabel" style="font-size: 1.05rem;">
                            Import Daftar Mitra Lapangan (Excel / CSV)
                        </h5>
                        <div class="text-muted" style="font-size: 0.73rem;">Unggah berkas rekrutmen / database mitra BPS untuk membuat dan memperbarui akun secara otomatis</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.import-mitra') }}" enctype="multipart/form-data" id="importMitraForm">
                @csrf
                <div class="modal-body p-4">
                    <!-- Template Download & Format Info Card -->
                    <div class="p-3 mb-3 rounded-3 border bg-light-subtle d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <i class="bi bi-file-earmark-arrow-down fs-2 text-success"></i>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">Format Rekrutmen BPS / Template SIMPEL</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Mendukung format ekspor resmi SOBAT (kolom Nama, SOBAT ID, Email, No HP, Kecamatan, Desa, dll).</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center shrink-0">
                            <a href="{{ route('admin.users.template-mitra') }}" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1.5 shadow-sm fw-semibold" style="font-size: 0.78rem;">
                                <i class="bi bi-download me-1"></i> Unduh Format Contoh
                            </a>
                        </div>
                    </div>

                    <!-- Informasi Kolom yang Dibaca vs Diabaikan -->
                    <div class="p-3 mb-3 rounded-3 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="bi bi-info-circle-fill text-primary" style="font-size: 0.95rem;"></i>
                                <span class="fw-bold text-dark" style="font-size: 0.84rem;">Ketentuan Kolom Excel: Apa yang Dibaca &amp; Diabaikan</span>
                            </div>
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.68rem; font-weight: 700;">
                                <i class="bi bi-shield-check me-0.5"></i>Aman Kolom Ekstra
                            </span>
                        </div>

                        <p class="text-muted mb-2" style="font-size: 0.74rem; line-height: 1.5;">
                            Sistem secara cerdas mengenali identitas mitra (seperti <strong>SOBAT ID, ID Mitra, Kode Petugas, atau NIK</strong>). <strong>Jika terdapat field/kolom lain pada file Excel Anda, sistem akan secara otomatis MENGABAIKANNYA</strong> sehingga Anda tidak perlu menghapus kolom-kolom tambahan dari hasil unduhan aplikasi SOBAT BPS.
                        </p>

                        <div class="row g-2">
                            <!-- Kolom yang DIBACA -->
                            <div class="col-lg-7 col-md-12">
                                <div class="p-2.5 rounded-3 border bg-white h-100 shadow-2xs">
                                    <div class="fw-bold text-success d-flex align-items-center gap-1 mb-1.5" style="font-size: 0.75rem; letter-spacing: 0.02em;">
                                        <i class="bi bi-check-circle-fill text-success"></i> DATA YANG DIBACA OLEH SISTEM:
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-person me-1 text-primary"></i>Nama Lengkap</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-card-text me-1 text-primary"></i>SOBAT ID / ID Mitra / NIK</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-envelope me-1 text-primary"></i>Email</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-telephone me-1 text-primary"></i>No Telp / WA</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-geo-alt me-1 text-primary"></i>Kecamatan</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-pin-map me-1 text-primary"></i>Desa / Kelurahan</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-house me-1 text-primary"></i>Alamat Detail</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-gender-ambiguous me-1 text-primary"></i>Jenis Kelamin</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-calendar-event me-1 text-primary"></i>TTL</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-mortarboard me-1 text-primary"></i>Pendidikan</span>
                                        <span class="badge bg-light text-dark border py-1 px-1.5" style="font-size: 0.68rem;"><i class="bi bi-briefcase me-1 text-primary"></i>Posisi</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom yang DIABAIKAN -->
                            <div class="col-lg-5 col-md-12">
                                <div class="p-2.5 rounded-3 border bg-white h-100 shadow-2xs">
                                    <div class="fw-bold text-secondary d-flex align-items-center gap-1 mb-1.5" style="font-size: 0.75rem; letter-spacing: 0.02em;">
                                        <i class="bi bi-dash-circle-fill text-muted"></i> FIELD LAIN OTOMATIS DIABAIKAN:
                                    </div>
                                    <div class="text-muted" style="font-size: 0.71rem; line-height: 1.45;">
                                        Kolom seperti <em>Status Seleksi, Umur, Status Kawin, Punya Motor, Pengalaman CAPI, Merk HP, RAM, Pernah SP/ST/SE/Susenas/Sakernas</em>, dll. akan dilewati secara aman tanpa mengganggu proses import.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Highlight Ketentuan Akun & Login -->
                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100" style="background: #FFFBEB; border-color: #FDE68A !important;">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-key-fill text-warning fs-5"></i>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">Password Bawaan</span>
                                </div>
                                <div class="small text-muted" style="font-size: 0.75rem; line-height: 1.45;">
                                    Seluruh akun mitra yang diimpor otomatis memiliki kata sandi bawaan resmi: 
                                    <code class="px-1.5 py-0.5 rounded bg-white text-danger fw-bold border">bps7408</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100" style="background: #EFF6FF; border-color: #BFDBFE !important;">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-person-badge-fill text-primary fs-5"></i>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">Fleksibilitas Login</span>
                                </div>
                                <div class="small text-muted" style="font-size: 0.75rem; line-height: 1.45;">
                                    Petugas/Mitra dapat langsung masuk ke portal menggunakan <strong>ID SOBAT / ID Mitra / NIK</strong> ataupun <strong>Email</strong> yang terdaftar.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Input -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.8rem; letter-spacing: 0.02em;">
                            PILIH BERKAS SPREADSHEET (.XLSX / .XLS / .CSV) <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               name="excel_file" 
                               class="form-control @error('excel_file') is-invalid @enderror" 
                               accept=".xlsx,.xls,.csv" 
                               required>
                        @error('excel_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted" style="font-size: 0.72rem;">
                            Ukuran berkas maksimal 20 MB. Sistem akan membaca otomatis kolom profil mitra termasuk ID SOBAT, nama, wilayah tugas, dan kontak.
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-1.5 shadow-sm fw-semibold" id="btnSubmitImportMitra" style="font-size: 0.85rem;">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah &amp; Import Mitra
                    </button>
                </div>
            </form>
        </div>
    </div>
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

                    <div class="row g-3 mb-3">
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
                        <div class="col-12">
                            <div class="form-check form-switch p-2.5 ps-5 rounded-3 border" style="background: #f8fafc;">
                                <input class="form-check-input mt-1" type="checkbox" role="switch" id="create_can_switch_role" name="can_switch_role" value="1">
                                <label class="form-check-label fw-bold text-dark small" for="create_can_switch_role">
                                    <i class="bi bi-arrow-left-right text-primary me-1"></i> Izinkan Dual Role (Bisa Beralih Jadi Administrator &amp; Pegawai)
                                </label>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    Bila diaktifkan, pengguna dapat beralih ke Mode Administrator langsung dari menu profil di kanan atas.
                                </div>
                            </div>
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
                    <div id="edit_master_admin_notice" class="alert alert-warning d-none py-2 px-3 small border-0 mb-3 d-flex align-items-center gap-2" style="background: #FFFBEB; color: #92400E; border-radius: 12px;">
                        <i class="bi bi-shield-lock-fill fs-5 text-warning flex-shrink-0"></i>
                        <div>
                            <strong>Akun Master Admin Terlindungi:</strong>
                            Role Administrator dan status aktif dilindungi permanen untuk menjamin keamanan sistem SIMPLE.
                        </div>
                    </div>
                    <input type="hidden" id="edit_role_hidden" name="role" value="admin" disabled>
                    <input type="hidden" id="edit_status_hidden" name="status" value="active" disabled>

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
                        <div class="col-12">
                            <div class="form-check form-switch p-2.5 ps-5 rounded-3 border" style="background: #f8fafc;">
                                <input class="form-check-input mt-1" type="checkbox" role="switch" id="edit_can_switch_role" name="can_switch_role" value="1">
                                <label class="form-check-label fw-bold text-dark small" for="edit_can_switch_role">
                                    <i class="bi bi-arrow-left-right text-primary me-1"></i> Izinkan Dual Role (Bisa Beralih Jadi Administrator &amp; Pegawai)
                                </label>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    Bila diaktifkan, pengguna dapat beralih ke Mode Administrator langsung dari menu profil di kanan atas.
                                </div>
                            </div>
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

<!-- ==============================================================
     MODAL TUTUP SEMUA AKSES ADMIN PEGAWAI
     ============================================================== -->
<div class="modal fade" id="revokeAllPegawaiAdminModal" tabindex="-1" aria-labelledby="revokeAllPegawaiAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="badge rounded-circle p-3" style="background: #FEE2E2; color: #DC2626; font-size: 2.2rem;">
                        <i class="bi bi-shield-x"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-2 text-dark">Tutup Semua Akses Admin Pegawai?</h5>
                <p class="text-muted small mb-4">
                    Tindakan ini akan mencabut izin Administrator (Dual Role) dari <strong>seluruh akun pegawai</strong> secara serentak. Pegawai yang bersangkutan hanya akan memiliki akses sebagai Pegawai/PML biasa dan tidak dapat beralih ke Mode Admin sampai izin dibuka kembali.
                </p>
                <form method="POST" action="{{ route('admin.users.revoke-all-pegawai-admin') }}">
                    @csrf
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                            <i class="bi bi-shield-slash me-1"></i> Ya, Tutup Semua Akses Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL KELOLA HAK ADMIN PEGAWAI (PILIH BANYAK / CHECKLIST)
     ============================================================== -->
<div class="modal fade" id="managePegawaiAdminModal" tabindex="-1" aria-labelledby="managePegawaiAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="managePegawaiAdminModalLabel">
                        <i class="bi bi-shield-check text-warning me-2"></i> Kelola Hak Akses Admin Pegawai (Pilih Banyak)
                    </h5>
                    <div class="text-muted small mt-0.5">
                        Centang pegawai yang diizinkan memiliki <strong>Dual Role (+Admin)</strong> untuk beralih jadi Administrator.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.update-multiple-pegawai-admin') }}">
                @csrf
                <div class="modal-body p-4">
                    <!-- Search inside modal & Quick Actions -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="input-group input-group-sm" style="max-width: 320px;">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="pegawaiModalSearch" class="form-control" placeholder="Cari nama atau NIP pegawai..." onkeyup="filterPegawaiModalList()">
                        </div>
                        <div class="d-flex gap-1.5">
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 small" onclick="toggleAllModalPegawai(true)">
                                <i class="bi bi-check-all me-1"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 small" onclick="toggleAllModalPegawai(false)">
                                <i class="bi bi-x-circle me-1"></i> Hapus Semua Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Checklist container -->
                    <div class="border rounded-3 p-2 bg-light" style="max-height: 380px; overflow-y: auto;">
                        <div class="row g-2" id="pegawaiModalList">
                            @forelse(($allPegawai ?? []) as $peg)
                                <div class="col-md-6 pegawai-modal-item" data-name="{{ strtolower($peg->name) }}" data-nip="{{ strtolower($peg->nik ?? '') }}">
                                    <label class="d-flex align-items-center gap-2.5 p-2 bg-white rounded-2 border h-100 cursor-pointer shadow-xs hover-bg-light">
                                        <input type="checkbox" name="pegawai_ids[]" value="{{ $peg->id }}" class="form-check-input flex-shrink-0 pegawai-modal-cb" {{ $peg->can_switch_role ? 'checked' : '' }}>
                                        <div class="overflow-hidden flex-grow-1">
                                            <div class="fw-semibold text-dark text-truncate small">{{ $peg->name }}</div>
                                            <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.68rem;">
                                                <span class="font-monospace">{{ $peg->nik ?: '@'.$peg->username }}</span>
                                                @if($peg->position)
                                                    <span class="text-truncate">&bull; {{ $peg->position }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($peg->can_switch_role)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.62rem;">
                                                Aktif
                                            </span>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-4">
                                    Belum ada data pegawai terdaftar.
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="form-text mt-2 text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Pegawai yang dicentang akan dapat beralih ke Mode Administrator. Pegawai yang tidak dicentang akan tetap sebagai Pegawai / PML biasa.
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between align-items-center">
                    <span class="small text-muted" id="modalPegawaiCheckedCount">0 pegawai dipilih</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-bps-orange rounded-pill px-4 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Hak Akses
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Tersembunyi untuk Eksekusi Aksi Massal Checkbox Tabel -->
<form id="bulkActionForm" method="POST" action="{{ route('admin.users.bulk-action') }}" class="d-none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <div id="bulkActionUserIdsContainer"></div>
</form>
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

        const isMaster = Boolean(user.is_master_admin || user.username === 'admin');
        const roleSelect = document.getElementById('edit_role');
        const statusSelect = document.getElementById('edit_status');
        const switchRoleCheck = document.getElementById('edit_can_switch_role');
        const masterNotice = document.getElementById('edit_master_admin_notice');
        const roleHidden = document.getElementById('edit_role_hidden');
        const statusHidden = document.getElementById('edit_status_hidden');

        roleSelect.value = user.role;
        statusSelect.value = user.status;
        switchRoleCheck.checked = Boolean(user.can_switch_role);

        if (isMaster) {
            roleSelect.value = 'admin';
            roleSelect.disabled = true;
            statusSelect.value = 'active';
            statusSelect.disabled = true;
            switchRoleCheck.checked = true;
            switchRoleCheck.disabled = true;
            if (roleHidden) roleHidden.disabled = false;
            if (statusHidden) statusHidden.disabled = false;
            if (masterNotice) masterNotice.classList.remove('d-none');
        } else {
            roleSelect.disabled = false;
            statusSelect.disabled = false;
            switchRoleCheck.disabled = false;
            if (roleHidden) roleHidden.disabled = true;
            if (statusHidden) statusHidden.disabled = true;
            if (masterNotice) masterNotice.classList.add('d-none');
        }

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

    // ==============================================================
    // Aksi Massal (Checkbox Multi-Select)
    // ==============================================================
    function toggleSelectAllUsers(master) {
        const checkboxes = document.querySelectorAll('.user-row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = master.checked;
            const tr = cb.closest('tr');
            if (tr) {
                if (master.checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            }
        });
        onUserRowCheckboxChange();
    }

    function onUserRowCheckboxChange() {
        const allCheckboxes = document.querySelectorAll('.user-row-checkbox');
        const checkedBoxes = document.querySelectorAll('.user-row-checkbox:checked');
        const count = checkedBoxes.length;
        const bar = document.getElementById('bulkActionBar');
        const badge = document.getElementById('bulkSelectedCountBadge');
        const master = document.getElementById('selectAllUsersCheckbox');

        allCheckboxes.forEach(cb => {
            const tr = cb.closest('tr');
            if (tr) {
                if (cb.checked) tr.classList.add('row-selected');
                else tr.classList.remove('row-selected');
            }
        });

        if (badge) badge.textContent = `${count} Pengguna Dipilih`;

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
            if (master) master.checked = false;
        }
    }

    function clearAllSelectedUsers() {
        document.querySelectorAll('.user-row-checkbox').forEach(cb => {
            cb.checked = false;
            const tr = cb.closest('tr');
            if (tr) tr.classList.remove('row-selected');
        });
        const master = document.getElementById('selectAllUsersCheckbox');
        if (master) master.checked = false;
        onUserRowCheckboxChange();
    }

    function submitBulkAction(actionType) {
        const selected = Array.from(document.querySelectorAll('.user-row-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Pilih minimal satu pengguna terlebih dahulu.');
            return;
        }

        const labels = {
            'grant_admin': 'Buka hak akses Administrator (Dual Role) untuk ' + selected.length + ' pengguna terpilih?',
            'revoke_admin': 'Cabut hak akses Administrator dari ' + selected.length + ' pengguna terpilih?',
            'activate': 'Aktifkan status akun ' + selected.length + ' pengguna terpilih?',
            'deactivate': 'Nonaktifkan status akun ' + selected.length + ' pengguna terpilih?',
            'delete': 'Hapus ' + selected.length + ' pengguna terpilih? Data akan diarsipkan menggunakan soft delete.'
        };

        const confirmMsg = labels[actionType] || 'Jalankan tindakan ini pada ' + selected.length + ' pengguna terpilih?';
        if (!confirm(confirmMsg)) {
            return;
        }

        const form = document.getElementById('bulkActionForm');
        const actionInput = document.getElementById('bulkActionInput');
        const container = document.getElementById('bulkActionUserIdsContainer');

        actionInput.value = actionType;
        container.innerHTML = '';
        selected.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'user_ids[]';
            hidden.value = id;
            container.appendChild(hidden);
        });

        form.submit();
    }

    // ==============================================================
    // Modal Kelola Hak Admin Pegawai (Pilih Banyak)
    // ==============================================================
    function filterPegawaiModalList() {
        const q = (document.getElementById('pegawaiModalSearch')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.pegawai-modal-item').forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const nip = item.getAttribute('data-nip') || '';
            if (q === '' || name.includes(q) || nip.includes(q)) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    }

    function toggleAllModalPegawai(check) {
        document.querySelectorAll('.pegawai-modal-cb').forEach(cb => {
            const parent = cb.closest('.pegawai-modal-item');
            if (!parent || !parent.classList.contains('d-none')) {
                cb.checked = check;
            }
        });
        updateModalPegawaiCount();
    }

    function updateModalPegawaiCount() {
        const count = document.querySelectorAll('.pegawai-modal-cb:checked').length;
        const el = document.getElementById('modalPegawaiCheckedCount');
        if (el) el.textContent = `${count} pegawai dipilih untuk hak admin`;
    }

    document.querySelectorAll('.pegawai-modal-cb').forEach(cb => {
        cb.addEventListener('change', updateModalPegawaiCount);
    });
    updateModalPegawaiCount();
</script>
@endpush
