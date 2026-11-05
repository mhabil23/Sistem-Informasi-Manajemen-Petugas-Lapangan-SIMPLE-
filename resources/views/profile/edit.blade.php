@extends('layouts.app')

@section('title', 'Profil Pengguna & Keamanan')
@section('page_title', 'Pengaturan Profil')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Profil Pengguna</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Left Column: User Summary Card -->
    <div class="col-lg-4 col-md-5">
        <div class="sp-card text-center p-4">
            <div class="sp-user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.2rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
            <p class="text-muted small mb-3">{{ '@' . $user->username }}</p>
            <div class="mb-3">
                <span class="badge {{ $user->isAdmin() ? 'badge-role-admin' : 'badge-role-petugas' }} px-3 py-2">
                    <i class="fa-solid {{ $user->isAdmin() ? 'fa-shield-halved' : 'fa-user-check' }} me-1"></i>
                    {{ $user->isAdmin() ? 'Administrator SIMPLE' : 'Petugas Lapangan' }}
                </span>
            </div>
            <div class="border-top pt-3 text-start small">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Email:</span>
                    <strong class="text-dark">{{ $user->email }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Nomor HP:</span>
                    <strong class="text-dark">{{ $user->phone ?? '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Status Akun:</span>
                    <span class="badge bg-success">Aktif</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Terdaftar Sejak:</span>
                    <strong class="text-dark">{{ $user->created_at->format('d M Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Profile & Password Forms -->
    <div class="col-lg-8 col-md-7">
        <!-- Edit Profile Form -->
        <div class="sp-card mb-4">
            <div class="sp-card-header">
                <h5 class="sp-card-title">
                    <i class="fa-solid fa-user-pen text-primary"></i>
                    Informasi Akun
                </h5>
            </div>
            <div class="sp-card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NAMA LENGKAP</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">USERNAME</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly disabled>
                            <small class="text-muted" style="font-size: 0.75rem;">Username tidak dapat diubah sendiri.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">EMAIL</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NOMOR HP / WHATSAPP</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-sp-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="sp-card mb-0">
            <div class="sp-card-header">
                <h5 class="sp-card-title">
                    <i class="fa-solid fa-lock text-warning"></i>
                    Perbarui Kata Sandi
                </h5>
            </div>
            <div class="sp-card-body">
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">KATA SANDI SAAT INI</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required placeholder="Masukkan kata sandi lama Anda">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KATA SANDI BARU</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimal 6 karakter">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">KONFIRMASI KATA SANDI BARU</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi kata sandi baru">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-sp-accent">
                            <i class="fa-solid fa-key me-1"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection