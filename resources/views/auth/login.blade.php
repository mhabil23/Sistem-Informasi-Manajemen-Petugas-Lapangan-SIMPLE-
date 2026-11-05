@extends('layouts.auth')

@section('title', 'Masuk ke Sistem')

@section('content')
<div class="auth-header">
    <div class="auth-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo BPS SIMPLE">
    </div>
    <h3 class="fw-bold mb-1 tracking-wide">SIMPLE</h3>
    <p class="text-white-50 small mb-0">Sistem Informasi Manajemen Petugas Lapangan</p>
</div>

<div class="auth-body">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa-solid fa-circle-check me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->has('login'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
            <div>{{ $errors->first('login') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="needs-validation">
        @csrf

        <div class="mb-3">
            <label for="login" class="form-label fw-semibold text-secondary small">USERNAME ATAU EMAIL</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="fa-regular fa-user"></i>
                </span>
                <input type="text" 
                       class="form-control border-start-0 ps-0 @error('login') is-invalid @enderror" 
                       id="login" 
                       name="login" 
                       value="{{ old('login') }}" 
                       placeholder="Masukkan username atau email" 
                       required 
                       autofocus>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label fw-semibold text-secondary small">KATA SANDI</label>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="password" 
                       class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder="Masukkan kata sandi" 
                       required>
                <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" 
                        type="button" 
                        id="togglePassword" 
                        title="Tampilkan / Sembunyikan Password">
                    <i class="fa-regular fa-eye" id="eyeIcon"></i>
                </button>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label text-muted small" for="remember">
                    Ingat Saya
                </label>
            </div>
            <span class="badge bg-light text-secondary border">v1.0-Release</span>
        </div>

        <button type="submit" class="btn btn-sp-primary w-100 py-2 fs-6 shadow">
            <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk ke Sistem
        </button>
    </form>

    <!-- Quick Fill Credentials Pill for Testing -->
    <div class="mt-4 pt-3 border-top text-center">
        <p class="text-muted small fw-medium mb-2">Akun Demo Cepat (Klik untuk mengisi):</p>
        <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillCredentials('admin', 'admin123')">
                <i class="fa-solid fa-shield-halved me-1"></i> Admin
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillCredentials('petugas1', 'password')">
                <i class="fa-solid fa-user-check me-1"></i> Petugas 1
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="fillCredentials('petugas2', 'password')">
                <i class="fa-solid fa-user-check me-1"></i> Petugas 2
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.classList.toggle('fa-eye', !isPassword);
            eyeIcon.classList.toggle('fa-eye-slash', isPassword);
        });
    }

    function fillCredentials(login, password) {
        document.getElementById('login').value = login;
        document.getElementById('password').value = password;
    }
</script>
@endpush

